<?php
public function send($objectType, $objectID, $actionType, $actionID, $actor = '')
{

    return $this->loadExtension('sms')->send($objectType, $objectID, $actionType, $actionID, $actor);
}
