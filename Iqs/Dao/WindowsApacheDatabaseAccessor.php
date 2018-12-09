<?php
/**
 * DatabaseAccessor
 *
 * This class generates a JSON object containing the data to be returned to the client
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Dao{

    use Iqs\Cnst\FieldKeys;
    class WindowsApacheDatabaseAccessor extends ADatabaseAccessor
    {
        protected function connectToDatabase() {

            $dbh = new \PDO("sqlsrv:server={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBHOST]};database={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBNAME]};",$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBUSER], $this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBPASS]);

            //TODO: set the time zone in the config file
            $dbh->exec("SET time_zone='+00:00';");
            $dbh -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $dbh;
        }

    }
}