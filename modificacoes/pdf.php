<?php
include('../init.php');
$ativo = GetModuleVariable('addon_catalogopdf','ativo');
if($ativo=='s'){
////////////////////////////////////////////////////
@header('Content-Type: text/pdf; charset=UTF-8');
$loja = GetConfig('ShopPath');
$nome = GetModuleVariable('addon_catalogopdf','loja');
include("MPDF45/mpdf.php");
$mpdf=new mPDF(); 
$imprimir = '';
$imprimirc = '';
class reduzir {
	
	public $version = '2.0.1';
	
	public $login = 'login_bitly';
	
	public $api_key = 'api_key';

	public $format = 'json';
	
	public $callback;
	
	public $url;
	
	protected $active = false;

	protected $fail = false;
	
	protected $action = null;
	
	public function __construct ( $login = null, $api_key = null ) {
		
		$this->format = strtolower( $this->format );
		
		$this->login 	= ( !is_null ( $login ) ) ? $login : $this->login;
		$this->api_key 	= ( !is_null ( $login ) ) ? $api_key : $this->api_key;
		
		
	}
	
	public function get (){
				
		 if( $this->format == 'json' ) {
			
			if ( !is_object ( $this->return ) ) 
				$this->return = json_decode( $this->return );
			
			if($this->return->statusCode == 'ERROR')
				$this->fail = true;
			else
				$this->fail = false;		
		
		} 
		
	}
	
	private function action ( $action ) {
		
		$this->action = $action;
		$this->active = false;
		
		$params = http_build_query ( array(
			'version'	=> $this->version,
			'login'		=> $this->login,
			'apiKey'	=> $this->api_key,
			'longUrl'	=> $this->url,
			'shortUrl'	=> $this->url,		
			'format'	=> $this->format,
			'callback'	=> $this->callback
		) );
		
	 	// Make a requisition to the Bit.ly API		
		$this->return = $this->get_file_contents ( 'http://api.bit.ly/' . $this->action . '?' . $params );
		
		// Take care of the response
		$this->get();
		
	}
	
	public function shorten ( $url = null ) {

		$this->url = ( !is_null( $url ) ) ? $url : $this->url;

		$this->action('shorten');
		
		return $this->getData()->shortUrl;
				
	}
	
	public function expand ( $url = null ) {

		$this->url = ( !is_null( $url ) ) ? $url : $this->url;
		
		$this->action('expand');
		
		return $this->getData()->longUrl;
		
	}
	
	public function info ( $url = null ) {
		
		$this->url = ( !is_null( $url ) ) ? $url : $this->url;
		
		$this->action('info');
		
		return $this->getData();
		
		
	}
	
	public function stats ( $url = null ) {
		
		$this->url = ( !is_null( $url ) ) ? $url : $this->url;

		$this->action('stats');
		
		return $this->getData();
		
		
	}
	
	public function getData() {
		
		if ( $this->active != false )
			return false;
			
		 if ( $this->format == 'json' ) {

        	if ( $this->fail != true ) {
				
				$ar_object_vars = get_object_vars ( $this->return->results );
				$ar_object_keys = array_keys ( $ar_object_vars );
				$node = $ar_object_keys[0];
				
				if ( $this->action != 'stats' )
		 			return 	$this->return->results->$node;
				else
					return $this->return->results;
		
			} else {
				
			$this->debug();
			}
         
		
		 } elseif ( $formato == 'xml' ) {
         
		 	return $this->return;
         
		 }
		
	}
	
	private function get_file_contents ( $url ) {
	
		if ( function_exists( 'curl_init' ) ) {

			$curl = curl_init ();
			curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $curl, CURLOPT_URL, $url );
			$contents = curl_exec ( $curl );
			curl_close ( $curl );

			if ( $contents ) 
				return $contents;
			else 
				return false;
				
		} else {
			return file_get_contents ( $url );
		}
		
	}
	
	public function debug () {
		
		echo "<pre>"; 
		print_r( $this->return ); 
		echo "</pre>";
		
	}
	
}

