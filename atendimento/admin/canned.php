<?php
	session_start() ;
	$sid = $action = $deptid = $cannedid = $ave_rating_string = $prev_action = "" ;
	$updated = 0 ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : $_POST['sid'] ;

	include_once( "../API/Util_Dir.php" ) ;
	if ( !isset( $_SESSION['session_admin'][$sid]['asp_login'] ) || !Util_DIR_CheckDir( "..", $_SESSION['session_admin'][$sid]['asp_login'] ) )
	{
		HEADER( "location: ../index.php?e2" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/".$_SESSION['session_admin'][$sid]['asp_login']."/".$_SESSION['session_admin'][$sid]['asp_login']."-conf-init.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php" ) ;
	include_once("$DOCUMENT_ROOT/API/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/update.php") ;
?>
<?php
	// initialize
	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "20" ;
		$text_width_long = "35" ;
		$textbox_width = "70" ;
	}
	else
	{
		$text_width = "10" ;
		$text_width_long = "20" ;
		$textbox_width = "40" ;
	}

	// check to make sure session is set.  if not, user is not authenticated.
	// send them back to login
	if ( !$_SESSION['session_admin'][$sid]['admin_id'] )
	{
		HEADER( "location: ../index.php?e3" ) ;
		exit ;
	}

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['prev_action'] ) ) { $prev_action = $_POST['prev_action'] ; }
	if ( isset( $_GET['prev_action'] ) ) { $prev_action = $_GET['prev_action'] ; }
	if ( isset( $_POST['deptid'] ) ) { $deptid = $_POST['deptid'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_POST['cannedid'] ) ) { $cannedid = $_POST['cannedid'] ; }
	if ( isset( $_GET['cannedid'] ) ) { $cannedid = $_GET['cannedid'] ; }

	if ( ( $action == "canned_responses" ) || ( $prev_action == "canned_responses" ) )
		$section = 1;
	else if ( ( $action == "canned_commands" ) || ( $prev_action == "canned_commands" ) )
		$section = 2 ;
	else if ( ( $action == "canned_initiate" ) || ( $prev_action == "canned_initiate" ) )
		$section = 4 ;

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = "&nbsp;";
?>
<?php
	// functions
?>
<?php
	// conditions
	
	if ( $action == "add_canned" )
	{
		$action = $_POST['prev_action'] ;
		if ( $cannedid )
			ServiceCanned_update_Canned( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $cannedid, $deptid, $_POST['name'], $_POST['message'] ) ;
		else
			ServiceCanned_put_UserCanned( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $deptid, $_POST['type'], $_POST['name'], $_POST['message'] ) ;
		$cannedid = 0 ;
		$deptid = 0 ;
		$updated = 1 ;
	}
	else if ( $action == "delete_canned" )
	{
		$action = $_GET['prev_action'] ;
		ServiceCanned_remove_UserCanned( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $cannedid ) ;
		$cannedid = 0 ;
		$deptid = 0 ;
	}

	$admin = AdminUsers_get_UserInfo( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], $_SESSION['session_admin'][$sid]['aspID'] ) ;
	$admin_departments = AdminUsers_get_UserDepartments( $dbh, $_SESSION['session_admin'][$sid]['admin_id'] ) ;

	$admin_dept_hash = Array() ;
	$admin_dept_hash[0] = "Todos meus Departamentos" ;
	$admin_dept_select_string = "" ;
	for ( $c = 0; $c < count( $admin_departments ); ++$c )
	{
		$department = $admin_departments[$c] ;
		$admin_dept_select_string .= "deptID = $department[deptID] OR " ;
		$admin_dept_hash[$department['deptID']] = stripslashes( $department['name'] ) ;
	}
	$admin_dept_select_string = substr( $admin_dept_select_string, 0, strlen( $admin_dept_select_string ) - 3 ) ;

?>
<?php include_once("./header.php") ; ?>
<script language="JavaScript">
<!--
	function do_submit()
	{
		if ( ( document.form.name.value == "" ) || ( document.form.message.value == "" ) )
			alert( "todos os campos são nescessarios." ) ;
		else
			document.form.submit() ;
	}

	function do_delete( cannedid )
	{
		if ( confirm( "você tem certeza em deletar?" ) )
			location.href = "canned.php?action=delete_canned&prev_action=<?php echo $action ?>&deptid=<?php echo $deptid ?>&sid=<?php echo $sid ?>&cannedid="+cannedid ;
	}

	function put_command(selected_index)
	{
		if ( selected_index > 0 )
			document.form.message.value = document.form.command[selected_index].value ;
	}

	function check_command()
	{
		if ( document.form.command.selectedIndex == 0 )
		{
			alert( "selecione o primeiro comando." ) ;
			document.form.command.focus() ;
		}
	}

	function open_help( action )
	{
		url = "<?php echo $BASE_URL ?>/help.php?action=" + action ;
		newwin = window.open(url, "help", "scrollbars=yes,menubar=no,resizable=1,location=no,width=350,height=250") ;
		newwin.focus() ;
	}
