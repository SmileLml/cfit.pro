<?php
class componentstatisticsModel extends model
{

    public function publicComponentList($startTime,$endTime,$searchtype,$isexport=0){

        if($isexport == 0){
            $exportUseParams = [
                'startTime'=>$startTime,
                'endTime'=>$endTime,
                'searchtype'=>$searchtype,
            ];

            $this->session->set('componentstatisticsQueryCondition', json_encode($exportUseParams), $this->app->openApp);
        }
        $allMonthsNum = $this->getMonthDiff($startTime,$endTime);

        $whereStartTime = $startTime.'-01 00:00:00';
        $monthDays = date('t',strtotime($endTime));
        $whereEndTime = date("Y-m-{$monthDays} 23:59:59",strtotime($endTime));

        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT_RELEASE)
            ->where("type")->eq('public')
            ->andWhere('deleted')->eq(0)
            ->andWhere('createTime')->ge($whereStartTime)
            ->andWhere('createTime')->le($whereEndTime)
            ->orderBy("createTime_desc")->fetchAll();

        $dataList = [];
        $heji = [];
        if($searchtype == $this->lang->componentstatistics->judgeTypeList[1]){
            foreach ($componentList as $component){
//            $publishdate = date("Ym",strtotime($component->createTime));
                $publishdate = substr($component->createTime,0,7);
                if(isset($dataList[$component->maintainerDept][$publishdate])){
                    $dataList[$component->maintainerDept][$publishdate]++;

                }else{
                    $dataList[$component->maintainerDept][$publishdate] = 1;
                }

                if(isset($heji[$publishdate])){
                    $heji[$publishdate]++;
                }else{
                    $heji[$publishdate] = 1;
                }

            }
        }else{
            foreach ($componentList as $component){
//            $publishdate = date("Ym",strtotime($component->createTime));
                $publishdate = substr($component->createTime,0,7);
                if(isset($dataList[$component->category][$publishdate])){
                    $dataList[$component->category][$publishdate]++;

                }else{
                    $dataList[$component->category][$publishdate] = 1;
                }

                if(isset($heji[$publishdate])){
                    $heji[$publishdate]++;
                }else{
                    $heji[$publishdate] = 1;
                }


            }
        }

        $dataList['合计'] = $heji;
        $monthList = $this->getMonthList($allMonthsNum,$endTime);
        sort($monthList);
        foreach ($monthList as $month){
            foreach ($dataList as $key=>$data){

                if(!isset($data[$month])){
                    $dataList[$key][$month] = 0;
                }

            }
        }

        foreach ($dataList as $key=>$data){

            ksort($data);
            $dataList[$key]= $data;


        }



//a($dataList);
        return ['dataList'=>$dataList,'monthsList'=>$monthList];



    }


    public function thirdComponentList($startTime,$endTime,$isexport=0){

        if($isexport == 0){
            $exportUseParams = [
                'startTime'=>$startTime,
                'endTime'=>$endTime
            ];

            $this->session->set('thridComponentstatisticsQueryCondition', json_encode($exportUseParams), $this->app->openApp);
        }
        $allMonthsNum = $this->getMonthDiff($startTime,$endTime);

        $whereStartTime = $startTime.'-01 00:00:00';
        $monthDays = date('t',strtotime($endTime));
        $whereEndTime = date("Y-m-{$monthDays} 23:59:59",strtotime($endTime));

        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT_RELEASE)
            ->where("type")->eq('third')
            ->andWhere('deleted')->eq(0)
            ->andWhere('createTime')->ge($whereStartTime)
            ->andWhere('createTime')->le($whereEndTime)
            ->orderBy("createTime_desc")->fetchAll();

        $dataList = [];
        $heji = [];

        foreach ($componentList as $component){
//            $publishdate = date("Ym",strtotime($component->createTime));
            $publishdate = substr($component->createTime,0,7);
            if(isset($dataList[$component->category][$publishdate])){
                $dataList[$component->category][$publishdate]++;

            }else{
                $dataList[$component->category][$publishdate] = 1;
            }

            if(isset($heji[$publishdate])){
                $heji[$publishdate]++;
            }else{
                $heji[$publishdate] = 1;
            }


        }


        $dataList['合计'] = $heji;
        $monthList = $this->getMonthList($allMonthsNum,$endTime);
        sort($monthList);
        foreach ($monthList as $month){
            foreach ($dataList as $key=>$data){

                if(!isset($data[$month])){
                    $dataList[$key][$month] = 0;
                }

            }
        }

        foreach ($dataList as $key=>$data){

            ksort($data);
            $dataList[$key]= $data;


        }



