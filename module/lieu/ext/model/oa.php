<?php
public function isClickable($lieu, $action)
{   
	$action    = strtolower($action);
	$clickable = commonModel::hasPriv('lieu', $action);
	if(!$clickable) return false;

	$account = $this->app->user->account;

	switch($action)
	{
	case 'edit':
	case 'delete':
		$canEdit = strpos(',wait,draft,reject,', ",{$lieu->status},") !== false && $lieu->createdBy == $account;

		return $canEdit;
	case 'switchstatus':
		$canSwitch = strpos(',wait,draft,', ",{$lieu->status},") !== false && $lieu->createdBy == $account;

		return $canSwitch;
	case 'review':
		$reviewedBy = $this->getReviewedBy($lieu->createdBy);
		$canReview  = strpos(',wait,doing,', ",$lieu->status,") !== false && $reviewedBy == $account;

		return $canReview;
	}

	return true;
}
