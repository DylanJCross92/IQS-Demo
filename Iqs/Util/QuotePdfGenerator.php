<?php
/**
 * QuotePdfGenerator
 *
 * Generates a PDF based on the quote
 *
 * @package SageSure\Iqs
 * @author  Scott D. Rackliff (scottr@owlsheadsolutions.com)
 * @since   0.6.0
 */

namespace Iqs\Util {
    use Iqs\Exception\IqsException;
    use Iqs\Cnst\InformationCode;


    class QuotePdfGenerator {

        private $dompdf;
        private $pathToPdfTemplate;
        public function __construct($pathToPdfTemplate){
            $this->pathToPdfTemplate = $pathToPdfTemplate;
            $this->dompdf = new \DOMPDF();
        }
		
		private function replaceTags($find, $replace, $string)
		{
			$string = str_replace("<!".$find."!>", $replace, $string);
			
			return $string;	
		}
		
		private $currencyTerms = array("TotalPremium", "CoverageA", "ReplacementCostBuilding", "ReplacementCostRounded", "CoverageB", "CoverageC", "CoverageD", "CoverageE", "CoverageL", "CoverageF", "CoverageM", "IdentityFraudCoverage", "JewelrySpecialLimits", "WaterBackupCoverage", "LossAssessmentCoverage", "AllOtherPerilsDeductible");
		private $YesNoTerms = array("PersonalInjuryCoverage", "MechanicalBreakdownCoverage", "EarthquakeCoverage");
		
		private function format_currency($string)
		{
			return "$".number_format($string);	
		}
		
		private function convertToYN($string)
		{
			$return = "";
			
			if($string == 100)
			{
				$return = "Yes";
			}
			else if($string == 200)
			{
				$return = "No";	
			}
			
			return $return;
		}
		
