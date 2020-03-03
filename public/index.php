<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/lib/random_code.php';
require 'lib/Db.class.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hello world");
    return $response;
});

$app->get('/api', function (Request $request, Response $response, $args) {
    $templates = new League\Plates\Engine(__DIR__ . '/templates');
    echo $templates->render('api', ['name' => 'Jonathan']);
    return $response;
});

$app->get('/demo', function (Request $request, Response $response, $args) {
    $templates = new League\Plates\Engine(__DIR__ . '/templates');
    echo $templates->render('demo', ['name' => 'Jonathan']);
    return $response;
});

$app->get('/api/info', function (Request $request, Response $response, $args) {
    $db = new Db();

    $device_count = $db->query("Select count(*) as count from devices")[0]['count'];
    $status_count = $db->query("Select count(*) as count from status")[0]['count'];
    $cmd_count = $db->query("Select count(*) as count from cmds")[0]['count'];

    $info=[];

    $info['devices']=$device_count;
    $info['cmds']=$cmd_count;
    $info['status']=$status_count;

    $payload = json_encode($info);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/device/new', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key=random_code(16,"t,n,s");
    $device_pwd=random_code(8,"t,n,s");
    $db = new Db();
    $insert = $db->query("INSERT INTO devices(device_key,device_pwd) VALUES(:k,:p)", array("k"=>$device_key,"p"=>$device_pwd));
    if($insert>0){
        $data = array('device_key' => $device_key,'device_pwd' => $device_pwd); 
    }else{
        $data = false;
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/device/remove', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $device_pwd = $allPostPutVars['device_pwd'];
    $db = new Db();
    $delete = $db->query("DELETE FROM devices WHERE device_key = :k and device_pwd=:p", array("k"=>$device_key,"p"=>$device_pwd));
    if($delete>0){
        $data = true; 
    }else{
        $data = false; 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/device/reset', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $device_pwd = $allPostPutVars['device_pwd'];
    
    $db = new Db();
    $device_count = $db->query("Select count(*) as count from devices WHERE device_key = :k and device_pwd=:p", array("k"=>$device_key,"p"=>$device_pwd))[0]['count'];
    if($device_count>0){
        $delete1 = $db->query("DELETE FROM cmds WHERE device_key = :k", array("k"=>$device_key));
        $delete2 = $db->query("DELETE FROM status WHERE device_key = :k", array("k"=>$device_key));
        if($delete1>0 and $delete2>0){
            $data = true; 
        }else{
            $data = false; 
        }
    }else{
        $data = false;
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/device/online', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $device_pwd = $allPostPutVars['device_pwd'];
    $db = new Db();
    $device_count = $db->query("Select count(*) as count from devices WHERE device_key = :k and device_pwd=:p", array("k"=>$device_key,"p"=>$device_pwd))[0]['count'];
    if($device_count>0){
        $update=$db->query("UPDATE devices SET last_online = now() WHERE device_key = :k", array("k"=>$device_key));
        if($update>0){
            $data = true; 
        }else{
            $data = false; 
        }
    }else{
        $data = false; 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/cmd/new', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $device_pwd = $allPostPutVars['device_pwd'];
    $cmd = $allPostPutVars['cmd'];
    $db = new Db();
    $device_count = $db->query("Select count(*) as count from devices WHERE device_key = :k and device_pwd=:p", array("k"=>$device_key,"p"=>$device_pwd))[0]['count'];
    if($device_count>0){
        $insert = $db->query("INSERT INTO cmds(cmd,is_execute,device_key) VALUES(:cmd,0,:k)", array("cmd"=>$cmd,"k"=>$device_key));
        if($insert>0){
            $data = true; 
        }else{
            $data = false; 
        }
    }else{
        $data = false; 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/cmd/last', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $db = new Db();
    $cmd = $db->query("Select * from cmds where device_key=:k order by id desc limit 0,1", array("k"=>$device_key));
    if(count($cmd)>0){
        $id = $cmd[0]['id'];
        $cmd = $cmd[0]['cmd'];
        $data = array('id' => $id,'cmd' => $cmd); 
    }else{
        $data = false; 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/cmd/exec', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $device_pwd = $allPostPutVars['device_pwd'];
    $cmd_id = $allPostPutVars['cmd_id'];
    $db = new Db();
    $device_count = $db->query("Select count(*) as count from devices WHERE device_key = :k and device_pwd=:p", array("k"=>$device_key,"p"=>$device_pwd))[0]['count'];
    if($device_count>0){
        $update=$db->query("UPDATE cmds SET is_execute = 1,exec_time = now() WHERE id = :i and device_key = :k", array("i"=>$cmd_id,"k"=>$device_key));
        if($update>0){
            $data = true; 
        }else{
            $data = false; 
        }
    }else{
        $data = false; 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/status/new', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $device_pwd = $allPostPutVars['device_pwd'];
    $name = $allPostPutVars['name'];
    $value = $allPostPutVars['value'];
    $value_type = $allPostPutVars['value_type'];

    $db = new Db();

    $device_count = $db->query("Select count(*) as count from devices WHERE device_key = :k and device_pwd=:p", array("k"=>$device_key,"p"=>$device_pwd))[0]['count'];
    if($device_count>0){
        $insert = $db->query("INSERT INTO status(name,value,value_type,device_key)VALUES(:n,:v,:vt,:k)",array("n"=>$name,"v"=>$value,"vt"=>$value_type,"k"=>$device_key));
        if($insert>0){
            $data = true; 
        }else{
            $data = false; 
        }  
    }else{
        $data = false; 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/value/last', function (Request $request, Response $response, $args) {
    $allPostPutVars = $request->getParsedBody();
    $device_key = $allPostPutVars['device_key'];
    $name = $allPostPutVars['name'];
    $db = new Db();
    $value = $db->query("Select * from value where device_key=:device_key and name=:name order by id desc limit 0,1", array("device_key"=>$device_key,"name"=>$name));
    if(count($value)>0){
        $name = $value[0]['name'];
        $value = $value[0]['value'];
        $value_type = $value[0]['value_type'];
        $data = array('err' => 'false', 'name' => $name,'value' => $value,'value_type' => $value_type); 
    }else{
        $data = array('err' => 'true', 'err_msg' => 'no value found'); 
    }
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://mysite')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->run();
