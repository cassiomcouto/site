<?php

	CLASS ISC_SIDEPRODUCTRELATED_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{
			if (!isset($GLOBALS['ISC_CLASS_PRODUCT'])) {
				$GLOBALS['ISC_CLASS_PRODUCT'] = GetClass('ISC_PRODUCT');
			}

			$relatedProducts = $GLOBALS['ISC_CLASS_PRODUCT']->GetRelatedProducts();

			if ($relatedProducts != "") {
				$output = "";

				if(GetConfig('EnableProductReviews') == 0) {
					$GLOBALS['HideProductRating'] = "display: none";
				}

				$query = "
					SELECT p.*, FLOOR(p.prodratingtotal/p.prodnumratings) AS prodavgrating, i.*, ".GetProdCustomerGroupPriceSQL()."
					FROM [|PREFIX|]products p
					LEFT JOIN [|PREFIX|]product_images i ON (p.productid = i.imageprodid)
					WHERE p.prodvisible='1' AND p.productid IN (" . $relatedProducts . ") AND i.imageisthumb=1
					".GetProdCustomerGroupPermissionsSQL()."
					ORDER BY prodsortorder ASC
				";

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

						$GLOBALS['ProductId'] = (int) $row['productid'];
						$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);
						$GLOBALS['ProductLink'] = ProdLink($row['prodname']);

						// Determine the price of this product
						$GLOBALS['ProductPrice'] = CalculateProductPrice($row);

						$image = new ISC_PRODUCT_IMAGE();
						$image->populateFromDatabaseRow($row);

						$GLOBALS['ProductThumb'] = imageThumb($row, prodLink($row['prodname']));
						$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideRelatedProducts");
					}

					$GLOBALS['SNIPPETS']['SideProductsRelated'] = $output;
				}
				else {
					// No related products, hide the panel
					$GLOBALS['HideRelatedProductsPanel'] = "none";
					$this->DontDisplay = true;
				}
			}
			else {
				// No related products, hide the panel
				$GLOBALS['HideRelatedProductsPanel'] = "none";
				$this->DontDisplay = true;
			}
		}
	}