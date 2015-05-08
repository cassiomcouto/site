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
	include_once("$DOCUMENT_ROOT/web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Cal.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Footprint/get.php") ;
	$section = 3;			// Section number - see header.php for list of section numbers
	$page_title = "LX LIVE | Atendimento Online - Administração";
	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';
?>
<?php

	// initialize
	$action = "" ;
	$m = $y = $d = "" ;
	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }
	$max_output = 25 ;

	if ((!$m) || (!$y))
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}

	if ( !$d )
	{
		// this is for the monthly breakdown
		$stat_begin = mktime( 0,0,0,$m,1,$y ) ;
		$stat_end = mktime( 23,59,59,$m,31,$y ) ;
	}
	else
	{
		$stat_begin = mktime( 0,0,0,$m,$d,$y ) ;
		$stat_end = mktime( 23,59,59,$m,$d,$y ) ;
	}

	$stat_date = date( "D F d, Y", $stat_begin ) ;
	$top_url_visits = ServiceFootprint_get_DayFootprint( $dbh, "", $stat_begin, $stat_end, $max_output, $session_setup['aspID'], $d, 0 ) ;
	$top_live_requests = ServiceLogs_get_DayMostLiveRequestPage( $dbh, $stat_begin, $stat_end, $max_output, $session_setup['aspID'] ) ;

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
  <td valign="top"> <p><span class="title">Reports: Traffic &amp; Visitor 
	  Footprints</span><br>
	  This report shows visitor footprints and page views. It breaks down 
	  the top most visited pages by daily or monthly. It also reports 
	  number of total page views and unique visits. This report also shows 
	  you the most frequent page(s) that the visitor request support from. 
	</p>
	<p>
	<?php if ( $action == "expand_month" ): ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="left"> 
		<th nowrap>Day</th>
		<th nowrap>Page Views</th>
		<th nowrap>Unique Visits</th>
	  </tr>
	<?php
		$grand_total_page_views = $grand_total_unique_visits = 0 ;
		for ( $c = 1; $m == date( "m", mktime( 0,0,0,$m,$c,$y ) ); ++$c )
		{
			$day = date( "F d, Y D", mktime( 0,0,0,$m,$c,$y ) ) ;

			$stat_begin = mktime( 0,0,0,$m,$c,$y ) ;
			$stat_end = mktime( 23,59,59,$m,$c,$y ) ;
			$total_page_views = ServiceFootprint_get_TotalDayFootprint( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
			$total_unique_visits = ServiceFootprint_get_TotalUniqueDayVisits( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
			$grand_total_page_views += $total_page_views ;
			$grand_total_unique_visits += $total_unique_visits ;

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;

			print "
				<tr $class>
					<td><a href=\"footprints.php?d=$c&m=$m&y=$y\">$day</td>
					<td align=\"left\">$total_page_views &nbsp;</td>
					<td align=\"left\">$total_unique_visits &nbsp;</td>
				</tr>" ;
		}
	?>
	<tr class="altcolor3">
		<th width="180" nowrap align="left">Grand Total for Month</th>
		<th align="left"><?php echo $grand_total_page_views ?></th>
		<th align="left"><?php echo $grand_total_unique_visits ?></th>
	</tr>
	 </table>
	<br> <br>
	 <table cellspacing=1 cellpadding=2 border=0 width="100%">
	 <tr> 
		<th colspan="2">Top <?php echo $max_output ?> Support Request Pages for this Month</th>
	  </tr>
	<?php
		for ( $c = 0; $c < count( $top_live_requests );++$c )
		{
			$footprint = $top_live_requests[$c] ;
			if ( !$footprint['url'] )
				$url_string = "<i>data empty</i>" ;
			else
			{
				$goto_url = "$footprint[url]?phplive_notally" ;
				if ( preg_match( "/\?/", $footprint['url'] ) )
					$goto_url = "$footprint[url]&phplive_notally" ;
				$url_string = "<a href=\"$goto_url\" target=\"new\">$footprint[url]</a>" ;
			}

			$class = "class=\"altcolor1\"" ;
			if ( $c % 2 )
				$class = "class=\"altcolor2\"" ;

			print "<tr $class><td>$footprint[total]</td><td>$url_string</td></tr>\n" ;
		}
	?>
	</table>
	





	<?php
		else:
		$total_page_views = ServiceFootprint_get_TotalDayFootprint( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
		$total_unique_visits = ServiceFootprint_get_TotalUniqueDayVisits( $dbh, $stat_begin, $stat_end, $session_setup['aspID'], 0 ) ;
	?>
	<b><?php echo $stat_date ?></b><br>
		<li> <?php echo $total_page_views ?> total page views
		<li> <?php echo $total_unique_visits ?> total unique visits
	</p>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	<tr> 
		<th colspan="2">Top <?php echo $max_output ?> Visited Pages </th>
	  </tr>
	<?php
		for ( $c = 0; $c < count( $top_url_visits );++$c )
		{
			$footprint = $top_url_visits[$c] ;
			//$url_unique_hits = ServiceFootprint_get_TotalUniqueURLDayVisits( $dbh, $stat_begin, $stat_end, $session_setup[aspID], $footprint[url] ) ;

			if ( !$footprint['url'] )
				$url_string = "<i>data empty</i>" ;
			else
			{
				$goto_url = "$footprint[url]" ;
				//if ( preg_match( "/\?/", $footprint['url'] ) )
				//	$goto_url = "$footprint[url]&phplive_notally" ;
				$url_string = "<a href=\"$goto_url\" target=\"new\">$footprint[url]</a>" ;
			}

			print "<tr class=\"altcolor1\"><td>$footprint[total]</td><td>$url_string</td></tr>\n" ;
		}
	?>
	</table>

	<br> <br> 

	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<th colspan="2">Top <?php echo $max_output ?> Support Request Pages </th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $top_live_requests );++$c )
			{
				$footprint = $top_live_requests[$c] ;
				if ( !$footprint['url'] )
					$url_string = "<i>data empty</i>" ;
				else
				{
					$goto_url = "$footprint[url]" ;
					//if ( preg_match( "/\?/", $footprint['url'] ) )
					//	$goto_url = "$footprint[url]&phplive_notally" ;
					$url_string = "<a href=\"$goto_url\" target=\"new\">$footprint[url]</a>" ;
				}

				print "<tr class=\"altcolor1\"><td>$footprint[total]</td><td>$url_string</td></tr>\n" ;
			}
		?>
	</table>

	<?php endif ; ?>
	</td>
  <td height="350" align="center" valign="top" style="background-image: url(../images/g_reports_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"><br> <img src="../images/spacer.gif" width="1" height="220"><br>
	<?php Util_Cal_DrawCalendar( $dbh, $m, $y, "footprints.php?", "footprints.php?", "footprints.php?action=expand_month", $action ) ; ?></td>
</tr>
 </table>
<?php include_once( "./footer.php" ) ; ?>
