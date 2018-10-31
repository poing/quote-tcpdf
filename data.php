<?php

//$test = array(1,2,3,4,5,6,7,8);

$table_rows = rand(3,7);

$quote_id = uniqid();
//$link_url = 'http://192.168.15.30/qgen/trunk/quote_v3.php?qid='.$quote_id;
$link_url = $quote_id;

//$install_cost = rand(10,9999999)*100;
//$monthly_cost = rand(10,999999)*100;

$serv['en'] = "Softbank Telecom Otoku Line";
$dlvr['en'] = "3 Weeks From Sunday";
$pmnt['en'] = "Months before activation";
$expr['en'] = date("Y/m/d", strtotime("+24 day"));

$serv['ja'] = "国際トールフリーサービス";
$dlvr['ja'] = "発注から3週間";
$pmnt['ja'] = "現行と同じ";
$expr['ja'] = $expr['en'];

$cmpy['en'][0] = "International Really Long Company Name Industries LLC";
$cmpy['ja'][0] = "本当に長い会社名国際システムサービス会社名国際株式会社";
$cmpy['en'][1] = "Company Co.";
$cmpy['ja'][1] = "本式会社";

$sign['en'] = 'Please sign or stamp with your company seal on the affixed request form, make a copy for your records, and then return the original by regular mail or by FAX.';
$sign['ja'] = '本見積書をご熟読の上、署名欄に署名・捺印後、弊社宛にご返送くださいますようお願い申し上げます。念のため、原本はコピーして保管してください。';
  

$tableAlign['header'] = Array (
	'C',
	'C',
	'C',
	'C',
	'C',
	'C',
	'C'
);
$tableAlign['content'] = Array (
	'C',
	'L',
	'C',
	'C',
	'R',
	'R',
	'L'
);


$tableWidth = Array(7,85,7,7,20,20,40);


$test = array(

array(1,2,3,4,5),
array(6,7,8,9,10),
array(11,12,13,14,'this is a lonnnnnggg statement'),
array(16,17,18,19,20),
array(21,22,23,24,25)

);

$user['en'] = "Yukari Haruzono";
$user['ja'] = "春園　ゆかり";

// -----------------------------------------------------------------------------
// Set placement for the company hanko
// The hanko is placed TWICE, once below address, once above.
//
// - set random position
// - 166~178 / 40~52 to cover en and jp company names.
// - -1 ~ 1 degree rotation

$hanko_align = rand(0,5)-3;
//$hanko_align = 3;
//$hanko_horz = 168;
$hanko_horz = rand(1420,1680)*0.1;
//$hanko_vert = 44;
$hanko_vert = rand(400,440)*0.1;

$hanko_image=rand(1,8);

// -----------------------------------------------------------------------------



//print_r($test);
?>
