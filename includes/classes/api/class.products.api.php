<?php
class API_PRODUCTS extends API_BASE
{
	/**
	* GetProducts
	* Gets a list of products that match the searchinfo parameters sent
	* with the request. These are identical to the options available
	* from the advanced product search in the control panel and that's the
	* exact system we tie into
	*/
	public function Action_GetProducts()
	{
		foreach($this->router->request->details as $field=>$val) {
			foreach($val as $k=>$v) {
				$_REQUEST[$k] = (string)$v;
			}
		}

		$start = 0;
		if(!empty($_REQUEST['start'])) {
			$start = (int)$_REQUEST['start'];
		}
		if(!isset($_REQUEST['noPaging'])) {
			$addLimit = true;
		}
		else {
			$addLimit = false;
		}

		$image = new ISC_PRODUCT_IMAGE(); // autoload helper so we can use exceptions defined in the product image class file
		unset($image);

		$products = GetClass('ISC_ADMIN_PRODUCT');
		$num_products = 0;
		$product_grid = $products->_GetProductList($start, "productid", "asc", $num_products, "", $addLimit);
		$product_array = array();

		if($num_products > 0) {
			while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($product_grid)) {

				$product = &$row;
				// stuff that comes directly from the database may be incomplete -- use the image library to make sure
				try {
					if (!$product['imageid']) {
						// no image present in data so throw an exception just to force the removal of data below in the catch{} block
						throw new ISC_PRODUCT_IMAGE_EXCEPTION();
					}

					$image = new ISC_PRODUCT_IMAGE();
					$image->populateFromDatabaseRow($product);

					// call the image library to make sure resized images are present and then add full urls so they're useful for remote users
					$product['imagefiletiny'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_TINY, true, true, false);
					$product['imagefilethumb'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true, true, false);
					$product['imagefilestd'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, true, false);
					$product['imagefilezoom'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, true, false);

					// call the image library to make sure resized images are present and the sizes are correct
					$product['imagefiletinysize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_TINY));
					$product['imagefilethumbsize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL));
					$product['imagefilestdsize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_STANDARD));
					$product['imagefilezoomsize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_ZOOM));

				} catch (Exception $exception) {
					// some sort of problem when dealing with product images - remove image info from the response
					unset(
						$product['imagefiletiny'],
						$product['imagefilethumb'],
						$product['imagefilestd'],
						$product['imagefilezoom'],
						$product['imagefiletinysize'],
						$product['imagefilethumbsize'],
						$product['imagefilestdsize'],
						$product['imagefilezoomsize'],
						$product['imagedesc'],
						$product['imagedateadded']
					);
				}

				// direct data feed also includes some fields that are irrelevant or unwanted
				unset(
					$product['imagefile'],	// don't provide a link to the non-water-marked image
					$product['imageprodid'],
					$product['imageprodhash'],
					$product['imageisthumb'],
					$product['imagesort']
				);

				array_push($product_array, $row);
			}
		}

		return array(
			'start' => $start,
			'end' => min($start + ISC_PRODUCTS_PER_PAGE, $num_products),
			'numResults' => $num_products,
			'results' => $product_array
		);
	}

	public function Action_GetProduct()
	{
		if(empty($this->router->request->details->productId)) {
			return array();
		}

		$image = new ISC_PRODUCT_IMAGE(); // autoload helper so we can use exceptions defined in the product image class file
		unset($image);

		$productId = (int)$this->router->request->details->productId;
		$productClass = new ISC_PRODUCT($productId);
		$product = $productClass->_product;

		// stuff that comes directly from the database may be incomplete -- use the image library to make sure
		try {
			if (!$product['imageid']) {
				// no image present in data so throw an exception just to force the removal of data below in the catch{} block
				throw new ISC_PRODUCT_IMAGE_EXCEPTION();
			}

			$image = new ISC_PRODUCT_IMAGE();
			$image->populateFromDatabaseRow($product);

			// call the image library to make sure resized images are present and then add full urls so they're useful for remote users
			$product['imagefiletiny'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_TINY, true, true, false);
			$product['imagefilethumb'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true, true, false);
			$product['imagefilestd'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, true, false);
			$product['imagefilezoom'] = $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, true, false);

			// call the image library to make sure resized images are present and the sizes are correct
			$product['imagefiletinysize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_TINY));
			$product['imagefilethumbsize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL));
			$product['imagefilestdsize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_STANDARD));
			$product['imagefilezoomsize'] = implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_ZOOM));

		} catch (Exception $exception) {
			// some sort of problem when dealing with product images - remove image info from the response
			unset(
				$product['imagefiletiny'],
				$product['imagefilethumb'],
				$product['imagefilestd'],
				$product['imagefilezoom'],
				$product['imagefiletinysize'],
				$product['imagefilethumbsize'],
				$product['imagefilestdsize'],
				$product['imagefilezoomsize'],
				$product['imagedesc'],
				$product['imagedateadded']
			);
		}

		// direct data feed also includes some fields that are irrelevant or unwanted
		unset(
			$product['imagefile'],	// don't provide a link to the non-water-marked image
			$product['imageprodid'],
			$product['imageprodhash'],
			$product['imageisthumb'],
			$product['imagesort']
		);

		if(empty($product)) {
			return array();
		}

		$product['prodlink'] = ProdLink($product['prodname']);

		// Fetch any images for the product

		$images = new ISC_PRODUCT_IMAGE_ITERATOR(ISC_PRODUCT_IMAGE::generateGetProductImagesFromDatabaseSql((int)$productId));
		foreach ($images as $image) {
			/** @var $image ISC_PRODUCT_IMAGE */
			$imageisthumb = 0;
			if ($image->getIsThumbnail()) {
				$imageisthumb = 1;
			}

			try {
				$product['images'][] = array(
					'imageid' => $image->getProductImageId(),
					'imagefiletiny' => $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_TINY, true, true, false),
					'imagefilethumb' => $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true, true, false),
					'imagefilestd' => $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, true, false),
					'imagefilezoom' => $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, true, false),
					'imagefiletinysize' => implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_TINY)),
					'imagefilethumbsize' => implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL)),
					'imagefilestdsize' => implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_STANDARD)),
					'imagefilezoomsize' => implode('x', $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_ZOOM)),
					'imageisthumb' => $imageisthumb,
					'imagesort' => $image->getSort(),
					'imagedesc' => $image->getDescription(),
					'imagedateadded' => $image->getDateAdded(),
				);
			} catch (Exception $exception) {
				// skip this image and bring down the count of product images obtained from ISC_PRODUCT
				$product['numimages']--;
			}
		}

		// Fetch the categories this product belongs to
		$trailCategories = array();
		$crumbList = array();
		$query = "
			SELECT c.categoryid, c.catparentlist
			FROM [|PREFIX|]categoryassociations ca
			JOIN [|PREFIX|]categories c ON (c.categoryid=ca.categoryid)
			WHERE ca.productId='".(int)$productId."'
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			if ($row['catparentlist'] == '') {
				$row['catparentlist'] = $row['categoryid'];
			}
			$cats = explode(",", $row['catparentlist']);
			$trailCategories = array_merge($trailCategories, $cats);
			$crumbList[$row['categoryid']] = $row['catparentlist'];
		}

		$trailCategories = implode(",", array_unique($trailCategories));
		$categories = array();
		if ($trailCategories != '') {
			// Now load the names for the parent categories from the database
			$query = "
				SELECT categoryid, catname
				FROM [|PREFIX|]categories
				WHERE categoryid IN (".$trailCategories.")
			";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$categories[$row['categoryid']] = $row['catname'];
			}
		}

		// Now we have all of the information we need to build the trails, lets actually build them
		foreach ($crumbList as $productcatid => $trail) {
			$cats = explode(',', $trail);
			$catName = '';
			$catLink = CatLink($productcatid, $categories[$productcatid]);
			foreach ($cats as $categoryid) {
				if(isset($categories[$categoryid])) {
					if($catName) {
						$catName .= ' &raquo; ';
					}
					$catName .= $categories[$categoryid];
				}
			}
			$product['categories'][] = array(
				'name' => $catName,
				'link' => $catLink,
				'id' => $productcatid
			);
		}

		if($product['prodvariationid'] > 0) {
			if ($product['prodsaleprice'] != 0) {
				$variationBasePrice = $product['prodsaleprice'];
			}
			else {
				$variationBasePrice = $product['prodprice'];
			}
			foreach($productClass->_prodvariationcombinations as $variation) {
				$variationPrice = CurrencyConvertFormatPrice(CalcProductVariationPrice($variationBasePrice, $variation['vcpricediff'], $variation['vcprice'], $product));
				$variationWeight = FormatWeight(CalcProductVariationWeight($product['prodweight'], $variation['vcweightdiff'], $variation['vcweight']), true);

				$variationName = '';
				$options = explode(',', $variation['vcoptionids']);
				foreach($options as $optionId) {
					$option = $productClass->_prodvariationoptions[$optionId];
					$variationName .= $option['voname'].': '.$option['vovalue'].', ';
				}
				$variationName = rtrim($variationName, ', ');
				$variationRow = array(
					'name' => $variationName,
					'id' => $variation['combinationid'],
					'price' => $variationPrice,
					'sku' => $variation['vcsku'],
					'weight' => $variationWeight,
				);

				if($product['prodinvtrack'] == 2) {
					$variationRow['stock'] = $variation['vcstock'];
				}

				if($variation['vcimagethumb']) {
					$variationRow['thumb'] = GetConfig('ShopPathNormal').'/'.GetConfig('ImageDirectory').'/'.$variation['vcimagethumb'];
				}

				if($variation['vcimagestd']) {
					$variationRow['standard'] = GetConfig('ShopPathNormal').'/'.GetConfig('ImageDirectory').'/'.$variation['vcimagestd'];
				}

				if($variation['vcimagezoom']) {
					$variationRow['image'] = GetConfig('ShopPathNormal').'/'.GetConfig('ImageDirectory').'/'.$variation['vcimagezoom'];
				}

				$product['variations'][] = $variationRow;
			}
		}

		return $product;
	}
}