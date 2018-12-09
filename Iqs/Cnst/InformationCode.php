<?php
/**
 * InformationCodes
 *
 * error codes and client friendly messages
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   1.0.0
 */

namespace Iqs\Cnst;


class InformationCode
{

    //no error
    public static $SYS_NO_ERROR = array('code' => '0000', 'message' => 'no error');

    //server specific errors 10000 >
    //general errors
    public static $SYS_ERROR = array('code' => '10000', 'message' => 'There has been a system error.'); //this message is typically overwritten with the message from the system
    public static $SYS_EZQUOTE_COM_ERROR = array('code' => '10025', 'message' => 'Error communicating with EZQuote API.');
    public static $SYS_MISSING_KEYS = array('code' => '10050', 'message' => 'Required incoming data keys are missing.');
    public static $SYS_MISSING_FILE_PATH_INI = array('code' => '10075', 'message' => 'File path ini file not found.');
    public static $SYS_UNABLE_TO_PARSE_CONFIG_FILE = array('code' => '10100', 'message' => 'Unable to parse config ini file.');
    public static $SYS_UNABLE_TO_ACCESS_DATABASE = array('code' => '10125', 'message' => 'Unable to Access Database.');


    //ProductData Incoming Data Validation 10200 -
    public static $SYS_PROD_DATA_FEID = array('code' => '10200', 'message' => 'FEID is invalid.');
    public static $SYS_PROD_DATA_INSURANCE_PROD = array('code' => '10225', 'message' => 'Insurance Product ID is invalid.');
    public static $SYS_PROD_DATA_UNSUPPORTED_ZIPCODE = array('code' => '10250', 'message' => 'Zipcode not supported for this product.');
    public static $SYS_PROD_DATA_UNSUPPORTED_STATE = array('code' => '10275', 'message' => 'State not supported.');

    //Address Validation 10300 -
    public static $SYS_ADDRESS_NO_ADDRESS_FOUND = array('code' => '10300', 'message' => 'Address not found.');

    //Session Management 10400 -
    public static $SYS_SESSION_NO_SESSION_FOUND = array('code' => '10400', 'message' => 'Session not found.');

    //EZQuote Responses Invalid 10500 -
    public static $SYS_EZQUOTE_UNEXPECTED_RESPONSE = array('code' => '10500', 'message' => 'Unexpected EZQuote Response.');
    public static $SYS_EZQUOTE_CONFLICT = array('code' => '10525', 'message' => 'There is a conflict with this data.'); //GEICO may have touched this quote
    public static $SYS_EZQUOTE_METHOD_NOT_ALLOWED = array('code' => '10550', 'message' => 'This method is not allowed.'); //GEICO may have touched this quote data

    //Recall quote errors 10600 -
    public static $SYS_RECALL_QUOTE_NOT_FOUND_IN_IQS = array('code' => '10600', 'message' => 'Quote not found in IQS');
    public static $SYS_RECALL_QUOTE_ZIP_NO_MATCH = array('code' => '10625', 'message' => 'Entered zip code does not match quote zip code.');

    //Quote errors 10700
    public static $SYS_QUOTE_LAST_SAVED_QUOTE_CALCUATED_COVERAGEA_NOT_FOUND = array('code' => '10700', 'message' => 'No Calculated Coverage A is Available in Last Saved Quote.');
    public static $SYS_QUOTE_RCE_VALUE_NOT_FOUND = array('code' => '10725', 'message' => 'No Replacement Cost Estimate Available in Active Session.');
    public static $SYS_QUOTE_NO_PREMIUM_RETURNED = array('code' => '10750', 'message' => 'No premium returned.');

    //Quote PDF errors 10800
    public static $SYS_QUOTEPDF_INVALID_TEMPLATE_PATH = array('code' => '10800', 'message' => 'PDF Template Path Invalid.');

    //config errors 10900
    public static $SYS_CONFIG_INVALID_SECTION = array('code' => '10900', 'message' => 'Invalid Configuration Section.');

