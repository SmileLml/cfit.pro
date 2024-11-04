<?php
public function getSimplePairs($orderBy = 'id_asc')
{
    $plans = $this->dao->select('id,title')
        ->from(TABLE_PRODUCTPLAN)
        ->where('deleted')->eq(0)
        ->orderBy($orderBy)
        ->fetchPairs('id', 'title');
    return $plans;
}
