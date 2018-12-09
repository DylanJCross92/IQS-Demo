<?php require "../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

$blockcodeId = !empty(trim($_POST["blockcodeId"])) ? trim($_POST["blockcodeId"]) : false;
$code = trim($_POST["code"]);
$blockcode_text = trim($_POST["blockcode_text"]);

if(!$code || !$blockcode_text)
{
	die();	
}

$addBlockCodesArray = array(
    array($code, $blockcode_text)
);

$Iqs = new Iqs;

if($blockcodeId)
{
	$updateBlockCodesArray = array(
		array($code, $blockcode_text, $blockcodeId)
	);
	
	$Iqs->updateBlockCodesValues($updateBlockCodesArray);
}
else
{
	$Iqs->addBlockCodesValues($addBlockCodesArray);
}

$getBlockCodesValues = $Iqs->getBlockCodesValues();
Cache::set("getBlockCodesValues", $getBlockCodesValues);
$blockcodes = json_decode(json_encode($Iqs->getBlockCodesValues()));

foreach($blockcodes as $blockCodesData):?> 
	<?php require ROOT."/app/views/parts/settings/blockcodes.table.row.php";?>
<?php endforeach;?>