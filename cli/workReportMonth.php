<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class workReportMonth extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $flag = 2;//月提醒
        $res[] = $this->loadModel('workReport')->sendmail($flag);
        return $res;
    }
}
$lock = getTimeLock('workReportMonth', 2); //锁定防止重复
$push = new workReportMonth();
$data = $push->doChange();
saveLog($data,'workReportMonth', 'doChange');
unlock($lock); //解除锁定
