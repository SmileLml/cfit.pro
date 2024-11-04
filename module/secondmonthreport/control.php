<?php

class secondmonthreport extends control
{

    /**
     * 实时报表 下钻查看
     * @param $starttime
     * @param $endtime
     * @param $deptID
     * @param $columnKey
     * @param $staticType
     * @param $isuseHisData
     * @return void
     */
    public function showrealtimedata($starttime,$endtime,$deptID,$columnKey,$staticType,$isuseHisData){
        $result = $this->secondmonthreport->showrealtimedata($starttime,$endtime,$deptID,$columnKey,$staticType,$isuseHisData);

        $this->view->historyDataList = $result['resdata'];

        $this->view->useFieldArr = explode(',',$result['field']);
        $this->view->staticType = $staticType;

        $this->view->endtime = $endtime;
//        $this->view->time = $time;
        $this->view->starttime = $starttime;
//        $this->view->dtype = $dtype;
        $this->view->deptID = $deptID;
        $this->view->columnKey = $columnKey;
        $this->view->isuseHisData = $isuseHisData;
        $this->view->linkviewtype = $this->secondmonthreport->getLinkViewType($staticType);
        /*$loadmodel = $this->lang->secondmonthreport->reportTomodules[$staticType];
        $this->loadModel($loadmodel);
        $this->view->destlang = $this->lang->$loadmodel;*/

        $this->view->destlang = $this->secondmonthreport->getColumnTopLang($staticType);
        if($result['multflag']){
            $this->display('secondmonthreport','showmultrealtimedata');
//            $this->display();
        }else{
            $this->display();
        }

    }
    public function historyDataShow($wholeID,$deptID,$columnKey){

        $resdata = $this->secondmonthreport->historyDataShow($wholeID,$deptID,$columnKey);

        $this->view->historyDataList = $resdata['historyData'];
        $this->view->wholeReport = $resdata['wholeReport'];

        //需要展示的字段,即表单快照字段
        $this->view->useFieldArr = explode(',',$this->view->wholeReport->exportFields);
        $this->view->title = '快照数据查看';
        $this->view->wholeID = $wholeID;
        $this->view->deptID = $deptID;
        $this->view->columnKey = $columnKey;

        $this->view->linkviewtype = $this->secondmonthreport->getLinkViewType($resdata['wholeReport']->type);

        //获取对应的模块
        /*$loadmodel = $this->lang->secondmonthreport->reportTomodules[$resdata['wholeReport']->type];
        $this->loadModel($loadmodel);
        $this->view->destlang = $this->lang->$loadmodel;*/
        $this->view->destlang = $this->secondmonthreport->getColumnTopLang($resdata['wholeReport']->type);

        $multflag = $this->lang->secondmonthreport->ismultdatasource[$resdata['wholeReport']->type];
        if($multflag){
            $this->display('secondmonthreport','historymultdatashow');
        }else{
            $this->display();
        }


    }
    /**
     * 实时报表下钻后的导出
     * @param $starttime
     * @param $endtime
     * @param $deptID
     * @param $columnKey
     * @param $staticType
     * @param $isuseHisData
     * @return void
     */
    public function  realtimeexport($starttime,$endtime,$deptID,$columnKey,$staticType,$isuseHisData){
        $this->loadModel('file');
//        $this->loadModel('problem');
        if ($_POST) {
            /*$loadmodel = $this->lang->secondmonthreport->reportTomodules[$staticType];
            $this->loadModel($loadmodel);
            $destlang = $this->lang->$loadmodel;*/
            $destlang = $this->secondmonthreport->getColumnTopLang($staticType);
//            $this->loadModel('problem');
            // Create field lists.

            // Get $demandBrowseInfo.

            $resdata = $this->secondmonthreport->showrealtimedata($starttime,$endtime,$deptID,$columnKey,$staticType,$isuseHisData);
            $exportdata = [];
            if($resdata['multflag']){
                /*foreach ($resdata['resdata'] as $key=>$multdata){
                    $multdata = array_values($multdata);
                    $exportdata = array_merge($exportdata,$multdata);
                }*/
                $exportdata = $resdata['resdata'];
            }else{
                $exportdata = $resdata['resdata'];
            }

            $fields = explode(',', $resdata['field']);
            foreach ($fields as $key => $fieldName) {
//                $fieldName = trim($fieldName);

                $fields[$fieldName] = $destlang->$fieldName;

                unset($fields[$key]);
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportdata);
            $this->post->set('kind', $this->lang->secondmonthreport->demandBrowse);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }


        $this->view->fileName        = $this->secondmonthreport->getExportFileName('realtime',$staticType,$columnKey,$starttime,$endtime);
//        $this->view->allExportFields = explode(',',$this->config->problem->list->exportMonthReportPartFields1);
        $this->view->allExportFields = [];
        $this->view->customExport    = false;

        $this->view->deptID = $deptID;
        $this->view->columnKey = $columnKey;

        $this->display();
    }
    /**
     * Method: browse
     * @param string $browseType
     * @param int    $param
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     * @param mixed  $wholeID
     */
    public function browse()
    {
        $staticType = 'problemOverall';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
                if('hisquarter' == $histimetype){
                    $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                    $this->view->quarterReport = $quarterReport;
                }
            }
        }else{

            //是否使用结转数据
            if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;


        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'browse';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->browse;
        $this->view->detailTitle = $this->lang->secondmonthreport->browse .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function getShowDetailListUrl($searchtype,$wholeID,$deptID,$columKey,$realstarttime,$realendtime,$staticType,$isuseHisData){
        $realstarttime = str_replace("-",'_',$realstarttime);
        $realendtime = str_replace("-",'_',$realendtime);
        if($deptID == -1){
            $deptID = '_1';
        }
        if($searchtype=='history'){
            $url = $this->createLink("secondmonthreport",'historyDataShow',"wholeID={$wholeID}&deptID={$deptID}&columKey={$columKey}",'',true);
        }else{


            $url = $this->createLink("secondmonthreport",'showrealtimedata',"realstarttime=".$realstarttime."&realendtime=".$realendtime."&deptID={$deptID}&columKey={$columKey}&staticType={$staticType}&isuseHisData={$isuseHisData}",'',true);
        }

        return $url;
    }

    public function getExportFormUrl($exportMethod,$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        $realstarttime = str_replace("-",'_',$realstarttime);
        $realendtime = str_replace("-",'_',$realendtime);
        if($deptID == -1){
            $deptID = '_1';
        }
        if($searchtype=='history'){
            $url = $this->createLink("secondmonthreport",$exportMethod,"searchtype={$searchtype}&wholeID={$wholeID}&deptID={$deptID}&realstarttime={$realstarttime}&realendtime={$realendtime}&staticType={$staticType}&isuseHisData={$isuseHisData}",'',true);
        }else{


            $url = $this->createLink("secondmonthreport",$exportMethod,"searchtype={$searchtype}&wholeID={$wholeID}&deptID={$deptID}&realstarttime={$realstarttime}&realendtime={$realendtime}&staticType={$staticType}&isuseHisData={$isuseHisData}",'',true);
        }

        return $url;
    }
    public function getrealtimebasicexportUrl($exportMethod,$phototype,$realstarttime,$realendtime,$deptID,$staticType,$isuseHisData){
        $realstarttime = str_replace("-",'_',$realstarttime);
        $realendtime = str_replace("-",'_',$realendtime);
        if($deptID == -1){
            $deptID = '_1';
        }
        if($phototype=='basic'){
            $url = $this->createLink("secondmonthreport",$exportMethod,"phototype={$phototype}&realstarttime={$realstarttime}&realendtime={$realendtime}&deptID={$deptID}&staticType={$staticType}&isuseHisData={$isuseHisData}",'',true);
        }else{


            $url = $this->createLink("secondmonthreport",$exportMethod,"phototype={$phototype}&realstarttime={$realstarttime}&realendtime={$realendtime}&deptID={$deptID}&staticType={$staticType}&isuseHisData={$isuseHisData}",'',true);
        }

        return $url;
    }

    public function getFormColumnFormat($columnvalue,$columkey,$searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData,$isshowdetaillist){
        if( $columnvalue > 0 && $isshowdetaillist){
            if($deptID == -1){
                $deptID = '_1';
            }
            return html::a($this->getShowDetailListUrl($searchtype,$wholeID,$deptID,$columkey,$realstarttime,$realendtime,$staticType,$isuseHisData),$columnvalue,'_self',"class='btn btn-link iframe' data-size='fullscreen'");

        }else {
            return $columnvalue;
        }
    }


    public function problemWaitSolve()
    {
        $staticType = 'problemWaitSolve';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;
        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{
            //是否使用结转数据
            if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;


        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'waitSolve';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->problemWaitSolve;
        $this->view->detailTitle = $this->lang->secondmonthreport->problemWaitSolve .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function problemUnresolved()
    {
        $staticType = 'problemUnresolved';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;
        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{
            //是否使用结转数据
            if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;


        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'unresolved';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->problemUnresolved;
        $this->view->detailTitle = $this->lang->secondmonthreport->problemUnresolved .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function problemExceed()
    {
        $staticType = 'problemExceed';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';

        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{


            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;
        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;
        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'exceed';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->problemExceed;
        $this->view->detailTitle = $this->lang->secondmonthreport->problemExceed .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function problemExceedBackIn()
    {
        $staticType = 'problemExceedBackIn';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;
        $str    = '';

        $detailReports = [];
        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
                if('hisquarter' == $histimetype){
                    $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                    $this->view->quarterReport = $quarterReport;
                }
            }
        }else{
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);

            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;
        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'exceedBackIn';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->problemExceedBackIn;
        $this->view->detailTitle = $this->lang->secondmonthreport->problemExceedBackIn .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function problemExceedBackOut()
    {
        $staticType = 'problemExceedBackOut';


        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $detailReports = [];
        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);

            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'exceedBackOut';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->problemExceedBackOut;
        $this->view->detailTitle = $this->lang->secondmonthreport->problemExceedBackOut.' | '.$str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function problemCompletedPlan()
    {
        $staticType = 'problemCompletedPlan';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hisquarter';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType, $histimetype);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;

        $str    = '';
        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
                if('hisquarter' == $histimetype){
                    $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                    $this->view->quarterReport = $quarterReport;
                }
            }
        }else{
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;
        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;
        $this->view->detailReports = $detailReports;
        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'problemCompletedPlan';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->problemCompletedPlan;
        $this->view->detailTitle = $this->lang->secondmonthreport->problemCompletedPlan .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    /**
     * 问题整体情况导出
     * @param mixed $wholeId
     * @param mixed $deptId
     */
    public function browseExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'       => $this->lang->secondmonthreport->deptName,
                'unaccepted'     => $this->lang->secondmonthreport->unaccepted,
                'waitAllocation' => $this->lang->secondmonthreport->waitAllocation,
                'waitSolve'      => $this->lang->secondmonthreport->waitSolve,
                'alreadySolve'   => $this->lang->secondmonthreport->alreadySolve,
                'total'          => $this->lang->secondmonthreport->total,
                'solveRate'      => $this->lang->secondmonthreport->solveRate,
            ];

//            $dept          = $this->getDeptSelect();
//            $detailReports = $this->secondmonthreport->getDetailReport($wholeId, $deptId);
            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
//                $detailReport->deptID = 0 < $detailReport->deptID ? $detailReport->deptID : -1;
                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'unaccepted'     => $detailReport->detail->unaccepted,
                        'waitAllocation' => $detailReport->detail->waitAllocation,
                        'waitSolve'      => $detailReport->detail->waitSolve,
                        'alreadySolve'   => $detailReport->detail->alreadySolve,
                        'total'          => $detailReport->detail->total,
                        'solveRate'      => $detailReport->detail->solveRate . '%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->problemproblemOverallStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'unaccepted'     => $detailReport->detail->unaccepted,
                        'waitAllocation' => $detailReport->detail->waitAllocation,
                        'waitSolve'      => $detailReport->detail->waitSolve,
                        'alreadySolve'   => $detailReport->detail->alreadySolve,
                        'total'          => $detailReport->detail->total,
                        'solveRate'      => $detailReport->detail->solveRate . '%',
                    ];
                }


            }

            $row = [
                'deptName'       => $this->lang->secondmonthreport->total,
                'unaccepted'     => array_sum(array_column($rows, 'unaccepted')),
                'waitAllocation' => array_sum(array_column($rows, 'waitAllocation')),
                'waitSolve'      => array_sum(array_column($rows, 'waitSolve')),
                'alreadySolve'   => array_sum(array_column($rows, 'alreadySolve')),
                'total'          => array_sum(array_column($rows, 'total')),
            ];
            $row['solveRate'] = ($row['total'] > 0 ? number_format(($row['alreadySolve'] / $row['total']) * 100, 2) : '0.00') . '%';
            $rows[]           = $row;
            $rows             = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    /**
     * 未解决问题统计导出
     * @param mixed $wholeId
     * @param mixed $deptId
     */
    public function problemWaitSolveExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'    => $this->lang->secondmonthreport->deptName,
                'twoMonth'    => $this->lang->secondmonthreport->twoMonth,
                'sixMonth'    => $this->lang->secondmonthreport->sixMonth,
                'twelveMonth'    => $this->lang->secondmonthreport->twelveMonth,
            ];

//            $dept          = $this->getDeptSelect();
//            $detailReports = $this->secondmonthreport->getDetailReport($wholeId, $deptId);
            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history') {
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[] = [
                        'deptName'    => zget($depts, $detailReport->deptID),
                        'twoMonth'    => $detailReport->detail->twoMonth,
                        'sixMonth'    => $detailReport->detail->sixMonth,
                        'twelveMonth' => $detailReport->detail->twelveMonth,
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,0);

                $dataList = $this->secondmonthreport->problemproblemWaitSolveStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'    => zget($depts, $detailReport->deptID),
                        'twoMonth'    => $detailReport->detail->twoMonth,
                        'sixMonth'    => $detailReport->detail->sixMonth,
                        'twelveMonth' => $detailReport->detail->twelveMonth,
                    ];
                }
            }
            $rows[] = [
                'deptName'    => $this->lang->secondmonthreport->total,
                'twoMonth'    => array_sum(array_column($rows, 'twoMonth')),
                'sixMonth'    => array_sum(array_column($rows, 'sixMonth')),
                'twelveMonth'    => array_sum(array_column($rows, 'twelveMonth')),
            ];
            $rows = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
    /**
     * 未解决问题统计导出
     * @param mixed $wholeId
     * @param mixed $deptId
     */
    public function problemUnresolvedExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'    => $this->lang->secondmonthreport->deptName,
                'letwoMonth'    => $this->lang->secondmonthreport->letwoMonth,
                'lesixMonth'    => $this->lang->secondmonthreport->lesixMonth,
                'letwelveMonth'    => $this->lang->secondmonthreport->letwelveMonth,
                'gttwelveMonth'    => $this->lang->secondmonthreport->gttwelveMonth,
            ];

//            $dept          = $this->getDeptSelect();
//            $detailReports = $this->secondmonthreport->getDetailReport($wholeId, $deptId);
            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history') {
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
//                $detailReport->deptID = 0 < $detailReport->deptID ? $detailReport->deptID : -1;
                    $rows[] = [
                        'deptName'    => zget($depts, $detailReport->deptID),
                        'letwoMonth'    => $detailReport->detail->letwoMonth,
                        'lesixMonth'    => $detailReport->detail->lesixMonth,
                        'letwelveMonth' => $detailReport->detail->letwelveMonth,
                        'gttwelveMonth' => $detailReport->detail->gttwelveMonth,
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,0);

                $dataList = $this->secondmonthreport->problemproblemUnresolvedStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'    => zget($depts, $detailReport->deptID),
                        'letwoMonth'    => $detailReport->detail->letwoMonth,
                        'lesixMonth'    => $detailReport->detail->lesixMonth,
                        'letwelveMonth' => $detailReport->detail->letwelveMonth,
                        'gttwelveMonth' => $detailReport->detail->gttwelveMonth,
                    ];
                }
            }
            $rows[] = [
                'deptName'    => $this->lang->secondmonthreport->total,
                'letwoMonth'    => array_sum(array_column($rows, 'letwoMonth')),
                'lesixMonth'    => array_sum(array_column($rows, 'lesixMonth')),
                'letwelveMonth'    => array_sum(array_column($rows, 'letwelveMonth')),
                'gttwelveMonth'    => array_sum(array_column($rows, 'gttwelveMonth')),
            ];
            $rows = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('rowmerge', ['startletter'=>'B','startnum'=>'1','endletter'=>'E','endnum'=>'1']);
            $this->post->set('colmerge', ['startletter'=>'A','startnum'=>'1','endletter'=>'A','endnum'=>'2']);
            $this->post->set('rowmergetitle', $this->lang->secondmonthreport->problemUnresolvedNotice);
            $this->post->set('sheettitle', $this->lang->secondmonthreport->problemUnresolved);
            $this->post->set('stardatarow', 3);
            $this->post->set('filename', $this->lang->secondmonthreport->problemUnresolved);

            $this->fetch('file', 'exportphpexcel' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    public function problemExceedExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'     => $this->lang->secondmonthreport->deptName,
                'alreadySolve' => $this->lang->secondmonthreport->exceed->alreadySolve,
                'waitSolve'    => $this->lang->secondmonthreport->exceed->waitSolve,
                'sum'    => $this->lang->secondmonthreport->exceed->sum,
                'total'        => $this->lang->secondmonthreport->exceed->total,
                'exceedRate'   => $this->lang->secondmonthreport->exceed->exceedRate,
            ];

