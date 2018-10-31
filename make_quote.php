<?php
/* --------------------------------------------------------
-----------------------------------------------------------

    ＩＮＶＩＴＥ Communications : Proprietary & Confidential

-----------------------------------------------------------

 FileName: quote_v2.php
 Function: Create PDF Quotes
 Verison:  0.2
 
 Author: Brian LaVallee  
 E-Mail: brian.lavallee@invite-comm.jp
 E-Mail: blavalleebvn@gmail.com
 
 Purpose: Create English and Japanese Quotes
 Usage: N/A

 Notes: Breaking this version into functions.
 
-----------------------------------------------------------
-------------------------------------------------------- */

// ***************************************************************************** 
//  SET VARIBLES
// ***************************************************************************** 

// -----------------------------------------------------------------------------

// Include the main TCPDF library (search for installation path).
// Also loads config/tcpdf_config_alt.php
require_once('include/tcpdf_include.php');

// Include Custom Functions
require_once('include/functions.php');

// ***************************************************************************** 
// TEST DATA - TEST DATA - TEST DATA - TEST DATA - TEST DATA - TEST DATA

include('data.php'); // Load Junk Data for Testing and Formating             

// TEST DATA - TEST DATA - TEST DATA - TEST DATA - TEST DATA - TEST DATA
// ***************************************************************************** 


// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^
// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^
// ***************************************************************************** 

$servername = "localhost";
$username = "user";
$password = "password";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

/* change character set to utf8 */
if (!$conn->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
}

$sql = "SELECT * FROM invoice WHERE install = 'Y'";
$sql2 = "SELECT * FROM invoice WHERE install = 'N'";
$sql3 = "SELECT * FROM notes";
$sql4 = "SELECT * FROM draft LIMIT 1";


$result = $conn->query($sql);
$result2 = $conn->query($sql2);
$result3 = $conn->query($sql3);
$result4 = $conn->query($sql4);

$draft = $result4->fetch_assoc();

$conn->close();

// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^


// -----------------------------------------------------------------------------
// Load Language Specific Values

include_once('fixed.php');

// -----------------------------------------------------------------------------
// Show Values in Japanese Yen

setlocale(LC_MONETARY, "ja_JP.UTF-8");

// -----------------------------------------------------------------------------
// Handle TZ issue caused by incorrect php.ini settings.

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

$pdf = new SYSPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// -----------------------------------------------------------------------------
// set document information

$pdf->sys_defaults();

// -----------------------------------------------------------------------------
// Add First page

$pdf->AddPage('P','A4');

// ***************************************************************************** 
//  BEGIN CONTENT
// ***************************************************************************** 

// -----------------------------------------------------------------------------
// set text shadow effect

$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.1, 'depth_h'=>0.1, 'color'=>array(180,180,180), 'opacity'=>1, 'blend_mode'=>'Normal'));


// -----------------------------------------------------------------------------
// TITLE

$pdf->setXY(30,8);
$pdf->SetFont($formatting[$lang]['font'], '', $formatting[$lang]['title_size'], '', true);
$pdf->MultiCell(0, 10, $formatting[$lang]['title'], 0, 'C', 0, 0, '', '', false);

// ---------------------------------------------------------
// Print Customer Company Name

//$pdf->quote_company($formatting[$lang]['font'],$cmpy[$lang][rand(0,1)]);
$pdf->quote_company($formatting[$lang]['font'],$draft["company"]);

// ---------------------------------------------------------
// Quote Summary

$pdf->quote_summary($lang,$draft["type"],$draft["delivery"],$draft["terms"]);

/*`company`
`type`
`delivery'
`terms`*/

// ---------------------------------------------------------
// cost Summary

$pdf->cost_summary($lang,$pdf->calcTotal($result),$pdf->calcTotal($result2));

// -----------------------------------------------------------------------------
// Issue Date

$pdf->setXY(130,30);
$pdf->SetFont($formatting[$lang]['font'], '', 9, '', true);
$pdf->MultiCell(0,0,$formatting[$lang]['issue'] . ' ' . date('Y/m/d'),0,1,'L');

// -----------------------------------------------------------------------------
// Quote ID

$pdf->setXY(130,35);
$pdf->SetFont($formatting[$lang]['font'], '', 9, '', true);
$pdf->MultiCell(0,0,$formatting[$lang]['quote'] . ' ' . strtoupper($quote_id),0,1,'L');

// -----------------------------------------------------------------------------
// set the barcode content and type
// NEED TO FIX BACK END - ONCE URL IS DEFINED

require_once('include/tcpdf_barcodes_2d_include.php');
$fixed_url = $link_url;
$pdf->write2DBarcode($link_url, 'QRCODE,M', 185, 29, 12, 12, $style, 'N');

// -----------------------------------------------------------------------------
// Insert Company Hanko - BOTTOM
// Full image on bottom, semi-transpearent on top.

$pdf->JapaneseHanko($hanko_align,$hanko_horz,$hanko_vert,0,$hanko_image);

// ---------------------------------------------------------
// Insert Company Address & Hanko

$pdf->gt_address($formatting[$lang]['font'],$formatting[$lang]['company'],$address[$lang],$formatting[$lang]['issuer'] . ' ' . $user[$lang]);

// ---------------------------------------------------------
// Insert Company Hanko - TOP

$pdf->JapaneseHanko($hanko_align,$hanko_horz,$hanko_vert,1,$hanko_image);

// ***************************************************************************** 
// ***************************************************************************** 

// No Shadow 
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0, 'depth_h'=>0, 'color'=>array(0,0,0), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Start Location
$pdf->setY(90);

// ***************************************************************************** 


$pdf->itemsTable($result,$lang,'initial');
$pdf->Ln(4);
$pdf->itemsTable($result2,$lang,'monthly');
$pdf->Ln(4);
$pdf->noteTable($result3,$lang);


// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^

//$foo = "at, velit. Pellentesque ultricies dignissim lacus. Aliquam rutrum lorem ac risus. Morbi metus. Vivamus euismod urna. Nullam lobortis quam a felis ullamcorper viverra. Maecenas iaculis aliquet diam. Sed diam";

/* $pdf->MultiCell(array_sum($width), 0, $foo, '', 'L', 0, 0, '', '', true);$pdf->Ln();
$pdf->MultiCell(array_sum($width), 0, $foo, '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(array_sum($width), 0, $foo, '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(array_sum($width), 0, $foo, '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(array_sum($width), 0, $foo, '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(array_sum($width), 0, $foo, '', 'L', 0, 0, '', '', true); */

/*while($row = $result3->fetch_assoc()) {

	$row_height = $pdf->getStringHeight(40,$row['note']);

	// Next Page w/o Header
	$pdf->nextPage(array_sum($width),$lang,$row_height);
	$pdf->MultiCell(0, 0, $foo, '', 'L', 0, 0, '', '', true);

} */

	//$pdf->MultiCell(0, 10, $foo, 0, 'L',0,0,'','',true,1,false,1,10,'M',true);$pdf->Ln();
	//$pdf->MultiCell(0, 10, $foo, 0, 'L',0,0,'','',true,1,false,1,10,'M',true);$pdf->Ln();


// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^

// ***************************************************************************** 
//   END CONTENT
// ***************************************************************************** 

// ---------------------------------------------------------
// Print Signature Block

$pdf->signature_block($pdf->GetY(),$fixed_url,$style,$lang);

// **** 282 LIMIT ****

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('/tmp/example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
