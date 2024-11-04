<?php
/**
 * Project: chengfangjinke
 * Method: createBuild
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 11:24
 * Desc: This is the code comment. This method is called createBuild.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $projectID
 * @return mixed|void
 */
public function createBuild($projectID)
{
    $build = new stdclass();
    $build->stories = '';
    $build->bugs    = '';
    $build = fixer::input('post')
        ->setDefault('project', $projectID)
        ->setDefault('product', 0)
        ->setDefault('branch', 0)
        ->setDefault('status', 'build')
        ->setDefault('dealuser', $this->post->builder)
        ->setDefault('demandid', '')
        ->setDefault('problemid', '')
        ->setDefault('sendlineId', '')
        ->setDefault('createdBy', $this->app->user->account)
        ->setDefault('createdDate', helper::now())
        ->setDefault('sendlineId', '')
        ->cleanInt('product,branch')
        //->add('project', (int)$projectID)
        ->stripTags($this->config->build->editor->create['id'], $this->config->allowedTags)
        ->remove('resolvedBy,allchecker,files,labels,uid,desc,issubmit')
        ->get();
    if($this->post->systemverify && !$this->post->verifyUser){
        return dao::$errors['verifyUser'] = $this->lang->build->verifyUserEmpty;
    }
    $isSetSeverityTestUser =  $this->loadModel('qualitygate')->getIsSetQualityGate($projectID);
    if($isSetSeverityTestUser){
        if(!isset($build->severityTestUser) || !$build->severityTestUser){
            return dao::$errors['severityTestUser'] = sprintf($this->lang->build->checkOpResultList['fieldEmpty'], $this->lang->build->severityTestUser) ;
        }
    }
    $users = $this->loadModel('user')->getPairs('noclosed');
    $comment = '';
    $pinfo = $this->dao->select('id,skipBuild,piplinePath,code')->from(TABLE_PRODUCT)->where('id')->eq($build->product)->fetch();
    $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $build->product, 0);
    if(isset($pinfo) && ($build->version == '1' || ($build->product != '99999' && $pinfo->skipBuild == '1'))){ //跳过制版，进入待测试
        $build->status   = 'waittest';
        $build->dealuser =  $build->testUser;
        if($build->version == '1'){ //产品版本是无
            $comment = sprintf($this->lang->build->noskipTip, $build->svnPath);
        }else if($build->product != '99999' && $build->version != '1' && isset($pinfo) && $pinfo->skipBuild == '1'){
            $comment = sprintf($this->lang->build->skipTip,$pinfo->piplinePath);
        }
    }
    if($build->product != '99999' && $build->version != '1'){
        $build->name = $pinfo->code.'-'.zget($plans,$build->version,'');
    }else if($build->product == '99999' || $build->version == '1'){
        $apps =  $this->loadModel('application')->getapplicationCodePairs();
        $app = zget($apps,$build->app,'');
        $build->name = $app.'-'.$this->lang->build->noProductUpdate;
    }
    //以下：报工取消关联具体所属任务
    /*$executAndTask = array_filter(explode(',',$this->post->taskid));
    $build->execution = isset($executAndTask[0]) ? $executAndTask[0] : 0;
    $build->taskid  = isset($executAndTask[1]) ? $executAndTask[1] : 0;

    $build->taskName = $this->post->taskname;
    unset($build->taskname);
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
    $tasks = $this->loadModel('task')->getExecutionTask($build->app,$projectID,$build->product,$build->version,array_merge(explode(',',$build->demandid),explode(',',$build->problemid),explode(',',$build->sendlineId)));
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

    $build->name = $build->name ? $build->name.'-'.date('His') :'';
    $build->code = isset($pinfo->code) ? $pinfo->code : '';
    $build = $this->loadModel('file')->processImgURL($build, $this->config->build->editor->create['id'], $this->post->uid);
    $this->dao->insert(TABLE_BUILD)->data($build)
                                   ->autoCheck()
                                   ->batchCheck($this->config->build->create->requiredFields, 'notempty')
                                   ->check('name', 'unique', "product = {$build->product} AND branch = {$build->branch} AND deleted = '0'")
                                   ->exec();
    $descBak = $this->post->desc;
    $buildID = $this->dao->lastInsertID();
    if(!dao::isError())
    {
        $progress = $this->lang->build->htmlCode . helper::now() . ' 由<strong>' . zget($users,$build->builder,'') . '</strong>新增' . '（'.$this->lang->build->buildId.'：'.$buildID.'）'.  '</span><br>' . $comment;
        $build->desc = $progress;
        $this->dao->update(TABLE_BUILD)->set('desc')->eq($build->desc)->where('id')->eq($buildID)->exec();

        $this->file->updateObjectID($this->post->uid, $buildID, 'build');
        $this->file->saveUpload('build', $buildID);
        $this->loadModel('score')->create('build', 'create', $buildID);
        //工作量默认0.1
        $this->loadModel('consumed')->record('build', $buildID, '0', $this->app->user->account, '', 'build', array());
        //跳过制版
        if(isset($pinfo) && ($build->version == '1' || ($build->product != '99999' && $pinfo->skipBuild == '1'))){
            $this->loadModel('consumed')->record('build', $buildID, '0', $build->builder, 'build', 'waittest', array());
        }
        if($build->status == 'waittest'){
            $nowBuild = $this->getByBuildID($buildID);
            $this->createTestTask($nowBuild);
        }
        //是否需要生成质量门禁
        if($isSetSeverityTestUser && $build->severityTestUser && $build->version > 1){
            $this->setQualityGate($build, $buildID, 'create');
        }
        //以下：报工取消自动记录任务工作量
        //任务记录工作量
      /*  $build =  $this->getByBuildID($buildID);
        $date = date('Y-m-d',strtotime($build->createdDate));
        $consumedres = $this->loadModel('consumed')->getObjectByID($buildID,'build','build');
        $consumed = array( $consumedres->consumed);
        $work = zget($this->lang->build->descList,'build','');
        $account = array($build->createdBy);
        $taskid = $build->taskid;
        $consumedid = $consumedres->id;
        $this->createEstimate( $taskid,$date,$work,$account,$consumed,$buildID,$consumedid);
        $this->post->desc = $descBak;*/
    }
    return $buildID;
}

