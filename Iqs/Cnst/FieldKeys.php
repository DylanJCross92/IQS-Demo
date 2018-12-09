<?php
/**
 * FieldNameKey
 *
 * names of each field incoming
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   0.3.0
 */
//naming convention: SYSTEM_SUBSYSTEM(S)_SECTION_DETAIL
namespace Iqs\Cnst;


class FieldKeys {

    //fundamental
    public static $IQS_BOOL_TRUE = "true";
    public static $IQS_BOOL_FALSE = "false";

    //IQS API Call Parameter Keys
    public static $IQS_PARAMETER_SESSION_ID = "SessionId";

    //IQS API Response Payload Names
    public static $IQS_PAYLOAD_ITEM_ADDRESS_ARRAY 				= "Addresses";
    public static $IQS_PAYLOAD_ITEM_QUOTE_ID 					= "QuoteId";
    public static $IQS_PAYLOAD_ITEM_EMPTY						= "";
    public static $IQS_PAYLOAD_ITEM_TEST_DATA 					= "TestData";
    public static $IQS_PAYLOAD_ITEM_SELECTED_QUOTE_DATA_ARRAY 	= "SelectedQuoteData";
    public static $IQS_PAYLOAD_ITEM_STORAGE_DATA 				= "SessionStorageData";
    public static $IQS_PAYLOAD_ITEM_QUOTE 						= "Quote";
	public static $IQS_PAYLOAD_ITEM_SYSTEM 						= "System";
    public static $IQS_PAYLOAD_ITEM_LAST_QUOTE_SECTION_PAGE_ID  = "LastQuoteSectionPageId";
    public static $IQS_PAYLOAD_ITEM_TOUCHED_SECTION_LIST        = "TouchedSectionList";
    public static $IQS_PAYLOAD_ITEM_API_DEBUG_MESSAGE           = "APIDebugMessage";
    public static $IQS_PAYLOAD_ITEM_API_DEBUG_ERROR             = "APIDebugError";
    public static $IQS_PAYLOAD_ITEM_EXCEPTION_DEBUG_MESSAGE     = "ExceptionMessage";
    public static $IQS_PAYLOAD_ITEM_DEFAULT_HOME_FEATURES         = "DefaultHomeFeatures";
    public static $IQS_CONFIGURATION_DP	                        = "dp3enabled";

    //Quote Data Items that require encryption
    public static $IQS_ENCRYPTION_ITEM_INSURED_1_SSN						= "Insured1SSN";
    public static $IQS_ENCRYPTION_ITEM_INSURED_1_SSN_DISPLAY				= "Insured1SSNDisplay";
    public static $IQS_ENCRYPTION_ITEM_INSURED_1_SSN_REQUIRED_INDICATOR 	= "Insured1SSNRequiredIndicator";

    //Quote Data Items that are invalid
    public static $IQS_INVALID_QUOTE_DATA_KEY_MESSAGE = "Message";
    public static $IQS_INVALID_QUOTE_DATA_KEY_ERRORS = "errors";

	//IQS getSystemData keys
    public static $IQS_SYSTEM_SECTION       = "system";
	public static $IQS_SYSTEM_DATA_DATE 	= "date";
	public static $IQS_SYSTEM_DATA_TIME		= "time";
	public static $IQS_SYSTEM_DATA_VERSION	= "version";
    public static $IQS_SYSTEM_DATA_EZQVERSION	= "ezqversion";
	
	//IQS file path Configuration File fields (filePaths.ini)
	public static $IQS_CONFIGURATION_IQSCONF 	= "iqsconf";
	public static $IQS_CONFIGURATION_FILEPATH	= "filepath";
	
    //IQS DynConf Database ConfId fields & table name
    public static $IQS_DYNCONF_CONFID_AUTH = "EzQuoteAuth";
    public static $IQS_DYNCONF_DEBUG_CONFID_AUTH = "EzQuoteDebugAuth";

