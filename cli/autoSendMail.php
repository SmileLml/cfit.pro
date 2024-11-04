<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class autoSendMail extends control
{
    public function sendReviewMail()
    {
        return $this->loadModel('review')->autosendmail(); //正常调用模块及方法
    }
}
$lock = getTimeLock('autoSendMail', 20); //锁定防止重复
$sendMail = new autoSendMail();
$data = $sendMail->sendReviewMail(); //执行
saveLog($data, 'autoSendMail');
unlock($lock); //解除锁定