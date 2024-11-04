<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class updateRequirementIfOverDate extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $res['inside'] = $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverDate'); //内部反馈超时 未做过处理的
        $res['out'] = $this->loadModel('requirement')->updateRequirementIfOverDate('ifOverTimeOutSide'); //外部反馈超时 未做过处理的
        return json_encode($res);
    }
}
//$lock = getLock('changeDemandStatus', 2); //锁定防止重复
$lock = getTimeLock('updateRequirementIfOverDate', 2); //锁定防止重复
$push = new updateRequirementIfOverDate();
$data = $push->doChange();
saveLog($data,'updateRequirementIfOverDate', 'doChange');
unlock($lock); //解除锁定
