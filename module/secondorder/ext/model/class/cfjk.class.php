<?php

class cfjkSecondorder extends secondorderModel
{
    public function printAssignedHtml($secondOrder, $users)
    {
        $secondOrder->dealUser = trim( $secondOrder->dealUser, ',');
        $assignedToText = !empty($secondOrder->dealUser) ? zmget($users, $secondOrder->dealUser) : '';

        $flag = common::hasPriv('secondorder', 'assignByUser', $secondOrder) && 'assigned' == $secondOrder->status;
        if(!$flag){
            return "<span style='padding-left: 21px'>{$assignedToText}</span>";
        }
        $secondUser = $this->dao
            ->select('objectID,account')
            ->from(TABLE_CONSUMED)
            ->where('objectType')->eq('secondorder')
            ->andWhere('objectID')->eq($secondOrder->id)
            ->andWhere('`before`')->eq('toconfirmed')
            ->andWhere('`after`')->eq('assigned')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetch();
        $flag = isset($secondUser->account) && !empty($secondUser->account) && $this->app->user->account == $secondUser->account;
        if(!$flag){
            return "<span style='padding-left: 21px'>{$assignedToText}</span>";
        }

        $btnClass     = "iframe btn btn-icon-left btn-sm";
        $assignToLink = helper::createLink('secondorder', 'assignByUser', "secondorderId=$secondOrder->id", '', true);

        return html::a(
            $assignToLink,
            "<i class='icon icon-hand-right'></i> <span title='" . $assignedToText . "' class='text-primary'>{$assignedToText}</span>",
            '', "class='$btnClass'");
    }
}
