<?php
	session_start() ;
	include_once("../web/conf-init.php");
	include_once("../API/sql.php") ;
	include_once("../system.php") ;
	include_once("../lang_packs/$LANG_PACK.php") ;
	include_once("../web/VERSION_KEEP.php" ) ;

	error_reporting(E_ALL ^ E_NOTICE);

	// initialize
	if ( preg_match( "/(MSIE)|(Gecko)/", $_SERVER['HTTP_USER_AGENT'] ) )
		$text_width = "12" ;
	else
		$text_width = "9" ;

	// get variables
	$action = $table = "" ;
	$success = 0 ;
	if ( isset( $_POST['action'] ) ) { $action = $_POST['action'] ; }
	if ( isset( $_GET['action'] ) ) { $action = $_GET['action'] ; }
	if ( isset( $_GET['table'] ) ) { $table = $_GET['table'] ; }
	if ( isset( $_POST['cleanup'] ) ) { $cleanup = $_POST['cleanup'] ; }
	if ( isset( $_POST['timeframe'] ) ) { $timeframe = $_POST['timeframe'] ; }

	// define phplive database table array
	$table_array = array (
			"chat_admin",
			"chat_adminrate",
			"chat_adminstatus",
			"chat_asp",
			"chatcanned",
			"chatclicks",
			"chatclicktracking",
			"chatdepartments",
			"chatfootprints",
			"chatfootprintstats",
			"chatfootprintsunique",
			"chatfootprinturlstats",
			"chatkbcats",
			"chatkbquestions",
			"chatkbratings",
			"chatkbsearchterms",
			"chatrefer",
			"chatrequestlogs",
			"chatrequests",
			"chatsessionlist",
			"chatsessions",
			"chatspamips",
			"chatspfootprints",
			"chatsprefer",
			"chatsurveylogs",
			"chatsurveys",
			"chattranscripts",
			"chatuserdeptlist"
			);

	// list of the tables that cannot be cleaned up
	$unclean_tables = "chat_admin chat_asp chatcanned chatclicktracking chatsessionlist chatsessions chatspamips chatdepartments chatkbcats chatkbquestions chatkbsearchterms chatsurveys chatuserdeptlist";

	// conditions
	switch ($action) {
		case "repair_all":
			foreach ($table_array as $v) {
				if (!$res = mysql_fetch_row(mysql_query("REPAIR TABLE `$v`"))) {
					$errormsg .= "Couldn't repair table \"" . $v . "\": " . mysql_error() . "<br>\n";
				} else {
					if ($res['2'] == "error") {
						$errormsg .= "Couldn't repair table \"" . $v . "\": " . $res['3'] . "<br>\n";
					}
				}
			}
			if ($errormsg == "") {
				$success = 1 ;
				$errormsg = "All tables repaired successfully!";
			}
		break;
		case "optimize_all":
			foreach ($table_array as $v) {
				if (!$res = mysql_fetch_row(mysql_query("OPTIMIZE TABLE `$v`"))) {
					$errormsg .= "Couldn't optimize table \"" . $v . "\": " . mysql_error() . "<br>\n";
				} else {
					if ($res['2'] == "error") {
						$errormsg .= "Couldn't optimize table \"" . $v . "\": " . $res['3'] . "<br>\n";
					}
				}
			}
			if ($errormsg == "" || !isset($errormsg)) {
				$success = 1 ;
				$errormsg = "All tables optimized successfully!";
			}
		break;
		case "repair_table":
			if (!$res = mysql_fetch_row(mysql_query("REPAIR TABLE `$table`"))) {
				$errormsg = "Couldn't repair table \"" . $table . "\": " . mysql_error() . "<br>\n";
			} else {
				if ($res['2'] == "error") {
					$errormsg = "Couldn't repair table \"" . $table . "\": " . $res['3'] . "<br>\n";
				}
			}
			if ($errormsg == "" || !isset($errormsg)) {
				$success = 1 ;
				$errormsg = "Table \"$table\" repaired successfully!";
			}
		break;
		case "optimize_table":
			if (!$res = mysql_fetch_row(mysql_query("REPAIR TABLE `$table`"))) {
				$errormsg .= "Couldn't optimize table \"" . $table . "\": " . mysql_error() . "<br>\n";
			} else {
				if ($res['2'] == "error") {
					$errormsg .= "Couldn't optimize table \"" . $table . "\": " . $res['3'] . "<br>\n";
				}
			}
			if ($errormsg == "" || !isset($errormsg)) {
				$success = 1 ;
				$errormsg = "Table \"$table\" optimized successfully!";
			}
		break;
		case "rebuild_table":

			// get contents of a phplive SQL text file into a string
			$filename = "phplive.txt";
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);

			mysql_query("DROP TABLE IF EXISTS $table");
			list ($extra, $contents) = split ("DROP TABLE IF EXISTS $table;", $contents);
			list ($contents, $extra) = split (";", $contents);

			if (!$res = mysql_query($contents)) {
				$errormsg = "Couldn't create table \"" . $table . "\": " . mysql_error() . "<br>\n";
				echo "<pre>". $contents . "</pre>";
			} else {
				$success = 1 ;
				$errormsg = "Table \"$table\" rebuilt successfully!";
			}

		break;
		case "cleanupdata":
			$timestamp = time();
			$timestamp = $timestamp - $timeframe;

			if ($timeframe != "none") {
				foreach ($cleanup as $m) {
					if ($timeframe == "0") {
						$query = "DELETE FROM `$m`";
						//echo $query . "<br>\n";
						mysql_query($query);
					} else {
						if ($m == "chatclicks") {
							$query = "DELETE FROM `$m` WHERE statdate < $timestamp";
							mysql_query($query);
							//echo $query . "<br>\n";
						} else {
							$query = "DELETE FROM `$m` WHERE created < $timestamp";
							mysql_query($query);
							//echo $query . "<br>\n";
						}
					}
					$success = 1 ;
				}
			}

		break;
	}

	$alert_message = $errormsg;

	// functions

	function secs_to_string ($secs, $long=false) {
	  // reset hours, mins, and secs we'll be using
	  $hours = 0;
	  $mins = 0;
	  $secs = intval ($secs);
	  $t = array(); // hold all 3 time periods to return as string

	  // take care of mins and left-over secs
	  if ($secs >= 60) {
	    $mins += (int) floor ($secs / 60);
	    $secs = (int) $secs % 60;

	    // now handle hours and left-over mins
	    if ($mins >= 60) {
	      $hours += (int) floor ($mins / 60);
	      $mins = $mins % 60;
	    }
	    // we're done! now save time periods into our array
	    $t['hours'] = (intval($hours) < 10) ? "0" . $hours : $hours;
	    $t['mins'] = (intval($mins) < 10) ? "0" . $mins : $mins;
	  }

	  // what's the final amount of secs?
	  $t['secs'] = (intval ($secs) < 10) ? "0" . $secs : $secs;

	  // decide how we should name hours, mins, sec
	  $str_hours = ($long) ? "hour" : "hr";

	  $str_mins = ($long) ? "minute" : "min";
	  $str_secs = ($long) ? "second" : "sec";

	  // build the pretty time string in an ugly way
	  $time_string = "";
	  $time_string .= ($t['hours']) ? $t['hours'] . " $str_hours" . ((intval($t['hours']) == 1) ? "" : "s") : "";
	  $time_string .= ($t['mins']) ? (($t['hours']) ? ", " : "") : "";
	  $time_string .= ($t['mins']) ? $t['mins'] . " $str_mins" . ((intval($t['mins']) == 1) ? "" : "s") : "";
	  $time_string .= ($t['hours'] || $t['mins']) ? (($t['secs'] > 0) ? ", " : "") : "";
	  $time_string .= ($t['secs']) ? $t['secs'] . " $str_secs" . ((intval($t['secs']) == 1) ? "" : "s") : "";

	  return empty($time_string) ? 0 : $time_string;
	}

	// Calculate DB size by adding table size + index size:
	$rows = mysql_query("SHOW TABLE STATUS");
	$dbsize = 0;
	while ($row = mysql_fetch_array($rows)) {

		$tblname = $row['Name'];

		$tblrows[$tblname] = number_format($row['Rows']);

		$tot_data = $row['Data_length'];
		$tot_idx  = $row['Index_length'];
		$total = $tot_data + $tot_idx;
		$dbsize += $total;
		$total = $total / 1024 ;
		$total = round ($total,1);
		if ($total > 1024) {
			$total = $total / 1024 ;
			$total = round ($total,1);
			$tblsize[$tblname] = $total . " Mb";
		} else {
			$tblsize[$tblname] = $total . " Kb";
		}

		$gain= $row['Data_free'];
		$gain = $gain / 1024 ;
		$total_gain += $gain;
		$gain = round ($gain,1);

		if ($gain == 0) {
			$tbloverhead[$tblname] = "0 Kb";
		} else {
			if ($gain > 1024) {
				$gain = $gain / 1024 ;
				$gain = round ($gain,1);
				$tbloverhead[$tblname] = "<span class=\"hilight\">" . $gain . " Mb</span>";
			} else {
				$tbloverhead[$tblname] = $gain . " Kb";
			}
		}

	}


	$dbsize += $total;
	$dbsize = $dbsize / 1024 ;
	$dbsize = round ($dbsize,1);

	if ($dbsize > 1024) {
		$dbsize = $dbsize / 1024 ;
		$dbsize = round ($dbsize,1);
		$mysqlinfo['size'] = $dbsize . " Mb";
	} else {
		$mysqlinfo['size'] = $dbsize . " Kb";
	}

	// determine mysql version
	$mysqlinfo['version'] = mysql_get_server_info();

	// check PHP version
	if (version_compare(phpversion(), "4.3.0", ">=")) {

		// get mysql state
		$mysql_stat = mysql_stat();

		// works around bug where there isnt a double space before
		// Queries in some MySQL versions
		if (!eregi("  Queries", $mysql_stat)) {
		   $mysql_stat = str_replace("Queries"," Queries",$mysql_stat);
		}

		$mysql_stat = explode("  ",$mysql_stat);
		foreach ($mysql_stat as $v) {
			list ($name, $value) = split (":",$v);
			$mysqlinfo[$name] = $value;
		}

		$uptime = $mysqlinfo['Uptime'];
		$mysqlinfo['Uptime'] = secs_to_string($uptime);

		if ($mysqlinfo['Queries per second avg'] > 1) {
			$mysqlinfo['Queries per second avg'] = round($mysqlinfo['Queries per second avg']);
		}

	}

