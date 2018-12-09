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
    use Iqs\Cnst\FieldKeys;
    use Iqs\Dao\EzQuoteApiAccessor;


//TODO: sanitize all data!
//TODO: do something with the success responses
    class MsSqlLinuxApacheDatabaseAccessor extends ADatabaseAccessor
    {
        const longreadlen = 30000;  //this seems to allow roughly 500 term names

        protected function connectToDatabase() {
            $dbh = odbc_connect("Driver={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBDRIVER]};Server={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBHOST]};Database={$this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBNAME]};", $this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBUSER], $this->dbConfigData[FieldKeys::$IQS_CONF_ELEMENT_DATABASE_DBPASS]);
            return $dbh;
        }

        public function saveLogEntry(LogEntry $logEntry){

            $logEntrySource = $logEntry->getLogDataSource();
            $logEntryMessage = $logEntry->getLogDataMessage();

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "INSERT INTO [Log] ([LogEntrySource], [LogEntryMessage], [LogEntryDateTime]) VALUES (?,?, GETDATE())");
            $result = odbc_execute($sql,array($logEntrySource,$logEntryMessage));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close ($dbcon);

        }

        public function saveNewSession(Session $activeSession){

            //save it as an active session first
            $sessionId = $activeSession->getSessionId();
            $serializedSession = base64_encode(serialize($activeSession));

            $dbcon = $this->connectToDatabase();
            //$sql = odbc_prepare($dbcon, "INSERT INTO [ActiveSessions] ([SessionGuid], [LastAccessDate], [SessionData]) VALUES (?,GETDATE(),?)");
            $sql = odbc_prepare($dbcon, "INSERT INTO [ActiveSessions] ([SessionGuid], [LastAccessDate], [SessionData]) VALUES (?,GETDATE(),?)");
            $result = odbc_execute($sql,array($sessionId,$serializedSession));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }

            odbc_close ($dbcon);
        }

        public function restoreSession($sessionId){

            $dbcon = $this->connectToDatabase();
            //$sql = odbc_prepare($dbcon, "SELECT [SessionData] FROM [ActiveSessions] WHERE [SessionGuid] = ?");
            $sql = odbc_prepare($dbcon, "SELECT cast([SessionData] as text) as SessionData FROM [ActiveSessions] WHERE [SessionGuid] = ?");
            $result = odbc_execute($sql,array($sessionId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
            }else{

                odbc_binmode ($sql, ODBC_BINMODE_RETURN);
                odbc_longreadlen ($sql, self::longreadlen);
                //odbc_longreadlen ($sql, 0);

                odbc_fetch_row($sql,0);

                $serializedSession = odbc_result($sql,"SessionData");

                $unserializedSession = unserialize(base64_decode($serializedSession));
                if(!$unserializedSession){
                    throw new IqsException(InformationCode::$SYS_SESSION_NO_SESSION_FOUND);
                }

                odbc_free_result($sql);
                odbc_close($dbcon);

                return $unserializedSession;
            }

        }

        public function deleteExpiredSession(){

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "DELETE FROM [ActiveSessions] WHERE [LastAccessDate] < DATEADD(hh,-12,GETDATE())");
            $result = odbc_execute($sql);
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close($dbcon);

        }


        public function removeSession($sessionId)
        {

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "DELETE FROM [ActiveSessions] WHERE [SessionGuid]= ?");
            $result = odbc_execute($sql,array($sessionId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close($dbcon);

        }

        public function updateSession(Session $activeSession)
        {

            $sessionId = $activeSession->getSessionId();
            $serializedSession = base64_encode(serialize($activeSession));
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "UPDATE [ActiveSessions] SET [LastAccessDate]=GETDATE(), [SessionData]=? WHERE [SessionGuid]=?");
            $result = odbc_execute($sql,array($serializedSession ,$sessionId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close ($dbcon);

        }

        public function saveLastSavedQuote(Session $activeSession){
            $quoteId = $activeSession->getQuoteId();
            $serializedSession = base64_encode(serialize($activeSession));
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "UPDATE [GeneratedQuotes] SET [LastSavedQuote]=? WHERE [QuoteId]=?");
            $result = odbc_execute($sql,array($serializedSession, $quoteId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close ($dbcon);
        }

        public function restoreLastSavedQuote($quoteId){
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "SELECT CAST([LastSavedQuote] AS text) AS LastSavedQuote FROM [GeneratedQuotes] WHERE [QuoteId] = ?");
            $result = odbc_execute($sql,array($quoteId));
            $unserializedSession = null;
            if(!$result){
                odbc_close($dbcon);
            }else{


                odbc_binmode ($sql, ODBC_BINMODE_RETURN);
                //odbc_longreadlen ($sql, 0);
                odbc_longreadlen ($sql, self::longreadlen);

                odbc_fetch_row($sql,0);
                $serializedSession = odbc_result($sql,"LastSavedQuote");
                $unserializedSession = unserialize(base64_decode($serializedSession));
                if(!$unserializedSession){
                    $unserializedSession = null;
                }


                odbc_free_result($sql);
                odbc_close($dbcon);

                return $unserializedSession;
            }
        }

        public function logQuoteBlock($quoteId, $blockCode)
        {

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "INSERT INTO [BlockedQuotes] ([QuoteId], [BlockCode]) VALUES (?,?)");
            $result = odbc_execute($sql,array($quoteId,$blockCode));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close ($dbcon);

        }

        public function logQuoteTouch(Session $activeSession){

            $quoteId = $activeSession->getQuoteId();
            $quoteFeid = $activeSession->getFeid();
            $sessionId = $activeSession->getSessionId();

            //if we don't have a quote ID yet, don't do anything
            if(!empty($quoteId)){
                //insert if it isn't there, update if it is
                $dbcon = $this->connectToDatabase();
                $itemCount = 0;


                $sql = odbc_prepare($dbcon, "SELECT count(*) as itemCount FROM [GeneratedQuotes] WHERE [QuoteId] = ?");
                $result = odbc_execute($sql,array($quoteId));
                if(!$result){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }else{
                    while(odbc_fetch_row($sql)) {
                        $itemCount=odbc_result($sql,'itemCount');
                    }
                }



                if($itemCount==0){//insert
                    $sql = odbc_prepare($dbcon, "INSERT INTO [GeneratedQuotes] ([QuoteId],[CreateDate], [LastAccessDate], [AccessCount], [Feid], [SessionGuid]) VALUES (?, GETDATE(), GETDATE(), 1, ?, ?)");
                    $result = odbc_execute($sql,array($quoteId,$quoteFeid, $sessionId));
                    if(!$result){
                        odbc_close($dbcon);
                        throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                    }
                }else{//update

                    $sql = odbc_prepare($dbcon, "UPDATE [GeneratedQuotes] SET [LastAccessDate]=GETDATE(), [AccessCount]=[AccessCount]+1, [SessionGuid]=? WHERE [QuoteId]=?");

                    $result = odbc_execute($sql,array($sessionId,$quoteId));
                    if(!$result){
                        odbc_close($dbcon);
                        throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                    }
                }

                odbc_close($dbcon);
            }

        }

        public function logSessionTouch(Session $activeSession){
            $quoteFeid = $activeSession->getFeid();
            $sessionId = $activeSession->getSessionId();
            $altData = $activeSession->getAltData();
            $lastPageId = $activeSession->getPageId();

            //insert if it isn't there, update if it is
            $dbcon = $this->connectToDatabase();
            $itemCount = 0;


            $sql = odbc_prepare($dbcon, "SELECT count(*) as itemCount FROM [GeneratedSessions] WHERE [SessionGuid] = ?");
            $result = odbc_execute($sql,array($sessionId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{
                while(odbc_fetch_row($sql)) {
                    $itemCount=odbc_result($sql,'itemCount');
                }
            }

            if($itemCount==0){//insert
                $sql = odbc_prepare($dbcon, "INSERT INTO [GeneratedSessions] ([SessionGuid], [CreateDate], [LastAccessDate], [AccessCount], [Feid], [LastPageId], [AltData]) VALUES (?, GETDATE(), GETDATE(), 1, ?, ?, ?)");
                $result = odbc_execute($sql,array($sessionId,$quoteFeid, $lastPageId, $altData));
                if(!$result){
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }
            }else{//update

                $sql = odbc_prepare($dbcon, "UPDATE [GeneratedSessions] SET [LastAccessDate]=GETDATE(), [AccessCount]=[AccessCount]+1, [LastPageId] = ? WHERE [SessionGuid]=?");

                $result = odbc_execute($sql,array($lastPageId, $sessionId));
                if(!$result){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }
            }

            odbc_close($dbcon);
        }

        public function logSessionTrack(Session $activeSession){

            $sessionId = $activeSession->getSessionId();
            $lastPageId = $activeSession->getPageId();

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "INSERT INTO [SessionTracking] ([SessionGuid],[PageId],[DateTime]) VALUES (?,?,GETDATE())");
            $result = odbc_execute($sql,array($sessionId,$lastPageId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close($dbcon);
        }

        public function logQuoteComplete(Session $activeSession){

            $quoteId = $activeSession->getQuoteId();

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "UPDATE [GeneratedQuotes] SET [LastAccessDate]=GETDATE(),[CompleteDate]=GETDATE(), [AccessCount]=[AccessCount]+1 WHERE [QuoteId]=?");
            $result = odbc_execute($sql,array($quoteId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close ($dbcon);

        }



        public function updateDynConf($confId, $confValue){

            //insert if it isn't there, update if it is
            $dbcon = $this->connectToDatabase();
            $itemCount = 0;


            $sql = odbc_prepare($dbcon, "SELECT count(*) as itemCount FROM [DynConf] WHERE [ConfId] = ?");
            $result = odbc_execute($sql,array($confId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{
                while(odbc_fetch_row($sql)) {
                    $itemCount=odbc_result($sql,'itemCount');
                }
            }

            if($itemCount==0){//insert
                $sql = odbc_prepare($dbcon, "INSERT INTO [DynConf] ([ConfId],[ConfValue]) VALUES (?,?)");
                $result = odbc_execute($sql,array($confId,$confValue));
                if(!$result){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }
            }else{//update
                $sql = odbc_prepare($dbcon, "UPDATE [DynConf] SET [ConfValue]=? WHERE [ConfId]=?");
                $result = odbc_execute($sql,array($confValue,$confId));
                if(!$result){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }
            }

            odbc_close($dbcon);
        }

        public function getDynConfValue($confId){
            $confValue = "";
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "SELECT [ConfValue] FROM [DynConf] WHERE [ConfId]=?");
            $result = odbc_execute($sql,array($confId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{
                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }
                if(count($rows)==0){
                    $confValue="empty";
                }else{
                    $confValue = $rows[0]["ConfValue"];
                }
            }

            odbc_free_result($sql);
            odbc_close($dbcon);
            return $confValue;

        }

        public function getDynConfValues(){
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "SELECT * FROM [DynConf]");
            $result = odbc_execute($sql);
            $rows = array();
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }
            }

            odbc_free_result($sql);
            odbc_close($dbcon);
            return $rows;

        }

        public function getIqsQuoteRecord($quoteId){

            $dbcon = $this->connectToDatabase();
            $itemCount = 0;

            $quoteRecord=false;
            $sql = odbc_prepare($dbcon, "SELECT [QuoteId],[CreateDate],[LastAccessDate],[CompleteDate],[AccessCount],[Feid],[SessionGuid] FROM [GeneratedQuotes] WHERE [QuoteId] = ?");
            $result = odbc_execute($sql,array($quoteId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }
                if(count($rows)==1){
                    $quoteRecord=$rows[0];
                }
            }
            odbc_close($dbcon);
            return $quoteRecord;
        }


        public function getIqsSessionRecord($sessionId){

            $dbcon = $this->connectToDatabase();
            $itemCount = 0;

            $quoteRecord=false;
            $sql = odbc_prepare($dbcon, "SELECT * FROM [GeneratedSessions] WHERE [SessionGuid] = ?");
            $result = odbc_execute($sql,array($sessionId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }
                if(count($rows)==1){
                    $quoteRecord=$rows[0];
                }
            }
            odbc_close($dbcon);
            return $quoteRecord;
        }

        public function getIqsSessionTrackingData($sessionId){
            $dbcon = $this->connectToDatabase();
            $itemCount = 0;

            $rows = false;
            $sql = odbc_prepare($dbcon, "SELECT * FROM [SessionTracking] WHERE [SessionGuid] = ?");
            $result = odbc_execute($sql,array($sessionId));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }

            }
            odbc_close($dbcon);
            return $rows;
        }

        public function deleteDynConfValue($confId)
        {
            parent::deleteDynConfValue($confId); // TODO: Change the autogenerated stub
        }

        public function getZipWhiteListValues(array $whiteListFilter = [])
        {
            $dbcon = $this->connectToDatabase();
            $rows = false;
            if(empty($whiteListFilter)){
                $sql = odbc_prepare($dbcon, "SELECT * FROM [ZipCodeWhiteList]");
                $result = odbc_execute($sql);
            }else{
                $filterString = "";
                foreach( $whiteListFilter as $filter => $value ) {
                    if(empty($filterString)){
                        $filterString.= " [".$filter."] = ?";
                    }else{
                        $filterString.=" AND [".$filter."] = ?";
                    }
                }

                $filterString = "SELECT * FROM [ZipCodeWhiteList] WHERE".$filterString;
                $sql = odbc_prepare($dbcon, $filterString);
                $result = odbc_execute($sql,array_values($whiteListFilter));
            }

            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }

            }
            odbc_close($dbcon);
            return $rows;
        }

        public function addZipWhiteListValues(array $whiteListValues)
        {
            $dbcon = $this->connectToDatabase();

            foreach( $whiteListValues as $whiteListItem ) {

                $sqlCheck = odbc_prepare($dbcon,"SELECT TOP 1 * FROM [ZipCodeWhiteList] WHERE [ZipCode] = ? AND [State]=? AND [Product]=?;");
                $whiteListItemCheck = array_slice($whiteListItem, 0, 3);
                $resultCheck = odbc_execute($sqlCheck,$whiteListItemCheck);
                if(!$resultCheck){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }else{
                    $rows = array();
                    while($myRow = odbc_fetch_array($sqlCheck)){
                        $rows[] = $myRow;
                    }
                    if(count($rows)==0){
                        $sql = odbc_prepare($dbcon, "INSERT INTO [ZipCodeWhiteList] ([ZipCode], [State], [Product], [DateTime]) VALUES (?,?,?,GETDATE())");
                        $result = odbc_execute($sql, $whiteListItem);
                        if (!$result) {
                            odbc_close($dbcon);
                            throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                        }
                    }
                }

            }

            odbc_close($dbcon);
        }

        public function updateZipWhiteListValues(array $whiteListValues)
        {
            $dbcon = $this->connectToDatabase();

            foreach( $whiteListValues as $whiteListItem ) {

                $sqlCheck = odbc_prepare($dbcon,"SELECT TOP 1 * FROM [ZipCodeWhiteList] WHERE [ZipCode] = ? AND [State]=? AND [Product]=?;");
                $whiteListItemCheck = array_slice($whiteListItem, 0, 3);
                $resultCheck = odbc_execute($sqlCheck,$whiteListItemCheck);
                if(!$resultCheck){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }else{
                    $rows = array();
                    while($myRow = odbc_fetch_array($sqlCheck)){
                        $rows[] = $myRow;
                    }
                    if(count($rows)==0){
                        $sql = odbc_prepare($dbcon, "UPDATE [ZipCodeWhiteList] SET [ZipCode]=?, [State]=?, [DateTime]=GETDATE(), [Product]=? WHERE [WhiteListId]=?");
                        $result = odbc_execute($sql, $whiteListItem);
                        if (!$result) {
                            odbc_close($dbcon);
                            throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                        }
                    }
                }

            }
            odbc_close($dbcon);
        }


        public function deleteZipWhiteListValues(array $whiteListId)
        {

            $dbcon = $this->connectToDatabase();
            foreach ($whiteListId as $id) {
                $sql = odbc_prepare($dbcon, "DELETE FROM [ZipCodeWhiteList] WHERE [WhiteListId]=?");
                $result = odbc_execute($sql, array($id));
                if (!$result) {
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }
            }
            odbc_close($dbcon);
        }

        public function getBlockCodesValues(array $blockCodesFilter = [])
        {
            $dbcon = $this->connectToDatabase();
            $rows = false;
            if(empty($blockCodesFilter)){
                $sql = odbc_prepare($dbcon, "SELECT * FROM [BlockCodes]");
                $result = odbc_execute($sql);
            }else{
                $filterString = "";
                foreach( $blockCodesFilter as $filter => $value ) {
                    if(empty($filterString)){
                        $filterString.= " [".$filter."] = ?";
                    }else{
                        $filterString.=" AND [".$filter."] = ?";
                    }
                }

                $filterString = "SELECT * FROM [BlockCodes] WHERE".$filterString;
                $sql = odbc_prepare($dbcon, $filterString);
                $result = odbc_execute($sql,array_values($blockCodesFilter));
            }

            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }

            }
            odbc_close($dbcon);
            return $rows;
        }

        public function addBlockCodesValues(array $blockCodesValues)
        {
            $dbcon = $this->connectToDatabase();


            foreach( $blockCodesValues as $BlockCodeItem ) {

                $sqlCheck = odbc_prepare($dbcon,"SELECT TOP 1 * FROM [BlockCodes] WHERE [BlockCode] = ? AND [BlockText]=?;");

                $resultCheck = odbc_execute($sqlCheck,$BlockCodeItem);
                if(!$resultCheck){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }else{
                    $rows = array();
                    while($myRow = odbc_fetch_array($sqlCheck)){
                        $rows[] = $myRow;
                    }
                    if(count($rows)==0){
                        $sql = odbc_prepare($dbcon, "INSERT INTO [BlockCodes] ([BlockCode], [BlockText], [DateTime]) VALUES (?,?,GETDATE())");
                        $result = odbc_execute($sql, $BlockCodeItem);
                        if (!$result) {
                            odbc_close($dbcon);
                            throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                        }
                    }
                }

            }
            odbc_close($dbcon);
        }

        /**
         * updateBlockCodesValues:
         * Updates an existing block code entry.
         * @param array: blockCodesValues is an array of arrays. each sub array holds 3 elements [new block code, new block text, original block code]
         * @throws IQSException: Unable to Access Database if the DB fails to respond properly
         */
        public function updateBlockCodesValues(array $blockCodesValues)
        {
            $dbcon = $this->connectToDatabase();

            foreach( $blockCodesValues as $blockCodeItem ) {
                //first, verify that the update doesn't create a duplicate code number
                $sqlCheck = odbc_prepare($dbcon,"SELECT TOP 1 * FROM [BlockCodes] WHERE [BlockCode] = ?");

                $resultCheck = odbc_execute($sqlCheck,array($blockCodeItem[0]));
                if(!$resultCheck){
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }else{
                    $rows = array();
                    while($myRow = odbc_fetch_array($sqlCheck)){
                        $rows[] = $myRow;
                    }
                    if((count($rows)==0) || ($blockCodeItem[0]==$blockCodeItem[2])){
                        //if nobody has that code number or if it matches the current code num then we can update the item
                        $sql = odbc_prepare($dbcon, "UPDATE [BlockCodes] SET [BlockCode]=?, [BlockText]=? WHERE [BlockCode]=?");
                        $result = odbc_execute($sql, $blockCodeItem);
                        if (!$result) {
                            odbc_close($dbcon);
                            throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                        }
                    }
                }

            }
            odbc_close($dbcon);
        }

        public function deleteBlockCodesValues(array $blockCodeId)
        {
            $dbcon = $this->connectToDatabase();
            foreach ($blockCodeId as $id) {
                $sql = odbc_prepare($dbcon, "DELETE FROM [BlockCodes] WHERE [BlockCode]=?");
                $result = odbc_execute($sql, array($id));
                if (!$result) {
                    odbc_close($dbcon);
                    throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
                }
            }
            odbc_close($dbcon);
        }

        public function getConfSectionValues($confSection = "")
        {
            $sqlString = "";
            $dbcon = $this->connectToDatabase();

            if($confSection == ""){
                $sqlString = "SELECT * FROM [".FieldKeys::$IQS_DATABASE_CONF_TABLE."]";
                $sql = odbc_prepare($dbcon, $sqlString);
                $result = odbc_execute($sql);
            }else{
                $this->validateConfCategory($confSection);
                $sqlString = "SELECT * FROM [".FieldKeys::$IQS_DATABASE_CONF_TABLE."] WHERE [".FieldKeys::$IQS_DATABASE_CONF_SECTION."]=? ";
                $sql = odbc_prepare($dbcon, $sqlString);
                $result = odbc_execute($sql,array($confSection));
            }


            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{

                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }

            }
            odbc_close($dbcon);
            return $rows;
        }

        public function getConfElementValue($confSection, $confElement)
        {
            $this->validateConfCategory($confSection);

            $confValue = "";
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "SELECT [".FieldKeys::$IQS_DATABASE_CONF_VALUE."]
                FROM [".FieldKeys::$IQS_DATABASE_CONF_TABLE."]
                WHERE [".FieldKeys::$IQS_DATABASE_CONF_SECTION."]=?
                AND [".FieldKeys::$IQS_DATABASE_CONF_ELEMENT."]=?");

            $result = odbc_execute($sql,array($confSection,$confElement));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }else{
                $rows = array();
                while($myRow = odbc_fetch_array($sql)){
                    $rows[] = $myRow;
                }
                if(count($rows)==0){
                    $confValue="";
                }else{
                    $confValue = $rows[0][FieldKeys::$IQS_DATABASE_CONF_VALUE];
                }
            }

            odbc_free_result($sql);
            odbc_close($dbcon);
            return $confValue;
        }

        public function addConfElementValue($confSection, $confElement, $confValue)
        {
            $this->validateConfCategory($confSection);

            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "INSERT INTO [".FieldKeys::$IQS_DATABASE_CONF_TABLE."] 
                ([".FieldKeys::$IQS_DATABASE_CONF_SECTION."], 
                [".FieldKeys::$IQS_DATABASE_CONF_ELEMENT."],
                [".FieldKeys::$IQS_DATABASE_CONF_VALUE."]) 
                VALUES (?,?,?)");
            $result = odbc_execute($sql,array($confSection,$confElement,$confValue));
            if(!$result){
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close ($dbcon);
        }

        public function updateConfElementValue($confSection, $confElement, $confValue)
        {
            $this->validateConfCategory($confSection);

            $dbcon = $this->connectToDatabase();

            $sql = odbc_prepare($dbcon, "UPDATE [".FieldKeys::$IQS_DATABASE_CONF_TABLE."] 
                SET [".FieldKeys::$IQS_DATABASE_CONF_VALUE."]=? 
                WHERE [".FieldKeys::$IQS_DATABASE_CONF_SECTION."] = ? 
                AND [".FieldKeys::$IQS_DATABASE_CONF_ELEMENT."]=?");
            $result = odbc_execute($sql, array($confValue,$confSection,$confElement));
            if (!$result) {
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }

            odbc_close ($dbcon);
        }

        public function deleteConfElementValue($confSection, $confElement)
        {
            $this->validateConfCategory($confSection);
            $dbcon = $this->connectToDatabase();
            $sql = odbc_prepare($dbcon, "DELETE FROM [".FieldKeys::$IQS_DATABASE_CONF_TABLE."] 
                WHERE [".FieldKeys::$IQS_DATABASE_CONF_SECTION."]=? 
                AND [".FieldKeys::$IQS_DATABASE_CONF_ELEMENT."]=?");
            $result = odbc_execute($sql, array($confSection,$confElement));
            if (!$result) {
                odbc_close($dbcon);
                throw new IqsException(InformationCode::$SYS_UNABLE_TO_ACCESS_DATABASE);
            }
            odbc_close($dbcon);
        }

        private function validateConfCategory($confSection){
            $sections = FieldKeys::getValidIqsConfSections();
            if(!in_array($confSection,$sections)){
                throw new IqsException(InformationCode::$SYS_CONFIG_INVALID_SECTION);
            }
        }

    }
}