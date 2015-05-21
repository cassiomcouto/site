<?php

	class CHECKOUT_boletobradesco extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_boletobradesco";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_boletobradesco()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = GetLang('boletobradescoName');
			$this->_description = GetLang('boletobradescoDesc');
			$this->SetImage('logo.gif');
			$this->_help = GetLang('boletobradescoHelp');
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
			   "help" => GetLang('boletobradescoContinentes'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);


			$this->_variables['boletobradescocedente'] = array("name" => "Cedente",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoCedente'),
			   "default" => "",
			   "required" => true
			);

			$this->_variables['boletobradescoagencia'] = array("name" => "Agencia",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoAgencia'),
			   "default" => "",
			   "required" => true
				);


$this->_variables['boletobradescoagenciadv'] = array("name" => "D&iacute;gito Verificador (Ag&ecirc;ncia)",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoagenciaDV'),
			   "default" => "0",
			   "required" => true
			);


			$this->_variables['boletobradescoconta'] = array("name" => "Conta",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoConta'),
			   "default" => "",
			   "required" => true
			);
		


		$this->_variables['boletobradescocontadv'] = array("name" => "D&iacute;gito Verificador (Conta)",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescocontaDV'),
			   "default" => "0",
			   "required" => true
			);


			$this->_variables['boletobradescocarteira'] = array("name" => "Carteira",
			   "type" => "dropdown",
			   "help" => GetLang('boletobradescoCarteira'),
			   "default" => "06",
			   "options" => array('06' => '06'),
			   "required" => true,
			   "multiselection" => false
			);
			



	//_-----------------------------------------------------------------------------------------demonstrativos
	
			$this->_variables['demoum'] = array("name" => "Demonstra&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoDemoU'),
			   "default" => "REFERENTE A COMPRAS ONLINE",
			   "required" => false
			);
		
			$this->_variables['demodois'] = array("name" => "Demonstra&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoDemoD'),
			   "default" => "Duvidas e sugest&otilde;es entre em contato conosco:",
			   "required" => false
			);
		
					$this->_variables['demotres'] = array("name" => "Demonstra&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoDemoT'),
			   "default" => "sac@seusite.com.br | +55 xx 0000.0000",
			   "required" => false
			);
			
			
	//_------------------------------------------------------------------------------------------fim-demonstrativos
	




			
			$this->_variables['boletobradescoinstrucaoum'] = array("name" => "Instru&ccedil;&atilde;o 1",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoInstrucaoUm'),
			   "default" => "Multa de R$ 3,00 por atraso.",
			   "required" => false
			);
		
			$this->_variables['boletobradescoinstrucaodois'] = array("name" => "Instru&ccedil;&atilde;o 2",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoInstrucaoDois'),
			   "default" => "Apos o vencimento, pagavel apenas no Banco Real",
			   "required" => false
			);
		
					$this->_variables['boletobradescoinstrucaotres'] = array("name" => "Instru&ccedil;&atilde;o 3",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoInstrucaoTres'),
			   "default" => "Fatura sujeita a protesto no SPC/SERASA",
			   "required" => false
			);
			
			
			$this->_variables['boletobradescoinstrucaoquatro'] = array("name" => "Instru&ccedil;&atilde;o 4",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoInstrucaoQuatro'),
			   "default" => "Juros de mora de 0,1% ao dia.",
			   "required" => false
			);
			
	
				
			$this->_variables['boletobradescoaceite'] = array("name" => "Aceite",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoAceite'),
			   "default" => "",
			   "required" => false
			);
			
			
			$this->_variables['boletobradescoespeciedoc'] = array("name" => "Esp&eacute;cie do documento",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoEspecieDoc'),
			   "default" => "DS",
			   "required" => false
			);

			$this->_variables['boletobradescoespecie'] = array("name" => "Esp&eacute;cie de cobran&ccedil;a",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoEspecie'),
			   "default" => "R$",
			   "required" => false
			);
			

			$this->_variables['boletobradescocpfcnpj'] = array("name" => "CPF ou CNPJ do Boleto",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoCNPF'),
			   "default" => "",
			   "required" => false
			);
		
		
		$this->_variables['boletobradescodiasparavencimento'] = array("name" => "Dias para vencimento",
			   "type" => "textbox",
			   "help" => GetLang('boletobradescoVence'),
			   "default" => "10",
			   "required" => true
			);
		


	$this->_variables['helptext'] = array("name" => "Mais configura&ccedil;&otilde;es",
			   "type" => "textarea",
			   "help" => GetLang('boletobradescoInst'),
			   "default" => "Voc&ecirc; escolheu pagar com Boleto Banc&aacute;rio do Banco Bradesco.\nPara reimprimir seu boleto clique no bot&atilde;o abaixo.<br>",
			   "required" => true,
			   "rows" => 7
			);
			


			
			
			
		}

	function GetOfflinePaymentMessage(){
	
						$endereco = $_SERVER ['REQUEST_URI'];
						$explonome = explode('.',$endereco);

						$explonomex = explode('/',$endereco);
						$number = count($explonomex)-1;
				
						
	
	if($explonomex[$number]=="areadecliente.php?action=order_status"){
	$ZUROX_ARRUMOU = $GLOBALS['ZUROX_ARRUMOU'];
	}else{
	$ZUROX_ARRUMOU = $_COOKIE['SHOP_ORDER_TOKEN'];
	}
	
	// Load the pending order
			$order = LoadPendingOrderByToken($ZUROX_ARRUMOU);
			$billhtml = "
<div class='FloatLeft'><b>Boleto Bradesco</b>
<br />
<a href='javascript:document.boleto.submit();void(0); window.location.href=\"".$GLOBALS['ShopPath']."\";'>
<img src='".$GLOBALS['ShopPath']."/modules/checkout/boletobradesco/images/gerar_boleto.gif' border='0'></a>
</div>

<br />

<form action='".$GLOBALS['ShopPath']."/modules/checkout/boletobradesco/boleto_bradesco.php' method='post' name='boleto' target='_blank'>

<input type='hidden' name='item_id' value='".isc_html_escape($order['orderid'])."' />

</form>


";
						
return $billhtml;

}
	
}

?>
