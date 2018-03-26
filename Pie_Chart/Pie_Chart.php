<?php

//user input
//$location = $_GET["files"];
//$time = $_GET["times"];
//$date = $_GET["dates"];
$date = $_REQUEST["date"];
$time = $_REQUEST["time"];
$locations = array();
//http://php.net/manual/en/function.glob.php
foreach (glob("../files/*_no2.xml") as $filename) {
//    echo $filename ."<br/>";
    $locations[] = $filename;
}


//test input
//$date = "14/10/2016";
//$time = "08:00:00";
//echo "$locations[0]";
//getLocationData($locations[0], $time, $date);
echo createJSONString($locations, $time, $date);

function getLocationData($location, $selectedTime, $selectedDate) {
    $xml = simplexml_load_file($location);

    //reading array should only contain 1 object
    $readingArray = $xml->xpath(
            "//reading[@date='$selectedDate' and @time='$selectedTime']"
    );
    
    $readingVal = 0;
    if($readingArray[0] !== NULL) {
        $readingVal = $readingArray[0]->attributes()->val;
    }
    //only will be 1 location element
    $locationArray = $xml->xpath("//location");
    $locationName = $locationArray[0]->attributes()->id;


    $rows = array();
    $rows[] = array("c" => array(
        array("v" => (string) $locationName),
        array("v" => (int) $readingVal))
        );
//    $x = json_encode($rows);
//    echo $x;
//    die();

    return $rows;
}

function createJSONString($locations, $selectedTime, $selectedDate) {

    $table = array();
    $table["cols"] = array(
        array("label" => "location", "type" => "string"),
        array("label" => "NO2", "type" => "number")
    );
    
    $rows = array();

    foreach ($locations as $location) {
        $row = getLocationData($location, $selectedTime, $selectedDate);
        //append return array to all rows: https://stackoverflow.com/a/4268954
        $rows = array_merge($rows, $row);
    }
    $table["rows"] = $rows;

    return json_encode($table);
}

?>