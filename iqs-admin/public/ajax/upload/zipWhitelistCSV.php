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
	
	$state = strtolower(trim($row[0]));
	$product = strtolower(trim($row[1]));
	$zipcode = strtolower(trim($row[2]));
	
	if($i == 0)
	{
		if($state == "state" && $product == "product" && $zipcode == "zipcode")
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
		$data_to_db[] = array($zipcode, $state, $product); 
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

if(!$Iqs->addZipWhiteListValues($data_to_db)) {
	$response = array(
		"error" => true
	);
	
	$response["message"] = "Failed to insert rows";
}



$response = array(
		"error" => false
	);
	
$getZipWhiteListValues = $Iqs->getZipWhiteListValues();
Cache::set("getZipWhiteListValues", $getZipWhiteListValues);
$zipcodes = json_decode(json_encode($getZipWhiteListValues));

 foreach($zipcodes as $zipData):?> 
	<?php require ROOT."/app/views/parts/settings/zipcodes.table.row.php";?>
<?php endforeach;?>		

<?php //echo json_encode($response);	

?>