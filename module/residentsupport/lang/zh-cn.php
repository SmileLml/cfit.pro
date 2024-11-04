<?php
$lang->residentsupport->common      = '驻场支持';
$lang->residentsupport->index       = '驻场支持菜单';
$lang->residentsupport->calendar    = '日历视图';
$lang->residentsupport->browse      = '部门视图';
$lang->residentsupport->view        = '值班详情';
$lang->residentsupport->export      = '导出原始模板';
$lang->residentsupport->exportRostering     = "导出排班模板";
$lang->residentsupport->exportRosteringData = '导出排班数据';
$lang->residentsupport->import              = '导入排班模板';
$lang->residentsupport->importrosteringData = "导入排班数据";
$lang->residentsupport->editedtemplate = "编辑模板";

$lang->residentsupport->rostering   = "排班";
$lang->residentsupport->onLineScheduling = '线上排班';
$lang->residentsupport->submit      = '提交审批';
$lang->residentsupport->review      = '审核';
$lang->residentsupport->enableScheduling = '启用排班';


$lang->residentsupport->editScheduling   = '编辑排班';
$lang->residentsupport->deleteDutyUser   = '删除排班';


$lang->residentsupport->editSchedulingTip  = '填写本部门的驻场值班人员';
$lang->residentsupport->submitTip          = '提交本部门领导审批';
$lang->residentsupport->reviewTip          = '审批确认';
$lang->residentsupport->deleteDutyUserTip  = '删除本部门的排班';
$lang->residentsupport->result         = '确认结果';
$lang->residentsupport->mailto         = '抄送给';
$lang->residentsupport->currentComment = '当前备注';
$lang->residentsupport->comment        = '确认意见';
$lang->residentsupport->consumed       = '工作量（小时）';
$lang->residentsupport->logComment     = '操作备注';

$lang->residentsupport->dutyDate         = '值班日期';
$lang->residentsupport->dutyGroupLeader  = '值班组长';
$lang->residentsupport->dutyDept         = '值班部门';
$lang->residentsupport->postTypeInfo     = '值班岗位';
$lang->residentsupport->requireInfo      = '值班要求';
$lang->residentsupport->timeType         = '时长类型';
$lang->residentsupport->dutyTime         = '值班时长';
$lang->residentsupport->dutyUser         = '值班人员';
$lang->residentsupport->dutyUserTableDetail   = '值班表格明细';
$lang->residentsupport->enable           = '是否启用';
$lang->residentsupport->status           = '状态';
$lang->residentsupport->processStatus     = '流程状态';
$lang->residentsupport->reviewAdvice      = '部门审批结果';

$lang->residentsupport->reviewVersion     = '审批版本';
$lang->residentsupport->reviewNode        = '流程节点';
$lang->residentsupport->reviewPerson      = '处理人';
$lang->residentsupport->reviewResult      = '处理结果';
$lang->residentsupport->reviewOpinion     = '处理意见';
$lang->residentsupport->reviewDate        = '处理时间';

$lang->residentsupport->basicInfo     = '基本信息';
$lang->residentsupport->deptBasicInfo = '值班参与部门审批信息';
$lang->residentsupport->templateId    = '模板编号';
$lang->residentsupport->dutyDateTime  = '值班日期';
$lang->residentsupport->type          = '值班类型';
$lang->residentsupport->subType       = '值班子类';
$lang->residentsupport->name          = '模板名称';
$lang->residentsupport->deptId        = '值班部门';
$lang->residentsupport->startDate     = '值班开始';
$lang->residentsupport->endDate       = '值班结束';
$lang->residentsupport->dealUsers     = '待处理人员';
$lang->residentsupport->createdBy     = '由谁创建';
$lang->residentsupport->createdTime   = '创建时间';
$lang->residentsupport->editBy        = '由谁编辑';
$lang->residentsupport->editByTime    = '编辑时间';
$lang->residentsupport->logBook       = '值班日志';
$lang->residentsupport->dateType      = '日期类型';
$lang->residentsupport->showimport    = '导入排版模板确认';
$lang->residentsupport->fileType    = '文件类型';

$lang->residentsupport->enableList = array();
$lang->residentsupport->enableList['0']  = '否';
$lang->residentsupport->enableList['1'] = '是';
/**
 * 排班视图分类查看
 */
