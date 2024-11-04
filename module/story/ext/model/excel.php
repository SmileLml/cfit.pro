<?php
public function setListValue($productID, $branch = 0)
{
    return $this->loadExtension('excel')->setListValue($productID, $branch);
}

public function createFromImport($productID, $branch = 0, $type = 'story')
{
    return $this->loadExtension('excel')->createFromImport($productID, $branch, $type);
}

public function replaceUserRequirementLang()
{
    return $this->loadExtension('excel')->replaceUserRequirementLang();
}
