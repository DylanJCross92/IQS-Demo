<?php
$iqsversion="1.8.11_dev_20170409_135003";

ini_set("odbc.defaultlrl", "100k");

require 'Iqs.php';
\Iqs\Iqs::registerAutoloader();

//TODO: We can put using statements here to shorten the class access paths throughout this file
use Iqs\Cnst\FieldKeys;
use \Iqs\Model\DataResponsePackage;

/** Slim Framework provides the REST API features
Slim API found here: http://dev.slimframework.com/phpdocs/namespaces/Slim.html
 */
require 'Vendor/Slim/Slim.php';
require('Vendor/dompdf/dompdf_config.inc.php');
\Slim\Slim::registerAutoloader();


/** Instantiate a Slim application */
$app = new \Slim\Slim();
$app->hook('slim.after','executeAfter');

/**REST RULES:
 * Create = PUT if and only if you are sending the full content of the specified resource (URL).
 * Create = POST if you are sending a command to the server to create a subordinate of the specified resource, using some server-side algorithm.
 * Retrieve = GET.
 * Update = PUT if and only if you are updating the full content of the specified resource.
 * Update = POST if you are requesting the server to update one or more subordinates of the specified resource.
 * Delete = DELETE.
 *
 */


$app -> post('/validateAddress', 'validateAddress');
$app -> post('/beginQuoteSession', 'beginQuoteSession');
$app -> put('/updateQuoteSession', 'updateQuoteSession');
$app -> post('/generateSavedQuote', 'generateSavedQuote');
$app -> post('/generateTempQuote', 'generateTempQuote');
$app -> get('/recallQuote/:quoteId/:zipCode','recallQuote');
$app -> get('/getSelectedQuoteData/:sessionId/:sessionQuoteDataArray','getSelectedQuoteData');
$app -> post('/logQuoteSessionBlock','logQuoteSessionBlock');
$app -> put('/updateSessionStorageData', 'updateSessionStorageData');
$app -> get('/getSessionStorageData/:sessionId','getSessionStorageData');
$app -> get('/getQuotePdf/:sessionId','getQuotePdf');
$app -> get('/getHomeFeatures/:sessionId','getHomeFeatures');
$app -> get('/getSystemData','getSystemData');
$app -> post('/beginIqsSession','beginIqsSession');
$app -> post('/logSessionTrack','logSessionTrack');
$app -> delete('/terminateIqsSession/:sessionId','terminateIqsSession');

//create our objects based on our environment
$configData;
$dbAccessor;
$dataLogger;
$sessionUtil;
$apiAccessor;
$debugData;

//items used to run after return to client
$returnedFlag = false;
$executeAfterFlag = false;
$globalSess;
$globalDataResponsePackageJson;

initialize();

$app->run();



function initialize(){
    global $configData, $dbAccessor, $dataLogger, $sessionUtil, $apiAccessor, $debugData;

    $configData = new Iqs\Dao\ConfigurationDataAccessor();
    $dbAccessor = Iqs\Util\DbAccessorFactory::getDbAccessorObject($configData);
    $sessionUtil = new Iqs\Util\SessionUtil($configData, $dbAccessor);
    $dataLogger = new Iqs\Util\DataLogger($configData,$dbAccessor);
    $debugData = new Iqs\Model\DebugData();
    $apiAccessor = new Iqs\Dao\EzQuoteApiAccessor($sessionUtil, $configData, $debugData);
}

///////////////////////////////////////////////////////////////
/////////////////  API functions  ////////////////////////////
/////////////////////////////////////////////////////////////

/**
 * beginIqsSession:
 * REST POST Call - Begins a new session
 * @return Session ID
 */