//-->
</script>

<?php
	if ( $action == "canned_responses" ):
	$canneds = ServiceCanned_get_UserCannedByType( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], 0, 'r', $admin_dept_select_string ) ;
	$cannedinfo = ServiceCanned_get_CannedInfo( $dbh, $cannedid, $_SESSION['session_admin'][$sid]['admin_id'] ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Operador: Respostas</span><br>
		Para Respostas Rapidas e Frequentes.</p>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<th align="left" nowrap>Referencia</th>
		<th align="left">Menssagem</th>
		<th align="left">Departamento</th>
		<th align="center">&nbsp;</th>
		<th align="center">&nbsp;</th>
	  </tr>
	  <?php
			for ( $c = 0; $c < count( $canneds ); ++$c )
			{
				$canned = $canneds[$c] ;

				$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
				$canned_message = nl2br( Util_Format_ConvertSpecialChars( $canned['message'] ) ) ;

				$class = "altcolor1" ;

				$edit_string = "&nbsp;" ;
				$delete_string = "&nbsp;" ;
				if ( $canned['userID'] == $admin['userID'] )
				{
					$edit_string = "<a href=\"canned.php?sid=$sid&deptid=$deptid&action=canned_responses&cannedid=$canned[cannedID]&#theform\">Editar</a>" ;
					$delete_string = "<a href=\"JavaScript:do_delete( $canned[cannedID] )\">Deletar</a>" ;
				}

				if ( !isset( $admin_dept_hash[$canned['deptID']] ) )
				{
					ServiceCanned_update_Canned( $dbh, $admin['userID'], $canned['cannedID'], 0, $canned['name'],  $canned['message'] ) ;
					$department = "Todos meus Departamentos" ;
				}
				$department = $admin_dept_hash[$canned['deptID']] ;
				if ( ( $canned['userID'] == $admin['userID'] ) || ( $canned['userID'] > 10000000 ) )
				{
					print "
						<tr class=\"$class\">
							<td>$canned_name</td>
							<td>$canned_message</td>
							<td>$department</td>
							<td>$edit_string</td>
							<td>$delete_string</td>
						</tr>
					" ;
				}
			}
		?>
	  <tr> 
		<td colspan=5 class="hdash2"><img src="../images/spacer.gif" width="1" height="1"></td>
	  </tr>
	  </table>

		<a name="theform"><form method="POST" action="canned.php" name="form"></a>
		<input type="hidden" name="action" value="add_canned">
		<input type="hidden" name="prev_action" value="<?php echo $action ?>">
		<input type="hidden" name="type" value="r">
		<input type="hidden" name="sid" value="<?php echo $sid ?>">
		<input type="hidden" name="cannedid" value="<?php echo $cannedid ?>">
		<table cellspacing=0 cellpadding=2 border=0>
		<tr>
			<td>Referencia</td>
			<td><input name="name" type="text" style="width:100px" size="<?php echo $text_width ?>" maxlength="20" value="<?php echo stripslashes( $cannedinfo['name'] ) ?>"> <i>(ex: Parabens)</i></td>
		</tr>
		<tr>
			<td colspan=2><span class="small">HTML não é aceito. Use <a href="JavaScript:open_help( 'commands' )">Comandos</a>.</td>
		</tr>
		<tr>
			<td>Menssagem</td>
			<td>
				<table cellspacing=0 cellpadding=0 border=0><tr><td><textarea name="message" cols="<?php echo $text_width_long ?>" rows=3 wrap="virtual"><?php echo preg_replace( "/\"/", "&quot;", stripslashes( $cannedinfo['message'] ) ) ?></textarea></td><td>&nbsp;</td><td><span class="small"><span class="hilight">%%user%%</span> - visitors's login<br>
				<span class="hilight">%%operator%%</span> - nome do operador<br></td></tr></table>
			</td>
		</tr>
		<tr>
			<td>Departamento</td>
			<td><select name="deptid"><option value="0" <?php echo ( $cannedinfo['deptID'] == 0 ) ? "selected" : "" ?>>All My Departments</option>
			<?php
				for ( $c = 0; $c < count( $admin_departments ); ++$c )
				{
					$department = $admin_departments[$c] ;
					$selected = "" ;
					if ( $department['deptID'] == $cannedinfo['deptID'] )
						$selected = "selected" ;

					print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
				}
			?></select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="button" class="mainButton" value="Enviar" OnClick="do_submit()"></td>
		</tr>
		</table>

		</form>
    </td>







<?php
	elseif ( $action == "canned_commands" ):
	$selected_push = $selected_email = $selected_url = $selected_image = "" ;
	$canneds = ServiceCanned_get_UserCannedByType( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], 0, 'c', $admin_dept_select_string ) ;
	$cannedinfo = ServiceCanned_get_CannedInfo( $dbh, $cannedid, $_SESSION['session_admin'][$sid]['admin_id'] ) ;
	if ( preg_match( "/^push:/", $cannedinfo['message'] ) )
		$selected_push = "selected" ;
	else if ( preg_match( "/^email:/", $cannedinfo['message'] ) )
		$selected_email = "selected" ;
	else if ( preg_match( "/^url:/", $cannedinfo['message'] ) )
		$selected_url = "selected" ;
	else if ( preg_match( "/^image:/", $cannedinfo['message'] ) )
		$selected_image = "selected" ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Operatdor: Comandos</span><br>
		Comandos Mais Usados em Respostas.</p>
	  <table cellspacing=1 cellpadding=2 border=0 width="100%">
		<tr> 
		  <th align="left" nowrap>Referencia</th>
		  <th align="left">Menssagem</th>
		  <th align="left">Departamento</th>
		  <th align="center">&nbsp;</th>
		  <th align="center">&nbsp;</th>
		</tr>
		<?php
			for ( $c = 0; $c < count( $canneds ); ++$c )
			{
				$canned = $canneds[$c] ;

				$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
				$canned_message = Util_Format_ConvertSpecialChars( $canned['message'] ) ;

				$class = "altcolor1" ;

				$edit_string = "&nbsp;" ;
				$delete_string = "&nbsp;" ;
				if ( $canned['userID'] == $admin['userID'] )
				{
					$edit_string = "<a href=\"canned.php?sid=$sid&deptid=$deptid&action=canned_commands&cannedid=$canned[cannedID]&#theform\">Editar</a>" ;
					$delete_string = "<a href=\"JavaScript:do_delete( $canned[cannedID] )\">Deletar</a>" ;
				}
				$department = $admin_dept_hash[$canned['deptID']] ;
				if ( ( $canned['userID'] == $admin['userID'] ) || ( $canned['userID'] > 10000000 ) )
				{
					print "
						<tr class=\"$class\">
							<td>$canned_name</td>
							<td>$canned_message</td>
							<td>$department</td>
							<td>$edit_string</td>
							<td>$delete_string</td>
						</tr>
					" ;
				}
			}
		?>
		<tr> 
		  <td colspan=5 class="hdash2"><img src="../images/spacer.gif" width="1" height="1"></td>
		</tr>
		</table>

		<a name="theform"><form method="POST" action="canned.php" name="form"></a>
		<input type="hidden" name="action" value="add_canned">
		<input type="hidden" name="prev_action" value="<?php echo $action ?>">
		<input type="hidden" name="type" value="c">
		<input type="hidden" name="sid" value="<?php echo $sid ?>">
		<input type="hidden" name="cannedid" value="<?php echo $cannedid ?>">

		<table cellspacing=0 cellpadding=2 border=0>
		<tr> 
			<td>Referencia</td>
			<td><input name="name" type="text" style="width:100px" size="<?php echo $text_width ?>" maxlength="20" value="<?php echo $cannedinfo['name'] ?>"> <i>(ex: PUSH demo)</i></td>
		</tr>
		<tr>
			<td><a href="JavaScript:open_help( 'commands' )">Comandos</a></td>
			<td><select name="command" OnChange="put_command( this.selectedIndex )">
				<option value=""></option>
				<option value="push:" <?php echo $selected_push ?>>push:</option>
				<option value="email:" <?php echo $selected_email ?>>email:</option>
				<option value="url:" <?php echo $selected_url ?>>url:</option>
				<option value="image:" <?php echo $selected_image ?>>image:</option>
				</select> <font color="#FF0000">*</font>
			</td>
		</tr>
		<tr>
			<td>Menssagem</td>
			<td><input type="text" name="message" size="<?php echo $text_width_long - 5 ?>" OnFocus="check_command()" OnBlur="new_string=replace(this.value, ' ', '');this.value=new_string;return true;" value="<?php echo preg_replace( "/\"/", "&quot;", stripslashes( $cannedinfo['message'] ) ) ?>"></td>
		</tr>
		<tr>
			<td>Departamento</td>
			<td><select name="deptid"><option value="0" <?php echo ( $cannedinfo['deptID'] == 0 ) ? "selected" : "" ?>>Todos Departamentos</option>
			<?php
				for ( $c = 0; $c < count( $admin_departments ); ++$c )
				{
					$department = $admin_departments[$c] ;
					$selected = "" ;
					if ( $department['deptID'] == $cannedinfo['deptID'] )
						$selected = "selected" ;

					print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
				}
			?></select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="button" class="mainButton" value="Enviar" OnClick="do_submit()"></td>
		</tr>
		</table>

		</form>
	  </td>








