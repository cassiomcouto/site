<?php

class SHIPPING_BRASPRESS extends ISC_SHIPPING
{
///////////////////////////////////////////////


		var $_shipclasses = array();
		var $_carriers = array();			
		


function SHIPPING_BRASPRESS(){
			parent::__construct();
			$this->_name = "Braspress - Transportadora";
			$this->_image = "sedex.gif";
			$this->_description = "Modulo de Envio por Transportadora Braspress";
			$this->_help = "Modulo de Envio por Transportadora Braspress";
			$this->_enabled = $this->CheckEnabled();
			$this->_countries = array("all");
			$this->_showtestlink = false;
}
//////////////////////////////////////////////
public function SetCustomVars()
{
	    $this->_variables['displayname'] = array(
		'name'			=> 'Braspress',
		'type'		=> 'textbox',
		'help'		=> 'Modulo de Envio por Transportadora Braspress.',
		'default'	=> $this->GetName(),
		'savedvalue'	=> array(),
		'required'	=> true);
		
		$this->_variables['cnpj'] = array(
		'name'			=> 'CNPJ',
		'type'		=> 'textbox',
		'help'		=> 'Ponha o CNPJ da Empresa.',
		'default'	=> '00000000000000',
		'required'	=> true);
		
		$this->_variables['numempresa'] = array(
		'name'			=> 'Empresa de Transporte (Numero)',
		'type'		=> 'textbox',
		'help'		=> 'Numero da Empresa de Transporte.',
		'default'	=> '0',
		'required'	=> true);
		
		$this->_variables['padrao'] = array(
		'name'			=> 'Peso Base (Cubagem: 300)',
		'type'		=> 'textbox',
		'help'		=> 'Peso de Base Padrao para Cubagem.',
		'default'	=> '300',
		'required'	=> true);
		
		$this->_variables['cep'] = array(
		'name'			=> 'CEP Local (Numero)',
		'type'		=> 'textbox',
		'help'		=> 'Ponha o CEP de Origem para calculo. Somente Numero.',
		'default'	=> '',
		'required'	=> true);
		
		$this->_variables['tipodefrete'] = array(
		'name'			=> 'Tipo de Frete',
		'type'		=> 'textbox',
		'help'		=> 'Ponha o Tipo de Frete de Envio.',
		'default'	=> '1',
		'required'	=> true);
}
/////////////////////////////////
function GetServiceQuotes()
{
	$QuoteList = array();
	$services = "Sedex";
	if(!is_array($services) && $services != "") {
		$services = array($services);
	}
	foreach($services as $service) {
		// Set the service type
		$this->_deliverytype = $service;

		// Next actually retrieve the quote
		$result = $this->GetQuote();

		// Was it a valid quote?
		if(is_object($result)) {
			$QuoteList[] = $result;
		// Invalid quote, log the error
		} else {
			foreach($this->GetErrors() as $error) {
				$GLOBALS['ISC_CLASS_LOG']->LogSystemError(array('shipping', $this->_name), $this->_deliverytypes[$delivery_type].": " .GetLang('ShippingQuoteError'), $error);
			}
			$this->ResetErrors();
		}
	}
	return $QuoteList;
}
//////////////////////////////////////////////
function GetQuote()
{

$cnpj = $this->GetValue("cnpj");
$num = $this->GetValue("numempresa");
$cep = $this->GetValue("cep");
$tip = $this->GetValue("tipodefrete");
$cpf = "00000000000";
$nitem = count($this->_products);
$destino = $this->_destination_zip;
$dest = str_replace("-","",$destino);

$padrao = $this->GetValue("padrao");
$dimensions = $this->getcombinedshipdimensions();
					$lar = $dimensions['width'];
					$com = $dimensions['length'];
					$alt = $dimensions['height'];
$lar = number_format(ConvertLength($lar, "m"),2);
$com = number_format(ConvertLength($com, "m"),2);
$alt = number_format(ConvertLength($alt, "m"),2);

$novopeso = $lar*$com*$alt*$padrao;

//$peso = (int) number_format(max(ConvertWeight($this->_weight, 'kgs'), 0.1), 1);	
if(!empty($lar)){			
$peso = (int) number_format(max(ConvertWeight($novopeso, 'kgs'), 0.1), 1);
}else{
$peso = (int) nnumber_format(max(ConvertWeight($this->_weight, 'kgs'), 0.1), 1);
}

$pacotes = count($_SESSION['CART']['ITEMS']);

$total = $count = 0;

if (isset($_SESSION['CART']['ITEMS'])) {
	foreach ($_SESSION['CART']['ITEMS'] as $item) {
		$total += $item['product_price'] * $item['quantity'];
	}
}
$valorfinal = number_format($total, 2, '.', '');
$valorfinal = str_replace(",","",$valorfinal);

$unid = count($_SESSION['CART']['ITEMS']);
						
$url = "http://tracking.braspress.com.br/trk/trkisapi.dll/PgCalcFrete_XML?param=$cnpj,$num,$cep,$dest,$cnpj,$cpf,$tip,$peso,$valorfinal,$pacotes";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);
$html = curl_exec ($ch);
curl_close ($ch);

$html = explode('<TOTALFRETE>', $html);
$html2 = explode('</TOTALFRETE>', $html[1]);

$val = $html2[0];

//$val = "1";

if($val != "0") {
$fr_quote = new ISC_SHIPPING_QUOTE($this->GetId(), $this->GetName(), $val, '', 10);
return $fr_quote;
} else {
return false;
}

}
/////////////////////////////////////////////


public function GetAvailableDeliveryMethods()
		{
			$methods = array();

			$this->SetCustomVars();

			$shipClasses = $this->GetValue("shipclasses");
			$carriers = $this->GetValue('carriers');

			if(!is_array($shipClasses) && $shipClasses != "") {
				$shipClasses = array($shipClasses);
			}

			foreach($carriers as $carrier) {
				foreach ($shipClasses as $class) {
					$methods[] = array_search($carrier, $this->_variables['carriers']['options']).' '.array_search($class, $this->_variables['shipclasses']['options']);
				}
			}

			return $methods;
		}
	



}
?>
