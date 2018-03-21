<?php

$inputFile = "../files/air_quality.csv";
$outputFile = "../files/wells_rd.xml";
$outputFiles = array("brislington.xml", "fishponds.xml", "parson_st.xml"
    , "rupert_st.xml", "wells_rd.xml", "newfoundland_way.xml");

if (!file_exists($inputFile)) {
    echo "input file doesnt exist:  $inputFile";
    die();
}

//open the input file
$file = fopen($inputFile, "rt");

//remove headers from csv, and use it to store the number of headers (=10)
$headers = fgetcsv($file);
$count = sizeof($headers) - 1;

//----------
//write to xml files!!
$outputStrings = array("<records>", "<records>", "<records>",
    "<records>", "<records>", "<records>");

while (($data = fgetcsv($file)) !== FALSE) {
    //create row
    $row = "\n<row>";
    for ($i = 0; $i < $count; $i++) {
        $header = trim($headers[$i]);
        $row .= "<$header val=\"" . trim($data[$i]) . "\"/>";
    }
    $row .= "</row>";

    //give row to correct string
    switch ($data[0]) {
        case 3: //bris
            $outputStrings[0] .= $row;
            break;
        case 6: //fish
            $outputStrings[1] .= $row;
            break;
        case 8: //parson
            $outputStrings[2] .= $row;
            break;
        case 9: //rupert
            $outputStrings[3] .= $row;
            break;
        case 10: //wells
            $outputStrings[4] .= $row;
            break;
        case 11: //newf
            $outputStrings[5] .= $row;
            break;
        default:
            echo "file not known $data[1]";
    }
}

for ($i = 0; $i < sizeof($outputStrings); $i++) {
    file_put_contents("../files/$outputFiles[$i]", "");
    $outputStrings[$i] .= "\n</records>";
    file_put_contents("../files/$outputFiles[$i]", $outputStrings[$i]);
}
fclose($file);
echo 'done';

?>
