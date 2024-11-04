<?php
public function getList($objectType, $objectID)
{
    return $this->loadExtension('feedback')->getList($objectType, $objectID);
}

public function getRelatedFields($objectType, $objectID)
{
    return $this->loadExtension('feedback')->getRelatedFields($objectType, $objectID);
}

public function printAction($action, $desc = '')
{
    return $this->loadExtension('feedback')->printAction($action, $desc);
}
