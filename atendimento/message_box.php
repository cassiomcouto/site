<?php
	session_start() ;
	$action = $deptid = $l = $x = $requestid = $success = $question = $sid = "" ;
	if ( isset( $_POST['l'] ) ) { $l = $_POST['l'] ; }
	if ( isset( $_GET['l'] ) ) { $l = $_GET['l'] ; }
	if ( isset( $_POST['x'] ) ) { $x = $_POST['x'] ; }
	if ( isset( $_GET['x'] ) ) { $x = $_GET['x'] ; }
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_POST['deptid'] ) ) { $deptid = $_POST['deptid'] ; }
	if ( isset( $_GET['requestid'] ) ) { $requestid = $_GET['requestid'] ; }
	if ( isset( $_GET['sid'] ) ) { $sid = $_GET['sid'] ; }

	include_once( "./API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( ".", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("./web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;

	$THEME = ( isset( $_GET['theme'] ) && $_GET['theme'] ) ? $_GET['theme'] : $THEME ;

	// initialize
	if ( file_exists( "web/$l/$LOGO" ) && $LOGO )
		$logo = "web/$l/$LOGO" ;
	else if ( file_exists( "web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "web/$LOGO_ASP" ;
	else if ( file_exists( "themes/$THEME/images/logo.gif" ) )
		$logo = "themes/$THEME/images/logo.gif" ;
	else
		$logo = "images/logo.gif" ;

	$aspinfo = AdminASP_get_UserInfo( $dbh, $x ) ;
	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $x ) ;

	// conditions

	if ( $action == "submit" )
	{
		if ( $deptinfo['email'] )
		{
			$cookie_lifespan = time() + 60*60*24*180 ;
			setcookie( "COOKIE_PHPLIVE_VEMAIL", stripslashes( $_POST['email'] ), $cookie_lifespan ) ;

			$message = "Live Support Message Delivery:\r\n-------------------------------------------\r\n\r\n" . stripslashes( $_POST['message'] ) ;
			$subject = stripslashes( $_POST['subject'] ) ;
			if ( mail( $deptinfo['email'], $subject, $message, "From: $_POST[name] <$_POST[email]>") )
				$success = 1 ;
		}
	}
	else if ( $action == "exit" )
	{
		ServiceChat_remove_ChatRequest( $dbh, $requestid ) ;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> <?php echo $LANG['TITLE_LEAVEMESSAGE'] ?> </title>

<script type="text/javascript" language="JavaScript1.2" src="js/chat_fn.js"></script>
<script type="text/javascript" language="JavaScript1.2">
<!--
	function init(){
		// Check for browser support
		if( !document.createElement && !document.createElementNS ) self.location.href = "http://www.osicodes.com/demos/phplive/browser.php";
	}

	window.onload = init;

	function do_submit()
	{
		if ( ( document.form.name.value == "" ) || ( document.form.email.value == "" )
			|| ( document.form.subject.value == "" ) || ( document.form.message.value == "" ) )
			alert( "<?php echo $LANG['MESSAGE_BOX_JS_A_ALLFIELDSSUP'] ?>" ) ;
		else if ( document.form.email.value.indexOf("@") == -1 )
			alert( "<?php echo $LANG['MESSAGE_BOX_JS_A_INVALIDEMAIL'] ?>" ) ;
		else
			document.form.submit() ;
	}

	function opennewwin(url)
	{
		window.open(url, "newwin", "scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes") ;
	}

//-->
</script>

<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {
	color: #000000;
	font-weight: bold;
}
.style2 {
	color: #0000FF;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<form method="post" action="message_box.php" name="form" id="form">
<input type="hidden" name="action" value="submit">
<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
<input type="hidden" name="x" value="<?php echo $x ?>">
<input type="hidden" name="l" value="<?php echo $l ?>">
<input type="hidden" name="requestid" value="<?php echo $requestid ?>">
<div id="main">
	<div id="logo"><img src="<?php echo $logo ?>" alt="" /></div>

	<?php if ( $action == "submit" ): ?>
		<div id="inputarea">
			<fieldset>
				<dl>
					<?php if ( $success ): ?>
					<big><b><?php echo $LANG['MESSAGE_BOX_SENT'] ?> <?php echo $deptinfo['name'] ?></b></big>
					<br><br>
					<input type="button" value="Fechar" OnClick="parent.window.close()" class="button">
					<?php endif ; ?>

					<?php if ( file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge_search.php" ) && $aspinfo['knowledgebase'] ) : ?>
					<br><br><a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_search.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&"><b><?php echo $LANG['CLICK_HERE'] ?></b></a> <?php echo $LANG['KB_SEARCH'] ?></a>
					<?php endif ; ?>
			</dl>
			</fieldset>
		</div>

	<?php else: ?>
		<p><?php echo ( $deptinfo['message'] ) ? stripslashes( $deptinfo['message'] ) : $LANG['MESSAGE_BOX_MESSAGE'] ?></p>
		
		<div id="inputarea">
			<fieldset>
				<dl>
					<dt><label for="name"><?php echo $LANG['WORD_NAME'] ?>:</label></dt>
					<dd class="textbox"><input type="text" name="name" id="name" size="40" maxlength="255" value="<?php echo isset( $_COOKIE['COOKIE_PHPLIVE_VLOGIN'] ) ? stripslashes( $_COOKIE['COOKIE_PHPLIVE_VLOGIN'] ) : "" ?>"></dd>
				</dl>
				<dl>
					<dt><label for="email"><?php echo $LANG['WORD_EMAIL'] ?>:</label></dt>
					<dd class="textbox"><input type="text" name="email" id="email" size="40" maxlength="255" value="<?php echo ( isset( $_COOKIE['COOKIE_PHPLIVE_VEMAIL'] ) && ( $_COOKIE['COOKIE_PHPLIVE_VEMAIL'] != "-@-.com" ) ) ? $_COOKIE['COOKIE_PHPLIVE_VEMAIL'] : "" ?>"></dd>
				</dl>
				<dl>
					<dt><label for="subject"><?php echo $LANG['WORD_SUBJECT'] ?>:</label></dt>
					<dd class="textbox"><input type="text" name="subject" id="subject" size="40" maxlength="255" value=""></dd>
				</dl>
				<dl>
					<dt><label for="message"><?php echo $LANG['WORD_MESSAGE'] ?>:</label></dt>
					<dd class="textbox"><textarea name="message" cols="25" rows="2" id="message" class="message2"><?php echo ( isset( $_SESSION['session_chat'][$sid]['question'] ) ) ? stripslashes( $_SESSION['session_chat'][$sid]['question'] ) : "" ; ?></textarea></dd>
					<dd><br><input type="button" class="button" name="send" value="<?php echo "$LANG[WORD_SEND] $LANG[WORD_EMAIL]" ?>" onclick="do_submit();" /></dd>
				</dl>
				<dl></dl>
				<dl>
					<?php if ( file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge_search.php" ) && $aspinfo['knowledgebase'] ) : ?>
					<dt></dt>
					<dd><a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_search.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&"><b><?php echo $LANG['CLICK_HERE'] ?></b></a> <?php echo $LANG['KB_SEARCH'] ?></a>
					<?php endif ; ?>
				</dl>
			</fieldset>
		</div>
	<?php endif ; ?>
	<div id="options">
		&nbsp;
	</div>
	
	<?php
		// because of tabbed browsers, we want to call a JavaScript window open function
		$branding = preg_replace( "/href=(.*?)( |>)/i", "href=\"JavaScript:opennewwin( \\1 )\"\\2", $LANG['DEFAULT_BRANDING'] ) ;
		$branding = preg_replace( "/target=(.*?)(>| >)/i", " >", $branding ) ;
	?>
	<div id="copyright"><span class="style2">Sistema Atendimento Online</span> <?php echo $PHPLIVE_VERSION ?> &copy; <a href="" target="_blank" class="style1"></a></a></div>
</div>
</form>
</body>
</html>
