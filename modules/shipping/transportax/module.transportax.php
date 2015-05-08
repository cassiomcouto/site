<?php

class SHIPPING_TRANSPORTAX extends ISC_SHIPPING
{
///////////////////////////////////////////////
			function SHIPPING_TRANSPORTAX(){
			parent::__construct();
			$this->_name = "Transporte da Loja";
			$this->_image = "transportax.gif";
			$this->_description = "O Produto &eacute; enviado pelo transporte da nossa loja";
			$this->_help = "Modulo de Entrega pelo transporte da loja";
			$this->_enabled = $this->CheckEnabled();
			$this->_countries = array("all");
			$this->_showtestlink = false;
}
//////////////////////////////////////////////
public function SetCustomVars()
{
	$this->_variables['displayname'] = array(
		'name'			=> 'Entrega pelo transporte da loja.',
		'type'		=> 'textbox',
		'help'		=> 'Modulo de Entrega em domicilio.',
		'default'	=> $this->GetName(),
		'savedvalue'	=> array(),
		'required'	=> true);

	$this->_variables['valorentrega'] = array(
		'name'			=> 'Preco do frete',
		'type'		=> 'textbox',
		'help'		=> 'Valor da entrega.',
		'default'	=> '0.00',
		'required'	=> true);



}
/////////////////////////////////
function GetServiceQuotes()
{
	$QuoteList = array();
	$services = "Transporte da Loja";
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
function GetQuote(){

$this->_shippingcost = $this->GetValue("valorentrega");

	$fr_quote = new ISC_SHIPPING_QUOTE($this->GetId(), $this->GetName(), $this->_shippingcost, '', 2);
 	return $fr_quote;


}
/////////////////////////////////////////////
}
?>
