<?php
date_default_timezone_set("Asia/Singapore");
	if(isset($_GET['generate1'])){
		//echo '<input type="hidden" value="' . htmlspecialchars($data) . '" />'."\n";
		//echo '<input type="text" name="testText2" value="wewewe">';
		//echo "print fro top";
		
	
		$image = file_get_contents('http://maps.googleapis.com/maps/api/staticmap?center=15719%20OAKLEAF%20RUN%20DRIVE,LITHIA,FL,33547,US&zoom=8&size=150x100&markers=color%3ablue%7Clabel%3aS%7C11211&sensor=false'); 
		$fp  = fopen('ae.png', 'w+'); 
		fputs($fp, $image); 
		fclose($fp); 
		unset($image);
		
		$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
		$txt = "Mickey Mouse\n";
		fwrite($myfile, $txt);
		$txt = "Minnie Mouse\n";
		fwrite($myfile, $txt);
		fclose($myfile);
		
		$file = fopen("test.txt","w");
		echo fputs($file,"Hello World. Testing!");
		fclose($file);
		
		//https://maps.googleapis.com/maps/api/staticmap?center=1.3017085612829866,%20103.83636023603027&zoom=20&size=640x640&maptype=roadmap&key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs
		

	}
?>
<!DOCTYPE html>
<html>
<body>
<!--<form action="index1.php" method="post">-->
	<h3>Auto Generate GeoRectified Image</h3>
	<!--<input type="submit" id="generate" name="generate" value="Generate Image">-->
	<button type="button" id="generate" name="generate">Generate Image</button>
	<p id="demo"></p>
	<!--<div id="map" style="width:100%;height:500px"></div>-->
	<div id="map" style="width:640px;height:640px"></div>
<!--</form>-->

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
    mapTypeControl: true,
    scaleControl: true,
    streetViewControl: true,
    //overviewMapControl: true,
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
}
	
document.getElementById("generate").addEventListener("click", function(){
	currentDateTime = "<?php echo date("Y-m-d_H-i-s") ?>";
	document.getElementById("demo").innerHTML = currentDateTime;
	// Get center of map, zoom, map type for image download
	centerLat = map.getCenter().lat();
	centerLng = map.getCenter().lng();
	zoom = map.getZoom();
	mapType = map.getMapTypeId();
	var bString1 = "https://maps.googleapis.com/maps/api/staticmap?center=";
	var bString2 = ",";
	var bString3 = "&zoom=";
	var bString4 = "&size=640x640&maptype=";
	var bString5 = "&key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs";
	var getImageURL1 = bString1 + centerLat + bString2 + centerLng + bString3 + zoom + bString4 + mapType + bString5;
	//https://maps.googleapis.com/maps/api/staticmap?center=1.3571235,103.798773&zoom=18&size=640x640&maptype=satellite&key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs
	$.ajax({
		type: "GET",
		url: "downloadImage.php",
		data: {
			imageURL: getImageURL1,
			currentDT: currentDateTime
		},
		success: function(response0){
			console.log("Downloading Image Successful");			
		}
	})
	
	//Get Top Left pixel in SVY21
	/////topLeftLat = map.getBounds().getNorthEast().lat();
	/////topLeftLng = map.getBounds().getSouthWest().lng();
	//https://developers.onemap.sg/commonapi/convert/4326to3414?latitude=1.319728905&longitude=103.8421581
	var aString1 = "https://developers.onemap.sg/commonapi/convert/4326to3414?latitude=";
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
							topLeftLat: topLeftLat,
							topLeftLng: topLeftLng
						},
						success: function(response0){
							console.log("Created World File");			
						}
					})
				}
			})
			
		}
	})
	
	
<?php
		//echo '<input type="hidden" value="' . htmlspecialchars($data) . '" />'."\n";
		//echo '<input type="text" name="testText2" value="wewewe">';
		//echo "print fro top";

?>

});

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA-k9PZYYg1a6uIfda9TKWUE6ZTsesHUqs&callback=myMap"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>
</html>