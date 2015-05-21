<?php
/////////////////////////////////////
include "../../../init.php";
/////////////////////////

$ped = $_POST['custom'];
$pedido = ereg_replace("[^0-9]", "", $ped);
$status = $_POST['payment_status'];
$idp = $_POST['txn_id'];


$orderPaymentStatus = '';
switch($_POST['payment_status']) {
			case "Completed":
				$newOrderStatus = ORDER_STATUS_AWAITING_SHIPMENT;
				break;

			case "Pending":
				$newOrderStatus = ORDER_STATUS_AWAITING_PAYMENT;
				break;

			case "Denied":
				$newOrderStatus = ORDER_STATUS_DECLINED;
				break;

			case "Failed":
				$newOrderStatus = ORDER_STATUS_DECLINED;
				break;

			case "Refunded":
				$newOrderStatus = ORDER_STATUS_REFUNDED;
				break;

			case "Reversed":
				$newOrderStatus = ORDER_STATUS_REFUNDED;
				break;

			case "Canceled_Reversal":
				$newOrderStatus = ORDER_STATUS_REFUNDED;
				break;
}

@UpdateOrderStatus($pedido, $newOrderStatus);



$msg =  $status;

$query = "UPDATE [|PREFIX|]orders SET ordpayproviderid = '".$idp."', 
ordpaymentstatus = '".$msg."' where orderid = '".$pedido."'";
$GLOBALS['ISC_CLASS_DB']->Query($query);



?>