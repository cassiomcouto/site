<?php



	if (!defined('ISC_BASE_PATH')) {



		die();



	}







	require_once(ISC_BASE_PATH.'/lib/class.xml.php');







	class ISC_REMOTE extends ISC_XML_PARSER



	{



		public function __construct()



		{



			parent::__construct();



		}







		public function HandleToDo()



		{



			/**



			 * Convert the input character set from the hard coded UTF-8 to their



			 * selected character set



			 */



			convertRequestInput();







			$what = isc_strtolower(@$_REQUEST['w']);







			switch  ($what) {



				case "countrystates": {



					$this->GetCountryStates();



					break;



				}



					case "orcamento": 

					$this->Orcamento();

					break;



				case "getstates": {



					$this->GetStateList();



					break;



				}



				case "getcountries": {



					$this->GetCountryList();



					break;



				}



				case "getexchangerate": {



					$this->GetExchangeRate();



					break;



				}



				case "expresscheckoutregister":



					$this->ExpressCheckoutRegister();



					break;



				case "expresscheckoutlogin":



					$this->ExpressCheckoutLogin();



					break;



				case "expresscheckoutgetaddressfields":



					$this->GetExpressCheckoutAddressFields();



					break;



				case "expresscheckoutgetshippers":



					$this->GetExpressCheckoutShippers();



					break;

		case 'avisar':
				$this->Avisar();
				break;


				case "expresscheckoutshowconfirmation":



					$this->GetExpressCheckoutConfirmation();



					break;



				case "expresscheckoutloadpaymentform":



					$this->GetExpressCheckoutPaymentForm();



					break;



				case "getshippingquotes":



					$this->GetShippingQuotes();



					break;



				case 'selectgiftwrapping':



					$this->SelectGiftWrapping();



					break;



				case 'editconfigurablefieldsincart':



					$this->EditConfigurableFieldsInCart();



					break;



				case 'deleteuploadedfileincart':



					$this->DeleteUploadedFileInCart();



					break;



				case 'addproducts':



					$this->AddProductsToCart();



					break;



				case 'linker':



					$linker = GetClass("ISC_LINKER");



					$linker->HandleToDo();



					break;



				case 'paymentprovideraction':



					$this->ProcessRemoteActions();



					break;



				case 'doadvancesearch':



					$this->doAdvanceSearch();



					break;



				case 'sortadvancesearch':



					$this->sortAdvanceSearch();



					break;



				case 'getvariationoptions':



					$this->GetVariationOptions();



					break;



				case "updatelanguage": {



					$this->UpdateLanguage();



					break;



				}



				case 'disabledesignmode':



					$this->DisableDesignMode();



					break;



					//modificacao



			    case 'simularfrete':



				$this->SimularFrete();



				break;



				case 'simularparcelas':



				$this->SimularParcelas();



				break;



				case 'scroll':



				$this->Scroll();



				break;



				case 'recomendar':



				$this->Recomendar();



				break;



				case 'tags1':



				$this->Tags1();



				break;



				case 'tipopagamento':



				$this->FormasdePagamento();



				break;



			}



		}





public function EmailREOrcamento($produto,$vars)

{



$store_name = GetConfig('StoreName');

$url = GetConfig('ShopPath');	

$ler = "select * from [|PREFIX|]products where productid = '".$produto."'";

$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);

$linhas = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado);

$image = "select * from [|PREFIX|]product_images where imageprodid = '".$linhas['productid']."' and imageisthumb = '1'";

$im = $GLOBALS['ISC_CLASS_DB']->Query($image);

$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);

$ord = rand(1111,99999999);

$html = '<h2>Orcamento de Produto - '.date('d/m/Y').' - #ORC'.$ord.'</h2>

<h2>Dados do Produto</h2>

<b>Nome do Produto: </b>'.$linhas['prodname'].'<br><b>ID: </b>#'.$linhas['productid'].'<br>

<b>Quantidade desejada: </b> '.$vars['qtd'].'<br>

<br>

<h2>Dados do Cliente</h2><br>

<b>Nome: </b> '.$vars['nomede'].'<br>

<b>Email: </b> '.$vars['emailde'].'<br>

<b>Empresa: </b> '.$vars['emp'].'<br>

<b>Telefone: </b> '.$vars['telefone'].'<br>

<b>Cidade: </b> '.$vars['cid'].'<br>

<h2>Mensagem</h2>

<i>'.$vars['obs'].'</i>

<br><br>

<a href="'.GetConfig("ShopPath").'/modificacoes/red.php?is='.$linhas['productid'].'" target="_blank">

'.$url.'/modificacoes/red.php?is='.$linhas['productid'].'

</a>

<br><b>'.$store_name.'</b>';



$email = GetConfig('AdminEmail');

require_once(ISC_BASE_PATH . "/lib/email.php");

$obj_email = GetEmailClass();

$obj_email->Set('CharSet', GetConfig('CharacterSet'));

$obj_email->From($vars['emailde'], $vars['nomede']);

$obj_email->Set("Subject", 'Pedido de Orcamento ID: #ORC'.$ord);

$obj_email->AddBody("html", $html);

$obj_email->AddRecipient($email, "", "h");

$email_result = $obj_email->Send();

if($email_result['success']) {

$result = array("outcome" => "success",

		"message" => '<font color="green">Orcamento enviado com sucesso!!!</font>'

);

} else {

$result = array("outcome" => "fail",

		"message" => '<font color="red">Falha no envio do orcamento!!</font>'

);

}



return $result['message'];

}





public function Orcamento()

		{

		

		$id = $_GET['id'];

//ler dados do produto

$ler = "select * from [|PREFIX|]products where productid = '".$id."'";

$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);

$linhas = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado);

$GLOBALS['Produton'] = $linhas['prodname'];

$GLOBALS['ProdutoIdn'] = $linhas['productid'];

//pega imagem

$image = "select * from [|PREFIX|]product_images where imageprodid = '".$linhas['productid']."' and imageisthumb = '1'";

$im = $GLOBALS['ISC_CLASS_DB']->Query($image);

$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);

$GLOBALS['ImagemG'] = $img['imagefile'];



if(empty($_GET['acao'])) {

$select = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('Orcamento');

echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($select, true);

}else{

//EmailRE($denome,$emailde,$paranome,$paraemail,$produto)

if(!empty($_REQUEST['nomede']) and !empty($_REQUEST['emailde']) and !empty($_REQUEST['telefone']) and !empty($_REQUEST['emp']) and !empty($_REQUEST['cid']) and !empty($_REQUEST['qtd']) and !empty($_REQUEST['obs'])) {

$dados = $this->EmailREOrcamento($GLOBALS['ProdutoIdn'],$_REQUEST);

echo '<h1>'.$dados.'</h1>';

} else {

echo print_r($_REQUEST).'<h2><font color="red">Ponha todos os dados do orcamento!</font></h2>';

}

}

}	







public function FormasdePagamento()



{



$ler = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'tipos' order by variableval asc";



$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);



$i = 1;



$GLOBALS['HTMLFormas'] = "";



while ($s = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {



//echo $s['variableval'];







$GLOBALS['HTMLFormas'] .= "<img src='modificacoes/meios/".$s['variableval'].".jpg' border='0' alt='".$s['variableval']."'>";







if($i%2==0) {



$GLOBALS['HTMLFormas'] .= "<br>";



}







$i++;



}



}







		



		public function Tags1()



		{



		$url = GetConfig('ShopPath');



		$query = "



			SELECT *



			FROM [|PREFIX|]product_tags";



		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



		echo '<tags>';



		while($tag = $GLOBALS['ISC_CLASS_DB']->Fetch($result)){



		echo "<a herf='".$url."/tags/".$tag['tagfriendlyname']."'>".$tag['tagname']."</a>";



		}



		echo '</tags>';







		}



		



			public function EmailRE($denome,$emailde,$paranome,$paraemail,$produto,$men)



		{







			$emails = array();



			$store_name = GetConfig('StoreName');



		$url = GetConfig('ShopPath');	



$ler = "select * from [|PREFIX|]products where productid = '".$produto."'";



$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);



$linhas = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado);



$image = "select * from [|PREFIX|]product_images where imageprodid = '".$linhas['productid']."' and imageisthumb = '1'";



$im = $GLOBALS['ISC_CLASS_DB']->Query($image);



$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);







			$this->_message = 'Ola, <b>'.$paranome.'</b><br>Um amigo lhe recomendou o seguinte produto:<br><b>'.$linhas['prodname'].'<br><img src="'.GetConfig("ShopPath").'/product_images/'.$img['imagefile'].'" width="280" height="180" border="0">



<br>



<i>'.$men.'</i>



<br><br>



<a href="'.GetConfig("ShopPath").'/modificacoes/red.php?is='.$linhas['productid'].'" target="_blank">



'.$url.'/modificacoes/red.php?is='.$linhas['productid'].'



</a><br><b>'.$store_name.'</b>';



			$this->_email = $paraemail;







			if (empty($this->_email)) {



				return;



			}







			$emails = preg_split('#[,\s]+#si', $this->_email, -1, PREG_SPLIT_NO_EMPTY);







			// Create a new email object through which to send the email



			







			require_once(ISC_BASE_PATH . "/lib/email.php");



			$obj_email = GetEmailClass();



			$obj_email->Set('CharSet', GetConfig('CharacterSet'));



			$obj_email->From($emailde, $denome);



			$obj_email->Set("Subject", 'Produto Recomendado por um Amigo - '.$store_name);



			$obj_email->AddBody("html", $this->_message);







			// Add all recipients



			foreach($emails as $email) {



				$obj_email->AddRecipient($email, "", "h");



			}







			$email_result = $obj_email->Send();







			if($email_result['success']) {



				$result = array("outcome" => "success",



								"message" => '<font color="green">Mensagem enviada com sucesso!!!</font>'



				);



			}



			else {



				$result = array("outcome" => "fail",



								"message" => '<font color="red">Falha no envio da mensagem!!</font>'



				);



			}







			return $result;



		}



		//modificacao



		public function Recomendar()



		{



		



		$id = $_GET['id'];



//ler dados do produto



$ler = "select * from [|PREFIX|]products where productid = '".$id."'";



$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);



$linhas = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado);



$GLOBALS['Produton'] = $linhas['prodname'];



$GLOBALS['ProdutoIdn'] = $linhas['productid'];



//pega imagem



$image = "select * from [|PREFIX|]product_images where imageprodid = '".$linhas['productid']."' and imageisthumb = '1'";



