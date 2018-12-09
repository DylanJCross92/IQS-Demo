<?php
/**
 * ConfigurationDataAccessor
 *
 * Reads configuration files and provides config data
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Dao{


    use Iqs\Exception\IqsException;
    use Iqs\Cnst\InformationCode;
    use Iqs\Model\Session;
    use Iqs\Util\DbAccessorFactory;
	use Iqs\Cnst\FieldKeys;

    class ConfigurationDataAccessor implements IConfigurationDataAccessor
    {

        private $configData;
        private $dbAccessor;

        public function __construct($configFilePathIni = 'filePaths.ini')
        {

            $configFile = $this->findIqsConfigFile($configFilePathIni);
            $this->readConfigFile($configFile);
            $this->dbAccessor = DbAccessorFactory::getDbAccessorObject($this);
            //$this->readDbConfig();

        }

        private function findIqsConfigFile($configFilePath){
            $configFilePath = dirname(__FILE__)."/../Conf/".$configFilePath;
            if (!$configFilePath = parse_ini_file($configFilePath, TRUE)) throw new IqsException(InformationCode::$SYS_MISSING_FILE_PATH_INI);
            return $configFilePath[FieldKeys::$IQS_CONFIGURATION_IQSCONF][FieldKeys::$IQS_CONFIGURATION_FILEPATH];
        }

        private function readConfigFile($configFile){
            if (!$this->configData = parse_ini_file($configFile, TRUE)){
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_PARSE_CONFIG_FILE);
            }
        }

        public function getConfigData($configSection){

            if(array_key_exists($configSection,$this->configData)) {
                return $this->configData[$configSection];
            }else{
                $dbconfig = $this->dbAccessor->getConfSectionValues();
                foreach(FieldKeys::getValidIqsConfSections() as $section){
                    foreach($dbconfig as $index=>$configElement) {
                        if ($configElement[FieldKeys::$IQS_DATABASE_CONF_SECTION] == $section) {
                            $elementsArray[$configElement[FieldKeys::$IQS_DATABASE_CONF_ELEMENT]] = $configElement[FieldKeys::$IQS_DATABASE_CONF_VALUE];
                            $this->configData[$section] = $elementsArray;
                            unset($dbconfig[$index]);
                        }
                    }
                    unset($elementsArray);
                }
            }
            return $this->configData[$configSection];

        }

        public function getConfigValue($configSection, $configKey){
            $value = "";
            $sectionArray = $this->getConfigData($configSection);

            if(array_key_exists($configKey, $sectionArray)){
                $value = $sectionArray[$configKey];
            }

            return $value;
        }

        //returns the DP3 config value for the state based on FEID
        public function isDpEnabled(Session $activeSession){
            $geostate = $activeSession->getState();
            $product = $geostate."dp3";
            $dpStatusAr = $this->getConfigData(FieldKeys::$IQS_CONF_SECTION_PRODUCTS);
            $dp3enabled = "false";
            if(array_key_exists($product,$dpStatusAr)){
                if($dpStatusAr[$product] == "true"){
                    $dp3enabled = "true";
                }
            }
            return $dp3enabled;
        }

        //returns the DP3 config value for the state based on FEID
        public function isProductEnabled(Session $activeSession, $insuranceProduct){
            $geostate = $activeSession->getState();
            $product = $geostate.$insuranceProduct;
            $prodStatusAr = $this->getConfigData(FieldKeys::$IQS_CONF_SECTION_PRODUCTS);
            $prodEnabled = "false";
            if(array_key_exists($product,$prodStatusAr)){
                if($prodStatusAr[$product] == "true"){
                    $prodEnabled = "true";
                }
            }
            return $prodEnabled;
        }

        public function isSupportedZip($zipCode, $state, $product){

            $whiteListStatus = $this->getConfigData(FieldKeys::$IQS_CONF_SECTION_WHITELISTENABLED);
            $fullprod = $state.$product;
            $enabled = $whiteListStatus[$fullprod];

            //if we aren't using the white list for this product then we always return that the zip is supported
            if($enabled == "false"){
                return true;
            }

            //if the whitelist is enabled then we need to examine the whitelist table, build a filter to send to the db accessor
            $filter = array(
                FieldKeys::$IQS_DATABASE_WHITELIST_FILTER_ZIPCODE => $zipCode,
                FieldKeys::$IQS_DATABASE_WHITELIST_FILTER_STATE => $state,
                FieldKeys::$IQS_DATABASE_WHITELIST_FILTER_PRODUCT => $product,
            );
            $whiteListValues = $this->dbAccessor->getZipWhiteListValues($filter);

            //was our zip in the list? if there is any row returned then the answer is yes.
            $rowCount = count($whiteListValues);
            if($rowCount>0){
                //the zip, state and prod are in the whitelist, return true
                return true;
            }

            return false;
        }

        public function getIqsVersion(){

            $versionFile=__DIR__ . "/../VERSION";
            $verFileCont = file_get_contents($versionFile);

            if((strpos($verFileCont, 'version=') !== false)){
                $iqsVersion = substr($verFileCont, strpos($verFileCont, "version=") + strlen("version="));
            }
            return $iqsVersion;
        }
    }
}