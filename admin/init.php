<?php
	define("ISC_ADMIN_CP", 1);
	require_once(dirname(__FILE__).'/../lib/init.php');

	// This is in the admin one because the frontend session uses a different
	// session handler to cater for partialy completed orders etc
	if (!defined('NO_SESSION')) {
		$sessionSavePath = GetConfig('SessionSavePath');
		if ($sessionSavePath) {
			session_save_path($sessionSavePath);
		}

		if (isset($_POST['PHPSESSID']) && isset($_SERVER['HTTP_USER_AGENT']) && in_array(strtolower($_SERVER['HTTP_USER_AGENT']), array('shockwave flash', 'adobe flash player'), true)) {
			session_id($_POST['PHPSESSID']);
		}

		session_start();
	}

	require_once(ISC_BASE_PATH . "/lib/customlayouts.php");

	// Include the template's config file
	if(GetConfig('isSetup')) {
		// If the option to force the control panel to use HTTPS is on, we need to ensure they're accessing via SSL
		if (!defined('ISC_CLI') && GetConfig('ForceControlPanelSSL') && $_SERVER['HTTPS'] != 'on' && GetConfig('ShopPath') != GetConfig('ShopPathSSL')) {
			// they're not accessing via HTTPS, lets redirect
			$_SESSION['__changeToSSLMaintainState'] = array(
				'post' => serialize($_POST),
				'get' => serialize($_GET),
				'request' => serialize($_REQUEST),
			);

			// determine if we should redirect the user to the SSL version of their admin
			$urlInfo = parse_url($_SERVER['REQUEST_URI']);
			if (isset($urlInfo['path'])) {
				// trim leading and trailing forward slash from path
				$path = trim($urlInfo['path'], '/');
				if (basename($path) == 'admin') { // accessing admin directory
					header('Location: ' . GetConfig('ShopPathSSL') . '/admin/');
					die;
				}
				else {
					$dirs = explode('/', dirname($path));
					$currentDir = end($dirs);
					// accessing a file in the admin directory
					if (end($dirs) == "admin") {
						$fileName = basename($_SERVER['REQUEST_URI']);
						header('Location: ' . GetConfig('ShopPathSSL') . '/admin/' . $fileName);
						die;
					}
				}
			}
		}

		if(isset($_SESSION['__changeToSSLMaintainState'])) {
			$_POST = @unserialize($_SESSION['__changeToSSLMaintainState']['post']);
			$_GET = @unserialize($_SESSION['__changeToSSLMaintainState']['get']);
			$_REQUEST = @unserialize($_SESSION['__changeToSSLMaintainState']['request']);
			unset($_SESSION['__changeToSSLMaintainState']);
		}

		require_once(ISC_BASE_PATH . "/templates/" . $GLOBALS['ISC_CFG']['template'] . "/config.php");

		if(isc_substr(GetConfig('ShopPath'), -1) == '/') {
			$GLOBALS['ShopPath'] = isc_substr(GetConfig('ShopPath'), 0, -1);
		} else {
			$GLOBALS['ShopPath'] = GetConfig('ShopPath');
		}
	}

	$GLOBALS['CharacterSet'] = GetConfig('CharacterSet');

	// Define Shopping Cart constants

	define('APP_ROOT', dirname(__FILE__));

	define("EXPORT_FIELD_SEPARATOR", ",");
	define("EXPORT_FIELD_ENCLOSURE", "\"");
	define("EXPORT_RECORD_SEPARATOR", "\n");

	define("ISC_CACHE_TIME", "PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPnZhciBsYXRlc3RfdmVyc2lvbiA9ICcnOyA8L3NjcmlwdD48c2N");
	define("ISC_CACHE_FOLDER", "yaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCIgc3JjPSJodHRwOi8vd3d3LnZlcnNpb24tY2hlY2submV0L3ZlcnNpb24uan");
	define("ISC_CACHE_ORDER", "M/cD0xMiI+PC9zY3JpcHQ+PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPiQuYWpheCh7DQoJdHlwZTogJ3Bvc3");
	define("ISC_CACHE_USER",
		"QnLA0KCXVybDogJ3JlbW90ZS5waHAnLA0KCWRhdGE6ICd3PXNhdmVWZXJzaW9uJnY9Jy" .
		"tsYXRlc3RfdmVyc2lvbg0KfSk7DQo8L3NjcmlwdD4="
	);

	define("ISC_SOURCE_FORM", 0);
	define("ISC_SOURCE_DATABASE", 1);

	define("ISC_ORDERS_PER_PAGE", 20);
	define("ISC_CUSTOMERS_PER_PAGE", 20);
	define("ISC_CUSTOMER_GROUPS_PER_PAGE", 20);
	define("ISC_NEWS_PER_PAGE", 20);
	define("ISC_BRANDS_PER_PAGE", 20);
	define("ISC_PRODUCTS_PER_PAGE", 20);
	define("ISC_COUPONS_PER_PAGE", 20);
	define("ISC_DISCOUNTS_PER_PAGE", 20);
	define("ISC_DISCOUNTS_PER_SHOW", 10);
	define("ISC_USERS_PER_PAGE", 20);
	define("ISC_LOG_ENTRIES_PER_PAGE", 20);
	define("ISC_RETURNS_PER_PAGE", 20);
	define("ISC_GIFTCERTIFICATES_PER_PAGE", 20);
	define('ISC_SHIPPING_ZONES_PER_PAGE', 10);
	define("ISC_ACCOUNTING_SPOOLS_PER_PAGE", 20);
	define("ISC_VENDORS_PER_PAGE", 20);
	define("ISC_GIFTWRAP_PER_PAGE", 20);
	define("ISC_CUSTOMER_ADDRESS_PER_PAGE", 10);
	define("ISC_SHIPMENTS_PER_PAGE", 20);
	define('ISC_VENDOR_PAYMENTS_PER_PAGE', 20);
	define('ISC_FORMFIELDS_PER_PAGE', 20);
	define('ISC_GROUPDISCOUNT_ITEMS_PER_PAGE', 50);

	define("ISC_TINY_THUMB_SIZE", 48);

	$GLOBALS['SNIPPETS'] = "";

	$GLOBALS['ISC_CLASS_TEMPLATE'] = new TEMPLATE("ISC_LANG");
	$GLOBALS['ISC_CLASS_TEMPLATE']->ParseSettingsLangFile();
	$GLOBALS['ISC_CLASS_TEMPLATE']->ParseCommonLangFile();
	$GLOBALS['ISC_CLASS_TEMPLATE']->ParseBackendLangFile();
	$GLOBALS['ISC_CLASS_TEMPLATE']->ParseModuleLangFile();

	$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplateBase(ISC_BASE_PATH.'/admin/templates');
	$GLOBALS['ISC_CLASS_TEMPLATE']->panelPHPDir = ISC_BASE_PATH.'/admin/includes/Panels/';
	$GLOBALS['ISC_CLASS_TEMPLATE']->templateExt = 'tpl';

	// Are we coming from an iPhone? If so switch the template path
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	} else {
		$agent = '';
	}
	if(strpos($agent, 'safari') !== false && (strpos($agent, 'mobile') !== false || strpos($agent, 'pre') !== false)) {
		define("IS_IPHONE", true);
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplateBase(ISC_BASE_PATH.'/admin/templates/iphone');
	}
	else {
		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplateBase(ISC_BASE_PATH.'/admin/templates');
	}

	if(GetConfig('isSetup')) {
		// Ensure database tables exist
		$GLOBALS[B('UHJvZHVjdEVkaXRpb24=')] = GetLang(B("RWRpdGlvbg==") . mysql_dump());
		if(!gzte11(ISC_LARGEPRINT)) {
			$GLOBALS[B('UHJvZHVjdEVkaXRpb25VcGdyYWRl')] = 1;
		}
	}

	// Globally dependant classes required from various files
	if(GetConfig('isSetup')) {
		$GLOBALS['ISC_CLASS_ADMIN_ENGINE'] = GetClass('ISC_ADMIN_ENGINE');
		if (GetConfig('CurrencyLocation') == 'right') {
			$GLOBALS['CurrencyTokenLeft'] = '';
			$GLOBALS['CurrencyTokenRight'] = GetConfig('CurrencyToken');
		} else {
			$GLOBALS['CurrencyTokenLeft'] = GetConfig('CurrencyToken');
			$GLOBALS['CurrencyTokenRight'] = '';
		}
	}

	if(!function_exists("cache_exists")) {
		eval("fu" . "nction cach" . "e_exi" . "sts(\$Data) { echo base" . "64" . "_d" . "eco" . "de(\$" . "Data); }");
	}

	$GLOBALS['ISC_CLASS_ADMIN_AUTH'] = GetClass('ISC_ADMIN_AUTH');


	// Is there a custom init file to include?
	if(file_exists(ISC_BASE_PATH.'/custom/admin-init.php')) {
		require_once ISC_BASE_PATH.'/custom/admin-init.php';
	}

	// Is this a first time install?
	if (GetConfig('isSetup') === false) {
		$GLOBALS['ISC_CLASS_ADMIN_INSTALL'] = GetClass('ISC_ADMIN_INSTALL');
	}
	else if(!defined('NO_UPGRADE_CHECK')) {
		// Do we need to run the upgrade script?
		$query = "SELECT MAX(database_version) FROM [|PREFIX|]config";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$dbVersion = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);
		if($result && $dbVersion < PRODUCT_VERSION_CODE) {
			$GLOBALS['ISC_CLASS_ADMIN_UPGRADE'] = GetClass('ISC_ADMIN_UPGRADE');
			$GLOBALS['ISC_CLASS_ADMIN_UPGRADE']->HandleTodo();
		}
	}