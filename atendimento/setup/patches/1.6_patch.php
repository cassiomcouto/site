<?
	if ( !file_exists( "../../web/conf-init.php" ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	// patch check for correct version patch.  this patch is for version 1.6
	$PATCH_VERSION = "1.6" ;

	include("../../web/conf-init.php") ;
	include("../../system.php") ;
	include("../../lang_packs/$LANG_PACK.php") ;
	include("../../API/sql.php" ) ;
	include("../../API/Users/get.php") ;
	include("../../API/Users/put.php") ;
?>
<?

	// initialize
?>
<?
	// functions
?>
<?
	// conditions

	if ( $action == "execute" )
	{
		if ( !preg_match( "/(1.5)/", $VERSION ) )
			$error = "ERROR: YOU MUST FIRST OVERRIDE YOUR OLD COPY OF PHP LIVE! WITH THE NEW PATCHED FILES" ;
		else if ( file_exists( "../../web/patches/$PATCH_VERSION"."_patch.log" ) )
			$error = "Your system is ALREADY PATCHED for v1.6!" ;
		else
		{
			// create patch log dir to keep track of if system has been patched
			if ( is_dir( "../../web/patches" ) != true )
				mkdir( "../../web/patches", 0755 ) ;

			// create the "chatdepartments" table
			$query = "CREATE TABLE chatdepartments (
				deptID int(10) unsigned NOT NULL auto_increment,
				name char(30) NOT NULL default '',
				transcript_share tinyint(1) NOT NULL default '0',
				transcript_expire_string char(10) NOT NULL default '',
				transcript_expire int(10) NOT NULL default '0',
				email char(150) NOT NULL default '',
				PRIMARY KEY  (deptID)
				)" ;
			database_mysql_query( $dbh, $query ) ;

			// create the "chatuserdeptlist" table - used to assign users to departments
			$query = "CREATE TABLE chatuserdeptlist (
				userID int(10) unsigned NOT NULL default '0',
				deptID int(10) unsigned NOT NULL default '0',
				KEY userID (userID),
				KEY deptID (deptID)
				)" ;
			database_mysql_query( $dbh, $query ) ;

			// create table to capture visitor foot prints
			$query = "CREATE TABLE chatfootprints (
				printID int(10) unsigned NOT NULL auto_increment,
				ip varchar(20) NOT NULL default '',
				created int(10) unsigned NOT NULL default '0',
				url char(255) NOT NULL default '',
				PRIMARY KEY  (printID),
				KEY ip (ip),
				KEY created (created),
				KEY url (url)
				)" ;
			database_mysql_query( $dbh, $query ) ;
			
			// select all the departments
			$query = "SELECT DISTINCT(department) FROM chat_admin" ;
			database_mysql_query( $dbh, $query ) ;
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$departments[] = $data ;
			} 

			// move all departments to use the deptID
			for ( $c = 0; $c < count( $departments ); ++$c )
			{
				$department = $departments[$c] ;

				$name = addslashes( $department[department] ) ;

				// first add the departments into the new created table
				$query = "INSERT INTO chatdepartments VALUES( 0, '$name', 0, '', 0, '' )" ;
				database_mysql_query( $dbh, $query ) ;
				$id = database_mysql_insertid( $dbh ) ;
				
				// now update all entries of the department to use the $id
				$query = "UPDATE chat_admin SET department = '$id' WHERE department = '$name'" ;
				database_mysql_query( $dbh, $query ) ;
				$query = "UPDATE chatrequestlogs SET department = '$id' WHERE department = '$name'" ;
				database_mysql_query( $dbh, $query ) ;
				$query = "UPDATE chattranscripts SET department = '$id' WHERE department = '$name'" ;
				database_mysql_query( $dbh, $query ) ;
			}

			// now move all departments
			$users = AdminUsers_get_AllUsers( $dbh, 0, 0 ) ;
			for ( $c = 0; $c < count( $users ); ++$c )
			{
				$user = $users[$c] ;
				$query = "INSERT INTO chatuserdeptlist VALUES( $user[userID], $user[department] )" ;
				database_mysql_query( $dbh, $query ) ;
			}

			// now we have to alter the department table to use the deptID and to create index
			$query = "ALTER TABLE chat_admin DROP `department`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs CHANGE `department` `deptID` INT(10) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs ADD INDEX(`deptID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chattranscripts CHANGE `department` `deptID` INT(10) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chattranscripts ADD INDEX(`deptID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD `deptID` INT(10) NOT NULL AFTER `userID`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD INDEX (`deptID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD `url` CHAR(255) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequests ADD INDEX(`url`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs ADD `url` CHAR(255) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs ADD INDEX(`url`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatsessionlist CHANGE `screen_name` `screen_name` CHAR(50) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatsessions CHANGE `screen_name` `screen_name` CHAR(50) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatsessions DROP `chatfile`" ;
			database_mysql_query( $dbh, $query ) ;

			// create the actual patch file
			$date = date( "D m/d/y h:i a", time() ) ;
			$success_string = "DO NOT DELETE OR SYSTEM MAY PATCH AGAIN!  THAT'S NOT GOOD!\n[$date] PATCH SUCCESSFUL from v1.5.3 to v$PATCH_VERSION - Added new 'chatdepartments' table and migrated all departments to use that table.  Altered database to use the deptID.\n" ;
			$fp = fopen ("../../web/patches/$PATCH_VERSION"."_patch.log", "wb+") ;
			fwrite( $fp, $success_string, strlen( $success_string ) ) ;
			fclose( $fp ) ;

			// create and put version file for v1.6
			$version_string = "0LEFT_ARROW0? \$PHPLIVE_VERSION = \"$PATCH_VERSION\" ; ?0RIGHT_ARROW0" ;
			$version_string = preg_replace( "/0LEFT_ARROW0/", "<", $version_string ) ;
			$version_string = preg_replace( "/0RIGHT_ARROW0/", ">", $version_string ) ;
			$fp = fopen ("../../web/VERSION_KEEP.php", "wb+") ;
			fwrite( $fp, $version_string, strlen( $version_string ) ) ;
			fclose( $fp ) ;

			// create the new conf file WITHOUT the $VERSION.. since we now
			// keep $VERSION in another file.  this external $VERSION will make it
			// much easier to detect upgrades and not write into the conf file all the time.
			// we HAVE to do it this time.
			$conf_string = "0LEFT_ARROW0?
				\$DATABASETYPE = \"$DATABASETYPE\" ;
				\$DATABASE = \"$DATABASE\" ;
				\$SQLHOST = \"$SQLHOST\" ;
				\$SQLLOGIN = \"$SQLLOGIN\" ;
				\$SQLPASS = \"$SQLPASS\" ;
				\$SITE_NAME = \"$SITE_NAME\" ;
				\$DOCUMENT_ROOT = \"$DOCUMENT_ROOT\" ;
				\$BASE_URL = \"$BASE_URL\" ;
				\$LOGO = \"$LOGO\" ;
				\$SUPPORT_LOGO = \"$SUPPORT_LOGO\" ;
				\$SUPPORT_LOGO_ONLINE = \"phplive_support_online.gif\" ;
				\$SUPPORT_LOGO_OFFLINE = \"phplive_support_offline.gif\" ;
				\$VISITOR_FOOTPRINT = 1 ;
				\$ALERT_SOUND = \"$ALERT_SOUND\" ;
				\$TEXT_COLOR = \"$TEXT_COLOR\" ;
				\$LINK_COLOR = \"$LINK_COLOR\" ;
				\$ALINK_COLOR = \"$ALINK_COLOR\" ;
				\$VLINK_COLOR = \"$VLINK_COLOR\" ;
				\$CLIENT_COLOR = \"$CLIENT_COLOR\" ;
				\$ADMIN_COLOR = \"$ADMIN_COLOR\" ;
				\$CHAT_BACKGROUND = \"$CHAT_BACKGROUND\" ;
				\$CHAT_REQUEST_BACKGROUND = \"$CHAT_REQUEST_BACKGROUND\" ;
				\$CHAT_BOX_BACKGROUND = \"$CHAT_BOX_BACKGROUND\" ;
				\$CHAT_BOX_TEXT = \"$CHAT_BOX_TEXT\" ;
				\$LANG_PACK = \"$LANG_PACK\" ;?0RIGHT_ARROW0" ;

			// create and put configuration data
			$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
			$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
			$fp = fopen ("../../web/conf-init.php", "wb+") ;
			fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
			fclose( $fp ) ;

			$success = 1 ;
		}
	}
