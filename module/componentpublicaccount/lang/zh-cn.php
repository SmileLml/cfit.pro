<?php
$lang->componentpublicaccount->common   = '公共组件使用台账';
$lang->componentpublicaccount->id = '编号';
$lang->componentpublicaccount->appname  = '系统名称';
$lang->componentpublicaccount->productname  = '产品名称';
$lang->componentpublicaccount->productversion   = '产品版本';
$lang->componentpublicaccount->productdept  = '产品所属部门';
$lang->componentpublicaccount->productconnect   = '产品联系人';
$lang->componentpublicaccount->componentname = '使用组件名称';
$lang->componentpublicaccount->componentversion = '使用组件版本';
$lang->componentpublicaccount->componentlevel = '级别';
$lang->componentpublicaccount->componentcategory = '类别';
$lang->componentpublicaccount->comment = '备注';
$lang->componentpublicaccount->create = '管理使用台账';
$lang->componentpublicaccount->browse = '列表';
$lang->componentpublicaccount->export = '导出';
$lang->componentpublicaccount->tips = '使用说明';
$lang->componentpublicaccount->tipContent = '1.若当前登录人不是所选产品的联系人，则无法管理使用台账。
2.优先根据系统+产品+版本找之前配置的使用台账信息，初始化到组件使用列表。
3.若2未找到，则找库中存储的该系统+产品历史版本中距离当前版本最近的版本对应的组件使用信息，初始化到组件使用列表。
4.若3未找到，则需要用户完全新增组件使用信息。';
$lang->componentpublicaccount->usedbrowse = '组件使用列表';
$lang->componentpublicaccount->createcomponentname = '组件名称';
$lang->componentpublicaccount->createcomponentversion = '组件版本';
$lang->componentpublicaccount->emptyObject            = '『%s 』不能为空。';
$lang->componentpublicaccount->notproductconnect            = '您不是该产品的联系人，无法维护组件使用台账';
$lang->componentpublicaccount->exportExcel= '公共技术组件';
$lang->componentpublicaccount->createrepeat= '组件重复，请重新选择';

$lang->componentpublicaccount->labelList['all'] = '所有';




$lang->componentpublicaccount->componentpublicaccountTip    = '备注：当前登录人只能管理自己所负责的公共技术组件（根据公共技术组件中的“联系人”判断）';
$lang->componentpublicaccount->componentpublicaccountWaring    = '使用注意：先选择组件名称再选组件版本，最后录入台账。如需切换组件，先将当前录入信息保存，否则页面会刷新清空已录入但未保存的台账信息';
$lang->componentpublicaccount->componentProjectList         = '使用组件项目列表';
$lang->componentpublicaccount->projectDept                  = '项目所属部门';
$lang->componentpublicaccount->projectName                  = '项目名称';
$lang->componentpublicaccount->startYear                    = '开始使用年份';
$lang->componentpublicaccount->startQuarter                 = '季度';
$lang->componentpublicaccount->componentDept                = '组件维护部门';
$lang->componentpublicaccount->projectManager               = '项目经理';
$lang->componentpublicaccount->startTime                    = '开始使用时间';
$lang->componentpublicaccount->createTime                   = '录入时间';
$lang->componentpublicaccount->componentNameError           = '请选择组件名称';
$lang->componentpublicaccount->componentVersionError        = '请选择组件版本';
$lang->componentpublicaccount->commentError                 = '备注不可超过300个字节(约100汉字)';

$lang->componentpublicaccount->years['']     = '';
$lang->componentpublicaccount->years['2020'] = '2020年';
$lang->componentpublicaccount->years['2021'] = '2021年';
$lang->componentpublicaccount->years['2022'] = '2022年';
$lang->componentpublicaccount->years['2023'] = '2023年';
$lang->componentpublicaccount->years['2024'] = '2024年';
$lang->componentpublicaccount->years['2025'] = '2025年';
$lang->componentpublicaccount->years['2026'] = '2026年';
$lang->componentpublicaccount->years['2027'] = '2027年';
$lang->componentpublicaccount->years['2028'] = '2028年';
$lang->componentpublicaccount->quarters['']  = '';
$lang->componentpublicaccount->quarters['1'] = '第一季度';
$lang->componentpublicaccount->quarters['2'] = '第二季度';
$lang->componentpublicaccount->quarters['3'] = '第三季度';
$lang->componentpublicaccount->quarters['4'] = '第四季度';

$lang->componentpublicaccount->levelList['company'] = '公司级';
$lang->componentpublicaccount->levelList['dept']    = '部门级';

$lang->componentpublicaccount->projectrepeat        = '项目名称重复，请重新选择';
$lang->componentpublicaccount->projectrepeatLineError        = '第%s行”项目名称“重复，请重新选择';
$lang->componentpublicaccount->projectDateError     = '“开始使用时间”不可以早于该项目的“立项时间”';
$lang->componentpublicaccount->projectDateLineError     = '第%s行“开始使用时间”不可以早于该项目的“立项时间”';


$lang->componentpublicaccount->action = new stdclass();
$lang->componentpublicaccount->action->createdaccount   = array('main' => '$date, 由 <strong>$actor</strong> 维护公共技术组件台账。');