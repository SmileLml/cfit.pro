<?php
/**
 * 根据自动化分类 key 获取 value。
 * Get the values according to the category keys.
 * 
 * @param  string $categoryKeys
 * @access public
 * @return string
 */
public function getCategoriesValueByKeys($categoryKeys)
{
    $categoryValues   = array();
    $categoryKeysList = explode(',', $categoryKeys);
    $categoryList     = $this->lang->testcase->categoryList;

    foreach($categoryKeysList as $categoryKey) $categoryValues[] = zget($categoryList, $categoryKey);

    return join(',', array_filter($categoryValues));
}
