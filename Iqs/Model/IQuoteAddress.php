<?php
/**
 * Created by PhpStorm.
 * User: scottr
 * Date: 3/18/14
 * Time: 4:15 PM
 */
namespace Iqs\Model;

interface IQuoteAddress
{
    /**
     * @param string $addressOne
     */
    public function setPropertyStreetNumber($addressOne);

    /**
     * @return string
     */
    public function getPropertyStreetNumber();

    /**
     * @return string
     */
    public function getAddressId();

    /**
     * @param string $state
     */
    public function setPropertyState($state);

    /**
     * @param string $city
     */
    public function setPropertyCity($city);

    public function getAddressAsJson();

    /**
     * @param string $zip
     */
    public function setPropertyZipCode($zip);

    /**
     * @return string
     */
    public function getPropertyAddressLine2();

    /**
     * @return string
     */
    public function getPropertyState();

    /**
     * @return array
     */
    public function getInfoFields();

    public function setAddressId($addressId);

    /**
     * @param string $PropertyAddressLine2
     */
    public function setPropertyAddressLine2($PropertyAddressLine2);


    public function updateInfoFields(array $newSupplimentalFields);

    /**
     * @return string
     */
    public function getPropertyZipCode();

    /**
     * @return string
     */
    public function getPropertyCity();

    public function getPropertyAddressArray();

    public function populateAddress(array $decodedJsonAddress);

}