function beginIqsSession(){

    try{
        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingProductDataArray = json_decode($request->getBody(), true)[FieldKeys::$PRODUCT_DATA_ARRAY];

        \Iqs\Util\IncomingDataValidator::validateIncomingProductDataArray($incomingProductDataArray);

        //create instances of the objects that we'll need
        global $sessionUtil;
        global $dataLogger;
        global $configData;

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        //data has been validated so we can assume it is safe, let's create a new GIQ session
        $activeSession = $sessionUtil->getNewSession($incomingProductDataArray);

        //verify that this state is enabled
        $curState = $activeSession->getState();
        if($configData->getConfigValue(FieldKeys::$IQS_CONF_SECTION_STATES, $curState)!=FieldKeys::$IQS_BOOL_TRUE){
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_PROD_DATA_UNSUPPORTED_STATE);
        }


        //save the session
        $sessionUtil->saveNewSession($activeSession);

        //log the new session in the GeneratedSessions table
        $dataLogger->logSessionTouch($activeSession);

        //determine if dp3 is enabled for this state
        $dp3enabled = $configData->isDpEnabled($activeSession);

        $prodConfig = $configData->getConfigData(FieldKeys::$IQS_CONF_SECTION_ENVIRONMENT);

        //add the session id to the data payload to send to the client (identify it with the payload key)
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PARAMETER_SESSION_ID,$activeSession->getSessionId());
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_CONFIGURATION_DP,$dp3enabled);
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_CONF_ELEMENT_ENV_DEBUG,$prodConfig[FieldKeys::$IQS_CONF_ELEMENT_ENV_DEBUG]);
        //Send the address array and the session ID to the client
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(\Exception $ex){
        catchSystemException($ex);
    }
    return;

}



/**
 * terminateIqsSession:
 * REST DELETE Call - Terminates a session - removes it from the ActiveSessions table
 * @param String: SessionId
 * @return empty response on success
 */
function terminateIqsSession($incomingSessionId){
    try{
        global $sessionUtil;

        $sessionUtil->removeSession($incomingSessionId);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_EMPTY, "");
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * logSessionTrack:
 * REST POST Call - Track a session's front end actions (page touches)
 * @param String: SessionId
 * @return empty response on success
 */
function logSessionTrack(){
    try{

        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$IQS_PARAMETER_SESSION_ID];
        $lstDataArray = json_decode($request->getBody(), true)[FieldKeys::$LOG_SESSION_TRACK_DATA_ARRAY];


        global $sessionUtil;
        global $dataLogger;

        $activeSession = $sessionUtil->restoreSession($incomingSessionId);
        $pageId = "";
        if(isset($lstDataArray[FieldKeys::$LOG_SESSION_TRACK_PAGEID])){
            $pageId = $lstDataArray[FieldKeys::$LOG_SESSION_TRACK_PAGEID];
        }

        $activeSession->setPageId($pageId);

        //save the session
        $sessionUtil->updateSession($activeSession);

        //update the GeneratedSession record
        $dataLogger->logSessionTouch($activeSession);
        //insert new tracking record
        $dataLogger->logSessionTrack($activeSession);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_EMPTY, "");
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(\Exception $ex){
        catchSystemException($ex);
    }
    return;
}


/**validateAddress:
 * REST POST Call - Accepts an address in an array, sends it to EZQ, returns the validated address, each with an ID number, if EZQ returns any.
 * @param String: SessionId - The current session ID
 * @param ValidateAddressDataArray: Array of key/value pairs street address data to be validated: (all strings) InsuredFirstName, InsuredLastName, PropertyStreetNumber, PropertyStreetName, PropertyAddressLine2, PropertyCity, PropertyState, PropertyZipCode
 * @return Array of Addresses containing the EZQ validated address(es), each with an ID number
 */
function validateAddress(){

    try{
        global $apiAccessor;

        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$IQS_PARAMETER_SESSION_ID];
        $incomingValidateAddressDataArray = json_decode($request->getBody(), true)[FieldKeys::$VALIDATE_ADDRESS_DATA_ARRAY];

        //if something is invalid we'll throw an IqsException exception (make sure to catch and log it)
        \Iqs\Util\IncomingDataValidator::validateIncomingAddressDataArray($incomingValidateAddressDataArray);


        //create instances of the objects that we'll need
        global $sessionUtil;
        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        //if the sessionId is good, restore the session, we already have a product array so no need to update.
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);


        //since the data is valid, put it into a quote object
        $preValidatedAddress = new \Iqs\Model\IqsQuoteAddress($incomingValidateAddressDataArray);

        //send the address data to the ezquote api
        $quoteAddressArray = $apiAccessor->validateAddress($preValidatedAddress, $activeSession);

        //store the address array with the session
        $activeSession->setQuoteAddressArray($quoteAddressArray);

        //add the quote address array to the data payload to send to the client (identify it with the payload key)
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_ADDRESS_ARRAY,$activeSession->getQuoteAddressArray());

        $sessionUtil->updateSession($activeSession);

        //Send the address array and the session ID to the client
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(\Exception $ex){
       catchSystemException($ex);
    }
    return;
}


