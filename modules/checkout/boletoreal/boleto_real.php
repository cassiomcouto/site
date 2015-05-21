<?php

global $itemId;
$itemId = $_POST['item_id'];
if($itemId=="" || $itemId==NULL || !isset($itemId)){
$itemId = $_GET['boleto'];
}


include "../../../dados.php";

//Variaveis do modulo
$BoletodiasVencimento		= corinthias("checkout_boletoreal","boletorealdiasparavencimento");
$BoletoCedenteDocumento 	= corinthias("checkout_boletoreal","boletorealcpfcnpj");
$BoletoEspecie 				= corinthias("checkout_boletoreal","boletorealespecie");
$BoletoEspecieDoc 			= corinthias("checkout_boletoreal","boletorealespeciedoc");
$BoletoAceite 				= corinthias("checkout_boletoreal","boletorealaceite");
$BoletoInstrucaoU 			= corinthias("checkout_boletoreal","boletorealinstrucaoum");
$BoletoInstrucaoD 			= corinthias("checkout_boletoreal","boletorealinstrucaodois");
$BoletoInstrucaoT 			= corinthias("checkout_boletoreal","boletorealinstrucaotres");
$BoletoInstrucaoQ 			= corinthias("checkout_boletoreal","boletorealinstrucaoquatro");
$BoletoDemoU 				= corinthias("checkout_boletoreal","demoum");
$BoletoDemoD 				= corinthias("checkout_boletoreal","demodois");
$BoletoDemoT 				= corinthias("checkout_boletoreal","demotres");
$BoletoCedente 				= corinthias("checkout_boletoreal","boletorealcedente");
$BoletoCarteira 			= corinthias("checkout_boletoreal","boletorealcarteira");
$BoletoAgencia 				= corinthias("checkout_boletoreal","boletorealagencia");
$BoletoConta 				= corinthias("checkout_boletoreal","boletorealconta");

//Variaveis da compra.

$statusPedido = $fetch_order['ordstatus']; if($statusPedido=="2" || $statusPedido=="3" || $statusPedido=="4" || $statusPedido=="5" || $statusPedido=="6" || $statusPedido=="10") { return false; echo "<script language=javascript>window.location.href=\"processado.php\";</script>"; };

$valorBoleto = $fetch_order['ordtotalamount'];
$sacadoBoleto = $fetch_order['ordbillfirstname']." ".$fetch_order['ordbilllastname'];
$enderecoBoleto = $fetch_order['ordbillstreet1']." - ".$fetch_order['ordbillsuburb']." - ".$fetch_order['ordbillstate'].", CEP: ".$fetch_order['ordbillzip'];


$prazo = $BoletodiasVencimento;
$data_venc = date("d/m/Y", time() + ($prazo * 86400)); 
$valor_cobrado = $valorBoleto;
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado, 2, ',', '');
$dadosboleto["nosso_numero"] = $itemId;
$dadosboleto["numero_documento"] = $itemId;
$dadosboleto["data_vencimento"] = $data_venc;
$dadosboleto["data_documento"] = date("d/m/Y");
$dadosboleto["data_processamento"] = date("d/m/Y");
$dadosboleto["valor_boleto"] = $valor_boleto;
$dadosboleto["sacado"] = $sacadoBoleto;
$dadosboleto["endereco1"] = $enderecoBoleto;
$dadosboleto["demonstrativo1"] = $BoletoDemoU;
$dadosboleto["demonstrativo2"] = $BoletoDemoD;
$dadosboleto["demonstrativo3"] = $BoletoDemoT;
$dadosboleto["instrucoes1"] = $BoletoInstrucaoU;
$dadosboleto["instrucoes2"] = $BoletoInstrucaoD;
$dadosboleto["instrucoes3"] = $BoletoInstrucaoT;
$dadosboleto["instrucoes4"] = $BoletoInstrucaoQ;
$dadosboleto["quantidade"] = "1";
$dadosboleto["valor_unitario"] = "$valor_cobrado";
$dadosboleto["aceite"] = $BoletoAceite;		
$dadosboleto["especie"] = $BoletoEspecie;
$dadosboleto["especie_doc"] = $BoletoEspecieDoc;
$dadosboleto["agencia"] = $BoletoAgencia;
$dadosboleto["conta"] = $BoletoConta;
$dadosboleto["carteira"] = $BoletoCarteira;
$dadosboleto["identificacao"] = "Impressão de Boleto Online (BANCO REAL)";
$dadosboleto["cpf_cnpj"] = $BoletoCedenteDocumento;
$dadosboleto["cedente"] = $BoletoCedente;
include("include/funcoes_real.php"); 
include("include/layout_real.php");

?>
<script language="javascript">
self.print();
</script>