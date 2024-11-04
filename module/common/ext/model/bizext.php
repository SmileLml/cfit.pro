<?php
public function setCompany()
{
if(!extension_loaded('ionCube Loader')) return parent::setCompany();
$this->loadExtension('zentaobiz')->setCompany();

    if(!extension_loaded('ionCube Loader')) return parent::setCompany();

    return $this->loadExtension('bizext')->setCompany();
}
