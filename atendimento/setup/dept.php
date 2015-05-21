<?php
	session_start() ;
	if ( isset( $_SESSION['session_setup'] ) ) { $session_setup = $_SESSION['session_setup'] ; } else { HEADER( "location: index.php" ) ; exit ; }
	include_once( "../API/Util_Dir.php" ) ;
	if ( !Util_DIR_CheckDir( "..", $session_setup['login'] ) )
	{
		HEADER( "location: index.php" ) ;
		exit ;
	}
	include_once("../web/conf-init.php");
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;
	include_once("../web/$session_setup[login]/$session_setup[login]-conf-init.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php");
	include_once("../system.php") ;
	include_once("$DOCUMENT_ROOT/API/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/update.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/put.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/remove.php") ;
	include_once("$DOCUMENT_ROOT/API/Canned/update.php") ;
	$section = 1;			// Section number - see header.php for list of section numbers

	// This is used in footer.php and it places a layer in the menu area when you are in
	// a section > 0 to provide navigation back.
	// This is currently set as a javascript back, but it could be replaced with explicit
	// links as using the javascript back button can cause problems after submitting a form
	// (cause the data to get resubmitted)

	$nav_line = '<a href="adddept.php" class="nav">:: Previous</a>';
?>
<?php

	// initialize
	$action = $deptid = $error = $success = "" ;
	$success = $cannedid = 0 ;

	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
	{
		$text_width = "20" ;
		$text_width_long = "55" ;
		$textbox_width = "80" ;
	}
	else
	{
		$text_width = "10" ;
		$text_width_long = "20" ;
		$textbox_width = "40" ;
	}

	// set adminid to big number since the setup user does not have admin id (operator id).
	// the big number will make sure it does not conflict with future operator ids.
	$adminid = 10000000 + $session_setup['aspID'] ;

	// get variables
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }
	if ( isset( $_POST['deptid'] ) ) { $deptid = $_POST['deptid'] ; }
	if ( isset( $_GET['cannedid'] ) ) { $cannedid = $_GET['cannedid'] ; }
	if ( isset( $_POST['cannedid'] ) ) { $cannedid = $_POST['cannedid'] ; }

	if ( !$deptid )
	{
		HEADER( "location: adddept.php" ) ;
		exit ;
	}
?>
<?php
	// functions
?>
<?php
	// conditions

	if ( $action == "add_canned" )
	{
		$action = $_POST['prev_action'] ;
		//$canned_exist = ServiceCanned_get_CannedInfoByName( $dbh, $deptid, $_POST['type'], $_POST['name'] ) ;

		//if ( !isset( $canned_exist['cannedID'] ) )
		//{
			if ( $cannedid )
				ServiceCanned_update_Canned( $dbh, $adminid, $cannedid, $deptid, $_POST['name'], $_POST['message'] ) ;
			else
				ServiceCanned_put_UserCanned( $dbh, $adminid, $deptid, $_POST['type'], $_POST['name'], $_POST['message'] ) ;
			$cannedid = 0 ;
		//}
		//else
			//$error = "That Reference Name is already in use.  Please choose another." ;
		$success = 1 ;
	}
	else if ( $action == "delete_canned" )
	{
		$action = $_GET['prev_action'] ;
		ServiceCanned_remove_UserCanned( $dbh, $adminid, $cannedid ) ;
		$cannedid = 0 ;
		$success = 1 ;
	}
	else if ( $action == "add_hours" )
	{
		$action = $_POST['prev_action'] ;
		AdminUsers_update_DeptValue( $dbh, $session_setup['aspID'], $deptid, "message", $_POST['message'] ) ;
		$success = 1 ;
	}
	else if ( $action == "update_greeting" )
	{
		$action = $_POST['prev_action'] ;
		AdminUsers_update_DeptValue( $dbh, $session_setup['aspID'], $deptid, "greeting", $_POST['greeting'] ) ;
		$success = 1 ;
	}
	$deptinfo = AdminUsers_get_DeptInfo( $dbh, $deptid, $session_setup['aspID'] ) ;
