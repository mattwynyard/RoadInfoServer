<?php
$startSH_Time = microtime(true);
$curl_sh = curl_init('https://infoconnect1.highwayinfo.govt.nz/ic/jbi/TREIS/REST/FeedService/');

curl_setopt($curl_sh, CURLOPT_HTTPHEADER, array(
    'username: mjwynyard', 'password: Copper2004'
        )
);
curl_setopt($curl_sh, CURLOPT_RETURNTRANSFER, true);
$responseSH = curl_exec($curl_sh);

file_put_contents("roadSH.xml", $responseSH);
$endSH_Time = microtime(true);

echo "SH xml downloaded: " .($endSH_Time - $startSH_Time) ." seconds\n\n";


#
# ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
# The code above gets the xml (using the username and password details )
#  as a string then saves it as an xml file called roadClosures.xml
#

$xml = simplexml_load_file("roadClosures.xml");
$result = $xml->xpath("//tns:roadEvent");
$count = sizeof($result);

$ids = $xml->xpath("//tns:eventId");
$locations = $xml->xpath("//tns:locationArea");
$descriptions = $xml->xpath("//tns:eventComments");
$endDates = $xml->xpath("//tns:endDate");
$planned = $xml->xpath("//tns:planned");
$alternativeRoute = $xml->xpath("//tns:alternativeRoute");
$eventType = $xml->xpath("//tns:eventType");
$expectedResoultion = $xml->xpath("//tns:expectedResolution");
$impact = $xml->xpath("//tns:impact");
$locationAreas = $xml->xpath("//tns:locationArea");
$restrictions = $xml->xpath("//tns:restrictions");
$startDates = $xml->xpath("//tns:startDate");
$status = $xml->xpath("//tns:status");
$eventCreated = $xml->xpath("//tns:eventCreated");
$eventModified = $xml->xpath("//tns:eventModified");

#############################################################################


$testPoints = $xml->xpath("//tns:wktGeometry");
$testPointsSize = sizeof($testPoints);

$exploded = array();
$finalPoints = array();

for ($i = 0; $i < $testPointsSize; $i++) {
    $exploded[$i] = explode(";", $testPoints[$i]);
    $points[$i] = $exploded[$i][1];

    if (substr($points[$i], 0, 1) === "P") {
        $tempP = explode("(", $points[$i]);
        $tempP2 = substr($tempP[1], 0, -1);
        $finalPoints[$i] = $tempP2;
    } else {
        $tempM = explode("((", $points[$i]);
        $tempM2 = substr($tempM[1], 0, -2);
        $tempM3 = str_replace(",", "", $tempM2);
        $finalPoints[$i] = $tempM3;
    }
}

#############################################################################


echo "#################### EXTRA INFO SIZES ETC ############################\n";
echo "\n";
echo "\n";
echo "Number of road events: " . "$count" . "\n";
echo "\n";
echo "Number of ids: " . sizeof($ids) . "\n";
echo "\n";
//echo "Number of descriptions: " . sizeof($descriptions) . "\n";
//echo "\n";
//echo "Number of Locations: " . sizeof($locations) . "\n";
//echo "\n";
//echo "Number of End Dates: " . sizeof($endDates) . "\n";
//echo "\n";
//echo "Number of Planned: " . sizeof($planned) . "\n";
//echo "\n";
//echo "Number of Alt Route: " . sizeof($alternativeRoute) . "\n";
//echo "\n";
//echo "Number of Event Type: " . sizeof($eventType) . "\n";
//echo "\n";
//echo "Number of Expected Resolutions: " . sizeof($expectedResoultion) . "\n";
//echo "\n";
//echo "Number of Impact: " . sizeof($impact) . "\n";
//echo "\n";
//echo "Number of Location Areas: " . sizeof($locationAreas) . "\n";
//echo "\n";
//echo "Number of Restrictions: " . sizeof($restrictions) . "\n";
//echo "\n";
//echo "Number of Start Dates: " . sizeof($startDates) . "\n";
//echo "\n";
//echo "Number of Status: " . sizeof($status) . "\n";
//echo "\n";
//echo "Number of Event Created: " . sizeof($eventCreated) . "\n";
//echo "\n";
//echo "Number of Event Modified: " . sizeof($eventModified) . "\n";
echo "\n";
echo "\n";
echo "\n";


#############################################################################

$tempStructArray = array();
$toBeEncoded = array();

function convertDates($x) {
    $tempDateArr = explode("T", $x);
    $dateConv = $tempDateArr[0];
    $dateArr = explode("-", $dateConv);
    $rawTime = $tempDateArr[1];
    $finalTime = substr($rawTime, 0, 5);
    $dateFinal = "Date: " . $dateArr[2] . "-" . $dateArr[1] . "-" . $dateArr[0] . " Time(24hr format): " . $finalTime;
    if ($dateFinal === "Date: -- Time(24hr format): ") {
        $dateFinal = "No end date set";
        return $dateFinal;
    } else {
        return $dateFinal;
    }
}


#############################################################################



$sizeT = sizeof($finalPoints);
$des = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("file", "/tmp/error-output.txt", "a")
);

$startTime = microtime(true);
for ($i = 0; $i < $sizeT; $i++) {
    
    $process = proc_open('./bin/NZMGtransform', $des, $pipes);

    if (is_resource($process)) {
       
        fwrite($pipes[0], $finalPoints[$i]);
        fclose($pipes[0]);

        $coords[$i] = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        
        $return_value = proc_close($process);
    }
}
$endTime = microtime(true);
echo "grid transform " .($endTime - $startTime) ." seconds\n\n";


##############################################################

for ($i = 0; $i < $count; $i ++) {

    $tempID = $ids[$i];

    $tempStructArray[ID] = (string) $ids[$i];
    $tempStructArray[StartDate] = convertDates($startDates[$i]);
    $tempStructArray[EventCreated] = convertDates($eventCreated[$i]);
    $tempStructArray[EventModified] = convertDates($eventModified[$i]);
    $tempStructArray[Description] = (string) $descriptions[$i];
    $tempStructArray[Location] = (string) $locations[$i];
    $tempStructArray[AlternativeRoute] = (string) $alternativeRoute[$i];
    $tempStructArray[EventType] = (string) $eventType[$i];
    $tempStructArray[ExpectedResolution] = (string) $expectedResoultion[$i];
    $tempStructArray[Planned] = (boolean) $planned[$i];
    $tempStructArray[CoOrds] = $coords[$i];

    if (empty($restrictions[$i])) {
        $restrictions[$i] .= "No restrictions apply";
        $tempStructArray[Restrictions] = (string) $restrictions[$i];
    } else {
        $tempStructArray[Restrictions] = (string) $restrictions[$i];
    }

    $tempStructArray[Impact] = (string) $impact[$i];
    $tempStructArray[LocationAreas] = (string) $locationAreas[$i];

    $tempEndDate = $xml->xpath("//tns:roadEvent[tns:eventId=$tempID]/tns:endDate");
    $tempEndDateVal = $tempEndDate[0];
    $tempStructArray[EndDate] = convertDates($tempEndDateVal);

    $toBeEncoded[$i] = $tempStructArray;
}

$final = json_encode($toBeEncoded);

file_put_contents("new.json", $final);

#############################################################################

echo "\n";
echo "\n";
echo "\n";
echo "\n";
//echo "Value of Co-Ords array: " . print_r($coords) . "\n";
echo "\n";
echo "\n";
echo "\n";
echo "\n";

echo "###   END   ###   END   ###   END   ###   END   ###   END   ###   END   ";
echo "\n";
echo "\n";
