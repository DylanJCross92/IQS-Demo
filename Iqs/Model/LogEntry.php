<?php
/**
 * LogEntry
 *
 * contains data to be logged
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Model;


class LogEntry {

    private $logDataSource = "";
    private $logDataMessage = "";

    public function setLogDataSource($newDataSource){
        $this->logDataSource = substr($newDataSource,0,1000);
    }

    public function setLogDataMessage($newDataMessage){
        $this->logDataMessage = substr($newDataMessage,0,1000);
    }

    public function getLogDataSource(){
        return $this->logDataSource;
    }

    public function getLogDataMessage(){
        return $this->logDataMessage;
    }

} 