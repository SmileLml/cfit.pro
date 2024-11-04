<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class reviewQzMailExpertIsJoinMeeting extends control
{
    public function sendMail()
    {
        return $this->loadModel('reviewqz')->mailExpertIsJoinMeeting(); //正常调用模块及方法
    }
}
$lock = getTimeLock('reviewQzMailExpertIsJoinMeeting', 20); //锁定防止重复
$sendMail = new reviewQzMailExpertIsJoinMeeting();
$data = $sendMail->sendMail(); //执行
saveLog($data, 'reviewQzMailExpertIsJoinMeeting', 'sendMail');
unlock($lock); //解除锁定