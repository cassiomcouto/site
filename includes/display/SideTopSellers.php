<?php

/**

 * Top Selling Products Panel

 *

 * This panel will show the top selling products

 * for the entire store, or if you're viewing

 * a vendor profile, the top selling products

 * for the current vendor.

 */

CLASS ISC_SIDETOPSELLERS_PANEL extends PANEL

{

	/**

	 * Set the panel settings

	 */

	public function SetPanelSettings()

	{

		$count = 1;



		if(!GetConfig('ShowProductRating')) {

			$GLOBALS['HideProductRating'] = "display: none";

		}



		$output = "";



		$vendorRestriction = '';



		// If we're on a vendor page, only show top sellers from this particular vendor

		if(isset($GLOBALS['ISC_CLASS_VENDORS'])) {

			$vendor = $GLOBALS['ISC_CLASS_VENDORS']->GetVendor();

			$vendorRestriction = " AND p.prodvendorid='".(int)$vendor['vendorid']."'";

		}



		$query = "

			SELECT p.*, FLOOR(prodratingtotal/prodnumratings) AS prodavgrating, pi.*, ".GetProdCustomerGroupPriceSQL()."

			FROM [|PREFIX|]products p

			LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1)

			WHERE p.prodnumsold > '0' AND p.prodvisible='1' ".$vendorRestriction."

			".GetProdCustomerGroupPermissionsSQL()."

			ORDER BY p.prodnumsold DESC

		";

		$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit(0, 6);

		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



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



			// Use the SideTopSellersFirst snippet for the first product only

			if($count == 1) {

				$snippet = "SideTopSellersFirst";

			}

			else {

				$snippet = "SideTopSellers";

			}



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



			$GLOBALS['ProductNumber'] = $count++;

			$GLOBALS['ProductId'] = $row['productid'];

			$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);



			// Determine the price of this product

			$GLOBALS['ProductPrice'] = CalculateProductPrice($row);

			

			

			

			

			//modificacao parcelamento

			$GLOBALS['FreteDestaques'] = FreteTipo($row['productid']);

			$GLOBALS['ProDestaques'] = simulador_de_rodape($row['productid']);

			

			

			

			$GLOBALS['ProductRating'] = (int)$row['prodavgrating'];

			$GLOBALS['ProductLink'] = ProdLink($row['prodname']);

			$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet($snippet);

		}



		if ($count == 2) { // if only one product then we need to clear the list by adding an empty list item otherwise the layout can be broken

			$output .= "<li></li>";

		}



		$GLOBALS['SNIPPETS']['SideTopSellers'] = $output;



		if(!$output) {

			$this->DontDisplay = true;

		}

	}

}