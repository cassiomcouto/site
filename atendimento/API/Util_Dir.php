<?php
	if ( ISSET( $_OFFICE_UTIL_DIR ) == true )
		return ;

	$_OFFICE_UTIL_DIR = true ;

	function Util_DIR_CheckDir( $path, $l )
	{
		$l = preg_replace( "/[^A-Z0-9a-z_@.\-]/", "", $l ) ;
		$conf_file = realpath( "$path/web/conf-init.php" ) ;
		$conf_file_l = realpath( "$path/web/$l/$l-conf-init.php" ) ;
		if ( is_dir( "$path/web/$l" ) && file_exists( $conf_file_l ) && file_exists( $conf_file ) )
			return true ;
		else
			return false ;
	}
?>