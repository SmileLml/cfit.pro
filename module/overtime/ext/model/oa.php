<?php
public function isClickable($overtime, $action)
{
	$action    = strtolower($action);
	$clickable = commonModel::hasPriv('overtime', $action);
	if(!$clickable) return false;

	$account = $this->app->user->account;

	switch($action)
	{
	case 'edit':
	case 'delete':
		$canEdit = strpos(',wait,draft,reject,', ",{$overtime->status},") !== false && $overtime->createdBy == $account;

		return $canEdit;
	case 'switchstatus':
		$canSwitch = strpos(',wait,draft,', ",{$overtime->status},") !== false && $overtime->createdBy == $account;

		return $canSwitch;
	case 'review':
		$reviewedBy = $this->getReviewedBy($overtime->createdBy);
		$canReview  = strpos(',wait,doing,', ",$overtime->status,") !== false && $reviewedBy == $account;

		return $canReview;
	}

	return true;
}
