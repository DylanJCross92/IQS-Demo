<?php
	require_once 'dompdf/autoload.inc.php';
	
	// reference the Dompdf namespace
	use Dompdf\Dompdf;
	
	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	
	$zipcodes = array(
		array("zipcode" => "02128", "date_created" => "1468521169"),
		array("zipcode" => "02124", "date_created" => "1468522269"),
		array("zipcode" => "02123", "date_created" => "1468523269"),
		array("zipcode" => "02122", "date_created" => "1468524369"),
		array("zipcode" => "02121", "date_created" => "1468525769"),
		array("zipcode" => "02120", "date_created" => "1468526569")
	);
	
	
	$html = '
	<link type="text/css" href="css/pdfstyles.css" rel="stylesheet" />
	<h1>Whitelisted Zipcodes</h1>
	<div class="generated">Report Generated on '.date("F j, Y, g:i a").'</div>
	';
	
	$html .= '
	<div class="table zipcode-whitelisting">
		<div class="row header" style="background: red;">
		  <div class="cell">
			#
		  </div>
		  <div class="cell zipcode">
			Zipcode
		  </div>
		  <div class="cell">
			Date Added
		  </div>
		  <div class="cell">
			Status
		  </div>
		</div>
		';
		$i = 1;
		foreach($zipcodes as $zipcode) {
			$number = $i++;
			
			$html .= '
		
		<div class="row row-'.$number.'">
			<div class="cell number">'.$number.'</div>
			<div class="cell zipcode">'.$zipcode["zipcode"].'</div>
			<div class="cell date">'.date("F j, Y", $zipcode["date_created"]).'</div>
			<div class="cell status">Active</div>
		</div>';
			
		}
            
    $html .= '</div>';
	
	
	$dompdf->loadHtml($html);
	
	// (Optional) Setup the paper size and orientation
	$dompdf->setPaper('A4', 'portrait');
	
	// Render the HTML as PDF
	$dompdf->render();
	
	// Output the generated PDF to Browser
	$dompdf->stream('my.pdf',array('Attachment'=>0));

?>