$mpdf->WriteHTML(utf8_encode('<a name="Pagina Inicial"></a>'));
$cont = 1;
$categorias = "select * from [|PREFIX|]categories where catvisible = 1 order by catnsetleft ASC";
$resultado = $GLOBALS['ISC_CLASS_DB']->Query($categorias);
$imprimirc .= '<b> <a href="#Pagina Inicial"> -> Pagina Inicial</a></b><br>';
while ($linhas = $GLOBALS['ISC_CLASS_DB']->Fetch($resultado)) {
//while categorias
if($linhas['catparentid']==0){
$imprimirc .= '<b><a href="#'.$linhas['catname'].'"> -> '.$linhas['catname'].'</a></b><br>';
}else{
$imprimirc .= '<a href="#'.$linhas['catname'].'"> - '.$linhas['catname'].'</a><br>';
}

$query = "select * from [|PREFIX|]products where [|PREFIX|]products.prodvisible = '1' and [|PREFIX|]products.prodcatids LIKE '".$linhas['categoryid']."%'";
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

$imprimir .= '<a name="'.$linhas['catname'].'"></a>';

$imprimir .= "<table width='100%' border='1' frame='void'>";

while ($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {

$image = "select * from [|PREFIX|]product_images where imageprodid = '".$row['productid']."' and imageisthumb = '1'";
$im = $GLOBALS['ISC_CLASS_DB']->Query($image);
$img = $GLOBALS['ISC_CLASS_DB']->Fetch($im);
$valorPa =number_format($row['prodcalculatedprice'], 2, ',', '');

$url = ProdLink($row['prodname']);

$u = GetModuleVariable('addon_catalogopdf','url');
if($u=='s'){
$red = new reduzir('brunoalencar','R_868a718fd50b9ef72874b456b90b9d30');
$url = $red->shorten($url);
}

$normalContent = strip_tags($row["proddesc"]);
$smallContent = substr($normalContent, 0, 100);
if (strlen($normalContent) > 101 && substr($smallContent, -1, 1) !== ".") {
$smallContent .= ".";
}

if($cont%2==0){
$cor = GetModuleVariable('addon_catalogopdf','cor2');
}else{
$cor = GetModuleVariable('addon_catalogopdf','cor1');
}

$imprimir .= "<tr>";
if(!empty($img['imagefile'])) {
$imprimir .= "<td width='160' height='120' align='center' bgcolor='".$cor."'>
<img src='".$loja."/miniatura.php?w=130&img=product_images/".$img['imagefile']."' border='1'>
</td>

<td width='100%' bgcolor='".$cor."'>
<b>Nome:</b> ".$row['prodname']." <b>ID#:</b> ".$row['productid']."<br>
<b>Valor:</b> ".CurrencyConvertFormatPrice($valorPa, 1, 0)."<br>
<b>Data:</b> ".date('d/m/Y')."<br>
<b>Detalhes:</b> <i>".$smallContent."</i><br>
<b>Link:</b> <a href='".$url."' target='_blank'>".$url."</a>
</td>
";
}else{
$imprimir .= "<td width='160' height='120' align='center' bgcolor='".$cor."'>
<img src='".$loja."/modificacoes/miniatura.php?w=130&img=sem.jpg' border='1'>
</td>

<td width='100%'>
<b>Nome:</b> ".$row['prodname']." <b>ID#:</b> ".$row['productid']."<br>
<b>Valor:</b> ".CurrencyConvertFormatPrice($valorPa, 1, 0)."<br>
<b>Data:</b> ".date('d/m/Y')."<br>
<b>Detalhes:</b> <i>".$smallContent."</i><br>
<b>Link:</b> <a href='".$url."' target='_blank'>".$url."</a>
</td>
";
}
$imprimir .= "</tr>";


if($cont%6==0){
$mpdf->setFooter(utf8_encode('Catalogo Virtual '.$nome.' ('.$loja.') - Pagina: {PAGENO}'));
}

$cont++;

}

$imprimir .= "</table>";

//fim while categoria
}
////////////////////////

//echo $imprimir;
$cop = base64_decode(base64_decode('ZDNkM0xteHZhbUUxTG1OdmJTNWljZz09'));
$mpdf->SetTitle('Catalogo Virtual de Produtos '.$nome.' - Gerado por '.$loja);
$mpdf->SetAuthor('Copyright © 2012 Nkt Sistemas contato@noisketa.com.br - '.$cop);
$mpdf->SetCreator('Copyright © 2012 Nkt Sistemas Catalogo PDF 1.0- '.$cop);
$imagemi = GetModuleVariable('addon_catalogopdf','imagem');
$mpdf->WriteHTML(utf8_encode('<div align="center">
<br><br><br><br>
<img src="'.$imagemi.'">
<br>
</div>
<div align="center">
<font size="12">'.$nome.'</font>
</div>'));
$mpdf->AddPage();
$mpdf->WriteHTML(utf8_encode('<h2>Departamentos da Loja</h2>'));
$mpdf->WriteHTML(utf8_encode($imprimirc));
$mpdf->AddPage();
$mpdf->WriteHTML(utf8_encode($imprimir));
$mpdf->AddPage();
$mpdf->WriteHTML(utf8_encode('<div align="center">
<em>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br>Catalogo PDF 2.2 - Powered By  <a href="http://www.scriptphpbr.com.br/"> Copyright © 2012 SCRIPT PHP BR</a><br>
2012 - Todos os direitos reservados.<br>
<br>
</em>
</div>'));
$mpdf->Output();
exit;
/////////////////////////////////////////////////////////////////
}else{
echo "<script type='text/javascript'>
alert('Catalogo PDF Esta Desativado na Loja!');
location.href = '".GetConfig("ShopPath")."';
</script>";
}
?>
