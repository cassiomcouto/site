<?php

class SHIPPING_DIRECTLOG extends ISC_SHIPPING
{
///////////////////////////////////////////////
function SHIPPING_DIRECTLOG(){
			parent::__construct();
			$this->_name = "DirectLog";
			$this->_image = "log.gif";
			$this->_description = "Meio de Envio por Transportadora DirectLog.";
			$this->_help = "Meio de Envio por Transportadora DirectLog.";
			$this->_enabled = $this->CheckEnabled();
			$this->_countries = array("all");
			$this->_showtestlink = false;
}
//////////////////////////////////////////////
public function SetCustomVars()
{
	$this->_variables['displayname'] = array(
		'name'			=> 'DirectLog',
		'type'		=> 'textbox',
		'help'		=> 'Meio de Envio por Transportadora DirectLog',
		'default'	=> $this->GetName(),
		'savedvalue'	=> array(),
		'required'	=> true
                     );
       $this->_variables['ide'] = array(
		'name'			=> 'Identificação',
		'type'		=> 'textbox',
		'help'		=> 'Meio de Envio por Transportadora DirectLog',
		'default'	=> '0000',
		'savedvalue'	=> array(),
		'required'	=> true
                     );

}
/////////////////////////////////
function GetServiceQuotes()
{
	$QuoteList = array();
	$services = "DirectLog";
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
////////////////////////////////////////////////////////

                        $this->_destzip = $this->_destination_zip;
						
						$ate = str_replace("-", "", $this->_destzip);
                        $this->_peso = number_format(max(ConvertWeight($this->_weight, 'kgs'), 0.1), 1);
////////////////////////////////////////////////////////
$codi = $this->GetValue("ide");
$url = "http://www.directlog.com.br/frete/pega_frete.asp?cdrem=$codi&peso=$this->_peso&cep=$ate&vltot=0";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);
$html = curl_exec ($ch);
curl_close ($ch);
$html1 = str_replace(",", "", $html);
//$html1 = str_replace(".", ",", $html1);
$total =number_format($html1, 2, '.', '');
// Create a quote object
$this->_shippingcost = "$total";

if($html != "0") {
	$fr_quote = new ISC_SHIPPING_QUOTE($this->GetId(), $this->GetName(), $this->_shippingcost, 'Entrega Economica', 0);
 	return $fr_quote;
               } else {
return false;
}
}
/////////////////////////////////////////////
}
?>
