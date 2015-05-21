<?
	if ( !file_exists( "../../web/conf-init.php" ) )
	{
		HEADER( "location: ../index.php" ) ;
		exit ;
	}
	include("../../web/conf-init.php") ;
	include("../../system.php") ;
	include("../../lang_packs/$LANG_PACK.php") ;
	include("../../API/sql.php" ) ;
	include("../../API/Users/get.php") ;
	include("../../API/Users/put.php") ;
	include("../../API/Users/remove.php") ;
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
		if ( !$CRYPTKEY )
			$error = "ERROR: YOU MUST FIRST OVERRIDE YOUR OLD COPY OF PHP LIVE! WITH THE NEW PATCHED FILES" ;
		else if ( file_exists( "../../web/patches/1.5.3_patch.log" ) )
			$error = "Your system is ALREADY PATCHED for v1.5.3!" ;
		else
		{
			// create patch log dir to keep track of if system has been patched
			if ( is_dir( "../../web/patches" ) != true )
				mkdir( "../../web/patches", 0755 ) ;

			$query = "SELECT *, DECODE( password, '$CRYPTKEY' ) AS password FROM chat_admin" ;
			database_mysql_query( $dbh, $query ) ;
			
			if ( $dbh[ 'ok' ] )
			{
				while ( $data = database_mysql_fetchrow( $dbh ) )
					$users[] = $data ;
			}

			// alter the table for the 128-bit MD5 hash key password
			$query = "ALTER TABLE chat_admin CHANGE `password` `password` VARCHAR(32) NOT NULL" ;
			database_mysql_query( $dbh, $query ) ;
			
			for ( $c = 0; $c < count( $users ); ++$c )
			{
				$user = $users[$c] ;
				$password = md5( $user[password] ) ;

				$query = "UPDATE chat_admin SET password = '$password' WHERE userID = $user[userID]" ;
				database_mysql_query( $dbh, $query ) ;
			}

			// create the actual patch file
			$date = date( "D m/d/y h:i a", time() ) ;
			$success_string = "DO NOT DELETE OR SYSTEM MAY PATCH AGAIN!  THAT'S NOT GOOD!\n[$date] PATCH SUCCESSFUL from v1.5.2 to v1.5.3 - [ALTER TABLE chat_admin CHANGE `password` `password` VARCHAR(32) NOT NULL] and update user's password to MD5 hash key\n" ;
			$fp = fopen ("../../web/patches/1.5.3_patch.log", "wb+") ;
			fwrite( $fp, $success_string, strlen( $success_string ) ) ;
			fclose( $fp ) ;

			$success = 1 ;
		}
	}
?>
<html>
<head>
<title> v1.5.2 to v1.5.3 Patch </title>
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
		<big><b>Congratulations!  Your system DB has been patched from v1.5.2 to v1.5.3!</b></big>
		<p>
		<li> <a href="../">Return to setup menu</a>







		<?php else: ?>
		<font color="#FF0000"><?php echo $error ?></font><br>
		<big><b>This v1.5.3 DB patch will do the following:</b></big>
		<ol>
			<li> Modify your database 'password' column.<p>
				<b>Database query that will be executed:</b><br>
			<font color="#660000">ALTER TABLE chat_admin CHANGE `password` `password` VARCHAR(32) NOT NULL</font>
			<p>

			<li> Update your current users' passwords to use the most secure 128-bit MD5 hash key.  The MD5 hash cannot be reversable and thus will keep all user passwords completely secure from any unwanted eyes.<p>
			
			<p>
			After you run this patch, everything will run as normal and all user passwords will remain the same.  Click the below button to run the patch.<p>

			If you do not run the patch, the system will NOT function normally.

			<p>
			<form method="GET" action="1.5.3_patch.php" method="POST">
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