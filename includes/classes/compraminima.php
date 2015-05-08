<?php
function TotalCarrinho(){
$total = $count = 0;

if (isset($_SESSION['CART']['ITEMS'])) {
	foreach ($_SESSION['CART']['ITEMS'] as $item) {
		$total += $item['product_price'] * $item['quantity'];
	}
}

return (float) $total;
}

function VerTotal(){

$orde = TotalCarrinho();
$min = GetModuleVariable("addon_compraminima", "valor");

$res = (float) $min-$orde;

if($orde<$min) {

echo'<script LANGUAGE="javascript">alert("O Valor Minimo para um Pedido é de: R$ '.$min.'\nO Valor de seu Pedido é: R$ '.$orde.'\nAinda Falta: R$ '.$res.'");</SCRIPT>';

}
return true;
}

function VerTotalRed(){

$orde = TotalCarrinho($_COOKIE['SHOP_SESSION_TOKEN']);
$min = GetModuleVariable("addon_compraminima", "valor");

$res = (float) $min-$orde;

if($orde<$min) {

header("Location: cart.php");

}
return true;
}
?>