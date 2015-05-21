<?php

require_once(dirname(__FILE__).'/lib/init.php');



define("APP_ROOT", dirname(__FILE__));



define("SEARCH_SIMPLE", 0);

define("SEARCH_ADVANCED", 1);



if (GetConfig('isSetup') === false) {

	header("Location: admin/index.php");

	die();

}



$GLOBALS['PathInfo'] = array();

$GLOBALS['RewriteRules'] = array(

	"index" => array(

		"class" => "class.index.php",

		"name" => "ISC_INDEX",

		"global" => "ISC_CLASS_INDEX"

	),

	"loja" => array(

		"class" => "class.index.php",

		"name" => "ISC_INDEX",

		"global" => "ISC_CLASS_INDEX"

	),

	"shop" => array(

		"class" => "class.index.php",

		"name" => "ISC_INDEX",

		"global" => "ISC_CLASS_INDEX"

	),

	"produto" => array(

		"class" => "class.product.php",

		"name" => "ISC_PRODUCT",

		"global" => "ISC_CLASS_PRODUCT"

	),

	"conteudo" => array(

		"class" => "class.page.php",

		"name" => "ISC_PAGE",

		"global" => "ISC_CLASS_PAGE"

	),

	"categoria" => array(

		"class" => "class.category.php",

		"name" => "ISC_CATEGORY",

		"global" => "ISC_CLASS_CATEGORY"

	),

	"marca" => array(

		"class" => "class.brands.php",

		"name" => "ISC_BRANDS",

		"global" => "ISC_CLASS_BRANDS"

	),

	"blog" => array(

		"class" => "class.news.php",

		"name" => "ISC_NEWS",

		"global" => "ISC_CLASS_NEWS"

	),

	"comparar" => array(

		"class" => "class.compare.php",

		"name" => "ISC_COMPARE",

		"global" => "ISC_CLASS_COMPARE"

	),

	"404" => array(

		"class" => "class.404.php",

		"name" => "ISC_404",

		"global" => "ISC_CLASS_404"

	),

	"tags" => array(

		"class" => "class.tags.php",

		"name" => "ISC_TAGS",

		"global" => "ISC_CLASS_TAGS"

	),

	"vendedor" => array(

		"class" => "class.vendors.php",

		"name" => "ISC_VENDORS",

		"global" => "ISC_CLASS_VENDORS"

	),

	"sitemap" => array(

		"class" => "class.sitemap.php",

		"name" => "ISC_SITEMAP",

		"global" => "ISC_CLASS_SITEMAP"

	)

);



$GLOBALS['RewriteURLBase'] = '';



// Initialise our session

require_once(ISC_BASE_PATH . "/includes/classes/class.session.php");

$GLOBALS['ISC_CLASS_SESSION'] = new ISC_SESSION();



// Is purchasing disabled in the store?

if(!GetConfig("AllowPurchasing")) {

	$GLOBALS['HidePurchasingOptions'] = "none";

}



// Are prices disabled in the store?

if(!GetConfig("ShowProductPrice")) {

	$GLOBALS['HideCartOptions'] = "none";

}



// Is the wishlist disabled in the store?

if(!GetConfig("EnableWishlist")) {

	$GLOBALS['HideWishlist'] = "none";

}



// Is account creation disabled in the store?

if(!GetConfig("EnableAccountCreation")) {

	$GLOBALS['HideAccountOptions'] = "none";

}





// Setup our currency. If we don't have one in our session then get/set our currency based on our geoIP location

SetupCurrency();



// Do we need to show the cart contents side box at all?

if(!isset($_SESSION['CART']['ITEMS']) || count($_SESSION['CART']['ITEMS']) == 0) {

	$GLOBALS['HidePanels'][] = "SideCartContents";

}



$GLOBALS['ISC_CLASS_TEMPLATE'] = new TEMPLATE("ISC_LANG");

$GLOBALS['ISC_CLASS_TEMPLATE']->FrontEnd();

