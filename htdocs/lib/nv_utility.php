<?php
define('data_dir',"./pressure_data");

function get_file_list($data_dir) {
	$file_list = array();
	$data_files = scandir($data_dir);
	foreach($data_files as $fn) {
		if($fn=="." || $fn=="..") {continue;}
		// Is this a text file or a directory?
		if(is_dir($data_dir."/".$fn)) {
			$dir_files = scandir($data_dir."/".$fn);
			foreach($dir_files as $dfn) {
				if($dfn=="." || $dfn=="..") {continue;}
				if(substr($dfn, 0, 1)!=".")
					$file_list[$fn][] = $dfn; // $file_list["folder_name"][] = "filename";
			}
		}
		else { // It's a file, not a dir
			$file_list["."][] = $fn;
		}
	}
	return $file_list;
}

function file_checklist($file_list) {
	echo "<ul class=\"cb_folder_list\">";
	foreach($file_list as $folder=>$files) {
		$disp_name = ($folder=="." ? "Unsorted" : $folder);
		echo "<li class=\"folder_name\"><span class=\"title\">".$disp_name."</span> <a class=\"selectnone\" href=\"#\"></a> <a class=\"selectall\" href=\"#\"></a> <a class=\"showhide\" href=\"#\"></a>";
		if(is_array($files)) {
			echo '<ul class="cb_file_list">';
			foreach($files as $fn) {
				echo '<li class="file_name">';
				//echo '<div class="roundedOne">';
				echo '<input type="checkbox" name="files['.$folder.'][]" id="cb'.$folder.$fn.'" value="'.$fn.'" class="" /> ';
				echo "<label for=\"cb".$folder.$fn."\">".$fn."</label>";
				//echo '</div>';
				echo '</li>';
			}
			echo "</ul></li>";
		}
		else {
			// Why wasn't the folder an array?
		}
	}
	echo "</ul>";
}


function load_file_data($folder,$fn,$thinval=250) {
	$output=array();
	
	$any_error = false; // This is an error in an individual reading
	$handle = fopen(data_dir."/".$folder."/".$fn,"r");
	$i=0;
	if ($handle) {
		while (($line = fgets($handle)) !== false) {
			if($i%$thinval==0) {
				$error = true;
				$la = explode(",",$line);
				if(count($la)<2)
					continue;
				$date = $la[0];
				$data = explode("P", trim($la[1]));
				$pressure = $data[0];
				if(isset($date) && isset($pressure)) {
					// Did the reading have a year?
					if(strpos($date,"2015/")!==false)
						$fd = date("Y-m-d G:i:s",strtotime($date));
					else
						$fd = date("Y-m-d G:i:s",strtotime("2015/".$date));
					
					// This at least used to be relevant
					if(date("Y",strtotime($fd)) == 2015) {
						if(is_numeric(trim($pressure))) {
							$error = false;
							//$fd = strtotime($fd);
							$output[]=array("x"=>$fd,"y"=>floatval(trim($pressure)));
						}
					}
				}
				if($error) {
					$any_error = true;
				}
			}
			$i++;
		}
	} else {
		//echo "error opening";
	} 
	fclose($handle);
	
	return $output;
}
// If an interval is found, return it in seconds
// If no interval found, return false
function findInterval($file) {
	$f = fopen(data_dir."/".$file, 'r');
	$lines = 0;
	$last_time = 0;
	$interval = -1;
	$i=0;
		while (($line = fgets($f)) !== false) {
		if($i>10) continue;
		$la = explode(",",$line);
		$date = $la[0];
		$fd = 0;
		if(strpos($date,"2015/")!==false)
			$fd = strtotime($date);
		else
			$fd = strtotime("2015/".$date);
		if($interval == $fd-$last_time) {
			fclose($f);
			return $interval;
		}
		// Next!
		if($fd-$last_time >0)
			$interval = $fd-$last_time;
		$last_time = $fd;
		$i++;
    }
    fclose($f);
    return false;
}
function countFileLines($file) {
    $f = fopen(data_dir."/".$file, 'rb');
    $lines = 0;
    while (!feof($f)) {
        $lines += substr_count(fread($f, 8192), "\n");
    }
    fclose($f);
    return $lines;
}




// TEMPLATE THINGS
function header_bar() {

	$pafm = "";
	$graph = "";
	
	if (strpos($_SERVER['PHP_SELF'],'pafm') !== false)
		$pafm = ' class="active"';
	if (strpos($_SERVER['PHP_SELF'],'graph') !== false)
		$graph = ' class="active"';

	$txt = <<<EOT
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/graphicas.php">Pressure Data Tool</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li $pafm><a href="./pafm.php">Data Manager <span class="sr-only">(current)</span></a></li>
        <li $graph><a href="./graphicas.php">Data Viewer</a></li>
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li><a href="http://adechonduras.org">adechonduras.org</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
EOT;
echo $txt;
}


function seeded_shuffle(array &$items, $seed = false) {
    $items = array_values($items);
    mt_srand($seed ? $seed : time());
    for ($i = count($items) - 1; $i > 0; $i--) {
        $j = mt_rand(0, $i);
        list($items[$i], $items[$j]) = array($items[$j], $items[$i]);
    }
}