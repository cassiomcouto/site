<?php
/////////////////////////////////////
include "../../../init.php";
///// inicio do retorno mercadopago ////////
function status($id){
if($id=='A') {
return 'Aprovado';
}else if($id=='P') {
return 'Pendente';
}else if($id=='C') {
return 'Cancelado';
}
}
function tipo($id){
if($id=='CC') {
return 'Cartao de Credito';
}else if($id=='BTR') {
return 'Debito Bancario';
}else if($id=='BTI') {
return 'Boleto Bancario';
}
}
/////////////////////////
if(!empty($_POST['mp_op_id'])){
/////////////////////////////////////
$pedido = $_POST['seller_op_id'];
$status = $_POST['status'];
$tipo = $_POST['payment_method'];
$codigo = $_POST['mp_op_id'];
$valor = $_POST['total_amount'];

//////////////////////////////////////
switch($status) {
case 'A';
@UpdateOrderStatus($pedido, ORDER_STATUS_AWAITING_SHIPMENT);
$msg =  "-----------------
\n## Aprovado (Verificar Manualmente) ##
\nTransacao: ".$codigo." 
\nStatus : ".status($status)."
\nData : ".date('d/m/Y')."
\nForma de Pagamento: ".tipo($tipo)."
\nTotal: ".$valor."
\n----------------";
$query = "UPDATE [|PREFIX|]orders SET 
ordcustmessage = '".$msg."' where orderid = '".$pedido."'";
$GLOBALS['ISC_CLASS_DB']->Query($query);
break;

case 'P';
@UpdateOrderStatus($pedido, ORDER_STATUS_PENDING);
$msg =  "-----------------
\n## Pendente ##
\nTransacao: ".$codigo." 
\nStatus : ".status($status)."
\nData : ".date('d/m/Y')."
\nForma de Pagamento: ".tipo($tipo)."
\nTotal: ".$valor."
\n----------------";
$query = "UPDATE [|PREFIX|]orders SET 
ordcustmessage = '".$msg."' where orderid = '".$pedido."'";
$GLOBALS['ISC_CLASS_DB']->Query($query);
break;

case 'C';
@UpdateOrderStatus($pedido, ORDER_STATUS_CANCELLED);
$msg =  "-----------------
\n## Cancelado ##
\nTransacao: ".$codigo." 
\nStatus : ".status($status)."
\nData : ".date('d/m/Y')."
\nForma de Pagamento: ".tipo($tipo)."
\nTotal: ".$valor."
\n----------------";
$query = "UPDATE [|PREFIX|]orders SET 
ordcustmessage = '".$msg."' where orderid = '".$pedido."'";
$GLOBALS['ISC_CLASS_DB']->Query($query);
break;

}
//////////////////////////////////////

} else {
@header("Location: ../../../index.php");
}

?>