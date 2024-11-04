<?php
/**
 * The control file of reviewqz module of ZenTaoPMS.
 *
 * Created by PhpStorm.
 * User: t_wangjiurong
 * Date: 2023/2/20
 * Time: 9:43
 */
class reviewqz extends control{
    /**
     * 清总评审列表
     */
    public function browse($browseType = 'all',$param = 0,$orderBy = 'id_desc', $recTotal = 0, $recPerPage = 15, $pageID = 1){
        $this->loadModel('datatable');
        $users = $this->loadModel('user')->getPairs('noletter');
        //去重复的状态列表
        $browseStatusList = array_unique($this->lang->reviewqz->browseStatus);

        //按照状态搜索
        $tempStatusList = ['' => ''];
        $tempStatusList = array_merge($tempStatusList, $browseStatusList);
        unset($tempStatusList['all']);
        //所有重新赋值
        $this->config->reviewqz->search['params']['status']['values']  = $tempStatusList;

        $this->config->reviewqz->search['params']['applicant']['values']  =  $users;
        $this->config->reviewqz->search['params']['dealUser']['values']  =  $users;

        /* By search. */
        $queryID = ($browseType == 'bySearch')  ? (int)$param : 0;
        $actionURL = $this->createLink('reviewqz', 'browse', "browseType=bySearch&param=myQueryID");
        $this->reviewqz->buildSearchForm($queryID, $actionURL);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        $this->view->title      = $this->lang->reviewqz->browse ;
        $this->view->position[] = $this->lang->reviewqz->browse;
        $this->view->reviewList = $this->reviewqz->reviewList($browseType, $queryID, $orderBy, $pager);
        $this->view->recTotal   = $recTotal;
        $this->view->recPerPage = $recPerPage;
        $this->view->pageID     = $pageID;
        $this->view->orderBy    = $orderBy;
        $this->view->browseType = $browseType;
        $this->view->pager      = $pager;
        $this->view->users      = $users;
        $this->view->queryID    = $queryID;
        $this->view->browseStatusList = $browseStatusList;
        $this->session->set('reviewqzList', $this->app->getURI(true), 'backlog');
        $this->display();
    }

    /**
     *清总评审详情
     *
     * @param $reviewId
     */
    public function view($reviewId){
        $this->app->loadLang('reviewissueqz');
        $objectType = $this->lang->reviewqz->objectType;
        //评审信息
        $review = $this->reviewqz->getByID($reviewId);
        //拟参会专家
        $planExportsList = $this->reviewqz->getPlanExportsList($reviewId, $review->version);
        //专家反馈信息
        $exportsFeedbackList = $this->reviewqz->getExportsFeedbackList($reviewId, $review->num);
        //专家处理意见
        $exportsReviewResultList = $this->reviewqz->getExportsReviewResultList($reviewId, $review->version);

        $actions = $this->loadModel('action')->getList($objectType, $reviewId);
        $users   = $this->loadModel('user')->getPairs('noletter');
        $this->view->title = $this->lang->reviewqz->view;
        $this->view->position[] = $this->lang->reviewqz->view;
        $this->view->review = $review;
        $this->view->planExportsList         = $planExportsList;
        $this->view->exportsFeedbackList     = $exportsFeedbackList;
        $this->view->exportsReviewResultList = $exportsReviewResultList;
        $this->view->actions = $actions; //日志信息
        $this->view->users = $users;
        $this->view->objectType = $objectType;
        $isAllowSubmit = $this->reviewqz->checkIsAllowSubmit($review, $this->app->user->account);
        $this->view->isAllowSubmit = $isAllowSubmit;
        $this->display();
    }

