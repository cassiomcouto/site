<!--
var click_for_live_support = "Click for Live Support" ;

<?php
	$deptid = isset( $_GET['deptid'] ) ? preg_replace( "/[^0-9]/", "", $_GET['deptid'] ) : 0 ;
	$base_url = isset( $_GET['base_url'] ) ? strip_tags( $_GET['base_url'] ) : "" ;
	$base_url = str_replace( "(", "", $base_url ) ; $base_url = str_replace( ")", "", $base_url ) ; $base_url = str_replace( "?", "", $base_url ) ; $base_url = str_replace( ";", "", $base_url ) ;
	$text = isset( $_GET['text'] ) ? strip_tags( $_GET['text'] ) : "" ;
	$text = str_replace( "\"", "", $text ) ; $text = str_replace( "'", "", $text ) ;
	$l = isset( $_GET['l'] ) ? preg_replace( "/[^0-9a-z._-]/i", "", $_GET['l'] ) : 0 ;
	$x = isset( $_GET['x'] ) ? preg_replace( "/[^0-9]/", "", $_GET['x'] ) : 0 ;
?>
function dounique() { var date = new Date() ; return date.getTime() ; }
var chatwindow_loaded = 0 ;
var popblock_action_id = 1 ;
var tracker_refresh = 10000 ; // 1000 = 1 second
var btn = <?php srand((double)microtime()) ; $btn = mt_rand(100,10000000) ; echo $btn ?> ;
var do_tracker_flag_<?php echo $btn ?> = 1 ;
var start_tracker = dounique() ;
var time_elapsed ;
var refer = escape( document.referrer ) ;
var phplive_base_url = '<?php echo $base_url ?>' ;
var initiate = chat_opened = 0 ;
var pullimage_<?php echo $btn ?> = new Image ;
var date = new Date() ;
var unique = dounique() ;
var chat_width = 450 ;
var chat_height = 360 ;
var url = escape( location.toString() ) ;
var phplive_image_<?php echo $btn ?> = "<?php echo $base_url ?>/image.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&pagex="+url+"&unique="+unique+"&refer="+refer+"&text=<?php echo $text ?>" ;


var scriptpad = "" ;
var ns=(document.layers);
var ie=(document.all);
var w3=(document.getElementById && !ie);

/********************************************/
/***** proactive image settings: begin ******/
var ProactiveDiv ;
var browser_width ;
var backtrack = 0 ;
var isclosed = 0 ;
var repeat = 1 ;
var timer = 20 ;
var halt = 0 ;

// browser detection
var browser_ua = navigator.userAgent.toLowerCase() ;
var browser_type, tempdata ;
function phplive_detect_ua( text )
{
	stringposition = browser_ua.indexOf(text) + 1 ;
	tempdata = text ;
	return stringposition ;
}

if (  phplive_detect_ua( "firefox" ) )
{
	chat_width = 461 ;
	chat_height = 360 ;
}

// write external style here.. it won't work if we put it directly in the DIV
style = "<style type=\"text/css\">" ;
style += "<!--" ;
style += "#ProactiveSupport_<?php echo $btn ?> {visibility:hidden; position:absolute; height:1; width:1; top:0; left:0;}" ;
style += "-->" ;
style += "</style>" ;
document.write( style ) ;
if (ie||w3)
	browser_width = document.body.offsetWidth ;
else
	browser_width = window.outerWidth ;

function toggleMotion( flag )
{
	if ( flag )
		halt = 1 ;
	else
		halt = 0 ;
}

function initializeProactive_<?php echo $btn ?>()
{

	if(!ns && !ie && !w3) return ;
	if(ie)		ProactiveDiv = eval('document.all.ProactiveSupport_<?php echo $btn ?>.style') ;
	else if(ns)	ProactiveDiv = eval('document.layers["ProactiveSupport_<?php echo $btn ?>"]') ;
	else if(w3)	ProactiveDiv = eval('document.getElementById("ProactiveSupport_<?php echo $btn ?>").style') ;

	if (ie||w3)
		ProactiveDiv.visibility = "visible" ;
	else
		ProactiveDiv.visibility = "show" ;

	backtrack = 0 ;
	isclosed = 0 ;
	repeat = 1 ;
	moveIt(20) ;
}

