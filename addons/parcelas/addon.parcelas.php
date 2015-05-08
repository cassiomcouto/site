<?php



class ADDON_PARCELAS extends ISC_ADDON



{







	public function __construct()



	{







		parent::__construct();











		$this->SetName('Simulador de Parcelas e Addons');











		$this->SetImage('');











		$this->SetHelpText('Escolha quais meios de pagamento o simulador de parcela ira exibir');











		$this->RegisterMenuItem(array(



			'location'		=> 'mnuTools',



			'icon'			=> 'icon.gif',



			'text'			=> 'Simulador de Parcelas e Addons',



			'description'	=> 'Configure as Opções que Sera Ultilizadas no Simulador de Frete',



			'id'			=> 'addon_simularparcelas'



		));















	}







	public function SetCustomVars()



	{





	$this->_variables['loginparapreco'] = array(

			'type' => 'dropdown',

			'name' => 'Amostrar preço na loja ?',

			'default' => 'sim',

			'options' => array(

				'Mostrar somente para logados' => 'nao',

				'Mostrar para todos' => 'sim'

			),

			"multiselect" => false,

			'required' => true);







		$this->_variables['tipos'] = array(



			'type' => 'dropdown',



			'name' => 'Tipos Aceitos (Pagina Produto)',



			'default' => '',



			'options' => array(



				'Deposito' => 'deposito',
	'Cielo Pagamentos' => 'cielo',
	'MercadoPago Pagamentos' => 'mercadopago',


				'Cheque' => 'cheque',



				'Boleto' => 'boleto',



				'PagSeguro' => 'pagseguro',



				'BCash' => 'bcash',



				'MOIP' => 'moip',



				'DinheiroMail' => 'dinheiromail',



				'Paypal' => 'paypalbrasil',



				'Visanet - Credito' => 'visacredito',



				'Visanet - Debito' => 'visadebito',



				'Mastercard' => 'master',



				'Dinners' => 'dinners',



				'SPS Bradesco' => 'sps',



				'Itau Shopline' => 'shopline',



				'BB Ofice Bank' => 'bbofice'



			),



			"multiselect" => true,



			'required' => true



		);



			$this->_variables['rodape1'] = array(



			'type' => 'dropdown',



			'name' => 'Tipo Aceito 01 (Rodape Produtos)',



			'default' => 'deposito',



			'options' => array(



		        'Nao Mostrar' => '1',



				'Deposito' => 'deposito',

	'Cielo Pagamentos' => 'cielo',
	'MercadoPago Pagamentos' => 'mercadopago',

				'Cheque' => 'cheque',



				'Boleto' => 'boleto',



				'PagSeguro' => 'pagseguro',



				'BCash' => 'bcash',



				'MOIP' => 'moip',



				'DinheiroMail' => 'dinheiromail',



				'Paypal' => 'paypalbrasil',



				'Visanet - Credito' => 'visacredito',



				'Visanet - Debito' => 'visadebito',



				'Mastercard' => 'master',



				'Dinners' => 'dinners',



				'SPS Bradesco' => 'sps1',



				'Itau Shopline' => 'shopline',



				'BB Ofice Bank' => 'bbofice'



			),



			"multiselect" => false,



			'required' => true



		);



		$this->_variables['rodape2'] = array(



			'type' => 'dropdown',



			'name' => 'Tipo Aceito 02 (Rodape Produtos)',



			'default' => 'pagseguro',



			'options' => array(



				'Nao Mostrar' => '1',



				'Deposito' => 'deposito',
	'Cielo Pagamentos' => 'cielo',
	'MercadoPago Pagamentos' => 'mercadopago',


				'Cheque' => 'cheque',



				'Boleto' => 'boleto',



				'PagSeguro' => 'pagseguro',



				'BCash' => 'bcash',



				'MOIP' => 'moip',



				'DinheiroMail' => 'dinheiromail',



				'Paypal' => 'paypalbrasil',



				'Visanet - Credito' => 'visacredito',



				'Visanet - Debito' => 'visadebito',



				'Mastercard' => 'master',



				'Dinners' => 'dinners',



				'SPS Bradesco' => 'sps1',



				'Itau Shopline' => 'shopline',



				'BB Ofice Bank' => 'bbofice'



			),



			"multiselect" => false,



			'required' => true



		);



		$this->_variables['descboleto'] = array("name" => "Desconto % (Apenas Boleto)",



			   "type" => "textbox",



			   "help" => 'Ponha o a Taxa a ser Cobrado em Cada Boleto.',



			   "default" => '0',



			   "required" => false);



			   







			






		



		$this->_variables['pdf'] = array(



			'type' => 'dropdown',



			'name' => 'Ativar Catalogo PDF',



			'default' => 'sim',



			'options' => array(



				'Sim, Ativar' => 'sim',



				'Não Ativar' => 'nao'



			),



			"multiselect" => false,



			'required' => true);



			







			   



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