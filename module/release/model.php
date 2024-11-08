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
class releaseModel extends model
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
        $release = $this->dao->select('t1.*, t2.id as buildID, t2.filePath, t2.scmPath, t2.name as buildName, t2.project, t3.name as productName, t3.type as productType')
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

    public function getFailsQz($releaseIds)
    {
        $releaseIdArray = explode(',', trim($releaseIds,','));
        $releases = $this->dao->select('id,pushStatusQz,pushStatusJx,pushFailsQz')
            ->from(TABLE_RELEASE)->where('id')->in($releaseIdArray)
            ->fetchall('id');
        $res = false;
        foreach ($releases as $release){
            if ($release->pushFailsQz >= 3){
                $res = true;
            }
        }
        return $res;
    }
    /**
     * 介质推送日志
     * @param $releaseIDs
     * @param int $type
     * @return array
     */
    public function getPushLog($releaseIDs, $type = 1)
    {
        $logs = [];
        $releases = $this->dao->select('*')->from(TABLE_PUSHLOG)->where('releaseId')->in( explode(',',trim($releaseIDs,',')))->andwhere('type')->eq($type)->fetchall();
         foreach ($releases as $release)
         {
             $logs[$release->releaseId][]= $release;
         }
        return $logs;
    }
    /**
     * Get list of releases.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $type
     * @access public
     * @return array
     */
    public function getList($productID, $branch = 0, $type = 'all')
    {
        return $this->dao->select('t1.*, t2.name as productName, t3.id as buildID, t3.name as buildName, t3.project, t4.name as projectName')
            ->from(TABLE_RELEASE)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->leftJoin(TABLE_BUILD)->alias('t3')->on('t1.build = t3.id')
            ->leftJoin(TABLE_PROJECT)->alias('t4')->on('t1.project = t4.id')
            ->where('t1.product')->eq((int)$productID)
            ->beginIF($branch)->andWhere('t1.branch')->eq($branch)->fi()
            ->beginIF($type != 'all')->andWhere('t1.status')->eq($type)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('t1.date DESC')
            ->fetchAll();
    }

    /**
     * Get last release.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return bool | object
     */
    public function getLast($productID, $branch = 0)
    {
        return $this->dao->select('id, name')->from(TABLE_RELEASE)
            ->where('product')->eq((int)$productID)
            ->beginIF($branch)->andWhere('branch')->eq($branch)->fi()
            ->orderBy('date DESC')
            ->limit(1)
            ->fetch();
    }

    /**
     * Get release builds from product.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function getReleaseBuilds($productID, $branch = 0)
    {
        $releases = $this->dao->select('build')->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('product')->eq($productID)
            ->beginIF($branch)->andWhere('branch')->eq($branch)->fi()
            ->fetchAll('build');
        return array_keys($releases);
    }

    /**
     * Create a release.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @access public
     * @return int
     */
    public function create($productID, $branch = 0)
    {
        $productID = (int)$productID;
        $branch    = (int)$branch;
        $buildID   = 0;

        /* Check build if build is required. */
        if(strpos($this->config->release->create->requiredFields, 'build') !== false and $this->post->build == false) return dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->release->build);

        /* Check date must be not more than today. */
        if($this->post->date > date('Y-m-d')) return dao::$errors[] = $this->lang->release->errorDate;

        $release = fixer::input('post')
            ->add('product', (int)$productID)
            ->add('branch',  (int)$branch)
            ->setDefault('stories', '')
            ->join('stories', ',')
            ->join('bugs', ',')
            ->setIF($this->post->build == false, 'build', $buildID)
            ->stripTags($this->config->release->editor->create['id'], $this->config->allowedTags)
            ->remove('allchecker,files,labels,uid')
            ->get();

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
                $build->product   = (int)$productID;
                $build->branch    = (int)$branch;
                $build->name      = $release->name;
                $build->date      = $release->date;
               // $build->builder   = $this->app->user->account;
                $build->desc      = $release->desc;
                $build->execution = 0;
               
                $build = $this->loadModel('file')->processImgURL($build, $this->config->release->editor->create['id']);
                $this->app->loadLang('build');
                $this->dao->insert(TABLE_BUILD)->data($build)
                    ->autoCheck()
                    ->check('name', 'unique', "product = {$productID} AND branch = {$branch} AND deleted = '0'")
                    ->batchCheck($this->config->release->create->requiredFields, 'notempty')
                    ->exec();
                if(dao::isError()) return false;

                $buildID = $this->dao->lastInsertID();
                $release->build = $buildID;
            }
        }

        if($release->build) 
        {
            $buildInfo = $this->dao->select('project, branch')->from(TABLE_BUILD)->where('id')->eq($release->build)->fetch();
            $release->branch  = $buildInfo->branch;
            $release->project = $buildInfo->project;
        }

        $release = $this->loadModel('file')->processImgURL($release, $this->config->release->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_RELEASE)->data($release)
            ->autoCheck()
            ->batchCheck($this->config->release->create->requiredFields, 'notempty')
            ->check('name', 'unique', "product = '{$release->product}' AND branch = '{$release->branch}' AND deleted = '0'");

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

            return $releaseID;
        }

        return false;
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
        $releaseID  = (int)$releaseID;
        $oldRelease = $this->dao->select('*')->from(TABLE_RELEASE)->where('id')->eq($releaseID)->fetch();
        $branch     = $this->dao->select('branch')->from(TABLE_BUILD)->where('id')->eq((int)$this->post->build)->fetch('branch');

        $release = fixer::input('post')->stripTags($this->config->release->editor->edit['id'], $this->config->allowedTags)
            ->add('branch',  (int)$branch)
            ->setIF(!$this->post->marker, 'marker', 0)
            ->cleanInt('product')
            ->remove('files,labels,allchecker,uid')
            ->get();
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
     * Batch unlink story.
     *
     * @param  int    $releaseID
     * @access public
     * @return void
     */
    public function batchUnlinkStory($releaseID)
    {
        $storyList = $this->post->storyIdList;
        if(empty($storyList)) return true;

        $release = $this->getByID($releaseID);
        $release->stories = ",$release->stories,";
        foreach($storyList as $storyID) $release->stories = str_replace(",$storyID,", ',', $release->stories);
        $release->stories = trim($release->stories, ',');
        $this->dao->update(TABLE_RELEASE)->set('stories')->eq($release->stories)->where('id')->eq((int)$releaseID)->exec();

        $this->loadModel('action');
        foreach($this->post->storyIdList as $unlinkStoryID) $this->action->create('story', $unlinkStoryID, 'unlinkedfromrelease', '', $releaseID);
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

    /**
     * Unlink bug.
     *
     * @param  int    $releaseID
     * @param  int    $bugID
     * @param  string $type
     * @access public
     * @return void
     */
    public function unlinkBug($releaseID, $bugID, $type = 'bug')
    {
        $release = $this->getByID($releaseID);
        $field = $type == 'bug' ? 'bugs' : 'leftBugs';
        $release->{$field} = trim(str_replace(",$bugID,", ',', ",{$release->$field},"), ',');
        $this->dao->update(TABLE_RELEASE)->set($field)->eq($release->$field)->where('id')->eq((int)$releaseID)->exec();
        $this->loadModel('action')->create('bug', $bugID, 'unlinkedfromrelease', '', $releaseID);
    }

    /**
     * Batch unlink bug.
     *
     * @param  int    $releaseID
     * @param  string $type
     * @access public
     * @return void
     */
    public function batchUnlinkBug($releaseID, $type = 'bug')
    {
        $bugList = $this->post->unlinkBugs;
        if(empty($bugList)) return true;

        $release = $this->getByID($releaseID);
        $field   = $type == 'bug' ? 'bugs' : 'leftBugs';
        $release->$field = ",{$release->$field},";
        foreach($bugList as $bugID) $release->$field = str_replace(",$bugID,", ',', $release->$field);
        $release->$field = trim($release->$field, ',');
        $this->dao->update(TABLE_RELEASE)->set($field)->eq($release->$field)->where('id')->eq((int)$releaseID)->exec();

        $this->loadModel('action');
        foreach($this->post->unlinkBugs as $unlinkBugID) $this->action->create('bug', $unlinkBugID, 'unlinkedfromrelease', '', $releaseID);
    }

    /**
     * Change status.
     *
     * @param  int    $releaseID
     * @param  string $status
     * @access public
     * @return bool
     */
    public function changeStatus($releaseID, $status)
    {
        $this->dao->update(TABLE_RELEASE)->set('status')->eq($status)->where('id')->eq($releaseID)->exec();
        return dao::isError();
    }

    public function getNamePairs()
    {
      return $this->dao->select('id, name')->from(TABLE_RELEASE)
        ->orderBy('date DESC')
        ->fetchPairs();
    }

    /**
     * TongYanQi 2022/11/2
     * 是否已经全部发送
     */
    public function ifReleasesPushed(string $releaseIds, $type = "qz") : bool
    {
        if(empty($releaseIds)) return true;
        $releaseIdArray = explode(',', trim($releaseIds,','));
        $releases = $this->dao->select('id,pushStatusQz,pushStatusJx,pushFailsQz,pushFailsJx')
            ->from(TABLE_RELEASE)->where('id')->in($releaseIdArray)
            ->fetchall('id');
        if(empty($releases) && !is_array($releases)) return false;
        foreach ($releases as $id => $release) {
            //失败三次且不是成功状态的
            if(strtolower($type) == "qz" && $release->pushFailsQz >= 3 && $release->pushStatusQz != 3 ){
                return false;
            }
            //如果不是3发送成功 改变状态为1, 条件：且不在 1=准备发送 2= 正在发送 （用于从新推送）
            if(strtolower($type) == "qz" && $release->pushStatusQz != 3) {
                $this->dao->update(TABLE_RELEASE)->set('pushStatusQz')->eq(1)->set('remotePathQz')->eq("")->where('id')->eq($id)->andwhere('pushStatusQz')->notin([1, 2])->exec(); //以前的介质没有标记推送
                return false;
            }
            if($type == "jx" && $release->pushStatusJx != 3) {
                return false;
            }
        }
        return true;
    }


    /**
     * 获得版本信息
     *
     * @param $ids
     * @param string $select
     * @return array
     */
    public function getReleaseListByIds($ids, $select = '*'){
        $data = [];
        if(!$ids){
            return $data;
        }
        $isSelectFiles = false;
        if(strpos($select, 'files') !== false || $select  = '*'){
            $isSelectFiles = true;
            if(strpos($select, 'files') !== false){
                $selectTempArray = array_flip(explode(',', $select));
                unset($selectTempArray['files']);
                $selectArray = array_flip($selectTempArray);
                $select = implode(',', $selectArray);
            }
        }

        $ret = $this->dao->select($select)
            ->from(TABLE_RELEASE)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($ids)
            ->orderBy('id_desc')
            ->fetchAll('id');
        if(!$ret){
            return $data;
        }
        if($isSelectFiles){
            foreach ($ret as $key => $val){
                $files = $this->loadModel('file')->getByObject('release', $key);
                $val->files = $files;
            }
        }
        $data = $ret;
        return $data;
    }
}
