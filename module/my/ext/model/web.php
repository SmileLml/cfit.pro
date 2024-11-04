<?php
public function setMenu()
{
$this->loadExtension('effort')->setMenu();

    return $this->loadExtension('web')->setMenu();
}
