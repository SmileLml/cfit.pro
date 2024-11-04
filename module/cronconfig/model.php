<?php
/**
 * The model file of cron module of ZenTaoCMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     cronconfig
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class cronconfigModel extends model
{
    /**
     * Get by Id.
     *
     * @param  int    $cronID
     * @access public
     * @return object
     */
    public function getById($cronID)
    {
        return $this->dao->select('*')
            ->from(TABLE_CRONCONFIG)
            ->where('id')->eq($cronID)
            ->andWhere('deleted')->eq('0')
            ->fetch();
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: t_wangjiurong
     * Year: 2023
     * Date: 2023/12/12
     * Time: 11:30
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->cronconfig->search['actionURL'] = $actionURL;
        $this->config->cronconfig->search['queryID']   = $queryID;
        $this->loadModel('search')->setSearchParams($this->config->cronconfig->search);
    }

    /**
     * 获得列表
     *
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return array
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null){
        $data = [];
        $cronconfigQuery = '';
        if($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('cronconfigQuery', $query->sql);
                $this->session->set('cronconfigForm', $query->form);
            }

            if($this->session->cronconfigQuery == false) $this->session->set('cronconfigQuery', ' 1 = 1');

            $cronconfigQuery = $this->session->cronconfigQuery;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_CRONCONFIG)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($cronconfigQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * 创建记录
     *
     * @return mixed
     */
    public function create(){
        $data = fixer::input('post')
            ->add('createBy', $this->app->user->account)
            ->add('createTime', helper::now() )
            ->get();
        $file = '../../cli/'.$data->command;
        if(!file_exists($file)){
            dao::$errors['command'] = $this->lang->cronconfig->commandNotExists;
            return false;
        }
        //新增
        $this->dao->insert(TABLE_CRONCONFIG)->data($data)->autoCheck()
            ->batchCheck($this->config->cronconfig->create->requiredFields, 'notempty')
            ->check('command', 'unique', "deleted = '0'")
            ->exec();
        $recordID = $this->dao->lastInsertId();
        return $recordID;
    }

    /**
     * 修改记录
     *
     * @param $cronID
     * @return array
     */
    public function update($cronID){
        $data = fixer::input('post')
            ->get();
        $oldInfo = $this->getById($cronID);

        //更新信息
        $updateParams = new stdClass();
        if(isset($data->command) && ($data->command != $oldInfo->command)){
            $updateParams->command = $data->command;
        }
        if(isset($data->remark) && ($data->remark != $oldInfo->remark)){
            $updateParams->remark = $data->remark;
        }
        if(isset($data->status) && ($data->status != $oldInfo->status)){
            $updateParams->status = $data->status;
        }
        if(empty((array)$updateParams)){
            dao::$errors[''] = $this->lang->cronconfig->noFieldEdit;
            return dao::$errors;
        }

        $file = '../../cli/'.$data->command;
        if(!file_exists($file)){
            dao::$errors['command'] = $this->lang->cronconfig->commandNotExists;
            return false;
        }

        $this->dao->update(TABLE_CRONCONFIG)->data($updateParams)->autoCheck()
            ->batchCheck($this->config->cronconfig->edit->requiredFields, 'notempty')
            ->check('command', 'unique', "id != $cronID AND deleted = '0'")
            ->where('id')->eq($cronID)
            ->exec();
        if($updateParams->status){
            $this->setCronStatusSession($oldInfo->command, $updateParams->status);
        }
        //返回差异
        return common::createChanges($oldInfo, $updateParams);
    }

    /**
     * 删除操作
     *
     * @param string $cronID
     * @return array|void
     */
    function deleted($cronID){
        $data = fixer::input('post')
            ->get();
        if(!(isset($data->comment) && !empty($data->comment))){
            dao::$errors['comment'] = $this->lang->cronconfig->commentEmpty;
            return dao::$errors;
        }
        $oldInfo = $this->getById($cronID);
        $updateParams = new stdClass();
        $updateParams->deleted = '1';

        $this->dao->update(TABLE_CRONCONFIG)->data($updateParams)->autoCheck()
            ->where('id')->eq($cronID)
            ->exec();
        //返回差异
        return common::createChanges($oldInfo, $updateParams);
    }

    /**
     * getByFileName
     *
     * @param  int    $fileName
     * @param $select
     * @access public
     * @return object
     */
    public function getByFileName($fileName, $select = '*')
    {
        return $this->dao->select($select)
            ->from(TABLE_CRONCONFIG)
            ->where('command')->eq($fileName)
            ->andWhere('deleted')->eq('0')
            ->fetch();
    }

    /**
     *
     * @param $fileName
     * @param $status
     */
    public function setCronStatusSession($fileName, $status){
        $sessionName = $fileName.'CronStatus';
        $this->session->set($sessionName, $status);
    }

    /**
     *查询该文件是否停止
     *
     * @param $fileName
     * @return bool
     */
    public function getCronIsStop($fileName){
        $isStop = false;
        $status = 'normal';
        $sessionName = $fileName.'CronStatus';
        if(isset($this->session->$sessionName) && ($this->session->$sessionName == 'stop')){
            $isStop = true;
            return $isStop;
        }
        $info = $this->getByFileName($fileName);
        if($info){
            $status = $info->status;
            if($status == 'stop'){
                $isStop = true;
            }
        }
        $this->setCronStatusSession($fileName, $status);
        return $isStop;
    }
}
