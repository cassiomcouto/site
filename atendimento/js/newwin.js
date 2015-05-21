function open_newwin( mypage, myname, w ,h , settings )
{
	// usage:
	// newwin = open_newwin( "index.phtml", "emailAll", 200, 100, "scrollbars=no,menubar=no,resizable=0,location=no" ) ;
	// <script language="JavaScript" src="<? echo $BASE_URL ?>/js/newwin.js"></script>

	LeftPosition = (screen.width) ? (screen.width-w)/2 : 0 ;
	TopPosition = (screen.height) ? (screen.height-h)/2 : 0 ;
	new_settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+','+settings ;
	win = window.open( mypage, myname, new_settings ) ;
	return win ;
}
