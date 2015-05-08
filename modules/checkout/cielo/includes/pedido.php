<?php
	
	class Pedido
	{

		public $dadosEcNumero;
		public $dadosEcChave;
		
		public $dadosPortadorNumero;
		public $dadosPortadorVal;
		public $dadosPortadorInd;
		public $dadosPortadorCodSeg;
		public $dadosPortadorNome;
		
		public $dadosPedidoNumero;
		public $dadosPedidoValor;
		public $dadosPedidoMoeda = "986";
		public $dadosPedidoData;
		public $dadosPedidoDescricao;
		public $dadosPedidoIdioma = "PT";
		
		public $formaPagamentoBandeira;
		public $formaPagamentoProduto;
		public $formaPagamentoParcelas;
		
		public $urlRetorno;
		public $autorizar;
		public $capturar;
		
		public $tid;
		public $status;
		public $urlAutenticacao;
		
		const ENCODING = "ISO-8859-1";
		
		function __construct()
		{
			// cria um logger
		}
		
		// Geradores de XML
		public function XMLHeader()
		{
			return '<?xml version="1.0" encoding="' . self::ENCODING . '" ?>'; 
		}
		
		public function XMLDadosEc()
		{
			$msg = '<dados-ec>' . "\n      " .
						'<numero>'
							. $this->dadosEcNumero . 
						'</numero>' . "\n      " .
						'<chave>'
							. $this->dadosEcChave .
						'</chave>' . "\n   " .
					'</dados-ec>';
							
			return $msg;
		}
		
		public function XMLDadosPortador()
		{
			$msg = '<dados-portador>' . "\n      " . 
						'<numero>' 
							. $this->dadosPortadorNumero .
						'</numero>' . "\n      " .
						'<validade>'
							. $this->dadosPortadorVal .
						'</validade>' . "\n      " .
						'<indicador>'
							. $this->dadosPortadorInd .
						'</indicador>' . "\n      " .
						'<codigo-seguranca>'
							. $this->dadosPortadorCodSeg .
						'</codigo-seguranca>' . "\n   ";
			
			// Verifica se Nome do Portador foi informado
			if($this->dadosPortadorNome != null && $this->dadosPortadorNome != "")
			{
				$msg .= '   <nome-portador>'
							. $this->dadosPortadorNome .
						'</nome-portador>' . "\n   " ;
			}
			
			$msg .= '</dados-portador>';
			
			return $msg;
		}
		
		public function XMLDadosCartao()
		{
			$msg = '<dados-cartao>' . "\n      " . 
						'<numero>' 
							. $this->dadosPortadorNumero .
						'</numero>' . "\n      " .
						'<validade>'
							. $this->dadosPortadorVal .
						'</validade>' . "\n      " .
						'<indicador>'
							. $this->dadosPortadorInd .
						'</indicador>' . "\n      " .
						'<codigo-seguranca>'
							. $this->dadosPortadorCodSeg .
						'</codigo-seguranca>' . "\n   ";

			// Verifica se Nome do Portador foi informado				
			if($this->dadosPortadorNome != null && $this->dadosPortadorNome != "")
			{
				$msg .= '   <nome-portador>'
							. $this->dadosPortadorNome .
						'</nome-portador>' . "\n   " ;
			}
			
			$msg .= '</dados-cartao>';
			
			return $msg;
		}
		
		public function XMLDadosPedido()
		{
			$this->dadosPedidoData = date("Y-m-d") . "T" . date("H:i:s");
			$msg = '<dados-pedido>' . "\n      " .
						'<numero>'
							. $this->dadosPedidoNumero . 
						'</numero>' . "\n      " .
						'<valor>'
							. $this->dadosPedidoValor .
						'</valor>' . "\n      " .
						'<moeda>'
							. $this->dadosPedidoMoeda .
						'</moeda>' . "\n      " .
						'<data-hora>'
							. $this->dadosPedidoData .
						'</data-hora>' . "\n      ";
			if($this->dadosPedidoDescricao != null && $this->dadosPedidoDescricao != "")
			{
				$msg .= '<descricao>'
					. $this->dadosPedidoDescricao .
					'</descricao>' . "\n      ";
			}
			$msg .= '<idioma>'
						. $this->dadosPedidoIdioma .
					'</idioma>' . "\n   " .
					'</dados-pedido>';
							
			return $msg;
		}
		
		public function XMLFormaPagamento()
		{
			$msg = '<forma-pagamento>' . "\n      " .
						'<bandeira>' 
							. $this->formaPagamentoBandeira .
						'</bandeira>' . "\n      " .
						'<produto>'
							. $this->formaPagamentoProduto .
						'</produto>' . "\n      " .
						'<parcelas>'
							. $this->formaPagamentoParcelas .
						'</parcelas>' . "\n   " .
					'</forma-pagamento>';
							
			return $msg;
		}
		 
		public function XMLUrlRetorno()
		{
			$msg = '<url-retorno>' . $this->urlRetorno . '</url-retorno>';
			
			return $msg;
		}
		
		public function XMLAutorizar()
		{
			$msg = '<autorizar>' . $this->autorizar . '</autorizar>';
			
			return $msg;
		}
		
		public function XMLCapturar()
		{
			$msg = '<capturar>' . $this->capturar . '</capturar>';
			
			return $msg;
		}
		
		// Envia Requisição
		public function Enviar($vmPost, $transacao)
		{

			// ENVIA REQUISIÇÃO SITE CIELO
			$vmResposta = httprequest(ENDERECO, "mensagem=" . $vmPost);
			
			return @simplexml_load_string($vmResposta);
		}
		
		// Requisições
		public function RequisicaoTransacao($incluirPortador)
		{
			$msg = $this->XMLHeader() . "\n" .
				   '<requisicao-transacao id="' . md5(date("YmdHisu")) . '" versao="' . VERSAO . '">' . "\n   "
				   		. $this->XMLDadosEc() . "\n   ";
			if($incluirPortador == true)
			{
					$msg .=	$this->XMLDadosPortador() . "\n   ";
			}
			$msg .=		  $this->XMLDadosPedido() . "\n   "
				   		. $this->XMLFormaPagamento() . "\n   "
				   		. $this->XMLUrlRetorno() . "\n   "
				   		. $this->XMLAutorizar() . "\n   "
				   		. $this->XMLCapturar() . "\n" ;
			
			$msg .= '</requisicao-transacao>';
			
			$objResposta = $this->Enviar($msg, "Transacao");
			return $objResposta;
		}
		
		public function RequisicaoTid()
		{
			$msg = $this->XMLHeader() . "\n" .
				   '<requisicao-tid id="' . md5(date("YmdHisu")) . '" versao ="' . VERSAO . '">' . "\n   "
				        . $this->XMLDadosEc() . "\n   " 
				        . $this->XMLFormaPagamento() . "\n" .
				   '</requisicao-tid>';
				        
			$objResposta = $this->Enviar($msg, "Requisicao Tid");
			return $objResposta;
		}
		
		public function RequisicaoAutorizacaoPortador()
		{
			$msg = $this->XMLHeader() . "\n" .
				   '<requisicao-autorizacao-portador id="' . md5(date("YmdHisu")) . '" versao ="' . VERSAO . '">' . "\n"
				   		. '<tid>' . $this->tid . '</tid>' . "\n   "
				        . $this->XMLDadosEc() . "\n   " 
				        . $this->XMLDadosCartao() . "\n   "
				        . $this->XMLDadosPedido() . "\n   "
				        . $this->XMLFormaPagamento() . "\n   "
				        . '<capturar-automaticamente>' . $this->capturar . '</capturar-automaticamente>' . "\n" .
				   '</requisicao-autorizacao-portador>';
			
			$objResposta = $this->Enviar($msg, "Autorizacao Portador");
			return $objResposta;
		}
		
		public function RequisicaoAutorizacaoTid()
		{
			$msg = $this->XMLHeader() . "\n" .
				 '<requisicao-autorizacao-tid id="' . md5(date("YmdHisu")) . '" versao="' . VERSAO . '">' . "\n  "
				 	. '<tid>' . $this->tid . '</tid>' . "\n  "
				 	. $this->XMLDadosEc() . "\n" .
				 '</requisicao-autorizacao-tid>';
				 	
			$objResposta = $this->Enviar($msg, "Autorizacao Tid");
			return $objResposta;
		}
		
		public function RequisicaoCaptura()
		{
			$msg = $this->XMLHeader() . "\n" .
				    '<requisicao-captura id="' . md5(date("YmdHisu")) . '" versao="' . VERSAO . '">' . "\n   "
				   	. '<tid>' . $this->tid . '</tid>' . "\n   "
				   	. $this->XMLDadosEc() . "\n";

				$msg .=	'   <anexo>Capturada Quando Cliente Visualizou o Cupom com IP: '.$_SERVER["REMOTE_ADDR"].'</anexo>' . "\n";

			$msg .= '</requisicao-captura>';
			
			$objResposta = $this->Enviar($msg, "Captura");
			return $objResposta;
		}
		
		public function RequisicaoCancelamento()
		{
			$msg = $this->XMLHeader() . "\n" . 
				   '<requisicao-cancelamento id="' . md5(date("YmdHisu")) . '" versao="' . VERSAO . '">' . "\n   "
				    . '<tid>' . $this->tid . '</tid>' . "\n   "
				    . $this->XMLDadosEc() . "\n" .
				   '</requisicao-cancelamento>';
			
			$objResposta = $this->Enviar($msg, "Cancelamento");
			return $objResposta;
		}
		
		
		
		public function RequisicaoConsulta()
		{
			$msg = $this->XMLHeader() . "\n" .
				   '<requisicao-consulta id="' . md5(date("YmdHisu")) . '" versao="' . VERSAO . '">' . "\n   "
				    . '<tid>' . $this->tid . '</tid>' . "\n   "
				    . $this->XMLDadosEc() . "\n" .
				   '</requisicao-consulta>';
			
			$objResposta = $this->Enviar($msg, "Consulta");
			return $objResposta;
		}
		
		
		
		// Traduz cógigo do Status
		public function getStatus()
		{
			$status;
			
			switch($this->status)
			{
				case "0": $status = "Criada";
						break;
				case "1": $status = "Em andamento";
						break;
				case "2": $status = "Autenticada";
						break;
				case "3": $status = "Não autenticada";
						break;
				case "4": $status = "Autorizada";
						break;
				case "5": $status = "Não autorizada";
						break;
				case "6": $status = "Aprovada";
						break;
				case "8": $status = "Não capturada";
						break;
				case "9": $status = "Cancelada";
						break;
				case "10": $status = "Em autenticação";
						break;
				default: $status = "n/a";
						break;
			}
			
			return $status;
		}
		
	}
	
?>