<?php
require_once dirname(__FILE__) . "/../class.exportfiletype.php";

class ISC_ADMIN_EXPORTFILETYPE_PRODUCTS extends ISC_ADMIN_EXPORTFILETYPE
{
	protected $type_name = "products";
	protected $type_icon = "product.gif";
	protected $type_idfield = "p.productid";
	protected $type_viewlink = "index.php?ToDo=viewProducts";

	protected $handlecats = false;
	protected $handleimages = false;
	protected $handlevars = false;

	protected $options; // variation options cache

	private $tableName = '[|PREFIX|]products p';


	public function GetFields()
	{
		$fields = array(
			"productID"				=> array("dbfield" => "p.productid"),
			"productType"			=> array(
											"dbfield" => "CASE prodtype WHEN 1 THEN 'P' WHEN 2 THEN 'D' END",
											"help" => GetLang("productTypeHelp")),
			"productCode"			=> array("dbfield" => "prodcode"),
			"productName"			=> array("dbfield" => "prodname"),
			"productBrand"			=> array("dbfield" => "brandname"),
			"productBrandName"		=> array("dbfield" => "CONCAT(IF(ISNULL(brandname), '', CONCAT(brandname, ' ')), ' ', prodname)"),
			"productDesc"			=> array("dbfield" => "proddesc", "format" => "text"),
			"productTaxable"		=> array("dbfield" => "prodistaxable", "format" => "bool"),
			"productCostPrice"		=> array("dbfield" => "prodcostprice", "format" => "number"),
			"productRetailPrice"	=> array("dbfield" => "prodretailprice", "format" => "number"),
			"productSalePrice"		=> array("dbfield" => "prodsaleprice", "format" => "number"),
			"productCalculatedPrice"=> array("dbfield" => "prodcalculatedprice", "format" => "number"),
			"productShippingPrice"	=> array("dbfield" => "prodfixedshippingcost", "format" => "number"),
			"productFreeShipping"	=> array("dbfield" => "prodfreeshipping", "format" => "bool"),
			"productWarranty"		=> array("dbfield" => "prodwarranty"),
			"productWeight"			=> array("dbfield" => "prodweight"),
			"productWidth"			=> array("dbfield" => "prodwidth"),
			"productHeight"			=> array("dbfield" => "prodheight"),
			"productDepth"			=> array("dbfield" => "proddepth"),
			"productPurchasable"	=> array("dbfield" => "prodallowpurchases", "format" => "bool"),
			"productVisible"		=> array("dbfield" => "prodvisible", "format" => "bool"),
			"productNotVisible"		=> array("dbfield" => "NOT prodvisible", "format" => "bool"),
			"productAvailability"	=> array("dbfield" => "prodavailability"),
			"productInventoried"	=> array("dbfield" => "(prodinvtrack > 0)", "format" => "bool"),
			"productStockLevel"		=> array("dbfield" => "prodcurrentinv"),
			"productLowStockLevel"	=> array("dbfield" => "prodlowinv"),
			"productDateAdded"		=> array("dbfield" => "proddateadded", "format" => "date"),
			"productLastModified"	=> array("dbfield" => "prodlastmodified", "format" => "date"),
			"productCategories"		=> array(
											"fields" => array(
															"productCategoryID" 	=> array(),
															"productCategoryName"	=> array(),
															"productCategoryPath"	=> array()
														)
										),
			"productImages"			=> array(
											"fields" => array(
															"productImageFile"	=> array(),
															"productImageURL"	=> array()
														)
										),
			"productPageTitle"		=> array("dbfield" => "prodpagetitle"),
			"productMetaKeywords"	=> array("dbfield" => "prodmetakeywords"),
			"productMetaDesc"		=> array("dbfield" => "prodmetadesc"),
			"productVariations"		=> array(
											"fields" => array(
															"productVarDetails"			=> array("ignore" => true),
															"productVarSKU"				=> array(),
															"productVarPrice"			=> array("format" => "number"),
															"productVarWeight"			=> array(),
															"productVarStockLevel"		=> array(),
															"productVarLowStockLevel"	=> array()
														)
										),
			"productMYOBAsset"		=> array(),
			"productMYOBIncome"		=> array(),
			"productMYOBExpense"	=> array(),
			"productCondition"		=> array("dbfield" => "prodcondition")
		);

		return $fields;
	}