        public function generateQuotePdf($quoteFields, $quoteId){

            //CRU doesn't show the prefix to customers
            $shortQuoteId = strstr($quoteId,"-");
            $shortQuoteId= ltrim ($shortQuoteId,'-');
            $rawHtml = file_get_contents(urldecode($this->pathToPdfTemplate));
			
            if(!$rawHtml){
                throw new IqsException(InformationCode::$SYS_QUOTEPDF_INVALID_TEMPLATE_PATH);
            }
			
			/*
				Below should NOT be removed
				
			*/
			
			if($quoteFields["InsuredFirstName"])
			{
				$firstName = $quoteFields["InsuredFirstName"];
			}
			else if($quoteFields["ApplicantFirstName"])
			{
				$firstName = $quoteFields["ApplicantFirstName"];
			}
			
			if($quoteFields["InsuredLastName"])
			{
				$lastName = $quoteFields["InsuredLastName"];
			}
			else if($quoteFields["ApplicantLastName"])
			{
				$lastName = $quoteFields["ApplicantLastName"];
			}
			
			$address1 = $quoteFields["PropertyStreetNumber"]." ".$quoteFields["PropertyStreetName"]." ".$quoteFields["PropertyAddressLine2"];
			$address2 = $quoteFields["PropertyCity"]." ".$quoteFields["PropertyState"]." ".$quoteFields["PropertyZipCode"];
			
			/*
				Above should NOT be removed
				
			*/
			
			
			/*
				Below should be removed and added to DB
				
			*/
			
			
			$AdditionalAmountsOfInsurance = $quoteFields['AdditionalAmountsOfInsurance'];

			if($AdditionalAmountsOfInsurance == 0)
			{
				$AdditionalAmountsOfInsurance = "0% - Excluded";
			}
			else if($AdditionalAmountsOfInsurance == 2500)
			{
				$AdditionalAmountsOfInsurance = "25%";	
			}
			else if($AdditionalAmountsOfInsurance == 5000)
			{
				$AdditionalAmountsOfInsurance = "50%";	
			}
			
			$IncreasedOrdinanceLimit = $quoteFields['IncreasedOrdinanceLimit'];
		
			if($IncreasedOrdinanceLimit == 1000)
			{
				$IncreasedOrdinanceLimit = "10%";
			}
			else if($IncreasedOrdinanceLimit == 2500)
			{
				$IncreasedOrdinanceLimit = "25%";	
			}
			else if($IncreasedOrdinanceLimit == 5000)
			{
				$IncreasedOrdinanceLimit = "50%";	
			}
			
			$AllOtherPerilsDeductible = $quoteFields['AllOtherPerilsDeductible'];
			
			if($AllOtherPerilsDeductible == 5)
			{
				$AllOtherPerilsDeductible = 500;
			}
			else if($AllOtherPerilsDeductible == 10)
			{
				$AllOtherPerilsDeductible = 1000;
			}
			else if($AllOtherPerilsDeductible == 25)
			{
				$AllOtherPerilsDeductible = 2500;	
			}
			else if($AllOtherPerilsDeductible == 50)
			{
				$AllOtherPerilsDeductible = 5000;
			}
			
			if(array_key_exists('PersonalPropertyReplacementCost', $quoteFields))
			{
			
				$PersonalPropertyReplacementCost = $quoteFields['PersonalPropertyReplacementCost'];
			
				if($PersonalPropertyReplacementCost == 200)
				{
					$PersonalPropertyReplacementCost = "Actual Cash Value";
				}
				else if($PersonalPropertyReplacementCost == 100)
				{
					$PersonalPropertyReplacementCost = "Replacement Value";	
				}
			
			}

            if(array_key_exists('TheftCoverage', $quoteFields))
            {

                $TheftCoverage = $quoteFields['TheftCoverage'];

                if($TheftCoverage == 1)
                {
                    $TheftCoverage = "Excluded";
                }
                else if($TheftCoverage == 100)
                {
                    $TheftCoverage = "On Premises Only";
                }
                else if($TheftCoverage == 200)
                {
                    $TheftCoverage = "On and Off Premises";
                }

            }


			if(array_key_exists('HurricaneDeductible', $quoteFields))
			{
				$HurricaneDeductible = $quoteFields['HurricaneDeductible'];

				if($HurricaneDeductible == 200) {
					$HurricaneDeductible = "2%";
				}
				else if($HurricaneDeductible == 300) {
					$HurricaneDeductible = "3%";
				}
				else if($HurricaneDeductible == 400) {
					$HurricaneDeductible = "4%";
				}
				else if($HurricaneDeductible == 500) {
					$HurricaneDeductible = "5%";
				}
			}

			if(array_key_exists('WindHailDeductible', $quoteFields))
			{
				$WindHailDeductible = $quoteFields['WindHailDeductible'];

				if($WindHailDeductible == 200) {
					$WindHailDeductible = "2%";
				}
                else if($WindHailDeductible == 300) {
                    $WindHailDeductible = "3%";
                }
				else if($WindHailDeductible == 400) {
					$WindHailDeductible = "4%";
				}
				else if($WindHailDeductible == 500) {
					$WindHailDeductible = "5%";
				}
			}



			
			/*
				Above should be removed and added to DB
				
			*/
			
			if(preg_match_all("/\<!(\w+?)\!>/siU", $rawHtml, $matches)) 
			{ 
				foreach($matches[1] as $termName) 
				{ 
					$replacement = "";
					
					if(array_key_exists($termName, $quoteFields))
					{
						$replacement = $quoteFields[$termName];
					}
					
					if($termName == "QuoteID")
					{
						$replacement = $shortQuoteId;
					}
					if($termName == "FullName")
					{
						$replacement = $firstName." ".$lastName;
					}
					if($termName == "Address1")
					{
						$replacement = $address1;
					}
					if($termName == "Address2")
					{
						$replacement = $address2;
					}
					
					if($termName == "CoverageC")
					{
						$replacement = ceil($quoteFields['CoverageC'] / 100) * 100;
					}
					/*
						Below should be moved to Database/use another method
					*/
					
					if($termName == "AdditionalAmountsOfInsurance")
					{
						$replacement = $AdditionalAmountsOfInsurance;	
					}
					
					if($termName == "IncreasedOrdinanceLimit")
					{
						$replacement = $IncreasedOrdinanceLimit;
					}
					
					if($termName == "AllOtherPerilsDeductible")
					{
						$replacement = $AllOtherPerilsDeductible;
					}
					
					if($termName == "PersonalPropertyReplacementCost")
					{
						$replacement = $PersonalPropertyReplacementCost;
					}

                    if($termName == "TheftCoverage")
                    {
                        $replacement = $TheftCoverage;
                    }

					if($termName == "HurricaneDeductible")
					{
						$replacement = $HurricaneDeductible;
					}

					if($termName == "WindHailDeductible")
					{
						$replacement = $WindHailDeductible;
					}
					
					/*
						Above should be moved to Database/use another method
					*/
					
					if(in_array($termName, $this->currencyTerms))
					{
						$replacement = $this->format_currency($replacement);
					}
					
					if(in_array($termName, $this->YesNoTerms))
					{
						$replacement = $this->convertToYN($replacement);
					}
					
					$rawHtml = $this->replaceTags($termName, $replacement, $rawHtml);
				 } 
			 }
			 
			 
            $this->dompdf->load_html($rawHtml);
            $this->dompdf->render();
            //$this->dompdf->stream('quote.pdf', array('Attachment'=>1));
            return $this->dompdf->output();


        }
    }

	
}


?>