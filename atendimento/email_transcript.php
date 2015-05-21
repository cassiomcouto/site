<?php
	session_start() ;
	$action = $sessionid = $requestid = $sid = "" ;
	$close_window = 0 ;
	$session_chat = $_SESSION['session_chat'] ;
	if ( isset( $_POST['sid'] ) ) { $sid = $_POST['sid'] ; }
	if ( isset( $_GET['sid'] ) ) { $sid = $_GET['sid'] ; }
	if ( isset( $_POST['sessionid'] ) ) { $sessionid = $_POST['sessionid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['requestid'] ) ) { $requestid = $_POST['requestid'] ; }
	if ( isset( $_GET['requestid'] ) ) { $requestid = $_GET['requestid'] ; }

	if ( !file_exists( "web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("./web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;

	// initialize
	if ( file_exists( "web/".$session_chat[$sid]['asp_login']."/$LOGO" ) && $LOGO )
		$logo = "web/".$session_chat[$sid]['asp_login']."/$LOGO" ;
	else if ( file_exists( "web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "web/$LOGO_ASP" ;
	else if ( file_exists( "themes/$THEME/images/logo.gif" ) )
		$logo = "themes/$THEME/images/logo.gif" ;
	else
		$logo = "images/logo.gif" ;
	
	$aspinfo = AdminASP_get_UserInfo( $dbh, $session_chat[$sid]['aspID'] ) ;
	$admin = AdminUsers_get_UserInfo( $dbh, $session_chat[$sid]['admin_id'], $session_chat[$sid]['aspID'] ) ;
	$department = AdminUsers_get_DeptInfo( $dbh, $session_chat[$sid]['deptid'], $session_chat[$sid]['aspID'] ) ;

	// conditions

	if ( !$admin['rateme'] && !$department['email_trans'] )
		$close_window = 1 ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Obrigado </title>

<script type="text/javascript" language="JavaScript1.2" src="js/chat_fn.js"></script>

<script type="text/javascript" language="JavaScript1.2">
<!--
	if ( <?php echo $close_window ?> )
	{
		window.close();
	}

	var pullimage ;

	function checkifloaded()
	{
		loaded = pullimage.width ;
		if ( loaded == 1 )
			window.close() ;
		else
			alert( "Error: Transcript did not send.  Please try again." ) ;
	}

	function do_submit()
	{
		if ( document.form.email.value != "" )
		{
			if ( document.form.email.value.indexOf("@") == -1 )
				alert( "<?php echo $LANG['MESSAGE_BOX_JS_A_INVALIDEMAIL'] ?>" ) ;
			else
				doit() ;
		}
		else
			doit() ;
	}

	function doit()
	{
		document.form.submitbutton.disabled = true ;
		document.form.submitbutton.value = "Please hold.  Sending ..." ;

		var email = document.form.email.value ;
		<?php if ( $admin['rateme']  ): ?>
		var rate_index = document.form.rate.selectedIndex ;
		var rate = document.form.rate[rate_index].value ;
		<?php else: ?>
		var rate = 0 ;
		<?php endif ; ?>
		var url = "<?php echo $BASE_URL ?>/admin/view_transcripts.php?action=send&l=<?php echo $session_chat[$sid]['asp_login'] ?>&x=<?php echo $session_chat[$sid]['aspID'] ?>&chat_session=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&deptid=<?php echo $session_chat[$sid]['deptid'] ?>&email="+email+"&optmessage=&rate="+rate ;

		pullimage = new Image ;
		pullimage.src = url ;
		pullimage.onload = checkifloaded ;
	}

	function opennewwin(url)
	{
		window.open(url, "newwin", "scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes") ;
	}
//-->
</script>


<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form onSubmit="return false;" name="form">
<div id="main">
	<div id="logo"><img src="<?php echo $logo ?>" alt="" /></div>
	<p>
	<div id="inputarea">
		<fieldset>
			<?php if ( $admin['rateme']  ): ?>
			<p>Qual a avalição sua do suporte prestado.</p>
			<dl>
				<dt><label for="rating">Avaliação</label></dt>
				<dd><select name="rate" id="rating" style="width: auto; ">
						<option value="0">Sem Respostas</option>
						<option value="4">Excelente</option>
						<option value="3">Muito Bom</option>
						<option value="2">Bom</option>
						<option value="1">Ruim</option>
					</select></dd>
			</dl>
			<?php else: ?>
			<input type="hidden" name="rating" value="0">
			<p>&nbsp;</p>
			<?php endif ; ?>
			<?php if ( $department['email_trans'] ): ?>
			<p><?php echo stripslashes( $aspinfo['trans_message'] ) ?></p>
			<dl>
				<dt><label for="email">Seu Email</label></dt>
				<dd class="textbox"><input type="text" name="email" id="email" size="40" maxlength="255" value=""></dd>
			</dl>
			<?php else: ?>
			<input type="hidden" name="email" value="">
			<?php endif ; ?>
			<dl></dl>
			<dl>
				<dt>&nbsp;</dt>
				<dd><br><input type="button" value="Enviar e Fechar Janela" onclick="do_submit()" class="button" name="submitbutton"></dd>
			</dl>
			<dl></dl>
			<dl>
				<dt>&nbsp;</dt>
				<dd><br><a href="javascript:void(0)" OnClick="window.open('admin/view_transcript.php?x=<?php echo $session_chat[$sid]['aspID'] ?>&l=<?php echo $session_chat[$sid]['asp_login'] ?>&chat_session=<?php echo $sessionid ?>&deptid=<?php echo $session_chat[$sid]['deptid'] ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&action=view', 'transcriptwin', 'status=no,scrollbars=no,menubar=no,toolbar=no,resizable=yes,location=no,width=450,height=360')" title="Printer friendly version" class="print">imprimir</a></dd>
			</dl>
		</fieldset>
		
		<div id="options">
			&nbsp;
		</div>
	
		<?php
			// because of tabbed browsers, we want to call a JavaScript window open function
			
			$branding = preg_replace( "/target=(.*?)(>| >)/i", " >", $branding ) ;
		?>
		<div id="copyright"><?php echo $branding ?> v<?php echo $PHPLIVE_VERSION ?> &copy;<a href="" target="_blank">Sistema de Atendimento online</a></div>
	</div>
</div>
</form>
</body>
</html>