/**
 * beginQuoteSession:
 * REST POST Call - Begins a quoting session with EZQuote
 * @param String: SessionId
 * @param BeginQuoteDataArray: - Array of key/value pairs necessary to begin a quote: AddressId (Integer: id number of the selected address item - from validateAddress response)
 * @return empty response on success
 */
function beginQuoteSession(){
    try{
        global $sessionUtil;
        global $configData;
        global $dataLogger;
        global $apiAccessor;

        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = getSessionId();
        $incomingBeginQuoteDataArray = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$BEGIN_QUOTE_SESSION_DATA_ARRAY];
        $selectedAddressIndex = null;

        //validate the incoming data
        \Iqs\Util\IncomingDataValidator::validateIncomingBeginQuoteSessionDataArray($incomingBeginQuoteDataArray);

        $selectedAddressIndex = $incomingBeginQuoteDataArray[FieldKeys::$BEGIN_QUOTE_SESSION_ADDRESS_ID];

        //create the objects we'll need
        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //check for invalid product here
        $insuranceProduct = strtolower($incomingBeginQuoteDataArray[FieldKeys::$BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT]);
        $productEnabled = ($configData->isProductEnabled($activeSession, $insuranceProduct) === 'true');


        if(!$productEnabled){
            //throw error - not allowed product coming through
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_PROD_DATA_INSURANCE_PROD);
        }



        //select the address in the session
        $activeSession->selectQuoteAddress($selectedAddressIndex);

        //check whitelist to verify product is available to this zip code
        $state = strtolower($activeSession->getState());
        $zipCode = $activeSession->getZipCode();
        $supportedZip = $configData->isSupportedZip($zipCode,$state,$insuranceProduct);

        if(!$supportedZip){
            //throw error - unserviced zip code for product coming through
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_PROD_DATA_UNSUPPORTED_ZIPCODE);
        }

        //add the data to the product data in the session
        $activeSession->updateQuoteFields($incomingBeginQuoteDataArray);


        //if we don't have a quote open, create a new one.
        if($activeSession->getQuoteId() == ""){
            //call EZQuote and start a quoting session with the data.
            $ezQuoteIdResponse = $apiAccessor->createNewQuote($activeSession);

            //save the quote id in the session object
            $activeSession->setQuoteId($ezQuoteIdResponse);
        }

        //save the session
        $sessionUtil->updateSession($activeSession);

        //update the GeneratedQuotes table
        $dataLogger->logQuoteTouch($activeSession);

        //send back the quoteId in case the front end needs to display a block or some other data
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_QUOTE_ID, $activeSession->getQuoteId());

        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}


/**
 * updateQuoteSession:
 * REST PUT Call - Updates a quoting session with EZQuote.  May also be used, in its "Long" form with RunUpdateInBackground as false to update
 * a quote and return a new premium so long as generateSavedQuote (ezrce) has been run against the quote already.
 * @param String: SessionId
 * @param Boolean: RunUpdateInBackground - determines if the call to EZQ is run after the response to the client or before.
 * @param UpdateQuoteDataArray - Array of key/value pair strings used to update a quote.  The keys are TermNames as provided by SageSure
 * @return If RunUpdateInBackground is true the response is always an empty response unless there is an error, otherwise the response is the quote data from EZQ.
 */
