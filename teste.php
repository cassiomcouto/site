<?php
require_once(dirname(__FILE__)."/init.php");

$serial = 'V2010AMxAjMW1zauBlUyN1ZQx2a01WT24GS0EVchlURBNUQRF0bwZkR3NVQFBTMwIjV';

$dec = str_replace('}%{','QB',$serial);
$dec = str_replace('V2010','==',$dec);
$dec = strrev($dec);
$dec = base64_decode($dec);
$dec = str_replace('V2010','',$dec);

echo $dec;

print_r(spr1ntf($dec));