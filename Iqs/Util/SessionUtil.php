<?php
/**
 * SessionManager
 *
 * Handles sessions
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Util;

use Iqs\Dao\IConfigurationDataAccessor;
use Iqs\Dao\IDatabaseAccessor;
use Iqs\Model\ICredentials;
use Iqs\Model\Session;
use Iqs\Model\EzQuoteCredentials;
use Iqs\Cnst\FieldKeys;


class SessionUtil {

    private $databaseAccessor;
    private $configurationDataAccessor;
    private $dataLogger;

    public function  __construct(IConfigurationDataAccessor $configurationDataAccessor, IDatabaseAccessor $databaseAccessor){
        $this->databaseAccessor = $databaseAccessor;
        $this->configurationDataAccessor = $configurationDataAccessor;
        $this->dataLogger = new DataLogger($configurationDataAccessor, $databaseAccessor);
    }

    public function getNewSession(array $productData){
        $newSession = new Session($productData, $this->configurationDataAccessor);

        return $newSession;
    }

    public function saveNewSession(Session $activeSession){
        $this->databaseAccessor->saveNewSession($activeSession);
        $this->databaseAccessor->deleteExpiredSession();
    }

    /**
     * Regenerates an expired session
     * @param array $sessionRecord
     * @param array $productDataArray
     * @return regenerated session if available, false if invalid QuoteId or SessionId are invalid
     */
    public function regenerateExpiredSession($quoteId){

        //validate that quote ID entered is in our records
        $quoteRecord = $this->databaseAccessor->getIqsQuoteRecord($quoteId);

        if($quoteRecord===false){
            return false;
        }

        //extract the values from the quote record
        $quoteFeid = $quoteRecord[FieldKeys::$IQS_DATABASE_GENERATEDQUOTES_FEID];
        $quoteSessionId = $quoteRecord[FieldKeys::$IQS_DATABASE_GENERATEDQUOTES_SESSION_GUID];
        $quoteId = $quoteRecord[FieldKeys::$IQS_DATABASE_GENERATEDQUOTES_QUOTEID];

        $sessionRecord = $this->databaseAccessor->getIqsSessionRecord($quoteSessionId);

        if($sessionRecord===false){
            return false;
        }

        //put together a product data array from the FEID and the debug data
        $productDataArray = array();

        //scan the feid for a ".d" at the end to denote debug mode.
        $debugMode = false;

        if(substr($quoteFeid, -strlen(FieldKeys::$DEBUG_MODE_QUOTE_RECORD_FEID_IDENTIFIER)) === FieldKeys::$DEBUG_MODE_QUOTE_RECORD_FEID_IDENTIFIER){
            $debugMode=true;
        }

        //add the FEID to the product data array
        $productDataArray[FieldKeys::$PRODUCT_FEID] = $quoteFeid;

        //add the debug mode to the product data array
        if($debugMode===true){
            $productDataArray[FieldKeys::$PRODUCT_DEBUG] = "true";
        }

        $activeSession = new Session($productDataArray,$this->configurationDataAccessor);

        //update the fields in the session object
        $activeSession->setSessionId($quoteSessionId);
        $activeSession->setQuoteId($quoteId);
        $activeSession->setFeid($quoteFeid);

        return $activeSession;

    }

    /**
     * Restore an active from the IQS database
     * @param $sessionId
     * @return mixed
     */
    public function restoreSession($sessionId){
        $activeSession = $this->databaseAccessor->restoreSession($sessionId);
        return $activeSession;
    }

    public function updateSession(Session $activeSession){
        $this->databaseAccessor->updateSession($activeSession);
        $this->dataLogger->logSessionTouch($activeSession);
    }

    public function saveLastSavedQuote(Session $activeSession){
        $this->databaseAccessor->saveLastSavedQuote($activeSession);
    }

    public function restoreLastSavedQuote($quoteId){
        $lastSavedQuote = $this->databaseAccessor->restoreLastSavedQuote($quoteId);
        return $lastSavedQuote;
    }

    public function removeSession($sessionId){
        $this->databaseAccessor->removeSession($sessionId);
    }

    public function generateQuotePdf($quoteFields, $quoteId, $pathToPdfTemplate){
        $quoteGenerator = new QuotePdfGenerator($pathToPdfTemplate);
        return $quoteGenerator->generateQuotePdf($quoteFields, $quoteId);
    }

    public function getSessionCredentials(Session $activeSession){

        $credentialsData =  $this->configurationDataAccessor->getConfigData(FieldKeys::$IQS_CONF_SECTION_EZQUOTEAPI);

        $tokens = $this->databaseAccessor->getDynConfValues();
        foreach($tokens as $token) {
            $credentialsData[$token[FieldKeys::$IQS_DATABASE_DYNCONF_CONFID]]=$token[FieldKeys::$IQS_DATABASE_DYNCONF_CONFVALUE];
        }

        $credentials = new EzQuoteCredentials($credentialsData, $activeSession->getDebug()); //EzQuote specific class

        return $credentials;
    }

    public function updateSessionToken(ICredentials $credentials){

        $this->databaseAccessor->updateDynConf($credentials->getConfId(),$credentials->getToken());

    }

    public function getSessionSectionIdArray($sessionId){

        $sessionTrackingRecords= $this->databaseAccessor->getIqsSessionTrackingData($sessionId);

        $pageIds = array();

        foreach($sessionTrackingRecords as $record){

            $pageIds[] = $record["PageId"];
        }

        $uniquePageIds = array_unique($pageIds);

        return $uniquePageIds;

    }



} 