function updateQuoteSession(){
    try{
        global $sessionUtil;
        global $apiAccessor;

        global $dataLogger;
        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = getSessionId();
        $incomingRunUpdateInBackground = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_RUN_UPDATE_IN_BACKGROUND];
        $incomingUpdateQuoteDataArray = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_DATA_ARRAY];
        \Iqs\Util\IncomingDataValidator::validateIncomingUpdateQuoteSessionDataArray($incomingUpdateQuoteDataArray);

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //add to or update the existing quote fields
        $activeSession->updateQuoteFields($incomingUpdateQuoteDataArray);

        //$globalSess = $activeSession;
        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_EMPTY, "");

        //if the client wants to execute the update in the background we have to do things differently to return to the user and continue to process
        //1 - package it all up in global vars
        //2 - set the executeAfter flag
        //put everything into global vars and finish the call.  After Slim finishes, the open code at the bottom of the page will execute
        if($incomingRunUpdateInBackground){

            //save the changes to the db
            $sessionUtil->updateSession($activeSession);

            //update the GeneratedQuotes table
            $dataLogger->logQuoteTouch($activeSession);

            //Save the quote data to the LastSavedQuote field
            $sessionUtil->saveLastSavedQuote($activeSession);

            global $executeAfterFlag;
            global $globalSess;
            global $globalDataResponsePackageJson;

            $executeAfterFlag = true;
            $globalSess = $activeSession;
            $globalDataResponsePackageJson = $dataResponsePackage->getJsonDataResponsePackage();
        }else{//otherwise we execute normally

            //call EZQuote and start a quoting session with the data.
            $ezQuoteResponse = $apiAccessor->updateQuote($activeSession);

            //get the CalculatedCoverageA value and stick it in the EZQ response
            $tmpCalculatedCovaArray = array(Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA);
            $lastCalculatedCovaValueArray = $activeSession->getQuoteFields($tmpCalculatedCovaArray);

            //put the CalculatedCoverageA data into the incoming data so that it is added to the quote as CoverageA (and as RCE)
            if(!empty($lastCalculatedCovaValueArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA])){
                $ezQuoteResponse[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA] = $lastCalculatedCovaValueArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA];
            }else{
                $ezQuoteResponse[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA] = "";
            }

            $activeSession->updateQuoteFields($ezQuoteResponse);

            //save the changes to the db
            $sessionUtil->updateSession($activeSession);

            //Save the quote data to the LastSavedQuote field
            $sessionUtil->saveLastSavedQuote($activeSession);


            $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

            $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_QUOTE, $activeSession->getQuoteFields());
            sendOutput($dataResponsePackage->getJsonDataResponsePackage());
        }

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * recallQuote:
 * REST GET Call - Gets quote data from an existing session
 * @param String: quoteId
 * @param String: zipCode
 * @return String: SessionId
 * @return Array: Quote - key value pairs containing quote data
 */
