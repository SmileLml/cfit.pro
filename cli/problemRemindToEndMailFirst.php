<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class problemRemindToEndMailFirst extends control
{
    public function sendMail()
    {
        $this->loadModel('problem');
        $res = $this->problem->remindToEndMailFirst();
        return $res;
    }
}

$lock = getTimeLock('problemRemindToEndMailFirst', 5); //锁定防止重复
$push = new problemRemindToEndMailFirst();
$data = $push->sendMail();
saveLog($data,'problemRemindToEndMailFirst', 'sendMail');
unlock($lock); //解除锁定
