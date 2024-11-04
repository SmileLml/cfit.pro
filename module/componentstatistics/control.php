<?php
class componentstatistics extends control
{
    public function publicComponentList($filterdate='lastsixmonth'){
        if(isset($_POST['filterkey'])){
            $filterdate = $_POST['filterkey'];
        }
        if(isset($_POST['searchtype'])){
            $searchtype = $_POST['searchtype'];
        }else{
            $searchtype = 1;
        }


        $tempFilterDate = $this->componentstatistics->getFilterDate($filterdate);
        $filterDateTypeFlag = false;
        if(!isset($_POST['startTime'])){
            $startTime = $tempFilterDate['startTime'];
        }else{
            $startTime = $_POST['startTime'];
            if($tempFilterDate['startTime'] != $_POST['startTime']){
                $filterDateTypeFlag = true;
            }
        }
        if(!isset($_POST['endTime'])){
            $endTime = $tempFilterDate['endTime'];
        }else{
            $endTime = $_POST['endTime'];
            if($tempFilterDate['endTime'] != $_POST['endTime']){
                $filterDateTypeFlag = true;
            }
        }
        if($filterDateTypeFlag){
            $filterdate = 'custom';
        }

        $this->app->loadLang('component');



        $result = $this->loadModel('componentstatistics')->publicComponentList($startTime,$endTime,$searchtype);
        $this->view->hejiList = array_pop($result['dataList']);
        $this->view->dataList = $result['dataList'];
        $this->view->monthsList = $result['monthsList'];


        $this->view->filterDate = ['startTime'=>$startTime,'endTime'=>$endTime];
        $this->view->selectfilterkey = $filterdate;


        $fiterDateList = [];
        foreach ($this->lang->componentstatistics->filterDate as $filterkey=>$val){
            $fiterDateList[$filterkey] = $this->componentstatistics->getFilterDate($filterkey);
        }

        $this->view->fiterDateList = $fiterDateList;
        $this->view->searchtype = $searchtype;
        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->componentstatistics->publicComponentList;

        $this->display();

    }


