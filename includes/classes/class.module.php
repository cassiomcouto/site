<?php

class ISC_MODULE
{
	/**
	 * @var string A unique id for this particular module (set automatically)
	 */
	protected $id = '';

	/**
	 * @var string The name of this module.
	 */
	protected $name = '';

	/**
	 * @var int The ID of the tab used to build the settings for this module.
	 */
	protected $tabId = 1;

	/**
	 * @var array A list of variables that this module has.
	 */
	protected $_variables = array();

	/**
	 * @var string The description of this module.
	 */
	protected $description = '';

	/**
	 * @var string The image to show for this module.
	 */
	protected $image = '';

	/**
	 * @var string The help text for this module.
	 */
	protected $help = '';

	/**
	 * @var string The type of help text to show (info or help)
	 */
	protected $helpType = 'help';

	/**
	 * @var string The type of module this is (analytics, checkout, shipping or notification)
	 */
	protected $type = '';

	/**
	 * @var string Any error messages set for this module.
	 */
	protected $errors = array();

	/**
	 * @var array Array of module variables for this module.
	 */
	protected $moduleVariables = array();

	/**
	* @var boolean Are the module variables loaded from the database
	*/
	protected $loadedVars = false;

	/**
	 * @var object Instance of the template class used to parse templates for this module.
	 */
	protected $template;

	/**
	 * The constructor will load any language variables for this module
	 *
	 */
	public function __construct()
	{
		$this->SetId(strtolower(get_class($this)));
		$this->LoadLanguageFile();
	}

	/**
	 * Return the ID of this module.
	 *
	 * @return string The ID of this module.
	 */
	public function GetId()
	{
		return $this->id;
	}

	/**
	 * Set the ID of this module.
	 *
	 * @param string The ID to set this module as.
	 */
	protected function SetId($Id)
	{
		$this->id = $Id;
	}

	/**
	 * Get the friendly name of this module.
	 *
	 * @return string The friendly name of this module.
	 */
	public function GetName()
	{
		// Kept for backwards compatibility
		if(isset($this->_name)) {
			return $this->_name;
		}

		return $this->name;
	}

	/**
	 * Set the friendly name of this module.
	 *
	 * @param string The friendly name to set this module to.
	 */
	protected function SetName($Name)
	{
		$this->name = $Name;
	}

	/**
	 * Get the description of this module.
	 *
	 * @return string The description of this module.
	 */
	public function GetDescription()
	{
		// Kept for backwards compatibility
		if(isset($this->_description)) {
			return $this->_description;
		}

		return $this->description;
	}

	/**
	 * Set the friendly name of this module.
	 *
	 * @param string The description to set this module to.
	 */
	protected function SetDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Checks if this module is enabled or not.
	 *
	 * @return boolean True if the module is enabled, false if not.
	 */
	public function IsEnabled()
	{
		if(method_exists($this, '_CheckEnabled')) {
			return $this->_CheckEnabled();
		}
		else if(method_exists($this, 'CheckEnabled')) {
			return $this->CheckEnabled();
		}
	}

	/**
	 * Check if this module is supported or not.
	 *
	 * @return boolean True if supported, false if unsupported.
	 */
	public function IsSupported()
	{
		// In the base class, assume all are supported.
		return true;
	}

	/**
	 * Get the help text to show for this module.
	 *
	 * @return string The help text to show for this module.
	 */
	public function GetHelpText()
	{
		// Kept for backwards compatibility
		if(isset($this->_help)) {
			return $this->_help;
		}

		return $this->help;
	}

	/**
	 * Set the help text to show for this module.
	 *
	 * @param string The text to set as the help text for this module.
	 * @param string The type of help text that this is. Set to 'info' to show the yellow info message.
	 */
	protected function SetHelpText($HelpText, $type='')
	{
		$this->help = $HelpText;
		if($type != '') {
			$this->helpType = $type;
		}
	}

	/**
	 * Get the type of help text we're showing.
	 *
	 * @return string The type of help text
	 */
	public function GetHelpTextType()
	{
		return $this->helpType;
	}

	/**
	 * Specify an image to use for this module's logo.
	 *
	 * @param string The filename of the image (relative to the folder for this module)
	 */
	protected function SetImage($ImageFile)
	{
		$this->image = $ImageFile;
	}