    //IQS DynConf Database Field names
    public static $IQS_DATABASE_DYNCONF_CONFID = "ConfId";
    public static $IQS_DATABASE_DYNCONF_CONFVALUE = "ConfValue";

    //IQS GeneratedQuotes Database Fields
    public static $IQS_DATABASE_GENERATEDQUOTES_GENERATEDQUOTEID    = "GeneratedQuoteId";
    public static $IQS_DATABASE_GENERATEDQUOTES_QUOTEID             = "QuoteId";
    public static $IQS_DATABASE_GENERATEDQUOTES_CREATEDATE          = "CreateDate";
    public static $IQS_DATABASE_GENERATEDQUOTES_LASTACCESSDATE      = "LastAccessDate";
    public static $IQS_DATABASE_GENERATEDQUOTES_COMPLETEDATE        = "CompleteDate";
    public static $IQS_DATABASE_GENERATEDQUOTES_ACCESSCOUNT         = "AccessCount";
    public static $IQS_DATABASE_GENERATEDQUOTES_FEID                = "Feid";
    public static $IQS_DATABASE_GENERATEDQUOTES_SESSION_GUID        = "SessionGuid";

    //WhiteList Filter Keys / db table columns
    public static $IQS_DATABASE_WHITELIST_TABLE	            = "ZipCodeWhiteList";
    public static $IQS_DATABASE_WHITELIST_WHITELISTID	    = "WhiteListId";
    public static $IQS_DATABASE_WHITELIST_FILTER_ZIPCODE	= "ZipCode";
    public static $IQS_DATABASE_WHITELIST_FILTER_STATE	    = "State";
    public static $IQS_DATABASE_WHITELIST_FILTER_PRODUCT	= "Product";

    //IQS Conf table fields
    public static $IQS_DATABASE_CONF_TABLE = "Conf";
    public static $IQS_DATABASE_CONF_SECTION = "ConfSection";
    public static $IQS_DATABASE_CONF_ELEMENT = "ConfElement";
    public static $IQS_DATABASE_CONF_VALUE = "ConfValue";

    //valid conf sections
    public static $IQS_CONF_SECTION_LOGGING = "logging";
    public static $IQS_CONF_SECTION_EZQUOTEAPI = "ezquoteapi";
    public static $IQS_CONF_SECTION_STATES = "statesenabled";
    public static $IQS_CONF_SECTION_PRODUCTS = "productsenabled";
    public static $IQS_CONF_SECTION_WHITELISTENABLED = "whitelistenabled";
    public static $IQS_CONF_SECTION_ENVIRONMENT = "env";


    public static function getValidIqsConfSections(){
        $confCategoryArray = array();
        $confCategoryArray[]=self::$IQS_CONF_SECTION_LOGGING;
        $confCategoryArray[]=self::$IQS_CONF_SECTION_EZQUOTEAPI;
        $confCategoryArray[]=self::$IQS_CONF_SECTION_STATES;
        $confCategoryArray[]=self::$IQS_CONF_SECTION_PRODUCTS;
        $confCategoryArray[]=self::$IQS_CONF_SECTION_WHITELISTENABLED;
        $confCategoryArray[]=self::$IQS_CONF_SECTION_ENVIRONMENT;
        return $confCategoryArray;
    }

    //IQS iqsConf.ini fields
    public static $IQS_CONF_ELEMENT_DATABASE_DBHOST 		= "dbhost";
    public static $IQS_CONF_ELEMENT_DATABASE_DBUSER 		= "dbuser";
    public static $IQS_CONF_ELEMENT_DATABASE_DBPASS 		= "dbpass";
    public static $IQS_CONF_ELEMENT_DATABASE_DBNAME 		= "dbname";
    public static $IQS_CONF_ELEMENT_DATABASE_DBPORT 		= "dbport";
    public static $IQS_CONF_ELEMENT_DATABASE_DBDRIVER 	    = "dbdriver";

