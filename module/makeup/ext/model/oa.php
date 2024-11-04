<?php
public function isClickable($makeup, $action)
{
	$action    = strtolower($action);
	$clickable = commonModel::hasPriv('makeup', $action);
	if(!$clickable) return false;

	$account = $this->app->user->account;

	switch($action)
	{
	case 'edit':
	case 'delete':
		$canEdit = strpos(',wait,draft,reject,', ",{$makeup->status},") !== false && $makeup->createdBy == $account;

		return $canEdit;
	case 'switchstatus':
		$canSwitch = strpos(',wait,draft,', ",{$makeup->status},") !== false && $makeup->createdBy == $account;

		return $canSwitch;
	case 'review':
		$reviewedBy = $this->getReviewedBy($makeup->createdBy);
		$canReview  = strpos(',wait,doing,', ",$makeup->status,") !== false && $reviewedBy == $account;

		return $canReview;
	}

	return true;
}

