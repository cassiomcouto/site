<?php
	include_once("./web/conf-init.php") ;
	include_once("./system.php") ;
	include_once( "./API/Util_Dir.php" ) ;

	if ( !Util_DIR_CheckDir( ".", $_GET['l'] ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Saindo de request_email.php ...</font>" ;
		exit ;
	}
?>
<html>
<head>
<title>  </title>
<script language="JavaScript">
<!--
// the reason for using date is to set a unique value so the status
// image is NOT CACHED (Netscape problem).  keep this or bad things could happen
var date = new Date() ;
var unique = date.getTime() ;
var request_url = "<?php echo $BASE_URL ?>/request.php?l=<?php echo $_GET['l'] ?>&x=<?php echo $_GET['x'] ?>&deptid=<?php echo isset( $_GET['deptid'] ) ? $_GET['deptid'] : 0 ?>&pagex=Email+Signature" ;

function launch_support()
{
	/*
	if ( navigator.userAgent.indexOf("MSIE") != -1 )
		top.resizeTo( 480, 540 ) ;
	else if ( navigator.userAgent.indexOf("Firefox") != -1 )
		top.resizeTo( 470, 522 ) ;
	else
		top.resizeTo( 470, 498 ) ;
	*/

	location.href = request_url ;
}
//-->
</script>
</head>
<body bgColor="#FFFFFF" OnLoad="launch_support()">
</body>
</html>