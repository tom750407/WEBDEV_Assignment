<?php

/*
  plan
 *  1) do xpath of:
 *      current location including id, lat and long
 *  2) append return array to all rows
 *      - create json string and return
 */

$locations = array();
//http://php.net/manual/en/function.glob.php
foreach (glob("../files/*_no2.xml") as $filename) {
    $locations[] = $filename;
}

echo createJSONString($locations);

function createJSONString($locations) {

    #2) do json stuff like scatter chart then append all row into rows array
    $table = array();
    $table["cols"] = array(
        array("label" => "lat", "type" => "number"),
        array("label" => "long", "type" => "number"),
        array("label" => "location", "type" => "string")
    );

    $rows = array();

    foreach ($locations as $location) {
        $row = getLocationData($location);
        //append return array to all rows: https://stackoverflow.com/a/4268954
        $rows = array_merge($rows, $row);
    }
    $table["rows"] = $rows;

    return json_encode($table);
}

function getLocationData($location) {

    #1) xpath
    $xml = simplexml_load_file($location);

//    //reading array should only contain 1 object
//    $readingArray = $xml->xpath(
//            "//reading[@date='$selectedDate' and @time='$selectedTime']"
//    );
//
//    $readingVal = 0;
//    if ($readingArray[0] !== NULL) {
//        $readingVal = $readingArray[0]->attributes()->val;
//    }

    $locationArray = $xml->xpath("//location");
    $locationName = $locationArray[0]->attributes()->id;
    $lat = $locationArray[0]->attributes()->lat;
    $long = $locationArray[0]->attributes()->long;

    $rows = array();
    $rows[] = array("c" => array(
            array("v" => (float) $lat),
            array("v" => (float) $long),
        array("v" => (string) $locationName))
    );

    return $rows;
}

?>