?>
<?php include_once( "./header.php" ) ; ?>
<!-- DO NOT REMOVE THE COPYRIGHT NOTICE OF "&nbsp; OSI Codes Inc." -->
<!-- copyright OSI Codes, http://www.osicodes.com [DO NOT DELETE] -->
<script language="JavaScript">
<!--
	function do_submit()
	{
		if ( ( document.form.name.value == "" ) || ( document.form.message.value == "" ) )
			alert( "All fields must be supplied." ) ;
		else
			document.form.submit()
	}

	function do_delete( cannedid )
	{
		if ( confirm( "Are you sure you want to delete?" ) )
			location.href = "dept.php?action=delete_canned&prev_action=<?php echo $action ?>&deptid=<?php echo $deptid ?>&cannedid="+cannedid ;
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
			alert( "Please chose a command first." ) ;
			document.form.command.focus() ;
		}
	}

	function view_screen()
	{
		var request_url = "../request.php?l=<?php echo $session_setup['login'] ?>&x=<?php echo $session_setup['aspID'] ?>&deptid=<?php echo $deptid ?>&pagex=message" ;
		newwin = window.open( request_url, "demo", 'scrollbars=no,menubar=no,resizable=0,location=no,screenX=50,screenY=100,width=450,height=360' ) ;
		newwin.focus() ;
	}

	function open_help( action )
	{
		url = "<?php echo $BASE_URL ?>/help.php?action=" + action ;
		newwin = window.open(url, "help", "scrollbars=yes,menubar=no,resizable=1,location=no,width=350,height=250") ;
		newwin.focus() ;
	}

//-->
</script>

