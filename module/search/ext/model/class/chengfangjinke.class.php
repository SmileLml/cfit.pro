<?php
class chengfangjinkeSearch extends searchModel
{
    /**
     * Project: chengfangjinke
     * Method: buildQuery
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:49
     * Desc: This is the code comment. This method is called buildQuery.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function buildQuery()
    {
        /* Init vars. */
        $where        = '';
        $groupItems   = $this->config->search->groupItems;
        $groupAndOr   = strtoupper($this->post->groupAndOr);
        $module       = $this->session->searchParams['module'];
        $searchParams = $module . 'searchParams';
        $fieldParams  = json_decode($_SESSION[$searchParams]['fieldParams']);
        $scoreNum     = 0;

        if($groupAndOr != 'AND' and $groupAndOr != 'OR') $groupAndOr = 'AND';

        for($i = 1; $i <= $groupItems * 2; $i ++)
        {
            /* The and or between two groups. */
            if($i == 1) $where .= '(( 1  ';
            if($i == $groupItems + 1) $where .= " ) $groupAndOr ( 1 ";

            /* Set var names. */
            $fieldName    = "field$i";
            $andOrName    = "andOr$i";
            $operatorName = "operator$i";
            $valueName    = "value$i";

            /* Fix bug #2704. */
            $field = $this->post->$fieldName;
            if(isset($fieldParams->$field) and $fieldParams->$field->control == 'input' and $this->post->$valueName === '0') $this->post->$valueName = 'ZERO';
            if($field == 'id' and $this->post->$valueName === '0') $this->post->$valueName = 'ZERO';

            /* Skip empty values. */
            if($this->post->$valueName == false) continue;
            if($this->post->$valueName == 'ZERO') $this->post->$valueName = 0;   // ZERO is special, stands to 0.
            if(isset($fieldParams->$field) and $fieldParams->$field->control == 'select' and $this->post->$valueName == 'null') $this->post->$valueName = '';   // Null is special, stands to empty if control is select. Fix bug #3279.

            $scoreNum += 1;

            /* Set and or. */
            $andOr = strtoupper($this->post->$andOrName);
            if($andOr != 'AND' and $andOr != 'OR') $andOr = 'AND';

            /* Set operator. */
            $value    = addcslashes(trim($this->post->$valueName), '%');
            $operator = $this->post->$operatorName;
            if(!isset($this->lang->search->operators[$operator])) $operator = '=';

            /* Set condition. */
            $condition = '';
            //授权管理转换待处理人
            if($module == 'modify' and $field == 'dealUser' and $operator == '='){
                $this->loadModel('modify');
                $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('modify', $value);
                if(!empty($dealUserList)){
                    $dealUserList = explode(',', $dealUserList);
                    foreach ($dealUserList as $dealUser){
                        if(strpos($condition, 'FIND_IN_SET') !== false){
                            if($value == $dealUser){
                                $condition .= ' or (FIND_IN_SET("'.$dealUser.'",t1.dealUser))';
                            }else{
                                $condition .= ' or (FIND_IN_SET("'.$dealUser.'",t1.dealUser) and t1.status in(';
                                $i = 0;
                                foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                                    if($i == 0){
                                        $condition .= "'".$key."'";
                                    }else{
                                        $condition .= ",'".$key."'";
                                    }
                                    $i++;
                                }
                                $condition .= '))';
                            }
                        }else{
                            if($value == $dealUser){
                                $condition .= ' (FIND_IN_SET("'.$dealUser.'",t1.dealUser))';
                            }else{
                                $condition .= ' (FIND_IN_SET("'.$dealUser.'",t1.dealUser) and t1.status in(';
                                $i = 0;
                                foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                                    if($i == 0){
                                        $condition .= "'".$key."'";
                                    }else{
                                        $condition .= ",'".$key."'";
                                    }
                                    $i++;
                                }
                                $condition .= '))';
                            }
                        }
                    }
                    $where .= " $andOr ($condition)";
                }
                continue ;
            }
            if($module == 'outwardDelivery' and $field == 'dealUser' and $operator == '='){
                $this->loadModel('outwarddelivery');
                $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('outwarddelivery', $value);
                if(!empty($dealUserList)){
                    $dealUserList = explode(',', $dealUserList);
                    foreach ($dealUserList as $dealUser){
                        if(strpos($condition, 'FIND_IN_SET') !== false){
                            if($value == $dealUser){
                                $condition .= ' or (FIND_IN_SET("'.$dealUser.'",t1.dealUser))';
                            }else{
                                $condition .= ' or (FIND_IN_SET("'.$dealUser.'",t1.dealUser) and t1.status in(';
                                $i = 0;
                                foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                                    if($i == 0){
                                        $condition .= "'".$key."'";
                                    }else{
                                        $condition .= ",'".$key."'";
                                    }
                                    $i++;
                                }
                                $condition .= '))';
                            }
                        }else{
                            if($value == $dealUser){
                                $condition .= ' (FIND_IN_SET("'.$dealUser.'",t1.dealUser))';
                            }else{
                                $condition .= ' (FIND_IN_SET("'.$dealUser.'",t1.dealUser) and t1.status in(';
                                $i = 0;
                                foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                                    if($i == 0){
                                        $condition .= "'".$key."'";
                                    }else{
                                        $condition .= ",'".$key."'";
                                    }
                                    $i++;
                                }
                                $condition .= '))';
                            }
                        }
                    }
                    $where .= " $andOr ($condition)";
                }
                continue ;
            }
            if($operator == "include")
            {
                if(isset($fieldParams->$field->mulit) && $fieldParams->$field->mulit){
                    $condition = " find_in_set('{$value}',`{$this->post->$fieldName}`) ";
                }else{
                    $condition = ' LIKE ' . $this->dbh->quote("%$value%");
                }

                if($module=='opinion'){
                    if(in_array($this->post->$fieldName, array('union', 'synUnion')))
                    {
                        $condition = ' LIKE ' . $this->dbh->quote("%,$value,%");
                    }else if(isset($fieldParams->$field->mulit) && $fieldParams->$field->mulit){
                        $condition = " find_in_set('{$value}',`{$this->post->$fieldName}`) ";
                    }
                    else
                    {
                        $condition = ' LIKE ' . $this->dbh->quote("%$value%");
                    }
                }
            }
            elseif($operator == "notinclude")
            {
                $condition = ' NOT LIKE ' . $this->dbh->quote("%$value%");
            }
            elseif($operator == 'belong')
            {
                if($this->post->$fieldName == 'module')
                {
                    $allModules = $this->loadModel('tree')->getAllChildId($value);
                    if($allModules) $condition = helper::dbIN($allModules);
                }
                elseif(in_array($this->post->$fieldName, array('dept', 'createdDept', 'acceptDept')))
                {
                    if(!$value) continue;
                    $allDepts = $this->loadModel('dept')->getAllChildId($value);
                    $condition = helper::dbIN($allDepts);
                }
                else
                {
                    $condition = ' = ' . $this->dbh->quote($value) . ' ';
                }
            }
            else
            {
                if($operator == 'between' and !isset($this->config->search->dynamic[$value])) $operator = '=';
                $condition = $operator . ' ' . $this->dbh->quote($value) . ' ';
            }

            /* Processing query criteria. */
            if($operator == '=' and preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value))
            {
                //外部年度计划的3个外表条件 不能使用>< 23:59:59的方式查找
                if($this->post->$fieldName == 'subTaskBegin' || $this->post->$fieldName == 'subTaskBegin' || $this->post->$fieldName == 'subTaskDemandDeadline'){
                    $where .= " $andOr " . '`' . $this->post->$fieldName . '` ' . $condition;
                } else {
                    $condition  = '`' . $this->post->$fieldName . "` >= '$value' AND `" . $this->post->$fieldName . "` <= '$value 23:59:59'";
                    $where     .= " $andOr ($condition)";
                }
            }
            elseif($operator == '<=' and preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $value))
            {
                $where .= " $andOr " . '`' . $this->post->$fieldName . "` <= '$value 23:59:59'";
            }elseif($operator == "include"){
                if($module == 'opinion')
                {
                    if(in_array($this->post->$fieldName, array('union','synUnion')))
                    {
                        $where .= " $andOr " . 'CONCAT(",", `' . $this->post->$fieldName . '`, ",") ' . $condition;
                    }else if(strpos($condition,'find_in_set') !== false){
                        $where .= " $andOr " . $condition;
                    }
                    else
                    {
                        $where .= " $andOr " . '`' . $this->post->$fieldName . '` ' . $condition;
                    }
                }
                else
                {
                    if(strpos($condition,'find_in_set') !== false){
                        $where .= " $andOr " . $condition;
                    }else{
                        $where .= " $andOr " . '`' . $this->post->$fieldName . '` ' . $condition;
                    }

                }
            }
            elseif($condition)
            {
                $where .= " $andOr " . '`' . $this->post->$fieldName . '` ' . $condition;
            }
        }

        $where .=" ))";
        $where  = $this->replaceDynamic($where);

        /* Save to session. */
        $querySessionName = $this->post->module . 'Query';
        $formSessionName  = $this->post->module . 'Form';
        $this->session->set($querySessionName, $where);
        $this->session->set($formSessionName,  $_POST);
        if($scoreNum > 2 && !dao::isError()) $this->loadModel('score')->create('search', 'saveQueryAdvanced');
    }
}
