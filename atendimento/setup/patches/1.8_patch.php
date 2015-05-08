<?
	if ( !file_exists( "../../web/conf-init.php" ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	// patch check for correct version patch.  this patch is for version 1.6
	$PATCH_VERSION = "1.8" ;

	include("../../web/conf-init.php") ;
	include("../../web/VERSION_KEEP.php") ;
	include("../../system.php") ;
	include("../../lang_packs/$LANG_PACK.php") ;
	include("../../API/sql.php" ) ;
	include("../../API/Users/get.php") ;
	include("../../API/Users/put.php") ;
?>
<?

	// initialize
	if ( preg_match( "/MSIE/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "20" ;
	else
		$text_width = "10" ;
?>
<?
	// functions
?>
<?
	// conditions

	if ( ( $action == "execute" ) && $login )
	{
		if ( file_exists( "../../web/patches/$PATCH_VERSION"."_patch.log" ) )
			$error = "Your system is ALREADY PATCHED for v$PATCH_VERSION!" ;
		else if ( !preg_match( "/(1.6)|(1.7)/", $PHPLIVE_VERSION ) )
			$error = "ERROR: YOU MUST FIRST OVERRIDE YOUR OLD COPY OF PHP LIVE! WITH THE NEW PATCHED FILES" ;
		else
		{
			// create patch log dir to keep track of if system has been patched
			if ( is_dir( "../../web/patches" ) != true )
				mkdir( "../../web/patches", 0755 ) ;

			// create the "chatdepartments" table
			$query = "CREATE TABLE chat_asp (
				aspID int(10) unsigned NOT NULL auto_increment,
				login char(15) NOT NULL default '',
				password char(15) NOT NULL default '',
				company char(50) NOT NULL default '',
				contact_name char(50) NOT NULL default '',
				contact_email char(160) NOT NULL default '',
				max_dept tinyint(3) NOT NULL default '0',
				max_users tinyint(3) NOT NULL default '0',
				footprints tinyint(1) NOT NULL default '0',
				created int(10) unsigned NOT NULL default '0',
				last_login int(10) unsigned NOT NULL default '0',
				active_status tinyint(1) NOT NULL default '0',
				PRIMARY KEY  (aspID),
				KEY created (created),
				KEY max_dept (max_dept),
				KEY max_users (max_users),
				KEY active_status (active_status)
			)" ;
			database_mysql_query( $dbh, $query ) ;

			$now = time() ;
			$query = "INSERT INTO chat_asp VALUES( 0, '$login', '$password', '$company', '$contact_name', '$contact_email', 10, 50, 1, $now, 0, 1 )" ;
			database_mysql_query( $dbh, $query ) ;
			
			$query = "ALTER TABLE chattranscripts CHANGE `from_screen_name` `from_screen_name` VARCHAR(50) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests CHANGE `from_screen_name` `from_screen_name` VARCHAR(50) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatdepartments ADD `transcript_save` TINYINT(1) NOT NULL AFTER `name`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD `question` CHAR(150) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "ALTER TABLE chat_admin ADD `aspID` INT(10) UNSIGNED DEFAULT '1' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_admin ADD INDEX (`aspID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatdepartments ADD `aspID` INT(10) UNSIGNED DEFAULT '1' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatdepartments ADD INDEX (`aspID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatfootprints ADD `aspID` INT(10) UNSIGNED DEFAULT '1' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatfootprints ADD INDEX (`aspID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs ADD `aspID` INT(10) UNSIGNED DEFAULT '1' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs ADD INDEX (`aspID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chattranscripts ADD `aspID` INT(10) UNSIGNED DEFAULT '1' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chattranscripts ADD INDEX (`aspID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_admin ADD `signal` TINYINT(1) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;

			// create the actual patch file
			$date = date( "D m/d/y h:i a", time() ) ;
			$success_string = "DO NOT DELETE OR SYSTEM MAY PATCH AGAIN!  THAT'S NOT GOOD!\n[$date] PATCH SUCCESSFUL from v1.6.x/1.7.x to v$PATCH_VERSION\n" ;
			$fp = fopen ("../../web/patches/$PATCH_VERSION"."_patch.log", "wb+") ;
			fwrite( $fp, $success_string, strlen( $success_string ) ) ;
			fclose( $fp ) ;

			// create and put version file for v1.8
			$version_string = "0LEFT_ARROW0? \$PHPLIVE_VERSION = \"$PATCH_VERSION\" ; ?0RIGHT_ARROW0" ;
			$version_string = preg_replace( "/0LEFT_ARROW0/", "<", $version_string ) ;
			$version_string = preg_replace( "/0RIGHT_ARROW0/", ">", $version_string ) ;
			$fp = fopen ("../../web/VERSION_KEEP.php", "wb+") ;
			fwrite( $fp, $version_string, strlen( $version_string ) ) ;
			fclose( $fp ) ;

			$conf_string = "0LEFT_ARROW0?
				\$LOGO = '$LOGO' ;
				\$COMPANY_NAME = '$SITE_NAME' ;
				\$SUPPORT_LOGO_ONLINE = '$SUPPORT_LOGO_ONLINE' ;
				\$SUPPORT_LOGO_OFFLINE = '$SUPPORT_LOGO_OFFLINE' ;
				\$VISITOR_FOOTPRINT = '$VISITOR_FOOTPRINT' ;
				\$TEXT_COLOR = '$TEXT_COLOR' ;
				\$LINK_COLOR = '$LINK_COLOR' ;
				\$ALINK_COLOR = '$ALINK_COLOR' ;
				\$VLINK_COLOR = '$VLINK_COLOR' ;
				\$CLIENT_COLOR = '$CLIENT_COLOR' ;
				\$ADMIN_COLOR = '$ADMIN_COLOR' ;
				\$CHAT_BACKGROUND = '$CHAT_BACKGROUND' ;
				\$CHAT_REQUEST_BACKGROUND = '$CHAT_REQUEST_BACKGROUND' ;
				\$CHAT_BOX_BACKGROUND = '$CHAT_BOX_BACKGROUND' ;
				\$CHAT_BOX_TEXT = '$CHAT_BOX_TEXT' ;
				\$POLL_TIME = '15' ;
				\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

			if ( is_dir( "../../web/$login" ) != true )
				mkdir( "../../web/$login", 0755 ) ;

			$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
			$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
			$fp = fopen ("../../web/$login/$login-conf-init.php", "wb+") ;
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
			fclose( $fp ) ;
			
			// create new default conf-init.php for global usages
			$conf_string = "0LEFT_ARROW0?
				\$NO_PCONNECT = '0' ;
				\$DATABASETYPE = '$DATABASETYPE' ;
				\$DATABASE = '$DATABASE' ;
				\$SQLHOST = '$SQLHOST' ;
				\$SQLLOGIN = '$SQLLOGIN' ;
				\$SQLPASS = '$SQLPASS' ;
				\$DOCUMENT_ROOT = '$DOCUMENT_ROOT' ;
				\$BASE_URL = '$BASE_URL' ;
				\$SITE_NAME = '$company' ;
				\$LOGO_ASP = '$LOGO' ;
				\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;
			$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
			$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
			$fp = fopen ("../../web/conf-init.php", "wb+") ;
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
			fclose( $fp ) ;

			// let's create an index file for the user so the path is more nice...
			// (/phplive/<user>/ instead of /phplive/index.php?l=<user>)
			$index_string = "0LEFT_ARROW0? \$path = explode( \"/\", \$PHP_SELF ) ; \$total = count( \$path ) ; \$login = \$path[\$total-2] ; HEADER( \"location: ../../index.php?l=\$login\" ) ; exit ; ?0RIGHT_ARROW0" ;
			$index_string = preg_replace( "/0LEFT_ARROW0/", "<", $index_string ) ;
			$index_string = preg_replace( "/0RIGHT_ARROW0/", ">", $index_string ) ;
			$fp = fopen ("../../web/$login/index.php", "wb+") ;
			fwrite( $fp, $index_string, strlen( $index_string ) ) ;
			fclose( $fp ) ;

			// now let's create an index.php page in the web/ directory for
			// extra security
			$index_string = "&nbsp;" ;
			$fp = fopen ("../../web/index.php", "wb+") ;
			fwrite( $fp, $index_string, strlen( $index_string ) ) ;
			fclose( $fp ) ;

			/**************************************
			/ let's do some clean up
			**************************************/
			if ( file_exists( "../../web/ip_notrack.txt" ) )
			{
				copy( "../../web/ip_notrack.txt", "../../web/$login/ip_notrack.txt" ) ;
				unlink( "../../web/ip_notrack.txt" ) ;
			}
			if ( file_exists( "../../web/$LOGO" ) && !is_dir( "../../web/$LOGO" ) )
			{
				copy( "../../web/$LOGO", "../../web/$login/$LOGO" ) ;
				unlink( "../../web/$LOGO" ) ;
			}
			if ( file_exists( "../../web/$SUPPORT_LOGO_ONLINE" ) && !is_dir( "../../web/$SUPPORT_LOGO_ONLINE" ) )
			{
				copy( "../../web/$SUPPORT_LOGO_ONLINE", "../../web/$login/$SUPPORT_LOGO_ONLINE" ) ;
				unlink( "../../web/$SUPPORT_LOGO_ONLINE" ) ;
			}
			if ( file_exists( "../../web/$SUPPORT_LOGO_OFFLINE" ) && !is_dir( "../../web/$SUPPORT_LOGO_OFFLINE" ) )
			{
				copy( "../../web/$SUPPORT_LOGO_OFFLINE", "../../web/$login/$SUPPORT_LOGO_OFFLINE" ) ;
				unlink( "../../web/$SUPPORT_LOGO_OFFLINE" ) ;
			}
			/*********** end clean up ************/

			$success = 1 ;
		}
	}
?>
<html>
<head>
<title> v1.6x/1.7x to v<?php echo $PATCH_VERSION ?> Patch </title>
<script language="JavaScript">
<!--
	function do_patch()
	{
		if ( ( document.form.login.value == "" ) || ( document.form.password.value == "" )
			|| ( document.form.company.value == "" ) || ( document.form.contact_name.value == "" )
			|| ( document.form.contact_email.value == "" ) )
			alert( "All fields must be supplied." ) ;
		else
			document.form.submit() ;
	}
//-->
</script>
<link rel="Stylesheet" href="../../css/base.css">
</head>

<body bgColor="#FFFFFF" text="#000000" link="#35356A" vlink="#35356A" alink="#35356A">
<table cellspacing=0 cellpadding=0 border=0 width="98%">
<tr>
	<td valign="top"><span class="basetxt">
		<img src="../../images/logo.gif">
		<br>

		<?php if ( $success ): ?>
		<br>
		<big><b>Congratulations!  Your system DB has been patched from v1.6/7 to v<?php echo $PATCH_VERSION ?>!</b></big>
		<p>
		<p>
		<big><b>IMPORTANT!</b></big><br>
		Because of the new database and system structure, there is a separate area to update your company information and to customize your company logo.  <font color="#FF0000">Please UNPROTECT your setup directory!  Please PROTECT your super/ directory!</font>  The setup directory has it's own login procedure, so you will not need to password protect.
		<p>
		<big><b>CREATE NEW STATUS ICON HTML CODE FROM YOUR SETUP AREA!</b></big><br>
		Because of the new system structure, you MUST go to your setup area and create and create new HTML code to put on your site.  YOUR status image will be broken until you do this!<br>
		<?php echo $BASE_URL ?>/web/<?php echo $login ?>
		<p>
		<li> <a href="../../super">Return to setup menu</a>







		<?php else: ?>
		<font color="#FF0000"><?php echo $error ?></font><br>
		<big><b>This v<?php echo $PATCH_VERSION ?> DB patch will do the following:</b></big>
		<ol>
			<form method="GET" action="1.8_patch.php" method="POST" name="form">
			<input type="hidden" name="action" value="execute">
			<li> Alter the neccessary table to reflect the new v1.8 restructered feature
				<p>
			
			<li> <b>Please supply your company information below:</b><br>
				<table cellspacing=1 cellpadding=1 border=0>
				<tr>
					<td><span class="basetxt">Chose Login</td>
					<td><font size=2><input type="text" name="login" size="<?php echo $text_width ?>" maxlength="15" class="input"></td>
				</tr>
				<tr>
					<td><span class="basetxt">Chose Password</td>
					<td><font size=2><input type="text" name="password" size="<?php echo $text_width ?>" maxlength="15" class="input"></td>
				</tr>
				<tr>
					<td><span class="basetxt">Company Name</td>
					<td><font size=2><input type="text" name="company" size="<?php echo $text_width ?>" maxlength="50" class="input"></td>
				</tr>
				<tr>
					<td><span class="basetxt">Contact Name</td>
					<td><font size=2><input type="text" name="contact_name" size="<?php echo $text_width ?>" maxlength="50" class="input"></td>
				</tr>
				<tr>
					<td><span class="basetxt">Contact Email</td>
					<td><font size=2><input type="text" name="contact_email" size="<?php echo $text_width ?>" maxlength="160" class="input"></td>
				</tr>
				</table>

			<p>
			After you run this patch, everything will run as normal and all user passwords will remain the same.  Click the below button to run the patch.<p>

			If you do not run the patch, the system will NOT function normally.

			<p>
			<input type="button" value="Execute Patch" OnClick="do_patch()">
			</form>
		</ol>
		<?php endif ?>



	</td>
</tr>
</table>
<p>
<font color="#9999B5" size=2><?php echo $LANG['DEFAULT_BRANDING'] ?></font>
<br>
</body>
</html>