public function saveBuild($projectID)
{
    $build = new stdclass();
    $build->stories = '';
    $build->bugs    = '';

    $build = fixer::input('post')
        ->setDefault('project', $projectID)
        ->setDefault('product', 0)
        ->setDefault('branch', 0)
        ->setDefault('status', 'wait')
        ->setDefault('dealuser', $this->app->user->account)
        ->setDefault('demandid', '')
        ->setDefault('problemid', '')
        ->setDefault('sendlineId', '')
        ->setDefault('createdBy', $this->app->user->account)
        ->setDefault('createdDate', helper::now())
        ->setDefault('sendlineId', '')
        ->cleanInt('product,branch')
        //->add('project', (int)$projectID)
        ->stripTags($this->config->build->editor->create['id'], $this->config->allowedTags)
        ->remove('resolvedBy,allchecker,files,labels,uid,desc,issubmit')
        ->get();

    $pinfo = $this->dao->select('id,skipBuild,piplinePath,code')->from(TABLE_PRODUCT)->where('id')->eq($build->product)->fetch();
    $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $build->product, 0);

    //产品号
    $productCode = '';
    $piplinePath = '';
    if(isset($pinfo)){
        $productCode = isset($pinfo->code) ? $pinfo->code : '';
        $piplinePath = isset($pinfo->piplinePath) ? $pinfo->piplinePath: '';
    }
    if(isset($pinfo) && ($build->version == '1' || ($build->product != '99999' && $pinfo->skipBuild == '1'))){

        $users = $this->loadModel('user')->getPairs('noclosed');
        if($build->version == '1'){
            $comment = sprintf($this->lang->build->noskipTip,$build->svnPath);
        }else if($build->product != '99999' && $build->version != '1' && $pinfo->skipBuild == '1'){
            $comment = sprintf($this->lang->build->skipTip, $piplinePath);
        }
    }

    if($build->product != '99999' && $build->version != '1'){
        $build->name = $productCode .'-'.zget($plans,$build->version,'');
    }else if($build->product == '99999' || $build->version == '1'){
        $apps =  $this->loadModel('application')->getapplicationCodePairs();
        $app = zget($apps,$build->app,'');
        $build->name = $app.'-'.$this->lang->build->noProductUpdate;
    }
    /*$executAndTask = array_filter(explode(',',$this->post->taskid));
    $build->execution = isset($executAndTask[0]) ? $executAndTask[0] : 0;
    $build->taskid  = isset($executAndTask[1]) ? $executAndTask[1] : 0;

    $build->taskName = $this->post->taskname;
    unset($build->taskname);
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
    $tasks = $this->loadModel('task')->getExecutionTask($build->app,$projectID,$build->product,$build->version,array_merge(explode(',',$build->demandid),explode(',',$build->problemid),explode(',',$build->sendlineId)));
    if($tasks){
        foreach ($tasks as $key => $task) {
            $build->execution .= $key .',';
            $build->taskid .= implode(',',array_keys($task)).',' ;
            $build->taskName .= implode(',',array_values($task)).',';
        }
    }
    unset( $build->demandChosen,$build->problemChosen,$build->sendlineChosen);
   /* if(empty($build->demandid) && empty($build->problemid) && empty($build->sendlineId)){
        return dao::$errors['demandid'] = $this->lang->build->demandAndProblemAndSecondEmpty;
    }*/
    $build->name = $build->name ? $build->name.'-'.date('His') :'';
    $build->code = isset($pinfo->code) ? $pinfo->code : '';
    $build = $this->loadModel('file')->processImgURL($build, $this->config->build->editor->create['id'], $this->post->uid);
    $this->dao->insert(TABLE_BUILD)->data($build)
        ->autoCheck()
        ->exec();
    $descBak = $this->post->desc;
    $buildID = $this->dao->lastInsertID();
    if(!dao::isError())
    {
        $this->file->updateObjectID($this->post->uid, $buildID, 'build');
        $this->file->saveUpload('build', $buildID);
    }
    return $buildID;
}

/**
 * Project: chengfangjinke
 * Method: getProductBuildPairs
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 11:25
 * Desc: This is the code comment. This method is called getProductBuildPairs.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $products
 * @param int $branch
 * @param string $params
 * @param bool $replace
 * @return array|string[]
 */
public function getProductBuildPairs($products, $branch = 0, $params = 'noterminate, nodone', $replace = true)
{
    $sysBuilds = array();
    if(strpos($params, 'noempty') === false) $sysBuilds = array('' => '');
    if(strpos($params, 'notrunk') === false) $sysBuilds = $sysBuilds + array('trunk' => $this->lang->trunk);

    if($products == 'na' || empty($products)) return [];

    $productBuilds = $this->dao->select('t1.id, t1.name, t3.id as releaseID, t3.status as releaseStatus, t4.name as branchName')->from(TABLE_BUILD)->alias('t1')
                          ->leftJoin(TABLE_RELEASE)->alias('t3')->on('t1.id = t3.build')
                          ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
                          ->where('t1.product')->in($products)
                          ->beginIF($branch)->andWhere('t1.branch')->in("0,$branch")->fi()
                          ->andWhere('t1.deleted')->eq(0)
                          ->orderBy('t1.date desc, t1.id desc')->fetchAll('id');

    /* Set builds and filter done projects and terminate releases. */
    $builds = array();
    foreach($productBuilds as $key => $build)
    {
        //if(empty($build->releaseID) and (strpos($params, 'nodone') !== false) and ($build->projectStatus === 'done')) continue;
        if((strpos($params, 'noterminate') !== false) and ($build->releaseStatus === 'terminate')) continue;
        $builds[$key] = ((strpos($params, 'withbranch') !== false and $build->branchName) ? $build->branchName . '/' : '') . $build->name;
    }

    if(!$builds) return $sysBuilds;

    /* if the build has been released and replace is true, replace build name with release name. */
    if($replace)
    {
        $releases = $this->dao->select('build, name')->from(TABLE_RELEASE)
            ->where('build')->in(array_keys($builds))
            ->andWhere('product')->in($products)
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();

        foreach($releases as $buildID => $releaseName) $builds[$buildID] = ((strpos($params, 'withbranch') !== false and $productBuilds[$buildID]->branchName) ? $productBuilds[$buildID]->branchName . '/' : '') . $releaseName;
    }

    return $sysBuilds + $builds;
}

/**
 * Project: chengfangjinke
 * Method: getProjectStories
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 11:25
 * Desc: This is the code comment. This method is called getProjectStories.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param int $projectID
 * @param int $productID
 * @param int $branch
 * @param string $orderBy
 * @param string $type
 * @param int $param
 * @param string $storyType
 * @param string $excludeStories
 * @param null $pager
 * @return array|mixed
 */
