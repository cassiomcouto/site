<?php
	$mac = 0 ;
	$browser_os = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "" ;
	$action = ( isset( $_GET['action'] ) && $_GET['action'] ) ? $_GET['action'] : "" ;

	$ip = $_SERVER['REMOTE_ADDR'] ;

	if ( preg_match( "/Mac( |_)/", $browser_os ) )
		$mac = 1 ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Por Favor Atualize seu Navegador.</title>
<script type="text/javascript" src="js/xmlhttp.js"></script>
</head>
<body bgColor="#FFFFFF" text="#000000" OnLoad="init()">
<font size=2 face="arial">
	Your browser OS: <font color="#FF0000"><b><?php echo $browser_os ?></b></font>
	<br>
	<?php if ( $mac && ( $action == "no" ) ): ?>
	<h3>Error: Navegador não Suportado</h3>
	Please consider using the following browsers for Mac:
	<ul>
		<li> <a href="http://www.apple.com/safari/" target="new">Safari >= 1.2</a>
		<li> <a href="http://www.mozilla.org/products/firefox/" target="new">Firefox >= 1.0</a>
		<li> <a href="http://channels.netscape.com/ns/browsers/download.jsp" target="new">Netscape >= 7.0</a>
	</ul>

	<?php elseif ( $action == "noxml" ): ?>
	<h3>Error: Browser component upgrade needed</h3>

	It looks like your browser is current, but a crucial component needs to be updated.  Please follow the below URL to upgrade your MSXML libraries of your browser.
	<ul>
		<li> <a href="http://www.microsoft.com/downloads/details.aspx?FamilyID=3144b72b-b4f2-46da-b4b6-c5d7485f2b42&DisplayLang=en" target="new">MSXML HTTP parser</a>
	</ul>

	<?php elseif ( $action == "no" ): ?>
	<h3>Navegador Não Suportado</h3>
	Atualize seu Navegador:
	<ul>
		<li> <a href="http://www.microsoft.com/windows/ie/downloads/default.mspx" target="new">Internet Explorer >= 6.0</a>
		<li> <a href="http://www.mozilla.org/products/firefox/" target="new">Firefox >= 1.0</a>
		<li> <a href="http://channels.netscape.com/ns/browsers/download.jsp" target="new">Netscape >= 7.0</a>
	</ul>

	<?php elseif ( $action == "ok" ): ?>
	<h3>Navegador Suportado</h3>
	Navegador OK!

	<?php else: ?>
	<h3>Detectando...</h3>

	<?php endif ; ?>
	<br><br>
	<form><input type="button" OnClick="window.close()" value="Fechar"></form>
</body>
</html>