<?php
header("Content-Type: text/html; charset=ISO-8859-1");
$p_cod_lis = $_POST['P_COD_LIS'];
if($p_cod_lis == '') {
$url = "http://websro.correios.com.br/sro_bin/txect01$.Inexistente?P_LINGUA=001&P_TIPO=002&P_COD_LIS=".$_GET['CodRastreio'];
$conecurl = @fopen("$url","r") or die ('<div class="ContentFrete"><strong>Código de Rastreio: '.$_GET['CodRastreio']."</strong><br><br>".'<center>erro na conexão</center></div>');
while(!feof($conecurl)) {
$lin .= fgets($conecurl,4096);
}
fclose($conecurl);

$lin = strtolower($lin);

$rest = substr($lin,0);
$nprimetable = strpos($rest,'<table ');
$fechatable = strpos($rest,'<hr ');
$quantopula = $fechatable - $nprimetable ;
$conteudo = substr($lin, $nprimetable ,$quantopula);

function get_anchor($html)
{
$er = "/<td.*?>.*?<\/td>/";
preg_match_all($er,$html,$links);
$link = $links[0];
return $link;
}

function get_label($url)
{
$label = str_replace("</td>","",preg_replace("/^/","",$url));
return $label;
}

echo '<div class="ContentFrete"><strong>Código de Rastreio: '.$_GET['CodRastreio']."</strong><br><br>".$conteudo."</div>";


}
?>