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
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;

	/*************************************
	* note about status of request:
	* 0 = not taken
	* 1 = taken
	* 2 = not taken
	* 3 = rejected
	* 4 = initiated by operator
	* 5 = initiated but rejected by visitor
	*************************************/
	$section = 8;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$action = "" ;
	$m = $y = $d = 0 ;

	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }

	$departments = AdminUsers_get_AllDepartments( $dbh, $session_setup['aspID'], 1 ) ;
	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
	$browsers = ARRAY (
		"IE 6.0" => "MSIE 6.0",
		"IE 5.0" => "MSIE 5.0",
		"IE 5.01" => "MSIE 5.01",
		"IE 5.5" => "MSIE 5.5",
		"Netscape 4.7x" => "Mozilla/4.7",
		"Netscape 6/6.x" => "Netscape6"
	) ;

	if ((!$m) || (!$y))
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}

	// the timespan to get the stats
	$stat_begin = mktime( 0,0,1,$m,$d,$y ) ;
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
<?php include_once("./header.php"); ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Operator Reports: Support Requests</span><br>
	  This page shows you the report of the support request breakdown 
	  by department(s) and operator(s). By selecting the date on the calendar, 
	  you can view past support requests. This report also shows you number 
	  of requests rejected or not taken. </p>
	<p><b><?php echo ( isset( $action ) && $action ) ? "" : $stat_date ?></b> </p>
	<p> Department Breakdown<br>
	  <!-- begin departments -->

	<?php if ( $action == "expand_month" ): ?>
	<table cellspacing=1 cellpadding=1 border=0 width="100%">
	<tr bgColor="#8080C0">
		<th width="200" align="left">Day</th>
		<th>Taken</th>
		<th>Not Taken</th>
		<th>Rejected</th>
	</tr>
	<?php
		$total_requests = $total_requests_not = $total_rejects = 0 ;
		for ( $c = 1; $m == date( "m", mktime( 0,0,0,$m,$c,$y ) ); ++$c )
		{
			$day = date( "F d, Y D", mktime( 0,0,0,$m,$c,$y ) ) ;

			$stat_begin = mktime( 0,0,0,$m,$c,$y ) ;
			$stat_end = mktime( 23,59,59,$m,$c,$y ) ;
			$total_taken = ServiceLogs_get_TotalRequestsPerDay( $dbh, "", $stat_begin, $stat_end, 1, $session_setup['aspID'] ) ;
			$total_nottaken = ServiceLogs_get_TotalRequestsPerDay( $dbh, "", $stat_begin, $stat_end, 0, $session_setup['aspID'] ) ;
			$total_rejected = ServiceLogs_get_TotalRequestsPerDay( $dbh, "", $stat_begin, $stat_end, 3, $session_setup['aspID'] ) ;
			$total_requests += $total_taken ;
			$total_rejects += $total_rejected ;
			$total_requests_not += $total_nottaken ;

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;

			print "<tr $class><td><a href=\"statistics.php?d=$c&m=$m&y=$y\">$day</td><td align=center>$total_taken</td><td align=center>$total_nottaken</td><td align=center>$total_rejected</td></tr>" ;
		}
	?>
	<tr>
		<th width="180" nowrap align="left">Total Requests Taken</th>
		<th><?php echo $total_requests ?></th>
		<th><?php echo $total_requests_not ?></th>
		<th><?php echo $total_rejects ?></th>
	</tr>
	</table>



	<?php else: ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th width="150" nowrap align="left">Department</th>
		<th nowrap>Taken</th>
		<th nowrap>Not Taken</th>
		<th nowrap>Rejected</th>
		<th nowrap>Initiated/Rejected</th>
	  </tr>
		<?php
			// 0-request not taken, 1-request taken, 3-rejected
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;
				$department = $departments[$c] ;
				$dept_name = stripslashes( $department['name'] ) ;
				$total_taken = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 1, $session_setup['aspID'] ) ;
				$total_nottaken = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 0, $session_setup['aspID'] ) ;
				$total_rejected = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 3, $session_setup['aspID'] ) ;
				$total_initiated = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 4, $session_setup['aspID'] ) ;
				$total_initiated_reject = ServiceLogs_get_TotalRequestsPerDay( $dbh, $department['deptID'], $stat_begin, $stat_end, 5, $session_setup['aspID'] ) ;
				$total_initiated += $total_initiated_reject ;

				print "
				<tr $class>
					<td width=\"120\">$dept_name</td>
					<td><a href=\"transcripts.php?action=view&deptid=$department[deptID]\">$total_taken</a></td>
					<td>$total_nottaken</td>
					<td>$total_rejected</td>
					<td>$total_initiated/$total_initiated_reject</td>
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
		<th nowrap>Taken</th>
		<th nowrap>Not Taken</th>
		<th nowrap>Rejected</th>
		<th nowrap>Initiated/Rejected</th>
	  </tr>
		<?php
			// 0-request not taken, 1-request taken, 3-rejected
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;

				$total_taken = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 1, $session_setup['aspID'] ) ;
				$total_nottaken = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 0, $session_setup['aspID'] ) ;
				$total_rejected = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 3, $session_setup['aspID'] ) ;
				$total_initiated = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 4, $session_setup['aspID'] ) ;
				$total_initiated_reject = ServiceLogs_get_TotalUserRequestCountPerDay( $dbh, $admin['userID'], $stat_begin, $stat_end, 5, $session_setup['aspID'] ) ;
				$total_initiated += $total_initiated_reject ;

				//$date = date( "m/d/y", $admin['created'] ) ;

				print "
					<tr $class>
						<td>$admin[login]</td>
						<td>$admin[name]</td>
						<td><a href=\"transcripts.php?action=view&userid=$admin[userID]\">$total_taken</a></td>
						<td>$total_nottaken</td>
						<td>$total_rejected</td>
						<td>$total_initiated/$total_initiated_reject</td>
					</tr>
				" ;
			}
		?>
	</table>
	<!-- end user stats -->
	<p> <span class="hilight">Taken</span> - Operator has taken a visitor's 
	  support request.<br>
	  <span class="hilight">Not Taken</span> - Operator did not take NOR 
	  rejected the request. They let it timeout.<br>
	  <span class="hilight">Rejected</span> - Operator has clicked "Busy" 
	  and rejected the request.<br>
	  <span class="hilight">Initiated</span> - Operator initiated the 
	  chat request.
<?php endif ; ?>
		</td>
  <td height="350" align="center" valign="top" style="background-image: url(../images/g_profile_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"><br><img src="../images/spacer.gif" width="1" height="220"><br>
	<?php Util_Cal_DrawCalendar( $dbh, $m, $y, "statistics.php?", "statistics.php?", "statistics.php?action=expand_month", $action ) ; ?>
	</td>
</tr>
 </table>

<?php include_once( "./footer.php" ) ; ?>
