<?php

class Dashboard extends Controller 
{
	public function index()
	{
		$data = array();
		
		$this->page_data = $data;
		$this->setup_view("dashboard/index");
		$this->render_view();
	}	
}

?>