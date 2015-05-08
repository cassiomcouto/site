<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$mailid	= corinthias("checkout_dinheiromail","pagdigemail");

//Variaveis da compra.
$statusPedido = $fetch_order['ordstatus']; if($statusPedido=="2" || $statusPedido=="3" || $statusPedido=="4" || $statusPedido=="5" || $statusPedido=="6" || $statusPedido=="10") { return false; echo "<script language=javascript>window.location.href=\"processado.php\";</script>"; };

$paymenturl = "https://brasil.dineromail.com/dinero-tools/login/Shop/Shop_Ingreso.asp";

	$valorfinal = number_format($fetch_order['ordgatewayamount'], 2, '.', '');

echo "
<form name='dinheiromail' action='".$paymenturl."' method='POST'>
<input name='NombreItem' type='hidden' value='PEDIDO #".$fetch_order['orderid']." na loja.'>
<input type='hidden' id='TipoMoneda' name='tipomoneda' value='1'>
<input name='PrecioItem' type='hidden' value='".$valorfinal."'>
<input type='hidden' id='E_Comercio' name='e_comercio' value='".$mailid."'>
<input name='NroItem' type='hidden' value='".$fetch_order['orderid']."'>
<input type='hidden' name='DireccionEnvio' value='1'>
<input type='hidden' name='Mensaje' value='1'>
<input type='hidden' id='MediosPago' name='MediosPago' value='4,5,6,14,15,16,17,2,7,13'>
<input type='hidden' id='TRX_ID' name='TRX_ID' value='".$fetch_order['orderid']."'>
</form>	                                     
";


?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
