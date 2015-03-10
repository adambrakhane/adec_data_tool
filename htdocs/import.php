<?php

function num2alpha($n)
{
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r;
    return $r;
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

$fn = dirname(__FILE__) . "/excel_forms/Francisco_2015_11FEB15_021215_revised.xlsx";
$objPHPExcel = PHPExcel_IOFactory::load($fn);

$raw_data = array();

echo date('H:i:s') , "Get some data<br>\n";
echo "<table border=\"1\">\n";
for ($row = 1; $row < 200; $row++) {
	echo "<tr>";
	for ($col=1; $col<19; $col++) {
		$cellValue = $objPHPExcel->getSheet(2)->getCell(num2alpha($col-1).$row)->getFormattedValue();
		echo "<td>" . $cellValue . "</td>";
		//$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, 'Test value');
		$raw_data[$row][$col] = $cellValue;
	}
	echo "</tr>\n";
}
echo "</table>\n";
for($i=1; $i<10;$i++) {
	for($j=10; $j<19;$j++) {
		$cellValue = $objPHPExcel->getSheet(2)->getCell(num2alpha($j-1).$i)->getFormattedValue();
		if($cellValue=="LAT") {
			$data_location["LAT"]["col"] = $j;
			$data_location["LAT"]["row"] = $i+1; // Starts row after title
		}
		if($cellValue=="LON") {
			$data_location["LON"]["col"] = $j;
			$data_location["LON"]["row"] = $i+1; // Starts row after title
		}
	}
}

function get_valid_cols($key,$data_location,$raw_data) {
	$row = $data_location[$key]["row"];
	$col = $data_location[$key]["col"];
	$val = $raw_data[$row][$col];
	while ($val>0) {
		echo $val."<br>";
		$val = $raw_data[$row++][$col]; // Next!
	}
}

echo "<pre>"; print_r($data_location); echo "</pre>";
get_valid_cols("LON",$data_location,$raw_data);
?>
