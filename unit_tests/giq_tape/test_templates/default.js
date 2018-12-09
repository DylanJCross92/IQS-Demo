var test = require('/node_modules/tape');
var request = require('request');

require("../tape_config.js");

var FEID = FEID ? FEID : "GIQ";

/*
 *   Global Variables
 */
var SessionId;
var quoteId;

console.log("============================");
console.log("=======| "+PropertyState+" "+Insurance_Product+" Test |======");
console.log("============================");
console.log("");

/*
 *   beginIqsSession
 */
test('beginIqsSession', function (assert) {

    //console.log("Retrieving beginIqsSession response");

    var requestData = {"ProductDataArray":{"Feid": FEID+""+PropertyState,"AltData":"SaneID:null","Debug":"false","PageId":"1.1"}};

    request({
        url: host+'/Iqs/api.php/beginIqsSession',
        method: "POST",
        json: requestData
    }, function(error, response, json){

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }
        else
        {
            SessionId = json.responseDataPayload.SessionId;
        }

        assert.equal(json.exception, null);

        assert.end();
    });

});


/*
 *   validateAddress
 */
test('validateAddress', function (assert) {

    //console.log("Retrieving validateAddress response");

    var requestData = {"SessionId":SessionId,"ValidateAddressDataArray":{"Insurance_Product": Insurance_Product, "PropertyStreetNumber": PropertyStreetNumber,"PropertyStreetName": PropertyStreetName,"PropertyCity": PropertyCity,"PropertyState": PropertyState,"PropertyZipCode": zipCode}};

    request({
        url: host+'/Iqs/api.php/validateAddress',
        method: "POST",
        json: requestData
    }, function (error, response, json) {

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }

        assert.equal(json.exception, null);

        assert.end();
    });


});

/*
 *   beginQuoteSession
 */
test('beginQuoteSession', function (assert) {

    //console.log("Retrieving beginQuoteSession response");

    var requestData = {"SessionId":SessionId,"BeginQuoteDataArray":{"AddressId":"0","Insurance_Product": Insurance_Product,"InsuredByCorporation":"200"}};

    request({
        url: host+'/Iqs/api.php/beginQuoteSession',
        method: "POST",
        json: requestData
    }, function (error, response, json) {

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }
        else
        {
            quoteId = json.responseDataPayload.QuoteId;
        }

        assert.equal(json.exception, null);

        assert.end();
    });
});

/*
 *   updateQuoteSession
 */
test('updateQuoteSession', function (assert) {

    //console.log("Retrieving updateQuoteSession response");

    var requestData;

    if(Insurance_Product == "HO3")
    {
        requestData = {"SessionId":SessionId, "UpdateQuoteDataArray":{"InsuredByCorporation":"","InsuredName":"","ApplicantFirstName":"","ApplicantLastName":"","InsuredFirstName":"first","InsuredLastName":"last","PropertyAddressLine2":"","PropertyUsage":"100","PropertyOccupancy":"100","MonthsUnoccupied":"","NumberOfMonthsUnoccupied":"","ShortTermRental":"","SingleOccupancy":"","StudentOccupancy":"","PropertyManagerType":"","PropertyManagerAddressLine1":"","PropertyManagerAddressLine2":"","PropertyManagerCity":"","PropertyManagerState":"","PropertyManagerZip":"","PriorCarrierNumber":"1","PriorCarrierOther":"","PriorExpirationDate":"","NumberOfClaims":"0","PriorCoverageA":"","LossDate1":"","LossAmount1":"","LossType1":"","LossCatIndicator1":"","LossDescription1":"","LossDate2":"","LossAmount2":"","LossType2":"","LossCatIndicator2":"","LossDescription2":"","LossDate3":"","LossAmount3":"","LossType3":"","LossCatIndicator3":"","LossDescription3":"","HomeStyle":"400","ConstructionYear":"1999","SquareFootUnderRoof":"2552","NumberOfStories":"100","StructureType":"100","ConstructionYearRoof":"1999","RoofCoveringType":"500","RoofGeometryType":"300","GarageType":"1","FoundationType":"300","ConstructionType":"200","Cladding":"","MasonryVeneerPercentage": "","NumberOfKitchens":"1","KitchenQuality":"100","NumberOfFullBaths":"2","FullBathQuality":"100","NumberOfHalfBaths":"0","HalfBathQuality":"","NumberOfFireplaces":"0","HeatPump":"100","CentralAir":"200","HomeFeatures1":"","HomeFeatures1SquareFeet":"","HomeFeatures2":"","HomeFeatures2SquareFeet":"","HomeFeatures3":"","HomeFeatures3SquareFeet":"","Pets":"200","Dogs":"","DistanceFireHydrant":"1000","PoolType":"1","PoolFence":"","DivingBoardSlide":"","ImmovablePoolLadder":"","MultiPolicy":"200","AutoPolicyNumber":"","PrimeTimeDiscount":"200","Insured1BirthDate":"11/29/1992","Insured1SSN":"432434243","Insured1SSNDisplay":"432434243","Insured1SSNRequiredIndicator":"100","InsuredEmailAddress":"test@test.com","FireAlarm":"","BurglarAlarm":"","Sprinklers":""},"RunUpdateInBackground":false};
    }
    else if(Insurance_Product == "DP3")
    {
        requestData = {"SessionId":SessionId, "UpdateQuoteDataArray":{"InsuredByCorporation":"","InsuredName":"first last","ApplicantFirstName":"","ApplicantLastName":"","InsuredFirstName":"first","InsuredLastName":"last","PropertyAddressLine2":"","PropertyUsage":"100","PropertyOccupancy":"200","MonthsUnoccupied":"","NumberOfMonthsUnoccupied":"0","ShortTermRental":"200","SingleOccupancy":"200","StudentOccupancy":"200","PropertyManagerType":"100","PropertyManagerAddressLine1":"","PropertyManagerAddressLine2":"","PropertyManagerCity":"","PropertyManagerState":"","PropertyManagerZip":"","PriorCarrierNumber":"1","PriorCarrierOther":"","PriorExpirationDate":"","NumberOfClaims":"0","PriorCoverageA":"","LossDate1":"","LossAmount1":"","LossType1":"","LossCatIndicator1":"","LossDescription1":"","LossDate2":"","LossAmount2":"","LossType2":"","LossCatIndicator2":"","LossDescription2":"","LossDate3":"","LossAmount3":"","LossType3":"","LossCatIndicator3":"","LossDescription3":"","HomeStyle":"400","ConstructionYear":"1999","SquareFootUnderRoof":"2552","NumberOfStories":"100","StructureType":"100","ConstructionYearRoof":"1999","RoofCoveringType":"500","RoofGeometryType":"300","GarageType":"1","FoundationType":"300","ConstructionType":"200","Cladding":"","MasonryVeneerPercentage": "","NumberOfKitchens":"1","KitchenQuality":"100","NumberOfFullBaths":"2","FullBathQuality":"100","NumberOfHalfBaths":"0","HalfBathQuality":"","NumberOfFireplaces":"0","HeatPump":"100","CentralAir":"200","HomeFeatures1":"","HomeFeatures1SquareFeet":"","HomeFeatures2":"","HomeFeatures2SquareFeet":"","HomeFeatures3":"","HomeFeatures3SquareFeet":"","Pets":"200","Dogs":"","DistanceFireHydrant":"1000","PoolType":"1","PoolFence":"","DivingBoardSlide":"","ImmovablePoolLadder":"","MultiPolicy":"200","AutoPolicyNumber":"","PrimeTimeDiscount":"200","Insured1BirthDate":"11/29/1992","Insured1SSN":"432434243","Insured1SSNDisplay":"432434243","Insured1SSNRequiredIndicator":"100","InsuredEmailAddress":"test@test.com","FireAlarm":"","BurglarAlarm":"","Sprinklers":""},"RunUpdateInBackground":false};
    }

    request({
        url: host+'/Iqs/api.php/updateQuoteSession',
        method: "PUT",
        json: requestData
    }, function (error, response, json) {

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }

        assert.equal(json.exception, null);

        assert.end();
    });
});

