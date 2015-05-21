<?php
	$css_path = ( !isset( $css_path ) ) ? $css_path = "../" : preg_replace( "/[a-z]/i", "", $css_path ) ;
	if ( !file_exists( $css_path."css/default.php" ) || !isset( $DOCUMENT_ROOT ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}

	if ( file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/$session_setup[login]/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;

	$initiate_chat_menu = $trackit_menu = $salespath_menu = "" ;
	if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) )
	{
		$initiate_chat_menu = " | <a href=\"$BASE_URL/setup/customize.php?action=initiate\" class=\"nav\">Iniciar Chat Image</a>" ;
		$trackit_menu = "<a href=\"$BASE_URL/admin/traffic/click_track.php\" class=\"nav\">Click Track\'it</a><!-- | <a href=\"$BASE_URL/admin/traffic/conversion.php\" class=\"nav\">Click Conversion</a> -->" ;
	}
	if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/salespath.php" ) )
		$salespath_menu = " | <a href=\"$BASE_URL/setup/salespath.php\" class=\"nav\">Sales Path</a>" ;
?>
<html>
<head>
<title> Administração Geral </title>
<?php include_once( $css_path."css/default.php" ) ; ?>
<script language="JavaScript" type="text/JavaScript">
<!--

var section = <?php echo $section ; ?> ;	// Section number

var nav_start = '<table width="427" border="0" cellspacing="0" cellpadding="3"><tr><td align="center" class="nav">';
var nav_end = '</td></tr></table>';

var nav = new Array();
nav[0] = 'Bem Vindo a Administração';
nav[1] = '<a href="<?php echo $BASE_URL ?>/setup/adddept.php" class="nav">Departamentos</a> | <a href="<?php echo $BASE_URL ?>/setup/adduser.php" class="nav">Operadores</a> | <a href="<?php echo $BASE_URL ?>/setup/code.php" class="nav">Gerar HTML</a>';
nav[2] = '<a href="<?php echo $BASE_URL ?>/setup/customize.php?action=colors" class="nav">Temas</a> | <a href="<?php echo $BASE_URL ?>/setup/customize.php?action=icons" class="nav">Chat Icones</a> <?php echo $initiate_chat_menu ?>';
nav[3] = '<a href="<?php echo $BASE_URL ?>/setup/footprints.php" class="nav">Trafegos</a> | <a href="<?php echo $BASE_URL ?>/setup/refer.php" class="nav">Referencia URLs</a>';
nav[4] = '<a href="<?php echo $BASE_URL ?>/setup/processes.php?action=chat" class="nav">Atual Chats</a> | <a href="<?php echo $BASE_URL ?>/setup/transcripts.php" class="nav">Chat Transcripts</a> | <a href="<?php echo $BASE_URL ?>/setup/processes.php?action=consol" class="nav">Operator Sessions</a>';
nav[5] = '<a href="<?php echo $BASE_URL ?>/setup/prefs.php?action=footprints" class="nav">Exclude IP</a> | <a href="<?php echo $BASE_URL ?>/setup/email_transcript.php" class="nav">Email Transcript</a> | <a href="<?php echo $BASE_URL ?>/setup/prefs.php?action=timezone" class="nav">Time Zone</a>';
nav[6] = '<a href="<?php echo $BASE_URL ?>/setup/chatprefs.php?action=polling" class="nav">Chat Request Polling Time</a> | <a href="<?php echo $BASE_URL ?>/setup/chatprefs.php?action=polling_type" class="nav">Chat Request Polling Type</a> | <a href="<?php echo $BASE_URL ?>/setup/chatprefs.php?action=language" class="nav">Idiomas</a>';
nav[7] = '<?php echo $trackit_menu ?> <?php echo $salespath_menu ?>';
nav[8] = '<a href="<?php echo $BASE_URL ?>/setup/statistics.php" class="nav">Support Request</a> | <a href="<?php echo $BASE_URL ?>/setup/opratings.php" class="nav">Operator Ratings</a> | <a href="<?php echo $BASE_URL ?>/setup/profiles.php?action=pics" class="nav">Operator Pics</a>';
nav[9] = '<a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_config.php" class="nav">Preferences</a> | <a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge_config.php?action=config" class="nav">Setup and Build</a>';

var button = new Array();
button[0] = '';
button[1] = 'manage';
button[2] = 'interface';
button[3] = 'reports';
button[4] = 'sessions';
button[5] = 'prefs';
button[6] = 'chatprefs';
button[7] = 'marketing';
button[8] = 'profile';
button[9] = 'knowledge';

// Onload
function init(){
	rollOver(section);					// Setup navigation
	if(section>0) sE(gE('navBack'));	// Show back button
}

// Rollover and navigation change
// s = section number; n = button name
function rollOver(s){
	if(button[s] != '' ) MM_swapImage(button[s],'','<?php echo $css_path ?>images/b_'+button[s]+'-over.gif',1);
	
	// Only show sub-navigation links in none NS4 browsers
	if(!l){
		e = gE('navigation');
		wH(e,nav[s]);
	}
}

// Rollout
function rollOut(s){
	if(!s) s=0;
	if(s>0 && s!=section) MM_swapImgRestore();
}