$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplateBase(ISC_BASE_PATH . "/templates");

$GLOBALS['ISC_CLASS_TEMPLATE']->panelPHPDir = ISC_BASE_PATH . "/includes/display/";

$GLOBALS['ISC_CLASS_TEMPLATE']->templateExt = "html";

$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate(GetConfig("template"));



$GLOBALS['ISC_CLASS_VISITOR'] = GetClass('ISC_VISITOR');



if(isset($GLOBALS['ShowStoreUnavailable'])) {

	$GLOBALS['ErrorMessage'] = GetLang('StoreUnavailable');

	$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("error");

	$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();

	exit;

}



// Set the default page title

$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(GetConfig('StoreName'));



// Get the number of items in the cart if any

if(isset($_SESSION['CART']['NUM_ITEMS'])) {

	$num_items = $_SESSION['CART']['NUM_ITEMS'];

	foreach($_SESSION['CART']['ITEMS'] as $item) {

		if(!isset($item['product_id'])) {

			continue;

		}

		$GLOBALS['CartQuantity'.$item['product_id']] = $item['quantity'];

	}

	if ($num_items == 1) {

		$GLOBALS['CartItems'] = GetLang('OneItem');

	} else if ($num_items > 1) {

		$GLOBALS['CartItems'] = sprintf(GetLang('XItems'), $num_items);

	} else {

		$GLOBALS['CartItems'] = '';

	}

}



// Define our checkout link to use

$GLOBALS['CheckoutLink'] = CheckoutLink();



// If there's a design mode token in the URL, grab it, cookie it and then redirect to the current page.

// If we don't redirect and instead output the page, it's possible to grab the authenticaiton token

// from the URL via CSRF etc.

if(!empty($_GET['designModeToken']) && getClass('ISC_ADMIN_AUTH')->isDesignModeAuthenticated($_GET['designModeToken'])) {

	isc_setCookie('designModeToken', $_GET['designModeToken'], 0, true);

	ob_end_clean();

	header('Location: '.getConfig('ShopPathNormal'));

	exit;

}
//funcionalidade para enquete

function ResEnquete($id){



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

function Enquete(){

$html = "<script>

function Votar(id,titulo){


var params = 'tipo=votar&voto='+id+'&enquete='+titulo;

$.ajax({
   type: 'post',
   url: 'modificacoes/postenquete.php',
   data: params,

   beforeSend: function(){
      $('#loading').fadeIn();
   },

   success: function(data){
   $('#loading').html(data);
   },
   
});

}";

$html .= "</script>";

$q1 = "SELECT * FROM enquete_titulo WHERE status_enquete='a' ORDER BY RAND()";
$r1 = $GLOBALS['ISC_CLASS_DB']->Query($q1);
$t1 = @$GLOBALS['ISC_CLASS_DB']->CountResult($r1);
$titulo = $GLOBALS['ISC_CLASS_DB']->Fetch($r1);

$html .= "<div id='loading' name='loading'><b>".$titulo['titulo_enquete']."</b><br>";

$q2 = "SELECT * FROM enquete_opcoes WHERE titulo_id='".$titulo['id_titulo']."'";
$r2 = $GLOBALS['ISC_CLASS_DB']->Query($q2);
while($opcoes = $GLOBALS['ISC_CLASS_DB']->Fetch($r2)){

$html .= "<input type='radio' name='opcao' id='opcao' onclick='Votar(".$opcoes['opcao_id'].",".$titulo['id_titulo'].");' value='".$opcoes['opcao_id']."'> ".$opcoes['opcao']."</br>";

}

$html .= "</div></div>";

foreach($_COOKIE as $a => $b){

$dados = explode('_',$a);

if($dados[0]=='enquete' && $titulo['id_titulo']==$b){

return ResEnquete($b);

}

}

if($t1>=1){

return $html;

}else{

return "Nenhuma Enquete!";

}

}

$GLOBALS['Enquete'] = Enquete();
//fim enquete