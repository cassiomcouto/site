<?php

global $itemId;
$itemId = $_POST['item_id'];
if($itemId=="" || $itemId==NULL || !isset($itemId)){
$itemId = $_GET['boleto'];
}


include "../../../dados.php";

//Variaveis do modulo
$BoletodiasVencimento		= corinthias("checkout_boletobradesco","boletobradescodiasparavencimento");
$BoletoCedenteDocumento 	= corinthias("checkout_boletobradesco","boletobradescocpfcnpj");
$BoletoEspecie 				= corinthias("checkout_boletobradesco","boletobradescoespecie");
$BoletoEspecieDoc 			= corinthias("checkout_boletobradesco","boletobradescoespeciedoc");
$BoletoAceite 				= corinthias("checkout_boletobradesco","boletobradescoaceite");
$BoletoInstrucaoU 			= corinthias("checkout_boletobradesco","boletobradescoinstrucaoum");
$BoletoInstrucaoD 			= corinthias("checkout_boletobradesco","boletobradescoinstrucaodois");
$BoletoInstrucaoT 			= corinthias("checkout_boletobradesco","boletobradescoinstrucaotres");
$BoletoInstrucaoQ 			= corinthias("checkout_boletobradesco","boletobradescoinstrucaoquatro");
$BoletoDemoU 				= corinthias("checkout_boletobradesco","demoum");
$BoletoDemoD 				= corinthias("checkout_boletobradesco","demodois");
$BoletoDemoT 				= corinthias("checkout_boletobradesco","demotres");
$BoletoCedente 				= corinthias("checkout_boletobradesco","boletobradescocedente");
$BoletoCarteira 			= corinthias("checkout_boletobradesco","boletobradescocarteira");
$BoletoAgencia 				= corinthias("checkout_boletobradesco","boletobradescoagencia");
$BoletoConta 				= corinthias("checkout_boletobradesco","boletobradescoconta");
$BoletoDVC 					= corinthias("checkout_boletobradesco","boletobradescocontadv");
$BoletoDVA 					= corinthias("checkout_boletobradesco","boletobradescoagenciadv");

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

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $sacadoBoleto;
$dadosboleto["endereco1"] = $enderecoBoleto;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $BoletoDemoU;
$dadosboleto["demonstrativo2"] = $BoletoDemoD;
$dadosboleto["demonstrativo3"] = $BoletoDemoT;
$dadosboleto["instrucoes1"] = $BoletoInstrucaoU;
$dadosboleto["instrucoes2"] = $BoletoInstrucaoD;
$dadosboleto["instrucoes3"] = $BoletoInstrucaoT;
$dadosboleto["instrucoes4"] = $BoletoInstrucaoQ;

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "1";
$dadosboleto["valor_unitario"] = "$valor_cobrado";
$dadosboleto["aceite"] = $BoletoAceite;		
$dadosboleto["especie"] = $BoletoEspecie;

$dadosboleto["uso_banco"] = ""; 	
$dadosboleto["especie_doc"] = $BoletoEspecieDoc;




// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - ITAÚ
$dadosboleto["agencia"] = $BoletoAgencia;
$dadosboleto["conta"] = $BoletoConta;
$dadosboleto["agencia_dv"] = $BoletoDVA; // Digito do Num da agencia
$dadosboleto["conta_dv"] = $BoletoDVC; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - ITAÚ
$dadosboleto["conta_cedente"] = $BoletoConta; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $BoletoDVC; // Digito da ContaCedente do Cliente

$dadosboleto["carteira"] = $BoletoCarteira;
//$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175 ou 109

// SEUS DADOS
$dadosboleto["identificacao"] = "Impressão de Boleto Online (BANCO BRADESCO)";

$dadosboleto["cpf_cnpj"] = $BoletoCedenteDocumento;
$dadosboleto["cedente"] = $BoletoCedente;

// NÃO ALTERAR!
include("include/funcoes_bradesco.php"); 
include("include/layout_bradesco.php");
?>
<script language="javascript">
self.print();
</script>