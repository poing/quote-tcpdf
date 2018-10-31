<?php

class SYSPDF extends TCPDF {





public function noteTable($result,$lang) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data
	
	$this->SetFillColor(235, 235, 235);
	$this->SetFont($formatting[$lang]['font'], '', 8, false);

	$this->MultiCell(0, 0, $formatting[$lang]['notes'], 'B', 'C', 1, 0, '', '', true);
	$this->Ln();
	
	//$this->SetFillColor(255, 255, 255);

	$note_number = 1;
	while($row = $result->fetch_assoc()) {

	// Dynamic Cell Height
	// Move this to function?
	$row_height = $this->getStringHeight(0,$row["note"]);

	// Next Page w/o Header
// ------

	// Check Amount of Space Left on Page
	if ($this->GetY() + $row_height > 276)
		{

		// Write Continued on Next Page
    	$this->Ln(0.5);

    	$this->SetX(12);
		
    	//$this->SetFont($formatting[$lang]['font'], '', 8, false);
		$this->MultiCell(array_sum($width), 0, $formatting[$lang]['next_page'], 'T', 'L', 0, 0, '', '', true);

		// Create New Page
		$this->AddPage('P','A4');
		$this->Ln(1.2);
		$this->MultiCell(0, 0, $formatting[$lang]['more_notes'], 'B', 'C', 1, 0, '', '', true);
		$this->Ln();

	}

// ------
//$this->TableItem($row["note"], 'L', '', $row_height, 12, $ii);


	



    //$this->SetLineWidth(0.05); 
	//$this->SetFont($formatting[$lang]['font'], '', 9.5, false);
	$this->MultiCell(0, 0, $note_number . ") " . $row["note"], '', 'L', 1, 0, '', '', true);
	
	$this->Ln();
	
	$note_number++;
}    
    return;


}


// ***************************************************************************** 
// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^
// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^
// ***************************************************************************** 
// -----------------------------------------------------------------------------
// Totals Block

public function itemsTable($result,$lang,$type) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data

	$headertext = Array(
		'No.',
		$formatting[$lang][$type.'_item'],
		$formatting[$lang]['qty'],
		$formatting[$lang]['unit'],
		$formatting[$lang]['price'],
		$formatting[$lang]['amount'],
		$formatting[$lang]['note']
	);
	
 	// Next Page w/ Header
	$this->nextPage($tableWidth,$lang,$this->getStringHeight(40,$formatting[$lang]['note']));


	$this->TableHeader($headertext, $tableAlign['header'], $tableWidth, 12);

	$ii = 1;

	while($row = $result->fetch_assoc()) {

	// Dynamic Cell Height
	// Move this to function?
	$row_height = $this->getStringHeight(40,$row["note"]);
	if ($this->getStringHeight(85,$row["item"]) > $row_height) {
		$row_height = $this->getStringHeight(85,$row["item"]);
	}
	

	// Next Page w/ Header
	$this->nextPage($tableWidth,$lang,$row_height,$headertext,$tableAlign['header']);

	$itemtext = Array(
		str_pad($ii,2,'0',STR_PAD_LEFT),
		$row["item"],
		str_pad($row["qty"],3,'0',STR_PAD_LEFT),
		$row["unit"],
		money_format('%.0n', $row["price"]),
		money_format('%.0n', $row["price"]*$row["qty"]),
		$row["note"]	
	);

$this->TableItem($itemtext, $tableAlign['content'], $tableWidth, $row_height, 12, $ii);

$ii++; 

$runningTotal += $row["price"]*$row["qty"];

}

// ** TAX **

//$tax = ROUND(($runningTotal*0.08)-0.5,0,PHP_ROUND_HALF_DOWN);

$tax = $this->japanTax($runningTotal);

$itemtext = Array(
	str_pad($ii,2,'0',STR_PAD_LEFT),
	"Consumption Tax",
	str_pad(1,3,'0',STR_PAD_LEFT),
	"8%",
	money_format('%.0n', $runningTotal),
	money_format('%.0n', $tax),
	//"$runningTotal * 0.08"
	""	
);

 $row_height = $this->getStringHeight(40,$tax);
 $this->nextPage($tableWidth,$lang,$row_height,$headertext,$tableAlign['header']);
 $this->TableItem($itemtext, $tableAlign['content'], $tableWidth, $row_height, 12, $ii);

// ** TAX **


// ***************************************************************************** 

// Next Page w/o Header
$this->nextPage($tableWidth,$lang,12);


$this->totalsBlock($runningTotal,$lang,$type);
//$this->ln(2);

}

// ***************************************************************************** 
// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^
// ~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^^^^~~~~~^^
// ***************************************************************************** 