function moveIt( h )
{
	if (ie)
	{
		documentHeight = document.body.offsetHeight/2+document.body.scrollTop-20 ;
		documentWidth = document.body.offsetWidth ;
	}
	else if (ns)
	{
		documentHeight = window.innerHeight/2+window.pageYOffset-20 ;
		documentWidth = window.innerWidth ;
	}
	else if (w3)
	{
		documentHeight = self.innerHeight/2+window.pageYOffset-20 ;
		documentWidth = self.innerWidth ;
	}
	ProactiveDiv.top = documentHeight-100 ;
	ProactiveDiv.left = documentWidth/2 ; // mod

	ProactiveDiv.left = h ;
	if ( h > ( browser_width - 350 ) )
		backtrack = 1 ;
	if ( backtrack && repeat && !halt )
		h -= 2 ;
	else if ( !backtrack && repeat && !halt )
		h += 2 ;

	if ( halt )
	{
		setTimeout("moveIt("+h+")",timer) ;
	}
	else if ( ( !backtrack || ( backtrack && ( h >= 20 ) ) ) && ( ( ProactiveDiv.visibility == "visible" ) || ( ProactiveDiv.visibility == "show" ) ) && repeat && !isclosed )
		setTimeout("moveIt("+h+")",timer) ;
	else if ( !isclosed )
	{
		backtrack = 0 ;
		repeat = 0 ;
		setTimeout("moveIt("+h+")",timer) ;
	}
	else
	{
		// incase it is closed during when it's off the page, set the position
		// back to the page so the horizontal scrollbars disappear (IE only)
		ProactiveDiv.left = h ;
	}
}

function DoClose(){
	if (ie||w3)
		ProactiveDiv.visibility = "hidden" ;
	else
		ProactiveDiv.visibility = "hide" ;
	isclosed = 1 ;
	halt = 0 ;
}

/********************************************/
/********************************************/


function checkinitiate_<?php echo $btn ?>()
{
	initiate = pullimage_<?php echo $btn ?>.width ;
	if ( ( initiate == 2 ) && !chat_opened )
	{
		chat_opened = 1 ;
		popblock_action_id = 2 ;
		launch_support_<?php echo $btn ?>() ;
	}
	else if ( ( initiate == 3 ) && !chat_opened )
	{
		chat_opened = 1 ;
		initializeProactive_<?php echo $btn ?>() ;
	}
	else if ( initiate == 100 )
	{
		do_tracker_flag_<?php echo $btn ?> = 0 ;
	}

	if ( ( initiate == 1 ) && chat_opened )
		chat_opened = 0 ;
}
function do_tracker_<?php echo $btn ?>()
{
	// check to make sure they are not idle for more then 1 hour... if so, then
	// they left window open and let's stop the tracker to save server load time.
	// (1000 = 1 second)
	var unique = dounique() ;
	time_elapsed = unique - start_tracker ;
	if ( time_elapsed > 3600000 )
		do_tracker_flag_<?php echo $btn ?> = 0 ;

	pullimage_<?php echo $btn ?> = new Image ;
	pullimage_<?php echo $btn ?>.src = "<?php echo $base_url ?>/image_tracker.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&pagex="+url+"&unique="+unique ;

	pullimage_<?php echo $btn ?>.onload = checkinitiate_<?php echo $btn ?> ;
	if ( do_tracker_flag_<?php echo $btn ?> == 1 )
		setTimeout("do_tracker_<?php echo $btn ?>()",tracker_refresh) ;
}

function start_timer_<?php echo $btn ?>( c )
{
	if ( c == 0 )
	{
		if ( !chatwindow_loaded && ( popblock_action_id == 1 ) )
			alert( "Popup blocker prevented the loading of the chat window.  Please press <SHIFT> while clicking the chat image." ) ;
		else if ( !chatwindow_loaded && ( popblock_action_id == 2 ) )
		{
			NotifyPopupBlocker_<?php echo $btn ?>() ;
			chat_opened = 1 ;
			popblock_action_id = 1 ;
			initializeProactive_<?php echo $btn ?>() ;
		}
		else
			chatwindow_loaded = 0 ;
	}
	else
	{
		--c ;
		var temp = setTimeout( "start_timer_<?php echo $btn ?>("+c+")", 1000 ) ;
	}
}