public function getProjectStories($projectID = 0, $productID = 0, $branch = 0, $orderBy = 't1.`order`_desc', $type = 'byModule', $param = 0, $storyType = 'story', $excludeStories = '', $pager = null)
{
    if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getExecutionStories();

    if(!$projectID) return array();
    $project = $this->dao->findById($projectID)->from(TABLE_PROJECT)->fetch();

    $type = strtolower($type);
    if($type == 'bysearch')
    {
        if($this->app->rawModule == 'projectstory') $this->session->projectStoryQuery = $this->session->storyQuery;
        $queryID  = (int)$param;
        $products = $this->loadModel('project')->getProducts($projectID);

        if($this->session->projectStoryQuery == false) $this->session->set('projectStoryQuery', ' 1 = 1');
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('projectStoryQuery', $query->sql);
                $this->session->set('projectStoryForm', $query->form);
            }
        }

        $allProduct = "`product` = 'all'";
        $storyQuery = $this->session->projectStoryQuery;
        if(strpos($this->session->projectStoryQuery, $allProduct) !== false)
        {
            $storyQuery = str_replace($allProduct, '1', $this->session->projectStoryQuery);
        }
        $storyQuery = preg_replace('/`(\w+)`/', 't2.`$1`', $storyQuery);

        $stories = $this->dao->select('distinct t1.*, t2.*, t3.branch as productBranch, t4.type as productType, t2.version as version')->from(TABLE_PROJECTSTORY)->alias('t1')
                        ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
                        ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t1.project = t3.project')
                        ->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t2.product = t4.id')
                        ->where($storyQuery)
                        ->andWhere('t1.project')->eq((int)$projectID)
                        ->andWhere('t2.deleted')->eq(0)
                        ->andWhere('t2.type')->eq($storyType)
                        ->beginIF($excludeStories)->andWhere('t2.id')->notIN($excludeStories)->fi()
                        ->orderBy($orderBy)
                        ->page($pager, 't2.id')
                        ->fetchAll('id');
    }
    else
    {
        $productParam = ($type == 'byproduct' and $param) ? $param : $this->cookie->storyProductParam;
        $branchParam  = ($type == 'bybranch'  and $param) ? $param : $this->cookie->storyBranchParam;
        $moduleParam  = ($type == 'bymodule'  and $param) ? $param : $this->cookie->storyModuleParam;
        $modules      = empty($moduleParam) ? array() : $this->dao->select('*')->from(TABLE_MODULE)->where('path')->like("%,$moduleParam,%")->andWhere('type')->eq('story')->andWhere('deleted')->eq(0)->fetchPairs('id', 'id');
        if(strpos($branchParam, ',') !== false) list($productParam, $branchParam) = explode(',', $branchParam);

        $unclosedStatus = $this->lang->story->statusList;
        unset($unclosedStatus['closed']);

        $stories = $this->dao->select('distinct t1.*, t2.*,t3.branch as productBranch,t4.type as productType,t2.version as version')->from(TABLE_PROJECTSTORY)->alias('t1')
                        ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
                        ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t3')->on('t1.project = t3.project')
                        ->leftJoin(TABLE_PRODUCT)->alias('t4')->on('t2.product = t4.id')
                        ->where('t1.project')->eq((int)$projectID)
                        ->andWhere('t2.type')->eq($storyType)
                        ->beginIF($excludeStories)->andWhere('t2.id')->notIN($excludeStories)->fi()
                        ->beginIF($project->type == 'project')
                        ->beginIF(!empty($productID))->andWhere('t1.product')->eq($productID)
                        ->beginIF($type == 'bybranch')->andWhere('t2.branch')->eq($branch)->fi()
                        ->beginIF(strpos('changed|closed', $type) !== false)->andWhere('t2.status')->eq($type)->fi()
                        ->beginIF($type == 'unclosed')->andWhere('t2.status')->in(array_keys($unclosedStatus))->fi()
                        ->fi()
                        ->beginIF($project->type != 'project')
                        ->beginIF(!empty($productParam))->andWhere('t1.product')->eq($productParam)->fi()
                        ->beginIF($this->session->projectStoryBrowseType == 'unclosed')->andWhere('t2.status')->in(array_keys($unclosedStatus))->fi()
                        ->fi()
                        ->beginIF(!empty($branchParam))->andWhere('t2.branch')->eq($branchParam)->fi()
                        ->beginIF($modules)->andWhere('t2.module')->in($modules)->fi()
                        ->andWhere('t2.deleted')->eq(0)
                        ->orderBy($orderBy)
                        ->page($pager, 't2.id')
                        ->fetchAll('id');
    }

    $query    = $this->dao->get();
    $branches = array();
    foreach($stories as $story)
    {
        if(empty($story->branch) and $story->productType != 'normal') $branches[$story->productBranch][$story->id] = $story->id;
    }
    foreach($branches as $branchID => $storyIdList)
    {
        $stages = $this->dao->select('*')->from(TABLE_STORYSTAGE)->where('story')->in($storyIdList)->andWhere('branch')->eq($branchID)->fetchPairs('story', 'stage');
        foreach($stages as $storyID => $stage) $stories[$storyID]->stage = $stage;
    }

    $this->dao->sqlobj->sql = $query;
    return $this->loadModel('story')->mergePlanTitle($productID, $stories, $branch, $type);
}

/**发送邮件
 * @param $buildID
 * @param $actionID
 */
public function sendmail($buildID, $actionID)
{
    $this->loadModel('mail');
    $build = $this->loadModel('build')->getByID($buildID);
    $users  = $this->loadModel('user')->getPairs('noletter');
    $product =  array('0' => '') + $this->loadModel('product')->getPairs();
    $application =  array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
    $version =  array('0' => '','1'=>'无') + $this->loadModel('productplan')->getPairs($build->product);

    /* 获取后台通知中配置的邮件发信。*/
    $this->app->loadLang('custommail');
    $mailConf   = isset($this->config->global->setBuildMail) ? $this->config->global->setBuildMail : '{"mailTitle":"","variables":[],"mailContent":""}';
    $mailConf   = json_decode($mailConf);

    /* 处理邮件发信的标题和日期*/
    $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

    /* Get action info. */
    $action          = $this->loadModel('action')->getById($actionID);
    $history         = $this->action->getHistory($actionID);
    $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

    /* Get mail content. */
    $oldcwd     = getcwd();
    $modulePath = $this->app->getModulePath($appName = '', 'build');
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

    /*处理收件人*/
    $toList = $build->dealuser;
    $ccList = '';
    if($build->status == 'waitdeptmanager'){ //待特批制版审批
        $mailTitle = $this->lang->build->dealSpecialPassMailTitle;
        $ccList = $build->severityTestUser; //抄送人
    }

    /* 处理邮件标题*/
    $subject = $mailTitle;
    /* Send emails. */
    //状态除待制版外发邮件
    if($build->status != 'waittest'){
        $this->mail->send($toList, $subject, $mailContent, $ccList);
    }
    if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
}

/**
 * @param $buildID
 * @param bool $showFile
 * @return mixed
 */
public function getByBuildID($buildID)
{
    $build = $this->dao->select("*")->from(TABLE_BUILD)->where('id')->eq($buildID)->fetch();

    $build = $this->loadModel('file')->replaceImgURL($build, 'comment,plateName');

    $build = $this->getConsumed($build);
    return $build;
}

/**
 *  获取工时投入信息。
 * @param $build
 * @return array
 */

public function getConsumed($build)
{
    if (empty($build)) return array();

    $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('build')
        ->andWhere('objectID')->eq($build->id)
        ->andWhere('parentID')->eq('0')
        ->andWhere('deleted')->eq(0)
        ->fetchAll();

    $build->consumed = $cs;
    return $build;
}

/**
 * 处理
 * @param $buildID
 * @return array|bool
 */
