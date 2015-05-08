<?php
include("../../../top.php");
?>

<?php
///print_r($_POST);
echo "<br>";
$ar = @$_REQUEST['CODRET'];

///////////////////////////////////////////////////
$query = sprintf("select * from [|PREFIX|]module_vars where modulename = 'checkout_masterkomerci' and variablename = 'afi'");
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

ini_set("allow_url_fopen", 1);
ini_set("display_errors", 0);
error_reporting(0);
ini_set("track_errors","0");


// ********************* Dados obtidos do retorno da Redecard ***************
// codigo da autorizacao, se tiver
$arp = $_REQUEST['NUMAUTOR'];
// comprovante de venda
$cv = $_REQUEST['NUMCV'];
// número sequencial unico da transação
$sqn = $_REQUEST['NUMSQN'];
// data da transação
$data = $_REQUEST['DATA'];
// Codigo de retorno AVS
$cravs = $_REQUEST['RESPAVS'];
// Mensagem de retorno do AVS
$mensavs = $_REQUEST['MSGAVS'];
// Número do pedido
$numpedido = $_REQUEST['NUMPEDIDO'];


///////////////////////////////////////////////////////
$queryss = sprintf("select * from [|PREFIX|]orders where orderid = ".$numpedido);
$resultss = $GLOBALS['ISC_CLASS_DB']->Query($queryss);
$rows = $GLOBALS['ISC_CLASS_DB']->Fetch($resultss);
///////////////////////////////////////////////////////////
$vai = sprintf("select * from pag_redecard where ped = ".$numpedido);
$vou = $GLOBALS['ISC_CLASS_DB']->Query($vai);
$itu = $GLOBALS['ISC_CLASS_DB']->Fetch($vou);
///////////////////////////////////////////////////////////

$valorPa =number_format($rows['ordtotalamount'], 2, '.', '');

// ******************************** Dados obtidos de sua loja ***************
// valor total da compra (sem formatação).
$total = $valorPa;
// número de parcelas da compra
$numparcelas = $itu['bandeira'];
// número de afiliação junto à Redecard
$numafiliacao = $row['variableval'];
// habilita cobrança de juros em parcelamento de compras. 0 = desativa , 1 = ativa
$RedeCardJurosParcelado = 0;

// status transacao
if ($_REQUEST['CODRET'] != "" && $_REQUEST['CODRET'] != "0"){
   $status = $_REQUEST['CODRET'];
   $autent = $_REQUEST['MSGRET'];
} else {
   $status = 0;
   // numero da autenticacao
   $autent = $_REQUEST['NUMAUTENT'];
}

// ************************ Confirma transação com a Redecard ***************
if ($status == 0) {
    $valores = "DATA=" . $data;
    $valores = $valores . "&TRANSACAO=203";
        if (strlen($numparcelas) == 1){
	       $parcelas = "0" . $numparcelas;
        } else {
	       $parcelas = $numparcelas;
		}

        if ($parcelas == "01" || $parcelas == "00" || $numparcelas == ""){
	       $parcelas = "00";
	       $trans_orig = "04";
        } else {
	        if ($RedeCardJurosParcelado == 1){
		       $trans_orig = "06";
	        } else {
		       $trans_orig = "08";
	        }
        }
    $valores = $valores . "&TRANSORIG=" . $trans_orig;
    $valores = $valores . "&PARCELAS=" . $parcelas;
    $valores = $valores . "&FILIACAO=" . $numafiliacao;
	$valores = $valores . "&DISTRIBUIDOR="; // este campo deve ser nulo
        //$total = substr($total, 0, (strlen($total)-2)) . "." . substr($total,-2);
    $valores = $valores . "&TOTAL=" . $total;
    $valores = $valores . "&NUMPEDIDO=" . $numpedido;
    $valores = $valores . "&NUMAUTOR=" . $arp;
    $valores = $valores . "&NUMCV=" . $cv;
    $valores = $valores . "&NUMSQN=" . $sqn;

	// contacta RedeCard e confirma transação
	$filename="http://ecommerce.redecard.com.br/pos_virtual/confirma.asp?" . $valores;

	$file = file($filename); 
	$retorna = $file[0]; 
	$arrLinhas = explode("&", $retorna);
	$i = 0; 
	foreach ($arrLinhas AS $line) { 
	   list($variavel, $valor) = explode('=', ($line)); 
	   $variavel = trim($variavel); 
	   $$variavel = $valor ; 
	   $i ++; 
	}

	$status = $_REQUEST['CODRET'];
	if ($status > 1) {
	   $autent = $_REQUEST['MSGRET'];
	}
}	

// **************************** Em caso de falha na transação ***************
if ($status > 1){
echo "<h2>Pedido Negado!</h2>";
	echo "Codigo:". $status . "<br>Erro:";
	echo htmlspecialchars(urldecode($autent));
	
$msg =  "-----------------\nPedido Negado, Pedido : ". $numpedido ." \nStatus : ".$status."\nErro : ".$autent."\n----------------";
            		
$query = "UPDATE [|PREFIX|]orders SET ordcustmessage = '".$msg."' where orderid = '".$numpedido."'";
$re = $GLOBALS['ISC_CLASS_DB']->Query($query);
@UpdateOrderStatus($numpedido, ORDER_STATUS_CANCELLED);

} else {

// ************** Em caso da transação já ter sido confirmada ***************
if ($status == 1){
	echo "<h2>Transação já confirmada.</h2><br>";
}

// ************** Em caso de transação aprovada ***************
echo "<h2>Transa&ccedil;&atilde;o aprovada!</h2>" . "<br>";
echo "Pedido: ".$numpedido . "<br>";
echo "Data: ".$data . "<br>";
echo "Auth: ".$cv;

// UPDATE ORDER STATUS PARA 'VALID'

$msg =  "-----------------\nPedido Aprovado, Pedido : ". $numpedido ." \nData : ".$data."\nAuth : ".$cv."\n----------------";
            		
$query = "UPDATE [|PREFIX|]orders SET ordcustmessage = '".$msg."' where orderid = '".$numpedido."'";
$re = $GLOBALS['ISC_CLASS_DB']->Query($query);
@UpdateOrderStatus($numpedido, ORDER_STATUS_PENDING);

// ************************** Monta o cupom *********************************
$URLCupom = "https://ecommerce.redecard.com.br/pos_virtual/cupom.asp?DATA=" . $data . "&TRANSACAO=201&NUMAUTOR=" . $arp . "&NUMCV=" . $cv;


echo "
<br>Caso seu navegador tenha bloqueado o popup do cupom, clique no link abaixo para visualizalo pois o pedido só sera validado se o popup for aberto.<br /><br>
<a href=\"javascript:window.open('$URLCupom','pagamento','width=300,height=350,scrollbars=no');void(0);\">VISUALIZAR CUPOM</a>
<SCRIPT LANGUAGE=javascript>
<!--
        vpos=window.open('$URLCupom','vpos','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=auto,resizable=no,copyhistory=no,width=300,height=300');
//-->
</SCRIPT>";

}

?>
<script language="JavaScript">
window.onload = maxWindow;

function maxWindow()
{
window.moveTo(0,0);


if (document.all)
{
  top.window.resizeTo(screen.availWidth,screen.availHeight);
}

else if (document.layers||document.getElementById)
{
  if (top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth)
  {
    top.window.outerHeight = screen.availHeight;
    top.window.outerWidth = screen.availWidth;
  }
}
}

</script>


<?php
include("../../../bottom.php");
?>