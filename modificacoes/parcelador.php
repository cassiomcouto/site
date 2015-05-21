<?php
function ValorProduto($valorp) {
		return $valorp;
}
		
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

function deposito($valor){
$ativo = GetModuleVariable('checkout_deposito','is_setup');
$desc = GetModuleVariable('checkout_deposito','desconto');
$nome = GetModuleVariable('checkout_deposito','displayname');
if(!empty($ativo)) {
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($valor, 1, 0);
$msg = "<b>".$preco."</b> a Vista no <b>Deposito</b><br>";
} else {
$valven = ($valor/100)*$desc;
$preco = CurrencyConvertFormatPrice($valor-$valven, 1, 0);
$msg = "<b>".$preco."</b> com <b>".$desc."%</b> de Desconto no <b>Deposito</b><br>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'<br>';
//fim do codigo de parcelamento
}
return $parcelador;
}


function pagseguro($valor){
$ativo = GetModuleVariable('checkout_pagseguro','is_setup');
$juross = GetModuleVariable('checkout_pagseguro','acrecimo');
$nome = GetModuleVariable('checkout_pagseguro','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
$valor = $valor;
if($juross<=0 OR empty($juross)){
$valor = $valor;
} else {
$valor = (($valor/100)*$juross)+$valor;
}

$msg = '';
$msg1 = '';
$splitss = (int) ($valor/5);
if($splitss<=12){
$div = $splitss;
}else{
$div = 12;
}
//echo $div."<br>";
for($j=1; $j<=$div;$j++) {
$cf = pow((1 + $taxa), $j);
$cf = (1 / $cf);
$cf = (1 - $cf);
$cf = ($taxa / $cf);
//echo $cf."<br>";
$parcelas = ($valor*$cf);
//echo $parcela."<br>";
$parcelas = number_format($parcelas, 2, '.', '');
//echo $parcela."<br>";
$valors = number_format($valor, 2, '.', '');
$op = GetModuleVariable('checkout_pagseguro','jurosde');
if($div==$j){
if($j==1 OR $op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>pagseguro</b><br>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>pagseguro</b><br>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'<br>';
//fim do codigo de parcelamento
}
return $parcelador;
}

function calculador($id) {

$query = sprintf("select * from [|PREFIX|]products where productid = '$id'");
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
$vari = $row['prodcallforpricinglabel'];
$valor =number_format($row['prodcalculatedprice'], 2, '.', '');

$dc = deposito($valor);
$dcs = pagseguro($valor);

$ht = $dc."".$dcs;

return $ht;
}

?>

