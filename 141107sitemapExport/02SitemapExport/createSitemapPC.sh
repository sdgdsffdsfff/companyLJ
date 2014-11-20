#!/bin/bash
(php ./createErShouFangXiangQing.php > log/erShouFangXiangQiang.log 2>&1 &);

(php ./createZuFangXiangQing.php > log/zuFangXiangQing.log 2>&1 &);

(php ./createChannelForEightKinds.php > log/channelForEightKinds.log 2>&1 &);

(php ./createQuXianList.php > log/quXianList.log 2>&1 &);

(php ./createXiaoQuList.php > log/xiaoQuList.log 2>&1 &);

(php ./createShangQuanList.php > log/ShangQuanList.log 2>&1 &);

(php ./createPartChannelAndQuXian.php > log/partChannelAndQuXian.log 2>&1 &);

(php ./createPartZiRuList.php > log/partZiRuList.log 2>&1 &);

(php ./createPartShangQuan.php > log/partShangQuan.log 2>&1 &);

(php ./createPartChengJiaoXiangQing.php > log/partChengJiaoXiangQing.log 2>&1 &);

(php ./createPartWeiTuoAndXinFangList.php > log/partWeiTuoAndXinFangList.log 2>&1 &);

