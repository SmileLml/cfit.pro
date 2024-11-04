<?php
include '../../control.php';
class myProblem extends problem
{

    /**
     * 为了加进展跟踪信息权限
     */
    public function getProgressInfo()
    {
       return true;
    }
}
