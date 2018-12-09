<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 10/14/2016
 * Time: 8:46 AM
 */

namespace Iqs\Model;


class DebugData
{
    public $apiMessage = "";
    public $apiError = "";

    public function __construct()
    {
    }

    public function getApiMessage(){
        return $this->apiMessage;
    }

    public function getApiError(){
        return $this->apiError;
    }

    public function setApiMessage($newMessage){
        $this->apiMessage = $newMessage;
    }

    public function setApiError($newError){
        $this->apiError = $newError;
    }
}