<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class sendRemindMail extends control
{
    public function sendMail()
    {
        return $this->loadModel('my')->sendToRemindMail(); //正常调用模块及方法
    }
}
$lock = getTimeLock('sendRemindMail', 20); //锁定防止重复
$sendMail = new sendRemindMail();
$data = $sendMail->sendMail(); //执行
saveLog($data, 'sendRemindMail');
unlock($lock); //解除锁定