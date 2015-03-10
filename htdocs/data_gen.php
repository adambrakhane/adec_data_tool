<?php

include('./lib/nv_utility.php');

function pr($d) {echo "<pre>"; print_r($d); echo "</pre>";}


$colors[] = "#1F77B4";
$colors[] = "#FF7F0E";
$colors[] = "#2CA02C";
$colors[] = "#D62728";
$colors[] = "#8A0815";
$colors[] = "#3C088A";
$colors[] = "#8A3C08";

$colors[] = "#AEC7E8";
$colors[] = "#FFBB78";
$colors[] = "#98DF8A";
$colors[] = "#FF9896";
$colors[] = "#650DEA";
$colors[] = "#F6A067";

seeded_shuffle($colors,100); // Pseudo random shuffle


if(isset($_GET['get']) || isset($_GET['json'])) {
	$jo = (isset($_GET['json']) ? true : false);
	$all_data = array();
	$i=0;
	if(isset($_POST["files"])) {
		foreach($_POST["files"] as $folder => $files) {
			foreach($files as $fn) {
				$values = array();
				$interval = (isset($_POST['interval']) ? $_POST['interval'] : 10 );
				$file_interval = findInterval($folder."/".$fn);
				$sample_rate = ceil($interval/$file_interval);
				/*if(isset($_POST['thinval']))
					$values = load_file_data($folder,$fn,intval($_POST['thinval']));
				else
					$values = load_file_data($folder,$fn);
				*/
				$values = load_file_data($folder,$fn,$sample_rate);
				if(!$jo) echo data_dir."/".$folder."/".$fn;
				if(!$jo) echo " (".count($values)." records selected of ".countFileLines($folder."/".$fn).")";
				if(!$jo) echo " (file interval: ".$file_interval.")";
				if(!$jo) echo " (file sample rate: ".$sample_rate.")";
				if(!$jo) echo "<br>";
				$disp_name = ($folder=="." ? "Unsorted" : $folder);
				$all_data[$i]["key"] = $disp_name." (".$fn.")";
				$all_data[$i]["color"] = $colors[$i%count($colors)];
				$all_data[$i]["values"] = $values;
				
				$i++; // one output row for each file
			}
			
		}
	}
	if($jo) {
		echo json_encode($all_data);
		die();
	}
	else {
		echo '<textarea style="width:90%;height:200px;">';
		echo json_encode($all_data);
		echo '</textarea>';
	}
}


$file_list = get_file_list(data_dir);


?>
<form action="data_gen.php?get" method="post">

<?php file_checklist($file_list); ?>
<label for="thinval">Resolution:</label>
<select name="thinval">
<option value="1">Perfect Detail (Probably won't work)</option>
<option value="10">Very Fine (Very Slow)</option>
<option value="100">Fine</option>
<option value="250" selected="selected">Medium (Best)</option>
<option value="500">Coarse</option>
<option value="1000">Very Coarse (Fast)</option>

</select>
<label for="interval">Spacing:</label>
<select name="interval">
<option value="1">1 second</option>
<option value="10">10 seconds</option>
<option value="30" selected="selected">30 seconds</option>
<option value="60">1 minute</option>
<option value="600">10 minutes</option>
<option value="1800">30 minutes</option>

</select>
<input type="submit">
</form>
<?php
	findInterval("fred/0210-0217.txt");
?>
