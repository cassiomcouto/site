<?php
require_once(dirname(__FILE__) . "/../classes/class.batch.importer.php");

class ISC_BATCH_IMPORTER_PRODUCTS extends ISC_BATCH_IMPORTER_BASE
{
	/**
	 * @var string The type of content we're importing. Should be lower case and correspond with template and language variable names.
	 */
	protected $type = "products";

	protected $_RequiredFields = array(
		"prodname"
	);

	public function __construct()
	{
		$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('batch.importer');

		/**
		 * @var array Array of importable fields and their friendly names.
		 */
		$this->_ImportFields = array(
			"prodname" => GetLang('ProductName'),
			"category" => GetLang('ImportProductsCategory'),
			"category2" => GetLang('ImportProductsCategory2'),
			"category3" => GetLang('ImportProductsCategory3'),
			"brandname" => GetLang('BrandName'),
			"prodcode" => GetLang('ProductCodeSKU'),
			"proddesc" => GetLang('ProductDescription'),
			"prodavailability" => GetLang('Availability'),
			"prodprice" => GetLang('Price'),
			"prodcostprice" => GetLang('CostPrice'),
			"prodsaleprice" => GetLang('SalePrice'),
			"prodretailprice" => GetLang('RetailPrice'),
			"prodcurrentinv" => GetLang('CurrentStockLevel'),
			"prodistaxable" => GetLang('ProdIsTaxable'),
			"prodlowinv" => GetLang('LowStockLevel'),
			"prodwarranty" => GetLang('ProductWarranty'),
			"prodfixedshippingcost" => GetLang('FixedShippingCost'),
			"prodweight" => GetLang('ProductWeight'),
			"prodwidth" => GetLang('ProductWidth'),
			"prodheight" => GetLang('ProductHeight'),
			"proddepth" => GetLang('ProductDepth'),
			"prodpagetitle" => GetLang('PageTitle'),
			"prodsearchkeywords" => GetLang('SearchKeywords'),
			"prodmetakeywords" => GetLang('MetaKeywords'),
			"prodmetadesc" => GetLang('MetaDescription'),
			"prodimagefile" => GetLang('ProductImage'),
			"prodfile" => GetLang('ProductFile'),
			"prodcondition" => GetLang('ProductCondition')
		);

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() == 0 && gzte11(ISC_HUGEPRINT)) {
			$this->_ImportFields['prodvendorid'] = GetLang('Vendor');
		}