$im = $GLOBALS['ISC_CLASS_DB']->Query($image);



$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);



$GLOBALS['ImagemG'] = $img['imagefile'];







if(empty($_GET['acao'])) {



$select = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('Indique');



echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($select, true);



}else{



//EmailRE($denome,$emailde,$paranome,$paraemail,$produto)



if(!empty($_REQUEST['nomede']) and !empty($_REQUEST['emailde']) and !empty($_REQUEST['nomepara']) and !empty($_REQUEST['emailpara']) and !empty($_REQUEST['mensagem'])) {



$dados = $this->EmailRE($_REQUEST['nomede'],$_REQUEST['emailde'],$_REQUEST['nomepara'],$_REQUEST['emailpara'],$_REQUEST['id'],$_REQUEST['mensagem']);



echo '<h1>'.$dados['message'].'</h1>';



} else {



echo '<h2><font color="red">Ponha todos os dados do formulario!</font></h2>';



}



}



}



		



public function Scroll()



{



$GLOBALS['ScrollHTML'] =""; 



$lers = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'scroll'";



$resultados = $GLOBALS['ISC_CLASS_DB']->Query($lers);



$ss = $GLOBALS['ISC_CLASS_DB']->Fetch($resultados);



switch($ss['variableval']) {



case 'html';



$GLOBALS['ScrollHTML'] .=  '<script type="text/javascript" src="modificacoes/jquery-1.2.2.pack.js"></script>



<link rel="stylesheet" type="text/css" href="modificacoes/featuredcontentglider.css" />



<script type="text/javascript" src="modificacoes/featuredcontentglider.js"></script>



<script type="text/javascript">



featuredcontentglider.init({



	gliderid: "canadaprovinces", //ID of main glider container



	contentclass: "glidecontent", //Shared CSS class name of each glider content



	togglerid: "p-select", //ID of toggler container



	remotecontent: "", //Get gliding contents from external file on server? "filename" or "" to disable



	selected: 0, //Default selected content index (0=1st)



	persiststate: false, //Remember last content shown within browser session (true/false)?



	speed: 700, //Glide animation duration (in milliseconds)



	direction: "rightleft", //set direction of glide: "updown", "downup", "leftright", or "rightleft"



	autorotate: true, //Auto rotate contents (true/false)?



	autorotateconfig: [3000, 20000] //if auto rotate enabled, set [milliseconds_btw_rotations, cycles_before_stopping]



})



</script>



<div id="canadaprovinces" class="glidecontentwrapper">';







$query = sprintf("select * from [|PREFIX|]products where prodfeatured = '1' ORDER BY rand() LIMIT 0,10");



$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



$i = 1;



while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {







$GLOBALS['ScrollHTML'] .= '<div class="glidecontent"><table><tr align=center>';







$image = sprintf("select * from [|PREFIX|]product_images where imageprodid = '".$row['productid']."' and imageisthumb = '1'");



$im = $GLOBALS['ISC_CLASS_DB']->Query($image);



$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);







$url = ProdLink($row['prodname']);







$GLOBALS['ScrollHTML'] .= "<td width=200 valign=middle><center><img src='miniatura.php?w=120&img=product_images/".$img['imagefile']."'></center></td><td width=100% align=center valign=middle><font face=arial size=5><a href='".$url."' target='_parent'>".$row['prodname']."</font></a><br><font face=arial size=4>Por Apenas: ".CurrencyConvertFormatPrice($row['prodcalculatedprice'], 1, 0)."</font></td>";











$GLOBALS['ScrollHTML'] .= "</tr></table></div>";



$i++;



}







$GLOBALS['ScrollHTML'] .=  '</div><div id="p-select" class="glidecontenttoggler"><a href="#" class="prev"><<</a>';







for($j=1;$j<$i;$j++) {



$GLOBALS['ScrollHTML'] .=  '<a href="#" class="toc">'.$j.'</a>';



}







$GLOBALS['ScrollHTML'] .= '<a href="#" class="next">>></a></div>';







break;



case 'flash';



$GLOBALS['ScrollHTML'] .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="scroll.swf" type="application/x-shockwave-flash" height="100%" width="100%">



<param name="movie" value="modificacoes/scroll.swf">



<param value="false" name="menu">



<param value="FFFFFF" name="bgcolor">



<param value="host=modificacoes/feed.php" name="flashvars">



<embed src="modificacoes/scroll.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="host=modificacoes/feed.php" type="application/x-shockwave-flash" wmode="transparent" height="100%" width="100%">



</object>';



break;



case 'nao';



break;







}



//imprime no html



$select = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('Scroll');



echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($select, true);



}



public function SimularFrete(){



//pega o produto	



$id = $_GET['id'];



//ler dados do produto



$ler = "select * from [|PREFIX|]products where productid = '".$id."'";



$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);



$linhas = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado);



$GLOBALS['Produto'] = $linhas['prodname'];



$GLOBALS['ProdutoId'] = $linhas['productid'];



$GLOBALS['Valor'] = number_format($linhas['prodcalculatedprice'], 2, '.', '');



$GLOBALS['Peso'] = number_format(max(ConvertWeight($linhas['prodweight'], 'kgs'), 0.1), 1);



//pega imagem



$image = "select * from [|PREFIX|]product_images where imageprodid = '".$linhas['productid']."' and imageisthumb = '1'";



$im = $GLOBALS['ISC_CLASS_DB']->Query($image);



$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);



$GLOBALS['Imagem'] = $img['imagefile'];



//pega a localizacao do cep



$GLOBALS['CepOrigem'] = GetConfig('CompanyZip');



$url = "http://www.mdconline.com.br/Webservices/WSCEP/servicoCEP.asp?txtCEPEnviado=".$GLOBALS['CepOrigem'];



$ch = curl_init();



curl_setopt($ch, CURLOPT_URL, $url);



curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 2);



$html1 = curl_exec ($ch);



curl_close ($ch);



$html = explode('<CIDADE>', $html1);



$html2 = explode('</CIDADE>', $html[1]);



$htmld = explode('<UF>', $html1);



$htmld2 = explode('</UF>', $htmld[1]);



$GLOBALS['Origem'] = $html2[0]." - ".$htmld2[0];



//aplica no template



$select = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('SimularFrete');



echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($select, true);



		}



		



		public function ValorProduto($produto) {



		$query = "SELECT * FROM [|PREFIX|]products where productid=".$produto;



		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



		$a = $GLOBALS['ISC_CLASS_DB']->Fetch($result);



		$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');



        $g = $GLOBALS['ISC_CLASS_CUSTOMER']->GetCustomerGroup();



		$valor = $a['prodcalculatedprice']-(($a['prodcalculatedprice']/100)*$g['discount']);



        return $valor;



		}



		
