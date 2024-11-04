<?php
/**
 * The dept module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     dept
 * @version     $Id: zh-cn.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->dept->common      = '部门结构';
$lang->dept->manageChild = "下级部门";
$lang->dept->edit        = "编辑部门";
$lang->dept->delete      = "删除部门";
$lang->dept->parent      = "上级部门";
$lang->dept->manager     = "负责人";
$lang->dept->name        = "部门名称";
$lang->dept->browse      = "部门维护";
$lang->dept->manage      = "维护部门";
$lang->dept->updateOrder = "更新排序";
$lang->dept->add         = "添加部门";
$lang->dept->grade       = "部门级别";
$lang->dept->order       = "排序";
$lang->dept->dragAndSort = "拖动排序";
$lang->dept->ldapName    = 'LDAP简称';
$lang->dept->planPerson  = '年度计划接口人';
$lang->dept->batchUpdate = '人员部门数据修正';
$lang->dept->updateTitle = '数据修正';
$lang->dept->user        = '用户';
$lang->dept->starttime   = "起始时间";
$lang->dept->endtime     = "截止时间";

$lang->dept->confirmDelete = " 您确定删除该部门吗？";
$lang->dept->successSave   = " 修改成功。";

$lang->dept->error = new stdclass();
$lang->dept->error->hasSons  = '该部门有子部门，不能删除！';
$lang->dept->error->hasUsers = '该部门有职员，不能删除！';

$lang->dept->planPersonEmpty = '年度计划接口人不能为空！年度计划审批为必须节点！';
$lang->dept->dateCompare     = '起始时间不能大于或等于截止时间！';
$lang->dept->paramEntire     = '接口参数不全！';

$lang->dept->deptNull = '空';
$lang->dept->deptAll = '全部';