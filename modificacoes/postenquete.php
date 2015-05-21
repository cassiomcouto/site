<?php
include('../init.php');
if($_POST['tipo']=='votar'){
@$GLOBALS['ISC_CLASS_DB']->Query("UPDATE `enquete_opcoes` SET `opcao_votos` = opcao_votos+1 WHERE `opcao_id` = '".$_POST['voto']."';");
@isc_setCookie("enquete_".$_POST['enquete'], $_POST['enquete'], time()+86400);

echo ResEnqueteVoto($_POST['enquete']);

}

function ResEnqueteVoto($id){

$html = '<div class="BlockContent">';

$q3 = "SELECT * FROM enquete_titulo WHERE id_titulo='".$id."'";
$r3 = $GLOBALS['ISC_CLASS_DB']->Query($q3);
$titulo = $GLOBALS['ISC_CLASS_DB']->Fetch($r3);

$html .= "<b>".$titulo['titulo_enquete']."</b><br><br>";

$q1 = "SELECT SUM(opcao_votos) AS total FROM enquete_opcoes WHERE titulo_id='".$id."'";
$r1 = $GLOBALS['ISC_CLASS_DB']->Query($q1);
$total = $GLOBALS['ISC_CLASS_DB']->Fetch($r1);

$variacao = (100/$total['total']);

$html .= "<ul>";

$q2 = "SELECT * FROM enquete_opcoes WHERE titulo_id='".$id."' ORDER BY opcao_votos DESC";
$r2 = $GLOBALS['ISC_CLASS_DB']->Query($q2);
while($opcoes = $GLOBALS['ISC_CLASS_DB']->Fetch($r2)){

$html .= "<li>&nbsp;".$opcoes['opcao']." <b>".number_format(($opcoes['opcao_votos']*$variacao), 1, '.', '.')."%</b></li>";

}

$html .= "</ul>";

$html .= "<center><br>Total de <b>".$total['total']."</b> votos!</center>";

$html .= "</div>";

return $html;

}
?>