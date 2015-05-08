<?php
include "../init.php";
ini_set("display_errors", 1);

function avisar($produto){
$a = "select * from [|PREFIX|]products where productid = '".$produto."'";
$b = $GLOBALS['ISC_CLASS_DB']->Query($a);
$dados = $GLOBALS['ISC_CLASS_DB']->Fetch($b);
if($dados['prodcurrentinv'] > 0){
return 'avisar';
}else{
return 'naoavisar';
}
}

function email($id,$nome,$email,$produto){
require_once(ISC_BASE_PATH . "/lib/email.php");

$a = "select * from [|PREFIX|]products where productid = '".$produto."'";
$b = $GLOBALS['ISC_CLASS_DB']->Query($a);
$dados = $GLOBALS['ISC_CLASS_DB']->Fetch($b);

$site = GetConfig('ShopPath').'/modificacoes/red.php?is='.$produto;

$oqueler = fopen('../templates/__emails/avisar.html', "r");
$conteudo_arquivo = fread($oqueler, filesize('../templates/__emails/avisar.html'));
$conteudo_arquivo = str_replace('%%NOME%%',$nome,$conteudo_arquivo);
$conteudo_arquivo = str_replace('%%PRODUTO%%',$site,$conteudo_arquivo);
$conteudo_arquivo = str_replace('%%PRODUTONOME%%',$dados['prodname'],$conteudo_arquivo);

$obj_email = GetEmailClass();
$obj_email->Set('CharSet', GetConfig('CharacterSet'));
$obj_email->From(GetConfig('AdminEmail'), GetConfig('StoreName'));
$obj_email->Set("Subject", 'Produto em Estoque - '.$dados['prodname'].'');
$obj_email->AddBody("html", $conteudo_arquivo);
$obj_email->AddRecipient($email, "", "h");
$obj_email->Send();

$GLOBALS['ISC_CLASS_DB']->Query("DELETE FROM loja_avisar WHERE id='".$id."'");

echo 'Aviso de Estoque: <b>'.$email.'</b><br>';
}

$a = "SELECT * FROM `loja_avisar`";
$b = $GLOBALS['ISC_CLASS_DB']->Query($a);
while($dados = $GLOBALS['ISC_CLASS_DB']->Fetch($b)) {
$acao = avisar($dados['produto']);
if($acao=='avisar'){
@email($dados['id'],$dados['nome'],$dados['email'],$dados['produto']);
}else{
echo 'Produto ainda sem Estoque!<br>';
}
}
?>
