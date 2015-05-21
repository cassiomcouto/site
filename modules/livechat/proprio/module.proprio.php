<?php
/**
 * Custom live chat integration module for Loja Virtual V2010.
 */
class LIVECHAT_PROPRIO extends ISC_LIVECHAT
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->SetName('Atendimento Online Proprio');
		$this->SetHelpText('Sistema de Atendimento Online Proprio');
	}

	/**
	 * Define the configurable settings for the custom live integration.
	 */
	public function SetCustomVars()
	{
		$this->_variables['livechatcode'] = array(
			'name' => 'Codigo HTML do Chat',
			'type' => 'textarea',
			'help' => 'Cole o Codigo HTML Gerado pelo seu sistema de atendimento online. Ex: Livezilla, Live Help, Suport Suite e muitos outros.',
			'default' => '',
			'required' => true
		);

		$this->_variables['position'] = array(
			'name' => 'Local',
			'type' => 'dropdown',
			'help' => 'Local onde ira aparecer o icone de atendimento online',
			'default' => 'panel',
			'options' => array(
				'Topo' => 'header',
				'Painel' => 'panel'
			),
			'required' => true
		);
	}

	/**
	 * Get the live chat tracking code for this module for the specified page position.
	 *
	 * @param string The position (header or panel) to fetch the tracking code for. If not the position that's
	 *				 enabled for this module, then this method should return an empty string.
	 * @return string String containing the live chat code.
	 */
	public function GetLiveChatCode($position)
	{
		if($position == $this->GetValue('position')) {
			return $this->GetValue('livechatcode');
		}
	}
}