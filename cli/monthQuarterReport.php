<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class monthQuarterReport extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错

        $this->fetch('secondmonthreport', 'quarterStatic');


        echo 'success';
    }
}

$lock   = getTimeLock('monthQuarterReport', 5); //锁定防止重复
$object = new monthQuarterReport('monthQuarterReport', 'doChange');
$object->doChange();
saveLog('success', 'monthQuarterReport', 'doChange');
unlock($lock); //解除锁定
