<?php
class deptorderModel extends model
{
    /**
     * Method: getList
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $deptorderQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('deptorderQuery', $query->sql);
                $this->session->set('deptorderForm', $query->form);
            }

            if($this->session->deptorderQuery == false) $this->session->set('deptorderQuery', ' 1 = 1');
            $deptorderQuery = $this->session->deptorderQuery;


        }
        $deptorderQuery = str_replace("`ifAccept` = ''","`ifAccept` = '0'", $deptorderQuery);
        $deptorders = $this->dao->select('*')->from(TABLE_DEPTORDER)
            ->where('deleted')->ne('1')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch'  and $browseType != 'tomedeal')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'tomedeal')->andWhere('dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($deptorderQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'deptorder', $browseType != 'bysearch');
        return $deptorders;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->deptorder->search['actionURL'] = $actionURL;
        $this->config->deptorder->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->deptorder->search);
    }

    /**
     * Method: create
     * @return mixed
     */
    public function create()
    {
        $data = fixer::input('post')
            ->remove('files,comment')
            ->join('team',',')
            ->join('ccList',',')
            ->stripTags($this->config->deptorder->editor->create['id'], $this->config->allowedTags)->get();

        if(empty($data->dealUser))
        {
            return dao::$errors['dealUser'] = $this->lang->deptorder->nextUserEmpty;
        }
        $data->createdBy    = $this->app->user->account;
        $data->createdDate  = date('Y-m-d H:i:s');
        $data->acceptUser   = $data->dealUser;
        $data->acceptDept   = $this->getDeptByUser($data->dealUser);
        $data->createdDept   = $this->app->user->dept;
        $data->status       = 'assigned';
        $this->dao->insert(TABLE_DEPTORDER)->data($data)->autoCheck()->batchCheck($this->config->deptorder->create->requiredFields, 'notempty')->exec();
        $deptorderID = $this->dao->lastInsertId();
        if(!dao::isError())
        {
            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_DEPTORDER)->where('createdDate')->gt($date)->fetch('c');
            $code   = 'CFIT-DT-' . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_DEPTORDER)->set('code')->eq($code)->where('id')->eq($deptorderID)->exec();
            $this->loadModel('consumed')->record('deptorder', $deptorderID, 0, $this->app->user->account, '', 'assigned', array());

