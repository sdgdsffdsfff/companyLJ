<?php
/*
*二手房区县列表页    http://城市简拼.lianjia.com/ershoufang/d区县id/
*租房区县列表页      http://城市简拼.lianjia.com/zufang/d区县id/
*学区房区县列表页    http://城市简拼.lianjia.com/school/d区县id/
*经纪人区县列表页    http://城市简拼.lianjia.com/jingjiren/d区县id/
*
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*/
require 'header.php'; 

$quXianCanShu = array(
    'ershoufang','zufang','school','jingjiren','xiaoqu',
);

foreach($quXianCanShu as $quXianCanShuCurrent){
    $doc = new DOMDocument("1.0","UTF-8");
    $doc->formatOutput = true;
    $urlset = $doc->createElement( "urlset" );
    $doc->appendChild( $urlset );

    foreach ($city as $eachCity => $cityInfo) {
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
            $url = $doc->createElement( "url" );


            $loc = $doc->createElement( "loc" );
            $quXianID = $row['bd_id'];
            $quXianUrl = "http://".$cityShort.".lianjia.com/$quXianCanShuCurrent"."/d".$quXianID."/";
            $loc->appendChild(
                $doc->createTextNode($quXianUrl)
            );
            $url->appendChild( $loc );   //url中加入loc
            
            createThreeAutoTag($doc,$url); 

            $urlset->appendChild( $url );

        }
    }

    $xmlFileQuXian = $xmlRootQuXian."quXian".ucfirst($quXianCanShuCurrent).".xml";
    echo $xmlFileQuXian;
    echo $doc->save( $xmlFileQuXian )." Bytes"."<br />";

}

?>
</body>
</html>