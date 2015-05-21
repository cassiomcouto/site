<?php
	class ISC_ADMIN_ORDERS
	{
		protected $orderEntity;
		protected $customerEntity;

		protected $_customSearch = array();

		/**
		 * The constructor.
		 */
		public function __construct()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('orders');

			// Initialise custom searches functionality
			require_once(dirname(__FILE__).'/class.customsearch.php');
			$GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH'] = new ISC_ADMIN_CUSTOMSEARCH('orders');

			$this->orderEntity = new ISC_ENTITY_ORDER();
			$this->customerEntity = new ISC_ENTITY_CUSTOMER();
		}

		public function HandleToDo($Do)
		{
			$GLOBALS['BreadcrumEntries'] = array(
				GetLang('Home') => "index.php",
				GetLang('Orders') => 'index.php?ToDo=viewOrders'
			);

			switch (isc_strtolower($Do))
			{
				case 'saveneworder':
					$this->SaveNewOrder();
					break;
				case 'saveupdatedorder':
					$this->SaveUpdatedOrder();
					break;
				case "addorder":
					$this->AddOrder();
					break;
				case 'editorder':
					$this->EditOrder();
					break;
				case "createorderview":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['BreadcrumEntries'][GetLang('CreateOrderView')] = "index.php?ToDo=createOrderView";
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CreateView();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "printmultiorderinvoices":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$this->PrintMultiInvoices();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "deletecustomordersearch":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteCustomSearch();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "customordersearch":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['BreadcrumEntries'][GetLang('CustomView')] = "index.php?ToDo=customOrderSearch";
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CustomSearch();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "searchordersredirect":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['BreadcrumEntries'][GetLang('SearchResults')] = "index.php?ToDo=searchOrders";
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SearchOrdersRedirect();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "searchorders":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['BreadcrumEntries'][GetLang('SearchResults')] = "index.php?ToDo=searchOrders";
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SearchOrders();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "saveupdatedordermessage":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=saveUpdatedOrderMessage");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SavedUpdatedOrderMessage();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editordermessage":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=viewOrderMessages&orderId=" . @(int)$_GET['orderId'], GetLang('EditMessage') => "index.php?ToDo=editOrderMessage");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->EditOrderMessage();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "savenewordermessage":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=saveUpdatedOrderMessage");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SaveNewOrderMessage();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "createordermessage":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=viewOrderMessages&orderId=" . @(int)$_GET['orderId'], GetLang('CreateMessage') => "index.php?ToDo=createOrderMessage");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CreateOrderMessage();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "deleteordermessages":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=saveUpdatedOrderMessage");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteOrderMessages();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "flagordermessage":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=viewOrderMessages");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->FlagOrderMessage();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "updateordermessagestatus":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=viewOrderMessages");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->UpdateOrderMessageStatus();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "viewordermessages":
					if(!gzte11(ISC_LARGEPRINT)) {
						exit;
					}
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('ViewMessages') => "index.php?ToDo=viewOrderMessages");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->ViewOrderMessages();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "deleteorders":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Delete_Orders)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteOrders();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "printorderinvoice":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$this->PrintInvoice();
					} else {
						echo "<script type=\"text/javascript\">window.close();</script>";
					}
					break;
				case "importordertrackingnumbers":
					if(gzte11(ISC_MEDIUMPRINT)) {
						if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Import_Order_Tracking_Numbers)) {
							if (!gzte11(ISC_MEDIUMPRINT)) {
								exit;
							}
							$this->ImportTrackingNumbers();
						} else {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
						}
					}
					break;
				case "viewsingleorder":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$output = GetClass('ISC_ADMIN_REMOTE')->GetOrderQuickView();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						echo $output;
					}
					break;
				case "updatemultiorderstatus":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("pageheader.popup");
						$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
						$this->updateOrderStatusBox();
						$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("pagefooter.popup");
						$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "refundorder":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->RefundOrder();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				default:
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {

						if(isset($_GET['searchQuery'])) {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders", GetLang('SearchResults') => "index.php?ToDo=viewOrders");
						}
						else {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Orders') => "index.php?ToDo=viewOrders");
						}

						if (GetSession('ordersearch') > 0) {
							if (!isset($_GET['searchId'])) {
								$_GET['searchId'] = GetSession('ordersearch');
								$_REQUEST['searchId'] = GetSession('ordersearch');
							}

							if ($_GET['searchId'] > 0) {
								$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customOrderSearch"));
							}
						}

						if (!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						}
						if (GetSession('ordersearch') > 0) {
							$this->CustomSearch();
						} else {
							UnsetSession('ordersearch');
							$this->ManageOrders();
						}
						if (!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						}
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
			}
		}

		protected function DeleteOrders()
		{
			$queries = array();

			if(isset($_POST['orders'])) {
				// The orders will be removed from the following tables:
				//
				//     - Orders
				//     - Order_Products
				//	   - order_downloads

				// Start a transaction
				$GLOBALS['ISC_CLASS_DB']->Query("START TRANSACTION");

				// What we do here is feed the list of orders IDs in to a query with the vendor applied so that way
				// we're sure we're only deleting orders this user has permission to delete.
				$orderIds = implode("','", array_map('intval', $_POST['orders']));
				$vendorId = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
				if($vendorId > 0) {
					$query = "
						SELECT orderid
						FROM [|PREFIX|]orders
						WHERE orderid IN ('".$orderIds."') AND ordvendorid='".(int)$vendorId."'
					";
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					$orderIds = array(0);
					while($order = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						$orderIds[] = $order['orderid'];
					}
					$orderIds = implode("','", array_map('intval', $orderIds));
				}

				// Start deleting the orders
				$this->_DeleteOrderProductFields($orderIds);
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('orders', "WHERE orderid IN ('".$orderIds."')");
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_products', "WHERE orderorderid IN ('".$orderIds."')");
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_downloads', "WHERE orderid IN ('".$orderIds."')");
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_messages', "WHERE messageorderid IN ('".$orderIds."')");
				$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_coupons', "WHERE ordcouporderid IN ('".$orderIds."')");

				$err = $GLOBALS['ISC_CLASS_DB']->GetErrorMsg();

				if ($err != "") {
					$this->ManageOrders($err, MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_DB']->Query("COMMIT");

					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($orderIds));

					$this->ManageOrders(GetLang('OrdersDeletedSuccessfully'), MSG_SUCCESS);
				}
			} else {
				if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Orders)) {
					$this->ManageOrders();
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}
			}
		}

		protected function ManageOrdersGrid(&$numOrders)
		{
			// Show a list of products in a table
			$page = 0;
			$start = 0;
			$numPages = 0;
			$GLOBALS['OrderGrid'] = "";
			$GLOBALS['Nav'] = "";
			$GLOBALS['SmallNav'] = "";
			$catList = "";
			$max = 0;

			// Is this a custom search?
			if(isset($_GET['searchId'])) {
				$this->_customSearch = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->LoadSearch($_GET['searchId']);
				$_REQUEST = array_merge($_REQUEST, (array)$this->_customSearch['searchvars']);

				// Override custom search sort fields if we have a requested field
				if(isset($_GET['sortField'])) {
					$_REQUEST['sortField'] = $_GET['sortField'];
				}
				if(isset($_GET['sortOrder'])) {
					$_REQUEST['sortOrder'] = $_GET['sortOrder'];
				}
			}
			else if(isset($_GET['searchQuery'])) {
				$GLOBALS['QueryEscaped'] = isc_html_escape($_GET['searchQuery']);
			}

			if(isset($_REQUEST['sortOrder']) && $_REQUEST['sortOrder'] == "asc") {
				$sortOrder = "asc";
			}
			else {
				$sortOrder = "desc";
			}

			$validSortFields = array('orderid', 'custname', 'orddate', 'ordstatus', 'newmessages', 'ordtotalamount');
			if(isset($_REQUEST['sortField']) && in_array($_REQUEST['sortField'], $validSortFields)) {
				$sortField = $_REQUEST['sortField'];
				SaveDefaultSortField("ManageOrders", $_REQUEST['sortField'], $sortOrder);
			}
			else {
				list($sortField, $sortOrder) = GetDefaultSortField("ManageOrders", "orderid", $sortOrder);
			}

			if (isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			} else {
				$page = 1;
			}

			// Build the pagination and sort URL
			$searchURL = '';
			foreach($_GET as $k => $v) {
				if($k == "sortField" || $k == "sortOrder" || $k == "page" || $k == "new" || $k == "ToDo" || $k == "SubmitButton1" || !$v) {
					continue;
				}
				$searchURL .= sprintf("&%s=%s", $k, urlencode($v));
			}

			$sortURL = sprintf("%s&amp;sortField=%s&amp;sortOrder=%s", $searchURL, $sortField, $sortOrder);

			$GLOBALS['SortURL'] = $sortURL;

			// Limit the number of orders returned
			if ($page == 1) {
				$start = 1;
			} else {
				$start = ($page * ISC_ORDERS_PER_PAGE) - (ISC_ORDERS_PER_PAGE-1);
			}

			$start = $start-1;

			// Get the results for the query
			$orderResult = $this->_GetOrderList($start, $sortField, $sortOrder, $numOrders);

			$numPages = ceil($numOrders / ISC_ORDERS_PER_PAGE);

			// Add the "(Page x of n)" label
			if($numOrders > ISC_ORDERS_PER_PAGE) {
				$GLOBALS['Nav'] = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);

				$GLOBALS['Nav'] .= BuildPagination($numOrders, ISC_ORDERS_PER_PAGE, $page, sprintf("index.php?ToDo=viewOrders%s", $sortURL));
			}
			else {
				$GLOBALS['Nav'] = "";
			}

			if(isset($_GET['searchQuery'])) {
				$query = $_GET['searchQuery'];
			} else {
				$query = "";
			}

			$GLOBALS['Nav'] = rtrim($GLOBALS['Nav'], ' |');
			$GLOBALS['SmallNav'] = rtrim($GLOBALS['SmallNav'], ' |');

			$GLOBALS['SearchQuery'] = $query;
			$GLOBALS['SortField'] = $sortField;
			$GLOBALS['SortOrder'] = $sortOrder;

			$sortLinks = array(
				"Id" => "orderid",
				"Cust" => "custname",
				"Date" => "orddate",
				"Status" => "ordstatus",
				"Message" => "newmessages",
				"Total" => "ordtotalamount"
			);
			BuildAdminSortingLinks($sortLinks, "index.php?ToDo=viewOrders&amp;".$searchURL."&amp;page=".$page, $sortField, $sortOrder);

			// Workout the maximum size of the array
			$max = $start + ISC_ORDERS_PER_PAGE;

			if ($max > count($orderResult)) {
				$max = count($orderResult);
			}

			if(!gzte11(ISC_LARGEPRINT)) {
				$GLOBALS['HideOrderMessages'] = "none";
				$GLOBALS['CustomerNameSpan'] = 2;
			}

			// Display the orders
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($orderResult)) {
				$GLOBALS['OrderId'] = $row['orderid'];
				$GLOBALS['CustomerId'] = $row['ordcustid'];
				$GLOBALS['OrderId1'] = $row['orderid'];
				$GLOBALS['Customer'] = isc_html_escape($row['custname']);

				$GLOBALS['Date'] = isc_date(GetConfig('DisplayDateFormat'), $row['orddate']);
				$GLOBALS['OrderStatusOptions'] = $this->GetOrderStatusOptions($row['ordstatus']);

				$GLOBALS['Total'] = FormatPriceInCurrency($row['ordtotalamount'], $row['orddefaultcurrencyid'], null, true);
				$GLOBALS['TrackingNo'] = isc_html_escape($row['ordtrackingno']);

				$GLOBALS['NotesIcon'] = "";
				$GLOBALS['CommentsIcon'] = "";

				// Look up the country for the IP address of this order
				if(gzte11(ISC_LARGEPRINT)) {
					$suspiciousOrder = false;
					$GLOBALS['FlagCellClass'] = $GLOBALS['FlagCellTitle'] = '';
					if($row['ordgeoipcountrycode'] != '') {
						$flag = strtolower($row['ordgeoipcountrycode']);
						// If the GeoIP based country code and the billing country code don't match, we flag this order as a different colour
						if(strtolower($row['ordgeoipcountrycode']) != strtolower($row['ordbillcountrycode'])) {
							$GLOBALS['FlagCellClass'] = "Suspicious";
							$suspiciousOrder = true;

						}
						$countryName = $row['ordgeoipcountry'];
					}
					else {
						$flag = strtolower($row['ordbillcountrycode']);
						$countryName = $row['ordbillcountry'];
						$GLOBALS['FlagCellTitle'] = $row['ordbillcountry'];
					}
					// Do we have a country flag to show?
					if(file_exists(ISC_BASE_PATH."/lib/flags/".$flag.".gif")) {
						$flag = GetConfig('AppPath')."/lib/flags/".$flag.".gif";
						if($suspiciousOrder == true) {
							$title = sprintf(GetLang('OrderCountriesDontMatch'), $row['ordbillcountry'], $row['ordgeoipcountry']);
							$GLOBALS['OrderCountryFlag'] = "<span onmouseout=\"HideQuickHelp(this);\" onmouseover=\"ShowQuickHelp(this, '".GetLang('PossibleFraudulentOrder')."', '".$title."');\"><img src=\"".$flag."\" alt='' /></span>";
						}
						else {
							$GLOBALS['OrderCountryFlag'] = "<img src=\"".$flag."\" alt='' title=\"".$countryName."\" />";
						}
					}
					else {
						$GLOBALS['OrderCountryFlag'] = '';
					}
				}
				else {
					$GLOBALS['HideCountry'] = "none";
				}

				// Workout the message link -- do they have permission to view order messages?
				$GLOBALS["HideMessages"] = "none";

				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Order_Messages) && $row['ordcustid'] > 0) {
					$numMessages = GetLang('Messages');
					if($row['nummessages'] == 1) {
						$numMessages = GetLang('OrderMessage');
					}
					$newMessages = '0 '.GetLang('NewText');
					if($row['newmessages'] > 0) {
						$newMessages = "<strong>" . $row['newmessages'] . " " . GetLang('NewText') . "</strong>";
					}
					$GLOBALS['MessageLink'] = sprintf("<a title='%s' class='Action' href='index.php?ToDo=viewOrderMessages&amp;ord
					erId=%d'>%s %s</a><br />(%s)",
						GetLang('MessageOrder'),
						$row['orderid'],
						$row['nummessages'],
						$numMessages,
						$newMessages
					);

					if($row["numunreadmessages"] > 0 && gzte11(ISC_LARGEPRINT)) {
						$GLOBALS["HideMessages"] = "";
						$GLOBALS["NumMessages"] = $row['numunreadmessages'];
					}
				}
				else {
					$GLOBALS['MessageLink'] = sprintf("<a class='Action' disabled>%s (0)</a>", GetLang('Messages'));
				}

				if(!gzte11(ISC_LARGEPRINT)) {
					$GLOBALS["HideMessages"] = "none";
				}

				// If the customer still exists, link to the customer page
				if(trim($row['custname']) != '') {
					$GLOBALS['CustomerLink'] = "<a href='index.php?ToDo=viewCustomers&amp;idFrom=".$GLOBALS['CustomerId']."&idTo=".$GLOBALS['CustomerId']."'>".$GLOBALS['Customer']."</a>";
				}
				else {
					$GLOBALS['CustomerLink'] = $row['ordbillfirstname'].' '.$row['ordbilllastname'];
				}

				if($row['ordcustid'] == 0) {
					$GLOBALS['CustomerLink'] .= " <span style=\"color: gray;\">".GetLang('GuestCheckoutCustomer')."</span>";
				}

				// If the order has any notes, flag it
				if($row['ordnotes'] != '') {
					$GLOBALS['NotesIcon'] = '<a href="#" onclick="Order.HandleAction(' . $row['orderid'] . ', \'orderNotes\');"><img src="images/note.png" alt="" title="' . GetLang('OrderHasNotes') . '" style="border-style: none;" /></a>';
					$GLOBALS['HasNotesClass'] = 'HasNotes';
				}
				else {
					$GLOBALS['HasNotesClass'] = '';
				}

				// does the order have a customer message?
				if (!empty($row['ordcustmessage'])) {
					$GLOBALS['CommentsIcon'] = '<a href="#" onclick="Order.HandleAction(' . $row['orderid'] . ', \'orderNotes\');"><img src="images/user_comment.png" alt="" title="' . GetLang('OrderHasComments') . '" style="border-style: none;" /></a>';
				}

				// If the order has any shipable items, show the link to ship items
				$GLOBALS['ShipItemsLink'] = '';
				if (isset($row['ordtotalshipped']) && isset($row['ordtotalqty'])) {
					if($row['ordisdigital'] == 0 && ($row['ordtotalqty']-$row['ordtotalshipped']) > 0) {
						$GLOBALS['ShipItemsLink'] = '<option id="ShipItemsLink'.$row['orderid'].'"  value="shipItems">'.GetLang('ShipItems').'</option>';
					}
				}


				//Show payment status blow order status
				$GLOBALS['PaymentStatus'] = '';
				$GLOBALS['HidePaymentStatus'] = 'display:none;';
				$GLOBALS['PaymentStatusColor'] = '';
				if($row['ordpaymentstatus'] != '') {
					$GLOBALS['HidePaymentStatus'] = '';
					$GLOBALS['PaymentStatusColor'] = '';
					switch($row['ordpaymentstatus']) {
						case 'authorized':
							$GLOBALS['PaymentStatusColor'] = 'PaymentAuthorized';
							break;
						case 'captured':
							$GLOBALS['PaymentStatus'] = GetLang('Payment')." ".ucfirst($row['ordpaymentstatus']);
							$GLOBALS['PaymentStatusColor'] = 'PaymentCaptured';
							break;
						case 'refunded':
						case 'partially refunded':
						case 'voided':
							$GLOBALS['PaymentStatus'] = GetLang('Payment')." ".ucwords($row['ordpaymentstatus']);
							$GLOBALS['PaymentStatusColor'] = 'PaymentRefunded';
							break;
					}
				}


				// If the allow payment delayed capture, show the link to Delayed capture
				$GLOBALS['DelayedCaptureLink'] = '';
				$GLOBALS['VoidLink'] = '';
				$GLOBALS['RefundLink'] ='';
				$transactionId = trim($row['ordpayproviderid']);

				//if orginal transaction id exist and payment provider is currently enabled
				if($transactionId != '' && GetModuleById('checkout', $provider, $row['orderpaymentmodule']) && $provider->IsEnabled() && !gzte11(ISC_HUGEPRINT)) {
					//if the payment module allow delayed capture and the current payment status is authorized
					//display delay capture option
					if(method_exists($provider, "DelayedCapture") && $row['ordpaymentstatus'] == 'authorized') {
						$GLOBALS['DelayedCaptureLink'] = '<option value="delayedCapture">'.GetLang('CaptureFunds').'</option>';

						$GLOBALS['PaymentStatus'] .= '<a onclick="Order.DelayedCapture('.$row['orderid'].'); return false;" href="#">'.GetLang('CaptureFunds').'</a>';
					}

					//if the payment module allow void transaction and the current payment status is authorized
					//display void option
					if(method_exists($provider, "DoVoid") && $row['ordpaymentstatus'] == 'authorized') {
						$GLOBALS['VoidLink'] = '<option value="voidTransaction">'.GetLang('VoidTransaction').'</option>';
					}

					//if the payment module allow refund and the current payment status is authorized
					//display refund option
					if(method_exists($provider, "DoRefund") && ($row['ordpaymentstatus'] == 'captured' || $row['ordpaymentstatus'] == 'partially refunded')) {
						$GLOBALS['RefundLink'] = '<option value="refundOrder">'.GetLang('Refund').'</option>';
					}
				}

				$GLOBALS["OrderStatusText"] = GetOrderStatusById($row['ordstatus']);
				$GLOBALS['OrderStatusId'] = $row['ordstatus'];
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("order.manage.row");
				$GLOBALS['OrderGrid'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
			}

			// Close the GeoIP database if we used it
			if(isset($gi)) {
				geoip_close($gi);
			}

			// Hide the message box in templates/iphone/MessageBox.html if we're not searching
			if(!isset($_REQUEST["searchQuery"]) && isset($_REQUEST["page"])) {
				$GLOBALS["HideYellowMessage"] = "none";
			}

			$GLOBALS['CurrentPage'] = $page;

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("orders.manage.grid");
			return $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
		}

		protected function ManageOrders($MsgDesc = "", $MsgStatus = "")
		{
			$GLOBALS['HideClearResults'] = "none";
			$status = array();
			$num_custom_searches = 0;
			$numOrders = 0;

			// Fetch any results, place them in the data grid
			$GLOBALS['OrderDataGrid'] = $this->ManageOrdersGrid($numOrders);

			// Was this an ajax based sort? Return the table now
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
				echo $GLOBALS['OrderDataGrid'];
				return;
			}

			if(isset($_REQUEST['searchQuery']) || isset($_GET['searchId'])) {
				$GLOBALS['HideClearResults'] = "";
			}

			if(isset($this->_customSearch['searchname'])) {
				$GLOBALS['ViewName'] = $this->_customSearch['searchname'];
			}
			else {
				$GLOBALS['ViewName'] = GetLang('AllOrders');
				$GLOBALS['HideDeleteViewLink'] = "none";
			}

			// Do we display the add order buton?
			if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_Orders)) {
				$GLOBALS['AddOrderButton'] = '<input type="button" value="' . GetLang('AddAnOrder') . '..." class="FormButton" style="width:100px" onclick="document.location.href=\'index.php?ToDo=addOrder\'" /><br /><br />';
			} else {
				$GLOBALS['AddOrderButton'] = '';
			}

			$GLOBALS['OrderActionOptions'] = '<option selected="1">' . GetLang('ChooseAction') . '</option>';

			// Do we need to disable the delete button?
			if (!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Delete_Orders) || $numOrders == 0) {
				$args = 'disabled="disabled"';
			} else {
				$args = 'value="delete"';
			}

			$GLOBALS['OrderActionOptions'] .= '<option ' . $args . '>' . GetLang('DeleteSelected') . '</option>';

			if($numOrders > 0) {
				if($MsgDesc == "" && (isset($_REQUEST['searchQuery']) || isset($_GET['searchId']) || count($_GET) > 1) && !isset($_GET['selectOrder'])) {
					if($numOrders == 1) {
						$MsgDesc = GetLang('OrderSearchResultsBelow1');
					}
					else {
						$MsgDesc = sprintf(GetLang('OrderSearchResultsBelowX'), $numOrders);
					}

					$MsgStatus = MSG_SUCCESS;
				}
				$args1 = 'value="printInvoice"';
				$args2 = 'value="printSlip"';
			}
			else {
				$args1 = 'disabled="disabled"';
				$args2 = 'disabled="disabled"';
			}

			$GLOBALS['OrderActionOptions'] .= '<option ' . $args1 . '>' . GetLang('PrintInvoicesSelected') . '</option>';
			$GLOBALS['OrderActionOptions'] .= '<option ' . $args2 . '>' . GetLang('PrintPackingSlipsSelected') . '</option>';

			if(!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS[base64_decode('SGlkZUV4cG9ydA==')] = "none";
			} else {
				// Do we need to disable the export button?
				if (!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Export_Orders) || $numOrders == 0) {
					$args = 'disabled="disabled"';
				} else {
					$args = 'value="export"';
				}
				$GLOBALS['OrderActionOptions'] .= '<option ' . $args . '>' . GetLang('ExportOrders') . '</option>';
			}

			$GLOBALS['OrderActionOptions'] .= '<option disabled="disabled"></option><optgroup label="' . GetLang('BulkOrderStatus') . '">';

			$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]order_status ORDER BY statusorder ASC");
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$GLOBALS['OrderActionOptions'] .= '<option value="updateStatus' . $row['statusid'] . '">' . $row['statusdesc'] . '</option>';
			}
			$GLOBALS['OrderActionOptions'] .= '</optgroup>';

			if (!isset($_REQUEST['searchId'])) {
				$_REQUEST['searchId'] = 0;
			}

			// Get the custom search as option fields
			$GLOBALS['CustomSearchOptions'] = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->GetSearchesAsOptions($_REQUEST['searchId'], $num_custom_searches, "AllOrders", "viewOrders", "customOrderSearch");

			if(!isset($_REQUEST['searchId'])) {
				$GLOBALS['HideDeleteCustomSearch'] = "none";
			} else {
				$GLOBALS['CustomSearchId'] = (int)$_REQUEST['searchId'];
			}

			$GLOBALS['OrderIntro'] = GetLang('ManageOrdersIntro');
			$GLOBALS['Message'] = '';
			// No orders
			if($numOrders == 0) {
				$GLOBALS['DisplayGrid'] = "none";

				// Performing a search of some kind
				if(count($_GET) > 1) {
					if ($MsgDesc == "") {
						$GLOBALS['Message'] = MessageBox(GetLang('NoOrderResults'), MSG_ERROR);
					}
				} else {
					$GLOBALS['Message'] = MessageBox(GetLang('NoOrders'), MSG_SUCCESS);
					$GLOBALS['DisplaySearch'] = "none";
				}
			}

			if($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			$flashMessages = GetFlashMessages();
			if(is_array($flashMessages)) {
				foreach($flashMessages as $flashMessage) {
					$GLOBALS['Message'] .= MessageBox($flashMessage['message'], $flashMessage['type']);
				}
			}

			$GLOBALS['ExportAction'] = "index.php?ToDo=startExport&t=orders";
			if (isset($GLOBALS['CustomSearchId']) && $GLOBALS['CustomSearchId'] != '0') {
				$GLOBALS['ExportAction'] .= "&searchId=" . $GLOBALS['CustomSearchId'];
			}
			else {
				$query_params = explode('&', $_SERVER['QUERY_STRING']);
				$params = array();
				$ignore = array("ToDo");
				foreach ($query_params as $param) {
					$arr = explode("=", $param);
					if (!in_arrayi($arr[0], $ignore)) {
						$params[$arr[0]] = urldecode($arr[1]);
					}
				}

				if (count($params)) {
					$GLOBALS['ExportAction'] .= "&" . http_build_query($params);
				}
			}

			$selectOrder = '';
			if (!empty($_GET['selectOrder']) && isId($_GET['selectOrder'])) {
				$selectOrder = 'QuickView(' . $_GET['selectOrder'] . ');';
			}
			$GLOBALS['SelectOrder'] = $selectOrder;

			// Used for iPhone interface
			$GLOBALS['OrderStatusOptions'] = $this->GetOrderStatusOptions();

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("orders.manage");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

		}

		public function _GetOrderList($Start, $SortField, $SortOrder, &$NumOrders, $AddLimit=true)
		{
			$extraFields = '';
			$extraJoins = '';

			if(isset($_REQUEST['couponCode']) && trim($_REQUEST['couponCode']) != '') {
				$extraFields = 'DISTINCT(co.ordcouporderid), ';
				$extraJoins = sprintf("INNER JOIN [|PREFIX|]order_coupons co ON (co.ordcouporderid=o.orderid AND co.ordcouponcode='%s')", $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['couponCode']));
			}

			// Return an array containing details about orders.
			$query = sprintf("
				SELECT %so.*, c.*, s.statusdesc AS ordstatustext, CONCAT(custconfirstname, ' ', custconlastname) AS custname,
					(
						SELECT COUNT(messageid)
						FROM [|PREFIX|]order_messages
						WHERE messageorderid=orderid
					) AS nummessages,
					(
						SELECT COUNT(messageid)
						FROM [|PREFIX|]order_messages
						WHERE messageorderid=orderid AND messagestatus != 'read'
					) AS numunreadmessages,
					(
						SELECT COUNT(messageid)
						FROM [|PREFIX|]order_messages
						WHERE messageorderid=orderid AND messagefrom='customer' AND messagestatus='unread'
					) AS newmessages
				FROM [|PREFIX|]orders o
				LEFT JOIN [|PREFIX|]customers c ON (o.ordcustid=c.customerid)
				LEFT JOIN [|PREFIX|]order_status s ON (s.statusid=o.ordstatus)
				%s", $extraFields, $extraJoins);

			$countQuery = "SELECT COUNT(o.orderid) FROM [|PREFIX|]orders o";
			if (!empty($extraJoins)) {
				$countQuery .= ' '.$extraJoins;
			}

			if(isset($_REQUEST['newMessages'])) {
				$countQuery .= " LEFT JOIN [|PREFIX|]order_messages ON (messageorderid=orderid) AND messagefrom='customer' AND messagestatus='unread'";
			}

			// Are there any search parameters?
			$queryWhere = '';

			$res = $this->BuildWhereFromVars($_REQUEST);
			$queryWhere .= $res["query"];
			$countQuery .= $res["count"];

			// Only fetch products which belong to the current vendor
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$queryWhere .= " AND ordvendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
			}

			$query .= " WHERE 1=1 ".$queryWhere;
			$countQuery .= " WHERE 1=1 ".$queryWhere;

			// Only those with new messages?
			if(isset($_REQUEST['newMessages'])) {
				$query .= " HAVING newmessages >= 1";
			}

			// How many results do we have?
			$result = $GLOBALS['ISC_CLASS_DB']->Query($countQuery);
			$NumOrders = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

			// Add the limit
			$query .= sprintf(" order by %s %s", $SortField, $SortOrder);
			if($AddLimit) {
				$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit($Start, ISC_ORDERS_PER_PAGE);
			}

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if($GLOBALS['ISC_CLASS_DB']->CountResult($result) == 0) {
				$GLOBALS['HideViewAllLink'] = 'none';
			}

			return $result;
		}

		/**
		* Builds a where statement for order listing based on values in an array
		*
		* @param mixed $array
		* @return mixed
		*/
		public function BuildWhereFromVars($array)
		{
			$queryWhere = "";
			$countQuery = "";

			if(isset($array['orderId']) && $array['orderId'] != '') {
				$queryWhere .= " AND orderid='".(int)$array['orderId']."'";
				return array("query" => $queryWhere,  "count" => $countQuery);
			}

			if(isset($array['customerId']) && $array['customerId'] != '') {
				$queryWhere .= " AND ordcustid='".(int)$array['customerId']."'";
				return array("query" => $queryWhere,  "count" => $countQuery);
			}

			if(isset($array['searchQuery']) && $array['searchQuery'] != "") {
				$search_query = $GLOBALS['ISC_CLASS_DB']->Quote($array['searchQuery']);
				$queryWhere .= " AND (
					orderid='".(int)$search_query."'
					OR ordtrackingno='".$search_query."'
					OR ordpayproviderid='".$search_query."'
					OR CONCAT(custconfirstname, ' ', custconlastname) LIKE '%".$search_query."%'
					OR CONCAT(ordbillfirstname, ' ', ordbilllastname) LIKE '%".$search_query."%'
					OR CONCAT(ordshipfirstname, ' ', ordshiplastname) LIKE '%".$search_query."%'
					OR custconemail    LIKE '%".$search_query."%'
					OR ordbillstreet1  LIKE '%".$search_query."%'
					OR ordbillstreet2  LIKE '%".$search_query."%'
					OR ordbillsuburb   LIKE '%".$search_query."%'
					OR ordbillstate    LIKE '%".$search_query."%'
					OR ordbillzip      LIKE '%".$search_query."%'
					OR ordbillcountry  LIKE '%".$search_query."%'
					OR ordshipstreet1  LIKE '%".$search_query."%'
					OR ordshipstreet2  LIKE '%".$search_query."%'
					OR ordshipsuburb   LIKE '%".$search_query."%'
					OR ordshipstate    LIKE '%".$search_query."%'
					OR ordshipzip      LIKE '%".$search_query."%'
					OR ordshipcountry  LIKE '%".$search_query."%'
				) ";
				$countQuery .= " LEFT JOIN [|PREFIX|]customers c ON (o.ordcustid=c.customerid)";
			}

			if(isset($array['orderStatus']) && $array['orderStatus'] != "") {
				$order_status = $GLOBALS['ISC_CLASS_DB']->Quote((int)$array['orderStatus']);
				$queryWhere .= sprintf(" AND ordstatus='%d'", $order_status);
			}
			// Otherwise, only fetch complete orders
			else {
				$queryWhere .= " AND ordstatus > 0";
			}

			if(isset($array['paymentMethod']) && $array['paymentMethod'] != "") {
				$payment_method = $GLOBALS['ISC_CLASS_DB']->Quote($array['paymentMethod']);
				$queryWhere .= sprintf(" AND orderpaymentmodule='%s'", $payment_method);
			}

			if(isset($_REQUEST['shippingMethod']) && $_REQUEST['shippingMethod'] != "") {
				$shipping_method = $GLOBALS['ISC_CLASS_DB']->Quote($_REQUEST['shippingMethod']);
				$queryWhere .= sprintf(" AND ordershipmodule='%s'", $shipping_method);
			}

			if(isset($array['orderFrom']) && isset($array['orderTo']) && $array['orderFrom'] != "" && $array['orderTo'] != "") {
				$order_from = (int)$array['orderFrom'];
				$order_to = (int)$array['orderTo'];
				$queryWhere .= sprintf(" AND (orderid >= '%d' and orderid <= '%d')", $GLOBALS['ISC_CLASS_DB']->Quote($order_from), $GLOBALS['ISC_CLASS_DB']->Quote($order_to));
			}
			else if(isset($array['orderFrom']) && $array['orderFrom'] != "") {
				$order_from = (int)$array['orderFrom'];
				$queryWhere .= sprintf(" AND orderid >= '%d'", $order_from);
			}
			else if(isset($array['orderTo']) && $array['orderTo'] != "") {
				$order_to = (int)$array['orderTo'];
				$queryWhere .= sprintf(" AND orderid <= '%d'", $order_to);
			}

			if(isset($array['totalFrom']) && $array['totalFrom'] != "" && isset($array['totalTo']) && $array['totalTo'] != "") {
				$from_total = $array['totalFrom'];
				$to_total = $array['totalTo'];
				$queryWhere .= sprintf(" AND ordtotalamount >= '%s' and ordtotalamount <= '%s'", $GLOBALS['ISC_CLASS_DB']->Quote($from_total), $GLOBALS['ISC_CLASS_DB']->Quote($to_total));
			}
			else if(isset($array['totalFrom']) && $array['totalFrom'] != "") {
				$from_total = $array['totalFrom'];
				$queryWhere .= sprintf(" AND ordtotalamount >= '%s'", $GLOBALS['ISC_CLASS_DB']->Quote($from_total));
			}
			else if(isset($array['totalTo']) && $array['totalTo'] != "") {
				$to_total = $array['totalTo'];
				$queryWhere .= sprintf(" AND ordtotalamount <= '%s'", $GLOBALS['ISC_CLASS_DB']->Quote($to_total));
			}

			// Limit results to a particular date range
			if(isset($array['dateRange']) && $array['dateRange'] != "") {
				$range = $array['dateRange'];
				switch($range) {
					// Orders within the last day
					case "today":
						$from_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), isc_date("d"), isc_date("Y"));
						break;
					// Orders received in the last 2 days
					case "yesterday":
						$from_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), isc_date("d")-1, isc_date("Y"));
						$to_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), isc_date("d"), isc_date("Y"));
						break;
					// Orders received in the last 24 hours
					case "day":
						$from_stamp = time()-60*60*24;
						break;
					// Orders received in the last 7 days
					case "week":
						$from_stamp = time()-60*60*24*7;
						break;
					// Orders received in the last 30 days
					case "month":
						$from_stamp = time()-60*60*24*30;
						break;
					// Orders received this month
					case "this_month":
						$from_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), 1, isc_date("Y"));
						break;
					// Orders received this year
					case "this_year":
						$from_stamp = isc_gmmktime(0, 0, 0, 1, 1, isc_date("Y"));
						break;
					// Custom date
					default:
						if(isset($array['fromDate']) && $array['fromDate'] != "") {
							$from_date = urldecode($array['fromDate']);
							$from_data = explode("/", $from_date);
							$from_stamp = isc_gmmktime(0, 0, 0, $from_data[0], $from_data[1], $from_data[2]);
						}
						if(isset($array['toDate']) && $array['toDate'] != "") {
							$to_date = urldecode($array['toDate']);
							$to_data = explode("/", $to_date);
							$to_stamp = isc_gmmktime(23, 59, 59, $to_data[0], $to_data[1], $to_data[2]);
						}
				}

				if (!isset($array['SearchByDate']) || $array['SearchByDate'] == 0) {
					if(isset($from_stamp)) {
						$queryWhere .= " AND orddate >= '".(int)$from_stamp."'";
					}
					if(isset($to_stamp)) {
						$queryWhere .= "AND orddate <='".(int)$to_stamp."'";
					}
				} else if ($array['SearchByDate'] == 1) {
					if(isset($from_stamp)) {
						$queryWhere .= " AND (
							SELECT opf.orderprodid
							FROM [|PREFIX|]order_products opf
							WHERE o.orderid=opf.orderorderid AND opf.ordprodeventdate >='".(int)$from_stamp."'
						)";
					}
					if(isset($to_stamp)) {
						$queryWhere .= " AND (
							SELECT opt.orderprodid
							FROM [|PREFIX|]order_products opt
							WHERE o.orderid=opt.orderorderid AND opt.ordprodeventdate <='".(int)$to_stamp."'
						)";
					}
				} else if ($array['SearchByDate'] == 2) {
					if(isset($from_stamp)) {
						$queryWhere .= " AND (orddate >= '".(int)$from_stamp."' OR (
							SELECT opf.orderprodid
							FROM [|PREFIX|]order_products opf
							WHERE o.orderid=opf.orderorderid AND opf.ordprodeventdate >='".(int)$from_stamp."'
						))";
					}

					if(isset($to_stamp)) {
						$queryWhere .= " AND (orddate <= '".(int)$to_stamp."' OR (
							SELECT opt.orderprodid
							FROM [|PREFIX|]order_products opt
							WHERE o.orderid=opt.orderorderid AND opt.ordprodeventdate <='".(int)$to_stamp."'
						))";
					}
					if(isset($to_stamp)) {
						$queryWhere .= "AND orddate <='".(int)$from_stamp."'";
					}
				}
			}

			// Orders which contain a particular product?
			if(isset($array['productId'])) {
				$queryWhere .= " AND (
					SELECT sp.orderprodid
					FROM [|PREFIX|]order_products sp
					WHERE sp.ordprodid='".(int)$array['productId']."' AND sp.orderorderid=o.orderid
					LIMIT 1
				)";
			}

			// Orders by product name
			if(isset($array['productName'])) {
				$queryWhere .= " AND (
					SELECT sp.orderprodid
					FROM [|PREFIX|]order_products sp
					WHERE sp.ordprodname LIKE '%".$GLOBALS['ISC_CLASS_DB']->Quote($array['productName'])."%' AND sp.orderorderid=o.orderid
					LIMIT 1
				)";
			}

			return array("query" => $queryWhere,  "count" => $countQuery);
		}

		/**
		 * Get all the available order status as html options (without the <select> tags)
		 *
		 * @param integer $SelectedStatus The status to mark as selected
		 *
		 * @return string The html with the option tags in it
		 */
		public function GetOrderStatusOptions($SelectedStatus = null)
		{
			// Get all order status options from the database
			static $statuses = null;
			$output = "";

			// Only do the database query the first time
			if ($statuses === null) {
				$statuses = array();
				if($SelectedStatus === 0 || $SelectedStatus === '0') {
					$statuses[] = array(
						"statusid" => 0,
						"statusdesc" => GetLang('Incomplete')
					);
				}
				$query = "select statusid, statusdesc from [|PREFIX|]order_status order by statusorder asc";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$statuses[] = $row;
				}
			}

			foreach ($statuses as $row) {
				// Only show the 0 status if it's our current status
				if($row['statusid'] == 0 && $SelectedStatus != 0) {
					continue;
				}
				if ($row['statusid'] == $SelectedStatus) {
					$sel = 'selected="selected"';
				} else {
					$sel = '';
				}
				$output .= sprintf("<option value='%d' %s>%s</option>", $row['statusid'], $sel, $row['statusdesc']);
			}

			return $output;
		}

		/**
		*	Get a list of order messages and return them as an array. Also pass
		*	back the number of new and total messages to the 2nd and 3rd reference params
		*/
		protected function GetOrderMessages($OrderId, $SortField, $SortOrder, &$NewMessages, &$TotalMessages)
		{
			$messages = array();
			$query = sprintf("select *, (select username from [|PREFIX|]users where pk_userid=staffuserid) as uname, (select userfirstname from [|PREFIX|]users where pk_userid=staffuserid) as ufname, (select userlastname from [|PREFIX|]users where pk_userid=staffuserid) as ulname from [|PREFIX|]order_messages where messageorderid='%d' order by %s %s", $GLOBALS['ISC_CLASS_DB']->Quote($OrderId), $SortField, $SortOrder);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				array_push($messages, $row);

				if($row['messagefrom'] == "customer" && $row['messagestatus'] == "unread") {
					$NewMessages++;
				}

				$TotalMessages++;
			}

			// If we're on the iPhone then reset the message stack to 0 unread
			if(defined("IS_IPHONE")) {
				$updatedMessage = array(
					"messagestatus" => "read"
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("order_messages", $updatedMessage, "messageorderid='".$GLOBALS['ISC_CLASS_DB']->Quote($OrderId)."'");
			}

			return $messages;
		}

		protected function ViewOrderMessages($MsgDesc = "", $MsgStatus = "")
		{
			$new_messages = 0;
			$total_messages = 0;
			$GLOBALS['MessageGrid'] = "";
			$GLOBALS['Indent'] = 0;

			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			if(isset($_GET['sortField'])) {
				$sort_field = $_GET['sortField'];
			} else {
				$sort_field = "messageid";
			}

			if(isset($_GET['sortOrder'])) {
				$sort_order = $_GET['sortOrder'];
			} else {
				$sort_order = "asc";
			}

			if(isset($_REQUEST['orderId'])) {
				$order_id = (int)$_REQUEST['orderId'];
				$GLOBALS['OrderId'] = $order_id;

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$message_list = $this->GetOrderMessages($order_id, $sort_field, $sort_order, $new_messages, $total_messages);

				if($total_messages == 1) {
					$lang = "OrderMessagesIntro1";
				} else {
					$lang = "OrderMessagesIntroX";
				}

				$GLOBALS['MessageIntro'] = sprintf(GetLang($lang), $total_messages, $new_messages, $order_id);

				if (!empty($message_list)) {
					foreach($message_list as $message) {
						$GLOBALS['MessageId'] = $message['messageid'];
						$GLOBALS['Subject'] = $message['subject'];
						$GLOBALS['MessageDate'] = isc_date(GetConfig('ExtendedDisplayDateFormat'), $message['datestamp']);

						// If the message isn't read then we'll wrap the subject in bold tags
						if($message['messagestatus'] == "unread" && $message['messagefrom'] == "customer") {
							$GLOBALS['Subject'] = sprintf("<strong>%s</strong>", $GLOBALS['Subject']);
						}

						$GLOBALS['OrderMessage'] = nl2br(isc_html_escape($message['message']));

						if($message['messagefrom'] == "customer") {
							$GLOBALS['OrderFrom'] = GetLang('FromCustomer');
						}
						else {
							if($message['ufname'] != "" || $message['ulname'] != "") {
								$GLOBALS['OrderFrom'] = trim(sprintf("%s %s", $message['ufname'], $message['ulname']));
							} else {
								$GLOBALS['OrderFrom'] = $message['uname'];
							}
						}

						if($message['messagefrom'] == "admin") {
							$GLOBALS['MessageStatus'] = GetLang('NA');
						}
						else if($message['messagefrom'] == "customer" && $message['messagestatus'] == "unread") {
							$GLOBALS['MessageStatus'] = sprintf(GetLang('MessageUnRead'), $GLOBALS['ShopPath'], $order_id, $message['messageid']);
						}
						else {
							$GLOBALS['MessageStatus'] = sprintf(GetLang('MessageRead'), $GLOBALS['ShopPath'], $order_id, $message['messageid']);
						}

						// Is the message flagged?
						if($message['isflagged'] == "0") {
							$GLOBALS['FlagState'] = "1";
							$GLOBALS['HideFlag'] = "none";
							$GLOBALS['FlagText'] = GetLang('Flag');
						}
						else {
							$GLOBALS['FlagState'] = "0";
							$GLOBALS['HideFlag'] = "";
							$GLOBALS['FlagText'] = GetLang('ClearFlag');
						}

						$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("message.manage.row");
						$GLOBALS['MessageGrid'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);

						// If they're sorted by default fields then indent each message
						if($sort_field == "messageid" && $sort_order == "asc") {
							$GLOBALS['Indent'] += 20;
						}
					}
				}
				else {
					$GLOBALS['DisplayGrid'] = "none";
					$GLOBALS['DisableDelete'] = "disabled readonly";
				}

				$GLOBALS['MessageSubject'] = $this->GetRecentCustomerMessage($order_id);
				$GLOBALS['ViewOrderMessages'] = sprintf(GetLang('ViewOrderMessages'), $order_id);
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("ordermessages.manage");
				$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
			}
		}

		protected function UpdateOrderMessageStatus()
		{
			if (isset($_GET['orderId']) && isset($_GET['messageId']) && isset($_GET['status'])) {
				$order_id = (int)$_GET['orderId'];
				$message_id = (int)$_GET['messageId'];
				$status = $_GET['status'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$updatedMessage = array(
					"messagestatus" => $status
				);

				if ($GLOBALS['ISC_CLASS_DB']->UpdateQuery("order_messages", $updatedMessage, "messageid='".$GLOBALS['ISC_CLASS_DB']->Quote($message_id)."'")) {
					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($message_id, $_GET['status']);

					$this->ViewOrderMessages(sprintf(GetLang('OrderMessageStatusChanged'), $status), MSG_SUCCESS);
				} else {
					$this->ViewOrderMessages(sprintf(GetLang('OrderMessageStatusChangeFailed'), $status), MSG_ERROR);
				}
			}
		}

		protected function FlagOrderMessage()
		{
			if(isset($_GET['flagState']) && isset($_GET['orderId']) && isset($_GET['messageId'])) {
				$flag_state = (int)$_GET['flagState'];
				$order_id = (int)$_GET['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$message_id = (int)$_GET['messageId'];

				$updatedMessage = array(
					"isflagged" => $flag_state
				);
				if($GLOBALS['ISC_CLASS_DB']->UpdateQuery("order_messages", $updatedMessage, "messageid='".$GLOBALS['ISC_CLASS_DB']->Quote($message_id)."'")) {
					if($flag_state == "0") {
						// Log this action
						$GLOBALS['ISC_CLASS_LOG']->LogAdminAction("cleared", $message_id);

						$lang = "OrderFlagCleared";
					}
					else {
						// Log this action
						$GLOBALS['ISC_CLASS_LOG']->LogAdminAction("flagged", $message_id);

						$lang = "OrderFlaggedOK";
					}

					$this->ViewOrderMessages(GetLang($lang), MSG_SUCCESS);
				}
				else {
					$this->ViewOrderMessages(sprintf(GetLang('OrderFlaggedFailed'), $flag_state), MSG_ERROR);
				}
			}
		}

		protected function DeleteOrderMessages()
		{
			if(isset($_POST['orderId']) && is_array($_POST['messages'])) {
				$order_id = (int)$_POST['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$message_ids = implode("','", $GLOBALS['ISC_CLASS_DB']->Quote($_POST['messages']));
				$query = sprintf("delete from [|PREFIX|]order_messages where messageorderid='%d' and messageid in('%s')", $GLOBALS['ISC_CLASS_DB']->Quote($order_id), $message_ids);

				if($GLOBALS['ISC_CLASS_DB']->Query($query)) {
					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($order_id, count($_POST['messages']));

					$this->ViewOrderMessages(GetLang('OrderMessagesDeletedOK'), MSG_SUCCESS);
				}
				else {
					$this->ViewOrderMessages(GetLang('OrderMessagesDeletedFailed'), MSG_ERROR);
				}
			}
		}

		public function GetCustomerNameByOrderId($OrderId)
		{
			$query = sprintf("select ordcustid, (select concat(custconfirstname, ' ', custconlastname) from [|PREFIX|]customers where customerid=ordcustid) as custname, (select custconemail from [|PREFIX|]customers where customerid=ordcustid) as custemail  from [|PREFIX|]orders where orderid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($OrderId));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				return sprintf("%s <%s>", $row['custname'], $row['custemail']);
			} else {
				return sprintf(GetLang('CustomerForOrderX'), $OrderId);
			}
		}

		public function GetCustomerEmailByOrderId($OrderId)
		{
			$query = sprintf("select ordcustid, (select custconemail from [|PREFIX|]customers where customerid=ordcustid) as custemail  from [|PREFIX|]orders where orderid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($OrderId));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				return $row['custemail'];
			} else {
				return "";
			}
		}

		/**
		*	Get the subject of the most recent customer message. If none is available just use "Re: Order #xxxx"
		*/
		public function GetRecentCustomerMessage($OrderId)
		{
			$query = sprintf("select subject from [|PREFIX|]order_messages where messageorderid='%d' and messagefrom='customer' order by messageid desc limit 1", $GLOBALS['ISC_CLASS_DB']->Quote($OrderId));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				return sprintf(GetLang('OrderMessageRe'), $row['subject']);
			} else {
				return sprintf(GetLang('OrderMessageDefaultSubject'), $OrderId);
			}
		}

		protected function CreateOrderMessage()
		{
			if(isset($_GET['orderId'])) {
				$order_id = (int)$_GET['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$GLOBALS['OrderId'] = $order_id;
				$GLOBALS['FormAction'] = "saveNewOrderMessage";
				$GLOBALS['Title'] = GetLang('CreateMessage');
				$GLOBALS['Intro'] = GetLang('CreateMessageIntro');
				$GLOBALS['ButtonAction'] = GetLang('SendMessage');

				$GLOBALS['MessageToFrom'] = GetLang('MessageTo');
				$GLOBALS['MessageTo'] = $this->GetCustomerNameByOrderId($order_id);
				$GLOBALS['MessageSubject'] = $this->GetRecentCustomerMessage($order_id);

				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("ordermessage.form");
				$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
			}
		}

		protected function SaveNewOrderMessage()
		{
			if(isset($_POST['orderId']) && isset($_POST['subject']) && isset($_POST['message'])) {
				$order_id = (int)$_POST['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$subject = $_POST['subject'];
				$message = $_POST['message'];

				// Save the message to the database first
				$newMessage = array(
					"messagefrom" => "admin",
					"subject" => $subject,
					"message" => $message,
					"datestamp" => time(),
					"messageorderid" => $order_id,
					"messagestatus" => "unread",
					"staffuserid" => $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetUserId(),
					"isflagged" => 0
				);
				$message_id =  $GLOBALS['ISC_CLASS_DB']->InsertQuery("order_messages", $newMessage);
				if($message_id) {
					$message_id = $GLOBALS['ISC_CLASS_DB']->LastId();

					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($message_id, $order_id);

					// Now send a notification email to the customer
					$customer_email = $this->GetCustomerEmailByOrderId($order_id);

					// Create a new email API object to send the email
					$store_name = GetConfig('StoreName');

					$emailTemplate = FetchEmailTemplateParser();
					$emailTemplate->SetTemplate("ordermessage_notification");
					$message = $emailTemplate->ParseTemplate(true);

					require_once(ISC_BASE_PATH . "/lib/email.php");
					$obj_email = GetEmailClass();
					$obj_email->Set('CharSet', GetConfig('CharacterSet'));
					$obj_email->From(GetConfig('OrderEmail'), $store_name);
					$obj_email->Set("Subject", $subject);
					$obj_email->AddBody("html", $message);
					$obj_email->AddRecipient($customer_email, "", "h");
					$email_result = $obj_email->Send();

					if($email_result['success']) {
						$this->ViewOrderMessages(GetLang('OrderMessageSentOK'), MSG_SUCCESS);
					}
					else {
						$this->ViewOrderMessages(GetLang('OrderMessagesSentEmailFailed'), MSG_ERROR);
					}
				}
				else {
					$this->ViewOrderMessages(GetLang('OrderMessagesSentFailed'), MSG_ERROR);
				}
			}
		}

		protected function EditOrderMessage()
		{
			if(isset($_GET['orderId']) && isset($_GET['messageId'])) {
				$order_id = (int)$_GET['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$message_id = (int)$_GET['messageId'];
				$query = sprintf("select * from [|PREFIX|]order_messages where messageid='%d' and messageorderid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($message_id), $GLOBALS['ISC_CLASS_DB']->Quote($order_id));
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

				if($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$GLOBALS['OrderId'] = $order_id;
					$GLOBALS['FormAction'] = "saveUpdatedOrderMessage";
					$GLOBALS['Title'] = GetLang('EditMessage');
					$GLOBALS['Intro'] = GetLang('EditMessageIntro');
					$GLOBALS['ButtonAction'] = GetLang('SaveMessage');
					$GLOBALS['MessageId'] = $message_id;
					$GLOBALS['MessageTo'] = $this->GetCustomerNameByOrderId($order_id);
					$GLOBALS['MessageSubject'] = $row['subject'];
					$GLOBALS['MessageContent'] = str_replace("<br />", "\n", $row['message']);

					if($row['messagefrom'] == "customer") {
						$GLOBALS['MessageToFrom'] = GetLang('MessageFrom');
					} else {
						$GLOBALS['MessageToFrom'] = GetLang('MessageTo');
					}

					$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("ordermessage.form");
					$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
				}
				else {
					$this->ViewOrderMessages(GetLang('OrderMessageSentOK'), MSG_SUCCESS);
				}
			}
		}

		protected function SavedUpdatedOrderMessage()
		{
			if(isset($_POST['orderId']) && isset($_POST['messageId']) && isset($_POST['subject']) && isset($_POST['message'])) {
				$order_id = (int)$_POST['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$message_id = (int)$_POST['messageId'];
				$subject = $_POST['subject'];
				// $message = str_replace("\n", "<br />", $_POST['message']);
				$message = $_POST['message'];
				$updatedMessage = array(
					"subject" => $subject,
					"message" => $message
				);
				if($GLOBALS['ISC_CLASS_DB']->UpdateQuery("order_messages", $updatedMessage, "messageid='".$GLOBALS['ISC_CLASS_DB']->Quote($message_id)."'")) {
					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($message_id, $order_id);

					$this->ViewOrderMessages(GetLang('OrderMessageUpdatedOK'), MSG_SUCCESS);
				}
				else {
					$this->ViewOrderMessages(GetLang('OrderMessagesUpdatedFailed'), MSG_ERROR);
				}
			}
		}

		protected function SearchOrders()
		{
			$GLOBALS['OrderPaymentOptions'] = "";
			$GLOBALS['OrderShippingOptions'] = "";

			$checkout_providers = GetCheckoutModulesThatCustomerHasAccessTo();
			$shipping_providers = GetAvailableModules('shipping', false, true, false);

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}

			foreach ($checkout_providers as $provider) {
				$GLOBALS['OrderPaymentOptions'] .= sprintf("<option value='%s'>%s</option>", $provider['object']->GetId(), $provider['object']->GetName());
			}

			foreach ($shipping_providers as $provider) {
				$GLOBALS['OrderShippingOptions'] .= sprintf("<option value='%s'>%s</option>", $provider['object']->GetId(), $provider['object']->GetName());
			}

			$GLOBALS['OrderStatusOptions'] = $this->GetOrderStatusOptions();
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("orders.search");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		*	This function checks to see if the user wants to save the search details as a custom search,
		*	and if they do one is created. They are then forwarded onto the search results
		*/
		protected function SearchOrdersRedirect()
		{
			// Format totals back to the western standard
			if($_GET['totalFrom'] != "") {
				$_GET['totalFrom'] = $_REQUEST['totalFrom'] = DefaultPriceFormat($_GET['totalFrom']);
			}

			if($_GET['totalTo'] != "") {
				$_GET['totalTo'] = $_REQUEST['totalTo'] = DefaultPriceFormat($_GET['totalTo']);
			}

			// Are we saving this as a custom search?
			if(isset($_GET['viewName']) && $_GET['viewName'] != '') {
				$search_id = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->SaveSearch($_GET['viewName'], $_GET);

				if($search_id > 0) {

					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($search_id, $_GET['viewName']);

					ob_end_clean();
					header(sprintf("Location:index.php?ToDo=customOrderSearch&searchId=%d&new=true", $search_id));
					exit;
				}
				else {
					$this->ManageOrders(sprintf(GetLang('ViewAlreadExists'), $_GET['viewName']), MSG_ERROR);
				}
			}
			// Plain search
			else {
				$this->ManageOrders();
			}
		}

		/**
		*	Load a custom search
		*/
		protected function CustomSearch()
		{
			SetSession('ordersearch', (int) $_GET['searchId']);

			if ($_GET['searchId'] > 0) {
				$this->_customSearch = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->LoadSearch($_GET['searchId']);
				$_REQUEST = array_merge($_REQUEST, $this->_customSearch['searchvars']);
			}

			if (isset($_REQUEST['new'])) {
				$this->ManageOrders(GetLang('CustomSearchSaved'), MSG_SUCCESS);
			} else {
				$this->ManageOrders();
			}
		}

		protected function DeleteCustomSearch()
		{

			if($GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->DeleteSearch($_GET['searchId'])) {
				// remove the saved search from the session to default to All Orders
				UnsetSession('ordersearch');

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($_GET['searchId']);

				$this->ManageOrders(GetLang('DeleteCustomSearchSuccess'), MSG_SUCCESS);
			}
			else {
				$this->ManageOrders(GetLang('DeleteCustomSearchFailed'), MSG_ERROR);
			}
		}

		/**
		*	Print an invoice for an order. If $EndWithPageBreak is true then we will output a page break
		*/
		protected function DoInvoicePrinting($OrderId, $EndWithPageBreak = false, $PrintAutomatically = true)
		{
			require_once ISC_BASE_PATH . '/lib/order.printing.php';
			$invoice = generatePrintableInvoice($OrderId);

			if(!$invoice) {
				echo "<script type=\"text/javascript\">window.close();</script>";
			}

			echo $invoice;

			// Should we output a pagebreak?
			if($EndWithPageBreak) {
				echo "<p class='PageBreak'>&nbsp;</p>";
			}

			// Should we print this order automatically?
			if($PrintAutomatically) {
				echo '<script type="text/javascript">window.setTimeout("window.print();", 1000);</script>';
			}
		}

		protected function PrintInvoice()
		{
			// Print an invoice for an order
			ob_end_clean();

			if(isset($_GET['orderId'])) {
				$order_id = (int)$_GET['orderId'];

				// Does this user have permission to view this order?
				$order = GetOrder($order_id);
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
				}

				$this->DoInvoicePrinting($order_id, true, true);
			}
			else {
				echo "<script type=\"text/javascript\">window.close();</script>";
			}

			die();
		}

		protected function PrintMultiInvoices()
		{
			// Print multiple invoices and separate each one with a page break
			ob_end_clean();

			if(isset($_POST['orders'])) {
				$order_ids = $_POST['orders'];
				sort($order_ids, SORT_NUMERIC);
				for($i = 0; $i < count($order_ids); $i++) {
					$order_id = $order_ids[$i];

					// Does this user have permission to view this order?
					$order = GetOrder($order_id);
					if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
						FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
					}

					if(count($order_ids) > 1) {
						if($i == count($order_ids)-1) {
							$do_print = true;
							$do_pagebreak = false;
						}
						else {
							$do_print = false;
							$do_pagebreak = true;
						}
					}
					else {
						$do_print = true;
						$do_pagebreak = false;
					}

					$this->DoInvoicePrinting($order_id, $do_pagebreak, $do_print);
				}
			}
			else {
				echo "<script type=\"text/javascript\">window.close();</script>";
			}

			die();
		}

		/**
		*	Create a view for orders. Uses the same form as searching but puts the
		*	name of the view at the top and it's mandatory instead of optional.
		*/
		protected function CreateView()
		{
			$GLOBALS['OrderPaymentOptions'] = "";
			$GLOBALS['OrderShippingOptions'] = "";

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}


			$checkout_providers = GetCheckoutModulesThatCustomerHasAccessTo();
			$shipping_providers = GetAvailableModules('shipping', false, true, false);

			foreach($checkout_providers as $provider) {
				$GLOBALS['OrderPaymentOptions'] .= sprintf("<option value='%s'>%s</option>", $provider['object']->GetId(), $provider['object']->GetName());
			}

			foreach($shipping_providers as $provider) {
				$GLOBALS['OrderShippingOptions'] .= sprintf("<option value='%s'>%s</option>", $provider['object']->GetId(), $provider['object']->GetName());
			}

			$GLOBALS['OrderStatusOptions'] = $this->GetOrderStatusOptions();
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("orders.view");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}


		protected function ImportTrackingNumbers()
		{
			require_once dirname(__FILE__)."/../importer/tracking_numbers.php";
			$importer = new ISC_BATCH_IMPORTER_TRACKING_NUMBERS();
		}

		protected function updateOrderStatusBox()
		{
			if (array_key_exists('orders', $_REQUEST) && array_key_exists('statusId', $_REQUEST) && isId($_REQUEST['statusId'])) {
				$GLOBALS['StatusID'] = $_REQUEST['statusId'];
				$GLOBALS['JavaScriptOrderIds'] = $_REQUEST['orders'];
				$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("orders.updatestatus.popup");
				$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
			}
		}

		/**
		* Delete order configurable product fields and the files uploaded with the order
		*
		* @param string $orderIds order ids separate by comma
		*
		*/
		protected function _DeleteOrderProductFields($orderIds)
		{
			$fieldsQuery = "Select * from [|PREFIX|]order_configurable_fields WHERE orderid IN ('".$orderIds."');";
			$fieldsResult = $GLOBALS['ISC_CLASS_DB']->Query($fieldsQuery);
			$fieldIds[] = array(0);
			while($field = $GLOBALS['ISC_CLASS_DB']->Fetch($fieldsResult)) {
				//remove uploaded file if there is any
				if($field['filename'] != '') {
					@unlink(ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products/'.$field['filename']);
				}
				$fieldIds[] = $field['orderfieldid'];
			}
			$fieldIdsString = implode("','", array_map('intval', $fieldIds));

			$GLOBALS['ISC_CLASS_DB']->DeleteQuery('order_configurable_fields', "WHERE orderfieldid IN ('".$fieldIdsString."')");
		}

		/**
		*	Load order product configurable fields layout in imodal.
		*
		*/
		public function LoadOrderProductFieldsFullView()
		{
			if(!isset($_REQUEST['orderId'])) {
				exit;
			}

			$ordprodid = 0;
			$GLOBALS['OrderProducts'] = '';

			$fieldsArray = $this->GetOrderProductFieldsData($_REQUEST['orderId']);

			$query = "SELECT ordprodname, orderprodid
						FROM [|PREFIX|]order_products
						WHERE orderorderid=".(int)$_REQUEST['orderId'];

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			//each item in the order
			while($orderProd = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				//if this order item doesn't has any configurable fields, go to the next item
				if(!isset($fieldsArray[$orderProd['orderprodid']])) {
					continue;
				}

				$productFields = '';
				$productFields = $this->LoadOrderProductFieldRow($fieldsArray[$orderProd['orderprodid']], true);

				//only load products with configurable fields
				if($productFields != '') {
					$GLOBALS['OrderProductName'] = isc_html_escape($orderProd['ordprodname']);
					$GLOBALS['OrderProductFields'] = $productFields;
					$GLOBALS['OrderProducts'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderProductFields');
				}
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('order.productfields');
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
			exit;
		}

		public function LoadOrderProductFieldRow($fields, $fullView = false)
		{
			if(empty($fields)) {
				return '' ;
			}
			$productFields = '';

			//each configurable field customer submited
			foreach($fields as $row) {

				if (empty($row['textcontents']) && empty($row['filename'])) {
					continue;
				}

				$fieldValue = '-';
				$fieldName = $row['fieldname'];
				switch($row['fieldtype']) {
					case 'file': {
						$fieldValue = '<a href="'.GetConfig('ShopPath').'/'.GetConfig('ImageDirectory').'/configured_products/'.urlencode($row['originalfilename']).'">'.isc_html_escape($row['originalfilename']).'</a>';
						break;
					}
					default: {
						if(isc_strlen($row['textcontents'])>50 && !$fullView) {
							$fieldValue = isc_html_escape(isc_substr($row['textcontents'], 0, 50))." ..";
						} else {
							$fieldValue = isc_html_escape($row['textcontents']);
						}
						break;
					}
				}

				$productFields .= "<dt>".isc_html_escape($fieldName).":</dt>";
				$productFields .= "<dd>".$fieldValue."</dd>";
			}

			return $productFields;
		}

		/**
		* get the product fields data for each order
		*
		* @param int $orderId, order id
		*
		* @return array an array of product fields data
		*/
		public function GetOrderProductFieldsData($orderId)
		{
			$query = "SELECT o.*
						FROM [|PREFIX|]order_configurable_fields o
							JOIN [|PREFIX|]product_configurable_fields p ON o.fieldid = p.productfieldid
						WHERE
							o.orderid=".(int)$orderId."
						ORDER BY p.fieldsortorder ASC";

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			$fields = array();
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$fields[$row['ordprodid']][] = $row;
			}

			return $fields;
		}

		/**
		 * Return an instance of the cart management API for a particular session ID.
		 * The session ID only needs to be supplied once, as the returned class is a singleton
		 * multiple calls to this method will just return the same object.
		 *
		 * @param string The name of the session to initiate with the API.
		 * @param boolean Set to true to reset the order session to an empty array even if it already exists.
		 * @return ISC_ADMIN_CART_API A referenced copy of the API object.
		 */
		public function GetCartApi($sessionId='', $reset=false)
		{
			static $classApi = null;
			if(is_null($classApi)) {
				$classApi = new ISC_ADMIN_CART_API;
				if(!isset($_SESSION['ORDER_MANAGER'][$sessionId]) || $reset == true) {
					$_SESSION['ORDER_MANAGER'][$sessionId] = array();
				}
				$classApi->SetCartSession($_SESSION['ORDER_MANAGER'][$sessionId]);
			}
			$api = &$classApi;
			return $api;
		}

		/**
		 * Show the form to edit an existing order.
		 */
		protected function EditOrder()
		{
			$GLOBALS['BreadcrumEntries'][GetLang('EditOrder')] = '';

			if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Orders)) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				return;
			}

			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();

			if(!isset($_REQUEST['orderId']) || !($order = GetOrder($_REQUEST['orderId']))) {
				FlashMessage(GetLang('InvalidOrderToEdit'), MSG_SUCCESS, 'index.php?ToDo=viewOrders');
			}

			// Does this user have permission to edit this order?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
			}

			// Load the customers language file explicitly
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('customers');

			$GLOBALS['FormAction'] = 'SaveUpdatedOrder';
			$GLOBALS['Title'] = GetLang('EditOrder');
			$GLOBALS['Intro'] = GetLang('EditOrderIntro');
			$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndContinueEditing');

			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$GLOBALS['OrderSession'] = isc_html_escape($_POST['orderSession']);
				$sessionId = $_POST['orderSession'];
				$api = $this->GetCartApi($order['orderid']);
			}
			else {
				$this->CleanupOrderManagerSessions();
				unset($_SESSION['ORDER_MANAGER'][$order['orderid']]);
				$sessionId = $order['orderid'];

				// Load the products from the order in to the order session
				$api = $this->GetCartApi($order['orderid']);
				$api->LoadInOrderItems($order['orderid']);
			}

			$GLOBALS['OrderSession'] = $sessionId;

			$this->GetCartApi()->Set('SHIPPING_METHOD', array(
				'methodCost' => $order['ordshipcost'],
				'methodName' => $order['ordshipmethod'],
				'methodModule' => $order['ordershipmodule'],
				'handlingCost' => $order['ordhandlingcost'],
				'methodId' => 'existing'
			));
			$this->GetCartApi()->Set('EXISTING_ORDER', $order['orderid']);

			$this->GetCartApi()->Set('SUBTOTAL_DISCOUNT', $order['orddiscountamount']);

			// set the anonymous email address for anonymous orders
			if ($order['ordcustid'] == 0) {
				$order['anonymousemail'] = $order['ordbillemail'];
			}

			$this->SetupOrderManagementForm($order);

			$GLOBALS['OrderItems'] = $this->GenerateOrderItemsGrid();
			$summary = $this->CalculateOrderSummary($order);
			$GLOBALS['OrderSummary'] = $this->GenerateOrderSummaryTable($summary);

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('order.form');
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
		}

		/**
		 * Set up all of the template variables and predefined values for showing the form to edit an
		 * existing order or create a new order. Will also set up the post variables as values if this
		 * is a post request.
		 *
		 * @param array Optionally, if editing an order, the existing order to use for the default values.
		 */
		protected function SetupOrderManagementForm($order=array())
		{
			$GLOBLS['CurrentTab'] = 0;

			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$postData = $_POST;
			}
			else {
				$postData = $order;
			}

			$orderFields = array(
				'OrderBillFirstName'	=> 'ordbillfirstname',
				'OrderBillLastName'		=> 'ordbilllastname',
				'OrderBillCompany'		=> 'ordbillcompany',
				'OrderBillPhone'		=> 'ordbillphone',
				'OrderBillStreet1'		=> 'ordbillstreet1',
				'OrderBillStreet2'		=> 'ordbillstreet2',
				'OrderBillSuburb'		=> 'ordbillsuburb',
				'OrderBillZip'			=> 'ordbillzip',
				'OrderShipFirstName'	=> 'ordshipfirstname',
				'OrderShipLastName'		=> 'ordshiplastname',
				'OrderShipCompany'		=> 'ordshipcompany',
				'OrderShipPhone'		=> 'ordshipphone',
				'OrderShipStreet1'		=> 'ordshipstreet1',
				'OrderShipStreet2'		=> 'ordshipstreet2',
				'OrderShipSuburb'		=> 'ordshipsuburb',
				'OrderShipZip'			=> 'ordshipzip',
				'CustomerEmail'			=> 'custconemail',
				'CustomerPassword'		=> 'custpassword',
				'CustomerPassword2'		=> 'custpassword2',
				'CustomerStoreCredit'	=> 'custstorecredit',
				'CustomerGroup'			=> 'custgroupid',
				'CustomerType'			=> 'customerType',
				'OrderComments'			=> 'ordcustmessage',
				'OrderNotes'			=> 'ordnotes',
				'OrderId'				=> 'orderid',
				'OrderTrackingNo'		=> 'ordtrackingno',
				'AnonymousEmail'		=> 'anonymousemail',
			);

			$GLOBALS['HideSelectedCustomer'] = 'display: none';
			$GLOBALS['HideCustomerSearch'] = '';
			$GLOBALS['HideAddressSelects'] = 'display: none';

			if(isset($postData['ordcustid']) && $postData['ordcustid'] > 0) {
				$GLOBALS['CurrentTab'] = 1;
				$GLOBALS['CustomerType'] = 'existing';
				$query = "
					SELECT *
					FROM [|PREFIX|]customers WHERE customerid='".(int)$postData['ordcustid']."'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$existingCustomer = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
				if($existingCustomer['customerid']) {
					$GLOBALS['HideSelectedCustomer'] = '';
					$GLOBALS['HideCustomerSearch'] = 'display: none';
					$GLOBALS['HideHistoryLink'] = 'display: none';

					$GLOBALS['CustomerId'] = $existingCustomer['customerid'];
					$GLOBALS['CustomerFirstName'] = isc_html_escape($existingCustomer['custconfirstname']);
					$GLOBALS['CustomerLastName'] = isc_html_escape($existingCustomer['custconlastname']);

					$GLOBALS['CustomerPhone'] = '';
					if($existingCustomer['custconphone']) {
						$GLOBALS['CustomerPhone'] = isc_html_escape($existingCustomer['custconphone']) . '<br />';
					}

					$GLOBALS['CustomerEmail'] = '';
					if($existingCustomer['custconemail']) {
						$GLOBALS['CustomerEmail'] = '<a href="mailto:'.isc_html_escape($existingCustomer['custconemail']).'">'.isc_html_escape($existingCustomer['custconemail']).'</a><br />';
					}

					$GLOBALS['CustomerCompany'] = '';
					if($existingCustomer['custconcompany']) {
						$GLOBALS['CustomerCompany'] = isc_html_escape($existingCustomer['custconcompany']).'<br />';
					}

					// Grab the addresses
					$addresses = $this->LoadCustomerAddresses($existingCustomer['customerid']);
					$GLOBALS['AddressJson']  =  'OrderManager.LoadInAddresses('.isc_json_encode($addresses).');';
					if(!empty($addresses)) {
						$GLOBALS['HideAddressSelects'] = '';
						$GLOBALS['DisableAddressSelects'] = 'disabled="disabled"';
					}
					$GLOBALS['SelectedCustomer'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrdersCustomerSearchResult');
				}
			}
			else if(isset($postData['ordcustid']) && $postData['ordcustid'] == 0) {
				if(!isset($postData['customerType'])) {
					$GLOBALS['CurrentTab'] = 2;
				}
				else if($postData['customerType'] == 'anonymous') {
					$GLOBALS['CurrentTab'] = 2;
				}
				else {
					$GLOBALS['CurrenTab'] = 1;
				}
			}

			/**
			 * Customer and order custom fields
			 */
			$GLOBALS['OrderCustomFormFieldsAccountFormId'] = FORMFIELDS_FORM_ACCOUNT;
			$GLOBALS['OrderCustomFormFieldsBillingFormId'] = FORMFIELDS_FORM_BILLING;
			$GLOBALS['OrderCustomFormFieldsShippingFormId'] = FORMFIELDS_FORM_SHIPPING;
			$GLOBALS['CustomFieldsAccountLeftColumn'] = '';
			$GLOBALS['CustomFieldsAccountRightColumn'] = '';
			$GLOBALS['CustomFieldsBillingColumn'] = '';
			$GLOBALS['CustomFieldsShippingColumn'] = '';

			$formIdx = array(FORMFIELDS_FORM_ACCOUNT, FORMFIELDS_FORM_BILLING, FORMFIELDS_FORM_SHIPPING);

			$fieldMap = array(
				'FirstName'		=> 'firstname',
				'LastName'		=> 'lastname',
				'CompanyName'	=> 'company',
				'Phone'			=> 'phone',
				'AddressLine1'	=> 'street1',
				'AddressLine2'	=> 'street2',
				'City'			=> 'suburb',
				'Zip'			=> 'zip',
				'Country'		=> 'country',
				'State'			=> 'state'
			);

			/**
			 * Now process the forms
			 */
			foreach ($formIdx as $formId) {
				$formSessionId = 0;
				if ($formId == FORMFIELDS_FORM_ACCOUNT) {

					/**
					 * We are only using the real custom fields for the account section, so check here
					 */
					if (!gzte11(ISC_MEDIUMPRINT)) {
						continue;
					}

					if (isset($existingCustomer['custformsessionid'])) {
						$formSessionId = $existingCustomer['custformsessionid'];
					}
				} else {
					if (isset($postData['ordformsessionid'])) {
						$formSessionId = $postData['ordformsessionid'];
					}
				}

				/**
				 * This part here gets all the existing fields
				 */
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields($formId, true);
				} else if (isId($formSessionId)) {
					$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields($formId, false, $formSessionId);
				} else {
					$fields = $GLOBALS['ISC_CLASS_FORM']->getFormFields($formId);
				}

				/**
				 * Get any selected country and state. This needs to be separate as we physically
				 * print out each form field at a time so we need this information before hand
				 */
				if ($formId !== FORMFIELDS_FORM_ACCOUNT) {
					$countryId = GetCountryIdByName(GetConfig('CompanyCountry'));
					$stateFieldId = 0;
					foreach (array_keys($fields) as $fieldId) {
						if (isc_strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'state') {
							$stateFieldId = $fieldId;
						} else if (isc_strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'country') {
							if ($_SERVER['REQUEST_METHOD'] == 'POST') {
								$country = $fields[$fieldId]->getValue();
							} if ($formId == FORMFIELDS_FORM_BILLING) {
								$country = @$order['ordbillcountry'];
							} else {
								$country = @$order['ordshipcountry'];
							}

							if (trim($country) !== '') {
								$countryId = GetCountryIdByName($country);
							}
						}
					}
				}

				/**
				 * Now we construct and build each form field
				 */
				$column = 0;
				foreach (array_keys($fields) as $fieldId) {

					if ($formId == FORMFIELDS_FORM_ACCOUNT) {

						if ($fields[$fieldId]->record['formfieldprivateid'] !== '' || !gzte11(ISC_MEDIUMPRINT)) {
							continue;
						}

						$fieldHTML = $fields[$fieldId]->loadForFrontend();

						if (($column%2) > 0) {
							$varname = 'CustomFieldsAccountLeftColumn';
						} else {
							$varname = 'CustomFieldsAccountRightColumn';
						}
					} else {

						/**
						 * We are using all the custom fields for the billing/shipping are, so check here
						 */
						if (!gzte11(ISC_MEDIUMPRINT) && $fields[$fieldId]->record['formfieldprivateid'] == '') {
							continue;
						}

						if ($formId == FORMFIELDS_FORM_BILLING) {
							$varname = 'CustomFieldsBillingColumn';
						} else {
							$varname = 'CustomFieldsShippingColumn';
						}

						/**
						 * Set the value for the private fields if this is NOT a post
						 */
						if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $fields[$fieldId]->record['formfieldprivateid'] !== '') {

							$key = @$fieldMap[$fields[$fieldId]->record['formfieldprivateid']];
							if (trim($key) !== '') {
								if ($formId == FORMFIELDS_FORM_BILLING) {
									$key = 'ordbill' . $key;
								} else {
									$key = 'ordship' . $key;
								}

								if (array_key_exists($key, $order)) {
									$fields[$fieldId]->setValue($order[$key]);
								}
							}
						}

						/**
						 * Add in any of the country/state lists if needed
						 */
						if (isc_strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'country') {
							$fields[$fieldId]->setOptions(array_values(GetCountryListAsIdValuePairs()));

							if ($fields[$fieldId]->getValue() == '') {
								$fields[$fieldId]->setValue(GetConfig('CompanyCountry'));
							}

							$fields[$fieldId]->addEventHandler('change', 'FormFieldEvent.SingleSelectPopulateStates', array('countryId' => $fieldId, 'stateId' => $stateFieldId, 'inOrdersAdmin' => true));

						} else if (isc_strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'state' && isId($countryId)) {
							$stateOptions = GetStateListAsIdValuePairs($countryId);
							if (is_array($stateOptions) && !empty($stateOptions)) {
								$fields[$fieldId]->setOptions($stateOptions);
							}
						}

						/**
						 * We also do not what these fields
						 */
						if (isc_strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'savethisaddress' || isc_strtolower($fields[$fieldId]->record['formfieldprivateid']) == 'shiptoaddress') {
							continue;
						}
					}

					$GLOBALS[$varname] .= $fields[$fieldId]->loadForFrontend() . "\n";
					$column++;
				}
			}

			/**
			 * Add this to generate our JS event script
			 */
			$GLOBALS['FormFieldEventData'] = $GLOBALS['ISC_CLASS_FORM']->buildRequiredJS();

			/**
			 * Do we display the customer custom fields?
			 */
			if (!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS['HideCustomFieldsAccountLeftColumn'] = 'none';
				$GLOBALS['HideCustomFieldsAccountRightColumn'] = 'none';
			} else {
				if ($GLOBALS['CustomFieldsAccountLeftColumn'] == '') {
					$GLOBALS['HideCustomFieldsAccountLeftColumn'] = 'none';
				}

				if ($GLOBALS['CustomFieldsAccountRightColumn'] == '') {
					$GLOBALS['HideCustomFieldsAccountRightColumn'] = 'none';
				}
			}

			$defaultValues = array(
				'custgroupid' => 0,
				'ordstatus' => 7
			);

			foreach($defaultValues as $postField => $default) {
				if(!isset($postData[$postField])) {
					$postData[$postField] = $default;
				}
			}

			foreach($orderFields as $templateField => $orderField) {
				if(!isset($postData[$orderField])) {
					$GLOBALS[$templateField] = '';
				}
				else {
					$GLOBALS[$templateField] = isc_html_escape($postData[$orderField]);
				}
			}

			if(isset($postData['ordbillsaveAddress'])) {
				$GLOBALS['OrderBillSaveAddress'] = 'checked="checked"';
			}

			if(isset($postData['ordshipsaveAddress'])) {
				$GLOBALS['OrderShipSaveAddress'] = 'checked="checked"';
			}

			if(isset($postData['shippingUseBilling'])) {
				$GLOBALS['ShippingUseBillingChecked'] = 'checked="checked"';
			}

			if(isset($postData['billingUseShipping'])) {
				$GLOBALS['BillingUseShippingChecked'] = 'checked="checked"';
			}

			$GLOBALS['OrderStatusOptions'] = $this->GetOrderStatusOptions($postData['ordstatus']);

			$customerClass = GetClass('ISC_ADMIN_CUSTOMERS');
			$GLOBALS['CustomerGroupOptions'] = $customerClass->GetCustomerGroupsAsOptions($postData['custgroupid']);

			// was a payment module previously recorded for the existing order?
			// we should just show a summary
			if (!empty($order['orderpaymentmodule'])) {
				$GLOBALS['PaymentMethod'] = GetLang('PaymentDetails');
				$GLOBALS['PaymentDetails'] = GetLang(
					'PaymentDetailInfo',
					array(
						'amount' => FormatPriceInCurrency($order['ordgatewayamount'],
						$order['ordcurrencyid']),
						'provider' => $order['orderpaymentmethod']
					)
				);

				$GLOBALS['DisplayPaymentStatus'] = 'none';
				$GLOBALS['DisplayTransactionId'] = 'none';
				if (!empty($order['ordpayproviderid'])) {
					$GLOBALS['TransactionId'] = $order['ordpayproviderid'];
					$GLOBALS['DisplayTransactionId'] = '';
				}
				if (!empty($order['ordpaymentstatus'])) {
					$GLOBALS['PaymentStatus'] = GetLang('Payment')." ".ucfirst($order['ordpaymentstatus']);
					$GLOBALS['DisplayPaymentStatus'] = '';
				}
				$GLOBALS['PaymentMethodsList'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderPaymentDetails');
			}
			else {
				// load list of payment providers
				$GLOBALS['PaymentMethod'] = GetLang('PaymentMethod');
				$GLOBALS['PaymentMethodsList'] = $this->GetPaymentProviderList($postData);
			}

			if(!empty($order)) {
				$GLOBALS['HideEmailInvoice'] = 'display: none';
			}
			else if(isset($postData['emailinvoice'])) {
				$GLOBALS['EmailInvoiceChecked'] = 'checked="checked"';
			}

			$GLOBALS['Message'] = GetFlashMessageBoxes();
		}

		/**
		 * Show the form to create a new order.
		 */
		protected function AddOrder()
		{
			$GLOBALS['BreadcrumEntries'][GetLang('AddAnOrder')] = 'index.php?ToDo=addOrder';

			if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_Orders)) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				return;
			}

			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();

			// Load the customers language file explicitly
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('customers');

			$GLOBALS['FormAction'] = 'saveNewOrder';
			$GLOBALS['Title'] = GetLang('AddAnOrder');
			$GLOBALS['Intro'] = GetLang('AddOrderIntro');
			$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndAddAnother');
			$GLOBALS['CurrentTab'] = 0;
			$GLOBALS['HideCustomerPasswordReminder'] = 'display: none';

			if($_SERVER['REQUEST_METHOD'] == 'POST') {
				$GLOBALS['OrderSession'] = isc_html_escape($_POST['orderSession']);
				$sessionId = $_POST['orderSession'];
			}
			else {
				$this->CleanupOrderManagerSessions();
				do {
					$sessionId = md5(uniqid());
				} while(isset($_SESSION['ORDER_MANAGER'][$sessionId]));
			}

			$api = $this->GetCartApi($sessionId);

			$GLOBALS['OrderSession'] = $sessionId;

			$this->SetupOrderManagementForm();

			$GLOBALS['OrderItems'] = $this->GenerateOrderItemsGrid();
			$GLOBALS['OrderSummary'] = $this->GenerateOrderSummaryTable();

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('order.form');
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
		}

		/**
		 * Clean up and remove any order sessions that were created but not updated
		 * over 24 hours ago.
		 */
		protected function CleanupOrderManagerSessions()
		{
			// Delete any order management sessions not updated within the last 24 hours
			if(!isset($_SESSION['ORDER_MANAGER'])) {
				return;
			}

			foreach($_SESSION['ORDER_MANAGER'] as $sessionId => $orderSession) {
				if(time()-$orderSession['LAST_UPDATED'] > 86400) {
					unset($_SESSION['ORDER_MANAGER'][$sessionId]);
				}
			}
		}

		/**
		 * Save a new order in the database.
		 */
		protected function SaveNewOrder()
		{
			if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Add_Orders)) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				return;
			}

			$message = '';
			if(!$this->ValidateOrder($_POST, $message)) {
				FlashMessage($message, MSG_ERROR);
				$this->AddOrder();
				return;
			}

			$orderId = $this->CommitOrder($_POST);
			if(!$orderId) {
				$error = $GLOBALS['ISC_CLASS_DB']->GetErrorMsg();
				FlashMessage(GetLang('ProblemSavingOrder').$error, MSG_ERROR);
				$this->AddOrder();
				return;
			}
			else {
				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($orderId);
				if ($GLOBALS['PaymentFailed']) {
					$location = 'index.php?ToDo=editOrder&orderId=' . $orderId;
				}
				else {
					if(isset($_REQUEST['addAnother'])) {
						$location = 'index.php?ToDo=addOrder';
					}
					else {
						$location = 'index.php?ToDo=viewOrders&selectOrder=' . $orderId;
					}
				}
				FlashMessage(sprintf(GetLang('OrderCreated'), $orderId, $orderId), MSG_SUCCESS, $location);
			}
		}

		/**
		 * Save an updated order in the database.
		 */
		protected function SaveUpdatedOrder()
		{
			if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Orders)) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				return;
			}

			$order = GetOrder($_REQUEST['orderid']);
			if(!isset($order['orderid'])) {
				FlashMessage(GetLang('InvalidOrder'), MSG_ERROR, 'index.php?ToDo=viewOrders');
			}

			// Does this user have permission to edit this order?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $order['ordvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewOrders');
			}

			$message = '';
			// Validate and if there's an error, show the edit page again for this order
			if(!$this->ValidateOrder($_POST, $message)) {
				FlashMessage($message, MSG_ERROR);
				$this->EditOrder();
				return;
			}


			// OK, so now it's valid, save the wrapping in the database
			if(!$this->CommitOrder($_POST, $order['orderid'])) {
				$error = $GLOBALS['ISC_CLASS_DB']->GetErrorMsg();
				FlashMessage(GetLang('ProblemSavingOrder').$error, MSG_ERROR);
				$this->EditOrder();
				return;
			}
			else {
				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($order['orderid']);
				if ($GLOBALS['PaymentFailed']) {
					$location = 'index.php?ToDo=editOrder&orderId=' . $order['orderid'];
				}
				else {
					if(isset($_REQUEST['addAnother'])) {
						$location = 'index.php?ToDo=editOrder&orderId='.$order['orderid'];
					}
					else {
						$location = 'index.php?ToDo=viewOrders&selectOrder=' . $order['orderid'];
					}
				}
				FlashMessage(sprintf(GetLang('OrderUpdated'), $order['orderid']), MSG_SUCCESS, $location);
			}
		}

		/**
		 * Validate the supplied information about an order before it is inserted/updated.
		 *
		 * @param array An array of details about the order.
		 * @param string An error message, by reference, if there are any errors.
		 * @return boolean True if the order is valid, false if not.
		 */
		protected function ValidateOrder($data, &$error)
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('customers');

			switch($data['customerType']) {
				case 'anonymous':
					break;
				case 'new':
					$requiredFields = array(
						'custconemail' => GetLang('CustomerEmailRequired'),
						'custpassword' => GetLang('CustomerPasswordRequired'),
						'custpassword2' => GetLang('CustomerPasswordConfirmRequired')
					);
					foreach($requiredFields as $field => $message) {
						if(!isset($data[$field]) || !trim($data[$field])) {
							$error = $message;
							return false;
						}
					}

					// Validate that the email address is actually valid
					if(!is_email_address($data['custconemail'])) {
						$error = GetLang('CustomerEmailInvalue');
						return false;
					}

					// Is the email address already in use?
					$query = "
						SELECT customerid
						FROM [|PREFIX|]customers
						WHERE custconemail='".$GLOBALS['ISC_CLASS_DB']->Quote($data['custconemail'])."'
					";
					if($GLOBALS['ISC_CLASS_DB']->FetchOne($query)) {
						$error = GetLang('CustomerEmailNotUnique');
						return false;
					}
					break;
				case 'existing':
					// Did they choose a customer?
					if(!$data['ordcustid']) {
						$error = GetLang('ErrorSelectACustomer');
						return false;
					}

					// Does the customer they've chosen actually exist?
					$query = "
						SELECT customerid
						FROM [|PREFIX|]customers
						WHERE customerid='".(int)$data['ordcustid']."'
					";
					if(!$GLOBALS['ISC_CLASS_DB']->FetchOne($query)) {
						$error = GetLang('CustomerDoesntExist');
						return false;
					}
			}

			// Are there any items in the order?
			$cartProducts = $this->GetCartApi($data['orderSession'])->GetProductsInCart();
			if(empty($cartProducts)) {
				$error = GetLang('OrderMustContainOneProduct');
				return false;
			}

			$requiredFields = array();

			if ($data['orderid'] == '') {
				$requiredFields['orderpaymentmodule'] = GetLang('InvalidPaymentModule');
			}

			foreach($requiredFields as $field => $message) {
				if(!isset($data[$field]) || !trim($data[$field])) {
					$error = $message;
					return false;
				}
			}

			return true;
		}

		/**
		 * Load and return an array of all of the shipping addresses associated with a particular
		 * customer ID. Will also generate a 'preview string' of all of the address details concatenated.
		 *
		 * @param int The ID of the customer to fetch the addresses for.
		 * @return array An array of addresses.
		 */
		public function LoadCustomerAddresses($customerId)
		{
			$customer = GetClass('ISC_CUSTOMER');
			$addresses = $customer->GetCustomerShippingAddresses($customerId);
			$addressResponse = array();
			foreach($addresses as $address) {
				$fields = array(
					'shipfullname',
					'shipcompany',
					'shipaddress1',
					'shipaddress2',
					'shipcity',
					'shipstate',
					'shipzip',
					'shipcountry',
					'shipcustomfields'
				);

				$formattedAddress = '';
				foreach($fields as $field) {

					/**
					 * Load in the custom fields if we have any
					 */
					if ($field == 'shipcustomfields' && isId($address['shipformsessionid'])) {
						$address[$field] = $GLOBALS['ISC_CLASS_FORM']->getSavedSessionData($address['shipformsessionid']);
						continue;
					}

					if(!isset($address[$field])) {
						continue;
					}
					$formattedAddress .= $address[$field] .', ';
				}

				/**
				 * Because we have both the billing and shipping forms in the one page, we have
				 * to assign the same values to both forms. We need to find out what is the original
				 * form so we can map it to the other
				 */
				if (isset($address['shipcustomfields']) && !empty($address['shipcustomfields'])) {
					$fieldIdx = array_keys($address['shipcustomfields']);
					$formIdx = $GLOBALS['ISC_CLASS_FORM']->findFormIdByFieldId($fieldIdx[0]);

					if (is_array($formIdx) && !empty($formIdx)) {
						$fieldMap = $GLOBALS['ISC_CLASS_FORM']->mapAddressFieldList($formIdx[0], $fieldIdx);
						$newCustom = array();

						/**
						 * OK, we got the map. now we can create our new custom fields data
						 */
						foreach ($fieldMap as $sourceFieldId => $targetFieldId) {
							if (!isset($address['shipcustomfields'][$sourceFieldId])) {
								continue;
							}

							$newCustom[$sourceFieldId] = $address['shipcustomfields'][$sourceFieldId];
							$newCustom[$targetFieldId] = $address['shipcustomfields'][$sourceFieldId];
						}

						$address['shipcustomfields'] = $newCustom;
					}
				}

				$formattedAddress = rtrim($formattedAddress, ', ');
				$address['preview'] = $formattedAddress;
				$addressResponse[] = $address;
			}
			return $addressResponse;
		}

		/**
		 * Actually save a new order or an updated existing order in the database
		 * after it's been validated.
		 *
		 * @param array An array of details about the order to save.
		 * @param int The ID of the existing order if we're updating an order.
		 * @return boolean True if successful, false if not.
		 */
		protected function CommitOrder($data, $orderId=0)
		{
			$GLOBALS['ISC_CLASS_DB']->StartTransaction();

			/**
			 * We need to find our billing/shipping details from the form fields first as it is
			 * also used in creating the customer
			 */
			$billingDetails = array();
			$shippingDetails = array();
			$billingFields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_BILLING, true);
			$shippingFields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_SHIPPING, true);
			$fields = $billingFields + $shippingFields;

			$addressMap = array(
				'FirstName' => 'firstname',
				'LastName' => 'lastname',
				'CompanyName' => 'company',
				'AddressLine1' => 'address1',
				'AddressLine2' => 'address2',
				'City' => 'city',
				'State' => 'state',
				'Zip' => 'zip',
				'State' => 'state',
				'Country' => 'country',
				'Phone' => 'phone'
			);

			foreach ($fields as $fieldId=>$thisField) {
				$privateName = $thisField->record['formfieldprivateid'];

				if ($privateName == '' || !array_key_exists($privateName, $addressMap)) {
					continue;
				}

				if ($thisField->record['formfieldformid'] == FORMFIELDS_FORM_BILLING) {
					$detailsVar =& $billingDetails;
				} else {
					$detailsVar =& $shippingDetails;
				}

				/**
				 * Find the country
				 */
				if (isc_strtolower($privateName) == 'country') {
					$detailsVar['shipcountry'] = $thisField->getValue();
					$detailsVar['shipcountryid'] = GetCountryByName($thisField->getValue());
					if (!isId($detailsVar['shipcountryid'])) {
						$detailsVar['shipcountryid'] = 0;
					}

				/**
				 * Else find the state
				 */
				} else if (isc_strtolower($privateName) == 'state') {
					$detailsVar['shipstate'] = $fields[$fieldId]->getValue();
					$stateInfo = GetStateInfoByName($detailsVar['shipstate']);

					if ($stateInfo && isId($stateInfo['stateid'])) {
						$detailsVar['shipstateid'] = $stateInfo['stateid'];
					} else {
						$detailsVar['shipstateid'] = 0;
					}

				/**
				 * Else the rest
				 */
				} else {
					$detailsVar['ship' . $addressMap[$privateName]] = $thisField->getValue();
				}
			}

			// If we're creating an account for this customer, create it now
			if($data['ordcustid'] == 0 && $data['customerType'] == 'new') {
				$customerData = array(
					'custconemail' => $data['custconemail'],
					'custpassword' => $data['custpassword'],
					'custconfirstname' => $billingDetails['shipfirstname'],
					'custconlastname' => $billingDetails['shiplastname'],
					'custconcompany' => $billingDetails['shipcompany'],
					'custconphone' => $billingDetails['shipphone'],
					'customertoken' => GenerateCustomerToken(),
					'custgroupid' => $data['custgroupid'],
					'custstorecredit' => DefaultPriceFormat($data['custstorecredit'])
				);

				/**
				 * Save the customer custom fields
				 */
				if (gzte11(ISC_MEDIUMPRINT)) {
					$formSessionId = $GLOBALS['ISC_CLASS_FORM']->saveFormSession(FORMFIELDS_FORM_ACCOUNT);
					if (isId($formSessionId)) {
						$customerData['custformsessionid'] = $formSessionId;
					}
				}

				$data['ordcustid'] = $this->customerEntity->add($customerData);

				if(!$data['ordcustid']) {
					$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
					return false;
				}
			}

			// we need to retrieve the location details so we can calculate the tax
			$order = $_REQUEST;

			/**
			* When the 'use billing address' option is set the form field data isn't submitted because the fields are disabled
			*/
			if (isset($order['shippingUseBilling']) && $order['shippingUseBilling'] == 1) {
				$shippingDetails = $billingDetails;
			}
			elseif (isset($order['billingUseShipping']) && $order['billingUseShipping'] == 1) {
				$billingDetails = $shippingDetails;
			}

			$order['ordbillcountry'] = $billingDetails['shipcountry'];
			$order['ordbillstate'] = $billingDetails['shipstate'];
			$order['ordbillzip'] = $billingDetails['shipzip'];
			$order['ordshipcountry'] = $shippingDetails['shipcountry'];
			$order['ordshipstate'] = $shippingDetails['shipstate'];
			$order['ordshipzip'] = $shippingDetails['shipzip'];

			$this->GetCartApi()->Set('SUBTOTAL_DISCOUNT', DefaultPriceFormat($_REQUEST['orddiscountamount']));

			$orderSummary = $this->CalculateOrderSummary($order, false);

			$defaultCurrency = GetDefaultCurrency();

			$email = '';
			if(isset($data['custconemail']) && $data['customerType'] == 'new') {
				$email = $data['custconemail'];
			}
			else if(isset($data['anonymousemail']) && $data['customerType'] == 'anonymous') {
				$email = $data['anonymousemail'];
			}

			$orderTotal = $orderSummary['total'] - $orderSummary['subtotalDiscount'];

			// NB we dont want to set the payment method just yet .. should wait for successfull processing
			$newOrder = array(
				'ordcustid' => $data['ordcustid'],
				'billingaddress' => $billingDetails,
				'ordbillemail' => $email,
				'ordbillphone' => $billingDetails['shipphone'],
				'ordgeoipcountry' => $billingDetails['shipcountry'],
				'ordgeoipcountrycode' => GetCountryISO2ByName($billingDetails['shipcountry']),
				'ordvendorid' => $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId(),
				'giftcertificates' => $this->GetCartApi()->GetGiftCertificates(),
				'ordshipcost' => $orderSummary['shippingCost'],
				'ordhandlingcost' => $orderSummary['handlingCost'],
				'ordtoken' => GenerateOrderToken(),
				'ordsubtotal' => $orderSummary['itemsubtotal'],
				'ordtaxtotal' => $orderSummary['taxCost'],
				'ordtaxrate' => $orderSummary['taxRate'],
				'ordtaxname' => $orderSummary['taxName'],
				'ordgiftcertificateamount' => $orderSummary['giftCertificateTotal'],
				'ordtotalincludestax' => $orderSummary['taxIncluded'],
				'orderpaymentmodule'		=> '',
				'ordgatewayamount'			=> 0,
				'ordshipmethod' => $orderSummary['shippingMethod'],
				'ordershipmodule' => $orderSummary['shippingModule'],
				'ordtotalamount' => $orderTotal,
				'ordstatus' => 0,
				'ordisdigital' => (int)$this->GetCartApi()->AllProductsInCartAreIntangible(),
				'ordcurrencyid' => $defaultCurrency['currencyid'],
				'ordcurrencyexchangerate' => 0,
				'ordcustmessage'=> @$data['ordcustmessage'],
				'ordnotes' => @$data['ordnotes'],
				'products' => $this->GetCartApi()->GetProductsInCart(),
				'ordtrackingno' => $data['ordtrackingno'],
				'orddiscountamount' => $orderSummary['subtotalDiscount']
			);

			if(isset($data['ordbillsaveAddress'])) {
				$newOrder['billingaddress']['saveAddress'] = 1;
				if (gzte11(ISC_MEDIUMPRINT)) {
					$newOrder['billingaddress']['shipformsessionid'] = $GLOBALS['ISC_CLASS_FORM']->saveFormSession(FORMFIELDS_FORM_BILLING);
				}
			}

			if (!empty($data['orderpaymentmodule'])) {
				if (in_array($data['orderpaymentmodule'], array('manual', 'giftcertificate', 'storecredit', 'custom'))) {
					$newOrder['orderpaymentmodule'] = $data['orderpaymentmodule'];
				}

				if($newOrder['orderpaymentmodule'] == 'manual') {
					$newOrder['orderpaymentmethod'] = GetLang('ManualPayment');
				}
				else if($newOrder['orderpaymentmodule'] == 'giftcertificate') {
					$newOrder['ordgiftcertificateamount'] = $orderSummary['total'];
				}
				else if($newOrder['orderpaymentmodule'] == 'storecredit') {
					$newOrder['ordstorecreditamount'] = $orderSummary['total'];
				}
				else if($newOrder['orderpaymentmodule'] == 'custom') {
					$newOrder['orderpaymentmethod'] = $data['paymentField']['custom']['name'];
				}
			}

			if(!$this->GetCartApi()->AllProductsInCartAreIntangible()) {

				if(isset($data['shippingUseBilling']) && $data['shippingUseBilling'] == 1) {
					$newOrder['shippingaddress'] = $newOrder['billingaddress'];

				} else {
					$newOrder['shippingaddress'] = $shippingDetails;
					if (isset($data['ordshipsaveAddress']) && gzte11(ISC_MEDIUMPRINT)) {

						/**
						 * This is a bit tricky. We need to convert these shipping fields to use the billing
						 * field IDs when saving in the shipping_addresses table as they all use the billing
						 * fields on the frontend
						 */
						$shippingFields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_SHIPPING, true);
						$shippingKeys = array_keys($shippingFields);
						$shippingMap = $GLOBALS['ISC_CLASS_FORM']->mapAddressFieldList(FORMFIELDS_FORM_SHIPPING, $shippingKeys);
						$shippingSessData = array();

						foreach ($shippingMap as $fieldId => $newBillingId) {
							if ($shippingFields[$fieldId]->record['formfieldprivateid'] !== '') {
								continue;
							}

							$shippingSessData[$newBillingId] = $shippingFields[$fieldId]->getValue();
						}

						$newOrder['shippingaddress']['shipformsessionid'] = $GLOBALS['ISC_CLASS_FORM']->saveFormSessionManual($shippingSessData);
					}
				}

				if(isset($data['ordshipsaveAddress'])) {
					$newOrder['shippingaddress']['saveAddress'] = 1;
				}

			}

			if($orderId > 0) {
				$existingOrder = GetOrder($orderId);
				$newOrder['ordvendorid'] = $existingOrder['ordvendorid'];
				$newOrder['extrainfo'] = @unserialize($existingOrder['extrainfo']);
				$newOrder['ordgatewayamount'] = $existingOrder['ordgatewayamount'];
				$newOrder['ordstorecreditamount'] = $existingOrder['ordstorecreditamount'];
				$newOrder['ordcurrencyid'] = $existingOrder['ordcurrencyid'];
				$newOrder['ordcurrencyexchangerate'] = $existingOrder['ordcurrencyexchangerate'];
				$newOrder['orderid'] = $orderId;
				$newOrder['orddate'] = $existingOrder['orddate'];
				$newOrder['ordipaddress'] = $existingOrder['ordipaddress'];
				// set by default the last used payment module
				$newOrder['orderpaymentmodule']= $existingOrder['orderpaymentmodule'];
			}

			/**
			 * Save the billing/shipping custom fields for the order
			 */
			if (gzte11(ISC_MEDIUMPRINT)) {
				if (isId($orderId) && isset($existingOrder['ordformsessionid']) && isId($existingOrder['ordformsessionid'])) {
					$GLOBALS['ISC_CLASS_FORM']->saveFormSession(array(FORMFIELDS_FORM_BILLING, FORMFIELDS_FORM_SHIPPING), true, $existingOrder['ordformsessionid']);
				} else {
					$formSessionId = $GLOBALS['ISC_CLASS_FORM']->saveFormSession(array(FORMFIELDS_FORM_BILLING, FORMFIELDS_FORM_SHIPPING));
					if (isId($formSessionId)) {
						$newOrder['ordformsessionid'] = $formSessionId;
					}
				}
			}

			if(isset($existingOrder)) {
				if(!$this->orderEntity->edit($newOrder)) {
					$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
					return false;
				}
			}
			else {
				$data['orderid'] = $this->orderEntity->add($newOrder);
				if(!$data['orderid']) {
					$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
					return false;
				}
			}

			// If one or more gift certificates were used we need to apply them to this order
			if($newOrder['ordgiftcertificateamount'] > 0 && isset($newOrder['giftcertificates']) && !empty($newOrder['giftcertificates'])) {
				$usedCertificates = array();
				$GLOBALS['ISC_CLASS_GIFT_CERTIFICATES'] = GetClass('ISC_GIFTCERTIFICATES');
				$GLOBALS['ISC_CLASS_GIFT_CERTIFICATES']->ApplyGiftCertificatesToOrder($newOrder['orderid'], $newOrder['ordtotalamount'], $newOrder['giftcertificates'], $usedCertificates);
			}

			$GLOBALS['ISC_CLASS_DB']->CommitTransaction();

			// Does the payment method need to process a payment?
			// Only process payments for new orders since providers wont let a transaction reference (order id) be used more than once
			$GLOBALS['PaymentFailed'] = false;
			if ($orderTotal > 0 && ($orderId == 0 || $existingOrder['ordpaymentstatus'] == '') && !empty($data['orderpaymentmodule'])) {
				$provider = null;
				$providerSuccess = false;
				$gatewayAmount = $orderSummary['adjustedTotalCost'];

				// Did the payment method have any info it needs to save? Save it
				GetModuleById('checkout', $provider, $data['orderpaymentmodule']);
				if(is_object($provider)) {
					if (method_exists($provider, 'SaveManualPaymentFields')) {
						$fields = $data['paymentField'][$data['orderpaymentmodule']];
						$provider->SaveManualPaymentFields(GetOrder($data['orderid'], false, false), $fields);
					}

					if (method_exists($provider, 'ProcessManualPayment')) {
						$fields = $data['paymentField'][$data['orderpaymentmodule']];
						$myOrder = GetOrder($data['orderid']);
						// set the order token as required by various payment methods
						ISC_SetCookie('SHOP_ORDER_TOKEN', $myOrder['ordtoken'], time() + (3600*24), true);
						// make the token immediately available
						$_COOKIE['SHOP_ORDER_TOKEN'] = $myOrder['ordtoken'];
						$result = $provider->ProcessManualPayment($myOrder, $fields);
						if ($result['result']) {

							if ($myOrder['ordisdigital']) {
								$newStatus = ORDER_STATUS_COMPLETED;
							}
							else {
								$newStatus = ORDER_STATUS_AWAITING_FULFILLMENT;
							}
							$data['ordstatus'] = $newStatus;

							$providerSuccess = true;
							$gatewayAmount = $result['amount'];

							FlashMessage(GetLang('OrderPaymentSuccess', array('amount' => FormatPrice($gatewayAmount), 'orderId' => $data['orderid'], 'provider' => $provider->GetName())), MSG_SUCCESS);
						}
						else {
							FlashMessage(GetLang('OrderPaymentFail', array('orderId' => $data['orderid'], 'provider' => $provider->GetName(), 'reason' => $result['message'])), MSG_ERROR);
							// we should redirect to the edit page if payment failed
							$GLOBALS['PaymentFailed'] = true;
						}
					}
					else {
						// all manual/offline methods will always be successfull
						$providerSuccess = true;
					}

					// was payment successfull? record payment info for the order
					if ($providerSuccess) {
						$updatedOrder = array(
							'orderpaymentmethod' 	=> $provider->GetDisplayName(),
							'orderpaymentmodule'	=> $provider->GetId(),
							'ordgatewayamount'		=> $gatewayAmount
						);

						$GLOBALS['ISC_CLASS_DB']->UpdateQuery("orders", $updatedOrder, "orderid='".(int)$data['orderid']. "'");
					}
				}
			}


			if($data['ordstatus'] != $newOrder['ordstatus']) {
				UpdateOrderStatus($data['orderid'], $data['ordstatus'], false);
			}

			// If we're emailing the customer about their order, send it now
			if(isset($data['emailinvoice']) && $data['emailinvoice'] == 1) {
				EmailInvoiceToCustomer($data['orderid']);
			}

			unset($_SESSION['ORDER_MANAGER'][$data['orderSession']]);

			return $data['orderid'];
		}

		/**
		 * Generate a grid/table containing all of the items in an order.
		 * Also generates an additional hidden row to use as the 'template'
		 * when the + icon to add a new item is clicked.
		 *
		 * @return string The generated grid of items in the order.
		 */
		public function GenerateOrderItemsGrid()
		{
			$itemGrid = '';
			$orderItems = $this->GetCartApi()->GetProductsInCart();
			if(empty($orderItems)) {
				$itemGrid .= $this->GenerateOrderItemRow(0);
			}

			foreach($orderItems as $rowId => $product) {
				$itemGrid .= $this->GenerateOrderItemRow($rowId, $product);
			}

			$itemGrid .= $this->GenerateOrderItemRow('rowtemplate', array(), true);
			return $itemGrid;
		}

		/**
		 * Calculate all of the summary information to show below the list of items
		 * when editing/creating an order (totals, shipping etc)
		 *
		 * @param array If editing an order, the existing order information.
		 * @return array Array of summary details about the order.
		 */
		public function CalculateOrderSummary($order=array(), $existingOrder = true)
		{
			if(empty($order)) {
				$order = $_REQUEST;
				$existingOrder = false;
			}

			$orderSummary = array(
				'total' => 0
			);

			$orderSummary['subtotal'] = $this->GetCartApi()->GetCartSubTotal(false, null, false); // No coupons
			$orderSummary['total'] += $orderSummary['subtotal'];
			$orderSummary['subtotalDiscount'] = $this->GetCartApi()->Get('SUBTOTAL_DISCOUNT');

			$couponTotal = 0;
			foreach($this->GetCartApi()->GetAppliedCouponCodes() as $coupon) {
				$orderSummary['total'] -= $coupon['coupontotal'];
			}

			if($orderSummary['total'] < 0) {
				$orderSummary['total'] = 0;
			}

			// we need to store the discounted subtotal as the 'subtotal' here is for the original/un-discounted prices
			$orderSummary['itemsubtotal'] = $orderSummary['total'];

			$orderSummary['wrappingCost'] = $this->GetCartApi()->GetWrappingCost();
			$orderSummary['subtotal'] -= $orderSummary['wrappingCost'];

			$shippingMethod = $this->GetCartApi()->Get('SHIPPING_METHOD');
			if(is_array($shippingMethod) && !empty($shippingMethod)) {
				$orderSummary['shippingCost'] = $shippingMethod['methodCost'];
				$orderSummary['shippingMethod'] = $shippingMethod['methodName'];
				$orderSummary['shippingModule'] = $shippingMethod['methodModule'];
				$orderSummary['handlingCost'] = $shippingMethod['handlingCost'];
			}
			else if($existingOrder) {
				$orderSummary['shippingCost'] = $order['ordshipcost'];
				$orderSummary['shippingMethod'] = $order['ordshipmethod'];
				$orderSummary['shippingModule'] = $order['ordershipmodule'];
				$orderSummary['handlingCost'] = $order['ordhandlingcost'];
			}
			else {
				$orderSummary['shippingCost'] = 0;
				$orderSummary['shippingMethod'] = '';
				$orderSummary['shippingModule'] = '';
				$orderSummary['handlingCost'] = 0;
			}

			$orderSummary['total'] += $orderSummary['shippingCost'];
			$orderSummary['total'] += $orderSummary['handlingCost'];

			// Set some default values incase tax isn't being applied
			$orderSummary['taxCost'] = 0;
			$orderSummary['taxIncluded'] = 0;
			$orderSummary['taxName'] = GetLang('Tax');
			$orderSummary['taxRate'] = 0;

			// we need to display
			if($existingOrder) {
				$orderSummary['taxCost'] = $order['ordtaxtotal'];
				$orderSummary['taxIncluded'] = $order['ordtotalincludestax'];
				if ($order['ordtaxname'] != '') {
					$orderSummary['taxName'] = $order['ordtaxname'];
				}
				$orderSummary['taxRate'] = $order['ordtaxrate'];
			}
			else {
				// Need to calculate the tax
				$billingAddress = array(
					'shipzip' => '',
					'shipstate' => '',
					'shipcountry' => '',
				);
				$shippingAddress = $billingAddress;

				foreach(array_keys($billingAddress) as $field) {
					$fieldName = str_replace('ship', '', $field);
					if(isset($order['ordbill'.$fieldName])) {
						$billingAddress[$field] = $order['ordbill'.$fieldName];
					}
					if(isset($order['ordship'.$fieldName])) {
						$shippingAddress[$field] = $order['ordship'.$fieldName];
					}
				}

				$customerClass = GetClass('ISC_CUSTOMER');
				$salesTaxData = $customerClass->GetSalesTaxRate($billingAddress, $shippingAddress);

				// Tax needs to be applied
				if($salesTaxData['tax_rate'] > 0) {
					$orderSummary['taxName'] = $salesTaxData['tax_name'];
					$orderSummary['taxRate'] = $salesTaxData['tax_rate'];

					$taxableTotal = 0;
					$cartProducts = $this->GetCartApi()->GetProductsInCart();
					foreach($cartProducts as $product) {
						if(!isset($product['data']) || $product['data']['prodtype'] != PT_GIFTCERTIFICATE && (!array_key_exists('prodistaxable', $product['data']) || $product['data']['prodistaxable'] == 1)) {
							// has this product had a coupon applied to it? use the discounted price if so
							if (isset($product['discount_price'])) {
								$prod_price = $product['discount_price'];
							}
							else {
								$prod_price = $product['product_price'];
							}
							$taxableTotal += ($prod_price * $product['quantity']);
						}
					}
					$taxableTotal += $orderSummary['wrappingCost'];

					// base taxable total
					$baseTotal = $taxableTotal;

					// Calculating tax based on the subtotal + shipping (and also handling if it's included in the shipping)
					if($salesTaxData['tax_based_on'] == 'subtotal_and_shipping') {
						if ($salesTaxData['tax_shipping'] == 0 || ($salesTaxData['tax_shipping'] == 1 && $taxableTotal > 0)) {
							$baseTotal += $orderSummary['shippingCost'];
						}
					}

					if(GetConfig('PricesIncludeTax')) {
						$orderSummary['taxCost'] = ($baseTotal / (100 + $salesTaxData['tax_rate'])) * $salesTaxData['tax_rate'];
						$orderSummary['taxIncluded'] = 1;
					}
					else {
						$orderSummary['taxCost'] = ($baseTotal / 100) * $salesTaxData['tax_rate'];
					}
				}
			}

			if($orderSummary['taxIncluded'] == 0) {
				$orderSummary['total'] += $orderSummary['taxCost'];
				// ISC saves products with the taxes applied, so subtract the subtotal
				if(GetConfig('TaxTypeSelected') == 2) {
					$orderSummary['subtotal'] -= $orderSummary['taxCost'];
					$orderSummary['total'] -= $orderSummary['taxCost'];
				}
			}

			if($orderSummary['subtotal'] < 0) {
				$orderSummary['subtotal'] = 0;
			}

			if($orderSummary['total'] < 0) {
				$orderSummary['total'] = 0;
			}

			$orderSummary['adjustedTotalCost'] = $orderSummary['total'];

			$GLOBALS['GiftCertificates'] = '';
			$newGiftCertificateTotal = 0;
			$giftCertificateTotal = 0;
			foreach($this->GetCartApi()->GetGiftCertificates() as $certificate) {
				if($certificate['giftcertbalance'] > $orderSummary['adjustedTotalCost']) {
					$amountUsed = $certificate['giftcertbalance'] - ($certificate['giftcertbalance'] - $orderSummary['adjustedTotalCost']);
				}
				else {
					$amountUsed = $certificate['giftcertbalance'];
				}

				$giftCertificateTotal += $amountUsed;
			}


			$orderSummary['giftCertificateTotal'] = $giftCertificateTotal;
			$orderSummary['storeCredit'] = 0;

			$orderSummary['adjustedTotalCost'] -= $orderSummary['giftCertificateTotal'];
			$orderSummary['adjustedTotalCost'] -= $orderSummary['storeCredit'];
			$orderSummary['adjustedTotalCost'] -= $orderSummary['subtotalDiscount'];

			if($orderSummary['adjustedTotalCost'] <= 0) {
				$orderSummary['adjustedTotalCost'] = 0;
			}

			return $orderSummary;
		}

		/**
		 * Generate a list of all of the payment providers available for processing
		 * manual orders.
		 *
		 * @param array Optionally, an array containing the existing order if we're editing one.
		 * @return string The generated HTML for the list of payment providers.
		 */
		public function GetPaymentProviderList($existingOrder=array())
		{
			$paymentMethodList = '';
			$availableModules = GetManualOrderCheckoutModules();

			// If we have an existing order, we can add the payment method
			if(!empty($existingOrder['orderpaymentmodule']) && !isset($availableModules[$existingOrder['orderpaymentmodule']]) && $existingOrder['orderpaymentmodule'] != 'custom' && isset($existingOrder['orderpaymentmethod'])) {
				if($existingOrder['orderpaymentmethod'] == 'giftcertificate') {
					$existingOrder['orderpaymentmodule'] = 'giftcertificate';
					$existingOrder['orderpaymentmethod'] = GetLang('GiftCertificates');
				}
				else if($existingOrder['orderpaymentmethod'] == 'storecredit') {
					$existingOrder['orderpaymentmodule'] = 'storecredit';
					$existingOrder['orderpaymentmethod'] = GetLang('StoreCredit');
				}
				$GLOBALS['PaymentMethodId'] = $existingOrder['orderpaymentmodule'];
				$GLOBALS['PaymentMethod'] = $existingOrder['orderpaymentmethod'];
				$GLOBALS['PaymentMethodChecked'] = 'checked="checked"';
				$GLOBALS['HidePaymentFields'] = 'display: none';
				$paymentMethodList .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderPaymentMethod');
			}
			// Show the fake 'Manual Payment' method for existing orders (backwards compatibility)
			else if(!empty($existingOrder['orderid']) && $existingOrder['orderpaymentmodule'] == 'manual') {
				$GLOBALS['PaymentMethodId'] = 'manual';
				$GLOBALS['PaymentMethod'] = GetLang('ManualPayment');
				$GLOBALS['PaymentMethodChecked'] = '';
				if(isset($existingOrder['orderpaymentmodule']) && $existingOrder['orderpaymentmodule'] != 'custom') {
					$GLOBALS['PaymentMethodChecked'] = 'checked="checked"';
				}
				$GLOBALS['HidePaymentFields'] = 'display: none';
				$paymentMethodList .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderPaymentMethod');
			}

			foreach($availableModules as $module) {
				$GLOBALS['PaymentMethodId'] = $module['object']->GetId();
				$GLOBALS['PaymentFields'] = '';
				$GLOBALS['HidePaymentFields'] = 'display: none';
				$GLOBALS['PaymentMethodChecked'] = '';
				$GLOBALS['PaymentMethodDisabled'] = '';
				$GLOBALS['PaymentMethodRequiresSSL'] = '';
				$GLOBALS['PaymentMethodSSLIcon'] = '';
				$GLOBALS['PaymentMethod'] = $module['object']->GetName();
				$GLOBALS['PaymentMethodSSLIcon'] = '';
				if ($module['object']->RequiresSSL() && $_SERVER['HTTPS'] == 'off') {
					$GLOBALS['PaymentMethodSSLIcon'] = ' <img src="images/lock.png" alt="" title="" style="position: relative; top: 2px;"/>';
					$GLOBALS['PaymentMethodRequiresSSL'] = 'class="RequiresSSL"';
					$paymentMethodList .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderPaymentMethod');
					continue;
				}


				$GLOBALS['PaymentValidation'] = '';
				if (method_exists($module['object'], 'GetManualPaymentJavascript')) {
					$GLOBALS['PaymentValidation'] = $module['object']->GetManualPaymentJavascript();
				}

				$customPaymentFields = $module['object']->GetManualPaymentFields($existingOrder);

				if(isset($existingOrder['orderpaymentmodule']) && $module['id'] == $existingOrder['orderpaymentmodule']) {
					$GLOBALS['PaymentMethodChecked'] = 'checked="checked"';
					$GLOBALS['HidePaymentFields'] = '';
				}
				if(!empty($customPaymentFields)) {
					if (!is_array($customPaymentFields)) {
						$GLOBALS['PaymentMethodDisabled'] = 'disabled="disabled"';
						$GLOBALS['PaymentMethod'] .= ' <i>(' . isc_html_escape($customPaymentFields) . ')</i>';
					}
					else {
						foreach($customPaymentFields as $name => $field) {
							$fieldRequired = '&nbsp;';
							if(!isset($field['value'])) {
								$field['value'] = '';
							}
							if (!empty($field['required'])) {
								$fieldRequired = '*';
							}
							switch($field['type']) {
								case 'select':
									$changeEvent = '';
									if(isset($field['onchange'])) {
										$changeEvent = 'onchange="'.$field['onchange'].'"';
									}
									$fieldValue = '<select '.$changeEvent.'" class="Field250" name="paymentField['.$module['id'].']['.$name.']">';
									if(!is_array($field['options'])) {
										$fieldValue .= $field['options'];
									}
									else {
										foreach($field['options'] as $key => $val) {
											$sel = '';
											if($field['value'] == $key) {
												$sel = 'selected="selected"';
											}
											$fieldValue .= '<option value="'.isc_html_escape($key).'">'.isc_html_escape($val).'</option>';
										}
									}
									$fieldValue .= '</select>';
									break;
								case 'textarea':
									$fieldValue = '<textarea rows="4" class="Field250" name="paymentField['.$module['id'].']['.$name.']">'.isc_html_escape($field['value']).'</textarea>';
									break;
								case 'password':
									$fieldValue = '<input type="password" class="Field250" value="'.isc_html_escape($field['value']).'" name="paymentField['.$module['id'].']['.$name.']" />';
									break;
								case 'html':
									$fieldValue = $field['html'];
									break;
								case 'label':
									$fieldValue = isc_html_escape($field['value']);
									break;
								default:
									$fieldValue = '<input type="text" class="Field250" value="'.isc_html_escape($field['value']).'" name="paymentField['.$module['id'].']['.$name.']" />';
									break;
							}
							$fieldJS = '';
							if (isset($field['js'])) {
								$fieldJS = '<script type="text/javascript">'. $field['js'] . '</script>';
							}
							$GLOBALS['PaymentFields'] .= '
								<dt class="Field_'.$name.'"><span class="Required">' . $fieldRequired . '</span> '.isc_html_escape($field['title']).':</dt>
								<dd class="Field_'.$name.'">'.$fieldValue.$fieldJS.'</dd>
							';
						}
					}
				}
				else {
					$GLOBALS['HidePaymentFields'] = 'display: none';
				}
				$paymentMethodList .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderPaymentMethod');
			}

			// Append a custom method to the end of the list
			$GLOBALS['PaymentMethodId'] = 'custom';
			$GLOBALS['PaymentMethod'] = GetLang('OtherPaymentMethod');
			$GLOBALS['PaymentMethodChecked'] = '';
			$GLOBALS['PaymentMethodDisabled'] = '';
			$GLOBALS['PaymentValidation'] = '';
			$GLOBALS['PaymentMethodRequiresSSL'] = '';
			$GLOBALS['PaymentMethodSSLIcon'] = '';
			if(isset($existingOrder['orderpaymentmodule']) && $existingOrder['orderpaymentmodule'] == 'custom') {
				$GLOBALS['PaymentMethodChecked'] = 'checked="checked"';
			}
			$GLOBALS['HidePaymentFields'] = 'display: none';
			$value = '';
			if(isset($existingOrder['paymentField']['custom']['name'])) {
				$value = isc_html_escape($existingOrder['paymentField']['custom']['name']);
			}
			else if(isset($existingOrder['orderpaymentmodule']) && $existingOrder['orderpaymentmodule'] == 'custom') {
				$value = isc_html_escape($existingOrder['orderpaymentmethod']);
			}
			$GLOBALS['PaymentFields'] = '
				<dt><span class="Required">&nbsp;</span> '.GetLang('Name').':</dt>
				<dd><input type="text" class="Field250" name="paymentField[custom][name]" value="'.$value.'" /></dt>
			';
			$paymentMethodList .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderPaymentMethod');
			return $paymentMethodList;
		}

		/**
		 * Generate the raw table show as the order summary (containing totals, shipping, tax etc)
		 *
		 * @param array The order summary, as generated by self::CalculateOrderSummary(). Generated if not supplied.
		 * @return string The generated summary table for this order.
		 */
		public function GenerateOrderSummaryTable($orderSummary=array())
		{
			if(empty($orderSummary)) {
				$orderSummary = $this->CalculateOrderSummary();
			}

			$GLOBALS['Subtotal'] = FormatPrice($orderSummary['subtotal']);

			$GLOBALS['CouponCodes'] = '';
			foreach($this->GetCartApi()->GetAppliedCouponCodes() as $coupon) {
				$GLOBALS['CouponCode'] = isc_html_escape($coupon['couponcode']);
				$GLOBALS['CouponId'] =  $coupon['couponid'];
				$GLOBALS['CouponCodeAmount'] = FormatPrice($coupon['coupontotal']);
				$GLOBALS['CouponCodes'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderSummaryCouponCode');
			}

			$GLOBALS['GiftWrappingTotal'] = FormatPrice($orderSummary['wrappingCost']);
			if($orderSummary['wrappingCost'] == 0) {
				$GLOBALS['HideGiftWrappingTotal'] = 'display: none';
			}

			$GLOBALS['ShippingCost'] = FormatPrice($orderSummary['shippingCost']);
			if(!$this->GetCartApi()->AllProductsInCartAreIntangible()) {
				$GLOBALS['ShippingMethod'] = isc_html_escape($orderSummary['shippingMethod']);
			}
			else {
				$GLOBALS['HideShipping'] = 'display: none';
			}

			$GLOBALS['HandlingCost'] = FormatPrice($orderSummary['handlingCost']);
			if($orderSummary['handlingCost'] == 0) {
				$GLOBALS['HideHandlingCost'] = 'display: none';
			}

			$GLOBALS['TaxName'] = isc_html_escape($orderSummary['taxName']);
			$GLOBALS['TaxCost'] = FormatPrice($orderSummary['taxCost']);

			if($orderSummary['taxIncluded'] == 0) {
				$GLOBALS['HideIncludedTaxCost'] = 'display: none';
			}
			else {
				$GLOBALS['HideTaxCost'] = 'display: none';
			}

			$GLOBALS['Total'] = FormatPrice($orderSummary['total']);

			$GLOBALS['GiftCertificates'] = '';

			$adjustedTotal = $orderSummary['total'];
			foreach($this->GetCartApi()->GetGiftCertificates() as $certificate) {
				$GLOBALS['GiftCertificateCode'] = isc_html_escape($certificate['giftcertcode']);
				$GLOBALS['GiftCertificateId'] = $certificate['giftcertid'];

				if($certificate['giftcertbalance'] > $adjustedTotal) {
					$remaining = $certificate['giftcertbalance'] - $adjustedTotal;
					$amountUsed = $certificate['giftcertbalance'] - ($certificate['giftcertbalance'] - $adjustedTotal);
				}
				else {
					$remaining = 0;
					$amountUsed = $certificate['giftcertbalance'];
				}
				$adjustedTotal -= $amountUsed;

				$GLOBALS['GiftCertificateRemaining'] = FormatPrice($remaining);
				$GLOBALS['CertificateAmountUsed'] = FormatPrice($amountUsed);
				$GLOBALS['GiftCertificates'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderSummaryGiftCertificate');
			}

			if($orderSummary['storeCredit'] == 0) {
				$GLOBALS['HideStoreCredit'] = 'display: none';
			}
			$GLOBALS['StoreCreditTotal'] = FormatPrice($orderSummary['storeCredit']);

			$currency = GetDefaultCurrency();
			$GLOBALS['DiscountAmountToken'] = $currency['currencystring'];
			$GLOBALS['DiscountAmount'] = FormatPrice($orderSummary['subtotalDiscount'], false, false, false, null, false);

			if($orderSummary['adjustedTotalCost'] != $orderSummary['total']) {
				$GLOBALS['AdjustedTotalCost'] = FormatPrice($orderSummary['adjustedTotalCost']);
			}
			else {
				$GLOBALS['HideAdjustedTotal'] = 'display: none';
			}

			return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderSummaryTable');
		}

		/**
		 * Generate an individual row for the order items table.
		 *
		 * @param string The unique identifier for this row.
		 * @param array Array of details about the product for this row.
		 * @param boolean Set to true to hide this row by default.
		 * @return string The generated HTML row for this item.
		 */
		public function GenerateOrderItemRow($rowId, $product=array(), $hidden=false)
		{
			static $first = true;
			static $publicWrappingOptions = null;

			if($hidden == true) {
				$GLOBALS['HideRow'] = 'display: none';
			}
			else {
				$GLOBALS['HideRow'] = '';
			}

			if(is_null($publicWrappingOptions)) {
				$wrappingOptions = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('GiftWrapping');
				if(empty($wrappingOptions)) {
					$publicWrappingOptions = false;
				}
				else {
					$publicWrappingOptions = true;
				}
			}

			if($first != true) {
				$GLOBALS['HideInsertTip'] = 'display: none';
			}
			$first = false;

			if(empty($product)) {
				$GLOBALS['CartItemId'] = $rowId;
				$GLOBALS['ProductCode'] = '';
				$GLOBALS['ProductId'] = 0;
				$GLOBALS['ProductName'] = '';
				$GLOBALS['HideWrappingOptions'] = 'display: none';
				$GLOBALS['HideProductFields'] = 'display: none;';
				$GLOBALS['HideProductVariation'] = 'display: none;';
				$GLOBALS['ProductPrice'] = FormatPrice(0, false, false, true);
				$GLOBALS['ProductOriginalPrice'] = FormatPrice(0, false, false, true);
				$GLOBALS['ProductQuantity'] = 1;
				$GLOBALS['ProductTotal'] = FormatPrice(0);
				$GLOBALS['HideEventDate'] = 'display : none;';
				$GLOBALS['EventDate'] = '';
				return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderItem');
			}

			$GLOBALS['CartItemId'] = $rowId; //isc_html_escape($product['cartitemid']);

			// If the item in the cart is a gift certificate, we need to show a special type of row
			if (isset($product['type']) && $product['type'] == "giftcertificate") {
				$GLOBALS['ProductCode'] = GetLang('NA');
				$GLOBALS['ProductName'] = isc_html_escape($product['product_name']);
				$GLOBALS['ProductQuantity'] = (int)$product['quantity'];
				$GLOBALS['ProductPrice'] = FormatPrice($product['product_price']);
				$GLOBALS['ProductOriginalPrice'] = FormatPrice($product['original_price']);
				$GLOBALS['ProductTotal'] = FormatPrice($product['product_price'] * $product['quantity']);
				return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderItemGiftCertificate');
			}
			// Normal product in the cart
			else {
				$GLOBALS['ProductId'] = $product['product_id'];
				$GLOBALS['ProductName'] = isc_html_escape($product['product_name']);
				$GLOBALS['ProductQuantity'] = (int)$product['quantity'];
				$GLOBALS['ProductCode'] = $product['product_code'];

				// Don't use the discount price here as we'll be showing the coupon codes
				// down below in the summary table
				$productPrice = $product['product_price'];

				$GLOBALS['ProductPrice'] = FormatPrice($productPrice, false, false, true);
				$GLOBALS['ProductOriginalPrice'] = FormatPrice($product['original_price'], false, false, true);
				$GLOBALS['ProductTotal'] = FormatPrice($productPrice*$product['quantity']);

				// Initialize the configurable product fields
				$GLOBALS['HideProductFields'] = 'display: none;';
				$GLOBALS['ProductFields'] = '';
				if(!empty($product['product_fields']) && is_array($product['product_fields'])) {
					$GLOBALS['HideProductFields'] = '';
					foreach($product['product_fields'] as $fieldId => $field) {
						$fieldName = isc_html_escape($field['fieldName']);

						switch($field['fieldType']) {
							case 'file':
								if(isset($field['fieldExisting'])) {
									$fileDirectory = 'configured_products';
								}
								else {
									$fileDirectory = 'configured_products_tmp';
								}
								$fieldValue = '<a href="'.GetConfig('ShopPath').'/'.GetConfig('ImageDirectory').'/'.$fileDirectory.'/'.$field['fileName'].'" target="_blank">'.isc_html_escape($field['fileOriginName']).'</a>';
								break;
							case 'checkbox':
								$fieldValue = GetLang('Checked');
								break;
							default:
								if(isc_strlen($field['fieldValue']) > 50) {
									$field['fieldValue'] = isc_substr($field['fieldValue'], 0, 50)." ..";
								}
								$fieldValue = isc_html_escape($field['fieldValue']);
								// browser is decoding the entities in the ajax response which prevents the row from loading so we need to double encode
								if(isset($_REQUEST['ajaxFormUpload'])) {
									$fieldName = isc_html_escape($fieldName);
									$fieldValue = isc_html_escape($fieldValue);
								}
						}

						if(!trim($fieldValue)) {
							continue;
						}

						$GLOBALS['ProductFields'] .= '
							<dt>'.$fieldName.':</dt>
							<dd>'.$fieldValue.'</dd>
						';
					}
				}

				// Can this item be wrapped?
				$GLOBALS['HideWrappingOptions'] = 'display: none';
				if($product['data']['prodtype'] == PT_PHYSICAL && @$product['data']['prodwrapoptions'] != -1 && $publicWrappingOptions == true) {
					$GLOBALS['HideWrappingOptions'] = '';

					if(isset($product['wrapping'])) {
						$wrapName = isc_html_escape($product['wrapping']['wrapname']);

						if(isset($_REQUEST['ajaxFormUpload'])) {
							$wrapName = isc_html_escape($wrapName);
						}

						$GLOBALS['GiftWrappingName'] = $wrapName;
						$GLOBALS['HideGiftWrappingAdd'] = 'display: none';
						$GLOBALS['HideGiftWrappingEdit'] = '';
						$GLOBALS['HideGiftWrappingPrice'] = '';
						$GLOBALS['GiftWrappingPrice'] = CurrencyConvertFormatPrice($product['wrapping']['wrapprice']);
					}
					else {
						$GLOBALS['GiftWrappingName'] = '';
						$GLOBALS['HideGiftWrappingAdd'] = '';
						$GLOBALS['HideGiftWrappingEdit'] = 'display: none';
						$GLOBALS['HideGiftWrappingPrice'] = 'display: none';
						$GLOBALS['GiftWrappingPrice'] = '';
					}
				}

				// Is this product a variation?
				$GLOBALS['ProductOptions'] = '';
				$GLOBALS['HideProductVariation'] = 'display: none';
				if(isset($product['options']) && !empty($product['options'])) {
					$comma = '';
					$GLOBALS['HideProductVariation'] = '';
					foreach($product['options'] as $name => $value) {
						if(!trim($name) || !trim($value)) {
							continue;
						}
						// browser is decoding the entities in the ajax response which prevents the row from loading so we need to double encode
						if(isset($_REQUEST['ajaxFormUpload'])) {
							$name = isc_html_escape($name);
							$value = isc_html_escape($value);
						}

						$GLOBALS['ProductOptions'] .= $comma.isc_html_escape($name).": ".isc_html_escape($value);
						$comma = ' / ';
					}
				}
				else if(isset($product['data']['prodvariationid']) && $product['data']['prodvariationid'] > 0) {
					$GLOBALS['HideProductVariation'] = '';
					$GLOBALS['ProductOptions'] = GetLang('xNone');
				}

				if (isset($product['data']['prodeventdaterequired']) && $product['data']['prodeventdaterequired']) {
					$GLOBALS['HideEventDate'] = '';
					$GLOBALS['EventDate'] = '<dl><dt>'.$product['data']['prodeventdatefieldname'].': </dt><dd>'.isc_date('jS M Y', $product['event_date']).'</dd></dl>';

				} else {
					$GLOBALS['HideEventDate'] = 'display : none;';
					$GLOBALS['EventDate'] = '';
				}


				return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('OrderItem');
			}
		}

		protected function RefundOrder()
		{
			$message = '';
			$messageStaus = MSG_ERROR;
			$provider = null;

			if(!isset($_REQUEST['orderid'])) {
				return false;
			}

			$orderId = $_REQUEST['orderid'];
			$order = GetOrder($_REQUEST['orderid']);
			if(!isset($order['orderid'])) {
				return false;
			}


			/* Validate posted data*/
			$refundType = '';
			if(!isset($_REQUEST['refundType'])) {
				return false;
			}

			$refundType = $_REQUEST['refundType'];

			//preset the refund amount to the available amount of the order
			$refundAmt = $order['ordgatewayamount'] - $order['ordrefundedamount'];

			//refund partial amount
			if($refundType== 'partial') {
				//is refund amount specified
				if(!isset($_REQUEST['refundAmt']) || $_REQUEST['refundAmt'] == '') {
					$message = GetLang('EnterRefundAmount');
				}
				//is refund amount specified a valid format
				else if(!is_numeric($_REQUEST['refundAmt']) || $_REQUEST['refundAmt'] <= 0) {
					$message = GetLang('InvalidRefundAmountFormat');
				}
				//is refund amount larger than the original order amount
				else if($_REQUEST['refundAmt'] + $order['ordrefundedamount']  > $order['ordgatewayamount']) {
					$message = GetLang('InvalidRefundAmount');
				}
				else {
					$refundAmt = $_REQUEST['refundAmt'];
				}
			}

			//there is an error message
			if($message != '') {
				FlashMessage($message, $messageStatus, 'index.php?ToDo=viewOrders');
			}

			$transactionId = trim($order['ordpayproviderid']);
			if($transactionId == '') {
				$message = GetLang('OrderTranscationIDNotFound');
			}
			else if(!GetModuleById('checkout', $provider, $order['orderpaymentmodule'])) {
				$message = GetLang('PaymentMethodNotExist');
			}
			else if(!$provider->IsEnabled()) {
				$message = GetLang('PaymentProviderIsDisabled');
			}
			else if(!method_exists($provider, "DoRefund")) {
				$message = GetLang('RefundNotAvailable');
			}
			else {
				//still here, perform a delay capture
				if($provider->DoRefund($order, $message, $refundAmt)) {
					$messageStatus = MSG_SUCCESS;

					//update order status
					$orderStatus = ORDER_STATUS_REFUNDED;
					UpdateOrderStatus($order['orderid'], $orderStatus, true);
				}
			}
			FlashMessage($message, $messageStatus, 'index.php?ToDo=viewOrders');

			return $message;
		}

		/**
		 * Format an address for display in the control panel for an order or shipment.
		 *
		 * @param array An array of details about the address.
		 * @param boolean Set to false to not include a flag image for the address country.
		 * @return string The generated HTML of the formatted address.
		 */
		public function BuildOrderAddressDetails($address, $includeFlag=true)
		{
			if(!isset($address['countrycode'])) {
				$address['countrycode'] = GetCountryISO2ByName($address['shipcountry']);
			}

			$countryFlag = '';
			if($includeFlag && $address['countrycode'] != '' && file_exists(ISC_BASE_PATH."/lib/flags/".strtolower($address['countrycode']).".gif")) {
				$countryFlag = "
					&nbsp;&nbsp;
					<img src=\"".GetConfig('AppPath')."/lib/flags/".strtolower($address['countrycode']).".gif\" style=\"vertical-align: middle;\" alt=\"\" />
				";
			}

			$addressPieces = array(
				isc_html_escape($address['shipfirstname']).' '.isc_html_escape($address['shiplastname']),
				isc_html_escape($address['shipcompany']),
				isc_html_escape($address['shipaddress1']),
				isc_html_escape($address['shipaddress2']),
				trim(isc_html_escape($address['shipcity'].', '.$address['shipstate'].' '.$address['shipzip']), ', '),
				isc_html_escape($address['shipcountry']).$countryFlag
			);

			$addressDetails = '';
			foreach($addressPieces as $piece) {
				if(!trim($piece)) {
					continue;
				}
				else if($addressDetails) {
					$addressDetails .= '<br />';
				}
				$addressDetails .= $piece;
			}
			return $addressDetails;
		}
	}