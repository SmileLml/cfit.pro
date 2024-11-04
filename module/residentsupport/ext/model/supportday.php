<?php
/**
 * 获得值班组长
 *
 * @param $dayId
 * @param array $dutyGroupLeaders
 * @param string $select
 * @return mixed
 */
public function getDutyGroupLeaderInfo($dayId, $dutyGroupLeaders = [], $select = '*'){
    return $this->loadExtension('supportday')->getDutyGroupLeaderInfo($dayId, $dutyGroupLeaders, $select);
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
    return $this->loadExtension('supportday')->getTempDayList($templateId, $startDate, $endDate, $select);
}

/**
 *通过日ids获得列表
 *
 * @param $ids
 * @param string $select
 * @return array
 */
public function getTempDayListByIds($ids, $select = '*'){
    return $this->loadExtension('supportday')->getTempDayListByIds($ids, $select);
}

/**
 * 获得模板id的值班组长
 *
 * @param $templateIds
 * @return mixed
 */
public function getGroupLeaderListGroupTempId($templateIds){
    return $this->loadExtension('supportday')->getGroupLeaderListGroupTempId($templateIds);
}

/**
 * 获得排班日信息
 *
 * @param $dayId
 * @return bool
 */
public function getTempDayInfoById($dayId){
    return $this->loadExtension('supportday')->getTempDayInfoById($dayId);
}


/**
 *根据条件查询值班日期列表
 *
 * @param $condition
 * @param string $select
 * @return array
 */
public function getDutyDayListByCondition($condition, $select = '*'){
    return $this->loadExtension('supportday')->getDutyDayListByCondition($condition, $select);
}

/**
 * 获得某一模板下的值班日信息
 *
 * @param $templateId
 * @param $dutyDate
 * @return mixed
 */
public function getDutyDayInfo($templateId, $dutyDate){
    return $this->loadExtension('supportday')->getDutyDayInfo($templateId, $dutyDate);
}

