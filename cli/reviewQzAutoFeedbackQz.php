<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class reviewQzAutoFeedbackQz extends control
{
    public function autoFeedbackQz()
    {
        return $this->loadModel('reviewqz')->autoFeedbackQz(); //正常调用模块及方法
    }
}
$lock = getTimeLock('reviewQzAutoFeedbackQz', 20); //锁定防止重复
$subObj = new reviewQzAutoFeedbackQz();
$data = $subObj->autoFeedbackQz(); //执行
saveLog($data, 'reviewQzAutoFeedbackQz', 'autoFeedbackQz');
unlock($lock); //解除锁定