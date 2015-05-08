<?php

class ISC_CART_API
{
	/**
	 * @var array An array of errors that may have been encountered.
	 */
	protected $errors = array();

	/**
	 * @var array An array containing the session of the cart we're managing.
	 */
	protected $cartSession = array();

	/**
	 * @var array An array containing the cached versions of all of the products in their cart with the product information.
	 */
	protected $cartProducts = array();

	/**
	 * @var boolean True if the error messages being returned are on a product level.
	 */
	public $productLevelError = false;

	/**
	 * @var boolean True if $cartProducts only contains gift certificates.
	 */
	protected $onlyGiftCertificates = false;

	/**
	 * Set an error message.
	 *
	 * @param string The error message to be set.
	 */
	protected function SetError($error)
	{
		$this->errors[] = $error;
	}

	/**
	 * Return an array of errors that have been encountered.
	 *
	 * @return array An array of error messages.
	 */
	public function GetErrors()
	{
		return $this->errors;
	}

	/**
	 * Check if one or more errors have occurred.
	 *
	 * @return boolean True if one or more errors have occurred, false if not.
	 */
	public function HasErrors()
	{
		if(!empty($this->errors)) {
			return true;
		}

		return false;
	}

	/**
	 * Generate a unique hash string based on the contents of the current cart.
	 *
	 * @return string The generated cart hash.
	 */
	public function GenerateCartHash()
	{
		if(!isset($this->cartSession['ITEMS'])) {
			return '';
		}
		$itemIds = array();
		foreach($this->cartSession['ITEMS'] as $item) {
			if(isset($item['variation_id'])) {
				$itemHash = $item['product_id'].'.'.$item['variation_id'];
			}
			else {
				$itemHash = $item['product_id'].'.0';
			}
			$itemHash .= '.'.$item['quantity'].'.'.$item['product_price'];
			$itemIds[] = $itemHash;
		}
		sort($itemIds);
		$itemIds = implode(',', $itemIds);
		return md5($itemIds);
	}

	/**
	 * Set the cart session that's currently being managed by the cart manager.
	 * Most of the time, this will just be $_SESSION['CART'].
	 *
	 * @param array The cart session we'll be looking after.
	 */
	public function SetCartSession(&$session)
	{
		$this->cartSession = &$session;
		if(!isset($this->cartSession['ITEMS'])) {
			$this->cartSession['ITEMS'] = array();
		}
		$this->cartSession['LAST_UPDATED'] = time();
	}

	/**
	 * Set a value in the cart session with the defined name.
	 *
	 * @param string The name of the value to set.
	 * @param mixed The value.
	 * @return boolean True if successful.
	 */
	public function Set($key, $val)
	{
		$this->cartSession[$key] = $val;
		return true;
	}

	public function SetArrayPush($key, $val)
	{
		$this->cartSession[$key][] = $val;
		return true;
	}

	public function SetItemValue($item, $data, $val)
	{
		$this->cartSession['ITEMS'][$item][$data] = $val;
		return true;
	}

	/**
	 * Get a value from the cart session with the defined name.
	 *
	 * @param string The name of the value to get.
	 * @return mixed The value.
	 */
	public function Get($key)
	{
		if(isset($this->cartSession[$key])) {
			return $this->cartSession[$key];
		}

		return false;
	}

	/**
	 * Remove the gift wrapping configuration from a particular item in the cart.
	 *
	 * @param string The ID of the item in the cart that gift wrapping should be removed from.
	 * @return boolean True if successful, false if not.
	 */
	public function RemoveGiftWrapping($itemId)
	{
		unset($this->cartSession['ITEMS'][$itemId]['wrapping']);
		return true;
	}

