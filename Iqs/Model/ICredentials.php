<?php 

namespace Iqs\Model;

	use Iqs\Dao\IConfigurationDataAccessor;
	

interface ICredentials {

	public function getUid();
	
	public function getPassword();
	
	public function getAlc();
	
	public function getToken();

    public function setUid($newUid);
	
	public function setPassword($newPassword);
	
	public function setAlc($newAlc);

    public function setTemporaryToken($newToken);

    public function getConfId();
	
}



?>