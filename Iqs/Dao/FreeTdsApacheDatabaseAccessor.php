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
    class FreeTdsApacheDatabaseAccessor extends ADatabaseAccessor
    {
        protected function connectToDatabase() {
            $dbh = new \PDO("odbc:Driver=FreeTDS; Port={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBPORT]}; SERVERNAME=mssql; Database={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBNAME]}; UID={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBUSER]}; PWD={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBPASS]}");

            //TODO: set the time zone in the config file
            $dbh->exec("SET time_zone='+00:00';");
            $dbh -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $dbh;
        }

    }
}