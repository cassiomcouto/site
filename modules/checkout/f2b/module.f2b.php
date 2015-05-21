<?php
class CHECKOUT_F2B extends ISC_CHECKOUT_PROVIDER
{

	/**
	 * @var boolean Does this payment provider require SSL?
	 */
	var $_requiresSSL = false;

	/**
	 *	Checkout class constructor
	 */
	public function __construct()
	{
		// Setup the required variables for the PayPal checkout module
		parent::__construct();
		$this->_name = GetLang('f2bName');
		$this->_image = "logo.gif";
		$this->_description = GetLang('f2bDesc');
		$this->_help = sprintf(GetLang('f2bHelp'), $GLOBALS['ShopPathSSL']);
		$this->_enabled = $this->CheckEnabled();
		$this->_height = 0;
		$this->_paymenttype = PAYMENT_PROVIDER_OFFLINE;
	}


	public function SetCustomVars()

	{

		$this->_variables['displayname'] = array("name" => "Nome de exibi&ccedil;&atilde;o",
		   "type" => "textbox",
		   "help" => GetLang('f2bDesc'),
		   "default" => "F2b Pagamento Online",
		   "required" => true
		);
		
		$this->_variables['conta'] = array("name" => "Sua conta da F2b",
		   "type" => "textbox",
		   "help" => GetLang('f2bConta'),
		   "default" => "",
		   "required" => true
		);

		$this->_variables['sacador'] = array("name" => "Titular da Conta F2b",
		   "type" => "textbox",
		   "help" => GetLang('f2bSacador'),
		   "default" => "",
		   "required" => true
		);


		$this->_variables['senha'] = array("name" => "Senha",
		   "type" => "textbox",
		   "help" => GetLang('f2bSenha'),
		   "default" => "",
		   "required" => true
		);



		$this->_variables['taxa'] = array("name" => "Taxa da cobran&ccedil;a",
		   "type" => "textbox",
		   "help" => GetLang('f2bCobranca'),
		   "default" => "0.00",
		   "required" => true
		);
	
	
	$this->_variables['tipotaxa'] = array("name" => "Tipo de taxa",
		   "type" => "dropdown",
		   "help" => GetLang('f2bCobranca'),
		   "options" => array('R$' => '0','%' => '1'),
		    "default" => "",
		   "required" => true
		);
		
		
				$this->_variables['comvencimento'] = array("name" => "Config. Vencimento",
		   "type" => "textbox",
		   "help" => GetLang('f2bVenc'),
		   "default" => "10",
		   "required" => true
		);


				$this->_variables['helptext'] = array("name" => "Config. Vencimento",
		   "type" => "textarea",
		   "help" => GetLang('f2bVenc'),
		   "default" => "Você escolheu pagar com F2b, estamos aguardando seu pagamento.",
		   "required" => true
		);
		
		$this->_variables['helptext'] = array("name" => 'Repagamento (Nao Modificar)',
		   "type" => "textarea",
		   "help" => 'Codigo HTML de Repagamento, Nao Modificar',
		   "default" => "<a href=\"javascript:window.open('%%GLOBAL_ShopPath%%/modules/checkout/f2b/repagar.php?pedido=%%GLOBAL_OrderId%%','popup','width=800,height=800,scrollbars=yes');void(0);\"><img src='%%GLOBAL_ShopPath%%/modules/checkout/f2b/images/gerar_boleto.gif' border='0'></a>
<br>",
		   "rows" => 7,
		   "required" => true
		);


		
		
	}



	function getofflinepaymentmessage($id){
	
	
	$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);
$billhtml = "
<div class='FloatLeft'><b>Pagamento Online F2b</b>
<br />
<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/f2b/repagar.php?pedido=".$id."','popup','width=800,height=800,scrollbars=yes');void(0);\">
<img src='".$GLOBALS['ShopPath']."/modules/checkout/f2b/images/gerar_boleto.gif' border='0'></a>
</div><br>
<br /><br /><br /><br />
Link Direto:<br>
<a href='".$GLOBALS['ShopPath']."/modules/checkout/f2b/repagar.php?pedido=".$id."' target='_blank'>".$GLOBALS['ShopPath']."/modules/checkout/f2b/repagar.php?pedido=".$id."</a><br>
";
	
return $billhtml;

}









}
?>
