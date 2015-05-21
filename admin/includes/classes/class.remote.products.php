<?php

	if (!defined('ISC_BASE_PATH')) {
		die();
	}

	require_once(ISC_BASE_PATH.'/lib/class.xml.php');

	class ISC_ADMIN_REMOTE_PRODUCTS extends ISC_XML_PARSER
	{
		public function __construct()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('products');
			//$this->xml();
		}

		public function HandleToDo()
		{
			/**
			 * Convert the input character set from the hard coded UTF-8 to their
			 * selected character set
			 */
			convertRequestInput();

			$what = isc_strtolower(@$_REQUEST['w']);

			switch ($what) {
				case "addcustomfield":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product) || $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->addCustomField();
					}
					exit;
					break;
				case "addproductfield":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->addProductField();
					}
					exit;
					break;
				case 'viewaffectedvariations':
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Products)) {
						$this->viewAffectedVariations();
					}
					exit;
					break;
				case "searchyoutube":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product) || $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->searchYouTube();
					}
					exit;
					break;
				case "watchyoutubevideo":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product) || $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->watchYouTubeVideo();
					}
					exit;
					break;
				case "getyoutubevideos":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product) || $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->getYouTubeVideos();
					}
					exit;
					break;
				case "getsourceproductimages":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product) || $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->getSourceProductImages();
					}
					exit;
				case "getsourceimagemanager":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Create_Product) || $GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Edit_Products)) {
						$this->getSourceImageManager();
					}
					exit;
					break;
				case "showprocessimages":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Settings)) {
						$this->showProcessImages();
					}
					exit;
					break;
				case "processimages":
					if ($GLOBALS['ISC_CLASS_ADMIN_AUTH']->HasPermission(AUTH_Manage_Settings)) {
						$this->processProductImages();
					}
					exit;
					break;
			}
		}

		/**
		* This function takes a series of video IDs and returns their data from youtube
		*
		* @see ISC_YOUTUBE
		* @return void
		*/
		private function getYouTubeVideos()
		{
			GetLib('class.youtube');
			GetLib('class.json');

			$videos = explode(',', $_GET['videos']);

			if(empty($videos)) {
				ISC_JSON::output(GetLang('VideoErrorNoVideos'));
			}

			// make youtube request
			$return = '';
			$youtube = new ISC_YOUTUBE;

			foreach($videos as $videoId) {
				if(!$youtube->loadVideoById($videoId)) {
					ISC_JSON::output(GetLang('VideoErrorCantLoadYouTube'));
				}

				$return .= $this->parseVideoRow($youtube->requestResult);
			}

			// return results
			ISC_JSON::output('Videos returned successfully.', true, array('html' => $return));
		}

		/**
		* This function takes a search keyword, uses it to search for youtube videos and outputs any HTML results
		*
		* @see ISC_YOUTUBE
		* @return void
		*/
		private function searchYouTube()
		{
			GetLib('class.youtube');
			GetLib('class.json');

			// get keywords, if none, return
			if(!isset($_GET['keywords'])) {
				ISC_JSON::output(GetLang('VideoNoSearchTerms'));
			}

			$keywords = trim($_GET['keywords']);

			$pageNumber = 1;

			if(isset($_GET['page'])) {
				$pageNumber = (int)$_GET['page'];
				$pageNumber = max(1, $pageNumber);
			}

			// make youtube request
			$youtube = new ISC_YOUTUBE;

			// check to see if they're requesting a specific video

			if(preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9\-\_]*)/', $keywords, $matches)) {
				$videoId = $matches[1];

				if(!$youtube->loadVideoById($videoId)) {
					ISC_JSON::output(GetLang('VideoErrorCantLoadYouTube'));
				}

				$return = $this->parseVideoRow($youtube->requestResult);

			} else {
				if(!$youtube->search($keywords, $pageNumber)) {
					ISC_JSON::output(GetLang('VideoErrorCantLoadSearchYouTube'));
				}

				$return = '';

				foreach ($youtube->requestResult->entry as $video) {
					$return .= $this->parseVideoRow($video);
				}

				// get the thumbnail image
				$namespaces = $youtube->requestResult->getNameSpaces(true);

				// get the media namespace
				$openSearch = $youtube->requestResult->children($namespaces['openSearch']);

				if($openSearch->totalResults > 10) {
					$return .=  $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('YouTubeVideoShowMoreRow');
				}
			}

			// return results
			ISC_JSON::output('Videos returned successfully.', true, array('html' => $return, 'nextpage' => ($pageNumber+1)));
		}

		/**
		* This function takes in a SimpleXML Object, sets the template variables and parses the template based on it.
		* The expected object is a 'entry' node from an ATOM feed from YouTube.
		*
		* @param SimpleXMLElement $video An 'entry' node from an ATOM feed.
		*/
		private function parseVideoRow ($video)
		{
			$rating      = '';
			$ratingStars = '';
			$viewCount   = '0';

			// get the thumbnail image
			$namespaces = $video->getNameSpaces(true);

			// get the media namespace
			$media = $video->children($namespaces['media']);

			// get the gd namespace, which contains information about the video ratings
			$ratings = $video->children($namespaces['gd']);

			// get the yt namespace, which contains information abouts the video statistics and video ID
			$stats = $video->children($namespaces['yt']);

			$thumbnail = $media->group->thumbnail->attributes();
			$length    = $media->group->content->attributes();
			$videoInfo = $media->group->children($namespaces['yt']);
			$duration  = $videoInfo->duration->attributes();

			// finding the video ID can be tricky
			$videoId = trim((string)@$videoInfo->videoid);

			if(empty($videoId)) {
				$videoId = @$video->id;
				if(!empty($videoId)) {
					$videoId = str_replace('http://gdata.youtube.com/feeds/api/videos/', '', $videoId);
				}
			}

			// the duration of the vieo is given in seconds, we want to format it into minutes
			$length = date('G:i:s', (int)$duration['seconds']);

			// if it's less than an hour, don't show zero for the hours
			if(substr($length,0, 2) == '0:') {
				$length = substr($length, 2);
			}

			// not all videos come with statistics, so if there is none, just ignore this code block
			if(isset($stats->statistics)) {
				$statsInfo = $stats->statistics->attributes();
				$viewCount = $statsInfo['viewCount'];
			}

			// not all videos come with rating information :(
			if(isset($ratings->rating)) {
				$rating = $ratings->rating->attributes();
				$averageRating = (float)$rating['average'];
				$ratingNumber = round($averageRating, 0);
				$ratingNumber = min($ratingNumber, 5);
				$ratingNumber = max($ratingNumber, 1);

				for($i=1;$i<=$ratingNumber;$i++) {
					$ratingStars .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('RatingOn');
				}

				for($i=1;$i<=(5-$ratingNumber);$i++) {
					$ratingStars .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('RatingOff');
				}
			}

			// set up the video summary, we need to make sure it's a string and that it's not too long
			$summary = (string)$media->group->description;
			if(strlen($summary) > 85) {
				$summary = substr($summary, 0, 85) . "...";
			}

			// set up the video title, we need to make sure it's a string and that it's not too long
			$title = (string)$video->title;
			if(strlen($title) > 25) {
				$title = substr($title, 0, 23) . "...";
			}

			$GLOBALS['videoLength']    = $length;
			$GLOBALS['videoRating']    = $ratingStars;
			$GLOBALS['videoId']        = $videoId;
			$GLOBALS['videoViews']     = number_format($viewCount);
			$GLOBALS['videoTitle']     = isc_html_escape($title);
			$GLOBALS['videoTitleFull'] = isc_html_escape((string)$video->title);
			$GLOBALS['videoImage']     = (string)$thumbnail['url'];
			$GLOBALS['videoSummary']   = isc_html_escape($summary);
			$GLOBALS['videoSummaryFull'] = isc_html_escape((string)$media->group->description);

			$html = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('YouTubeVideoRow');

			// IE doesn't like new lines in lists at all, it adds weird spacing between list items.
			$html = str_replace(array("\r", "\n"), '', $html);

			return $html;
		}

		/**
		* This function takes a video ID for a youtube video and outputs the embed HTML.
		* The output of this function is expected to be displayed in a modal window
		*
		* @return void
		*/
		private function watchYouTubeVideo ()
		{
			$GLOBALS['videoId'] = urlencode($_GET['videoid']);
			echo $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('EmbeddedYouTubeVideo');
		}
		private function showProcessImages ()
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('settings');
			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.images.process");
			$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
			exit;
		}

		private function getSourceProductImages ()
		{
			GetLib('class.json');

			if(isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			} else {
				$page = 1;
			}

			$limit = 10;
			$start = ($page * $limit) - $limit;
			$message = '';
			$paging = '';

			if(isset($_GET['searchterm'])) {
				$query = 'select *, p.prodname as productname from `[|PREFIX|]product_images` as pi
				inner join `[|PREFIX|]products`  as p on pi.imageprodid=p.productid
				inner join `[|PREFIX|]product_search`  as ps on ps.productid=p.productid
					where MATCH (ps.prodname) AGAINST ("' . $GLOBALS['ISC_CLASS_DB']->Quote($_GET['searchterm']) .'")
				limit ' . $start . ', ' . $limit;

				$numRows = $GLOBALS['ISC_CLASS_DB']->FetchOne('select count(*) from `[|PREFIX|]product_images` as pi
				inner join `[|PREFIX|]products`  as p on pi.imageprodid=p.productid
				inner join `[|PREFIX|]product_search`  as ps on ps.productid=p.productid
					where MATCH (ps.prodname) AGAINST ("' . $GLOBALS['ISC_CLASS_DB']->Quote($_GET['searchterm']) .'")');

			} else {
				$query = 'select * from `[|PREFIX|]product_images` as pi inner join `[|PREFIX|]products`  as p on pi.imageprodid=p.productid limit ' . $start . ', ' . $limit;
				$numRows = $GLOBALS['ISC_CLASS_DB']->FetchOne('select count(*) from `[|PREFIX|]product_images` as pi inner join `[|PREFIX|]products`  as p on pi.imageprodid=p.productid');
			}


			$imageIterator = new ISC_PRODUCT_IMAGE_ITERATOR($query);

			$numPages = ceil(($numRows/$limit));
			$returnImages = array();
			foreach($imageIterator as $imageId=>$image) {

				try {
					$zoomSize = $image->getResizedFileDimensions(ISC_PRODUCT_IMAGE_SIZE_ZOOM, false);
					$productName = $image->getProductName();
					if(strlen($productName) > 21) {
						$productName = isc_substr($productName, 0, 21) . '...';
					}

					$row = array(
									'url' => $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, true),
									'id' => 'productimage_' . $imageId,
									'productname' => $productName,
									'zoom' => $image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, false),
									'zoomwidth' => $zoomSize[0],
									'zoomheight' => $zoomSize[1],
									);
					$returnImages[] = $row;
				} catch (Exception $exception) {
					//
				}
			}

			if($numRows > $limit) {
				$paging = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);

				$pagingURL = 'remote.php?remoteSection=products&w=getsourceproductimages';
				if(isset($_GET['searchterm'])) {
					$pagingURL .= "&searchterm=" . urlencode($_GET['searchterm']);
				}

				$paging .= BuildPagination($numRows, $limit, $page, $pagingURL);
			}

			if($numRows == 0) {
				if(isset($_GET['searchterm'])) {
					$message = str_replace('{searchterms}', isc_html_escape($_GET['searchterm']), GetLang('ProductImagesNoSearchImages'));
				} else {
					$message = GetLang('ProductImagesNoImages');
				}
			}

			ISC_JSON::output($message, true, array('images' => $returnImages, 'paging' => $paging, /*'query' => $query*/ ));
			exit;
		}

		private function getSourceImageManager ()
		{
			GetLib('class.json');
			GetLib('class.imagedir');

			if(isset($_GET['page'])) {
				$currentPage = (int)$_GET['page'];
			} else {
				$currentPage = 1;
			}

			$perPage = 10;
			$start = ($currentPage * $perPage) - $perPage;
			$paging = '';
			$message = '';

			$imageDir = new ISC_IMAGEDIR();
			$dirCount = $imageDir->CountDirItems();

			if($imageDir->CountDirItems() == 0){
				ISC_JSON::output(GetLang('ProductImagesNoImagesImageManager'), true, array('images' => array(), 'paging' => $paging,));
				die();
			}

			if ($perPage > 0) {
				$imageDir->start = ($perPage * $currentPage) - $perPage;
				$imageDir->finish = ($perPage * $currentPage);
			}

			$numPages = ceil($dirCount / $perPage);

			// generate list of images
			$images = $imageDir->GetImageDirFiles();
			$imagesList = "";
			foreach ($images as $image) {
				$imageName = $image['name'];
				if(strlen($imageName) > 21) {
					$imageName = isc_substr($imageName, 0, 21) . '...';
				}
				$returnImages[] = array(
									'url' => 'uploaded_images/' . $image['name'],
									'id' => 'imagemanager_' . md5($image['name']),
									'productname' => isc_html_escape($imageName),
									'zoom' => $image['url'],
									'zoomwidth' => $image['width'],
									'zoomheight' => $image['height'],
								);
			}

			if($dirCount > $perPage) {
				$paging = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $currentPage, $numPages);
				$paging .= BuildPagination($dirCount, $perPage, $currentPage, 'remote.php?remoteSection=products&w=getsourceimagemanager');
			}

			ISC_JSON::output($message, true, array('images' => $returnImages, 'paging' => $paging, /*'query' => $query*/ ));
			exit;
		}

		private function processProductImages ()
		{
			$db = &$GLOBALS['ISC_CLASS_DB'];
			GetLib('class.json');

			$query = "
				SELECT
					(SELECT COUNT(*) FROM [|PREFIX|]product_images) AS prodimagecount,
					(SELECT COUNT(*) FROM [|PREFIX|]product_variation_combinations WHERE vcimage != '') AS varimagecount
			";

			$result = $db->Query($query);
			$countrow = $db->Fetch($result);
			$total = $countrow['prodimagecount'] + $countrow['varimagecount'];

			$start = max(0, @(int)$_POST['start']);
			$limit = 10;
			$completed = 0;

			if ($start < $countrow['prodimagecount']) {
				$imageIterator = new ISC_PRODUCT_IMAGE_ITERATOR('select * from `[|PREFIX|]product_images` limit ' . $start . ', ' . $limit);

				foreach($imageIterator as $imageId=>$image) {
					try {
						// the first argument to saveToDatabase is $generateImages. If true (is by default), the images will be regenerated
						$image->saveToDatabase();
					} catch (ISC_PRODUCT_IMAGE_SOURCEFILEDOESNTEXIST_EXCEPTION $exception) {
						//
					}
					++$completed;
				}
			}

			// was there any remaining 'items' to process for this iteration? start on variation images
			$var_limit = $limit - $completed;

			if ($var_limit > 0) {
				$var_start = $start - $countrow['prodimagecount'];

				$query = '
					SELECT
						GROUP_CONCAT(CAST(combinationid AS CHAR)) AS combinations,
						vcimage
					FROM
						[|PREFIX|]product_variation_combinations
					WHERE
						vcimage != ""
					GROUP BY
						vcimage
					LIMIT
						' . $var_start . ', ' . $var_limit;

				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					try {
						$image = new ISC_PRODUCT_IMAGE();
						$image->setSourceFilePath($row['vcimage']);

						$updatedVariation = array(
							'vcimagezoom' 	=> $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, false),
							'vcimagestd' 	=> $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_STANDARD, true, false),
							'vcimagethumb' 	=> $image->getResizedFilePath(ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL, true, false)
						);

						$GLOBALS['ISC_CLASS_DB']->UpdateQuery('product_variation_combinations', $updatedVariation, 'combinationid IN (' . $row['combinations'] . ')');
					}
					catch (Exception $ex) {
					}

					++$completed;
				}
			}

			ISC_JSON::output('', true, array('completed' => $completed, 'start' => (int)$start, 'total'=> (int)$total));
			exit;
		}

		private function addCustomField()
		{
			if (!array_key_exists('nextId', $_REQUEST)) {
				print '';
				exit;
			}

			$GLOBALS['ISC_ADMIN_CLASS_PRODUCT'] = GetClass('ISC_ADMIN_PRODUCT');
			$GLOBALS['CustomFieldKey'] = $_REQUEST['nextId'];
			$GLOBALS['CustomFieldName'] = '';
			$GLOBALS['CustomFieldValue'] = '';
			$GLOBALS['CustomFieldLabel'] = $GLOBALS['ISC_ADMIN_CLASS_PRODUCT']->GetFieldLabel($_REQUEST['nextId']+1, GetLang('CustomField'));

			print $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CustomFields');
			exit;
		}

		private function addProductField()
		{
			if (!isset($_REQUEST['nextId'])) {
				print '';
				exit;
			}

			$GLOBALS['ISC_ADMIN_CLASS_DB'] = GetClass('ISC_ADMIN_PRODUCT');
			$GLOBALS['ProductFieldName'] = GetLang('FieldName');
			$GLOBALS['FieldNameClass'] = 'FieldHelp';

			$GLOBALS['ProductFieldType'] = 'text';
			$GLOBALS['ProductFieldFileType'] = GetLang('FieldFileType');
			$GLOBALS['FileTypeClass'] = 'FieldHelp';
			$GLOBALS['ProductFieldFileSize'] = GetLang('FieldFileSize');
			$GLOBALS['FileSizeClass'] = 'FieldHelp';
			$GLOBALS['HideFieldFileType'] = 'display:none;';

			$GLOBALS['ProductFieldRequired'] = '';
			$GLOBALS['ProductFieldKey'] =(int)$_REQUEST['nextId'];
			$GLOBALS['ProductFieldLabelNumber'] = $GLOBALS['ProductFieldKey'] +1;

			print $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ProductFields');
			exit;
		}

		private function viewAffectedVariations()
		{
			/**
			 * Make sure we have our variation
			 */
			$variatonIdx = array();
			if (isset($_REQUEST['variationIdx'])) {
				$variatonIdx = explode(',', $_REQUEST['variationIdx']);
				$variatonIdx = array_filter($variatonIdx, 'isId');
			}

			if (empty($variatonIdx)) {
				print '';
				exit;
			}

			/**
			 * Also make sure that we were given a type (either 'edit' or 'delete') because without this then we do not
			 * know what is being removed
			 */
			$type = '';
			if (isset($_REQUEST['actionType'])) {
				$type = strtolower($_REQUEST['actionType']);
			}

			/**
			 * See if we were passed any option value Ids to cross check with
			 */
			$valueIdx = array();
			if (isset($_REQUEST['optionValueIdx'])) {
				$valueIdx = explode(',', $_REQUEST['optionValueIdx']);
				$valueIdx = array_filter($valueIdx, 'isId');
			}

			$affected = "";
			if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Products)) {
				$canEdit = true;
			} else {
				$canEdit = false;
			}

			switch (strtolower($type)) {
				case 'delete':
				case 'add':

					/**
					 * If we are deleting or adding then just work on the $variatonIdx. 'Add' goes in here aswell because if a value is added then ALL existing combintaions
					 * for that variation are invalid
					 */
					$tmpVarId = null;
					$tmpVarName = '';
					$products = array();

					$query = "SELECT v.variationid, v.vname, p.productid, p.prodname
								FROM [|PREFIX|]product_variations v
								JOIN [|PREFIX|]products p ON v.variationid = p.prodvariationid
								WHERE v.variationid IN(" . implode(',', $variatonIdx) . ")
								ORDER BY v.variationid";

					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
						if (is_null($tmpVarId) || $tmpVarId !== $row['variationid']) {
							if (isId($tmpVarId)) {
								$GLOBALS['ProductName'] = $tmpVarName;
								$GLOBALS['ProductVariationList'] = '<li>' . implode('</li><li>', $products) . '</li>';
								$affected .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('VariationAffectedProducts');
							}

							$tmpVarId = $row['variationid'];
							$tmpVarName = $row['vname'];
							$products = array();
						}

						if ($canEdit) {
							$products[] = '<a class="Action" target="_blank" href="index.php?ToDo=editProduct&amp;productId=' . (int)$row['productid'] . '" title="' . isc_html_escape($row['prodname']) . '">' . isc_html_escape($row['prodname']) . '</a>';
						} else {
							$products[] = isc_html_escape($row['prodname']);
						}
					}

					/**
					 * Get the last one
					 */
					if (!empty($products)) {
						$GLOBALS['ProductName'] = $tmpVarName;
						$GLOBALS['ProductVariationList'] = '<li>' . implode('</li><li>', $products) . '</li>';
						$affected .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('VariationAffectedProducts');
					}

					break;

				case 'edit':

					/**
					 * If we're editing then use the $valueIdx aswell and ONLY use the first element in $variatonIdx as you can only edit one at a time
					 */
					$productId = null;
					$productName = '';
					$variations = array();
					$extraselect = '';

					if (!empty($valueIdx)) {
						$extraselect = " AND o.voptionid NOT IN(" . implode(',', $valueIdx) . ")";
					}

					$query = "SELECT p.productid, p.prodname, c.vcoptionids, GROUP_CONCAT(o.voptionid) AS valueidx
								FROM [|PREFIX|]products p
								JOIN [|PREFIX|]product_variation_options o ON p.prodvariationid = o.vovariationid
								JOIN [|PREFIX|]product_variation_combinations c ON o.vovariationid = c.vcvariationid AND p.productid = c.vcproductid
								WHERE p.prodvariationid = " . (int)$variatonIdx[0] . $extraselect . "
								GROUP BY c.combinationid";

					$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
					while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

						if ($row['valueidx'] == '') {
							continue;
						}

						/**
						 * Explode on the $row['vcoptionids'] and $row['valueidx'] and see if any of the $row['vcoptionids'] exist in $row['valueidx']
						 */
						$selectedValues = explode(',', $row['vcoptionids']);
						$deletedValues = explode(',', $row['valueidx']);
						$selected = false;
						foreach ($selectedValues as $idx) {
							if (in_array($idx, $deletedValues)) {
								$selected = true;
								break;
							}
						}

						/**
						 * Have we been affected by this edit?
						 */
						if ($selected) {
							if (is_null($productId) || $productId !== $row['productid']) {
								if (isId($productId)) {
									if ($canEdit) {
										$GLOBALS['ProductName'] = '<a class="Action" target="_blank" href="index.php?ToDo=editProduct&amp;productId=' . (int)$productId . '" title="' . isc_html_escape($productName) . '">' . isc_html_escape($productName) . '</a>';
									} else {
										$GLOBALS['ProductName'] = isc_html_escape($productName);
									}

									$GLOBALS['ProductVariationList'] = '<li>' . implode('</li><li>', $variations) . '</li>';
									$affected .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('VariationAffectedProducts');
								}

								$productId = $row['productid'];
								$productName = $row['prodname'];
								$variations = array();
							}

							$option = array();
							$sResult = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]product_variation_options o WHERE voptionid IN(" . $row['vcoptionids'] . ")");
							while ($sRow = $GLOBALS['ISC_CLASS_DB']->Fetch($sResult)) {
								$option[] = isc_html_escape($sRow['voname'] . ': ' . $sRow['vovalue']);
							}

							$variations[] = implode(', ', $option);
						}
					}

					/**
					 * Get the last one
					 */
					if (!empty($variations)) {
						if ($canEdit) {
							$GLOBALS['ProductName'] = '<a class="Action" target="_blank" href="index.php?ToDo=editProduct&amp;productId=' . (int)$productId . '" title="' . isc_html_escape($productName) . '">' . isc_html_escape($productName) . '</a>';
						} else {
							$GLOBALS['ProductName'] = isc_html_escape($productName);
						}

						$GLOBALS['ProductVariationList'] = '<li>' . implode('</li><li>', $variations) . '</li>';
						$affected .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('VariationAffectedProducts');
					}

					break;
			}

			$GLOBALS['AffectedProducts'] = $affected;
			$GLOBALS['ProductVariationPopupIntro'] = GetLang('ProductVariationPopup' . ucfirst(strtolower($type)) . 'Intro');
			$GLOBALS["ISC_CLASS_TEMPLATE"]->SetTemplate("products.variation.affected.popup");
			$GLOBALS["ISC_CLASS_TEMPLATE"]->ParseTemplate();
			exit;
		}
	}