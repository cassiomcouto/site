<?php
ini_set("display_errors", 0);

//print_r($_REQUEST);

global $itemId;
$itemId = $_GET['pedido'];

$parcela = $_GET['pacela'];

if($parcela>=1 and $parcela<=9){
$parcela = "0".$parcela;
}else{
$parcela = $parcela;
}

$acao = $_GET['acao'];


include "../../../config/config.php";

$servidor = $GLOBALS['ISC_CFG']["dbServer"];
$usuariodb = $GLOBALS['ISC_CFG']["dbUser"];
$senhadb = $GLOBALS['ISC_CFG']["dbPass"];
$bancodados = $GLOBALS['ISC_CFG']["dbDatabase"];
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$nomeloja = $GLOBALS['ISC_CFG']["StoreName"];
$emailloja = $GLOBALS['ISC_CFG']["OrderEmail"];
$urlloja = $GLOBALS['ISC_CFG']["ShopPath"];

$urlloja = str_replace('http://','https://',$urlloja);

$conexao2 = mysql_connect($servidor, $usuariodb, $senhadb) or print(mysql_error());
$selecionabanco = mysql_select_db($bancodados,$conexao2) or print(mysql_error());
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS pag_redecard (id int(6) NOT NULL auto_increment, ped int(6) NOT NULL, bandeira varchar(250) NOT NULL, PRIMARY KEY  (id)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
@mysql_query($sql);
///////
$verifica = mysql_query("select * from pag_redecard where ped ='$itemId'") or print(mysql_error());
$ok = mysql_fetch_array($verifica);
if(empty($ok)){
$inserir = "INSERT INTO pag_redecard(ped,bandeira) VALUES('$itemId','$parcela');";
@mysql_query($inserir);
}else{
$result = mysql_query("UPDATE pag_redecard SET bandeira='$parcela' WHERE ped='$itemId'") or die(mysql_error()); 
}
/////////////////////////////////////////////////////
function corinthias($modulo, $alvo){
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$SCBoleto = mysql_query("select * from ".$prefixotabela."module_vars where modulename='$modulo' and variablename='$alvo'") or print(mysql_error());
$ftM = mysql_fetch_array($SCBoleto);
return $ftM['variableval'];
}

//Form
function ronaldo($valor){
$prefixotabela = $GLOBALS['ISC_CFG']["tablePrefix"];
$form = mysql_query("select * from ".$prefixotabela."formfieldsessions where formfieldsessioniformsessionid='$valor' and formfieldfieldlabel ='CPF'") or print(mysql_error());
$dados = mysql_fetch_array($form);

$var = explode('"',$dados[formfieldfieldvalue]);

return $var[1];
}


//Pedido
$selectorder = mysql_query("select * from ".$prefixotabela."orders where orderid='".$itemId."'") or print(mysql_error());
$fetch_order = mysql_fetch_array($selectorder);
$clientecustomer = $fetch_order['ordcustid'];

//Cliente
$selectcustomer = mysql_query("select * from ".$prefixotabela."customers where customerid='".$clientecustomer."'") or print(mysql_error());
$fetch_customer = mysql_fetch_array($selectcustomer);
/////////////////////////

function  _RedeCard_CodVer($n_filiacao,$total,$ip) {

                $data = getdate();
                $segundosAgora = $data['seconds'];
                /*
                esta é uma tabelinha de codificação da própria redecard, onde eles
                embaralham os segundos.
                NÃO ALTERAR!
                */
                $_secCodificado = array(11,17,21,31,56,34,42,3,18,13,
                12,18,22,32,57,35,43,4,19,14,9,20,23,33,58,36,44,5,24,
                15,62,25,34,59,37,45,6,25,16,27,63,26,35,60,38,46,7,26,
                17,28,14,36,2,39,47,8,29,22,55,33);
               
                $segundosAgora = $_secCodificado[ $segundosAgora ];

                $pad = '';
                if ($segundosAgora < 10) {
                        $pad = "0";
                } else {
                        $pad = "";
                }
                $tamIP = strlen($ip);
                $total = intval($total);
                $numfil = intval($n_filiacao);
                $i5 = $total + $segundosAgora;
                $i6 = $segundosAgora + $tamIP;
                $i7 = $segundosAgora * $numfil;
                $i8 = strlen($i7);
                return "$i7$i5$i6-$i8$pad$segundosAgora";
        }

$afi = corinthias("checkout_masterkomerci","afi");
$totalmenos = $fetch_order['ordgatewayamount'];
$valorPa =number_format($totalmenos, 2, '.', '');
$ip = $_SERVER['REMOTE_ADDR'];


$codver = _RedeCard_CodVer($afi,$valorPa,$ip);
?>

<form name='rd' action='https://ecommerce.redecard.com.br/pos_virtual/form_card.asp' method='POST'>
<input type="hidden" name="TOTAL" value="<?php echo $valorPa;?>" />
<input type="hidden" name="TRANSACAO" value="<?php echo $acao;?>" />
<input type="hidden" name="PARCELAS" value="<?php echo $parcela;?>" />
<input type="hidden" name="FILIACAO" value="<?php echo $afi;?>" />
<input type="hidden" name="DISTRIBUIDOR" value="" />
<input type="hidden" name="BANDEIRA" value="MASTERCARD" />
<input type="hidden" name="NUMPEDIDO" value="<?php echo $fetch_order['orderid'];?>" />
<input type="hidden" name="PAX1" value="<?php echo $fetch_order['orderid'];?>" />
<input type="hidden" name="CODVER" value="<?php echo $codver;?>" />
<input type="hidden" name="URLBACK" value="<?php echo $urlloja;?>/modules/checkout/masterkomerci/retornorede.php" />
<input type="hidden" name="URLCIMA" value="<?php echo $urlloja;?>/modules/checkout/masterkomerci/topo.jpg" />
<input type="hidden" name="TARGET" value="_parent" />
</form>

<script type="text/javascript"> window.onload = function(){ document.forms[0].submit(); } </script>
