<?php
public function getStories($productID, $branch, $browseType, $queryID, $moduleID, $type = 'story', $sort, $pager = null)
{
    return $this->loadExtension('feedback')->getStories($productID, $branch, $browseType, $queryID, $moduleID, $type, $sort, $pager);
}
