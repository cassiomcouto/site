<?php
	session_start() ;
	if ( !isset( $_SESSION['session_admin'] ) && !isset( $_SESSION['session_chat'] ) && !isset( $_SESSION['session_setup'] ) )
	{
		print "<font color=\"#FF0000\">[Access Denied-transcriptm] Exiting...</font>" ;
		exit ;
	}

	$success = 0 ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	$chat_session = ( isset( $_GET['chat_session'] ) ) ? $_GET['chat_session'] : "" ;

	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error in view_transcript.php: config files not found!] Saindo...</font>" ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$l/$l-conf-init.php");
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Transcripts/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Logs/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;

	// initialize
	$rating_hash = Array() ;
	$rating_hash[4] = "Excelente" ;
	$rating_hash[3] = "Muito Bom" ;
	$rating_hash[2] = "Bom" ;
	$rating_hash[1] = "Ruim" ;
	$rating_hash[0] = "<i>não avaliado</i>" ;

	$aspinfo = AdminASP_get_UserInfo( $dbh, $x ) ;
	$transcriptinfo = ServiceTranscripts_get_TranscriptInfo(  $dbh, $chat_session, $x ) ;

	if ( !isset( $transcriptinfo['created'] ) )
	{
		$session_chat = $_SESSION['session_chat'] ;
		$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
		$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
		$deptid = ( isset( $_GET['deptid'] ) ) ? $_GET['deptid'] : "" ;

		$requestinfo = ServiceChat_get_ChatRequestInfo( $dbh, $requestid ) ;
		$requestloginfo = ServiceLogs_get_SessionRequestLog( $dbh, $chat_session ) ;
		$transcriptinfo = Array() ;
		$transcriptinfo['deptID'] = $deptid ;
		$transcriptinfo['created'] = $requestloginfo['created'] ;
		$transcriptinfo['rating'] = 0 ;
		$transcriptinfo['from_screen_name'] = $session_chat[$sid]['visitor_name'] ;
		$transcriptinfo['email'] = $requestinfo['email'] ;
		$transcriptinfo['userID'] = $session_chat[$sid]['admin_id'] ;
		$transcriptinfo['formatted'] = join( "", file( "$DOCUMENT_ROOT/web/chatsessions/".$chat_session."_transcript.txt" ) ) ;
	}
	else
	{
		$requestloginfo = ServiceLogs_get_SessionRequestLog( $dbh, $chat_session ) ;
	}

	$department = AdminUsers_get_DeptInfo( $dbh, $transcriptinfo['deptID'], $x ) ;
	$userinfo = AdminUsers_get_UserInfo( $dbh, $transcriptinfo['userID'], $x ) ;
	$date = date( "D m/d/y $TIMEZONE_FORMAT:i$TIMEZONE_AMPM", ( $transcriptinfo['created'] + $TIMEZONE ) ) ;
	$rating = ( isset( $transcriptinfo['rating'] ) ) ? $transcriptinfo['rating'] : 0 ;
	$rating = $rating_hash[$rating] ;

	$duration = $transcriptinfo['created'] - $chat_session ;
	if ( $duration <= 0 ) { $duration = 1 ; }
	if ( $duration > 60 )
		$duration = round( $duration/60 ) . " min" ;
	else
		$duration = $duration . " sec" ;
	
	$warning = "" ;
	if ( !preg_match( "/<p class=/", $transcriptinfo['formatted'] ) )
		$warning = "<p class=\"alert\">Atencao: Formato de Versao Suporte Online.  Web e email pode estar destorcido ou incorreto.</p>" ;
	
	$transcript_output = stripslashes( $transcriptinfo['formatted'] ) ;
	// strip out html and other header for v2.8.2 and older
	$transcript_output = preg_replace( "/<html(.*?)\/head>/", "", $transcript_output ) ;
	// show timestamp for only setup admin and operators
	if ( isset( $_SESSION['session_admin'] ) || isset( $_SESSION['session_setup'] ) )
		$transcript_output = preg_replace( "/<ts(.*?)ts>/", "<small>\\1</small>", $transcript_output ) ;
?>
<?php include_once( "../files/nodelete_chat.php" ) ; ?>
<?php echo $warning ?>
<table cellspacing=0 cellpadding=0 border=0>
<?php if ( !preg_match( "/\[ Operator-to-Operator pedido \]/", $transcript_output ) ): ?>
<tbody>
<tr><td><b>Empresa</td><td><?php echo stripslashes( $aspinfo['company'] ) ?></td></tr>
<tr><td><b>Departamento</td><td><?php echo $department['name'] ?></td></tr>
<tr><td><b>Operador</td><td><?php echo stripslashes( $userinfo['name'] ) ?> &lt;<a href="mailto:<?php echo $userinfo['email'] ?>"><?php echo $userinfo['email'] ?></a>&gt;</td></tr>
<tr><td><b>Visitante</td><td><?php echo stripslashes( $transcriptinfo['from_screen_name'] ) ?> &lt;<a href="mailto:<?php echo $transcriptinfo['email'] ?>"><?php echo $transcriptinfo['email'] ?></a>&gt;</td></tr>
<tr><td><b>Infor</td><td><?php echo $date ?> (rating: <?php echo $rating ?>)</td></tr>
<tr><td><b>Navegador</td><td><?php echo $requestloginfo['browser_os'] ?></td></tr>
<tr><td><b>Resolução</td><td><?php echo $requestloginfo['display_resolution'] ?></td></tr>
<tr><td><b>Host</td><td><?php echo $requestloginfo['ip'] ?> (<?php echo $requestloginfo['hostname'] ?>)</td></tr>
</tbody>
<?php endif; ?>
</table>
<?php echo $transcript_output ; ?>
</div>
</body>
</html>