    public static $IQS_CONF_ELEMENT_PLATFORM_INSTALLTYPE    = "installtype";

    public static $IQS_CONF_ELEMENT_LOGGING_DATABASELOGGING 	= "databaselogging";
    public static $IQS_CONF_ELEMENT_LOGGING_FILELOGGING 		= "filelogging";
    public static $IQS_CONF_ELEMENT_LOGGING_LOGFILEPATH		    = "logfilepath";

    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_BASEURL 	    = "baseurl";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_APIUID	    = "apiuid";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_APIPW		= "apipw";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_APIALC	    = "apialc";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_DEBUG_APIUID	= "debugapiuid";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_DEBUG_APIPW	= "debugapipw";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_DEBUG_APIALC	= "debugapialc";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_UID 	    = "methodovrduid_";
    public static $IQS_CONF_ELEMENT_EZQUOTEAPI_METHOD_AUTH_PW 	    = "methodovrdpw_";

    public static $IQS_CONF_ELEMENT_ENC_KEY = "key";

    public static $IQS_CONF_ELEMENT_ENV_DEBUG = "debug";
    //note: states and products are dynamic and are not reflected as permanent elements like the elements above

    //EzQuote API method names
    //TODO: fill this out and replace throughout code

    //EzQuote general payload items
    public static $EZ_QUOTE_STATUS = "status";
    public static $EZ_QUOTE_ENTITY = "entity";

    //EzQuote Auth Response
    public static $EZ_QUOTE_AUTH = "Authorization";

    //EzQuote Address Response keys
    public static $EZ_QUOTE_ADDRESS_FULL_INFO	= "AddressFullInfo";
    public static $EZ_QUOTE_ADDRESS_BASIC_INFO	= "AddressBasicInfo";

    //EzQuote Quote Response from ezrce
    public static $EZ_QUOTE_QUOTE_RESPONSE_PREMIUM = "TotalPremium";


    //validateAddress & beginIqsSession - ProductDataArray
    public static $PRODUCT_DATA_ARRAY 	= "ProductDataArray";
    public static $PRODUCT_FEID			= "Feid";
    public static $PRODUCT_DEBUG        = "Debug";
    public static $PRODUCT_ALTDATA      = "AltData";
    public static $PRODUCT_PAGEID       = "PageId";
	public static $PRODUCT_DP3			= "DP3";
	public static $PRODUCT_HO3			= "HO3";



    //logPageTouch - LogPageTouchDataArray
    public static $LOG_SESSION_TRACK_DATA_ARRAY = "LogSessionTrackDataArray";
    public static $LOG_SESSION_TRACK_PAGEID = "PageId";

    //validateAddress - ValidateAddressDataArray
    public static $VALIDATE_ADDRESS_DATA_ARRAY 				= "ValidateAddressDataArray";
    public static $VALIDATE_ADDRESS_PROPERTY_STREET_NUMBER 	= "PropertyStreetNumber";
    public static $VALIDATE_ADDRESS_PROPERTY_STREET_NAME 	= "PropertyStreetName";
    public static $VALIDATE_ADDRESS_PROPERTY_CITY 			= "PropertyCity";
    public static $VALIDATE_ADDRESS_PROPERTY_STATE 			= "PropertyState";
    public static $VALIDATE_ADDRESS_PROPERTY_ZIP_CODE 		= "PropertyZipCode";

    //needed address full info keys
    public static $ADDRESS_FULL_INFO_SQRFTUNDERROOFRETRIEVED = "SquareFootUnderRoofRetrieved";
    public static $ADDRESS_FULL_INFO_CONSTRUCTYEARRETRIEVED = "ConstructionYearRetrieved";

