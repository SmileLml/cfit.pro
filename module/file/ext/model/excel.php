<?php
public function excludeHtml($content, $extra = '')
{
    return $this->loadExtension('excel')->excludeHtml($content, $extra);
}

public function setAreaStyle($excelSheet, $style, $area)
{
    return $this->loadExtension('excel')->setAreaStyle($excelSheet, $style, $area);
}

public function getRowsFromExcel($file)
{
    return $this->loadExtension('excel')->getRowsFromExcel($file);
}