public function calcTotal($result) {

	$runningTotal = 0;
	while($row = $result->fetch_assoc()) {
		$runningTotal += $row["price"]*$row["qty"];
	}
	
	// Reset Result Set to 0 for re-use
	$result->data_seek(0);
	
	$tax = $this->japanTax($runningTotal);
	
	return $runningTotal + $tax;

}

// -----------------------------------------------------------------------------
// Totals Block

public function totalsBlock($runningTotal,$lang,$type) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data

	// Set Fill Color
	$this->SetFillColor(203, 203, 203);
	
	// Set Placement
	$this->SetX(12);
	$this->SetFont($formatting[$lang]['font'], '', 10, false);

	$this->MultiCell(110,10,$formatting[$lang][$type.'_cost'],1,'C',1,0,'','',true,0,false,1,10,'M');

	$this->MultiCell(36, 10, money_format('%.0n', $runningTotal+$this->japanTax($runningTotal)),1,'R',0,0,'','',true,0,false,1,10,'M');

	$this->MultiCell(40, 10, '', 'LT', 'C', 0, 0, '', '', true);

	$this->Ln();

}
// EN Totals Block

// -----------------------------------------------------------------------------
// Japan Tax Calculation (Round Down)

public function japanTax($amount) {

    if ($amount > 0) {
	 $tax = ROUND(($amount*0.08)-0.5,0);
	} else {
	 $tax = 0;
	}
    
    return $tax;
    
}
// END Japan Tax Calculation

// -----------------------------------------------------------------------------
// Next Page

public function nextPage($width,$lang,$nextcell,$headertext,$headeralign) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data

	// Check Amount of Space Left on Page
	if ($this->GetY() + $nextcell > 276)
		{

		// Write Continued on Next Page
    	$this->SetX(12);
		$this->MultiCell(array_sum($width), 0, $formatting[$lang]['next_page'], 'T', 'L', 0, 0, '', '', true);

		// Create New Page
		$this->AddPage('P','A4');
		$this->Ln(1.2);
	
		// Add Header if Necessary
		if (isset($headertext)) {
	
			$this->TableHeader($headertext, $headeralign, $width, 12);

		}
	}

	return;

}
// END Next Page


// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
        // Page footer
        public function xxxFooter() {
        
        include('fixed.php');
		include('data.php'); // Load Testing Data
        
        // Position at 15 mm from bottom
        $this->SetY(-15);
        
        // Set font
        //   $this->SetFont('helvetica', 'I', 8);
        // Page number
                $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
//$this->Cell(-15, 10, 'aaabbbccc', 0, false, 'L', 0, '', 0, false, 'T', 'M');  
//$this->Arrow(50,210,20,210,1,5,30);         
$foo=rand(0+10,210-10-50);

$this->write1DBarcode($link_url, 'C39', 10, 293, 50, 6, 0.4, $style, 'N');
$this->write1DBarcode($this->PageNo(), 'EAN2', 170, 293, '', 6, 0.4, $style, 'N');

     
                }
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// MultiCell Table Header

public function TableHeader($headertext, $headeralign, $width, $left) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data
    
	$this->SetX($left);
    $this->SetLineWidth(0.1);

	
	$this->SetTextColor(0, 0, 0); 
	$this->SetFont($formatting[$lang]['font'], '', 6.5, false);

	//	$this->setTextShadow(array('enabled'=>false, 'depth_w'=>0, 'depth_h'=>0, 'color'=>false, 'opacity'=>1, 'blend_mode'=>'Normal'));

	$this->SetFillColor(203, 203, 203);

	if (is_array($headertext)) {

	foreach ($headertext as $i => $value) {
    	$this->MultiCell($width[$i], 2, $value, 1, $headeralign[$i], 1, 0, '', '', true);
	}
	
	} else {
	
		$this->MultiCell($width, 2, $headertext, 1, $headeralign, 1, 0, '', '', true);
	
	}
	
	$this->Ln();
    
    //return;
    
}
// END TableHeader

// -----------------------------------------------------------------------------
// MultiCell Item List

public function TableItem($text, $align, $width, $height, $left, $line) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data
    
	$this->SetX($left);
    $this->SetLineWidth(0.1);

	
	$this->SetFont($formatting[$lang]['font'], '', 6.5, false);
//	$this->setTextShadow(array('enabled'=>false, 'depth_w'=>0, 'depth_h'=>0, 'color'=>false, 'opacity'=>1, 'blend_mode'=>'Normal'));
	//$this->SetTextColor(255, 0, 0); 


	foreach ($text as $i => $value) {
	
	 if ($line % 2 == 0) {
	 	$this->SetFillColor(235, 235, 235);
	 	//$this->SetTextColor(255, 0, 0); 
	 } else {
	 	$this->SetFillColor(255, 255, 255);
	 	//$this->SetTextColor(0, 0, 255); 
	 }
    $this->SetLineWidth(0.05); 
	$this->MultiCell($width[$i], $height, $value, 'LR', $align[$i], 1, 0, '', '', true);

	
	}
	
	$this->Ln();
    
    return;
    
}
// END TableHeader

