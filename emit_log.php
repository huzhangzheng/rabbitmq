<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/9/25 16:20
 */
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$connection = new AMQPStreamConnection('localhost',5672,'guest','guest');
$channel = $connection->channel();
$channel->exchange_declare('logs','fanout',false,false,false);

$data=implode(' ',array_slice($argv,1));
if(empty($data)) $data='Hello World!';

$msg= new AMQPMessage($data);
$channel->basic_publish($msg,'logs');

echo "[X] Sent ",$data,"\n";
$channel->close();
$connection->close();
