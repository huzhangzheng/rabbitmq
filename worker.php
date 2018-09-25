<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/7/18 12:36
 */
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection=new AMQPStreamConnection('localhost',5672,'guest','guest','/');
$channel=$connection->channel();
$channel->queue_declare('task_queue',false,true,false,false);//queue_declare 的第三个参数设置为 true,队列持久化

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
$callback=function ($msg){
    echo '[X] received',$msg->body,"\n";
    sleep(substr_count($msg->body,'.'));
    echo '[X] done',"\n";
//    print_r($msg->delivery_info);
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);//消息确认
};
$channel->basic_qos(null,1,null);//在消息确认之前不再给消费者分配消息
$channel->basic_consume('task_queue','',false,false,false,false,$callback);//basic_consume的第四个参数设置为false(true表示不开启消息确认)，并且工作进程处理完消息后发送确认消息
while(count($channel->callbacks)){
    $channel->wait();
}
$channel->close();
$connection->close();
