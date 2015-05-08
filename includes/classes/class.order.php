<?php

	class ISC_ORDER
	{
		private $pendingData = array();

		private $orderToken = '';
		private $paymentProvider = null;

		public function HandlePage()
		{
			// Set up the incoming order details
			$this->SetOrderData();

			$action = "";
			if(isset($_REQUEST['action'])) {
				$action = isc_strtolower($_REQUEST['action']);
			}

			switch($action) {
				default: {
					$this->FinishOrder();
				}
			}
		}

		private function SetOrderData()
		{
			// Some payment providers like WorldPay simply "fetch" FinishOrder.php and so it
			// doesn't factor in cookies stored by Shopping Cart, so we have to pass back the
			// order token manually from those payment providers. We do this by taking the
			// cart ID passed back from the provider which stores the order's unique token.
			if(isset($_COOKIE['SHOP_ORDER_TOKEN'])) {
				$this->orderToken = $_COOKIE['SHOP_ORDER_TOKEN'];
			}
				
			
			else if(isset($_REQUEST['provider'])) {
				GetModuleById('checkout', $this->paymentProvider, $_REQUEST['provider']);

				if(in_array("GetOrderToken", get_class_methods($this->paymentProvider))) {
					$this->orderToken = $this->paymentProvider->GetOrderToken();
				}
				else {
					ob_end_clean();
					header(sprintf("Location:%s", $GLOBALS['ShopPath']));
					die();
				}
			}

			// Load the pending orders from the database
			$this->pendingData = LoadPendingOrdersByToken($this->orderToken, true);

			if(!$this->orderToken || $this->pendingData === false) {
				$this->BadOrder();
				exit;
			}

			if($this->paymentProvider === null) {
				GetModuleById('checkout', $this->paymentProvider, $this->pendingData['paymentmodule']);
			}

			if($this->paymentProvider) {
				$this->paymentProvider->SetOrderData($this->pendingData);
			}
		}

		/**
		*	Show the "Thanks for Your Order" page and email an invoice to the customer.
		*	Also clear the outstanding order cookies and related data
		*/
		private function ThanksForYourOrder()
		{
			// Reload all fo the information about the order as there's a good chance
			// a fair bit of it has changed now
			$this->SetOrderData();

			$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');
			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');

			$GLOBALS['HideError'] = "none";
			$GLOBALS['HidePaidOrderConfirmation'] = '';
			$GLOBALS['HideAwaitingPayment'] = "none";

			$GLOBALS['HideStoreCreditUse'] = 'none';

			if($this->pendingData['storecreditamount'] > 0) {
				$GLOBALS['HideStoreCreditUse'] = '';
				$GLOBALS['StoreCreditUsed'] = CurrencyConvertFormatPrice($this->pendingData['storecreditamount']);

				$GLOBALS['StoreCreditBalance'] = CurrencyConvertFormatPrice($GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerStoreCredit($this->pendingData['customerid']));
				$GLOBALS['ISC_LANG']['OrderCreditDeducted'] = sprintf(GetLang('OrderCreditDeducted'), GetConfig('CurrencyToken') . $GLOBALS['StoreCreditUsed']);
			}

			// If it was an offline payment method, show the post-purchase message and hide other messages
			if(is_object($this->paymentProvider) && $this->paymentProvider->GetPaymentType() == PAYMENT_PROVIDER_OFFLINE && method_exists($this->paymentProvider, 'GetOfflinePaymentMessage')) {
				$defaultCurrency = GetDefaultCurrency();
				$GLOBALS['OrderTotal'] = FormatPrice($this->pendingData['gatewayamount'], false, true, false, $defaultCurrency, true);

				$GLOBALS['HidePaidOrderConfirmation'] = "none";
				
				
				
				
				
				
				
				foreach($this->pendingData['orders'] as $order) {
				$GLOBALS['PaymentMessage'] = $this->paymentProvider->GetOfflinePaymentMessage( $order['orderid']); 
				}
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				$GLOBALS['SNIPPETS']['OfflinePaymentMessage'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("OfflinePaymentMessage");
			}
			else {
				// Was the order declined?
				if($this->pendingData['status'] == 6) {
					$GLOBALS['HideError'] = '';
					$GLOBALS['ErrorMessage'] = sprintf(GetLang('ErroOrderDeclined'), GetConfig('OrderEmail'), GetConfig('OrderEmail'));
					$GLOBALS['HidePaidOrderConfirmation'] = 'none';
					$GLOBALS['ISC_LANG']['ThanksForYourOrder'] = GetLang('YourPaymentWasDeclined');
				}
				// Order is still awaiting payment
				else if($this->pendingData['status'] == 7) {
					$GLOBALS['HidePaidOrderConfirmation'] = "none";
					$GLOBALS['HideAwaitingPayment'] = "";
				}
				// Otherwise, order was successful
				else {
					// Is it a physical or digital order?
					if($this->pendingData['isdigital'] == 1) {

						// If this order has no customer ID associated with it (guest checkout with no account creation) then display an alternative text with no download link
						if (!isId($this->pendingData['customerid'])) {
							$GLOBALS['DigitalOrderConfirmation'] = GetLang('DigitalOrderConfirmationGuestCheckout');
							$GLOBALS['HideDigitalOrderDownloadLink'] = 'none';
						}
						// Otherwise display nthe normal text with the download link in it
						else {
							$GLOBALS['DigitalOrderConfirmation'] = GetLang('DigitalOrderConfirmation');
							$GLOBALS['HideDigitalOrderDownloadLink'] = '';
						}

						$GLOBALS['HidePhysicalOrderConfirmation'] = "none";
						$GLOBALS['HidePhysicalViewOrderLink'] = "none";
					}
					else {

						// If this order has no customer ID associated with it (guest checkout with no account creation) then display an alternative text with no view order link
						if (!isId($this->pendingData['customerid'])) {
							$GLOBALS['PhysicalOrderConfirmation'] = GetLang('PhysicalOrderConfirmationGuestCheckout');
							$GLOBALS['HidePhysicalViewOrderLink'] = 'none';
						}
						// Otherwise display nthe normal text with the download link in it
						else {
							$GLOBALS['PhysicalOrderConfirmation'] = GetLang('PhysicalOrderConfirmation');
							$GLOBALS['HidePhysicalViewOrderLink'] = '';
						}

						$GLOBALS['HideDigitalOrderConfirmation'] = "none";
						$GLOBALS['HideDigitalOrderDownloadLink'] = "none";
					}
				}
			}

			// Include the conversion code for each analytics module
			$GLOBALS['ConversionCode'] = '';
			$analyticsModules = GetAvailableModules('analytics', true, true);
			foreach($analyticsModules as $module) {
				$module['object']->SetOrderData($this->pendingData);
				$trackingCode = $module['object']->GetConversionCode();
				if($trackingCode != '') {
					$GLOBALS['ConversionCode'] .= "
						<!-- Start conversion code for ".$module['id']." -->
						".$trackingCode."
						<!-- End conversion code for ".$module['id']." -->
					";
				}
			}

			// Include the conversion tracking code for affiliates
			foreach($this->pendingData['orders'] as $order) {
				if(strlen(GetConfig('AffiliateConversionTrackingCode')) > 0) {
					$converted_code = GetConfig('AffiliateConversionTrackingCode');
					$converted_code = str_ireplace('%%ORDER_AMOUNT%%', $order['ordsubtotal'], $converted_code);
					$converted_code = str_ireplace('%%ORDER_ID%%', $order['orderid'], $converted_code);
					$GLOBALS['ConversionCode'] .= '\n\n' . $converted_code;
				}
			}

			// Are product update options enabled?
			if(GetConfig('MailXMLAPIValid') && GetConfig('UseMailAPIForUpdates')) {
				$order = reset($this->pendingData['orders']);
				$email = $order['ordbillemail'];
				$GLOBALS['CustomerEmail'] = $email;
				$GLOBALS['ProductUpdatesList'] = "";

				$query = "
					SELECT DISTINCT ordprodid, ordprodname
					FROM [|PREFIX|]order_products
					WHERE orderorderid IN (".implode(array_keys($this->pendingData['orders'])).")
					ORDER BY ordprodname
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

				while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$GLOBALS['ProductName'] = isc_html_escape($row['ordprodname']);
					$GLOBALS['ProductId'] = $row['ordprodid'];
					$GLOBALS['ProductUpdatesList'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("ProductUpdatesRadio");
				}
			}
			else {
				// Hide the product updates div
				$GLOBALS['HideProductUpdates'] = "none";
			}

			if(method_exists($this->paymentProvider, 'ShowOrderConfirmation')) {
				$GLOBALS['OrderConfirmationDetails'] = $this->paymentProvider->ShowOrderConfirmation($this->pendingData);
			}

			// Show the order confirmation screen
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetLang('ThanksForYourOrder'));
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("order");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		*	Complete the order after the customer is brought back from the payment provider
		*/
		public function FinishOrder()
		{
			// Orders are still incomplete, so we need to validate them
			if($this->pendingData['status'] == ORDER_STATUS_INCOMPLETE) {
				// Verify the pending order
				$newStatus = VerifyPendingOrder($this->orderToken);

				// Order was declined and we're rejecting all declined payments
				if($newStatus == ORDER_STATUS_DECLINED) {
					$Msg = sprintf(GetLang('ErroOrderDeclined'), GetConfig('OrderEmail'), GetConfig('OrderEmail'));
					$this->BadOrder(GetLang('YourPaymentWasDeclined'), $Msg);
				}
				// This order is valid
				elseif($newStatus !== false) {

					$prodOrdered = array();
					if(isset($_SESSION['CART']['ITEMS']) && !empty($_SESSION['CART']['ITEMS'])) {
						foreach($_SESSION['CART']['ITEMS'] as $item) {
							if(isset($item['product_id'])) {
								$prodOrdered[] = $item['product_id'];
							}
						}
						$_SESSION['ProductJustOrdered'] = implode(',',$prodOrdered);
					}
					if(CompletePendingOrder($this->orderToken, $newStatus)) {
						// Order was saved. Show the confirmation screen and email an invoice to the customer
						$this->ThanksForYourOrder();
						return;
					}
				}

				// If we're still here, either the order didnt complete or the order was invalid
				$this->BadOrder();
			}
			// Order is already complete - there's a good chance the customer has refreshed the page,
			// or they've come back from somewhere like PayPal who in the mean time has already sent
			// us a ping back to validate and begin processing the order - show the thank you page
			else if($this->pendingData['status'] == ORDER_STATUS_DECLINED) {
					$Msg = sprintf(GetLang('ErroOrderDeclined'), GetConfig('OrderEmail'), GetConfig('OrderEmail'));
					$this->BadOrder(GetLang('YourPaymentWasDeclined'), $Msg);
			}
			else {
				$this->ThanksForYourOrder();
				return;
			}
		}

		/**
		*	Something went wrong when trying to validate the order, show an error with the stores order email address for help
		*/
		public function BadOrder($Title="", $Message="", $Detailed="")
		{
			$GLOBALS['ErrorTitle'] = GetLang('OrderError');
			if($Title) {
				$GLOBALS['ISC_LANG']['SomethingWentWrong'] = $Title;
			}

			if($Message == "") {
				$GLOBALS['ErrorMessage'] = sprintf(GetLang('BadOrderDetailsFromProvider'), GetConfig('OrderEmail'), GetConfig('OrderEmail'));
			}
			else {
				$GLOBALS['ErrorMessage'] = $Message;
			}

			if($Detailed != "") {
				$GLOBALS['ErrorDetails'] = $Detailed;
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle($GLOBALS['ErrorTitle']);
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("error");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
			exit;
		}
	}