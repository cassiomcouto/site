<?php

	class CHECKOUT_DEPOSITO extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_deposito";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_DEPOSITO()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = "Deposito Bancario";
			$this->_description = "Dados Bancarios para Deposito ou transferencia entre contas";
			$this->_enabled = $this->CheckEnabled();
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

			$this->_variables['displayname'] = array("name" => "Nome",
			   "type" => "textbox",
			   "help" => GetLang('DisplayNameHelp'),
			   "default" => $this->GetName(),
			   "required" => true
			);

			$this->_variables['availablecountries'] = array("name" => "Paises Aceitos",
			   "type" => "dropdown",
			   "help" => GetLang('BankDepositAvailableCountriesHelp'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);


			$this->_variables['desconto'] = array("name" => "Desconto em %",
			   "type" => "textbox",
			   "help" => 'Desconto Sobre o Valor Total em Porcentagem',
			   "default" => '0',
			   "required" => false
			);

			$this->_variables['helptext'] = array("name" => "Informações da Conta",
			   "type" => "textarea",
			   "help" => GetLang('BankDepositAccountInformationHelp'),
			   "default" => "Banco: Banco do Brasil\nAgencia: 64646\nNome: John Smith\nConta: XXXXXXXXXXXX\n\nMais Instrucoes e Detalhes.",
			   "required" => true,
			   "rows" => 7
			);


		}


		/**
		*	Return the bank account details needed to pay for the order
		*/
		function getofflinepaymentmessage($id)
		{
		$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);
        $valorcerto = $order['ordtotalamount'];
$ped = $order['orderid'];

            $desconto = trim($this->GetValue("desconto"));

			$porcem = $valorcerto/100;
			$vezdes = $porcem*$desconto;
			$totalmenos = $valorcerto-$vezdes;
			$valord =number_format($totalmenos, 2, ',', '');

if($desconto != "0") {
$msg = "<h2>Pedido total de $valord com $desconto% de Desconto!</h2>";
} else {
$msg = "<h2>Pedido total de $valord!</h2>";
}

$dados = $this->GetValue("helptext");
$gerador = "$msg<br><br>$dados";

			return $gerador;
		}
	}

?>