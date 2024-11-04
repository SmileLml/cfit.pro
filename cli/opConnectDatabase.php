<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class opConnectDatabase extends control
{
    public function doUpdate()
    {
        $this->config->debug = 2; //启动报错
        $account = 'admin';
        $updateParams = new stdClass();
        $updateParams->last = time();
        $dept = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($account)->fetch();
//        $res = $this->loadModel('user')->updateUserInfo($account, $updateParams);
        if ($dept==11)
        {
            return True;
        }
        return False;
    }
}
$lock = getTimeLock('opConnectDatabase', 2); //锁定防止重复
$ret = new opConnectDatabase();
$data = $ret->doUpdate();
echo $data;
//saveLog($data,'opConnectDatabase', 'doUpdate');
unlock($lock); //解除锁定