// Basic functions
d=document;l=d.layers;op=navigator.userAgent.indexOf('Opera')!=-1;var ie=d.all?1:0;var w3c=d.getElementById?1:0;
function gE(e,f){if(l){f=(f)?f:self;var V=f.document.layers;if(V[e])return V[e];for(var W=0;W<V.length;)t=gE(e,V[W++]);return t;}if(d.all)return d.all[e];return d.getElementById(e);} // Get element
function wH(e,h){if(l){Y=e.document;Y.open();Y.write(h);Y.close();}else if(e.innerHTML)e.innerHTML=h;}		// Write html
function sE(e){l?e.visibility='show':e.style.visibility='visible';}
function hE(e){l?e.visibility='hide':e.style.visibility='hidden';}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}


//-->
</script>
<script language="JavaScript" src="<?php echo $css_path ?>js/global.js"></script>
</head>
<body onload="init();" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="65" valign="top" class="bgHead"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="102" height="65" rowspan="2" valign="bottom" class="bgCornerTop"><img src="<?php echo $css_path ?>images/bg_corner_top.gif" width="102" height="65"></td>
		  <td height="65" rowspan="2"><div id="logo"><a href="<?php echo $BASE_URL ?>/setup/options.php"><img src="<?php echo $logo ?>" border="0"></a></div></td>
		  <td align="right" valign="top"><strong><a href="<?php echo $BASE_URL ?>/setup/login.php?action=logout">Sair</a>&nbsp;&nbsp;</strong></td>
		</tr>
		<tr> 
		  <td align="right"> <table border="0" cellspacing="0" cellpadding="0">
			  <tr align="center"> 
				<td><a href="<?php echo $BASE_URL ?>/setup/manager.php" onMouseOut="rollOut(1)" onMouseOver="rollOver(1)"><img src="<?php echo $css_path ?>images/b_manage.gif" alt="Manager" name="gerenciar" width="32" height="32" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/interface.php" onMouseOut="rollOut(2)" onMouseOver="rollOver(2)"><img src="<?php echo $css_path ?>images/b_interface.gif" alt="Interface" name="interface" width="32" height="32" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/prefs.php" onMouseOut="rollOut(5)" onMouseOver="rollOver(5)"><img src="<?php echo $css_path ?>images/b_prefs.gif" alt="Preferences" name="preferenciar" width="32" height="32" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/profiles.php" onMouseOut="rollOut(8)" onMouseOver="rollOver(8)"><img src="<?php echo $css_path ?>images/b_profile.gif" alt="Profiles" name="perfil" width="32" height="32" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/sessions.php" onMouseOut="rollOut(4)" onMouseOver="rollOver(4)"><img src="<?php echo $css_path ?>images/b_sessions.gif" alt="Sessions" name="sessoes" width="32" height="32" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/reports.php" onMouseOut="rollOut(3)" onMouseOver="rollOver(3)"><img src="<?php echo $css_path ?>images/b_reports.gif" alt="Reports" name="reports" width="32" height="32" hspace="5" border="0"></a></td>

				<td><a href="<?php echo $BASE_URL ?>/setup/chatprefs.php" onMouseOut="rollOut(6)" onMouseOver="rollOver(6)"><img src="<?php echo $css_path ?>images/b_chatprefs.gif" alt="Chat Prefs" name="chat configs" width="32" height="32" hspace="5" border="0"></a></td>

				<?php if ( $INITIATE && ( file_exists( "$DOCUMENT_ROOT/admin/traffic/click_track.php" ) || file_exists( "$DOCUMENT_ROOT/web/$session_setup[login]/salespath.php" ) ) ): ?>
				<td><a href="<?php echo $BASE_URL ?>/setup/marketing.php" onMouseOut="rollOut(7)" onMouseOver="rollOver(7)"><img src="<?php echo $css_path ?>images/b_marketing.gif" alt="Marketing" name="publicidade" width="32" height="32" hspace="5" border="0"></a></td>
				<?php endif ; ?>

				<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/knowledge.php" ) ): ?>
				<td><a href="<?php echo $BASE_URL ?>/admin/traffic/knowledge.php" onMouseOut="rollOut(9)" onMouseOver="rollOver(9)"><img src="<?php echo $css_path ?>images/b_knowledge.gif" alt="Knowlege Base" name="knowledge" width="32" height="32" hspace="5" border="0"></a></td>
				<?php endif ; ?>

				<td><img src="<?php echo $css_path ?>images/spacer.gif" width="5" height="1"></td>
			  </tr>
			</table></td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td width="100%"><img src="<?php echo $css_path ?>images/bg_corner_bot.gif" width="121" height="47"></td>
		  <td width="427" valign="top" class="bgNav"><table width="427" border="0" cellspacing="0" cellpadding="1">
			  <tr>
				<td height="24" align="center" class="nav"><b><div style="position:relative" id="navigation">&nbsp;</div></b></td>
			  </tr>
			</table></td>
		  <td width="10"><img src="<?php echo $css_path ?>images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td valign="top" class="bg"> 

<!-- **** Start of the page body area **** -->