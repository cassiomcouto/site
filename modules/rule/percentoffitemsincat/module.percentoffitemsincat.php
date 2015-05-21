<?php

class RULE_PERCENTOFFITEMSINCAT extends ISC_RULE
{
	private $amount;
	private $catids;
	protected $vendorSupport = true;

	public function __Construct($amount=0, $catids=array())
	{
		parent::__construct();

		$this->amount = $amount;
		$this->catids = $catids;

		$this->setName('PERCENTOFFITEMSINCAT');
		$this->displayName = GetLang($this->getName().'displayName');

		$this->addJavascriptValidation('amount', 'int', 0, 100);
		$this->addJavascriptValidation('catids', 'array');

		$this->addActionType('itemdiscount');
		$this->ruleType = 'Product';
	}

	public function initialize($data)
	{
		parent::initialize($data);

		$tmp = unserialize($data['configdata']);

		$this->amount = $tmp['var_amount'];
		$this->catids = $tmp['var_catids'];
	}

	public function initializeAdmin()
	{
		if (!empty($this->catids)) {
			$selectedCategories = explode(',', $this->catids);
		} else {
			$selectedCategories = array();
		}

		$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
		$GLOBALS['CategoryList'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions($selectedCategories, "<option %s value='%d'>%s</option>", 'selected="selected"', " ", false);

		if (count($selectedCategories) < 1) {
			$GLOBALS['AllCategoriesSelected'] = "selected=\"selected\"";
		}
	}

	public function isTrue()
	{
		$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');

		$cartProducts = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart(true);
		$found = false;

		foreach ($cartProducts as $key=>$product) {
			if (isset($product['type']) && $product['type'] == 'giftcertificate') {
				continue;
			}

			$productCats = explode(",", $product['data']['categoryids']);
			$ruleCats = explode(",", $this->catids);
			$apply = false;
			foreach ($ruleCats as $catid) {
				if (!in_array($catid, $productCats) && $catid != 0) {
					continue;
				}

				$apply = true;
				$found[] = $catid;
			}

			if(!$apply) {
				continue;
			}

			$productPrice = $product['product_price'];
			$discountAmount = ($this->amount / 100) * $productPrice;

			if ($discountAmount < 0) {
				$discountAmount = 0;
			}

			// Make sure items aren't going in to the negative
			if($productPrice - $discountAmount < 0) {
				$discountAmount = $productPrice;
			}

			$this->itemdiscount += $discountAmount * $product['quantity'];

			if(empty($product['discountRuleDiscounts'])) {
				$product['discountRuleDiscounts'] = array();
			}
			$product['discountRuleDiscounts'][$this->getDbId()] = $discountAmount;
			$GLOBALS['ISC_CLASS_CART']->api->setItemValue($key, 'discountRuleDiscounts', $product['discountRuleDiscounts']);
		}

		if (!empty($found)) {

			$catname = '';
			$catids = implode(',', $found);

			$query = "
					SELECT catname
					FROM [|PREFIX|]categories
					WHERE categoryid IN ($catids)
			";

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while($var = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$catname[] = $var['catname'];
			}
			if (isset($catname{1})) {
				$GLOBALS['ISC_CLASS_CART']->api->SetArrayPush('DISCOUNT_MESSAGES', sprintf(GetLang($this->getName().'DiscountMessagePlural'), $this->amount, implode(' and ',$catname)));
			} else {
				$GLOBALS['ISC_CLASS_CART']->api->SetArrayPush('DISCOUNT_MESSAGES', sprintf(GetLang($this->getName().'DiscountMessage'), $this->amount, implode(' and ',$catname)));
			}
			return true;
		}


		return false;
	}

	public function haltReset()
	{
		return false;
	}


}