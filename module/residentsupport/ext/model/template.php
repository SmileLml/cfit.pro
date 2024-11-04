<?php

/**
 *获得模板信息
 *
 * @param $templateDeptId
 * @param string $select
 * @return bool
 */
public function getTemplateInfoById($templateDeptId, $select = '*'){
    return $this->loadExtension('template')->getTemplateInfoById($templateDeptId, $select);
}

/**
 * 根据排期条件查询模板列表
 *
 * @param $condition
 * @param string $select
 * @return array
 */
public function getTemplateInfoBySchedulingCondition($condition, $select = '*'){
    return $this->loadExtension('template')->getTemplateInfoBySchedulingCondition($condition, $select);
}

/**
 *根据条件查询值班模板列表
 *
 * @param $condition
 * @param string $select
 * @return array
 */
public function getTemplateListByCondition($condition, $select = '*'){
    return $this->loadExtension('template')->getTemplateListByCondition($condition, $select);
}