//            $dept          = $this->getDeptSelect();
//            $detailReports = -1 == $deptId ? [] : $this->secondmonthreport->getDetailReport($wholeId, $deptId);
            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history') {
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'alreadySolve' => $detailReport->detail->alreadySolve,
                        'waitSolve'    => $detailReport->detail->waitSolve,
                        'sum'    => $detailReport->detail->sum,
                        'total'        => $detailReport->detail->total,
                        'exceedRate'   => $detailReport->detail->exceedRate . '%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->problemproblemExceedStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'alreadySolve' => $detailReport->detail->alreadySolve,
                        'waitSolve'    => $detailReport->detail->waitSolve,
                        'sum'    => $detailReport->detail->sum,
                        'total'        => $detailReport->detail->total,
                        'exceedRate'   => $detailReport->detail->exceedRate . '%',
                    ];
                }
            }

            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'alreadySolve' => array_sum(array_column($rows, 'alreadySolve')),
                'waitSolve'    => array_sum(array_column($rows, 'waitSolve')),
                'sum'    => array_sum(array_column($rows, 'sum')),
                'total'        => array_sum(array_column($rows, 'total')),
            ];
            $row['exceedRate'] = ($row['total'] > 0 ? number_format($row['sum'] / $row['total'] * 100, 2) : '0.00') . '%';
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->lang->secondmonthreport->problemExceed . $info->startday.' ~ '.$info->endday;
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    public function problemExceedBackInExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'          => $this->lang->secondmonthreport->deptName,
                'foverdueNum'         => $this->lang->secondmonthreport->foverdueNum,
                'backTotal'         => $this->lang->secondmonthreport->backTotal,
                'backExceedRate'    => $this->lang->secondmonthreport->backExceedRate,

            ];

//            $dept          = $this->getDeptSelect();
//            $detailReports = -1 == $deptId ? [] : $this->secondmonthreport->getDetailReport($wholeId, $deptId);
            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history') {
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[] = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',

                    ];

                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->problemproblemExceedBackInStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',
                    ];
                }
            }


            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'foverdueNum' => array_sum(array_column($rows, 'foverdueNum')),
                'backTotal'    => array_sum(array_column($rows, 'backTotal')),

            ];
            $row['backExceedRate'] = $row['backTotal'] > 0 ? sprintf("%0.2f",($row['foverdueNum']/$row['backTotal'])*100).'%' : '0.00%';

            $rows[]            = $row;

            $rows = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    public function problemExceedBackOutExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'          => $this->lang->secondmonthreport->deptName,
                'foverdueNum'         => $this->lang->secondmonthreport->foverdueNum,
                'backTotal'         => $this->lang->secondmonthreport->backTotal,
                'backExceedRate'    => $this->lang->secondmonthreport->backExceedRate,
            ];

