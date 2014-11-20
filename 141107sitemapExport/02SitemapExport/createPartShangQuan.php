<?php
/*
*生成4个城市的商圈数据
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*/
require 'header.php'; 

$doc = new DOMDocument("1.0","UTF-8");
$doc->formatOutput = true;
$urlset = $doc->createElement( "urlset" );
$doc->appendChild( $urlset );

foreach ($cityPart as $eachCity => $cityInfo) {  //城市循环
    $cityID = $cityInfo['0'];
    $cityShort = $cityInfo['1'];

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
        $quXianID = $row['bd_id'];
        $queryShangQuan = "SELECT bbd_id FROM district_business where bd_id = '$quXianID'"; 

        $resultShangQuan = $db->query($queryShangQuan);

        if ($resultShangQuan) {   
            $num_results02 = $resultShangQuan->num_rows;
            if($num_results02 <= 0 ){
                echo "Number of lines is zero";   //这里不用break，通过下面的while跳出循环
            }
            echo "<p>Number of lines: ".$num_results02."</p>";
        } else {
            echo "An error has occurred.  The item was not added.";
            break;
        }

        while($rowShangQuan = $resultShangQuan->fetch_assoc()){
            $url = $doc->createElement( "url" );

            $loc = $doc->createElement( "loc" );
            $shangQuanID = $rowShangQuan['bbd_id'];
            $shangQuanUrl = "http://".$cityShort.".lianjia.com/sold/d$quXianID"."b$shangQuanID"."/";
            $loc->appendChild(
                $doc->createTextNode($shangQuanUrl)
            );
            $url->appendChild( $loc );   //url中加入loc
            
            createThreeAutoTag($doc,$url); 

            $urlset->appendChild( $url );


        }
    }
}

$xmlFilePartShangQuan = $xmlRootPartShangQuan."partShangQuan.xml";
echo $xmlFilePartShangQuan;
echo $doc->save( $xmlFilePartShangQuan )." Bytes"."<br />";



?>