public function deal($buildID)
{

    $oldBuild = $this->getByBuildID($buildID);
    if($this->post->oldstatus != $oldBuild->status ){
        return dao::$errors = array('' =>  $this->lang->build->nowStatusError );
    }
   /* if(!$this->post->consumed)
    {
        return dao::$errors['consumed'] = $this->lang->demand->consumedEmpty;
    }*/
    if(!$this->post->status){
       // return dao::$errors['result'] = $this->lang->build->resultEmpty;
        return dao::$errors = array('status' => $oldBuild->status != 'waitverifyapprove' ? $this->lang->build->resultEmpty :$this->lang->build->approveResultEmpty );
    }
    //工作量必须是数字
   /* $consumed = $this->post->consumed;
    if($consumed != 0 || $consumed != '0.0'){
        $checkRes = $this->loadModel('consumed')->checkConsumedInfo($consumed);
        if(!$checkRes){
            return false;
        }
    }*/
   /* //增加配合人员不能重复 的验证
    $count = count($this->post->relevantUser);
    $uniquecount = count(array_unique($this->post->relevantUser));
    if ($count > $uniquecount) {
        return dao::$errors['relevantUser'] = $this->lang->build->relevantDeptRepeat;
    }*/
    // 单选改多选后，数组的第一个值为空。去除第一个值，否则参数校验不能通过
    if(isset($_POST['relevantUser'])){
        $temp = $_POST['relevantUser'];
        $_POST['relevantUser'] = array_slice($temp, 1, count($temp));
    }

    $isWarn = $_POST['isWarn']; //是否有警告信息
    $warnMsg = '';
    $oldStatus = $this->post->oldstatus;
    $status   = $this->post->status;
    $deptManagerUsers  = '';
   //项目id
   $projectId = $oldBuild->project;
   //产品id
   $productId = $oldBuild->product;
   $productVersion = $oldBuild->version;
   if($oldStatus == 'waittest'){ //测试环节
       $isQualityGate = $this->getIsQualityGate($oldBuild);
       if($isQualityGate) {
           $this->loadModel('qualitygate');
           $qualityGateInfo = $this->qualitygate->getQualityGateInfoByBuildId($oldBuild->id, 'id, status');
           $severityTestResult = $qualityGateInfo->status; //安全测试结果
           switch ($severityTestResult){
               case $this->lang->qualitygate->statusArray['waitconfirm']: //待确认
                   if($status == 'waitverify'){ //待测试时选择测试通过
                       return dao::$errors['status'] = $this->lang->build->checkOpResultList['severityTestResultError'];
                   }
                   break;

               case $this->lang->qualitygate->statusArray['finish']: //已完成
                   if($status == 'waitverify' || $status == 'waitdeptmanager') { //待测试时选择测试通过
                       $severityGateResult = $this->qualitygate->getSeverityGateResult($projectId, $productId, $productVersion, $buildID);
                       if($severityGateResult == 2){ //安全门禁不通过
                           if($isWarn == 'yes'){
                               $warnMsg = $this->lang->build->checkOpResultList['severityGateResultWarn'];
                           }else{
                               //获得下一步操作状态和待处理人
                               $deptManagerUsers = $this->loadModel('project')->getProjectDeptManagerUsers($projectId);
                               if(!$deptManagerUsers){
                                   return dao::$errors[''] = $this->lang->build->checkOpResultList['deptManagerUserEmptyError'];
                               }
                               $this->post->status = 'waitdeptmanager'; //待部门负责人审批
                           }
                       }else{ //安全门禁没有或者已经通过
                           $this->post->status = 'waitverify'; //待验证
                       }
                   }
                   break;

               case $this->lang->qualitygate->statusArray['noneedtest']: //无需测试，不需要处理
                   break;

               default:
                   break;

           }
       }

   }
    //检查关配合人员工作量信息
    $checkRes = $this->loadModel('consumed')->checkPostDetails();
    if (!$checkRes) {
        return dao::$errors;
    }
    if(in_array($this->post->status,array('verifyfailed' , 'waitverifyapprove'))){
        $actualVerifyUser = trim(implode(',',array_filter($this->post->actualVerifyUser)),'');
        if(!$actualVerifyUser ){
            return dao::$errors['actualVerifyUser'] = $this->lang->build->actualVerifyUserEmpty;
        }
        if(!$this->post->actualVerifyDate){
            return dao::$errors['actualVerifyDate'] = $this->lang->build->actualVerifyDateEmpty;
        }
        if(is_array($this->post->verifyFiles) && count($this->post->verifyFiles)){
            return dao::$errors = array('verifyFiles' => $this->lang->build->verifyFilesEmpty);
        }
        unset($this->post->files);
    }
    if(!$this->post->comment && $this->post->status != 'verifysuccess' && !($oldBuild->status == 'waitdeptmanager' && $this->post->status == 'waitverify')){
        if($this->post->status == 'waitdeptmanager'){  //下一步部门负责人特批处理
            return dao::$errors['comment'] = sprintf($this->lang->build->checkOpResultList['fieldEmpty'], $this->lang->build->specialPassReason);
        }else{
            return dao::$errors = array('comment' => $oldBuild->status != 'waitverifyapprove' ? $this->lang->build->commentEmpty : $this->lang->build->approveOpinionEmpty);
        }
    }
    //警告信息
    if($warnMsg){
        dao::$warns[''] = $warnMsg;
        return dao::$warns;
    }


    $waitverifyapproveUser = $this->lang->build->leaderList['users'];
    $data = fixer::input('post')
        ->setIF($this->post->status == 'waittest', 'dealuser', $oldBuild->testUser)
        ->setIF($this->post->status == 'waitdeptmanager', 'dealuser', $deptManagerUsers) //质量门禁的测试已通过到待部门负责人审批
        ->setIF($this->post->status == 'waitverify', 'dealuser', $oldBuild->builder)  //测试通过待验证
        /*->setIF($this->post->status == 'testsuccess', 'dealuser', $oldBuild->verifyUser)*/
        ->setIF($this->post->status == 'verifysuccess', 'dealuser', $oldBuild->builder)
        ->setIF($this->post->status == 'testfailed', 'dealuser', $oldBuild->createdBy)  //测试未通过
        ->setIF($this->post->status == 'versionfailed', 'dealuser', $oldBuild->createdBy)
        /*->setIF($this->post->status == 'verifyfailed', 'dealuser', $oldBuild->createdBy)
        ->setIF($this->post->status == 'waitverifyapprove', 'dealuser',  $waitverifyapproveUser)
        ->setIF($this->post->status == 'verifyrejectbacksystem', 'dealuser', $oldBuild->verifyUser)
        ->setIF($this->post->status == 'verifyrejectsubmit', 'dealuser', $oldBuild->createdBy)*/

        ->setIF($this->post->status == 'released', 'dealuser', '')
        ->join('actualVerifyUser', ',')
        ->stripTags($this->config->build->editor->deal['id'], $this->config->allowedTags)
        ->remove('uid,relevantUser,workload,user,consumed,files,verifyFiles,isWarn')
        ->get();
    if(mb_strlen($data->name) > 140){
       return  dao::$errors['name'] = $this->lang->build->NameError;
    }
   // $data->status = $data->status == 'verifyrejectbacksystem' ? 'testsuccess' :  $data->status;
   // $this->post->status = $data->status == 'verifyrejectbacksystem' ? 'testsuccess' :  $data->status;
    if($data->status == 'verifyrejectbacksystem'){
        $backStatus = 'testsuccess';
        $data->verifyRejectBack = $oldBuild->verifyRejectBack +1;
    }
    $data->lastDealDate = helper::today();
    if($this->post->status != 'waittest'){
       // unset($data->name);
        unset($data->filePath);
    }
    if($this->post->status == 'released'){
        if(!$this->post->releaseName){
            return dao::$errors['releaseName'] = $this->lang->build->releaseNameEmpty;
        }
        $checkreleaseName = $this->dao->select('name')->from(TABLE_RELEASE)->where('name')->eq($this->post->releaseName)->fetch();
        if($checkreleaseName){
            return dao::$errors['releaseName'] = sprintf($this->lang->build->existBuild,$checkreleaseName->name) ;
        }
        if(!$this->post->releasePath){
           return dao::$errors['releasePath'] = $this->lang->build->releasePathEmpty;
        }
        // 截掉 /ftpdatas 前缀
        if(strpos($this->post->releasePath,'/ftpdatas') === 0) {
            $this->post->releasePath = substr($this->post->releasePath,9);
        }
        $config = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('mediaCheckList')->fetchPairs('key');
        if($config['release'] == 1) { //校验开关
            if($this->loadModel('projectrelease')->checkPath($this->post->releasePath) == false) return false;
        }
        if(!$this->post->plateName){
            return dao::$errors['plateName'] = $this->lang->build->plateNameEmpty;
        }
    }
    if($this->post->status == 'waittest'){
        $data->date = helper::today();
        if(!$this->post->name){
            return dao::$errors['name'] = $this->lang->build->nameEmpty;
        }
        if(!$this->post->filePath){
            return dao::$errors['filePath'] = $this->lang->build->filePathEmpty;
        }
    }

    //是否需要重置状态
    $data->status = $this->post->status;
    if($data->status == 'waitdeptmanager'){
        $data->specialPassReason = $data->comment; //特批制版原因
    }
    $data = $this->loadModel('file')->processImgURL($data, $this->config->build->editor->deal['id'], $this->post->uid);
    if(isset($data->comment)){
        $users = $this->loadModel('user')->getPairs('noclosed');
        $progress = $this->lang->build->htmlCode . helper::now() . ' 由<strong>' . zget($users,$this->app->user->account,'') . '</strong>新增' . '（'.$this->lang->build->buildId.'：'.$buildID.'）'.'</span><br>' . $data->comment;
        $data->desc = $oldBuild->desc .'<br>'.$progress;
        unset($data->comment);
    }
    if(in_array($this->post->status,array('waitverify','testfailed', 'waitdeptmanager')) && ($oldStatus != 'waitdeptmanager')){ //测试操作
        //测试配合人员
        $data->testRelevantUser   = empty($_POST['relevantUser']) ? '' :implode(',', array_filter($_POST['relevantUser']));
    }
    if(in_array($this->post->status,array('waitverifyapprove','verifyfailed'))){
        //验证配合人员
        $data->verifyRelevantUser = empty($_POST['relevantUser']) ? '' :implode(',',  array_filter($_POST['relevantUser']));
        //增加项目白名单
        if(!is_array($waitverifyapproveUser)){
            $waitverifyapproveUser = explode(',', $waitverifyapproveUser);
        }
        foreach ($waitverifyapproveUser as $userAccount){
            $res = $this->addProjectReviewWhitelist($oldBuild->project, $buildID, $userAccount);
        }
    }
    unset($data->oldstatus);
    if(isset($data->relevantUser)){
        unset($data->relevantUser);
    }
    $data->status = isset($backStatus) ? $backStatus : $data->status;
        $this->dao->update(TABLE_BUILD)->data($data)->autoCheck()
        ->batchCheck($this->config->build->deal->requiredFields, 'notempty')
        ->where('id')->eq($buildID)
        ->exec();
    $this->loadModel('consumed')->record('build', $buildID, '0',$this->app->user->account, $oldBuild->status, $this->post->status, '');
    if(isset($backStatus)){
        $this->loadModel('consumed')->record('build', $buildID, '0',$this->app->user->account, $this->post->status,$backStatus, '');
    }

    $this->file->saveUpload('build', $buildID,'verifyFiles',in_array($this->post->status,array('verifyfailed' , 'waitverifyapprove')) ? 'verifyFiles':'');
    $this->loadModel('file')->updateObjectID($this->post->uid, $buildID, 'build');
    if(!dao::isError()){
        //以下：报工取消自动记录任务工作量
        //任务记录工作量
        $build =  $this->getByBuildID($buildID);

        /*$consumedres = $this->loadModel('consumed')->getObjectByID($buildID,'build',$build->status);
       $date = $build->lastDealDate;
       $consumed = array_filter($this->post->workload);
       $consu = $consumedres->consumed;
       array_unshift( $consumed,$consu);
       $work = zget($this->lang->build->descList,$build->status,'');
       $account = array_filter($this->post->relevantUser);
       $user = $this->app->user->account;
       array_unshift($account,$user);

       $taskid = $build->taskid;
       $consumedid = $consumedres->id;
       $this->createEstimate( $taskid,$date,$work,$account,$consumed,$buildID,$consumedid);*/

        //待测试 测试地址回填问题池、需求池
        if($build->status == 'waittest'){
            $path = helper::now().' 测试地址： '.$build->filePath;
            $demandid =  $oldBuild->demandid;
            $problemid =  $oldBuild->problemid;
            /*if($demandid) {
                $this->backFill($demandid, 'demand', $path);
            }
            if($problemid){
                $this->backFill($problemid,'problem',$path);
            }*/
            $this->fill($demandid,$problemid,$path);
        }

        if(($build->status == 'waitdeptmanager')){ //待部门领导人特批操作时，保存bug快照
            $ret = $this->setBuildBugPhoto($buildID, $projectId, $productId, $productVersion);
        }

        //待上线 发布地址回填问题池、需求池
        if($build->status == 'released'){
            $path = helper::now().' 发布地址： '.$build->releasePath;
            $demandid =  $oldBuild->demandid;
            $problemid =  $oldBuild->problemid;
           /* if($demandid){
                $this->backFill($demandid,'demand',$path);
            }
            if($problemid){
                $this->backFill($problemid,'problem',$path);
            }*/
            $this->fill($demandid,$problemid,$path);
        }
        //创建测试单
        /*$pinfo = $this->dao->select('id,skipBuild')->from(TABLE_PRODUCT)->where('id')->eq($build->product)->fetch();
        //不跳过制版
         if($build->status == 'waittest'  && ($pinfo->skipBuild != '1' || $build->product == '99999')){*/
         if($build->status == 'waittest'){
             $nowBuild = $this->getByBuildID($buildID);
             $this->createTestTask($nowBuild);
         }
         //创建发布
         if($build->status == 'released'){
             $nowBuild = $this->getByBuildID($buildID);
            // $nowBuild->uid = $this->post->uid;
             $this->createRelease($nowBuild);
         }
    }
    return common::createChanges($oldBuild, $data);
}