            $this->loadModel('file')->updateObjectID($this->post->uid, $deptorderID, 'deptorder');
            $this->file->saveUpload('deptorder', $deptorderID);
        }

        return $deptorderID;
    }

    /**
     * Method: update
     * Product: PhpStorm
     * @param $deptorderID
     * @return array
     */
    public function update($deptorderID)
    {
        $olddeptorder = $this->getByID($deptorderID);
        $deptorder = fixer::input('post')
            ->remove('uid,files,comment')
            ->join('team',',')
            ->join('ccList',',')
            ->striptags($this->config->deptorder->editor->edit['id'], $this->config->allowedTags)
            ->get();

        if(empty($deptorder->dealUser))
        {
            return dao::$errors['dealUser'] = $this->lang->deptorder->nextUserEmpty;
        }
        $deptorder->editedBy    = $this->app->user->account;
        $deptorder->editedDate  = date('Y-m-d H:i:s');
        $deptorder->status       = 'assigned';
        $deptorder->acceptUser   = $deptorder->dealUser;
        $deptorder->acceptDept   = $this->getDeptByUser($deptorder->dealUser);

        $deptorder = $this->loadModel('file')->processImgURL($deptorder, $this->config->deptorder->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_DEPTORDER)->data($deptorder)->autoCheck()
            ->batchCheck($this->config->deptorder->edit->requiredFields, 'notempty')
            ->where('id')->eq($deptorderID)
            ->exec();
        if($this->getLastAction($deptorderID) != 'edited' && !dao::isError()) {
            $this->loadModel('consumed')->record('deptorder', $deptorderID, 0, $this->app->user->account, $olddeptorder->status, 'assigned', array());
        }

        $this->loadModel('file')->updateObjectID($this->post->uid, $deptorderID, 'deptorder');
        $this->file->saveUpload('deptorder', $deptorderID);

        return common::createChanges($olddeptorder, $deptorder);
    }

    /**
     * Project: chengfangjinke
     * Method: editAssignedTo
     * @param $deptorderID
     * @return array
     */
    public function editAssignedTo($deptorderID)
    {

        $olddeptorder = $this->getByID($deptorderID);
        $deptorder    = array();

        if(empty($_POST['acceptUser']))
        {
            return dao::$errors['acceptUser'] = $this->lang->deptorder->acceptUserEmpty;
        }
        else
        {
            $acceptDept =  $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($_POST['acceptUser'])->fetch();
            $this->dao->update(TABLE_DEPTORDER)
                 ->set('acceptUser')->eq($_POST['acceptUser'])
                 ->set('acceptDept')->eq($acceptDept->dept)
                 ->where('id')->eq($deptorderID)
                 ->exec();
        }

        return common::createChanges($olddeptorder, $deptorder);
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedByID
     * @param $consumedID
     * @return mixed
     */
    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }

    /**
     * Project: chengfangjinke
     * Method: getConsumedList
     * @param $deptorderID
     * @return mixed
     */
    public function getConsumedList($deptorderID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('deptorder')
            ->andWhere('objectID')->eq($deptorderID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * @param $deptorderID
     * @return mixed
     */
    public function getByID($deptorderID)
    {
        $deptorder = $this->dao->select("*")->from(TABLE_DEPTORDER)->where('id')->eq($deptorderID)->fetch();
        $deptorder = $this->loadModel('file')->replaceImgURL($deptorder, 'desc,progress,dealRes,consultRes,testRes');
        $deptorder->files = $this->loadModel('file')->getByObject('deptorder', $deptorder->id);
        return $deptorder;
    }

    public function getByIdList($deptorderIdList, $isPairs = false)
    {
        if(empty($deptorderIdList)) return array();

        $deptorders = $this->dao->select("*")->from(TABLE_DEPTORDER)->where('id')->in($deptorderIdList)->fetchAll();
        if($isPairs)
        {
            $pairs = array();
            foreach($deptorders as $deptorder)
            {
                $pairs[$deptorder->id] = $deptorder->code;
            }
            $deptorders = $pairs;
        }
        return $deptorders;
    }

    /* 获取制版次数。*/
    public function getBuild($deptorderID)
    {
        $buildTotal = $this->dao->select('count(*) as total')->from(TABLE_CONSUMED)
            ->where('objectType')->eq('deptorder')
            ->andWhere('objectID')->eq($deptorderID)
            ->andWhere('after')->eq('build')
            ->fetch('total');
        return empty($buildTotal) ? 0 : $buildTotal;
    }

    /**
     * Project: chengfangjinke
     * Method: deal
     * @param $deptorderID
     * @return array
     */
    public function deal($deptorderID)
    {

        $olddeptorder = $this->getByID($deptorderID);

        if($this->post->completeStatus == '' && $this->post->ifAccept != '0')
        {
            return dao::$errors['completeStatus'] = sprintf($this->lang->deptorder->emptyObject, $this->lang->deptorder->completeStatus);
        }
        $data = fixer::input('post')->stripTags($this->config->deptorder->editor->deal['id'], $this->config->allowedTags)
            ->remove('uid,files,comment,relevantUser')
            ->get();

        if($this->post->ifAccept == '0') {
            $data->status = 'backed';
            $data->dealUser = $olddeptorder->createdBy;
            $data->app = $olddeptorder->app;
            $data->planstartDate = $olddeptorder->planstartDate;
            $data->planoverDate = $olddeptorder->planoverDate;
            $data->startDate = $olddeptorder->startDate;
            $data->overDate = $olddeptorder->overDate;
            $data->consultRes = $olddeptorder->consultRes;
            $this->config->deptorder->deal->requiredFields = 'progress';
        }else {
            if($olddeptorder->status == 'assigned' or $olddeptorder->status == 'tosolve') {
                $projectId = $this->getProjectPlanInfo($olddeptorder->acceptDept);
                if(empty($projectId))
                {
                    $changes = new stdClass();
                    $changes->noPrj = $this->lang->deptorder->noSecondProject;
                    return $changes;
                }
            }
            if($this->post->completeStatus == '1'){
                $data->status = 'solved';
                $data->dealUser = '';
                $this->config->deptorder->deal->requiredFields = 'app,progress,startDate,overDate,planstartDate,planoverDate';
            }else{
                $data->status = 'tosolve';
                unset($data->startDate);
                unset($data->overDate);
                if(!$this->post->dealUser)
                {
                    return dao::$errors['dealUser'] = $this->lang->deptorder->nextUserEmpty;
                }
            }
        }
        if($data->ifAccept == '1' and $this->post->planoverDate and strtotime($this->post->planstartDate) > strtotime($this->post->planoverDate))
        {
            return dao::$errors['planoverDate'] = sprintf($this->lang->error->gt,$this->lang->deptorder->planoverDate,$this->lang->deptorder->planstartDate);
        }
        if($data->ifAccept == '1' and $this->post->overDate and strtotime($this->post->startDate) > strtotime($this->post->overDate))
        {
            return dao::$errors['overDate'] = sprintf($this->lang->error->gt,$this->lang->deptorder->overDate,$this->lang->deptorder->startDate);
        }
        //20221011 当前进展追加
        if($data->progress){
            $users = $this->loadModel('user')->getPairs('noclosed');
            $progress = '<span style="background-color: #ffe9c6">' . helper::now() . ' 由<strong>' . zget($users,$this->app->user->account,'') . '</strong>新增' . '</span><br>' . $data->progress;
            $data->progress = $olddeptorder->progress .'<br>'.$progress;
        }
        if($data->completeStatus == '1' && $data->ifAccept == '1'){
            if(in_array($olddeptorder->type, array('consult', 'test'))) $this->config->deptorder->deal->requiredFields .= ',' . $olddeptorder->type . 'Res';
            else $this->config->deptorder->deal->requiredFields .= ',dealRes';
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->deptorder->editor->deal['id'], $this->post->uid);
        $this->dao->update(TABLE_DEPTORDER)->data($data)->autoCheck()
             ->batchCheck($this->config->deptorder->deal->requiredFields, 'notempty')
             ->where('id')->eq($deptorderID)
             ->exec();
        $this->loadModel('consumed')->record('deptorder', $deptorderID, 0, $this->app->user->account, $olddeptorder->status, $data->status);

        $this->loadModel('file')->updateObjectID($this->post->uid, $deptorderID, 'deptorder');
        $this->file->saveUpload('deptorder', $deptorderID);
        $commentBak = $this->post->comment;
        if(($this->post->ifAccept == '1' and ($olddeptorder->status == 'assigned' or $olddeptorder->status == 'tosolve')) or ($olddeptorder->ifAccept == '1' and $this->post->ifAccept == '0'))
        {
            $data->type = $olddeptorder->type;
            $data->code = $olddeptorder->code;
            $data->project = $projectId;
            $data->dealUser = $data->dealUser == '' ? $this->app->user->account : $data->dealUser;
            $data->product = 0;
            $data->execution = '';
            $data->productPlan = 0;
            $data->lastDealDate = helper::now();
            $task =  $this->loadModel('problem')->toTaskProblemDemand($data,$deptorderID,'deptorder');//新增关联
            if($task and $this->post->ifAccept == '1'){
                $data->id = $deptorderID;
               // $this->loadModel('task')->checkStageAndTask($projectId, $data->app,'deptorder',$data,0);//创建任务
                $this->loadModel('task')->assignedAutoCreateStageTask($projectId,'deptorder',$data->app,$data->code,$data);
            }/*elseif($task and $this->post->ifAccept == '0'){
                $data->dealUser = $olddeptorder->dealUser; // 废弃任务待处理人不更新
                $this->loadModel('task')->checkStageAndTask($projectId, $data->app,'deptorder',$data, 0,true);//废弃任务
            }*/
        }
        $newdeptorder = $this->getByID($deptorderID);
        $_POST['comment'] = $commentBak;
        return common::createChanges($olddeptorder, $newdeptorder);
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * @param $deptorderID
     */
    public function close($deptorderID)
    {
        $olddeptorder = $this->getByID($deptorderID);
        $data = new stdClass();
        $data->status   = 'closed';
        $data->dealUser = '';
        $data->closeReason = $this->post->closeReason;
        $data->closedBy    = $this->app->user->account;
        $data->closedDate  = date('Y-m-d  H:i:s');
        if($this->post->comment == '')
        {
            return dao::$errors['comment'] = sprintf($this->lang->deptorder->emptyObject, $this->lang->deptorder->comment);
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->deptorder->editor->close['id'], $this->post->uid);
        $this->dao->update(TABLE_DEPTORDER)->data($data)->autoCheck()
            ->batchCheck($this->config->deptorder->close->requiredFields, 'notempty')
            ->where('id')->eq($deptorderID)->exec();

        if(!dao::isError())
        {
            $this->loadModel('consumed')->record('deptorder', $deptorderID, 0, $this->app->user->account, $olddeptorder->status, 'closed', array());
        }
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * Product: PhpStorm
     * @param $deptorder
     * @param $action
     * @return bool
     */
    public static function isClickable($deptorder, $action)
    {
        global $app;
        $action = strtolower($action);

        //单子删除后，所有按钮不可见
        if($deptorder->deleted){
           return false;
        }
        if($action == 'edit')            return ($deptorder->status == 'assigned' or $deptorder->status == 'backed') and $app->user->account == $deptorder->createdBy;
        if($action == 'deal')            return ($deptorder->status != 'closed' and $deptorder->status != 'backed') and ($app->user->account == $deptorder->dealUser or  $app->user->account == 'admin');
//        if($action == 'delete')          return $deptorder->status != 'closed';
        if($action == 'close')           return $deptorder->status != 'closed';
//        if($action == 'editassignedto')  return $deptorder->status != 'closed';
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getPairs($code = '')
    {
        return $this->dao->select('id,code')->from(TABLE_DEPTORDER)
            ->where('deleted')->ne('1')
            ->andwhere('code')->in($code)
            ->orderBy('id_desc')
            ->fetchAll();
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getNamePairs()
    {
        return $this->dao->select("id,concat(code,'（',IFNULL(summary,''),'）') as code")->from(TABLE_DEPTORDER)
            ->where('deleted')->ne('1')
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: getPairs
     * @return mixed
     */
    public function getNamePairsAll()
    {
        return $this->dao->select("id,concat(code,'（',IFNULL(summary,''),'）') as code")->from(TABLE_DEPTORDER)
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * 根据多个id获取信息
     * @param array $ids
     * @return stdClass
     */
    public function getPairsByIds($ids)
    {
        if(empty($ids)) return null;
        $info = $this->dao->select('id,code,`summary`')->from(TABLE_DEPTORDER)
            ->where('status')->ne('deleted')
            ->andwhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchall();
        $deptorders = new stdClass();
        foreach ($info as $item)
        {
            $id = $item->id;
            $deptorders->$id = ['code'=>$item->code, 'desc' =>$item->summary];
        }
        return  $deptorders;
    }

    /**
     * Send mail.
     *
     * @param  int    $deptorderID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($deptorderID, $actionID)
    {
        $this->loadModel('mail');
        $deptorder = $this->getById($deptorderID);
        $users   = $this->loadModel('user')->getPairs('noletter');



        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setDeptorderMail) ? $this->config->global->setDeptorderMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);
        $browseType = 'deptorder';

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('deptorder')
            ->andWhere('objectID')->eq($deptorderID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'deptorder');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();
        chdir($oldcwd);

        $toList = $deptorder->dealUser;
        $ccList = '';
        if(in_array($action->action, array('created','edited')))
        {
            $ccList = $deptorder->ccList ?? '';
        }

        // 完成状态处理
        $tome = false;
        if($deptorder->ifAccept == '1' and $deptorder->status == 'solved')
        {
            $mailTitle = sprintf($this->lang->deptorder->ccMailTitle, $deptorder->code);
            list($toList, $ccList) = $this->getToAndCcList($deptorder);
            $tome = true;
        }

        /* 处理邮件标题。*/
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList, $tome);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * Product: PhpStorm
     * @param $object
     * @return array
     */
    public function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $toList = $this->dao->select('actor')->from(TABLE_ACTION)->where('objectType')->eq('deptorder')->andWhere('objectID')->eq($object->id)->andWhere('action')->eq('deal')->fetchAll();
        $toArr = Array();
        Array_push($toArr, $this->app->user->account);
        foreach ($toList as $to)
        {
            if(!in_array($to->actor, $toArr))
            {
                Array_push($toArr, $to->actor);
            }
        }

        $details = $this->loadModel('dept')->getByID($object->acceptDept);
        $ccList  = trim($details->manager, ',');

        return array(implode(',',$toArr), $ccList);
    }

    /**
     * 根据拼音首字母取系统id
     * @param $code
     * @return mixed
     */
    public function getAppIdByAppCode($codes)
    {
        $apps = $this->dao->select('id')->from(TABLE_APPLICATION)->where('code')->in($codes)->fetchAll('id');
        if(empty($apps)) return '';
        return implode(',', array_keys($apps));
    }

    function checkTimeFormat($date)
    {
        if(date('Y-m-d H:i:s', strtotime($date)) == $date ){
            return true;
        }
        if(date('Y-m-d H:i', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    function checkDateFormat($date)
    {
        if(date('Y-m-d', strtotime($date)) == $date ){
            return true;
        }
        return false;
    }

    // 获取分类下的子类数据。
    public function getChildTypeList($assignType = '')
    {
        // 自定义的deptorder子类数据。
        $typeList      = $this->lang->deptorder->typeList;
        $childTypeList = isset($this->lang->deptorder->childTypeList) ? $this->lang->deptorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        $customList    = empty($childTypeList[$assignType]) ? array('0' => '') : $childTypeList[$assignType];
        if(!empty($customList)) $customList = array('0' => '') + $customList;
        return $customList;
    }

    // 获取分类下的子类数据键值对。
    public function getChildTypeTileList($firstNull = false)
    {
        // 自定义的deptorder子类数据。
        $typeList      = $this->lang->deptorder->typeList;
        $childTypeList = isset($this->lang->deptorder->childTypeList) ? $this->lang->deptorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $customList = array();
        if($firstNull) $customList[0] = '';
        foreach($childTypeList as $type => $items)
        {
            // deptorder分类不存在的话，子类也不展示了。
            if(empty($typeList[$type])) continue;

            foreach($items as $key => $value)
            {
                $customList[$key] = $value;
            }
        }

        return $customList;
    }

    // 获取分类下的子类数据(父分类)。
    public function getChildTypeParentList()
    {
        // 自定义的deptorder子类数据。
        $typeList      = $this->lang->deptorder->typeList;
        $childTypeList = isset($this->lang->deptorder->childTypeList) ? $this->lang->deptorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        return $childTypeList;
    }

    public function getChildTypeParentNameList()
    {
        $childTypeList = $this->getChildTypeTileList(true);
        $allTypeList   = $this->getChildTypeParentList();
        foreach($childTypeList as $index => $name)
        {
            if(empty($index))
            {
                $childTypeList[$index] = $name;
                continue;
            }

            foreach($allTypeList as $typeKey => $typeData)
            {
                $typeName = $this->lang->deptorder->typeList[$typeKey];
                foreach($typeData as $childIndex => $childValue)
                {
                    if($index == $childIndex) $childTypeList[$index] = $typeName . '-' . $name;
                }
            }
        }
        return $childTypeList;
    }

    // 获取分类下的子类数据(父分类)。
    public function getAllChildTypeList()
    {
        // 自定义的deptorder子类数据。
        $typeList      = $this->lang->deptorder->typeList;
        $childTypeList = isset($this->lang->deptorder->childTypeList) ? $this->lang->deptorder->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);

        $data = array();
        foreach($typeList as $key => $type)
        {
            if(empty($key)) continue;
            if(empty($childTypeList[$key]))
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = array();
            }
            else
            {
                $data[$key]['name']  = $type;
                $data[$key]['child'] = $childTypeList[$key];
            }
        }
        return $data;
    }

    /**
     * @param $account
     * 获取人员部门
     */
    public function getDeptByUser($account)
    {
        $dept = $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($account)->fetch('dept');

        return $dept;
    }

    /**
     * @param $id
     * 获得最后的action记录
     */
    public function getLastAction($id)
    {
        $lastAction = $this->dao->select('action')
                        ->from(TABLE_ACTION)
                        ->where('objectType')->eq('deptorder')
                        ->andWhere('objectID')->eq($id)
                        ->orderBy('action_desc')
                        ->fetch('action');
        return $lastAction;
    }

    /**
     * @param $objectID
     * @return mixed
     */
    public function getConsumedsByID($objectID)
    {
        $details = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq('deptorder')
            ->andWhere('deleted')->eq(0)
            ->fetchAll();

        return $details;
    }

    /**
     * @param $deptorderID
     * @param $consumedID
     * @return array
     * 编辑流程状态
     */
    public function statusedit($deptorderID, $consumedID)
    {

        $res = array();

        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealUser')->get();
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $deptorderID, 'deptorder');
        if($isLast){
            //最后一个节点时没有设置处理人
            $dealUser = $this->post->dealUser;
//            if(!$dealUser){
//                $errors['dealUser'] = sprintf($this->lang->deptorder->emptyObject, $this->lang->deptorder->dealUser);
//                return dao::$errors = $errors;
//            }
        }

        $this->dao->update(TABLE_CONSUMED)->data($consumed)->where('id')->eq($consumedID)->exec();

        $olddeptorder = $this->getByID($deptorderID);
        if(($olddeptorder->status != $consumed->after) || ($olddeptorder->dealUser != $dealUser)) {
            $this->dao->update(TABLE_DEPTORDER)->set('status')->eq($consumed->after)->set('dealUser')->eq($dealUser)->where('id')->eq($deptorderID)->exec();
            $data = new stdClass();
            $data->status   = $consumed->after;
            $data->dealUser = $dealUser;
            $res = common::createChanges($olddeptorder, $data);
        }

        return $res;
    }

    /**
     * @param $deptorderID
     * @return mixed
     * 获取历次当前进展
     */
    public function getProgress($deptorderID)
    {
        $progress =  $this->dao->select('actor,date,comment')->from(TABLE_ACTION)->where('objectType')->eq('deptorderProgress')->andWhere('objectID')->eq($deptorderID)->fetchAll();
        return $progress;
    }


    /**
     * 获取项目
     * @param $deptID
     * @return mixed
     */
    public function getProjectPlanInfo($deptID)
    {
        //天津不再区分具体部门，都统一归属到天津分公司，即只要是具体部门都获取上一级的部门
        $deptName = $this->loadModel('dept')->getByID($deptID);
        $deptID = isset($deptName->name) && strpos($deptName->name,'天津') !== false &&  !empty($deptName->parent) ? $deptName->parent: $deptID;
        $projectPlanId = $this->dao->select('project')->from(TABLE_PROJECTPLAN)->where('secondLine')->eq('1')->andWhere('year')->eq('2022')->andWhere('code')->like('%DEP')->andWhere('bearDept')->eq($deptID)->fetch('project');
        return $projectPlanId;
    }

    /**
     * 获取配合人员
     * @param $consumeds
     * @return mixed
     */
    public function getrelevantUsers($consumeds)
    {
        $relevantUsers = '';
        $relevantUser = '';
        $users   = $this->loadModel('user')->getPairs('noclosed|noletter');
        foreach ($consumeds as $consumed)
        {
            if(empty($consumed->details)) continue;
            $details = $this->loadModel('consumed')->getConsumedDetailsArray($consumed->details);
            foreach ($details as $key => $detail)
            {
                $relevantUser .= zget($users, $detail->account) . ',';
            }
            $relevantUsers .= substr($relevantUser, 0, -1) . ';';
            $relevantUser = '';
        }
        return trim($relevantUsers, ';');
    }

    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $toList = '';
        $toList = $obj->dealUser;
        $ccList = '';

        $mailconfig = '';
        if($obj->ifAccept == '1' and $obj->status == 'solved')
        {
            $mailTitle = sprintf($this->lang->deptorder->ccMailTitle, $obj->code);
            list($toList, $ccList) = $this->getToAndCcList($obj);
            $mailconfig = '{"mailTitle":"'.$mailTitle.'","variables":[],"mailContent":""}';

        }

        if(is_array($toList)){
            $toList =  implode(",",$toList);
        }
        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html#app=secondorder');
//        $url = '';
        $subcontent = [];
        $subcontent['headTitle']    = '';

        $subcontent['headSubTitle'] = '';


        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '';//消息体 编号后边位置 标题
        //标题
        $title = '';
        $actions = [];


        if($mailconfig){
            return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>$mailconfig];
        }else{
            return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
        }


    }
}