function launch_support_<?php echo $btn ?>()
{
	start_timer_<?php echo $btn ?>( 2 ) ;
	var request_url_<?php echo $btn ?> = "<?php echo $base_url ?>/request.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&pagex="+url ;
	var newwin_chat = window.open( request_url_<?php echo $btn ?>, unique, 'scrollbars=no,menubar=no,resizable=0,location=no,screenX=50,screenY=100,width='+chat_width+',height='+chat_height+'' ) ;
	if ( newwin_chat )
	{
		newwin_chat.focus() ;
		DoClose() ;
		chatwindow_loaded = 1 ;
	}
}

function WriteChatDiv()
{
	var scroll_image = new Image ;
	scroll_image.src = "<?php echo $base_url ?>/scroll_image.php?x=<?php echo $x ?>&l=<?php echo $l ?>&"+unique ;

	output = "<div id=\"ProactiveSupport_<?php echo $btn ?>\">" ;
	output += "<table cellspacing=0 cellpadding=0 border=0>" ;
	output += "<tr><td align=right><table cellspacing=0 cellpadding=0 border=0><tr><td bgColor=#E1E1E1><a href='JavaScript:RejectInitiate();' OnMouseOver=\"toggleMotion(1)\" OnMouseOut=\"toggleMotion(0)\"><font color=#828282 size=1 face=arial>&nbsp;close window</font>&nbsp;<img src=\"<?php echo $base_url ?>/images/initiate_close.gif\" width=10 height=10 border=0></a></td></tr></table></td></tr>" ;
	output += "<tr><td align=center>" ;
	output += "<a href=\"JavaScript:launch_support_<?php echo $btn ?>()\" OnMouseOver=\"toggleMotion(1)\" OnMouseOut=\"toggleMotion(0)\"><img src=\""+scroll_image.src+"\" border=0></a>" ;
	output += "</td></tr></table>" ;
	output += "</div>" ;
	document.writeln( output ) ;

	if(ie)		ProactiveDiv = eval('document.all.ProactiveSupport_<?php echo $btn ?>.style') ;
	else if(ns)	ProactiveDiv = eval('document.layers["ProactiveSupport_<?php echo $btn ?>"]') ;
	else if(w3)	ProactiveDiv = eval('document.getElementById("ProactiveSupport_<?php echo $btn ?>").style') ;

	if (ie||w3)
		ProactiveDiv.visibility = "hidden" ;
	else
		ProactiveDiv.visibility = "hide" ;
}

function RejectInitiate()
{
	var rejectimage_<?php echo $btn ?> = new Image ;
	rejectimage_<?php echo $btn ?>.src = "<?php echo $base_url ?>/image_tracker.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&unique="+unique+"&action=reject" ;
	DoClose() ;
	chat_opened = 0 ;
}

function NotifyPopupBlocker_<?php echo $btn ?>()
{
	var notifyimage_<?php echo $btn ?> = new Image ;
	notifyimage_<?php echo $btn ?>.src = "<?php echo $base_url ?>/image_tracker.php?l=<?php echo $l ?>&x=<?php echo $x ?>&deptid=<?php echo $deptid ?>&unique="+unique+"&action=notifypopup" ;
}

var status_image_<?php echo $btn ?>_<?php echo $deptid ?> = "<a href='JavaScript:void(0)' onMouseOver='window.status=\""+click_for_live_support+"\"; return true;' onMouseOut='window.status=\"\"; return true;' OnClick='launch_support_<?php echo $btn ?>()'><?php echo $text ?><img src="+phplive_image_<?php echo $btn ?>+" border=0 alt='"+click_for_live_support+"' title='"+click_for_live_support+"'></a>" ;
document.write( status_image_<?php echo $btn ?>_<?php echo $deptid ?> ) ;

if ( !phplive_loaded )
{
	WriteChatDiv() ;
	do_tracker_<?php echo $btn ?>() ;
}
var phplive_loaded = 1 ;
//-->