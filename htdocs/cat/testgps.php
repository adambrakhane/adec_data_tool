<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      html, body, #map-canvas { height: 100%; margin: 0; padding: 0;}
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js">
    </script>
    <script type="text/javascript">
	function initialize() {
		var mapOptions = {
			center: { lat: 14.4, lng: -87},
			zoom: 8
		};
		var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
		setMarkers(map, gps_list);
	}
	
	var gps_list = [
	  ['Tanque', 14.16838, -87.98188, 4],
	  ['Primera Casa', 14.168104, -87.988940, 5],
	  ['Casa media', 14.164626, -88.002813, 3]
	];
	
	function setMarkers(map, locations) {
	  // Add markers to the map

	  // Marker sizes are expressed as a Size of X,Y
	  // where the origin of the image (0,0) is located
	  // in the top left of the image.

	  for (var i = 0; i < locations.length; i++) {
		var locs = locations[i];
		var myLatLng = new google.maps.LatLng(locs[1], locs[2]);
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			title: locs,
			zIndex: locs[3]
		});
	  }
	}

	google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
<div id="map-canvas"></div>
  </body>
</html>