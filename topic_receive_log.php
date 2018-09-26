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
$channel->exchange_declare('topic_logs','topic',false,false,false);

list($queue_name)=$channel->queue_declare("",false,false,true,false);
$severities=array_slice($argv,1);
if(empty($severities )) {
    file_put_contents('php://stderr', "Usage: $argv[0] [info] [warning] [error]\n");
    exit(1);
}
foreach ($severities as $severity){
    $channel->queue_bind($queue_name,'topic_logs',$severity);
}
echo '[*] waiting for logs. To exit press CTRL+C',"\n";
$callback=function ($msg){
    echo '[X] ',$msg->delivery_info['routing_key'],':',$msg->body,"\n";
};
$channel->basic_consume($queue_name,'',false,true,false,false,$callback);
while(count($channel->callbacks)){
    $channel->wait();
}

$channel->close();
$connection->close();
