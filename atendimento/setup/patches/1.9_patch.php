<?
	if ( !file_exists( "../../web/conf-init.php" ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	// patch check for correct version patch.  this patch is for version 1.6
	$PATCH_VERSION = "1.9" ;

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

	if ( $action == "execute" )
	{
		if ( file_exists( "../../web/patches/$PATCH_VERSION"."_patch.log" ) )
			$error = "Your system is ALREADY PATCHED for v$PATCH_VERSION!" ;
		else if ( !preg_match( "/(1.8)/", $PHPLIVE_VERSION ) )
			$error = "ERROR: YOU ARE NOT RUNNING v1.8 AND PATCH WILL NOT WORK.  PLEASE FRESH INSTALL v1.9 OR UPGRADE TO v1.8 BEFORE RUNNING THIS PATCH." ;
		else
		{
			// create patch log dir to keep track of if system has been patched
			if ( is_dir( "../../web/patches" ) != true )
				mkdir( "../../web/patches", 0755 ) ;

			// create the "chatdepartments" table
			$query = "CREATE TABLE chatfootprintsunique (
						ip varchar(20) NOT NULL default '',
						created int(10) unsigned NOT NULL default '0',
						updated int(10) unsigned NOT NULL default '0',
						url varchar(255) NOT NULL default '',
						aspID int(10) unsigned NOT NULL default '1',
						PRIMARY KEY  (ip,aspID),
						KEY url (url)
					)" ;
			database_mysql_query( $dbh, $query ) ;
			
			$query = "ALTER TABLE chatrequestlogs ADD `hostname` CHAR(150) NOT NULL AFTER `ip`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_asp CHANGE `max_users` `max_users` MEDIUMINT(3) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_asp CHANGE `max_dept` `max_dept` MEDIUMINT(3) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_asp ADD `initiate_chat` TINYINT(1) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD `aspID` INT(10) UNSIGNED NOT NULL AFTER `deptID`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD INDEX (`aspID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_admin ADD `console_close_min` MEDIUMINT(10) UNSIGNED DEFAULT '10' NOT NULL AFTER `last_active_time`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chat_admin ADD `session_sid` INT(10) UNSIGNED NOT NULL AFTER `console_close_min`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatsessions CHANGE `sessionID` `sessionID` INT(10) UNSIGNED NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatdepartments ADD `initiate_chat` TINYINT(1) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatsessions ADD `initiate` CHAR(15) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;

			if ( is_dir( "../../web/chatrequests" ) != true )
				mkdir( "../../web/chatrequests", 0777 ) ;
			if ( is_dir( "../../web/chatpolling" ) != true )
				mkdir( "../../web/chatpolling", 0777 ) ;

			// create the actual patch file
			$date = date( "D m/d/y h:i a", time() ) ;
			$success_string = "DO NOT DELETE OR SYSTEM MAY PATCH AGAIN!  THAT'S NOT GOOD!\n[$date] PATCH SUCCESSFUL from v1.6.x/1.7.x to v$PATCH_VERSION\n" ;
			$fp = fopen ("../../web/patches/$PATCH_VERSION"."_patch.log", "wb+") ;
			fwrite( $fp, $success_string, strlen( $success_string ) ) ;
			fclose( $fp ) ;

			// create and put version file
			$version_string = "0LEFT_ARROW0? \$PHPLIVE_VERSION = \"$PATCH_VERSION\" ; ?0RIGHT_ARROW0" ;
			$version_string = preg_replace( "/0LEFT_ARROW0/", "<", $version_string ) ;
			$version_string = preg_replace( "/0RIGHT_ARROW0/", ">", $version_string ) ;
			$fp = fopen ("../../web/VERSION_KEEP.php", "wb+") ;
			fwrite( $fp, $version_string, strlen( $version_string ) ) ;
			fclose( $fp ) ;

			$success = 1 ;
		}
	}
?>
<html>
<head>
<title> v1.8 to v<?php echo $PATCH_VERSION ?> Patch </title>
<script language="JavaScript">
<!--
	function do_patch()
	{
		if ( confirm( "Ready to upgrade to v1.9?" ) )
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
		<big><b>Congratulations!  Your system DB has been patched from v1.8 to v<?php echo $PATCH_VERSION ?>!</b></big>
		<p>
		THINGS YOU SHOULD DO.
		<p>
		<big><b>1. Turn Persistent DB Connection OFF.</b></big><br>
		It is recommended that you put persistent DB connection to OFF.  Our testing shows that having it OFF decreases server load and strain.  You can set this value in your <b><a href="<?php echo $BASE_URL ?>/super/"><?php echo $BASE_URL ?>/super</a></b> area.
		<p>
		<big><b>2. Turn initiate chat feature ON.</b></big><br>
		v1.9's core feature is initiate chat.  You must turn this function on in your <b><a href="<?php echo $BASE_URL ?>/super/"><?php echo $BASE_URL ?>/super</a></b> area.  Once you turn initiate chat on, you can set this option for EACH department in the setup department area.
		<p>
		<font color="#FF0000"><big><b>3. CREATE NEW STATUS ICON HTML CODE FROM YOUR SETUP AREA!</b></big></font><br>
		Because of the new system structure, you MUST go to your setup area and create new HTML code to put on your site.  YOUR status image will not function properly until you do this!<br>
		<?php echo $BASE_URL ?>/setup/
		<p>
		<li> <a href="<?php echo $BASE_URL ?>/setup/">Return to setup area</a>







		<?php else: ?>
		<font color="#FF0000"><?php echo $error ?></font><br>
		<big><b>This v<?php echo $PATCH_VERSION ?> DB patch will do the following:</b></big>
		<ol>
			<form method="GET" action="1.9_patch.php" method="POST" name="form">
			<input type="hidden" name="action" value="execute">
			<li> Alter and create the neccessary table to reflect the new v1.9 restructered feature
				<p>
			<li> Create some folders that will be used for v1.9 initiate chat feature.
				<p>

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
</body>
</html>