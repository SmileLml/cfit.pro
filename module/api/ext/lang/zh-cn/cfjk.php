<?php
$lang->api->Demand_number             = '需求单编号';
$lang->api->Demand_Department         = '需求提出部门';
$lang->api->Demand_contact            = '需求联系人';
$lang->api->Contact_telephone         = '联系人电话';
$lang->api->Proposed_date             = '提出日期';
$lang->api->Expected_realization_date = '需求方建议完成时间';
$lang->api->Demand_name               = '需求主题';
$lang->api->User_demand_background    = '需求描述';
$lang->api->Feasibility_study_report  = '可研报告';
$lang->api->Requirement_documents     = '需求书';
$lang->api->User_demand_backgrounds   = '需求背景';
$lang->api->Degree_of_urgency         = '需求紧急程度';
$lang->api->Demand_category           = '需求类别';

$lang->api->Demand_item_name             = '需求任务名称';
$lang->api->Demand_item_number           = '需求任务编号';
$lang->api->Demand_number                = '需求单编号';
$lang->api->Requirement_item_description = '需求任务描述';
$lang->api->Product                      = '所属产品编号';
$lang->api->Product_Line                 = '所属产品线编号';
$lang->api->ChangeOrder_Number           = '变更单编号';

$lang->api->Change_number                = '变更单唯一标识';
$lang->api->Demand_number                = '业务需求唯一标识';
$lang->api->Change_background            = '变更背景';
$lang->api->Change_content               = '变更内容';
$lang->api->General_manager              = '部门总经理';
$lang->api->Product_manager              = '业务需求产品经理';
$lang->api->Change_entry                 = '需求条目变更涉及条目';
$lang->api->Circumstance                 = '需求变更确认情况';
$lang->api->Missed_demolition            = '是否为漏拆产品需求';

$lang->api->Project_team              = '项目组';
$lang->api->Planned_completion_time   = '计划完成时间';
$lang->api->Responsible               = '责任人';
$lang->api->Attribution_item          = '归属项目';
$lang->api->Feedback_number           = '需求反馈单编号';
$lang->api->Contact_telephone         = '联系人电话';
$lang->api->Feedback_person           = '金科反馈人';
$lang->api->Feedback_date             = '反馈日期';
$lang->api->Requirement_item_number   = '需求任务编号';
$lang->api->Implementation_mode       = '实现方式';
$lang->api->Requirement_item_analysis = '需求任务分析';
$lang->api->Handling_suggestions      = '处理建议';
$lang->api->Implementation            = '实施情况';

$lang->api->audit_result  = '审核结果';
$lang->api->audit_opinion = '审核意见';
$lang->api->feedback_id   = '反馈单ID';

$lang->api->itemId   = '事项ID';
$lang->api->itemType = '事项类型';

$lang->api->successful             = '操作成功';
$lang->api->fieldMissing           = '字段%s不存在，请检查请求参数';
$lang->api->fieldEmpty             = '字段%s不能为空，请检查请求参数';
$lang->api->syncCreate             = '总中心接口同步创建';
$lang->api->syncUpdate             = '总中心接口同步更新';
$lang->api->jxsyncUpdate           = '金信接口同步更新';
$lang->api->syncSubdivide          = '总中心接口处理拆分';
$lang->api->opinionEmpty           = '使用需求单编号:%s未在系统中查询到已存在的数据';
$lang->api->feedbackEmpty          = '使用反馈单ID:%s未在系统中查询到已存在的数据';
$lang->api->requirementEmpty       = '使用需求任务编号:%s未在系统中查询到已存在的数据';
$lang->api->feedbackApproved       = '审核通过';
$lang->api->feedbackFailed         = '审核未通过';
$lang->api->illegalStatus          = '系统中的需求反馈单状态未处于【外部变更审核中】或【外部审核中】。';
$lang->api->changeFeedbackApproved = '变更审核通过';
$lang->api->changeFeedbackFailed   = '变更审核未通过';
//$lang->api->deleteOpinion          = '删除需求意向';
$lang->api->deleteOpinion          = '由清算总中心删除';
$lang->api->deleteRequirement      = '删除需求任务';
$lang->api->infoEmpty              = '使用数据单编号:%s未在系统中查询到已存在的数据';
$lang->api->noRequirementChange    = '未查询到该变更单,请先同步变更单信息';
$lang->api->notAllowField    = '不能携带字段:%s';
$lang->api->requirementChangeEmpty = '需求变更单号【ChangeOrder_Number】不能为空，请检查该字段';
$lang->api->rejectingShort         = '退回原因不能少于%s个字符。';
$lang->api->networkError           = '网络错误，请重试。';

$lang->api->problemNoUpdate        = '不允许更新问题，请按新问题提出，并关闭旧问题';

$lang->api->nameError              = '不是协议字段';
$lang->api->emptyError             = '不可以为空';

//清总同步需求意向需求种类字段映射
$lang->api->categoryTechnologyList = ['技术研发类','自主技术I类','自主技术II类','自主技术III类','自主技术Ⅰ类','自主技术Ⅱ类','自主技术Ⅲ类'];
$lang->api->categoryBusinessList   = ['自主业务I类','自主业务II类','自主业务III类','自主业务Ⅰ类','自主业务Ⅱ类','自主业务Ⅲ类'];

//问题反馈单状态错误提示
$lang->api->problem = new stdClass();
$lang->api->problem->closeStatusError      = '该问题单已经关闭，如需更新请按新问题提出。';
$lang->api->problem->jxFeedbackStatusError = '该问题金信反馈已通过，仅支持关闭。如需更新请关闭该问题后，按新问题提出。';
$lang->api->problem->qzFeedbackStatusError = '该问题清总反馈已通过，仅支持关闭。如需更新请关闭该问题后，按新问题提出。';

//删除需求条目时的状态判断 已交付、上线成功、变更单退回、变更单异常、已关闭、已挂起
$lang->api->demandStautsList = ['delivery','onlinesuccess','changeabnormal','chanereturn','closed','suspend'];