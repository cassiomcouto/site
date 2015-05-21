<?php

global $itemId;
$itemId = $_POST['item_id'];
if($itemId=="" || $itemId==NULL || !isset($itemId)){
$itemId = $_GET['boleto'];
}


include "../../../dados.php";

//Variaveis do modulo
$BoletoDesc 				= corinthias("checkout_boletoitau","desconto");

$BoletodiasVencimento		= corinthias("checkout_boletoitau","boletoitaudiasparavencimento");
$BoletoCedenteDocumento 	= corinthias("checkout_boletoitau","boletoitaucpfcnpj");
$BoletoEspecie 				= corinthias("checkout_boletoitau","boletoitauespecie");
$BoletoEspecieDoc 			= corinthias("checkout_boletoitau","boletoitauespeciedoc");
$BoletoAceite 				= corinthias("checkout_boletoitau","boletoitauaceite");
$BoletoInstrucaoU 			= corinthias("checkout_boletoitau","boletoitauinstrucaoum");
$BoletoInstrucaoD 			= corinthias("checkout_boletoitau","boletoitauinstrucaodois");
$BoletoInstrucaoT 			= corinthias("checkout_boletoitau","boletoitauinstrucaotres");
$BoletoInstrucaoQ 			= corinthias("checkout_boletoitau","boletoitauinstrucaoquatro");
$BoletoDemoU 				= corinthias("checkout_boletoitau","demoum");
$BoletoDemoD 				= corinthias("checkout_boletoitau","demodois");
$BoletoDemoT 				= corinthias("checkout_boletoitau","demotres");
$BoletoCedente 				= corinthias("checkout_boletoitau","boletoitaucedente");
$BoletoCarteira 			= corinthias("checkout_boletoitau","boletoitaucarteira");
$BoletoAgencia 				= corinthias("checkout_boletoitau","boletoitauagencia");
$BoletoConta 				= corinthias("checkout_boletoitau","boletoitauconta");
$BoletoDV 				= corinthias("checkout_boletoitau","boletoitaudv");

//Variaveis da compra.
$valorBoleto = $fetch_order['ordtotalamount'];
$sacadoBoleto = $fetch_order['ordbillfirstname']." ".$fetch_order['ordbilllastname'];
$enderecoBoleto = $fetch_order['ordbillstreet1']." - ".$fetch_order['ordbillsuburb']." - ".$fetch_order['ordbillstate'].", CEP: ".$fetch_order['ordbillzip'];


$prazo = $BoletodiasVencimento;

$data_venc = date("d/m/Y", time() + ($prazo * 86400)); 
$valor_cobrado = $valorBoleto;
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado, 2, ',', '');

if($BoletoDesc > 0){
$valor_desconto = ($valor_cobrado/100)*$BoletoDesc;
$valor_boleto=number_format($valor_cobrado-$valor_desconto, 2, ',', '');
}

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

$dadosboleto["conta_dv"] = $BoletoDV; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - ITAÚ

$dadosboleto["carteira"] = $BoletoCarteira;
//$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175 ou 109

// SEUS DADOS
$dadosboleto["identificacao"] = "Impressão de Boleto Online (BANCO ITAÚ)";

$dadosboleto["cpf_cnpj"] = $BoletoCedenteDocumento;
$dadosboleto["cedente"] = $BoletoCedente;

// NÃO ALTERAR!
include("include/funcoes_itau.php"); 
include("include/layout_itau.php");
?>
<script language="javascript">
self.print();
</script>