//            $dept          = $this->getDeptSelect();
//            $detailReports = -1 == $deptId ? [] : $this->secondmonthreport->getDetailReport($wholeId, $deptId);
            $depts          = $this->loadModel('dept')->getDeptByOrder();


            $rows = [];
            if($searchtype == 'history') {
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[] = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',

                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->problemproblemExceedBackOutStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',
                    ];
                }
            }


            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'foverdueNum' => array_sum(array_column($rows, 'foverdueNum')),
                'backTotal'    => array_sum(array_column($rows, 'backTotal')),

            ];
            $row['backExceedRate'] = $row['backTotal'] > 0 ? sprintf("%0.2f",($row['foverdueNum']/$row['backTotal'])*100).'%' : '0.00%';

            $rows[]            = $row;

            $rows = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
    /**
     * 问题整体情况导出
     * @param mixed $wholeId
     * @param mixed $deptId
     */
    public function problemCompletedPlanExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName' => $this->lang->secondmonthreport->deptName,
                'noPlan'   => '未按计划解决',
                'plan'     => '按计划解决',
                'total'    => $this->lang->secondmonthreport->total,
                'planRate' => '按计划解决率',
            ];
            $depts = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[] = [
                        'deptName' => zget($depts, $detailReport->deptID),
                        'noPlan'   => $detailReport->detail->noPlan,
                        'plan'     => $detailReport->detail->plan,
                        'total'    => $detailReport->detail->total,
                        'planRate' => $detailReport->detail->planRate . '%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('problem');
                $dataList = $this->secondmonthreport->getProblemDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->problemproblemOverallStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName' => zget($depts, $detailReport->deptID),
                        'noPlan'   => $detailReport->detail->noPlan,
                        'plan'     => $detailReport->detail->plan,
                        'total'    => $detailReport->detail->total,
                        'planRate' => $detailReport->detail->planRate . '%',
                    ];
                }


            }

            $row = [
                'deptName' => $this->lang->secondmonthreport->total,
                'noPlan'   => array_sum(array_column($rows, 'noPlan')),
                'plan'     => array_sum(array_column($rows, 'plan')),
                'total'    => array_sum(array_column($rows, 'total')),
            ];
            $row['planRate'] = ($row['total'] > 0 ? number_format(($row['plan'] / $row['total']) * 100, 2) : '0.00') . '%';
            $rows[]          = $row;
            $rows            = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'problem');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }

    public function overallDemandSituation()
    {
        $this->view->selected = 'overallDemandSituation';
        $this->display();
    }
    public function testdatafram(){
        $time = $this->secondmonthreport->getCustomTimeFrame('requirement_inside');
        a($time);
    }

    public function ajaxgetdatalist(){
        $result = $this->secondmonthreport->ajaxgetdatalist();

        echo $result;
    }
    public function ajaxgetdaterange(){
        $result = $this->secondmonthreport->ajaxgetdaterange();
        $this->send($result);

    }

    //问题池 历史数据生成方法
    public function problemStatistics()
    {

        $time = time();
        $this->loadModel('problem');
        $dtype = 1;
        // ====  问题整体情况统计表 start  历史结转数据参与统计 ==
        $formType = 'problemOverall';
        //获取开始结束时间，传入
        $problemOverallDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $problemOverallIDS = $this->secondmonthreport->problemHistoryOverall($problemOverallDate['startdate'], $problemOverallDate['enddate'],$time,$problemOverallDate,$formType);
        //问题整体统计表 基础快照
        $this->problemphoto($problemOverallDate,$time,$formType,'all',1);

        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        //问题整体统计表 表单快照
        $this->problempartphoto($problemOverallDate,$time,$problemOverallIDS,$fieldArrs['form'],$formType);
        // ====  问题整体情况统计表 end ==
        // ====  两个月未解决问题统计表 start  此统计表 废弃==
        /*$formType = 'problemWaitSolve';
        $problemWaitSolveDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $problemWaitSolveIDS = $this->secondmonthreport->problemHistoryWaitSolve($problemWaitSolveDate['startdate'], $problemWaitSolveDate['enddate'],$time,$problemWaitSolveDate,$formType);

        //两个月未解决问题统计表 基础快照
        $this->problemphoto($problemWaitSolveDate,$time,$formType,'all',1);
        //两个月未解决问题统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->problempartphoto($problemWaitSolveDate,$time,$problemWaitSolveIDS,$fieldArrs['form'],$formType);*/
        // ====  两个月未解决问题统计表 end ==

        // ====  未解决问题统计表 start 历史结转数据参与统计==
        $formType = 'problemUnresolved';
        $problemUnresolvedDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $problemUnresolvedIDS = $this->secondmonthreport->problemHistoryUnresolved($problemUnresolvedDate['startdate'], $problemUnresolvedDate['enddate'],$time,$problemUnresolvedDate,$formType);

        //未解决问题统计表 基础快照
        $this->problemphoto($problemUnresolvedDate,$time,$formType,'all',1);
        //未解决问题统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->problempartphoto($problemUnresolvedDate,$time,$problemUnresolvedIDS,$fieldArrs['form'],$formType);
        // ====  未解决问题统计表 end ==


        // ====  问题解决超期统计表 start ==
        $formType = 'problemExceed';
        $problemExceedDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);
        $problemExceedIDS = $this->secondmonthreport->problemHistoryExceed($problemExceedDate['startdate'], $problemExceedDate['enddate'],$time,$problemExceedDate,$formType);

        //问题解决超期统计表 基础快照
        $this->problemphoto($problemExceedDate,$time,$formType,'all',0);
        //问题解决超期统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->problempartphoto($problemExceedDate,$time,$problemExceedIDS,$fieldArrs['form'],$formType);
        // ====  问题解决超期统计表 end ==

        // ====  内部反馈超期统计表 start ==
        $formType = 'problemExceedBackIn';
        $problemExceedBackInDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $problemExceedBackInIDS = $this->secondmonthreport->problemHistoryExceedBackIn($problemExceedBackInDate['startdate'], $problemExceedBackInDate['enddate'],$time,$problemExceedBackInDate,$formType);

        //内部反馈超期统计表 基础快照
        $this->problemphoto($problemExceedBackInDate,$time,$formType,'all',0);
        //内部反馈超期统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->problempartphoto($problemExceedBackInDate,$time,$problemExceedBackInIDS,$fieldArrs['form'],$formType);
        // ====  内部反馈超期统计表 end ==
        // ====  外部反馈超期统计表 start ==
        $formType = 'problemExceedBackOut';
        $problemExceedBackOutDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $problemExceedBackOutIDS = $this->secondmonthreport->problemHistoryExceedBackOut($problemExceedBackOutDate['startdate'], $problemExceedBackOutDate['enddate'],$time,$problemExceedBackOutDate,$formType);

        //外部反馈超期统计表 基础快照
        $this->problemphoto($problemExceedBackOutDate,$time,$formType,'all',0);
        //外部反馈超期统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->problempartphoto($problemExceedBackOutDate,$time,$problemExceedBackOutIDS,$fieldArrs['form'],$formType);
        // ====  外部反馈超期统计表 end ==
        echo '生成成功';
    }
    // 生成 未解决问题统计表/未实现需求统计表 使用一次。
    public function unrealizedStatistics(){
        $time = time();
        $this->loadModel('problem');
        $this->loadModel('demand');
        $dtype = 1;
        $formType = 'problemUnresolved';
        $problemUnresolvedDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $problemUnresolvedIDS = $this->secondmonthreport->problemHistoryUnresolved($problemUnresolvedDate['startdate'], $problemUnresolvedDate['enddate'],$time,$problemUnresolvedDate,$formType);

        //未解决问题统计表 基础快照
        $this->problemphoto($problemUnresolvedDate,$time,$formType,'all',0);
        //未解决问题统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->problempartphoto($problemUnresolvedDate,$time,$problemUnresolvedIDS,$fieldArrs['form'],$formType);
        // ====  未解决问题统计表 end ==

        // ====  未实现需求统计表 start 历史结转数据参与统计 ==
        $formType = 'demandunrealized';
        //获取开始结束时间，传入
        $demandunrealizedDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $demandunrealizedIDS = $this->secondmonthreport->demandHistoryUnrealized($demandunrealizedDate['startdate'], $demandunrealizedDate['enddate'],$time,$demandunrealizedDate,$formType);

        //未实现需求统计表 基础快照
        $this->demandphoto($demandunrealizedDate,$time,$formType,'all',1);

        //未实现需求统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->demandpartphoto($demandunrealizedDate,$time,$demandunrealizedIDS,$fieldArrs['form'],$formType);
        // ====  未实现需求统计表 end ==
        echo 'finish';

    }
    /**
     需求池历史数据生成方法
     */
    public function demandStatistics()
    {

        $time = time();
        $this->loadModel('demand');
        $dtype = 1;
        // ====  需求整体情况统计表 start 历史结转数据参与统计==
        $formType = 'demand_whole';
        //获取开始结束时间，传入
        $demand_wholeDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $demand_wholeIDS = $this->secondmonthreport->demandHistoryWhole($demand_wholeDate['startdate'], $demand_wholeDate['enddate'],$time,$demand_wholeDate,$formType);

        //需求整体情况统计表 基础快照
        $this->demandphoto($demand_wholeDate,$time,$formType,'all',1);

        //需求整体情况统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->demandpartphoto($demand_wholeDate,$time,$demand_wholeIDS,$fieldArrs['form'],$formType);
        // ====  需求整体情况统计表 end ==

        // ====  未实现需求统计表 start 历史结转数据参与统计 ==
        $formType = 'demandunrealized';
        //获取开始结束时间，传入
        $demandunrealizedDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $demandunrealizedIDS = $this->secondmonthreport->demandHistoryUnrealized($demandunrealizedDate['startdate'], $demandunrealizedDate['enddate'],$time,$demandunrealizedDate,$formType);

        //未实现需求统计表 基础快照
        $this->demandphoto($demandunrealizedDate,$time,$formType,'all',1);

        //未实现需求统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->demandpartphoto($demandunrealizedDate,$time,$demandunrealizedIDS,$fieldArrs['form'],$formType);
        // ====  未实现需求统计表 end ==

        // ====  需求条目实现超期统计表 start ==
        $formType = 'demand_realized';
        //获取开始结束时间，传入
        $demandrealizedDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);


        $demandrealizedIDS = $this->secondmonthreport->demandHistoryrealized($demandrealizedDate['startdate'], $demandrealizedDate['enddate'],$time,$demandrealizedDate,$formType);

        //需求条目实现超期统计表 基础快照
        $this->demandphoto($demandrealizedDate,$time,$formType,'all',0);

        //需求条目实现超期统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->demandpartphoto($demandrealizedDate,$time,$demandrealizedIDS,$fieldArrs['form'],$formType);
        // ====  未实现需求统计表 end ==
        $this->loadModel('requirement');
        // ====  需求任务内部反馈超期统计表 start ==
        $formType = 'requirement_inside';
        //获取开始结束时间，传入
        $requirement_insideDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $requirement_insideIDS = $this->secondmonthreport->requirementHistoryinside($requirement_insideDate['startdate'], $requirement_insideDate['enddate'],$time,$requirement_insideDate,$formType);

        //需求任务内部反馈超期统计表 基础快照
        $this->requirementphoto($requirement_insideDate,$time,$formType,'all',0);

        //需求任务内部反馈超期统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->requirementpartphoto($requirement_insideDate,$time,$requirement_insideIDS,$fieldArrs['form'],$formType);
        // ====  需求任务内部反馈超期统计表 end ==

        // ====  需求任务外部反馈超期统计表 start ==
        $formType = 'requirement_outside';
        //获取开始结束时间，传入
        $requirement_outsideDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $requirement_outsideIDS = $this->secondmonthreport->requirementHistoryoutside($requirement_outsideDate['startdate'], $requirement_outsideDate['enddate'],$time,$requirement_outsideDate,$formType);

        //需求任务外部反馈超期统计表 基础快照
        $this->requirementphoto($requirement_outsideDate,$time,$formType,'all',0);

        //需求任务外部反馈超期统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->requirementpartphoto($requirement_outsideDate,$time,$requirement_outsideIDS,$fieldArrs['form'],$formType);
        // ====  需求任务外部反馈超期统计表 end ==
        echo '生成成功';

    }
    public function secondOrderStatistics(){
        $time = time();
        $this->loadModel('secondorder');
        $dtype = 1;
        // ====  工单类型统计表 start 历史结转数据参与统计==
        $formType = 'secondorderclass';
        //获取开始结束时间，传入
        $secondorderclassDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);
        $secondorderclassIDS = $this->secondmonthreport->secondorderHistoryclass($secondorderclassDate['startdate'], $secondorderclassDate['enddate'],$time,$secondorderclassDate,$formType);

        //工单类型统计表 基础快照
        $this->secondorderclassphoto($secondorderclassDate,$time,$formType,'all',1);
        //工单类型统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->secondorderclasspartphoto($secondorderclassDate,$time,$secondorderclassIDS,$fieldArrs['form'],$formType);
        // ====  工单类型统计表 end ==

        // ====  工单受理统计表 start ==
        $formType = 'secondorderaccept';
        //获取开始结束时间，传入
        $secondorderacceptDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $secondorderacceptIDS = $this->secondmonthreport->secondorderHistoryaccept($secondorderacceptDate['startdate'], $secondorderacceptDate['enddate'],$time,$secondorderacceptDate,$formType);

        //工单受理统计表 基础快照
        $this->secondorderclassphoto($secondorderacceptDate,$time,$formType,'all',0);

        //工单受理统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->secondorderclasspartphoto($secondorderacceptDate,$time,$secondorderacceptIDS,$fieldArrs['form'],$formType);
        // ====  工单受理统计表 end ==
        echo '生成成功';

    }
    public function supportStatistics(){
        $time = time();
        $this->loadModel('support');
        $dtype = 1;
        // ====  现场支持统计表 start ==
        $formType = 'support';
        //获取开始结束时间，传入
        $supportDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $supportIDS = $this->secondmonthreport->supportHistorysupport($supportDate['startdate'], $supportDate['enddate'],$time,$supportDate,$formType);

        //现场支持统计表 基础快照
        $this->supportphoto($supportDate,$time,$formType,'all',0);
        //现场支持统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->supportpartphoto($supportDate,$time,$supportIDS,$fieldArrs['form'],$formType);
        // ====  现场支持统计表 end ==
        echo '生成成功';


    }
    public function workloadStatistics(){
        $time = time();

        $dtype = 1;
        // ====  基础MA工作量统计表 start ==
        $formType = 'workload';
        //基础MA工作量统计表，传入
        $workloadDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $workloadIDS = $this->secondmonthreport->workloadHistoryworkload($workloadDate['startdate'], $workloadDate['enddate'],$time,$workloadDate,$formType);

        //基础MA工作量统计表 基础快照
        $this->workloadphoto($workloadDate,$time,$formType,'all',0);
        //基础MA工作量统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->workloadpartphoto($workloadDate,$time,$workloadIDS,$fieldArrs['form'],$formType);
        // ====  基础MA工作量统计表 end ==
        echo '生成成功';

    }

    public function modifyStatistics(){
        $time = time();

        $dtype = 1;
        // ====  变更整体统计表 start ==
        $formType = 'modifywhole';
        //变更整体统计表，传入
        $modifywholeDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);

        $modifywholeIDS = $this->secondmonthreport->modifyHistorywhole($modifywholeDate['startdate'], $modifywholeDate['enddate'],$time,$modifywholeDate,$formType);

        //变更整体统计表 基础快照
        $this->modifywholephoto($modifywholeDate,$time,$formType,'all',0);
        //变更整体统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->modifywholepartphoto($modifywholeDate,$time,$modifywholeIDS,$fieldArrs['form'],$formType);
        // ====  变更整体统计表 end ==

        // ====  变更异常统计表 start ==
        $formType = 'modifyabnormal';
        //变更异常统计表，传入
        $modifyabnormalDate = $this->secondmonthreport->getSnapshotDateRange($formType,1,$dtype);


        $modifyabnormalIDS = $this->secondmonthreport->modifyHistorynormal($modifyabnormalDate['startdate'], $modifyabnormalDate['enddate'],$time,$modifyabnormalDate,$formType);

        //变更异常统计表 基础快照
        $this->modifywholephoto($modifyabnormalDate,$time,$formType,'all',0);
        //变更异常统计表 表单快照
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $this->modifywholepartphoto($modifyabnormalDate,$time,$modifyabnormalIDS,$fieldArrs['form'],$formType);
        // ====  变更异常统计表 end ==
        echo '生成成功';

    }


    public function testExcel($wholdID){
//        $this->config->problem->list->exportMonthReportPartFields1;
        $this->secondmonthreport->excelToRedis($wholdID);
    }




    /**导出下钻查看的数据明细
     * @param $wholeID
     * @param $deptID
     * @param $columnKey
     * @return void
     */
    public function exportDataList($wholeID,$deptID,$columnKey){
        $this->loadModel('file');
        $wholeInfo = $this->secondmonthreport->getWholeReportByID($wholeID);
        if ($_POST) {
            $secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;
//            $this->loadModel('problem');
            // Create field lists.
            $fields = explode(',', $wholeInfo->exportFields);
            /*$loadmodel = $this->lang->secondmonthreport->reportTomodules[$wholeInfo->type];
            $this->loadModel($loadmodel);
            $destlang = $this->lang->$loadmodel;*/
            $destlang = $this->secondmonthreport->getColumnTopLang($wholeInfo->type);
            foreach ($fields as $key => $fieldName) {
//                $fieldName = trim($fieldName);

                $fields[$fieldName] = $destlang->$fieldName;

                unset($fields[$key]);
            }
            // Get $demandBrowseInfo.

            $resdata = $this->secondmonthreport->historyDataShow($wholeID,$deptID,$columnKey);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $resdata['historyData']);
            $this->post->set('kind', $this->lang->secondmonthreport->demandBrowse);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }


        $this->view->fileName        = $this->secondmonthreport->getExportFileName('history',$wholeInfo->type,$columnKey,$wholeInfo->startday,$wholeInfo->endday);
        $this->view->allExportFields = $wholeInfo->exportFields;
        $this->view->customExport    = false;
        $this->view->wholeID = $wholeID;
        $this->view->deptID = $deptID;
        $this->view->columnKey = $columnKey;

        $this->display();
    }

    /**
     * 问题池快照导出
     * @param $paramyear
     * @param $parammonth
     * @param $curtime
     */

    public function problempartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType='')
    {
        $this->loadModel('problem');
        $this->loadModel('file');
        $problemLang   = $this->lang->problem;

        // Create field lists.
        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $problemLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get problems.

        $problems    = [];
        $field       = 't2.originalResolutionDate,t2.delayResolutionDate,t2.delayReason,t2.delayStatus,t2.delayVersion,t2.delayStage,t2.delayDealUser,t2.delayUser,t2.delayDate';
        //可能有历史结转数据，不再限制时间范围
        if($ids){
            $problems    = $this->dao
                ->select("t1.*,{$field}")
                ->from(TABLE_PROBLEM)->alias('t1')
                ->leftJoin(TABLE_DELAY)->alias('t2')
                ->on("t1.id = t2.objectId and t2.objectType = 'problem'")
                ->where('t1.status')->ne('deleted')
//            ->andWhere('t1.createdDate')->ge($dataBetween['startdate'])
                ->andWhere('t1.id')->in($ids)
//            ->andWhere('t1.createdDate')->le($dataBetween['enddate'])
                ->fetchAll('id');
        }else{
            $problems = [];
        }


        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();
        $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');

        $this->loadModel('secondline');
        foreach ($problems as $problem) {
            //是否超期
//            $problem                                     = $this->loadModel('problem')->getIsExceed($problem);
            $problem                                     = $this->problem->getIfOverDate($problem);
            $problem->monthreportfeedbackStartTimeInside = $problem->ifOverDateInside['start'] ?? '';
            $problem->monthreportfeedbackEndTimeInside   = $problem->deptPassTime              ?? '';
            $problem->monthreportdealAssigned            = $problem->dealAssigned              ?? '';
            $problem->insideFeedbackDate                 = $problem->ifOverDateInside['end']   ?? '';
            $problem->feedbackStartTimeOutside           = $problem->ifOverDate['start']       ?? '';
            $problem->monthreportfeedbackEndTimeOutside  = $problem->innovationPassTime        ?? '';
            $problem->outsideFeedbackDate                = $problem->ifOverDate['end']         ?? '';
            $problem->ifOverTime                         = $problem->ifOverDate['flag']        ?? '';
            $problem->ifOverTimeInside                   = $problem->ifOverDateInside['flag']  ?? '';
            $problem->source     = $problemLang->sourceList[$problem->source];
            $problem->type       = $problemLang->typeList[$problem->type];

            $problem->acceptUser = zget($users, $problem->acceptUser, '');
            $problem->acceptDept = zget($depts, $problem->acceptDept, '');

            $problem->createdDept = $dmap[$problem->createdBy]->dept ? $depts[$dmap[$problem->createdBy]->dept] : '';

            $problem->monthreportcreatedBy = $users[$problem->createdBy];
            $problem->fixType              = zget($problemLang->fixTypeList, $problem->fixType, '');

            if (in_array($problem->status, ['feedbacked', 'build', 'released', 'delivery', 'onlinesuccess', 'closed'])) {
                $problem->dealUser = '';
            } else {
                $problem->dealUser = zmget($users, $problem->dealUser);
            }
            $problem->editedBy   = zget($users, $problem->editedBy, '');
            $problem->closedBy   = zget($users, $problem->closedBy, '');
            $problem->onlineTime = $problem->solvedTime;
            //如果关联二线取二线时间，如果有待关闭状态取待关闭时间，没有待关闭取已关闭时间，否则为空
            $problem            = $this->problem->getSolvedTime($problem);
            $problem->solveDate = '';
            if ('delivery' == $problem->status || 'onlinesuccess' == $problem->status || 'onlinefailed' == $problem->status) {
                $dealDateObj = $this->loadModel('consumed')->getDealDate('problem', $problem->id);
                if ($dealDateObj) {
                    $problem->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                }
            } elseif ('closed' == $problem->status) {
                $dealDateObj = $this->loadModel('problem')->getDate($problem->id);
                if ($dealDateObj) {
                    $problem->solveDate = $dealDateObj->lastDealDate;
                }
            }

            $problem->status = $problemLang->statusList[$problem->status];
            // 处理所属应用系统。
            if ($problem->app) {
                $as = [];
                foreach (explode(',', $problem->app) as $app) {
                    if (!$app) {
                        continue;
                    }
                    $as[] = zget($apps, $app);
                }
                $problem->app = implode(',', $as);
            }



            // 获取制版次数。不需要查询获取了，有字段累计增加。
            //$problem->buildTimes = $this->problem->getBuild($problem->id);

            // 获取关联的生产变更，数据修正，数据获取。
            $problem->relationModify = '';
            $problem->relationFix    = '';
            $problem->relationGain   = '';

            //反馈单状态
            $problem->ReviewStatus = zget($this->lang->problem->feedbackStatusList, $problem->ReviewStatus);

            //是否退回
            $problem->ifReturn = zget($this->lang->problem->ifReturnList, $problem->ifReturn);
            $problem->isChange = zget($this->lang->problem->isChangeList, $problem->isChange);
            //是否最终方案
            $problem->IfultimateSolution     = zget($this->lang->problem->ifultimateSolutionList, $problem->IfultimateSolution);
            $problem->isExtended             = zget($this->lang->problem->isExtendedList, $problem->isExtended);
            $problem->isBackExtended         = zget($this->lang->problem->isBackExtendedList, $problem->isBackExtended);
            $problem->ChangeIdRelated        = strip_tags($problem->ChangeIdRelated);
            $problem->EffectOfService        = strip_tags($problem->EffectOfService);
            $problem->IncidentIdRelated      = strip_tags($problem->IncidentIdRelated);
            $problem->DrillCausedBy          = strip_tags($problem->DrillCausedBy);
            $problem->Optimization           = strip_tags($problem->Optimization);
            $problem->Tier1Feedback          = strip_tags($problem->Tier1Feedback);
            $problem->solution               = strip_tags($problem->solution);
            $problem->ChangeSolvingTheIssue  = strip_tags($problem->ChangeSolvingTheIssue);
            $problem->ReasonOfIssueRejecting = strip_tags($problem->ReasonOfIssueRejecting);
            $problem->EditorImpactscope      = strip_tags($problem->EditorImpactscope);
            $problem->revisionRecord         = strip_tags($problem->revisionRecord);
            $problem->ProblemSource          = $problem->ProblemSource;
            $problem->sourece                = zget($this->lang->problem->sourceList, $problem->source, $problem->source);
            $problem->SolutionFeedback       = zget($this->lang->problem->solutionFeedbackList,$problem->SolutionFeedback,$problem->SolutionFeedback);
            //新增加字段-反馈单
            $problem->TimeOfOccurrence  = $problem->occurDate;
            $problem->problemFeedbackId = $problem->IssueId;
            $problem->ultimateSolution  = $problem->solution;
            $problem->completedPlan   = zget($this->lang->problem->completedPlanList,$problem->completedPlan,$problem->completedPlan);
            $problem->examinationResult  = zget($this->lang->problem->examinationResultList,$problem->examinationResult,$problem->examinationResult);
            //计划解决变更时间与延期解决时间取最大时间
            if($problem->PlannedTimeOfChange < $problem->delayResolutionDate){
                $problem->PlannedTimeOfChange = $problem->delayResolutionDate;
            }

        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $problems);
        $this->post->set('kind', 'problem');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);
        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名 需求条目
        $formType = strtolower($formType);
        $_POST['generatefilename'] = 'problem_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    /**
     * @Notes:需求整体情况统计表
     * @Date: 2023/10/18
     * @Time: 10:19
     * @Interface demandBrowse
     * @param int    $wholeID
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     */
    public function demandBrowse()
    {
        $staticType = 'demand_whole';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{

            //是否使用结转数据
            if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;

        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'demandBrowse';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->demandBrowse;
        $this->view->detailTitle = $this->lang->secondmonthreport->demandBrowse .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    /**
     * @Notes:未实现需求统计表
     * @Date: 2023/10/18
     * @Time: 9:35
     * @Interface demandunrealized
     * @param int    $wholeID
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     */
    public function demandunrealized()
    {
        $staticType = 'demandunrealized';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{

            //是否使用结转数据
            if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;

        //构造合计数据
        /*$accountArr = $this->buildTotalInfo($detailReports);
        if (!empty($detailReports)) {
            $detailReports = array_merge($detailReports, $accountArr);
        }*/

        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'demandUnrealized';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->demandunrealized;
        $this->view->detailTitle = $this->lang->secondmonthreport->demandunrealized .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    /**
     * @Notes:需求条目实现超期统计表
     * @Date: 2023/10/18
     * @Time: 10:51
     * @Interface demandExceed
     * @param int    $wholeID
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     */
    public function demandExceed()
    {
        $staticType = 'demand_realized';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{

            //是否使用结转数据

            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;

        //构造合计数据
        /*$accountArr = $this->buildExeceed($detailReports);
        if (!empty($detailReports)) {
            $detailReports = array_merge($detailReports, $accountArr);
        }*/


        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'demandExceed';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->title       = $this->lang->secondmonthreport->demandExceed;
        $this->view->detailTitle = $this->lang->secondmonthreport->demandExceed .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    /**
     * @Notes:需求任务内部反馈超期统计表
     * @Date: 2023/10/18
     * @Time: 11:08
     * @Interface demandExceedBackIn
     * @param int    $wholeID
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     */
    public function demandExceedBackIn()
    {
        $staticType = 'requirement_inside';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
                if('hisquarter' == $histimetype){
                    $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                    $this->view->quarterReport = $quarterReport;
                }
            }
        }else{

            //是否使用结转数据

            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;


        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'demandExceedBackIn';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->apps        = $this->loadModel('application')->getPairs();
        $this->view->title       = $this->lang->secondmonthreport->demandExceedBackIn;
        $this->view->detailTitle = $this->lang->secondmonthreport->demandExceedBackIn .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    /**
     * @Notes:需求任务外部反馈超期统计表
     * @Date: 2023/10/18
     * @Time: 11:09
     * @Interface demandExceedBackOut
     * @param int    $wholeID
     * @param string $orderBy
     * @param int    $recTotal
     * @param int    $recPerPage
     * @param int    $pageID
     */
    public function demandExceedBackOut()
    {
        $staticType = 'requirement_outside';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{

            //是否使用结转数据

            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;

        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'demandExceedBackOut';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);
        $this->view->apps        = $this->loadModel('application')->getPairs();
        $this->view->title       = $this->lang->secondmonthreport->demandExceedBackOut;
        $this->view->detailTitle = $this->lang->secondmonthreport->demandExceedBackOut .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    /**
     * @Notes:需求整体情况统计表导出
     * @Date: 2023/10/19
     * @Time: 17:13
     * @Interface demandBrowseExport
     * @param int $wholeID
     * @param int $deptID
     */
    public function demandBrowseExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;
            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $secondmonthreportConfig->export->demandBrowseFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);

                $fields[$fieldName] = $secondmonthreportLang->{$fieldName} ?? $fieldName;

                unset($fields[$key]);
            }
            // Get $demandBrowseInfo.
            $depts          = $this->loadModel('dept')->getDeptByOrder();


            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'implementedNum' => $detailReport->detail->implementedNum,
                        'unrealizedNum'      => $detailReport->detail->unrealizedNum,
                        'total'   => $detailReport->detail->total,
                        'realizationRate'      => $detailReport->detail->realizationRate . '%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('demand');
                $dataList = $this->secondmonthreport->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->demandwholeDemandMonthStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);


                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'implementedNum' => $detailReport->detail->implementedNum,
                        'unrealizedNum'      => $detailReport->detail->unrealizedNum,
                        'total'          => $detailReport->detail->total,
                        'realizationRate'      => $detailReport->detail->realizationRate . '%',
                    ];
                }
            }

            $row = [
                'deptName'       => $this->lang->secondmonthreport->total,
                'implementedNum' => array_sum(array_column($rows, 'implementedNum')),
                'unrealizedNum'      => array_sum(array_column($rows, 'unrealizedNum')),
                'total'          => array_sum(array_column($rows, 'total')),
            ];
            $row['realizationRate'] = ($row['total'] > 0 ? number_format(($row['implementedNum'] / $row['total']) * 100, 2) : '0.00') . '%';
            $rows[]           = $row;
            $rows             = json_decode(json_encode($rows));



            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', $this->lang->secondmonthreport->demandBrowse);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->export->demandBrowseFields;
        $this->view->customExport    = false;

        $this->display();
    }

    /**
     * @Notes:未实现需求统计表导出
     * @Date: 2023/10/20
     * @Time: 9:59
     * @Interface demandUnrealizedExport
     * @param int $wholeID
     * @param int $deptID
     */
    public function demandunrealizedExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;
            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $secondmonthreportConfig->export->demandUnrealizedInfoFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);

                $fields[$fieldName] = $secondmonthreportLang->{$fieldName} ?? $fieldName;

                unset($fields[$key]);
            }
            // Get $demandBrowseInfo.
            $depts          = $this->loadModel('dept')->getDeptByOrder();


            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'demandletwoMonth' => $detailReport->detail->demandletwoMonth,
                        'demandlesixMonth'      => $detailReport->detail->demandlesixMonth,
                        'demandletwelveMonth'   => $detailReport->detail->demandletwelveMonth,
                        'demandgttwelveMonth'      => $detailReport->detail->demandgttwelveMonth,
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('demand');
                $dataList = $this->secondmonthreport->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->demandunrealizedStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'demandletwoMonth' => $detailReport->detail->demandletwoMonth,
                        'demandlesixMonth'      => $detailReport->detail->demandlesixMonth,
                        'demandletwelveMonth'   => $detailReport->detail->demandletwelveMonth,
                        'demandgttwelveMonth'      => $detailReport->detail->demandgttwelveMonth,
                    ];
                }
            }

            $row = [
                'deptName'       => $this->lang->secondmonthreport->total,
                'demandletwoMonth' => array_sum(array_column($rows, 'demandletwoMonth')),
                'demandlesixMonth'      => array_sum(array_column($rows, 'demandlesixMonth')),
                'demandletwelveMonth'          => array_sum(array_column($rows, 'demandletwelveMonth')),
                'demandgttwelveMonth'          => array_sum(array_column($rows, 'demandgttwelveMonth')),
            ];

            $rows[]           = $row;
            $rows             = json_decode(json_encode($rows));


            /*$this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', $this->lang->secondmonthreport->demandunrealized);*/

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('rowmerge', ['startletter'=>'B','startnum'=>'1','endletter'=>'E','endnum'=>'1']);
            $this->post->set('colmerge', ['startletter'=>'A','startnum'=>'1','endletter'=>'A','endnum'=>'2']);
            $this->post->set('rowmergetitle', $this->lang->secondmonthreport->demandunrealizedNotice);
            $this->post->set('sheettitle', $this->lang->secondmonthreport->demandunrealized);
            $this->post->set('stardatarow', 3);
            $this->post->set('filename', $this->lang->secondmonthreport->demandunrealized);

            $this->fetch('file', 'exportphpexcel' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }
        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = [];
        $this->view->customExport    = false;

        $this->display();
    }

    /**
     * @Notes:需求条目实现超期统计表导出数据
     * @Date: 2023/10/20
     * @Time: 15:24
     * @Interface demandExceedExport
     * @param int $wholeID
     * @param int $deptID
     */
    public function demandExceedExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;
            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $secondmonthreportConfig->export->demandExceedInfoFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);

                $fields[$fieldName] = $secondmonthreportLang->{$fieldName} ?? $fieldName;

                unset($fields[$key]);
            }
            // Get $demandBrowseInfo.
            $depts          = $this->loadModel('dept')->getDeptByOrder();


            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'realizedNum' => $detailReport->detail->realizedNum,
                        'twoMonthNum'      => $detailReport->detail->twoMonthNum,
                        'amount'   => $detailReport->detail->amount,
                        'totalDemand'      => $detailReport->detail->totalDemand,
                        'overdueRate'      => $detailReport->detail->overdueRate.'%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('demand');
                $dataList = $this->secondmonthreport->getDemandDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->demandrealizedMonthStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'       => zget($depts, $detailReport->deptID),
                        'realizedNum' => $detailReport->detail->realizedNum,
                        'twoMonthNum'      => $detailReport->detail->twoMonthNum,
                        'amount'   => $detailReport->detail->amount,
                        'totalDemand'      => $detailReport->detail->totalDemand,
                        'overdueRate'      => $detailReport->detail->overdueRate.'%',
                    ];
                }
            }

            $row = [
                'deptName'       => $this->lang->secondmonthreport->total,
                'realizedNum' => array_sum(array_column($rows, 'realizedNum')),
                'twoMonthNum'      => array_sum(array_column($rows, 'twoMonthNum')),
                'amount'          => array_sum(array_column($rows, 'amount')),
                'totalDemand'          => array_sum(array_column($rows, 'totalDemand')),
            ];
            $row['overdueRate'] = ($row['totalDemand'] > 0 ? number_format(($row['amount'] / $row['totalDemand']) * 100, 2) : '0.00') . '%';
            $rows[]           = $row;
            $rows             = json_decode(json_encode($rows));



            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', $this->lang->secondmonthreport->demandExceed);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }
        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = [];
        $this->view->customExport    = false;

        $this->display();
    }

    /**
     * @Notes:需求任务内部反馈超期统计表导出
     * @Date: 2023/10/20
     * @Time: 17:51
     * @Interface demandExceedBackInExport
     * @param int $wholeID
     * @param int $deptID
     */
    public function demandExceedBackInExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;

            $fields = $this->post->exportFields ?: explode(',', $secondmonthreportConfig->export->demandExceedBackInInfoFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);

                $fields[$fieldName] = $secondmonthreportLang->{$fieldName} ?? $fieldName;

                unset($fields[$key]);
            }
            // Get $demandBrowseInfo.
            $depts          = $this->loadModel('dept')->getDeptByOrder();



            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[] = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('requirement');
                $dataList = $this->secondmonthreport->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->requirementInsideStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[] = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',
                    ];
                }
            }

            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'foverdueNum' => array_sum(array_column($rows, 'foverdueNum')),
                'backTotal'    => array_sum(array_column($rows, 'backTotal')),

            ];
            $row['backExceedRate'] = $row['backTotal'] > 0 ? sprintf("%0.2f",($row['foverdueNum']/$row['backTotal'])*100).'%' : '0.00%';

            $rows[]            = $row;
            $rows = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', $this->lang->secondmonthreport->demandExceedBackIn);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }
        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->export->demandExceedBackInInfoFields;
        $this->view->customExport    = false;

        $this->display();
    }

    /**
     * @Notes:需求任务外部反馈超期统计表导出
     * @Date: 2023/10/23
     * @Time: 10:10
     * @Interface demandExceedBackOutExport
     * @param int $wholeID
     * @param int $deptID
     */
    public function demandExceedBackOutExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData)
    {
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $secondmonthreportLang   = $this->lang->secondmonthreport;
            $secondmonthreportConfig = $this->config->secondmonthreport;
            // Create field lists.
            $fields = $this->post->exportFields ?: explode(',', $secondmonthreportConfig->export->demandExceedBackOutInfoFields);
            foreach ($fields as $key => $fieldName) {
                $fieldName = trim($fieldName);

                $fields[$fieldName] = $secondmonthreportLang->{$fieldName} ?? $fieldName;

                unset($fields[$key]);
            }
            // Get $demandBrowseInfo.

            $depts          = $this->loadModel('dept')->getDeptByOrder();




            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {

                    $rows[] = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $this->loadModel('requirement');
                $dataList = $this->secondmonthreport->getRequirementDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->requirementOutsideStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[] = [
                        'deptName'          => zget($depts, $detailReport->deptID),
                        'foverdueNum'         => $detailReport->detail->foverdueNum,
                        'backTotal'         => $detailReport->detail->backTotal,
                        'backExceedRate'    => $detailReport->detail->backExceedRate . '%',
                    ];
                }
            }

            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'foverdueNum' => array_sum(array_column($rows, 'foverdueNum')),
                'backTotal'    => array_sum(array_column($rows, 'backTotal')),

            ];
            $row['backExceedRate'] = $row['backTotal'] > 0 ? sprintf("%0.2f",($row['foverdueNum']/$row['backTotal'])*100).'%' : '0.00%';

            $rows[]            = $row;
            $rows = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', $this->lang->secondmonthreport->demandExceedBackOut);
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }
        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->allExportFields = $this->config->secondmonthreport->export->demandExceedBackOutInfoFields;
        $this->view->customExport    = false;

        $this->display();
    }




    /**
     * 问题池快照导出
     * @param $paramyear
     * @param $parammonth
     * @param $curtime
     * $formType == $staticType
     */
    public function problemphoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0)
    {
        $this->app->loadLang('problem');
        $this->loadModel('problem');
//        $this->loadModel('secondmonthreport');
        $this->loadModel('file');
        $problemLang   = $this->lang->problem;
        $problemConfig = $this->config->problem;
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
        // Create field lists.
//        $fields = explode(',', $problemConfig->list->exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $problemLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // Get problems.

        $problems    = [];
        $field       = 't2.objectId,t2.originalResolutionDate,t2.delayResolutionDate,t2.delayReason,t2.delayStatus,t2.delayVersion,t2.delayStage,t2.delayDealUser,t2.delayUser,t2.delayDate';
        $fieldArr       = ['originalResolutionDate'=>'','delayResolutionDate'=>'','delayReason'=>'','delayStatus'=>'','delayVersion'=>'','delayStage'=>'','delayDealUser'=>'','delayUser'=>'','delayDate'=>''];

        /*$problems    = $this->dao
            ->select("t1.*,{$field}")
            ->from(TABLE_PROBLEM)->alias('t1')
            ->leftJoin(TABLE_DELAY)->alias('t2')
            ->on("t1.id = t2.objectId and t2.objectType = 'problem'")
            ->where('t1.status')->ne('deleted')
            ->andWhere('t1.createdDate')->ge($dateFram['startdate'])
            ->andWhere('t1.createdDate')->le($dateFram['enddate'])
//            ->beginIF($datasource == 'feedback')->andWhere('t1.createdBy')->in(['guestcn','guestjx'])->fi()
            ->beginIF(in_array($formType,['problemExceedBackIn','problemExceedBackOut']) )->andWhere('t1.createdBy')->in(['guestcn','guestjx'])->fi()
            ->fetchAll('id');*/

        $problems = $this->secondmonthreport->getProblemDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);
        if($problems){
            $problemsIDS = array_keys($problems);
            //查询延期申请单
            $delaylogs    = $this->dao
                ->select($field)
                ->from(TABLE_DELAY)->alias('t2')
                ->where("t2.objectType")->eq('problem')
                ->andWhere("t2.objectId")->in($problemsIDS)
                ->fetchAll('objectId');

            //补齐数据
            foreach ($problems as $key=>$problem){
                if(isset($delaylogs[$key])){
                    foreach ($delaylogs[$key] as $fieldkey=>$fieldval){
                        $problem->$fieldkey = $fieldval;
                    }
                }else{
                    foreach ($fieldArr as $fieldkey=>$fieldval){
                        $problem->$fieldkey = $fieldval;
                    }
                }

            }
        }

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();
        $dmap  = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
        $plans = $this->loadModel('project')->getPairs();

        $productPairs     = ['99999' => '无'] + $this->loadModel('product')->getSimplePairs();
        $productPlanPairs = ['1' => '无']     + $this->loadModel('productplan')->getSimplePairs();

        $this->loadModel('secondline');
        foreach ($problems as $problem) {
            //是否超期
//            $problem                                     = $this->loadModel('problem')->getIsExceed($problem);
            $problem                                     = $this->problem->getIfOverDate($problem);
            $problem->monthreportfeedbackStartTimeInside = $problem->ifOverDateInside['start'] ?? '';
            $problem->monthreportfeedbackEndTimeInside   = $problem->deptPassTime              ?? '';
            $problem->monthreportdealAssigned            = $problem->dealAssigned              ?? '';
            $problem->insideFeedbackDate                 = $problem->ifOverDateInside['end']   ?? '';
            $problem->feedbackStartTimeOutside           = $problem->ifOverDate['start']       ?? '';
            $problem->monthreportfeedbackEndTimeOutside  = $problem->innovationPassTime        ?? '';
            $problem->outsideFeedbackDate                = $problem->ifOverDate['end']         ?? '';
            $problem->ifOverTime                         = $problem->ifOverDate['flag']        ?? '';
            $problem->ifOverTimeInside                   = $problem->ifOverDateInside['flag']  ?? '';
//                $problem->status = $problemLang->statusList[$problem->status];
            $problem->source     = $problemLang->sourceList[$problem->source];
            $problem->type       = $problemLang->typeList[$problem->type];
            $problem->severity   = $problemLang->severityList[$problem->severity];
            $problem->acceptUser = zget($users, $problem->acceptUser, '');
            $problem->acceptDept = zget($depts, $problem->acceptDept, '');

//            $problem->pri         = $problemLang->priList[$problem->pri];
            $problem->createdDept = $dmap[$problem->createdBy]->dept ? $depts[$dmap[$problem->createdBy]->dept] : '';

            $problem->monthreportcreatedBy = $users[$problem->createdBy];
            $problem->fixType              = zget($problemLang->fixTypeList, $problem->fixType, '');
            $problem->projectPlan          = zget($plans, $problem->projectPlan, '');
            //20220311 新增
            $problem->systemverify   = zget($this->lang->problem->needOptions, $problem->systemverify, '');
            $problem->verifyperson   = zget($users, $problem->verifyperson, '');
            $problem->laboratorytest = zget($users, $problem->laboratorytest, '');

            if (in_array($problem->status, ['feedbacked', 'build', 'released', 'delivery', 'onlinesuccess', 'closed'])) {
                $problem->dealUser = '';
            } else {
                $problem->dealUser = zmget($users, $problem->dealUser);
            }
            $problem->editedBy   = zget($users, $problem->editedBy, '');
            $problem->closedBy   = zget($users, $problem->closedBy, '');
            $problem->onlineTime = $problem->solvedTime;
            //如果关联二线取二线时间，如果有待关闭状态取待关闭时间，没有待关闭取已关闭时间，否则为空
            $problem            = $this->problem->getSolvedTime($problem);
            $problem->solveDate = '';
            if ('delivery' == $problem->status || 'onlinesuccess' == $problem->status || 'onlinefailed' == $problem->status) {
                $dealDateObj = $this->loadModel('consumed')->getDealDate('problem', $problem->id);
                if ($dealDateObj) {
                    $problem->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                }
            } elseif ('closed' == $problem->status) {
                $dealDateObj = $this->loadModel('problem')->getDate($problem->id);
                if ($dealDateObj) {
                    $problem->solveDate = $dealDateObj->lastDealDate;
                }
            }

            $problem->status = $problemLang->statusList[$problem->status];
            // 处理所属应用系统。
            if ($problem->app) {
                $as = [];
                foreach (explode(',', $problem->app) as $app) {
                    if (!$app) {
                        continue;
                    }
                    $as[] = zget($apps, $app);
                }
                $problem->app = implode(',', $as);
            }

            // 处理系统分类。
            if ($problem->isPayment) {
                $as = [];
                foreach (explode(',', $problem->isPayment) as $paymentID) {
                    if (!$paymentID) {
                        continue;
                    }
                    $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                }
                $isPayment          = implode(',', $as);
                $problem->isPayment = $isPayment;
            }
            // 获取所属产品。
            $productName = '';
            $products    = explode(',', $problem->product);
            foreach ($products as $item) {
                $productName .= zget($productPairs, $item, '') . ',';
            }
            $problem->product = trim($productName, ',');

            // 获取所属产品计划。
            $productPlanName = '';
            $productPlans    = explode(',', $problem->productPlan);
            foreach ($productPlans as $item) {
                $productPlanName .= zget($productPlanPairs, $item, '') . ',';
            }
            $problem->productPlan = trim($productPlanName, ',');
            // 获取制版次数。不需要查询获取了，有字段累计增加。
            //$problem->buildTimes = $this->problem->getBuild($problem->id);

            // 获取关联的生产变更，数据修正，数据获取。
            $problem->relationModify = '';
            $problem->relationFix    = '';
            $problem->relationGain   = '';

            //反馈单状态
            $problem->ReviewStatus = zget($this->lang->problem->feedbackStatusList, $problem->ReviewStatus);
            //反馈单待处理人
            /*if (null != $problem->feedbackToHandle) {
                $userName = '';
                $myArray  = explode(',', $problem->feedbackToHandle);
                foreach ($myArray as $account) {
                    if ('' == $userName) {
                        $userName .= $users[$account];
                    } else {
                        $userName .= ',';
                        $userName .= $users[$account];
                    }
                }
                $problem->feedbackToHandle = $userName;
            }*/
            //是否退回
            $problem->ifReturn = zget($this->lang->problem->ifReturnList, $problem->ifReturn);
            $problem->isChange = zget($this->lang->problem->isChangeList, $problem->isChange);
            //是否最终方案
            $problem->IfultimateSolution     = zget($this->lang->problem->ifultimateSolutionList, $problem->IfultimateSolution);
            $problem->isExtended             = zget($this->lang->problem->isExtendedList, $problem->isExtended);
            $problem->isBackExtended         = zget($this->lang->problem->isBackExtendedList, $problem->isBackExtended);
            $problem->ChangeIdRelated        = strip_tags($problem->ChangeIdRelated);
            $problem->EffectOfService        = strip_tags($problem->EffectOfService);
            $problem->IncidentIdRelated      = strip_tags($problem->IncidentIdRelated);
            $problem->DrillCausedBy          = strip_tags($problem->DrillCausedBy);
            $problem->Optimization           = strip_tags($problem->Optimization);
            $problem->Tier1Feedback          = strip_tags($problem->Tier1Feedback);
            $problem->solution               = strip_tags($problem->solution);
            $problem->ChangeSolvingTheIssue  = strip_tags($problem->ChangeSolvingTheIssue);
            $problem->ReasonOfIssueRejecting = strip_tags($problem->ReasonOfIssueRejecting);
            $problem->EditorImpactscope      = strip_tags($problem->EditorImpactscope);
            $problem->revisionRecord         = strip_tags($problem->revisionRecord);
            $problem->ProblemSource          = $problem->ProblemSource;
            $problem->sourece                = zget($this->lang->problem->sourceList, $problem->source, $problem->source);
            $problem->SolutionFeedback       = zget($this->lang->problem->solutionFeedbackList,$problem->SolutionFeedback,$problem->SolutionFeedback);
            //新增加字段-反馈单
            $problem->TimeOfOccurrence  = $problem->occurDate;
            $problem->problemFeedbackId = $problem->IssueId;
            $problem->ultimateSolution  = $problem->solution;
            //反馈次数
            if (null == $problem->IssueId) {
                $problem->feedbackNum = null;
            } else {
                if ($problem->feedbackNum > 0) {
                    $problem->feedbackNum = $problem->feedbackNum - 1;
                }
            }
            //延期信息
            /*$delay = $this->dao
                ->select('delayResolutionDate,delayStatus')
                ->from(TABLE_DELAY)
                ->where('objectType')->eq('problem')
                ->andWhere('objectId')->eq($problem->id)
                ->fetch();
            if (!empty($delay)) {
                $problem->delayResolutionDate = date('Y-m-d', strtotime($delay->delayResolutionDate));
                $problem->delayStatus         = zget($this->lang->problem->delayStatusList, $delay->delayStatus);
            } else {
                $problem->delayResolutionDate = '';
                $problem->delayStatus         = '';
            }*/
            if(!empty($problem->delayStatus)){
                $problem->monthreportdelayResolutionDate = date('Y-m-d', strtotime($problem->delayResolutionDate));
                $problem->delayStatus                    = zget($this->lang->problem->delayStatusList, $problem->delayStatus);
            }
            $problem->completedPlan   = zget($this->lang->problem->completedPlanList,$problem->completedPlan,$problem->completedPlan);
            $problem->examinationResult  = zget($this->lang->problem->examinationResultList,$problem->examinationResult,$problem->examinationResult);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $problems);
        $this->post->set('kind', 'problem');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名 问题池 内外部反馈  和其他表的数据源不一致单独生成文件。
        //文件名采用小写
        $formType = strtolower($formType);
        if($datasource == 'feedback'){
            $_POST['generatefilename'] = 'problem_monthreport_basic_' .$formType. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        }else{
            $_POST['generatefilename'] = 'problem_monthreport_basic_' .$formType. $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        }

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    public function requirementpartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType='')
    {

        $this->loadModel('requirement');
        $this->loadModel('file');
        $requirementLang   = $this->lang->requirement;
        $requirementConfig = $this->config->requirement;
        $this->app->loadLang('opinioninside');
        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $requirementLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }
        $requirements = [];


        // 查询需求数据，调用process方法获取评审人。
   /*     $requirements = $this->dao->select('t1.*')->from(TABLE_REQUIREMENT)->alias('t1')
            ->innerJoin(TABLE_OPINION)->alias('t2')
            ->on('t1.opinion=t2.id')
            ->where('t1.`status`')->ne('deleted')
            ->andWhere('t1.`status`')->ne('deleteout')
            ->andWhere('t1.createdBy')->eq('guestcn')
            ->andWhere('t1.id')->in($ids)
            ->andWhere('t1.sourceRequirement')->eq(1)
            ->andWhere('t1.createdDate')->ge($dataBetween['startdate'])
            ->andWhere('t1.createdDate')->le($dataBetween['enddate'])

            ->fetchAll('id');*/

        $requirements = $this->secondmonthreport->getRequirementDataListByIDs($ids);
        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->app->loadLang('opinion');
        $apps     = $this->loadModel('application')->getPairs();
   
        $depts    = $this->loadModel('dept')->getOptionMenu();
        foreach ($depts as $key => $dept) {
            $depts[$key] = substr_replace($dept, '', 0, 1);
        }

