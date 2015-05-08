<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; }
	$action = $error = $login = $password = $l = "" ;
	if ( !file_exists( "../web/conf-init.php" ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Error.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;

	// initialize
	if ( !isset( $_SESSION['session_setup'] ) )
	{
		session_register( "session_setup" ) ;
		$session_setup = ARRAY() ;
		$_SESSION['session_setup'] = ARRAY() ;
	}

	if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	// conditions
	if ( $action == "login" )
	{
		if ( isset( $_POST['login'] ) ) { $login = $_POST['login'] ; }
		if ( isset( $_GET['login'] ) ) { $login = $_GET['login'] ; }
		if ( isset( $_POST['password'] ) ) { $password = $_POST['password'] ; }
		if ( isset( $_GET['password'] ) ) { $password = $_GET['password'] ; }

		$admin = AdminASP_get_UserInfoByLoginPass( $dbh, $login, $password ) ;
		if ( $admin['aspID'] )
		{
			if ( $admin['active_status'] )
			{
				$_SESSION['session_setup'] = $admin ;
				HEADER( "location: $BASE_URL/setup/options.php" ) ;
				exit ;
			}
			else
				$error = "conta inativa." ;
		}
		else
			$error = "senha invalida." ;
	}
	else if ( $action == "logout" )
	{
		session_unregister( "session_setup" ) ;
	}
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o geral - Sistema de Atendimento Online</title>
<?php $css_path = "../" ; include_once( "../css/default.php" ) ; ?>

<script language="JavaScript">
<!--
	function do_alert()
	{
		<?php
			if ( $error )
				print " alert( \"$error\" ) ;" ;
		?>
	}
//-->
</script>

<style type="text/css">
<!--
.style1 {
	color: #0000FF;
	font-weight: bold;
}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" OnLoad="do_alert()">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="65" valign="top" class="bgHead"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="102" height="65" valign="bottom" class="bgCornerTop"><img src="../images/bg_corner_top.gif" width="102" height="65"></td>
		  <td height="65"><div id="logo"><img src="<?php echo $logo ?>" border="0"></div></td>
		  <td align="right" valign="top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td><img src="../images/bg_corner_bot.gif" width="121" height="47"> <img src="../images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td align="center" valign="top" class="bg">
		<!-- **** Start of the page body area **** -->
		<form method="POST" action="<?php echo $BASE_URL ?>/setup/login.php" name="form">
		<input type="hidden" name="action" value="login">
		<table cellspacing=1 cellpadding=3 border=0 width="300">
		<tr align="center"> 
			<th colspan=2><span class="basicTitle">Area de Administra&ccedil;&atilde;o</span></th>
		</tr>
		<tr> 
			<td align="right"><strong>Usuario:</strong></td>
			<td> 
			<input type="text" style="width: 150px" name="login" size="10" maxlength="25" value="<?php echo $login ?>"></td>
		</tr>
		<tr> 
			<td align="right"><strong>Senha:</strong></td>
			<td> 
			<input type="password"  style="width: 150px" name="password" size="10" maxlength="15"></td>
		</tr>
		<tr> 
			<td>&nbsp;</td>
			<td> 
			<input name="Submit" type="submit" class="mainButton" value="Entrar"></td>
		</tr>
	  </table>
	</form>

		<!-- **** End of the page body area **** -->
	  </p></td>
  </tr>
  <tr> 
	<td height="20" align="right" class="bgFooter" style="height:20px"><img src="../images/bg_corner_footer.gif" alt="" width="94" height="20"></td>
  </tr>
  <tr> 
	<td height="20" align="center" class="bgCopyright" style="height:20px">
		<span class="style1"> | Sistema de Atendimento Online | </span><?php echo $PHPLIVE_VERSION ?> &copy; <a href="" target="_blank" class="style1"> </a></span>	</td>
  </tr>
</table>
</body>
</html>
<?php

//	mysql_close( $dbh['con'] ) ;

?>
