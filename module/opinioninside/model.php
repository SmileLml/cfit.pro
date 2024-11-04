<?php

class opinioninsideModel extends model
{
    /**
     * Get opinion list.
     * @param string $browseType
     * @param string $orderBy
     * @param object $pager
     * @access public
     * @return void
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null, $extra = '', $begin = '', $end = '')
    {
        /* 获取搜索条件的查询SQL。*/
        $opinionQuery = '';
        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('opinioninsideQuery', $query->sql);
                $this->session->set('opinioninsideForm', $query->form);
            }

            if ($this->session->opinionQuery == false) $this->session->set('opinioninsideQuery', ' 1 = 1');
            $opinionQuery = $this->session->opinioninsideQuery;

            // 处理[同步单位]搜索字段
            if (strpos($opinionQuery, '`synUnion`') !== false) {
                $opinionQuery = str_replace('`synUnion`', "CONCAT(',', `synUnion`, ',')", $opinionQuery);
            }
            // 处理[业务需求单位]搜索字段
            if (strpos($opinionQuery, '`union`') !== false) {
                $opinionQuery = str_replace('`union`', "CONCAT(',', `union`, ',')", $opinionQuery);
            }
        }

        /* 创建SQL查询数据。*/
        $opinions = $this->dao->select('*')->from(TABLE_OPINION)
            ->where(1)
            ->andWhere('sourceOpinion')->eq(2)
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'noclosed' and $browseType != 'assigntome' and $browseType != 'ignore')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'noclosed')->andWhere('status')->ne('closed')->fi()
            ->beginIF($browseType == 'assigntome')->andWhere('assignedTo')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'all')->andWhere('status')->ne('deleted')->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($opinionQuery)->fi()
            ->beginIF($begin)->andWhere('createdDate')->ge($begin)->fi()
            ->beginIF($end)->andWhere('createdDate')->le($end)->fi()
            ->beginIF(strpos($extra, 'nodeleted') !== false)->andWhere('status')->ne('deleted')->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        /* 保存查询条件并查询子需求条目。*/
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'opinioninside', $browseType != 'bysearch');
        return $this->getChildren($opinions);
    }

    public function getPairs($orderBy = 'id_desc')
    {
        $opinions = $this->dao->select('id,name')->from(TABLE_OPINION)
            ->where('status')->ne('deleted')
            ->andWhere('sourceOpinion')->eq(2)
            ->orderBy($orderBy)
            ->fetchPairs();

        return $opinions;
    }

    /**
     * TongYanQi 2022/12/19
     * 所有状态 统计用
     */
    public function getAllStatus()
    {
        $opinions = $this->dao->select('id,name,status,`category`,`union`')->from(TABLE_OPINION)
            ->where('status')->ne('deleted')
            ->andWhere('sourceOpinion')->eq(2)
            ->andwhere('status')->ne('suspend')
            ->fetchAll();
        return $opinions;
    }
    /**
     * Get opinion children.
     *
     * @param array $opinions
     * @access public
     * @return void
     */
    public function getChildren($opinions)
    {
        if (empty($opinions)) {
            return $opinions;
        }
        /* 获取需求意向的子需求条目，并获取需求条目的评审人，然后返回数据。*/
        $this->loadModel('review');
        $children = $this->dao->select('*')->from(TABLE_REQUIREMENT)
            ->where('opinion')->in(array_keys($opinions))
            ->andWhere('status')->ne('deleted')
            ->andWhere('sourceRequirement')->eq(1)
            ->orderBy('id DESC')->fetchGroup('opinion');

        $demand = $this->dao->select('id,requirementID,code,title,app,product,project')->from(TABLE_DEMAND)
            ->where('opinionID')->in(array_keys($opinions))
            ->andWhere('status')->ne('deleted')
            ->andWhere('sourceDemand')->eq(2)
            ->orderBy('id DESC')->fetchGroup('requirementID');


        foreach ($children as $opinionID => $requirements) {
            foreach ($requirements as $requirement) {
                $requirement->reviewer = $this->review->getReviewer('requirement', $requirement->id, $requirement->changeVersion);
                $requirement->children = isset($demand[$requirement->id])?$demand[$requirement->id]:array();
            }
            $opinions[$opinionID]->children = $requirements;
        }
        return $opinions;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $opinionID
     * @return mixed
     */
    public function getByID($opinionID)
    {
        /* 查询需求意向的子需求条目、需求意向信息和需求意向相关附件然后返回数据。*/
        $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)->where('opinion')->eq($opinionID)->fetchAll();
        //需求意向的所属项目取需求任务的并集
        $projectArray = array_unique(array_column($requirements,'project'));
        $opinion = $this->dao->findByID($opinionID)->from(TABLE_OPINION)->fetch();
        $opinion->files = $this->loadModel('file')->getByObject('opinion', $opinionID);
        $opinion->requirements = $requirements;
        $opinion->projectArray = $projectArray;

        $opinion = $this->loadModel('file')->replaceImgURL($opinion, 'background,overview,desc');
        $opinion = $this->getConsumed($opinion);

        return $opinion;
    }

    /**
     * 定时更新需求意向状态
     */
    public function changeStatus()
    {
        $opinionInfo = $this->getOpinionInfoAboutStatus();
        /**@var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $enterArray = [];  //已录入
        $splitArray = []; //已拆分
        $deliverArray = []; //已交付
        $onlineStatusArray = []; //上线成功 需要单条进行处理
        $langStatus = $this->lang->opinion->statusList;
        foreach ($opinionInfo as $item)
        {
            $requirementInfo = $requirementModel->getRequirementInfoByOpinionID($item->id);
            $paramsArray = $this->changeOpinionStatus($requirementInfo,$item);//处理需求意向的最终状态
            $opinionStatus = !empty($paramsArray) ? $paramsArray['opinionStatus'] : '';
            switch($opinionStatus)
            {
                case 'created': //已录入
                    /*
                     * 没有需求任务的场景与需求意向做联动时判断意向状态若为“已挂起”、“待更新”、“审核已通过”、“审核未通过”则不改需求意向状态。
                     * 需求意向状态为：已交付、已拆分、上线成功时状态更新为已录入
                    */
                    if(empty($requirementInfo))
                    {
                      if(in_array($item->status,$this->lang->opinion->statusArr['changeStatus']))
                      {
                          $enterArray = $requirementModel->insertActionArray($item->id,$this->lang->opinion->noRequirementCode,$item->status);
                          $this->updateStatusById('created',$item->id);
                          $this->loadModel('consumed')->record('opinion', $item->id, 0, 'guestjk', $item->status, 'created');
                          if(!empty($enterArray))
                          {
                              $this->loadModel('action')->createActions('opinion', $enterArray, 'createdscript',$langStatus,1);
                          }
                      }
                    }else{
                        if($item->status != 'created')
                        {
                            $enterArray = $requirementModel->insertActionArray($item->id,$paramsArray['code'],$item->status);
                            $this->updateStatusById('created',$item->id);
                            $this->loadModel('consumed')->record('opinion', $item->id, 0, 'guestjk', $item->status, 'created');
                            if(!empty($enterArray))
                            {
                                $this->loadModel('action')->createActions('opinion', $enterArray, 'createdscript',$langStatus,1);
                            }
                        }
                    }
                    break;
                case 'subdivided': //已拆分
                    if($item->status != 'subdivided')
                    {
                        $splitArray = $requirementModel->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $this->updateStatusById('subdivided',$item->id);
                        $this->loadModel('consumed')->record('opinion', $item->id, 0, 'guestjk', $item->status, 'subdivided');
                        if(!empty($splitArray))
                        {
                            $this->loadModel('action')->createActions('opinion', $splitArray, 'subdividedscript',$langStatus,1);
                        }
                    }
                    break;
                case 'delivery': //已交付
                    if($item->status != 'delivery'){
                        $deliverArray = $requirementModel->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $this->updateStatusById('delivery',$item->id);
                        $this->loadModel('consumed')->record('opinion', $item->id, 0, 'guestjk', $item->status, 'delivery');
                        if(!empty($deliverArray))
                        {
                            $this->loadModel('action')->createActions('opinion', $deliverArray, 'deliveryscript',$langStatus,1);
                        }
                    }
                    break;
                case 'online': //上线成功
                    if($item->status != 'online'){
                        $onlineStatusArray = $requirementModel->insertActionArray($item->id,$paramsArray['code'],$item->status);
                        $onlineTimeByDemand = $paramsArray['onlineTimeByDemand'];
                        $this->updateStatusById('online',$item->id,$onlineTimeByDemand);
                        $this->loadModel('consumed')->record('opinion', $item->id, 0, 'guestjk', $item->status, 'online');
                        if(!empty($onlineStatusArray))
                        {
                            $this->loadModel('action')->createActions('opinion', $onlineStatusArray, 'onlinescript',$langStatus,1);
                        }
                    }else{
                        //原状态为上线成功需将 二线单子更大的时间更新
                        $onlineTimeByDemand = $paramsArray['onlineTimeByDemand'];
                        $this->updateStatusById('online',$item->id,$onlineTimeByDemand);
                    }
                    break;
            }
        }
    }

    /**
     * @Notes: 更新状态
     * @Date: 2023/4/21
     * @Time: 15:45
     * @Interface updateStatusById
     * @param $status
     * @param $id
     * @param string $onlineTimeByDemand
     */
    public function updateStatusById($status,$id,$onlineTimeByDemand = '')
    {
        if(empty($onlineTimeByDemand)){
            $this->dao->update(TABLE_OPINION)->set('status')->eq($status)->set('onlineTimeByDemand')->eq(NULL)->where('id')->eq($id)->exec();
        }else{
            $this->dao->update(TABLE_OPINION)->set('status')->eq($status)->set('onlineTimeByDemand')->eq($onlineTimeByDemand)->where('id')->eq($id)->exec();
        }
    }
    /**
     * @Notes: 获取近一年需求意向数据，用于状态联动
     * @Date: 2023/4/11
     * @Time: 16:19
     * @Interface getOpinionInfoAboutStatus
     */
    public function getOpinionInfoAboutStatus()
    {
        $opinionInfo = $this->dao->select('id,status,code')
            ->from(TABLE_OPINION)
            ->where('status')
            ->ne("deleted")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll();
//        $opinionInfo = $this->dao->select('id,status,code')
//            ->from(TABLE_OPINION)
//            ->where('id')
//            ->eq($testId)
//            ->fetchAll();
        return $opinionInfo;
    }

    /**
     * 7.2.3.1
     * 当需求意向下未删除的需求任务数量=0，则需要联动需求意向的流程状态为“审核已通过”，如果需求意向状态本身是“审核已通过”，则不需要update
     */
    public function changeToPass()
    {
        $opinions = $this->dao->select('id,status,demandCode')
            ->from(TABLE_OPINION)
            ->where('status')
            ->in("subdivided,delivery,online")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $opinionIds = array_keys($opinions);
        $updateIdList = [];
        $updateIdListCreated = [];
        foreach ($opinionIds as $opinionId) {
            $count = $this->dao->select('id')
                ->from(TABLE_REQUIREMENT)
                ->where('opinion')
                ->eq($opinionId)
                ->andwhere('status')
                ->ne("deleted")
                ->count();
            if ($count == 0) {
                if(empty($opinions[$opinionId]->demandCode)){ //内部意向 变审核通过
                    $updateIdList[] = $opinionId;
                    $this->loadModel('consumed')->recordAuto('opinion', $opinionId, 0, $opinions[$opinionId]->status, 'pass');
                } else { //外部意向 变已分配
                    $updateIdListCreated[] = $opinionId;
                    $this->loadModel('consumed')->recordAuto('opinion', $opinionId, 0, $opinions[$opinionId]->status, 'created');
                }

            }
        }
        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
        if(count($updateIdListCreated) > 1000){ $updateIdListCreated = array_slice($updateIdListCreated, 0, 1000);} //in 最多1000，多出的下次再处理
//        $this->dao->update(TABLE_OPINION)->set('status')->eq('pass')->where('id')->in($updateIdList)->exec();
//        $this->dao->update(TABLE_OPINION)->set('status')->eq('created')->where('id')->in($updateIdListCreated)->exec();
//        $this->loadModel('action')->createActions('opinion', $updateIdList, 'pass');
//        $this->loadModel('action')->createActions('opinion', $updateIdListCreated, 'created');
        return "pass opinionIds:" . implode(',', $updateIdList);
    }

    /**
     * 7.2.3.2
     * 当需求任务流程状态 in (已发布、已拆分) > 0，则需要联动需求意向状态为“已拆分”，
     * 联动前需要判断需求意向状态是否满足update条件：需求意向流程状态 in (审核已通过、已拆分、已交付、上线成功、已关闭)，其他流程状态时不联动为“已拆分”。
     */
    public function changeToSubdivided()
    {
        $opinions = $this->dao->select('id, status')
            ->from(TABLE_OPINION)
            ->where('status')
            ->in("pass,delivery,closed,online") //审核已通过、(已拆分 不需要)、已交付、上线成功、已关闭 online?
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $opinionIds = array_keys($opinions);
        $updateIdList = [];
        foreach ($opinionIds as $opinionId) {
            $count = $this->dao->select('id')
                ->from(TABLE_REQUIREMENT)
                ->where('opinion')
                ->eq($opinionId)
                ->andwhere('status')
                ->in("splited, published")
                ->count();
            if ($count > 0) {
                $updateIdList[] = $opinionId;
                $this->loadModel('consumed')->recordAuto('opinion', $opinionId, 0, $opinions[$opinionId]->status, 'subdivided');
            }
        }
        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
//        $this->dao->update(TABLE_OPINION)->set('status')->eq('subdivided')->where('id')->in($updateIdList)->andwhere('status')->ne('subdivided')->exec();
//        $this->loadModel('action')->createActions('opinion', $updateIdList, 'subdivided');
        return "Subdivided opinionIds:" . implode(',', $updateIdList);
    }

    /**
     *  7.3.3.3
     * 当需求意向下的所有需求任务状态除了已关闭，其他全部为“已交付” && 需求意向为“已拆分”时，自动流转“需求意向”状态为“已交付”，待处理人置空。，
     * 比如需求任务A和B，如果A已关闭，B为待上线，则需求意向要update 为已交付，状态联动忽略已关闭的。
     */
    public function changeToDelivery()
    {
        $opinions = $this->dao->select('id,status')
            ->from(TABLE_OPINION)
            ->where('status')
            ->eq("subdivided")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $opinionIds = array_keys($opinions);
        $updateIdList = [];
        foreach ($opinionIds as $opinionId) {
            $count = $this->dao->select('id')
                ->from(TABLE_REQUIREMENT)
                ->where('opinion')
                ->eq($opinionId)
                ->andwhere('status')
                ->notin("closed, delivered, onlined, deleted")
                ->count();
            $onlineDate = $this->dao->select('onlineTimeByDemand') //最新的上线时间
                ->from(TABLE_REQUIREMENT)
                ->where('opinion')
                ->eq($opinionId)
                ->andwhere('status')
                ->eq("onlinesuccess")
                ->orderby("onlineTimeByDemand_desc")
                ->fetch('onlineTimeByDemand');
            if ($count == 0 && $onlineDate) {
                $updateIdList[] = $opinionId;
                $this->dao->update(TABLE_OPINION)->set('status')->eq('delivery')->set('dealUser')->eq('')->set('onlineTimeByDemand ')->eq($onlineDate)->where('id')->eq($opinionId)->exec();
                $this->loadModel('consumed')->recordAuto('opinion', $opinionId, 0, $opinions[$opinionId]->status, 'delivery');
            }
        }
//        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
//        $this->dao->update(TABLE_OPINION)->set('status')->eq('delivery')->set('dealUser')->eq('')->where('id')->in($updateIdList)->exec();
//        $this->loadModel('action')->createActions('opinion', $updateIdList, 'delivery');
        return "delivery opinionIds:" . implode(',', $updateIdList);
    }

    /**
     * 7.4.3.5
     * 当需求意向下的所有需求任务状态除了已关闭，其他全部为“上线成功” && 需求意向为“已交付”时，自动流转“需求意向”状态为“上线成功”，待处理人置空。
     * 比如需求任务A和B，如果A已关闭，B为“上线成功”，则需求意向要upate 为“上线成功”，状态联动忽略已关闭的。需求意向流程状态不需要联动“上线失败”。
     */
    public function changeToOnlineByRequirement()
    {
        $opinions = $this->dao->select('id,status')
            ->from(TABLE_OPINION)
            ->where('status')
            ->in("delivery, subdivided")
            ->andWhere('createdDate')
            ->gt(date('Y-m-d', strtotime("-1 year")))
            ->fetchAll('id');
        $opinionIds = array_keys($opinions);
        $updateIdList = [];
        foreach ($opinionIds as $opinionId) {
            $count = $this->dao->select('id')
                ->from(TABLE_REQUIREMENT)
                ->where('opinion')
                ->eq($opinionId)
                ->andwhere('status')
                ->notin("closed, onlined, deleted") //除了 已关闭 已删除 已上线 无其他
                ->count();
            $countOnlineSuccess =  $this->dao->select('id') //至少有一条成功的才算
                ->from(TABLE_REQUIREMENT)
                ->where('opinion')
                ->eq($opinionId)
                ->andwhere('status')
                ->eq("onlined")
                ->count();
            if($count == 0 && $countOnlineSuccess > 0){
                $updateIdList[] = $opinionId;
                $onlineDate =  $this->dao->select('onlineTimeByDemand') //至少有一条成功的才算
                ->from(TABLE_REQUIREMENT)
                    ->where('opinion')
                    ->eq($opinionId)
                    ->andwhere('status')
                    ->eq("onlined")
                    ->orderby("onlineTimeByDemand_desc")
                    ->fetch('onlineTimeByDemand');
                $this->dao->update(TABLE_OPINION)->set('status')->eq('online')->set('dealUser')->eq('')->set('onlineTimeByDemand')->eq($onlineDate)->where('id')->eq($opinionId)->exec();
                $this->loadModel('consumed')->recordAuto('opinion', $opinionId, 0, $opinions[$opinionId]->status, 'online');
            }
        }
//        if(count($updateIdList) > 1000){ $updateIdList = array_slice($updateIdList, 0, 1000);} //in 最多1000，多出的下次再处理
//        $this->dao->update(TABLE_OPINION)->set('status')->eq('online')->set('dealUser')->eq('')->where('id')->in($updateIdList)->exec();
//        $this->loadModel('action')->createActions('opinion', $updateIdList, 'online');
        return "onlined requirementIds:" . implode(',', $updateIdList);

    }


    /**
     * Desc: 查询opinion表字段
     * Date: 2022/5/19
     * Time: 19:44
     *
     * @param $opinionID
     * @return array
     *
     */
    public function getOwnerDefineFieldById($opinionID)
    {
        return $this->dao->select('id,code,deadLine,createdDate,name,`union`,status,sourceMode,receiveDate')->from(TABLE_OPINION)->where('id')->eq($opinionID)->fetch();
    }

    /* 获取工时投入信息。*/
    public function getConsumed($opinion)
    {
        if (empty($opinion)) return array();

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('opinion')
            ->andWhere('objectID')->eq($opinion->id)
            ->fetchAll();

        $opinion->consumed = $cs;
        return $opinion;
    }

    public function editassignedto($id){
        $oldOpinion = $this->getByID($id);
        $opinion = fixer::input('post')
            ->remove('comment')
            ->join('assignedTo', ',')
            ->get();
        if(empty($opinion->assignedTo)){
            dao::$errors['assignedTo'] =  sprintf($this->lang->opinion->error->empty, $this->lang->opinion->assignedTo);
            return;
        }
        $this->dao->update(TABLE_OPINION)->data($opinion)
            ->batchCheck($this->config->opinioninside->editassignedto->requiredFields, 'notempty')
            ->where('id')->eq($id)
            ->exec();
        if (!dao::isError()) return common::createChanges($oldOpinion, $opinion);
    }

    public function getByIdSimple($opinionID)
    {
        if (empty($opinionID)) return array();
        return $this->dao->findByID($opinionID)->from(TABLE_OPINION)->fetch();
    }

    public function getByCode($demandCode)
    {
        $opinion = $this->dao->select('*')->from(TABLE_OPINION)->where('demandCode')->eq($demandCode)->andWhere('status')->ne('deleted')->fetch();
        return $opinion;
    }

    /**
     * Create a opinion.
     *
     * @access public
     * @return void
     */
    public function create($isSync = false)
    {
        /* 获取post数据并处理数据。*/
        $opinion = fixer::input('post')
            ->add('createdBy', $isSync ? 'guestcn' :$this->app->user->account)
            ->add('createdDate', helper::today())
            ->add('status', 'created')
            ->add('dealUser', $isSync ? $this->lang->opinioninside->apiDealUserList['userAccount'] : $this->post->assignedTo) //同步过来的待处理人为李甜梓，创建的单子待处理人为产品经理
            ->add('receiveDate', $isSync?helper::now():$this->post->receiveDate)  //同步过的单子接受日期为同步时间
            ->join('synUnion', ',')
            ->join('union', ',')
            ->join('mailto',',')
            ->remove('uid,files,labels,comment')
            ->stripTags($this->config->opinioninside->editor->create['id'], $this->config->allowedTags)
            ->get();

        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($opinion->deadline))
        {
            dao::$errors['deadline'] =  sprintf($this->lang->opinioninside->error->empty, $this->lang->opinioninside->deadline);
            return;
        }

        //需求提出时间
        if(!$this->loadModel('common')->checkJkDateTime($opinion->date))
        {
            dao::$errors['date'] =  sprintf($this->lang->opinioninside->error->empty, $this->lang->opinioninside->date);
            return;
        }

        //需求接收时间
        if(!$this->loadModel('common')->checkJkDateTime($opinion->receiveDate))
        {
            dao::$errors['receiveDate'] =  sprintf($this->lang->opinioninside->error->empty, $this->lang->opinioninside->receiveDate);
            return;
        }

        $opinion->sourceOpinion = 2;

        //迭代二十六 创建人如果是产品经理，这状态直接为"审核已通过"
        $consumedStatus = 'created';
        $poList = $this->loadModel('dept')->getPoUser();//产品经理
        if(in_array($this->app->user->account,array_keys($poList)))
        {
            $consumedStatus = 'pass';
            $opinion->status = 'pass';
        }
        /* 插入数据后，判断是否有误，然后更新code参数，并保存文件。*/
        $opinion = $this->loadModel('file')->processImgURL($opinion, $this->config->opinioninside->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_OPINION)->data($opinion)
            ->batchCheck($this->config->opinioninside->create->requiredFields, 'notempty')->exec();
        if (!dao::isError()) {
            $opinionID = $this->dao->lastInsertID();
//            $code = substr($opinion->createdDate, 0, 4) . sprintf('%03d', $opinionID);
            $code = 'CFIT-W-' . date('Y') .sprintf('%02d', $opinionID);
            $this->dao->update(TABLE_OPINION)->set('code')->eq($code)->where('id')->eq($opinionID)->exec();

            $this->loadModel('file')->updateObjectID($this->post->uid, $opinionID, 'opinion');
            $this->file->saveUpload('opinion', $opinionID);
            if (!$isSync) {
                $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, '-', $consumedStatus);
            } else {
                $this->loadModel('consumed')->record('opinion', $opinionID, 0, 'guestcn', '-', $consumedStatus);
            }
            return $opinionID;
        }

        return false;
    }

    /**
     * Update a opinion.
     *
     * @access int $opinionID
     * @access boolean $isSync
     * @access public
     * @return void
     */
    public function update($opinionID, $isSync = false)
    {

        /* 获取旧的需求意向数据，并处理post请求参数。*/
        $oldOpinion = $this->getByID($opinionID);
        $opinion = fixer::input('post')
            ->add('editedBy', $isSync ? 'guestcn' : $this->app->user->account)
            ->add('editedDate', helper::now())
            ->remove('uid,files,labels,comment')
            ->join('synUnion', ',')
            ->join('union', ',')
            ->join('mailto',',')
            ->setIF(empty($oldOpinion->demandCode), 'status', 'created') //外部单子编辑时不修改状态。
            ->add('dealUser', $isSync ? $this->lang->opinioninside->apiDealUserList['userAccount'] : $this->post->assignedTo)
            ->stripTags($this->config->opinioninside->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if(isset($opinion->deadline) && $opinion->deadline == ''){
            unset($opinion->deadline);
        }
        if(!isset($opinion->mailto)){
            $opinion->mailto = '';
        }

        //期望完成时间
        if(!$this->loadModel('common')->checkJkDateTime($opinion->deadline))
        {
            dao::$errors['deadline'] =  sprintf($this->lang->opinion->error->empty, $this->lang->opinion->deadline);
            return;
        }

        //需求提出时间
        if(!$this->loadModel('common')->checkJkDateTime($opinion->date))
        {
            dao::$errors['date'] =  sprintf($this->lang->opinion->error->empty, $this->lang->opinion->date);
            return;
        }

        //需求接收时间
        if(!$this->loadModel('common')->checkJkDateTime($opinion->receiveDate))
        {
            dao::$errors['receiveDate'] =  sprintf($this->lang->opinion->error->empty, $this->lang->opinion->receiveDate);
            return;
        }

        /* 执行SQL，处理相关附件，并获取变动的字段进行返回。*/
        $opinion = $this->loadModel('file')->processImgURL($opinion, $this->config->opinioninside->editor->edit['id'], $this->post->uid);

        //迭代二十六 待处理人发生变化 忽略自动恢复
        if($oldOpinion->dealUser != $opinion->dealUser){
            $opinion->ignore = '';
        }

        $this->dao->update(TABLE_OPINION)->data($opinion)
            ->batchCheck($this->config->opinioninside->edit->requiredFields, 'notempty')
            ->where('id')->eq($opinionID)
            ->exec();

        if (!$isSync) {
            $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $oldOpinion->status,empty($oldOpinion->demandCode)?'created':$oldOpinion->status);// 编辑不改变外部单子的状态
        } else {
            $this->loadModel('consumed')->record('opinion', $opinionID, 0, 'guestcn', $oldOpinion->status, $oldOpinion->status);
        }
        $this->loadModel('file')->updateObjectID($this->post->uid, $opinionID, 'opinion');
        $this->file->saveUpload('opinion', $opinionID);

        return common::createChanges($oldOpinion, $opinion);
    }

    /**
     * Project: chengfangjinke
     * Method: suspend
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:14
     * Desc: This is the code comment. This method is called suspend.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $demandID
     * @return array
     */
    public function suspend($opinionID)
    {
        $oldOpinion = $this->getByID($opinionID);
        $opinion = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_OPINION)->data($opinion)
            ->where('id')->eq($opinionID)
            ->exec();

        $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $oldOpinion->status, 'suspend');

        return common::createChanges($oldOpinion, $opinion);
    }

    /**
     * Build search form.
     *
     * @param int $queryID
     * @param string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->opinioninside->search['actionURL'] = $actionURL;
        $this->config->opinioninside->search['queryID']   = $queryID;
        $this->config->opinioninside->search['params']['project']['values']    = array('0' => '') + $this->loadModel('project')->getProjects('');

        $this->loadModel('search')->setSearchParams($this->config->opinioninside->search);
    }

    /**
     * Project: chengfangjinke
     * Method: setListValue
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called setListValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function setListValue()
    {
        /* 导出需求意向数据调用该方法设置下拉选项的可选值。*/
        $this->app->loadLang('opinion');
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $newUsers = array();
        foreach ($users as $account => $name) {
            if (!$account) continue;
            $newUsers[$account] = $name . "(#$account)";
        }
        $usersArr = array_values($newUsers);

        $sourceModeList = $this->lang->opinion->sourceModeList;
        $categoryList = $this->lang->opinion->categoryList;
        $unionList = $this->lang->opinion->unionList;
        $statusList = $this->lang->opinioninside->statusList;
        $this->post->set('sourceModeList', join(',', $sourceModeList));
        $this->post->set('categoryList', join(',', $categoryList));
        $this->post->set('unionList', join(',', $unionList));
        $this->post->set('statusList', array_values($statusList));
        $this->post->set('createdByList', $usersArr);
        // $this->post->set('assignedToList', $usersArr);
        // $this->post->set('dealUserList', $usersArr);
        $this->post->set('listStyle', $this->config->opinioninside->export->listFields);
        $this->post->set('extraNum', 0);
    }

    /**
     * Project: chengfangjinke
     * Method: createFromImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called createFromImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function createFromImport()
    {
        /* 加载action、opinion和file模块，并获取导入数据。*/
        $this->loadModel('action');
        $this->loadModel('opinion');
        $this->loadModel('file');
        $now = helper::now();
        $data = fixer::input('post')->get();

        /* 加载purifier富文本过滤器。*/
        $this->app->loadClass('purifier', true);
        $purifierConfig = HTMLPurifier_Config::createDefault();
        $purifierConfig->set('Filter.YouTube', 1);
        $purifier = new HTMLPurifier($purifierConfig);

        /* 获取旧的需求意向数据。*/
        if (!empty($_POST['id'])) {
            $oldOpinions = $this->dao->select('*')->from(TABLE_OPINION)->where('id')->in(($_POST['id']))->fetchAll('id');
        }

        /* 初始化导入数据变量。*/
        $opinions = array();
        $line = 1;
        $names = array();
        foreach ($data->name as $key => $name) {
            /* 定义一个导入数据对象，如果name参数为空，则跳过该行数据。*/
            $opinionData = new stdclass();
            $specData = new stdclass();
            if (!$name) continue;
            if(array_search($name,$names)){
                dao::$errors[] = sprintf($this->lang->opinioninside->duplicateNameError, array_search($name,$names), $line);
            }else{
                $names[$line] = $name;
            }

            /* 将页面获取到的数据赋值给对象。*/
            $opinionData->name = $name;
            $opinionData->background = nl2br($purifier->purify($this->post->background[$key]));
            $opinionData->overview = nl2br($purifier->purify($this->post->overview[$key]));
            $opinionData->desc = nl2br($purifier->purify($this->post->desc[$key]));
            $opinionData->sourceMode = $data->sourceMode[$key];
            $opinionData->sourceName = $data->sourceName[$key];
            $opinionData->category = $data->category[$key];
            $opinionData->union = implode(',', $data->union[$key]);
            $opinionData->receiveDate = $data->receiveDate[$key];
            $opinionData->deadline = $data->deadline[$key];
            $opinionData->createdBy = $data->createdBy[$key];
            $opinionData->contact = $data->contact[$key];
            $opinionData->contactInfo = $data->contactInfo[$key];
            $opinionData->assignedTo = $data->assignedTo[$key];
//            $opinionData->workload = $data->workload[$key];
            $opinionData->status = $data->status[$key];
            $opinionData->dealUser = $data->dealUser[$key];
            $opinionData->remark = $data->remark[$key];
            $opinionData->sourceOpinion = 2;


            $ret = strtotime($data->receiveDate[$key]);
            if($ret === FALSE || $ret == -1){
                dao::$errors[] = sprintf($this->lang->opinioninside->timeError, $line, $this->lang->opinioninside->receiveDate);
            }else if(!$this->validateDate($data->receiveDate[$key],"Y-m-d") && !$this->validateDate($data->receiveDate[$key],"Y/m/d")){
                dao::$errors[] = sprintf($this->lang->opinioninside->timeError, $line, $this->lang->opinioninside->receiveDate);
            }

            $ret = strtotime($data->deadline[$key]);
            if($ret === FALSE || $ret == -1){
                dao::$errors[] = sprintf($this->lang->opinioninside->timeError, $line, $this->lang->opinioninside->deadline);
            }else if(!$this->validateDate($data->deadline[$key],"Y-m-d") && !$this->validateDate($data->deadline[$key],"Y/m/d")){
                dao::$errors[] = sprintf($this->lang->opinioninside->timeError, $line, $this->lang->opinioninside->deadline);
            }


            /* 判断那些字段是必填的。*/
            if (isset($this->config->opinioninside->import->requiredFields)) {
                $requiredFields = explode(',', $this->config->opinioninside->import->requiredFields);
                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($opinionData->$requiredField)) dao::$errors[] = sprintf($this->lang->opinioninside->noRequire, $line, $this->lang->opinioninside->$requiredField);
                }
            }

            $opinions[$key]['opinionData'] = $opinionData;
            $line++;
        }

        /* 判断是否由必填项，如果有，则提示错误信息。*/
        if (dao::isError()) die(js::error(dao::getError()));

        /* 进行导入数据处理。*/
        foreach ($opinions as $key => $newOpinion) {
            /* 判断当前数据是否已存在，不存在的则为$opinionID赋值为0。*/
            $opinionID = 0;
            $opinionData = $newOpinion['opinionData'];
            if (!empty($_POST['id'][$key]) and empty($_POST['insert'])) {
                $opinionID = $data->id[$key];
                if (!isset($oldOpinions[$opinionID])) $opinionID = 0;
            }

            /* 如果$opinionID有值，则说明需求意向已存在，按照更新的情况来处理。*/
            if ($opinionID) {
                $oldOpinion = $oldOpinions[$opinionID];
                $opinionChanges = common::createChanges($oldOpinion, $opinionData);

                if ($opinionChanges) {
                    $this->dao->update(TABLE_OPINION)
                        ->data($opinionData)
                        ->autoCheck()
                        ->batchCheck($this->config->opinioninside->create->requiredFields, 'notempty')
                        ->where('id')->eq((int)$opinionID)->exec();

                    if (!dao::isError()) {
                        if ($opinionChanges) {
                            $actionID = $this->action->create('opinion', $opinionID, 'Edited', '');
                            $this->action->logHistory($actionID, $opinionChanges);
                            $this->loadModel('consumed');
                            $this->consumed->record('opinion', $opinionID, 0, $opinionData->createdBy, '', $opinionData->status, array());
                        }
                    }
                }
            } else {
                /* 如果是全新插入的需求意向，处理好数据后，执行SQL进行数据插入。*/
                // $opinionData->createdBy = $this->app->user->account;
                $opinionData->createdDate = $now;
                if(empty($opinionData->status)){
                    $opinionData->status = 'created';
                }
                if(empty($opinionData->dealUser)){
                    $opinionData->dealUser = $opinionData->assignedTo;
                }
                $this->dao->insert(TABLE_OPINION)->data($opinionData)->autoCheck()->exec();
                if (!dao::isError()) {
                    $opinionID = $this->dao->lastInsertID();
//                    $code = substr($opinionData->createdDate, 0, 4) . sprintf('%03d', $opinionID);
                    $code = 'CFIT-W-'.substr($opinionData->createdDate, 0, 4) . sprintf('%03d', $opinionID);
                    $this->dao->update(TABLE_OPINION)->set('code')->eq($code)->where('id')->eq($opinionID)->exec();
                    $this->action->create('opinion', $opinionID, 'import', '');
                    $this->loadModel('consumed');
                    $this->consumed->record('opinion', $opinionID, 0, $opinionData->createdBy, '', $opinionData->status, array());
                }
            }
        }

        /* 判断数据是否处理完毕，处理完毕则删除导入文件，并清除session信息。*/
        if ($this->post->isEndPage) {
            unlink($this->session->fileImport);
            unset($_SESSION['fileImport']);
        }
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $opinion
     * @param $action
     * @return bool
     */
    public static function isClickable($opinion, $action)
    {
        global $app;
        /* 对操作转换成小写，根据状态判断当前操作是否允许高亮。*/
        $action = strtolower($action);
        /* 推送过来的需求意向不可操作 拆分、变更、删除、挂起。*/
        //拆分 审核已通过、已录入 待处理人
        if ($action == 'subdivide') return $app->user->account == 'admin' or (in_array($opinion->status,array('pass','subdivided')) and $app->user->account == $opinion->dealUser);
        //编辑 已录入、待更新、审核未通过
        if ($action == 'edit')      return $app->user->account == 'admin' or (in_array($opinion->status,array('created','waitupdate','reject')) and $app->user->account == $opinion->createdBy);
        //变更需求意向 审核已通过、已拆分 待处理人
        if ($action == 'change')    return  $app->user->account == 'admin' or (in_array($opinion->status,array('pass','subdivided')) and $app->user->account == $opinion->dealUser);
        //指派需求意向 已录入、审核已通过、已拆分 待处理人
        if ($action == 'assignment')return $app->user->account == 'admin' or (in_array($opinion->status,array('created','pass','subdivided')) and $app->user->account == $opinion->dealUser);
        //审批需求意向 已录入 待处理人
        if ($action == 'review')    return $app->user->account == 'admin' or (in_array($opinion->status,array('created')) and $app->user->account == $opinion->dealUser);
        //挂起需求意向 审核已通过、已拆分  创建人、后台自定义挂起人 页面控制人
        if ($action == 'close')     return $app->user->account == 'admin' or in_array($opinion->status,array('pass','subdivided'));
        //激活 挂起人
        if ($action == 'reset')     return $app->user->account == 'admin' or ($opinion->status == 'closed' and $app->user->account == $opinion->closedBy);
        //删除 已录入、审核未通过 创建人
        if ($action == 'delete')    return $app->user->account == 'admin' or ($opinion->status != 'deleted' and empty($opinion->children) and in_array($opinion->status,array('created','reject')) and $app->user->account == $opinion->createdBy);
        //忽略 非已挂起
        if ($action == 'ignore')    return $app->user->account == 'admin' or ($opinion->status != 'closed' and $app->user->account == $opinion->dealUser);
        //激活 忽略人和当前处理人
        if ($action == 'recoveryed')   return $app->user->account == 'admin' or ($opinion->status != 'closed' and (in_array($app->user->account,explode(',',$opinion->ignore)) or $app->user->account == $opinion->dealUser));

        return true;
    }

    public function assignment($id)
    {
        $oldOpinion = $this->getByID($id);
        $opinion = fixer::input('post')
            ->remove('comment')
            ->get();
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(empty($opinion->dealUser)){
            dao::$errors['dealUser'] =  sprintf($this->lang->opinioninside->error->empty, $this->lang->opinioninside->assignTo);
            return;
        }
        //迭代二十六 待处理人发生变化 忽略自动恢复
        if($oldOpinion->dealUser != $opinion->dealUser){
            $opinion->ignore = '';
        }

        $this->dao->update(TABLE_OPINION)->data($opinion)
            ->batchCheck($this->config->opinioninside->assignment->requiredFields, 'notempty')
            ->where('id')->eq($id)
            ->exec();

        $this->loadModel('consumed')->record('opinion', $id, 0, $this->app->user->account, $oldOpinion->status, $oldOpinion->status);
        if (!dao::isError()) return common::createChanges($oldOpinion, $opinion);
    }

    public function review($id)
    {
        $oldOpinion = $this->getByID($id);
        $opinion = fixer::input('post')
            ->remove('comment')
            ->get();
        if(empty($opinion->status)){
            dao::$errors['status'] =  sprintf($this->lang->opinioninside->error->empty, $this->lang->opinioninside->result);
            return;
        }
        if(empty($opinion->dealUser)){
            dao::$errors['dealUser'] =  sprintf($this->lang->opinioninside->error->empty, $this->lang->opinioninside->nextDealUser);
            return;
        }

        // 审核结果通过，状态处理
        if($opinion->status == 'pass')
        {
            $oldStatus = array_column($oldOpinion->consumed,'after');
            $requirementStatus = [];
            if(!empty($oldOpinion->requirements))
            {
                $requirementStatus = array_column($oldOpinion->requirements,'status');
            }

            // 变更 && 存在需求任务已拆分
            if(in_array('waitupdate',$oldStatus) && in_array('splited',$requirementStatus))
            {
                $opinion->status = 'subdivided';
            }elseif (in_array('waitupdate',$oldStatus) && $oldOpinion->beforeChangedStatus && !in_array('splited',$requirementStatus))
            {
                $opinion->status = $oldOpinion->beforeChangedStatus;
            }
        }
        //迭代二十六 待处理人发生变化 忽略自动恢复
        if($oldOpinion->dealUser != $opinion->dealUser){
            $opinion->ignore = '';
        }

        $this->dao->update(TABLE_OPINION)->data($opinion)
            ->batchCheck($this->config->opinioninside->review->requiredFields, 'notempty')
            ->where('id')->eq($id)
            ->exec();

        $this->loadModel('consumed')->record('opinion', $id, 0, $this->app->user->account, $oldOpinion->status, $opinion->status);
    }

    public function recover($opinionID = 0)
    {
        $oldOpinion = $this->getByID($opinionID);
        $opinion = fixer::input('post')
            ->remove('comment')
            ->get();

        $this->dao->update(TABLE_OPINION)->data($opinion)
            ->where('id')->eq($opinionID)
            ->exec();

        $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, 'suspend', $opinion->status);
        return common::createChanges($oldOpinion, $opinion);

    }

    public function close($opinionID)
    {
        if (empty($_POST['comment'])) {
            dao::$errors['comment'] = sprintf($this->lang->opinioninside->error->empty,$this->lang->opinioninside->comment);
            return false;
        }
        // 更新需求意向状态
        $opinionItem = $this->getByID($opinionID);
        $data = new stdclass();
        $data->lastStatus = $opinionItem->status; // 记录关闭前状态
        $data->status = 'closed';
        $data->closedBy = $this->app->user->account;
        $data->closedDate = helper::today();
        $this->dao->update(TABLE_OPINION)->data($data)->where('id')->eq($opinionID)->exec();
        $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $opinionItem->status, 'closed', array());

        // 更新需求任务状态
        $requirementItem = $this->loadModel('requirementinside')->getByOpinion($opinionID);
        $data1 = new stdclass();
        foreach ($requirementItem as $requirement)
        {
            if($requirement->status == 'closed'){
                continue; // 防止重复关闭
            }
            $data1->lastStatus = $requirement->status; // 记录关闭前状态
            $data1->status = 'closed';
            $data1->closedBy = $this->app->user->account;
            $data1->closedDate = helper::today();
            $this->dao->update(TABLE_REQUIREMENT)
                ->data($data1)
                ->where('id')->eq($requirement->id)
                ->exec();
            if(!dao::isError())
            {
                $this->loadModel('action')->create('requirement', $requirement->id, 'suspenditem', $this->post->comment);
                $this->loadModel('consumed')->record('requirement', $requirement->id, 0, $this->app->user->account, $requirement->status, 'closed', array());
            }
        }

        // 更新需求条目状态
        $demandItem = $this->loadModel('demandinside')->getByOpinion($opinionID);
        $data2 = new stdclass();
        foreach ($demandItem as $demand)
        {
            if($demand->status == 'suspend'){
                continue; // 防止重复关闭
            }
            $data2->lastStatus = $demand->status; // 记录关闭前状态
            $data2->status = 'suspend';
            $data2->closedBy = $this->app->user->account;
            $data2->closedDate = helper::today();
            $this->dao->update(TABLE_DEMAND)
                ->data($data2)
                ->where('id')->eq($demand->id)
                ->exec();
            if(!dao::isError())
            {
                $this->loadModel('action')->create('demand', $demand->id, 'suspend', $this->post->comment);
                $this->loadModel('consumed')->record('demand', $demand->id, 0, $this->app->user->account, $demand->status, 'suspend', array());
            
            }
        }
    }

    public function activate($opinionID)
    {
        if (empty($_POST['comment'])) {
            dao::$errors['comment'] = sprintf($this->lang->opinioninside->error->empty,$this->lang->opinioninside->comment);
            return false;
        }
        $old = $this->getByID($opinionID);
        $data = new stdclass();
        $data->status = $old->lastStatus;
        $data->activedBy = $this->app->user->account;
        $data->activedDate = helper::today();
        $this->dao->update(TABLE_OPINION)
            ->data($data)
            ->where('id')->eq($opinionID)
            ->exec();
        $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, 'closed', $data->status, array());


        // 更新需求任务状态
        $requirementItem = $this->loadModel('requirementinside')->getByOpinion($opinionID);
        $data1 = new stdclass();
        foreach ($requirementItem as $requirement)
        {
            if($requirement->status != 'closed'){
                continue;
            }
            $data1->status = $requirement->lastStatus;
            $data1->activatedBy = $this->app->user->account;
            $data1->activatedDate = helper::today();
            $this->dao->update(TABLE_REQUIREMENT)
                ->data($data1)
                ->where('id')->eq($requirement->id)
                ->exec();
            if(!dao::isError())
            {
                $this->loadModel('action')->create('requirement', $requirement->id, 'activated', $this->post->comment);
                $this->loadModel('consumed')->record('requirement', $requirement->id, 0, $this->app->user->account, 'closed', $data1->status, array());
            }
        }

        // 更新需求条目状态
        $demandItem = $this->loadModel('demandinside')->getByOpinion($opinionID);
        foreach ($demandItem as $demand)
        {
            if($demand->status != 'suspend'){
                continue;
            }
            $this->dao->update(TABLE_DEMAND)
                ->set('status')->eq($demand->lastStatus)
                ->where('id')->eq($demand->id)
                ->exec();
            if(!dao::isError())
            {
                $this->loadModel('action')->create('demand', $demand->id, 'activated', $this->post->comment);
                $this->loadModel('consumed')->record('demand', $demand->id, 0, $this->app->user->account, 'suspend', $demand->lastStatus, array());
            }
        }
    }


    public function reset($opinionID)
    {
        // 重启需求意向
        $old = $this->getByID($opinionID);
        $this->dao->update(TABLE_OPINION)
            ->set('status')->eq($old->lastStatus)
            ->where('id')->eq($opinionID)
            ->exec();
    }
    /**
     * Project: chengfangjinke
     * Method: subdivide
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called subdivide.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $opinionID
     * @return mixed
     */
    public function subdivide($opinionID)
    {
        $this->app->loadLang('demandinside');
        //判断空处理
        if (empty($_POST['nextUser'])) {
            dao::$errors[] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->dealUser);
        }
        $demandTitle = $_POST['demandTitle'];
        $demandDesc = $_POST['demandDesc'];

        foreach ($demandTitle as $title) {
            if ($title == '') {
                $errors['demandTitle'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->opinioninside->demandTitle);
                return dao::$errors = $errors;
            }
        }
        foreach ($demandDesc as $desc) {
            if ($desc == '') {
                $errors['demandDesc'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->opinioninside->demandDesc);
                return dao::$errors = $errors;
            }
        }

        if (dao::isError()) return false;

        $this->loadModel('consumed');
        $data = fixer::input('post')
            ->remove('uid,demandDesc')
            ->get();
        $opinion = $this->getByID($opinionID);
        // 去除无效需求。
        foreach ($data->demandTitle as $i => $title) {
            if (!$title) unset($data->demandTitle[$i]);
        }

