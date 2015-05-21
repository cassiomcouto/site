<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$EmailPagamentoDigital	= corinthias("checkout_bcash","pagdigemail");
$ac	= corinthias("checkout_bcash","acrecimo");
//Variaveis da compra.
$statusPedido = $fetch_order['ordstatus']; if($statusPedido=="2" || $statusPedido=="3" || $statusPedido=="4" || $statusPedido=="5" || $statusPedido=="6" || $statusPedido=="10") { return false; echo "<script language=javascript>window.location.href=\"processado.php\";</script>"; };

$paymenturl = "https://www.pagamentodigital.com.br/checkout/pay/";

	$total = $fetch_order['ordgatewayamount'];
	$c = ($total/100)*$ac;
	$valorpg = str_replace(",", ".",$total+$c);
	$valorfinal = number_format($valorpg, 2, '.', '');


/*
if($ac>"0"){
$ms = "<b>Total de: ".$valorfinal." Com ".$ac."% de Acrecimo.</b>";
} else {
$ms = "<b>Total de: ".$valorfinal." Sem Acrecimo.</b>";
}
*/

echo "<br><br><center><h2>".$ms."<br>Aguarde o Redirecionamento...</h2><br>
<form name='bcash' action='".$paymenturl."' method='POST'>
<input name='email_loja' type='hidden' value='".$EmailPagamentoDigital."'>
<input name='produto_codigo_1' type='hidden' value='".$fetch_order['orderid']."'>
<input name='produto_descricao_1' type='hidden' value='PEDIDO #".$fetch_order['orderid']." na loja ".$nomeloja."'>
<input name='produto_qtde_1' type='hidden' value='1'>
<input name='produto_valor_1' type='hidden' value='".$valorfinal."'>               
<input name='tipo_integracao' type='hidden' value='PAD'>
<input name='frete' type='hidden' value='0'>
<input name='id_pedido' type='hidden' value='".rand(0,9999999999999999999999)."'>
<input name='email' type='hidden' value='".$fetch_customer['custconemail']."'>
<input name='nome' type='hidden' value='".$fetch_customer['custconfirstname']." ".$fetch_customer['custconlastname']."'>
<input name='cpf' type='hidden' value='".$fetch_order['ordbillcompany']."'>
<input name='telefone' type='hidden' value='(99)".$fetch_customer['custconphone']."'>
<input name='endereco' type='hidden' value='".$fetch_order['ordbillstreet1']."'>
<input name='bairro' type='hidden' value='".$fetch_order['ordbillstreet2']."'>
<input name='cidade' type='hidden' value='".$fetch_order['ordbillsuburb']."'>
<input name='estado' type='hidden' value='".$fetch_order['ordbillstate']."'>
<input name='cep' type='hidden' value='".$fetch_order['ordshipzip']."'>
<input name='celular' type='hidden' value='(99)9999-9999'>
<input name='data_nascimento' type='hidden' value='27/08/1987'>
<input name='sexo' type='hidden' value='M'>
<input name='free' type='hidden' value=''>
</form>	                                     
";


?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
