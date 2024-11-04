<?php
$lang->component->create = "发起申请";
$lang->component->componentType = "组件类型";
$lang->component->application = "申请方式";
$lang->component->type['thirdParty'] = "第三方组件";
$lang->component->type['public'] = "公共组件";
$lang->component->applicationMethod['new'] = "新引入";

/*
$lang->component->applicationMethod['upgrade'] = "升级";
$lang->component->applicationMethod['exit'] = "退出";
*/

$lang->component->name = '组件名称';
$lang->component->version = '组件版本';
$lang->component->developLanguage = '开发语言';
$lang->component->licenseType = '许可证类型';
$lang->component->artifactId = 'ArtifactId';
$lang->component->groupId = 'GroupId';
$lang->component->project = '关联项目';
$lang->component->applicationReason = '申请原因';
$lang->component->evidence = '评估举证';
$lang->component->submit = '提交评审';

$lang->component->maintainer = '联系人';
$lang->component->location = '获取位置';
$lang->component->functionDesc = '功能说明';
$lang->component->files = '文档上传';
$lang->component->hasProfessionalReview = '公共组件设计是否已经通过专业评审';
$lang->component->professionalReviewResult['1'] = "是";
$lang->component->professionalReviewResult['0'] = "否";

$lang->component->newThirdPartyName=$lang->component->name;
$lang->component->newPublicName=$lang->component->name;
$lang->component->newThirdPartyVersion=$lang->component->version;
$lang->component->newPublicVersion=$lang->component->version;
$lang->component->newThirdPartyDevelopLanguage=$lang->component->developLanguage;
$lang->component->newPublicDevelopLanguage=$lang->component->developLanguage;
$lang->component->newThirdPartyProjectId=$lang->component->project;
$lang->component->newPublicProjectId=$lang->component->project;

$lang->component->id = '编号';
$lang->component->level = '级别';
$lang->component->status = '当前状态';
$lang->component->cid = '组件id';
$lang->component->isattach = '是否纳入现有组件';
$lang->component->incorporate = '纳入现有组件';
$lang->component->reviewStage = '评审阶段';
$lang->component->dealUser = '待处理人';
$lang->component->createdBy = '创建人';
$lang->component->createdDept = '维护部门';
$lang->component->createdDate = '创建时间';
$lang->component->common          = '组件引入申请';
$lang->component->browse          = '组件引入申请列表';
$lang->component->edit          = '编辑申请';
$lang->component->reviewObject          = '评审对象';
$lang->component->maintainer          = '联系人';
$lang->component->location          = '获取位置';
$lang->component->relationgit          = '涉及git库';
$lang->component->functionDesc          = '功能说明';
$lang->component->fileTitle = '附件';
$lang->component->basicInfo = '基础信息';
$lang->component->reviewTime = '评审通过时间';
$lang->component->statusTransition       = '状态流转';
$lang->component->nodeUser               = '节点处理人';
$lang->component->before                 = '操作前';
$lang->component->after                  = '操作后';
$lang->component->time                  = '操作时间';
$lang->component->workhour               = '工作量';
$lang->component->view     = '查看组件引入申请';
$lang->component->consumed      = '工作量(小时)';
$lang->component->result  = '评审结果';
$lang->component->rejectReason  = '不通过原因';
$lang->component->mailto      = '抄送人';
$lang->component->dealcomment  = '本次操作备注';
$lang->component->review  = '评审申请';
$lang->component->teamMember  = '评审小组成员';
$lang->component->teamMemberCountError  = '评审小组成员数量应为奇数';
$lang->component->componetNOTEmpty  = '组件名称必选';
$lang->component->publish  = '发布组件';
$lang->component->publicpublish  = '公共组件-发布组件';
$lang->component->thirdpublish  = '第三方组件-发布组件';
$lang->component->mydeptisempty  = '您的部门为空，请联系管理员为您分配部门';
$lang->component->mydeptManageisempty  = '您的部门领导人(多人配置为空，请联系管理员配置)';
$lang->component->export = '导出';
$lang->component->exportExcel= '组件引入申请';
$lang->component->delete= '删除';
$lang->component->editstatus= '编辑状态';