	/**
	 * Return the image to show as this module's logo.
	 *
	 * @return string The image file name for this module.
	 */
	public function GetImage()
	{
		// Kept for backwards compatibility
		if(isset($this->_image)) {
			$this->image = $this->_image;
		}

		if(!$this->image) {
			return '';
		}

		$idBits = explode('_', $this->GetId(), 2);
		if($idBits[0] == 'addon') {
			$image = '/addons';
		}
		else {
			$image = '/modules/'.$idBits[0];
		}
		$image .= '/'.$idBits[1];
		if($idBits[0] != 'addon') {
			$image .= '/images';
		}
		$image .= '/'.$this->image;
		if(file_exists(ISC_BASE_PATH.$image)) {
			return GetConfig('ShopPath').$image;
		}
		else {
			return '';
		}
	}

	/**
	 * Load the language file for this module in to the global language scope.
	 * Any language variable sthat conflict with existing language variables will be ignored.
	 */
	protected function LoadLanguageFile()
	{
		if (!isset($this->id) || empty($this->id)) {
			return;
		}

		if (!isset($this->type) || empty($this->type)) {
			return;
		}

		$lang = 'en';

		if (strpos(GetConfig('Language'), '/') === false) {
			$lang = GetConfig('Language');
		}

		$mod_id = str_replace($this->type.'_', '', $this->id);

		if($this->type == 'addon') {
			$directory = ISC_BASE_PATH.'/addons/';
		}
		else {
			$directory = ISC_BASE_PATH.'/modules/'.$this->type.'/';
		}

		$lang_file = $directory.$mod_id.'/lang/'.$lang.'/language.ini';


		// Try and fall back to english if the module hasn't been translated yet
		if (!is_file($lang_file)) {
			$lang_file = $directory.$mod_id.'/lang/en/language.ini';
		}

		if (!is_file($lang_file)) {
			return;
		}

		$GLOBALS['ISC_CLASS_TEMPLATE']->ParseLangFile($lang_file);
	}

	/**
	 * Gets & parses a module specific template. This has its own method because module specific templates are
	 * stored independently of the rest of the store.
	 *
	 * @param string The name of the template.
	 * @param boolean True to return the template, false to output.
	 */
	public function ParseTemplate($template, $return = false)
	{
		$mod_id = str_replace($this->type.'_', '', $this->id);

		if($this->type == 'addon') {
			$templateDir = ISC_BASE_PATH.'/addons/'.$mod_id.'/templates/';
		}
		else {
			$templateDir = ISC_BASE_PATH.'/modules/'.$this->type.'/'.$mod_id.'/templates/';
		}

		if(!is_object($this->template)) {
			$this->template = new TEMPLATE('ISC_LANG');
			$this->template->frontEnd = false;
			$this->template->templateExt = 'tpl';
			$this->template->SetTemplateBase($templateDir);
		}

		$this->template->SetTemplate($template);
		$output = $this->template->ParseTemplate($return);
		if($return) {
			return $output;
		}
	}

	/**
	 * Set an error message for this particular module.
	 *
	 * @param string The message to be set.
	 */
	protected function SetError($message)
	{
		$this->errors[] = $message;
	}

	/**
	 * Retrieve any error messages that this module has set.
	 *
	 * @return array Array of error messages.
	 */
	public function GetErrors()
	{
		return $this->errors;
	}

	/**
	 * Does this module have any errors set?
	 *
	 * @return boolean True if there are errors. False if not.
	 */
	public function HasErrors()
	{
		if(empty($this->errors)) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Reset the list of errors back to an empty array.
	 */
	public function ResetErrors()
	{
		$this->errors = array();
	}

	/**
	 * Get a list of the configurable variables for this module.
	 */
	public function GetCustomVars()
	{
		$this->SetCustomVars();
		return $this->_variables;
	}

	/**
	 * Set up any custom variables for the module.
	 */
	public function SetCustomVars()
	{
		return true;
	}

	/**
	 * Load any custom variables for the module.
	 */
	public function LoadCustomVars()
	{
		$this->loadedVars = true;

		$this->moduleVariables = array();

		$moduleBits = explode("_", $this->GetId(), 2);
		// First try to load a cached version of this module's settings
		$cachedModuleVars = $GLOBALS['ISC_CLASS_DATA_STORE']->Read(ucfirst($moduleBits[0]).'ModuleVars');
		if($cachedModuleVars !== false && isset($cachedModuleVars[$this->GetId()])) {
			$cachedModuleSettings = $cachedModuleVars[$this->GetId()];
			foreach($cachedModuleSettings as $varName => $varValue) {
				$this->moduleVariables[$varName] = $varValue;
			}
		}

		// Otherwise, fall back to the default
		else {
			$query = "SELECT * FROM [|PREFIX|]module_vars WHERE modulename='".$GLOBALS['ISC_CLASS_DB']->Quote($this->GetId())."'";
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
				$varName = str_replace($row['modulename'] . "_", "", $row['variablename']);

				if(isset($this->moduleVariables[$varName])) {
					if(!is_array($this->moduleVariables[$varName])) {
						$this->moduleVariables[$varName] = array($this->moduleVariables[$varName]);
					}
					$this->moduleVariables[$varName][] = $row['variableval'];
				}
				else {
					$this->moduleVariables[$varName] = $row['variableval'];
				}
			}
		}
	}

