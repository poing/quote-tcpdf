#!/usr/bin/php
<?php
/* --------------------------------------------------------
-----------------------------------------------------------

   Global TaNK : Proprietary & Confidential

-----------------------------------------------------------

 FileName: add_font.php
 Function: Add Fonts to TCPDF
 Verison:  0.1
 
 Author: Brian LaVallee  
 E-Mail: b.lavallee@globaltank.jp
 E-Mail: blavalleebvn@gmail.com
 
 Purpose: Simplify adding Japanese Fonts to TCPDF
 Usage: ./add_font.php /path_to/font.ttf /path_to/font.ttf

 Notes: None
 
-----------------------------------------------------------
-------------------------------------------------------- */

// Just tired of running into TZ error.
date_default_timezone_set('Asia/Tokyo');

// --------------------------------------------------------
// Load the TCPDF Library - Taken from TCPDF examples

// Include the main TCPDF library (search the library on the following directories).
$tcpdf_include_dirs = array(
 realpath('../tcpdf.php'), 
 realpath('../tcpdf/tcpdf.php'), 
 '/usr/share/php/tcpdf/tcpdf.php', 
 '/usr/share/tcpdf/tcpdf.php', 
 '/usr/share/php-tcpdf/tcpdf.php', 
 '/var/www/tcpdf/tcpdf.php', 
 '/var/www/html/tcpdf/tcpdf.php', 
 '/usr/local/apache2/htdocs/tcpdf/tcpdf.php'
);

// Attempt to load from each possible path.
foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
 if (@file_exists($tcpdf_include_path)) {
  require_once($tcpdf_include_path);
  break;
 }
}



// --------------------------------------------------------
// Add Font(s) from CLI

// for testing
//var_dump($argv);

// Check for minimum of one (1) command-line argument
if (isset($argv[1])) {

// Loop through command-line arguments
 for($i = 1; $i < $argc; $i++) {

  // Add the font using TCPDF::addTTFfont()
  $result = TCPDF::addTTFfont($argv[$i], 'TrueTypeUnicode', '', 32);
  
  // Show name of added font -or- error
  echo $result ? "Added " . $result . " to TCPDF.\n" : "ERROR: Verify that " . $argv[$i] . " is a TrueTypeFont.\n";

 }; // end loop

} else {

// Show usage when no command-line arguments provided
echo "Usage: ./add_font FONT_FILE [FONT_FILE] ..\n";

}; // end if

// END

?>
