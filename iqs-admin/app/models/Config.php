<?php

class Config 
{
	protected $config;
	
	public function __construct() {
		global $config;
		$this->config = $config;	
	}
	
	public function clean_name($name) {
		return preg_replace('/\/index$/', '', $name);	
	}
	
	public function site_config($key = false) {
		
		if($key) 
		{
			if(array_key_exists($key, $this->config["site"]))
			{
				return $this->config["site"][$key];
			}
		}
		else
		{
			return $this->config["site"];
		}
		
		return false;
	}
	
	public function templates_config($name, $key = false) {
		
		$name = $this->clean_name($name);
		$arr = array_unique(array_merge($this->config["templates"]["default"], $this->config["templates"][$name]));
		
		if($key) 
		{
			if(array_key_exists($key, $arr))
			{
				return $arr[$key];
			}
		}
		else
		{
			return $this->config["templates"];
		}
		
		return false;
	}
	
	public function site_name() {
		return $this->site_config("name");	
	}
	
	public function page_title($name) {
		
		return $this->templates_config($name, "page_title");
	}
	
	public function site_title($name) {
		return $this->site_name()." | ". $this->page_title($name);		
	}
	
	public function landing_page() {
		return $this->site_config("landing_page");	
	}

	public function state_from_abbrev($abbr) {
		return $this->config["full_states"][$abbr] ? $this->config["full_states"][$abbr] : $abbr;
	}
	
}

?>