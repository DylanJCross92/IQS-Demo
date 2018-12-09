<?php

namespace Iqs\Model;

use Iqs\Cnst\FieldKeys;

abstract class AQuoteCredentials implements ICredentials {

    protected $uid = "";
    protected $password = "";
    protected $alc = "";
    protected $token = "empty";
    protected $credentialsData;
    protected $debugMode = false;

	
	
	public function __construct(array $credentialsData, $debugMode)
	{
	    $this->credentialsData = $credentialsData;
        //set the EzQuote API default UID, PW & ALC for the creds. Get the token too.
        if($debugMode === true)
        {

            $this->debugMode = true;
            $uidKey = FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_DEBUG_APIUID;
            $pwKey = FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_DEBUG_APIPW;
            $alcKey = FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_DEBUG_APIALC;
            $tokenKey = FieldKeys::$IQS_DYNCONF_DEBUG_CONFID_AUTH;

        }else{

            $uidKey = FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_APIUID;
            $pwKey = FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_APIPW;
            $alcKey = FieldKeys::$IQS_CONF_ELEMENT_EZQUOTEAPI_APIALC;
            $tokenKey = FieldKeys::$IQS_DYNCONF_CONFID_AUTH;

        }

        if(array_key_exists($uidKey,$this->credentialsData)) {
            $this->uid = $credentialsData[$uidKey];
        }

        if(array_key_exists($pwKey,$this->credentialsData)) {
            $this->password = $credentialsData[$pwKey];
        }

        if(array_key_exists($alcKey,$this->credentialsData)) {
            $this->alc = $credentialsData[$alcKey];
        }

        if(array_key_exists($tokenKey,$this->credentialsData)) {
            $this->token = $credentialsData[$tokenKey];
        }
	}

	
	public function getUid()
	{
		return $this->uid;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	
	public function getAlc()
	{
		return $this->alc;
	}

    public function getToken(){
        return $this->token;
    }
	
	public function setUid($newUid)
	{
		$this->uid = $newUid;	
	}
	
	public function setPassword($newPassword)
	{
		$this->password = $newPassword;
	}
	
	public function setAlc($newAlc)
	{
		$this->alc = $newAlc;
	}

    public function setTemporaryToken($newToken){
        $this->token = $newToken;
    }

    public function getConfId(){
        if($this->debugMode === true){
            return FieldKeys::$IQS_DYNCONF_DEBUG_CONFID_AUTH;
        }else{
            return FieldKeys::$IQS_DYNCONF_CONFID_AUTH;
        }
    }

}


?>