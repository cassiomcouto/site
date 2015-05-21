<?php
	if ( ISSET( $_OFFICE_UTIL_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_LOADED = true ;

	function Util_Format_ConvertSpecialChars( $string )
	{
		$string = stripslashes( $string ) ;
		$string = preg_replace( "/</", "&lt;", $string ) ;
		$string = preg_replace( "/>/", "&gt;", $string ) ;
		$string = preg_replace( "/\"/", "&quot;", $string ) ;
		return $string ;
	}

	function Util_Format_CleanVariable( $string )
	{
		// take out common malicious characters that are
		// typically NOT used on standard inputs
		$string = preg_replace( "/[$#;?]|(eval\()|(eval +\()|(char\()|(char +\()|(exec\()|(exec +\()/i", "", trim( rtrim( $string ) ) ) ;
		return $string ;
	}

	function Util_Format_Bytes( $bytes )
	{
		$string = "" ;

		$kils = round ( $bytes/1000 ) ;
		$kil_re = ( $bytes % 1000 ) ;

		if ( $kils > 999 )
		{
			$megs = floor ( $kils/1000 ) ;
			$meg_re = ( $kils % 1000 ) ;
			$meg_per = $meg_re/1000 ;
			$megs_final = $megs + $meg_per ;
			$string = "$megs_final M" ;
		}
		elseif ( ( $bytes < 999 ) && ( $bytes ) )
		{
			$string = "$bytes byte" ;
		}
		else if ( $bytes )
		{
			$string = "$kils k" ;
		}

		return $string ;
	}

	/*****  Util_Get_IP  ****************************************
	*  
	*	History:
	*
	*****************************************************************/
	function Util_Get_IP ( $headers )
	{
		$remote_addr = $_SERVER['REMOTE_ADDR'] ;
		return $remote_addr ;
	}
?>