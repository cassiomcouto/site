<?php
	if ( ISSET( $_OFFICE_GET_ServiceSpam_LOADED ) == true )
		return ;

	$_OFFICE_GET_ServiceSpam_LOADED = true ;
	FUNCTION ServiceSpam_get_IPs( &$dbh,
						$aspid )
	{
		if ( $aspid == "" )
		{
			return false ;
		}
		$aspid = database_mysql_quote( $aspid ) ;
		$ips = Array() ;

		$query = "SELECT * FROM chatspamips WHERE aspID = $aspid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$ips[] = $data ;
			return $ips ;
		}
		return false ;
	}

?>
