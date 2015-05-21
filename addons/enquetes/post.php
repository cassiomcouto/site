<?php
require_once('../../init.php');
$nome = $_POST['v2'];
$email = $_POST['v1'];
$query = "INSERT INTO [|PREFIX|]subscribers (
`subscriberid` ,
`subemail` ,
`subfirstname`
)
VALUES (
NULL , '".$nome."','".$email."'
);
";
$GLOBALS['ISC_CLASS_DB']->Query($query);
?>