function recallQuote($quoteId, $zipCode){
    try{
        global $sessionUtil;
        global $dataLogger;
        global $apiAccessor;

        //rebuild the old session
        $activeSession = $sessionUtil->regenerateExpiredSession($quoteId);

        if($activeSession===false){
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_RECALL_QUOTE_NOT_FOUND_IN_IQS);
        }

        //check with EZQ to get the quote data
        $returnedQuoteData = $apiAccessor->recallQuote($activeSession);

        //validate that we have a zip code that matches the quote
        if(array_key_exists(FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE, $returnedQuoteData)){
            if($returnedQuoteData[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE] != $zipCode){
                throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_RECALL_QUOTE_ZIP_NO_MATCH);
            }
        }else{
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_EZQUOTE_UNEXPECTED_RESPONSE);
        }

        //quote is a match, add the CalculatedCoverageA data if it is there and populate it with the quote data from ezquote
        //restore the last saved quote to get the ezrce Calculated CoverageA data
        $lastSavedQuote = $sessionUtil->restoreLastSavedQuote($activeSession->getQuoteId());

        //will hold the last known Page ID
        $lastQuoteSectionPageId = "";
        $sectionIdArray = "";
        //get the last known CalculatedCoverageA value
        if(!empty($lastSavedQuote)){


            //try to get the last known quoting page Id and return it
            $tmpLastQuoteSectionPageIdArray = array(Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_LAST_QUOTE_SECTION_PAGE_ID);
            $lastQuoteSectionPageIdArray = $lastSavedQuote->getQuoteFields($tmpLastQuoteSectionPageIdArray);
            if(!empty($lastQuoteSectionPageIdArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_LAST_QUOTE_SECTION_PAGE_ID])){
                $lastQuoteSectionPageId = $lastQuoteSectionPageIdArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_LAST_QUOTE_SECTION_PAGE_ID];
                $returnedQuoteData[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_LAST_QUOTE_SECTION_PAGE_ID] = $lastQuoteSectionPageId;
            }

            //try to get the list of all pages that this session ever touched
            $sectionIdArray = $sessionUtil->getSessionSectionIdArray($activeSession->getSessionId());

            //try to restore the insurance product ID (HO3 / DP3 / Etc)
            $tmpLastInsuranceProduct = array(Iqs\Cnst\FieldKeys::$BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT);
            $lastQuoteInsuranceProductArray = $lastSavedQuote->getQuoteFields($tmpLastInsuranceProduct);
            if(!empty($lastQuoteInsuranceProductArray[Iqs\Cnst\FieldKeys::$BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT])){
                $returnedQuoteData[Iqs\Cnst\FieldKeys::$BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT] = $lastQuoteInsuranceProductArray[Iqs\Cnst\FieldKeys::$BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT];
            }

            $tmpCalculatedCovaArray = array(Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA);
            $lastCalculatedCovaValueArray = $lastSavedQuote->getQuoteFields($tmpCalculatedCovaArray);
            //put the CalculatedCoverageA data into the incoming data so that it is added to the quote as CoverageA (and as RCE) if it is there.
            if(!empty($lastCalculatedCovaValueArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA])){
                $returnedQuoteData[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA] = $lastCalculatedCovaValueArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA];
            }
        }else{
            $returnedQuoteData[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA] = "";
        }

        $activeSession->updateQuoteFields($returnedQuoteData);


        //make sure we aren't regenerating an active session (nuke any active session w/ this quoteID)
        $sessionUtil->removeSession($activeSession->getSessionId());
        $sessionUtil->saveNewSession($activeSession);

        //log the new session in the GeneratedSessions table
        $dataLogger->logSessionTouch($activeSession);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        //add the session id and the quote data to the return payload
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PARAMETER_SESSION_ID,$activeSession->getSessionId());
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_QUOTE, $returnedQuoteData);
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_LAST_QUOTE_SECTION_PAGE_ID, $lastQuoteSectionPageId);
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_TOUCHED_SECTION_LIST, $sectionIdArray);

        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * getSelectedQuoteData:
 * REST GET Call - Gets quote data from an existing session
 * @param String: sessionId
 * @param Array: SelectedQuoteDataArray - array of term names for requested quote data
 * @return Array: SelectedQuoteData - key value pairs containing requested quote data
 */
function getSelectedQuoteData($incomingSessionId,$incomingEncodedGetSelectedQuoteDataArray){
    try{
        global $sessionUtil;
        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        $incomingGetSelectedStorageDataArray=json_decode(urldecode($incomingEncodedGetSelectedQuoteDataArray),true);

        if(empty($incomingSessionId) || ($incomingSessionId=="null")){
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
        }

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //search for and send back any saved fields we find
        $returnSlectedQuoteDataArray = $activeSession->getQuoteFields($incomingGetSelectedStorageDataArray);
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_SELECTED_QUOTE_DATA_ARRAY, $returnSlectedQuoteDataArray);
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}


/**
 * logQuoteSessionBlock:
 * REST POST Call - Generates a block record in the IQS database.
 * @param String: sessionId
 * @param Array: BlockQuoteDataArray - BlockQuoteCode (String: code number / id of the reason for block)
 * @return empty response on success
 */
