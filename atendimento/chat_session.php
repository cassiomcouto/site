<?php
	session_start() ;
	$action = "" ;
	$session_chat = $_SESSION['session_chat'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	$sessionid = ( isset( $_GET['sessionid'] ) ) ? $_GET['sessionid'] : "" ;
	$requestid = ( isset( $_GET['requestid'] ) ) ? $_GET['requestid'] : "" ;
	$start = ( isset( $_GET['start'] ) ) ? $_GET['start'] : "" ;
	$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : "" ;

	if ( !file_exists( "web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: config files not found!] Exiting chat_session.php ...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("./web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("./lang_packs/$LANG_PACK.php") ;
	include_once("./system.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/remove.php") ;

	if ( $session_chat[$sid]['op2op'] && $session_chat[$sid]['isadmin'] )
		$name_typing = $session_chat[$sid]['admin_name'] ;
	else if ( $session_chat[$sid]['op2op'] && !$session_chat[$sid]['isadmin'] )
		$name_typing = $session_chat[$sid]['visitor_name'] ;
	else if ( $session_chat[$sid]['isadmin'] )
		$name_typing = $session_chat[$sid]['visitor_name'] ;
	else
		$name_typing = $session_chat[$sid]['admin_name'] ;
?>
<html><head><title> [chat sessão] </title>
<script language="JavaScript" src="js/xmlhttp.js"></script>
<script language="JavaScript">
<!--
	var iswriting = 0 ;
	var unique = 0 ;
	var j_string = "" ;
	var message = "" ;
	var admin_name = "<?php echo $session_chat[$sid]['admin_name'] ?>" ;
	var name_typing = "<?php echo $name_typing ?>" ;

	function dounique()
	{
		var date = new Date() ;
		return date.getTime() ;
	}

	function init_session()
	{
		if ( window.parent.frames['main'].window.mainloaded )
			var temp = setTimeout( "xmlpull()", 1000 ); // give buffer for nodelete_chat.php
		else
			var temp = setTimeout( "init_session()",1000) ;
	}

	function checkifloaded( flag )
	{
		if ( flag == 1 )
			do_reload() ;
		else if ( flag == 2 )
			window.parent.frames['main'].window.toggle_typing( 1 ) ;
		else
			window.parent.frames['main'].window.toggle_typing( -1 ) ;
	}

	function do_reload()
	{
		window.parent.frames['main'].window.toggle_typing( -1 ) ;
		unique = dounique() ;
		var xmlreload = initxmlhttp() ;
		var url = "<?php echo $BASE_URL ?>/pull/chat_session.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&unique="+unique ;
		xmlreload.open( "GET", url, true ) ;
		xmlreload.onreadystatechange=function()
		{
			if (xmlreload.readyState==4)
			{
				j_string = xmlreload.responseText ;
				if ( j_string )
				{
					var string_array = j_string.split( "<br<?php echo $sessionid ?>>" ) ;
					for ( var c = 0; c < string_array.length; ++c )
					{
						var j_string_write = string_array[c] ;
						if ( j_string_write )
						{
							if ( j_string_write.indexOf("window.parent.frames") != -1 )
								eval( j_string_write ) ;
							else if ( j_string_write.indexOf("<push<?php echo $sessionid ?> ") != -1 )
							{
								var result = j_string_write.match( /<push<?php echo $sessionid ?> (.*?) >/ ) ;
								var push_url = result[1] ;
								window.parent.frames['main'].window.addMessage( j_string_write, <?php echo ( $session_chat[$sid]['isadmin'] ) ? "'".$session_chat[$sid]['visitor_name']."'" : "admin_name" ; ?>, '<?php echo ( $session_chat[$sid]['isadmin'] ) ? "client" : "operator" ; ?>', 'receive' ) ;
								window.parent.dopush( push_url, "newwin" ) ;
							}
							else if ( j_string_write.indexOf("<respawn<?php echo $sessionid ?>>") != -1 )
							{
								if ( !<?php echo $session_chat[$sid]['isadmin'] ?> )
									window.parent.frames['main'].window.respawn = 1 ;
							}
							else if ( j_string_write.indexOf("<mbox<?php echo $sessionid ?>>") != -1 )
								window.parent.window.messagebox() ;
							else
							{
								window.parent.frames['main'].window.addMessage( j_string_write, name_typing, '<?php echo ( $session_chat[$sid]['isadmin'] ) ? "client" : "operator" ; ?>', 'receive' ) ;
							}
						}
					}
				}
			}
		}
		xmlreload.send(null) ;
	}

	function xmlpull()
	{
		unique = dounique() ;
		var xmlchat = initxmlhttp() ;
		var url = '<?php echo $BASE_URL ?>/pull/chat.php?sid=<?php echo $sid ?>&sessionid=<?php echo $sessionid ?>&requestid=<?php echo $requestid ?>&unique='+unique+'&iswriting='+iswriting ;
		xmlchat.open( "GET", url, true ) ;
		xmlchat.onreadystatechange=function()
		{
			if (xmlchat.readyState==4)
			{
				checkifloaded( xmlchat.responseText ) ;
			}
		}
		xmlchat.send(null) ;
		var temp = setTimeout("xmlpull()",<?php echo $CHECK_NEW_MSG_REFRESH * 1000 ?>) ;
	}

	function xmlsubmit(message)
	{
		iswriting = 0 ;
		var temp_message = encodeURIComponent( message ) ;
		if ( temp_message == "undefined" )
			temp_message = escape( message ) ;
		message = temp_message.replace( /%0A/gi, "-br-" ) ;
		var xmlchatsubmit = initxmlhttp() ;
		var url = "<?php echo $BASE_URL ?>/pull/chat_session.php?action=submit&sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&message="+message ;
		xmlchatsubmit.open( "GET", url, true ) ;
		xmlchatsubmit.onreadystatechange=function()
		{
			if (xmlchatsubmit.readyState==4)
			{
				return true ;
			}
		}
		xmlchatsubmit.send(null) ;
	}

//-->
</script></head>
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" OnLoad="init_session()">
</body></html>
<?php
//	mysql_close( $dbh['con'] ) ;
?>
