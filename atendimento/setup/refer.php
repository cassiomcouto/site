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
	include_once("$DOCUMENT_ROOT/API/Refer/get.php") ;
	$section = 3 ;			// Section number - see header.php for list of section numbers

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// initialize
	$m = $d = $y = $error = $action = "" ;
	$success = 0 ;

	// get variables
	if ( isset( $_GET['m'] ) ) { $m = $_GET['m'] ; }
	if ( isset( $_GET['d'] ) ) { $d = $_GET['d'] ; }
	if ( isset( $_GET['y'] ) ) { $y = $_GET['y'] ; }
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	if ( $action )
		$nav_line = '<a href="refer.php" class="nav">:: Previous</a>';

	if ( !$m || !$y || !$d )
	{
		$m = date( "m",time()+$TIMEZONE ) ;
		$y = date( "Y",time()+$TIMEZONE ) ;
		$d = date( "j",time()+$TIMEZONE ) ;
	}
	$stat_begin = mktime( 0,0,0,date( "m",time()+$TIMEZONE ),date( "j",time()+$TIMEZONE ),date( "Y",time()+$TIMEZONE ) ) ;

	$selected_begin = mktime( 0,0,0,$m,$d,$y ) ;
	$selected_date = date( "D F d, Y", $selected_begin ) ;
	$refers = ServiceRefer_get_ReferOnDate( $dbh, $session_setup['aspID'], $selected_begin, ( $selected_begin + (60*60*24) ) ) ;

include_once("./header.php") ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Reports: Refer URL Statistics</span><br>
	  See where your visitors are coming from with Refer URL stats below. 
	</p>
	<ul>
	  <li>You can use the data to maximize your ad campaign or to get 
		a better understanding of your visitors. </li>
	  <li>The system tracks only 10 days of stats. Please check here regularly 
		or print out each day for your records. </li>
	</ul>

	<?php if ( $action == "export" ): ?>
	<!-- future version possibility -->



	<?php else: ?>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr align="center"> 
		<?php
			for ( $c = $stat_begin; $c > ( $stat_begin - (60*60*24*10) ); 1 )
			{
				$date = date( "D M d", $c ) ;
				$m = date( "m",$c ) ;
				$y = date( "Y",$c ) ;
				$d = date( "j",$c ) ;
				print "<th class=\"nav\"><a href=\"refer.php?m=$m&d=$d&y=$y\" class=\"nav\">$date</a></th>" ;
				$c -= (60*60*24) ;
			}
		?>
	  </tr>
	</table>
	<p><strong>Refer stats for date: <?php echo $selected_date ?></strong> &nbsp; 
	  (max 500 results)

	</p>
	<table width="100%" border=0 cellpadding=2 cellspacing=1>
	  <tr> 
		<th align="left" width="30">Count</th>
		<th align="left">Refer URL</th>
	  </tr>
		<?php
			for ( $c = 0; $c < count( $refers ); ++$c )
			{
				$refer = $refers[$c] ;
				$class = "class=\"altcolor1\"" ;
				if ( $c % 2 )
					$class = "class=\"altcolor2\"" ;
				$refer_url = wordwrap( stripslashes( $refer['refer_url'] ), 100, "<br>", 1 ) ;

				print "
					<tr $class>
						<td>$refer[total]</td>
						<td><a href=\"$refer[refer_url]\" target=\"new\">$refer_url</a></td>
					</tr>
				" ;
			}
		?>
	</table>
	<?php endif ; ?>

  </td>
</tr>
 </table>
<?php include_once( "./footer.php" ) ; ?>