<?php $path = explode( "/", $_SERVER['PHP_SELF'] ) ; $total = count( $path ) ; $login = $path[$total-2] ; $winapp = isset( $_GET['winapp'] ) ? $_GET['winapp'] : "" ; HEADER( "location: ../../index.php?l=$login&winapp=$winapp" ) ; exit ; ?>