//a($dataList);
        return ['dataList'=>$dataList,'monthsList'=>$monthList];



    }


    public function publicComponentIntroduceList($startTime,$endTime,$isexport=0){

        if($isexport == 0){
            $exportUseParams = [
                'startTime'=>$startTime,
                'endTime'=>$endTime,

            ];

            $this->session->set('componentstatisticsIntroduceQueryCondition', json_encode($exportUseParams), $this->app->openApp);
        }
        $allMonthsNum = $this->getMonthDiff($startTime,$endTime);

        $whereStartTime = $startTime.'-01 00:00:00';
        $monthDays = date('t',strtotime($endTime));
        $whereEndTime = date("Y-m-{$monthDays} 23:59:59",strtotime($endTime));

        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT)
            ->where("type")->eq('public')
            ->andWhere('deleted')->eq(0)
            ->andWhere('status')->in("published,reject,incorporate")
            ->andWhere('finalstatetime')->ge($whereStartTime)
            ->andWhere('finalstatetime')->le($whereEndTime)
            ->fetchAll();


//        $dataList = [];
        $heji = [];

        $dataList = [
            'published_company'=>[],
            'published_dept'=>[],
            'incorporate_company'=>[],
            'incorporate_dept'=>[],
            'reject'=>[],
        ];

        foreach ($componentList as $component){

            $finalstaedate = substr($component->finalstatetime,0,7);
            if($component->status == 'reject'){
                $statusLevel = $component->status;
            }else{
                $statusLevel = $component->status.'_'.$component->level;
            }

            if(isset($dataList[$statusLevel][$finalstaedate])){
                $dataList[$statusLevel][$finalstaedate]++;

            }else{
                $dataList[$statusLevel][$finalstaedate] = 1;
            }

            if(isset($heji[$finalstaedate])){
                $heji[$finalstaedate]++;
            }else{
                $heji[$finalstaedate] = 1;
            }


        }




        $dataList['合计'] = $heji;
        $monthList = $this->getMonthList($allMonthsNum,$endTime);
        sort($monthList);
        foreach ($monthList as $month){
            foreach ($dataList as $key=>$data){

                if(!isset($data[$month])){
                    $dataList[$key][$month] = 0;
                }

            }
        }

        foreach ($dataList as $key=>$data){

            ksort($data);
            $dataList[$key]= $data;


        }


        return ['dataList'=>$dataList,'monthsList'=>$monthList];



    }



    public function thirdpartyComponentIntroduceList($startTime,$endTime,$isexport=0){

        if($isexport == 0){
            $exportUseParams = [
                'startTime'=>$startTime,
                'endTime'=>$endTime,

            ];

            $this->session->set('componentstatisticsThirdpartyIntroduceQueryCondition', json_encode($exportUseParams), $this->app->openApp);
        }
        $allMonthsNum = $this->getMonthDiff($startTime,$endTime);

        $whereStartTime = $startTime.'-01 00:00:00';
        $monthDays = date('t',strtotime($endTime));
        $whereEndTime = date("Y-m-{$monthDays} 23:59:59",strtotime($endTime));

        $componentList = $this->dao->select("*")->from(TABLE_COMPONENT)
            ->where("type")->eq('thirdParty')
            ->andWhere('deleted')->eq(0)
            ->andWhere('status')->in("published,reject")
            ->andWhere('finalstatetime')->ge($whereStartTime)
            ->andWhere('finalstatetime')->le($whereEndTime)
            ->fetchAll();


//        $dataList = [];
        $heji = [];

        $dataList = [
            'published'=>[],
            'reject'=>[],
        ];

        foreach ($componentList as $component){

            $finalstaedate = substr($component->finalstatetime,0,7);

            $statusLevel = $component->status;


            if(isset($dataList[$statusLevel][$finalstaedate])){
                $dataList[$statusLevel][$finalstaedate]++;

            }else{
                $dataList[$statusLevel][$finalstaedate] = 1;
            }

            if(isset($heji[$finalstaedate])){
                $heji[$finalstaedate]++;
            }else{
                $heji[$finalstaedate] = 1;
            }


        }




        $dataList['合计'] = $heji;
        $monthList = $this->getMonthList($allMonthsNum,$endTime);
        sort($monthList);
        foreach ($monthList as $month){
            foreach ($dataList as $key=>$data){

                if(!isset($data[$month])){
                    $dataList[$key][$month] = 0;
                }

            }
        }

        foreach ($dataList as $key=>$data){

            ksort($data);
            $dataList[$key]= $data;


        }


        return ['dataList'=>$dataList,'monthsList'=>$monthList];



    }

    public function getMonthList($months,$endTime){
        $monthList = [];

        for($i=0;$i<=$months;$i++){
            $monthList[] = date("Y-m",strtotime("-{$i} Months",strtotime($endTime)));
        }

        return $monthList;

    }

    public function getMonthDiff($startTime,$endTime){
        $startTimeArr = explode("-",$startTime);
        $endTimeArr = explode("-",$endTime);
        $chaY = $endTimeArr[0] - $startTimeArr[0];
        $chaM = $endTimeArr[1] - $startTimeArr[1];

        return $chaY*12 + $chaM;
        /*$diffmonths = date_diff(date_create($startTime.'-01'),date_create($endTime.'-01'));
        a($diffmonths);
        return $diffmonths->y*12 + $diffmonths->m;*/

    }

    public function getFilterDate($filterKey){
        $date = [];
        if($filterKey == 'thismonth'){
            $date['startTime'] = date("Y-m");
            $date['endTime'] = date("Y-m");
        }elseif ($filterKey == 'lastmonth'){
            $date['startTime'] = date("Y-m",strtotime("-1 Months"));
            $date['endTime'] = date("Y-m",strtotime("-1 Months"));
        }elseif ($filterKey == 'lastthreemonth'){
            $date['startTime'] = date("Y-m",strtotime("-2 Months"));
            $date['endTime'] = date("Y-m");
        }elseif ($filterKey == 'lastsixmonth'){
            $date['startTime'] = date("Y-m",strtotime("-5 Months"));
            $date['endTime'] = date("Y-m");
        }elseif ($filterKey == 'thisyear'){
            $date['startTime'] = date("Y-01");
            $date['endTime'] = date("Y-m");
        }elseif ($filterKey == 'lastyear'){
            $date['startTime'] = date("Y-m",strtotime("-11 Months"));
            $date['endTime'] = date("Y-m");
        }elseif ($filterKey == 'custom'){
            $date['startTime'] = date("Y-m",strtotime("-11 Months"));
            $date['endTime'] = date("Y-m");
        }

        return $date;
    }




    // 组件使用数量统计
    public function getComponentAccountList($startTime, $endTime,$startQuarter,$endQuarter, $isexport = '0'){
        $quarterMonth = $this->getQuarterToMonth($startQuarter);



        if($isexport == 0){
            $exportUseParams = [
                'startTime'=>$startTime,
                'endTime'=>$endTime,
                'startQuarter'=>$startQuarter,
                'endQuarter'=>$endQuarter,
            ];

            $this->session->set('componentstatisticsQueryQuarterCondition', json_encode($exportUseParams), $this->app->openApp);
        }

        $whereStartTime = $startTime.$startQuarter;
        $whereEndTime = $endTime.$endQuarter;

        $res = [];$dataList = [];$list = [];$num = [];
        $components = $this->dao->select('componentId, count(1) as num, startYear, startQuarter')->from(TABLE_COMPONENT_PUBLIC_ACCOUNT)
            ->where('deleted')->eq('0')
            ->andWhere('startTime')->ge($whereStartTime)->andWhere('startTime')->le($whereEndTime)
            ->groupBy('componentId,startYear,startQuarter')
            ->orderBy('startYear,startQuarter')
            ->fetchAll();


        foreach($components as $component){
            $component->quarter = $component->startYear.'-'.$component->startQuarter;
            $connectKey = $component->startYear.'-'.$component->startQuarter;
//            $list[] = $component->startYear.'-'.$component->startQuarter;
            if(!isset($dataList[$component->componentId][$connectKey])){
                $dataList[$component->componentId][$connectKey] = $component->num;
            }
            if(isset($num[$connectKey])){
                $num[$connectKey] += $component->num;
            }else{
                $num[$connectKey] = $component->num;
            }

        }

        $quarterMonthList = $this->getQuarterList($startTime,$startQuarter,$endTime,$endQuarter);

//        $quarterMonthList = array_keys($num);

        sort($quarterMonthList);
        $dataList['合计'] = $num;
        foreach($quarterMonthList as  $quarterVal){
            foreach($dataList as  $key=>$data){
                if(!isset($data[$quarterVal])){
                    $dataList[$key][$quarterVal] = 0;
                }
            }
        }

        foreach ($dataList as $key=>$dataVal) {
            ksort($dataVal);
            $dataList[$key]= $dataVal;

        }



        $res['data'] = $dataList;
        $res['list'] = $quarterMonthList;
        return $res;
    }
    public function getQuarterList($startYear,$startQuarter,$endYear,$endQuarter){
        $quartersList = [];

        $chaYear = $endYear - $startYear;

        $chaQuarters = abs($endQuarter - $startQuarter);

        $coutQuarters = $chaYear*4 + $chaQuarters;


        for($i=0;$i<=$coutQuarters;$i++){

            $quartersList[] = $startYear.'-'.$startQuarter;

            $startQuarter+=1;
            if($startQuarter % 5 == 0){
                $startYear += 1;
                $startQuarter = 1;
            }
            if($startYear>=$endYear && $startQuarter > $endQuarter){
                break;
            }
        }

        return $quartersList;

    }
    //
    public function getQuarterDate($filterKey){
        $date = [];
        $month = date('n');

        if($filterKey == 'thisquarter'){
            if($month < 4){
                $date['startTime'] = date("Y");
                $date['endTime'] = date("Y");
                $date['startQuarter'] = 1;
                $date['endQuarter'] = 1;
            }elseif($month < 7){
                $date['startTime'] = date("Y");
                $date['endTime'] = date("Y");
                $date['startQuarter'] = 2;
                $date['endQuarter'] = 2;
            }elseif($month < 10){
                $date['startTime'] = date("Y");
                $date['endTime'] = date("Y");
                $date['startQuarter'] = 3;
                $date['endQuarter'] = 3;
            }else{
                $date['startTime'] = date("Y");
                $date['endTime'] = date("Y");
                $date['startQuarter'] = 4;
                $date['endQuarter'] = 4;
            }
        }elseif ($filterKey == 'lastquarter'){
            $quarter = $this->getMonthToQuarter($month);

            if($quarter>1){
                $date['startTime'] = date("Y");
                $date['endTime'] = date("Y");
                $date['startQuarter'] = $quarter-1;
                $date['endQuarter'] = $quarter-1;
            }else{
                $date['startTime'] = date("Y",strtotime("-1 year"));
                $date['endTime'] = date("Y",strtotime("-1 year"));
                $date['startQuarter'] = 4;
                $date['endQuarter'] = 4;

            }

        }elseif ($filterKey == 'thisyear'){
            $quarter = $this->getMonthToQuarter($month);
            $date['startTime'] = date("Y");
            $date['endTime'] = date("Y");
            $date['startQuarter'] = 1;
            $date['endQuarter'] = $quarter;
        }elseif ($filterKey == 'lastyear'){


            $date['startTime'] = date("Y-m",strtotime("-9 Months"));
            $startMonth = date("n",strtotime("-9 Months"));
            $date['endTime'] = date("Y-m");
            $quarter = $this->getMonthToQuarter($month);
            $startQuarter = $this->getMonthToQuarter($startMonth);
            if($quarter < 4){
                $date['startTime'] = date("Y",strtotime("-1 year"));
            }else{
                $date['startTime'] = date("Y");
                $startQuarter = 1;
            }

            $date['endTime'] = date("Y");
            $date['startQuarter'] = $startQuarter;
            $date['endQuarter'] = $quarter;

        }elseif ($filterKey == 'custom'){
            $date['startTime'] = date("Y-m",strtotime("-9 Months"));
            $startMonth = date("n",strtotime("-9 Months"));
            $date['endTime'] = date("Y-m");
            $quarter = $this->getMonthToQuarter($month);
            $startQuarter = $this->getMonthToQuarter($startMonth);
            if($quarter < 4){
                $date['startTime'] = date("Y",strtotime("-1 year"));
            }else{
                $date['startTime'] = date("Y");
                $startQuarter = 1;
            }

            $date['endTime'] = date("Y");
            $date['startQuarter'] = $startQuarter;
            $date['endQuarter'] = $quarter;

        }

        return $date;
    }
    public function getMonthToQuarter($month){
        $quarter= 0;
        if($month<=3){
            $quarter = 1;
        }else if($month>3 && $month<=6){
            $quarter = 2;
        }else if($month>6 && $month<=9){
            $quarter = 3;
        }else if($month>9 && $month<=12){
            $quarter = 4;
        }
        return $quarter;
    }

    public function getQuarterToMonth($quarter){

        $month = ['start'=>0,'end'=>0];
        if($quarter == 1){
            $month['start'] = '01';
            $month['end'] = '03';
        }elseif($quarter == 2){
            $month['start'] = '04';
            $month['end'] = '06';
        }elseif($quarter == 3){
            $month['start'] = '07';
            $month['end'] = '09';
        }elseif($quarter == 4){
            $month['start'] = '10';
            $month['end'] = '12';
        }
        return $month;



    }


    public function getQuarterDatebak($filterKey){
        $date = [];
        $month = date('m');
        if($filterKey == 'thisquarter'){
            if($month < 4){
                $date['startTime'] = date("Y-01");
                $date['endTime'] = date("Y-03");
            }elseif($month < 7){
                $date['startTime'] = date("Y-04");
                $date['endTime'] = date("Y-06");
            }elseif($month < 10){
                $date['startTime'] = date("Y-07");
                $date['endTime'] = date("Y-09");
            }else{
                $date['startTime'] = date("Y-10");
                $date['endTime'] = date("Y-12");
            }
        }elseif ($filterKey == 'lastquarter'){
            if($month < 4){
                $date['startTime'] = date("Y-10",strtotime("-11 Months"));
                $date['endTime'] = date("Y-12",strtotime("-11 Months"));
            }elseif($month < 7){
                $date['startTime'] = date("Y-01");
                $date['endTime'] = date("Y-03");
            }elseif($month < 10){
                $date['startTime'] = date("Y-04");
                $date['endTime'] = date("Y-06");
            }else{
                $date['startTime'] = date("Y-07");
                $date['endTime'] = date("Y-09");
            }
        }elseif ($filterKey == 'thisyear'){
            $date['startTime'] = date("Y-01");
            $date['endTime'] = date("Y-m");
        }elseif ($filterKey == 'lastyear'){
            $date['startTime'] = date("Y-m",strtotime("-11 Months"));
            $date['endTime'] = date("Y-m");
        }

        return $date;
    }


















































































}