<?php

class Cache {
	
	public static function setup() {
		$Iqs = new Iqs;
		
		$data_array = array(
			"getZipWhiteListValues" => $Iqs->getZipWhiteListValues(),
			"getBlockCodesValues" => $Iqs->getBlockCodesValues(),
			"getConfSectionValues_env" => $Iqs->getConfSectionValues("env"),
			"getConfSectionValues_logging" => $Iqs->getConfSectionValues("logging"),
			"getConfSectionValues_ezquoteapi" => $Iqs->getConfSectionValues("ezquoteapi"),
			"getConfSectionValues_statesenabled" => $Iqs->getConfSectionValues("statesenabled"),
			"getConfSectionValues_productsenabled" => $Iqs->getConfSectionValues("productsenabled"),
			"getConfSectionValues_whitelistenabled" => $Iqs->getConfSectionValues("whitelistenabled"),
			"getSystemData" => $Iqs->getSystemData()
		);
		
		foreach($data_array as $key => $value){
			Cache::set($key, $value);
		}
	}
	
	public static function set($key, $value){
		session_start();
		$_SESSION[$key] = $value;		
	}
	
	public static function get($key){
		session_start();
		return isset($_SESSION[$key]) ? $_SESSION[$key] : false;		
	}
}

?>