<?php
	if ( ISSET( $_OFFICE_UTIL_forms_LOADED ) == true )
		return ;

	$_OFFICE_UTIL_forms_LOADED = true ;

	function yesno($match){
		$output = "" ;
		$choices = array("No", "Yes");
		for($index = 0; $index < count($choices); ++$index){
			if($index == $match){ $output .= "<option value=\"$index\" selected>$choices[$index] "; }
			else{ $output .= "<option value=\"$index\">$choices[$index] "; }
		}
		return $output;
	}

	function active_status($match){
		$output = "" ;
		$choices = array("Inactive", "Active");
		for($index = 0; $index < count($choices); ++$index){
			if($index == $match){ $output .= "<option value=\"$index\" selected>$choices[$index] "; }
			else{ $output .= "<option value=\"$index\">$choices[$index] "; }
		}
		return $output;
	}

	function active_status_hash(){
		$output = ARRAY(
			"0" => "Inactive",
			"1" => "Active"
		) ;
		return $output;
	}

	function numbers($match, $begin, $end){
		$output = "" ;
		for ($counter = $begin; $counter <= $end; ++$counter){
			if($counter == $match){ $output .= "<option value=\"$counter\" selected>$counter "; }
			else{ $output .= "<option value=\"$counter\">$counter "; }
		}
		return $output;
	}

	function numbers_blank($match, $begin, $end){
		$output = "" ;
		if ( $match == "" ) { $output .= "<option value=\"\" selected> </option>"; }
		else { $output .= "<option value=\"\"> </option>"; }

		for ($counter = $begin; $counter <= $end; ++$counter){
			if($counter == $match){ $output .= "<option value=\"$counter\" selected>$counter "; }
			else{ $output .= "<option value=\"$counter\">$counter "; }
		}
		return $output;
	}

	function numbers_fill($match, $begin, $end){
		$output = "" ;
		$output .= "<option value=\"00\"> " ;
		for ($counter = $begin; $counter <= $end; ++$counter){
			if($counter < 10){
				if($counter == $match){ $output .= "<option value=\"$counter\" selected>0$counter "; }
				else{ $output .= "<option value=\"$counter\">0$counter "; }
			}
			else{
				if($counter == $match){ $output .= "<option value=\"$counter\" selected>$counter "; }
				else{ $output .= "<option value=\"$counter\">$counter "; }
			}
		}
		return $output;
	}

	function numbers_desc($match, $begin, $end){
		$output = "" ;
		for ($counter = $end; $counter >= $begin; --$counter){
			if($counter == $match){ $output .= "<option value=\"$counter\" selected>$counter "; }
			else{ $output .= "<option value=\"$counter\">$counter "; }
		}
		return $output;
	}

	function numbers_desc_blank($match, $begin, $end){
		$output = "" ;
		if ( $match == "" ) { $output .= "<option value=\"\" selected> </option>"; }
		else { $output .= "<option value=\"\"> </option>"; }

		for ($counter = $begin; $counter >= $end; --$counter){
			if($counter == $match){ $output .= "<option value=\"$counter\" selected>$counter "; }
			else{ $output .= "<option value=\"$counter\">$counter "; }
		}
		return $output;
	}

	function numbers_fill_desc($match, $begin, $end){
		$output = "" ;
		for ($counter = $end; $counter >= $begin; --$counter){
			if($counter < 10){
				if($counter == $match){ $output .= "<option value=\"$counter\" selected>0$counter "; }
				else{ $output .= "<option value=\"$counter\">0$counter "; }
			}
			else{
				if($counter == $match){ $output .= "<option value=\"$counter\" selected>$counter "; }
				else{ $output .= "<option value=\"$counter\">$counter "; }
			}
		}
		return $output;
	}

?>