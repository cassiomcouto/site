<?php

class ADDON_COMPRAMINIMA extends ISC_ADDON
{
	/**
	 * Set up the addon specific variables for this addon.
	 */
	public function __construct()
	{
		// Call the parent constructor (this is required!)
		parent::__construct();

		// Set the display name for this addon
		$this->SetName('Compra Minima');

		// Set the image for this addon
		$this->SetImage('');

		// Set the help text for this addon
		$this->SetHelpText('Especifique o Valor Minimo para Compra na Loja');

		
	}

	/**
	 * Setup the settings for this addon.
	 */
	public function SetCustomVars()
	{
	
	// Register a menu item for this addon under the orders menu
		$this->RegisterMenuItem(array(
			'location'		=> 'mnuOrders',
			'icon'			=> 'icon.gif',
			'text'			=> 'Especifique o Valor Minimo para Compra na Loja',
			'description'	=> 'Especifique o Valor Minimo para Compra na Loja'
		));
                		
		$this->_variables['valor'] = array(
			'type' => 'text',
			'name' => 'Valor Minimo de Compra',
			'default' => '00',
			'required' => true
		);
	}


	public function Init()
	{
		//$this->ShowSaveAndCancelButtons(false);
	}


	public function ToolsMenuExample()
	{
		echo "Acesse Adicionas > Configuracoes de Adicionais e Especifique o Valor.";
	}
}