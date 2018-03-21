<?php

//user input
$file = $_REQUEST["file"];
$date = $_REQUEST["date"];
$time = $_REQUEST["time"];

//test input
//$file = "files/brislington_no2.xml";
//$date = "2016";
//$time="10:00:00";
echo createJSONString($file, $time, $date);

function createJSONString($inputFilePath, $selectedTime, $selectedDate) {
    $xml = simplexml_load_file($inputFilePath);
    $readingArray = $xml->xpath("//reading[@time='$selectedTime' and contains(@date,'$selectedDate')]");
//    print_r($resultArr);

//need to sort array
    $rows = array();
    $table = array();
    $table["cols"] = array(
        array("label" => "date", "type" => "date"),
        array("label" => "NO2", "type" => "number"),
        array("role" => "style", "type" => "string"),
        array("role" => "tooltip", "type" => "string", "p" => array('html' => true))
    );
    
    $colorNumber = 0;
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
        $temp[] = array("v" => selectColor($no2val)); //add colour

        $rows[] = array("c" => $temp); //add row to new column
    }
    $table["rows"] = $rows;
    
    return json_encode($table);
}

function selectColor($val) {
    if ($val >= 0 && $val <= 67) {
        return "#DAF7A6";
    }
    if ($val >= 68 && $val <= 134) {
        return "#80FF00";
    }
    if ($val >= 135 && $val <= 200) {
        return "#94C800";
    }
    if ($val >= 201 && $val <= 267) {
        return "#F3F000";
    }
    if ($val >= 268 && $val <= 334) {
        return "#FFC300";
    }
    if ($val >= 335 && $val <= 400) {
        return "#F19A00";
    }
    if ($val >= 401 && $val <= 467) {
        return "#FF5F5F";
    }
    if ($val >= 468 && $val <= 534) {
        return "#FE0404";
    }
    if ($val >= 535 && $val <= 600) {
        return "#900C3F";
    }
    if ($val >= 601) {
        return "#BE02E3";
    }
}

?>