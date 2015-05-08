<?php
	session_start() ;
	$session_chat = $_SESSION['session_chat'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;

	if ( !file_exists( "web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("./web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Refer/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;

	// initialize
	$rating_hash = Array() ;
	$rating_hash[4] = "Excellent" ;
	$rating_hash[3] = "Very Good" ;
	$rating_hash[2] = "Good" ;
	$rating_hash[1] = "Needs Improvement" ;
	$rating_hash[0] = "&nbsp;" ;

	$m = date( "m",mktime() ) ;
	$y = date( "Y",mktime() ) ;
	$d = date( "j",mktime() ) ;

	// the timespan to get the stats
	$begin = mktime( 0,0,0,$m,$d,$y ) ;
	$end = mktime( 23,59,59,$m,$d,$y ) ;
	$requestinfo = ServiceChat_get_ChatRequestInfo( $dbh, $requestid ) ;
	$admin = AdminUsers_get_UserInfo( $dbh, $session_chat[$sid]['admin_id'], $session_chat[$sid]['aspID'] ) ;

	// conditions
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat [informações]</title>

<link href="themes/<?php echo ( $_SESSION['session_chat'][$sid]['isadmin'] && $_SESSION['session_chat'][$sid]['theme'] ) ? $_SESSION['session_chat'][$sid]['theme'] : $THEME ?>/style.css" rel="stylesheet" type="text/css" />

</head>
<body class="operatorbody">

<?php if ( $_SESSION['session_chat'][$sid]['chatfile_get'] == "" ): ?>
<big><b>Sessão Terminada.</b></big>



<?php
	elseif ( ( $action == "footprints" ) && $VISITOR_FOOTPRINT ):
	include_once("$DOCUMENT_ROOT/API/Footprint/get.php") ;
	$footprints_today = ServiceFootprint_get_DayFootprint( $dbh, $requestinfo['ip_address'], $begin, $end, 0, $session_chat[$sid]['aspID'], 0, 0 ) ;
	$footprints_beforetoday = ServiceFootprint_get_BeforeDayFootprint( $dbh, $requestinfo['ip_address'], $begin, 15, $session_chat[$sid]['aspID'] ) ;
?>
<!-- display only if visitor footprints is enabled -->
<table cellspacing="1">
	<thead>
	<tr>
		<th colspan="2">Paginas Visitadas Hoje - IP (<?php echo $requestinfo['ip_address'] ?>)</th>
	</tr>
	</thead>
	<tbody>
	<?php
		for ( $c = 0; $c < count( $footprints_today );++$c )
		{
			$footprint = $footprints_today[$c] ;

			$footprint_url = stripslashes( $footprint['url'] ) ;
			$string_length = strlen( $footprint_url ) ;
			if ( $string_length > 70 )
				$footprint_url = wordwrap( $footprint_url, 65, "<br>", 1 ) ;

			print "<tr><th>$footprint[total]</th><td><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$footprint[url]', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$footprint_url</a></td></tr>\n" ;
		}
	?>
	</tbody>
</table>

<table cellspacing="1">
	<thead>
		<th colspan="2">Paginas Visitadas</th>
	</thead>
	<tbody>
	<?php
		for ( $c = 0; $c < count( $footprints_beforetoday );++$c )
		{
			$footprint = $footprints_beforetoday[$c] ;

			$footprint_url = stripslashes( $footprint['url'] ) ;
			$string_length = strlen( $footprint_url ) ;
			if ( $string_length > 70 )
				$footprint_url = wordwrap( $footprint_url, 65, "<br>", 1 ) ;

			print "<tr><th>$footprint[total]</th><td><a href=\"JavaScript:void(0)\" OnClick=\"window.open('$footprint[url]', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">$footprint_url</a></td></tr>\n" ;
		}
	?>
	</tbody>
</table>
<!-- end visitor footprings -->






<?php
	elseif ( $action == "transcripts" ):
	include_once("$DOCUMENT_ROOT/API/Transcripts/get.php") ;
	$transcripts = ServiceTranscripts_get_TranscriptsByIP( $dbh, $requestinfo['ip_address'], $session_chat[$sid]['aspID'] ) ;
?>
<table cellspacing="1">
	<thead>
		<tr>
		<th colspan="5">Transcripts IP: <?php echo $requestinfo['ip_address'] ?></th>
		</tr>
	</thead>
	<tbody class="subhead">
		<tr>
			<th>Data</td>
			<th>Avaliação</td>
			<th>Nome</td>
			<th>Tamanho</td>
			<th>Duração</td>
		</tr>
	</tbody>
	<tbody>
	<?php
		for ( $c = 0; $c < count( $transcripts );++$c )
		{
			$transcript = $transcripts[$c] ;

			$rating = ( isset( $transcript['rating'] ) ) ? $transcript['rating'] : 0 ;
			$rating = $rating_hash[$rating] ;

			$duration = $transcript['created'] - $transcript['chat_session'] ;
			if ( $duration <= 0 ) { $duration = 1 ; }
			if ( $duration > 60 )
				$duration = round( $duration/60 ) . " min" ;
			else
				$duration = $duration . " sec" ;

			$class = "class=\"row1\"" ;
			if ( $c % 2 )
				$class = "class=\"row2\"" ;

			$date = date( "m/d/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $transcript['created'] + $TIMEZONE ) ) ;
			$size = Util_Format_Bytes( strlen( strip_tags( $transcript['plain'] ) ) ) ;
			print "<tr $class><td>&raquo; <a href=\"javascript:void(0)\" OnClick=\"window.open('admin/view_transcript.php?x=".$session_chat[$sid]['aspID']."&l=".$session_chat[$sid]['asp_login']."&chat_session=$transcript[chat_session]&sid=$sid&requestid=$requestid&action=view&theme_admin=".$session_chat[$sid]['theme']."', '$transcript[created]', 'status=no,scrollbars=no,menubar=no,toolbar=no,resizable=yes,location=no,width=450,height=360')\">$date</a></td><td>$rating</td><td>$transcript[from_screen_name]</td><td>$size</td><td>$duration</td></tr>\n" ;
		}
	?>
	</tbody>
</table>







<?php
	elseif ( $action == "spam" ):
?>
<p>Bloquer IP de acessar o sistema.</p>
<p>
<form><input type="button" class="go" OnClick="window.open( '<?php echo $BASE_URL ?>/admin/index.php?x=<?php echo $session_chat[$sid]['aspID'] ?>&sid=<?php echo $admin['session_sid'] ?>&action=set&ip=<?php echo $requestinfo['ip_address'] ?>', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1' )" value="Clique Para Bloquear <?php echo $requestinfo['ip_address'] ?>"></form>







<?php
	else:
	$total_request = ServiceLogs_get_TotalIpRequests( $dbh, $requestinfo['ip_address'], $session_chat[$sid]['aspID'] ) ;
	$referinfo = ServiceRefer_get_ReferInfo( $dbh, $session_chat[$sid]['aspID'], $requestinfo['ip_address'] ) ;
	$requestlog = ServiceLogs_get_SessionRequestLog( $dbh, $session_chat[$sid]['sessionid'] ) ;

	$clicked_url = stripslashes( $requestinfo['url'] ) ;
	$string_length = strlen( $clicked_url ) ;
	if ( $string_length > 60 )
		$clicked_url = wordwrap( $clicked_url, 55, "<br>", 1 ) ;
	
	$refer_url = stripslashes( preg_replace( "/\"/", "&quot;", $referinfo['refer_url'] ) ) ;
	$refer_url = stripslashes( preg_replace( "/'/", "&#39;", $refer_url ) ) ;
	$string_length = strlen( $refer_url ) ;
	if ( $string_length > 60 )
		$refer_url = wordwrap( $refer_url, 55, "<br>", 1 ) ;
?>

<table cellspacing="1">
<thead>
		<tr>
			<th colspan="2">Informações do Visitante</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Formulario</th>
			<td><a href="JavaScript:void(0)" OnClick="window.open('<?php echo $requestinfo['url'] ?>', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')"><?php echo $clicked_url ?></a></td>
		</tr>
		<tr>
			<th>Referencia URL</th>
			<td><a href="JavaScript:void(0)" OnClick="window.open('<?php echo stripslashes( $referinfo['refer_url'] ) ?>', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')"><?php echo $refer_url ?></a></td>
		</tr>
		<?php if ( isset( $requestinfo['email'] ) ): ?>
		<tr>
			<th>Email</th>
			<td><a href="mailto:<?php echo $requestinfo['email'] ?>"><?php echo $requestinfo['email'] ?></a></td>
		</tr>
		<?php endif; ?>
		<tr>
			<th>Pedido de Chat</th>
			<td><?php echo $total_request ?> time(s)</td>
		</tr>
		<tr>
			<th>Navegador/OS</th>
			<td><?php echo $requestinfo['browser_type'] ?></td>
		</tr>
		<tr>
			<th>IP</th>
			<td><?php echo $requestinfo['ip_address'] ?></td>
		</tr>
		<tr>
			<th>Host Name</th>
			<td><?php echo $requestlog['hostname'] ?></td>
		</tr>
		<tr>
			<th>Monitor</th>
			<td><?php echo $requestinfo['display_resolution'] ?></td>
		</tr>
		<tr>
			<th>Time Zone</th>
			<td><?php echo $requestinfo['visitor_time'] ?></td>
		</tr>
	</tbody>
</table>
<?php endif ; ?>
</body>
</html>
<?php
//	mysql_close( $dbh['con'] ) ;
?>