<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 月报统计历史数据 刷新变更类型统计表的 统计维度key
     * @return void
     */
    public function updateMonthReportmodifywholefield(){
        $monthreportList = $this->dao->select("id")->from(TABLE_WHOLE_REPORT)->where('type')->eq('modifywhole')->fetchAll();
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
                        $tempjson->modifyandcncca1 = $tempjson->a1;
                        unset($tempjson->a1);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a2)){
                        $tempjson->modifyandcncca2 = $tempjson->a2;
                        unset($tempjson->a2);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a3)){
                        $tempjson->modifyandcncca3 = $tempjson->a3;
                        unset($tempjson->a3);
                        $updataflag = true;
                    }
                    if(isset($tempjson->a4)){
                        $tempjson->modifyandcncca4 = $tempjson->a4;
                        unset($tempjson->a4);
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