		parent::__construct();
	}

	/**
	 * Custom step 1 code specific to product importing. Calls the parent ImportStep1 funciton.
	 */
	protected function _ImportStep1($MsgDesc="", $MsgStatus="")
	{
		if($message = strtokenize($_REQUEST, '#')) {
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoError("", $message, MSG_ERROR);
			exit;
		}

		if ($MsgDesc != "" && !isset($GLOBALS['Message'])) {
			$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
		}

		if(isset($_POST['AutoCategory']) || $_SERVER['REQUEST_METHOD'] != "POST") {
			$GLOBALS['AutoCategoryChecked'] = "checked=\"checked\"";
		}

		$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');

		$GLOBALS['CategoryOptions'] = $GLOBALS["ISC_CLASS_ADMIN_CATEGORY"]->GetCategoryOptions(array(), "<option %s value='%d'>%s</option>", "selected=\"selected\"", "", false);
		if($GLOBALS['CategoryOptions'] == '') {
			$GLOBALS['ISC_LANG']['ImportProductsCategory'] = GetLang('ImportCreateCategory');
			$GLOBALS['HideCategorySelect'] = "none";
			$GLOBALS['HideCategoryTextbox'] = '';
		}
		else {
			$GLOBALS['HideCategoryTextbox'] = 'none';
		}

		// Set up generic import options
		parent::_ImportStep1();
	}

	/**
	 * Custom step 2 code specific to product importing. Calls the parent ImportStep2 funciton.
	 */
	protected function _ImportStep2($MsgDesc="", $MsgStatus="")
	{
		// Haven't been to this step before, need to parse CSV file
		if(isset($_POST) && !empty($_POST)) {
			if(!isset($this->ImportSession['CategoryId']) && !isset($this->ImportSession['AutoCategory'])) {
				if(isset($_POST['AutoCategory'])) {
					$this->ImportSession['AutoCategory'] = 1;
					$this->_RequiredFields[] = "category";
					$GLOBALS['CategoryRequired'] = 1;
				}
				else {
					if(!isset($_POST['CategoryId']) && !isset($_POST['CategoryName'])) {
						$this->_ImportStep1(GetLang('ImportInvalidCategory'), MSG_ERROR);
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						exit;
					}
					// Creating a new category
					else if(isset($_POST['CategoryName']) && $_POST['CategoryName'] != "") {
						// Pass on to category creation function
						$_POST['catname'] = $_POST['CategoryName'];
						$_POST['catdesc'] = '';
						$_POST['catpagetitle'] = '';
						$_POST['catmetakeywords'] = '';
						$_POST['catmetadesc'] = '';
						$_POST['catlayoutfile'] = '';
						$_POST['catsort'] = 0;
						$_POST['catparentid'] = 0;
						$_POST['catsearchkeywords'] = '';
						$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
						$error = $GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->_CommitCategory(0);
						if($error) {
							$this->_ImportStep1($error, MSG_ERROR);
						}
						$_POST['CategoryId'] = $GLOBALS['ISC_CLASS_DB']->LastId();
					}
					// Missing selection
					else if(empty($_POST['CategoryId'])) {
						$this->_ImportStep1(GetLang('ImportInvalidCategory'), MSG_ERROR);
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						exit;
					}
					$this->ImportSession['CategoryId'] = $_POST['CategoryId'];
				}

			}
		}

		// Set up generic import options

		if ($MsgDesc != "") {
			$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
		}

		parent::_ImportStep2();
	}

	/**
	 * Imports an actual product record in to the database.
	 *
	 * @param array Array of record data
	 */
	protected function _ImportRecord($record)
	{
		static $categoryCache;
		static $categoryNameCache;

		if(!is_array($categoryCache)) {
			$categoryCache = array();
		}
		if(!is_array($categoryNameCache)) {
			$categoryNameCache = array();
		}

		static $vendorCatCache;
		static $vendorAllCats;

		if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			// get a list of categories vendor has access to import to
			if (!is_array($vendorCatCache)) {
				$query = "SELECT vendoraccesscats FROM [|PREFIX|]vendors WHERE vendorid = '" . $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() . "'";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				$accesscats = $GLOBALS['ISC_CLASS_DB']->FetchOne($result, 'vendoraccesscats');
				if ($accesscats) {
					$vendorAllCats = false;
					$vendorCatCache = explode(",", $accesscats);
				}
				else {
					$vendorAllCats = true;
					$vendorCatCache = array();
				}
			}
		}

		if(!isset($record['prodname']) || empty($record['prodname'])) {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportProductsMissingName');
			return;
		}

		if ($message = strtokenize($_REQUEST, '#')) {
			$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang(B('UmVhY2hlZFByb2R1Y3RMaW1pdA=='));
			return;
		}

		$productHash = uniqid('IMPORT', true); // uniqid is random enough on it's own, md5-ing it just makes it 'less random' since md5 values can collide
		$productId = 0;
		$hasThumb = false;
		$productFiles = array();
		$productImages = array();
		$existing = null;

		// Is there an existing product with the same name?
		$query = sprintf("SELECT * FROM [|PREFIX|]products WHERE prodname='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($record['prodname']));
		$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
		if($existing = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
			// Overriding existing products, set the product id
			if(isset($this->ImportSession['OverrideDuplicates']) && $this->ImportSession['OverrideDuplicates'] == 1) {
				$productId = $existing['productid'];
				$cats = null;

				if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() != $existing['prodvendorid']) {
					$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportProductInvalidVendor');
					return;
				}

				$this->ImportSession['Results']['Updates'][] = $record['prodname'];
			}
			else {
				$this->ImportSession['Results']['Duplicates'][] = $record['prodname'];
				return;
			}
		}
		// Do we have a product file? We need to deal with it now damn it!
		if(isset($record['prodfile']) && $record['prodfile'] != '') {
			// Is this a remote file?
			$downloadDirectory = ISC_BASE_PATH."/".GetConfig('DownloadDirectory');
			if(isc_substr(isc_strtolower($record['prodfile']), 0, 7) == "http://") {
				// Need to fetch the remote file
				$file = PostToRemoteFileAndGetResponse($record['prodfile']);
				if($file) {
					// Place it in our downloads directory
					$randomDir = strtolower(chr(rand(65, 90)));
					if(!is_dir($downloadDirectory.$randomDir)) {
						if(!@mkdir($downloadDirectory."/".$randomDir, ISC_WRITEABLE_DIR_PERM)) {
							$randomDir = '';
						}
					}

					// Generate a random filename
					$fileName = $randomDir . "/" . GenRandFileName(basename($record['prodfile']));
					if(!@file_put_contents($downloadDirectory."/".$fileName, $file)) {
						$this->ImportSession['Results']['Warnings'][] = $record['prodname'].GetLang('ImportProductFileUnableToMove');
					}
					else {
						$productFiles[] = array(
							"prodhash" => "",
							"downfile" => $fileName,
							"downdateadded" => time(),
							"downmaxdownloads" => 0,
							"downexpiresafter" => 0,
							"downfilesize" => filesize($downloadDirectory."/".$fileName),
							"downname" => basename($record['prodfile']),
							"downdescription" => ""
						);
					}
				}
				else {
					$this->ImportSession['Results']['Warnings'][] = $record['prodname'].GetLang('ImportProductFileDoesntExist');
				}
			}
			// Treating the file as a local file, in the product_fules/import directory
			else {
				// This file exists, can be imported
				if(file_exists($downloadDirectory."/import/".$record['prodfile'])) {

					// Move it to our images directory
					$randomDir = strtolower(chr(rand(65, 90)));
					if(!is_dir("../".$downloadDirectory."/".$randomDir)) {
						if(!@mkdir($downloadDirectory."/".$randomDir, ISC_WRITEABLE_DIR_PERM)) {
							$randomDir = '';
						}
					}

					// Generate a random filename
					$fileName = $randomDir . "/" . GenRandFileName($record['prodfile']);
					if(!@copy($downloadDirectory."/import/".$record['prodfile'], $downloadDirectory."/".$fileName)) {
						$this->ImportSession['Results']['Warnings'][] = $record['prodname'].GetLang('ImportProductFileUnableToMove');
					}
					else {
						$productFiles[] = array(
							"prodhash" => "",
							"downfile" => $fileName,
							"downdateadded" => time(),
							"downmaxdownloads" => 0,
							"downexpiresafter" => 0,
							"downfilesize" => filesize($downloadDirectory."/".$fileName),
							"downname" => basename($record['prodfile']),
							"downdescription" => ""
						);
					}
				}
				else {
					$this->ImportSession['Results']['Warnings'][] = $record['prodname'].GetLang('ImportProductFileDoesntExist');
				}
			}
		}

		// Do we have an image? We need to deal with it before we do anything else
		$productImages = array();
		if(isset($record['prodimagefile']) && $record['prodimagefile'] != '') {

			// code exists in the new product image management classes to handle these imports
			$imageFile = $record['prodimagefile'];
			$imageAdmin = new ISC_ADMIN_PRODUCT_IMAGE();

			if (preg_match('#^(?P<scheme>[a-zA-Z0-9\.]+)://#i', $imageFile, $matches)) {
				// the filename is an external URL, import it against the calcualted product hash
				$imageAdmin->importImagesFromUrls($productHash, array($imageFile), $importImages, $importImageErrors, true);

				if (count($importImages)) {
					$productImages = array_merge($productImages, $importImages);
				}

				if (count($importImageErrors)) {
					// as this import works on one file only and importImagesFromWebUrls creates one error per file, can simply tack on the new error
					$importImageError = $importImageErrors[0];
					if (is_array($importImageError)) {
						$this->ImportSession['Results']['Warnings'][] = $importImageErrors[1];
					} else {
						$this->ImportSession['Results']['Warnings'][] = $importImageErrors;
					}
				}

			} else {
				// the filename is a local file

				$importImageFilePath = ISC_BASE_PATH . "/" . GetConfig('ImageDirectory') . "/import/" . $imageFile;

				try {
					$importedImage = ISC_PRODUCT_IMAGE::importImage($importImageFilePath, basename($importImageFilePath), $productHash, true, false);
					$productImages[] = $importedImage;
				} catch (Exception $exception) {
					$this->ImportSession['Results']['Warnings'][] = $exception->getMessage();
				}
			}
		}

		// Automatically fetching categories based on CSV field
		if(isset($this->ImportSession['AutoCategory'])) {
			// We specified more than one level for the category back in the configuration
			if(isset($record['category1'])) {
				$record['category'] = array($record['category1']);
				if(isset($record['category2']) && $record['category2'] != '') {
					$record['category'][] = $record['category2'];
				}
				if(isset($record['category3']) && $record['category3'] != '') {
					$record['category'][] = $record['category3'];
				}
				$record['category'] = implode("/", $record['category']);
			}


			if(!$record['category']) {
				$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportProductsMissingCategory');
				return;
			}

			// Import the categories for the products too
			$categoryList = explode(";", $record['category']);
			$cats = array();
			foreach($categoryList as $importCategory) {
				$categories = explode("/", $importCategory);
				$parentId = 0;
				$lastCategoryId = 0;
				if(!isset($categoryCache[$importCategory])) {
					foreach($categories as $category) {
						$category = trim($category);
						if($category == '') {
							continue;
						}
						$query = "SELECT catname, categoryid, catparentlist FROM [|PREFIX|]categories WHERE catname='".$GLOBALS['ISC_CLASS_DB']->Quote($category)."' AND catparentid='".$parentId."'";
						$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
						$existingCategory = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
						// category doesn't exist, create the category if we have permission
						if(!$existingCategory) {
							if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Create_Category)) {
								// Create the category
								$_POST['catname'] = $category;
								$_POST['catdesc'] = '';
								$_POST['catsort'] = 0;
								$_POST['catparentid'] = $parentId;
								$_POST['catpagetitle']  = '';
								$_POST['catmetakeywords'] = '';
								$_POST['catmetadesc'] = '';
								$_POST['catlayoutfile'] = '';
								$_POST['catsearchkeywords'] = '';
								$GLOBALS['ISC_CLASS_ADMIN_CATEGORY'] = GetClass('ISC_ADMIN_CATEGORY');
								$error = $GLOBALS['ISC_CLASS_ADMIN_CATEGORY']->_CommitCategory(0);
								if($error) {
									$this->ImportSession['Results']['Failures'][] = implode(",", $record['original_record'])." ".GetLang('ImportProductsMissingCategory');
									return;
								}
								$lastCategoryId = $GLOBALS['NewCategoryId'];
							}
							else {
								// no category creation permission, abort this record
								$this->ImportSession['Results']['Warnings'][] = $record['prodname'] . " " . GetLang('ImportNoPermissionCreateCategory');
							}
						}
						else {
							$lastCategoryId = $existingCategory['categoryid'];
							$categoryNameCache[$lastCategoryId] = $existingCategory['catname'];
						}
						$parentId = $lastCategoryId;
					}
					// add the category to the cache
					if($lastCategoryId) {
						$categoryCache[$importCategory] = $lastCategoryId;
						$cats[] = $lastCategoryId;
					}
				}
				else {
					$cats[] = $categoryCache[$importCategory];
				}
			}
		}
		// Manually set a category
		elseif (isset($this->ImportSession['CategoryId'])) {
			$cats = array($this->ImportSession['CategoryId']);
		}

		// no categories to import to? abort
		if (count($cats) == 0) {
			$this->ImportSession['Results']['Failures'][] = $record['prodname'] ." " . GetLang('ImportNoCategories');
			return;
		}
		// check if vendor has permission to import to the specific categories
		elseif ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() && !$vendorAllCats) {
			$catsCopy = $cats;
			foreach ($cats as $x => $cat) {
				if (!in_array($cat, $vendorCatCache)) {
					// no access, remove from list
					unset($catsCopy[$x]);
					// generate a warning
					$this->ImportSession['Results']['Warnings'][] = $record['prodname'] ." " . sprintf(GetLang('ImportNoPermissionImportToCategory'), $categoryNameCache[$cat]);
				}
			}
			$cats = $catsCopy;

			// no cats left, abort this record
			if (count($cats) == 0) {
				$this->ImportSession['Results']['Failures'][] = $record['prodname'] ." " . GetLang('ImportNoPermissionImportAllCategories');
				return;
			}
		}

		// check the condition is valid
		$validConditions = array('new', 'used', 'refurbished');
		if (!isset($record['prodcondition']) || !in_array(isc_strtolower($record['prodcondition']), $validConditions)) {
			$record['prodcondition'] = 'New';
		}

		// If this is an update then we have to merge in the existing information that is NOT in the CSV file
		if (is_array($existing)) {
			$record = $record + $existing;
		}

		// Apply any default data
		$defaults = array(
			'prodistaxable' => 1,
			'prodprice' => 0,
			'prodcostprice' => 0,
			'prodretailprice' => 0,
			'prodsaleprice' => 0,
			'prodsearchkeywords' => '',
			'prodsortorder' => 0,
			'prodvisible' => 1,
			'prodfeatured' => 0,
			'prodrelatedproducts' => '-1',
			'prodoptionsrequired' => 0,
			'prodfreeshipping' => 0,
			'prodlayoutfile' => '',
			'prodtags' => '',
			'prodcondition' => 'New'
		);

		$record += $defaults;

		// Does the brand already exist?
		$brandId = 0;
		if(isset($record['brandname']) && $record['brandname'] != '') {
			$query = sprintf("select brandid from [|PREFIX|]brands where brandname='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($record['brandname']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			if($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$brandId = $row['brandid'];
			}
			// Create new brand
			else {
				// do we have permission to create brands?
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Add_Brands)) {
					$newBrand = array(
						"brandname" => $record['brandname']
					);
					$brandId = $GLOBALS['ISC_CLASS_DB']->InsertQuery("brands", $newBrand);
					$brandId = $GLOBALS['ISC_CLASS_DB']->LastId();
				}
				else {
					// no brand creation permission, abort this record
					$this->ImportSession['Results']['Failures'][] = $record['prodname'] . " " . GetLang('ImportNoPermissionCreateBrand');
					return;
				}
			}
		}

		// Set the inventory tracking flag. There is no option so look for other inventory level settings. New records only
		if (!is_array($existing) && ((isset($record['prodlowinv']) && trim($record['prodlowinv']) !== '') || (isset($record['prodcurrentinv']) && trim($record['prodcurrentinv']) !== ''))) {
			$record['prodinvtrack'] = 1;
		}

		if (isset($record['prodfile']) && $record['prodfile'] != '') {
			$productType = 2;
		} else if (isset($existing['prodtype']) && isId($existing['prodtype'])) {
			$productType = (int)$existing['prodtype'];
		} else {
			$productType = 1;
		}

		if(isset($record['prodistaxable'])) {
			$record['prodistaxable'] = $this->StringToYesNoInt($record['prodistaxable']);
		}

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$vendorId = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
		}
		else {
			$vendorId = (int)@$record['prodvendorid'];
		}

		// This is our product
		$productData = array(
			"prodname" => $record['prodname'],
			"prodcode" => @$record['prodcode'],
			"proddesc" => @$record['proddesc'],
			"prodsearchkeywords" => @$record['prodsearchkeywords'],
			"prodtype" => $productType,
			"prodprice" => DefaultPriceFormat($record['prodprice']),
			"prodcostprice" => DefaultPriceFormat($record['prodcostprice']),
			"prodretailprice" => DefaultPriceFormat($record['prodretailprice']),
			"prodsaleprice" => DefaultPriceFormat($record['prodsaleprice']),
			"prodavailability" => @$record['prodavailability'],
			"prodsortorder" => $record['prodsortorder'],
			"prodvisible" => $record['prodvisible'],
			"prodfeatured" => $record['prodfeatured'],
			"prodrelatedproducts" => $record['prodrelatedproducts'],
			"prodinvtrack" => (int)@$record['prodinvtrack'],
			"prodcurrentinv" => (int)@$record['prodcurrentinv'],
			"prodlowinv" => (int)@$record['prodlowinv'],
			"prodoptionsrequired" => $record['prodoptionsrequired'],
			"prodwarranty" => @$record['prodwarranty'],
			"prodheight" => (float)@$record['prodheight'],
			"prodweight" => (float)@$record['prodweight'],
			"prodwidth" => (float)@$record['prodwidth'],
			"proddepth" => (float)@$record['proddepth'],
			"prodfreeshipping" => $record['prodfreeshipping'],
			"prodfixedshippingcost" => DefaultPriceFormat(@$record['prodfixedshippingcost']),
			"prodbrandid" => (int)$brandId,
			"prodcats" => $cats,
			"prodpagetitle" => @$record['prodpagetitle'],
			"prodmetakeywords" => @$record['prodmetakeywords'],
			"prodmetadesc" => @$record['prodmetadesc'],
			"prodlayoutfile" => $record['prodlayoutfile'],
			"prodistaxable" => $record['prodistaxable'],
			'prodvendorid' => $vendorId,
			'prodtags' => $record['prodtags'],
			'prodmyobasset' => '',
			'prodmyobincome' => '',
			'prodmyobexpense' => '',
			'prodpeachtreegl' => '',
			'prodcondition' => $record['prodcondition']
		);

		/**
		 * The variation is part of the product record, so it will have to be attached to the record if this is an
		 * update AND the existing product already has a variation
		 */
		if (is_array($existing) && isId($existing['prodvariationid'])) {
			$productData['prodvariationid'] = $existing['prodvariationid'];
		}

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$productData['prodvendorid'] = $GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId();
		}

		$empty = array();

		// Save it
		$err = '';
		$GLOBALS['ISC_CLASS_ADMIN_PRODUCT']->_CommitProduct($productId, $productData, $empty, $empty, $empty, $err, $empty, true);

		if($productId == 0) {
			$productId = $GLOBALS['NewProductId'];
		}

		// Are there any images?
		if(count($productImages) > 0) {

			// delete any existing ones
			$existingImages = new ISC_PRODUCT_IMAGE_ITERATOR("SELECT * FROM `[|PREFIX|]product_images` WHERE imageprodid = " . (int)$productId);
			foreach ($existingImages as $existingImage) {
				/** @var $existingImages ISC_PRODUCT_IMAGE */
				$existingImage->delete(false);
			}

			// update ones that were processed above to be linked to the new product id
			$updateData = array(
				'imageprodid' => $productId,
				'imageprodhash' => ''
			);

			$updateWhere = "imageprodhash = '" . $GLOBALS['ISC_CLASS_DB']->Quote($productHash) . "'";

			$GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_images', $updateData, $updateWhere);
		}

		// Are there any product files?
		if(count($productFiles) > 0) {
			foreach($productFiles as $file) {
				$file['productid'] = $productId;
				$GLOBALS['ISC_CLASS_DB']->InsertQuery("product_downloads", $file);
			}
		}

		++$this->ImportSession['Results']['SuccessCount'];
	}
}