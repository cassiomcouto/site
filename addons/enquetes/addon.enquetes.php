<?php
	require_once(dirname(__FILE__) . '/../../includes/classes/class.addon.php');
	
   /*
   atencao: ao descriptografar o codigo o usuario esta totalmente ciente da perda do suporte, e atualizacoes posteriores, alem de ser banido do sistema.
   */
	
class ADDON_ENQUETES extends ISC_ADDON
{

		public function __construct()
		{
			$this->SetId('addon_enquetes');
			$this->SetName('Enquetes 1.0');
			$this->RegisterMenuItem(array(
				'location' => 'mnuMarketing',
				'icon' => '',
				'text' => 'Enquetes - Admin',
				'description' => 'Modulo gerenciamento de enquetes da loja.',
				'id' => 'addon_enquetes'
			));

			$this->SetImage('descontos.jpg');
			$this->SetHelpText('Modulo gerenciamento de enquetes da loja.');

		}


		public function init()
		{   
		$this->ShowSaveAndCancelButtons(true);
		}
		
		public function SetCustomVars()
	    {
		$this->_variables['ativarenquetes'] = array(
			'type' => 'dropdown',
			'name' => 'Ativar Enquetes?',
			'default' => 'sim',
			'options' => array(
				'SIM' => 'sim',
				'NAO' => 'nao'

			),
			"multiselect" => false,
			'required' => true
		);
		}
		
		

		
		function EntryPoint()
		{ 
		
		@$GLOBALS['ISC_CLASS_DB']->Query("CREATE TABLE IF NOT EXISTS `enquete_opcoes` (
  `opcao_id` int(10) NOT NULL auto_increment,
  `titulo_id` int(11) NOT NULL,
  `opcao` varchar(150) NOT NULL,
  `opcao_votos` int(10) NOT NULL,
  PRIMARY KEY  (`opcao_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;");
		
		@$GLOBALS['ISC_CLASS_DB']->Query("CREATE TABLE IF NOT EXISTS `enquete_titulo` (
  `id_titulo` int(10) NOT NULL auto_increment,
  `titulo_enquete` varchar(150) NOT NULL,
  `status_enquete` enum('a','i') NOT NULL,
  PRIMARY KEY  (`id_titulo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;");
		
		echo '<h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enquetes 1.0 </h2>';
		echo '<table width="600"><tr>';
		
		echo '<td width="150" align="center" valign="top">
		<a href="index.php?ToDo=runAddon&addon=addon_enquetes&func=adicionar"><img src="../addons/enquetes/add.gif" border="0"></a>
		</td>';
		
		echo '<td width="150" align="center" valign="top">
		<a href="index.php?ToDo=runAddon&addon=addon_enquetes&func=ver"><img src="../addons/enquetes/ver.gif" border="0"></a>
		</td>';
		
		echo '</tr></table>';
		}
		
		function adicionar(){
		
		echo '<script>
		function Validar(dados){
		if(dados.titulo.value==""){
		alert("Digite Titulo da Enquete!");
		dados.titulo.focus();
		return false;
		}
		if(dados.numero.value==""){
		alert("Selecione o Numero de Opções!");
		dados.numero.focus();
		return false;
		}
		return true;
		}
		</script>
		';
		
		echo '<div class="ContentContainer">';
		echo '<h2>&nbsp;Adicionar Enquete / <a href="index.php?ToDo=runAddon&addon=addon_enquetes">Inicio</a></h2>';
		echo '<form method="post" action="index.php?ToDo=runAddon&addon=addon_enquetes&func=adicionar2" onsubmit="return Validar(this);">';
		echo '<b>Titulo:</b> <input name="titulo" id="titulo" value="" type="text" size="50"> (titulo da enquete)<br>';

		echo '<b>Numero de Opcoes:</b> <select name="numero" id="numero">';

		echo '<option value="2">2</option>';
		echo '<option value="3">3</option>';
		echo '<option value="4">4</option>';
		echo '<option value="5">5</option>';
		echo '<option value="6">6</option>';
		echo '<option value="7">7</option>';
		echo '<option value="8">8</option>';
		echo '<option value="9">9</option>';
		echo '<option value="10">10</option>';

        echo '</select> (numero de opcoes da enquete)<br>';
		
		echo '<br><input type="submit" value="Continuar ->>"></br>';

		echo '</form>';
		echo '</div>';
		}
		
		function adicionar2(){
		
		echo '<script>
		function Validar(dados){';

		for($i=1;$i<=$_POST['numero'];$i++){
		
		echo 'if(dados.opcao_'.$i.'.value==""){
		alert("Digite o valor da opcao '.$i.'!");
		dados.opcao_'.$i.'.focus();
		return false;
		}';
		
		}

		echo 'return true;
		}
		</script>
		';
		
		echo '<div class="ContentContainer">';
		echo '<h2>&nbsp;Opcoes Enquete '.$_POST['titulo'].' / <a href="index.php?ToDo=runAddon&addon=addon_enquetes">Inicio</a></h2>';
		echo '<form method="post" action="index.php?ToDo=runAddon&addon=addon_enquetes&func=adicionar3" onsubmit="return Validar(this);">';
		echo '<input type="hidden" name="titulo" value="'.$_POST['titulo'].'">';
		echo '<input type="hidden" name="numero" value="'.$_POST['numero'].'">';
		
		for($i=1;$i<=$_POST['numero'];$i++){
		
		echo '<b>Opcao '.$i.':</b> <input name="opcao_'.$i.'" id="opcao_'.$i.'" value="" type="text" size="50"><br>';
		
		}

		echo '<br><input type="submit" value="Concluir"></br>';

		echo '</form>';
		echo '</div>';
		}
		
		function adicionar3(){
		
		$numero = $_POST['numero'];
		
		$sqltitulo = $GLOBALS['ISC_CLASS_DB']->Query("INSERT INTO  `enquete_titulo` (
`id_titulo` ,
`titulo_enquete` ,
`status_enquete`
)
VALUES (
NULL ,  '".$_POST['titulo']."',  'a'
);");

$iddotitulo = $GLOBALS['ISC_CLASS_DB']->LastId($sqltitulo);

for($i=1;$i<=$numero;$i++){

@$GLOBALS['ISC_CLASS_DB']->Query("INSERT INTO  `enquete_opcoes` (
`opcao_id` ,
`titulo_id` ,
`opcao` ,
`opcao_votos`
)
VALUES (
NULL ,  '".$iddotitulo."',  '".$_POST['opcao_'.$i]."',  '0'
);");


}

echo "<script>
alert('Enquete Cadastrada com Sucesso!');
location.href='index.php?ToDo=runAddon&addon=addon_enquetes&func=ver';
</script>";
		
		}
		
		function Res($id){

$html = '';

$q3 = "SELECT * FROM enquete_titulo WHERE id_titulo='".$id."'";
$r3 = $GLOBALS['ISC_CLASS_DB']->Query($q3);
$titulo = $GLOBALS['ISC_CLASS_DB']->Fetch($r3);

$q1 = "SELECT SUM(opcao_votos) AS total FROM enquete_opcoes WHERE titulo_id='".$id."'";
$r1 = $GLOBALS['ISC_CLASS_DB']->Query($q1);
$total = @$GLOBALS['ISC_CLASS_DB']->Fetch($r1);

if($total['total']==0){
$total_a = 1;
}else{
$total_a = $total['total'];
}

$variacao = (100/$total_a);


$q2 = "SELECT * FROM enquete_opcoes WHERE titulo_id='".$id."' ORDER BY opcao_votos DESC";
$r2 = $GLOBALS['ISC_CLASS_DB']->Query($q2);
while($opcoes = $GLOBALS['ISC_CLASS_DB']->Fetch($r2)){

$html .= "".$opcoes['opcao']." <b>".number_format(($opcoes['opcao_votos']*$variacao), 1, '.', '.')."%</b></br>";

}



$html .= "Total de <b>".$total['total']."</b> votos!";


return $html;

}
		
		function ver(){
		
		echo "<h2>Lista de Enquetes / <a href='index.php?ToDo=runAddon&addon=addon_enquetes'>Inicio</a></h2>";
		
		echo "<table class='LetterSort' width='100%'>";
		
		echo "<tr class='Heading3'>";
		
		echo "<td>ID</td>";
		
		echo "<td>Titulo</td>";
		
		echo "<td>Opcoes</td>";

		echo "<td>Mostra</td>";
		
		echo "<td>Deletar</td>";
		
		echo "</tr>";
		
		$query = "SELECT * FROM  `enquete_titulo` ORDER BY id_titulo DESC";
	    $result = @$GLOBALS['ISC_CLASS_DB']->Query($query);
		$total_usuarios = @$GLOBALS['ISC_CLASS_DB']->CountResult($query);
		
		if($total_usuarios==0){
		
		echo "<tr class='GridRow'>";
		
		echo "<td colspan='4'>Nenhuma enquete foi encontada um sua loja!</td>";
		
		echo "</tr>";
		
		}else{
		
		while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)){
		
		$res = $this->Res($row['id_titulo']);
		
		echo "<tr class='GridRow'>";
		
		echo "<td>".$row['id_titulo']."</td>";
		
		echo "<td>".$row['titulo_enquete']."</td>";
		
		echo '<td><img onmouseout="HideHelp(\'opcoes\');" onmouseover="ShowHelp(\'opcoes\', \''.$row['titulo_enquete'].'\', \''.$res.'\')" src="images/find.gif" border="0"><div style="display: none;" id="opcoes"></div></td>';

        if($row['status_enquete']=='a'){

		echo '<td><a href="index.php?ToDo=runAddon&addon=addon_enquetes&func=inativo&id='.$row['id_titulo'].'" onclick="return confirm(\'Tem certeza em desativar a enquete?\');"><img src="images/success.gif" border="0"></a></td>';

        }else{

	echo '<td><a href="index.php?ToDo=runAddon&addon=addon_enquetes&func=ativar&id='.$row['id_titulo'].'" onclick="return confirm(\'Tem certeza em ativar a enquete?\');"><img src="images/cross.gif" border="0"></a></td>';

        }
		
		echo '<td><a href="index.php?ToDo=runAddon&addon=addon_enquetes&func=deletar&id='.$row['id_titulo'].'" onclick="return confirm(\'Tem certeza em excluir a enquete?\');"><img src="images/cross.gif" border="0"></a></td>';
		
		echo "</tr>";
		
		}
		}
		
		echo "</table>";
		
		}
		
		function deletar(){
		@$GLOBALS['ISC_CLASS_DB']->Query("DELETE FROM enquete_titulo WHERE id_titulo = '".$_GET['id']."'");
		@$GLOBALS['ISC_CLASS_DB']->Query("DELETE FROM enquete_opcoes WHERE titulo_id = '".$_GET['id']."'");
		@header("Location: index.php?ToDo=runAddon&addon=addon_enquetes&func=ver");
		}

		function inativo(){
		@$GLOBALS['ISC_CLASS_DB']->Query("UPDATE enquete_titulo SET status_enquete = 'i' WHERE id_titulo = '".$_GET['id']."'");
		@header("Location: index.php?ToDo=runAddon&addon=addon_enquetes&func=ver");
		}

		function ativar(){
		@$GLOBALS['ISC_CLASS_DB']->Query("UPDATE enquete_titulo SET status_enquete = 'a' WHERE id_titulo = '".$_GET['id']."'");
		@header("Location: index.php?ToDo=runAddon&addon=addon_enquetes&func=ver");
		}

		
		
}
	

?>