// -----------------------------------------------------------------------------
// Random Data For Testing & Formatting

public function generateRandomString($length = 10) {
    
    $characters = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $randomString;
    
}
// END generateRandomString

// -----------------------------------------------------------------------------
// Japanses Hanko

public function JapaneseHanko($hanko_align,$hanko_horz,$hanko_vert,$hanko_visable,$image) {

// Angle, Horizontal Position, Vertical Position, Alpha on/off

	$this->StartTransform();
	
	 if($hanko_visable==1){
	  $this->SetAlpha(0.7); // - not needed on bottom
	 }

	$this->Rotate($hanko_align,$hanko_horz,$hanko_vert);
	$this->Image('images/hanko/invite-'.$image.'.png',$hanko_horz,$hanko_vert,23,'');

	$this->StopTransform();

	return;

}
// END JapaneseHanko

// -----------------------------------------------------------------------------
// Defaults

public function gt_address($font,$company,$address,$created) {

	// Place Company Name - Large Font
	$this->setXY(112,45);
	$this->SetFont($font, '', 14, '', true);
	$this->MultiCell(0,0,$company,0,1,'L');
	
	// Place address information
	$this->setXY(112,51);
	$this->SetFont($font, '', 11, '', true);
	$this->MultiCell(0,0,$address,0,1,'L');

	// Place Issuer Information
	$this->setX(112);
	$this->MultiCell(0,0,$created,0,1,'L');

return;

}

public function quote_company($font,$company) {

	$this->SetFont($font, 'B', 16, '', true);

	$this->setXY(17,29);
	$this->MultiCell(106, 8, $company, 'B', 'C',0,0,'','',true,3,false,1,8,'M',true);

	return;
}

// -----------------------------------------------------------------------------
// Show Signature Block 

public function signature_block($y,$fixed_url,$style,$lang) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data	

	// set common parameters
	$start_block = 215;
	$line_length = 150;
	
	// insert new page if no space available on current page
	$this->setY($y);
	if ($y > $start_block-5)
		{       
		// create new page
		$this->AddPage('P','A4');
	}
	
	//$this->MultiCell(0,10,$sign[$lang],0,'L',0,1,'','',true,4);
	$this->SetFont('', '', 6, '', true);
	$this->MultiCell(0, 10, "◆ " . $formatting[$lang]['tc'], 0, 'L',0,0,'','',true,1,false,1,10,'M',true);	
	$this->Ln(2.3);
	$this->MultiCell(0, 10, $sign[$lang], 0, 'L',0,0,'','',true,1,false,1,10,'M',true);
	
	//$this->MultiCell(120,10,$sign[$lang],0,'L',0,1,'','',true,4,false,1,10);
	
	// show barcode with signature block, if not first page
	if ($this->GetPage() > 1)
	{       
		$this->write2DBarcode($fixed_url, 'QRCODE,M', 177, 262, 20, 20, $style, 'N');   
	}


	// Starting placement for signature block
	$this->setY($start_block);
	
	// draw four (4) lines
	for ($i = 1; $i <= 4; $i++) {
	    $this->SetLineWidth(0.05);
		$this->setX($this->GetX()+2);
		$this->MultiCell($line_length,15,'','B','C',0,1,'','',true,0,false,1,15,'M');
	}
	
	// prepare to show text
    $this->SetFont($formatting[$lang]['font'], '', 8, '', true);
	$this->SetFillColor(255, 255, 255);
	$this->SetTextColor(0, 0, 0); 
	$this->setY($start_block+7);
	
	$this->setTextShadow(array('enabled'=>false, 'depth_w'=>0, 'depth_h'=>0, 'color'=>false, 'opacity'=>1, 'blend_mode'=>'Normal'));
	
	$text_offset = 5;
	

	// Company Line
$this->MultiCell($this->GetStringWidth($formatting[$lang]['sign_company'])+$text_offset,15,$formatting[$lang]['sign_company'] . ':',0,'B',1,$lang=='en'?1:0,'','',true,0,false,1,15,'M');

	// Hanko Symbol (if Japanese)
	if (!($lang=='en')) {
		$this->setX($line_length+10);
		$this->MultiCell(0,15,'（印）',0,'B',1,1,'','',true,0,false,1,15,'M');	
	}

	// Signature Line
