<?php
public function getFields($module, $action, $getRealOptions = true, $datas = array())
{
    return $this->loadExtension('flow')->getFields($module, $action, $getRealOptions, $datas);
}

public function saveNotice($id)
{
    return $this->loadExtension('flow')->saveNotice($id);
}
