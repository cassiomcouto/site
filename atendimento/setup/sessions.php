<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	$section = 4;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="options.php" class="nav">:: Home</a>';

	// Include header
	include_once("./header.php");
?> 
<table width="100%" border="0" cellspacing="0" cellpadding="15">
  <tr> 
	<td width="100%" valign="top"> <p><span class="title">Sessions</span><br></p>
	  <p>
		Complete list of current active chat processes. You may manually kill and end the process.<br>
		<big><li> <strong><a href="processes.php?action=chat">Current Chats</a></strong></big></p>
	 <p>
		View/Search past saved chat transcripts by department or operator.<br>
		<big><li> <strong><a href="transcripts.php">Chat Transcripts</a></strong></big></p>
	  <p>Complete list of all operators and their active Operator Console status.<br>
		<big><li> <strong><a href="processes.php?action=consol">Operator Console Status</a></strong></big></p></td>
	<td height="350" style="background-image: url(../images/g_sessions_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
  </tr>
</table>
<?php
// Include Footer
include("footer.php");
?>