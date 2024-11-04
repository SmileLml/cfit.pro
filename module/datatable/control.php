<?php
/**
 * The view file of datatable module of ZenTaoPMS.
 *
 * @copyright   Copyright 2014-2014 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     business(商业软件)
 * @author      Hao sun <sunhao@cnezsoft.com>
 * @package     datatable
 * @version     $Id$
 * @link        http://www.zentao.net
 */
class datatable extends control
{
    /**
     * Construct function, set menu.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save config
     *
     * @access public
     * @return void
     */
    public function ajaxSave()
    {
        if(!empty($_POST))
        {
            $account = $this->app->user->account;
            if($account == 'guest')
            {
                $this->send(['result' => 'fail', 'target' => $target, 'message' => 'guest.']);
            }

            $name = 'datatable.' . $this->post->target . '.' . $this->post->name;
            $this->loadModel('setting')->setItem($account . '.' . $name, $this->post->value);
            if($this->post->allModule !== false)
            {
                $this->setting->setItem("$account.execution.task.allModule", $this->post->allModule);
            }
            if($this->post->global)
            {
                $this->setting->setItem('system.' . $name, $this->post->value);
            }

            if(dao::isError())
            {
                $this->send(['result' => 'fail', 'message' => 'dao error.']);
            }
            $this->send(['result' => 'success']);
        }
    }

    /**
     * custom fields.
     *
     * @param string $module
     * @param string $method
     * @access public
     * @return void
     */
    public function ajaxCustom($module, $method)
    {
        $target = $module . ucfirst($method);
        $mode   = isset($this->config->datatable->$target->mode) ? $this->config->datatable->$target->mode : 'table';
        $key    = $mode == 'datatable' ? 'cols' : 'tablecols';
        if($module == 'testtask')
        {
            if($method == 'browse')
            {
                $this->loadModel('testtask');
                $this->config->testtask->datatable = $this->config->testtask->datatableMainBrowse;
            }
            else
            {
                $this->loadModel('testcase');
                $this->app->loadConfig('testtask');
                $this->config->testcase->datatable->defaultField                  = $this->config->testtask->datatable->defaultField;
                $this->config->testcase->datatable->fieldList['actions']['width'] = '100';
            }
        }
        elseif($module == 'testcase')
        {
            $this->loadModel('testcase');
            unset($this->config->testcase->datatable->fieldList['assignedTo']);
        }
        elseif($module == 'project')
        {
            $this->loadModel('project');
            if($method == 'defect')
            {
                $this->config->project->datatable = $this->config->project->datatableDefect;
            }
            elseif($method == 'testtask')
            {
                $this->config->project->datatable = $this->config->project->datatableTesttask;
            }
            elseif($method == 'testcase')
            {
                $this->loadModel('testcase');
                $this->config->project->datatable = $this->config->testcase->datatable;

                $this->config->project->datatable->defaultField = $this->config->project->datatableTestcase->defaultField;

                unset($this->config->project->datatable->fieldList['assignedTo']);
            }
            elseif($method == 'bug')
            {
                $this->loadModel('bug');
                $this->config->project->datatable = $this->config->bug->datatable;

                $this->config->project->datatable->defaultField = $this->config->project->datatableBug->defaultField;
            }
            elseif($method == 'testsuite')
            {
                $this->config->project->datatable = $this->config->project->datatableTestsuite;
            }
            elseif($method == 'testreport')
            {
                $this->config->project->datatable = $this->config->project->datatableTestreport;
            }
        }

        $this->view->module = $module;
        $this->view->method = $method;
        $this->view->mode   = $mode;

        $module = zget($this->config->datatable->moduleAlias, "$module-$method", $module);

        $setting = '';
        if(isset($this->config->datatable->$target->$key))
        {
            $setting = $this->config->datatable->$target->$key;
        }
        if(empty($setting))
        {
            $this->loadModel($module);
            $setting = json_encode($this->config->$module->datatable->defaultField);
        }
        $this->view->cols    = $this->datatable->getFieldList($module, $method);
        $this->view->setting = $setting;
        $this->display();
    }

