<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/9/26 15:23
 * 服务端要接数据，并把数据返回客户端
 */
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$connection = new AMQPStreamConnection('localhost','5672','guest','guest');
$channel=$connection->channel();
$channel->queue_declare('rpc_queue',false,false,false,false);

echo '[X] waiting RPC requests',"\n";
$callback=function ($req){
    $n=(int)$req->body;
    echo "fib(",$n,")\n";
    $msg= new AMQPMessage((string)fib($n),['correlation_id'=>$req->get('correlation_id')]);
    $req->delivery_info['channel']->basic_publish($msg,'',$req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);
};
$channel->basic_qos(null,1,null);
$channel->basic_consume('rpc_queue','',false,false,false,false,$callback);
while (count($channel->callbacks)){
    $channel->wait();
}
$channel->close();
$connection->close();

function fib($n){
    if($n==0){
        return 0;
    }else if($n==1){
        return 1;
    }else{
        return fib($n-2)+fib($n-1);
    }
}