function logQuoteSessionBlock(){
    try{
        global $sessionUtil;
        global $dataLogger;
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = getSessionId();
        $incomingLogQuoteBlockDataArray = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$LOG_QUOTE_SESSION_BLOCK_DATA_ARRAY];

        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        $quoteId = $activeSession->getQuoteId();
        $blockCode = $incomingLogQuoteBlockDataArray[Iqs\Cnst\FieldKeys::$LOG_QUOTE_SESSION_BLOCK_CODE];
        $dataLogger->logQuoteBlock($quoteId,$blockCode);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_EMPTY, "");
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * updateSessionStorageData:
 * REST PUT Call - Saves GIQ data with the session
 * @param String: sessionId
 * @param String: UpdateSessionStorageData - Typically a JSON object
 * @return empty response on success
 */
function updateSessionStorageData(){
    try{
        global $sessionUtil;

        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = getSessionId();
        $incomingUpdateSessionStorageData = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$UPDATE_SESSION_STORAGE_DATA];

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //add to or update the existing quote fields
        $activeSession->updateStorageField($incomingUpdateSessionStorageData);

        //save the changes to the db
        $sessionUtil->updateSession($activeSession);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_EMPTY, "");
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * getSessionStorageData:
 * REST GET Call - Returns GIQ data with the session
 * @param String: sessionId
 * @return String: SessionStorageData
 */
function getSessionStorageData($incomingSessionId){
    try{
        global $sessionUtil;
        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        if(strlen($incomingSessionId)<1){
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
        }

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //search for and send back any saved fields we find
        $returnSlectedStorageData = $activeSession->getStorageField();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_STORAGE_DATA, $returnSlectedStorageData);
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * getQuotePdf:
 * REST GET Call - Returns a PDF formatted quote
 * @param String: sessionId
 * @return PDF data stream
 */
function getQuotePdf($incomingSessionId){
    try{
        global $sessionUtil;
        global $app;
        if(strlen($incomingSessionId)<1){
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
        }
        //we get the pointer to the pdf template for this state here
        $req = \Slim\Slim::getInstance()->request();
        $pathToPdfTemplate = $req->get('pdfpath');

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //put the call to the PDF Generator here
        $pdf = $sessionUtil->generateQuotePdf($activeSession->getQuoteFields(), $activeSession->getQuoteId(), $pathToPdfTemplate);

        //set the response headers
        //$app->response->headers->set('Content-Disposition', 'attachment; filename="quote.pdf"'); //only if we want to treat as attachment
        $app->response->headers->set("Content-Type", "application/pdf"); //$f->type

        $app->response->setBody($pdf);

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * generateSavedQuote:
 * REST POST Call - Sends collected quote to EZQ ezrce to calculate the Replacement Cost Estimate (rce) and provide a complete quote.
 * @param String: sessionId
 * @param Array: UpdateQuoteDataArray - Array of key/value pair strings used to update a quote.  The keys are TermNames as provided by SageSure
 * @return String: Quote - a completed quote
 */
function generateSavedQuote(){
    try{
        global $sessionUtil;
        global $dataLogger;
        global $apiAccessor;

        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = getSessionId();
        $incomingUpdateQuoteDataArray = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_DATA_ARRAY];
        \Iqs\Util\IncomingDataValidator::validateIncomingUpdateQuoteSessionDataArray($incomingUpdateQuoteDataArray);

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //set CoverageA value to blank so that RCE is triggered
        $incomingUpdateQuoteDataArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_A] = "";

        //add to or update the existing quote fields
        $activeSession->updateQuoteFields($incomingUpdateQuoteDataArray);

        //call EZQuote and send up the quote data.
        $ezQuoteResponse = $apiAccessor->generateSavedQuote($activeSession);

        //copy the response fields to the session if they are valid (do they contain a premium)
        if(array_key_exists(FieldKeys::$EZ_QUOTE_QUOTE_RESPONSE_PREMIUM,$ezQuoteResponse)){

            //if CoverageA is returned from ezrce, copy its value into a new field called CalculatedCoverageA
            $ezQuoteResponse[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA] = "";
            if(!empty($ezQuoteResponse[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_A])){
                $ezQuoteResponse[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA] = $ezQuoteResponse[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_A];
                $activeSession->updateQuoteFields($ezQuoteResponse);
                //save the quote to the GeneratedQuotes table as well so we can keep an quote with RCE data on hand
                $sessionUtil->saveLastSavedQuote($activeSession);
            }else{
                $activeSession->updateQuoteFields($ezQuoteResponse);
            }
        }else{
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_QUOTE_NO_PREMIUM_RETURNED);
        }

        //save the changes to the db
        $sessionUtil->updateSession($activeSession);

        //This should be returning a complete quote.  Log the fact that we've just done a completed quote.
        $dataLogger->logQuoteComplete($activeSession);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_QUOTE, $activeSession->getQuoteFields()); //test (was $ezQuoteResponse)
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());


    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}

