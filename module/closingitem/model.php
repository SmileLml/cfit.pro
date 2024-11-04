<?php
class closingitemModel extends model
{
    const MAXNODE           = 2;   //审批节点最大值是2
    /*
     * Add.
     *
     * @access public
     * @return int|bool
     */
    public function create($projectID)
    {
        // 正整数校验
        if ($this->post->isAssembly == 1 && (floor($this->post->assemblyNum) != $this->post->assemblyNum || $this->post->assemblyNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->assemblyNumError;
            return false;
        }
//        if (!empty($_POST['realPoints']) && (floor($this->post->realPoints) != $this->post->realPoints || $this->post->realPoints < 0)) {
//            dao::$errors[] = $this->lang->closingitem->realPointsError;
//            return false;
//        }
        if (!empty($_POST['achievementNum']) && (floor($this->post->achievementNum) != $this->post->achievementNum || $this->post->achievementNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->achievementNumError;
            return false;
        }
        if (!empty($_POST['planNum']) && (floor($this->post->planNum) != $this->post->planNum || $this->post->planNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->planNumError;
            return false;
        }
        if (!empty($_POST['outPlanNum']) && (floor($this->post->outPlanNum) != $this->post->outPlanNum || $this->post->outPlanNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->outPlanNumError;
            return false;
        }
//        $deptManager = $this->dao->select('id, manager1')->from(TABLE_DEPT)->fetchPairs();
        $dept = $this->dao->select('bearDept')->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch();
        $deptQa = $this->dao->select('qa')->from(TABLE_DEPT)->where('id')->eq($dept->bearDept)->fetch();
        $assembly   = $this->reformAssembly();      // 生成组件信息json
        $tools      = $this->reformTools();         // 测试工具信息json
        $knowledge  = $this->reformKnowledge();     // 知识库相关信息json
        $adviseList = $this->reformAdviseList();    // 生成意见列表

        $nodes2 = [];
        if ($assembly === false || $tools === false || $knowledge === false) return false;
        if($this->post->assemblyAdvise == 1) $nodes2 = explode(',',$this->config->closingitem->assemblyPerson);
        if($this->post->toolsAdvise == 1) $nodes2 = array_merge($nodes2 ,explode(',',$this->config->closingitem->toolsPerson));
        if($this->post->osspAdvise == 1 || $this->post->platformAdvise == 1) $nodes2 = array_merge($nodes2 ,explode(',',$this->config->closingitem->knowledgePerson));
        if($this->post->projectType == 6) $nodes2 = array_merge($nodes2 ,explode(',',$this->config->closingitem->preResearchPerson));
        $version = 1;
        $nodes[] = $deptQa->qa;
        $nodes[] = $nodes2;

        // 生成数据
        $data = fixer::input('post')
            ->add('assemblyInfo', json_encode($assembly))
            ->add('toolsInfo', json_encode($tools))
            ->add('knowledgeInfo', json_encode($knowledge))
            ->add('projectId', $projectID)
            ->add('reviewStage', '0')
            ->add('version', $version)
            ->add('status', $this->lang->closingitem->statusList['waitsubmit'])
            ->add('dealuser', $this->app->user->account)
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::now())
            ->add('files', '')
            ->join('demandAdvise', ',')
            ->join('constructionAdvise', ',')
            ->remove('codes1,codes2,codes3,codes4,codes5,codes6,codes7,assemblyIndex,assemblyDesc,assemblyLevel,toolsName,toolsVersion,isTesting,toolsType,toolsDesc,advise2,advise4,advise5,advise6,submitFileName,submitReason,versionCodeOSSP,comment')
            ->get();

        // 数据存主表
        $this->dao->insert(TABLE_CLOSINGITEM)->data($data)->exec();
        $itemID = $this->dao->lastInsertID();

        $this->submitReviewclosingitem($itemID, $nodes, $version); //提交审批

        // 建议数据存建议表
        foreach($adviseList as $val){
            $val['projectId']     = $projectID;
            $val['itemId']        = $itemID;
            $val['status']        = $this->lang->closingitem->statusList['waitFeedback'];
            $val['createdBy']     = $this->app->user->account;
            $val['createdDate']   = helper::now();
            // 插入结项建议表
            $this->dao->insert(TABLE_CLOSINGADVISE)->data($val)->exec();
            $adviseID = $this->dao->lastInsertID();
            //生成历史记录
            $this->loadModel('action')->create('closingadvise', $adviseID, 'created');
        }

        // 保存流程状态
        $this->loadModel('consumed')->record('closingitem', $itemID, '0', $this->app->user->account, '', $this->lang->closingitem->statusList['waitsubmit'], array());

        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID('', $itemID, 'closingitem');
            $this->file->saveUpload('closingitem', $itemID);
            return $itemID;
        }
        return false;
    }

    /*
     * 公共组件表单内容
     */
    public function reformAssembly()
    {
        $i = 0;
        $assemblys = [];
        // 必填校验
        if ( $this->post->isAssembly == 1 && empty($_POST['codes1'][0])) {
            dao::$errors[] = $this->lang->closingitem->assemblyError;
            return false;
        }
        foreach ($_POST['codes1'] as $code) {
            if (empty($code) || $this->post->isAssembly != 1) continue;
            if (empty($_POST['assemblyIndex'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->assemblyIndexEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['assemblyDesc'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->assemblyDescEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['assemblyLevel'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->assemblyLevelEmpty, $i + 1)];
                return false;
            }
//            if (empty($_POST['assemblyStatus'][$i])) {
//                dao::$errors[''] = [sprintf($this->config->closingitem->statusEmpty, $i + 1)];
//                return false;
//            }
            $temp['codes1'] = $code;
            $temp['assemblyIndex']   = $_POST['assemblyIndex'][$i];
            $temp['assemblyDesc']    = $_POST['assemblyDesc'][$i];
            $temp['assemblyLevel']   = $_POST['assemblyLevel'][$i];
//            $temp['assemblyStatus']  = $_POST['assemblyStatus'][$i];
            $assemblys[]             = $temp;
            $i++;
        }
        return $assemblys;
    }

    /*
     * 测试工具表单内容
     */
    public function reformTools()
    {
        $i = 0;
        $tools = [];
        // 必填校验
        if ( $this->post->toolsUsage == 1 && empty($_POST['codes3'][0])) {
            dao::$errors[''] = [sprintf($this->lang->closingitem->toolsError, $i + 1)];
            return false;
        }
        foreach ($_POST['codes3'] as $code) {
            if (empty($code) || $this->post->toolsUsage != 1) continue;
            if (empty($_POST['toolsName'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->toolsNameEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['toolsVersion'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->toolsVersionEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['toolsType'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->toolsTypeEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['toolsDesc'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->toolsDescEmpty, $i + 1)];
                return false;
            }
            $temp['codes3'] = $code;
            $temp['toolsName']        = $_POST['toolsName'][$i];
            $temp['toolsVersion']     = $_POST['toolsVersion'][$i];
            $temp['isTesting']        = $_POST['isTesting'][$i];
            $temp['toolsType']        = $_POST['toolsType'][$i];
            $temp['toolsDesc']        = $_POST['toolsDesc'][$i];
            $tools[]                  = $temp;
            $i++;
        }
        return $tools;
    }


    /*
     * 知识库表单内容
     */
    public function reformKnowledge()
    {
        $i = 0;
        $tools = [];
        // 必填校验
        if($this->post->adviseChecklist == 1 && empty($_POST['codes7'][0])){
            dao::$errors[''] = [sprintf($this->lang->closingitem->adviseChecklistError)];
            return false;
        }
        foreach ($_POST['codes7'] as $code) {
            if (empty($code) || $this->post->adviseChecklist != 1) continue;
            if (empty($_POST['submitFileName'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->submitFileNameEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['submitReason'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->submitReasonEmpty, $i + 1)];
                return false;
            }
            if (empty($_POST['versionCodeOSSP'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->versionCodeOSSP, $i + 1)];
                return false;
            }
            if (empty($_POST['comment'][$i])) {
                dao::$errors[''] = [sprintf($this->config->closingitem->comment, $i + 1)];
                return false;
            }
            $temp['codes7']          = $code;
            $temp['submitFileName']  = $_POST['submitFileName'][$i];
            $temp['submitReason']    = $_POST['submitReason'][$i];
            $temp['versionCodeOSSP'] = $_POST['versionCodeOSSP'][$i];
            $temp['advise']          = $_POST['comment'][$i];
            $knowledges[]                  = $temp;
            $i++;
        }

        return $knowledges;
    }


    /*
     * 意见表单列表
     */
    public function reformAdviseList()
    {
        $list = [];
//        $deptManager = $this->dao->select('id, manager1')->from(TABLE_DEPT)->fetchPairs();
        // 校验公共技术组件意见
        if($this->post->assemblyAdvise == 1 && empty($_POST['codes2'][0])){
            dao::$errors[''] = [sprintf($this->lang->closingitem->assemblyAdviseError)];
            return false;
        }
        if ($this->post->assemblyAdvise == 1 && is_array($_POST['codes2'])) {
            $i = 0;
            foreach ($_POST['codes2'] as $code) {
                if (empty($code)) continue;
                if (empty($_POST['advise2'][$i])) {
                    dao::$errors[''] = [sprintf($this->config->closingitem->advise2Empty, $i + 1)];
                    return false;
                }
                $temp1['dept']        = '2';
                $temp1['source']      = '1';
                $temp1['dealuser']    = $this->config->closingitem->assemblyPerson;
                $temp1['advise']      = $_POST['advise2'][$i];
                $list[]               = $temp1;
                $i++;
            }
        }
        // 校验测试工具意见
        if($this->post->toolsAdvise == 1 && empty($_POST['codes4'][0])){
            dao::$errors[''] = [sprintf($this->lang->closingitem->toolsAdviseError)];
            return false;
        }
        if ($this->post->toolsAdvise == 1 && is_array($_POST['codes4'])) {
            $i = 0;
            foreach ($_POST['codes4'] as $code) {
                if (empty($code)) continue;
                if (empty($_POST['advise4'][$i])) {
                    dao::$errors[''] = [sprintf($this->config->closingitem->advise4Empty, $i + 1)];
                    return false;
                }
                $temp2['dept']       = '11';
                $temp2['source']     = '2';
                $temp2['dealuser']   = $this->config->closingitem->toolsPerson;
                $temp2['advise']     = $_POST['advise4'][$i];
                $list[]              = $temp2;
                $i++;
            }
        }
        // 校验OSSP改进意见
        if($this->post->osspAdvise == 1 && empty($_POST['codes5'][0])){
            dao::$errors[''] = [sprintf($this->lang->closingitem->osspAdviseError)];
            return false;
        }
        if ($this->post->osspAdvise == 1 && is_array($_POST['codes5'])) {
            $i = 0;
            foreach ($_POST['codes5'] as $code) {
                if (empty($code)) continue;
                if (empty($_POST['advise5'][$i])) {
                    dao::$errors[''] = [sprintf($this->config->closingitem->advise5Empty, $i + 1)];
                    return false;
                }
                $temp3['dept']       = '3';
                $temp3['source']     = '3';
                $temp3['dealuser']   = $this->config->closingitem->knowledgePerson;
                $temp3['advise']     = $_POST['advise5'][$i];
                $list[]              = $temp3;
                $i++;
            }
        }
        // 校验成方研效平台改进意见
        if($this->post->platformAdvise == 1 && empty($_POST['codes6'][0])){
            dao::$errors[''] = [sprintf($this->lang->closingitem->platformAdviseError)];
            return false;
        }
        if ($this->post->platformAdvise == 1 && is_array($_POST['codes6'])) {
            $i = 0;
            foreach ($_POST['codes6'] as $code) {
                if (empty($code)) continue;
                if (empty($_POST['advise6'][$i])) {
                    dao::$errors[''] = [sprintf($this->config->closingitem->advise6Empty, $i + 1)];
                    return false;
                }
                $temp4['dept']       = '3';
                $temp4['source']     = '4';
                $temp4['dealuser']   = $this->config->closingitem->knowledgePerson;
                $temp4['advise']     = $_POST['advise6'][$i];
                $list[]              = $temp4;
                $i++;
            }
        }
        return $list;
    }

    // 审批
    public function review($itemID){

        $closingitem = $this->getByID($itemID);
        $this->loadModel('review');
        // 检查是否有关联意见没处理
        if($closingitem->status == $this->lang->closingitem->statusList['waitFeedback'] && $this->post->result == 'pass') {
            $last = $closingitem->dealuser == $this->app->user->account ? 'last' : '';
            $check = $this->checkAdviseResult($itemID, $last);
            if(!empty($check)){
                if(!empty($last)){
                    dao::$errors[] = $this->lang->closingitem->checkAdviseLastError;
                }else{
                    dao::$errors[] = $this->lang->closingitem->checkAdviseError;
                }
                return false;
            }
        }
        // 检查是否允许评审
        if(empty($_POST['result']))
        {
            dao::$errors[] = $this->lang->closingitem->resultError;
            return false;
        }
        if(empty($_POST['suggest']))
        {
            dao::$errors[] = $this->lang->closingitem->suggestError;
            return false;
        }

        $bool = $closingitem->status == $this->lang->closingitem->statusList['waitPreReview'] ? false : true;
        $result = $this->loadModel('review')->check('closingitem', $itemID, $closingitem->version, $this->post->result, $this->post->suggest,'','',$bool);

        if($result == 'pass')
        {
            $afterStage = $closingitem->reviewStage + 1;  //审批通过，自动前进一步
            // 待QA审批
            if($closingitem->reviewStage < self::MAXNODE-1){
                $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('closingitem')   //查找下一节点的状态
                ->andWhere('objectID')->eq($closingitem->id)
                    ->andWhere('version')->eq($closingitem->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

                if($next)
                {
                    $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                    $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                    $reviewers = $this->review->getReviewer('closingitem', $itemID, $closingitem->version, $afterStage);
                    if(empty($reviewers)){
                        $status = $this->lang->closingitem->statusList['alreadyFeedback'];
                    }else{
                        $status = $this->lang->closingitem->statusList['waitFeedback'];
                    }
                    $reviewers = implode(',',array_unique(explode(',',$reviewers)));
                    $this->dao->update(TABLE_CLOSINGITEM)->set('dealuser')->eq($reviewers)->where('id')->eq($itemID)->exec();
                }

                //更新状态
                $this->dao->update(TABLE_CLOSINGITEM)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->where('id')->eq($itemID)->exec();
                $this->loadModel('consumed')->record('closingitem', $itemID, '0', $this->app->user->account, $closingitem->status, $status, array());
            }else{
                $now = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('closingitem')   //查找当前节点的状态
                    ->andWhere('objectID')->eq($closingitem->id)
                    ->andWhere('version')->eq($closingitem->version)
                    ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch('id');

                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pass')->where('id')->eq($now)->exec();  //更新当前节点的状态为pass
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pass')->where('node')->eq($now)->exec();
                $status = $this->lang->closingitem->statusList['alreadyFeedback'];

                // 修改主表记录
                $this->dao->update(TABLE_CLOSINGITEM)->set('dealuser')->eq('')->set('status')->eq($status)->where('id')->eq($itemID)->exec();
                $this->loadModel('consumed')->record('closingitem', $itemID, '0', $this->app->user->account, $closingitem->status, $status, array());
            }
        }elseif($result == 'part'){
            $persons = [];
            // 各部门负责人反馈
            if(in_array($this->app->user->account, explode(',',$this->config->closingitem->assemblyPerson))){
                $arr = array_flip(explode(',',$this->config->closingitem->assemblyPerson));
                unset($arr[$this->app->user->account]);
                $persons = array_flip($arr);
            }
            if(in_array($this->app->user->account, explode(',',$this->config->closingitem->toolsPerson))){
                $arr = array_flip(explode(',',$this->config->closingitem->toolsPerson));
                unset($arr[$this->app->user->account]);
                $persons = array_merge($persons,array_flip($arr));
            }
            if(in_array($this->app->user->account, explode(',',$this->config->closingitem->knowledgePerson))){
                $arr = array_flip(explode(',',$this->config->closingitem->knowledgePerson));
                unset($arr[$this->app->user->account]);
                $persons = array_merge($persons,array_flip($arr));
            }
            if(in_array($this->app->user->account, explode(',',$this->config->closingitem->preResearchPerson))){
                $arr = array_flip(explode(',',$this->config->closingitem->preResearchPerson));
                unset($arr[$this->app->user->account]);
                $persons = array_merge($persons,array_flip($arr));
            }

            // 查找当前节点的状态
            $now = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('closingitem')
                ->andWhere('objectID')->eq($closingitem->id)
                ->andWhere('version')->eq($closingitem->version)
                ->andWhere('status')->eq('pending')->orderBy('stage,id')->fetch('id');

            // 修改当前审核人的状态为操作状态
            $this->dao->update(TABLE_REVIEWER)
                ->set('status')->eq('ignore')
                ->set('reviewTime')->eq(helper::now())
                ->where('node')->eq($now)
                ->andWhere('status')->eq('pending') //当前状态
                ->andWhere('reviewer')->in($persons)
                ->exec();

            // 查询待处理人
            $reviewers = $this->review->getReviewer('closingitem', $itemID, $closingitem->version, $closingitem->reviewStage + 1);

            if(empty($reviewers)) {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pass')->where('id')->eq($now)->exec();  //更新当前节点的状态为pass
                $status = $this->lang->closingitem->statusList['alreadyFeedback'];
            }else{
                $status = $closingitem->status;
            }
            // 修改主表记录
            $this->dao->update(TABLE_CLOSINGITEM)->set('dealuser')->eq($reviewers)->set('status')->eq($status)->where('id')->eq($itemID)->exec();
            $this->loadModel('consumed')->record('closingitem', $itemID, '0', $this->app->user->account, $closingitem->status, $status, array());
        }else{
            // QA不通过(退回)
            $this->dao->update(TABLE_CLOSINGITEM)->set('dealuser')->eq($closingitem->createdBy)->set('reviewStage')->eq(0)->set('status')->eq($this->lang->closingitem->statusList['qaReject'])->where('id')->eq($itemID)->exec();
            $this->loadModel('consumed')->record('closingitem', $itemID, '0', $this->app->user->account, $closingitem->status, $this->lang->closingitem->statusList['qaReject'], array());
        }
    }


    /**
     * 项目结项提交审核
     * @param $itemID
     * @param $nodes
     * @param $version
     */
    public function submitReviewclosingitem($itemID, $nodes , $version)
    {
        // 第一阶段状态为待处理
        $status = 'pending';
        $stage = 1;

        for ($i = 0; $i < self::MAXNODE; $i++)
        {
            if(!is_array($nodes[$i])){
                $reviewer = array($nodes[$i]);
            }else{
                $reviewer = $nodes[$i];
            }
            $this->loadModel('review')->addNode('closingitem', $itemID, $version, $reviewer, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }

    // 获取结项数据
    public function getByProjectID($projectId){
        $data = false;
        if(!$projectId){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_CLOSINGITEM)
            ->where('projectId')->eq($projectId)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        return $ret;
    }

    // 获取结项数据
    public function getByID($itemId){
        $data = false;
        if(!$itemId){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_CLOSINGITEM)
            ->where('id')->eq($itemId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
            $objectType = $this->lang->closingitem->objectType;
            //关联建议
            $ret->advises  = $this->getAdvises($itemId);
            //文件
            $ret->files    = $this->loadModel('file')->getByObject($objectType, $itemId);
            //状态流转
            $ret->consumed =  $this->loadModel('consumed')->getConsumed($objectType, $itemId);
            $data = $ret;
        }
        return $data;
    }

    // 获取对应意见
    public function getAdvises($itemId){
        $list = [];
        $advises = $this->dao->select('*')->from(TABLE_CLOSINGADVISE)
            ->where('itemId')->eq($itemId)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();

        // 对应意见分组
        foreach ($advises as $advise){
            $list[$advise->source][] = $advise;
        }
        return $list;
    }


    /**
     * Judge button if can clickable.
     *
     * @param  object $review
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($closingitem, $action)
    {
        global $app;
        $dealusers = explode(',',$closingitem->dealuser);
        if($action == 'edit') {
            if((($closingitem->status == 'qaReject' || $closingitem->status ==  'waitsubmit') && $app->user->account == $closingitem->createdBy  && in_array($app->user->account, $dealusers)) || $app->user->account == 'admin') return true;
        }

        if($action == 'submit')  {
            if(($closingitem->status ==  'waitsubmit') && $app->user->account == $closingitem->createdBy && in_array($app->user->account, $dealusers)) return true;
        }

        if($action == 'review')  {
            if(($closingitem->status == 'waitPreReview' || $closingitem->status ==  'waitFeedback') && in_array($app->user->account, $dealusers)) return true;
        }

        if($action == 'delete'){//专家评审
            if($closingitem->status != 'alreadyFeedback' && $app->user->account == $closingitem->createdBy) return true;
        }

        return false;

    }


    /**
     * Send mail
     *
     * @param  int    $itemID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($itemID, $actionID)
    {
        $this->loadModel('mail');
        $this->loadModel('closingadvise');
        $this->loadModel('projectplan');
        $typeList     = $this->lang->projectplan->typeList;
        $projects     = $this->projectplan->getAllProjects();
        $users = $this->loadModel('user')->getPairs('noletter');

        $closingitem = $this->getById($itemID);
        // 查询是否都未处理(若已有处理人处理则不再发邮件 避免重复发邮件)
        if($closingitem->status == 'waitFeedback'){
            $reviewers = $this->getIsPassReviewer($itemID, $closingitem->version);
            if(!empty($reviewers)) return;
        }
        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setClosingitemMail) ? $this->config->global->setClosingitemMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get actions. */
        $action  = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'closingitem');
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

        /* Send it. */
        $this->mail->send($closingitem->dealuser, $mailTitle, $mailContent, '');
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    // 喧喧消息
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $closingitem = $this->getById($objectID);
        // 查询是否都未处理(若已有处理人处理则不再发邮件 避免重复发邮件)
        if($closingitem->status == 'waitFeedback'){
            $reviewers = $this->getIsPassReviewer($objectID, $closingitem->version);
            if(!empty($reviewers)) return;
        }
        $toList = $obj->dealuser;
        if(is_array($toList)){
            $toList = implode(',', $toList);
        }
        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "projectId=$closingitem->projectId&id=$objectID", 'html');

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

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];

    }

    /**
     * 查询是否有未处理意见
     *
     * @param $itemID
     * @param string $last
     * @return mixed
     */
    public function checkAdviseResult($itemID, $last = ''){
        $userAccount = $this->app->user->account;
        $userAccountStr = "'".$userAccount."'";
        $data =  $this->dao->select('*')->from(TABLE_CLOSINGADVISE)
            ->where('itemId')->eq($itemID)
            ->beginIF(empty($last))
            ->andWhere("FIND_IN_SET({$userAccountStr}, dealuser)")
            ->fi()
            ->andWhere('status')->eq($this->lang->closingitem->statusList['waitFeedback'])
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        return $data;
    }


    //Update
    public function update($itemId, $projectID)
    {
        // 正整数校验
        if ($this->post->isAssembly == 1 && (floor($this->post->assemblyNum) != $this->post->assemblyNum || $this->post->assemblyNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->assemblyNumError;
            return false;
        }
//        if (!empty($_POST['realPoints']) && (floor($this->post->realPoints) != $this->post->realPoints || $this->post->realPoints < 0)) {
//            dao::$errors[] = $this->lang->closingitem->realPointsError;
//            return false;
//        }
        if (!empty($_POST['achievementNum']) && (floor($this->post->achievementNum) != $this->post->achievementNum || $this->post->achievementNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->achievementNumError;
            return false;
        }
        if (!empty($_POST['planNum']) && (floor($this->post->planNum) != $this->post->planNum || $this->post->planNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->planNumError;
            return false;
        }
        if (!empty($_POST['outPlanNum']) && (floor($this->post->outPlanNum) != $this->post->outPlanNum || $this->post->outPlanNum < 0)) {
            dao::$errors[] = $this->lang->closingitem->outPlanNumError;
            return false;
        }
        $this->loadModel('closingadvise');
        $oldData = $this->getByID($itemId);
//        $deptManager = $this->dao->select('id, manager1')->from(TABLE_DEPT)->fetchPairs();
        $dept = $this->dao->select('bearDept')->from(TABLE_PROJECTPLAN)->where('project')->eq($projectID)->fetch();
        $deptQa = $this->dao->select('qa')->from(TABLE_DEPT)->where('id')->eq($dept->bearDept)->fetch();
        $assembly   = $this->reformAssembly();      // 生成组件信息json
        $tools      = $this->reformTools();         // 测试工具信息json
        $knowledge  = $this->reformKnowledge();     // 知识库相关信息json
        $adviseList = $this->reformAdviseList();    // 生成意见列表

        $nodes2 = [];
        if(!empty($assembly) || $this->post->assemblyAdvise == 1) $nodes2 = explode(',',$this->config->closingitem->assemblyPerson);
        if(!empty($tools) || $this->post->toolsAdvise == 1) $nodes2 = array_merge($nodes2 ,explode(',',$this->config->closingitem->toolsPerson));
        if($this->post->osspAdvise == 1 || $this->post->platformAdvise == 1) $nodes2 = array_merge($nodes2 ,explode(',',$this->config->closingitem->knowledgePerson));
        if($this->post->projectType == 6) $nodes2 = array_merge($nodes2 ,explode(',',$this->config->closingitem->preResearchPerson));
        $nodes[] = $deptQa->qa;
        $nodes[] = $nodes2;

        // 生成数据
        $data = fixer::input('post')
            ->add('assemblyInfo', json_encode($assembly))
            ->add('toolsInfo', json_encode($tools))
            ->add('knowledgeInfo', json_encode($knowledge))
            ->add('projectId', $projectID)
            //->add('dealuser', $deptQa->qa) //qa
            ->add('files', '')
            ->join('demandAdvise', ',')
            ->join('constructionAdvise', ',')
            ->remove('codes1,codes2,codes3,codes4,codes5,codes6,codes7,assemblyIndex,assemblyDesc,assemblyLevel,toolsName,toolsVersion,isTesting,toolsType,toolsDesc,advise2,advise4,advise5,advise6,submitFileName,submitReason,versionCodeOSSP,comment')
            ->get();

        // 生成新版本节点
        if($oldData->status == $this->lang->closingitem->statusList['qaReject']){
            // 改主表数据
            $newVersion = $oldData->version+1;
            $data->version = $newVersion;
            $data->status  = $this->lang->closingitem->statusList['waitsubmit'];
            $this->dao->update(TABLE_CLOSINGITEM)->data($data)->where('id')->eq($itemId)->exec();
            $this->submitReviewclosingitem($itemId, $nodes, $newVersion); //提交审批

            // 保存流程状态
            $this->loadModel('consumed')->record('closingitem', $itemId, '0', $this->app->user->account, $this->lang->closingitem->statusList['qaReject'], $this->lang->closingitem->statusList['waitsubmit'], array());
        }elseif($oldData->assemblyAdvise != $data->assemblyAdvise || $oldData->toolsAdvise != $data->toolsAdvise || ($oldData->osspAdvise != $data->osspAdvise && $oldData->platformAdvise != $data->platformAdvise && $data->osspAdvise == $data->platformAdvise)){//改了是否有意见
            // 改主表数据
            $newVersion = $oldData->version+1;
            $data->version = $newVersion;
            $this->dao->update(TABLE_CLOSINGITEM)->data($data)->where('id')->eq($itemId)->exec();
            $this->submitReviewclosingitem($itemId, $nodes, $newVersion); //提交审批
        }else{
            // 改主表数据
            $this->dao->update(TABLE_CLOSINGITEM)->data($data)->where('id')->eq($itemId)->exec();
        }

        // 建议数据存建议表
        $this->dao->update(TABLE_CLOSINGADVISE)->set('deleted')->eq('1')->where('itemId')->eq($itemId)->exec();
        foreach($adviseList as $val){
            $val['projectId']     = $projectID;
            $val['itemId']        = $itemId;
            $val['status']        = $this->lang->closingitem->statusList['waitFeedback'];
            $val['createdBy']     = $this->app->user->account;
            $val['createdDate']   = helper::now();
            $list[$val['source']][]                 = $val;
            // 插入结项建议表
            $this->dao->insert(TABLE_CLOSINGADVISE)->data($val)->exec();
            $adviseID = $this->dao->lastInsertID();
            //生成历史记录
            $this->loadModel('action')->create('closingadvise', $adviseID, 'created');
        }

        if(!dao::isError())
        {
            $this->loadModel('file')->updateObjectID('', $itemId, 'closingitem');
            $this->file->saveUpload('closingitem', $itemId);
            $change = $this->createChanges($oldData, $data);
            $listChange = $this->createArrChanges($oldData->advises, $list, $this->lang->closingadvise->sourceList, $this->lang->closingitem->sourceList);
            $change = array_merge($change, $listChange);
            return $change;
        }
    }

    // 生成主表变更信息
    public static function createChanges($old, $new)
    {
        $changes    = array();
        foreach($new as $key => $value)
        {
            if(isset($old->$key) and is_string($old->$key) and $value != $old->$key)
            {
                $diff = '';
                $changes[] = array('field' => $key, 'old' => $old->$key, 'new' => $value, 'diff' => $diff);
            }
        }
        return $changes;
    }

    // 生成意见表变更信息
    public static function createArrChanges($oldArr, $newArr, $sourceList, $itemSourceList)
    {
        $changes    = array();
        foreach($sourceList as $key => $val){
            $oldAdvise = json_encode(array_column($oldArr[$key], advise));
            $newAdvise = json_encode(array_column($newArr[$key], advise));

            if( $oldAdvise != $newAdvise ){
                $changes[] = array('field' => $itemSourceList[$key], 'old' => $oldAdvise, 'new' => $newAdvise, 'diff' => '');
            }
        }
        return $changes;
    }

    // 获取项目结项状态
    public function getItemStatusPairsByProjectID($projectID){
        return $this->dao->select('id,status')->from(TABLE_CLOSINGITEM)
            ->where('projectId')->eq($projectID)
            ->andWhere('deleted')->eq('0')
            ->fetchPairs();
    }

    // 获取已处理的处理人
    public function getIsPassReviewer($objectID, $version = 1)
    {
        $node = $this->dao->select('*')->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('closingitem')
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('status')->eq('pending')
            ->orderBy('stage,id')
            ->fetch();
        if(!$node) return '';

        $reviews = $this->dao->select('reviewer')->from(TABLE_REVIEWER)
            ->where('node')->eq($node->id)
            ->andWhere('status')->eq('pass')
            ->orderBy('id')
            ->fetchPairs();
        return join(',', $reviews);
    }
}
