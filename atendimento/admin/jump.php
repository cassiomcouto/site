<?php
	include_once("../web/conf-init.php");
	$page = "" ;
	if ( isset( $_GET['page'] ) ) { $page = $_GET['page'] ; }
	$query = $_SERVER['QUERY_STRING'] ;

	$url = "index.php?$query&jump=1" ;
	if ( $page == "initiate" )
		$url = "$BASE_URL/admin/canned.php?$query&action=canned_initiate" ;
	else if ( $page == "transcript" )
		$url = "$BASE_URL/admin/view_transcript.php?$query&action=view" ;
?>
<html>
<head>
<title>jump</title>
<script language="JavaScript">
<!--
	// this file is here because php HEADER redirect still does not
	// read the new session that has been set.  so JavaScript redirect
	// will make it register next time
	function do_jump()
	{
		location.href = "<?php echo $url ?>" ;
	}
//-->
</script>
</head>
<body OnLoad="do_jump()"></body>
</html>
