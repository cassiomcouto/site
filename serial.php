<?php
// ponha o dominio onde voce ira instalar a loja com www. Ex: www.site.com.br
$domain='www.evollution.inf.br';

// nao modificar
$domain=substr($domain,4);
$nfe = "16";
////////////////////

// uso: 8 = premium, 4 = super, 2 = mega, 1 = mini
$versao = "8";

// ponha o numero de produtos para loja onde 0 e ilimitados
$produtos = "0";

// ponha o numero de usuarios para loja onde 0 e ilimitados
$usuarios = "0";

// ponha a data de expirracao da loja mes, dia e ano
$vencimento = mktime(0, 0, 0, 5, 24, 4000);

$serial = strrev(base64_encode('SERIAL'.base64_encode(pack("CCVvvH*", $nfe,$versao,$vencimento,$usuarios,$produtos,md5($domain))).'2012'));
$serial = str_replace('==','EVO',$serial);
$serial = str_replace('1','%',$serial);
$serial = str_replace('2','&',$serial);
$serial = str_replace('3','#',$serial);
$serial = str_replace('4','@',$serial);
echo '<body background="http://www.evollution.inf.br/webloja/fundoserial.jpg"><p align="center">&nbsp;<p align="center">&nbsp;<p align="center">&nbsp;<p align="center"><a target="_blank" href="http://www.evollution.inf.br"><img src="http://www.evollution.inf.br/webloja/logo.png"></a><p align="center"><b><font color="#FFFFFF">Serial da Loja:</font></b><p align="center"> '.$serial. "<br><br></p>";


?>
