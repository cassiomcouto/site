<?php
function generatePrintableInvoice($orderId)
{
	$db = $GLOBALS['ISC_CLASS_DB'];

	$template = new TEMPLATE('ISC_LANG');
	$template->frontEnd();
	$template->setTemplateBase(ISC_BASE_PATH . "/templates");
	$template->panelPHPDir = ISC_BASE_PATH . "/includes/display/";
	$template->templateExt = "html";
	$template->setTemplate(getConfig("template"));

	$template->assign('HeaderLogo', fetchHeaderLogo());
	$template->assign('StoreAddressFormatted', nl2br(getConfig('StoreAddress')));

	$query = "
		SELECT o.*, CONCAT(c.custconfirstname, ' ', c.custconlastname) AS ordcustname, c.custconemail AS ordcustemail, c.custconphone AS ordcustphone
		FROM [|PREFIX|]orders o
		LEFT JOIN [|PREFIX|]customers c ON o.ordcustid = c.customerid
		WHERE o.orderid = '".(int)$orderId."'
	";

	$result = $db->Query($query);
	$row = $db->Fetch($result);

	if(!$row) {
		return false;
	}

	$template->assign('OrderId', $row['orderid']);
	$template->assign('OrderDate', cDate($row['orddate']));

	if($row['ordcustmessage']) {
		$template->assign('Comments', nl2br(isc_html_escape($row['ordcustmessage'])));
	}
	else {
		$template->assign('HideComments', 'display: none');
	}

	$template->assign('InvoiceTitle', sprintf(getLang('InvoiceTitle'), $orderId));
	$template->assign('ItemCost', currencyConvertFormatPrice($row['ordsubtotal'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));
	if($row['ordshipcost']) {
		$template->assign('ShippingCost', currencyConvertFormatPrice($row['ordshipcost'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));
	}
	else {
		$template->assign('HideShippingCost', 'display: none');
	}
	// Is there a handling fee?
	if ($row['ordhandlingcost'] > 0) {
		$template->assign('HandlingCost', currencyConvertFormatPrice($row['ordhandlingcost'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));
	}
	else {
		$template->assign('HideHandlingCost', 'display: none');
	}

	// Is there any sales tax?
	if($row['ordtaxtotal'] > 0) {
		if($row['ordtaxname']) {
			$template->assign('SalesTaxName', isc_html_escape($row['ordtaxname']));
		}
		else {
			$template->assign('SalesTaxName', getLang('InvoiceSalesTax'));
		}

		if($row['ordtotalincludestax']) {
			$template->assign('HideSalesTax', 'none');
			$template->assign('SalesTaxName', isc_html_escape($row['ordtaxname']) . ' ' . getLang('IncludedInTotal'));
		}
		else {
			$template->assign('HideSalesTaxIncluded', 'none');
		}
		$template->assign('SalesTax', currencyConvertFormatPrice($row['ordtaxtotal'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));
	}
	else {
		$template->assign('HideSalesTax', 'none');
		$template->assign('HideSalesTaxIncluded', 'none');
	}

	$template->assign('TotalCost', currencyConvertFormatPrice($row['ordtotalamount'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));

	// Format the customer details
	if($row['ordcustid'] == 0) {
		$template->assign('HideCustomerDetails', 'display: none');
	}
	$template->assign('CustomerId', $row['ordcustid']);
	$template->assign('CustomerName', isc_html_escape($row['ordcustname']));
	$template->assign('CustomerEmail', $row['ordcustemail']);
	$template->assign('CustomerPhone', $row['ordcustphone']);

	// Format the billing address
	$template->assign('ShipFullName', isc_html_escape($row['ordbillfirstname'].' '.$row['ordbilllastname']));

	if($row['ordbillcompany']) {
		$template->assign('ShipCompany', '<br />'.isc_html_escape($row['ordbillcompany']));
	}
	else {
		$template->assign('ShipCompany', '');
	}

	$addressLine = isc_html_escape($row['ordbillstreet1']);
	if ($row['ordbillstreet2'] != "") {
		$addressLine .=  '<br />' . isc_html_escape($row['ordbillstreet2']);
	}
	$template->assign('ShipAddressLines', $addressLine);

	$template->assign('ShipSuburb', isc_html_escape($row['ordbillsuburb']));
	$template->assign('ShipState', isc_html_escape($row['ordbillstate']));
	$template->assign('ShipZip', isc_html_escape($row['ordbillzip']));
	$template->assign('ShipCountry', isc_html_escape($row['ordbillcountry']));
	$template->assign('BillingAddress', $template->getSnippet('AddressLabel'));
	$template->assign('BillingPhone', isc_html_escape($row['ordbillphone']));
	if(!$row['ordbillphone']) {
		$template->assign('HideBillingPhone', 'display: none');
	}
	$template->assign('BillingEmail', isc_html_escape($row['ordbillemail']));
	if(!$row['ordbillemail']) {
		$template->assign('HideBillingEmail', 'display: none');
	}

	// Is there a shipping address, or is it a digital download?
	if ($row['ordshipfirstname'] == "") {
		$template->assign('ShippingAddress', getLang('NA'));
		$template->assign('HideShippingPhone', 'display: none');
		$template->assign('HideShippingEmail', 'display: none');
	}
	else {
		// Format the shipping address
		$template->assign('ShipFullName', isc_html_escape($row['ordshipfirstname'].' '.$row['ordshiplastname']));

		if($row['ordshipcompany']) {
			$template->assign('ShipCompany', '<br />'.isc_html_escape($row['ordshipcompany']));
		}
		else {
			$template->assign('ShipCompany', '');
		}

		$addressLine = isc_html_escape($row['ordshipstreet1']);
		if ($row['ordshipstreet2'] != "") {
			$addressLine .=  '<br />' . isc_html_escape($row['ordshipstreet2']);
		}
		$template->assign('ShipAddressLines', $addressLine);

		$template->assign('ShipSuburb', isc_html_escape($row['ordshipsuburb']));
		$template->assign('ShipState', isc_html_escape($row['ordshipstate']));
		$template->assign('ShipZip', isc_html_escape($row['ordshipzip']));
		$template->assign('ShipCountry', isc_html_escape($row['ordshipcountry']));
		$template->assign('ShippingAddress', $template->getSnippet('AddressLabel'));
		$template->assign('ShippingPhone', isc_html_escape($row['ordshipphone']));
		if(!$row['ordshipphone']) {
			$template->assign('HideShippingPhone', 'display: none');
		}
		$template->assign('ShippingEmail', isc_html_escape($row['ordshipemail']));
		if(!$row['ordshipemail']) {
			$template->assign('HideShippingEmail', 'display: none');
		}
	}

	// Set the payment method
	$paymentMethods = array();
	$paymentMethod = $row['orderpaymentmethod'];
	if($row['orderpaymentmethod'] == '') {
		$paymentMethod = getLang('NA');
	}

	if($row['orderpaymentmethod'] != 'storecredit' && $row['orderpaymentmethod'] != 'giftcertificate') {
		if($row['ordgatewayamount']) {
			$paymentMethod .= " (". formatPriceInCurrency($row['ordgatewayamount'], $row['orddefaultcurrencyid']).")";
		}
		else {
			$paymentMethod .= " (". formatPriceInCurrency($row['ordtotalamount'], $row['orddefaultcurrencyid']).")";
		}

		$paymentMethods[] = $paymentMethod;
	}

	if($row['ordstorecreditamount'] > 0) {
		$paymentMethods[] = GetLang('StoreCredit') . " (".FormatPriceInCurrency($row['ordstorecreditamount'], $row['orddefaultcurrencyid']) . ")";
	}

	if($row['ordgiftcertificateamount'] > 0 && gzte11(ISC_LARGEPRINT)) {
		$paymentMethods[] = GetLang('GiftCertificates') . " (".FormatPriceInCurrency($row['ordgiftcertificateamount'], $row['orddefaultcurrencyid']) . ")";
	}

	$template->assign('PaymentMethod', implode('<br />', $paymentMethods));

	// Set the shipping method
	if ($row['ordshipmethod'] == "") {
		if ($row['ordisdigital']) {
			$template->assign('ShippingMethod', isc_html_escape(getLang('ImmediateDownload')));
		}
		else {
			$template->assign('ShippingMethod', isc_html_escape(getLang('FreeShipping')));
		}
	}
	else {
		$template->assign('ShippingMethod', isc_html_escape($row['ordshipmethod']));
	}

	// Get the products in the order
	$fieldsArray = array();
	$query = "
		SELECT o.*
		FROM [|PREFIX|]order_configurable_fields o
		JOIN [|PREFIX|]product_configurable_fields p ON o.fieldid = p.productfieldid
		WHERE o.orderid=".(int)$orderId."
		ORDER BY p.fieldsortorder ASC
	";
	$result = $db->Query($query);
	$fields = array();
	while ($row = $db->Fetch($result)) {
		$fieldsArray[$row['ordprodid']][] = $row;
	}

	$query = "
		SELECT *
		FROM [|PREFIX|]order_products
		WHERE orderorderid='".(int)$orderId."'
	";
	$result = $db->query($query);

	$productsTable = '';
	$wrappingTotal = 0;

	while($product = $db->fetch($result)) {
		$template->assign('ProductName', isc_html_escape($product['ordprodname']));
		if($product['ordprodsku']) {
			$template->assign('ProductSku', isc_html_escape($product['ordprodsku']));
		}
		else {
			$template->assign('ProductSku', getLang('NA'));
		}
		$template->assign('ProductQuantity', $product['ordprodqty']);

		$pOptions = '';
		if($product['ordprodoptions'] != '') {
			$options = @unserialize($product['ordprodoptions']);
			if(!empty($options)) {
				foreach($options as $name => $value) {
					$template->assign('FieldName', isc_html_escape($name));
					$template->assign('FieldValue', isc_html_escape($value));
					$pOptions .= $template->GetSnippet('PrintableInvoiceItemConfigurableField');
				}
			}
		}

		if($pOptions) {
			$template->assign('ProductOptions', $pOptions);
			$template->assign('HideVariationOptions', '');
		}
		else {
			$template->assign('HideVariationOptions', 'display: none');
		}

		$productFields = '';
		if(!empty($fieldsArray[$product['orderprodid']])) {
			$fields = $fieldsArray[$product['orderprodid']];
			foreach($fields as $field) {
				if(empty($field['textcontents']) && empty($field['filename'])) {
					continue;
				}

				$fieldValue = '-';
				$template->assign('FieldName', isc_html_escape($field['fieldname']));

				if($row['fieldtype'] == 'file') {
					$fieldValue = '<a href="'.GetConfig('ShopPath').'/'.GetConfig('ImageDirectory').'/configured_products/'.urlencode($field['originalfilename']).'">'.isc_html_escape($row['originalfilename']).'</a>';
				}
				else {
					$fieldValue = isc_html_escape($field['textcontents']);
				}

				$template->assign('FieldValue', $fieldValue);
				$productFields .= $template->getSnippet('PrintableInvoiceItemConfigurableField');
			}
		}
		$template->assign('ProductConfigurableFields', $productFields);
		if(!$productFields) {
			$template->assign('HideConfigurableFields', 'display: none');
		}
		else {
			$template->assign('HideConfigurableFields', '');
		}

		$template->assign('ProductCost', currencyConvertFormatPrice($product['ordprodcost'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));
		$template->assign('ProductTotalCost', currencyConvertFormatPrice($product['ordprodcost'] * $product['ordprodqty'], $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));

		if($product['ordprodwrapcost'] > 0) {
			$wrappingTotal += $product['ordprodwrapcost'] * $product['ordprodqty'];
		}

		if($product['ordprodwrapname']) {
			$template->assign('FieldName', getLang('GiftWrapping'));
			$template->assign('FieldValue', isc_html_escape($product['ordprodwrapname']));
			$template->assign('ProductGiftWrapping', $template->getSnippet('PrintableInvoiceItemConfigurableField'));
			$template->assign('HideGiftWrapping', '');
		}
		else {
			$template->assign('ProductGiftWrapping', '');
			$template->assign('HideGiftWrapping', 'display: none');
		}

		if($product['ordprodeventdate']) {
			$template->assign('FieldName', isc_html_escape($product['ordprodeventname']));
			$template->assign('FieldValue', isc_date('dS M Y', $product['ordprodeventdate']));
			$template->assign('ProductEventDate', $template->getSnippet('PrintableInvoiceItemConfigurableField'));
			$template->assign('HideEventDate', '');
		}
		else {
			$template->assign('ProductEventDate', '');
			$template->assign('HideEventDate', 'display: none');
		}

		$productsTable .= $template->GetSnippet('PrintableInvoiceItem');
	}
	$template->assign('ProductsTable', $productsTable);

	if($wrappingTotal > 0) {
		$template->assign('GiftWrappingTotal', currencyConvertFormatPrice($wrappingTotal, $row['ordcurrencyid'], $row['ordcurrencyexchangerate'], true));
	}
	else {
		$template->assign('HideGiftwrappingTotal', 'display: none');
	}

	$template->setTemplate("invoice_print");
	return $template->parseTemplate(true);
}

/**
 * Generate a packing slip for either an entire order or one or a specific shipment in an order.
 *
 * @param int The order ID to print the packing slip for.
 * @param int Optionally, if we're printing a specific shipment, the ID of the shipment to print a packing slip for.
 * @return string The generated packing slip (HTML)
 */
function generatePrintablePackingSlip($orderId, $shipmentId=0)
{
	$db = $GLOBALS['ISC_CLASS_DB'];

	$template = new TEMPLATE('ISC_LANG');
	$template->frontEnd();
	$template->setTemplateBase(ISC_BASE_PATH . "/templates");
	$template->panelPHPDir = ISC_BASE_PATH . "/includes/display/";
	$template->templateExt = "html";
	$template->setTemplate(getConfig("template"));

	$products = array();

	if($shipmentId != 0) {
		$query = "
			SELECT *
			FROM [|PREFIX|]shipments
			WHERE shipmentid='".(int)$shipmentId."'
		";
		$result = $db->query($query);
		$shipmentDetails = $db->fetch($result);
		if(!isset($shipmentDetails['shipmentid'])) {
			return false;
		}

		// Load the items
		$query = "
			SELECT *
			FROM [|PREFIX|]shipment_items
			WHERE shipid='".(int)$shipmentId."'
		";
		$result = $db->Query($query);
		while($product = $db->Fetch($result)) {
			// Standadize the product details
			$products[] = array(
				'prodcode' => $product['itemprodsku'],
				'prodname' => $product['itemprodname'],
				'prodqty' => $product['itemqty'],
				'prodoptions' => $product['itemprodoptions'],
				'prodvariationid' => $product['itemprodvariationid'],
				'prodordprodid' => $product['itemordprodid'],
				'prodeventdatename' => $product['itemprodeventname'],
				'prodeventdate' => $product['itemprodeventdate'],
			);
		}

		$template->assign('PackingSlipTitle', sprintf(GetLang('PackingSlipTitleShipment'), $shipmentDetails['shipmentid']));
		$orderId = $shipmentDetails['shiporderid'];
		$orderDate = $shipmentDetails['shiporderdate'];
		$shippingMethod = $shipmentDetails['shipmethod'];
		$trackingNo = $shipmentDetails['shiptrackno'];
		$comments = $shipmentDetails['shipcomments'];
		$dateShipped = $shipmentDetails['shipdate'];
		$addressColumnPrefix = 'ship';
	}
	// Printing a packing slip for an entire order
	else {
		$shipmentDetails = GetOrder($orderId, true);
		foreach($shipmentDetails['products'] as $product) {
			// Skip anything that's not a physical product
			if($product['ordprodtype'] != 'physical') {
				continue;
			}
			// Standadize the product details
			$products[] = array(
				'prodcode' => $product['ordprodsku'],
				'prodname' => $product['ordprodname'],
				'prodqty' => $product['ordprodqty'],
				'prodoptions' => $product['ordprodoptions'],
				'prodvariationid' => $product['ordprodvariationid'],
				'prodordprodid' => $product['orderprodid'],
				'prodeventdatename' => $product['ordprodeventname'],
				'prodeventdate' => $product['ordprodeventdate'],
			);
		}

		$template->assign('PackingSlipTitle', sprintf(GetLang('PackingSlipTitleOrder'), $shipmentDetails['orderid']));
		$orderId = $shipmentDetails['orderid'];
		$orderDate = $shipmentDetails['orddate'];
		$shippingMethod = $shipmentDetails['ordshipmethod'];
		$trackingNo = $shipmentDetails['ordtrackingno'];
		$comments = $shipmentDetails['ordcustmessage'];
		$addressColumnPrefix = 'ord';
	}

	$template->assign('OrderId', $orderId);
	$template->assign('OrderDate', cDate($orderDate));

	if($shippingMethod) {
		$template->assign('ShippingMethod', isc_html_escape($shippingMethod));
	}
	else {
		$template->assign('HideShippingMethod', 'display: none');
	}

	if($trackingNo) {
		$template->assign('TrackingNo', isc_html_escape($trackingNo));
	}
	else {
		$template->assign('HideTrackingNo', 'display: none');
	}

	if($comments) {
		$template->assign('Comments', nl2br(isc_html_escape($comments)));
	}
	else {
		$template->assign('HideComments', 'display: none');
	}

	if(isset($dateShipped)) {
		$template->assign('DateShipped', cDate($dateShipped));
	}
	else {
		$template->assign('HideShippingDate', 'display: none');
	}

	if(empty($products)) {
		return false;
	}

	$query = "
		SELECT customerid, CONCAT(custconfirstname, ' ', custconlastname) AS ordcustname, custconemail AS ordcustemail, custconphone AS ordcustphone
		FROM [|PREFIX|]customers
		WHERE customerid = '".$db->Quote($shipmentDetails[$addressColumnPrefix.'custid'])."'
	";
	$query .= $db->AddLimit(0, 1);
	$result = $db->Query($query);

	$template->assign('CustomerName', '');
	$template->assign('CustomerEmail', '');
	$template->assign('CustomerPhone', '');

	if($row = $db->Fetch($result)) {
		// Format the customer details
		$template->assign('CustomerName', isc_html_escape($row['ordcustname']));
		$template->assign('CustomerEmail', isc_html_escape($row['ordcustemail']));
		$template->assign('CuastomerPhone', isc_html_escape($row['ordcustphone']));
		$template->assign('CustomerId', $row['customerid']);
	}
	else {
		$template->assign('HideCustomerDetails', 'display: none');
	}

	if(isset($shipmentDetails['ordvendorid']) && $shipmentDetails['ordvendorid']>0) {
		$vendorq = "
			SELECT *
			FROM [|PREFIX|]vendors
			WHERE vendorid = '".$db->quote($shipmentDetails['ordvendorid'])."'
		";
		$vendorResult = $db->query($vendorq);
		$vendorDetails = $db->fetch($vendorResult);
		$template->assign('StoreAddressFormatted', isc_html_escape($vendorDetails['vendoraddress']).'<br />'.isc_html_escape($vendorDetails['vendorcity']. ', '. $vendorDetails['vendorcity']. ', '.$vendorDetails['vendorzip'])."<br />".isc_html_escape($vendorDetails['vendorcountry']));

	}
	else {
		$template->assign('StoreAddressFormatted', nl2br(GetConfig('StoreAddress')));
	}

	$addressDetails = array(
		'shipfirstname'	=> $shipmentDetails[$addressColumnPrefix.'billfirstname'],
		'shiplastname'	=> $shipmentDetails[$addressColumnPrefix.'billlastname'],
		'shipcompany'	=> $shipmentDetails[$addressColumnPrefix.'billcompany'],
		'shipaddress1'	=> $shipmentDetails[$addressColumnPrefix.'billstreet1'],
		'shipaddress2'	=> $shipmentDetails[$addressColumnPrefix.'billstreet2'],
		'shipcity'		=> $shipmentDetails[$addressColumnPrefix.'billsuburb'],
		'shipstate'		=> $shipmentDetails[$addressColumnPrefix.'billstate'],
		'shipzip'		=> $shipmentDetails[$addressColumnPrefix.'billzip'],
		'shipcountry'	=> $shipmentDetails[$addressColumnPrefix.'billcountry'],
		'countrycode'	=> $shipmentDetails[$addressColumnPrefix.'billcountrycode'],
	);
	$template->assign('BillingAddress', ISC_ADMIN_ORDERS::buildOrderAddressDetails($addressDetails, false));
	$template->assign('BillingPhone', isc_html_escape($shipmentDetails[$addressColumnPrefix.'billphone']));
	if(!$shipmentDetails[$addressColumnPrefix.'billphone']) {
		$template->assign('HideBillingPhone', 'display: none');
	}
	$template->assign('BillingEmail', isc_html_escape($shipmentDetails[$addressColumnPrefix.'billemail']));
	if(!$shipmentDetails[$addressColumnPrefix.'billemail']) {
		$template->assign('HideBillingEmail', 'display: none');
	}

	$addressDetails = array(
		'shipfirstname'	=> $shipmentDetails[$addressColumnPrefix.'shipfirstname'],
		'shiplastname'	=> $shipmentDetails[$addressColumnPrefix.'shiplastname'],
		'shipcompany'	=> $shipmentDetails[$addressColumnPrefix.'shipcompany'],
		'shipaddress1'	=> $shipmentDetails[$addressColumnPrefix.'shipstreet1'],
		'shipaddress2'	=> $shipmentDetails[$addressColumnPrefix.'shipstreet2'],
		'shipcity'		=> $shipmentDetails[$addressColumnPrefix.'shipsuburb'],
		'shipstate'		=> $shipmentDetails[$addressColumnPrefix.'shipstate'],
		'shipzip'		=> $shipmentDetails[$addressColumnPrefix.'shipzip'],
		'shipcountry'	=> $shipmentDetails[$addressColumnPrefix.'shipcountry'],
		'countrycode'	=> $shipmentDetails[$addressColumnPrefix.'shipcountrycode'],
	);
	$template->assign('ShippingAddress', ISC_ADMIN_ORDERS::buildOrderAddressDetails($addressDetails, false));
	$template->assign('ShippingPhone', isc_html_escape($shipmentDetails[$addressColumnPrefix.'shipphone']));
	if(!$shipmentDetails[$addressColumnPrefix.'shipphone']) {
		$template->assign('HideShippingPhone', 'display: none');
	}
	$template->assign('ShippingEmail', isc_html_escape($shipmentDetails[$addressColumnPrefix.'shipemail']));
	if(!$shipmentDetails[$addressColumnPrefix.'shipemail']) {
		$template->assign('HideShippingEmail', 'display: none');
	}

	$fieldsArray = array();
	$query = "
		SELECT o.*
		FROM [|PREFIX|]order_configurable_fields o
		JOIN [|PREFIX|]product_configurable_fields p ON o.fieldid = p.productfieldid
		WHERE o.orderid=".(int)$orderId."
		ORDER BY p.fieldsortorder ASC
	";
	$result = $db->Query($query);
	$fields = array();
	while ($row = $db->Fetch($result)) {
		$fieldsArray[$row['ordprodid']][] = $row;
	}

	// Build the list of products that are being shipped
	$productsTable = '';
	foreach($products as $product) {
		$template->assign('ProductName', isc_html_escape($product['prodname']));
		if($product['prodcode']) {
			$template->assign('ProductSku', isc_html_escape($product['prodcode']));
		}
		else {
			$template->assign('ProductSku', getLang('NA'));
		}
		$template->assign('ProductQuantity', $product['prodqty']);

		$pOptions = '';
		if($product['prodoptions'] != '') {
			$options = @unserialize($product['prodoptions']);
			if(!empty($options)) {
				foreach($options as $name => $value) {
					$template->assign('FieldName', isc_html_escape($name));
					$template->assign('FieldValue', isc_html_escape($value));
					$pOptions .= $template->GetSnippet('PrintableInvoiceItemConfigurableField');
				}
			}
		}

		if($pOptions) {
			$template->assign('ProductOptions', $pOptions);
			$template->assign('HideVariationOptions', '');
		}
		else {
			$template->assign('HideVariationOptions', 'display: none');
		}

		$productFields = '';
		if(!empty($fieldsArray[$product['prodordprodid']])) {
			$fields = $fieldsArray[$product['prodordprodid']];
			foreach($fields as $field) {
				if(empty($field['textcontents']) && empty($field['filename'])) {
					continue;
				}

				$fieldValue = '-';
				$template->assign('FieldName', isc_html_escape($field['fieldname']));

				if($row['fieldtype'] == 'file') {
					$fieldValue = '<a href="'.GetConfig('ShopPath').'/'.GetConfig('ImageDirectory').'/configured_products/'.urlencode($field['originalfilename']).'">'.isc_html_escape($row['originalfilename']).'</a>';
				}
				else {
					$fieldValue = isc_html_escape($field['textcontents']);
				}

				$template->assign('FieldValue', $fieldValue);
				$productFields .= $template->getSnippet('PrintableInvoiceItemConfigurableField');
			}
		}
		$template->assign('ProductConfigurableFields', $productFields);
		if(!$productFields) {
			$template->assign('HideConfigurableFields', 'display: none');
		}
		else {
			$template->assign('HideConfigurableFields', '');
		}

		if($product['prodeventdatename']) {
			$template->assign('FieldName', isc_html_escape($product['prodeventdatename']));
			$template->assign('FieldValue', isc_date('dS M Y', $product['prodeventdate']));
			$template->assign('ProductEventDate', $template->getSnippet('PrintableInvoiceItemConfigurableField'));
			$template->assign('HideEventDate', '');
		}
		else {
			$template->assign('ProductEventDate', '');
			$template->assign('HideEventDate', 'display: none');
		}

		$productsTable .= $template->GetSnippet('PrintablePackingSlipItem');
	}
	$template->assign('ProductsTable', $productsTable);
	$template->setTemplate('packing_slip_print');
	return $template->parseTemplate(true);
}