    //beginQuoteSession
    public static $BEGIN_QUOTE_SESSION_DATA_ARRAY	= "BeginQuoteDataArray";
    public static $BEGIN_QUOTE_SESSION_ADDRESS_ID	= "AddressId";
    public static $BEGIN_QUOTE_SESSION_PRODUCT_ID   = "productId";
    public static $BEGIN_QUOTE_SESSION_PRODUCT_ID_CAP   = "ProductId";
    public static $BEGIN_QUOTE_SESSION_INSURANCE_PRODUCT   = "Insurance_Product";
    public static $BEGIN_QUOTE_SESSION_BEGIN_QUOTE_SESSION_INSURED_BY_CORPORATION = "InsuredByCorporation";

    //quoteBlock
    public static $LOG_QUOTE_SESSION_BLOCK_DATA_ARRAY	= "BlockQuoteDataArray";
    public static $LOG_QUOTE_SESSION_BLOCK_CODE 		= "BlockQuoteCode";

    //updateStorage
    public static $UPDATE_SESSION_STORAGE_DATA 				    	        = "UpdateSessionStorageData";

    //getQuoteData
    public static $GET_SELECTED_QUOTE_DATA_ARRAY                            = "SelectedQuoteDataArray";

    //updateQuoteSession
    public static $UPDATE_QUOTE_SESSION_RUN_UPDATE_IN_BACKGROUND            = "RunUpdateInBackground";
    public static $UPDATE_QUOTE_SESSION_DATA_ARRAY 							= "UpdateQuoteDataArray";
    public static $UPDATE_QUOTE_SESSION_INSURANCE_PRODUCT                   = "Insurance_Product";
	public static $UPDATE_QUOTE_SESSION_INSURED_NAME 						= "insuredName";
    public static $UPDATE_QUOTE_SESSION_INSURED_FIRST_NAME 		            = "InsuredFirstName";
    public static $UPDATE_QUOTE_SESSION_INSURED_LAST_NAME		            = "InsuredLastName";
	public static $UPDATE_QUOTE_SESSION_APPLICANT_FIRST_NAME 		        = "ApplicantFirstName";
    public static $UPDATE_QUOTE_SESSION_APPLICANT_LAST_NAME		            = "ApplicantLastName";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_ADDRESS_LINE_2             = "PropertyAddressLine2";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_USAGE 						= "PropertyUsage";
	public static $UPDATE_QUOTE_SESSION_PROPERTY_OCCUPANCY 					= "PropertyOccupancy";
	public static $UPDATE_QUOTE_SESSION_MONTHS_UNOCCUPIED 					= "MonthsUnoccupied";
	public static $UPDATE_QUOTE_SESSION_INSURED_BY_CORPORATION 				= "InsuredByCorporation";

    //added for DP3 6/25/2015 SDR
    public static $UPDATE_QUOTE_SESSION_NUMBER_OF_MONTHS_UNOCCUPIED 		= "NumberOfMonthsUnoccupied";
    public static $UPDATE_QUOTE_SESSION_SHORT_TERM_RENTAL                   = "ShortTermRental";
    public static $UPDATE_QUOTE_SESSION_SINGLE_OCCUPANCY                    = "SingleOccupancy";
    public static $UPDATE_QUOTE_SESSION_STUDENT_OCCUPANCY                   = "StudentOccupancy";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_TYPE               = "PropertyManagerType";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_ADDRESS_LINE_1     = "PropertyManagerAddressLine1";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_ADDRESS_LINE_2     = "PropertyManagerAddressLine2";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_CITY               = "PropertyManagerCity";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_STATE              = "PropertyManagerState";
    public static $UPDATE_QUOTE_SESSION_PROPERTY_MANAGER_ZIPCODE            = "PropertyManagerZip";
    //end add

	public static $UPDATE_QUOTE_SESSION_PRIOR_CARRIER_NUMBER 				= "PriorCarrierNumber";
	public static $UPDATE_QUOTE_SESSION_PRIOR_CARRIER_OTHER 				= "PriorCarrierOther";
	public static $UPDATE_QUOTE_SESSION_PRIOR_EXPIRATION_DATE 				= "PriorExpirationDate";
	public static $UPDATE_QUOTE_SESSION_PRIOR_COVERAGE_A 					= "PriorCoverageA";
	public static $UPDATE_QUOTE_SESSION_NUMBER_OF_CLAIMS					= "NumberOfClaims";
	