//        $projects = $this->loadModel('projectplan')->getPairs();
        foreach ($requirements as $requirement) {

            $demandsOther     = $this->requirement->getDemandByRequirement($requirement->id);
            $ownProjectArr    = !empty($requirement->project) ? explode(',', $requirement->project) : [];
            $demandProjectArr = array_column($demandsOther, 'project');
//            $mergeProjectArr  = array_merge($ownProjectArr, $demandProjectArr);

            $acceptUserArr      = array_filter(array_unique(array_column($demandsOther, 'acceptUser')));
            $acceptDeptArr      = array_filter(array_unique(array_column($demandsOther, 'acceptDept')));
            $requirement->owner = zmget($users, implode(',', $acceptUserArr));
            $requirement->dept  = zmget($depts, implode(',', $acceptDeptArr));

            //迭代二十九 【反馈人员所属部门】（若反馈人为空，则取反馈单待处理人所属部门，若反馈单待处理人为空则反馈人员所属部门为空）
            if (!empty($requirement->feedbackBy)) {
                $feedbackDepts = $this->loadModel('user')->getUserDeptIds($requirement->feedbackBy);
                $requirement->feedbackBy = zget($users, $requirement->feedbackBy, '');
                $requirement->feedbackDept = zmget($depts, implode(',', $feedbackDepts));
            }

            //待反馈状态取反馈单待处理人所属部门
            if($requirement->feedbackStatus == 'tofeedback')
            {
                $userDept = $this->loadModel('user')->getByAccount($requirement->feedbackDealUser);
                $requirement->feedbackDept = isset($userDept->dept) ? zget($depts, $userDept->dept, '') : '';
//                $requirement->feedbackBy = zget($users, $requirement->feedbackDealUser, '');
            }



            //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
            if ('guestcn' == $requirement->createdBy) {
                $requirement->acceptTime       = $requirement->createdDate;
                $requirement->newPublishedTime = '0000-00-00 00:00:00' != $requirement->feekBackStartTime ? $requirement->feekBackStartTime : '';
            } else {

                $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
            }

            $requirement->ID = $requirement->id;

            $requirement->status            = zget($requirementLang->statusList, $requirement->status, '');

            $requirement->monthreportmethod = zmget($requirementLang->actualMethodList, $requirement->actualMethod, '');


            $appList            = explode(',', $requirement->app);
            $appChnList         = [];
            foreach ($appList as $app) {
                $appChn       = zget($apps, $app, '');
                $appChnList[] = $appChn;
            }
            $requirement->app      = implode(',', $appChnList);


            $requirement->desc           = strip_tags($requirement->desc);

            $requirement->monthreportcreatedBy = zget($users, $requirement->createdBy, '');
            $requirement->editedBy             = zget($users, $requirement->editedBy, '');
            $requirement->closedBy             = zget($users, $requirement->closedBy, '');
            $requirement->activatedBy          = zget($users, $requirement->activatedBy, '');
            $requirement->ignoredBy            = zget($users, $requirement->ignoredBy, '');
            $requirement->recoveryedBy         = zget($users, $requirement->recoveryedBy, '');

            $requirement->feedbackStatus   = zget($requirementLang->feedbackStatusList, $requirement->feedbackStatus, '');

            //100 是一个标识，标记默认值 为否
            if (100 == $requirement->ifOverDate) {
                $requirement->ifOverTime = '否';
            } else {
                $requirement->ifOverTime = zget($this->lang->requirement->ifOverDateList, $requirement->ifOverDate, '');
            }
            //内部反馈开始时间
            $requirement->monthreportinsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTime ? $requirement->feekBackStartTime : '';
            //部门审核通过时间（内部反馈结束时间）
            $requirement->monthreportinsideEnd = '0000-00-00 00:00:00' != $requirement->deptPassTime ? $requirement->deptPassTime : '';
            //内部反馈期限
            $requirement->insideFeedback = '0000-00-00 00:00:00' != $requirement->feekBackEndTimeInside ? $requirement->feekBackEndTimeInside : '';
            $requirement->onlineTimeByDemand = '0000-00-00 00:00:00' != $requirement->onlineTimeByDemand ? $requirement->onlineTimeByDemand : '';

            //100 是一个标识，标记默认值 为否
            if (100 == $requirement->ifOverTimeOutSide) {
                $requirement->monthreportifOverTimeOutSide = '否';
            } else {
                $requirement->monthreportifOverTimeOutSide = zget($requirementLang->ifOverDateList, $requirement->ifOverTimeOutSide, '');
            }
            //外部反馈开始时间
            $requirement->outsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTimeOutside ? $requirement->feekBackStartTimeOutside : '';
            $requirement->monthreportoutsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTimeOutside ? $requirement->feekBackStartTimeOutside : '';
            //同步清总成功时间（外部反馈结束时间）
            $requirement->monthreportoutsideEnd = '0000-00-00 00:00:00' != $requirement->innovationPassTime ? $requirement->innovationPassTime : '';
            //外部反馈期限
            $requirement->outsideFeedback = $requirement->feekBackEndTimeOutSide;

            $requirement->ifOutUpdate    = zget($requirementLang->ifOutUpdateList, $requirement->ifOutUpdate);
            $requirement->feedbackOver   = zget($this->lang->requirement->feedbackOverList, $requirement->feedbackOver);
            $requirement->lastChangeTime = '0000-00-00 00:00:00' != $requirement->lastChangeTime ? $requirement->lastChangeTime : '';
        }
        $this->post->set('fields', $fields);
        $this->post->set('rows', $requirements);
        $this->post->set('kind', 'requirement');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);
        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'requirement_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    /**
     * 需求任务快照
     * @param $paramyear
     * @param $parammonth
     * @param $curtime
     */
    public function requirementphoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0)
    {

        $this->loadModel('requirement');
        $this->loadModel('file');
        $requirementLang   = $this->lang->requirement;
        $requirementConfig = $this->config->requirement;
        $this->app->loadLang('opinioninside');
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
//        $fields = explode(',', $requirementConfig->exportlist->exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $requirementLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        // 查询需求数据，调用process方法获取评审人。
        /*$requirements = $this->dao->select('t1.*')->from(TABLE_REQUIREMENT)->alias('t1')
            ->innerJoin(TABLE_OPINION)->alias('t2')
            ->on('t1.opinion=t2.id')
            ->where('t1.`status`')->ne('deleted')
            ->andWhere('t1.`status`')->ne('deleteout')
            ->andWhere('t1.createdBy')->eq('guestcn') //外部同步的单子
            ->andWhere('t1.sourceRequirement')->eq(1)
            ->andWhere('t1.createdDate')->ge($dataBetween['startdate'])
            ->andWhere('t1.createdDate')->le($dataBetween['enddate'])

            ->fetchAll('id');*/

        $requirements = $this->secondmonthreport->getRequirementDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);
        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $this->app->loadLang('opinion');
        $apps     = $this->loadModel('application')->getPairs();
        $lines    = $this->loadModel('product')->getLinePairs();
        $products = $this->product->getPairs();
        $depts    = $this->loadModel('dept')->getOptionMenu();
        foreach ($depts as $key => $dept) {
            $depts[$key] = substr_replace($dept, '', 0, 1);
        }

