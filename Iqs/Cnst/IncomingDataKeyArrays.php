<?php
/**
 * IncomingDataKeys
 *
 * groups of keys expected
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   0.3.0
 */

namespace Iqs\Cnst;


class IncomingDataKeyArrays
{

    public static function getIncomingValidateAddressDataArrayKeys(){
        $incomingAddressArray = array();
        $incomingAddressArray[]=FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER;
        $incomingAddressArray[]=FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NAME;
        $incomingAddressArray[]=FieldKeys::$VALIDATE_ADDRESS_PROPERTY_CITY;
        $incomingAddressArray[]=FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STATE;
        $incomingAddressArray[]=FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE;
        return $incomingAddressArray;
    }

    public static function getIncomingProductDataArrayKeys(){
        $incomingProductDataArray = array();
        $incomingProductDataArray[]=FieldKeys::$PRODUCT_FEID;
        return $incomingProductDataArray;
    }

    public static function getIncomingBeginQuoteDataArrayKeys(){
        $incomingBeginQuoteDataArray = array();
        $incomingBeginQuoteDataArray[]=FieldKeys::$BEGIN_QUOTE_SESSION_ADDRESS_ID;
        //(SDR - Removed at randy's request flodock 7/13/2015 for v1.3.3)$incomingBeginQuoteDataArray[]=FieldKeys::$BEGIN_QUOTE_SESSION_PRODUCT_ID;
        $incomingBeginQuoteDataArray[]=FieldKeys::$BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT;
        $incomingBeginQuoteDataArray[]=FieldKeys::$BEGIN_QUOTE_SESSION_BEGIN_QUOTE_SESSION_INSURED_BY_CORPORATION;
        return $incomingBeginQuoteDataArray;
    }

    public static function getIncomingLogQuoteBlockDataArrayKeys(){
        $incomingLogQuoteBlockDataArray = array();
        $incomingLogQuoteBlockDataArray[] = FieldKeys::$LOG_QUOTE_SESSION_BLOCK_CODE;
        return $incomingLogQuoteBlockDataArray;
    }

    public static function getEncryptionItemArrayKeys(){
        $encryptionItemsArray = array();
        $encryptionItemsArray[] = FieldKeys::$IQS_ENCRYPTION_ITEM_INSURED_1_SSN;
        $encryptionItemsArray[] = FieldKeys::$IQS_ENCRYPTION_ITEM_INSURED_1_SSN_DISPLAY;
        return $encryptionItemsArray;
    }

    public static function getInvalidQuoteDataKeys(){
        $invalidQuoteDataKeys = array();
        $invalidQuoteDataKeys[] = FieldKeys::$IQS_INVALID_QUOTE_DATA_KEY_MESSAGE;
        $invalidQuoteDataKeys[] = FieldKeys::$IQS_INVALID_QUOTE_DATA_KEY_ERRORS;
        return $invalidQuoteDataKeys;
    }

}