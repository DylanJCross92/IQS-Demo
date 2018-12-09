<?php require "../../../app/init.php";
/*
$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}*/

$whitelistId = trim($_POST["whitelistId"]);

if(!$whitelistId)
{
	die();
}

$deleteWhiteListItemsArray = array($whitelistId);

$Iqs = new Iqs;
$Iqs->deleteZipWhiteListValues($deleteWhiteListItemsArray);

Cache::set("getZipWhiteListValues", $Iqs->getZipWhiteListValues());
?>