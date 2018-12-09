<?php
/**
 * DataLogger
 *
 * Provides static method to log data (to database or file)
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Util;


use Iqs\Dao\IConfigurationDataAccessor;
use Iqs\Dao\IDatabaseAccessor;
use Iqs\Model\LogEntry;
use Iqs\Model\Session;
use Iqs\Cnst\InformationCode;
use Iqs\Cnst\FieldKeys;
use Iqs\Exception\IqsException;

class DataLogger {

    private $configData;
    private $dbAccessor;

    public function __construct(IConfigurationDataAccessor $configData, IDatabaseAccessor $dbAccessor){
        $this->configData = $configData;
        $this->dbAccessor = $dbAccessor;
    }

    public function logError(LogEntry $logEntry){
        $loggingConfig = $this->configData->getConfigData('logging');

        if($loggingConfig[FieldKeys::$IQS_CONF_ELEMENT_LOGGING_DATABASELOGGING] == 'true'){
            self::logErrorToDatabase($logEntry);
        }

        if($loggingConfig[FieldKeys::$IQS_CONF_ELEMENT_LOGGING_FILELOGGING] == 'true'){
           $logFilePath = $loggingConfig[FieldKeys::$IQS_CONF_ELEMENT_LOGGING_LOGFILEPATH];
           self::logErrorToFile($logEntry, $logFilePath);
        }
    }

    public function logException(\Exception $exception){
        $logEntry = new LogEntry();
        $logEntry->setLogDataSource($exception->getTraceAsString());
        $logEntry->setLogDataMessage($exception->getMessage());
        self::logError($logEntry);
    }

    private function logErrorToDatabase(LogEntry $logEntry){
            $this->dbAccessor->saveLogEntry($logEntry);
    }

    private function logErrorToFile(LogEntry $logEntry, $logFilePath){
        $logString = date("Y-m-d H:i:s")."\nMessage: ".$logEntry->getLogDataMessage()."\nTrace:\n".$logEntry->getLogDataSource()."\n\n";
        try{
            file_put_contents($logFilePath, $logString, FILE_APPEND);
        }catch (\Exception $e){
            //our logging system has failed to write to file - nothing we can do
        }
    }

    public function logQuoteBlock($quoteId, $blockCode){
        $this->dbAccessor->logQuoteBlock($quoteId,$blockCode);
    }

    public function logQuoteTouch(Session $activeSession){
        $this->dbAccessor->logQuoteTouch($activeSession);
    }

    public function logSessionTouch(Session $activeSession){
        $this->dbAccessor->logSessionTouch($activeSession);
    }

    public function logSessionTrack(Session $activeSession){
        $this->dbAccessor->logSessionTrack($activeSession);
    }

    public function logQuoteComplete(Session $activeSession){
        $this->dbAccessor->logQuoteComplete($activeSession);
    }

} 