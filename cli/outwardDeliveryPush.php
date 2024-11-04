<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class push extends control
{
    public function doPush()
    {
        $this->config->debug = 2; //启动报错
        if($this->config->global->outwardDeliveryCron == 'disable'){ return "未开启定时推送"; }
        try {
            $res['testingrequest'] = $this->loadModel('testingrequest')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['testingrequest'] = $e;
        }
        try {
            $res['productenroll'] = $this->loadModel('productenroll')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['productenroll'] = $e;
        }
        try {
            $res['modifycncc'] = $this->loadModel('modifycncc')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['modifycncc'] = $e;
        }
        try {
            $res['infoqz'] = $this->loadModel('infoqz')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['infoqz'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('outwardDelivery', 2); //锁定防止重复
$lock = getTimeLock('outwardDelivery', 2); //锁定防止重复
$push = new push();
$data = $push->doPush();
saveLog($data, 'outwardDelivery');
unlock($lock); //解除锁定
