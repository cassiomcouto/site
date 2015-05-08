<?php
require_once(dirname(__FILE__)."/init.php");
$query = sprintf("select * from [|PREFIX|]products");
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
$marcaid = $row['prodbrandid'];
$catid = $row['prodcatids'];
$pre = $row['prodcalculatedprice'];
$desc = strip_tags($row['proddesc']);
$url = ProdLink($row['prodname']);
$shop = GetConfig('ShopPath');

$mar = sprintf("select * from [|PREFIX|]brands where brandid = '".$marcaid."'");
$resultm = $GLOBALS['ISC_CLASS_DB']->Query($mar);
$rowm = $GLOBALS['ISC_CLASS_DB']->Fetch($resultm);

$cat = sprintf("select * from [|PREFIX|]categories where categoryid = '".$catid."'");
$resultc = $GLOBALS['ISC_CLASS_DB']->Query($cat);
$rowc = $GLOBALS['ISC_CLASS_DB']->Fetch($resultc);

$image = sprintf("select * from [|PREFIX|]product_images where imageprodid = '".$row['productid']."' and imageisthumb = '1'");
$im = $GLOBALS['ISC_CLASS_DB']->Query($image);
$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);
echo $rowc['catname']."|".$rowm['brandname']."||".$row['productid']."|".$row['prodname']."|".$desc."|".$url."|".$shop."/".$img['imagefile']."|".$pre."|BRL\n";
}
?>