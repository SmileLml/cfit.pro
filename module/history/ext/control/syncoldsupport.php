<?php
include '../../control.php';
class myHistory extends history
{
    /**
     * 旧的数据支持迁到新的现场支持表
     * @return void
     */
    public function syncOldSupport($start = 0)
    {
        $i = 0;
        $limit = 100;
        while (1){
            $sql = "select  * from zt_flow_support where 1 limit {$start}, {$limit};";
            $data = $this->dao->query($sql)->fetchAll();
            if(empty($data)){
                break;
            }
            $start += $limit;
            $createdUsers = array_column($data, 'createdBy');
            $userList = $this->loadModel('user')->getUserInfoListByAccounts($createdUsers, 'account,dept');
            if($userList){
                $userList = array_column($userList, null, 'account');
            }

            $ids = array_column($data, 'id');
            $fileList   = $this->loadModel('file')->getFileListByObjectIds('support', $ids);
            $actionList = $this->loadModel('action')->getActionListByObjectIds('support', $ids);
            $exWhere = ' isOld = 2';
            $localesupportList = $this->loadModel('localesupport')->getAllListByIds($ids, 'id', $exWhere);


            if($localesupportList){
                $localesupportList = array_column($localesupportList, null, 'id');
            }

            foreach ($data as $val){
                $id = $val->id;
                if(isset($localesupportList[$id])){ //已经存在
                    continue;
                }
                $i++;
                $appIds = $val->app;
                $owndeptList = [];
                $sjList      = [];
                $appIds = explode(',', $appIds);
                foreach ($appIds as $appId){
                    $owndeptList[$appId] = $val->owndept;
                    $sjList[$appId] = $val->sj;
                }
                $owndeptInfo = json_encode($owndeptList);
                $sjInfo      = json_encode($sjList);
                $createdBy = $val->createdBy;
                $userInfo = zget($userList, $createdBy, new stdClass());
                $supportUsers = $val->pnams;
                $status       = 'waitdept';
                $deptManagers = $val->approver;
                $dealUsers    = $deptManagers;
                $dept = $val->dept;
                $deptManagersGroup = [
                    $dept => [$deptManagers]
                ];
                if($val->status == 2){
                    $status = 'pass';
                    $dealUsers = '';
                }
                $temp = new  stdClass();
                $temp->id          = $val->id;
                $temp->status      = $status; //审核通过或者待审批
                $temp->dealUsers   = $dealUsers; //待处理人
                $temp->createdBy   = $val->createdBy;
                $temp->createdTime = $val->createdDate;
                $temp->createdDept = isset($userInfo->dept) ? $userInfo->dept : 0; //todo
                $temp->editedBy    = $val->editedBy;
                $temp->editedBy    = $val->editedBy;
                $temp->editedtime  = $val->editedDate;
                $temp->mailto     = $val->mailto;
                $temp->deleted    = $val->deleted;
                $temp->startDate    = $val->sdate;
                $temp->area         = $val->area;
                $temp->appIds       = $val->app;
                $temp->stype        = $val->stype;
                $temp->work         = $val->workh;
                $temp->deptIds      = $dept;
                $temp->supportUsers = $supportUsers;
                $temp->deptManagers = $deptManagers;
                $temp->deptManagersGroup = json_encode($deptManagersGroup);
                $temp->owndept = $owndeptInfo;
                $temp->reason  = $val->reason;
                $temp->remark  = $val->remark;
                $temp->endDate = $val->edate;
                $temp->sj      = $sjInfo;
                $temp->jxdepart = $val->jxdepart;
                $temp->sysper = $val->sysper;
                $temp->manufacturer = $val->manufacturer;
                $temp->isOld = 2; //是否是旧数据
                $currentTime = strtotime($val->createdDate);
                $code = $this->loadModel('localesupport')->getCode($currentTime);
                $temp->code = $code;

                //插入数据
                $this->dao->insert(TABLE_LOCALESUPPORT)->data($temp)->exec();
                $localesupportId = $this->dao->lastInsertID();
                $supportUsersArray = explode(',', $supportUsers); //支持人员
                if(!empty($supportUsersArray)){ //报工信息
                    $workh = $val->workh;
                    $count = count($supportUsersArray);
                    $maxKey = $count - 1;
                    if($workh > 0){
                        if($count == 1){
                            $consumed = $workh;
                        }else{
                            $consumed = number_format($workh / $count, 1);
                        }
                    }else{
                        $consumed = $workh;
                    }
                    foreach ($supportUsersArray as $tempKey => $supportUser){
                        if(($workh > 0) &&  ($count > 1) && ($tempKey == $maxKey)){ //最后一个人兜底
                            $tempConsumed = $consumed * $tempKey;
                            $consumed = number_format($workh - $tempConsumed, 1);
                        }
                        $tempParams = new stdClass();
                        $tempParams->supportId   = $localesupportId;
                        $tempParams->supportDate = $val->sdate;
                        $tempParams->deptId      = $val->dept;
                        $tempParams->supportUser = $supportUser;
                        $tempParams->consumed    = $consumed;
                        $tempParams->syncStatus  = 2;
                        $tempParams->createdBy  = $val->createdBy;
                        $tempParams->createdDate = $val->editedDate;
                        $this->dao->insert(TABLE_LOCALESUPPORT_WORKREPORT)->data($tempParams)->exec();
                    }
                }
                if($status == 'waitdept'){ //添加审核节点
                    $temp->id = $localesupportId;
                    $version = 1;
                    $ret = $this->loadModel('localesupport')->addReviewNode($temp, $version, $status);
                }

                //附件
                $tempFileList = zget($fileList, $id, []);
                if(!empty($tempFileList)){
                    foreach ($tempFileList as $fileParam){
                        $fileParam->objectType = 'localesupport';
                        $fileParam->objectID   = $localesupportId;
                        unset($fileParam->id);
                        //插入附件
                        $this->dao->insert(TABLE_FILE)->data($fileParam)->exec();
                    }
                }
                //日志
                $tempActionList = zget($actionList, $id, []);
                if(!empty($tempActionList)){
                    foreach ($tempActionList as $actionParam){
                        $actionParam->objectType = 'localesupport';
                        $actionParam->objectID   = $localesupportId;
                        $historyList = zget($actionParam, 'historyList', []);
                        unset($actionParam->id);
                        unset($actionParam->historyList);
                        //插入日志
                        $this->dao->insert(TABLE_ACTION)->data($actionParam)->exec();
                        $actionId = $this->dao->lastInsertID();
                        if(!empty($historyList)){
                            foreach ($historyList as $historyParam){
                                $historyParam->action = $actionId;
                                unset($historyParam->id);
                                $this->dao->insert(TABLE_HISTORY)->data($historyParam)->exec();
                            }
                        }
                    }
                }
            }
        }
        echo '同步了'.$i. '条数据';
    }
}