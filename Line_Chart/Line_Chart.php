<?php

//user input
//$location = $_GET["files"];
//$time = $_GET["times"];
//$date = $_GET["dates"];
$file = $_REQUEST["file"];
$date = $_REQUEST["date"];
$time = $_REQUEST["time"];

//test input
//$file = "../files/brislington_no2.xml";
//$date = "10/9/2016";
//$time="10:00:00";
echo createJSONString($file, $time, $date);

function createJSONString($inputFilePath, $selectedTime, $selectedDate) {
    /*
      plan of action:
     *  1) get current date (user input) and the next day
     *      - need to convert string into date() and then +1 day to it
     *  2) do xpath of:
     *      if((time >= timeInput AND date==currentdate)
     *          AND (time <= timeInput AND date==(currentDate+1))  
     *      current date |--------| next date (want values inside
     * 
     *  3) do what we did in scatter chart. (REMEMBER TO SORT XPATH ARRAY)
     *      - create json string and return
     */

    #1) calc current date and next date
    list($dd, $mm, $yyyy) = sscanf($selectedDate, "%d/%d/%d");
    $tomorrow = date("d/m/Y", strtotime("+1 day", strtotime("$mm/$dd/$yyyy")));


//    echo $selectedTime . "<br>";
//    echo $selectedDate . "<br>";
//    echo $tomorrow . "<br>";
//    die();
    
    #2) xpath
    $xml = simplexml_load_file($inputFilePath);
    $readingArray = $xml->xpath(
            "//reading["
            . "(translate(@time, ':', '') >= translate('$selectedTime', ':', '') and @date='$selectedDate')"
            . "or"
            . "(translate(@time, ':', '') <= translate('$selectedTime', ':', '') and @date='$tomorrow')"
            . "]"
    );

    #3) sort array, and do json stuff like scatter chart
    usort($readingArray, "sortReadings");

    $rows = array();
    $table = array();
    $table["cols"] = array(
        array("label" => "time", "type" => "date"),
        array("label" => "NO2", "type" => "number")
//        array("role" => "style", "type" => "string")
    );

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
//        $temp[] = array("v" => selectColor($no2val)); //add colour

        $rows[] = array("c" => $temp); //add row to new column
    }
    $table["rows"] = $rows;

    return json_encode($table);
}

function sortReadings($a, $b) {
    $readingA = simplexml_load_string($a->asXML());
    $readingB = simplexml_load_string($b->asXML());

    $dateA = DateTime::createFromFormat("d/m/Y H:i:s", $readingA->attributes()->date . " " . $readingA->attributes()->time);
    $dateB = DateTime::createFromFormat("d/m/Y H:i:s", $readingB->attributes()->date . " " . $readingB->attributes()->time);

    if ($dateA == $dateB) {
        return 0;
    }
    if ($dateA > $dateB) {
        return 1;
    }
    if ($dateA < $dateB) {
        return -1;
    }
}
?>