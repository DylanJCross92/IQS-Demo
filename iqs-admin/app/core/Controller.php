<?php

class Controller 
{
	protected $Config;
	protected $site_root;
	protected $Database;
	protected $view;
	
	protected $site_title;
	protected $page_data;
	protected $parent_slug;
	protected $template_file;
	
	public function __construct() {
		$this->Config = new Config;
		$this->site_root = $this->Config->site_config("site_root");	
		
		$this->Database = new Iqs;
	}
	
	public function model($model)
	{
		//TODO: Check if file exists before requiring
		require_once $this->site_root."/app/models/".$model.".php";
		return new $model();
	}
	
	public function load($file, $pageData) 
	{
		if(file_exists($file))
		{
			require_once $file;	
		}
		//TODO: If file doesn't exists, show error
	}
	
	public function parent_slug() {
		$url = explode("/", filter_var(rtrim($this->view, "/"), FILTER_SANITIZE_URL));
		$this->parent_slug = $url[0];
	}
	
	public function get_parts($filename) {
		
		$file = $this->site_root."/app/views/parts/".$this->parent_slug."/".$filename.".php";
		
		if(file_exists($file))
		{
			return $file;	
		}
		
		return false;
	}
	
	public function get_view() {
		
		$file = $this->site_root."/app/views/".$this->view.".php";
		
		if(file_exists($file))
		{
			return $file;	
		}
		
		return false;	
	}
	
	public function header() {
		
		$file = $this->site_root."/app/views/".$this->Config->templates_config($this->view, "header").".php";
		
		if(file_exists($file))
		{
			return $file;	
		}
		
		return false;
	}
	
	public function footer() {
		
		$file = $this->site_root."/app/views/".$this->Config->templates_config($this->view, "footer").".php";
		
		if(file_exists($file))
		{
			return $file;	
		}
		
		return false;
	}
	
	public function setup_view($view)
	{
		//Setup the $view
		$this->view = $view;	
		
		//Setup the $parent_slug
		$url = explode("/", filter_var(rtrim($this->view, "/"), FILTER_SANITIZE_URL));
		$this->parent_slug = $url[0];
		
		//Setup the $site_title
		$this->site_title = $this->Config->site_title($this->view) ?: "no title";
		
		//Setup the $template_file
		$this->template_file = $this->site_root."/app/views/".$this->parent_slug."/index.php";
		
		//Setup the $page_data
		$this->page_data = array(
			"site_title" => $this->site_title,
			"view" => $this->view,
			"parent_slug" => $this->parent_slug,
			"data" => $this->page_data
		);
		
		//Convert $page_data to object
		$this->page_data = (object)$this->page_data;
		
	}
	
	public function render_view() 
	{
		$page_data = $this->page_data;
		require $this->header();
		
		$template = new Template();
		$template->page_data = json_decode(json_encode($this->page_data));
		$template->sidebar = $template->render($this->get_parts("sidebar"));
		$template->content = $template->render($this->get_view());
		
		echo $template->render($this->template_file);
		
		require $this->footer();
	}
}

?>