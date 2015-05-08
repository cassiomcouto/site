<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$EmailPagSeguro	= corinthias("checkout_mercadopago","pagemail");
$token	= corinthias("checkout_mercadopago","token");
$ac	= corinthias("checkout_mercadopago","acrecimo");

$paymenturl = "https://www.mercadopago.com/mlb/buybutton";

	$total = $fetch_order['ordgatewayamount'];
	$c = ($total/100)*$ac;
	$valorpg = str_replace(",", ".",$total+$c);
	$valorfinal2 = number_format($valorpg, 2, '.', '');


if($ac>"0"){
$ms = "<b>Total de: ".$valorfinal2." Com ".$ac."% de Acrecimo.</b>";
} else {
$ms = "<b>Total de: ".$valorfinal2." Sem Acrecimo.</b>";
}


echo "<br><br><center><h2>".$ms."<br>Aguarde o Redirecionamento...</h2><br>
<form name='mercadopago' action='".$paymenturl."' method='POST'>
<input type='hidden' name='enc' value='".$token."'/>
<input type='hidden' name='acc_id' value='".$EmailPagSeguro."'/>
<input type='hidden' name='price' value='".$valorfinal2."' />
<input type='hidden' name='url_process' value='".$urlloja. "/modules/checkout/mercadopago/red.php?acao=processa&pedido=".base64_encode($fetch_order['orderid']) . "'/>
<input type='hidden' name='url_succesfull' value='".$urlloja. "/modules/checkout/mercadopago/red.php?acao=confirma&pedido=".base64_encode($fetch_order['orderid']) . "'/>
<input type='hidden' name='url_cancel' value='".$urlloja. "/modules/checkout/mercadopago/red.php?acao=cancela&pedido=".base64_encode($fetch_order['orderid']) . "'/>
<input type='hidden' name='currency' value='REA'>

<input type='hidden' name='shipping_cost' value='0'/>
<input type='hidden' name='item_id' value='".$fetch_order['orderid']."' />
<input type='hidden' name='name' value='PEDIDO #".$fetch_order['orderid']." na loja.' />
<input type='hidden' name='cart_cep' value='".$fetch_order['ordbillzip']."'/>
<input type='hidden' name='cart_street' value='" . $fetch_order['ordbillstreet1'] . "'/>
<input type='hidden' name='cart_number' value=''/>
<input type='hidden' name='cart_complement' value=''/>
<input type='hidden' name='cart_phone' value='" . $fetch_customer['custconphone'] . "'/>
<input type='hidden' name='cart_district' value='" . $basket['delInf']['suburb'] . "'/>
<input type='hidden' name='cart_city' value='" . $fetch_order['ordbillsuburb'] . "'/>
<input type='hidden' name='cart_state' value='" . $fetch_order['ordbillcountry'] . "'/>
<input type='hidden' name='cart_name' value='".$fetch_order['ordbillfirstname']."'/>
<input type='hidden' name='cart_surname' value='".$fetch_order['ordbilllastname']."'/>
<input type='hidden' name='cart_email' value='" . $fetch_customer['custconemail'] . "'/>
<input type='hidden' name='cart_doc_nbr' value=''/>
<input type='hidden' name='seller_op_id' value='".$fetch_order['orderid']."' />
</form>
";

?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
