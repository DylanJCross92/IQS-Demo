<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 3/30/14
 * Time: 6:10 PM
 */
namespace Iqs\Dao;

use Iqs\Model\IQuoteAddress;
use Iqs\Model\Session;

interface IQuoteApiAccessor
{
    public function createNewQuote(Session $activeSession);

    public function updateQuote(Session $activeActiveSession);

    public function validateAddress(IQuoteAddress $address, Session $activeSession);
}