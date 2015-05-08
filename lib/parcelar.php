<?php
function ValorProduto($produto) {
		$query = "SELECT * FROM [|PREFIX|]products where productid=".$produto;
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$a = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
		
$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
$g = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerGroup();

//print_r($g);

$valor = $a['prodcalculatedprice']-(($a['prodcalculatedprice']/100)*$g['discount']);

return $valor;

}

function FreteTipo($produto) {
		$query = "SELECT * FROM [|PREFIX|]products where productid=".$produto;
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$dados = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
		if($dados['prodfreeshipping']==1){
		$d = '<img src="%%GLOBAL_AppPath%%/modificacoes/frete_gratis.gif" border="0">';
		return $d;
		}else{
		$s = '<img src="%%GLOBAL_AppPath%%/modificacoes/frete_pago.gif" border="0">';
		return $s;
		}
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



$ler = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'rodape1' or variablename = 'rodape2' order by variablename asc";
$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);
$parcelador = "";
while ($s = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {

//inicio do switch
switch($s['variableval']) {


case 'shopline':
$ativo = GetModuleVariable('checkout_itaushopline','is_setup');
$nome = GetModuleVariable('checkout_itaushopline','displayname');
$boleto = '';
$facil = '';
$finan = '';
$trans = '';
if(!empty($ativo)) {
//verifica o desconto
$valordoproduto = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>itau shopline</b>";
//inicio do codigo do parcelamento
$parcelador .= $msg.'</fbr>';
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
$valordoproduto = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>bb ofice bank</b>";


//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;

case 'mercadopago';
$ativo = GetModuleVariable('checkout_mercadopago','is_setup');
$juross = GetModuleVariable('checkout_mercadopago','acrecimo');
$nome = GetModuleVariable('checkout_mercadopago','displayname');
if(!empty($ativo)) {
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
if($juross<=0 OR empty($juross)){
$valor = $valor;
} else {
$valor = (($valor/100)*$juross)+$valor;
}
$parmin = 10;
///////////////////////////////////////
$msg = '';
//////////////////////////////////////
$splitss = (int) ($valor/$parmin);
if($splitss<=18){
/////////////////
if($splitss==0 OR $splitss==1 OR $splitss==2){
$juros = 1;
$div = 1;
}
if($splitss==3 OR $splitss==4 OR $splitss==5){
$juros = 0.3533;
$div = 3;
}
if($splitss==6 OR $splitss==7 OR $splitss==8){
$juros = 0.18081;
$div = 6;
}
if($splitss==9 OR $splitss==10 OR $splitss==11){
$juros = 0.123877;
$div = 9;
}
if($splitss==12 OR $splitss==13 OR $splitss==14){
$juros = 0.094575;
$div = 12;
}
if($splitss==15 OR $splitss==16 OR $splitss==17){
$juros = 0.077993;
$div = 15;
}
if($splitss==18 OR $splitss==19 OR $splitss==20){
$juros = 0.066661;
$div = 18;
}
/////////////////
}else{
$juros = 0.066661;
$div = 18;
}

$msg .="Em <b>".$div."x</b> de <b>".CurrencyConvertFormatPrice(($valor*$juros), 1, 0)."</b> no <b>mercadopago</b> ";

$parcelador .= $msg.'<br>';
///////////////////////////////////////
}
break;

case 'cielo':
$ativo = GetModuleVariable('checkout_cielo','is_setup');
$nome = 'Cat&atilde;o de Credito';
$div = GetModuleVariable('checkout_cielo','div');
$juross = '0';
$taxa = GetModuleVariable('checkout_cielo','juros');
$jt = 0;
$pm = GetModuleVariable('checkout_cielo','parcelamin');

if(!empty($ativo)) {
//verifica o juros
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=$pm){
$div = 1;
}

for($j=1; $j<=$div;$j++) {
if($div==$j){
if($jt==0)
$parcelas = jurosSimples($valor, $taxa, $j);
else
$parcelas = jurosComposto($valor, $taxa, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

$op = GetModuleVariable('checkout_cielo','jurosde');
if($op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> no <b>cart&atilde;o de credito</b>";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> no <b>cart&atilde;o de credito</b>";
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
$valordoproduto = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>bradesco online</b>";



//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
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
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=$pm){
$div = 1;
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
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=$pm){
$div = 1;
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


case 'visadebito':
$ativo = GetModuleVariable('checkout_visanet','is_setup');
$nome = GetModuleVariable('checkout_visanet','displayname');
$desc = GetModuleVariable('checkout_visanet','desconto');

if(!empty($ativo)) {
//verifica o desconto
$valordoproduto = ValorProduto($produto);
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> a vista no <b>visa electron</b>";
} else {
$valven = ($valordoproduto/100)*$desc;
$preco = CurrencyConvertFormatPrice($valordoproduto-$valven, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> com <b>".$desc."%</b> de desconto no <b>visa electron</b>";
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
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=$pm){
$div = 1;
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




case 'paypal':
$ativo = GetModuleVariable('checkout_paypalbrasil','is_setup');
$desc = GetModuleVariable('checkout_paypalbrasil','desconto');
$nome = GetModuleVariable('checkout_paypalbrasil','displayname');
if(!empty($ativo)) {
//verifica o desconto
$valordoproduto = ValorProduto($produto);
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> a vista no <b>paypal</b>";
} else {
$valven = ($valordoproduto/100)*$desc;
$preco = CurrencyConvertFormatPrice($valordoproduto-$valven, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> com <b>".$desc."%</b> de desconto no <b>paypal</b>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
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
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=5){
$div = 1;
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



case 'moip':
$ativo = GetModuleVariable('checkout_moip','is_setup');
$juross = GetModuleVariable('checkout_moip','acrecimo');
$nome = GetModuleVariable('checkout_moip','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=5){
$div = 1;
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

case 'bcash':
$ativo = GetModuleVariable('checkout_bcash','is_setup');
$juross = GetModuleVariable('checkout_bcash','acrecimo');
$nome = GetModuleVariable('checkout_bcash','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=5){
$div = 1;
}

for($j=1; $j<=$div;$j++) {
if($div==$j){

$parcelas = jurosComposto($valor, 1.99, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

$op = GetModuleVariable('checkout_bcash','jurosde');
if($j==1 OR $op>=$j) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>BCash</b> ";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>BCash</b> 	";
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'</br>';
//fim do codigo de parcelamento
}
break;

case 'pagseguro':
$ativo = GetModuleVariable('checkout_pagseguro','is_setup');
$juross = GetModuleVariable('checkout_pagseguro','acrecimo');
$nome = GetModuleVariable('checkout_pagseguro','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$valordoproduto = ValorProduto($produto);
$valor = $valordoproduto;
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
if($valor<=5){
$div = 1;
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
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros no <b>pagseguro</b> ";
}else{
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros no <b>pagseguro</b> ";
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
$valordoproduto = ValorProduto($produto);
if($desc<=0){
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg = "<b>1x</b> de <b> ".$preco."</b> no <b>boleto</b>";
} else {
$valven = ($valordoproduto/100)*$desc;
$preco = CurrencyConvertFormatPrice($valordoproduto-$valven, 1, 0);
$msg = "<b>1x</b> de <b>".$preco."</b> com <b>".$desc."%</b> de desconto no <b>boleto</b>";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
break; // fim boleto

case 'sps':
$ativo = GetModuleVariable('checkout_spsbradesco','is_setup');
$nome = GetModuleVariable('checkout_spsbradesco','displayname');
$boleto = GetModuleVariable('checkout_spsbradesco','pagboletos');
$facil = GetModuleVariable('checkout_spsbradesco','pagfacil');
$finan = GetModuleVariable('checkout_spsbradesco','pagfinan');
$trans = GetModuleVariable('checkout_spsbradesco','pagtrans');
if(!empty($ativo)) {
//verifica o desconto
$valordoproduto = ValorProduto($produto);
$msg = "";
$valordoproduto = ValorProduto($produto);
$msg = "";
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg .= "<b>1x</b> de <b> ".$preco."</b> no <b>bradesco online</b>";
//inicio do codigo do parcelamento
$parcelador .= $msg.'</br>';
//fim do codigo de parcelamento
}
break;



case 'cheque':
$ativo = GetModuleVariable('checkout_cheque','is_setup');
$juros = GetModuleVariable('checkout_cheque','juros');
$nome = GetModuleVariable('checkout_cheque','displayname');
$div = GetModuleVariable('checkout_cheque','dividir');
$jde = GetModuleVariable('checkout_cheque','jurosde');
$pmin = GetModuleVariable('checkout_cheque','parmin');
if(!empty($ativo)) {
//verifica o juros
$valordoproduto = ValorProduto($produto);
if($juros<=0 OR empty($juros)){
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg = "<b>1x</b> de <b> ".$preco."</b> sem juros no <b>cheque</b>";
} else {
$msg = '';
$msg1 = '';
$splits = (int) ($valordoproduto/$pmin);
if($splits<=$div){
$div = $splits;
}else{
$div = $div;
}
for ($j=1;$j<=$div;$j++) {
if($div==$j){
if ($jde<=$j and $jde<='50') {
$valven = ($valordoproduto/100)*$juros;
$msg1 .= "<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice(($valordoproduto+$valven)/$j, 1, 0)."</b> com juros no <b>cheque</b>";
}else{
$msg .= "<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valordoproduto/$j, 1, 0)."</b>sem juros no <b>cheque</b>";
}
}
}
}
//inicio do codigo do parcelamento
$parcelador .= $msg.''.$msg1.'<br>';
//fim do codigo de parcelamento
}
break;


case 'deposito': //deposito
$ativo = GetModuleVariable('checkout_deposito','is_setup');
$desc = GetModuleVariable('checkout_deposito','desconto');
$nome = GetModuleVariable('checkout_deposito','displayname');
if(!empty($ativo)) {
//verifica o desconto
$valordoproduto = ValorProduto($produto);
if($desc<=0 OR empty($desc)){
$preco = CurrencyConvertFormatPrice($valordoproduto, 1, 0);
$msg = "ou <b>".$preco."</b> a Vista no <b>Deposito</b>";
} else {
$valven = ($valordoproduto/100)*$desc;
$preco = CurrencyConvertFormatPrice($valordoproduto-$valven, 1, 0);
$msg = "ou  <b>".$preco."</b> com <b>".$desc."%</b> de Desconto no <b>Depósito em conta</b> ";
}
//inicio do codigo do parcelamento
$parcelador .= $msg.'<br>';
//fim do codigo de parcelamento
}
break; // fim deposito

}

}


$mostra = GetModuleVariable('addon_parcelas','loginparapreco');
if($mostra=='nao'){

$customerClass = GetClass('ISC_CUSTOMER');
if(!$customerClass->GetCustomerId()) {
return '';
}else{
return $parcelador;
}

}else{
return $parcelador;
}



}
?>
