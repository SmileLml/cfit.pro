<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class putproductionPush extends control
{
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['putproductionPush'] = $this->loadModel('putproduction')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['putproductionPush'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('putproductionPush', 5); //锁定防止重复
$push = new putproductionPush();
$data = $push->doPush();
saveLog($data,'putproductionPush', 'doPush');
unlock($lock); //解除锁定
