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
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/ASP/update.php") ;
	$section = 5;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';
?>
<?php

	// initialize
	$action = $error = "" ;
	$success = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "60" ;
		$textbox_width = "70" ;
	}
	else
	{
		$text_width = "35" ;
		$textbox_width = "35" ;
	}

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
?>
<?php
	// functions
?>
<?php
	// conditions

	if ( $action == "update" )
	{
		AdminASP_update_TableValue( $dbh, $session_setup['aspID'], "trans_message", $_POST['trans_message'] ) ;
		AdminASP_update_TableValue( $dbh, $session_setup['aspID'], "trans_email", $_POST['trans_email'] ) ;
		$_SESSION['session_setup']['trans_message'] = $_POST['trans_message'] ;
		$_SESSION['session_setup']['trans_email'] = $_POST['trans_email'] ;
		$session_setup['trans_message'] = $_POST['trans_message'] ;
		$session_setup['trans_email'] = $_POST['trans_email'] ;
		$success = 1 ;
	}
?>
<?php include_once("./header.php"); ?>
<script language="JavaScript">
<!--
	function do_submit()
	{
		if ( ( document.form.trans_message.value == "" ) || ( document.form.trans_email.value == "" ) )
			alert( "All fields MUST be provided." ) ;
		else if ( document.form.trans_email.value.indexOf("%%transcript%%") == -1 )
			alert( "Email body MUST contain the %%transcript%% variable." ) ;
		else
		{
			document.form.submit() ;
		}
	}

	function do_alert()
	{
		if( <?php echo $success ?> )
			alert( 'Success!' ) ;
	}
//-->
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Preferences: Email 
	  Transcript</span><br>
	  You can customize the message of the &quot;Email Transcripts&quot; 
	  page and also the email content of the transcript letter.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?></p>
	  <form method="POST" action="email_transcript.php" name="form">
	<input type="hidden" name="action" value="update">
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<td valign="top" align="right" nowrap><strong>Webpage Text:</strong></td>
		<td valign="top"> <input type="text" name="trans_message" size="<?php echo $text_width ?>" maxlength="255" style="width:300px" value="<?php echo stripslashes( $session_setup['trans_message'] ) ?>"></td>
	  </tr>
	  <tr>
		<td colspan=2>&nbsp;</td>
	  </tr>
	  <tr> 
		<td>&nbsp;</td>
		<td> <span class="hilight">%%username%%</span> - chat name used 
		  by visitor (optional)<br>
		  <span class="hilight">%%transcript%%</span> - the complete chat 
		  transcript. (MUST be included.)</td>
	  
	  <tr> 
		<td valign="top" align="right" nowrap><strong>Email Body:</strong></td>
		<td valign="top"> <textarea cols="<?php echo $textbox_width ?>" name="trans_email" rows="12" wrap="virtual" style="width:300px"><?php echo stripslashes( $session_setup['trans_email'] ) ?></textarea></td>
	  </tr>
	  <tr> 
		<td>&nbsp;</td>
		<td> <input type="button" class="mainButton" value="Submit" OnClick="do_submit()"></td>
	  </tr>
	</table>
	</form>
	</td>
  <td style="background-image: url(../images/g_prefs_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>
<?php include_once( "./footer.php" ) ; ?>