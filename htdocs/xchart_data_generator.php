<?php
set_time_limit(0);
error_reporting(0);

$filename1 = "C:/Users/Adam/Documents/HN2015/pressure_data/rotary_president/0209-0211.txt";
$filename2 = "C:/Users/Adam/Documents/HN2015/pressure_data/freds1/0126-0210.txt";
define("OUT",false);
$outfile = "C:/Users/Adam/Documents/HN2015/pressure_data/rotary_president/0209-0211_processed.txt";

if(OUT) $ofp = fopen($outfile,"w");
if(OUT) fputs($ofp,"Fecha y Hora,Presión\n");
if(OUT) fputs($ofp,"(año/mes/día hora:minuto:segundo),(PSI)\n");
$output = array();
$output["xScale"] = "time";
$output["yScale"] = "linear";
$output["type"] = "line";
$output["main"] = array();
$output["main"][0]["className"] = "color1";
$output["main"][0]["data"] = array();
$output["comp"][0]["className"] = "color7";
$output["comp"][0]["type"] = "line";
$output["comp"][0]["data"] = array();
$handle = fopen($filename1, "r");
$first_date = null; $first_date_set=false;
$last_date = null;
$any_error = false;
$i=0;
if ($handle) {
    while (($line = fgets($handle)) !== false) {
		if($i%10==0) {
			$error = true;
			$la = explode(",",$line);
			$date = $la[0];
			$data = explode("P", trim($la[1]));
			$pressure = $data[0];
			if(isset($date) && isset($pressure)) {
				if(strpos($date,"2015/")!==false) {
					$fd = date("Y-m-d G:i:s",strtotime($date));
				}
				else
					$fd = date("Y-m-d G:i:s",strtotime("2015/".$date));
				$year = date("Y",strtotime($fd));
				if($year == 2015) {
					if(is_numeric(trim($pressure))) {
						$error = false;
						if(OUT) fputs($ofp, $fd.",".trim($pressure)."\n"); // write the data in the opened file
						//print($fd.",".trim($pressure)."\n");
						$output["main"][0]["data"][]=array("x"=>$fd,"y"=>floatval(trim($pressure)));
						// To set used dates
						if(!$first_date_set) {$first_date = $fd; $first_date_set = true;}
						$last_date = $fd;
					}
				}
			}
			if($error) {
				//echo "<b>".$line."</b> (".$i.")<br>";
				$any_error = true;
			}
		}
		$i++;
    }
} else {
    echo "error opening";
} 
fclose($handle);

$handle = fopen($filename2, "r");
$first_date = null; $first_date_set=false;
$last_date = null;
$any_error = false;
$i=0;
if ($handle) {
    while (($line = fgets($handle)) !== false) {
		if($i%100==0) {
			$error = true;
			$la = explode(",",$line);
			$date = $la[0];
			$data = explode("P", trim($la[1]));
			$pressure = $data[0];
			if(isset($date) && isset($pressure)) {
				if(strpos($date,"2015/")!==false) {
					$fd = date("Y-m-d G:i:s",strtotime($date));
				}
				else
					$fd = date("Y-m-d G:i:s",strtotime("2015/".$date));
				$year = date("Y",strtotime($fd));
				if($year == 2015) {
					if(is_numeric(trim($pressure))) {
						$error = false;
						if(OUT) fputs($ofp, $fd.",".trim($pressure)."\n"); // write the data in the opened file
						//print($fd.",".trim($pressure)."\n");
						$output["comp"][0]["data"][]=array("x"=>$fd,"y"=>floatval(trim($pressure)));
						// To set used dates
						if(!$first_date_set) {$first_date = $fd; $first_date_set = true;}
						$last_date = $fd;
					}
				}
			}
			if($error) {
				//echo "<b>".$line."</b> (".$i.")<br>";
				$any_error = true;
			}
		}
		$i++;
    }
} else {
    echo "error opening";
} 
fclose($handle);
if(OUT) fclose($ofp);
/*if(!$any_error) {
	echo "<hr><h1>No error</h1><hr>";
}
	echo "<hr>" . $i . " rows processed<hr>";
	echo "<hr>From: " . $first_date . " to " . $last_date . "<hr>";*/
print("var data = ");
print(json_encode($output));
print(";");