<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class defectRemindProjectManager extends control
{
    public function sendMail()
    {
        $this->loadModel('defect');
        $res = $this->defect->remindProjectManagerMail();
        return $res;
    }
}

$lock = getTimeLock('defectRemindProjectManager', 5); //锁定防止重复
$push = new defectRemindProjectManager();
$data = $push->sendMail();
saveLog($data,'defectRemindProjectManager', 'sendMail');
unlock($lock); //解除锁定
