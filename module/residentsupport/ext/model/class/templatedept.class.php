<?php
class templatedeptResidentsupport extends residentsupportModel
{
    /**
     *获得模板下某一个部门信息
     *
     * @param $templateDeptId
     * @param string $select
     * @return bool
     */
    public function getTemplateDeptInfoById($templateDeptId, $select = '*'){
        if(!$templateDeptId){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)
            ->where("id")->eq($templateDeptId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($data){
            //模板ID
            $templateId = $data->templateId;
            //模板信息
            $templateInfo = $this->loadModel('residentsupport')->getTemplateInfoById($templateId);
            $data->templateInfo = $templateInfo;
            $objectType = $this->lang->residentsupport->objectTypeList['resident_support_template_dept'];
            $status = $data->status;
            if(in_array($status, $this->lang->residentsupport->temDeptAllowReviwStatusList)){
                //获得审核人
                $this->loadModel('review');
                $version = $data->version;
                $data->reviewers = $this->review->getReviewer($objectType, $templateDeptId, $version);
            }
        }
        return $data;
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
        if(!($templateId && $deptId)){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)
            ->where("templateId")->eq($templateId)
            ->andWhere('deptId')->eq($deptId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        return $data;
    }

    /**
     *获得申请提交的下一个状态
     *
     * @param $templateDeptInfo
     * @return string
     */
    public function getTemplateDeptSubmitNextStatus($templateDeptInfo){
        $nextStatus = '';
        $status = $templateDeptInfo->status;
        switch ($status){
            case $this->lang->residentsupport->temDeptStatusList['waitApply']:
                $nextStatus = $this->lang->residentsupport->temDeptStatusList['waitDeptReview']; //待部门审批
                break;

            default:
                break;
        }
        return $nextStatus;
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
        $nextDealUsers = '';
        if(!empty($postUsers)){
            $nextDealUsers = $postUsers;
        }else{
            switch ($nextStatus){
                case $this->lang->residentsupport->temDeptStatusList['waitDeptReview']: //待部门审批
                    $deptId = $templateDeptInfo->deptId;
                    $deptInfo = $this->loadModel('dept')->getByID($deptId);
                    $nextDealUsers = $deptInfo->manager;
                    break;

                case $this->lang->residentsupport->temDeptStatusList['waitPdReview']: //产创审核
                    $type = $templateDeptInfo->templateInfo->type;
                    $nextDealUsers = $this->getPdReviewDealUsers($type);
                    break;

                case $this->lang->residentsupport->temDeptStatusList['reject']:
                    $nextDealUsers = $templateDeptInfo->applySubmitBy;
                    break;
                case $this->lang->residentsupport->temDeptStatusList['modifyReject']:
                    $nextDealUsers = '';
                    break;

                default:
                    break;
            }
        }
        return $nextDealUsers;
    }

    /**
     *获得产创部审核人
     *
     * @param $type
     * @return string
     */
    public function getPdReviewDealUsers($type){
        $dealUsers = '';
        $userList = $this->loadModel('custom')->getCustomSetList('problem', 'apiDealUserList');
        if($userList){
            switch ($type){
                case 1:
                    $dealUsers = zget($userList, 'userAccount'); //支付类
                    break;

                case 2:
                    $dealUsers = zget($userList, 'jxDealAccount'); //总行类
                    break;

                default:
                    break;
            }
        }
        return $dealUsers;
    }

    /**
     *获得对应审核节点的标识
     *
     * @param $status
     * @return string
     */
    public function getTemplateDepReviewNodeCode($status){
        $nodeCode = zget($this->lang->residentsupport->temDeptNodeCodeList, $status);
        return $nodeCode;
    }

    /**
     *获得审批的下一状态
     *
     * @param $templateDeptInfo
     * @param $reviewResult
     * @return string
     */
    public function getTemplateDeptReviewNextStatus($templateDeptInfo, $reviewResult){
        $nextStatus = '';
        if($reviewResult == 'reject'){
            $isModify = $templateDeptInfo->isModify;
            if($isModify == $this->lang->residentsupport->temDeptModifyStatusList[2]){
                $nextStatus = $this->lang->residentsupport->temDeptStatusList['modifyReject']; //变更驳回
            }else{
                $nextStatus = $this->lang->residentsupport->temDeptStatusList['reject'];
            }
        }elseif($reviewResult == 'pass'){
            $status = $templateDeptInfo->status;
            switch ($status){
                case $this->lang->residentsupport->temDeptStatusList['waitDeptReview']: //待部门审批
                    $nextStatus = $this->lang->residentsupport->temDeptStatusList['waitPdReview'];  //待产创确认
                    break;

                case $this->lang->residentsupport->temDeptStatusList['waitPdReview']:  //待产创确认
                    $nextStatus = $this->lang->residentsupport->temDeptStatusList['pass'];  //已确认
                    break;

                default:
                    break;
            }

        }
        return $nextStatus;
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
        if(!($templateId)){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)
            ->where("templateId")->eq($templateId)
            ->beginIF(!empty($deptIds))->andWhere('deptId')->in($deptIds)->fi()
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        //echo $this->dao->printSql();
        return $data;
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
        if(!($templateId)){
            return false;
        }
        $data = $this->dao->select($select)
            ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)
            ->where("templateId")->eq($templateId)
            ->beginIF(!empty($deptIds))->andWhere('deptId')->in($deptIds)->fi()
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->ne($this->lang->residentsupport->temDeptStatusList['pass'] )
            ->fetchAll();
        return $data;
    }
}

