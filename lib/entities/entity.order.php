<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'entity.base.php');

class ISC_ENTITY_ORDER extends ISC_ENTITY_BASE
{
	private $shipping;
	private $product;
	private $customer;

	protected $useTransactions;

	/**
	 * Constructor
	 *
	 * Base constructor
	 *
	 * @access public
	 */
	public function __construct()
	{
		$schema = array(
				"orderid" => "int",
				"ordtoken" => "text",
				"ordcustid" => "int",
				"orddate" => "date",
				"ordlastmodified" => "date",
				"ordsubtotal" => "price",
				"ordtaxtotal" => "price",
				"ordtaxname" => "text1",
				"ordtaxrate" => "text1",
				"ordtotalincludestax" => "bool",
				"ordshipcost" => "price",
				"ordshipmethod" => "text",
				"ordhandlingcost" => "price",
				"ordtotalamount" => "price",
				"ordstatus" => "int",
				"ordtotalqty" => "int",
				"ordtotalshipped" => "int",
				"orderpaymentmethod" => "text",
				"orderpaymentmodule" => "text",
				"ordpayproviderid" => "text",
				"ordpaymentstatus" => "text",
				"ordrefundedamount" => "price",
				"ordbillfirstname" => "text",
				"ordbilllastname" => "text",
				"ordbillcompany" => "text",
				"ordbillstreet1" => "text",
				"ordbillstreet2" => "text",
				"ordbillsuburb" => "text",
				"ordbillstate" => "text",
				"ordbillzip" => "text",
				"ordbillcountry" => "text",
				"ordbillcountrycode" => "text",
				"ordbillcountryid" => "int",
				"ordbillstateid" => "int",
				"ordbillphone" => "text",
				"ordbillemail" => "text",
				"ordshipfirstname" => "text",
				"ordshiplastname" => "text",
				"ordshipcompany" => "text",
				"ordshipstreet1" => "text",
				"ordshipstreet2" => "text",
				"ordshipsuburb" => "text",
				"ordshipstate" => "text",
				"ordshipzip" => "text",
				"ordshipcountry" => "text",
				"ordshipcountrycode" => "text",
				"ordshipcountryid" => "int",
				"ordshipstateid" => "int",
				"ordshipphone" => "text",
				"ordshipemail" => "text",
				"ordisdigital" => "bool",
				"ordtrackingno" => "text",
				"orddateshipped" => "date",
				"ordgatewayamount" => "price",
				"ordstorecreditamount" => "price",
				"ordgiftcertificateamount" => "price",
				"ordinventoryupdated" => "bool",
				"ordonlygiftcerts" => "bool",
				"extrainfo" => "text",
				"ordipaddress" => "text",
				"ordgeoipcountry" => "text",
				"ordgeoipcountrycode" => "text",
				"ordcurrencyid" => "int",
				"orddefaultcurrencyid" => "int",
				"ordcurrencyexchangerate" => "price",
				"ordshippingzoneid" => "int",
				"ordshippingzone" => "text",
				"ordnotes" => "text",
				"ordcustmessage" => "text",
				"ordvendorid" => "int",
				"ordformsessionid" => "int",
				"orddiscountamount" => "price",
		);

		$tableName = "orders";
		$primaryKeyName = "orderid";
		$searchFields = array(
				"orderid",
				"ordtoken",
				"ordcustid",
				"ordbillfirstname",
				"ordbilllastname",
				"ordbillemail",
				"ordshipfirstname",
				"ordshiplastname",
				"ordshipemail"
		);

		$customKeyName = "ordformsessionid";

		parent::__construct($schema, $tableName, $primaryKeyName, $searchFields, $customKeyName);

		$this->shipping = new ISC_ENTITY_SHIPPING();
		$this->product = new ISC_ENTITY_PRODUCT();
		$this->customer = new ISC_ENTITY_CUSTOMER();

		$this->useTransactions = true;
	}

