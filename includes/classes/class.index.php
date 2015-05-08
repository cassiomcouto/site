<?php

	class ISC_INDEX
	{
		public function HandlePage()
		{
			// No action here, just show the home page
			$this->ShowHomePage();
		}
public function Twitter()
{
$a = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'twitter'";
$b = $GLOBALS['ISC_CLASS_DB']->Query($a);
$c = $GLOBALS['ISC_CLASS_DB']->Fetch($b);
$d = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'usertwitter'";
$e = $GLOBALS['ISC_CLASS_DB']->Query($d);
$f = $GLOBALS['ISC_CLASS_DB']->Fetch($e);

$GLOBALS['Twitter'] = "";
if($c['variableval']=='sim'){
$GLOBALS['Twitter'] .= '<div class="Block Twitter Moveable Panel" id="Ultimos Twitter">
<h2>Twitter</h2>';

$url = 'http://twitter.com/statuses/user_timeline/'.$f['variableval'].'.rss?count=5';  
$xml = simplexml_load_file($url);  

foreach($xml->channel->item as $node){ 
 
$GLOBALS['Twitter'] .= '<ol>';  
$GLOBALS['Twitter'] .= '<li><a href="'.$node->link.'" target="_blank">'.utf8_decode($node->title).'</a></li>';
//$GLOBALS['Twitter'] .= '<li>gggg</li>';
$GLOBALS['Twitter'] .= '</ol>';  
}

$GLOBALS['Twitter'] .= "</div>";
}
return $GLOBALS['Twitter'];  
}

		
//classe pdf
public function PDF()
{
$lers = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'pdf'";
$resultados = $GLOBALS['ISC_CLASS_DB']->Query($lers);
$ss = $GLOBALS['ISC_CLASS_DB']->Fetch($resultados);

if($ss['variableval']=='sim'){
$GLOBALS['PDF'] = "";
$GLOBALS['PDF'] .= '<div class="Block PDF Moveable Panel" id="PDF">
<h2>Catalogo</h2>';
$GLOBALS['PDF'] .= '<div class="BlockContent" align="center">';
$GLOBALS['PDF'] .="<a href='modificacoes/pdf.php' target='_blank'> <img src='modificacoes/baixar.gif' border='0'></a>";
$GLOBALS['PDF'] .="</div></div>";
}else{
$GLOBALS['PDF'] ="";
}
return $GLOBALS['PDF'];
}

		
		public function Scroll()
{
$GLOBALS['ScrollHTML'] =""; 
$lers = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'scroll'";
$resultados = $GLOBALS['ISC_CLASS_DB']->Query($lers);
$ss = $GLOBALS['ISC_CLASS_DB']->Fetch($resultados);
switch($ss['variableval']) {
case 'html';
$GLOBALS['ScrollHTML'] .=  '<script type="text/javascript" src="modificacoes/jquery-1.2.2.pack.js"></script>
<link rel="stylesheet" type="text/css" href="modificacoes/featuredcontentglider.css" />
<script type="text/javascript" src="modificacoes/featuredcontentglider.js"></script>
<script type="text/javascript">
featuredcontentglider.init({
	gliderid: "canadaprovinces", //ID of main glider container
	contentclass: "glidecontent", //Shared CSS class name of each glider content
	togglerid: "p-select", //ID of toggler container
	remotecontent: "", //Get gliding contents from external file on server? "filename" or "" to disable
	selected: 0, //Default selected content index (0=1st)
	persiststate: false, //Remember last content shown within browser session (true/false)?
	speed: 700, //Glide animation duration (in milliseconds)
	direction: "rightleft", //set direction of glide: "updown", "downup", "leftright", or "rightleft"
	autorotate: true, //Auto rotate contents (true/false)?
	autorotateconfig: [3000, 20000] //if auto rotate enabled, set [milliseconds_btw_rotations, cycles_before_stopping]
})
</script>
<div id="canadaprovinces" class="glidecontentwrapper">';

$query = sprintf("select * from [|PREFIX|]products where prodfeatured = '1' ORDER BY rand() LIMIT 0,10");
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
$i = 1;
while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

$GLOBALS['ScrollHTML'] .= '<div class="glidecontent"><table><tr align=center>';

$image = sprintf("select * from [|PREFIX|]product_images where imageprodid = '".$row['productid']."' and imageisthumb = '1'");
$im = $GLOBALS['ISC_CLASS_DB']->Query($image);
$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);

$url = ProdLink($row['prodname']);

$GLOBALS['ScrollHTML'] .= "<td width=200 valign=middle><center><img src='miniatura.php?w=120&img=product_images/".$img['imagefile']."'></center></td><td width=100% align=center valign=middle><font face=arial size=5><a href='".$url."' target='_parent'>".$row['prodname']."</font></a><br><font face=arial size=4>Por Apenas: ".CurrencyConvertFormatPrice($row['prodcalculatedprice'], 1, 0)."</font></td>";


$GLOBALS['ScrollHTML'] .= "</tr></table></div>";
$i++;
}

$GLOBALS['ScrollHTML'] .=  '</div><div id="p-select" class="glidecontenttoggler"><a href="#" class="prev"><<</a>';

for($j=1;$j<$i;$j++) {
$GLOBALS['ScrollHTML'] .=  '<a href="#" class="toc">'.$j.'</a>';
}

$GLOBALS['ScrollHTML'] .= '<a href="#" class="next">>></a></div>';

break;
case 'flash';
$GLOBALS['ScrollHTML'] .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" data="scroll.swf" type="application/x-shockwave-flash" height="100%" width="100%">
<param name="movie" value="modificacoes/scroll.swf">
<param value="false" name="menu">
<param value="FFFFFF" name="bgcolor">
<param value="host=modificacoes/feed.php" name="flashvars">
<embed src="modificacoes/scroll.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="host=modificacoes/feed.php" type="application/x-shockwave-flash" wmode="transparent" height="100%" width="100%">
</object>';
break;
case 'nao';
break;

}
//imprime no html
return $GLOBALS['ScrollHTML'];
}


