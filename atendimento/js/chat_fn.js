// Cycle through font sizing in the conversation window
function fontSize(){
	frames['fmain'].fontsizeup();
	inputFocus();
}

// Jump cursor foxus back to the input box
function inputFocus(){
	document.getElementById('message').focus();
}

// Call flashsound swf to play desired sound
function playSound(sound){
	if( mute == -1 ){
		if( document.flashsound ){
			if( typeof document.flashsound.TGotoLabel != "undefined" ){
				document.flashsound.TGotoLabel("/", sound);
				document.flashsound.TPlay("/");
			}
		}
	}
}

// Toggle sound mute state
function muteSound(){
	var e = document.getElementById("sound");
	e.innerHTML = ( mute == 1 ) ? "Sound On" : "Sound Off";
	e.className = ( mute == 1 ) ? "soundOn" : "soundOff";
	mute *= -1;
	inputFocus();
}

// Start conversation timer
function start_timer( initial_value ){
	if( initial_value ) unixtime = initial_value;
	timer_switch = 1;
	objVis('onlinetime',1);		// Show online time text
	timer_cycle();
}

// Stop conversation timer
function stop_timer(){
	timer_switch = 0;
	final_switch = "off";
}

// Update timer
function timer_cycle(){
	if( final_switch == "on" ){
		var now = new Date() ;
		// let's start at 0 so we can increase each second
		var the_timer = new Date( now.getTime() - unixtime ) ;
		var minutes = the_timer.getMinutes() ;
		var seconds = the_timer.getSeconds() ;
		// tack on 0 if on digit
		if( minutes <= 9 ) minutes = "0" + minutes ;
		if( seconds <= 9 ) seconds = "0" + seconds ;
		
		document.getElementById("timer").innerHTML = minutes + ":" + seconds;

		// call timer each second so we can see the cycle
		if( timer_switch && final_switch == "on" ){
			timeout = setTimeout("timer_cycle()", 1000);
		}
	} else {
		//document.getElementById("timer").innerHTML = "&nbsp;";
	}
}

// Send message
function sendMessage(isadmin, user_name, visitor_name){
	var m = document.getElementById("message");
	var msg = m.value.trimtrailing();
	msg = msg.tags();
	var orig_msg = msg;

	strlen = msg.length ;
	if ( strlen > 1400 )
	{
		alert( "Text is too long.  Less then 1400 characters please." ) ;
		return false ;
	}
	
	// firefox uses \n only, IE uses \r\n
	if ( isadmin )
	{
		msg = msg.replace(/(\r\n)/gi,"<br>");
		msg = msg.replace(/\n/gi,"<br>"); // firefox double check
		msg = msg.parseadmin(user_name,visitor_name);
		while( ( msg.indexOf("url:") != -1 ) || ( msg.indexOf("push:") != -1 ) || ( msg.indexOf("image:") != -1 ) || ( msg.indexOf("email:") != -1 ) )
			msg = msg.parseadmin(user_name,visitor_name);
		addMessage(msg,user_name,"operator","");
	}
	else
	{
		msg = msg.replace(/(\r\n)/gi,"<br>");
		msg = msg.replace(/\n/gi,"<br>"); // firefox double check
		addMessage(msg,user_name,"client","");
	}
	window.parent.frames['session'].window.xmlsubmit(orig_msg) ;
	document.chatform.submit();
	m.value = "" ;
	m.focus();
}

// Add message to conversation window
function addMessage(message,user,class_name,sound){
	var f, p, s, txtNode;
	msg_count++;
	
	f = frames['fmain'];
	e = f.document.getElementById('conversation');		// ref to conversation block
	
	if( f.document.createElement ){
		p = f.document.createElement("p");				// Create <p> to hold message
	} else {
		p = f.document.createElementNS("http://www.w3.org/1999/xhtml","p");
	}
	
	p.setAttribute("id","msg-"+msg_count);			// Assign id to message
	e.appendChild(p);								// Add new message to the end of the conversation
	
	var m = f.document.getElementById("msg-"+msg_count);
	m.className = class_name
	m.innerHTML = (user != "") ? "<span>"+user+":</span> "+message : message;
	
	if( sound ) playSound(sound);
	
	// Scroll new message into view
	frames['fmain'].scrollTo(0,99999);
}

