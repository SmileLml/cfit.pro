<?php
class closingadviseModel extends model
{
    const MAXNODE           = 2;   //审批节点最大值是2

    // 审批
    public function review($itemID){

        $closingadvise = $this->getByID($itemID);
        $this->loadModel('review');
        // 检查参数是否为空
        if(empty($_POST['status']))
        {
            dao::$errors[] = $this->lang->closingadvise->statusError;
            return false;
        }
        if(empty($_POST['comment']))
        {
            dao::$errors[] = $this->lang->closingadvise->suggestError;
            return false;
        }
//        // 删除当前处理人
//        $oldDealuser = explode(',',$closingadvise->dealuser);
//        foreach($oldDealuser as $key => $dealuser){
//            if($dealuser == $this->app->user->account) unset($oldDealuser[$key]);
//        }

        // 生成修改数据
        $data           = new stdClass();
        $data->status   = $this->post->status;
        $data->comment  = $this->post->comment;
        //$data->dealuser = empty($oldDealuser) ? '' : implode(',',$oldDealuser);

        // 修改主表记录
        $this->dao->update(TABLE_CLOSINGADVISE)->data($data)->where('id')->eq($itemID)->exec();

//        // 状态流转
//        $this->loadModel('consumed')->record('closingadvise', $itemID, 0, $this->app->user->account, $closingadvise->status, $this->post->status);

        // 返回变更数据
        if(empty($closingadvise->comment)) $closingadvise->comment = '';
        $logChange = common::createChanges($closingadvise, $data);
        return $logChange;
    }



    // 获取结项数据
    public function getByProjectID($projectId, $orderBy, $pager){
        $data = false;
        if(!$projectId){
            return $data;
        }

        $orderBy = "t1.".$orderBy;
        $ret = $this->dao->select('t1.*')->from(TABLE_CLOSINGADVISE)->alias('t1')
            ->leftJoin(TABLE_CLOSINGITEM)->alias('t2')->on('t1.itemId=t2.id')
            ->where('t1.projectId')->eq($projectId)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.status')->in(['waitFeedback','alreadyFeedback'])
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        return $ret;
    }

    // 获取结项数据
    public function getByID($itemId){
        $data = false;
        if(!$itemId){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_CLOSINGADVISE)
            ->where('id')->eq($itemId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if($ret){
//            $objectType = $this->lang->closingitem->objectType;
//            //状态流转
//            $ret->consumed =  $this->loadModel('consumed')->getConsumed($objectType, $itemId);
            $data = $ret;
        }
        return $data;
    }


    /**
     * Judge button if can clickable.
     *
     * @param  object $review
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($closingadvise ,$action)
    {
        global $app;

        if($action == 'review' || $action == 'assignUser')  {
            if(in_array($app->user->account,explode(',',$closingadvise->dealuser)) && $closingadvise->itemStatus != 'alreadyFeedback') return true;
        }

        return false;

    }

    // 指派待处理人
    public function assignUser($id){
        $data = fixer::input('post')->get();
        if(empty(array_filter($data->dealusers))){
            dao::$errors[] = $this->lang->closingadvise->dealusersError;
            return false;
        }

        $oldData = $this->getByID($id);
        $dealusers = implode(',',$data->dealusers);

        // 改closingadvise表待处理人
        $value = new stdClass();
        $value->dealuser = $dealusers;
        $this->dao->update(TABLE_CLOSINGADVISE)->set('dealuser')->eq($dealusers)->where('id')->eq($id)->exec();

        $change = common::createChanges($oldData, $value);
        return $change;
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
        $this->loadModel('closingitem');
        $this->loadModel('projectplan');
        $projects        = $this->projectplan->getAllProjects();
        $feedbackResults = $this->lang->closingitem->feedbackResult;;

        $closingadvise = $this->getById($itemID);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setClosingadviseMail) ? $this->config->global->setClosingadviseMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get actions. */
        $action  = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'closingadvise');
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
        $this->mail->send($closingadvise->dealuser, $mailTitle, $mailContent, '');
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    // 喧喧消息
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $closingadvise = $this->getById($objectID);
        $toList = $obj->dealuser;
        if(is_array($toList)){
            $toList = implode(',', $toList);
        }
        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "projectId=$closingadvise->projectId&id=$objectID", 'html');

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

}
