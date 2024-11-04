<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 月报统计历史数据 起始结束 日期刷新
     * @return void
     */
    public function updateMonthReportusemonth(){
        $monthreportList = $this->dao->select("*")->from(TABLE_WHOLE_REPORT)->where('month')->eq(0)->andWhere('dtype')->eq(1)->fetchAll();

        $account = 'guestjk';
        foreach ($monthreportList as $report){

            $updata = [];
            $month = date("n",strtotime($report->endday));
            $year = date("Y",strtotime($report->endday));
            $updata['month'] = $month;
            $updata['year'] = $year;

            $res = $this->dao->update(TABLE_WHOLE_REPORT)->data($updata)->where("id")->eq($report->id)->exec();
            
            $result[$report->id] = '类型：'.$report->type.'= 月份：'.$report->month."->".$updata['month'].'; 年份：'.$report->year."->".$updata['year'];
        }

        a("数据处理记录条数：".count($result));
        a($result);

    }
}