/*
 *   generateSavedQuote
 */
test('generateSavedQuote', function (assert) {

    //console.log("Retrieving generateSavedQuote response");

    var requestData = {"SessionId":SessionId,"UpdateQuoteDataArray":{"AdditionalAmountsOfInsurance":"2500","OptionCoverageB":"1000","OptionCoverageC":"7000","OptionCoverageD":"3000","PersonalPropertyReplacementCost":"100","EarthquakeCoverage":"200","IdentityFraudCoverage":"0","WaterBackupCoverage":"0","JewelrySpecialLimits":"1500","MechanicalBreakdownCoverage":"200","LossAssessmentCoverage":"1000","IncreasedOrdinanceLimit":"1000","AllOtherPerilsDeductible":"10","CoverageE":"300000","CoverageF":"1000","PersonalInjuryCoverage" :"200"}};

    request({
        url: host+'/Iqs/api.php/generateSavedQuote',
        method: "POST",
        json: requestData
    }, function (error, response, json) {

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }
        else
        {
            console.log("====================");
            console.log("Total Premium: " + json.responseDataPayload.Quote.TotalPremium);
            console.log("====================");
        }

        assert.equal(json.exception, null);

        assert.end();
    });
});

/*
 *   generateTempQuote
 */
test('generateTempQuote', function (assert) {

    //console.log("Retrieving generateTempQuote response");

    var requestData = {"SessionId":SessionId,"UpdateQuoteDataArray":{"AdditionalAmountsOfInsurance":"2500","OptionCoverageB":"1000","OptionCoverageC":"7000","OptionCoverageD":"3000","PersonalPropertyReplacementCost":"100","EarthquakeCoverage":"200","IdentityFraudCoverage":"0","WaterBackupCoverage":"0","JewelrySpecialLimits":"1500","MechanicalBreakdownCoverage":"200","LossAssessmentCoverage":"1000","IncreasedOrdinanceLimit":"1000","AllOtherPerilsDeductible":"25","CoverageE":"300000","CoverageF":"1000","PersonalInjuryCoverage":"200"}};

    request({
        url: host+'/Iqs/api.php/generateTempQuote',
        method: "POST",
        json: requestData
    }, function (error, response, json) {

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }
        else
        {
            console.log("====================");
            console.log("Total Premium: " + json.responseDataPayload.Quote.TotalPremium);
            console.log("====================");
        }

        assert.equal(json.exception, null);

        assert.end();
    });
});


/*
 *   recallQuote
 */
test('recallQuote', function (assert) {

    //console.log("Retrieving recallQuote response");

    request({
        url: host+'/Iqs/api.php/recallQuote/'+quoteId+'/'+zipCode,
        method: "GET",
        json: true
    }, function (error, response, json) {

        if (json.exception) {
            console.log(json.exception.exceptionMessage);
        }

        assert.equal(json.exception, null);

        assert.end();
    });
});