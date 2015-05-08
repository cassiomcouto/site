<?php

	CLASS ISC_SIDECATEGORYNEWPRODUCTS_PANEL extends PANEL
	{
		public $cacheable = true;
		public $cacheId = "categories.products.sidenewproducts";

		public function __construct()
		{
			$this->cacheId .= ".".$GLOBALS['CatId'];
		}

		public function SetPanelSettings()
		{
			$output = "";
			$categorySql = $GLOBALS['ISC_CLASS_CATEGORY']->GetCategoryAssociationSQL();

			$query = "
				SELECT p.*, FLOOR(prodratingtotal/prodnumratings) AS prodavgrating, pi.*, ".GetProdCustomerGroupPriceSQL()."
				FROM [|PREFIX|]products p USE INDEX (PRIMARY)
				LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1)
				WHERE p.prodvisible='1' ".$categorySql."
				ORDER BY p.productid DESC
			";
			$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit(0, 5);

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {
				if(!GetConfig('ShowProductRating')) {
					$GLOBALS['HideProductRating'] = "display: none";
				}

				$GLOBALS['AlternateClass'] = '';
				while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					if($GLOBALS['AlternateClass'] == 'Odd') {
						$GLOBALS['AlternateClass'] = 'Even';
					}
					else {
						$GLOBALS['AlternateClass'] = 'Odd';
					}

					$GLOBALS['ProductCartQuantity'] = '';
					if(isset($GLOBALS['CartQuantity'.$row['productid']])) {
						$GLOBALS['ProductCartQuantity'] = (int)$GLOBALS['CartQuantity'.$row['productid']];
					}

					$GLOBALS['ProductId'] = $row['productid'];
					$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);
					$GLOBALS['ProductLink'] = ProdLink($row['prodname']);

					// Determine the price of this product
					$GLOBALS['ProductPrice'] = CalculateProductPrice($row);

					$GLOBALS['ProductRating'] = (int)$row['prodavgrating'];
					$GLOBALS['ProductDate'] = isc_date(GetConfig('DisplayDateFormat'), $row['proddateadded']);

					if (isId($row['prodvariationid']) || trim($row['prodconfigfields'])!='' || $row['prodeventdaterequired'] == 1) {
						$GLOBALS['ProductURL'] = ProdLink($row['prodname']);
						$GLOBALS['ProductAddText'] = GetLang('ProductChooseOptionLink');
					} else {
						$GLOBALS['ProductURL'] = CartLink($row['productid']);
						$GLOBALS['ProductAddText'] = GetLang('ProductAddToCartLink');
					}

	if (CanAddToCart($row) && GetConfig('ShowAddToCartLink')) {

						


$mos = GetModuleVariable('addon_parcelas','loginparapreco');
if($mos=='nao'){

$customerClass = GetClass('ISC_CUSTOMER');
if(!$customerClass->GetCustomerId()) {
$GLOBALS['HideActionAdd'] = "none;";
}else{
$GLOBALS['HideActionAdd'] = "";
}

}else{
$GLOBALS['HideActionAdd'] = '';
}


							$GLOBALS['Estoque'] = 'none';

						} else {

							$GLOBALS['HideActionAdd'] = 'none';

							$GLOBALS['Estoque'] = '';

						}

					$GLOBALS['ProductThumb'] = ImageThumb($row, ProdLink($row['prodname']));

					$GLOBALS['HideProductVendorName'] = 'display: none';
					$GLOBALS['ProductVendor'] = '';
					if(GetConfig('ShowProductVendorNames') && $row['prodvendorid'] > 0) {
						$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');
						if(isset($vendorCache[$row['prodvendorid']])) {
							$GLOBALS['ProductVendor'] = '<a href="'.VendorLink($vendorCache[$row['prodvendorid']]).'">'.isc_html_escape($vendorCache[$row['prodvendorid']]['vendorname']).'</a>';
							$GLOBALS['HideProductVendorName'] = '';
						}
					}

					$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideCategoryNewProducts");
				}

				// Showing the syndication option?
				if(GetConfig('RSSNewProducts') != 0 && GetConfig('RSSCategories') != 0 && GetConfig('RSSSyndicationIcons') != 0) {
					$GLOBALS['ISC_LANG']['CategoryNewProductsFeed'] = sprintf(GetLang('CategoryNewProductsFeed'), $GLOBALS['CatName']);
					$GLOBALS['SNIPPETS']['SideCategoryNewProductsFeed'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideCategoryNewProductsFeed");
				}
			}
			else {
				$GLOBALS['HideSideCategoryNewProductsPanel'] = "none";
				$this->DontDisplay = true;
			}

			$GLOBALS['SNIPPETS']['SideNewProducts'] = $output;
		}
	}