<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class changeOutsidePlansStatus extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $res[] = $this->loadModel('outsideplan')->changeStatusByProjectPlan();
        return $res;
    }
}
//$lock = getLock('changeOutsidePlansStatus', 5); //锁定防止重复
$lock = getTimeLock('changeOutsidePlansStatus', 5); //锁定防止重复
$push = new changeOutsidePlansStatus();
$data = $push->doChange();
saveLog($data,'changeOutsidePlansStatus', 'doChange');
unlock($lock); //解除锁定
