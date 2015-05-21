<?php
include('../init.php');
$loja = GetConfig('ShopPath');
$nome = GetConfig('CompanyName');

$query = "select * from [|PREFIX|]products where prodvisible = '1' ORDER BY prodname ASC";
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
$imprimir .= "<table width='100%' border='0'>";
while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
$image = "select * from [|PREFIX|]product_images where imageprodid = '".$row['productid']."' and imageisthumb = '1'";
$im = $GLOBALS['ISC_CLASS_DB']->Query($image);
$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);
$valorPa =number_format($row['prodcalculatedprice'], 2, ',', '');
$url = ProdLink($row['prodname']);

$normalContent = strip_tags($row["proddesc"]);
				$smallContent = substr($normalContent, 0, 149);

				if (strlen($normalContent) > 150 && substr($smallContent, -1, 1) !== ".") {
				$smallContent .= " ...";
}

$imprimir .= "<tr>";
$imprimir .= "<td colspan='2'><h3>&nbsp;&nbsp;&nbsp;".$row['prodname']."</h3></td>";
$imprimir .= "</tr>";
$imprimir .= "<tr>";
if(!empty($img['imagefile'])) {
$imprimir .= "<td width='160'>&nbsp;&nbsp;&nbsp;<img src='miniatura.php?w=150&img=../product_images/".$img['imagefile']."' border='1'></td>
<td width='100%'>
<b>ID#:</b> ".$row['productid']."<br>
<b>Preço:</b> ".$valorPa." R$<br>
<b>Data do Preço:</b> ".date('d/m/Y')."<br>
<b>Descrição:</b> <i>".$smallContent."</i><br>
<b>URL:</b> <a href='".$url."' target='_blank'>".$loja."/modificacoes/red.php?is=".$row['productid']."</a><br>
<pre>Obs: Os preços e estoque poderam sobre modificações sem aviso previo no periodo.</pre>
</td>
";
}else{
$imprimir .= "<td width='160'>&nbsp;&nbsp;&nbsp;<img src='miniatura.php?w=150&img=sem.jpg' border='1'></td>
<td width='100%'>
<b>ID#:</b> ".$row['productid']."<br>
<b>Preço:</b> ".$valorPa." R$<br>
<b>Data do Preço:</b> ".date('d/m/Y')."<br>
<b>Descrição:</b> <i>".$smallContent."</i><br>
<b>URL:</b> <a href='".$url."' target='_blank'>".$loja."/red.php?id=".$row['productid']."</a><br>
<pre>Obs: Os preços e estoque poderam sobre modificações sem aviso previo no periodo.</pre>
</td>
";
}
$imprimir .= "</tr>";
$imprimir .= "<tr>";
$imprimir .= "<td colspan='2'><hr></td>";
$imprimir .= "</tr>";
$imprimir .= "<tr>";
}
$imprimir .= "</table>";



echo $imprimir;
?>