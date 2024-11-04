<?php
include '../../control.php';
class mySecondmonthreport extends secondmonthreport
{
    public function quarterStatic($type = 0, $quarter = 0, $year = 0)
    {
        $secondMonthReport = $this->loadModel('secondmonthreport');
        $secondMonthReport->initializationTime($quarter, $year);
        //问题整体情况统计表
        if(empty($type) || 1 == $type || 'problemOverall' == $type){
            $this->addProblemOverall();
        }
        if(empty($type) || 2 == $type || 'problemCompletedPlan' == $type){
            $this->addProblemCompletedPlan();
        }
        //内部反馈超期统计表
        if(empty($type) || 3 == $type || 'problemExceedBackIn' == $type){
            $this->addProblemExceedBackIn();
        }
        //需求任务内部反馈超期统计表
        if(empty($type) || 4== $type || 'requirementExceedBackIn' == $type){
            $this->addRequirementExceedBackIn();
        }
        //任务工单类型统计表
        if(empty($type) || 5 == $type || 'secondOrderClass' == $type){
            $this->addSecondOrderClass();
        }
        //变更异常统计表
        if(empty($type) || 6 == $type || 'modifyabnormal' == $type){
            $this->addModifyAbnormal();
        }
        if(empty($type) || 7 == $type || 'support' == $type){
            $this->addSupport();
        }
        if(empty($type) || 8 == $type || 'workload' == $type){
            $this->addWorkload();
        }

        echo '生成成功';
    }

    /**
     * 问题整体情况统计表  历史结转数据参与统计
     * @return void
     */
    private function addProblemOverall()
    {
        $this->loadModel('problem');
        $secondMonthReport = $this->loadModel('secondmonthreport');
        $formType = 'problemOverall';

        //获取统计时间范围
        $problemOverallDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        //生成季度报表
        $problemOverallIDS = $secondMonthReport->problemOverallQuarter($problemOverallDate);

        //问题整体统计表 基础快照
        $this->problemphoto($problemOverallDate,$problemOverallDate['time'],$formType,'all',1);

        //问题整体统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->problempartphoto($problemOverallDate,$problemOverallDate['time'],$problemOverallIDS,$fieldArrs['form'],$formType);
    }

    /**
     * 内部反馈超期统计表
     * @return void
     */
    private function addProblemExceedBackIn()
    {
        $this->loadModel('problem');
        $secondMonthReport = $this->loadModel('secondmonthreport');
        $formType = 'problemExceedBackIn';

        $problemExceedBackInDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $problemExceedBackInIDS = $secondMonthReport->problemHistoryExceedBackInQuarter($problemExceedBackInDate);

        //内部反馈超期统计表 基础快照
        $this->problemphoto($problemExceedBackInDate,$problemExceedBackInDate['time'],$formType,'all',0);

        //内部反馈超期统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->problempartphoto($problemExceedBackInDate,$problemExceedBackInDate['time'],$problemExceedBackInIDS,$fieldArrs['form'],$formType);
    }

    /**
     * 需求任务内部反馈超期统计表
     * @return void
     */
    private function addRequirementExceedBackIn()
    {
        $secondMonthReport = $this->loadModel('secondmonthreport');
        $formType = 'requirement_inside';

        //获取开始结束时间，传入
        $requirement_insideDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $requirement_insideIDS = $secondMonthReport->requirementHistoryInsideQuarter($requirement_insideDate);

        //需求任务内部反馈超期统计表 基础快照
        $this->requirementphoto($requirement_insideDate,$requirement_insideDate['time'],$formType,'all',0);

        //需求任务内部反馈超期统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->requirementpartphoto($requirement_insideDate,$requirement_insideDate['time'],$requirement_insideIDS,$fieldArrs['form'],$formType);
    }

    /**
     * 工单类型统计表 历史结转数据参与统计
     * @return void
     */
    private function addSecondOrderClass()
    {
        $this->loadModel('secondorder');
        $secondMonthReport = $this->loadModel('secondmonthreport');

        $formType = 'secondorderclass';
        //获取开始结束时间，传入
        $secondorderclassDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $secondorderclassIDS = $secondMonthReport->secondOrderHistoryClassQuarter($secondorderclassDate);

        //工单类型统计表 基础快照
        $this->secondorderclassphoto($secondorderclassDate,$secondorderclassDate['time'],$formType,'all',1);

        //工单类型统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->secondorderclasspartphoto($secondorderclassDate,$secondorderclassDate['time'],$secondorderclassIDS,$fieldArrs['form'],$formType);
    }

    /**
     * 变更异常统计表
     * @return void
     */
    private function addModifyAbnormal()
    {
        $secondMonthReport = $this->loadModel('secondmonthreport');

        $formType = 'modifyabnormal';
        //获取开始结束时间，传入
        $modifyabnormalDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $modifyabnormalIDS = $secondMonthReport->modifyHistoryNormalQuarter($modifyabnormalDate);

        //变更异常统计表 基础快照
        $this->modifywholephoto($modifyabnormalDate,$modifyabnormalDate['time'],$formType,'all',0);

        //变更异常统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->modifywholepartphoto($modifyabnormalDate,$modifyabnormalDate['time'],$modifyabnormalIDS,$fieldArrs['form'],$formType);
    }

    /**
     * 现场支持统计表
     * @return void
     */
    private function addSupport()
    {
        $secondMonthReport = $this->loadModel('secondmonthreport');

        $formType = 'support';
        //获取开始结束时间，传入
        $supportDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $supportIDS = $secondMonthReport->supportHistoryQuarter($supportDate);

        //现场支持统计表 基础快照
        $this->supportphoto($supportDate,$supportDate['time'],$formType,'all',0);

        //现场支持统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->supportpartphoto($supportDate,$supportDate['time'],$supportIDS,$fieldArrs['form'],$formType);
    }

    /**
     * 工作量统计表
     * @return void
     */
    private function addWorkload()
    {
        $secondMonthReport = $this->loadModel('secondmonthreport');
        $formType = 'workload';

        $workloadDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $workloadIDS = $secondMonthReport->workloadHistoryQuarter($workloadDate);

        //基础MA工作量统计表 基础快照
        $this->workloadphoto($workloadDate,$workloadDate['time'],$formType,'all',0);

        //基础MA工作量统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->workloadpartphoto($workloadDate,$workloadDate['time'],$workloadIDS,$fieldArrs['form'],$formType);
    }

    private function addProblemCompletedPlan()
    {
        $secondMonthReport = $this->loadModel('secondmonthreport');
        $formType = 'problemCompletedPlan';

        $problemDate = $secondMonthReport->getTimeRangeByQuarter(3, $formType);

        $problemIDS = $secondMonthReport->problemCompletedPlanHistoryQuarter($problemDate);

        //内部反馈超期统计表 基础快照
        $this->problemphoto($problemDate,$problemDate['time'],$formType,'all',0);

        //内部反馈超期统计表 表单快照
        $fieldArrs = $secondMonthReport->getexportField($formType,'');
        $this->problempartphoto($problemDate,$problemDate['time'],$problemIDS,$fieldArrs['form'],$formType);
    }
}
