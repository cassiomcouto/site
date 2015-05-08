<?php

	include(dirname(__FILE__)."/init.php");
	header(sprintf("Location: %s/areadecliente.php?action=order_status", $GLOBALS["ShopPathSSL"]));