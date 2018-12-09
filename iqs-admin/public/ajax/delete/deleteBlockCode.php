<?php require "../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

$blockCodeId = trim($_POST["blockCodeId"]);

if(!$blockCodeId)
{
	die();
}

$deleteBlockCodesItemsArray = array($blockCodeId);

$Iqs = new Iqs;
$Iqs->deleteBlockCodesValues($deleteBlockCodesItemsArray);

Cache::set("getBlockCodesValues", $Iqs->getBlockCodesValues());

?>