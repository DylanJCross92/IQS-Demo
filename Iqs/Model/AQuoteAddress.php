<?php
/**
 * QuoteAddress
 *
 * The address to be validated for the quote
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Model;

use Iqs\Cnst\FieldKeys;
use Iqs\Cnst\FieldNameKey;

abstract class AQuoteAddress implements IQuoteAddress
{
    protected $addressId = "";
    public $PropertyStreetNumber = "";
    public $PropertyStreetName = "";
    public $PropertyAddressLine2 = "";
    public $PropertyCity = "";
    public $PropertyState = "";
    public $PropertyZipCode = "";
    protected $infoFields = array();

    public function __construct(array $decodedJsonAddress){

        $this->populateAddress($decodedJsonAddress);

    }

    public function populateAddress(array $decodedJsonAddress){

        $this->PropertyStreetNumber = $decodedJsonAddress[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER];
        $this->PropertyStreetName = $decodedJsonAddress[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NAME];
        $this->PropertyCity = $decodedJsonAddress[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_CITY];
        $this->PropertyState = $decodedJsonAddress[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STATE];
        $this->PropertyZipCode = $decodedJsonAddress[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE];

    }

    /**
     * @return string
     */
    public function getPropertyAddressLine2()
    {
        return $this->PropertyAddressLine2;
    }

    /**
     * @param string $PropertyAddressLine2
     */
    public function setPropertyAddressLine2($PropertyAddressLine2)
    {
        $this->PropertyAddressLine2 = $PropertyAddressLine2;
    }

    /**
     * @return string
     */
    public function getPropertyStreetNumber()
    {
        return $this->PropertyStreetNumber;
    }

    /**
     * @param string $addressOne
     */
    public function setPropertyStreetNumber($addressOne)
    {
        $this->PropertyStreetNumber = $addressOne;
    }

    /**
     * @return string
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return string
     */
    public function getPropertyCity()
    {
        return $this->PropertyCity;
    }

    /**
     * @param string $city
     */
    public function setPropertyCity($city)
    {
        $this->PropertyCity = $city;
    }

    /**
     * @param string $PropertyStreetName
     */
    public function setPropertyStreetName($PropertyStreetName)
    {
        $this->PropertyStreetName = $PropertyStreetName;
    }

    /**
     * @return string
     */
    public function getPropertyStreetName()
    {
        return $this->PropertyStreetName;
    }



    /**
     * @return string
     */
    public function getPropertyState()
    {
        return $this->PropertyState;
    }

    /**
     * @param string $state
     */
    public function setPropertyState($state)
    {
        $this->PropertyState = $state;
    }

    /**
     * @return array
     */
    public function getInfoFields()
    {
        return $this->infoFields;
    }

    /**
     * @return string
     */
    public function getPropertyZipCode()
    {
        return $this->PropertyZipCode;
    }

    /**
     * @param string $zip
     */
    public function setPropertyZipCode($zip)
    {
        $this->PropertyZipCode = $zip;
    }

    public function getPropertyAddressArray()
    {
        $addressArray = array();
        $addressArray[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER] = $this->getPropertyStreetNumber();
        $addressArray[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NAME] = $this->getPropertyStreetName();
        $addressArray[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_CITY] = $this->getPropertyCity();
        $addressArray[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STATE] = $this->getPropertyState();
        $addressArray[FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE] = $this->getPropertyZipCode();
        return $addressArray;
    }


    public function updateInfoFields(array $newInfoFields){
        $this->infoFields = array_merge($this->infoFields, $newInfoFields);
    }

    public function getAddressAsJson(){
        //protected & private fields are not encoded (intentional)
        return json_encode($this);
    }

} 