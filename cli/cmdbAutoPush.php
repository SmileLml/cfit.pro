<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class cmdbAutoPush extends control
{
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['cmdbAutoPush'] = $this->loadModel('cmdbsync')->getUnPushedAndPush();
        } catch (Exception $e) {
            $res['cmdbAutoPush'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('modifyPush', 5); //锁定防止重复
$lock = getTimeLock('cmdbAutoPush', 5); //锁定防止重复
$push = new cmdbAutoPush();
$data = $push->doPush();
saveLog($data,'cmdbAutoPush', 'doPush');
unlock($lock); //解除锁定
