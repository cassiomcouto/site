<?php
global $itemId;
$itemId = $_GET['pedido'];
include "../../../dados.php";
function jurosSimples($valor, $taxa, $parcelas) {
$taxa = $taxa/100;
$m = $valor * (1 + $taxa * $parcelas);
$valParcela = $m/$parcelas;
return $valParcela;
}

function jurosComposto($valor, $taxa, $parcelas) {
$taxa = $taxa/100;
$valParcela = $valor * pow((1 + $taxa), $parcelas);
$valParcela = $valParcela/$parcelas;
return $valParcela;
}
$valor = $fetch_order['ordgatewayamount'];
$help = "";
$div = corinthias("checkout_mastercard","div");
$juross = '0';
$taxa = corinthias("checkout_mastercard","juros");
$jt = corinthias("checkout_mastercard","tipojuros");
$pm = corinthias("checkout_mastercard","parcelamin");
if($juross<=0 OR empty($juross)){
$valor = $valor;
} else {
$valor = (($valor/100)*$juross)+$valor;
}
$msg = '';
$msg1 = '';
$splitss = (int) ($valor/$pm);
if($splitss<=$div){
$div = $splitss;
}else{
$div = $div;
}

for($j=1; $j<=$div;$j++) {

if($jt==0){
$parcelas = jurosSimples($valor, $taxa, $j);
}else{
$parcelas = jurosComposto($valor, $taxa, $j);
}
$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');
$op = corinthias("checkout_mastercard","jurosde");

if($op>=$j) {
$msg .="<a href=\"".$urlloja."/modules/checkout/mastercard/pagar.php?pedido=". $fetch_order['orderid']."&pacela=".$j."&key=c2VtanVyb3M=\"><font size='3'><b>".$j."x</b> de <b>".number_format($valors/$j, 2, '.', ',')."</b> s/ juros</font></a><br>";
}else{
$msg1 .="<a href=\"".$urlloja."/modules/checkout/mastercard/pagar.php?pedido=". $fetch_order['orderid']."&pacela=".$j."&key=Y29tanVyb3M=\"><font size='3'><b>".$j."x</b> de <b>".number_format($parcelas, 2, '.', ',')."</b> c/ juros</font></a><br>";
}
}
$help .= "<div class='FloatLeft'><img src='".$urlloja."/modules/checkout/mastercard/images/final.gif'><br><br>";
$help .= '<div class="eg-bar">
<table width="100%" border="0">
<tr>
<td width="100%">'.$msg.''.$msg1.'</td>
</tr>
</table>
</div>';
echo $help;
?>

