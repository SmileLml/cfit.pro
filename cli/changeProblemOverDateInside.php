<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class changeProblemOverDateInside extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $res[] = $this->loadModel('problem')->updateifOverDateInsideNew();
        $res[] = $this->loadModel('problem')->updateIsExceedByTime();
        return $res;
    }
}
$lock = getTimeLock('changeProblemOverDateInside', 2); //锁定防止重复
$push = new changeProblemOverDateInside();
$data = $push->doChange();
saveLog($data,'changeProblemOverDateInside', 'doChange');
unlock($lock); //解除锁定
