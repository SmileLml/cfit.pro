<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 月报统计历史数据 刷新现场支持统计维度key
     * @return void
     */
    public function updateMonthReportsupportfield(){
        $monthreportList = $this->dao->select("id")->from(TABLE_WHOLE_REPORT)->where('type')->eq('support')->fetchAll();
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
                    if(isset($tempjson->a1)){
                        $tempjson->supporta1 = $tempjson->a1;
                        unset($tempjson->a1);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a2)){
                        unset($tempjson->a2);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a3)){
                        $tempjson->supporta3 = $tempjson->a3;
                        unset($tempjson->a3);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a4)){
                        $tempjson->supporta4 = $tempjson->a4;
                        unset($tempjson->a4);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a5)){
                        $tempjson->supporta5 = $tempjson->a5;
                        unset($tempjson->a5);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a6)){
                        $tempjson->supporta6 = $tempjson->a6;
                        unset($tempjson->a6);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a7)){
                        $tempjson->supporta7 = $tempjson->a7;
                        unset($tempjson->a7);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a8)){
                        unset($tempjson->a8);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a9)){
                        unset($tempjson->a9);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a10)){
                        unset($tempjson->a10);
                        $updataflag = true;
                    }

                    if(isset($tempjson->a11)){
                        $tempjson->supporta11 = $tempjson->a11;
                        unset($tempjson->a11);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a12)){
                        $tempjson->supporta12 = $tempjson->a12;
                        unset($tempjson->a12);
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