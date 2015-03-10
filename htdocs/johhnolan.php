<?php

if(isset($_GET['example_1'])) {
$project_count = 1000;
$total_cost = 4000000/10;
$failed_at_5_years = .3;
$failed_at_10_years = .5;
$failed_at_20_years = 1;

$fail_set_1 = [
  5 => 0.3,
  10 => 0.5,
  20 => 0.2,
];
$fail_set_2 = [
  5 => 0.3,
  10 => 0.5,
  35 => 0.2,
];
$fail_set_3 = [
  3 => 0.05,
  6 => 0.2,
  9 => 0.1,
  12 => 0.1,
  15 => 0.2,
  18 => 0.15,
  20 => 0.2
];
$sum=0;
for($i=1;$sum<=1;$i++) {
	//$fail_set_4[$i] = $fail_set_4[$i-1]+0.03;
	$fail_set_4[$i] = 0.03;
	$sum += 0.03;
}

$projects = array();
for($i=0;$i<$project_count;$i++) {
	$projects["random"][$i]["lifetime"] = mt_rand(1,20);
	$projects["set_1"][$i]["lifetime"] = checkWithSet($fail_set_1);
	$projects["set_2"][$i]["lifetime"] = checkWithSet($fail_set_2);
	$projects["set_3"][$i]["lifetime"] = checkWithSet($fail_set_3);
	$projects["set_4"][$i]["lifetime"] = checkWithSet($fail_set_4);
}
?>
Project lifetimes generated at random. Refresh page for new results.
<table border="1">
	<tr>
		<th></th>
		<th>Project #</th>
		<th>Random (0,20] Lifetime</th>
		<th>Fail:<br> 30% by 5y,<br> 50% by 10y,<br> 100% by 20y</th>
		<th>Fail:<br> 30% by 5y,<br> 50% by 10y,<br> 100% by 35y</th>
		<th>Fail:<br><?php
		$rt = 0;
		foreach($fail_set_3 as $year => $percent) {
			$rt += $percent*100;
			echo $rt, "% by ".$year."y<br>";
		}
		?></th>
		<th>Fail:<br> 3% per year</th>
	</tr>
<?php
$colsum[0] = 0;
$colsum[1] = 0;
$colsum[2] = 0;
$colsum[3] = 0;
$colsum[4] = 0;

$tablebody = "";
for($i=0; $i<$project_count; $i++) {
	$tablebody .=  "<tr>";
	$tablebody .=  "<td></td>";
	$tablebody .=  "<td>".$i."</td>";
	$tablebody .=  "<td>".$projects['random'][$i]['lifetime']."</td>";
	$tablebody .=  "<td>".$projects['set_1'][$i]['lifetime']."</td>";
	$tablebody .=  "<td>".$projects['set_2'][$i]['lifetime']."</td>";
	$tablebody .=  "<td>".$projects['set_3'][$i]['lifetime']."</td>";
	$tablebody .=  "<td>".$projects['set_4'][$i]['lifetime']."</td>";
	$tablebody .= "</tr>";
	$colsum[0] += $projects['random'][$i]['lifetime'];
	$colsum[1] += $projects['set_1'][$i]['lifetime'];
	$colsum[2] += $projects['set_2'][$i]['lifetime'];
	$colsum[3] += $projects['set_3'][$i]['lifetime'];
	$colsum[4] += $projects['set_4'][$i]['lifetime'];
}
$a_sum_body = "";
foreach($colsum as $k => $cs) {
	$l_a_m[$k] = $cs/$project_count;
	$a_sum_body .= "<th>".$l_a_m[$k]." years</th>";
}
?>

<tr><th>lifetime_arithmetic_mean: </th><th></th><?=$a_sum_body;?></tr>
<?=$tablebody;?>
</table>

<?php
} // END example 1
?>

<?php
function array_average_by_key( $arr )
{
    $sums = array();
    $counts = array();
    foreach( $arr as $k => &$v )
    {
        foreach( $v as $sub_k => $sub_v )
        {
            if( !array_key_exists( $sub_k, $counts ) )
            {
                $counts[$sub_k] = 0;
                $sums[$sub_k]   = 0;
            }
            $counts[$sub_k]++;
            $sums[$sub_k]  += $sub_v;
        }
    }
    $ret = array();
    foreach( $sums as $k => $v )
    {
        $ret[$k] = $v / $counts[$k];
    }
    return $ret;
}

function checkWithSet(array $set, $length=10000)
{
   $left = 0;
   foreach($set as $num=>$right)
   {
      $set[$num] = $left + $right*$length;
      $left = $set[$num];
   }
   $test = mt_rand(1, $length);
   $left = 1;
   foreach($set as $num=>$right)
   {
      if($test>=$left && $test<=$right)
      {
         return $num;
      }
      $left = $right;
   }
   return null;//debug, no event realized
}