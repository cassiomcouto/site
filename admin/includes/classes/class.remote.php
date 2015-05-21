<?php

	if (!defined('ISC_BASE_PATH')) {
		die();
	}

	require_once(ISC_BASE_PATH.'/lib/class.xml.php');

	class ISC_ADMIN_REMOTE extends ISC_XML_PARSER
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function HandleToDo()
		{
			/**
			 * Convert the input character set from the hard coded UTF-8 to their
			 * selected character set
			 */
			convertRequestInput();

			$what = isc_strtolower(@$_REQUEST['w']);

			switch  ($what) {
				case 'productimages':
					$adminProductImage = new ISC_ADMIN_PRODUCT_IMAGE();
					$adminProductImage->routeRemoteRequest($this);
					break;

				case 'getpageparentoptions':
					$this->GetPageParentOptions();
					break;
				case "getshippingmoduleproperties":
					$this->GetShippingModuleProperties();
					break;
				case "multicountrystates":
					$this->GetMultiCountryStates();
					break;
				case "saveversion":
					$this->SaveVersion();
					break;
				case "testsmtpsettings":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Settings)) {
						$this->TestSMTPSettings();
					}
				break;
				case "updatecustomergroup":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Customers)) {
						$this->UpdateCustomerGroup();
					}
					break;
				case "clearcreditcarddetails":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$this->ClearCreditCardDetails();
					}
					break;
				case "getvariationcombinations": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product)) {
						$this->GetVariationCombinationsTable();
					}
					break;
				}
				case "customfieldsformailinglist": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Newsletter_Subscribers)) {
						$this->GetCustomFieldsForMailingList();
					}
					break;
				}
				case "textcustomfieldsformailinglist": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Newsletter_Subscribers)) {
						$this->GetTextCustomFieldsForMailingList();
					}
					break;
				}
				case "relatedproducts": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->GetRelatedProducts();
					}
					break;
				}
				case "inventorylevels": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->GetInventoryLevels();
					}
					break;
				}
				case "orderquickview": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$this->GetOrderQuickView();
					}
					break;
				}
				case "countrystates": {
					$this->GetCountryStates();
					break;
				}
				case "addorderprodsearch": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$this->GetMatchingProducts();
					}
					break;
				}
				case "customerorders": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$this->GetCustomerOrders();
					}
					break;
				}
				case "updateorderstatus": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Orders)) {
						$this->UpdateOrderStatus();
					}
					break;
				}
				case "updatetrackingno": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Orders)) {
						$this->UpdateTrackingNo();
					}
					break;
				}
				case "updateperproductinventorylevels": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->UpdatePerProductInventoryLevels();
					}
					break;
				}
				case "updateperoptioninventorylevels": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->UpdatePerOptionInventoryLevels();
					}
					break;
				}
				case "testftpsettings": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Backups)) {
						$this->TestFTPSettings();
					}
					break;
				}
				case "downloadtemplatefile": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->DownloadTemplateFile();
					}
					break;
				}
				case "checktemplatekey": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->CheckTemplateKey();
					}
					break;
				}
				case "checktemplateversion": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->CheckTemplateVersion();
					}
					break;
				}
				case "saveproductdownload": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->SaveProductDownload();
					}
					break;
				}
				case "deleteproductdownload": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->DeleteProductDownload();
					}
					break;
				}
				case "editproductdownload": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->EditProductDownload();
					}
					break;
				}
				case "updatepageorders": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Pages)) {
						$this->UpdatePageOrders();
					}
					break;
				}
				case "updatecategoryorders": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Categories)) {
						$this->UpdateCategoryOrders();
					}
					break;
				}
				case "savequickcategory": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Categories)) {
						$this->SaveNewQuickCategory();
					}
					break;
				}
				case "approvereviews": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Reviews)) {
						$this->ApproveReviews();
					}
					break;
				}
				case "disapprovereviews": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Reviews)) {
						$this->DisapproveReviews();
					}
					break;
				}
				case "deletereviews": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Reviews)) {
						$this->DeleteReviews();
					}
					break;
				}
				case "popupproductsearch": {
					$this->PopupProductSearch();
					break;
				}
				case "loginfoquickview": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->LogInfoQuickView();
					}
					break;
				}
				case "generateapikey": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Users)
					|| $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_User)) {
						$this->GenerateNewAPIKey();
					}
					break;
				}
				case "returnquickview": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Returns)) {
						$this->ReturnQuickView();
					}
					break;
				}
				case "updatereturnnotes": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Returns)) {
						$this->UpdateReturnNotes();
					}
					break;
				}
				case "updatereturnstatus": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Returns)) {
						$this->UpdateReturnStatus();
					}
					break;
				}
				case "updatestorecredit": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Returns)) {
						$this->UpdateStoreCredit();
					}
					break;
				}
				case "giftcertificatequickview": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_GiftCertificates)) {
						$this->GiftCertificateQuickView();
					}
					break;
				}
				case "updategiftcertificatestatus": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_GiftCertificates)) {
						$this->UpdateGiftCertificateStatus();
					}
					break;
				}
				case "validateaddonkey": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Addons)) {
						$this->CheckAddonKey();
					}
					break;
				}
				case "downloadaddonzip": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Addons)) {
						$this->DownloadAddonZip();
					}
					break;
				}
				case "getemailtemplate": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->GetEmailTemplate();
					}
					break;
				}
				case 'getemailtemplatedirectory':
					$this->GetEmailTemplateDirectory();
					break;
				case "updateemailtemplate": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->UpdateEmailTemplate();
					}
					break;
				}
				case "useproductserverfile": {
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)
					|| $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_Products)) {
						$this->UseProductServerFile();
					}
					break;
				}
				case "getheaderimage":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->getHeaderImage();
						break;
					}
				case "getblankheaderimage":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->downloadHeaderImage('blank');
						break;
					}
				case "getorigheaderimage":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->downloadHeaderImage('original');
						break;
					}
				case "getcurrentheaderimage":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->downloadHeaderImage('current');
						break;
					}

				case "uploadheaderimage":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->uploadHeaderImage();
						break;
					}
				case "deleteheaderimage":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->deleteHeaderImage();
						break;
					}
				case "updatelogo":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->UpdateLogo();
						break;
					}
				case "previewlogo":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->PreviewLogo();
						break;
					}
				case 'updatelogonone':
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->UpdateLogoNone();
						break;
					}
				case "checknewlogos":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->CheckNewLogos();
						break;
					}
				case "downloadlogofile":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
						$this->DownloadLogoFile();
						break;
					}
					break;
				case "getexchangerate":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Settings)) {
						$this->getExchangeRate();
					}
					break;
				case "updateexchangerate":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Settings)) {
						$this->UpdateExchangeRate();
					}
					break;
				case "updatetemplatefields":
					$this->UpdateTemplateFields();
					break;
				case "getstates":
					$this->GetStateList();
					break;
				case "bulkupdatevariations":
					$this->BulkUpdateVariations();
					break;
			}
		}

		private function GetPageParentOptions()
		{
			if(!isset($_REQUEST['pageId'])) {
				exit;
			}

			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorId = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
			}
			else if(isset($_REQUEST['vendorId'])) {
				$vendorId = (int)$_REQUEST['vendorId'];
			}
			else {
				$vendorId = 0;
			}

			$pages = GetClass('ISC_ADMIN_PAGES');
			echo $pages->GetParentPageOptions(0, $_REQUEST['pageId'], $vendorId);
			exit;
		}

		private function GetShippingModuleProperties()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('settings.shipping');
			GetModuleById('shipping', $shippingModule, $_REQUEST['module']);
			if(!is_object($shippingModule)) {
				exit;
			}
			$shippingModule->SetMethodId(0);
			echo $shippingModule->GetPropertiesSheet(0);
			echo "<span style=\"display: none;\" id=\"moduleName\">".$shippingModule->GetName()."</span>";
			echo "<script type=\"text/javascript\"> function ShipperValidation() { ".$GLOBALS['ShippingJavaScript']." return true; }</script>";
			exit;
		}

		/**
		 * Save the version number of the latest ISC release to the data cache.
		 */
		private function SaveVersion()
		{
			if(!isset($_REQUEST['v'])) {
				exit;
			}

			$updatedVersion = array(
				'latest' => $_REQUEST['v'],
				'lastCheck' => time()
			);
			$GLOBALS['ISC_CLASS_DATA_STORE']->Save('LatestVersion', $updatedVersion);
			echo '1';
			exit;
		}

		/**
		 * Test the SMTP settings from the settings page.
		 *
		 */
		private function TestSMTPSettings()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('settings');
			$subject = sprintf(GetLang('TestSendingSubject'), GetConfig('StoreName'));
			$text = sprintf(GetLang('TestSendingEmail'), GetConfig('StoreName'));
			if(!isset($_POST['AdminEmail'])) {
				$tags[] = $this->MakeXMLTag('status',0);
				$tags[] = $this->MakeXMLTag('message', GetLang('EnterAdminEmail'));
				$this->SendXMLHeader();
				$this->SendXMLResponse($tags);
				die();
			}
			else {
				$preview_email = $_POST['AdminEmail'];
			}

			require_once(ISC_BASE_PATH . "/lib/email.php");
			$email_api = GetEmailClass();

			$email_api->Set('SMTPServer', $_POST['MailSMTPServer']);
			if(isset($_POST['MailSMTPUsername']) && !empty($_POST['MailSMTPUsername'])) {
				$email_api->Set('SMTPUsername', $_POST['MailSMTPUsername']);
			}

			if(isset($_POST['MailSMTPPassword']) && !empty($_POST['MailSMTPPassword'])) {
				$email_api->Set('SMTPPassword', $_POST['MailSMTPPassword']);
			}

			if(isset($_POST['MailSMTPPort']) && !empty($_POST['MailSMTPPort'])) {
				$email_api->Set('SMTPPort', $_POST['MailSMTPPort']);
			}

			$email_api->Set('Subject', $subject);
			$email_api->Set('FromAddress', $preview_email);
			$email_api->Set('ReplyTo', $preview_email);
			$email_api->Set('BounceAddress', $preview_email);

			$email_api->AddBody('text', $text);

			$email_api->AddRecipient($preview_email, '', 't');
			$send_result = $email_api->Send();

			if (isset($send_result['success']) && $send_result['success'] > 0) {
				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', sprintf(GetLang('TestEmailSent'), $_POST['AdminEmail']));
			}
			else {
				$failure = array_shift($send_result['fail']);
				$msg = sprintf(GetLang('TestEmailNotSent'), $preview_email, $failure[1]);
				$tags[] = $this->MakeXMLTag('status',1);
				$tags[] = $this->MakeXMLTag('message', $msg);
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		* UpdateCustomerGroup
		* Update the custgroupid field which is the group that the customer belongs to
		*
		* @return Int 1 on success, 0 on failure
		*/
		private function UpdateCustomerGroup()
		{
			if (isset($_REQUEST['customerId']) && isset($_REQUEST['groupId'])) {
				$entity = new ISC_ENTITY_CUSTOMER();

				if ($entity->editGroup($_REQUEST['customerId'], $_REQUEST['groupId'])) {
					print 1;
				} else {
					print 0;
				}
			}
		}

		/**
		 * Remove the credit card details from a particular order.
		 *
		 * @return void
		 */
		private function ClearCreditCardDetails()
		{
			$query = "SELECT orderid, extrainfo FROM [|PREFIX|]orders WHERE orderid='".$GLOBALS['ISC_CLASS_DB']->Quote((int)$_REQUEST['orderId'])."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$order = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
			if(!isset($order['orderid']) || !$order['orderid']) {
				exit;
			}

			$extraInfo = @unserialize($order['extrainfo']);
			if(is_array($extraInfo)) {
				unset($extraInfo['cc_ccno']);
				unset($extraInfo['cc_cvv2']);
				unset($extraInfo['cc_name']);
				unset($extraInfo['cc_ccaddress']);
				unset($extraInfo['cc_cczip']);
				unset($extraInfo['cc_cctype']);
				unset($extraInfo['cc_ccexpm']);
				unset($extraInfo['cc_ccexpy']);

				if(isset($extraInfo['cc_issueno'])) {
					unset($extraInfo['cc_issueno']);
				}

				if(isset($extraInfo['cc_issuedatey'])) {
					unset($extraInfo['cc_issuedatey']);
					unset($extraInfo['cc_issuedatem']);
					unset($extraInfo['cc_issuedated']);
				}

				$updatedOrder = array(
					"extrainfo" => serialize($extraInfo)
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("orders", $updatedOrder, "orderid='".$order['orderid']."'");
				echo 1;
			}

			exit;
		}

		/**
		 * Update the status of a gift certificate from the manage page
		 *
		 * @return void
		 **/
		private function UpdateGiftCertificateStatus()
		{
			if(!isset($_REQUEST['giftCertificateId'])) {
				exit;
			}
			$query = sprintf("SELECT * FROM [|PREFIX|]gift_certificates WHERE giftcertid='%d'", $_REQUEST['giftCertificateId']);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$certificate = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
			if(!$certificate['giftcertid']) {
				exit;
			}

			$updatedStatus = array(
				"giftcertstatus" => (int)$_REQUEST['status']
			);

			if($_REQUEST['status'] == 2) {
				// Are gift certificates set to expire?
				if(GetConfig('GiftCertificateExpiry') > 0) {
					$expiry = time() + GetConfig('GiftCertificateExpiry');
				}
				else {
					$expiry = 0;
				}
				$updatedStatus['giftcertexpirydate'] = $expiry;
			}
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("gift_certificates", $updatedStatus, "giftcertid='".$GLOBALS['ISC_CLASS_DB']->Quote($certificate['giftcertid'])."'");
			echo 1;
			exit;
		}

		/**
		 * Show the quick view for gift certificates
		 *
		 * @return void
		 **/
		private function GiftCertificateQuickView()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('giftcertificates');
			if(!isset($_REQUEST['giftCertificateId'])) {
				exit;
			}

			$query = sprintf("SELECT * FROM [|PREFIX|]gift_certificates WHERE giftcertid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['giftCertificateId']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$certificate = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			// Fetch out the history for this gift certificate
			$query = "
				SELECT h.*, CONCAT(c.custconfirstname, ' ', c.custconlastname) AS customername
				FROM [|PREFIX|]gift_certificate_history h
				LEFT JOIN [|PREFIX|]customers c ON (c.customerid=h.histcustomerid)
				WHERE h.histgiftcertid='" . $certificate['giftcertid'] . "'
				ORDER BY historddate ASC";

			$GLOBALS['Message'] = isc_html_escape($certificate['giftcertmessage']);
			$GLOBALS['FromEmail'] = isc_html_escape($certificate['giftcertfromemail']);
			$GLOBALS['FromName'] = isc_html_escape($certificate['giftcertfrom']);
			$GLOBALS['ToEmail'] = isc_html_escape($certificate['giftcerttoemail']);
			$GLOBALS['ToName'] = isc_html_escape($certificate['giftcertto']);

			$GLOBALS['GiftCertificateHistory'] = '';

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$GLOBALS['CustomerName'] = isc_html_escape($row['customername']);
				$GLOBALS['CustomerId'] = (int) $row['histcustomerid'];
				$GLOBALS['OrderId'] = (int) $row['historderid'];
				$GLOBALS['OrderDate'] = CDate($row['historddate']);
				$GLOBALS['BalanceUsed'] = FormatPrice($row['histbalanceused']);
				$GLOBALS['BalanceRemaining'] = FormatPrice($row['histbalanceremaining']);

				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("giftcertificate.quickview.item");
				$GLOBALS['GiftCertificateHistory'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
			}

			if($GLOBALS['GiftCertificateHistory'] == '') {
				$GLOBALS['GiftCertificateHistory'] = sprintf('<div style="padding-left: 10px;">%s</div>', GetLang('GiftCertificateNotUsed'));
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("giftcertificate.quickview");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		 * Update the store credit for a customer
		 *
		 * @return void
		 **/
		private function UpdateStoreCredit()
		{
			if(!isset($_REQUEST['customerId']) || !$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Customers)) {
				exit;
			}

			$query = sprintf("SELECT customerid, custstorecredit FROM [|PREFIX|]customers WHERE customerid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['customerId']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$customer = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if($customer['customerid'] == 0) {
				exit;
			}

			$updatedCustomer = array(
				"custstorecredit" => DefaultPriceFormat($_REQUEST['credit'])
			);
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("customers", $updatedCustomer, "customerid='".$GLOBALS['ISC_CLASS_DB']->Quote($customer['customerid'])."'");

			// Log the credit change
			$creditChange = CFloat($_REQUEST['credit'] - $customer['custstorecredit']);
			if($creditChange != 0) {
				$creditLog = array(
					"customerid" => (int) $customer['customerid'],
					"creditamount" => $creditChange,
					"credittype" => "adjustment",
					"creditdate" => time(),
					"creditrefid" => 0,
					"credituserid" => $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetUserId(),
					"creditreason" => ""
				);
				$GLOBALS['ISC_CLASS_DB']->InsertQuery("customer_credits", $creditLog);
			}
			echo 1;
			exit;
		}

		/**
		 * Update the return status for an order
		 *
		 * @return void
		 **/
		private function UpdateReturnStatus()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('returns');
			if(!isset($_REQUEST['returnId'])) {
				exit;
			}

			$SQL = "SELECT r.*, o.ordcurrencyid, o.ordcurrencyexchangerate \n"
				 . "  FROM [|PREFIX|]returns r \n"
				 . "       JOIN [|PREFIX|]orders o ON r.retorderid = o.orderid \n"
				 . " WHERE r.returnid = " . (int)$_REQUEST['returnId'];

			$result = $GLOBALS['ISC_CLASS_DB']->Query($SQL);
			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if(!$row['returnid']) {
				exit;
			}

			// Do we have permission to alter this return?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $row['retvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				exit;
			}

			$GLOBALS['ISC_CLASS_ADMIN_RETURNS'] = GetClass('ISC_ADMIN_RETURNS');
			if($GLOBALS['ISC_CLASS_ADMIN_RETURNS']->UpdateReturnStatus($row, $_REQUEST['status'])) {
				echo 1;
			}
			exit;
		}

		/**
		 * Update the staff only notes for a return
		 *
		 * @return void
		 **/
		private function UpdateReturnNotes()
		{
			if(!isset($_REQUEST['returnId'])) {
				exit;
			}

			$query = sprintf("SELECT * FROM [|PREFIX|]returns WHERE returnid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['returnId']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if(!$row['returnid']) {
				exit;
			}

			// Do we have permission to alter this return?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $row['retvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				exit;
			}

			$updatedReturn = array(
				"retstaffnotes" => $_POST['returnNotes']
			);
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("returns", $updatedReturn, "returnid='".$GLOBALS['ISC_CLASS_DB']->Quote($row['returnid'])."'");
			echo 1;
			exit;
		}

		/**
		 * Display the quick view for a return
		 *
		 * @return void
		 **/
		private function ReturnQuickView()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('returns');
			if(!isset($_REQUEST['returnId'])) {
				exit;
			}

			$query = sprintf("SELECT * FROM [|PREFIX|]returns WHERE returnid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['returnId']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			// Do we have permission to view this return?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $row['retvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				exit;
			}


			$GLOBALS['ReturnReason'] = isc_html_escape($row['retreason']);

			if(!$row['retaction']) {
				$row['retaction'] = GetLang('NA');
			}

			$GLOBALS['ReturnAction'] = isc_html_escape($row['retaction']);
			$GLOBALS['ReturnId'] = (int) $row['returnid'];
			$GLOBALS['StaffNotes'] = isc_html_escape($row['retstaffnotes']);
			if(!$row['retcomment']) {
				$row['retcomment'] = GetLang('NA');
			}
			$GLOBALS['CustomerComments'] = isc_html_escape($row['retcomment']);

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("return.quickview");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		 * Show the quick view for a log entry
		 *
		 * @return void
		 **/
		private function LogInfoQuickView()
		{
			if(!isset($_REQUEST['logid'])) {
				exit;
			}

			$query = sprintf("SELECT logmsg FROM [|PREFIX|]system_log WHERE logid='%d'", (int)$_REQUEST['logid']);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$msg = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

			echo $msg;
			exit;
		}

		/**
		 * Show the select a product popup page on the create a coupon page
		 *
		 * @return void
		 **/
		private function PopupProductSearch()
		{
			if(isset($_REQUEST['category']) && $_REQUEST['category'] == 0) {
				unset($_REQUEST['category']);
			}

			if(!isset($_REQUEST['searchQuery']) && !isset($_REQUEST['category'])) {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', GetLang('ProductSelectEnterTerms'), true);
			}
			else {
				if(isset($_REQUEST['category'])) {
					$_REQUEST['category'] = array($_REQUEST['category']);
				}
				$ResultCount = 0;
				$GLOBALS['ISC_CLASS_ADMIN_PRODUCT'] = GetClass('ISC_ADMIN_PRODUCT');
				$products = $GLOBALS['ISC_CLASS_ADMIN_PRODUCT']->_GetProductList(0, 'p.prodname', 'asc', $ResultCount, 'p.productid,p.prodname,p.prodprice,p.prodsaleprice,p.prodretailprice,p.prodconfigfields,p.prodvariationid,p.prodtype,p.prodcode,p.prodistaxable,p.prodeventdaterequired', false);

				if($ResultCount == 0) {
					$tags[] = $this->MakeXMLTag('status', 0);
					if(isset($_REQUEST['searchQuery'])) {
						$tags[] = $this->MakeXMLTag('message', GetLang('ProductSelectNoProducts'), true);
					} else {
						$tags[] = $this->MakeXMLTag('message', GetLang('ProductSelectNoProductsCategory'), true);
					}
				}
				else {
					$results = '';
					$tags[] = $this->MakeXMLTag('status', 1);

					while($product = $GLOBALS['ISC_CLASS_DB']->Fetch($products)) {
						$actualPrice = CalcRealPrice($product['prodprice'], $product['prodretailprice'], $product['prodsaleprice'], $product['prodistaxable']);
						$actualPrice = CalcProdCustomerGroupPrice($product, $actualPrice);
						$isConfigurable = false;
						if($product['prodvariationid'] != 0 || $product['prodconfigfields'] != 0 || $product['prodeventdaterequired'] == 1) {
							$isConfigurable = true;
						}
						$results .= '
							<product>
								<productId>'.$product['productid'].'</productId>
								<productName><![CDATA['.$product['prodname'].']]></productName>
								<productPrice>'.FormatPrice($actualPrice, false, false, true).'</productPrice>
								<productCode><![CDATA['.$product['prodcode'].']]></productCode>
								<productType>'.$product['prodtype'].'</productType>
								<productConfigurable>'.(int)$isConfigurable.'</productConfigurable>
							</product>
						';
					}
					$tags[] = $this->MakeXMLTag('results', $results);
				}
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Update the sort order of the categories when they are reordered
		 *
		 * @return void
		 **/
		private function UpdateCategoryOrders()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('categories');

			$this->_BuildCategoryOrders($_POST['CategoryList']);

			// Update the data store
			$GLOBALS['ISC_CLASS_DATA_STORE']->UpdateRootCategories();

			// Also make sure that all the root categories do NOT have any images assoiated with them
			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->RemoveRootImages();

			// update the nested set values
			// @todo the front end currently does not tell the backend which category was moved, only the new structure - if this takes too long to run, the front end needs changing to include which category was moved so a partial update is possible
			$nested = new ISC_NESTEDSET_CATEGORIES();
			$nested->rebuildTree();

			$tags[] = $this->MakeXMLTag('status', 1);
			$tags[] = $this->MakeXMLTag('message', GetLang('CategoryOrdersUpdated'), true);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Automatically set the sortorder of the categories when they are dragged so they are displayed in the correct order
		 *
		 * @return void
		 **/
		private function _BuildCategoryOrders($list, $parent=0, $parents=array())
		{
			if(!is_array($list)) {
				return;
			}

			foreach($list as $order => $category) {
				$myParents = $parents;
				$myParents[] = $category['id'];
				$parentList = implode(",", $myParents);
				$updatedCategory = array(
					"catsort" => $order+1,
					"catparentid" => $parent,
					"catparentlist" => $parentList
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("categories", $updatedCategory, "categoryid='".$GLOBALS['ISC_CLASS_DB']->Quote((int)$category['id'])."'");
				if(isset($category['children'])) {
					$this->_BuildCategoryOrders($category['children'], $category['id'], $myParents);
				}
			}
		}

		/**
		* Updates the order of fields in an export template
		*
		**/
		private function UpdateTemplateFields()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('exporttemplates');

			$field_type = $_REQUEST['l'];

			$template_id = $_REQUEST["tempId"];

			$templates = GetClass('ISC_ADMIN_EXPORTTEMPLATES');
			try {
				$template = $templates->GetTemplate($template_id);

				if ($template['builtin']) {
					die();
				}
			}
			catch (Exception $ex) {
				die();
			}

			require_once(APP_ROOT . "/includes/exporter/class.exportfiletype.factory.php");

			$type = ISC_ADMIN_EXPORTFILETYPE_FACTORY::GetExportFileType($field_type);
			$fields = $type->LoadFields();

			foreach ($_POST[$field_type . "FieldList"] as $order => $field) {
				$field_id = substr($field, strpos($field, "-") + 1);

				if (!isset($fields[$field_id])) {
					continue;
				}

				//check if field exists
				$query = "SELECT exporttemplatefieldid FROM [|PREFIX|]export_template_fields WHERE fieldid = '" . $GLOBALS['ISC_CLASS_DB']->Quote($field_id) . "' AND fieldtype = '" . $GLOBALS['ISC_CLASS_DB']->Quote($field_type) . "' AND exporttemplateid = '" . $GLOBALS['ISC_CLASS_DB']->Quote($template_id) . "'";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

				if ($GLOBALS['ISC_CLASS_DB']->CountResult($result)) {
					$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

					// update existing field
					$query = "UPDATE [|PREFIX|]export_template_fields SET sortorder = $order WHERE exporttemplatefieldid = " . $row['exporttemplatefieldid'];
					$GLOBALS['ISC_CLASS_DB']->Query($query);
				}
				else {
					// create field
					 $field_array = array(
						"exporttemplateid"	=> $template_id,
						"fieldid"			=> $field_id,
						"fieldtype"			=> $field_type,
						"fieldname"			=> $fields[$field_id]['label'],
						"includeinexport"	=> 0,
						"sortorder"			=> $order
					);

					$GLOBALS['ISC_CLASS_DB']->InsertQuery('export_template_fields', $field_array);
				}

				$query = "UPDATE [|PREFIX|]export_template_fields SET sortorder = $order WHERE fieldid = '" . $GLOBALS['ISC_CLASS_DB']->Quote($field_id) . "' AND fieldtype = '" . $GLOBALS['ISC_CLASS_DB']->Quote($field_type) . "' AND exporttemplateid = '" . $GLOBALS['ISC_CLASS_DB']->Quote($template_id) . "'";
				$GLOBALS['ISC_CLASS_DB']->Query($query);
			}

			$tags[] = $this->MakeXMLTag('status', 1);
			$tags[] = $this->MakeXMLTag('message', sprintf(GetLang('FieldOrderUpdated'), $field_type), true);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Update the sort order of the pages
		 *
		 * @return void
		 **/
		private function UpdatePageOrders()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('pages');
			$this->_BuildPageOrders($_POST['PageList']);

			// Update the data store
			$GLOBALS['ISC_CLASS_DATA_STORE']->UpdatePages();

			// update the nested set values
			// @todo the front end currently does not tell the backend which page was moved, only the new structure - if this takes too long to run, the front end needs changing to include which page was moved so a partial update is possible
			$nested = new ISC_NESTEDSET_PAGES();
			$nested->rebuildTree();

			$tags[] = $this->MakeXMLTag('status', 1);
			$tags[] = $this->MakeXMLTag('message', GetLang('PageOrdersUpdated'), true);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Automatically set the sortorder of the pages when they are dragged so they are displayed in the correct order
		 *
		 * @return void
		 **/
		private function _BuildPageOrders($list, $parent=0, $parents=array())
		{
			if(!is_array($list)) {
				return;
			}

			foreach($list as $order => $page) {
				$myParents = $parents;
				$myParents[] = $page['id'];
				$parentList = implode(",", $myParents);
				$updatedPage = array(
					"pagesort" => $order+1,
					"pageparentid" => $parent,
					"pageparentlist" => $parentList
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("pages", $updatedPage, "pageid='".$GLOBALS['ISC_CLASS_DB']->Quote((int)$page['id'])."'");
				if(isset($page['children'])) {
					$this->_BuildPageOrders($page['children'], $page['id'], $myParents);
				}
			}
		}

		/**
		 * Bulk approve reviews from the manage reviews page
		 *
		 * @return void
		 **/
		private function ApproveReviews()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('reviews');
			$GLOBALS['ISC_CLASS_ADMIN_REVIEW'] = GetClass('ISC_ADMIN_REVIEW');
			$err = '';
			$msg = $GLOBALS['ISC_CLASS_ADMIN_REVIEW']->DoApproveReviews($_POST['reviews'], $err);
			if($err != "") {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $err, true);
			}
			else {
				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', $msg, true);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($_POST['reviews']));
				$numReviews = 0;
				$grid = $GLOBALS['ISC_CLASS_ADMIN_REVIEW']->ManageReviewsGrid($numReviews);
				if($numReviews > 0) {
					$tags[] = $this->MakeXMLTag('grid', $grid, true);
				}
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			exit;
		}

		/**
		 * Bulk dis-approve reviews from the manage reviews page
		 *
		 * @return void
		 **/
		private function DisapproveReviews()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('reviews');
			$GLOBALS['ISC_CLASS_ADMIN_REVIEW'] = GetClass('ISC_ADMIN_REVIEW');
			$err = '';
			$msg = $GLOBALS['ISC_CLASS_ADMIN_REVIEW']->DoDisapproveReviews($_POST['reviews'], $err);
			if($err != "") {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $err, true);
			}
			else {
				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', $msg, true);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($_POST['reviews']));
				$numReviews = 0;
				$grid = $GLOBALS['ISC_CLASS_ADMIN_REVIEW']->ManageReviewsGrid($numReviews);
				if($numReviews > 0) {
					$tags[] = $this->MakeXMLTag('grid', $grid, true);
				}
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			exit;
		}

		/**
		 * Bulk delete reviews from the manage reviews page
		 *
		 * @return void
		 **/
		private function DeleteReviews()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('reviews');
			$GLOBALS['ISC_CLASS_ADMIN_REVIEW'] = GetClass('ISC_ADMIN_REVIEW');
			$err = '';
			$msg = $GLOBALS['ISC_CLASS_ADMIN_REVIEW']->DoDeleteReviews($_POST['reviews'], $err);
			if($err != "") {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $err, true);
			}
			else {
				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', $msg, true);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($_POST['reviews']));

				$numReviews = 0;
				$grid = $GLOBALS['ISC_CLASS_ADMIN_REVIEW']->ManageReviewsGrid($numReviews);
				if($numReviews > 0) {
					$tags[] = $this->MakeXMLTag('grid', $grid, true);
				}
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			exit;
		}

		/**
		 * Allow the quick creation of a new category from the create product page
		 *
		 * @return void
		 **/
		private function SaveNewQuickCategory()
		{
			include_once(APP_ROOT."/../lib/api/category.api.php");

			$_POST['catpagetitle'] = '';
			$_POST['catmetakeywords'] = '';
			$_POST['catmetadesc'] = '';
			$_POST['catlayoutfile'] = '';
			$_POST['catsort'] = 0;
			$_POST['catimagefile'] = '';
			$_POST['catsearchkeywords'] = '';
			$_POST['cat_enable_optimizer'] = 0;

			$category = new API_CATEGORY();
			$CatID = $category->create();
			if($category->error) {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $category->error, true);
			}
			else {
				$tags[] = $this->MakeXMLTag('status', 1);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($CatID, $_POST['catname']);

				if(isset($_POST['selectedcats']) && is_array($_POST['selectedcats'])) {
					array_walk($_POST['selectedcats'], 'intval');
				}
				else {
					$_POST['selectedcats'] = array();
				}
				$_POST['selectedcats'][] = $CatID;
				$selectedCategories = $_POST['selectedcats'];
				require_once(dirname(__FILE__) . "/class.category.php");
				$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
				$categories = sprintf("<select size=\"5\" id=\"category\" name=\"category[]\" class=\"Field400 ISSelectReplacement\" style=\"height:140px;\" multiple>%s</select>", $GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->GetCategoryOptions($selectedCategories, "<option %s value='%d'>%s</option>", 'selected="selected"', "", false));
				$tags[] = $this->MakeXMLTag('catid', $CatID, true);
				$tags[] = $this->MakeXMLTag('categories', $categories, true);
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			exit;
		}

		/**
		 * Save a product download file for a digital product on the server from the add/edit product page
		 *
		 * @return void
		 **/
		private function SaveProductDownload()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('products');

			$GLOBALS['ISC_CLASS_ADMIN_PRODUCT'] = GetClass('ISC_ADMIN_PRODUCT');
			$err = '';
			$_REQUEST['downdescription'] = urldecode($_REQUEST['downdescription']);
			if($GLOBALS['ISC_CLASS_ADMIN_PRODUCT']->SaveProductDownload($err)) {
				if(isset($_REQUEST['downloadid'])) {
					$GLOBALS['ISC_LANG']['ProductDownloadUploaded'] = GetLang('ProductDownloadSaved');
				}
				if(isset($_REQUEST['productId'])) {
					$grid = $GLOBALS['ISC_CLASS_ADMIN_PRODUCT']->GetDownloadsGrid($_REQUEST['productId']);
				}
				else {
					$grid = $GLOBALS['ISC_CLASS_ADMIN_PRODUCT']->GetDownloadsGrid(0, $_REQUEST['productHash']);
				}
				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', GetLang('ProductDownloadUploaded'), true);
				$tags[] = $this->MakeXMLTag('grid', $grid, true);
			}
			else {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $err, true);
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Delete a product download file for a digital product on the server from the add/edit product page
		 *
		 * @return void
		 **/
		private function DeleteProductDownload()
		{
			$GLOBALS['ISC_CLASS_ADMIN_PRODUCT'] = GetClass('ISC_ADMIN_PRODUCT');
			$GLOBALS['ISC_CLASS_ADMIN_PRODUCT']->DeleteProductDownload($_REQUEST['downloadid']);
			$tags[] = $this->MakeXMLTag('status', 1);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Edit a product download file for a digital product on the server from the add/edit product page
		 *
		 * @return void
		 **/
		private function EditProductDownload()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('products');

			$query = sprintf("select * from [|PREFIX|]product_downloads where downloadid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['downloadid']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if (!$row) {
				die();
			}

			$GLOBALS['DownloadId'] = (int) $row['downloadid'];
			$GLOBALS['DownloadName'] = isc_html_escape($row['downname']);
			$GLOBALS['DownloadDescription'] = isc_html_escape($row['downdescription']);
			$GLOBALS['MaxDownloads'] = (int) $row['downmaxdownloads'];

			if($row['downexpiresafter']) {
				$days = $row['downexpiresafter']/86400;
				if(($days % 365) == 0) {
					$GLOBALS['ExpiresAfter'] = $days/365;
					$GLOBALS['RangeYearsSelected'] = "selected=\"selected\"";
				}
				else if(($days % 30) == 0) {
					$GLOBALS['ExpiresAfter'] = $days/30;
					$GLOBALS['RangeMonthsSelected'] = "selected=\"selected\"";
				}
				else if(($days % 7) == 0) {
					$GLOBALS['ExpiresAfter'] = $days/7;
					$GLOBALS['RageWeeksSelected'] = "selected=\"selected\"";
				}
				else {
					$GLOBALS['ExpiresAfter'] = $days;
				}
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("product.form.download.edit");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		private function PreviewLogo()
		{
			GetLib('logomaker/class.logomaker');

			$logoPath = ISC_BASE_PATH."/templates/".GetConfig('template')."/logo/";
			$configFile = $logoPath.'config.php';
			$logoName = GetConfig('template');

			if(!file_exists($configFile)) {
				$tags = array();
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', 'Config file for '.$logoName.' doesn\'t exist');
				$this->SendXMLHeader();
				$this->SendXMLResponse($tags);
				die();
			}

			require $configFile;
			$className = $logoName .'_logo';
			$tmpClass = new $className;
			$logoImage = $logoName.'.'.$tmpClass->FileType;

			if(isset($_POST['ExtraText0'])) {
				$fields = array();
				$name = 'ExtraText0';
				$i = 0;
				while(isset($_POST[$name])) {
					if($_POST[$name]) {
						$tmpClass->Text[$i] = $_POST[$name];
					}
					$i++;
					$name = 'ExtraText'.$i;
				}
			}

			$logoData = $tmpClass->GenerateLogo();
			ClearTmpLogoImages();
			$imageFile = 'tmp_'.$logoName.'_'.rand(5,10000).'.png';
			file_put_contents(ISC_BASE_PATH . '/cache/logos/'.$imageFile, $logoData);
			$tags = array();
			$tags[] = $this->MakeXMLTag('status', 1);
			$tags[] = $this->MakeXMLTag('logoImage', $imageFile);
			$tags[] = $this->MakeXMLTag('backgroundImage', GetConfig('ShopPath') . "/templates/".GetConfig('template')."/logo/" . $tmpClass->displayBgImg);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}


		/**
		 * Takes a filename (string) and makes sure that the extension is a valid image extension
		 *
		 * @param string $filename The filename to check the extension for
		 *
		 * @return boolean True if it is a valid image extension, false otherwise
		 */

		public function IsImageFileJpgPng($fileName)
		{
			$imgFiles = array('.jpg','.png');
			foreach($imgFiles as $_key=>$value) {
				$length = strlen($value);
				// check the extension
				if(strlen($fileName) > 0){
					$extStart = strlen($fileName)-$length;
					if($extStart >= 0){
						if(substr($fileName,$extStart) == $value) {
							// must be a bad file!
							return true;
						}
					}
				}
			}

			return false;
		}

		private function deleteHeaderImage()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('layout');

			$return = array('success'=>false);

			$headerImageUri = $headerImagePath = $hasHeaderImage = false;

			$imagesPath = ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/header_images';
			$imagesUri = GetConfig('ShopPath') . '/' . GetConfig('ImageDirectory') . '/header_images';

			$headerImageUriJPG  = $imagesUri . '/' . GetConfig('template') . '_headerImage.';
			$headerImagePathJPG = $imagesPath . '/' . GetConfig('template') . '_headerImage.';

			$headerImageUriPNG  =  $headerImageUriJPG . 'png';
			$headerImagePathPNG =  $headerImagePathJPG . 'png';

			$headerImageUriJPG  .= 'jpg';
			$headerImagePathJPG .= 'jpg';

			if (file_exists($headerImagePathJPG)) {
				$headerImagePath = $headerImagePathJPG;
				$headerImageUri = $headerImageUriJPG;
				$hasHeaderImage = true;
			} else if (file_exists($headerImagePathPNG)) {
				$headerImagePath = $headerImagePathPNG;
				$headerImageUri = $headerImageUriPNG;
				$hasHeaderImage = true;
			}

			if (!$hasHeaderImage) {
				$return['message'] = GetLang('HeaderImageCurrentDoesntExist');

			} elseif (!is_file($headerImagePath)) {
				$return['message'] = GetLang('HeaderImageCurrentDoesntExist');

			} else {
				if (@unlink($headerImagePath)) {
					$return['success'] = true;
					$return['message'] = GetLang('HeaderImageDeleteSuccess');
				} else {
					$return['message'] = sprintf(GetLang('HeaderImageDeleteFail'), $headerImagePath);
				}
			}

			die(isc_json_encode($return));
		}

		private function uploadHeaderImage()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('layout');

			if ($_FILES['HeaderImageFile']['error'] != 0 || $_FILES['HeaderImageFile']['size'] == 0) {
				die(isc_json_encode(array(
					'success' => false,
					'message' => GetLang('LayoutHeaderImageUploadNoValidImage').ini_get('upload_max_filesize')
				)));
			}

			if (!$this->IsImageFileJpgPng($_FILES["HeaderImageFile"]["name"])) {
				die(isc_json_encode(array(
					'success' => false,
					'message' => GetLang('LayoutHeaderImageUploadNoValidImage2')
				)));
			}

			$templateName = GetConfig('template');
			$fileParts = pathinfo($_FILES['HeaderImageFile']['name']);
			$ext = $fileParts['extension'];
			$imagesPath = ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/header_images';
			$imagesUri = GetConfig('ShopPath') . '/' . GetConfig('ImageDirectory') . '/header_images';

			if(!is_dir($imagesPath)) {
				mkdir($imagesPath, ISC_WRITEABLE_DIR_PERM);
			}

			$headerImagePath = $imagesPath . '/' . $templateName . '_headerImage.' . $ext;
			if (!move_uploaded_file($_FILES['HeaderImageFile']['tmp_name'], $headerImagePath)) {
				$message = str_replace('%%PATH%%', '/'. GetConfig('ImageDirectory') . '/header_images/', GetLang('LayoutHeaderImageUploadErrorPath'));
				die(isc_json_encode(array(
					'success' => false,
					'message' => $message
				)));
			}

			isc_chmod($headerImagePath, ISC_WRITEABLE_FILE_PERM);

			$dirObject = new DirectoryIterator($imagesPath  );
			foreach($dirObject as $fileName=>$objFile){
				if($objFile->getFilename() != $templateName . '_headerImage.' . $ext){
					@unlink($objFile->getPath()."/". $objFile->getFilename());
				}
			}

			die(isc_json_encode(array(
				'success' => true,
				'message' => GetLang('LayoutHeaderImageUploadSuccess'),
				'currentImage' => $imagesUri . '/' . $templateName . '_headerImage.' . $ext
			)));
		}

		private function downloadHeaderImage($getImage)
		{
			$templateImagesPath = ISC_BASE_PATH . "/templates/" . GetConfig('template') . '/images/'. GetConfig('SiteColor');
			$imagesPath = ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/header_images';

			$headerImagePath = $templateImagesPath . '/headerImage';
			$currentPath = $imagesPath . '/' . GetConfig('template') . '_headerImage';
			$filePath = '';

			switch($getImage) {
				case 'original':
					$filePath = $headerImagePath;
					$fileName = "headerImage";
					break;
				case 'blank':
					$filePath = $headerImagePath . 'Blank';
					$fileName = "headerImageBlank";
					break;
				case 'current':
					$filePath = $currentPath;
					$fileName = GetConfig('template') . '_headerImage';
					break;
				default:
					die('Invalid header image type');
			}

			if (file_exists($filePath . '.jpg')) {
				$filePath .= '.jpg';
				$fileName .= '.jpg';
				header("Content-type: image/jpeg");

			} else if (file_exists($filePath . '.png')) {
				$filePath .= '.png';
				$fileName .= '.png';
				header("Content-type: image/png");
			} else {
				die('Invalid header image type');
			}

			ob_end_clean();

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment; filename=" .$fileName . ";");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($filePath));

			$fp = fopen($filePath, "rb");
			while (!feof($fp)) {
				echo fread($fp, 8192);
				flush();
			}

			@fclose($fp);
			die();
		}

		private function getHeaderImage()
		{
			$imagesUri = GetConfig('ShopPath') . '/' . GetConfig('ImageDirectory') . '/header_images';
			$imagesPath = ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/header_images';

			$templateImagesUri  = GetConfig('ShopPath') . "/templates/" . GetConfig('template') . '/images/'. GetConfig('SiteColor');
			$templateImagesPath = ISC_BASE_PATH . "/templates/" . GetConfig('template') . '/images/'. GetConfig('SiteColor');

			$headerImageUriJPG  = $imagesUri . '/' . GetConfig('template') . '_headerImage.';
			$headerImagePathJPG = $imagesPath . '/' . GetConfig('template') . '_headerImage.';

			$headerImageUriPNG  =  $headerImageUriJPG . 'png';
			$headerImagePathPNG =  $headerImagePathJPG . 'png';

			$headerImageUriJPG  .= 'jpg';
			$headerImagePathJPG .= 'jpg';

			if (file_exists($headerImagePathJPG)) {
				$headerImagePath = $headerImagePathJPG;
				$headerImageUri = $headerImageUriJPG;
				$hasCurrent = true;
			} else if (file_exists($headerImagePathPNG)) {
				$headerImagePath = $headerImagePathPNG;
				$headerImageUri = $headerImageUriPNG;
				$hasCurrent = true;
			} else {

				$headerImageUriJPG  = $templateImagesUri . '/headerImage.jpg';
				$headerImagePathJPG = $templateImagesPath . '/headerImage.jpg';

				$headerImageUriPNG  = $templateImagesUri . '/headerImage.png';
				$headerImagePathPNG	 = $templateImagesPath . '/headerImage.png';
				$hasCurrent = false;

				if (file_exists($headerImagePathJPG)) {
					$headerImagePath = $headerImagePathJPG;
					$headerImageUri = $headerImageUriJPG;
				} else if (file_exists($headerImagePathPNG)) {
					$headerImagePath = $headerImagePathPNG;
					$headerImageUri = $headerImageUriPNG;
				} else {
					die(isc_json_encode(array('success' => false)));
				}
			}

			$imageTag = '<img src="' . $headerImageUri .'" />';
			$currentImage = $headerImageUri;

			$headerImageBlankUriJPG  = $templateImagesUri . '/headerImageBlank.jpg';
			$headerImageBlankPathJPG = $templateImagesPath . '/headerImageBlank.jpg';

			$headerImageBlankUriPNG  = $templateImagesUri . '/headerImageBlank.png';
			$headerImageBlankPathPNG = $templateImagesPath . '/headerImageBlank.png';

			$headerImageOrigUriJPG  = $templateImagesUri . '/headerImage.jpg';
			$headerImageOrigPathJPG = $templateImagesPath . '/headerImage.jpg';

			$headerImageOrigUriPNG  = $templateImagesUri . '/headerImage.png';
			$headerImageOrigPathPNG	 = $templateImagesPath . '/headerImage.png';

			if (file_exists($headerImageBlankPathJPG)) {
				$blankImage = $headerImageBlankUriJPG;
				$origImage = $headerImageOrigUriJPG;

			} else if (file_exists($headerImageBlankPathPNG)) {
				$blankImage = $headerImageBlankUriPNG;
				$origImage = $headerImageOrigUriPNG;

			} else {
				$blankImage = false;
			}

			die(isc_json_encode(array('success' => true, 'image' => $imageTag, 'origBlank' => $blankImage, 'origImage' => $origImage, 'hasCurrent'=>$hasCurrent, 'currentImage'=>$currentImage)));
		}


		private function UpdateLogo()
		{
			$tpl = GetClass('ISC_ADMIN_LAYOUT');
			$textArray = array();

			$fields = array();
			if(isset($_POST['ExtraText0'])) {
				$name = 'ExtraText0';
				$i = 0;
				while(isset($_POST[$name])) {
					$fields[] = $_POST[$name];
					$i++;
					$name = 'ExtraText'.$i;
				}
			}

			$imageFile = $tpl->BuildLogo($_REQUEST['logo'], $fields);

			if(!$imageFile) {
				$tags = array();
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', 'Config file for '.$logoName.' doesn\'t exist');
				$this->SendXMLHeader();
				$this->SendXMLResponse($tags);
				die();
			}

			$logoName = GetConfig('template');
			$className = $logoName .'_logo';
			$tmpClass = new $className;

			$tags = array();
			$tags[] = $this->MakeXMLTag('status', 1);
			$tags[] = $this->MakeXMLTag('logoImage', $imageFile);
			$tags[] = $this->MakeXMLTag('backgroundImage', GetConfig('ShopPath') . "/templates/".GetConfig('template')."/logo/" . $tmpClass->displayBgImg);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		private function UpdateLogoNone()
		{
			$s = GetClass('ISC_ADMIN_SETTINGS');

			$GLOBALS['ISC_NEW_CFG']['LogoType'] = "text";

			$GLOBALS['ISC_NEW_CFG']['UsingLogoEditor'] = 0;
			$GLOBALS['ISC_NEW_CFG']['UsingTemplateLogo'] = 0;

			if($_POST['UseAlternateTitle'] == 'true') {
				$GLOBALS['ISC_NEW_CFG']['UseAlternateTitle'] = 1;
				$GLOBALS['ISC_NEW_CFG']['AlternateTitle'] = $_POST['AlternateTitle'];
			}
			else {
				$GLOBALS['ISC_NEW_CFG']['UseAlternateTitle'] = 0;
				$GLOBALS['ISC_NEW_CFG']['AlternateTitle'] = '';
			}
			$s->CommitSettings();

			$tags = array();
			$tags[] = $this->MakeXMLTag('status', 1);
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Download a new template zip file on the store design page
		 *
		 * @return void
		 **/
		private function DownloadTemplateFile()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('layout');
			$tpl = GetClass('ISC_ADMIN_LAYOUT');
			$tpl->DownloadNewTemplates2();

			if(!isset($GLOBALS['ErrorMessage']) || $GLOBALS['ErrorMessage'] == "") {
				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', GetLang("TemplatesDownloadedOK"), true);
				$tags[] = $this->MakeXMLTag('template', $_REQUEST['template'], true);
			}else {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $GLOBALS['ErrorMessage'], true);
				$tags[] = $this->MakeXMLTag('template', $_REQUEST['template'], true);
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Check to make sure a template is the latest version
		 *
		 * @return void
		 **/
		private function CheckTemplateVersion()
		{
			$tpl = GetClass('ISC_ADMIN_LAYOUT');
			$url = $tpl->BuildTemplateURL($GLOBALS['ISC_CFG']['TemplateVersionURL'], array(
				"template" => GetConfig('template'),
				"version" => PRODUCT_VERSION_CODE
			));
			// Get a list of available templates
			$version = '';
			$version = PostToRemoteFileAndGetResponse($url);

			if ($version) {
				if(isc_strtolower(isc_substr($version, 0, 5)) != "error") {
					// success
					$tags[] = $this->MakeXMLTag('status', 1);
					if ($version === '') {
						$verson = 0;
					}
					$tags[] = $this->MakeXMLTag('version', $version);
				}else {
					// there was a problem
					$tags[] = $this->MakeXMLTag('status', 0);
					$tags[] = $this->MakeXMLTag('message', $version);
				}
			}else {
				// there was a problem
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', 'Unable to open remote site for version checking');
			}
			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Check that the license key is valid for a commerical template
		 *
		 * @return void
		 **/
		private function CheckTemplateKey()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('layout');
			GetLib('class.remoteopener');

			$opener = new connect_remote();

			// Get the information about this template from the remote server
			$host = base64_encode($_SERVER['HTTP_HOST']);
			$urlBits = array(
				'template' => urlencode($_REQUEST['template']),
				'key' => urlencode($_POST['templateKey']),
				'host' => $host
			);

			$url = $this->BuildTemplateURL($GLOBALS['ISC_CFG']['TemplateVerifyURL'], $urlBits);

			$response = PostToRemoteFileAndGetResponse($url);

			// A remote connection couldn't be established
			if($response === null) {
				exit;
			}

			$templateXML = @simplexml_load_string($response);
			if(!is_object($templateXML)) {
				exit;
			}

			$GLOBALS['ErrorMessage'] = '';
			if(isset($templateXML->error)) {
				switch(strval($this->error)) {
					case "invalid":
						$GLOBALS['ErrorMessage'] = GetLang('InvalidKey');
						return false;
					case "invalid_domain":
						$GLOBALS['ErrorMessage'] = GetLang('InvalidKeyDomain');
						return false;
					case "invalid_tpl":
						$GLOBALS['ErrorMessage'] = GetLang('InvalidKeyTemplate');
						return false;
					case "invalid_tpl2":
						$GLOBALS['ErrorMessage'] = GetLang('InvalidKeyTemplate2');
						return false;
					default:
						$GLOBALS['ErrorMessage'] = GetLang('InvalidTemplate');
						return false;
				}
			}

			if($GLOBALS['ErrorMessage'] == '') {
				$tags[] = $this->MakeXMLTag('status', 1);
			}
			else {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $GLOBALS['ErrorMessage']);
			}

			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Get the related products when you pick a category on the add/edit a product page if you are manually specifying related products
		 *
		 * @return void
		 **/
		private function GetRelatedProducts()
		{
			$output = "";
			$cat = (int)$_REQUEST['c'];

			$query = sprintf("select * from [|PREFIX|]categoryassociations ca inner join [|PREFIX|]products p on ca.productid=p.productid where ca.categoryid='%d' order by p.prodname", $GLOBALS['ISC_CLASS_DB']->Quote($cat));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$output .= sprintf("%d~~~%s|||", $row['productid'], $row['prodname']);
			}

			echo $output;
		}

		/**
		 * Show the inventory management quick view on the manage products page if inventory tracking is on for a product
		 *
		 * @return void
		 **/
		private function GetInventoryLevels()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('products');

			if(isset($_REQUEST['p']) && isset($_REQUEST['i']) && isset($_REQUEST['v']) && isset($_REQUEST['t'])) {
				$prodId = (int)$_REQUEST['p'];
				$invType = (int)$_REQUEST['i'];
				$variationId = (int)$_REQUEST['v'];
				$combinations = array();

				// First determine if inventory tracking is by product or by option
				if ($invType == 1) {
					// Simply query the products table for current and low stock levels
					$query = sprintf("select prodcurrentinv, prodlowinv from [|PREFIX|]products where productid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($prodId));
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

					if($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						printf("<b style='font-size:13px; padding-bottom:5px'>%s</strong>", GetLang("UpdateInventoryLevels"));
						echo "<table border='0'>";
						echo "<tr>";
						echo "<td valign='top'><img src='images/nodejoin.gif' style='padding-top:5px' /></td>";
						printf("<td>%s:</td>", GetLang("CurrentStock"));
						printf("<td><input type='text' size='3' value='%d' name='stock_level_%d' id='stock_level_%d' /></td>", $row['prodcurrentinv'], $prodId, $prodId);
						echo "</tr>";
						echo "<tr>";
						echo "<td>";
						printf("<td>%s:</td>", GetLang("LowStockLevel"));
						printf("<td><input type='text' size='3' value='%d' name='stock_level_notify_%d' id='stock_level_notify_%d' /></td>", $row['prodlowinv'], $prodId, $prodId);
						echo "</tr>";
						echo "</table>";
						printf("<input class='StockButton' type='button' value='%s' onclick='UpdateStockLevel(%d, 0)' style='margin-left:110px' />&nbsp; <img src='images/ajax-blank.gif' id='loading%d' />", GetLang("Save"), $prodId, $prodId);
					}
				} else {
					$optionIds = array();

					// Fetch out the variation combinations for this product
					$query = "SELECT * FROM [|PREFIX|]product_variation_combinations WHERE vcproductid='".$prodId."'";
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					while($combination = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						$combinations[] = $combination;
						$optionIds = array_merge($optionIds, explode(",", $combination['vcoptionids']));
					}

					$optionIds = array_unique($optionIds);

					// Now fetch out the options we need to get
					if(!empty($optionIds)) {
						$optionIds = implode(",", $optionIds);
						// Get the combination options
						$variations = array();
						$query = "SELECT * FROM [|PREFIX|]product_variation_options WHERE voptionid IN (".$optionIds.")";
						$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
						while($variation = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
							$variations[$variation['voptionid']] = array($variation['voname'], $variation['vovalue']);
						}
					}

					printf("<b style='font-size:13px'>%s</strong><div style='padding:20px 20px 0px 20px'>", GetLang("UpdateInventoryLevels"));

					foreach($combinations as $row) {
						$output = "";
						$options = explode(",", $row['vcoptionids']);

						foreach($options as $option) {
							$output .= isc_html_escape($variations[$option][0]) . ": " . isc_html_escape($variations[$option][1]) . ", ";
						}

						$output = trim($output, ', ');
						echo "<strong><em>" . $output . "</em></strong>";
						echo "<br />";
						echo "<table border='0' style='padding-bottom:10px'>";
						echo "<tr>";
						echo "<td valign='top'><img src='images/nodejoin.gif' style='padding-top:5px' /></td>";
						printf("<td>%s:</td>", GetLang("CurrentStock"));
						printf("<td><input type='text' size='3' value='%d' name='stock_level_%d_%d' id='stock_level_%d_%d' /></td>", $row['vcstock'], $prodId, $row['combinationid'], $prodId, $row['combinationid']);
						echo "</tr>";
						echo "<tr>";
						echo "<td>";
						printf("<td>%s:</td>", GetLang("LowStockLevel"));
						printf("<td><input type='text' size='3' value='%d' name='stock_level_%d_%d' id='stock_level_notify_%d_%d' /></td>", $row['vclowstock'], $prodId, $row['combinationid'], $prodId, $row['combinationid']);
						echo "</tr>";
						echo "</table>";
					}

					echo "</div>";
					printf("<input class='StockButton' type='button' value='%s' onclick='UpdateStockLevel(%d, 1)' style='margin-left:130px' />&nbsp; <img src='images/ajax-blank.gif' id='loading%d' />", GetLang('Save'), $prodId, $prodId);
				}
			}
		}

		/**
		 * Display the configurable product fields in order's quick view
		 *
		 * @param int $orderProdId Order product item id
		 * @param int $orderId order id
		 * @return void
		 **/
		private function GetOrderProductsFieldsRow($fields)
		{
			if(empty($fields)) {
				return '';
			}

			$productFields = '';

			$productFields .= "<tr><td height='18' class='text' colspan='2'><div style='padding-left: 20px;'><strong>".GetLang('ConfigurableFields').":</strong><br /><dl class='HorizontalFormContainer'>";

			foreach($fields as $field) {
				$fieldValue = '';
				$fieldName = $field['fieldname'];
				switch($field['fieldtype']) {
					// the field is a file, add a link to the file name
					case 'file':
						$fieldValue = "<a target='_blank' href='".$GLOBALS['ShopPath']."/viewfile.php?orderprodfield=".$field['orderfieldid']."' >".isc_html_escape($field['originalfilename'])."</a>";
						break;
					case 'checkbox':
						$fieldValue = GetLang('Checked');
						break;
					default:
						if(isc_strlen($field['textcontents'])>50) {
							$fieldValue = isc_html_escape(isc_substr($field['textcontents'], 0, 50))." <a href='#' onclick='Order.LoadOrderProductFieldData(".$field['orderid']."); return false;'><i> ".GetLang('More')."</i></a>";
						} else {
							$fieldValue = isc_html_escape($field['textcontents']);
						}
						break;
				}

				if($fieldValue != '') {
					$productFields .= "<dt>".isc_html_escape($fieldName).":</dt>";
					$productFields .= "<dd>".$fieldValue."</dd>";
				}
			}

			$productFields .= "</dl></div></td></tr>";
			return $productFields;
		}

		/**
		 * Display the quick view for an order
		 *
		 * @return void
		 **/
		public function GetOrderQuickView()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('orders');

			// Output a quick view for this order to be used on the manage orders page
			$orderId = (int) $_REQUEST['o'];
			$GLOBALS["OrderId"] = $orderId;

			// Get the details for this order from the database
			$query = "
				SELECT o.*, CONCAT(custconfirstname, ' ', custconlastname) AS custname, custconemail, custconphone, s.zonename AS shippingzonename,
				(SELECT COUNT(messageid) FROM [|PREFIX|]order_messages WHERE messageorderid=orderid AND messagestatus='unread') AS numunreadmessages
				FROM [|PREFIX|]orders o
				LEFT JOIN [|PREFIX|]customers c ON (c.customerid=o.ordcustid)
				LEFT JOIN [|PREFIX|]shipping_zones s ON (s.zoneid=o.ordshippingzoneid)
				WHERE o.orderid='".$GLOBALS['ISC_CLASS_DB']->Quote($orderId)."'
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				// If this user is a vendor, do they have permission to acess this order?
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $row['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					exit;
				}

				$GLOBALS['OrderDate'] = isc_date("d M Y H:i:s",$row['orddate']);
				$GLOBALS['ISC_CLASS_ADMIN_ORDERS'] = GetClass('ISC_ADMIN_ORDERS');
				$GLOBALS['OrderStatusOptions'] = $GLOBALS['ISC_CLASS_ADMIN_ORDERS']->GetOrderStatusOptions($row['ordstatus']);
				$GLOBALS['TrackingNo'] = $row['ordtrackingno'];
				$GLOBALS['NumMessages'] = $row['numunreadmessages'];

				if($row["numunreadmessages"] == 0) {
					$GLOBALS["HideMessages"] = "none";
				}

				if(!gzte11(ISC_LARGEPRINT)) {
					$GLOBALS["HideMessageItems"] = "none";
				}

				$row['custname'] = isc_html_escape(trim($row['custname']));

				$addressDetails = array(
					'shipfirstname'	=> $row['ordbillfirstname'],
					'shiplastname'	=> $row['ordbilllastname'],
					'shipcompany'	=> $row['ordbillcompany'],
					'shipaddress1'	=> $row['ordbillstreet1'],
					'shipaddress2'	=> $row['ordbillstreet2'],
					'shipcity'		=> $row['ordbillsuburb'],
					'shipstate'		=> $row['ordbillstate'],
					'shipzip'		=> $row['ordbillzip'],
					'shipcountry'	=> $row['ordbillcountry'],
					'countrycode'	=> $row['ordbillcountrycode'],
				);
				$GLOBALS['BillingAddress'] = ISC_ADMIN_ORDERS::BuildOrderAddressDetails($addressDetails);

				$GLOBALS['BillingEmail'] = '';
				$GLOBALS['BillingPhone'] = '';
				$GLOBALS['ShippingEmail'] = '';
				$GLOBALS['ShippingPhone'] = '';

				// For the iPhone's "Map This" feature
				$GLOBALS['OneLineBillingAddress'] = trim(isc_html_escape($row['ordbillstreet1'] . ' ' . $row['ordbillstreet2'] . ' ' . $row['ordbillsuburb'] . ' ' . $row['ordbillstate'] . ' ' . $row['ordbillzip'] . ' ' . $row['ordbillcountry']));
				$GLOBALS['OneLineShippingAddress'] = trim(isc_html_escape($row['ordshipstreet1'] . ' ' . $row['ordshipstreet2'] . ' ' . $row['ordshipsuburb'] . ' ' . $row['ordshipstate'] . ' ' . $row['ordshipzip'] . ' ' . $row['ordshipcountry']));

				// This customer still exists, use their most recent email address and phone number
				if($row['custname'] != '') {
					$GLOBALS['BillingEmail'] = sprintf('<a href="mailto:%s" target="_blank">%s</a>', urlencode($row['custconemail']), isc_html_escape($row['custconemail']));
					$GLOBALS['ShippingEmail'] = sprintf('<a href="mailto:%s" target="_blank">%s</a>', urlencode($row['custconemail']), isc_html_escape($row['custconemail']));

					if ($row['ordbillphone'] != '') {
						$GLOBALS['BillingPhone'] = isc_html_escape($row['ordbillphone']);
					} else {
						$GLOBALS['BillingPhone'] = isc_html_escape($row['custconphone']);
					}

					if ($row['ordshipphone'] != '') {
						$GLOBALS['ShippingPhone'] = isc_html_escape($row['ordshipphone']);
					} else {
						$GLOBALS['ShippingPhone'] = isc_html_escape($row['custconphone']);
					}
				}
				// Customer has been removed but we still have the email address and phone number from when they placed their order
				else if($row['ordbillphone'] != '' || $row['ordbillemail'] != '' || $row['ordshipphone'] != '' || $row['ordshipemail'] != '') {
					$GLOBALS['BillingEmail'] = sprintf('<a href="mailto:%s" target="_blank">%s</a>', $row['ordbillemail'], $row['ordbillemail']);
					$GLOBALS['BillingPhone'] = isc_html_escape($row['ordbillphone']);
					$GLOBALS['ShippingEmail'] = sprintf('<a href="mailto:%s" target="_blank">%s</a>', $row['ordshipemail'], $row['ordshipemail']);
					$GLOBALS['ShippingPhone'] = isc_html_escape($row['ordshipphone']);
				}

				if ($GLOBALS['BillingPhone'] === '') {
					$GLOBALS['BillingPhone'] = GetLang('NA');
				}
				if ($GLOBALS['BillingEmail'] === '') {
					$GLOBALS['BillingEmail'] = GetLang('NA');
				}
				if ($GLOBALS['ShippingPhone'] === '') {
					$GLOBALS['ShippingPhone'] = GetLang('NA');
				}
				if ($GLOBALS['ShippingEmail'] === '') {
					$GLOBALS['ShippingEmail'] = GetLang('NA');
				}

				$GLOBALS['PaymentMethod'] = array();

				if($row['orderpaymentmethod'] == '') {
					$row['orderpaymentmethod'] = "N/A";
				}

				if($row['orderpaymentmethod'] != "storecredit" && $row['orderpaymentmethod'] != "giftcertificate") {
					if($row['ordgatewayamount']) {
						$row['orderpaymentmethod'] .= " (". FormatPriceInCurrency($row['ordgatewayamount'], $row['orddefaultcurrencyid']).")";
					}
					else {
						$row['orderpaymentmethod'] .= " (". FormatPriceInCurrency($row['ordtotalamount'], $row['orddefaultcurrencyid']).")";
					}

					// Does the payment method have any extra info to show?
					$provider = null;
					$GLOBALS['ExtraInfo'] = '';
					if(GetModuleById('checkout', $provider, $row['orderpaymentmodule'])) {
						if(method_exists($provider, "DisplayPaymentDetails")) {
							$GLOBALS['ExtraInfo'] = $provider->DisplayPaymentDetails($row);
						}
					}

					$GLOBALS['PaymentMethod'][] = $row['orderpaymentmethod'];
				}
				if($row['ordstorecreditamount'] > 0) {
					$GLOBALS['PaymentMethod'][] = GetLang('PaymentStoreCredit') . " (".FormatPriceInCurrency($row['ordstorecreditamount'], $row['orddefaultcurrencyid']) . ")";
				}

				if($row['ordgiftcertificateamount'] > 0 && gzte11(ISC_LARGEPRINT)) {
					$GLOBALS['PaymentMethod'][] = sprintf(GetLang('PaymentGiftCertificates'), $row['orderid']) . " (".FormatPriceInCurrency($row['ordgiftcertificateamount'], $row['orddefaultcurrencyid']) . ")";
				}

				$GLOBALS['IPAddress'] = $row['ordipaddress'];

				$GLOBALS['PaymentMethod'] = implode("<br />", $GLOBALS['PaymentMethod']);

				$GLOBALS['HideShippingZone'] = 'display: none';

				if ($row['ordpayproviderid'] != '') {
					$GLOBALS['TransactionId'] = $row['ordpayproviderid'];
				} else {
					$GLOBALS['TransactionId'] = GetLang('NA');
					$GLOBALS['HideTransactionId'] = 'display: none';
				}

				$extraArray = @unserialize($row['extrainfo']);
				$paymentMessage = '';
				if(isset($extraArray['payment_message']) && $extraArray['payment_message']!= '') {
					$paymentMessage = "<br />".isc_html_escape($extraArray['payment_message']);
				}

				if (isset($row['ordpaymentstatus']) && $row['ordpaymentstatus'] != '') {
					$GLOBALS['PaymentStatus'] = ucfirst($row['ordpaymentstatus']).$paymentMessage;
				}
				else {
					$GLOBALS['PaymentStatus'] = GetLang('NA');
					if($paymentMessage) {
						$GLOBALS['PaymentStatus'] .= $paymentMessage;
					}
					else {
						$GLOBALS['HidePaymentStatus'] = 'display: none';
					}
				}

				$GLOBALS['CouponsUsed'] = '';
				$GLOBALS['HideCouponsUsed'] = 'display: none';

				// Get the products in the order
				$query = "SELECT o.*
					FROM [|PREFIX|]order_coupons o
					WHERE ordcouporderid='" . $orderId . "'";

				$coupons = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($coupon = $GLOBALS['ISC_CLASS_DB']->Fetch($coupons)) {

					$GLOBALS['CouponsUsed'] .= $coupon['ordcouponcode'] . ',';

					$GLOBALS['HideCouponsUsed'] = '';
				}


				// If it's a digital order then we don't need to show the shipping details
				if ($row['ordisdigital'] == 0) {
					$addressDetails = array(
						'shipfirstname'	=> $row['ordshipfirstname'],
						'shiplastname'	=> $row['ordshiplastname'],
						'shipcompany'	=> $row['ordshipcompany'],
						'shipaddress1'	=> $row['ordshipstreet1'],
						'shipaddress2'	=> $row['ordshipstreet2'],
						'shipcity'		=> $row['ordshipsuburb'],
						'shipstate'		=> $row['ordshipstate'],
						'shipzip'		=> $row['ordshipzip'],
						'shipcountry'	=> $row['ordshipcountry'],
						'countrycode'	=> $row['ordshipcountrycode'],
					);
					$GLOBALS['ShippingAddress'] = ISC_ADMIN_ORDERS::BuildOrderAddressDetails($addressDetails);

					if ($row['ordshipmethod'] != "") {
						$GLOBALS['ShippingMethod'] = isc_html_escape($row['ordshipmethod']);
					} else {
						$GLOBALS['ShippingMethod'] = GetLang('NA');
					}

					if($row['ordshippingzoneid'] != 0) {
						$GLOBALS['HideShippingZone'] = '';
						if($row['shippingzonename']) {
							$GLOBALS['ShippingZone'] = "<a href=\"index.php?ToDo=editShippingZone&amp;zoneId=".$row['ordshippingzoneid']."\">".isc_html_escape($row['shippingzonename'])."</a>";
							$GLOBALS['ShippingZoneNoLink'] = isc_html_escape($row['shippingzonename']);
						}
						else {
							$GLOBALS['ShippingZone'] = isc_html_escape($row['shippingzonename']);
						}
					}

					$GLOBALS['ShippingCost'] = FormatPriceInCurrency($row['ordshipcost'], $row['orddefaultcurrencyid']);
				}
				else {
					$GLOBALS['HideShippingPanel'] = "none";
				}

				$GLOBALS['HideVendor'] = 'display: none';
				if(gzte11(ISC_HUGEPRINT) && $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() == 0 && $row['ordvendorid'] > 0) {
					$GLOBALS['HideVendor'] = '';
					$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');
					if(isset($vendorCache[$row['ordvendorid']])) {
						$vendor = $vendorCache[$row['ordvendorid']];
						$GLOBALS['VendorName'] = isc_html_escape($vendor['vendorname']);
						$GLOBALS['VendorId'] = $vendor['vendorid'];
						$GLOBALS['HideVendor'] = '';
					}
				}

				$prodFieldsArray=$GLOBALS['ISC_CLASS_ADMIN_ORDERS']->GetOrderProductFieldsData($orderId);

				// Get the products in the order
				$query = "
					SELECT o.*, p.prodname
					FROM [|PREFIX|]order_products o
					LEFT JOIN [|PREFIX|]products p ON (p.productid=o.ordprodid)
					WHERE orderorderid='" . $orderId . "'
					ORDER BY ordprodname";

				$pResult = $GLOBALS['ISC_CLASS_DB']->Query($query);

				$GLOBALS['ProductsTable'] = "<table width=\"95%\" align=\"center\" border=\"0\" cellspacing=0 cellpadding=0>";

				// Add a notice about the order containing only digitally downloadable products
				if($row['ordisdigital'] == 1) {
					$GLOBALS['ProductsTable'] .= sprintf("

						<tr>
							<td style=\"padding:5px; background-color:lightyellow\" width=\"100%%\" class=\"text\" colspan=\"2\">
								%s
							</td>
						</tr>
						<tr>
							<td colspan=\"2\">&nbsp;</td>
						</tr>
					", GetLang('DigitalOrderNotice'));
				}
				$wrappingTotal = 0;
				$productAmounts = array();
				while($pRow = $GLOBALS['ISC_CLASS_DB']->Fetch($pResult)) {
					$sku = "";

					if($pRow['ordprodsku'] != "") {
						$sku = "<br /><em>" . isc_html_escape($pRow['ordprodsku']) . "</em>";
					}

					$sStart = $sEnd = '';
					$refunded = '';
					$shippedLabel = '';
					if($pRow['ordprodqtyshipped'] > 0) {
						$shippedLabel = '<div class="Shipped">'.sprintf(GetLang('OrderProductsShippedX'), $pRow['ordprodqtyshipped']).'</div>';
					}

					if($pRow['ordprodrefunded'] > 0) {
						if($pRow['ordprodrefunded'] == $pRow['ordprodqty']) {
							$sStart = "<del>";
							$sEnd = "</del>";
							$refunded = '<div class="Refunded">'.GetLang('OrderProductRefunded').'</span>';
						}
						else {
							$refunded = '<div class="Refunded">'.sprintf(GetLang('OrderProductsRefundedX'), $pRow['ordprodrefunded']).'</div>';
						}
						$cost = $pRow['ordprodcost'] * ($pRow['ordprodqty'] - $pRow['ordprodrefunded']);
					}
					else {
						$cost = $pRow['ordprodcost'] * $pRow['ordprodqty'];
					}

					if($pRow['prodname']) {
						$pRow['ordprodname'] = "<a href='".ProdLink($pRow['prodname'])."' target='_blank'>".isc_html_escape($pRow['ordprodname'])."</a>";
					}

					$pOptions = '';
					if($pRow['ordprodoptions'] != '') {
						$options = @unserialize($pRow['ordprodoptions']);
						if(!empty($options)) {
							$pOptions = "<blockquote style=\"padding-left: 10px; margin: 0;\">";
							$comma = '';
							foreach($options as $name => $value) {
								$pOptions .= $comma.isc_html_escape($name).": ".isc_html_escape($value);
								$comma = '<br />';
							}
							$pOptions .= "</blockquote>";
						}
					}

					if($pRow['ordprodwrapcost'] > 0) {
						$wrappingTotal += $pRow['ordprodwrapcost'] * $pRow['ordprodqty'];
					}

					$giftOptions = '';
					if($pRow['ordprodwrapname']) {
						$giftOptions .= "<tr><td height='18' class='QuickGiftWrapping text' colspan='2'><div>";
						$giftOptions .= GetLang('GiftWrapping').": ".isc_html_escape($pRow['ordprodwrapname']);
						$giftOptions .= " [<a href='#' onclick=\"\$.iModal({type: 'ajax', url: 'remote.php?remoteSection=orders&w=viewGiftWrappingDetails&orderprodid=".$pRow['orderprodid']."'}); return false;\">".GetLang('ViewDetails')."</a>]";
						$giftOptions .= "</div></td></tr>";
					}

					$prodFields= '';
					if(isset($prodFieldsArray[$pRow['orderprodid']])) {
						$prodFields = $this->GetOrderProductsFieldsRow($prodFieldsArray[$pRow['orderprodid']]);
					}

					$eventDate='';
					if ($pRow['ordprodeventdate'] != null) {
						$eventDate = '<tr><td style="padding:5px 0px 5px 15px;">'.$pRow['ordprodeventname'] . ': ' . isc_date('jS M Y', $pRow['ordprodeventdate']) . '</tr>';
					}

					$itemDetails = '';
					if($shippedLabel || $refunded) {
						$itemDetails = "<tr><td class='text' colspan='2' style='padding-left: 20px;'>";
						$itemDetails .= $shippedLabel.$refunded;
						$itemDetails .= '</td></tr>';
					}

					$GLOBALS['ProductsTable'] .= "
						<tr>
							<td style=\"padding-left:12px; padding-top:5px\" width=\"70%\" class=\"text\">".$sStart.$pRow['ordprodqty']." x ".$pRow['ordprodname'].$sEnd.$sku.$pOptions."</td>
							<td class=\"text\" width=\"30%%\" align=\"right\">".FormatPriceInCurrency($cost, $row['orddefaultcurrencyid'])."</td>
						</tr>
						".$giftOptions.$eventDate.$prodFields.$itemDetails."
					";

					$productAmounts[$pRow['orderprodid']] = $pRow['ordprodoriginalcost'] * $pRow['ordprodqty'];
				}

				$GLOBALS['ProductsTable'] .= "<tr><td colspan='2'><hr noshade size='1'></td></tr>";

				$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>%s</td></tr>", GetLang('SubTotal'), FormatPriceInCurrency($row['ordsubtotal'], $row['orddefaultcurrencyid']));

				if($wrappingTotal > 0) {
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>%s</td></tr>", GetLang('GiftWrapping'), FormatPriceInCurrency($wrappingTotal, $row['orddefaultcurrencyid']));
				}

				// Do we need to show a shipping cost?
				if($row['ordshipmethod'] != "" && $row['ordshipcost'] > 0) {
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>%s</td></tr>", GetLang('Shipping'), FormatPriceInCurrency($row['ordshipcost'], $row['orddefaultcurrencyid']));
				}

				// Do we need to show a handling fee?
				if($row['ordhandlingcost'] > 0) {
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>%s</td></tr>", GetLang('Handling'), FormatPriceInCurrency($row['ordhandlingcost'], $row['orddefaultcurrencyid']));
				}

				if ($row['orddateshipped'] > 0) {
					$GLOBALS['ShippingDate'] = isc_date(GetConfig('DisplayDateFormat'), $row['orddateshipped']);
				} else {
					$GLOBALS['ShippingDate'] = GetLang('NA');
				}

				// Do we need to show sales tax?
				if($row['ordtaxtotal'] > 0 && $row['ordtotalincludestax'] == 0) {
					if($row['ordtaxname']) {
						$taxName = isc_html_escape($row['ordtaxname']);
					}
					else {
						$taxName = GetLang('SalesTax');
					}
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>%s</td></tr>", $taxName, FormatPriceInCurrency($row['ordtaxtotal'], $row['orddefaultcurrencyid']));
				}

				// the stored total is minus the discount amount, so add it back on to make everything add up
				$orderTotal = $row['ordtotalamount'] + $row['orddiscountamount'];

				$adjustedTotal = $row['ordtotalamount'] - $row['ordgiftcertificateamount'] - $row['ordstorecreditamount'];

				$totalClass = "";
				if ($adjustedTotal == $row['ordtotalamount']) {
					$totalClass = "QuickTotal ";
				}

				$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='%stext' align='right'>%s:</td><td class='%stext' align='right'>%s</td></tr>", $totalClass, GetLang('Total'), $totalClass, FormatPriceInCurrency($orderTotal, $row['orddefaultcurrencyid']));

				// Do we need to show sales tax that was already included in the totals? We show it after the order total
				if($row['ordtaxtotal'] > 0 && $row['ordtotalincludestax'] == 1) {
					if($row['ordtaxname']) {
						$taxName = isc_html_escape($row['ordtaxname']);
					}
					else {
						$taxName = GetLang('SalesTax');
					}
					$taxName .= ' '.GetLang('IncludedInTotal');
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>%s</td></tr>", $taxName, FormatPrice($row['ordtaxtotal']));
				}

				// any gift certificates to show?
				if ($row['ordgiftcertificateamount'] > 0) {
					$query = "
						SELECT
							gch.*,
							gc.giftcertcode
						FROM
							[|PREFIX|]gift_certificate_history gch
							LEFT JOIN [|PREFIX|]gift_certificates gc ON gc.giftcertid = gch.histgiftcertid
						WHERE
							gch.historderid = '" . $orderId . "'";

					$gresult = $GLOBALS['ISC_CLASS_DB']->Query($query);
					while ($grow = $GLOBALS['ISC_CLASS_DB']->Fetch($gresult)) {
						// output the gift cert. row
						$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>-%s</td></tr>", GetLang('GiftCertificate') . " (" . $grow['giftcertcode'] . ")", FormatPriceInCurrency($grow['histbalanceused'], $row['orddefaultcurrencyid']));
					}
				}

				// store credit used?
				if ($row['ordstorecreditamount'] > 0) {
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>-%s</td></tr>", GetLang('StoreCredit'), FormatPriceInCurrency($row['ordstorecreditamount'], $row['orddefaultcurrencyid']));
				}

				// discount amount (from discount rules)
				if ($row['orddiscountamount'] > 0) {
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>-%s</td></tr>", GetLang('DiscountAmount'), FormatPriceInCurrency($row['orddiscountamount'], $row['orddefaultcurrencyid']));
				}

				//show adjusted row?
				if ($adjustedTotal != $orderTotal) {
					$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='QuickTotal text' align='right'>%s:</td><td class='QuickTotal text' align='right'>%s</td></tr>", GetLang('AdjustedTotal'), FormatPriceInCurrency($adjustedTotal, $row['orddefaultcurrencyid']));
				}

				// any coupons in this order?
				$query = "SELECT * FROM [|PREFIX|]order_coupons WHERE ordcouporderid = '" . $orderId . "'";
				$cresult = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$coupons = array();
				while ($crow = $GLOBALS['ISC_CLASS_DB']->Fetch($cresult)) {
					//calculate the amount this coupon is worth for the product
					if ($crow['ordcoupontype'] == 1) { // percent type
						$amount = $productAmounts[$crow['ordcoupprodid']] * $crow['ordcouponamount'] / 100;
					}
					else { //fixed amount
						$amount = $crow['ordcouponamount'];
					}

					if (isset($coupons['ordcouponid'])) {
						$coupons['ordcouponid']['amount'] += $amount;
					}
					else {
						$coupons['ordcouponid'] = array(
							'code' => $crow['ordcouponcode'],
							'amount' => $amount
						);
					}
				}

				if (count($coupons)) {
					foreach ($coupons as $coupon) {
						// output the coupon code row
						$GLOBALS['ProductsTable'] .= sprintf("<tr><td height='18' class='text' align='right'>%s:</td><td class='text' align='right'>-%s</td></tr>", GetLang('CouponCode') . " (" . $coupon['code'] . ")", FormatPriceInCurrency($coupon['amount'], $row['orddefaultcurrencyid']));
					}
				}

				if (isset($row['ordpaymentstatus'])) {
					if ($row['ordpaymentstatus'] == 'refunded' || $row['ordpaymentstatus'] == 'partially refunded') {
						$GLOBALS['ProductsTable'] .= '<tr><td class="text" align="right" height="18">'.GetLang('Refunded').':</td><td class="text" align="right">'.FormatPriceInCurrency($row['ordrefundedamount'], $row['orddefaultcurrencyid']).'</td></tr>';
					}
				}

				$GLOBALS['ProductsTable'] .= "</table>";

				$GLOBALS['OrderComments'] = '';
				if (trim($row['ordcustmessage']) != '') {
					$GLOBALS['OrderComments'] = nl2br(isc_html_escape($row['ordcustmessage']));
				}
				else {
					$GLOBALS['HideOrderComments'] = 'display: none';
				}

				/**
				 * Order form field
				 */
				$GLOBALS['HideBillingFormFields'] = '';
				$GLOBALS['HideShippingFormFields'] = '';
				$GLOBALS['BillingFormFields'] = '';
				$GLOBALS['ShippingFormFields'] = '';

				$billingFields = array();
				$shippingFields = array();

				if (gzte11(ISC_MEDIUMPRINT) && isId($row['ordformsessionid'])) {
					$billingFields = $GLOBALS['ISC_CLASS_FORM']->getSavedSessionData($row['ordformsessionid'], array(), FORMFIELDS_FORM_BILLING, true);
					$shippingFields = $GLOBALS['ISC_CLASS_FORM']->getSavedSessionData($row['ordformsessionid'], array(), FORMFIELDS_FORM_SHIPPING, true);
				}

				/**
				 * Do we have the correct version?
				 */
				if (!gzte11(ISC_MEDIUMPRINT)) {
					$GLOBALS['HideBillingFormFields'] = 'none';
					$GLOBALS['HideShippingFormFields'] = 'none';

				/**
				 * OK, we're allow to
				 */
				} else {

					/**
					 * Lets do the billing first. Do we have any?
					 */
					if (empty($billingFields)) {
						$GLOBALS['HideBillingFormFields'] = 'none';
					} else {
						$GLOBALS['BillingFormFields'] = $this->buildOrderFormFields($billingFields);
					}

					/**
					 * Now the shipping
					 */
					if (empty($billingFields)) {
						$GLOBALS['HideShippingFormFields'] = 'none';
					} else {
						$GLOBALS['ShippingFormFields'] = $this->buildOrderFormFields($shippingFields);
					}
				}

				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("order.quickview");
				$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
			} else {
				echo GetLang('OrderDetailsNotFound');
			}
		}

		private function GetMultiCountryStates()
		{
			echo GetMultiCountryStateOptions(array((int)$_REQUEST['c']));
			exit;
		}

		/**
		 * Display a list of states for a given country
		 *
		 * @return void
		 **/
		private function GetCountryStates()
		{
			$country = $_REQUEST['c'];
			if(IsId($country)) {
				$countryId = $country;
			}
			else {
				$countryId = GetCountryIdByName($country);
			}

			if(isset($_REQUEST['s']) && GetStateByName($_REQUEST['s'], $countryId)) {
				$state = $_REQUEST['s'];
			}
			else {
				$state = '';
			}

			if(isset($_REQUEST['format']) && $_REQUEST['format'] == 'options') {
				echo GetStateListAsOptions($country, $state, false, '', '', false, true);
			}
			else {
				echo GetStateList((int)$country);
			}
		}

		/**
		 * Display a summary of all the orders for a given customer
		 *
		 * @return void
		 **/
		private function GetCustomerOrders()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('customers');

			$custId = (int) $_REQUEST['c'];

			// Get the details for the orders from the database
			$query = "
				SELECT o.*, c.custconemail
				FROM [|PREFIX|]orders o
				LEFT JOIN [|PREFIX|]customers c ON (c.customerid=o.ordcustid)
				WHERE ordcustid='".(int)$custId."' AND ordstatus != 0
			";
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$query .= " AND ordvendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
			}
			$query .= "ORDER BY orderid DESC";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				// Output the details of the order
				$GLOBALS['OrderId'] = (int) $row['orderid'];
				$GLOBALS['OrderStatus'] = GetOrderStatusById($row['ordstatus']);
				$GLOBALS['OrderTotal'] = FormatPrice($row['ordtotalamount']);
				$GLOBALS['OrderDate'] = CDate($row['orddate']);
				$GLOBALS['OrderViewLink'] = '<a href="#" onclick="viewOrderNotes(' . $row['orderid'] . '); return false;">' . GetLang('CustomerOrderListNotesLink') . '</a>';

				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("customer.quickorder");
				$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

				// The email is used by the view all orders button
				$GLOBALS['Email'] = isc_html_escape($row['custconemail']);
				$GLOBALS['CustomerId'] = $row['ordcustid'];
			}
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("customer.quickorderall");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		 * Update the order status of a specific order from the manage orders page
		 *
		 * @return void
		 **/
		private function UpdateOrderStatus()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('orders');

			if(isset($_REQUEST['o']) && isset($_REQUEST['s'])) {
				$order_id = (int)$_REQUEST['o'];
				$status = (int)$_REQUEST['s'];

				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					echo 0;
					exit;
				}

				if (UpdateOrderStatus($order_id, $status)) {
					echo 1;
				} else {
					echo 0;
				}
			}
			else {
				echo 0;
			}

			exit;
		}

		/**
		 * Update the tracking number of an order from the manage orders page
		 *
		 * @return void
		 **/
		private function UpdateTrackingNo()
		{
			if(isset($_REQUEST['o']) && isset($_REQUEST['tn'])) {
				$order_id = (int)$_REQUEST['o'];

				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					echo 0;
					exit;
				}

				$trackingno = $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['tn']);

				$updatedOrder = array(
					"ordtrackingno" => $_REQUEST['tn'],
				);

				if ($GLOBALS['ISC_CLASS_DB']->UpdateQuery("orders", $updatedOrder, "orderid='".$GLOBALS['ISC_CLASS_DB']->Quote($order_id)."'")) {
					echo "1";
				} else {
					echo "0";
				}

				// If the checkout module that was used for an order is still enabled and has a function
				// to handle a status change, then call that function
				$valid_checkout_modules = GetAvailableModules('checkout', true, true);
				$valid_checkout_module_ids = array();
				foreach ($valid_checkout_modules as $valid_module) {
					$valid_checkout_module_ids[] = $valid_module['id'];
				}

				$query = "SELECT *
				FROM [|PREFIX|]orders
				WHERE orderid = '".$GLOBALS['ISC_CLASS_DB']->Quote($order_id)."'";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$order = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

				if (in_array($order['orderpaymentmodule'], $valid_checkout_module_ids)) {
					GetModuleById('checkout', $checkout_module, $order['orderpaymentmodule']);
					if (method_exists($checkout_module, 'HandleUpdateTrackingNum')) {
						call_user_func(array($checkout_module, 'HandleUpdateTrackingNum'), $order_id, $trackingno);
					}
				}

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($order_id, $trackingno);
			}
		}

		/**
		*	Update the inventory levels for a product that has no options
		*/
		private function UpdatePerProductInventoryLevels()
		{
			if(!gzte11(ISC_MEDIUMPRINT)) {
				echo 0;
				exit;
			}

			if(isset($_REQUEST['p']) && isset($_REQUEST['c']) && isset($_REQUEST['l'])) {
				$product_id = (int)$_REQUEST['p'];
				$current_stock_level = (int)$_REQUEST['c'];
				$low_stock_level = (int)$_REQUEST['l'];

				$updatedProduct = array(
					"prodcurrentinv" => $current_stock_level,
					"prodlowinv" => $low_stock_level,
					"prodlastmodified" => time()
				);
				if($GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updatedProduct, "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($product_id)."'")) {
					$query = sprintf("SELECT prodname FROM [|PREFIX|]products WHERE productid='%d'", $product_id);
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					$prodName = $GLOBALS['ISC_CLASS_DB']->FetchOne($query);

					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($product_id, $product_id, $current_stock_level, $low_stock_level);

					echo "1";
				}
				else {
					echo "0";
				}
			}
		}

		/**
		*	Update the inventory levels for a product that has options
		*/
		private function UpdatePerOptionInventoryLevels()
		{
			if(!gzte11(ISC_MEDIUMPRINT)) {
				echo 0;
				exit;
			}

			if(isset($_REQUEST['i'])) {
				$inventory_data = $_REQUEST['i'];
				$inventory_levels = array();
				$queries = array();
				$done = array();
				$total_stock_units = 0;
				$total_low_units = 0;
				$product_id = 0;

				parse_str($inventory_data, $inv_array);

				// Execute all of the queries in a transaction
				$GLOBALS['ISC_CLASS_DB']->Query("start transaction");

				foreach($inv_array as $k=>$v) {
					$tmp = explode("_", $k);
					$id = (int)$tmp[count($tmp)-1];
					$inventory_levels[$id] = array();

					if(!in_array($id, $done)) {
						$product_id = (int)$tmp[count($tmp)-2];
						$current = (int)$inv_array["stock_level_" . $product_id . "_" . $id];
						$low = (int)$inv_array["stock_level_notify_" . $product_id . "_" . $id];

						$updatedLevels = array(
							"vcstock" => $current,
							"vclowstock" => $low
						);
						$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_variation_combinations", $updatedLevels, "combinationid='".$GLOBALS['ISC_CLASS_DB']->Quote((int)$id)."'");

						// Increment the number of total units in stock
						$total_stock_units += $current;
						$total_low_units += $low;

						// Mark this particular product option as done
						array_push($done, $id);
					}
				}

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($product_id, $product_id, $total_stock_units, $total_low_units);

				// Finally we need to update the prodcurrentinv field in the products table
				$updatedProduct = array(
					"prodcurrentinv" => $total_stock_units,
					"prodlastmodified" => time()
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updatedProduct, "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($product_id)."'");

				$err = $GLOBALS['ISC_CLASS_DB']->GetErrorMsg();
				if($err == "") {
					// No error, commit the transaction
					$GLOBALS['ISC_CLASS_DB']->Query("commit");
					echo "1";
				} else {
					// Something went wrong, rollback
					$GLOBALS['ISC_CLASS_DB']->Query("rollback");
					echo "0";
				}
			}
		}

		/**
		 * Checks if the user entered FTP settings are valid or not.
		*/
		private function TestFTPSettings()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('settings');

			if (!function_exists("ftp_connect")) {
				exit;
			}

			@list($host, $port) = @explode(":", $_POST['host']);
			if(!$host) {
				exit;
			}
			if(!$port) {
				$port = 21;
			}
			if(!isset($_POST['user']) || !isset($_POST['pass'])) {
				exit;
			}

			$ftpcon = @ftp_connect($host, $port, 10);
			if(!$ftpcon) {
				echo sprintf('alert("%s"); $("#BackupsRemoteFTPHost").focus(); $("#BackupsRemoteFTPHost").select();', GetLang('BackupFTPBadServer'));
				exit;
			}

			$login = @ftp_login($ftpcon, $_POST['user'], $_POST['pass']);
			if(!$login) {
				echo sprintf('alert("%s"); $("#BackupsRemoteFTPUser").focus(); $("#BackupsRemoteFTPUser").select();', GetLang('BackupFTPBadUser'));
				exit;
			}

			if(isset($_POST['path']) && $_POST['path'] != "" && !@ftp_chdir($ftpcon, $_POST['path'])) {
				echo sprintf('alert("%s"); $("#BackupsRemoteFTPPath").focus(); $("#BackupsRemoteFTPPath").select();', GetLang('BackupFTPBadPath'));
				exit;
			}

			// All is well, let the user know
			echo sprintf('alert("%s");', GetLang('BackupFTPSuccess'));
			exit;
		}

		/**
		* Return a list of text custom fields for a E-mail Marketer mailing list
		*/
		private function GetTextCustomFieldsForMailingList()
		{
			if(isset($_REQUEST['list'])) {
				$list = $_REQUEST['list'];
				$GLOBALS['ISC_CLASS_ADMIN_SENDSTUDIO'] = GetClass('ISC_ADMIN_SENDSTUDIO');
				echo $GLOBALS['ISC_CLASS_ADMIN_SENDSTUDIO']->GetAvailableCustomFieldsForList($list, "text");
			}
		}

		/**
		* Return a list of custom fields for a E-mail Marketer mailing list
		*/
		private function GetCustomFieldsForMailingList()
		{
			if(isset($_REQUEST['list'])) {
				$list = $_REQUEST['list'];
				$GLOBALS['ISC_CLASS_ADMIN_SENDSTUDIO'] = GetClass('ISC_ADMIN_SENDSTUDIO');
				echo $GLOBALS['ISC_CLASS_ADMIN_SENDSTUDIO']->GetAvailableCustomFieldsForList($list);
			}
		}

		/**
		* Generate a new API key for the XML API
		*/
		private function GenerateNewAPIKey()
		{
			echo isc_html_escape(sha1(rand(1, 65535) . time() . microtime()));
		}

		/**
		* CheckAddonKey
		* Check if a valid addon key has been specified when trying to download an addon
		*/
		private function CheckAddonKey()
		{
			$url = GetConfig('AddonLicenseURL') . '?key=' . str_replace("+", "%2B", urlencode($_REQUEST['key'])) .'&h='.base64_encode(urlencode($_SERVER['HTTP_HOST']));
			$result = PostToRemoteFileAndGetResponse($url);
			echo $result;
		}

		/**
		* DownloadAddonZip
		* Download the zip file for the license and extract it
		*
		* @return Void
		*/
		private function DownloadAddonZip()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('addons');

			if(!isset($_REQUEST['key']) && !isset($_REQUEST['prodId'])) {
				exit;
			}

			if(isset($_REQUEST['prodId'])) {
				$url = GetConfig('AddonStreamURL') . '?prodId='.(int)$_REQUEST['prodId'];
			}
			else {
				$key = $_REQUEST['key'];
				$url = GetConfig('AddonStreamURL') . '?key=' . str_replace("+", "%2B", urlencode($key)) .'&h='.base64_encode(urlencode($_SERVER['HTTP_HOST']));
			}

			$zip = PostToRemoteFileAndGetResponse($url);
			if(strlen($zip) > 0) {
				// Save the zip file to a temporary file in the cache folder which is writable
				$cache_path = realpath(ISC_BASE_PATH."/cache/");
				if(is_writable($cache_path)) {
					$temp_file = $cache_path . "/addon_" . rand(1, 100000) . ".zip";

					if($fp = fopen($temp_file, "wb")) {
						if(fwrite($fp, $zip)) {
							fclose($fp);

							// Is the addons folder writable?
							$addon_path = realpath(ISC_BASE_PATH."/addons/");

							if(is_writable($addon_path)) {
								// Try to extract the zip to the addons folder
								Getlib('class.zip');

								$archive = new PclZip($temp_file);
								if($archive->extract(PCLZIP_OPT_PATH, $addon_path) == 0) {
									// The unzipping failed
									echo GetLang("AddonUnzipFailed");
								}
								else {
									// The unzip was successful
									echo "success";
									$GLOBALS['ISC_CLASS_LOG']->LogAdminAction();
								}

								// Remove the temporary zip file
								unlink($temp_file);
							}
							else {
								echo GetLang("AddonFolderNotWritable");
							}
						}
						else {
							echo GetLang("AddonTempFolderNotWritable");
						}
					}
					else {
						echo GetLang("AddonTempFolderNotWritable");
					}
				}
				else {
					echo GetLang("AddonTempFolderNotWritable");
				}
			}
			else {
				echo GetLang("AddonDownloadZipFailed");
			}
		}

		/**
		 * Get the edit email template wysiwyg under store design
		 *
		 * @return void
		 **/
		private function GetEmailTemplate()
		{
			if(empty($_REQUEST['file']) || empty($_REQUEST['id'])) {
				exit;
			}

			$templateDirectories = GetClass('ISC_ADMIN_LAYOUT')->GetEmailTemplateDirectories();
			$validTemplate = false;
			foreach(array_reverse($templateDirectories) as $directory) {
				$path = realpath($directory.'/'.$_REQUEST['file']);

				//replace \  with / for windows servers so strpos would work,
				$path = str_replace('\\', '/',$path);
				$directory  = str_replace('\\', '/',$directory);

				if($path && is_file($path) && strpos($path, $directory) !== false) {
					$validTemplate = true;
					break;
				}
			}

			if(!$validTemplate) {
				exit;
			}

			$fileData = file_get_contents($path);
			$wysiwygOptions = array(
				'id'			=> 'wysiwyg_'.isc_html_escape($_REQUEST['id']),
				'width'			=> '100%',
				'height'		=> '500px',
				'value'			=> $fileData,
				'editorOnly'	=> true,
				'delayLoad'		=> true,
			);
			$editor = GetClass('ISC_ADMIN_EDITOR')->GetWysiwygEditor($wysiwygOptions);
			echo "<div style='margin:10px'>" . $editor . "</div>";
		}

		/**
		 * Update an email template that has been edited via the store design section of the control panel
		 *
		 * @return void
		 * @author /bin/bash: niutil: command not found
		 **/
		private function UpdateEmailTemplate()
		{
			if(empty($_REQUEST['file']) || !isset($_REQUEST['html'])) {
				exit;
			}

			$templateDirectories = GetClass('ISC_ADMIN_LAYOUT')->GetEmailTemplateDirectories();
			$templateDirectory = array_pop($templateDirectories);

			$fullPath = $templateDirectory.'/'.$_REQUEST['file'];
			if(strpos($fullPath, $templateDirectory) === false) {
				exit;
			}
			$parentDirectory = dirname($fullPath);
			// Attempt to create the directory structure for this template. If we can't, exit
			if(!is_dir($parentDirectory) && !@mkdir($parentDirectory, ISC_WRITEABLE_DIR_PERM, true)) {
				exit;
			}

			if(!file_put_contents($fullPath, $_REQUEST['html'])) {
				exit;
			}

			echo 'success';
		}

		/**
		* GetVariationCombinationsTable
		* Get a list of option combinations and return them as a table
		*
		* @return Void
		*/
		private function GetVariationCombinationsTable($options = array(), $return = false)
		{
			$productId = 0;
			$productHash = '';
			$prodIdorHash = '';
			$vid = 0;
			$inv = 0;
			$currentVariationId = 0;

			if(isset($_GET['v']) && is_numeric($_GET['v']) && isset($_GET['inv']) && is_numeric($_GET['inv'])) {
				$vid = (int)$_GET['v'];
				$inv = (bool)$_GET['inv'];
			}
			else {
				die;
			}

			if (isset($_GET['productId'])) {
				$productId = (int)$_GET['productId'];
				$prodIdorHash = $productId;
			}

			if (!empty($_GET['productHash'])) {
				$productHash = $_GET['productHash'];
				$prodIdorHash = $GLOBALS['ISC_CLASS_DB']->Quote($productHash);
			}

			if (isId($productId)) {
				$query = 'SELECT prodvariationid FROM [|PREFIX|]products WHERE productid = ' . $productId;
				$res = $GLOBALS['ISC_CLASS_DB']->Query($query);
				if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($res)) {
					$currentVariationId = $row['prodvariationid'];
				}
			}

			$prodId = $productId;

			// a different variation was selected
			if ($vid != $currentVariationId) {
				// cleanup the combinations table of old temp records
				$query = "DELETE FROM [|PREFIX|]product_variation_combinations WHERE vcproductid = 0 AND vcproducthash = '" . $prodIdorHash . "' AND vcvariationid != " . $vid;
				$GLOBALS['ISC_CLASS_DB']->Query($query);

				if ($vid > 0) {
					// any temp combinations left? if there is then we shouldn't generate new ones
					$query = "SELECT COUNT(*) AS combocount FROM [|PREFIX|]product_variation_combinations WHERE vcproductid = 0 AND vcproducthash = '" . $prodIdorHash . "'";
					$res = $GLOBALS['ISC_CLASS_DB']->Query($query);
					$count = $GLOBALS['ISC_CLASS_DB']->FetchOne($res);

					if ($count == 0) {
						$optionIds = array();
						// get all of the variation options
						$query = sprintf("SELECT * FROM [|PREFIX|]product_variation_options WHERE vovariationid='%d' ORDER BY vooptionsort, vovaluesort", $vid);
						$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

						while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
							$optionIds[$row['voname']][] = $row['voptionid'];
						}

						// generate temporary combination records
						GetClass('ISC_ADMIN_PRODUCT')->SaveCombinations('', $optionIds, $prodIdorHash, $vid, true);
					}

					$prodId = 0;
					if ($productHash == '') {
						$productHash = $productId;
					}
				}
			}

			$filterOptions = array();
			if (!empty($options)) {
				$filterOptions = $options;
			}
			elseif (isset($_POST['filterOption'])) {
				$filterOptions = $_POST['filterOption'];
			}

			$html = GetClass('ISC_ADMIN_PRODUCT')->_LoadVariationCombinationsTable($vid, $inv, $productId, $productHash, $filterOptions);
			if ($return) {
				return $html;
			}

			echo $html;
			die;
		}

		private function BulkUpdateVariations()
		{
			$productId = 0;
			$vid = 0;
			$inv = 0;
			$useHash = false;

			if(isset($_GET['v']) && is_numeric($_GET['v']) && isset($_GET['inv']) && is_numeric($_GET['inv'])) {
				$vid = (int)$_GET['v'];
				$inv = (bool)$_GET['inv'];
			}

			if (isset($_GET['productId'])) {
				$productId = (int)$_GET['productId'];
			}

			if (isId($productId)) {
				$query = 'SELECT prodvariationid FROM [|PREFIX|]products WHERE productid = ' . $productId;
				$res = $GLOBALS['ISC_CLASS_DB']->Query($query);
				if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($res)) {
					if ($row['prodvariationid'] != $vid) {
						$useHash = true;
					}
				}
			}

			if (!empty($_GET['productHash'])) {
				$useHash = true;
				$productId = $GLOBALS['ISC_CLASS_DB']->Quote($_GET['productHash']);
			}

			if ($useHash) {
				$whereSQL = "vcproductid = 0 AND vcproducthash = '" . $productId . "' ";
			}
			else {
				$whereSQL = 'vcproductid = ' . $productId . ' ';
			}

			$filterOptions = array();
			if (isset($_GET['filterOptions'])) {
				parse_str($_GET['filterOptions'], $filterOptions);
			}

			// create the sql to update the filtered options
			$optionSQL = '';
			if (!empty($filterOptions)) {
				foreach ($filterOptions as $optionName => $optionValues) {
					$thisOptionSQL = '';
					foreach ($optionValues as $value) {
						if ($value == 'all') {
							continue;
						}

						if ($thisOptionSQL) {
							$thisOptionSQL .= ' OR ';
						}
						$thisOptionSQL .= "CONCAT(',', vcoptionids, ',') LIKE '%," . $value . ",%'";
					}

					if ($thisOptionSQL) {
						if ($optionSQL) {
							$optionSQL .= " AND ";
						}

						$optionSQL .= "(" . $thisOptionSQL . ")";
					}
				}
			}

			if ($optionSQL != '') {
				$optionSQL = ' AND ' . $optionSQL;
			}

			$updates = array();
			switch ($_GET['updatePurchaseable']) {
				case "reset":
				case "yes":
					$updates[] = "vcenabled = '1'";
					break;
				case "no":
					$updates[] = "vcenabled = '0'";
					break;
			}

			switch ($_GET['updatePriceDiff']) {
				case "reset":
					$updates[] = "vcpricediff = ''";
					$updates[] = "vcprice = 0";
					break;
				case "add":
				case "subtract":
				case "fixed":
					$updates[] = "vcpricediff = '" . $_GET['updatePriceDiff'] . "'";
					$updates[] = "vcprice = " . (float)$_GET['updatePrice'];
					break;
			}

			switch ($_GET['updateWeightDiff']) {
				case "reset":
					$updates[] = "vcweightdiff = ''";
					$updates[] = "vcweight = 0";
					break;
				case "add":
				case "subtract":
				case "fixed":
					$updates[] = "vcweightdiff = '" . $_GET['updateWeightDiff'] . "'";
					$updates[] = "vcweight = " . (float)$_GET['updateWeight'];
					break;
			}

			if ($inv) {
				if ($_GET['updateStockLevel'] != '') {
					$updates[] = 'vcstock = ' . (int)$_GET['updateStockLevel'];
				}

				if ($_GET['updateLowStockLevel'] != '') {
					$updates[] = 'vclowstock = ' . (int)$_GET['updateLowStockLevel'];
				}
			}

			// delete existing images?
			if (isset($_GET['updateDelImages'])) {
				// get distinct images not associated with variations that aren't in the current filter
				$query = '
					SELECT
						vcimagezoom,
						vcimagestd,
						vcimagethumb
					FROM
						[|PREFIX|]product_variation_combinations pvc
					WHERE
						' . $whereSQL .
						$optionSQL . '
					GROUP BY
						vcimagezoom
					HAVING
						COUNT(*) = (
									SELECT
										COUNT(*)
									FROM
										[|PREFIX|]product_variation_combinations pvc2
									WHERE
										pvc2.vcproductid = pvc.vcproductid AND
										pvc2.vcimagezoom = pvc.vcimagezoom
									)
				';

				$res = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($res)) {
					GetClass('ISC_ADMIN_PRODUCT')->DeleteVariationImagesForRow($row);
				}

				$updates[] = "vcimage = ''";
				$updates[] = "vcimagezoom = ''";
				$updates[] = "vcimagestd = ''";
				$updates[] = "vcimagethumb = ''";
			}
			// import image
			elseif (isset($_FILES['updateImage'])) {
				try {
					$image = ISC_PRODUCT_IMAGE::importImage($_FILES['updateImage']['tmp_name'], $_FILES['updateImage']['name'], false, false, true, false);

					$zoom = $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, false);
					$standard = $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, false);
					$thumb = $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true, false);

					$updates[] = "vcimage = '" . $image->getSourceFilePath() . "'";
					$updates[] = "vcimagezoom = '" . $zoom . "'";
					$updates[] = "vcimagestd = '" . $standard . "'";
					$updates[] = "vcimagethumb = '" . $thumb . "'";
				}
				catch (Exception $ex) {

				}
			}

			if (!empty($updates)) {
				$updates[] = "vclastmodified = " . time();

				$updateSQL = implode(', ', $updates);

				// update the combinations
				$query = 'UPDATE [|PREFIX|]product_variation_combinations SET ' . $updateSQL . ' WHERE ' . $whereSQL . $optionSQL;
				$GLOBALS['ISC_CLASS_DB']->Query($query);
			}

			// regenerate the combinations table to get fresh data
			$html = $this->GetVariationCombinationsTable($filterOptions, true);
			$response['tableData'] = $html;
			echo '<textarea>'.isc_json_encode($response).'</textarea>';
			exit;
		}

		/**
		* Used by add/edit a product to add a file for a digital download from the import directory
		*
		* @return string Either "success" or "failure"
		*/
		private function UseProductServerFile()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('products');

			$tags = array();
			$err = false;

			// The file has to be a valid import file
			$p = GetClass('ISC_ADMIN_PRODUCT');
			$valid_files = $p->_GetImportFilesArray();
			if (!in_array($_REQUEST['serverfile'], $valid_files)) {
				$err = GetLang('InvalidFileName');
			}

			if ($err === false && $p->SaveProductDownload($err)) {
				$_REQUEST['downdescription'] = urldecode($_REQUEST['downdescription']);

				if (isset($_REQUEST['productId'])) {
					$grid = $p->GetDownloadsGrid($_REQUEST['productId']);
				} else {
					$grid = $p->GetDownloadsGrid(0, $_REQUEST['productHash']);
				}

				$tags[] = $this->MakeXMLTag('status', 1);
				$tags[] = $this->MakeXMLTag('message', GetLang('ProductDownloadSaved'), true);
				$tags[] = $this->MakeXMLTag('grid', $grid, true);

			} else {
				$tags[] = $this->MakeXMLTag('status', 0);
				$tags[] = $this->MakeXMLTag('message', $err, true);
			}

			$this->SendXMLHeader();
			$this->SendXMLResponse($tags);
			die();
		}

		/**
		 * Get the exchange rate of a currency
		 *
		 * Method will call the selected currency application based in the currency converter ID $cid and return the exchange rate based on the currency code $ccode.
		 *
		 * @access public
		 * @return null
		 */
		public function GetExchangeRate()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('settings');

			if (!array_key_exists("cid", $_REQUEST)
				|| !GetModuleById("currency", $module, $_REQUEST['cid'])) {
				print "{'status':false, 'rate':null, 'message':'". GetLang("CurrencyProviderRequestUnavailable") . "'};";
				exit;
			}
			else if (!array_key_exists("ccode", $_REQUEST)) {
				print "{'status':false, 'rate':null, 'message':'". GetLang("ErrorEnterCurrencyCodeForConverter") . "'};";
				exit;
			}

			// Call Currency application and get the exchange rate
			if (($rate = $module->GetExchangeRateUsingBase($_REQUEST['ccode'])) === false) {
				$messages =$module->GetErrors();
				$message = implode(',', $messages);
				print "{'status':false, 'rate':null, 'message':'" . $message . "'};";
			}
			else {
				print "{'status':true, 'rate':'" . (string)$rate . "', 'message':'" . addslashes(sprintf(GetLang('CurrencyModuleSuccessMessage'), $rate)) . "'};";
			}

			exit;
		}

		/**
		 * Update the exchange rate of a currency
		 *
		 * Method will automatically update the exchange rate currency corresponding to the currency id $currencyid
		 *
		 * @access public
		 * @return null
		 */
		public function UpdateExchangeRate()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('settings');

			$currModules = explode(",", GetConfig("CurrencyMethods"));

			if (!isset($_REQUEST['cid']) || !isset($_REQUEST['currencyid'])) {
				print "{'id': " . ((int) $_REQUEST['currencyid']) . ", 'status':1, 'newRate':null, 'seq': " . ((int) $_REQUEST['seq']) . "};";
				exit;
			}

			$module = null;
			GetModuleById("currency", $module, $_REQUEST['cid']);

			if ($module === null || $module === false) {
				print "{'id': " . ((int) $_REQUEST['currencyid']) . ", 'status':1, 'newRate':null, 'seq': " . ((int) $_REQUEST['seq']) . "};";
				exit;
			}

			$query = "SELECT *
			FROM [|PREFIX|]currencies
			WHERE currencyid = '" . $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['currencyid'])."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if ($row == false) {
				print "{'id': " . ((int) $_REQUEST['currencyid']) . ", 'status':1, 'newRate':null, 'seq': " . ((int) $_REQUEST['seq']) . "};";
				exit;
			}

			$rate = $module->GetExchangeRateUsingBase($row['currencycode']);

			if ($rate === false) {
				$messages = $module->GetErrors();
				$message = $messages[0];
				if ($message == GetLang("CurrencyProviderRequestUnavailable")) {
					print "{'id': " . ((int) $_REQUEST['currencyid']) . ", 'status':1, 'newRate':null, 'seq': " . ((int) $_REQUEST['seq']) . "};";
				} else {
					print "{'id': " . ((int) $_REQUEST['currencyid']) . ", 'status':2, 'newRate':null, 'seq': " . ((int) $_REQUEST['seq']) . "};";
				}
			} else {
				$data = array();
				$data['currencyexchangerate'] = $rate;
				$data["currencylastupdated" ] = time();

				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("currencies", $data, "currencyid='".$GLOBALS['ISC_CLASS_DB']->Quote((int)$_REQUEST['currencyid'])."'");

				$GLOBALS['ISC_CLASS_DATA_STORE']->UpdateCurrencies();

				print "{'id': " . ((int) $_REQUEST['currencyid']) . ", 'status':0, 'newRate':'" . (string)FormatPrice($rate, false, true, false, $row, false) . "', 'seq': " . ((int) $_REQUEST['seq']) . "};";
			}
			exit;
		}

		private function GetStateList()
		{
			$remote = GetClass('ISC_REMOTE');
			return $remote->GetStateList();
		}

		private function buildOrderFormFields($widgetData)
		{
			if (!is_array($widgetData)) {
				return '';
			}

			$html = '';

			foreach ($widgetData as $data) {
				$data['label'] = trim($data['label']);
				$data['label'] = isc_html_escape($data['label']);

				if (substr($data['label'], -1) !== ':' && substr($data['label'], -1) !== '?') {
					$data['label'] .= ':';
				}

				if (is_array($data['value'])) {
					$data['value'] = array_map('isc_html_escape', $data['value']);
					$data['value'] = implode('<br />', $data['value']);
				} else {
					$data['value'] = isc_html_escape($data['value']);
				}

				$GLOBALS['FormFieldLabel'] = isc_html_escape($data['label']);
				$GLOBALS['FormFieldValue'] = $data['value'];

				$html .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderFormFields');
			}

			return $html;
		}

		/**
		 * Generate the contents of an email template directory to allow
		 * the user to edit one or more files in the directory.
		 */
		private function GetEmailTemplateDirectory()
		{
			if(!isset($_REQUEST['path']) || !isset($_REQUEST['parent']) || !$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Templates)) {
				exit;
			}

			echo GetClass('ISC_ADMIN_LAYOUT')->GetEmailTemplateRows($_REQUEST['path'], $_REQUEST['parent']);
		}
	}