$lang->residentsupport->schedulingDeptLabelList = array();
$lang->residentsupport->schedulingDeptLabelList['selfDept'] = '本部门排班';
$lang->residentsupport->schedulingDeptLabelList['all']      = '所有排班';



//评审结论
$lang->residentsupport->reviewConclusionList = array();
$lang->residentsupport->reviewConclusionList['']   = '';
$lang->residentsupport->reviewConclusionList['pass']   = '通过';
$lang->residentsupport->reviewConclusionList['reject'] = '拒绝';

$lang->residentsupport->confirmDelete = '确认删除该条值班记录？';

$lang->residentsupport->labelList = array();
$lang->residentsupport->labelList['all'] = '所有';

$lang->residentsupport->importantTimeList[0] = '否';
$lang->residentsupport->importantTimeList[1] = '是';

$lang->residentsupport->mail                = new stdclass();
$lang->residentsupport->mail->create        = new stdclass();
$lang->residentsupport->mail->edit          = new stdclass();
$lang->residentsupport->mail->create->title = "%s提交了值班申请 #%s";
$lang->residentsupport->mail->edit->title   = "%s编辑了值班申请 #%s";

$lang->residentsupport->objectTypeList = [];
$lang->residentsupport->objectTypeList['resident_support_template']      = 'residentsupporttemplate'; //模板维度
$lang->residentsupport->objectTypeList['resident_support_template_dept'] = 'residentsupport'; //部门维度(部门维度操作)
$lang->residentsupport->objectTypeList['resident_support_day'] = 'residentsupportday'; //天维度

//公共验证验证
$lang->residentsupport->checkCommonResultList = [];
$lang->residentsupport->checkCommonResultList['pass']              = "验证成功";
$lang->residentsupport->checkCommonResultList['fail']              = '验证失败';
$lang->residentsupport->checkCommonResultList['typeEmpty']         = "支付类型不能为空";
$lang->residentsupport->checkCommonResultList['subTypeEmpty']      = "值班子类不能为空";
$lang->residentsupport->checkCommonResultList['templateIdEmpty']    = "请选择排班模板";
$lang->residentsupport->checkCommonResultList['startDateEmpty']    = "值班开始时间不能为空";
$lang->residentsupport->checkCommonResultList['startDateError']    = "值班开始时间须大于当天时间";
$lang->residentsupport->checkCommonResultList['endDateEmpty']      = "值班结束时间不能为空";
$lang->residentsupport->checkCommonResultList['endDateError']      = "值班结束时间不能小于开始时间";
$lang->residentsupport->checkCommonResultList['schedulingSearchEmpty'] = '没有搜索到符合排期条件的排期模板';
$lang->residentsupport->checkCommonResultList['schedulingSearchError'] = '搜索到符合排期条件的排期模板有『%s 』个，请进一步精确条件';
$lang->residentsupport->checkCommonResultList['dutyDeptCheckError'] = '有值班部门未审核结束或者未审核通过，不允许执行『%s 』操作';
//公共操作
$lang->residentsupport->opCommonResultList = [];
$lang->residentsupport->opCommonResultList['pass']              = "操作成功";
$lang->residentsupport->opCommonResultList['fail']              = '操作失败';
$lang->residentsupport->opCommonResultList['noUpdate']          = '没有任何修改，无需保存操作';

//提交申请验证
$lang->residentsupport->checkSubmitResultList = [];
$lang->residentsupport->checkSubmitResultList['pass']              = "允许提交审批";
$lang->residentsupport->checkSubmitResultList['fail']              = '验证提交失败';
$lang->residentsupport->checkSubmitResultList['statusError']       = '当前状态『%s 』不允许申请提交';
$lang->residentsupport->checkSubmitResultList['statusOrVersionError'] = '当前状态或者版本不允许申请提交，请刷新后操作';
$lang->residentsupport->checkSubmitResultList['userError']         = '当前用户不允许申请审批';
$lang->residentsupport->checkSubmitResultList['managerUsersEmpty'] = '部门负责人不能为空';
$lang->residentsupport->checkSubmitResultList['searchTimeEmpty'] = '搜索日期错误，模板日期信息不存在，请修改搜索日期信息';

