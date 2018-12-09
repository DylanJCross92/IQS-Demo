<?php require "../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

$whitelistId = !empty(trim($_POST["whitelistId"])) ? trim($_POST["whitelistId"]) : false;
$zipcode = trim($_POST["zipcode"]);
$state_product = trim(strtolower($_POST["state_product"]));

$state = substr($state_product, 0, 2);
$product = substr($state_product, 2, 5);

if(!$zipcode || !$state || !$product)
{
	die();
}

if(!preg_match("/^([0-9]{5})(-[0-9]{4})?$/i", $zipcode))
{
    return false;
}

$newWhiteListItemsArray = array(
    array($zipcode, $state, $product)
);


$Iqs = new Iqs;
if($whitelistId)
{
	$updateWhiteListItemsArray = array(
		array($zipcode, $state, $product, $whitelistId)
	);
	
	$Iqs->updateZipWhiteListValues($updateWhiteListItemsArray);
}
else
{
	$Iqs->addZipWhiteListValues($newWhiteListItemsArray);
}

$getZipWhiteListValues = $Iqs->getZipWhiteListValues();
Cache::set("getZipWhiteListValues", $getZipWhiteListValues);
$zipcodes = json_decode(json_encode($getZipWhiteListValues));

 foreach($zipcodes as $zipData): ?> 
	<?php require ROOT."/app/views/parts/settings/zipcodes.table.row.php";?>
<?php endforeach; ?>