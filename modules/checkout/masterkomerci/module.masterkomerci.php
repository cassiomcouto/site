<?php
class CHECKOUT_MASTERKOMERCI extends ISC_CHECKOUT_PROVIDER
{

	/**
	 * @var boolean Does this payment provider require SSL?
	 */
	var $_requiresSSL = false;

	/**
	 *	Checkout class constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_name = "Cartão Mastercard - Redecard";
		$this->_image = "images.jpeg";
		$this->_description = "Modulo de Pagamento Online Mastercard Redecard de Cartões de Creditos.";
		$this->_enabled = $this->CheckEnabled();
		$this->_height = 0;
		$this->_paymenttype = PAYMENT_PROVIDER_OFFLINE;
	}



	public function SetCustomVars()

	{
	
						$this->_variables['displayname'] = array("name" => "Nome",
			   "type" => "textbox",
			   "help" => 'Nome do Modulo',
			   "default" => "Cartão Mastercard",
			   "required" => true
			);
			
		$this->_variables['afi'] = array("name" => 'Filiacao',
		   "type" => "textbox",
		   "help" => 'Ponha seu Numero de Filiacao na Redecard',
		   "default" => "123456",
		   "required" => true
		);

		$this->_variables['juros'] = array("name" => 'Taxa de Juros em %',
		   "type" => "textbox",
		   "help" => 'Ponha a Taxa de Juros em % a Ser Cobrada',
		   "default" => "0",
		   "required" => true
		);
		$this->_variables['parcelamin'] = array("name" => "Parcela Minima",
			   "type" => "textbox",
			   "help" => 'Parcela Minina',
			   "default" => "15",
			   "required" => true
			);
			
	$this->_variables['div'] = array("name" => "Dividir em ate:",
			   "type" => "dropdown",
			   "help" => 'Sera cobrado juros a partir da parcela.',
			   "default" => '6',
			   "options" => array("1x"=>"1","2x"=>"2","3x"=>"3","4x"=>"4","5x"=>"5","6x"=>"6","7x"=>"7","8x"=>"8","9x"=>"9","10x"=>"10","11x"=>"11","12x"=>"12"),
			   "required" => true
			);
			
		
	
		$this->_variables['jurosde'] = array("name" => "Sem Juros ate:",
			   "type" => "dropdown",
			   "help" => 'Sera cobrado juros a partir da parcela.',
			   "default" => '6',
			   "options" => array("Nenhuma"=>"99","1x"=>"1","2x"=>"2","3x"=>"3","4x"=>"4","5x"=>"5","6x"=>"6","7x"=>"7","8x"=>"8","9x"=>"9","10x"=>"10","11x"=>"11","12x"=>"12"),
			   "required" => true
			);
			
					$this->_variables['tipojuros'] = array("name" => "Tipo de Juros",
			   "type" => "dropdown",
			   "help" => '',
			   "default" => '0',
			   "options" => array("Simples"=>"0","Composto"=>"1"),
			   "required" => true
		);		
				}



	public function jurosSimples($valor, $taxa, $parcelas) {
$taxa = $taxa/100;
$m = $valor * (1 + $taxa * $parcelas);
$valParcela = $m/$parcelas;
return $valParcela;
}

public function jurosComposto($valor, $taxa, $parcelas) {
$taxa = $taxa/100;
$valParcela = $valor * pow((1 + $taxa), $parcelas);
$valParcela = $valParcela/$parcelas;
return $valParcela;
}

function getofflinepaymentmessage(){
$order = LoadPendingOrderByToken($_COOKIE['SHOP_ORDER_TOKEN']);
$help = "";		
$valor = $order['ordtotalamount'];
$div = $this->GetValue('div');
$juross = '0';
$taxa = $this->GetValue('juros');
$jt = $this->GetValue('tipojuros');
$pm = $this->GetValue('parcelamin');

if($juross<=0 OR empty($juross)){
$valor = $valor;
} else {
$valor = (($valor/100)*$juross)+$valor;
}

$msg = '';
$msg1 = '';
if($valor<=$pm){
$splitss = 1;
}else{
$splitss = (int) ($valor/$pm);
}

if($splitss<=$div){
$div = $splitss;
}else{
$div = $div;
}

for($j=1; $j<=$div;$j++) {

if($jt==0)
$parcelas = $this->jurosSimples($valor, $taxa, $j);
else
$parcelas = $this->jurosComposto($valor, $taxa, $j);

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');
$op = $this->GetValue('jurosde');

if($op>=$j) {

if($j==1){
$ac = '04';
$jj = '00';
} else {
$ac = '08';
$jj = $j;
}

$msg .="<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/masterkomerci/pagar.php?pedido=".$order['orderid']."&acao=".$ac."&pacela=".$jj."&key=c2VtanVyb3M=','popup','width=800,height=800,scrollbars=yes');void(0);\"><font size='2'><b>".$j."x</b> de <b>".number_format($valors/$j, 2, '.', ',')."</b> sem juros</font></a><br>";
}else{
$msg1 .="<a href=\"javascript:window.open('".$GLOBALS['ShopPath']."/modules/checkout/masterkomerci/pagar.php?pedido=".$order['orderid']."&acao=06&pacela=".$j."&key=Y29tanVyb3M=','popup','width=800,height=800,scrollbars=yes');void(0);\"><font size='2'><b>".$j."x</b> de <b>".number_format($parcelas, 2, '.', ',')."</b> com juros</font></a><br>";
}

}

$help .= "<div class='FloatLeft'><img src='".$GLOBALS['ShopPath']."/modules/checkout/masterkomerci/images/final.gif'><br><br>";

$help .= '<div class="eg-bar">
<table width="100%" border="0">
<tr>
<td width="100%">'.$msg.''.$msg1.'</td>
</tr>
</table>
<br>Clique na parcela desejada acima.<br>
Parcela Minima: <b>'.number_format($pm, 2, '.', ',').'</b>
<!--<b>Link de Repagamento:</b><br><br><br>
<a href='.$GLOBALS["ShopPath"].'/modules/checkout/masterkomerci/repagar.php?pedido='.$order['orderid'].' target="_blank">'.$GLOBALS["ShopPath"].'/modules/checkout/masterkomerci/repagar.php?pedido='.$order['orderid'].'</a><br>-->
</div>';
	

return $help;


}

}
?>
