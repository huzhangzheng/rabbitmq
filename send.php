<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/7/14 14:35
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin','dog');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);
$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hello');
echo " [x] Sent 'Hello World!'\n";
$channel->close();
$connection->close();