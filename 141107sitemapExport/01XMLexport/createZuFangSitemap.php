<?php
/*
*生成出租房源的结构化数据
*@author:周宁桐
*@mail:zhouningtong@betafang.com
*@date:2014-11-19
*
*
*/
require 'header.php';   //header中有通用的数据库连接等操作
    

for( ;; $curLine+=$countStepZuFang , $xmlFileCount++ ){
    $xmlFileZufang = $XML_ROOT."zufang$xmlFileCount.xml";
    echo $xmlFileZufang."<br />";

    //查询租房 的结果
    //$query = "SELECT * FROM era_house where SELL_OR_RENT = 103100000002 limit $curLine,$countStep";  //偏移量，步长
    $query = "SELECT * FROM era_house where SELL_OR_RENT = 103100000002 and STATUS = 105000000001  limit $curLine,$countStepZuFang"; 
    $result = $db->query($query);
    //查询结果判断
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
    //var_dump($result);
    //die;
    

    //XML输出设置DOM
    $doc = new DOMDocument("1.0","UTF-8");  //编码加在这里才有效!
    $doc->formatOutput = true;
    $urlset = $doc->createElement( "urlset" );
    $doc->appendChild( $urlset );


    /*
    for($j=0; $j<$countStep/10; $j++){    //一次取step/10大小的数据，是需要和其他表关联的数据！！
        $row = $result->fetch_assoc();  //取得每一行最初query的结果
        //$rowFangYuanID['trim($row['HS_ID'])'] = trim($row['HS_ID']);      //保存房源ID的一维数组
        $rowXiaoQuID['trim($row['COMMUNITY_CODE']'] = trim($row['COMMUNITY_CODE']);   //保存小区编号的一维数组
    }

    $queryCommunity = "SELECT * FROM community where comm_code IN '$rowXiaoQuID'";
    $resultCommunity = $db->query($queryCommunity);
    $rowCommunity = $resultCommunity->fetch_assoc();

    $queryEraHouseComment = "SELECT * FROM era_house_comment where hid IN ('$fangYuanID', ... ... .. ..) order by seq limit 1";
    $resultEraHouseComment = $db->query($queryEraHouseComment);   //query命令变量要有单引号
    while( $rowTemp = $resultEraHouseComment->fetch_assoc()) {
        $rowEraHouseComment[$row["rowTemp"]] = $row;
    }
    */   //注释的缩进位置也很重要！不然会影响括号

    for ($i=0; $i < $num_results; $i++) {
        $row = $result->fetch_assoc();   //取得每一行最初query的结果

        $fangYuanID = trim($row['HS_ID']);
        $xiaoQuID = trim($row['COMMUNITY_CODE']);
        $agentID = trim($row['BELONG_AGENT_ID']);

        $queryEraHouseComment = "SELECT uid,comment_title,content FROM era_house_comment where hid = '$fangYuanID' order by seq limit 1";
        $resultEraHouseComment = $db->query($queryEraHouseComment);   //query命令要有单引号
        $rowEraHouseComment = $resultEraHouseComment->fetch_assoc();

        $queryCommunity = "SELECT comm_name FROM community where comm_code = '$xiaoQuID' limit 1";
        $resultCommunity = $db->query($queryCommunity);
        $rowCommunity = $resultCommunity->fetch_assoc();

        $queryEraHousePic = "SELECT big_pic_path FROM era_house_pic where house_code = '$fangYuanID' order by dis_no";
        $resultEraHousePic = $db->query($queryEraHousePic);
        $rowEraHousePic = $resultEraHousePic->fetch_assoc();

        $agentID = trim($rowEraHouseComment['uid']);
        if(!isset($agentID)){
            $agentID = trim($row['BELONG_AGENT_ID']);
        }

        $queryEraAgent = "SELECT user_name,mobile,four_phone_two,four_phone_one,small_photo_path,code_pic_path FROM era_agent where user_code = '$agentID' limit 1";
        $resultEraAgent = $db->query($queryEraAgent);
        $rowEraAgent = $resultEraAgent->fetch_assoc();


        //变量准备
        $picUrlPre = "http://image.homelink.com.cn/";
        if( !isset( $rowEraHousePic['big_pic_path'] )) {
            $covorImageString = "http://static-xeq.lianjia.com/static/asset/img/new-version/default_block.png";
        }else{
            $covorImageString = $picUrlPre.trim($rowEraHousePic['big_pic_path']);
        }


        //covorImageString
        //$covorImageString = trim($rowEraHousePic['big_pic_path']);

        $brokerName = $rowEraAgent['user_name'];
        if(!isset($brokerName)){
            $brokerName = "链家网";
        }

        $brokerTel = $rowEraAgent['mobile'];
        if(!isset($brokerTel)){
            $brokerTel = $rowEraAgent['four_phone_two'];
        }
        if(!isset($brokerTel)){
            $brokerTel = $rowEraAgent['four_phone_one']; 
        }
        if(!isset($brokerTel)){
            $brokerTel = "4007-001001";
        }
        $brokerTelShort = substr($brokerTel, 0,11); //手机号会不会越界？

        $zhujianString = $row['HS_ID'];  //CDCH86400243
        $zhujianCityString = strtolower(substr($zhujianString,0,2));  //cd

        $roomNum = (int)$row['ROOM_NUM'];
        if( $roomNum <=0 ){
            $roomNum = 0;
        }
        $hallNum = (int)$row['HALL_NUM'];
        if( $hallNum <=0 ){
            $hallNum = 0;
        }

        switch( $hallNum + $roomNum ){
            case '1':$houseStruString = "一居";break;
            case '2':$houseStruString = "两居";break;
            case '3':$houseStruString = "三居";break;
            case '4':$houseStruString = "四居";break;
            case '5':$houseStruString = "五居";break;
            case '6':$houseStruString = "六居";break;
            case '7':$houseStruString = "七居";break;
            case '8':$houseStruString = "八居";break;
            case '9':$houseStruString = "九居";break;
            case '10':$houseStruString = "十居";break;
            default :$houseStruString = "其它";break;
        }

        switch(trim($row['HOUSE_STATUS'])) {
        case "103300000001" : $HouseStatusString="闲置"; break;
        case "103300000002" : $HouseStatusString="自住"; break;
        case "103300000003" : $HouseStatusString="出租"; break;
        default : $HouseStatusString="其它"; break;
        }

        $RealestateNameString = $rowCommunity['comm_name'];
        if ( empty($RealestateNameString)) {
            $RealestateNameString = "未知";
        }

        $RoomOriString = trim((string)$row['HO_ID']);
        //$RoomOriStringShort = substr($RoomOriStringWhole, 0,)
        $RoomOriStringNew = "";
        foreach (explode(",", $RoomOriString) as $ori) {
            switch ($ori) {
                case ' ':break;
                case '100500000001': $RoomOriStringNew .= "东"; break;
                case '100500000003': $RoomOriStringNew .= "南"; break;
                case '100500000005': $RoomOriStringNew .= "西"; break;
                case '100500000007': $RoomOriStringNew .= "北"; break;
                default :$RoomOriStringNew .= "未知"; break;
            }
        }
        //多处使用变量初始化结束

        $url = $doc->createElement( "url" );
        //loc和data都是url的下一级 11
        //loc大标签，类型为URL地址，最大长度256个字符 必须符合正则表达式(http://)(.+)
        $loc = $doc->createElement( "loc" );


        $loc->appendChild(
            $doc->createTextNode("http://".$zhujianCityString.".lianjia.com/zufang/".
                $zhujianString.".shtml")
        );
        $url->appendChild( $loc );   //url中加入loc
        

        //data  11
        $data = $doc->createElement( "data" );

        //data-RentalInfo  11
        $RentalInfo =  $doc->createElement( "RentalInfo" );
        

        //data-RentalInfo-name   暂缺，需要复杂的扩展
        //可以填标题 11  定慧西里次顶层南北两居总价最便宜的两居随时看房
        $name=$doc->createElement("name");
        $nameString = trim( $rowEraHouseComment['comment_title'] );
        if( empty($nameString) ) {
                $nameString = $RealestateNameString."小区".$houseStruString.$roomNum."室".$hallNum."厅";
        }
        $name->appendChild(
            $doc->createTextNode( $nameString )
        );
        $RentalInfo->appendChild( $name );


        //data-RentalInfo-domain指定值
        $domain=$doc->createElement("domain");
        $domain->appendChild(
            $doc->createTextNode("房产")
        );
        $RentalInfo->appendChild( $domain ); 

        //data-RentalInfo-type指定值
        $type=$doc->createElement("type");
        $type->appendChild(
            $doc->createTextNode("HouseProperty")
        );
        $RentalInfo->appendChild( $type ); 




        $lastmod=$doc->createElement("lastmod");
        if( empty($row['LAST_MODIFY_TIME']) ){
            $lastModTimeStringPre = "2014-07-11";
            $lastModTimeStringPost = "00:00:00";
        }else{
            $lastModTimeString = (string)$row['LAST_MODIFY_TIME'];
            $lastModTimeStringPre = substr($lastModTimeString,0,10);
            $lastModTimeStringPost= substr($lastModTimeString,11,8);
            //TO_DATE('字符串'，'YYYY-MM-DD HH24：MI：SS')
        }
        $lastmod->appendChild(
            $doc->createTextNode($lastModTimeStringPre."T".$lastModTimeStringPost)
        );
        $RentalInfo->appendChild( $lastmod );




        //data-RentalInfo-publishTime  表中有这一列，可以直接调用  11
        $publishTime=$doc->createElement("publishTime");


        if(empty($row['SE_CREATE_TIME'])){
            $publishTimeStringPre = "2014-07-11";
            $publishTimeStringPost = "00:00:00";
        }else{
            $publishTimeString = (string)$row['SE_CREATE_TIME'];
            $publishTimeStringPre = substr($publishTimeString,0,10);
            $publishTimeStringPost= substr($publishTimeString,11,8);
        }
        $publishTime->appendChild(
            $doc->createTextNode($publishTimeStringPre."T".$publishTimeStringPost)
        );
        $RentalInfo->appendChild( $publishTime );


        //data-RentalInfo-priority   01   这个如何判断？
        //指定此链接相对于其他链接的优先权比值，此值定于0.0-1.0之间
        $priority=$doc->createElement("priority");
        $priorityFloat=1.0;
        $priorityFloatFormat = number_format($priorityFloat, 1, '.', '');  
        $priority->appendChild(
            $doc->createTextNode( $priorityFloatFormat )
        );
        $RentalInfo->appendChild( $priority ); 


        //data-RentalInfo-source  11
        $source=$doc->createElement("source");
        $source->appendChild(
            $doc->createTextNode("链家网")
        );
        $RentalInfo->appendChild( $source );


        //data-RentalInfo-status  11  有效值为：ADD、MOD
        //表示该条数据的状态：ADD表示这是增加的数据；MOD表示修改的数据
        $status=$doc->createElement("status");
        $status->appendChild(
            $doc->createTextNode("ADD")
        );
        $RentalInfo->appendChild( $status );   


        //data-RentalInfo-RentalType   11
        //租赁类型，有效值为：整租、合租、床位、短租、其它、未知
        $RentalType=$doc->createElement("RentalType");
        $RentalTypeStringWhole = (string)$row['RENT_TYPE'];
        switch($RentalTypeStringWhole) {
            case ("112000000001") :$RentalTypeStringShort = "整租";break;
            case("112000000002") :$RentalTypeStringShort = "合租";break;
            default :$RentalTypeStringShort = "未知";break;
        }
        $RentalType->appendChild(
            $doc->createTextNode($RentalTypeStringShort)
        );
        $RentalInfo->appendChild( $RentalType ); 


        //data-RentalInfo-RentalPrice   11
        //单位：元/月，最少出现1次 最多出现1次，类型为整数
        $RentalPrice=$doc->createElement("RentalPrice");
        $RentalPriceInt = (int)$row['RENT_PRICE'];
        $RentalPrice->appendChild(
            $doc->createTextNode($RentalPriceInt)
        );
        $RentalInfo->appendChild( $RentalPrice ); 


        //data-RentalInfo-RentalPayment  11
        //租赁付款方式:押2付3等,字符串, 这数据库注释也太差了
        $RentalPayment=$doc->createElement("RentalPayment");
        if(isset($RentalPaymentString)){
            unset($RentalPaymentString);
        }
        $RentalPaymentString = trim((string)$row['RENT_HR_PID']);

        switch($RentalPaymentString) {
            case "112300000001" :$RentalPaymentStringShort = "月付";break;
            case "112300000002" :$RentalPaymentStringShort = "双月付";break;
            case "112300000003" :$RentalPaymentStringShort = "季付";break;
            case "112300000004" :$RentalPaymentStringShort = "半年付";break;
            case "112300000005" :$RentalPaymentStringShort = "年付";break;
            case "112500000003" :$RentalPaymentStringShort = "季付";break;
            default : $RentalPaymentStringShort = "未知";
        }
        $RentalPayment->appendChild(
            $doc->createTextNode( $RentalPaymentStringShort )
        );
        $RentalInfo->appendChild( $RentalPayment ); 


        //data-RentalInfo-Contact  需要扩展  11
        $Contact=$doc->createElement("Contact");


        //data-RentalInfo-Contact-Type   11
        //字符串，有效值为：中介、个人、房东、其它、未知
        $Type=$doc->createElement("Type");

        $Type->appendChild(
            $doc->createTextNode("中介")
        );
        $Contact->appendChild( $Type );


        //data-RentalInfo-Contact-name   11
        //有所属人 BELONG_AGENT_ID   缺姓名
        $name=$doc->createElement("name");
        $name->appendChild(
            $doc->createTextNode($brokerName)
        );
        $Contact->appendChild( $name );


        //data-RentalInfo-Contact-telephone  11
        //字符串  手机号   有所属人 BELONG_AGENT_ID  
        $telephone=$doc->createElement("telephone");
        $telephone->appendChild(
            $doc->createTextNode($brokerTelShort)
        );
        $Contact->appendChild( $telephone );


        //data-RentalInfo-Contact-worksFor   11
        //如果是个人房源，请填写"个人"，最少出现1次 最多出现1次，类型为字符串
        $worksFor=$doc->createElement("worksFor");
        $worksFor->appendChild(
            $doc->createTextNode("链家网")
        );
        $Contact->appendChild( $worksFor );


        //data-RentalInfo-Contact-Profile   01
        //中介/个人/房东头像，最少出现0次 最多出现1次，类型为URL地址
        $Profile=$doc->createElement("Profile");
        //$profileUrlPre= "http://image.homelink.com.cn";
        if(isset($rowEraAgent['small_photo_path'])){
            $profileUrl=$picUrlPre.trim($rowEraAgent['small_photo_path']);
        }elseif(isset($rowEraAgent['code_pic_path'])){
            $profileUrl=$picUrlPre.trim($rowEraAgent['code_pic_path']);           
        }else{
            $profileUrl = "http://static-xeq.lianjia.com/static/asset/img/new-version/default_block.png";
        }
        $Profile->appendChild(
            $doc->createTextNode( $profileUrl )
        );
        $Contact->appendChild( $Profile );

        /*
        //data-RentalInfo-Contact-Gender   01
        //字符串，有效值为：男、女  有所属人 BELONG_AGENT_ID   缺性别
        $Gender=$doc->createElement("Gender");
        $Gender->appendChild(
            $doc->createTextNode("")
        );
        $Contact->appendChild( $Gender );
        */

        $RentalInfo->appendChild( $Contact );  //Contact加入


        //data-RentalInfo-Installations  1-14
        //屋内设施，每次包含一种设施；有n种设施就出现n次（不得重复）最少出现1次最多出现14次，字符串
        //有效值为：独立阳台、卫生间、宽带、电视、暖气、空调、热水器、洗衣机、冰箱、床、衣柜、沙发、煤气、未知
        $Installations=$doc->createElement("Installations");

        $Installations->appendChild(
            $doc->createTextNode("未知")
        );
        $RentalInfo->appendChild( $Installations ); 


        if($HouseStatusString=="闲置"){
            //data-RentalInfo-CheckinTime   01
            //随时 or 9月底  字符串
            $CheckinTime=$doc->createElement("CheckinTime");
            $CheckinTime->appendChild(
                $doc->createTextNode("随时入住")
            );
            $RentalInfo->appendChild( $CheckinTime ); 
        }


        if($RentalTypeStringShort == "合租"){

            //！！！如果整租，忽略此项！！！           
            //data-RentalInfo-RentRoom  需要扩展  01
            $RentRoom=$doc->createElement("RentRoom");


            //data-RentalInfo-RentRoom-RoomArea   11   只有建筑面积，估计不对
            //字符串，单位为m2 , 是指出租房屋的面积，非整个房子面积  \d+m2
            $RoomArea=$doc->createElement("RoomArea");
            $RoomArea->appendChild(
                $doc->createTextNode((string)(int)$row['BUILDING_AREA']."m2")
            );
            $RentRoom->appendChild( $RoomArea );


            //data-RentalInfo-RentRoom-RoomOrientation   11  只能选1个
            //字符串，有效值为：东、西、南、北、东北、东南、西北、西南、其它、未知
            $RoomOrientation=$doc->createElement("RoomOrientation");
            $RoomOrientation->appendChild(
                $doc->createTextNode( $RoomOriStringNew )
            );
            $RentRoom->appendChild( $RoomOrientation );


            $RentalInfo->appendChild( $RentRoom );  //RentRoom加入RentalInfo

            //data-RentalInfo-RentRoom-AdditionalInformation   01
            //附加信息 字符串：独立卫生间，有空调...等等   暂时没有
            /*
            $AdditionalInformation=$doc->createElement("AdditionalInformation");
            $AdditionalInformation->appendChild(
                $doc->createTextNode("")
            );
            $RentRoom->appendChild( $AdditionalInformation );

            */
        //合租时生效的括号，整租不进入此段
        } 
        


        //data-RentalInfo-TimeforView    01
        //看房时间,字符串：随时。。    和钥匙信息绑定
        $TimeforView=$doc->createElement("TimeforView");
        if(isset($row['KEY_AGENT_ID'])){
            $timeForViewString = "随时看房";
        }else{
            $timeForViewString = "未知";
        }
        $TimeforView->appendChild(
            $doc->createTextNode($timeForViewString)
        );
        $RentalInfo->appendChild( $TimeforView ); 


        //data-RentalInfo-RentalIntroduce   11
        //租房简介,可以很多字符
        $RentalIntroduce=$doc->createElement("RentalIntroduce");
        if(!empty($rowEraHouseComment['content'])){
            $rentalIntroduceString = trim(strip_tags($rowEraHouseComment['content']));
        }else{
            $rentalIntroduceString = $nameString;
        } 

        $RentalIntroduce->appendChild(
            $doc->createTextNode( $rentalIntroduceString )
        );
        $RentalInfo->appendChild( $RentalIntroduce ); 


        //data-RentalInfo-House  需要扩展  11
        $House = $doc->createElement("House");


        //data-RentalInfo-House-PropertyVerified   11
        //房源是否认证，0/1，类型为字符串，有效值为：1、0
        $PropertyVerified = $doc->createElement("PropertyVerified");
        $ifVerified = (int)$row['IF_OWNER_CERTIFICATION'];
        if( $ifVerified != 1 ){
            $ifVerified = 0;
        }
        $PropertyVerified->appendChild(
            $doc->createTextNode( (string)$ifVerified )
        );
        $House->appendChild( $PropertyVerified );


        //data-RentalInfo-House-Url    11
        //小区url,类型为URL地址，自己生成
        $Url=$doc->createElement("Url");

        $Url->appendChild(
            $doc->createTextNode("http://".$zhujianCityString.".lianjia.com/zufang/".
                $zhujianString.".shtml")
        );
        $House->appendChild( $Url );


        //data-RentalInfo-House-HouseArea这个应该是对的，上面一个房屋面积可能有问题
        //房屋面积,是指整个房子的面积，非出租房屋面积    11
        $HouseArea = $doc->createElement("HouseArea");

        $HouseArea->appendChild(
            $doc->createTextNode((string)(int)$row['BUILDING_AREA']."m2")
        );
        $House->appendChild( $HouseArea );


        //data-RentalInfo-House-HouseType   11
        //符串,有效值为：普通住宅、公寓、别墅、平房、经济适用房、商住两用、其它、未知
        $HouseType = $doc->createElement("HouseType");
        switch(trim($row['HOUSE_TYPE'])) {
            case "107500000002" :$houseTypeString="公寓";    break;
            case "107500000003" :$houseTypeString="普通住宅";break;
            case "107500000004" :$houseTypeString="别墅";    break;
            case "107500000005" :$houseTypeString="平房";    break;
            case '107500000001' :$houseTypeString="平房";    break;
            case "107500000006" :$houseTypeString="商住两用";break;
            case "107500000013" :$houseTypeString="商住两用";break;   
            case "107500000007" :$houseTypeString="商住两用";break;
            case "107500000010" :$houseTypeString="商住两用";break;
            case "107500000009" :$houseTypeString="经济适用房";break;
            default :            $houseTypeString="其它";    break;
        }
        $HouseType->appendChild(
            $doc->createTextNode($houseTypeString)
        );
        $House->appendChild( $HouseType );


        //data-RentalInfo-House-HouseOrientation   11
        //字符串，有效值为：朝东、朝南、朝西、朝北、南北通透、东西朝向、东南朝向、东北朝向、西北朝向、西南朝向、其它、未知    说法和“房间朝向”不同
        //以room的方向作为house的方向
        $HouseOrientation = $doc->createElement("HouseOrientation");
        $HouseOrientation->appendChild(
            $doc->createTextNode( "未知" )   //用的就是房子的方向
        );
        $House->appendChild( $HouseOrientation );


        //data-RentalInfo-House-HouseAllFloor   11
        //总楼层，整数 +层
        $HouseAllFloor = $doc->createElement("HouseAllFloor");
        $HouseFloorNum = (int)$row['TOTAL_FLOOR'];
        $HouseAllFloor->appendChild(
            $doc->createTextNode($HouseFloorNum."层" )
        );
        $House->appendChild( $HouseAllFloor );


        //data-RentalInfo-House-HouseSituation   01
        //房屋现状,有效值为：自住、出租、闲置、其它、未知
        $HouseSituation = $doc->createElement("HouseSituation");
        //$HouseStatus = (string)$row['HOUSE_STATUS'];
        $HouseSituation->appendChild(
            $doc->createTextNode( $HouseStatusString )
        );
        $House->appendChild( $HouseSituation );


        //data-RentalInfo-House-Decoration   11
        //字符串，有效值为：豪华装修、精装修、中等装修、简单装修、毛坯、其它、未知
        $Decoration = $doc->createElement("Decoration");
        switch(trim((string)$row['HD_ID'])) {
            case '112100000001' : $DecorationStr = "毛坯";     break;
            case '112100000002' : $DecorationStr = "简单装修"; break;
            case '112100000004' : $DecorationStr = "精装修";   break;
            default : $DecorationStr = "其它";                 break;                
        }
        $Decoration->appendChild(
            $doc->createTextNode($DecorationStr)
        );
        $House->appendChild( $Decoration );


        //data-RentalInfo-House-CovorImage   11
        //封面图片,url地址
        $CovorImage = $doc->createElement("CovorImage");
        $CovorImage->appendChild(
            $doc->createTextNode($covorImageString)
        );
        $House->appendChild( $CovorImage );

        $HouseImage = $doc->createElement("HouseImage");//era_house_pic这个表里有图片
        //$houseString = $picUrlPre.trim($rowEraHousePic['big_pic_path']);
        $HouseImage->appendChild(
            $doc->createTextNode($covorImageString)
        );
        $House->appendChild( $HouseImage );


        while($resultEraHousePic->fetch_assoc()){
            //data-RentalInfo-House-HouseImage   1+
            //房屋图片，如果有多个，可以多次出现此标签，一个标签表示一个图片，不要与CovorImage重复,URL 
            $HouseImage = $doc->createElement("HouseImage");//era_house_pic这个表里有图片
            $houseString = $picUrlPre.trim($rowEraHousePic['big_pic_path']);
            $HouseImage->appendChild(
                $doc->createTextNode($houseString)
            );
            $House->appendChild( $HouseImage );
        }


        //data-RentalInfo-House-HouseImageNum   11
        //房屋图片数，符合正则表达式\d+张   字符串
        $HouseImageNum = $doc->createElement("HouseImageNum");
        $HouseImageNum->appendChild(
            $doc->createTextNode( (string)$row['PIC_COUNT']."张" )
        );
        $House->appendChild( $HouseImageNum );

        /*
        //data-RentalInfo-House-Tag    0+次
        //标签示例：精装修 学区房 紧邻地铁 免中介费 独门独卫 南北通透……字符串
        $Tag = $doc->createElement("Tag");
        $Tag->appendChild(
            $doc->createTextNode("")
        );
        $House->appendChild( $Tag );
        */

        //data-RentalInfo-House-HouseFloor   11  避免敏感数据，全部写0！！！
        //总楼层，格整数 +层/地下，类型为字符串
        $HouseFloor = $doc->createElement("HouseFloor");
        $HouseFloor->appendChild(
            $doc->createTextNode("0层")  //这个应该不对，变量时总楼层数
        );
        $House->appendChild( $HouseFloor );


        //data-RentalInfo-House-BuildNum   11
        //楼号,整数   只有楼栋编号BUILDING_CODE，没有小区的楼的编号
        $BuildNum = $doc->createElement("BuildNum");
        $BuildNum->appendChild(
            $doc->createTextNode((int)"0")
        );
        $House->appendChild( $BuildNum );


        //data-RentalInfo-House-BuildType   11
        //建筑类型字符串，有效值为：塔楼、板楼、平房、其它、未知
        $BuildType = $doc->createElement("BuildType");
        switch(trim((string)$row['HBT_ID']))  {
            case '102200000001': $buildTypeString = "板楼"; break;
            case '102200000002': $buildTypeString = "塔楼"; break;
            case '102200000002': $buildTypeString = "平房"; break;
            default :            $buildTypeString = "其它"; break;
        }  
        $BuildType->appendChild(
            $doc->createTextNode($buildTypeString)
        );
        $House->appendChild( $BuildType );


        //data-RentalInfo-House-HouseStructurename   11  户型名，字符串，
        //有效值为：一居、两居、三居、四居、五居、六居、七居、八居、九居、十居、其它、未知
        $HouseStructurename = $doc->createElement("HouseStructurename");
        $HouseStructurename->appendChild(
            $doc->createTextNode($houseStruString)
        );
        $House->appendChild( $HouseStructurename );


        //data-RentalInfo-House-HouseStructureshi   11
        //字符串，必须符合正则表达式\d+室,没有，请写0室
        $HouseStructureshi = $doc->createElement("HouseStructureshi");
        $HouseStructureshi->appendChild(
            $doc->createTextNode($roomNum."室")
        );
        $House->appendChild( $HouseStructureshi );




        //data-RentalInfo-House-HouseStructureting   11
        //字符串，必须符合正则表达式\d+厅,没有，请写0厅
        $HouseStructureting = $doc->createElement("HouseStructureting");
        $HouseStructureting->appendChild(
            $doc->createTextNode($hallNum."厅")
        );
        $House->appendChild( $HouseStructureting );


        //data-RentalInfo-House-HouseStructurewei   11
        //字符串，必须符合正则表达式\d+卫,没有，请写0卫
        $HouseStructurewei = $doc->createElement("HouseStructurewei");
        $HouseStructurewei->appendChild(
            $doc->createTextNode((int)$row['BATHROOM_NUM']."卫")
        );
        $House->appendChild( $HouseStructurewei );

        $RentalInfo->appendChild( $House );   //House加入RentalInfo


        //data-RentInfo-RealEstate 需要扩展   11
        $RealEstate = $doc->createElement("RealEstate");


        //data-RentInfo-RealEstate-RealestateID   11
        //对应小区的id要与提交的小区的xml的loc字段一致且需要使用url来唯一标识
        //如果没有对应资料，填写“0”，
        $RealestateID = $doc->createElement("RealestateID");


        $RealestateID->appendChild(
            $doc->createTextNode("http://".$zhujianCityString.".lianjia.com/xiaoqu/".
                $row['COMMUNITY_CODE']."/")
        );
        $RealEstate->appendChild( $RealestateID );


        //data-RentInfo-RealEstate-RealestateName  11
        //对应小区的名字；如果资料，填写“未知”，字符串   小区名，community表中
        $RealestateName = $doc->createElement("RealestateName");
        $RealestateName->appendChild(
            $doc->createTextNode(trim($RealestateNameString))
        );
        $RealEstate->appendChild( $RealestateName );


        //RealEstate加入RentalInfo
        $RentalInfo->appendChild($RealEstate);


        //data-RentalInfo-Broker    11   只有BELONG_AGENT_ID
        //经纪人的姓名；如果为个人房源，写：个人房源
        $Broker=$doc->createElement("Broker");
        $Broker->appendChild(
            $doc->createTextNode($brokerName)
        );
        $RentalInfo->appendChild( $Broker ); 


        //data-RentalInfo-BrokerTel    01
        //经纪人的电话（手机或者座机，必须是纯数字或者是区号-电话号码的格式）
        $BrokerTel=$doc->createElement("BrokerTel");
        $BrokerTel->appendChild(
            $doc->createTextNode($brokerTelShort)
        );
        $RentalInfo->appendChild( $BrokerTel ); 


        $data->appendChild( $RentalInfo );  //RentalInfo加入data

        $url->appendChild($data);   //data加入url

        $urlset->appendChild( $url );   //终于理解了，是在最后插入整个url模块
    }


    echo $doc->save($xmlFileZufang)." Bytes"."<br />";
}

    
require 'footer.php';

?>
</body>
</html>