    //清总评审接口人指派专家
    public function assignExports($id){
        if($_POST){
            $logChanges = $this->reviewqz->assignExports( $id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('reviewqz', $id, '指派专家', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $expertStr = '';
        $users       = $this->loadModel('user')->getPairs('noletter');
        $expertList  = $this->dao->select('planJinkeExports')->from(TABLE_REVIEWQZ)
            ->where('id')->eq($id)
            ->fetch();
        $planJinkeExports = $expertList->planJinkeExports;
        if(strrpos($planJinkeExports,',')){
            $expertArr = explode(',',$planJinkeExports);
        }elseif(strrpos($planJinkeExports,'，')){
            $expertArr = explode('，',$planJinkeExports);
        }elseif(strrpos($planJinkeExports,'、')){
            $expertArr = explode('、',$planJinkeExports);
        }
        if(!empty($expertArr)){
            $names = array_flip($users);
            foreach($expertArr as $name){
                if($names[$name]){
                    $expertStr .= $names[$name].',';
                }
            }
            $expertStr = substr($expertStr,0,-1);
        }
        $this->view->expertStr  = $expertStr;
        $this->view->expertList = $planJinkeExports;
        $this->view->users      = $users;
        $this->display();
    }

    // 专家确认
    public function confirm($id){
        $info = $this->reviewqz->getReviewById($id);
        if($_POST){
            $logChanges = $this->reviewqz->confirm($info, $id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('reviewqz', $id, '专家确认', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $users       = $this->loadModel('user')->getPairs('noletter');
        $node = $this->reviewqz->findNode($id, $info->version, 'expertIsJoinReview');
        $expertList  = $this->dao->select('reviewer,status')->from(TABLE_REVIEWER)
            ->where('node')->eq($node)
            ->fetchAll();
        foreach($expertList as $account){
            $account->name = $users[$account->reviewer];
            unset($users[$account->reviewer]);
        }
        // 根据是否超时判断接口人是否可操作 1 已超时 2 未超时
        $liasisonOfficer = $this->config->reviewqz->liasisonOfficer;
        $show = $info->overtime == '1' && (in_array($this->app->user->account, explode(',',$liasisonOfficer))) ? '1':'2';

        $this->view->expertList = $expertList;
        $this->view->users      = $users;
        $this->view->show       = $show;
        $this->display();
    }

    // 反馈专家名单
    public function feedback($id){
        $info = $this->reviewqz->getReviewById($id);
        if($_POST){
            $data = fixer::input('post')->get();
            $logChanges = $this->reviewqz->feedbackQzExperts($id, $data, 'feedback');
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }

            $actionID = $this->loadModel('action')->create('reviewqz', $id, '反馈清总专家名单', $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $users       = $this->loadModel('user')->getPairs('noletter');
        $node = $this->reviewqz->findNode($id, $info->version, 'expertIsJoinReview');

        $expertList  = $this->dao->select('reviewer,status')->from(TABLE_REVIEWER)
            ->where('node')->eq($node)
            ->fetchAll();
        foreach($expertList as $account){
            $account->name = $users[$account->reviewer];
            unset($users[$account->reviewer]);
        }

        $this->view->expertList = $expertList;
        $this->view->users      = $users;
        $this->display();
    }

    // 金科变更参会专家
    public function change($id){
        $info = $this->reviewqz->getReviewById($id);
        $node = $this->reviewqz->findNode($id, $info->version, 'expertIsJoinReview');
        $users       = $this->loadModel('user')->getPairs('noletter');
        $expertList  = $this->dao->select('reviewer,status')->from(TABLE_REVIEWER)
            ->where('node')->eq($node)
            ->fetchAll();
        if($_POST){
            $logChanges = $this->reviewqz->change($info, $expertList);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            if($info->status == 'reviewPass'){
                $action = '变更申请';
            }else{
                $action = '审批打回处理';
            }
            $actionID = $this->loadModel('action')->create('reviewqz', $id, $action, $this->post->comment);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }

        if($info->status != 'reviewPass' && $info->status != 'reviewRefuse'){
            //错误提示
            $res['message'] = '该评审当前状态不支持变更。';
            return $res;
        }

        foreach($expertList as $account){
            $account->name = $users[$account->reviewer];
            unset($users[$account->reviewer]);
        }

        $this->view->expertList = $expertList;
        $this->view->info       = $info;
        $this->view->users      = $users;
        $this->display();
    }

    // 获取选择标题下拉框
    public function ajaxGetReviewqz($browseType)
    {
        echo $this->reviewqz->getReviewqzTitle($browseType);
    }

    // 专家评审
    public function submit($id){
        if($_POST){
            $logChanges = $this->reviewqz->submit($id);
            if(dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                $this->send($response);
            }
            $actionID = $this->loadModel('action')->create('reviewqz', $id, '专家评审', $this->lang->reviewqz->reviewResultList[$this->post->reviewResult]);
            if($logChanges) {
                $this->action->logHistory($actionID, $logChanges);
            }

            $response['result']  = 'success';
            $response['message'] = $this->lang->saveSuccess;
            $response['locate']  = 'parent';
            $this->send($response);
        }
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    // 定时更新未确认参会专家待处理人为接口人
    public function updateDealUser(){
        $tomorrow = helper::tomorrow();
        $list = $this->dao->select('id')->from(TABLE_REVIEWQZ)->where('confirmJoinDeadLine')->like("$tomorrow%")->andWhere('status')->in($this->lang->reviewqz->allowConfirmStatusList)->fetchAll('id');
        if(!empty($list)){
            $userStr = $this->config->reviewqz->liasisonOfficer;
            foreach($list as $id => $val){
                $oldData = $this->reviewqz->getReviewById($id);
                // 新增下一节点
                $this->reviewqz->changeNode('waitFeedbackQz', explode(',',$userStr), $id, $oldData->version, 'expertIsJoinReview');
                //状态流转
                $this->loadModel('consumed')->record('reviewqz', $id, 0, $this->app->user->account, 'expertConfirm', 'waitFeedbackQz');

                //修改数据
                $newData = new stdClass();
                $newData->status    = 'waitFeedbackQz';
                $newData->dealUser  = $userStr;
                $newData->overtime  = '1';
                $this->dao->update(TABLE_REVIEWQZ)->data($newData)->where('id')->eq($id)->exec();

                //获得修改信息
                $logChanges = common::createChanges($oldData, $newData);
                $actionID = $this->loadModel('action')->create('reviewqz', $id, '流程自动流转为待反馈专家');
                if($logChanges) {
                    $this->action->logHistory($actionID, $logChanges);
                }
            }
        }
    }

    /**
     * 定时给专家发邮件催办是否参会
     */
    public function mailExpertIsJoinMeeting()
    {
        $ret = $this->loadModel('reviewqz')->mailExpertIsJoinMeeting(); //正常调用模块及方法
        echo '处理了'.$ret . '条';
    }

    /**
     * 定时给评审接口人邮件催办参会专家推送到清总
     */
    public function mailFeedbackQz()
    {
        $ret = $this->loadModel('reviewqz')->mailFeedbackQz(); //正常调用模块及方法
        echo '处理了'.$ret . '条';
    }

    /**
     * 自动反馈清总参会专家
     */
    public function autoFeedbackQz()
    {
        $ret = $this->loadModel('reviewqz')->autoFeedbackQz(); //正常调用模块及方法
        echo '处理了'.$ret . '条';
    }

    /**
     * 自动设置不参会
     */
    public function autoSetNotJoinMeeting(){
        $ret = $this->loadModel('reviewqz')->autoSetNotJoinMeeting(); //正常调用模块及方法
        echo '处理了'.$ret . '条';
    }
}
