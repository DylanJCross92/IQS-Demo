<?php

class Products extends Controller 
{
	public function index($params)
	{
		$data = array();
		$view = "products/index";
		
		if($params[0])
		{
			$data = array("product" => $params[0]);
			$view = "products/product";
		}
		
		$this->page_data = $data;
		$this->setup_view($view);
		$this->render_view();
	}
}

?>