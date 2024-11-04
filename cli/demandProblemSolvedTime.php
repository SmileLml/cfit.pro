<?php
/**
 * php cli 执行该文件
 * crontab -e
 */
require 'base.php';

class demandProblemSolvedTime extends control
{

    /**
     * 更新需求解決时间
     */
    public function updateDemandSolvedTime(){
        $this->config->debug = 2; //启动报错
        $table = TABLE_DEMAND;
        $tabletype = 'demand';
        $arr = array();
        //查询表
        $demandId = $this->dao->select('id,solvedTime')->from($table)
            ->where('status')->ne('deleted')
            ->andwhere('status')->ne('closed')
            ->fetchAll('id');
        $demandIds = array_column($demandId, 'id');
        $type = array('modify','fix','gain','modifycncc','gainQz','info','infoQz','fixQz');//类型
        $infotype = array('fix','gain');
        $infoqztype = array('gainQz','fixQz');
        // 查询二线表关联数据
        $sendline = $this->dao->select('id,objectID,objectType,relationType,relationID,createdDate')->from(TABLE_SECONDLINE)
            ->where('deleted')->eq(0)
            ->andwhere('objectType')->eq("$tabletype")
            ->andwhere('objectID')->in($demandIds)
            ->andwhere('relationType')->in($type)
            ->orderBy('id desc')
            ->fetchAll('id');

        $sendres = array();
        foreach ($sendline as $key => $item) {
            $sendres[$item->objectID][$item->id] = $sendline[$key];
        }

        foreach ($sendres as $key => $item) {
            $max = array_search(max($item), $item);//获取多个关联内容中最新的
            if(in_array($item[$max]->relationType,$infotype)){
                $objecttype  = 'info';
                $after = 'productsuccess';
            }elseif(in_array($item[$max]->relationType,$infoqztype)){
                $objecttype  = 'infoQz';
                $after = 'leadersuccess';
            }else{
                $objecttype = $item[$max]->relationType;
                $after = 'productsuccess';
            }
            //查询二线专员审批节点
            $before = $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                ->where('deleted')->eq(0)
                ->andwhere('`after`')->eq($after)
                ->andwhere('`objectID`')->eq($item[$max]->relationID)
                ->andwhere('`objectType`')->eq($objecttype)
                ->orderBy('id desc')
                ->limit(1)
                ->fetch();
            if ($before) {
                //查询是否之后有退回的操作
                $reject = $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                    ->where('deleted')->eq(0)
                    ->andwhere('`objectID`')->eq($item[$max]->relationID)
                    ->andwhere('`objectType`')->eq($objecttype)
                    ->andwhere('`after`')->eq('reject')
                    ->andwhere('id')->gt($before->id)
                    ->orderBy('id desc')
                    ->limit(1)
                    ->fetch();

                //没有退回获取时间
                if (!$reject) {
                    //根据二线专员节点 获取前一个节点时间 并更新主表解决时间
                    $before2 = $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                        ->where('deleted')->eq(0)
                        ->andwhere('`after`')->eq($before->before)
                        ->andwhere('`objectID`')->eq($item[$max]->relationID)
                        ->andwhere('`objectType`')->eq($objecttype)
                        ->orderBy('id desc')
                        ->limit(1)
                        ->fetch();
                    if(!$before2){
                        $before2 =  $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                            ->where('deleted')->eq(0)
                            ->andwhere('`objectID`')->eq($item[$max]->relationID)
                            ->andwhere('`objectType`')->eq($objecttype)
                            ->andwhere('id')->lt($before->id)
                            ->orderBy('id desc')
                            ->limit(1)
                            ->fetch();
                    }
                    //更新解决时间
                    $createdDate = isset($before2->createdDate) ? $before2->createdDate : '';
                    $this->dao->update($table)->set('solvedTime')->eq($createdDate)->where('id')->eq($key)->exec();
                    $arr[] = $key;
                }else{
                    $this->dao->update($table)->set('solvedTime')->eq('')->where('id')->eq($key)->exec();
                    $arr[] = $key;
                }
            }
        }
        return json_encode(array('updateDemandSolvedTime'=>$arr));
    }
    /**
     * 更新问题解決时间
     */
    public function updateProblemSolvedTime(){
        $this->config->debug = 2; //启动报错
        $table = TABLE_PROBLEM;
        $tabletype = 'problem';
        $arr = array();
        //查询表
        $demandId = $this->dao->select('id,solvedTime')->from($table)
            ->where('status')->ne('deleted')
            ->andwhere('status')->ne('closed')
            ->fetchAll('id');
        $demandIds = array_column($demandId, 'id');
        $type = array('modify','fix','gain','modifycncc','gainQz','info','infoQz','fixQz');//类型
        $infotype = array('fix','gain');
        $infoqztype = array('gainQz','fixQz');
        // 查询二线表关联数据
        $sendline = $this->dao->select('id,objectID,objectType,relationType,relationID,createdDate')->from(TABLE_SECONDLINE)
            ->where('deleted')->eq(0)
            ->andwhere('objectType')->eq("$tabletype")
            ->andwhere('objectID')->in($demandIds)
            ->andwhere('relationType')->in($type)
            ->orderBy('id desc')
            ->fetchAll('id');

        $sendres = array();
        foreach ($sendline as $key => $item) {
            $sendres[$item->objectID][$item->id] = $sendline[$key];
        }

        foreach ($sendres as $key => $item) {
            $max = array_search(max($item), $item);//获取多个关联内容中最新的
            if(in_array($item[$max]->relationType,$infotype)){
                $objecttype  = 'info';
                $after = 'productsuccess';
            }elseif(in_array($item[$max]->relationType,$infoqztype)){
                $objecttype  = 'infoQz';
                $after = 'leadersuccess';
            }else{
                $objecttype = $item[$max]->relationType;
                $after = 'productsuccess';
            }
            //查询二线专员审批节点
            $before = $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                ->where('deleted')->eq(0)
                ->andwhere('`after`')->eq($after)
                ->andwhere('`objectID`')->eq($item[$max]->relationID)
                ->andwhere('`objectType`')->eq($objecttype)
                ->orderBy('id desc')
                ->limit(1)
                ->fetch();
            if ($before) {
                //查询是否之后有退回的操作
                $reject = $this->dao->select('id,`before`,`after`,createdBy')->from(TABLE_CONSUMED)
                    ->where('deleted')->eq(0)
                    ->andwhere('`objectID`')->eq($item[$max]->relationID)
                    ->andwhere('`objectType`')->eq($objecttype)
                    ->andwhere('`after`')->eq('reject')
                    ->andwhere('id')->gt($before->id)
                    ->orderBy('id desc')
                    ->limit(1)
                    ->fetch();

                //没有退回获取时间
                if (!$reject) {
                    //根据二线专员节点 获取前一个节点时间 并更新主表解决时间
                    $before2 = $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                        ->where('deleted')->eq(0)
                        ->andwhere('`after`')->eq($before->before)
                        ->andwhere('`objectID`')->eq($item[$max]->relationID)
                        ->andwhere('`objectType`')->eq($objecttype)
                        ->orderBy('id desc')
                        ->limit(1)
                        ->fetch();
                    if(!$before2){
                        $before2 =  $this->dao->select('id,`before`,`after`,createdBy,createdDate')->from(TABLE_CONSUMED)
                            ->where('deleted')->eq(0)
                            ->andwhere('`objectID`')->eq($item[$max]->relationID)
                            ->andwhere('`objectType`')->eq($objecttype)
                            ->andwhere('id')->lt($before->id)
                            ->orderBy('id desc')
                            ->limit(1)
                            ->fetch();
                    }
                    //更新解决时间
                    $createdDate = isset($before2->createdDate) ? $before2->createdDate : '';
                    $this->dao->update($table)->set('solvedTime')->eq($createdDate)->where('id')->eq($key)->exec();
                    $arr[] = $key;
                }else{
                    $this->dao->update($table)->set('solvedTime')->eq('')->where('id')->eq($key)->exec();
                    $arr[] = $key;
                }
            }
        }
        return json_encode(array('updateProblemSolvedTime'=>$arr));
    }

}
//$lock = getLock('demandProblemSolvedTime', 40); //锁定防止重复
$lock = getTimeLock('demandProblemSolvedTime', 40); //锁定防止重复
$reviewManageAllOwner = new demandProblemSolvedTime();
$dataDemand = $reviewManageAllOwner->updateDemandSolvedTime();
saveLog($dataDemand);
$data = $reviewManageAllOwner->updateProblemSolvedTime();
saveLog($data);
unlock($lock); //解除锁定

