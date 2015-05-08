<?php


	CLASS ISC_SIDECATEGORYTOPSELLERS_PANEL extends PANEL
	{
		public $cacheable = true;
		public $cacheId = "categories.products.sidetopsellers";

		public function __construct()
		{
			$this->cacheId .= ".".$GLOBALS['CatId'];
		}

		public function SetPanelSettings()
		{
			$count = 1;
			$output = "";

			if(!GetConfig('ShowProductRating')) {
				$GLOBALS['HideProductRating'] = "display: none";
			}

			$categorySql = $GLOBALS['ISC_CLASS_CATEGORY']->GetCategoryAssociationSQL();
			$query = "
				SELECT p.*, FLOOR(prodratingtotal/prodnumratings) AS prodavgrating, pi.*, ".GetProdCustomerGroupPriceSQL()."
				FROM [|PREFIX|]products p
				LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1)
				WHERE p.prodnumsold > '0' AND p.prodvisible='1' ".$categorySql."
				ORDER BY p.prodnumsold DESC
			";
			$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit(0, 5);

			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {
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

					if($count == 1) {
						$snippet = "SideTopSellersFirst";
					}
					else {
						$snippet = "SideTopSellers";
					}

					$GLOBALS['ProductThumb'] = ImageThumb($row, ProdLink($row['prodname']));
					$GLOBALS['ProductId'] = $row['productid'];
					$GLOBALS['ProductNumber'] = $count++;
					$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);
					$GLOBALS['ProductLink'] = ProdLink($row['prodname']);
//modificacao parcelamento
						$GLOBALS['FreteDestaques'] = FreteTipo($row['productid']);
						$GLOBALS['ProDestaques'] = simulador_de_rodape($row['productid']);
					// Determine the price of this product
					$GLOBALS['ProductPrice'] = CalculateProductPrice($row);

					if (isId($row['prodvariationid']) || trim($row['prodconfigfields'])!='' || $row['prodeventdaterequired'] == 1) {
						$GLOBALS['ProductURL'] = ProdLink($row['prodname']);
						$GLOBALS['ProductAddText'] = GetLang('ProductChooseOptionLink');
					} else {
						$GLOBALS['ProductURL'] = CartLink($row['productid']);
						$GLOBALS['ProductAddText'] = GetLang('ProductAddToCartLink');
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

					$GLOBALS['ProductNumber'] = $count++;
			$GLOBALS['ProductId'] = $row['productid'];
			$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);

			// Determine the price of this product
			$GLOBALS['ProductPrice'] = CalculateProductPrice($row);
			
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

			
			
			//modificacao parcelamento
			$GLOBALS['FreteDestaques'] = FreteTipo($row['productid']);
			$GLOBALS['ProDestaques'] = simulador_de_rodape($row['productid']);
			
			
			
			$GLOBALS['ProductRating'] = (int)$row['prodavgrating'];
			$GLOBALS['ProductLink'] = ProdLink($row['prodname']);
			$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet($snippet);
				}

				// if only one product then we need to clear the list by adding an empty list item otherwise the layout can be broken
				if ($count == 2) {
					$output .= "<li></li>";
				}
			}
			else {
				$GLOBALS['HideSideCategoryTopSellersPanel'] = "none";
				$this->DontDisplay = true;
			}

			$GLOBALS['SNIPPETS']['SideTopSellers'] = $output;
		}
	}