<?php
class templateResidentsupport extends residentsupportModel
{
    /**
     *获得模板下某一个部门信息
     *
     * @param $templateDeptId
     * @param string $select
     * @return bool
     */
    public function getTemplateInfoById($templateDeptId, $select = '*'){
        if(!$templateDeptId){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)
            ->where("id")->eq($templateDeptId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        return $data;
    }


    /**
     *根据条件查询排班模板
     *
     * @param $condition
     * @param string $select
     * @return mixed
     */
    public function getTemplateInfoBySchedulingCondition($condition, $select = '*'){
        $ret = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE)
            ->where("id")->eq($condition->templateId)
            ->andWhere('startDate')->le($condition->startDate)
            ->andWhere('endDate')->ge($condition->endDate)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        return $ret;
    }

    /**
     *根据条件查询值班模板列表
     *
     * @param $condition
     * @param string $select
     * @return array
     */
    public function getTemplateListByCondition($condition, $select = '*'){
        $data = [];
        if(!($condition)){
            return $data;
        }
        $sql  = "select ".$select." from " . TABLE_RESIDENT_SUPPORT_TEMPLATE .
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

