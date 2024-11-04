<?php
/**
 *  重写方法，解决查看新建需求详情时，所属产品显示数据库默认值的问题，应显示空
 * @param $demandID
 * @param bool $showFile
 * @return mixed
 */
public function getByID($demandID, $showFile = false)
{
    $demand = $this->dao->select("*")->from(TABLE_DEMAND)->where('id')->eq($demandID)->fetch();
    if($demand->product == '8'){
        $demand->product = '0';
    }
    $demand = $this->loadModel('file')->replaceImgURL($demand, 'desc,reason,progress,solution,conclusion,plateMakAp,plateMakInfo,comment');
    $demand = $this->getConsumed($demand);
    if($showFile) $demand->files = $this->loadModel('file')->getByObject('demand', $demand->id);
    return $demand;
}

/**
 * 重写方法  新增逻辑 当前状态 待开发时 所属应用系统必填
 * @param $demandID
 * @return array
 */
public function deal($demandID)
{
    //状态是待分配页面时工作量默认为 0
    $stat = array('assigned','feedbacked','solved','closed','');
    if(in_array($this->post->status,$stat)){
        $this->post->consumed = 0;
    }

    $oldDemand = $this->getByID($demandID);
    $data = fixer::input('post')
        ->join('app', ',')
        ->join('coordinators', ',')
        ->stripTags($this->config->demandinside->editor->deal['id'], $this->config->allowedTags)
        ->remove('uid,mailto,user')
        ->get();

    //已录入到开发中的处理流程
    if($oldDemand->status == 'wait')
    {
        if (!$this->post->dealUser) {
            $errors['dealUser'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->nextUser);
            return dao::$errors = $errors;
        }


        if (!$this->post->progress) {
            $errors['progress'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->progress);
            return dao::$errors = $errors;
        }

        //待处理人发生变化，忽略自动恢复
        if($oldDemand->dealUser != $data->dealUser)
        {
            $data->ignoreStatus = 0;
        }

        //20221011 当前进展追加
        if($data->progress){
            $users = $this->loadModel('user')->getPairs('noclosed');
            $progress = '<span style="background-color: #ffe9c6">' .helper::now()." 由<strong>".zget($users,$this->app->user->account,'')."</strong>新增".'<br></span>'.$data->progress;
            $data->progress = $oldDemand->progress .'<br>'.$progress;
        }
        $application = $oldDemand->application;

        $oldDemand->dealUser = $data->dealUser;
        $this->dao->update(TABLE_DEMAND)->data($data)->autoCheck()
            ->batchCheck($this->config->demandinside->deal->requiredFields, 'notempty')
            ->where('id')->eq($demandID)
            ->exec();
        if(!dao::isError()){
            //待开发 项目管理中创建任务
            if($data->status == 'feedbacked' ){
                $this->loadModel('demand')->insertProjectPlan($oldDemand); //同步到年度计划
                $data = $oldDemand;
                //查看所属项目是否和产品关联
                $linkedProducts = $this->loadModel('product')->getProducts($data->project);
                $products = array_column($linkedProducts,'id');
                if($data->product != '99999'){
                    if(in_array($data->product,$products) === false){
                        $product = array_filter(explode(',',$data->product));
                        //$product = array_merge($products,$product);
                        //只处理新增的产品
                        if(!empty($product)){
                           $this->loadModel('demandinside')->bindProduct($data->project,$product,'demand',$demandID);
                        }
                    }
                }
    //           $data->code = $oldDemand->code;
                $task =  $this->loadModel('problem')->toTaskProblemDemand($data,$demandID,'demandinside');//新增关联
                if($task){
                    /** @var taskModel $taskModel */
                    $taskModel = $this->loadModel('task');
                    $data->mailto = $this->post->mailto;
                    $app = explode(',',$data->app);
                    if($application){
                        //如果多个系统，且有系统有阶段，则在此系统创建
                        $app = $application;
                    }else{
                        //如果多个系统，所有系统都没有阶段，则在第一个系统创建
                        $app = $app[0];
                    }
                    $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('application')->eq($app)->where('id')->eq($task)->exec();
                    //$this->loadModel('task')->checkStageAndTask($data->project, $app,'project',$data,0);//创建任务
                    $data->projectPlan = $data->project;
                    $taskModel->assignedAutoCreateStageTask($data->project,'demandinside',$app,$data->code,$data);
                }
            }
        }
        //迭代十四 需求条目分析后，向需求任务回填字段
        if($this->post->status == 'feedbacked' || $this->post->status == 'solved'){
            $requirementID = $oldDemand->requirementID;
            $requirement = $this->loadModel('requirementinside')->getByRequirementID($requirementID);
            if(!empty($requirementID))
            {
                $demandInfo = $this->loadModel('demandinside')->getByRequirementID($requirementID);
                if($demandInfo)
                {
                    $productArr    = [];
                    $appArr        = [];
                    $endDateArr    = [];
                    $acceptDeptArr = [];
                    $acceptUserArr = [];
                    $projectArr = array();
                    $updateRequirementInfo = new stdClass();
                    foreach ($demandInfo as $key=>$value)
                    {
                        $productArr[$key]    = $value->product;
                        $appArr[$key]        = $value->app;
                        $endDateArr[$key]    = $value->end;
                        $acceptDeptArr[$key] = $value->acceptDept;
                        $acceptUserArr[$key] = $value->acceptUser;
                        $projectArr[$key]    = $value->project;
                    }
                    $productStr = !empty($productArr) ? implode(',',array_unique(array_filter($productArr))) : '';
                    $appStr = !empty($appArr) ? implode(',',array_unique(explode(',',implode(',',array_filter($appArr))))): '';
                    $endDateStr = !empty($appArr) ? max(array_unique(array_filter($endDateArr))) : '';
                    $acceptDeptStr = !empty($appArr) ? implode(',',array_unique(array_filter($acceptDeptArr))) : '';
                    $acceptUserStr = !empty($appArr) ? implode(',',array_unique(array_filter($acceptUserArr))) : '';
                    $projectStr = !empty($projectArr) ? implode(',',array_unique(explode(',',implode(',',array_filter($projectArr))))): '';
                    //金科内部才回填，外部单号不回填
                    if($requirement->entriesCode == null){
                        if(!empty($productStr))     $updateRequirementInfo->product = $productStr;
                        if(!empty($endDateStr))     $updateRequirementInfo->end     = $endDateStr;
                        if(!empty($acceptDeptStr))  $updateRequirementInfo->dept    = $acceptDeptStr;
                        if(!empty($acceptUserStr))  $updateRequirementInfo->owner   = $acceptUserStr;
                        if(!empty($projectStr))  $updateRequirementInfo->project   = $projectStr;
                    }
                    if(!empty($appStr))         $updateRequirementInfo->app     = $appStr;
                    $this->dao->update(TABLE_REQUIREMENT)->data($updateRequirementInfo)->where('id')->eq($requirementID)->exec();
                }
            }
        }
        $this->loadModel('consumed')->record('demand', $demandID, $this->post->consumed, $this->app->user->account, $oldDemand->status, 'feedbacked', $this->post->mailto);

        $this->loadModel('file')->updateObjectID($this->post->uid, $demandID, 'demand');
        $this->file->saveUpload('demand', $demandID);
    }else if(in_array($oldDemand->status,array('feedbacked','released','onlinesuccess'))){//开发中的处理流程
        //校验必填项
        if(!$this->loadModel('common')->checkJkDateTime($this->post->actualOnlineDate))
        {
            $errors['actualOnlineDate'] = sprintf($this->lang->demandinside->emptyObject, $this->lang->demandinside->actualOnlineDate);
            return dao::$errors = $errors;
        }
        $updateInfo = new stdClass();
        $updateInfo->status           = 'onlinesuccess';
        //$updateInfo->solvedTime       = date('Y-m-d H:i:s',strtotime($data->solvedTime));
        $updateInfo->actualOnlineDate = date('Y-m-d H:i:s',strtotime($data->actualOnlineDate));
        $updateInfo->dealUser         = '';
        $this->dao->update(TABLE_DEMAND)->data($updateInfo)->where('id')->eq($demandID)->exec();
        $this->loadModel('demandcollection')->statusChange($demandID);
        $this->loadModel('consumed')->record('demand', $demandID, 0, $this->app->user->account, $oldDemand->status, 'onlinesuccess');
    }
    return common::createChanges($oldDemand, $updateInfo);
}

