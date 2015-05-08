<?php

global $itemId;
$itemId = $_POST['item_id'];
if($itemId=="" || $itemId==NULL || !isset($itemId)){
$itemId = $_GET['boleto'];
}


include "../../../dados.php";

//Variaveis do modulo
$BoletodiasVencimento		= corinthias("checkout_boletobancodobrasil","boletobancodobrasildiasparavencimento");
$BoletoCedenteDocumento 	= corinthias("checkout_boletobancodobrasil","boletobancodobrasilcpfcnpj");
$BoletoEspecie 				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilespecie");
$BoletoEspecieDoc 			= corinthias("checkout_boletobancodobrasil","boletobancodobrasilespeciedoc");
$BoletoAceite 				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilaceite");
$BoletoInstrucaoU 			= corinthias("checkout_boletobancodobrasil","boletobancodobrasilinstrucaoum");
$BoletoInstrucaoD 			= corinthias("checkout_boletobancodobrasil","boletobancodobrasilinstrucaodois");
$BoletoInstrucaoT 			= corinthias("checkout_boletobancodobrasil","boletobancodobrasilinstrucaotres");
$BoletoInstrucaoQ 			= corinthias("checkout_boletobancodobrasil","boletobancodobrasilinstrucaoquatro");
$BoletoDemoU 				= corinthias("checkout_boletobancodobrasil","demoum");
$BoletoDemoD 				= corinthias("checkout_boletobancodobrasil","demodois");
$BoletoDemoT 				= corinthias("checkout_boletobancodobrasil","demotres");
$BoletoCedente 				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilcedente");
$BoletoCarteira 			= corinthias("checkout_boletobancodobrasil","boletobancodobrasilcarteira");
$BoletoAgencia 				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilagencia");
$BoletoConta 				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilconta");

$BoletoConvenio				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilconvenio");
$BoletoContrato				= corinthias("checkout_boletobancodobrasil","boletobancodobrasilcontrato");
$BoletoVC 					= corinthias("checkout_boletobancodobrasil","boletobancodobrasilvariacaocarteira");
$BoletoFC 					= corinthias("checkout_boletobancodobrasil","boletobancodobrasilformatacaoconvenio");
$BoletoFNN 					= corinthias("checkout_boletobancodobrasil","boletobancodobrasilformatacaonossonumero");

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

$dadosboleto["convenio"] = $BoletoConvenio;
$dadosboleto["contrato"] = $BoletoContrato;
$dadosboleto["variacao_carteira"] = $BoletoVC;
$dadosboleto["formatacao_convenio"] = $BoletoFC;
$dadosboleto["formatacao_nosso_numero"] = $BoletoFNN; 

$dadosboleto["carteira"] = $BoletoCarteira;
//$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175 ou 109

// SEUS DADOS
$dadosboleto["identificacao"] = "Impressão de Boleto Online (BANCO DO BRASIL)";

$dadosboleto["cpf_cnpj"] = $BoletoCedenteDocumento;
$dadosboleto["cedente"] = $BoletoCedente;

// NÃO ALTERAR!
include("include/funcoes_bb.php"); 
include("include/layout_bb.php");
?>
<script language="javascript">
self.print();
</script>