<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class outWeeklyreportPushQZ extends control
{
    public function doPush()
    {
        $this->config->debug = 2; //启动报错
       $this->loadModel('weeklyreportout');
       $result = $this->weeklyreportout->batchPushWeeklyrportQz();
        if($result){
            //为外部周报推送增加历史记录
            foreach ($result as $outreportid=>$res){

                $this->loadModel('action')->create('weeklyreportout', $outreportid, 'dopush','',$res['message'],'guestjk');
            }
        }
       return $result;
    }
}

$push = new outWeeklyreportPushQZ();
$res = $push->doPush();

saveLog($res, 'outWeeklyreportout');