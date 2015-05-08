<?php
	$CRYPTKEY = "NULLED BY CHT" ;
	$TRANSFER_BUFFER = 120 ;	// seconds
	$CHECK_NEW_MSG_REFRESH = 2 ;	// seconds
	$NEW_CHAT_REQUEST_REFRESH = 4 ;	// seconds
	$admin_idle_value = 20 ;	// seconds
	$admin_idle = time() - $admin_idle_value ;
	$CHAT_TIMEOUT = 45 ;	// seconds
	$FOOTPRINT_IDLE = 15 ;	// seconds
	if ( phpversion() > 5 )
		date_default_timezone_set( "US/Eastern" ) ;
	$TIMEZONE = 0 ; $TIMEZONE_FORMAT = "h" ; $TIMEZONE_AMPM = " a" ;
	if ( isset( $COMPANY_NAME ) && $COMPANY_NAME )
	{
		if ( preg_match( "/<:>/", $COMPANY_NAME ) )
		{
			LIST( $COMPANY_NAME_TEMP, $TIMEZONE ) = EXPLODE( "<:>", $COMPANY_NAME ) ;
			if ( $TIMEZONE )
			{
				$TIMEZONE_FORMAT = substr( $TIMEZONE, 0, 1 ) ;
				$TIMEZONE = substr( $TIMEZONE, 1, strlen( $TIMEZONE ) ) ;
				$TIMEZONE_AMPM = ( $TIMEZONE_FORMAT == "H" ) ? "" : " a" ;
			}
		}
		else
			$COMPANY_NAME_TEMP = $COMPANY_NAME ;
	}
?>