<?php

	class CHECKOUT_BOLETOREAL extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_boletoreal";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_BOLETOREAL()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = GetLang('BoletoRealName');
			$this->_description = GetLang('BoletoRealDesc');
			$this->SetImage('logo.gif');
			$this->_help = GetLang('BoletoRealHelp');
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
			   "help" => GetLang('BoletoRealContinentes'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);


			$this->_variables['boletorealcedente'] = array("name" => "Cedente",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealCedente'),
			   "default" => "",
			   "required" => true
			);

			$this->_variables['boletorealagencia'] = array("name" => "Agencia",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealAgencia'),
			   "default" => "",
			   "required" => true
				);

			$this->_variables['boletorealconta'] = array("name" => "Conta",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealConta'),
			   "default" => "",
			   "required" => true
			);
		
			$this->_variables['boletorealcarteira'] = array("name" => "Carteira",
			   "type" => "dropdown",
			   "help" => GetLang('BoletoRealCarteira'),
			   "default" => "20",
			   "options" => array('Sem Registro' => '20', 'Com Registro' => '57'),
			   "required" => true,
			   "multiselection" => false
			);
			



	//_-----------------------------------------------------------------------------------------demonstrativos
	
			$this->_variables['demoum'] = array("name" => "Demonstra&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealDemoU'),
			   "default" => "REFERENTE A COMPRAS ONLINE",
			   "required" => false
			);
		
			$this->_variables['demodois'] = array("name" => "Demonstra&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealDemoD'),
			   "default" => "Duvidas e sugest&otilde;es entre em contato conosco:",
			   "required" => false
			);
		
					$this->_variables['demotres'] = array("name" => "Demonstra&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealDemoT'),
			   "default" => "sac@seusite.com.br | +55 xx 0000.0000",
			   "required" => false
			);
			
			
	//_------------------------------------------------------------------------------------------fim-demonstrativos
	




			
			$this->_variables['boletorealinstrucaoum'] = array("name" => "Instru&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealInstrucaoUm'),
			   "default" => "Multa de R$ 3,00 por atraso.",
			   "required" => false
			);
		
			$this->_variables['boletorealinstrucaodois'] = array("name" => "Instru&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealInstrucaoDois'),
			   "default" => "Apos o vencimento, pagavel apenas no Banco Real",
			   "required" => false
			);
		
					$this->_variables['boletorealinstrucaotres'] = array("name" => "Instru&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealInstrucaoTres'),
			   "default" => "Fatura sujeita a protesto no SPC/SERASA",
			   "required" => false
			);
			
			
			$this->_variables['boletorealinstrucaoquatro'] = array("name" => "Instru&ccedil;&atilde;o 4",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealInstrucaoQuatro'),
			   "default" => "Juros de mora de 0,1% ao dia.",
			   "required" => false
			);
			
	
	
	
	
	
	
			
			
			$this->_variables['boletorealaceite'] = array("name" => "Aceite",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealAceite'),
			   "default" => "A",
			   "required" => false
			);
			
			
			$this->_variables['boletorealespeciedoc'] = array("name" => "Esp&eacute;cie do documento",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealEspecieDoc'),
			   "default" => "DM",
			   "required" => false
			);

			$this->_variables['boletorealespecie'] = array("name" => "Esp&eacute;cie de cobran&ccedil;a",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealEspecie'),
			   "default" => "Real",
			   "required" => false
			);
			

			$this->_variables['boletorealcpfcnpj'] = array("name" => "CPF ou CNPJ do Boleto",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealCNPF'),
			   "default" => "",
			   "required" => false
			);
		
		
		$this->_variables['boletorealdiasparavencimento'] = array("name" => "Dias para vencimento",
			   "type" => "textbox",
			   "help" => GetLang('BoletoRealVence'),
			   "default" => "10",
			   "required" => true
			);
			
			$this->_variables['postagem'] = array("name" => "LINK de Repagamento",
			   "type" => "textbox",
			   "help" => "URL para Repagamento",
			   "default" => "%%GLOBAL_ShopPath%%/modules/checkout/boletoreal/boleto_real.php?boleto=",
			   "required" => true
			);
			
			$this->_variables['imagems'] = array("name" => "IMAGEM de Repagamento",
			   "type" => "textbox",
			   "help" => "URL para Repagamento",
			   "default" => "%%GLOBAL_ShopPath%%/modules/checkout/boletoreal/images/logo.gif",
			   "required" => true
			);
		
			
			
	$this->_variables['helptext'] = array("name" => "Mais configura&ccedil;&otilde;es",
			   "type" => "textarea",
			   "help" => GetLang('BoletoRealInst'),
			   "default" => "Voc&ecirc; escolheu pagar com Boleto Banc&aacute;rio do Banco Real.\nPara reimprimir seu boleto clique no bot&atilde;o abaixo.<br>",
			   "required" => true,
			   "rows" => 7
			);
			


			
			
			
		}

	function getofflinepaymentmessage(){
	
			$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);


$billhtml = "
<div class='FloatLeft'><b>Boleto Banco Real</b>
</div>
<br />

<form action='".$GLOBALS['ShopPath']."/modules/checkout/boletoreal/boleto_real.php' method='post' name='boleto' target='_blank'>

<input type='hidden' name='item_id' value='".$id."' />
<input type='image' src='".$GLOBALS['ShopPath']."/modules/checkout/boletoreal/images/gerar_boleto.gif' value='submit'><br>
URL DO BOLETO: <a href='".$GLOBALS['ShopPath']."/modules/checkout/boletoreal/boleto_real.php?boleto=".$id."'>".$GLOBALS['ShopPath']."/modules/checkout/boletoreal/boleto_real.php?boleto=".$id."</a><br><br><br>


</form>


";						
return $billhtml;

}
	
}

?>
