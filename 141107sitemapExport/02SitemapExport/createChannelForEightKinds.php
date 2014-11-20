<?php
/*
*生成8个城市各个频道数据
*全国大首页   http://www.lianjia.com/
*城市首页    http://城市简拼.lianjia.com/
*二手房城市列表页    http://城市简拼.lianjia.com/ershoufang/
*小区频道页   http://城市简拼.lianjia.com/xiaoqu/
*学区房频道页  http://城市简拼.lianjia.com/school/
*学区房学校列表页    http://城市简拼.lianjia.com/school/list/
*租房频道页   http://城市简拼.lianjia.com/zufang/
*经纪人频道页  http://城市简拼.lianjia.com/jingjiren/
*问答频道页   http://城市简拼.lianjia.com/ask/
*资讯频道页   http://城市简拼.lianjia.com/news/
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*
*
*/

require 'header.php'; 

foreach ($city as $eachCity => $cityInfo) {
    $urlPost = $cityInfo['2'];    //循环要生成表的种类
    
    $doc = new DOMDocument("1.0","UTF-8");
    $doc->formatOutput = true;
    $urlset = $doc->createElement( "urlset" );
    $doc->appendChild( $urlset );

    foreach($city as $eachCity02 => $cityInfo02){   //循环城市
        $cityUrlPre = $cityInfo02['1'];                       
        $cityUrlPreFull = "http://".$cityUrlPre.".lianjia.com/";

        $url = $doc->createElement( "url" );
        $loc = $doc->createElement( "loc" );

        $loc->appendChild(
            $doc->createTextNode($cityUrlPreFull.$urlPost."/")
        );
        $url->appendChild( $loc );   //url中加入loc
        
        createThreeAutoTag($doc,$url); 

        $urlset->appendChild( $url );

    }

    if($urlPost == 'school/list'){
        $urlPost=substr($urlPost,0,6).substr($urlPost,7,4);
    }

    $urlPost = ucfirst($urlPost);
    $xmlFileChannel = $xmlRootChannel."channel$urlPost".".xml";
    echo $xmlFileChannel;
    echo $doc->save( $xmlFileChannel )." Bytes"."<br />";

}  


$doc = new DOMDocument("1.0","UTF-8");
$doc->formatOutput = true;
$urlset = $doc->createElement( "urlset" );
$doc->appendChild( $urlset );


$url = $doc->createElement( "url" );
$loc = $doc->createElement( "loc" );

$loc->appendChild(
    $doc->createTextNode("http://www.lianjia.com/")
);
$url->appendChild( $loc );   //url中加入loc

createThreeAutoTag($doc,$url); 

$urlset->appendChild( $url );


foreach ($city as $eachCity => $cityInfo) {
    $cityUrlPre = $cityInfo['1'];
    $cityUrlPreFull = "http://".$cityUrlPre.".lianjia.com/";

    $url = $doc->createElement( "url" );
    $loc = $doc->createElement( "loc" );

    $loc->appendChild(
        $doc->createTextNode($cityUrlPreFull)
    );
    $url->appendChild( $loc );   //url中加入loc
    
    createThreeAutoTag($doc,$url); 

    $urlset->appendChild( $url );
}

$xmlFileChannel = $xmlRootChannel."channelCity.xml";
echo $xmlFileChannel;
echo $doc->save( $xmlFileChannel )." Bytes"."<br />";

?>
</body>
</html>