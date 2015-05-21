<?php

	class CHECKOUT_MERCADOPAGO extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_mercadopago";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_MERCADOPAGO()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = 'MercadoPago - ML';
			$this->_description = 'Pagamento online com mercadopago';
			$this->SetImage('logo.gif');
			$this->_help = 'Pague agora seus pagamento online com mercadopago';
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
			   "help" => 'Nome do Modulo',
			   "default" => "MercadoPago Pagamentos",
			   "required" => true
			);


			$this->_variables['availablecountries'] = array("name" => "Continentes",
			   "type" => "dropdown",
			   "help" => GetLang('PagContinente'),
			   "default" => "all",
			   "required" => true,
			   "options" => GetCountryListAsNameValuePairs(),
				"multiselect" => true
			);


			$this->_variables['pagemail'] = array("name" => "Numero da Conta",
			   "type" => "textbox",
			   "help" => '',
			   "default" => "000000000",
			   "required" => true
			);
			
						$this->_variables['token'] = array("name" => "Chave Criptografada",
			   "type" => "textbox",
			   "help" => 'Ponha o token de retorno automatico de pedidos.',
			   "default" => "chave-criptografada",
			   "required" => false
			);
			
			$this->_variables['sonda'] = array("name" => "Sonda",
			   "type" => "textbox",
			   "help" => 'Ponha a sonda de verificacao de pedidos.',
			   "default" => "",
			   "required" => false
			);
			
			$this->_variables['acrecimo'] = array("name" => "Acrecimo em %",
			   "type" => "textbox",
			   "help" => '',
			   "default" => "0.00",
			   "required" => true
			);
			
			$this->_variables['jurosde'] = array("name" => "Sem Juros ate",
			   "type" => "dropdown",
			   "help" => 'Quantidade de Vezes a Ser Dividido o juros iniciara.',
			   "default" => '0',
			   "options" => array("Nenhuma"=>"0","1x"=>"1","2x"=>"2","3x"=>"3","4x"=>"4","5x"=>"5","6x"=>"6","7x"=>"7","8x"=>"8","9x"=>"9","10x"=>"10","11x"=>"11","12x"=>"12","13x"=>"13","14x"=>"14","15x"=>"15"),
			   "required" => true
			);

						
			
	$this->_variables['helptext'] = array("name" => "Mais configura&ccedil;&otilde;es",
			   "type" => "textarea",
			   "help" => GetLang('PagInst'),
			   "default" => "Voc&ecirc; escolheu pagar com PagSeguro.\nPara acessar novamente o pagamento clique no bot&atilde;o abaixo.<br>",
			   "required" => true,
			   "rows" => 7
			);
			
			
			
			

			$this->_variables['helptext'] = array("name" => 'Repagamento (Nao Modificar)',
		   "type" => "textarea",
		   "help" => 'Codigo HTML de Repagamento, Nao Modificar',
		   "default" => "<a href=\"javascript:window.open('%%GLOBAL_ShopPath%%/modules/checkout/mercadopago/repagar.php?pedido=%%GLOBAL_OrderId%%','popup','width=800,height=800,scrollbars=yes');void(0);\"><img src='%%GLOBAL_ShopPath%%/modules/checkout/mercadopago/images/final.gif' border='0'></a>
<br>",
		   "rows" => 7,
		   "required" => true
		);
			
			
			
			
		}

	function getofflinepaymentmessage(){
	
	// Load the pending order
			$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);

			// Fetch the customer details
			$query = sprintf("SELECT * FROM [|PREFIX|]customers WHERE customerid='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($order['ordcustid']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$customer = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
	
	
	
$desc1 = $this->GetValue("acrecimo");

	$total = $order['ordgatewayamount'];
	$c = ($total/100)*$desc1;
	$valorpg = str_replace(",", ".",$total+$c);
	$valorfinal = number_format($valorpg, 2, '.', '');



if($desc1>"0"){
$ms = "<b>Total de: ".$valorfinal." Com ".$desc1."% de Acrecimo.</b>";
} else {
$ms = "<b>Total de: ".$valorfinal." Sem Acrecimo.</b>";
}
			

$billhtml = "
<div class='FloatLeft'><b>MercadoPago Pagamentos Online</b>
<br />
".$ms."
<br />
<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/repagar.php?pedido=".$order['orderid']."','popup','width=800,height=800,scrollbars=yes');void(0);\">
<img src='".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/images/final.gif' border='0'></a>
</div><br>
<div style='display:none;'>
Link Direto:<br>
<a href='".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/repagar.php?pedido=".$order['orderid']."' target='_blank'>".$GLOBALS['ShopPath']."/modules/checkout/mercadopago/repagar.php?pedido=".$order['orderid']."</a><br>
</div>
<br />
";
						
return $billhtml;

}

}

?>