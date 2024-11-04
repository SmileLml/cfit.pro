<?php
public function setListValue($executionID)
{
    return $this->loadExtension('excel')->setListValue($executionID);
}

public function createFromImport($executionID)
{
    return $this->loadExtension('excel')->createFromImport($executionID);
}
