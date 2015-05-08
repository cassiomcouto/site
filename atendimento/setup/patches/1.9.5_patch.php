<?
	if ( !file_exists( "../../web/conf-init.php" ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	// patch check for correct version patch.  this patch is for version 1.6
	$PATCH_VERSION = "1.9.5" ;
	$success = 0 ;
	$action = $error = "" ;

	include_once("../../web/conf-init.php") ;
	include_once("../../web/VERSION_KEEP.php") ;
	include_once("../../system.php") ;
	include_once("../../lang_packs/$LANG_PACK.php") ;
	include_once("../../API/sql.php" ) ;
	include_once("../../API/ASP/get.php") ;
?>
<?

	// initialize
	if ( preg_match( "/MSIE/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "20" ;
	else
		$text_width = "10" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
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
		else if ( !preg_match( "/(1.9)/", $PHPLIVE_VERSION ) )
			$error = "ERROR: YOU ARE NOT RUNNING v1.9!  PATCH WILL NOT WORK.  PLEASE FRESH INSTALL v1.9.5 OR <a href=\"1.9_patch.php\">UPGRADE TO v1.9</a> BEFORE RUNNING THIS PATCH." ;
		else
		{
			// create patch log dir to keep track of if system has been patched
			if ( is_dir( "../../web/patches" ) != true )
				mkdir( "../../web/patches", 0755 ) ;

			$query = "CREATE TABLE chatfootprintstats (
					aspID int(10) unsigned NOT NULL default '0',
					statdate int(10) unsigned NOT NULL default '0',
					created int(10) unsigned NOT NULL default '0',
					pageviews int(10) unsigned NOT NULL default '0',
					uniquevisits int(10) unsigned NOT NULL default '0',
					PRIMARY KEY  (aspID,statdate)
					)" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "CREATE TABLE chatfootprinturlstats (
					statID int(10) unsigned NOT NULL auto_increment,
					aspID int(10) unsigned NOT NULL default '0',
					statdate int(10) unsigned NOT NULL default '0',
					created int(10) unsigned NOT NULL default '0',
					url char(255) NOT NULL default '',
					clicks int(10) unsigned NOT NULL default '0',
					PRIMARY KEY  (statID),
					KEY aspID (aspID)
					)" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "ALTER TABLE `chattranscripts` DROP INDEX `deptID_2`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequestlogs` DROP INDEX `status_2`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatfootprints` DROP INDEX `url`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatfootprints` DROP INDEX `url_2`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatfootprintsunique` DROP INDEX `url`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequestlogs` DROP INDEX `url`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequestlogs` DROP INDEX `status_2`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequestlogs` DROP INDEX `url`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatcanned` ADD `deptID` INT(10) UNSIGNED NOT NULL AFTER `userID`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatcanned` ADD INDEX (`deptID`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatdepartments` ADD `message` MEDIUMTEXT NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatdepartments` ADD `status_image_offline` CHAR(20) NOT NULL AFTER `initiate_chat`, ADD `status_image_online` CHAR(20) NOT NULL AFTER `status_image_offline`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequestlogs` DROP PRIMARY KEY" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequestlogs` ADD INDEX(`chat_session`)" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatdepartments` ADD `greeting` TEXT NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chat_admin` DROP `greeting`" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE `chatrequests` DROP INDEX `url`" ;
			database_mysql_query( $dbh, $query ) ;
			
			$greeting = addslashes( $LANG['CHAT_GREETING'] ) ;
			$query = "UPDATE chatdepartments SET greeting = '$greeting'" ;
			database_mysql_query( $dbh, $query ) ;

			$users = AdminASP_get_AllUsers( $dbh, 0, 0 ) ;
			for ( $c = 0; $c < count( $users ); ++$c )
			{
				$user = $users[$c] ;

				if ( $user['login'] )
				{
					$index_string = "0LEFT_ARROW0? \$path = explode( \"/\", \$_SERVER['PHP_SELF'] ) ; \$total = count( \$path ) ; \$login = \$path[\$total-2] ; \$winapp = isset( \$_GET['winapp'] ) ? \$_GET['winapp'] : \"\" ; HEADER( \"location: ../../index.php?l=\$login&winapp=\$winapp\" ) ; exit ; ?0RIGHT_ARROW0" ;
					$index_string = preg_replace( "/0LEFT_ARROW0/", "<", $index_string ) ;
					$index_string = preg_replace( "/0RIGHT_ARROW0/", ">", $index_string ) ;
					$fp = fopen ("../../web/$user[login]/index.php", "wb+") ;
					fwrite( $fp, $index_string, strlen( $index_string ) ) ;
					fclose( $fp ) ;

					if ( file_exists( "../../web/$user[login]/ip_notrack.txt" ) )
					{
						$ip_notrack_string_array = file( "../../web/$user[login]/ip_notrack.txt" ) ;
						$ip_notrack_string = $ip_notrack_string_array[0] ;
						include_once( "../../web/$user[login]/$user[login]-conf-init.php" ) ;

						$conf_string = "0LEFT_ARROW0?
							\$LOGO = '$LOGO' ;
							\$COMPANY_NAME = '$COMPANY_NAME' ;
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
							\$POLL_TIME = '$POLL_TIME' ;
							\$INITIATE = '$INITIATE' ;
							\$IPNOTRACK = '$ip_notrack_string' ;
							\$LANG_PACK = '$LANG_PACK' ;?0RIGHT_ARROW0" ;

						$conf_string = preg_replace( "/0LEFT_ARROW0/", "<", $conf_string ) ;
						$conf_string = preg_replace( "/0RIGHT_ARROW0/", ">", $conf_string ) ;
						$fp = fopen ("../../web/$user[login]/$user[login]-conf-init.php", "wb+") ;
						fwrite( $fp, $conf_string, strlen( $conf_string ) ) ;
						fclose( $fp ) ;
						unlink( "../../web/$user[login]/ip_notrack.txt" ) ;
					}
				}
			}

			// create the actual patch file
			$date = date( "D m/d/y h:i a", time() ) ;
			$success_string = "DO NOT DELETE OR SYSTEM MAY PATCH AGAIN!  THAT'S NOT GOOD!\n[$date] PATCH SUCCESSFUL from v1.9 to v$PATCH_VERSION\n" ;
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

			HEADER( "location: index.php?$PATCH_VERSION" ) ;
			exit ;
		}
	}
?>
<html>
<head>
<title> v1.9 to v<?php echo $PATCH_VERSION ?> Patch </title>
<script language="JavaScript">
<!--
	function do_patch()
	{
		if ( confirm( "Ready to upgrade to v1.9.5?" ) )
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
		<big><b>Congratulations!  Your system DB has been patched from v1.9 to v<?php echo $PATCH_VERSION ?>!</b></big>
		<p>
		<li> <a href="<?php echo $BASE_URL ?>/setup/">Return to setup area</a>







		<?php else: ?>
		<font color="#FF0000"><?php echo $error ?></font><br>
		<big><b>This v<?php echo $PATCH_VERSION ?> DB patch will do the following:</b></big>
		<ol>
			<form method="GET" action="1.9.5_patch.php" method="POST" name="form">
			<input type="hidden" name="action" value="execute">
			<li> Alter and create the neccessary table to reflect the new v1.9.5 changes.
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