<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class outWeeklyreportGenerate extends control
{
    public function doGeneration()
    {
        $this->config->debug = 2; //启动报错
       $this->loadModel('weeklyreportout');
       $generationInfo = $this->dao->select("*")->from(TABLE_OUTSIDEPROJECTWEEKLYREPORT_QUEUE)->where("isgeneration")->eq(0)->fetch();
        $result = [];
       if($generationInfo){
           $outplanList = $this->dao->select("*")->from(TABLE_OUTSIDEPLAN)->where("status")->in(['wait','notfinished','exceptionallyprogressing'])->andWhere('deleted')->eq(0)->fetchAll();

           foreach ($outplanList as $outplan){
               $result[] = $tepresult = $this->weeklyreportout->generateOutReport($outplan,$generationInfo->weeknum,$generationInfo->outreportStartDate,$generationInfo->outreportEndDate,'guestjk');

               if($tepresult['code'] == 200){

                   if(isset($tepresult['outreportid'])){
                       //增加操作记录
                       $actionID = $this->loadModel('action')->create('weeklyreportout', $tepresult['outreportid'], 'dogeneration','','','guestjk');

                   }
               }
           }
           $updata = [
               'isgeneration'=>1,
               'updateTime'=>helper::now()
           ];
           $this->dao->update(TABLE_OUTSIDEPROJECTWEEKLYREPORT_QUEUE)->data($updata)->where("id")->eq($generationInfo->id)->exec();
       }

       return $result;
    }
}

$push = new outWeeklyreportGenerate();
$res = $push->doGeneration();
saveLog($res, 'outWeeklyreportoutgeneration');