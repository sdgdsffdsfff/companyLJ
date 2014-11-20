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

    //此行需要修改
    $XML_ROOT = $DOCUMENT_ROOT."results/";
    echo $DOCUMENT_ROOT."<br />";

    $countStepXiaoQu = 5000; //小区单个文件中的数量
    $countStepZuFang = 2000; //租房单个文件中租房数量   2500个一般不到10M
    $curLine = 0;
    $xmlFileCount = 1;
    //$xmlFileZufangDelete = $DOCUMENT_ROOT."XMLexport/zufangdelete.xml";
    
    
    //连接数据库
    
    $db = new mysqli('172.27.6.17', 'xierqi', 'H9489B7388DDD25L', 'homelink');  //准生产数据库
    //$db = new mysqli('172.27.2.123', 'xierqi', '2F3C12E6FC720E7E', 'homelink');    //线上数据库
    //$db = new mysqli('10.210.84.75', 'admin', '123456', 'homelink');
    if (mysqli_connect_errno()) {
        echo "Error: Could not connect to database.  Please try again later.";
        exit;
    }