$lang->component->deleteSuccess = '删除成功!';
$lang->component->deleteNotAllow = '该申请无法删除，请联系管理员!';
$lang->component->notDeleteAuth = '无权限删除!';
$lang->component->finalstatetime = '终态时间';

$lang->component->confirmDelete = '您确认要删除吗？';

$lang->component->levelList['company'] = '公司级';
$lang->component->levelList['dept'] = '部门级';

$lang->component->confirmList = array();
$lang->component->confirmList['pass']   = '通过';
$lang->component->confirmList['reject'] = '不通过';

$lang->component->publicconfirmList = array();
$lang->component->publicconfirmList['pass']   = '通过';
$lang->component->publicconfirmList['reject'] = '不通过';
$lang->component->publicconfirmList['incorporate'] = '纳入现有组件';

$lang->component->teamresultList = array();
$lang->component->teamresultList['pass'] = '通过';
$lang->component->teamresultList['reject'] = '不通过';
$lang->component->teamresultList['appoint'] = '指派评估人员';

$lang->component->teampublicresultList = array();
$lang->component->teampublicresultList['pass'] = '通过';
$lang->component->teampublicresultList['reject'] = '不通过';
$lang->component->teampublicresultList['appoint'] = '指派评估人员';
$lang->component->teampublicresultList['incorporate'] = '纳入现有组件';

$lang->component->publishType = "发布形式";
$lang->component->publishList = array();
$lang->component->publishList['create'] = '新建发布';
$lang->component->publishList['incorporate'] = '纳入现有组件';

$lang->component->reviewStageList[''] = '';


$lang->component->statusList[''] = '';
$lang->component->statusList['tosubmit']     = '待提交';
$lang->component->statusList['todepartreview']      = '待部门领导审';
$lang->component->statusList['toappoint']    = '待架构部处理';
$lang->component->statusList['toteamreview']        = '待评估小组审';
$lang->component->statusList['toarchitreview']        = '待架构部确认';
$lang->component->statusList['toarchitleaderreview']        = '待架构部领导确认';
$lang->component->statusList['topublish']        = '待发布';
$lang->component->statusList['published']        = '已发布';
$lang->component->statusList['reject']        = '不通过';
$lang->component->statusList['incorporate']        = '纳入现有组件';

$lang->component->statusKeyList[''] = '';
$lang->component->statusKeyList['tosubmit']     = 'tosubmit';
$lang->component->statusKeyList['todepartreview']      = 'todepartreview';
$lang->component->statusKeyList['toappoint']    = 'toappoint';
$lang->component->statusKeyList['toteamreview']        = 'toteamreview';
$lang->component->statusKeyList['toarchitreview']        = 'toarchitreview';
$lang->component->statusKeyList['toarchitleaderreview']        = 'topublish';
$lang->component->statusKeyList['topublish']        = 'topublish';
$lang->component->statusKeyList['published']        = 'published';
$lang->component->statusKeyList['reject']        = 'reject';
$lang->component->statusKeyList['incorporate']        = 'incorporate';

$lang->component->tosubmit     = 'tosubmit';
$lang->component->notAllowDeleteStatus     = ['toappoint','toteamreview','toarchitreview','toarchitleaderreview'];
$lang->component->notAllowDeleteStage     = [2,3,4,5];


$lang->component->labelList['all'] = '所有';
$lang->component->labelList['tosubmit'] = $lang->component->statusList['tosubmit'];
$lang->component->labelList['todepartreview'] = $lang->component->statusList['todepartreview'];
$lang->component->labelList['toappoint'] = $lang->component->statusList['toappoint'];
$lang->component->labelList['toteamreview'] = $lang->component->statusList['toteamreview'];
$lang->component->labelList['toarchitreview'] = $lang->component->statusList['toarchitreview'];
$lang->component->labelList['toarchitreview'] = $lang->component->statusList['toarchitreview'];
$lang->component->labelList['toarchitleaderreview'] = $lang->component->statusList['toarchitleaderreview'];
$lang->component->labelList['topublish'] = $lang->component->statusList['topublish'];
$lang->component->labelList['published'] = $lang->component->statusList['published'] ;
$lang->component->labelList['incorporate'] = $lang->component->statusList['incorporate'] ;

