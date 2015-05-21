<?php
	session_start() ;
	$isavailable = $transferred = 0 ;
	$action = $sessionid = $requestid = $userid = $deptid = $j_string = "" ;
	$session_chat = $_SESSION['session_chat'] ;
	$sid = ( isset( $_GET['sid'] ) ) ? $_GET['sid'] : "" ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_POST['sessionid'] ) ) { $sessionid = $_POST['sessionid'] ; }
	if ( isset( $_GET['sessionid'] ) ) { $sessionid = $_GET['sessionid'] ; }
	if ( isset( $_POST['requestid'] ) ) { $requestid = $_POST['requestid'] ; }
	if ( isset( $_GET['requestid'] ) ) { $requestid = $_GET['requestid'] ; }
	if ( isset( $_POST['userid'] ) ) { $userid = $_POST['userid'] ; }
	if ( isset( $_GET['userid'] ) ) { $userid = $_GET['userid'] ; }
	if ( isset( $_POST['deptid'] ) ) { $deptid = $_POST['deptid'] ; }
	if ( isset( $_GET['deptid'] ) ) { $deptid = $_GET['deptid'] ; }

	if ( !file_exists( "web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php" ) || !file_exists( "web/conf-init.php" ) )
	{
		print "<font color=\"#FF0000\">[Configuration Error: configurações nao encontradas!] Exiting...</font>" ;
		exit ;
	}
	include_once("./web/conf-init.php") ;
	$DOCUMENT_ROOT = realpath( preg_replace( "/http:/", "", $DOCUMENT_ROOT ) ) ;	include_once("./web/".$session_chat[$sid]['asp_login']."/".$session_chat[$sid]['asp_login']."-conf-init.php") ;
	include_once("./system.php") ;
	include_once("./lang_packs/$LANG_PACK.php") ;
	include_once("$DOCUMENT_ROOT/API/sql.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/Util.php") ;
	include_once("$DOCUMENT_ROOT/API/Users/get.php") ;
	include_once("$DOCUMENT_ROOT/API/Chat/get.php") ;

	// initialize
	// we use $rand to prevent loading from cached pages
	mt_srand ((double) microtime() * 1000000);
	$rand = mt_rand() ;

	// conditions
	if ( $action == "transfer_verify" )
	{
		// let's make sure operator is available before we confirm
		// the transfer
		$user = AdminUsers_get_UserInfo( $dbh, $userid, $session_chat[$sid]['aspID'] ) ;
		$department = AdminUsers_get_DeptInfo( $dbh, $deptid, $session_chat[$sid]['aspID'] ) ;
		if ( ( $user['available_status'] == 1 ) && ( $user['last_active_time'] > $admin_idle ) && $department['deptID'] )
			$isavailable = 1 ;
	}
	else if ( $action == "transfer_doit" )
	{
		$timestamp = date( "$TIMEZONE_FORMAT:i:s$TIMEZONE_AMPM", ( time() + $TIMEZONE ) ) ;
		$transfer_operator = AdminUsers_get_UserInfo( $dbh, $userid, $session_chat[$sid]['aspID'] ) ;
		$department = AdminUsers_get_DeptInfo( $dbh, $deptid, $session_chat[$sid]['aspID'] ) ;

		$_SESSION['session_chat'][$sid]['admin_poll_list'] .= " AND chat_admin.userID <> $transfer_operator[userID]" ;

		$string = "Please hold while being transferred to <b>$transfer_operator[name]</b> of $department[name]." ;
		$put_string = "window.parent.frames[\"main\"].window.addMessage( \"$string\", \"\", \"notice\", \"\" ) ;<br$sessionid>;window.parent.frames[\"main\"].window.write_typing('$transfer_operator[name]');<br$sessionid>" ;
		$transcript_string = "<p class=\"notice\">$string<ts ($timestamp) ts></p><br$sessionid>" ;

		// append the transfer message to the visitor's chat session
		UtilChat_AppendToChatfile( $session_chat[$sid]['chatfile_put'], $put_string ) ;
		UtilChat_AppendToChatfile( $session_chat[$sid]['chatfile_transcript'], $transcript_string ) ;

		// set it to empty so it does not look for the file.
		// the file is now used by the transferred operator.
		$_SESSION['session_chat'][$sid]['chatfile_get'] = "DUMMY_FILE" ;
		// set the put file to DUMP.txt so the visitor does not see messages
		// if THIS operator types a message... limit clutter
		$_SESSION['session_chat'][$sid]['chatfile_put'] = "DUMP.txt" ;
		$_SESSION['session_chat'][$sid]['chatfile_transcript'] = "DUMP_TR.txt" ;
		/**************** end dump ******************/

		// update the chatsessionlist table so it is updated with the new operator
		ServiceChat_update_SessionListLogin( $dbh, $sessionid, $session_chat[$sid]['screen_name'], $transfer_operator['name'] ) ;
		// put chat request for other the other operator
		ServiceChat_update_TransferCall( $dbh, $requestid, $userid, $deptid, 3 ) ;

		$j_string = preg_replace( "/'/", "&#039;", $string ) ;
		$j_string = "Suporte Transferido para <b>$transfer_operator[name]</b> of $department[name].  Esse Chat sera Finalizado." ;

		$_SESSION['session_admin'][$sid]['requests'] = 0 ;
		$transferred = 1 ;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat [admin view info]</title>

<link href="themes/<?php echo ( $_SESSION['session_chat'][$sid]['isadmin'] && $_SESSION['session_chat'][$sid]['theme'] ) ? $_SESSION['session_chat'][$sid]['theme'] : $THEME ?>/style.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!--
	function start_timer( c )
	{
		if ( c == 0 )
			location.href = "chat_admin_transfer.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&userid=<?php echo $userid ?>&action=transfer_doit&deptid=<?php echo $deptid ?>" ;
		document.counter.counter_value.value = c ;
		--c ;
		var temp = setTimeout( "start_timer("+c+")", 1000 ) ;
	}

	function transferok()
	{
		parent.window.parent.frames['main'].window.addMessage( '<?php echo $j_string ?>', '', 'notice', '' ) ;
	}

	function do_load()
	{
		<?php if ( ( $action == "transfer_verify" ) && $isavailable ): ?>
			start_timer( 15 ) ;
		<?php endif ; ?>
		<?php if ( $transferred ): ?>
			transferok() ;
		<?php endif ; ?>
	}
//-->
</script>

</head>
<body class="operatorbody" OnLoad="do_load()">

<?php if ( $_SESSION['session_chat'][$sid]['chatfile_get'] == "" ): ?>
<big><b>This session has ended.</b></big>


<?php elseif ( $isavailable && ( $action == "transfer_verify" ) ): ?>
<form name="counter">
Auto transferir para <big><b><?php echo "$user[name]" ?></b></big> of <?php echo $department['name'] ?> in <input type="text" name="counter_value" value="" class="input" size=2> segundos...
<br><br>
[ <a href="chat_admin_transfer.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&userid=<?php echo $userid ?>&action=transfer_doit&deptid=<?php echo $deptid ?>">Transferir Agora</a> ]
&nbsp;
[ <a href="chat_admin_transfer.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&userid=<?php echo $userid ?>&rand=<?php echo $rand ?>">Para Transferencia</a> ]
</form>


<?php elseif ( $action == "transfer_verify" ): ?>
<b><?php echo $user['name'] ?></b> esta <b>OFFLINE</b>.
<br><br>
<a href="chat_admin_transfer.php?sessionid=<?php echo $sessionid ?>&sid=<?php echo $sid ?>&requestid=<?php echo $requestid ?>&userid=<?php echo $userid ?>&rand=<?php echo $rand ?>">Voltar</a>


<?php elseif ( $action == "transfer_doit" ): ?>
<big><b>Transferido com Sucesso!</b></big>


<?php
	else:
	$departments = AdminUsers_get_AllDepartments( $dbh, $session_chat[$sid]['aspID'], 1 ) ;
?>
<form method="POST" action="chat_admin_transfer.php">
<input type="hidden" name="action" value="transfer">
<?php
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$department_users = AdminUsers_get_AllDeptUsers( $dbh, $department['deptID'] ) ;

		print "
				<table cellspacing=\"1\">
				<thead>
				<tr>
					<th colspan=5>Departamento: <b>$department[name]</th>
				</tr>
				</thead>
				<tbody class=\"subhead\">
				<tr>
					<th>&nbsp;</td>
					<th>Login</td>
					<th>Nome</td>
					<th>Status</td>
					<th>Suporte</td>
				</tr>
				</tbody>
		" ;
		for ( $c2 = 0; $c2 < count( $department_users ); ++$c2 )
		{
			$user = $department_users[$c2] ;
			$total_sessions = ServiceChat_get_UserTotalChatSessions( $dbh, $user['name'] ) ;

			$class = "class=\"row1\"" ;
			if ( $c2 % 2 )
				$class = "class=\"row2\"" ;

			$status = "offline" ;
			$activity = "not available" ;
			if ( ( $user['available_status'] == 1 ) && ( $user['last_active_time'] > $admin_idle ) )
			{
				$status = "online" ;
				$activity = "$total_sessions requests" ;
			}

			$transfer_button = "&nbsp;" ;
			if ( $user['userID'] != $session_chat[$sid]['admin_id'] )
				$transfer_button = "<input type=\"button\" class=\"go\" OnClick=\"location.href='chat_admin_transfer.php?sessionid=$sessionid&sid=$sid&requestid=$requestid&userid=$user[userID]&action=transfer_verify&deptid=$department[deptID]'\" value=\"Transferir\">" ;

			print "
				<tbody>
				<tr $class>
					<td width=55>$transfer_button</td>
					<td>$user[login]</td>
					<td>$user[name]</td>
					<td class=\"$status\">$status</td>
					<td>$activity</td>
				</tr>
				</tbody>
			" ;
		}
		print "				</table>" ;
	}
?>
</table>
</form>

<?php endif ; ?>

</center>
</body>
</html>
<?php
//	mysql_close( $dbh['con'] ) ;
?>