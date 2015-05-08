<?php
	session_start() ;
	include_once("../web/conf-init.php");
	include_once("../API/sql.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php" ) ;
?>
<?php

	// initialize
	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	$action = $error_mesg = "" ;
	$success = 0 ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['success'] ) ) { $success = $_GET['success'] ; }
	if ( isset( $_POST['transcripts'] ) ) { $transcripts = $_POST['transcripts'] ; }
	if ( isset( $_POST['footprints'] ) ) { $footprints = $_POST['footprints'] ; }
	if ( isset( $_POST['referer'] ) ) { $referer = $_POST['referer'] ; }
	if ( isset( $_POST['chatrequest'] ) ) { $chatrequest = $_POST['chatrequest'] ; }
	if ( isset( $_POST['op_status'] ) ) { $op_status = $_POST['op_status'] ; }
	if ( isset( $_POST['op_rating'] ) ) { $op_rating = $_POST['op_rating'] ; }

?>
<?php
	// functions
?>
<?php
	// conditions

	$success = 0;
	$timestamp = time();
	switch ($action) {
		case "cleanup":
			$success = 1;
			if ($transcripts == "0") {
				if (!mysql_query("DELETE FROM `chattranscripts`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			} elseif ($transcripts == "none") {
				// do nothing
			} else {
				$tmp_stamp = $timestamp - $transcripts;
				if (!mysql_query("DELETE FROM `chattranscripts` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			}

			if ($footprints == "0") {
				if (!mysql_query("DELETE FROM `chatfootprints`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
				if (!mysql_query("DELETE FROM `chatfootprintstats`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
				if (!mysql_query("DELETE FROM `chatfootprintsunique`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
				if (!mysql_query("DELETE FROM `chatfootprinturlstats`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			} elseif ($footprints == "none") {
				// do nothing
			} else {
				$tmp_stamp = $timestamp - $footprints;
				if (!mysql_query("DELETE FROM `chatfootprints` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
				if (!mysql_query("DELETE FROM `chatfootprintstats` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
				if (!mysql_query("DELETE FROM `chatfootprintsunique` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
				if (!mysql_query("DELETE FROM `chatfootprinturlstats` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			}

			if ($referer == "0") {
				if (!mysql_query("DELETE FROM `chatrefer`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			} elseif ($referer == "none") {
				// do nothing
			} else {
				$tmp_stamp = $timestamp - $referer;
				if (!mysql_query("DELETE FROM `chatrefer` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			}

			if ($chatrequest == "0") {
				if (!mysql_query("DELETE FROM `chatrequestlogs`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			} elseif ($chatrequest == "none") {
				// do nothing
			} else {
				$tmp_stamp = $timestamp - $chatrequest;
				if (!mysql_query("DELETE FROM `chatrequestlogs` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			}

			if ($op_status == "0") {
				if (!mysql_query("DELETE FROM `chat_adminstatus`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			} elseif ($op_status == "none") {
				// do nothing
			} else {
				$tmp_stamp = $timestamp - $op_status;
				if (!mysql_query("DELETE FROM `chat_adminstatus` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			}

			if ($op_rating == "0") {
				if (!mysql_query("DELETE FROM `chat_adminrate`")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			} elseif ($op_rating == "none") {
				// do nothing
			} else {
				$tmp_stamp = $timestamp - $op_rating;
				if (!mysql_query("DELETE FROM `chat_adminrate` WHERE created < $tmp_stamp")) {
					echo "MySQL Error: " . mysql_error();
					$success = 0;
				}
			}

		break;
		case "restore":
		break;
	}

?>
<?php include_once( "./header.php" ) ; ?>

<span class="title">Data Cleanup</span> - <a href="index.php">back to menu</a><p>

<style type="text/css">
	.dblink, a.dblink {
		font-weight: bold;
		font-size: 11px;
		color: #002E5B;
	}

	a.dblink:hover {
		color: #29C029;
		text-decoration: underline;
	}
</style>


<form method="POST" action="cleanup.php">

<table cellpadding="3" cellspacing="0" border="1" style="border-collapse: collapse;">
<tr>
	<th width="120">Data Cleanup</th>
	<th width="400">Info.</th>
	<th>&nbsp;</th>
</tr>
<tr>
	<td width="120" valign="top">Chat Transcripts</td>
	<td width="400">This will cleanup any old transcripts. This will only remove data from the chat_transcript table within the phplive database.</td>
	<td valign="top">

		<select size="1" name="transcripts">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

	</td>
</tr>
<tr>
	<td width="120" valign="top">Footprints & Traffic</td>
	<td width="400">This will cleanup any old footprint/traffic information. This removes data from the chatfootprints, chatfootprintstats, chatfootprintsunique, & chatfootprinturlstats tables.</td>
	<td valign="top">

		<select size="1" name="footprints">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

	</td>
</tr>
<tr>
	<td width="120" valign="top">Referer Info</td>
	<td width="400">This will cleanup any old referring URL data. This removes data from the chatrefer table.</td>
	<td valign="top">


		<select size="1" name="referer">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

	</td>
</tr>
<tr>
	<td width="120" valign="top">Chat Request Logs</td>
	<td width="400">This will cleanup any old chat request logs. This removes data from the chatrequestlogs table.</td>
	<td valign="top">

		<select size="1" name="chatrequest">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

	</td>
</tr>
<tr>
	<td width="120" valign="top">Operator Status</td>
	<td width="400">This will cleanup any old Operator online/offline stats. This removes data from the chat_adminstatus table.</td>
	<td valign="top">

		<select size="1" name="op_status">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

	</td>
</tr>
<tr>
	<td width="120" valign="top">Operator Ratings</td>
	<td width="400">This will cleanup any old Operator ratings. This removes data from the chat_adminrate table.</td>
	<td valign="top">

		<select size="1" name="op_rating">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

	</td>
</tr>
<tr>
	<td width="120" valign="top">&nbsp;</td>
	<td width="400">&nbsp;</td>
	<td valign="top"><input type="submit" name="Submit" value="Submit"></td>
</tr>

</table>

<input type="hidden" name="action" value="cleanup">
</form>

<?php include_once( "./footer.php" ) ; ?>