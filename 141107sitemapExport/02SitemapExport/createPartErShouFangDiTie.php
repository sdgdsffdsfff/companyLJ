<?php
/*
http://bj.lianjia.com/ershoufang/ditie/
http://bj.lianjia.com/ershoufang/ditie/li线路id/
http://bj.lianjia.com/ershoufang/ditie/li线路ids站点id/
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

for($i=1;i<19;i++){
    $subwayLine = $i;




    $query = "SELECT bd_id FROM district where city_id = '$cityID'"; 
    $result = $db->query($query);
    
    if ($result) {   
        $num_results = $result->num_rows;
        if($num_results <= 0 ){
            break;
        }
        echo "<p>Number of lines: ".$num_results."</p>";
    } else {
        echo "An error has occurred.  The item was not added.";
        break;
    }

}






    foreach ($city as $eachCity => $cityInfo) {  //城市循环
        $cityID = $cityInfo['0'];
        $cityShort = $cityInfo['1'];

        $query = "SELECT bd_id FROM district where city_id = '$cityID'"; 
        $result = $db->query($query);
        
        if ($result) {   
            $num_results = $result->num_rows;
            if($num_results <= 0 ){
                break;
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
                    break;
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
                $shangQuanUrl = "http://".$cityShort.".lianjia.com/$shangQuanCanShuCurrent".
                    "/d$quXianID"."b$shangQuanID"."/";
                $loc->appendChild(
                    $doc->createTextNode($shangQuanUrl)
                );
                $url->appendChild( $loc );   //url中加入loc
                
                createThreeAutoTag($doc,$url); 

                $urlset->appendChild( $url );


            }
        }
    }

    $xmlFileShangQuan = $xmlRootShangQuan."shangQuan".ucfirst($shangQuanCanShuCurrent).".xml";
    echo $xmlFileShangQuan;
    echo $doc->save( $xmlFileShangQuan )." Bytes"."<br />";

}

?>
</body>
</html>