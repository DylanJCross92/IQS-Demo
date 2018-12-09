<?php 

class Stormpath {
	
	public $application;
	
	public function __construct(){
		require ROOT.'/app/vendor/stormpath/vendor/autoload.php';
		
		Stormpath\Client::$apiKeyFileLocation = STORMPATH_API_KEY_FILE;
		$this->application = Stormpath\Resource\Application::get(STORMPATH_APPLICATION);
	}
	
	public function Application() {
		return $this->application;	
	}
	
}

?>