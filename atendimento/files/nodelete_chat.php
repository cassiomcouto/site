<?php
	if ( !session_is_registered( "session_chat" ) && !session_is_registered( "session_admin" ) && !session_is_registered( "session_setup" ) )
		session_start() ;
	include_once( "../web/conf-init.php" ) ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Dir.php") ;

	$transcript = "" ;
	$text = ( isset( $_GET['text'] ) ) ? $_GET['text'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;
	$admin_id = ( isset( $_GET['admin_id'] ) ) ? $_GET['admin_id'] : "" ;
	$theme_admin = ( isset( $_GET['theme_admin'] ) ) ? $_GET['theme_admin'] : "" ;
	$respawn = 0 ;

	if ( isset( $_SESSION['session_chat'][$sid]['asp_login'] ) )
		$l = $_SESSION['session_chat'][$sid]['asp_login'] ;
	else if ( isset( $_SESSION['session_admin'][$sid]['asp_login'] ) )
		$l = $_SESSION['session_admin'][$sid]['asp_login'] ;
	else if ( isset(  $_SESSION['session_setup']['login'] ) )
		$l =  $_SESSION['session_setup']['login'] ;
	else 
		$l = "none_provided" ;
	if ( !Util_DIR_CheckDir( "..", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config file not found!] Exiting... [nodelete_chat.php] [$l-$sid]</font>" ;
		exit ;
	}
	include_once("$DOCUMENT_ROOT/web/$l/$l-conf-init.php") ;

	if ( !$text && isset( $_SESSION['session_chat'][$sid]['chatfile_transcript'] ) && file_exists( "$DOCUMENT_ROOT/web/chatsessions/".$_SESSION['session_chat'][$sid]['chatfile_transcript'] ) && !isset( $transcript_output ) )
	{
		$transcript = join( "", file( "$DOCUMENT_ROOT/web/chatsessions/".$_SESSION['session_chat'][$sid]['chatfile_transcript'] ) ) ;
		if ( $_SESSION['session_chat'][$sid]['isadmin'] )
			$transcript = preg_replace( "/<admin_strip>(.*?)<\/admin_strip>/", "", $transcript ) ;
	}

	// if admin_id is passed, then we need to update the session
	if ( $admin_id )
		$_SESSION['session_chat'][$sid]['admin_id'] = $admin_id ;
	
	if ( isset( $_SESSION['session_chat'][$sid]['isadmin'] ) && $_SESSION['session_chat'][$sid]['isadmin'] && $_SESSION['session_chat'][$sid]['theme'] )
		$THEME = $_SESSION['session_chat'][$sid]['theme'] ;
	else if ( $theme_admin )
		$THEME = $theme_admin ;
?>
<html>
<head>
<title>Conversation</title>
<script type="text/javascript" language="JavaScript1.2" src="<?php echo $BASE_URL ?>/js/styleswitcher.js"></script>
<style type="text/css">
<!--
@import url(<?php echo $BASE_URL ?>/css/text-small.css);

* { margin: 0; padding: 0; }

body {
	font-family: Arial, Helvetica, sans-serif;
	margin: 3px;
}
* html body { width: 93%; }	/* IE tweak */

-->
</style>

<link href="<?php echo $BASE_URL ?>/css/text-large.css" rel="alternate stylesheet" type="text/css" title="A++" />
<link href="<?php echo $BASE_URL ?>/css/text-medium.css" rel="alternate stylesheet" type="text/css" title="A+" />
<link href="<?php echo $BASE_URL ?>/css/text-small.css" rel="alternate stylesheet" type="text/css" title="A" />
<link href="<?php echo $BASE_URL ?>/themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!--
	if ( <?php echo $respawn ?> )
		window.parent.window.parent.frames['main'].window.respawn = 1 ;
//-->
</script>

</head>
<body class="chatbody">
<div id="conversation">
<?php if ( $text ): ?>
<p class="notice"><?php echo stripslashes( urldecode( $text ) ) ?></p>
<?php elseif ( $transcript ):
	echo stripslashes( urldecode( $transcript ) )  ;
?>
<?php endif ; 
?>