	/**
	* Set a value of a variable for a module - does not update the database and is primarily used for test harnesses.
	*
	* @param string $varName
	* @param mixed $value
	* @param bool $loaded If true (default) will also mark vars for this module as 'loaded' so as not to attempt to query the database
	* @return void
	*/
	public function SetValue ($varName, $value, $loaded = true)
	{
		if ($loaded) {
			$this->loadedVars = true;
		}

		$this->moduleVariables[$varName] = $value;
	}

	/**
	 * Get the value of a variable for a module
	 *
	 * @param string The name of the variable to get
	 * @return mixed The value of the variable or NULL if the value wasn't found
	 */
	public function GetValue($varName)
	{
		if (!$this->loadedVars) {
			$this->LoadCustomVars();
		}

		if(isset($this->moduleVariables[$varName])) {
			return $this->moduleVariables[$varName];
		}

		return null;
	}

	/**
	 * Log a message of status Debug to the Shopping Cart system log
	 *
	 * @param mixed The var to log.
	 * @param boolean Should we escape the html in the var
	 *
	 * @return void
	 **/
	public function DebugLog ($var='', $escape=true)
	{
		$output = print_r($var, true);

		if ($escape) {
			$output = isc_html_escape($output);
		}

		$trace = debug_backtrace();
		$last_call = array_shift($trace);

		$called_from = 'Called from file '.str_replace(ISC_BASE_PATH, '', $last_call['file']).' at line '.$last_call['line']."<br />\n";

		$GLOBALS['ISC_CLASS_LOG']->LogSystemDebug(array('payment', $this->_name), 'DEBUG: '.$called_from.'<pre>'."\n".$output."\n</pre>\n");
	}

