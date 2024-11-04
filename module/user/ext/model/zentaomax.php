<?php
public function getPairsByRole($role)
{
    return $this->loadExtension('cmmi')->getPairsByRole($role);
}
