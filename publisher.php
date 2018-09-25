<?php
/**
 * Created by PhpStorm.
 * 生产者
 * User: hzz
 * Date: 2018/7/13 14:50
 */
date_default_timezone_set('Asia/Shanghai');
$con_arr=array(
    'host' => '127.0.0.1',
    'port' => '5672',
    'login' => 'admin',
    'password' => 'admin',
    'vhost'=>'dog'
);
$exchange_name='e_linvo';
$queue_name='q_linvo';
$route_key='key1';

//创建连接和channel
$conn= new AMQPConnection($con_arr);
//print_r($conn);die;

if($conn->connect()){
    die('can not connect to broker');
}
$channel=new AMQPChannel($conn);

//创建交换机对象
$exchange=new AMQPExchange($channel);
$exchange->setName($exchange_name);
for($i=0;$i<5;$i++){
    sleep(1);
    $message='test message'.date('Y-m-d H:i:s');
    echo $exchange->publish($message,$route_key)."\n";
}
$conn->disconnect();
