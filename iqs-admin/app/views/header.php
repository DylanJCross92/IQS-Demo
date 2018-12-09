<?php $Auth = new Auth();

if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

$parent_slug = strtolower($page_data->parent_slug);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $page_data->site_title;?></title>
<base href="<?php echo BASE_URL;?>">
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script src="js/jquery.js"></script>
<script src="js/global.js"></script>
</head>
<body class="admin-page <?php echo $parent_slug;?>-page">
<header>
	<div class="center">
    	<div class="multi-cols">
        	<div class="col-1">
            	<ul class="main-menu">
            		<li class="<?php if(strtolower($parent_slug) == "dashboard"){echo "current-page";}?>"><a href="dashboard">Dashboard</a></li>
                    <li class="<?php if(strtolower($parent_slug) == "settings"){echo "current-page";}?>"><a href="settings">Settings</a></li>
                    <!--<li class="<?php if(strtolower($parent_slug) == "products"){echo "current-page";}?>"><a href="products">Products</a></li>-->
                </ul>
            </div>
            <!--<div class="col-2">
            	<div class="heading">Internet Quoting Admin</div>
            </div>-->
            <div class="col-3">
            	<a class="logout button light-blue" href="logout.php?logout=true">Logout</a>
            </div>
    	</div>
    </div>
</header>
<section class="container center">