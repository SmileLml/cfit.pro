<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class changeDemandStatus extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $res[] = $this->loadModel('problem')->changeBySecondLineV3();
//        $res[] = $this->loadModel('demand')->changeBySecondLineV3();
        $res[] = $this->loadModel('requirement')->changeStatus();
        $res[] = $this->loadModel('opinion')->changeStatus();
        return $res;
    }
}
//$lock = getLock('changeDemandStatus', 2); //锁定防止重复
$lock = getTimeLock('changeDemandStatus', 2); //锁定防止重复
$push = new changeDemandStatus();
$data = $push->doChange();
saveLog($data,'changeDemandStatus', 'doChange');
unlock($lock); //解除锁定
