<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 月报统计历史数据 起始结束 日期刷新
     * @return void
     */
    public function updateMonthReportStartAndEndTime(){
        $monthreportList = $this->dao->select("*")->from(TABLE_WHOLE_REPORT)->where('month')->gt(0)->andWhere('dtype')->eq(1)->fetchAll();
        $account = 'guestjk';
        foreach ($monthreportList as $report){

            $updata = [];

            // 需求池存入的月份比问题池多1个月。需要单独处理
            if(strpos($report->type,'requirement') !== false || strpos($report->type,'demand') !== false){
                $starttime = $report->year.'-01-01';
                $month = (int)$report->month -1;
                $endtime = $report->year.'-'.$month.'-'.date('t',strtotime($report->year.'-'.$month.'-01'));
                $updata['startday'] = $starttime;
                $updata['endday'] = $endtime;
            }else{
                $starttime = $report->year.'-01-01';
                $endtime = $report->year.'-'.$report->month.'-'.date('t',strtotime($report->year.'-'.$report->month.'-01'));
                $updata['startday'] = $starttime;
                $updata['endday'] = $endtime;
            }

            $updata['month'] = 0;



            $res = $this->dao->update(TABLE_WHOLE_REPORT)->data($updata)->where("id")->eq($report->id)->exec();
            
            $result[$report->id] = '类型：'.$report->type.'= 月份：'.$report->month."->".$updata['month'].';起始时间：'.$report->startday."->".$updata['startday'].';截止时间：'.$report->endday."->".$updata['endday'];
        }

        a("数据处理记录条数：".count($result));
        a($result);

    }
}