//        $projects = $this->loadModel('projectplan')->getPairs();
        foreach ($requirements as $requirement) {
            $opinion          = $this->loadModel('opinion')->getByID($requirement->opinion);
            $demandsOther     = $this->requirement->getDemandByRequirement($requirement->id);
            $ownProjectArr    = !empty($requirement->project) ? explode(',', $requirement->project) : [];
            $demandProjectArr = array_column($demandsOther, 'project');
            $mergeProjectArr  = array_merge($ownProjectArr, $demandProjectArr);
            /** @var projectplanModel $projectPlanModel */
            /*$projectPlanModel = $this->loadModel('projectplan');
            $projectArray     = array_filter(array_unique($mergeProjectArr));
            if (!empty($projectArray)) {
                $projectList = $projectPlanModel->getPlanInProjectIDs($projectArray);
                if ($projectList) {
                    $arr        = [];
                    $projectStr = '';
                    foreach ($projectList as $v) {
                        $arr[] = $v->id;
                    }
                    $projectStr           = implode(',', $arr);
                    $requirement->project = $projectStr;
                }
            }*/
            $acceptUserArr      = array_filter(array_unique(array_column($demandsOther, 'acceptUser')));
            $acceptDeptArr      = array_filter(array_unique(array_column($demandsOther, 'acceptDept')));
            $requirement->owner = zmget($users, implode(',', $acceptUserArr));
            $requirement->dept  = zmget($depts, implode(',', $acceptDeptArr));

            //迭代二十九 【反馈人员所属部门】（若反馈人为空，则取反馈单待处理人所属部门，若反馈单待处理人为空则反馈人员所属部门为空）
            if (!empty($requirement->feedbackBy)) {
                $feedbackDepts = $this->loadModel('user')->getUserDeptIds($requirement->feedbackBy);
                $requirement->feedbackBy = zget($users, $requirement->feedbackBy, '');
                $requirement->feedbackDept = zmget($depts, implode(',', $feedbackDepts));
            }

            //待反馈状态取反馈单待处理人所属部门
            if($requirement->feedbackStatus == 'tofeedback')
            {
                $userDept = $this->loadModel('user')->getByAccount($requirement->feedbackDealUser);
                $requirement->feedbackDept = isset($userDept->dept) ? zget($depts, $userDept->dept, '') : '';
//                $requirement->feedbackBy = zget($users, $requirement->feedbackDealUser, '');
            }

            //外部同步需求任务接收时间取创建时间，内部需求任务取需求意向接收时间
            if ('guestcn' == $requirement->createdBy) {
                $requirement->acceptTime       = $requirement->createdDate;
                $requirement->newPublishedTime = '0000-00-00 00:00:00' != $requirement->feekBackStartTime ? $requirement->feekBackStartTime : '';
            } else {
                $opinionInfo                   = $this->loadModel('opinion')->getByID($requirement->opinion);
                $requirement->acceptTime       = $opinionInfo->receiveDate ?? '';
//                $consumedInfo                  = $this->loadModel('consumed')->getCreatedDate('requirement', $requirement->id, '', 'published');
//                $requirement->newPublishedTime = $consumedInfo->createdDate ?? '';
                $requirement->newPublishedTime = $requirement->newPublishedTime != '0000-00-00 00:00:00' ? $requirement->newPublishedTime : '';
            }
            $requirement->ID = $requirement->id;
            $dealUserList    = explode(',', $requirement->dealUser);
            $dealUserChnList = [];
            foreach ($dealUserList as $dealUser) {
                $dealUserChn       = zget($users, $dealUser, '');
                $dealUserChnList[] = $dealUserChn;
            }
            $requirement->dealUser = implode(',', $dealUserChnList);
            if (in_array($requirement->status, ['delivered', 'onlined'])) {
                $requirement->dealUser = '';
            }
            $requirement->status            = zget($requirementLang->statusList, $requirement->status, '');
            $requirement->opinion           = $opinion->code;
            $requirement->sourceMode        = zget($this->lang->opinion->sourceModeListOld, $opinion->sourceMode, '');
            $requirement->monthreportmethod = zmget($requirementLang->actualMethodList, $requirement->actualMethod, '');
            $unionList                      = explode(',', $opinion->union);
            $unionChnList                   = [];
            foreach ($unionList as $union) {
                $unionChn       = zget($this->lang->opinion->unionList, $union, '');
                $unionChnList[] = $unionChn;
            }
            $requirement->union = implode(',', $unionChnList);
            $appList            = explode(',', $requirement->app);
            $appChnList         = [];
            foreach ($appList as $app) {
                $appChn       = zget($apps, $app, '');
                $appChnList[] = $appChn;
            }
            $requirement->app      = implode(',', $appChnList);
            $productManagerList    = explode(',', $requirement->productManager);
            $productManagerChnList = [];
            foreach ($productManagerList as $productManager) {
                $productManagerChn       = zget($users, $productManager, '');
                $productManagerChnList[] = $productManagerChn;
            }
            $requirement->productManager = implode(',', $productManagerChnList);

            $projectManagerList    = explode(',', $requirement->projectManager);
            $projectManagerChnList = [];
            foreach ($projectManagerList as $projectManager) {
                $projectManagerChn       = zget($users, $projectManager, '');
                $projectManagerChnList[] = $projectManagerChn;
            }
            $requirement->projectManager = implode(',', $projectManagerChnList);
            $requirement->desc           = strip_tags($requirement->desc);

            $lineList    = explode(',', $requirement->line);
            $lineChnList = [];
            foreach ($lineList as $line) {
                $lineChn       = zget($lines, $line, '');
                $lineChnList[] = $lineChn;
            }
            $requirement->line = implode(',', $lineChnList);

            $productList    = explode(',', $requirement->product);
            $productChnList = [];
            foreach ($productList as $product) {
                $productChn       = zget($products, $product, '');
                $productChnList[] = $productChn;
            }
            $requirement->product              = implode(',', $productChnList);
            $requirement->monthreportcreatedBy = zget($users, $requirement->createdBy, '');
            $requirement->editedBy             = zget($users, $requirement->editedBy, '');
            $requirement->closedBy             = zget($users, $requirement->closedBy, '');
            $requirement->activatedBy          = zget($users, $requirement->activatedBy, '');
            $requirement->ignoredBy            = zget($users, $requirement->ignoredBy, '');
            $requirement->recoveryedBy         = zget($users, $requirement->recoveryedBy, '');

            $feedbackDealUserList    = explode(',', $requirement->feedbackDealUser);
            $feedbackDealUserChnList = [];
            foreach ($feedbackDealUserList as $feedbackDealUser) {
                $feedbackDealUserChn       = zget($users, $feedbackDealUser, '');
                $feedbackDealUserChnList[] = $feedbackDealUserChn;
            }
            $requirement->feedbackDealUser = implode(',', $feedbackDealUserChnList);
            $requirement->feedbackStatus   = zget($requirementLang->feedbackStatusList, $requirement->feedbackStatus, '');

            //100 是一个标识，标记默认值 为否
            if (100 == $requirement->ifOverDate) {
                $requirement->ifOverTime = '否';
            } else {
                $requirement->ifOverTime = zget($this->lang->requirement->ifOverDateList, $requirement->ifOverDate, '');
            }
            //内部反馈开始时间
            $requirement->monthreportinsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTime ? $requirement->feekBackStartTime : '';
            //部门审核通过时间（内部反馈结束时间）
            $requirement->monthreportinsideEnd = '0000-00-00 00:00:00' != $requirement->deptPassTime ? $requirement->deptPassTime : '';
            //内部反馈期限
            $requirement->insideFeedback = '0000-00-00 00:00:00' != $requirement->feekBackEndTimeInside ? $requirement->feekBackEndTimeInside : '';
            $requirement->onlineTimeByDemand = '0000-00-00 00:00:00' != $requirement->onlineTimeByDemand ? $requirement->onlineTimeByDemand : '';

            //100 是一个标识，标记默认值 为否
            if (100 == $requirement->ifOverTimeOutSide) {
                $requirement->monthreportifOverTimeOutSide = '否';
            } else {
                $requirement->monthreportifOverTimeOutSide = zget($requirementLang->ifOverDateList, $requirement->ifOverTimeOutSide, '');
            }
            //外部反馈开始时间
            $requirement->outsideStart = '0000-00-00 00:00:00' != $requirement->feekBackStartTimeOutside ? $requirement->feekBackStartTimeOutside : '';
            //同步清总成功时间（外部反馈结束时间）
            $requirement->monthreportoutsideEnd = '0000-00-00 00:00:00' != $requirement->innovationPassTime ? $requirement->innovationPassTime : '';
            //外部反馈期限
            $requirement->outsideFeedback = $requirement->feekBackEndTimeOutSide;

            $requirement->ifOutUpdate    = zget($requirementLang->ifOutUpdateList, $requirement->ifOutUpdate);
            $requirement->feedbackOver   = zget($this->lang->requirement->feedbackOverList, $requirement->feedbackOver);
            $requirement->lastChangeTime = '0000-00-00 00:00:00' != $requirement->lastChangeTime ? $requirement->lastChangeTime : '';

        }
        $this->post->set('fields', $fields);
        $this->post->set('rows', $requirements);
        $this->post->set('kind', 'requirement');
        //文件路径

        //文件名 需求任务
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);
        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'requirement_monthreport_basic_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    public function demandpartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType='')
    {
//        $this->loadModel('secondmonthreport');

        $this->loadModel('demand');
        $this->loadModel('file');
        $this->loadModel('opinion');
        $demandLang   = $this->lang->demand;
        $demandConfig = $this->config->demand;
        $this->lang->demand->typeList = $this->lang->opinion->sourceModeListOld;
        $this->lang->demand->unionList = $this->lang->opinion->unionList;
        // Create field lists.
        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $demandLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }
        // Get demands.




        /*$demands     = $this->dao->select('*')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('sourceDemand')->eq(1) //查询外部的数据
            ->andWhere('fixType')->eq('second')
            ->andWhere('id')->in($ids)

            ->fetchAll('id');*/
        $demands = $this->secondmonthreport->getDemandDataListByIDs($ids);


        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();

        $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
        $this->loadModel('secondline');


        // Obtain the receiver.

        // 获取所有需求条目数据。
        $allRequirement = $this->loadModel('requirement')->getPairs();
        $opinionList    = $this->loadModel('opinion')->getPairs();
        foreach ($demands as $demandKey => $demand) {

//            $demand->actualMethod = $requirementInfo[$demand->requirementID]->actualMethod;
            if(isset($demand->publishedTime) && $demand->publishedTime){
                $demand = $this->loadModel('demand')->getIsExceed($demand, $demand->publishedTime);
            }else{
                $demand = $this->loadModel('demand')->getIsExceed($demand, '');
            }

            // 处理需求来源方式。
            $demand->type = zget($demandLang->typeList, $demand->type, '');
            // 处理业务需求单位。
            $demand->union = zget($demandLang->unionList, $demand->union, '');

            $demand->monthreportrequirementmethod = zmget($this->lang->requirement->actualMethodList, $demand->actualMethod, '');
            if($demand->publishedTime){
                $demand->newPublishedTime = $demand->publishedTime;
            }else{
                $demand->newPublishedTime = '';
            }

            $demand->state            = $demandLang->stateList[$demand->state];
            $demand->createdDept      = $depts[$dmap[$demand->createdBy]->dept];
            $demand->acceptUser       = zget($users, $demand->acceptUser, $demand->acceptUser);
            $demand->acceptDept       = zget($depts, $demand->acceptDept, '');
            $demand->createdBy        = $users[$demand->createdBy];
            //迭代二十五 只有已录入状态有待处理人,已挂起
            $dealUser = '';
            if (in_array(($demand->status), ['wait', 'suspend'])) {
                $dealUser = zget($users, $demand->dealUser, '');
            }
            $demand->dealUser = $dealUser;
            $demand->editedBy = zget($users, $demand->editedBy, '');
            //20220311 新增
            $demand->systemverify   = zget($this->lang->demand->needOptions, $demand->systemverify, '');
            $demand->verifyperson   = zget($users, $demand->verifyperson, '');
            $demand->laboratorytest = zget($users, $demand->laboratorytest, '');

            $demand->closedBy = zget($users, $demand->closedBy, '');
            $demand->desc     = strip_tags($demand->desc);

            if ($demand->fixType) {
                $demand->fixType = $demandLang->fixTypeList[$demand->fixType];
            }

            $demand->solveDate = '';
            if ('delivery' == $demand->status || 'onlinesuccess' == $demand->status || 'onlinefailed' == $demand->status) {
                $dealDateObj = $this->loadModel('consumed')->getDealDate('demand', $demand->id);
                if ($dealDateObj) {
                    $demand->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                }
            } elseif ('closed' == $demand->status) {
                $dealDateObj = $this->loadModel('demand')->getDate($demand->id);
                if ($dealDateObj) {
                    $demand->solveDate = $dealDateObj->lastDealDate;
                }
            }

            $demand->status = $demandLang->statusList[$demand->status];


            // 处理所属应用系统。
            if ($demand->app) {
                $as = [];
                foreach (explode(',', $demand->app) as $app) {
                    if (!$app) {
                        continue;
                    }
                    $as[] = zget($apps, $app);
                }
                $demand->app = implode(',', $as);
            }
            //接收时间取需求任务的接收时间
//            $demand->monthreportrcvDate = $requirementAcceptTime[$demand->requirementID];
            if ('0000-00-00' == $demand->end) {
                $demand->end = '';
            }

            // 获取需求意向。
            if (0 == $demand->opinionID) {
                $demand->opinionID = '';
            } else {
                $demand->opinionID = zget($opinionList, $demand->opinionID, '');
            }
            // 获取需求任务。
            if (0 == $demand->requirementID) {
                $demand->requirementID = '';
            } else {
                $demand->requirementID = zget($allRequirement, $demand->requirementID, '');
            }
            // 处理系统分类。
            if ($demand->isPayment) {
                $as = [];
                foreach (explode(',', $demand->isPayment) as $paymentID) {
                    if (!$paymentID) {
                        continue;
                    }
                    $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                }
                $isPayment         = implode(',', $as);
                $demand->isPayment = $isPayment;
            }

            // 获取关联的生产变更，数据修正，数据获取。
            $demand->relationModify = '';
            $demand->relationFix    = '';
            $demand->relationGain   = '';

            /*$demand->delayStatus = zget($this->lang->demand->delayStatusList, $demand->delayStatus, '');
            if (!empty($demand->delayResolutionDate)) {
                $demand->monthreportdelayResolutionDate = date('Y-m-d', strtotime($demand->delayResolutionDate));
            } else {
                $demand->monthreportdelayResolutionDate = $demand->delayResolutionDate;
            }*/
            $demand->isExtended = zget($this->lang->demand->isExtendedList, $demand->isExtended);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $demands);
        $this->post->set('kind', 'demand');
        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);
        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'demand_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    /**
     * 需求条目快照
     * @param $endtime
     * @param $curtime
     * @param $starttime
     * @param $type 1默认  2 自定义
     */
    public function demandphoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0)
    {
//        $this->loadModel('secondmonthreport');


        $this->loadModel('demand');
        $this->loadModel('file');
        $this->loadModel('opinion');

        $demandLang   = $this->lang->demand;
        $demandConfig = $this->config->demand;

        $this->lang->demand->typeList = $this->lang->opinion->sourceModeListOld;
        $this->lang->demand->unionList = $this->lang->opinion->unionList;

        // Create field lists.
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
//        $fields = explode(',', $demandConfig->list->exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $demandLang->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        /*$demands     = $this->dao->select('*')->from(TABLE_DEMAND)
            ->where('status')->ne('deleted')
            ->andWhere('sourceDemand')->eq(1) //查询外部的数据
            ->andWhere('fixType')->eq('second') //二线实现的需求条目
            ->andWhere('createdDate')->ge($dataBetween['startdate'])
            ->andWhere('createdDate')->le($dataBetween['enddate'])
            ->fetchAll('id');*/
        $demands = $this->secondmonthreport->getDemandDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();



        $dmap = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
        $this->loadModel('secondline');

        $productPairs     = $this->loadModel('product')->getSimplePairs();
        $productPlanPairs = $this->loadModel('productplan')->getSimplePairs();
        $plans            = $this->loadModel('project')->getPairs();

        /*$requirementInfo = $this->dao->select('id,acceptTime,opinion,createdBy,createdDate,method,actualMethod,feekBackStartTime,newPublishedTime')
            ->from(TABLE_REQUIREMENT)
            ->where('status')->ne('deleted')
            ->andWhere('`status`')->ne('deleteout')
            ->andWhere('sourceRequirement')->eq(1)
            ->fetchAll('id');

        $requirementAcceptTime = [];
        $newPublishedTime      = [];
        foreach ($requirementInfo as $item) {
            $requirementAcceptTime[$item->id] = $item->acceptTime;
            if ('guestcn' == $item->createdBy) {
                $requirementAcceptTime[$item->id] = $item->createdDate;
                $newPublishedTime[$item->id]      = '0000-00-00 00:00:00' != $item->feekBackStartTime ? $item->feekBackStartTime : '';
            } else {
                $opinionInfo                      = $this->loadModel('opinion')->getByIdSimple($item->opinion);
                $requirementAcceptTime[$item->id] = $opinionInfo->receiveDate ?? '';
//                $consumedInfo                     = $this->loadModel('consumed')->getCreatedDate('requirement', $item->id, '', 'published');
//                $newPublishedTime[$item->id]      = $consumedInfo->createdDate ?? '';
                $newPublishedTime[$item->id]      = $item->newPublishedTime != '0000-00-00 00:00:00' ? $item->newPublishedTime : '';
            }
        }

        $newPublishedTime = array_filter($newPublishedTime);*/
        // Obtain the receiver.

        // 获取所有需求条目数据。
        $allRequirement = $this->loadModel('requirement')->getPairs();
        $opinionList    = $this->loadModel('opinion')->getPairs();
        foreach ($demands as $demandKey => $demand) {

//            $demand->actualMethod = $demand->actualMethod;
            if(isset($demand->publishedTime) && $demand->publishedTime){
                $demand = $this->loadModel('demand')->getIsExceed($demand, $demand->publishedTime);
            }else{
                $demand = $this->loadModel('demand')->getIsExceed($demand, '');
            }

            // 处理需求来源方式。
            $demand->type = zget($demandLang->typeList, $demand->type, '');
            // 处理业务需求单位。
            $demand->union = zget($demandLang->unionList, $demand->union, '');

            $demand->monthreportrequirementmethod = zmget($this->lang->requirement->actualMethodList, $demand->actualMethod, '');

            if($demand->publishedTime){
                $demand->newPublishedTime = $demand->publishedTime;
            }else{
                $demand->newPublishedTime = '';
            }

            $demand->state            = $demandLang->stateList[$demand->state];
            $demand->createdDept      = $depts[$dmap[$demand->createdBy]->dept];
            $demand->acceptUser       = zget($users, $demand->acceptUser, $demand->acceptUser);
            $demand->acceptDept       = zget($depts, $demand->acceptDept, '');
            $demand->createdBy        = $users[$demand->createdBy];
            //迭代二十五 只有已录入状态有待处理人,已挂起
            $dealUser = '';
            if (in_array(($demand->status), ['wait', 'suspend'])) {
                $dealUser = zget($users, $demand->dealUser, '');
            }
            $demand->dealUser = $dealUser;
            $demand->editedBy = zget($users, $demand->editedBy, '');
            //20220311 新增
            $demand->systemverify   = zget($this->lang->demand->needOptions, $demand->systemverify, '');
            $demand->verifyperson   = zget($users, $demand->verifyperson, '');
            $demand->laboratorytest = zget($users, $demand->laboratorytest, '');

            $demand->closedBy = zget($users, $demand->closedBy, '');
            $demand->desc     = strip_tags($demand->desc);

            if ($demand->fixType) {
                $demand->fixType = $demandLang->fixTypeList[$demand->fixType];
            }

            $demand->solveDate = '';
            if ('delivery' == $demand->status || 'onlinesuccess' == $demand->status || 'onlinefailed' == $demand->status) {
                $dealDateObj = $this->loadModel('consumed')->getDealDate('demand', $demand->id);
                if ($dealDateObj) {
                    $demand->solveDate = date('Y-m-d', strtotime($dealDateObj->createdDate));
                }
            } elseif ('closed' == $demand->status) {
                $dealDateObj = $this->loadModel('demand')->getDate($demand->id);
                if ($dealDateObj) {
                    $demand->solveDate = $dealDateObj->lastDealDate;
                }
            }

            $demand->status = $demandLang->statusList[$demand->status];

            // 处理所属应用系统。
            if ($demand->project) {
                $as = [];
                foreach (explode(',', $demand->project) as $project) {
                    if (!$project) {
                        continue;
                    }
                    $as[] = zget($plans, $project);
                }
                $demand->project = implode(',', $as);
            }
            // 处理所属应用系统。
            if ($demand->app) {
                $as = [];
                foreach (explode(',', $demand->app) as $app) {
                    if (!$app) {
                        continue;
                    }
                    $as[] = zget($apps, $app);
                }
                $demand->app = implode(',', $as);
            }
            //接收时间取需求任务的接收时间
//            $demand->monthreportrcvDate = $requirementAcceptTime[$demand->requirementID];
            if ('0000-00-00' == $demand->end) {
                $demand->end = '';
            }

            // 获取需求意向。
            if (0 == $demand->opinionID) {
                $demand->opinionID = '';
            } else {
                $demand->opinionID = zget($opinionList, $demand->opinionID, '');
            }
            // 获取需求任务。
            if (0 == $demand->requirementID) {
                $demand->requirementID = '';
            } else {
                $demand->requirementID = zget($allRequirement, $demand->requirementID, '');
            }
            // 处理系统分类。
            if ($demand->isPayment) {
                $as = [];
                foreach (explode(',', $demand->isPayment) as $paymentID) {
                    if (!$paymentID) {
                        continue;
                    }
                    $as[] = zget($this->lang->application->isPaymentList, $paymentID, $paymentID);
                }
                $isPayment         = implode(',', $as);
                $demand->isPayment = $isPayment;
            }

            // 获取制版次数。
            $demand->buildTimes = $this->demand->getBuild($demand->id);

            // 获取所属产品。
            $demand->product = zget($productPairs, $demand->product, '');

            // 获取所属产品计划。
            $demand->productPlan = zget($productPlanPairs, $demand->productPlan, '');

            // 获取关联的生产变更，数据修正，数据获取。
            $demand->relationModify = '';
            $demand->relationFix    = '';
            $demand->relationGain   = '';

            /*$demand->delayStatus = zget($this->lang->demand->delayStatusList, $demand->delayStatus, '');
            if (!empty($demand->delayResolutionDate)) {
                $demand->monthreportdelayResolutionDate = date('Y-m-d', strtotime($demand->delayResolutionDate));
            } else {
                $demand->monthreportdelayResolutionDate = $demand->delayResolutionDate;
            }*/
            $demand->isExtended = zget($this->lang->demand->isExtendedList, $demand->isExtended);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $demands);
        $this->post->set('kind', 'demand');
        //文件路径

        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);
        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'demand_monthreport_basic_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }



    //搜索和列表展示
    public function getDeptSelect()
    {
        $debts = $this->loadModel('dept')->getOptionMenu();
        foreach ($debts as $key => $dept) {
            $debts[$key] = 0 < $key ? trim($dept, '/') : '全部';
        }

        $debts[-1] = '空';


        return $debts;
    }

    /**
     * @Notes:搜索部门
     * @Date: 2023/10/30
     * @Time: 10:13
     * @Interface getDepts
     * @return mixed
     */
    public function getDepts()
    {
        $depts = $this->loadModel('dept')->getOptionMenu();
        foreach ($depts as $key => $dept) {
            $depts[$key] = substr_replace($dept, '', 0, 1);
        }
        $depts[0] = '全部';

        return $depts;
    }


    //工单类型统计基础快照
    public function secondorderclassphoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0){

        $this->loadModel('file');

        // Create field lists.
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->secondorderphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }


  /*      $sencondorderList = $this->dao->select('`code`,`status`,`app`,`summary`,`type`,`acceptUser`,`acceptDept`')->from(TABLE_SECONDORDER)
            ->where('deleted')->eq('0')
            ->andWhere('createdDate')->between($timeFrame['startdate'], $timeFrame['enddate'])
            ->beginIF($ispart)->andWhere('id')->in($ids)->fi()
            ->orderBy("id_desc")
            ->fetchAll();*/

        $sencondorderList = $this->secondmonthreport->getSecondorderDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();
        $this->loadModel('secondorder');

        foreach ($sencondorderList as $secondorder) {
            $subtype = $this->loadModel('secondorder')->getChildTypeList($secondorder->type);
            $secondorder->acceptDept = zget($depts,$secondorder->acceptDept,'');
            $secondorder->acceptUser = zget($users,$secondorder->acceptUser);
            $secondorder->app = zget($apps,$secondorder->app);
            $secondorder->status = zget($this->lang->secondorder->statusList,$secondorder->status);
            $secondorder->type = zget($this->lang->secondorder->typeList,$secondorder->type);
            $secondorder->ifAccept = zget($this->lang->secondorder->ifAcceptList,$secondorder->ifAccept);
            $secondorder->subtype = zget($subtype,$secondorder->subtype);
            $secondorder->createdUser = zget($users,$secondorder->createdBy);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $sencondorderList);
        $this->post->set('kind', 'secondorderclass');
        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'secondorder_monthreport_basic_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }
    public function secondorderclasspartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType=''){

        $this->loadModel('file');

        // Create field lists.
        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->secondorderphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }


        $sencondorderList = $this->secondmonthreport->getSecondorderDataListByIDs($ids);


        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();
        $this->loadModel('secondorder');

        foreach ($sencondorderList as $secondorder) {
            $subtype = $this->loadModel('secondorder')->getChildTypeList($secondorder->type);
            $secondorder->acceptDept = zget($depts,$secondorder->acceptDept,'');
            $secondorder->acceptUser = zget($users,$secondorder->acceptUser);
            $secondorder->app = zget($apps,$secondorder->app);
            $secondorder->status = zget($this->lang->secondorder->statusList,$secondorder->status);
            $secondorder->type = zget($this->lang->secondorder->typeList,$secondorder->type);
            $secondorder->ifAccept = zget($this->lang->secondorder->ifAcceptList,$secondorder->ifAccept);
            $secondorder->subtype = zget($subtype,$secondorder->subtype);
            $secondorder->createdUser = zget($users,$secondorder->createdBy);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $sencondorderList);
        $this->post->set('kind', 'secondorderclass');
        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'secondorder_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    //变更整体统计表快照