	public static $UPDATE_QUOTE_SESSION_LOSS_DATE1 							= "LossDate1";
	public static $UPDATE_QUOTE_SESSION_LOSS_AMOUNT1 						= "LossAmount1";
	public static $UPDATE_QUOTE_SESSION_LOSS_TYPE1 							= "LossType1";
	public static $UPDATE_QUOTE_SESSION_LOSS_CAT_INDICATOR1 				= "LossCatIndicator1";
	public static $UPDATE_QUOTE_SESSION_LOSS_DESCRIPTION1 					= "LossDescription1";
	
	public static $UPDATE_QUOTE_SESSION_LOSS_DATE2 							= "LossDate2";
	public static $UPDATE_QUOTE_SESSION_LOSS_AMOUNT2 						= "LossAmount2";
	public static $UPDATE_QUOTE_SESSION_LOSS_TYPE2 							= "LossType2";
	public static $UPDATE_QUOTE_SESSION_LOSS_CAT_INDICATOR2 				= "LossCatIndicator2";
	public static $UPDATE_QUOTE_SESSION_LOSS_DESCRIPTION2 					= "LossDescription2";
	
	public static $UPDATE_QUOTE_SESSION_LOSS_DATE3 							= "LossDate3";
	public static $UPDATE_QUOTE_SESSION_LOSS_AMOUNT3 						= "LossAmount3";
	public static $UPDATE_QUOTE_SESSION_LOSS_TYPE3 							= "LossType3";
	public static $UPDATE_QUOTE_SESSION_LOSS_CAT_INDICATOR3 				= "LossCatIndicator3";
	public static $UPDATE_QUOTE_SESSION_LOSS_DESCRIPTION3 					= "LossDescription3";

