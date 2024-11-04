<?php
public function create($executionID = 0, $bugID = 0, $from = '')
{
    return $this->loadExtension('feedback')->create($executionID, $bugID, $from);
}

public function getById($storyID, $version = 0, $setImgSize = false)
{
    return $this->loadExtension('feedback')->getById($storyID, $version, $setImgSize);
}

public function sendmail($storyID, $actionID)
{
    return $this->loadExtension('feedback')->sendmail($storyID, $actionID);
}
