# iot-Server-none-user

无需注册账户，直接新建设备，接入指令状态云。

建立设备后，会返回一个随机设备密码。该密码用来重置设备（清空全部状态与指令）或删除设备及分享设备。


install php / mysql
```
sudo apt-get install php mysql-server php-mysql
```

clone project
```
git clone https://github.com/iot-server/iot-server-none-user.git
```

install

```
cd iot-server
composer install
```
start
```
cd public
php -S localhost:3000
```

api
```
http://localhost:3000/api
```

