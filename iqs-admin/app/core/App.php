<?php

class App 
{
	protected $Config;
	protected $site_root;
	
	protected $controller = "";
	protected $controllerObj = "";
	protected $method = "index";
	protected $params = array();
	
	public function __construct()
	{
		$url = $this->parseUrl();
		
		//Load Config to set default Controller from config
		$this->Config = new Config;
		$this->site_root = $this->Config->site_config("site_root");	
		$this->controller = $this->Config->site_config("landing_page");
		
		if(file_exists($this->site_root."/app/controllers/".$url[0].".php"))
		{
			$this->controller = $url[0];
			unset($url[0]);
		}
		
		require_once $this->site_root."/app/controllers/".$this->controller.".php";
		
		$this->controllerObj = new $this->controller;
		
		if(isset($url[1]))
		{
			if(method_exists($this->controllerObj, $url[1])) 
			{
				$this->method = $url[1];
				unset($url[1]);
			}
		}
		
		$this->params = $url ? array_values($url) : [];
		
		//$this->params = array("controller" => $this->controller, "method" => $this->method);
		
		call_user_func_array([$this->controllerObj, $this->method], [$this->params]);
	}
	
	protected function parseUrl() 
	{
		if(isset($_GET["url"]))
		{
			return $url = explode("/", filter_var(rtrim($_GET["url"], "/"), FILTER_SANITIZE_URL));
		}
	}
}

?>