	private function setAddresses(&$savedata, $rawInput)
	{
		$addressMap = array(
			"firstname" => "firstname",
			"lastname" => "lastname",
			"company" => "company",
			"street1" => "address1",
			"street2" => "address2",
			"suburb" => "city",
			"state" => "state",
			"stateid" => "stateid",
			"zip" => "zip",
			"country" => "country",
			"countryid" => "countryid"
		);

		$addressSectionMap = array(
			"bill" => "billing",
			"ship" => "shipping"
		);

		foreach ($addressSectionMap as $ordSectionKey => $addressSectionKey) {
			$addressSectionName = $addressSectionKey . "address";

			if (!array_key_exists($addressSectionName, $rawInput) || !is_array($rawInput[$addressSectionName])) {
				continue;
			}

			foreach ($addressMap as $ordKey => $addressKey) {
				$columnName = "ord" . $ordSectionKey . $ordKey;
				$addressName = "ship" . $addressKey;

				if (!array_key_exists($addressName, $rawInput[$addressSectionName])) {
					continue;
				}

				$savedata[$columnName] = $rawInput[$addressSectionName][$addressName];
			}
		}
	}

	protected function addPrehook(&$savedata, $rawInput)
	{
		$GLOBALS["ISC_CLASS_ACCOUNT"] = GetClass("ISC_ACCOUNT");

		// Load the shipping address if we don't have a custom one
		if (array_key_exists("shippingaddressid", $rawInput) && isId($rawInput["shippingaddressid"])) {
			$rawInput["shippingaddress"] = $GLOBALS["ISC_CLASS_ACCOUNT"]->GetShippingAddress($rawInput["shippingaddressid"]);
		}

		// Load the billing address if we don't have a custom one
		if (array_key_exists("billingaddressid", $rawInput) && isId($rawInput["billingaddressid"])) {
			$rawInput["billingaddress"] = $GLOBALS["ISC_CLASS_ACCOUNT"]->GetShippingAddress($rawInput["billingaddressid"]);
		}

		// If we don"t have a shipping address for this order then it's a digital order - we need to set up an empty
		// array with the address fields
		if(!array_key_exists("shippingaddress", $rawInput)) {
			$rawInput["shippingaddress"] = array(
				"shipfirstname"		=> "",
				"shiplastname"		=> "",
				"shipaddress1"		=> "",
				"shipaddress2"		=> "",
				"shipcity"			=> "",
				"shipstate"			=> "",
				"shipzip"			=> "",
				"shipcountry"		=> "",
				"shipcountryid"		=> "",
				"shipstateid"		=> "",
				"shipcompany"		=> "",
			);
		}

		if(!array_key_exists("ordstatus", $rawInput)) {
			$savedata["ordstatus"] = 0;
		}

		$providerName = "";
		$providerId = "";

		// Order was paid for entirely with gift certificates
		if ($rawInput["orderpaymentmodule"] == "giftcertificate") {
			$providerName = "giftcertificate";
			$providerid = "";
		}
		// Order was paid for entirely using store credit
		else if ($rawInput["orderpaymentmodule"] == "storecredit") {
			$providerName = "storecredit";
			$providerId = "";
		}
		// Went through some sort of payment gateway
		else {
			if (array_key_exists("ordgatewayamount", $rawInput) && $rawInput["ordgatewayamount"] > 0) {
				if (GetModuleById("checkout", $provider, $rawInput["orderpaymentmodule"]) && is_object($provider)) {
					$providerName = $provider->GetDisplayName();
					$providerId = $provider->GetId();
				}
				else {
					$providerId = $rawInput["orderpaymentmodule"];
					$providerName = $rawInput["orderpaymentmethod"];
				}
			}
			else {
				$providerName = "";
				$providerId = "";
			}
		}

		$savedata["orderpaymentmodule"] = $providerId;
		$savedata["orderpaymentmethod"] = $providerName;

		/**
		 * Get the customer ID if we don't have it
		 */
		if (!array_key_exists("ordcustid", $rawInput) && array_key_exists("customertoken", $rawInput)) {
			$GLOBALS["ISC_CLASS_CUSTOMER"] = GetClass("ISC_CUSTOMER");
			$savedata["ordcustid"] = $GLOBALS["ISC_CLASS_CUSTOMER"]->GetCustomerIdByToken($rawInput["customertoken"]);
		}

		/**
		 * Loop through all of the products in this order to see if they're entirely gift certificates. Count the total
		 * quantity while we are here
		 */
		$savedata["ordonlygiftcerts"] = 1;
		$savedata["ordtotalqty"] = 0;
		foreach ($rawInput["products"] as $product) {
			if (isset($product["type"]) && $product["type"] != "giftcertificate") {
				$savedata["ordonlygiftcerts"] = 0;
			}

			$savedata["ordtotalqty"] += $product["quantity"];
		}

		// Fetch the country codes for the billing and shipping addresses
		$savedata["ordbillcountrycode"] = GetCountryISO2ByName($rawInput["billingaddress"]["shipcountry"]);

		if (isset($rawInput["shippingaddress"]["shipcountry"])) {
			$savedata["ordshipcountrycode"] = GetCountryISO2ByName($rawInput["shippingaddress"]["shipcountry"]);
		}

		if (!array_key_exists("ordgeoipcountry", $rawInput) && !array_key_exists("ordgeoipcountrycode", $rawInput)) {
			// Attempt to determine the GeoIP location based on their IP address

			require_once ISC_BASE_PATH."/lib/geoip/geoip.php";
			$gi = geoip_open(ISC_BASE_PATH."/lib/geoip/GeoIP.dat", GEOIP_STANDARD);
			$savedata["ordgeoipcountrycode"] = geoip_country_code_by_addr($gi, GetIP());

			// If we get the country, look up the country name as well
			if (trim($savedata["ordgeoipcountrycode"]) !== "") {
				$savedata["ordgeoipcountry"] = geoip_country_name_by_addr($gi, GetIP());
			}
		}

		if (isset($rawInput['vendorid'])) {
			$savedata["ordvendorid"] = $rawInput['vendorid'];
		}
		else {
			$savedata["ordvendorid"] = 0;
		}

		if (!array_key_exists("extraInfo", $rawInput) || !is_array($rawInput["extraInfo"])) {
			$savedata["extraInfo"] = array();
		} else {
			$savedata["extraInfo"] = $rawInput["extraInfo"];
		}

		if (array_key_exists("giftcertificates", $rawInput) && is_array($rawInput["giftcertificates"])) {
			$savedata["extraInfo"]["giftcertificates"] = $rawInput["giftcertificates"];
		}

		$savedata["extraInfo"] = serialize($savedata["extraInfo"]);

		if (!array_key_exists("ordshippingzoneid", $rawInput)) {
			$savedata["ordshippingzoneid"] = 0;
		}

		if (!array_key_exists("ordshippingzone", $rawInput)) {
			$savedata["ordshippingzone"] = "";
		}

		if (isset($rawInput["billingaddress"]) && is_array($rawInput["billingaddress"])) {
			if (array_key_exists("shipemail", $rawInput["billingaddress"])) {
				$savedata["ordbillemail"] = $rawInput["billingaddress"]["shipemail"];
			}

			if (array_key_exists("shipphone", $rawInput["billingaddress"])) {
				$savedata["ordbillphone"] = $rawInput["billingaddress"]["shipphone"];
			}
		}

		if (isset($rawInput["shippingaddress"]) && is_array($rawInput["shippingaddress"])) {
			if (array_key_exists("shipemail", $rawInput["shippingaddress"])) {
				$savedata["ordshipemail"] = $rawInput["shippingaddress"]["shipemail"];
			}

			if (array_key_exists("shipphone", $rawInput["shippingaddress"])) {
				$savedata["ordshipphone"] = $rawInput["shippingaddress"]["shipphone"];
			}
		}

		$customer = "";

		if (array_key_exists("ordcustid", $savedata)) {
			$customer = $this->customer->get($savedata["ordcustid"]);
		}

		/**
		 * If we don't have a billing or shipping email address or phone number but we have a customer, fetch & use the
		 * information from the customer
		 */
		if (is_array($customer)) {
			$fillinMap = array(
				"ordbillemail" => "custconemail",
				"ordshipemail" => "custconemail",
				"ordbillphone" => "custconphone",
				"ordshipphone" => "custconphone",
			);

			foreach ($fillinMap as $saveKey => $customerKey) {
				if (array_key_exists($customerKey, $customer) && (!array_key_exists($saveKey, $savedata) || trim($savedata[$saveKey] == ""))) {
					$savedata[$saveKey] = $customer[$customerKey];
				}
			}
		}

		$defaultCurrency = GetDefaultCurrency();

		if (is_array($defaultCurrency) && array_key_exists("currencyid", $defaultCurrency) && isId($defaultCurrency["currencyid"])) {
			$savedata["orddefaultcurrencyid"] = $defaultCurrency["currencyid"];
		}

		$this->setAddresses($savedata, $rawInput);

		$savedata["orddate"] = time();
		$savedata["ordlastmodified"] = time();
	}

