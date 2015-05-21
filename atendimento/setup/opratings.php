<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Cal.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Survey/get.php") ;
	$section = 8;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>' ;
?>
<?php

	// initialize
	$action = "" ;
	$m = $y = $d = "" ;
	$rating_hash = Array() ;
	$rating_hash[4] = "Excellent" ;
	$rating_hash[3] = "Very Good" ;
	$rating_hash[2] = "Good" ;
	$rating_hash[1] = "Needs Improvement" ;
	$rating_hash[0] = "&nbsp;" ;

	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }

	$departments = AdminUsers_get_AllDepartments( $dbh, $session_setup['aspID'], 1 ) ;
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;

	if ((!$m) || (!$y))
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}

	// the timespan to get the stats
	$stat_begin = mktime( 0,0,0,$m,$d,$y ) ;
	$stat_end = mktime( 23,59,59,$m,$d,$y ) ;

	$stat_date = date( "D F d, Y", $stat_begin ) ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
?>
<?php
	// functions
?>
<?php
	// conditions
?>
<?php include_once("./header.php") ; ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Operator Reports : Support Requests</span><br>
	  This page shows you the report of visitor support ratings by department(s) and operator(s).  By selecting the date on the calendar, you can view past support ratings.</p>
	<p><b><?php echo ( isset( $action ) && $action ) ? "" : $stat_date ?></b> </p>
	<p> Department Breakdown<br>
	  <!-- begin departments -->

	<?php if ( $action == "expand_month" ): ?>
	<table cellspacing=1 cellpadding=1 border=0 width="100%">
	<tr bgColor="#8080C0">
		<th width="200">Day</th>
		<th>Total Rates</th>
		<th>Ave Rating</th>
	</tr>
	<?php
		$total_requests = $c_total_rates = $c_sum_rates = 0 ;
		$c_sum_ave_rate = "" ;
		for ( $c = 1; $m == date( "m", mktime( 0,0,0,$m,$c,$y ) ); ++$c )
		{
			$day = date( "F d, Y D", mktime( 0,0,0,$m,$c,$y ) ) ;

			$stat_begin = mktime( 0,0,0,$m,$c,$y ) ;
			$stat_end = mktime( 23,59,59,$m,$c,$y ) ;
			$total_rates = ServiceSurvey_get_DeptTotalRates( $dbh, 0, 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;
			$sum_rates = ServiceSurvey_get_DeptTotalRatings( $dbh, 0, 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;

			$ave_rating = 0 ;
			if ( $sum_rates > 0 )
				$ave_rating = round( $sum_rates/$total_rates ) ;
				
			$c_total_rates += $total_rates ;
			$c_sum_rates += $sum_rates ;

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;
			$ave_rating_string = $rating_hash[$ave_rating] ;

			print "<tr $class><td><a href=\"opratings.php?d=$c&m=$m&y=$y\">$day</td><td align=center>$total_rates</td><td align=center>$ave_rating_string</td></tr>" ;
		}

		if ( $c_total_rates > 0 )
			$c_sum_ave_rate = $rating_hash[round( $c_sum_rates/$c_total_rates )] ;
	?>
	<tr>
		<th width="180" nowrap align="left">Total Requests Taken</th>
		<th><?php echo $c_total_rates ?></th>
		<th><?php echo $c_sum_ave_rate ?></th>
	</tr>
	</table>


	<?php else: ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th width="150" nowrap align="left">Department</th>
		<th nowrap>Total Rates</th>
		<th nowrap>Ave Rating</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$department = $departments[$c] ;
				$dept_name = stripslashes( $department['name'] ) ;
				$total_rates = ServiceSurvey_get_DeptTotalRates( $dbh, 0, $department['deptID'], $stat_begin, $stat_end, $session_setup['aspID'] ) ;
				$sum_rates = ServiceSurvey_get_DeptTotalRatings( $dbh, 0, $department['deptID'], $stat_begin, $stat_end, $session_setup['aspID'] ) ;

				if ( $sum_rates > 0 )
					$ave_rating = round( $sum_rates/$total_rates ) ;
				else
					$ave_rating = 0 ;

				$ave_rating_string = $rating_hash[$ave_rating] ;

				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;

				print "
				<tr $class>
					<td width=\"120\">$dept_name</td>
					<td>$total_rates</td>
					<td>$ave_rating_string</td>
				</tr>
				" ;
			}
		?>
	</table>
	<!-- end departments -->
	<br> 
	<!-- begin user stats -->
	User Breakdown:<br>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th width="60" nowrap>Login</th>
		<th width="80" nowrap>Name</th>
		<th nowrap>Total Rates</th>
		<th nowrap>Ave Today</th>
		<th nowrap>Overall</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;

				$total_rates = ServiceSurvey_get_DeptTotalRates( $dbh, $admin['userID'], 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;
				$sum_rates = ServiceSurvey_get_DeptTotalRatings( $dbh, $admin['userID'], 0, $stat_begin, $stat_end, $session_setup['aspID'] ) ;

				if ( $sum_rates > 0 )
					$ave_rating = round( $sum_rates/$total_rates ) ;
				else
					$ave_rating = 0 ;

				$ave_rating_string = $rating_hash[$ave_rating] ;
				$overall_rating_string = $rating_hash[$admin['rate_ave']] ;

				//$date = date( "m/d/y", $admin['created'] ) ;

				print "
					<tr $class>
						<td>$admin[login]</td>
						<td>$admin[name]</td>
						<td>$total_rates</td>
						<td>$ave_rating_string</td>
						<td>$overall_rating_string</td>
					</tr>
				" ;
			}
		?>
	</table>
<?php endif ; ?>
	</td>
  <td height="350" align="center" valign="top" style="background-image: url(../images/g_profile_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"><br><img src="../images/spacer.gif" width="1" height="220"><br>
	<?php Util_Cal_DrawCalendar( $dbh, $m, $y, "opratings.php?", "opratings.php?", "opratings.php?action=expand_month", $action ) ; ?>
	</td>
</tr>
 </table>

<?php include_once( "./footer.php" ) ; ?>