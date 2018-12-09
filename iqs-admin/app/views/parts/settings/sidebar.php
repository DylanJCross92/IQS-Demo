<?php 
$sidebar_menu = array(
			array("url" => "settings/zipcodes", "label" => "Zipcodes", "current_page" => $page_data->view),	
			array("url" => "settings/blockcodes", "label" => "Blockcodes", "current_page" => $page_data->view),
			array("url" => "settings/configuration", "label" => "Configuration", "current_page" => $page_data->view),
		);
?>
<ul class="sidebar-menu">
	<?php foreach($sidebar_menu as $menu):?>
		<li class="<?php if(strtolower($menu["current_page"]) == strtolower($menu["url"])){echo "current-page";}?>"><a href="<?php echo $menu["url"]?>"><?php echo $menu["label"]?></a></li>
    <?php endforeach;?>
</ul>