	public static $UPDATE_QUOTE_SESSION_HOME_STYLE 							= "HomeStyle";
	public static $UPDATE_QUOTE_SESSION_STRUCTURE_TYPE 						= "StructureType";
	public static $UPDATE_QUOTE_SESSION_CONSTRUCTION_YEAR 					= "ConstructionYear";
	public static $UPDATE_QUOTE_SESSION_SQUARE_FOOT_UNDER_ROOF	 			= "SquareFootUnderRoof";
    public static $UPDATE_QUOTE_SESSION_SQUARE_FOOT_UNDER_ROOF_FLA	 		= "SquareFootUnderRoofFLA";
    public static $UPDATE_QUOTE_SESSION_SQUARE_FOOT_UNDER_ROOF_GARAGE	 	= "SquareFootUnderRoofGarage";
	public static $UPDATE_QUOTE_SESSION_NUMBER_OF_STORIES 					= "NumberOfStories";
	public static $UPDATE_QUOTE_SESSION_CONSTRUCTION_YEAR_ROOF				= "ConstructionYearRoof";
	public static $UPDATE_QUOTE_SESSION_ROOF_COVERING_TYPE					= "RoofCoveringType";
	public static $UPDATE_QUOTE_SESSION_ROOF_GEOMETRY_TYPE					= "RoofGeometryType";
	public static $UPDATE_QUOTE_SESSION_GARAGE_TYPE 						= "GarageType";
	public static $UPDATE_QUOTE_SESSION_FOUNDATION_TYPE 					= "FoundationType";
	public static $UPDATE_QUOTE_SESSION_CONSTRUCTION_TYPE 					= "ConstructionType";
	public static $UPDATE_QUOTE_SESSION_CLADDING 							= "Cladding";
	public static $UPDATE_QUOTE_SESSION_MASONRY_VENEER_PERCENTAGE 			= "MasonryVeneerPercentage";
	public static $UPDATE_QUOTE_SESSION_NUMBER_OF_KITCHENS 					= "NumberOfKitchens";
	public static $UPDATE_QUOTE_SESSION_KITCHEN_QUALITY 					= "KitchenQuality";
	public static $UPDATE_QUOTE_SESSION_NUMBER_OF_FULL_BATHS 				= "NumberOfFullBaths";
	public static $UPDATE_QUOTE_SESSION_FULL_BATH_QUALITY 					= "FullBathQuality";
	public static $UPDATE_QUOTE_SESSION_NUMBER_OF_HALF_BATHS 				= "NumberOfHalfBaths";
	public static $UPDATE_QUOTE_SESSION_HALF_BATH_QUALITY 					= "HalfBathQuality";
	public static $UPDATE_QUOTE_SESSION_NUMBER_OF_FIREPLACES				= "NumberOfFireplaces";
	public static $UPDATE_QUOTE_SESSION_HEAT_PUMP 							= "HeatPump";
	public static $UPDATE_QUOTE_SESSION_CENTRAL_AIR 						= "CentralAir";
	public static $UPDATE_QUOTE_SESSION_HOME_FEATURES_1 					= "HomeFeatures1";
	public static $UPDATE_QUOTE_SESSION_HOME_FEATURES_2 					= "HomeFeatures2";
	public static $UPDATE_QUOTE_SESSION_HOME_FEATURES_3						= "HomeFeatures3";
	public static $UPDATE_QUOTE_SESSION_HOME_FEATURES_1_SQUARE_FEET			= "HomeFeatures1SquareFeet";
	public static $UPDATE_QUOTE_SESSION_HOME_FEATURES_2_SQUARE_FEET			= "HomeFeatures2SquareFeet";
	public static $UPDATE_QUOTE_SESSION_HOME_FEATURES_3_SQUARE_FEET		 	= "HomeFeatures3SquareFeet";
	public static $UPDATE_QUOTE_SESSION_PETS								= "Pets";
	public static $UPDATE_QUOTE_SESSION_DOGS 								= "Dogs";
	public static $UPDATE_QUOTE_SESSION_DISTANCE_FIRE_HYDRANT 				= "DistanceFireHydrant";
	public static $UPDATE_QUOTE_SESSION_POOL_TYPE 							= "PoolType";
	public static $UPDATE_QUOTE_SESSION_POOL_FENCE 							= "PoolFence";
	public static $UPDATE_QUOTE_SESSION_DIVING_BOARD_SLIDE					= "DivingBoardSlide";
	public static $UPDATE_QUOTE_SESSION_IMMOVABLE_POOL_LADDER				= "ImmovablePoolLadder";
	public static $UPDATE_QUOTE_SESSION_MULTI_POLICY						= "MultiPolicy";
	public static $UPDATE_QUOTE_SESSION_AUTO_POLICY_NUMBER 					= "AutoPolicyNumber";
	public static $UPDATE_QUOTE_SESSION_PRIME_TIME_DISCOUNT					= "PrimeTimeDiscount";
	public static $UPDATE_QUOTE_SESSION_INSURED_1_BIRTH_DATE				= "Insured1BirthDate";
	public static $UPDATE_QUOTE_SESSION_INSURED_1_SSN						= "Insured1SSN";
	public static $UPDATE_QUOTE_SESSION_INSURED_1_SSN_DISPLAY				= "Insured1SSNDisplay";
	public static $UPDATE_QUOTE_SESSION_INSURED_1_SSN_REQUIRED_INDICATOR 	= "Insured1SSNRequiredIndicator";
	public static $UPDATE_QUOTE_SESSION_INSURED_EMAIL_ADDRESS 				= "InsuredEmailAddress";
	public static $UPDATE_QUOTE_SESSION_FIRE_ALARM							= "FireAlarm";
	public static $UPDATE_QUOTE_SESSION_BURGLAR_ALARM						= "BurglarAlarm";
	public static $UPDATE_QUOTE_SESSION_SPRINKLERS							= "Sprinklers";

