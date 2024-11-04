<?php
class cmdbsyncModel extends model
{
    public function buildSearchForm($queryID, $actionURL){
        $this->config->cmdbsync->search['actionURL'] = $actionURL;
        $this->config->cmdbsync->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->cmdbsync->search);
    }

    /**
     * 获得查询列表
     *
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return array
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null){
        $data = [];
        $cmdbsyncQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('cmdbsyncQuery', $query->sql);
                $this->session->set('cmdbsyncForm', $query->form);
            }
            if ($this->session->cmdbsyncQuery == false) $this->session->set('cmdbsyncQuery', ' 1 = 1');

            $cmdbsyncQuery = $this->session->cmdbsyncQuery;
        }
        $account = $this->app->user->account;
        //查询列表
        $ret = $this->dao->select('*')
            ->from(TABLE_CMDBSYNC)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' && $browseType != 'bysearch'   &&  $browseType != 'tomedeal')
            ->andWhere('status')->eq($browseType)
            ->fi()
            ->beginIF($browseType == 'tomedeal')
            ->andWhere("FIND_IN_SET('{$account}', dealUser)")
            ->fi()
            ->beginIF($browseType == 'bysearch')
            ->andWhere($cmdbsyncQuery)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'cmdbsync', $browseType != 'bysearch');
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 处理
     * @param $id
     * @return void
     */
    public function deal($id){
        $data = fixer::input('post')
            ->get();
        if(empty($data->result)){
            return dao::$errors['result'] = sprintf($this->lang->cmdbsync->emptyObject, $this->lang->cmdbsync->result);
        }
        if($data->result == 'pass' && empty($data->isAuto)){
            return dao::$errors['isAuto'] = sprintf($this->lang->cmdbsync->emptyObject, $this->lang->cmdbsync->isAuto);
        }

        $cmdbsyncInfo = $this->getByID($id);
        $updateInfo = array();
        $updateInfo['result'] = $data->result;
        if($data->result == 'pass'){
            $updateInfo['isAuto'] = $data->isAuto;
        }
        $updateInfo['comment'] = $data->comment;
        $updateInfo['status'] = $data->result;
        $updateInfo['dealUser'] = '';
        $updateInfo['sendStatus'] = 'success';
        $this->dao->begin();  //开启事务
        $this->dao->update(TABLE_CMDBSYNC)->data($updateInfo)->where('id')->eq($id)->exec();
        $this->tryError(1);
        $pushData = array();
        $pushData['id'] = $id;
        $putproductionInfo = $this->dao->select("*")
            ->from(TABLE_PUTPRODUCTION)
            ->where('id')->eq($cmdbsyncInfo->putproductionId)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        $pushData['numbers'] = $putproductionInfo->code;
        $pushData['bAgree'] = zget($this->lang->cmdbsync->externalResultList, $data->result);
        $pushData['auditOpinion'] = $data->comment;
        $productionSystemConfirmKeysReqList = array();
        if($data->result == 'pass'){
            if($data->isAuto == 'auto'){
                //更新系统台账
                $info = json_decode($cmdbsyncInfo->info);
                if(!empty($info->addInfo)){
                    $addInfo = $info->addInfo;
                    foreach ($addInfo as $add){
                        $existApplication = $this->dao->select('*')->from(TABLE_APPLICATION)
                            ->where('deleted')->eq(0)
                            ->andWhere("(name = '".$add->name."' or code = '".$add->code."')")
                            ->orderBy('id_desc')
                            ->fetchAll('id');
                        if(!empty($existApplication)){
                            dao::$errors[''] = $this->lang->cmdbsync->existApplication;
                            $this->tryError(1);
                        }
                        $add->id = '';
                        $add->isPayment = '2';
                        $add->createType = '2';
                        $this->dao->insert(TABLE_APPLICATION)
                            ->data($add)
                            ->exec();
                        $lastInsertID = $this->dao->lastInsertID();
                        $this->loadModel('action')->create('application', $lastInsertID, 'synccreated', '由CMDB同步单'.$cmdbsyncInfo->id.'自动创建','',$this->app->user->account);
                        $confirmKey = new stdClass();
                        $confirmKey->cfitKey = $lastInsertID;
                        $confirmKey->cfidKey = $add->cfidKey;
                        array_push($productionSystemConfirmKeysReqList, $confirmKey);
                    }
                }
                if(!empty($info->deleteInfo)){
                    $deleteInfo = $info->deleteInfo;
                    foreach ($deleteInfo as $delete){
                        $this->dao->update(TABLE_APPLICATION)
                            ->set('deleted')->eq('1')->where('id')->in($delete->id)->exec();
                        $this->loadModel('action')->create('application', $delete->id, 'syncdeleted', '由CMDB同步单'.$cmdbsyncInfo->id.'自动删除','',$this->app->user->account);
                    }
                }
                if(!empty($info->updateInfo)){
                    $updateAppInfo = $info->updateInfo;
                    foreach ($updateAppInfo as $id=>$updateApp){
                        $appId = '';
                        $updateAppArray = array();
                        foreach ($updateApp as $key => $app){
                            if($app->isColumnDiffer){
                                $updateAppArray[$key] = $app->new;
                            }else if($key == 'id'){
                                $appId = $app->new;
                            }
                        }
                        if(!empty($updateAppArray)){
                            $applicationOld = $this->dao->findByID($appId)->from(TABLE_APPLICATION)->fetch();
                            $this->dao->update(TABLE_APPLICATION)
                                ->data($updateAppArray)->where('id')->eq($appId)->exec();
                            $actionID = $this->loadModel('action')->create('application', $appId, 'syncupdate', '由CMDB同步单'.$cmdbsyncInfo->id.'自动修改','',$this->app->user->account);
                            $changes = common::createChanges($applicationOld, $updateAppArray);
                            $this->action->logHistory($actionID, $changes);
                        }
                    }
                }
            }else{
                $info = json_decode($cmdbsyncInfo->info);
                if(!empty($info->addInfo)){
                    $addInfo = $info->addInfo;
                    foreach ($addInfo as $add){
                        $applicationNew = $this->dao->select('*')->from(TABLE_APPLICATION)->where('cfidKey')->eq($add->cfidKey)->fetch();
                        $confirmKey = new stdClass();
                        $confirmKey->cfitKey = $applicationNew->id;
                        $confirmKey->cfidKey = $add->cfidKey;
                        array_push($productionSystemConfirmKeysReqList, $confirmKey);
                    }
                }
            }
        }
        $this->tryError(1);
        $this->loadModel('action')->create('cmdbsync', $id, 'deal', $this->lang->cmdbsync->result.'：'.zget($this->lang->cmdbsync->resultList,$data->result).'<br>'.$this->lang->cmdbsync->comment.'：'.$data->comment.'<br>'.$this->lang->cmdbsync->isAuto.'：'.zget($this->lang->cmdbsync->isAutoList,$data->isAuto),'',$this->app->user->account);
        $this->tryError(1);
        $pushData['productionSystemConfirmKeysReqList'] = $productionSystemConfirmKeysReqList;
        $requestClass = $this->pushFeedback($pushData);
        $this->tryErrorRequest(1, $requestClass);
        //保存发送日志
        $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
            $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
        $this->dao->commit(); //提交事务
    }

    /**
     * 处理之后同步接口
     * @param $externalCode
     * @return mixed
     */
    public function pushFeedback($pushData){
        $this->loadModel('requestlog');

        $pushEnable = $this->config->global->pushCmdbAppEnable;
        $requestClass = new stdClass();
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->pushCmdbDealUrl;
            $pushAppId = $this->config->global->pushCmdbAppId;
            $pushAppSecret = $this->config->global->pushCmdbAppSecret;
            //请求头
            $headers = array();
            $headers[] = 'appId: ' . $pushAppId;
            //$headers[] = 'appSecret: ' . $pushAppSecret;
            $ts = time();
            $headers[] = 'ts: ' . $ts;
            $uuid = $this->create_guid();
            $headers[] = 'nonce: ' . $uuid;
            $sign = md5('appId='.$pushAppId.'&nonce='.$uuid.'&ts='.$ts.'&appSecret='.$pushAppSecret);
            $headers[] = 'sign: ' . $sign;
            $pushDataArray = array();
            //投产单号
            $pushDataArray['numbers']               = $pushData['numbers'];
            //是否确认通过
            $pushDataArray['agree']               = $pushData['bAgree'];
            //反馈信息
            $pushDataArray['auditOpinion']               = $pushData['auditOpinion'];
            //系统信息
            $pushDataArray['productionSystemConfirmKeysReqList']               = $pushData['productionSystemConfirmKeysReqList'];

            //请求类型
            $object = 'cmdbsync';
            $objectType = 'pushCmdbDeal';
            $method = 'POST';

            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushDataArray, $method, 'json', array(), $headers);
            //若清总未返回结果或结果失败，就报错
            if (!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->status == 'success') {
                    $status = 'success';
                }
                $response = $result;
            } else {
                $response = '对方无响应';
            }
            $requestClass->url = $url;
            $requestClass->object = $object;
            $requestClass->objectType = $objectType;
            $requestClass->method = $method;
            $requestClass->pushData = $pushDataArray;
            $requestClass->response = $response;
            $requestClass->status = $status;
            $requestClass->extra = $extra;
            $requestClass->id = $pushData['id'];
            if (empty($result) or $resultData->status != 'success') {
                dao::$errors[] = $this->lang->cmdbsync->syncFail ;
            }
        }else{
            /*dao::$errors[] = $this->lang->cmdbsync->enableFail;*/
        }
        return $requestClass;
    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if (dao::isError()) {
            if ($rollBack == 1) {
                $this->dao->rollBack();
            }
            $response['result'] = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    public function tryErrorRequest($rollBack = 0, $requestClass)
    {
        if (dao::isError()) {
            if ($rollBack == 1) {
                $this->dao->rollBack();
            }
            $response['result'] = 'fail';
            $response['message'] = dao::getError();
            //保存发送日志
            $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
            $this->send($response);
        }
    }

    /**
     * 直接输出data数据
     * @access public
     */
    private function send($data)
    {
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取单条信息
     * @param $id
     * @return stdClass
     */
    public function getByID($id){
        $data = new  stdClass();
        $info = $this->dao->select("*")
            ->from(TABLE_CMDBSYNC)
            ->where('id')->eq($id)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if(!$info){
            return $data;
        }
        return $info;
    }

    public static function isClickable($cmdbsync, $action)
    {
        global $app;
        $action = strtolower($action);
        switch (strtolower($action)){
            case 'deal': //处理
                $statusList = ['toconfirm'];
                $dealUser = explode(',', trim($cmdbsync->dealUser, ','));
                return in_array($cmdbsync->status,$statusList) and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin');
            case 'repush': //处理
                $dealUser = explode(',', trim($cmdbsync->dealUser, ','));
                return $cmdbsync->sendStatus == 'fail' and (in_array($app->user->account, $dealUser) || $app->user->account == 'admin');
            default:
                return true;
        }
    }

    /**
     * sendmail
     *
     * @param  int    $putproductionID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($cmdbsyncId, $actionID)
    {
        $this->loadModel('mail');
        $cmdbsync   = $this->getById($cmdbsyncId);
        $users = $this->loadModel('user')->getPairs('noletter');

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        /*$mailConf   = isset($this->config->global->setPutproductionMail) ? $this->config->global->setPutproductionMail : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);*/
        $appList = $this->loadModel('application')->getapplicationNameCodePairs();
        $cmdbsync->app = zmget($appList, $cmdbsync->app, '');
        if($cmdbsync->type == 'putproduction'){
            $mailTitle  = $this->lang->cmdbsync->noticeTitle;
        }else{
            $mailTitle  = $this->lang->cmdbsync->noticeCmdbTitle;
        }
        $mailContent = $this->lang->cmdbsync->mailContent;


        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $modulePath = $this->app->getModulePath($appName = '', 'cmdbsync');
        $oldcwd     = getcwd();
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
        foreach ($this->lang->cmdbsync->apiDealUserList as $key => $value) {
            if(!empty($key)){
                $userIds[] = $key;
            }
        }
        $toList = implode(',',$userIds);
        if(!$toList) return;
        $subject = $mailTitle;
        /* Send mail. */
        $this->mail->send($toList, $subject, $mailContent, '');
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * @Notes:喧喧
     * @Date: 2024/1/10
     * @Time: 17:53
     * @Interface getXuanxuanTargetUser
     * @param $obj
     * @param $objectType
     * @param $objectID
     * @param $actionType
     * @param $actionID
     * @param string $actor
     * @return array|false
     */
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info   = $this->getById($objectID);

        foreach ($this->lang->cmdbsync->apiDealUserList as $key => $value) {
            if(!empty($key)){
                $userIds[] = $key;
            }
        }
        $toList = implode(',',$userIds);
        if(!$toList) return;

        $server   = $this->loadModel('im')->getServer('zentao');
        $url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html');
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';
        $subcontent['count']        = 0;
        $subcontent['id']           = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']         = $obj->id;//消息体 编号后边位置 标题

        //标题
        $title = '';
        $actions = [];

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions];
    }

    public function create_guid($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['LOCAL_ADDR'];
        $data .= $_SERVER['LOCAL_PORT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);
        return $guid;
    }

    /**
     * 将无差异的单子自动推送
     * @return array|false
     */
    public function getUnPushedAndPush(){
        $unPushed = $this->dao->select('*')->from(TABLE_CMDBSYNC)->where('status')->eq('pass')->andWhere('sendStatus')->eq('tosend')->andWhere('type')->eq('putproduction')->andWhere('isDiffer')->ne('1')->fetchALl('id');
        if(empty($unPushed)) return [];
        $res = [];
        foreach ($unPushed as $unPushedObject)
        {
            $pushData = array();
            $pushData['id'] = $unPushedObject->id;
            $putproductionInfo = $this->dao->select("*")
                ->from(TABLE_PUTPRODUCTION)
                ->where('id')->eq($unPushedObject->putproductionId)
                ->andWhere('deleted')->eq('0')
                ->fetch();
            $pushData['numbers'] = $putproductionInfo->code;
            $pushData['bAgree'] = zget($this->lang->cmdbsync->externalResultList, 'pass');
            $requestClass = $this->pushFeedback($pushData);
            $updateInfo = array();
            if (dao::isError()) {
                $response['result'] = 'fail';
                $response['message'] = dao::getError();
                if($unPushedObject->failNum+1 >= 5){
                    $updateInfo['sendStatus'] = 'fail';
                    foreach ($this->lang->cmdbsync->reSendUserList as $key => $value) {
                        if(!empty($key)){
                            $userIds[] = $key;
                        }
                    }
                    $updateInfo['dealUser'] = implode(',',$userIds);
                }
                $updateInfo['failNum'] = $unPushedObject->failNum+1;
                $this->loadModel('action')->create('cmdbsync',$unPushedObject->id, 'jxsyncfail', '');
            }else{
                $updateInfo['sendStatus'] = 'success';
                $this->loadModel('action')->create('cmdbsync',$unPushedObject->id, 'jxsyncsuccess', '');
            }
            //保存发送日志
            $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
            $this->dao->update(TABLE_CMDBSYNC)->data($updateInfo)->where('id')->eq($unPushedObject->id)->exec();
            $run['cmdbsync']    = $unPushedObject->id;
            $run['response']            = $requestClass->response;
            $res[] = $run;
        }
        return $res;
    }
}