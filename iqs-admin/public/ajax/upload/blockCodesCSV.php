<?php require "../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

// Check if file was uploaded
if(!isset($_FILES["csv"])) {
	$response = array(
			"error" => true
		);
	
	$response["message"] = "No file uploaded";
	echo json_encode($response);	
	
	return false;	
}

// Check if file is a CSV file	
if(pathinfo($_FILES["csv"]["name"], PATHINFO_EXTENSION) != "csv") 
{
	$response = array(
			"error" => true
		);
	
	$response["message"] = "The uploaded file is not a CSV";
	echo json_encode($response);	
	
	return false;	
}

// Check if file has content
if ($_FILES["csv"]["size"] <= 0) {
	$response = array(
		"error" => true
	);

	$response["message"] = "The uploaded file is empty";	
	echo json_encode($response);	
	
	return false;
}

// Get the CSV file
$file = $_FILES["csv"]["tmp_name"];
$handle = fopen($file,"r");

$data = array();
while($row = fgetcsv($handle)) {
   $data[] = $row;
}

if(count($data)<=0)
{
	$response = array(
		"error" => true
	);

	$response["message"] = "The uploaded file is empty";
	echo json_encode($response);	
	
	return false;	
}

$i = 0;
$valid = false;
$data_to_db = array();
foreach($data as $row) {
	
	$blockCode = strtolower(trim($row[0]));
	$blockText = strtolower(trim($row[1]));
	
	if($i == 0)
	{
		if($blockCode == "blockcode" && $blockText == "blocktext")
		{
			$valid = true;
		}
		else
		{
			$valid = false;	
		}
	}
	else if($valid)
	{
		$data_to_db[] = array($blockCode, $blockText); 
	}
	else
	{
		$response = array(
			"error" => true
		);
	
		$response["message"] = "There was an error with the format of the file";
	}
	
	$i++;	
}

$Iqs = new Iqs;

if(!$Iqs->addBlockCodesValues($data_to_db)) {
	$response = array(
		"error" => true
	);
	
	$response["message"] = "Failed to insert rows";
}

$response = array(
		"error" => false
	);
	
	
$getBlockCodesValues = $Iqs->getBlockCodesValues();
Cache::set("getBlockCodesValues", $getBlockCodesValues);
$blockcodes = json_decode(json_encode($Iqs->getBlockCodesValues()));

 foreach($blockcodes as $blockCodesData):?> 
	<?php require ROOT."/app/views/parts/settings/blockcodes.table.row.php";?>
<?php endforeach;?>		