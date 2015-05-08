<?php

	CLASS ISC_SIDECATEGORYLIST_PANEL extends PANEL
	{
		public function SetPanelSettings()
		{
			$output = "";
			$categories = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('RootCategories');

			if (!isset($categories[0])) {
				$this->DontDisplay = true;
				return;
			}

			foreach($categories[0] as $rootCat) {
				// If we don't have permission to view this category then skip
				if(!CustomerGroupHasAccessToCategory($rootCat['categoryid'])) {
					continue;
				}


				$GLOBALS['SubCategoryList'] = $this->GetSubCategory($categories, $rootCat['categoryid']);
				$GLOBALS['LastChildClass']='';
				$GLOBALS['CategoryName'] = isc_html_escape($rootCat['catname']);
				$GLOBALS['CategoryLink'] = CatLink($rootCat['categoryid'], $rootCat['catname'], true);
				$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideCategoryList");

			}

			if(!$output) {
				$this->DontDisplay = true;
				return;
			}

			$GLOBALS['SNIPPETS']['SideCategoryList'] = $output;
		}


		/**
		* get the html for sub category list
		*
		* @param array $categories the array of all categories in a tree structure
		* @param int $parentCatId the parent category ID of the sub category list
		*
		* return string the html of the sub category list
		*/
		private function GetSubCategory($categories, $parentCatId)
		{

			$output = '';
			//if there is sub category for this parent cat
			if (isset($categories[$parentCatId]) && !empty($categories[$parentCatId])) {
				$i=1;
				foreach ($categories[$parentCatId] as $subCat) {
					// If we don't have permission to view this category then skip
					if (!CustomerGroupHasAccessToCategory($subCat['categoryid'])) {
						continue;
					}
					$catLink = CatLink($subCat['categoryid'], $subCat['catname'], false);
					$catName = isc_html_escape($subCat['catname']);

					$GLOBALS['SubCategoryList'] = $this->GetSubCategory($categories, $subCat['categoryid']);

					//set the class for the last category of its parent category
					$GLOBALS['LastChildClass']='';
					if($i == count($categories[$parentCatId])) {
						$GLOBALS['LastChildClass']='LastChild';
					}
					$i++;

					$GLOBALS['CategoryName'] = $catName;
					$GLOBALS['CategoryLink'] = $catLink;
					$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("SideCategoryList");
				}
			}
			if ($output!='') {
				$output = '<ul>'.$output.'</ul>';
			}
			return $output;
		}
	}