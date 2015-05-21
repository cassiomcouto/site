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
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	$section = 1;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="adddept.php" class="nav">:: Previous</a>';
?>
<?php

	// initialize
	$action = "" ;
	$deptid = "" ;
	$success = 0 ;
	$error_mesg = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "20" ;
	else
		$text_width = "10" ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_POST['deptid'] ) ) { $deptid = $_POST['deptid'] ; }
	if ( isset( $_GET['success'] ) ) { $success = $_GET['success'] ; }

	if ( !$deptid )
	{
		HEADER( "location: adddept.php" ) ;
		exit ;
	}
?>
<?php
	// functions
?>
<?php
	// conditions

	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $session_setup['aspID'] ) ;
	if ( $action == "upload_status_image" )
	{
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

		$filename = "DEPT_$now.$extension" ;
		if ( eregi( "gif", $filetype ) ||  eregi( "jpeg", $filetype ) )
		{
			if( move_uploaded_file( $_FILES['pic']['tmp_name'], "../web/$session_setup[login]/$filename" ) )
			{
				chmod( "../web/$session_setup[login]/$filename", 0777 ) ;
				if ( $_POST['logo_name'] == "status_image_online" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$deptinfo[status_image_online]" ) && $deptinfo['status_image_online'] )
						unlink( "../web/$session_setup[login]/$deptinfo[status_image_online]" ) ;
					AdminUsers_update_DeptValue( $dbh, $session_setup['aspID'], $deptid, "status_image_online", $filename ) ;
				}
				else if ( $_POST['logo_name'] == "status_image_offline" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$deptinfo[status_image_offline]" ) && $deptinfo['status_image_offline'] )
						unlink( "../web/$session_setup[login]/$deptinfo[status_image_offline]" ) ;
					AdminUsers_update_DeptValue( $dbh, $session_setup['aspID'], $deptid, "status_image_offline", $filename ) ;
				}
				else if ( $_POST['logo_name'] == "status_image_away" )
				{
					if ( file_exists ( "../web/$session_setup[login]/$deptinfo[status_image_away]" ) && $deptinfo['status_image_away'] )
						unlink( "../web/$session_setup[login]/$deptinfo[status_image_away]" ) ;
					AdminUsers_update_DeptValue( $dbh, $session_setup['aspID'], $deptid, "status_image_away", $filename ) ;
				}
			}

			HEADER( "location: dept_icons.php?deptid=$deptid&success=1" ) ;
			exit ;
		}
		else if ( $pic_name != "" )
			$error_mesg = "Please upload ONLY GIF or JPEG formats.<br>" ;
	}
?>
<?php include_once("./header.php"); ?>
<script language="JavaScript">
<!--
	function do_upload(the_form)
	{
		if ( the_form.pic.value == "" )
			alert( "Input cannot be blank." ) ;
		else
			the_form.submit() ;
	}
//-->
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td valign="top"> <p><span class="title">Manager: <?php echo stripslashes( $deptinfo['name'] ) ?> Department Status 
	  Icons</span><br>
	  Each department can have their own online/offline status image. 
	  The department specific status image will only display if you generate 
	  department specific HTML code. </p>
	<table width="100%" border="0" cellspacing="8" cellpadding="0">
	  <form method="POST" action="dept_icons.php" enctype="multipart/form-data" name="support_logo_online">
		<input type="hidden" name="action" value="upload_status_image">
		<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
		<input type="hidden" name="logo_name" value="status_image_online">
		<tr> 
		  <td>Upload Department <u>Online</u> Support Image</td>
		  <td rowspan="3" align="right"><img src="
			<?php
				if ( file_exists( "../web/$session_setup[login]/$deptinfo[status_image_online]" ) && $deptinfo['status_image_online'] )
					echo "../web/$session_setup[login]/$deptinfo[status_image_online]";
				else if ( isset( $SUPPORT_LOGO_ONLINE ) && $SUPPORT_LOGO_ONLINE && file_exists( "../web/$session_setup[login]/$SUPPORT_LOGO_ONLINE" ) )
					echo "../web/$session_setup[login]/$SUPPORT_LOGO_ONLINE" ;
				else
					echo "../images/phplive_support_online.gif" ;
			?>"></td>
		</tr>
		<tr> 
		  <td align="right"> <input type="file" name="pic" size="20"></td>
		</tr>
		<tr> 
		  <td align="right"><input type="button" class="mainButton" value="Upload Image" onclick="do_upload(document.support_logo_online);"></td>
		</tr>
	  </form>
	  <tr>
	  <td colspan="2" class="hdash">&nbsp;</td>
	  </tr>
	  <form method="POST" action="dept_icons.php" enctype="multipart/form-data" name="support_logo_offline">
		<input type="hidden" name="action" value="upload_status_image">
		<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
		<input type="hidden" name="logo_name" value="status_image_offline">
		<tr> 
		  <td>Upload Department <u>Offline</u> Support Image</td>
		  <td rowspan="3" align="right"><img src="
			<?php
				if ( file_exists( "../web/$session_setup[login]/$deptinfo[status_image_offline]" ) && $deptinfo['status_image_offline'] )
					echo "../web/$session_setup[login]/$deptinfo[status_image_offline]";
				else if ( isset( $SUPPORT_LOGO_OFFLINE ) && $SUPPORT_LOGO_OFFLINE && file_exists( "../web/$session_setup[login]/$SUPPORT_LOGO_OFFLINE" ) )
					echo "../web/$session_setup[login]/$SUPPORT_LOGO_OFFLINE" ;
				else
					echo "../images/phplive_support_offline.gif" ;
			?>"></td>
		</tr>
		<tr> 
		  <td align="right"> <input type="file" name="pic" size="20"></td>
		</tr>
		<tr> 
		  <td align="right"><input type="button" class="mainButton" value="Upload Image" onclick="do_upload(document.support_logo_offline);"></td>
		</tr> 
	  </form>
	  <!-- <tr>
	   <td colspan="2" class="hdash">&nbsp;</td>
	  </tr>
	  <form method="POST" action="dept_icons.php" enctype="multipart/form-data" name="support_logo_away">
		<input type="hidden" name="action" value="upload_status_image">
		<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
		<input type="hidden" name="logo_name" value="status_image_away">
		<tr> 
		  <td>Upload Department <u>Offline</u> Support Image<br>
			If operators' consoles are open but status is offline, below status icon will display.  ONLY when the operators' status are offline AND consoles are closed will the image show the above offline icon.
			</td>
		  <td rowspan="3" align="right"><img src="
			<?php
				if ( file_exists( "../web/$session_setup[login]/$deptinfo[status_image_away]" ) && $deptinfo['status_image_away'] )
					echo "../web/$session_setup[login]/$deptinfo[status_image_away]";
				else if ( isset( $SUPPORT_LOGO_AWAY ) && $SUPPORT_LOGO_AWAY && file_exists( "../web/$session_setup[login]/$SUPPORT_LOGO_AWAY" ) )
					echo "../web/$session_setup[login]/$SUPPORT_LOGO_AWAY" ;
				else
					echo "../images/phplive_support_away.gif" ;
			?>"></td>
		</tr>
		<tr> 
		  <td align="right"> <input type="file" name="pic" size="20"></td>
		</tr>
		<tr> 
		  <td align="right"><input type="button" class="mainButton" value="Upload Image" onclick="do_upload(document.support_logo_away);"></td>
		</tr>
	  </form> -->
	</table></td>
  <td style="background-image: url(../images/g_manage_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>
<?php include_once( "./footer.php" ) ; ?>