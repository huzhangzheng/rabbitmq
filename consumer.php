<?php
/**
 * Created by PhpStorm.
 * 消费者
 * User: hzz
 * Date: 2018/7/13 14:27
 */
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
$conn=new AMQPConnection($con_arr);
if(!$conn->connect()){
    die('can not connect to the broker');
}
$channel=new AMQPChannel($conn);

//创建交换机
$exchange=new AMQPEXchange($channel);
$exchange->setName($exchange_name);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_DURABLE);
echo 'exchange status'.$exchange->declareExchange()."\n";

//创建队列
$queue=new AMQPQueue($channel);
$queue->setName($queue_name);
$queue->setFlags(AMQP_DURABLE);
echo 'message total'.$queue->declareQueue()."\n";

//绑定交换机和队列，指定路由key
$queue->bind($exchange_name,$route_key);

//阻塞模式接受消息
while(true){
    $queue->consume('processMessage');
}

$conn->disconnect();

/**
 * 消费回调函数
 * 处理消息
 */
function processMessage($envelope,$queue){
    $msg=$envelope->getBody();
    echo $msg."\n";
    $queue->ack($envelope->getDeliverTag());//手动发送ack应答
}



