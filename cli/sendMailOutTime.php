<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class sendMailOutTime extends control
{
    public function sendMail()
    {
        $res[] = $this->loadModel('problem')->insideFeedback();
        $res[] = $this->loadModel('problem')->outsideFeedback();
        //$res[] = $this->loadModel('problem')->sendmailBySolvingOutTime();
        $res[] = $this->loadModel('requirement')->sendmailByOutTime();
        $res[] = $this->loadModel('requirement')->sendmailByOutTimeOutside();//需求任务-外部
        //$res[] = $this->loadModel('demand')->sendmailByOutTime();
        return $res;
    }
}

$lock = getTimeLock('sendMailOutTime', 5); //锁定防止重复
$push = new sendMailOutTime();
$data = $push->sendMail();
saveLog($data,'sendMailOutTime', 'sendMail');
unlock($lock); //解除锁定
