<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class updateDemandDeliveryOver extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $res= $this->loadModel('demand')->updateDemandDeliveryOver(); //需求条目交付是否超期状态脚本
        return json_encode($res);
    }
}
$lock = getTimeLock('updateDemandDeliveryOver', 2); //锁定防止重复
$push = new updateDemandDeliveryOver();
$data = $push->doChange();
saveLog($data,'updateDemandDeliveryOver', 'doChange');
unlock($lock); //解除锁定
