<!DOCTYPE html>
<html lang="en">
<head>
	<meta charse="UTF-8">
	<link rel="stylesheet" type="text/css" href="lib/xcharts.css"/>
	<link rel="stylesheet" type="text/css" href="lib/bootstrap-theme.min.css"/>






</head>
<body>

	<div class="row">
		<figure style="width: 1000px; height: 400px;" id="chart"></figure>
	</div>

	
	
	
	<script type="text/javascript" src="lib/jquery.min.js"></script>
	<script type="text/javascript" src="lib/d3.v3.js"></script>
	<script type="text/javascript" src="lib/xcharts.js"></script>
	
	<script type="text/javascript">
	<?php include("xchart_data_generator.php"); ?>
	var tt = document.createElement('div'),
					leftOffset = -(~~$('html').css('padding-left').replace('px', '') + ~~$('body').css('margin-left').replace('px', '')),
					topOffset = -32;
	tt.className = 'ex-tooltip';
	document.body.appendChild(tt);
	var opts = {
	  "dataFormatX": function (x) { return d3.time.format('%Y-%m-%d %H:%M:%S').parse(x); },
	  "tickFormatX": function (x) { return d3.time.format('%m/%d %H:%M')(x); },
	  "mouseover": function (d, i) {
		var pos = $(this).offset();
			$(tt).text(d3.time.format('%Y-%m-%d %H:%M:%S')(d.x) + ': ' + d.y)
			  .css({top: topOffset + pos.top, left: pos.left + leftOffset})
			  .show();
		console.log("done");
	  },
	  "mouseout": function (x) {
		$(tt).hide();
	  }
	};
	var myChart = new xChart('line', data, '#chart', opts);
	$("circle").attr('r',2); // Control the data point radius
	</script>
	
</body>
</html>