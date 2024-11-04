<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class sendOverTimePushMail extends control
{
    public function sendMail()
    {
        return $this->loadModel('monitorservice')->pushOverTime(); //正常调用模块及方法
    }
}
$lock = getTimeLock('sendOverTimePushMail', 5); //锁定防止重复
$sendMail = new sendOverTimePushMail();
$data = $sendMail->sendMail(); //执行
saveLog($data, 'sendOverTimePushMail');
unlock($lock); //解除锁定