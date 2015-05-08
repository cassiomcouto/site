<?php
	session_start() ;
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util.php" ) ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Form.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/update.php") ;
?>
<?php

	// initialize
	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "20" ;
	else
		$text_width = "10" ;

	// get variables
	$success = 0 ;
	$action = $error = "" ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
?>
<?php
	// functions
?>
<?php
	// conditions
	if ( $action == "update" )
	{
		// make sure login is not taken if new login is different
		$aspid = $_POST['aspid'] ;
		$orig_login = $_POST['orig_login'] ;
		$login = $_POST['login'] ;
		$password = $_POST['password'] ;
		$company = $_POST['company'] ;
		$contact_name = $_POST['contact_name'] ;
		$contact_email = $_POST['contact_email'] ;
		$max_dept = $_POST['max_dept'] ;
		$max_users = $_POST['max_users'] ;
		$footprints = $_POST['footprints'] ;
		$active_status = $_POST['active_status'] ;
		$initiate_chat = ( isset( $_POST['initiate_chat'] ) ) ? $_POST['initiate_chat'] : 0 ;
		$nopconnect = $_POST['nopconnect'] ;

		if ( ( ( $orig_login != $login ) && !AdminASP_get_IsLoginTaken( $dbh, $login ) ) || ( $orig_login == $login ) )
		{
			if ( AdminASP_update_user( $dbh, $aspid, $login, $password, $company, $contact_name, $contact_email, $max_dept, $max_users, $footprints, $active_status, $initiate_chat ) )
			{
				if ( file_exists( "../web/$orig_login/$orig_login-conf-init.php" ) )
				{
					include_once( "../web/$orig_login/$orig_login-conf-init.php" ) ;

					// check to see if login is different.  if so, then we need to rename
					// the directory and then remove the old conf file
					if ( $orig_login != $login && file_exists( "../web/$orig_login/$orig_login-conf-init.php" ) )
					{
						unlink( "../web/$orig_login/$orig_login-conf-init.php" ) ;
						rename( "../web/$orig_login", "../web/$login" ) ;
					}

					$timezone = "" ;
					if ( preg_match( "/<:>/", $COMPANY_NAME ) )
						LIST( $COMPANY_NAME, $timezone ) = EXPLODE( "<:>", $COMPANY_NAME ) ;

					$conf_string = "0LEFT_ARROW0?php
						\$LOGO = '$LOGO' ;
						\$COMPANY_NAME = '$company<:>$timezone' ;
						\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
						\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
						\$SUPPORT_LOGO_AWAY = '$SUPPORT_LOGO_AWAY' ;
						\$VISITOR_FOOTPRINT = '$footprints' ;
						\$THEME = '$THEME' ;
						\$POLL_TIME = '$POLL_TIME' ;
						\$INITIATE = '$initiate_chat' ;
						\$INITIATE_IMAGE = '$INITIATE_IMAGE' ;
						\$IPNOTRACK = '$IPNOTRACK' ;
						\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;
				}
				else
				{
					if ( is_dir( "../web/$login" ) != true )
						mkdir( "../web/$login", 0755 ) ;
					$conf_string = "0LEFT_ARROW0?php
						\$LOGO = '' ;
						\$COMPANY_NAME = '$company<:>' ;
						\$SUPPORT_LOGO_ONLINE = 'phplive_support_online.gif' ;
						\$SUPPORT_LOGO_OFFLINE = 'phplive_support_offline.gif' ;
						\$SUPPORT_LOGO_AWAY = '' ;
						\$VISITOR_FOOTPRINT = '$footprints' ;
						\$THEME = 'default' ;
						\$POLL_TIME = '30' ;
						\$INITIATE = '$initiate_chat' ;
						\$INITIATE_IMAGE = '' ;
						\$IPNOTRACK = '' ;
						\$LANG_PACK = 'English'; ?0RIGHT_ARROW0" ;
				}

				$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
				$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
				$fp = fopen ("../web/$login/$login-conf-init.php", "wb+") ;
				fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
				fclose( $fp ) ;

				// now change the default conf file that is used for
				// ALL the sites
				if ( !isset( $ASP_KEY ) ) { $ASP_KEY = "" ; }
				// will add an extra "/" on windows systems to limit possible error
				$DOCUMENT_ROOT = addslashes( $DOCUMENT_ROOT ) ;
				$conf_string = "0LEFT_ARROW0?php
					\$ASP_KEY = '$ASP_KEY' ;
					\$NO_PCONNECT = '$nopconnect' ;
					\$DATABASETYPE = '$DATABASETYPE' ;
					\$DATABASE = '$DATABASE' ;
					\$SQLHOST = '$SQLHOST' ;
					\$SQLLOGIN = '$SQLLOGIN' ;
					\$SQLPASS = '$SQLPASS' ;
					\$DOCUMENT_ROOT = '$DOCUMENT_ROOT' ;
					\$BASE_URL = '$BASE_URL' ;
					\$SITE_NAME = '$company' ;
					\$LOGO_ASP = '$LOGO_ASP' ;
					\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;
				$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
				$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
				$fp = fopen ("../web/conf-init.php", "wb+") ;
				fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
				fclose( $fp ) ;

				$NO_PCONNECT = $nopconnect ;
				$INITIATE = $initiate_chat ;
				$success = 1 ;
			}
		}
		else
			$error = "That login is already in use." ;
	}

	$userinfo = AdminASP_get_UserInfo( $dbh, 1 ) ;
	$active_select = active_status( $userinfo['active_status'] ) ;
	$footprints_select = yesno( $userinfo['footprints'] ) ;
	$initiate_select = yesno( $userinfo['initiate_chat'] ) ;
