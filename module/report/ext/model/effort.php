<?php
public function getUserYearEfforts($account, $year)
{
    return $this->loadExtension('effort')->getUserYearEfforts($account, $year);
}

public function getEffort4Month($account, $year)
{
    return $this->loadExtension('effort')->getEffort4Month($account, $year);
}
