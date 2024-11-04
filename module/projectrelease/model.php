<?php
/**
 * The model file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: model.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php
class projectreleaseModel extends model
{
    /**
     * Get release by id.
     *
     * @param  int    $releaseID
     * @param  bool   $setImgSize
     * @access public
     * @return object
     */
    public function getByID($releaseID, $setImgSize = false)
    {
        $release = $this->dao->select('t1.*, t2.id as buildID, t2.filePath, t2.scmPath, t2.name as buildName, t2.execution, t3.name as productName, t3.type as productType')
            ->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')->on('t1.build = t2.id')
            ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
            ->where('t1.id')->eq((int)$releaseID)
            ->orderBy('t1.id DESC')
            ->fetch();
        if(!$release) return false;

        $this->loadModel('file');
        $release = $this->file->replaceImgURL($release, 'desc');
        $release->files = $this->file->getByObject('release', $releaseID);
        if(empty($release->files))$release->files = $this->file->getByObject('build', $release->buildID);
        if($setImgSize) $release->desc = $this->file->setImgSize($release->desc);
        return $release;
    }

    /**
     * Get list of releases.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $type
     * @access public
     * @return array
     */
    public function getList($projectID, $productID, $branch = 0, $type = 'all')
    {
        return $this->dao->select('t1.*, t2.name as productName, t3.id as buildID, t3.name as buildName, t3.execution, t4.name as executionName')
            ->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_BUILD)->alias('t3')->on('t1.build = t3.id')
            ->leftJoin(TABLE_EXECUTION)->alias('t4')->on('t3.execution = t4.id')
            ->where('t1.project')->eq((int)$projectID)
            ->beginIF($type != 'all')->andWhere('t1.status')->eq($type)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.date DESC')
            ->fetchAll();
    }

    /**
     * Get last release.
     *
     * @param  int    $projectID
     * @access public
     * @return bool | object
     */
    public function getLast($projectID)
    {
        return $this->dao->select('id, name')->from(TABLE_RELEASE)
            ->where('project')->eq((int)$projectID)
            ->orderBy('date DESC')
            ->limit(1)
            ->fetch();
    }

    /**
     * Get release builds from project.
     *
     * @param  int    $projectID
     * @access public
     * @return array
     */
    public function getReleaseBuilds($projectID)
    {
        $releases = $this->dao->select('build')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('project')->eq($projectID)
            ->fetchAll('build');
        return array_keys($releases);
    }

    /**
     * Create a release.
     *
     * @access public
     * @return int
     */
    public function create($projectID)
    {
        // 截掉 /ftpdatas 前缀
        if(strpos($this->post->path,'/ftpdatas') === 0) {
            $this->post->path = substr($this->post->path,9);
        }

        $config         = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('mediaCheckList')->fetchPairs('key');
        if($config['release'] == 1) { //校验开关
            if($this->checkPath($this->post->path) == false) return false;
        }

        $productID = $this->post->product;
        $branch    = $this->post->branch;
        $productVersion = $this->post->productVersion;
        $buildID   = 0;
        $app = null;
        /* Check build if build is required. */
        if(strpos($this->config->release->create->requiredFields, 'build') !== false and $this->post->build == false) return dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->release->build);

        /* Check date must be not more than today. */
        if($this->post->date > date('Y-m-d')) return dao::$errors[] = $this->lang->release->errorDate;

        if($this->post->build)
        {
            $build     = $this->loadModel('build')->getByID($this->post->build);
            $productID = $build->product;
            $productVersion = $build->version;
            $branch    = $build->branch;
            $app = $build->app;
        }

        $release = fixer::input('post')
            ->add('project', $projectID)
            ->add('product', (int)$productID)
            ->add('productVersion', (int)$productVersion)
            ->add('branch',  (int)$branch)
            ->setIF(isset($app), 'app', (int)$app)
            ->setDefault('stories', '')
            ->join('stories', ',')
            ->join('bugs', ',')
            ->setIF($this->post->build == false, 'build', $buildID)
            ->setIF($productID, 'product', $productID)
            ->setIF($branch, 'branch', $branch)
            ->stripTags($this->config->release->editor->create['id'], $this->config->allowedTags)
            ->remove('allchecker,files,labels,uid')
            ->get();
        $release->path = $this->post->path;

        /* Auto create build when release is not link build. */
        if(empty($release->build) and $release->name)
        {
            $build = $this->dao->select('*')->from(TABLE_BUILD)
                ->where('deleted')->eq('0')
                ->andWhere('name')->eq($release->name)
                ->andWhere('product')->eq($productID)
                ->andWhere('branch')->eq($branch)
                ->fetch();
            if($build)
            {
                return dao::$errors['build'] = sprintf($this->lang->release->existBuild, $release->name);
            }
            else
            {
                $build = new stdclass();
                $build->project   = $projectID;
                $build->product   = (int)$productID;
                $build->branch    = (int)$branch;
                $build->name      = $release->name;
                $build->date      = $release->date;
                $build->builder   = $this->app->user->account;
                $build->desc      = $release->desc;
                $build->execution = 0;
                $build->app       =  $release->app;
                $build->product   = $release->product;
                $build->version   = $release->productVersion;
                $build->status    = 'released';
                $build->createdBy = $this->app->user->account;
                $build->createdDate = helper::now();
                $build->releaseName = $release->name;
                $pinfo = $this->dao->select('id,code')->from(TABLE_PRODUCT)->where('id')->eq($productID)->fetch();
                $build->code = isset($pinfo->code) ? $pinfo->code : '';

                $build = $this->loadModel('file')->processImgURL($build, $this->config->release->editor->create['id']);
               //20220130 修改，新增判空字段desc
                $this->dao->insert(TABLE_BUILD)->data($build)
                    ->autoCheck()
                    ->check('name', 'unique', "product = '{$productID}' AND branch = '{$branch}' AND deleted = '0'")
                    ->batchCheck('name,desc', 'notempty')
                    ->exec();
                if(dao::isError()) return false;

                $buildID = $this->dao->lastInsertID();
                $release->build = $buildID;
            }
        }

        if($release->build) $release->branch = $this->dao->select('branch')->from(TABLE_BUILD)->where('id')->eq($release->build)->fetch('branch');

        $release = $this->loadModel('file')->processImgURL($release, $this->config->release->editor->create['id'], $this->post->uid);

        $pinfo = $this->dao->select('id,code,line')->from(TABLE_PRODUCT)->where('id')->eq($productID)->fetch();
        $release->productCodeInfo = $pinfo->code;
        $release->uuid = time().'-'.rand(1000, 9999); //uuid现在没用到
        $this->dao->insert(TABLE_RELEASE)->data($release)
            ->autoCheck()
            ->batchCheck($this->config->release->create->requiredFields, 'notempty')
            ->check('name', 'unique', "product = {$release->product} AND branch = {$release->branch} AND deleted = '0'");

        if(dao::isError())
        {
            if(!empty($buildID)) $this->dao->delete()->from(TABLE_BUILD)->where('id')->eq($buildID)->exec();
            return false;
        }

        $this->dao->exec();

        if(dao::isError())
        {
            if(!empty($buildID)) $this->dao->delete()->from(TABLE_BUILD)->where('id')->eq($buildID)->exec();
        }
        else
        {
            $releaseID = $this->dao->lastInsertID();
            $this->file->updateObjectID($this->post->uid, $releaseID, 'release');
            $this->file->saveUpload('release', $releaseID);
            $this->loadModel('score')->create('release', 'create', $releaseID);

            // 正常 状态 同步安全资产平台发布数据
            $releaseNow = $this->getByID($releaseID);
            if(!empty($releaseNow->app)){
                $this->pushSafeAsset($releaseNow);
            }

            
            return $releaseID;
        }

        return false;
    }

    public function checkPath($path, $name = '')
    {
        if(empty(trim($path))){
            dao::$errors['path'] = $name. '发布地址是必填的';
            return false;
        }
        //return true; //其他不校验
        if(substr($path, 0, 7) !=='/files/' || substr($path, -4) !=='.zip'){
            dao::$errors['path'] =  $name. '发布地址应以“/files/”或“/ftpdatas/”  开头和“.zip”结尾';
            return false;
        }
        $filters = ' \“:：,，。…、~`＠＃￥％＆×＋｜＝－＊＾＄～｀!@#$%^&*\+—=！￥【】\|\"\'’‘“”；;\?\？\·';
        $filterPath = preg_replace('/([\x80-\xff]*)/i','', $path);
        for ($i = 0; $i <strlen($filterPath); $i++){
            if(strstr($filters, $filterPath[$i])) {
                dao::$errors['path'] =  $name. '发布地址符号只能含有/-_(){}[]<>《》';
                return false;
            }
        }
        if($this->checkRemoteFile($path, $name) == false){
            return false;
        }
        return  true;
    }

    function checkRemoteFile($remoteFile,  $name = '' ){
        $config         = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('sftpList')->fetchPairs('key');
        $conn           = ssh2_connect($config['host'], $config['port']);   //登陆远程服务器
        if(!ssh2_auth_password($conn, $config['username'], $config['password'])) {
            dao::$errors['path'] = 'sfpt用户名密码配置错误';
            return false;
        }                                                                   //用户名密码验证
        $sftp           = ssh2_sftp($conn);                     //打开sftp
        $remotFileMd5   = $this->getMd5FileName($remoteFile);   ///aaa.zip 转成 aaa.md5

            $resource = "ssh2.sftp://{$sftp}" . $remoteFile;    //远程文件地址md5
            if (!file_exists($resource)) {
                dao::$errors['path'] =  $name. '在sftp上压缩包不存在';
                return false;
            } //检查下载文件是否存在

            $resource = "ssh2.sftp://{$sftp}" . $remotFileMd5;          //远程文件地址md5
            if (!file_exists($resource)) {                              //如果找不到.MD5 找.org
                $remotFileMd5 =  $this->getMd5OrgName($remoteFile);     ///aaa.zip 转成 aaa.org
                $resource = "ssh2.sftp://{$sftp}" . $remotFileMd5;      //远程文件地址md5
                if (!file_exists($resource)) {
                    $remotFileMd5 = $this->getMd5OrgFile($remoteFile);  //兼容md5.org
                    $resource = "ssh2.sftp://{$sftp}" . $remotFileMd5;      //远程文件地址md5
                    if(!file_exists($resource)){
                        dao::$errors['path'] =  $name. '在sftp上md5文件不存在';
                        return false;
                    }
                } //检查下载md5是否存在
            }
        return  true;
    }

    //把后缀变成MD5
    public function getMd5FileName($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'md5';
    }
    public function getMd5OrgName($filename)
    {
        $arr = explode('.', $filename);
        $ext = end($arr);
        $extLen = strlen($ext);
        return substr($filename, 0, -$extLen) . 'org';
    }
    //把文件名变成md5.org
    public function getMd5OrgFile($filename)
    {
        $arr = explode('/', $filename);
        $arr[sizeof($arr)-1]='md5.org';
        return rtrim(implode('/',$arr),'/');
    }
    /**
     * Update a release.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function update($releaseID)
    {
        //if($this->checkPath($this->post->path) == false) return false; //2022-7-26 取消验证地址
        $releaseID  = (int)$releaseID;
        $oldRelease = $this->dao->select('*')->from(TABLE_RELEASE)->where('id')->eq($releaseID)->fetch();
        $branch     = $this->dao->select('branch')->from(TABLE_BUILD)->where('id')->eq((int)$this->post->build)->fetch('branch');

        $release = fixer::input('post')->stripTags($this->config->release->editor->edit['id'], $this->config->allowedTags)
            ->add('branch',  (int)$branch)
            ->setIF(!$this->post->marker, 'marker', 0)
            ->cleanInt('product')
            ->remove('files,labels,allchecker,uid')
            ->get();

        if($this->post->build)
        {
            $build     = $this->loadModel('build')->getByID($this->post->build);
            $release->product = $build->product;
            $release->productVersion = $build->version;
            $release->branch    = $build->branch;
            $release->app       = $build->app;
        }
        // 截掉 /ftpdatas 前缀
        if(strpos($release->path,'/ftpdatas') === 0) {
            $release->path = substr($release->path,9);
        }

        $config         = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('mediaCheckList')->fetchPairs('key');
        if($config['release'] == 1) { //校验开关
            if($this->checkPath($release->path) == false) return false;
        }
        //如果介质变化 重置 推送条件
        if($release->path != $oldRelease->path){
            $release->remotePathQz = '';
            $release->remotePathJx = '';
            $release->pushStatusQz = 0;
            $release->pushStatusJx = 0;
            $release->pushFailsQz = 0;
            $release->md5 = '';
        }
        $release = $this->loadModel('file')->processImgURL($release, $this->config->release->editor->edit['id'], $this->post->uid);
        $this->dao->update(TABLE_RELEASE)->data($release)
            ->autoCheck()
            ->batchCheck($this->config->release->edit->requiredFields, 'notempty')
            ->check('name', 'unique', "id != '$releaseID' AND product = '{$release->product}' AND branch = '$branch' AND deleted = '0'")
            ->where('id')->eq((int)$releaseID)
            ->exec();
        if(!dao::isError())
        {
            $this->file->updateObjectID($this->post->uid, $releaseID, 'release');
            return common::createChanges($oldRelease, $release);
        }
    }

    /**
     * Link stories
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function linkStory($releaseID)
    {
        $release = $this->getByID($releaseID);
        $product = $this->loadModel('product')->getByID($release->product);

        foreach($this->post->stories as $i => $storyID)
        {
            if(strpos(",{$release->stories},", ",{$storyID},") !== false) unset($_POST['stories'][$i]);
        }

        $release->stories .= ',' . join(',', $this->post->stories);
        $this->dao->update(TABLE_RELEASE)->set('stories')->eq($release->stories)->where('id')->eq((int)$releaseID)->exec();

        if($release->stories)
        {
            $this->loadModel('story');
            $this->loadModel('action');
            foreach($this->post->stories as $storyID)
            {
                /* Reset story stagedBy field for auto compute stage. */
                $this->dao->update(TABLE_STORY)->set('stagedBy')->eq('')->where('id')->eq($storyID)->exec();
                if($product->type != 'normal') $this->dao->update(TABLE_STORYSTAGE)->set('stagedBy')->eq('')->where('story')->eq($storyID)->andWhere('branch')->eq($release->branch)->exec();

                $this->story->setStage($storyID);

                $this->action->create('story', $storyID, 'linked2release', '', $releaseID);
            }
        }
    }

    /**
     * Unlink story
     *
     * @param  int    $releaseID
     * @param  int    $storyID
     * @access public
     * @return void
     */
    public function unlinkStory($releaseID, $storyID)
    {
        $release = $this->getByID($releaseID);
        $release->stories = trim(str_replace(",$storyID,", ',', ",$release->stories,"), ',');
        $this->dao->update(TABLE_RELEASE)->set('stories')->eq($release->stories)->where('id')->eq((int)$releaseID)->exec();
        $this->loadModel('action')->create('story', $storyID, 'unlinkedfromrelease', '', $releaseID);
    }

    /**
     * Link bugs.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @access public
     * @return void
     */
    public function linkBug($releaseID, $type = 'bug')
    {
        $release = $this->getByID($releaseID);

        $field = $type == 'bug' ? 'bugs' : 'leftBugs';

        foreach($this->post->bugs as $i => $bugID)
        {
            if(strpos(",{$release->$field},", ",{$bugID},") !== false) unset($_POST['bugs'][$i]);
        }

        $release->$field .= ',' . join(',', $this->post->bugs);
        $this->dao->update(TABLE_RELEASE)->set($field)->eq($release->$field)->where('id')->eq((int)$releaseID)->exec();

        $this->loadModel('action');
        foreach($this->post->bugs as $bugID) $this->action->create('bug', $bugID, 'linked2release', '', $releaseID);
    }

    //获取发布路径
    public function getPath($releaseID)
    {
        return $this->dao->select('id, path, name')->from(TABLE_RELEASE)
            ->where('id')->eq((int)$releaseID)
            ->limit(1)
            ->fetch();
    }
    public function getPaths($releaseIDs)
    {
        return $this->dao->select('id, path, name')->from(TABLE_RELEASE)
            ->where('id')->in($releaseIDs)
            ->fetchall('id');
    }
    /**
     * Judge button if can clickable.
     *
     * @param  object $release
     * @param  string $action
     * @access public
     * @return void
     */
    public static function isClickable($release, $action)
    {
        global $app;

        $action = strtolower($action);

        $dealUsers  = [];
        if($release->dealUser){
            $dealUsers = explode(',', $release->dealUser);
        }

        //审核操作
        if($action == 'deal'){
            return in_array($app->user->account, $dealUsers);
        }
        //没有联动过的可以编辑
        if ($action == 'edit'){
            return $release->syncStateTimes <= 0;
        }

        return true;
    }

    /**
     *获取列表
     *
     * @param $ids
     * @param string $select
     * @return array
     */
    public function getValidListByIds($ids, $select = '*',$syncStateTimes=false){
        $data = [];
        if(!$ids){
            return $data;
        }
        //生产变更同步状态，同步次数为0、产品版本不为空 “无”
        $ret = $this->dao->select($select)->from(TABLE_RELEASE)
            ->where('id')->in($ids)
            ->andWhere('deleted')->eq('0')
            ->andWhere('status')->ne($this->lang->projectrelease->statusList['terminate'])
            ->beginif($syncStateTimes)->andWhere('syncStateTimes')->eq(0)->fi()
            //1：无，0：没选
            ->beginif($syncStateTimes)->andWhere('productVersion')->notIN([0,1])->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *同步项目发布的变更信息
     *
     * @param $releaseInfo
     * @param $syncObjectType
     * @param $syncObjectId
     * @param $dealUser
     * @param $syncObjectCreateTime
     * @param $object
     * @return bool
     */
    public function syncObjectInfo($releaseInfo, $syncObjectType, $syncObjectId, $dealUser, $syncObjectCreateTime, $object = ''){
        $res = true;
        if(!($releaseInfo && $syncObjectType && $syncObjectId && $syncObjectCreateTime && $dealUser)){
            $res = false;
            return $res;
        }
        $releaseId = $releaseInfo->id;
        $oldStatus = $releaseInfo->status;
        $oldVersion = $releaseInfo->version;
        $syncStateTimes = $releaseInfo->syncStateTimes + 1;

        $objectType = $this->lang->projectrelease->objectType;
        $reviewers  = explode(',', $dealUser);
       /* $createUserDepts = $this->loadModel('user')->getUserInfoListByAccounts($releaseInfo->createdBy);
        $depts = $this->loadModel('dept')->getByID($createUserDepts[$releaseInfo->createdBy]->dept);
        $reviewers  = array_unique(array_filter(array_merge(explode(',', $dealUser),explode(',',$depts->cm))));
        $dealUser = $reviewers ? implode(',',$reviewers) : $dealUser;*/
        $stage = 1;
        if(in_array($oldStatus, $this->lang->projectrelease->needChangeVersionStatusList)){
            $version = $oldVersion + 1;
        }else{
            $version    = $oldVersion;
        }


        if($oldStatus != $this->lang->projectrelease->statusList['waitBaseline']){ //待打基线
            $nextStatus = $this->lang->projectrelease->statusList['waitBaseline'];
        }else{
            $nextStatus = $oldStatus;
        }
        $nodeCode = $this->getNodeCodeByStatus($nextStatus);
        $extParams = [
            'nodeCode' => $nodeCode,
        ];

        $nodeId = 0;
        if($oldStatus == $this->lang->projectrelease->statusList['waitBaseline']){
            $nodeId = $this->loadModel('review')->getReviewNodeId($objectType, $releaseId, $version, $nodeCode);
        }

        if($nodeId){ //修改节点审核人
            $ret = $this->loadModel('review')->updateReviewersByNodeId($nodeId, $reviewers);
        }else{ //新增审核节点
            $ret = $this->loadModel('review')->addNode($objectType, $releaseId, $version, $reviewers, true, 'pending', $stage, $extParams);
        }
        //忽略节点
        if($version > $oldVersion){
            $needDealIgnore = $this->loadModel('review')->getUnDealReviewNodes($objectType, $releaseId, $oldVersion);
            if(!empty($needDealIgnore)){
                $ret = $this->loadModel('review')->ignoreReviewNodeAndReviewers($needDealIgnore);
            }
        }
        //变更发布表主表
        $updateParams = new stdClass();
        $updateParams->status               = $nextStatus;
        $updateParams->version              = $version;
        $updateParams->dealUser             = $dealUser;
        $updateParams->syncObjectType       = $syncObjectType;
        $updateParams->syncObjectId         = $syncObjectId;
        $updateParams->syncObjectCreateTime = $syncObjectCreateTime;
        $updateParams->syncStateTimes       = $syncStateTimes;
        $this->dao->update(TABLE_RELEASE)->data($updateParams)->where('id')->eq($releaseId)->exec();

        //添加日志
        $logChange = common::createChanges($releaseInfo, $updateParams);
        $actionType = 'modifysyncreleasestatus';
        $commentType = 'sync'.$syncObjectType.'comment';
        $comment =  sprintf($this->lang->projectrelease->$commentType, $object->code, $object->status);
        $actionID = $this->loadModel('action')->create('release', $releaseId, $actionType, $comment);
        if(!empty($logChange)) {
            $this->action->logHistory($actionID, $logChange);
        }

        // 待合并代码 状态 同步安全资产平台发布数据
        $releaseNow = $this->getByID($releaseId);
        if(!empty($releaseNow->app) && $releaseNow->status == 'waitBaseline'){
            $this->pushSafeAsset($releaseNow);
        }


        return $res;
    }

    /**
     * 根据评审状态获得审核节点标识
     *
     * @param $status
     * @return string
     */
    public function getNodeCodeByStatus($status){
        $nodeCode = '';
        switch ($status){
            case $this->lang->projectrelease->statusList['waitBaseline']:
                $nodeCode = $this->lang->projectrelease->nodeCodeList['baseline'];
                break;

            case $this->lang->projectrelease->statusList['waitCmConfirm']:
                $nodeCode = $this->lang->projectrelease->nodeCodeList['cmConfirm'];
                break;

            default:
                break;
        }
        return $nodeCode;
    }

    /**
     *检查是否允许处理
     *
     * @param $release
     * @param $userAccount
     * @return array
     */
    public function checkIsAllowDeal($release,  $version, $status, $userAccount = ''){
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$release){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }

        if(($version != $release->version) || ($status != $release->status)){
            $message = $this->lang->projectrelease->checkCommonResultList['versionOrStatusChange'];
            $res['message'] = $message;
            return $res;
        }

        //当前状态
        $allowReviewStatusList = $this->lang->projectrelease->allowDealStatusList;
        //是否在审核状态
        if(!in_array($release->status, $allowReviewStatusList)){
            $statusDesc = zget($this->lang->projectrelease->statusLabelList, $status);
            $res['message'] = sprintf($this->lang->projectrelease->checkDealOpResultList['statusError'], $statusDesc);
            return $res;
        }


        $dealUser  = [];
        if(isset($release->dealUser) &&  ($release->dealUser)){
            $dealUser = explode(',', $release->dealUser);
        }
        if(!in_array($userAccount, $dealUser)){
            $res['message'] = $this->lang->projectrelease->checkDealOpResultList['userError'];
            return $res;
        }
        $res['result'] = true;
        return $res;
    }


    /**
     *处理操作
     * @param $releaseID
     * @return array
     */
    public function deal($releaseID){
        $data = fixer::input('post')->get();
        $formResult = '';
        if (isset($data->result)){
            $formResult = $data->result;//接收表单cm确认结果
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->projectrelease->editor->deal['id'], $this->post->uid);
        //项目发布信息
        $releaseInfo = $this->getByID($releaseID);
        $oldStatus = $releaseInfo->status;
        $userAccount = $this->app->user->account;
        //检查是否允许处理
        $res = $this->checkIsAllowDeal($releaseInfo, $data->version, $data->status, $userAccount);
        if(!$res['result']){
            dao::$errors[''] = $res['message'];
            return false;
        }
        if ($data->alreadyBaseLine == 2){
            $data->tagPath = [];
        }else{
            if (empty(array_filter($data->tagPath))){
                dao::$errors[''] = '请填写基线地址';
                return false;
            }
        }
        //检查处理信息
        $res = $this->checkDealParams($releaseInfo, $data);
        if(!$res['result']){
            return false;
        }
        $data = $res['data'];
        $reviewResult = $data->result; //评审结果
        if ($formResult != ''){
            $reviewResult = $formResult; //原来只要基线地址校验通过，cm就为通过，现在可以打回不通过
        }

        if (!$reviewResult) {
            dao::$errors['result'] = $this->lang->projectrelease->checkDealOpResultList['resultError'];
            return false;
        }
        //评审操作
        $objectType = $this->lang->projectrelease->objectType;
        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check($objectType, $releaseID, $releaseInfo->version, $reviewResult, $this->post->comment, '', null, $is_all_check_pass);

        //应该以新提交的是否包含基线地址为准
        $releaseInfo->baseLineCondition = $data->baseLineCondition;
        //获得审核后的状态
        $reviewNextInfo = $this->getReviewNextInfo($releaseInfo, $reviewResult);
        $nextStatus = $reviewNextInfo['nextStatus'];
        $version    = $reviewNextInfo['version'];
        if(!$nextStatus){
            dao::$errors[''] = $this->lang->projectrelease->checkDealOpResultList['nextStatusError'];
            return false;
        }
        $nextDealUser = $this->getNextDealUser($releaseInfo, $version, $nextStatus);

        //修改主表
        $updateParams = new stdClass();
        $updateParams->status      = $nextStatus;
        $updateParams->version     = $version;
        $updateParams->dealUser    = $nextDealUser;
        if ($oldStatus ==  $this->lang->projectrelease->statusList['waitBaseline']){ //待打基线
            $updateParams->baseLineCondition = $data->baseLineCondition;
            $updateParams->baseLinePath =  $data->baseLinePath;
            $updateParams->baseLineTime =  helper::now();
            $updateParams->baseLineUser =  $this->app->user->account;
            $updateParams->alreadyMergeCode =  $data->alreadyMergeCode;
            $updateParams->alreadyBaseLine =  $data->alreadyBaseLine;
        }elseif($oldStatus == $this->lang->projectrelease->statusList['waitCmConfirm']){ //待cm确认结果
            $updateParams->baseLineCondition = $data->baseLineCondition;
            $updateParams->baseLinePath =  $data->baseLinePath;
//            $updateParams->baseLineTime =  helper::now();
//            $updateParams->baseLineUser =  $this->app->user->account;
            $updateParams->cmConfirm = $reviewResult;
            $updateParams->cmConfirmTime = helper::now();
            $updateParams->cmConfirmUser = $this->app->user->account;
            $updateParams->alreadyMergeCode =  $data->alreadyMergeCode;
            $updateParams->alreadyBaseLine =  $data->alreadyBaseLine;
        }

        //更新项目发布表
        $this->dao->update(TABLE_RELEASE)->data($updateParams)->autoCheck()
            ->where('id')->eq($releaseID)
            ->exec();
        if(dao::isError()) {
            dao::$errors[''] = $this->lang->projectrelease->checkCommonResultList['opError'];
            return false;
        }

        //处理基线日志信息
        if ($oldStatus ==  $this->lang->projectrelease->statusList['waitBaseline']){ //待打基线
            $ret = $this->addReleaseBaseLineLog($releaseInfo, $updateParams);
        }elseif($oldStatus == $this->lang->projectrelease->statusList['waitCmConfirm']){ //待cm确认结果
            $ret = $this->setReleaseCmConfirmLog($releaseInfo, $updateParams);
        }

        if ($nextStatus ==  $this->lang->projectrelease->statusList['passBaseline']){ //已打基线
            $this->addBaseLine($releaseID);
        }
        //判断是否需要添加审核节点
        $isAddNode = $this->getIsAddReviewNode($nextStatus);
        if($isAddNode){
            $newReleaseInfo = $this->getByID($releaseID);
            $res = $this->addReviewNode($newReleaseInfo);
        }
        //返回修改的信息
        $logChange = common::createChanges($releaseInfo, $updateParams);
        return $logChange;
    }

    /**
     *获得是否需要新增审核节点
     *
     * @param $status
     * @return bool
     */
    public function getIsAddReviewNode($status){
        $isAddReviewNode = false;
        if(!$status){
            return $isAddReviewNode;
        }
        if(in_array($status, $this->lang->projectrelease->needAddReviewNodeStatusList)){
            $isAddReviewNode = true;
        }
        return $isAddReviewNode;
    }

    /**
     * 检查处理操作的参数
     *
     * @param $releaseInfo
     * @param $data
     * @return array
     */
    public function checkDealParams($releaseInfo, $data){
        $res = array(
            'result'  => false,
            'message' => '',
            'data'    => $data,
        );
        if(!($releaseInfo && $data)){
            $message = $this->lang->common->errorParamId;
            dao::$errors[''] = $message;
            $res['message'] = $message;
            return $res;
        }
        $oldStatus = $releaseInfo->status;
        if($oldStatus == $this->lang->projectrelease->statusList['waitBaseline'] || $oldStatus == $this->lang->projectrelease->statusList['waitCmConfirm']){ //待打基线
            $checkRes = $this->checkBaseLineInfo($data);
            if(!$checkRes['result']){
                return $res;
            }
            $data = $checkRes['data'];
        }
        $res['result'] = true;
        $res['data']   = $data;
        return $res;
    }

    /**
     *检查打基线信息
     *
     * @param $data
     * @return array
     */
    public function checkBaseLineInfo($data){
        $res = array(
            'result'  => false,
            'message' => '',
            'data'    => $data,
        );
        if(!$data){
            return $res;
        }
        $tagPath = $data->tagPath;  //基线路径
        $pregTime = "/^((\d{4})(0?[1-9]|1[0-2])(0?[1-9]|[12]\d|30|31))$/";
        $existTagPath = [];
        foreach ($tagPath as $key => $item) {
            $sortKey = $key + 1;
            //验证路径规则
            if (!empty($item)) {
                $tagPathUrlStart = substr($item, 0, 7);
                $tagPathArray = explode('/', $item); //通过“/”分割
                $tagPathCount = count($tagPathArray);
                if(!(($tagPathUrlStart == 'http://') && ($tagPathCount >= 2))){
                    $message =  $this->lang->projectrelease->checkDealOpResultList['tagPathError'];
                    dao::$errors['tagPath' . $sortKey] = $message;
                    $res['message'] = $message;
                    return $res;
                }
                //实际地址
                $tagPathEnd = $tagPathArray[$tagPathCount - 1];
                $pathArray = explode('_', $tagPathEnd); //通过“_”分割
                $pathBegin = $pathArray[0]; //路径前缀
                $pathReverseArray = array_reverse($pathArray);
                $pathEndFirst  = isset($pathReverseArray[1]) ? $pathReverseArray[1]:''; //路径后缀第一部分
                $pathEndSecond = $pathReverseArray[0]; //路径后缀第二部分
                if(($pathBegin != 'TAG') || !(!empty($pathEndFirst) && substr($pathEndFirst, 0, 1) == 'V') || !(preg_match($pregTime, $pathEndSecond))){
                      $message =  $this->lang->projectrelease->checkDealOpResultList['tagPathError'];
                      dao::$errors['tagPath' . $sortKey] = $message;
                      $res['message'] = $message;
                      return $res;
                }
                //已经存在
                if(in_array($item, $existTagPath)){
                    $message =  $this->lang->projectrelease->checkDealOpResultList['tagPathExistError'];
                    dao::$errors['tagPath' . $sortKey] = $message;
                    $res['message'] = $message;
                    return $res;
                }
                $existTagPath[] = $item;
            }
        }
        //去掉空元素
        $tagPath = array_filter($tagPath);
        if ($tagPath) {
            $data->baseLineCondition = 'yes';//已打基线
            $data->baseLinePath = implode(',', $tagPath);
        }else{
            $data->baseLineCondition = 'no' ;//未打基线
            $data->baseLinePath = '';
        }
        $data->result = 'pass';
        //返回
        $res['result'] = true;
        $res['data']  = $data;
        return $res;
    }


    /**
     *获得审核后的下一节点信息
     *
     * @param $releaseInfo
     * @param $reviewResult
     * @return array
     */
    public function getReviewNextInfo($releaseInfo, $reviewResult){
        $data = [
            'nextStatus'  => '',
            'version'     => $releaseInfo->version,
        ];
        $oldStatus   = $releaseInfo->status;
        if($reviewResult == 'reject'){
            if($oldStatus == $this->lang->projectrelease->statusList['waitCmConfirm']){ //当前状态cm确认
                $data['nextStatus']  = $this->lang->projectrelease->statusList['waitBaseline']; //返回到归档信息
                $data['version']     = $releaseInfo->version + 1; //版本加1
            }else{
                $data['nextStatus'] = 'reject'; //返回到退回
            }
        }else{//审核通过
            switch ($oldStatus){
                case $this->lang->projectrelease->statusList['waitBaseline']:
                    $data['nextStatus']  = $this->lang->projectrelease->statusList['waitCmConfirm'];
                    break;

                case $this->lang->projectrelease->statusList['waitCmConfirm']:
                    if($releaseInfo->baseLineCondition == 'yes'){
                        $data['nextStatus']  = $this->lang->projectrelease->statusList['passBaseline'];
                    }else{
                        $data['nextStatus']  = $this->lang->projectrelease->statusList['passNoBaseline'];
                    }
                    break;

                default:
                    break;
            }
        }
        return $data;
    }

    /**
     *获得下一步处理人
     *
     * @param $releaseInfo
     * @param $nextVersion
     * @param $nextStatus
     * @param string $postUser
     * @return string
     */
    public function getNextDealUser($releaseInfo, $nextVersion, $nextStatus, $postUser = ''){
        $nextDealUser = '';
        if($postUser){
            $nextDealUser = $postUser;
        }else{
            $objectType = $this->lang->projectrelease->objectType;
            switch ($nextStatus){
                case $this->lang->projectrelease->statusList['waitBaseline']:
                    $lastVersion = $nextVersion - 1;
                    $nodeCode  = $this->getNodeCodeByStatus($nextStatus);
                    $reviewers = $this->loadModel('review')->getReviewersByNodeCode($objectType, $releaseInfo->id, $lastVersion, $nodeCode);
                    $nextDealUser = $reviewers;
                    break;

                case $this->lang->projectrelease->statusList['waitCmConfirm']:
                    $nextDealUser = $releaseInfo->createdBy;
                    break;

                default:
                    break;
            }
        }

        return $nextDealUser;
    }

    /**
     * 新增审核节点
     *
     * @param $releaseInfo
     * @param array $reviewers
     * @return bool
     */
    public function addReviewNode($releaseInfo, $reviewers = []){
        $res = false;
        if(!$releaseInfo){
            return $res;
        }
        $releaseId = $releaseInfo->id;
        $status = $releaseInfo->status;
        $version = $releaseInfo->version;
        //审核或者指派节点
        $objectType = $this->lang->projectrelease->objectType;
        $maxStage = $this->loadModel('review')->getReviewMaxStage($releaseInfo->id, $objectType, $releaseInfo->version);
        $stage = $maxStage + 1;
        //审核人
        if(!$reviewers){
            $reviewers = explode(',', $releaseInfo->dealUser);
        }
        $nodeCode = $this->getNodeCodeByStatus($status);
        //扩展信息
        $extParams = [
            'nodeCode' => $nodeCode,
        ];
        $ret = $this->loadModel('review')->addNode($objectType, $releaseId, $version, $reviewers, true, 'pending', $stage, $extParams);

        return true;
    }

    /**
     *添加项目发布基线日志信息
     *
     * @param $releaseInfo
     * @param $baseLineParams
     * @return bool
     */
    public function addReleaseBaseLineLog($releaseInfo, $baseLineParams){
        if(!($releaseInfo && $baseLineParams)){
            return false;
        }
        $addParams = new stdClass();
        $addParams->releaseId = $releaseInfo->id;
        $addParams->version   = $releaseInfo->version;
        $addParams->baseLineCondition = $baseLineParams->baseLineCondition;
        $addParams->baseLinePath      = $baseLineParams->baseLinePath;
        $addParams->baseLineTime      = $baseLineParams->baseLineTime;
        $addParams->baseLineUser      = $baseLineParams->baseLineUser;
        $this->dao->insert(TABLE_RELEASE_BASELINE_LOG)->data($addParams)->autoCheck()->exec();
        if(dao::isError()){
            return false;
        }
        return true;
    }

    /**
     * 设置项目确认结果日志
     *
     * @param $releaseInfo
     * @param $cmConfirmParams
     * @return bool
     */
    public function setReleaseCmConfirmLog($releaseInfo, $cmConfirmParams){
        if(!($releaseInfo && $cmConfirmParams)){
            return false;
        }
        $releaseId = $releaseInfo->id;
        $version   = $releaseInfo->version;
        //查询对应的打基线log信息
        $logInfo = $this->getReleaseBaseLineLogInfo($releaseId, $version, 'id');
        if(!$logInfo){
            return false;
        }
        $logId = $logInfo->id;
        $updateParams = new stdClass();
        $updateParams->cmConfirm     = $cmConfirmParams->cmConfirm;
        $updateParams->cmConfirmTime = $cmConfirmParams->cmConfirmTime;
        $updateParams->cmConfirmUser = $cmConfirmParams->cmConfirmUser;
//        $updateParams->baseLineTime  = $cmConfirmParams->baseLineTime;
//        $updateParams->baseLineUser  = $cmConfirmParams->baseLineUser;
        $updateParams->baseLinePath  = $cmConfirmParams->baseLinePath;
        $updateParams->baseLineCondition = $cmConfirmParams->baseLineCondition;
        $this->dao->update(TABLE_RELEASE_BASELINE_LOG)->data($updateParams)->where('id')->eq($logId)->exec();
        return true;
    }

    /**
     * 获得项目发布打基线日志信息
     *
     * @param $releaseId
     * @param $version
     * @param string $select
     * @return bool
     */
    public function getReleaseBaseLineLogInfo($releaseId, $version, $select = '*'){
        if(!$releaseId){
            return false;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_RELEASE_BASELINE_LOG)
            ->where('releaseId')->eq($releaseId)
            ->andWhere('version')->eq($version)
            ->orderBy('id DESC')
            ->limit(1)
            ->fetch();
        if(!$ret){
            return false;
        }
        return $ret;
    }

    /**
     *获得基线信息列表
     *
     * @param $releaseId
     * @param string $select
     * @param $orderBy
     * @return array
     */
    public function getBaseLineLogList($releaseId, $select = '*', $orderBy = 'id DESC'){
        $data = [];
        if(!$releaseId){
            return $data;
        }
        $ret = $this->dao->select($select)
            ->from(TABLE_RELEASE_BASELINE_LOG)
            ->where('releaseId')->eq($releaseId)
            ->orderBy($orderBy)
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;

    }

    /**
     * 基线情况：打基线 相关数据入库baseline
     * @param $releaseId
     */
    public function addBaseLine($releaseId){
        $baselinetype = 'code';
        $objectType = $this->lang->projectrelease->objectType;
        $release = $this->getByID($releaseId);
        //基线路径
        $baselinePath =  explode(',',$release->baseLinePath);
        $member =  $this->loadModel('project')->getTeamMembers($release->project);//团队
        $member = array_column($member,'role','account');

        $proj = $this->loadModel('project')->getByID($release->project);
        $mark = $this->dao->select('t2.mark')->from(TABLE_RELEASE)->alias('t1')->leftJoin(TABLE_PROJECTPLAN)->alias('t2')->on('t2.project = t1.project')->where('t1.id')->eq($releaseId)->fetch();
        foreach ($baselinePath as $key => $currentPath) {
            $title = helper::cut_str($currentPath, "/", -1); //取最后

            $baseline = new stdClass();
            $baseline->title = $title;
            $baseline->type  = $baselinetype;
            $baseline->cm     = $release->cmConfirmUser;
            $baseline->cmDate = $release->cmConfirmTime;
            $baseline->reviewer = $this->lang->projectrelease->baseLineReviewerName;
            $baseline->reviewedDate = helper::today();
            $baseline->project = $release->project;
            $baseline->objectType = $objectType;
            $baseline->objectID   = $releaseId;
            $baseline->version    = $release->version;
            $baseline->createdDate = helper::today();
            $baseline->createdBy = $this->app->user->account;

            $project = new stdclass();
            foreach ($member as $k=>$value) {
                $val = explode(',',$value);
                if(in_array('2',$val)){
                    $PM = $k;//项目经理2
                }
                if (in_array('11',$val)){
                    $QA = $k;//质量保证工程师11
                }
                if (in_array('1',$val)){
                    $PO = $k;//项目主管1
                }
            }
            $project->PM =  !empty($PM) ? $PM : $proj->PM;//项目经理2
            $project->QA =  !empty($QA) ? $QA : $proj->QA;//质量保证工程师11
            $project->PO =  !empty($PO) ? $PO : $proj->PO;//项目主管1
            $project->code = isset($mark) ? $mark->mark : '';//空

            $pathFinal = explode('/', $currentPath);
            krsort($pathFinal); //对数组进行按照键值降序排序
            $checkPath = array_values($pathFinal)[0];

            $item = new stdClass();
            $item->title = $this->lang->projectrelease->baseLineCmItemTitle;
            $item->code  = '';//空
            $item->version = helper::cut_str($checkPath,'_',-2); //版本，取倒数第二位
            $item->changed = '0';
            $item->changedID = 0;
            //$item->changedDate = helper::today();
            $item->path    = $currentPath;
            $item->comment = $this->lang->projectrelease->baseLineCommentDesc;

            //存基线表
            $this->dao->insert(TABLE_BASELINE)->data($baseline)->autoCheck()->exec();
            $baselineID = $this->dao->lastInsertID();

            //存配置表
            $item->baseline = $baselineID;
            $this->dao->insert(TABLE_CMITEM)->data($item)->exec();

            //更新项目表
            $this->dao->update(TABLE_PROJECT)->data($project)->where('id')->eq($release->project)->exec();
        }
    }

    /**
     * 发布 正常、待合并代码 同步推送安全资产平台
     * @param $release
     */
    public function pushSafeAsset($release){
        $pushEnable = $this->config->global->pushSafeAssetEnable;
        //判断请求配置是否可用
        if ($pushEnable == 'enable') {
            $url = $this->config->global->pushSafeAssetUrl;
            $pushAppId = $this->config->global->pushSafeAssetAppId;
            $pushAppSecret = $this->config->global->pushSafeAssetAppSecret;
            $headers = array();
            $headers[] = 'X-AppId: ' . $pushAppId;
            $headers[] = 'X-AppSecret: ' . $pushAppSecret;

            $deptList = $this->loadModel('dept')->getOptionMenu();
            $users = $this->loadmodel('user')->getPairs('noletter');

            $application = $this->loadModel('application')->getByID($release->app);
            //$product = $this->loadModel('product')->getPairs();
            $productName = $this->loadModel('product')->getProductNamesByIds(explode(',',$release->product));
            $productVersion = $this->loadModel('productplan')->getPairs($release->product);
            $pushData = array();
            $pushData['purpose']                = $release->deleted == '0' ? ($release->status == 'normal' ? 1 : 2): '3'; //正常 1 ； 待合并状态 2；发布单已删除
            $pushData['dpmpSystemId']           = $release->app;
            $pushData['name']                   = $application->name;
            $pushData['abbr']                   = $application->code;
            $pushData['type']                   = $application->isPayment;
            $pushData['constractUnit']          = $application->team;
            $pushData['demandUnit']             = $application->fromUnit;
            $pushData['attribute']              = $application->attribute;
            $pushData['dpmpDeptId']             = $application->belongDeptIds;
            $pushData['manager']                =  $application->systemManager;
            $mobiles = $this->dao->select('mobile')->from(TABLE_USER)->where('account')->in($application->systemManager)->fetchAll();
            $pushData['managerTel']             = $mobiles ? implode(',',array_column($mobiles,'mobile')) : "";

            $pushData['describe']               = $application->desc;
            $pushData['dpmpProductId']          = $release->product != '99999'  ? $release->product : '';
            $pushData['dpmpVersionId']          = $release->productVersion != '1' && !empty($release->productVersion) ? $release->productVersion : '';
            $pushData['dpmpReleaseId']          = $release->id;

            $pushData['productName']            = $release->product != '99999' ? zget($productName,$release->product) : '无';//zget($product,$release->product,'') : '无';
            $pushData['productCode']            = $release->productCodeInfo;
            $pushData['productVersion']         = $productVersion  ? ($release->productVersion !='1' ? zget($productVersion, $release->productVersion,'') : '无') :'';
            $pushData['status']                 = $release->status == 'normal' ? 'waitOnline' : 'online';
            $pushData['gitPaths']               = $release->scmPath ? trim(str_replace(PHP_EOL,',',str_replace("\r\n",',',$release->scmPath)),',') : ''; //20240527 新增制版的git地址
            $pushData['isDeleted']              = $release->deleted;
            $pushData['releaseDate']            = $release->date;
            $pushData['releaseAddress']         = $release->path;

            $object = 'projectrelease';
            $objectType = 'pushSafeAsset';
            $status = 'fail';
            $extra = '';

            $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);

            if(!empty($result)) {
                $resultData = json_decode($result);
                if ($resultData->returnCode == '000000') {
                    $status = 'success';
                }
            }
            $response = $result;
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);

        }
    }
}
