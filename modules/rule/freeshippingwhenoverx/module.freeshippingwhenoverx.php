<?php

class RULE_FREESHIPPINGWHENOVERX extends ISC_RULE
{
	private $amount;
	protected $vendorSupport = true;

	public function __Construct()
	{
		parent::__construct();

		$this->setName('FREESHIPPINGWHENOVERX');

		$currency = GetDefaultCurrency();
		if ($currency['currencystringposition'] == "LEFT") {
			$x = $currency['currencystring'] . "X";
		}
		else {
			$x = "X" . $currency['currencystring'];
		}
		$this->displayName = sprintf(GetLang($this->getName().'displayName'), $x);

		$this->addJavascriptValidation('amount', 'int');
		$this->addActionType('freeshipping');

		$this->ruleType = 'Order';
	}

	public function initialize($data)
	{

		parent::initialize($data);

		$tmp = unserialize($data['configdata']);

		$this->amount = $tmp['varn_amount'];
	}

	public function initializeAdmin()
	{
		$currency = GetDefaultCurrency();
		if ($currency['currencystringposition'] == "LEFT") {
			$GLOBALS['CurrencyLeft'] = $currency['currencystring'];
		}
		else {
			$GLOBALS['CurrencyRight'] =  $currency['currencystring'];
		}
	}

	public function isTrue()
	{
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
		$cartProducts = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();

		$total = 0;

		foreach($cartProducts as $product) {
			$total += $product['product_price']*$product['quantity'];
		}

		if ($total >= $this->amount) {
			$GLOBALS['ISC_CLASS_CART']->api->set('FREE_SHIPPING', true);
			return true;
		}

		return false;
	}

	public function haltReset()
	{
		return false;
	}


}