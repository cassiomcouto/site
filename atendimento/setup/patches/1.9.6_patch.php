<?
	if ( !file_exists( "../../web/conf-init.php" ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	// patch check for correct version patch.  this patch is for version 1.6
	$PATCH_VERSION = "1.9.6" ;
	$previous_version = "1.9.5" ;
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
		else if ( !preg_match( "/($previous_version)/", $PHPLIVE_VERSION ) )
			$error = "ERROR: YOU ARE NOT RUNNING v$previous_version!  PATCH WILL NOT WORK.  PLEASE FRESH INSTALL v$PHPLIVE_VERSION OR <a href=\"$previous_version"."_patch.php\">UPGRADE TO v$previous_version</a> BEFORE RUNNING THIS PATCH." ;
		else
		{
			// create patch log dir to keep track of if system has been patched
			if ( is_dir( "../../web/patches" ) != true )
				mkdir( "../../web/patches", 0755 ) ;

			$query = "CREATE TABLE chatrefer (
					aspID int(10) unsigned NOT NULL default '0',
					created int(10) unsigned NOT NULL default '0',
					ip char(20) NOT NULL default '',
					refer_url char(255) NOT NULL default '',
					PRIMARY KEY  (aspID,ip),
					KEY created (created)
					)" ;
			database_mysql_query( $dbh, $query ) ;

			$query = "ALTER TABLE chat_asp ADD `trans_message` CHAR(255) NOT NULL, ADD `trans_email` TEXT NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chattranscripts CHANGE deptID deptID INT(10) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "ALTER TABLE chatrequestlogs CHANGE deptID deptID INT(10) DEFAULT '0' NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;

			$trans_email = "Hello %%username%%,

Below is the complete transcript of your chat session:
===
%%transcript%%
===

Thank you

" ;
			$users = AdminASP_get_AllUsers( $dbh, 0, 0 ) ;
			for ( $c = 0; $c < count( $users ); ++$c )
			{
				$user = $users[$c] ;
				if ( $user['login'] )
				{
					$query = "UPDATE chat_asp SET trans_message = 'If you would like to receive a copy of this chat session transcript, please input your email address below and Submit.', trans_email = '$trans_email' WHERE aspID = $user[aspID]" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}

			// create the actual patch file
			$date = date( "D m/d/y h:i a", time() ) ;
			$success_string = "DO NOT DELETE OR SYSTEM MAY PATCH AGAIN!  THAT'S NOT GOOD!\n[$date] PATCH SUCCESSFUL from v$previous_version to v$PATCH_VERSION\n" ;
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
<title> v<?php echo $previous_version ?> to v<?php echo $PATCH_VERSION ?> Patch </title>
<script language="JavaScript">
<!--
	function do_patch()
	{
		if ( confirm( "Ready to upgrade to v<?php echo $PATCH_VERSION ?>?" ) )
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
		<big><b>Congratulations!  Your system DB has been patched from v<?php echo $previous_version ?> to v<?php echo $PATCH_VERSION ?>!</b></big>
		<li> <a href="<?php echo $BASE_URL ?>/setup/">Return to setup area</a>







		<?php else: ?>
		<font color="#FF0000"><?php echo $error ?></font><br>
		<big><b>This v<?php echo $PATCH_VERSION ?> DB patch will do the following:</b></big>
		<ol>
			<form method="GET" action="<?php echo $PATCH_VERSION ?>_patch.php" method="POST" name="form">
			<input type="hidden" name="action" value="execute">
			<li> Alter and create the neccessary table to reflect the new v<?php echo $PATCH_VERSION ?> changes.
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
<br>
</body>
</html>