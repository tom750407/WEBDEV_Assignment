<?php

$files = array("brislington.xml" => "brislington_no2.xml", "fishponds.xml" => "fishponds_no2.xml"
    , "parson_st.xml" => "parson_st_no2.xml", "rupert_st.xml" => "rupert_st_no2.xml"
    , "wells_rd.xml" => "wells_rd_no2.xml", "newfoundland_way.xml" => "newfoundland_way_no2.xml");

$path = "../files/";
foreach ($files as $inputFile => $outputFile) {
//    echo "$inputFile : $outputFile <br>";
    createNO2File($path.$inputFile, $path.$outputFile);
    echo "done: $outputFile <br>";
}

function createNO2File($inputFile, $outputFile) {
    $reader = new XMLReader();
    if(!$reader->open($inputFile)) {
        echo "could not open file: $inputFile";
        die();
    }
    
    $writer = new XMLWriter();
    $writer->openMemory();
    
    //write to document
    /*
    FORMAT:
     
<?xml version="1.0" encoding="UTF-8"?>
<data type="nitrogen dioxide">
    <location id="wells road" lat="51.427" long="-2.568">
        <reading date="13/02/2016" time="03:15:00" val="11"/>
        <reading date="13/02/2016" time="03:30:00" val="11"/>
        <reading date="13/02/2016" time="03:45:00" val="11"/>

        <!-- thouands of other rows -->

        <reading date="13/02/2017" time="16:15:00" val="35"/>

    </location>
</data>
     * 
     */
    file_put_contents($outputFile, "");
    
    
    //write to doc
    $writer->startDocument("1.0", "UTF-8"); //xml version and encoding
    $writer->setIndent(True);
    $writer->startElement("data");
    $writer->writeAttribute("type", "nitrogen dioxied");
    $writer->startElement("location");
    
    $bool = true;
    $dom = new DOMDocument;
    $flush = 0;
    
    while($reader->read() && $reader->name !== "row");
    
    while($reader->name === "row") {
        
        $xml = simplexml_import_dom($dom->importNode($reader->expand(), true));
        
        //location ONCE
        if($bool) {
            //write location attributes once
            $writer->writeAttribute("id", $xml->monitor_description->attributes()->val);
            $writer->writeAttribute("lat", $xml->lat->attributes()->val);
            $writer->writeAttribute("long", $xml->long->attributes()->val);
            
//            echo $xml->monitor_description->attributes()->val;
            $bool = false;
        }
        
        //<reading>
        $writer->startElement("reading");
        $writer->writeAttribute("date", $xml->date->attributes()->val);
        $writer->writeAttribute("time", $xml->time->attributes()->val);
        $writer->writeAttribute("val", $xml->no2->attributes()->val);
        $writer->endElement(); //</reading>
        
        
        //jump to next row
        $reader->next("row");
        $flush++;
        if($flush === 1000) {
            $flush = 0;
            file_put_contents($outputFile, $writer->flush(true), FILE_APPEND);
        }
    }
    
    $writer->endElement(); //</location>
    $writer->endElement(); //</data>
    $writer->endDocument();
    
    //closing
    file_put_contents($outputFile, $writer->flush(true), FILE_APPEND);
    $reader->close();
    
}
?>