//    public function modifywholephoto($endtime='', $curtime = 0,$starttime='',$dtype=1,$ispart = 0,$ids=['modify'=>[],'modifycc'=>[]],$ftype=''){
    public function modifywholephoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0){

        $this->loadModel('file');

        // Create field lists.
        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->modifyphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        if($formType == 'modifyabnormal'){
            $allList = $this->loadModel('secondmonthreport')->getModifyFinishData($dateFram['startdate'],$dateFram['enddate'],0);
            $modifyList = $allList['modify'];
            $modifyccList = $allList['modifycncc'];
            $creditList   = $allList['credit'];
        }else{
            $modifyList = $this->secondmonthreport->getModifyDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);
            $modifyccList = $this->secondmonthreport->getModifycnccDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);
            $creditList   = $this->secondmonthreport->getCreditDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);
        }

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();
        $this->loadModel('modify');
        $this->loadModel('modifycncc');
        $modifyList = array_values($modifyList);
        foreach ($modifyList as $modify) {
            $modify->createdDept = zget($depts,$modify->createdDept);
            $modify->createdBy = zget($users,$modify->createdBy);
            $modify->mode = zget($this->lang->modify->modeList,$modify->mode);
            $modify->level = zget($this->lang->modify->levelList,$modify->level);
            $modify->app = zmget($apps,$modify->app);
            $modify->status = zget($this->lang->modify->statusList,$modify->status);
            $modify->type = zget($this->lang->modify->typeList,$modify->type);
            $modify->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$modify->exybtjsource);
        }
        $modifyccList = array_values($modifyccList);
        foreach ($modifyccList as $modifycc) {
            $modifycc->createdDept = zget($depts,$modifycc->createdDept);
            $modifycc->createdBy = zget($users,$modifycc->createdBy);
            $modifycc->mode = zget($this->lang->modifycncc->modeList,$modifycc->mode);
            $modifycc->level = zget($this->lang->modifycncc->levelList,$modifycc->level);
            $modifycc->app = zmget($apps,$modifycc->app);
            $modifycc->status = zget($this->lang->modifycncc->statusList,$modifycc->status);
            $modifycc->type = zget($this->lang->modifycncc->typeList,$modifycc->type);
            $modifycc->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$modifycc->exybtjsource);
        }
        $this->app->loadLang('credit');
        $creditList = array_values($creditList);
        foreach ($creditList as $credit) {
            $credit->createdDept = zget($depts,$credit->createdDept);
            $credit->createdBy = zget($users,$credit->createdBy);
            $credit->mode = zget($this->lang->modifycncc->modeList,$credit->mode);
            $credit->level = zget($this->lang->credit->levelList,$credit->level);
            $credit->app = zmget($apps,$credit->appIds);
            $credit->status = zget($this->lang->credit->statusList,$credit->status);
            $credit->type = zget($this->lang->credit->emergencyTypeList,$credit->type);
            $credit->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$credit->exybtjsource);
        }

        $modifyccList = array_merge($modifyccList,$modifyList,$creditList);


        $this->post->set('fields', $fields);
        $this->post->set('rows', $modifyccList);
        $this->post->set('kind', 'modifywhole');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'modify_monthreport_basic_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }
    public function modifywholepartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType=''){

        $this->loadModel('file');

        // Create field lists.
        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->modifyphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }


        $modifyList = $this->secondmonthreport->getModifyDataListByIDs($ids['modify']);
        $modifyccList = $this->secondmonthreport->getModifycnccDataListByIDs($ids['modifycncc']);
        $creditList = $this->secondmonthreport->getCreditDataListByIDs($ids['credit']);


        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();
        $this->loadModel('modify');
        $this->loadModel('modifycncc');
        $modifyList = array_values($modifyList);
        foreach ($modifyList as $modify) {
            $modify->createdDept = zget($depts,$modify->createdDept);
            $modify->createdBy = zget($users,$modify->createdBy);
            $modify->mode = zget($this->lang->modify->modeList,$modify->mode);
            $modify->level = zget($this->lang->modify->levelList,$modify->level);
            $modify->app = zmget($apps,$modify->app);
            $modify->status = zget($this->lang->modify->statusList,$modify->status);
            $modify->type = zget($this->lang->modify->typeList,$modify->type);
            $modify->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$modify->exybtjsource);
        }
        $modifyccList = array_values($modifyccList);
        foreach ($modifyccList as $modifycc) {
            $modifycc->createdDept = zget($depts,$modifycc->createdDept);
            $modifycc->createdBy = zget($users,$modifycc->createdBy);
            $modifycc->mode = zget($this->lang->modifycncc->modeList,$modifycc->mode);
            $modifycc->level = zget($this->lang->modifycncc->levelList,$modifycc->level);
            $modifycc->app = zmget($apps,$modifycc->app);
            $modifycc->status = zget($this->lang->modifycncc->statusList,$modifycc->status);
            $modifycc->type = zget($this->lang->modifycncc->typeList,$modifycc->type);
            $modifycc->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$modifycc->exybtjsource);
        }
        $this->app->loadLang('credit');
        foreach ($creditList as $credit) {
            $credit->createdDept = zget($depts,$credit->createdDept);
            $credit->createdBy = zget($users,$credit->createdBy);
            $credit->mode = zget($this->lang->modifycncc->modeList,$credit->mode);
            $credit->level = zget($this->lang->credit->levelList,$credit->level);
            $credit->app = zmget($apps,$credit->appIds);
            $credit->status = zget($this->lang->credit->statusList,$credit->status);
            $credit->type = zget($this->lang->credit->emergencyTypeList,$credit->type);
            $credit->exybtjsource = zget($this->lang->secondmonthreport->modifyexybtjsourceList,$credit->exybtjsource);
        }

        $modifyccList = array_merge($modifyccList,$modifyList,$creditList);


        $this->post->set('fields', $fields);
        $this->post->set('rows', $modifyccList);
        $this->post->set('kind', 'modifywhole');
        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'modify_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }
    //现场支持统计快照
    public function supportphoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0){


        $this->loadModel('file');

        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->supportphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }




        /*$supportList = $this->dao->select('`id`,`sdate`,`edate`,`area`,`app`,`stype`,`reason`,`dept`,`pnams`,`workh`')->from(TABLE_FLOW_SUPPORT)
            ->where('deleted')->eq('0')
//            ->andWhere('status')->eq('2')
            ->andWhere('sdate')->between($timeFrame['startdate'], $timeFrame['enddate'])
            ->beginIF($ispart)->andWhere('id')->in($ids)->fi()
            ->orderBy("id_desc")
            ->fetchAll();*/
        $supportList = $this->secondmonthreport->getSupportDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();

        $areaFieldInfo = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('support')->andWhere('field')->eq('area')->fetch();
        $areaList = json_decode($areaFieldInfo->options,true);

        $stypeFieldInfo = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('support')->andWhere('field')->eq('stype')->fetch();
        $stypeList = json_decode($stypeFieldInfo->options,true);

        foreach ($supportList as $support) {
            $support->reason = htmlspecialchars_decode(strip_tags($support->reason));
            $support->dept = zget($depts,$support->dept);
            $support->pnams = zmget($users,$support->pnams);
            $support->app = zmget($apps,$support->appIds);
            $support->area = zget($areaList,$support->area);
            $support->stype = zget($stypeList,$support->stype);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $supportList);
        $this->post->set('kind', 'support');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'support_monthreport_basic_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }


    public function supportpartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType=''){


        $this->loadModel('file');

        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->supportphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }




        $supportList = $this->secondmonthreport->getSupportDataListByIDs($ids);

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');
        $apps  = $this->loadModel('application')->getPairs();
        $depts = $this->secondmonthreport->getremovedeptbias();

        $areaFieldInfo = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('support')->andWhere('field')->eq('area')->fetch();
        $areaList = json_decode($areaFieldInfo->options,true);

        $stypeFieldInfo = $this->dao->select("`options`")->from(TABLE_WORKFLOWFIELD)->where('module')->eq('support')->andWhere('field')->eq('stype')->fetch();
        $stypeList = json_decode($stypeFieldInfo->options,true);

        foreach ($supportList as $support) {
            $support->dept = zget($depts,$support->dept);
            $support->pnams = zmget($users,$support->pnams);
            $support->app = zmget($apps,$support->appIds);
            $support->area = zget($areaList,$support->area);
            $support->stype = zget($stypeList,$support->stype);
        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $supportList);
        $this->post->set('kind', 'support');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名 需求条目
        $_POST['generatefilename'] = 'support_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';
        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }

    //工作量统计快照
    public function workloadphoto($dateFram,$time,$formType,$datasource='all',$isuseHisData=0){

        $this->loadModel('file');

        $fieldArrs = $this->secondmonthreport->getexportField($formType,'');
        $fields = explode(',', $fieldArrs['basic'] );
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->workloadphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }

        $effortList = $this->secondmonthreport->getWorkloadDataList($dateFram['startdate'],$dateFram['enddate'],0,$formType,$isuseHisData);
        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');

        $depts = $this->secondmonthreport->getremovedeptbias();

        $pattern = '/CFIT-(?:Q|T|D|WD)-\d{8}-\d{2,}/';

        foreach ($effortList as $effort) {
            $effort->deptID = zget($depts,$effort->deptID);
            $effort->account = zget($users,$effort->account);
            $effort->abstract = '';
            if(preg_match($pattern,$effort->name,$matches)){
                if(isset($matches[0]) && $matches[0]){
                    if(strpos($matches[0],'CFIT-Q-') !== false ){
                        //问题池
                        $effort->abstract = $this->dao->select("abstract")->from(TABLE_PROBLEM)->where('code')->eq($matches[0])->fetch('abstract');
                    }else if(strpos($matches[0],'CFIT-D-') !== false ){
                        //需求池外部
                        $effort->abstract = $this->dao->select("`desc` as abstract")->from(TABLE_DEMAND)->where('code')->eq($matches[0])->fetch('abstract');
                        $effort->abstract = strip_tags($effort->abstract);
                    }else if(strpos($matches[0],'CFIT-WD-') !== false ){
                        //需求池内部 去除 需求池内部数据
                        unset($effortList[$key]);

                    }else if(strpos($matches[0],'CFIT-T-') !== false ){
                        //工单池
                        $effort->abstract = $this->dao->select("summary as abstract")->from(TABLE_SECONDORDER)->where('code')->eq($matches[0])->fetch('abstract');

                    }
                    if($effort->abstract){
                        $effort->abstract = strip_tags($effort->abstract);
                        $effort->abstract = html_entity_decode($effort->abstract);
                    }
                }

            }

        }

        $this->post->set('fields', $fields);
        $this->post->set('rows', $effortList);
        $this->post->set('kind', 'workload');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名
        $_POST['generatefilename'] = 'workload_monthreport_basic_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }
    public function workloadpartphoto($dateFram,$time,$ids=[],$exportMonthReportFields='',$formType=''){

        $this->loadModel('file');

        $fields = explode(',', $exportMonthReportFields);
        foreach ($fields as $key => $fieldName) {
            $fieldName          = trim($fieldName);
            $fields[$fieldName] = $this->lang->secondmonthreport->workloadphoto->{$fieldName} ?? $fieldName;
            unset($fields[$key]);
        }
        $effortList = $this->secondmonthreport->getWorkloadDataListByIDs($ids);

        // Get users, products and executions.
        $users = $this->loadModel('user')->getPairs('noletter');

        $depts = $this->secondmonthreport->getremovedeptbias();

        $pattern = '/CFIT-(?:Q|T|D|WD)-\d{8}-\d{2,}/';

        foreach ($effortList as $effort) {
            $effort->deptID = zget($depts,$effort->deptID);
            $effort->account = zget($users,$effort->account);
            $effort->abstract = '';
            if(preg_match($pattern,$effort->name,$matches)){
                if(isset($matches[0]) && $matches[0]){
                    if(strpos($matches[0],'CFIT-Q-') !== false ){
                        //问题池
                        $effort->abstract = $this->dao->select("abstract")->from(TABLE_PROBLEM)->where('code')->eq($matches[0])->fetch('abstract');
                    }else if(strpos($matches[0],'CFIT-D-') !== false ){
                        //需求池外部
                        $effort->abstract = $this->dao->select("`desc` as abstract")->from(TABLE_DEMAND)->where('code')->eq($matches[0])->fetch('abstract');
                        $effort->abstract = strip_tags($effort->abstract);
                    }else if(strpos($matches[0],'CFIT-WD-') !== false ){
                        //需求池内部 去除 需求池内部数据
                        unset($effortList[$key]);

                    }else if(strpos($matches[0],'CFIT-T-') !== false ){
                        //工单池
                        $effort->abstract = $this->dao->select("summary as abstract")->from(TABLE_SECONDORDER)->where('code')->eq($matches[0])->fetch('abstract');

                    }
                    if($effort->abstract){
                        $effort->abstract = strip_tags($effort->abstract);
                        $effort->abstract = html_entity_decode($effort->abstract);
                    }
                }

            }

        }


        $this->post->set('fields', $fields);
        $this->post->set('rows', $effortList);
        $this->post->set('kind', 'workload');

        //文件路径
        $_POST['generatefilepath'] = dirname(__DIR__, 2) . '/www/data/upload/monthreport/';
        $this->checkPathIsExistAndBuild($_POST['generatefilepath']);

        $filestartday = str_replace(['-','_'],'',$dateFram['startday']);
        $fileendday = str_replace(['-','_'],'',$dateFram['endday']);
        //文件名采用小写
        $formType = strtolower($formType);
        //文件名
        $_POST['generatefilename'] = 'workload_monthreport_'.$formType . $filestartday . '_' . $fileendday. '_' . $time . '.xlsx';

        $res = $this->fetch('file', 'write2xlsx', $_POST);
        return true;
    }
    //生成excel目录检测
    public function checkPathIsExistAndBuild($path){
        if(!file_exists($path))
        {
            @mkdir($path, 0777, true);
            touch($path . 'index.html');
            //2023-11-03 兼容cli模式下生成的目录文件权限
            if(PHP_SAPI == 'cli'){
                chmod($path,0777);
                chmod($path . 'index.html',0777);
                @chown($path,'apache');
                @chown($path . 'index.html','apache');
                @chgrp($path,'apache');
                @chgrp($path . 'index.html','apache');
            }
        }
    }

    //任务工单
    public function secondorderclass(){

        $staticType = 'secondorderclass';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;
        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
                if('hisquarter' == $histimetype){
                    $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                    $this->view->quarterReport = $quarterReport;
                }
            }
        }else{
            //是否使用结转数据
            if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }

            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;


        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;

        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'secondorderclass';