	/**
	 * Build the HTML form item for each module variable.
	 *
	 * @param string The identifier for this
	 */
	protected function _BuildFormItem($id, &$var, $useTabs=true, $moduleId='')
	{
		// What type of variable is it?
		$item = "";

		if(!$moduleId) {
			$moduleId = $this->GetId();
		}

		if(!isset($GLOBALS['ValidationJavascript'])) {
			$GLOBALS['ValidationJavascript'] = '';
		}

		if($useTabs == true) {
			$showTab = "ShowTab(".$this->tabId.");";
		}
		else {
			$showTab = '';
		}

		if(!isset($var['type'])) {
			return '';
		}

		switch ($var['type']) {
			case "blank": {
				$item = "";
				$GLOBALS['Required'] = "";
				break;
			}
			case "label": {
				$item = $var['label'];
				$GLOBALS['Required'] = "&nbsp;&nbsp;";
				break;
			}
			case "custom": {
				$item = '';
				if (method_exists($this, $var['callback'])) {
					$item = call_user_func(array($this, $var['callback']), $id);
				}
				if(isset($var['javascript'])) {
					$GLOBALS['ValidationJavascript'] .= $var['javascript'];
				}

				if (isset($var['required']) && $var['required']) {
					$GLOBALS['Required'] = "<span class=\"Required\">*</span>";
				} else {
					$GLOBALS['Required'] = "&nbsp;&nbsp;";
				}

				break;
			}
			case "checkbox":
				$default = false;
				if (isset($var['default']) && $var['default'] != "") {
					$default = $var['default'];
				}

				if($this->GetValue($id)) {
					$default = $this->GetValue($id);
				}

				$checked = "";
				if ($default) {
					$checked = 'checked="checked"';
				}

				$txtName = $moduleId."[".$id."]";
				$txtId = $moduleId."_".$id;

				$item = '<input type="checkbox" name="' . $txtName . '" id="' . $txtId . '" value="1" ' . $checked . '/>';

				$GLOBALS['Required'] = "&nbsp;&nbsp;";

				if (isset($var['label']) && $var['label']) {
					$item = "<label>" . $item . $var['label'] . "</label>";
				}
				break;
			case 'text':
			case "textbox":
			case "password":

				$default = "";

				if (isset($var['default']) && $var['default'] != "") {
					$default = $var['default'];
				}

				if($this->GetValue($id)) {
					$default = $this->GetValue($id);
				}


				if(isset($var['format']) && $default !== '') {
					switch($var['format']) {
						case 'price':
							$default = FormatPrice($default, false, false);
							break;
						case 'weight':
						case 'dimension':
							$default = FormatWeight($default, false);
							break;
					}
				}

				$default = isc_html_escape($default);

				if (isset($var['size'])) {
					$txt_size = $var['size'];
					$txtClass = "Field";
				}
				else {
					$txt_size = "";
					$txtClass = "Field250";
				}

				if (isset($var['prefix'])) {
					$txtPrefix = $var['prefix'] . " ";
				} else {
					$txtPrefix = "";
				}

				if (isset($var['suffix'])) {
					$txtSuffix = ' '.$var['suffix'];
				} else {
					$txtSuffix = '';
				}

				if($var['type'] == 'password') {
					$type = 'password';
				}
				else {
					$type = 'text';
				}

				$readOnly = '';
				if(isset($var['readonly']) && $var['readonly'] == true) {
					$readOnly = 'readonly="readonly"';
				}

				$txtName = $moduleId."[".$id."]";
				$txtId = $moduleId."_".$id;
				$item = $txtPrefix."<input type=\"".$type."\" class=\"".$txtClass."\" name=\"".$txtName."\" id=\"".$txtId."\" value=\"".isc_html_escape($default)."\" size=\"".$txt_size."\" ".$readOnly." />".$txtSuffix;

				if (isset($var['required']) && $var['required']) {
					$GLOBALS['Required'] = "<span class=\"Required\">*</span>";
				} else {
					$GLOBALS['Required'] = "&nbsp;&nbsp;";
				}

				if (isset($var['required']) && $var['required']) {
					$message = addslashes(sprintf(GetLang('EnterValueForField'), $var['name']));
					$GLOBALS['ValidationJavascript'] .= "
						if(!$('#".$txtId."').val()) {
							".$showTab."
							alert('".$message."');
							$('#".$txtId."').focus();
							return false;
						}
					";
				}

				break;
			case "textarea": {
				$default = "";

				if ($var['default'] != "") {
					$default = $var['default'];
				}

				if($this->GetValue($id)) {
					$default = $this->GetValue($id);
				}

				if(isset($var['format']) && $default !== '') {
					switch($var['format']) {
						case 'price':
							$default = FormatPrice($default, false, false);
							break;
						case 'weight':
						case 'dimension':
							$default = FormatWeight($default, false);
							break;
					}
				}

				$default = isc_html_escape($default);

				if(isset($var['rows'])) {
					$txtRows = $var['rows'];
				}
				else {
					$txtRows = 5;
				}

				if(isset($var['prefix'])) {
					$txtPrefix = $var['prefix'] . " ";
				}
				else {
					$txtPrefix = "";
				}

				$txtName = sprintf("%s[%s]", $moduleId, $id);
				$txtId = sprintf("%s_%s", $moduleId, $id);
				if(!isset($var['class']) || $var['class'] == '') {
					$txtClass = "Field250";
				} else {
					$txtClass = $var['class'];
				}
				$item = sprintf("%s<textarea class='%s' name='%s' id='%s' rows='%d'>%s</textarea>", $txtPrefix, $txtClass, $txtName, $txtId, $txtRows, $default);

				if($var['required']) {
					$GLOBALS['Required'] = "<span class=\"Required\">*</span>";
				}
				else {
					$GLOBALS['Required'] = "&nbsp;&nbsp;";
				}

				if($var['required']) {
					$message = addslashes(sprintf(GetLang('EnterValueForField'), $var['name']));
					$GLOBALS['ValidationJavascript'] .= "
						if(!$('#".$txtId."').val()) {
							".$showTab."
							alert('".$message."');
							$('#".$txtId."').focus();
							return false;
						}
					";
				}

				break;
			}
			case "dropdown": {
				$additionalClass = '';
				if (isset($var['multiselect']) && $var['multiselect']) {
					if (isset($var['multiselectheight'])) {
						$multiSelect = sprintf("multiple size='%s'", $var['multiselectheight']);
					}
					else {
						$multiSelect = "multiple size='7'";
					}
					$additionalClass = "ISSelectReplacement";
				}
				else {
					$multiSelect = "";
				}

				if ($multiSelect) {
					$selName = sprintf("%s[%s][]", $moduleId, $id);
				}
				else {
					$selName = sprintf("%s[%s]", $moduleId, $id);
				}

				$selId = sprintf("%s_%s", $moduleId, $id);

				$item = sprintf("<select %s class='Field250 %s' name='%s' id='%s'>", $multiSelect, $additionalClass, $selName, $selId);

				if ($var['required']) {
					$GLOBALS['Required'] = "<span class=\"Required\">*</span>";
				} else {
					$GLOBALS['Required'] = "&nbsp;&nbsp;";
				}

				$default = '';
				if(isset($var['default'])) {
					$default = $var['default'];
				}

				if($this->GetValue($id)) {
					$default = $this->GetValue($id);
				}

				if(!is_array($default)) {
					$default = array($default);
				}

				// Loop through each of the options
				foreach ($var['options'] as $k=>$v) {
					$sel = '';
					if(in_array($v, $default)) {
						$sel = 'selected="selected"';
					}
					$item .= "<option ".$sel." value='".$v."'>".$k."</option>";
				}

				$item .= "</select>";

				if ($var['required']) {
					$message = addslashes(sprintf(GetLang('ChooseOptionForField'), $var['name']));
					$GLOBALS['ValidationJavascript'] .= "
						if($('#".$selId."').val() == -1) {
							".$showTab."
							alert('".$message."');
							$('#".$selId."').focus();
							return false;
						}
					";
				}

				break;
			}
		}

		return $item;
	}

