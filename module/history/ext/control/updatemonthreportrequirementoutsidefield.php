<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 月报统计历史数据 刷新需求池 需求任务外部反馈超期统计表 反馈单总数和反馈超期率字段名字
     * @return void
     */
    public function updateMonthReportrequirementoutsidefield(){
        $monthreportList = $this->dao->select("id")->from(TABLE_WHOLE_REPORT)->where('type')->eq('requirement_outside')->fetchAll();
        $wholereportIDList = [];
        if($monthreportList){
            $wholereportIDList = array_column($monthreportList,'id');
        }
        $upresult = [];
        if($wholereportIDList){
            $detailReportList = $this->dao->select('id,detail')->from(TABLE_DETAIL_REPORT)->where('wholeID')->in($wholereportIDList)->fetchAll();
            if($detailReportList){
                foreach ($detailReportList as $detalReport){
                    $updataflag = false;
                    $tempjson = json_decode($detalReport->detail);
                    if(isset($tempjson->total)){
                        $tempjson->backTotal = $tempjson->total;
                        unset($tempjson->total);
                        $updataflag = true;
                    }
                    if(isset($tempjson->overdueRate)){
                        $tempjson->backExceedRate = $tempjson->overdueRate;
                        unset($tempjson->overdueRate);
                        $updataflag = true;

                    }
                    if($updataflag){
                        $this->dao->update(TABLE_DETAIL_REPORT)->data(['detail'=>json_encode($tempjson)])->where('id')->eq($detalReport->id)->exec();
                        $upresult[] = $detalReport->id;
                    }
                }
            }
        }

        a("数据处理记录条数：".count($upresult));
        a($upresult);

    }
}