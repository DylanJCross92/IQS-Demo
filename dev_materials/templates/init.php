<?php //iqs-admin/app ?>
<?php session_start();

define("ROOT", dirname(__DIR__));
define("IQS_ROOT", dirname(dirname(__DIR__)));
/* Stormpath Config */
define("STORMPATH_API_KEY_FILE", ROOT."/app/vendor/stormpath/apiKey.properties");
define("STORMPATH_APPLICATION", "@iqs_admin_stormpathurl@");
/* URL Config */
define("HOST", "@iqs_admin_hosturl@");
define("BASE_URL", HOST."@iqs_admin_baseurl@");

require IQS_ROOT."@iqs_admin_iqspath@";
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