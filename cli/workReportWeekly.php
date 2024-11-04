<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class workReportWeekly extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $flag = 1;//周提醒
        $res[] = $this->loadModel('workReport')->sendmail($flag);
        return $res;
    }
}
$lock = getTimeLock('workReportWeekly', 2); //锁定防止重复
$push = new workReportWeekly();
$data = $push->doChange();
saveLog($data,'workReportWeekly', 'doChange');
unlock($lock); //解除锁定