	protected function PostFieldLoad()
	{
		$fields = $this->fields;

		if ($this->templateid) {
			$fields['productMYOBAsset']['dbfield'] = "'" . $GLOBALS['ISC_CLASS_DB']->Quote($this->template['myobassetaccount']) . "'";
			$fields['productMYOBIncome']['dbfield'] = "'" . $GLOBALS['ISC_CLASS_DB']->Quote($this->template['myobincomeaccount']) . "'";
			$fields['productMYOBExpense']['dbfield'] = "'" . $GLOBALS['ISC_CLASS_DB']->Quote($this->template['myobexpenseaccount']) . "'";

			// is the categories fields used?
			if ($fields['productCategories']['used']) {
				// are any sub-fields ticked? let parent handle row output if none are
				$catfieldsused = false;
				foreach ($fields['productCategories']['fields'] as $id => $field) {
					if ($field['used']) {
						$catfieldsused = true;
						break;
					}
				}

				$this->handlecats = $catfieldsused;
			}

			// is the images fields used?
			if ($fields['productImages']['used']) {
				$imagefieldsused = false;

				// are any sub-fields ticked? let parent handle row output if none are
				$catfieldsused = false;
				foreach ($fields['productImages']['fields'] as $id => $field) {
					if ($field['used']) {
						$imagefieldsused = true;
						break;
					}
				}

				$this->handleimages = $imagefieldsused;
			}

			// is the variations used?
			if ($fields['productVariations']['used']) {
				// are any sub-fields ticked? let parent handle row output if none are
				$varfieldsused = false;
				foreach ($fields['productVariations']['fields'] as $id => $field) {
					if ($field['used']) {
						$varfieldsused = true;
						break;
					}
				}

				$this->handlevars = $varfieldsused;

				if ($varfieldsused) {
					//======cache variations for performance======/

					// get the variation options
					$query = "SELECT * FROM [|PREFIX|]product_variation_options ORDER BY vovariationid, voptionid";
					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					$options_cache = array();
					while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						$options_cache[$row['vovariationid']][$row['voname']][] = array("id" => $row['voptionid'], "value" => $row['vovalue']);
					}

					foreach ($options_cache as $variationid => $var_options) {
						// get a list of all possible combinations using the options for this variation
						$options = array_cartesian_product($var_options, true);

						// the options for each variation
						foreach ($options as $option) {
							$optionids = "";
							$description = "";

							// build strings of the id's and values of each option
							foreach ($option as $key => $value) {
								if ($optionids) {
									$optionids .= ",";
								}
								$optionids .= $value["id"];

								if ($description) {
									$description .= ", ";
								}
								$description .= $key . ": " . $value["value"];
							}

							$new_options[$optionids] = array(
								"options"		=> $option,
								"description"	=> $description
							);
						}

						$this->options[$variationid] = $new_options;
					}
				}
			}
		}

