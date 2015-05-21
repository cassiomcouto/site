<?php

class PANEL
{

	var $_htmlFile;


	var $_tplFile;


	function ParsePanel($TplFile="")
	{
		$htmlPanelData = '';
		$parsedPanelData = '';

		$this->_tplFile = $TplFile;

		// check for file and load the contents
		if (file_exists($this->_htmlFile)) {
			if ($fp = @fopen($this->_htmlFile, 'rb')) {
				while (!feof($fp)) {
					$htmlPanelData .= fgets($fp, 4096);
				}
				@fclose($fp);
			}
		}

		// set the global settings/variables for all panels
		$this->SetGlobalPanelSettings();

		// sets the local panel settings, defined within the extendee
		if (method_exists($this, 'SetPanelSettings')) {
			$this->SetPanelSettings();
		}

		// some panels require the option to return blank
		if (isset($this->DontDisplay) && $this->DontDisplay == true) {
			$parsedPanelData = '';
		} else {
			// parse panel as normal
			$parsedPanelData = $htmlPanelData;
		}

		return $parsedPanelData;
	}

	/**
	* SetHTMLFile() Function
	*
	* Sets the Class-wide html filename variable. Only used within the extended panel class.
	*
	* @name		SetHTMLFile()
	* @author	Jordie Bodlay <jordie@yagbu.net>
	* @version 	1.00
	* @param 	string $HTMLFile
	*
	*/
	function SetHTMLFile($HTMLFile)
	{
		$this->_htmlFile = $HTMLFile;
	}

	/**
	* SetGlobalPanelSettings() Function
	*
	* Sets variables and settings that can then be accessed from any panel.
	*
	* @name		SetGlobalPanelSettings()
	* @author	Jordie Bodlay <jordie@yagbu.net>
	* @version 	1.00
	*
	*/
	function SetGlobalPanelSettings()
	{
		// make the site's URL global. e.g. use for image path's
	}

}