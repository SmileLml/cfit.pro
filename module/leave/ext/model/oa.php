<?php
public function isClickable($leave, $action)
{
	$action    = strtolower($action);
	$clickable = commonModel::hasPriv('leave', $action);
	if(!$clickable) return false;

	$account = $this->app->user->account;

	switch($action)
	{
	case 'back':
		$canBack = $leave->status == 'pass' && date('Y-m-d H:i:s') > "$leave->begin $leave->start" && date('Y-m-d H:i:s') < "$leave->end $leave->finish" && $leave->backDate != "$leave->end $leave->finish" && $leave->createdBy == $account;

		return $canBack;
	case 'edit':
	case 'delete': 
		$canEdit = strpos(',wait,draft,reject,', ",{$leave->status},") !== false && $leave->createdBy == $account;

		return $canEdit;
	case 'switchstatus':
		$canSwitch = strpos(',wait,draft,', ",{$leave->status},") !== false && $leave->createdBy == $account;

		return $canSwitch;
	case 'review':
		$reviewedBy = $this->getReviewedBy($leave->createdBy);
		$canReview  = strpos(',wait,doing,back,', ",$leave->status,") !== false && $reviewedBy == $account;

		return $canReview;
	}

	return true;
}

