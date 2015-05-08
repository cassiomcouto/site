<?php
require_once(dirname(__FILE__).'/../../../lib/init.php');

$dados = urlencode($_POST['dados']);
$bannerId = $_POST['bannerId'];
$ToDo = $_POST['ToDo'];

$consulta = mysql_query("UPDATE spb_banners SET content='".$dados."' WHERE bannerid='".$bannerId."'") or die($erro = mysql_error());

if($consulta){
echo "result=".$dados;
}else{
echo "result=You just wrote";
}

?>