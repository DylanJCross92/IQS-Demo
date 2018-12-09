<?php
use Iqs\Dao\ConfigurationDataAccessor;
use Iqs\Util\DbAccessorFactory;

class Iqs 
{

	protected $Database;
	protected $ConfigData;
	public function __construct() {
        $configData = new ConfigurationDataAccessor();
		$dbAccessor = DbAccessorFactory::getDbAccessorObject($configData);

		$this->ConfigData = $configData;
		$this->Database = $dbAccessor;
	}
	
	public function getZipWhiteListValues() {
		return $this->Database->getZipWhiteListValues();
	}
	
	public function addZipWhiteListValues($arr) {
		return $this->Database->addZipWhiteListValues($arr);
	}
	
	public function updateZipWhiteListValues($arr) {
		return $this->Database->updateZipWhiteListValues($arr);
	}
	
	public function deleteZipWhiteListValues($arr) {
		return $this->Database->deleteZipWhiteListValues($arr);	
	}
	
	public function getBlockCodesValues() {
		return $this->Database->getBlockCodesValues();
	}
	
	public function addBlockCodesValues($arr) {
		return $this->Database->addBlockCodesValues($arr);
	}
	
	public function updateBlockCodesValues($arr) {
		return $this->Database->updateBlockCodesValues($arr);
	}
	
	public function deleteBlockCodesValues($arr) {
		return $this->Database->deleteBlockCodesValues($arr);	
	}
	
	public function getConfSectionValues($confSection, $ordered = true) {
		
		$sectionValues = $this->Database->getConfSectionValues($confSection);
		
		if($ordered)
		{
			$ordered = array();
			foreach($sectionValues as $sectionValue) {
				$ordered[$sectionValue["ConfElement"]] = array("key" => $sectionValue["ConfElement"], "value" => $sectionValue["ConfValue"]);
			}
		}
		
		return $ordered ?: $sectionValues;		
	}
	
	public function getConfElementValue($confSection, $confElement = false) {
		return $this->Database->getConfElementValue($confSection, $confElement);		
	}
	
	public function updateConfElementValue($confSection, $confElement, $confValue) {
		return $this->Database->updateConfElementValue($confSection, $confElement, $confValue);			
	}
	
	public function getSystemData() {

        $sessionUtil = new Iqs\Util\SessionUtil($this->ConfigData, $this->Database);
        $debugData = new Iqs\Model\DebugData();
        $apiAccessor = new Iqs\Dao\EzQuoteApiAccessor($sessionUtil, $this->ConfigData, $debugData);

        $ezqResponse = $apiAccessor->getEzQuoteVersion();
        $ezqVersion = "Unable To Access EZQuote";
        if(isset($ezqResponse["version"])){
            $ezqVersion = $ezqResponse["version"];
        }
        $iqsver = $this->ConfigData->getIqsVersion();

        $systemData[Iqs\Cnst\FieldKeys::$IQS_SYSTEM_DATA_DATE] = gmdate('Y-m-d');
        $systemData[Iqs\Cnst\FieldKeys::$IQS_SYSTEM_DATA_TIME] = gmdate('H:i:s');
        $systemData[Iqs\Cnst\FieldKeys::$IQS_SYSTEM_DATA_VERSION] = $iqsver;
        $systemData[Iqs\Cnst\FieldKeys::$IQS_SYSTEM_DATA_EZQVERSION] = $ezqVersion;

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        $dataResponsePackage->addResponseDataPayloadItem(Iqs\Cnst\FieldKeys::$IQS_PAYLOAD_ITEM_SYSTEM, $systemData);
        $jsonArray = json_decode($dataResponsePackage->getJsonDataResponsePackage(),true);
        return $jsonArray;

	    /*
		$service_url = HOST."/quotes/Iqs/api.php/getSystemData";
		$curl = curl_init($service_url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		//execute the session
		$curl_response = curl_exec($curl);
		
		//finish off the session
		curl_close($curl);
		$curl_jason = json_decode($curl_response, true);

		return $curl_jason;
	    */
	}
	
}
?>