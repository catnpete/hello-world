<?php
	$currentDT = $_GET['currentDT'];
	$resolution = $_GET['resolution'];
	$topLeftLat = $_GET['topLeftUTMLat'];
	$topLeftLng = $_GET['topLeftUTMLng'];
	
	$myfile = fopen($currentDT.".pgw", "w") or die("Unable to open file!");
	$txt = $resolution . "\n";
	fwrite($myfile, $txt);
	$txt = "0.0\n";
	fwrite($myfile, $txt);
	$txt = "0.0\n";
	fwrite($myfile, $txt);
	$txt = "-" . $resolution . "\n";
	fwrite($myfile, $txt);
	$txt = $topLeftLat . "\n";
	fwrite($myfile, $txt);
	$txt = $topLeftLng . "\n";
	fwrite($myfile, $txt);
	fclose($myfile);

	/*$file = fopen("test.txt","w");
	echo fputs($file,"Hello World. Testing!");
	fclose($file);*/
		
?>