/**
 * generateTempQuote:
 * REST POST Call - Sends collected quote to EZQ ezrerate to calculate a temporary, unsaved quote with premium.  getSavedQuote must be called against the quote first.
 * @param String: sessionId
 * @param Array: UpdateQuoteDataArray - Array of key/value pair strings used to update a quote.  The keys are TermNames as provided by SageSure
 * @return String: Quote - a completed quote
 */
function generateTempQuote(){
    try{
        global $sessionUtil;
        global $apiAccessor;

        //get the POST data from slim and decode it as arrays (not objects)
        $request = \Slim\Slim::getInstance()->request();
        $incomingSessionId = getSessionId();
        $incomingUpdateQuoteDataArray = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_DATA_ARRAY];
        \Iqs\Util\IncomingDataValidator::validateIncomingUpdateQuoteSessionDataArray($incomingUpdateQuoteDataArray);

        //restore the session
        $activeSession = $sessionUtil->restoreSession($incomingSessionId);

        //copy the ezrce calculated CoverageA data into the CoverageA field: this call should never be called before generateSavedQuote or else there will be no saved RCE value
        $tmpCalculatedCovaArray = array(Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA);
        $calculatedCovaValueArray = $activeSession->getQuoteFields($tmpCalculatedCovaArray);

        //put the CalculatedCoverageA data into the incoming data so that it is added to the quote as CoverageA (and as RCE)
        if(!empty($calculatedCovaValueArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA])){
            $incomingUpdateQuoteDataArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_A] = $calculatedCovaValueArray[Iqs\Cnst\FieldKeys::$UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA];
        }else{
            throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_QUOTE_LAST_SAVED_QUOTE_CALCUATED_COVERAGEA_NOT_FOUND);
        }

        //add to or update the existing quote fields
        $activeSession->updateQuoteFields($incomingUpdateQuoteDataArray);

        //call EZQuote and send up the quote data.
        $ezQuoteResponse = $apiAccessor->generateTempQuote($activeSession);

        $activeSession->updateQuoteFields($ezQuoteResponse);

        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_QUOTE, $activeSession->getQuoteFields());
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());

    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}


/**
 * getHomeFeatures:
 * REST POST Call - Sends collected a subset of data relevant to additional home features to EZQ to get default additional home features for the product
 * @param String: sessionId
 * @return String: JSON Data with home feature default data for the specified product
 */
function getHomeFeatures($incomingSessionId){
    try{
        global $sessionUtil;
        global $apiAccessor;

        $activeSession = $sessionUtil->restoreSession($incomingSessionId);
        //get the default home features from EZQ

        $ezqResponse = $apiAccessor->getHomeFeatures($activeSession);

        $dataResponsePackage = new DataResponsePackage();

        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_DEFAULT_HOME_FEATURES, $ezqResponse);

        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}


/**
 * getSystemData:
 * REST GET Call - Returns information about the IQS and EZQ system
 * @return String: System - JSON data about the system including version number, date, time
 */
