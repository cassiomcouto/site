<?php
class ADDON_RASTREAMENTO extends ISC_ADDON
{

	public function __construct()
	{

		parent::__construct();


		$this->SetName('Rastreador Correios');


		$this->SetImage('');


		$this->SetHelpText('Rastreador de Pedidos Online Enviados via Correios do Brasil.<br>Para configurar o Cron/Agendador de Tarefa use os dados abaixo.<br>1 - Periodicidade use a cada 2 Horas.<br>2 - Url a ser Execultada sera '.GetConfig("ShopPath").'/modificacoes/croncorreios.php<br>3 - Com os dados acima e só configurar seu Cpanel, Plesk ou outro painel.<br>Obs: Para que o sistema rastrear um pedido o Admin da loja deverar salvar o Codigo de rastreamento do mesmo no campo correto (um apenas por pedido), e em seguinda atualizar o Status do pedido para Enviado ou Enviado Parcialmete. Sempre que um pedido for dado pelo sistema como Entregue, automaticamente o Status do pedido e modificado para Completo.');

$sql = "CREATE TABLE IF NOT EXISTS `rastreamento` (
  `id` int(8) NOT NULL auto_increment,
  `pedido` int(10) NOT NULL,
  `data` varchar(20) NOT NULL,
  `hora` varchar(20) NOT NULL,
  `status` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
@$GLOBALS['ISC_CLASS_DB']->Query($sql);

	}

	public function SetCustomVars()
	{


		$this->_variables['ativar'] = array(
			'type' => 'dropdown',
			'name' => 'Ativar Rastreamento?',
			'default' => 'sim',
			'options' => array(
				'SIM' => 'sim',
				'NAO' => 'nao'

			),
			"multiselect" => false,
			'required' => true
		);

		$this->_variables['avisar'] = array(
			'type' => 'dropdown',
			'name' => 'Avisar sempre que?',
			'default' => '2',
			'options' => array(
				'Quando Entregue?' => '0',
				'Aguardar Retirada?' => '1',
				'Todas Atualizacoes' => '2'

			),
			"multiselect" => false,
			'required' => true
		);
		
		$this->_variables['avisaradm'] = array(
			'type' => 'dropdown',
			'name' => 'Avisar Admin Quando Entregue?',
			'default' => 'nao',
			'options' => array(
				'SIM' => 'sim',
				'NAO' => 'nao'

			),
			"multiselect" => false,
			'required' => true
		);


	}


	public function Init()
	{

	}


	public function EntryPoint()
	{
	
    echo 'Modulo sem area interativa, apenas configure o mesmo.';
	}


}
?>