public function getApplication($projectID, $executionID = 0,$fixtype = null,$app = null)
{
    $this->loadModel('project');
    $defaults =  array('0' => '');
    if(!empty($projectID))
    {
        $executions = $this->project->getExecutionByAvailable($projectID);

        if(!empty($executions)) $defaults += $executions;
    }
    $where = '';
    $this->app->loadLang('task');
    $gd = $this->lang->task->stageList['sendgd'] ;
    if($fixtype == 'second'){
        if($app) {
            $appname = '';
            $class= '';
            $apps = explode(',', $app);
            $new = array();
            foreach ($apps as $app) {
                $appname = $this->dao->select('concat(code,"_",name) as name')->from(TABLE_APPLICATION)->where('id')->eq($app)->fetch('name');
                $defaults = array_filter($defaults);
                $where = "readonly = 'readonly'";
                if($defaults && $projectID){
                    foreach ($defaults as $key=>$default) {
                        //过滤二线工单
                        if(strstr($default,$gd) !== false){
                            continue;
                        }
                        $defa = trim(strrchr($default,'/'),'/');
                        if($defa == $appname){
                            $executionID = $key;
                            unset($defaults);
                            $new = array($key=>$default);
                            $defaults = $new;
                            $class = $app;
                            break;
                        }
                    }
                }
            }
        }
    }

}


