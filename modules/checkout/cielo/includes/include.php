<?php
//detalhes do pedido
require_once 'pedido.php';

//nao modificar
error_reporting(0);
ini_set("track_errors","0");

//pega o cartao e ver qual e
if($cartao=='visa'){

$op = 'visa';
$autorizar = 2;

}elseif($cartao=='electron'){

$op = 'visa';
$autorizar = 2;

}elseif($cartao=='mastercard'){

$op = 'mastercard';
$autorizar = 2;

}elseif($cartao=='elo'){

$op = 'elo';
$autorizar = 3;

}elseif($cartao=='diners'){

$op = 'diners';
$autorizar = 3;

}elseif($cartao=='discover'){

$op = 'discover';
$autorizar = 3;

}

//variaveis e dados cielo
define('VERSAO', "1.1.0");
define('TESTE', "https://qasecommerce.cielo.com.br/servicos/ecommwsec.do");
define('PRODUCAO', "https://ecommerce.cielo.com.br/servicos/ecommwsec.do");

//status do pedidos
$aprovadocielo = ORDER_STATUS_AWAITING_SHIPMENT;
$canceladocielo = ORDER_STATUS_CANCELLED;

//faz o parcelamento
function parcelarcielo($valorTotal, $taxa, $nParcelas){
    $taxa = $taxa/100;
    $cadaParcela = ($valorTotal*$taxa)/(1-(1/pow(1+$taxa, $nParcelas)));
    return round($cadaParcela, 2);
}

function TotalPedidoCielo($itemId){
//dados Pedido
$sql = $GLOBALS['ISC_CLASS_DB']->Query("select * from [|PREFIX|]orders where orderid='".$itemId."'");
$fetch_order = $GLOBALS['ISC_CLASS_DB']->Fetch($sql);
return $fetch_order['ordgatewayamount'];
}

// Envia requisiчуo
function httprequest($paEndereco, $paPost){
	$sessao_curl = curl_init();
	curl_setopt($sessao_curl, CURLOPT_URL, $paEndereco);
	curl_setopt($sessao_curl, CURLOPT_FAILONERROR, true);
	curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($sessao_curl, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($sessao_curl, CURLOPT_CAINFO, getcwd() .
			"/ssl/VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt");
	curl_setopt($sessao_curl, CURLOPT_SSLVERSION, 3);
	curl_setopt($sessao_curl, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($sessao_curl, CURLOPT_TIMEOUT, 40);
	curl_setopt($sessao_curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($sessao_curl, CURLOPT_POST, true);
	curl_setopt($sessao_curl, CURLOPT_POSTFIELDS, $paPost );
	$resultado = curl_exec($sessao_curl);
	
	curl_close($sessao_curl);
	if ($resultado)
	{
		return $resultado;
	}
	else
	{
		return 'Erro';
	}
}

// Monta URL de retorno
function ReturnURL()
{
	$pageURL = 'http';

	if ($_SERVER["SERVER_PORT"] == 443) // protocolo https
	{
		$pageURL .= 's';
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"]. substr($_SERVER["REQUEST_URI"], 0);
	}
	// ALTERNATIVA PARA SERVER_NAME -> HOST_HTTP

	$file = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

	$ReturnURL = str_replace($file, "retorno.php", $pageURL);

	return $ReturnURL;
}
function simpleXMLToArray($xml,
                    $flattenValues=true,
                    $flattenAttributes = true,
                    $flattenChildren=true,
                    $valueKey='@value',
                    $attributesKey='@attributes',
                    $childrenKey='@children'){

        $return = array();
        if(!($xml instanceof SimpleXMLElement)){return $return;}
        $name = $xml->getName();
        $_value = trim((string)$xml);
        if(strlen($_value)==0){$_value = null;};

        if($_value!==null){
            if(!$flattenValues){$return[$valueKey] = $_value;}
            else{$return = $_value;}
        }

        $children = array();
        $first = true;
        foreach($xml->children() as $elementName => $child){
            $value = simpleXMLToArray($child, $flattenValues, $flattenAttributes, $flattenChildren, $valueKey, $attributesKey, $childrenKey);
            if(isset($children[$elementName])){
                if($first){
                    $temp = $children[$elementName];
                    unset($children[$elementName]);
                    $children[$elementName][] = $temp;
                    $first=false;
                }
                $children[$elementName][] = $value;
            }
            else{
                $children[$elementName] = $value;
            }
        }
        if(count($children)>0){
            if(!$flattenChildren){$return[$childrenKey] = $children;}
            else{$return = array_merge($return,$children);}
        }

        $attributes = array();
        foreach($xml->attributes() as $name=>$value){
            $attributes[$name] = trim($value);
        }
        if(count($attributes)>0){
            if(!$flattenAttributes){$return[$attributesKey] = $attributes;}
            else{$return = array_merge($return, $attributes);}
        }
       
        return $return;
}
?>