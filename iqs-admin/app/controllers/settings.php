<?php

class Settings extends Controller 
{
	public function index()
	{
		/* Set default method to zipcodes */
		$this->zipcodes();
	}
	
	public function zipcodes() 
	{
		$data = array(
			"zipcodes_list" => Cache::get("getZipWhiteListValues")
		);
		
		$this->page_data = $data;
		$this->setup_view("settings/zipcodes");		
		$this->render_view();
	}
	
	public function blockcodes() 
	{
		$data = array(
			"blockCodes" => Cache::get("getBlockCodesValues")
		);
		
		$this->page_data = $data;
		$this->setup_view("settings/blockcodes");
		$this->render_view();
	}
	
	public function configuration() 
	{
		$data = array(
			"conf" => array(
				"env" => Cache::get("getConfSectionValues_env"),
				"logging" => Cache::get("getConfSectionValues_logging"),
				"ezquoteapi" => Cache::get("getConfSectionValues_ezquoteapi"),
				"statesenabled" => Cache::get("getConfSectionValues_statesenabled"),
				"productsenabled" => Cache::get("getConfSectionValues_productsenabled"),
				"whitelistenabled" => Cache::get("getConfSectionValues_whitelistenabled")
			)
		);
		
		$this->page_data = $data;
		$this->setup_view("settings/configuration");
		$this->render_view();
	}
	
}

?>