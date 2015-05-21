<?php
include "../init.php";
ini_set("display_errors", 0);
ini_set("allow_url_fopen", 1);
ini_set("allow_url_include", 1);
//funcao que faz o rastreamento
function rastrear($codigo){
	    $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14";
	    $conhecimento = trim(strtoupper($codigo));
		$reffer = "http://websro.correios.com.br/sro_bin/txect01$.QueryList";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://websro.correios.com.br/sro_bin/txect01$.QueryList");
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_REFERER, $reffer);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20); // segundos 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"P_ITEMCODE=&P_LINGUA=001&P_TESTE=&P_TIPO=001&P_COD_UNI=$conhecimento&Z_ACTION=Pesquisar");
		$result=curl_exec ($ch);
		curl_close ($ch);
		if($result == ""){
			echo 'O sistema não recebeu nenhuma resposta dos correios<br>';
			return 'Erro';	
		}
	$resultado_temp = $result;
	
	$pos_inicial = strpos($resultado_temp, '- Histórico do Objeto');
	if($pos_inicial > 1) {
	$pos_inicial = strpos($resultado_temp, '</tr>');
	$pos_final = strpos($resultado_temp, '</TABLE>');
	$pos_final = $pos_final - $pos_inicial;
	
	$resultado_temp = substr($resultado_temp, ($pos_inicial + 6), ($pos_final + 2));

	$tabela = '' . $resultado_temp . '';
	
	$resultado_temp = explode('<tr><td ', $resultado_temp);
			$pos_inicial = strpos($resultado_temp[1], '">');
			$pos = strpos($resultado_temp[1], '>');
			$data_status = substr($resultado_temp[1], ($pos + 1), 10);
			$hora_status = substr($resultado_temp[1], ($pos + 12), 5);
			$pos_final = strpos($resultado_temp[1], '</font>');
			$pos_final = $pos_final - $pos_inicial;
			$status = substr($resultado_temp[1], ($pos_inicial + 2), ($pos_final - 2));
			$resultado['status'] = $status;
			$resultado['data_status'] = $data_status; 
			$resultado['hora_status'] = $hora_status;
			$resultado['tabela'] = $tabela;
	}else{
	echo '<center><b>O objeto ' . $conhecimento . ' ainda não foi cadastrado junto aos correios.</center></b>';
	$resultado['status'] = 'Erro';
				
	}
return $resultado;
}

function enviaremail($var,$nome,$email,$pedido,$objeto){
require_once(ISC_BASE_PATH . "/lib/email.php");
$urlsite = GetConfig('ShopPath');
$nomeloja = GetConfig('StoreName');
$url = "http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=$objeto";

if($var['status']=='Entregue'){
@UpdateOrderStatus($pedido, ORDER_STATUS_COMPLETED);
}

$avisaradm = GetModuleVariable('addon_rastreamento','avisaradm');
if($var['status']=='Entregue' and $avisaradm=='sim'){

$obj_emaila = GetEmailClass();
$obj_emaila->Set('CharSet', GetConfig('CharacterSet'));
$obj_emaila->From(GetConfig('AdminEmail'), 'Web');
$obj_emaila->Set("Subject", 'Um pedido foi entregue id#'.$pedido);
$obj_emaila->AddBody("html", 'Ola, <b>Admin</b><br>Estamos lhe avisando que o pedido de numero '.$pedido.' e e codigo de rastreamento '.$objeto.' foi entregue ao seu devido cliente pelos correios.<br>');
$obj_emaila->AddRecipient(GetConfig('AdminEmail'), "", "h");

$obj_emaila->Send();

}

include('msgcorreios.php');

$obj_email = GetEmailClass();
$obj_email->Set('CharSet', GetConfig('CharacterSet'));
$obj_email->From(GetConfig('AdminEmail'), GetConfig('StoreName'));
$obj_email->Set("Subject", $tituloemail);
$obj_email->AddBody("html", $msg);

$avisaradmtodos = GetModuleVariable('addon_rastreamento','avisaradmtodos');

if($avisaradmtodos=='sim'){
$obj_email->AddRecipient($email, "", "h");
$obj_email->AddRecipient(GetConfig('AdminEmail'), "", "h");

}else{
$obj_email->AddRecipient($email, "", "h");

}

$obj_email->Send();

}

$ativo = GetModuleVariable('addon_rastreamento','ativar');
$avisar = GetModuleVariable('addon_rastreamento','avisar');


// ver se o mod esta ativo
if($ativo=='sim'){


$urlsite = GetConfig('ShopPath');
$nomeloja = GetConfig('StoreName');

$a = "select * from [|PREFIX|]orders where ordstatus IN('2','3') AND length(ordtrackingno) > 10";

$b = $GLOBALS['ISC_CLASS_DB']->Query($a);

$total = $GLOBALS['ISC_CLASS_DB']->CountResult($a);


// inicio ver se tem itens
if($total>0){

//inicio do while

while($dados = $GLOBALS['ISC_CLASS_DB']->Fetch($b)) {

$objeto = str_replace(' ','',$dados['ordtrackingno']);

echo "Rastreio de: ".$objeto."<br>";

$row = rastrear($objeto);


if($row['status']!='Erro'){ 

$query = "select * from rastreamento WHERE pedido = '".$dados['orderid']."'";
$totalresultados = $GLOBALS['ISC_CLASS_DB']->CountResult($query);


if($totalresultados==0){

$GLOBALS['ISC_CLASS_DB']->Query('INSERT INTO rastreamento(pedido,data,hora,status) values("'.$dados['orderid'].'","'.$row['data_status'].'","'.$row['hora_status'].'","'.$row['status'].'");');

$alterou = 1;

echo ' Primeiro envio (enviado)<br>-----------------<br>';

}else if($totalresultados>=1) {


$query = "select * from rastreamento WHERE pedido = '".$dados['orderid']."'";
$fim = $GLOBALS['ISC_CLASS_DB']->Query($query);
$fim = $GLOBALS['ISC_CLASS_DB']->Fetch($fim);

$alterou = 0;

$bb = ' Status sem modificacao<br>-----------------<br>';

if($row['status'] != $fim['status']){

$query = "UPDATE rastreamento SET status = '".$row['status']."' where pedido = '".$dados['orderid']."'";
$GLOBALS['ISC_CLASS_DB']->Query($query);

$alterou = 1;

$bb = ' Status modificado (enviado)<br>-----------------<br>';

}

echo $bb;

}


$objeto = str_replace(' ','',$dados['ordtrackingno']);
$emails = str_replace(' ','',$dados['ordbillemail']);
$nomes = $dados['ordbillfirstname']." ".$dados['ordbilllastname'];
$pedido = $dados['orderid'];



if(($avisar == '0') and ($row['status'] == 'Entregue')){

echo enviaremail($row,$nomes,$emails,$pedido,$objeto);

}elseif(($avisar == '1') and ($row['status'] == 'Aguardando retirada')){

echo enviaremail($row,$nomes,$emails,$pedido,$objeto);
	
}elseif($avisar == '2' && $alterou=='1'){

echo enviaremail($row,$nomes,$emails,$pedido,$objeto);

}



}else{

echo 'Erro ao Rastrear Pedido!!';

}

}
//fim do while


}else{

echo 'Nenhum Pedido a Rastrear no Momento!!';

}
// fim do se nao tiver nenhum pedido

} else {

echo 'Modulo de Rastreamento esta Desativado!!';

}//fim do modulo ativo ou nao
?>
