<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class demandRemindToEndMailFirst extends control
{
    public function sendMail()
    {
        $this->loadModel('demand');
        $res = $this->demand->remindToEndMailFirst();
        return $res;
    }
}

$lock = getTimeLock('demandRemindToEndMailFirst', 5); //锁定防止重复
$push = new demandRemindToEndMailFirst();
$data = $push->sendMail();
saveLog($data,'demandRemindToEndMailFirst', 'sendMail');
unlock($lock); //解除锁定