<?php
	elseif ( $action == "canned_initiate" ):
	$canneds = ServiceCanned_get_UserCannedByType( $dbh, $_SESSION['session_admin'][$sid]['admin_id'], 0, 'i', $admin_dept_select_string ) ;
	$cannedinfo = ServiceCanned_get_CannedInfo( $dbh, $cannedid, $_SESSION['session_admin'][$sid]['admin_id'] ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
    <td width="100%" height="350" valign="top"> 
	  <p><span class="title">Operador: Mensagem Inicial</span><br>
		Menssagem Inicial de Suporte.</p>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<th align="left" nowrap>Referencia</th>
		<th align="left">Mensagem</th>
		<th align="center">&nbsp;</th>
		<th align="center">&nbsp;</th>
	  </tr>
	  <?php
			for ( $c = 0; $c < count( $canneds ); ++$c )
			{
				$canned = $canneds[$c] ;

				$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
				$canned_message = Util_Format_ConvertSpecialChars( $canned['message'] ) ;

				$class = "altcolor1" ;

				$edit_string = "&nbsp;" ;
				$delete_string = "&nbsp;" ;
				if ( $canned['userID'] == $admin['userID'] )
				{
					$edit_string = "<a href=\"canned.php?sid=$sid&deptid=$deptid&action=canned_initiate&cannedid=$canned[cannedID]\">Editar</a>" ;
					$delete_string = "<a href=\"JavaScript:do_delete( $canned[cannedID] )\">Deletar</a>" ;
				}

				if ( ( $canned['userID'] == $admin['userID'] ) || ( $canned['userID'] > 10000000 ) )
				{
					print "
						<tr class=\"$class\">
							<td>$canned_name</td>
							<td>$canned_message</td>
							<td>$edit_string</td>
							<td>$delete_string</td>
						</tr>
					" ;
				}
			}
		?>
	  <tr> 
		<td colspan=4 class="hdash2"><img src="../images/spacer.gif" width="1" height="1"></td>
	  </tr>
	  </table>

		<form method="POST" action="canned.php" name="form">
		<input type="hidden" name="action" value="add_canned">
		<input type="hidden" name="prev_action" value="<?php echo $action ?>">
		<input type="hidden" name="type" value="i">
		<input type="hidden" name="sid" value="<?php echo $sid ?>">
		<input type="hidden" name="cannedid" value="<?php echo $cannedid ?>">
		<input type="hidden" name="deptid" value="0">
		<table cellspacing=0 cellpadding=2 border=0>
		<tr> 
			<td>Referencia</td>
			<td> <input name="name" type="text" style="width:100px" size="<?php echo $text_width ?>" maxlength="20" value="<?php echo stripslashes( $cannedinfo['name'] ) ?>"> <i>(example: Sales Intro)</i></td>
		</tr>
		<tr>
			<td>Menssagem</td>
			<td> <input name="message" type="text" size="<?php echo $text_width_long ?>" value="<?php echo preg_replace( "/\"/", "&quot;", stripslashes( $cannedinfo['message'] ) ) ?>"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td> <input type="button" class="mainButton" value="Enviar" OnClick="do_submit()"></td>
		</tr>
		</table>

	  </form>
    </td>












<?php else: ?>
<!-- future release may use this space -->




<?php endif ; ?>
	<td style="background-image: url(../images/g_canned_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
  </tr>
</table>

<?php include_once( "../setup/footer.php" ); ?>