	protected function editPrehook($orderId, &$savedata, $rawInput)
	{
		if (array_key_exists("products", $rawInput)) {
			$savedata["ordtotalqty"] = 0;
			foreach ($rawInput["products"] as $product) {
				$savedata["ordtotalqty"] += $product["quantity"];
			}
		}

		if (array_key_exists("extraInfo", $rawInput) && is_array($rawInput["extraInfo"])) {
			if (array_key_exists("giftcertificates", $rawInput) && is_array($rawInput["giftcertificates"])) {
				$rawInput["extraInfo"]["giftcertificates"] = $rawInput["giftcertificates"];
			}

			$savedata["extraInfo"] = serialize($rawInput["extraInfo"]);
		}

		$this->setAddresses($savedata, $rawInput);

		$savedata["ordlastmodified"] = time();
	}

	private function commitProducts($orderId, $products, $editingExisting=false)
	{
		$existingOrder = null;
		if ($editingExisting) {
			$existingOrder = GetOrder($orderId);
		}

		$couponsUsed = array();
		$giftCertificates = array();

		foreach ($products as $product) {

			$existingProduct = false;
			if(isset($product['existing_order_product']) && isset($existingOrder['products'][$product['existing_order_product']])) {
				$existingProduct = $existingOrder['products'][$product['existing_order_product']];
				unset($existingOrder['products'][$product['existing_order_product']]);
			}

			if(!isset($product['product_code'])) {
				$product['product_code'] = '';
			}

			if(!isset($product['variation_id'])) {
				$product['variation_id'] = 0;
			}


			if(isset($product['discount_price'])) {
				$price = $product['discount_price'];
			}
			else {
				$price = $product['product_price'];
			}

			// Set up some default values for the product
			$newProduct = array(
				'ordprodsku'			=> $product['product_code'],
				"ordprodname"			=> $product['product_name'],
				"ordprodtype"			=> '',
				"ordprodcost"			=> $price,
				"ordprodoriginalcost"	=> $product['product_price'],
				"ordprodweight"			=> 0,
				"ordprodqty"			=> $product['quantity'],
				"orderorderid"			=> $orderId,
				"ordprodid"				=> $product['product_id'],
				"ordprodvariationid"	=> $product['variation_id'],
				"ordprodoptions"		=> '',
				"ordprodcostprice"		=> 0,
				"ordprodfixedshippingcost" => 0,
				"ordprodistaxable"		=> 1,
				"ordprodwrapid"			=> 0,
				"ordprodwrapname"		=> '',
				"ordprodwrapcost"		=> 0,
				"ordprodwrapmessage"	=> ''
			);

			// This product is a gift certificate so set the appropriate values
			if (isset($product['type']) && $product['type'] == "giftcertificate") {
				// Gift certificates can't be edited
				if(isset($product['existing_order_product'])) {
					continue;
				}

				$newProduct['ordprodtype'] = 'giftcertificate';
				$giftCertificates[] = $product;
			}

			// Normal product
			else {
				if(isset($product['data'])) {
					$newProduct['ordprodtype'] = $product['data']['prodtype'];
				}
				else {
					$newProduct['ordprodtype'] = 'physical';
				}
			}

			if(isset($product['data']['prodcostprice'])) {
				$newProduct['ordprodcostprice'] = (float)$product['data']['prodcostprice'];
			}

			if(isset($product['options'])) {
				$newProduct['ordprodoptions'] = serialize($product['options']);
			}

			if (isset($product['data']['prodweight'])) {
				$newProduct['ordprodweight'] = $product['data']['prodweight'];
			}

			if (isset($product['data']['prodfixedshippingcost'])) {
				$newProduct['ordprodfixedshippingcost'] = $product['data']['prodfixedshippingcost'];
			}

			if (isset($product['data']['prodistaxable'])) {
				$newProduct['ordprodistaxable'] = $product['data']['prodistaxable'];
			}

			if (isset($product['event_date']) && isset($product['event_name'])) {
				$newProduct['ordprodeventdate'] = $product['event_date'];
				$newProduct['ordprodeventname'] = $product['event_name'];
			}

			// If wrapping has been applied to this product, add it in
			if(isset($product['wrapping'])) {
				$newProduct['ordprodwrapid'] = $product['wrapping']['wrapid'];
				$newProduct['ordprodwrapname'] = $product['wrapping']['wrapname'];
				$newProduct['ordprodwrapcost'] = $product['wrapping']['wrapprice'];
				if(isset($product['wrapping']['wrapmessage'])) {
					$newProduct['ordprodwrapmessage'] = $product['wrapping']['wrapmessage'];
				}
			}

			if (isset($product['original_price'])) {
				$newProduct['ordoriginalprice'] = $product['original_price'];
			}

			if(is_array($existingProduct)) {
				$ordProdID = $existingProduct['orderprodid'];
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery('order_products', $newProduct, "orderprodid='".(int)$ordProdID."'");

				// Delete any existing product fields we don't have
				$query = "
					SELECT orderfieldid, filename
					FROM [|PREFIX|]order_configurable_fields
					WHERE ordprodid='".$ordProdID."' AND fieldtype='file'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($field = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					@unlink(ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products/'.$field['filename']);
				}
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_configurable_fields', "WHERE ordprodid='".$ordProdID."'");
			}
			else {
				$ordProdID = $GLOBALS['ISC_CLASS_DB']->InsertQuery("order_products", $newProduct);
			}

			// Add configurable product fields come with the order to database
			if(isset($product['product_fields'])) {
				foreach ($product['product_fields'] as $fieldId => $field) {


					//move the uploaded file to configured_products folder from the temp folder.
					if($field['fieldType'] == 'file' && trim($field['fileName']) != '') {
						$filePath = ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products/'.$field['fileName'];
						$fileTmpPath = ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products_tmp/'.$field['fileName'];

						//do not remove the temp file here, because the payment may not successful
						//the file should still be viewable in in the cart,
						@copy($fileTmpPath, $filePath);
					}



					$fieldArray = array(
						'ordprodid' 		=> (int)$ordProdID,
						'fieldid'			=> (int)$fieldId,
						'orderid'			=> (int)$orderId,
						'fieldname'			=> $field['fieldName'],
						'fieldtype'			=> $field['fieldType'],
						'textcontents'		=> '',
						'filename'			=> '',
						'filetype'			=> '',
						'originalfilename'	=> '',
						'productid'			=> $product['product_id'],
					);

					if($field['fieldType'] == 'file' && trim($field['fileName']) != '') {
						$fieldArray['filename'] = trim($field['fileName']);
						$fieldArray['filetype'] = trim($field['fileType']);
						$fieldArray['originalfilename'] = trim($field['fileOriginName']);
					}
					else {
						$fieldArray['textcontents'] = trim($field['fieldValue']);
					}
					$GLOBALS['ISC_CLASS_DB']->InsertQuery("order_configurable_fields", $fieldArray);
				}
			}




			// Ensure that coupons aren't being saved with gift certificates
			if (isset($product['couponcode'])) {
				$newOrderCoupon = array(
					"ordcouporderid" => $orderId,
					"ordcoupprodid" => $ordProdID,
					"ordcouponid" => $product['coupon'],
					"ordcouponcode" => $product['couponcode'],
					"ordcouponamount" => $product['discount'],
					"ordcoupontype"	=> $product['coupontype']
				);

				$update_coup = false;
				if (is_array($existingProduct)) {
					$query = "SELECT ordcoupid
								FROM [|PREFIX|]order_coupons
								WHERE ordcoupprodid = " . (int)$ordProdID;

					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						$GLOBALS['ISC_CLASS_DB']->UpdateQuery("order_coupons", $newOrderCoupon, "ordcoupid = " . $row["ordcoupid"]);
						$update_coup = true;
					}
				}

				if (!$update_coup) {
					$GLOBALS['ISC_CLASS_DB']->InsertQuery("order_coupons", $newOrderCoupon);
				}
			}
			else if (is_array($existingProduct)) {
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_coupons', "WHERE ordcoupprodid='".$ordProdID."'");
			}

			if(isset($existingOrder['ordinventoryupdated']) && $existingOrder['ordinventoryupdated'] == 1) {
				// If we're editing an existing order and the quantities or variation have changed, do we need to
				// update the inventory quantities?
				if(is_array($existingProduct) && (
						$existingProduct['ordprodvariationid']) != $newProduct['ordprodvariationid'] ||
						$existingProduct['ordprodqty'] != $newProduct['ordprodqty']
				) {
					AdjustProductInventory($existingProduct['ordprodid'], $existingProduct['ordprodvariationid'], @$product['data']['prodinvtrack'], '+'.$existingProduct['ordprodqty']);
					AdjustProductInventory($newProduct['ordprodid'], $newProduct['ordprodvariationid'], @$product['data']['prodinvtrack'], '-'.$newProduct['ordprodqty']);
				}

				// This is a new product in an existing order with inventory quantities
				// taken away, take them away for this product
				else if(!is_array($existingProduct)) {
					AdjustProductInventory($newProduct['ordprodid'], $newProduct['ordprodvariationid'], @$product['data']['prodinvtrack'], '+'.$newProduct['ordprodqty']);
				}
			}
		}

		// If we have one or more gift certificates to create, we need to create them now.
		if (count($giftCertificates) > 0) {
			$GLOBALS['ISC_CLASS_GIFT_CERTIFICATES'] = GetClass('ISC_GIFTCERTIFICATES');
			$GLOBALS['ISC_CLASS_GIFT_CERTIFICATES']->CreateGiftCertificatesFromOrder($orderId, $giftCertificates, 1);
		}


		// Now remove any deleted items from the order
		if($editingExisting) {
			$removeItemIds = implode(',', array_keys($existingOrder['products']));

			if($removeItemIds != '') {
				$query = "
							SELECT op.orderprodid, p.productid, p.prodinvtrack
							FROM [|PREFIX|]order_products as op
							INNER JOIN [|PREFIX|]products as p
							ON op.ordprodid = p.productid
							WHERE op.orderprodid IN (".$removeItemIds.") AND ordprodid > 0
						";

				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($prod = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$existingOrder['products'][$prod['orderprodid']]['prodinvtrack'] = $prod['prodinvtrack'];
				}

				//update product inventory level
				foreach($existingOrder['products'] as $rmProd) {
					if (!$rmProd['ordprodid']) {
						continue;
					}
					AdjustProductInventory($rmProd['ordprodid'], $rmProd['ordprodvariationid'],  $rmProd['prodinvtrack'], '+'.$rmProd['ordprodqty']);
				}

				// Delete any existing product fields we don't have
				$query = "
					SELECT orderfieldid, filename
					FROM [|PREFIX|]order_configurable_fields
					WHERE ordprodid IN (".$removeItemIds.") AND fieldtype='file'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($field = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					@unlink(ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products/'.$field['filename']);
				}
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_configurable_fields', "WHERE ordprodid IN (".$removeItemIds.")");
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_products', "WHERE orderprodid IN (".$removeItemIds.")");
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_coupons', "WHERE ordcoupprodid IN (".$removeItemIds.")");
			}
		}
		return true;
	}

	private function commitAddresses($rawInput, $savedata=array())
	{
		if (array_key_exists("shippingaddressid", $rawInput) || array_key_exists("billingaddressid", $rawInput)) {
			$addressesToUpdate = array();
			if (array_key_exists("shippingaddressid", $rawInput)) {
				$addressesToUpdate[] = (int)$rawInput["shippingaddressid"];
			}
			if (array_key_exists("billingaddressid", $rawInput)) {
				$addressesToUpdate[] = (int)$rawInput["billingaddressid"];
			}

			$addressesToUpdate = array_unique($addressesToUpdate);

			$updatedAddress = array(
				"shiplastused" => time()
			);
			$GLOBALS["ISC_CLASS_DB"]->UpdateQuery("shipping_addresses", $updatedAddress, "shipid IN (".implode(",", $addressesToUpdate).")");
		}

		// Do we need to save an address?
		if (isset($rawInput["billingaddress"]) && isset($savedata['ordcustid']) && is_array($rawInput["billingaddress"]) && isset($rawInput["billingaddress"]['saveAddress']) && isId($savedata["ordcustid"])) {
			$address = $rawInput["billingaddress"];
			$address["shipcustomerid"] = $savedata["ordcustid"];
			$this->shipping->add($address);
		}

		// Are we saving the shipping address too? We only do this if the customer chose to and
		// the billing & shipping address line 1 aren't the same
		if(isset($rawInput["shippingaddress"]) && isset($savedata['ordcustid']) && is_array($rawInput["shippingaddress"]) && isset($rawInput["shippingaddress"]['saveAddress']) && isId($savedata["ordcustid"])) {
			$address = $rawInput["shippingaddress"];
			$address["shipcustomerid"] = $savedata["ordcustid"];
			if (!$this->shipping->basicSearch($address)) {
				$this->shipping->add($address);
			} else if (isset($address["shipformsessionid"]) && isId($address["shipformsessionid"])) {
				$GLOBALS["ISC_CLASS_FORM"]->deleteFormSession($address["shipformsessionid"]);
			}
		}

		return true;
	}

	protected function addPosthook($orderId, $savedata, $rawInput)
	{
		$this->commitProducts($orderId, $rawInput["products"], false);
		$this->commitAddresses($rawInput, $savedata);

		return true;
	}

	protected function editPosthook($orderId, $savedata, $rawInput)
	{
		$this->commitProducts($orderId, $rawInput["products"], true);
		$this->commitAddresses($rawInput, $savedata);

		return true;
	}

	protected function deletePosthook($orderId, $order, $deleteGiftCertificates=false)
	{
		/**
		 * Set up the delete queries we'll be using
		 */
		$queries = array(
			"DELETE FROM [|PREFIX|]order_products WHERE orderorderid = " . (int)$orderId,
			"DELETE FROM [|PREFIX|]order_coupons WHERE ordcouporderid = " . (int)$orderId,
			"DELETE FROM [|PREFIX|]order_messages WHERE messageorderid = " . (int)$orderId,
			"DELETE FROM [|PREFIX|]order_downloads WHERE orderid = " . (int)$orderId,
		);

		/**
		 * If deleting gift certificates too, add that in to the mix
		 */
		if ($deleteGiftCertificates) {
			$queries[] = "DELETE FROM [|PREFIX|]gift_certificates WHERE giftcertorderid = " . (int)$orderid;
		}

		foreach ($queries as $query) {
			if (!$GLOBALS["ISC_CLASS_DB"]->Query($query)) {
				return false;
			}
		}
		/**
		 * Delete the form session if we can
		 */
		if (isId($order["ordformsessionid"])) {
			$GLOBALS["ISC_CLASS_FORM"]->deleteFormSession($order["ordformsessionid"]);
		}

		return true;
	}

	/**
	 * Delete old incomplete orders
	 *
	 * Method will delete all incomplete orders that are a week old
	 *
	 * @access public
	 * @return bool TRUE if all the incomplete old orders were deleted, FALSE otherwise
	 */
	public function deleteOldOrders()
	{
		$query = "SELECT orderid
					FROM [|PREFIX|]orders
					WHERE ordstatus=0 AND orddate < '".(time()-604800)."'";

		$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
		while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
			self::delete($row["orderid"]);

			$GLOBALS["ISC_CLASS_DB"]->DeleteQuery("gift_certificates", "WHERE giftcertorderid = " . $row["orderid"]);
		}

		return true;
	}

