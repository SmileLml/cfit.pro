<?php
public function getUsers($browseType = 'inside', $deptID, $pager = null, $orderBy = 'id')
{
    return $this->loadExtension('zentaobiz')->getUsers($browseType, $deptID, $pager, $orderBy);
}

public function getDeptUserPairs($deptID = 0, $params = '')
{
    return $this->loadExtension('zentaobiz')->getDeptUserPairs($deptID, $params);
}

public function getManager($deptID)
{
    return $this->loadExtension('zentaobiz')->getManager($deptID);
}
