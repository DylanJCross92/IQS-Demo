<?php
/**
 * IncomingDataValidator
 *
 * Provides static methods to validate incoming data
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Util {

    use Iqs\Cnst\FieldKeys;
    use Iqs\Cnst\InformationCode;
    use Iqs\Cnst\IncomingDataKeyArrays;
    use Iqs\Exception\IqsException;

    class IncomingDataValidator
    {

        public static function validateIncomingAddressDataArray($untestedIncomingAddressDataArray)
        {
            //check for missing keys (we ignore extra keys)
            self::verifyAllKeysPresent(IncomingDataKeyArrays::getIncomingValidateAddressDataArrayKeys(), $untestedIncomingAddressDataArray);

            foreach($untestedIncomingAddressDataArray as $key => $value){
                self::validateIncomingAddressDataArrayWithSelect($key,$value);
            }

            return InformationCode::$SYS_NO_ERROR;
		}

        public static function validateIncomingProductDataArray($untestedIncomingProductDataArray)
        {

            //TODO: actually validate incoming product data - verify that feid and Insurance_Product are present and available

            //check for missing keys (we ignore extra keys)
            self::verifyAllKeysPresent(IncomingDataKeyArrays::getIncomingProductDataArrayKeys(), $untestedIncomingProductDataArray);

            return InformationCode::$SYS_NO_ERROR;
        }

        public static function validateIncomingBeginQuoteSessionDataArray($untestedIncomingBeginQuoteDataArray){
            //check for missing keys
            self::verifyAllKeysPresent(IncomingDataKeyArrays::getIncomingBeginQuoteDataArrayKeys(), $untestedIncomingBeginQuoteDataArray);
        }

        public static function validateIncomingUpdateQuoteSessionDataArray($untestedIncomingQuoteDataArray)
        {
	
			
            //TODO: actually validate incoming information.  Verify that every field in the array has data that is legit
            foreach($untestedIncomingQuoteDataArray as $key => $value){
                self::validateIncomingUpdateQuoteSessionDataWithSelect($key, $value);
            }

            return InformationCode::$SYS_NO_ERROR;
        }

        private static function verifyAllKeysPresent($internalKeyArray, $incomingArray)
        {
            $missingItemsArray = array_diff($internalKeyArray, array_keys($incomingArray));
            if (count($missingItemsArray) > 0) {
                throw new IqsException(InformationCode::$SYS_MISSING_KEYS);
            }
        }

		
        private static function maxLength($string, $maxLength)
		{
			$strLength = trim(strlen($string));
			$maxLength = $maxLength ? $maxLength : 1000;
			
			$return = $strLength<=$maxLength && $strLength>0;
			
			return $return;
		}
		
		private static function maxAmount($string, $maxAmount)
		{
			$return = trim($string) <= $maxAmount;
			
			return $return;	
		}
		
		private static function isText($string, $maxLength=false)
		{
			$string = trim($string);
			$return = preg_match("/^[a-zA-Z ]+$/", $string) && self::maxLength($string, $maxLength);
			
			return $return;	
		}
		
		private static function isNumeric($string, $maxLength=false)
		{
			$string = trim($string);

			$return = is_numeric($string) && self::maxLength($string, $maxLength);
			
			return $return;	
		}
		
		private static function isAlphaNumeric($string, $maxLength=false)
        {
			$string = trim($string);
			$return = preg_match("/^(?:(?:[a-zA-Z0-9.]+ *)+)$/", $string) && self::maxLength($string, $maxLength);
			
			return $return;
        }

        private static function isAlphaNumericAndHyphen($string, $maxLength=false)
        {
            $string = trim($string);
            $return = preg_match("/^(?:(?:[a-zA-Z0-9.-]+ *)+)$/", $string) && self::maxLength($string, $maxLength);

            return $return;
        }
		
		private static function isDate($string, $maxDate=false)
		{
			$string = trim($string);
			$result = true;
			
			if($maxDate)
			{
				$currentAge = strtotime($string);		
				$allowedAge = time() - 1577836800;//$maxDate * 365 * 24 * 60 * 60;
				$result = ($allowedAge - $currentAge) >= 0;			
			}
			
			$return = preg_match("/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/", $string) && self::maxLength($string, 100) && $result;
			
			return $return;	
		}
		
		public static function isYear($string)
		{
			$string = trim($string);
			$return = preg_match("/^(17|18|19|20)\d{2}$/", $string) && self::maxLength($string, 4);
			
			return $return;
		}
		
		public static function isOfAge($string)
		{
			$string = strtotime($string);
			$min = strtotime('+18 years', $string);
			
			if(time() < $min)  
			{
				return false; 
			}
			else
			{
				return true;	
			}
		}
		
		private static function isCurrency($string, $maxAmount=false)
		{
			$string = trim($string);
			$return = preg_match('/^\$?(?!0.00)(\d{1,3}(,\d{3})*|\d+)(\.\d\d)?$/', $string) && self::maxAmount($string, $maxAmount);
			
			return $return;	
		}
		
		private static function isEmail($string)
		{
			$string = trim($string);
			$return = filter_var($string, FILTER_VALIDATE_EMAIL);
			
			return $return;	
		}
		
		private static function isSSN($string)
		{
			$string = trim($string);
			$return = preg_match("/^\d{3}-?\d{2}-?\d{4}$/", $string);
			
			return $return;
		}
		
		private static function validateIncomingAddressDataArrayWithSelect($untestedAddressDataKey, $untestedAddressDataValue){
            switch ($untestedAddressDataKey) {

                case FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER:
					
					if(!self::isAlphaNumeric($untestedAddressDataValue))
					{
						throw new IqsException(InformationCode::$VAL_STREETNUMBER_ERROR);
					}
	

                break;

                case FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STREET_NAME:

                    if(!self::isAlphaNumeric($untestedAddressDataValue))
                    {
                        throw new IqsException(InformationCode::$VAL_STREETNAME_ERROR);
                    }

                break;

                case FieldKeys::$VALIDATE_ADDRESS_PROPERTY_CITY:

                    if(!self::isAlphaNumeric($untestedAddressDataValue))
                    {
                        throw new IqsException(InformationCode::$VAL_CITY_ERROR);
                    }

                break;

                case FieldKeys::$VALIDATE_ADDRESS_PROPERTY_STATE:

                    if(!self::isText($untestedAddressDataValue))
                    {
                        //throw new IqsException(InformationCode::$);	//NOTE: Not required - so doesn't have an error - what do to?
                    }

                break;

                case FieldKeys::$VALIDATE_ADDRESS_PROPERTY_ZIP_CODE:

                    if(!self::isNumeric($untestedAddressDataValue))
                    {
                        //throw new IqsException(InformationCode::$);	//NOTE: Not required - so doesn't have an error - what do to?
                    }
                break;

                default:
                    break;
            }
        }
		
        private static function validateIncomingUpdateQuoteSessionDataWithSelect($untestedQuoteDataKey, $untestedQuoteDataValue){
            switch ($untestedQuoteDataKey) {
                case FieldKeys::$UPDATE_QUOTE_SESSION_INSURANCE_PRODUCT:
                    if(!self::isAlphaNumericAndHyphen($untestedQuoteDataValue))
                    {
                        throw new IqsException(InformationCode::$SYS_PROD_DATA_INSURANCE_PROD);
                    }

                    break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_FIRST_NAME:
				case FieldKeys::$UPDATE_QUOTE_SESSION_APPLICANT_FIRST_NAME:

                    if(!self::isText($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_FIRSTNAME_ERROR);
                    }

                    break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_LAST_NAME:
				case FieldKeys::$UPDATE_QUOTE_SESSION_APPLICANT_LAST_NAME:

                    if(!self::isText($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_LASTNAME_ERROR);
                    }

                    break;
				
                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_ADDRESS_LINE_2:

                    if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_APARTMENTNUMBER_ERROR);
                    }

                    break;
                
				case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_USAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_PROPERTYUSAGE_ERROR);
					}
                   
                break;
                
				case FieldKeys::$UPDATE_QUOTE_SESSION_MONTHS_UNOCCUPIED:
                case FieldKeys::$UPDATE_QUOTE_SESSION_NUMBER_OF_MONTHS_UNOCCUPIED:
	
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_TIMEUNOCCUPIED_ERROR);	
					}
					
                break;
                
				case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_BY_CORPORATION:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_OWNEDBY_ERROR);	
					}
					
                break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_NAME:
					
					if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_ENTITYNAME_ERROR);	
					}
					
                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_SHORT_TERM_RENTAL:

                    if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_SHORTTERMRENTAL_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_SINGLE_OCCUPANCY:

                    if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_SINGLEOCCUPANCY_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_STUDENT_OCCUPANCY:

                    if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_STUDENTOCCUPANCY_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_TYPE:

                    if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_PROPERTYMANAGERTYPE_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_ADDRESS_LINE_1:
                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_ADDRESS_LINE_2:

                    if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_PROPERTYMANAGERADDRESS_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_CITY:

                    if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_PROPERTYMANAGERCITY_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_STATE:

                    if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_PROPERTYMANAGERSTATE_ERROR);
                    }

                break;

                case FieldKeys::$UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_ZIPCODE:

                    if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
                    {
                        throw new IqsException(InformationCode::$VAL_PROPERTYMANAGERZIP_ERROR);
                    }

                break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_PRIOR_CARRIER_NUMBER:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_INSURANCEAGENCIES_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_PRIOR_CARRIER_OTHER:
					
					if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_PRIORCARRIEROTHER_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_PRIOR_COVERAGE_A:
					
					if(!self::isCurrency($untestedQuoteDataValue, 999999999) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_DWELLINGCOVERAGE_ERROR);	
					}
					
				break;
			
				case FieldKeys::$UPDATE_QUOTE_SESSION_PRIOR_EXPIRATION_DATE:
					
					if(!self::isDate($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_POLICYEXPIRATION_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_DATE1:
				
				if(!self::isDate($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_DATEOFLOSS1_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_DATE2:
				
				if(!self::isDate($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_DATEOFLOSS2_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_DATE3:
					
					if(!self::isDate($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_DATEOFLOSS3_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_AMOUNT1:
				
				if(!self::isCurrency($untestedQuoteDataValue, 5000000) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSAMOUNTPAID1_ERROR);	
					}
					
				break;
										
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_AMOUNT2:
				
				if(!self::isCurrency($untestedQuoteDataValue, 5000000) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSAMOUNTPAID2_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_AMOUNT3:
					
					if(!self::isCurrency($untestedQuoteDataValue, 5000000) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSAMOUNTPAID3_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_TYPE1:
				
				if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSTYPE1_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_TYPE2:
				
				if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSTYPE2_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_TYPE3:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSTYPE3_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_CAT_INDICATOR1:
				
				if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSCATASTROPHE1_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_CAT_INDICATOR2:
				
				if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSCATASTROPHE2_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_CAT_INDICATOR3:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSCATASTROPHE3_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_DESCRIPTION1:
				
				if(!$untestedQuoteDataValue && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSDESCRIPTION1_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_DESCRIPTION2:
				
				if(!$untestedQuoteDataValue && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSDESCRIPTION2_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_DESCRIPTION3:
					
					if(!$untestedQuoteDataValue && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_LOSSDESCRIPTION3_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_STYLE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_HOMETYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_STRUCTURE_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_STRUCTURETYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_CONSTRUCTION_YEAR:
					
					if(!self::isYear($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_YEARBUILT_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_SQUARE_FOOT_UNDER_ROOF:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue <=10000 && $untestedQuoteDataValue > 200)
					{
						throw new IqsException(InformationCode::$VAL_LIVINGAREA_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_CONSTRUCTION_YEAR_ROOF:
					
					if(!self::isYear($untestedQuoteDataValue) || $untestedQuoteDataValue >= FieldKeys::$UPDATE_QUOTE_SESSION_CONSTRUCTION_YEAR)					
					{
						throw new IqsException(InformationCode::$VAL_ROOFINSTALLED_ERROR);	
					}
					
					break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_ROOF_COVERING_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_ROOFTYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_ROOF_GEOMETRY_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_ROOFSHAPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_GARAGE_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_GARAGETYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_FOUNDATION_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_FOUNDATIONTYPE_ERROR);	
					}
					
					break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_CONSTRUCTION_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_CONSTRUCTIONTYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_CLADDING:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_CLADDINGTYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_MASONRY_VENEER_PERCENTAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_MASONRYVEENERTYPE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_NUMBER_OF_KITCHENS:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_NUMBEROFKITCHENS_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_KITCHEN_QUALITY:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_KITCHENGRADE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_NUMBER_OF_FULL_BATHS:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_NUMBEROFFULLBATHROOMS_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_FULL_BATH_QUALITY:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_FULLBATHROOMGRADE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_NUMBER_OF_HALF_BATHS:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_NUMBEROFHALFBATHROOMS_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_HALF_BATH_QUALITY:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_HALFBATHROOMGRADE_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_NUMBER_OF_FIREPLACES:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_NUMBEROFFIREPLACES_ERROR);	
					}
					
				break;
					
				/*case FieldKeys::$UPDATE_QUOTE_SESSION_HEAT_PUMP:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_HEATPUMP_ERROR);	
					}
					
				break;*/
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_CENTRAL_AIR:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_CENTRALAIR_ERROR);	
					}
					
				break;
					/*
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_FEATURES_1:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						//throw new IqsException(InformationCode::$);	//NOTE: Not required - so doesn't have an error - what do to?
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_FEATURES_2:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						//throw new IqsException(InformationCode::$);	//NOTE: Not required - so doesn't have an error - what do to?	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_FEATURES_3:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						//throw new IqsException(InformationCode::$);	//NOTE: Not required - so doesn't have an error - what do to?
					}
					
				break;
					*/
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_FEATURES_1_SQUARE_FEET:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_HOMEFEATURESQFT_ERROR);
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_FEATURES_2_SQUARE_FEET:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_HOMEFEATURESQFT_ERROR);
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_HOME_FEATURES_3_SQUARE_FEET:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_HOMEFEATURESQFT_ERROR);
					}
					
				break;
					
				/*case FieldKeys::$UPDATE_QUOTE_SESSION_PETS:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_DOGOWNER_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_DOGS:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_DOGBREEDS_ERROR);	
					}
					
				break;
				*/
				case FieldKeys::$UPDATE_QUOTE_SESSION_DISTANCE_FIRE_HYDRANT:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_DISTANCETOFIREHYDRANT_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_POOL_TYPE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_SWIMMINGPOOL_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_POOL_FENCE:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_PROPERTYFENCED_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_DIVING_BOARD_SLIDE:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_BOARDORSLIDE1_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_IMMOVABLE_POOL_LADDER:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_IMMOVABLELADDER_ERROR);	
					}
					
				break;
					
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_MULTI_POLICY:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_MULTIPOLICY_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_AUTO_POLICY_NUMBER:
					
					if(!self::isAlphaNumeric($untestedQuoteDataValue, 50) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_AUTOPOLICYNUMBER_ERROR);	
					}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_PRIME_TIME_DISCOUNT:

						if (!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue) {
							throw new IqsException(InformationCode::$VAL_PRIMETIMEDISCOUNT_ERROR);
						}
					
				break;
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_1_BIRTH_DATE:
					
					$age = FieldKeys::$UPDATE_QUOTE_SESSION_PRIME_TIME_DISCOUNT == 100 ? 50 : false;

					if(!self::isDate($untestedQuoteDataValue, $age) && self::isOfAge($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_INSURED1BIRTHDATE_ERROR);
					}
					
				break;
				/*
				case FieldKeys::$UPDATE_QUOTE_SESSION_FIRE_ALARM:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_BURGLAR_ALARM:
					
					if(!self::isNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_SPRINKLERS:
					
					if(!self::isAlphaNumeric($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
					*/
				case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_1_SSN:
					
					if(!self::isSSN($untestedQuoteDataValue) && $untestedQuoteDataValue)
					{
						throw new IqsException(InformationCode::$VAL_INSURED1SSN_ERROR);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_INSURED_EMAIL_ADDRESS:
					
					if(!self::isEmail($untestedQuoteDataValue))
					{
						throw new IqsException(InformationCode::$VAL_INSUREDEMAILADDRESS_ERROR);	
					}
					
				break;
				
				
				//QUOTE PAGE VALIDATION//
				
				/*
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_A:
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_A_DISPLAY:
					
					if(!self::isCurrency($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_ADDITIONAL_AMOUNTS_OF_INSURANCE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_OPTION_COVERAGE_B:
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_B:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_OPTION_COVERAGE_D:
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_D:		
							
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;	
					
				case FieldKeys::$UPDATE_QUOTE_SESSION_OPTION_COVERAGE_C:
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVEAGE_C:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_PERSONAL_PROPERTY_REPLACEMENT_COST:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_EARTHQUAKE_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_IDENTITY_FRAUD_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_WATER_BACKUP_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_JEWELRY_SPECIAL_LIMITS:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_MECHANICAL_BREAKDOWN_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_THEFT_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_LOSS_ASSESSMENT_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_INCREASED_ORDINCANCE_LIMIT:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_ALL_OTHER_PERILS_DEDUCTIBLE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;	
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_E:
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_L:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;	
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_F:
				case FieldKeys::$UPDATE_QUOTE_SESSION_COVERAGE_M:
					
					if(!self::isCurrency($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;	
				
				case FieldKeys::$UPDATE_QUOTE_SESSION_PERSONAL_INJURY_COVERAGE:
					
					if(!self::isNumeric($untestedQuoteDataValue))
					{
						//throw new IqsException(InformationCode::$);	
					}
					
				break;	
				*/
	
                default:
                    break;
            }
        }


    }

}