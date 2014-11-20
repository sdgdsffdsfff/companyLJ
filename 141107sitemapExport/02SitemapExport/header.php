<?php
    header("Content-Type: text/html;encoding='UTF-8'");

    class RunTime//页面执行时间类
    { 
        private $starttime;//页面开始执行时间 
        private $stoptime;//页面结束执行时间 
        private $spendtime;//页面执行花费时间 
        function getmicrotime()//获取返回当前微秒数的浮点数 
        { 
            list($usec,$sec)=explode(" ",microtime()); 
            return ((float)$usec + (float)$sec); 
        } 
        function start()//页面开始执行函数，返回开始页面执行的时间 
        { 
            $this->starttime=$this->getmicrotime(); 
        } 
         function end()//显示页面执行的时间 
        { 
            $this->stoptime=$this->getmicrotime(); 
            $this->spendtime=$this->stoptime-$this->starttime; 
            //return round($this->spendtime,10); 
        } 
        function display()
        {
            //$this->end();
            echo "<p>Runtime: ".round($this->spendtime,10)." s</p>";
        }
    }
    
    $timer=new Runtime(); 
    $timer->start(); 


    $DOCUMENT_ROOT = "./";
    echo $DOCUMENT_ROOT."<br />";
    
    //结构化数据导出，按需要修改
    $xmlRootXiangQing = $DOCUMENT_ROOT."results/";       //详情生成地址
    $xmlRootChannel = $DOCUMENT_ROOT."results/channel/"; //频道生成地址
    $xmlRootQuXian = $DOCUMENT_ROOT."results/quxian/";   //区县生成地址
    $xmlRootXiaoQu = $DOCUMENT_ROOT."results/xiaoqu/";   //小区省城地址
    $xmlRootShangQuan = $DOCUMENT_ROOT."results/shangquan/"; //商圈生成地址
    $xmlRootPartChannelAndQuXian = $DOCUMENT_ROOT."results/";  //部分城市区县地址
    $xmlRootPartShangQuan = $DOCUMENT_ROOT."results/";  //部分城市商圈地址
    $xmlRootPartChengJiaoXiangQing = $DOCUMENT_ROOT."results/partChengJiaoXiangQing/";  //部分城市商圈地址
    $xmlRootWeiTuoAndXinFangList = $DOCUMENT_ROOT."results/";   //业主委托页面列表地址



    //sitemap
    $countStepZuFangXiangQiang = 55000;//单个文件中租房数量   55000个一般不到10M
    $countStepErShouFangXiangQing = 55000;


    $countStepPartChengJiaoXiangQing = 50000; //部分城市的房源成交详情步长设置
    $curLine = 0;
    $xmlFileCount = 1;
    //$xmlFileZufangDelete = $DOCUMENT_ROOT."XMLexport/zufangdelete.xml";
    
    $city = array( 
        array('0'=>'310000','1'=>'sh','2'=>'ershoufang'),
        array('0'=>'510100','1'=>'cd','2'=>'xiaoqu'),
        array('0'=>'320100','1'=>'nj','2'=>'school'),
        array('0'=>'330100','1'=>'hz','2'=>'school/list'),
        array('0'=>'370200','1'=>'qd','2'=>'zufang'),
        array('0'=>'210200','1'=>'dl','2'=>'jingjiren'),
        array('0'=>'120000','1'=>'tj','2'=>'ask'),
        array('0'=>'110000','1'=>'bj','2'=>'news'),
    );

    $cityPart = array( 
        array('0'=>'320100','1'=>'nj','2'=>'school'),
        array('0'=>'330100','1'=>'hz','2'=>'school/list'),
        array('0'=>'120000','1'=>'tj','2'=>'ask'),
        array('0'=>'110000','1'=>'bj','2'=>'news'),
    );


    function createThreeAutoTag($doc,$url){
            //data-RentalInfo-lastmod 表中有这一列，可以直接调用  11
        $lastmod=$doc->createElement("lastmod");
        $lastmod->appendChild(
            $doc->createTextNode("2014-07-11")
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

    }
    
    //连接数据库
    
    //$db = new mysqli('172.27.6.17', 'xierqi', 'H9489B7388DDD25L', 'homelink');  //准生产数据库
    $db = new mysqli('172.27.2.123', 'xierqi', '2F3C12E6FC720E7E', 'homelink');    //线上数据库
    //$db = new mysqli('10.210.84.75', 'admin', '123456', 'homelink');  //本地
    if (mysqli_connect_errno()) {
        echo "Error: Could not connect to database.  Please try again later.";
        exit;
    }
