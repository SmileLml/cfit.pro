<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class demandRemindToEndMailSecond extends control
{
    public function sendMail()
    {
        $this->loadModel('demand');
        $res = $this->demand->remindToEndMailSecond();
        return $res;
    }
}

$lock = getTimeLock('demandRemindToEndMailSecond', 5); //锁定防止重复
$push = new demandRemindToEndMailSecond();
$data = $push->sendMail();
saveLog($data,'demandRemindToEndMailSecond', 'sendMail');
unlock($lock); //解除锁定