/**
 * 退回
 * @param $buildID
 */
public function back($buildID){
    if(!$this->post->comment){
        return dao::$errors = array('comment' => $this->lang->build->commentEmpty);
    }

    $oldBuild = $this->getByBuildID($buildID);
    $build    = fixer::input('post')->stripTags($this->config->build->editor->back['id'], $this->config->allowedTags)
        ->setDefault('lastDealDate', date('Y-m-d H:i:s'))
        ->setDefault('dealuser', $oldBuild->createdBy)
        ->setDefault('status', 'back')
        ->setDefault('releaseName', '')
        ->remove('consumed')
        ->get();

    $build = $this->loadModel('file')->processImgURL($build, $this->config->build->editor->back['id'], $this->post->uid);
    if(isset($build->comment)){
        $users = $this->loadModel('user')->getPairs('noclosed');
        $progress = $this->lang->build->htmlCode . helper::now() . ' 由<strong>' . zget($users,$this->app->user->account,'') . '</strong>新增' . '（'.$this->lang->build->buildId.'：'.$buildID.'）'. '</span><br>' . $build->comment;
        $build->desc = $oldBuild->desc .'<br>'.$progress;
        unset($build->comment);
    }

    $this->dao->update(TABLE_BUILD)->data($build)->where('id')->eq($buildID)->exec();
    $this->loadModel('consumed')->record('build', $buildID, '0',$this->app->user->account, $oldBuild->status, 'back', '');
    $this->file->saveUpload('build', $buildID);
    $this->loadModel('file')->updateObjectID($this->post->uid, $buildID, 'build');

    if(!dao::isError())
    {
        //以下：报工取消关联具体所属任务
        //任务记录工作量
       /* $build =  $this->getByBuildID($buildID);
        if($build->taskid){
        $consumedres = $this->loadModel('consumed')->getObjectByIDToMax($buildID,'build',$build->status);
        $date = $build->lastDealDate;
        $consumed = array($consumedres->consumed);
        $work = zget($this->lang->build->descList,$build->status,'');
        $user = array($this->app->user->account);
        $taskid = $build->taskid;
        $consumedid = $consumedres->id;
        $this->createEstimate( $taskid,$date,$work,$user,$consumed,$buildID,$consumedid);
        }*/
        return common::createChanges($oldBuild, $build);
    }
}

