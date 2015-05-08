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
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;

	$section = 2;			// Section number - see header.php for list of section numbers

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
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Interface</span><br></p>
	
			Customize your chat window theme/skin.<br>
			<big><li> <strong><a href="customize.php?action=themes">Chat Window Theme</a></strong></big></p>
			<p>
			Set your default live support status icons.<br>
			<big><li> <strong><a href="customize.php?action=icons">Default Chat Icons</a></strong></big></p>

			<?php if ( $INITIATE && !file_exists( "$DOCUMENT_ROOT/admin/auction/index.php" ) && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
			<p>
			Set your default Initiate Chat scrolling image.<br>
			<big><li> <strong><a href="customize.php?action=initiate">Initiate Chat Image</a></strong></big></p>
			<?php endif ; ?>
	</td>
	<td style="background-image: url(../images/g_interface_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>
<?php
// Include Footer
include_once("footer.php");


?>
