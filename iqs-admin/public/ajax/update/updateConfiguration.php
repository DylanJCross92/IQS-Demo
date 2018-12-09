<?php require "../../../app/init.php";

$Auth = new Auth();
if(!$Auth->is_logged_in())
{
	$Auth->not_logged_in();	
}

$Iqs = new Iqs;
$confSection = $_POST["conf_section"];

if($confSection)
{
	unset($_POST["conf_section"]);
	
	$error = false;
	foreach($_POST as $key => $value)
	{
		if(empty($value))
		{
			$error = true;	
		}
		
		if(!$error)
		{
			$Iqs->updateConfElementValue($confSection, $key, $value);
			Cache::set("getConfSectionValues_".$confSection, $Iqs->getConfSectionValues($confSection));
		}
	}
}



?>