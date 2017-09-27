<?php
date_default_timezone_set("Asia/Singapore");

?>
<!DOCTYPE html>
<html lang="en">
 <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>WebApp CI2</title>

	<link href="css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<div class="container">
<!--<form action="index1.php" method="post">-->
	<h3>Auto Generate GeoRectified Image</h3>
	<!--<input type="submit" id="generate" name="generate" value="Generate Image">-->
	<button type="button" id="generate" name="generate">Generate Image</button>
	<!--<button type="button" id="download" name="download">Download Image</button>-->
	<p id="demo"></p>
	<!--<div id="map" style="width:100%;height:500px"></div>-->
	<div id="map" style="width:640px;height:640px"></div>
	<br><br>
<!--</form>-->



<div class="container-fluid" style="width: 100%; margin: 0 auto;">
	<table class="table table-hover">
		<tr>
			<th>File Name</th>
			<th>Image</th>
			<!--<th colspan="4">More Functions</th>-->
		</tr>
	<?php
	if ($handle = opendir('images/')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {?>
				<tr>
					<td><?php echo "<a href='downloadImage.php?file=".$entry."'>".$entry."</a>\n"; ?></td>
					<td><img src=<?php echo 'images/' . $entry;?> alt="no image" height="50"></td>
				</tr>
	<?php
			}
		}
	closedir($handle);
	}
	?>
	</table>
</div>
<script>
var map = "";
var topLeftLat = 0;
var topLeftLng = 0;
var topRightLat = 0;
var topRightLng = 0;
var centerLat = 0;
var centerLng = 0;
var zoom = 0;
var mapType = "";
var currentDateTime = "";

function myMap() {
  var mapCanvas = document.getElementById("map");
  var mapOptions = {
    center: new google.maps.LatLng(1.3571235,103.7987731),
    zoom: 10,
	mapTypeId: google.maps.MapTypeId.HYBRID,
    //panControl: true,
    zoomControl: true,
	zoomControlOptions: {
		  position: google.maps.ControlPosition.RIGHT
	},
    mapTypeControl: true,
    scaleControl: true,
    streetViewControl: true,
	streetViewControlOptions: {
              position: google.maps.ControlPosition.RIGHT_TOP
          },
    //overviewMapControl: true,
	fullscreenControl: false, 
    rotateControl: true   
  };
  
	map = new google.maps.Map(mapCanvas, mapOptions);


	google.maps.event.addListener(map, 'bounds_changed', function() {
		// Get center of map for image download
		console.log(map.getCenter().toString());
		console.log(map.getBounds().getNorthEast());
		console.log(map.getBounds().getSouthWest());
		topLeftLat = map.getBounds().getNorthEast().lat();
		topLeftLng = map.getBounds().getSouthWest().lng();
		topRightLat = map.getBounds().getNorthEast().lat();
		topRightLng = map.getBounds().getNorthEast().lng();
	});
}//END OF myMap FUNCTION

/*
document.getElementById("download").addEventListener("click", function(){
	$.ajax({
		type: "GET",
		url: "downloadImage2.php",
		data: {
			imageURL: "haha",
			currentDT: "tie"
		},
		success: function(response0){
			console.log("Downloading Image Successful");			
		}
	})
});
*/

document.getElementById("generate").addEventListener("click", function(){
	currentDateTime = "<?php echo date("Y-m-d_H-i-s") ?>";
	//document.getElementById("demo").innerHTML = currentDateTime;
	// Get center of map, zoom, map type for image download
	centerLat = map.getCenter().lat();
	centerLng = map.getCenter().lng();
	centerPoint = map.getCenter();
	zoom = map.getZoom();
	mapType = map.getMapTypeId();
	var bString1 = "https://maps.googleapis.com/maps/api/staticmap?center=";
	var bString2 = ",";
	var bString3 = "&zoom=";
	var bString4 = "&size=640x640&maptype=";
	var bString5 = "&key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs";
	var getImageURL1 = bString1 + centerLat + bString2 + centerLng + bString3 + zoom + bString4 + mapType + bString5;
	//https://maps.googleapis.com/maps/api/staticmap?center=1.3571235,103.798773&zoom=18&size=640x640&maptype=satellite&key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs
/*	$.ajax({
		type: "GET",
		url: "downloadImageSHIT.php",
		data: {
			imageURL: getImageURL1,
			currentDT: currentDateTime
		},
		success: function(response0){
			console.log("Downloading Image Successful");			
		}
	})*/
	
	
	var googleProjection = "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs";
	var utmZ48SProjection = "+proj=utm +zone=48 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs";
	var topLeftUTM = proj4(googleProjection,utmZ48SProjection,[topLeftLng,topLeftLat]);
	var topRightUTM = proj4(googleProjection,utmZ48SProjection,[topRightLng,topRightLat]);
	//var projectedCo1 = proj4(googleProjection,utmZ48SProjection,[103.77112488947456,1.446610401902438]);

	console.log("Printing UTM WGS84 48S projection");
	console.log(topLeftUTM);
	console.log(topLeftUTM[0]);
	console.log(topRightUTM);
	console.log(topRightUTM[0] - topLeftUTM[0]);
	console.log((topRightUTM[0] - topLeftUTM[0])/640.0);
	
	$.ajax({
		type: "GET",
		url: "createWorldFile.php",
		data: {
			currentDT: currentDateTime,
			resolution: ((topRightUTM[0] - topLeftUTM[0])/640.0),
			topLeftLat: topLeftUTM[0],
			topLeftLng: topLeftUTM[1]
		},
		success: function(response0){
			console.log("Created World File");
			$.ajax({
				type: "GET",
				url: "downloadImageSHIT.php",
				data: {
					imageURL: getImageURL1,
					currentDT: currentDateTime
				},
				success: function(response0){
					console.log("Downloading Image Successful");
					location.reload();
					map.setCenter(centerPoint);
					map.setZoom(zoom);
				}
			})
		}
	})
	
	//Get Top Left pixel using One Map api
	//https://developers.onemap.sg/commonapi/convert/4326to3414?latitude=1.319728905&longitude=103.8421581
/*	var aString1 = "https://developers.onemap.sg/commonapi/convert/4326to3414?latitude=";
	var aString2 = "&longitude=";
	var aString3 = aString1 + topLeftLat + aString2 + topLeftLng;
	var aString4 = aString1 + topRightLat + aString2 + topRightLng;
	$.ajax({
		type: "Get",
		url: aString3,
		//data: {
		success: function(response1){
			console.log("printing CONVERSION OF TOP LEFT Lng to METERS");
			console.log(response1.Y);
			console.log(aString4);
			$.ajax({
				type: "Get",
				url: aString4,
				success: function(response2){
					console.log("printing Left Top in METERS ");
					console.log(response1);
					console.log(response1.Y);
					console.log("printing Right Top in METERS ");
					console.log(response2);
					console.log(response2.Y);
					var dist = response2.X - response1.X;
					console.log(dist);
					var resolution = dist / 640.0;
					console.log(resolution);
					$.ajax({
						type: "GET",
						url: "createWorldFile.php",
						data: {
							currentDT: currentDateTime,
							resolution: resolution,
							topLeftLat: response1.X,
							topLeftLng: response1.Y
						},
						success: function(response0){
							console.log("Created World File");			
						}
					})
				}
			})
			
		}
	})*/

}); //END OF GENERATE Button

</script>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs&callback=myMap"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/proj4-src.js?version=1.01"></script>
</body>
</html>