//        $this->view->depts       = $this->getDeptSelect();
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);

        $this->view->title       = $this->lang->secondmonthreport->secondorderclass;
        $this->view->detailTitle = $this->lang->secondmonthreport->secondorderclass .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';

        $this->display();
    }
    public function secondorderaccept(){
        $staticType = 'secondorderaccept';
        $this->app->loadLang('secondorder');
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;
        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{
            //是否使用结转数据 暂时注释后期看业务方要求
            /*if($realtimetype == 'curyear'){
                $isuseHisData = 1;
            }*/

            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);
            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->curWholeReport  = $wholeInfo;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;

        //左侧选中标识，传当前方法名字
        $this->view->selected    = 'secondorderaccept';
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);

        $this->view->title       = $this->lang->secondmonthreport->secondorderaccept;
        $this->view->detailTitle = $this->lang->secondmonthreport->secondorderaccept .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function modifywhole(){

        $staticType = 'modifywhole';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                }
            }
        }else{
            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = $staticType;
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);

        $this->view->title       = $this->lang->secondmonthreport->modifywhole;
        $this->view->detailTitle = $this->lang->secondmonthreport->modifywhole .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }
    public function modifyabnormal(){
        $staticType = 'modifyabnormal';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = true;
                    }
                    if('hisquarter' == $histimetype){
                        $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                        $this->view->quarterReport = $quarterReport;
                    }
                }
            }
        }else{


            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = true;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;


        //左侧选中标识，传当前方法名字
        $this->view->selected    = $staticType;
//        $this->view->depts       = $this->getDeptSelect();
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);

        $this->view->title       = $this->lang->secondmonthreport->modifyabnormal;
        $this->view->detailTitle = $this->lang->secondmonthreport->modifyabnormal .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }
    public function support(){

        $staticType = 'support';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = false;
                    }
                    if('hisquarter' == $histimetype){
                        $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                        $this->view->quarterReport = $quarterReport;
                    }
                }
            }
        }else{


            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = false;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;


        //左侧选中标识，传当前方法名字
        $this->view->selected    = $staticType;
        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);

        $this->view->title       = $this->lang->secondmonthreport->support;
        $this->view->detailTitle = $this->lang->secondmonthreport->support .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }

    public function workload(){

        $staticType = 'workload';
        //搜索提交的条件
        $searchtype = 'history';
        $histimetype = 'hismonth';
        if($_POST){
            $searchtype = $_POST['searchtype'];
            $histimetype = isset($_POST['histimetype']) ? $_POST['histimetype'] : '';
            $hisdatelist = isset($_POST['hisdatelist']) ? $_POST['hisdatelist'] : '';
            $realtimetype = isset($_POST['realtimetype']) ? $_POST['realtimetype'] : '';
            $realstarttime = isset($_POST['realstarttime']) ? $_POST['realstarttime'] : '';
            $realendtime = isset($_POST['realendtime']) ? $_POST['realendtime'] : '';
            $sformtype = isset($_POST['sformtype']) ? $_POST['sformtype'] : '';
        }else{
//            $histimetype =  '';
            $hisdatelist =  '';
            $realtimetype = '';
            $realstarttime = '';
            $realendtime =  '';
            $sformtype =    '';
            $hisdatelist = $this->secondmonthreport->getSearchDefaultID($staticType);
        }
        $deptID = 0;
        if (isset($_POST['deptID']) && $_POST['deptID']) {
            $deptID = $_POST['deptID'];
        }
        $this->view->histimetype = $histimetype;
        $this->view->hisdatelist = $hisdatelist;
        $this->view->realtimetype = $realtimetype;
        $this->view->realstarttime = $realstarttime;
        $this->view->realendtime = $realendtime;
        $this->view->searchtype = $searchtype;
        $this->view->staticType = $staticType;


        $str    = '';

        $wholeInfo = null;
        $isuseHisData = 0;
        $isshowdetaillist = false;
        if($searchtype == 'history'){

            $detailReports = [];
            if ($hisdatelist) {
                $detailReports = $this->secondmonthreport->getOrderDataList($hisdatelist, $deptID,'deptID');

                $wholeInfo                 = $this->secondmonthreport->getWholeReportByID($hisdatelist);
                if($wholeInfo){
                    $str = $wholeInfo->startday.' ~ '.$wholeInfo->endday;
                    if($wholeInfo->useIDArr && $wholeInfo->exportFields && $wholeInfo->exportFields){
                        $isshowdetaillist = false;
                    }
                    if('hisquarter' == $histimetype){
                        $quarterReport = $this->secondmonthreport->getProblemOverallReport($wholeInfo, $deptID);
                        $this->view->quarterReport = $quarterReport;
                    }
                }
            }
        }else{


            $detailReports = $this->secondmonthreport->getProblemReal($realstarttime, $realendtime,$staticType,$deptID,$isuseHisData);

            $detailReports = $this->secondmonthreport->getOrderRealTimeDataList($detailReports);
            $str = $realstarttime.' ~ '.$realendtime;
            $isshowdetaillist = false;

        }
        $this->view->isuseHisData = $isuseHisData;
        $this->view->isshowdetaillist = $isshowdetaillist;

        $this->view->detailReports = $detailReports;

        $this->view->deptID          = $deptID;
        $this->view->wholeID         = $hisdatelist;
        $this->view->curWholeReport  = $wholeInfo;
        //左侧选中标识，传当前方法名字
        $this->view->selected    = $staticType;

        $this->view->depts = $this->loadModel('dept')->getDeptByOrder();
        $this->view->searchdepts = $this->secondmonthreport->getFrontShowDept(1);

        $this->view->title       = $this->lang->secondmonthreport->workload;
        $this->view->detailTitle = $this->lang->secondmonthreport->workload .' | '. $str;
        $this->view->isExecutive = 'admin' == $this->app->user->account || $this->secondmonthreport->isExecutive($this->app->user->account);
        $this->view->topmenukey = 'secondmonthreport';
        $this->display();
    }



    public function workloadExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName' => $this->lang->secondmonthreport->deptName,
                'secondproblem'         => $this->lang->secondmonthreport->workloadShowTypeList['secondproblem'],
                'seconddemand'         => $this->lang->secondmonthreport->workloadShowTypeList['seconddemand'],
                'secondorder'         => $this->lang->secondmonthreport->workloadShowTypeList['secondorder'],
                'secondcustom'         => $this->lang->secondmonthreport->workloadShowTypeList['secondcustom'],
                'countPeopleMonth'          => $this->lang->secondmonthreport->countPeopleMonth,
            ];

            $depts          = $this->loadModel('dept')->getDeptByOrder();
            $rows = [];

            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'secondproblem'         => $detailReport->detail->secondproblem,
                        'seconddemand'         => $detailReport->detail->seconddemand,
                        'secondorder'         => $detailReport->detail->secondorder,
                        'secondcustom'         => $detailReport->detail->secondcustom,
                        'countPeopleMonth'      => $detailReport->detail->countPeopleMonth

                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);

                $dataList = $this->secondmonthreport->getWorkloadDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->workloadStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'secondproblem'         => $detailReport->detail->secondproblem,
                        'seconddemand'         => $detailReport->detail->seconddemand,
                        'secondorder'         => $detailReport->detail->secondorder,
                        'secondcustom'         => $detailReport->detail->secondcustom,
                        'countPeopleMonth'      => $detailReport->detail->countPeopleMonth

                    ];
                }
            }

            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'secondproblem'     => array_sum(array_column($rows, 'secondproblem')),
                'seconddemand'     => array_sum(array_column($rows, 'seconddemand')),
                'secondorder'     => array_sum(array_column($rows, 'secondorder')),
                'secondcustom'     => array_sum(array_column($rows, 'secondcustom')),
                'countPeopleMonth'      => array_sum(array_column($rows, 'countPeopleMonth'))

            ];
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));
            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'workload');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);

        $this->view->customExport    = false;
        $this->display();
    }


    public function supportExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'   => $this->lang->secondmonthreport->deptName,
                'supporta1'  => $this->lang->secondmonthreport->supportShowStypeList['supporta1'],
                'supporta3'  => $this->lang->secondmonthreport->supportShowStypeList['supporta3'],
                'supporta4'  => $this->lang->secondmonthreport->supportShowStypeList['supporta4'],
                'supporta5'  => $this->lang->secondmonthreport->supportShowStypeList['supporta5'],
                'supporta6'  => $this->lang->secondmonthreport->supportShowStypeList['supporta6'],
                'supporta7'  => $this->lang->secondmonthreport->supportShowStypeList['supporta7'],
                'supporta8'  => $this->lang->secondmonthreport->supportShowStypeList['supporta8'],
                'supporta11' => $this->lang->secondmonthreport->supportShowStypeList['supporta11'],
                'supporta12' => $this->lang->secondmonthreport->supportShowStypeList['supporta12'],
                'total'      => $this->lang->secondmonthreport->total,
            ];

            $depts         = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];

            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[] = [
                        'deptName' => zget($depts, $detailReport->deptID),
                        'supporta1'       => $detailReport->detail->supporta1,
                        'supporta3'       => $detailReport->detail->supporta3,
                        'supporta4'       => $detailReport->detail->supporta4,
                        'supporta5'       => $detailReport->detail->supporta5,
                        'supporta6'       => $detailReport->detail->supporta6,
                        'supporta7'       => $detailReport->detail->supporta7,
                        'supporta8'       => $detailReport->detail->supporta8,
                        'supporta11'      => $detailReport->detail->supporta11,
                        'supporta12'      => $detailReport->detail->supporta12,
                        'total'    => $detailReport->detail->total
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);

                $dataList = $this->secondmonthreport->getSupportDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->supportStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[] = [
                        'deptName' => zget($depts, $detailReport->deptID),
                        'supporta1'       => $detailReport->detail->supporta1,
                        'supporta3'       => $detailReport->detail->supporta3,
                        'supporta4'       => $detailReport->detail->supporta4,
                        'supporta5'       => $detailReport->detail->supporta5,
                        'supporta6'       => $detailReport->detail->supporta6,
                        'supporta7'       => $detailReport->detail->supporta7,
                        'supporta8'       => $detailReport->detail->supporta8,
                        'supporta11'      => $detailReport->detail->supporta11,
                        'supporta12'      => $detailReport->detail->supporta12,
                        'total'    => $detailReport->detail->total
                    ];
                }
            }

            $row = [
                'deptName' => $this->lang->secondmonthreport->total,
                'supporta1'       => array_sum(array_column($rows, 'supporta1')),
                'supporta3'       => array_sum(array_column($rows, 'supporta3')),
                'supporta4'       => array_sum(array_column($rows, 'supporta4')),
                'supporta5'       => array_sum(array_column($rows, 'supporta5')),
                'supporta6'       => array_sum(array_column($rows, 'supporta6')),
                'supporta7'       => array_sum(array_column($rows, 'supporta7')),
                'supporta8'       => array_sum(array_column($rows, 'supporta8')),
                'supporta11'      => array_sum(array_column($rows, 'supporta11')),
                'supporta12'      => array_sum(array_column($rows, 'supporta12')),
                'total'    => array_sum(array_column($rows, 'total')),

            ];
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));

            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'support');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);

        $this->view->customExport    = false;
        $this->display();
    }

    public function modifyabnormalExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName' => $this->lang->secondmonthreport->deptName,
                'abnormalNum'         => $this->lang->secondmonthreport->abnormalNum,
                'modifyCountNum'        => $this->lang->secondmonthreport->modifyCountNum,
                'banormalrate'        => $this->lang->secondmonthreport->banormalrate,
            ];

            $depts          = $this->loadModel('dept')->getDeptByOrder();
            $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');


            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[] = [
                        'deptName'        => zget($depts, $detailReport->deptID),
                        'abnormalNum'        => $detailReport->detail->abnormalNum,
                        'modifyCountNum'        => $detailReport->detail->modifyCountNum,
                        'banormalrate'   => $detailReport->detail->banormalrate.'%'
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $dataList = $this->loadModel('secondmonthreport')->getModifyFinishData($actionChangeDate['start'],$actionChangeDate['end'],$deptID);
                //$dataList['modify'] = $this->secondmonthreport->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
                //$dataList['modifycncc'] = $this->secondmonthreport->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
                //$dataList['credit'] = $this->secondmonthreport->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->modifyabnormalStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[] = [
                        'deptName'        => zget($depts, $detailReport->deptID),
                        'abnormalNum'        => $detailReport->detail->abnormalNum,
                        'modifyCountNum'        => $detailReport->detail->modifyCountNum,
                        'banormalrate'   => $detailReport->detail->banormalrate.'%'
                    ];
                }
            }

            $row = [
                'deptName'        => $this->lang->secondmonthreport->total,
                'abnormalNum' => array_sum(array_column($rows, 'abnormalNum')),
                'modifyCountNum' => array_sum(array_column($rows, 'modifyCountNum')),
            ];
            $row['banormalrate'] = $row['modifyCountNum'] > 0 ? sprintf("%0.2f",($row['abnormalNum']/$row['modifyCountNum'])*100).'%' : '0.00%';
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));
            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'modifyabnormal');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);

        $this->view->customExport    = false;
        $this->display();
    }

    public function modifywholeExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }
            $this->loadModel('file');
            $fields = [
                'deptName'        => $this->lang->secondmonthreport->deptName,
                'modifyandcncca1' => $this->lang->secondmonthreport->modifyShowmodeList['modifyandcncca1'],
                'modifyandcncca2' => $this->lang->secondmonthreport->modifyShowmodeList['modifyandcncca2'],
                'modifyandcncca3' => $this->lang->secondmonthreport->modifyShowmodeList['modifyandcncca3'],
                'modifyandcncca4' => $this->lang->secondmonthreport->modifyShowmodeList['modifyandcncca4'],
                'total'           => $this->lang->secondmonthreport->total,


            ];

            $depts          = $this->loadModel('dept')->getDeptByOrder();


            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[] = [
                        'deptName'        => zget($depts, $detailReport->deptID),
                        'modifyandcncca1' => $detailReport->detail->modifyandcncca1,
                        'modifyandcncca2' => $detailReport->detail->modifyandcncca2,
                        'modifyandcncca3' => $detailReport->detail->modifyandcncca3,
                        'modifyandcncca4' => $detailReport->detail->modifyandcncca4,
                        'total'           => $detailReport->detail->total,


                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
                $dataList = [];
                $dataList['modify'] = $this->secondmonthreport->getModifyDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
                $dataList['modifycncc'] = $this->secondmonthreport->getModifycnccDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);
                $dataList['credit'] = $this->secondmonthreport->getCreditDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->modifywholeStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[] = [
                        'deptName'        => zget($depts, $detailReport->deptID),
                        'modifyandcncca1' => $detailReport->detail->modifyandcncca1,
                        'modifyandcncca2' => $detailReport->detail->modifyandcncca2,
                        'modifyandcncca3' => $detailReport->detail->modifyandcncca3,
                        'modifyandcncca4' => $detailReport->detail->modifyandcncca4,
                        'total'           => $detailReport->detail->total,

                    ];
                }
            }

            $row = [
                'deptName'        => $this->lang->secondmonthreport->total,
                'modifyandcncca1' => array_sum(array_column($rows, 'modifyandcncca1')),
                'modifyandcncca2' => array_sum(array_column($rows, 'modifyandcncca2')),
                'modifyandcncca3' => array_sum(array_column($rows, 'modifyandcncca3')),
                'modifyandcncca4' => array_sum(array_column($rows, 'modifyandcncca4')),
                'total'           => array_sum(array_column($rows, 'total')),


            ];
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));
            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'modifywhole');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);

        $this->view->customExport    = false;
        $this->display();
    }
    public function secondorderacceptExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        if ($_POST) {
            if($deptID == '_1'){
                $deptID = -1;
            }

            $this->loadModel('file');
            $this->app->loadLang('secondorder');
            $fields = [
                'deptName' => $this->lang->secondmonthreport->deptName,
                'backed'         => $this->lang->secondmonthreport->secondorderMapStatusUseShowList['backed'],
                'towaitfinish'      => $this->lang->secondmonthreport->secondorderMapStatusUseShowList['towaitfinish'],
                'solved'          => $this->lang->secondmonthreport->secondorderMapStatusUseShowList['solved'],
                'total'          => $this->lang->secondmonthreport->total,
            ];

            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];

            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'backed'         => $detailReport->detail->backed,
                        'towaitfinish'      => $detailReport->detail->towaitfinish,
                        'solved'            => $detailReport->detail->solved,
                        'total'        => $detailReport->detail->total
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);

                $dataList = $this->secondmonthreport->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->secondorderacceptStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'backed'         => $detailReport->detail->backed,
                        'towaitfinish'      => $detailReport->detail->towaitfinish,
                        'solved'            => $detailReport->detail->solved,
                        'total'        => $detailReport->detail->total
                    ];
                }
            }
            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'backed'     => array_sum(array_column($rows, 'backed')),
                'towaitfinish'      => array_sum(array_column($rows, 'towaitfinish')),
                'solved'        => array_sum(array_column($rows, 'solved')),
                'total'       => array_sum(array_column($rows, 'total')),

            ];
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));
            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'secondorderaccept');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);
        $this->view->customExport    = false;

        $this->display();
    }
    public function secondorderclassExport($searchtype,$wholeID,$deptID,$realstarttime,$realendtime,$staticType,$isuseHisData){
        if ($_POST) {
            $this->loadModel('file');

            $fields = [
                'deptName' => $this->lang->secondmonthreport->deptName,
                'consult'     => $this->lang->secondmonthreport->secondorderTypeList['consult'],
                'plan'        => $this->lang->secondmonthreport->secondorderTypeList['plan'],
                'test'        => $this->lang->secondmonthreport->secondorderTypeList['test'],
                'script'      => $this->lang->secondmonthreport->secondorderTypeList['script'],
                'support'     => $this->lang->secondmonthreport->secondorderTypeList['support'],
                'other'       => $this->lang->secondmonthreport->secondorderTypeList['other'],
                'total'       => $this->lang->secondmonthreport->total,
            ];

            $depts          = $this->loadModel('dept')->getDeptByOrder();

            $rows = [];
            if($searchtype == 'history'){
                $detailReports = $this->secondmonthreport->getOrderDataList($wholeID, $deptID,'deptID');
                foreach ($detailReports as $detailReport) {
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'consult'     => $detailReport->detail->consult,
                        'plan'        => $detailReport->detail->plan,
                        'test'        => $detailReport->detail->test,
                        'script'      => $detailReport->detail->script,
                        'support'     => $detailReport->detail->support,
                        'other'        => $detailReport->detail->other,
                        'total'        => $detailReport->detail->total,
                    ];
                }
            }else{
                $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);

                $dataList = $this->secondmonthreport->getSecondorderDataList($actionChangeDate['start'],$actionChangeDate['end'],$deptID,$staticType,$isuseHisData);

                $dataList = $this->secondmonthreport->secondorderclassStatic($dataList,$deptID);
                $dataList = $this->secondmonthreport->DataToReportFormat($dataList['staticdata'],$staticType);
                $dataList = $this->secondmonthreport->getOrderRealTimeDataList($dataList);

                foreach ($dataList as $detailReport){
                    $rows[]               = [
                        'deptName'     => zget($depts, $detailReport->deptID),
                        'consult'     => $detailReport->detail->consult,
                        'plan'        => $detailReport->detail->plan,
                        'test'        => $detailReport->detail->test,
                        'script'      => $detailReport->detail->script,
                        'support'        => $detailReport->detail->support,
                        'other'        => $detailReport->detail->other,
                        'total'        => $detailReport->detail->total,
                    ];
                }
            }

            $row = [
                'deptName'     => $this->lang->secondmonthreport->total,
                'consult'     => array_sum(array_column($rows, 'consult')),
                'plan'        => array_sum(array_column($rows, 'plan')),
                'test'        => array_sum(array_column($rows, 'test')),
                'script'      => array_sum(array_column($rows, 'script')),
                'support'     => array_sum(array_column($rows, 'support')),
                'other'       => array_sum(array_column($rows, 'other')),
                'total'       => array_sum(array_column($rows, 'total')),
            ];
            $rows[]            = $row;
            $rows              = json_decode(json_encode($rows));
            $this->post->set('fields', $fields);
            $this->post->set('rows', $rows);
            $this->post->set('kind', 'secondorderclass');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        if($searchtype == 'history'){
            $info = $this->secondmonthreport->getWholeReportByID($wholeID);
        }else{
            $actionChangeDate = $this->secondmonthreport->actionDateToSecond($realstarttime,$realendtime);
            $info = new stdClass();
            $info->startday = $actionChangeDate['oldstart'];
            $info->endday = $actionChangeDate['oldend'];
        }

        $this->view->fileName        = $this->secondmonthreport->getExportFileName($searchtype,$staticType,'',$info->startday,$info->endday);

        $this->view->customExport    = false;
        $this->display();
    }

    public function ajaxSearchDeptList()
    {
        $stype = isset($_POST['stype']) ? $_POST['stype'] : '';
        $deptList   = $this->secondmonthreport->getNeedShowDept(0, $stype == 'hisquarter');
        $deptParent = $this->secondmonthreport->loadModel('dept')->getDeptAndChild();

        foreach ($deptParent as $key => $value) {
            if (!in_array($key, $deptList)) {
                unset($deptParent[$key]);
            }
        }
        $deptIds = array_unique(array_values($deptParent));
        $depts = $this->dao->select('id,name')->from(TABLE_DEPT)->where('id')->in($deptIds)->fetchPairs();

        $depts = ['0' => '全部'] + $depts;
        if(isset($deptIds['-1'])){
            $depts = $depts + ['-1' => '空'] ;
        }

        die(html::select('deptID', $depts, '0', "class='form-control chosen' "));;
    }

}
