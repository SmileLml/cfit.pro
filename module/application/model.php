<?php
class applicationModel extends model
{
    /**
     * Get application list.
     * @param  string  $browseType 
     * @param  string  $orderBy 
     * @param  object  $pager 
     * @access public
     * @return void
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $applicationQuery = '';
        if($browseType == 'bysearch')
        {    
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('applicationQuery', $query->sql);
                $this->session->set('applicationForm', $query->form);
            }

            if($this->session->applicationQuery == false) $this->session->set('applicationQuery', ' 1 = 1');

            $applicationQuery = $this->session->applicationQuery;
        }

        $applications = $this->dao->select('*')->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            // ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($applicationQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'application', $browseType != 'bysearch');
        return $applications;
    }


    /**
     * Project: chengfangjinke
     *
     * @param int $programID
     * @param string $exWhere
     * @return mixed
     */
    public function getPairs($programID = 0, $exWhere = '')
    {
        return $this->dao->select('id, CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
            ->beginIF($exWhere)->andWhere($exWhere)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    public function getPairsAll($programID = 0)
    {
        return $this->dao->select('id, CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)
            ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * Project: chengfangjinke
     * Method: getapplicationNameCodePairs
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/03/03
     * Time: 15:23
     * Desc: This is the code comment. This method is called getPairs.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param int $programID
     * @param $ids
     * @return mixed
     */
     public function getapplicationNameCodePairs($programID = 0, $ids = [])
     {
         $data = $this->dao->select('id, CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)
             ->where('deleted')->eq(0)
             ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
             ->beginIF($ids)->andWhere('id')->in($ids)->fi()
             ->orderBy('id_desc')
             ->fetchPairs();
         return $data;

     }
     public function getapplicationCodePairs($programID = 0)
     {
         return $this->dao->select('id,code')->from(TABLE_APPLICATION)
             ->where('deleted')->eq(0)
             ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
             ->orderBy('id_desc')
             ->fetchPairs();
     }

     public function getapplicationNamePairs($programID = 0)
     {
         return $this->dao->select('id,name')->from(TABLE_APPLICATION)
             ->where('deleted')->eq(0)
             ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
             ->orderBy('id_desc')
             ->fetchPairs();
     }

    /**
     * 选择系统，带系统类型
     * @param int $programID
     * @return mixed
     */
    public function getapplicationNameCodePairsWithisPayment($programID = 0)
    {
        return $this->dao->select('id,isPayment,team,CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            ->orderBy('id_desc')
            ->fetchAll('id');
    }

    public function getApplicationCodePairsSyncJinx(){
        return $this->dao->select('id,isPayment,team,code, CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)
        ->where('deleted')->eq(0)
        ->andwhere('isSyncJinx')->eq('yes')
        ->orderBy('id_desc')
        ->fetchAll('id');
    }
    public function getApplicationCodePairsSyncQz(){
        return $this->dao->select('id,isPayment,team, CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)
            ->where('deleted')->eq(0)
            ->andwhere('isSyncQz')->eq('yes')
            ->orderBy('id_desc')
            ->fetchAll('id');
    }

    /**
     * 选择系统，带系统类型
     * by shixuyang
     * @return mixed
     */
    public function getapplicationNameCodePairsWithPartition($isCPCC = false)
    {
//        TABLE_PARTITION
        return $this->dao->select('application,applicationCnName as applicationName')->from(TABLE_SYSTEM_PARTITION)
            ->where('deleted')->eq('0')
            ->andWhere('applicationCnName')->ne('')
            ->beginIF($isCPCC!=false)->andWhere('dataOrigin')->eq($isCPCC)->fi()
            ->groupBy('application')
            ->fetchPairs();
    }
    // 原来手动导入的系统分区表
    public function getapplicationNameCodePairsWithPartition2($isCPCC = false)
    {
        return $this->dao->select('application,applicationName')->from(TABLE_PARTITION)
            ->where('deleted')->eq('0')
            ->beginIF($isCPCC!=false)->andWhere('dataOrigin')->eq($isCPCC)->fi()
            ->groupBy('application')
            ->fetchPairs();
    }
    /**
     * Project: cfit
     * Method: getapplicationInfo
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/1/10
     * Time: 11:22
     * Desc: This is the code comment. This method is called applicationInfo.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: qjw
     * @param int $programID
     * @return mixed
     */
     public function getapplicationInfo($programID = 0)
     {
         return $this->dao->select('id,CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name, ifnull(isPayment,"0") as isPayment')->from(TABLE_APPLICATION)
             ->where('deleted')->eq(0)
             ->beginIF($programID)->andWhere('program')->eq($programID)->fi()
             ->orderBy('id_desc')
             ->fetchAll('id');
     }

    /**
     * Build search form.
     * 
     * @param  int    $queryID 
     * @param  string $actionURL 
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->application->search['actionURL'] = $actionURL;
        $this->config->application->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->application->search);
    }

    /**
     * Create a application.
     * 
     * @access public
     * @return void
     */
    public function create()
    {
        $app = fixer::input('post')
            ->join('belongDeptIds', ',')
            ->join('network', ',')
            ->join('architecture', ',')
            ->join('userScope', ',')
            ->join('systemManager', ',')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', helper::today())
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->remove('uid,files,labels,comment,feature')
            ->stripTags($this->config->application->editor->create['id'], $this->config->allowedTags)
            ->get();

        if(!$this->post->isPayment)
        {
            dao::$errors['isPayment'] = $this->lang->application->isPaymentEmpty; 
            return false;
        }

        $app = $this->loadModel('file')->processImgURL($app, $this->config->application->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_APPLICATION)->data($app)->autoCheck()->batchCheck($this->config->application->create->requiredFields, 'notempty')->exec();

        $appID = 0;
        if(!dao::isError())
        {
            $appID = $this->dao->lastInsertID();
            $this->loadModel('file')->updateObjectID($this->post->uid, $appID, 'application');
            $this->file->saveUpload('application', $appID);
        }

        return $appID;
    }

    /**
     * Get application.
     * 
     * @param  int    $appID
     * @access public
     * @return void
     */
    public function getByID($appID)
    {
        if(empty($appID)) return null;
        $application = $this->dao->findByID($appID)->from(TABLE_APPLICATION)->fetch();
        $application = $this->loadModel('file')->replaceImgURL($application, 'desc');
        $application->files = $this->loadModel('file')->getByObject('application', $appID);

        return $application;
    }

    /**
     * Update application.
     * 
     * @access int $appID 
     * @access public
     * @return void
     */
    public function update($appID)
    {
        $oldApp      = $this->getByID($appID);
        $application = fixer::input('post')
            ->join('network', ',')
            ->join('belongDeptIds', ',')
            ->join('architecture', ',')
            ->join('userScope', ',')
            ->join('systemManager', ',')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::today())
            ->remove('uid,files,labels,comment')
            ->stripTags($this->config->application->editor->edit['id'], $this->config->allowedTags)
            ->get();
        if(!isset($application->isPayment)) $application->isPayment = 0;

        $this->dao->begin();  //开启事务
        $application = $this->loadModel('file')->processImgURL($application, $this->config->application->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_APPLICATION)->data($application)->autoCheck()
            ->batchCheck($this->config->application->edit->requiredFields, 'notempty')
            ->where('id')->eq($appID)
            ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $appID, 'application');
        $this->file->saveUpload('application', $appID);
        $this->tryError(1);
        if(($oldApp->systemDept != $application->systemDept || $oldApp->range != $application->range || $oldApp->projectMonth != $application->projectMonth) && !empty($oldApp->ciKey)){
            $requestClass = $this->pushFeedback($appID);
            $this->tryErrorRequest(1, $requestClass);
            //保存发送日志
            $this->loadModel('requestlog')->saveRequestLog($requestClass->url, $requestClass->object, $requestClass->objectType, $requestClass->method,
                $requestClass->pushData, $requestClass->response, $requestClass->status, $requestClass->extra, $requestClass->id);
        }
        $this->dao->commit(); //提交事务
        return common::createChanges($oldApp, $application);
    }

    /**
     * Project: chengfangjinke
     * Method: createFromImport
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 11:23
     * Desc: This is the code comment. This method is called createFromImport.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
    public function createFromImport()
    {
        $apps = array();
        $response['result'] = true;
        foreach($_POST['name'] as $key => $name)
        {
            $app = new stdClass();
            $app->name        = $name;
            $app->code        = $_POST['code'][$key];
            $app->team        = $_POST['team'][$key];
            $app->isPayment   = $_POST['isPayment'][$key];
            $app->attribute   = $_POST['attribute'][$key];
            $app->network     = $_POST['network'][$key];
            $app->fromUnit     = $_POST['fromUnit'][$key];
            $app->feature     = $_POST['feature'][$key];
            $app->range     = $_POST['range'][$key];
            $app->useDept     = $_POST['useDept'][$key];
            $app->projectMonth     = $_POST['projectMonth'][$key];
            $app->productDate     = $_POST['productDate'][$key];
            $app->desc     = $_POST['desc'][$key];
            $app->isBasicLine     = $_POST['isBasicLine'][$key];
            $app->isSyncJinx     = $_POST['isSyncJinx'][$key];
            $app->isSyncQz     = $_POST['isSyncQz'][$key];
            $app->createdBy   = $this->app->user->account;
            $app->createdDate = date('Y-m-d');
            $app->editedBy   = $this->app->user->account;
            $app->editedDate = date('Y-m-d');

            if ($app->isBasicLine == ''){
                $response['result']  = false;
                $response['message'] = '请选择是否属于基线';
            }
            if ($app->isSyncJinx == ''){
                $response['result']  = false;
                $response['message'] = '请选择是否同步金信';
            }
            if ($app->isSyncQz == ''){
                $response['result']  = false;
                $response['message'] = '请选择是否同步清总';
            }
            if(!$response['result']){
                return $response;
            }
            $apps[] = $app;
        }
        foreach($apps as $app)
        {
            $this->dao->insert(TABLE_APPLICATION)->data($app)->exec();
            $appID = $this->dao->lastInsertID();
            $this->loadModel('action')->create('application', $appID, 'created', '');
        }
        return $response;
    }


    /**
     * Project: chengfangjinke
     * Method: setListValue
     * User: Tony Stark
     * Year: 2022
     * Date: 2022/1/6
     * Time: 14:00
     * Desc: This is the code comment. This method is called setListValue.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     */
     public function setListValue()
     {
         $boolList        = $this->lang->application->boolList;
         $teamList        = $this->lang->application->teamList;
         $isPaymentList   = $this->lang->application->isPaymentList;
         $attributeList   = $this->lang->application->attributeList;
         $networkList     = $this->lang->application->networkList;
         $fromUnitList    = $this->lang->application->fromUnitList;
         $continueLevelList = $this->lang->application->continueLevelList;


         $this->post->set('teamList',       join(',', $teamList));
         $this->post->set('isBasicLineList',join(',', $boolList));
         $this->post->set('isSyncJinxList', join(',', $boolList));
         $this->post->set('isSyncQzList', join(',', $boolList));
         $this->post->set('isPaymentList',  join(',', $isPaymentList));
         $this->post->set('attributeList',  join(',', $attributeList));
         $this->post->set('networkList',    join(',', $networkList));
         $this->post->set('fromUnitList',   join(',', $fromUnitList));
         $this->post->set('listStyle',      $this->config->application->export->listFields);
         $this->post->set('continueLevelList',   join(',', $continueLevelList));
         $this->post->set('extraNum', 0);

     }

    /**
     *通过id获得app英文名称
     *
     * @author wangjiurong
     * @param string $select
     * @return array
     */
    public function getAppCodeNameListByIds($ids){
        $data = [];
        if(!$ids){
            return $data;
        }
        $ret = $this->dao->select('code,CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)->where('id')->in($ids)->fetchAll();
        if(!empty($ret)){
            $data = $ret;
        }
        return  $data;
    }
   /**
     * 查询项目和应用系统下的关联产品
     * @param $projectID
     * @param $application
     * @return mixed
     */
    public function getAppProducts($projectID, $application)
    {
        $query = $this->dao->select('t2.id, t2.name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('t1.project')->eq((int)$projectID)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t2.app')->eq($application);
        return $query->fetchPairs('id', 'name');
    }

    /**
     * 查询所属项目下的应用系统
     * @param $projectID
     * @return mixed
     */
    public function getApps($projectID)
    {
        $query = $this->dao->select('t3.id, CASE WHEN t3.code != "" THEN concat(concat(t3.code,"_"),t3.name) ELSE t3.name END as name')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_APPLICATION)->alias('t3')->on('t2.app = t3.id')
            ->where('t1.project')->eq((int)$projectID)
            ->andWhere('t2.deleted')->eq(0)->fetchPairs('id', 'name');
        return $query;
    }

    /**
     * 通过id获得英文名称
     *
     * @param $id
     * @return bool
     */
    public function getAppNameById($id){
        if(!$id){
            return false;
        }
        $ret = $this->dao->select('code,CASE WHEN code != "" THEN concat(concat(code,"_"),name) ELSE name END as name')->from(TABLE_APPLICATION)->where('id')->eq($id)->fetch();
        return $ret;
    }

    /**
     * 处理之后同步接口
     * @param $externalCode
     * @return mixed
     */
    public function pushFeedback($id){
        $this->loadModel('requestlog');
        $application = $this->dao->findByID($id)->from(TABLE_APPLICATION)->fetch();
        $pushEnable = $this->config->global->pushCmdbAppEnable;
        $requestClass = new stdClass();
        //判断是否开启发送反馈
        if ($pushEnable == 'enable') {
            $url = $this->config->global->pushAppInfoUrl;
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

            //cmdbId
            $pushData['ciKey']               = $application->ciKey;
            //开发单位部门
            $depts = $this->loadModel('dept')->getTopPairs();
            $pushData['developmentUnitDept']               = zmget($depts, $application->systemDept, '');
            //系统使用部门
            $pushData['useDept']               = $application->range;
            //首次立项年月
            $pushData['projectMonth']               = $application->projectMonth;

            //请求类型
            $object = 'cmdbsync';
            $objectType = 'pushAppInfoUrl';
            $method = 'POST';

            $response = '';
            $status = 'fail';
            $extra = '';
            $result = $this->loadModel('requestlog')->http($url, $pushData, $method, 'json', array(), $headers);
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
            $requestClass->pushData = $pushData;
            $requestClass->response = $response;
            $requestClass->status = $status;
            $requestClass->extra = $extra;
            $requestClass->id = $pushData->id;
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
     * 通过ids获得列表
     *
     * @param $ids
     * @param string $select
     * @return array
     */
    public function getAppListByIds($ids, $select = '*'){
        $data = [];
        if(!$ids){
            $ret = $this->dao->select($select)
                ->from(TABLE_APPLICATION)
                ->where('deleted')->eq(0)
                ->fetchAll();
        }else{
            if(!is_array($ids)){
                $ids = explode(',', $ids);
            }
            $ret = $this->dao->select($select)
                ->from(TABLE_APPLICATION)
                ->where('deleted')->eq(0)
                ->andWhere('id')->in($ids)
                ->fetchAll();
        }
        if(!empty($ret)){
            $data = $ret;
        }
        return  $data;
    }

}
