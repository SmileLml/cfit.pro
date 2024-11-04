<?php

class updateProblem extends problemModel
{
    /**
     * 更新问题单交付是否超期字段
     * @return void
     */
    public function updateIsExceedByTime()
    {
        $list = $this->dao
            ->select('*')
            ->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            //->andWhere('isExceedByTime')->eq('否')
            ->fetchAll();

        $arr = [];
        foreach ($list as $item){
            $arr[$item->id] = $this->loadModel('problem')->getIsExceedByTime($item);
        }

        return $arr;
    }

    /**
     * 更新问题单内部反馈是否超期字段
     * @return void
     */
    public function updateIfOverDateInsideNew()
    {
        $list = $this->dao
            ->select('*')
            ->from(TABLE_PROBLEM)
            ->where('status')->ne('deleted')
            ->andWhere('IssueId')->ne('')
            ->andWhere('ifOverDateInside')->eq('0')
            ->fetchAll();
        $arr = [];
        foreach ($list as $item){
            $problem = $this->loadModel('problem')->getIfOverDate($item);
            $arr[$problem->id] = $problem->ifOverDateInside['flag'];
        }

        return $arr;
    }

    /**
     * 生成指派按钮
     * @param $problem
     * @param $users
     * @return string
     */
    public function printAssignedHtml($problem, $users)
    {
        $errorMsg = $this->isAssigned($problem);
        $problem->dealUser = trim( $problem->dealUser, ',');
        $assignedToText = !empty($problem->dealUsers) ? $problem->dealUsers : '';

        if(!empty($errorMsg)){
            return "<span style='padding-left: 21px'>{$assignedToText}</span>";
        }

        $btnClass     = "iframe btn btn-icon-left btn-sm";
        $assignToLink = helper::createLink('problem', 'assignByUser', "problemId=$problem->id", '', true);

        return html::a(
            $assignToLink,
            "<i class='icon icon-hand-right'></i> <span title='" . $assignedToText . "' class='text-primary'>{$assignedToText}</span>",
            '', "class='$btnClass'");
    }

    /**
     * 问题单指派权限判断
     * @param $problem
     * @return string
     */
    public function isAssigned($problem)
    {
        if(!common::hasPriv('problem', 'assignByUser', $problem)){
            return $this->lang->problem->assignedAuthError;
        }

        if('assigned' != $problem->status){
            return $this->lang->problem->assignedStatusError;
        }

        $userInfo = $this->dao
            ->select('objectID,account')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('problem')
            ->andWhere('objectID')->eq($problem->id)
            ->andWhere('`before`')->eq('confirmed')
            ->andWhere('`after`')->eq('assigned')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetch();

        $flag = isset($userInfo->account) && !empty($userInfo->account) && $this->app->user->account == $userInfo->account;
        if(!$flag){
            return $this->lang->problem->assignedUserError;
        }

        return '';
    }

}





