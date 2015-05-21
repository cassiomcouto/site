<?php

	class CHECKOUT_boletoitau extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_boletoitau";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_boletoitau()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = GetLang('boletoitauName');
			$this->_description = GetLang('boletoitauDesc');
			$this->SetImage('logo.gif');
			$this->_help = GetLang('boletoitauHelp');
			//$this->_enabled = $this->_CheckEnabled();
			$this->_height = 0;
			
			// This is an offline payment method
			$this->_paymenttype = PAYMENT_PROVIDER_OFFLINE;
		}

		/*
			Is bank deposit accessible by the customer? It depends on which
			"Available Countries" are setup by the administrator
		*/
		function isaccessible()
		{
			// If bank deposit is available for all countries then return true
			$available_countries = $this->GetValue("availablecountries");

			if( (!is_array($available_countries) && $available_countries == "all") || (is_array($available_countries) && in_array("all", $available_countries)) ) {
				return true;
			}
			else if(!isset($GLOBALS['ISC_CLASS_ACCOUNT'])) { // Always accessible to the Admin panel
				return true;
			}
			else {
				// Load the pending order
				$pendingOrder = LoadPendingOrderByToken();

				// Check the country in the billing address. If it's not set then assume true
				if(isset($pendingOrder['ordbillcountryid'])) {
					$billing_country_id = $pendingOrder['ordbillcountryid'];
					if(is_array($available_countries)) {
						if(in_array($billing_country_id, $available_countries)) {
							return true;
						} else {
							return false;
						}
					}
					else {
						if($billing_country_id == $available_countries) {
							return true;
						} else {
							return false;
						}
					}
				}
				else {
					return true;
				}
			}
		}

		/**
		* Custom variables for the checkout module. Custom variables are stored in the following format:
		* array(variable_id, variable_name, variable_type, help_text, default_value, required, [variable_options], [multi_select], [multi_select_height])
		* variable_type types are: text,number,password,radio,dropdown
		* variable_options is used when the variable type is radio or dropdown and is a name/value array.
		*/
		function SetCustomVars()
		{



			$this->_variables['availablecountries'] = array("name" => "Continentes",
			   "type" => "dropdown",
			   "help" => GetLang('boletoitauContinentes'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);			
			
			
			$this->_variables['desconto'] = array("name" => "Desconto em %",
			   "type" => "textbox",
			   "help" => '',
			   "default" => "0",
			   "required" => true
			);
			
			


			$this->_variables['boletoitaucedente'] = array("name" => "Cedente",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauCedente'),
			   "default" => "",
			   "required" => true
			);

			$this->_variables['boletoitauagencia'] = array("name" => "Agencia",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauAgencia'),
			   "default" => "",
			   "required" => true
				);

			$this->_variables['boletoitauconta'] = array("name" => "Conta",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauConta'),
			   "default" => "",
			   "required" => true
			);
		
			$this->_variables['boletoitaucarteira'] = array("name" => "Carteira",
			   "type" => "dropdown",
			   "help" => GetLang('boletoitauCarteira'),
			   "default" => "20",
			   "options" => array('175' => '175', '109' => '109'),
			   "required" => true,
			   "multiselection" => false
			);
			



	//_-----------------------------------------------------------------------------------------demonstrativos
	
			$this->_variables['demoum'] = array("name" => "Demonstra&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauDemoU'),
			   "default" => "REFERENTE A COMPRAS ONLINE",
			   "required" => false
			);
		
			$this->_variables['demodois'] = array("name" => "Demonstra&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauDemoD'),
			   "default" => "Duvidas e sugest&otilde;es entre em contato conosco:",
			   "required" => false
			);
		
					$this->_variables['demotres'] = array("name" => "Demonstra&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauDemoT'),
			   "default" => "sac@seusite.com.br | +55 xx 0000.0000",
			   "required" => false
			);
			
			
	//_------------------------------------------------------------------------------------------fim-demonstrativos
	




			
			$this->_variables['boletoitauinstrucaoum'] = array("name" => "Instru&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauInstrucaoUm'),
			   "default" => "Multa de R$ 3,00 por atraso.",
			   "required" => false
			);
		
			$this->_variables['boletoitauinstrucaodois'] = array("name" => "Instru&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauInstrucaoDois'),
			   "default" => "Apos o vencimento, pagavel apenas no Banco Real",
			   "required" => false
			);
		
					$this->_variables['boletoitauinstrucaotres'] = array("name" => "Instru&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauInstrucaoTres'),
			   "default" => "Fatura sujeita a protesto no SPC/SERASA",
			   "required" => false
			);
			
			
			$this->_variables['boletoitauinstrucaoquatro'] = array("name" => "Instru&ccedil;&atilde;o 4",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauInstrucaoQuatro'),
			   "default" => "Juros de mora de 0,1% ao dia.",
			   "required" => false
			);
			
	
	
	
	
	
	
			
			
			$this->_variables['boletoitauaceite'] = array("name" => "Aceite",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauAceite'),
			   "default" => "",
			   "required" => false
			);
			
			
			$this->_variables['boletoitauespeciedoc'] = array("name" => "Esp&eacute;cie do documento",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauEspecieDoc'),
			   "default" => "",
			   "required" => false
			);

			$this->_variables['boletoitauespecie'] = array("name" => "Esp&eacute;cie de cobran&ccedil;a",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauEspecie'),
			   "default" => "R$",
			   "required" => false
			);
			

			$this->_variables['boletoitaucpfcnpj'] = array("name" => "CPF ou CNPJ do Boleto",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauCNPF'),
			   "default" => "",
			   "required" => false
			);
		
		
		$this->_variables['boletoitaudiasparavencimento'] = array("name" => "Dias para vencimento",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauVence'),
			   "default" => "10",
			   "required" => true
			);
		


		$this->_variables['boletoitaudv'] = array("name" => "D&iacute;gito Verificador",
			   "type" => "textbox",
			   "help" => GetLang('boletoitauDV'),
			   "default" => "0",
			   "required" => true
			);

			$this->_variables['postagem'] = array("name" => "LINK de Repagamento",
			   "type" => "textbox",
			   "help" => "URL para Repagamento",
			   "default" => "%%GLOBAL_ShopPath%%/modules/checkout/boletoitau/boleto_itau.php?boleto=",
			   "required" => true
			);
			
			$this->_variables['imagems'] = array("name" => "IMAGEM de Repagamento",
			   "type" => "textbox",
			   "help" => "URL para Repagamento",
			   "default" => "%%GLOBAL_ShopPath%%/modules/checkout/boletoitau/images/logo.gif",
			   "required" => true
			);
			
			
	$this->_variables['helptext'] = array("name" => "Mais configura&ccedil;&otilde;es",
			   "type" => "textarea",
			   "help" => GetLang('boletoitauInst'),
			   "default" => "Voc&ecirc; escolheu pagar com Boleto Banc&aacute;rio do Banco Ita&uacute;.\nPara reimprimir seu boleto clique no bot&atilde;o abaixo.<br>",
			   "required" => true,
			   "rows" => 7
			);
			


			
			
			
		}

	function getofflinepaymentmessage($id){
	
			$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);
			
			$desc1 = $this->GetValue("desconto");

	$total = $order['ordgatewayamount'];
	$c = ($total/100)*$desc1;
	$valorpg = str_replace(",", ".",$total-$c);
	$valorfinal = number_format($valorpg, 2, '.', '');



if($desc1>"0"){
$ms = "<b>Pague seu pedido com ".$desc1."% de desconto.</b>";
} else {
$ms = '';
}

			$billhtml = "
<div class='FloatLeft'><b>Boleto Itau</b><br />
".$ms."
<br />
</div>

<br />

<form action='".$GLOBALS['ShopPath']."/modules/checkout/boletoitau/boleto_itau.php' method='post' name='boleto' target='_blank'>

<input type='hidden' name='item_id' value='".$id."' />
<input type='image' src='".$GLOBALS['ShopPath']."/modules/checkout/boletoitau/images/gerar_boleto.gif' value='submit'><br>
URL DO BOLETO: <a href='".$GLOBALS['ShopPath']."/modules/checkout/boletoitau/boleto_itau.php?boleto=".$id."'>".$GLOBALS['ShopPath']."/modules/checkout/boletoitau/boleto_itau.php?boleto=".$id."</a><br><br><br>


</form>


";
						
return $billhtml;

}
	
}

?>
