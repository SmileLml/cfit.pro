<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class test extends control
{
    public function testme()
    {
        return $this->loadModel('downloads')->testTime(); //正常调用模块及方法
    }
}
getLock(); //锁定防止重复
$test = new test();
$data = $test->testme(); //执行
saveLog($data); //存日志
unlock(); //解除锁定