    public function ajaxFixedSort($module, $method)
    {
        $this->loadModel($module);
        $this->loadModel('setting');
        $langModule = $module;

        if($_POST)
        {
            if($_POST['fixedField'] == '')
            {
                //echo js::alert($this->lang->datatable->fixedFieldEmpty);
                //die();
                $this->setting->deleteItems("owner={$this->app->user->account}&module{=$module}&section={$method}&key=fixedSort");
                die(js::reload('parent'));
            }
            $settings = ["{$method}.fixedSort" => $_POST['fixedField'] . '_' . $_POST['fixedSort']];
            $this->setting->setItems("{$this->app->user->account}.{$module}", $settings);

            if(dao::isError())
            {
                die(js::error(dao::getError()));
            }
            echo js::alert($this->lang->saveSuccess);
            die(js::reload('parent'));
        }

        $fieldList = [];

        if($module == 'project')
        {
            if($method == 'defect')
            {
                $fieldList = $this->config->project->datatableDefect->fieldList;
            }
            elseif($method == 'testtask')
            {
                $fieldList = $this->config->project->datatableTesttask->fieldList;
            }
            elseif($method == 'testcase')
            {
                $langModule = 'testcase';
                $this->loadModel($langModule);

                $fieldList = $this->config->testcase->datatable->fieldList;
            }
            elseif($method == 'bug')
            {
                $langModule = 'bug';
                $this->loadModel($langModule);
                
                $fieldList = $this->config->bug->datatable->fieldList;
            }
            elseif($method == 'testsuite')
            {
                $fieldList = $this->config->project->datatableTestsuite->fieldList;
            }
            elseif($method == 'testreport')
            {
                $fieldList = $this->config->project->datatableTestreport->fieldList ;
            }
        }
        elseif($module == 'testtask')
        {
            if($method == 'browse')
            {
                $fieldList = $this->config->testtask->datatableMainBrowse->fieldList;
            }
            elseif($method == 'cases')
            {
                $this->loadModel('testcase');
                $langModule = 'testcase';
                $fieldList  = $this->config->testcase->datatable->fieldList;
            }
        }

        if(empty($fieldList))
        {
            $fieldList = $this->config->$module->datatable->fieldList;
        }

        $this->loadModel($langModule);
        /* 获取可以排序的字段。*/
        $sortFields = ['' => ''];
        foreach($fieldList as $key => $field)
        {
            if(!isset($field['sort']) or $field['sort'] != 'no')
            {
                $title = zget($this->lang->$langModule, $field['title'], null);

                if(empty($title)) $title = zget($this->lang->$module, $field['title'], null);
                if(empty($title)) $title = zget($this->lang, $field['title'], $field['title']);

                $sortFields[$key] = $title;
            }
        }

        $fixedSetting = $this->setting->getItem("owner={$this->app->user->account}&module={$module}&section={$method}&key=fixedSort");
        $defaultKey   = '';
        $defaultSort  = '';
        if($fixedSetting)
        {
            $fixedSetting = explode('_', $fixedSetting);
            $defaultKey   = $fixedSetting[0];
            $defaultSort  = $fixedSetting[1];
        }

        $this->view->sortFields  = $sortFields;
        $this->view->module      = $module;
        $this->view->method      = $method;
        $this->view->defaultKey  = $defaultKey;
        $this->view->defaultSort = $defaultSort;

        $this->display();
    }

    /**
     * Ajax reset cols
     *
     * @param string $module
     * @param string $method
     * @param string $confirm
     * @access public
     * @return void
     */
    public function ajaxReset($module, $method, $system = 0, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            die(js::confirm($this->lang->datatable->confirmReset, inlink('ajaxReset', "module=$module&method=$method&system=$system&confirm=yes")));
        }

        $account = $this->app->user->account;
        $target  = $module . ucfirst($method);
        $mode    = isset($this->config->datatable->$target->mode) ? $this->config->datatable->$target->mode : 'table';
        $key     = $mode == 'datatable' ? 'cols' : 'tablecols';

        $this->loadModel('setting')->deleteItems("owner=$account&module=datatable&section=$target&key=$key");
        if($system)
        {
            $this->setting->deleteItems("owner=system&module=datatable&section=$target&key=$key");
        }
        die(js::reload('parent'));
    }
}
