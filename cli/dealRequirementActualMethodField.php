<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class dealRequirementActualMethodField extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错
        $this->fetch('history', 'actualFixtype');
        echo 'success';
    }
}

$lock   = getTimeLock('dealRequirementActualMethodField', 5); //锁定防止重复
$object = new dealRequirementActualMethodField();
$object->doChange();
saveLog('success', 'dealRequirementActualMethodField', 'doChange');
unlock($lock); //解除锁定
