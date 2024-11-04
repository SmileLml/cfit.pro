<?php

class quarterSecondmonthreport extends secondmonthreportModel
{
    //当前时间
    private $nowTime;
    //统计时间范围（根据当前时间前一天计算统计时间范围）
    private $statisticTime;
    //季度统计开始时间
    private $quarterStart;
    //季度统计结束时间
    private $quarterEnd;
    //年度统计开始时间
    private $yearStart;
    //年度统计结束时间
    private $yearEnd;
    //父级合并部门
    private $deptParent;
    //统计部门
    private $deptList;

    public function __construct($appName = '')
    {
        parent::__construct($appName);

        $this->nowTime = time();

        $this->statisticTime = $this->nowTime - 86400;

        $this->yearStart = date('Y-01-01', $this->statisticTime);
        $this->yearEnd   = date('Y-m-d 23:59:59', strtotime('last day of', $this->statisticTime));

        list($this->quarterStart, $this->quarterEnd) = $this->getQuarterDate();

        $this->deptList   = $this->getNeedShowDept(0, true);
        $this->deptParent = $this->loadModel('dept')->getDeptAndChild();
        foreach ($this->deptParent as $key => $value) {
            if (!in_array($key, $this->deptList)) {
                unset($this->deptParent[$key]);
            }
        }
    }

    public function initialization($quarter)
    {
        if (!in_array($quarter, [1, 2, 3, 4])) {
            exit('季度参数不合法');
        }
        $month               = $quarter * 3;
        $this->statisticTime = strtotime(date('Y-' . $month . '-01 H:i:s', $this->nowTime));

        $this->yearStart = date('Y-01-01', $this->statisticTime);
        $this->yearEnd   = date('Y-m-d 23:59:59', strtotime('last day of', $this->statisticTime));

        list($this->quarterStart, $this->quarterEnd) = $this->getQuarterDate();
    }

    public function initializationTime($quarter = 0, $year = 0)
    {
        if (!in_array($quarter, [0, 1, 2, 3, 4])) {
            exit('季度参数不合法');
        }
        if ($year > date('Y')) {
            exit('年份参数不能大于当前年份');
        }

        $year = $year == 0 ? date('Y') : $year;
        if($quarter == 0){
            $statisticTime = $this->nowTime - 86400;
            $year = date('Y', $statisticTime);
            $month = date('m', $statisticTime);
        }else{
            $month = $quarter * 3;
        }

        $this->statisticTime = strtotime(date($year . '-' . $month . '-01 H:i:s', $this->nowTime));
        $this->yearStart = date('Y-01-01', $this->statisticTime);
        $this->yearEnd   = date('Y-m-d 23:59:59', strtotime('last day of', $this->statisticTime));

        list($this->quarterStart, $this->quarterEnd) = $this->getQuarterDate();
    }

    /**
     * 获取季度时间范围
     * @return array|int[]
     */
    private function getQuarterDate()
    {
        $month = date('m', $this->statisticTime);

        if (0 != $month % 3) {
            return [0, 0];
        }

        $tmp   = date('Y-m-01', $this->statisticTime);
        $start = date('Y-m-01', strtotime('-2 months', strtotime($tmp)));
        $end   = date('Y-m-d 23:59:59', strtotime('last day of', $this->statisticTime));

        return [$start, $end];
    }

    /**
     * 获取季度统计时间范围
     * @param $isyearForm
     * @param $formtype
     * @return array
     */
    public function getTimeRangeByQuarter($isyearForm, $formtype)
    {
        return [
            'startdate'        => $this->yearStart,
            'enddate'          => $this->yearEnd,
            'startday'         => substr($this->yearStart, 0, 10),
            'endday'           => substr($this->yearEnd, 0, 10),
            'isyearform'       => $isyearForm,
            'stype'            => $formtype,
            'year'             => date('Y', strtotime($this->yearStart)),
            'month'            => date('m', strtotime($this->yearEnd)),
            'dtype'            => 1,
            'calendarstartday' => $this->yearStart,
            'time'             => $this->nowTime,
        ];
    }

    /**
     * 获取历史结转数据Id
     * @param $type
     * @return array
     */
    private function getHistoryData($type)
    {
        $data = $this->dao->select('objectid')->from(TABLE_SECONDMONTHHISTORYDATA)->alias('t1')
            ->where('t1.sourceyear')->eq((int)$this->lang->secondmonthreport->examinecycleList['examineyear'])
            ->andWhere('t1.sourcetype')->eq($type)
            ->andWhere('t1.deleted')->eq(0)
            ->fetchAll('objectid');

        return empty($data) ? [] : array_keys($data);
    }

    /**
     * 小时转为其他时间单位（天、月、年）
     * @param $arr
     * @return mixed
     */
    private function hourToOther($arr)
    {
        $halfDay    = ($arr['hour'] % 4) > 0 ? 0.5 : 0;
        $dayTemp    = floor($arr['hour'] / 4);
        $arr['day'] = ($dayTemp / 2) + $halfDay;

        $arr['month'] = number_format($arr['hour'] / ($this->lang->secondmonthreport->monthReportWorkHours['workHours'] * 8), 2);
        $arr['year']  = number_format($arr['hour'] / ($this->lang->secondmonthreport->monthReportWorkHours['workHours'] * 96), 2);

        return $arr;
    }

    /**
     * 保存季度统计数据
     * @param $data
     * @param $timeFrame
     * @param  mixed $detailId
     * @return array
     */
    public function reportSave($data, $timeFrame, $detailId)
    {
        switch ($timeFrame['stype']) {
            case 'problemExceedBackIn':
            case 'problemCompletedPlan':
            case 'problemOverall':
                $saveId = $this->addWholeReport($timeFrame, $this->nowTime, $timeFrame['stype']);
                break;
            case 'requirement_inside':
                $saveId = $this->demandmonthreportadd($timeFrame, $this->nowTime, $timeFrame['stype']);
                break;
            case 'secondorderclass':
                $saveId = $this->secondordermonthreportadd($timeFrame, $this->nowTime, $timeFrame['stype']);
                break;
            case 'modifyabnormal':
                $saveId = $this->modifymonthreportadd($timeFrame, $this->nowTime, $timeFrame['stype']);
                break;
            case 'support':
                $saveId = $this->supportmonthreportadd($timeFrame, $this->nowTime, $timeFrame['stype']);
                break;
            case 'workload':
                $saveId = $this->workloadmonthreportadd($timeFrame, $this->nowTime, $timeFrame['stype']);
                break;
            default:
                $saveId = 0;
        }

        if ($saveId <= 0) {
            return $saveId;
        }

        $this->dao->update(TABLE_WHOLE_REPORT)->data(['useIDArr' => $detailId])->where('id')->eq($saveId)->exec();

        foreach ($data as $deptId => $item) {
            $arr = [
                'deptID'      => $deptId,
                'tableType'   => $timeFrame['stype'],
                'wholeID'     => $saveId,
                'detail'      => json_encode($item),
                'createdDate' => date('Y-m-d H:i:s'),
            ];
            $this->dao->insert(TABLE_DETAIL_REPORT)->data($arr)->exec();
        }

        return $saveId;
    }

