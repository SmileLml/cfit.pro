<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class destroyInfoqzPush extends control
{
    /**
     * User: chendongcheng
     * Date: 2022/11/25
     * 推送数据销毁
     */
    public function doPush(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['destroyInfoqzPush'] = $this->loadModel('infoqz')->pushDestroyedData();
        } catch (Exception $e) {
            $res['destroyInfoqzPush'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('destroyInfoqzPush', 5); //锁定防止重复
$lock = getTimeLock('destroyInfoqzPush', 5); //锁定防止重复
$push = new destroyInfoqzPush();
$data = $push->doPush();
saveLog($data,'destroyInfoqzPush', 'doPush');
unlock($lock); //解除锁定
