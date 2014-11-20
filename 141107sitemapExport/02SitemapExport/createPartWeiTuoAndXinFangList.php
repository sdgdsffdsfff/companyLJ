<?php
/*
业主委托页面，缺南京  
    http://bj.lianjia.com/yezhu/
    http://tj.lianjia.com/yezhu/
    http://qd.lianjia.com/yezhu/
    http://dl.lianjia.com/yezhu/
    http://cd.lianjia.com/yezhu/
    http://sh.lianjia.com/yezhu/
    http://hz.lianjia.com/yezhu/
新房频道，缺杭州、成都，上海  
    http://bj.lianjia.com/xinfang/
    http://dl.lianjia.com/xinfang/
    http://tj.lianjia.com/xinfang/
    http://nj.lianjia.com/xinfang/
    http://bj.lianjia.com/zufang/hezufang/
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

foreach ($city as $eachCity => $cityInfo) {
    $cityID = $cityInfo['0'];
    $cityShort = $cityInfo['1'];

    if( $cityID != '320100' ){
        $url = $doc->createElement( "url" );

        $loc = $doc->createElement( "loc" );
        $urlString = "http://".$cityShort.".lianjia.com/yezhu/";
        $loc->appendChild(
            $doc->createTextNode($urlString)
        );
        $url->appendChild( $loc );   //url中加入loc
        
        createThreeAutoTag($doc,$url); 

        $urlset->appendChild( $url );   
    }
}

$xmlFileWeiTuoList = $xmlRootWeiTuoAndXinFangList."partWeiTuoList.xml";
echo $xmlFileWeiTuoList;
echo $doc->save( $xmlFileWeiTuoList )." Bytes"."<br />";


//生成5个城市的新房频道
$doc = new DOMDocument("1.0","UTF-8");
$doc->formatOutput = true;
$urlset = $doc->createElement( "urlset" );
$doc->appendChild( $urlset );

foreach ($city as $eachCity => $cityInfo) {
    $cityID = $cityInfo['0'];
    $cityShort = $cityInfo['1'];

    if( $cityShort != 'hz' and $cityShort != 'cd' and $cityShort != 'sh' and $cityShort != 'qd'){
        $url = $doc->createElement( "url" );

        $loc = $doc->createElement( "loc" );
        $urlString = "http://".$cityShort.".lianjia.com/xinfang/";
        $loc->appendChild(
            $doc->createTextNode($urlString)
        );
        $url->appendChild( $loc );   //url中加入loc
        
        createThreeAutoTag($doc,$url); 

        $urlset->appendChild( $url );   
    }
}


$url = $doc->createElement( "url" );

$loc = $doc->createElement( "loc" );
$urlString = "http://bj.lianjia.com/zufang/hezufang/";
$loc->appendChild(
    $doc->createTextNode($urlString)
);
$url->appendChild( $loc );   //url中加入loc

createThreeAutoTag($doc,$url); 

$urlset->appendChild( $url );   


$xmlFileXinFangList = $xmlRootWeiTuoAndXinFangList."partXinFangList.xml";
echo $xmlFileXinFangList;
echo $doc->save( $xmlFileXinFangList )." Bytes"."<br />";

?>