<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$EmailMoip	= corinthias("checkout_moip","pagemail");
$ac	= corinthias("checkout_moip","acrecimo");
//Variaveis da compra.
$statusPedido = $fetch_order['ordstatus']; if($statusPedido=="2" || $statusPedido=="3" || $statusPedido=="4" || $statusPedido=="5" || $statusPedido=="6" || $statusPedido=="10") { return false; echo "<script language=javascript>window.location.href=\"processado.php\";</script>"; };


$cep = str_replace("-","",$fetch_order['ordshipzip']);

	$total = $fetch_order['ordgatewayamount'];
	$c = ($total/100)*$ac;
	$valorpg = str_replace(",", ".",$total+$c);
	$valorfinal = number_format($valorpg, 2, '.', '');
	$valorfinal2 = number_format($valorpg, 2, '', '');

if($ac>"0"){
$ms = "<b>Total de: ".$valorfinal." Com ".$ac."% de Acrecimo.</b>";
} else {
$ms = "<b>Total de: ".$valorfinal." Sem Acrecimo.</b>";
}


echo "<br><br><center><h2>".$ms."<br>Aguarde o Redirecionamento...</h2><br>
<form action='https://www.moip.com.br/PagamentoMoIP.do' method='post' name='moip'>
<input type='hidden' name='id_carteira' value='".$EmailMoip."'>
<input type='hidden' name='valor' value='" . $valorfinal2 . "'>
<input type='hidden' name='nome' value='Pedido #" . $fetch_order['orderid'] . " na loja '>
<input type='hidden' name='pagador_nome' value='" . $fetch_order['ordbillfirstname'] . "'>
<input type='hidden' name='pagador_email' value='" .$fetch_customer['custconemail']. "'>
<input type='hidden' name='pagador_logradouro' value='" . $fetch_order['ordbillstreet1'] . "'>
<input type='hidden' name='pagador_bairro' value='" . $fetch_order["ordbillsuburb"] . "'>
<input type='hidden' name='pagador_cidade' value='" . $fetch_order["ordshipsuburb"] . "'>
<input type='hidden' name='pagador_estado' value='" . substr($fetch_order["ordshipstate"],0,2) . "'>
<input type='hidden' name='pagador_cep' value='" . $cep . "'>
</form>
";

?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
