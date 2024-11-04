<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class reviewQzMailFeedbackQz extends control
{
    public function sendMail()
    {
        return $this->loadModel('reviewqz')->mailFeedbackQz(); //正常调用模块及方法
    }
}
$lock = getTimeLock('reviewQzMailFeedbackQz', 20); //锁定防止重复
$sendMail = new reviewQzMailFeedbackQz();
$data = $sendMail->sendMail(); //执行
saveLog($data, 'reviewQzMailFeedbackQz', 'sendMail');
unlock($lock); //解除锁定