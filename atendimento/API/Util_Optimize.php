<?php
	if ( ISSET( $_OFFICE_UTIL_OPT_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_OPT_LOADED = true ;

	function Util_OPT_Database( $dbh,
				$tables )
	{

		if ( count( $tables ) > 0 )
		{
			for ( $c = 0; $c < count( $tables ); ++$c )
			{
				$query = "OPTIMIZE TABLE $tables[$c]" ;
				database_mysql_query( $dbh, $query ) ;
			}
			
			return true ;
		}
		else
			return false ;
	}

?>