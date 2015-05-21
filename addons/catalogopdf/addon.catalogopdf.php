<?php
class ADDON_CATALOGOPDF extends ISC_ADDON
{

	public function __construct()
	{

		parent::__construct();


		$this->SetName('Catalogo PDF 1.0');


		$this->SetImage('catalogo.gif');


		$this->SetHelpText('Configuracoes do catalogo virtual de produtos em PDF.');


	}

	public function SetCustomVars()
	{

		$this->_variables['ativo'] = array(
			'type' => 'dropdown',
			'name' => 'Ativar Catalogo?',
                        "help" => 'Ativar o catalogo na Loja.',
			'default' => 's',
			'options' => array(
				'SIM' => 's',
				'NAO' => 'n'
			),
			"multiselect" => false,
			'required' => true
		);

                $this->_variables['url'] = array(
			'type' => 'dropdown',
			'name' => 'Ativar Mini URL?',
                        "help" => 'Ativar Mini URL com o endereco http://bity.ly/GHhb5D.',
			'default' => 'n',
			'options' => array(
				'SIM' => 's',
				'NAO' => 'n'
			),
			"multiselect" => false,
			'required' => true
		);

                $this->_variables['loja'] = array("name" => "Nome da Loja?",
			   "type" => "textbox",
			   "help" => 'Ponha o nome da loja a ser exibido no PDF.',
			   "default" => GetConfig('StoreName'),
			   "required" => true
                );

                $this->_variables['cor1'] = array("name" => "Cor 01?",
			   "type" => "textbox",
			   "help" => 'Ponha a cor 01 a ser exibida a lista de produtos no PDF.',
			   "default" => '#D3D3D3',
			   "required" => true
                );

                $this->_variables['cor2'] = array("name" => "Cor 02?",
			   "type" => "textbox",
			   "help" => 'Ponha a cor 02 a ser exibida a lista de produtos no PDF.',
			   "default" => '#FFFFFF',
			   "required" => true
                );

                $this->_variables['imagem'] = array("name" => "Imagem de Inicio?",
			   "type" => "textbox",
			   "help" => 'Ponha o endereco completo da URL com o caminho da imagem a ser exibida na primeira pagina do catalogo.',
			   "default" => GetConfig('ShopPath').'/modificacoes/MPDF45/logo.jpg',
			   "required" => true
                );

                $this->_variables['chave'] = array("name" => "Serial do Addon?",
			   "type" => "textbox",
			   "help" => 'Ponha o serial recebido para o dominio registrado.',
			   "default" => 'ILIMITADO',
			   "required" => true
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
