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
        ->stripTags($this->config->demand->editor->deal['id'], $this->config->allowedTags)
        ->remove('uid,mailto,user')
        ->get();
    //20221011 当前进展追加
    if($data->progress){
        $users = $this->loadModel('user')->getPairs('noclosed');
                $progress = '<span style="background-color: #ffe9c6">' .helper::now()." 由<strong>".zget($users,$this->app->user->account,'')."</strong>新增".'<br></span>'.$data->progress;
        $data->progress = $oldDemand->progress .'<br>'.$progress;
    }
    $application = $oldDemand->application;

    $oldDemand->dealUser = $data->dealUser;
    $this->dao->update(TABLE_DEMAND)->data($data)->autoCheck()
        ->batchCheck($this->config->demand->deal->requiredFields, 'notempty')
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
                         $this->loadModel('demand')->bindProduct($data->project,$product,'demand',$demandID);
                      }
                  }
              }

           $task =  $this->loadModel('problem')->toTaskProblemDemand($data,$demandID,'demand');//新增关联
           if($task){
               /** @var taskModel $taskModel */
               $taskModel = $this->loadModel('task');
//               $data->mailto = $this->post->mailto;
               $app = explode(',',$data->app);
               if($application){
                   //如果多个系统，且有系统有阶段，则在此系统创建
                   $app = $application;
               }else{
                   //如果多个系统，所有系统都没有阶段，则在第一个系统创建
                   $app = $app[0];
               }
               $this->dao->update(TABLE_TASK_DEMAND_PROBLEM)->set('application')->eq($app)->where('id')->eq($task)->exec();
               $data->projectPlan = $data->project;
              // $this->loadModel('task')->checkStageAndTask($data->project, $app,'project',$data,0);//创建任务
               //迭代三十 创建人取成方金科、指派给取需求条目的研发责任人 无研发责任人取创建人
               $data->dealUser = !empty($data->acceptUser) ? $data->acceptUser : $data->createdBy;
               $taskModel->assignedAutoCreateStageTask($data->project,'demand',$app,$data->code,$data);
           }
       }
   }
    //迭代十四 需求条目分析后，向需求任务回填字段
    if($this->post->status == 'feedbacked' || $this->post->status == 'solved'){
        $requirementID = $oldDemand->requirementID;
        /**
         * @var requirementModel $requirementModel
         * @var demandModel $demandModel
         */
        $requirementModel = $this->loadModel('requirement');
        $demandModel = $this->loadModel('demand');
        $requirement = $requirementModel->getByRequirementID($requirementID);
        if(!empty($requirementID))
        {
            $demandInfo = $demandModel->getByRequirementID('*',$requirementID);
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

    return common::createChanges($oldDemand, $data);
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

/**
 * 发送超时提醒邮件
 * @return mixed
 */
public function sendmailByOutTime()
{
    return $this->loadExtension('chengfangjinke')->outTimeNew();
}


/**
 * 交付管理创建页面需求条目下拉框(需求条目互斥的公用方法)
 *
 * @param string $type
 * @param int $id
 * @param int $isNewModifycncc
 * @param string $exWhere
 * @param array $demandIds
 * @return mixed
 */
public function modifySelect($type = '', $id = 0,  $isNewModifycncc = 1, $exWhere = '', $demandIds = [])
{
    return $this->loadExtension('chengfangjinke')->modifySelect($type, $id, $isNewModifycncc, $exWhere, $demandIds);
}

/**
 * 交付管理修改页面需求条目下拉框
 * @param $demandId
 * @param $type
 * @param $objectId
 * @param $isNewModifycncc
 * @return array
 */
public function modifySelectByEdit($demandId, $type, $objectId, $isNewModifycncc = 1,$source='')
{
    return $this->loadExtension('chengfangjinke')->modifySelectByEdit($demandId, $type, $objectId, $isNewModifycncc,$source);
}

/**
 * 需求条目是否关联一次
 * @param $demandId
 * @return array
 */
public function isSingleUsage($demandId, $type = '', $objectId = '', $isNewModifycncc = 1)
{
    return $this->loadExtension('chengfangjinke')->isSingleUsage($demandId, $type, $objectId, $isNewModifycncc);
}

/**
 * @Notes:需求条目二线月报统计
 * @Date: 2023/10/9
 * @Time: 15:40
 * @Interface monthreport
 * @param $yearParam
 * @param $monthParam
 */
public function monthReport($endtime='',$time='',$starttime='',$dtype=1)
{
    return $this->loadExtension('chengfangjinke')->monthReport($endtime,$time,$starttime,$dtype);
}


public function getIsExceed($demand,$publishedTime)
{
    return $this->loadExtension('chengfangjinke')->getIsExceed($demand,$publishedTime);
}

/**
 * @Notes:二线需求条目交付是否超期状态修改
 * @Date: 2024/4/11
 * @Time: 15:11
 * @Interface updateDemandDeliveryOver
 * @return mixed
 */
public function updateDemandDeliveryOver()
{
    return $this->loadExtension('chengfangjinke')->updateDemandDeliveryOver();
}

/**
 * 获得允许被投产使用的需求列表
 *
 * @param int $ignorePutProductionId
 * @param $demandIds
 * @return mixed
 */
public function getAllowPutProductionDemandList($ignorePutProductionId = 0, $demandIds = []){
    return $this->loadExtension('chengfangjinke')->getAllowPutProductionDemandList($ignorePutProductionId, $demandIds);

}

