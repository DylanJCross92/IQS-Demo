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


    use Iqs\Cnst\InformationCode;
    use Iqs\Model\LogEntry;
    use Iqs\Model\Session;
    use Iqs\Exception\IqsException;


    abstract class ADatabaseAccessor implements IDatabaseAccessor
    {

        protected $dbConfigData;

        public function __construct(IConfigurationDataAccessor $configurationData)
        {
            $this->dbConfigData = $configurationData->getConfigData("database");
        }

        public function saveLogEntry(LogEntry $logEntry)
        {

            $logEntrySource = $logEntry->getLogDataSource();
            $logEntryMessage = $logEntry->getLogDataMessage();
            $sql = "INSERT INTO Log (LogEntrySource, LogEntryMessage, LogEntryDateTime) VALUES (:LogEntrySource,:LogEntryMessage, GETDATE())";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam("LogEntrySource", $logEntrySource);
            $sth->bindParam("LogEntryMessage", $logEntryMessage);
            $sth->execute();

        }

        public function saveNewSession(Session $activeSession)
        {
            $sessionId = $activeSession->getSessionId();
            $serializedSession = base64_encode(serialize($activeSession));
            $sql = "INSERT INTO ActiveSessions (SessionGuid, LastAccessDate, SessionData) VALUES (:SessionGuid,GETDATE(),:SessionData)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":SessionGuid", $sessionId);
            $sth->bindParam(":SessionData", $serializedSession);

            $sth->execute();

        }

        public function restoreSession($sessionId)
        {
            $sql = "SELECT SessionData FROM ActiveSessions WHERE SessionGuid= CONVERT(NVARCHAR,:sessionId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam("sessionId", $sessionId);
            $sth->execute();
            $result = $sth->fetchAll();
            if (count($result) > 0) {
                $serializedSession = $result[0]["SessionData"];
                $unserializedSession = unserialize(base64_decode($serializedSession));
                return $unserializedSession;
            } else {
                throw new IqsException(InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
            }
        }

        public function deleteExpiredSession()
        {
            $sql = "DELETE FROM ActiveSessions WHERE LastAccessDate < DATEADD(n,-60,GETDATE())";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->execute();
        }

        abstract protected function connectToDatabase();

        public function removeSession($sessionId)
        {
            $sql = "DELETE FROM ActiveSessions WHERE WHERE SessionGuid= CONVERT(NVARCHAR,:sessionId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam("sessionId", $sessionId);
            $sth->execute();
        }

        public function updateSession(Session $activeSession)
        {
            $sessionId = $activeSession->getSessionId();
            $serializedSession = base64_encode(serialize($activeSession));
            $sql = "UPDATE ActiveSessions SET LastAccessDate=GETDATE(), SessionData=:SessionData WHERE SessionGuid=CONVERT(NVARCHAR,:SessionGuid)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":SessionGuid", $sessionId);
            $sth->bindParam(":SessionData", $serializedSession);
            $sth->execute();
        }

        public function saveLastSavedQuote(Session $activeSession)
        {
            //TODO: provide PDO functionality for alternative drivers
        }

        public function restoreLastSavedQuote($quoteId)
        {
            //TODO: provide PDO functionality for alternative drivers
        }

        public function logSessionTouch(Session $activeSession)
        {
            //TODO: provide PDO functionality for alternative drivers
        }

        public function logSessionTrack(Session $activeSession)
        {
            //TODO: provide PDO functionality for alternative drivers
        }

        public function logQuoteBlock($quoteId, $blockCode)
        {
            $sql = "INSERT INTO BlockedQuotes (QuoteId, BlockCode) VALUES (:quoteId,:blockCode)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":quoteId", $quoteId);
            $sth->bindParam(":blockCode", $blockCode);
            $sth->execute();
        }

        public function logQuoteTouch(Session $activeSession)
        {
            $quoteId = $activeSession->getQuoteId();
            $quoteFeid = $activeSession->getFeid();
            //PDO and MSSQL don't like to play nice so we have to break this into two DB calls
            $sql = "SELECT count(*) FROM GeneratedQuotes WHERE QuoteId=CONVERT(NVARCHAR,:quoteId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":quoteId", $quoteId);
            $sth->execute();
            $rows = $sth->fetch(\PDO::FETCH_NUM);
            $rowcnt = $rows[0];

            if ($rowcnt > 0) {
                $sql = "UPDATE GeneratedQuotes SET LastAccessDate=GETDATE(), AccessCount=AccessCount+1 WHERE QuoteId=CONVERT(NVARCHAR,:quoteId)";
                $db = $this->connectToDatabase();
                $sth = $db->prepare($sql);
                $sth->bindParam(":quoteId", $quoteId);
                $sth->execute();
            } else {
                $sql = "INSERT INTO GeneratedQuotes (QuoteId, CreateDate, LastAccessDate, AccessCount, Feid) VALUES (:quoteId, GETDATE(), GETDATE(), 1, :feid)";
                $db = $this->connectToDatabase();
                $sth = $db->prepare($sql);
                $sth->bindParam(":quoteId", $quoteId);
                $sth->bindParam(":feid", $quoteFeid);
                $sth->execute();
            }
        }

        public function logQuoteComplete(Session $activeSession)
        {
            $quoteId = $activeSession->getQuoteId();
            $sql = "UPDATE GeneratedQuotes SET LastAccessDate=GETDATE(), CompleteDate=GETDATE(), AccessCount=AccessCount+1 WHERE QuoteId=CONVERT(NVARCHAR,:quoteId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":quoteId", $quoteId);
            $sth->execute();
        }


        public function updateDynConf($confId, $confValue)
        {
            //PDO and MSSQL don't like to play nice so we have to break this into two DB calls
            $sql = "SELECT count(*) FROM DynConf WHERE ConfId=CONVERT(NVARCHAR,:confId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":confId", $confId);
            $sth->execute();
            $rows = $sth->fetch(\PDO::FETCH_NUM);
            $rowcnt = $rows[0];

            if ($rowcnt > 0) {
                $sql = "UPDATE DynConf SET ConfValue=:confValue WHERE ConfId=CONVERT(NVARCHAR,:confId)";
                $db = $this->connectToDatabase();
                $sth = $db->prepare($sql);
                $sth->bindParam(":confId", $confId);
                $sth->bindParam(":confValue", $confValue);
                $sth->execute();
            } else {
                $sql = "INSERT INTO DynConf (ConfId, ConfValue) VALUES (:confId,:confValue)";
                $db = $this->connectToDatabase();
                $sth = $db->prepare($sql);
                $sth->bindParam(":confId", $confId);
                $sth->bindParam(":confValue", $confValue);
                $sth->execute();
            }
        }

        public function getDynConfValue($confId)
        {
            $sql = "SELECT ConfValue FROM DynConf WHERE ConfId=CONVERT(NVARCHAR, :confId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam("confId", $confId);
            $sth->execute();
            $result = $sth->fetchAll();
            if (count($result) > 0) {
                $confValue = $result[0]["ConfValue"];
            } else {
                $confValue = "";
            }

            return $confValue;
        }

        public function getIqsQuoteRecord($quoteId)
        {
            //see if the quote id exists in our table
            $sql = "SELECT count(*) FROM GeneratedQuotes WHERE QuoteId=CONVERT(NVARCHAR,:quoteId)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":quoteId", $quoteId);
            $sth->execute();
            $result = $sth->fetchAll();

            if (count($result) == 1) {
                return $result;
            }
            return false;
        }

        public function getIqsSessionRecord($sessionId)
        {
            //see if the session id exists in our table
            $sql = "SELECT count(*) FROM GeneratedSessions WHERE SessionGuid=CONVERT(NVARCHAR,:sessionGuid)";
            $db = $this->connectToDatabase();
            $sth = $db->prepare($sql);
            $sth->bindParam(":sessionGuid", $sessionId);
            $sth->execute();
            $result = $sth->fetchAll();

            if (count($result) == 1) {
                return $result;
            }
            return false;
        }

        public function getIqsSessionTrackingData($sessionId)
        {
            //TODO: provide PDO functionality for alternative drivers
        }

        public function deleteDynConfValue($confId)
        {
            //TODO: provide PDO functionality for alternative drivers
        }

        public function getZipWhiteListValues(array $whiteListFilter)
        {
            // TODO: Implement getZipWhiteListValues() method.
        }

        public function addZipWhiteListValues(array $whiteListValues)
        {
            // TODO: Implement addZipWhiteListValues() method.
        }

        public function updateZipWhiteListValues(array $whiteListValues)
        {
            // TODO: Implement updateZipWhiteListValues() method.
        }

        public function deleteZipWhiteListValues(array $whiteListId)
        {
            // TODO: Implement deleteZipWhiteListValues() method.
        }

        public function getBlockCodesValues(array $blockCodesFilter)
        {
            // TODO: Implement getBlockCodesValues() method.
        }

        public function addBlockCodesValues(array $blockCodesFilter)
        {
            // TODO: Implement getBlockCodesValues() method.
        }

        public function updateBlockCodesValues(array $blockCodesValues)
        {
            // TODO: Implement updateBlockCodesValues() method.
        }


        public function deleteBlockCodesValues(array $blockCodeId)
        {
            // TODO: Implement getBlockCodesValues() method.
        }

        //*************************** Conf Methods ****************************//
        public function getConfSectionValues($confSection)
        {
            // TODO: Implement getConfSectionValues() method.
        }


        public function getConfElementValue($confSection, $confElement)
        {

        }

        public function addConfElementValue($confSection, $confElement, $confValue)
        {

        }

        public function updateConfElementValue($confSection,$confElement, $confValue)
        {

        }

        public function deleteConfElementValue($confSection,$confElement)
        {

        }



    }


}