	/**
	 * Remove a particular item from the shopping cart.
	 *
	 * @param string The ID of the item in the cart that should be removed.
	 * @return boolean True if successful, false if not.
	 */
	public function RemoveItem($itemId)
	{
		if(!isset($this->cartSession['ITEMS'][$itemId])) {
			return true;
		}

		$cartItem = $this->cartSession['ITEMS'][$itemId];

		$this->RemoveItemChildren($itemId);

		unset($this->cartSession['ITEMS'][$itemId]);
		$this->cartSession['NUM_ITEMS'] -= $cartItem['quantity'];

		// If the cart is now empty, remove any gift certificates
		if(empty($this->cartSession['ITEMS'])) {
			unset($this->cartSession['GIFTCERTIFICATES']);
			$this->cartSession['NUM_ITEMS'] = 0;
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		// ensure any applied coupons are valid
		$this->CheckCouponsStillValid();

		return true;
	}

	public function RemoveItemChildren($itemId)
	{

		if (!isset($this->cartSession['ITEMS'][$itemId])) {
			return;
		}
		$cartItem = $this->cartSession['ITEMS'][$itemId];

		if (isset($cartItem['Children'])) {
			foreach ($cartItem['Children'] as $child) {
				foreach($this->cartSession['ITEMS'] as $k=>$p) {
					if (isset($p['parent_id']) && $p['parent_id'] == $child) {
						$this->RemoveItem($k);
					}
				}
			}
			unset($this->cartSession['ITEMS'][$itemId]['Children']);
		}
	}

	/**
	 * Update the quantity of a particular item in the cart to something else.
	 *
	 * @param string The ID of the item in the cart to update the quantity of.
	 * @param int The quantity the item should be updated to.
	 * @param int The quantity to use to check stock levels against. This can differ from the quantity when editing an order.
	 * @return boolean True if successful, false if not.
	 */
	public function UpdateCartQuantity($itemId, $quantity, $quantityCheck = -1)
	{
		$products = $this->GetProductsInCart();

		// Quantity was set to 0, remove this item
		if($quantity <= 0) {
			return $this->RemoveItem($itemId);
		}

		if(!isset($products[$itemId]) || !IsId($quantity)) {
			return false;
		}

		if ($quantityCheck < 0) {
			$quantityCheck = $quantity;
		}

		$cartItem = $products[$itemId];

		foreach ($products as $id=>$productInfo) {

				if ($itemId != $id && $productInfo['product_id'] == $cartItem['product_id'] && $productInfo['variation_id'] == $cartItem['variation_id']) {
					$quantityCheck += $productInfo['quantity'];
				}
		}

		// Do we have enough of this particular item in stock? If not, return with an error
		if(isset($cartItem['data']['prodinvtrack']) && $cartItem['data']['prodinvtrack'] > 0 && $quantityCheck > $cartItem['data']['prodcurrentinv']) {
			// Variation?
			$variationAppend = '';
			if(isset($cartItem['variation_id']) && $cartItem['variation_id'] > 0) {
				foreach($cartItem['options'] as $name => $value) {
					if(!trim($name) || !trim($value)) {
						continue;
					}
					$variationAppend .= isc_html_escape($name).': '.isc_html_escape($value).', ';
				}
				$variationAppend = ' ('.rtrim($variationAppend, ', ').')';
			}
			$productName = isc_html_escape($cartItem['product_name']).$variationAppend;
			$this->SetError(sprintf(GetLang('InvLevelBelowOrderQty'), $productName));
			return false;
		}

		// We have enough in stock, so update the quantity in the cart
		$this->cartSession['NUM_ITEMS'] = $this->cartSession['NUM_ITEMS'] - $cartItem['quantity'] + $quantity;
		$this->cartSession['ITEMS'][$itemId]['quantity'] = $quantity;

		// If our quantities changed then we have to recalculate the bulk discount price
		if ($cartItem['quantity'] != $quantity) {
			// Apply a quantity discount for this item if there is one
			$this->cartSession['ITEMS'][$itemId]['product_price'] = $this->CalculateQuantityDiscount($itemId, $cartItem['original_price']);

			$this->cartSession['ITEMS'][$itemId]['product_price'] = CalculateCustGroupDiscount($cartItem['product_id'], $this->cartSession['ITEMS'][$itemId]['product_price'], @$cartItem['customer_group']);
		}

		if ($this->cartSession['ITEMS'][$itemId]['product_price'] < 0) {
			$this->cartSession['ITEMS'][$itemId]['product_price'] = 0;
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		// ensure any applied coupons are valid
		$this->CheckCouponsStillValid();

		return true;
	}

	/**
	* Check if any applied coupons are now invalid (such as the cart total going below the min required amount)
	* Removes the coupon if it is invalid and generates an error.
	*
	*/
	protected function CheckCouponsStillValid()
	{
		$coupons = $this->GetAppliedCouponCodes();
		$couponsRemoved = false;
		$couponErrors = '';

		foreach ($coupons as $coupon) {
			$couponCode = $coupon['couponcode'];
			// Look up the coupon code
			$query = "
				SELECT *
				FROM [|PREFIX|]coupons
				WHERE couponcode='".$GLOBALS['ISC_CLASS_DB']->Quote($couponCode)."'
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			if (!($thisCoupon = $GLOBALS['ISC_CLASS_DB']->Fetch($result))) {
				//@todo .. need to not remove a coupon when editing an existing order
				$this->RemoveCouponCode($coupon['couponid']);
				continue;
			}

			$subtotal = $this->GetCartSubTotal(false, null, false, true, true);

			// Does the cart subtotal meet the minimum purchase amount required?
			if($thisCoupon['couponminpurchase'] > 0 && $subtotal < $thisCoupon['couponminpurchase']) {
				// remove the coupon
				$this->RemoveCouponCode($coupon['couponid']);
				// notify customer that coupon was removed
				$difference = CurrencyConvertFormatPrice($thisCoupon['couponminpurchase'] - $this->GetCartSubTotal(false, null, true, false, true));
				if ($couponErrors) {
					$couponErrors .= "<br/>";
				}
				$couponErrors .= sprintf(GetLang("InvalidCouponRemoved"), $couponCode) . " " . sprintf(GetLang('InvalidCouponMinPrice'), $difference);
			}
		}

		if ($couponErrors) {
			if (isset($_SESSION['CART']['ERROR']) && $_SESSION['CART']['ERROR'] != "") {
				$_SESSION['CART']['ERROR'] .= "<br>";
			}
			else {
				$_SESSION['CART']['ERROR'] = "";
			}
			$_SESSION['CART']['ERROR'] .= $couponErrors;
		}
	}

	/**
	 * Calculate a quantity/bulk discount tier for a price based on the passed product price
	 * and discount details.
	 *
	 * @param string The ID of the item in the cart we're determining the quantity discount for.
	 * @param float The price we're adjusting the quantity discount for.
	 * @return The adjusted price for the product based on the tier pricing, if there is any.
	 */
	public function CalculateQuantityDiscount($itemId, $price)
	{
		if(!isset($this->cartSession['ITEMS'][$itemId])) {
			return $price;
		}

		$cartItem = $this->cartSession['ITEMS'][$itemId];

		$discount = GetBulkDiscountByQuantity($cartItem['product_id'], $cartItem['quantity']);

		if(!is_array($discount)) {
			return $price;
		}

		switch ($discount['discounttype']) {
			case 'price':
				$price -= (float)$discount['discountamount'];
				break;

			case 'percent':
				$price -= (((int)$discount['discountamount'] / 100) * $price);
				break;

			case 'fixed':
				$price = $discount['discountamount'];
				break;
		}

		if ($price < 0) {
			$price = 0;
		}

		return $price;
	}

	/**
	 * Apply a coupon code to all of the products in the cart.
	 *
	 * @param string The coupon code to be applied.
	 * @return boolean True if successful, false if not.
	 */
	public function ApplyCoupon($couponCode)
	{
		$products = $this->GetProductsInCart();

		// By default, assume the coupon is valid
		$validCoupon = true;
		$appliedCoupon = false;

		// We'll store details for each product that the coupon
		// can be applied to, and will then merge that with the
		// details of the cart in the session
		$appliedCartItems = array();

		$couponCode = trim($couponCode);

		// Look up the coupon code
		$query = "
			SELECT *
			FROM [|PREFIX|]coupons
			WHERE couponcode='".$GLOBALS['ISC_CLASS_DB']->Quote($couponCode)."'
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$coupon = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
		if(!$coupon || !$coupon['couponcode']) {
			$this->SetError(GetLang('InvalidCouponCode'));
			return false;
		}

		// Coupon code is disabled, it can't be applied
		if($coupon['couponenabled'] == 0) {
			$this->SetError(GetLang('InvalidCouponDisabled'));
			return false;
		}

		// If the coupon has expired, it can't be used
		if($coupon['couponexpires'] != 0) {
			// coupon expires at 23:59:59 of the day
			$expires = $coupon['couponexpires'] + 86399;

			if (isc_mktime() > $expires) {
				$this->SetError(sprintf(GetLang('InvalidCouponExpired'), date(GetConfig('DisplayDateFormat'), $coupon['couponexpires'])));
				return false;
			}
		}

		// Has the coupon already reached it's maximum number of uses? It can't be used
		if($coupon['couponmaxuses'] != 0 && $coupon['couponnumuses'] >= $coupon['couponmaxuses']) {
			$this->SetError(GetLang('InvalidCouponExpiredUses'));
			return false;
		}

		// Does the cart subtotal meet the minimum purchase amount required?
		if($coupon['couponminpurchase'] > 0 && $this->GetCartSubTotal(false, null, false, false, true) < $coupon['couponminpurchase']) {
			$difference = CurrencyConvertFormatPrice($coupon['couponminpurchase'] - $this->GetCartSubTotal(false, null, true, false, true));
			$this->SetError(sprintf(GetLang('InvalidCouponMinPrice'), $difference));
			return false;
		}

		// load up the categories/products it can be applied to
		$query = "SELECT * FROM [|PREFIX|]coupon_values WHERE couponid = " . $coupon['couponid'];
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		while ($valuerow = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			$coupon['couponappliestovalues'][] = $valuerow['valueid'];
		}

		// Now we can actually begin applying the coupon to the cart

		// Coupon is based on categories - so loop through products and check category association
		if($coupon['couponappliesto'] == 'categories') {
			$applicableCategories = $coupon['couponappliestovalues'];
			foreach($products as $itemId => $product) {
				// By default, $applyCoupon is set to default (to not apply this coupon code to this product)
				// unless this coupon code applies to all categories
				$applyCoupon = false;
				if(in_array(0, $coupon['couponappliestovalues'])) {
					$applyCoupon = true;
				}
				// Need to actually check to see if this product exists within the selected categories
				else {
					$productCats = explode(',', $product['data']['categoryids']);
					foreach($applicableCategories as $category) {
						if(in_array($category, $productCats)) {
							$applyCoupon = true;
							break;
						}
					}
				}

				if($applyCoupon) {
					$appliedCartItems[] = $itemId;
				}
			}
		}
		// Coupon code is based on products
		else {
			$applicableProducts = $coupon['couponappliestovalues'];
			foreach($products as $itemId => $product) {
				if(in_array($product['product_id'], $applicableProducts)) {
					$appliedCartItems[] = $itemId;
				}
			}
		}

		if($coupon['coupontype'] == 1) {
			$couponType = '%';
		}
		else {
			$couponType = '$';
		}

		// If the coupon couldn't be applied to anything, don't continue
		if(empty($appliedCartItems)) {
			$this->SetError(GetLang('InvalidCouponCode'));
			return false;
		}

		// Now actually apply and merge the coupons in to the cart
		foreach($appliedCartItems as $itemId) {
			$cartItem = &$this->cartSession['ITEMS'][$itemId];

			// Do not apply the coupon code if this item is not a product
			if(isset($cartItem['type']) && $cartItem['type'] == 'giftcertificate') {
				continue;
			}

			$cartItem['discount'] = $coupon['couponamount'] / 1; //div by 1 removes unnecessary zeros in the fractional part of the amount
			$cartItem['coupontype'] = $coupon['coupontype'];
			$cartItem['couponcode'] = $coupon['couponcode'];
			$cartItem['coupon'] = $coupon['couponid'];
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return true;
	}

	/**
	 * Remove an applies coupon code from the cart.
	 *
	 * @param int The ID of the coupon code to be removed from the cart.
	 * @return boolean True if successful, false if not.
	 */
	public function RemoveCouponCode($couponId)
	{
		$cartProducts = $this->GetProductsInCart();
		foreach($cartProducts as $itemId => $product) {
			if(isset($product['coupon']) && $product['coupon'] == $couponId) {
				unset($this->cartSession['ITEMS'][$itemId]['discount']);
				unset($this->cartSession['ITEMS'][$itemId]['coupontype']);
				unset($this->cartSession['ITEMS'][$itemId]['couponcode']);
				unset($this->cartSession['ITEMS'][$itemId]['coupon']);
			}
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return true;
	}

	/**
	 * Apply a gift certificate to the shopping cart.
	 *
	 * @param string The gift certificate code to apply to the cart.
	 * @return boolean True if successful, false if not.
	 */
	public function ApplyGiftCertificate($giftCertificateCode)
	{
		// First check if we have a valid gift certificate
		$query = "
			SELECT *
			FROM [|PREFIX|]gift_certificates
			WHERE (giftcertstatus=2 OR giftcertstatus=4) AND giftcertcode='".$GLOBALS['ISC_CLASS_DB']->Quote(trim($giftCertificateCode))."'
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$certificate = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		// Invalid gift certificate code was entered
		if(!$certificate['giftcertid'] || $certificate['giftcertbalance'] == 0) {
			$this->SetError(GetLang('BadGiftCertificateInvalid'));
			return false;
		}
		// This gift certificate has expired
		else if($certificate['giftcertstatus'] == 4 || ($certificate['giftcertexpirydate'] != 0 && time() >= $certificate['giftcertexpirydate'])) {
			if($certificate['giftcertexpirydate'] != 0) {
				$this->SetError(sprintf(GetLang('BadGiftCertificateExpired'), CDate($certificate['giftcertexpirydate'])));
			}
			else {
				$this->SetError(GetLang('BadGiftCertificateInvalid'));
			}

			if($certificate['giftcertstatus'] != 4) {
				$updatedCertificate = array(
					'giftcertstatus' => 4
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery('gift_certificates', $updatedCertificate, "giftcertid='".$GLOBALS['ISC_CLASS_DB']->Quote($certificate['giftcertid'])."'");
			}

			return false;
		}

		// If the gift certificate has already been applied to the cart, it can't be applied again
		if(isset($this->cartSession['GIFTCERTIFICATES'][$certificate['giftcertid']])) {
			return true;
		}

		// Apply it to the customers cart
		$this->cartSession['GIFTCERTIFICATES'][$certificate['giftcertid']] = array(
			"giftcertcode" => $certificate['giftcertcode'],
			"giftcertid" => $certificate['giftcertid'],
			"giftcertamount" => $certificate['giftcertamount'],
			"giftcertbalance" => $certificate['giftcertbalance'],
			"giftcertexpirydate" => $certificate['giftcertexpirydate']
		);

		return true;
	}

	/**
	 * Remove a gift certificate that's been applied to the shopping cart.
	 *
	 * @param int The ID of the gift certificate that should be removed.
	 * @return boolean True if successful, false if not.
	 */
	public function RemoveAppliedGiftCertificate($giftCertificateId)
	{
		unset($this->cartSession['GIFTCERTIFICATES'][$giftCertificateId]);
		return true;
	}

	/**
	 * Get an array of all of the products in the shopping cart as well as the
	 * product & variation information from the database.
	 *
	 * @param boolean Set to true to do a hard refresh of the data as opposed to using the cached version.
	 * @param boolean Set to true to reverse the items so the most recent item added is at the top.
	 * @return array An array of products in the cart.
	 */
	public function GetProductsInCart($hardRefresh=false, $reverse=true)
	{
		// Just use the cached version if we've already been here before
		if(!empty($this->cartProducts) && $hardRefresh == false) {
			return $this->cartProducts;
		}

		if(!isset($this->cartSession['ITEMS'])) {
			return array();
		}

		if($reverse == true) {
			$this->cartProducts = array_reverse($this->cartSession['ITEMS'], true);
		}
		else {
			$this->cartProducts = $this->cartSession['ITEMS'];
		}

		$productIds = array();
		$variationIds = array();

		foreach($this->cartProducts as $itemId => $item) {
			unset($this->cartSession['ITEMS'][$itemId]['data']);
			$this->cartProducts[$itemId]['cartitemid'] = $itemId;

			// If this is a gift certificate, we just set up the data/info for the product here - don't need to load anything
			if(isset($item['type']) && $item['type'] == 'giftcertificate') {
				$this->cartProducts[$itemId]['data'] = array(
					'productid' => -1,
					'prodname' => CurrencyConvertFormatPrice($item['product_price']).' '.GetLang('GiftCertificate'),
					'prodcalculatedprice' => $item['product_price'],
					'prodtype' => PT_GIFTCERTIFICATE
				);
				$this->cartSession['ITEMS'][$itemId]['product_name'] = $this->cartProducts[$itemId]['data']['prodname'];
			}
			// If it's a normal product, we need to load the information
			else if($item['product_id'] != 0) {
				$productIds[] = $item['product_id'];

				if(isset($item['variation_id']) && $item['variation_id'] > 0) {
					$variationIds[] = $item['variation_id'];
				}
			}
		}

		$productIds = implode(',', $productIds);
		$productData = array();
		$variationData = array();
		if($productIds) {
			// Load up the data for the products
			$query = "
				SELECT p.productid, prodcurrentinv, prodcode, prodinvtrack, prodweight, prodwidth, prodheight, prodvariationid,
					proddepth, prodname, prodprice, prodretailprice, prodsaleprice, prodcalculatedprice,
					prodavailability, prodtype, prodcostprice, prodfixedshippingcost, prodfreeshipping, prodoptionsrequired,
					pi.*, prodistaxable, prodwrapoptions, prodvendorid, prodeventdaterequired, prodeventdatefieldname, ".GetProdCustomerGroupPriceSQL().",
					(SELECT group_concat(ca.categoryid SEPARATOR ',') FROM [|PREFIX|]categoryassociations ca WHERE p.productid=ca.productid) AS categoryids
				FROM [|PREFIX|]products p
				LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1)
				WHERE p.productid in (".$productIds.")
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($product = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$productData[$product['productid']] = $product;
			}

			// Are there any variations to load?
			if(!empty($variationIds)) {
				$query = "
					SELECT combinationid, vcproductid, vcimagethumb, vcweight, vcweightdiff, vcstock
					FROM [|PREFIX|]product_variation_combinations
					WHERE combinationid IN (".implode(",", $variationIds).")
				";

				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$variationImages = array();
				while($variation = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$variationData[$variation['combinationid']] = $variation;
				}
			}
		}

		// Now loop through all of the items in the cart again, and apply any
		// data we have for the product or variation, as well as any discount
		// rules
		foreach($this->cartProducts as $itemId => $item) {
			if (isset($item['type']) && $item['type'] == "giftcertificate") {
				continue;
			}

			// If the product does not exist, remove it
			if(!isset($productData[$item['product_id']])) {
				$this->RemoveItem($itemId);
				continue;
			}

			$hasNormalProducts = true;

			// Set the product data
			$this->cartProducts[$itemId]['data'] = $productData[$item['product_id']];

			// If this item is a variation, load in the weight etc
			if(isset($item['variation_id']) && isset($variationData[$item['variation_id']])) {
				$variationInfo = $variationData[$item['variation_id']];
				// Override the thumbnail for this item with the variation thumbnail
				if($variationInfo['vcimagethumb']) {
					$this->cartProducts[$itemId]['data']['vcimagethumb'] = $variationInfo['vcimagethumb'];
				}

				if($variationInfo['vcweight'] != 0) {
					$this->cartProducts[$itemId]['data']['prodweight'] = CalcProductVariationWeight($productData[$item['product_id']]['prodweight'], $variationInfo['vcweightdiff'], $variationInfo['vcweight']);
				}

				// Store the # of this combination we have in stock
				if($productData[$item['product_id']]['prodinvtrack'] == 2) {
					$this->cartProducts[$itemId]['data']['prodcurrentinv'] = $variationInfo['vcstock'];
				}
			}

			$discountedPrice = $item['product_price'];

			if(!empty($item['discountRuleDiscounts'])) {
				foreach($item['discountRuleDiscounts'] as $discount) {
					$discountedPrice -= $discount;
				}
			}

			// Is there a coupon code applied to this product?
			if(!empty($item['discount'])) {
				$discountType = $item['coupontype'];
				$discountAmount = $item['discount'];

				// Now workout the discount amount
				if ($discountType == 0) {
					// It's a dollar discount
					$discountedPrice -= $discountAmount;
				}
				else {
					// It's a percentage discount
					$discount = $discountedPrice * $discountAmount / 100;
					$discountedPrice -= $discount;
				}
			}

			if($discountedPrice < 0) {
				$discountedPrice = 0;
			}

			if($discountedPrice != $item['product_price']) {
				$this->cartProducts[$itemId]['discount_price'] = $discountedPrice;
			}
		}

		if(isset($hasNormalProducts)) {
			$this->onlyGiftCertificates = false;
		}
		else {
			$this->onlyGiftCertificates = true;
		}

		return $this->cartProducts;

	}

	/**
	 * Calculate the total amount deducted for coupon codes in the shopping cart.
	 *
	 * @return float The total amount deducted from the order because of gift certificates.
	 */
	public function CalculateCouponCodeDiscount()
	{
		return $this->GetCartSubTotal(false,null,false,true,true)-$this->GetCartSubTotal(false,null,true,true,true);
	}

	/**
	 * Return an array containing all of the coupon codes for products applied to the order
	 * and their discounts.
	 *
	 * @return array An array of coupon codes.
	 */
	public function GetAppliedCouponCodes()
	{
		$couponCodes = array();
		$products = $this->GetProductsInCart();

		foreach($products as $product) {
			if(!isset($product['coupon'])) {
				continue;
			}

			if(isset($couponCodes[$product['coupon']])) {
				$couponCodes[$product['coupon']]['coupontotal'] += ($product['product_price'] - $product['discount_price'])  * $product['quantity'];
			}
			else {
				$couponCodes[$product['coupon']] = array(
					'couponid' => $product['coupon'],
					'couponcode' => $product['couponcode'],
					'coupontype' => $product['coupontype'],
					'discount' => $product['discount'],
					'coupontotal' => ($product['product_price'] - $product['discount_price']) * $product['quantity']
				);
			}
		}

		return $couponCodes;
	}

	public function UpdateCartInformation()
	{
		$this->cartSession['FREE_SHIPPING'] = false;
		$this->cartSession['DISCOUNT_MESSAGES'] = null;

		$cartProducts = $this->GetProductsInCart();

		foreach ($cartProducts as $key=>$product) {
			$this->SetItemValue($key, 'discount_price', $product['product_price']);
		}

		require_once(ISC_BASE_PATH . "/lib/rule.php");
		$response = EvaluateRules('all', $this->GetCartSubTotal(false, null, false, false, true));

		$this->cartSession['SUBTOTAL_DISCOUNT'] = $response['subtotal'];
		$this->cartSession['RULE_USES'] = $response['ruleuses'];
	}

	/**
	*
	* @param bool $shippableTotal
	* @param int $vendorId
	* @param bool $includeDiscounts
	* @param bool $includeCertificates
	* @param bool $hardRefresh
	*/
	public function GetCartSubTotal($shippableTotal=false, $vendorId=null, $includeDiscounts=true, $includeCertificates=true, $hardRefresh=false)
	{
		if (isset($this->cartSession['SUBTOTAL']) && !$hardRefresh) {
			//return $this->cartSession['SUBTOTAL'];
		}

		$subtotal = 0;
		$products = $this->GetProductsInCart();

		foreach($products as $k => $product) {


			if(!isset($product['data'])) {
				continue;
			}

			if(!isset($product['data']['prodvendorid'])) {
				$product['data']['prodvendorid'] = 0;
			}

			// If we only want the total for a specific vendor and this product doesn't belong to the vendor, skip over it
			if(!is_null($vendorId) && $vendorId !== $product['data']['prodvendorid']) {
				continue;
			}

			if($shippableTotal == true && $product['data']['prodtype'] == PT_PHYSICAL && $product['data']['prodfixedshippingcost'] > 0) {
				continue;
			}

			if(!$includeCertificates && isset($product['type']) && $product['type'] == 'giftcertificate') {
				continue;
			}

			if(isset($product['discount_price']) && $includeDiscounts == true) {
				$price = $product['discount_price'];
			}
			else {
				$price = $product['product_price'];
			}

			$subtotal += ((int)$product['quantity'] * $price);

			if (isset($product['wrapping'])) {
				$subtotal += ((int)$product['quantity'] * $product['wrapping']['wrapprice']);
			}
		}

		$this->cartSession['SUBTOTAL'] = $subtotal;

		return $subtotal;
	}

	public function GetCartFreeShipping($hardRefresh=false)
	{

		if ($hardRefresh) {
			$response = $this->UpdateCartInformation();
		}

		if (isset($this->cartSession['FREE_SHIPPING'])) {
			return $this->cartSession['FREE_SHIPPING'];
		}

		return false;
	}

	/**
	 * Get a CSV list of all of the product IDs for products that exist in the cart.
	 *
	 * @return string A CSV list of products in the cart.
	 */
	public function GetProductsInCartCSV()
	{
		if(!isset($this->cartSession['ITEMS'])) {
			return '';
		}

		$cartProducts = array();
		foreach($this->cartSession['ITEMS'] as $item) {
			$cartProducts[] = $item['product_id'];
		}
		return implode(',', array_unique($cartProducts));
	}

	/**
	 * Count the number of actual products in the cart (not items).
	 *
	 * @return int The number of products in the cart.
	 */
	public function GetNumProductsInCart()
	{
		if(!isset($this->cartSession['ITEMS'])) {
			return 0;
		}

		return count($this->cartSession['ITEMS']);
	}

	/**
	 * Get information about a particular product in the cart.
	 *
	 * @param string The item ID to fetch the information of.
	 * @return array An array of information about the cart product.
	 */
	public function GetProductInCart($itemId)
	{
		$products = $this->GetProductsInCart();
		if(!isset($products[$itemId])) {
			return false;
		}
		return $products[$itemId];
	}

	/**
	 * Count the number of actual items (based on the quantity) in the cart.
	 *
	 * @return int The number of items in the cart.
	 */
	public function GetNumItemsInCart()
	{
		if(!isset($this->cartSession['ITEMS'])) {
			return 0;
		}

		if(isset($this->cartSession['NUM_ITEMS'])) {
			return $this->cartSession['NUM_ITEMS'];
		}

		$numItems = 0;
		foreach($this->cartSession['ITEMS'] as $item) {
			$numItems += $item['quantity'];
		}

		$this->cartSession['NUM_ITEMS'] = $numItems;
		return $numItems;
	}

	/**
	 * Check if all products in the shopping cart are intangible (gift certificates or downloadable products)
	 *
	 * @return boolean True if successful, false if not.
	 */
	public function AllProductsInCartAreIntangible($vendorId=null)
	{
		$products = $this->GetProductsInCart();
		foreach($products as $product) {
			if(!isset($product['data'])) {
				continue;
			}

			// This product belongs to a vendor we don't care about, skip it
			if(!is_null($vendorId) && isset($product['data']['prodvendorid']) && $product['data']['prodvendorid'] != $vendorId) {
				continue;
			}

			// Physical product - return false
			if($product['data']['prodtype'] == PT_PHYSICAL) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Return the number of physical products (ie, not digital) in the shopping cart.
	 *
	 * @return int The number of physical products.
	 */
	public function GetNumPhysicalProducts()
	{
		$num = 0;
		$products = $this->GetProductsInCart();
		foreach($products as $product) {
			if(!isset($product['data'])) {
				continue;
			}

			// Physical product
			if($product['data']['prodtype'] == PT_PHYSICAL) {
				$num += $product['quantity'];
			}
		}

		return $num;
	}

	/**
	 * Get a list of the products in the shopping cart, but structured as a nested array
	 * for each vendor.
	 *
	 * @param boolean True to do a hard refresh from the database.
	 * @return array An array of items in the cart, grouped by vendor.
	 */
	public function GetProductsInCartByVendor($hardRefresh=false)
	{
		$vendorProducts = array();
		$cartProducts = $this->GetProductsInCart($hardRefresh);
		foreach($cartProducts as $product) {
			if(isset($product['data']['prodvendorid']) && gzte11(ISC_HUGEPRINT)) {
				$vendorProducts[$product['data']['prodvendorid']][] = $product;
			}
			else {
				$vendorProducts[0][] = $product;
			}
		}

		return $vendorProducts;
	}

	/**
	 * Get a list of the vendor IDs for all of the items going through the checkout.
	 *
	 * @return array An array of order IDs.
	 */
	public function GetCartVendorIds()
	{
		$vendorIds = array();
		if(!gzte11(ISC_HUGEPRINT)) {
			$vendorIds[] = 0;
			return $vendorIds;
		}

		$cartProducts = $this->GetProductsInCart();
		foreach($cartProducts as $product) {
			if(isset($product['data']['prodvendorid']) && $product['data']['prodvendorid']) {
				$vendorIds[] = $product['data']['prodvendorid'];
			}
			else {
				$vendorIds[] = 0;
			}
		}

		// Return only unique entries, and ensure that there are no missing keys
		return array_values(array_unique($vendorIds));
	}

	/**
	 * Add a virtual (product that does not exist in the store) item to cart session.
	 *
	 * @param array An array of details about the virtual product.
	 * @param string The ID to insert the item as in to the order.
	 * @return boolean True if successful, false if not.
	 */
	public function AddVirtualItem($productDetails, $itemId=null)
	{
		// If a name wasn't entered, don't add this item to the cart
		if(!isset($productDetails['prodname']) || !trim($productDetails['prodname'])) {
			return false;
		}

		// Get the default currency at the time of adding and store it
		$defaultCurrency = GetDefaultCurrency();

		// Actually add the item
		$cartProduct = array(
			'product_id'		=> 0,
			'variation_id'		=> 0,
			'options'			=> array(),
			'quantity'			=> $productDetails['quantity'],
			'product_name'		=> $productDetails['prodname'],
			'product_code'		=> $productDetails['prodcode'],
			'product_price'		=> $productDetails['prodprice'],
			'original_price'	=> $productDetails['prodprice'],
			'default_currency'	=> $defaultCurrency['currencyid'],
		);

		if(is_null($itemId)) {
			$this->cartSession['ITEMS'][] = $cartProduct;

			// Fetch the ID that was just added
			$cartItemsTemp = $this->cartSession['ITEMS'];
			end($cartItemsTemp);
			list($itemId,) = each($cartItemsTemp);
		}
		else {
			$this->cartSession['ITEMS'][$itemId] = $cartProduct;
		}

		if(isset($this->cartSession['NUM_ITEMS']) && $this->cartSession['NUM_ITEMS'] > 0) {
			$this->cartSession['NUM_ITEMS'] += $productDetails['quantity'];
		}
		else {
			$this->cartSession['NUM_ITEMS'] = $productDetails['quantity'];
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return $itemId;
	}

	/**
	 * Add an item to the shopping cart based on the supplied product ID, quantity and additional
	 * details.
	 *
	 * @param int The ID of the product that should be added to the cart.
	 * @param int The quantity of the item that should be added. (defaults to 1)
	 * @param mixed The variation ID (or array of variation information) of the product that should be added to the cart. If null, no variation is added.
	 * @param array An array of configurable fields (if any) that should be attached to this product in the cart.
	 * @param string If specified, the cart item ID that this item should be inserted as. WARNING - if this ID already exists in the cart, it will be overwritten.
	 * @param array Optionally an array containing additional items as defined in the method itself.
	 * @param boolean Set to false to not update the quantity if this product already exists in the cart.
	 * @return string The cart item ID for this product that's just been added. False on error.
	 */
	public function AddItem($productId, $quantity=1, $variationDetails=null, $configurableOptions=array(), $cartItemId=null, $options=array(), $parentId=null, $reorder=false)
	{
		if ($quantity <= 0) {
			$this->SetError(GetLang('InvalidQuantity'));
			$this->productLevelError = true;
			return false;
		}

		$customerGroup = null;
		if(isset($options['customerGroup'])) {
			$customerGroup = $options['customerGroup'];
		}

		$query = "
			SELECT p.*, ".GetProdCustomerGroupPriceSQL($customerGroup)."
			FROM [|PREFIX|]products p
			WHERE p.productid='".(int)$productId."'
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$product = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
		if(!$product || !$product['productid']) {
			return false;
		}

		// Did we add a variation?
		$variation = 0;
		if(IsId($variationDetails)) {
			$variation = (int)$variationDetails;
		}
		// User added a variation but had javascript disabled, need to fetch the variations and find a matching one
		else if(is_array($variationDetails) && $variationDetails[1] != 0) {
			// Load the variations for this product
			$GLOBALS['ISC_CLASS_PRODUCT'] = new ISC_PRODUCT($productId);
			$variation = $GLOBALS['ISC_CLASS_PRODUCT']->GetVariationCombination($variationDetails);
		}

		$productPrice = CalcRealPrice($product['prodprice'], $product['prodretailprice'], $product['prodsaleprice'], $product['prodistaxable']);
		$productCode = $product['prodcode'];

		$variationOptions = array();
		if($variation != 0) {
			// Load the variation
			$query = "
				SELECT *
				FROM [|PREFIX|]product_variation_combinations
				WHERE combinationid='".$GLOBALS['ISC_CLASS_DB']->Quote($variation)."'
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$productVariation = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if(!$productVariation['combinationid']) {
				$this->productLevelError = true;
				return false;
			}

			// Load the combination details for this variation
			$query = "
				SELECT *
				FROM [|PREFIX|]product_variation_options
				WHERE voptionid IN (".$productVariation['vcoptionids'].")
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($variationOption = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$variationOptions[$variationOption['voname']] = $variationOption['vovalue'];
			}

			// Calculate the price of this variation
			//if the product is on sale, use the sale price to calculate the variation price.
			//can't use prodcalculatedprice, because it includes the tax , so the tax would be added again
			if ($product['prodsaleprice'] != 0) {
				$variationBasePrice = $product['prodsaleprice'];
			} else {
				$variationBasePrice = $product['prodprice'];
			}
			$productPrice = CalcProductVariationPrice($variationBasePrice, $productVariation['vcpricediff'], $productVariation['vcprice']); // Intentionally don't pass $product as we calculate the group price below.

			// Assign the product code if we have any for this variation
			if ($productVariation['vcsku'] !== '') {
				$productCode = $productVariation['vcsku'];
			}
		}

		if($product['prodvariationid'] && $product['prodoptionsrequired'] && !$variation) {
			$this->SetError(GetLang('ErrorNoVariationSelected'));
			$this->productLevelError = true;
			return false;
		}

		// Are we passing a custom price for this product? If so use that
		if(isset($options['priceOverride'])) {
			$productPrice = $options['priceOverride'];
		}

		// Finally, determine the price based on the customer group
		$originalPrice = $productPrice;

		$currentInventory = $product['prodcurrentinv'];
		if($variation > 0 && $product['prodinvtrack']==2) {
			$currentInventory = $productVariation['vcstock'];
		}

		// Do we have enough of this particular item in stock? If not, return with an error
		if(isset($product['prodinvtrack']) && $product['prodinvtrack'] > 0 && $quantity > $currentInventory) {
			if($quantity == 1) {
				$this->SetError(sprintf(GetLang('CannotAddQuantityToCartSingle'), $product['prodname']));
			}
			else {
				$this->SetError(sprintf(GetLang('CannotAddQuantityToCart'), $quantity, $product['prodname']));
			}
			return false;
		}

		// Check if the item is already in the cart, if so, just update the quantity
		$cartProducts = $this->GetProductsInCart();

		if (!$this->CheckCartQuantity($productId, $quantity, $variation)) {
			return false;
		}

		foreach($cartProducts as $itemId => $cartItem) {

			if($cartItem['product_id'] != $productId || $cartItem['variation_id'] != $variation) {
				continue;
			}
			// Product in the cart has wrapping, we can't update it's quantity
			else if(isset($cartItem['wrapping'])) {
				continue;
			}
			// Product in the cart has configurable fields, can't update it's quantity
			else if(isset($cartItem['product_fields']) && is_array($cartItem['product_fields']) && !empty($cartItem['product_fields'])) {
				continue;
			}
			// Product has a separate event date
			else if (isset($options['EventDate']) && isset($cartItem['event_date']) && $options['EventDate'] != $cartItem['event_date']) {
				continue;
			}
			// Is this item attached to a parent?
			else if (isset($cartItem['parent_id'])) {
				continue;
			}
			// This product matches, call the update quantity method and return
			else if(!isset($options['updateQtyIfExists']) || $options['updateQtyIfExists'] == true) {

				if ($this->UpdateCartQuantity($itemId, $cartItem['quantity']+$quantity) == false) {
					return false;
				}

				return $itemId;
			}
		}

		// Get the default currency at the time of adding and store it
		$defaultCurrency = GetDefaultCurrency();

		// Actually add the item
		$cartProduct = array(
			'product_id' => $productId,
			'variation_id' => $variation,
			'options' => $variationOptions,
			'quantity' => $quantity,
			'product_name' => $product['prodname'],
			'product_code' => $productCode,
			'product_price' => $productPrice,
			'original_price' => $originalPrice,
			'default_currency' => $defaultCurrency['currencyid'],
			'customer_group' => $customerGroup
		);

		// Is product data supplied already? This will come from admin manual orders
		if (isset($options['data'])) {
			$cartProduct['data'] = $options['data'];
		}

		if ($parentId !== null) {
			$cartProduct['parent_id'] = $parentId;
		}

		if (isset($options['EventDate'])) {
			$cartProduct['event_name'] = $product['prodeventdatefieldname'];
			$cartProduct['event_date'] = $options['EventDate'];
		}

		if($reorder == true) {
			$cartProduct['product_fields'] = $configurableOptions;
		} else {
			// Set up any product fields
			$productFieldsData = $this->GetConfigurableFieldData($productId, $configurableOptions);

			if($productFieldsData === false) {
				$this->productLevelError = true;
				return false;
			}
			else {
				$cartProduct['product_fields'] = $productFieldsData;
			}
		}

		if(is_null($cartItemId)) {
			$this->cartSession['ITEMS'][] = $cartProduct;

			// Fetch the ID that was just added
			$cartItemsTemp = $this->cartSession['ITEMS'];
			end($cartItemsTemp);
			list($cartItemId,) = each($cartItemsTemp);
		}
		else {
			$this->cartSession['ITEMS'][$cartItemId] = $cartProduct;
		}

		// Apply a quantity discount for this item if there is one
		$QtyDiscountPrice= $this->CalculateQuantityDiscount($cartItemId, $cartProduct['original_price']);

		// Apply customer group discount
		$this->cartSession['ITEMS'][$cartItemId]['product_price'] = CalcProdCustomerGroupPrice($product, $QtyDiscountPrice, $customerGroup);

		if(isset($this->cartSession['NUM_ITEMS']) && $this->cartSession['NUM_ITEMS'] > 0) {
			$this->cartSession['NUM_ITEMS'] += $quantity;
		}
		else {
			$this->cartSession['NUM_ITEMS'] = $quantity;
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		// Reapply any coupons so that the new product gets the discount
		$this->ReapplyCoupons();

		return $cartItemId;
	}

	/**
	* Gets a list of coupons and attempts to reapply them to products so all applicable products get the discount
	*
	*/
	protected function ReapplyCoupons()
	{
		$coupons = $this->GetAppliedCouponCodes();
		if (count($coupons)) {
			foreach ($coupons as $coupon) {
				$this->ApplyCoupon($coupon['couponcode']);
			}
		}
	}

	public function CheckCartQuantity($productId, $quantity=0, $variationId=0)
	{

		$products = $this->GetProductsInCart();

		$available = 0;

		$inCart = false;
		$qtyChecking = false;

		foreach ($products as $id=>$productInfo) {
				if ($productInfo['product_id'] == $productId && $productInfo['variation_id'] == $variationId) {
					$quantity += $productInfo['quantity'];
					$available = $productInfo['data']['prodcurrentinv'];
					$qtyChecking = $productInfo['data']['prodinvtrack'];
					$inCart = true;
				}
		}

		if ($inCart && $qtyChecking && $quantity > $available) {
			return false;
		}

		return true;
	}

	/**
	 * Update the configurable fields of an item in the shopping cart.
	 *
	 * @param string The ID of the item in the cart to update the configurable fields for.
	 * @param array An array of the configurable fields/options to be updated.
	 * @return boolean True if successful, false if not.
	 */
	public function UpdateItemConfiguration($itemId, $configurableOptions)
	{
		if(!isset($this->cartSession['ITEMS'][$itemId])) {
			return false;
		}

		$productFields = $this->GetConfigurableFieldData($this->cartSession['ITEMS'][$itemId]['product_id'], $configurableOptions, $itemId);
		if($productFields === false) {
			return false;
		}

		if(empty($productFields)) {
			unset($this->cartSession['ITEMS'][$itemId]['product_fields']);
		}
		else {
			$this->cartSession['ITEMS'][$itemId]['product_fields'] = $productFields;
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return true;
	}

	public function UpdateEventDate($itemId, $eventData)
	{
		if(!isset($this->cartSession['ITEMS'][$itemId])) {
			return false;
		}

		$this->cartSession['ITEMS'][$itemId]['event_date'] = isc_gmmktime(0,0,0,$eventData['Mth'],$eventData['Day'],$eventData['Yr']);

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return true;
	}

	/**
	 * Fetch all of the configurable fields/options for a particular product.
	 *
	 * @param int The product ID to fetch the configurable fields for.
	 * @return array An array of configurable fields for the product.
	 */
	public function GetProductConfigurableFields($productId)
	{
		$configurableFields = array();
		$query = "
			SELECT *
			FROM [|PREFIX|]product_configurable_fields
			WHERE fieldprodid='".(int)$productId."'
			ORDER BY fieldsortorder
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		while($field = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			$configurableFields[$field['productfieldid']] = $field;
		}
		return $configurableFields;
	}

	/**
	 * Upload a file for a configurable product field.
	 *
	 * @param array Array containing the settings for the product field.
	 * @param array Array contanining the file information (generally from $_FILE)
	 * @return boolean True if successful, false if not.
	 */
	private function UploadConfigurableFile($fieldOptions, $file)
	{
		if(!is_array($fieldOptions) || !is_array($file)) {
			return false;
		}

		$extension = GetFileExtension($file['name']);
		$allowedExtensions = array_map('trim', explode(',', $fieldOptions['fieldfiletype']));
		$allowedExtensions = array_map('isc_strtolower', $allowedExtensions);
		if($fieldOptions['fieldfiletype'] && !in_array(isc_strtolower($extension), $allowedExtensions)) {
			$this->SetError(sprintf(GetLang('InvalidFileType'), isc_html_escape($fieldOptions['fieldfiletype'])));
			return false;
		}

		// Check that the maximum size is not exceeded
		if($fieldOptions['fieldfilesize'] > 0 && $file['size'] > $fieldOptions['fieldfilesize']*1024) {
			$this->SetError(sprintf(GetLang('InvalidFileSize'), $fieldOptions['fieldfilesize']));
			return false;
		}

		// Store the uploaded files in our configured products directory
		/**
		 * @todo Implement temporary sharing/storage location with automatic pruning.
		 */
		$uploadDirectory = ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products_tmp/';
		$fileName = $fieldOptions['productfieldid'].'_'.md5(uniqid()).'.'.$extension;
		if(!move_uploaded_file($file['tmp_name'], $uploadDirectory.$fileName)) {
			$this->SetError(sprintf(GetLang('CanNotUploadFile')));
			return false;
		}

		// If we've just uploaded an image, we need to perform a bit of additional validation
		// to ensure it's not someone uploading bogus images.
		$imageExtensions = array(
			'gif',
			'png',
			'jpg',
			'jpeg',
			'jpe',
			'tiff',
			'bmp'
		);
		if(in_array($extension, $imageExtensions)) {
			// Check a list of known MIME types to establish the type of image we're uploading
			switch(isc_strtolower($file['type'])) {
				case 'image/gif':
					$imageType = IMAGETYPE_GIF;
					break;
				case 'image/jpg':
				case 'image/x-jpeg':
				case 'image/x-jpg':
				case 'image/jpeg':
				case 'image/pjpeg':
				case 'image/jpg':
					$imageType = IMAGETYPE_JPEG;
					break;
				case 'image/png':
				case 'image/x-png':
					$imageType = IMAGETYPE_PNG;
					break;
				case 'image/bmp':
					$imageType = IMAGETYPE_BMP;
					break;
				case 'image/tiff':
					$imageType = IMAGETYPE_TIFF_II;
					break;
				default:
					$imageType = 0;
			}

			$imageDimensions = getimagesize($uploadDirectory.$fileName);
			if(!is_array($imageDimensions) || $imageDimensions[2] != $imageType) {
				@unlink($uploadDirectory.$fileName);
				$this->SetError(GetLang('InvalidImageFile'));
				return false;
			}
		}

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return $fileName;
	}

	/**
	 * Build an array of information about a configurable product field so that it can be added under
	 * an item to the cart.
	 *
	 * @param int The ID of the product that has the configurable fields.
	 * @param array An array containing the product fields in fieldId => value format (value is an array for files)
	 * @param int If these configurable fields should be built for an existing item in the cart, the ID of that cart item.
	 * @return boolean True if successful, false if not.
	 */
	public function GetConfigurableFieldData($productId, $fieldData, $existingItemId=-1)
	{
		$configurableFields = $this->GetProductConfigurableFields($productId);

		// Product has no configurable fields, return
		if(empty($configurableFields)) {
			return array();
		}

		$existingFields = array();
		if($existingItemId > -1 && isset($this->cartSession['ITEMS'][$existingItemId]['product_fields'])) {
			$existingFields = $this->cartSession['ITEMS'][$existingItemId]['product_fields'];
		}
		$fieldValues = array();
		foreach($configurableFields as $fieldId => $field) {
			switch($field['fieldtype']) {
				case 'file':
					// The field is required and we don't have a value
					if($field['fieldrequired'] == 1 && !isset($fieldData[$fieldId]) && !isset($existingFields[$fieldId])) {
						$this->SetError(GetLang('EnterRequiredField'));
						return false;
					}

					// Field wasn't uploaded/changed/specified, so carry on
					if(!isset($fieldData[$fieldId]) || !is_array($fieldData[$fieldId]) || !$fieldData[$fieldId]['name']) {
						if(isset($existingFields[$fieldId])) {
							$fieldValues[$fieldId] = $existingFields[$fieldId];
						}
						continue;
					}

					$file = $fieldData[$fieldId];

					// If we can't save the uploaded file, throw back an error
					$uploadedFile = $this->UploadConfigurableFile($field, $file);
					if($uploadedFile === false) {
						$this->productLevelError = true;
						return false;
					}

					// If there was an existing file, delete it
					if(isset($existingFields[$fieldId]['fileName']) && $existingFields[$fieldId]['fileName'] != $uploadedFile) {
						@unlink(ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products_tmp/'.$existingFields[$fieldId]['fileName']);
					}

					$fieldValues[$fieldId] = array(
						'fieldType' => $field['fieldtype'],
						'fieldName' => $field['fieldname'],
						'fileType' => $file['type'],
						'fileOriginName' => $file['name'],
						'fileName' => $uploadedFile
					);
					break;
				default:
					// The field is required and we don't have a value
					if($field['fieldrequired'] == 1 && (!isset($fieldData[$fieldId]) || !trim($fieldData[$fieldId]))) {
						$this->SetError(GetLang('EnterRequiredField'));
						return false;
					}

					if(!isset($fieldData[$fieldId])) {
						continue;
					}

					// Save the field
					$fieldValues[$fieldId] = array(
						'fieldType' => $field['fieldtype'],
						'fieldName' => $field['fieldname'],
						'fieldValue' => $fieldData[$fieldId]
					);
			}
		}
		return $fieldValues;
	}

	/**
	 * Apply gift wrapping to a particular item in the cart.
	 *
	 * @param string The ID of the item in the cart to apply gift wrapping to.
	 * @param string The method to wrap items (same to wrap all items the same, otherwise items are wrapped individually)
	 * @param array An array containing the wrapping settings for each item being wrapped.
	 * @param array An array containing the wrapping gift messages (if any) for each item being wrapped.
	 * @return boolean True if successful, false if not.
	 */
	public function ApplyGiftWrapping($itemId, $wrappingType='same', $wrappingOptions=array(), $wrappingMessages=array())
	{
		$cartProducts = $this->GetProductsInCart();

		if(!isset($cartProducts[$itemId])) {
			$this->SetError(GetLang('GiftWrappingNotApplied'));
			return false;
		}

		$cartProduct = $cartProducts[$itemId];

		// Gift wrapping isn't available for this product
		if(isset($cartProduct['data']['prodwrapoptions']) && $cartProduct['data']['prodwrapoptions'] == -1) {
			$this->SetError(GetLang('GiftWrappingNotApplied'));
			return false;
		}

		// If we have a quantity of one in the cart or selected "all the same" it's quite easy to do this
		if($cartProduct['quantity'] == 1 || $wrappingType == 'same') {
			if(!isset($wrappingOptions['all']) || !$wrappingOptions['all']) {
				unset($this->cartSession['ITEMS'][$itemId]['wrapping']);
				return true;
			}

			// Load the select method of gift wrapping
			$query = "
				SELECT *
				FROM [|PREFIX|]gift_wrapping
				WHERE wrapid='".(int)$wrappingOptions['all']."'
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$wrapping = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
			if(!isset($wrapping['wrapid']) || ($wrapping['wrapvisible'] == 0 && !in_array($wrapping['wrapid'], explode(',', $cartProduct['data']['prodwrapoptions'])))) {
				$this->SetError(GetLang('GiftWrappingNotApplied'));
				return false;
			}

			// Apply it to the item in the cart
			$wrappingOption = array(
				'wrapid' => $wrapping['wrapid'],
				'wrapprice' => $wrapping['wrapprice'],
				'wrapname' => $wrapping['wrapname'],
				'wrapmessage' => ''
			);

			if($wrapping['wrapallowcomments'] == 1 && isset($wrappingMessages['all'])) {
				$wrappingOption['wrapmessage'] = $wrappingMessages['all'];
			}

			$this->cartSession['ITEMS'][$itemId]['wrapping'] = $wrappingOption;

			// Reset the cached cart back to an empty array
			$this->cartProducts = array();

			return true;
		}
		// Otherwise, we've selected multiple types of gift wrapping, so we need to break each item up in the cart
		else {
			$wrappingIds = range(1, $cartProduct['quantity']);
			$wrappedCount = 0;
			$newCartItems = array();
			foreach($wrappingIds as $id) {
				if(!isset($wrappingOptions[$id]) || $wrappingOptions[$id] == '') {
					continue;
				}

				// Load the select method of gift wrapping
				$query = "
					SELECT *
					FROM [|PREFIX|]gift_wrapping
					WHERE wrapid='".(int)$wrappingOptions[$id]."'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$wrapping = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

				// Ignore any invalid selections
				if(!isset($wrapping['wrapid']) || ($wrapping['wrapvisible'] == 0 && !in_array($wrapping['wrapid'], explode(',', $cartProduct['data']['prodwrapoptions'])))) {
					continue;
				}

				// Apply it to the item in the cart
				$wrappingOption = array(
					'wrapid' => $wrapping['wrapid'],
					'wrapprice' => $wrapping['wrapprice'],
					'wrapname' => $wrapping['wrapname']
				);

				if($wrapping['wrapallowcomments'] == 1 && isset($wrappingMessages[$id])) {
					$wrappingOption['wrapmessage'] = $wrappingMessages[$id];
				}

				++$wrappedCount;
				$actualCartItem = $cartProduct;
				$actualCartItem['quantity'] = 1;
				$actualCartItem['wrapping'] = $wrappingOption;
				$newCartItems[] = $actualCartItem;
			}


			$newCart = array();

			// Remove all created items as we don't want to copy them.
			foreach ($this->cartSession['ITEMS'] as $key=>$item) {
				$this->RemoveItemChildren($key);
			}

			// Now we rebuild the cart to insert all of these items as SEPARATE items
			// after each other - we could use some array_splice hackery to do this
			// but it's just quicker if we do it this way as we want to rebuild the keys
			// too.
			foreach($this->cartSession['ITEMS'] as $key => $item) {
				if($key != $itemId) {
					$newCart[] = $item;
				}
				else {
					// One or more items remain un-giftwrapped, so add them
					if($cartProduct['quantity'] != $wrappedCount) {
						$item['quantity'] -= $wrappedCount;
						$newCart[] = $item;
					}
					// Add in the wrapped items
					foreach($newCartItems as $item) {
						$newCart[] = $item;
					}
				}
			}

			// Reset the cached cart back to an empty array
			$this->cartProducts = array();

			$this->cartSession['ITEMS'] = $newCart;

			$this->UpdateCartInformation();
			return true;
		}
	}

	/**
	 * Update the active variation for an item in the cart.
	 *
	 * @param string The item ID of the item in the shopping cart to update.
	 * @param mixed The variation ID (or array of variation information) of the product that should be added to the cart. If null, no variation is added.
	 * @param int Optionally a group ID to calculate the price for.
	 * @return boolean True if successful, false if not.
	 */
	public function UpdateItemVariation($itemId, $variationDetails=null, $groupId=null)
	{
		$cartProducts = $this->GetProductsInCart();

		if(!isset($cartProducts[$itemId])) {
			$this->SetError('2');
			return false;
		}

		$cartItem = $cartProducts[$itemId];

		// Did we add a variation?
		$variation = 0;
		if(IsId($variationDetails)) {
			$variation = (int)$variationDetails;
		}

		// User added a variation but had javascript disabled, need to fetch the variations and find a matching one
		else if(is_array($variationDetails) && $variationDetails['variation'][1] != 0) {
			// Load the variations for this product
			$GLOBALS['ISC_CLASS_PRODUCT'] = new ISC_PRODUCT($cartItem['product_id']);
			$variation = $GLOBALS['ISC_CLASS_PRODUCT']->GetVariationCombination($variationDetails);
		}

		$query = "
			SELECT p.*, ".GetProdCustomerGroupPriceSQL()."
			FROM [|PREFIX|]products p
			WHERE p.productid='".(int)$cartItem['product_id']."'
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$product = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		// Reset the price of the product back to the default
		$productPrice = $cartItem['data']['prodcalculatedprice'];

		$variationOptions = array();
		if($variation != 0) {
			// Load the variation
			$query = "
				SELECT *
				FROM [|PREFIX|]product_variation_combinations
				WHERE combinationid='".$GLOBALS['ISC_CLASS_DB']->Quote($variation)."'
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$productVariation = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if(!$productVariation['combinationid']) {
				$this->SetError('5');
				return false;
			}

			// Load the combination details for this variation
			$query = "
				SELECT *
				FROM [|PREFIX|]product_variation_options
				WHERE voptionid IN (".$productVariation['vcoptionids'].")
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($variationOption = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$variationOptions[$variationOption['voname']] = $variationOption['vovalue'];
			}

			// Calculate the price of this variation
			$productPrice = CalcProductVariationPrice($productPrice, $productVariation['vcpricediff'], $productVariation['vcprice']);

		}

		if($cartItem['data']['prodoptionsrequired'] && $cartItem['data']['prodvariationid'] && !$variation) {
			$this->SetError(GetLang('ErrorNoVariationSelected'));
			return false;
		}

		// Finally, determine the price based on the customer group
		$productPrice = CalcProdCustomerGroupPrice($product, $productPrice, $groupId);

		$currentInventory = $cartItem['data']['prodcurrentinv'];
		if($variation > 0) {
			$currentInventory = $productVariation['vcstock'];
		}

		// Do we have enough of this particular item in stock? If not, return with an error
		if(isset($cartItem['data']['prodinvtrack']) && $cartItem['data']['prodinvtrack'] > 0 && $cartItem['quantity'] > $currentInventory) {
			if($cartItem['quantity'] == 1) {
				$this->SetError(sprintf(GetLang('CannotAddQuantityToCartSingle'), $product['prodname']));
			}
			else {
				$this->SetError(sprintf(GetLang('CannotAddQuantityToCart'), $cartItem['quantity'], $product['prodname']));
			}
			return false;
		}

		$this->cartSession['ITEMS'][$itemId]['options'] = $variationOptions;
		$this->cartSession['ITEMS'][$itemId]['variation_id'] = $variation;
		$this->cartSession['ITEMS'][$itemId]['original_price'] = $productPrice;
		$this->cartSession['ITEMS'][$itemId]['product_price'] = $productPrice;

		// Reset the cached cart back to an empty array
		$this->cartProducts = array();

		return true;
	}

	/**
	 * Determine the total wrapping cost for all of the items in the cart.
	 *
	 * @return float The calculated wrapping cost.
	 */
	public function GetWrappingCost()
	{
		$wrappingCost = 0;
		$products = $this->GetProductsInCart();
		foreach($products as $product) {
			if(!isset($product['wrapping']) || !is_array($product['wrapping'])) {
				continue;
			}
			$wrappingCost += $product['wrapping']['wrapprice'];
		}
		return $wrappingCost;
	}

	/**
	 * Return an array containing all of the gift certificates applied to items in the cart.
	 *
	 * @return array An array of gift certificates applied.
	 */
	public function GetGiftCertificates()
	{
		if(!isset($this->cartSession['GIFTCERTIFICATES'])) {
			return array();
		}
		$certificates = $this->cartSession['GIFTCERTIFICATES'];
		uasort($certificates, "GiftCertificateSort");
		return $certificates;
	}

	/**
	 * Check and return if the cart only contains gift certificates.
	 *
	 * @return boolean True if the cart only contains gift certificates.
	 */
	public function ContainsOnlyGiftCertificates()
	{
		return $this->onlyGiftCertificates;
	}

	/**
	 * Update specific details about an item in the cart (name, price, code etc)
	 *
	 * @param string The ID of the cart item to update the details for.
	 * @param array Details of the product to update.
	 * @return boolean True if successful, false if not.
	 */
	public function UpdateCartProduct($itemId, $product)
	{
		if(!isset($this->cartSession['ITEMS'][$itemId])) {
			return false;
		}

		$fields = array(
			'product_name' => 'prodname',
			'product_code' => 'prodcode',
			'product_price' => 'prodprice',
			'original_price' => 'prodoriginalprice'
		);

		$currentPrice = $this->cartSession['ITEMS'][$itemId]['product_price'];

		foreach($fields as $cartField => $productField) {
			if(!isset($product[$productField])) {
				continue;
			}

			$this->cartSession['ITEMS'][$itemId][$cartField] = $product[$productField];
		}

		if(isset($product['prodprice']) && $product['prodprice'] != $currentPrice) {
			$this->cartSession['ITEMS'][$itemId]['custom_price'] = true;
		}

		$this->cartProducts = $this->cartSession['ITEMS'];

		// Update the quantity too if it's present
		if(isset($product['quantity'])) {
			$quantityCheck = 0;
			// only check the difference between the original quantity
			if (isset($this->cartSession['ITEMS'][$itemId]['original_quantity'])) {
				$quantityCheck = $product['quantity'] - $this->cartSession['ITEMS'][$itemId]['original_quantity'];
				if ($quantityCheck < 0) {
					$quantityCheck = 0;
				}
			}

			return $this->UpdateCartQuantity($itemId, $product['quantity'], $quantityCheck);
		}

		$this->cartProducts = array();
		return true;
	}
}