    public function exportPublicComponentStatistics(){

        $params = json_decode($this->session->componentstatisticsQueryCondition,true);
        if($_POST){


            $result = $this->componentstatistics->publicComponentList($params['startTime'],$params['endTime'],$params['searchtype'],1);

            $fields = [];
            if($params['searchtype'] == $this->lang->componentstatistics->judgeTypeList[1]){
                $fields['maintainerDept'] = $this->lang->componentstatistics->maintainerDept;
            }else{
                $fields['category'] = $this->lang->componentstatistics->category;
            }
            foreach ($result['monthsList'] as $month){
                $fields[$month] = $month;
            }
            $fields[$this->lang->componentstatistics->heji] = $this->lang->componentstatistics->heji;
            $depts      = $this->loadModel('dept')->getOptionMenu();
            $this->app->loadLang('component');
            $exportdataList = [];
            $i=0;
            foreach($result['dataList'] as $key=>$data){
                $heji = array_sum($data);
                if($params['searchtype'] == $this->lang->componentstatistics->judgeTypeList[1]){
                    $exportdataList[$i]['maintainerDept'] = zget($depts,$key);
                }else{
                    $exportdataList[$i]['category'] = zget($this->lang->component->categoryList,$key);
                }

                $exportdataList[$i] = array_merge($exportdataList[$i],$data);
                $exportdataList[$i][$this->lang->componentstatistics->heji] = $heji;

                $exportdataList[$i] = (object)$exportdataList[$i];

                $i++;

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportdataList);
            $this->post->set('kind', 'componentstatistics');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);




        }
        $this->view->fileName        = $this->lang->componentstatistics->exportPublicComponentStatisticsName.' '.$params['startTime'].'~'.$params['endTime'];
        $this->display('componentstatistics','export');
    }






    // 组件使用数量统计
    public function usedComponentList($filterdate = 'thisyear'){
        if(isset($_POST['filterkey'])){
            $filterdate = $_POST['filterkey'];
        }
        $tempFilterDate = $this->componentstatistics->getQuarterDate($filterdate);
        $filterDateTypeFlag = false;
        if(!isset($_POST['startTime'])){
            $startTime = $tempFilterDate['startTime'];
        }else{
            $startTime = $_POST['startTime'];
            if($tempFilterDate['startTime'] != $_POST['startTime']){
                $filterDateTypeFlag = true;
            }
        }
        if(!isset($_POST['endTime'])){
            $endTime = $tempFilterDate['endTime'];
        }else{
            $endTime = $_POST['endTime'];
            if($tempFilterDate['endTime'] != $_POST['endTime']){
                $filterDateTypeFlag = true;
            }
        }

        if(!isset($_POST['endTime'])){
            $startQuarter = $tempFilterDate['startQuarter'];
        }else{
            $startQuarter = $_POST['startQuarter'];
            if($tempFilterDate['startQuarter'] != $_POST['startQuarter']){
                $filterDateTypeFlag = true;
            }
        }

        if(!isset($_POST['endTime'])){
            $endQuarter = $tempFilterDate['endQuarter'];
        }else{
            $endQuarter = $_POST['endQuarter'];
            if($tempFilterDate['endQuarter'] != $_POST['endQuarter']){
                $filterDateTypeFlag = true;
            }
        }

        if($filterDateTypeFlag){
            $filterdate = 'custom';
        }

        $res = $this->componentstatistics->getComponentAccountList($startTime,$endTime,$startQuarter,$endQuarter);

        $this->view->filterDate = ['startTime'=>$startTime,'endTime'=>$endTime,'startQuarter'=>$startQuarter,'endQuarter'=>$endQuarter];

        $fiterDateList = [];
        foreach ($this->lang->componentstatistics->quartersDate as $filterkey=>$val){
            $fiterDateList[$filterkey] = $this->componentstatistics->getQuarterDate($filterkey);
        }

        $this->view->title         = $this->lang->componentstatistics->usedComponentList;
        $this->view->fiterDateList = $fiterDateList;

        $this->view->hejiList = array_pop($res['data']);
        $this->view->data          = $res['data'];
        $this->view->numlist       = $res['list'];
        // 组件
        $componentNames = $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)->where('deleted')->eq('0')->orderBy('id_desc')->fetchPairs();
        $this->view->componentNames       = array('' => '') + $componentNames;
        $this->view->selectfilterkey      = $filterdate;
        $this->display();
    }

    // 第三方组件-组件清单数量统计
    public function thirdComponentList($filterdate='lastsixmonth'){
        if(isset($_POST['filterkey'])){
            $filterdate = $_POST['filterkey'];
        }

        $tempFilterDate = $this->componentstatistics->getFilterDate($filterdate);
        $filterDateTypeFlag = false;
        /*if(!isset($_POST['startTime'])){
            $startTime = $tempFilterDate['startTime'];
        }else{
            $startTime = $_POST['startTime'];
        }
        if(!isset($_POST['endTime'])){
            $endTime = $tempFilterDate['endTime'];
        }else{
            $endTime = $_POST['endTime'];
        }*/
        if(!isset($_POST['startTime'])){
            $startTime = $tempFilterDate['startTime'];
        }else{
            $startTime = $_POST['startTime'];
            if($tempFilterDate['startTime'] != $_POST['startTime']){
                $filterDateTypeFlag = true;
            }
        }
        if(!isset($_POST['endTime'])){
            $endTime = $tempFilterDate['endTime'];
        }else{
            $endTime = $_POST['endTime'];
            if($tempFilterDate['endTime'] != $_POST['endTime']){
                $filterDateTypeFlag = true;
            }
        }
        if($filterDateTypeFlag){
            $filterdate = 'custom';
        }

        $this->app->loadLang('component');

        $result = $this->loadModel('componentstatistics')->thirdComponentList($startTime, $endTime);
        $this->view->hejiList = array_pop($result['dataList']);
        $this->view->dataList = $result['dataList'];
        $this->view->monthsList = $result['monthsList'];

        $this->view->filterDate = ['startTime'=>$startTime,'endTime'=>$endTime];
        $this->view->selectfilterkey = $filterdate;

        $fiterDateList = [];
        foreach ($this->lang->componentstatistics->filterDate as $filterkey=>$val){
            $fiterDateList[$filterkey] = $this->componentstatistics->getFilterDate($filterkey);
        }

        $this->view->title         = $this->lang->componentstatistics->thirdComponentList;
        $this->view->fiterDateList = $fiterDateList;
        $this->view->depts         = $this->loadModel('dept')->getOptionMenu();
        $this->display();
    }


    /**
     * 公共技术组件-组件引入评估统计
     * @param $filterdate
     * @return void
     */
    public function publicComponentIntroduceList($filterdate='lastsixmonth'){
        if(isset($_POST['filterkey'])){
            $filterdate = $_POST['filterkey'];
        }



        $tempFilterDate = $this->componentstatistics->getFilterDate($filterdate);
        $filterDateTypeFlag = false;
        if(!isset($_POST['startTime'])){
            $startTime = $tempFilterDate['startTime'];
        }else{
            $startTime = $_POST['startTime'];
            if($tempFilterDate['startTime'] != $_POST['startTime']){
                $filterDateTypeFlag = true;
            }
        }
        if(!isset($_POST['endTime'])){
            $endTime = $tempFilterDate['endTime'];
        }else{
            $endTime = $_POST['endTime'];
            if($tempFilterDate['endTime'] != $_POST['endTime']){
                $filterDateTypeFlag = true;
            }
        }
        if($filterDateTypeFlag){
            $filterdate = 'custom';
        }

        $this->app->loadLang('component');



        $result = $this->loadModel('componentstatistics')->publicComponentIntroduceList($startTime,$endTime);
        $this->view->hejiList = array_pop($result['dataList']);
        $this->view->dataList = $result['dataList'];
        $this->view->monthsList = $result['monthsList'];


        $this->view->filterDate = ['startTime'=>$startTime,'endTime'=>$endTime];
        $this->view->selectfilterkey = $filterdate;


        $fiterDateList = [];
        foreach ($this->lang->componentstatistics->filterDate as $filterkey=>$val){
            $fiterDateList[$filterkey] = $this->componentstatistics->getFilterDate($filterkey);
        }

        $this->view->fiterDateList = $fiterDateList;

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->componentstatistics->publicComponentIntroduceList;

        $this->display();

    }

    /**
     * 导出公共技术组件-组件引入评估统计

     * @return void
     */
    public function exportPublicComponentIntroduceList(){
        $params = json_decode($this->session->componentstatisticsIntroduceQueryCondition,true);

        if($_POST){

            $result = $this->componentstatistics->publicComponentIntroduceList($params['startTime'],$params['endTime'],1);

            $fields = [];
            $fields['examineVerdict'] = $this->lang->componentstatistics->examineVerdict;
            foreach ($result['monthsList'] as $month){
                $fields[$month] = $month;
            }
            $fields[$this->lang->componentstatistics->heji] = $this->lang->componentstatistics->heji;

            $this->app->loadLang('component');
            $exportdataList = [];
            $i=0;
            foreach($result['dataList'] as $key=>$data){
                $heji = array_sum($data);
                $exportdataList[$i]['examineVerdict'] = zget($this->lang->componentstatistics->columnkeylable,$key);

                $exportdataList[$i] = array_merge($exportdataList[$i],$data);
                $exportdataList[$i][$this->lang->componentstatistics->heji] = $heji;

                $exportdataList[$i] = (object)$exportdataList[$i];

                $i++;

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportdataList);
            $this->post->set('kind', 'componentstatistics');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);




        }
        $this->view->fileName        = $this->lang->componentstatistics->exportPublicComponentIntroduceList.' '.$params['startTime'].'~'.$params['endTime'];
        $this->display('componentstatistics','export');

    }


    /**
     * 第三方组件-组件引入评估统计
     * @param $filterdate
     * @return void
     */
    public function thirdpartyComponentIntroduceList($filterdate='lastsixmonth'){
        if(isset($_POST['filterkey'])){
            $filterdate = $_POST['filterkey'];
        }


        $tempFilterDate = $this->componentstatistics->getFilterDate($filterdate);
        $filterDateTypeFlag = false;
        if(!isset($_POST['startTime'])){
            $startTime = $tempFilterDate['startTime'];
        }else{
            $startTime = $_POST['startTime'];
            if($tempFilterDate['startTime'] != $_POST['startTime']){
                $filterDateTypeFlag = true;
            }
        }
        if(!isset($_POST['endTime'])){
            $endTime = $tempFilterDate['endTime'];
        }else{
            $endTime = $_POST['endTime'];
            if($tempFilterDate['endTime'] != $_POST['endTime']){
                $filterDateTypeFlag = true;
            }
        }
        if($filterDateTypeFlag){
            $filterdate = 'custom';
        }

        $this->app->loadLang('component');



        $result = $this->loadModel('componentstatistics')->thirdpartyComponentIntroduceList($startTime,$endTime);
        $this->view->hejiList = array_pop($result['dataList']);
        $this->view->dataList = $result['dataList'];
        $this->view->monthsList = $result['monthsList'];


        $this->view->filterDate = ['startTime'=>$startTime,'endTime'=>$endTime];
        $this->view->selectfilterkey = $filterdate;


        $fiterDateList = [];
        foreach ($this->lang->componentstatistics->filterDate as $filterkey=>$val){
            $fiterDateList[$filterkey] = $this->componentstatistics->getFilterDate($filterkey);
        }

        $this->view->fiterDateList = $fiterDateList;

        $this->view->depts = $this->loadModel('dept')->getOptionMenu();
        $this->view->title = $this->lang->componentstatistics->thirdpartyComponentIntroduceList;

        $this->display();

    }

    /**
     * 导出第三方组件-组件引入评估统计

     * @return void
     */
    public function exportThirdpartyComponentIntroduceList(){
        $params = json_decode($this->session->componentstatisticsThirdpartyIntroduceQueryCondition,true);

        if($_POST){


            $result = $this->componentstatistics->thirdpartyComponentIntroduceList($params['startTime'],$params['endTime'],1);

            $fields = [];
            $fields['examineVerdict'] = $this->lang->componentstatistics->examineVerdict;
            foreach ($result['monthsList'] as $month){
                $fields[$month] = $month;
            }
            $fields[$this->lang->componentstatistics->heji] = $this->lang->componentstatistics->heji;

            $this->app->loadLang('component');
            $exportdataList = [];
            $i=0;
            foreach($result['dataList'] as $key=>$data){
                $heji = array_sum($data);
                $exportdataList[$i]['examineVerdict'] = zget($this->lang->componentstatistics->thirdcolumnkeylable,$key);

                $exportdataList[$i] = array_merge($exportdataList[$i],$data);
                $exportdataList[$i][$this->lang->componentstatistics->heji] = $heji;

                $exportdataList[$i] = (object)$exportdataList[$i];

                $i++;

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportdataList);
            $this->post->set('kind', 'componentstatistics');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);




        }
        $this->view->fileName        = $this->lang->componentstatistics->exportThirdpartyComponentIntroduceList.' '.$params['startTime'].'~'.$params['endTime'];
        $this->display('componentstatistics','export');

    }
    //导出第三方组件-组件清单数量统计
    public function exportThirdComponentStatistics(){

        $params = json_decode($this->session->thridComponentstatisticsQueryCondition,true);

        if($_POST){


            $result = $this->componentstatistics->thirdComponentList($params['startTime'],$params['endTime'],1);

            $fields = [];
            $fields['category'] = $this->lang->componentstatistics->category;
            foreach ($result['monthsList'] as $month){
                $fields[$month] = $month;
            }
            $fields[$this->lang->componentstatistics->heji] = $this->lang->componentstatistics->heji;
            $depts      = $this->loadModel('dept')->getOptionMenu();
            $this->app->loadLang('component');
            $exportdataList = [];
            $i=0;
            foreach($result['dataList'] as $key=>$data){
                $heji = array_sum($data);
                $exportdataList[$i]['category'] = zget($this->lang->component->thirdcategoryList,$key);

                $exportdataList[$i] = array_merge($exportdataList[$i],$data);
                $exportdataList[$i][$this->lang->componentstatistics->heji] = $heji;

                $exportdataList[$i] = (object)$exportdataList[$i];

                $i++;

            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportdataList);
            $this->post->set('kind', 'componentstatistics');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);




        }
        $this->view->fileName        = $this->lang->componentstatistics->exportThirdComponentList.' '.$params['startTime'].'~'.$params['endTime'];
        $this->display('componentstatistics','export');
    }

    // 导出公共技术组件-组件使用数量统计
    public function exportUsedComponentList (){
        $params = json_decode($this->session->componentstatisticsQueryQuarterCondition,true);
        if($_POST){
            $result = $this->componentstatistics->getComponentAccountList($params['startTime'], $params['endTime'],$params['startQuarter'],$params['endQuarter'], 1);

            $fields = [];
            $fields['componentName'] = $this->lang->componentstatistics->componentName;
            foreach ($result['list'] as $quarter){
                $fields[$quarter] = $quarter;
            }
            $fields[$this->lang->componentstatistics->heji] = $this->lang->componentstatistics->heji;
            $this->app->loadLang('component');
            $exportdataList = [];
            $i=0;
            // 组件
            $componentNames = $this->dao->select('id,name')->from(TABLE_COMPONENT_RELEASE)->where('deleted')->eq(0)->orderBy('id_desc')->fetchPairs();
            foreach($result['data'] as $key=>$data){
                a($key);
                $heji = array_sum($data);
                $exportdataList[$i]['componentName'] = zget($componentNames,$key);

                $exportdataList[$i] = array_merge($exportdataList[$i],$data);
                $exportdataList[$i][$this->lang->componentstatistics->heji] = $heji;

                $exportdataList[$i] = (object)$exportdataList[$i];

                $i++;
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $exportdataList);
            $this->post->set('kind', 'componentstatistics');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }
        $this->view->fileName        = $this->lang->componentstatistics->exportUsedComponentList.' '.$params['startTime'].'-'.$params['startQuarter'].'~'.$params['endTime'].'-'.$params['endQuarter'];
        $this->display('componentstatistics','export');
    }


















































}