public function getCollectionPairs()
{
    return $this->dao
        ->select("id, concat(id,'_',IFNULL(trim(title),'')) as title")
        ->from(TABLE_DEMANDCOLLECTION)
        ->where('deleted')->eq('0')
        ->orderBy('id_desc')
        ->fetchPairs();
}

/**
 * 投产/变更单联动需求条目状态
 * @param $productionId
 * @return true|void
 */
public function collectionStatus($productionId)
{
    $production = $this->dao
        ->select('`id`,`code`,`status`,`actualOnlineTime`,`correlationDemand`')
        ->from(TABLE_PRODUCTIONCHANGE)
        ->where('id')->eq($productionId)
        ->fetch();

    if('validateSuccess' != $production->status || empty($production->correlationDemand)){
        return true;
    }

    $demandIds = explode(',', trim($production->correlationDemand, ','));

    if(empty($demandIds)){
        return true;
    }

    $demands = $this->dao
        ->select('id,status,actualOnlineDate')
        ->from(TABLE_DEMAND)
        ->where('id')->in($demandIds)
        ->fetchAll('id');

    $this->dao
        ->update(TABLE_DEMAND)
        ->set('status')->eq('onlinesuccess')
        ->set('actualOnlineDate')->eq($production->actualOnlineTime)
        ->set('solvedTime')->eq($production->actualOnlineTime)
        ->set('dealUser')->eq('')
        ->where('id')->in($demandIds)
        ->andWhere('status')->notIn(['onlinesuccess'])
        ->exec();

    foreach ($demandIds as $demandId){
        $demand = $demands[$demandId];

        if(in_array($demand->status, ['onlinesuccess'])){
            continue;
        }

        $this->loadModel('consumed')->record('demand', $demandId, '', 'guestjk', $demand->status, 'onlinesuccess', '');

        $this->loadModel('action')->create('demand', $demandId, 'syncstatus', '投产/变更单：' . $production->code);

        $this->loadModel('demandcollection')->statusChange($demandId);

        return true;
    }

}
