<?php require "../../../app/init.php";

$email = $_POST["email"];                                   
$password = $_POST["password"];

$Stormpath = new Stormpath();
$application = $Stormpath->Application();
		
try {
	$passwordGrant = new \Stormpath\Oauth\PasswordGrantRequest($email, $password);
	
	$auth = new \Stormpath\Oauth\PasswordGrantAuthenticator($application);
	$result = $auth->authenticate($passwordGrant);
	
	$_SESSION["login_token"] = $result->getAccessTokenString();
	$_SESSION["login_token_href"] = $result->getAccessTokenHref();
	
	$response = array(
				"error" => false
			);
}
catch (\Stormpath\Resource\ResourceError $e) {
  	// Login attempt failed.
  	$response = array(
				"error" => true
			);
	$response["error_messages"]["email"] = $e->getMessage();
}

Cache::setup();

echo json_encode($response);	
?>