<?php if ( $action == "greeting" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Manager: <?php echo stripslashes( $deptinfo['name'] ) ?>
	  Greeting</span><br>
	  Edit your department chat greeting. This message is displayed when 
	  a visitor waits while a support representative is being contacted.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?>
	</p>
			<table width="100" border=0 cellpadding=2 cellspacing=1>
			  <form method="POST" action="dept.php" name="form">
				<input type="hidden" name="action" value="update_greeting">
				<input type="hidden" name="prev_action" value="<?php echo $action ?>">
				<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
				<tr> 
				  <td colspan="2" valign="top"> <textarea cols="<?php echo $textbox_width ?>" name="greeting" rows="5" wrap="virtual" class="textarea"><?php echo ( isset( $deptinfo['greeting'] ) ) ? $deptinfo['greeting'] : "" ?></textarea></td>
				</tr>
				<tr> 
				  <td width="50%" align="right"> <input name="Submit" type="submit" class="mainButton" value="Submit"> 
					&nbsp;&nbsp;&nbsp; </td>
				  <td width="50%"><span class="hilight">%%user%%</span> - visitors's 
					login<br> <span class="hilight">%%date%%</span> - today's 
					date </td>
				</tr>
			  </form>
			</table></td>
  <td style="background-image: url(../images/g_manage_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>


<?php elseif ( $action == "offline" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Manager: <?php echo stripslashes( $deptinfo['name'] ) ?> Away Msg</span><br>
	  You can place a short message stating your department hours, offline 
	  message, or best time or ways to reach the department.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?>
	  </p> <p><strong><a href="JavaScript:view_screen()">View 
	  current "Leave a Message" screen</a></strong>
	<p> 
	<table width="100%" border=0 cellpadding=2 cellspacing=1>
	  <form method="POST" action="dept.php" name="form">
		<input type="hidden" name="action" value="add_hours">
		<input type="hidden" name="prev_action" value="<?php echo $action ?>">
		<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
		<tr> 
		  <td valign="top">(HTML is allowed. Please keep this message 
			very brief.)<br> 
			<textarea cols="<?php echo $textbox_width ?>" rows="3" wrap="virtual" class="input" name="message" style="width: 400px;"><?php echo stripslashes( $deptinfo['message'] ) ?></textarea></td>
		</tr>
		<tr> 
		  <td align="center"> <input name="Submit" type="submit" class="mainButton" value="Submit"> 
		  </td>
		</tr>
	  </form>
	</table></td>
  <td style="background-image: url(../images/g_manage_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>


<?php elseif ( $action == "away" ): ?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Manager: <?php echo stripslashes( $deptinfo['name'] ) ?> Away 
	  Msg</span><br>
	  The below message will be displayed when the operators are currently on "Away" status.</p>
	<p> 
	<table width="100%" border=0 cellpadding=2 cellspacing=1>
	  <form method="POST" action="dept.php" name="form">
		<input type="hidden" name="action" value="add_hours">
		<input type="hidden" name="prev_action" value="<?php echo $action ?>">
		<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
		<tr> 
		  <td valign="top">(HTML is allowed. Please keep this message 
			very brief.)<br> 
			<textarea cols="<?php echo $textbox_width ?>" rows="3" wrap="virtual" class="input" name="message" style="width: 400px;"><?php echo stripslashes( $deptinfo['message'] ) ?></textarea></td>
		</tr>
		<tr> 
		  <td align="center"> <input name="Submit" type="submit" class="mainButton" value="Submit"> 
		  </td>
		</tr>
	  </form>
	</table></td>
  <td style="background-image: url(../images/g_manage_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>


<?php 
	elseif ( $action == "canned_responses" ):
	$canneds = ServiceCanned_get_UserCannedByType( $dbh, $adminid, $deptid, 'r', '' ) ;
	$cannedinfo = ServiceCanned_get_CannedInfo( $dbh, $cannedid, $adminid ) ;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="15">
<tr> 
  <td height="350" valign="top"> <p><span class="title">Manager: <?php echo stripslashes( $deptinfo['name'] ) ?> Canned 
	  Responses</span><br>
	  You can set department global canned responses here.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?></p>
	<ul>
	  <li>The below canned responses will be displayed on all the department 
		operators' canned drop down menus.</li>
	  <li>Department operators will not have access to add/edit/delete 
		these. You may only add/edit/delete the global canned responses 
		here in the setup area. </li>
	</ul>
	<font color="#FF0000"><?php echo $error ?></font><br>
	<table cellspacing=1 cellpadding=2 border=0 width="100%">
	  <tr> 
		<th align="left" nowrap>Reference</th>
		<th>Message</th>
		<th align="center">&nbsp;</th>
		<th align="center">&nbsp;</th>
	  </tr>
	  <?php
			for ( $c = 0; $c < count( $canneds ); ++$c )
			{
				$canned = $canneds[$c] ;

				$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
				$canned_message = nl2br( Util_Format_ConvertSpecialChars( $canned['message'] ) ) ;

				print "
					<tr class=\"altcolor2\">
						<td>$canned_name</td>
						<td>$canned_message</td>
						<td><a href=\"dept.php?deptid=$deptid&action=canned_responses&cannedid=$canned[cannedID]\">Edit</a></td>
						<td><a href=\"JavaScript:do_delete( $canned[cannedID] )\">Delete</a></td>
					</tr>
				" ;
			}
		?>
	  <tr> 
		<td colspan=4 class="hdash2"><img src="../images/spacer.gif" width="1" height="1"></td>
	  </tr>
	  	<form method="POST" action="dept.php" name="form">
			<input type="hidden" name="action" value="add_canned">
			<input type="hidden" name="prev_action" value="<?php echo $action ?>">
			<input type="hidden" name="type" value="r">
			<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
			<input type="hidden" name="cannedid" value="<?php echo $cannedid ?>">
	  <tr>
		<td><input name="name" type="text" style="width:100px" size="<?php echo $text_width ?>" maxlength="20" value="<?php echo ( isset( $cannedinfo['name'] ) ) ? $cannedinfo['name'] : "" ?>"></td>
		<td colspan=4><textarea name="message" type="text" cols="<?php echo $text_width + 30 ?>" rows=2><?php echo ( isset( $cannedinfo['message'] ) ) ? preg_replace( "/\"/", "&quot;", stripslashes( $cannedinfo['message'] ) ) : "" ?></textarea> <input type="button" class="mainButton" value="Submit" onClick="do_submit()"></td>
	  </tr>
	  <tr> 
		  <td>&nbsp;</td>
		  <td colspan="3">
			HTML not allowed.  Please use <a href="JavaScript:open_help( 'commands' )">commands</a>.<p>
			Predefined Variables:<br>
			<span class="hilight">%%user%%</span> - visitors's 
			login<br>
			<span class="hilight">%%operator%%</span> - operator's 
			name<br>
		</td>
	  </tr></form>
	</table>
	
  </td>
<td style="background-image: url(../images/g_canned_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
</tr>
</table>


<?php
	elseif( $action == "canned_commands" ):
	$canneds = ServiceCanned_get_UserCannedByType( $dbh, $adminid, $deptid, 'c', '' ) ;
	$cannedinfo = ServiceCanned_get_CannedInfo( $dbh, $cannedid, $adminid ) ;
	$selected_push = $selected_email = $selected_image = $selected_url = "" ;
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
	  <p><span class="title">Operator: <?php echo stripslashes( $deptinfo['name'] ) ?> Canned Commands</span><br>
		Canned commands are quick ways to execute simple HTML tasks.  <?php echo ( isset( $success ) && $success ) ? "<font color=\"#29C029\"><big><b>Update Success!</b></big></font>" : "" ?></p>
	<ul>
	  <li>The below canned commands will be displayed on all the department 
		operators' canned drop down menus.</li>
	  <li>Department operators will not have access to add/edit/delete 
		these. You may only add/edit/delete the global canned commands 
		here in the setup area. </li>
	</ul>
	<font color="#FF0000"><?php echo $error ?></font><br>
	  <table cellspacing=1 cellpadding=2 border=0 width="100%">
		<tr> 
		  <th align="left" nowrap>Reference</th>
		  <th>Message</th>
		  <th align="center">&nbsp;</th>
		  <th align="center">&nbsp;</th>
		</tr>
		<?php
			for ( $c = 0; $c < count( $canneds ); ++$c )
			{
				$canned = $canneds[$c] ;

				$canned_name = Util_Format_ConvertSpecialChars( $canned['name'] ) ;
				$canned_message = Util_Format_ConvertSpecialChars( $canned['message'] ) ;

				print "
					<tr class=\"altcolor2\">
						<td>$canned_name</td>
						<td>$canned_message</td>
						<td><a href=\"dept.php?deptid=$deptid&action=canned_commands&cannedid=$canned[cannedID]\">Edit</a></td>
						<td><a href=\"JavaScript:do_delete( $canned[cannedID] )\">Delete</a></td>
					</tr>
				" ;
			}
		?>
		<tr> 
		  <td colspan=4 class="hdash2"><img src="../images/spacer.gif" width="1" height="1"></td>
		</tr>
		<form method="POST" action="dept.php" name="form">
			<input type="hidden" name="action" value="add_canned">
			<input type="hidden" name="prev_action" value="<?php echo $action ?>">
			<input type="hidden" name="type" value="c">
			<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
			<input type="hidden" name="cannedid" value="<?php echo $cannedid ?>">
		  <tr> 
			<td><input name="name" type="text" style="width:100px" size="<?php echo $text_width ?>" maxlength="20" value="<?php echo $cannedinfo['name'] ?>"></td>
			<td nowrap colspan=3> <select name="command" OnChange="put_command( this.selectedIndex )">
				<option value=""></option><option value="push:" <?php echo $selected_push ?>>push:</option>
				<option value="email:" <?php echo $selected_email ?>>email:</option>
				<option value="url:" <?php echo $selected_url ?>>url:</option>
				<option value="image:" <?php echo $selected_image ?>>image:</option>
			  </select> <input type="text" name="message" size="<?php echo $text_width ?>" OnFocus="check_command()" OnBlur="new_string=replace(this.value, ' ', '');this.value=new_string;return true;" value="<?php echo preg_replace( "/\"/", "&quot;", stripslashes( $cannedinfo['message'] ) ) ?>"> 
			<input type="button" class="mainButton" value="Submit" OnClick="do_submit()"></td>
		  </tr>
		  <tr> 
			<td>&nbsp;</td>
			<td colspan="2">
				<b>url:</b><i> URL</i> (hyperlink a URL)<br>
				<b>email:</b><i> example@osicodes.com</i> (hyperlink an email)<br>
				<b>image:</b><i> URL/sample.gif</i> (embed an image)<br>
				<b>push:</b><i> URL</i> (opens new browser containing URL of webpage, word docs, etc.)<br> 
			</td>
		  </tr>
		</form>
	  </table></td>
    <td style="background-image: url(../images/g_canned_big.jpg);background-repeat: no-repeat;"><img src="../images/spacer.gif" width="229" height="1"></td>
  </tr>
</table>


<?php endif; ?>
<?php include_once( "./footer.php" ) ; ?>