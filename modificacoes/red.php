<?php
ini_set("display_errors", 0);
include("../init.php");
$id = $_GET['is'];
$query = sprintf("select * from [|PREFIX|]products where productid = ".$id);
$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);
$url = ProdLink($row['prodname']);
header("Location: $url");
?>