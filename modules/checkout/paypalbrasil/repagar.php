<?php

global $itemId;
$itemId = $_GET['pedido'];

include "dados.php";

//Variaveis do modulo
$email = corinthias("checkout_paypalbrasil","email");
$modo = corinthias("checkout_paypalbrasil","mode");

if($modo == "YES") {
$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
$paypal_url = "https://www.paypal.com/br/cgi-bin/webscr";
}

$total = $fetch_order['ordgatewayamount'];
$valorfinal2 = number_format($total, 2, '.', '');

echo "<br><br><center><h2>Aguarde o Redirecionamento...</h2><br>

<form name='paypal' action='".$paypal_url."' method='POST'>
<input type='hidden' name='cmd' value='_ext-enter' />
<input type='hidden' name='redirect_cmd' value='_xclick' />
<input type='hidden' name='business' value='".$email."' />
<input type='hidden' name='rm' value='2' />
<input type='hidden' name='amount' value='".$valorfinal2."' />
<input type='hidden' name='no_shipping' value='1' />
<input type='hidden' name='tax' value='0' />
<input type='hidden' name='no_note' value='1' />
<input type='hidden' name='currency_code' value='BRL' />
<input type='hidden' name='item_name' value='PEDIDO #".$fetch_order['orderid']." na loja.' />
<input type='hidden' name='custom' value='".$fetch_order['orderid']."' />
<input type='hidden' name='notify_url' value='".$urlloja."/modules/checkout/paypalbrasil/retorno.php' />
<input type='hidden' name='cancel_return' value='".$urlloja."/cart.php' />
<input type='hidden' name='return' value='".$urlloja."/index.php' />
<input type='hidden' name='first_name' value='".$fetch_order['ordbillfirstname']."' />
<input type='hidden' name='last_name' value='".$fetch_order['ordbilllastname']."' />
<input type='hidden' name='email' value='".$fetch_customer['custconemail']."' />
<input type='hidden' name='address1' value='".$fetch_order['ordbillstreet1']."' />
<input type='hidden' name='address2' value='".$fetch_order['ordbillstreet2']."' />
<input type='hidden' name='day_phone_a' value='".$fetch_customer['custconphone']."' />
<input type='hidden' name='country' value='".$fetch_order['ordbillcountry']."' />
<input type='hidden' name='zip' value='".$fetch_order['ordbillzip']."' />
<input type='hidden' name='city' value='".$fetch_order['ordbillsuburb']."' />
<input type='hidden' name='address_override' value='1' />

</form>
";

?>
<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
