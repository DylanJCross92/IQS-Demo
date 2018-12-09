<?php require "../app/init.php";

$Auth = new Auth();
if($Auth->is_logged_in())
{
	header('Location: index.php');
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Insurance Quoting Service Admin - Login</title>
<link href="css/styles.css" rel="stylesheet" type="text/css">
<script src="js/jquery.js"></script>
<script src="js/global.js"></script>
</head>
<body class="login-page">

<header>
	<div class="center">
    	<div class="multi-cols">
            <div class="col-2">
            	<div class="heading">Insurance Quoting Service Admin</div>
            </div>
    	</div>
    </div>
</header>
    <div class="header">
    	<h1>Login</h1>
    </div>
    <div class="container">
        <form class="login-form" method="post" action="#">
            <div><input type="email" name="email" placeholder="Email"/></div>
            <div><input type="password" name="password" placeholder="Password"/></div>
            <div><input type="submit" name="submit" value="Log In" class="login button blue"/></div>
        </form>
    </div>

</body>
</html>