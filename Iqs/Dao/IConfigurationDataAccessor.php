<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 3/30/14
 * Time: 6:21 PM
 */
namespace Iqs\Dao;

use Iqs\Model\Session;

interface IConfigurationDataAccessor
{
    public function getConfigData($configSection);

    public function getConfigValue($configSection, $configKey);

    public function isDpEnabled(Session $activeSession);

    public function isProductEnabled(Session $activeSession, $insuranceProduct);

    public function isSupportedZip($zipCode, $state, $product);
}