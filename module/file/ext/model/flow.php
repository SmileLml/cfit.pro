<?php
public function parseExcel($fields = array(), $sheetIndex = 0)
{
    return $this->loadExtension('flow')->parseExcel($fields, $sheetIndex);
}