/**
 * 新建测试单
 * @param $date
 */
public function createTestTask($data){

    unset($_POST);
    if($data->problemid){
        $problemid = $this->dao->select('id')->from(TABLE_PROBLEM)->where('code')->in($data->problemid)->fetchAll('id');
    }
    if($data->demandid){
        $demandid = $this->dao->select('id')->from(TABLE_DEMAND)->where('code')->in($data->demandid)->fetchAll('id');
    }
    if($data->sendlineId){
        $secondid = $this->dao->select('id')->from(TABLE_SECONDORDER)->where('code')->in($data->sendlineId)->fetchAll('id');
    }
    //如果产品选无且产品除无外仅有一个，则替换为仅有的那个产品
    if($data->product == '99999'){
        $products = $this->loadModel('application')->getAppProducts($data->project,$data->app);
        $data->product = count($products) == 1 ? key($products) : 0 ;
    }

    $_POST['project'] = $data->project;
    $_POST['product'] = $data->product;
    $_POST['build']   = $data->id;
    $_POST['problem'] = isset($problemid) ? array_keys($problemid) : 0;
    $_POST['requirement'] = isset($demandid) ? array_keys($demandid) : 0;
    $_POST['secondorder'] = isset($secondid) ? array_keys($secondid) : 0;
    $_POST['owner']  = $data->testUser;
    $_POST['begin'] = '0000-00-00';
    $_POST['end']   = '0000-00-00';
    $_POST['status'] = 'wait';
    $_POST['name'] = $data->name.'_测试单';
    $_POST['mailto'] = explode(',',$data->createdBy);
    $_POST['applicationID'] = $data->app;
    $testtask = $this->dao->select('*')->from(TABLE_TESTTASK)->where('build')->eq($data->id)->fetch();
    if($testtask){
        $changes = $this->loadModel('testtask')->update($testtask->id);
        if($changes){
            $actionID = $this->loadModel('action')->create('testtask', $testtask->id, 'edited');
            $this->loadModel('action')->logHistory($actionID, $changes);
        }
    }else{
        $taskID = $this->loadModel('testtask')->create( $data->project);
        $this->loadModel('action')->create('testtask', $taskID, 'opened');
    }

}

/**
 * 创建发布
 * @param $data
 */
public function createRelease($data){

    $this->loadModel('projectrelease');
    $this->app->loadConfig('release');
    unset($_POST);
    $_POST['project'] = $data->project;
    $_POST['name']    = $data->releaseName;
    $_POST['build']   = $data->id;
    $_POST['date']    = date('Y-m-d');
    $_POST['product'] = $data->product;
    $_POST['path']    = $data->releasePath;
    $_POST['desc']    = $data->plateName;
    $_POST['mailto']    = implode(',',array_unique(array($data->createdBy,$data->testUser,$data->verifyUser)));
    $_POST['app']     = $data->app;
    $_POST['productVersion']    = $data->version;
    $_POST['createdBy']    = $this->app->user->account;
    $releaseID = $this->projectrelease->create($data->project);
    /*$this->loadModel('file')->updateObjectID($data->uid, $releaseID, 'release');
    $this->file->saveUpload('release', $releaseID);*/
    $now = helper::today();
    $filebuild = $this->dao->select('id')->from(TABLE_FILE)
         ->where('objectID')->eq($data->id)
         ->andWhere('objectType')->eq('build')
         ->andWhere('extra')->ne('editor')
         ->andWhere('addedby')->eq($this->app->user->account)
         ->andWhere('addedDate')->like("$now%")
         ->andWhere('extra')->ne('verifyFiles')
         ->andWhere('deleted')->eq('0')
         ->fetchAll();
    if(isset($filebuild)){
          $filebuildid = array_column($filebuild,'id');
          $this->dao->update(TABLE_FILE)->set('objectID')->eq($releaseID)->set('objectType')->eq('release')->where('id')->in($filebuildid)->exec();
    }
    $this->loadModel('action')->create('release', $releaseID, 'opened');

}

/**
 * @param $buildID
 * @return mixed|void
 */
public function rebuild($buildId)
{
    $oldBuild = $this->getById((int)$buildId);
    $oldBuild->status = 'build';
//    $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
    $oldBuild->dealuser = $oldBuild->builder;
    $oldBuild->createdBy =  $this->app->user->account;
    $oldBuild->createdDate = helper::now();
    $oldBuild->name ='';
    $oldBuild->filePath ='';
    $oldBuild->editedBy ='';
    $oldBuild->editedDate ='';
    $oldBuild->releaseName ='';
    $oldBuild->releasePath ='';
    $oldBuild->plateName ='';
    $oldBuild->date ='';
    $oldBuild->testRelevantUser   = '';
    $oldBuild->verifyRelevantUser = '';
    $oldBuild->actualVerifyUser   = '';
    $oldBuild->actualVerifyDate   = '';
    $oldBuild->verifyRejectBack   = '0';
    $oldBuild->updateFileDate     = '';
    $users = $this->loadModel('user')->getPairs('noclosed');
    $comment = '';
    $pinfo = $this->dao->select('id,skipBuild,piplinePath,code')->from(TABLE_PRODUCT)->where('id')->eq($oldBuild->product)->fetch();
    if(isset($pinfo) && ($oldBuild->version == '1' || ($oldBuild->product != '99999' && $pinfo->skipBuild == '1'))){
        $oldBuild->status   = 'waittest';
        $oldBuild->dealuser =  $oldBuild->testUser;

        $users = $this->loadModel('user')->getPairs('noclosed');
        if($oldBuild->version == '1'){
            $comment = sprintf($this->lang->build->noskipTip,$oldBuild->svnPath);
        }else if($oldBuild->product != '99999' && $oldBuild->version != '1' && $pinfo->skipBuild == '1'){
            $comment = sprintf($this->lang->build->skipTip,$pinfo->piplinePath);
        }
    }
    $plans = array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs( $oldBuild->product, 0);
    if($oldBuild->product != '99999' && $oldBuild->version != '1'){
       $oldBuild->name = $pinfo->code.'-'.zget($plans,$oldBuild->version,'');
      }else if($oldBuild->product == '99999' || $oldBuild->version == '1'){
       $apps =  $this->loadModel('application')->getapplicationCodePairs();
       $app = zget($apps,$oldBuild->app,'');
       $oldBuild->name = $app.'-'.$this->lang->build->noProductUpdate;
    }
    $oldBuild->name = $oldBuild->name ? $oldBuild->name.'-'.date('His') :'';
//    $oldBuild->desc = $this->post->desc;
    unset($oldBuild->id);
    $buildDesc = $oldBuild->desc;
    if($oldBuild->status   != 'waittest'){
       unset($oldBuild->desc);
    }
    unset($oldBuild->executionName);
    unset($oldBuild->productName);
    unset($oldBuild->productType);
    unset($oldBuild->files);
    $oldBuild = $this->loadModel('file')->processImgURL($oldBuild, $this->config->build->editor->rebuild['id'], $this->post->uid);
    $this->dao->insert(TABLE_BUILD)->data($oldBuild)
        ->batchCheck($this->config->build->rebuild->requiredFields, 'notempty')
        ->exec();
    if(!dao::isError())
    {
        $buildID = $this->dao->lastInsertID();
        $file['objectType'] = 'build';
        $file['objectID']   = $buildID;
        $file['addedBy']    = $this->app->user->account;
        $file['addedDate']  = helper::now();
        $this->dao->update(TABLE_FILE)->data($file)->where('objectID')->eq($buildId)->andWhere('extra')->ne('verifyFiles')->exec();

        $this->loadModel('score')->create('build', 'create', $buildID);
        //工作量默认0.1
        $this->loadModel('consumed')->record('build', $buildID, '0', $this->app->user->account, '', 'build', array());
        //跳过制版
        if(isset($pinfo) && ($oldBuild->version == '1' || ($oldBuild->product != '99999' && $pinfo->skipBuild == '1'))){
            $this->loadModel('consumed')->record('build', $buildID, '0', $oldBuild->builder, 'build', 'waittest', array());
        }
        $progress = $this->lang->build->htmlCode . helper::now() . ' 由<strong>' . zget($users,$oldBuild->builder,'') . '</strong>新增' . '（'.$this->lang->build->buildId.'：'.$buildID.'）'.'</span><br>' . $comment;
        $buildDesc = $buildDesc.'<br>'.$progress;
        $this->dao->update(TABLE_BUILD)->set('dealuser')->eq(' ')->where('id')->eq($buildId)->exec();
        $this->dao->update(TABLE_BUILD)->set('desc')->eq($buildDesc)->where('id')->eq($buildID)->exec();
        $projectID = $oldBuild->project;
        $isSetSeverityTestUser =  $this->loadModel('qualitygate')->getIsSetQualityGate($projectID);
        //是否需要生成质量门禁
        if($isSetSeverityTestUser && $oldBuild->severityTestUser && $oldBuild->version > 1){
            $this->setQualityGate($oldBuild, $buildID, 'create');
        }

        return $buildID;
    }
}

