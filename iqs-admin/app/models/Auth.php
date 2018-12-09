<?php 

class Auth {
	
	private $login_token;
	private $application;
	
	public function __construct(){
		
		$Stormpath = new Stormpath();
		$this->application = $Stormpath->Application();
		
		$this->login_token = isset($_SESSION["login_token"]) ? $_SESSION["login_token"] : false;
	}
	
	public function clear_sessions() {
		session_unset();
    	session_destroy();
	}
	
	public function is_logged_in() {
		
		if(!$this->login_token)
		{
			$this->clear_sessions();
			return false;	
		}
		
		try {
			(new \Stormpath\Oauth\VerifyAccessToken($this->application))->verify($this->login_token);
			return true;
		}
		catch (\Stormpath\Resource\ResourceError $e) {
			
			$this->clear_sessions();
			
		  	return false;
		}
	}
	
	public function not_logged_in() {
		header('Location: '.BASE_URL.'login.php');	
	}
	
	public function logout($logout = false) {
		
		if(!isset($_SESSION["login_token_href"]) || !$logout) 
		{
			header('Location: '.BASE_URL.'login.php');	
		}
		
		try {
			$token = Stormpath\Resource\AccessToken::get($_SESSION["login_token_href"]);
			$token->delete();
			
			$this->clear_sessions();
			 
			header('Location: '.BASE_URL.'login.php');
		}
		catch (\Stormpath\Resource\ResourceError $e) {
			header('Location: '.BASE_URL.'login.php');
		}
	}

}
?>