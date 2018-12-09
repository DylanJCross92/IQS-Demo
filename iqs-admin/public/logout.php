<?php require "../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

if($_GET["logout"] == "true") {
	$Auth->logout(true);
}

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>GIQ Admin - Logout</title>
<link href="styles.css" rel="stylesheet" type="text/css">
<script src="js/jquery.js"></script>
<script src="js/global.js"></script>
</head>
<body class="logout-page">
    <div class="header">
    	<h1>Logging out..</h1>
    </div>
</body>
</html>