<?php
	$current_version = "3.2.2" ;
	if ( !file_exists ( "../../web/VERSION_KEEP.php" ) )
	{
		HEADER( "location: ../../setup/" ) ;
		exit ;
	}

	include( "../../web/VERSION_KEEP.php" ) ;

	$error = $version = $notice_string = "" ;
	$url = 0 ;

	if ( isset( $_GET['version'] ) ) { $version = $_GET['version'] ; }
	if ( $version == "1.9.8" )
		$notice_string = "IMPORTANT!  You MUST Generate NEW HTML Code in the Setup area to avoid errors on your site and to activate the new features!" ;

	if ( !is_writeable( "../../web/VERSION_KEEP.php" ) )
	{
		print "<font color=\"#FF0000\">Warning!</font>  Your version file is not writeable!  Please change the permissions of your phplive/web/VERSION_KEEP.php file so that it is read/writeable." ;
		exit ;
	}

	switch ( $PHPLIVE_VERSION )
	{
		case "1.9":
			$url = "1.9.5_patch.php" ;
			break ;
		case "1.9.5":
			$url = "1.9.6_patch.php" ;
			break ;
		case "1.9.6":
			$url = "1.9.7_patch.php" ;
			break ;
		case "1.9.7":
			$url = "1.9.7.1_patch.php" ;
			break ;
		case "1.9.7.1":
			$url = "1.9.7.2_patch.php" ;
			break ;
		case "1.9.7.2":
			$url = "1.9.8_patch.php" ;
			break ;
		case "1.9.8":
			$url = "1.9.9_patch.php" ;
			break ;
		case "1.9.9":
			$url = "2.0_patch.php" ;
			break ;
		case "2.0":
			$url = "2.1_patch.php" ;
			break ;
		case "2.1":
			$url = "2.1.1_patch.php" ;
			break ;
		case "2.1.1":
			$url = "2.2_patch.php" ;
			break ;
		case "2.2":
			$url = "2.3_patch.php" ;
			break ;
		case "2.3":
			$url = "2.5_patch.php" ;
			break ;
		case "2.5":
			$url = "2.5.1_patch.php" ;
			break ;
		case "2.5.1":
			$url = "2.5.2_patch.php" ;
			break ;
		case "2.5.2":
			$url = "2.6_patch.php" ;
			break ;
		case "2.6":
			$url = "2.6.1_patch.php" ;
			break ;
		case "2.6.1":
			$url = "2.6.5_patch.php" ;
			break ;
		case "2.6.5":
			$url = "2.6.6_patch.php" ;
			break ;
		case "2.6.6":
			$url = "2.7_patch.php" ;
			break ;
		case "2.7":
			$url = "2.8_patch.php" ;
			break ;
		case "2.8":
			$url = "2.8.1_patch.php" ;
			break ;
		case "2.8.1":
			$url = "2.8.2_patch.php" ;
			break ;
		case "2.8.2":
			$url = "3.0_patch.php" ;
			break ;
		case "3.0":
			$url = "3.1_patch.php" ;
			break ;
		case "3.1":
			$url = "3.2_patch.php" ;
			break ;
		case "3.2":
			$url = "3.2.1_patch.php" ;
			break ;
		case "3.2.1":
			$url = "3.2.2_patch.php" ;
			break ;
		case $current_version:
			$error = "<span class=\"basicTitle\">Your PHP Live! <font color=\"#3333FF\">version $current_version</font> is up to date.  No more patches available.<br><br> <a href=\"../index.php\">Return to PHP Live! Setup</a></span>" ;
			break ;
		default:
			$error = "Your version is too old.  You MUST do a FRESH install.  Remove this current system and install NEW." ;
	}
?>
<html>
<head>
<title> Upgrading and Patching your system </title>
<script language="JavaScript">
<!--
	if ( '<?php echo $url ?>' && ( '<?php echo $url ?>' != '0' ) )
		setTimeout("location.href='<?php echo $url ?>'",5000) ;
//-->
</script>
<?php $css_path = "../../" ; include_once( "../../css/default.php" ) ; ?>
</head>

<body bgColor="#FFFFFF" text="#000000" link="#35356A" vlink="#35356A" alink="#35356A">
<table cellspacing=0 cellpadding=0 border=0 width="98%">
<tr>
	<td valign="top"><span class="basetxt">
		<img src="../../images/logo.gif">
		<p>

		<?php if ( $url ): ?>
		<big><b>Upgrade Patch Available!</b></big>
		<p>
		<big><b>Redirecting you to <font color="#FF0000"><?php echo $url ?></font> - please hold...</b></big>

		<?php else: ?>
		<table cellspacing=0 cellpadding=2 border=0 width="100%">
		<tr>
			<td><span class="basetxt"><big><b>
				<?php echo $notice_string ?>
				</b></big>
			</td>
		</table>
		<p>
		<font color="#FF0000"><big><b><?php echo $error ?></b></big></big>

		<?php endif ; ?>
	</td>
</tr>
</table>
<br>
</body>
</html>