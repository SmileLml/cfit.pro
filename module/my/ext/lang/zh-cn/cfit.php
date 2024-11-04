<?php

$lang->my->myReviewList                      = [];
$lang->my->myReviewList['opinion']           = '外部-需求意向';
$lang->my->myReviewList['requirement']       = '外部-需求任务';
$lang->my->myReviewList['demand']            = '外部-需求条目';
$lang->my->myReviewList['opinioninside']     = '内部-需求意向';
$lang->my->myReviewList['requirementinside'] = '内部-需求任务';
$lang->my->myReviewList['demandinside']      = '内部-需求条目';
$lang->my->myReviewList['problem']           = '问题';
$lang->my->myReviewList['productionchange']  = '内部自建投产/变更';
$lang->my->myReviewList['secondorder']       = '任务工单';
$lang->my->myReviewList['deptorder']         = '部门工单';
$lang->my->myReviewList['projectplan']       = '年度计划';
//$lang->my->myReviewList['defect']            = '清总缺陷';
$lang->my->myReviewList['projectplanChange'] = '年度计划变更';
$lang->my->myReviewList['projectplanStart']  = '申请立项';
$lang->my->myReviewList['projectplansh']       = '年度计划(上海)';
$lang->my->myReviewList['projectplanshChange'] = '年度计划变更(上海)';
$lang->my->myReviewList['projectplanshStart']  = '申请立项(上海)';
$lang->my->myReviewList['change']            = '项目变更';
$lang->my->myReviewList['build']             = '版本测试'; //20220930 增加测试版本
$lang->my->myReviewList['projectrelease']    = '版本发布';
$lang->my->myReviewList['putproduction']    = '金信-投产移交';
$lang->my->myReviewList['modify']            = '金信-生产变更';
//$lang->my->myReviewList['fix']               = '金信-数据修正'; //2023-06-01 需求id:2346 去掉
$lang->my->myReviewList['outwarddelivery']   = '清总-对外交付';
$lang->my->myReviewList['gain']              = '金信-数据获取';
$lang->my->myReviewList['gainqz']            = '清总-数据获取';
$lang->my->myReviewList['credit']            = '征信交付';
$lang->my->myReviewList['datamanagement']    = '数据管理';
$lang->my->myReviewList['sectransfer']       = '对外移交';
$lang->my->myReviewList['closingitem']       = '项目结项';
$lang->my->myReviewList['closingadvise']     = '项目结项意见';
$lang->my->myReviewList['osspchange']        = '体系OSSP变更申请';
$lang->my->myReviewList['localesupport']   = '现场支持';
$lang->my->myReviewList['residentsupport']   = '驻场支持';
$lang->my->myReviewList['copyright']         = '自主知识产权';
$lang->my->myReviewList['copyrightqz']       = '清总知识产权';
$lang->my->myReviewList['component']         = '组件管理';
$lang->my->myReviewList['cmdbsync']         = 'CMDB同步';
$lang->my->myReviewList['environmentorder']         = '环境部署工单';
$lang->my->myReviewList['authorityapply']         = '权限申请';
//$lang->my->myReviewList['review']            = '项目评审'; //20220712 刪除
//$lang->my->myReviewList['reviewqz']          = '清总-评审列表';
$lang->my->myReviewList['qualitygate']       = '安全门禁';

$lang->my->see = '查看';
$lang->my->confirm = '确认';

$lang->my->authorization = '授权管理';
$lang->my->objectType = '所属模块';
$lang->my->authorizedPerson = '被授权人员';
$lang->my->startTime = '授权开始日期';
$lang->my->endTime = '授权结束日期';
$lang->my->permanently = '永久授权';
$lang->my->authorizer = '授权人';
$lang->my->authorizerTerm = '授权期限';
$lang->my->context = '内容说明';
$lang->my->enabled = '是否启用';
$lang->my->operate = '操作';
$lang->my->num                      = '序号';

$lang->my->objectTypeList                      = [];
$lang->my->objectTypeList['modify']                               = '金信交付-生产变更';
$lang->my->objectTypeList['outwarddelivery']                      = '清总交付-对外交付';

$lang->my->commentUpdate = $lang->my->authorizer.'：“%s”，'.$lang->my->objectType.'：“%s”，'.$lang->my->authorizedPerson.'：由“%s”修改为“%s”，'.$lang->my->startTime.'：由“%s”修改为“%s”，'.$lang->my->endTime.'：由“%s”修改为“%s”，'.$lang->my->permanently.'：由“%s”修改为“%s”，'
    .$lang->my->enabled.'：由“%s”修改为“%s”';
$lang->my->permanentlyList                      = [];
$lang->my->permanentlyList['1']                               = '否';
$lang->my->permanentlyList['2']                      = '是';

$lang->my->startTimeError                      = '【序号：%s】开始日期要大于当前日期';
$lang->my->endTimeError                      = '【序号：%s】结束日期要大于开始日期';
$lang->my->authorizerNullError                      = '【序号：%s】被授权人不能为空';
$lang->my->objectTypeNullError                      = '【序号：%s】所属模块不能为空';
$lang->my->timeNullError                      = '【序号：%s】请选择授权日期或勾选永久授权';
$lang->my->cancelcontext                      = '您的授权已于%s取消';
$lang->my->remindcontext = '你的授权即将到期，望知悉';
$lang->my->choseError = '请勾选授权模块';
$lang->my->timeError                     = '【序号：%s】时间格式错误';
$lang->my->notice =  '温馨提示：<br>
1.授权管理可按照模块分别授权，支持按时间段授权或永久授权；<br>
2.授权配置后，被授权人将与授权人同步收到相关申请，被授权人可进行操作；<br>
3.授权人可主动取消授权或超出授权期限后将自动取消授权；<br>
4.其他说明：支持授权的模块将逐步增加';