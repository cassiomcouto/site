<?php
	include_once("./web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("$DOCUMENT_ROOT/system.php") ;
	include_once("$DOCUMENT_ROOT/lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;

	// initialize
	$action = "" ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	$success = 0 ;
	// update all admins status to not available if they have been idle

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }

	$css_path = ( !isset( $css_path ) ) ? $css_path = "./" : preg_replace( "/[a-z]/i", "", $css_path ) ;
	if ( !file_exists( $css_path."css/default.php" ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
?>
<html>
<head>
<title>Ajuda Rapida - Sistema de Atendimento online </title>
<?php include_once( $css_path."css/default.php" ) ; ?>
<script language="JavaScript">
<!--

//-->
</script>

<style type="text/css">
<!--
.style1 {
	color: #0000FF;
	font-weight: bold;
}
-->
</style>
<body bgColor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr>
	<td valign="top" class="bg" width="100%">

		<table cellspacing=1 cellpadding=3 border=0 width="95%">
		<tr>
			<td><span class="basicTitle">Ajuda Rapida - Sistema de Atendimento online!</span><p>

			<?php if ( $action == "sp_cell" ): ?>
			<b><li> Cell Phone Format</b><br>
			Always include your COUNTRY CODE in your cell phone.  For US clients, please include the leading 1 and your area code.  For all international clients, include your country code and no prefix:
			<ul> Example Demo Cellular Formats
				<li> 12125555555 (example US)
				<li> 448885550000 (example UK)
			</ul>
				

			<?php elseif ( $action == "share_transcripts" ): ?>
			<b><li> Share Transcripts</b><br>
			If you allow sharing of transcripts, all operators that are assigned to a department will be able to view each other's transcripts.  If you do not allow sharing of transcripts, operators will only be able to view their own transcripts.


			<?php elseif ( $action == "visible" ): ?>
			<b><li> Department Visible to Public (not hidden)</b><br>
			Hidden departments will allow managers/admins to monitor and be able to receive calls that are only transferred to them or via operator-to-operator chat.  Hidden departments are not visible or directly accessible to the public.

			<?php elseif ( $action == "show_que" ): ?>
			<b><li> Display Chat Que</b><br>
			When visitors request support, the system will display the number of current chats active (que).  You may hide or display this information to the visitor.


			<?php elseif ( $action == "sp_cpage" ): ?>
			<b><li> Purchase Complete Confirmation Page</b><br>
			Typically, every eCommerce websites have a basic order process: browse and select goods/services, capture online payment and lastly a <u><i>purchase complete confirmation page</i></u> or Thank You page.
			<p>
			The "Sales Path" HTML Code belongs in the purchase complete confirmation page.
			<p>
			<b>Question:</b> What if I have a script that creates dynamically a purchase complete confirmation page?<br>
			<b>Answer:</b> Simply place the "Sales Path" HTML code in the confirmation portion of the script (right after the receipt output is fine).


			<?php elseif ( $action == "sp_message" ): ?>
			<b><li> &lt;Your Message&gt;</b><br>
			If you notice in the "Sales Path" HTML code, there is <span class="hilight">&lt;Your Message&gt;</span> string.  You need to replace this with that of your personal notification message. This message will be sent to your cellular or email each time a "Sales Path" is tracked.
			<ul> Example Notification Messages
				<li> Just got a Sales!
				<li> A person just signed up!
			</ul>

			If you are computer and code savvy, you can dynamically put variables to your message.  For example, if you have different products and would like to include that information in your notifications, simply generate the notification message from your script!
			<ul> Example Dynamic Notification Messages
				<li> &lt;? print $total ?&gt; units sold at $&lt;? print $cost ?&gt;
			</ul>


			<?php elseif ( $action == "commands" ): ?>
			<ul>
				<li> <span class="hilight"><b>url:</b></span><i>URL</i> (hyperlink a URL) 
				<li> <span class="hilight"><b>image:</b></span><i>URL/sample.gif</i> (embed an image)
				<li> <span class="hilight"><b>push:</b></span><i>URL</i> (opens new browser containing URL of webpage, word docs, etc.)
			</ul>

			<p>
			examples:<br>
			<code>url:http://www.google.com/trial.php</code><br>
			<code>email:nulled@google.com</code>


			<?php elseif ( $action == "email_transcripts" ): ?>
			<b><li> Visitor Email Transcripts</b><br>
			When a visitor ends the chat session, he/she can request a copy of the transcript to their email address.  You can enable or disable this feature.  In some sales environment, this option may be useful.



			<?php elseif ( $action == "traffic_monitor" ): ?>
			<b><li> Operator Traffic Monitor</b><br>
			Enabling this feature allows operators within this department to view the website traffic on their operator consoles.  This also allows operators to initiate chat with your website visitors.


			<?php endif ; ?>

			<p>
			<form><input type="button" class="mainButton" value="Close Window" OnClick="window.close()"></form>
			</td>
		</tr>
		</table>
	</td>
  </tr>
  <tr> 
	<td height="20" align="right" class="bgFooter" style="height:20px"><img src="<?php echo $css_path ?>images/bg_corner_footer.gif" alt="" width="94" height="20"></td>
  </tr>
  <tr>
	<td height="20" align="center" class="bgCopyright" style="height:20px">
		<span class="style1">Sistema Atendimento online 
	Versão</span> <?php echo $PHPLIVE_VERSION ?> &copy; <a href="" target="_blank" class="style1"></a>	</td>
  </tr>
</table>
</body>
</html>
