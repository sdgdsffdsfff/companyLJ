<?php
/*
*http://bj.lianjia.com/sold/房源id.shtml
*http://tj.lianjia.com/sold/房源id.shtml
*http://nj.lianjia.com/sold/房源id.shtml
*http://hz.lianjia.com/sold/房源id.shtml
*
*
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*/
require 'header.php';   //header中有通用的数据库连接等操作


foreach($cityPart as $eachCity => $cityInfo){   //针对每个城市的循环
    $cityID = $cityInfo['0'];
    $cityShort = $cityInfo['1'];
    
    $query = "SELECT HS_ID FROM era_house where CITY_ID = '$cityID' and STATUS = 105000000001";  //query里的limit不会降低性能，所以去掉limit无所谓
    $result = $db->query($query);
    if ($result) {   
        //echo  $db->affected_rows." lines can get.";  功能与下面两行相同
        $num_results = $result->num_rows; 
        if($num_results <= 0){
            echo "Number of lines is zero , lets continue";
            continue;
        }
        echo "<p>Number of lines: ".$num_results."</p>";
    } else {
        echo "An error has occurred.  The item was not added.";
        break;
    }
    $curLine = 0;   //对每一个城市，这个值要调整为0
    $xmlFileCount = 1;

    for( ;$curLine < $num_results; $curLine+=$countStepPartChengJiaoXiangQing , $xmlFileCount++ ){
        $doc = new DOMDocument("1.0","UTF-8");  //编码加在这里才有效！！
        $doc->formatOutput = true;
        $urlset = $doc->createElement( "urlset" );
        $doc->appendChild( $urlset );


        for ($i=0 ; $i < $countStepPartChengJiaoXiangQing ; $i++){
            if( $row = $result->fetch_assoc() ){
                $url = $doc->createElement( "url" );  //url标签开始

                $loc = $doc->createElement( "loc" );
                $chengJiaoXiangQingUrl = "http://".$cityShort.".lianjia.com/sold/".$row['HS_ID'].".shtml";
                $loc->appendChild(
                    $doc->createTextNode($chengJiaoXiangQingUrl)
                );
                $url->appendChild( $loc );   //url中加入loc
                
                createThreeAutoTag($doc,$url); 

                $urlset->appendChild( $url );   //url标签结束
            }
        }

        $xmlFilePartChengJiaoXiangQing = $xmlRootPartChengJiaoXiangQing."partChengJiao".ucfirst($cityShort).$xmlFileCount.".xml";
        echo $xmlFilePartChengJiaoXiangQing."<br />";
        echo $doc->save($xmlFilePartChengJiaoXiangQing)." Bytes";
    }

}

require 'footer.php';

?>