<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class reviewQzAutoSetNotJoinMeeting extends control
{
    public function autoSetNotJoinMeeting()
    {
        return $this->loadModel('reviewqz')->autoSetNotJoinMeeting(); //正常调用模块及方法
    }
}
$lock = getTimeLock('reviewQzAutoSetNotJoinMeeting', 20); //锁定防止重复
$subObj = new reviewQzAutoSetNotJoinMeeting();
$data = $subObj->autoSetNotJoinMeeting(); //执行
saveLog($data, 'reviewQzAutoSetNotJoinMeeting', 'autoSetNotJoinMeeting');
unlock($lock); //解除锁定