?>
<?php include_once( "./header.php" ) ; ?>

<span class="title">Informa&ccedil;&otilde;es do Banco de Dados </span> - <a href="index.php">Voltar ou menu</a>
<p>

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

<script language="JavaScript">
<!--
	function do_rebuild( table )
	{
		if ( confirm( "Are you sure you want to rebuild "+table+"? All data will be lost." ) )
			location.href = "dbinfo.php?action=rebuild_table&table="+table ;
	}

	function do_optimize_all()
	{
		if ( confirm( "Are you sure you want to optimize all tables?" ) )
			location.href = "dbinfo.php?action=optimize_all" ;

	}

	function do_cleanup()
	{
		if ( confirm( "Are you sure you wish to cleanup and remove old data?" ) )
			document.form1.submit() ;
	}

	var checkflag = "false";
	function checkall() {
		var empls = document.form1['cleanup[]'];

		if (checkflag == "false") {
			for (i = 0; i < empls.length; i++) {
				empls[i].checked = true;
			}
			checkflag = "true";
			//return "Uncheck All";
		} else {
			for (i = 0; i < empls.length; i++) {
				empls[i].checked = false;
			}
			checkflag = "false";
			//return "Check All";
		}
	}

-->
</script>

<table width="100%" cellpadding="0" cellspacing="5" border="0">
<tr><td>