$lang->component->developLanguageList = array();
$lang->component->developLanguageList[''] = '';
$lang->component->locationTip="获取该组件的路径，例如：http://111.1.1.10/aadf/adsf/asdf.git 或者 svn路径";
$lang->component->filesTip="若申请组件级别为【公司级】，需要上传评审通过的'组件设计文档和使用文档'，每个附件大小不超过100M";
$lang->component->versionError = "组件版本不能为空，且不能包含中文";
$lang->component->licenseTypeError = "许可证类型不能为空，且不能包含中文，最大40个英文字符";
$lang->component->emptyObject            = '『%s 』不能为空。';
$lang->component->nameRepeatError="当前组件名称已存在，请先联系管理员";
$lang->component->statusError  = '当前状态未处于待提交';
$lang->component->comment      = '本次操作备注';
$lang->component->submitError = '提交评审失败，原因:『%s 』';
$lang->component->submitSuccess = '成功提交评审，已通知评审人，后续评审进度会邮件通知，请关注!';
$lang->component->chineseClassify = '中文分类';
$lang->component->englishClassify = '英文分类';
$lang->component->nowComponetNotExist = '现有组件不存在';

$lang->component->reviewNodeList = array();
$lang->component->reviewNodeList['1'] = '部门领导审';
$lang->component->reviewNodeList['2'] = '架构部处理';
$lang->component->reviewNodeList['3'] = '评估小组审';
$lang->component->reviewNodeList['4'] = '架构部确认';
$lang->component->reviewNodeList['5'] = '架构部领导确认';



$lang->component->reviewNodeStatusList = array();
$lang->component->reviewNodeStatusList['1'] = 'todepartreview';
$lang->component->reviewNodeStatusList['2'] = 'toappoint';
$lang->component->reviewNodeStatusList['3'] = 'toteamreview';
$lang->component->reviewNodeStatusList['4'] = 'toarchitreview';
$lang->component->reviewNodeStatusList['5'] = 'toarchitleaderreview';

//架构部处理人
$lang->component->productManagerReviewer = array();
$lang->component->productManagerReviewer[''] = '';

$lang->component->reviewOpinion='处理意见';
$lang->component->reviewNode='评审阶段';
$lang->component->reviewer='处理人';
$lang->component->reviewResult='处理结果';
$lang->component->reviewOpinion='处理意见';
$lang->component->reviewOpinionTime='处理时间';
$lang->component->confirmResultList = array();
$lang->component->confirmResultList['pass']                   = '通过';
$lang->component->confirmResultList['reject']                 = '不通过';
$lang->component->confirmResultList['pending']                = '审批中';
$lang->component->confirmResultList['ignore']                 = '跳过';
$lang->component->confirmResultList['wait']                   = '等待处理';
$lang->component->confirmResultList['appoint']              = '指派评估人员';
$lang->component->confirmResultList['confirming']              = '确认中';
$lang->component->confirmResultList['incorporate']              = '纳入现有组件';

//邮件相关
$lang->component->creater = '申请人';
$lang->component->createrTime = '申请时间';

$lang->component->changeteamreviewer='重新指派评估人员';

$lang->component->save = '确定';

$lang->component->category = '类别';
$lang->component->categoryList = array();

$lang->component->thirdcategoryList = array();
$lang->component->thirdStatusList = array();

$lang->component->publishStatus = '当前状态';
$lang->component->publishStatusList = array();

$lang->component->editcomment = '编辑处理意见';

$lang->component->carbonCopyList = array();


$lang->component->action = new stdclass();
$lang->component->action->incorporate    = array('main' => '$date, 由 <strong>$actor</strong> 纳入组件。');
$lang->component->action->dohistoryfinalstatetime     = array('main' => '$date, 由 <strong>$actor</strong> 更新'.$lang->component->finalstatetime.'。');