	/**
	 * Get the number of settings that this module has.
	 *
	 * @return int The number of settings.
	 */
	public function GetNumSettings()
	{
		return count($this->_variables);
	}


	/**
	 * Save the configuration variables for this module that come in from the POST
	 * array.
	 *
	 * @param array An array of configuration variables.
	 * @param bool RUE to delete any existing module settings, FALSE not to. Default is TRUE
	 * @return boolean True if successful.
	 */
	public function SaveModuleSettings($settings=array(), $deleteFirst=true)
	{
		// Delete any current settings the module has if we are set to
		if ($deleteFirst) {
			$this->DeleteModuleSettings();
		}

		// If we weren't supplied any settings and this module has one or more settings
		// don't continue and don't mark it as being set up yet
		if(empty($settings) && $this->GetNumSettings() > 0) {
			return true;
		}

		// Mark the module has being configured
		$newVar = array(
			'modulename' => $this->GetId(),
			'variablename' => 'is_setup',
			'variableval' => 1
		);
		$GLOBALS['ISC_CLASS_DB']->InsertQuery('module_vars', $newVar);

		$moduleVariables = $this->GetCustomVars();

		$this->moduleVariables = array();

		// Loop through the options that this module has
		foreach($settings as $name => $value) {
			$format = '';
			if(isset($moduleVariables[$name]['format'])) {
				$format = $moduleVariables[$name]['format'];
			}

			if(is_array($value)) {
				foreach($value as $childValue) {
					switch($format) {
						case 'price':
							$value = DefaultPriceFormat($childValue);
							break;
						case 'weight':
						case 'dimension':
							$value = DefaultDimensionFormat($value);
							break;
					}
					$newVar = array(
						'modulename' => $this->GetId(),
						'variablename' => $name,
						'variableval' => $childValue
					);
					$GLOBALS['ISC_CLASS_DB']->InsertQuery('module_vars', $newVar);

					$this->moduleVariables[$name][] = $childValue;
				}
			}
			else {
				switch($format) {
					case 'price':
						$value = DefaultPriceFormat($value);
						break;
					case 'weight':
					case 'dimension':
						$value = DefaultDimensionFormat($value);
						break;
				}
				$newVar = array(
					'modulename' => $this->GetId(),
					'variablename' => $name,
					'variableval' => $value
				);

				$GLOBALS['ISC_CLASS_DB']->InsertQuery('module_vars', $newVar);

				$this->moduleVariables[$name] = $value;
			}
		}

		$this->loadedVars = true;

		return true;
	}

	/**
	 * Delete all of the configuration/settings associated with this module.
	 *
	 */
	public function DeleteModuleSettings()
	{
		// Delete the existing settings for this module
		$GLOBALS['ISC_CLASS_DB']->DeleteQuery('module_vars', "WHERE modulename='".$this->GetId()."'");
	}
}
