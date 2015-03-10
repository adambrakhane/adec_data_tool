<?php include('./lib/nv_utility.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charse="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="lib/nv.d3.css"/>
	<link rel="stylesheet" type="text/css" href="lib/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="lib/bootstrap-sandstone.css"/>
	<link rel="stylesheet" type="text/css" href="lib/jquery-ui.css"/>
	<link rel="stylesheet" type="text/css" media="print" href="bootstrap.min.css">
	<link rel="stylesheet" type="text/css" media="print" href="nv.d3.css">

	<style>
		html, body {
			background-color:#F0F0F0;
		}
		.nv-legend.text {
			font-weight:800;
			color:#fff;
		}
		.nvd3 text {
			color:#fff;
		}
		.nv-series {
			color:#fff;
		}
		circle.nv-legend-symbol:hover {
			stroke-width: 10px;
		}
		.btn-submit {
			float:right;
		}
		
		#file_list_well {
			padding:0px;
			height:200px;
			overflow:scroll;
			overflow-x: hidden;
		}
		ul.cb_folder_list {
			width:100%;
			margin:0;
			padding:0;
		}
		ul.cb_file_list {
			width:100%;
			margin:0;
			padding:0px 0px 0px 0px;
			list-style-type: none;
		}
		li.folder_name {
			display:block;
			width:100%;
			
			background-color:#f6f6ff;
			
			color:#666;
			font-size:18px;
			font-weight:600;
			list-style-type: none;
			border-bottom:1px solid #6F6F6F;
		}
		li.folder_name span.title {
			padding:2px 0px 1px 10px;
		}
		li.folder_name a, li.folder_name a:visited {
			font-size:13px;
			float:right;
			padding-top:3px;
			text-decoration:none;
		}
		li.folder_name a:hover {
			font-color:white;
		}
		
		li.folder_name:first-letter {
			text-transform: uppercase;
		}
		li.file_name {
			display:block;
			width:100%;
			border-bottom:1px solid #cfcfcf;
			font-size:13px;
			font-weight:300;
			color:#000;
		}
		li.file_name input[type=checkbox] {
			float:left;
			margin-right:10px;
		}
		li.file_name label {
			display:block;
			width:100%;
			height:100%;
			margin:0px 0px 0px 20px;
		}
		li.file_name label:hover {
			background-color:#E5F2FB;
		}
		li.file_name:nth-child(odd) {
			background-color:#efefef;
		}
		li.file_name:nth-child(even) {
			background-color:#fff6f6;
		}
		li a.showhide {
			width:100px;
			
		}
		li a.title {
			
		}
		li a.select {
			width:100px;
		}
		
		li ul { display: none; }
		.sprite-spinner {
			overflow: hidden;
			position: relative;
			display:inline-block;
			text-align:left;
		}

		.loading_sprite {
			float:left;
			text-align:center;
			margin:100px auto;
			width:100%;
			position:absolute;
			z-index:1000;
			font-size:48px;
			font-weight:700;
			background-color:white;
			opacity:0.8;
			background:radial-gradient(circle, #fff, #888 );
			border-radius:30px;
			padding:50px 20px 50px 20px;
			display:none;
		}

		body{
			background:radial-gradient(circle, #6d8aa5, #303d49 );
		}

		#svg_panel {
			border:3px solid black;
			border-radius:3px;
			background:radial-gradient(circle, #F0F0F0, #D6D6D6 );
		}
		#interval_slider {
			margin:5px 5px 10px 5px;
			height:60px;
		}
		#interval_slider a.ui-slider-handle {
			height:70px;
		}
		#interval_slider a.ui-state-active, #interval_slider a.ui-state-hover, #interval_slider a.ui-state-focus{
			top:2px;
			height:55px;
			background: #f8f7f6 url("images/ui-bg_fine-grain_10_f8f7f6_60x60.png") 50% 50% repeat;
		}
		.selected_count {
			float:right;
		}
		
		
		.nvd3.nv-line .nvd3.nv-scatter .nv-groups .nv-point {
			fill-opacity: 0;
			stroke-opacity: 0;
		}
		.nvd3.nv-line .nvd3.nv-scatter .nv-groups .nv-point .hover {
			fill-opacity: 0;
			stroke-opacity: 1;
		}
	</style>





</head>
<body>
<div class="loading_sprite">
    <div class="sprite-spinner">
      <img src="lib/images/loading_sprite.png" alt=""/>
	  
    </div><br>
	Loading...
