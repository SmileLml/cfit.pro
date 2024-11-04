<?php
include '../../control.php';
class myDemand extends demand
{

    /**
     * 根据相关流程状态 获取下一节点处理人
     */
    public function ajaxGetNextUser($demandid , $status)
    {
        $demand = $this->loadModel('demand')->getByID($demandid);
        $type = 'demand';
        $user = "";
        switch ($status){
            case 'build':
                $user = $demand->laboratorytest;
                break;
            case 'waitverify':
                 $all = array($this->getObjectByID($demandid,$type,'solved'));
                 $user = $all[0]->account;
                break;
            /* case 'testfailed':
                  $all = array($this->getObjectByID($demandid,$type,'feedbacked'));
                  $user = $all[0]->account;
                 break;*/
            case 'testsuccess':
                 $user = $demand->verifyperson;
                break;
            case 'verifysuccess':
                $all = array($this->getObjectByID($demandid,$type,'solved'));
                $user = $all[0]->account;
                break;
            /*case 'versionfailed':
                $all = array($this->getObjectByID($demandid,$type,'feedbacked'));
                $user = $all[0]->account;
                break;*/
            case 'testfailed':
            case 'versionfailed':
            case 'verifyfailed':
                $all = array($this->getObjectByID($demandid,$type,'feedbacked'));
                $user = $all[0]->account;
                break;
        }
        die($user);
    }

    /**
     * 查询处理流程信息   返回操作状态 和操作人
     * @param $objectID
     * @param $objectType
     * @return mixed
     */
    public function getObjectByID($objectID, $objectType,$status)
    {
        $list = $this->dao->select('max(id) id')->from(TABLE_CONSUMED)
                ->where('objectID')->eq($objectID)
                ->andWhere('objectType')->eq($objectType)
                ->andWhere('`before`')->eq($status)
                ->groupBy('`before`')
                ->fetchAll('id');
        $result = $this->dao->select('`before`,account')->from(TABLE_CONSUMED)
                ->where('id')->in(array_keys($list))
                ->orderBy('id desc')
                ->fetch();

        return $result;
    }
}
