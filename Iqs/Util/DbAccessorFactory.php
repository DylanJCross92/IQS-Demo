<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 7/28/2016
 * Time: 12:14 PM
 */

namespace Iqs\Util{

    use Iqs\Cnst\FieldKeys;
    use Iqs\Dao\WindowsApacheDatabaseAccessor;
    use Iqs\Dao\FreeTdsApacheDatabaseAccessor;
    use Iqs\Dao\MsSqlLinuxApacheDatabaseAccessor;
    use Iqs\Dao\ConfigurationDataAccessor;

    class DbAccessorFactory
    {
        static public function getDbAccessorObject(ConfigurationDataAccessor $configData) {

            $installConfigData = $configData->getConfigData("platform");

            //choose the correct database object
            if($installConfigData[FieldKeys::$IQS_CONF_ELEMENT_PLATFORM_INSTALLTYPE]=="0"){//Windows Apache (using MS PDO drivers)
                $dbAccessor = new WindowsApacheDatabaseAccessor($configData);
            }
            else if($installConfigData[FieldKeys::$IQS_CONF_ELEMENT_PLATFORM_INSTALLTYPE]=="1"){//Linux Apache (using FreeTDS drivers)
                $dbAccessor = new FreeTdsApacheDatabaseAccessor($configData);
            }
            else if($installConfigData[FieldKeys::$IQS_CONF_ELEMENT_PLATFORM_INSTALLTYPE]=="2"){//Linux Apache (using MS SQL ODBC 11 drivers)
                $dbAccessor = new MsSqlLinuxApacheDatabaseAccessor($configData);
            }
            else{
                $dbAccessor = new MsSqlLinuxApacheDatabaseAccessor($configData);
            }

            return $dbAccessor;
        }

    }
}