<?php

	/**
	* This is the SEO Indexing module for Interspire Shopping Cart. To enable
	* this module in Interspire Shopping Cart login to the control panel and click the
	* Settings -> Analytics Settings tab in the menu.
	*/
	class ANALYTICS_SEOINDEXING extends ISC_ANALYTICS
	{

		/*
			Analytics class constructor
		*/
		public function __construct()
		{

			// Setup the required variables for the SEO module
			parent::__construct();

			$this->_name = GetLang('SEOIndexingName');
			$this->_image = "scs_seo_indexing.png";
			$this->_description = GetLang('SEOIndexingDesc');
			$this->_help = sprintf(GetLang('SEOIndexingHelp'), $GLOBALS['ShopPath'], $GLOBALS['StoreName']);
			$this->_height = 0;
		}

		/**
		* Custom variables for the analytics module. Custom variables are stored in the following format:
		* array(variable_id, variable_name, variable_type, help_text, default_value, required, [variable_options], [multi_select], [multi_select_height])
		* variable_type types are: text,number,password,radio,dropdown
		* variable_options is used when the variable type is radio or dropdown and is a name/value array.
		*/
		public function SetCustomVars()
		{
			$this->_variables['homeindexing'] = array(
				"name" => "Apply Canonical URL to Shop Home",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingCanonicalCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingHomeNo') => "NO",
					GetLang('SEOIndexingHomeYes') => "YES"
				),
				"multiselect" => false
			);
			$this->_variables['productindexing'] = array(
				"name" => "Apply Canonical URL to Products",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingCanonicalCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingProductsNo') => "NO",
					GetLang('SEOIndexingProductsYes') => "YES"
				),
				"multiselect" => false
			);
			$this->_variables['categoryindexing'] = array(
				"name" => "Apply Canonical URL to Categories",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingCanonicalCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingCategoryNo') => "NO",
					GetLang('SEOIndexingCategoryYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['brandindexing'] = array(
				"name" => "Apply Canonical URL to Brands",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingCanonicalCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingBrandNo') => "NO",
					GetLang('SEOIndexingBrandYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['pagesindexing'] = array(
				"name" => "Apply Canonical URL to Pages",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingCanonicalCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingPagesNo') => "NO",
					GetLang('SEOIndexingPagesYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['httpsindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to HTTPS area",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['shopbypriceindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Shop by Price",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['createaccountindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Create Account",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['cartindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Shopping Cart",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['giftcertindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Gift Certificate",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['loginindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Customer Login",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['searchindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Search Page",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['tagsindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Product Tags",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);

			$this->_variables['wishlistindexing'] = array(
				"name" => "Apply NOINDEX, FOLLOW to Wishlist",
				"type" => "dropdown",
				"help" => GetLang('SEOIndexingTrackingCodeHelp'),
				"default" => "no",
				"required" => true,
				"options" => array(
					GetLang('SEOIndexingNoindexFollowNo') => "NO",
					GetLang('SEOIndexingNoindexFollowYes') => "YES"
				),
				"multiselect" => false
			);
		}

		/**
		* Return the tracking code for this analytics module.
		*/
		public function GetTrackingCode()
		{
			$trackingCode = '';

			// If we're on a secure server, make sure we're loading the tracking code for the secure server too
			/*if(isset($_GET['sort'])) {
				$trackingCode = '<link rel="canonical" href="'.$GLOBALS['ShopPath'].'/'.$GLOBALS['URL'].'" />';
			} else {
				$trackingCode = '';
			}*/

			$canonical_brand = $this->GetValue("brandindexing");
			if($canonical_brand == "YES") {
				if(isset($GLOBALS['ISC_CLASS_BRANDS'])) {
						$trackingCode = '<link rel="canonical" href="'.$GLOBALS['ShopPath'].'/'.$GLOBALS['URL'].'" />';
				}
			}

			/*$canonical_category = $this->GetValue("categoryindexing");
			if($canonical_category == "YES") {
				if(isset($GLOBALS['CatId']) && !isset($GLOBALS['PriceMin']) && !isset($GLOBALS['PriceMax'])) {
					if (!isset($_GET['sort']) || $_GET['sort'] === '') {
						$trackingCode = '';
					} else {
						$trackingCode = '<link rel="canonical" href="'.$GLOBALS['ShopPath'].'/'.$GLOBALS['URL'].'" />';
					}
				}
			}*/

			$canonical_category = $this->GetValue("categoryindexing");
			if($canonical_category == "YES") {
				if(isset($GLOBALS['ISC_CLASS_CATEGORY']) && isset($GLOBALS['CatId'])) {
				$GLOBALS['CategoryLink'] = CatLink($GLOBALS['CatId'], $GLOBALS['ISC_CLASS_CATEGORY']->GetName(), false);
						$trackingCode = '<link rel="canonical" href="'.$GLOBALS['CategoryLink'].'" />';
				}
			}

			$canonical_home = $this->GetValue("homeindexing");
			if($canonical_home == "YES") {
				if(isset($GLOBALS['ISC_CLASS_INDEX'])) {
					$trackingCode = '<link rel="canonical" href="'.$GLOBALS['ShopPath'].'/" />';
				}
			}

			$canonical_product = $this->GetValue("productindexing");
			if($canonical_product == "YES") {
				if(isset($GLOBALS['ISC_CLASS_PRODUCT']) && $GLOBALS['ISC_CLASS_PRODUCT']->GetProductId() > 0) {
					$trackingCode = '<link rel="canonical" href="'.$GLOBALS['CurrentProductLink'].'" />';
				}
			}

			$canonical_pages = $this->GetValue("pagesindexing");
			if($canonical_pages == "YES") {
				if(isset($GLOBALS['ISC_CLASS_PAGE']) && !isset($GLOBALS['ISC_CLASS_INDEX']) && $GLOBALS['ISC_CLASS_PAGE']->GetPageId() > 0) {
					$GLOBALS['PageLink'] = PageLink($GLOBALS['ISC_CLASS_PAGE']->GetPageId(), $GLOBALS['ISC_CLASS_PAGE']->GetPageTitle());
					$trackingCode = '<link rel="canonical" href="'.$GLOBALS['PageLink'].'" />';
				}
			}

			$noindex_shopbyprice = $this->GetValue("shopbypriceindexing");
			if($noindex_shopbyprice == "YES") {
				if(isset($GLOBALS['PriceMin']) && isset($GLOBALS['PriceMax'])) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_tags = $this->GetValue("tagsindexing");
			if($noindex_tags == "YES") {
				if(isset($GLOBALS['ISC_CLASS_TAGS'])) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_createaccount = $this->GetValue("createaccountindexing");
			if($noindex_createaccount == "YES") {
				$noindex_url = str_replace("http://", "", $GLOBALS['ShopPathNormal'].'/login.php');
				$original_url = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				$split_url = explode('?', $original_url);
				$current_url = $split_url[0];
				if($current_url == $noindex_url) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_cart = $this->GetValue("cartindexing");
			if($noindex_cart == "YES") {
				$current_url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				$noindex_url = str_replace("http://", "", $GLOBALS['ShopPathNormal'].'/cart.php');
				if($current_url == $noindex_url) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_gift = $this->GetValue("giftcertindexing");
			if($noindex_gift == "YES") {
				$current_url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				$noindex_url = str_replace("http://", "", $GLOBALS['ShopPathNormal'].'/giftcertificates.php');
				if($current_url == $noindex_url) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_login = $this->GetValue("loginindexing");
			if($noindex_login == "YES") {
				$current_url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				$noindex_url = str_replace("http://", "", $GLOBALS['ShopPathNormal'].'/login.php');
				if($current_url == $noindex_url) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_search = $this->GetValue("searchindexing");
			if($noindex_search == "YES") {
				$noindex_url = str_replace("http://", "", $GLOBALS['ShopPathNormal'].'/search.php');
				$original_url = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				$split_url = explode('?', $original_url);
				$current_url = $split_url[0];
				if($current_url == $noindex_url) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_wishlist = $this->GetValue("wishlistindexing");
			if($noindex_wishlist == "YES") {
				$current_url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				$noindex_url = str_replace("http://", "", $GLOBALS['ShopPathNormal'].'/login.php?from=wishlist.php%3F');
				if($current_url == $noindex_url) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			}

			$noindex_https = $this->GetValue("httpsindexing");
			if(($noindex_https == "YES") && ($GLOBALS['ISC_CFG']["UseSSL"] >= 1) && ($_SERVER['HTTPS'] == 'on')) {
					$trackingCode = '<meta name="robots" content="noindex, follow">';					
				}
			
			if(!isset($_REQUEST['page']) || empty($_REQUEST['page'])) {
			
				return $trackingCode;
			}
		}

		/**
		 * Return the conversion tracking code for this module.
		 */
		public function GetConversionCode()
		{
			/*$trackingCode = $this->GetValue('trackingcode');


			return $conversionCode;*/
		}
	}

?>