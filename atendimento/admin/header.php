<?php
	$l = $_SESSION['session_admin'][$sid]['asp_login'] ;
	if ( isset( $LOGO ) && file_exists( "$DOCUMENT_ROOT/web/".$_SESSION['session_admin'][$sid]['asp_login']."/$LOGO" ) && $LOGO )
		$logo = "$BASE_URL/web/".$_SESSION['session_admin'][$sid]['asp_login']."/$LOGO" ;
	else if ( file_exists( "$DOCUMENT_ROOT/web/$LOGO_ASP" ) && $LOGO_ASP )
		$logo = "$BASE_URL/web/$LOGO_ASP" ;
	else
		$logo = "$BASE_URL/images/logo.gif" ;
?>
<html>
<head>
<title> Operador </title>
<?php $css_path = "../" ; include( "../css/default.php" ) ; ?>
<script language="JavaScript" src="../js/global.js"></script>
<script language="JavaScript">
var section = <?php echo $section ?> ;	// Section number
var rating_str = 'Você Avaliado no Atendimento - <font color="#48648C"><?php echo $ave_rating_string ?></font>' ;
</script>
<script language="JavaScript" src="../js/admin.js"></script>
</head>
<body onload="init()" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:100%">
  <tr> 
	<td height="65" valign="top" class="bgHead">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
			<td width="102" height="65" rowspan="2" valign="bottom" class="bgCornerTop"><img src="../images/bg_corner_top.gif" width="102" height="65"></td>
			<td height="65" align="left" rowspan="2"><div id="logo"><a href="index.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>"><img src="<?php echo $logo ?>" border="0"></a></div></td>
			<td align="right" valign="top">&nbsp;(If you are not <?php echo stripslashes( $admin['name'] ) ?>, <a href="../index.php?action=logout&sid=<?php echo $sid ?>&l=<?php echo $l ?>">clique aqui</a>)<strong>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="../index.php?action=logout&sid=<?php echo $sid ?>&l=<?php echo $l ?>">Sair</a>&nbsp;&nbsp;</strong></td>
		</tr>
		</table>
	  </td>
  </tr>
  <tr> 
	<td height="47" valign="top" class="bgMenuBack"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		  <td width="100%"><img src="../images/bg_corner_bot.gif" width="121" height="47"></td>
		  <td width="427" valign="top" class="bgNav"><table width="427" border="0" cellspacing="0" cellpadding="1">
			  <tr> 
				<td height="24" align="center" class="nav"><b> <div style="position:relative" id="navigation">&nbsp;</div></b></td>
			  </tr>
			</table></td>
		  <td width="10"><img src="../images/spacer.gif" width="10" height="1"></td>
		</tr>
	  </table></td>
  </tr>
  <tr> 
	<td valign="top" class="bg">

	<center>
	| <a href="index.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>">Inicial</a> |
	<a href="canned.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>&action=canned_responses">Respostas</a> |
	<a href="canned.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>&action=canned_commands">Comandos</a> |
	<?php if ( $INITIATE && file_exists( "$DOCUMENT_ROOT/admin/traffic/admin_puller.php" ) ): ?>
	<a href="canned.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>&action=canned_initiate">Mensagens Inicial</a> |
	<?php endif; ?>
	<a href="index.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>&action=edit_password">Preferencias</a> |
	<a href="index.php?sid=<?php echo $sid ?>&deptid=<?php echo $deptid ?>&action=spam">Spam Bloqueio</a> |
	</center>
	<br>

	<!-- **** Start of the page body area **** -->
	
