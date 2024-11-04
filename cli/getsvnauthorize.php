<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class getsvnauthorize extends control
{
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['getauthorize'] = $this->loadModel('myauthority')->getSvnAuthorityModel();
        } catch (Exception $e) {
            $res['getauthorize'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('getsvnauthorize', 60); //锁定防止重复
$push = new getsvnauthorize();
$data = $push->doPush();
saveLog($data,'getsvnauthorize', 'doPush');
unlock($lock); //解除锁定
