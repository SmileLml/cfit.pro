<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class changeProblemCompletePlanAndResult extends control
{
    public function doChange()
    {

        $res['updateCompletedPlanProblemId'] = $this->loadModel('problem')->getCompletedPlan(); //是否按计划完成
        $res['updateexaminationResultProblemId'] = $this->loadModel('problem')->getExaminationResult();//审核结果
        return $res;
    }
}

$lock = getTimeLock('changeProblemCompletePlanAndResult', 2); //锁定防止重复
$push = new changeProblemCompletePlanAndResult();
$data = $push->doChange();
saveLog($data,'changeProblemCompletePlanAndResult', 'doChange');
unlock($lock); //解除锁定