//审核验证
$lang->residentsupport->checkReviewResultList = [];
$lang->residentsupport->checkReviewResultList['pass']              = "允许审批";
$lang->residentsupport->checkReviewResultList['fail']              = '审批失败';
$lang->residentsupport->checkReviewResultList['statusError']       = '当前状态『%s 』不允许审批';
$lang->residentsupport->checkReviewResultList['statusOrVersionError'] = '当前状态或者版本不允许审批，请刷新后操作';
$lang->residentsupport->checkReviewResultList['userError']         = '当前用户不允许审批';
$lang->residentsupport->checkReviewResultList['resultError']       = '审批结果不能为空';

//删除排班验证
$lang->residentsupport->checkDeleteResultList = [];
$lang->residentsupport->checkDeleteResultList['statusError']          = '当前状态『%s 』不允许删除排班';
$lang->residentsupport->checkDeleteResultList['statusOrVersionError'] = '当前状态或者版本不允许删除排班，请刷新后操作';
$lang->residentsupport->checkDeleteResultList['noDutyUserError']      = '该模板该部门下的排班信息已经全部删除，无需删除操作';


//启用排班验证
$lang->residentsupport->checkSchedulingResultList = [];
$lang->residentsupport->checkSchedulingResultList['dayEmpty']            = '该模板该段时间内没有排班信息';
$lang->residentsupport->checkSchedulingResultList['needSchedulingEmpty'] = '该段时间下都已经启用排班，不存在未启用';
$lang->residentsupport->checkSchedulingResultList['statusError']          = '当前状态『%s 』不允许编辑排班';
$lang->residentsupport->checkSchedulingResultList['noAllowError']         = '当前没有可提交的排班信息';
$lang->residentsupport->checkSchedulingResultList['userError']  = '该模板不包含用户所在部门的排班信息，无需排班';
$lang->residentsupport->checkSchedulingResultList['statusOrVersionError'] = '当前状态或者版本不允许编辑排班，请刷新后操作';
$lang->residentsupport->checkSchedulingResultList['noDutyUserError']      = '当前排班信息已经删除不允许编辑排班，请刷新后操作';
$lang->residentsupport->checkSchedulingResultList['dayDutyUserRepeatError']  = '『%s 』值班人员重复';
$lang->residentsupport->checkSchedulingResultList['dutyUserEmptyError']   = '第『%s 』行记录，值班人员为空';
$lang->residentsupport->checkSchedulingResultList['dutyUserRepeatError']  = '第『%s 』行记录,值班人员重复';
$lang->residentsupport->checkSchedulingResultList['dutyUserDetailEmptyError']  = '不存在排班信息';
$lang->residentsupport->checkSchedulingResultList['workExistError']    = '已经填写值班日志，不允许变更排班';
$lang->residentsupport->checkSchedulingResultList['deptStatusError']  = '该天值班部门信息都未审核通过，不允许变更排班';
$lang->residentsupport->checkSchedulingResultList['schedulingIntervalDayError']  = '非二线专员只允许变更『%s 』天以后的排班';

$lang->residentsupport->exportObj = new stdClass();
$lang->residentsupport->exportObj->num = "值班记录数";


$lang->residentsupport->importTitle = "导入";
$lang->residentsupport->importTxt = "请先导出原始模板，按照模板格式填写数据后再导入。";
$lang->residentsupport->importrosteringDataTxt = "请先导出排班模板，按照模板格式填写数据后再导入。";

$lang->residentsupport->choiceRostering = "排班模板";
$lang->residentsupport->cozyTips = "温馨提示：只排班本部门，如果值班日期不存在，请通知产创部初始化排班模板；当天的第一个值班人员默认为值班组长";
$lang->residentsupport->modifySchedulingTips = "温馨提示，如果参与该天排班的某部门没有审核完成，将不允许变更该部门的排班";

//扩展日志信息
$lang->residentsupport->actionExtra = [];
$lang->residentsupport->actionExtra['submit'] = '『%s 』申请审批';
$lang->residentsupport->actionExtra['review'] ='『%s 』审核%s';
$lang->residentsupport->actionExtra['deleteDutyUser'] ='删除『%s 』排班';
$lang->residentsupport->calendarimport = '日历视图导入';
$lang->residentsupport->calendarexport ='日历视图导出';


