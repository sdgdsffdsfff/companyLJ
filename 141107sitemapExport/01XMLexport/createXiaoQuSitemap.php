<?php
/*
*生成小区的结构化数据
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*
*
*/
require 'header.php';   //header中有通用的数据库连接等操作

//查询租房 的结果
//$query = "SELECT * FROM decoration_company";


for(;; $curLine+=$countStepXiaoQu , $xmlFileCount++){
    //$xmlFileXiaoqu = $DOCUMENT_ROOT."XMLexport/xiaoqu$xmlFileCount.xml";
    $xmlFileXiaoqu = $XML_ROOT."xiaoqu$xmlFileCount.xml";
    echo $xmlFileXiaoqu."<br />";


    $query = "SELECT * FROM community where comm_status = 1 limit $curLine,$countStepXiaoQu"; //where SELL_OR_RENT = 103100000002
    //$query = "SELECT * FROM era_house where SELL_OR_RENT = 103100000002 limit $curLine,$countStep"; 
    $result = $db->query($query);
    if ($result) {   
        //echo  $db->affected_rows." lines can get.";  功能与下面两行相同
        $num_results = $result->num_rows;
        if($num_results <= 0 ){
            echo "results_lines<=0";
            break;
        }
        echo "<p>Number of lines: ".$num_results."</p>";

    } else {
        //echo "An error has occurred.  The item was not added.";
        break;
    }

    //XML输出设置DOM
    $doc = new DOMDocument("1.0","UTF-8");  //编码加在这里才有效！！
    $doc->formatOutput = true;
    

    $urlset = $doc->createElement( "urlset" );
    $doc->appendChild( $urlset );

    for ($i=0; $i < $num_results; $i++) {
        $row = $result->fetch_assoc();  //取得每一行  这里的row取的是community的数据
        
        $xiaoQuID = $row['comm_code'];
        $shangQuanID= ($row['bbd_id']);

        $queryEraHouse = "SELECT SE_CREATE_TIME,IF_SCHOOL_HOUSE FROM era_house where COMMUNITY_CODE = '$xiaoQuID' limit 1";
        $resultEraHouse = $db->query($queryEraHouse);   //query命令要有单引号
        $rowEraHouse = $resultEraHouse->fetch_assoc();

        $queryEraCommunityComments = "SELECT comment,bdname FROM era_community_comments where community_code = '$xiaoQuID' limit 1";
        $resultEraCommunityComments = $db->query($queryEraCommunityComments);   //query命令要有单引号
        $rowEraCommunityComments = $resultEraCommunityComments->fetch_assoc();    

        $queryDistrictBusiness = "SELECT bbd_name FROM district_business where bbd_id = '$shangQuanID' limit 1";
        $resultDistrictBusiness = $db->query($queryDistrictBusiness);   //query命令要有单引号
        $rowDistrictBusiness = $resultDistrictBusiness->fetch_assoc(); 
        
        //地铁这里还可以修改
        $queryCommunityRelateSubwaylineWalktime = "SELECT * FROM community_relate_subwayline_walktime where community_code = '$xiaoQuID' limit 1";
        $resultCommunityRelateSubwaylineWalktime = $db->query($queryCommunityRelateSubwaylineWalktime);   //query命令要有单引号，只列了
        $rowCommunityRelateSubwaylineWalktime = $resultCommunityRelateSubwaylineWalktime->fetch_assoc(); 



        $url = $doc->createElement( "url" );

        //loc和data都是url的下一级 11
        //loc大标签，类型为URL地址，最大长度256个字符 必须符合正则表达式(http://)(.+)
        $loc = $doc->createElement( "loc" );
        //$zhujianString = $row['HS_ID'];
        //$zhujianCityString = strtolower(substr($zhujianString,0,2));
        $loc->appendChild(
            //$doc->createTextNode("http://".$zhujianCityString.".lianjia.com/xiaoqu/".
            $doc->createTextNode("http://bj.lianjia.com/xiaoqu/".
                $xiaoQuID."/")
        );
        $url->appendChild( $loc );   //url中加入loc
        

        //data  11
        $data = $doc->createElement( "data" );


        //data-RealEstate  11
        $RealEstate =  $doc->createElement( "RealEstate" );
        
        //data-RealEstate-domain指定值
        $domain=$doc->createElement("domain");
        $domain->appendChild(
            $doc->createTextNode("房产")
        );
        $RealEstate->appendChild( $domain ); 


        //data-RealEstate-type指定值
        $type=$doc->createElement("type");
        $type->appendChild(
            $doc->createTextNode("HouseProperty")
        );
        $RealEstate->appendChild( $type ); 


        //data-RealEstate-name   community表中有相关信息
        //小区名，小区名 11
        $name=$doc->createElement("name");
        //$row['COMMUNITY_CODE']
        $name->appendChild(
            $doc->createTextNode($row['comm_name'])
        );
        $RealEstate->appendChild( $name );

        
        //data-RealEstate-lastmod community表中有这一列，可以直接调用
        //缺少日期和时间中间的T 有没有截断的函数  11
        $lastmod=$doc->createElement("lastmod");
        //$type1=gettype($row['LAST_MODIFY_TIME']);


        if( empty($row['last_modify_time']) ){
            $lastModTimeStringPre = "2014-07-11";
            $lastModTimeStringPost = "00:00:00";
        }else{
            $lastModTimeString = (string)$row['last_modify_time'];
            $lastModTimeStringPre = substr($lastModTimeString,0,10);
            $lastModTimeStringPost= substr($lastModTimeString,11,8);
            //TO_DATE('字符串'，'YYYY-MM-DD HH24：MI：SS')
        }
        $lastmod->appendChild(
            $doc->createTextNode($lastModTimeStringPre."T".$lastModTimeStringPost)
        );
        $RealEstate->appendChild( $lastmod );

        
        //data-RealEstate-publishTime表中有这一列，可以直接调用
        //缺少日期和时间中间的T     01
        if(!empty($rowEraHouse['SE_CREATE_TIME'])){
            $publishTime=$doc->createElement("publishTime");
            $publishTimeString = (string)$rowEraHouse['SE_CREATE_TIME'];
            $publishTimeStringPre = substr($publishTimeString,0,10);
            $publishTimeStringPost= substr($publishTimeString,11,8);

            $publishTime->appendChild(
                $doc->createTextNode($publishTimeStringPre."T".$publishTimeStringPost)
            );
            $RealEstate->appendChild( $publishTime );
        }
        

        //data-RealEstate-changefreq   01
        //有效值为：always、hourly、daily、weekly、monthly、yearly、never选哪一个？
        $changefreq=$doc->createElement("changefreq");
        $changefreq->appendChild(
            $doc->createTextNode("hourly")
        );
        $RealEstate->appendChild( $changefreq );


        //data-RealEstate-ResidenceName,暂时只有COMMUNITY_CODE
        //小区名，小区详情页标题  11  和小区名一样？
        $ResidenceName=$doc->createElement("ResidenceName");

        $ResidenceName->appendChild(
            $doc->createTextNode( $row['comm_name'] )
        );
        $RealEstate->appendChild( $ResidenceName );


        //data-RealEstate-Url    11
        //小区url,类型为URL地址，自己生成
        $Url=$doc->createElement("Url");
        $Url->appendChild(
            $doc->createTextNode("http://bj.lianjia.com/xiaoqu/".
                $xiaoQuID."/")
        );
        $RealEstate->appendChild( $Url );

    
        //data-RealEstate-PropertyCompany
        //物业公司，暂时没找到    11
        $PropertyCompany=$doc->createElement("PropertyCompany");
        $PropertyCompany->appendChild(
            $doc->createTextNode($row['property_company_name'])
        );
        $RealEstate->appendChild( $PropertyCompany );

        //?????
        //data-RealEstate-HouseType   $row['HOUSE_TYPE']需要转化成汉字
        //物业类型,住宅、商住HOUSE_TYPE  11
        $HouseType=$doc->createElement("HouseType");   //不知道编号对应的类型
        /*
        switch(trim($rowEraHouse['HOUSE_TYPE'])) {
            case "107500000001" :$houseTypeString="车库";break;
            case "107500000002" :$houseTypeString="公寓";
            case "107500000003" :$houseTypeString="普通住宅";
            case "107500000004" :$houseTypeString="别墅";
            case "107500000005" :$houseTypeString="四合院";
            case "107500000006" :$houseTypeString="写字楼"; 
            case "107500000007" :$houseTypeString="商业";
            case "107500000008" :$houseTypeString="工业厂房";
            case "107500000009" :$houseTypeString="经济适用房";
            case "107500000010" :$houseTypeString="综合";
            case "107500000013" :$houseTypeString="商铺";
            default : $houseTypeString="其他"; break;
        }
        */   //是否需要switch语句转换？
        $HouseType->appendChild(
            //$doc->createTextNode($row['build_type'])
            $doc->createTextNode("住宅")
        );
        $RealEstate->appendChild( $HouseType );


        //data-RealEstate-HouseAge
        //FINISH_YEAR建筑年代   11
        $HouseAge=$doc->createElement("HouseAge");
        $HouseAge->appendChild(
            $doc->createTextNode($row['comm_finished_year']."年")
        );
        $RealEstate->appendChild( $HouseAge );


        //data-RealEstate-SchoolDistrict
        //学区房，有效值：是/否/未知   11
        $SchoolDistrict=$doc->createElement("SchoolDistrict");

        if($rowEraHouse['IF_SCHOOL_HOUSE']==1){
            $is_school_house_str="是";
        }elseif($rowEraHouse['IF_SCHOOL_HOUSE']==0){
            $is_school_house_str="否";
        }else{
            $is_school_house_str="未知";
        }
        $SchoolDistrict->appendChild(
            $doc->createTextNode($is_school_house_str)
        );
        $RealEstate->appendChild( $SchoolDistrict );


        //data-RealEstate-Developers
        //开发商，应该需要联表查询   11
        $Developers=$doc->createElement("Developers");
        if(empty($row['developer_name'])){
            $developersString = "未知";
        }else{
            $developersString = trim($row['developer_name']);
        }
        $Developers->appendChild(
            $doc->createTextNode($developersString)
        );
        $RealEstate->appendChild( $Developers );


        //data-RealEstate-Introduce
        //小区简介，字符串   11
        $Introduce=$doc->createElement("Introduce");
        if(empty($rowEraCommunityComments['comment'])){
            $introduceString = "未知";
        }else{
            $introduceString = $rowEraCommunityComments['comment'];
        }
        $Introduce->appendChild(
            $doc->createTextNode($introduceString)
        );
        $RealEstate->appendChild( $Introduce );


        //data-RealEstate-Price
        //小区平均房价，整数   11
        $Price=$doc->createElement("Price");
        $Price->appendChild(
            $doc->createTextNode( (int)$row['comm_unit_price'])
        );
        $RealEstate->appendChild( $Price );


        //data-RealEstate-VolumeRate   11
        //容积率，精确到小数点后两位，类型为小数，需要联表
        $VolumeRate=$doc->createElement("VolumeRate");
        $VolumeRate->appendChild(
            $doc->createTextNode($row['cubage_rate'])
        );
        $RealEstate->appendChild( $VolumeRate );


        //data-RealEstate-TotalConArea   总建面可能有问题，需要联表
        //总建面，整数   11
        $TotalConArea=$doc->createElement("TotalConArea");
        $TotalConArea->appendChild(
            $doc->createTextNode((int)$row['bulid_area'])
        );
        $RealEstate->appendChild( $TotalConArea );


        //data-RealEstate-TotalHoushold
        //总户数,类型为整数，可能要联表   01
        $TotalHoushold=$doc->createElement("TotalHoushold");
        $TotalHoushold->appendChild(
            $doc->createTextNode($row['house_amount'])
        );
        $RealEstate->appendChild( $TotalHoushold );


        //data-RealEstate-GreenRate
        //绿化率，需要联表    11
        $GreenRate=$doc->createElement("GreenRate");
        $GreenRate->appendChild(
            $doc->createTextNode((int)$row['virescence_rate'])
        );
        $RealEstate->appendChild( $GreenRate );


        //data-RealEstate-TotalBuilding
        //楼栋总数，小区建筑数，数字，联表   01
        $TotalBuilding=$doc->createElement("TotalBuilding");
        $TotalBuilding->appendChild(
            $doc->createTextNode( $row['bulid_count'] )
        );
        $RealEstate->appendChild( $TotalBuilding );


        //data-RealEstate-SurroundFacility   联表  1+   需要联什么表？
        //周边设施，交通、餐饮娱乐、学校医院、景观设施等（受人关注且欢迎的设施），如：XX中学 双榆树公园 华联商厦 
        //丽亭华苑酒店等。一个标签填写一个设施，多个设施允许出现多个标签，最少出现1次 不限制最多出现次数，类型为字符串
        $SurroundFacility=$doc->createElement("SurroundFacility");
        $SurroundFacility->appendChild(
            $doc->createTextNode("未知")
        );
        $RealEstate->appendChild( $SurroundFacility );

        /*
        //data-RealEstate-BadSurroundFacility   0+
        //嫌恶设施，如加油站、垃圾场等。一个标签一个设施，多个设施运行出现多个标签，
        //最少出现0次 不限制最多出现次数，类型为字符串
        $BadSurroundFacility=$doc->createElement("BadSurroundFacility");
        $BadSurroundFacility->appendChild(
            $doc->createTextNode("")
        );
        $RealEstate->appendChild( $BadSurroundFacility );
        */


        if(!empty($row['newHsCount'])) {
            //data-RealEstate-onsale_num  01
            //在售房源数,是指同小区？类型为整数
            $onsale_num=$doc->createElement("onsale_num");
            $onsale_num->appendChild(
                $doc->createTextNode($row['newHsCount'])
            );
            $RealEstate->appendChild( $onsale_num );
        }


        if(!empty($row['newHrCount'])){
            //data-RealEstate-onrent_num   01
            //在租房源数，类型为整数  
            $onrent_num=$doc->createElement("onrent_num");
            $onrent_num->appendChild(
                $doc->createTextNode($row['newHrCount'])
            );
            $RealEstate->appendChild( $onrent_num );
        }



        //data-RealEstate-Place  需要扩展
        $Place=$doc->createElement("Place");   //  11

                
        //data-RealEstate-Place-City   11
        //城市，城市命名无需在后方加市，直接写XX；如果没有城市内容，填写“未知”
        $City=$doc->createElement("City");
        switch($row['city_id']) {
            case "110000" :$cityString="北京";break;
            case "510100" :$cityString="成都";break;
            case "210200" :$cityString="大连";break;
            case "330100" :$cityString="杭州";break;
            case "320100" :$cityString="南京";break;
            case "370200" :$cityString="青岛";break;
            case "120000" :$cityString="天津";break;
            case "310000" :$cityString="上海";break; 
            default :      $cityString="未知";break;          
        }
        $City->appendChild(
            $doc->createTextNode( $cityString )
        );
        $Place->appendChild( $City );

        //data-RealEstate-Place-District   11   不至于要海量switch吧？？
        //区，区域名称无需在后方加区，直接写XX；如果没有区域内容，填写“未知”
        $District=$doc->createElement("District");
        if(empty($rowEraCommunityComments['bdname'])){
            $districtString = "未知";
        }else{
            $districtString = $rowEraCommunityComments['bdname'];
        }
        
        $District->appendChild(
            $doc->createTextNode($districtString)   //需要联一个找区的表$['bd_id']城区ID
        );
        $Place->appendChild( $District );


        //data-RealEstate-Place-LocalBusiness    联表查，应该可以用小区地址
        //商圈，如果没有商圈内容，填写“未知”,字符串  11  只有商圈ID？
        $LocalBusiness=$doc->createElement("LocalBusiness");
        if(empty($rowDistrictBusiness['bbd_name'])) {
            $localBusinessString = "未知";
        }else{
            $localBusinessString =$rowDistrictBusiness['bbd_name'];
        }
        $LocalBusiness->appendChild(
            $doc->createTextNode($localBusinessString)//$['bbd_id']商圈ID
        );
        $Place->appendChild( $LocalBusiness );


        //data-RealEstate-Place-Street
        //街道；如果没有街道内容，填写“未知”，字符串  11
        $Street=$doc->createElement("Street");
        $streetString = trim($row['comm_addr']);
        if(empty($streetString)){
            $streetString = "未知";
        }
        $Street->appendChild(
            $doc->createTextNode($streetString)
        );
        $Place->appendChild( $Street );

        //data-RealEstate-Place-Map
        //地图页面,字符串  01   需要联表 //$row['soso_map']
        $Map=$doc->createElement("Map");
        $Map->appendChild(
            $doc->createTextNode("http://bj.lianjia.com/xiaoqu/".
                $xiaoQuID."/#sosoB")
        );
        $Place->appendChild( $Map );

        //data-RealEstate-Place-Coordinate 经纬度坐标，格式AAA,BBB，其中AAA为经度坐标，BBB纬度坐标，中间以”,“隔开；
        //无数据请填写”0“，字符串，必须符合正则表达式(\d+\.\d+,\d+\.\d+)|(\d+,\d+\.\d+)|(\d+\.\d+,\d+)|(\d+,\d+)|0   11
        //用哪种地图？
        $Coordinate=$doc->createElement("Coordinate");
        if(empty($row['googel_lo'])){
            $jingDuString = "0";
        }else{
            $jingDuString = $row['googel_lo'];
        }
        if(empty($row['google_la'])){
            $weiDuString = "0";
        }else{
            $weiDuString = $row['google_la'];
        }
        $Coordinate->appendChild(
            $doc->createTextNode($jingDuString.",".$weiDuString)
        );
        $Place->appendChild( $Coordinate );

        //data-RealEstate-Place-CoordinateSys
        //地图坐标系 11   字符串，有效值为：1、2、3、4、5、6、7、8、9、0
        $CoordinateSys=$doc->createElement("CoordinateSys");
        $coordinateSysString = "3";
        $CoordinateSys->appendChild(
            $doc->createTextNode($coordinateSysString)
        );
        $Place->appendChild( $CoordinateSys );
        
        //Place扩展完成
        $RealEstate->appendChild( $Place );


        for($subwayi=1;$subwayi<16;$subwayi++){
            if(!empty($rowCommunityRelateSubwaylineWalktime["line_$subwayi"])){
                        //data-RealEstate-SubwayInfo  需要扩展
                $SubwayInfo=$doc->createElement("SubwayInfo");    //0+最多任意次

                //data-RealEstate-SubwayInfo-SubwayLine   只有是否临近地铁，但是没有地铁线
                //地铁线路 11  字符串
                $SubwayLine=$doc->createElement("SubwayLine");
                $SubwayLine->appendChild(
                    $doc->createTextNode($subwayi."号线")
                );


                $SubwayInfo->appendChild( $SubwayLine );
                //community_relate_subwayline_walktime 这个表

                //data-RealEstate-SubwayInfo-SubwayStation
                //地铁站的站名，类型为字符串  11  
                $SubwayStation=$doc->createElement("SubwayStation");
                $SubwayStation->appendChild(
                    $doc->createTextNode($localBusinessString)
                );
                $SubwayInfo->appendChild( $SubwayStation );


                //data-RealEstate-SubwayInfo-Distance
                //距离，小区和该地铁站的距离   整数  11
                $Distance=$doc->createElement("Distance");
                $Distance->appendChild(
                    $doc->createTextNode(100*$rowCommunityRelateSubwaylineWalktime["line_$subwayi"])
                );
                $SubwayInfo->appendChild( $Distance );

                //SubwayInfo扩展完成
                $RealEstate->appendChild( $SubwayInfo );
            }   
        }

        $data->appendChild( $RealEstate );  //end-data-RealEstate

        $url->appendChild($data);

        $urlset->appendChild( $url );   //终于理解了，是在最后插入整个url模块
    } 

    echo $doc->save($xmlFileXiaoqu)." Bytes";
}

    
require 'footer.php';
?>
</body>
</html>
