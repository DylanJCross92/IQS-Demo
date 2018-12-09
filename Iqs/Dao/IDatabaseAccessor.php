<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 3/30/14
 * Time: 6:09 PM
 */
namespace Iqs\Dao;

use Iqs\Model\LogEntry;
use Iqs\Model\Session;

interface IDatabaseAccessor
{
    public function saveLogEntry(LogEntry $logEntry);

    public function saveNewSession(Session $activeSession);

    public function restoreSession($sessionId);

    public function removeSession($sessionId);

    public function updateSession(Session $activeSession);

    public function logSessionTouch(Session $activeSession);

    public function logSessionTrack(Session $activeSession);

    public function logQuoteBlock($quoteId, $blockId);

    public function logQuoteTouch(Session $activeSession);

    public function logQuoteComplete(Session $activeSession);

    public function updateDynConf($confId, $confVal);

    public function getDynConfValue($confId);

    public function deleteDynConfValue($confId);

    public function deleteExpiredSession();

    public function getIqsQuoteRecord($quoteId);

    public function getIqsSessionRecord($sessionId);

    public function getIqsSessionTrackingData($sessionId);

    public function saveLastSavedQuote(Session $rceSession);

    public function restoreLastSavedQuote($quoteId);

    //*************************** White List Methods ****************************//

    public function getZipWhiteListValues(array $whiteListFilter);

    public function addZipWhiteListValues(array $whiteListValues);

    public function updateZipWhiteListValues(array $whiteListValues);

    public function deleteZipWhiteListValues(array $whiteListId);

    //*************************** Blockcodes Methods ****************************//

    public function getBlockCodesValues(array $blockCodesFilter);

    public function addBlockCodesValues(array $blockCodesFilter);

    public function updateBlockCodesValues(array $blockCodesValues);

    public function deleteBlockCodesValues(array $blockCodeId);

    //*************************** Conf Methods ****************************//

    public function getConfSectionValues($confSection);

    public function getConfElementValue($confSection, $confElement);

    public function addConfElementValue($confSection,$confElement, $confValue);

    public function updateConfElementValue($confSection,$confElement, $confValue);

    public function deleteConfElementValue($confSection,$confElement);


}