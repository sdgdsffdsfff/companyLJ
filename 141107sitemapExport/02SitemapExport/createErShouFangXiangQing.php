<?php
/*
*生成8个城市二手房详情数据
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*
*
*/
require 'header.php';   //header中有通用的数据库连接等操作
    

for( ;; $curLine+=$countStepErShouFangXiangQing , $xmlFileCount++ ){
    $xmlFileErShouFang = $xmlRootXiangQing."erShouFangXiangQing$xmlFileCount.xml";
    echo $xmlFileErShouFang."<br />";

    //查询二手房 的结果
    //$query = "SELECT * FROM era_house where SELL_OR_RENT = 103100000002 limit $curLine,$countStep";  //偏移量，步长
    $query = "SELECT HS_ID,DATE(LAST_MODIFY_TIME) AS Date FROM era_house where SELL_OR_RENT = 103100000001 and STATUS = 105000000001 limit $curLine,$countStepErShouFangXiangQing"; 
    $result = $db->query($query);
    //查询结果判断
    if ($result) {   
        //echo  $db->affected_rows." lines can get.";  功能与下面两行相同
        $num_results = $result->num_rows;
        if($num_results <= 0 ){
            break;
        }
        echo "<p>Number of lines: ".$num_results."</p>";
    } else {
        echo "An error has occurred. The item was not added.";
        break;
    }
    //var_dump($result);
    //die;
    
    //XML输出设置DOM
    $doc = new DOMDocument("1.0","UTF-8");  //编码加在这里才有效!
    $doc->formatOutput = true;
    $urlset = $doc->createElement( "urlset" );
    $doc->appendChild( $urlset );


    for ($i=0; $i < $num_results; $i++) {
        $row = $result->fetch_assoc(); //取得每一行最初query的结果

        $zhujianString = trim($row['HS_ID']);  //CDCH86400243
        $zhujianCityString = strtolower(substr($zhujianString,0,2));  //cd

        $url = $doc->createElement( "url" );
        //loc和data都是url的下一级 11
        //loc大标签，类型为URL地址，最大长度256个字符 必须符合正则表达式(http://)(.+)
        $loc = $doc->createElement( "loc" );

        $loc->appendChild(
            $doc->createTextNode("http://".$zhujianCityString.".lianjia.com/ershoufang/".
                $zhujianString.".shtml")
        );
        $url->appendChild( $loc );   //url中加入loc
        

        
        //data-RentalInfo-lastmod 表中有这一列，可以直接调用  11
        $lastmod=$doc->createElement("lastmod");
        $lastModTimeString = (string)$row['Date'];
        $lastmod->appendChild(
            $doc->createTextNode($lastModTimeString)
        );
        $url->appendChild( $lastmod );


        //data-RentalInfo-publishTime  表中有这一列，可以直接调用  11
        $changefreq=$doc->createElement("changefreq");
        $changefreq->appendChild(
            $doc->createTextNode("daily")
        );
        $url->appendChild( $changefreq );


        //data-RentalInfo-priority   01   这个如何判断？
        //指定此链接相对于其他链接的优先权比值，此值定于0.0-1.0之间
        $priority=$doc->createElement("priority");
        $priorityFloat=1.0;
        $priorityFloatFormat = number_format($priorityFloat, 2, '.', '');  
        $priority->appendChild(
            $doc->createTextNode( $priorityFloatFormat )
        );
        $url->appendChild( $priority ); 


        $urlset->appendChild( $url );   //终于理解了，是在最后插入整个url模块
    }


    echo $doc->save($xmlFileErShouFang)." Bytes"."<br />";
}  
require 'footer.php';

?>
</body>
</html>