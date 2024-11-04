<?php
public function authorize($account)
{
    return $this->loadExtension('feedback')->authorize($account);
}

public function getHasFeedbackPriv($pager = null)
{
    return $this->loadExtension('feedback')->getHasFeedbackPriv($pager);
}

public function getPairs($params = '', $usersToAppended = '', $maxCount = 0)
{
    return $this->loadExtension('feedback')->getPairs($params, $usersToAppended, $maxCount);
}