?>
<html>
<head>
<title> v1.5.3 to v<?php echo $PATCH_VERSION ?> Patch </title>
<link rel="Stylesheet" href="../../css/base.css">
</head>

<body bgColor="#FFFFEA" text="#000000" link="#FF661C" vlink="#FF661C" alink="#FF6600">
<table cellspacing=0 cellpadding=0 border=0 width="98%">
<tr>
	<td valign="top"><span class="basetxt">
		<img src="../../images/logo.gif">
		<br>

		<?php if ( $success ): ?>
		<br>
		<big><b>Congratulations!  Your system DB has been patched from v1.5.3 to v<?php echo $PATCH_VERSION ?>!</b></big>
		<p>
		If your system does not function properly, please email <a href="mailto:tech@fuck.com?subject=Patch Error">tech@fuck.com</a>.
		<p>
		<li> <a href="../">Return to setup menu</a>







		<?php else: ?>
		<font color="#FF0000"><?php echo $error ?></font><br>
		<big><b>This v<?php echo $PATCH_VERSION ?> DB patch will do the following:</b></big>
		<ol>
			<li> Add a new table "chatdepartments"<br>
				<p>

			<li> Add a new table "chatuserdeptlist"<br>
				<p>

			<li> Migrate your departments into the new <b>"chatdepartments"</b> table.<p>
			<li> Migrate your users' department into the new <b>"chatuserdeptlist"</b> table.
			<p>

			<li> Modify your current departments to use the new <b>deptID</b> created from the above inserts.<br>
				<p>
			
			<li> Alter the <b>"chatrequests"</b> table to be able to track department requests (since user can be in multiple departments).  Also alter to handle the footprint URL the visitor came from.<br>
				<p>

			<li> Add a new table "chatfootprints" to keep track visited URLs.

			<p>
			After you run this patch, everything will run as normal and all user passwords will remain the same.  Click the below button to run the patch.<p>

			If you do not run the patch, the system will NOT function normally.

			<p>
			<form method="GET" action="1.6_patch.php" method="POST">
			<input type="hidden" name="action" value="execute">
			<input type="submit" value="Execute Patch">
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