/**
 * @param $objectID
 * @return mixed
 */
public function getConsumedsByID($objectID)
{
    $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('build')
        ->andWhere('objectID')->eq($objectID)
        ->andWhere('parentID')->eq('0')
        ->andWhere('deleted')->eq(0)
        ->orderBy('id_asc')
        ->fetchAll();
    return $cs;
}

/**
 * @param $consumedID
 * @return mixed
 */
public function getConsumedByID($consumedID)
{
    return $this->dao->select('*')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch();
}

/**
 *
 * @param $data
 * @param $date
 * @param $work
 */
public function createEstimate($taskid,$date,$work,$account,$consumed,$buildID,$consumedID){
    $this->loadModel('effort');
    $this->loadModel('task');
    $this->loadModel('action');
    $task = $this->task->getByID($taskid);

    $left = $task->left;
    unset($_POST);
    $_POST['dates'] = $date;
    $_POST['consumed'] = $consumed;
    $_POST['works'] = $work;
    $_POST['accounts'] = $account;
    if(!empty($_POST))
    {
        // 成方金科剩余工时=estimate-consumed
        $efforts = array();
        $totalConsumed = array();
        foreach($_POST['consumed'] as $key => $c)
        {
            //if(!$c) continue;
            if(empty($_POST['dates'])) continue;

            $left -= $c;

            $row = new stdclass();
            $row->date     = $_POST['dates'];
            $row->consumed = $c;
            $row->left     = $left;
            $row->work     = $_POST['works'];
            $row->account  = $_POST['accounts'][$key];
            $row->source   = 1;
            $row->buildID   = $buildID;
            $row->consumedID = $consumedID;
            //$row->progress = $_POST['progress'][$key];

            $efforts[] = $row;
            if(!isset($totalConsumed[$row->date])) $totalConsumed[$row->date] = 0;
            $totalConsumed[$row->date] += $c;
        }
        /*foreach($totalConsumed as $consumedDate => $consumed)
        {
            $consumedToday = $this->loadModel('effort')->getWorkloadToday($this->app->user->account, $consumed, 'insert', $consumedDate);
        }*/
        foreach (explode(',', $taskid) as $toTaskId)
        {
            $this->task->batchCreateEffort(intval($toTaskId), $efforts);
        }
        if(dao::isError()) die(js::error(dao::getError()));
        $ext = new stdClass();
        $ext->field = 'desc';
        $ext->old   = '';
        $consume = is_array($consumed) ? $consumed[0] : $consumed;
        $ext->new   =  '测试版本('.$buildID.') :'.$work.'('.$consume.')';
        $extra = array($ext);
        $this->action->create('task', $taskid, 'builddesc', '','','','',true,$extra);

    }
}

/**
 * 制版删除同步删除工时
 * @param $execution
 * @param $taskid
 */
public function deleteTaskEstimate($execution,$taskid,$buildID){
 $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('objectType')->eq('task')
     ->andWhere('objectID')->eq("$taskid")
     ->andWhere('execution')->eq("$execution")
     ->andWhere('buildID')->eq("$buildID")
     ->andWhere('source')->eq(1)
     ->exec();
 $this->loadModel('action')->create('task', $taskid, 'deleteestimate', '制版删除,同步删除工时');
 $this->loadModel('task')->computeConsumed($taskid);
}

/**
 * 待测试 待发布 信息回填
 * @param $execution
 * @param $taskid
 */
public function backFill($ID,$type,$path){
    $ids = explode(',',$ID);
    $table = $type == 'problem' ? TABLE_PROBLEM : TABLE_DEMAND;
    foreach ($ids as $item) {
        if(!$item) continue;
        $plateMakInfo = $this->dao->select('plateMakInfo,id')->from($table)->where('code')->eq($item)->fetch();
        $path = isset($plateMakInfo->plateMakInfo) ? $plateMakInfo->plateMakInfo.'<br>'. $path : $path;
        $this->dao->update($table)->set('plateMakInfo')->eq("$path")->where('id')->eq($plateMakInfo->id)->exec();
        $this->loadModel('action')->create($type, $plateMakInfo->id, 'dealbackfill', '处理回填');
    }
}

/**
 * 状态流转工作量删除同时删除任务对应的工时
 * @param $taskid
 * @param $buildID
 * @param $consumedID
 *
 */
public function deleteWorkLoadTaskEstimate($taskid,$buildID,$consumedID){
    $effort = $this->dao->select('id')->from(TABLE_EFFORT)->where('objectType')->eq('task')
        ->andWhere('objectID')->eq("$taskid")
        ->andWhere('buildID')->eq("$buildID")
        ->andWhere('consumedID')->eq("$consumedID")
        ->andWhere('source')->eq(1)
        ->fetchAll();
    if($effort){
        $effortid = array_column($effort,'id');
        $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('id')->in($effortid)->exec();
        /*$this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('objectType')->eq('task')
            ->andWhere('objectID')->eq("$taskid")
            ->andWhere('buildID')->eq("$buildID")
            ->andWhere('consumedID')->eq("$consumedID")
            ->andWhere('source')->eq(1)
            ->exec();*/
        $this->loadModel('action')->create('task', $taskid, 'deleteestimate', '状态流转工作量删除,同步删除工时');
        $this->loadModel('task')->computeTask($taskid);
        $this->loadModel('task')->computeConsumed($taskid);
    }
}

