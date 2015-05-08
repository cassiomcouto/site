<?php
class CHECKOUT_PAYPALBRASIL extends ISC_CHECKOUT_PROVIDER
{

	/**
	 * @var boolean Does this payment provider require SSL?
	 */
	protected $requiresSSL = false;

	/**
	 * @var boolean Does this provider support orders from more than one vendor?
	 */
	protected $supportsVendorPurchases = true;

	/**
	 * @var boolean Does this provider support shipping to multiple addresses?
	 */
	protected $supportsMultiShipping = true;

	/**
	 * @var string The shop owners PayPal email address
	 */
	private $_email = "";

	/**
	 * @var string Should the order be passed through in test mode?
	 */
	private $_testmode = "";

	/**
	 *	Checkout class constructor
	 */
	public function __construct()
	{
		// Setup the required variables for the PayPal checkout module
		parent::__construct();
		$this->_name = 'PayPal Brasil';
		$this->_image = "paypal_logo.gif";
		$this->_description = 'Modulo de Pagamento Paypal Brasil - www.loja5.com.br';
		$this->_help = 'Modulo de Pagamento Paypal Brasil - www.loja5.com.br';
		$this->_paymenttype = PAYMENT_PROVIDER_OFFLINE;
	}

	public function SetCustomVars()
	{
		$this->_variables['displayname'] = array("name" => "Nome",
		   "type" => "textbox",
		   "help" => '',
		   "default" => $this->GetName(),
		   "required" => true
		);

		$this->_variables['email'] = array("name" => "PayPal Email",
		   "type" => "textbox",
		   "help" => '',
		   "default" => "",
		   "required" => true
		);

		$this->_variables['mode'] = array("name" => "Modo Teste",
		   "type" => "dropdown",
		   "help" => '',
		   "default" => "no",
		   "required" => true,
		   "options" => array('Nao' => "NO",
						  'Sim' => "YES"
			),
			"multiselect" => false
		);
	}
	
	
	function getofflinepaymentmessage(){
	
	$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);

$billhtml = "
<div class='FloatLeft'><b>Pagamento Online PayPal</b>
<br />
<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/paypalbrasil/repagar.php?pedido=".$order['orderid']."','popup','width=800,height=800,scrollbars=yes');void(0);\">
<img src='".$GLOBALS['ShopPath']."/modules/checkout/paypalbrasil/images/final.gif' border='0'></a>
</div><br>
<div style='display:none;'>
Link Direto:<br>
<a href='".$GLOBALS['ShopPath']."/modules/checkout/paypalbrasil/repagar.php?pedido=".$order['orderid']."' target='_blank'>".$GLOBALS['ShopPath']."/modules/checkout/paypalbrasil/repagar.php?pedido=".$order['orderid']."</a>
</div>";
						
return $billhtml;

}

	
	
}
?>