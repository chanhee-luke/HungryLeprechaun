<?php

$long	= $_GET["long"];
$lat	= $_GET["lat"];
$query	= array_key_exists("query", $_GET) ? $_GET["query"] : NULL;
$price	= array_key_exists("price", $_GET) ? $_GET["price"] : NULL;
$rad	= array_key_exists("rad", $_GET) ? $_GET["rad"] : NULL;
$num	= array_key_exists("num", $_GET) ? $_GET["num"] : NULL;


$command = "./kdtree";

if(!is_null($query))
	$command .= " -q " . $query;

if(!is_null($price))
	$command .= " -p " . $price;

if(!is_null($rad))
	$command .= " -r " . $rad;

if(!is_null($num))
	$command .= " -n " . $num;

$command .= " " . $long . " " . $lat;

//echo $command . "\n";

$handle = popen($command, "r");

//open json
$str = file_get_contents('./locations.json');
$data = json_decode($str, TRUE);

$results = array();
$i = 0;

while ($s = fgets($handle, 1024)) {
	if($i >= 26) break;
	$results[$i++] = $data[intval(chop($s))];
}

echo json_encode($results);

pclose($handle);
