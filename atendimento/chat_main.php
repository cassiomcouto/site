<?php
	session_start() ;
	$session_chat = $_SESSION['session_chat'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$sessionid = ( isset( $_GET['sessionid'] ) ) ? $_GET['sessionid'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;

	if ( !file_exists( "web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting chat_main.php ...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("./web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("./web/VERSION_KEEP.php") ;
	include_once("./system.php") ;
	include_once("./lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Util.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Transcripts/put.php") ;

	// initialize
	$save_success = $do_viewit = 0 ;
	$admin = AdminUsers_get_UserInfo( $dbh, $session_chat[$sid]['admin_id'], $session_chat[$sid]['aspID'] ) ;
	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $session_chat[$sid]['deptid'], $session_chat[$sid]['aspID'] ) ;

	$THEME = ( $_SESSION['session_chat'][$sid]['isadmin'] && $_SESSION['session_chat'][$sid]['theme'] ) ?  $_SESSION['session_chat'][$sid]['theme'] : $THEME ;
	if ( file_exists( "web/".$session_chat[$sid]['asp_login']."/$LOGO" ) && $LOGO )
		$logo = "web/".$session_chat[$sid]['asp_login']."/$LOGO" ;
	else if ( file_exists( "web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "web/$LOGO_ASP" ;
	else if ( file_exists( "themes/$THEME/images/logo.gif" ) )
		$logo = "themes/$THEME/images/logo.gif" ;
	else
		$logo = "images/logo.gif" ;

	if ( $session_chat[$sid]['op2op'] && $session_chat[$sid]['isadmin'] )
		$name_typing = $session_chat[$sid]['admin_name'] ;
	else if ( $session_chat[$sid]['op2op'] && !$session_chat[$sid]['isadmin'] )
		$name_typing = $session_chat[$sid]['visitor_name'] ;
	else if ( $session_chat[$sid]['isadmin'] )
		$name_typing = $session_chat[$sid]['visitor_name'] ;
	else
		$name_typing = $session_chat[$sid]['admin_name'] ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $LANG['TITLE_SUPPORTREQUEST'] ?></title>

<script type="text/javascript" language="JavaScript1.2" src="js/chat_fn.js"></script>

<script type="text/javascript" language="JavaScript1.2">
<!--
	var unixtime = new Date().getTime();
	var timer_switch = 0;
	var final_switch = "on";
	var display_timer = "";
	var current_switch_value = 'on';
	var timeout = "";					
	var mute = -1;						// Mute sound when = 1
	var user_name = "";					// Client name
	var msg_count = 0;					// Message counter
	var fmain_url = "";					// The url of the conversation iFrame
	var sid = <?php echo $sid ?>;			// Session ID
	var mainloaded = 0 ;
	var active_tab = "info";
	var respawn = 0;					// only respawn if successful connection to op
	var pullimage ;
	var isclosed = 0 ;

	// Startup
	function init_main(){	
		if ( !mainloaded )
		{
			// Check for sound support and hide sound button if not available
			//if( document.flashsound && typeof document.flashsound.TGotoLabel == "undefined" ) objDisplay("soundbtn",-1);
			
			start_timer();
			toggle_typing( -1 ) ;
			fmain_url = frames['fmain'].location.href;
			inputFocus();
			write_typing( "<?php echo $name_typing ?>" ) ;
			mainloaded = 1 ;
		}
	}

	function checkifloaded()
	{
		loaded = pullimage.width ;
		if ( loaded == 1 )
			return true ;
		return false ;
	}

	function closeWindow( click_flag ){
		if ( !isclosed )
		{
			isclosed = 1 ;
			pullimage = new Image ;
			pullimage.src = "pull/chat_session.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&action=exit" ;
			pullimage.onload = checkifloaded ;

			// only ask for rating if deptid is present (it's not op2op chat)
			if ( respawn && <?php echo $session_chat[$sid]['deptid'] ?> )
			{
				url = "email_transcript.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&l=<?php echo $session_chat[$sid]['asp_login'] ?>&requestid=<?php echo $requestid ?>" ;
				if ( click_flag )
					var temp = setTimeout( "parent.window.location.href = url", 500 ) ;
				else
				{
					newwin = window.open( url, "rateme", 'status=no,scrollbars=no,menubar=no,resizable=0,location=no,screenX=50,screenY=100,width=450,height=360' ) ;
					newwin.focus() ;
				}
			}
			else if ( click_flag )
			{
				// delay it so it gives time for exit to register
				var temp = setTimeout( "parent.window.close()", 500 ) ;
			}
		}
	}

	function send_iswriting(e){
		window.parent.frames['session'].window.iswriting = 1 ;
		var key = -1 ;
		var shift ;

		key = e.keyCode ;
		shift = e.shiftKey ;

		if ( !shift && ( ( key == 13 ) || ( key == 10 ) ) )
		{
			sendMessage(<?php echo $session_chat[$sid]['isadmin'] ?>, '<?php echo ( $name_typing == $session_chat[$sid]['visitor_name'] ) ? $session_chat[$sid]['admin_name'] : $session_chat[$sid]['visitor_name'] ; ?>', '<?php echo ( $name_typing == $session_chat[$sid]['visitor_name'] ) ? $session_chat[$sid]['visitor_name'] : $session_chat[$sid]['admin_name'] ; ?>') ;
		}
	}

	function do_select( the_form )
	{
		index = the_form.selectedIndex ;
		new_message = the_form[index].value ;
		document.chatform.message.value = new_message ;
	}

	function toggle_typing( flag )
	{
		objVis("typing", flag, window.parent.frames['main']) ;
	}

	function write_typing( name )
	{
		window.parent.frames['session'].window.name_typing = name ;
		var id = "typing" ;
		document.getElementById(id).innerHTML = name + " esta digitando uma mensagem..." ;
	}
//-->
</script>

<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="themes/<?php echo $THEME ?>/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {
	color: #0000FF;
	font-weight: bold;
}
-->
</style>
</head>

<body OnUnload="closeWindow(0)">
<form method="post" action="chat.php" name="chatform" id="chatform" target="chatout">
<div id="main">
	<div id="logo"><img src="<?php echo $logo ?>" alt="" /></div>
	<div id="chat">
		<iframe src="files/nodelete_chat.php?requestid=<?php echo $requestid ?>&sid=<?php echo $sid ?>" width="100%" height="196" frameborder="0" name="fmain" id="fmain" OnLoad="init_main()"></iframe>
	</div>

	<div id="inputarea">
		<textarea name="message" cols="25" rows="2" id="message" class="message1" onKeyup="send_iswriting(event);" wrap="virtual"></textarea>
		<input type="submit" id="send" name="send" value="Enviar" onClick="return sendMessage(<?php echo $session_chat[$sid]['isadmin'] ?>, '<?php echo ( $name_typing == $session_chat[$sid]['visitor_name'] ) ? $session_chat[$sid]['admin_name'] : $session_chat[$sid]['visitor_name'] ; ?>', '<?php echo ( $name_typing == $session_chat[$sid]['visitor_name'] ) ? $session_chat[$sid]['visitor_name'] : $session_chat[$sid]['admin_name'] ; ?>');" />
	</div>

	<div id="status">
		<div id="typing" style="visibility:hidden;"></div>
		<span id="onlinetime" style="visibility:hidden;">Online: <span id="timer">00:00</span></span>
	</div>
	
	<div id="close"><a href="javascript:closeWindow(1);" title="Close window and end conversation">Fechar</a></div>
	
	<div id="sound_print">
		<a href="javascript:void(0)" OnClick="window.open('admin/view_transcript.php?x=<?php echo $session_chat[$sid]['aspID'] ?>&l=<?php echo $session_chat[$sid]['asp_login'] ?>&chat_session=<?php echo $sessionid ?>&deptid=<?php echo $session_chat[$sid]['deptid'] ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&action=view&theme_admin=<?php echo ( $session_chat[$sid]['isadmin'] ) ? $session_chat[$sid]['theme'] : "" ?>', 'newwin', 'status=no,scrollbars=no,menubar=no,toolbar=no,resizable=yes,location=no,width=450,height=360')" title="Printer friendly version" class="print">Imprimir</a>&nbsp;&nbsp;
		<a href="javascript:fontSize();" title="Change font size" class="font">Tamanho do Texto</a>&nbsp;&nbsp;
		<a href="javascript:muteSound();" title="Disable sound alerts" class="soundOn" id="sound">Som Ligar</a>
	</div>
	
	<?php
		if ( !$session_chat[$sid]['isadmin'] && $session_chat[$sid]['deptid'] ):
		$branding = preg_replace( "/href=(.*?)( |>)/i", "href=\"JavaScript:void(0)\" OnClick=\"window.open('\\1', 'newwin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes')\"\\2", preg_replace( "/['\"]/", "", $LANG['DEFAULT_BRANDING'] ) ) ;
		$branding = preg_replace( "/target=(.*?)(>| >)/i", " >", $branding ) ;
	?>
	<div id="copyright"><span class="style1">Sistema de Atendimento online</span> &copy;<a href="" title="Atendimento On-line" target="_blank">  </a></div>
	<?php endif ; ?>
	
</div>

<?php
	if ( $session_chat[$sid]['isadmin'] && $session_chat[$sid]['deptid'] ):
	$admin_dept_select_string = "deptID = ".$session_chat[$sid]['deptid'] ;
	include_once("./API/Canned/get.php") ;
?>
<div id="operator">
	<div>
		<label for="responses">Respostas:</label>
		<select name="responses" id="responses">
			<option value="">&nbsp;</option>
			<?php
				$canneds = ServiceCanned_get_UserCannedByType( $dbh, $session_chat[$sid]['admin_id'], $session_chat[$sid]['deptid'], 'r', $admin_dept_select_string ) ;
				for ( $c = 0; $c < count( $canneds ); ++$c )
				{
					$canned = $canneds[$c] ;
					$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
					$canned_message = Util_Format_ConvertSpecialChars( $canned['message'] ) ;

					print "		<option value=\"$canned_message\">$canned_name</option>\n" ;
				}
			?>
		</select> <input name="submit_response" type="button" value="Ir" class="go" OnClick="do_select( document.chatform.responses )" />&nbsp;&nbsp;
		
		<label for="replies">Comandos:</label>
		<select name="replies" id="replies">
			<option value="">&nbsp;</option>
			<?php
				$canneds = ServiceCanned_get_UserCannedByType( $dbh, $session_chat[$sid]['admin_id'], $session_chat[$sid]['deptid'], 'c', $admin_dept_select_string ) ;
				for ( $c = 0; $c < count( $canneds ); ++$c )
				{
					$canned = $canneds[$c] ;
					$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
					$canned_message = Util_Format_ConvertSpecialChars( $canned['message'] ) ;

					print "		<option value=\"$canned_message\">$canned_name</option>\n" ;
				}
			?>
		</select> <input name="submit_replies" type="button" value="Ir" class="go" OnClick="do_select( document.chatform.replies )" />
	</div>


	<div id="tabs">
		<ul>
			<li class="activetab" id="info"><a href="chat_admin_vinfo.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>" onclick="swapTabs('info');" target="foperator"><span>Info</span></a></li>
			<li id="footprints"><a href="chat_admin_vinfo.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&action=footprints" onclick="swapTabs('footprints');" target="foperator"><span>Footprints</span></a></li>
			<li id="transcripts"><a href="chat_admin_vinfo.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&action=transcripts" onclick="swapTabs('transcripts');" target="foperator"><span>Transcripts</span></a></li>
			<li id="call"><a href="chat_admin_transfer.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&action=transfer" onclick="swapTabs('call');" target="foperator"><span>Transfer</span></a></li>
			<li id="spam"><a href="chat_admin_vinfo.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&action=spam" onclick="swapTabs('spam');" target="foperator"><span>Spam Block</span></a></li>
		</ul>
	</div>
	<div id="operatorpanel">
		<iframe src="chat_admin_vinfo.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>" width="97%" height="200" frameborder="0" name="foperator" id="foperator"></iframe>
	</div>
</div>
<?php endif ; ?>

<div class="aux" style="visibility:hidden;">
	<iframe src="files/nodelete_blank.php" width="2" height="2" frameborder="0" name="chatout" id="chatout"></iframe>
</div>
<div class="aux" style="visibility:hidden;">
	<iframe src="files/nodelete_blank.php" width="2" height="2" frameborder="0" name="chatin" id="chatin"></iframe>
</div>
<?php if ( $session_chat[$sid]['isadmin'] && $session_chat[$sid]['deptid'] ): ?>
<div class="aux" style="visibility:hidden;">
	<iframe src="files/nodelete_blank.php" width="2" height="2" frameborder="0" name="adminframe" id="adminframe"></iframe>
</div>
<?php endif ; ?>
<div class="aux" style="display:hidden;">
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0" width="1" height="1" id="flashsound">
	  <param name="movie" value="sounds/chat_sounds.swf" />
	  <param name="quality" value="high" />
	  <param name="swliveconnect" value="true" />
	  <embed src="sounds/chat_sounds.swf" quality="high" pluginspage="//www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="1" height="1" swliveconnect="true" name="flashsound"></embed>
	</object>
</div>
</form>
</body>
</html>
