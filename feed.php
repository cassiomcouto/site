<?php
require_once(dirname(__FILE__)."/init.php");
//////////////////////////////////////////////
echo "<productslider>
<config>
<version>scriptphpbr.com.br</version> 
<alignment>center</alignment>
<spacing>20</spacing>
<margin>50</margin>
<scaleSize>1.2</scaleSize>
<enableToolTips>0</enableToolTips>
<enableSound>1</enableSound>
<stageColor>0xFFFFFF</stageColor>
<iconColor>0x000000</iconColor>
<imageHeight>100</imageHeight>
<imageWidth>100</imageWidth>
<scrollSpeed>7</scrollSpeed>

<autoScale>1</autoScale>
<sliderDirection>horizontal</sliderDirection>
<enableIcon>1</enableIcon>
<enableFixedToolTips>1</enableFixedToolTips>
</config>
<products>";

$query = "select * from [|PREFIX|]products ORDER BY rand() LIMIT 0,15";
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {
	
	echo $row['prodname'];
$image = "select * from [|PREFIX|]product_images where imageprodid = '$row[productid]' and imageisthumb = '1'";
$im = $GLOBALS['ISC_CLASS_DB']->Query($image);
$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);
$valorPa =number_format($row['prodcalculatedprice'], 2, ',', '');
$url = ProdLink($row['prodname']);
//$urls = $GLOBALS['ShopPath'];

echo"<product> 
<productName><![CDATA[".$row['prodname']."]]></productName> 
<imagePath><![CDATA[miniatura.php?w=120&img=product_images/".$img['imagefile']."]]></imagePath> 
<targetURL><![CDATA[".$url."]]></targetURL> 
<productPrice><![CDATA[".$valorPa."]]></productPrice> 
<productCurrency><![CDATA[R$]]></productCurrency> 
</product>";
}
echo "</products>
</productslider>";
?>



