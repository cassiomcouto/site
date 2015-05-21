<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: ../../setup/index.php" ) ; exit ; }
	include_once( "../../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "../..", $session_setup['login'] ) )
	{
		HEADER( "location: ../../setup/options.php" ) ;
		exit ;
	}
	include_once("../../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	$section = 9;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)
	$nav_line = "<a href=\"$BASE_URL/setup/options.php\" class=\"nav\">:: Home</a>" ;
	$css_path = "../../" ;

	// initialize
	$action = $error_mesg = $page_string = "" ;
	$success = $deptid = $surveyid = $page = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "50" ;
	else
		$text_width = "25" ;

	if ( !isset( $_SESSION['session_survey'] ) )
	{
		session_register( "session_survey" ) ;
		$session_survey = ARRAY() ;
		$_SESSION['session_survey'] = ARRAY() ;
	}

?>
<?php include_once("$DOCUMENT_ROOT/setup/header.php") ; ?>
<script language="JavaScript">
<!--
	
//-->
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td width="100%" valign="top"> 
	<p><span class="title">Knowledge Base</span><br></p>
	  <p>
		Set your Knowledge Base preferences.<br>
		<big><li> <strong><a href="./knowledge_config.php">Preferences</a></strong></big></p>
		<p>
		Input categories, questions, answers and build your knowledge base system.  This self-help knowledge base can be used by visitors during offline hours or prior to requesting support.<br>
		<big><li> <strong><a href="./knowledge_config.php?action=config">Setup and Build</a></strong></big></p>
		<p>
		<!-- View common KB search terms performed by your visitors and optimize your system to serve up results or related keywords.<br>
		<big><li> <strong><a href="./knowledge_config.php?action=optimize">Optimize</a></strong></big></p>
		<p> -->
    <p>&nbsp;</p></td>
  <td height="350" align="center" style="background-image: url(../../images/g_knowledge_big);background-repeat: no-repeat;"><img src="../../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>

<?php include_once( "$DOCUMENT_ROOT/setup/footer.php" ) ; ?>