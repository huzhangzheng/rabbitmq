<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/7/18 10:43
 */
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection=new AMQPStreamConnection('localhost',5672,'guest','guest','/');
$channel=$connection->channel();
$channel->queue_declare('task_queue',false,true,false,false);//队列持久化

$data=implode(' ',array_slice($argv,1));
if(empty($data)) $data='Hello World!';

$msg=new AMQPMessage($data,array('delivery_mode'=>AMQPMessage::DELIVERY_MODE_PERSISTENT));//消息持久化
$channel->basic_publish($msg,'','task_queue');
echo "[X]Sent",$data,"\n";
$channel->close();
$connection->close();