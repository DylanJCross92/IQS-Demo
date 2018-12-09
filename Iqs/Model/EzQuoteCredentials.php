<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 7/25/14
 * Time: 11:46 AM
 */

namespace Iqs\Model;

use Iqs\Cnst\FieldKeys;


class EzQuoteCredentials extends AQuoteCredentials {

    private $methodName = "";

    public function getUid($methodName = "")
    {
        if($methodName == ""){
            return parent::getUid();
        }else{
            if(array_key_exists(FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_UID.$methodName,$this->credentialsData)){
                $this->uid = $this->credentialsData[FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_UID.$methodName];
                $this->methodName = $methodName;
                return $this->uid;
            }
            else{
                return parent::getUid();
            }
        }
    }

    public function getPassword($methodName = "")
    {
        if($methodName == ""){
            return parent::getPassword();
        }else{
            if(array_key_exists(FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_PW.$methodName,$this->credentialsData)){
                $this->password = $this->credentialsData[FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_PW.$methodName];
                $this->methodName = $methodName;
                return $this->password;
            }else{
                return parent::getPassword();
            }
        }
    }

    public function getToken($methodName = "")
    {
        //check the creds to see if there is a UID and PW for this method. If so, generate a token and use it. If not, defer to parent
        if($methodName == ""){
            return parent::getToken();
        }else{
            if(array_key_exists(FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_UID.$methodName,$this->credentialsData) &&
                array_key_exists(FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_PW.$methodName,$this->credentialsData)){

                $methodUid = $this->credentialsData[FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_UID.$methodName];
                $methodPw = $this->credentialsData[FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_PW.$methodName];

                $methodToken = base64_encode($methodUid.":".$methodPw);

                $this->token = $methodToken;
                return $this->token;
            }else{
                return parent::getToken();
            }
        }
    }

} 