	public function get($orderId, $userOrderAddress=false)
	{
		$order = parent::get($orderId);

		if (!$order) {
			return false;
		}

		$order["customer"] = false;

		if (isId($order["ordcustid"])) {

			$customer = $this->customer->get($order["ordcustid"]);
			$order["customer"] = $customer;

			/**
			 * Assign the addresses that were used in this order if we have to
			 */
			if ($userOrderAddress) {
				$customer["addresses"] = array();

				$customer["addresses"][0] = array(
					'shipid' => '',
					'shipcustomerid' => $order["ordcustid"]
				);

				foreach (array_keys($order) as $key) {
					if (substr($key, 0, 7) == "ordbill") {
						$newKey = substr($key, 7);
						$customer["addresses"][0]["ship" . $newKey] = $entity[$key];
					}
				}

				$customer["addresses"][1] = array(
					"shipid" => "",
					"shipcustomerid" => $order["ordcustid"]
				);

				foreach (array_keys($order) as $key) {
					if (substr($key, 0, 7) == "ordship") {
						$newKey = substr($key, 7);
						$customer["addresses"][1]["ship" . $newKey] = $entity[$key];
					}
				}
			}
		}

		$order["products"] = array();

		$query = "SELECT *
					FROM [|PREFIX|]order_products
					WHERE orderorderid = " . (int)$orderId;

		$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
		while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
			$prod = $this->product->get($row["ordprodid"]);
			if ($prod) {
				$prod["prodorderquantity"] = $row["ordprodqty"];
				$prod["prodorderamount"] = $row["ordprodcost"];
				$prod["prodordvariationid"] = $row["ordprodvariationid"];
				$prod["prodorderid"] = $row["orderprodid"];
				$order["products"][] = $prod;
			}
		}

		return $order;
	}
}
