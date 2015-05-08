<?php
class ADDON_SIMULARFRETE extends ISC_ADDON
{

	public function __construct()
	{

		parent::__construct();


		$this->SetName('Simulador de Frete');


		$this->SetImage('');


		$this->SetHelpText('Escolha quais meios o simulador de Frete Ira Mostrar');


		$this->RegisterMenuItem(array(
			'location'		=> 'mnuTools',
			'icon'			=> 'icon.gif',
			'text'			=> 'Simulador de Frete',
			'description'	=> 'Configure as Opções que Sera Ultilizadas no Simulador de Frete',
			'id'			=> 'addon_simularfrete'
		));



	}

	public function SetCustomVars()
	{

		$this->_variables['tipos'] = array(
			'type' => 'dropdown',
			'name' => 'Tipos Aceitos',
			'default' => '',
			'options' => array(
				'PAC' => 'pac',
				'E-Sedex' => 'esedex',
				'Sedex a Cobrar' => 'acobrar',
				'Braspress' => 'bras',
				'DirectLog' => 'direct',
				'Trans. da Loja' => 'fixo',
				'Sedex' => 'sedex',
				'Download' => 'download'
			),
			"multiselect" => true,
			'required' => true
		);
	}


	public function Init()
	{

	}


	public function EntryPoint()
	{

	}


	public function ToolsMenuExample()
	{
	}
}