	//quote data
	
	public static $UPDATE_QUOTE_SESSION_COVERAGE_A 							= "CoverageA";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_A_DISPLAY 					= "CoverageADisplay";
	public static $UPDATE_QUOTE_SESSION_ADDITIONAL_AMOUNTS_OF_INSURANCE 	= "AdditionalAmountsOfInsurance";
	public static $UPDATE_QUOTE_SESSION_OPTION_COVERAGE_B 					= "OptionCoverageB";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_B 							= "CoverageB";
	public static $UPDATE_QUOTE_SESSION_OPTION_COVERAGE_D					= "OptionCoverageD";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_D 							= "CoverageD";
	public static $UPDATE_QUOTE_SESSION_OPTION_COVERAGE_C 					= "OptionCoverageC";
	public static $UPDATE_QUOTE_SESSION_COVEAGE_C 							= "CoverageC";
	public static $UPDATE_QUOTE_SESSION_PERSONAL_PROPERTY_REPLACEMENT_COST 	= "PersonalPropertyReplacementCost";
	public static $UPDATE_QUOTE_SESSION_EARTHQUAKE_COVERAGE 				= "EarthquakeCoverage";
	public static $UPDATE_QUOTE_SESSION_IDENTITY_FRAUD_COVERAGE 			= "IdentityFraudCoverage";
	public static $UPDATE_QUOTE_SESSION_WATER_BACKUP_COVERAGE 				= "WaterBackupCoverage";
	public static $UPDATE_QUOTE_SESSION_JEWELRY_SPECIAL_LIMITS 				= "JewelrySpecialLimits";
	public static $UPDATE_QUOTE_SESSION_MECHANICAL_BREAKDOWN_COVERAGE 		= "MechanicalBreakdownCoverage";
	public static $UPDATE_QUOTE_SESSION_THEFT_COVERAGE 						= "TheftCoverage";
	public static $UPDATE_QUOTE_SESSION_LOSS_ASSESSMENT_COVERAGE 			= "LossAssessmentCoverage";
	public static $UPDATE_QUOTE_SESSION_INCREASED_ORDINCANCE_LIMIT 			= "IncreasedOrdinanceLimit";
	public static $UPDATE_QUOTE_SESSION_ALL_OTHER_PERILS_DEDUCTIBLE 		= "AllOtherPerilsDeductible";
	public static $UPDATE_QUOTE_SESSION_HURRICANE_DEDUCTIBLE 				= "HurricaneDeductible";
	public static $UPDATE_QUOTE_SESSION_WIND_HAIL_DEDUCTIBLE				= "WindHailDeductible";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_E 							= "CoverageE";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_L 							= "CoverageL";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_F 							= "CoverageF";
	public static $UPDATE_QUOTE_SESSION_COVERAGE_M 							= "CoverageM";
	public static $UPDATE_QUOTE_SESSION_PERSONAL_INJURY_COVERAGE 			= "PersonalInjuryCoverage";

    public static $UPDATE_QUOTE_SESSION_USER                                = "User";
    public static $UPDATE_QUOTE_SESSION_AGENCY_LOCATION_CODE                = "AgencyLocationCode";

    //Custom fields, not required by EZQ but used internally
    public static $UPDATE_QUOTE_SESSION_REPLACEMENT_COST_ESTIMATE           = "ReplacementCostEstimate";
    public static $UPDATE_QUOTE_SESSION_CALCULATED_COVERAGEA                = "CalculatedCoverageA";
    public static $UPDATE_QUOTE_SESSION_LAST_QUOTE_SECTION_PAGE_ID          = "LastQuoteSectionPageId";

    //Debugging
    public static $DEBUG_MODE_QUOTE_RECORD_FEID_IDENTIFIER = ".d";

	//PDF Template Internal Query String
	public static $PDFTEMPLATE_PREFIX_QUERY_STRING	= "PDFTEMPLATEPREFIX";



} 