<?php $this->layout('head', ['title' => 'api']) ?>
<?php
$host="localhost:3000/api";
$Parsedown = new Parsedown();
echo $Parsedown->text('### API');
echo $Parsedown->text('1、统计信息');
echo $Parsedown->text('```curl '.$host.'/info -X GET```');

echo $Parsedown->text('2、添加设备');
echo $Parsedown->text('```curl '.$host.'/device/new -X POST```');
echo $Parsedown->text('如果成功，返回设备的 device_key（16位）和 device_pwd（随机密码）');


echo $Parsedown->text('3、删除设备');
echo $Parsedown->text('```curl '.$host.'/device/remove -X POST -d "device_key=xxx&device_pwd=xxx"```');

echo $Parsedown->text('4、向设备发送指令');
echo $Parsedown->text('指令并未设置有效期，未来会设置有效期');
echo $Parsedown->text('```curl '.$host.'/cmd/new -X POST -d "device_key=xxx&device_pwd=xxx&cmd=xxx"```');

echo $Parsedown->text('5、读取设备的最后指令');
echo $Parsedown->text('```curl '.$host.'/cmd/last -X POST -d "device_key=xxx"```');


echo $Parsedown->text('6、 设置指令为已执行');
echo $Parsedown->text('```curl '.$host.'/cmd/exec -X POST -d "device_key=xxx&device_pwd=xxx&cmd_id=xxx"```');

echo $Parsedown->text('7、保存传感器数据');
echo $Parsedown->text('```curl '.$host.'/status/new -X POST -d "device_key=xxx&device_pwd=xxx&name=xxx&value=xxx&value_type=xxx"```');


echo $Parsedown->text('8、读取传感器最后的数据');
echo $Parsedown->text('```curl '.$host.'/status/last -X POST -d "device_key=xxx&name=xxx"```');


echo $Parsedown->text('9、 设备重新初始化（删除全部状态及指令）');
echo $Parsedown->text('```curl '.$host.'/device/reset -X POST -d "device_key=xxx&device_pwd=xxx"```');

echo $Parsedown->text('10、 设备上线');
echo $Parsedown->text('```curl '.$host.'/device/online -X POST -d "device_key=xxx&device_pwd=xxx"```');
?>
