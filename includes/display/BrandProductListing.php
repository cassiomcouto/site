<?php



	CLASS ISC_BRANDPRODUCTLISTING_PANEL extends PANEL

	{

		public function SetPanelSettings()

		{

			$count = 0;

			$output = "";

			$products = array();



			$GLOBALS['BrandsMessage'] = '';



			// If we're showing the "All brands" page then we to display a list of all the brands

			if($GLOBALS['ISC_CLASS_BRANDS']->ShowingAllBrands()) {



				// Output sub-categories

				$GLOBALS['SNIPPETS']['SubBrands'] = "";

				$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]brands ORDER BY brandname ASC");



				if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {



					// Check to see if we need to add in place holder images or if we are just displaying text

					if (!($rtn = $GLOBALS['ISC_CLASS_DB']->Fetch($GLOBALS['ISC_CLASS_DB']->Query("SELECT COUNT(*) AS Total FROM [|PREFIX|]brands WHERE brandimagefile != ''"))) || !$rtn['Total']) {

						$useImages = false;

					} else {

						$useImages = true;

						if (GetConfig('BrandDefaultImage') !== '') {

							$defaultImage = GetConfig('ShopPath') . '/' . GetConfig('BrandDefaultImage');

						} else {

							$defaultImage = $GLOBALS['IMG_PATH'].'/BrandDefault.gif';

						}

					}

					

					



					for ($i=1; ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)); $i++) {

						$GLOBALS['SubBrandName'] = isc_html_escape($row['brandname']);

						$GLOBALS['SubBrandLink'] = BrandLink($row['brandname']);

						if ($useImages) {

							if ($row['brandimagefile'] !== '') {

								$GLOBALS['SubBrandImage'] = GetConfig('ShopPath') . '/' . GetConfig('ImageDirectory') . '/' . $row['brandimagefile'];

							} else {

								$GLOBALS['SubBrandImage'] = $defaultImage;

							}



							$GLOBALS['SNIPPETS']['SubBrands'] .= '<li style="width: ' . GetConfig('BrandImageWidth') . 'px; height: ' . (GetConfig('BrandImageHeight')+50) . 'px">' . $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SubBrandItemImage") . '</li>';



							if ($i%GetConfig('BrandPerRow') === 0) {

								$GLOBALS['SNIPPETS']['SubBrands'] .= '<li style="float:none; clear:both;"></li>';

							}

						} else {

							$GLOBALS['SNIPPETS']['SubBrands'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SubBrandItem");

						}

					}



					if ($useImages) {

						$output = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SubBrandsGrid");

					} else {

						$output = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SubBrands");

					}



					$output = $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($output, $GLOBALS['SNIPPETS']);

					$GLOBALS['BrandsMessage'] = $output;



				} else {

					$GLOBALS['BrandsMessage'] = GetLang('ViewAllBrandsInstructions');

				}



				$GLOBALS['BrandProductListing'] = "<li>&nbsp;</li>";

				$GLOBALS['HideBrandProductListing'] = "none";

				return;

			}



			// Load the products into the reference array

			$GLOBALS['ISC_CLASS_BRANDS']->GetProducts($products);

			$GLOBALS['BrandProductListing'] = "";



			if($GLOBALS['BrandName'] == "") {

				// We're on the 'All Brands' page and the brands list is on the right

				$GLOBALS['ChooseBrandFromList'] = sprintf(GetLang('ChooseBrandFromList'), GetLang('Right'));

				$GLOBALS['BrandProductListing'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("BrandMainPageMessage");

			}

			else {

				// Show products for a specific brand

				if(!GetConfig('ShowProductRating')) {

					$GLOBALS['HideProductRating'] = "display: none";

				}



				$GLOBALS['AlternateClass'] = '';

				foreach($products as $row) {



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



					$GLOBALS['ProductId'] = (int) $row['productid'];

					$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);

					$GLOBALS['ProductLink'] = ProdLink($row['prodname']);

					$GLOBALS['ProductRating'] = (int) $row['prodavgrating'];



					// Determine the price of this product

					$GLOBALS['ProductPrice'] = CalculateProductPrice($row);




						//modificacao parcelamento

						$GLOBALS['FreteDestaques'] = FreteTipo($row['productid']);

						$GLOBALS['ProDestaques'] = simulador_de_rodape($row['productid']);

					$GLOBALS['ProductThumb'] = ImageThumb($row, ProdLink($row['prodname']));



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



					$GLOBALS['HideProductVendorName'] = 'display: none';

					$GLOBALS['ProductVendor'] = '';

					if(GetConfig('ShowProductVendorNames') && $row['prodvendorid'] > 0) {

						$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');

						if(isset($vendorCache[$row['prodvendorid']])) {

							$GLOBALS['ProductVendor'] = '<a href="'.VendorLink($vendorCache[$row['prodvendorid']]).'">'.isc_html_escape($vendorCache[$row['prodvendorid']]['vendorname']).'</a>';

							$GLOBALS['HideProductVendorName'] = '';

						}

					}

                $GLOBALS['FreteBrand'] = FreteTipo($row['productid']);

                $GLOBALS['ProBrand'] = simulador_de_rodape($row['productid']);

					$GLOBALS['BrandProductListing'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("BrandProductsItem");

				}



				if($GLOBALS['ISC_CLASS_BRANDS']->GetNumProducts() == 0) {

					// There are no products in this category

					$GLOBALS['BrandsMessage'] = GetLang('NoProductsInBrand');

					$GLOBALS['BrandProductListing'] = "<li>&nbsp;</li>";

					$GLOBALS['HideBrandProductListing'] = "none";

				}

			}

		}

	}