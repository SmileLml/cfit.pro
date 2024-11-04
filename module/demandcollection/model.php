<?php
class demandcollectionModel extends model
{
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $demandcollectionQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('demandcollectionQuery', $query->sql);
                $this->session->set('demandcollection', $query->form);
            }

            if($this->session->demandcollectionQuery == false) $this->session->set('demandcollectionQuery', ' 1 = 1');

            $demandcollectionQuery = $this->session->demandcollectionQuery;

        }

        $demandcollections = $this->dao->select('*')->from(TABLE_DEMANDCOLLECTION)
            ->where('deleted')->eq(0)
            ->beginIF($browseType != 'all' and $browseType != 'bysearch' and $browseType != 'wait')->andWhere('state')->eq($browseType)->fi()
            ->beginIF($browseType == 'wait')->andWhere('dealuser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($demandcollectionQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'demandcollection', $browseType != 'bysearch');

        return $demandcollections;
    }

    public function create()
    {
        $demandcollection = fixer::input('post')
            ->add('createBy', $this->app->user->account)
            ->add('createDate', helper::now())
            ->add('updateBy', $this->app->user->account)
            ->add('updateDate', helper::now())
            ->add('state','1')
            ->remove('files,copyFor')
            ->stripTags($this->config->demandcollection->editor->create['id'], $this->config->allowedTags)
            ->get();
        $demandcollection = $this->loadModel('file')->processImgURL($demandcollection, $this->config->demandcollection->editor->create['id'], $this->post->uid);
        //如果需求类型是数据修正，沟通确认记录和沟通确认人必填
        if($demandcollection->type == 6){
            $this->config->demandcollection->create->requiredFields .= ',commConfirmRecord,commConfirmBy,correctionReason';
        }else{
            $demandcollection->commConfirmRecord = $demandcollection->commConfirmBy = $demandcollection->correctionReason = '';
        }
        if($this->post->copyFor)
        {
            $demandcollection->copyFor = implode(',',$this->post->copyFor);
        }

        if($this->post->submitter)
        {
            $demandcollection->dept = $this->loadModel('user')->getByID($this->post->submitter)->dept;
        }

        if($this->post->productmanager)
        {
            $demandcollection->dealuser = $this->post->productmanager;
        }
        //沟通确认人多选
        if($this->post->commConfirmBy)
        {
            $demandcollection->commConfirmBy = implode(',',array_filter($this->post->commConfirmBy));
        }

        $this->dao->insert(TABLE_DEMANDCOLLECTION)->data($demandcollection)
            ->autoCheck()->batchCheck($this->config->demandcollection->create->requiredFields, 'notempty')
            ->exec();
        $demandcollectionID = 0;
        if(!dao::isError())
        {
            $demandcollectionID = $this->dao->lastInsertID();
            $this->loadModel('file')->saveUpload('demandcollection', $demandcollectionID);
            $this->file->updateObjectID($this->post->uid, $demandcollectionID, 'demandcollection');
        }
        return $demandcollectionID;

    }

    public function update($demandcollectionId, $actioned = 'edit')
    {
        $oldDemandCollection = $this->getByID($demandcollectionId);
        $demandcollection = fixer::input('post')
            ->join('product', ',')
            ->join('Expected', ',')
            ->join('Actual', ',')
            ->remove('files,copyFor,operations')
            ->stripTags($this->config->demandcollection->editor->edit['id'], $this->config->allowedTags)
            ->get();
        if(isset($demandcollection->product) && !empty($demandcollection->product)){
            $demandcollection->product  = trim($demandcollection->product, ',');
        }

        if(isset($demandcollection->Expected) && !empty($demandcollection->Expected)){
            $demandcollection->Expected  = trim($demandcollection->Expected, ',');
        }

        if(isset($demandcollection->Actual) && !empty($demandcollection->Actual)){
            $demandcollection->Actual  = trim($demandcollection->Actual, ',');
        }
        //如果需求类型是数据修正，沟通确认记录和沟通确认人必填
        if($actioned != 'closed'){
            if($demandcollection->type == 6 ){
                $this->config->demandcollection->edit->requiredFields .= ',commConfirmRecord,commConfirmBy,correctionReason';
            }else{
                $demandcollection->commConfirmRecord = $demandcollection->commConfirmBy = '';
            }
        }
        //沟通确认人多选
        if($demandcollection->commConfirmBy)
        {
            $demandcollection->commConfirmBy = implode(',',array_filter($demandcollection->commConfirmBy));
        }

        $demandcollection = $this->loadModel('file')->processImgURL($demandcollection, $this->config->demandcollection->editor->edit['id'], $this->post->uid);

        if($actioned == 'edit')
        {
            $demandcollection->updateBy = $this->app->user->account;
            $demandcollection->updateDate = helper::now();
            $demandcollection->state = '1';
            $demandcollection->dealuser = $this->post->productmanager;
        }

        if($actioned == 'deal')
        {
            if(!isset($demandcollection->product) || empty($demandcollection->product)){
                dao::$errors['product'] = sprintf($this->lang->demandcollection->fieldEmptyError ,  $this->lang->demandcollection->product);
                return false;
            }
            if(empty($oldDemandCollection->responseDate)){
                 $demandcollection->responseDate = helper::now();
            }
            $demandcollection->processingBy = $this->app->user->account;
            $demandcollection->processingDate = helper::now();
            $demandcollection->dealuser = $this->post->assignFor;

            if($this->post->operations == 'transfer')
            {
                $demandcollection->handoverDate = helper::now();
                $demandcollection->handoverBy = $this->app->user->account;
                $storyOld = $this->dao->select('*')->from(TABLE_STORY)
                    ->where('source')->eq('tb')
                    ->andWhere('sourceNote')->eq($demandcollectionId)
                    ->fetch();
                $productIds = array_filter(explode(',', $demandcollection->product));
                $plans = $this->loadModel('productplan')->getPairs($productIds, '0', '', true);
                if(!$storyOld)
                {
                    $story = new stdclass();
                    $story->product = $demandcollection->product;
                    $story->module = '0';
                    $story->plan = '';
                    $story->source = 'tb';
                    $story->sourceNote = $demandcollectionId;
                    $story->assignedTo = $demandcollection->dealuser;
                    $story->title = '[' . zget($this->lang->demandcollection->typeList,$demandcollection->type) . ']' . $demandcollection->title;
                    $story->color = '';
                    $story->pri = $demandcollection->priority;
                    $story->estimate = '';
                    $story->spec = $demandcollection->analysis;
                    $story->verify = '';
                    $story->status = 'draft';
                    $story->mailto = $demandcollection->dealuser;
                    $story->keywords = $this->lang->demandcollection->Expected . ':' . zget($plans,$demandcollection->Expected);
                    $story->type = 'story';
                    $story->assignedDate = helper::now();
                    $story->version = '1';
                    $story->openedBy = $this->app->user->account;
                    $story->openedDate = helper::now();
                    $story->stage = 'wait';
                    $this->dao->insert(TABLE_STORY)->data($story,'spec,verify')->exec();
                    $storyID = $this->dao->lastInsertID();
                    $data          = new stdclass();
                    $data->story   = $storyID;
                    $data->version = 1;
                    $data->title   = $story->title;
                    $data->spec    = $story->spec;
                    $data->verify  = $story->verify;
                    $this->dao->insert(TABLE_STORYSPEC)->data($data)->exec();
                    $actionID = $this->loadModel('action')->create('story', $storyID, 'Opened', '');

                }elseif($storyOld->status != 'active')
                {
                    // update
                    $story = new stdclass();
                    $story->sourceNote = $demandcollectionId;
                    $story->assignedTo = $demandcollection->dealuser;
                    $story->title = '[' . zget($this->lang->demandcollection->typeList,$demandcollection->type) . ']' . $demandcollection->title;
                    $story->pri = $demandcollection->priority;
                    $expected = '';
                    if(isset($demandcollection->Expected) && !empty($demandcollection->Expected)){
                        $expected = $demandcollection->Expected;
                    }
                    $story->keywords = $this->lang->demandcollection->Expected . ':' . $expected;
                    $story->assignedTo = $demandcollection->dealuser;
                    $story->lastEditedBy = $this->app->user->account;
                    $story->lastEditedDate = helper::now();
                    $this->dao->update(TABLE_STORY)->data($story)->where('id')->eq($storyOld->id)->exec();
                    $actionID = $this->loadModel('action')->create('story', $storyOld->id, 'Edited', '');
                    $data          = new stdclass();
                    $data->spec = $demandcollection->analysis;
                    $this->dao->update(TABLE_STORYSPEC)->data($data)->where('story')->eq($storyOld->id)->exec();
                }else{
                    dao::$errors['storystateerror'] = $this->lang->demandcollection->storystateerror;
                    return false;
                }
            }
        }

        if($actioned == 'confirmed')
        {
            $demandcollection->confirmBy = $this->app->user->account;
            $demandcollection->confirmDate = helper::now();
            $demandcollection->dealuser = $this->post->productmanager;
        }

        if($actioned == 'closed')
        {
            $demandcollection->closedBy = $this->app->user->account;
            $demandcollection->closedDate = helper::now();
            $demandcollection->dealuser = '';
        }

        if($this->post->copyFor)
        {
            $demandcollection->copyFor = implode(',',$this->post->copyFor);
        }

        if($this->post->submitter)
        {
            $demandcollection->dept = $this->loadModel('user')->getByID($this->post->submitter)->dept;
        }
        if(!$this->post->launchDate)
        {
           unset($demandcollection->launchDate);
        }
        
        $this->dao->update(TABLE_DEMANDCOLLECTION)->data($demandcollection)->autoCheck()
        ->autoCheck()->batchCheck($this->config->demandcollection->edit->requiredFields, 'notempty')
        ->where('id')->eq($demandcollectionId)
        ->exec();
        $this->loadModel('file')->saveUpload('demandcollection', $demandcollectionId);
        $this->file->updateObjectID($this->post->uid, $demandcollectionId, 'demandcollection');
        return common::createChanges($oldDemandCollection,$demandcollection);
    }

    public function getByID($demandcollectionId=0, $showFile = false)
    {
        $demandcollection = $this->dao->findByID($demandcollectionId)->from(TABLE_DEMANDCOLLECTION)->fetch();
        $story = $this->dao->select('id,sourceNote')->from(TABLE_STORY)->where('sourceNote')->eq($demandcollection->id)->andWhere('source')->eq('tb')->fetch();
        $demandcollection->storyId   = isset($story->id)   ? $story->id   : '';
        $demandcollection = $this->loadModel('file')->replaceImgURL($demandcollection, $this->config->demandcollection->editor->edit['id']);
        if($showFile) $demandcollection->files = $this->loadModel('file')->getByObject('demandcollection', $demandcollectionId);

        return $demandcollection;
    }

    // 查找有权限查看或填写解决方案字段人员
    public function getScheme($param){
        $viewers = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('module')->eq('demandcollection')->andWhere('section')->eq($param)->andWhere('`system`')->eq(0)->fetchPairs();
        return $viewers;
    }

    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->demandcollection->search['actionURL'] = $actionURL;
        $this->config->demandcollection->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->demandcollection->search);
    }


    public static function isClickable($demandcollection,$action){
        global $app;
        $action = strtolower($action);
        if($action == 'edit')           return $demandcollection->state == '1';
        if($action == 'deal')           return true;
        if($action == 'confirmed')   return ($demandcollection->state == '2' or $demandcollection->state == '8') and strpos(",$demandcollection->dealuser,", ",{$app->user->account},") !== false;;
        if($action == 'closed')        return true;
        if($action == 'selectspace')        return true;
        //同步需求池权限：后台配置并且只有待处理人或产品经理高亮。
        if($action == 'syncdemand'){
            return $app->user->account == $demandcollection->productmanager or strpos(",$demandcollection->dealuser,", ",{$app->user->account},") !== false or 'admin' == $app->user->account;
        }
    }

    public function printCell($col, $demand, $depts, $users, $plans)
    {
        //$account    = $this->app->user->account;
        $id = $col->id;

        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            if($id == 'id') $class .= ' cell-id';
            if($id == 'title')
            {
                $class .= ' text-left' . $demand->title;
                $title  = "title='{$demand->title}'";
            }
            echo "<td class='" . $class . "' $title>";
            switch($id)
            {
                case 'id':
                    echo "<div class='checkbox-primary'><input type='checkbox' name='idList[]' value='$demand->id' id='idList.$demand->id'> <label for='idList.$demand->id'></label></div>".$demand->id;
                    break;
                case 'title':
                    echo html::a(helper::createLink('demandcollection','view', "demandcollectionId=$demand->id"),$demand->title);
                    break;
                case 'dept':
                    echo '<div class="ellipsis" title="' . zget($depts,$demand->dept) . '">' . zget($depts,$demand->dept) .'</div>';
                    break;
                case 'type':
                    echo zget($this->lang->demandcollection->typeList,$demand->type);
                    break;
                case 'priority':
                    echo $demand->priority;
                    break;
                case 'feedbackResult':
                    echo '<div class="ellipsis" title="' . $demand->feedbackResult . '">' . $demand->feedbackResult .'</div>';
                    break;
               case 'submitter':
                    echo zget($users,$demand->submitter);
                    break;
                case 'belongModel':
                    echo '<div class="text-ellipsis" title="' . zget($this->getChildTypeList($demand->belongPlatform),$demand->belongModel) . '">' . zget($this->getChildTypeList($demand->belongPlatform),$demand->belongModel) .'</div>';
                    break;
                case 'belongPlatform':
                    echo '<div class="text-ellipsis" title="' . zget($this->lang->demandcollection->belongPlatform,$demand->belongPlatform) . '">' . zget($this->lang->demandcollection->belongPlatform,$demand->belongPlatform) .'</div>';
                    break;
                case 'createDate':
                    echo substr($demand->createDate, 0, 11);
                    break;
                case 'responseDate':
                    echo substr($demand->responseDate, 0, 11);
                    break;
                case 'processingDate':
                    echo substr($demand->processingDate, 0, 11);
                    break;
                case 'handoverDate':
                    echo substr($demand->handoverDate, 0, 11);
                    break;
                case 'feedbackDate':
                    echo substr($demand->feedbackDate, 0, 11);
                    break;
                case 'scheduledDate':
                    echo substr($demand->scheduledDate, 0, 11);
                    break;
                case 'developstate':
                    echo $demand->developstate;
                    break;
                case 'launchDate':
                    echo $demand->launchDate?$demand->launchDate:"";
                    break;
                case 'Expected':
                    echo '<div class="ellipsis" title="' . zget($plans,$demand->Expected) . '">' . zget($plans,$demand->Expected) .'</div>';
                    break;
                case 'Actual':
                    echo '<div class="ellipsis" title="' . zget($plans,$demand->Actual) . '">' . zget($plans,$demand->Actual) .'</div>';
                    break;
                case 'Implementation':
                    echo zget($depts,$demand->Implementation);
                    break;
                case 'Developer':
                    echo zget($users,$demand->Developer);
                    break;
                case 'state':
                    echo zget($this->lang->demandcollection->statusList,$demand->state);
                    break;
                case 'dealuser':
                    echo zget($users,$demand->dealuser);
                    break;
                case 'actions':
                    common::printIcon('demandcollection', 'edit', "demandcollectionId=$demand->id", $demand, 'list');
                    common::printIcon('demandcollection', 'deal', "demandcollectionId=$demand->id", $demand, 'list','time');
                    common::printIcon('demandcollection', 'confirmed', "demandcollectionId=$demand->id", $demand, 'list','ok', '', 'iframe', true);
                    common::printIcon('demandcollection', 'closed', "demandcollectionId=$demand->id", $demand, 'list', 'off','', 'iframe', true);
//                    common::printIcon('demandcollection', 'selectspace', "id=$demand->id", $demand, 'list', 'refresh','', 'iframe', true);
                    if ($demand->cardID > 0){
                        if (common::hasPriv('demandcollection', 'selectspace')){
                            echo '<a href="javascript:void(0)" class="btn editDemandToKanban" id="editDemandToKanban_'.$demand->id.'" title="需求收集同步看板" onclick="updateKanbancard('.$demand->id.')"><i class="icon-demandcollection-selectspace icon-refresh"></i></a>';
                            //<a href="/cfitsd/demandcollection-selectspace-637.html?onlybody=yes" class="btn iframe" title="需求收集同步看板" data-app="demandcollection"><i class="icon-demandcollection-selectspace icon-refresh"></i></a>

                        }else{

                        }
                    }else{
                        common::printIcon('demandcollection', 'selectspace', "id=$demand->id", $demand, 'list', 'refresh','', 'iframe', true,'data-width="660"');
                    }
                    if (common::hasPriv('demandcollection', 'syncDemand') && $this->isClickable($demand, 'syncDemand')){
                        echo '<button type="button" class="btn" title="' . $this->lang->demandcollection->syncDemand . '" onclick="isClickable('.$demand->id.', \'syncDemand\')"><i class="icon-common-suspend icon-exchange"></i></button>';
                    }
                    common::printIcon('demandcollection', 'syncDemand', "id=$demand->id", $demand, 'list', 'exchange','', 'hidden', false, 'id=isClickable_syncDemand' . $demand->id);
                echo '</td>';
            }
        }
    }



    /**
     * Send mail
     *
     * @param  int    $storyID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($demandcollectionId, $actionID)
    {
        $this->loadModel('mail');
        $demandcollection = $this->getById($demandcollectionId);
        $users  = $this->loadModel('user')->getPairs('noletter');
        /* Get actions. */
        $action  = $this->loadModel('action')->getById($actionID);
        $history = $this->action->getHistory($actionID);
        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();
        if($action->action == 'created'){ //创建需求搜集
            $toListUsers = [];
            $ccListUsers = [];
            $ccList = '';
            if($demandcollection->assignFor){
                $toListUsers[] = $demandcollection->assignFor;
            }
            if($demandcollection->productmanager){
                $toListUsers[] = $demandcollection->productmanager;
            }
            $toListUsers = array_filter(array_unique($toListUsers));
            $toList = implode(',', $toListUsers);

            //抄送人
            if($demandcollection->copyFor){
                $ccListUsers = explode(',', $demandcollection->copyFor);
                $ccListUsers = array_filter(array_unique($ccListUsers));
                $ccList = implode(',', $ccListUsers);
            }

        }else{
            //新增抄送人和抄送人字段内容作去重处理
            $copyFor = implode(',',$this->getScheme('copyForList')).",".$demandcollection->productmanager;
            $copyForArray = $demandcollection->copyFor?$demandcollection->copyFor.','.$copyFor:$copyFor;
            $ccList = ltrim(implode(',',array_unique(explode(",",$copyForArray))),',');

            //给需求提交者和指派人发邮件
            $toListArray = $demandcollection->assignFor ? $demandcollection->submitter .','. $demandcollection->assignFor: $demandcollection->submitter;
            $toList = implode(',',array_unique(explode(",",$toListArray)));
        }



        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setDemandcollectionMail) ? $this->config->global->setDemandcollectionMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
       // $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);
        $statusDesc = zget($this->lang->demandcollection->statusList, $demandcollection->state, '');
        $mailTitle = "【通知】您有一个【需求收集】{$statusDesc}，请及时登录研发过程管理平台进行查看";


        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'demandcollection');
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
        $this->mail->send($toList, $mailTitle, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }
    public function batchKanbanState($id){
        if (!$id){
            return false;
        }
        $oldDemandCollection = $this->getByID($id);
        $data = new stdClass();
        $data->processingBy = $this->app->user->account;
        $data->processingDate = helper::now();
        $data->dealuser = '';
        $data->state = 5;
        $this->dao->update(TABLE_DEMANDCOLLECTION)->data($data)
            ->where('id')->eq($id)
            ->exec();
        $changes = common::createChanges($oldDemandCollection,$data);
        $actionID = $this->loadModel('action')->create('demandcollection', $id, 'deal', '看板归档卡片');
        $this->action->logHistory($actionID, $changes);
        $this->sendmail($id,$actionID);
    }


    // 获取分类下的子类数据。
    public function getChildTypeList($assignType = '')
    {
        // 自定义的demandcollection子类数据。
        $childTypeList = isset($this->lang->demandcollection->childTypeList) ? $this->lang->demandcollection->childTypeList['all'] : '[]';
        $childTypeList = json_decode($childTypeList, true);
        $customList    = empty($childTypeList[$assignType]) ? array('0' => '') : $childTypeList[$assignType];
        if(!empty($customList)) $customList = array('0' => '') + $customList;
        return $customList;
    }
}

