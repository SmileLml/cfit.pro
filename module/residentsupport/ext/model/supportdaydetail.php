<?php

/**
 *获得模板信息
 *
 * @param $templateDeptId
 * @param string $select
 * @return bool
 */
public function getDutyUserListByTemplateAndDeptId($templateId, $deptId, $select = '*'){
    return $this->loadExtension('supportdaydetail')->getDutyUserListByTemplateAndDeptId($templateId, $deptId, $select);
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
    return $this->loadExtension('supportdaydetail')->getDutyUserListByTemplateId($templateId, $dayIds, $deptId);
}

/**
 *通过值班用户详情ids获得列表
 *
 * @param $ids
 * @param string $select
 * @return bool
 */
public function getDutyUserListByIds($ids, $select = '*'){
    return $this->loadExtension('supportdaydetail')->getDutyUserListByIds($ids, $select);
}

/**
 *获得指定天的第一个值班人员列表
 *
 * @param $dayIds
 * @return mixed
 */
public function getDayFirstUserList($dayIds){
    return $this->loadExtension('supportdaydetail')->getDayFirstUserList($dayIds);
}

/**
 *获得指定某一天的第一个值班人员
 *
 * @param $dayId
 * @return mixed
 */
public function getDayFirstDutyUser($dayId){
    return $this->loadExtension('supportdaydetail')->getDayFirstDutyUser($dayId);
}
/**
 *获得模板部门下的值班人员
 *
 * @param $templateIds
 * @return array
 */
public function getDutyUserListGroupTempAndDeptId($templateIds){
    return $this->loadExtension('supportdaydetail')->getDutyUserListGroupTempAndDeptId($templateIds);
}

/**
 *获得未排班人员数量
 *
 * @param $templateId
 * @param $deptId
 * @return mixed
 */
public function getUnSchedulingCount($templateId, $deptId){
    return $this->loadExtension('supportdaydetail')->getUnSchedulingCount($templateId, $deptId);
}

/**
 * 按天获得值班详情列表
 *
 * @param $dayId
 * @param string $select
 * @return mixed
 */
public function getDutyUserListByDayId($dayId, $select = '*'){
    return $this->loadExtension('supportdaydetail')->getDutyUserListByDayId($dayId, $select);
}

/**
 *根据条件查询值班人列表
 *
 * @param $condition
 * @param string $select
 * @return array
 */
public function getDutyUserListByCondition($condition, $select = '*'){
    return $this->loadExtension('supportdaydetail')->getDutyUserListByCondition($condition, $select);
}


