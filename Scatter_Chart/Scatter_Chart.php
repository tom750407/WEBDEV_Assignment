<?php

/*
  plan
 *  1) do xpath of:
 *      current date
 *  2) create json string and return
 */

//user input
$file = $_REQUEST["file"];
$date = $_REQUEST["date"];
$time = $_REQUEST["time"];

//test input
//$file = "../files/newfoundland_way_no2.xml";
//$date = "2015";
//$time="11:00:00";

echo createJSONString($file, $time, $date);

function createJSONString($inputFilePath, $selectedTime, $selectedDate) {
    #1) xpath
    $xml = simplexml_load_file($inputFilePath);
    $readingArray = $xml->xpath("//reading[@time='$selectedTime' and contains(@date,'$selectedDate')]");

    $rows = array();
    $table = array();
    $table["cols"] = array(
        array("label" => "date", "type" => "date"),
        array("label" => "NO2", "type" => "number"),
    );

    #2) create json string and return
    foreach ($readingArray as $reading) {
        $temp = array();
        $reading = simplexml_load_string($reading->asXML());
        $date = DateTime::createFromFormat("d/m/Y H:i:s", $reading->attributes()->date . " " . $reading->attributes()->time);
        #json string for no2
        $no2val = $reading->attributes()->val;
        # create json string (for date)
        //https://developers.google.com/chart/interactive/docs/datesandtimes
        //"...months are indexed starting at zero (January is month 0, December is month 11)."
        $dateFormat = "Date(";
        $dateFormat .= date("Y", $date->format("U")) . ", ";
        $dateFormat .= (date("m", $date->format("U")) - 1) . ", ";
        $dateFormat .= date("d", $date->format("U")) . ", ";
        $dateFormat .= date("H", $date->format("U")) . ", ";
        $dateFormat .= date("i", $date->format("U")) . ", ";
        $dateFormat .= date("s", $date->format("U")) . ")";

        $temp[] = array("v" => $dateFormat); //add date
        $temp[] = array("v" => (int) $no2val); //add no2

        $rows[] = array("c" => $temp); //add row to new column
    }
    $table["rows"] = $rows;

    return json_encode($table);
}

?>