<?php
$config->build = new stdclass();
$config->build->create = new stdclass();
$config->build->edit   = new stdclass();
$config->build->deal   = new stdclass();
$config->build->copy   = new stdclass();
$config->build->rebuild   = new stdclass();
$config->build->workloadedit = new stdclass();
$config->build->back         = new stdclass();
$config->build->save         = new stdclass();
$config->build->create->requiredFields = 'product,version,builder,testUser,svnPath,app,taskName';
$config->build->edit->requiredFields   = 'product,version,builder,testUser,svnPath,app,taskName';
$config->build->copy->requiredFields   = 'product,version,builder,testUser,svnPath,app,taskName';
$config->build->rebuild->requiredFields   = 'desc';
$config->build->deal->requiredFields   = 'desc';
$config->build->workloadedit->requiredFields = 'account,after';
$config->build->back->requiredFields         = '';
$config->build->save->requiredFields = 'product,version,app';

$config->build->editor = new stdclass();
$config->build->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->build->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->build->editor->copy = array('id' => 'desc', 'tools' => 'simpleTools');
$config->build->editor->rebuild = array('id' => 'desc', 'tools' => 'simpleTools');
$config->build->editor->deal   = array('id' => 'comment,specialPassReason', 'tools' => 'simpleTools');
$config->build->editor->workloadedit   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->build->editor->workloaddelete = array('id' => 'comment', 'tools' => 'simpleTools');
$config->build->editor->back = array('id' => 'comment', 'tools' => 'simpleTools');
$config->build->editor->batchdeal = array('id' => 'comment,plateName', 'tools' => 'simpleTools');
global $lang;
$config->build->search['module']             = 'build';
$config->build->search['fields']['name']     = $lang->build->name;
$config->build->search['fields']['id']       = $lang->build->id;
$config->build->search['fields']['dealuser']       = $lang->build->dealuser;
$config->build->search['fields']['status']       = $lang->build->status;
$config->build->search['fields']['app']      = $lang->build->app;
$config->build->search['fields']['product']  = $lang->build->product;
$config->build->search['fields']['scmPath']  = $lang->build->scmPath;
$config->build->search['fields']['filePath'] = $lang->build->filePath;
$config->build->search['fields']['date']     = $lang->build->date;
$config->build->search['fields']['builder']  = $lang->build->builder;
$config->build->search['fields']['desc']     = $lang->build->desc;

$config->build->search['params']['name']     = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->build->search['params']['dealuser']  = array('operator' => 'include', 'control' => 'select',  'values' => 'users');
$config->build->search['params']['status']     = array('operator' => 'include', 'control' => 'select',  'values' => array(''=>'')+ array_diff_key($this->lang->build->statusList,[''=>'-']));
$config->build->search['params']['app']      = array('operator' => '=',       'control' => 'select', 'values' => '');
$config->build->search['params']['product']  = array('operator' => '=',       'control' => 'select', 'values' => 'products');
$config->build->search['params']['scmPath']  = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->build->search['params']['filePath'] = array('operator' => 'include', 'control' => 'input',  'values' => '');
$config->build->search['params']['date']     = array('operator' => '=',       'control' => 'input',  'values' => '', 'class' => 'date');
$config->build->search['params']['builder']  = array('operator' => '=',       'control' => 'select', 'values' => 'users');
$config->build->search['params']['desc']     = array('operator' => 'include', 'control' => 'input',  'values' => '');