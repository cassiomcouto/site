// when viewing transcripts, we need set this or Javascript error
var loaded = 1 ;

var nav_start = '<table width="427" border="0" cellspacing="0" cellpadding="3"><tr><td align="center" class="nav">';
var nav_end = '</td></tr></table>';

var nav = new Array();
nav[0] = 'Operator Administration Area';
nav[1] = 'Canned Responses';
nav[2] = 'Canned Commands';
nav[3] = 'Preferences and Password Change';
nav[4] = 'Initiate Intro Message';
nav[5] = rating_str;
nav[6] = 'Block SPAM IPs';

var button = new Array();
button[0] = '';
button[1] = 'comments';
button[2] = 'commands';
button[3] = 'prefs';
button[4] = 'initiate';
button[5] = '' ;
button[6] = '' ;

// Onload
function init(){
	rollOver(section);					// Setup navigation
	if(section>0) sE(gE('navBack'));	// Show back button
}

// Rollover and navigation change
// s = section number; n = button name
function rollOver(s){
	if(button[s] != '' ) MM_swapImage(button[s],'','../images/b_'+button[s]+'-over.gif',1);
	
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