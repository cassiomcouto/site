<?php
	session_start() ;
	if ( !file_exists( "../web/conf-init.php" ) )
	{
		HEADER( "location: ../setup/index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php" ) ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Form.php") ;
	include_once("$DOCUMENT_ROOT/API/ASP/get.php") ;
?>
<?php

	// initialize
	$success = 0 ;
?>
<?php
	// functions
?>
<?php
	// conditions
?>
<?php include_once( "./header.php" ) ; ?>
<span id="result_box"><span title="Congratulations!" onmouseover="this.style.backgroundColor='#ebeff9'" onmouseout="this.style.backgroundColor='#fff'">Parab&eacute;ns! O </span><span title="System is successfully setup!" onmouseover="this.style.backgroundColor='#ebeff9'" onmouseout="this.style.backgroundColor='#fff'">Sistema de Atendimento Online foi instalado com sucesso!</span></span>
<p><span id="result_box"><span title="This is the super admin area.">Esta &eacute; a &aacute;rea de administra&ccedil;&atilde;o super. </span><span title="You can update your company information and customize your company logo here">Voc&ecirc; pode atualizar suas informa&ccedil;&otilde;es sobre a empresa e personalizar o logotipo de sua empresa aqui</span></span>.
<p>
[ <a href="profile.php"><span id="result_box"><span title="Your Site Profile">Perfil do Seu Site </span></span></a>]
[ <a href="customize.php"><span id="result_box"><span title="Customize Logo">Personalizar Logo</span></span></a> ]
[ <a href="dbinfo.php"><span id="result_box"><span title="Database Info">Info Banco de Dados</span></span></a> ]

<?php
	if ( file_exists( "asp.php" ) && $ASP_KEY )
		print "		<big><b>[ <a href=\"asp.php\">ASP Service Suite</a> ]</b></big>" ;
?>
<p>
<hr>
<p>Para personalizar o seu site online/off-line, &iacute;cones,  gerenciar servi&ccedil;os e usu&aacute;rios, registros de vista, e outras tarefas de  configura&ccedil;&atilde;o, acesse a &aacute;rea abaixo e fa&ccedil;a o login com seu login e senha de  Administrador do sistema e configure de acordo as suas necessidades. </p>
<p>
<big><b><a href="<?php echo $BASE_URL ?>/setup/index.php"><?php echo $BASE_URL ?>/setup/</a></b></big>
<hr>

<?php include_once( "./footer.php" ) ; ?>