/**
 * 状态流转工作量编辑同时编辑任务对应的工时
 * @param $taskid
 * @param $buildID
 * @param $consumedID
 *
 */
public function editWorkLoadTaskEstimate($oldDetail,$taskid,$buildID,$consumedID,$consumedTime,$account){

     $effort = $this->dao->select('*')->from(TABLE_EFFORT)->where('objectType')->eq('task')
     ->andWhere('objectID')->in("$taskid")
     ->andWhere('buildID')->eq("$buildID")
     ->andWhere('consumedID')->eq("$consumedID")
     ->andWhere('source')->eq(1)->fetchAll();
      //相关人员工时
      $detailInfo = $this->dao->select('*')->from(TABLE_CONSUMED)->where('parentId')->eq($consumedID)->fetchAll();
      $arrayAdd = array(); // 新增
      $arrayUpdate = array(); //更新
      $arrayDelete = array();//删除的
    $effortaccount = array_column($effort,'account');
    $effortconsumed = array_column($effort,'consumed');
    $effortobject   =  array_combine($effortaccount,$effortconsumed);//旧工作量

    $detailaccount = array_column($detailInfo,'account');
    $detailconsumed = array_column($detailInfo,'consumed');
    $detailobject = array_combine($detailaccount,$detailconsumed);
    $detailobject = array_merge(array($account=>$consumedTime),$detailobject);//新工作量

    $oldArray = array_keys($effortobject);//旧工作量 account
    $newArray = array_keys($detailobject);//新工作量 account
    //判断差异
    $diffdelete = array_diff($oldArray,$newArray); //删除的

    //删除的存删除数组
    foreach ($diffdelete as $delete) {
        $arrayDelete[$delete] = $effortobject[$delete];
    }
    $diffAdd    = array_diff($newArray,$oldArray); //新增的
    //新增的存新增数组
    foreach ($diffAdd as $add) {
        $arrayAdd[$add] = $detailobject[$add];
    }
    $intersect  = array_intersect($oldArray,$newArray); //交集
    //交集查看值是否修改，存入更新数组
    foreach ($intersect as $inter) {
        //比较工时值
        if($effortobject[$inter] != $detailobject[$inter]){
            $arrayUpdate[$inter] = $detailobject[$inter];
        }
    }
    //删除
    if($arrayDelete){
        foreach ($arrayDelete as $k=>$item) {
            $diffeffort = $this->dao->select('*')->from(TABLE_EFFORT)->where('objectType')->eq('task')
                ->andWhere('objectID')->in("$taskid")
                ->andWhere('buildID')->eq("$buildID")
                ->andWhere('consumedID')->eq("$consumedID")
                ->andWhere('source')->eq(1)
                ->andWhere('account')->eq($k)
                ->fetchAll();
            $diffid = array_column($diffeffort,'id');
            $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('id')->in($diffid)->exec();
        }
    }
    //新增
    if($arrayAdd){
        $efforts = array();
        $totalConsumed = array();
        foreach ($arrayAdd as $key=>$add) {
            $row = new stdclass();
            $row->date     = isset($_POST['dates']) ? $_POST['dates'] : helper::now();
            $row->consumed = $add;
            $row->left     = 0;
            $row->work     = $effort[$key]->work;
            $row->account  = $key;
            $row->source   = 1;
            $row->buildID   = $buildID;
            $row->consumedID = $consumedID;

            $efforts[] = $row;
            if(!isset($totalConsumed[$row->date])) $totalConsumed[$row->date] = 0;
            $totalConsumed[$row->date] += 0;
        }
        $this->loadModel('task')->batchCreateEffort(intval($taskid), $efforts);
    }
    //更新
    if($arrayUpdate){
        foreach ($arrayUpdate as $name=>$item) {

                $infoeffort = $this->dao->select('*')->from(TABLE_EFFORT)->where('objectType')->eq('task')
                    ->andWhere('objectID')->in("$taskid")
                    ->andWhere('buildID')->eq("$buildID")
                    ->andWhere('consumedID')->eq("$consumedID")
                    ->andWhere('source')->eq(1)
                    ->andWhere('account')->eq($name)
                    ->fetchAll();
                $infoid = array_column($infoeffort,'id');
                $this->dao->update(TABLE_EFFORT)
                    ->set('consumed')->eq($item)->where('id')->in($infoid)->exec();

        }
    }
    if($arrayUpdate || $arrayAdd || $arrayDelete){
        $this->loadModel('action')->create('task', $taskid, 'editestimate', '状态流转工作量更新,同步更新工时');
        $this->loadModel('task')->computeTask($taskid);
        $this->loadModel('task')->computeConsumed($taskid);
    }
}

// 喧喧消息
public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
    $build = $this->loadModel('build')->getByID($objectID);

    //状态除待制版外发喧喧
    if($build->status == 'waittest'){
        $toList = '';
    }else{
        /* 处理收件人。*/
        $toList = $build->dealuser;
    }

    $server   = $this->loadModel('im')->getServer('zentao');
    $url = $server.helper::createLink($objectType, 'view', "id=$objectID", 'html#app=project');
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

public function fill($demandId,$problemId,$path){

    if($demandId) {
        $demandIds = explode(',',$demandId);
        foreach ($demandIds as $item) {
            $this->backFill($item, 'demand', $path);
        }
    }
    if($problemId){
        $problemIds = explode(',',$problemId);
        foreach ($problemIds as $item) {
            $this->backFill($item,'problem',$path);
        }
    }
}

/**
 * 编辑附件
 * @param $buildID
 * @return array|bool
 */
public function editFilesByID($buildID)
{
    $oldBuild = $this->getByID($buildID);
    if(is_array($this->post->verifyFiles) && count($this->post->verifyFiles)){
        return dao::$errors = array('verifyFiles' => $this->lang->build->verifyFilesEmpty);
    }
    $build = fixer::input('post')
        ->add('updateFileDate', helper::now())
        ->remove('verifyFiles')
        ->get();
    $this->dao->update(TABLE_BUILD)->data($build)->autoCheck()
        ->where('id')->eq($buildID)
        ->exec();
    $this->loadModel('file')->updateObjectID($this->post->uid, $buildID, 'build');
    $this->loadModel('file')->saveUpload('build', $buildID,'verifyFiles', 'verifyFiles');

    if(!dao::isError()) return common::createChanges($oldBuild, $build);

    return false;
}

/**
 * Project: chengfangjinke
 * Method: dealDescByTimeDeOrder
 * @param $build
 * @return array|mixed|string[]
 */
public function reverseDesc($build) {
    $descArray = preg_split('/(?:<br>)+/', $build->desc);
//    $pattern = '/\d{4}-\d{1,2}-\d{1,2}\s+\d{1,2}:\d{1,2}:\d{1,2}/';
//    $descList = array(''=>'');
//    foreach ($descArray as $desc) {
//        preg_match($pattern, $desc, $key);
//        $descList = $descList + array($key[0] => $desc);
//    }
//    krsort($descList, SORT_STRING);
    $res = implode("<br>", array_reverse($descArray));
    return $res;
}


