<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$EmailPagSeguro	= corinthias("checkout_pagseguro","pagemail");
$ac	= corinthias("checkout_pagseguro","acrecimo");
//Variaveis da compra.
$statusPedido = $fetch_order['ordstatus']; if($statusPedido=="2" || $statusPedido=="3" || $statusPedido=="4" || $statusPedido=="5" || $statusPedido=="6" || $statusPedido=="10") { return false; echo "<script language=javascript>window.location.href=\"processado.php\";</script>"; };

$paymenturl = "https://pagseguro.uol.com.br/checkout/checkout.jhtml";

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
<form name='pagseguro' action='".$paymenturl."' method='POST'>
<input type='hidden' name='email_cobranca' value='".$EmailPagSeguro."' />
<input type='hidden' name='tipo' value='CBR' />
<input type='hidden' name='moeda' value='BRL' />
<input type='hidden' name='item_id' value='".$fetch_order['orderid']."' />
<input type='hidden' name='item_valor' value='".$valorfinal2."' />
<input type='hidden' name='item_frete' value='0' />
<input type='hidden' name='item_peso' value='0' />
<input type='hidden' name='item_quant' value='1' />
<input type='hidden' name='item_descr' value='PEDIDO #".$fetch_order['orderid']." na loja.' />
<input type='hidden' name='cliente_nome' value='".$fetch_order['ordbillfullname']."' />
<input type='hidden' name='url_retorno' value='".$urlloja."' />
<input type='hidden' name='cliente_email' value='".$fetch_customer['custconemail']."' />
<input type='hidden' name='cliente_end' value='".$fetch_order['ordbillstreet1']."' />
<input type='hidden' name='cliente_tel' value='".$fetch_customer['custconphone']."' />
<input type='hidden' name='cliente_pais' value='".$fetch_order['ordbillcountry']."' />
<input type='hidden' name='cliente_cep' value='".$fetch_order['ordbillzip']."' />
<input type='hidden' name='cliente_cidade' value='".$fetch_order['ordbillsuburb']."' />
</form>
";

?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
