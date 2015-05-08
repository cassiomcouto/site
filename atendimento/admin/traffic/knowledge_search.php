<?php
	$l = ( isset( $_GET['l'] ) ) ? $_GET['l'] : "" ;
	$x = ( isset( $_GET['x'] ) ) ? $_GET['x'] : "" ;
	$deptid = ( isset( $_GET['deptid'] ) ) ? $_GET['deptid'] : 0 ;

	include_once( "../../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "../..", $l ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting... [knowledge_search.php]</font>" ; exit ;
	}
	include_once("../../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$l/$l-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;

	if ( file_exists( "$DOCUMENT_ROOT/web/$l/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/$l/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else if ( file_exists( "$DOCUMENT_ROOT/themes/$THEME/images/logo.gif" ) )
		$logo = "$BASE_URL/themes/$THEME/images/logo.gif" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	// conditions
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Knowledge BASE (FAQ) </title>

<link href="<?php echo $BASE_URL ?>/css/layout.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $BASE_URL ?>/themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!--
//-->
</script>

</head>
<body>
<div id="main">

	<div id="logo"><img src="<?php echo $logo ?>" alt="" border=0 /></div><br />

	<div id="chat">
		<iframe src="knowledge_searchm.php?l=<?php echo $l ?>&x=<?php echo $x ?>&action=<?php echo $action ?>&deptid=<?php echo $deptid ?>" width="100%" height="240" frameborder="0" name="fmain" id="fmain"></iframe>
	</div>

	<div id="options">
		<a href="<?php echo $BASE_URL ?>/request.php?x=<?php echo $x ?>&l=<?php echo $l ?>&deptid=<?php echo $deptid ?>">Return to Live Support</a>
	</div>
	<div id="copyright"><?php echo $LANG['DEFAULT_BRANDING'] ?> v<?php echo $PHPLIVE_VERSION ?> &copy; OSI Codes Inc.</div>
	
</div>
</body>
</html>