    /**
     * 问题整体情况统计表 历史统计数据生成
     * @param $timeFrame
     * @return mixed
     */
    public function problemOverallQuarter($timeFrame)
    {
        //根据统计时间获取统计源数据
        $sourceResultData = $this->loadModel('secondmonthreport')->getProblemDataList($this->yearStart, $this->yearEnd, 0, $timeFrame['stype'], 1);

        //根据统计规则获取统计详情
        $staticResult = $this->problemproblemOverallStaticQuarter($sourceResultData, 0);
        //保存统计详情
        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->problemproblemOverallSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->problemOverallQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    /**
     * 统计季度报表
     * @param $list
     * @return array
     */
    private function problemOverallQuarterStatic($list)
    {
        $arr = [
            'sum'           => 0,
            'sumRate'       => '0.00%',
            'guestjx'       => 0,
            'guestjxRate'   => '0.00%',
            'guestcn'       => 0,
            'guestcnRate'   => '0.00%',
            'self'          => 0,
            'selfRate'      => '0.00%',
            'solved'        => 0,
            'noSolve'       => 0,
            'solveRate'     => '0%',
            'averagePeriod' => 0,
            'maxPeriod'     => 0,
        ];

        $data[0] = [
            'quarter' => $arr,
            'year'    => $arr,
            'history' => $arr,
        ];
        $deptParent = $this->deptParent;
        $deptList   = $this->deptList;
        $historyId  = $this->getHistoryData('problem');
        $id         = [];
        foreach ($list as $item) {
            $item->acceptDept = empty($item->acceptDept) ? -1 : $item->acceptDept;
            if (!in_array($item->acceptDept, $deptList)) {
                continue;
            }
            if (!isset($data[$deptParent[$item->acceptDept]])) {
                $data[$deptParent[$item->acceptDept]] = [
                    'quarter' => $arr,
                    'year'    => $arr,
                    'history' => $arr,
                ];
            }
            if ($this->quarterStart <= $item->createdDate && $this->quarterEnd >= $item->createdDate) {
                $data[0]['quarter']                              = $this->problemOverallStaticInfo($item, $data[0]['quarter']);
                $data[$deptParent[$item->acceptDept]]['quarter'] = $this->problemOverallStaticInfo($item, $data[$deptParent[$item->acceptDept]]['quarter']);
            }
            if ($this->yearStart <= $item->createdDate && $this->yearEnd >= $item->createdDate) {
                $data[0]['year']                              = $this->problemOverallStaticInfo($item, $data[0]['year']);
                $data[$deptParent[$item->acceptDept]]['year'] = $this->problemOverallStaticInfo($item, $data[$deptParent[$item->acceptDept]]['year']);
            }
            if (in_array($item->id, $historyId)) {
                $data[0]['history']                              = $this->problemOverallStaticInfo($item, $data[0]['history']);
                $data[$deptParent[$item->acceptDept]]['history'] = $this->problemOverallStaticInfo($item, $data[$deptParent[$item->acceptDept]]['history']);
            }
        }

        foreach ($data as $deptId => $value) {
            $value['yearSum'] = [
                'sum'           => $value['year']['sum'] + $value['history']['sum'],
                'guestjx'       => $value['year']['guestjx'] + $value['history']['guestjx'],
                'guestcn'       => $value['year']['guestcn'] + $value['history']['guestcn'],
                'self'          => $value['year']['self'] + $value['history']['self'],
                'solved'        => $value['year']['solved'] + $value['history']['solved'],
                'noSolve'       => $value['year']['noSolve'] + $value['history']['noSolve'],
                'solveRate'     => '0.00%',
                'averagePeriod' => $value['year']['averagePeriod'] + $value['history']['averagePeriod'],
                'maxPeriod'     => max($value['year']['maxPeriod'], $value['history']['maxPeriod']),
            ];
            $data[$deptId] = $value;

            //计算百分比
            foreach ($value as $key => $val) {
                if (0 < $val['sum']) {
                    $data[$deptId][$key]['solveRate']   = number_format($val['solved'] / $val['sum'] * 100, 2) . '%';
                    $data[$deptId][$key]['guestjxRate'] = number_format($val['guestjx'] / $val['sum'] * 100, 2) . '%';
                    $data[$deptId][$key]['guestcnRate'] = number_format($val['guestcn'] / $val['sum'] * 100, 2) . '%';
                    $data[$deptId][$key]['selfRate']    = number_format($val['self'] / $val['sum'] * 100, 2) . '%';
                }
                if (0 < $val['solved']) {
                    $data[$deptId][$key]['averagePeriod'] = ceil($val['averagePeriod'] / $val['solved']);
                }
                if (0 < $data[$deptId]['yearSum']['sum']) {
                    $data[$deptId][$key]['sumRate'] = number_format($val['sum'] / $data[$deptId]['yearSum']['sum'] * 100, 2) . '%';
                }
            }
        }

        return $data;
    }

    /**
     * 统计问题来源和问题解决情况
     * @param $problem
     * @param $arr
     * @return mixed
     */
    private function problemOverallStaticInfo($problem, $arr)
    {
        $solvedStatus  = ['delivery', 'onlinesuccess', 'closed', 'toclose'];
        $noSolveStatus = ['assigned', 'feedbacked', 'build', 'released', 'exception'];
        /*$status        = array_merge($noSolveStatus, $solvedStatus);
        if (!in_array($problem->status, $status)) {
            return $arr;
        }*/

        ++$arr['sum'];

        if (in_array($problem->createdBy, ['guestjx', 'guestcn'])) {
            ++$arr[$problem->createdBy];
        } else {
            ++$arr['self'];
        }

        if (in_array($problem->status, $solvedStatus)) {
            ++$arr['solved'];

            $solvedTime = strtotime($problem->solvedTime);
            $dealTime   = strtotime($problem->dealAssigned);
            if ($dealTime > 0 && $solvedTime > 0 && $dealTime < $solvedTime) {
                $period = ceil(($solvedTime - $dealTime) / 86400);
                $arr['averagePeriod'] += $period;

                $arr['maxPeriod'] = $arr['maxPeriod'] < $period ? $period : $arr['maxPeriod'];
            }
        }

        if (in_array($problem->status, $noSolveStatus)) {
            ++$arr['noSolve'];
        }

        return $arr;
    }

    /**
     * 问题整体统计详情
     * @param $problemData
     * @param $deptID
     * @param $isQuarter
     * @return array
     */
    public function problemproblemOverallStaticQuarter($problemData, $deptID = 0)
    {
        $alreadySolve     = ['delivery', 'onlinesuccess', 'closed', 'toclose'];
        $waitSolve        = ['assigned', 'feedbacked', 'released', 'build', 'exception'];
        $deptParent       = $this->deptParent;
        $needShowDeptList = $this->deptList;

        $overalls                = []; //整体统计
        $overallsIDS             = []; //整体统计
        $problemOverallDetailArr = [];
        $problemOverallIdArr     = [];
        $deptIDArr               = [];

        $overallsDefault = [
            'unaccepted'     => 0,
            'waitAllocation' => 0,
            'waitSolve'      => 0,
            'alreadySolve'   => 0,
            'total'          => 0,
            'solveRate'      => '0.00',
        ];

        foreach ($problemData as $item) {
            $item->acceptDept = empty($item->acceptDept) ? -1 : $item->acceptDept;
            if (!in_array($item->acceptDept, $needShowDeptList)) {
                continue;
            }
            //初始化统计报表
            if (!isset($overalls[$deptParent[$item->acceptDept]])) {
                $overalls[$deptParent[$item->acceptDept]] = $overallsDefault;
            }
            //统计总数
            if ('suspend' != $item->status) {
                ++$overalls[$deptParent[$item->acceptDept]]['total'];
                $overallsIDS[]                                                               = $item->id;
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['total'][]              = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['total'][$item->id] = $item;
            }
            //状态为未受理
            if ('returned' == $item->status) {
                ++$overalls[$deptParent[$item->acceptDept]]['unaccepted'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['unaccepted'][]              = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['unaccepted'][$item->id] = $item;
            }
            //状态为待分配
            if ('confirmed' == $item->status) {
                ++$overalls[$deptParent[$item->acceptDept]]['waitAllocation'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['waitAllocation'][]              = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['waitAllocation'][$item->id] = $item;
            }
            //状态为待解决
            if (in_array($item->status, $waitSolve)) {
                ++$overalls[$deptParent[$item->acceptDept]]['waitSolve'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['waitSolve'][]              = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['waitSolve'][$item->id] = $item;
            }
            //状态为已解决
            if (in_array($item->status, $alreadySolve)) {
                ++$overalls[$deptParent[$item->acceptDept]]['alreadySolve'];
                $problemOverallIdArr[$deptParent[$item->acceptDept]]['alreadySolve'][]              = $item->id;
                $problemOverallDetailArr[$deptParent[$item->acceptDept]]['alreadySolve'][$item->id] = $item;
            }
        }
        //补齐数据
        //整体统计
        if (!$deptID) {
            foreach ($deptParent as $alldept) {
                if (!isset($overalls[$alldept])) {
                    //部门为空 且 无数据时 不需要候补
                    $overalls[$alldept] = $overallsDefault;
                }
            }
        }

        //整体统计表剔除 不是统计部门中部门数据为 0 的数据
        foreach ($overalls as $dept => $dataArr) {
            $overalls[$dept]['solveRate'] = $dataArr['total'] > 0 ? number_format(($dataArr['alreadySolve'] / $dataArr['total']) * 100, 2) : '0.00';
            $deptIDArr[]                  = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return [
            'useids'       => array_unique($overallsIDS),
            'deptcolumids' => $problemOverallIdArr,
            'staticdata'   => $overalls,
            'detail'       => $problemOverallDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }

    /**
     * 查询问题整体统计季度报表
     * @param $wholeInfo
     * @param $deptId
     * @return mixed
     */
    public function getProblemOverallReport($wholeInfo, $deptId = 0)
    {
        $wholeInfo = $this->dao
            ->select('*')
            ->from(TABLE_WHOLE_REPORT)
            ->where('dtype')->eq(1)
            ->andWhere('type')->eq($wholeInfo->type)
            ->andWhere('useIDArr')->eq($wholeInfo->id)
            ->andWhere('isyear')->eq(3)
            ->orderBy('year_desc,month_desc')
            ->fetch();

        $report = $detailReports = $this->dao->select('*')->from(TABLE_DETAIL_REPORT)
            ->where('wholeID')->eq($wholeInfo->id)
            ->andWhere('deptID')->eq($deptId)
            ->fetch();

        return json_decode($report->detail);
    }

    /**
     * 问题单内部反馈超期季度统计
     * @param $timeFrame
     * @return mixed
     */
    public function problemHistoryExceedBackInQuarter($timeFrame)
    {
        //根据统计时间获取统计源数据
        $sourceResultData = $this->loadModel('secondmonthreport')->getProblemDataList($this->yearStart, $this->yearEnd, 0, $timeFrame['stype'], 0);

        //根据统计规则获取统计详情
        $staticResult = $this->problemExceedBackInStaticInfo($sourceResultData, 0);
        //保存统计详情
        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->problemproblemExceedBackInSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->problemExceedBackInStaticQuarter($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    /**
     * 统计问题单内部反馈超期季度
     * @param $list
     * @return array
     */
    public function problemExceedBackInStaticQuarter($list)
    {
        $arr = [
            'total'      => 0,
            'exceed'     => 0,
            'exceedRate' => '0.00%',
        ];

        $data[0] = [
            'quarter' => $arr,
            'yearSum' => $arr,
        ];
        $problemModel = $this->loadModel('problem');
        $deptParent   = $this->deptParent;
        $deptList     = $this->deptList;

        foreach ($list as $item) {
            $item->acceptDept = empty($item->acceptDept) ? -1 : $item->acceptDept;
            if (empty($item->acceptDept)) {
                $item->acceptDept = -1;
            }
            if (!in_array($item->acceptDept, $deptList)) {
                continue;
            }
            $item = $problemModel->getIfOverDate($item);

            if (!isset($data[$deptParent[$item->acceptDept]])) {
                $data[$deptParent[$item->acceptDept]] = [
                    'quarter' => $arr,
                    'yearSum' => $arr,
                ];
            }
            if ($this->quarterStart <= $item->createdDate && $this->quarterEnd >= $item->createdDate) {
                ++$data[0]['quarter']['total'];
                ++$data[$deptParent[$item->acceptDept]]['quarter']['total'];
                if (!empty($item->IssueId) && isset($item->ifOverDateInside) && '是' == $item->ifOverDateInside['flag']) {
                    ++$data[0]['quarter']['exceed'];
                    ++$data[$deptParent[$item->acceptDept]]['quarter']['exceed'];
                }
            }
            if ($this->yearStart <= $item->createdDate && $this->yearEnd >= $item->createdDate) {
                ++$data[0]['yearSum']['total'];
                ++$data[$deptParent[$item->acceptDept]]['yearSum']['total'];
                if (!empty($item->IssueId) && isset($item->ifOverDateInside) && '是' == $item->ifOverDateInside['flag']) {
                    ++$data[0]['yearSum']['exceed'];
                    ++$data[$deptParent[$item->acceptDept]]['yearSum']['exceed'];
                }
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($value as $key => $val) {
                if (0 < $val['total']) {
                    $data[$deptId][$key]['exceedRate'] = number_format($val['exceed'] / $val['total'] * 100, 2) . '%';
                }
            }
        }

        return $data;
    }

    /**
     * 问题单内部反馈超期季度统计详情
     * @param $problemData
     * @param $deptID
     * @return array
     */
    public function problemExceedBackInStaticInfo($problemData, $deptID = 0)
    {
        set_time_limit(0);
        $problemModel                 = $this->loadModel('problem');
        $deptParent                   = $this->deptParent;
        $needShowDeptList             = $this->deptList;
        $exceedBackIns                = []; //问题解决超期统计
        $exceedBackInsIDS             = []; //内部反馈超期
        $problemExceedBackInIdArr     = [];
        $problemExceedBackInDetailArr = [];
        $deptIDArr                    = [];

        $exceedBackInsDefault = [
            'backTotal'      => 0,
            'backExceedRate' => '0.00',
            'foverdueNum'    => 0,
        ];

        foreach ($problemData as $item) {
            $item->acceptDept = empty($item->acceptDept) ? -1 : $item->acceptDept;
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }
            if (!in_array($item->acceptDept, $needShowDeptList)) {
                continue;
            }

            if (!empty($item->IssueId)) {
                if (!isset($exceedBackIns[$deptParent[$item->acceptDept]])) {
                    //内部反馈超期统计初始化
                    $exceedBackIns[$deptParent[$item->acceptDept]] = $exceedBackInsDefault;
                }

                ++$exceedBackIns[$deptParent[$item->acceptDept]]['backTotal'];
                $problemExceedBackInIdArr[$deptParent[$item->acceptDept]]['backTotal'][]              = $item->id;
                $problemExceedBackInDetailArr[$deptParent[$item->acceptDept]]['backTotal'][$item->id] = $item;
            }

            $item = $problemModel->getIfOverDate($item);
            // 2023-05-24去掉， 注释里先保留 && '1' != $item->isBackExtended
            if (!empty($item->IssueId) && isset($item->ifOverDateInside) && '是' == $item->ifOverDateInside['flag']) {
                if (isset($exceedBackIns[$deptParent[$item->acceptDept]]['foverdueNum'])) {
                    ++$exceedBackIns[$deptParent[$item->acceptDept]]['foverdueNum'];
                } else {
                    $exceedBackIns[$deptParent[$item->acceptDept]]['foverdueNum'] = 1;
                }
                $exceedBackInsIDS[]                                                                     = $item->id;
                $problemExceedBackInIdArr[$deptParent[$item->acceptDept]]['foverdueNum'][]              = $item->id;
                $problemExceedBackInDetailArr[$deptParent[$item->acceptDept]]['foverdueNum'][$item->id] = $item;
            }
        }

        //内部反馈超期统计表 补齐部门
        //实时表单有部门搜索时 不再补齐部门
        if (!$deptID) {
            foreach ($deptParent as $showDept) {
                if (!isset($exceedBackIns[$showDept])) {
                    $exceedBackIns[$showDept] = $exceedBackInsDefault;
                }
            }
        }

        //内部反馈超期统计表 剔除 不是统计部门中部门数据为 0 的数据
        foreach ($exceedBackIns as $dept => $dataArr) {
            if (!$dataArr['backTotal'] && !in_array($dept, $needShowDeptList)) {
                unset($exceedBackIns[$dept]);
                continue;
            }
            $exceedBackIns[$dept]['backExceedRate'] = $dataArr['backTotal'] > 0 ? number_format($dataArr['foverdueNum'] / $dataArr['backTotal'] * 100, 2) : '0.00';
            $deptIDArr[]                            = $dept;
        }

        return [
            'useids'       => array_unique($exceedBackInsIDS),
            'deptcolumids' => $problemExceedBackInIdArr,
            'staticdata'   => $exceedBackIns,
            'detail'       => $problemExceedBackInDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }

    /**
     * 需求任务内部反馈超期季度统计表
     * @param $timeFrame
     * @return mixed
     */
    public function requirementHistoryInsideQuarter($timeFrame)
    {
        //根据统计时间获取统计源数据
        $sourceResultData = $this->loadModel('secondmonthreport')->getRequirementDataList($this->yearStart, $this->yearEnd, 0, '', 1);

        //根据统计规则获取统计详情
        $staticResult = $this->requirementInsideQuarterStaticInfo($sourceResultData);
        //保存统计详情
        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->requirementInsideSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->requirementInsideQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    /**
     * 需求任务内部反馈超期季度统计
     * @param $list
     * @return array
     */
    public function requirementInsideQuarterStatic($list)
    {
        $arr = [
            'total'      => 0,
            'exceed'     => 0,
            'exceedRate' => '0.00%',
        ];
        $data[0] = [
            'quarter' => $arr,
            'yearSum' => $arr,
        ];
        $deptParent = $this->deptParent;
        $deptList   = $this->deptList;

        foreach ($list as $item) {
            $item->dept = empty($item->dept) ? -1 : $item->dept;
            if (!in_array($item->dept, $deptList)) {
                continue;
            }

            if (!isset($data[$deptParent[$item->dept]])) {
                $data[$deptParent[$item->dept]] = [
                    'quarter' => $arr,
                    'yearSum' => $arr,
                ];
            }

            if ($this->quarterStart <= $item->createdDate && $this->quarterEnd >= $item->createdDate) {
                ++$data[0]['quarter']['total'];
                ++$data[$deptParent[$item->dept]]['quarter']['total'];
                if (2 == $item->ifOverDate) {
                    ++$data[0]['quarter']['exceed'];
                    ++$data[$deptParent[$item->dept]]['quarter']['exceed'];
                }
            }
            if ($this->yearStart <= $item->createdDate && $this->yearEnd >= $item->createdDate) {
                ++$data[0]['yearSum']['total'];
                ++$data[$deptParent[$item->dept]]['yearSum']['total'];
                if (2 == $item->ifOverDate) {
                    ++$data[0]['yearSum']['exceed'];
                    ++$data[$deptParent[$item->dept]]['yearSum']['exceed'];
                }
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($value as $key => $val) {
                if (0 < $val['total']) {
                    $data[$deptId][$key]['exceedRate'] = number_format($val['exceed'] / $val['total'] * 100, 2) . '%';
                }
            }
        }

        return $data;
    }

    /**
     * 需求任务内部反馈超期季度统计详情
     * @param $requirementInsideDataList
     * @param $deptID
     * @return array
     */
    public function requirementInsideQuarterStaticInfo($requirementInsideDataList, $deptID = 0)
    {
        $deptArr                     = [];
        $jsonRealizedData            = [];
        $useIDS                      = [];
        $requirement_insideDetailArr = [];
        $requirement_insideIdArr     = [];
        $deptIDArr                   = [];

        $deptParent       = $this->deptParent;
        $needShowDeptList = $this->deptList;

        foreach ($requirementInsideDataList as $realizedValue) {
            $realizedValue->dept = empty($realizedValue->dept) ? -1 : $realizedValue->dept;
            if (!in_array($realizedValue->dept, $needShowDeptList)) {
                continue;
            }
            $deptArr[$deptParent[$realizedValue->dept]][] = $realizedValue;
        }
        //因不需要补齐部门，此判断可保留
        if (!empty($deptArr)) {
            foreach ($deptArr as $i => $item) {
                $foverdueNum = 0; //反馈超期数
                if (empty($i)) {
                    continue;
                }
                $backTotal = count($item); //反馈单总数

                foreach ($item as $v) {
                    if ('0000-00-00 00:00:00' == $v->feekBackStartTime) {
                        $v->feekBackStartTime = '';
                    }
                    if ('0000-00-00 00:00:00' == $v->deptPassTime) {
                        $v->deptPassTime = '';
                    }
                    //反馈单总数
                    $requirement_insideIdArr[$i]['backTotal'][]           = $v->id;
                    $requirement_insideDetailArr[$i]['backTotal'][$v->id] = $v;
                    //内部反馈超期数 && $v->feedbackOver != 1 2024-05-24去掉，注释中保留 预防业务方撤回修改
                    if (2 == $v->ifOverDate) {
                        ++$foverdueNum;
                        $useIDS[]                                               = $v->id;
                        $requirement_insideIdArr[$i]['foverdueNum'][]           = $v->id;
                        $requirement_insideDetailArr[$i]['foverdueNum'][$v->id] = $v;
                    }
                }

                if (!empty($backTotal)) {
                    $backExceedRate = $foverdueNum / $backTotal * 100; //超期率 合计/条目总数
                } else {
                    $backExceedRate = 0;
                }
                $jsonRealizedData[$i]['deptID']         = $i;
                $jsonRealizedData[$i]['backTotal']      = $backTotal;
                $jsonRealizedData[$i]['foverdueNum']    = $foverdueNum;
                $jsonRealizedData[$i]['backExceedRate'] = number_format($backExceedRate, 2);
            }
        }

        //补齐部门数据
        if (!$deptID) {
            foreach ($deptParent as $showDept) {
                if (!isset($jsonRealizedData[$showDept])) {
                    $jsonRealizedData[$showDept] = [
                        'deptID'         => $showDept,
                        'backTotal'      => 0,
                        'foverdueNum'    => 0,
                        'backExceedRate' => '0.00',
                    ];
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($jsonRealizedData as $dept => $dataArr) {
            if (!$dataArr['backTotal'] && !in_array($dept, $needShowDeptList)) {
                unset($jsonRealizedData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        return [
            'useids'       => array_unique($useIDS),
            'deptcolumids' => $requirement_insideIdArr,
            'staticdata'   => $jsonRealizedData,
            'detail'       => $requirement_insideDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }

    /**
     * 任务工单类型季度统计表
     * @param $timeFrame
     * @return mixed
     */
    public function secondOrderHistoryClassQuarter($timeFrame)
    {
        //根据统计时间获取统计源数据
        $sourceResultData = $this->loadModel('secondmonthreport')->getSecondorderDataList($this->yearStart, $this->yearEnd, 0, $timeFrame['stype'], 1);

        //根据统计规则获取统计详情
        $staticResult = $this->secondOrderClassQuarterStaticInfo($sourceResultData, 0);
        //保存统计详情
        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->secondorderclassSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->secondOrderClassQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    /**
     * 任务工单类型季度统计
     * @param $list
     * @return array
     */
    public function secondOrderClassQuarterStatic($list)
    {
        $this->app->loadLang('secondorder');
        $types = array_keys(array_filter($this->lang->secondmonthreport->secondorderTypeList) + ['sum' => 'sum']);

        $arr = [];
        foreach ($types as $type) {
            $arr[$type] = 0;
        }
        $data[0] = ['quarter' => $arr, 'year' => $arr, 'history' => $arr];

        $deptParent    = $this->deptParent;
        $deptList      = $this->deptList;
        $historyId     = $this->getHistoryData('secondorder');
        $innerFunction = function ($secondOrder, $info) {
            ++$info['sum'];
            ++$info[$secondOrder->type];

            return $info;
        };

        foreach ($list as $item) {
            $item->acceptDept = empty($item->acceptDept) ? -1 : $item->acceptDept;
            if (!in_array($item->acceptDept, $deptList)) {
                continue;
            }
            if (!isset($data[$deptParent[$item->acceptDept]])) {
                $data[$deptParent[$item->acceptDept]] = [
                    'quarter' => $arr,
                    'year'    => $arr,
                    'history' => $arr,
                ];
            }
            if ($this->quarterStart <= $item->createdDate && $this->quarterEnd >= $item->createdDate) {
                $data[0]['quarter']                              = $innerFunction($item, $data[0]['quarter']);
                $data[$deptParent[$item->acceptDept]]['quarter'] = $innerFunction($item, $data[$deptParent[$item->acceptDept]]['quarter']);
            }
            if ($this->yearStart <= $item->createdDate && $this->yearEnd >= $item->createdDate) {
                $data[0]['year']                              = $innerFunction($item, $data[0]['year']);
                $data[$deptParent[$item->acceptDept]]['year'] = $innerFunction($item, $data[$deptParent[$item->acceptDept]]['year']);
            }
            if (in_array($item->id, $historyId)) {
                $data[0]['history']                              = $innerFunction($item, $data[0]['history']);
                $data[$deptParent[$item->acceptDept]]['history'] = $innerFunction($item, $data[$deptParent[$item->acceptDept]]['history']);
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($types as $type) {
                $value['yearSum'][$type] = $value['year'][$type] + $value['history'][$type];
            }

            $data[$deptId] = $value;
        }

        return $data;
    }

    /**
     * 任务工单类型季度统计详情
     * @param $secondorderData
     * @param $deptID
     * @return array
     */
    public function secondOrderClassQuarterStaticInfo($secondorderData, $deptID = 0)
    {
        //按照配置的部门 当统计数据中无此部门进行补全
        $needShowDeptList = $this->deptList;
        $deptParent       = $this->deptParent;

        $countData                 = [];
        $useIDS                    = [];
        $secondorderclassDetailArr = [];
        $secondorderclassIdArr     = [];
        $deptIDArr                 = [];
        //按部门-》分类 统计
        foreach ($secondorderData as $sencondorder) {
            $sencondorder->acceptDept = empty($sencondorder->acceptDept) ? -1 : $sencondorder->acceptDept;
            if (!in_array($sencondorder->acceptDept, $needShowDeptList)) {
                continue;
            }
            //如果不再统计类型中，跳过
            if (!isset($this->lang->secondmonthreport->secondorderTypeList[$sencondorder->type])) {
                continue;
            }

            if (!isset($countData[$deptParent[$sencondorder->acceptDept]][$sencondorder->type])) {
                $countData[$deptParent[$sencondorder->acceptDept]][$sencondorder->type] = 1;
            } else {
                ++$countData[$deptParent[$sencondorder->acceptDept]][$sencondorder->type];
            }
            $useIDS[]                                                                                                  = $sencondorder->id;
            $secondorderclassIdArr[$deptParent[$sencondorder->acceptDept]][$sencondorder->type][]                      = $sencondorder->id;
            $secondorderclassDetailArr[$deptParent[$sencondorder->acceptDept]][$sencondorder->type][$sencondorder->id] = $sencondorder;
            $secondorderclassIdArr[$deptParent[$sencondorder->acceptDept]]['total'][]                                  = $sencondorder->id;
            $secondorderclassDetailArr[$deptParent[$sencondorder->acceptDept]]['total'][$sencondorder->id]             = $sencondorder;
        }

        //每组数据补全分类
        foreach ($this->lang->secondmonthreport->secondorderTypeList as $type => $val) {
            if (!$type) {
                continue;
            }
            foreach ($countData as $dept => $dataArr) {
                if (!isset($dataArr[$type])) {
                    $countData[$dept][$type] = 0;
                }
            }
        }

        //补齐部门
        if (!$deptID) {
            foreach ($deptParent as $deptVal) {
                if (!isset($countData[$deptVal])) {
                    foreach ($this->lang->secondmonthreport->secondorderTypeList as $type => $val) {
                        if (!$type) {
                            continue;
                        }
                        $countData[$deptVal][$type] = 0;
                    }
                }
            }
        }

        //行补充合计
        foreach ($countData as $dept => $dataArr) {
            $countData[$dept]['total'] = array_sum($countData[$dept]);
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept => $dataArr) {
            if (!$dataArr['total'] && !in_array($dept, $needShowDeptList)) {
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return [
            'useids'       => array_unique($useIDS),
            'deptcolumids' => $secondorderclassIdArr,
            'staticdata'   => $countData,
            'detail'       => $secondorderclassDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }

    /**
     * 变更异常季度统计表
     * @param $timeFrame
     * @return mixed
     */
    public function modifyHistoryNormalQuarter($timeFrame)
    {
        //根据统计时间获取统计源数据
        $sourceResultData = $this->getModifyFinishData($this->yearStart, $this->yearEnd, 0);

        $staticResult = $this->modifyabnormalQuarterStaticInfo($sourceResultData, 0);

        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->modifyabnormalSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->modifyAbnormalQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    /**
     * 变更异常季度统计
     * @param $list
     * @param $deptID
     * @return array
     */
    public function modifyAbnormalQuarterStatic($list, $deptID = 0)
    {
        $arr = [
            'first'      => 0,
            'second'     => 0,
            'third'      => 0,
            'total'      => 0,
            'exceed'     => 0,
            'exceedRate' => '0.00%',
        ];
        $data[0] = [
            'quarter' => $arr,
            'yearSum' => $arr,
        ];
        $exceedStatus = [
            'modify'     => ['modifyerror', 'modifysuccesspart', 'modifyfail', 'modifyrollback'], //变更失败，部分成功，变更异常，变更回退
            'modifycncc' => ['modifyfail', 'modifysuccesspart'], //变更失败，部分成功
            'credit'     => ['fail', 'successpart', 'modifyerror', 'modifyrollback'], //变更失败，部分成功，变更异常，变更回退
        ];

        $innerFunction = function ($item, $info, $exceedStatus) {
            ++$info['total'];
            switch ($item->level) {
                case '1':
                    ++$info['first'];
                    break;
                case '2':
                    ++$info['second'];
                    break;
                case '3':
                    ++$info['third'];
                    break;
            }

            if (in_array($item->status, $exceedStatus)) {
                ++$info['exceed'];
            }

            return $info;
        };

        $deptParent = $this->deptParent;
        $deptList   = $this->deptList;

        foreach ($list as $type => $value) {
            foreach ($value as $item) {
                $item->createdDept = empty($item->createdDept) ? -1 : $item->createdDept;
                if (!in_array($item->createdDept, $deptList)) {
                    continue;
                }

                if (!isset($data[$deptParent[$item->createdDept]])) {
                    $data[$deptParent[$item->createdDept]] = [
                        'quarter' => $arr,
                        'yearSum' => $arr,
                    ];
                }

                if ($this->quarterStart <= $item->realEndTime && $this->quarterEnd >= $item->realEndTime) {
                    $data[0]['quarter']                               = $innerFunction($item, $data[0]['quarter'], $exceedStatus[$type]);
                    $data[$deptParent[$item->createdDept]]['quarter'] = $innerFunction($item, $data[$deptParent[$item->createdDept]]['quarter'], $exceedStatus[$type]);
                }
                if ($this->yearStart <= $item->realEndTime && $this->yearEnd >= $item->realEndTime) {
                    $data[0]['yearSum']                               = $innerFunction($item, $data[0]['yearSum'], $exceedStatus[$type]);
                    $data[$deptParent[$item->createdDept]]['quarter'] = $innerFunction($item, $data[$deptParent[$item->createdDept]]['yearSum'], $exceedStatus[$type]);
                }
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($value as $key => $val) {
                if (0 < $val['total']) {
                    $data[$deptId][$key]['exceedRate'] = number_format($val['exceed'] / $val['total'] * 100, 2) . '%';
                }
            }
        }

        return $data;
    }

    /**
     * 变更异常季度统计详情
     * @param $modifyabnormalData
     * @param $deptID
     * @return array
     */
    public function modifyabnormalQuarterStaticInfo($modifyabnormalData, $deptID = 0)
    {
        $useIDS                  = ['modify' => [], 'modifycncc' => [], 'credit' => []];
        $modifyabnormalDetailArr = ['modify' => [], 'modifycncc' => [], 'credit' => []];
        $modifyabnormalIdArr     = ['modify' => [], 'modifycncc' => [], 'credit' => []];
        $deptIDArr               = [];

        $needShowDeptList = $this->deptList;
        $deptParent       = $this->deptParent;
        $abnormalNum      = 'abnormalNum';
        $modifyCountNum   = 'modifyCountNum';

        $countData = [];
        foreach ($modifyabnormalData['modify'] as $modify) {
            $modify->createdDept = empty($modify->createdDept) ? -1 : $modify->createdDept;
            if (!isset($this->lang->secondmonthreport->modifyUseStatus[$modify->status]) || !in_array($modify->createdDept, $needShowDeptList)) {
                continue;
            }

            if (!isset($countData[$deptParent[$modify->createdDept]][$modifyCountNum])) {
                $countData[$deptParent[$modify->createdDept]][$modifyCountNum] = 1;
            } else {
                ++$countData[$deptParent[$modify->createdDept]][$modifyCountNum];
            }

            $modifyabnormalIdArr['modify'][$deptParent[$modify->createdDept]][$modifyCountNum][]                = $modify->id;
            $modifyabnormalDetailArr['modify'][$deptParent[$modify->createdDept]][$modifyCountNum][$modify->id] = $modify;
            //异常单
            if (in_array($modify->status, $this->lang->secondmonthreport->modifyreissueArray)) {
                if (!isset($countData[$deptParent[$modify->createdDept]][$abnormalNum])) {
                    $countData[$deptParent[$modify->createdDept]][$abnormalNum] = 1;
                } else {
                    ++$countData[$deptParent[$modify->createdDept]][$abnormalNum];
                }

                $useIDS['modify'][]                                                                              = $modify->id;
                $modifyabnormalIdArr['modify'][$deptParent[$modify->createdDept]][$abnormalNum][]                = $modify->id;
                $modifyabnormalDetailArr['modify'][$deptParent[$modify->createdDept]][$abnormalNum][$modify->id] = $modify;
            }
        }

        foreach ($modifyabnormalData['modifycncc'] as $modifycncc) {
            $modifycncc->createdDept = empty($modifycncc->createdDept) ? -1 : $modifycncc->createdDept;
            if (!isset($this->lang->secondmonthreport->modifyccUseStatus[$modifycncc->status]) || !in_array($modifycncc->createdDept, $needShowDeptList)) {
                continue;
            }

            if (!isset($countData[$deptParent[$modifycncc->createdDept]][$modifyCountNum])) {
                $countData[$deptParent[$modifycncc->createdDept]][$modifyCountNum] = 1;
            } else {
                ++$countData[$deptParent[$modifycncc->createdDept]][$modifyCountNum];
            }
            $modifyabnormalIdArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$modifyCountNum][]                    = $modifycncc->id;
            $modifyabnormalDetailArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$modifyCountNum][$modifycncc->id] = $modifycncc;
            //异常单
            if (in_array($modifycncc->status, $this->lang->secondmonthreport->modifyccreissueArray)) {
                if (!isset($countData[$deptParent[$modifycncc->createdDept]][$abnormalNum])) {
                    $countData[$deptParent[$modifycncc->createdDept]][$abnormalNum] = 1;
                } else {
                    ++$countData[$deptParent[$modifycncc->createdDept]][$abnormalNum];
                }
                $useIDS['modifycncc'][]                                                                                      = $modifycncc->id;
                $modifyabnormalIdArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$abnormalNum][]                    = $modifycncc->id;
                $modifyabnormalDetailArr['modifycncc'][$deptParent[$modifycncc->createdDept]][$abnormalNum][$modifycncc->id] = $modifycncc;
            }
        }

        foreach ($modifyabnormalData['credit'] as $credit) {
            $credit->createdDept = empty($credit->createdDept) ? -1 : $credit->createdDept;
            if ('cancel' == $credit->status || !in_array($credit->createdDept, $needShowDeptList)) {
                continue;
            }
            if (!isset($countData[$deptParent[$credit->createdDept]][$modifyCountNum])) {
                $countData[$deptParent[$credit->createdDept]][$modifyCountNum] = 1;
            } else {
                ++$countData[$deptParent[$credit->createdDept]][$modifyCountNum];
            }

            $modifyabnormalIdArr['credit'][$deptParent[$credit->createdDept]][$modifyCountNum][]                = $credit->id;
            $modifyabnormalDetailArr['credit'][$deptParent[$credit->createdDept]][$modifyCountNum][$credit->id] = $credit;
            //异常单
            if (in_array($credit->status, $this->lang->secondmonthreport->creditreissueArray)) {
                if (!isset($countData[$deptParent[$credit->createdDept]][$abnormalNum])) {
                    $countData[$deptParent[$credit->createdDept]][$abnormalNum] = 1;
                } else {
                    ++$countData[$deptParent[$credit->createdDept]][$abnormalNum];
                }

                $useIDS['credit'][]                                                                              = $credit->id;
                $modifyabnormalIdArr['credit'][$deptParent[$credit->createdDept]][$abnormalNum][]                = $credit->id;
                $modifyabnormalDetailArr['credit'][$deptParent[$credit->createdDept]][$abnormalNum][$credit->id] = $credit;
            }
        }

        foreach ($countData as $ldept => $ldataArr) {
            if (isset($ldataArr[$abnormalNum])) {
                $countData[$ldept]['banormalrate'] = sprintf('%.2f', ($ldataArr[$abnormalNum] / $ldataArr[$modifyCountNum]) * 100);
            } else {
                $countData[$ldept]['banormalrate'] = '0.00';
            }
        }
        //补齐属性
        foreach ([$abnormalNum => $abnormalNum, $modifyCountNum => $modifyCountNum] as $status => $val) {
            if (!$status) {
                continue;
            }
            foreach ($countData as $dept => $dataArr) {
                if (!isset($dataArr[$status])) {
                    $countData[$dept][$status] = 0;
                }
            }
        }

        //补齐部门
        if (!$deptID) {
            foreach ($deptParent as $deptVal) {
                if (!isset($countData[$deptVal])) {
                    $countData[$deptVal][$abnormalNum]    = '0';
                    $countData[$deptVal][$modifyCountNum] = '0';
                    $countData[$deptVal]['banormalrate']  = '0.00';
                }
            }
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept => $dataArr) {
            if (!$dataArr[$abnormalNum] && !$dataArr[$modifyCountNum] && !in_array($dept, $needShowDeptList)) {
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }
        //反馈用到的数据，给接下来生成快照的方法使用。
        $useIDS['modifycncc'] = array_unique($useIDS['modifycncc']);
        $useIDS['modify']     = array_unique($useIDS['modify']);
        $useIDS['credit']     = array_unique($useIDS['credit']);

        return [
            'useids'       => $useIDS,
            'deptcolumids' => $modifyabnormalIdArr,
            'staticdata'   => $countData,
            'detail'       => $modifyabnormalDetailArr,
            'deptids'      => $deptIDArr,
            'multkey'      => ['modify', 'modifycncc', 'credit'],
        ];
    }

    /**
     * 获取完结变更单元数据
     * @param $start
     * @param $end
     * @param $deptID
     * @return array
     */
    public function getModifyFinishData($start, $end, $deptID)
    {
        $realusedepts = [];
        if ($deptID) {
            $realusedepts = $this->loadModel('secondmonthreport')->getRealUseDepts($deptID);
        }

        //去除 删除的 ，迭代34去掉 待提交的
        if (-1 == $deptID) {
            $modifyList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`app`,`level`,`desc`,`createdBy`,`type`,realEndTime')
                ->from(TABLE_MODIFY)
                ->where('status')->in(['modifyerror', 'modifysuccesspart', 'modifyfail', 'modifyrollback', 'modifysuccess', 'closed'])
                ->andWhere(' (createdDept=0 or createdDept is null) ')
                ->andWhere('realEndTime')->between($start, $end)
                ->orderBy('id_desc')
                ->fetchAll('id');
            $modifycnccList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`belongedApp` as `app`,`level`,`desc`,`createdBy`,`type`,actualEnd as realEndTime')
                ->from(TABLE_MODIFYCNCC)
                ->where('status')->in(['modifyfail', 'modifysuccesspart', 'modifysuccess', 'closed'])
                ->andWhere('deleted')->ne('1')
                ->andWhere(' (createdDept=0 or createdDept is null) ')
                ->andWhere('actualEnd')->between($start, $end)
                ->orderBy('id_desc')
                ->fetchAll('id');
            $creditList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`appIds`,`level`,`desc`,`createdBy`,`emergencyType` as type,onlineTime as realEndTime')
                ->from(TABLE_CREDIT)
                ->where('status')->in(['fail', 'successpart', 'modifyerror', 'modifyrollback', 'success'])
                ->andWhere(' (createdDept=0 or createdDept is null) ')
                ->andWhere('onlineTime')->between($start, $end)
                ->orderBy('id_desc')
                ->fetchAll('id');
        } else {
            $modifyList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`app`,`level`,`desc`,`createdBy`,`type`,realEndTime')
                ->from(TABLE_MODIFY)
                ->where('status')->in(['modifyerror', 'modifysuccesspart', 'modifyfail', 'modifyrollback', 'modifysuccess', 'closed'])
                ->beginIF($deptID && $realusedepts)->andWhere('createdDept')->in($realusedepts)->fi()
                ->andWhere('realEndTime')->between($start, $end)
                ->orderBy('id_desc')
                ->fetchAll('id');
            $modifycnccList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`belongedApp` as `app`,`level`,`desc`,`createdBy`,`type`,actualEnd as realEndTime')
                ->from(TABLE_MODIFYCNCC)
                ->where('status')->in(['modifyfail', 'modifysuccesspart', 'modifysuccess', 'closed'])
                ->andWhere('deleted')->ne('1')
                ->beginIF($deptID && $realusedepts)->andWhere('createdDept')->in($realusedepts)->fi()
                ->andWhere('actualEnd')->between($start, $end)
                ->orderBy('id_desc')
                ->fetchAll('id');
            $creditList = $this->dao->select('`id`,`status`,`mode`,`createdDept`,`code`,`appIds`,`level`,`desc`,`createdBy`,`emergencyType` as type,onlineTime as realEndTime')
                ->from(TABLE_CREDIT)
                ->where('status')->in(['fail', 'successpart', 'modifyerror', 'modifyrollback', 'success'])
                ->beginIF($deptID && $realusedepts)->andWhere('createdDept')->in($realusedepts)->fi()
                ->andWhere('onlineTime')->between($start, $end)
                ->orderBy('id_desc')
                ->fetchAll('id');
        }

        foreach ($modifyList as $modify) {
            $modify->exybtjsource = 'modify';
        }
        foreach ($modifycnccList as $modifycncc) {
            $modifycncc->exybtjsource = 'modifycncc';
        }
        foreach ($creditList as $credit) {
            $credit->exybtjsource = 'credit';
        }

        return [
            'modify'     => array_values($modifyList),
            'modifycncc' => array_values($modifycnccList),
            'credit'     => array_values($creditList),
        ];
    }

    /**
     * 现场支持季度统计表
     * @param $timeFrame
     * @return mixed
     */
    public function supportHistoryQuarter($timeFrame)
    {
        $sourceResultData = $this->loadModel('secondmonthreport')->getSupportDataList($this->yearStart, $this->yearEnd, 0, $timeFrame['stype'], 1);

        $staticResult = $this->supportQuarterStaticInfo($sourceResultData, 0);

        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->supportSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->supportQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    /**
     * 现场支持季度统计
     * @param $list
     * @return array
     */
    public function supportQuarterStatic($list)
    {
        $arr = [
            'hour'  => 0,
            'day'   => 0,
            'month' => 0,
            'year'  => 0,
        ];
        $data[0] = [
            'quarter' => $arr,
            'yearSum' => $arr,
        ];
        $supportTypeList = $this->lang->secondmonthreport->supportMapStypeList;
        $deptParent      = $this->deptParent;
        $deptList        = $this->deptList;

        $innerFunction = function ($item, $info, $is = false) {
            $day   = 0;
            $workh = ceil($item->workh);
            if ($workh % 4 > 0) {
                $day = floor($workh / 4) / 2 + 0.5;
            } elseif (0 == $workh % 4) {
                $day = floor($workh / 4) / 2;
            }

            $info['hour'] += $item->workh;
            $info['day']  += $day;

            return $info;
        };

        foreach ($list as $item) {
            $item->dept = empty($item->dept) ? -1 : $item->dept;
            if (!in_array($item->dept, $deptList) || !isset($supportTypeList[$item->stype])) {
                continue;
            }

            if (!isset($data[$deptParent[$item->dept]])) {
                $data[$deptParent[$item->dept]] = [
                    'quarter' => $arr,
                    'yearSum' => $arr,
                ];
            }

            if ($this->quarterStart <= $item->sdate && $this->quarterEnd >= $item->sdate) {
                $data[0]['quarter']                        = $innerFunction($item, $data[0]['quarter']);
                $data[$deptParent[$item->dept]]['quarter'] = $innerFunction($item, $data[$deptParent[$item->dept]]['quarter']);
            }
            if ($this->yearStart <= $item->sdate && $this->yearEnd >= $item->sdate) {
                $data[0]['yearSum']                        = $innerFunction($item, $data[0]['yearSum'], true);
                $data[$deptParent[$item->dept]]['yearSum'] = $innerFunction($item, $data[$deptParent[$item->dept]]['yearSum']);
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($value as $key => $val) {
                if (0 < $val['hour']) {
                    $data[$deptId][$key]['month'] = number_format($val['day'] / ($this->lang->secondmonthreport->monthReportWorkHours['workHours']), 2);
                    $data[$deptId][$key]['year']  = number_format($val['day'] / ($this->lang->secondmonthreport->monthReportWorkHours['workHours'] * 12), 2);
                }
            }
        }

        return $data;
    }

    /**
     * 现场支持季度统计详情
     * @param $supportData
     * @param $deptID
     * @return array
     */
    public function supportQuarterStaticInfo($supportData, $deptID = 0)
    {
        $this->app->loadLang('support');

        $useIDS           = [];
        $supportDetailArr = [];
        $supportIdArr     = [];
        $deptIDArr        = [];
        $countData        = [];
        $supportTypeList  = $this->lang->secondmonthreport->supportMapStypeList;
        $deptParent       = $this->deptParent;
        $needShowDeptList = $this->deptList;

        //按部门-》分类 统计
        foreach ($supportData as $support) {
            $support->dept = empty($support->dept) ? -1 : $support->dept;
            if (!in_array($support->dept, $needShowDeptList) || !isset($supportTypeList[$support->stype])) {
                continue;
            }

            if (!isset($countData[$deptParent[$support->dept]][$supportTypeList[$support->stype]])) {
                $countData[$deptParent[$support->dept]][$supportTypeList[$support->stype]] = $support->workh;
            } else {
                $countData[$deptParent[$support->dept]][$supportTypeList[$support->stype]] += $support->workh;
            }
            $useIDS[]                                                                                       = $support->id;
            $supportIdArr[$deptParent[$support->dept]][$supportTypeList[$support->stype]][]                 = $support->id;
            $supportDetailArr[$deptParent[$support->dept]][$supportTypeList[$support->stype]][$support->id] = $support;
            $supportIdArr[$deptParent[$support->dept]]['total'][]                                           = $support->id;
            $supportDetailArr[$deptParent[$support->dept]]['total'][$support->id]                           = $support;
        }

        //每组数据补全分类
        foreach ($supportTypeList as $type => $val) {
            if (!$val) {
                continue;
            }
            foreach ($countData as $dept => $dataArr) {
                if (!isset($dataArr[$val])) {
                    $countData[$dept][$val] = 0;
                }
            }
        }

        //补齐部门
        if (!$deptID) {
            foreach ($deptParent as $deptVal) {
                if (!isset($countData[$deptVal])) {
                    foreach ($this->lang->secondmonthreport->supportMapStypeList as $type => $val) {
                        $countData[$deptVal][$val] = 0;
                    }
                }
            }
        }

        //行补充合计
        foreach ($countData as $dept => $dataArr) {
            $countData[$dept]['total'] = array_sum($countData[$dept]);
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept => $dataArr) {
            if (!$dataArr['total'] && !in_array($dept, $needShowDeptList)) {
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }

        return [
            'useids'       => array_unique($useIDS),
            'deptcolumids' => $supportIdArr,
            'staticdata'   => $countData,
            'detail'       => $supportDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }

    public function workloadHistoryQuarter($timeFrame)
    {
        $sourceResultData = $this->loadModel('secondmonthreport')->getWorkloadDataList($this->yearStart, $this->yearEnd, 0, $timeFrame['stype'], 0);

        $staticResult = $this->workloadQuarterStaticInfo($sourceResultData, 0);

        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->workloadSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->workloadQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    public function workloadQuarterStatic($list): array
    {
        $arr = [
            'hour'  => 0,
            'day'   => 0,
            'month' => 0,
            'year'  => 0,
        ];
        $data[0] = [
            'quarter' => $arr,
            'yearSum' => $arr,
        ];
        $deptParent = $this->deptParent;
        $deptList   = $this->deptList;

        foreach ($list as $item) {
            $item->deptID = empty($item->deptID) ? -1 : $item->deptID;
            $flag         = (false !== strpos($item->name, 'CFIT-Q-') && 1 == $item->source)
                || ((false !== strpos($item->name, 'CFIT-D-')) && 1 == $item->source)
                || (false !== strpos($item->name, 'CFIT-T-') && 1 == $item->source);
//                || (0 == $item->source || (1 == $item->source && '其他类型' == $item->name));

            if (!in_array($item->deptID, $deptList) || !$flag) {
                continue;
            }

            if (!isset($data[$deptParent[$item->deptID]])) {
                $data[$deptParent[$item->deptID]] = [
                    'quarter' => $arr,
                    'yearSum' => $arr,
                ];
            }

            if ($this->quarterStart <= $item->date && $this->quarterEnd >= $item->date) {
                $data[0]['quarter']['hour']                          += $item->consumed;
                $data[$deptParent[$item->deptID]]['quarter']['hour'] += $item->consumed;
            }
            if ($this->yearStart <= $item->date && $this->yearEnd >= $item->date) {
                $data[0]['yearSum']['hour']                          += $item->consumed;
                $data[$deptParent[$item->deptID]]['yearSum']['hour'] += $item->consumed;
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($value as $key => $val) {
                if (0 < $val['hour']) {
                    $data[$deptId][$key] = $this->hourToOther($val);
                }
            }
        }

        return $data;
    }

    public function workloadQuarterStaticInfo($workloadData, $deptID = 0): array
    {
        $needShowDeptList  = $this->deptList;
        $deptParent        = $this->deptParent;
        $useIDS            = [];
        $workloadDetailArr = [];
        $workloadIdArr     = [];
        $deptIDArr         = [];
        $countData         = [];

        foreach ($workloadData as $effort) {
            $effort->deptID = empty($effort->deptID) ? -1 : $effort->deptID;
            if (!in_array($effort->deptID, $needShowDeptList)) {
                continue;
            }

            //问题单
            if (false !== strpos($effort->name, 'CFIT-Q-') && 1 == $effort->source) {
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['secondproblem'];
            //|| strpos($effort->name,'CFIT-WD-') !== false
            } elseif ((false !== strpos($effort->name, 'CFIT-D-')) && 1 == $effort->source) { //需求池内部 去除 需求池内部数据
                //需求单
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['seconddemand'];
            } elseif (false !== strpos($effort->name, 'CFIT-T-') && 1 == $effort->source) {
                $stype = $this->lang->secondmonthreport->workloadMapTypeList['secondorder'];
            } elseif (0 == $effort->source || (1 == $effort->source && '其他类型' == $effort->name)) {
                continue;
            //自建任务
                //$stype = $this->lang->secondmonthreport->workloadMapTypeList['secondcustom'];
            } else {
                continue;
            }

            if (!isset($countData[$deptParent[$effort->deptID]][$stype])) {
                $countData[$deptParent[$effort->deptID]][$stype] = $effort->consumed;
            } else {
                $countData[$deptParent[$effort->deptID]][$stype] += $effort->consumed;
            }
            $useIDS[]                                                                         = $effort->id;
            $workloadIdArr[$deptParent[$effort->deptID]][$stype][]                            = $effort->id;
            $workloadDetailArr[$deptParent[$effort->deptID]][$stype][$effort->id]             = $effort;
            $workloadIdArr[$deptParent[$effort->deptID]]['countPeopleMonth'][]                = $effort->id;
            $workloadDetailArr[$deptParent[$effort->deptID]]['countPeopleMonth'][$effort->id] = $effort;
        }

        //人月转换
        $monthworkload = (float)($this->lang->secondmonthreport->monthReportWorkHours['workHours']) * 8;
        foreach ($countData as $divdept => $divDataArr) {
            foreach ($divDataArr as $divTypeKey => $divTypeValue) {
                $countData[$divdept][$divTypeKey] = sprintf('%.2f', $divTypeValue / $monthworkload);
            }
        }

        //每组数据补全分类
        unset($this->lang->secondmonthreport->workloadMapTypeList['secondcustom']);
        foreach ($this->lang->secondmonthreport->workloadMapTypeList as $type => $val) {
            if (!$val) {
                continue;
            }
            foreach ($countData as $dept => $dataArr) {
                if (!isset($dataArr[$val])) {
                    $countData[$dept][$val] = '0.00';
                }
            }
        }

        //补齐部门
        if (!$deptID) {
            foreach ($deptParent as $deptVal) {
                if (!isset($countData[$deptVal])) {
                    foreach ($this->lang->secondmonthreport->workloadMapTypeList as $type => $val) {
                        if (!$val) {
                            continue;
                        }
                        $countData[$deptVal][$val] = '0.00';
                    }
                }
            }
        }

        //行补充合计
        foreach ($countData as $dept => $dataArr) {
            $countData[$dept]['countPeopleMonth'] = array_sum($countData[$dept]);
        }

        //剔除 不是统计部门中部门数据为 0 的数据
        foreach ($countData as $dept => $dataArr) {
            if (!$dataArr['countPeopleMonth'] && !in_array($dept, $needShowDeptList)) {
                unset($countData[$dept]);
                continue;
            }
            $deptIDArr[] = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return [
            'useids'       => array_unique($useIDS),
            'deptcolumids' => $workloadIdArr,
            'staticdata'   => $countData,
            'detail'       => $workloadDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }

    public function problemCompletedPlanHistoryQuarter($timeFrame)
    {
        //根据统计时间获取统计源数据
        $sourceResultData = $this->loadModel('secondmonthreport')->getProblemDataList($this->yearStart, $this->yearEnd, 0, $timeFrame['stype'], 0);

        //根据统计规则获取统计详情
        $staticResult = $this->problemCompletedPlanQuarterStaticInfo($sourceResultData, 0);
        //保存统计详情
        $timeFrame['isyearform'] = 4;
        $detailId                = $this->loadModel('secondmonthreport')->problemCompletedPlanSave(
            $staticResult['deptcolumids'],
            $staticResult['staticdata'],
            $timeFrame['stype'],
            $this->nowTime,
            $timeFrame
        );

        //根据统计规则获取统计报表
        $staticData = $this->problemCompletedPlanQuarterStatic($sourceResultData);

        //保存报表
        $timeFrame['isyearform'] = 3;
        $this->reportSave($staticData, $timeFrame, $detailId);

        return $staticResult['useids'];
    }

    public function problemCompletedPlanQuarterStatic($list)
    {
        $arr = [
            'total'    => 0,
            'noPlan'   => 0,
            'plan'     => 0,
            'planRate' => '0.00%',
        ];

        $data[0] = [
            'quarter' => $arr,
            'yearSum' => $arr,
        ];
        $problemModel  = $this->loadModel('problem');
        $deptParent    = $this->deptParent;
        $deptList      = $this->deptList;
        $innerFunction = function ($problem, $info) {
            ++$info['total'];

            if ('2' == $problem->completedPlan) {//未按计划解决
                ++$info['noPlan'];
            } elseif ('1' == $problem->completedPlan) {//按计划解决
                ++$info['plan'];
            }

            return $info;
        };
        foreach ($list as $item) {
            $item->PlannedTimeOfChange = '0000-00-00 00:00:00' == $item->PlannedTimeOfChange ? '' : $item->PlannedTimeOfChange;
            $item->acceptDept          = empty($item->acceptDept) ? -1 : $item->acceptDept;
            if (
                !in_array($item->acceptDept, $deptList)
                || 'noproblem' == $item->type
                || in_array($item->status, ['returned', 'confirmed', 'assigned'])
                || ('closed' == $item->status && empty($item->PlannedTimeOfChange))
                || ($this->yearStart > $item->PlannedTimeOfChange || $this->yearEnd < $item->PlannedTimeOfChange)
            ) {
                continue;
            }

            if (!isset($data[$deptParent[$item->acceptDept]])) {
                $data[$deptParent[$item->acceptDept]] = [
                    'quarter' => $arr,
                    'yearSum' => $arr,
                ];
            }
            if ($this->quarterStart <= $item->PlannedTimeOfChange && $this->quarterEnd >= $item->PlannedTimeOfChange) {
                $data[0]['quarter']                              = $innerFunction($item, $data[0]['quarter']);
                $data[$deptParent[$item->acceptDept]]['quarter'] = $innerFunction($item, $data[$deptParent[$item->acceptDept]]['quarter']);
            }
            if ($this->yearStart <= $item->PlannedTimeOfChange && $this->yearEnd >= $item->PlannedTimeOfChange) {
                $data[0]['yearSum']                              = $innerFunction($item, $data[0]['yearSum']);
                $data[$deptParent[$item->acceptDept]]['yearSum'] = $innerFunction($item, $data[$deptParent[$item->acceptDept]]['yearSum']);
            }
        }

        foreach ($data as $deptId => $value) {
            foreach ($value as $key => $val) {
                if (0 < $val['plan']) {
                    $data[$deptId][$key]['planRate'] = number_format($val['plan'] / $val['total'] * 100, 2) . '%';
                }
            }
        }

        return $data;
    }

    public function problemCompletedPlanQuarterStaticInfo($problemData, $deptID = 0)
    {
        $arr = [
            'noPlan'   => 0,
            'plan'     => 0,
            'total'    => 0,
            'planRate' => 0,
        ];
        $data             = [];
        $deptParent       = $this->deptParent;
        $needShowDeptList = $this->deptList;
        $overallsIDS      = []; //整体统计
        $problemDetailArr = [];
        $problemIdArr     = [];
        $deptIDArr        = [];

        foreach ($problemData as $item) {
            $item->PlannedTimeOfChange = '0000-00-00 00:00:00' == $item->PlannedTimeOfChange ? '' : $item->PlannedTimeOfChange;
            $item->acceptDept          = empty($item->acceptDept) ? -1 : $item->acceptDept;

            if (
                !in_array($item->acceptDept, $needShowDeptList)
                || 'noproblem' == $item->type
                || in_array($item->status, ['returned', 'confirmed', 'assigned'])
                || ('closed' == $item->status && empty($item->PlannedTimeOfChange))
                || ($this->yearStart > $item->PlannedTimeOfChange || $this->yearEnd < $item->PlannedTimeOfChange)
            ) {
                continue;
            }
            if (!$item->acceptDept) {
                $item->acceptDept = -1;
            }
            //初始化统计报表
            if (!isset($data[$deptParent[$item->acceptDept]])) {
                $data[$deptParent[$item->acceptDept]] = $arr;
            }

            ++$data[$deptParent[$item->acceptDept]]['total'];
            $overallsIDS[]                                                        = $item->id;
            $problemIdArr[$deptParent[$item->acceptDept]]['total'][]              = $item->id;
            $problemDetailArr[$deptParent[$item->acceptDept]]['total'][$item->id] = $item;

            if ('2' == $item->completedPlan) {//未按计划解决
                ++$data[$deptParent[$item->acceptDept]]['noPlan'];
                $problemIdArr[$deptParent[$item->acceptDept]]['noPlan'][]              = $item->id;
                $problemDetailArr[$deptParent[$item->acceptDept]]['noPlan'][$item->id] = $item;
            } elseif ('1' == $item->completedPlan) {//按计划解决
                ++$data[$deptParent[$item->acceptDept]]['plan'];
                $problemIdArr[$deptParent[$item->acceptDept]]['plan'][]              = $item->id;
                $problemDetailArr[$deptParent[$item->acceptDept]]['plan'][$item->id] = $item;
            }
        }

        if (!$deptID) {
            foreach ($deptParent as $alldept) {
                if (!isset($data[$alldept])) {
                    //部门为空 且 无数据时 不需要候补
                    $data[$alldept] = $arr;
                }
            }
        }

        //整体统计表剔除 不是统计部门中部门数据为 0 的数据
        foreach ($data as $dept => $dataArr) {
            if ($dataArr['plan'] > 0) {
                $data[$dept]['planRate'] = number_format(($dataArr['plan'] / $dataArr['total']) * 100, 2);
            }
            $deptIDArr[] = $dept;
        }

        //反馈用到的数据，给接下来生成快照的方法使用。
        return [
            'useids'       => array_unique($overallsIDS),
            'deptcolumids' => $problemIdArr,
            'staticdata'   => $data,
            'detail'       => $problemDetailArr,
            'deptids'      => $deptIDArr,
        ];
    }
}
