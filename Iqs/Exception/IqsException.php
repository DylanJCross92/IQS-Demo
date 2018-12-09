<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 3/19/14
 * Time: 9:39 AM
 */

namespace Iqs\Exception;

use Iqs\Model\ClientExceptionPackage;

class IqsException extends \Exception{

    private $informationCode = array();

    public function __construct(array $informationCodeArray)
    {
        $this->informationCode = $informationCodeArray;
        $this->message = "IQS Internal Exception: ".$informationCodeArray['message'];
    }

    public function getIqsException(){
        $exception = new ClientExceptionPackage($this->informationCode);
        return $exception;
    }

    public function setIqsExceptionMessage($message){
        $this->message = $message;
    }

} 