<?php
/**
 * ClientExceptionPackage
 *
 * contains sanitized exception data to be sent to the client
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Model;

class ClientExceptionPackage {

    public $exceptionCode = "";
    public $exceptionMessage = "";

    public function __construct($informationCodeArray)
    {
        $this->exceptionCode = $informationCodeArray['code'];
        $this->exceptionMessage = $informationCodeArray['message'];
    }

    public function setWithInformationCode($informationCodeArray)
    {
        $this->exceptionCode = $informationCodeArray['code'];
        $this->exceptionMessage = $informationCodeArray['message'];
    }

    public function setExceptionCode($newExceptionCode){
        $this->exceptionCode = $newExceptionCode;
    }

    public function setExceptionMessage($newExceptionMessage){
        $this->exceptionMessage = $newExceptionMessage;
    }

} 