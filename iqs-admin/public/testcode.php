<?php

require '../../Iqs/Iqs.php';
\Iqs\Iqs::registerAutoloader();

//************************** SDR Added 7/28/2016 - IQS SDK Objects for DB and config access ***************************
use Iqs\Dao\ConfigurationDataAccessor;
use Iqs\Util\DbAccessorFactory;

$configData = new ConfigurationDataAccessor();
$dbAccessor = DbAccessorFactory::getDbAccessorObject($configData);

//Add new whitelist values, must be in order of zip, state, product
echo "<br/><br/>************************** Here we add the items below to the table *****************************<br/><br/>";
echo "array(\"12345\",\"NY\",\"HO3\"),<br/>
    array(\"11111\",\"VA\",\"DP3\"),<br/>
    array(\"99999\",\"SC\",\"HO3\"),<br/>
    array(\"99577\",\"AK\",\"HO3\"),<br/>
    array(\"99577\",\"AK\",\"DP3\"),<br/>
    array(\"77777\",\"AK\",\"HO3\")<br/><br/>";
$newWhiteListItemsArray = array(
    array("12345","NY","HO3"),
    array("11111","VA","DP3"),
    array("99999","SC","HO3"),
    array("99577","AK","HO3"),
    array("99577","AK","DP3"),
    array("77777","AK","HO3")
);

$dbAccessor->addZipWhiteListValues($newWhiteListItemsArray);

//Getting white list values
echo "<br/><br/>************************** Now we Show Table Items Using A Filter on ZipCode 99577 and Product DP3 *****************************<br/><br/>";

$filterA["ZipCode"] = "99577";
$filterA["Product"] = "DP3";

$whitelist = $dbAccessor->getZipWhiteListValues($filterA);
echo var_dump($whitelist);


echo "<br/><br/>************************** Filter on State AK *****************************<br/><br/>";

$filterB["State"] = "AK";

$whitelist = $dbAccessor->getZipWhiteListValues($filterB);
echo var_dump($whitelist);


echo "<br/><br/>************************** No Filter - pass in empty array to see all rows *****************************<br/><br/>";

$whitelist = $dbAccessor->getZipWhiteListValues();
echo var_dump($whitelist);



echo "<br/><br/>************************** Next we update, replacing all data with XXXXX values *****************************<br/><br/>";


//Updating existing whitelist values, we'll turn everything in the DB into "xxx" values as an example
/*
 *
 * The Array looks  like this when done, in case you want to build one manually
 * It is zipcode, state, product, whiteListId
$updateWhiteListItemsArray = array(
    array("xxxxx","xx","xxx","5"),
    array("xxxxx","xx","xxx","6"),
    array("xxxxx","xx","xxx","7")
);
 */
$updateWhiteListItemsArray = array();
foreach ($whitelist as $listItem){
    $updateWhiteListItemsArray[] = array("xxxxx","xx","xxx",$listItem["WhiteListId"]);
}
$dbAccessor->updateZipWhiteListValues($updateWhiteListItemsArray);

echo "<br/><br/>************************** Here we show all items as updated *****************************<br/><br/>";
$myArray = [];
$whitelist = $dbAccessor->getZipWhiteListValues($myArray);
echo var_dump($whitelist);

echo "<br/><br/>************************** Finally We delete everything in the table *****************************<br/><br/>";

//Delete everything in the table. Delete only needs an array of WhiteListId values
/*$deleteWhiteListItemsArray = array();
foreach ($whitelist as $listItem){
    $deleteWhiteListItemsArray[] = $listItem["WhiteListId"];
}
$dbAccessor->deleteZipWhiteListValues($deleteWhiteListItemsArray);
*/

echo "<br/><br/>************************** Here we show that the table is empty (there should be nothing below) *****************************<br/><br/>";
$myArray = [];
$whitelist = $dbAccessor->getZipWhiteListValues($myArray);
echo var_dump($whitelist);



?>








<?php 
/*
if(!$_SESSION["token"]){
	try {
		$passwordGrant = new \Stormpath\Oauth\PasswordGrantRequest('dylanc@owlsheadsolutions.com', 'Password123');
		
		$auth = new \Stormpath\Oauth\PasswordGrantAuthenticator($application);
		$result = $auth->authenticate($passwordGrant);
		
		$refreshTokenJwt = $result->getAccessTokenString();
		
		$_SESSION["token"] = $refreshTokenJwt;
		
		echo "</p>";
		echo "Logging In";
		
		
		// Refresh Token - start
		//$refreshGrant = new \Stormpath\Oauth\RefreshGrantRequest($refreshTokenJwt);
		//$auth = new \Stormpath\Oauth\RefreshGrantAuthenticator($application);
		//$result = $auth->authenticate($refreshGrant);
		// Refresh Token - end
	
	}
	catch (\Stormpath\Resource\ResourceError $e) {
	  // Login attempt failed.
	 
	  echo $e->getMessage();
	}
}

try {
	$result = (new \Stormpath\Oauth\VerifyAccessToken($application))->verify($_SESSION["token"]);
	
	echo "</p>";
	echo "Logged In";
}
catch (\Stormpath\Resource\ResourceError $e) {
  // Login attempt failed.
 
  echo $e->getMessage();
  
  unset($_SESSION["token"]);
}


//var_dump($result);


//Create a User Account
try {
	$account = \Stormpath\Resource\Account::instantiate([
	  'givenName' => 'Dylan',
	  'surname' => 'Cross',
	  'username' => 'dylanjcross922',
	  'email' => 'dylanc2@owlsheadsolutions.com',
	  'password' => 'Password123'
	]);

	//$customData = $account->customData;
	//$customData->favoriteColor = 'blue';
	
	$account = $application->createAccount($account);
}
catch (\Stormpath\Resource\ResourceError $re) {
	echo $re->getMessage();
}

echo "</p>";


try {
	$result = $application->authenticate('dylanc@owlsheadsolutions.com', 'Password123');
	$account = $result->account;
	
	echo "Logged in";
  
} catch (\Stormpath\Resource\ResourceError $e) {
  // Login attempt failed.
 
  echo $e->getMessage();
}

echo "</p>";

//Retrieve the Account
$accounts = $application->accounts;
$search = new \Stormpath\Resource\Search();
$accounts->search = $search->addEquals('username', 'dylanjcross92');

foreach($accounts as $account) {
	echo $account->username;
}*/
?>