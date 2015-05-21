<?php

	class CHECKOUT_MOIP extends ISC_CHECKOUT_PROVIDER
	{

		/*
			Does this payment provider require SSL?
		*/
		var $_requiresSSL = false;

		/*
			The help text that will be displayed post-checkout
		*/
		var $_paymenthelp = "";

		var	$_id = "checkout_moip";

		/*
			Checkout class constructor
		*/
		function CHECKOUT_MOIP()
		{
			// Setup the required variables for the bank deposit checkout module
			parent::__construct();
			$this->_name = "Moip";
			$this->_description = "Moip";
			$this->SetImage('logo.gif');
			$this->_help = "Moip";
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

		function SetCustomVars()
		{

					$this->_variables['displayname'] = array("name" => "Nome",
			   "type" => "textbox",
			   "help" => 'Nome do Modulo',
			   "default" => "Moip",
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


			$this->_variables['pagemail'] = array("name" => "E-mail cadastrado",
			   "type" => "textbox",
			   "help" => GetLang('PagEmail'),
			   "default" => "",
			   "required" => true
			);
			
			$this->_variables['acrecimo'] = array("name" => "Acrecimo em %",
			   "type" => "textbox",
			   "help" => '',
			   "default" => "0",
			   "required" => true
			);
			
						$this->_variables['jurosde'] = array("name" => "Sem Juros ate",
			   "type" => "dropdown",
			   "help" => 'Quantidade de Vezes a Ser Dividido o juros iniciara.',
			   "default" => '0',
			   "options" => array("Nenhuma"=>"0","1x"=>"1","2x"=>"2","3x"=>"3","4x"=>"4","5x"=>"5","6x"=>"6","7x"=>"7","8x"=>"8","9x"=>"9","10x"=>"10","11x"=>"11","12x"=>"12","13x"=>"13","14x"=>"14","15x"=>"15"),
			   "required" => true
			);
						
			
			
			$this->_variables['htmlmoip'] = array("name" => "HTML da sua Imagem do Moip",
			   "type" => "textarea",
			   "help" => GetLang('PagImagemHTML'),
			   "default" => "",
			   "required" => true,
			   "rows" => 7
			);
			
$this->_variables['helptext'] = array("name" => 'Repagamento (Nao Modificar)',
		   "type" => "textarea",
		   "help" => 'Codigo HTML de Repagamento, Nao Modificar',
		   "default" => "<a href=\"javascript:window.open('%%GLOBAL_ShopPath%%/modules/checkout/moip/repagar.php?pedido=%%GLOBAL_OrderId%%','popup','width=800,height=800,scrollbars=yes');void(0);\"><img src='%%GLOBAL_ShopPath%%/modules/checkout/moip/images/final.gif' border='0'></a>
<br>",
		   "rows" => 7,
		   "required" => true
		);
			


			
			
			
		}

	function getofflinepaymentmessage($id){
	
	// Load the pending order
			$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);

$desc1 = $this->GetValue("acrecimo");

	$total = $order['ordgatewayamount'];
	$c = ($total/100)*$desc1;
	$valorpg = str_replace(",", ".",$total+$c);
	$valorfinal = number_format($valorpg, 2, '.', '');



if($desc1>"0"){
$ms = "<b>Total de: ".$valorfinal." Com ".$desc1."% de Acrecimo.</b>";
} else {
$ms = "<b>Total de: ".$valorfinal." Sem Acecimo.</b>";
}


			$billhtml = "
<div class='FloatLeft'><b>Pagamento por MOIP</b>
<br />
".$ms."
<br />
<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/moip/repagar.php?pedido=".$id."','popup','width=800,height=800,scrollbars=yes');void(0);\"><img src='".$GLOBALS['ShopPath']."/modules/checkout/moip/images/final.gif' border='0'></a>
</div><br><br /><br /><br /><br />
Link Direto:<br>
<a href='".$GLOBALS['ShopPath']."/modules/checkout/moip/repagar.php?pedido=".$id."' target='_blank'>".$GLOBALS['ShopPath']."/modules/checkout/moip/repagar.php?pedido=".$id."</a>
<br />Voc&ecirc; pode pagar com:<br />".$this->GetValue("htmlmoip")."


";
						
return $billhtml;

}

}

?>