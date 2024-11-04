<?php
/**
 * The model file of review module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      wangjiurong
 * @package     model
 * @version     $Id: control.php 5107 2020-09-09 09:46:12Z xieqiyu@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class archiveModel extends model{


    /**
     * 添加规档信息
     *
     * @param $projectId
     * @param $objectType
     * @param $objectID
     * @param $version
     * @param $params
     * @return bool
     */
    public function addArchiveInfo($projectId, $objectType, $objectID, $version, $params){
        if(!($projectId && $objectType && $objectID && $params)){
            return false;
        }
        $params->project     = $projectId;
        $params->objectType  = $objectType;
        $params->objectID    = $objectID;
        $params->version     = $version;
        $params->createdBy   = $this->app->user->account;
        $params->createdTime = helper::now();
        $this->dao->insert(TABLE_ARCHIVE)->data($params)->exec();
        return true;
    }

    /**
     *获得归档信息
     *
     * @param $objectType
     * @param $objectID
     * @param string $version
     * @return array|bool
     */
    public function getArchiveList($objectType, $objectID, $version = ''){
        $data = [];
        if(!($objectType && $objectID)){
            return $data;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_ARCHIVE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('deleted')->eq('0')
            ->beginIF($version != '')->andWhere('version')->eq($version)->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }


    /**
     * 获得最大版本的基线列表
     *
     * @param $objectType
     * @param $objectID
     * @return array
     */
    public function getMaxVersionArchiveList($objectType, $objectID){
        $data = [];
        if(!($objectType && $objectID)){
            return $data;
        }
        $ret = $this->dao->select('MAX(version) as version')
            ->from(TABLE_ARCHIVE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        if(!$ret){
            return $data;
        }
        //最大版本
        $version = $ret->version;
        $ret = $this->dao->select('*')
            ->from(TABLE_ARCHIVE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('version')->eq($version)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     *获得归档信息
     *
     * @param $objectType
     * @param $objectIds
     * @return array|bool
     */
    public function getArchiveListByObjectIds($objectType, $objectIds){
        $data = [];
        if(!($objectType && $objectIds)){
            return $data;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_ARCHIVE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->in($objectIds)
            ->andWhere('deleted')->eq('0')
            ->fetchAll();
        if($ret){
            foreach ($ret as $val){
                $objectID = $val->objectID;
                $data[$objectID][] = $val;
            }
        }
        return $data;
    }

    /**
     *获得某项目空间某个变更的所有归档信息
     *
     * @param $objectType
     * @param $objectID
     * @param string $version
     * @return array|bool
     */
    public function getArchiveAllList($project,$objectType, $objectID, $version = ''){
        $data = [];
        if(!($objectType && $objectID)){
            return $data;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_ARCHIVE)
            ->where('project')->eq($project)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('deleted')->eq('0')
            ->beginIF($version != '')->andWhere('version')->eq($version)->fi()
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 删除归档信息
     * @param $archiveIDS
     */
    public function deleteAll($archiveIDS){
        $this->dao->update(TABLE_ARCHIVE)->set('deleted')->eq('1')->where('id')->in($archiveIDS)->exec();
    }

}
