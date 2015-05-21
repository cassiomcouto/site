<?php

	class CHECKOUT_boletobancodobrasil extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_boletobancodobrasil";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_boletobancodobrasil()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = GetLang('boletobancodobrasilName');
			$this->_description = GetLang('boletobancodobrasilDesc');
			$this->SetImage('logo.gif');
			$this->_help = GetLang('boletobancodobrasilHelp');
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
			   "help" => GetLang('boletobancodobrasilContinentes'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);


			$this->_variables['boletobancodobrasilcedente'] = array("name" => "Cedente",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilCedente'),
			   "default" => "",
			   "required" => true
			);

			$this->_variables['boletobancodobrasilagencia'] = array("name" => "Agencia",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilAgencia'),
			   "default" => "",
			   "required" => true
				);


			$this->_variables['boletobancodobrasilconta'] = array("name" => "Conta",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilConta'),
			   "default" => "",
			   "required" => true
			);
		


			$this->_variables['boletobancodobrasilcarteira'] = array("name" => "Carteira",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilCarteira'),
			   "default" => "18",
			   "required" => true
			);
			
			$this->_variables['boletobancodobrasilconvenio'] = array("name" => "Convenio",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilConvenio'),
			   "default" => "0000000",
			   "required" => true
			);


		$this->_variables['boletobancodobrasilcontrato'] = array("name" => "Contrato",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilContrato'),
			   "default" => "0000000",
			   "required" => true
			);
			
				$this->_variables['boletobancodobrasilvariacaocarteira'] = array("name" => "Varia&ccedil;&atilde;o da Carteira",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilVariacaoCarteira'),
			   "default" => "-019",
			   "required" => true
			);


				$this->_variables['boletobancodobrasilformatacaoconvenio'] = array("name" => "Formata&ccedil;&atilde;o do Conv&ecirc;nio",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilFormatacaoConvenio'),
			   "default" => "7",
			   "required" => true
			);


				$this->_variables['boletobancodobrasilformatacaonossonumero'] = array("name" => "Formata&ccedil;&atilde;o do Nosso N&uacute;mero",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilFormatacaoNossoNumero'),
			   "default" => "7",
			   "required" => true
			);




	//_-----------------------------------------------------------------------------------------demonstrativos
	
			$this->_variables['demoum'] = array("name" => "Demonstra&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilDemoU'),
			   "default" => "REFERENTE A COMPRAS ONLINE",
			   "required" => false
			);
		
			$this->_variables['demodois'] = array("name" => "Demonstra&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilDemoD'),
			   "default" => "Duvidas e sugest&otilde;es entre em contato conosco:",
			   "required" => false
			);
		
					$this->_variables['demotres'] = array("name" => "Demonstra&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilDemoT'),
			   "default" => "sac@seusite.com.br | +55 xx 0000.0000",
			   "required" => false
			);
			
			
	//_------------------------------------------------------------------------------------------fim-demonstrativos
	




			
			$this->_variables['boletobancodobrasilinstrucaoum'] = array("name" => "Instru&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilInstrucaoUm'),
			   "default" => "Multa de R$ 3,00 por atraso.",
			   "required" => false
			);
		
			$this->_variables['boletobancodobrasilinstrucaodois'] = array("name" => "Instru&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilInstrucaoDois'),
			   "default" => "Apos o vencimento, pagavel apenas no Banco Real",
			   "required" => false
			);
		
					$this->_variables['boletobancodobrasilinstrucaotres'] = array("name" => "Instru&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilInstrucaoTres'),
			   "default" => "Fatura sujeita a protesto no SPC/SERASA",
			   "required" => false
			);
			
			
			$this->_variables['boletobancodobrasilinstrucaoquatro'] = array("name" => "Instru&ccedil;&atilde;o 4",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilInstrucaoQuatro'),
			   "default" => "Juros de mora de 0,1% ao dia.",
			   "required" => false
			);
			
	
				
			$this->_variables['boletobancodobrasilaceite'] = array("name" => "Aceite",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilAceite'),
			   "default" => "N",
			   "required" => false
			);
			
			
			$this->_variables['boletobancodobrasilespeciedoc'] = array("name" => "Esp&eacute;cie do documento",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilEspecieDoc'),
			   "default" => "DS",
			   "required" => false
			);

			$this->_variables['boletobancodobrasilespecie'] = array("name" => "Esp&eacute;cie de cobran&ccedil;a",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilEspecie'),
			   "default" => "R$",
			   "required" => false
			);
			

			$this->_variables['boletobancodobrasilcpfcnpj'] = array("name" => "CPF ou CNPJ do Boleto",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilCNPF'),
			   "default" => "",
			   "required" => false
			);
		
		
		$this->_variables['boletobancodobrasildiasparavencimento'] = array("name" => "Dias para vencimento",
			   "type" => "textbox",
			   "help" => GetLang('boletobancodobrasilVence'),
			   "default" => "10",
			   "required" => true
			);
			
						$this->_variables['postagem'] = array("name" => "LINK de Repagamento",
			   "type" => "textbox",
			   "help" => "URL para Repagamento",
			   "default" => "%%GLOBAL_ShopPath%%/modules/checkout/boletobancodobrasil/boleto_bancodobrasil.php?boleto=",
			   "required" => true
			);
			
			$this->_variables['imagems'] = array("name" => "IMAGEM de Repagamento",
			   "type" => "textbox",
			   "help" => "URL para Repagamento",
			   "default" => "%%GLOBAL_ShopPath%%/modules/checkout/boletobancodobrasil/images/logo.gif",
			   "required" => true
			);
		


	$this->_variables['helptext'] = array("name" => "Mais configura&ccedil;&otilde;es",
			   "type" => "textarea",
			   "help" => GetLang('boletobancodobrasilInst'),
			   "default" => "Voc&ecirc; escolheu pagar com Boleto Banc&aacute;rio do Banco do Brasil.\nPara reimprimir seu boleto clique no bot&atilde;o abaixo.<br>",
			   "required" => true,
			   "rows" => 7
			);
			


			
			
			
		}

	function getofflinepaymentmessage($id){
	
			$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);
			$billhtml = "
<div class='FloatLeft'><b>Boleto Banco de Brasil</b>
</div>
<br />
".$id."
<form action='".$GLOBALS['ShopPath']."/modules/checkout/boletobancodobrasil/boleto_bancodobrasil.php' method='post' name='boleto' target='_blank'>

<input type='hidden' name='item_id' value='".$id."' />
<input type='image' src='".$GLOBALS['ShopPath']."/modules/checkout/boletobancodobrasil/images/gerar_boleto.gif' value='submit'><br>
URL DO BOLETO: <a href='".$GLOBALS['ShopPath']."/modules/checkout/boletobancodobrasil/boleto_bancodobrasil.php?boleto=".$id."'>".$GLOBALS['ShopPath']."/modules/checkout/boletobancodobrasil/boleto_bancodobrasil.php?boleto=".$id."</a><br><br><br>


</form>


";
						
return $billhtml;

}
	
}

?>
