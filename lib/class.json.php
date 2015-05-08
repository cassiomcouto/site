<?php

class ISC_JSON
{
	public static function output($message, $success=false, $additionalArray=null)
	{
		if(is_array($additionalArray) && !empty($additionalArray)) {
			$jsonArray = $additionalArray;
		}else{
			$jsonArray = array();
		}

		$jsonArray['success'] = (bool)$success;
		$jsonArray['message'] = $message;

		$charset = GetConfig('CharacterSet');
		if (!$charset) {
			$charset = 'utf-8';
		}

		header('Content-type: application/x-javascript; charset=' . $charset);
		echo '{}&& ';	//	json prefix - http://trac.dojotoolkit.org/ticket/6380
		echo isc_json_encode($jsonArray);
		die();
	}

	public static function decode($string)
	{
		if(substr($string, 0, 5) == '{}&& ') {
			$string = substr($string, 5);
		}

		return json_decode($string);
	}
}