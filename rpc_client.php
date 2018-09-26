<?php
/**
 * Created by PhpStorm.
 * User: hzz
 * Date: 2018/9/26 15:41
 * 发送请求，接收服务端返回结果
 */
require_once __DIR__.'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
class FibonacciRpcClient{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;
    public function __construct()
    {
        $this->connection=new AMQPStreamConnection('localhost','5672','guest','guest');
        $this->channel=$this->connection->channel();
        list($this->callback_queue)=$this->channel->queue_declare('',false,false,true,false);
        $this->channel->basic_consume($this->callback_queue,'',false,false,false,false,array($this,'on_response'));
    }
    public function on_response($req){
        if($req->get('correlation_id')==$this->corr_id){
            $this->response=$req->body;
        }
    }
    public function call($n){
        $this->response=null;
        $this->corr_id=uniqid();
        $msg=new AMQPMessage((string)$n,['correlation_id'=>$this->corr_id,'reply_to'=>$this->callback_queue]);
        $this->channel->basic_publish($msg,'','rpc_queue');
        while(!$this->response){
            $this->channel->wait();
        }
        return (int)$this->response;
    }
}
$fibnacci_rpc=new FibonacciRpcClient();
$response=$fibnacci_rpc->call(30);
echo 'got',$response,"\n";