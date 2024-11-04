<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class datamanagementRemind extends control
{

    public function doRemind(): array
    {
        $this->config->debug = 2; //启动报错
        try {
            $res['remind'] = $this->loadModel('datamanagement')->timeRemind();
        } catch (Exception $e) {
            $res['remind'] = $e;
        }
        return $res;
    }
}
//$lock = getLock('datamanagementRemind', 5); //锁定防止重复
$lock = getTimeLock('datamanagementRemind', 5); //锁定防止重复
$push = new datamanagementRemind();
$data = $push->doRemind();
saveLog($data,'datamanagementRemind', 'doRemind');
unlock($lock); //解除锁定
