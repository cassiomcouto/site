<?php
	/*******************************************************
	* COPYRIGHT OSI CODES - PHP Live!
	*******************************************************/
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
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;

	// initialize
	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$textbox_width = "80" ;
	else
		$textbox_width = "50" ;

	$deptid = ( isset( $_GET['deptid'] ) ) ? $_GET['deptid'] : "" ;

	$BASE_URL_STRIP = $BASE_URL ;
	//$BASE_URL_STRIP = preg_replace( "/http:/", "", $BASE_URL ) ;

	$text = isset( $_GET['text'] ) ? stripslashes( $_GET['text'] ) : "" ;
	$text = str_replace( " ", "+", $text ) ;
	$text = str_replace( "\"", "", $text ) ;
	$text = str_replace( "'", "", $text ) ;
?>
<html>
<head>
<title> Text Only HTML Code </title>
<?php $css_path = "../" ; include( "../css/default.php" ) ; ?>

<script language="JavaScript">
<!--

//-->
</script>

</head>
<body bgColor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr>
	<form>
	<td valign="top" class="bg" align="center">
		<span class="basicTitle">TEXT ONLY HTML code</span>
		<br>
<textarea cols="<?php echo $textbox_width ?>" rows="8" wrap="virtual" name="messagebox" onFocus="this.select(); return true;">
<script language="JavaScript" src="<?php echo $BASE_URL_STRIP ?>/js/status_image.php?base_url=<?php echo $BASE_URL_STRIP ?>&l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=<?php echo $deptid ?>&text=<?php echo $text ?>"></script>
</textarea>
	</td>
	</form>
  </tr>
  <tr> 
	<td height="20" align="right" class="bgFooter" style="height:20px"><img src="<?php echo $css_path ?>images/bg_corner_footer.gif" alt="" width="94" height="20"></td>
  </tr>
</table>
</body>
</html>