//        $consumed = count($data->demandTitle) ? round($data->consumed / count($data->demandTitle), 1) : 0;

        $demandIdList = array();
        $uid = $this->post->uid;
        $demandDescList = $_POST['demandDesc'];
        foreach ($data->demandTitle as $i => $title) {
            if (!$title) continue;

            $demand = new stdclass();
            $demand->opinionID = $opinionID;
            $demand->title = $title;
            //$demand->desc        = $_POST[demandDesc[$i]];
            $demand->type = $opinion->sourceMode;
            $demand->source = $opinion->sourceName;
            $demand->endDate = $opinion->deadline;
            $demand->dealUser = $data->nextUser;
            $demand->createdBy = $this->app->user->account;
            $demand->createdDate = helper::today();
            $demand->createdDept = $this->app->user->dept;
            $demand->lastDealDate = date('Y-m-d');

            // 处理富文本字段内容。
            unset($_POST);
            $_POST['desc'] = $demandDescList[$i];
            $postData = fixer::input('post')->stripTags('desc', $this->config->allowedTags)->get();
            $demand->desc = $postData->desc;
            $demand = $this->loadModel('file')->processImgURL($demand, 'desc', $this->post->uid);

            $demand->sourceRequirement = 2; //内部
            $this->dao->insert(TABLE_DEMAND)->data($demand)->autoCheck()->exec();

            $demandID = $this->dao->lastInsertID();

            $this->loadModel('file')->updateObjectID($uid, $demandID, 'demand');
            $this->file->saveUpload('demand', $demandID);

            // 更新需求代号。
            $date = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_DEMAND)->where('createdDate')->eq($date)->fetch('c');
            $code = 'CFIT-WD-' . date('Ymd-') . sprintf('%02d', $number);
            $this->dao->update(TABLE_DEMAND)->set('code')->eq($code)->where('id')->eq($demandID)->exec();

            // 将本地拆分工时平均后，记录到需求的工时中。
            $this->consumed->record('demand', $demandID, 0, $this->app->user->account, '', 'wait', array());

            // 记录拆分的动作。
            $this->loadModel('action')->create('demand', $demandID, 'created');
            $demandIdList[] = $demandID;
        }
        /* 将需求意向的状态修改为已拆分。*/
        if (!empty($demandIdList)) {
            $this->dao->update(TABLE_OPINION)->set('status')->eq('subdivided')
                ->where('id')->eq($opinionID)
                ->exec();
        }
        return $demandIdList;
    }

    /**
     * sendmail
     *
     * @param int $opinionID
     * @param int $actionID
     * @access public
     * @return void
     */
    public function sendmail($opinionID, $actionID)
    {
        /* 加载mail模块用于发信通知，获取需求意向和人员信息。*/
        $this->loadModel('mail');
        $opinion = $this->getById($opinionID);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 处理多选字段需求单位,待处理人映射 */
        $opinion->unionCopy = $this->ecodeSelfConfigFileds($this->lang->opinioninside->unionList,$opinion->union,',');
        $opinion->dealUserCopy = $this->ecodeSelfConfigFileds($users,$opinion->dealUser,',');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setOpinionMail) ? $this->config->global->setOpinionMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'opinion';

        /* 处理邮件发信的标题和日期。*/
