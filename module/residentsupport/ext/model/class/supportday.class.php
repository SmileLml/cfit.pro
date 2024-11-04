<?php
class supportdayResidentsupport extends residentsupportModel
{
    /**
     *获得值班组长
     *
     * @param $dayId
     * @param array $dutyGroupLeaders
     * @param string $select
     * @return bool
     */
    public function getDutyGroupLeaderInfo($dayId, $dutyGroupLeaders = [], $select = '*'){
        if(!$dayId){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where("id")->eq($dayId)
            ->beginIF(is_array($dutyGroupLeaders) && !empty($dutyGroupLeaders))->andWhere('dutyGroupLeader')->in($dutyGroupLeaders)->fi()
            ->andWhere('deleted')->eq('0')
            ->andWhere('dutyGroupLeader')->ne('')
            ->fetch();
        return $data;
    }

    /**
     *获得模板下的日列表
     *
     * @param $templateId
     * @param string $startDate
     * @param string $endDate
     * @param string $select
     * @return array
     */
    public function getTempDayList($templateId, $startDate = '', $endDate = '', $select = '*'){
        $data = [];
        if(!$templateId){
            return $data;
        }
        $dayList = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where("templateId")->eq($templateId)
            ->andWhere('deleted')->eq('0')
            ->beginIF(!empty($startDate))->andWhere('dutyDate')->ge($startDate)->fi()
            ->beginIF(!empty($endDate))->andWhere('dutyDate')->le($endDate)->fi()
            ->fetchAll('id');
        if($dayList){
            $data = $dayList;
        }
        return $data;
    }

    /**
     *通过日ids获得列表
     *
     * @param $ids
     * @param string $select
     * @return array
     */
    public function getTempDayListByIds($ids, $select = '*'){
        $data = [];
        if(!$ids){
            return $data;
        }
        $dayList = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where("id")->in($ids)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        if($dayList){
            $data = $dayList;
        }
        return $data;
    }
    /**
     *获得模板的值班组长
     *
     * @param $templateIds
     * @return array
     */
    public function getGroupLeaderListGroupTempId($templateIds){
        $data = [];
        if(!$templateIds){
            return $data;
        }
        $dutyGroupLeaderList = $this->dao->select('templateId, group_concat(distinct dutyGroupLeader) as dutyGroupLeaders')
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where("templateId")->in($templateIds)
            ->andWhere('deleted')->eq('0')
            ->andWhere('dutyGroupLeader')->ne('')
            ->groupBy('templateId')
            ->fetchAll('templateId');
        if(!$dutyGroupLeaderList){
            return $data;
        }
        $userAccounts = [];
        foreach ($dutyGroupLeaderList as $val){
            $dutyGroupLeaders = $val->dutyGroupLeaders;
            $currentUserAccounts = explode(',', $dutyGroupLeaders);
            $userAccounts = array_merge($userAccounts, $currentUserAccounts);
        }

        $userList = $this->loadModel('user')->getUserInfoListByAccounts($userAccounts, 'account,realname,dept');
        $deptIds = array_column($userList, 'dept');
        $deptList = $this->loadModel('dept')->getDeptListByIds($deptIds, 'id,name');
        $deptList = array_column($deptList, null, 'id');

        foreach ($dutyGroupLeaderList as $templateId => $val){
            $dutyGroupLeaders = $val->dutyGroupLeaders;
            $currentUserAccounts = explode(',', $dutyGroupLeaders);
            foreach ($currentUserAccounts as $userAccount){
                $uerInfo = zget($userList, $userAccount);
                $deptId = zget($uerInfo, 'dept');
                $deptInfo = zget($deptList, $deptId);
                $deptName = zget($deptInfo, 'name');

                $temp = new stdClass();
                $temp->dutyGroupLeader = $userAccount;
                $temp->realname = zget($uerInfo, 'realname');
                $temp->deptId = $deptId;
                $temp->deptName = $deptName;
                $data[$templateId][] = $temp;
            }
        }
        return $data;
    }

    /**
     * 获得排班日信息
     *
     * @param $dayId
     * @return bool
     */
    public function getTempDayInfoById($dayId){
        if(!$dayId){
            return false;
        }
        $data = $this->dao->select('*')
            ->from(TABLE_RESIDENT_SUPPORT_DAY)
            ->where("id")->eq($dayId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($data){
            $templateId = $data->templateId;
            //模板信息
            $templateInfo = $this->loadModel('residentsupport')->getTemplateInfoById($templateId);
            $data->templateInfo = $templateInfo;
        }
        return $data;
    }

    /**
     *根据条件查询值班日期列表
     *
     * @param $condition
     * @param string $select
     * @return array
     */
    public function getDutyDayListByCondition($condition, $select = '*'){
        $data = [];
        if(!($condition)){
            return $data;
        }
        $sql  = "select ".$select." from " . TABLE_RESIDENT_SUPPORT_DAY .
            " where 1 and deleted = '0'";
        foreach ($condition as $key => $val){
            if(is_array($val)){
                $valStr = implode(',', $val);
                $sql .= " and {$key} in (".$valStr.")";
            }else{
                $sql .= " and {$key} = '{$val}'";
            }
        }
        $ret = $this->dao->query($sql)->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 根据模板和日期获得某天值班信息
     *
     * @param $templateId
     * @param $dutyDate
     * @return bool
     */
    public function getDutyDayInfo($templateId, $dutyDate){
        if(!($templateId && $dutyDate)){
            return false;
        }
        $ret = $this->dao->select('t1.*, group_concat(distinct t2.dutyUser) as dutyUsers')
            ->from(TABLE_RESIDENT_SUPPORT_DAY)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias("t2")
            ->on("t1.id=t2.dayId")
            ->where("t1.templateId")->eq($templateId)
            ->andWhere('t1.dutyDate')->eq($dutyDate)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.templateId')->eq($templateId)
            ->andWhere('t2.deleted')->eq('0')
            ->groupBy('t1.id')
            ->fetch();
        return $ret;
    }
}

