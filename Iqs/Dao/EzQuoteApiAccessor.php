<?php
/**
 * EzQuoteApiAccessor
 *
 * Handles communications to the EZ Quote API
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */


namespace Iqs\Dao;
use Iqs\Exception\IqsException;
use Iqs\Model\IQuoteAddress;
use Iqs\Model\EzQuoteAddress;
use Iqs\Model\Session;
use Iqs\Cnst\InformationCode;
use Iqs\Cnst\FieldKeys;
use Iqs\Util\SessionUtil;
use Iqs\Model\DebugData;

define('POST','post');
define('PUT','put');
define('GET','get');
define('AUTH','auth');

class EzQuoteApiAccessor implements IQuoteApiAccessor
{

    private $failedAuth = false;
    private $sessionUtil;
    private $configData;
    private $configDataObj;
    private $debugData;
    private $curMethod = "";

    public function __construct(SessionUtil $sessionUtil, IConfigurationDataAccessor $configData, DebugData &$debugData)
	{
        $this->configData = $configData->getConfigData(FieldKeys::$IQS_CONF_SECTION_EZQUOTEAPI);
        $this->configDataObj = $configData;
        $this->sessionUtil = $sessionUtil;
        $this->debugData = $debugData;
    }

    public function validateAddress(IQuoteAddress $address, Session $activeSession)
    {

        $ezQuoteAddressResponse = $this->executeEzQuoteApiCall("ezaddresses", POST, $activeSession, $address->getAddressAsJson());

        $quoteAddressArray = $this->createQuoteAddressArray($ezQuoteAddressResponse);

        return $quoteAddressArray;

    }

    public function createNewQuote(Session $activeSession)
    {

        $response = $this->executeEzQuoteApiCall("ezquotes", POST, $activeSession, $activeSession->getQuoteFieldsAsJson());

        //analyze the response and throw error if invalid
        $quoteId="";
        if(isset($response["id"])){
            $quoteId=$response["id"];
        }

        if($quoteId==""){
            $unexpectedResponseEx = new IqsException(InformationCode::$SYS_EZQUOTE_UNEXPECTED_RESPONSE);
            throw $unexpectedResponseEx;
        }

        return $quoteId;

    }

    public function updateQuote(Session $activeSession)
    {
        $methodNameWithArg = "ezquotes/" . $activeSession->getQuoteId();
        $ezqResponse = $this->executeEzQuoteApiCall($methodNameWithArg, POST, $activeSession, $activeSession->getQuoteFieldsAsJson());
        return $ezqResponse;
    }

    public function generateSavedQuote(Session $activeSession)
    {
        $methodNameWithArg = "ezrce/" . $activeSession->getQuoteId();

        //Verify we get valid ezq quote array back
        $ezQuoteResponse = $this->executeEzQuoteApiCall($methodNameWithArg, POST, $activeSession, $activeSession->getQuoteFieldsAsJson());
        if(!is_array($ezQuoteResponse)){
            throw new IqsException(InformationCode::$SYS_EZQUOTE_UNEXPECTED_RESPONSE);
        }

        return $ezQuoteResponse;
    }

    public function generateTempQuote(Session $activeSession)
    {
        $methodNameWithArg = "ezrerate/" . $activeSession->getQuoteId();
        $ezQuoteResponse = $this->executeEzQuoteApiCall($methodNameWithArg, POST, $activeSession, $activeSession->getQuoteFieldsAsJson());
        //Verify we get valid ezq quote array back
        if(!is_array($ezQuoteResponse)){
            throw new IqsException(InformationCode::$SYS_EZQUOTE_UNEXPECTED_RESPONSE);
        }
        return $ezQuoteResponse;
    }

    public function recallQuote(Session $activeSession){
        $methodNameWithArg = "ezquotes/" . $activeSession->getQuoteId();

        //Verify we get valid ezq quote array back
        $ezQuoteResponse = $this->executeEzQuoteApiCall($methodNameWithArg, GET, $activeSession);
        if(!is_array($ezQuoteResponse)){
            throw new IqsException(InformationCode::$SYS_EZQUOTE_UNEXPECTED_RESPONSE);
        }

        return $ezQuoteResponse;
    }

    public function getEzQuoteVersion(){
        $methodName = "version";
        $curl = curl_init();
        $url = $this->configData[FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_BASEURL] . $methodName;
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //return the data if 1, return true/false if zero
        $ezquoteResponse = curl_exec($curl);
        return json_decode($ezquoteResponse,true);
    }

