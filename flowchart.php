<?php
/* --------------------------------------------------------
-----------------------------------------------------------

   Proprietary & Confidential

-----------------------------------------------------------

 FileName: quote_v2.php
 Function: Create PDF Quotes
 Verison:  0.2
 
 Author: Brian LaVallee  
 E-Mail: b.lavallee@invite-comm.jp
 E-Mail: blavalleebvn@gmail.com
 
 Purpose: Create English and Japanese Quotes
 Usage: N/A

 Notes: Breaking this version into functions.
 
-----------------------------------------------------------
-------------------------------------------------------- */

// Include the main TCPDF library (search for installation path).
// Also loads config/tcpdf_config_alt.php
require_once('include/tcpdf_include.php');

require_once('include/functions.php');


// ***************************************************************************** 
// TEST DATA

include('data.php'); // Load Testing Data

// ***************************************************************************** 

// -----------------------------------------------------------------------------
// Load Language Specific Values

include_once('fixed.php');

// -----------------------------------------------------------------------------
// Yen Setting
// NEED TO FIX - Does not show yen when language is set to english

setlocale(LC_MONETARY, "ja_JP.UTF-8");

// -----------------------------------------------------------------------------
// Handle TZ issue caused by improper php.ini settings.

date_default_timezone_set('Asia/Tokyo');


// -----------------------------------------------------------------------------
// Check language flags

if(isset($_GET['lang'])) {
 $lang = $_GET['lang'];
} else {
 $lang = 'en';
};

// ***************************************************************************** 
// ***************************************************************************** 
// -----------------------------------------------------------------------------
// create new PDF document

$pdf = new GTPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// -----------------------------------------------------------------------------
// set document information

$pdf->gt_defaults();

// -----------------------------------------------------------------------------
// Add First page

$pdf->AddPage('P','A4');

// ***************************************************************************** 
//  BEGIN CONTENT
// ***************************************************************************** 

// -----------------------------------------------------------------------------
// set text shadow effect

$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.1, 'depth_h'=>0.1, 'color'=>array(180,180,180), 'opacity'=>1, 'blend_mode'=>'Normal'));

$pdf->StarPolygon(160, 230, 15, 10, 3);



// $pdf->Line(5+40/2, 30+30/2, 50+40/2, 30+30/2);

// Rounded rectangle
// $pdf->Text(5, 20, 'Rounded rectangle examples');
 $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
/* $pdf->RoundedRect(5, 30, 40, 30, 1.50, '1111', 'DF');
$pdf->RoundedRect(50, 30, 40, 30, 6.50, '1011');
$pdf->RoundedRect(95, 30, 40, 30, 10.0, '1111', null, $style6);
$pdf->RoundedRect(140, 30, 40, 30, 8.0, '0101', 'DF', $style6, array(200, 200, 200));
*/


$pdf->Line(5, 80, 80, 80);

// *** TEST DATA ***
$ml=Array(1,2,3,1,1,1,1);

// Number of boxes to show (for testing)
$boxes=count($ml);

$pw=220; 		// Page Width
$pm=20;  		// Page Margin
$ps=$pw-$pm;	// Printable Space

$span=$ps/($boxes*1.2);		// Reserve Space for ALL boxes

$vstart=40;

// Boxes should be no larger than 40 wide
if ($span > 40) {
  $span=40;
}

$bw=$span;		// Box Width
$bh=$span*0.66;	// Box Hieght

// Count max number of rows
$vert=max($ml);
$vspace=($vert*($bh+20))-20;



// Spacing of the boxes
//  (Remaining Space / (Empty Space / 2 )) + Box Width
$space=$bw+((($pw-($span*$boxes))/($boxes+1))/2);

for ($x=1; $x<=$boxes; $x++) {

// vertical center
for ($y=1; $y<=$ml[$x-1]; $y++) {

$midpoint=($vspace/$vert)-($bh/2);		// Middle Point
$midstart=$midpoint-((($bh+20)*$ml[$x-1])/2);
$vpoint=$midstart+(($bh+20)*$y);


$pdf->RoundedRect(($pm/2)+(($space*$x)-$bw), 10+$vpoint, $bw, $bh, 1.50, '1111', 'DF', $style6, array(204, 220, 204));


}

}

// Look and building an array of connection information.  Define the center points and draw the lines after.

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('/tmp/example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
