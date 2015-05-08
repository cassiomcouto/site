<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Administração Loja Virtual - Login de Acesso</title>
<meta name="robots" content="noindex, nofollow" />
<link rel="SHORTCUT ICON" href="favicon.ico" />
<!--modificacoes-->
<script type="text/javascript" src="modificacoes/keyboard.js"></script>
<link rel="stylesheet" href="modificacoes/keyboard.css" type="text/css" />
<!--fim-->

<script type="text/javascript">
function entrar(){
document.getElementById("msg").style.display = "";
document.getElementById("msg_texto").innerHTML = "Validando...";
//window.location = '%%GLOBAL_ShopPath%%/admin/index.php?ToDo=processLogin';
var url = "%%GLOBAL_ShopPath%%/admin/index.php?ToDo=processLogin";
var params = "lorem=ipsum&name=binny";
http.open("POST", url, true);
http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
http.setRequestHeader("Content-length", params.length);
http.setRequestHeader("Connection", "close");

http.onreadystatechange = function() {//Call a function when the state changes.
	if(http.readyState == 4 && http.status == 200) {
		alert(http.responseText);
	}
}
http.send(params);

return false;
}
</script>
<script language=JavaScript>
function keypresed() {
alert('Teclado Desabilitado, Utilize o Teclado Virtual para Logar !');
}

document.onkeydown=keypresed;
document.onmousedown=click;
</script>

<style type="text/css">
	body { padding:0; margin:0; background:#B7B7B7 url(modificacoes/login_r1_c1.jpg) repeat-x; font-size:12px; font-family:Tahoma; }
	.frm_login { color:#FFF; }
  .campo_texto  { font-size:12px; font-family:Tahoma;border:1px solid #51A8FF; padding:3px; background-image:url(modificacoes/fundo_input.gif); background-repeat:repeat-x; background-position:top; }
	#msg_texto { font-size:12px; font-family:Tahoma; font-weight:bold; color:#FFF;  }
	a:link,a:visited { color: #FFFFFF; text-decoration: none; }
	a:hover { color: #FFFFFF; text-decoration: underline; }
</style>
</head>
<body>
<form action="%%GLOBAL_ShopPath%%/admin/index.php?ToDo=%%GLOBAL_SubmitAction%%" method="post" enctype="multipart/form-data" name="frmlogin" id="frmlogin" onSubmit="return entrar();">
  <table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center"> <a href="http://sistemaslx.com.br" target="_blanck"><img src="modificacoes/logo_nkt-info.png" border="0" alt=" Sistemas LX - Sua Web Solução Completa"  title="Sistemas LX - Sua Web Solução Completa"></a></td>
    </tr>
    <tr>
      <td height="100" align="center" background="modificacoes/formloginbg.png" style="background-position:center;background-repeat:no-repeat;">
      <table border="0" cellspacing="5" cellpadding="0" class="frm_login">
        <tr>
          <td align="left"><b>Usu&aacute;rio:</b></td>
          <td rowspan="2" align="left">&nbsp;</td>
          <td align="left"><b>Senha:</b></td>
        </tr>
        <tr>
          <td align="left"><input autocomplete="off" type="text" name="username" id="username" class="keyboardInput" size=25></td>
          <td align="left"><input autocomplete="off" type="password" name="password" id="password" class="keyboardInput" value="%%GLOBAL_Password%%"></td>
        </tr>
      </table></td>
    </tr>
    <tr id="msg" style="display:">
      <td align="center" id="msg_texto" height="10" valign="top">%%GLOBAL_Message%% Para sua seguran&ccedil;a utilize o teclado virtual.<br></td>
	  
    </tr>
    <tr>
      <td align="center"><br><input type="image" name="bt_entrar" src="modificacoes/bt_entrar.png" alt="Entrar" title="Entrar">&nbsp;&nbsp;
     <a href="index.php?ToDo=forgotPass" target="_blank"><img src="modificacoes/bt_recuperar.png" border="0" alt="Recuperar Senha"  title="Recuperar Senha"></a></td>
      
      
      
   
      
    </tr>
      </table>
      </br></br><p/> 
      <center><b><font color="#FFFFFF">&Aacute;rea Restrita</font></b></center></br>
      <center><b><font color="#FFFFFF">Administra&ccedil;&atilde;o - Mega Loja Virtual Evollution V 10  - 2012</font></b></center> </br>

</form>
</body>
</html>
