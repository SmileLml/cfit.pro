<?php
/**
 * The model file of reviewpqz module of ZenTaoPMS.
 *
 * Created by PhpStorm.
 * User: t_wangjiurong
 * Date: 2023/2/20
 * Time: 9:43
 */
class reviewissueqzModel extends model
{
    public function buildSearchForm($queryID, $actionURL){
        $this->config->reviewissueqz->search['actionURL'] = $actionURL;
        $this->config->reviewissueqz->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->reviewissueqz->search);
    }

    /**
     *清总评审问题列表
     *
     * @param int $reviewID
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($reviewID = 0, $browseType, $queryID, $orderBy, $pager = null){
        $reviewissueqzQuery = '';
        $this->loadModel('reviewqz');
        if($browseType == 'bySearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query) {
                $this->session->set('reviewissueqzQuery', $query->sql);
                $this->session->set('reviewissueqzForm', $query->form);
            }
            if($this->session->reviewissueqzQuery == false) $this->session->set('reviewissueqzQuery', ' 1 = 1');
            $reviewissueqzQuery = $this->session->reviewissueqzQuery;
        }
        //当前用户
        $currentUser = $this->app->user->account;
        //admin暂存入接口人
        $liasisonOfficer = $this->config->reviewqz->liasisonOfficer.',admin';
        $andWhere = "((createBy = '$currentUser') or (raiseBy = '$currentUser'))";
        
        // 获取评审id范围
        $data = $this->dao->select('*')->from(TABLE_REVIEWISSUEQZ)
            ->where('deleted')->eq('0')
            ->beginIF(!in_array($currentUser, explode(',',$liasisonOfficer)))->andWhere($andWhere)->fi()
            ->beginIF($reviewID > 0)->andWhere('reviewId')->eq($reviewID)->fi()
            ->beginIF($browseType == 'bySearch')->andWhere($reviewissueqzQuery)->fi()
            ->beginIF($browseType == 'myCreated')->andWhere('createBy')->eq($currentUser)->fi()
            ->beginIF(($browseType != 'all') && ($browseType != 'bySearch') && ($browseType != 'myCreated'))->andWhere('status')->eq($browseType)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        if($data){
            $reviewIds = array_column($data, 'reviewId');
            $reviewIds = array_flip(array_flip($reviewIds));
            $reviewList = $this->loadModel('reviewqz')->getListByIds($reviewIds, 'id,title');
            if($reviewList){
                $reviewList = array_column($reviewList, null, 'id');
            }
            foreach ($data as $key => $val){
                $reviewId = $val->reviewId;
                $reviewInfo = zget($reviewList, $reviewId, new stdClass());
                $val->reviewTitle = zget($reviewInfo, 'title', '');
            }
        }
        return $data;
    }

    /**
     * Get information by id.
     *
     * @param  int   $issueID
     * @return array
     */
    public function getByID($issueID){
        $issue = $this->dao->select('t1.*, t2.id as reviewID, t2.title as reviewTitle,t2.status as reviewStatus, t2.createBy as reviewCreatedBy')
            ->from(TABLE_REVIEWISSUEQZ)->alias('t1')
            ->leftJoin(TABLE_REVIEWQZ)->alias('t2')
            ->on('t1.reviewId=t2.id')
            ->where('t1.id')->eq($issueID)
            ->andWhere('t1.deleted')->eq(0)
            ->fetch();

        $issue = $this->loadModel('file')->replaceImgURL($issue, 'desc');
        return $issue;
    }


    /**
     *清总评审问题列表
     *
     * @param $col
     * @param $issue
     * @param $reviewID
     * @param $users
     * @param $status
     * @param $orderBy
     * @param $pager
     */
    public function printCell($col, $issue,$reviewID, $users,$status,$orderBy,$pager)
    {
        $id = $col->id;
        $params = "issudID=$issue->id"."&reviewId=$reviewID"."&statusNew=$status"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        if($col->show)
        {
            $class = "c-$id";
            $title  = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->title}'";
            }
            if($id == 'reviewId')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->reviewTitle}'";
            }
            echo "<td class='" . $class . "' $title>";
            switch($id) {
                case 'id':
                    echo $issue->id;
                    break;
                case 'reviewId':
                    echo html::a(helper::createLink('reviewqz', 'view', "reviewID=$issue->reviewId"),'<div class="reviewTitle" title="' . $issue->reviewTitle . '">' . $issue->reviewTitle .'</div>');
                    break;

                case 'title':
                    echo html::a(helper::createLink('reviewissueqz', 'view', $params),'<div class="problemTitle" title="' . $issue->title . '">' . $issue->title .'</div>');
                    break;
                case 'desc':
                    echo '<div class="change" title="' . strip_tags($issue->desc) . '">' . $issue->desc .'</div>';
                    break;

                case 'raiseBy':
                    echo zget($users, $issue->raiseBy);
                    break;
                case 'raiseDate':
                    echo $issue->raiseDate;
                    break;
                case 'status':
                    echo zget($this->lang->reviewissueqz->statusLabelList, $issue->status);
                    break;

                case 'dealUser':
                    echo zget($users, $issue->dealUser);
                    break;

                case 'actions':
                    $recTotal = $pager->recTotal;
                    $recPerPage = $pager->recPerPage;
                    $pageID = $pager->pageID;
                    $param = "issueID=$issue->id&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
                    common::hasPriv('reviewissueqz','edit') ? common::printIcon('reviewissueqz', 'edit', $param, $issue, 'list','','','','','', $this->lang->reviewissueqz->edit) : '';
                    common::hasPriv('reviewissueqz','delete') ? common::printIcon('reviewissueqz', 'delete',$param , $issue, 'list', 'trash', '', 'iframe', true,'', $this->lang->reviewissueqz->delete) : '';
            }

            echo '</td>';
        }

    }


    /**
     * Judge button if can clickable.
     *
     * @param $issue
     * @param $action
     * @return bool
     */
    public static function isClickable($issue, $action){
        $res = array(
            'result'  => true,
        );

        global $app;
        $action = strtolower($action);
        $reviewissueqzModel = new reviewissueqzModel();

        if($action == 'edit') { //编辑
            $res = $reviewissueqzModel->checkIsAllowEdit($issue, $app->user->account);
        }

        if($action == 'delete'){//删除
            $res = $reviewissueqzModel->checkIsAllowDelete($issue, $app->user->account);
        }
        return $res['result'];
    }

    //新建问题
    public function create($projectID)
    {
        $data = fixer::input('post')
            ->add('status', 'created')
            ->add('createBy', $this->app->user->account)
            ->add('createTime', date('Y-m-d H:i:s'))
            ->add('dealUser', $this->app->user->account)
            ->remove('uid')
            ->stripTags($this->config->reviewissueqz->editor->create['id'], $this->config->allowedTags)
            ->get();

        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewissueqz->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_REVIEWISSUEQZ)->data($data)
            ->batchCheck($this->config->reviewissueqz->create->requiredFields, 'notempty')
            ->autoCheck()
            ->exec();

        if(!dao::isError())
        {
            $issueID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $issueID, 'reviewissueqz');
            return $issueID;
        }
        return false;
    }

    //批量新建问题数据
    public function batchCreate($projectID)
    {
        $data = fixer::input('post')->get();
        $this->app->loadClass('purifier', true);
        $addDataTips= array();$addData= array();

        //第1行必须创建
        if(empty($data->title[0])){
            die(js::alert($this->lang->reviewissueqz->emptyData,true));
        }else{
            //只填写文件名/位置，不填写判断
            foreach($data->title as $key => $value)
            {
                if(!empty($value)){
                    $titleData = $this->reviewData($data,$key);
                    if(!empty($titleData['line']))
                        $addDataTips[$titleData['line']] = $titleData['reviewData'];
                }
                //构造数据
                $titleData = $this->reviewData($data,$key);
                $addData[] = $titleData['reviewData'];
            }
            //只填写描述，不填写文件名/位置判断
            foreach ($data->desc as $k=>$v)
            {
                if(!empty($v)){
                    $descData = $this->reviewData($data,$k);
                    if(!empty($descData['line']))
                        $addDataTips[$descData['line']] = $descData['reviewData'];
                }
            }
        }

        //去除中间未填写项数据，只保存有效数据
        foreach ($addData as $i=>$item){
            if(empty($item->title)){
                unset($addData[$i]);
            }
        }

        ksort($addDataTips);
        if(!empty($addDataTips)) {
            foreach ($addDataTips as $item => $dataValue) {
                $requiredFields = explode(',', $this->config->reviewissueqz->beatchCreate->requiredFields);

                foreach ($requiredFields as $requiredField) {
                    $requiredField = trim($requiredField);
                    if (empty($dataValue->$requiredField)) {
                        dao::$errors[] = sprintf($this->lang->reviewissueqz->noRequire, $item, $this->lang->reviewissueqz->$requiredField);
                    }
                }
            }
        }
        if(dao::isError()) die(js::error(dao::getError()));
        foreach ($addData as $insertData){
            $this->dao->insert(TABLE_REVIEWISSUEQZ)->data($insertData)->exec();
            if(!dao::isError())
            {
                $reviewIssueId = $this->dao->lastInsertID();
                $this->loadModel('action')->create('reviewissueqz', $reviewIssueId, 'Created');
            }
            if(dao::isError()) die(js::error(dao::getError()));
        }
    }

    //数据判断
    public function reviewData($data,$i)
    {
        $reviewData = new stdClass();
        $line = [];
        $review = $data->reviewId[$i];

        $reviewInfo = $this->loadModel('reviewqz')->getReviewById($review);
        $reviewData->reviewId      = $review;
        $reviewData->status        = 'created';
        $reviewData->title         = $data->title[$i];
        $reviewData->desc          = $data->desc[$i];
        $reviewData->createBy      = $this->app->user->account;
        $reviewData->createTime    = date('Y-m-d H:i:s');
        $reviewData->raiseBy       = $data->raiseBy[$i];
        $reviewData->raiseDate     = $data->raiseDate[$i];
        $reviewData->dealUser      = $this->app->user->account;

        if(isset($this->config->reviewissueqz->beatchCreate->requiredFields))
        {
            $requiredFields = explode(',', $this->config->reviewissueqz->beatchCreate->requiredFields);
            foreach($requiredFields as $requiredField)
            {
                $requiredField = trim($requiredField);
                if(empty($reviewData->$requiredField)){
                    $line = $i+1;
                }
            }
        }
        $returnData = [
            'line'=>$line,
            'reviewData'=>$reviewData
        ];
        return $returnData;
    }

    /**
     * 编辑操作
     *
     * @param $issueId
     * @return array|bool
     */
    function update($issueId){
        $issueInfo = $this->getByID($issueId);
        //检查是否许删除
        $res = $this->checkIsAllowEdit($issueInfo, $this->app->user->account);
        if(!$res['result']){
            dao::$errors[''] = $res['message'];
            return false;
        }
        $currentTime = helper::now();
        $data = fixer::input('post')
            ->add('editBy', $this->app->user->account)
            ->add('editTime', $currentTime)
            ->remove('uid')
            ->stripTags($this->config->reviewissueqz->editor->edit['id'], $this->config->allowedTags)
            ->get();
        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewissueqz->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_REVIEWISSUEQZ)->data($data)
            ->autoCheck()
            ->where('id')->eq($issueId)->batchCheck($this->config->reviewissueqz->edit->requiredFields, 'notempty')->autoCheck()
            ->exec();
        if(!dao::isError()) {
            $this->file->updateObjectID($this->post->uid, $issueId, $this->lang->reviewissueqz->objectType);
            return common::createChanges($issueInfo, $data); //返回修改信息
        }
        return false;

    }

    /**
     *删除操作
     *
     * @param string $issueId
     * @return bool|void
     */
    public function deleteOp($issueId){
        $issueInfo = $this->getByID($issueId);
        //检查是否许删除
        $res = $this->checkIsAllowDelete($issueInfo, $this->app->user->account);
        if(!$res['result']){
            dao::$errors[''] = $res['message'];
            return false;
        }
        //获得数据
        $data = fixer::input('post')
            ->add('deleted', '1') //删除
            ->remove('uid')
            ->stripTags($this->config->reviewissueqz->editor->delete['id'], $this->config->allowedTags)
            ->get();

        $data = $this->loadModel('file')->processImgURL($data, $this->config->reviewissueqz->editor->delete['id'], $this->post->uid);

        //删除操作
        $this->dao->update(TABLE_REVIEWISSUEQZ)->data($data)
            ->autoCheck()
            ->where('id')->eq($issueId)->exec();
        if(dao::isError()) {
            return false;
        }

        $this->file->updateObjectID($this->post->uid, $issueId, $this->lang->reviewissueqz->objectType);
        return true;
    }

    /**
     *检查是否允许删除
     *
     * @param $issueInfo
     * @param $userAccount
     * @return array
     */
    public function checkIsAllowDelete($issueInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );

        if(!$issueInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $issueInfo->status;
        $allowStatusList = $this->lang->reviewissueqz->allowDeleteStatusList;
        if(!in_array($status, $allowStatusList)){
            $statusDesc = zget($this->lang->reviewissueqz->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->reviewissueqz->checkResultList['statusError'], $statusDesc, $this->lang->reviewissueqz->delete);
            return $res;
        }

        //admin用户暂时也存入到待处理用户中
        $dealUsers  = ['admin', $issueInfo->createBy, $issueInfo->raiseBy];
        if(!in_array($userAccount, $dealUsers)){
            $res['message'] = sprintf($this->lang->reviewissueqz->checkResultList['userError'], $this->lang->reviewissueqz->delete);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }


    /**
     *检查是否允许编辑
     *
     * @param $issueInfo
     * @param $userAccount
     * @return array
     */
    public function checkIsAllowEdit($issueInfo, $userAccount){
        $res = array(
            'result'  => false,
            'message' => '',
        );

        if(!$issueInfo){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //当前状态
        $status = $issueInfo->status;
        $allowStatusList = $this->lang->reviewissueqz->allowEditStatusList;
        if(!in_array($status, $allowStatusList)){
            $statusDesc = zget($this->lang->reviewissueqz->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->reviewissueqz->checkResultList['statusError'], $statusDesc, $this->lang->reviewissueqz->edit);
            return $res;
        }

        //admin用户暂时也存入到待处理用户中
        $dealUsers  = ['admin', $issueInfo->createBy,  $issueInfo->raiseBy];
        if(!in_array($userAccount, $dealUsers)){
            $res['message'] = sprintf($this->lang->reviewissueqz->checkResultList['userError'], $this->lang->reviewissueqz->edit);
            return $res;
        }
        $res['result'] = true;
        return $res;
    }

    // 清总变更召开会议时间邮件
    public function sendmail($info, $newDate)
    {
        $this->loadModel('mail');
        $users  = $this->loadModel('user')->getPairs('noletter');
        $toList = '';$expertStr = '';
        $reviewerList = $this->loadModel('reviewqz')->getPlanExportsList($info->id, $info->version);
        foreach($reviewerList as $value){
            $toList .= $value->reviewer.',';
            $expertStr .= $users[$value->reviewer].',';
        }
        $toList = substr($toList,0,-1);
        $expertStr = substr($expertStr,0,-1);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setReviewIssueQzMail) ? $this->config->global->setReviewIssueQzMail : '{"mailTitle":"","variables":[],"mailContent":""}';

        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get actions. */
//        $action  = $this->loadModel('action')->getById($actionID);
//        $history = $this->action->getHistory($actionID);
//        $action->history    = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'reviewissueqz');
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
        $this->mail->send($toList, $mailTitle, $mailContent, '');
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    // 喧喧消息
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info = $this->getByID($objectID);
        $toList = '';
        $reviewerList = $this->loadModel('reviewqz')->getPlanExportsList($info->id, $info->version);
        foreach($reviewerList as $value){
            $toList .= $value->reviewer.',';
        }
        $toList = substr($toList,0,-1);

        $url = '';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         = '';//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }
}
