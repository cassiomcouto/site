<?php
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
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util_Image.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	$section = 8;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	/*************************************/
	//
	// upload picture max width and height
	//
	$max_width = 75 ;
	$max_height = 75 ;
	/*************************************/

	// initialize
	$action = $error_mesg = $userid = "" ;
	$success = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['userid'] ) ) { $userid = $_POST['userid'] ; }
	if ( isset( $_GET['userid'] ) ) { $userid = $_GET['userid'] ; }

	// conditions
	if ( $action == "upload_pic" )
	{
		$userinfo = AdminUsers_get_UserInfo( $dbh, $userid, $session_setup['aspID'] ) ;

		$pic_name = $_FILES['pic']['name'] ;
		$now = time() ;
		$filename = eregi_replace( " ", "_", $pic_name ) ;
		$filename = eregi_replace( "%20", "_", $filename ) ;

		$filesize = $_FILES['pic']['size'] ;
		$filetype = $_FILES['pic']['type'] ;

		if ( eregi( "gif", $filetype ) )
			$extension = "GIF" ;
		elseif ( eregi( "jpeg", $filetype ) )
			$extension = "JPEG" ;
		else
			$extension = "" ;

		$filename = "PROFILE_$now.$extension" ;
		if ( eregi( "gif", $filetype ) ||  eregi( "jpeg", $filetype ) && $userinfo['userID'] && $extension )
		{
			if( move_uploaded_file( $_FILES['pic']['tmp_name'], "../web/$session_setup[login]/$filename" ) )
			{
				chmod( "../web/$session_setup[login]/$filename", 0777 ) ;
				if ( file_exists ( "../web/$session_setup[login]/$userinfo[pic]" ) && $userinfo['pic'] )
					unlink( "../web/$session_setup[login]/$userinfo[pic]" ) ;
			}

			$image_size = getimagesize( "../web/$session_setup[login]/$filename" ) ;
			if( $image_size[0] > $max_width )
			{
				$action = "pics" ;
				$error_mesg = "Uploaded image size: $image_size[3].<br>Image WIDTH must be less then $max_width px.  Picture did not upload." ;
				unlink( "../web/$session_setup[login]/$filename" ) ;
			}
			else if( $image_size[1] > $max_height )
			{
				$action = "pics" ;
				$error_mesg = "Uploaded image size:$image_size[3].<br>Image HEIGHT must be less then $max_height px.  Picture did not upload." ;
				unlink( "../web/$session_setup[login]/$filename" ) ;
			}
			else
			{
				AdminUsers_update_UserValue( $dbh, $userid, "pic", $filename ) ;
				HEADER( "location: profiles.php?action=pics&success=1" ) ;
				exit ;
			}
		}
		else if ( $pic_name != "" )
		{
			$action = "pics" ;
			$error_mesg = "Please upload ONLY GIF or JPEG formats.<br>" ;
		}
	}
	else if ( $action == "delete" )
	{
		$userinfo = AdminUsers_get_UserInfo( $dbh, $userid, $session_setup['aspID'] ) ;
		AdminUsers_update_UserValue( $dbh, $userinfo['userID'], "pic", "" ) ;
		if ( file_exists ( "../web/$session_setup[login]/$userinfo[pic]" ) && $userinfo['pic'] )
			unlink( "../web/$session_setup[login]/$userinfo[pic]" ) ;
		HEADER( "location: profiles.php?action=pics&success=1" ) ;
		exit ;
	}

	$admins = AdminUsers_get_AllUsers( $dbh, 0, 0, $session_setup['aspID'] ) ;
?>
<?php include_once("./header.php") ; ?>
<script language="JavaScript">
<!--
	function do_upload(the_form)
	{
		if ( the_form.pic.value == "" )
			alert( "Input cannot be blank." ) ;
		else
			the_form.submit() ;
	}

	function do_alert()
	{
		<?php if ( $success ) { print "		alert( 'Success!' ) ;\n" ; } ?>
	}

	function do_delete( userid )
	{
		if ( confirm( "Really delete picture?" ) )
			location.href = "profiles.php?action=delete&userid="+userid ;
	}
//-->
</script>





<?php 
	if ( $action == "pics" ):
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Operator Profiles: Upload Operator Pics</span><br>
	  You can upload a picture of each operator that will be displayed along with the initial welcome message of the support request. [ <a href="adduser.php">Manage Operators</a> ]</p>
	
	<span class="smallTitle"><font color="#FF0000"><?php echo $error_mesg ?></font></span><br>
	<table cellspacing=1 cellpadding=3 border=0 width="100%">
		<?php
			for ( $c = 0; $c < count( $admins ); ++$c )
			{
				$admin = $admins[$c] ;
				$date = date( "D m/d/y h:i a", $admin['created'] ) ;

				$class = "altcolor2" ;

				$pic = "$BASE_URL/images/no_pic.gif" ;
				$delete_string = "" ;
				if ( $admin['pic'] )
				{
					$pic = "$BASE_URL/web/$session_setup[login]/$admin[pic]" ;
					$delete_string = "<br>[ <a href=\"JavaScript:do_delete( $admin[userID] )\">delete</a> ]" ;
				}

				print "
					<form method=\"POST\" action=\"profiles.php\" enctype=\"multipart/form-data\">
					<input type=\"hidden\" name=\"action\" value=\"upload_pic\">
					<input type=\"hidden\" name=\"userid\" value=\"$admin[userID]\">
					<tr class=\"$class\">
						<td valign=\"top\" align=\"center\"><img src=\"$pic\">$delete_string</td>
						<td valign=\"top\" width=\"100%\">
							Name: $admin[name]<br>
							Login: $admin[login]<br>
							Email: <a href=\"mailto:$admin[email]\">$admin[email]</a><br>
							<font color=\"#FF8040\">(Max width: $max_width px, Max height: $max_height px)</font><br>
							<input type=\"file\" name=\"pic\" size=\"15\"> &nbsp;
							<input type=\"button\" class=\"mainButton\" value=\"Upload Picture\" OnClick=\"do_upload(this.form);\">
						</td>
					</tr>
					</form>
				" ;
			}

			if ( count( $admins ) <= 0 )
				print "<span class=\"smallTitle\"><a href=\"adduser.php\">You have no Operators.  Click Here to Add/Edit Operators.</a></span>" ;
		?>
		</table>
	</td>





<?php else: ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Operator Prefs and Reports</span><br></p>
	  <p>
		View report of the support request breakdown by department(s) and operator(s).<br>
		<big><li> <strong><a href="statistics.php">Support Requests</a></strong></big></p>
	<p>
		View visitor ratings of support operators by daily, monthly and department breakdown.<br>
		<big><li> <strong><a href="opratings.php">Operator Ratings</a></strong></big></p>
	  <p>
		Upload operator's profile picture.<br>
		<big><li> <strong><a href="profiles.php?action=pics">Operator Pics</a></strong></big></p>
	  </td>


<?php endif ;?>
  <td style="background-image: url(../images/g_profile_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
 </table>
<?php include_once( "./footer.php" ) ; ?>