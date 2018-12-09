<?php //iqs-admin/app ?>
<?php session_start();

define("ROOT", dirname(__DIR__));
define("IQS_ROOT", dirname(dirname(__DIR__)));
/* Stormpath Config */
define("STORMPATH_API_KEY_FILE", ROOT."/app/vendor/stormpath/apiKey.properties");
define("STORMPATH_APPLICATION", "https://api.stormpath.com/v1/applications/3tYKm4UTVNYOaKUctCN9mp");
/* URL Config */
define("HOST", "http://localhost");
define("BASE_URL", HOST.":8080/iqs-admin/public/");

require IQS_ROOT."/Iqs/Iqs.php";
\Iqs\Iqs::registerAutoloader();

require_once "config.php";

spl_autoload_register(function($class_name) {
	
	if(file_exists(ROOT."/app/core/".$class_name.".php"))
	{
		require_once(ROOT."/app/core/".$class_name.".php"); 	
	}
	
	if(file_exists(ROOT."/app/models/".$class_name.".php"))
	{
		require_once(ROOT."/app/models/".$class_name.".php"); 	
	}
});
?>