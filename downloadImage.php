<?php
	$response0 = $_GET['imageURL'];
	$currentDT = $_GET['currentDT'];
	$image = file_get_contents($response0); 
	$fp  = fopen($currentDT.'.png', 'w+'); 
	fputs($fp, $image); 
	fclose($fp); 
	unset($image);
?>