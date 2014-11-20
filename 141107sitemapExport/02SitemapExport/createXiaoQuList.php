<?php
/*
*小区页面    http://城市简拼.lianjia.com/xiaoqu/小区id/
*小区二手房列表页    http://城市简拼.lianjia.com/xiaoqu/小区id/esf/
*小区租房列表页 http://城市简拼.lianjia.com/xiaoqu/小区id/zf/
*
*
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*/
require 'header.php'; 

$xiaoQuCanShu = array(
    array('0'=>'yemian','1'=>''),
    array('0'=>'ershoufang','1'=>'esf'),  //所有涉及到url的都在最后加slash
    array('0'=>'zufang','1'=>'zf'),
);

foreach($xiaoQuCanShu as $xiaoQuCanShuCurrent){
    $doc = new DOMDocument("1.0","UTF-8");
    $doc->formatOutput = true;
    $urlset = $doc->createElement( "urlset" );
    $doc->appendChild( $urlset );

    foreach ($city as $eachCity => $cityInfo) {
        $cityID = $cityInfo['0'];
        $cityShort = $cityInfo['1'];

        $query = "SELECT comm_code FROM community where city_id = '$cityID' and comm_status = 1"; 
        $result = $db->query($query);
        
        if ($result) {   
            $num_results = $result->num_rows;
            if($num_results <= 0 ){
                echo "Number of lines is zero.";
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
            $xiaoQuID = $row['comm_code'];

            if($xiaoQuCanShuCurrent['0']=='yemian'){
                $xiaoQuUrl = "http://".$cityShort.".lianjia.com/xiaoqu/$xiaoQuID"."/";    
            }else{

                $xiaoQuUrl = "http://".$cityShort.".lianjia.com/xiaoqu/$xiaoQuID"."/".$xiaoQuCanShuCurrent['1']."/";                    
            }

            $loc->appendChild(
                $doc->createTextNode($xiaoQuUrl)
            );
            $url->appendChild( $loc );   //url中加入loc
            
            createThreeAutoTag($doc,$url); 

            $urlset->appendChild( $url );

        }
    }

    $xmlFileXiaoQu = $xmlRootXiaoQu."xiaoQu".ucfirst($xiaoQuCanShuCurrent['0']).".xml";
    echo $xmlFileXiaoQu;
    echo $doc->save( $xmlFileXiaoQu )." Bytes"."<br />";

}

?>
</body>
</html>