<?php
$lang->componentthirdaccount->common   = '第三方组件使用台账';
$lang->componentthirdaccount->code = '编号';
$lang->componentthirdaccount->appname  = '系统名称';
$lang->componentthirdaccount->productname  = '产品名称';
$lang->componentthirdaccount->productversion   = '产品版本';
$lang->componentthirdaccount->productdept  = '产品所属部门';
$lang->componentthirdaccount->productconnect   = '产品联系人';
$lang->componentthirdaccount->componentname = '使用组件名称';
$lang->componentthirdaccount->componentversion = '使用组件版本';
$lang->componentthirdaccount->componentlevel = '级别';
$lang->componentthirdaccount->componentcategory = '类别';
$lang->componentthirdaccount->comment = '备注';
$lang->componentthirdaccount->create = '分产品批量管理';
$lang->componentthirdaccount->browse = '列表';
$lang->componentthirdaccount->export = '导出';
$lang->componentthirdaccount->tips = '使用说明';
$lang->componentthirdaccount->tipContent = '1.若当前登录人不是所选产品的联系人，则无法管理使用台账。
2.优先根据系统+产品+版本找之前配置的使用台账信息，初始化到组件使用列表。
3.若2未找到，则找库中存储的该系统+产品历史版本中距离当前版本最近的版本对应的组件使用信息，初始化到组件使用列表。
4.若3未找到，则需要用户完全新增组件使用信息。';
$lang->componentthirdaccount->usedbrowse = '组件使用列表';
$lang->componentthirdaccount->createcomponentname = '组件名称';
$lang->componentthirdaccount->createcomponentversion = '组件版本';
$lang->componentthirdaccount->emptyObject            = '『%s 』不能为空。';
$lang->componentthirdaccount->notproductconnect            = '您不是该产品的联系人，无法维护组件使用台账';
$lang->componentthirdaccount->exportExcel= '第三方组件';
$lang->componentthirdaccount->vulnerabilityLevel= '使用组件漏洞级别';
$lang->componentthirdaccount->customusedbrowse = '自定义组件使用列表';
$lang->componentthirdaccount->customComponent= '自定义组件名称';
$lang->componentthirdaccount->customComponentVersion= '自定义组件版本';
$lang->componentthirdaccount->createrepeat= '组件重复，请重新选择';

$lang->componentthirdaccount->labelList['all'] = '所有';

$lang->componentthirdaccount->apiItems['componentName']                           = ['name'=>'组件名称', 'required' => 1, 'target' => 'componentName'];
$lang->componentthirdaccount->apiItems['appName']                             = ['name'=>'系统名称', 'required' => 1, 'target' => 'appName'];
$lang->componentthirdaccount->apiItems['productName']                             = ['name'=>'产品名称', 'required' => 1, 'target' => 'productName'];
$lang->componentthirdaccount->apiItems['productVersion']                             = ['name'=>'产品版本', 'required' => 1, 'target' => 'productVersion'];