//        $bestDate  = empty($opinion->changedDate) ? '' : $requirement->changedDate;
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);


        /* Get action info. */
        /* 当前需求意向的操作记录。*/
        $action = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        /* 获取当前模块路径，然后获取发信模板，为发信模板赋值。*/
        $modulePath = $this->app->getModulePath($appName = '', 'opinion');
        $oldcwd = getcwd();
        $viewFile = $modulePath . 'view/pendingsendmail.html.php';
        chdir($modulePath . 'view');
        if (file_exists($modulePath . 'ext/view/sendmail.html.php')) {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }
        ob_start();
        include $viewFile;
        foreach (glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        /* 获取发信人和抄送人数据。*/
        $sendUsers = $this->getToAndCcList($opinion);
        if($action->action == 'editassignedto') $sendUsers = array($opinion->assignedTo,'') ;
        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;
        $subject = $this->getSubject($opinion, $action->action);
        /* Send mail. */
        /* 调用mail模块的send方法进行发信。*/
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if ($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Get mail subject.
     *
     * @param object $opinion
     * @param string $actionType created|edited
     * @access public
     * @return string
     */
    public function getSubject($opinion, $actionType)
    {
        /* Set email title. */
        if( strpos($actionType,'sync')  !== 0 and !($opinion->demandCode && $actionType == 'deleted'))
        {
            return sprintf($this->lang->opinioninside->mail->$actionType, $this->app->user->realname, $opinion->id, $opinion->name);
        }elseif($actionType=='editassignedto'){
            return sprintf($this->lang->opinioninside->mail->$actionType, $this->app->user->realname, $opinion->id, $opinion->name);
        }else{
            if($actionType == 'deleted') $actionType = 'syncdeleted';
            return sprintf($this->lang->opinioninside->mail->$actionType, $opinion->id, $opinion->name);
        }
    }

    /**
     * Get toList and ccList.
     *
     * @param object $opinion
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($opinion)
    {
        /* Set toList and ccList. */
        /* 初始化发信人和抄送人变量，获取发信人和抄送人数据。*/
        $toList = $opinion->dealUser;
        $ccList = '';
        if($opinion->status == 'created') {
            $ccList = str_replace(' ', '', trim($opinion->mailto, ','));
        }
        return array($toList, $ccList);
    }

    public function updatePlanDeadline($id,$date)
    {
        $requirement = $this->dao->select('opinion')->from(TABLE_REQUIREMENT)->where('id')->eq($id)->fetch();
        $data        = $this->dao->select('planDeadline')->from(TABLE_OPINION)->where('id')->eq($requirement->opinion)->fetch();
        if(empty($data->planDeadline)||strtotime($date)-strtotime($data->planDeadline)>0){
            $this->dao->update(TABLE_OPINION)->set('planDeadline')->eq($date)->where('id')->eq($requirement->opinion)->exec();
        }
    }

    /**
     * Project: chengfangjinke
     * Method: getProgress
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:50
     * Desc: This is the code comment. This method is called getProgress.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $opinions
     * @return array
     */
    public function getProgress($opinions)
    {
        /* 获取所有任务及其所属项目计划信息。*/
        $taskMap = array();
        $tasks = $this->dao->select('t1.*,t2.id pid')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t2')
            ->on('t1.project = t2.project')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.parent')->eq(0)
            ->fetchAll('id');
        foreach ($tasks as $task) {
            if (!isset($taskMap[$task->pid])) $taskMap[$task->pid] = array('estimate' => 0, 'consumed' => 0, 'left' => 0, 'progress' => 0);
            $taskMap[$task->pid]['estimate'] += $task->estimate;
            $taskMap[$task->pid]['progress'] += $task->progress * $task->estimate;
        }

        /* 计算项目计划完成进度。*/
        $progress = array();
        foreach ($taskMap as $project => $data) {
            $progress[$project] = !empty($data['estimate']) ? round(($data['progress'] / $data['estimate'])) : 0;
        }
        return $progress;

        /* No need executions. Skip.*/
        $projectMap = $this->dao->select('id,project')->from(TABLE_PROJECTPLAN)->fetchAll('id');
        $projectList = array();
        foreach ($opinions as $opinion) {
            if (!isset($opinion->children)) continue;

            foreach ($opinion->children as $requirement) {
                if ($requirement->project and isset($projectMap[$requirement->project])) $projectList[] = $projectMap[$requirement->project]->project;
            }
        }

        if (empty($projectList)) return array();

        $executions = $this->dao->select('*')->from(TABLE_EXECUTION)
            ->where('project')->in($projectList)
            ->andWhere('type')->eq('stage')
            ->andWhere('grade')->eq(1)
            ->fetchAll('id');

        $tasks = $this->dao->select('id, execution, estimate, consumed, `left`, status, closedReason')
            ->from(TABLE_TASK)
            ->where('execution')->in(array_keys($executions))
            ->andWhere('parent')->lt(1)
            ->andWhere('deleted')->eq(0)
            ->fetchGroup('execution', 'id');

        $hours = array();
        $emptyHour = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);
        /* Compute totalEstimate, totalConsumed, totalLeft. */
        foreach ($tasks as $executionID => $executionTasks) {
            $hour = (object)$emptyHour;
            foreach ($executionTasks as $task) {
                if ($task->status != 'cancel') {
                    $hour->totalEstimate += $task->estimate;
                    $hour->totalConsumed += $task->consumed;
                }
                if ($task->status != 'cancel' and $task->status != 'closed') $hour->totalLeft += $task->left;
            }
            $hours[$executionID] = $hour;
        }

        /* Compute totalReal and progress. */
        foreach ($hours as $hour) {
            $hour->totalEstimate = round($hour->totalEstimate, 1);
            $hour->totalConsumed = round($hour->totalConsumed, 1);
            $hour->totalLeft = round($hour->totalLeft, 1);
            $hour->totalReal = $hour->totalConsumed + $hour->totalLeft;
            $hour->progress = $hour->totalReal ? round($hour->totalConsumed / $hour->totalReal, 3) * 100 : 0;
        }

        $projectExecutions = array();
        foreach ($hours as $executionID => $hour) {
            $progress = $hour->totalReal ? round($hour->totalConsumed / $hour->totalReal, 3) * 100 : 0;
            $projectID = $executions[$executionID]->project;
            if (!isset($projectExecutions[$projectID])) {
                $projectExecutions[$projectID] = array();
            }

            $exe = $executions[$executionID];
            //$exe->progress = $progress;
            $projectExecutions[$projectID][] = $exe;
        }

        $result = array();
        foreach ($projectMap as $planID => $p) {
            if (isset($projectExecutions[$p->project])) {
                $result[$planID] = $projectExecutions[$p->project];
            }
        }
        return $result;
    }

    /**
     * 提供给需求任务创建-状态不包含删除、审核未通过、待更新、清总同步，
     * shixuyang
     * @param $orderBy
     * @return mixed
     */
    public function getPairsByRequmentBrowse($orderBy = 'id_desc')
    {
        $opinions = $this->dao->select('id,name')->from(TABLE_OPINION)
            ->where('status')->ne('deleted')
            ->andWhere('status')->ne('reject')
            ->andWhere('status')->ne('waitupdate')
            ->andWhere('status')->ne('created')
            ->andWhere('demandCode')->isNull()
            ->andWhere('sourceOpinion')->eq(2)
            ->orderBy($orderBy)
            ->fetchPairs();
        return $opinions;
    }

    /**
     * TongYanQi 2022/12/13
     * 已挂起的需求意向只能由挂起人选择
     */
    public function getOpinionsPairsByUser($orderBy='id_desc'){
        $opinions = $this->dao->select('id,name,status,dealUser,closedBy, createdBy, closedDate,assignedTo')->from(TABLE_OPINION)
        ->where('status')->ne('deleted')
        ->andWhere('status')->ne('reject')
        ->andWhere('status')->ne('waitupdate')
        ->andWhere('status')->ne('created')
        ->andWhere('sourceOpinion')->eq(2)
        ->orderBy($orderBy)
        ->fetchAll('id');
        $list = [];
//        $expDate = date('Y-m-d h:i:s', strtotime('-3 months'));
        foreach ($opinions as $opinion){
            //如果是已挂起 大于3个月的 不显示 closed 之前已经被为已挂起 不是已关闭了
//            if($opinion->status == 'closed' && $opinion->closedDate < $expDate)
//            {
//                continue;
//            }
//            //如果是已挂起 并且不是挂起人 也不是待处理人 不显示
//            if($opinion->status == 'closed' && $opinion->closedBy != $this->app->user->account && $opinion->dealUser != $this->app->user->account && $opinion->createdBy != $this->app->user->account)
//            {
//                continue;
//            }
//            if($opinion->status != 'closed' && $opinion->dealUser != '' && $opinion->dealUser != $this->app->user->account){
//                continue;
//            }
            /*迭代二十六修改*/
            //挂起状态 去除登录人非挂起人、创建人、到处理人、需求负责人之外的数据
            if($opinion->status == 'closed' && $opinion->closedBy != $this->app->user->account && strstr($opinion->dealUser,  $this->app->user->account) == false && $opinion->createdBy != $this->app->user->account && strstr($opinion->assignedTo,  $this->app->user->account) == false)
            {
                continue;
            }
            //待处理人和需求负责人并集取值
            if($opinion->status != 'closed' && strstr($opinion->dealUser,  $this->app->user->account) == false && strstr($opinion->assignedTo,  $this->app->user->account) == false)
            {
                continue;
            }
            if($opinion->status == 'closed')
            {
                $opinion->name = $opinion->name ."[已挂起]";
            }
            $list[$opinion->id] = $opinion->name ;
        }
        return $list;
    }


    /**
     * Desc: 未删除 && 状态不为（已录入，审核未通过，待更新）的需求意向
     * Date: 2022/8/4
     * Time: 16:37
     *
     */
    public function getOpinionList()
    {
        $statusList = $this->lang->opinioninside->statusList;
        unset($statusList['deleted']); //未删除
        unset($statusList['created']); //已录入
        unset($statusList['reject']);//审核未通过
        unset($statusList['waitupdate']);//待更新
        $opinionsList = $this->dao->select('id,name')->from(TABLE_OPINION)
            ->where('status')->in(array_keys($statusList))
            ->andWhere('sourceOpinion')->eq(2)
            ->orderBy('id_desc')
            ->fetchPairs();
        return $opinionsList;
    }

    /**
     * 后台配置多选字段展示解析，
     * lizhongzheng
     * * @param $configList
     * @param $fieldCode
     * @param $sep
     * @return $res
     */
    public function ecodeSelfConfigFileds($configList, $fieldCode, $sep)
    {
        $res = '';
        $unionList = explode(',', str_replace(' ', '', $fieldCode));
        foreach ($unionList as $union) {
            if ($union) $res .= zget($configList, $union, '') . $sep;
        }
        return substr_replace($res, '', -1);

    }

    /**
     * Method: change
     * Desc: This is the code comment. This method is called change.
     * @param $opinionID
     * @return array
     */
    public function change($opinionID)
    {
        if (empty($_POST['comment'])) {
            dao::$errors['comment'] = sprintf($this->lang->opinioninside->error->empty,$this->lang->opinioninside->comment);
            return false;
        }
        /* 获取旧的需求条目信息，获取post接收的参数信息和需求条目版本信息，将需求条目信息记录到需求版本记录。*/
        $oldOpinion = $this->getByID($opinionID);
        //更新下一节点处理人
        $data = new stdClass();
        $data->dealUser   = $oldOpinion->createdBy;
        $data->status   = 'waitupdate';
        if($oldOpinion->createdBy != $oldOpinion->dealUser){
            $data->ignore = '';
        }
        // 记录变更前已拆分的状态
        if(in_array('subdivided',array_column($oldOpinion->consumed,'after')))
        {
            $data->beforeChangedStatus = $oldOpinion->status;
        }
        $this->dao->update(TABLE_OPINION)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->opinioninside->changed->requiredFields, 'notempty')
            ->where('id')->eq($opinionID)
            ->exec();
        $this->loadModel('consumed')->record('opinion', $opinionID, 0, $this->app->user->account, $oldOpinion->status, 'waitupdate');
        return common::createChanges($oldOpinion, $data);

    }

    /**
     * Desc: 恢复
     *
     * @param $opinionID
     * @return mixed
     *
     */
    public function recoveryed($opinionID)
    {
        $oldOpinion = $this->dao->select('`ignore`')
            ->from(TABLE_OPINION)
            ->where('id')->eq($opinionID)
            ->fetch();
        $ingoreList = explode(',',$oldOpinion->ignore);
        foreach ($ingoreList as $index=>$ignore)
        {
            if($ignore == $this->app->user->account)
            {
                unset($ingoreList[$index]);
            }
        }
        $data = new stdClass();
        $data->ignore   = implode(',',$ingoreList);
        $data->recoveredBy   = $this->app->user->account;
        $data->recoveredDate = date('Y-m-d H:i:s');

        $this->dao->update(TABLE_OPINION)->data($data)
            ->autoCheck()
            ->where('id')->eq($opinionID)
            ->exec();
        return common::createChanges($oldOpinion, $data);
    }

    /**
     * Desc: 忽略
     *
     * @param $opinionID
     * @return mixed
     *
     */
    public function ignore($opinionID)
    {
        $oldOpinion = $this->dao->select('`ignore`')
            ->from(TABLE_OPINION)
            ->where('id')->eq($opinionID)
            ->fetch();;
        $data = new stdClass();
        $data->ignore   = $oldOpinion->ignore . ',' . $this->app->user->account;
        $data->suspendBy = $this->app->user->account;
        $data->suspendDate = date('Y-m-d H:i:s');

        $this->dao->update(TABLE_OPINION)->data($data)
            ->autoCheck()
            ->batchCheck($this->config->opinioninside->ignore->requiredFields, 'notempty')
            ->where('id')->eq($opinionID)
            ->exec();
        return common::createChanges($oldOpinion, $data);
    }

    /**
     * @Notes: 需求任务联动需求意向状态
     * @Date: 2023/4/11
     * @Time: 10:38
     * @Interface changeOpinionStatus
     * @param int $opinionID
     * @param array $requirementInfo
     * @param array $opinionInfo
     * @return array
     */
    public function changeOpinionStatus($requirementInfo = [],$opinionInfo = []): array
    {
        $paramsArray = [];
        $statusList = array_unique(array_column($requirementInfo,'status'));
        $code = '';
        if(count($statusList) == 0){ //如果没有需求任务则需求意向为<已录入>
            $paramsArray = $this->codeAndOtherParams($code,'created');
        }else if(count($statusList) == 1){
            $code = $requirementInfo[0]->code;
            $requirementStatus = $statusList[0];
            if($requirementStatus == 'delivered'){//1.需求任务全部状态为<已交付>时，需求意向联动为<已交付>。
                $paramsArray = $this->codeAndOtherParams($code,'delivery');
            }
            if($requirementStatus == 'onlined'){ //2.需求任务全部状态为<上线成功>时，需求意向联动为<上线成功>。
                //需要获取上线时间做大的任务的id对应的code
                $onlineTimeByDemand = max(array_column($requirementInfo,'onlineTimeByDemand'));
                foreach ($requirementInfo as $item){
                    if($item->onlineTimeByDemand == $onlineTimeByDemand){
                        $code = $item->code;
                    }
                }
                $paramsArray = $this->codeAndOtherParams($code,'online',$onlineTimeByDemand);
            }
            if($opinionInfo->status != 'closed' && $requirementStatus == 'closed'){//3.若全部需求任务为已挂起，则联动需求意向时判断该意向是否为已挂起，意向也为已挂起则不更新意向状态，意向不为已挂起，则联动意向状态为已录入。
                $paramsArray = $this->codeAndOtherParams($code,'created');
            }
            if(in_array($requirementStatus,['topublish','published','splited'])){//4.需求任务全部状态为<待发布、已发布、已拆分>时，需求意向联动为<已拆分>。
                $paramsArray = $this->codeAndOtherParams($code,'subdivided');
            }
        }else{
            //已挂起的不进行修改
            if(in_array('topublish',$statusList) or in_array('published',$statusList) or in_array('splited',$statusList)){//1、待发布、已发布、已拆分 需求意向为<已拆分>
                foreach ($requirementInfo as $item){
                    if($item->status == $statusList[0]){
                        $code = $item->code;
                    }
                }
                $paramsArray = $this->codeAndOtherParams($code,'subdivided');
            }else if(in_array('delivered',$statusList)){ //2.除上状态只要存在已交付，需求意向为<已交付>
                foreach ($requirementInfo as $item){
                    if($item->status == 'delivered'){
                        $code = $item->code;
                    }
                }
                $paramsArray = $this->codeAndOtherParams($code,'delivery');
            }else if(in_array('onlined',$statusList)){//3.除上状态只要存在上线成功，需求意向为<上线成功>
                $onlineTimeByDemand = max(array_column($requirementInfo,'onlineTimeByDemand'));
                foreach ($requirementInfo as $item){
                    if($item->status == 'onlined' && $item->onlineTimeByDemand == $onlineTimeByDemand){
                        $code = $item->code;
                    }
                }
                $paramsArray = $this->codeAndOtherParams($code,'online',$onlineTimeByDemand);
            }
        }
        return $paramsArray;
    }

    /**
     * @Notes: 获取最终code,status等数据
     * @Date: 2023/4/17
     * @Time: 14:37
     * @Interface codeAndOtherParams
     * @param string $code
     * @param string $status
     * @param string $onlineTimeByDemand
     * @return array
     */
    public function codeAndOtherParams($code='',$status = '',$onlineTimeByDemand = '')
    {
        $returnArray['code'] = $code;
        $returnArray['opinionStatus'] = $status;
        $returnArray['onlineTimeByDemand'] = $onlineTimeByDemand;
        return $returnArray;
    }


    /**
     * @Notes:更新需求意向
     * @Date: 2023/4/11
     * @Time: 15:38
     * @Interface updateOpinion
     * @param $opinionId
     * @param $opinionStatus
     * @param $onlineTimeByDemand
     */
    public function updateOpinion($opinionId,$opinionStatus,$onlineTimeByDemand = '')
    {
        if($opinionStatus != 'online'){
            $this->dao->update(TABLE_OPINION)->set('status')->eq($opinionStatus)->set('onlineTimeByDemand')->eq('')->where('id')->eq($opinionId)->exec();
        }else{
            $this->dao->update(TABLE_OPINION)->set('status')->eq($opinionStatus)->set('onlineTimeByDemand')->eq($onlineTimeByDemand)->where('id')->eq($opinionId)->exec();
        }
    }

    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $opinion  = $obj;

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();


        $sendUsers = $this->getToAndCcList($opinion);

        if($action->action == 'editassignedto') $sendUsers = array($opinion->assignedTo,'');
        if (!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        $url = '';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '['.$opinion->code.']';
        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }
}