<p><a href="dbinfo.php?action=repair_all" class="dblink">REPAIR ALL TABLES</a> - This task will go through each table and run a repair command. This will result in all tables being repaired if they are in any way corrupted.</p>
<p><a href="JavaScript:do_optimize_all()" class="dblink">OPTIMIZE ALL TABLES</a> - This task will optimize each table in the PHP Live database. Performing this task is best done during non-office hours. The Optmize command will lock each table while it is being optimized resulting in any incoming queries to be put on hold until optimization is complete. Performing this task during busy times can result in your MySQL systems max connections being reached.</p>

</td>
<td width="300" valign="top">

<?php
	// check PHP version
	if (version_compare(phpversion(), "4.3.0", ">=")) {
?>

<table cellpadding="3" cellspacing="0" border="1" style="border-collapse: collapse;">
<tr>
	<th colspan="2">MySQL Info</td>
</tr>
<tr>
	<td bgcolor="#FFFFFF" width="130"><b>MySQL Version</b></td>
	<td><?php echo $mysqlinfo['version']; ?></td>
</tr>
<tr>
	<td bgcolor="#FFFFFF" width="130"><b>Database Size</b></td>
	<td><?php echo $mysqlinfo['size']; ?></td>
</tr>
<tr>
	<td bgcolor="#FFFFFF" width="130"><b>Uptime</b></td>
	<td><?php echo $mysqlinfo['Uptime']; ?></td>
</tr>
<tr>
	<td bgcolor="#FFFFFF" width="130"><b>Queries per second</b></td>
	<td><?php echo $mysqlinfo['Queries per second avg']; ?></td>
</tr>
</table>

<?php  } else { ?>

<table cellpadding="3" cellspacing="0" border="1" style="border-collapse: collapse;">
<tr>
	<th colspan="2">MySQL Info</td>
</tr>
<tr>
	<td>MySQL Info is only supported on servers with PHP v4.3.0 or higher.</td>
</tr>
<tr>
</table>


<?php } ?>

