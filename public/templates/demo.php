<?php $this->layout('head', ['title' => 'demo']) ?>
<?php
$host="localhost:3000/api";
$Parsedown = new Parsedown();
echo $Parsedown->text('# demo');

echo $Parsedown->text('### 一、远程遥控灯');
echo $Parsedown->text('1、添加设备（电灯）');
echo $Parsedown->text('```curl '.$host.'/device/new -X POST```');
echo $Parsedown->text('返回');
echo $Parsedown->text('```{"device_key":"6H3solfZgvOMj1B7","device_pwd":"TcP-8jfp"}```');


echo $Parsedown->text('2、向设备发送指令');
echo $Parsedown->text('```curl '.$host.'/cmd/new -X POST -d "device_key=6H3solfZgvOMj1B7&device_pwd=TcP-8jfp&cmd=on"```');

echo $Parsedown->text('3、设备读取最后一个指令');
echo $Parsedown->text('```curl '.$host.'/cmd/last -X POST -d "device_key=6H3solfZgvOMj1B7"```');
echo $Parsedown->text('返回：');
echo $Parsedown->text('```{"id":1,"cmd":"on"}```');



echo $Parsedown->text('4、设备告知指令已执行');
echo $Parsedown->text('```curl '.$host.'/cmd/exec -X POST -d "device_key=6H3solfZgvOMj1B7&device_pwd=TcP-8jfp&cmd_id=1"```');

echo $Parsedown->text('### 二、dht迷你气象站');
echo $Parsedown->text('1、添加设备');
echo $Parsedown->text('```curl '.$host.'/device/new -X POST```');
echo $Parsedown->text('返回：');
echo $Parsedown->text('```{"device_key":"e8HcaZ4gx_$MK9#U","device_pwd":"zY@%3pn_"}```');


echo $Parsedown->text('8、读取传感器最后的数据');
echo $Parsedown->text('```curl '.$host.'/status/last -X POST -d "device_key=xxx&name=xxx"```');


echo $Parsedown->text('9、 设备重新初始化（删除全部状态及指令）');
echo $Parsedown->text('```curl '.$host.'/device/reset -X POST -d "device_key=xxx&device_pwd=xxx"```');

echo $Parsedown->text('10、 设备上线');
echo $Parsedown->text('```curl '.$host.'/device/online -X POST -d "device_key=xxx"```');
?>