public function FormasdePagamento()
{
$ler = "select * from [|PREFIX|]module_vars where modulename = 'addon_parcelas' and variablename = 'tipos' order by variableval asc";
$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);
$i = 1;
$GLOBALS['HTMLFormas'] = "";
$GLOBALS['HTMLFormas'] .= '<div class="Block Formas Moveable Panel" id="Formas" align="center">
<h2>Pagamento</h2>';
$GLOBALS['HTMLFormas'] .= '<div class="BlockContent" align="center">';
while ($s = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {
//echo $s['variableval'];

$GLOBALS['HTMLFormas'] .= "<img src='modificacoes/meios/".$s['variableval'].".gif' border='0' alt='".$s['variableval']."'>";

if($i%2==0) {
$GLOBALS['HTMLFormas'] .= "<br>";
}

$i++;
}
$GLOBALS['HTMLFormas'] .= '</div></div>';
return $GLOBALS['HTMLFormas'];
}

public function FormasdeEnvio()
{
$ler = "select * from [|PREFIX|]module_vars where modulename = 'addon_simularfrete' and variablename = 'tipos' order by variableval desc";
$resultado = $GLOBALS['ISC_CLASS_DB']->Query($ler);
$i = 1;
$GLOBALS['HTMLFormasE'] = "";
$GLOBALS['HTMLFormasE'] .= '<div class="Block FormasEnvio Moveable Panel" id="FormasEnvio" align="center">
<h2>Entrega</h2>';
$GLOBALS['HTMLFormasE'] .= '<div class="BlockContent" align="center">';
while ($s = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {
//echo $s['variableval'];

$GLOBALS['HTMLFormasE'] .= "<img src='modificacoes/meios/".$s['variableval'].".gif' border='0' alt='".$s['variableval']."'>";

if($i%2==0) {
$GLOBALS['HTMLFormasE'] .= "<br>";
}

$i++;
}
$GLOBALS['HTMLFormasE'] .= '</div></div>';
return $GLOBALS['HTMLFormasE'];
}

		public function ShowHomePage()
		{
			if(isset($GLOBALS['PathInfo'][0]) && ($GLOBALS['PathInfo'][0] == 'store' || $GLOBALS['PathInfo'][0] == 'shop')) {
				$GLOBALS['ActivePage'] = 'store';
			}
			else {
				$GLOBALS['ActivePage'] = "home";
			}

			// Is there a normal page set to be the default home page?
			$pagesCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Pages');
			if($GLOBALS['ActivePage'] != 'store' && isset($pagesCache['defaultPage']) && is_array($pagesCache['defaultPage']) && $pagesCache['defaultPage']['pageid'] > 0) {
				// Load a page created from the control panel
				$GLOBALS['ISC_CLASS_PAGE'] = new ISC_PAGE($pagesCache['defaultPage']['pageid'], true, $pagesCache['defaultPage']);
				$GLOBALS['ISC_CLASS_PAGE']->ShowPage();
			}
			else {
				// Load the dynamic home page instead
				if(GetConfig('HomePagePageTitle')) {
					$title = GetConfig('HomePagePageTitle');
				}
				else {
					$title = GetConfig('StoreName');
				}
				//novas funcoes
				$this->Scroll();
				$this->FormasdePagamento();
				$this->FormasdeEnvio();
				$this->PDF();
				$this->Twitter();
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle($title);
				$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("default");
				$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
			}
		}
	}