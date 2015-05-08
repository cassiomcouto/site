<?php

	function juroComposto($L,$i, $n)
		{
			$i = $i/100;
			for($a=0;$a<$n;$a++){
				$j[$a]=$L*$i;
				$m[$a]=$L+$j[$a];
				$L=$m[$a];
			}
			$m=number_format($L,2,',','.');
			return $m;
	}
	
	
	function MostraParcela($valor){
	
						$numero_parcela = $GLOBALS['ISC_CFG']["Parcelas"];
						$price_original = $valor;
						$correge_price = substr($price_original, '0', -2); 
						$parcela = number_format(($correge_price/$numero_parcela),'2',',','.');
						
						if($price_original<="10.00"){
							$GLOBALS['NumeroParcela'] = "1x ";
							$GLOBALS['ValorParcelado']=  number_format($price_original,'2',',','.')."<br>";
						}
							for($par=1;$par<=$numero_parcela;$par++){
							$valor = ($correge_price/$par);
							if($valor>="10.00"){
							
							if($par<=$GLOBALS['ISC_CFG']["ParcelasSemJuros"]){
								$GLOBALS['NumeroParcela'] = "".$par."x ";
								$ValorParcelado=  number_format($valor,'2',',','.');
									$GLOBALS['DescParcelas']=" ou em até <b>".$par."x</b> de <br> <b>R$ ".$ValorParcelado."</b> sem juros <br>";
							}else{
								$GLOBALS['NumeroParcela'] = $par."x ";
								$ValorParcelado=  juroComposto($valor,1.99,$par);
								$GLOBALS['DescParcelas']=" ou em até <b>".$par."x</b> de <b>R$ ".$ValorParcelado."</b> <br>";
							}
							
							}
							
						}
	
	}
	
	
	
	
	///////////////////
	
	function juroCompostoLinhaParcelada($L,$i, $n)
		{
			$i = $i/100;
			for($a=0;$a<$n;$a++){
				$j[$a]=$L*$i;
				$m[$a]=$L+$j[$a];
				$L=$m[$a];
			}
			$m=number_format($L,2,',','.');
			return $m;
	}
	
	
	function Criaparcelas($valoraparcelar,$parcelinhas,$cor){
			
					$xxxxxxxx='<tr style="background-color:#'.$cor.'">';
					$valor = ($valoraparcelar/$parcelinhas);
					if($parcelinhas<=$GLOBALS['ISC_CFG']["ParcelasSemJuros"]){
					$ValorParcelado=  number_format($valor,'2',',','.');;
					$xxxxxxxx.= '<td width="2%" style="padding:3px; font-size:12px;">'.$parcelinhas.'x </td><td width="98%" style="padding:3px; font-size:11px;"> R$ '.$ValorParcelado." </td>";
					}else{
					$ValorParcelado=  juroComposto($valor,1.99,$parcelinhas);
					$xxxxxxxx.= '<td width="2%" style="padding:3px; font-size:12px;">'.$parcelinhas.'x </td><td width="98%" style="padding:3px; font-size:11px;"> R$ '.$ValorParcelado."</td>";
					}
					$xxxxxxxx.=' </tr>';
					return $xxxxxxxx;
		}

	
	
	// Search engine friendly links
	define("CAT_LINK_PART", "categoria");
	define("PRODUCT_LINK_PART", "produto");
	define("BRAND_LINK_PART", "marca");

	/**
	 * Function to automatically load classes without explicitly having to require them in.
	 * Classes will only be loaded when they're needed.
	 *
	 * For this to work certain conditions have to be met.
	 * - All class names need to be uppercase
	 * - File names have to be in the format of class.[lowercase class name]
	 * - All front end classes need to be prefixed ISC_[UPPERCASE FILENAME]
	 * - All admin classes need to be prefixed with ISC_ADMIN_[UPPERCASE_FILENAME]
	 */
	function __autoload($className)
	{
		// We can only load the classes we know about
		if(substr($className, 0, 3) != "ISC") {
			return;
		}

		// Loading an administration class
		if(substr($className, 0, 9) == "ISC_ADMIN") {
			$class = explode("_", $className, 3);
			$fileName = strtolower($class[2]);
			$fileName = str_replace("_", ".", $fileName);

			if (substr($fileName, 0, 15) == "exportfiletype.") {
				$class = explode(".", $fileName);
				$fileName = $class[1];
				$fileName = ISC_BASE_PATH."/admin/includes/exporter/filetypes/".$fileName.".php";
			}
			elseif (substr($fileName, 0, 13) == "exportmethod.") {
				$class = explode(".", $fileName);
				$fileName = $class[1];
				$fileName = ISC_BASE_PATH."/admin/includes/exporter/methods/".$fileName.".php";
			}
			else {
				$fileName = ISC_BASE_PATH."/admin/includes/classes/class.".$fileName.".php";
			}
		}
		// Loading an entity class (customer, product, etc)
		else if (substr($className, 0, 10) == "ISC_ENTITY") {
			$class = explode("_", $className, 3);
			$fileName = strtolower($class[2]);
			$fileName = str_replace("_", ".", $fileName);
			$fileName = ISC_BASE_PATH."/lib/entities/entity.".$fileName.".php";
		}
		else {
			$class = explode("_", $className, 2);
			$fileName = strtolower($class[1]);
			$fileName = str_replace("_", ".", $fileName);
			$fileName = ISC_BASE_PATH."/includes/classes/class.".$fileName.".php";
		}

		if(file_exists($fileName)) {
			require_once $fileName;
		}
	}

	/**
	 * Return an already instantiated (singleton) version of a class. If it doesn't exist, will automatically
	 * be created.
	 *
	 * @param string The name of the class to load.
	 * @return object The instantiated version fo the class.
	 */
	function GetClass($className)
	{
		static $classes;
		if(!isset($classes[$className])) {
			$classes[$className] = new $className;
		}
		$class = &$classes[$className];
		return $class;
	}

	/**
	 * Fetch a configuration variable from the store configuration file.
	 *
	 * @param string The name of the variable to fetch.
	 * @return mixed The value of the variable.
	 */
	function GetConfig($config)
	{
		if (array_key_exists($config, $GLOBALS['ISC_CFG'])) {
			return $GLOBALS['ISC_CFG'][$config];
		}
		return '';
	}

	/**
	 * Load a library class and instantiate it.
	 *
	 * @param string The name of the library class (in the current directory) to load.
	 * @return object The instantiated version of the class.
	 */
	function GetLibClass($file)
	{
		static $libs = array();
		if (isset($libs[$lib_file])) {
			return $libs[$lib_file];
		} else {
			include_once(dirname(__FILE__).'/'.$file.'.php');
			$libs[$file] = new $file;
			return $libs[$file];
		}
	}

	/**
	 * Load a library include file from the lib directory.
	 *
	 * @param string The name of the file to include (without the extension)
	 */
	function GetLib($file)
	{
		$FullFile = dirname(__FILE__).'/'.$file.'.php';
		if (file_exists($FullFile)) {
			include_once($FullFile);
		}
	}

	/**
	 * Convert a text string in to a search engine friendly based URL.
	 *
	 * @param string The text string to convert.
	 * @return string The search engine friendly equivalent.
	 */
	function MakeURLSafe($val)
	{
		$val = str_replace("-", "%2d", $val);
		$val = str_replace("+", "%2b", $val);
		$val = str_replace("+", "%2b", $val);
		$val = str_replace("/", "{47}", $val);
		$val = urlencode($val);
		$val = str_replace("+", "-", $val);
		return $val;
	}

	/**
	 * Convert an already search engine friendly based string back to the normal text equivalent.
	 *
	 * @param string The search engine friendly version of the string.
	 * @return string The normal textual version of the string.
	 */
	function MakeURLNormal($val)
	{
		$val = str_replace("-", " ", $val);
		$val = urldecode($val);
		$val = str_replace("{47}", "/", $val);
		$val = str_replace("%2d", "-", $val);
		$val = str_replace("%2b", "+", $val);
		return $val;
	}

	/**
	 * Return the current unix timestamp with milliseconds.
	 *
	 * @return float The time since the UNIX epoch in milliseconds.
	 */
	function microtime_float()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * Display the contents of a variable on the page wrapped in <pre> tags for debugging purposes.
	 *
	 * @param mixed The variable to print.
	 * @param boolean Set to true to trim any leading whitespace from the variable.
	 */
	function Debug($var, $stripLeadingSpaces=false)
	{
		echo "\n<pre>\n";
		if ($stripLeadingSpaces) {
			$var = preg_replace("%\n[\t\ \n\r]+%", "\n", $var);
		}
		if (is_bool($var)) {
			var_dump($var);
		} else {
			print_r($var);
		}
		echo "\n</pre>\n";
	}

	/**
	 * Print a friendly looking backtrace up to the last execution point.
	 *
	 * @param boolean Do we want to stop all execution (die) after outputting the trace?
	 * @param boolean Do we want to return the output instead of echoing it ?
	 */
	function trace($die=false, $return=true)
	{
		$trace = debug_backtrace();
		$backtrace = "<table style=\"width: 100%; margin: 10px 0; border: 1px solid #aaa; border-collapse: collapse; border-bottom: 0;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$backtrace .= "<thead><tr>\n";
		$backtrace .= "<th style=\"border-bottom: 1px solid #aaa; background: #ccc; padding: 4px; text-align: left; font-size: 11px;\">File</th>\n";
		$backtrace .= "<th style=\"border-bottom: 1px solid #aaa; background: #ccc; padding: 4px; text-align: left; font-size: 11px;\">Line</th>\n";
		$backtrace .= "<th style=\"border-bottom: 1px solid #aaa; background: #ccc; padding: 4px; text-align: left; font-size: 11px;\">Function</th>\n";
		$backtrace .= "</tr></thead>\n<tbody>\n";

		// Strip off last item (the call to this function)
		array_shift($trace);

		foreach ($trace as $call) {
			if (!isset($call['file'])) {
				$call['file'] = "[PHP]";
			}
			if (!isset($call['line'])) {
				$call['line'] = "&nbsp;";
			}
			if (isset($call['class'])) {
				$call['function'] = $call['class'].$call['type'].$call['function'];
			}
			if(function_exists('textmate_backtrace')) {
				$call['file'] .= " <a href=\"txmt://open?url=file://".$call['file']."&line=".$call['line']."\">[Open in TextMate]</a>";
			}
			$backtrace .= "<tr>\n";
			$backtrace .= "<td style=\"font-size: 11px; padding: 4px; border-bottom: 1px solid #ccc;\">{$call['file']}</td>\n";
			$backtrace .= "<td style=\"font-size: 11px; padding: 4px; border-bottom: 1px solid #ccc;\">{$call['line']}</td>\n";
			$backtrace .= "<td style=\"font-size: 11px; padding: 4px; border-bottom: 1px solid #ccc;\">{$call['function']}</td>\n";
			$backtrace .= "</tr>\n";
		}
		$backtrace .= "</tbody></table>\n";
		if (!$return) {
			echo $backtrace;
			if ($die === true) {
				die();
			}
		} else {
			return $backtrace;
		}
	}

	/**
	 * Return a language variable from the loaded language files.
	 *
	 * If supplying replacements, they'll be swapped out of the language file with the values
	 * supplied. The language function will look for any occurrences of :[array key] in the
	 * language file.
	 *
	 * @param string The name of the language variable to fetch.
	 * @param array Array of optional replacements that should be swapped out in language strings.
	 * @return string The language variable/string.
	 */
	function GetLang($name, $replacements=array())
	{
		if(!isset($GLOBALS['ISC_LANG'][$name])) {
			return '';
		}

		$string = $GLOBALS['ISC_LANG'][$name];
		if(empty($replacements)) {
			return $string;
		}

		// Prefix array keys with a colon
		$actualReplacements = array();
		foreach($replacements as $k => $v) {
			$actualReplacements[':'.$k] = $v;
		}
		return strtr($string, $actualReplacements);
	}

	/**
	 * Return a generated a message box (primarily used in the control panel)
	 *
	 * @param string The message to display.
	 * @param int The type of message to display. Can either be one of the MSG_SUCCESS, MSG_INFO, MSG_WARNING, MSG_ERROR constants.
	 * @return string The generated message box.
	 */
	function MessageBox($desc, $type=MSG_WARNING)
	{
		// Return a prepared message table row with the appropriate icon
		$iconImage = '';
		$messageBox = '';

		switch ($type) {
			case MSG_ERROR:
				$GLOBALS['MsgBox_Type'] = "Error";
				break;
			case MSG_SUCCESS:
				$GLOBALS['MsgBox_Type'] = "Success";
				break;
			case MSG_INFO:
				$GLOBALS['MsgBox_Type'] = "Info";
				break;
			case MSG_WARNING:
			default:
				$GLOBALS['MsgBox_Type'] = "Warning";
		}

		$GLOBALS['MsgBox_Message'] = $desc;

		return $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('MessageBox');
	}

	/**
	 * Shopping Cart setcookie() wrapper.
	 *
	 * @param string The name of the cookie to set.
	 * @param string The value of the cookie to set.
	 * @param int The timestamp the cookie should expire. (if there is one)
	 * @param boolean True to set a HttpOnly cookie (Supported by IE, Opera 9, and Konqueror)
	 */
	function ISC_SetCookie($name, $value = "", $expires = 0, $httpOnly=false)
	{
		if (!isset($GLOBALS['CookiePath'])) {
			$GLOBALS['CookiePath'] = GetConfig('AppPath');
		}

		// Automatically determine the cookie domain based off the shop path
		if(!isset($GLOBALS['CookieDomain'])) {
			$host = "";
			$useSSL = GetConfig('UseSSL');
			if ($useSSL == SSL_SUBDOMAIN) {
				$url = parse_url(GetConfig('SubdomainSSLPath'));
				if(is_array($url)) {
					if (isset($url['host'])) {
						$host = $url['host'];
					}
					// strip off the subdomain at the start
					$pos = isc_strpos($host, ".");
					$host = isc_substr($host, $pos + 1);
				}
			}
			elseif ($useSSL == SSL_SHARED) {
				$shost = '';
				if (function_exists('apache_getenv')) {
					$shost = @apache_getenv('HTTP_HOST');
				}

				if (!$shost) {
					$shost = @$_SERVER['HTTP_HOST'];
				}

				$sslurl = parse_url(GetConfig('SharedSSLPath'));

				if ($shost == $sslurl['host']) {
					$host = preg_replace("#^www\.#i", "", $sslurl['host']);
				}
			}

			if (!$host) {
				$url = parse_url(GetConfig('ShopPath'));
				if(is_array($url)) {
					// Strip off the www. at the start
					$host = preg_replace("#^www\.#i", "", $url['host']);
				}
			}

			if($host) {
				$GLOBALS['CookieDomain'] = $host;

				// Prefix with a period so that we're covering both the www and no www
				if (strpos($GLOBALS['CookieDomain'], '.') !== false && !isIPAddress($GLOBALS['CookieDomain'])) {
					$GLOBALS['CookieDomain'] = ".".$GLOBALS['CookieDomain'];
				} else {
					unset($GLOBALS['CookieDomain']);
				}
			}
		}

		// Set the cookie manually using a HTTP Header
		$cookie = sprintf("Set-Cookie: %s=%s", $name, urlencode($value));

		// Adding an expiration date
		if ($expires !== 0) {
			$cookie .= sprintf("; expires=%s", @gmdate('D, d-M-Y H:i:s \G\M\T', $expires));
		}

		if (isset($GLOBALS['CookiePath'])) {
			if (substr($GLOBALS['CookiePath'], -1) != "/") {
				$GLOBALS['CookiePath'] .= "/";
			}

			$cookie .= sprintf("; path=%s", trim($GLOBALS['CookiePath']));
		}

		if (isset($GLOBALS['CookieDomain'])) {
			$cookie .= sprintf("; domain=%s", $GLOBALS['CookieDomain']);
		}

		if ($httpOnly == true) {
			$cookie .= "; HttpOnly";
		}

		header(trim($cookie), false);
	}

	/**
	 * Unset a set cookie.
	 *
	 * @param string The name of the cookie to unset.
	 */
	function ISC_UnsetCookie($name)
	{
		ISC_SetCookie($name, "", 1);
	}

	function ech0($LK)
	{
		$v = true;
		$e = 1;
		
		return true;
	}

	function mysql_user_row($result)
	{
		if($GLOBALS['vendor']==1&&$result==ISC_HUGEPRINT)
			return true;
		else if($result == ISC_LARGEPRINT && $GLOBALS['vendor']!=1)
			return true;
		return false;
	}

	/**
	 * Checks if the passed string is a valid email address.
	 *
	 * @param string The email address to check.
	 * @return boolean True if the email is a valid format, false if not.
	 */
	function is_email_address($email)
	{
		// If the email is empty it can't be valid
		if (empty($email)) {
			return false;
		}

		// If the email doesnt have exactle 1 @ it isnt valid
		if (isc_substr_count($email, '@') != 1) {
			return false;
		}

		$matches = array();
		$local_matches = array();
		preg_match(':^([^@]+)@([a-zA-Z0-9\-][a-zA-Z0-9\-\.]{0,254})$:', $email, $matches);

		if (count($matches) != 3) {
			return false;
		}

		$local = $matches[1];
		$domain = $matches[2];

		// If the local part has a space but isnt inside quotes its invalid
		if (isc_strpos($local, ' ') && (isc_substr($local, 0, 1) != '"' || isc_substr($local, -1, 1) != '"')) {
			return false;
		}

		// If there are not exactly 0 and 2 quotes
		if (isc_substr_count($local, '"') != 0 && isc_substr_count($local, '"') != 2) {
			return false;
		}

		// if the local part starts or ends with a dot (.)
		if (isc_substr($local, 0, 1) == '.' || isc_substr($local, -1, 1) == '.') {
			return false;
		}

		// If the local string doesnt start and end with quotes
		if ((isc_strpos($local, '"') || isc_strpos($local, ' ')) && (isc_substr($local, 0, 1) != '"' || isc_substr($local, -1, 1) != '"')) {
			return false;
		}

		preg_match(':^([\ \"\w\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~\.]{1,64})$:', $local, $local_matches);

		// Check the domain has at least 1 dot in it
		if (isc_strpos($domain, '.') === false) {
			return false;
		}

		if (!empty($local_matches) ) {
			return true;
		} else {
			return false;
		}
	}

	function getProdImageURL($prodImageData = array(), $size = ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL)
	{
		$imageURL = '';
		if(!empty($prodImageData)) {

			$image = GetClass("ISC_PRODUCT_IMAGE");
			$image->populateFromDatabaseRow($prodImageData);

			try {
				$imageURL = $image->getResizedUrl($size, true);
			} catch (Exception $exception) {
				// do nothing, will result in returning blank string, which is fine
			}
		}
		return $imageURL;
	}

	/**
	 * Build the HTML for the thumbnail image of a product.
	 *
	 * @param string The filename of the thumbnail.
	 * @param string The URL that the thumbnail should link to.
	 * @param string The optional target for the link.
	 * @return string The built HTML for the thumbnail.
	 */
	function ImageThumb($imageData, $link='', $target='', $class='')
	{

		if(!is_array($imageData)) {
			$thumb = $imageData;
		} else {
			$thumb = getProdImageURL($imageData, ISC_PRODUCT_IMAGE_SIZE_THUMBNAIL);
		}
		if(!$thumb) {
			switch(GetConfig('DefaultProductImage')) {
				case 'template':
					$thumb = $GLOBALS['IMG_PATH'].'/ProductDefault.gif';
					break;
				case '':
					$thumb = '';
					break;
				default:
					$thumb = GetConfig('ShopPath').'/'.GetConfig('DefaultProductImage');
			}
		}
		/*
		else {
			$thumbPath = APP_ROOT.'/'.GetConfig('ImageDirectory').'/'.$thumb;
			$thumb = $GLOBALS['ShopPath'].'/'.GetConfig('ImageDirectory').'/'.$thumb;
		}
		*/
		if(!$thumb) {
			return '';
		}

		if($target != '') {
			$target = 'target="'.$target.'"';
		}

		if($class != '') {
			$class = 'class="'.$class.'"';
		}

		$imageThumb = '';
		if($link != '') {
			$imageThumb .= '<a href="'.$link.'" '.$target.' '.$class.'>';
		}

		$imageSize = @getimagesize($thumbPath);

		if(is_array($imageSize) && !empty($imageSize)) {
			$imageThumb .= '<img src="'.$thumb.'" alt="" ' . $imageSize[3] . ' />';
		}else{
			$imageThumb .= '<img src="'.$thumb.'" alt="" />';
		}

		if($link != '') {
			$imageThumb .= '</a>';
		}

		return $imageThumb;
	}

	/**
	 * Generate the link to a product.
	 *
	 * @param string The name of the product to generate the link to.
	 * @return string The generated link to the product.
	 */
	function ProdLink($prod)
	{
		if ($GLOBALS['EnableSEOUrls'] == 1) {
			return sprintf("%s/%s/%s.html", GetConfig('ShopPathNormal'), PRODUCT_LINK_PART, MakeURLSafe($prod));
		} else {
			return sprintf("%s/produto.php?produto=%s", GetConfig('ShopPathNormal'), MakeURLSafe($prod));
		}
	}

	/**
	 * Generate the link to a brand name.
	 *
	 * @param string The name of the brand (if null, the link to all brands is generated)
	 * @param array An optional array of query string arguments that need to be present.
	 * @param boolean Set to false to not separate query string arguments with &amp; but use & instead. Useful if generating a link to use for a redirect.
	 * @return string The generated link to the brand.
	 */
	function BrandLink($brand=null, $queryString=array(), $entityAmpersands=true)
	{
		// If we don't have a brand then we're just generating the link to the "all brands" page
		if($brand === null) {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link = sprintf("%s/%s/", $GLOBALS['ShopPathNormal'], BRAND_LINK_PART, MakeURLSafe($brand));
			} else {
				$link = sprintf("%s/brands.php", $GLOBALS['ShopPathNormal'], MakeURLSafe($brand));
			}
		}
		else {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link = sprintf("%s/%s/%s.html", $GLOBALS['ShopPathNormal'], BRAND_LINK_PART, MakeURLSafe($brand));
			} else {
				$link = sprintf("%s/brands.php?brand=%s", $GLOBALS['ShopPathNormal'], MakeURLSafe($brand));
			}
		}

		if($entityAmpersands) {
			$ampersand = '&amp;';
		}
		else {
			$ampersand = '&';
		}
		if(is_array($queryString) && count($queryString) > 0) {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link .= '?';
			}
			else {
				$link .= $ampersand;
			}
			$qString = array();
			foreach($queryString as $k => $v) {
				$qString[] = $k.'='.urlencode($v);
			}
			$link .= implode($ampersand, $qString);
		}

		return $link;
	}

	/**
	 * Generate a link to a specific vendor.
	 *
	 * @param array Array of details about the vendor to link to.
	 * @param array An optional array of query string arguments that need to be present.
	 * @return string The generated link to the vendor.
	 */
	function VendorLink($vendor="", $queryString=array())
	{
		$link = '';

		if(!is_array($vendor)) {
			if($GLOBALS['EnableSEOUrls'] == 1) {
				$link = GetConfig('ShopPathNormal').'/vendors/';
			}
			else {
				$link = GetConfig('ShopPathNormal').'/vendors.php';
			}
		}
		else if($GLOBALS['EnableSEOUrls'] == 1 && $vendor['vendorfriendlyname']) {
			$link = GetConfig('ShopPathNormal').'/vendors/'.$vendor['vendorfriendlyname'];
		}
		else {
			$link = GetConfig('ShopPathNormal').'/vendors.php?vendorid='.(int)$vendor['vendorid'];
		}

		if(is_array($queryString) && count($queryString) > 0) {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link .= '?';
			}
			else {
				$link .= '&';
			}
			$qString = array();
			foreach($queryString as $k => $v) {
				$qString[] = $k.'='.urlencode($v);
			}
			$link .= implode('&', $qString);
		}

		return $link;
	}

	/**
	 * Generate a link to browse the products belonging to a specific vendor.
	 *
	 * @param array Array of details about the vendor to link to.
	 * @param array An optional array of query string arguments that need to be present.
	 * @return string The generated link to the vendor.
	 */
	function VendorProductsLink($vendor, $queryString=array())
	{
		$link = '';
		if($GLOBALS['EnableSEOUrls'] == 1 && $vendor['vendorfriendlyname']) {
			$link = GetConfig('ShopPathNormal').'/vendors/'.$vendor['vendorfriendlyname'].'/item/';
		}
		else {
			$link = GetConfig('ShopPathNormal').'/vendors.php?vendorid='.(int)$vendor['vendorid'].'&action=produto';
		}

		if(is_array($queryString) && count($queryString) > 0) {
			if (strpos($link, '?') === false) {
				$link .= '?';
			}
			else {
				$link .= '&';
			}
			$qString = array();
			foreach($queryString as $k => $v) {
				$qString[] = $k.'='.urlencode($v);
			}
			$link .= implode('&', $qString);
		}

		return $link;
	}

	/**
	 * Generate the link to a particular tag or a list of tags.
	 *
	 * @param string The friendly name of the tag (if we have one)
	 * @param string the ID of the tag (if we have one)
	 * @param array An optional array of query string arguments that need to be present.
	 * @return string The generated link to the tag.
	 */
	function TagLink($friendlyName="", $tagId=0, $queryString=array())
	{
		$link = '';

		if($GLOBALS['EnableSEOUrls'] == 1 && $friendlyName) {
			$link = GetConfig('ShopPathNormal').'/tags/'.$friendlyName;
		}
		else if($tagId) {
			$link = GetConfig('ShopPathNormal').'/tags.php?tagid='.(int)$tagId;
		}
		else {
			if($GLOBALS['EnableSEOUrls'] == 1) {
				$link = GetConfig('ShopPathNormal').'/tags/';
			}
			else {
				$link = GetConfig('ShopPathNormal').'/tags.php';
			}
		}

		if(is_array($queryString) && count($queryString) > 0) {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link .= '?';
			}
			else {
				$link .= '&';
			}
			$qString = array();
			foreach($queryString as $k => $v) {
				$qString[] = $k.'='.urlencode($v);
			}
			$link .= implode('&', $qString);
		}

		return $link;
	}

	/**
	* Generate the link to the initial sitemap page
	*
	*/
	function SitemapLink ()
	{
		$url = GetConfig('ShopPathNormal') . '/';

		if ($GLOBALS['EnableSEOUrls'] == 1) {
			$url .= 'sitemap/';

		} else {
			$url .= 'sitemap.php';
		}

		return $url;
	}

	/**
	 * Generate the link to a category.
	 *
	 * @param int The ID of the category to generate the link to.
	 * @param string The name of the category to generate the link to.
	 * @param boolean Set to true to base this link as a root category link.
	 * @param array An optional array of query string arguments that need to be present.
	 * @return string The generated link to the category.
	 */
	function CatLink($CategoryId, $CategoryName, $parent=false, $queryString=array())
	{
		// Workout the category link, starting from the bottom and working up
		$link = "";
		$arrCats = array();

		if ($parent === true) {
			$parent = 0;
			$arrCats[] = $CategoryName;
		} else {
			static $categoryCache;

			if(!is_array($categoryCache)) {
				$categoryCache = array();
				$query = "SELECT catname, catparentid, categoryid FROM [|PREFIX|]categories order by catsort desc, catname asc";
				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
				while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
					$categoryCache[$row['categoryid']] = $row;
				}
			}
			if(empty($categoryCache)) {
				return '';
			}
			if (isset($categoryCache[$CategoryId])) {
				$parent = $categoryCache[$CategoryId]['catparentid'];

				if ($parent == 0) {
					$arrCats[] = $categoryCache[$CategoryId]['catname'];
				} else {
					// Add the first category
					$arrCats[] = $CategoryName;
					$lastParent=0;
					while ($parent != 0 && $parent != $lastParent) {
						$arrCats[] = $categoryCache[$parent]['catname'];
						$lastParent = $categoryCache[$parent]['categoryid'];
						$parent = (int)$categoryCache[$parent]['catparentid'];
					}
				}
			}
		}

		$arrCats = array_reverse($arrCats);

		for ($i = 0; $i < count($arrCats); $i++) {
			$link .= sprintf("%s/", MakeURLSafe($arrCats[$i]));
		}

		// Now we reverse the array and concatenate the categories to form the link
		if ($GLOBALS['EnableSEOUrls'] == 1) {
			$link = sprintf("%s/%s/%s", $GLOBALS['ShopPathNormal'], CAT_LINK_PART, $link);
		} else {
			$link = trim($link, "/");
			$link = sprintf("%s/departamento.php?category=%s", $GLOBALS['ShopPathNormal'], $link);
		}

		if(is_array($queryString) && !empty($queryString)) {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link .= '?';
			}
			else {
				$link .= '&';
			}
			$link .= http_build_query($queryString);
		}

		return $link;
	}

	/**
	 * Generate the link to a search results page.
	 *
	 * @param array An array of search terms/arguments
	 * @param int The page number we're currently on.
	 * @param string Set to true to prefix with the search page URL.
	 * @return string The search results page URL.
	 */
	function SearchLink($Query, $Page, $AppendSearchURL=true)
	{
		$search_link = '';
		foreach ($Query as $field => $term) {
			if ($term && is_array($term)) {
				$terms = $term;
				$term = '';
				foreach ($terms as $v) {
					$search_link .= sprintf("&%s[]=%s", $field, urlencode($v));
				}
			} else if ($term) {
				$search_link .= sprintf("&%s=%s", $field, urlencode($term));
			}
		}
		// Strip initial & off the search URL
		if ($AppendSearchURL !== false) {
			$search_link = isc_substr($search_link, 1);
			$search_link = sprintf("%s/search.php?%s&page=%d", $GLOBALS['ShopPathNormal'], $search_link, $Page);
		}
		return $search_link;
	}

	function fix_url($link)
	{
		if (isset($GLOBALS['KM']) || isset($_GET['bk'])) {
			if(isset($GLOBALS['KM'])) {
				$m = $GLOBALS['KM'];
			}
			else {
				$m = GetLang('BadLKHInv');
			}
			$GLOBALS['Message'] = MessageBox($m, MSG_ERROR);
		}
	}

	// Return a shopping cart link in standard format
	function CartLink($prodid=0)
	{
		if($prodid == 0) {
			return sprintf("%s/compras.php", $GLOBALS['ShopPathNormal']);
		}
		else {
			return sprintf("%s/compras.php?action=add&amp;product_id=%d", $GLOBALS['ShopPathNormal'], $prodid);
		}
	}

	// Return a blog link in standard format
	function BlogLink($blogid, $blogtitle)
	{
		if ($GLOBALS['EnableSEOUrls'] == 1) {
			return sprintf("%s/blog/%d/%s.html", $GLOBALS['ShopPathNormal'], $blogid, MakeURLSafe($blogtitle));
		} else {
			return sprintf("%s/anuncio.php?newsid=%s", $GLOBALS['ShopPathNormal'], $blogid);
		}
	}

	// Return a page link in standard format
	function PageLink($pageid, $pagetitle, $vendor=array())
	{
		$link = GetConfig('ShopPathNormal').'/';
		if(!empty($vendor)) {
			if($GLOBALS['EnableSEOUrls'] == 1 && $vendor['vendorfriendlyname']) {
				$link .= 'vendedor/'.$vendor['vendorfriendlyname'].'/'.MakeURLSafe($pagetitle).'.html';
			}
			else {
				$link .= 'vendedor.php?vendorid='.(int)$vendor['vendorid'].'&pageid='.(int)$pageid;
			}
		}
		else {
			if ($GLOBALS['EnableSEOUrls'] == 1) {
				$link .= 'conteudo/'.MakeURLSafe($pagetitle).'.html';
			}
			else {
				$link .= 'conteudo.php?pageid='.(int)$pageid;
			}
		}
		return $link;
	}

	/**
	* Get a link to the compare products page
	*
	* @param array The array of ids to compare
	*
	* @return string The html href
	*/
	function CompareLink($prodids=array())
	{
		$link = '';

		if ($GLOBALS['EnableSEOUrls'] == 1) {
			$link = $GLOBALS['ShopPathNormal'].'/comparar/';
		} else {
			$link = $GLOBALS['ShopPathNormal'].'/compare.php?';
		}

		// If no ids have been passed (e.g. for a form submit), then return
		// the base compare url
		if (empty($prodids)) {
			return $link;
		}

		// Make sure each of the product ids is an integer
		foreach ($prodids as $k => $v) {
			if (!is_numeric($v) || $v < 0) {
				unset($prodids[$k]);
			}
		}

		$link .= implode('/', $prodids);

		return $link;
	}

	// Return the extension of a file name
	function GetFileExtension($FileName)
	{
		$data = explode(".", $FileName);
		return $data[count($data)-1];
	}

	/**
	 * Convert a weight between the specified units.
	 *
	 * @param string The weight to convert.
	 * @param string The unit to convert the weight to.
	 * @param string Optionally, the unit to convert the weight from. If not specified, assumes the store default.
	 * @return string The converted weight.
	 */
	function ConvertWeight($weight, $toUnit, $fromUnit=null)
	{
		if(is_null($fromUnit)) {
			$fromUnit = GetConfig('WeightMeasurement');
		}
		$fromUnit = strtolower($fromUnit);
		$toUnit = strtolower($toUnit);

		$units = array(
				'pounds' => array('lbs', 'pounds', 'lb'),
				'kg' => array('kg', 'kgs', 'kilos', 'kilograms'),
				'gram' => array('g', 'grams')
		);

		foreach ($units as $unit) {
			if(in_array($fromUnit, $unit) && in_array($toUnit, $unit)) {
				return $weight;
			}
		}

		// First, let's convert back to a standardized measurement. We'll use grams.
		switch(strtolower($fromUnit)) {
			case 'lbs':
			case 'pounds':
			case 'lb':
				$weight *= 453.59237;
				break;
			case 'ounces':
				$weight *= 28.3495231;
				break;
			case 'kg':
			case 'kgs':
			case 'kilos':
			case 'kilograms':
				$weight *= 1000;
				break;
			case 'g':
			case 'grams':
				break;
			case 'tonnes':
				$weight *= 1000000;
				break;
		}

		// Now we're in a standardized measurement, start converting from grams to the unit we need
		switch(strtolower($toUnit)) {
			case 'lbs':
			case 'pounds':
			case 'lb':
				$weight *= 0.00220462262;
				break;
			case 'ounces':
				$weight *= 0.0352739619;
				break;
			case 'kg':
			case 'kgs':
			case 'kilos':
			case 'kilograms':
				$weight *= 0.001;
				break;
			case 'g':
			case 'grams':
				break;
			case 'tonnes':
				$weight *= 0.000001;
				break;
		}
		return $weight;
	}

	/**
	 * Convert a length between the specified units.
	 *
	 * @param string The length to convert.
	 * @param string The unit to convert the length to.
	 * @param string Optionally, the unit to convert the length from. If not specified, assumes the store default.
	 * @return string The converted length.
	 */
	function ConvertLength($length, $toUnit, $fromUnit=null)
	{
		if(is_null($fromUnit)) {
			$fromUnit = GetConfig('LengthMeasurement');
		}

		// First, let's convert back to a standardized measurement. We'll use millimetres
		switch(strtolower($fromUnit)) {
			case 'inches':
			case 'in':
			{
				$length *= 25.4;
				break;
			}
			case 'centimeters':
			case 'centimetres':
			case 'cm':
			{
				$length *= 10;
				break;
			}
			case 'metres':
			case 'meters':
			case 'm':
			{
				$length *= 10;
				break;
			}
			case 'millimetres':
			case 'millimeters':
			case 'mm':
			{
				break;
			}
		}

		// Now we're in a standardized measurement, start converting from grams to the unit we need
		switch(strtolower($toUnit)) {
			case 'inches':
			case 'in':
			{
				$length *= 0.0393700787;
				break;
			}

			case 'centimeters':
			case 'centimetres':
			case 'cm':
			{
				$length *= 0.1;
				break;
			}
			case 'metres':
			case 'meters':
			case 'm':
			{
				$length *= 0.001;
				break;
			}
			case 'mm':
			case 'millimetres':
			case 'millimeters':
			{
				break;
			}
		}

		return $length;
	}

	/**
	 * Calculate the weight adjustment for a variation of a product.
	 *
	 * @param string The base weight of the product.
	 * @param string The type of adjustment to be performed (empty, add, subtract, fixed)
	 * @param string The value to be adjusted by
	 * @return string The adjusted value
	*/
	function CalcProductVariationWeight($baseWeight, $type, $difference)
	{
		switch($type) {
			case "fixed":
				return $difference;
				break;
			case "add":
				return $baseWeight + $difference;
				break;
			case "subtract":
				$adjustedWeight = $baseWeight - $difference;
				if($adjustedWeight <= 0) {
					$adjustedWeight = 0;
				}
				return $adjustedWeight;
				break;
			default:
				return $baseWeight;
		}
	}

	function mhash1($token = 5)
	{
		
	}

	/**
	 * Fetch the name of a product from the passed product ID.
	 *
	 * @param int The ID of the product.
	 * @return string The name of the product.
	 */
	function GetProdNameById($prodid)
	{
		$query = "
			SELECT prodname
			FROM [|PREFIX|]products
			WHERE productid='".(int)$prodid."'
		";
		return $GLOBALS['ISC_CLASS_DB']->FetchOne($query);
	}

	/**
	 * Check if the passed string is indeed valid ID for an item.
	 *
	 * @param string The string to check that's a valid ID.
	 * @return boolean True if valid, false if not.
	 */
	function isId($id)
	{
		// If the type casted version fo the integer is the same as what's passed
		// and the integer is > 0, then it's a valid ID.
		if(isc_is_int($id) && $id > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Check if passed string is a price (decimal) format
	 *
	 * @param string The The string to check that's a valid price.
	 * @return boolean True if valid, false if not
	 */
	function IsPrice($price)
	{
		// Format the price as we'll be storing it internally
		$price = DefaultPriceFormat($price);

		// If the price contains anything other than [0-9.] then it's invalid
		if(preg_match('#[^0-9\.]#i', $price)) {
			return false;
		}

		return true;
	}

	function gzte11($str)
	{
		
		if($GLOBALS['vendor']==0&&$str&(ISC_LARGEPRINT | ISC_MEDIUMPRINT | ISC_SMALLPRINT))
			return true;
		else if($GLOBALS['vendor']==1&&$str&(ISC_HUGEPRINT | ISC_LARGEPRINT | ISC_MEDIUMPRINT | ISC_SMALLPRINT))
			return true;
			/*
		switch ($dbDump) {
			case ISC_HUGEPRINT:
				$b = ISC_HUGEPRINT | ISC_LARGEPRINT | ISC_MEDIUMPRINT | ISC_SMALLPRINT;
				break;
			case ISC_LARGEPRINT:
				$b = ISC_LARGEPRINT | ISC_MEDIUMPRINT | ISC_SMALLPRINT;
				break;
			case ISC_MEDIUMPRINT:
				$b = ISC_MEDIUMPRINT | ISC_SMALLPRINT;
				break;
			case ISC_SMALLPRINT:
				$b = ISC_SMALLPRINT;
				break;
		}
		if (($str & $b) == $str) {
			return true;
		}
		else {
			return false;
		}*/
	}

	function FormatWeight($weight, $includemeasure=false)
	{
		$num = number_format($weight, GetConfig('DimensionsDecimalPlaces'), GetConfig('DimensionsDecimalToken'), GetConfig('DimensionsThousandsToken'));

		if ($includemeasure) {
			$num .= " " . GetConfig('WeightMeasurement');
		}

		return $num;
	}

	/**
	* Format a number using the configured decimal and thousand tokens to an optional number of decimal places
	*
	* @param mixed The number to format
	* @param int The number of decimal places to format the number to. If -1 is specified (default) then the number of decimal places in the original number will be used.
	* @return string The formatted number
	*/
	function FormatNumber($number, $decimalPlaces = -1)
	{
		// drop off any excess zeroes in the fractional component
		$number /= 1;

		if ($decimalPlaces == -1) {
			if (strrchr($number, '.')) {
				$decimalPlaces = strlen(strrchr($number, '.')) - 1;
			}
		}

		if ($decimalPlaces < 0) {
			$decimalPlaces = 0;
		}

		$number = number_format($number, $decimalPlaces, GetConfig('DimensionsDecimalToken'), GetConfig('DimensionsThousandsToken'));

		return $number;
	}

	function SetPGQVariablesManually()
	{
		// Retrieve the query string variables. Can't use the $_GET array
		// because of SEO friendly links in the URL

		if(!isset($_SERVER['REQUEST_URI'])) {
			return;
		}

		$uri = $_SERVER['REQUEST_URI'];
		$tempRay = explode("?", $uri);
		$_SERVER['REQUEST_URI'] = $tempRay[0];

		if (is_numeric(isc_strpos($uri,"?"))) {
			$tempRay2 = explode("&",$tempRay[1]);
			foreach ($tempRay2 as $key => $value) {
				if(!$key) {
					continue;
				}
				$tempRay3 = array();
				$tempRay3 = explode("=",$value);
				if(!isset($tempRay3[1])) {
					$tempRay3[1] = '';
				}
				$_GET[$tempRay3[0]] = urldecode($tempRay3[1]);
				$_REQUEST[$tempRay3[0]] = urldecode($tempRay3[1]);
			}
		}
	}

	/**
	 * Check if PHPs GD module is enabled and PNG images can be created.
	 *
	 * @return boolean True if GD is enabled, false if not.
	 */
	function GDEnabledPNG()
	{
		if (function_exists('imageCreateFromPNG')) {
			return true;
		}
		return false;
	}

	function CleanPath($path)
	{
		// init
		$result = array();

		if (IsWindowsServer()) {
			// if its windows we need to change the path a bit!
			$path = str_replace("\\","/",$path);
			$driveletter = isc_substr($path,0,2);
			$path = isc_substr($path,2);
		}

		$pathA = explode('/', $path);

		if (!$pathA[0]) {
			$result[] = '';
		}

		foreach ($pathA as $key => $dir) {
			if ($dir == '..') {
				if (end($result) == '..') {
					$result[] = '..';
				} else if (!array_pop($result)) {
					$result[] = '..';
				}
			} else if ($dir && $dir != '.') {
				$result[] = $dir;
			}
		}

		if (!end($pathA)) {
			$result[] = '';
		}

		$path = implode('/', $result);

		if (IsWindowsServer()) {
			// if its windows we need to add the drive letter back on
			$path = $driveletter . $path;
		}
		if (isc_substr($path,isc_strlen($path)-1,1) == '/' && strlen($path) > 1) {
			$path = isc_substr($path,0,isc_strlen($path)-1);
		}
		return $path;
	}

	function cache_time($Page)
	{
		// Check the cache time on a page. If it's expired then return a new cache time
		if($Page == '') {
			return 0;
		}
		else {
			return rand(10, 100);
		}
	}

	/**
	 * Is the current server a Microsoft Windows based server?
	 *
	 * @return boolean True if Microsoft Windows, false if not.
	 */
	function IsWindowsServer()
	{
		if(isc_substr(isc_strtolower(PHP_OS), 0, 3) == 'win') {
			return true;
		}
		else {
			return false;
		}
	}

	function hex2rgb($hex)
	{
		// If the first char is a # strip it off
		if (isc_substr($hex, 0, 1) == '#') {
			$hex = isc_substr($hex, 1);
		}

		// If the string isnt the right length return false
		if (isc_strlen($hex) != 6) {
			return false;
		}

		$vals = array();
		$vals[] = hexdec(isc_substr($hex, 0, 2));
		$vals[] = hexdec(isc_substr($hex, 2, 2));
		$vals[] = hexdec(isc_substr($hex, 4, 2));
		$vals['r'] = $vals[0];
		$vals['g'] = $vals[1];
		$vals['b'] = $vals[2];
		return $vals;
	}

	function isnumeric($num)
	{
		
	}

	// the main function that draws the gradient
	function gd_gradient_fill($im,$direction,$start,$end)
	{

		switch ($direction) {
			case 'horizontal':
				$line_numbers = imagesx($im);
				$line_width = imagesy($im);
				list($r1,$g1,$b1) = hex2rgb($start);
				list($r2,$g2,$b2) = hex2rgb($end);
				break;
			case 'vertical':
				$line_numbers = imagesy($im);
				$line_width = imagesx($im);
				list($r1,$g1,$b1) = hex2rgb($start);
				list($r2,$g2,$b2) = hex2rgb($end);
				break;
			case 'ellipse':
			case 'circle':
				$line_numbers = sqrt(pow(imagesx($im),2)+pow(imagesy($im),2));
				$center_x = imagesx($im)/2;
				$center_y = imagesy($im)/2;
				list($r1,$g1,$b1) = hex2rgb($end);
				list($r2,$g2,$b2) = hex2rgb($start);
				break;
			case 'square':
			case 'rectangle':
				$width = imagesx($im);
				$height = imagesy($im);
				$line_numbers = max($width,$height)/2;
				list($r1,$g1,$b1) = hex2rgb($end);
				list($r2,$g2,$b2) = hex2rgb($start);
				break;
			case 'diamond':
				list($r1,$g1,$b1) = hex2rgb($end);
				list($r2,$g2,$b2) = hex2rgb($start);
				$width = imagesx($im);
				$height = imagesy($im);
				if($height > $width) {
					$rh = 1;
				}
				else {
					$rh = $width/$height;
				}
				if($width > $height) {
					$rw = 1;
				}
				else {
					$rw = $height/$width;
				}
				$line_numbers = min($width,$height);
				break;
			default:
				list($r,$g,$b) = hex2rgb($start);
				$col = imagecolorallocate($im,$r,$g,$b);
				imagefill($im, 0, 0, $col);
				return true;

		}

		for( $i = 0; $i < $line_numbers; $i=$i+1 ){
			if( $r2 - $r1 != 0 ) {
				$r = $r1 + ( $r2 - $r1 ) * ( $i / $line_numbers );
			}
			else {
				$r = $r1;
			}
			if( $g2 - $g1 != 0 ) {
				$g = $g1 + ( $g2 - $g1 ) * ( $i / $line_numbers );
			}
			else {
				$g1;
			}
			if( $b2 - $b1 != 0 ) {
				$b = $b1 + ( $b2 - $b1 ) * ( $i / $line_numbers );
			}
			else {
				$b = $b1;
			}
			$fill = imagecolorallocate($im, $r, $g, $b);
			switch ($direction) {
				case 'vertical':
					imageline($im, 0, $i, $line_width, $i, $fill);
					break;
				case 'horizontal':
					imageline($im, $i, 0, $i, $line_width, $fill);
					break;
				case 'ellipse':
				case 'circle':
					imagefilledellipse($im,$center_x, $center_y, $line_numbers-$i, $line_numbers-$i,$fill);
					break;
				case 'square':
				case 'rectangle':
					imagefilledrectangle($im,$i*$width/$height,$i*$height/$width,$width-($i*$width/$height), $height-($i*$height/$width),$fill);
					break;
				case 'diamond':
					imagefilledpolygon($im, array (
					$width/2, $i*$rw-0.5*$height,
					$i*$rh-0.5*$width, $height/2,
					$width/2,1.5*$height-$i*$rw,
					1.5*$width-$i*$rh, $height/2 ), 4, $fill);
					break;
				default:
			}
		}
	}

	function CEpoch($Val)
	{
		// Converts a time() value to a relative date value
		$stamp = time() - (time() - $Val);
		return isc_date(GetConfig('ExportDateFormat'), $stamp);
	}

	function CDate($Val)
	{
		return isc_date(GetConfig('DisplayDateFormat'), $Val);
	}

	function CStamp($Val)
	{
		return isc_date(GetConfig('DisplayDateFormat') ." h:i A", $Val);
	}

	function CFloat($Val)
	{
		$Val = str_replace(GetConfig('CurrencyToken'), "", $Val);
		$Val = str_replace(GetConfig('ThousandsToken'), "", $Val);
		settype($Val, "double");
		$Val = number_format($Val, GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), "");
		return $Val;
	}

	function CNumeric($Val)
	{
		$Val = preg_replace("#[^0-9\.\,]+#i", "", $Val);
		$Val = str_replace(GetConfig('ThousandsToken'), "", $Val);
		$Val = str_replace(GetConfig('DecimalToken'), ".", $Val);
		$Val = number_format($Val, GetConfig('DecimalPlaces'), ".", "");
		return $Val;
	}

	function CDbl($Val)
	{
		$Val = str_replace(GetConfig('CurrencyToken'), "", $Val);
		$Val = str_replace(GetConfig('ThousandsToken'), "", $Val);
		$Val = number_format($Val, GetConfig('DecimalPlaces'), GetConfig('DecimalToken'), GetConfig('ThousandsToken'));
		settype($Val, "double");
		return $Val;
	}

	/**
	 * Convert a localized weight or dimension back to the standardized western format.
	 *
	 * @param string The weight to convert.
	 * @return string The converted weight.
	 */
	function DefaultDimensionFormat($dimension)
	{
		$dimension = preg_replace("#[^0-9\.\,]+#i", "", $dimension);
		$dimension = str_replace(GetConfig('DimensionsThousandsToken'), "", $dimension);

		if(GetConfig('DimensionsDecimalToken') != '.') {
			$dimension = str_replace(GetConfig('DimensionsDecimalToken'), ".", $dimension);
		}

		$dimension = number_format(doubleval($dimension), GetConfig('DimensionsDecimalPlaces'), ".", "");

		return $dimension;
	}


	function GenRandFileName($FileName, $Append="")
	{
		// Generates a random filename to store images and product downloads.
		// Adds 5 random characters to the end of the file name.
		// Gets the original file extension from $FileName

		// Have the random characters already been added to the filename?
		if (!is_numeric(isc_strpos($FileName, "__"))) {
			$fileName = "";
			$tmp = explode(".", $FileName);
			$ext = isc_strtolower($tmp[count($tmp)-1]);
			$FileName = isc_strtolower($FileName);
			$FileName = str_replace("." . $ext, "", $FileName);

			for ($i = 0; $i < 5; $i++) {
				$fileName .= rand(0,9);
			}

			return sprintf("%s__%s.%s", $FileName,$fileName, $ext);
		} else {
			$tmp = explode(".", $FileName);
			$ext = isc_strtolower($tmp[count($tmp)-1]);
			$FileName = isc_strtolower($FileName);
			if ($Append != '') {
				$FileName = str_replace("." . $ext, sprintf("_%s", $Append) . "." . $ext, $FileName);
			}
			return $FileName;
		}
	}

	function ProductExists($ProdId)
	{
		if (!isId($ProdId)) {
			return false;
		}

		// Check if a record is found for a product and return true/false
		$query = sprintf("select 'exists' from [|PREFIX|]products where productid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($ProdId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function ReviewExists($ReviewId)
	{
		// Check if a record is found for a product and return true/false
		$query = sprintf("select reviewid from [|PREFIX|]reviews where reviewid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($ReviewId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function ConvertDateToTime($Stamp)
	{
		$vals = explode("/", $Stamp);
		return isc_gmmktime(0, 0, 0, $vals[0], $vals[1], $vals[2]);
	}


	function GetStatesByCountryNameAsOptions($CountryName, &$NumberOfStates, $SelectedStateName="")
	{
		// Return a list of states as a JavaScript array
		$output = "";
		$query = sprintf("select stateid, statename from [|PREFIX|]country_states where statecountry=(select countryid from [|PREFIX|]countries where countryname='%s')", $GLOBALS['ISC_CLASS_DB']->Quote($CountryName));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		$NumberOfStates = $GLOBALS['ISC_CLASS_DB']->CountResult($result);

		while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			if ($row['statename'] == $SelectedStateName) {
				$sel = 'selected="selected"';
			} else {
				$sel = "";
			}

			$output .= sprintf("<option %s value='%d'>%s</option>", $sel, $row['stateid'], $row['statename']);
		}

		return $output;
	}

	/**
	 * Check if a product can be added to the customer's cart or not.
	 *
	 * @param array An array of information about the product.
	 * @return boolean True if the product can be sold. False if not.
	 */
	function CanAddToCart($product)
	{
		// If pricing is hidden, obviously it can't be added
		if(!GetConfig('ShowProductPrice') || $product['prodhideprice']  == 1) {
			return false;
		}

		// If this item is sold out, then obviously it can't be added
		else if($product['prodinvtrack'] == 1 && $product['prodcurrentinv'] <= 0) {
			return false;
		}

		// If purchasing is disabled, then oviously it cannot be added either
		else if(!$product['prodallowpurchases'] || !GetConfig('AllowPurchasing')) {
			return false;
		}

		// Otherwise, the product can be added to the cart
		return true;
	}

	/**
	 * Check if a product can be sold or not based on visibility, current stock level etc
	 */
	function IsProductSaleable($product)
	{
		if(!$product['prodallowpurchases']) {
			return false;
		}

		// Inventory tracking at product level
		if ($product['prodinvtrack'] == 1) {
			if ($product['prodcurrentinv'] <= 0) {
				return false;
			} else {
				return true;
			}
		}
		// Inventory tracking at product option level
		if ($product['prodinvtrack'] == 2) {
			$inventory = array();

			// What we do here is fetch a list of product options and return an array containing each option & its availablility
			$query = sprintf("select * from [|PREFIX|]product_variation_combinations where vcproductid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($product['productid']));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				if ($row['vcstock'] <= 0) {
					$inventory[$row['combinationid']] = false;
				} else {
					$inventory[$row['combinationid']] = true;
				}
			}
			return $inventory;
		}
		// No inventory tracking
		else {
			return true;
		}
	}

	function CustomerExists($CustId)
	{
		if (!isId($CustId)) {
			return false;
		}

		// Check if a record is found for a customer and return true/false
		$query = sprintf("select customerid from [|PREFIX|]customers where customerid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($CustId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function CustomerGroupExists($CustGroupId)
	{
		if (!isId($CustGroupId)) {
			return false;
		}

		// Check if a record is found for a customer and return true/false
		$query = sprintf("select customergroupid from [|PREFIX|]customer_group where customergroupid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($CustGroupId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function AddressExists($AddrId, $CustId = null)
	{
		// Check if a record is found for a customer and return true/false
		$query = "SELECT shipid FROM [|PREFIX|]shipping_addresses WHERE shipid='" . $GLOBALS['ISC_CLASS_DB']->Quote($AddrId) . "'";
		if (isId($CustId)) {
			$query .= " AND shipcustomerid='" . $GLOBALS['ISC_CLASS_DB']->Quote($CustId) . "'";
		}

		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function NewsExists($NewsId)
	{
		// Check if a record is found for a news post and return true/false
		$query = sprintf("select newsid from [|PREFIX|]news where newsid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($NewsId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function GenerateCouponCode()
	{
		// Generates a random string between 10 and 15 characters
		// which is then references back to the coupon database
		// to workout the discount, etc

		$len = rand(8, 12);

		// Always start the coupon code with a letter
		$retval = chr(rand(65, 90));

		for ($i = 0; $i < $len; $i++) {
			if (rand(1, 2) == 1) {
				$retval .= chr(rand(65, 90));
			} else {
				$retval .= chr(rand(48, 57));
			}
		}

		return $retval;
	}

	function CouponExists($CouponId)
	{
		// Check if a record is found for a coupon and return true/false
		$query = sprintf("select couponid from [|PREFIX|]coupons where couponid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($CouponId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function UserExists($UserId)
	{
		// Check if a record is found for a news post and return true/false
		$query = sprintf("select pk_userid from [|PREFIX|]users where pk_userid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($UserId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function PageExists($PageId)
	{
		// Check if a record is found for a page and return true/false
		$query = sprintf("select pageid from [|PREFIX|]pages where pageid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($PageId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			return true;
		} else {
			return false;
		}
	}

	function GetCountriesByIds($Ids)
	{
		$countries = array();
		$query = sprintf("select countryname from [|PREFIX|]countries where countryid in (%s)", $Ids);
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			array_push($countries, $row['countryname']);
		}

		return $countries;
	}

	function GetStatesByIds($Ids)
	{
		$Ids = trim($Ids, ",");
		$states = array();
		$query = sprintf("select statename from [|PREFIX|]country_states where stateid in (%s)", $Ids);
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
			array_push($states, $row['statename']);
		}

		return $states;
	}

	function regenerate_cache($Page)
	{
		// Regenerate the cache of a page if it's expired
		if ($Page != "" && (isset($GLOBALS[b('Q2hlY2tWZXJzaW9u')]) && $GLOBALS[b('Q2hlY2tWZXJzaW9u')] == true)) {
			$cache_time = ISC_CACHE_TIME;
			$cache_folder = ISC_CACHE_FOLDER;
			$cache_order = ISC_CACHE_ORDER;
			$cache_user = ISC_CACHE_USER;
			$cache_data = $cache_time . $cache_folder . $cache_order . $cache_user;
			// Can we regenerate the cache?
			if (!cache_exists($cache_data)) {
				$cache_built = true;
			}
		}
	}

	/**
	*	Generate a custom token that's unique to this customer
	*/
	function GenerateCustomerToken()
	{
		$rnd = rand(1, 99999);
		$uid = uniqid($rnd, true);
		return $uid;
	}

	/**
	*	Is the customer logged into his/her account?
	*/
	function CustomerIsSignedIn()
	{
		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');
		if ($GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerId()) {
			return true;
		} else {
			return false;
		}
	}

	function CustomerIsSignedInThroughOrder()
	{
		if (CustomerIsSignedIn()) {
			return true;
		}

		if ($GLOBALS['ISC_CLASS_CUSTOMER']->isCustomerOrderSession()) {
			return true;
		}

		return false;
	}

	/**
	*	Get the SKU of a product based on its ID
	*/
	function GetSKUByProductId($ProductId, $VariationId=0)
	{
		$sku = "";
		if($VariationId > 0) {
			$query = "SELECT vcsku FROM [|PREFIX|]product_variation_combinations WHERE combinationid='".(int)$VariationId."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$sku = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);
			if($sku) {
				return $sku;
			}
		}

		// Still here? Then we were either not fetching the SKU for a variation or this variation doesn't have a SKU - use the product SKU
		$query = "SELECT prodcode FROM [|PREFIX|]products WHERE productid='".(int)$ProductId."'";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$sku = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);
		return $sku;
	}

	/**
	*	Get the product type (digital or physical) of a product based on its ID
	*/
	function GetTypeByProductId($ProductId)
	{
		$prod_type = "";
		$query = sprintf("select prodtype from [|PREFIX|]products where productid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($ProductId));
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

		if ($row !== false) {
			$prod_type = $row['prodtype'];
		}

		return $prod_type;
	}

	if (!function_exists('instr')) {
		function instr($needle,$haystack)
		{
			return (bool)(isc_strpos($haystack,$needle)!==false);
		}
	}


	if (!defined('FILE_USE_INCLUDE_PATH')) {
		define('FILE_USE_INCLUDE_PATH', 1);
	}

	if (!defined('LOCK_EX')) {
		define('LOCK_EX', 2);
	}

	if (!defined('FILE_APPEND')) {
		define('FILE_APPEND', 8);
	}

	/**
	 * Builds an array of product search terms from an array of input (handles advanced language searching, category selections)
	 *
	 * @param array Array of search input
	 * @return array Formatted search input array
	 */
	function BuildProductSearchTerms($input)
	{

		$searchTerms = array();
		$matches = array();
		// Here we parse out any advanced search identifiers from the search query such as price:, :rating etc

		$advanced_params = array(GetLang('SearchLangPrice'), GetLang('SearchLangRating'), GetLang('SearchLangInStock'), GetLang('SearchLangFeatured'), GetLang('SearchLangFreeShipping'));
		if (isset($input['search_query'])) {
			$query = str_replace(array("&lt;", "&gt;"), array("<", ">"), $input['search_query']);

			foreach ($advanced_params as $param) {
				if ($param == GetLang('SearchLangPrice') || $param == GetLang('SearchLangRating')) {
					$match = sprintf("(<|>)?([0-9\.%s]+)-?([0-9\.%s]+)?", preg_quote(GetConfig('CurrencyToken'), "#"), preg_quote(GetConfig('CurrencyToken'), "#"));
				} else if ($param == GetLang('SearchLangFeatured') || $param == GetLang('SearchLangInStock') || $param == GetLang('SearchLangFreeShipping')) {
					$match = "(true|false|yes|no|1|0|".preg_quote(GetLang('SearchLangYes'), "#")."|".preg_quote(GetLang('SearchLangNo'), "#").")";
				} else {
					continue;
				}
				preg_match("#\s".preg_quote($param, "#").":".$match.'(\s|$)#i', $query, $matches);
				if (count($matches) > 0) {
					if ($param == "price" || $param == "rating") {
						if ($matches[3]) {
							$input[$param.'_from'] = (float)$matches[2];
							$input[$param.'_to'] = (float)$matches[3];
						} else {
							if ($matches[1] == "<") {
								$input[$param.'_to'] = (float)$matches[2];
							} else if ($matches[1] == ">") {
								$input[$param.'_from'] = (float)$matches[2];
							} else if ($matches[1] == "") {
								$input[$param] = (float)$matches[2];
							}
						}
					} else if ($param == "featured" || $param == "instock" || $param == "freeshipping") {
						if ($param == "freeshipping") {
							$param = "shipping";
						}
						if ($matches[1] == "true" || $matches[1] == "yes" || $matches[1] == 1) {
							$input[$param] = 1;
						}
						else {
							$input[$param] = 0;
						}
					}
					$matches[0] = str_replace(array("<", ">"), array("&lt;", "&gt;"), $matches[0]);
					$input['search_query'] = trim(preg_replace("#".preg_quote(trim($matches[0]), "#")."#i", "", $input['search_query']));
				}
			}
			// Pass the modified search query back
			$searchTerms['search_query'] = $input['search_query'];
		}

		if(isset($input['searchtype'])) {
			$searchTerms['searchtype'] = $input['searchtype'];
		}

		if(isset($input['categoryid'])) {
			$input['category'] = $input['categoryid'];
		}

		if (isset($input['category'])) {
			if (!is_array($input['category'])) {
				$input['category'] = array($input['category']);
			}
			$searchTerms['category'] = $input['category'];
		}

		if (isset($input['searchsubs']) && $input['searchsubs'] != "") {
			$searchTerms['searchsubs'] = $input['searchsubs'];
		}

		if (isset($input['price']) && $input['price'] != "") {
			$searchTerms['price'] = $input['price'];
		}

		if (isset($input['price_from']) && $input['price_from'] != "") {
			$searchTerms['price_from'] = $input['price_from'];
		}

		if (isset($input['price_to']) && $input['price_to'] != "") {
			$searchTerms['price_to'] = $input['price_to'];
		}

		if (isset($input['rating']) && $input['rating'] != "") {
			$searchTerms['rating'] = $input['rating'];
		}

		if (isset($input['rating_from']) && $input['rating_from'] != "") {
			$searchTerms['rating_from'] = $input['rating_from'];
		}

		if (isset($input['rating_to']) && $input['rating_to'] != "") {
			$searchTerms['rating_to'] = $input['rating_to'];
		}

		if (isset($input['featured']) && is_numeric($input['featured']) != "") {
			$searchTerms['featured'] = (int)$input['featured'];
		}

		if (isset($input['shipping']) && is_numeric($input['shipping']) != "") {
			$searchTerms['shipping'] = (int)$input['shipping'];
		}

		if (isset($input['instock']) && is_numeric($input['instock'])) {
			$searchTerms['instock'] = (int)$input['instock'];
		}

		if (isset($input['brand']) && is_numeric($input['brand'])) {
			$searchTerms['brand'] = (int)$input['brand'];
		}

		return $searchTerms;
	}

	/**
	*	Get all of the child categories for category with ID $parent
	*/
	function GetChildCats($parent=0)
	{
		static $nodesByPid;
		if (!isset($nodesByPid) || !@is_array($nodesByPid)) {
			$tree = new Tree();
			$query = "SELECT * FROM [|PREFIX|]categories";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$nodesByPid[(int) $row['catparentid']][] = (int) $row['categoryid'];
			}

			$called = true;
		}

		$children = array();

		if (!@is_array($nodesByPid[$parent])) {
			return $children;
		}

		foreach ($nodesByPid[$parent] as $categoryid) {
			$children[] = $categoryid;
			// Fetch nested children
			if (@is_array($nodesByPid[$categoryid])) {
				$children = array_merge($children, GetChildCats($categoryid));
			}
		}

		return $children;
	}

	/**
	 * Build an SQL query for the specified search terms.
	 *
	 * @param array Array of search terms
	 * @param string String of fields to match
	 * @param string The field to sort by
	 * @param string The order to sort results by
	 * @return array An array containing the query to count the number of results and a query to perform the search
	 */
	function BuildProductSearchQuery($searchTerms, $fields="", $sortField=array("score", "proddateadded"), $sortOrder="desc")
	{
		$queryWhere = array();
		$joinQuery = '';

		// Construct the full text search part of the query
		$fulltext_fields = array("ps.prodname", "ps.prodcode", "ps.proddesc", "ps.prodsearchkeywords");

		if (!$fields) {
			$fields = "p.*, FLOOR(p.prodratingtotal/p.prodnumratings) AS prodavgrating, ".GetProdCustomerGroupPriceSQL().", ";
			$fields .= "pi.* ";
			if (isset($searchTerms['search_query']) && $searchTerms['search_query'] != "") {
				$fields .= ', '.$GLOBALS['ISC_CLASS_DB']->FullText($fulltext_fields, $searchTerms['search_query'], false) . " as score ";
			}
		}

		if(isset($searchTerms['categoryid'])) {
			$searchTerms['category'] = array($searchTerms['categoryid']);
		}

		// If we're searching by category, we need to completely
		// restructure the search query - so do that first
		$categorySearch = false;
		$categoryIds = array();
		if(isset($searchTerms['category']) && is_array($searchTerms['category'])) {
			foreach($searchTerms['category'] as $categoryId) {
				// All categories were selected, so don't continue
				if($categoryId == 0) {
					$categorySearch = false;
					break;
				}

				$categoryIds[] = (int)$categoryId;

				// If searching sub categories automatically, fetch & tack them on
				if(isset($searchTerms['searchsubs']) && $searchTerms['searchsubs'] == 'ON') {
					$categoryIds = array_merge($categoryIds, GetChildCats($categoryId));
				}
			}

			$categoryIds = array_unique($categoryIds);
			if(!empty($categoryIds)) {
				$categorySearch = true;
			}
		}

		if($categorySearch == true) {
			$fromTable = '[|PREFIX|]categoryassociations a, [|PREFIX|]products p';
			$queryWhere[] = 'a.productid=p.productid AND a.categoryid IN ('.implode(',', $categoryIds).')';
		}
		else {
			$fromTable = '[|PREFIX|]products p';
		}

		if (isset($searchTerms['search_query']) && $searchTerms['search_query'] != "") {
			// Only need the product search table if we have a search query
			$joinQuery .= "INNER JOIN [|PREFIX|]product_search ps ON (p.productid=ps.productid) ";
		} else if ($sortField == "score") {
			// If we don't, we better make sure we're not sorting by score
			$sortField = "p.prodname";
			$sortOrder = "ASC";
		}

		$joinQuery .= "LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1) ";

		$queryWhere[] = "p.prodvisible='1'";

		// Add in the group category restrictions
		$permissionSql = GetProdCustomerGroupPermissionsSQL(null, false);
		if($permissionSql) {
			$queryWhere[] = $permissionSql;
		}

		// Do we need to filter on brand?
		if (isset($searchTerms['brand']) && $searchTerms['brand'] != "") {
			$brand_id = (int)$searchTerms['brand'];
			$queryWhere[] = "p.prodbrandid='" . $GLOBALS['ISC_CLASS_DB']->Quote($brand_id) . "'";
		}

		// Do we need to filter on price?
		if (isset($searchTerms['price'])) {
			$queryWhere[] = "p.prodcalculatedprice='".$GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['price'])."'";
		} else {
			if (isset($searchTerms['price_from']) && is_numeric($searchTerms['price_from'])) {
				$queryWhere[] = "p.prodcalculatedprice >= '".$GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['price_from'])."'";
			}

			if (isset($searchTerms['price_to']) && is_numeric($searchTerms['price_to'])) {
				$queryWhere[] = "p.prodcalculatedprice <= '".$GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['price_to'])."'";
			}
		}

		// Do we need to filter on rating?
		if (isset($searchTerms['rating'])) {
			$queryWhere[] = "FLOOR(p.prodratingtotal/p.prodnumratings) = '".(int)$searchTerms['rating']."'";
		}
		else {
			if (isset($searchTerms['rating_from']) && is_numeric($searchTerms['rating_from'])) {
				$queryWhere[] = "FLOOR(p.prodratingtotal/p.prodnumratings) >= '".(int)$searchTerms['rating_from']."'";
			}

			if (isset($searchTerms['rating_to']) && is_numeric($searchTerms['rating_to'])) {
				$queryWhere[] = "FLOOR(p.prodratingtotal/p.prodnumratings) <= '".(int)$searchTerms['rating_to']."'";
			}
		}

		// Do we need to filter on featured?
		if (isset($searchTerms['featured']) && $searchTerms['featured'] != "") {
			$featured = (int)$searchTerms['featured'];

			if ($featured == 1) {
				$queryWhere[] = "p.prodfeatured=1";
			}
			else {
				$queryWhere[] = "p.prodfeatured=0";
			}
		}

		// Do we need to filter on free shipping?
		if (isset($searchTerms['shipping']) && $searchTerms['shipping'] != "") {
			$shipping = (int)$searchTerms['shipping'];

			if ($shipping == 1) {
				$queryWhere[] = "p.prodfreeshipping='1' ";
			}
			else {
				$queryWhere[] = "p.prodfreeshipping='0' ";
			}
		}

		// Do we need to filter only products we have in stock?
		if (isset($searchTerms['instock']) && $searchTerms['instock'] != "") {
			$stock = (int)$searchTerms['instock'];
			if ($stock == 1) {
				$queryWhere[] = "(p.prodcurrentinv>0 or p.prodinvtrack=0) ";
			}
		}

		if (isset($searchTerms['search_query']) && $searchTerms['search_query'] != "") {
			$termQuery = "(" . $GLOBALS['ISC_CLASS_DB']->FullText($fulltext_fields, $searchTerms['search_query'], true);
			$termQuery .= "OR ps.prodname like '%" . $GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['search_query']) . "%' ";
			$termQuery .= "OR ps.proddesc like '%" . $GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['search_query']) . "%' ";
			$termQuery .= "OR ps.prodsearchkeywords like '%" . $GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['search_query']) . "%' ";
			$termQuery .= "OR ps.prodcode = '" . $GLOBALS['ISC_CLASS_DB']->Quote($searchTerms['search_query']) . "') ";
			$queryWhere[] = $termQuery;
		}

		if (!is_array($sortField)) {
			$sortField = array($sortField);
		}

		if (!is_array($sortOrder)) {
			$sortOrder = array($sortOrder);
		}

		$sortField = array_filter($sortField);
		$sortOrder = array_filter($sortOrder);

		if (count($sortOrder) < count($sortField)) {
			$missing = count($sortField) - count($sortOrder);
			$sortOrder += array_fill(count($sortOrder), $missing, 'desc');
		} else if (count($sortOrder) > count($sortField)) {
			$sortOrder = array_slice($sortOrder, 0, count($sortField));
		}

		if (!empty($sortField)) {
			$orderBy = array();
			$sortField = array_values($sortField);
			$sortOrder = array_values($sortOrder);

			foreach ($sortField as $key => $field) {
				$orderBy[] = $field . ' ' . $sortOrder[$key];
			}

			$orderBy = ' ORDER BY ' . implode(',', $orderBy);
		} else {
			$orderBy = '';
		}

		$query = "
			SELECT ".$fields."
			FROM ".$fromTable."
			".$joinQuery."
			WHERE 1=1 AND ".implode(' AND ', $queryWhere).$orderBy;

		$countQuery = "
			SELECT COUNT(p.productid)
			FROM ".$fromTable."
			".$joinQuery."
			WHERE 1=1 AND ".implode(' AND ', $queryWhere);

		return array(
			'query' => $query,
			'countQuery' => $countQuery
		);
	}

	function GenerateRSSHeaderLink($link, $title="")
	{
		if (isset($title) && $title != "") {
			$rss_title = sprintf("%s (%s)", $title, GetLang('RSS20'));
			$atom_title = sprintf("%s (%s)", $title, GetLang('Atom03'));
		} else {
			$rss_title = GetLang('RSS20');
			$atom_title = GetLang('Atom03');
		}
		if (isc_strpos($link, '?') !== false) {
			$link .= '&';
		} else {
			$link .= '?';
		}
		$link = str_replace("&amp;", "&", $link);
		$link = str_replace("&", "&amp;", $link);
		$links = sprintf('<link rel="alternate" type="application/rss+xml" title="%s" href="%s" />'."\n", $rss_title, $link."type=rss");
		$links .= sprintf('<link rel="alternate" type="application/atom+xml" title="%s" href="%s" />'."\n", $atom_title, $link."type=atom");
		return $links;
	}

	function B($x)
	{
		return base64_decode($x);
	}

	/**
	 * Build a set of pagination links for large result sets.
	 *
	 * @param int The number of results
	 * @param int The number of results per page
	 * @param int The current page
	 * @param string The base URL to add page numbers to - use {page} placeholder to put page numbers in a specific part of the url
	 * @return string The built pagination
	 */
	function BuildPagination($resultCount, $perPage, $currentPage, $url, $precall='')
	{
		if ($resultCount <= $perPage) {
			return;
		}

		$pageCount = ceil($resultCount / $perPage);
		$pagination = '';

		if (!isset($GLOBALS['SmallNav'])) {
			$GLOBALS['SmallNav'] = '';
		}

		if ($currentPage > 1) {
			$pagination .= sprintf("<a href='%s'>&laquo;&laquo;</a> |", isc_html_escape(BuildPaginationUrl($url, 1, $precall)));
			$pagination .= sprintf(" <a href='%s'>&laquo; %s</a> |", isc_html_escape(BuildPaginationUrl($url, $currentPage - 1, $precall)), isc_html_escape(GetLang('Previous')));
			$GLOBALS['SmallNav'] .= sprintf(" <span style='cursor:pointer; text-decoration:underline' onclick=\"document.location.href='%s'\">&laquo; %s</span> |", isc_html_escape(BuildPaginationUrl($url, $currentPage - 1, $precall)), isc_html_escape(GetLang('Previous')));
		}
		else {
			$pagination .= '&laquo;&laquo; | &laquo;&nbsp;' . isc_html_escape(GetLang('Previous')) . '&nbsp;|';
		}

		$MaxLinks = 10;

		if ($pageCount > $MaxLinks) {
			$start = $currentPage - (floor($MaxLinks / 2));
			if ($start < 1) {
				$start = 1;
			}

			$end = $currentPage + (floor($MaxLinks / 2));
			if ($end > $pageCount) {
					$end = $pageCount;
			}
			if ($end < $MaxLinks) {
					$end = $MaxLinks;
			}

			$pagesToShow = ($end - $start);
			if (($pagesToShow < $MaxLinks) && ($pageCount > $MaxLinks)) {
				$start = $end - $MaxLinks + 1;
			}
		}
		else {
			$start = 1;
			$end = $pageCount;
		}

		for ($i = $start; $i <= $end; ++$i) {
			if ($i > $pageCount) {
				break;
			}

			$pagination .= '&nbsp;';
			if ($i == $currentPage) {
				$pagination .= sprintf(" <strong>%d</strong> |", $i);
			} else {
				$pagination .= sprintf(" <a href='%s'>%d</a> |", isc_html_escape(BuildPaginationUrl($url, $i, $precall)), $i);
			}
		}

		if ($currentPage == $pageCount) {
			$pagination .= '&nbsp;' . isc_html_escape(GetLang('Next')) . '&nbsp;&raquo; | &raquo;&raquo;';
		} else {
			$pagination .= sprintf(" <a href='%s'>%s &raquo;</a> |", isc_html_escape(BuildPaginationUrl($url, $currentPage + 1, $precall)), isc_html_escape(GetLang('Next')));
			$GLOBALS['SmallNav'] .= sprintf(" <span style='cursor:pointer; text-decoration:underline' onclick=\"document.location.href='%s'\">%s &raquo;</span> |", isc_html_escape(BuildPaginationUrl($url, $currentPage + 1, $precall)), isc_html_escape(GetLang('Next')));
			$pagination .= sprintf(" <a href='%s'>&raquo;&raquo;</a>", isc_html_escape(BuildPaginationUrl($url, $pageCount, $precall)));
		}

		return $pagination;
	}

	/**
	*
	* @param string $url
	* @param int $page
	* @param string $precall
	* @return string
	*/
	function BuildPaginationUrl($url, $page, $precall='')
	{
		if (isc_strpos($url, "{page}") === false) {
			if (isc_strpos($url, "?") === false) {
				$url .= "?";
			}
			else {
				$url .= "&";
			}
			$url .= "page=$page";
		}
		else {
			$url = str_replace("{page}", $page, $url);
		}

		if ($precall !== '') {
			if (isc_strpos($url, "?") === false) {
				$url .= "?";
			} else {
				$url .= "&";
			}

			$url .= "precall=" . $precall;
		}

		return $url;
	}

	/**
	* NiceSize
	*
	* Returns a datasize formatted into the most relevant units
	* @return string The formatted filesize
	* @param int Size In Bytes
	* @param int How many decimal places to use in the return
	* @param string Optionally force size into this format ('B', 'KB', 'MB', 'GB')
	*/
	function NiceSize($SizeInBytes=0, $Precision=2, $ForceToFormat='', $noPostFix=false)
	{
		static $map = array(
			'GB' => 1073741824, // 1024 to the power of 3
			'MB' => 1048576, // 1024 to the power of 2
			'KB' => 1024,
			'B' => 1
		);

		$key = '';

		if ($ForceToFormat !== '') {
			$key = strtoupper($ForceToFormat);
			if (!array_key_exists($key, $map)) {
				return false;
			}
		} else {
			foreach ($map as $k => $v) {
				if ($SizeInBytes >= $v) {
					$key = $k;
					break;
				}
			}
		}

		if (!isc_is_int($Precision)) {
			$Precision = 2;
		} else {
			$Precision = (int)$Precision;
		}

		if ($key == '') {
			$key = 'B';
		}

		if ($noPostFix) {
			return sprintf("%01." . $Precision . "f", $SizeInBytes / $map[$key]);
		} else {
			return sprintf("%01." . $Precision . "f %s", $SizeInBytes / $map[$key], $key);
		}
	}

	/**
	* NiceTime
	*
	* Returns a formatted timestamp
	* @return string The formatted string
	* @param int The unix timestamp to format
	*/
	function NiceTime($UnixTimestamp)
	{
		return isc_date('jS F Y H:i:s', $UnixTimestamp);
	}

	function AlphaOnly($str)
	{
		return preg_replace('/[^a-zA-Z]/','',$str);
	}

	function AlphaNumOnly($str)
	{
		return preg_replace('/[^a-zA-Z0-9]/','',$str);
	}

	function AlphaExtendedOnly($str)
	{
		return preg_replace('/[^a-zA-Z\-_ \.,]/','',$str);
	}

	function AlphaNumExtendedOnly($str)
	{
		return preg_replace('/[^a-zA-Z0-9\-_ \.,]/','',$str);
	}

	function gd_version()
	{
		$gd = gd_info();
		return $gd['GD Version'];
	}

	/**
	* CheckDirWritable
	* A function to determine if the directory is writable. PHP's built in function
	* doesn't always work as expected.
	* This function creates the file, writes to it, closes it and deletes it. If all
	* actions work, then the directory is writable.
	* PHP's inbuilt
	*
	* @param String $dir full directory to test if writable
	*
	* @return Boolean
	*/

	function CheckDirWritable($dir)
	{
		$tmpfilename = str_replace("//","/", $dir . time() . '.txt');

		$fp = @fopen($tmpfilename, 'w+');

		// check we can create a file
		if (!$fp) {
			return false;
		}

		// check we can write to the file
		if (!@fputs($fp, "testing write")) {
			return false;
		}

		// check we can close the connection
		if (!@fclose($fp)) {
			return false;
		}

		// check we can delete the file
		if (!@unlink($tmpfilename)) {
			return false;
		}

		// if we made it here, it all works. =)
		return true;

	}

	/**
	* CheckFileWritable
	* A function to determine if the directory is writable. PHP's built in function
	* doesn't always work as expected and not on all operating sytems.
	*
	* This function reads the file, grabs the content, then writes it back to the
	* file. If this all worked, the file is obviously writable.
	*
	* @param String $filename full path to the file to test
	*
	* @return Boolean
	*/

	function CheckFileWritable($filename)
	{

		$OrigContent = "";
		$fp = @fopen($filename, 'r+');

		// check we can read the file
		if (!$fp) {
			return false;
		}

		while (!feof($fp)) {
			$OrigContent .= fgets($fp, 8192);
		}

		// we read the file so the pointer is at the end
		// we need to put it back to the beginning to write!
		fseek($fp, 0);

		// check we can write to the file
		if (!@fputs($fp, $OrigContent)) {
			return false;
		}

		// check we can close the connection
		if (!fclose($fp)) {
			return false;
		}

		// if we made it here, it all works. =)
		return true;
	}

	function spr1ntf($z)
	{
		
	}

	/**
	 * Handle password authentication for a password imported from another store.
	 *
	 * @param The plain text version of the password to check.
	 * @param The imported password.
	 */
	function ValidImportPassword($password, $importedPassword)
	{
		list($system, $importedPassword) = explode(":", $importedPassword, 2);

		switch ($system) {
			case "osc":
			case "zct":
				// OsCommerce/ZenCart passwords are stored as md5(salt.password):salt
				list($saltedPass, $salt) = explode(":", $importedPassword);
				if (md5($salt.$password) == $saltedPass) {
					return true;
				} else {
					return false;
				}
				break;
		}

		return false;
	}

	function GetMaxUploadSize()
	{
		$sizes = array(
			"upload_max_filesize" => ini_get("upload_max_filesize"),
			"post_max_size" => ini_get("post_max_size")
		);
		$max_size = -1;
		foreach ($sizes as $size) {
			if (!$size) {
				continue;
			}
			$unit = isc_substr($size, -1);
			$size = isc_substr($size, 0, -1);
			switch (isc_strtolower($unit))
			{
				case "g":
					$size *= 1024;
				case "m":
					$size *= 1024;
				case "k":
					$size *= 1024;
			}
			if ($max_size == -1 || $size > $max_size) {
				$max_size = $size;
			}
		}
		return NiceSize($max_size);
	}

	/**
	*	Dump the contents of the server's MySQL database into a variable
	*/
	function mysql_dump()
	{
		
	}


	function getPostRedirectURL($ch, $header)
	{

		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// Request is not a redirect, so we don't need to follow it
		if(substr($responseCode, 0, 1) != 3) {
			return '';
		}

		// Grab the location match/redirect from the headers
		if(!preg_match('#Location:(.*)\n#', $header, $matches)) {
			return '';
		}
		// Determine the new URL to redirect to.
		// A web server can respond with Location: /blah.php or Location: ?test
		// which means use the pieces from the previous location.
		$redirectUrl = parse_url(trim($matches[1]));
		$currentUrl = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
		if(empty($redirectUrl['scheme'])) {
			$redirectUrl['scheme'] = $currentUrl['scheme'];
		}
		if(empty($redirectUrl['host'])) {
			$redirectUrl['host'] = $currentUrl['host'];
		}
		if(empty($redirectUrl['port'])) {
			if(isset($currentUrl['port'])) {
				$redirectUrl['port'] = $currentUrl['port'];
			} else {
				$redirectUrl['port'] = '80';
			}
		}
		if(empty($redirectUrl['path'])) {
			$redirectUrl['path'] = $currentUrl['path'];
		}

		$newUrl = $redirectUrl['scheme'].'://'.$redirectUrl['host'].$redirectUrl['path'];
		if($redirectUrl['query']) {
			$newUrl .= '?'.$redirectUrl['query'];
		}
		return $newUrl;

	}

	define('ISC_REMOTEFILE_ERROR_NONE', 0); // no error
	define('ISC_REMOTEFILE_ERROR_UNKNOWN', 1); // an error from the underlying transfer library that we haven't classified yet
	define('ISC_REMOTEFILE_ERROR_TIMEOUT', 2); // the request timed out before it completed
	define('ISC_REMOTEFILE_ERROR_EMPTY', 3); // the request was successful, but the response from the server was empty
	define('ISC_REMOTEFILE_ERROR_SENDFAIL', 4); // the request could not be sent - usually when fsockopen() fails or curl fails to init properly due to an internal error or invalid url etc.
	define('ISC_REMOTEFILE_ERROR_NOHOST', 5); // no host specified in the request URL
	define('ISC_REMOTEFILE_ERROR_TOOMANYREDIRECTS', 6); // too many redirect responses to follow
	define('ISC_REMOTEFILE_ERROR_LOGINDENIED', 7); // if authorisation was required but not given or incorrect authorisation details
	define('ISC_REMOTEFILE_ERROR_HTTPERROR', 8); // http error response from the remote server
	define('ISC_REMOTEFILE_ERROR_DNSFAIL', 9); // failed to lookup the host by dns

	/**
	*	Post to a remote file and return the response.
	*	Vars should be passed in URL format, i.e. x=1&y=2&z=3
	*
	* @param string $Path
	* @param string $Vars
	* @param int $timeout
	* @param int $error By-reference variable which will be populated with an error code from one of the defined ISC_REMOTEFILE_ERROR_? constants
	*/
	function PostToRemoteFileAndGetResponse($Path, $Vars="", $timeout=60, &$error = null)
	{
		if(function_exists("curl_exec")) {
			// Use CURL if it's available
			$ch = curl_init($Path);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($timeout > 0 && $timeout !== false) {
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			}

			// Setup the proxy settings if there are any
			if (GetConfig('HTTPProxyServer')) {
				curl_setopt($ch, CURLOPT_PROXY, GetConfig('HTTPProxyServer'));
				if (GetConfig('HTTPProxyPort')) {
					curl_setopt($ch, CURLOPT_PROXYPORT, GetConfig('HTTPProxyPort'));
				}
			}

			if (GetConfig('HTTPSSLVerifyPeer') == 0) {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			}

			// A blank encoding means accept all (defalte, gzip etc)
			if (defined('CURLOPT_ENCODING')) {
				curl_setopt($ch, CURLOPT_ENCODING, '');
			}

			if($Vars != "") {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $Vars);
			}

			if (!ISC_SAFEMODE && ini_get('open_basedir') == '') {
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$result = curl_exec($ch);
			} else {
				curl_setopt($ch, CURLOPT_HEADER, true);

				$curRequest = 1;
				while($curRequest <= 10) {
					$result = curl_exec($ch);

					// For any responses that include a 1xx Informational response at the
					// start, strip those off. An informational response is a response
					// consisting of only a status line and possibly headers. Terminated by CRLF.
					while(preg_match('#^HTTP/1\.1 1[0-9]{2}#', $result) && preg_match('#\r?\n\r?\n#', $result, $matches)) {
						$result = substr($result, strpos($result, $matches[0]) + strlen($matches[0]));
						$result = ltrim($result);
					}

					list($header, $result) = preg_split('#\r?\n\r?\n#', $result, 2);

					$newUrl = getPostRedirectURL($ch, $header);
					if($newUrl == '') {
						break;
					}
					curl_setopt($ch, CURLOPT_URL, $newUrl);
					$curRequest++;
				}
			}

			if ($result === false) {
				// something failed... there's quite a few other curl error codes but these are the most common
				// using numbers here instead of constants due to changes in php versions and libcurl versions
				$curlError = curl_errno($ch);
				switch ($curlError) {
					case 1: //CURLE_UNSUPPORTED_PROTOCOL
					case 2: //CURLE_FAILED_INIT
					case 3: //CURLE_URL_MALFORMAT
					case 7: //CURLE_COULDNT_CONNECT
					case 27: //CURLE_OUT_OF_MEMORY
					case 41: //CURLE_FUNCTION_NOT_FOUND
					case 55: //CURLE_SEND_ERROR
					case 56: //CURLE_RECV_ERROR
					$error = ISC_REMOTEFILE_ERROR_SENDFAIL;
					break;

					case 47: //CURLE_TOO_MANY_REDIRECTS
					$error = ISC_REMOTEFILE_ERROR_TOOMANYREDIRECTS;
					break;

					case 22: //CURLE_HTTP_RETURNED_ERROR
					$error = ISC_REMOTEFILE_ERROR_HTTPERROR;
					break;

					case 52: //CURLE_GOT_NOTHING
					$error = ISC_REMOTEFILE_ERROR_EMPTY;
					break;

					case 67: //CURLE_LOGIN_DENIED
					$error = ISC_REMOTEFILE_ERROR_LOGINDENIED;
					break;

					case 28: //CURLE_OPERATION_TIMEDOUT
					$error = ISC_REMOTEFILE_ERROR_TIMEOUT;
					break;

					case 5: //CURLE_COULDNT_RESOLVE_PROXY:
					case 6: //CURLE_COULDNT_RESOLVE_HOST:
					$error = ISC_REMOTEFILE_ERROR_DNSFAIL;
					break;

					default:
					$error = ISC_REMOTEFILE_ERROR_UNKNOWN;
					break;
				}
			}

			return $result;
		}
		else {
			// Use fsockopen instead
			$Path = @parse_url($Path);
			if(!isset($Path['host']) || $Path['host'] == '') {
				$error = ISC_REMOTEFILE_ERROR_NOHOST;
				return null;
			}
			if(!isset($Path['port'])) {
				$Path['port'] = 80;
			}
			if(!isset($Path['path'])) {
				$Path['path'] = '/';
			}
			if(isset($Path['query'])) {
				$Path['path'] .= "?".$Path['query'];
			}

			if(isset($Path['scheme']) && strtolower($Path['scheme']) == 'https') {
				$socketHost = 'ssl://'.$Path['host'];
				$Path['port'] = 443;
			}
			else {
				$socketHost = $Path['host'];
			}

			$fp = @fsockopen($Path['host'], $Path['port'], $errorNo, $error, 5);
			if(!$fp) {
				$error = ISC_REMOTEFILE_ERROR_SENDFAIL;
				return null;
			}

			$headers = array();

			// If we have one or more variables, perform a post request
			if($Vars != '') {
				$headers[] = "POST ".$Path['path']." HTTP/1.0";
				$headers[] = "Content-Length: ".strlen($Vars);
				$headers[] = "Content-Type: application/x-www-form-urlencoded";
			}
			// Otherwise, let's get.
			else {
				$headers[] = "GET ".$Path['path']." HTTP/1.0";
			}
			$headers[] = "Host: ".$Path['host'];
			$headers[] = "Connection: Close";
			$headers[] = ""; // Extra CRLF to indicate the start of the data transmission

			if($Vars != '') {
				$headers[] = $Vars;
			}

			if(!fwrite($fp, implode("\r\n", $headers))) {
				@fclose($fp);
				return false;
			}

			if($timeout > 0 && $timeout !== false) {
				@stream_set_timeout($fp, $timeout);
			}

			$result = '';
			$meta = stream_get_meta_data($fp);
			while(!feof($fp) && !$meta['timed_out']) {
				$result .= @fgets($fp, 12800);
				$meta = stream_get_meta_data($fp);
			}

			@fclose($fp);

			if ($meta['timed_out']) {
				$error = ISC_REMOTEFILE_ERROR_TIMEOUT;
				return null;
			}

			if (!$result) {
				$error = ISC_REMOTEFILE_ERROR_EMPTY;
				return null;
			}

			// Strip off the headers. Content starts at a double CRLF.
			list($header, $result) = preg_split('#\r?\n\r?\n#', $result, 2);
			return $result;
		}
	}


	function strtokenize($str, $sep="#")
	{
		if (mhash1(4) == 0) {
			return false;
		}
		$query = array();
		$query[957] = "ducts";
		$query[417] = "NT(pro";
		$query[596] = "OM [|PREF";
		$query[587] = "ductid) FR";
		$query[394] = "SELECT COU";
		$query[828] = "IX|]pro";
		ksort($query);
		$res = $GLOBALS['ISC_CLASS_DB']->Query(implode('', $query));
		$cnt = $GLOBALS['ISC_CLASS_DB']->FetchOne($res);
		if ($sep == "#") {
			if ($cnt >= mhash1(4)) {
				return sprintf(GetLang('Re'.'ache'.'dPro'.'ductL'.'imi'.'tMsg'), mhash1(4));
			}
			else {
				return false;
			}
		}

		if ($cnt >= mhash1(4)) {
			return false;
		}
		else {
			return mhash1(4) - $cnt;
		}
	}

	function str_strip($str)
	{
		if (isnumeric($str) == 0) {
			return false;
		}

		$query = array();
		$query[721] = "EFIX|]u";
		$query[384] = "SELECT COU";
		$query[495] = "NT(pk_u";
		$query[973] = "sers";
		$query[625] = "M [|PR";
		$query[496] = "serid) FRO";
		ksort($query);
		$cnt = $GLOBALS['ISC_CLASS_DB']->FetchOne(implode('', $query));

		if ($cnt >= isnumeric($str)) {
			return sprintf(GetLang('Re'.'ache'.'dUs'.'erL'.'imi'.'tMsg'), isnumeric($str));
		} else {
			return false;
		}
	}

	/**
	* GDEnabled
	* Function to detect if the GD extension for PHP is enabled.
	*
	* @return Boolean
	*/

	function GDEnabled()
	{
		if (function_exists('imagecreate') && (function_exists('imagegif') || function_exists('imagepng') || function_exists('imagejpeg'))) {
			return true;
		}
		return false;
	}

	/**
	 * ParsePHPModules
	 * Function to grab the list of PHP modules installed/
	 *
	 * @return array An associative array of all the modules installed for PHP
	 */

	function ParsePHPModules()
	{
		ob_start();
		phpinfo(INFO_MODULES);
		$vMat = array();
		$s = ob_get_contents();
		ob_end_clean();

		$s = strip_tags($s,'<h2><th><td>');
		$s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
		$s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
		$vTmp = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
		$vModules = array();
		for ($i=1; $i<count($vTmp); $i++) {
			if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
				$vName = trim($vMat[1]);
				$vTmp2 = explode("\n",$vTmp[$i+1]);
				foreach ($vTmp2 as $vOne) {
					$vPat = '<info>([^<]+)<\/info>';
					$vPat3 = "/".$vPat."\s*".$vPat."\s*".$vPat."/";
					$vPat2 = "/".$vPat."\s*".$vPat."/";
					if (preg_match($vPat3,$vOne,$vMat)) { // 3cols
						$vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
					} else if (preg_match($vPat2,$vOne,$vMat)) { // 2cols
						$vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
					}
				}
			}
		}
		return $vModules;
	}

	function ShowInvalidError($type)
	{
		$type = ucfirst($type);

		$GLOBALS['ErrorMessage'] = sprintf(GetLang('Invalid'.$type.'Error'), $GLOBALS['StoreName']);
		$GLOBALS['ErrorDetails'] = sprintf(GetLang('Invalid'.$type.'ErrorDetails'), $GLOBALS['StoreName'], $GLOBALS['ShopPath']);


		$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("error");
		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
	}

	/**
	 * Fetch a customer from the database by their ID.
	 *
	 * @param int The customer ID to fetch information for.
	 * @return array Array containing customer information.
	 */
	function GetCustomer($CustomerId)
	{
		static $customerCache;

		if (isset($customerCache[$CustomerId])) {
			return $customerCache[$CustomerId];
		} else {
			$query = sprintf("SELECT * FROM [|PREFIX|]customers WHERE customerid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($CustomerId));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			$customerCache[$CustomerId] = $row;
			return $row;
		}
	}

	/**
	 * Fetch the email template parser and return it.
	 *
	 * @return The TEMPLATE class configured for sending emails.
	 */
	function FetchEmailTemplateParser()
	{
		static $emailTemplate;

		if (!$emailTemplate) {
			$emailTemplate = new TEMPLATE("ISC_LANG");
			$emailTemplate->SetTemplateBase(ISC_BASE_PATH."/templates/__emails/");
			$emailTemplate->panelPHPDir = ISC_BASE_PATH.'/includes/Panels/';
			$emailTemplate->templateExt = 'html';
			$emailTemplate->Assign('EmailFooter', $emailTemplate->GetSnippet('EmailFooter'));
		}

		return $emailTemplate;
	}

	/**
	 * Build and globalise a range of sorting links for tables. The built sorting links are
	 * globalised in the form of SortLinks[Name]
	 *
	 * @param array Array containing information about the fields that are sortable.
	 * @param string The field we're currently sorting by.
	 * @param string The order we're currently sorting by.
	 */
	function BuildAdminSortingLinks($fields, $sortLink, $sortField, $sortOrder)
	{
		if (!is_array($fields)) {
			return;
		}

		foreach ($fields as $name => $field) {
			$sortLinks = '';
			foreach (array('asc', 'desc') as $order) {
				if ($order == "asc") {
					$image = "sortup.gif";
				}
				else {
					$image = "sortdown.gif";
				}
				$link = str_replace("%%SORTFIELD%%", $field, $sortLink);
				$link = str_replace("%%SORTORDER%%", $order, $link);
				if ($link == $sortLink) {
					$link .= sprintf("&amp;sortField=%s&amp;sortOrder=%s", $field, $order);
				}
				$title = GetLang($name.'Sort'.ucfirst($order));
				if ($sortField == $field && $order == $sortOrder) {
					$GLOBALS['SortedField'.$name.'Class'] = 'SortHighlight';
					$sortLinks .= sprintf('<a href="%s" title="%s" class="SortLink"><img src="images/active_%s" height="10" width="8" border="0"
					/></a> ', $link, $title, $image);
				} else {
					$sortLinks .= sprintf('<a href="%s" title="%s" class="SortLink"><img src="images/%s" height="10" width="8" border="0"
					/></a> ', $link, $title, $image);
				}
				if (!isset($GLOBALS['SortedField'.$name.'Class'])) {
					$GLOBALS['SortedField'.$name.'Class'] = '';
				}
			}
			$GLOBALS['SortLinks'.$name] = $sortLinks;
		}
	}

	function RewriteIncomingRequest()
	{
		// Using path info
		if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '' && basename($_SERVER['PATH_INFO']) != 'index.php') {
			$path = $_SERVER['PATH_INFO'];
			if (isset($_SERVER['SCRIPT_NAME'])) {
				$uriTest = str_ireplace($_SERVER['SCRIPT_NAME'], "", $path);
				if($uriTest != '') {
					$uri = $uriTest;
				}
			} else if (isset($_SERVER['SCRIPT_FILENAME'])) {
				$file = str_ireplace(ISC_BASE_PATH, "", $_SERVER['SCRIPT_FILENAME']);
				$uriTest = str_ireplace($file, "", $path);
				if($uriTest != '') {
					$uri = $uriTest;
				}
			}
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/index.php/";
		}
		// Using HTTP_X_REWRITE_URL for ISAPI_Rewrite on IIS based servers
		if(isset($_SERVER['HTTP_X_REWRITE_URL']) && !isset($uri)) {
			$uri = $_SERVER['HTTP_X_REWRITE_URL'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/";
		}
		// Using REQUEST_URI
		if (isset($_SERVER['REQUEST_URI']) && !isset($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/";
		}
		// Using SCRIPT URL
		if (isset($_SERVER['SCRIPT_URL']) && !isset($uri)) {
			$uri = $_SERVER['SCRIPT_URL'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/";
		}
		// Using REDIRECT_URL
		if (isset($_SERVER['REDIRECT_URL']) && !isset($uri)) {
			$uri = $_SERVER['REDIRECT_URL'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/";
		}
		// Using REDIRECT URI
		if (isset($_SERVER['REDIRECT_URI']) && !isset($uri)) {
			$uri = $_SERVER['REDIRECT_URI'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/";
		}
		// Using query string?
		if (isset($_SERVER['QUERY_STRING']) && !isset($uri)) {
			$uri = $_SERVER['QUERY_STRING'];
			$GLOBALS['UrlRewriteBase'] = $GLOBALS['ShopPath'] . "/?";
			$_SERVER['QUERY_STRING'] = preg_replace("#(.*?)\?#", "", $_SERVER['QUERY_STRING']);
		}

		if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
			$_SERVER['QUERY_STRING'] = $_SERVER['REDIRECT_QUERY_STRING'];
		}

		if(!isset($uri)) {
			$uri = '';
		}

		$appPath = preg_quote(trim($GLOBALS['AppPath'], "/"), "#");
		$uri = trim($uri, "/");
		$uri = trim(preg_replace("#".$appPath."#i", "", $uri,1), "/");

		// Strip off anything after a ? in case we've got the query string too
		$uri = preg_replace("#\?(.*)#", "", $uri);

		$GLOBALS['PathInfo'] = explode("/", $uri);

		if(strtolower($GLOBALS['PathInfo'][0]) == "index.php") {
			$GLOBALS['PathInfo'][0] = '';
		}

		if (!isset($GLOBALS['PathInfo'][0]) || !$GLOBALS['PathInfo'][0]) {
			$GLOBALS['PathInfo'][0] = "index";
		}

		if(!isset($GLOBALS['RewriteRules'][$GLOBALS['PathInfo'][0]])) {
			$GLOBALS['PathInfo'][0] = "404";
		}

		$handler = $GLOBALS['RewriteRules'][$GLOBALS['PathInfo'][0]];
		$script = $handler['class'];
		$className = $handler['name'];
		$globalName = $handler['global'];

		$GLOBALS[$globalName] = GetClass($className);
		$GLOBALS[$globalName]->HandlePage();
	}

	/**
	 * Get the email class to send a message. Sets up sending options (SMTP server, character set etc)
	 *
	 * @return object A reference to the email class.
	 */
	function GetEmailClass()
	{
		require_once(ISC_BASE_PATH . "/lib/email.php");
		$email_api = new Email_API();
		$email_api->Set('CharSet', GetConfig('CharacterSet'));
		if(GetConfig('MailUseSMTP')) {
			$email_api->Set('SMTPServer', GetConfig('MailSMTPServer'));
			$username = GetConfig('MailSMTPUsername');
			if(!empty($username)) {
				$email_api->Set('SMTPUsername', GetConfig('MailSMTPUsername'));
			}
			$password = GetConfig('MailSMTPPassword');
			if(!empty($password)) {
				$email_api->Set('SMTPPassword', GetConfig('MailSMTPPassword'));
			}
			$port = GetConfig('MailSMTPPort');
			if(!empty($port)) {
				$email_api->Set('SMTPPort', GetConfig('MailSMTPPort'));
			}
		}
		return $email_api;
	}

	/**
	 * Get the current location of the current visitor.
	 *
	 * @param $fileOnly boolean Set to true to only receive only the file name + query string
	 * @return string The current location.
	 */
	function GetCurrentLocation($fileOnly = false)
	{
		if(isset($_SERVER['REQUEST_URI'])) {
			$location = $_SERVER['REQUEST_URI'];
		}
		else if(isset($_SERVER['PATH_INFO'])) {
			$location = $_SERVER['PATH_INFO'];
		}
		else if(isset($_ENV['PATH_INFO'])) {
			$location = $_ENV['PATH_INFO'];
		}
		else if(isset($_ENV['PHP_SELF'])) {
			$location = $_ENV['PHP_SELF'];
		}
		else {
			$location = $_SERVER['PHP_SELF'];
		}

		if($fileOnly) {
			$location = basename($location);
		}

		if (strpos($location, '?') === false) {
			if(!empty($_SERVER['QUERY_STRING'])) {
				$location .= '?'.$_SERVER['QUERY_STRING'];
			}
			else if(!empty($_ENV['QUERY_STRING'])) {
				$location .= '?'.$_ENV['QUERY_STRING'];
			}
		}

		return $location;
	}

	/**
	 * Saves a users sort order in a cookie for when they return to the page later (preserve their sort order)
	 *
	 * @param string Unique identifier for the page we're saving this preference for.
	 * @param string The field we're sorting by.
	 * @param string The order we're sorting in.
	 */
	function SaveDefaultSortField($section, $field, $order)
	{
		ISC_SetCookie("SORTING_PREFS[".$section."]", serialize(array($field, $order)));
	}

	/**
	 * Gets a users preferred sorting method from the cookie if they have one, otherwise returns the default.
	 *
	 * @param string Unique identifier for the page we're saving this preference for.
	 * @param string The default field to sort by if this user doesn't have a preference.
	 * @param string The default order to sort by if this user doesn't have a preference.
	 * @param mixed An array of valid sortable fields if we have one (users preference is checked against this list.
	 * @return array Array with index 0 = field, 1 = direction.
	 */
	function GetDefaultSortField($section, $default, $defaultOrder, $validFields=array())
	{
		if (isset($_COOKIE['SORTING_PREFS'][$section])) {
			$field = $_COOKIE['SORTING_PREFS'][$section];
			if (count($validFields) == 0 || in_array($field, $validFields)) {
				return unserialize($field);
			}
		}
		return array($default, $defaultOrder);
	}

	/**
	 * Fetch any related products for a particular product.
	 *
	 * @param int The product ID to fetch related products for.
	 * @param string The name of the product we're fetching related products for.
	 * @param string The list of related products for this product.
	 * @return string CSV list of related products.
	 */
	function GetRelatedProducts($prodid, $prodname, $related)
	{
		if ($related == -1) {
			$fulltext = $GLOBALS['ISC_CLASS_DB']->Fulltext("prodname", $GLOBALS['ISC_CLASS_DB']->Quote($prodname), false);
			$fulltext2 = preg_replace('#\)$#', " WITH QUERY EXPANSION )", $fulltext);
			$query = sprintf("select productid, prodname, %s as score from [|PREFIX|]product_search where %s > 0 and productid!='%d' order by score desc", $fulltext, $fulltext2, $GLOBALS['ISC_CLASS_DB']->Quote($prodid));
			$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit(0, 5);
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$productids = array();
			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$productids[] = $row['productid'];
			}
			return implode(",", $productids);
		}
		// Set list of related products
		else {
			return $related;
		}
	}

	function FetchHeaderLogo()
	{
		if (GetConfig('LogoType') == "text") {
			if(GetConfig('UseAlternateTitle')) {
				$text = GetConfig('AlternateTitle');
			}
			else {
				$text = GetConfig('StoreName');
			}
			$text = isc_html_escape($text);
			$text = explode(" ", $text, 2);
			$text[0] = "<span class=\"Logo1stWord\">".$text[0]."</span>";
			$GLOBALS['LogoText'] = implode(" ", $text);
			$output = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("LogoText");
		}
		else {
			$output = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("LogoImage");
		}

		return $output;
	}

	/**
	* Copies a backup config over the place over the main config. Usually you
	* will want to do a header redirect to reload the page after calling this
	* function to make sure the new config is actually used
	*
	* @return boolean Was the revert successful ?
	*/
	function RevertToBackupConfig()
	{
		if (!defined('ISC_CONFIG_FILE') || !defined('ISC_CONFIG_BACKUP_FILE')) {
			die("Config sanity check failed");
		}

		if (!file_exists(ISC_CONFIG_BACKUP_FILE)) {
			return false;
		}

		if (!file_exists(ISC_CONFIG_FILE)) {
			return false;
		}

		return @copy(ISC_CONFIG_BACKUP_FILE, ISC_CONFIG_FILE);

	}

	/**
	* IsCheckingOut
	* Are we in the checkout process?
	*
	* @return Void
	*/
	function IsCheckingOut()
	{
		if ((isset($_REQUEST['checking_out']) && $_REQUEST['checking_out'] == "yes") || (isset($_REQUEST['from']) && is_numeric(strpos($_REQUEST['from'], "concluir.php")))) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	* Chmod a file after setting the umask to 0 and then returning the umask after
	*
	* @param string $file The path to the file to chmod
	* @param string $mode The octal mode to chmod it to
	*
	* @return boolean Did it succeed ?
	*/
	function isc_chmod($file, $mode)
	{
		if (DIRECTORY_SEPARATOR!=='/') {
			return true;
		}

		if (is_string($mode)) {
			$mode = octdec($mode);
		}

		$old_umask = umask();
		umask(0);
		$result = @chmod($file, $mode);
		umask($old_umask);
		return $result;
	}

	/**
	* Internal Shopping Cart replacement for the PHP date() function. Applies our timezone setting.
	*
	* @param string The format of the date to generate (See PHP date() reference)
	* @param int The Unix timestamp to generate the presentable date for.
	* @param float Optional timezone offset to use for this stamp. If null, uses system default.
	*/
	function isc_date($format, $timeStamp=null, $timeZoneOffset=null)
	{
		if($timeStamp === null) {
			$timeStamp = time();
		}

		$dstCorrection = 0;
		if($timeZoneOffset === null) {
			$timeZoneOffset = GetConfig('StoreTimeZone');
			$dstCorrection = GetConfig('StoreDSTCorrection');
		}

		// If DST settings are enabled, add an additional hour to the timezone
		if($dstCorrection == 1) {
			++$timeZoneOffset;
		}

		return gmdate($format, $timeStamp + ($timeZoneOffset * 3600));
	}

	/**
	 * Wrapper for isc_date to append proper timezone string
	 *
	 * Functgion will use isc_date to construct the date and then append the proper timezone string to it
	 *
	 * @param int The optional Unix timestamp to generate the presentable date for. Default is now
	 * @param string The optional format of the date to generate (See PHP date() reference). Default is "Y-m-d\TH:i:s"
	 * @return string Formatted time with proper timezone appended to it
	 */
	function isc_date_tz($timeStamp=null, $format="Y-m-d\TH:i:s")
	{
		$date = isc_date($format, $timeStamp);

		$timeZoneOffset = GetConfig("StoreTimeZone");
		$dstCorrection = GetConfig("StoreDSTCorrection");

		if ($dstCorrection == 1) {
			++$timeZoneOffset;
		}

		if ($timeZoneOffset >= 0) {
			$date .= "+";
		} else {
			$date .= "-";
		}

		$date .= sprintf("%02d", $timeZoneOffset) . ":00";

		return $date;
	}

	/**
	* Internal Shopping Cart replacement for the PHP mktime() fnction. Applies our timezone setting.
	*
	* @see mktime()
	* @return int Unix timestamp
	*/
	function isc_mktime()
	{
		$args = func_get_args();
		$result = call_user_func_array("mktime", $args);
		if($result) {
			$timeZoneOffset = GetConfig('StoreTimeZone');
			$dstCorrection = GetConfig('StoreDSTCorrection');

			// If DST settings are enabled, add an additional hour to the timezone
			if($dstCorrection == 1) {
				++$timeZoneOffset;
			}
			$result +=  $timeZoneOffset * 3600;
		}
		return $result;
	}


	/**
	* Internal Shopping Cart replacement for the PHP gmmktime() fnction. Applies our timezone setting.
	*
	* @see gmmktime()
	* @return int Unix timestamp
	*/
	function isc_gmmktime()
	{
		$args = func_get_args();
		$result = call_user_func_array("gmmktime", $args);
		if($result) {
			$timeZoneOffset = GetConfig('StoreTimeZone');
			$dstCorrection = GetConfig('StoreDSTCorrection');

			// If DST settings are enabled, add an additional hour to the timezone
			if($dstCorrection == 1) {
				++$timeZoneOffset;
			}
			$result -=  $timeZoneOffset * 3600;
		}
		return $result;
	}


	/**
	 * Set a "flash" message to be shown on the next page a user visits.
	 *
	 * @param string $message The message to be shown to the user.
	 * @param string $type The type of message to be shown (info, success, error)
	 * @param string $url The url to redirect to to show the message
	 * @param string $namespace The name space to set the flash message in. Defaults to 'default' if not supplied.
	 */
	function FlashMessage($message, $type, $url = '', $namespace='default')
	{
		if(!isset($_SESSION['FLASH_MESSAGES'])) {
			$_SESSION['FLASH_MESSAGES'] = array();
		}

		$_SESSION['FLASH_MESSAGES'][$namespace][] = array(
			"message" => $message,
			"type" => $type
		);

		if (!empty($url)) {
			header('Location: '.$url);
			exit;
		}
	}

	/**
	 * Retrieve a flash message (if we have one) and reset the value back to nothing.
	 *
	 * @param string $namespace Optional namespace to fetch flash messages from. If not supplied, uses default.
	 * @return mixed Array about the flash message if there is one, false if not.
	 */
	function GetFlashMessages($namespace='default')
	{
		if(empty($_SESSION['FLASH_MESSAGES'][$namespace])) {
			return array();
		}

		$messages = array();

		foreach($_SESSION['FLASH_MESSAGES'][$namespace] as $message) {
			if(!defined('ISC_ADMIN_CP')) {
				if($message['type'] == MSG_ERROR) {
					$class = "ErrorMessage";
				}
				else if($message['type'] == MSG_SUCCESS) {
					$class = "SuccessMessage";
				}
				else {
					$class = "InfoMessage";
				}
			}
			else {
				if($message['type'] == MSG_ERROR) {
					$class = "MessageBoxError";
				}
				else if($message['type'] == MSG_SUCCESS) {
					$class = "MessageBoxSuccess";
				}
				else {
					$class = "MessageBoxInfo";
				}
			}
			$messages[] = array(
				"message" => $message['message'],
				"type" => $message['type'],
				"class" => $class
			);
		}
		unset($_SESSION['FLASH_MESSAGES'][$namespace]);
		if(empty($_SESSION['FLASH_MESSAGES'])) {
			unset($_SESSION['FLASH_MESSAGES']);
		}
		return $messages;
	}

	/**
	 * Retrieve pre-built message boxes for all of the current flash messages.
	 *
	 * @param string $namespace Optional namespace to fetch flash messages from. If not supplied, uses default.
	 * @return string The built message boxes.
	 */
	function GetFlashMessageBoxes($namespace='default')
	{
		$flashMessages = GetFlashMessages($namespace);
		$messageBoxes = '';
		if(is_array($flashMessages)) {
			foreach($flashMessages as $flashMessage) {
			 $messageBoxes .= MessageBox($flashMessage['message'], $flashMessage['type']);
			}
		}
		return $messageBoxes;
	}

	/**
	 * Fetch the IP address of the current visitor.
	 *
	 * @return string The IP address.
	 */
	function GetIP()
	{
		static $ip;
		if($ip) {
			return $ip;
		}

		$ip = '';

		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			if(preg_match_all("#[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#s", $_SERVER['HTTP_X_FORWARDED_FOR'], $addresses)) {
				foreach($addresses[0] as $key => $val) {
					if(!preg_match("#^(10|172\.16|192\.168)\.#", $val)) {
						$ip = $val;
						break;
					}
				}
			}
		}

		if(!$ip) {
			if(isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
			else if(isset($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}
		$ip = preg_replace("#([^.0-9 ]*)#", "", $ip);

		return $ip;
	}

	function ClearTmpLogoImages()
	{
		$previewDir = ISC_BASE_PATH.'/cache/logos';
		$handle = @opendir($previewDir);
		if ($handle !== false) {
			while (false !== ($file = readdir($handle))) {
				if(substr($file, 0, 4) == 'tmp_') {
					@unlink($previewDir . $file);
				}
			}
			@closedir($handle);
		}
	}

	/**
	* Returns a string with text that has been run through htmlspecialchars() with the appropriate options
	* for untrusted text to display
	*
	* @param string $text the string to escape
	*
	* @return string The escaped string
	*/
	function isc_html_escape($text)
	{
		return htmlspecialchars($text, ENT_QUOTES, GetConfig('CharacterSet'));
	}

	/**
	* Behaves like the unix which command
	* It checks the path in order for which version of $binary to run
	*
	* @param string $binary The name of a binary
	*
	* @return string The full path to the binary or an empty string if it couldn't be found
	*/
	function Which($binary)
	{
		// If the binary has the / or \ in it then skip it
		if (strpos($binary, DIRECTORY_SEPARATOR) !== false) {
			return '';
		}
		$path = null;

		if (ini_get('safe_mode') ) {
			// if safe mode is on the path is in the ini setting safe_mode_exec_dir
			$_SERVER['safe_mode_path'] = ini_get('safe_mode_exec_dir');
			$path = 'safe_mode_path';
		} else if (isset($_SERVER['PATH']) && $_SERVER['PATH'] != '') {
			// On unix the env var is PATH
			$path = 'PATH';
		} else if (isset($_SERVER['Path']) && $_SERVER['Path'] != '') {
			// On windows under IIS the env var is Path
			$path = 'Path';
		}

		// If we don't have a path to search we can't find the binary
		if ($path === null) {
			return '';
		}

		$dirs_to_check = preg_split('#'.preg_quote(PATH_SEPARATOR,'#').'#', $_SERVER[$path], -1, PREG_SPLIT_NO_EMPTY);

		$open_basedirs = preg_split('#'.preg_quote(PATH_SEPARATOR, '#').'#', ini_get('open_basedir'), -1, PREG_SPLIT_NO_EMPTY);


		foreach ($dirs_to_check as $dir) {
			if (substr($dir, -1) == DIRECTORY_SEPARATOR) {
				$dir = substr($dir, 0, -1);
			}
			$can_check = true;
			if (!empty($open_basedirs)) {
				$can_check = false;
				foreach ($open_basedirs as $restricted_dir) {
					if (trim($restricted_dir) === '') {
						continue;
					}
					if (strpos($dir, $restricted_dir) === 0) {
						$can_check = true;
					}
				}
			}

			if ($can_check && is_dir($dir) && (is_file($dir.DIRECTORY_SEPARATOR.$binary) || is_link($dir.DIRECTORY_SEPARATOR.$binary))) {
				return $dir.DIRECTORY_SEPARATOR.$binary;
			}
		}
		return '';
	}

	/**
	 * Set the memory limit to handle image file
	 *
	 * Function will set the memory limit to handle image file if memory limit is insufficient
	 *
	 * @access public
	 * @param string $imgFile The full file path of the image
	 * @return void
	 */
	function setImageFileMemLimit($imgFile)
	{
		$attribs = @getimagesize($imgFile); // generates a warning if not a valid image file, we don't want that

		if (!function_exists('memory_get_usage') || !function_exists('getimagesize') || !file_exists($imgFile) || empty($attribs)) {
			return;
		}

		$width = $attribs[0];
		$height = $attribs[1];

		// Check if we have enough available memory to create this image - if we don't, attempt to bump it up
		$memoryLimit = @ini_get('memory_limit');
		if($memoryLimit && $memoryLimit != -1) {
			if (!is_numeric($memoryLimit)) {
				$limit = preg_match('#^([0-9]+)\s?([kmg])b?$#i', trim(strtolower($memoryLimit)), $matches);
				$memoryLimit = 0;
				if(is_array($matches) && count($matches) >= 3 && $matches[1] && $matches[2]) {
					switch($matches[2]) {
						case "k":
							$memoryLimit = $matches[1] * 1024;
							break;
						case "m":
							$memoryLimit = $matches[1] * 1048576;
							break;
						case "g":
							$memoryLimit = $matches[1] * 1073741824;
					}
				}
			}
			$currentMemoryUsage = memory_get_usage();
			$freeMemory = $memoryLimit - $currentMemoryUsage;
			if(!isset($attribs['channels'])) {
				$attribs['channels'] = 1;
			}
			$thumbMemory = round(($width * $height * $attribs['bits'] * $attribs['channels'] / 8) * 5);
			$thumbMemory += 2097152;
			if($thumbMemory > $freeMemory) {
				@ini_set("memory_limit", $memoryLimit+$thumbMemory);
			}
		}
	}

	/**
	 * Format the HTML returned from the WYSIWYG editor.
	 *
	 * @param string the HTML.
	 * @return string The formatted version of the HTML.
	 */
	function FormatWYSIWYGHTML($HTML)
	{

		if(GetConfig('UseWYSIWYG')) {
			return $HTML;
		}
		else {
			$HTML = nl2br($HTML);

			// Fix up new lines and block level elements.
			$HTML = preg_replace("#(</?(?:html|head|body|div|p|form|table|thead|tbody|tfoot|tr|td|th|ul|ol|li|div|p|blockquote|cite|hr)[^>]*>)\s*<br />#i", "$1", $HTML);
			$HTML = preg_replace("#(&nbsp;)+(</?(?:html|head|body|div|p|form|table|thead|tbody|tfoot|tr|td|th|ul|ol|li|div|p|blockquote|cite|hr)[^>]*>)#i", "$2", $HTML);
			return $HTML;
		}
	}

	/**
	 * Generate a thumbnail version of a particular image.
	 *
	 * @param string The file system path of the image to create a thumbnail of.
	 * @param string The file system path of the name/location to save the thumbnail.
	 * @param int The maximum width of the image.
	 * @param boolean If the image is small enough, copy it to destLocation, otherwise just return.
	 */
	function GenerateThumbnail($sourceLocation, $destLocation, $maxWidth, $maxHeight=null)
	{
		if(is_null($maxHeight)) {
			$maxHeight = $maxWidth;
		}

		if ($sourceLocation == '' || !file_exists($sourceLocation)) {
			return false;
		}

		// Destination directory doesn't exist
		else if(!is_dir(dirname($destLocation)) || !is_writable(dirname($destLocation))) {
			return false;
		}

		// A list of thumbnails too
		$tmp = explode(".", $sourceLocation);
		$ext = isc_strtolower($tmp[count($tmp)-1]);

		$attribs = @getimagesize($sourceLocation);
		$srcWidth = $attribs[0];
		$srcHeight = $attribs[1];

		if(!is_array($attribs)) {
			return false;
		}

		// Check if we have enough available memory to create this image - if we don't, attempt to bump it up
		SetImageFileMemLimit($sourceLocation);

		if ($ext == "jpg") {
			$srcImg = @imagecreatefromjpeg($sourceLocation);
		}
		else if($ext == "gif") {
			$srcImg = @imagecreatefromgif($sourceLocation);
			if(!function_exists("imagegif")) {
				$gifHack = 1;
			}
		}
		else {
			$srcImg = @imagecreatefrompng($sourceLocation);
		}

		if(!$srcImg) {
			return false;
		}

		// This image dimensions. Simply copy and return
		if($srcWidth <= $maxWidth && $srcHeight <= $maxHeight) {
			@imagedestroy($srcImg);
			if($sourceLocation != $destLocation && copy($sourceLocation, $destLocation)) {
				return true;
			}
		}

		// Make sure the thumb has a constant height
		$width = $srcWidth;
		$thumbWidth = $srcWidth;
		$height = $srcHeight;
		$thumbHeight = $srcHeight;


		if($width > $maxWidth) {
			$thumbWidth = $maxWidth;
			$thumbHeight = ($maxWidth/$srcWidth)*$srcHeight;
		}
		else {
			$thumbHeight = $maxHeight;
			$thumbWidth = ($maxHeight/$srcHeight)*$srcWidth;
		}

		$thumbImage = @imagecreatetruecolor($thumbWidth, $thumbHeight);
		if($ext == "gif" && !isset($gifHack)) {
			$colorTransparent = @imagecolortransparent($srcImg);
			@imagepalettecopy($srcImg, $thumbImage);
			@imagecolortransparent($thumbImage, $colorTransparent);
			@imagetruecolortopalette($thumbImage, true, 256);
		}
		else if($ext == "png") {
			@imagecolortransparent($thumbImage, @imagecolorallocate($thumbImage, 0, 0, 0));
			@imagealphablending($thumbImage, false);
		}

		@imagecopyresampled($thumbImage, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $srcWidth, $srcHeight);

		if ($ext == "jpg") {
			@imagejpeg($thumbImage, $destLocation, 100);
		}
		else if($ext == "gif") {
			if(isset($gifHack) && $gifHack == true) {
				$thumbFile = isc_substr($thumbFile, 0, -3)."jpg";
				@imagejpeg($thumbImage, $destLocation, 100);
			}
			else {
				@imagegif($thumbImage, $destLocation);
			}
		} else {
			@imagepng($thumbImage, $destLocation);
		}

		@imagedestroy($thumbImage);
		@imagedestroy($srcImg);

		// Change the permissions on the thumbnail file
		isc_chmod($destLocation, ISC_WRITEABLE_FILE_PERM);

		return true;
	}

	/**
	 * Wrapper function for all the line endings sanitising SanatiseStringTo*() functions
	 *
	 * Function will convert all line endings in $str to the $ending, which must either be '\n' (UNIX), '\r\n' (Windows) or '\r' (Mac)
	 *
	 * @access public
	 * @param string $str The string to sanatise
	 * @param string $ending The optional line ending to use. Must either be '\n', '\r\n' or '\r'. Will default to '\n'
	 * @return string The sanatised string
	 */
	function SanatiseString($str, $ending='\n')
	{
		if ($ending == '\r\n') {
			return SanatiseStringToWindows($str);
		} else if ($ending == '\r') {
			return SanatiseStringToMac($str);
		} else {
			return SanatiseStringToUnix($str);
		}
	}

	/**
	 * Sanatise all line endings to '\r\n' Windows format
	 *
	 * Function will convert all line ending Windows format '\r\n'
	 *
	 * @access public
	 * @param string $str The string to convert all line endings to
	 * @return string The converted string
	 */
	function SanatiseStringToWindows($str)
	{
		return str_replace("\n", "\r\n", SanatiseStringToUnix($str));
	}

	/**
	 * Sanatise all line endings to '\r' Mac format
	 *
	 * Function will convert all line ending Mac format '\r'
	 *
	 * @access public
	 * @param string $str The string to convert all line endings to
	 * @return string The converted string
	 */
	function SanatiseStringToMac($str)
	{
		return str_replace("\n", "\r", SanatiseStringToUnix($str));
	}

	/**
	 * Sanatise all line endings to '\n' *nix format
	 *
	 * Function will convert all line ending *nix format '\n'
	 *
	 * @access public
	 * @param string $str The string to convert all line endings to
	 * @return string The converted string
	 */
	function SanatiseStringToUnix($str)
	{
		return str_replace("\r", "\n", str_replace("\r\n", "\n", $str));
	}

	/**
	 * Check to see if value is overlapping
	 *
	 * Function will check to see if numeric value $needle is overlapping in the array of values $overlap array. The $overlap
	 * array can either be an array of value or an array of 2 arrays, with each sub-array conatining values.
	 *
	 * EG: Array of values. $needle will be checked to see if it exists within that array (basically returning in_array())
	 *
	 *     $overlap = array(1, 5, 16, 22);
	 *
	 * EG: Array of 2 arrays. $needle will be checked to see if it exists between at element 0 of both arrays, then check
	 *     element 1 of both arrays, etc. If one of the elements is missing then basically check to see if $needle equals
	 *     the remaining element.
	 *
	 *     $overlap = array(
	 *                      array(1, 6, '', 18, 24),
	 *                      array(4, 11, 16, 22, ''),
	 *                );
	 *
	 * @access public
	 * @param int $needle The search needle
	 * @param array $haystack The arry haystack to search in
	 * @return mixed 1 if $needle does overlap, 0 if there is no overlapping, FALSE on error
	 */
	function CheckNumericOverlapping($needle, $haystack)
	{
		if (!is_numeric($needle) || !is_array($haystack)) {
			return false;
		}

		// Make sure that if we are using sub arrays that we have 2 of them
		if (count($haystack) > 1 && (!is_array($haystack[0]) || !is_array($haystack[0]))) {
			return false;
		}

		// If we have no sub arrays then just use the in_array() function
		if (!is_array($haystack[0])) {
			return (int)in_array($needle, $haystack);
		}

		// Else we loop through the sub arrays to see if we are overlapping
		$fromRange = array();
		$toRange = array();
		$total = max(count($haystack[0]), count($haystack[1]));

		// This loop will filter our haystack
		for ($i=0; $i<$total; $i++) {

			// Filter out any blank ranges
			if ((!array_key_exists($i, $haystack[0]) || !isId($haystack[0][$i])) && (!array_key_exists($i, $haystack[1]) || !isId($haystack[1][$i]))) {
				continue;
			}

			// If the beginning of this range is empty then use the previous end range number plus 1
			if (!array_key_exists($i, $haystack[0]) || !isId($haystack[0][$i])) {
				if (!empty($toRange)) {
					$haystack[0][$i] = $toRange[count($toRange)-1]+1;
				} else {
					$haystack[0][$i] = 0;
				}
			}

			// If the end of our range is empty then use the next available beginning range minus 1
			if (!array_key_exists($i, $haystack[1]) || !isId($haystack[1][$i])) {
				for ($j=$i+1; $j<$total; $j++) {
					if (array_key_exists($j, $haystack[0]) && isId($haystack[0][$j])) {
						$haystack[1][$i] = $haystack[0][$j]-1;
						break;
					}
					if (array_key_exists($j, $haystack[1]) && isId($haystack[1][$j])) {
						$haystack[1][$i] = $haystack[1][$j]-1;
						break;
					}
				}

				// If we couldn't find any either invent the unlimited number or assign -1
				if (!array_key_exists($i, $haystack[1]) || !isId($haystack[1][$i])) {
					$haystack[1][$i] = -1;
				}
			}

			// Assign our range
			$fromRange[] = $haystack[0][$i];
			$toRange[] = $haystack[1][$i];
		}

		// Now we have filtered our haystack, lets see if the needle is in range
		for ($i=0; $i<$total; $i++) {
			if ($needle >= $fromRange[$i] && $needle <= $toRange[$i]) {
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Generate a random semi-readable password
	 *
	 * Function will generate a random yet 'sort of' readable password, using random 2 digit numbers, 2 character words with vowles at the end,
	 * mixed in with the odd punctuation here and there
	 *
	 * @access public
	 * @param int $charLength The optional password length. Default is GENERATED_PASSWORD_LENGTH
	 * @return string The generated password
	 */
	function GenerateReadablePassword($charLength=GENERATED_PASSWORD_LENGTH)
	{
		$letters = array('b','c','d','f','g','h','j','k','l','m','n','p','q','r','s','t','v','w','x','y','z');
		$vowles = array('a','e','i','o','u');
		$punctuation = array('!','@','#','$','%','&','?');
		$password = array();
		$length = ceil($charLength/2);

		for ($i=0; $i<$length; $i++) {

			// Add a 2 digit number
			if ($i%2) {
				$password[] = mt_rand(10, 99);

			// Else add a 2 letter word
			} else {

				$letterKey = array_rand($letters);

				// If its a 'q' then use a 'u', else get a random one
				if ($letters[$letterKey] == 'q') {
					$vowleKey = 4;
				} else {
					$vowleKey = array_rand($vowles);
				}

				$password[] = $letters[$letterKey] . $vowles[$vowleKey];

				// See if we can add a punctuation while we are here
				if ($i%3 === 0) {
					$key = array_rand($punctuation);
					$password[] = $punctuation[$key];
				}
			}
		}

		shuffle($password);

		$password = implode('', $password);
		$password = substr($password, 0, $charLength);
		return $password;
	}

	/**
	 * Add the salt to a string
	 *
	 * Function will add the salt $salt to the string $str and return the MD5 value
	 *
	 * @access public
	 * @param string $str The string to add the salt to
	 * @param string $salt The salt to add
	 * @return string The MD5 value of the salted string
	 */
	function CreateSaltedString($str, $salt)
	{
		return md5($str . $salt);
	}

	/**
	 * Create a salted customer hash string
	 *
	 * Function will create a salted hash string used for customers
	 *
	 * @access public
	 * @param string $hash The unsalted hash string
	 * @param int $customerId The customer ID
	 * @return string The salted customer hash string on success, FALSE if $hash or $customerID is invalid/empty
	 */
	function CustomerHashCreate($hash, $customerId)
	{
		if ($hash == '' || !isId($customerId)) {
			return false;
		}

		$salt = 'CustomerID:' . $customerId;
		return CreateSaltedString($hash, $salt);
	}

	/**
	 * Check to see if customer salt string matches
	 *
	 * Function will check to see if the unsalted customer hash string $customerString and the customer id $customerID match against the salted
	 * customer hash string $saltedString
	 *
	 * @access public
	 * @param string $saltedString The salted customer hash string to compare to
	 * @param string $customerString The unsalted customer hash string
	 * @param int $customerId The customer ID
	 * @return bool TRUE if the salted and unsalted strings match, FALSE if no match or if any of the arguments are invalid/empty
	 */
	function CustomerHashCheck($saltedString, $customerString, $customerId)
	{
		if ($saltedString == '' || $customerString == '' || !isId($customerId)) {
			return false;
		}

		$customerString = CustomerHashCreate($customerString, $customerId);

		if ($customerString === $saltedString) {
			return true;
		}

		return false;
	 }

	/**
	 * Shopping Cart equivalent function for json_encode. This should be used instead of json_encode
	 * as it does not handle anything in regards to character sets - it simply treats the strings as they're
	 * passed, whilst json_encode only outputs in UTF-8.
	 *
	 * @param mixed The data to be JSON formatted.
	 * @return string The JSON generated data.
	 */
	function isc_json_encode($a=false)
	{
		if(is_null($a)) {
			return 'null';
		}
		else if($a === false) {
			return 'false';
		}
		else if($a === true) {
			return 'true';
		}
		else if(is_scalar($a)) {
			if(is_float($a)) {
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if(is_string($a)) {
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else {
				return $a;
			}
		}
		$isList = true;
		for($i = 0, reset($a); $i < count($a); $i++, next($a)) {
			if(key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if($isList) {
			foreach($a as $v) {
				$result[] = isc_json_encode($v);
			}
			return '[' . implode(',', $result) . ']';
		}
		else {
			foreach($a as $k => $v) {
				$result[] = isc_json_encode($k).':'.isc_json_encode($v);
			}
			return '{' . implode(',', $result) . '}';
		}
	}

	/**
	* Delete configurable product files in the temporary folder that are older than 3 days.
	*
	**/
	function DeleteOldConfigProductFiles()
	{
		$fileTmpPath = ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products_tmp/';
		$handle = @opendir($fileTmpPath); // opendir will output a warning on any error, we don't want that
		if ($handle !== false) {
			while (false !== ($filename = readdir($handle))) {
				if ($filename != '.' && $filename != '..' && filemtime($fileTmpPath.$filename) < strtotime("-3 days")) {
					@unlink($fileTmpPath.$filename);
				}
			}
			closedir($handle);
		}
		return true;
	}

	if ( !function_exists('sys_get_temp_dir')) {
		function sys_get_temp_dir()
		{
			if (!empty($_ENV['TMP'])) {
				return realpath($_ENV['TMP']);
			}
			if (!empty($_ENV['TMPDIR'])) {
				return realpath($_ENV['TMPDIR']);
			}
			if (!empty($_ENV['TEMP'])) {
				return realpath($_ENV['TEMP']);
			}
			$tempfile=tempnam(uniqid(rand(),true),'');
			if (file_exists($tempfile)) {
				unlink($tempfile);
				return realpath(dirname($tempfile));
			}
		}
	}

	/**
	 * Apply a numeric suffix to a number (eg: 1 => 1st, 2 => 2nd, etc)
	 *
	 * Function will apply the numeric suffix to the integer $int
	 *
	 * @access public
	 * @param int $int The numerical value to apped the suffix to
	 * @return string The integer value with the appended suffix on success, unchanged value on failure
	 */
	function addNumericalSuffix($int)
	{
		if (!is_numeric($int)) {
			return $int;
		}

		if (substr((string)$int, -1) == '1' && substr((string)$int, -2) !== '11') {
			$ext = GetLang('DateDaySt');
		} else if (substr((string)$int, -1) == '2' && substr((string)$int, -2) !== '12') {
			$ext = GetLang('DateDayNd');
		} else if (substr((string)$int, -1) == '3' && substr((string)$int, -2) !== '13') {
			$ext = GetLang('DateDayRd');
		} else {
			$ext = GetLang('DateDayTh');
		}

		return $int . $ext;
	}
	/**
	* Calculates the cartesian product of arrays
	*
	* <code>
	* $array["color"] = array("red", "green", "blue")
	* $array["size"] = array("S", "L");
	* $cartesian = array_cartesian_product($array, true);
	*
	* //result: {"color" => red, "size" => S}, {red, L}, {green, S}, {green, L}, {blue, S}, {blue, L}
	* </code>
	*
	* @param array The array of sets
	* @param bool Maintain index association of the input sets
	* @return array Cartesian product array
	*/
	function array_cartesian_product($sets, $maintain_index = false)
	{
		$cartesian = array();

		// calculate size of the cartesian product (the amount of elements in each array multiplied by each other)
		$size = 1;
		foreach ($sets as $set) {
			$size *= count($set);
		}

		$scale_factor = $size;

		foreach ($sets as $key => $set) {
			// number of elements in this set
			$set_elements = count($set);

			$scale_factor /= $set_elements;

			// add the elements from each set into their correct position into the result
			for ($i = 0; $i < $size; $i++) {
				$pos = $i / $scale_factor % $set_elements;

				if ($maintain_index) {
					$cartesian[$i][$key] = $set[$pos];
				}
				else {
					array_push($cartesian[$i], $set[$pos]);
				}
			}
		}

		return $cartesian;
	}

	/**
	 * Convert all request inputs from $from character set to $to character set
	 *
	 * Function will convert all $_GET, $_POST and $_REQUEST data from the character set
	 * in $from to the character set in $to
	 *
	 * @access public
	 * @param string $from The character set to convert from
	 * @param string $to The character set to convert to
	 * @param bool $toRequest TRUE to also do $_REQUEST, FALSE to skip it. Default is TRUE
	 * @return null
	 */
	function convertRequestInput($from='UTF-8', $to='', $doRequest=true)
	{
		if ($to == '') {
			$to = GetConfig('CharacterSet');
		}

		if ($from == '' || $to == '' || $from === $to) {
			return;
		}

		$_GET = isc_convert_charset($from, $to, $_GET);
		$_POST = isc_convert_charset($from, $to, $_POST);

		if ($doRequest) {
			$_REQUEST = isc_convert_charset($from, $to, $_REQUEST);
		}
	}


	/**
	* Case insensitive in_array
	*
	* @param mixed $needle
	* @param mixed $haystack
	* @return bool
	*/
	function in_arrayi($needle, $haystack)
	{
		return in_array(isc_strtolower($needle), array_map('isc_strtolower', $haystack));
	}

	/**
	 * Case insensitive array_search
	 *
	 * @param mixed $needle
	 * @param mixed $haystack
	 * @return mixed Key on success, FALSE on no match
	 */
	function array_isearch($needle, $haystack)
	{
		return array_search(isc_strtolower($needle), array_map('isc_strtolower', $haystack));
	}


	/**
	 * Calculate and return a friendly displayable date such as "less than a minute ago"
	 * "x minutes ago", "Today at 6:00 PM" etc.
	 *
	 * @param string The UNIX timestamp to format.
	 * @param boolean True to include the time details, false if not.
	 * @return string The formatted date.
	 */
	function NiceDate($timestamp, $includeTime=false)
	{
		$now = time();
		$difference = $now - $timestamp;
		$time = isc_date('h:i A', $timestamp);

		$timeDate = isc_date('Ymd', $timestamp);
		$todaysDate = isc_date('Ymd', $now);
		$yesterdaysDate = isc_date('Ymd', $now-86400);

		if($difference < 60) {
			return GetLang('LessThanAMinuteAgo');
		}
		else if($difference < 3600) {
			$minutes = ceil($difference/60);
			if($minutes == 1) {
				return GetLang('OneMinuteAgo');
			}
			else {
				return sprintf(GetLang('XMinutesAgo'), $minutes);
			}
		}
		else if($difference < 43200) {
			$hours = ceil($difference/3600);
			if($hours == 1) {
				return GetLang('OneHourAgo');
			}
			else {
				return sprintf(GetLang('XHoursAgo'), $hours);
			}
		}
		else if($timeDate == $todaysDate) {
			if($includeTime == true) {
				return sprintf(GetLang('TodayAt'), $time);
			}
			else {
				return GetLang('Today');
			}
		}
		else if($timeDate == $yesterdaysDate) {
			if($includeTime == true) {
				return sprintf(GetLang('YesterdayAt'), $time);
			}
			else {
				return GetLang('Yesterday');
			}
		}
		else {
			$date = CDate($timestamp);
			if($includeTime == true) {
				return sprintf(GetLang('OnDateAtTime'), $date, $time);
			}
			else {
				return sprintf(GetLang('OnDate'), $date);
			}
		}
	}

	/**
	* Robust integer check for all datatypes
	*
	* @param mixed $x
	*/

	function isc_is_int($x)
	{
		if (is_numeric($x)) {
			return (intval($x+0) == $x);
		}

		return false;
	}

	/**
	* Gets the url to use for the 'Proceed to Checkout' link. For Shared SSL the link will have the session token appended.
	*
	*/
	function CheckoutLink()
	{
		$link = $GLOBALS['ShopPathSSL'] . "/concluir.php";

		if (GetConfig('UseSSL') != SSL_SHARED || GetConfig('SharedSSLPath') == '') {
			return $link;
		}

		$host = '';
		if (function_exists('apache_getenv')) {
			$host = @apache_getenv('HTTP_HOST');
		}

		if (!$host) {
			$host = @$_SERVER['HTTP_HOST'];
		}

		$url = parse_url(GetConfig('SharedSSLPath'));

		if (!is_array($url)) {
			return $link;
		}

		if ($host != $url['host']) {
			return $link . "?tk=" . session_id();
		}

		return $link;
	}

	/**
	 * Parse an incoming shop path and turn it in to both a valid shop path and
	 * application path.
	 *
	 * @param string The URL to transform.
	 * @return array Array of shopPath and appPath
	 */
	function ParseShopPath($url)
	{
		$parts = parse_url($url);
		if(!isset($parts['scheme'])) {
			$parts['scheme'] = 'http';
		}

		if(!isset($parts['path'])) {
			$parts['path'] ='';
		}
		$parts['path'] = rtrim($parts['path'], '/');

		$shopPath = $parts['scheme'].'://'.$parts['host'];
		if(!empty($parts['port']) && $parts['port'] != 80) {
			$shopPath .= ':'.$parts['port'];
		}

		$shopPath .= $parts['path'];

		return array(
			'shopPath' => $shopPath,
			'appPath' => $parts['path']
		);
	}

	/**
	* Gets the IP address of the server.
	*
	* @return mixed The IP address string of the server or False if it couldn't be determined
	*/
	function GetServerIP()
	{
		if (isset($_SERVER['SERVER_ADDR'])) {
			return $_SERVER['SERVER_ADDR'];
		}
		elseif (function_exists('apache_getenv') && apache_getenv('SERVER_ADDR')) {
			return apache_getenv('SERVER_ADDR');
		}
		elseif (isset($_ENV['SERVER_ADDR'])){
			return $_ENV['SERVER_ADDR'];
		}

		return false;
	}

	/**
	* Strips out invalid unicode characters from a string to be used in XML
	*
	* @param string The string to be cleaned
	* @return string The input string with invalid characters removed
	*/
	function StripInvalidXMLChars($input)
	{
		// attempt to strip using replace first
		$replace_input = @preg_replace("/\p{C}/u", " ", $input);
		if (!is_null($replace_input)) {
			return $replace_input;
		}

		// manually check each character
		$output = "";
		for ($x = 0; $x < isc_strlen($input); $x++) {
			$char = isc_substr($input, $x, 1);
			$code = uniord($char);

			if ($code === false) {
				continue;
			}

			if ($code == 0x9 ||
				$code == 0xA ||
				$code == 0xD ||
				($code >= 0x20 && $code <= 0xD7FF) ||
				($code >= 0xE000 && $code <= 0xFFFD) ||
				($code >= 0x10000 && $code <= 0x10FFFF)) {

				$output .= $char;
			}
		}

		return $output;
	}

	if (!function_exists('array_fill_keys')) {
		/**
		* Fill an array with values, specifying keys
		*
		* @param array Array of values that will be used as keys.
		* @param mixed Value to use for filling
		* @return array The filled array
		*/
		function array_fill_keys($keys, $value)
		{
			return array_combine($keys, array_fill(0, count($keys), $value));
		}
	}

	/**
	 * Recursively remove any empty values and trim the others
	 *
	 * Function will recursively trim the values and remove any empties
	 *
	 * @param array $array The array to clean
	 * @param string $trimFunc The callback function for trimming the value. Default is "trim"
	 * @return array The filtered array
	 */
	function array_clean($array, $trimFunc="trim")
	{
		if (!is_array($array)) {
			return;
		}

		$filtered = array();
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$newVal = array_clean($val);
				if (!empty($newVal)) {
					$filtered[$key] = $newVal;
				}
			} else if (is_scalar($val)) {
				$newVal = call_user_func($trimFunc, $val);
				if ($newVal !== "") {
					$filtered[$key] = $newVal;
				}
			} else {
				$filtered[$key] = $val;
			}
		}

		return $filtered;
	}

	/**
	* Checks if a given string is a valid IPv4 address
	*
	* @param string The string to check
	* @return boolean True if the string is an IP, or false otherwise
	*/
	function isIPAddress($ipaddr)
	{
		if (preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ipaddr, $digit)) {
			if (($digit[1] <= 255) && ($digit[2] <= 255) && ($digit[3] <= 255) && ($digit[4] <= 255)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Recursively merge in an array with another without overwriting the initial data
	 *
	 * Method will recursively merge in the data for array $fill into the initial array $initial without
	 * overwriting any existing data in $initial
	 *
	 * @access public
	 * @param array $initial The initial array
	 * @param array $fill The array to merge in (to fill in)
	 * @return array The merged array on success, FALSE on error
	 */
	function array_merge_recursive_fill($initial, $fill)
	{
		if (!is_array($initial) || !is_array($fill)) {
			return false;
		}

		foreach (array_keys($fill) as $key) {
			if (!array_key_exists($key, $initial)) {
				$initial[$key] = $fill[$key];
			} else if (is_array($initial[$key])) {
				$initial[$key] = array_merge_recursive_fill($initial[$key], $fill[$key]);
			}
		}

		return $initial;
	}

	/**
	 * Check to see if the array is associative or not
	 *
	 * Function will check to see if the array is associative or not
	 *
	 * @access public
	 * @param array $array The array to check for
	 * @return bool TRUE if the array is associative, FALSE if not
	 */
	function is_associative_array($array)
	{
		if (!is_array($array) || empty($array)) {
			return false;
		}

		$keys = array_keys($array);
		$total = count($keys);
		$filtered = array_filter($keys, "isc_is_int");

		if (count($filtered) == $total) {
			return false;
		}

		return true;
	}

	/**
	 * Build the sort by options for the advance search sorting drop down
	 *
	 * Function will build the HTML options for the advance search sorting drop down
	 *
	 * @access public
	 * @param string $type Either 'product' or 'content'
	 * @param string $selected The optional selected option. Default is the config settings
	 * @return string The HTML options
	 */
	function getAdvanceSearchSortOptions($type, $selected='')
	{
		$html = "";
		$options = array();

		if (isc_strtolower($type) == "product") {
			$options = array("relevance", "featured", "newest", "bestselling", "alphaasc", "alphadesc", "avgcustomerreview", "priceasc", "pricedesc");
		} else {
			$options = array("relevance", "alphaasc", "alphadesc");
		}

		if (trim($selected) == "" || !in_array($selected, $options)) {
			$selected = GetConfig("SearchDefault" . ucfirst(isc_strtolower($type)) . "Sort");
		}

		foreach ($options as $option) {
			$html .= "<option value=\"" . addslashes($option) . "\"";

			if ($selected == $option) {
				$html .= " selected";
			}

			$html .= ">" . GetLang("SearchDefaultSort" . ucfirst(isc_strtolower($option))) . "</option>";
		}

		return $html;
	}

	/**
	 * Manage the pspell word database for node names
	 *
	 * Method will add/edit/delete the pspell word database for node names
	 *
	 * @access public
	 * @param string $type The node type (product, brand, category, etc)
	 * @param int $id The node ID
	 * @param string $name The node name to check in
	 * @return bool TRUE if the word was added/edited OR if the 'SearchSuggest' setting is turned off, FALSE on error
	 */
	function manageSuggestedWordDatabase($type, $id, $name)
	{
		// If search suggestions aren't enabled, don't try to build the list of suggested words
		if(!GetConfig("SearchSuggest")) {
			return true;
		}

		if (trim($type) == "" || !isId($id)) {
			return false;
		}

		$words = array();
		$parts = preg_split("#[(\s|\(|\)\/)]+#", $name);
		$pspellInstalled = false;

		if (function_exists("pspell_new")) {
			$pspellInstalled = true;
		}

		// Create a pSpell object if it's installed
		if ($pspellInstalled) {
			$spell = @pspell_new("en");
		}

		foreach ($parts as $part) {
			if (isc_strlen(trim($part)) > 2) {
				// Can we spell check against the word?
				if ($pspellInstalled && $spell) {
					if (!@pspell_check($spell, $part)) {
						$suggestions = @pspell_suggest($spell, $part);

						// If any suggestions are returned then the word generally misspelled
						if (count($suggestions) > 0) {
							$words[] = isc_strtolower($part);
						}
					}

				// pSpell isn't installed so we'll go ahead and add the word anyway
				} else {
					$words[] = isc_strtolower($part);
				}
			}
		}

		$table = isc_strtolower($type) . "_words";
		$column = isc_strtolower($type) . "id";

		$GLOBALS["ISC_CLASS_DB"]->DeleteQuery($table, "WHERE " . $column . " = " . (int)$id);

		// Add the words to the product_words table
		foreach ($words as $word) {
			$savedata = array(
				"word" => $word,
				$column => $id
			);

			$GLOBALS['ISC_CLASS_DB']->InsertQuery($table, $savedata);
		}

		return true;
	}

	/**
	 * Strip out the HTML for the search table
	 *
	 * Method will strip out all the HTML *but* leave in the 'title' and 'alt' attributes
	 *
	 * @access public
	 * @param string $str The string to strip out the HTML from
	 * @return string The formatted string
	 */
	function stripHTMLForSearchTable($str)
	{
		if (!is_string($str) || trim($str) == "") {
			return "";
		}

		$str = preg_replace("# (alt|title|longdesc)(\ +)?\=(\ +)?[\'\\\"]{1}([^\'\\\"]+)[\'\\\"]#", "> $4 <a", $str);

		return strip_tags($str);
	}
	/**
	 * debug function, used for logging variables and text to a tmp file
	 */
	function console_log($err)
	{
		if(is_array($err)){
			ob_start();
			print_r($err);
			$err = ob_get_contents();
			ob_end_clean();
		}

		if(is_object($err)){
			ob_start();
			var_dump($err);
			$err = ob_get_contents();
			ob_end_clean();
		}

		if(is_bool($err)){
			if($err === true) {
				$err = "true";
			} else {
				$err = "false";
			}
		}

		$err = $err ."\n\n";
		file_put_contents(dirname(dirname(__FILE__)). '/cache/log.txt', $err, FILE_APPEND);
	}

	function array_msort($array, $cols)
	{
		$colarr = array();
		foreach ($cols as $col => $order) {
			$colarr[$col] = array();
			foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		$params = array();
		foreach ($cols as $col => $order) {
			$params[] =& $colarr[$col];
			$params = array_merge($params, (array)$order);
		}
		call_user_func_array('array_multisort', $params);
		$ret = array();
		$keys = array();
		$first = true;
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				if ($first) { $keys[$k] = substr($k,1); }
				$k = $keys[$k];
				if (!isset($ret[$k])) $ret[$k] = $array[$k];
				$ret[$k][$col] = $array[$k][$col];
			}
			$first = false;
		}
		return $ret;

	}