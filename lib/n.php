<?php
include("../init.php");
function ValorProduto($produto) {
		$query = "SELECT * FROM [|PREFIX|]products where productid=".$produto;
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		return $GLOBALS['ISC_CLASS_DB']->Fetch($result);
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

function simulador_de_rodape($produto){

$ler = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'rodape1' or variablename = 'rodape2'";
$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);
$parcelador = "";
while ($s = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {

echo $s['variableval'];
echo '<br>';


//inicio do switch
switch($s['variableval']) {

case 'deposito': //deposito
$ativo = GetModuleVariable('checkout_deposito','is_setup');
$desc = GetModuleVariable('checkout_deposito','desconto');
$nome = GetModuleVariable('checkout_deposito','displayname');
if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg = "<b>".$preco."</b> a Vista no <b>Deposito</b>";
} else {
$valven = ($pro['prodcalculatedprice']/100)*$desc;
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice']-$valven, 1, 0);
$msg = "<b>".$preco."</b> com <b>".$desc."%</b> de Desconto no <b>Dep&oacute;sito</b>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'<br>';
//fim do codigo de parcelamento
}
break; // fim deposito

case 'cheque':
$ativo = GetModuleVariable('checkout_cheque','is_setup');
$juros = GetModuleVariable('checkout_cheque','juros');
$nome = GetModuleVariable('checkout_cheque','displayname');
$div = GetModuleVariable('checkout_cheque','dividir');
$jde = GetModuleVariable('checkout_cheque','jurosde');
$pmin = GetModuleVariable('checkout_cheque','parmin');
if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
if($juros<=0 OR empty($juros)){
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg = "<b>1x</b> de <b> ".$preco."</b> sem juros no <b>cheque</b>";
} else {
$msg = '';
$msg1 = '';
$splits = (int) ($pro['prodcalculatedprice']/$pmin);
if($splits<=$div){
$div = $splits;
}else{
$div = $div;
}
for ($j=1;$j<=$div;$j++) {
if($div==$j){
if ($jde<=$j and $jde<='50') {
$valven = ($pro['prodcalculatedprice']/100)*$juros;
$msg1 .= "<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice(($pro['prodcalculatedprice']+$valven)/$j, 1, 0)."</b> com juros no <b>cheque</b>";
}else{
$msg .= "<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($pro['prodcalculatedprice']/$j, 1, 0)."</b>sem juros no <b>cheque</b>";
}
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'<br>';
//fim do codigo de parcelamento
}
break;

case 'boleto': //boleto
$desc = GetModuleVariable('addon_parcelas','descboleto');

//verifica o desconto
$pro = ValorProduto($produto);
if($desc<=0){
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg = "<b>1x</b> de <b> ".$preco."</b> no <b>boleto</b>";
} else {
$valven = ($pro['prodcalculatedprice']/100)*$desc;
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice']-$valven, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> com <b>".$desc."%</b> de desconto no <b>boleto</b>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
break; // fim boleto

case 'pagseguro':
$ativo = GetModuleVariable('checkout_pagseguro','is_setup');
$juross = GetModuleVariable('checkout_pagseguro','acrecimo');
$nome = GetModuleVariable('checkout_pagseguro','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>Pagseguro</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>Pagseguro</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'<br>';
//fim do codigo de parcelamento
}
break;

case 'pagdigital':
$ativo = GetModuleVariable('checkout_pagamentodigital','is_setup');
$juross = GetModuleVariable('checkout_pagamentodigital','acrecimo');
$nome = GetModuleVariable('checkout_pagamentodigital','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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

for($j=1; $j<=$div;$j++) {
if($div==$j){

$parcelas = jurosComposto($valor, 1.99, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

$op = GetModuleVariable('checkout_pagamentodigital','jurosde');
if($j==1 OR $op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>Pagamento Digital</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>Pagamento Digital</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'moip':
$ativo = GetModuleVariable('checkout_moip','is_setup');
$juross = GetModuleVariable('checkout_moip','acrecimo');
$nome = GetModuleVariable('checkout_moip','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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
if($div==$j){
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
$op = GetModuleVariable('checkout_moip','jurosde');
if($j==1 OR $op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>moip</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>moip</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'dinheiromail':
$ativo = GetModuleVariable('checkout_dinheiromail','is_setup');
$juross = GetModuleVariable('checkout_dinheiromail','acrecimo');
$nome = GetModuleVariable('checkout_dinheiromail','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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

for($j=1; $j<=$div;$j++) {

if($div==$j){
$parcelas = jurosSimples($valor, 1.99, $j);
$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');
$op = GetModuleVariable('checkout_dinheiromail','jurosde');
if($j==1 OR $op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>dinheiromail</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>dinheiromail</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'paypal':
$ativo = GetModuleVariable('checkout_paypal','is_setup');
$desc = GetModuleVariable('checkout_paypal','desconto');
$nome = GetModuleVariable('checkout_paypal','displayname');
if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> a vista no <b>paypal</b>";
} else {
$valven = ($pro['prodcalculatedprice']/100)*$desc;
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice']-$valven, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> com <b>".$desc."%</b> de desconto no <b>paypal</b>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;

case 'visacredito':
$ativo = GetModuleVariable('checkout_visanet','is_setup');
$nome = GetModuleVariable('checkout_visanet','displayname');
$div = GetModuleVariable('checkout_visanet','div');
$juross = '0';
$taxa = GetModuleVariable('checkout_visanet','juros');
$jt = GetModuleVariable('checkout_visanet','tipojuros');

$pm = GetModuleVariable('checkout_visanet','parcelamin');

if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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
if($div==$j){
if($jt==0)
$parcelas = jurosSimples($valor, $taxa, $j);
else
$parcelas = jurosComposto($valor, $taxa, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

$op = GetModuleVariable('checkout_visanet','jurosde');
if($op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>visa</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>visa</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'visadebito':
$ativo = GetModuleVariable('checkout_visanet','is_setup');
$nome = GetModuleVariable('checkout_visanet','displayname');
$desc = GetModuleVariable('checkout_visanet','desconto');

if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> a vista no <b>visa electron</b>";
} else {
$valven = ($pro['prodcalculatedprice']/100)*$desc;
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice']-$valven, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> com <b>".$desc."%</b> de desconto no <b>visa electron</b>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;

case 'master':
$ativo = GetModuleVariable('checkout_mastercard','is_setup');
$nome = GetModuleVariable('checkout_mastercard','displayname');
$div = GetModuleVariable('checkout_mastercard','div');
$juross = '0';
$taxa = GetModuleVariable('checkout_mastercard','juros');
$jt = GetModuleVariable('checkout_mastercard','tipojuros');

$pm = GetModuleVariable('checkout_mastercard','parcelamin');

if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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
if($div==$j){
if($jt==0)
$parcelas = jurosSimples($valor, $taxa, $j);
else
$parcelas = jurosComposto($valor, $taxa, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

$op = GetModuleVariable('checkout_mastercard','jurosde');
if($op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>mastercard</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>mastercard</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'dinners':
$ativo = GetModuleVariable('checkout_dinners','is_setup');
$nome = GetModuleVariable('checkout_dinners','displayname');
$div = GetModuleVariable('checkout_dinners','div');
$juross = '0';
$taxa = GetModuleVariable('checkout_dinners','juros');
$jt = GetModuleVariable('checkout_dinners','tipojuros');

$pm = GetModuleVariable('checkout_dinners','parcelamin');

if(!empty($ativo)) {
//verifica o juros
$pro = ValorProduto($produto);
$valor = $pro['prodcalculatedprice'];
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
if($div==$j){
if($jt==0)
$parcelas = jurosSimples($valor, $taxa, $j);
else
$parcelas = jurosComposto($valor, $taxa, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

$op = GetModuleVariable('checkout_dinners','jurosde');
if($op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>diners</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>diners</b>";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'sps1':
$ativo = GetModuleVariable('checkout_spsbradesco','is_setup');
$nome = GetModuleVariable('checkout_spsbradesco','displayname');
$boleto = GetModuleVariable('checkout_spsbradesco','pagboletos');
$facil = GetModuleVariable('checkout_spsbradesco','pagfacil');
$finan = GetModuleVariable('checkout_spsbradesco','pagfinan');
$trans = GetModuleVariable('checkout_spsbradesco','pagtrans');
if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>bradesco online</b>";



//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;

case 'sps':
$ativo = GetModuleVariable('checkout_spsbradesco','is_setup');
$nome = GetModuleVariable('checkout_spsbradesco','displayname');
$boleto = GetModuleVariable('checkout_spsbradesco','pagboletos');
$facil = GetModuleVariable('checkout_spsbradesco','pagfacil');
$finan = GetModuleVariable('checkout_spsbradesco','pagfinan');
$trans = GetModuleVariable('checkout_spsbradesco','pagtrans');
if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
$msg = "";
$pro = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>bradesco online</b>";
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;


case 'bbofice':
$ativo = GetModuleVariable('checkout_bbcomercio','is_setup');
$nome = GetModuleVariable('checkout_bbcomercio','displayname');
$boleto = '';
$facil = '';
$finan = '';
$trans = '';
if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>bb ofice bank</b>";


//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;


case 'shopline':
$ativo = GetModuleVariable('checkout_itaushopline','is_setup');
$nome = GetModuleVariable('checkout_itaushopline','displayname');
$boleto = '';
$facil = '';
$finan = '';
$trans = '';
if(!empty($ativo)) {
//verifica o desconto
$pro = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($pro['prodcalculatedprice'], 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>itau shopline</b>";
//inicio do codigo do parcelamento
$parcelador .= $msg.'</fbr>';
//fim do codigo de parcelamento
}
break;

}

}
return $parcelador;
}
echo simulador_de_rodape(2);
?>