<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class monthReport extends control
{
    public function doChange()
    {
        $this->config->debug = 2; //启动报错

        $time       = time();
        /*$paramyear  = date("Y");
        $parammonth = date("n");*/

        $endtime='';
        $starttime='';
        $dtype=1;
        $secondmonthreportModel = $this->loadModel('secondmonthreport');
        //简洁版统计调用方法。
        //问题池统计
        $this->fetch('secondmonthreport', 'problemStatistics');
        //需求池统计
        $this->fetch('secondmonthreport', 'demandStatistics');
        //工单池
        $this->fetch('secondmonthreport', 'secondOrderStatistics');
        //现场支持
        $this->fetch('secondmonthreport', 'supportStatistics');
        //工作量统计
        $this->fetch('secondmonthreport', 'workloadStatistics');
        //变更统计
        $this->fetch('secondmonthreport', 'modifyStatistics');
        /*
        //需求池统计
        $this->fetch('secondmonthreport', 'customDemandMonthReport', [$endtime, $time, $starttime,$dtype]);
        //工单池
        $this->fetch('secondmonthreport', 'customSecondOrderStatistics', [$endtime, $time, $starttime,$dtype]);
        //变更统计
        $this->fetch('secondmonthreport', 'customModifywholeStatistics', [$endtime, $time, $starttime,$dtype]);
        //现场支持
        $this->fetch('secondmonthreport', 'customSupportStatistics', [$endtime, $time, $starttime,$dtype]);
        //工作量统计
        $this->fetch('secondmonthreport', 'customWorkloadStatistics', [$endtime, $time, $starttime,$dtype]);*/
        echo 'success';




        //暂时注释。待稳定后删除 上边采用了 新型的执行方法。更简洁
        /*
        //问题池生成统计表
        $useIDS = $secondmonthreportModel->problemStatistics($endtime, $time,$starttime,$dtype);

        //需求池生成统计表
        $UseDemandIDS = $this->loadModel('demand')->monthReport($endtime, $time,$starttime,$dtype);

        //问题池基础数据快照
        $this->fetch('secondmonthreport', 'problemphoto', [$endtime, $time, $starttime,$dtype]);
        $this->fetch('secondmonthreport', 'customProblemStatistics', [$endtime, $time, $starttime,$dtype]);
        //问题池反馈超期快照
        $this->fetch('secondmonthreport', 'problemphoto', [$endtime, $time, $starttime,$dtype,'feedback']);


        $this->fetch('secondmonthreport', 'requirementphoto', [$endtime, $time, $starttime,$dtype]);
        $this->fetch('secondmonthreport', 'demandphoto', [$endtime, $time, $starttime,$dtype]);

        $this->loadModel('problem');

        //问题池快照
        $this->fetch('secondmonthreport','problempartphoto',[$endtime,$time,$starttime,$dtype,$useIDS['overallsIDS'],$this->config->problem->list->exportMonthReportPartFields1,'problemoverall']);

        $this->fetch('secondmonthreport','problempartphoto',[$endtime,$time,$starttime,$dtype,$useIDS['waitSolvesIDS'],$this->config->problem->list->exportMonthReportPartFields1,'problemwaitsolve']);
        $this->fetch('secondmonthreport','problempartphoto',[$endtime,$time,$starttime,$dtype,$useIDS['exceedsIDS'],$this->config->problem->list->exportMonthReportPartFields1,'problemexceed']);
        $this->fetch('secondmonthreport','problempartphoto',[$endtime,$time,$starttime,$dtype,$useIDS['exceedBackInsIDS'],$this->config->problem->list->exportMonthReportPartFields2,'problemexceedbackin']);
        $this->fetch('secondmonthreport','problempartphoto',[$endtime,$time,$starttime,$dtype,$useIDS['exceedBackOutsIDS'],$this->config->problem->list->exportMonthReportPartFields3,'problemexceedbackout']);


        $requirementModel = $this->loadModel('requirement');
        $demandModel = $this->loadModel('demand');


        $this->fetch('secondmonthreport','demandpartphoto',[$endtime,$time,$starttime,$dtype,$UseDemandIDS['wholeDemandIDS'],$this->config->demand->list->exportMonthReportPartFields1,'demand_whole']);
        $this->fetch('secondmonthreport','demandpartphoto',[$endtime,$time,$starttime,$dtype,$UseDemandIDS['demand_unrealizedIDS'],$this->config->demand->list->exportMonthReportPartFields1,'demand_unrealized']);
        $this->fetch('secondmonthreport','demandpartphoto',[$endtime,$time,$starttime,$dtype,$UseDemandIDS['demand_realizedIDS'],$this->config->demand->list->exportMonthReportPartFields1,'demand_realized']);
        $this->fetch('secondmonthreport','requirementpartphoto',[$endtime,$time,$starttime,$dtype,$UseDemandIDS['requirement_insideIDS'],$this->config->requirement->exportlist->exportMonthReportPartFields1,'requirement_inside']);
        $this->fetch('secondmonthreport','requirementpartphoto',[$endtime,$time,$starttime,$dtype,$UseDemandIDS['requirement_outsideIDS'],$this->config->requirement->exportlist->exportMonthReportPartFields2,'requirement_outside']);

        //任务工单统计表
        $secondmonthreportModel->secondorderacceptStatistics($endtime,$time, $starttime,$dtype);
        $secondmonthreportModel->secondorderclassStatistics($endtime,$time, $starttime,$dtype);
        //变更统计
        $secondmonthreportModel->modifywholeStatistics($endtime,$time, $starttime,$dtype);
        $secondmonthreportModel->modifyabnormalStatistics($endtime,$time, $starttime,$dtype);*/



    }
}

$lock   = getTimeLock('workReportMonth', 5); //锁定防止重复
$object = new monthReport('monthReport', 'doChange');
$object->doChange();
saveLog('success', 'monthReport', 'doChange');
unlock($lock); //解除锁定