		$this->fields = $fields;
	}

	private function BuildProductExportCategories($categoryids)
	{
		static $categoryCache;
		if(empty($categoryCache)) {
			$query = "SELECT categoryid, catname, catparentid, catparentlist FROM [|PREFIX|]categories";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$categoryCache = array();
			while($category = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$category['trail'] = '';
				$categoryCache[$category['categoryid']] = $category;
			}
		}

		$categoryids = explode(",", $categoryids);
		$productCategories = array();
		foreach($categoryids as $categoryId) {
			if($categoryId == '') {
				continue;
			}
			// This category doesn't exist, continue
			if(!isset($categoryCache[$categoryId])) {
				continue;
			}

			// We've already built a trail for this category, just add the prebuild version
			if($categoryCache[$categoryId]['trail'] != '') {
				$productCategories[$categoryId] = $categoryCache[$categoryId]['trail'];
			}

			$categoryTrail = '';
			$parentId = $categoryId;
			do {
				$categoryTrail = $categoryCache[$parentId]['catname'] . '/' . $categoryTrail;
				$parentId = $categoryCache[$parentId]['catparentid'];
			}
			while(isset($categoryCache[$parentId]) && $parentId != 0);

			$categoryTrail = rtrim($categoryTrail, '/');
			// Cache the result
			$categoryCache[$categoryId]['trail'] = $categoryTrail;
			if($categoryTrail == '') {
				continue;
			}
			$productCategories[$categoryId] = array(
				"productCategoryID"		=> $categoryId,
				"productCategoryName" 	=> $categoryCache[$categoryId]['catname'],
				"productCategoryPath"	=> $categoryTrail
			);
		}

		return $productCategories;
	}

	protected function HandleRow($row)
	{
		//override myob fields if the product has data for these settings
		if ($row['asset']) {
			$row['productMYOBAsset'] = $row['asset'];
		}
		if ($row['income']) {
			$row['productMYOBIncome'] = $row['income'];
		}
		if ($row['asset']) {
			$row['productMYOBExpense'] = $row['expense'];
		}

		if ($this->handlecats) {
			$row = $this->HandleCategories($row);
		}

		if ($this->handleimages) {
			$row = $this->HandleImages($row);
		}

		if ($this->handlevars && $row['prodvariationid']) {
			$row = $this->HandleVariations($row);
		}

		return $row;
	}

	private function HandleImages($row)
	{
		$images = array();

		$imageIterator = new ISC_PRODUCT_IMAGE_ITERATOR('SELECT * FROM [|PREFIX|]product_images WHERE imageprodid = ' . $row['prodid']);
		foreach($imageIterator as $imageId => $image) {
			$images[] = array(
				'productImageFile' 	=> $image->getFileName(),
				'productImageURL' 	=> GetConfig('ShopPath') . '/' . GetConfig('ImageDirectory') . '/' . $image->GetSourceFilePath()
			);
		}

		$row['productImages'] = $images;

		return $row;
	}

	private function HandleCategories($row)
	{
		//=========Product Categories==========/

		// get the categories for this product
		$categories = $this->BuildProductExportCategories($row['categoryids']);
		$new_categories = $categories;

		// remove unused fields
		$cat_fields = $this->fields['productCategories']['fields'];

		foreach ($categories as $id => $category) {
			foreach ($cat_fields as $fieldid => $field) {
				if (!$field['used']) {
					unset($new_categories[$id][$fieldid]);
				}
			}
		}

		// apply any field formatting
		$this->FormatColumns($new_categories, $this->fields['productCategories']['fields']);

		$row["productCategories"] = $new_categories;

		return $row;
	}

	protected function HandleVariations($row)
	{
		//========Product Variations==========//

		// get the position of the options in the variation fields
		$option_position = $this->GetFieldPosition('productVarDetails', $this->fields['productVariations']['fields']);

		// get the options for the variation
		$varoptions = $this->options[$row['prodvariationid']];

		// get the data for the combinations
		$query = "SELECT * FROM [|PREFIX|]product_variation_combinations WHERE vcproductid = " . $row['prodid'];
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		while ($comborow = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			// get the specific combination of options
			$options = $varoptions[$comborow['vcoptionids']]['options'];

			$prodoptions = array();

			$prodoptions['productVarDetails']		= "";
			$prodoptions['productVarSKU']			= $comborow['vcsku'];
			switch ($comborow['vcpricediff']) {
				case "add":
					$pricediff = "+";
					break;
				case "substract":
					$pricediff = "-";
					break;
				default:
					$pricediff = "";
			}
			$prodoptions['productVarPrice'] = $pricediff . $comborow['vcprice'];
			switch ($comborow['vcweightdiff']) {
				case "add":
					$weightdiff = "+";
						break;
					case "substract":
						$weightdiff = "-";
						break;
					default:
						$weightdiff = "";
				}
				$prodoptions['productVarWeight']		= $weightdiff . $comborow['vcweight'];
				$prodoptions['productVarStockLevel']	= $comborow['vcstock'];
				$prodoptions['productVarLowStockLevel']	= $comborow['vclowstock'];

				// get our variation fields into the right order
				$outoptions = array();
				foreach($this->fields['productVariations']['fields'] as $id => $field) {
					if ($field['used']) {
						$outoptions[$id] = $prodoptions[$id];
					}
				}

				$prodoptions = $outoptions;

				if ($this->fields['productVariations']['fields']['productVarDetails']['used']) {
					$optionkeys = array();
					$optionvalues = array();
					foreach ($options as $key => $value) {
						$optionvalues[$key] = $value['value'];
						$optionkeys[] = $key;
					}

					$keys = array_keys($prodoptions);
					// insert the fields into correct position
					array_splice($prodoptions, $option_position, 0, $optionvalues);
					array_splice($keys, $option_position, 0, $optionkeys);

					$prodoptions = array_combine($keys, $prodoptions);
					$proddetails = $varoptions[$comborow['vcoptionids']]['description'];
					$prodoptions['productVarDetails'] = $proddetails;
				}

				$prodvariations[] = $prodoptions;
			}

			$row["productVariations"] = $prodvariations;

		return $row;
	}

	protected function GetQuery($columns, $where, $vendorid)
	{
		if ($vendorid) {
			if ($where) {
				$where .= " AND ";
			}
			$where .= "p.prodvendorid = '" . $vendorid . "'";
		}

		if ($where) {
			$where = " WHERE " . $where;
		}

		$query = "
			SELECT "
				. $columns . ",
				p.productid AS prodid,
				prodvariationid,
				prodmyobasset AS asset,
				prodmyobincome AS income,
				prodmyobexpense AS expense,
				(SELECT GROUP_CONCAT(ca.categoryid SEPARATOR ',') FROM [|PREFIX|]categoryassociations ca WHERE p.productid = ca.productid) AS categoryids
			FROM
				(
					SELECT
						DISTINCT ca.productid
					FROM
						[|PREFIX|]categoryassociations ca
						INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
						INNER JOIN [|PREFIX|]product_search ps ON p.productid = ps.productid
						LEFT JOIN [|PREFIX|]brands b ON b.brandid = p.prodbrandid
						" . $where . "
				) AS ca
				INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
				LEFT JOIN [|PREFIX|]brands b ON b.brandid = p.prodbrandid
			";

		if($GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId()) {
			$query .= " AND prodvendorid = '" . (int)$GLOBALS['ISC_CLASS_ADMIN_AUTH']->GetVendorId() . "'";
		}

		return $query;
	}

	public function GetListColumns()
	{
		$columns = array(
			"ID",
			"SKU",
			"Name",
			"Price",
			"Visible",
			"Featured"
		);

		return $columns;
	}

	public function GetListSortLinks()
	{
		$sortLinks = array(
			"ID" => "productid",
			"SKU" => "prodcode",
			"Name" => "prodname",
			"Price" => "prodprice",
			"Visible" => "prodvisible",
			"Featured" => "prodfeatured"
		);

		return $sortLinks;
	}

	public function GetListQuery($where, $vendorid, $sortField, $sortOrder)
	{
		SetupCurrency();

		if ($vendorid) {
			if ($where) {
				$where .= " AND ";
			}
			$where .= "p.prodvendorid = '" . $vendorid . "'";
		}

		if ($where) {
			$where = "WHERE " . $where;
		}

		$query = "
				SELECT
					p.productid,
					p.prodcode,
					p.prodname,
					p.prodprice,
					p.prodvisible,
					p.prodfeatured
				FROM
					(
						SELECT
							DISTINCT ca.productid
						FROM
							[|PREFIX|]categoryassociations ca
							INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
							INNER JOIN [|PREFIX|]product_search ps ON p.productid = ps.productid
							LEFT JOIN [|PREFIX|]brands b ON b.brandid = p.prodbrandid
							" . $where . "
						ORDER BY
							" . $sortField . " " . $sortOrder . "
					) AS ca
					INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
				";

		return $query;
	}

	public function GetListCountQuery($where, $vendorid)
	{
		if ($vendorid) {
			if ($where) {
				$where .= " AND ";
			}
			$where .= "p.prodvendorid = '" . $vendorid . "'";
		}

		if ($where) {
			$where = "WHERE " . $where;
		}

		$query = "
				SELECT
					COUNT(DISTINCT p.productid) AS ListCount
				FROM
					[|PREFIX|]categoryassociations ca
					INNER JOIN [|PREFIX|]products p ON p.productid = ca.productid
					INNER JOIN [|PREFIX|]product_search ps ON p.productid = ps.productid
					LEFT JOIN [|PREFIX|]brands b ON b.brandid = p.prodbrandid
				" . $where;

		return $query;
	}

	public function GetListRow($row)
	{
		$new_row['ID'] = $row['productid'];
		$new_row['SKU'] = $row['prodcode'];
		$new_row['Name'] = $row['prodname'];
		$new_row['Price'] = FormatPriceInCurrency($row['prodprice']);
		if ($row['prodvisible']) {
			$new_row['Visible'] = '<img src="images/tick.gif" alt="tick"/>';
		}
		else {
			$new_row['Visible'] = '<img src="images/cross.gif" alt="cross"/>';
		}

		if ($row['prodfeatured']) {
			$new_row['Featured'] = '<img src="images/tick.gif" alt="tick"/>';
		}
		else {
			$new_row['Featured'] = '<img src="images/cross.gif" alt="cross"/>';
		}

		return $new_row;
	}

	protected function BuildWhereFromFields($search_fields)
	{
		$class = GetClass('ISC_ADMIN_PRODUCT');

		$res = $class->BuildWhereFromVars($search_fields);
		$where = $res['query'];

		// strip AND from beginning and end of statement
		$where = preg_replace("/^( ?AND )?|( AND ?)?$/i", "", $where);

		return $where;
	}

	public function HasPermission()
	{
		return $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Export_Products);
	}
}