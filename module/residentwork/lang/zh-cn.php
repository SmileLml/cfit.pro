<?php
$lang->residentwork->common        = '驻场支持-值班视图';
$lang->residentwork->browse        = '值班视图';
$lang->residentwork->recordDutyLog = '填写值班日志';
$lang->residentwork->modifyScheduling = '变更排班';
$lang->residentwork->view          = '值班日志详情';
$lang->residentwork->export        = '导出数据';

$lang->residentwork->basicInfo     = '排班明细';
$lang->residentwork->workLog       = '值班日志';
$lang->residentwork->id            = '编号';
$lang->residentwork->planDate      = '计划值班日期';
$lang->residentwork->actualDate    = '实际值班日期';
$lang->residentwork->user          = '计划值班人员';
$lang->residentwork->actualUser    = '实际值班人员';
$lang->residentwork->application   = '涉及系统';
$lang->residentwork->phone         = '联系方式';
$lang->residentwork->type          = '值班类型';
$lang->residentwork->importantTime = '是否重要时段';
$lang->residentwork->desc          = '值班日志';
$lang->residentwork->createdBy     = '由谁创建';
$lang->residentwork->createdDate   = '创建时间';
$lang->residentwork->mailto        = '抄送给';
$lang->residentwork->exportName    = '值班记录';
$lang->residentwork->actualLeader  = '实际值班组长';
$lang->residentwork->dutyPlace     = '值班地点';
$lang->residentwork->isEmergency   = '是否存在应急事件';
$lang->residentwork->emergencyRemark   = '应急事件简要说明';
$lang->residentwork->warnLogs      = '下一值班重点关注';
$lang->residentwork->enclosure     = '附件';
$lang->residentwork->ccTo          = '抄送给';
$lang->residentwork->fileList      = '已上传附件列表';
$lang->residentwork->analysis      = '支付交易系统运行质量日报分析';


$lang->residentwork->dutyDate         = '值班日期';
$lang->residentwork->requireInfo      = '值班要求';
$lang->residentwork->postTypeInfo     = '值班岗位';
$lang->residentwork->timeType         = '时长类型';
$lang->residentwork->dutyTime         = '值班时长';
$lang->residentwork->dutyDept         = '值班部门';
$lang->residentwork->dutyGroupLeader  = '值班组长';
$lang->residentwork->dutyUser         = '值班人员';

$lang->residentwork->fillInDate       = '日志填写时间';
$lang->residentwork->fillInCreated    = '日志填写人员';
$lang->residentwork->dateType         = '日期类型';
$lang->residentwork->subType          = '值班子类';

$lang->residentwork->dealUsers        = '待处理人员';

$lang->residentwork->confirmDelete = '确认删除该条值班记录？';

$lang->residentwork->typeList = array();
$lang->residentwork->typeList[''] = '';

$lang->residentwork->labelList = array();
$lang->residentwork->labelList['all']    = '所有';
$lang->residentwork->labelList['enable'] = '启用';
$lang->residentwork->labelList['unable'] = '未启用';

$lang->residentwork->importantTimeList[2] = '否';
$lang->residentwork->importantTimeList[1] = '是';
$lang->residentwork->workExport = '导出日排班明细';
$lang->residentwork->workexportAll = '值班视图导出';

$lang->residentwork->mail                = new stdclass();
$lang->residentwork->mail->create        = new stdclass();
$lang->residentwork->mail->edit          = new stdclass();
$lang->residentwork->mail->create->title = "%s提交了值班申请 #%s";
$lang->residentwork->mail->edit->title   = "%s编辑了值班申请 #%s";

//值班日志模板导出字段表头
explode(',',"dutyDate,type,subType,requireInfo,postType,timeType,dutyDuration,dutyUserDept,dutyGroupLeader,dutyUser,createdBy,createdDate,area,");

$lang->residentwork->exportFileds = new stdClass();
$lang->residentwork->exportFileds->dutyDate = '值班日期';
$lang->residentwork->exportFileds->type = '值班类型';
$lang->residentwork->exportFileds->subType = '值班子类';
$lang->residentwork->exportFileds->requireInfo = '值班要求';
$lang->residentwork->exportFileds->postType = '值班岗位';
$lang->residentwork->exportFileds->timeType = '时长类型';
$lang->residentwork->exportFileds->dutyDuration = '值班时长';
$lang->residentwork->exportFileds->dutyUserDept = '值班部门';
$lang->residentwork->exportFileds->dutyGroupLeader = '值班组长';
$lang->residentwork->exportFileds->dutyUser = '值班人员';
$lang->residentwork->exportFileds->createdDate= '日志填写时间';
$lang->residentwork->exportFileds->createdBy = '日志填写人员';
$lang->residentwork->exportFileds->area = '值班地点';
$lang->residentwork->exportFileds->actualLeader = $lang->residentwork->actualLeader;
$lang->residentwork->exportFileds->user = $lang->residentwork->actualUser;
$lang->residentwork->exportFileds->dateType = $lang->residentwork->dateType;
$lang->residentwork->exportFileds->isEmergency = $lang->residentwork->isEmergency;
$lang->residentwork->exportFileds->remark = $lang->residentwork->emergencyRemark;
$lang->residentwork->exportFileds->logs = $lang->residentwork->desc;
$lang->residentwork->exportFileds->warnLogs = $lang->residentwork->warnLogs;

//启用排班验证
$lang->residentwork->checkDutyLogResultList = [];
$lang->residentwork->checkDutyLogResultList['dutyDateError']      = '该时间大于当天时间，不能填写值班日志';
$lang->residentwork->checkDutyLogResultList['dutyDeptCheckError'] = '该时间排班信息未全部审核通过，不能填写值班日志';
//日志推送状态
$lang->residentwork->logPushTitle = "日志推送状态";
$lang->residentwork->logPushStatusArray = [];
$lang->residentwork->logPushStatusArray[0] = '暂未推送';
$lang->residentwork->logPushStatusArray[1] = '推送成功';
$lang->residentwork->logPushStatusArray[2] = '推送失败';
$lang->residentwork->pushTitle = "推送状态";
$lang->residentwork->pushTxt = "是否提交当前值班日志，提交后该值班信息将推送至总中心，并邮件抄送相关责任人";
$lang->residentwork->create = "值班日报";
$lang->residentwork->createlog = "新建值班日志";
$lang->residentwork->editlog = "编辑自建日志";
$lang->residentwork->logSource = "日报来源";
$lang->residentwork->exportFileds->logSource = $lang->residentwork->logSource;
$lang->residentwork->exportFileds->pushTitle = $lang->residentwork->pushTitle;



