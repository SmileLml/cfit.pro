<?php
class supportdaydetailResidentsupport extends residentsupportModel
{
    /**
     *获得某一个模板下某个部门的用户值班信息
     *
     * @param $templateId
     * @param $deptId
     * @param string $select
     * @return bool
     */
    public function getDutyUserListByTemplateAndDeptId($templateId, $deptId, $select = '*'){
        if(!($templateId && $deptId)){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("templateId")->eq($templateId)
            ->andWhere('dutyUserDept')->eq($deptId)
            ->andWhere('deleted')->eq('0')
            ->andWhere('dutyUser')->ne('')
            ->fetchAll();
        return $data;
    }


    /**
     *获得模板下的值班用户列表
     *
     * @param $templateId
     * @param array $dayIds
     * @param $deptId
     * @return array
     */
    public function getDutyUserListByTemplateId($templateId, $dayIds = [], $deptId = 0){
        $data = [];
        if(!$templateId){
            return $data;
        }
        //值班详情
        $dayDetailList = $this->dao->select('t1.*')
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias("t1")
            ->leftjoin(TABLE_RESIDENT_SUPPORT_DAY)->alias("t2")
            ->on("t2.id=t1.dayId")
            ->where("t1.templateId")->eq($templateId)
            ->andWhere('t1.deleted')->eq('0')
            ->beginIF(!empty($dayIds))->andWhere('t1.dayId')->in($dayIds)->fi()
            ->beginIF(!empty($deptId))->andWhere('t1.dutyUserDept')->eq($deptId)->fi()
            ->orderby("t2.dutyDate_asc")
            ->fetchAll();
        if(!$dayDetailList){
            return $data;
        }
        foreach ($dayDetailList as $val){
            $dayId  = $val->dayId;
            $deptId = $val->dutyUserDept;
            $data[$dayId][$deptId][] = $val;
        }
        return $data;
    }

    /**
     *通过值班用户详情ids获得列表
     *
     * @param $ids
     * @param string $select
     * @return bool
     */
    public function getDutyUserListByIds($ids, $select = '*'){
        if(!($ids)){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("id")->in($ids)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        return $data;
    }

    /**
     *获得指定天的第一个值班人员列表
     *
     * @param $dayIds
     * @return mixed
     */
    public function getDayFirstUserList($dayIds){
        $data = [];
        if(!($dayIds)){
            return $data;
        }
        $ret = $this->dao->select('dayId, dutyUser')
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("dayId")->in($dayIds)
            ->andWhere('deleted')->eq('0')
            ->groupBy('dayId')
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'dutyUser', 'dayId');
        }
        return $data;
    }
    /**
     *获得指定某一天的第一个值班人员
     *
     * @param $dayId
     * @return mixed
     */
    public function getDayFirstDutyUser($dayId){
        $firstDutyUser = '';
        if(!($dayId)){
            return $firstDutyUser;
        }
        $ret = $this->dao->select('id,dutyUser')
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("dayId")->eq($dayId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $firstDutyUser = $ret->dutyUser;
        }
        return $firstDutyUser;
    }

    /**
     *获得模板部门下的值班人员
     *
     * @param $templateIds
     * @return array
     */
    public function getDutyUserListGroupTempAndDeptId($templateIds){
        $data = [];
        if(!$templateIds){
            return $data;
        }
        $ret = $this->dao->select('templateId, dutyUserDept, group_concat(dutyUser) as dutyUsers')
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("templateId")->in($templateIds)
            ->andWhere('deleted')->eq('0')
            ->groupBy('templateId, dutyUserDept')
            ->fetchAll('');
        if($ret){
            foreach ($ret as $val){
                $templateId = $val->templateId;
                $dutyUserDept = $val->dutyUserDept;
                $dutyUsers = $val->dutyUsers;
                $data[$templateId][$dutyUserDept] = $dutyUsers;
            }
        }
        return $data;
    }

    /**
     *获得未排班数量
     *
     * @param $templateId
     * @param $deptId
     * @return int
     */
    public function getUnSchedulingCount($templateId, $deptId){
        $count = 0;
        if(!($templateId && $deptId)){
            return $count;
        }
        $data = $this->dao->select('count(id) as count')
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("templateId")->eq($templateId)
            ->andWhere('dutyUserDept')->eq($deptId)
            ->andWhere('deleted')->eq('0')
            ->andWhere('dutyUser')->eq('')
            ->fetch();
        if($data){
            $count = $data->count;
        }
        return $count;
    }

    /**
     *通过日id获得当日值班列表
     *
     * @param $dayId
     * @param string $select
     * @return bool
     */
    public function getDutyUserListByDayId($dayId, $select = '*'){
        if(!($dayId)){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)
            ->where("dayId")->eq($dayId)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        return $data;
    }


    /**
     *根据条件查询值班人列表
     *
     * @param $condition
     * @param string $select
     * @return array
     */
    public function getDutyUserListByCondition($condition, $select = '*'){
        $data = [];
        if(!($condition)){
            return $data;
        }
        $sql  = "select ".$select." from " . TABLE_RESIDENT_SUPPORT_DAY_DETAIL .
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

}

