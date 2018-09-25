<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/7/14 14:38
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
//$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin','dog');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C\n";
$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};
$channel->basic_consume('hello', '', false, true, false, false, $callback);
while (count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();