</td></tr>

</table>
<br>

<form name="form1" method="POST">

<table cellpadding="3" cellspacing="0" border="1" style="border-collapse: collapse;">
<tr>
	<th width="200">Table</th>
	<th>Rows</th>
	<th width="150">Size</th>
	<th width="150">Overhead</th>
	<th>Error</th>
	<th width="150">Options</th>
	<th width="50">
		Cleanup	<input type="checkbox" onclick="checkall()" name="sdf" value="sdf">
	</th>
</tr>
<?php

foreach ($table_array as $v) {

	$error_msg = "";
	$num_rows = "";

	$query = "DESCRIBE $v" ;
	if (!$table_description_result = mysql_query($query)) {
		$error_msg = mysql_error();
	} else {
		if (!$result2 = mysql_query("SELECT * FROM $v LIMIT 0 , 5")) {
			$error_msg = mysql_error();
		} else {
			$num_rows = @mysql_num_rows($result2);
			if ( !is_int($num_rows) ) {
				$error_msg = mysql_error();
			} else {
				$error_msg = "";
			}
		}
	}

	if ($error_msg == "") {
		$error_msg = "None";
	} else {
		$error_msg = "<font color='red'>" . $error_msg . "</font>";
	}

	if ( !is_int($num_rows) ) {
		$num_rows = "N/A";
	}

	echo "<tr>
		<td>$v</td>
		<td align=\"center\">$tblrows[$v]</td>
		<td align=\"center\">$tblsize[$v]</td>
		<td align=\"center\">$tbloverhead[$v]</td>
		<td>$error_msg</td>
		<td align=\"center\"><a href=\"dbinfo.php?action=repair_table&table=$v\">Repair</a>&nbsp;&nbsp;&nbsp;<a href=\"dbinfo.php?action=optimize_table&table=$v\">Optimize</a>&nbsp;&nbsp;&nbsp;<a href=\"JavaScript:do_rebuild('$v')\">Rebuild</a></td>
		";


	if ( eregi($v, $unclean_tables) ) {
		echo "		<td align=\"center\">&nbsp;</td>";
	} else {
		echo "		<td align=\"center\"><input type=\"checkbox\" name=\"cleanup[]\" value=\"$v\"></td>";
	}

	echo "</tr>\n";
}

?>
<tr>
	<td colspan="7" align="right">

		<p>&nbsp;</p>

		<select size="1" name="timeframe">
			<option value="none">None</option>
			<option value="0">All data</option>
			<option value="604800">Older than 7 days</option>
			<option value="864000">Older than 10 days</option>
			<option value="2592000">Older than 30 days</option>
			<option value="5184000">Older than 60 days</option>
			<option value="7776000">Older than 90 days</option>
		</select>

		<input type="button" name="cleanup" value="Cleanup Old Data" OnClick="do_cleanup()">

	</td>
</tr>
</table>

<input type="hidden" name="action" value="cleanupdata">

</form>

<?php

?>
<?php include_once( "./footer.php" ) ; 
?>