    //client side equivalent error messages 2000 - 5000
    public static $VAL_FIRSTNAME_ERROR = array('code' => '2000', 'message' => 'Please enter a First Name.');
    public static $VAL_LASTNAME_ERROR = array('code' => '2025', 'message' => 'Please enter a Last Name.');
    public static $VAL_STREETNUMBER_ERROR = array('code' => '2050', 'message' => 'Please enter a Street Number.');
    public static $VAL_STREETNAME_ERROR = array('code' => '2075', 'message' => 'Please enter a Street Name.');
    public static $VAL_APARTMENTNUMBER_ERROR = array('code' => '2100', 'message' => 'Please enter a Apartment, unit, floor, etc.');
    public static $VAL_CITY_ERROR = array('code' => '2125', 'message' => 'Please enter a City.');
    public static $VAL_PROPERTYUSAGE_ERROR = array('code' => '2150', 'message' => 'Please select an option from the highlighted drop down.');
        //Added 6/25/2015 DP3 Rental and Vacant info
    public static $VAL_TIMEUNOCCUPIED_ERROR = array('code' => '2175', 'message' => 'Please select the amount of time unoccupied.');
    public static $VAL_SHORTTERMRENTAL_ERROR = array('code' => '2176', 'message' => 'Please select long term or short term rental type.');
    public static $VAL_SINGLEOCCUPANCY_ERROR = array('code' => '2177', 'message' => 'Please select if single occupancy.');
    public static $VAL_STUDENTOCCUPANCY_ERROR = array('code' => '2178', 'message' => 'Please select if student occupancy.');
    public static $VAL_PROPERTYMANAGERTYPE_ERROR = array('code' => '2179', 'message' => 'Please select the property manager type.');
    public static $VAL_PROPERTYMANAGERADDRESS_ERROR = array('code' => '2180', 'message' => 'Please enter a valid property manager address.');
    public static $VAL_PROPERTYMANAGERCITY_ERROR = array('code' => '2181', 'message' => 'Please enter a valid property manager city.');
    public static $VAL_PROPERTYMANAGERSTATE_ERROR = array('code' => '2182', 'message' => 'Please enter a valid property manager state.');
    public static $VAL_PROPERTYMANAGERZIP_ERROR = array('code' => '2183', 'message' => 'Please enter a valid property manager zip code.');
        //end add
    public static $VAL_OWNEDBY_ERROR = array('code' => '2200', 'message' => 'Please confirm Property Ownership.'); //NOTE: This doesn't get sent to back-end
    public static $VAL_ENTITYNAME_ERROR = array('code' => '2225', 'message' => 'Please enter the Name of Entity.');
    public static $VAL_CURRENTINSURANCE_ERROR = array('code' => '2250', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_INSURANCEAGENCIES_ERROR = array('code' => '2275', 'message' => 'Please select an option from the highlighted drop down.');
	public static $VAL_PRIORCARRIEROTHER_ERROR = array('code' => '2285', 'message' => 'Please enter the name of your prior carrier.');
    public static $VAL_POLICYEXPIRATION_ERROR = array('code' => '2300', 'message' => 'Please enter the policy expiration date.');
    public static $VAL_DWELLINGCOVERAGE_ERROR = array('code' => '2325', 'message' => 'Please enter the dwelling coverage amount on your current policy.');
    public static $VAL_NUMBEROFCLAIMS_ERROR = array('code' => '2350', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_DATEOFLOSS1_ERROR = array('code' => '2375', 'message' => 'Please enter the date of Loss.');
	public static $VAL_DATEOFLOSS2_ERROR = array('code' => '2380', 'message' => 'Please enter the date of Loss.');
	public static $VAL_DATEOFLOSS3_ERROR = array('code' => '2385', 'message' => 'Please enter the date of Loss.');
    public static $VAL_LOSSAMOUNTPAID1_ERROR = array('code' => '2500', 'message' => 'Please enter the amount paid for Loss, rounded to the nearest dollar.');
	public static $VAL_LOSSAMOUNTPAID2_ERROR = array('code' => '2505', 'message' => 'Please enter the amount paid for Loss, rounded to the nearest dollar.');
	public static $VAL_LOSSAMOUNTPAID3_ERROR = array('code' => '2510', 'message' => 'Please enter the amount paid for Loss, rounded to the nearest dollar.');
    public static $VAL_LOSSTYPE1_ERROR = array('code' => '2525', 'message' => 'Please select an option from the highlighted drop down.');
	public static $VAL_LOSSTYPE2_ERROR = array('code' => '2530', 'message' => 'Please select an option from the highlighted drop down.');
	public static $VAL_LOSSTYPE3_ERROR = array('code' => '2535', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_LOSSCATASTROPHE1_ERROR = array('code' => '2550', 'message' => 'Please confirm if Loss is a result of a Catastrophe.');
	public static $VAL_LOSSCATASTROPHE2_ERROR = array('code' => '2555', 'message' => 'Please confirm if Loss is a result of a Catastrophe.');
	public static $VAL_LOSSCATASTROPHE3_ERROR = array('code' => '2560', 'message' => 'Please confirm if Loss is a result of a Catastrophe.');
    public static $VAL_LOSSDESCRIPTION1_ERROR = array('code' => '2575', 'message' => 'Please include a Description of the Loss.');
	public static $VAL_LOSSDESCRIPTION2_ERROR = array('code' => '2580', 'message' => 'Please include a Description of the Loss.');
	public static $VAL_LOSSDESCRIPTION3_ERROR = array('code' => '2585', 'message' => 'Please include a Description of the Loss.');
    public static $VAL_HOMETYPE_ERROR = array('code' => '2600', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_YEARBUILT_ERROR = array('code' => '2625', 'message' => 'Please provide the Year Home was Built. If you do not know the exact year, please provide an estimated year.');
    public static $VAL_ROOFINSTALLED_ERROR = array('code' => '2650', 'message' => 'Please provide the year the Current Roof was installed.');
    public static $VAL_LIVINGAREA_ERROR = array('code' => '2675', 'message' => 'Please provide approximate total living area in square feet.');
    public static $VAL_NUMBEROFSTORIES_ERROR = array('code' => '2700', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_STRUCTURETYPE_ERROR = array('code' => '2725', 'message' => 'Please select a Structure Type.');
    public static $VAL_ROOFTYPE_ERROR = array('code' => '2750', 'message' => 'Please select a Roof Type.');
    public static $VAL_ROOFSHAPE_ERROR = array('code' => '2775', 'message' => 'Please select a Roof Shape.');
    public static $VAL_GARAGESIZE_ERROR = array('code' => '2800', 'message' => 'Please select an option from the highlighted drop down.'); //NOTE: This doesn't have a TermName?
    public static $VAL_GARAGETYPE_ERROR = array('code' => '2825', 'message' => 'Please select a Garage Type.');
    public static $VAL_FOUNDATIONTYPE_ERROR = array('code' => '2850', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_CONSTRUCTIONTYPE_ERROR = array('code' => '2875', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_CLADDINGTYPE_ERROR = array('code' => '2900', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_MASONRYVEENERTYPE_ERROR = array('code' => '2925', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_NUMBEROFKITCHENS_ERROR = array('code' => '2950', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_NUMBEROFFULLBATHROOMS_ERROR = array('code' => '2975', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_NUMBEROFHALFBATHROOMS_ERROR = array('code' => '3000', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_NUMBEROFFIREPLACES_ERROR = array('code' => '3025', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_KITCHENGRADE_ERROR = array('code' => '3050', 'message' => 'Please select an option from the highlighted drop down. See help icon for a guide on grade types.');
    public static $VAL_FULLBATHROOMGRADE_ERROR = array('code' => '3075', 'message' => 'Please select an option from the highlighted drop down. See help icon for a guide on grade types.');
    public static $VAL_HALFBATHROOMGRADE_ERROR = array('code' => '3100', 'message' => 'Please select an option from the highlighted drop down. See help icon for a guide on grade types.');
    public static $VAL_HEATPUMP_ERROR = array('code' => '3125', 'message' => 'Please confirm if your home uses a Heat Pump.');
    public static $VAL_CENTRALAIR_ERROR = array('code' => '3150', 'message' => 'Please confirm if your home uses Central Air Conditioning.');
    public static $VAL_STOVE_ERROR = array('code' => '3175', 'message' => 'Please confirm if there is a Wood, Wood Pellet or Coal Stove.');
    public static $VAL_HEATEDBYOIL_ERROR = array('code' => '3200', 'message' => 'Please confirm if this Home was ever Heated by Oil.');
    public static $VAL_OILTANKLOCATION_ERROR = array('code' => '3225', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_DOGOWNER_ERROR = array('code' => '3250', 'message' => 'Please confirm if you own a dog.');
    public static $VAL_DOGBREEDS_ERROR = array('code' => '3275', 'message' => 'Please confirm if you own any of the types of dogs listed below.');
    public static $VAL_DISTANCETOFIREHYDRANT_ERROR = array('code' => '3300', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_SWIMMINGPOOL_ERROR = array('code' => '3325', 'message' => 'Please select an option from the highlighted drop down.');
    public static $VAL_PROPERTYFENCED_ERROR = array('code' => '3350', 'message' => 'Please confirm if the Property Around the Pool is Fenced.');
    public static $VAL_BOARDORSLIDE1_ERROR = array('code' => '3375', 'message' => 'Please confirm if there is a diving board or slide.');
    public static $VAL_BOARDORSLIDE2_ERROR = array('code' => '3400', 'message' => 'Please confirm if there is a diving board or slide.');
    public static $VAL_IMMOVABLELADDER_ERROR = array('code' => '3425', 'message' => 'Please confirm if there is an immovable ladder.');
    public static $VAL_MULTIPOLICY_ERROR = array('code' => '3450', 'message' => 'Please confirm if you currently have an auto policy with Geico.');
    public static $VAL_AUTOPOLICYNUMBER_ERROR = array('code' => '3475', 'message' => 'Please enter your Geico Auto Policy Number.');
    public static $VAL_PRIMETIMEDISCOUNT_ERROR = array('code' => '3500', 'message' => 'Please select Yes or No to the question below.');
    public static $VAL_INSURED1BIRTHDATE_ERROR = array('code' => '3525', 'message' => 'Please provide your Date of Birth');
    public static $VAL_INSURED1SSN_ERROR = array('code' => '3550', 'message' => 'Please provide a valid 9-Digit Social Security Number.');
    public static $VAL_INSUREDEMAILADDRESS_ERROR = array('code' => '3575', 'message' => 'Please provide a valid Email Address.');
    public static $VAL_ACCEPTLEGALTERMS_ERROR = array('code' => '3600', 'message' => 'Please confirm the Legal Disclosure Information by checking the box.');
	public static $VAL_HOMEFEATURESQFT_ERROR = array('code' => '3625', 'message' => 'Please select an option from the highlighted drop down.');
} 