<?php
class ISC_SIDECATEGORYSHOPBYPRICE_PANEL extends PANEL
{
	public function setPanelSettings()
	{
		$output = "";
		$lastend = 0;

		$categoryId = $GLOBALS['ISC_CLASS_CATEGORY']->getId();
		$categoryName = $GLOBALS['ISC_CLASS_CATEGORY']->getName();
		$categorySql = $GLOBALS['ISC_CLASS_CATEGORY']->getCategoryAssociationSQL();

		$query = "
			SELECT
				MIN(prodcalculatedprice) AS pmin,
				MAX(prodcalculatedprice) AS pmax
			FROM
				[|PREFIX|]products p
			WHERE
				p.prodvisible='1' AND
				p.prodhideprice=0 "
				. $categorySql . "
			ORDER BY
				p.productid DESC
		";
		$result = $GLOBALS['ISC_CLASS_DB']->query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->fetch($result);
		if(!$row) {
			$this->DontDisplay = true;
			return;
		}

		$min = ceil($row['pmin']);
		$max = ceil($row['pmax']);

		// Is there enough of a variation to show a shop by price panel?
		if ($max - $min <= $min) {
			$this->DontDisplay = true;
			return;
		}

		$diff = (($max - $min) / 5);

		if($diff == 0) {
			$diff = 1;
		}

		for ($i = 0; $i < 5; $i++) {
			if ($lastend == 0) {
				$start = $min + ($diff * $i);
			} else {
				$start = $lastend;
			}

			$end = $start + $diff;

			if($end == $lastend) {
				break;
			}

			if ($lastend == 0) {
				$start = 0;
			}

			$lastend = $end;

			$start = round($start);
			$end = round($end);

			$GLOBALS['PriceLow'] = currencyConvertFormatPrice($start);
			$GLOBALS['PriceHigh'] = currencyConvertFormatPrice($end);
			$GLOBALS['PriceLink'] = isc_html_escape(catLink($categoryId, $categoryName, false, array(
				'price_min' => $start,
				'price_max' => $end,
				'sort' => $GLOBALS['ISC_CLASS_CATEGORY']->getSort()
			)));
			$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->getSnippet("ShopByPriceItem");
		}
		$GLOBALS['SNIPPETS']['SideCategoryShopByPrice'] = $output;
	}
}