<?php require_once "../app/init.php";

$template = trim(strtolower($_GET["template"]));

if($template)
{
	function array_to_csv_download($array, $filename = "export.csv", $delimiter=",") {
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
	
		$f = fopen('php://output', 'w');
	
		foreach ($array as $line) {
			fputcsv($f, $line, $delimiter);
		}
	} 
	
	$Iqs = new Iqs;
	
	if($template == "zipcodes") 
	{
		$zipcodes = $Iqs->getZipWhiteListValues();
		
		$formatted_zipcodes = array(
			array("state","product","zipcode")
		);
		foreach($zipcodes as $zipcode) {
			$formatted_zipcodes[] = array("state" => trim($zipcode["State"]), "product" => trim($zipcode["Product"]), "zipcode" => trim($zipcode["ZipCode"]));
		}
		
		$array = $formatted_zipcodes;
	}
	else if($template == "blockcodes")
	{
		$blockcodes = $Iqs->getBlockCodesValues();
		
		$formatted_blockcodes = array(
			array("BlockCode","BlockText")
		);
		foreach($blockcodes as $blockcode) {
			$formatted_blockcodes[] = array("BlockCode" => trim($blockcode["BlockCode"]), "BlockText" => trim($blockcode["BlockText"]));
		}
		
		$array = $formatted_blockcodes;
	}
	else if($template == "configuration")
	{
		$configurations = $Iqs->getConfSectionValues("", false);
		
		$formatted_configuration = array(
			array("Section", "Key","Value")
		);
		foreach($configurations as $configuration) {
			$formatted_configuration[] = array("section" => trim($configuration["ConfSection"]), "key" => trim($configuration["ConfElement"]), "value" => trim($configuration["ConfValue"]));
		}
		
		$array = $formatted_configuration;
	}
	else
	{
		die();
	}
	
	array_to_csv_download($array);
}
else
{
	echo "No template was selected.";	
}
?>