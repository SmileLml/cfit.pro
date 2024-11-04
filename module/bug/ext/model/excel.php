<?php
public function setListValue($applicationID, $productID, $branch, $projectID = 0)
{
    return $this->loadExtension('excel')->setListValue($applicationID, $productID, $branch, $projectID);
}

public function createFromImport($productID, $branch = 0)
{
    return $this->loadExtension('excel')->createFromImport($productID, $branch);
}
