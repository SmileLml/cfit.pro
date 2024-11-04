<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class problemRemindToEndMailSecond extends control
{
    public function sendMail()
    {
        $this->loadModel('problem');
        $res = $this->problem->remindToEndMailSecond();
        return $res;
    }
}

$lock = getTimeLock('problemRemindToEndMailSecond', 5); //锁定防止重复
$push = new problemRemindToEndMailSecond();
$data = $push->sendMail();
saveLog($data,'problemRemindToEndMailSecond', 'sendMail');
unlock($lock); //解除锁定
