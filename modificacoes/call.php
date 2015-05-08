<?php
include("../init.php");
$qt = $_POST['qtda'];
$cep = $_POST['cep'];
$de = $_POST['de'];

$id_produto = $_POST['id_produto'];
//echo $id_produto;
$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
$g = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerGroup();

//print_r($g);

$valor = $_POST['valor']-(($_POST['valor']/100)*$g['discount']);

$uni = $_POST['peso'];
$loja = GetConfig('ShopPath');
$peso = $uni*$qt;
// inicio das funcoes
function deondetue($cep) {
$url = "http://www.mdconline.com.br/Webservices/WSCEP/servicoCEP.asp?txtCEPEnviado=$cep";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);
$html1 = curl_exec ($ch);
curl_close ($ch);

$html = explode('<CIDADE>', $html1);
$html2 = explode('</CIDADE>', $html[1]);

$htmld = explode('<UF>', $html1);
$htmld2 = explode('</UF>', $htmld[1]);
if(!empty($html2[0])) {
return $html2[0]." - ".$htmld2[0];
} else {
return "Cep Desconhecido";
}
}
function correios($de,$para,$peso,$valor,$tipo) {
$valor = str_replace('.',',',$valor);

	$nCdEmpresa='09119132';
	$sDsSenha='08677327';
	
	
$correios ="http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPreco?"
."nCdEmpresa=".$nCdEmpresa."&"
."sDsSenha=".$sDsSenha."&"
."sCepOrigem=".$de."&"
."sCepDestino=".$para."&"
."nVlPeso=".$peso."&"
."nCdFormato=1&"
."nVlComprimento=20&"
."nVlAltura=20&"
."nVlLargura=20&"
."sCdMaoPropria=N&"
."nVlValorDeclarado=".$valor."&"
."sCdAvisoRecebimento=N&"
."nCdServico=".$tipo."&"
."nVlDiametro=0&"
."StrRetorno=xml";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $correios);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);
$html1 = curl_exec ($ch);
curl_close ($ch);
//pega o valor
$html = explode('<Valor>', $html1);
$html2 = explode('</Valor>', $html[1]);
$total = str_replace(',','.',$html2[0]);
//pega o prazo
$pra = explode('<PrazoEntrega>', $html1);
$prazo = explode('</PrazoEntrega>', $pra[1]);

return array('valor'=>$total,'prazo'=>$prazo[0]);

}
function frete($mod,$var){
$is = "select * from [|PREFIX|]shipping_vars where modulename='".$mod."' and variablename='".$var."'";
$es = $GLOBALS['ISC_CLASS_DB']->Query($is);
$dad = $GLOBALS['ISC_CLASS_DB']->Fetch($es);
return $dad['variableval'];
}




