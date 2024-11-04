<?php
include '../../control.php';
class mySecondmonthreport extends secondmonthreport
{
//    考核表单 菜单 demo 具体业务逻辑后期开发。
    public function examineproblemexceed($wholeID = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 5, $pageID = 1)
    {
        $staticType = 'problemOverall';
        $this->app->loadClass('pager', $static = true);
        $pager           = new pager($recTotal, $recPerPage, $pageID);
        $wholeReportList = $this->secondmonthreport->getWholeReportList($staticType, $orderBy, $pager);
        $deptID = 0;
        if (!$wholeID) {
            $wholeID = $this->dao->select('id')->from(TABLE_WHOLE_REPORT)->where('type')->eq($staticType)->orderBy($orderBy)->fetch('id');
        }
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }

        $str    = '';

        $detailReports = [];
        if ($wholeID) {
            $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
            $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($wholeID);
            if($wholeInfo){
                $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
            }
        }

        $this->view->detailReports = $detailReports;
        $this->view->wholeReportList = $wholeReportList;
        $this->view->pager           = $pager;
        $this->view->orderBy         = $orderBy;
        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $wholeID;
        $this->view->curWholeReport  = $wholeInfo;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'examineproblemexceed';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->browse;
        $this->view->detailTitle = $this->lang->secondmonthreport->browse .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'examineform';
        $this->display();
    }
}
