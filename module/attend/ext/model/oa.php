<?php
/**
 * Compute attend's status.
 *
 * @param  object $attend
 * @access public
 * @return string
 */
public function computeStatus($attend)
{
    return $this->loadExtension('oa')->computeStatus($attend);
}

/**
 * Save stat.
 *
 * @param  int    $date
 * @access public
 * @return bool
 */
public function saveStat($date)
{
    return $this->loadExtension('oa')->saveStat($date);
}

/**
 * Set reviewer for attend.
 *
 * @access public
 * @return bool
 */
public function setManager()
{
    return $this->loadExtension('oa')->setManager();
}

/**
 * Judge an action is clickable or not.
 * 
 * @param  object $attend 
 * @param  string $action 
 * @access public
 * @return bool
 */
public function isClickable($attend, $action)
{
    $action    = strtolower($action);
    $clickable = commonModel::hasPriv('attend', $action);
    if(!$clickable) return false;

    $account = $this->app->user->account;

    switch($action)
    {
    case 'review' :
        $reviewedBy = $this->getReviewedBy($attend->account);
        $canReview  = $attend->reviewStatus == 'wait' && $reviewedBy == $account;

        return $canReview;
    }

    return true;
}
