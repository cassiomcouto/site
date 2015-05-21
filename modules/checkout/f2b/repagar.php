<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$conta	= corinthias("checkout_f2b","conta");
$senha	= corinthias("checkout_f2b","senha");
$taxa	= corinthias("checkout_f2b","taxa");
$tipotaxa	= corinthias("checkout_f2b","tipotaxa");
//Variaveis da compra.
$statusPedido = $fetch_order['ordstatus']; if($statusPedido=="2" || $statusPedido=="3" || $statusPedido=="4" || $statusPedido=="5" || $statusPedido=="6" || $statusPedido=="10") { return false; echo "<script language=javascript>window.location.href=\"processado.php\";</script>"; };


	$total = $fetch_order['ordgatewayamount'];
	$valorPa =number_format($total, 2, ',', '');

$prazo = corinthias("checkout_f2b","comvencimento");
$data_venc = date("d/m/Y", time() + ($prazo * 86400));

$explodir = explode("/",$data_venc);

$anof = $explodir[2];
$mesf = $explodir[1];
$diaf = $explodir[0];

$vencimentoF = $anof."-".$mesf."-".$diaf;


echo "
<form action='http://www.f2b.com.br/BillingWeb' method='post'>
<input type='hidden' name='tipo_cobranca' value=''>
<input type='hidden' name='conta' value='" . $conta . "'>
<input type='hidden' name='senha' value='" . $senha . "'>
<input type='hidden' name='valor' value='" . $valorPa . "'>
<input type='hidden' name='taxa' value='" . $taxa . "'>
<input type='hidden' name='tipo_taxa' value='" . $tipotaxa . "'>
<input type='hidden' name='demonstrativo_1' value='Pedido #" . $fetch_order['orderid'] . " na loja '>
<input type='hidden' name='vencimento' value='".$vencimentoF."'>
<input type='hidden' name='nome' value='" . $fetch_order['ordbillfirstname'] . "'>
<input type='hidden' name='email_1' value='" .$fetch_customer['custconemail']. "'>
<INPUT type='hidden' name='agendamento' value='n'>
<INPUT type='hidden' name='atualizar' value='s'>
<input type='hidden' name='endereco_logradouro' value='" . $fetch_order['ordbillstreet1'] . "'>
<input type='hidden' name='endereco_numero' value='0'>
<input type='hidden' name='endereco_bairro' value='" . $fetch_order["ordbillsuburb"] . "'>
<input type='hidden' name='endereco_cidade' value='" . $fetch_order["ordshipsuburb"] . "'>
<input type='hidden' name='endereco_estado' value='" . substr($fetch_order["ordshipstate"],0,2) . "'>
<input type='hidden' name='endereco_cep' value='" . $fetch_order["ordshipzip"] . "'>
</form>";
?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
