<?php
/**
 * The model file of build module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: model.php 4970 2013-07-02 05:58:11Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class buildModel extends model
{
    const BUILD_REASON     = 1007;    //标志白名单中的用户由制版系统部审批节点添加
    /**
     * Get build info.
     *
     * @param  int    $buildID
     * @param  bool   $setImgSize
     * @access public
     * @return object|bool
     */
    public function getByID($buildID, $setImgSize = false)
    {
        $build = $this->dao->select('t1.*, t2.name as executionName, t3.name as productName, t3.type as productType')
            ->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
            ->where('t1.id')->eq((int)$buildID)
            ->fetch();
        if(!$build) return false;

        $build = $this->loadModel('file')->replaceImgURL($build, 'desc,specialPassReason');
        $build->files = $this->file->getByObject('build', $buildID,'verifyFiles');
        if($setImgSize) $build->desc = $this->file->setImgSize($build->desc);
        return $build;
    }

    /**
     * Get by ID list.
     *
     * @param  array $idList
     * @access public
     * @return array
     */
    public function getByList($idList)
    {
        return $this->dao->select('*')->from(TABLE_BUILD)->where('id')->in($idList)->fetchAll('id');
    }

    /**
     * Get builds of a project.
     *
     * @param  int    $projectID
     * @param  string $type
     * @param  int    $param
     * @access public
     * @return array
     */
    public function getProjectBuilds($projectID = 0, $type = 'all', $param = 0,$orderBy, $pager)
    {
        $data = [];
        $account = $this->app->user->account;
        $ret = $this->dao->select('t1.*, t2.name as executionName, t2.id as executionID, t3.name as productName, t4.name as branchName')
            ->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
            ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
            ->where('t1.project')->eq((int)$projectID)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.project')->ne(0)
           // ->andWhere('t1.dealuser')->eq($account)
            ->beginIF($type == 'product' and $param)->andWhere('t1.product')->eq($param)->fi()
            ->beginIF($type == 'bysearch')->andWhere($param)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        if($ret){
            foreach ($ret as $buildInfo){
                $isQualityGate = false;
                if($buildInfo->status == 'waittest'){ //待测试
                    $isQualityGate = $this->getIsQualityGate($buildInfo);
                }
                $buildInfo->isQualityGate = $isQualityGate;
            }
            $data = $ret;
        }
        return $data;
    }

    /**
     * Get builds of a project in pairs.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $params       noempty|notrunk, can be a set of them
     * @param  int    $buildID
     * @access public
     * @return array
     */
    public function getProjectBuildPairs($projectID, $productID, $branch = 0, $params = '', $buildID = 0)
    {
        $sysBuilds      = array();
        $selectedBuilds = array();
        if(strpos($params, 'noempty') === false) $sysBuilds = array('' => '');
        if(strpos($params, 'notrunk') === false) $sysBuilds = $sysBuilds + array('trunk' => $this->lang->trunk);
        if($buildID != 0) $selectedBuilds = $this->dao->select('id, name')->from(TABLE_BUILD)->where('id')->in($buildID)->fetchPairs();

        $projectBuilds = $this->dao->select('t1.id, t1.name, t1.project, t2.status as projectStatus, t3.id as releaseID, t3.status as releaseStatus, t4.name as branchName')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->leftJoin(TABLE_RELEASE)->alias('t3')->on('t1.id = t3.build')
            ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
            ->where('t1.project')->eq((int)$projectID)
            ->beginIF($productID)->andWhere('t1.product')->eq((int)$productID)->fi()
            ->beginIF($branch)->andWhere('t1.branch')->in("0,$branch")->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.date desc, t1.id desc')->fetchAll('id');

        /* Set builds and filter terminate releases. */
        $builds = array();
        foreach($projectBuilds as $buildID => $build)
        {
            if(empty($build->releaseID) and (strpos($params, 'nodone') !== false) and ($build->projectStatus === 'done')) continue;
            if((strpos($params, 'noterminate') !== false) and ($build->releaseStatus === 'terminate')) continue;
            $builds[$buildID] = $build->name;
        }
        if(!$builds) return $sysBuilds + $selectedBuilds;

        /* if the build has been released, replace build name with release name. */
       /* $releases = $this->dao->select('build, name')->from(TABLE_RELEASE)
            ->where('build')->in(array_keys($builds))
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();
        foreach($releases as $buildID => $releaseName) $builds[$buildID] = $releaseName;*/

        return $sysBuilds + $builds + $selectedBuilds;
    }

    /**
     * Get builds of a project by search.
     *
     * @param  int    $projectID
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getProjectBuildsBySearch($projectID, $queryID,$orderBy, $pager = null )
    {
        /* If there are saved query conditions, reset the session. */
        if((int)$queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('projectBuildQuery', $query->sql);
                $this->session->set('projectBuildForm', $query->form);
            }
        }
        if($this->session->projectBuildQuery == false) $this->session->set('projectBuildQuery', ' 1 = 1');

        $buildQuery = $this->session->projectBuildQuery;
        $buildQuery = $this->loadModel('reviewmeeting')->dealSqlAmbiguous($buildQuery,'t1','createdBy');
        $buildQuery = $this->loadModel('reviewmeeting')->dealSqlAmbiguous($buildQuery,'t1','version');
        $buildQuery = $this->loadModel('reviewmeeting')->dealSqlAmbiguous($buildQuery,'t1','createdDate');
        $buildQuery = $this->loadModel('reviewmeeting')->dealSqlAmbiguous($buildQuery,'t1','status');
        $buildQuery = $this->loadModel('reviewmeeting')->dealSqlAmbiguous($buildQuery,'t1','app');
        /* Distinguish between repeated fields. */
        $fields = array('id' => '`id`', 'name' => '`name`', 'product' => '`product`', 'desc' => '`desc`', 'project' => '`project`');
        foreach($fields as $field)
        {
            if(strpos($this->session->projectBuildQuery, $field) !== false)
            {
                $buildQuery = str_replace($field, "t1." . $field, $buildQuery);
            }
        }
        $buildQuery = str_replace("t1.`version` = ''", "t1.`version` is NULL", $buildQuery);

        return $this->getProjectBuilds($projectID, 'bysearch', $buildQuery,$orderBy, $pager);
    }

    /**
     * Get builds of a execution.
     *
     * @param  int        $executionID
     * @param  string     $type      all|product|bysearch
     * @param  int|string $param     productID|buildQuery
     * @access public
     * @return array
     */
    public function getExecutionBuilds($executionID, $type = '', $param = '')
    {
        return $this->dao->select('t1.*, t2.name as executionName, t3.name as productName, t4.name as branchName')
            ->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
            ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
            ->where('t1.execution')->eq((int)$executionID)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($type == 'product' and $param)->andWhere('t1.product')->eq($param)->fi()
            ->beginIF($type == 'bysearch')->andWhere($param)->fi()
            ->orderBy('t1.date DESC, t1.id desc')
            ->fetchAll('id');
    }

    /**
     * Get builds of a execution by search.
     *
     * @param  int    $executionID
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getExecutionBuildsBySearch($executionID, $queryID)
    {
        /* If there are saved query conditions, reset the session. */
        if((int)$queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('executionBuildQuery', $query->sql);
                $this->session->set('executionBuildForm', $query->form);
            }
        }

        if($this->session->executionBuildQuery == false) $this->session->set('executionBuildQuery', ' 1 = 1');
        $buildQuery = $this->session->executionBuildQuery;

        /* Distinguish between repeated fields. */
        $fields = array('id' => '`id`', 'name' => '`name`', 'product' => '`product`', 'desc' => '`desc`');
        foreach($fields as $field)
        {
            if(strpos($this->session->executionBuildQuery, $field) !== false)
            {
                $buildQuery = str_replace($field, "t1." . $field, $buildQuery);
            }
        }

        return $this->getExecutionBuilds($executionID, 'bysearch', $buildQuery);
    }

    /**
     * Get builds of a execution in pairs.
     *
     * @param  int    $executionID
     * @param  int    $productID
     * @param  string $params       noempty|notrunk, can be a set of them
     * @param  int    $buildID
     * @access public
     * @return array
     */
    public function getExecutionBuildPairs($executionID, $productID, $branch = 0, $params = '', $buildID = 0)
    {
        $sysBuilds      = array();
        $selectedBuilds = array();
        if(strpos($params, 'noempty') === false) $sysBuilds = array('' => '');
        if(strpos($params, 'notrunk') === false) $sysBuilds = $sysBuilds + array('trunk' => $this->lang->trunk);
        if($buildID != 0) $selectedBuilds = $this->dao->select('id, name')->from(TABLE_BUILD)->where('id')->in($buildID)->fetchPairs();

        $executionBuilds = $this->dao->select('t1.id, t1.name, t1.execution, t2.status as executionStatus, t3.id as releaseID, t3.status as releaseStatus, t4.name as branchName')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_RELEASE)->alias('t3')->on('t1.id = t3.build')
            ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
            ->where('t1.execution')->eq((int)$executionID)
            ->beginIF($productID)->andWhere('t1.product')->eq((int)$productID)->fi()
            ->beginIF($branch)->andWhere('t1.branch')->in("0,$branch")->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.date desc, t1.id desc')->fetchAll('id');

        /* Set builds and filter terminate releases. */
        $builds = array();
        foreach($executionBuilds as $buildID => $build)
        {
            if(empty($build->releaseID) and (strpos($params, 'nodone') !== false) and ($build->executionStatus === 'done')) continue;
            if((strpos($params, 'noterminate') !== false) and ($build->releaseStatus === 'terminate')) continue;
            $builds[$buildID] = $build->name;
        }
        if(!$builds) return $sysBuilds + $selectedBuilds;

        /* if the build has been released, replace build name with release name. */
        $releases = $this->dao->select('build, name')->from(TABLE_RELEASE)
            ->where('build')->in(array_keys($builds))
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();
        foreach($releases as $buildID => $releaseName) $builds[$buildID] = $releaseName;

        return $sysBuilds + $builds + $selectedBuilds;
    }

    /**
     * Get last build.
     *
     * @param  int    $executionID
     * @access public
     * @return bool | object
     */
    public function getLast($executionID)
    {
        return $this->dao->select('id, name')->from(TABLE_BUILD)
            ->where('execution')->eq((int)$executionID)
            ->orderBy('date DESC,id DESC')
            ->limit(1)
            ->fetch();
    }

    /**
     * Create a build
     *
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function create($executionID)
    {
        $build = new stdclass();
        $build->stories = '';
        $build->bugs    = '';

        $execution = $this->loadModel('execution')->getByID($executionID);

        $build = fixer::input('post')
            ->setDefault('project', $execution->project)
            ->setDefault('product', 0)
            ->setDefault('branch', 0)
            ->cleanInt('product,branch')
            ->add('execution', (int)$executionID)
            ->stripTags($this->config->build->editor->create['id'], $this->config->allowedTags)
            ->remove('resolvedBy,allchecker,files,labels,uid')
            ->get();

        $build = $this->loadModel('file')->processImgURL($build, $this->config->build->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_BUILD)->data($build)
            ->autoCheck()
            ->batchCheck($this->config->build->create->requiredFields, 'notempty')
            ->check('name', 'unique', "product = {$build->product} AND branch = {$build->branch} AND deleted = '0'")
            ->exec();

        if(!dao::isError())
        {
            $buildID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $buildID, 'build');
            $this->file->saveUpload('build', $buildID);
            $this->loadModel('score')->create('build', 'create', $buildID);
            return $buildID;
        }
    }

    /**
     * Update a build.
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function update($buildID)
    {
        $buildID  = (int)$buildID;
        $oldBuild = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->eq($buildID)->fetch();
        $build    = fixer::input('post')->stripTags($this->config->build->editor->edit['id'], $this->config->allowedTags)
            ->setDefault('product', $oldBuild->product)
            ->setDefault('branch', $oldBuild->branch)
            ->setDefault('demandid', '')
            ->setDefault('problemid', '')
            ->setDefault('sendlineId', '')
            ->setDefault('editedBy', $this->app->user->account)
            ->setDefault('editedDate', date('Y-m-d H:i:s'))
            ->setDefault('sendlineId', '')
            ->cleanInt('product,branch')
            ->setDefault('status', 'build')
            ->setIF($oldBuild->status == 'back', 'status', 'build')
            ->remove('allchecker,resolvedBy,files,labels,uid,desc,issubmit')
            ->get();

        if($this->post->systemverify && !$this->post->verifyUser){
            return dao::$errors['verifyUser'] = $this->lang->build->verifyUserEmpty;
        }
        $projectID = $oldBuild->project;
        $isSetSeverityTestUser =  $this->loadModel('qualitygate')->getIsSetQualityGate($projectID);
        if($isSetSeverityTestUser){
            if(!isset($build->severityTestUser) || !$build->severityTestUser){
                return dao::$errors['severityTestUser'] = sprintf($this->lang->build->checkOpResultList['fieldEmpty'], $this->lang->build->severityTestUser) ;
            }
        }
        $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $build->product, 0);

        /*$executAndTask = array_filter(explode(',',$this->post->taskid));
        $build->execution = isset($executAndTask[0]) ? $executAndTask[0] : 0;
        $build->taskid  = isset($executAndTask[1]) ? $executAndTask[1] : 0;*/

        $build->dealuser = $build->builder;
        $pinfo = $this->dao->select('id,skipBuild,piplinePath,code')->from(TABLE_PRODUCT)->where('id')->eq($build->product)->fetch();
        if($build->product != '99999' && $build->version != '1'){
            $build->name = $pinfo->code.'-'.zget($plans,$build->version,'');
        }else if($build->product == '99999' || $build->version == '1'){
            $apps =  $this->loadModel('application')->getapplicationCodePairs();
            $app = zget($apps,$build->app,'');
            $build->name = $app.'-'.$this->lang->build->noProductUpdate;
        }
        $build->name = $build->name ? $build->name.'-'.date('His') :'';
        if(isset($pinfo) && ($build->version == '1' || ($build->product != '99999' && $pinfo->skipBuild == '1'))){
            $build->status   = 'waittest';
            $build->dealuser =  $build->testUser;

            $users = $this->loadModel('user')->getPairs('noclosed');
            if($build->version == '1'){
                $comment = sprintf($this->lang->build->noskipTip,$build->svnPath);
            }else if($build->product != '99999' && $build->version != '1' && $pinfo->skipBuild == '1'){
                $comment = sprintf($this->lang->build->skipTip,$pinfo->piplinePath);
            }
            $progress = $this->lang->build->htmlCode . helper::now() . ' 由<strong>' . zget($users,$build->builder,'') . '</strong>新增' . '（'.$this->lang->build->buildId.'：'.$buildID.'）'. '</span><br>' . $comment;
            $build->desc = $oldBuild->desc.'<br>'.$progress;
        }else{
            $build->desc = $oldBuild->desc;
        }

       // $build->taskName = $this->post->taskname;
        $build->code = isset($pinfo->code) ? $pinfo->code : '';

        //unset($build->taskname);
        /*$taskname = trim( trim( strrchr($this->post->taskname,'['),']'),'[');//所属任务
        $taskname = explode(',',$taskname);
        foreach ($taskname as $item) {
            if(strpos($item,'D') !== false){
                $build->demandid .= $item .',';
            }elseif (strpos($item,'Q') !== false){
                $build->problemid .= $item .',';
            }else{
                $build->sendlineId .= $item .',';
            }
        }*/

        $build->demandid   = $build->demandChosen ? preg_replace('/[\r\n]/', '',str_replace(PHP_EOL,',',trim($build->demandChosen))) : '';
        $build->problemid  = $build->problemChosen ? preg_replace('/[\r\n]/', '',str_replace(PHP_EOL,',',trim($build->problemChosen))) : '';
        $build->sendlineId = $build->sendlineChosen ? preg_replace('/[\r\n]/', '',str_replace(PHP_EOL,',',trim($build->sendlineChosen))) : '';
        $build->execution = '';
        $build->taskid = '';
        $build->taskName = '';
        $tasks = $this->loadModel('task')->getExecutionTask($build->app,$oldBuild->project,$build->product,$build->version,array_merge(explode(',',$build->demandid),explode(',',$build->problemid),explode(',',$build->sendlineId)));
        if($tasks){
            foreach ($tasks as $key => $task) {
                $build->execution .= $key .',';
                $build->taskid .= implode(',',array_keys($task)).',' ;
                $build->taskName .= implode(',',array_values($task)).',';
            }
        }
        unset( $build->demandChosen,$build->problemChosen,$build->sendlineChosen);
        if(empty($build->demandid) && empty($build->problemid) && empty($build->sendlineId)){
            return dao::$errors['demandid'] = $this->lang->build->demandAndProblemAndSecondEmpty;
        }
        //$build = $this->loadModel('file')->processImgURL($build, $this->config->build->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_BUILD)->data($build)
            ->autoCheck()
            ->batchCheck($this->config->build->edit->requiredFields, 'notempty')
            ->where('id')->eq($buildID)
            ->check('name', 'unique', "id != $buildID AND product = {$build->product} AND branch = {$build->branch} AND deleted = '0'")
            ->exec();
        if(isset($build->branch) and $oldBuild->branch != $build->branch) $this->dao->update(TABLE_RELEASE)->set('branch')->eq($build->branch)->where('build')->eq($buildID)->exec();
        if(!dao::isError())
        {
//            $this->file->updateObjectID($this->post->uid, $buildID, 'build');
//            $this->file->saveUpload('build', $buildID);
//            $this->loadModel('score')->create('build', 'create', $buildID);
            //创建测试单
            if($build->status == 'waittest'){
                $nowBuild = $this->loadModel('build')->getByBuildID($buildID);
                $this->loadModel('build')->createTestTask($nowBuild);
            }
            //跳过制版
            if(isset($pinfo) && ($build->version == '1' || ($build->product != '99999' && $pinfo->skipBuild == '1'))){
             //工作量默认0.1
                $this->loadModel('consumed')->record('build', $buildID, '0.1', $this->app->user->account, '', 'build', array());
                $this->loadModel('consumed')->record('build', $buildID, '0', $build->builder, 'build', 'waittest', array());
            }
            //是否需要生成质量门禁
            if($isSetSeverityTestUser && $build->severityTestUser){
                $build->project = $projectID;
                if($build->version > 1){
                    $this->setQualityGate($build, $buildID, 'edit');
                }else{ //没有版本，todo暂时不做处理
                }
            }
            return common::createChanges($oldBuild, $build);
        }
    }

    public function updateSave($buildID)
    {
        $buildID  = (int)$buildID;
        $oldBuild = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->eq($buildID)->fetch();
        $build    = fixer::input('post')->stripTags($this->config->build->editor->edit['id'], $this->config->allowedTags)
            ->setDefault('product', $oldBuild->product)
            ->setDefault('branch', $oldBuild->branch)
            ->setDefault('demandid', '')
            ->setDefault('problemid', '')
            ->setDefault('sendlineId', '')
            ->setDefault('editedBy', $this->app->user->account)
            ->setDefault('editedDate', date('Y-m-d H:i:s'))
            ->setDefault('sendlineId', '')
            ->cleanInt('product,branch')
            ->setDefault('status', 'wait')
            ->setDefault('createdBy', $this->app->user->account)
            ->remove('allchecker,resolvedBy,files,labels,uid,desc,issubmit')
            ->get();

        $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $build->product, 0);

        $executAndTask = array_filter(explode(',',$this->post->taskid));
        $build->execution = isset($executAndTask[0]) ? $executAndTask[0] : 0;
        $build->taskid  = isset($executAndTask[1]) ? $executAndTask[1] : 0;

        $build->dealuser = $this->app->user->account;
        $pinfo = $this->dao->select('id,skipBuild,piplinePath,code')->from(TABLE_PRODUCT)->where('id')->eq($build->product)->fetch();
        if($build->product != '99999' && $build->version != '1'){
            $build->name = $pinfo->code.'-'.zget($plans,$build->version,'');
        }else if($build->product == '99999' || $build->version == '1'){
            $apps =  $this->loadModel('application')->getapplicationCodePairs();
            $app = zget($apps,$build->app,'');
            $build->name = $app.'-'.$this->lang->build->noProductUpdate;
        }
        $build->name = $build->name ? $build->name.'-'.date('His') :'';
        if(isset($pinfo) && ($build->version == '1' || ($build->product != '99999' && $pinfo->skipBuild == '1'))){
            $users = $this->loadModel('user')->getPairs('noclosed');
            if($build->version == '1'){
                $comment = sprintf($this->lang->build->noskipTip,$build->svnPath);
            }else if($build->product != '99999' && $build->version != '1' && $pinfo->skipBuild == '1'){
                $comment = sprintf($this->lang->build->skipTip,$pinfo->piplinePath);
            }
            $progress = $this->lang->build->htmlCode . helper::now() . ' 由<strong>' . zget($users,$build->builder,'') . '</strong>新增' . '（'.$this->lang->build->buildId.'：'.$buildID.'）'. '</span><br>' . $comment;
            $build->desc = $oldBuild->desc.'<br>'.$progress;
        }else{
            $build->desc = $oldBuild->desc;
        }

        //$build->taskName = $this->post->taskname;
        $build->code = isset($pinfo->code) ? $pinfo->code : '';

        /*unset($build->taskname);
        $taskname = trim( trim( strrchr($this->post->taskname,'['),']'),'[');//所属任务
        $taskname = explode(',',$taskname);
        foreach ($taskname as $item) {
            if(strpos($item,'D') !== false){
                $build->demandid .= $item .',';
            }elseif (strpos($item,'Q') !== false){
                $build->problemid .= $item .',';
            }else{
                $build->sendlineId .= $item .',';
            }
        }*/

        $build->demandid   = $build->demandChosen ? preg_replace('/[\r\n]/', '',str_replace(PHP_EOL,',',trim($build->demandChosen))) : '';
        $build->problemid  = $build->problemChosen ? preg_replace('/[\r\n]/', '',str_replace(PHP_EOL,',',trim($build->problemChosen))) : '';
        $build->sendlineId = $build->sendlineChosen ? preg_replace('/[\r\n]/', '',str_replace(PHP_EOL,',',trim($build->sendlineChosen))) : '';
        $build->execution = '';
        $build->taskid = '';
        $build->taskName = '';
        $tasks = $this->loadModel('task')->getExecutionTask($build->app,$oldBuild->project,$build->product,$build->version,array_merge(explode(',',$build->demandid),explode(',',$build->problemid),explode(',',$build->sendlineId)));
        if($tasks){
            foreach ($tasks as $key => $task) {
                $build->execution .= $key .',';
                $build->taskid .= implode(',',array_keys($task)).',' ;
                $build->taskName .= implode(',',array_values($task)).',';
            }
        }
        unset( $build->demandChosen,$build->problemChosen,$build->sendlineChosen);
        /*if(empty($build->demandid) && empty($build->problemid) && empty($build->sendlineId)){
            return dao::$errors['demandid'] = $this->lang->build->demandAndProblemAndSecondEmpty;
        }*/
        //$build = $this->loadModel('file')->processImgURL($build, $this->config->build->editor->edit['id'], $this->post->uid);

        unset($build->desc);
        $this->dao->update(TABLE_BUILD)->data($build)
            ->autoCheck()
            ->batchCheck($this->config->build->save->requiredFields, 'notempty')
            ->where('id')->eq($buildID)
            ->exec();
        if(!dao::isError())
        {
//            $this->file->updateObjectID($this->post->uid, $buildID, 'build');
//            $this->file->saveUpload('build', $buildID);
            return common::createChanges($oldBuild, $build);
        }
    }

    /**
     * Update linked bug to resolved.
     *
     * @param  object    $build
     * @access public
     * @return void
     */
    public function updateLinkedBug($build)
    {
        $bugs = empty($build->bugs) ? '' : $this->dao->select('*')->from(TABLE_BUG)->where('id')->in($build->bugs)->fetchAll();
        $data = fixer::input('post')->get();
        $now  = helper::now();

        $resolvedPairs = array();
        if(isset($_POST['bugs']))
        {
            foreach($data->bugs as $key => $bugID)
            {
                if(isset($_POST['resolvedBy'][$bugID])) $resolvedPairs[$bugID] = $data->resolvedBy[$bugID];
            }
        }

        $this->loadModel('action');
        if(!$bugs) return false;
        foreach($bugs as $bug)
        {
            if($bug->status == 'resolved' or $bug->status == 'closed') continue;

            $bug->resolvedBy     = $resolvedPairs[$bug->id];
            $bug->resolvedDate   = $now;
            $bug->status         = 'resolved';
            $bug->confirmed      = 1;
            $bug->assignedDate   = $now;
            $bug->assignedTo     = $bug->openedBy;
            $bug->lastEditedBy   = $this->app->user->account;
            $bug->lastEditedDate = $now;
            $bug->resolution     = 'fixed';
            $bug->resolvedBuild  = $build->id;
            $this->dao->update(TABLE_BUG)->data($bug)->where('id')->eq($bug->id)->exec();
            $this->action->create('bug', $bug->id, 'Resolved', '', 'fixed', $bug->resolvedBy);
        }
    }

    /**
     * Link stories
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function linkStory($buildID)
    {
        $build = $this->getByID($buildID);

        foreach($this->post->stories as $i => $storyID)
        {
            if(strpos(",{$build->stories},", ",{$storyID},") !== false) unset($_POST['stories'][$i]);
        }

        $build->stories .= ',' . join(',', $this->post->stories);
        $this->dao->update(TABLE_BUILD)->set('stories')->eq($build->stories)->where('id')->eq((int)$buildID)->exec();

        $this->loadModel('action');
        foreach($this->post->stories as $storyID) $this->action->create('story', $storyID, 'linked2build', '', $buildID);
    }

    /**
     * Unlink story
     *
     * @param  int    $buildID
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function unlinkStory($buildID, $storyID)
    {
        $build = $this->getByID($buildID);
        $build->stories = trim(str_replace(",$storyID,", ',', ",$build->stories,"), ',');
        if($build->stories) $build->stories = ',' . $build->stories;

        $this->dao->update(TABLE_BUILD)->set('stories')->eq($build->stories)->where('id')->eq((int)$buildID)->exec();
        $this->loadModel('action')->create('story', $storyID, 'unlinkedfrombuild', '', $buildID, '', false);
    }

    /**
     * Batch unlink story.
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function batchUnlinkStory($buildID)
    {
        $storyList = $this->post->unlinkStories;
        if(empty($storyList)) return true;

        $build = $this->getByID($buildID);
        $build->stories = ",$build->stories,";
        foreach($storyList as $storyID) $build->stories = str_replace(",$storyID,", ',', $build->stories);
        $build->stories = trim($build->stories, ',');
        $this->dao->update(TABLE_BUILD)->set('stories')->eq($build->stories)->where('id')->eq((int)$buildID)->exec();

        $this->loadModel('action');
        foreach($this->post->unlinkStories as $unlinkStoryID) $this->action->create('story', $unlinkStoryID, 'unlinkedfrombuild', '', $buildID);
    }

    /**
     * Link bugs.
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function linkBug($buildID)
    {
        $build = $this->getByID($buildID);

        foreach($this->post->bugs as $i => $bugID)
        {
            if(strpos(",{$build->bugs},", ",{$bugID},") !== false) unset($_POST['bugs'][$i]);
        }

        $build->bugs .= ',' . join(',', $this->post->bugs);
        $this->updateLinkedBug($build);
        $this->dao->update(TABLE_BUILD)->set('bugs')->eq($build->bugs)->where('id')->eq((int)$buildID)->exec();

        $this->loadModel('action');
        foreach($this->post->bugs as $bugID) $this->action->create('bug', $bugID, 'linked2bug', '', $buildID);
    }

    /**
     * Unlink bug.
     *
     * @param  int    $buildID
     * @param  int    $bugID
     * @access public
     * @return void
     */
    public function unlinkBug($buildID, $bugID)
    {
        $build = $this->getByID($buildID);
        $build->bugs = trim(str_replace(",$bugID,", ',', ",$build->bugs,"), ',');
        if($build->bugs) $build->bugs = ',' . $build->bugs;

        $this->dao->update(TABLE_BUILD)->set('bugs')->eq($build->bugs)->where('id')->eq((int)$buildID)->exec();
        $this->loadModel('action')->create('bug', $bugID, 'unlinkedfrombuild', '', $buildID, '', false);
    }

    /**
     * Batch unlink bug.
     *
     * @param  int    $buildID
     * @access public
     * @return void
     */
    public function batchUnlinkBug($buildID)
    {

        $bugList = $this->post->unlinkBugs;
        if(empty($bugList)) return true;

        $build = $this->getByID($buildID);
        $build->bugs = ",$build->bugs,";
        foreach($bugList as $bugID) $build->bugs = str_replace(",$bugID,", ',', $build->bugs);
        $build->bugs = trim($build->bugs, ',');
        $this->dao->update(TABLE_BUILD)->set('bugs')->eq($build->bugs)->where('id')->eq((int)$buildID)->exec();

        $this->loadModel('action');
        foreach($this->post->unlinkBugs as $unlinkBugID) $this->action->create('bug', $unlinkBugID, 'unlinkedfrombuild', '', $buildID);
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object    $build
     * @param  string    $action
     * @access public
     * @return bool
     */
    public static function isClickable($build, $action)
    {
        global $app;
        $action = strtolower($action);
        $user = explode(',',$build->dealuser);
        if(empty($build)) return true;
        if($action == 'deal')   return ($app->user->account == 'admin' or in_array($app->user->account,$user) )  and !in_array($build->status, array('testfailed', 'versionfailed', 'verifyfailed','back','wait','verifyrejectsubmit'));
        if($action == 'edit')   return ($app->user->account == 'admin' or in_array($app->user->account,$user) or $app->user->account == $build->createdBy) and in_array($build->status, array('wait', 'back'));
        if($action == 'rebuild')   return ($app->user->account == 'admin' or in_array($app->user->account,$user)) and in_array($build->status, array('testfailed', 'versionfailed', 'verifyfailed','verifyrejectsubmit'));
        if($action == 'delete')   return ($app->user->account == 'admin' or $app->user->account == $build->createdBy or $app->user->account == $build->builder) and in_array($build->status, array('build', 'back','wait'));
        if($action == 'ignore')  return ($app->user->account == 'admin' || in_array($app->user->account,$user)) && !empty($build->dealuser) && in_array($build->status, array('testfailed', 'versionfailed','verifyfailed','verifyrejectsubmit'));
        return true;
    }

    /**
     * Method: workloadEdit
     * @param $buildID
     * @param $consumedID
     * @return array
     */
    public function workloadEdit($buildID, $consumedID)
    {
      //查询处理前的
        $oldConsumed = $this->loadModel('consumed')->getWorkloadDetails($consumedID);
        $oldbuild = $this->getByID($buildID);
        //返回信息
        $res = array();
        //检查时间信息
       /* $consumedTime = $this->post->consumed;
        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumedTime);
        if(!$checkRes){
            return dao::$errors;
        }*/
        //检查关配合人员工作量信息
        $checkRes = $this->loadModel('consumed')->checkPostDetails(true);
        if(!$checkRes){
            return dao::$errors;
        }

        $consumed = fixer::input('post')->remove('comment, relevantUser, workload, dealuser')->get();
        /* Judge whether the current work record is the last one. */
        $isLast = $this->loadModel('consumed')->checkIsLastConsumed($consumedID, $buildID, 'build');
        $dealuser = $this->post->dealuser;
        //获得相关配合人员工作量信息
         $consumed->details = $this->loadModel('consumed')->getPostDetails();

        if($consumed->after == 'testsuccess' and $oldbuild->systemverify == '0')
        {
            $consumed->after = 'verifysuccess';
        }

        $this->dao->update(TABLE_CONSUMED)->data($consumed)->autoCheck() //编辑工作量
        ->batchCheck($this->config->build->workloadedit->requiredFields, 'notempty')
            ->where('id')->eq($consumedID)
            ->exec();

        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('build')
            ->andWhere('objectID')->eq($buildID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedsByID($buildID);

        //最后一个工作量节点修改需求单的待处理状态和待处理人
        if($isLast) {
            if(($oldbuild->status != $consumed->after) || ($oldbuild->dealuser != $dealuser)){
                $this->dao->update(TABLE_BUILD)->set('status')->eq($consumed->after)->set('dealuser')->eq($dealuser)->where('id')->eq($buildID)->exec();

                $data = new stdClass();
                $data->status   = $consumed->after;
                $data->dealuser = $dealuser;
                $res = common::createChanges($oldbuild, $data);
            }
        }
        if($consumed->after == 'waitverify'){
            //测试配合人员
            $data->testRelevantUser   = empty($_POST['relevantUser']) ? '' :implode(',', array_filter($_POST['relevantUser']));
            $this->dao->update(TABLE_BUILD)->set('testRelevantUser')->eq($data->testRelevantUser)->where('id')->eq($buildID)->exec();
            $data = new stdClass();
            $data->testRelevantUser  =  $data->testRelevantUser ;
            $res = common::createChanges($oldbuild, $data);
        }
        if($consumed->after == 'verifysuccess'){
            //验证配合人员
            $data->verifyRelevantUser = empty($_POST['relevantUser']) ? '' :implode(',',  array_filter($_POST['relevantUser']));
            $this->dao->update(TABLE_BUILD)->set('verifyRelevantUser')->eq($data->verifyRelevantUser)->where('id')->eq($buildID)->exec();
            $data = new stdClass();
            $data->verifyRelevantUser  =  $data->verifyRelevantUser ;
            $res = common::createChanges($oldbuild, $data);
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID)
        {
            $this->dao->update(TABLE_ACTION)->set('actor')->eq($consumed->account)->where('id')->eq($actionID)->exec();
        }

        /* 处理相关配合人员的记录（增删改） */
       // $this->loadModel('consumed')->dealRelevantUser($consumedID);
        //同步编辑工时  报工该造不需要工时同步
       /* if(!dao::isError()){
            //工时同步更新
            $build = $this->getByID($buildID);
            $this->loadModel('build')->editWorkLoadTaskEstimate($oldConsumed,$build->taskid,$buildID,$consumedID,$consumedTime,$this->post->account);
        }*/
        return $res;
    }

    /**
     * Project: chengfangjinke
     * Method: workloadDelete
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:54
     * Desc: This is the code comment. This method is called workloadDelete.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $buildID
     * @param $consumedID
     * @return array
     */
    public function workloadDelete($buildID, $consumedID)
    {
        $actions = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('objectType')->eq('build')
            ->andWhere('objectID')->eq($buildID)
            ->andWhere('action')->eq('deal')
            ->orderBy('id_asc')
            ->fetchAll();

        $consumeds = $this->getConsumedList($buildID);

        /* Judge whether the current work record is the last one. */
        $total  = count($consumeds) - 1;
        $isLast = false;
        $previousID = 0;
        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID)
            {
                $isLast = $index == $total ? true : false;
                $previousID = $consumeds[$total - 1]->id;
            }
        }

        if($isLast and $previousID)
        {
            $consumed = $this->getConsumedByID($previousID);
            $this->dao->update(TABLE_BUILD)->set('status')->eq($consumed->after)->where('id')->eq($buildID)->exec();
        }

        /* Get the corresponding relationship between work record and operation record. */
        $actionID = 0;
        array_splice($consumeds, 0, 1); // Remove the first work record.

        foreach($consumeds as $index => $cs)
        {
            if($cs->id == $consumedID) $actionID = $actions[$index]->id;
        }

        if($actionID) $this->dao->delete()->from(TABLE_ACTION)->where('id')->eq($actionID)->exec();

        /* 逻辑删除 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('id')->eq($consumedID)->exec(); //逻辑删除
        /* 删除相关配合人员记录 */
        $this->dao->update(TABLE_CONSUMED)->set('deleted')->eq(1)->where('parentID')->eq($consumedID)->exec(); //删除相关配合人员记录
       //同步删除工时
        $oldBuild = $this->getByID($buildID);
        /*if(!dao::isError()){
            $this->loadModel('build')->deleteWorkLoadTaskEstimate($oldBuild->taskid,$buildID,$consumedID);
        }*/
        return array();
    }

    /**
     * @param $buildID
     * @return mixed
     * 获取历次备注
     */
    public function getDesc($buildID)
    {
        $desc =  $this->dao->select('actor,date,comment')->from(TABLE_ACTION)->where('objectType')->eq('buildDesc')->andWhere('objectID')->eq($buildID)->fetchAll();
        return $desc;
    }

    public function getConsumedList($buildID)
    {
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('build')
            ->andWhere('objectID')->eq($buildID)
            ->andWhere('parentID')->eq('0')
            ->andWhere('deleted')->eq(0)
            ->orderBy('id_asc')
            ->fetchAll();
        return $cs;
    }

    public function getConsumedByID($consumedID)
    {
        return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
    }



    public function batchDeal($buildID){
        /*if (!$this->post->result) {
            dao::$errors['result'] = $this->lang->projectplan->resultEmpty;
            return false;
        }*/

        if (!$this->post->comment) {
            dao::$errors['comment'] = $this->lang->projectplan->commentEmpty;
            return false;
        }
        if(!$buildID){
            dao::$errors['comment'] = "制版id不能为空";
            return false;
        }
        $buildIDArr = explode(",",$buildID);

        $buildlistinfo = $this->getInIDs($buildID);
        $projects = array_column($buildlistinfo,"project");
        $projectCount = count(array_unique($projects));
        if($projectCount > 1){
            dao::$errors['project'] = $this->lang->build->projectError;
            return false;
        }
        $statusreviewStage = array_column($buildlistinfo,"status","reviewStage");
        $statusreviewStatecount =  count(array_unique($statusreviewStage));

        if($statusreviewStatecount > 1){
            dao::$errors['yearBatchReviewing'] = "您勾选的制版未处在相同审批节点！";
            return false;
        }

        $this->dao->begin();
        $this->session->set('post', $_POST);
        $result = [];
        foreach ($buildIDArr as $build){
            $buildInfo= $this->getInIDs($build);
            $_POST =  $this->session->post;
            $_POST['name'] = $buildInfo[$build]->name;
            $result[$build] = $this->deal($build);
            if(dao::isError())
            {
                $this->dao->rollBack();
                return false;
            }
        }
        $this->session->set('post', '');
        $this->dao->commit();
        return $result;

    }
    public function getInIDs($buildIDs)
    {
        $plans = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->in($buildIDs)->andWhere('deleted')->ne(1)->fetchAll('id');
        return $plans;
    }

    /**
     * 忽略待处理人
     *
     * @param $buildID
     * @return array|bool
     */
    public function ignore($buildID){
        $build = $this->getById($buildID);
        $params = new stdClass();
        $params->dealuser = '';
        $this->dao->update(TABLE_BUILD)->data($params)->where('id')->eq($buildID)->exec();
        if(!dao::isError()) {
            return common::createChanges($build, $params);
        }
        return false;
    }

    /**
     * Get pairs by id.
     *
     * @param  array  $buildIdList
     * @access public
     * @return array
     */
    public function getPairsById($buildIdList)
    {
        $builds = $this->dao->select('id,name')->from(TABLE_BUILD)->where('id')->in($buildIdList)->andWhere('deleted')->eq('0')->fetchPairs();
        return $builds;
    }

    public function getPairsByJoins($app = '', $projects = '', $products = '', $branch = 0, $params = 'noterminate, nodone', $replace = true)
    {
        if($products == 'na' || $products === 0 || $products === '0') $products = 99999;

        $sysBuilds = array();
        if(strpos($params, 'noempty') === false) $sysBuilds = array('' => '');
        if(strpos($params, 'notrunk') === false) $sysBuilds = $sysBuilds + array('trunk' => $this->lang->trunk);

        $productBuilds = $this->dao->select('t1.id, t1.name, t3.id as releaseID, t3.status as releaseStatus, t4.name as branchName')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_RELEASE)->alias('t3')->on('t1.id = t3.build')
            ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
            ->where(1)
            ->beginIF($app)->andWhere('t1.app')->in($app)->fi()
            ->beginIF($projects)->andWhere('t1.project')->in($projects)->fi()
            ->beginIF($products != 'all')->andWhere('t1.product')->in($products)->fi()
            ->beginIF($branch)->andWhere('t1.branch')->in("0,$branch")->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.date desc, t1.id desc')
            ->fetchAll('id');

        /* Set builds and filter done projects and terminate releases. */
        $builds = array();
        foreach($productBuilds as $key => $build)
        {
            if((strpos($params, 'noterminate') !== false) and ($build->releaseStatus === 'terminate')) continue;
            $builds[$key] = ((strpos($params, 'withbranch') !== false and $build->branchName) ? $build->branchName . '/' : '') . $build->name;
        }

        if(!$builds) return $sysBuilds;

        /* if the build has been released and replace is true, replace build name with release name. */
        if($replace)
        {
            $releases = $this->dao->select('build, name')->from(TABLE_RELEASE)
                ->where('build')->in(array_keys($builds))
                ->andWhere('deleted')->eq(0)
                ->fetchPairs();

            foreach($releases as $buildID => $releaseName) $builds[$buildID] = ((strpos($params, 'withbranch') !== false and $productBuilds[$buildID]->branchName) ? $productBuilds[$buildID]->branchName . '/' : '') . $releaseName;
        }

        return $sysBuilds + $builds;
    }

    /**
     * 增加项目评审白名单
     *
     * @param $projectId
     * @params $reviewId
     * @param $userAccount
     * @return false|void
     */
    public function addProjectReviewWhitelist($projectId, $buildId, $userAccount){
        if(!($projectId && $userAccount)){
            return false;
        }
        //$reason = 1002;
        //检查是否有项目权限
        $res = $this->loadModel('project')->checkOwnProjectPermission($projectId, $userAccount, $buildId, self::BUILD_REASON);
        if($res){
            return true;
        }
        $res = $this->loadModel('project')->addProjectWhitelistInfo($projectId,  $userAccount, $buildId, self::BUILD_REASON);
        return $res;
    }
    /**
     * @param array $ids
     * 判断制版是否经过系统部审核
     * return false|void
     */
    public function checkSystemPass($ids){
        $result = true;
        return $result;
        // 20240328 紧急需求，去掉系统部审核节点
        $list = $this->dao->select('t2.id,t2.name')->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')
            ->on('t1.build = t2.id')
            ->where('t1.id')->in($ids)
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            // 不再判断是否勾选需要系统部审核
            // ->andWhere('systemverify')->eq('1')
            ->fetchall();
        // 判断需要系统部审核的是否经过系统部审核
        foreach ($list as $key => $value) {
            $res = $this->dao->select('id')->from(TABLE_CONSUMED)
                ->where('objectType')->eq('build')
                ->andWhere('objectID')->eq($value->id)
                ->andWhere('`after`')->eq('verifysuccess')
                ->andWhere('`before`')->eq('waitverifyapprove')
                ->andWhere('deleted')->eq('0')
                ->orderBy('id_desc')
                ->fetch();
            if (!$res){
                $result = false;
            }
        }
        // false 制版未经过系统部审核，生产变更需要系统部审核
        return $result;
    }

    /**
     * 设置质量门禁
     *
     * @param $buildInfo
     * @param $newBuildID
     * @param string $op
     * @return bool
     */
    public function setQualityGate($buildInfo, $newBuildID, $op = 'create'){
        $this->loadModel('qualitygate');
        if($op != 'create'){ //编辑制版信息
            $qualityGateInfo = $this->qualitygate->getQualityGateInfoByBuildId($newBuildID, '*');
            if($qualityGateInfo){
                $qualityGateId = $qualityGateInfo->id;
                $updateParams = new stdClass();
                if($qualityGateInfo->projectId != $buildInfo->project){
                    $updateParams->projectId = $buildInfo->project;
                }
                if($qualityGateInfo->projectId != $buildInfo->project){
                    $updateParams->projectId = $buildInfo->project;
                }
                if($qualityGateInfo->productId != $buildInfo->product){
                    $updateParams->productId = $buildInfo->product;
                }
                if($qualityGateInfo->productVersion != $buildInfo->version){
                    $updateParams->productVersion = $buildInfo->version;
                }
                if(!empty((array) $updateParams)){ //项目、产品、产品版本发生了变化
                    $updateParams->severityTestUser = $buildInfo->severityTestUser;
                    $updateParams->status           = $this->lang->qualitygate->statusArray['waitconfirm']; //重新修改状态
                    $ret = $this->qualitygate->update($qualityGateId, $updateParams, false);
                    if(dao::isError()){
                        return false;
                    }
                }
                return true;
            }
        }
        //查询是否存在，存在则更新，不存在则新增
        $projectId = $buildInfo->project;
        $productId = $buildInfo->product;
        $productVersion = $buildInfo->version;
        $buildId = 0;
        $qualityGateInfo = $this->qualitygate->getOneQualityGateInfo($projectId, $productId, $productVersion, $buildId, 0, '*');
        if($qualityGateInfo){ //修改
            $qualityGateId = $qualityGateInfo->id;
            $updateParams = new stdClass();
            $updateParams->buildId = $newBuildID;
            $ret = $this->qualitygate->update($qualityGateId, $updateParams, false);
            if(dao::isError()){
                return false;
            }
        }else{ //新增
            $params =  new stdClass();
            $params->projectId        = $projectId;
            $params->productId        = $productId;
            $params->productVersion   = $productVersion;
            $params->buildId          = $newBuildID;
            $params->severityTestUser = $buildInfo->severityTestUser;
            $params->status           = $this->lang->qualitygate->statusArray['waitconfirm'];
            $params->createdBy        = $op == 'create' ?  $buildInfo->createdBy : $buildInfo->editedBy;
            //创建
            $qualityGateId = $this->qualitygate->create($params);
            if(dao::isError()){
                return false;
            }
        }
        return true;
    }

    /**
     * 是否显示质量门禁
     *
     * @param $buildInfo
     * @return bool
     */
    public function getIsQualityGate($buildInfo){
        $isQualityGate = false;
        $productVersion = $buildInfo->version;
        if($productVersion && $productVersion != 1){
            $projectID = $buildInfo->project;
            $isSetQualityGate =  $this->loadModel('qualitygate')->getIsSetQualityGate($projectID);
            if($isSetQualityGate){
                $buildId = $buildInfo->id;
                $qualityGateInfo = $this->loadModel('qualitygate')->getQualityGateInfoByBuildId($buildId);
                if($qualityGateInfo){
                    $isQualityGate = true;
                }
            }
        }
        return $isQualityGate;
    }

    /**
     * 设置版本bug快照
     *
     * @param $buildID
     * @param $projectId
     * @param int $productId
     * @param $productVersion
     * @return bool
     */
    public function setBuildBugPhoto($buildID, $projectId, $productId = 0, $productVersion = ''){
        if(!($buildID && $projectId)){
            return false;
        }
        $bugList = $this->loadModel('qualitygate')->getSeverityGateUnClosedBugList($projectId, $productId, $productVersion);
        if(empty($bugList)){
            return true;
        }
        foreach ($bugList as $params){
            $params->buildId     = $buildID;
            $params->createdBy   = $this->app->user->account;
            $params->createdTime = helper::now();
            $this->dao->insert(TABLE_BUILD_BUG_PHOTO)->data($params)
                ->autoCheck()
                ->exec();
        }
        return true;
    }

}