// Automatically convert userside urls into clickable links
function autoURL(message){
	var regex = /\b((https?|telnet|gopher|file|wais|ftp):[\w\/\#~:.?+=&%@!\-]+?)*?(?=[.:?\-]*(?:[^\w\/\#~:.?+=&%@!\-]|$))/gi;
	
	return message.replace(regex, "<a href=\"$1\" target=\"_blank\" title=\"Link opens in a new window\">$1</a>");
}

// Swap admin tabs
function swapTabs(tab){
	if( tab != active_tab ){
		document.getElementById(tab).className = "activetab";
		document.getElementById(active_tab).className = "";
		active_tab = tab;
	}
}


// Set display of page element
// f = frame, n = element id, b = display style e.g. none,block,inline
// s[-1,0,1] = state (hide, toggle display, show)
function objDisplay(n,s,f,b){
	if( !b ) b = "block";
	if( !f ) f = this;
	var e = f.document.getElementById(n);
	if(!s || s == 0) s = (e.style.display == 'none') ? 1:-1;
	e.style.display = (s==1) ? b:'none';
}

// Set visibility of page element
// f = frame, n = element id 
// s[-1,0,1] = state (hide, toggle display, show)
function objVis(n,s,f){
	if( !f ) f = window;
	var e = f.document.getElementById(n);
	if(!s || s == 0) s = (e.style.visibility == 'hidden') ? 1:-1;
	e.style.visibility = (s==1) ? 'visible':'hidden';
}

function regmatch(s,r){
	var myString = new String(s) ;
	var myRE = new RegExp(r, "i") ;
	var results = myString.match(myRE) ;
	return (results[1]) ;
}


//
// Handy prototypes
String.prototype.trim = function(){ return this.replace(/^\W*|\W*$/g, ""); };	// Trim non-word characters from the start and end of the string
String.prototype.tags = function(){ var string = this.replace(/>/g, "&gt;"); return string.replace(/</g, "&lt;"); };
String.prototype.trimtrailing = function(){
	var lastchars = this.substr((this.length-2), this.length) ;
	if ( lastchars == "\r\n" ) { return this.substr(0, (this.length-2)) ; }
	else { return this ; }
};
String.prototype.parseadmin = function(admin_name, visitor_name){
	var output_string = this ;
	var temp_string ;
	if ( output_string.indexOf("image:") != -1 )
		output_string = output_string.replace( /image:(.*?)($| |<br>)/, "<img src=$1 > " ) ;
	if ( output_string.indexOf("url:") != -1 )
	{
		var url_prefix = "http:" ;
		if ( output_string.indexOf("https:" ) != -1 )
			url_prefix = "https:" ;

		temp_string = regmatch( output_string, "url:(.*?)($| |<br>)" ) ;
		if ( temp_string.indexOf( url_prefix ) != -1 )
			temp_string = "<a href=\"JavaScript:void(0)\" OnClick=\"window.open('"+temp_string+"', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">"+temp_string+"</a> " ;
		else
			temp_string = "<a href=\"JavaScript:void(0)\" OnClick=\"window.open('"+url_prefix+"//"+temp_string+"', 'admin', 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1')\">"+url_prefix+"//"+temp_string+"</a> " ;
		output_string = output_string.replace( /url:(.*?)($| |<br>)/, temp_string ) ;
	}
	if ( output_string.indexOf("push:") != -1 )
	{
		var url_prefix = "http:" ;
		if ( output_string.indexOf("https:" ) != -1 )
			url_prefix = "https:" ;

		temp_string = regmatch( output_string, "push:(.*?)($| |<br>)" ) ;
		if ( temp_string.indexOf( url_prefix ) != -1 )
			temp_string = "[ PUSHING webpage <a href="+temp_string+" target=new >"+temp_string+"</a> ]" ;
		else
			temp_string = "[ PUSHING webpage <a href="+url_prefix+"//"+temp_string+" target=new >"+url_prefix+"//"+temp_string+"</a> ]" ;
		output_string = output_string.replace( /push:(.*?)($| |<br>)/, temp_string ) ;
	}
	if ( output_string.indexOf("email:") != -1 )
		output_string = output_string.replace( /email:(.*?)($| |<br>)/, "<a href=mailto:$1 >$1</a> " ) ;
	if ( output_string.indexOf("%%user%%") != -1 )
		output_string = output_string.replace( /%%user%%/g, visitor_name ) ;
	if ( output_string.indexOf("%%operator%%") != -1 )
		output_string = output_string.replace( /%%operator%%/g, admin_name ) ;
	return output_string ;
};