</div>
<?php header_bar(); ?>
<div class="container">
	<div class="row">
		<h1><small>Pressure Data</small></h1>
	</div>
	<div class="row" id="chart_container">
		<div style="col-lg-12">
			<div class="panel panel-default" id='no_chart_message'>
				<div class="panel-body">
					Use the form below to load data
				</div>
			</div>
			<div class="panel panel-primary" id='chart'>
				<div class="panel-heading">
					<div class="" style="float:right;margin-top:10px;">
						<button id="reset_graph" class="btn btn-default">Reset Graph</button>
						<button id="clear_graph" class="btn btn-default">Clear Graph</button>
					</div>
					<h2>Data</h2>
				</div>
				<div class="panel-body" style="" id="svg_panel">
					<svg style='height:500px;width:100%;'> </svg>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<form action="data_gen.php?json" method="post" name="data_select_form" id="data_select_form">
		<div class="row well">
			<div class="col-lg-8 cb_well">
				<div class="well">
					<div class="selected_count"><span>0</span> files selected</div>
					<button id="cb_select_all" type="button" class="btn btn-primary btn-xs">Select All Files</button>
					<button id="cb_select_none" type="button" class="btn btn-primary btn-xs">Clear Selection</button><br>
					
					<div class="well" id="file_list_well">
						<?php file_checklist(get_file_list(data_dir)); ?>
					</div>
					<div class="form-group" style="text-align:center;">
						<button type="submit" class="btn btn-success">Load Data</button>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="well">
					<div class="form-group">
						<!--<label for="thinval">Resolution:</label>
						<select name="thinval" class="form-control">
							<option value="1">Perfect Detail (Probably won't work)</option>
							<option value="10">Very Fine (Very Slow)</option>
							<option value="100">Fine</option>
							<option value="250" selected="selected">Medium (Best)</option>
							<option value="500">Coarse</option>
							<option value="1000">Very Coarse (Fast)</option>
						</select>
						-->
						<label for="interval">Sample Interval:</label>
						<select name="interval" class="form-control" id="interval_select">
							<option value="30">30 seconds (Probably won't work)</option>
							<option value="60">1 minute (Very Slow)</option>
							<option value="300">5 minutes</option>
							<option value="600" selected="selected">10 minutes (Best Choice)</option>
							<option value="1800">30 minutes</option>
							<option value="3600">1 hour (Very Fast)</option>

						</select>
					</div>
					<div class="form-group" style="text-align:center;">
						<button type="submit" class="btn btn-success">Load Data</button>
					</div>
				</div>
			</div>
		</div>	
	</form>
</div> <!--// Container -->

<script type="text/javascript" src="lib/jquery.min.js"></script>
<script type="text/javascript" src="lib/d3.v3.js"></script>
<script type="text/javascript" src="lib/nv.d3.js"></script>
<script type="text/javascript" src="lib/bootstrap.min.js"></script>
<script type="text/javascript" src="lib/jquery-ui.min.js"></script>

<script type="text/javascript">
	var raw_data = [];
	function create_chart() {
		
		var chart = nv.models.lineWithFocusChart();
		
		var xAxisFormatter = function (d) {
			return d3.time.format('%m/%d %H:%M')(new Date(d));
		};
		var x2AxisFormatter = function (d) {
			return d3.time.format('%m/%d')(new Date(d));
		};

		var yAxisFormatter = function (d) {
			return d3.format(',.1')(d);
		};

		chart.xAxis
			.tickFormat(xAxisFormatter);
			
		chart.x2Axis
			.tickFormat(x2AxisFormatter);
		
		chart.yAxis
			.tickFormat(yAxisFormatter)
			.axisLabel("Pressure");
		chart.y2Axis
			.tickFormat(yAxisFormatter);
		if($("#interval_select").val()>=900)
			chart.interpolate("cardinal");
		else
			chart.interpolate("monotone");

		chart.tooltipContent(function (key, y, e, graph) {
			var bgCol = raw_data[graph.seriesIndex].color;
			var content = '<h3 style="font-weight:400;background-color: ';
			content += bgCol + ';color:#000000;font-size:17px;">';
			content += y + '</h3><p style="font-size:15px">Ubicación: ' +  key + '</p>';
			content += '<p style="font-size:15px">Presión: <strong>' +  e + '</strong></p>';
			return content;
		});
		chart.noData("Use the form below to select data")
		
		chart.x(function (d) {
			//return d.x; // This one is if we pass in a time stamp
			return d3.time.format("%Y-%m-%d %H:%M:%S").parse(d.x); // This is if we pass in a date as a string
		});
		chart.y(function (d) { return d.y });
		
		chart.options({
			noData: "Select a file to graph",
			transitionDuration: 500,
			showLegend: true,
			showXAxis: true,
			showYAxis: true,
			rightAlignYAxis: false,
			
		});
		chart.margin({
			top: 30,
			right: 20,
			bottom: 50,
			left: 45
		});
		chart.forceY([0,35]); // Never more zoomed than this.
		d3.select('#chart svg')
			.datum(raw_data)
			.transition().duration(500)
			.call(chart)
			
		

		$('#testhref').click(function(){
			  console.log(btoa(d3.select('#chart svg').html()));
			});

		nv.utils.windowResize(chart.update);
		
		return chart;
	}
	function creation_callback() {
		// the callback
		$(".nv-series").click(function(){
			if($(this).css("opacity")>0.5)
				$(this).css("opacity",.5);
			else
				$(this).css("opacity",1);
		});
		
		//console.log($(".nv-noData"));
	}
	
	
	function destroy_graph() {
		$("#chart").slideUp();
		d3.selectAll("#chart svg > *").remove();
		$("#no_chart_message").slideDown();
	}
	function open_loader() {$(".loading_sprite").fadeIn();}
	function close_loader() {$(".loading_sprite").fadeOut();}
	
	function create_graph() {
		open_loader(); 
		$("#no_chart_message").slideUp();
		var chart = create_chart();
		nv.addGraph(chart,creation_callback());
		$("#chart").slideDown();
		chart.update();
		$('html, body').animate({
			scrollTop: $("#chart_container").offset().top
		}, 1000);
		close_loader();
	}
	function reset_graph() {
		destroy_graph();
		create_graph();
	}
	$("#reset_graph").click(function() {
		reset_graph();
	});
	$("#load_graph").click(function() {
		create_graph();
	});
	
	$("#clear_graph").click(function() {
		destroy_graph();
	});
	
	
	$("#data_select_form").submit(function(e){
		e.preventDefault();
		open_loader();
		destroy_graph();

		$.post("data_gen.php?json",
			$(this).serialize(),
			function(data, status){
				raw_data = eval(data);
				reset_graph();
			}
		);
		
		e.preventDefault();
		return false;
	});
	
	// Load the chart with the page
	destroy_graph();
	
	
		function updateCheckedText() {
		var count = countCheckedFiles();
		$(".selected_count span").html(count);
		return true;
	}
	function countCheckedFiles() {
		var count = 0;
		$("#file_list_well").find("input").each(function() {
			if(this.checked == true)
				count++;
		});
		
		return count;
	}
	$("#cb_select_all").click(function() {
		var form = $(this).closest("form");
		$('#'+form.attr('id')+' :checkbox').each(function() {
			this.checked = true;                        
		});
		updateCheckedText();
	});
	$("#cb_select_none").click(function() {
		var form = $(this).closest("form");
		$('#'+form.attr('id')+' :checkbox').each(function() {
			this.checked = false;                        
		});
		updateCheckedText();
	});
	
	
	
	
	$(function() {
		$('li.folder_name').filter(function(i) { return $('ul', this).length >= 1; }).each(function(i) {
			
			// Add the show/hide files links
			$(this).children("a.showhide").append(
				$("<span />").text("Show files").addClass("link")
			)
			.click(function(e) {
				e.preventDefault();
				var $ul = $(this).next("ul");
				if ($ul.is(":visible")) {
					$ul.find("ul").slideUp();
					$ul.slideUp();
					$ul.parent().find("span.link").text("Show files");
				}
				else {
					$ul.slideDown();
					$ul.parent().find("span.link").text("Hide files");
				};
			})

			// Add the select none links
			$(this).children("a.selectnone").append(
				$("<span />").text("/None").addClass("selectlink")
			)
			.click(function(e) {
				e.preventDefault();
				var $ul = $(this).next("ul");
				$ul.find("ul").slideUp();
				$ul.slideUp();
				$(this).parent().children("ul").children("li").children("input:checkbox").each(function() {
					$(this).prop('checked', false);
				});
				updateCheckedText();
			})
			// Add the select all links
			$(this).children("a.selectall").append(
				$("<span />").text("Select All").addClass("selectlink")
			)
			.click(function(e) {
				e.preventDefault();
				var $ul = $(this).next("ul");
				$ul.find("ul").slideDown();
				$ul.slideDown();
				$(this).parent().children("ul").children("li").children("input:checkbox").each(function() {
					$(this).prop('checked', true);
				});
				updateCheckedText();
			})
			
			
		});
	});
	
	
</script>
<script>
(function() {
    SpriteSpinner = function(el, options){
        var self = this,
            img = el.children[0];
        this.interval = options.interval || 10;
        this.diameter = options.diameter || img.width;
        this.count = 0;
        this.el = el;
        img.setAttribute("style", "position:absolute");
        el.style.width = this.diameter+"px";
        el.style.height = this.diameter+"px";
        return this;
    };
    SpriteSpinner.prototype.start = function(){
        var self = this,
            count = 0,
            img = this.el.children[0];
        this.el.display = "block";
        self.loop = setInterval(function(){
            if(count == 19){
                count = 0;
            }
            img.style.top = (-self.diameter*count)+"px";
            count++;
        }, this.interval);
    };
    SpriteSpinner.prototype.stop = function(){
        clearInterval(this.loop);
        this.el.style.display = "none";
    };
    document.SpriteSpinner = SpriteSpinner;
	
	// ## Slider init
	var select = $( "#interval_select" );
    var slider = $( "<div id='interval_slider'></div>" ).insertBefore( select ).slider({
		min: 1,
		max: select.children("option").length,
		range: false,
		value: select[ 0 ].selectedIndex + 1,
		slide: function( event, ui ) {
			select[ 0 ].selectedIndex = ui.value - 1;
		}
    });
    select.change(function() {
		slider.slider( "value", this.selectedIndex + 1 );
    });
})();

$(document).ready(function(){
    $(".sprite-spinner").each(function(i){
      var s = new SpriteSpinner(this, {
        interval:80
      });
      s.start();
    });
});
</script>
</body>
</html>