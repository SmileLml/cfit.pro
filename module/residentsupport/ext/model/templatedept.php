<?php

/**
 *获得模板下某一个部门信息
 *
 * @param $templateDeptId
 * @param string $select
 * @return bool
 */
public function getTemplateDeptInfoById($templateDeptId, $select = '*'){
    return $this->loadExtension('templatedept')->getTemplateDeptInfoById($templateDeptId, $select);
}


/**
 *根据模板和部门获取模板下的部门信息
 *
 * @param $templateId
 * @param $deptId
 * @param string $select
 * @return mixed
 */
public function getTemplateDeptInfoByTemAndDeptId($templateId, $deptId, $select = '*'){
    return $this->loadExtension('templatedept')->getTemplateDeptInfoByTemAndDeptId($templateId, $deptId, $select);
}

/**
 *获得申请提交的下一个状态
 *
 * @param $templateDeptInfo
 * @return string
 */
public function getTemplateDeptSubmitNextStatus($templateDeptInfo){
    return $this->loadExtension('templatedept')->getTemplateDeptSubmitNextStatus($templateDeptInfo);
}

/**
 *获得审批的下一状态
 *
 * @param $templateDeptInfo
 * @param $reviewResult
 * @return mixed
 */
public function getTemplateDeptReviewNextStatus($templateDeptInfo, $reviewResult){
    return $this->loadExtension('templatedept')->getTemplateDeptReviewNextStatus($templateDeptInfo, $reviewResult);
}

/**
 *获得下一状态的处理用户
 *
 * @param $templateDeptInfo
 * @param $nextStatus
 * @param string $postUsers
 * @return string
 */
public function getTemplateDeptNextDealUsers($templateDeptInfo, $nextStatus, $postUsers = ''){
    return $this->loadExtension('templatedept')->getTemplateDeptNextDealUsers($templateDeptInfo, $nextStatus, $postUsers);
}

/**
 *获得对应审核节点的标识
 *
 * @param $status
 * @return string
 */
public function getTemplateDepReviewNodeCode($status){
    return $this->loadExtension('templatedept')->getTemplateDepReviewNodeCode($status);
}


/**
 *根据模板和部门获取模板下的部门信息
 *
 * @param $templateId
 * @param $deptIds
 * @param string $select
 * @return mixed
 */
public function getTemplateDeptListByTemAndDeptIds($templateId, $deptIds = [], $select = '*'){
    return $this->loadExtension('templatedept')->getTemplateDeptListByTemAndDeptIds($templateId, $deptIds, $select);
}

/**
 *获得产创部审核人
 *
 * @param $type
 * @return string
 */
public function getPdReviewDealUsers($type){
    return $this->loadExtension('templatedept')->getPdReviewDealUsers($type);
}

/**
 *根据模板和部门获取模板下的部门信息
 *
 * @param $templateId
 * @param $deptIds
 * @param string $select
 * @return mixed
 */
public function getUnCheckPassDutyDeptList($templateId, $deptIds = [], $select = '*'){
    return $this->loadExtension('templatedept')->getUnCheckPassDutyDeptList($templateId, $deptIds, $select);
}