public function Avisar()
{
@$GLOBALS['ISC_CLASS_DB']->Query("CREATE TABLE IF NOT EXISTS `loja_avisar` (
  `id` int(6) NOT NULL auto_increment,
  `produto` int(6) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `data` varchar(20) NOT NULL,
  `avisado` enum('S','N') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;");
////////////////////////////////////
$r = $GLOBALS['ISC_CLASS_DB']->Query('SELECT * FROM `loja_avisar` WHERE produto = "'.$_POST['id_produto'].'" AND email = "'.$_POST['email'].'"');
$dados = $GLOBALS['ISC_CLASS_DB']->Fetch($r);
if(empty($dados['email'])){
///////////////////////////////////
$image = "INSERT INTO `loja_avisar` (
`id` ,
`produto` ,
`nome` ,
`email` ,
`data` ,
`avisado`
)
VALUES (
NULL , '".$_POST['id_produto']."', '".$_POST['nome']."', '".$_POST['email']."', '".time()."', 'N'
);
";
@$GLOBALS['ISC_CLASS_DB']->Query($image);
echo "<b>Seu contato foi registrado com sucesso!</b><br>Quando o produto estiver novamente disponível você receberá uma notificação.";
}else{
echo "<b>Email já cadastrado para o produto!</b><br>";
}
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



		



		public function SimularParcelas()



		{







$mos = GetModuleVariable('addon_parcelas','loginparapreco');

if($mos=='nao'){



$customerClass = GetClass('ISC_CUSTOMER');

if(!$customerClass->GetCustomerId()) {

die('Somente Clientes logados!');

}



}





//inicio da funcao



$produto = $_GET['id'];



$ler = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'tipos' order by variableval asc";



$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);



$i = 1;



$GLOBALS['HTML'] = "";



while ($s = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {



//inicio do switch



switch($s['variableval']) {







case 'deposito': //deposito



$ativo = GetModuleVariable('checkout_deposito','is_setup');



$desc = GetModuleVariable('checkout_deposito','desconto');



$nome = GetModuleVariable('checkout_deposito','displayname');



if(!empty($ativo)) {



//verifica o desconto



$pro = $this->ValorProduto($produto);



if($desc<=0 OR empty($desc)){



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg = "<b> ".$preco." a vista.";



} else {



$valven = ($pro/100)*$desc;



$preco = CurrencyConvertFormatPrice($pro-$valven, 1, 0);



$msg = "<b> ".$preco."</b> a vista com <b>".$desc."%</b> de desconto.";



}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/deposito.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break; // fim deposito







case 'cheque':



$ativo = GetModuleVariable('checkout_cheque','is_setup');



$juros = GetModuleVariable('checkout_cheque','juros');



$nome = GetModuleVariable('checkout_cheque','displayname');



$div = GetModuleVariable('checkout_cheque','dividir');



$jde = GetModuleVariable('checkout_cheque','jurosde');



$pmin = GetModuleVariable('checkout_cheque','parmin');



if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



if($juros<=0 OR empty($juros)){



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg = "<b> ".$preco."</b> a vista.";



} else {



$msg = '';



$msg1 = '';



$splits = (int) ($pro/$pmin);



if($splits<=$div){



$div = $splits;



}else{



$div = $div;



}



for ($j=1;$j<=$div;$j++) {



if ($jde<=$j and $jde<='50') {



$valven = ($pro/100)*$juros;



$msg1 .= $j."x de <b>".CurrencyConvertFormatPrice(($pro+$valven)/$j, 1, 0)."</b> com juros.<br>";



}else{



$msg .= $j."x de <b>".CurrencyConvertFormatPrice($pro/$j, 1, 0)."</b> sem juros.<br>";



}



}



}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/cheque.gif" /></td>



<td width="40%"><font size="2">'.$msg.'</font></td>



<td width="40%"><font size="2">'.$msg1.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'boleto': //boleto



$desc = GetModuleVariable('addon_parcelas','descboleto');







//verifica o desconto



$pro = $this->ValorProduto($produto);



if($desc<=0){



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg = "<b> ".$preco."</b> a vista.";



} else {



$valven = ($pro/100)*$desc;



$preco = CurrencyConvertFormatPrice($pro-$valven, 1, 0);



$msg = "<b> ".$preco."</b> a vista com <b>".$desc."%</b> de desconto.";



}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>Boleto Bancario</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/boleto.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



break; // fim boleto







case 'pagseguro':



$ativo = GetModuleVariable('checkout_pagseguro','is_setup');



$juross = GetModuleVariable('checkout_pagseguro','acrecimo');



$nome = GetModuleVariable('checkout_pagseguro','displayname');



$taxa = 0.0199;



if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/5);



if($splitss<=12){



$div = $splitss;



}else{



$div = 12;



}



//echo $div."<br>";



for($j=1; $j<=$div;$j++) {



$cf = pow((1 + $taxa), $j);



$cf = (1 / $cf);



$cf = (1 - $cf);



$cf = ($taxa / $cf);



//echo $cf."<br>";



$parcelas = ($valor*$cf);



//echo $parcela."<br>";



$parcelas = number_format($parcelas, 2, '.', '');



//echo $parcela."<br>";



$valors = number_format($valor, 2, '.', '');



$op = GetModuleVariable('checkout_pagseguro','jurosde');



if($j==1 OR $op>=$j) {



$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros.<br>";



}else{



$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros.<br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/pagseguro.gif" /></td>



<td width="40%"><font size="2">'.$msg.'</font></td>



<td width="40%"><font size="2">'.$msg1.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;



case 'mercadopago':
$ativo = GetModuleVariable('checkout_mercadopago','is_setup');
$juross = GetModuleVariable('checkout_mercadopago','acrecimo');
$nome = GetModuleVariable('checkout_mercadopago','displayname');
$taxa = 0.0199;
if(!empty($ativo)) {
//verifica o juros
$pro = $this->ValorProduto($produto);
$valor = $pro;
if($juross<=0 OR empty($juross)){
$valor = $valor;
} else {
$valor = (($valor/100)*$juross)+$valor;
}

$msg = '';
$msg1 = '';
$splitss = (int) ($valor/10);
if($splitss<=18){
$div = $splitss;
}else{
$div = 18;
}

for($j=1; $j<=$div;$j++) {
$splitss = $j;
if($splitss<=18){
/////////////////
if($splitss==0 OR $splitss==1 OR $splitss==2){
$juros = 1;
$divs = 1;
}
if($splitss==3 OR $splitss==4 OR $splitss==5){
$juros = 0.3533;
$divs = 3;
}
if($splitss==6 OR $splitss==7 OR $splitss==8){
$juros = 0.18081;
$divs = 6;
}
if($splitss==9 OR $splitss==10 OR $splitss==11){
$juros = 0.123877;
$divs = 9;
}
if($splitss==12 OR $splitss==13 OR $splitss==14){
$juros = 0.094575;
$divs = 12;
}
if($splitss==15 OR $splitss==16 OR $splitss==17){
$juros = 0.077993;
$divs = 15;
}
if($splitss==18 OR $splitss==19 OR $splitss==20){
$juros = 0.066661;
$divs = 18;
}
/////////////////
}else{
$juros = 0.066661;
$divs = 18;
}

$parcelas = number_format($parcelas, 2, '.', '');
$valors = number_format($valor, 2, '.', '');

//$op = GetModuleVariable('checkout_mercadopago','jurosde');
if($j==1) {
$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors, 1, 0)."</b> sem juros.<br>";
}else if($j==$divs){
$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors*$juros, 1, 0)."</b> com juros.<br>";
}

}
//inicio do codigo do parcelamento
$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>MercadoPago Pagamentos</div>
<div id="faq'.$i.'" class="icongroup1">
<table width="100%" border="0">
<tr>
<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/mercadopago.gif" /></td>
<td width="40%"><font size="2">'.$msg.'</font></td>
<td width="40%"><font size="2">'.$msg1.'</font></td>
</tr>
</table>
</div>';
//fim do codigo de parcelamento
}
break;

case 'cielo':
$ativo = GetModuleVariable('checkout_cielo','is_setup');
$nome = 'Cat&atilde;o de Cr&eacute;dito';
$div = GetModuleVariable('checkout_cielo','div');
$juross = '0';
$taxa = GetModuleVariable('checkout_cielo','juros');

$jt = 0;

$pm = GetModuleVariable('checkout_cielo','parcelamin');

if(!empty($ativo)) {
//verifica o juros
$pro = $this->ValorProduto($produto);
$valor = $pro;
if($juross<=0 OR empty($juross)){
$valor = $valor;
} else {
$valor = (($valor/100)*$juross)+$valor;
}

$msg = '';
$msg1 = '';
$splitss = (int) ($valor/$pm);
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

$op = GetModuleVariable('checkout_cielo','jurosde');
if($op>=$j) {
$msg .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> no <b>cart&atilde;o de credito</b>.</font><br>";
}else{
$msg1 .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> (<u>".$parcelas*$j."</u>) no <b>cart&atilde;o de credito</b>.</font><br>";
}

}
//inicio do codigo do parcelamento
$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>Cart&atilde;o de Credito</div>
<div id="faq'.$i.'" class="icongroup1">
<table width="100%" border="0">
<tr>
<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/modulo_cielo.gif" /></td>
<td width="40%">'.$msg.'</td>
<td width="50%">'.$msg1.'</td>
</tr>
</table>
</div>';
//fim do codigo de parcelamento
}
break;


case 'bcash':



$ativo = GetModuleVariable('checkout_bcash','is_setup');



$juross = GetModuleVariable('checkout_bcash','acrecimo');



$nome = GetModuleVariable('checkout_bcash','displayname');



$taxa = 0.0199;



if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/5);



if($splitss<=12){



$div = $splitss;



}else{



$div = 12;



}







for($j=1; $j<=$div;$j++) {











$parcelas = $this->jurosComposto($valor, 1.99, $j);







$parcelas = number_format($parcelas, 2, '.', '');



$valors = number_format($valor, 2, '.', '');







$op = GetModuleVariable('checkout_bcash','jurosde');



if($j==1 OR $op>=$j) {



$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros.<br>";



}else{



$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros.<br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/pagdigital.gif" /></td>



<td width="40%"><font size="2">'.$msg.'</font></td>



<td width="40%"><font size="2">'.$msg1.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'moip':



$ativo = GetModuleVariable('checkout_moip','is_setup');



$juross = GetModuleVariable('checkout_moip','acrecimo');



$nome = GetModuleVariable('checkout_moip','displayname');



$taxa = 0.0199;



if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/5);



if($splitss<=12){



$div = $splitss;



}else{



$div = 12;



}



//echo $div."<br>";



for($j=1; $j<=$div;$j++) {



$cf = pow((1 + $taxa), $j);



$cf = (1 / $cf);



$cf = (1 - $cf);



$cf = ($taxa / $cf);



//echo $cf."<br>";



$parcelas = ($valor*$cf);



//echo $parcela."<br>";



$parcelas = number_format($parcelas, 2, '.', '');



//echo $parcela."<br>";



$valors = number_format($valor, 2, '.', '');



$op = GetModuleVariable('checkout_moip','jurosde');



if($j==1 OR $op>=$j) {



$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros.<br>";



}else{



$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros.<br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/moip.gif" /></td>



<td width="40%"><font size="2">'.$msg.'</font></td>



<td width="40%"><font size="2">'.$msg1.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'dinheiromail':



$ativo = GetModuleVariable('checkout_dinheiromail','is_setup');



$juross = GetModuleVariable('checkout_dinheiromail','acrecimo');



$nome = GetModuleVariable('checkout_dinheiromail','displayname');



$taxa = 0.0199;



if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/5);



if($splitss<=12){



$div = $splitss;



}else{



$div = 12;



}







for($j=1; $j<=$div;$j++) {











$parcelas = $this->jurosSimples($valor, 1.99, $j);







$parcelas = number_format($parcelas, 2, '.', '');



$valors = number_format($valor, 2, '.', '');







$op = GetModuleVariable('checkout_dinheiromail','jurosde');



if($j==1 OR $op>=$j) {



$msg .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros.<br>";



}else{



$msg1 .="<b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> com juros.<br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/dinmail.png" /></td>



<td width="40%"><font size="2">'.$msg.'</font></td>



<td width="40%"><font size="2">'.$msg1.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'paypal':



$ativo = GetModuleVariable('checkout_paypalbrasil','is_setup');



$desc = GetModuleVariable('checkout_paypalbrasil','desconto');



$nome = GetModuleVariable('checkout_paypalbrasil','displayname');



if(!empty($ativo)) {



//verifica o desconto



$pro = $this->ValorProduto($produto);



if($desc<=0 OR empty($desc)){



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg = "<b> ".$preco." a vista.";



} else {



$valven = ($pro/100)*$desc;



$preco = CurrencyConvertFormatPrice($pro-$valven, 1, 0);



$msg = "<b> ".$preco."</b> a vista com <b>".$desc."%</b> de desconto.";



}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/paypal.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'visacredito':



$ativo = GetModuleVariable('checkout_visanet','is_setup');



$nome = GetModuleVariable('checkout_visanet','displayname');



$div = GetModuleVariable('checkout_visanet','div');



$juross = '0';



$taxa = GetModuleVariable('checkout_visanet','juros');



$jt = GetModuleVariable('checkout_visanet','tipojuros');







$pm = GetModuleVariable('checkout_visanet','parcelamin');







if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/$pm);



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







$op = GetModuleVariable('checkout_visanet','jurosde');



if($op>=$j) {



$msg .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros.</font><br>";



}else{



$msg1 .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> (<u>".$parcelas*$j."</u>) com juros.</font><br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>Cartão Visa</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/cartao_visa.gif" /></td>



<td width="40%">'.$msg.'</td>



<td width="50%">'.$msg1.'</td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'visadebito':



$ativo = GetModuleVariable('checkout_visanet','is_setup');



$nome = GetModuleVariable('checkout_visanet','displayname');



$desc = GetModuleVariable('checkout_visanet','desconto');







if(!empty($ativo)) {



//verifica o desconto



$pro = $this->ValorProduto($produto);



if($desc<=0 OR empty($desc)){



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg = "<b> ".$preco." a vista.";



} else {



$valven = ($pro/100)*$desc;



$preco = CurrencyConvertFormatPrice($pro-$valven, 1, 0);



$msg = "<b> ".$preco."</b> a vista com <b>".$desc."%</b> de desconto.";



}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>Visa Electron</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/cartao_visa_electron.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'master':



$ativo = GetModuleVariable('checkout_mastercard','is_setup');



$nome = GetModuleVariable('checkout_mastercard','displayname');



$div = GetModuleVariable('checkout_mastercard','div');



$juross = '0';



$taxa = GetModuleVariable('checkout_mastercard','juros');



$jt = GetModuleVariable('checkout_mastercard','tipojuros');







$pm = GetModuleVariable('checkout_mastercard','parcelamin');







if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/$pm);



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







$op = GetModuleVariable('checkout_mastercard','jurosde');



if($op>=$j) {



$msg .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> sem juros.</font><br>";



}else{



$msg1 .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> (<u>".$parcelas*$j."</u>) com juros.</font><br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/cartao_mastercard.gif" /></td>



<td width="40%">'.$msg.'</td>



<td width="50%">'.$msg1.'</td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'dinners':



$ativo = GetModuleVariable('checkout_dinners','is_setup');



$nome = GetModuleVariable('checkout_dinners','displayname');



$div = GetModuleVariable('checkout_dinners','div');



$juross = '0';



$taxa = GetModuleVariable('checkout_dinners','juros');



$jt = GetModuleVariable('checkout_dinners','tipojuros');







$pm = GetModuleVariable('checkout_dinners','parcelamin');







if(!empty($ativo)) {



//verifica o juros



$pro = $this->ValorProduto($produto);



$valor = $pro;



if($juross<=0 OR empty($juross)){



$valor = $valor;



} else {



$valor = (($valor/100)*$juross)+$valor;



}







$msg = '';



$msg1 = '';



$splitss = (int) ($valor/$pm);



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







$op = GetModuleVariable('checkout_dinners','jurosde');



if($op>=$j) {



$msg .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($valors/$j, 1, 0)."</b> s/sem juros.</font><br>";



}else{



$msg1 .="<font size='2'><b>".$j."x</b> de <b>".CurrencyConvertFormatPrice($parcelas, 1, 0)."</b> (<u>".$parcelas*$j."</u>) com juros.</font><br>";



}







}



//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/cartao_diners.gif" /></td>



<td width="40%">'.$msg.'</td>



<td width="50%">'.$msg1.'</td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







case 'sps':



$ativo = GetModuleVariable('checkout_spsbradesco','is_setup');



$nome = GetModuleVariable('checkout_spsbradesco','displayname');



$boleto = GetModuleVariable('checkout_spsbradesco','pagboletos');



$facil = GetModuleVariable('checkout_spsbradesco','pagfacil');



$finan = GetModuleVariable('checkout_spsbradesco','pagfinan');



$trans = GetModuleVariable('checkout_spsbradesco','pagtrans');



if(!empty($ativo)) {



//verifica o desconto



$pro = $this->ValorProduto($produto);



$msg = "";



if($boleto=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista no boleto.<br>";



}



if($facil=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista por cartão de debito.<br>";



}



if($finan=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> financiado em até <b>24x</b> (com juros do banco).<br>";



}



if($trans=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista por transferência bancaria.<br>";



}











//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/spsbradesco.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;











case 'bbofice':



$ativo = GetModuleVariable('checkout_bbcomercio','is_setup');



$nome = GetModuleVariable('checkout_bbcomercio','displayname');



$boleto = '';



$facil = '';



$finan = '';



$trans = '';



if(!empty($ativo)) {



//verifica o desconto



$pro = $this->ValorProduto($produto);



$msg = "";



if($boleto=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista no boleto.<br>";



}



if($facil=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista por cartão de debito.<br>";



}



if($trans=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista por transferência bancaria.<br>";



}











//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/bb_commerce.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;











case 'shopline':



$ativo = GetModuleVariable('checkout_itaushopline','is_setup');



$nome = GetModuleVariable('checkout_itaushopline','displayname');



$boleto = '';



$facil = '';



$finan = '';



$trans = '';



if(!empty($ativo)) {



//verifica o desconto



$pro = $this->ValorProduto($produto);



$msg = "";



if($boleto=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista no boleto.<br>";



}



if($facil=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista por cartão de debito.<br>";



}



if($trans=="") {



$preco = CurrencyConvertFormatPrice($pro, 1, 0);



$msg .= "<b> ".$preco."</b> a vista por transferência bancaria.<br>";



}











//inicio do codigo do parcelamento



$GLOBALS['HTML'] .= '<div class="eg-bar"><span id="faq'.$i.'-title" class="iconspan"><img src="%%GLOBAL_ShopPath%%/modificacoes/minus.gif" /></span>'.$nome.'</div>



<div id="faq'.$i.'" class="icongroup1">



<table width="100%" border="0">



<tr>



<td width="10%"><img src="%%GLOBAL_ShopPath%%/modificacoes/itau_shopline.gif" /></td>



<td width="80%" colspan="2"><font size="2">'.$msg.'</font></td>



</tr>



</table>



</div>';



//fim do codigo de parcelamento



}



break;







}



$i++;



//fim do switch



}



//aplica no template



$select = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('SimularParcela');



echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($select, true);



}







		public function DisableDesignMode()



		{



			isc_unsetCookie('designModeToken');



			exit;



		}







		public function DeleteUploadedFileInCart()



		{



			if(!isset($_REQUEST['item']) || !isset($_REQUEST['field'])) {



				return false;



			}







			$itemId = $_REQUEST['item'];



			$fieldId = $_REQUEST['field'];







			if(isset($_SESSION['CART']['ITEMS'][$itemId]['product_fields'][$fieldId]['fileName'])) {



				$field = $_SESSION['CART']['ITEMS'][$itemId]['product_fields'];







				@unlink(ISC_BASE_PATH.'/'.GetConfig('ImageDirectory').'/configured_products/'.$field[$fieldId]['fileName']);







				foreach($field[$fieldId] as $key => $value) {



					unset($_SESSION['CART']['ITEMS'][$itemId]['product_fields'][$fieldId][$key]);



				}



				unset($_SESSION['CART']['ITEMS'][$itemId]['product_fields'][$fieldId]);



			}



		}







		public function EditConfigurableFieldsInCart()



		{



			if(!isset($_REQUEST['itemid'])) {



				return false;



			}







			$itemId = (int)$_REQUEST['itemid'];



			$output = '';



			$cartItem = $_SESSION['CART']['ITEMS'][$itemId];



			$cartItemFields = $_SESSION['CART']['ITEMS'][$itemId]['product_fields'];







			$GLOBALS['ItemId'] = $itemId;



			$GLOBALS['ISC_CLASS_PRODUCT'] = GetClass('ISC_PRODUCT');







			$GLOBALS['CartProductName'] = isc_html_escape($cartItem['product_name']);



			$fields = $GLOBALS['ISC_CLASS_PRODUCT']->GetProductFields($cartItem['product_id']);







			foreach($fields as $field) {







				$GLOBALS['ProductFieldType'] = isc_html_escape($field['type']);



				$GLOBALS['ProductFieldId'] = (int)$field['id'];



				$GLOBALS['ProductFieldName'] = isc_html_escape($field['name']);



				$GLOBALS['ProductFieldRequired'] = '';



				$GLOBALS['FieldRequiredClass'] = '';



				$GLOBALS['ProductFieldValue'] = '';



				$GLOBALS['ProductFieldFileValue'] = '';



				$GLOBALS['HideCartFileName'] = 'display: none';



				$GLOBALS['CheckboxFieldNameLeft'] = '';



				$GLOBALS['CheckboxFieldNameRight'] = '';



				$GLOBALS['HideDeleteFileLink'] = 'display: none';



				$GLOBALS['HideFileHelp'] = "display:none";







				$cartItemField = array(



					"fieldType" => '',



					"fieldName" => '',



					"fileType" => '',



					"fileOriginName" => '',



					"fileName" => '',



					"fieldValue" => '',



				);



				if(isset($cartItemFields[$field['id']])) {



					$cartItemField = $cartItemFields[$field['id']];



				}







				$snippetFile = 'ProductFieldInput';







				switch ($field['type']) {



					case 'textarea': {



						$GLOBALS['ProductFieldValue'] = isc_html_escape($cartItemField['fieldValue']);



						$snippetFile = 'ProductFieldTextarea';



						break;



					}



					case 'file': {



						$fieldValue = isc_html_escape($cartItemField['fileOriginName']);



						$GLOBALS['HideDeleteCartFieldFile'] = '';



						$GLOBALS['CurrentProductFile'] = $fieldValue;



						$GLOBALS['ProductFieldFileValue'] = $fieldValue;



						$GLOBALS['HideFileHelp'] = "";



						$GLOBALS['FileSize'] = NiceSize($field['fileSize']*1024);







						if($fieldValue != '') {



							$GLOBALS['HideCartFileName'] = '';



						}







						if(!$field['required']) {



							$GLOBALS['HideDeleteFileLink'] = '';



						}



						$GLOBALS['FileTypes'] = isc_html_escape($field['fileType']);



						break;



					}



					case 'checkbox': {



						$GLOBALS['CheckboxFieldNameLeft'] = $GLOBALS['ProductFieldName'];



						if($cartItemField['fieldValue'] == 'on') {



							$GLOBALS['ProductFieldValue'] = 'checked';



						}



						$snippetFile = 'ProductFieldCheckbox';



						break;



					}



					default: {



						$GLOBALS['ProductFieldValue'] = isc_html_escape($cartItemField['fieldValue']);



						break;



					}



				}







				if($field['required']) {



					$GLOBALS['ProductFieldRequired'] = '<span class="Required">*</span>';



					$GLOBALS['FieldRequiredClass'] = 'FieldRequired';



				}



				$output .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('Cart'.$snippetFile);



			}



			$GLOBALS['SNIPPETS']['ProductFieldsList'] = $output;







			$editProductFields = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartEditProductFieldsForm');



			echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($editProductFields, $GLOBALS['SNIPPETS']);



		}







		public function SelectGiftWrapping()



		{



			$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');



			$cartProducts = $GLOBALS['ISC_CLASS_CART']->api->GetProductsInCart();







			if(!isset($_REQUEST['itemId']) || !isset($cartProducts[$_REQUEST['itemId']])) {



				exit;



			}







			$cartProduct = $cartProducts[$_REQUEST['itemId']];







			$GLOBALS['GiftWrappingTitle'] = sprintf(GetLang('GiftWrappingForX'), isc_html_escape($cartProduct['product_name']));



			$GLOBALS['ProductName'] = $cartProduct['product_name'];



			$GLOBALS['ItemId'] = (int)$_REQUEST['itemId'];







			// Get the available gift wrapping options for this product



			if($cartProduct['data']['prodwrapoptions'] == -1) {



				exit;



			}



			else if($cartProduct['data']['prodwrapoptions'] == 0) {



				$giftWrapWhere = "wrapvisible='1'";



			}



			else {



				$wrapOptions = implode(',', array_map('intval', explode(',', $cartProduct['data']['prodwrapoptions'])));



				$giftWrapWhere = "wrapid IN (".$wrapOptions.")";



			}



			$query = "



				SELECT *



				FROM [|PREFIX|]gift_wrapping



				WHERE ".$giftWrapWhere."



				ORDER BY wrapname ASC



			";



			$wrappingOptions = array();



			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



			while($wrap = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {



				$wrappingOptions[$wrap['wrapid']] = $wrap;



			}







			// This product is already wrapped, select the existing value



			$selectedWrapping = 0;



			$GLOBALS['GiftWrapMessage'] = '';



			if(isset($cartProduct['wrapping'])) {



				$selectedWrapping = $cartProduct['wrapping']['wrapid'];



			}







			if(isset($cartProduct['wrapping']['wrapmessage'])) {



				$GLOBALS['GiftWrapMessage'] = isc_html_escape($cartProduct['wrapping']['wrapmessage']);



			}







			$GLOBALS['HideGiftWrapMessage'] = 'display: none';







			// Build the list of wrapping options



			$GLOBALS['WrappingOptions'] = '';



			$GLOBALS['GiftWrapPreviewLinks'] = '';



			foreach($wrappingOptions as $option) {



				$sel = '';



				if($selectedWrapping == $option['wrapid']) {



					$sel = 'selected="selected"';



					if($option['wrapallowcomments']) {



						$GLOBALS['HideGiftWrapMessage'] = '';



					}



				}



				$classAdd = '';



				if($option['wrapallowcomments']) {



					$classAdd = 'AllowComments';



				}







				if($option['wrappreview']) {



					$classAdd .= ' HasPreview';



					$previewLink = GetConfig('ShopPath').'/'.GetConfig('ImageDirectory').'/'.$option['wrappreview'];



					if($sel) {



						$display = '';



					}



					else {



						$display = 'display: none';



					}



					$GLOBALS['GiftWrapPreviewLinks'] .= '<a id="GiftWrappingPreviewLink'.$option['wrapid'].'" class="GiftWrappingPreviewLinks" target="_blank" href="'.$previewLink.'" style="'.$display.'">'.GetLang('Preview').'</a>';



				}







				$GLOBALS['WrappingOptions'] .= '<option class="'.$classAdd.'" value="'.$option['wrapid'].'" '.$sel.'>'.isc_html_escape($option['wrapname']).' ('.CurrencyConvertFormatPrice($option['wrapprice']).')</option>';



			}







			if($cartProduct['quantity'] > 1) {



				$GLOBALS['ExtraClass'] = 'PL40';



				$GLOBALS['GiftWrapModalClass'] = 'SelectGiftWrapMultiple';



				$GLOBALS['SNIPPETS']['GiftWrappingOptions'] = '';



				for($i = 1; $i <= $cartProduct['quantity']; ++$i) {



					$GLOBALS['GiftWrappingId'] = $i;



					$GLOBALS['SNIPPETS']['GiftWrappingOptions'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('GiftWrappingWrapOptions');



				}



			}



			else {



				$GLOBALS['HideSplitWrappingOptions'] = 'display: none';



			}







			$GLOBALS['HideWrappingTitle']		= 'display: none';



			$GLOBALS['HideWrappingSeparator']	= 'display: none';



			$GLOBALS['GiftWrappingId'] = 'all';



			$GLOBALS['SNIPPETS']['GiftWrappingOptionsSingle'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('GiftWrappingWrapOptions');







			$selectWrapping = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('SelectGiftWrapping');



			echo $GLOBALS['ISC_CLASS_TEMPLATE']->ParseSnippets($selectWrapping, $GLOBALS['SNIPPETS']);



		}







		/**



		 * Validate the registration information for a customer registering an account using the express checkout.



		 *



		 * @param boolean Set to true to simply return true instead of spitting out a success message.



		 */



		private function ExpressCheckoutRegister($return=false)



		{



			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');







			// Check that a customer doesn't already exist with this email address



			if(!isset($_POST['billing_EmailAddress']) || $_POST['billing_EmailAddress'] == '') {



				$tags[] = $this->MakeXMLTag('status', 0);



				$tags[] = $this->MakeXMLTag('message', GetLang('AccountEnterValidEmail'), true);



				$tags[] = $this->MakeXMLTag('focus', '#account_email');



				$tags[] = $this->MakeXMLTag('step', 'BillingAddress');



			}



			else if ($GLOBALS['ISC_CLASS_CUSTOMER']->AccountWithEmailAlreadyExists($_POST['billing_EmailAddress'])) {



				$tags[] = $this->MakeXMLTag('status', 0);



				$tags[] = $this->MakeXMLTag('message', sprintf(GetLang('AccountUpdateEmailTaken'), $_POST['billing_EmailAddress']), true);



				$tags[] = $this->MakeXMLTag('focus', '#account_email');



				$tags[] = $this->MakeXMLTag('step', 'BillingAddress');



			}







			if(!empty($tags)) {



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			if($return) {



				return;



			}



			else {



				$tags[] = $this->MakeXMLTag('status', 1);



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}



		}







		/**



		 * Fetch the address entry fields for a guest when using the express checkout.



		 */



		private function GetExpressCheckoutAddressFields()



		{



			$GLOBALS['ISC_CLASS_CHECKOUT'] = GetClass('ISC_CHECKOUT');







			// If the customer was logged in - they've just said they're checking out anonymously so log them out



			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');



			$GLOBALS['ISC_CLASS_CUSTOMER']->Logout(true);







			$tags[] = $this->MakeXMLTag('status', 1);



			$tags[] = $this->MakeXMLTag('billingContents', $GLOBALS['ISC_CLASS_CHECKOUT']->ExpressCheckoutChooseAddress('billing', true), true);



			$tags[] = $this->MakeXMLTag('shippingContents', $GLOBALS['ISC_CLASS_CHECKOUT']->ExpressCheckoutChooseAddress('shipping', true), true);



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			die();



		}







		/**



		 * Check a customers entered credentials when logging in via the express checkout.



		 */



		private function ExpressCheckoutLogin()



		{



			// Attempt to log the customer in



			$GLOBALS['ISC_CLASS_CUSTOMER'] = GetClass('ISC_CUSTOMER');



			if(!$GLOBALS['ISC_CLASS_CUSTOMER']->CheckLogin(true)) {



				$tags[] = $this->MakeXMLTag('status', 0);



				$loginLink = '#';



				$onClick = '$("#checkout_type_register").click(); $("#CreateAccountButton").click(); return false;';



				$tags[] = $this->MakeXMLTag('message', sprintf(GetLang('CheckoutBadLoginDetails'), $loginLink, $onClick), true);



				$tags[] = $this->MakeXMLTag('errorContainer', '#CheckoutLoginError');



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				die();



			}







			// Otherwise, the customer is now logged in and can continue the checkout



			$GLOBALS['ISC_CLASS_CHECKOUT'] = GetClass('ISC_CHECKOUT');







			$tags[] = $this->MakeXMLTag('status', 1);



			$tags[] = $this->MakeXMLTag('billingContents', $GLOBALS['ISC_CLASS_CHECKOUT']->ExpressCheckoutChooseAddress('billing', true), true);



			$tags[] = $this->MakeXMLTag('shippingContents', $GLOBALS['ISC_CLASS_CHECKOUT']->ExpressCheckoutChooseAddress('shipping', true), true);



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			die();



		}







		/**



		 * Generate the payment form for a payment provider (credit card manual, etc) and display it for the express checkout.



		 */



		private function GetExpressCheckoutPaymentForm()



		{



			$GLOBALS['ISC_CLASS_CHECKOUT'] = GetClass('ISC_CHECKOUT');







			// Attempt to create the pending order with the selected details



			$pendingResult = $GLOBALS['ISC_CLASS_CHECKOUT']->SavePendingOrder();







			// There was a problem creating the pending order



			if(!is_array($pendingResult)) {



				$tags[] = $this->MakeXMLTag('status', 0);



				$tags[] = $this->MakeXMLTag('step', 'Confirmation');



				$tags[] = $this->MakeXMLTag('message', GetLang('ProblemCreatingOrder'), true);



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			// There was a problem creating the pending order but we have an actual error message



			if(isset($pendingResult['error'])) {



				$tags[] = $this->MakeXMLTag('status', 0);



				$tags[] = $this->MakeXMLTag('step', 'Confirmation');



				$tags[] = $this->MakeXMLTag('message', $pendingResult['error'], true);



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			// Otherwise, the gateway want's to do something



			if($pendingResult['provider']->GetPaymentType() == PAYMENT_PROVIDER_ONLINE || method_exists($pendingResult['provider'], 'ShowPaymentForm')) {



				if($pendingResult['provider']->GetPaymentType() !== PAYMENT_PROVIDER_ONLINE) {



					$pendingResult['showPaymentForm'] = $pendingResult['provider']->ShowPaymentForm();



				}







				// If we have a payment form to show then show that



				if(isset($pendingResult['showPaymentForm']) && $pendingResult['showPaymentForm']) {



					$tags[] = $this->MakeXMLTag('status', 1);



					$tags[] = $this->MakeXMLTag('paymentContents', $pendingResult['provider']->ShowPaymentForm(), true);



					$this->SendXMLHeader();



					$this->SendXMLResponse($tags);



				}



			}



			exit;



		}







		/**



		 * Validate an incoming shipping or billing address checking for missing fields and showing error



		 * messages where necessary. Returns a structured address array if the passed address is valid.



		 *



		 * @param string The type of address to validate (billing or shipping)



		 * @return array An array of information about the address if valid.



		 */



		private function GetExpressCheckoutAddressData($type)



		{



			// Check to see if our state is required for the selected country



			$stateRequired = false;



			if (isset($_POST[$type.'_country']) && isId($_POST[$type.'_country']) && (!isset($_POST[$type . '_state']) || !$_POST[$type . '_state'])) {



				$query = $GLOBALS['ISC_CLASS_DB']->Query("SELECT COUNT(*) AS Total FROM [|PREFIX|]country_states WHERE statecountry='" . (int)$_POST[$type.'_country'] . "'");



				if (($total = $GLOBALS['ISC_CLASS_DB']->FetchOne($query, 'Total')) > 0) {



					$stateRequired = true;



				}



			}







			$addressVars = array(



				'shipfirstname' => array(



					'field' => $type.'_FirstName',



					'required' => true,



					'message' => GetLang('EnterShippingFirstName')



				),



				'shiplastname' => array(



					'field' => $type.'_LastName',



					'required' => true,



					'message' => GetLang('EnterShippingLastName')



				),



				'shipcompany' => array(



					'field' => $type.'_CompanyName',



					'required' => false,



				),



				'shipaddress1' => array(



					'field' => $type.'_AddressLine1',



					'required' => true,



					'message' => GetLang('EnterShippingAddress')



				),



				'shipaddress2' => array(



					'field' => $type.'_AddressLine2',



					'required' => false,



				),



				'shipcity' => array(



					'field' => $type.'_City',



					'required' => true,



					'message' => GetLang('EnterShippingCity')



				),



				'shipstate' => array(



					'field' => $type.'_State',



					'required' => $stateRequired,



					'message' => GetLang('EnterShippingState')



				),



				'shipzip' => array(



					'field' => $type.'_Zip',



					'required' => true,



					'message' => GetLang('EnterShippingZip')



				),



				'shipcountry' => array(



					'field' => $type.'_Country',



					'required' => true,



					'message' => GetLang('EnterShippingCountry')



				),



				'shipphone' => array(



					'field' => $type.'_Phone',



					'required' => true,



					'message' => GetLang('EnterShippingPhone')



				),



			);







			if($type == 'billing' && !CustomerIsSignedIn()) {



				$addressVars['shipemail'] = array(



					'field' => 'billing_EmailAddress',



					'required' => true,



					'message' => GetLang('AccountEnterValidEmail')



				);



			}







			$addressData = array();



			$step = ucfirst($type).'Address';







			foreach($addressVars as $field => $fieldInfo) {



				$postField = $fieldInfo['field'];



				// If this field is required and it hasn't been passed then we need to spit out an error



				if($fieldInfo['required'] == true && (!isset($_POST[$postField]) || !$_POST[$postField])) {



						$tags[] = $this->MakeXMLTag('status', 0);



						$tags[] = $this->MakeXMLTag('step', $step);



						$tags[] = $this->MakeXMLTag('focus', '#'.$postField);



						$tags[] = $this->MakeXMLTag('message', $fieldInfo['message']);



						$this->SendXMLHeader();



						$this->SendXMLResponse($tags);



						exit;



				}







				// If the state field, we also need to get the ID of the state and save it too



				if($field == 'shipstate') {



					$stateInfo = GetStateInfoByName($_POST[$postField]);



					$addressData['shipstate'] = $_POST[$postField];



					if ($stateInfo) {



						$addressData['shipstateid'] = $stateInfo['stateid'];



					} else {



						$addressData['shipstateid'] = 0;



					}



					continue;



				}



				else if($field == 'shipcountry') {



					$addressData['shipcountry'] = $_POST[$postField];



					$addressData['shipcountryid'] = GetCountryByName($_POST[$postField]);



					if (!isId($addressData['shipcountryid'])) {



						$addressData['shipcountryid'] = 0;



					}



					continue;



				}



				$addressData[$field] = $_POST[$postField];



			}







			$addressData['shipdestination'] = 'residential';







			// OK, we've got everything we want, we can just return it now



			return $addressData;







		}







		/**



		 * Generate the order confirmation message and save the pending order for a customer checking out via the



		 * express checkout



		 */



		private function GetExpressCheckoutConfirmation()



		{



			$GLOBALS['ISC_CLASS_CHECKOUT'] = GetClass('ISC_CHECKOUT');



			$GLOBALS['ISC_CLASS_CART'] = GetClass('ISC_CART');







			// If the customer is not logged in and guest checkout is enabled, then don't go any further



			if(!CustomerIsSignedIn() && !GetConfig('GuestCheckoutEnabled') && !isset($_POST['createAccount'])) {



				$tags[] = $this->MakeXMLTag('status', 0);



				$tags[] = $this->MakeXMLTag('step', 'AccountDetails');



				$tags[] = $this->MakeXMLTag('message', GetLang('GuestCheckoutDisabledError'));



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



			}







			// If the customer is creating an account, validate their account creation



			if(isset($_POST['createAccount'])) {



				$this->ExpressCheckoutRegister(true);



			}







			// Using a new billing address



			if(isset($_REQUEST['billingType']) && $_REQUEST['billingType'] == 'new') {



				// Loop through all of the address fields and build the address to save with the order



				$addressData = $this->GetExpressCheckoutAddressData('billing');







				if(isset($_POST['billing_SaveThisAddress'])) {



					$addressData['saveAddress'] = true;



				}







				// Set aside any of the custom fields if we have any



				if (isset($_POST['custom']) && is_array($_POST['custom'])) {



					// We need to split it up between customer and billing custom data







					$accountFields = $GLOBALS['ISC_CLASS_FORM']->getFormFields(FORMFIELDS_FORM_ACCOUNT);



					$accountData = array();



					$billingData = array();







					foreach (array_keys($_POST['custom']) as $fieldId) {



						if (array_key_exists($fieldId, $accountFields)) {



							$accountData[$fieldId] = $_POST['custom'][$fieldId];



						} else {



							$billingData[$fieldId] = $_POST['custom'][$fieldId];



						}



					}







					if (!empty($accountData)) {



						$GLOBALS['ISC_CLASS_CHECKOUT']->SetCustomFieldData('customer', $accountData);



					}







					if (!empty($billingData)) {



						$GLOBALS['ISC_CLASS_CHECKOUT']->SetCustomFieldData('billing', $billingData);



					}



				}







				if(!$GLOBALS['ISC_CLASS_CHECKOUT']->SetOrderBillingAddress($addressData)) {



					$tags[] = $this->MakeXMLTag('status', 0);



					$tags[] = $this->MakeXMLTag('step', 'BillingAddress');



					$tags[] = $this->MakeXMLTag('message', GetLang('UnableSaveOrderBillingAddress'));



					$this->SendXMLHeader();



					$this->SendXMLResponse($tags);



					exit;



				}



			}



			else {



				// If we're here, we need to save the details the customer entered in the session



				if(!$GLOBALS['ISC_CLASS_CHECKOUT']->SetOrderBillingAddress($_REQUEST['billingAddressId'])) {



					$tags[] = $this->MakeXMLTag('status', 0);



					$tags[] = $this->MakeXMLTag('step', 'BillingAddress');



					$tags[] = $this->MakeXMLTag('message', GetLang('UnableSaveOrderBillingAddress'));



					$this->SendXMLHeader();



					$this->SendXMLResponse($tags);



					exit;



				}



			}







			if(!$GLOBALS['ISC_CLASS_CART']->api->AllProductsInCartAreIntangible()) {



				// If the shipping provider couldn't be saved with the order show an error message



				$checkout = GetClass('ISC_CHECKOUT');



				$cartContent = $checkout->BreakdownCartByAddressVendor();



				foreach($cartContent as $vendorId => $addresses) {



					foreach(array_keys($addresses) as $addressId) {



						if(!isset($_REQUEST['selectedShippingMethod'][$vendorId][$addressId]) || !$GLOBALS['ISC_CLASS_CHECKOUT']->SetOrderShippingProvider($vendorId, $addressId, $_REQUEST['selectedShippingMethod'][$vendorId][$addressId])) {



							$tags[] = $this->MakeXMLTag('status', 0);



							$tags[] = $this->MakeXMLTag('step', 'ShippingProvder');



							$tags[] = $this->MakeXMLTag('message', GetLang('UnableSaveOrderShippingMethod'));



							$this->SendXMLHeader();



							$this->SendXMLResponse($tags);



							exit;



						}



					}



				}



			}







			$confirmation = $GLOBALS['ISC_CLASS_CHECKOUT']->GenerateExpressCheckoutConfirmation();







			$tags[] = $this->MakeXMLTag('status', 1);



			$tags[] = $this->MakeXMLTag('confirmationContents', $confirmation, true);



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			exit;



		}







		/**



		 * Generate a list of shipping methods/providers for a customer checking out via the express checkout.



		 */



		private function GetExpressCheckoutShippers()



		{



			// Now we have the zone, what available shipping methods do we have?



			$checkout = GetClass('ISC_CHECKOUT');



			$cart = GetClass('ISC_CART');







			if(!$cart->api->AllProductsInCartAreIntangible()) {



				// Using a new shipping address



				if(isset($_REQUEST['shippingType']) && $_REQUEST['shippingType'] == 'new') {



					$addressData = $this->GetExpressCheckoutAddressData('shipping');







					if(isset($_POST['shipping_SaveThisAddress']) && $_POST['shipping_SaveThisAddress'] !== '') {



						$addressData['saveAddress'] = true;



					}



					$addressId = 0;







					// Set aside any of the custom fields if we have any



					if (isset($_POST['custom']) && is_array($_POST['custom'])) {







						/**



						 * We need to map this into the billing fields as we are assuming that any



						 * address is using the billing fields in the frontend



						 */



						$shippingKeys = array_keys($_POST['custom']);



						$fieldAddressMap = $GLOBALS['ISC_CLASS_FORM']->mapAddressFieldList(FORMFIELDS_FORM_SHIPPING, $shippingKeys);



						$shippingSessData = array();







						foreach ($fieldAddressMap as $field => $newBillingId) {



							$shippingSessData[$newBillingId] = $_POST['custom'][$field];



						}







						$checkout->SetCustomFieldData('shipping', $shippingSessData);



					}







					if(!$checkout->SetOrderShippingAddress($addressData)) {



						$tags[] = $this->MakeXMLTag('status', 0);



						$tags[] = $this->MakeXMLTag('step', 'ShippingAddress');



						$tags[] = $this->MakeXMLTag('message', GetLang('UnableSaveOrderShippingAddress'));



						$this->SendXMLHeader();



						$this->SendXMLResponse($tags);



						exit;



					}



				}



				// Otherwise we've selected an existing address to use



				else {



					if(!isset($_REQUEST['shippingAddressId']) || !$checkout->SetOrderShippingAddress($_REQUEST['shippingAddressId'])) {



						$tags[] = $this->MakeXMLTag('status', 0);



						$tags[] = $this->MakeXMLTag('step', 'ShippingAddress');



						$tags[] = $this->MakeXMLTag('message', GetLang('UnableSaveOrderShippingAddress'));



						$this->SendXMLHeader();



						$this->SendXMLResponse($tags);



						exit;



					}



					else {



						$addressId = $_REQUEST['shippingAddressId'];



					}



				}



			}







			$availableMethods = $checkout->GetCheckoutShippingMethods();







			if(empty($availableMethods)) {



				$tags[] = $this->MakeXMLTag('status', 0);



				$tags[] = $this->MakeXMLTag('step', 'ShippingAddress');



				$tags[] = $this->MakeXMLTag('message', GetLang('UnableToShipToAddressSingle'), true);



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			$hideItemList = false;



			if(count($availableMethods) == 1 && count(current($availableMethods)) == 1) {



				$GLOBALS['HideVendorTitle'] = 'display: none';



				$GLOBALS['HideVendorItems'] = 'display: none';



				$hideItemList = true;



			}







			$hasTransit = false;



			$GLOBALS['ShippingQuotes'] = '';



			$orderShippingAddresses = $checkout->GetOrderShippingAddresses();



			$vendors = $cart->api->GetCartVendorIds();







			foreach($vendors as $i => $vendorId) {



				$GLOBALS['VendorId'] = $vendorId;



				if($vendorId != 0) {



					$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');



					$vendor = $vendorCache[$vendorId];



					$GLOBALS['VendorName'] = isc_html_escape($vendor['vendorname']);



				}



				else {



					$GLOBALS['VendorName'] = GetConfig('StoreName');



				}







				$shippingDestinations = $availableMethods[$vendorId];



				if(count($shippingDestinations) == 1 && !isset($_SESSION['CHECKOUT']['SPLIT_SHIPPING'])) {



					$GLOBALS['HideAddressLine'] = 'display: none';



				}



				else {



					$GLOBALS['HideAddressLine'] = '';



				}







				$textItemList = '';







				foreach($shippingDestinations as $addressId => $shippingInfo) {



					if(isset($vendors[$i+1]) || isset($shippingDestinations[$addressId+1])) {



						$GLOBALS['HideHorizontalRule'] = '';



					}



					else {



						$GLOBALS['HideHorizontalRule'] = 'display: none';



					}







					$GLOBALS['AddressId'] = $addressId;



					// If no methods are available, this order can't progress so show an error



					if(empty($shippingInfo['quotes'])) {



						$GLOBALS['HideNoShippingProviders'] = '';



						$GLOBALS['HideShippingProviderList'] = 'none';



						$hideItemList = false;



					}







					$GLOBALS['ItemList'] = '';



					if(!$hideItemList) {



						foreach($shippingInfo['items'] as $product) {



							// Only show physical items



							if($product['data']['prodtype'] != PT_PHYSICAL) {



								continue;



							}



							$textItemList .= $product['quantity'].' x '.$product['product_name']."\n";



							$GLOBALS['ProductQuantity'] = $product['quantity'];



							$GLOBALS['ProductName'] = isc_html_escape($product['product_name']);







							$GLOBALS['HideGiftWrapping'] = 'display: none';



							$GLOBALS['HideGiftMessagePreview'] = 'display: none';



							$GLOBALS['GiftWrappingName'] = '';



							$GLOBALS['GiftMessagePreview'] = '';



							if(isset($product['wrapping']['wrapname'])) {



								$GLOBALS['HideGiftWrapping'] = '';



								$GLOBALS['GiftWrappingName'] = isc_html_escape($product['wrapping']['wrapname']);



								if(isset($product['wrapping']['wrapmessage'])) {



									if(isc_strlen($product['wrapping']['wrapmessage']) > 30) {



										$product['wrapping']['wrapmessage'] = substr($product['wrapping']['wrapmessage'], 0, 27).'...';



									}



									$GLOBALS['GiftMessagePreview'] = isc_html_escape($product['wrapping']['wrapmessage']);



									if($product['wrapping']['wrapmessage']) {



										$GLOBALS['HideGiftMessagePreview'] = '';



									}



								}



							}







							$GLOBALS['ItemList'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ShippingQuoteProduct');



						}



					}







					// If no methods are available, this order can't progress so show an error



					if(empty($shippingInfo['quotes'])) {



						$tags[] = $this->MakeXMLTag('status', 0);



						$tags[] = $this->MakeXMLTag('step', 'ShippingAddress');



						$textItemList = rtrim($textItemList, "\n");



						$tags[] = $this->MakeXMLTag('message', GetLang('AjaxUnableToShipToAddress')."\n\n".$textItemList, true);



						$this->SendXMLHeader();



						$this->SendXMLResponse($tags);



						exit;



					}







					if(!$GLOBALS['HideAddressLine']) {



						$address = $orderShippingAddresses[$addressId];



						$addressLine = array(



							$address['shipfirstname'].' '.$address['shiplastname'],



							$address['shipcompany'],



							$address['shipaddress1'],



							$address['shipaddress2'],



							$address['shipcity'],



							$address['shipstate'],



							$address['shipzip'],



							$address['shipcountry']



						);







						// Please see self::GenerateShippingSelect below.



						$addressLine = array_filter($addressLine, array($checkout, 'FilterAddressFields'));



						$GLOBALS['AddressLine'] = isc_html_escape(implode(', ', $addressLine));



					}



					else {



						$GLOBALS['AddressLine'] = '';



					}







					// Now build a list of the actual available quotes



					$GLOBALS['ShippingProviders'] = '';



					foreach($shippingInfo['quotes'] as $quoteId => $method) {



						$GLOBALS['ShipperName'] = isc_html_escape($method['description']);



						$GLOBALS['ShippingPrice'] = CurrencyConvertFormatPrice($method['price']);



						$GLOBALS['ShippingQuoteId'] = $quoteId;



						$GLOBALS['ShippingData'] = $GLOBALS['ShippingQuoteId'];







						if(isset($method['transit'])) {



							$hasTransit = true;







							$days = $method['transit'];







							if ($days == 0) {



								$transit = GetLang("SameDay");



							}



							else if ($days == 1) {



								$transit = GetLang('NextDay');



							}



							else {



								$transit = sprintf(GetLang('Days'), $days);



							}







							$GLOBALS['TransitTime'] = $transit;



							$GLOBALS['TransitTime'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingTransitTime');



						}



						else {



							$GLOBALS['TransitTime'] = "";



						}



						$GLOBALS['ShippingProviders'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("ExpressCheckoutShippingMethod");



					}







					// Add it to the list



					$GLOBALS['ShippingQuotes'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ShippingQuote');



					$_SESSION['CHECKOUT']['SHIPPING_QUOTES'][$vendorId][$addressId] = $shippingInfo['quotes'];



				}



			}







			if ($hasTransit) {



				$GLOBALS['DeliveryDisclaimer'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingDeliveryDisclaimer');



			}







			$methodList = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('ExpressCheckoutChooseShipper');



			$tags[] = $this->MakeXMLTag('status', 1);



			$tags[] = $this->MakeXMLTag('providerContents', $methodList, true);



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



		}







		/**



		 * Retrieve a list of shipping quotes for a customer estimating their shipping on the 'View Cart' page.



		 */



		private function GetShippingQuotes()



		{



			if(!isset($_POST['countryId'])) {



				exit;



			}







			unset($_SESSION['SHIPPING']);







			// Determine which shipping zone we're in



			$address = array(



				'shipcountryid' => (int)$_POST['countryId'],



				'shipstateid' => '',



				'shipzip' => ''



			);







			$_SESSION['CART']['SHIPPING']['COUNTRY_ID'] = (int)$_POST['countryId'];







			if(isset($_POST['stateId'])) {



				$address['shipstateid'] = (int)$_POST['stateId'];



				$_SESSION['CART']['SHIPPING']['STATE_ID'] = (int)$_POST['stateId'];



			}







			if(isset($_POST['zipCode'])) {



				$address['shipzip'] = $_POST['zipCode'];



				$_SESSION['CART']['SHIPPING']['ZIP_CODE'] = $_POST['zipCode'];



			}







			// What shipping zone do we fall under?



			$shippingZone = GetShippingZoneIdByAddress($address);







			$_SESSION['CART']['SHIPPING']['ZONE'] = $shippingZone;







			// Now we have the zone, what available shipping methods do we have?



			$cart = GetClass('ISC_CART');



			$vendorMethods = $cart->GetAvailableShippingMethods($address);







			$cartProducts = $cart->api->GetProductsInCartByVendor();







			$GLOBALS['ShippingQuotes'] = '';







			// If there's only one vendor, don't show the vendor titles



			$GLOBALS['HideVendorDetails'] = '';



			$hideItemList = false;



			if(count($cartProducts) == 1) {



				$hideItemList = true;



				$GLOBALS['HideVendorDetails'] = 'display: none';



				$GLOBALS['ShippingQuotesListNote'] = '';



				$GLOBALS['HideShippingQuotesListNote'] = 'display: none';



				$GLOBALS['VendorShippingQuoteClass'] = '';



			}



			else {



				$GLOBALS['ShippingQuotesListNote'] = GetLang('ShippingQuotesListNote');



				$GLOBALS['HideShippingQuotesListNote'] = '';



				$GLOBALS['VendorShippingQuoteClass'] = 'VendorShipping';



			}







			$hasTransit = false;







			foreach($vendorMethods as $vendorId => $methods) {



				if(empty($methods)) {



					echo GetLang('UnableEstimateShipping');



					exit;



				}



				$GLOBALS['VendorId'] = $vendorId;



				$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');







				if($vendorId != 0) {



					$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');



					$vendor = $vendorCache[$vendorId];



					$GLOBALS['VendorName'] = isc_html_escape($vendor['vendorname']);



				}



				else {



					$GLOBALS['VendorName'] = GetConfig('StoreName');



				}







				$GLOBALS['ShippingQuoteRow'] = '';







				foreach($methods as $quoteId => $method) {



					$GLOBALS['ShipperName'] = isc_html_escape($method['description']);



					$GLOBALS['ShippingPrice'] = CurrencyConvertFormatPrice($method['price']);



					$GLOBALS['ShippingQuoteId'] = $quoteId;







					$GLOBALS['ShippingItemList'] = '';



					$GLOBALS['HideShippingItemList'] = 'display: none';



					if(!$hideItemList && !empty($cartProducts[$vendorId])) {



						$GLOBALS['HideShippingItemList'] = '';



						foreach($cartProducts[$vendorId] as $product) {



							if($product['data']['prodtype'] != PT_PHYSICAL) {



								continue;



							}



							$GLOBALS['ProductQuantity'] = $product['quantity'];



							$GLOBALS['ProductName'] = isc_html_escape($product['product_name']);



							$GLOBALS['ShippingItemList'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('EstimatedShippingQuoteProduct');



						}



					}







					if(isset($method['transit'])) {



						$hasTransit = true;







						$days = $method['transit'];







						if ($days == 0) {



							$transit = GetLang("SameDay");



						}



						else if ($days == 1) {



							$transit = GetLang('NextDay');



						}



						else {



							$transit = sprintf(GetLang('Days'), $days);



						}







						$GLOBALS['TransitTime'] = $transit;



						$GLOBALS['TransitTime'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingTransitTime');



					}



					else {



						$GLOBALS['TransitTime'] = "";



					}







					$GLOBALS['ShippingQuoteRow'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingQuoteRow');



				}



				$GLOBALS['ShippingQuotes'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('EstimatedShippingQuote');







				// For good measure (and validation on the server side!) we store the list of quotes we got



				$_SESSION['CART']['SHIPPING_QUOTES'][$vendorId] = $methods;



			}







			if ($hasTransit) {



				$GLOBALS['DeliveryDisclaimer'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('CartShippingDeliveryDisclaimer');



			}







			echo $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet('EstimatedShippingQuoteList');



		}







		private function GetCountryStates()



		{



			$country = $_REQUEST['c'];



			echo GetStateList($country);



		}







		private function GetExchangeRate()



		{



			if (!array_key_exists("currencyid", $_REQUEST)



				|| !($result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]currencies WHERE currencyid = " . (int)$_REQUEST['currencyid']))



				|| !($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result))) {



				exit;



			}







			print $row['currencyexchangerate'];



			exit;



		}







		public function GetStateList()



		{



			if (!array_key_exists('countryName', $_POST) || $_POST['countryName'] == '') {



				$tags[] = $this->MakeXMLTag('status', 0);



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			$tags[] = $this->MakeXMLTag('status', 1);



			$tags[] = '<options>';







			$query = "SELECT statename



						FROM [|PREFIX|]countries c



							JOIN [|PREFIX|]country_states s ON c.countryid = s.statecountry



						WHERE c.countryname='" . $GLOBALS['ISC_CLASS_DB']->Quote($_POST['countryName']) . "'



						ORDER BY statename ASC";







			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {



				$tags[] = '<option>';



				$tags[] = $this->MakeXMLTag('name', $row['statename'], true);



				$tags[] = '</option>';



			}







			$tags[] = '</options>';



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			exit;



		}







		private function GetCountryList()



		{



			$tags[] = $this->MakeXMLTag('status', 1);



			$tags[] = '<options>';







			$result = $GLOBALS['ISC_CLASS_DB']->Query("SELECT * FROM [|PREFIX|]countries ORDER BY countryname ASC");



			while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {



				$tags[] = '<option>';



				$tags[] = $this->MakeXMLTag('name', $row['countryname'], true);



				$tags[] = '</option>';



			}







			$tags[] = '</options>';



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			exit;



		}







		/**



		* Handles adding products from the list display mode



		*



		*/



		private function AddProductsToCart()



		{



			$response = array();







			if (isset($_REQUEST['products'])) {



				$cart = GetClass('ISC_CART');







				$products = explode("&", $_REQUEST["products"]);







				foreach ($products as $product) {



					list($id, $qty) = explode("=", $product);



					if (!$cart->AddSimpleProductToCart($id, $qty)) {



						$response["error"] = $_SESSION['ProductErrorMessage'];



					}



				}



			}







			echo isc_json_encode($response);



			exit;



		}











		public function ProcessRemoteActions()



		{







			if(!isset($_REQUEST['provider'])) {



				$tags[] = $this->MakeXMLTag('errorMsg', GetLang('ExpressCheckoutLoadError')."1");



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}



			if(!GetModuleById('checkout', $provider, $_REQUEST['provider'])) {



				$tags[] = $this->MakeXMLTag('errorMsg', GetLang('ExpressCheckoutLoadError')."2");



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			// This gateway doesn't support remote actions



			if(!method_exists($provider, 'ProcessRemoteActions')) {



				$tags[] = $this->MakeXMLTag('errorMsg', GetLang('ExpressCheckoutLoadError')."3");



				$this->SendXMLHeader();



				$this->SendXMLResponse($tags);



				exit;



			}







			$result = $provider->ProcessRemoteActions();



			$tags[] = $this->MakeXMLTag('errorMsg', $result['error']);



			$tags[] = $this->MakeXMLTag('data', isc_html_escape($result['data']));



			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			exit;



		}











		private function doAdvanceSearch()



		{



			throw new Exception('MOVED TO ShowAjaxSearchPage');



		}







		private function sortAdvanceSearch()



		{



			if (!array_key_exists("section", $_REQUEST) || trim($_REQUEST["section"]) == "") {



				exit;



			}







			if (!array_key_exists("sortBy", $_REQUEST) || trim($_REQUEST["sortBy"]) == "") {



				exit;



			}







			$this->doAdvanceSearch();



		}











		public function GetVariationOptions()



		{



			$productId = (int)$_GET['productId'];



			$optionIds = $_GET['options'];



			$optionIdsArray = explode(',', $optionIds);







			// We need to find the next type of option that's selectable, so what we do



			// is because the vcoptionids column is in the order that the customer selects



			// the options, we just find a single matching option and then look up values



			// according to the voname.







			$query = "



				SELECT prodvariationid, vnumoptions



				FROM [|PREFIX|]products p



				JOIN [|PREFIX|]product_variations v ON (v.variationid=p.prodvariationid)



				WHERE p.productid='".$productId."'



			";



			$result =$GLOBALS['ISC_CLASS_DB']->query($query);



			$product = $GLOBALS['ISC_CLASS_DB']->fetch($result);







			// Invalid product variation, or product doesn't have a variation



			if(empty($product)) {



				exit;



			}







			// If we received the number of options the variation has in, then the customer



			// has selected an entire row. Find that row.



			if(count($optionIdsArray) == $product['vnumoptions']) {



				$setMatches = array();



				foreach($optionIdsArray as $optionId) {



					$setMatches[] = 'FIND_IN_SET('.$optionId.', vcoptionids)';



				}



				$query = "



					SELECT *



					FROM [|PREFIX|]product_variation_combinations



					WHERE



						vcproductid='".$productId."' AND



						vcenabled=1 AND



						".implode(' AND ', $setMatches)."



					LIMIT 1



				";



				$result = $GLOBALS['ISC_CLASS_DB']->query($query);



				$combination = $GLOBALS['ISC_CLASS_DB']->fetch($result);











				$productClass = new ISC_PRODUCT($productId);



				$combinationDetails = $productClass->getCombinationDetails($combination);



				$combinationDetails['comboFound'] = true;







				echo isc_json_encode($combinationDetails);



				exit;



			}







			// Try to find a combination row with the incoming option ID string, to determine



			// which set of options is next.



			$query = "



				SELECT DISTINCT voname



				FROM [|PREFIX|]product_variation_options



				WHERE



					vovariationid='".$product['prodvariationid']."'



				ORDER BY vooptionsort ASC



				LIMIT ".count($optionIdsArray).", 1



			";



			$optionName = $GLOBALS['ISC_CLASS_DB']->fetchOne($query);







			$hasOptions = false;



			$valueHTML = '';







			$query = "



				SELECT *



				FROM [|PREFIX|]product_variation_options



				WHERE



					vovariationid='".$product['prodvariationid']."' AND



					voname='".$GLOBALS['ISC_CLASS_DB']->quote($optionName)."'



				ORDER BY vovaluesort ASC



			";



			$result = $GLOBALS['ISC_CLASS_DB']->query($query);



			while($option = $GLOBALS['ISC_CLASS_DB']->fetch($result)) {



				$query = "



					SELECT combinationid



					FROM [|PREFIX|]product_variation_combinations



					WHERE



						vcproductid='".$productId."' AND



						vcenabled=1 AND



						FIND_IN_SET(".$option['voptionid'].", vcoptionids) > 0



					LIMIT 1



				";



				// Ok, this variation option isn't in use for this product at the moment. Skip it



				if(!$GLOBALS['ISC_CLASS_DB']->fetchOne($query)) {



					continue;



				}







				$GLOBALS['OptionId'] = (int)$option['voptionid'];



				$GLOBALS['OptionValue'] = isc_html_escape($option['vovalue']);



				$valueHTML .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("ProductVariationListMultipleItem");



				$hasOptions = true;



			}







			$return = array(



				'hasOptions' 	=> $hasOptions,



				'options'		=> $valueHTML



			);







			echo isc_json_encode($return);



			exit;



		}















		/**



		* Updates the language file. Used by design mode



		*



		* @return void



		*/



		private function UpdateLanguage()



		{



			if(!getClass('ISC_ADMIN_AUTH')->isDesignModeAuthenticated()) {



				exit;



			}







			getClass('ISC_ADMIN_ENGINE')->loadLangFile('layout');



			$name	= str_replace("lang_", "", $_REQUEST['LangName']);



			$value	= $_REQUEST['NewValue'];



			/*$value = str_replace(array("\n","\r"), "", $value);*/



			$value = str_replace('"', "&quot;", $value);







			$content = file_get_contents(ISC_BASE_PATH."/language/".GetConfig('Language')."/front_language.ini");



			$frontLang = parse_ini_file(ISC_BASE_PATH."/language/".GetConfig('Language')."/front_language.ini");







			$replacement = $name . ' = "' . str_replace('$', '\$', $value) . '"';



			$replace = preg_replace("#^\s*".preg_quote($name, "#")."\s*=\s*\"".preg_quote(@$frontLang[$name], "#").'"\s*$#im', $replacement, $content);







			if(file_put_contents(ISC_BASE_PATH."/language/".GetConfig('Language')."/front_language.ini", $replace)) {



				$tags[] = $this->MakeXMLTag('status',1);



				$tags[] = $this->MakeXMLTag('newvalue', $value, true);



			}else {



				$tags[] = $this->MakeXMLTag('status',0);



				$tags[] = $this->MakeXMLTag('message', GetLang('UpdateLanguage'));



			}







			$this->SendXMLHeader();



			$this->SendXMLResponse($tags);



			die();



		}



	}



