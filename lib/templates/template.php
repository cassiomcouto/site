<?php
ini_set("memory_limit","160M");
	class TEMPLATE
	{
		const TYPE_LAYOUT = 'layouts';
		const TYPE_PANEL = 'panels';
		const TYPE_SNIPPET = 'snippets';

		// Private variables
		public $_tplName = "";

		private $_tplData = "";

		private $_tplPageTitle = "";

		private $_tplMetaKeywords = "";

		private $_tplMetaDescription = "";

		private $langVar = '';

		private $templateDirectories = array();

		/**
		* @var string the directory with the template panel php files in it
		*/
		public $panelPHPDir = '';

		/**
		* @var string the extension of templates in the $this->baseDir
		*/
		public $templateExt = 'html';

		/**
		* @var string $userAgent The user agent for requesting files if external
		* includes are used
		*/
		public $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';

		public $frontEnd = false;

		public $panelClassPrefix = PRODUCT_ID;

		/**
		* class constructor
		* @return void
		*/
		public function __construct($var)
		{
			$GLOBALS["SNIPPETS"] = array();

			$this->langVar = $var;

			if(!isset($GLOBALS["SNIPPETS"])) {
				$GLOBALS["SNIPPETS"] = array();
			}

			if(!isset($GLOBALS[$this->langVar])) {
				$GLOBALS[$this->langVar] = array();
			}

			// Setup default META data
			$this->SetMetaKeywords(GetConfig('MetaKeywords'));
			$this->SetMetaDescription(GetConfig('MetaDesc'));
		}

		public function FrontEnd()
		{
			$this->frontEnd = true;
			$this->ParseSettingsLangFile();
			$this->ParseCommonLangFile();
			$this->ParseFrontendLangFile();
			$this->ParseModuleLangFile();
		}

		public function ParseSettingsLangFile()
		{
			$settingsLangFile = dirname(__FILE__) . "/../../language/".GetConfig('Language')."/settings.ini";
			$this->ParseLangFile($settingsLangFile);
		}

		public function ParseCommonLangFile()
		{
			$commonLangFile = dirname(__FILE__) . "/../../language/".GetConfig('Language')."/common.ini";
			$this->ParseLangFile($commonLangFile);
		}

		public function ParseFrontendLangFile()
		{
			$frontLangFile = dirname(__FILE__) . "/../../language/".GetConfig('Language')."/front_language.ini";
			$this->ParseLangFile($frontLangFile);
		}

		public function ParseBackendLangFile()
		{
			$backLangFile = dirname(__FILE__) . "/../../language/".GetConfig('Language')."/admin/common.ini";
			$this->ParseLangFile($backLangFile);
		}

		public function ParseModuleLangFile()
		{
			$backLangFile = dirname(__FILE__) . "/../../language/".GetConfig('Language')."/module_language.ini";
			$this->ParseLangFile($backLangFile);
		}

		/**
		* sets the template base directory as well as some defaults
		*
		* @param string the base template directory
		*
		* @return void
		*/
		public function SetTemplateBase($dir)
		{
			$this->templateDirectories = array(
				'layouts' => array(),
				'panels' => array(),
				'snippets' => array()
			);

			if($this->frontEnd) {
				$this->RegisterTemplateDirectory(self::TYPE_LAYOUT, $dir.'/'.GetConfig('template'));
				$this->RegisterTemplateDirectory(self::TYPE_PANEL, $dir.'/'.GetConfig('template').'/Panels/');
				$this->RegisterTemplateDirectory(self::TYPE_SNIPPET, $dir.'/'.GetConfig('template').'/Snippets/');

				$this->RegisterTemplateDirectory(self::TYPE_LAYOUT, $dir.'/__master');
				$this->RegisterTemplateDirectory(self::TYPE_PANEL, $dir.'/__master/Panels');
				$this->RegisterTemplateDirectory(self::TYPE_SNIPPET, $dir.'/__master/Snippets');
			}
			else {
				$this->RegisterTemplateDirectory(self::TYPE_LAYOUT, $dir);
				$this->RegisterTemplateDirectory(self::TYPE_PANEL, $dir.'/Panels');
				$this->RegisterTemplateDirectory(self::TYPE_SNIPPET, $dir.'/Snippets');
			}
		}

		/**
		 * Register a directory to load a particular type of templates from.
		 *
		 * @param const A constant (TYPE_LAYOUT, TYPE_PANEL, TYPE_SNIPPET) specifying the type of template files in this folder.
		 * @param string The path to load the files from.
		 * @param int The priority to insert the loading at. Priorities with a lower number are searched first.
		 */
		public function RegisterTemplateDirectory($type, $directory, $priority=100)
		{
			if(!isset($this->templateDirectories[$type][$priority])) {
				$this->templateDirectories[$type][$priority] = array();
			}

			$this->templateDirectories[$type][$priority][] = $directory;
			ksort($this->templateDirectories[$type], SORT_NUMERIC);
		}

		/**
		 * Calculate the full path of the template file to load, given a certain type of template file and the name.
		 * Will loop through all allowable/registered directories until a file is found. The full path to that
		 * file will theb be returned.
		 */
		public function CalculateFilePath($type, $file)
		{
			foreach($this->templateDirectories[$type] as $priorities) {
				foreach($priorities as $folder) {
					if(file_exists($folder.'/'.$file)) {
						return $folder.'/'.$file;
					}
				}
			}

			return false;
		}

		/**
		* SetTemplate
		* Set the template to $TplName
		*
		* @param string $TplName the template name without the extension
		*
		* @return void
		*/
		public function SetTemplate($TplName)
		{
			$this->_tplName = $TplName;
		}

		/**
		* _GetTemplate
		* Returns the contents of a template if the template has been loaded
		*
		* @return string
		*/
		public function _GetTemplate()
		{
			return $this->_tplData;
		}

		/**
		* SetPageTitle
		* Set the title of the page
		*
		* @param string $title The title to set the page to
		*
		* @return void
		*/
		public function SetPageTitle($Title)
		{
			$this->_tplPageTitle = $Title;
		}

		public function SetMetaKeywords($Keywords)
		{
			$this->_tplMetaKeywords = $Keywords;
		}

		public function SetMetaDescription($Description)
		{
			$this->_tplMetaDescription = $Description;
		}

		/**
		* _GetPageTitle
		* Get the title of the page
		*
		* @return string The title of the page
		*/
		public function _GetPageTitle()
		{
			return $this->_tplPageTitle;
		}

		public function _GetMetaKeywords()
		{
			return $this->_tplMetaKeywords;
		}

		public function _GetMetaDescription()
		{
			return $this->_tplMetaDescription;
		}

		/**
		* ParseTemplate
		* Parse any special variables in the currently set template
		*
		* @param bool $return If true the template will be returned as a string
		* rather then echo'd
		* @param mixed $parsePage If set to false then load the template from the
		* disk otherwise $parsePage will be treated like the template contents
		*
		* @return mixed returns the parsed template if $return is true otherwise it
		* returns nothing
		*/
		public function ParseTemplate($return=false, $parsePage=false)
		{
			if (!$parsePage) {
				$this->_tplData = $this->_LoadTemplateFile();
			} else {
				$this->_tplData = $parsePage;
			}

			$this->_tplData = $this->_LangifyHTMLTag();
			$this->_tplData = $this->_ParsePanels();
			$this->_tplData = $this->_ParseIncludes();
			$this->_tplData = $this->_ParseBanners();

			$this->_tplData = $this->ParseSnippets($this->_tplData, $GLOBALS["SNIPPETS"]);
			$this->_tplData = $this->ParseGL($this->_tplData);
			$this->_tplData = $this->_ParseConstants();

			$template = $this->_GetTemplate();
			/*
			if($this->frontEnd) {
				$template .= '<script type="text/javascript">$("body").append("<div style=\'position: absolute; top: 0; background: green; padding: 4px 10px; color: white; font-size: 11px;\'>'.$this->_tplName.'.'.$this->templateExt.'</div>")</script>';
			} */
			if ($return) {
				return $template;
			} else {
				echo $template;
			}
		}

		/**
		* _LoadTemplateFile
		* Load the template from disk
		*
		* @return string The contents of the template file
		*/
		private function _LoadTemplateFile()
		{
			$tplData = "";

			if (!isset($this->_tplName)) {
				// No template name specified
				trigger_error(sprintf("%s", $GLOBALS[$this->langVar]['errNoTemplateNameSpecified']), E_USER_WARNING);
				return '';
			}

			$templatePath = $this->CalculateFilePath(self::TYPE_LAYOUT, $this->_tplName.'.'.$this->templateExt);
			if(!$templatePath) {
				trigger_error(sprintf(GetLang('errCouldntLoadTemplate'), $this->_tplName.'.'.$this->templateExt), E_USER_WARNING);
			}
			else {
				return file_get_contents($templatePath);
			}
		}

		/**
		* _ParseIncludes
		* Parse any includes in the template and insert the required data
		*
		* @return string The template with includes parsed in it
		*/
		private function _ParseIncludes()
		{
			// Parse out all of the panels in the template
			$tplData = $this->_GetTemplate();
			$matches = array();

			if (!isset($this->_tplName)) {
				// No template name specified
				trigger_error(sprintf("%s", $GLOBALS[$this->langVar]["errNoTemplateNameSpecified"]), E_USER_WARNING);
				return $tplData;
			}

			// Parse out the panel tokens in the template file

			preg_match_all("`(?siU)(%%Include.(.*)%%)`", $tplData, $matches);
			$includeRoot = ISC_BASE_PATH . '/templates/__includes/';
			foreach ($matches[0] as $key => $k) {
				$pattern1 = $k;
				$pattern2 = str_replace("%", "", $pattern1);
				$pattern2 = str_replace("Include.", "", $pattern2);
				$localPath = realpath($includeRoot . $pattern2);
				if (strpos($pattern2, "http://") === 0) {
					// Trick the site into thinking it a regular user as some sites stop
					// other servers from taking files
					ini_set('user_agent', $this->userAgent);
					$includeData = PostToRemoteFileAndGetResponse($pattern2);
				}
				else if($localPath && file_exists($localPath) && strpos($localPath, $includeRoot) !== false) {
					// Must be in the root folder
					$includeData = file_get_contents(ISC_BASE_PATH . '/' . $pattern2);
				}
				else {
					continue;
				}
				$tplData = str_replace($pattern1, $includeData, $tplData);
			}
			return $tplData;
		}

		/**
		* _ParseConstants
		* Parse any constants in the template, replacing them with their values
		*
		* @return string the template with it's constants parsed in it
		*/
		private function _ParseConstants()
		{
			$tplData = $this->_GetTemplate();

			if (!isset($this->_tplName)) {
				// No template name specified
				trigger_error(sprintf("%s", $GLOBALS[$this->langVar]['errNoTemplateNameSpecified']), E_USER_WARNING);
			}
			$title = $this->_GetPageTitle();
			$title = str_replace(array("<", ">"), array("&lt;", "&gt;"), $title);
			$tplData = str_replace("%%Page.WindowTitle%%", $title, $tplData);
			$tplData = str_replace("%%Page.MetaKeywords%%", isc_html_escape($this->_GetMetaKeywords()), $tplData);
			$tplData = str_replace("%%Page.MetaDescription%%", isc_html_escape($this->_GetMetaDescription()), $tplData);

			return $tplData;
		}

		/**
		* _ParsePanels
		* Parse any panels in the template, inserting the panel if required
		*
		* @param mixed $input if input is false load the template from disk otherwise
		* $input is treated like the contents of the template
		*
		* @return string The template with panels parsed in it
		*/
		private function _ParsePanels($input=false)
		{
			$matches = Array();

			// Parse out all of the panels in the template
			if (!$input) {
				$tplData = $this->_GetTemplate();
			} else {
				$tplData = $input;
			}

			if (!isset($this->_tplName)) {
				// No template name specified
				trigger_error(sprintf("%s", $GLOBALS[$this->langVar]["errNoTemplateNameSpecified"]), E_USER_WARNING);
			} else {


				$tplData = $this->Parse('Panel.', $tplData, 'GetPanelContent');
			}

			return $tplData;
		}

		/**
		* GetPanelContent
		* Get the contents for a given panel
		*
		* @param string $PanelId the name of the panel without the file extension
		*
		* @return string the html to put into the template to replace the keyword
		*/
		public function GetPanelContent($PanelId)
		{
			// Parse the PHP panel and return its content
			$panelData = "";
			$panelLogic = $this->panelPHPDir.$PanelId.'.php';

			// If the panel can be shown, show it
			if(!isset($GLOBALS["HidePanels"])) {
				$GLOBALS["HidePanels"] = array();
			}

			if(in_array($PanelId, $GLOBALS["HidePanels"])) {
				return '';
			}

			$panelTemplate = $this->CalculateFilePath(self::TYPE_PANEL, $PanelId.'.html');
			if(!$panelTemplate) {
				return "<div>[Panel not found: '" . $PanelId . "']</div>";
			}

			// Each panel has a generic panel parsing class. We will include
			// that file and parse the template
			$panelName = str_replace('Panel', '_Panel', $PanelId);

			if($this->frontEnd) {
				$panelClass = strtoupper(PRODUCT_ID.'_'.$panelName.'_PANEL');
			}
			else {
				$panelClass = $panelName;
			}

			if(file_exists($panelLogic)) {
				// Parse the PHP panel if it exists
				include_once($panelLogic);
				$objPanel = new $panelClass();
				$objPanel->SetHTMLFile($panelTemplate);

				// Otherwise we have to parse the actual panel
				$panelData = $objPanel->ParsePanel(sprintf("%s.%s", $this->_tplName, $this->templateExt));
			}
			else {
				$panelData = file_get_contents($panelTemplate);
			}

			$panelData = $this->ParseGL($panelData);
			$panelData = $this->ParseSnippets($panelData, $GLOBALS['SNIPPETS']);

			if($panelData) {
				$panelData = $this->_ParsePanels($panelData);
			}

			return $panelData;
		}

		/**
		* GetSnippet
		* Load a snippet from disk
		*
		* @param string $PanelId The name of the snippet without the file extension
		*
		* @return string The snippet with global and language strings parsed from it
		*/
		public function GetSnippet($PanelId)
		{
			$snippetFile = $this->CalculateFilePath(self::TYPE_SNIPPET, $PanelId.'.html');
			if(!$snippetFile) {
				return "<div>[Snippet not found: '" . $PanelId . "']</div>";
			}

			$snippetData = file_get_contents($snippetFile);
			return $this->ParseGL($snippetData);
		}

		/**
		* ParseSnippets
		* Parse the snippets from a template
		*
		* @param string $string the string to parse for snippets
		* @param mixed $snippets an array of snippets to parse
		*/
		public function ParseSnippets($string,$snippets)
		{

			$string = $this->Parse('SNIPPET_', $string, $snippets);

			// Make sure that if the replacement has a snippet in it that we replace
			// that but limit it to 3 replacement times in case there is a loop
			$limit = 3;
			while (preg_match('#%%SNIPPET_[a-zA-Z0-9_\.]+%%#i', $string) && $limit > 0) {
				$string = $this->ParseSnippets($string, $snippets);
				$limit--;
			}

			return $string;
		}

		/**
		* ParseGL
		* Parse global and language vars from a template/panel/snippet
		*
		* @param string $TemplateData The string to parse for vars
		*
		* @return string The string with the vars replaced
		*/
		public function ParseGL($TemplateData)
		{
			// Parse out global and language variables from template data and
			// return it. This is used from the generic panel class for each panel
			$tplData = $TemplateData;

			// Design mode is only supported on the front end
			if(!$this->frontEnd) {
				$dmLangEditing = false;
			}
			// No design mode session was found
			else if(empty($_COOKIE['designModeToken']) && empty($_POST['designModeToken'])) {
				$dmLangEditing = false;
			}
			// Found and working with a valid design mode session
			else if(GetClass('ISC_ADMIN_AUTH')->isDesignModeAuthenticated()) {
				$dmLangEditing = true;
			}
			else {
				$dmLangEditing = false;
			}

			// If design mode is on, we need to do a lot of cool string replacement stuff
			if($dmLangEditing) {
				$badMatches = array();
				$scriptStart = 0;
				do {
					$scriptStart = stripos($tplData, "<script", $scriptStart);
					if($scriptStart === false) {
						break;
					}
					$scriptEnd = stripos($tplData, "</script>", $scriptStart);
					if($scriptEnd === false) {
						break;
					}
					$badMatches[] = substr($tplData, $scriptStart, $scriptEnd-$scriptStart);
					$tplData = substr_replace($tplData, "%%DM_LANG_EDIT%%", $scriptStart, $scriptEnd-$scriptStart);
				}
				while($scriptStart !== false);
				$valueStart = 0;
				$badMatches2 = array();
				do {
					$valueStart = stripos($tplData, "value=\"", $valueStart);
					if($valueStart === false) {
						break;
					}
					$valueEnd = stripos($tplData, "\"", $valueStart+8);
					if($valueEnd === false) {
						break;
					}
					$badMatches2[] = substr($tplData, $valueStart, $valueEnd-$valueStart+1);
					$tplData = substr_replace($tplData, "%%DM2_LANG_EDIT%%", $valueStart, $valueEnd-$valueStart+1);
				}
				while($valueStart !== false);
			}

			// Parse out the javascript language pack variables in the template file
			preg_match_all("/(?siU)(%%LNG_JS_[a-zA-Z0-9]{1,}%%)/", $tplData, $matches);
			foreach ($matches[0] as $key => $k) {
				$pattern1 = $k;
				$pattern2 = str_replace("%", "", $pattern1);
				$pattern2 = str_replace("LNG_JS_", "", $pattern2);

				if($dmLangEditing == true) {
					if (isset($GLOBALS['ISC_LANG'][$pattern2])) {
						$lang_data = "<span id='lang_".$pattern2."' class='LNGString'>";
						$lang_data .= GetLang($pattern2)."</span>";
						$tplData = str_replace($pattern1,  addcslashes($lang_data, "'"), $tplData);
					}
				}
				else {
					if (isset($GLOBALS['ISC_LANG'][$pattern2])) {
						$tplData = str_replace($pattern1,  addcslashes(GetLang($pattern2), "'"), $tplData);
					}
				}
			}

			// Parse out the language pack variables in the template file
			preg_match_all("/(?siU)(%%LNG_[a-zA-Z0-9_]{1,}%%)/", $tplData, $matches);
			foreach ($matches[0] as $key => $k) {
				$pattern1 = $k;
				$pattern2 = str_replace("%", "", $pattern1);
				$pattern2 = str_replace("LNG_", "", $pattern2);

				if($dmLangEditing == true) {
					if (isset($GLOBALS['ISC_LANG'][$pattern2])) {
						$lang_data = "<span id='lang_".$pattern2."' class='LNGString'>";
						$lang_data .= GetLang($pattern2)."</span>";
						$tplData = str_replace($pattern1, $lang_data, $tplData);
					}
				}
				else {
					if (isset($GLOBALS['ISC_LANG'][$pattern2])) {
						$tplData = str_replace($pattern1, GetLang($pattern2), $tplData);
					}
				}
			}

			if($dmLangEditing) {
				if(count($badMatches) > 0) {
					foreach($badMatches as $match) {
						preg_match_all("/(?siU)(%%LNG_[a-zA-Z0-9]{1,}%%)/", $match, $matches);
						foreach ($matches[0] as $key => $k) {
							$pattern1 = $k;
							$pattern2 = str_replace("%", "", $pattern1);
							$pattern2 = str_replace("LNG_", "", $pattern2);
							if (isset($GLOBALS['ISC_LANG'][$pattern2])) {
								$match = str_replace($pattern1, GetLang($pattern2), $match);
							}
						}
						$startPos = strpos($tplData, "%%DM_LANG_EDIT%%");
						$length = strlen("%%DM_LANG_EDIT%%");
						$tplData = substr_replace($tplData, $match, $startPos, $length);
					}
				}
				if(count($badMatches2) > 0) {
					foreach($badMatches2 as $match) {
						preg_match_all("/(?siU)(%%LNG_[a-zA-Z0-9]{1,}%%)/", $match, $matches);
						foreach ($matches[0] as $key => $k) {
							$pattern1 = $k;
							$pattern2 = str_replace("%", "", $pattern1);
							$pattern2 = str_replace("LNG_", "", $pattern2);
							if (isset($GLOBALS['ISC_LANG'][$pattern2])) {
								$match = str_replace($pattern1, GetLang($pattern2), $match);
							}
						}
						$startPos = strpos($tplData, "%%DM2_LANG_EDIT%%");
						$length = strlen("%%DM2_LANG_EDIT%%");
						$tplData = substr_replace($tplData, $match, $startPos, $length);
					}
				}

			}

			$tplData = $this->Parse("GLOBAL_", $tplData, $GLOBALS);

			return $tplData;
		}

		/**
		* Parse
		* Generic parsing function
		*
		* @param the prefix to search for
		* @param the text to parse
		* @param the associative array or function with the replacement
		* values in/returned by it
		*
		* @return string the parsed text
		*/
		public function Parse($prefix, $text, $replace)
		{
			$matches = array();
			$output = $text;

			// Parse out the language pack variables in the template file
			preg_match_all('/(?siU)(%%'.preg_quote($prefix).'[a-zA-Z0-9_\.]+%%)/', $output, $matches);

			foreach ($matches[0] as $key=>$k) {
				$pattern1 = $k;
				$pattern2 = str_replace('%', '', $pattern1);
				$pattern2 = str_replace($prefix.'', '', $pattern2);

				if (is_array($replace) && isset($replace[$pattern2])) {
					$output = str_replace($pattern1, $replace[$pattern2], $output);
				} elseif (is_string($replace) && method_exists($this, $replace)) {
					$result = $this->$replace($pattern2);
					$output = str_replace($pattern1, $result, $output);
				} else {
					$output = str_replace($pattern1, '', $output);
				}
			}
			return $output;
		}

		/**
		* GetAndParseFile
		* Load a file from the network and parse it for global and lang strings
		*
		* @param string $File The file on the server to parse
		*
		* @return string the data that has been loaded and parsed
		*/
		public function GetAndParseFile($File)
		{
			// need to check to make sure we aren't including the file twice
			if (!isset($GLOBAL['IncludedFiles'])) {
				$GLOBAL['IncludedFiles'] = array();
			}

			if (in_array($File,$GLOBAL['IncludedFiles'])) {
				return '';
			} else {
				$GLOBAL['IncludedFiles'][] = $File;
			}
			// Open a file, parse out tokens and return it
			$dir = dirname(__FILE__)."/../../";
			$file = realpath($dir.$File);

			$fdata = file_get_contents($file);

			$fdata = $this->ParseGL($fdata);

			return $fdata;
		}

		/**
		* Parse a lang file and store it's values in the $GLOBALS[$this->langVar]
		* array
		* @return void;
		*/
		public function ParseLangFile($file)
		{
			if (!file_exists($file)) {
				// Trigger an error -- has to be in English though
				// because we can't load the language file
				trigger_error(sprintf("The language file %s couldn't be opened.", $file), E_USER_WARNING);
			} else {
				// Parse the ArticleLive language file
				$vars = parse_ini_file($file);
				if (isset($GLOBALS[$this->langVar])) {
					$GLOBALS[$this->langVar] = array_merge($GLOBALS[$this->langVar], $vars);
				} else {
					$GLOBALS[$this->langVar] = $vars;
				}

				if (!is_array($GLOBALS[$this->langVar])) {
					// Couldn't load the language file
					trigger_error(sprintf("The language file %s couldn't be loaded.", $file), E_USER_WARNING);
				}
			}
		}

		/**
		* ParseBanners
		* Parse banners placeholders from a template file using the $GLOBALS["Banners"] array
		*
		* @param string $TemplateData The string to parse for vars
		*
		* @return string The string with the vars replaced
		*/
				private function _ParseBanners()
		{
			if(!isset($GLOBALS['ISC_CLASS_BANNER'])) {
				$GLOBALS['ISC_CLASS_BANNER'] = GetClass('ISC_BANNER');
			}

			// Parse out banner variables from template data and
			// return it. This is used specifically for  Shopping Cart only
			$tplData = $this->_GetTemplate();

			// Are there any banners to include?
			if(isset($GLOBALS["PageType"]) && isset($GLOBALS["Banners"]) && is_array($GLOBALS["Banners"]) && count($GLOBALS["Banners"]) > 0) {
				switch($GLOBALS["PageType"]) {
					case "home_page":
					case "search_page": {
						// Is there a top template?
						if(isset($GLOBALS["Banners"]["top"])) {
							// Replace it out
							$tplData = str_replace("%%Banner.TopBanner%%", $GLOBALS["Banners"]["top"]["content"], $tplData);
						}
						else {
							// Replace it with nothing
							$tplData = str_replace("%%Banner.TopBanner%%", "", $tplData);
						}

						// Is there a bottom template?
						if(isset($GLOBALS["Banners"]["bottom"])) {
							// Replace it out
							$tplData = str_replace("%%Banner.BottomBanner%%", $GLOBALS["Banners"]["bottom"]["content"], $tplData);
						}
						else {
							$tplData = str_replace("%%Banner.BottomBanner%%", "", $tplData);
						}
						
						// modificacao /////////////////////////////////////////
						if(isset($GLOBALS["Banners"]["direito"])) {
							// Replace it out
							$tplData = str_replace("%%Banner.Direito%%", $GLOBALS["Banners"]["direito"]["content"], $tplData);
						}
						else {
							$tplData = str_replace("%%Banner.Direito%%", "", $tplData);
						}
						

						if(isset($GLOBALS["Banners"]["esquerdo"])) {
							// Replace it out
							$tplData = str_replace("%%Banner.Esquerdo%%", $GLOBALS["Banners"]["esquerdo"]["content"], $tplData);
						}
						else {
							$tplData = str_replace("%%Banner.Esquerdo%%", "", $tplData);
						}
						//////////////////////////////////////////////////////////////////

						break;
					}
					case "category_page":
					case "brand_page": {

						// Are we on a category page or brand page?
						if($GLOBALS["PageType"] == "category_page") {
							$id = $GLOBALS["CatId"];
						}
						else {
							$id = $GLOBALS["BrandId"];
						}

						if(isset($GLOBALS["Banners"][$id])) {
							// Is there a top template?
							if(isset($GLOBALS["Banners"][$id]["top"])) {
								// Replace it out
								$tplData = str_replace("%%Banner.TopBanner%%", $GLOBALS["Banners"][$id]["top"]["content"], $tplData);
							}
							else {
								// Replace it with nothing
								$tplData = str_replace("%%Banner.TopBanner%%", "", $tplData);
							}

							if(isset($GLOBALS["Banners"][$id]["bottom"])) {
								// Replace it out
								$tplData = str_replace("%%Banner.BottomBanner%%", $GLOBALS["Banners"][$id]["bottom"]["content"], $tplData);
							}
							else {
								// Replace it with nothing
								$tplData = str_replace("%%Banner.BottomBanner%%", "", $tplData);
							}
							
						// modificacao ////////////////////////////////////////////////////////////
						if(isset($GLOBALS["Banners"][$id]["direito"])) {

							$tplData = str_replace("%%Banner.Direito%%", $GLOBALS["Banners"][$id]["direito"]["content"], $tplData);
						}
						else {
							$tplData = str_replace("%%Banner.Direito%%", "", $tplData);
						}
						

						if(isset($GLOBALS["Banners"][$id]["esquerdo"])) {
							// Replace it out
							$tplData = str_replace("%%Banner.Esquerdo%%", $GLOBALS["Banners"][$id]["esquerdo"]["content"], $tplData);
						}
						else {
							$tplData = str_replace("%%Banner.Esquerdo%%", "", $tplData);
						}
						/////////////////////////////////////////////////////////////
						
						}
						else {
							// Replace the banners with nothing
							$tplData = str_replace("%%Banner.TopBanner%%", "", $tplData);
							$tplData = str_replace("%%Banner.BottomBanner%%", "", $tplData);
							$tplData = str_replace("%%Banner.Direito%%", "", $tplData);
							$tplData = str_replace("%%Banner.Esquerdo%%", "", $tplData);
						}

						break;
					}
				}
				
			}
			else {
				// Replace the banners with nothing
				$tplData = str_replace("%%Banner.TopBanner%%", "", $tplData);
				$tplData = str_replace("%%Banner.BottomBanner%%", "", $tplData);
				$tplData = str_replace("%%Banner.Direito%%", "", $tplData);
				$tplData = str_replace("%%Banner.Esquerdo%%", "", $tplData);
			}

			return $tplData;
		}

		public function Assign($k, $v)
		{
			$GLOBALS[$k] = $v;
		}

		/**
		 * _LangifyHTMLTag
		 * Convert the <html> tag in to it's equivilent for the language in use.
		 * Will switch text direction if necessary and add lang attributes to the head tag.
		 * Pass in the template to be converted.
		 *
		 * @param string The template contents.
		 */
		private function _LangifyHTMLTag()
		{
			$tplData = $this->_GetTemplate();

			if(isset($this->_DoneHead)) {
				return $tplData;
			}

			if(isc_strpos($tplData, "<html") !== false) {
				$this->_DoneHead = true;
			}
			else {
				return $tplData;
			}

			if(GetConfig('Language')) {
				if(function_exists('str_ireplace')) {
					$tplData = str_ireplace("<html", sprintf("<html xml:lang=\"%s\" lang=\"%s\"", GetConfig('Language'), GetConfig('Language')), $tplData);
				}
				else {
					$tplData = str_replace("<html", sprintf("<html xml:lang=\"%s\" lang=\"%s\"", GetConfig('Language'), GetConfig('Language')), $tplData);
				}
			}

			if(GetLang('RTL') == 1) {
				$tplData = str_ireplace("<html", "<html dir=\"rtl\"", $tplData);
				if($this->frontEnd) {
					$rtlCSSPath = ISC_BASE_PATH . "/templates/" . GetConfig('template') . "/Styles/rtl.css";
					$rtlCSS = $GLOBALS['TPL_PATH'] . "/Styles/rtl.css";
				}
				else {
					$rtlCSSPath = ISC_BASE_PATH.'/admin/Styles/rtl.css';
					$rtlCSS = "Styles/rtl.css";
				}
				if(file_exists($rtlCSSPath)) {
					$GLOBALS['RTLStyles'] = sprintf('<link rel="stylesheet" type="text/css" href="%s" />', $rtlCSS);
				}
			}

			return $tplData;
		}
	}