function getSystemData(){
   	try{

        global $apiAccessor;
        global $configData;

        $ezqResponse = $apiAccessor->getEzQuoteVersion();
        $ezqVersion = "Unable To Access EZQuote";
        if(isset($ezqResponse["version"])){
            $ezqVersion = $ezqResponse["version"];
        }
        $iqsver = $configData->getIqsVersion();
		
		$systemData[FieldKeys::$IQS_SYSTEM_DATA_DATE] = gmdate('Y-m-d');
		$systemData[FieldKeys::$IQS_SYSTEM_DATA_TIME] = gmdate('H:i:s');
		$systemData[FieldKeys::$IQS_SYSTEM_DATA_VERSION] = $iqsver;
        $systemData[FieldKeys::$IQS_SYSTEM_DATA_EZQVERSION] = $ezqVersion;
		
        $dataResponsePackage = new \Iqs\Model\DataResponsePackage();

        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_SYSTEM, $systemData);

        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }catch (\Iqs\Exception\IqsException $iqsex){
        catchIqsException($iqsex);
    }catch(Exception $ex){
        catchSystemException($ex);
    }
    return;
}




///////////////////////////////////////////////////////////////////
//////////////  utility functions  ///////////////////////////////
/////////////////////////////////////////////////////////////////


function catchIqsException(\Iqs\Exception\IqsException $ex){
    global $returnedFlag;
    //return IqsException exception
    $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
    $iqsException = $ex->getIqsException();
    $dataResponsePackage->setException($iqsException);
    $dataResponsePackage = getDebugMessages($dataResponsePackage, $ex);
    //create objects we need
    global $dataLogger;

    $dataLogger->logException($ex);

    //send to client
    if(!$returnedFlag){
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }

}

function catchSystemException($ex){
    global $returnedFlag;
    //return system exception (a bit more work than IqsExceptions)
    $dataResponsePackage = new \Iqs\Model\DataResponsePackage();
    $clientException = new \Iqs\Model\ClientExceptionPackage(\Iqs\Cnst\InformationCode::$SYS_ERROR);

    //$clientException->setExceptionMessage($ex->getMessage());
    $dataResponsePackage->setException($clientException);
    $dataResponsePackage = getDebugMessages($dataResponsePackage, $ex);
    //create objects we need
    global $dataLogger;

    //log exception
    $dataLogger->logException($ex);

    //send to client
    if(!$returnedFlag){
        sendOutput($dataResponsePackage->getJsonDataResponsePackage());
    }
}

//we use send output so that if we wish to continue processing after output we can (EZQuote API can be slow on unimportant returns).
function sendOutput($output){
    global $returnedFlag;

    echo $output;

    $returnedFlag = true;
}

function getSessionId(){
    $request = \Slim\Slim::getInstance()->request();
    $incomingSessionId = json_decode($request->getBody(), true)[Iqs\Cnst\FieldKeys::$IQS_PARAMETER_SESSION_ID];
    if(empty($incomingSessionId) || ($incomingSessionId=="null")){
        throw new \Iqs\Exception\IqsException(\Iqs\Cnst\InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
    }
    return $incomingSessionId;
}

function getDebugMessages($dataResponsePackage, $exception){
    global $configData;
    global $debugData;

    //if we are staging, drop in any ezq messages or errors into the return payload
    $prodConfig = $configData->getConfigData(FieldKeys::$IQS_CONF_SECTION_ENVIRONMENT);
    if($prodConfig[FieldKeys::$IQS_CONF_ELEMENT_ENV_DEBUG]==FieldKeys::$IQS_BOOL_TRUE){
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_API_DEBUG_MESSAGE, $debugData->getApiMessage());
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_API_DEBUG_ERROR, $debugData->getApiError());
        $dataResponsePackage->addResponseDataPayloadItem(FieldKeys::$IQS_PAYLOAD_ITEM_EXCEPTION_DEBUG_MESSAGE, $exception->getMessage());
    }

    return $dataResponsePackage;
}


//call EZQuote and update the quote.  executed after the system has responded to the front end.
if($executeAfterFlag){
    global $apiAccessor;
    ob_end_clean();
    header("Connection: close\r\n");
    header("Content-Encoding: none\r\n");
    ignore_user_abort(true); // optional
    ob_start();
    echo ($globalDataResponsePackageJson);
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();     // Strange behaviour, will not work
    flush();            // Unless both are called !
    ob_end_clean();


    $returnedFlag = true;

//do processing here
    //sleep(5);

    $ezQuoteIdResponse = $apiAccessor->updateQuote($globalSess);
}