if (!eregi("^[0-9]{5}-[0-9]{3}$", $cep)) {
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Digite um CEP valido!!</b>';
} else {
?>

<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tbody>
<tr style="" bgcolor="#d6d6d6"><td colspan='4' style="border: 2px solid rgb(173, 173, 173); padding: 7px; margin-bottom: 10px;">
<div> Destino: <b><?php echo deondetue($cep);?></b></div>
</td></tr>

<tr style="" bgcolor="#d6d6d6"><td style="border-bottom: 1px solid silver;">&nbsp;</td><td style="border-bottom: 1px solid silver;"><strong>Forma de envio</strong></td><td style="border-bottom: 1px solid silver;" align="right"><font color="blue"><strong>Valor&nbsp;</strong></font></td><td style="border-bottom: 1px solid silver;"><strong>&nbsp;Detalhes</strong></td></tr>

<?php

$i = "select * from [|PREFIX|]module_vars where modulename='addon_simularfrete' and variablename='tipos'";
$e = $GLOBALS['ISC_CLASS_DB']->Query($i);
while($re = $GLOBALS['ISC_CLASS_DB']->Fetch($e)) {
switch($re['variableval']){
case 'pac':
$var = correios($de,$cep,$peso,$valor,41106);

if($var['valor']!='0'){
$var1 = "R$&nbsp;".$var['valor'];
}else{
$var1 = 'Indinsponivel';
}
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/pac.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;PAC</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>'.$var1.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;Prazo de entrega &eacute; de at&eacute; '.$var['prazo'].' dias &uacute;teis.</td>
</tr>';
break;

case 'sedex':
$var = correios($de,$cep,$peso,$valor,40010);
if($var['valor']!='0'){
$var1 = "R$&nbsp;".$var['valor'];
}else{
$var1 = 'Indinsponivel';
}
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/sedex.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;Sedex</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>'.$var1.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;Prazo de entrega &eacute; de at&eacute; '.$var['prazo'].' dias &uacute;teis.</td>
</tr>';

break;

case 'esedex':
$var = correios($de,$cep,$peso,$valor,81019);
if($var['valor']!='0'){
$var1 = "R$&nbsp;".$var['valor'];
}else{
$var1 = 'Indinsponivel';
}
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/esedex.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;eSedex</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>'.$var1.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;Prazo de entrega &eacute; de at&eacute; '.$var['prazo'].' dias &uacute;teis.</td>
</tr>';
break;

case 'acobrar':
$var = correios($de,$cep,$peso,$valor,40045);
if($var['valor']!='0'){
$var1 = "R$&nbsp;".$var['valor'];
}else{
$var1 = 'Indinsponivel';
}
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/sedex_acobrar.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;Sedex a Cobrar</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>'.$var1.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;Prazo de entrega &eacute; de at&eacute; '.$var['prazo'].' dias &uacute;teis.</td>
</tr>';
break;

case 'porkg':
break;

case 'fixo':
$var = frete('shipping_transportax','valorentrega');
$nom = frete('shipping_transportax','displayname');
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/loja.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;'.$nom.'</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>R$&nbsp;'.$var.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;Nossa loja leva o produto até você.</td>
</tr>';
break;

case 'direct':
$varr = frete('shipping_directlog','ide');
$nom = frete('shipping_directlog','displayname');
$cep = str_replace(' ','',$cep);
$cep = str_replace('-','',$cep);
$url = "http://www.directlog.com.br/frete/pega_frete.asp?cdrem=".$varr."&peso=".$peso."&cep=".$cep."&vltot=".$valor;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);
$html = curl_exec ($ch);
curl_close ($ch);
$html1 = str_replace(",", "", $html);
//$html1 = str_replace(".", ",", $html1);
$total =number_format($html1, 2, '.', '');
// Create a quote object
if($total!='0'){
$var = "R$&nbsp;".$total;
}else{
$var = 'Indinsponivel';
}
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/log.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;'.$nom.'</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>'.$var.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;A Transportadora leva o produto até você.</td>
</tr>';
break;

case 'bras':
$cnpj = frete('shipping_braspress','cnpj');
$num = frete('shipping_braspress','numempresa');
$ceps = frete('shipping_braspress','cep');
$tip = frete('shipping_braspress','tipodefrete');
$cep = str_replace(' ','',$cep);
$cep = str_replace('-','',$cep);
$cpf = "00000000000";
$nom = frete('shipping_braspress','displayname');
$url = "http://tracking.braspress.com.br/trk/trkisapi.dll/PgCalcFrete_XML?param=$cnpj,$num,$ceps,$cep,$cnpj,$cpf,$tip,$peso,$valor,$qt";
//echo $url;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);
$html = curl_exec ($ch);
curl_close ($ch);

$htmlU = explode('<PRAZO>', $html);
$html2U = explode('</PRAZO>', $htmlU[1]);

$html = explode('<TOTALFRETE>', $html);
$html2 = explode('</TOTALFRETE>', $html[1]);

$total = $html2[0];
$prazo = $html2U[0];
// Create a quote object
if($total!='0'){
$var = "R$&nbsp;".$total;
}else{
$var = 'Indinsponivel';
}
echo '<tr class="linhas" bgcolor="#f5f5f5" valign="top">
<td width="10%"><b>
<img src="'.$loja.'/modificacoes/log.gif">
</b>
</td>
<td width="30%" valign="Middle"><b>&nbsp;'.$nom.'</b></td>
<td align="right" width="20%" valign="Middle"><font color="blue"><strong>'.$var.'&nbsp;</strong></font></td>
<td width="40%" valign="Middle">&nbsp;O prazo de entrega é de até '.$prazo.' dias.</td>
</tr>';
break;

}

echo "<br>";

}
?>


</tbody></table></td></tr></tbody>

<?php
}
?>
