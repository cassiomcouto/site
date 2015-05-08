<?php

/** Loja Virtual V2010 - hiperlojas2008@gmail.com **
  *
  * Data: Thu, 27 Sep 2012 01:05:43 +0000
  *
  * nao modificar o arquivo manualmente
  *
*/

$cacheData = array (
  'checkout_bcash' => 
  array (
    'is_setup' => '1',
    'displayname' => 'bcash',
    'availablecountries' => 'all',
    'pagdigemail' => 'financeiro@lxhost.net.br',
    'acrecimo' => '0.00',
    'jurosde' => '0',
    'helptext' => '<a href="javascript:window.open(\'%%GLOBAL_ShopPath%%/modules/checkout/pagamentodigital/repagar.php?pedido=%%GLOBAL_OrderId%%\',\'popup\',\'width=800,height=800,scrollbars=yes\');void(0);"><img src=\'%%GLOBAL_ShopPath%%/modules/checkout/pagamentodigital/images/final.gif\' border=\'0\'></a>
<br>',
  ),
  'checkout_deposito' => 
  array (
    'is_setup' => '1',
  ),
  'checkout_pagseguro' => 
  array (
    'is_setup' => '1',
    'displayname' => 'PagSeguro',
    'availablecountries' => 'all',
    'pagemail' => 'financeiro@lxhost.net.br',
    'token' => '0',
    'acrecimo' => '0.00',
    'jurosde' => '0',
    'helptext' => '<a href="javascript:window.open(\'%%GLOBAL_ShopPath%%/modules/checkout/pagseguro/repagar.php?pedido=%%GLOBAL_OrderId%%\',\'popup\',\'width=800,height=800,scrollbars=yes\');void(0);"><img src=\'%%GLOBAL_ShopPath%%/modules/checkout/pagseguro/images/final.gif\' border=\'0\'></a>
<br>',
    'htmlpagseguro' => '<!--ok-->',
  ),
);

?>