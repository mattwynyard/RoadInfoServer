<?php

require "CHCHEvent.php";

$curl_chch = curl_init('https://infoconnect1.highwayinfo.govt.nz/ic/jbi/TMP/REST/FeedService/');
#Christchurch xml
$startCH_Time = microtime(true);
curl_setopt($curl_chch, CURLOPT_HTTPHEADER, array(
    'username: mjwynyard', 'password: Copper2004'
        )
);
curl_setopt($curl_chch, CURLOPT_RETURNTRANSFER, true);
$responseCH = curl_exec($curl_chch);
//echo $responseCH;
file_put_contents("roadCHCH.xml", $responseCH);
$endCH_Time = microtime(true);

echo "CHCH xml downloaded: " .($endCH_Time - $startCH_Time) ." seconds\n\n";

$xml = simplexml_load_file("roadCHCH.xml");

$events = $xml->xpath("tns:tmpsResult/tns:item");
$id = $xml->xpath("//tns:id");
$title = $xml->xpath("//tns:title");
$address = $xml->xpath("//tns:address");
$startDate = $xml->xpath("//tns:startDate");
$endDate = $xml->xpath("//tns:endDate");
$description = $xml->xpath("//tns:publicDescription");
$status = $xml->xpath("//tns:roadClosureStatus");
$updated = $xml->xpath("//tns:lastUpdated");
$job = $xml->xpath("//tns:jobType");
$significance = $xml->xpath("//tns:significance");
$timeofDay = $xml->xpath("//tns:timeOfDay");
$impacts = $xml->xpath("//tns:trafficImpacts/tns:item");


//$impacysArray = array();
$count = count($id);

$countImpacts = count($impacts);
//echo $countImpacts;
//echo $impactArray[0];


echo $count ."\n";
//print_r($id);


$eventArray = array(); //holds all CHCHEvents
$titleArray = array();
$impactArray = array();
for ($i = 0; $i < $count; $i++) {
    
    $obj = new CHCHEvent;
    $obj->setID($id[$i]);
    $eventArray[$i] = $obj;
    echo $obj->getID() . "\n";

}


