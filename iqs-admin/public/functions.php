<?php

function root_url() {
	
	$pagename = isset($_GET["page"]) ? $_GET["page"] : "dashboard";
	
	return "?page=".$pagename;
}

?>