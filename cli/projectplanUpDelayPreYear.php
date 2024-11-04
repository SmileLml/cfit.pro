<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class projectplanUpDelayPreYear extends control
{
    public function doCliUpIsDelayLog()
    {
        $this->config->debug = 2; //启动报错
        $projectplanModel = $this->loadModel('projectplan');
        $projectplanList = $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where('deleted')->eq(0)->fetchAll();
        $account = 'guestjk';
        $currYear = date("Y");

        $preYear = date("Y",strtotime('-1 year',strtotime($currYear.'-01-01')));

        $result = [];
       if($projectplanList){


           foreach ($projectplanList as $projectplan){
               $delayLog = $this->dao->select("*")->from(TABLE_PROJECTPLAN_ISDELAY_LOG)->where('planID')->eq($projectplan->id)->andWhere('year')->eq($preYear)->fetch();
               //如果不存在则插入， 存在 则暂时不更新。
               if(!$delayLog){
                   $indata = [];
                   $indata['planID'] = $projectplan->id;
                   $indata['year'] = $preYear;
                   $indata['status'] = $projectplan->isDelayPreYear;
                   $indata['createdDate'] = helper::now();
                   $indata['createdBy'] = $account;
                   $this->dao->insert(TABLE_PROJECTPLAN_ISDELAY_LOG)->data($indata)->exec();

               }
               //更新 年度计划的  是否上一年结转 状态
               if(in_array($projectplan->insideStatus,['done','cancel','abort']) || $projectplan->year == $currYear){
                   $updata['isDelayPreYear'] = 2;
               }else{
//            当【内部项目状态】为非上述状态时，字段【是否上一年度延续】初始化为“是”
                   $updata['isDelayPreYear'] = 1;
               }
               $res = $this->dao->update(TABLE_PROJECTPLAN)->data($updata)->where("id")->eq($projectplan->id)->exec();
               if($res){
                   $this->loadModel('action')->create('projectplan', $projectplan->id, 'docliupisdelaylog',$projectplan->isDelayPreYear."->".$updata['isDelayPreYear'],'',$account);
               }

               $result[$projectplan->id] = $projectplan->isDelayPreYear."->".$updata['isDelayPreYear'];

           }
       }


       return $result;
    }
}

$push = new projectplanUpDelayPreYear();
$res = $push->doCliUpIsDelayLog();

saveLog($res, 'projectplan');