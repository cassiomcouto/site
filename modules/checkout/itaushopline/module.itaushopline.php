<?php
class CHECKOUT_ITAUSHOPLINE extends ISC_CHECKOUT_PROVIDER
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
		parent::__construct();
		$this->_name = "Itau Shopline";
		$this->_image = "images/pagar.gif";
		$this->_description = "Modulo de Pagamento ItauShopline";
                $this->_help = "Modulo de Pagamento Itau shopline";
		$this->_enabled = $this->CheckEnabled();
		$this->_height = 0;
		$this->_paymenttype = PAYMENT_PROVIDER_OFFLINE;
	}


	public function SetCustomVars()

	{
	
	$this->_variables['desconto'] = array("name" => "Desconto em %",
			   "type" => "textbox",
			   "help" => '',
			   "default" => "0",
			   "required" => true
			);

		$this->_variables['codigo'] = array("name" => 'Codigo da Empresa 26 Chaves',
		   "type" => "textbox",
		   "help" => 'Codigo da Empresa com 26 Chaves Todas Maiusculas',
		   "default" => "",
		   "required" => true
		);

		$this->_variables['chave'] = array("name" => 'Chave Criptografada',
		   "type" => "textbox",
		   "help" => 'Ponha a Chave Criptografada 16 Chaves Todas Maiusculas',
		   "default" => "",
		   "required" => true
		);

		$this->_variables['url'] = array("name" => 'Url de Retorno',
		   "type" => "textbox",
		   "help" => 'Url de Retorno',
		   "default" => "",
		   "required" => true
		);

		$this->_variables['abs1'] = array("name" => 'Observação 01',
		   "type" => "textbox",
		   "help" => 'Observação 01',
		   "default" => "",
		   "required" => true
		);

		$this->_variables['abs2'] = array("name" => 'Observação 02',
		   "type" => "textbox",
		   "help" => 'Observação 02',
		   "default" => "",
		   "required" => true
		);

		$this->_variables['abs3'] = array("name" => 'Observação 03',
		   "type" => "textbox",
		   "help" => 'Observação 03',
		   "default" => "",
		   "required" => true
		);
		

		
		
	}



	function getofflinepaymentmessage(){

$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);

$desc1 = $this->GetValue("desconto");

	$total = $order['ordgatewayamount'];
	$c = ($total/100)*$desc1;
	$valorpg = str_replace(",", ".",$total-$c);
	$valorfinal = number_format($valorpg, 2, '.', '');
	
	if($desc1>"0"){
$ms = "<b>Total de: R$ ".$valorfinal." com ".$desc1."% de desconto.</b>";
} else {
$ms = '';
}
	
	
			$billhtml = "
<div class='FloatLeft'><b>Itau Shopline</b>
<br />
".$ms."
<br />
<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/itaushopline/pagar.php?pedido=".isc_html_escape($order['orderid'])."','popup','width=800,height=800,scrollbars=yes');void(0);\">
<img src='".$GLOBALS['ShopPath']."/modules/checkout/itaushopline/images/pagar.gif' border='0'></a>
</div>
<div style='display:none;'>
Link de Pagamento Direto:<br>
<a href='".$GLOBALS['ShopPath']."/modules/checkout/itaushopline/pagar.php?pedido=".isc_html_escape($order['orderid'])."' target='_blank'>".$GLOBALS['ShopPath']."/modules/checkout/itaushopline/pagar.php?pedido=".isc_html_escape($order['orderid'])."</a>
</div>
";
	
return $billhtml;
}









}
?>
