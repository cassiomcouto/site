<?php

	class ISC_ADMIN_PRODUCT
	{
		private $productEntity;

		public $_customSearch = array();

		/**
		* An instance of the database object
		*
		* @var MySQLDb
		*/
		private $db;

		/**
		 * The constructor.
		 */
		public function __construct()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('products');
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('optimizer');
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->stylesheets[] = 'Styles/products.css';

			// set the database member object
			$this->db = $GLOBALS['ISC_CLASS_DB'];
			$this->engine = &$GLOBALS['ISC_CLASS_ADMIN_ENGINE'];

			// Initialise custom searches functionality
			require_once(dirname(__FILE__).'/class.customsearch.php');
			$GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH'] = new ISC_ADMIN_CUSTOMSEARCH('products');
			$GLOBALS['WeightMeasurement'] = GetConfig('WeightMeasurement');
			$GLOBALS['LengthMeasurement'] = GetConfig('LengthMeasurement');

			$this->productEntity = new ISC_ENTITY_PRODUCT();
		}

		public function HandleToDo($Do)
		{
			switch (isc_strtolower($Do)) {
				case "deleteproductvariations":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang("ProductVariations") => "index.php?ToDo=viewProductVariations", GetLang('DeleteProductVariation') => "index.php?ToDo=deleteProductVariation");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteVariations();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editproductvariation2":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang("ProductVariations") => "index.php?ToDo=viewProductVariations", GetLang('EditProductVariation') => "index.php?ToDo=editProductVariation");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->EditVariationStep2();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editproductvariation":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang("ProductVariations") => "index.php?ToDo=viewProductVariations", GetLang('EditProductVariation') => "index.php?ToDo=editProductVariation");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->EditVariationStep1();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "addproductvariation2":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang("ProductVariations") => "index.php?ToDo=viewProductVariations", GetLang('AddProductVariation') => "index.php?ToDo=addProductVariation");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->AddVariationStep2();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "addproductvariationoption":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {
						$this->AddVariationOptionStep1();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "addproductvariation":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang("ProductVariations") => "index.php?ToDo=viewProductVariations", GetLang('AddProductVariation') => "index.php?ToDo=addProductVariation");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->AddVariationStep1();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "viewproductvariations":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Variations)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('ProductVariations') => "index.php?ToDo=viewProductVariations");

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						}

						$this->ViewVariations();

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						}

						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "savebulkeditproducts":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products) && gzte11(ISC_LARGEPRINT)) {

						if(isset($_POST['addanother'])) {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('EditProduct') => "index.php?ToDo=editProduct");
						}
						else {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");
							if (GetSession('productsearch') > 0) {
								if (!isset($_GET['searchId'])) {
									$_GET['searchId'] = GetSession('productsearch');
									$_REQUEST['searchId'] = GetSession('productsearch');
								}

								if ($_GET['searchId'] > 0) {
									$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customProductSearch"));
								}
							}
						}

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->BulkEditProductsStep2();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "bulkeditproducts":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products) && gzte11(ISC_LARGEPRINT)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('BulkEditProducts1') => "index.php?ToDo=bulkEditProducts");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->BulkEditProductsStep1();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "createproductview":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('CreateProductView') => "index.php?ToDo=createProductView");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CreateView();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "importproducts":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Import_Products)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('ImportProducts') => "index.php?ToDo=importProducts");
						$this->ImportProducts();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editproduct2":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {

						if(isset($_POST['addanother'])) {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('EditProduct') => "index.php?ToDo=editProduct");
						}
						else {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");
							if (GetSession('productsearch') > 0) {
								if (!isset($_GET['searchId'])) {
									$_GET['searchId'] = GetSession('productsearch');
									$_REQUEST['searchId'] = GetSession('productsearch');
								}

								if ($_GET['searchId'] > 0) {
									$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customProductSearch"));
								}
							}
						}

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->EditProductStep2();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editproduct":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('EditProduct') => "index.php?ToDo=editProduct");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->EditProductStep1();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editproductvisibility":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						}

						$this->EditVisibility();

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						}

						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "editproductfeatured":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						}

						$this->EditFeatured();

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						}

						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "deleteproducts":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Delete_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");
						if (GetSession('productsearch') > 0) {
							if (!isset($_GET['searchId'])) {
								$_GET['searchId'] = GetSession('productsearch');
								$_REQUEST['searchId'] = GetSession('productsearch');
							}

							if ($_GET['searchId'] > 0) {
								$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customProductSearch"));
							}
						}

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteProducts();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "addproduct2":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Create_Product)) {

						if(isset($_POST['addanother'])) {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('AddProduct') => "index.php?ToDo=addProduct");
						}
						else {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");
							if (GetSession('productsearch') > 0) {
								if (!isset($_GET['searchId'])) {
									$_GET['searchId'] = GetSession('productsearch');
									$_REQUEST['searchId'] = GetSession('productsearch');
								}

								if ($_GET['searchId'] > 0) {
									$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customProductSearch"));
								}
							}
						}

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->AddProductStep2();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "addproduct":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Create_Product)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('AddProduct') => "index.php?ToDo=addProduct");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->AddProductStep1();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "deletecustomproductsearch":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteCustomSearch();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "customproductsearch":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('CustomView') => "index.php?ToDo=customProductSearch");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CustomSearch();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "searchproductsredirect":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('SearchResults') => "index.php?ToDo=searchProducts");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SearchProductsRedirect();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "searchproducts":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('SearchProducts') => "index.php?ToDo=searchProducts");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SearchProducts();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "popupproductselect":
					$this->PopupProductSelect();
					break;
				case "copyproduct":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Create_Product)) {

						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('CopyProduct') => "index.php?ToDo=copyProduct");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CopyProductStep1();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case "copyproduct2":
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Create_Product)) {

						if(isset($_POST['addanother'])) {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('CopyProduct') => "index.php?ToDo=addProduct");
						}
						else {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");
							if (GetSession('productsearch') > 0) {
								if (!isset($_GET['searchId'])) {
									$_GET['searchId'] = GetSession('productsearch');
									$_REQUEST['searchId'] = GetSession('productsearch');
								}

								if ($_GET['searchId'] > 0) {
									$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customProductSearch"));
								}
							}
						}

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->CopyProductStep2();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				case 'downloadproductfile':
					$this->DownloadProductFile();
					break;
				case 'importproductvariations':
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Import_Products)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('ImportProductVariations') => "index.php?ToDo=importProductVariations");
						$this->ImportProductVariations();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
					break;
				default:
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

						if(isset($_GET['searchQuery'])) {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts", GetLang('SearchResults') => "index.php?ToDo=viewProducts");
						}
						else {
							$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Products') => "index.php?ToDo=viewProducts");
						}

						if (GetSession('productsearch') > 0) {
							if (!isset($_GET['searchId'])) {
								$_GET['searchId'] = GetSession('productsearch');
								$_REQUEST['searchId'] = GetSession('productsearch');
							}

							if ($_GET['searchId'] > 0) {
								$GLOBALS['BreadcrumEntries'] = array_merge($GLOBALS['BreadcrumEntries'], array(GetLang('CustomView') => "index.php?ToDo=customProductSearch"));
							}
						}

						if (!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						}

						if (GetSession('productsearch') > 0) {
							$this->CustomSearch();
						} else {
							UnsetSession('productsearch');
							$this->ManageProducts();
						}
						if (!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						}
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
			}
		}

		public function _GetPopupCategoryList($parentid=0, $prefix='')
		{
			$subs = array();

			// If we don't have any data get it from the db
			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->getCatsInfo();
			if (empty($GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->tree->nodesByPid)) {
				$parentid = 0;
			}

			if (!isset($GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->tree->nodesByPid[$parentid])) {
				return '';
			}

			$cats = '';

			// Create the formatted array
			foreach ($GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->tree->nodesByPid[$parentid] as $k => $catid) {
				$cat = $GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->catsById[$catid];
				$cats .= sprintf("<li onclick='if(this.parentNode.previousItem) { this.parentNode.previousItem.className = \"\"; } this.className=\"active\"; current_category = %s; this.parentNode.previousItem = this; ProductSelect.LoadLinks(\"category=%d\");'>%s<img src='images/category.gif' alt='' style='vertical-align: middle' /> %s</li>", $cat['categoryid'], $cat['categoryid'], $prefix, isc_html_escape($cat['catname']));
				$cats .= $this->_GetPopupCategoryList($cat['categoryid'], $prefix.'&nbsp;&nbsp;&nbsp;&nbsp;');
			}
			return $cats;
		}

		public function PopupProductSelect()
		{
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("pageheader.popup");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

			$GLOBALS['Callbacks'] = '';
			$callbacks = array(
				'selectCallback',
				'removeCallback',
				'closeCallback',
				'getSelectedCallback'
			);
			foreach($callbacks as $function) {
				if(isset($_REQUEST[$function])) {
					$GLOBALS['Callbacks'] .= 'ProductSelect.'.$function.' = window.opener.'.$_REQUEST[$function].';';
				}
			}

			$GLOBALS['ParentProductSelect'] = $_REQUEST['ProductSelect'];
			$GLOBALS['ParentProductList'] = $_REQUEST['ProductList'];

			if(isset($_REQUEST['FocusOnClose'])) {
				$GLOBALS['FocusOnClose'] = isc_html_escape($_REQUEST['FocusOnClose']);
			}

			if(isset($_REQUEST['single']) && $_REQUEST['single'] == 1) {
				$GLOBALS['ProductSelectSingle'] = 1;
			}
			else {
				$GLOBALS['ProductSelectSingle'] = 0;
			}

			// Get a listing of all of the categories
			$GLOBALS['CategorySelect'] = $this->_GetPopupCategoryList();

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("products.popupselect");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("pagefooter.popup");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		* Takes the filename of an image already uploaded into the image directory, generates a thumbnal from it, stores it in the image directory and returns its name
		*
		* Note: checked for code removal in 5500 (ISC-102) but appears to be in use by variations -ge
		*
		* @param string $ImageName
		* @param string $Size
		* @param bool $OverrideExisting
		* @return bool
		*/
		public function _AutoGenerateThumb($ImageName, $Size="thumb", $OverrideExisting=false)
		{
			$imgFile = realpath(ISC_BASE_PATH."/" . GetConfig('ImageDirectory'));
			$imgFile .= "/" . $ImageName;

			if ($ImageName == '' || !file_exists($imgFile)) {
				return false;
			}

			// A list of thumbnails too
			$tmp = explode(".", $imgFile);
			$ext = isc_strtolower($tmp[count($tmp)-1]);

			// If overriding the existing image, set the output filename to the input filename
			if($OverrideExisting == true) {
				$thumbFileName = $ImageName;
			}
			else {
				$thumbFileName = GenRandFileName($ImageName, $Size);
			}

			$attribs = @getimagesize($imgFile);
			$width = $attribs[0];
			$height = $attribs[1];

			if(!is_array($attribs)) {
				return false;
			}

			// Check if we have enough available memory to create this image - if we don't, attempt to bump it up
			setImageFileMemLimit($imgFile);

			$thumbFile = realpath(ISC_BASE_PATH."/" . GetConfig('ImageDirectory'));
			$thumbFile .= "/" . $thumbFileName;

			if ($ext == "jpg") {
				$srcImg = @imagecreatefromjpeg($imgFile);
			} else if($ext == "gif") {
				$srcImg = @imagecreatefromgif($imgFile);
				if(!function_exists("imagegif")) {
					$gifHack = 1;
				}
			} else {
				$srcImg = @imagecreatefrompng($imgFile);
			}

			if(!$srcImg) {
				return false;
			}

			$srcWidth = @imagesx($srcImg);
			$srcHeight = @imagesy($srcImg);

			if($Size == "tiny") {
				$AutoThumbSize = ISC_PRODUCT_IMAGE_SIZE_TINY;
			} else {
				$AutoThumbSize = ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL;
			}

			// This thumbnail is smaller than the Shopping Cart dimensions, simply copy the image and return
			if($srcWidth <= $AutoThumbSize && $srcHeight <= $AutoThumbSize) {
				@imagedestroy($srcImg);
				if($OverrideExisting == false) {
					@copy($imgFile, $thumbFile);
				}
				return $thumbFileName;
			}

			// Make sure the thumb has a constant height
			$thumbWidth = $width;
			$thumbHeight = $height;

			if($width > $AutoThumbSize) {
				$thumbWidth = $AutoThumbSize;
				$thumbHeight = ceil(($height*(($AutoThumbSize*100)/$width))/100);
				$height = $thumbHeight;
				$width = $thumbWidth;
			}

			if($height > $AutoThumbSize) {
				$thumbHeight = $AutoThumbSize;
				$thumbWidth = ceil(($width*(($AutoThumbSize*100)/$height))/100);
			}

			$thumbImage = @imagecreatetruecolor($thumbWidth, $thumbHeight);
			if($ext == "gif" && !isset($gifHack)) {
				$colorTransparent = @imagecolortransparent($srcImg);
				@imagepalettecopy($srcImg, $thumbImage);
				@imagecolortransparent($thumbImage, $colorTransparent);
				@imagetruecolortopalette($thumbImage, true, 256);
			}
			else if($ext == "png") {
				@imagecolortransparent($thumbImage, @imagecolorallocate($thumbImage, 0, 0, 0));
				@imagealphablending($thumbImage, false);
			}

			@imagecopyresampled($thumbImage, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);

			if ($ext == "jpg") {
				@imagejpeg($thumbImage, $thumbFile, 100);
			} else if($ext == "gif") {
				if(isset($gifHack) && $gifHack == true) {
					$thumbFile = isc_substr($thumbFile, 0, -3)."jpg";
					@imagejpeg($thumbImage, $thumbFile, 100);
				}
				else {
					@imagegif($thumbImage, $thumbFile);
				}
			} else {
				@imagepng($thumbImage, $thumbFile);
			}

			@imagedestroy($thumbImage);
			@imagedestroy($srcImg);

			// Change the permissions on the thumbnail file
			isc_chmod($thumbFile, ISC_WRITEABLE_FILE_PERM);

			return $thumbFileName;
		}

		public function _GetCustomFieldData($ProductId = 0, &$RefArray = array())
		{
			// Gets the custom fields of a product. If $ProductId is 0 then
			// the data is retrieved from the form. If not, it is retrieved
			// from the custom fields table. Returns the data to the array
			// referenced by the $RefArray variable.

			if ($ProductId == 0) {
				// Get the data for this product from the form.
				if (array_key_exists("customFieldName", $_POST)) {
					foreach (array_keys($_POST["customFieldName"]) as $key) {
						if ($_POST["customFieldName"][$key] != "") {

							if (!isset($_POST["customFieldValue"][$key])) {
								$val = "";
							} else {
								$val = $_POST["customFieldValue"][$key];
							}

							$RefArray[] = array(
								"name" => $_POST["customFieldName"][$key],
								"value" => $val
							);
						}
					}
				}
			} else {
				// Get the data for this product from the database
				$query = sprintf("select * from [|PREFIX|]product_customfields where fieldprodid='%d' Order by fieldid ASC", $GLOBALS['ISC_CLASS_DB']->Quote($ProductId));
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

				while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
					$RefArray[] = array(
						"name" => $row['fieldname'],
						"value" => $row['fieldvalue']
					);
				}
			}
		}


		/**
		*get snippet for the configurable product field
		*
		*@param $field data  of a configurable product field
		*
		*@return string html of the field
		*/
		Private function _GetProductFieldRow($field = array())
		{
			$GLOBALS['ProductFieldId'] = (int)$field['id'];

			$GLOBALS['ProductFieldName'] = GetLang('FieldName');
			$GLOBALS['FieldNameClass'] = 'FieldHelp';
			if(trim($field['name']) != '') {
				$GLOBALS['ProductFieldName'] = isc_html_escape($field['name']);
				$GLOBALS['FieldNameClass'] = '';
			}
			$GLOBALS['ProductFieldType'] = isc_html_escape($field['type']);

			$GLOBALS['ProductFieldFileType'] = GetLang('FieldFileType');
			$GLOBALS['FileTypeClass'] = 'FieldHelp';
			if(trim($field['fileType'])!='') {
				$GLOBALS['ProductFieldFileType'] = isc_html_escape($field['fileType']);
				$GLOBALS['FileTypeClass'] = '';
			}

			$GLOBALS['ProductFieldFileSize'] = GetLang('FieldFileSize');
			$GLOBALS['FileSizeClass'] = 'FieldHelp';
			if(trim($field['fileSize']) != '') {
				$GLOBALS['ProductFieldFileSize'] = isc_html_escape($field['fileSize']);
				$GLOBALS['FileSizeClass'] = '';
			}
			$GLOBALS['ProductFieldLabelNumber'] = $GLOBALS['ProductFieldKey'] + 1;

			if($field['required']==1) {
				$GLOBALS['ProductFieldRequired'] = 'checked';
			} else {
				$GLOBALS['ProductFieldRequired'] = '';
			}

			$GLOBALS['ProductFieldTypeText'] = '';
			$GLOBALS['ProductFieldTypeTextarea'] = '';
			$GLOBALS['ProductFieldTypeFile'] = '';
			$GLOBALS['ProductFieldTypeCheckbox'] = '';
			$GLOBALS['HideFieldFileType'] = 'display: none';

			switch($GLOBALS['ProductFieldType']) {
				case 'text': {
					$GLOBALS['ProductFieldTypeText'] = 'Selected';
					break;
				}
				case 'textarea': {
					$GLOBALS['ProductFieldTypeTextarea'] = 'Selected';
					break;
				}
				case 'file': {
					$GLOBALS['HideFieldFileType'] = '';
					$GLOBALS['ProductFieldTypeFile'] = 'Selected';
					break;
				}
				case 'checkbox': {
					$GLOBALS['ProductFieldTypeCheckbox'] = 'Selected';
					break;
				}
			}

			if (!$GLOBALS['ProductFieldKey']) {
				$GLOBALS['HideProductFieldDelete'] = 'none';
			} else {
				$GLOBALS['HideProductFieldDelete'] = '';
			}

			return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ProductFields');
		}

		/**
		* create configurable products fields section on products page
		*
		* @param int $productId product id
		* @param bool $CopyProduct, is this for coping a product
		* @return string html of the configurable products fields section
		*/
		Private function _GetProductFieldsLayout($productId = 0, $CopyProduct = false)
		{
			$arrProductFields = array();
			$productFields = '';
			$GLOBALS['ProductFieldKey'] = 0;
			$GLOBALS['ProductFieldNumber'] = 1;

			$this->_GetProductFieldData($productId, $arrProductFields, $CopyProduct);
			if (!empty($arrProductFields)) {
				foreach ($arrProductFields as $f) {
					$productFields .= $this->_GetProductFieldRow($f);
					$GLOBALS['ProductFieldNumber']++;
					$GLOBALS['ProductFieldKey']++;
				}
			}
			if($GLOBALS['ProductFieldKey'] == 0) {
				$GLOBALS['FieldLastKey'] = 1;
			} else {
				$GLOBALS['FieldLastKey'] = $GLOBALS['ProductFieldKey'];
			}
			//create one empty row if there isn't any field
			if ($productFields=='') {
				$field = array('name'=>'', 'type'=>'', 'fileType'=>'', 'fileSize'=>'', 'required'=>'', 'id'=>0);
				$productFields .= $this->_GetProductFieldRow($field);
			}
			return $productFields;
		}

		/**
		* valid configurable product fields
		*
		* @param array $ProductFields configurable fields data
		*
		* @return String form validation message
		*/
		private function _ValidateProductFields($ProductFields)
		{
			if(empty($ProductFields)) {
				return '';
			}
			foreach ($ProductFields as $field) {
				if($field['name'] == '' && ($field['type'] != 'text' || $field['required']==1)) {
					return GetLang('EnterProductFieldName');
				}

				if($field['type'] == 'file' && $field['fileType'] == '') {
					return GetLang('EnterProductFieldFileType');
				}

				if($field['type'] == 'file' && $field['fileSize'] == '') {
					return GetLang('EnterProductFieldFileSize');
				}
			}
			return '';
		}

		/**
		* Gets the configurable product fields of a product. If $ProductId is 0 then
		* the data is retrieved from the form. If not, it is retrieved
		* from the custom fields table. Returns the data to the array
		* referenced by the $RefArray variable.
		*
		* @param int $ProductId product id
		* @param array $RefArray fields data
		* @param bool $CopyProduct if this is called to copy a product, then the field id shouldn't be set, it should be treated as a new field
		*/
		private function _GetProductFieldData($ProductId = 0, &$RefArray = array(), $CopyProduct = false)
		{
			if ($ProductId == 0) {
				// Get the data for this product from the form.
				if (isset($_POST['productFieldName'])) {
					if(is_array($_POST['productFieldName'])) {
						foreach ($_POST['productFieldName'] as $key => $name) {
							if (trim($name) != "") {
								$type = 'text';
								$required = 0;
								$fileType = '';
								$fileSize = 0;
								if (isset($_POST["productFieldType"][$key])) {
									$type = $_POST["productFieldType"][$key];
									if($type=='file') {
										if(isset($_POST["productFieldFileType"][$key])) {
											$fileType = $_POST["productFieldFileType"][$key];
										}
										if (isset($_POST["productFieldFileSize"][$key])) {
											$fileSize = $_POST["productFieldFileSize"][$key];
										}
									}
								}

								if (isset($_POST["productFieldRequired"][$key])) {
									$required = 1;
								}

								$RefArray[] = array(
									"id"		=> $_POST["productFieldId"][$key],
									"name"		=> $name,
									"type"		=> $type,
									"fileType"	=> $fileType,
									"fileSize"	=> $fileSize,
									"required"	=> $required
								);
							}
						}
					}
				}
			} else {
				// Get the data for this product from the database
				$query = "select * from [|PREFIX|]product_configurable_fields where fieldprodid='".$GLOBALS['ISC_CLASS_DB']->Quote($ProductId)."' Order by fieldsortorder ASC";
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

				while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {

					//if this is to copy a product, then the field id should be set to 0 to trigger a insertion for the new field when save it.
					if($CopyProduct) {
						$productFieldId = 0;

					//otherwise this is editing a product
					} else {
						$productFieldId = $row['productfieldid'];
					}

					$RefArray[] = array(
						"id"		=> $productFieldId,
						"name"		=> $row['fieldname'],
						"type"		=> $row['fieldtype'],
						"fileType"	=> $row['fieldfiletype'],
						"fileSize"	=> $row['fieldfilesize'],
						"required"	=> $row['fieldrequired']
					);
				}
			}
		}

		/**
		* save configurable product fields details in database
		*
		* @param array $ProductFields product fields data
		* @param int $prodId Product id
		*
		*/
		Private function _SaveProductFields($ProductFields, $prodId)
		{
			//get current field ids from data base
			$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT productfieldid FROM [|PREFIX|]product_configurable_fields WHERE fieldprodid='".(int) $prodId."'");

			$unaffectedFields = array();
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$unaffectedFields[] = $row['productfieldid'];
			}

			$productFieldIDs = array();
			if (!empty($ProductFields)) {
				$sortOrder = 1;
				foreach ($ProductFields as $field) {

					if ($field['name'] === GetLang('FieldName') || $field['fileType'] === GetLang('FieldFileType') || $field['fileSize'] === GetLang('FieldFileSize')) {
						continue;
					}

					$newField = array(
						"fieldprodid" => $prodId,
						"fieldname" => $field['name'],
						"fieldtype" => $field['type'],
						"fieldfiletype" => $field['fileType'],
						"fieldfilesize" => $field['fileSize'],
						"fieldrequired" => $field['required'],
						"fieldsortorder" => $sortOrder
					);

					//if this is a existing field, update it
					if(isset($field['id']) && $field['id'] > 0) {
						//remove the field id from unaffected fields because it's been updated
						if(in_array($field['id'], $unaffectedFields)) {
							$key = array_search($field['id'], $unaffectedFields);
							unset($unaffectedFields[$key]);
						}
						$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_configurable_fields", $newField, "productfieldid='".(int)$field['id']."'");
						$productFieldIDs[] = (int)$field['id'];
					}
					//if this is a new field, insert it
					else {
						$newFieldId = $GLOBALS['ISC_CLASS_DB']->InsertQuery("product_configurable_fields", $newField);
						$productFieldIDs[] = $newFieldId;
					}
					$sortOrder++;
				}
			}

			if(!empty($unaffectedFields)) {
				$fields = implode("','", $unaffectedFields);
				$GLOBALS['ISC_CLASS_DB']->Query("DELETE FROM [|PREFIX|]product_configurable_fields WHERE  productfieldid in ('".$fields."')");
			}

			$updateArray = array(
					"prodconfigfields" => implode(",", $productFieldIDs),
			);
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updateArray, "productid=".(int)$prodId);
		}

		/**
		 * Return the field label
		 *
		 * Method will construct the field lable so words and numbers can be in different language. The label will be used in the sprintf() function
		 * so there must be a '%s' to be replaced with the field number
		 *
		 * @access public
		 * @param int $key the current number
		 * @param string The field label to do the sprintf() with
		 * @return The replaced field label
		 */
		public function GetFieldLabel($key, $label)
		{
			$parts = str_split($key);
			$number = '';

			foreach ($parts as $part) {
				$number .= GetLang('Number' . $part);
			}

			return sprintf($label, $number);
		}

		/**
		 * Get the discount rules HTML
		 *
		 * Method will return the discount rules HTML for the discount panel
		 *
		 * @access private
		 * @param int $productId The optional product. Default will look in the POST
		 * @return string The discount rules HTML
		 */
		private function GetDiscountRules($productId=0)
		{
			$discounts = $this->GetDiscountRulesData($productId, true);
			$GLOBALS['DiscountRules'] = '';
			$GLOBALS['DiscountRulesKey'] = 0;

			if (!empty($discounts)) {
				foreach ($discounts as $discount) {

					// Type reset
					$GLOBALS['DiscountRulesTypePriceSelected'] = '';
					$GLOBALS['DiscountRulesTypePercentSelected'] = '';
					$GLOBALS['DiscountRulesTypeFixedSelected'] = '';

					$GLOBALS['DiscountRulesType' . ucfirst(isc_strtolower($discount['type'])) . 'Selected'] = "SELECTED";
					$GLOBALS['DiscountRulesQuantityMin'] = isc_html_escape($discount['quantitymin']);
					$GLOBALS['DiscountRulesQuantityMax'] = isc_html_escape($discount['quantitymax']);
					$GLOBALS['DiscountRulesAmount'] = $discount['amount'];
					$GLOBALS['DiscountRulesLabel'] = $this->GetFieldLabel(($GLOBALS['DiscountRulesKey']+1), GetLang('DiscountRulesField'));
					$GLOBALS['DiscountRulesAmountPrefix'] = '';
					$GLOBALS['DiscountRulesAmountPostfix'] = '';

					// Now for the funky part of displaying either the percentage or their default currency symbol
					if (isc_strtolower($discount['type']) == 'percent') {
						$GLOBALS['DiscountRulesAmountPrefix'] = '%';
					} else {
						if (GetConfig('CurrencyLocation') == 'left') {
							$GLOBALS['DiscountRulesAmountPrefix'] = GetConfig('CurrencyToken');
						} else {
							$GLOBALS['DiscountRulesAmountPostfix'] = GetConfig('CurrencyToken');
						}
					}

					// Now assign the different line endings
					if (isc_strtolower(isc_html_escape($discount['type'])) == 'fixed') {
						$GLOBALS['DiscountRulesLineEnding'] = GetLang('DiscountRulesForEachItem');
					} else {
						$GLOBALS['DiscountRulesLineEnding'] = GetLang('DiscountRulesOffEachItem');
					}

					$GLOBALS['DiscountRules'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('DiscountRules');

					$GLOBALS['DiscountRulesKey']++;
				}
			}
			else {
				// Show an empty discount rule if no rules are defined
				$GLOBALS['DiscountRulesTypePriceSelected'] = 'SELECTED';
				$GLOBALS['DiscountRulesTypePercentSelected'] = '';
				$GLOBALS['DiscountRulesTypeFixedSelected'] = '';
				$GLOBALS['DiscountRulesQuantityMin'] = '';
				$GLOBALS['DiscountRulesQuantityMax'] = '';
				$GLOBALS['DiscountRulesAmount'] = '';
				$GLOBALS['DiscountRulesLabel'] = $this->GetFieldLabel(($GLOBALS['DiscountRulesKey']+1), GetLang('DiscountRulesField'));
				$GLOBALS['DiscountRulesAmountPrefix'] = GetConfig('CurrencyToken');
				$GLOBALS['DiscountRulesAmountPostfix'] = '';
				$GLOBALS['DiscountRulesLineEnding'] = GetLang('DiscountRulesOffEachItem');
				$GLOBALS['DiscountRules'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('DiscountRules');
			}

			return $GLOBALS['DiscountRules'];
		}

		/**
		 * Get the discount rules
		 *
		 * Method will return the discount rules either from the POST or from the database
		 *
		 * @access private
		 * @param int $productId The optional product ID associated with the discount rules. Will default to 0 (retrieve from POST)
		 * @param bool $removeEmptyRows TRUE to remove any empty records, FALSE to keep them in. Only used on the POST request. Default is FALSE
		 * @return array The array of discount rules
		 */
		private function GetDiscountRulesData($productId=0, $removeEmptyRows=false)
		{
			$discount = array();

			// Get the data from the POST
			if (!isId($productId)) {
				if (array_key_exists("discountRulesType", $_POST)) {
					foreach (array_keys($_POST["discountRulesType"]) as $key) {

						if (!isset($_POST["discountRulesQuantityMin"][$key]) || $_POST["discountRulesQuantityMin"][$key] == '') {
							$quantitymin = '';
						} else {
							$quantitymin = $_POST["discountRulesQuantityMin"][$key];
						}

						if (!isset($_POST["discountRulesQuantityMax"][$key]) || $_POST["discountRulesQuantityMax"][$key] == '') {
							$quantitymax = '';
						} else {
							$quantitymax = $_POST["discountRulesQuantityMax"][$key];
						}

						if (!isset($_POST["discountRulesAmount"][$key]) || $_POST["discountRulesAmount"][$key] == '') {
							$amount = '';
						} else {
							$amount = $_POST["discountRulesAmount"][$key];
						}

						// Check for any empties
						if ($removeEmptyRows && $quantitymin == '' && $quantitymax == '' && $amount == '') {
							continue;
						}

						$discount[] = array(
							"type" => $_POST["discountRulesType"][$key],
							"quantitymin" => $quantitymin,
							"quantitymax" => $quantitymax,
							"amount" => $amount,
						);
					}
				}

			// Else get it from the database
			} else {

				// Order it by quantity. Looks a bit weird because zeros are astrixes
				$query = "
					SELECT *
					FROM [|PREFIX|]product_discounts
					WHERE discountprodid = " . (int)$productId . "
					ORDER BY IF(discountquantitymax > discountquantitymin, discountquantitymax, discountquantitymin) ASC
				";

				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

				while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
					// If the min and max quantities are 0 then convert them to astrixes
					if (!isId($row['discountquantitymin'])) {
						$row['discountquantitymin'] = '*';
					}

					if (!isId($row['discountquantitymax'])) {
						$row['discountquantitymax'] = '*';
					}

					// If the type is a percent then it must be an integer
					if ($row['discounttype'] == 'percent') {
						$row['discountamount'] = (int)$row['discountamount'];
					}
					else {
						// Convert the price to their default currency format if we are a price type
						$row['discountamount'] = FormatPrice($row['discountamount'], false, false);
					}

					$discount[] = array(
						"type" => $row['discounttype'],
						"quantitymin" => $row['discountquantitymin'],
						"quantitymax" => $row['discountquantitymax'],
						"amount" => $row['discountamount'],
					);
				}
			}

			return $discount;
		}

		/**
		 * Validate discount rules data
		 *
		 * Method will validate all the discount rules POST data
		 *
		 * @access private
		 * @param string &$error The referenced string to store the error in, if any were found
		 * @return bool TRUE if POST data is valid, FALSE if there were errors
		 */
		private function ValidateDiscountRulesData(&$error)
		{
			$discounts = $this->GetDiscountRulesData(0);

			// Check to see if we have anything to validate
			if (empty($discounts)) {
				return true;
			}

			// Variable to check for overlapping
			$overlap = array(
						0 => array(),
						1 => array()
						);

			// This is to check for the previous quantities
			$prevMax = null;

			// OK, we have some, now check each rule
			foreach ($discounts as $key => $discount) {

				// Check first to see if these are empty records. If so then just continue
				if ($discount['quantitymin'] == '' && $discount['quantitymax'] == '' && $discount['amount'] == '') {
					continue;
				}

				if ($discount['quantitymin'] == '') {
					$error = sprintf(GetLang('DiscountRulesQuantityMinRequired'), $key+1);
					return false;
				}

				if (!isId($discount['quantitymin']) && $discount['quantitymin'] !== '*') {
					$error = sprintf(GetLang('DiscountRulesQuantityMinInvalid'), $key+1);
					return false;
				}

				if ($discount['quantitymax'] == '') {
					$error = sprintf(GetLang('DiscountRulesQuantityMaxRequired'), $key+1);
					return false;
				}

				if (!isId($discount['quantitymax']) && $discount['quantitymax'] !== '*') {
					$error = sprintf(GetLang('DiscountRulesQuantityMaxInvalid'), $key+1);
					return false;
				}

				// Check to see if the min is still lower than the maximum quantity
				if ($discount['quantitymin'] !== '*' && $discount['quantitymax'] !== '*' && $discount['quantitymin'] > $discount['quantitymax']) {
					$error = sprintf(GetLang('DiscountRulesQuantityMinHigher'), $key+1);
					return false;
				}

				// Both min and max values cannot be astrix
				if ($discount['quantitymin'] == '*' && $discount['quantitymax'] == '*') {
					$error = sprintf(GetLang('DiscountRulesQuantityBothAstrix'), $key+1);
					return false;
				}

				// Check to see if the previous max and current min quantities are both astrixes
				if (!is_null($prevMax) && $prevMax == '*' && $discount['quantitymin'] == '*') {
					$error = sprintf(GetLang('DiscountRulesQuantityMinPrevMaxAstrix'), $key+1);
					return false;
				}

				// Check for overlapping
				if ($discount['quantitymin'] !== '*' && CheckNumericOverlapping($discount['quantitymin'], $overlap) == 1) {
					$error = sprintf(GetLang('DiscountRulesQuantityMinOverlap'), $key+1);
					return false;
				}
				if ($discount['quantitymax'] !== '*' && CheckNumericOverlapping($discount['quantitymin'], $overlap) == 1) {
					$error = sprintf(GetLang('DiscountRulesQuantityMinOverlap'), $key+1);
					return false;
				}

				// Check those values for our next loop
				if ($discount['quantitymin'] !== '*') {
					$overlap[0][] = $discount['quantitymin'];
				} else {
					$overlap[0][] = '';
				}

				if ($discount['quantitymax'] !== '*') {
					$overlap[1][] = $discount['quantitymax'];
				} else {
					$overlap[1][] = '';
				}

				$type = isc_strtolower(isc_html_escape($discount['type']));

				// Do we have the currect type?
				if ($type !== 'price' && $type !== 'percent' && $type !== 'fixed') {
					$error = sprintf(GetLang('DiscountRulesTypeInvalid'), $key+1);
					return false;
				}

				if ($discount['amount'] == '') {
					$error = sprintf(GetLang('DiscountRulesAmountRequired'), $key+1);
					return false;
				}

				// Do we have a valit price/percentage?
				if (!isId($discount['amount']) && CPrice($discount['amount']) == '') {
					$error = sprintf(GetLang('DiscountRulesAmountInvalid'), $key+1);
					return false;
				}

				// Now we do some checking compared againt the product price
				switch ($type) {
					case 'price':
						if (DefaultPriceFormat($discount['amount']) >= DefaultPriceFormat($_POST['prodPrice'])) {
							$error = sprintf(GetLang('DiscountRulesAmountPriceInvalid'), $key+1);
							return false;
						}
						break;

					case 'percent':
						if ((int)$discount['amount'] >= 100) {
							$error = sprintf(GetLang('DiscountRulesAmountPercentInvalid'), $key+1);
							return false;
						} else if (strpos($discount['amount'], '.') !== false) {
							$error = sprintf(GetLang('DiscountRulesAmountPercentIsFloat'), $key+1);
							return false;
						}
						break;

					case 'fixed':
						if ($discount['amount'] >= $_POST['prodPrice']) {
							$error = sprintf(GetLang('DiscountRulesAmountFixedInvalid'), $key+1);
							return false;
						}
						break;
				}

				// Store value to be used as previous value next time
				$prevMax = $discount['quantitymax'];
			}

			return true;
		}

		/**
		*	If the editor is disabled then we'll see if we need to run
		*	nl2br on the text if it doesn't contain any HTML tags
		*/
		public function FormatWYSIWYGHTML($HTML)
		{

			if(GetConfig('UseWYSIWYG')) {
				return $HTML;
			}
			else {
				// We need to sanitise all the line feeds first to 'nl'
				$HTML = SanatiseStringToUnix($HTML);

				// Now we can use nl2br()
				$HTML = nl2br($HTML);

				// But we still need to strip out the new lines as nl2br doesn't really 'replace' the new lines, it just inserts <br />before it
				$HTML = str_replace("\n", "", $HTML);

				// Fix up new lines and block level elements.
				$HTML = preg_replace("#(</?(?:html|head|body|div|p|form|table|thead|tbody|tfoot|tr|td|th|ul|ol|li|div|p|blockquote|cite|hr)[^>]*>)\s*<br />#i", "$1", $HTML);
				$HTML = preg_replace("#(&nbsp;)+(</?(?:html|head|body|div|p|form|table|thead|tbody|tfoot|tr|td|th|ul|ol|li|div|p|blockquote|cite|hr)[^>]*>)#i", "$2", $HTML);
				return $HTML;
			}
		}

		public function _GetProductData($ProductId = 0, &$RefArray = array())
		{
			// Gets the details of a product. If $ProductId is 0 then
			// the data is retrieved from the form. If not, it is retrieved
			// from the products table. Returns the data to the array
			// referenced by the $RefArray variable.

			if ($ProductId == 0) {
				// Get the data for this product from the form. The arrays
				// index names will match the table field names exactly.

				$RefArray['productid'] = 0;
				$RefArray['prodhash'] = $_POST['productHash'];
				$RefArray['prodname'] = $_POST['prodName'];
				$RefArray['prodcats'] = $_POST['category'];
				$RefArray['prodtype'] = $_POST['prodtype'];
				$RefArray['prodcode'] = $_POST['prodCode'];
				$RefArray['productVariationExisting'] = $_POST['productVariationExisting'];

				if(isset($_POST["wysiwyg_html"])) {
					$RefArray['proddesc'] = $this->FormatWYSIWYGHTML($_POST["wysiwyg_html"]);
				}
				else {
					$RefArray['proddesc'] = $this->FormatWYSIWYGHTML($_POST['wysiwyg']);
				}

				$RefArray['prodpagetitle'] = $_POST['prodPageTitle'];
				$RefArray['prodsearchkeywords'] = $_POST['prodSearchKeywords'];
				$RefArray['prodavailability'] = $_POST['prodAvailability'];
				$RefArray['prodprice'] = DefaultPriceFormat($_POST['prodPrice']);
				$RefArray['prodcostprice'] = DefaultPriceFormat($_POST['prodCostPrice']);
				$RefArray['prodretailprice'] = DefaultPriceFormat($_POST['prodRetailPrice']);
				$RefArray['prodsaleprice'] = DefaultPriceFormat($_POST['prodSalePrice']);
				$RefArray['prodsortorder'] = (int)$_POST['prodSortOrder'];
				if(isset($_POST['prodIsTaxable'])) {
					$RefArray['prodistaxable'] = (int)$_POST['prodIsTaxable'];
				}
				else {
					$RefArray['prodistaxable'] = 0;
				}

				$RefArray['prodwrapoptions'] = 0;
				if(isset($_POST['prodwraptype'])) {
					switch($_POST['prodwraptype']) {
						case 'custom':
							$RefArray['prodwrapoptions'] = implode(",", array_map('intval', $_POST['prodwrapoptions']));
							break;
						case 'none':
							$RefArray['prodwrapoptions'] = -1;
					}
				}

				if (isset($_POST['prodVisible'])) {
					$RefArray['prodvisible'] = 1;
				} else {
					$RefArray['prodvisible'] = 0;
				}

				// Only store admins can set the store featured status of an item
				if (isset($_POST['prodFeatured']) && !$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$RefArray['prodfeatured'] = 1;
				}
				else if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() > 0 && isset($_POST['productId']) && $_POST['productId'] > 0) {
					$this->_GetProductData($_POST['productId'], $existingProduct);
					$RefArray['prodfeatured'] = $existingProduct['prodfeatured'];
				}
				else {
					$RefArray['prodfeatured'] = 0;
				}

				// Anyone can set the vendor featured status
				if (isset($_POST['prodvendorfeatured'])) {
					$RefArray['prodvendorfeatured'] = 1;
				}
				else {
					$RefArray['prodvendorfeatured'] = 0;
				}

				$RefArray['prodhideprice'] = 0;
				$RefArray['prodcallforpricinglabel'] = '';
				if(isset($_POST['prodAllowPurchasing'])) {
					$RefArray['prodallowpurchases'] = 1;
				}
				else {
					$RefArray['prodallowpurchases'] = 0;
					if(isset($_POST['prodHidePrices'])) {
						$RefArray['prodhideprice'] = 1;
					}
					if(isset($_POST['prodCallForPricingLabel'])) {
						$RefArray['prodcallforpricinglabel'] = $_POST['prodCallForPricingLabel'];
					}
				}

				if(isset($_POST['prodRelatedAuto'])) {
					$RefArray['prodrelatedproducts'] = -1;	// Auto detected
				}
				else {
					if(isset($_POST['prodRelatedProducts'])) {
						$RefArray['prodrelatedproducts'] = implode(",", array_map('intval', $_POST['prodRelatedProducts']));
					}
					else {
						$RefArray['prodrelatedproducts'] = "";
					}
				}

				$RefArray['prodinvtrack'] = (int)$_POST['prodInvTrack'];

				// Is the inventory tracking per product? If so, get the
				// current and low stock level counts. If not, they are zero.

				if ($RefArray['prodinvtrack'] == 1) {
					$RefArray['prodcurrentinv'] = $_POST['prodCurrentInv'];
					$RefArray['prodlowinv'] = $_POST['prodLowInv'];
				} else {
					$RefArray['prodcurrentinv'] = 0;
					$RefArray['prodlowinv'] = 0;
				}

				$RefArray['prodtags'] = $_POST['prodTags'];

				$RefArray['prodweight'] = DefaultDimensionFormat($_POST['prodWeight']);
				$RefArray['prodwidth'] = DefaultDimensionFormat($_POST['prodWidth']);
				$RefArray['prodheight'] = DefaultDimensionFormat($_POST['prodHeight']);
				$RefArray['proddepth'] = DefaultDimensionFormat($_POST['prodDepth']);
				$RefArray['prodfixedshippingcost'] = DefaultPriceFormat($_POST['prodFixedCost']);

				$RefArray['prodwarranty'] = $_POST['prodWarranty'];

				$RefArray['prodpagetitle'] = $_POST['prodPageTitle'];
				// Handle the META keywords
				$RefArray['prodmetakeywords'] = $_POST['prodMetaKeywords'];
				$RefArray['prodmetadesc'] = $_POST['prodMetaDesc'];

				if (isset($_POST['prodFreeShipping'])) {
					$RefArray['prodfreeshipping'] = 1;
				} else {
					$RefArray['prodfreeshipping'] = 0;
				}

				if (isset($_POST['prodOptionsRequired'])) {
					$RefArray['prodoptionsrequired'] = 1;
				} else {
					$RefArray['prodoptionsrequired'] = 0;
				}

				// Workout the brand of the product
				$RefArray['prodbrandid'] = (int)$_POST['brandbox'];

				$RefArray['prodlayoutfile'] = $_POST['prodlayoutfile'];

				if($_POST['brandname'] != "" && $GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Add_Brands)) {
					// Do we need to add the brandname as a new brand?
					$brandName = $_POST['brandname'];
					$query = sprintf("select brandid from [|PREFIX|]brands where lower(brandname)='%s'", isc_strtolower($brandName));
					$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
					$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

					if($row !== false) {
						// It's an existing brand, no need to add it
						$RefArray['prodbrandid'] = $row['brandid'];
					}
					else {
						// It's a new brand, we need to save it
						$newBrand = array(
							"brandname" => $brandName,
							"brandpagetitle" => "",
							"brandmetakeywords" => "",
							"brandmetadesc" => ""
						);
						$GLOBALS['ISC_CLASS_DB']->InsertQuery("brands", $newBrand);

						if($GLOBALS["ISC_CLASS_DB"]->GetErrorMsg() == "") {
							$RefArray['prodbrandid'] = $GLOBALS["ISC_CLASS_DB"]->LastId();
						}
						else {
							$RefArray['prodbrandid'] = 0;
						}
					}
				}

				$RefArray['prodeventdaterequired'] = 0;
				$RefArray['prodeventdatefieldname'] = '';
				$RefArray['prodeventdatelimited'] = 0;
				$RefArray['prodeventdatelimitedtype'] = 0;
				$RefArray['prodeventdatelimitedstartdate'] = 0;
				$RefArray['prodeventdatelimitedenddate'] = 0;

				if (isset($_POST['EventDateRequired'])) {
					$RefArray['prodeventdaterequired'] = true;
				}
				if (isset($_POST['EventDateFieldName'])) {
					$RefArray['prodeventdatefieldname'] = $_POST['EventDateFieldName'];
				}
				if (isset($_POST['LimitDates'])) {
					$RefArray['prodeventdatelimited'] = true;
				}
				if (isset($_POST['LimitDatesSelect'])) {
					$RefArray['prodeventdatelimitedtype'] = (int)$_POST['LimitDatesSelect'];

					switch ($RefArray['prodeventdatelimitedtype']) {
						case 1:
							$cal = $_POST['Calendar1'];
							$RefArray['prodeventdatelimitedstartdate'] = isc_gmmktime(0, 0, 0, (int)$cal['From']['Mth'],(int)$cal['From']['Day'],(int)$cal['From']['Yr']);
							$RefArray['prodeventdatelimitedenddate'] = isc_gmmktime(0, 0, 0, (int)$cal['To']['Mth'],(int)$cal['To']['Day'],(int)$cal['To']['Yr']);
						break;

						case 2:
							$cal = $_POST['Calendar2'];
							$RefArray['prodeventdatelimitedstartdate'] = isc_gmmktime(0, 0, 0, (int)$cal['From']['Mth'],(int)$cal['From']['Day'],(int)$cal['From']['Yr']);
						break;

						case 3:
							$cal = $_POST['Calendar3'];
							$RefArray['prodeventdatelimitedenddate'] = isc_gmmktime(0, 0, 0, (int)$cal['To']['Mth'],(int)$cal['To']['Day'],(int)$cal['To']['Yr']);
						break;
					}
				}

				// The ID of the variation the product is using
				if(isset($_POST['variationId']) && is_numeric($_POST['variationId']) && $_POST['prodtype'] == PT_PHYSICAL) {
					$RefArray['prodvariationid'] = (int)$_POST['variationId'];
				}
				else {
					$RefArray['prodvariationid'] = 0;
				}

				$RefArray['prodvendorid'] = 0;
				if(gzte11(ISC_HUGEPRINT)) {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					// User is assigned to a vendor so any products they create must be too
					if(isset($vendorData['vendorid'])) {
						$RefArray['prodvendorid'] = $vendorData['vendorid'];
					}
					else if(isset($_POST['vendor'])) {
						$RefArray['prodvendorid'] = (int)$_POST['vendor'];
					}
				}

				$RefArray['prodmyobasset'] = $_POST['prodMYOBAsset'];
				$RefArray['prodmyobincome'] = $_POST['prodMYOBIncome'];
				$RefArray['prodmyobexpense'] = $_POST['prodMYOBExpense'];

				$RefArray['prodpeachtreegl'] = $_POST['prodPeachtreeGL'];

				$RefArray['prodcondition'] = $_POST['prodCondition'];
				if (isset($_POST['prodShowCondition'])) {
					$RefArray['prodshowcondition'] = 1;
				}
				else {
					$RefArray['prodshowcondition'] = 0;
				}

				// product videos
				$RefArray['product_videos'] = array();
				if(isset($_POST['videos'])) {
					$RefArray['product_videos'] = $_POST['videos'];
				}

				// product images
				$RefArray['product_images'] = array();

				if (isset($_POST['prodEnableOptimizer'])) {
					$RefArray['product_enable_optimizer'] = 1;
				} else {
					$RefArray['product_enable_optimizer'] = 0;
				}

			} else {
				// Get the data for this product from the database
				$query = sprintf("select * from [|PREFIX|]products where productid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($ProductId));
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
				$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

				if ($row !== false) {
					$RefArray = $row;
				}

				// Get the categories that this product appears in
				$RefArray['prodcats'] = array();
				$query = sprintf("select categoryid from [|PREFIX|]categoryassociations where productid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($ProductId));
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

				while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
					$RefArray['prodcats'][] = $row['categoryid'];
				}

				// Are there any related products?
				if ($RefArray['prodrelatedproducts'] != "") {
					$query = sprintf("select productid, prodname from [|PREFIX|]products where productid in (%s)", $RefArray['prodrelatedproducts']);
					$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

					while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
						$RefArray['prodrelated'][] = array($row['productid'], $row['prodname']);
					}
				}

				// Fetch any tags as a CSV list
				$query = "
					SELECT t.tagname
					FROM [|PREFIX|]product_tagassociations a
					INNER JOIN [|PREFIX|]product_tags t ON (t.tagid=a.tagid)
					WHERE a.productid='".(int)$ProductId."'
				";
				$productTags = array();
				$RefArray['prodtags'] = '';
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$productTags[] = $row['tagname'];
				}
				$RefArray['prodtags'] = implode(', ', $productTags);

				// Grab the videos from the database
				$query = 'select * from `[|PREFIX|]product_videos` where video_product_id=' . (int)$ProductId . ' order by `video_sort_order` asc';
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

				while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
					$RefArray['product_videos'][$row['video_id']] = array('title' => $row['video_title'], 'desc' => $row['video_description'], 'length' => $row['video_length']);
				}

				// product images
				$RefArray['product_images'] = ISC_PRODUCT_IMAGE::getProductImagesFromDatabase($ProductId);
			}
		}

		public function _StoreFileAndReturnId($FileName, $FileType)
		{
			// This function takes a file name as its arguement and stores
			// this file in the /downloads or /images directory depending
			// on the $FileType enumeration value

			if ($FileType == FT_DOWNLOAD) {
				$dir = GetConfig('DownloadDirectory');
			}
			else {
				$dir = GetConfig('ImageDirectory');
			}

			if (is_array($_FILES[$FileName]) && $_FILES[$FileName]['name'] != "") {
				// If it's an image, make sure it's a valid image type
				if ($FileType == FT_IMAGE && isc_strtolower(isc_substr($_FILES[$FileName]['type'], 0, 5)) != "image") {
					return "";
				}

				if (is_dir(sprintf("../%s", $dir))) {
					// Images and downloads will be stored within a directory randomly chosen from a-z.
					$randomDir = strtolower(chr(rand(65, 90)));
					if(!is_dir("../".$dir."/".$randomDir)) {
						if(!@mkdir("../".$dir."/".$randomDir, ISC_WRITEABLE_DIR_PERM)) {
							$randomDir = '';
						}
					}

					// Clean up the incoming file name a bit
					$_FILES[$FileName]['name'] = preg_replace("#[^\w.]#i", "_", $_FILES[$FileName]['name']);
					$_FILES[$FileName]['name'] = preg_replace("#_{1,}#i", "_", $_FILES[$FileName]['name']);

					$randomFileName = GenRandFileName($_FILES[$FileName]['name']);
					$fileName = $randomDir . "/" . $randomFileName;
					$dest = realpath(ISC_BASE_PATH."/" . $dir);
					while(file_exists($dest."/".$fileName)) {
						$fileName = basename($randomFileName);
						$fileName = substr_replace($randomFileName, "-".rand(0, 10000000000), strrpos($randomFileName, "."), 0);
						$fileName = $randomDir . "/" . $fileName;
					}
					$dest .= "/".$fileName;

					if(move_uploaded_file($_FILES[$FileName]["tmp_name"], $dest)) {
						isc_chmod($dest, ISC_WRITEABLE_FILE_PERM);
						// The file was moved successfully
						return $fileName;
					}
					else {
						// Couldn't move the file, maybe the directory isn't writable?
						return "";
					}
				} else {
					// The directory doesn't exist
					return "";
				}
			} else {
				// The file doesn't exist in the $_FILES array
				return "";
			}
		}

		/**
		* Get a list of files in the product_downloads/import directory which can be associated
		* with the product as download file
		*
		* @return string The html for the options
		*/
		public function _GetImportFilesOptions()
		{
			$files = $this->_GetImportFilesArray();
			$format = '<option value="%1$s">%1$s</option>'."\n";
			$output = '';
			if (is_array($files)) {
				foreach ($files as $file) {
					$output .= sprintf($format, isc_html_escape($file));
				}
			}
			return $output;
		}

		/**
		* Get a list of files in the product_downloads/import directory which can be associated
		* with the product as download file
		*
		* @return string The array of file names
		*/
		public function _GetImportFilesArray()
		{
			if(!is_dir(ISC_BASE_PATH.'/'.GetConfig('DownloadDirectory').'/import')) {
				return;
			}
			$files = scandir(ISC_BASE_PATH.'/'.GetConfig('DownloadDirectory').'/import');
			$ignore_files = array ('.', '..', '.svn', 'CVS', 'Thumbs.db');
			$files = array_diff($files, $ignore_files);
			return $files;
		}

		public function _CommitProduct($ProductId, &$Data, &$Variations, &$CustomFields, $DiscountRules=array(), &$Err = null, &$ProductFields=array(), $isImport=false)
		{
			// Commit the details for the product to the database
			$query = "";
			$err = null;
			$searchData = array(
				"prodname" => $Data['prodname'],
				"prodcode" => $Data['prodcode'],
				"proddesc" => stripHTMLForSearchTable($Data['proddesc']),
				"prodsearchkeywords" => $Data['prodsearchkeywords']
			);

			// Start the transaction
			$GLOBALS["ISC_CLASS_DB"]->Query("start transaction");
			$updateImageQuery = "";

			if ($ProductId == 0) {
				// Add the date this product was modified
				$prodId = $this->productEntity->add($Data);

				$GLOBALS['NewProductId'] = $prodId;

				// ---- Build the query for the product_search table ----
				$searchData['productid'] = $prodId;
				$GLOBALS['ISC_CLASS_DB']->InsertQuery("product_search", $searchData);

				// Build the queries for the videos table -----



				// Set some $_GET variables so the newest product appears at the top of the list
				$_GET['sortField'] = "productid";
				$_GET['sortOrder'] = "desc";

				// Save the product tags
				$this->SaveProductTags($Data['prodtags'], $prodId, true);
			}
			else {
				// Update the existing products details
				$prodId = $Data['productid'] = (int)$ProductId;
				$this->productEntity->edit($Data);

				// Update the search data
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_search", $searchData, "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($prodId)."'");

				if (isset($Data['prodcats']) && $Data['prodcats'] != null) {
					// Remove the existing category associations
					$query = sprintf("DELETE FROM [|PREFIX|]categoryassociations WHERE productid='%d'", $prodId);
					$GLOBALS['ISC_CLASS_DB']->Query($query);
				}

				// Save the product tags
				$this->SaveProductTags($Data['prodtags'], $ProductId, false);
			}

			// Save the videos associated with the product
			if (isset($Data['product_videos'])) {
				// need the isset check as the importer isn't providing video data
				$this->saveProductVideos($prodId, $Data['product_videos']);
			}

			//save optimizer settings for this product
			$optimizer = getClass('ISC_ADMIN_OPTIMIZER');
			if(isset($Data['product_enable_optimizer']) && $Data['product_enable_optimizer']==1) {
				$optimizer->savePerItemOptimizerConfig('product', $prodId);
			} else {
				$optimizer->deletePerItemOptimizerConfig('product', array($prodId));
			}

			// Build the queries for the category associations table -----
			$accessibleCategories = array();
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorInfo = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				if($vendorInfo['vendoraccesscats']) {
					$accessibleCategories = explode(',', $vendorInfo['vendoraccesscats']);
				}
			}
			if(isset($Data['prodcats'])) {
				foreach ($Data['prodcats'] as $cat) {
					// If this user doesn't have permission to place products in this category, skip over it
					if(!empty($accessibleCategories) && !in_array($cat, $accessibleCategories)) {
						continue;
					}
					$newAssociation = array(
						"productid" => $prodId,
						"categoryid" => $cat
					);
					$GLOBALS['ISC_CLASS_DB']->InsertQuery("categoryassociations", $newAssociation);
				}
			}

			/**
			 * Was this product commited from the batch importer? If so then exit now or we'll ruin all the other product linked tables
			 */
			if ($isImport) {
				if($GLOBALS["ISC_CLASS_DB"]->GetErrorMsg() == "") {
					// The product was commited successfully, commit the transaction
					$GLOBALS["ISC_CLASS_DB"]->Query("commit");
					return true;
				}
				else {
					// The product commit failed
					$GLOBALS["ISC_CLASS_DB"]->Query("rollback");
					return false;
				}
			}

			// Build the queries for the product variation combinations table -----
			$sumCurrent = 0;
			$sumLow = 0;

			// first delete any temporary combinations NOT for the chosen variation
			if (!empty($Data['prodhash'])) {
				$prodIdOrHash = $Data['prodhash'];
			}
			else {
				$prodIdOrHash = $prodId;
			}

			$this->DeleteTemporaryCombinationsForProduct($prodIdOrHash, $Data['prodvariationid']);


			/**
			 * Associated any hashed variations with the new product ID
			 */
			if (isset($Data['prodhash']) && $Data['prodhash'] !== '') {
				$savedata = array(
					'vcproductid' => $prodId,
					'vcproducthash' => '',
					'vclastmodified' => time()
				);

				$GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_variation_combinations', $savedata, "vcproducthash='" . $GLOBALS['ISC_CLASS_DB']->Quote($Data['prodhash']) . "'");
			}


			if(isset($Data['prodvariationid']) && $Data['prodvariationid'] != 0 && isset($Variations) && is_array($Variations) && $Data['prodtype'] == PT_PHYSICAL) {
				// have we selected a variation that isn't the original variation? we need to move temp combinations to real ones
				if ($Data['productVariationExisting'] != $Data['prodvariationid']) {
					// first nuke off any existing variation data
					if ($Data['productVariationExisting'] > 0) {
						$this->_DeleteVariationCombinationsForProduct($prodId);
					}

					$savedata = array(
						'vcproductid' => $prodId,
						'vcproducthash' => ''
					);

					$GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_variation_combinations', $savedata, "vcproducthash='" . $GLOBALS['ISC_CLASS_DB']->Quote($prodId) . "'");
				}


				// update existing variations
				foreach($Variations as $Variation) {
					// First up, do we need to delete the image?
					if($Variation['vcimage'] == "REMOVE") {
						// Yes, get the image details
						$query = "
							SELECT
								vcimage,
								vcimagezoom,
								vcimagestd,
								vcimagethumb
							FROM
								[|PREFIX|]product_variation_combinations
							WHERE
								combinationid = " . $Variation['combinationid'];
						$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
						$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

						$this->DeleteVariationImagesForRow($row);
					}

					// Now update the record
					$updatedCombo = array(
						"vcproductid" => $prodId,
						"vcvariationid" => $Variation['vcvariationid'],
						"vcenabled" => $Variation['vcenabled'],
						"vcoptionids" => $Variation['vcoptionids'],
						"vcsku" => $Variation['vcsku'],
						"vcpricediff" => $Variation['vcpricediff'],
						"vcprice" => $Variation['vcprice'],
						"vcweightdiff" => $Variation['vcweightdiff'],
						"vcweight" => $Variation['vcweight'],
						"vcstock" => $Variation['vcstock'],
						"vclowstock" => $Variation['vclowstock'],
						"vclastmodified" => time()
					);

					// Only update the images if they've changed
					if($Variation['vcimage'] == "REMOVE") {
						$updatedCombo['vcimage'] = "";
						$updatedCombo['vcimagezoom'] = "";
						$updatedCombo['vcimagestd'] = "";
						$updatedCombo['vcimagethumb'] = "";
					}
					else if($Variation['vcimagezoom'] != "") {
						$updatedCombo['vcimage'] = $Variation['vcimage'];
						$updatedCombo['vcimagezoom'] = $Variation['vcimagezoom'];
						$updatedCombo['vcimagestd'] = $Variation['vcimagestd'];
						$updatedCombo['vcimagethumb'] = $Variation['vcimagethumb'];
					}

					$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_variation_combinations", $updatedCombo, "combinationid='".$GLOBALS['ISC_CLASS_DB']->Quote($Variation['combinationid'])."'");
					$sumCurrent += $Variation['vcstock'];
					$sumLow += $Variation['vclowstock'];
				}

				// If the inventory tracking is happening per product variation then we need to add
				// the current and low stock level sums to the products table
				if ($Data['prodinvtrack'] == 2) {
					$updatedProduct = array(
						"prodcurrentinv" => $sumCurrent,
						"prodlowinv" => $sumLow
					);
					$GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updatedProduct,  "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($prodId)."'");
				}
			}
			else {
				// If it's an existing product then we need to delete all of the variation combinations, images, etc
				if($prodId > 0) {
					$this->_DeleteVariationCombinationsForProduct($prodId);
				}
			}

			// Build the queries for the custom fields table -----
			$GLOBALS['ISC_CLASS_DB']->Query("DELETE FROM [|PREFIX|]product_customfields WHERE fieldprodid='".$GLOBALS['ISC_CLASS_DB']->Quote((int) $prodId)."'");
			if (!empty($CustomFields)) {
				foreach ($CustomFields as $c) {
					$newField = array(
						"fieldprodid" => $prodId,
						"fieldname" => $c['name'],
						"fieldvalue" => $c['value']
					);
					$GLOBALS['ISC_CLASS_DB']->InsertQuery("product_customfields", $newField);
				}
			}

			$this->_SaveProductFields($ProductFields, $prodId);

			// Upload any product downloads if we have them
			if(isset($_FILES) && isset($_FILES['newdownload']) && isset($_FILES['newdownload']['name']) && $_FILES['newdownload']['name'] != '') {
				$this->SaveProductDownload($err);
			}

			// Associate any product images and downloads which were uploaded earlier with this product
			if(isset($Data['prodhash']) && $Data['prodhash'] !== '') {
				$updateImages = array(
					"imageprodid" => $prodId,
					"imageprodhash" => ''
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_images", $updateImages, "imageprodhash='".$GLOBALS['ISC_CLASS_DB']->Quote($Data['prodhash'])."'".$updateImageQuery);

				$updatedDownloads = array(
					"productid" => $prodId,
					"prodhash" => ''
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_downloads", $updatedDownloads, "prodhash='".$GLOBALS['ISC_CLASS_DB']->Quote($Data['prodhash'])."'");
			}

			// Now we add our discount rules
			$GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_discounts', 'WHERE discountprodid=' . (int)$prodId);

			// If we have variations then do not process them
			if((!isset($Data['prodvariationid']) || !isId($Data['prodvariationid'])) && empty($Variations)) {
				foreach ($DiscountRules as $rule) {

					// If the min and max quantities are astrixes then convert them to 0
					if ($rule['quantitymin'] == '*') {
						$rule['quantitymin'] = 0;
					}

					if ($rule['quantitymax'] == '*') {
						$rule['quantitymax'] = 0;
					}

					// Change the type of the amount, just in case
					if (isc_strtolower($rule['type']) == 'percent') {
						$rule['amount'] = (int)$rule['amount'];
					}

					// Fix for bug ISC-219: Removed code
					// Casting $rule['amount'] to a float using (float) here, if the amount was a dollar amount, would change a string of '12,000.00' to 12 (it'd cut at the comma)
					// If the amount is not a percentage, DefaultPriceFormat() below will ensure the amount is formatted and sanitized properly

					$newRule = array(
						'discountprodid' => (int)$prodId,
						'discountquantitymin' => (int)$rule['quantitymin'],
						'discountquantitymax' => (int)$rule['quantitymax'],
						'discounttype' => isc_strtolower($rule['type']),
						'discountamount' => DefaultPriceFormat($rule['amount'])
					);

					$GLOBALS['ISC_CLASS_DB']->InsertQuery('product_discounts', $newRule);
				}
			}

			if($GLOBALS["ISC_CLASS_DB"]->GetErrorMsg() == "") {
				// The product was commited successfully, commit the transaction
				$GLOBALS["ISC_CLASS_DB"]->Query("commit");
				return true;
			}
			else {
				// The product commit failed
				$GLOBALS["ISC_CLASS_DB"]->Query("rollback");
				return false;
			}
		}



		/**
		* This function saves the videos associated with a product
		*
		* @param integer $productId The ID of the product with which to associate the videos
		* @param array $videos The array of video IDs to insert into the database, in their order in the array
		*/
		public function saveProductVideos ($productId, $videos)
		{
			$productId = (int)$productId;
			if($productId < 1) {
				return;
			}

			// remove existing videos
			$this->db->Query("DELETE from `[|PREFIX|]product_videos` where video_product_id=" . $productId);

			// add new videos in the correct order
			$sortOrder = 0;
			foreach($videos as $videoId => $videoData) {
				if(empty($videoId) || empty($productId)) {
					continue;
				}

				$insertValues = array(
					'video_product_id'  => $productId,
					'video_id'          => $videoId,
					'video_sort_order'  => $sortOrder,
					'video_title'       => $videoData['title'],
					'video_description' => $videoData['desc'],
					'video_length'      => $videoData['length'],
				);

				$this->db->InsertQuery('product_videos', $insertValues);
				++$sortOrder;
			}
		}

		public function DoDeleteProducts($ids)
		{
			if(!is_array($ids)) {
				$ids = array($ids);
			}

			foreach ($ids as $key => $id) {
				if (!is_numeric($id) || $id<=0) {
					unset($ids[$key]);
				}
			}

			// Start a transaction
			$GLOBALS["ISC_CLASS_DB"]->Query("start transaction");

			// The products and related data will be removed from the following tables:
			//
			//     - Products
			//     - CategoryAssociations
			//     - Product_CustomFields
			//     - Product_Images
			//     - Product_Variation_Combinations
			//     - Product_Downloads
			//		- product_configurable_fields

			// What we do here is feed the list of product IDs in to a query with the vendor applied so that way
			// we're sure we're only deleting products this user has permission to delete.
			$prodids = implode("','", array_map('intval', $ids));
			$vendorId = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
			if($vendorId > 0) {
				$query = "
					SELECT productid
					FROM [|PREFIX|]products
					WHERE productid IN ('".$prodids."') AND prodvendorid='".(int)$vendorId."'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$prodids = array(0);
				while($product = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$prodids[] = $product['productid'];
				}
				$prodids = implode("','", array_map('intval', $prodids));
			}

			// Build a list of queries to execute
			$queries[] = sprintf("delete from [|PREFIX|]categoryassociations where productid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]product_customfields where fieldprodid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]reviews where revproductid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]product_search where productid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]product_words where productid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]product_downloads where productid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]wishlist_items where productid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]product_configurable_fields where fieldprodid in ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]customer_group_discounts where discounttype='PRODUCT' AND catorprodid IN ('%s')", $prodids);
			$queries[] = sprintf("delete from [|PREFIX|]product_videos where video_product_id IN ('%s')", $prodids);

			//delete google website optimizer test details for the products
			$optimizer = getClass('ISC_ADMIN_OPTIMIZER');
			$optimizer->deletePerItemOptimizerConfig('product', $ids);

			// Delete the product downloads from the file system
			$query = sprintf("select downfile from [|PREFIX|]product_downloads where productid in ('%s')", $prodids);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				@unlink(APP_ROOT."/../".GetConfig('DownloadDirectory')."/".$row['downfile']);
			}

			$vc_queries = $this->_DeleteVariationCombinationsForProduct($prodids, true);
			$queries = array_merge($vc_queries, $queries);

			// Delete the product record here so we can keep a record of what was deleted for the accounting modules
			$this->productEntity->multiDelete($ids);

			foreach ($queries as $query) {
				$GLOBALS["ISC_CLASS_DB"]->Query($query);
			}
			$err = $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg();

			if($err != "") {
				// Queries failed, rollback
				$GLOBALS["ISC_CLASS_DB"]->Query("rollback");
				return false;
			}
			else {
				// Query was a success
				$GLOBALS["ISC_CLASS_DB"]->Query("commit");
				return true;
			}
		}

		/**
		* _DeleteVariationCombinationsForProduct
		* Delete variation combinations for a product, including the images
		*
		* @param String $ProductIds The id(s) of the products to delete varations for in CSV, such as 105,106
		* @param Boolean $ReturnQueries If true, the queries will be returned as an array. If false, they will be ran instead.
		* @return String
		*/
		public function _DeleteVariationCombinationsForProduct($ProductIds, $ReturnQueries=false)
		{
			$queries = array();

			// Delete the product combination images from the file system
			$query = "
				SELECT
					vcimage,
					vcimagezoom,
					vcimagestd,
					vcimagethumb
				FROM
					[|PREFIX|]product_variation_combinations
				WHERE
					vcproductid IN ('" . $ProductIds . "')";

			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				$this->DeleteVariationImagesForRow($row);
			}

			// Now delete the entries in the product_variation_combinations table
			$queries[] = "DELETE FROM [|PREFIX|]product_variation_combinations WHERE vcproductid IN ('" . $ProductIds . "')";

			if($ReturnQueries) {
				return $queries;
			}
			else {
				$GLOBALS["ISC_CLASS_DB"]->Query($queries[0]);
			}
		}

		public function DeleteProducts()
		{
			$queries = array();

			if(isset($_POST['products'])) {
				if(!$this->DoDeleteProducts($_POST['products'])) {
					$err = $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg();
					$this->ManageProducts($err, MSG_ERROR);
				}
				else {
					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($_POST['products']));
					$this->ManageProducts(GetLang('ProductsDeletedSuccessfully'), MSG_SUCCESS);
				}
			}
			else {
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts();
				}
				else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}
			}
		}

		public function EditVisibility()
		{
			// Update the visibility of a product with a simple query

			$prodId = (int)$_GET['prodId'];
			$visible = (int)$_GET['visible'];

			$query = sprintf("SELECT prodname, prodvendorid FROM [|PREFIX|]products WHERE productid='%d'", $prodId);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$product = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			// Does this user have permission to toggle the visibility for this product?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $product['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				echo 0;
				exit;
			}

			// Log this action
			$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($prodId, $product['prodname']);

			$updatedProduct = array(
				"prodvisible" => $visible
			);
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updatedProduct, "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($prodId)."'");

			unset($_REQUEST['visible']);
			unset($_GET['visible']);

			if ($GLOBALS["ISC_CLASS_DB"]->Error() == "") {
				if(isset($_REQUEST['ajax'])) {
					echo 1;
					exit;
				}

				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(GetLang('ProductVisibleSuccessfully'), MSG_SUCCESS);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('ProductVisibleSuccessfully'), MSG_SUCCESS);
				}
			} else {
				if(isset($_REQUEST['ajax'])) {
					echo 0;
					exit;
				}
				$err = '';
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(sprintf(GetLang('ErrVisibilityNotChanged'), $err), MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrVisibilityNotChanged'), $err), MSG_ERROR);
				}
			}
		}

		public function EditFeatured()
		{
			// Update the visibility of a product with a simple query

			$prodId = (int)$_GET['prodId'];
			$featured = (int)$_GET['featured'];

			$query = sprintf("SELECT prodname, prodvendorid FROM [|PREFIX|]products WHERE productid='%d'", $prodId);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$product = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			// Does this user have permission to toggle the featured status for this product?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $product['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				echo 0;
				exit;
			}

			// Log this action
			$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($prodId, $product['prodname']);

			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$featuredColumn = 'prodvendorfeatured';
			}
			else {
				$featuredColumn = 'prodfeatured';
			}

			$updatedProduct = array(
				$featuredColumn => $featured
			);
			$GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updatedProduct, "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($prodId)."'");

			unset($_REQUEST['featured']);
			unset($_GET['featured']);

			if ($GLOBALS["ISC_CLASS_DB"]->Error() == "") {
				if(isset($_REQUEST['ajax'])) {
					echo 1;
					exit;
				}
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(GetLang('ProductVisibleSuccessfully'), MSG_SUCCESS);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('ProductVisibleSuccessfully'), MSG_SUCCESS);
				}
			} else {
				if(isset($_REQUEST['ajax'])) {
					echo 0;
					exit;
				}
				$err = '';
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(sprintf(GetLang('ErrVisibilityNotChanged'), $err), MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrVisibilityNotChanged'), $err), MSG_ERROR);
				}
			}
		}

		public function ManageProductsGrid(&$numProducts)
		{
			// Show a list of products in a table
			$page = 0;
			$start = 0;
			$numProducts = 0;
			$numPages = 0;
			$GLOBALS['ProductGrid'] = "";
			$GLOBALS['Nav'] = "";
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

			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$featuredColumn = 'prodvendorfeatured';
			}
			else {
				$featuredColumn = 'prodfeatured';
			}

			$validSortFields = array('productid', 'prodcode', 'currentinv', 'prodname', 'prodcalculatedprice', 'prodvisible', $featuredColumn);

			if(isset($_REQUEST['sortOrder']) && $_REQUEST['sortOrder'] == "asc") {
				$sortOrder = "asc";
			}
			else {
				$sortOrder = "desc";
			}

			if(isset($_REQUEST['sortField']) && in_array($_REQUEST['sortField'], $validSortFields)) {
				$sortField = $_REQUEST['sortField'];
				SaveDefaultSortField("ManageProducts", $_REQUEST['sortField'], $sortOrder);
			} else {
				list($sortField, $sortOrder) = GetDefaultSortField("ManageProducts", "productid", $sortOrder);
			}


			if(isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			}
			else {
				$page = 1;
			}

			if(isset($_GET['filterCategory']) && $_GET['filterCategory'] == "-1") {
				$GLOBALS['FilterLow'] = "selected=\"selected\"";
			}

			if(isset($_GET['filterCategory'])) {
				$filterCat = (int)$_GET['filterCategory'];
			}
			else {
				$filterCat = 0;
			}

			if(!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS['ProductNameSpan'] = 3;
				$GLOBALS['HideInventoryOptions'] = 'none';
			}
			else {
				$GLOBALS['HideInventoryOptions'] = '';
			}

			// Build the pagination and sort URL
			$searchURL = '';
			foreach($_GET as $k => $v) {
				if($k == "sortField" || $k == "sortOrder" || $k == "page" || $k == "new" || $k == "ToDo" || $k == "SubmitButton1" || $k == "ISSelectReplacement_category" || (!$v && $v !== '0')) {
					continue;
				}
				if(is_array($v)) {
					foreach($v as $v2) {
						$searchURL .= sprintf("&%s[]=%s", $k, urlencode($v2));
					}
				}
				else {
					$searchURL .= sprintf("&%s=%s", $k, urlencode($v));
				}
			}

			// Build the letter sorting
			$letterURL = sprintf("%s&amp;sortField=%s&amp;sortOrder=%s", preg_replace("#&letter=[a-zA-Z0-9\-]{1,2}#i", "", $searchURL), $sortField, $sortOrder);
			$GLOBALS['LetterURL'] = $letterURL;
			$extra = '';
			if(isset($_REQUEST['letter']) && $_REQUEST['letter'] == "0-9") {
				$extra = 'ActiveLetter';
			}
			$GLOBALS['LetterSortGrid'] = sprintf('<td width="3%%"><a href="index.php?ToDo=viewProducts%s&amp;letter=0-9" title="%s" class="SortLink %s">#</a></td>', $letterURL, sprintf(GetLang('ViewProductsLetter'), '0-9'), $extra);
			$letters = preg_split('%,\s+%s', GetLang('Alphabet'));
			foreach ($letters as $letter) {
				$extra = '';
				if (isset($_REQUEST['letter']) && $_REQUEST['letter'] == $letter) {
					$extra = 'ActiveLetter';
				}
				$GLOBALS['LetterSortGrid'] .= sprintf('<td width="3%%"><a href="index.php?ToDo=viewProducts%s&amp;letter=%s" title="%s" class="SortLink %s">%s</a></td>', $letterURL, $letter, sprintf(GetLang('ViewProductsLetter'), isc_strtoupper($letter)), $extra, isc_strtoupper($letter));
			}
			$letter = GetLang('Clear');
			$GLOBALS['LetterSortGrid'] .= sprintf('<td width="3%%"><a href="index.php?ToDo=viewProducts%s" class="SortLink">%s</a></td>', $letterURL, $letter, $letter);

			$sortURL = sprintf("%s&amp;sortField=%s&amp;sortOrder=%s", $searchURL, $sortField, $sortOrder);
			$GLOBALS['SortURL'] = $sortURL;

			// Limit the number of questions returned
			if($page == 1) {
				$start = 1;
			}
			else {
				$start = ($page * ISC_PRODUCTS_PER_PAGE) - (ISC_PRODUCTS_PER_PAGE-1);
			}

			$start = $start-1;

			// Get the results for the query
			$product_result = $this->_GetProductList($start, $sortField, $sortOrder, $numProducts);
			$numPages = ceil($numProducts / ISC_PRODUCTS_PER_PAGE);

			// Add the "(Page x of n)" label
			if($numProducts > ISC_PRODUCTS_PER_PAGE) {
				$GLOBALS['Nav'] = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);

				$GLOBALS['Nav'] .= BuildPagination($numProducts, ISC_PRODUCTS_PER_PAGE, $page, sprintf("index.php?ToDo=viewProducts%s", $sortURL));
			}
			else {
				$GLOBALS['Nav'] = "";
			}

			if (isset($_REQUEST['searchQuery'])) {
				$query = $_REQUEST['searchQuery'];
			} else {
				$query = '';
			}

			$GLOBALS['EscapedQuery'] = isc_html_escape($query);

			$GLOBALS['Nav'] = preg_replace('# \|$#',"", $GLOBALS['Nav']);
			$GLOBALS['SearchQuery'] = isc_html_escape($query);
			$GLOBALS['SortField'] = $sortField;
			$GLOBALS['SortOrder'] = $sortOrder;


			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$featuredColumn = 'prodvendorfeatured';
			}
			else {
				$featuredColumn = 'prodfeatured';
			}

			$sortLinks = array(
				"Code" => "prodcode",
				"Stock" => "currentinv",
				"Name" => "prodname",
				"Price" => "prodcalculatedprice",
				"Visible" => "prodvisible",
				"Featured" => $featuredColumn
			);

			BuildAdminSortingLinks($sortLinks, "index.php?ToDo=viewProducts&amp;".$searchURL."&amp;page=".$page, $sortField, $sortOrder);


			// Workout the maximum size of the array
			$max = $start + ISC_PRODUCTS_PER_PAGE;

			if ($max > $numProducts) {
				$max = $numProducts;
			}

			if($numProducts > 0) {
				// Display the products
				while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($product_result)) {
					if ($row['prodcode'] == "") {
						$GLOBALS['SKU'] = GetLang('NA');
					} else {
						$GLOBALS['SKU'] = isc_html_escape($row['prodcode']);
					}

					$GLOBALS['ProductId'] = (int)$row['productid'];
					$GLOBALS['Name'] = sprintf("<a title='%s' class='Action' href='%s' target='_blank'>%s</a>", GetLang('ProductView'), ProdLink($row['prodname']), isc_html_escape($row['prodname']));

					// Do we need to show product thumbnails?
					if(GetConfig('ShowThumbsInControlPanel')) {
						if ($row['imageid'] !== null) {
							$image = new ISC_PRODUCT_IMAGE();
							$image->populateFromDatabaseRow($row);
							try {
								$imageThumbnailUrl = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_TINY, true);
								$imageDimensions = $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_TINY);
								$GLOBALS['ProductImage'] = sprintf('<img src="%1$s" width="%2$d" height="%3$d" />', isc_html_escape($imageThumbnailUrl), $imageDimensions[0], $imageDimensions[1]);
							} catch (Exception $exception) {
								// source image is not readable, show the no image placeholder
								$GLOBALS['ProductImage'] = sprintf("<div class='NoThumb'>%s<br />%s<br />%s</div>", GetLang('NoImage1'), GetLang('NoImage2'), GetLang('NoImage3'));
							}
						} else {
							$GLOBALS['ProductImage'] = sprintf("<div class='NoThumb'>%s<br />%s<br />%s</div>", GetLang('NoImage1'), GetLang('NoImage2'), GetLang('NoImage3'));
						}
					}
					else {
						// Use JavaScript to hide the thumbnail field
						$GLOBALS['HideThumbnailField'] = "1";
					}

					$GLOBALS['Price'] = FormatPrice($row['prodcalculatedprice']);
					$GLOBALS['StockExpand'] = "&nbsp;";
					$GLOBALS['LowStockStyle'] = "";

					if ($row['prodinvtrack'] == 0) {
						$GLOBALS['StockInfo'] = GetLang('NA');
					} else if($row['prodinvtrack'] > 0) {

						$GLOBALS['StockExpand'] = sprintf("<a href=\"#\" onclick=\"ShowStock('%d', '%d', '%d'); return false;\"><img id=\"expand%d\" src=\"images/plus.gif\" align=\"left\"  class=\"ExpandLink\" width=\"19\" height=\"16\" title=\"%s\" border=\"0\"></a>", $row['productid'], $row['prodinvtrack'], $row['prodvariationid'], $row['productid'], GetLang('ClickToViewStock'));

						$percent = 0;
						if($row['prodlowinv'] > 0) {
							$percent = ceil(($row['currentinv'] / ($row['prodlowinv'] * 2)) * 100);
						} elseif ($row['currentinv'] > 0) {
							$percent = 100;
						}

						if($percent > 100) {
							$percent = 100;
						}

						if($percent > 75) {
							$stockClass = 'InStock';
							$orderMore = GetLang('SNo');
						}
						else if($percent > 50) {
							$stockClass = 'StockWarning';
							$orderMore = GetLang('Soon');
						}
						else {
							$stockClass = 'LowStock';
							$orderMore = GetLang('SYes');
						}
						$width = ceil(($percent/100)*72);

						$stockInfo = sprintf(GetLang('CurrentStockLevel').': %s<br />'.GetLang('LowStockLevel1').': %s<br />'.GetLang('OrderMore').': '.$orderMore, $row['currentinv'], $row['prodlowinv'], $orderMore);

						$GLOBALS['StockInfo'] = sprintf("<div class=\"StockLevelIndicator\" onmouseover=\"ShowQuickHelp(this, '%s', '%s')\" onmouseout=\"HideQuickHelp(this)\"><span class=\"%s\" style=\"width: %spx\"></span></div>", GetLang('StockLevel'), $stockInfo, $stockClass, $width);
					}

					// If they have permission to edit products, they can change
					// the visibility status of a product by clicking on the icon

					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {
						if ($row['prodvisible'] == 1) {
							$GLOBALS['Visible'] = sprintf("<a title='%s' href='index.php?ToDo=editProductVisibility&amp;prodId=%d&amp;visible=0' onclick=\"quickToggle(this, 'visible'); return false;\"><img border='0' src='images/tick.gif' alt='tick'></a>", GetLang('ClickToHide'), $row['productid']);
						} else {
							$GLOBALS['Visible'] = sprintf("<a title='%s' href='index.php?ToDo=editProductVisibility&amp;prodId=%d&amp;visible=1' onclick=\"quickToggle(this, 'visible'); return false;\"><img border='0' src='images/cross.gif' alt='cross'></a>", GetLang('ClickToShow'), $row['productid']);
						}
					} else {
						if ($row['prodvisible'] == 1) {
							$GLOBALS['Visible'] = '<img border="0" src="images/tick.gif" alt="tick">';
						} else {
							$GLOBALS['Visible'] = '<img border="0" src="images/cross.gif" alt="cross">';
						}
					}

					// If they have permission to edit products, they can change
					// the featured status of a product by clicking on the icon

					if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
						$featuredColumn = 'prodvendorfeatured';
					}
					else {
						$featuredColumn = 'prodfeatured';
					}

					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {
						if ($row[$featuredColumn] == 1) {
							$GLOBALS['Featured'] = sprintf("<a title='%s' href='index.php?ToDo=editProductFeatured&amp;prodId=%d&amp;featured=0' onclick=\"quickToggle(this, 'featured'); return false;\"><img border='0' src='images/tick.gif' alt='tick'></a>", GetLang('ClickToHide'), $row['productid']);
						} else {
							$GLOBALS['Featured'] = sprintf("<a title='%s' href='index.php?ToDo=editProductFeatured&amp;prodId=%d&amp;featured=1' onclick=\"quickToggle(this, 'featured'); return false;\"><img border='0' src='images/cross.gif' alt='cross'></a>", GetLang('ClickToShow'), $row['productid']);
						}
					} else {
						if ($row[$featuredColumn] == 1) {
							$GLOBALS['Featured'] = '<img border="0" src="images/tick.gif" alt="tick">';
						} else {
							$GLOBALS['Featured'] = '<img border="0" src="images/cross.gif" alt="cross">';
						}
					}

					// Workout the edit link -- do they have permission to do so?
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {
						$GLOBALS['EditProductLink'] = sprintf("<a title='%s' class='Action' href='index.php?ToDo=editProduct&amp;productId=%d'>%s</a>", GetLang('ProductEdit'), $row['productid'], GetLang('Edit'));
					} else {
						$GLOBALS['EditProductLink'] = sprintf("<a class='Action' disabled>%s</a>", GetLang('Edit'));
					}


					$GLOBALS['CopyProductLink'] = "<a title='".GetLang('ProductCopy')."' class='Action' href='index.php?ToDo=copyProduct&amp;productId=".$row['productid']."'>".GetLang('Copy')."</a>";

					$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("product.manage.row");
					$GLOBALS['ProductGrid'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
				}

			}
			if($GLOBALS['ProductGrid'] == '') {
				if(isset($_REQUEST['letter'])) {
					$GLOBALS['ProductGrid'] = sprintf('<tr>
						<td colspan="11" style="padding:10px"><em>%s</em></td>
					</tr>', sprintf(GetLang('LetterSortNoResults'), isc_strtoupper($_REQUEST['letter'])));
				}
			}
			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.manage.grid");
			return $GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate(true);
		}

		public function ManageProducts($MsgDesc = "", $MsgStatus = "")
		{
			$GLOBALS['HideClearResults'] = "none";
			$catList = "";
			$numProducts = 0;

			// Fetch any results, place them in the data grid
			$GLOBALS['ProductDataGrid'] = $this->ManageProductsGrid($numProducts);

			// Was this an ajax based sort? Return the table now
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
				echo $GLOBALS['ProductDataGrid'];
				return;
			}

			if(isset($this->_customSearch['searchname'])) {
				$GLOBALS['ViewName'] = $this->_customSearch['searchname'];
			}
			else {
				$GLOBALS['ViewName'] = GetLang('AllProducts');
				$GLOBALS['HideDeleteViewLink'] = "none";
			}

			$num_custom_searches = 0;

			if (!isset($_REQUEST['searchId'])) {
				$_REQUEST['searchId'] = 0;
			}

			// Get the custom search as option fields
			$GLOBALS['CustomSearchOptions'] = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->GetSearchesAsOptions(@$_REQUEST['searchId'], $num_custom_searches, "AllProducts", "viewProducts", "customProductSearch");

			if(isset($_REQUEST['searchQuery'])) {
				$GLOBALS['HideClearResults'] = "";
			}

			if (!isset($_REQUEST['searchId']) || (int) $_REQUEST['searchId'] <= 0) {
				$GLOBALS['HideDeleteCustomSearch'] = "none";
			} else {
				$GLOBALS['CustomSearchId'] = (int)$_REQUEST['searchId'];
			}

			// Do we need to disable the delete button?
			if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Delete_Products) || $numProducts == 0) {
				$GLOBALS['DisableDelete'] = "DISABLED";
			}

			// Do we need to disable the expory button?
			if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Export_Products) || $numProducts == 0) {
				$GLOBALS['DisableExport'] = "DISABLED";
			}

			$GLOBALS['ProductIntro'] = GetLang('ManageProductsIntro');

			if($numProducts > 0) {
				if($MsgDesc == "" && (isset($_REQUEST['searchQuery']) || (isset($_GET['searchId']) && $_GET['searchId'] > 0))) {
					if($numProducts == 1) {
						$MsgDesc = GetLang('ProductSearchResultsBelow1');
					}
					else {
						$MsgDesc = sprintf(GetLang('ProductSearchResultsBelowX'), $numProducts);
					}

					$MsgStatus = MSG_SUCCESS;
				}
			}
			else {
				$GLOBALS['DisplayGrid'] = "none";
				if(count($_GET) > 1) {
					if($MsgDesc == "") {
						$GLOBALS['Message'] = MessageBox(GetLang('NoProductResults'), MSG_ERROR);
					}
				}
				else {
					// No actual custoemrs
					$GLOBALS['DisplaySearch'] = "none";
					$GLOBALS['Message'] = MessageBox(GetLang('NoProducts'), MSG_SUCCESS);
				}
			}

			if(!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS[base64_decode('SGlkZUV4cG9ydA==')] = "none";
			}

			if(!gzte11(ISC_LARGEPRINT)) {
				$GLOBALS[base64_decode("SGlkZUJ1bGtFeHBvcnRCdXR0b24=")] = "none";
			}

			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			$flashMessages = GetFlashMessages();
			if(is_array($flashMessages) || !empty($flashMessages)) {
				if(!isset($GLOBALS['Message'])) {
					$GLOBALS['Message'] = '';
				}
				foreach($flashMessages as $flashMessage) {
					$GLOBALS['Message'] .= MessageBox($flashMessage['message'], $flashMessage['type']);
				}
			}

			// Do we have permission to bulk edit products?
			if(!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {
				$GLOBALS['DisableBulkEdit'] = "DISABLED='DISABLED'";
			}

			$GLOBALS['ExportAction'] = "index.php?ToDo=startExport&t=products";
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
						$params[$arr[0]] = $arr[1];
					}
				}

				if (count($params)) {
					$GLOBALS['ExportAction'] .= "&" . http_build_query($params);
				}
			}

			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.manage");
			$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
		}

		public function _GetChildCats($parent=0)
		{
			static $called;
			if($called == false) {
				$this->tree = new Tree();
				$query = sprintf("SELECT * FROM [|PREFIX|]categories");
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$this->tree->nodesByPid[(int) $row['catparentid']][] = (int) $row['categoryid'];
				}
				$called = true;
			}

			$children = array();

			if(!@is_array($this->tree->nodesByPid[$parent])) {
				return $children;
			}

			foreach($this->tree->nodesByPid[$parent] as $categoryid) {
				$children[] = $categoryid;
				// Fetch nested children
				if(@is_array($this->tree->nodesByPid[$categoryid])) {
					$children = array_merge($children, $this->_GetChildCats($categoryid));
				}
			}

			return $children;
		}

		public function _GetProductList($Start, $SortField, $SortOrder, &$NumProducts, $fields='', $AddLimit=true, $exportPrimaryImage=false)
		{
			// Return an array containing details about products.
			// Takes into account search and advanced search values too.

			if($fields == '') {
				$fields = "p.productid, p.prodname, p.prodvariationid, p.prodprice, prodinvtrack, p.prodcode, p.proddesc, IF(prodinvtrack = 0, 0, prodcurrentinv) AS currentinv, prodvisible, prodlowinv, prodcalculatedprice, p.prodfeatured, p.prodvendorfeatured, p.prodistaxable, t.*";
			}

			$joinQuery = '';
			$queryWhere = '';

			$searchData = $this->BuildWhereFromVars($_REQUEST);
			$queryWhere = $searchData['query'];
			$joinQuery = $searchData['join'];
			$categorySearch = $searchData['categorySearch'];

			// Only fetch products which belong to the current vendor
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$queryWhere .= "prodvendorid = '" . $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() . "' AND ";
			}

			if($queryWhere) {
				$queryWhere = "WHERE " . $queryWhere . " 1=1";
			}

			// Fetch the number of results
			if ($categorySearch) {
				$countQuery = "
					SELECT
						COUNT(DISTINCT p.productid)
					FROM
						[|PREFIX|]categoryassociations ca
						INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
						" . $joinQuery . "
					" . $queryWhere;
			}
			else {
				$countQuery = "
					SELECT
						COUNT(p.productid)
					FROM
						[|PREFIX|]products p
						" . $joinQuery . "
					" . $queryWhere;
			}

			$result = $GLOBALS['ISC_CLASS_DB']->Query($countQuery);
			$NumProducts = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

			// Construct the product query
			$limit = "";
			if($AddLimit) {
				$limit = $GLOBALS["ISC_CLASS_DB"]->AddLimit($Start, ISC_PRODUCTS_PER_PAGE);
			}

			if ($categorySearch) {
				$query = "
					SELECT
						" . $fields . "
					FROM
						(
							SELECT
								DISTINCT ca.productid
							FROM
								[|PREFIX|]categoryassociations ca
								INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid "
								. $joinQuery
								. $queryWhere . "
							ORDER BY
								" . $SortField . " " . $SortOrder . $limit . "
						) AS ca
						INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
						LEFT JOIN [|PREFIX|]product_images t ON (t.imageisthumb=1 and t.imageprodid=p.productid)
					";

				// Load the thumbnail
				if($exportPrimaryImage == true) {
					$query .= " LEFT JOIN [|PREFIX|]product_images pi ON (pi.imageisthumb=0 AND pi.imageprodid=p.productid AND pi.imagesort=0)";
				}
			}
			else {

				// Load the thumbnail
				if($exportPrimaryImage == true) {
					$joinQuery .= " LEFT JOIN [|PREFIX|]product_images pi ON (pi.imageisthumb=0 AND pi.imageprodid=p.productid AND pi.imagesort=0)";
				}

				$query = "
					SELECT
						" . $fields . "
					FROM
						[|PREFIX|]products p
						LEFT JOIN [|PREFIX|]product_images t ON (t.imageisthumb=1 and t.imageprodid=p.productid) "
						. $joinQuery
						. $queryWhere . "
					ORDER BY "
						. $SortField . " " . $SortOrder . $limit;

			}

			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			return $result;
		}

		public function BuildWhereFromVars($array)
		{
			$queryWhere = "";
			$joinQuery = "";

			$categorySearch = false;

			// Are we selecting a specific product?
			if(isset($array['productId']) && $array['productId'] != '') {
				$queryWhere .= " p.productid = '" . $array['productId'] . "' AND ";
				// dont need to build a where if only one product searched
				return array("query" => $queryWhere, "join" => $joinQuery, "categorySearch"=>$categorySearch);
			}

			// If we're searching by category, we need to completely
			// restructure the search query - so do that first
			$categoryIds = array();
			if(isset($array['category']) && is_array($array['category'])) {
				foreach($array['category'] as $categoryId) {
					// All categories were selected, so don't continue
					if($categoryId == 0) {
						$categorySearch = false;
						break;
					}

					$categoryIds[] = (int)$categoryId;

					// If searching sub categories automatically, fetch & tack them on
					if(isset($array['subCats']) && $array['subCats'] == 1) {
						$categoryIds = array_merge($categoryIds, $this->_GetChildCats($categoryId));
					}
				}

				$categoryIds = array_unique($categoryIds);
				if(!empty($categoryIds)) {
					$categorySearch = true;
				}
			}

			if($categorySearch == true) {
				$queryWhere .= "ca.categoryid IN (" . implode(',', $categoryIds) . ") AND ";
			}

			if(isset($array['searchQuery']) && $array['searchQuery'] != "") {
				// Perform a full text based search on the products search table
				$search_query = $array['searchQuery'];

				$fulltext_fields = array("ps.prodname", "ps.prodcode");
				$queryWhere .= "(" . $GLOBALS["ISC_CLASS_DB"]->FullText($fulltext_fields, $search_query, true);
				$queryWhere .= "OR ps.prodname like '%" . $GLOBALS['ISC_CLASS_DB']->Quote($search_query) . "%' ";
				$queryWhere .= "OR ps.prodcode = '" . $GLOBALS['ISC_CLASS_DB']->Quote($search_query) . "' ";

				if (isId($search_query)) {
					$queryWhere .= "OR p.productid='" . (int)$search_query . "'";
				}

				$queryWhere .= ") AND ";

				// Add the join for the fulltext column
				$joinQuery .= " INNER JOIN [|PREFIX|]product_search ps ON p.productid=ps.productid ";
			}

			if(isset($array['letter']) && $array['letter'] != '') {
				$letter = chr(ord($array['letter']));
				if($array['letter'] == '0-9') {
					$queryWhere .= " p.prodname NOT REGEXP('^[a-zA-Z]') AND ";
				}
				else if(isc_strlen($letter) == 1) {
					$queryWhere .= " p.prodname LIKE '".$GLOBALS['ISC_CLASS_DB']->Quote($letter)."%' AND ";
				}
			}

			if(isset($array['soldFrom']) && isset($array['soldTo']) && $array['soldFrom'] != "" && $array['soldTo'] != "") {
				$sold_from = (int)$array['soldFrom'];
				$sold_to = (int)$array['soldTo'];
				$queryWhere .= sprintf("(prodnumsold >= '%d' and prodnumsold <= '%d') and ", $sold_from, $sold_to);
			}

			else if(isset($array['soldFrom']) && $array['soldFrom'] != "") {
				$sold_from = (int)$array['soldFrom'];
				$queryWhere .= sprintf("prodnumsold >= '%d' and ", $sold_from);
			}
			else if(isset($array['soldTo']) && $array['soldTo'] != "") {
				$sold_to = (int)$array['soldTo'];
				$queryWhere .= sprintf("prodnumsold <= '%d' and ", $sold_to);
			}

			if(isset($array['priceFrom']) && $array['priceFrom'] != "" && isset($array['priceTo']) && $array['priceTo'] != "") {
				$price_from = (int)$array['priceFrom'];
				$price_to = (int)$array['priceTo'];
				$queryWhere .= sprintf(" prodcalculatedprice >= '%s' and prodcalculatedprice <= '%s' and ", $price_from, $price_to);
			}
			else if(isset($array['priceFrom']) && $array['priceFrom'] != "") {
				$price_from = (int)$array['priceFrom'];
				$queryWhere .= sprintf(" prodcalculatedprice >= '%s' and ", $price_from);
			}
			else if(isset($array['priceTo']) && $array['priceTo'] != "") {
				$price_to = (int)$array['priceTo'];
				$queryWhere .= sprintf(" prodcalculatedprice <= '%s' and ", $price_to);
			}

			if(isset($array['inventoryFrom']) && $array['inventoryFrom'] != "" && isset($array['inventoryTo']) && $array['inventoryTo'] != "") {
				$inventory_from =(int)$array['inventoryFrom'];
				$inventory_to = (int)$array['inventoryTo'];
				$queryWhere .= sprintf("prodcurrentinv >= '%s' and prodcurrentinv <= '%s' and ", $inventory_from, $inventory_to);
			}
			else if(isset($array['inventoryFrom']) && $array['inventoryFrom'] != "") {
				$inventory_from =(int) $array['inventoryFrom'];
				$queryWhere .= sprintf("prodcurrentinv >= '%s' and ", $inventory_from);
			}
			else if(isset($array['inventoryTo']) && $array['inventoryTo'] != "") {
				$inventory_to = (int)$array['inventoryTo'];
				$queryWhere .= sprintf("prodcurrentinv <= '%s' and ", $inventory_to);
			}

			if (isset($array['inventoryLow']) && $array['inventoryLow'] != 0) {
				$lowVarInvProdIds = array();
				$inventoryLowVarQuery = "SELECT DISTINCT(vcproductid) FROM [|PREFIX|]product_variation_combinations WHERE vcstock<=vclowstock AND vclowstock > 0";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($inventoryLowVarQuery);
				while ($lowVarInventory = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$lowVarInvProdIds[]=$lowVarInventory['vcproductid'];
				}
				$queryWhere .= "(prodcurrentinv <= prodlowinv AND prodlowinv > 0 AND prodinvtrack=1) OR ( prodinvtrack=2 AND p.productid in ('".implode('\',\'', $lowVarInvProdIds)."')) AND ";
			}

			if(isset($array['brand']) && $array['brand'] != "") {
				$brand = (int)$array['brand'];
				$queryWhere .= sprintf("prodbrandid = '%d' AND ", $brand);
			}

			// Product visibility
			if(isset($array['visibility'])) {
				if($array['visibility'] == 1) {
					$queryWhere .= "prodvisible=1 AND ";
				}
				else if($array['visibility'] === '0') {
					$queryWhere .= "prodvisible=0 AND ";
				}
			}

			// Featured products?
			if(isset($array['featured'])) {
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$featuredColumn = 'prodvendorfeatured';
				}
				else {
					$featuredColumn = 'prodfeatured';
				}

				if($array['featured'] == 1) {
					$queryWhere .= $featuredColumn."=1 AND ";
				}
				else if($_REQUEST['featured'] === '0') {
					$queryWhere .= $featuredColumn."=0 AND ";
				}
			}

			// Free shipping
			if(isset($_REQUEST['freeShipping'])) {
				if($_REQUEST['freeShipping'] == 1) {
					$queryWhere .= "prodfreeshipping=1 AND ";
				}
				else if($_REQUEST['freeShipping'] === '0') {
					$queryWhere .= "prodfreeshipping=0 AND ";
				}
			}

			return array("query" => $queryWhere, "join" => $joinQuery, "categorySearch" => $categorySearch);
		}

		/**
		* Sets up $GLOBALS with product image related data for use with product.form templates
		*
		* @param array $productImages An array of ISC_PRODUCT_IMAGE instances
		*/
		public function setupProductImageGlobals ($productImages)
		{
			// swfupload support
			$GLOBALS['maxUploadSize'] = ISC_UPLOADHANDLER::$maxUploadSize;
			$GLOBALS['sessionid'] = session_id();
			$_SESSION['STORESUITE_CP_TOKEN'] = $_COOKIE['STORESUITE_CP_TOKEN'];

			// file extensions that should be accepted as images
			$extensions = '*.' . implode(';*.', ISC_IMAGE_LIBRARY_FACTORY::getSupportedImageExtensions());
			$GLOBALS['productImage_swfUploadFileTypes_js'] = isc_json_encode($extensions);

			// create a html template for use in javascript when adding product image rows and store it as a javascript string
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('product.form.images.row');
			$GLOBALS['productImage_thumbnailWidth'] = ISC_PRODUCT_IMAGE::getSizeWidth(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL);
			$GLOBALS['productImage_thumbnailHeight'] = ISC_PRODUCT_IMAGE::getSizeHeight(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL);
			$GLOBALS['productImage_newRowTemplate_js'] = isc_json_encode($GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true));

			// generate statements to initialise new productimages as javascript objects
			$GLOBALS['productImage_javascriptInitialiseCode'] = '';
			foreach ($productImages as $productImage) {
				/** @var $productImage ISC_PRODUCT_IMAGE */

				$baseThumbnail = 'false';
				if ($productImage->getIsThumbnail()) {
					$baseThumbnail = 'true';
				}

				try {
					$preview = $productImage->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true);
					$zoom = $productImage->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true);
				} catch (Exception $Exception) {
					$preview = false;
					$zoom = false;
				}

				$GLOBALS['productImage_javascriptInitialiseCode'] .= sprintf(
					'new ProductImages.Image({id:%1$d,product:%8$d,preview:%2$s,zoom:%3$s,description:%4$s,baseThumbnail:%5$s,sort:%7$d,hash:%9$s});',
					/*1*/ $productImage->getProductImageId(),
					/*2*/ isc_json_encode($preview),
					/*3*/ isc_json_encode($zoom),
					/*4*/ isc_json_encode($productImage->getDescription()),
					/*5*/ $baseThumbnail,
					/*6*/ null,
					/*7*/ $productImage->getSort(),
					/*8*/ $productImage->getProductId(),
					/*9*/ isc_json_encode($productImage->getProductHash())
				);
			}

			// done setting up the product images template, render it and put it into the main template
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('product.form.images');
			$GLOBALS['productImagesList'] = $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
		}

		public function AddProductStep2()
		{

			if($message = strtokenize($_REQUEST, '#')) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError(GetLang(B('UmVhY2hlZFByb2R1Y3RMaW1pdA==')), $message, MSG_ERROR);
				exit;
			}

			// Get the information from the form and add it to the database
			$arrData = array();
			$arrCustomFields = array();
			$arrVariations = array();
			$err = "";

			$this->_GetProductData(0, $arrData);

			$downloadError = '';
			if (isset($_FILES['newdownload']) && isset($_FILES['newdownload']['tmp_name']) && $_FILES['newdownload']['tmp_name'] != '') {
				if (!$this->SaveProductDownload($downloadError)) {
					$this->AddProductStep1($downloadError, MSG_ERROR);
					return;
				}
			}

			// Does a product with the same name already exist?
			$query = "SELECT productid FROM [|PREFIX|]products WHERE prodname='".$GLOBALS['ISC_CLASS_DB']->Quote($arrData['prodname'])."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$existingProduct = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if($existingProduct['productid']) {
				$this->AddProductStep1(GetLang('ProductWithSameNameExists'), MSG_ERROR, true);
				return;
			}

			// Validate out discount rules
			$discount = $this->GetDiscountRulesData(0, true);
			if (!empty($discount) && !$this->ValidateDiscountRulesData($error)) {
				$GLOBALS['CurrentTab'] = 7;
				$this->AddProductStep1($error, MSG_ERROR, true);
				return;
			}

			$this->_GetProductFieldData(0, $arrProductFields);
			$productFieldsError = $this->_ValidateProductFields($arrProductFields);
			if($productFieldsError != '') {
				$this->AddProductStep1($productFieldsError, MSG_ERROR);
				return;
			}

			$this->_GetCustomFieldData(0, $arrCustomFields);
			$this->_GetVariationData(0, $arrVariations);

			// Commit the values to the database
			if ($this->_CommitProduct(0, $arrData, $arrVariations, $arrCustomFields, $discount, $err, $arrProductFields)) {

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($GLOBALS['NewProductId'], $arrData['prodname']);

				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					// Save the words to the product_words table for search spelling suggestions
					manageSuggestedWordDatabase("product", $GLOBALS['NewProductId'], $arrData['prodname']);
					if(isset($_POST['addanother'])) {
						FlashMessage(GetLang('ProductAddedSuccessfully'), MSG_SUCCESS);
						header("Location: index.php?ToDo=addProduct");
						exit;
					}
					else {
						$redirectUrl = 'index.php?ToDo=viewProducts';
						// Mark the design step as complete
						if(GetClass('ISC_ADMIN_ENGINE')->MarkGettingStartedComplete('products')) {
							$redirectUrl = 'index.php';
						}

						FlashMessage(GetLang('ProductAddedSuccessfully'), MSG_SUCCESS, $redirectUrl);
						exit;
					}
				} else {
					FlashMessage(GetLang('ProductAddedSuccessfully'), MSG_SUCCESS);
					header("Location: index.php");
					exit;
				}
			} else {
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					FlashMessage(sprintf(GetLang('ErrProductNotAdded'), $err), MSG_ERROR);
					header("Location: index.php?ToDo=addProduct");
					exit;
				} else {
					FlashMessage(sprintf(GetLang('ErrProductNotAdded'), $err), MSG_ERROR);
					header("Location: index.php");
					exit;
				}
			}
		}

		public function AddProductStep1($MsgDesc = "", $MsgStatus = "", $PreservePost=false)
		{
			if($message = strtokenize($_REQUEST, '#')) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError(GetLang(B('UmVhY2hlZFByb2R1Y3RMaW1pdA==')), $message, MSG_ERROR);
				exit;
			}



			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			} else {
				$flashMessages = GetFlashMessages();
				if(is_array($flashMessages)) {
					$GLOBALS['Message'] = '';
					foreach($flashMessages as $flashMessage) {
						$GLOBALS['Message'] .= MessageBox($flashMessage['message'], $flashMessage['type']);
					}
				}
			}

			// Get the getting started box if we need to
			$GLOBALS['GettingStartedStep'] = '';
			if(empty($GLOBALS['Message']) && (isset($_GET['wizard']) && $_GET['wizard']==1) &&  !in_array('products', GetConfig('GettingStartedCompleted')) && !GetConfig('DisableGettingStarted')) {
				$GLOBALS['GettingStartedTitle'] = GetLang('WizardAddProducts');
				$GLOBALS['GettingStartedContent'] = GetLang('WizardAddProductsDesc');
				$GLOBALS['GettingStartedStep'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('GettingStartedModal');
			}

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}

			// delete any uploaded images which are not attached to a product and older than 24 hours
			ISC_PRODUCT_IMAGE::deleteOrphanedProductImages();

			// Delete any temporary combination records older than 24h
			$this->DeleteTemporaryCombinations();

			// Delete any uploaded product downloads which are not attached to a product and older than 24h
			$query = sprintf("select downloadid, downfile from [|PREFIX|]product_downloads where downdateadded<'%d' and productid=0", time()-86400);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$dlids = array();
			while($download = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				@unlink(APP_ROOT."../".GetConfig('DownloadDirectory')."/".$download['downfile']);
				$dlids[] = $download['downloadid'];
			}
			if(count($dlids) > 0) {
				$query = sprintf("delete from [|PREFIX|]product_downloads where downloadid in (%s)", implode(",", $dlids));
				$GLOBALS['ISC_CLASS_DB']->Query($query);
			}

			$GLOBALS['ServerFiles'] = $this->_GetImportFilesOptions();

			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');

			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$GLOBALS['HideStoreFeatured'] = 'display: none';
			}
			else if(!gzte11(ISC_HUGEPRINT)) {
				$GLOBALS['HideVendorFeatured'] = 'display: none';
			}


			// Set the global variables for the select boxes
			$from_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), isc_date("d"), isc_date("Y"));
			$to_stamp = isc_gmmktime(0, 0, 0, isc_date("m")+1, isc_date("d"), isc_date("Y"));

			$from_day = isc_date("d", $from_stamp);
			$from_month = isc_date("m", $from_stamp);
			$from_year = isc_date("Y", $from_stamp);

			$to_day = isc_date("d", $to_stamp);
			$to_month = isc_date("m", $to_stamp);
			$to_year = isc_date("Y", $to_stamp);

			$GLOBALS['OverviewFromDays'] = $this->_GetDayOptions($from_day);
			$GLOBALS['OverviewFromMonths'] = $this->_GetMonthOptions($from_month);
			$GLOBALS['OverviewFromYears'] = $this->_GetYearOptions($from_year);

			$GLOBALS['OverviewToDays'] = $this->_GetDayOptions($to_day);
			$GLOBALS['OverviewToMonths'] = $this->_GetMonthOptions($to_month);
			$GLOBALS['OverviewToYears'] = $this->_GetYearOptions($to_year);

			$productImages = array();

			if($PreservePost == true) {
				$this->_GetProductData(0, $arrData);
				$this->_GetCustomFieldData(0, $arrCustomFields);

				$productImages = $arrData['product_images'];

				$GLOBALS["ProdType_" . $arrData['prodtype']] = 'checked="checked"';
				$GLOBALS['ProdType'] = $arrData['prodtype'] - 1;
				$GLOBALS['ProdCode'] = isc_html_escape($arrData['prodcode']);

				$GLOBALS['ProdName'] = isc_html_escape($arrData['prodname']);

				$visibleCategories = array();
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					if($vendorData['vendoraccesscats']) {
						$visibleCategories = explode(',', $vendorData['vendoraccesscats']);
					}
				}

				$GLOBALS['CategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions($arrData['prodcats'], "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false, '', $visibleCategories);
				$GLOBALS['RelatedCategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions(0, "<option %s value='%d'>%s</option>", "selected=\"selected\"", "- ", false);

				$wysiwygOptions = array(
					'id'		=> 'wysiwyg',
					'width'		=> '100%',
					'height'	=> '500px',
					'value'		=> $arrData['proddesc']
				);
				$GLOBALS['WYSIWYG'] = GetClass('ISC_ADMIN_EDITOR')->GetWysiwygEditor($wysiwygOptions);

				$GLOBALS['ProdSearchKeywords'] = $arrData['prodsearchkeywords'];
				$GLOBALS['ProdAvailability'] = $arrData['prodavailability'];
				$GLOBALS['ProdPrice'] = number_format($arrData['prodprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");

				if (CFloat($arrData['prodcostprice']) > 0) {
					$GLOBALS['ProdCostPrice'] = number_format($arrData['prodcostprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodretailprice']) > 0) {
					$GLOBALS['ProdRetailPrice'] = number_format($arrData['prodretailprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodsaleprice']) > 0) {
					$GLOBALS['ProdSalePrice'] = number_format($arrData['prodsaleprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				$GLOBALS['ProdSortOrder'] = $arrData['prodsortorder'];

				if ($arrData['prodvisible'] == 1) {
					$GLOBALS['ProdVisible'] = "checked";
				}

				if ($arrData['prodfeatured'] == 1) {
					$GLOBALS['ProdFeatured'] = "checked";
				}

				if($arrData['prodvendorfeatured'] == 1) {
					$GLOBALS['ProdVendorFeatured'] = 'checked="checked"';
				}

				if($arrData['prodistaxable'] == 1) {
					$GLOBALS['ProdIsTaxable'] = "checked";
				}

				if($arrData['prodallowpurchases'] == 1) {
					$GLOBALS['ProdAllowPurchases'] = 'checked="checked"';
				}
				else {
					if($arrData['prodhideprice'] == 1) {
						$GLOBALS['ProdHidePrice'] = 'checked="checked"';
					}
				}

				$GLOBALS['ProdCallForPricing'] = isc_html_escape(@$arrData['prodCallForPricingLabel']);

				$GLOBALS['ProdWarranty'] = $arrData['prodwarranty'];
				$GLOBALS['ProdWeight'] = number_format($arrData['prodweight'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");

				if (CFloat($arrData['prodwidth']) > 0) {
					$GLOBALS['ProdWidth'] = number_format($arrData['prodwidth'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");
				}

				if (CFloat($arrData['prodheight']) > 0) {
					$GLOBALS['ProdHeight'] = number_format($arrData['prodheight'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");
				}

				if (CFloat($arrData['proddepth']) > 0) {
					$GLOBALS['ProdDepth'] = number_format($arrData['proddepth'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");
				}

				if (CFloat($arrData['prodfixedshippingcost']) > 0) {
					$GLOBALS['ProdFixedShippingCost'] = number_format($arrData['prodfixedshippingcost'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if ($arrData['prodfreeshipping'] == 1) {
					$GLOBALS['FreeShipping'] = 'checked="checked"';
				}

				if($arrData['prodrelatedproducts'] == -1) {
					$GLOBALS['IsProdRelatedAuto'] = 'checked="checked"';
				}
				else if(isset($arrData['prodrelated'])) {
					$GLOBALS['RelatedProductOptions'] = "";

					foreach ($arrData['prodrelated'] as $r) {
						$GLOBALS['RelatedProductOptions'] .= sprintf("<option value='%d'>%s</option>", $r[0], $r[1]);
					}
				}

				$GLOBALS['WrappingOptions'] = $this->BuildGiftWrappingSelect(explode(',', $arrData['prodwrapoptions']));
				$GLOBALS['HideGiftWrappingOptions'] = 'display: none';
				if($arrData['prodwrapoptions'] == 0) {
					$GLOBALS['WrappingOptionsDefaultChecked'] = 'checked="checked"';
				}
				else if($arrData['prodwrapoptions'] == -1) {
					$GLOBALS['WrappingOptionsNoneChecked'] = 'checked="checked"';
				}
				else {
					$GLOBALS['HideGiftWrappingOptions'] = '';
					$GLOBALS['WrappingOptionsCustomChecked'] = 'checked="checked"';
				}

				$GLOBALS['CurrentStockLevel'] = $arrData['prodcurrentinv'];
				$GLOBALS['LowStockLevel'] = $arrData['prodlowinv'];
				$GLOBALS["InvTrack_" . $arrData['prodinvtrack']] = 'checked="checked"';

				if ($arrData['prodinvtrack'] == 1) {
					$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(true);";
				} else {
					$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(false);";
				}


				if ($arrData['prodoptionsrequired'] == 1) {
					$GLOBALS['ProdOptionRequired'] = 'checked="checked"';
				}

				if ($arrData['prodtype'] == 1) {
					$GLOBALS['HideProductInventoryOptions'] = "none";
				}

				if (GetConfig('PricesIncludeTax')) {
					$GLOBALS['PriceMsg'] = GetLang('IncTax');
				} else {
					$GLOBALS['PriceMsg'] = GetLang('ExTax');
				}

				$GLOBALS['CustomFields'] = '';
				$GLOBALS['CustomFieldKey'] = 0;

				if (!empty($arrCustomFields)) {
					foreach ($arrCustomFields as $f) {
						$GLOBALS['CustomFieldName'] = isc_html_escape($f['name']);
						$GLOBALS['CustomFieldValue'] = isc_html_escape($f['value']);
						$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));

						if (!$GLOBALS['CustomFieldKey']) {
							$GLOBALS['HideCustomFieldDelete'] = 'none';
						} else {
							$GLOBALS['HideCustomFieldDelete'] = '';
						}

						$GLOBALS['CustomFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

						$GLOBALS['CustomFieldKey']++;
					}
				}

				// Add one more custom field
				$GLOBALS['CustomFieldName'] = '';
				$GLOBALS['CustomFieldValue'] = '';
				$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));

				if (!$GLOBALS['CustomFieldKey']) {
					$GLOBALS['HideCustomFieldDelete'] = 'none';
				} else {
					$GLOBALS['HideCustomFieldDelete'] = '';
				}

				$GLOBALS['CustomFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

				// Get the brands as select options
				$GLOBALS['ISC_CLASS_ADMIN_BRANDS'] = GetClass('ISC_ADMIN_BRANDS');
				$GLOBALS['BrandNameOptions'] = $GLOBALS['ISC_CLASS_ADMIN_BRANDS']->GetBrandsAsOptions($arrData['prodbrandid']);

				// Get a list of all layout files
				$layoutFile = 'product.html';
				if($arrData['prodlayoutfile']) {
					$layoutFile = $arrData['prodlayoutfile'];
				}
				$GLOBALS['LayoutFiles'] = GetCustomLayoutFilesAsOptions("product.html", $layoutFile);

				$GLOBALS['ProdPageTitle'] = $arrData['prodpagetitle'];
				$GLOBALS['ProdMetaKeywords'] = $arrData['prodmetakeywords'];
				$GLOBALS['ProdMetaDesc'] = $arrData['prodmetadesc'];

				if (isset($_REQUEST['productHash'])) {
					// load any previously uploaded images
					$productImages = ISC_PRODUCT_IMAGE::getProductImagesFromDatabase($_REQUEST['productHash'], null, true);
				}
			}
			else {
				$Cats = array();
				$Description = GetLang('TypeProductDescHere');

				$GLOBALS['ProdType'] = 0;
				$GLOBALS["ProdType_1"] = 'checked="checked"';
				$GLOBALS['HideFile'] = "none";

				$visibleCategories = array();
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					if($vendorData['vendoraccesscats']) {
						$visibleCategories = explode(',', $vendorData['vendoraccesscats']);
					}
				}
				$GLOBALS['CategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions($Cats, "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false, '', $visibleCategories);
				$GLOBALS['RelatedCategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions($Cats, "<option %s value='%d'>%s</option>", "selected=\"selected\"", "- ", false);

				$wysiwygOptions = array(
					'id'		=> 'wysiwyg',
					'width'		=> '100%',
					'height'	=> '500px',
					'value'		=> $Description
				);
				$GLOBALS['WYSIWYG'] = GetClass('ISC_ADMIN_EDITOR')->GetWysiwygEditor($wysiwygOptions);

				$GLOBALS['ProdVisible'] = "checked";
				$GLOBALS['ProdSortOrder'] = 0;
				$GLOBALS["InvTrack_0"] = 'checked="checked"';
				$GLOBALS['HideProductInventoryOptions'] = "none";
				$GLOBALS['CurrentStockLevel'] = 0;
				$GLOBALS['LowStockLevel'] = 0;
				$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(false);";
				$GLOBALS['ExistingDownload'] = "false";
				$GLOBALS['IsProdRelatedAuto'] = 'checked="checked"';
				$GLOBALS['ProdIsTaxable'] = "checked";

				$GLOBALS['ProdAllowPurchases'] = 'checked="checked"';
				$GLOBALS['ProdCallForPricingLabel'] = GetLang('ProductCallForPricingDefault');

				// Get the brands as select options
				$GLOBALS['ISC_CLASS_ADMIN_BRANDS'] = GetClass('ISC_ADMIN_BRANDS');
				$GLOBALS['BrandNameOptions'] = $GLOBALS['ISC_CLASS_ADMIN_BRANDS']->GetBrandsAsOptions();

				$GLOBALS['CustomFieldKey'] = 0;
				$GLOBALS['CustomFieldName'] = '';
				$GLOBALS['CustomFieldValue'] = '';
				$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));
				$GLOBALS['HideCustomFieldDelete'] = 'none';
				$GLOBALS['CustomFields'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

				$GLOBALS['WrappingOptions'] = $this->BuildGiftWrappingSelect();
				$GLOBALS['WrappingOptionsDefaultChecked'] = 'checked="checked"';
			}

			$this->setupProductImageGlobals($productImages);

			$GLOBALS['ProductFields'] = $this->_GetProductFieldsLayout(0);

			if(!gzte11(ISC_HUGEPRINT)) {
				$GLOBALS['HideVendorOption'] = 'display: none';
			}
			else {
				$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				if(isset($vendorData['vendorid'])) {
					$GLOBALS['HideVendorSelect'] = 'display: none';
					$GLOBALS['CurrentVendor'] = isc_html_escape($vendorData['vendorname']);
				}
				else {
					$GLOBALS['HideVendorLabel'] = 'display: none';
					if($PreservePost) {
						$GLOBALS['VendorList'] = $this->BuildVendorSelect($_POST['vendor']);
					}
					else {
						$GLOBALS['VendorList'] = $this->BuildVendorSelect();
					}
				}
			}

			// Does this store have any categories?
			if(isset($GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->catsById) && count($GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->catsById) == 0) {
				$GLOBALS['NoCategoriesJS'] = 'true';
			}

			$GLOBALS['FormType'] = "AddingProduct";
			$GLOBALS['FormAction'] = "addProduct2";
			$GLOBALS['Title'] = GetLang('AddProductTitle');
			$GLOBALS['Intro'] = GetLang('AddProductIntro');

			$GLOBALS['ProductWeightHelp'] = sprintf(GetLang('ProductWeightHelp'), GetConfig('WeightMeasurement'));
			$GLOBALS['ProductWidthHelp'] = sprintf(GetLang('ProductWidthHelp'), isc_strtolower(GetConfig('LengthMeasurement')));
			$GLOBALS['ProductHeightHelp'] = sprintf(GetLang('ProductHeightHelp'), isc_strtolower(GetConfig('LengthMeasurement')));
			$GLOBALS['ProductDepthHelp'] = sprintf(GetLang('ProductDepthHelp'), isc_strtolower(GetConfig('LengthMeasurement')));

			$GLOBALS['CurrentTab'] = 0;

			if(GetConfig('PricesIncludeTax')) {
				$GLOBALS['PriceMsg'] = GetLang('IncTax');
			}
			else {
				$GLOBALS['PriceMsg'] = GetLang('ExTax');
			}

			if(!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS['HideInventoryOptions'] = "none";
			}
			else {
				$GLOBALS['HideInventoryOptions'] = '';
			}

			$GLOBALS['ISC_LANG']['MaxUploadSize'] = sprintf(GetLang('MaxUploadSize'), GetMaxUploadSize());
			if(isset($_REQUEST['productHash'])) {
				// Get a list of any downloads associated with this product
				$GLOBALS['DownloadsGrid'] = $this->GetDownloadsGrid(0, $_REQUEST['productHash']);
				if($GLOBALS['DownloadsGrid'] == '') {
					$GLOBALS['DisplayDownloaadGrid'] = "none";
					$GLOBALS['DisplayDownloadUploadGap'] = 'none';
				}
				$GLOBALS['ProductHash'] = $_REQUEST['productHash'];
			}
			else {
				$GLOBALS['DisplayDownloaadGrid'] = "none";
				$GLOBALS['DisplayDownloadUploadGap'] = 'none';
				$GLOBALS['ProductHash'] = md5(time().uniqid(rand(), true));
			}

			// Get a list of all layout files
			$GLOBALS['LayoutFiles'] = GetCustomLayoutFilesAsOptions("product.html");

			// By default we have no variation selected
			$GLOBALS['IsNoVariation'] = 'checked="checked"';
			$GLOBALS['HideVariationList'] = "none";

			// If there are no variations then disable the option to choose one
			$numVariations = 0;
			$GLOBALS['VariationOptions'] = $this->GetVariationsAsOptions($numVariations);

			if($numVariations == 0) {
				$GLOBALS['VariationDisabled'] = "DISABLED";
				$GLOBALS['VariationColor'] = "#CACACA";
			}

			// By default we set variations to NO
			$GLOBALS['IsNoVariation'] = 'checked="checked"';

			// By default we set product options required to YES
			$GLOBALS['OptionsRequired'] = 'checked="checked"';

			// Display the discount rules
			$GLOBALS['DiscountRules'] = $this->GetDiscountRules(0);

			$GLOBALS['EventDateFieldName'] = GetLang('EventDateDefault');

			// Hide if we are not enabled
			if (!GetConfig('BulkDiscountEnabled')) {
				$GLOBALS['HideDiscountRulesWarningBox'] = '';
				$GLOBALS['DiscountRulesWarningText'] = GetLang('DiscountRulesNotEnabledWarning');
				$GLOBALS['DiscountRulesWithWarning'] = 'none';

			// Also hide it if this product has variations
			} else if (isset($arrData['prodvariationid']) && isId($arrData['prodvariationid'])) {
				$GLOBALS['HideDiscountRulesWarningBox'] = '';
				$GLOBALS['DiscountRulesWarningText'] = GetLang('DiscountRulesVariationWarning');
				$GLOBALS['DiscountRulesWithWarning'] = 'none';
			} else {
				$GLOBALS['HideDiscountRulesWarningBox'] = 'none';
				$GLOBALS['DiscountRulesWithWarning'] = '';
			}

			$GLOBALS['DiscountRulesEnabled'] = (int)GetConfig('BulkDiscountEnabled');

			if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Category)) {
				$GLOBALS['HideCategoryCreation'] = 'display: none';
			}

			// Can we show the 'add brand' input box?
			$GLOBALS["HideAddBrandBox"] = "";
			$GLOBALS["BrandNameProdHelp"] = "";
			if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Add_Brands)) {
				$GLOBALS["HideAddBrandBox"] = "display: none";
				$GLOBALS["BrandNameProdHelp"] = GetLang("BrandNameProdNoAddHelp");
			} else {
				$GLOBALS["BrandNameProdHelp"] = GetLang("BrandNameProdHelp");
			}

			//Google website optimizer
			$GLOBALS['GoogleWebsiteOptimizerIntro'] = GetLang('EnableGoogleWebsiteOptimizerAfterSave');
			$GLOBALS['ShowEnableGoogleWebsiteOptimzer'] = 'display:none';
			$GLOBALS['DisableOptimizerCheckbox'] = 'DISABLED=DISABLED';

			$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndAddAnother');
			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("product.form");
			$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
		}

		/**
		* GetVariationsAsOptions
		* Get a list of variations as <OPTION>tags
		*
		* @param Int $NumVariations A reference variable to pass back how many variations there are
		* @param Int $Selected The ID of the variation to select
		* @return String
		*/
		public function GetVariationsAsOptions(&$NumVariations, $Selected=0)
		{
			$queryWhere = '';
			// Only fetch variations which belong to the current vendor
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$queryWhere .= " AND vvendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
			}

			$query = "
				SELECT variationid, vname
				FROM [|PREFIX|]product_variations
				WHERE 1=1
			";
			$query .= $queryWhere;
			$query .= "ORDER BY vname ASC";
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			$NumVariations = $GLOBALS["ISC_CLASS_DB"]->CountResult($result);
			$options = "";

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				if($row['variationid'] == $Selected) {
					$sel = 'selected="selected"';
				}
				else {
					$sel = "";
				}

				$options .= sprintf("<option value='%d' %s>%s</option>", $row['variationid'], $sel, isc_html_escape($row['vname']));
			}

			return $options;
		}

		public function EditProductStep2()
		{
			// Get the information from the form and add it to the database
			$prodId = (int)$_POST['productId'];
			$arrData = array();
			$arrCustomFields = array();
			$arrVariations = array();
			$err = "";
			$this->_GetProductData($prodId, $existingData);
			$this->_GetProductData(0, $arrData);
			$this->_GetCustomFieldData(0, $arrCustomFields);
			$this->_GetVariationData(0, $arrVariations);
			$this->_GetProductFieldData(0, $arrProductFields);

			//validate product fields
			$productFieldsError = $this->_ValidateProductFields($arrProductFields);
			if($productFieldsError != '') {
				$this->EditProductStep1($productFieldsError, MSG_ERROR, true);
				return;
			}

			$discount = $this->GetDiscountRulesData(0, true);

			// Does this user have permission to edit this product?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $existingData['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewProducts');
			}

			$downloadError = '';
			if (isset($_FILES['newdownload']) && isset($_FILES['newdownload']['tmp_name']) && $_FILES['newdownload']['tmp_name'] != '') {
				if (!$this->SaveProductDownload($downloadError)) {
					$this->EditProductStep1($downloadError, MSG_ERROR);
					return;
				}
			}

			// Does a product with the same name already exist?
			$query = "SELECT productid FROM [|PREFIX|]products WHERE prodname='".$GLOBALS['ISC_CLASS_DB']->Quote($arrData['prodname'])."' AND productid!='".$prodId."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$existingProduct = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if($existingProduct['productid']) {
				$this->EditProductStep1(GetLang('ProductWithSameNameExists'), MSG_ERROR, true);
				return;
			}

			// Validate out discount rules
			if (!empty($discount) && !$this->ValidateDiscountRulesData($error)) {
				$_POST['currentTab'] = 7;
				$this->EditProductStep1($error, MSG_ERROR, true);
				return;
			}

			//Validate Google Website Optimizer form
			if(isset($_POST['prodEnableOptimizer'])) {
				$optimizer = getClass('ISC_ADMIN_OPTIMIZER');
				$error = $optimizer -> validateConfigForm();
				if($error!='') {
					$_POST['currentTab'] = 8;
					$this->EditProductStep1($error, MSG_ERROR, true);
					return;
				}
			}

			// Commit the values to the database
			if ($this->_CommitProduct($prodId, $arrData, $arrVariations, $arrCustomFields, $discount, $err, $arrProductFields)) {
				$successMessage = sprintf(GetLang('ProductUpdatedSuccessfully'), isc_html_escape($arrData['prodname']), ProdLink($arrData['prodname']));
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {

					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($prodId, $arrData['prodname']);

					// Save the words to the product_words table for search spelling suggestions
					manageSuggestedWordDatabase("product", $prodId, $arrData['prodname']);

					if(isset($_POST['addanother'])) {
						$_GET['productId'] = $prodId;
						$this->EditProductStep1($successMessage, MSG_SUCCESS);
					}
					else {
						FlashMessage($successMessage, MSG_SUCCESS);
						header("Location: index.php?ToDo=viewProducts");
						exit;
					}
				} else {
					FlashMessage($successMessage, MSG_SUCCESS);
					header("Location: index.php");
					exit;
				}
			} else {
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(sprintf(GetLang('ErrProductNotUpdated'), $err), MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(sprintf(GetLang('ErrProductNotUpdated'), $err), MSG_ERROR);
				}
			}
		}

		public function EditProductStep1($MsgDesc = "", $MsgStatus = "", $PreservePost=false)
		{
			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			// Show the form to edit a product
			$prodId = (int)$_REQUEST['productId'];
			$z = 0;

			$arrData = array();
			$arrCustomFields = array();

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}

			$GLOBALS['ServerFiles'] = $this->_GetImportFilesOptions();

			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');

			// load image manager language file as the lang vars are used by product image management
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('imagemanager');

			// Make sure the product exists
			if (ProductExists($prodId)) {
				$this->_GetProductData($prodId, $arrData);

				// Does this user have permission to edit this product?
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $arrData['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewProducts');
				}

				if($PreservePost == true) {
					$this->_GetProductData(0, $arrData);
					$this->_GetCustomFieldData(0, $arrCustomFields);
					$GLOBALS['ProductFields'] = $this->_GetProductFieldsLayout(0);
				} else {
					$this->_GetCustomFieldData($prodId, $arrCustomFields);
					$GLOBALS['ProductFields'] = $this->_GetProductFieldsLayout($prodId);
				}

				if(isset($_POST['currentTab'])) {
					$GLOBALS['CurrentTab'] = (int)$_POST['currentTab'];
				}
				else {
					$GLOBALS['CurrentTab'] = 0;
				}

				$GLOBALS['FormAction'] = "editProduct2";
				$GLOBALS['ProductId'] = $prodId;
				$GLOBALS['Title'] = GetLang('EditProductTitle');
				$GLOBALS['Intro'] = GetLang('EditProductIntro');
				$GLOBALS["ProdType_" . $arrData['prodtype']] = 'checked="checked"';
				$GLOBALS['ProdType'] = $arrData['prodtype'] - 1;
				$GLOBALS['ProdCode'] = isc_html_escape($arrData['prodcode']);
				$GLOBALS['ProdHash'] = '';

				// set videos data
				$GLOBALS['YouTubeVideos'] = '';
				$videosArray = array();
				if(isset($arrData['product_videos']) && !empty($arrData['product_videos'])) {
					foreach($arrData['product_videos'] as $videoId=>$videoData) {
						$videosArray[] = $videoId;
					}
					$GLOBALS['YouTubeVideos'] = isc_html_escape(implode(',', $videosArray));
				}

				// --- BEGIN PRODUCT IMAGES

				// create a html template for use in javascript when adding product image rows and store it as a javascript string
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('product.form.images.row');
				$GLOBALS['productImage_thumbnailWidth'] = ISC_PRODUCT_IMAGE::getSizeWidth(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL);
				$GLOBALS['productImage_thumbnailHeight'] = ISC_PRODUCT_IMAGE::getSizeHeight(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL);
				$GLOBALS['productImage_newRowTemplate_js'] = isc_json_encode($GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true));
				$GLOBALS['sessionid'] = session_id();
				$_SESSION['STORESUITE_CP_TOKEN'] = $_COOKIE['STORESUITE_CP_TOKEN'];

				// send through the file extensions that should be accepted as images
				$extensions = '*.' . implode(';*.', ISC_IMAGE_LIBRARY_FACTORY::getSupportedImageExtensions());
				$GLOBALS['productImage_swfUploadFileTypes_js'] = isc_json_encode($extensions);

				// generate statements to initialise new productimages as javascript objects
				$GLOBALS['productImage_javascriptInitialiseCode'] = '';
				foreach ($arrData['product_images'] as $productImage) {

					$baseThumbnail = 'false';
					if ($productImage->getIsThumbnail()) {
						$baseThumbnail = 'true';
					}

					try {
						$preview = $productImage->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true);
						$zoom = $productImage->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true);
					} catch (Exception $Exception) {
						$preview = false;
						$zoom = false;
					}

					$GLOBALS['productImage_javascriptInitialiseCode'] .= sprintf(
						'new ProductImages.Image({id:%1$d,product:%8$d,preview:%2$s,zoom:%3$s,description:%4$s,baseThumbnail:%5$s,sort:%7$d});',
						/*1*/ $productImage->getProductImageId(),
						/*2*/ isc_json_encode($preview),
						/*3*/ isc_json_encode($zoom),
						/*4*/ isc_json_encode($productImage->getDescription()),
						/*5*/ $baseThumbnail,
						/*6*/ null,
						/*7*/ $productImage->getSort(),
						/*8*/ $productImage->getProductId()
					);
				}

				// done setting up the product images template, render it and put it into the main template
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate('product.form.images');
				$GLOBALS['productImagesList'] = $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);

				// --- END PRODUCT IMAGES

				$GLOBALS['ProdTags'] = isc_html_escape($arrData['prodtags']);


				$GLOBALS['ProdName'] = isc_html_escape($arrData['prodname']);
				$visibleCategories = array();
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					if($vendorData['vendoraccesscats']) {
						$visibleCategories = explode(',', $vendorData['vendoraccesscats']);
					}
				}

				$GLOBALS['CategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions($arrData['prodcats'], "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false, '', $visibleCategories);
				$GLOBALS['RelatedCategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions(0, "<option %s value='%d'>%s</option>", "selected=\"selected\"", "- ", false);

				$wysiwygOptions = array(
					'id'		=> 'wysiwyg',
					'width'		=> '100%',
					'height'	=> '500px',
					'value'		=> $arrData['proddesc']
				);
				$GLOBALS['WYSIWYG'] = GetClass('ISC_ADMIN_EDITOR')->GetWysiwygEditor($wysiwygOptions);

				$GLOBALS['ProdSearchKeywords'] = isc_html_escape($arrData['prodsearchkeywords']);
				$GLOBALS['ProdAvailability'] = isc_html_escape($arrData['prodavailability']);
				$GLOBALS['ProdPrice'] = number_format($arrData['prodprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");

				if (CFloat($arrData['prodcostprice']) > 0) {
					$GLOBALS['ProdCostPrice'] = number_format($arrData['prodcostprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodretailprice']) > 0) {
					$GLOBALS['ProdRetailPrice'] = number_format($arrData['prodretailprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodsaleprice']) > 0) {
					$GLOBALS['ProdSalePrice'] = number_format($arrData['prodsaleprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				$GLOBALS['ProdSortOrder'] = $arrData['prodsortorder'];

				if ($arrData['prodvisible'] == 1) {
					$GLOBALS['ProdVisible'] = "checked";
				}

				if ($arrData['prodfeatured'] == 1) {
					$GLOBALS['ProdFeatured'] = "checked";
				}

				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$GLOBALS['HideStoreFeatured'] = 'display: none';
				}
				else if(!gzte11(ISC_HUGEPRINT) || !$arrData['prodvendorid']) {
					$GLOBALS['HideVendorFeatured'] = 'display: none';
				}

				if($arrData['prodvendorfeatured'] == 1) {
					$GLOBALS['ProdVendorFeatured'] = 'checked="checked"';
				}

				if($arrData['prodistaxable'] == 1) {
					$GLOBALS['ProdIsTaxable'] = 'checked';
				}

				if($arrData['prodallowpurchases'] == 1) {
					$GLOBALS['ProdAllowPurchases'] = 'checked="checked"';
				}
				else {
					if($arrData['prodhideprice'] == 1) {
						$GLOBALS['ProdHidePrice'] = 'checked="checked"';
					}
					$GLOBALS['ProdCallForPricingLabel'] = isc_html_escape($arrData['prodcallforpricinglabel']);
				}

				$GLOBALS['ProdWarranty'] = $arrData['prodwarranty'];
				$GLOBALS['ProdWeight'] = number_format($arrData['prodweight'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");

				if (CFloat($arrData['prodwidth']) > 0) {
					$GLOBALS['ProdWidth'] = number_format($arrData['prodwidth'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");
				}

				if (CFloat($arrData['prodheight']) > 0) {
					$GLOBALS['ProdHeight'] = number_format($arrData['prodheight'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");
				}

				if (CFloat($arrData['proddepth']) > 0) {
					$GLOBALS['ProdDepth'] = number_format($arrData['proddepth'], GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), "");
				}

				if (CFloat($arrData['prodfixedshippingcost']) > 0) {
					$GLOBALS['ProdFixedShippingCost'] = number_format($arrData['prodfixedshippingcost'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if ($arrData['prodfreeshipping'] == 1) {
					$GLOBALS['FreeShipping'] = 'checked="checked"';
				}

				if($arrData['prodrelatedproducts'] == -1) {
					$GLOBALS['IsProdRelatedAuto'] = 'checked="checked"';
				}
				else if(isset($arrData['prodrelated'])) {
					$GLOBALS['RelatedProductOptions'] = "";

					foreach ($arrData['prodrelated'] as $r) {
						$GLOBALS['RelatedProductOptions'] .= sprintf("<option value='%d'>%s</option>", (int) $r[0], isc_html_escape($r[1]));
					}
				}

				$GLOBALS['CurrentStockLevel'] = $arrData['prodcurrentinv'];
				$GLOBALS['LowStockLevel'] = $arrData['prodlowinv'];
				$GLOBALS["InvTrack_" . $arrData['prodinvtrack']] = 'checked="checked"';

				if ($arrData['prodinvtrack'] == 1) {
					$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(true);";
				} else {
					$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(false);";
				}

				if ($arrData['prodoptionsrequired'] == 1) {
					$GLOBALS['OptionsRequired'] = 'checked="checked"';
				}

				if ($arrData['prodtype'] == 1) {
					$GLOBALS['HideProductInventoryOptions'] = "none";
				}

				$GLOBALS['EnterOptionPrice'] = sprintf(GetLang('EnterOptionPrice'), GetConfig('CurrencyToken'), GetConfig('CurrencyToken'));
				$GLOBALS['EnterOptionWeight'] = sprintf(GetLang('EnterOptionWeight'), GetConfig('WeightMeasurement'));
				$GLOBALS['HideCustomFieldLink'] = "none";

				if (GetConfig('PricesIncludeTax')) {
					$GLOBALS['PriceMsg'] = GetLang('IncTax');
				} else {
					$GLOBALS['PriceMsg'] = GetLang('ExTax');
				}

				$GLOBALS['ProductFields'] = $this->_GetProductFieldsLayout($prodId);

				$GLOBALS['CustomFields'] = '';
				$GLOBALS['CustomFieldKey'] = 0;

				if (!empty($arrCustomFields)) {
					foreach ($arrCustomFields as $f) {
						$GLOBALS['CustomFieldName'] = isc_html_escape($f['name']);
						$GLOBALS['CustomFieldValue'] = isc_html_escape($f['value']);
						$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));

						if (!$GLOBALS['CustomFieldKey']) {
							$GLOBALS['HideCustomFieldDelete'] = 'none';
						} else {
							$GLOBALS['HideCustomFieldDelete'] = '';
						}

						$GLOBALS['CustomFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

						$GLOBALS['CustomFieldKey']++;
					}
				}

				// Add one more custom field
				$GLOBALS['CustomFieldName'] = '';
				$GLOBALS['CustomFieldValue'] = '';
				$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));

				if (!$GLOBALS['CustomFieldKey']) {
					$GLOBALS['HideCustomFieldDelete'] = 'none';
				} else {
					$GLOBALS['HideCustomFieldDelete'] = '';
				}

				$GLOBALS['CustomFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

				$GLOBALS['ProductHash'] = '';

				// Get a list of any downloads associated with this product
				$GLOBALS['DownloadsGrid'] = $this->GetDownloadsGrid($prodId);
				$GLOBALS['ISC_LANG']['MaxUploadSize'] = sprintf(GetLang('MaxUploadSize'), GetMaxUploadSize());
				if($GLOBALS['DownloadsGrid'] == '') {
					$GLOBALS['DisplayDownloaadGrid'] = "none";
				}

				// Get the brands as select options
				$GLOBALS['ISC_CLASS_ADMIN_BRANDS'] = GetClass('ISC_ADMIN_BRANDS');
				$GLOBALS['BrandNameOptions'] = $GLOBALS['ISC_CLASS_ADMIN_BRANDS']->GetBrandsAsOptions($arrData['prodbrandid']);
				$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndContinueEditing');

				// Get a list of all layout files
				$layoutFile = 'product.html';
				if($arrData['prodlayoutfile'] != '') {
					$layoutFile = $arrData['prodlayoutfile'];
				}
				$GLOBALS['LayoutFiles'] = GetCustomLayoutFilesAsOptions("product.html", $layoutFile);

				$GLOBALS['ProdPageTitle'] = isc_html_escape($arrData['prodpagetitle']);
				$GLOBALS['ProdMetaKeywords'] = isc_html_escape($arrData['prodmetakeywords']);
				$GLOBALS['ProdMetaDesc'] = isc_html_escape($arrData['prodmetadesc']);
				$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndContinueEditing');

				if(!gzte11(ISC_MEDIUMPRINT)) {
					$GLOBALS['HideInventoryOptions'] = "none";
				}
				else {
					$GLOBALS['HideInventoryOptions'] = '';
				}

				// Does this product have a variation assigned to it?
				$GLOBALS['ProductVariationExisting'] = $arrData['prodvariationid'];

				if($arrData['prodvariationid'] > 0) {
					$GLOBALS['IsYesVariation'] = 'checked="checked"';
				}
				else {
					$GLOBALS['IsNoVariation'] = 'checked="checked"';
					$GLOBALS['HideVariationList'] = "none";
					$GLOBALS['HideVariationCombinationList'] = "none";
				}

				// If there are no variations then disable the option to choose one
				$numVariations = 0;
				$GLOBALS['VariationOptions'] = $this->GetVariationsAsOptions($numVariations, $arrData['prodvariationid']);

				if($numVariations == 0) {
					$GLOBALS['VariationDisabled'] = "DISABLED";
					$GLOBALS['VariationColor'] = "#CACACA";
					$GLOBALS['IsNoVariation'] = 'checked="checked"';
					$GLOBALS['IsYesVariation'] = "";
					$GLOBALS['HideVariationCombinationList'] = "none";
				}
				else {
					// Load the variation combinations
					if($arrData['prodinvtrack'] == 2) {
						$show_inv_fields = true;
					}
					else {
						$show_inv_fields = false;
					}

					$GLOBALS['VariationCombinationList'] = $this->_LoadVariationCombinationsTable($arrData['prodvariationid'], $show_inv_fields, $arrData['productid']);
				}

				$GLOBALS['WrappingOptions'] = $this->BuildGiftWrappingSelect(explode(',', $arrData['prodwrapoptions']));
				$GLOBALS['HideGiftWrappingOptions'] = 'display: none';
				if($arrData['prodwrapoptions'] == 0) {
					$GLOBALS['WrappingOptionsDefaultChecked'] = 'checked="checked"';
				}
				else if($arrData['prodwrapoptions'] == -1) {
					$GLOBALS['WrappingOptionsNoneChecked'] = 'checked="checked"';
				}
				else {
					$GLOBALS['HideGiftWrappingOptions'] = '';
					$GLOBALS['WrappingOptionsCustomChecked'] = 'checked="checked"';
				}

				if(!gzte11(ISC_HUGEPRINT)) {
					$GLOBALS['HideVendorOption'] = 'display: none';
				}
				else {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					if(isset($vendorData['vendorid'])) {
						$GLOBALS['HideVendorSelect'] = 'display: none';
						$GLOBALS['CurrentVendor'] = isc_html_escape($vendorData['vendorname']);
					}
					else {
						$GLOBALS['HideVendorLabel'] = 'display: none';
						$GLOBALS['VendorList'] = $this->BuildVendorSelect($arrData['prodvendorid']);
					}
				}

				// Display the discount rules
				if ($PreservePost == true) {
					$GLOBALS['DiscountRules'] = $this->GetDiscountRules(0);
				} else {
					$GLOBALS['DiscountRules'] = $this->GetDiscountRules($prodId);
				}

				// Hide if we are not enabled
				if (!GetConfig('BulkDiscountEnabled')) {
					$GLOBALS['HideDiscountRulesWarningBox'] = '';
					$GLOBALS['DiscountRulesWarningText'] = GetLang('DiscountRulesNotEnabledWarning');
					$GLOBALS['DiscountRulesWithWarning'] = 'none';

				// Also hide it if this product has variations
				} else if (isset($arrData['prodvariationid']) && isId($arrData['prodvariationid'])) {
					$GLOBALS['HideDiscountRulesWarningBox'] = '';
					$GLOBALS['DiscountRulesWarningText'] = GetLang('DiscountRulesVariationWarning');
					$GLOBALS['DiscountRulesWithWarning'] = 'none';
				} else {
					$GLOBALS['HideDiscountRulesWarningBox'] = 'none';
					$GLOBALS['DiscountRulesWithWarning'] = '';
				}

				$GLOBALS['DiscountRulesEnabled'] = (int)GetConfig('BulkDiscountEnabled');

				if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Category)) {
					$GLOBALS['HideCategoryCreation'] = 'display: none';
				}

				$GLOBALS['EventDateFieldName'] = $arrData['prodeventdatefieldname'];

				if ($GLOBALS['EventDateFieldName'] == null) {
					$GLOBALS['EventDateFieldName'] = GetLang('EventDateDefault');
				}

				if ($arrData['prodeventdaterequired'] == 1) {
					$GLOBALS['EventDateRequired'] = 'checked="checked"';
					$from_stamp = $arrData['prodeventdatelimitedstartdate'];
					$to_stamp = $arrData['prodeventdatelimitedenddate'];
				} else {
					$from_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), isc_date("d"), isc_date("Y"));
					$to_stamp = isc_gmmktime(0, 0, 0, isc_date("m")+1, isc_date("d"), isc_date("Y"));
				}
				if ($arrData['prodeventdatelimited'] == 1) {
					$GLOBALS['LimitDates'] = 'checked="checked"';
				}

				$GLOBALS['LimitDateOption1'] = '';
				$GLOBALS['LimitDateOption2'] = '';
				$GLOBALS['LimitDateOption3'] = '';

				switch ($arrData['prodeventdatelimitedtype']) {

					case 1 :
						$GLOBALS['LimitDateOption1'] = 'selected="selected"';
					break;
					case 2 :
						$GLOBALS['LimitDateOption2'] = 'selected="selected"';
					break;
					case 3 :
						$GLOBALS['LimitDateOption3'] = 'selected="selected"';
					break;
				}

				// Set the global variables for the select boxes

				$from_day = isc_date("d", $from_stamp);
				$from_month = isc_date("m", $from_stamp);
				$from_year = isc_date("Y", $from_stamp);

				$to_day = isc_date("d", $to_stamp);
				$to_month = isc_date("m", $to_stamp);
				$to_year = isc_date("Y", $to_stamp);

				$GLOBALS['OverviewFromDays'] = $this->_GetDayOptions($from_day);
				$GLOBALS['OverviewFromMonths'] = $this->_GetMonthOptions($from_month);
				$GLOBALS['OverviewFromYears'] = $this->_GetYearOptions($from_year);

				$GLOBALS['OverviewToDays'] = $this->_GetDayOptions($to_day);
				$GLOBALS['OverviewToMonths'] = $this->_GetMonthOptions($to_month);
				$GLOBALS['OverviewToYears'] = $this->_GetYearOptions($to_year);

				$GLOBALS['ProdMYOBAsset'] = isc_html_escape($arrData['prodmyobasset']);
				$GLOBALS['ProdMYOBIncome'] = isc_html_escape($arrData['prodmyobincome']);
				$GLOBALS['ProdMYOBExpense'] = isc_html_escape($arrData['prodmyobexpense']);

				$GLOBALS['ProdPeachtreeGL'] = isc_html_escape($arrData['prodpeachtreegl']);

				$GLOBALS['ProdCondition' . $arrData['prodcondition'] . 'Selected'] = 'selected="selected"';
				if ($arrData['prodshowcondition']) {
					$GLOBALS['ProdShowCondition'] = 'checked="checked"';
				}

				// Can we show the 'add brand' input box?
				$GLOBALS["HideAddBrandBox"] = "";
				$GLOBALS["BrandNameProdHelp"] = "";
				if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Add_Brands)) {
					$GLOBALS["HideAddBrandBox"] = "display: none";
					$GLOBALS["BrandNameProdHelp"] = GetLang("BrandNameProdNoAddHelp");
				} else {
					$GLOBALS["BrandNameProdHelp"] = GetLang("BrandNameProdHelp");
				}


				//Google website optimizer
				$GLOBALS['GoogleWebsiteOptimizerIntro'] = GetLang('ProdGoogleWebsiteOptimizerIntro');

				$GLOBALS['HideOptimizerConfigForm'] = 'display:none;';
				$GLOBALS['CheckEnableOptimizer'] = '';

				$GLOBALS['SkipOpimizerConfirmMsg'] = 'true';
				$enabledOptimizers = GetConfig('OptimizerMethods');
				if(!empty($enabledOptimizers)) {
					foreach ($enabledOptimizers as $id => $date) {
						GetModuleById('optimizer', $optimizerModule, $id);
						if ($optimizerModule->_testPage == 'products' || $optimizerModule->_testPage == 'all') {
							$GLOBALS['SkipOpimizerConfirmMsg'] = 'false';
							break;
						}
					}
				}

				if($arrData['product_enable_optimizer'] == '1') {
					$GLOBALS['HideOptimizerConfigForm'] = '';
					$GLOBALS['CheckEnableOptimizer'] = 'Checked';
				}

				$optimizer = getClass('ISC_ADMIN_OPTIMIZER');
				$GLOBALS['OptimizerConfigForm'] = $optimizer->showPerItemConfigForm('product', $prodId, prodLink($arrData['prodname']));

				$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndContinueEditing');
				$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("product.form");
				$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
			} else {
				// The product doesn't exist
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(GetLang('ProductDoesntExist'), MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}
			}
		}

		protected function _GetDayOptions($Selected=0)
		{
			$output = "";

			for($i = 1; $i <= 31; $i++) {
				if($Selected == $i) {
					$sel = 'selected="selected"';
				}
				else {
					$sel = "";
				}

				$output .= sprintf("<option value='%d' %s>%s</option>", $i, $sel, $i);
			}

			return $output;
		}

		/**
			*	Return a list of months as option tags
			*/
		protected function _GetMonthOptions($Selected=0)
		{
			$output = "";

			for($i = 1; $i <= 12; $i++) {
				if($Selected == $i) {
					$sel = 'selected="selected"';
				}
				else {
					$sel = "";
				}

				$stamp = isc_gmmktime(0, 0, 0, $i, 1, 2000);
				$month = isc_date("M", $stamp);
				$output .= sprintf("<option value='%d' %s>%s</option>", $i, $sel, $month);
			}

			return $output;
		}

		/**
			*	Return a list of years as option tags
			*/
		protected function _GetYearOptions($Selected=0)
		{

			$output = "";

			for($i = isc_date("Y"); $i <= isc_date("Y")+5; $i++) {
				if($Selected == $i) {
					$sel = 'selected="selected"';
				}
				else {
					$sel = "";
				}

				$output .= sprintf("<option value='%d' %s>%s</option>", $i, $sel, $i);
			}

			return $output;
		}

		// Get a list of downloads associated with a particular product.
		public function GetDownloadsGrid($productId=0, $productHash='')
		{
			if($productId > 0) {
				$where = sprintf("pd.productid='%d'", $productId);
			}
			else {
				$where = sprintf("pd.prodhash='%s'", $productHash);
			}

			$query = sprintf("
				select pd.*, sum(od.numdownloads) as numdownloads
				from [|PREFIX|]product_downloads pd
				left join [|PREFIX|]order_downloads od on (od.downloadid=pd.downloadid)
				where %s
				group by pd.downloadid", $where);
			$grid = '';

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$GLOBALS['DownloadId'] = $row['downloadid'];
				$GLOBALS['DownloadFile'] = $row['downfile'];
				$GLOBALS['NumDownloads'] = number_format($row['numdownloads']);
				$GLOBALS['DownloadName'] = $row['downname'];
				if($row['downdescription']) {
					$GLOBALS['DownloadName'] = sprintf("<span onmouseover=\"ShowQuickHelp(this, '%s', '%s');\" onmouseout=\"HideQuickHelp(this);\" class=\"HelpText\">%s</span>", $GLOBALS['DownloadName'], str_replace("'", "\\'", $row['downdescription']), $GLOBALS['DownloadName']);
				}
				$GLOBALS['DownloadSize'] = NiceSize($row['downfilesize']);
				if($row['downmaxdownloads'] == 0) {
					$GLOBALS['MaxDownloads'] = GetLang('Unlimited');
				}
				else {
					$GLOBALS['MaxDownloads'] = $row['downmaxdownloads'];
				}
				if($row['downexpiresafter']) {
					$days = $row['downexpiresafter']/86400;
					if(($days % 365) == 0) {
						$GLOBALS['ExpiresAfter'] = number_format($days/365)." ".GetLang('YearsLower');
					}
					else if(($days % 30) == 0) {
						$GLOBALS['ExpiresAfter'] = number_format($days/30)." ".GetLang('MonthsLower');
					}
					else if(($days % 7) == 0) {
						$GLOBALS['ExpiresAfter'] = number_format($days/7)." ".GetLang('WeeksLower');
					}
					else {
						$GLOBALS['ExpiresAfter'] = number_format($days)." ".GetLang('DaysLower');
					}
				}
				else {
					$GLOBALS['ExpiresAfter'] = GetLang('Never');
				}

				$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("product.form.downloadrow");
				$grid .= $GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate(true);
			}
			return $grid;
		}

		// Save a new or modified product download in the database.
		public function SaveProductDownload(&$err)
		{
			if (!isset($_REQUEST['downmaxdownloads'])) {
				$_REQUEST['downmaxdownloads'] = 0;
			}

			if (!isset($_REQUEST['downexpiresafter'])) {
				$_REQUEST['downexpiresafter'] = 0;
			}

			if (isset($_REQUEST['downexpiresrange'])) {
				if ($_REQUEST['downexpiresrange'] == "years") {
					$_REQUEST['downexpiresafter'] *= 365;
				} else if ($_REQUEST['downexpiresrange'] == "months") {
					$_REQUEST['downexpiresafter'] *= 30;
				} else if($_REQUEST['downexpiresrange'] == "weeks") {
					$_REQUEST['downexpiresafter'] *= 7;
				}
			}

			$filename = '';
			$filesize = 0;

			// Saving a new download
			if (!isset($_REQUEST['downloadid']) || $_REQUEST['downloadid'] == 0) {
				// Are we picking a file from the server to use instead of uploading one
				// directly from the browser ?
				if (isset($_REQUEST['serverfile'])) {

					// Is the file name valid ?
					$valid_files = $this->_GetImportFilesArray();
					if (!in_array($_REQUEST['serverfile'], $valid_files)) {
						$err = GetLang('InvalidFileName');
						return false;
					}

					$dirs = range('a', 'z');

					$downfile = $dirs[array_rand($dirs)].'/'.$_REQUEST['serverfile'];

					$source = ISC_BASE_PATH.'/'.GetConfig('DownloadDirectory').'/import/'.$_REQUEST['serverfile'];
					$dest = ISC_BASE_PATH.'/'.GetConfig('DownloadDirectory').'/'.$downfile;

					// We use sprintf here to avoid a bug with 32bit platforms and files > 2GB
					$filesize = sprintf("%u", filesize($source));

					// If the file is larger than 20 megabytes then move the file
					if ($filesize > 20 * 1024 * 1024) {
						if (!rename($source, $dest)) {
							return false;
						}
					}
					// If the file is smaller than 20 megabytes then copy the file (since it is probably safter to do this)
					else {
						if (!copy($source, $dest)) {
							return false;
						}
					}
					$filename = $_REQUEST['serverfile'];
					$filesize = filesize($dest);
				} else {
					if(!isset($_FILES['newdownload'])) {
						$err = GetLang('UploadErrorIniSize');
						return false;
					}

					if($_FILES['newdownload']['tmp_name'] == '' || $_FILES['newdownload']['size'] == 0) {
						$err = GetLang('UploadFailed');
						return false;
					}

					if($_FILES['newdownload']['error'] > 0) {
						switch($_FILES['newdownload']['error'])
						{
							case UPLOAD_ERR_INI_SIZE:
								$err = GetLang('UploadErrorIniSize');
								break;
							case UPLOAD_ERR_FORM_SIZE:
								$err = GetLang('UploadErrorFormSize');
								break;
							case UPLOAD_ERR_PARTIAL:
								$err = GetLang('UploadErrorPartial');
								break;
							case UPLOAD_ERR_NO_FILE:
								$err = GetLang('UploadErrorNoFile');
								break;
							case UPLOAD_ERR_NO_TMP_DIR:
								$err = GetLang('UploadErrorNoTmp');
								break;
							case UPLOAD_ERR_CANT_WRITE:
								$err = GetLang('UploadErrorCantWrite');
								break;
							case UPLOAD_ERR_CANT_WRITE:
								$err = GetLang('UploadErrorExtension');
								break;
						}
						return false;
					}
					$downfile = $this->_StoreFileAndReturnId("newdownload", FT_DOWNLOAD);
					if (!$downfile) {
						$err = GetLang('UploadErrorCantWrite');
						return false;
					}

					$filename = $_FILES['newdownload']['name'];
					$filesize = $_FILES['newdownload']['size'];

				}

				if(isset($_REQUEST['productId']) && $_REQUEST['productId'] != 0) {
					$productId = (int)$_REQUEST['productId'];
					$productHash = '';
				}
				else {
					$productId = '0';
					$productHash = $_REQUEST['productHash'];
				}

				$newDownload = array(
					"downfile" => $downfile,
					"productid" => $productId,
					"prodhash" => $productHash,
					"downdateadded" => time(),
					"downmaxdownloads" => (int)$_REQUEST['downmaxdownloads'],
					"downexpiresafter" => (int)$_REQUEST['downexpiresafter']*86400,
					"downname" => $filename,
					"downfilesize" => (int) $filesize,
					"downdescription" => $_REQUEST['downdescription']
				);
				$downloadid = $GLOBALS['ISC_CLASS_DB']->InsertQuery("product_downloads", $newDownload);

				$query = sprintf("SELECT prodname FROM [|PREFIX|]products WHERE productid='%d'", $productId);
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$prodName = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction("created", $downloadid, $filename, $productId, $prodName);
			}
			// Updating an existing download
			else {
				$downloadid = (int)$_REQUEST['downloadid'];
				$updatedDownload = array(
					"downdescription" => $_REQUEST['downdescription'],
					"downmaxdownloads" => (int)$_REQUEST['downmaxdownloads'],
					"downexpiresafter" => (int)$_REQUEST['downexpiresafter']*86400
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_downloads", $updatedDownload, "downloadid='".$GLOBALS['ISC_CLASS_DB']->Quote($downloadid)."'");

				$query = sprintf("SELECT p.prodname, p.productid, d.downname FROM [|PREFIX|]product_downloads d, [|PREFIX|]products p WHERE d.downloadid='%d' AND p.productid=d.productid", $downloadid);
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$product = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction("updated", $downloadid, $product['downname'], $product['productid'], $product['prodname']);
			}
			return true;
		}

		// Delete a download from a particular product.
		public function DeleteProductDownload()
		{
			if(!isset($_REQUEST['downloadid'])) {
				return false;
			}

			$downloadid = (int)$_REQUEST['downloadid'];

			$query = sprintf("SELECT p.prodname, p.productid, d.downname, d.downfile, p.prodvendorid FROM [|PREFIX|]product_downloads d, [|PREFIX|]products p WHERE d.downloadid='%d' AND p.productid=d.productid", $downloadid);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$download = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			// Does this user have permission to edit this product?
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $download['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				return false;
			}

			// Log this action
			$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($downloadid, $download['downname'], $download['productid'], $download['prodname']);

			// Remove the file from the file system
			if($download) {
				@unlink(GetConfig('DownloadDirectory') . "/" . $download['downfile']);
			}

			// Delete from the database
			$query = sprintf("delete from [|PREFIX|]product_downloads where downloadid='%d'", $downloadid);
			$GLOBALS['ISC_CLASS_DB']->Query($query);
			return true;
		}

		public function SearchProducts()
		{
			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
			$GLOBALS['CategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions("", "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false);

			$GLOBALS['ISC_CLASS_ADMIN_BRANDS'] = GetClass('ISC_ADMIN_BRANDS');
			$GLOBALS['BrandNameOptions'] = $GLOBALS['ISC_CLASS_ADMIN_BRANDS']->GetBrandsAsOptions();

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}

			if(!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS['HideInventoryOptions'] = "none";
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("products.search");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		*	This function checks to see if the user wants to save the search details as a custom search,
		*	and if they do one is created. They are then forwarded onto the search results
		*/
		public function SearchProductsRedirect()
		{

			// Format prices back to the western standard
			if($_GET['priceFrom'] != "") {
				$_GET['priceFrom'] = DefaultPriceFormat($_GET['priceFrom']);
				$_REQUEST['priceFrom'] = $_GET['priceFrom'];
			}

			if($_GET['priceTo'] != "") {
				$_GET['priceTo'] = DefaultPriceFormat($_GET['priceTo']);
				$_REQUEST['priceTo'] = $_GET['priceTo'];
			}

			// Are we saving this as a custom search?
			if(isset($_GET['viewName']) && $_GET['viewName'] != '') {
				if(isset($_GET['ISSelectReplacement_category'])) {
					unset($_GET['ISSelectReplacement_category']);
				}

				$search_id = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->SaveSearch($_GET['viewName'], $_GET);

				if($search_id > 0) {
					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($search_id, $_GET['viewName']);

					ob_end_clean();
					header(sprintf("Location:index.php?ToDo=customProductSearch&searchId=%d&new=true", $search_id));
					exit;
				}
				else {
					$this->ManageProducts(sprintf(GetLang('ViewAlreadExists'), isc_html_escape($_GET['viewName'])), MSG_ERROR);
				}

			}
			// Plain search
			else {
				$this->ManageProducts();
			}
		}

		/**
		*	Load a custom search
		*/
		public function CustomSearch()
		{
			SetSession('productsearch', (int) $_GET['searchId']);

			if ($_GET['searchId'] > 0) {
				$this->_customSearch = $GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->LoadSearch($_GET['searchId']);
				$_REQUEST = array_merge($_REQUEST, $this->_customSearch['searchvars']);
			}

			if (isset($_REQUEST['new'])) {
				$this->ManageProducts(GetLang('CustomSearchSaved'), MSG_SUCCESS);
			} else {
				$this->ManageProducts();
			}
		}

		public function DeleteCustomSearch()
		{
			if($GLOBALS['ISC_CLASS_ADMIN_CUSTOMSEARCH']->DeleteSearch($_GET['searchId'])) {
				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($_GET['searchId']);

				$this->ManageProducts(GetLang('DeleteCustomSearchSuccess'), MSG_SUCCESS);
			}
			else {
				$this->ManageProducts(GetLang('DeleteCustomSearchFailed'), MSG_ERROR);
			}
		}

		public function ImportProducts()
		{
			require_once dirname(__FILE__)."/../importer/products.php";
			$importer = new ISC_BATCH_IMPORTER_PRODUCTS();
		}

		public function ImportProductVariations()
		{
			require_once dirname(__FILE__)."/../importer/product_variations.php";
			$importer = new ISC_BATCH_IMPORTER_PRODUCT_VARIATIONS();
		}

		/**
		*	Create a view for products. Uses the same form as searching but puts the
		*	name of the view at the top and it's mandatory instead of optional.
		*/
		public function CreateView()
		{
			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');

			$visibleCategories = array();
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				if($vendorData['vendoraccesscats']) {
					$visibleCategories = explode(',', $vendorData['vendoraccesscats']);
				}
			}

			$GLOBALS['CategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions("", "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false, '', $visibleCategories);

			$GLOBALS['ISC_CLASS_ADMIN_BRANDS'] = GetClass('ISC_ADMIN_BRANDS');
			$GLOBALS['BrandNameOptions'] = $GLOBALS['ISC_CLASS_ADMIN_BRANDS']->GetBrandsAsOptions();

			$GLOBALS['OrderPaymentOptions'] = "";
			$GLOBALS['OrderShippingOptions'] = "";

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}

			if(!gzte11(ISC_MEDIUMPRINT)) {
				$GLOBALS['HideInventoryOptions'] = "none";
			}

			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("products.view");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		* BulkEditProductsStep1
		* Show the form to bulk edit at least two products
		*
		* @return Void
		*/
		public function BulkEditProductsStep1($MsgDesc = "", $MsgStatus = "")
		{
			$GLOBALS['ProductList'] = "";
			$GLOBALS['ProductIds'] = "";

			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');

			if(!isset($_POST['products']) || !is_array($_POST['products'])) {
				$this->ManageProducts();
				die();
			}

			$product_ids = implode(",", array_map('intval', $_POST['products']));

			$visibleCategories = array();
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				if($vendorData['vendoraccesscats']) {
					$visibleCategories = explode(',', $vendorData['vendoraccesscats']);
				}
			}

			if(strlen($product_ids) < 1) {
				$this->ManageProducts();
				die();
			}

			// Only fetch products this user can actually edit
			$vendorRestriction = '';
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$vendorRestriction = " AND prodvendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
			}

			$query = sprintf("
				SELECT productid, prodname, prodprice, prodfreeshipping, prodvisible, prodfeatured, prodvendorfeatured, prodbrandid,
				(SELECT brandname FROM [|PREFIX|]brands WHERE brandid=prodbrandid) as prodbrand
				FROM [|PREFIX|]products p
				WHERE productid IN (%s) ".$vendorRestriction,
				$product_ids
			);

			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

			$ProductIds = array();

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				$ProductIds[] = $row['productid'];
				$GLOBALS['ProductId'] = $row['productid'];
				$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);
				$GLOBALS['ProductBrand'] = isc_html_escape($row['prodbrand']);
				$GLOBALS['ProductBrandId'] = $row['prodbrandid'];
				$GLOBALS['ProductExistingBrand'] = $row['prodbrand'];
				$GLOBALS['ProductExistingBrandId'] = $row['prodbrandid'];

				$GLOBALS['ProductVisible'] = '';
				if($row['prodvisible']) {
					$GLOBALS['ProductVisible'] = 'checked="checked"';
				}

				$GLOBALS['ProductFeatured'] = '';
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() > 0) {
					$featuredColumn = 'prodvendorfeatured';
				}
				else {
					$featuredColumn = 'prodfeatured';
				}
				if($row[$featuredColumn]) {
					$GLOBALS['ProductFeatured'] = 'checked="checked"';
				}

				$GLOBALS['ProductFreeShipping'] = '';
				if($row['prodfreeshipping']) {
					$GLOBALS['ProductFreeShipping'] = 'checked="checked"';
				}

				$GLOBALS['ProductPrice'] = number_format($row['prodprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");

				// Load the product categories
				$cquery = sprintf("SELECT categoryid FROM [|PREFIX|]categoryassociations ca WHERE ca.productid=%d", $row['productid']);
				$cresult = $GLOBALS["ISC_CLASS_DB"]->Query($cquery);
				$cats = array();

				while($crow = $GLOBALS["ISC_CLASS_DB"]->Fetch($cresult)) {
					array_push($cats, $crow['categoryid']);
				}

				// Get the product categories list
				$GLOBALS['ProductCategories'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions($cats, "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false, '', $visibleCategories);
				$GLOBALS['ProductExistingCategories'] = implode(",", $cats);

				$GLOBALS['ProductList'] .= $GLOBALS["ISC_CLASS_TEMPLATE"]->GetSnippet("BulkEditItem");
			}

			$GLOBALS['ProductIds'] = implode(',', $ProductIds);
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("product.bulkedit.form");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		* BulkEditProductsStep2
		* Save the changes made on the bulk editing page
		*
		* @return Void
		*/
		public function BulkEditProductsStep2()
		{
			if(isset($_POST["product_ids"])) {
				$product_ids = explode(",", $_POST["product_ids"]);

				// Only fetch products this user can actually edit
				$vendorRestriction = '';
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$vendorRestriction = " AND prodvendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
				}

				// Load the existing products
				$existingProducts = array();
				$query = "SELECT * FROM [|PREFIX|]products WHERE productid IN (".implode(",", $GLOBALS['ISC_CLASS_DB']->Quote($product_ids)).") ".$vendorRestriction;
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($product = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$existingProducts[$product['productid']] = $product;
				}

				foreach($product_ids as $product_id) {
					$prodname =			$_POST["prodname_" . $product_id];
					$prodprice =		DefaultPriceFormat($_POST["prodprice_" . $product_id]);
					$prodbrand =		$_POST["prodbrand_" . $product_id];
					$prodbrandold =		$_POST["existing_brand_" . $product_id];
					$prodbrandid =		$_POST["existing_brand_id_" . $product_id];
					$prodcats =			$_POST["category_" . $product_id];
					$prodcatsold =		$_POST["existing_categories_" . $product_id];

					$prodfeatured = 0;
					if(isset($_POST['prodfeatured_'.$product_id])) {
						$prodfeatured = 1;
					}

					$prodvisible = 0;
					if(isset($_POST['prodvisible_'.$product_id])) {
						$prodvisible = 1;
					}

					$prodfreeshipping = 0;
					if(isset($_POST['prodfreeshipping_'.$product_id])) {
						$prodfreeshipping = 1;
					}

					$prodCatsCSV = implode(",", $prodcats);

					// Calculate the new price of the product
					$existingProduct = $existingProducts[$product_id];
					$prodcalculatedprice = CalcRealPrice($prodprice, $existingProduct['prodretailprice'], $existingProduct['prodsaleprice'], $existingProduct['prodistaxable']);

					// Do we need to update the categories?
					if($prodCatsCSV != $prodcatsold) {
						$GLOBALS["ISC_CLASS_DB"]->DeleteQuery("categoryassociations", sprintf("WHERE productid='%d'", $product_id));

						// Add the new category associations
						foreach($prodcats as $cat_id) {
							$ca = array("productid" => $product_id,
										"categoryid" => $cat_id
							);
							$GLOBALS['ISC_CLASS_DB']->InsertQuery("categoryassociations", $ca);
						}
					}

					// Do we need to update the brand?
					if($prodbrand != $prodbrandold) {

						if($prodbrand != "") {
							// Is it an existing brand?
							$bquery = sprintf("SELECT brandid FROM [|PREFIX|]brands WHERE brandname='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($prodbrand));
							$bresult = $GLOBALS["ISC_CLASS_DB"]->Query($bquery);
							$brow = $GLOBALS["ISC_CLASS_DB"]->Fetch($bresult);

							if($brow !== false) {
								// It's an existing brand
								$brand_id = $brow['brandid'];
							}
							else {
								// It's a new brand, let's add it
								$ba = array("brandname" => $prodbrand);
								$GLOBALS['ISC_CLASS_DB']->InsertQuery("brands", $ba);
								$brand_id = $GLOBALS["ISC_CLASS_DB"]->LastId();
							}
						}
						else {
							// Delete the brand
							$brand_id = 0;
						}
					}
					else {
						// The brand hasn't been changed
						$brand_id = $prodbrandid;
					}

					if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() > 0) {
						$featuredColumn = 'prodvendorfeatured';
					}
					else {
						$featuredColumn = 'prodfeatured';
					}

					// Update the product details
					$pa = array("productid" => $product_id,
								"prodname" => $prodname,
								"prodprice" => $prodprice,
								"prodcalculatedprice" => $prodcalculatedprice,
								$featuredColumn => $prodfeatured,
								"prodvisible" => $prodvisible,
								"prodfreeshipping" => $prodfreeshipping,
								"prodbrandid" => $brand_id,
								"prodcatids" => $prodCatsCSV

					);

					$this->productEntity->edit($pa);

					// update search data - can only update name here
					$searchData = array(
						"prodname" => $prodname,
					);

					$GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_search", $searchData, "productid='".$GLOBALS['ISC_CLASS_DB']->Quote($product_id)."'");

					// Save the words to the product_words table for search spelling suggestions
					manageSuggestedWordDatabase("product", $product_id, $prodname);
				}

				// Update product pricing
				$GLOBALS['ISC_CLASS_ADMIN_SETTINGS'] = GetClass('ISC_ADMIN_SETTINGS');
				$GLOBALS['ISC_CLASS_ADMIN_SETTINGS']->_UpdateProductPrices();

				// Do we want to keep editing or return to the products list?
				if(isset($_POST['keepediting'])) {
					$_POST['products'] = $product_ids;
					$this->BulkEditProductsStep1(GetLang("BulkEditUpdatedSuccessfully"), MSG_SUCCESS);
				}
				else {
					$this->ManageProducts(GetLang("BulkEditUpdatedSuccessfully"), MSG_SUCCESS);

				}
			}
			else {
				$this->ManageProducts();
			}
		}

		/**
		* ViewVariations
		* Show a list of all available product variations
		*
		* @return Void
		*/
		public function ViewVariations($MsgDesc = "", $MsgStatus = "")
		{

			$GLOBALS['VariationDataGrid'] = $this->_GetVariationGrid($num_variations);

			// Was this an ajax based sort? Return the table now
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
				echo $GLOBALS['VariationDataGrid'];
				return;
			}

			// Disable the delete button if there aren't any variations
			if($num_variations == 0) {
				$GLOBALS['DisableDelete'] = "DISABLED='DISABLED'";
				$GLOBALS['DisplayGrid'] = "none";
				$MsgDesc = GetLang("NoProductVariations");
				$MsgStatus = MSG_INFO;
			}

			if($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.variations.manage");
			$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
		}

		/**
		* _GetVariationGrid
		* Get all of the product variations from the database and return them as a grid
		*
		* @param Int $NumVariations A reference variable to store the number of variations found
		* @return String
		*/
		public function _GetVariationGrid(&$NumVariations)
		{

			// Show a list of variations in a table
			$page = 0;
			$start = 0;
			$numVariations = 0;
			$numPages = 0;
			$GLOBALS['VariationsGrid'] = "";
			$GLOBALS['Nav'] = "";
			$max = 0;

			$validSortFields = array('vname', 'vnumoptions');

			if(isset($_REQUEST['sortOrder']) && $_REQUEST['sortOrder'] == "asc") {
				$sortOrder = "asc";
			}
			else {
				$sortOrder = "desc";
			}

			if(isset($_REQUEST['sortField']) && in_array($_REQUEST['sortField'], $validSortFields)) {
				$sortField = $_REQUEST['sortField'];
				SaveDefaultSortField("ViewProductVariations", $_REQUEST['sortField'], $sortOrder);
			} else {
				list($sortField, $sortOrder) = GetDefaultSortField("ViewProductVariations", "vname", $sortOrder);
			}

			if(isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			}
			else {
				$page = 1;
			}

			// Build the pagination and sort URL
			$searchURL = '';
			foreach($_GET as $k => $v) {
				if($k == "sortField" || $k == "sortOrder" || $k == "page" || $k == "new" || $k == "ToDo" || $k == "SubmitButton1" || !$v) {
					continue;
				}
				if(is_array($v)) {
					foreach($v as $v2) {
						$searchURL .= sprintf("&%s[]=%s", $k, urlencode($v2));
					}
				}
				else {
					$searchURL .= sprintf("&%s=%s", $k, urlencode($v));
				}
			}

			$sortURL = sprintf("%s&amp;sortField=%s&amp;sortOrder=%s", $searchURL, $sortField, $sortOrder);
			$GLOBALS['SortURL'] = $sortURL;

			// Limit the number of questions returned
			if($page == 1) {
				$start = 1;
			}
			else {
				$start = ($page * ISC_PRODUCTS_PER_PAGE) - (ISC_PRODUCTS_PER_PAGE-1);
			}

			$start = $start-1;

			// Get the results for the query
			$variation_result = $this->_GetVariationList($start, $sortField, $sortOrder, $numVariations);
			$numPages = ceil($numVariations / ISC_PRODUCTS_PER_PAGE);
			$NumVariations = $numVariations;

			// Add the "(Page x of n)" label
			if($numVariations > ISC_PRODUCTS_PER_PAGE) {
				$GLOBALS['Nav'] = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);

				$GLOBALS['Nav'] .= BuildPagination($numVariations, ISC_PRODUCTS_PER_PAGE, $page, sprintf("index.php?ToDo=viewProductVariations%s", $sortURL));
			}
			else {
				$GLOBALS['Nav'] = "";
			}

			$GLOBALS['Nav'] = preg_replace('# \|$#',"", $GLOBALS['Nav']);
			$GLOBALS['SortField'] = $sortField;
			$GLOBALS['SortOrder'] = $sortOrder;
			$sortLinks = array(
				"Name" => "vname",
				"Options" => "vnumoptions",
			);

			BuildAdminSortingLinks($sortLinks, "index.php?ToDo=viewProductVariations&amp;".$searchURL."&amp;page=".$page, $sortField, $sortOrder);

			// Workout the maximum size of the array
			$max = $start + ISC_PRODUCTS_PER_PAGE;

			if ($max > $numVariations) {
				$max = $numVariations;
			}

			if($numVariations > 0) {
				// Display the products
				while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($variation_result)) {
					$GLOBALS['VariationId'] = (int) $row['variationid'];
					$GLOBALS['Name'] = isc_html_escape($row['vname']);
					if(gzte11(ISC_HUGEPRINT) && $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() == 0 && $row['vendorname']) {
						$GLOBALS['Name'] .= ' <small><strong>('.GetLang('Vendor').': '.$row['vendorname'].')</strong></small>';
					}
					$GLOBALS['NumOptions'] = (int) $row['vnumoptions'];
					$GLOBALS['Edit'] = '<a class="Action" href="index.php?ToDo=editProductVariation&amp;variationId=' . $row['variationid'] . '" title="' . GetLang('ProductVariationEdit') . '">' . GetLang('Edit') . '</a>';
					$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("product.variations.manage.row");
					$GLOBALS['VariationsGrid'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
				}

			}

			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.variations.manage.grid");
			return $GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate(true);
		}

		public function _GetVariationList($Start, $SortField, $SortOrder, &$NumVariations, $fields='', $AddLimit=true)
		{
			// Return an array containing details about variations.
			if($fields == '') {
				$fields = " *, v.vendorname AS vendorname ";
			}

			$queryWhere = '';

			// Only fetch variations which belong to the current vendor
			if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
				$queryWhere .= " AND vvendorid='".(int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()."'";
			}

			$countQuery = "SELECT COUNT(variationid) FROM [|PREFIX|]product_variations WHERE 1=1 ".$queryWhere;

			$query = "
				SELECT ".$fields."
				FROM [|PREFIX|]product_variations p
				LEFT JOIN [|PREFIX|]vendors v ON (v.vendorid=p.vvendorid)
				WHERE 1=1
			";
			$query .= $queryWhere;

			// Fetch the number of results
			$result = $GLOBALS['ISC_CLASS_DB']->Query($countQuery);
			$NumVariations = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

			// Add the sorting options
			$query .= sprintf("order by %s %s", $SortField, $SortOrder);

			// Add the limit
			if($AddLimit) {
				$query .= $GLOBALS["ISC_CLASS_DB"]->AddLimit($Start, ISC_PRODUCTS_PER_PAGE);
			}

			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			return $result;
		}

		/**
		* AddVariationStep1
		* The form to add a product variation with options to the store
		*
		* @return Void
		*/
		public function AddVariationStep1($MsgDesc = "", $MsgStatus = "", $PreservePost=false)
		{
			if($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			$GLOBALS['FormAction'] = "addProductVariation2";
			$GLOBALS['Title'] = GetLang("AddProductVariation");
			$GLOBALS['SaveAndAddAnother'] = GetLang("SaveAndAddAnother");

			if (!array_key_exists('variationId', $_POST)) {
				$GLOBALS['VariationName'] = isc_html_escape(GetLang('ProductVariationTestDataName'));
			} else if (array_key_exists('vname', $_POST)) {
				$GLOBALS['VariationName'] = isc_html_escape($_POST['vname']);
			}

			if (array_key_exists('variationId', $_POST)) {
				$GLOBALS['HideVariationTestDataWarning'] = 'none';
			}

			if(!gzte11(ISC_HUGEPRINT)) {
				$GLOBALS['HideVendorOption'] = 'display: none';
			}
			else {
				$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				if(isset($vendorData['vendorid'])) {
					$GLOBALS['HideVendorSelect'] = 'display: none';
					$GLOBALS['CurrentVendor'] = isc_html_escape($vendorData['vendorname']);
				}
				else {
					$GLOBALS['HideVendorLabel'] = 'display: none';
					$GLOBALS['VendorList'] = $this->BuildVendorSelect();
				}
			}

			/**
			 * Display the test data only when they have entered the variation admin for the first time
			 */
			if (!array_key_exists('variationId', $_POST)) {
				$variationData = $this->GetVariationTestData();
			} else {
				$variationData = $this->GetVariationData(0);
			}

			$GLOBALS['Variations'] = $this->BuildVariationCreate($variationData);

			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.variation.form");
			echo $GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
		}

		/**
		* AddVariationStep2
		* Save the details of the variation to the database
		*
		* @return Void
		*/
		public function AddVariationStep2()
		{
			$data = $this->GetVariationData(0);

			/**
			 * Validate our data
			 */
			if (!$this->ValidateVariationData($data, $error)) {
				return $this->AddVariationStep1($error, MSG_ERROR, true);
			}

			/**
			 * Add our new variation record
			 */
			$variationId = $this->SaveVariationData($data);

			/**
			 * Did we get any errors?
			 */
			if (!isId($variationId)) {
				return $this->AddVariationStep1(sprintf(GetLang("ErrorWhenAddingVariation"), $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg()), MSG_ERROR, true);
			}

			if (isset($_POST['addanother'])) {
				$_POST = array('variationId' => '');
				$this->AddVariationStep1(GetLang("VariationAddedSuccessfully"), MSG_SUCCESS);
			} else {
				$this->ViewVariations(GetLang("VariationAddedSuccessfully"), MSG_SUCCESS);
			}
		}

		/**
		* EditVariationStep1
		* The form to edit a product variation with options to the store
		*
		* @return Void
		*/
		public function EditVariationStep1($MsgDesc = "", $MsgStatus = "", $PreservePost=false)
		{
			if($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			$variationId = null;
			if (isset($_GET['variationId'])) {
				$variationId = (int)$_GET['variationId'];
			}

			if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
				return $GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
			}

			/**
			 * Get our variation data. If we couldn't get it then display an error
			 */
			if ($PreservePost) {
				$variationData = $this->GetVariationData(0);
			} else {
				$variationData = $this->GetVariationData($variationId);
			}

			if (!isId($variationId) || !$variationData) {
				return $this->ViewVariations(GetLang('ProductVariationErrorDoesNotExists'), MSG_ERROR);
			}

			/**
			 * We need to have a list of all the variation options that are in use by products
			 */
			$affected = array();
			$result = $GLOBALS['ISC_CLASS_DB']->Query("
				SELECT
					voptionid
				FROM
					[|PREFIX|]product_variation_options
				WHERE
					vovariationid = " . (int)$variationId);
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$affected[] = $row['voptionid'];
			}

			$affected = array_unique($affected);
			$GLOBALS['AffectedVariations'] = implode(',', $affected);

			$GLOBALS['FormAction'] = "editProductVariation2";
			$GLOBALS['Title'] = GetLang("EditProductVariation");
			$GLOBALS['VariationName'] = $variationData['name'];
			$GLOBALS['VariationId'] = $variationData['id'];
			$GLOBALS['SaveAndAddAnother'] = GetLang("SaveAndContinueEditing");
			$GLOBALS['HideVariationTestDataWarning'] = 'none';

			if(!gzte11(ISC_HUGEPRINT)) {
				$GLOBALS['HideVendorOption'] = 'display: none';
			}
			else {
				$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
				if(isset($vendorData['vendorid'])) {
					$GLOBALS['HideVendorSelect'] = 'display: none';
					$GLOBALS['CurrentVendor'] = isc_html_escape($vendorData['vendorname']);
				}
				else {
					$GLOBALS['HideVendorLabel'] = 'display: none';
					$GLOBALS['VendorList'] = $this->BuildVendorSelect();
				}
			}

			$GLOBALS['Variations'] = $this->BuildVariationCreate($variationData);

			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.variation.form");
			print $GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
		}

		/**
		* EditVariationStep2
		* Save the details of the variation to the database
		*
		* @return Void
		*/
		public function EditVariationStep2()
		{
			$variationId = null;
			if (isset($_POST['variationId'])) {
				$variationId = (int)$_POST['variationId'];
			}

			if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
				return $GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
			}

			/**
			 * Get our variation data. If we couldn't get it then display an error
			 */
			$variationData = $this->GetVariationData(0);

			if (!isId($variationId) || !$variationData) {
				return $this->ViewVariations(GetLang('ProductVariationErrorDoesNotExists'), MSG_ERROR);
			}

			/**
			 * Validate our data
			 */
			if (!$this->ValidateVariationData($variationData, $error)) {
				$_GET['variationId'] = $variationId;
				return $this->EditVariationStep1($error, MSG_ERROR, true);
			}

			/**
			 * Add our new variation record
			 */
			$rtn = $this->SaveVariationData($variationData);

			/**
			 * Did we get any errors?
			 */
			if (!$rtn) {
				$_GET['variationId'] = $variationId;
				return $this->EditVariationStep1(sprintf(GetLang("ErrorWhenUpdatingVariation"), $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg()), MSG_ERROR, true);
			}

			if (isset($_POST['addanother'])) {
				$_GET['variationId'] = $variationId;
				$this->EditVariationStep1(GetLang("VariationUpdatedSuccessfully"), MSG_SUCCESS);
			} else {
				$this->ViewVariations(GetLang("VariationUpdatedSuccessfully"), MSG_SUCCESS);
			}
		}

		/**
		 * Save the variation information
		 *
		 * Method will save the variation information to the database. Will look in the $data array for the variation ID to see if it is an update
		 * or a new record
		 *
		 * @access private
		 * @param array $data The variation information to save
		 * @return mixed Either the new variation ID if successfully added, TRUE if successfully updated, FALSE otherwise
		 */
		private function SaveVariationData($data)
		{
			/**
			 * Do we have any data to insert/update?
			 */
			if (!is_array($data) || empty($data)) {
				return false;
			}

			$variation = null;

			if (isId($data['id'])) {
				$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]product_variations WHERE variationid = " . (int)$data['id']);
				$variation = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
			}

			/**
			 * Check to see if we were given a proper variation ID
			 */
			if (isId($data['id']) && !is_array($variation)) {
				return false;
			}

			/**
			 * Start our transaction. If that dies then bail
			 */
			if ($GLOBALS["ISC_CLASS_DB"]->StartTransaction() === false) {
				return false;
			}

			$savedata = array(
						'vname' => $data['name'],
						'vnumoptions' => count($data['options']),
			);

			/**
			 * Assign our vendor ID
			 */
			if (gzte11(ISC_HUGEPRINT)) {
				// User is assigned to a vendor so any variations they create must be too
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$savedata['vvendorid'] = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
				} else if(isId($data['vendor'])) {
					$savedata['vvendorid'] = (int)$data['vendor'];
				}
			}

			/**
			 * Add/Update the variation record
			 */
			if (!isId($data['id'])) {
				$rtn = $GLOBALS['ISC_CLASS_DB']->InsertQuery('product_variations', $savedata);
				$data['id'] = $rtn;
			} else {
				$rtn = $GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_variations', $savedata, "variationid=" . (int)$data['id']);
			}

			if ($rtn === false) {
				return false;
			}

			/**
			 * Now to add/edit the options. These options are in order.
			 */
			$optionPos = 0;
			$deleteCombo = false;
			$groupedValues = array();
			foreach ($data['options'] as $option) {

				$optionPos++;
				$valuePos = 0;
				$addValues = array();
				$editValues = array();
				$origOptionName = '';
				$newOptionName = '';

				foreach ($option['values'] as $value) {
					$valuePos ++;
					$savedata = array(
						'vovariationid' => (int)$data['id'],
						'voname' => $option['name'],
						'vovalue' => $value['name'],
						'vooptionsort' => (int)$optionPos,
						'vovaluesort' => (int)$valuePos,
					);

					/**
					 * Are we updating or adding
					 */
					if (!isset($value['valueid']) || !isId($value['valueid'])) {
						$rtn = $GLOBALS['ISC_CLASS_DB']->InsertQuery('product_variation_options', $savedata);
						$addValues[] = (int)$rtn;
					} else {

						/**
						 * If we are updating then we need to make sure that option name is the same for all the values within that option
						 */
						if ($origOptionName == '') {
							$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT voname FROM [|PREFIX|]product_variation_options WHERE voptionid = " . (int)$value['valueid']);
							$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
							$origOptionName = isc_html_escape($row['voname']);
							$newOptionName = $savedata['voname'];
						}

						$editValues[] = (int)$value['valueid'];
						$rtn = $GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_variation_options', $savedata, 'voptionid=' . (int)$value['valueid']);
					}

					if ($rtn === false) {
						$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
						return false;
					}
				}

				$groupedValues = array_merge($groupedValues, $editValues, $addValues);
				$groupedValues = array_unique($groupedValues);

				/**
				 * If our variation already exists and we have added a new value then delete all combinations for it
				 */
				if (is_array($variation) && !empty($addValues)) {
					$deleteCombo = true;
				}

				/**
				 * Update our new option name if we have to
				 */
				if ($origOptionName !== '') {
					$savedata = array(
						'voname' => $newOptionName
					);

					$rtn = $GLOBALS['ISC_CLASS_DB']->UpdateQuery("product_variation_options", $savedata, "vovariationid=" . (int)$data['id']  . " AND voname='" . $GLOBALS['ISC_CLASS_DB']->Quote($origOptionName) . "'");
					if ($rtn === false) {
						$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
						return false;
					}
				}
			}

			/**
			 * OK, we have all the option values, now we remove any combinations that were using any deleted option values. Again, only do this for existing variations and also
			 * too only do this if $deleteCombo is FALSE!!!
			 */
			if (is_array($variation) && !$deleteCombo) {

				/**
				 * First, run a query to see which options (grouped option values) are to be deleted
				 */
				$query = "SELECT voname, GROUP_CONCAT(voptionid) AS vovalues, COUNT(*) AS vototal, SUM(IF(voptionid IN(" . implode(',', $groupedValues) . "), 0, 1)) AS vodelete
							FROM [|PREFIX|]product_variation_options
							GROUP BY voname";

				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

					$tmpValues = explode(',', $row['vovalues']);
					$deleteComboIdx = array();

					/**
					 * Was the entire option deleted?
					 */
					if ($row['vototal'] == $row['vodelete']) {

						/**
						 * We keep a record of what we removed because when we remove one option from the combination then we'll also create duplicate combinations
						 */
						$duplicatRecords = array();

						/**
						 * Loop through all the combinations and remove that option while still keeping that combination
						 */
						$sResult = $GLOBALS['ISC_CLASS_DB']->Query("SELECT combinationid, vcoptionids FROM [|PREFIX|]product_variation_combinations WHERE vcvariationid=" . (int)$data['id']);
						while ($sRow = $GLOBALS['ISC_CLASS_DB']->Fetch($sResult)) {
							$tmpCobmo = explode(',', $sRow['vcoptionids']);
							$tmpCount = count($tmpCobmo);

							foreach ($tmpValues as $findValue) {
								$foundKey = array_search($findValue, $tmpCobmo);
								if ($foundKey !== false) {
									unset($tmpCobmo[$foundKey]);
								}
							}

							/**
							 * Do we need to do anything?
							 */
							if ($tmpCount !== count($tmpCobmo)) {

								/**
								 * Build the key to check for duplicates
								 */
								$duplicateKey = $tmpCobmo;
								sort($duplicateKey);
								$duplicateKey = implode('-', $duplicateKey);

								/**
								 * Check our duplicate record. If we are in then mark it to be deleted
								 */
								if (array_key_exists($duplicateKey, $duplicatRecords)) {
									$deleteComboIdx[] = (int)$sRow['combinationid'];

								/**
								 * Else we update it
								 */
								} else {
									$duplicatRecords[$duplicateKey] = true;
									sort($tmpCobmo);
									$rtn = $GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_variation_combinations', array('vcoptionids' => implode(',', $tmpCobmo)), 'combinationid = ' . (int)$sRow['combinationid']);

									if ($rtn === false) {
										$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
										return false;
									}
								}
							}
						}

					/**
					 * Else we just delete those combinations that use these values IF some values were deleted. Store the combinationid to an array so we can just use one delete query
					 */
					} else if ($row['vodelete'] > 0) {
						$sResult = $GLOBALS['ISC_CLASS_DB']->Query("SELECT combinationid, vcoptionids FROM [|PREFIX|]product_variation_combinations WHERE vcvariationid=" . (int)$data['id']);

						while ($sRow = $GLOBALS['ISC_CLASS_DB']->Fetch($sResult)) {
							$tmpCobmo = explode(',', $sRow['vcoptionids']);
							$removeCombo = false;

							foreach ($tmpCobmo as $id) {
								if (!in_array($id, $groupedValues)) {
									$removeCombo = true;
									break;
								}
							}

							if ($removeCombo) {
								$deleteComboIdx[] = (int)$sRow['combinationid'];
							}
						}
					}

					/**
					 * Delete any combinations if we have to
					 */
					if (!empty($deleteComboIdx)) {
						$rtn = $GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_variation_combinations', 'WHERE combinationid IN(' . implode(',', $deleteComboIdx) . ')');
						if ($rtn === false) {
							$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
							return false;
						}
					}
				}

			/**
			 * Else we delete all combinations that are using this variation IF this variation has had some extra values added
			 */
			} else if ($deleteCombo) {
				$rtn = $GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_variation_combinations', 'WHERE vcvariationid=' . (int)$data['id']);
				if ($rtn === false) {
					$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
					return false;
				}

				$rtn = $GLOBALS['ISC_CLASS_DB']->UpdateQuery('products', array('prodvariationid' => '0'), 'prodvariationid=' . (int)$data['id']);
				if ($rtn === false) {
					$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
					return false;
				}
			}

			/**
			 * Now we delete all values that were removed. Only do this for existing variations as it is pretty usless on new variations
			 */
			if (is_array($variation)) {

				$extraWhere = '';
				if (!empty($groupedValues)) {
					$extraWhere = " AND voptionid NOT IN(" . implode(',', $groupedValues) . ")";
				}

				$rtn = $GLOBALS['ISC_CLASS_DB']->DeleteQuery("product_variation_options", "WHERE vovariationid=" . (int)$data['id'] . $extraWhere);
				if ($rtn === false) {
					$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
					return false;
				}
			}

			/**
			 * Lastly, we need to update the last modified time for all the products that use this variation
			 */
			$savedata = array(
				"prodlastmodified" => time()
			);

			$GLOBALS["ISC_CLASS_DB"]->UpdateQuery("products", $savedata, "prodvariationid=" . (int)$data["id"]);

			/**
			 * Now we try commiting this. If we get an error here then just bail
			 */
			if ($GLOBALS['ISC_CLASS_DB']->CommitTransaction() === false) {
				$GLOBALS['ISC_CLASS_DB']->RollbackTransaction();
				return false;
			}

			/**
			 * All is good, now return something to say so
			 */
			if ($variation) {
				return true;
			} else {
				return $data['id'];
			}
		}

		/**
		 * Built the variation form
		 *
		 * Function will build the sortable HTML form used for filling in the variation data
		 *
		 * @access private
		 * @param array $data The optional data to build from. Should be the return from GetVariationData()
		 * @return string The variation HTML form
		 */
		private function BuildVariationCreate($data=array())
		{
			/**
			 * A fallback for adding the essential information
			 */
			if (!is_array($data) || empty($data) || !array_key_exists('options', $data) || empty($data['options'])) {
				$data = array(
							array(
								'index' => 0,
								'name' => '',
								'values' => array(
												array(
														'index' => 0,
														'valueid' => '',
														'name' => '',
													)
												)
										)
							);
			} else {
				$data = $data['options'];
			}

			/**
			 * Now to build the rows. Firstly see if we should hide the option row delete button
			 */
			if (count($data) <= 1) {
				$GLOBALS['HideRowDelete'] = 'none';
			}

			$rows = '';

			foreach ($data as $row) {

				if (array_key_exists('values', $row) && is_array($row['values'])) {
					$values = '';

					/**
					 * Should we hide the value delete button?
					 */
					if (count($row['values']) <= 1) {
						$GLOBALS['HideOptionDelete'] = 'none';
					}
					foreach ($row['values'] as $value) {

						$GLOBALS['VariationOptionRankId'] = isc_html_escape($row['index']);
						$GLOBALS['VariationValueRankId'] = $value['index'];

						if (array_key_exists('valueid', $value)) {
							$GLOBALS['VariationValueId'] = isc_html_escape($value['valueid']);
						}

						if (array_key_exists('name', $value)) {
							$GLOBALS['VariationValue'] = isc_html_escape($value['name']);
						}

						$values .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ProductVariationValue');
					}

					$GLOBALS['ProductVariationValue'] = $values;
				}

				$GLOBALS['VariationOptionRankId'] = isc_html_escape($row['index']);
				$GLOBALS['VariationOptionName'] = isc_html_escape($row['name']);
				$rows .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ProductVariationRow');
			}

			return $rows;
		}

		/**
		 * Validate the submitted variation form data
		 *
		 * Method will validate the submitted variation form data
		 *
		 * @access private
		 * @param array $data The variation data to validate
		 * @param string &$error The referenced string to store the error in, if any were found
		 * @return bool TRUE if POST data is valid, FALSE if there were errors
		 */
		private function ValidateVariationData($data, &$error)
		{
			/**
			 * Do we have anything to validate?
			 */
			if (empty($data) || $data['name'] == '') {
				$error = GetLang("ProductVariationErrorNoVariationName");
				return false;
			}

			/**
			 * Check to see if this variation name is unique
			 */
			$query = "SELECT * FROM [|PREFIX|]product_variations WHERE vname='" . $GLOBALS['ISC_CLASS_DB']->Quote($data['name']) . "'";
			if (isId($data['id'])) {
				$query .= " AND variationid != " . (int)$data['id'];
			}

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			if ($GLOBALS['ISC_CLASS_DB']->CountResult($result)) {
				$error = GetLang("ProductVariationErrorNameNotUnique");
				return false;
			}

			/**
			 * Do we have any options?
			 */
			if (!array_key_exists('options', $data) || empty($data['options'])) {
				$error = GetLang('ProductVariationErrorNoData');
				return false;
			}

			$pos=0;
			$optionNames = array();
			foreach ($data['options'] as $rowIndex => $row) {
				$pos++;

				if ($row['name'] == '') {
					$error = sprintf(GetLang('ProductVariationErrorNoOptionName'), $pos);
					return false;
				} else if (count($row['values']) <= 1) {
					$error = sprintf(GetLang('ProductVariationErrorInvalidOptions'), $pos);
					return false;
				}

				$validateUniqueIdx = array();

				foreach ($row['values'] as $value) {
					if (isset($value['valueid']) && isId($value['valueid'])) {
						$validateUnique[] = (int)$value['valueid'];
					}
				}

				/**
				 * Check to see if each of our option names are unique
				 */
				foreach ($optionNames as $id => $name) {
					if ($name == $row['name']) {
						$error = sprintf(GetLang('ProductVariationErrorOptionNameNotUnique'), $pos, ($id+1));
						return false;
					}
				}

				$optionNames[] = $row['name'];
			}

			return true;
		}

		/**
		 * Get the posted variation data
		 *
		 * Method will return the posted variation data
		 *
		 * @access private
		 * @param int $variationId The optional variation to load from the database. Default is 0 (load from POST)
		 * @return array The posted variation data
		 */
		private function GetVariationData($variationId=0)
		{
			$data = array();

			/**
			 * Load from database
			 */
			if (isId($variationId)) {
				$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]product_variations WHERE variationid=" . (int)$variationId);
				$variation = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

				if (!$variation) {
					return $data;
				}

				$data['id'] = (int)$variation['variationid'];
				$data['name'] = $variation['vname'];
				$data['vendor'] = (int)$variation['vvendorid'];
				$data['options'] = array();

				/**
				 * Now get the options
				 */
				$currentOption = null;
				$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]product_variation_options WHERE vovariationid=" . (int)$variationId . " ORDER BY vooptionsort, vovaluesort");

				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

					/**
					 * Check to see if we are still using the same option
					 */
					if (is_null($currentOption) || $currentOption !== $row['voname']) {
						$optionKey = count($data['options']);
						$valueKey = 0;
						$currentOption = $row['voname'];
						$data['options'][$optionKey] = array(
										'index' => $optionKey,
										'name' => $row['voname'],
										'values ' => array(),
						);
					}

					/**
					 * Add the option
					 */
					$data['options'][$optionKey]['values'][$valueKey] = array(
										'valueid' => $row['voptionid'],
										'index' => $valueKey,
										'name' => $row['vovalue']
					);

					$valueKey++;
				}

			/**
			 * Else load from POST
			 */
			} else {

				$data = array(
					'options' => array()
				);

				if (array_key_exists('variationId', $_POST)) {
					$data['id'] = (int)$_POST['variationId'];
				}

				if (array_key_exists('vname', $_POST)) {
					$data['name'] = $_POST['vname'];
				}

				if (array_key_exists('vendor', $_POST)) {
					$data['vendor'] = $_POST['vendor'];
				}

				/**
				 * Go get our options. Bail if we do not have any
				 */
				if (!array_key_exists('variationOptionName', $_POST) || !is_array($_POST['variationOptionName'])) {
					return $data;
				}

				$options = array();
				foreach ($_POST['variationOptionName'] as $optionId => $optionVal) {

					/**
					 * Start our record
					 */
					$optionKey = count($options);
					$options[$optionKey] = array(
							'index' => $optionId,
							'name' => trim($optionVal),
							'values' => array()
					);

					/**
					 * Do we have any values at all?
					 */
					if (!isset($_POST['variationOptionValue'][$optionId]) || !is_array($_POST['variationOptionValue'][$optionId])) {
						continue;
					}

					foreach ($_POST['variationOptionValue'][$optionId] as $valueId => $valueVal) {

						$valueKey = count($options[$optionKey]['values']);

						$options[$optionKey]['values'][$valueKey] = array(
											'index' => $valueId,
											'name' => trim($valueVal)
						);

						if (isset($_POST['variationOptionValueId'][$optionId][$valueId]) && isId($_POST['variationOptionValueId'][$optionId][$valueId])) {
							$options[$optionKey]['values'][$valueKey]['valueid'] = (int)$_POST['variationOptionValueId'][$optionId][$valueId];
						}
					}
				}

				/**
				 * Add our options to our return data array
				 */
				$data['options'] = $options;
			}

			return $data;
		}

		/**
		 * Get default variation options
		 *
		 * Method will return the default variations when adding in a new variation. Basically some test data
		 *
		 * @access private
		 * return array The default variation test data
		 */
		private function GetVariationTestData()
		{
			$testdata = array(
						'options' => array()
						);

			$testdata['options'][] = array(
					'index' => 0,
					'name' => GetLang('ProductVariationTestDataOptionColour'),
					'values' => array(
									array(
										'index' => 0,
										'name' => GetLang('ProductVariationTestDataValueColourRed')
									),
									array(
										'index' => 1,
										'name' => GetLang('ProductVariationTestDataValueColourBlue')
									),
									array(
										'index' => 2,
										'name' => GetLang('ProductVariationTestDataValueColourPurple')
									),
									array(
										'index' => 3,
										'name' => GetLang('ProductVariationTestDataValueColourOrange')
									)
								)
						);

			$testdata['options'][] = array(
					'index' => 1,
					'name' => GetLang('ProductVariationTestDataOptionSize'),
					'values' => array(
									array(
										'index' => 0,
										'name' => GetLang('ProductVariationTestDataValueSizeSmall')
									),
									array(
										'index' => 1,
										'name' => GetLang('ProductVariationTestDataValueSizeMedium')
									),
									array(
										'index' => 2,
										'name' => GetLang('ProductVariationTestDataValueSizeLarge')
									),
									array(
										'index' => 3,
										'name' => GetLang('ProductVariationTestDataValueSizeXLarge')
									)
								)
						);

			$testdata['options'][] = array(
					'index' => 2,
					'name' => GetLang('ProductVariationTestDataOptionStyle'),
					'values' => array(
									array(
										'index' => 0,
										'name' => GetLang('ProductVariationTestDataValueStyleModern')
									),
									array(
										'index' => 1,
										'name' => GetLang('ProductVariationTestDataValueStyleClassic')
									)
								)
						);

			return $testdata;
		}

		/**
		* _LoadVariation
		* Load the details of a product variation from the product_variations table
		*
		* @param Int $VariationId The id of the variation to load
		* @return Array containing the variation details on success, false on failure
		*/
		public function _LoadVariation($VariationId)
		{
			$query = sprintf("SELECT * FROM [|PREFIX|]product_variations WHERE variationid='%d'", $VariationId);
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

			if($row !== false) {
				return $row;
			}
			else {
				return false;
			}
		}

		/**
		* _LoadVariationOptions
		* Load a list of options for a variation
		*
		* @param Int $VariationId The id of the variation to load options for
		* @return String containing the variation details on success, false on failure
		*/
		public function _LoadVariationOptions($VariationId)
		{
			$query = sprintf("SELECT * FROM [|PREFIX|]product_variation_options WHERE vovariationid='%d'", $VariationId);
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			$options = "";
			$names_done = array();

			if($GLOBALS["ISC_CLASS_DB"]->CountResult($result) > 0) {
				while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
					if(!in_array($row['voname'], $names_done)) {
						$options .= "\n" . $row['voname'] . ":";
						$names_done[] = $row['voname'];
					}

					$options .= $row['vovalue'] . ",";
				}

				$options = str_replace(",\n", "", $options);
				$options = preg_replace("/,$/", "", $options);
				return $options;
			}
			else {
				return false;
			}
		}

		/**
		* DeleteVariations
		* Delete one/more product variations from the database
		*
		* @return Void
		*/
		public function DeleteVariations()
		{
			if(isset($_POST['variations']) && is_array($_POST['variations'])) {

				foreach ($_POST['variations'] as $k => $v) {
					$_POST['variations'][$k] = (int) $v;
				}

				// What we do here is feed the list of product IDs in to a query with the vendor applied so that way
				// we're sure we're only deleting variations this user has permission to delete.
				$variation_ids = implode("','", array_map('intval', $_POST['variations']));
				$vendorId = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
				if($vendorId > 0) {
					$query = "
						SELECT variationid
						FROM [|PREFIX|]product_variations
						WHERE variationid IN ('".$variation_ids."') AND vvendorid='".(int)$vendorId."'
					";
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					$variation_ids = '';
					while($variation = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						$variation_ids .= $variation['variationid'].',';
					}
					$variation_ids = rtrim($variation_ids, ',');
				}
				$GLOBALS["ISC_CLASS_DB"]->StartTransaction();

				// Delete the variation
				$GLOBALS["ISC_CLASS_DB"]->DeleteQuery("product_variations", sprintf("WHERE variationid IN('%s')", $variation_ids));

				// Delete the variation combinations
				$GLOBALS["ISC_CLASS_DB"]->DeleteQuery("product_variation_combinations", sprintf("WHERE vcvariationid IN('%s')", $variation_ids));

				// Delete the variation options
				$GLOBALS["ISC_CLASS_DB"]->DeleteQuery("product_variation_options", sprintf("WHERE vovariationid IN('%s')", $variation_ids));

				// Update the products that use this variation to not use any at all
				$GLOBALS["ISC_CLASS_DB"]->UpdateQuery("products", array("prodvariationid" => "0"), "prodvariationid IN('" . $variation_ids . "')");

				if($GLOBALS["ISC_CLASS_DB"]->GetErrorMsg() == "") {
					$GLOBALS["ISC_CLASS_DB"]->CommitTransaction();
					$this->ViewVariations(GetLang("VariationDeletedSuccessfully"), MSG_SUCCESS);
				}
				else {
					$GLOBALS["ISC_CLASS_DB"]->RollbackTransaction();
					$this->ViewVariations(sprintf(GetLang("ErrorWhenDeletingVariation"), $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg()), MSG_ERROR);
				}
			}
			else {
				$this->ViewVariations();
			}
		}

		/**
		 * Copy variation combination data from one product to another product
		 *
		 * Method will duplicate all the variation combination data, imcluding images, to either a product ID or a product hash
		 *
		 * @access private
		 * @param int $fromProductId The product to duplciate the variations from
		 * @param int $toProductId The optional product ID to duplicate the variations to. This will delete all existing variations!
		 * @param string $toProductHash The optional product hash to duplicate the variations to. This will delete all existing variations!
		 * @return bool TRUE if all the variation combinations were duplicated successfully, FALSE if not
		 */
		private function _CopyVariationData($fromProductId, $toProductId=0, $toProductHash='')
		{
			if (!isId($fromProductId)) {
				print 'Step 1';
				return false;
			}

			/**
			 * Must either have a product ID or a hash string
			 */
			if (!isId($toProductId) && $toProductHash == '') {
				print 'Step 2';
				return false;
			}

			/**
			 * Delete all previous variations for the 'to' product as we really do not want to mix them up
			 */
			if (isId($toProductId)) {
				$rtn = $GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_variation_combinations', "WHERE vcproductid=" . (int)$toProductId);
			} else {
				$rtn = $GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_variation_combinations', "WHERE vcproducthash='" . $GLOBALS['ISC_CLASS_DB']->Quote($toProductHash) . "'");
			}

			if ($rtn === false) {
				return false;
			}

			$dir = GetConfig('ImageDirectory');
			$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]product_variation_combinations WHERE vcproductid=" . (int)$fromProductId);
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

				$newImage = '';
				$newThumb = '';

				/**
				 * Copy the images if we have to
				 */
				if ($row['vcimage'] !== '') {
					$origImage = realpath(ISC_BASE_PATH . '/' . $dir . '/' . $row['vcimage']);
					$newImage = $this->_CopyImages($origImage);
				}

				if ($row['vcimagezoom'] !== '') {
					$origZoom = realpath(ISC_BASE_PATH . '/' . $dir . '/' . $row['vcimagezoom']);
					$newZoom = $this->_CopyImages($origZoom);
				}

				if ($row['vcimagestd'] !== '') {
					$origStandard = realpath(ISC_BASE_PATH . '/' . $dir . '/' . $row['vcimagestd']);
					$newStandard = $this->_CopyImages($origStandard);
				}

				if ($row['vcimagethumb'] !== '') {
					$origThumb = realpath(ISC_BASE_PATH . '/' . $dir . '/' . $row['vcimagethumb']);
					$newThumb = $this->_CopyImages($origThumb);
				}

				/**
				 * Now store the record
				 */
				$savedata = $row;
				$savedata['vcimage'] = $newImage;
				$savedata['vcimagezoom'] = $newZoom;
				$savedata['vcimagestd'] = $newStandard;
				$savedata['vcimagethumb'] = $newThumb;
				unset($savedata['combinationid']);

				if (isId($toProductId)) {
					$savedata['vcproductid'] = (int)$toProductId;
					$savedata['vcproducthash'] = '';
				} else {
					unset($savedata['vcproductid']);
					$savedata['vcproducthash'] = $toProductHash;
				}

				$GLOBALS['ISC_CLASS_DB']->InsertQuery('product_variation_combinations', $savedata);
			}

			return true;
		}

		/**
		* _GetVariationData
		* Load the variation data for a product either from the form or database
		*
		* @param Int $ProductId The ID of the product to load variations for. 0 if it's a new product
		* @param String $RefArray The array to store the variation details in
		* @return Void
		*/
		public function _GetVariationData($ProductId = 0, &$RefArray = array())
		{
			if($ProductId == 0) {
				// First, do we even have a variation selected?
				if(isset($_POST['variationId']) && is_numeric($_POST['variationId']) && isset($_POST['options'])) {
					foreach($_POST['options'] as $option_counter => $option) {
						$tmp = array();

						// The combination ID hasn't been assigned yet
						if(isset($option['id'])) {
							$tmp['combinationid'] = $option['id'];
						}
						else {
							$tmp['combinationid'] = 0;
						}

						// The product ID hasn't been assigned yet
						$tmp['vcproductid'] = 0;

						// The variation id
						$tmp['vcvariationid'] = (int)$_POST['variationId'];

						// Is the combination enabled?
						$tmp['vcenabled'] = 0;
						if(isset($option['enabled'])) {
							$tmp['vcenabled'] = 1;
						}

						// The variation option combination
						$ids = preg_replace("/^#/", "", $option['variationcombination']);
						$ids = str_replace("#", ",", $ids);
						$tmp['vcoptionids'] = $ids;

						// The product option's SKU
						$tmp['vcsku'] = $option['sku'];

						// The price difference type
						$tmp['vcpricediff'] = $option['pricediff'];

						// The price difference or fixed price
						$tmp['vcprice'] = DefaultPriceFormat($option['price']);

						// The weight difference type
						$tmp['vcweightdiff'] = $option['weightdiff'];

						// The weight difference or fixed weight
						$tmp['vcweight'] = DefaultDimensionFormat($option['weight']);

						$tmp['vcimage'] = '';
						$tmp['vcimagezoom'] = '';
						$tmp['vcimagestd'] = '';
						$tmp['vcimagethumb'] = '';

						if (isset($_FILES['options']['name'][$option_counter]['image'])) {
							try {
								$image = ISC_PRODUCT_IMAGE::importImage(
									$_FILES['options']['tmp_name'][$option_counter]['image'],
									$_FILES['options']['name'][$option_counter]['image'],
									false,
									false,
									true,
									false
								);

								$tmp['vcimage'] = $image->getSourceFilePath();
								$tmp['vcimagezoom'] = $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, false);
								$tmp['vcimagestd'] = $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, false);
								$tmp['vcimagethumb'] = $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true, false);
							}
							catch (Exception $ex) {
							}
						}
						elseif (isset($option['delimage'])) {
							$tmp['vcimage'] = "REMOVE";
						}

						// The current stock level
						if(isset($option['currentstock'])) {
							$tmp['vcstock'] = (int)$option['currentstock'];
						}
						else {
							$tmp['vcstock'] = 0;
						}

						// The low stock level
						if(isset($option['lowstock'])) {
							$tmp['vclowstock'] = (int)$option['lowstock'];
						}
						else {
							$tmp['vclowstock'] = 0;
						}

						// Push the option to the stack
						array_push($RefArray, $tmp);
					}
				}
			}
		}

		/*
		* _LoadVariationCombinationsTable
		* Create and output the table that contains all combinations of options for a product variation
		*
		* @param Int $VariationId The variation which contains the combinations to load
		* @param Boolean $ShowInventoryFields Whether to include the "Stock Level" and "Low Stock Level" fields in the table
		* @param Int $ProductId The optional ID of the products saved option combinations that should be used to pre-populate the fields
		* @return Void
		*/
		public function _LoadVariationCombinationsTable($VariationId, $ShowInventoryFields, $ProductId=0, $ProductHash='', $filterOptions = array())
		{
			$GLOBALS['HeaderRows'] = "";
			$GLOBALS['VariationRows'] = "";
			$options = array();
			$option_ids = array();
			$i = 0;


			$query = sprintf("SELECT DISTINCT(voname) FROM [|PREFIX|]product_variation_options WHERE vovariationid='%d' ORDER BY vooptionsort, vovaluesort", $VariationId);
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				$GLOBALS['HeaderRows'] .= sprintf("<td>%s</td>", isc_html_escape($row['voname']));
				$options[$row['voname']] = array();
				$option_ids[$row['voname']] = array();
			}

			// Now get all of the variation options
			$query = sprintf("SELECT * FROM [|PREFIX|]product_variation_options WHERE vovariationid='%d' ORDER BY vooptionsort, vovaluesort", $VariationId);
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				$options[$row['voname']][] = $row['vovalue'];
				$option_ids[$row['voname']][] = $row['voptionid'];
			}

			$newOptions = $options;
			$newOptionIds = $option_ids;

			// were filter options submitted?
			if (!empty($filterOptions)) {
				foreach ($option_ids as $optionName => $optionValues) {
					// were values selected for this option?
					if (isset($filterOptions[$optionName])) {
						$currentValues = $filterOptions[$optionName];
						// ignore filtering if the 'All' value was selected
						if (!in_array('all', $currentValues)) {
							// remove any options not in the list
							$diffValues = array_diff($optionValues, $currentValues);
							foreach ($diffValues as $value) {
								$index = array_search($value, $optionValues);
								unset($newOptions[$optionName][$index]);
								unset($newOptionIds[$optionName][$index]);
							}
						}
					}
				}
			}

			// create the form for filtering options
			$filterOptionsHTML = '';
			foreach ($option_ids as $optionName => $optionList) {
				$allSelected = '';
				if (!isset($filterOptions[$optionName]) || in_array('all', $filterOptions[$optionName])) {
					$allSelected = ' selected="selected"';
				}

				$filterOptionsHTML .= '<label>' . isc_html_escape($optionName) . ':</label>';
				$filterOptionsHTML .= '<select multiple="multiple" size="4" name="filterOption[' . isc_html_escape($optionName) . '][]"><option value="all"' . $allSelected . '>(' . GetLang('All') . ')</option>';

				foreach ($optionList as $optionId) {
					$key = array_search($optionId, $optionList);
					$optionValue = $options[$optionName][$key];
					$selected = '';
					if (isset($filterOptions[$optionName]) && in_array($optionId, $filterOptions[$optionName])) {
						$selected = ' selected="selected"';
					}
					$filterOptionsHTML .= '<option value="' . isc_html_escape($optionId) . '"' . $selected . '>' . isc_html_escape($optionValue) . '</option>';
				}

				$filterOptionsHTML .= '</select><br />';
			}

			$GLOBALS['FilterOptions'] = $filterOptionsHTML;

			$page = 0;
			$start = 0;
			$numOptions = 1;
			$numPages = 0;
			$GLOBALS['Nav'] = "";
			$max = 0;



			/*
			$validSortFields = array('productid', 'prodcode', 'currentinv', 'prodname', 'prodcalculatedprice', 'prodvisible', $featuredColumn);

			if(isset($_REQUEST['sortOrder']) && $_REQUEST['sortOrder'] == "asc") {
				$sortOrder = "asc";
			}
			else {
				$sortOrder = "desc";
			}

			if(isset($_REQUEST['sortField']) && in_array($_REQUEST['sortField'], $validSortFields)) {
				$sortField = $_REQUEST['sortField'];
				SaveDefaultSortField("ManageProducts", $_REQUEST['sortField'], $sortOrder);
			} else {
				list($sortField, $sortOrder) = GetDefaultSortField("ManageProducts", "productid", $sortOrder);
			}
			*/

			if(isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			}
			else {
				$page = 1;
			}

			//$sortURL = sprintf("%s&amp;sortField=%s&amp;sortOrder=%s", $searchURL, $sortField, $sortOrder);
			//$GLOBALS['SortURL'] = $sortURL;
			$sortURL = '';

			// Limit the number of questions returned
			if($page == 1) {
				$start = 1;
			}
			else {
				$start = ($page * ISC_PRODUCTS_PER_PAGE) - (ISC_PRODUCTS_PER_PAGE-1);
			}

			$start = $start-1;

			foreach ($newOptions as $option) {
				$numOptions *= count($option);
			}

			$numPages = ceil($numOptions / ISC_PRODUCTS_PER_PAGE);

			$filterQuery = '';
			if (!empty($filterOptions)) {
				$GLOBALS['FilterOptionsQuery'] = http_build_query($filterOptions);

				$queryOptions = array('filterOption' => $filterOptions);
				$filterQuery = "&" . http_build_query($queryOptions);
			}

			// Add the "(Page x of n)" label
			if($numOptions > ISC_PRODUCTS_PER_PAGE) {
				$GLOBALS['Nav'] = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);

				$GLOBALS['Nav'] .= BuildPagination($numOptions, ISC_PRODUCTS_PER_PAGE, $page, "remote.php?w=getVariationCombinations&productId=" . $ProductId . "&productHash=" . $ProductHash . "&v=" . $VariationId . "&inv=" . (int)$ShowInventoryFields . $sortURL . $filterQuery);
			}
			else {
				$GLOBALS['Nav'] = "";
			}

			$GLOBALS['Nav'] = preg_replace('# \|$#',"", $GLOBALS['Nav']);
			//$GLOBALS['SortField'] = $sortField;
			//$GLOBALS['SortOrder'] = $sortOrder;

			// Get the variation combinations as text, such as #red#small#modern
			$GLOBALS["variation_data"] = array();
			$GLOBALS['VariationRows'] = "";
			$this->GetCombinationText('', $newOptions, 0, ISC_PRODUCTS_PER_PAGE, $start);
			$GLOBALS["variation_combinations"] = $GLOBALS["variation_data"];

			// Get the variation combinations ID's, such as #145#185#195
			$GLOBALS["variation_data"] = array();
			$this->GetCombinationText('', $newOptionIds, 0, ISC_PRODUCTS_PER_PAGE, $start);
			$GLOBALS["variation_combination_ids"] = $GLOBALS["variation_data"];

			// Setup a counter
			$count = 0;

			// Loop through the variation combination ID's and output them as hidden fields
			foreach($GLOBALS["variation_combination_ids"] as $k => $combo) {
				$GLOBALS['VariationRows'] .= sprintf("	<input name='options[$count][variationcombination]' type='hidden' value='%s' /></td>", $combo);
				++$count;
			}

			// Reset the counter
			$count = 0;

			// Now loop through all of the options and output the combinations
			if(count($GLOBALS["variation_combinations"]) > 0 && $GLOBALS["variation_combinations"][0] != "") {
				foreach($GLOBALS["variation_combinations"] as $k => $combo) {

					// Set the default values
					$enabled = 'checked="checked"';
					$sku = '';
					$price = '';
					$weight = '';
					$add_p_checked = '';
					$add_w_checked = '';
					$show_price = 'none';
					$show_weight = 'none';
					$fixed_p_checked = '';
					$fixed_w_checked = '';
					$subtract_p_checked = '';
					$subtract_w_checked = '';

					if (isId($ProductId) || $ProductHash !== '') {
						// Get the variation combination's existing details from the product_variation_combinations table
						$combo_ids = preg_replace("/^#/", "", $GLOBALS["variation_combination_ids"][$count]);
						$combo_ids = str_replace("#", ",", $combo_ids);

						$query = "SELECT * FROM [|PREFIX|]product_variation_combinations WHERE vcoptionids='" . $GLOBALS['ISC_CLASS_DB']->Quote($combo_ids) . "' AND ";
						if (empty($ProductHash)) {
							$query .= "vcproductid=" . (int)$ProductId;
						} else {
							$query .= "vcproducthash='" . $GLOBALS['ISC_CLASS_DB']->Quote($ProductHash) . "'";
						}
						$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
						$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

						// Are there any option details?
						if($row !== false) {
							if($row['vcenabled'] == 1) {
								$enabled = 'checked="checked"';
							}
							else {
								$enabled = "";
							}

							$sku = $row['vcsku'];
							$price = '';
							$show_price = 'none';
							$add_p_checked = '';
							$fixed_p_checked = '';
							$subtract_p_checked = '';

							switch($row['vcpricediff']) {
								case "add": {
									$add_p_checked = 'selected="selected"';
									$show_price = "";
									$price = FormatPrice($row['vcprice'], false, false);
									break;
								}
								case "subtract": {
									$subtract_p_checked = 'selected="selected"';
									$show_price = "";
									$price = FormatPrice($row['vcprice'], false, false);
									break;
								}
								case "fixed": {
									$fixed_p_checked = 'selected="selected"';
									$show_price = "";
									$price = FormatPrice($row['vcprice'], false, false);
									break;
								}
							}

							$add_w_checked = '';
							$subtract_w_checked = '';
							$fixed_w_checked = '';
							$show_weight = 'none';
							$weight = '';

							switch($row['vcweightdiff']) {
								case "add": {
									$add_w_checked = 'selected="selected"';
									$show_weight = "";
									$weight = FormatWeight($row['vcweight'], false);
									$show_weight = "";
									break;
								}
								case "subtract": {
									$subtract_w_checked = 'selected="selected"';
									$show_weight = "";
									$weight = FormatWeight($row['vcweight'], false);
									$show_weight = "";
									break;
								}
								case "fixed": {
									$fixed_w_checked = 'selected="selected"';
									$show_weight = "";
									$weight = FormatWeight($row['vcweight'], false);
									$show_weight = "";
									break;
								}
							}
						}
					}

					$GLOBALS['VariationRows'] .= sprintf("<input type='hidden' name='options[$count][id]' value='%d' />", $row['combinationid']);
					$GLOBALS['VariationRows'] .= "<tr class=\"GridRow CombinationRow\">";
					$GLOBALS['VariationRows'] .= sprintf("	<td style='padding-left:4px'><input name='options[$count][enabled]' type='checkbox' %s value='ON' /></td>", $enabled);

					$combo = preg_replace("/^#/", "", $combo);
					$combos = explode("#", $combo);

					foreach($combos as $c) {
						$GLOBALS['VariationRows'] .= sprintf("	<td>%s</td>", isc_html_escape($c));
					}

					$GLOBALS['VariationRows'] .= sprintf("	<td><input name='options[$count][sku]' type='text' class='Field50' value='%s' /></td>", isc_html_escape($sku));

					$GLOBALS['VariationRows'] .= sprintf("	<td>
																<select class='PriceDrop' name='options[$count][pricediff]' onchange=\"if(this.selectedIndex>0) { $(this).parent().find('span').show(); $(this).parent().find('span input').focus(); $(this).parent().find('span input').select(); } else { $(this).parent().find('span').hide(); } \">
																	<option value=''>%s</option>
																	<option %s value='add'>%s</option>
																	<option %s value='subtract'>%s</option>
																	<option %s value='fixed'>%s</option>
																</select>
																<span style='display:%s'>
																	%s <input name='options[$count][price]' type='text' class='Field50 PriceBox' style='width:40px' value='%s' /> %s
																</span>
															</td>", GetLang("NoChange"), $add_p_checked, GetLang("VariationAdd"), $subtract_p_checked, GetLang("VariationSubtract"), $fixed_p_checked, GetLang("VariationFixed"), $show_price, $GLOBALS['CurrencyTokenLeft'], $price, $GLOBALS['CurrencyTokenRight']);

					$GLOBALS['VariationRows'] .= sprintf("	<td>
																<select class='WeightDrop' name='options[$count][weightdiff]' onchange=\"if(this.selectedIndex>0) { $(this).parent().find('span').show(); $(this).parent().find('span input').focus(); $(this).parent().find('span input').select(); } else { $(this).parent().find('span').hide(); } \">
																	<option value=''>%s</option>
																	<option %s value='add'>%s</option>
																	<option %s value='subtract'>%s</option>
																	<option %s value='fixed'>%s</option>
																</select>
																<span style='display:%s'>
																	<input name='options[$count][weight]' type='text' class='Field50 WeightBox' style='width:40px' value='%s' /> %s
																</span>
															</td>", GetLang("NoChange"), $add_w_checked, GetLang("VariationAdd"), $subtract_w_checked, GetLang("VariationSubtract"), $fixed_w_checked, GetLang("VariationFixed"), $show_weight, $weight, GetConfig('WeightMeasurement'));

					$GLOBALS['VariationRows'] .= "	<td><input name='options[$count][image]' type='file' class='Field150 OptionImage' />";

					if($row['vcimage'] != "") {
						$GLOBALS['VariationRows'] .= sprintf("	<br /><input name='options[$count][delimage]' id='variation_delete_image_$count' type='checkbox' value='ON' /> <label for='variation_delete_image_$count'>%s</label> %s <a href='%s' target='_blank'>%s</a>", GetLang("DeleteVariationImage"), GetLang("Currently"), sprintf("%s/%s/%s", $GLOBALS['ShopPath'], GetConfig('ImageDirectory'), $row['vcimage']), $row['vcimage']);
					}

					$GLOBALS['VariationRows'] .= "	</td>";

					// Is inventory tracking enabled for variations?
					if($ShowInventoryFields) {
						$InventoryFieldsHide = "display: auto;";
					}
					else {
						$InventoryFieldsHide = "display: none;";
					}

					$GLOBALS['VariationRows'] .= sprintf("	<td class=\"VariationStockColumn\" style=\"".$InventoryFieldsHide."\"><input name='options[$count][currentstock]' type='text' class='Field50 StockLevel' value='%d' /></td>", $row['vcstock']);
					$GLOBALS['VariationRows'] .= sprintf("	<td class=\"VariationStockColumn\" style=\"".$InventoryFieldsHide."\"><input name='options[$count][lowstock]' type='text' class='Field50 LowStockLevel' value='%d' /></td>", $row['vclowstock']);

					$GLOBALS['VariationRows'] .= "</tr>";
					$count++;
				}
			}

			if(!$ShowInventoryFields) {
				$GLOBALS['HideInv'] = "none";
				$GLOBALS['ColSpan'] = 8;
			}
			else {
				$GLOBALS['ColSpan'] = 10;
			}


			$GLOBALS['VariationId'] = $VariationId;
			$GLOBALS['ShowInv'] = (int)$ShowInventoryFields;
			if (!empty($ProductHash)) {
				$GLOBALS['VProductId'] = '';
				$GLOBALS['VProductHash'] = $ProductHash;
			}
			else {
				$GLOBALS['VProductId'] = $ProductId;
				$GLOBALS['VProductHash'] = '';
			}

			if (!isset($_COOKIE['showVariationFilter']) || $_COOKIE['showVariationFilter'] == 'true') {
				$GLOBALS['ShowFilterChecked'] = 'checked="checked"';
			}
			else {
				$GLOBALS['ShowVariationFilter'] = 'none';
			}


			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("products.variation.combination");
			return $GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate(true);
		}

		/**
		* GetCombinationText
		* Get all possible option combinations and return them as a string of arrays, such as #red#small#retro or #red#small#modern
		*
		* @param String $string The format to arrange combinations in
		* @param String $traits The array of combinations to iterate through
		* @param Int $i The position of the iteration
		* @return Void
		*/
		public function GetCombinationText($string, $traits, $i=0, $limit = 10, $offset = 0, &$counter = 0)
		{
			if (count($GLOBALS["variation_data"]) == $limit) {
				return;
			}

			$keys = array_keys($traits);

			if($i >= count($traits)) {
				$counter++;
				if ($counter > $offset) {
					$GLOBALS["variation_data"][] = trim($string);
				}
			}
			else {
				foreach($traits[$keys[$i]] as $trait) {
					$this->GetCombinationText("$string#$trait", $traits, $i + 1, $limit, $offset, $counter);
				}
			}
		}

		/**
		* GetCombinationIds
		* Get all possible option combinations and return them as an ID of arrays, such as #143#223#154 or #192#121#175
		*
		* @param String $string The format to arrange combinations in
		* @param String $traits The array of combinations to iterate through
		* @param Int $i The position of the iteration
		* @return Void
		*/
		public function SaveCombinations($string, $traits, $productId, $variationId, $saveAsHash = false, $i=0)
		{
			$keys = array_keys($traits);

			if($i >= count($traits)) {
				$optionIds = ltrim($string, ',');
				$prodId = 0;
				$prodHash = '';
				if ($saveAsHash) {
					$prodHash = $productId;
				}
				else {
					$prodId = $productId;
				}
				$newCombo = array(
					"vcproductid" => $prodId,
					"vcproducthash" => $prodHash,
					"vcvariationid" => $variationId,
					"vcenabled" => 1,
					"vcoptionids" => $optionIds,
					"vcsku" => '',
					"vcpricediff" => '',
					"vcprice" => 0,
					"vcweightdiff" => '',
					"vcweight" => 0,
					"vcimage" => '',
					"vcimagezoom" => '',
					"vcimagestd" => '',
					"vcimagethumb" => '',
					"vcstock" => 0,
					"vclowstock" => 0,
					"vclastmodified" => time()
				);
				$GLOBALS['ISC_CLASS_DB']->InsertQuery("product_variation_combinations", $newCombo);
			}
			else {
				foreach($traits[$keys[$i]] as $trait) {
					$this->SaveCombinations("$string,$trait", $traits, $productId, $variationId, $saveAsHash, $i + 1);
				}
			}
		}

		public function CopyProductStep2()
		{
			if($message = strtokenize($_REQUEST, '#')) {
				$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError(GetLang(B('UmVhY2hlZFByb2R1Y3RMaW1pdA==')), $message, MSG_ERROR);
				exit;
			}

			$prodId = (int)$_POST['originalProductId'];

			// Get the information from the form and add it to the database
			$arrData = array();
			$arrCustomFields = array();
			$arrVariations = array();
			$err = "";

			$this->_GetProductData(0, $arrData);
			$this->_GetCustomFieldData(0, $arrCustomFields);
			$this->_GetVariationData(0, $arrVariations);
			$this->_GetProductFieldData(0, $arrProductFields);

			$discount = $this->GetDiscountRulesData(0, true);

			$downloadError = '';
			if (isset($_FILES['newdownload']) && isset($_FILES['newdownload']['tmp_name']) && $_FILES['newdownload']['tmp_name'] != '') {
				if (!$this->SaveProductDownload($downloadError)) {
					$this->CopyProductStep1($downloadError, MSG_ERROR, true, $prodId);
					return;
				}
			}

			// Does a product with the same name already exist?
			$query = "SELECT productid FROM [|PREFIX|]products WHERE prodname='".$GLOBALS['ISC_CLASS_DB']->Quote($arrData['prodname'])."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$existingProduct = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if($existingProduct['productid']) {
				return $this->CopyProductStep1(GetLang('ProductWithSameNameExists'), MSG_ERROR, true, $prodId);
			}

			// Validate out discount rules
			if (!empty($discount) && !$this->ValidateDiscountRulesData($error)) {
				$_POST['currentTab'] = 7;
				$this->CopyProductStep1($error, MSG_ERROR, true, $prodId);
				return;
			}

			//Validate Google Website Optimizer form
			if(isset($_POST['prodEnableOptimizer'])) {
				$optimizer = getClass('ISC_ADMIN_OPTIMIZER');
				$error = $optimizer -> validateConfigForm();
				if($error!='') {
					$_POST['currentTab'] = 8;
					$this->EditProductStep1($error, MSG_ERROR, true);
					return;
				}
			}

			// Commit the values to the database
			if ($this->_CommitProduct(0, $arrData, $arrVariations, $arrCustomFields, $discount, $err, $arrProductFields)) {

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($GLOBALS['NewProductId'], $arrData['prodname']);

				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					// Save the words to the product_words table for search spelling suggestions
					manageSuggestedWordDatabase("product", $GLOBALS['NewProductId'], $arrData['prodname']);
					if(isset($_POST['addanother'])) {
						FlashMessage(GetLang('ProductAddedSuccessfully'), MSG_SUCCESS);
						header("Location: index.php?ToDo=addProduct");
						exit;
					}
					else {
						FlashMessage(GetLang('ProductAddedSuccessfully'), MSG_SUCCESS);
						header("Location: index.php?ToDo=viewProducts");
						exit;
					}
				} else {
					FlashMessage(GetLang('ProductAddedSuccessfully'), MSG_SUCCESS);
					header("Location: index.php");
					exit;
				}
			} else {
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					FlashMessage(sprintf(GetLang('ErrProductNotAdded'), $err), MSG_ERROR);
					header("Location: index.php?ToDo=addProduct");
					exit;
				} else {
					FlashMessage(sprintf(GetLang('ErrProductNotAdded'), $err), MSG_ERROR);
					header("Location: index.php");
					exit;
				}
			}
		}

		public function CopyProductStep1($MsgDesc = "", $MsgStatus = "", $PreservePost=false, $OriginalProductID=0)
		{
			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			// Show the form to edit a product
			if (isset($_REQUEST['productId']) && isId($_REQUEST['productId'])) {
				$OriginalProductID = $_REQUEST['productId'];
			}

			$prodId = $OriginalProductID;
			$z = 0;
			$arrData = array();
			$arrCustomFields = array();

			if (GetConfig('CurrencyLocation') == 'right') {
				$GLOBALS['CurrencyTokenLeft'] = '';
				$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
			} else {
				$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
				$GLOBALS['CurrencyTokenRight'] = '';
			}

			$GLOBALS['ServerFiles'] = $this->_GetImportFilesOptions();

			$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');

			// Make sure the product exists
			if (ProductExists($prodId)) {

				if($PreservePost == true) {
					$this->_GetProductData(0, $arrData);
					$this->_GetCustomFieldData(0, $arrCustomFields);
					$GLOBALS['ProductFields'] = $this->_GetProductFieldsLayout(0, true);

					// Restore the hash
					$GLOBALS['ProductHash'] = $arrData['prodhash'];
				} else {
					$this->_GetProductData($prodId, $arrData);
					$this->_GetCustomFieldData($prodId, $arrCustomFields);
					$GLOBALS['ProductFields'] = $this->_GetProductFieldsLayout($prodId, true);

					// Generate the hash
					$GLOBALS['ProductHash'] = md5(time().uniqid(rand(), true));

					// We'll need to duplicate (copy) the thumbnail, images and download files here
					$this->_CopyDownloads($prodId, 0, $GLOBALS['ProductHash']);
					$productImages = ISC_PRODUCT_IMAGE::copyImagesToProductHash($prodId, $GLOBALS['ProductHash']);
					$this->setupProductImageGlobals($productImages);

					$arrData['prodname'] = GetLang('CopyOf') . $arrData['prodname'];
				}

				// Does this user have permission to edit this product?
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $arrData['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewProducts');
				}

				if(isset($_POST['currentTab'])) {
					$GLOBALS['CurrentTab'] = (int)$_POST['currentTab'];
				}
				else {
					$GLOBALS['CurrentTab'] = 0;
				}

				$GLOBALS['FormAction'] = 'copyProduct2';
				$GLOBALS['Title'] = GetLang('CopyProductTitle');
				$GLOBALS['Intro'] = GetLang('CopyProductIntro');
				$GLOBALS["ProdType_" . $arrData['prodtype']] = 'checked="checked"';
				$GLOBALS['ProdType'] = $arrData['prodtype'] - 1;
				$GLOBALS['ProdCode'] = isc_html_escape($arrData['prodcode']);
				$GLOBALS['ProdName'] = isc_html_escape($arrData['prodname']);
				$GLOBALS['OriginalProductId'] = $OriginalProductID;

				$visibleCategories = array();
				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					if($vendorData['vendoraccesscats']) {
						$visibleCategories = explode(',', $vendorData['vendoraccesscats']);
					}
				}
				$GLOBALS['CategoryOptions'] = $GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->GetCategoryOptions($arrData['prodcats'], "<option %s value='%d'>%s</option>", "selected='selected'", "", false, '', $visibleCategories);
				$GLOBALS['RelatedCategoryOptions'] = $GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->GetCategoryOptions(0, "<option %s value='%d'>%s</option>", "selected='selected'", "- ", false);

				$wysiwygOptions = array(
					'id'		=> 'wysiwyg',
					'width'		=> '100%',
					'height'	=> '500px',
					'value'		=> $arrData['proddesc']
				);
				$GLOBALS['WYSIWYG'] = GetClass('ISC_ADMIN_EDITOR')->GetWysiwygEditor($wysiwygOptions);

				$GLOBALS['ProdSearchKeywords'] = isc_html_escape($arrData['prodsearchkeywords']);
				$GLOBALS['ProdAvailability'] = isc_html_escape($arrData['prodavailability']);
				$GLOBALS['ProdPrice'] = number_format($arrData['prodprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");

				if (CFloat($arrData['prodcostprice']) > 0) {
					$GLOBALS['ProdCostPrice'] = number_format($arrData['prodcostprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodretailprice']) > 0) {
					$GLOBALS['ProdRetailPrice'] = number_format($arrData['prodretailprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodsaleprice']) > 0) {
					$GLOBALS['ProdSalePrice'] = number_format($arrData['prodsaleprice'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				$GLOBALS['ProdSortOrder'] = $arrData['prodsortorder'];

				if ($arrData['prodvisible'] == 1) {
					$GLOBALS['ProdVisible'] = "checked";
				}

				if ($arrData['prodfeatured'] == 1) {
					$GLOBALS['ProdFeatured'] = "checked";
				}

				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					$GLOBALS['HideStoreFeatured'] = 'display: none';
				}
				else if(!gzte11(ISC_HUGEPRINT) || !$arrData['prodvendorid']) {
					$GLOBALS['HideVendorFeatured'] = 'display: none';
				}

				if($arrData['prodvendorfeatured'] == 1) {
					$GLOBALS['ProdVendorFeatured'] = 'checked="checked"';
				}

				if($arrData['prodistaxable'] == 1) {
					$GLOBALS['ProdIsTaxable'] = 'checked';
				}

				if($arrData['prodallowpurchases'] == 1) {
					$GLOBALS['ProdAllowPurchases'] = 'checked="checked"';
				}
				else {
					if($arrData['prodhideprice'] == 1) {
						$GLOBALS['ProdHidePrice'] = 'checked="checked"';
					}
					$GLOBALS['ProdCallForPricingLabel'] = isc_html_escape($arrData['prodcallforpricinglabel']);
				}

				$GLOBALS['ProdWarranty'] = $arrData['prodwarranty'];
				$GLOBALS['ProdWeight'] = number_format($arrData['prodweight'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");

				if (CFloat($arrData['prodwidth']) > 0) {
					$GLOBALS['ProdWidth'] = number_format($arrData['prodwidth'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodheight']) > 0) {
					$GLOBALS['ProdHeight'] = number_format($arrData['prodheight'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['proddepth']) > 0) {
					$GLOBALS['ProdDepth'] = number_format($arrData['proddepth'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if (CFloat($arrData['prodfixedshippingcost']) > 0) {
					$GLOBALS['ProdFixedShippingCost'] = number_format($arrData['prodfixedshippingcost'], GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
				}

				if ($arrData['prodfreeshipping'] == 1) {
					$GLOBALS['FreeShipping'] = 'checked="checked"';
				}

				if($arrData['prodrelatedproducts'] == -1) {
					$GLOBALS['IsProdRelatedAuto'] = 'checked="checked"';
				}
				else if(isset($arrData['prodrelated'])) {
					$GLOBALS['RelatedProductOptions'] = "";

					foreach ($arrData['prodrelated'] as $r) {
						$GLOBALS['RelatedProductOptions'] .= sprintf("<option value='%d'>%s</option>", (int) $r[0], isc_html_escape($r[1]));
					}
				}

				$GLOBALS['ProdTags'] = $arrData['prodtags'];

				$GLOBALS['CurrentStockLevel'] = $arrData['prodcurrentinv'];
				$GLOBALS['LowStockLevel'] = $arrData['prodlowinv'];
				$GLOBALS["InvTrack_" . $arrData['prodinvtrack']] = 'checked="checked"';

				$GLOBALS['WrappingOptions'] = $this->BuildGiftWrappingSelect(explode(',', $arrData['prodwrapoptions']));
				$GLOBALS['HideGiftWrappingOptions'] = 'display: none';
				if($arrData['prodwrapoptions'] == 0) {
					$GLOBALS['WrappingOptionsDefaultChecked'] = 'checked="checked"';
				}
				else if($arrData['prodwrapoptions'] == -1) {
					$GLOBALS['WrappingOptionsNoneChecked'] = 'checked="checked"';
				}
				else {
					$GLOBALS['HideGiftWrappingOptions'] = '';
					$GLOBALS['WrappingOptionsCustomChecked'] = 'checked="checked"';
				}

				if ($arrData['prodinvtrack'] == 1) {
					$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(true);";
				} else {
					$GLOBALS['OptionButtons'] = "ToggleProductInventoryOptions(false);";
				}

				if ($arrData['prodoptionsrequired'] == 1) {
					$GLOBALS['OptionsRequired'] = 'checked="checked"';
				}

				if ($arrData['prodtype'] == 1) {
					$GLOBALS['HideProductInventoryOptions'] = "none";
				}

				$GLOBALS['EnterOptionPrice'] = sprintf(GetLang('EnterOptionPrice'), GetConfig('CurrencyToken'), GetConfig('CurrencyToken'));
				$GLOBALS['EnterOptionWeight'] = sprintf(GetLang('EnterOptionWeight'), GetConfig('WeightMeasurement'));
				$GLOBALS['HideCustomFieldLink'] = "none";

				if (GetConfig('PricesIncludeTax')) {
					$GLOBALS['PriceMsg'] = GetLang('IncTax');
				} else {
					$GLOBALS['PriceMsg'] = GetLang('ExTax');
				}

				$GLOBALS['CustomFields'] = '';
				$GLOBALS['CustomFieldKey'] = 0;

				if (!empty($arrCustomFields)) {
					foreach ($arrCustomFields as $f) {
						$GLOBALS['CustomFieldName'] = isc_html_escape($f['name']);
						$GLOBALS['CustomFieldValue'] = isc_html_escape($f['value']);
						$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));

						if (!$GLOBALS['CustomFieldKey']) {
							$GLOBALS['HideCustomFieldDelete'] = 'none';
						} else {
							$GLOBALS['HideCustomFieldDelete'] = '';
						}

						$GLOBALS['CustomFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

						$GLOBALS['CustomFieldKey']++;
					}
				}

				// Add one more custom field
				$GLOBALS['CustomFieldName'] = '';
				$GLOBALS['CustomFieldValue'] = '';
				$GLOBALS['CustomFieldLabel'] = $this->GetFieldLabel(($GLOBALS['CustomFieldKey']+1), GetLang('CustomField'));

				if (!$GLOBALS['CustomFieldKey']) {
					$GLOBALS['HideCustomFieldDelete'] = 'none';
				} else {
					$GLOBALS['HideCustomFieldDelete'] = '';
				}

				$GLOBALS['CustomFields'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');

				// Get a list of any downloads associated with this product
				$GLOBALS['DownloadsGrid'] = $this->GetDownloadsGrid(0, $GLOBALS['ProductHash']);
				$GLOBALS['ISC_LANG']['MaxUploadSize'] = sprintf(GetLang('MaxUploadSize'), GetMaxUploadSize());
				if($GLOBALS['DownloadsGrid'] == '') {
					$GLOBALS['DisplayDownloaadGrid'] = "none";
				}

				// Get the brands as select options
				$GLOBALS['ISC_CLASS_ADMIN_BRANDS'] = GetClass('ISC_ADMIN_BRANDS');
				$GLOBALS['BrandNameOptions'] = $GLOBALS['ISC_CLASS_ADMIN_BRANDS']->GetBrandsAsOptions($arrData['prodbrandid']);
				$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndAddAnother');

				// Get a list of all layout files
				$layoutFile = 'product.html';
				if($arrData['prodlayoutfile'] != '') {
					$layoutFile = $arrData['prodlayoutfile'];
				}
				$GLOBALS['LayoutFiles'] = GetCustomLayoutFilesAsOptions("product.html", $layoutFile);

				$GLOBALS['ProdPageTitle'] = isc_html_escape($arrData['prodpagetitle']);
				$GLOBALS['ProdMetaKeywords'] = isc_html_escape($arrData['prodmetakeywords']);
				$GLOBALS['ProdMetaDesc'] = isc_html_escape($arrData['prodmetadesc']);
				$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndAddAnother');

				if(!gzte11(ISC_MEDIUMPRINT)) {
					$GLOBALS['HideInventoryOptions'] = "none";
				}
				else {
					$GLOBALS['HideInventoryOptions'] = '';
				}

				// Does this product have a variation assigned to it?
				$GLOBALS['ProductVariationExisting'] = $arrData['prodvariationid'];

				if($arrData['prodvariationid'] > 0) {
					$GLOBALS['IsYesVariation'] = 'checked="checked"';
				}
				else {
					$GLOBALS['IsNoVariation'] = 'checked="checked"';
					$GLOBALS['HideVariationList'] = "none";
					$GLOBALS['HideVariationCombinationList'] = "none";
				}

				// If there are no variations then disable the option to choose one
				$numVariations = 0;
				$GLOBALS['VariationOptions'] = $this->GetVariationsAsOptions($numVariations, $arrData['prodvariationid']);

				if($numVariations == 0) {
					$GLOBALS['VariationDisabled'] = "DISABLED";
					$GLOBALS['VariationColor'] = "#CACACA";
					$GLOBALS['IsNoVariation'] = 'checked="checked"';
					$GLOBALS['IsYesVariation'] = "";
					$GLOBALS['HideVariationCombinationList'] = "none";
				}
				else {
					// Load the variation combinations
					if($arrData['prodinvtrack'] == 2) {
						$show_inv_fields = true;
					}
					else {
						$show_inv_fields = false;
					}

					/**
					 * We'll need to duplicate the variation combinations here if we are NOT preserving the post
					 */
					if (!$PreservePost) {
						$this->_CopyVariationData($arrData['productid'], 0, $GLOBALS['ProductHash']);
					}

					$GLOBALS['VariationCombinationList'] = $this->_LoadVariationCombinationsTable($arrData['prodvariationid'], $show_inv_fields, 0, $GLOBALS['ProductHash']);
				}

				if(!gzte11(ISC_HUGEPRINT)) {
					$GLOBALS['HideVendorOption'] = 'display: none';
				}
				else {
					$vendorData = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendor();
					if(isset($vendorData['vendorid'])) {
						$GLOBALS['HideVendorSelect'] = 'display: none';
						$GLOBALS['CurrentVendor'] = isc_html_escape($vendorData['vendorname']);
					}
					else {
						$GLOBALS['HideVendorLabel'] = 'display: none';
						$GLOBALS['VendorList'] = $this->BuildVendorSelect($arrData['prodvendorid']);
					}
				}

				// Display the discount rules
				if ($PreservePost == true) {
					$GLOBALS['DiscountRules'] = $this->GetDiscountRules(0);
				} else {
					$GLOBALS['DiscountRules'] = $this->GetDiscountRules($prodId);
				}


				// Hide if we are not enabled
				if (!GetConfig('BulkDiscountEnabled')) {
					$GLOBALS['HideDiscountRulesWarningBox'] = '';
					$GLOBALS['DiscountRulesWarningText'] = GetLang('DiscountRulesNotEnabledWarning');
					$GLOBALS['DiscountRulesWithWarning'] = 'none';

				// Also hide it if this product has variations
				} else if (isset($arrData['prodvariationid']) && isId($arrData['prodvariationid'])) {
					$GLOBALS['HideDiscountRulesWarningBox'] = '';
					$GLOBALS['DiscountRulesWarningText'] = GetLang('DiscountRulesVariationWarning');
					$GLOBALS['DiscountRulesWithWarning'] = 'none';
				} else {
					$GLOBALS['HideDiscountRulesWarningBox'] = 'none';
					$GLOBALS['DiscountRulesWithWarning'] = '';
				}

				$GLOBALS['DiscountRulesEnabled'] = (int)GetConfig('BulkDiscountEnabled');

				$GLOBALS['EventDateFieldName'] = $arrData['prodeventdatefieldname'];

				if ($GLOBALS['EventDateFieldName'] == null) {
					$GLOBALS['EventDateFieldName'] = GetLang('EventDateDefault');
				}

				if ($arrData['prodeventdaterequired'] == 1) {
					$GLOBALS['EventDateRequired'] = 'checked="checked"';
					$from_stamp = $arrData['prodeventdatelimitedstartdate'];
					$to_stamp = $arrData['prodeventdatelimitedenddate'];
				} else {
					$from_stamp = isc_gmmktime(0, 0, 0, isc_date("m"), isc_date("d"), isc_date("Y"));
					$to_stamp = isc_gmmktime(0, 0, 0, isc_date("m")+1, isc_date("d"), isc_date("Y"));
				}
				if ($arrData['prodeventdatelimited'] == 1) {
					$GLOBALS['LimitDates'] = 'checked="checked"';
				}

				$GLOBALS['LimitDateOption1'] = '';
				$GLOBALS['LimitDateOption2'] = '';
				$GLOBALS['LimitDateOption3'] = '';

				switch ($arrData['prodeventdatelimitedtype']) {

					case 1 :
						$GLOBALS['LimitDateOption1'] = 'selected="selected"';
					break;
					case 2 :
						$GLOBALS['LimitDateOption2'] = 'selected="selected"';
					break;
					case 3 :
						$GLOBALS['LimitDateOption3'] = 'selected="selected"';
					break;
				}

				// Set the global variables for the select boxes

				$from_day = isc_date("d", $from_stamp);
				$from_month = isc_date("m", $from_stamp);
				$from_year = isc_date("Y", $from_stamp);

				$to_day = isc_date("d", $to_stamp);
				$to_month = isc_date("m", $to_stamp);
				$to_year = isc_date("Y", $to_stamp);

				$GLOBALS['OverviewFromDays'] = $this->_GetDayOptions($from_day);
				$GLOBALS['OverviewFromMonths'] = $this->_GetMonthOptions($from_month);
				$GLOBALS['OverviewFromYears'] = $this->_GetYearOptions($from_year);

				$GLOBALS['OverviewToDays'] = $this->_GetDayOptions($to_day);
				$GLOBALS['OverviewToMonths'] = $this->_GetMonthOptions($to_month);
				$GLOBALS['OverviewToYears'] = $this->_GetYearOptions($to_year);

				if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Category)) {
					$GLOBALS['HideCategoryCreation'] = 'display: none';
				}

				//Google website optimizer
				$GLOBALS['HideOptimizerConfigForm'] = 'display:none;';
				$GLOBALS['CheckEnableOptimizer'] = '';
				$GLOBALS['SkipConfirmMsg'] = 'false';
				$GLOBALS['GoogleWebsiteOptimizerIntro'] = GetLang('ProdGoogleWebsiteOptimizerIntro');

				$enabledOptimizers = GetConfig('OptimizerMethods');
				if(!empty($enabledOptimizers)) {
					foreach ($enabledOptimizers as $id => $date) {
						GetModuleById('optimizer', $optimizerModule, $id);
						if ($optimizerModule->_testPage == 'products' || $optimizerModule->_testPage == 'all') {
							$GLOBALS['SkipConfirmMsg'] = 'false';
							break;
						}
					}
				}
				if($arrData['product_enable_optimizer'] == '1') {
					$GLOBALS['HideOptimizerConfigForm'] = '';
					$GLOBALS['CheckEnableOptimizer'] = 'Checked';
				}

				$optimizer = getClass('ISC_ADMIN_OPTIMIZER');
				$GLOBALS['OptimizerConfigForm'] = $optimizer->showPerItemConfigForm('product', $arrData['productid'],prodLink($arrData['prodname']));

				$GLOBALS['SaveAndAddAnother'] = GetLang('SaveAndAddAnother');
				$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("product.form");
				$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
			} else {
				// The product doesn't exist
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Products)) {
					$this->ManageProducts(GetLang('ProductDoesntExist'), MSG_ERROR);
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}
			}
		}

		/**
		* copy a product image to another random directory
		*
		* @param string $imagePath, the path to the origin image
		*
		* return string path to the new image
		*/
		public function _CopyImages($imagePath, $dir = '')
		{
			//check if the original file exist
			if (!is_file($imagePath)) {
				return '';
			}

			if($dir == '') {
				$dir = GetConfig('ImageDirectory');
			}

			$dest = realpath(ISC_BASE_PATH."/" . $dir);
			$randomDir = strtolower(chr(rand(65, 90)));
			if(!is_dir("../".$dir."/".$randomDir)) {
				if(!@mkdir("../".$dir."/".$randomDir, ISC_WRITEABLE_DIR_PERM)) {
					$randomDir = '';
				}
			}

			$fileName = preg_replace('/^.*\//', '', $imagePath);

			//check is filename exsits in the dest directory, rename file name if exsits
			if (file_exists($dest.$randomDir.'/'.$fileName)) {
				$fileName = basename($randomFileName);
				$fileName = substr_replace($randomFileName, "-".rand(0, 10000000000), strrpos($randomFileName, "."), 0);
			}
			$newPath = $dest.'/'.$randomDir.'/'.$fileName;

			//cppy file to new directory
			if (copy($imagePath, $newPath)) {
				return $randomDir.'/'.$fileName;
			} else {
				return '';
			}
		}

		public function _CopyDownloads($fromProdctId, $toProductId=0, $toProductHash='')
		{
			$total = 0;
			$imgDir = realpath(ISC_BASE_PATH."/" . GetConfig('DownloadDirectory'));

			$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]product_downloads WHERE productid='" . $GLOBALS['ISC_CLASS_DB']->Quote($fromProdctId) . "'");

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

				if (($downfile = $this->_CopyImages($imgDir . '/' . $row['downfile'], GetConfig('DownloadDirectory'))) == '') {
					continue;
				}
				$newDownload = array(
					"downfile" => $downfile,
					"downdateadded" => time(),
					"downmaxdownloads" => (int)$row['downmaxdownloads'],
					"downexpiresafter" => (int)$row['downexpiresafter'],
					"downname" => $row['downname'],
					"downfilesize" => (int)$row['downfilesize'],
					"downdescription" => $row['downdescription']
				);

				if (isId($toProductId)) {
					$newDownload['productid'] = $toProductId;
					$newDownload['prodhash'] = '';
				} else {
					$newDownload['productid'] = '0';
					$newDownload['prodhash'] = $toProductHash;
				}

				if ($GLOBALS['ISC_CLASS_DB']->InsertQuery("product_downloads", $newDownload)) {
					$total++;
				}
			}

			return $total;
		}

		/**
		 * Build a list of vendors that can be chosen for a product.
		 *
		 * @param int The vendor ID to select, if any.
		 * @return string The HTML options for the select box of vendors.
		 */
		private function BuildVendorSelect($selectedVendor=0)
		{
			$options = '<option value="0">'.GetLang('ProductNoVendor').'</option>';
			$query = "
				SELECT vendorid, vendorname
				FROM [|PREFIX|]vendors
				ORDER BY vendorname ASC
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($vendor = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$sel = '';
				if($selectedVendor == $vendor['vendorid']) {
					$sel = 'selected="selected"';
				}
				$options .= '<option value='.(int)$vendor['vendorid'].' '.$sel.'>'.isc_html_escape($vendor['vendorname']).'</option>';
			}
			return $options;
		}

		/**
		 * Build a list of gift wrapping options to select per product.
		 *
		 * @param array An array of gift wrapping options that should be selected.
		 * @return string The HTML options for the select box of gift wrapping options.
		 */
		private function BuildGiftWrappingSelect($selected=array())
		{
			$query = "
				SELECT wrapname, wrapprice, wrapid
				FROM [|PREFIX|]gift_wrapping
				ORDER BY wrapname ASC
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$options = '';
			while($wrap = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$sel = '';
				if(in_array($wrap['wrapid'], $selected)) {
					$sel = 'selected="selected"';
				}
				$options .= '<option value='.(int)$wrap['wrapid'].' '.$sel.'>'.isc_html_escape($wrap['wrapname']).'</option>';
			}
			return $options;
		}

		/**
		 * Save the tags a product has been tagged with in the database.
		 *
		 * @param string A CSV list of tags to be associated with the product.
		 * @param int The product ID to associate the tags with.
		 * @param boolean True if this is a new product, false if not (new products mean we don't need to delete existing tags etc)
		 * @return boolean True if successful, false if not.
		 */
		public function SaveProductTags($tags, $productId, $newProduct=false)
		{
			// Split up the tags and make them unique
			$tags = explode(',', $tags);
			foreach($tags as $k => $tag) {
				if(!trim($tag) || isc_strlen($tag) == 2) {
					unset($tags[$k]);
					continue;
				}
				$tags[$k] = trim($tags[$k]);
			}

			// No tags & away we go!
			if($newProduct && empty($tags)) {
				return false;
			}

			$uniqueTags = array_unique(array_map('isc_strtolower', $tags));
			$tagList = array();
			foreach(array_keys($uniqueTags) as $k) {
				$tagList[] = trim($tags[$k]);
			}
			$uniqueTags = array_values($uniqueTags);

			// Get a list of the tags that this product already has
			$existingTags = array();
			$productTagIds = array();

			if($newProduct == false) {
				$query = "
					SELECT a.tagid, t.tagname, t.tagcount
					FROM [|PREFIX|]product_tagassociations a
					INNER JOIN [|PREFIX|]product_tags t ON (t.tagid=a.tagid)
					WHERE a.productid='".(int)$productId."'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while($tag = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$existingTags[$tag['tagid']] = $tag;
				}
			}

			// Now attempt to establish which of these tags already exist and which we need to create
			$query = "
				SELECT tagid, tagname
				FROM [|PREFIX|]product_tags
				WHERE tagname IN ('".implode("','", array_map(array($GLOBALS['ISC_CLASS_DB'], 'Quote'), $tagList))."')
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while($tag = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				// This tag exists but the product doesn't have it already, so we need to tag it
				$productTagIds[] = $tag['tagid'];

				if(!isset($existingTags[$tag['tagid']])) {
					$tagsToMark[] = $tag['tagid'];
				}

				// Remove the tag from the list of what we need to create
				$keyId = array_search(strtolower($tag['tagname']), $uniqueTags);
				if($keyId !== false) {
					unset($tagList[$keyId], $uniqueTags[$keyId]);
				}
			}

			// What's left in the array is now what we need to create, so go ahead and create those tags
			foreach($tagList as $tag) {
				$tagId = $this->CreateProductTag($tag);
				$productTagIds[] = $tagId;
				$tagsToMark[] = $tagId;
			}

			// Update the tag count for all of the tags - so now that current + 1 products have this tag
			if(!empty($tagsToMark)) {
				$query = "
					UPDATE [|PREFIX|]product_tags
					SET tagcount=tagcount+1
					WHERE tagid IN (".implode(',', $tagsToMark).")
				";
				$GLOBALS['ISC_CLASS_DB']->Query($query);
			}

			// Now delete any tag associations
			if($newProduct == false) {
				$deletedTags = array_diff(array_keys($existingTags), $productTagIds);
				if(!empty($deletedTags)) {
					$GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_tagassociations', "WHERE tagid IN (".implode(',', $deletedTags).") AND productid='".(int)$productId."'");

					// Delete any existing tags where they were only previously associated with one product, as now they're associated with 0
					$GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_tags', "WHERE tagid IN (".implode(',', $deletedTags).") AND tagcount=1");

					$query = "
						UPDATE [|PREFIX|]product_tags
						SET tagcount=tagcount-1
						WHERE tagid IN (".implode(',', $deletedTags).")
					";
					$GLOBALS['ISC_CLASS_DB']->Query($query);
				}
			}

			// And finally, insert all of the new tag associations
			$insertValues = '';
			if(!empty($tagsToMark)) {
				foreach($tagsToMark as $tagId) {
					$insertValues .= "('".$tagId."', '".$productId."'), ";
				}
				$insertValues = rtrim($insertValues, ', ');
				$GLOBALS['ISC_CLASS_DB']->Query("
					INSERT INTO [|PREFIX|]product_tagassociations
					(tagid, productid)
					VALUES
					".$insertValues
				);
			}

			return true;
		}

		/**
		 * Create a product tag with a unique "friendly name" in the database.
		 *
		 * @param string The name of the tag to create.
		 * @return int The ID of the tag we've just created.
		 */
		private function CreateProductTag($tag)
		{
			$friendlyName = isc_strtolower(trim($tag));
			$friendlyName = preg_replace("#\s#", "-", $friendlyName);
			$friendlyName = preg_replace("#([^a-zA-Z0-9-_])#", "", $friendlyName);
			$friendlyName = preg_replace("#\-{2,}#", '', $friendlyName);

			// If a friendly name couldn't be generated then we store the tag ID as the friendly name.
			if(!$friendlyName) {
				$newTag = array(
					'tagname' => $tag
				);
				$tagId = $GLOBALS['ISC_CLASS_DB']->InsertQuery('product_tags', $newTag);
				$updatedTag = array(
					'tagfriendlyname' => $tagId
				);
				$GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_tags', $updatedTag, "tagid='".(int)$tagId."'");
				return $tagId;
			}
			// Otherwise, generate a friendly ID
			else {
				$friendlyCount = 0;
				$currentFriendlyName = $friendlyName;
				do {
					$query = "
						SELECT tagid
						FROM [|PREFIX|]product_tags
						WHERE tagfriendlyname='".$GLOBALS['ISC_CLASS_DB']->Quote($currentFriendlyName)."'
					";
					$exists = $GLOBALS['ISC_CLASS_DB']->FetchOne($query);
					if($exists) {
						++$friendlyCount;
						$currentFriendlyName = $friendlyName.$friendlyCount;
					}
					// Found a place, insert and then get out asap!
					else {
						$newTag = array(
							'tagname' => $tag,
							'tagfriendlyname' => $currentFriendlyName
						);
						$tagId = $GLOBALS['ISC_CLASS_DB']->InsertQuery('product_tags', $newTag);
						return $tagId;
					}
				} while($exists);
			}
		}

		/**
		 * Download a particular digital download attached to a product.
		 */
		public function DownloadProductFile()
		{
			if(!$GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
				FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php');
			}

			$query = "
				SELECT *
				FROM [|PREFIX|]product_downloads
				WHERE downloadid='".(int)$_REQUEST['downloadid']."'
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$download = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
			if(!$download) {
				FlashMessage(GetLang('ProductDoesntExist'), MSG_ERROR, 'index.php?ToDo=viewProducts');
			}

			// If they're downloading a file for the product they're currently creating, don't check if the
			// product exists, as it won't.
			if($download['productid']) {
				$query = "
					SELECT productid, prodvendorid
					FROM [|PREFIX|]products
					WHERE productid='".$download['productid']."'
				";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$product = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
				if(!$product) {
					FlashMessage(GetLang('ProductDoesntExist'), MSG_ERROR, 'index.php?ToDo=viewProducts');
				}

				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $product['prodvendorid'] != $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
					FlashMessage(GetLang('Unauthorized'), MSG_ERROR, 'index.php?ToDo=viewProducts');
				}
			}

			$filepath = realpath(ISC_BASE_PATH.'/'.GetConfig('DownloadDirectory')).'/'.$download['downfile'];
			DownloadFile($filepath);
			exit;
		}

		public function DeleteTemporaryCombinations()
		{
			$time = time() - 86400;

			// get the images for the combinations
			$query = "
				SELECT
					vcimage,
					vcimagezoom,
					vcimagestd,
					vcimagethumb
				FROM
					`[|PREFIX|]product_variation_combinations`
				WHERE
					vcproductid = 0 AND
					vcimage != '' AND
					vclastmodified < " . $time;

			$res = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($res)) {
				$this->DeleteVariationImagesForRow($row);
			}

			// delete the combinations
			$GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_variation_combinations', 'WHERE vcproductid = AND vclastmodified < ' . $time);
		}

		public function DeleteTemporaryCombinationsForProduct($productHash, $variationId)
		{
			// Delete the product combination images from the file system
			$query = '
				SELECT
					vcimage,
					vcimagezoom,
					vcimagestd,
					vcimagethumb
				FROM
					[|PREFIX|]product_variation_combinations
				WHERE
					vcproducthash = "' . $GLOBALS['ISC_CLASS_DB']->Quote($productHash) . '" AND
					vcvariationid != ' . $variationId;

			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				$this->DeleteVariationImagesForRow($row);
			}

			$GLOBALS['ISC_CLASS_DB']->DeleteQuery('product_variation_combinations', 'WHERE vcproducthash = "' . $GLOBALS['ISC_CLASS_DB']->Quote($productHash) . '" AND vcvariationid != ' . $variationId);
		}

		/**
		* Deletes images associated with a variation combination given a row of combination data
		*
		* @param array $row The combination data
		*/
		public function DeleteVariationImagesForRow($row)
		{
			if (!empty($row['vcimage'])) {
				@unlink(ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/' . $row['vcimage']);
			}

			if (!empty($row['vcimagezoom'])) {
				@unlink(ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/' . $row['vcimagezoom']);
			}

			if (!empty($row['vcimagestd'])) {
				@unlink(ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/' . $row['vcimagestd']);
			}

			if (!empty($row['vcimagethumb'])) {
				@unlink(ISC_BASE_PATH . '/' . GetConfig('ImageDirectory') . '/' . $row['vcimagethumb']);
			}
		}
	}