?>
<?php include_once( "./header.php" ) ; ?>
<script language="JavaScript">
<!--
	function do_update_user()
	{
		var success = 1 ;
		for( c = 2; c < ( document.form.length - 1 ); ++c )
		{
			if ( document.form[c].value == "" )
			{
				alert( "All fields MUST be filled." ) ;
				success = 0 ;
				break ;
			}
		}
		if ( success )
		{
			if ( document.form.max_dept.value < 1 )
				alert( "Max department must be AT LEAST one." ) ;
			else if ( document.form.max_users.value < 1 )
				alert( "Max operators must be AT LEAST one." ) ;
			else
			{
				if ( confirm( "Are you sure?" ) )
					document.form.submit() ;
			}
		}
	}
//-->
</script>
<span id="result_box"><span title="Site Profile">Site perfil</span></span> - <a href="index.php"><span id="result_box"><span title="back to menu">voltar ao menu</span></span></a><br>
<p>
<font color="#FF0000"><?php echo $error ?></font>
<form method="POST" action="profile.php" name="form">
<input type="hidden" name="action" value="update">
<input type="hidden" name="aspid" value="<?php echo $userinfo['aspID'] ?>">
<input type="hidden" name="orig_login" value="<?php echo $userinfo['login'] ?>">
<input type="hidden" name="nopconnect" value="1">
<p>
<table cellpadding=1 cellspacing=1 border=0>
<tr>
	<td>Nome da Empresa </td>
	<td> <input type="text" name="company" size="<?php echo $text_width ?>" maxlength="50" onKeyPress="return nospecials(event)" value="<?php echo stripslashes( $userinfo['company'] ) ?>"></td>
</tr>
<tr>
	<td>Usuario - ADM </td>
	<td> <input type="text" name="login" size="<?php echo $text_width ?>" maxlength="15" onKeyPress="return nospecials(event)" value="<?php echo $userinfo['login'] ?>"></td>
	<td>Senha</td>
	<td> <input type="text" name="password" size="<?php echo $text_width ?>" maxlength="15"" value="<?php echo $userinfo['password'] ?>"></td>
</tr>
<tr>
	<td>Nome de Contato </td>
	<td> <input type="text" name="contact_name" size="<?php echo $text_width ?>" maxlength="50" value="<?php echo stripslashes( $userinfo['contact_name'] ) ?>"></td>
	<td>E-mail de Contato </td>
	<td> <input type="text" name="contact_email" size="<?php echo $text_width ?>" maxlength="150" value="<?php echo $userinfo['contact_email'] ?>"></td>
</tr>
<tr>
	<td>M&aacute;ximo de Departamentos</td>
	<td><input type="text" name="max_dept" size="4" maxlength="3" onKeyPress="return numbersonly(event)" value="<?php echo $userinfo['max_dept'] ?>"></td>
</tr>
<tr>
	<td>M&aacute;ximo de Operadores</td>
	<td><input type="text" name="max_users" size="4" maxlength="3" onKeyPress="return numbersonly(event)" value="<?php echo $userinfo['max_users'] ?>"></td>
</tr>
<tr>
	<td>Status</td>
	<td><select name="active_status"><?php echo $active_select ?></select></td>
</tr>
<tr>
	<td>Footprints</td>
	<td><select name="footprints" class="select"><?php echo $footprints_select ?></select></td>
</tr>
<?php if ( file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
<tr>
	<td colspan=4><span id="result_box"><span title="If initiate chat is disabled, operator's traffic monitor will also be disabled.">Se iniciar o bate-papo est&aacute; desativado, monitor do operador de tr&aacute;fego tamb&eacute;m ser&aacute; desativado.<br />
	</span><span title="This setting will override every department's initiate settings.">Esta defini&ccedil;&atilde;o ir&aacute; substituir as defini&ccedil;&otilde;es de cada departamento do in&iacute;cio.</span></span></td>
</tr>
<tr>
	<td><span id="result_box"><span title="Initiate Chat">Iniciar Chat</span></span></td>
	<td><select name="initiate_chat"><?php echo $initiate_select ?></select></td>
</tr>
<?php endif ; ?>
<tr>
	<td colspan=4>&nbsp;</td>
</tr>
<tr>
	<td></td>
	<td><input type="button" class="mainButton" value="Salvar" OnClick="do_update_user()"></td>
</tr>
</table>
</form>

<?php include_once( "./footer.php" ) ; ?>