$this->MultiCell($this->GetStringWidth($formatting[$lang]['sign_auth'])+$text_offset,15,$formatting[$lang]['sign_auth'] . ':',0,'B',1,1,'','',true,0,false,1,15,'M');	

// printed name line
$this->MultiCell($this->GetStringWidth($formatting[$lang]['sign_name'])+$text_offset,15,$formatting[$lang]['sign_name'] . ':',0,'B',1,1,'','',true,0,false,1,15,'M');
	
// date line	
$this->MultiCell($this->GetStringWidth($formatting[$lang]['sign_date'])+$text_offset,15,$formatting[$lang]['sign_date'] . ':',0,'B',1,1,'','',true,0,false,1,15,'M');	

	
}

public function quote_summary($lang,$service,$delivery,$terms) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data

	$this->SetFont($formatting[$lang]['font'], 'B', 10, '', 0);

	$this->setY(40);

//$this->MultiCell(0, 0, 'This is a test');

	//$this->MultiCell(110, '', $formatting[$lang]['service_type'] . " " . $serv[$lang], 0, 'L',0,1,'','',true,0,0,0,0,'M');
	$this->MultiCell(110, '', $formatting[$lang]['service_type'] . " " . $service, 0, 'L',0,1,'','',true,0,0,0,0,'M');

	
	//$this->MultiCell(110, '', $formatting[$lang]['delivery_date'] . " " . $dlvr[$lang], 0, 'L',0,1,'','',true,0,false,1,8,'M');
	$this->MultiCell(110, '', $formatting[$lang]['delivery_date'] . " " . $delivery, 0, 'L',0,1,'','',true,0,false,1,8,'M');
	
	//$this->MultiCell(110, '', $formatting[$lang]['payment_term'] . " " . $pmnt[$lang], 0, 'L',0,1,'','',true,0,false,1,8,'M');
	$this->MultiCell(110, '', $formatting[$lang]['payment_term'] . " " . $terms, 0, 'L',0,1,'','',true,0,false,1,8,'M');
	
	
	$this->MultiCell(110, '', $formatting[$lang]['exiration_date'] . " " . $expr[$lang], '', 'L',0,1,'','',true,0,false,1,8,'M');

}

public function cost_summary($lang,$install_cost,$monthly_cost) {

	// include text translations
	include('fixed.php');
	include('data.php'); // Load Testing Data
	
	$this->setXY(12,70);
	$this->MultiCell(30, 8, $formatting[$lang]['initial_cost'], 0, 'L',0,0,'','',true,0,false,1,8,'M');
	$this->MultiCell(30, 8, money_format('%.0n', $install_cost),'B','R',0,0,'','',true,0,false,1,8,'M');
	$this->Ln();
	$this->setX(12);
	$this->MultiCell(30, 8, $formatting[$lang]['monthly_cost'], 0, 'L',0,0,'','',true,0,false,1,8,'M');
	$this->MultiCell(30, 8, money_format('%.0n', $monthly_cost),'B','R',0,0,'','',true,0,false,1,8,'M');
	
	Return;
	
}	

// -----------------------------------------------------------------------------
// Defaults

public function sys_defaults() {

	$this->SetCreator(PDF_CREATOR);
	$this->SetAuthor('INVITE Communications');
	$this->SetTitle('Quotation and Order Sheet');
	$this->SetSubject('Quote');
	$this->SetKeywords('INVITE, INVTIE Communications, Quote, Order');
	$this->SetProtection(Array("modify"));


// -----------------------------------------------------------------------------
// set default header data

	$this->SetHeaderData(GT_LOGO, PDF_HEADER_LOGO_WIDTH);

// -----------------------------------------------------------------------------
// , PDF_HEADER_STRING, array(0,64,255), array(0,64,128));

	$this->setFooterData();

// -----------------------------------------------------------------------------
	// set header and footer fonts
	$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', '25'));
	//$this->setHeaderFont(Array(ipagp, '', '20'));
	$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// -----------------------------------------------------------------------------
	// set default monospaced font
	$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// -----------------------------------------------------------------------------
	// set margins
	$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$this->SetHeaderMargin(PDF_MARGIN_HEADER);
	$this->SetFooterMargin(PDF_MARGIN_FOOTER);

// -----------------------------------------------------------------------------
	// set auto page breaks
	$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// -----------------------------------------------------------------------------
	// set image scale factor
	$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

// -----------------------------------------------------------------------------
	// set some language-dependent strings (optional)
	/*if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$this->setLanguageArray($l);
	}
	*/

// -----------------------------------------------------------------------------
	// set default font subsetting mode
	$this->setFontSubsetting(true);
	
	return;

}

// END DEFAULTS

}
// END CLASS

?>