    public function getHomeFeatures(Session $activeSession){
        $methodNameWithArg = "homefeatures";

        $ezQuoteResponse = $this->executeEzQuoteApiCall($methodNameWithArg, POST, $activeSession, $activeSession->getHomeFeaturesRequestDataAsJson());
        //Verify we get valid ezq quote array back
        if(!is_array($ezQuoteResponse)){
            throw new IqsException(InformationCode::$SYS_EZQUOTE_UNEXPECTED_RESPONSE);
        }
        return $ezQuoteResponse;
    }

    private function authenticate(Session $activeSession){

        $credentials = $this->sessionUtil->getSessionCredentials($activeSession);
        $authArray['User']=$credentials->getUid($this->curMethod);
        $authArray['Password']=$credentials->getPassword($this->curMethod);
        $authJson=json_encode($authArray);
        $authReturn = $this->executeEzQuoteApiCall("auth", AUTH, $activeSession, $authJson);
        $authToken = $authReturn[FieldKeys::$EZ_QUOTE_AUTH];
        $credentials->setTemporaryToken($authToken);
        $this->sessionUtil->updateSessionToken($credentials);

    }


    private function executeEzQuoteApiCall($methodName, $type, Session $activeSession, $data = "")
    {
        $this->curMethod = $methodName;
        $curl = curl_init();
        $curState = strtoupper($activeSession->getState());
        $url = $this->configData[FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_BASEURL] . $methodName;

        $credentials = $this->sessionUtil->getSessionCredentials($activeSession);
        $authToken = $credentials->getToken($methodName);

        switch ($type) {
            case POST:
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Authorization: Bearer ". $authToken, "Content-Type: application/json"));
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case PUT: //Note: cURL PUT calls always return a 500 error against EZQ API, hence why we use POST for updateQuote
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Authorization: Bearer ". $authToken, "Content-Type: application/json", "Content-Length: " . strlen($data)));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case GET:
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Authorization: Bearer ". $authToken, "Content-Type: application/json"));
                break;
            case AUTH:
                curl_setopt ($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/json", "Product: ".$curState));
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Authorization: Bearer ". $authToken, "Content-Type: application/json"));
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //return the data if 1, return true/false if zero
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $ezquoteResponse = curl_exec($curl);

        $info =  curl_getinfo($curl);
        $httpResponseCode = $info['http_code'];

        //EZQ V2 Compatibility Update - 11/8/2016 SDR
        $ezqDecoded = json_decode($ezquoteResponse,true);

        //EZQ V2 Compatibility update - 1/19/2017 SDR (EZQ now sends http codes back as "status" JSON values. Always a 200 now with http code embedded in JSON payload)
        $ezqEntityValue = "";
        if($type==AUTH){
            $ezqEntityValue = $ezqDecoded;
        }else{
            $ezqEntityValue = json_decode($ezqDecoded[FieldKeys::$EZ_QUOTE_ENTITY], true);
            $ezqStatusValue = json_decode($ezqDecoded[FieldKeys::$EZ_QUOTE_STATUS], true);

            if(($httpResponseCode==200) && ($ezqStatusValue != 200)){
                $httpResponseCode = $ezqStatusValue;
            }

        }


        //add any EZQ api errors or messages to the debug object
        $this->checkForApiMessages($ezqEntityValue, $ezquoteResponse);

        //codes
        //401 - credentials required
        //409 (CONFLICT) is returned on a get quote if the createUser and updateUser do not match.
        //405 (METHOD NOT ALLOWED)  is returned on update quote if the updateUser does not match the current user and it’s not a test policy.

        //200 - OK (expected result)
        //204 - No Content
        //206 - Partial Content

        //in the event that we get a 401 error we need to run auth and get a new key.  However, we don't want to get stuck in a loop, so only allow once per http session.
        if($httpResponseCode==409){

            //this may mean that the quote has been touched by a GEICO rep or someone other than the original user
            //first check to see if we have already attempted a re-run of the auth this http session
            if($this->failedAuth==true){
                throw new IqsException(InformationCode::$SYS_EZQUOTE_CONFLICT);
            }else{
                //if we're here then this is our first re-try at auth
                $this->failedAuth=true;
                $this->authenticate($activeSession);
                //now that we should have a legit auth code, re-run this call
                $ezquoteResponse = $this->executeEzQuoteApiCall($methodName, $type, $activeSession, $data);
                //curl returns false if failure
                if ($ezquoteResponse == false) {
                    throw new IqsException(InformationCode::$SYS_EZQUOTE_CONFLICT);
                }
                return $ezquoteResponse;//return it raw as the real call should have decoded the json
            }

        }
        elseif($httpResponseCode==405){

            //this may mean that the quote has been touched by a GEICO rep or someone other than the original user
            //first check to see if we have already attempted a re-run of the auth this http session
            if($this->failedAuth==true){
                throw new IqsException(InformationCode::$SYS_EZQUOTE_METHOD_NOT_ALLOWED);
            }else{
                //if we're here then this is our first re-try at auth
                $this->failedAuth=true;
                $this->authenticate($activeSession);
                //now that we should have a legit auth code, re-run this call
                $ezquoteResponse = $this->executeEzQuoteApiCall($methodName, $type, $activeSession, $data);
                //curl returns false if failure
                if ($ezquoteResponse == false) {
                    throw new IqsException(InformationCode::$SYS_EZQUOTE_METHOD_NOT_ALLOWED);
                }
                return $ezquoteResponse;//return it raw as the real call should have decoded the json
            }


        }
        elseif($httpResponseCode==401 || $httpResponseCode==403){
            //auth code may be bad.  attempt re-auth and then run again

            //first check to see if we have already attempted a re-run of the auth this http session
            if($this->failedAuth==true){
                throw new IqsException(InformationCode::$SYS_EZQUOTE_COM_ERROR);
            }else{
                //if we're here then this is our first re-try at auth
                $this->failedAuth=true;
                $this->authenticate($activeSession);
                //now that we should have a legit auth code, re-run this call
                $ezquoteResponse = $this->executeEzQuoteApiCall($methodName, $type, $activeSession, $data);
                //curl returns false if failure
                if ($ezquoteResponse == false) {
                    throw new IqsException(InformationCode::$SYS_EZQUOTE_COM_ERROR);
                }
                return $ezquoteResponse;//return it raw as the real call should have decoded the json
            }
        }else{
            //curl returns false if failure
            if ($ezquoteResponse === false) {
                throw new IqsException(InformationCode::$SYS_EZQUOTE_COM_ERROR);
            }




            return $ezqEntityValue;
        }
    }


    private function createQuoteAddressArray($decodedEzQuoteAddressArray){
        $quoteAddressArray = array();
        $addressId = 0;
        if (count($decodedEzQuoteAddressArray)>0){

            if(!$this->isAddressValid($decodedEzQuoteAddressArray[0])){
                throw new IqsException(InformationCode::$SYS_ADDRESS_NO_ADDRESS_FOUND);
            }

            foreach($decodedEzQuoteAddressArray as $decodedAddress){

                if($this->isAddressValid($decodedAddress)){
                    $quoteAddress = $this->generateAddressObject($decodedAddress, $addressId);
                    $quoteAddressArray[]=$quoteAddress;
                }

                $addressId++;
            }
        }
        else{
            throw new IqsException(InformationCode::$SYS_EZQUOTE_COM_ERROR);
        }
        return $quoteAddressArray;
    }

    private function generateAddressObject($decodedAddress, $addressId){
        $quoteAddress = new EzQuoteAddress($decodedAddress);
        $quoteAddress->setAddressId($addressId);
        $basicInfo = $decodedAddress["AddressBasicInfo"];
        $fullInfo = $decodedAddress["AddressFullInfo"];
        $quoteAddress->updateInfoFields($basicInfo);
        $quoteAddress->updateInfoFields($fullInfo);
        return $quoteAddress;
    }

    private function isAddressValid(array $address){
        //we can check the first item for bogus elements to see if the address sent in is bogus or not found
        //we need state, zip, street, city
        $streetNum = (isset($address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER]) ?
            $address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER] : "");
        $street = (isset($address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NAME]) ?
            $address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NAME] : "");
        $city = (isset($address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_CITY])?
            $address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_CITY] : "");
        $state = (isset($address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STATE])?
            $address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STATE] : "");
        $zip = (isset($address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE]) ?
            $address[FieldKeys::$EZ_QUOTE_ADDRESS_BASIC_INFO][FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE] : "");

        if(strlen($streetNum==0)||
            strlen($street)==0 ||
            strlen($city)==0  ||
            strlen($state)==0 ||
            strlen($zip)==0){ //empty zip code, bogus address returned
            return false;
        }
        return true;
    }

    private function checkForApiMessages($apiResponseJson, $apiResponseRaw){
        if(isset($apiResponseJson[FieldKeys::$IQS_INVALID_QUOTE_DATA_KEY_MESSAGE])){
            $this->debugData->setApiMessage($apiResponseJson[FieldKeys::$IQS_INVALID_QUOTE_DATA_KEY_MESSAGE]);
        }else{
            if(is_string($apiResponseRaw) && !empty($apiResponseRaw)){
                $this->debugData->setApiMessage(json_encode($apiResponseRaw));
            }
        }
        if(isset($apiResponseJson[FieldKeys::$IQS_INVALID_QUOTE_DATA_KEY_ERRORS])) {
            $this->debugData->setApiError($apiResponseJson[FieldKeys::$IQS_INVALID_QUOTE_DATA_KEY_ERRORS]);
        }


    }

}