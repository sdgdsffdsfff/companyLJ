<?php
/*
*http://bj.lianjia.com/zufang/hezufang/d区县id/
*
*
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*/
require 'header.php'; 

$doc = new DOMDocument("1.0","UTF-8");
$doc->formatOutput = true;
$urlset = $doc->createElement( "urlset" );
$doc->appendChild( $urlset );

$cityID = "110000";

$query = "SELECT bd_id FROM district where city_id = '$cityID'"; 
$result = $db->query($query);

if ($result) {   
    $num_results = $result->num_rows;
    if($num_results <= 0 ){
        echo "Number of lines is zero";
    }
    echo "<p>Number of lines: ".$num_results."</p>";
} else {
    echo "An error has occurred.  The item was not added.";
    break;
}

while($row = $result->fetch_assoc()){
    $url = $doc->createElement( "url" );
    $loc = $doc->createElement( "loc" );
    $quXianID = $row['bd_id'];
    $quXianUrl = "http://bj.lianjia.com/zufang/hezufang/d$quXianID"."/";
    $loc->appendChild(
        $doc->createTextNode($quXianUrl)
    );
    $url->appendChild( $loc );   //url中加入loc
    
    createThreeAutoTag($doc,$url); 

    $urlset->appendChild( $url );

}

$xmlFilePartChannelAndQuXian = $xmlRootPartChannelAndQuXian."partZiRuList.xml";
echo $xmlFilePartChannelAndQuXian;
echo $doc->save( $xmlFilePartChannelAndQuXian )." Bytes"."<br />";

?>