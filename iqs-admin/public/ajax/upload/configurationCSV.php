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

$Iqs = new Iqs;

$i = 0;
$valid = false;
$data_to_db = array();
foreach($data as $row) {
	
	$section = strtolower(trim($row[0]));
	$key = strtolower(trim($row[1]));
	$value = strtolower(trim($row[2]));
	
	if($i == 0)
	{
		if($section == "section" && $key == "key" && $value == "value")
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
		

		if(!$Iqs->updateConfElementValue($section, $key, $value)) {
			$response = array(
				"error" => true
			);
			
			$response["message"] = "Failed to insert rows";
		}
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

$data_array = array(
	"getConfSectionValues_logging" => $Iqs->getConfSectionValues("logging"),
	"getConfSectionValues_ezquoteapi" => $Iqs->getConfSectionValues("ezquoteapi"),
	"getConfSectionValues_statesenabled" => $Iqs->getConfSectionValues("statesenabled"),
	"getConfSectionValues_productsenabled" => $Iqs->getConfSectionValues("productsenabled"),
	"getConfSectionValues_whitelistenabled" => $Iqs->getConfSectionValues("whitelistenabled")
);

foreach($data_array as $key => $value){
	Cache::set($key, $value);
}

$response = array(
		"error" => false
	);

?>