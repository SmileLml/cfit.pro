<?php

class redismanage
{
    public $conn;
    public function __construct()
    {
        global $config;
        $this->conn = new Redis();


        $this->conn->connect($config->redis->host,$config->redis->port);
        if(isset($config->redis->password)){
            $ret = $this->conn->auth($config->redis->password);
            /*if(!$ret){
                throw new Exception("redis auth fail");
            }*/
        }
        $pingres = $this->conn->ping('ping');


    }

    public function __destruct(){
        $this->conn->close();
    }
}