<?php

/*
  plan
 *  1) do xpath of:
 *      current date and location
 *  2) append return array to all rows
 *      - create json string and return
 */

//user input
$date = $_REQUEST["date"];
$time = $_REQUEST["time"];
$locations = array();
//http://php.net/manual/en/function.glob.php
foreach (glob("../files/*_no2.xml") as $filename) {
    $locations[] = $filename;
}

//test input
//$date = "14/10/2016";
//$time = "08:00:00";
//getLocationData($locations[0], $time, $date);

echo createJSONString($locations, $time, $date);

function createJSONString($locations, $selectedTime, $selectedDate) {

    #2) do json stuff like scatter chart then append all row into rows array
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

function getLocationData($location, $selectedTime, $selectedDate) {

    #1) xpath
    $xml = simplexml_load_file($location);

    //reading array should only contain 1 object
    $readingArray = $xml->xpath(
            "//reading[@date='$selectedDate' and @time='$selectedTime']"
    );

    $readingVal = 0;
    if ($readingArray[0] !== NULL) {
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

    return $rows;
}

?>