<?php
$lang->release->path    = '发布地址';
$lang->release->mailto  = '通知人';
$lang->release->publish = '通知发布';

$lang->release->uploadedFiles  = '已上传附件';
$lang->release->sendSuccess    = '发送成功';

$lang->projectrelease->publish = '通知发布';
$lang->projectrelease->productCode = '产品编号';
$lang->projectrelease->repush = '重推介质';

$lang->build->desc = '制品名称';//20220130 新增
$lang->projectrelease->buildname = '制版名称';
$lang->projectrelease->dealUser = '待处理人';
$lang->projectrelease->statusDesc = '流程状态';

$lang->projectrelease->baseLineInfo = '基线地址';

$lang->projectrelease->baseLineTime = '打基线时间';
$lang->projectrelease->baseLineUser = '打基线人';
$lang->projectrelease->baseLineCondition = '是否已打基线';//是否设置基线
$lang->projectrelease->baseLinePath = '基线路径';
$lang->projectrelease->cmConfirm = '基线路径';
$lang->projectrelease->cmConfirm = 'CM确认结果';
$lang->projectrelease->cmConfirmUser = 'CM确认人';
$lang->projectrelease->cmConfirmTime = 'CM确认时间';


$lang->projectrelease->plateTip = '请输入产品编号,多个之间换行,如果无产品介质升级，则填写“无”';

$lang->projectrelease->product  = '产品名称';
$lang->projectrelease->app      = '应用系统';
$lang->projectrelease->productversion      = '产品版本';

$lang->projectrelease->tagPath   = '代码TAG路径';
$lang->projectrelease->cmConfirm = 'CM确认结果';
$lang->projectrelease->comment   = '本次操作备注';
$lang->projectrelease->tagPathTip = '例如：http://111.1.11.61/svn/TAG_IAMS-SERVER_V1.0.0.0_20221112';
$lang->projectrelease->commentTip = '当变更成功之后，研发人员将release分支合并至master，并基于master打tag，tag命名规范：TAG_${git库名}_Vx.x.x.x_${当前年月日}，比如：TAG_IAMS-SERVER_V1.0.0.0_20221112';
$lang->projectrelease->syncmodifycomment     = '项目发布关联的金信生产变更单%s流转为“%s”';
$lang->projectrelease->syncmodifycncccomment = '项目发布关联的清总生产变更单%s流转为“%s”';
$lang->projectrelease->syncputProductcomment = '项目发布关联的金信-投产移交单%s流转为“%s”';


/**
 * 发布状态列表
 */
$lang->projectrelease->statusList = [];
$lang->projectrelease->statusList['normal']          = 'normal'; //正常状态刚创建完成
$lang->projectrelease->statusList['waitBaseline']    = 'waitBaseline'; //待打基线
$lang->projectrelease->statusList['waitCmConfirm']   = 'waitCmConfirm'; //待CM确认
$lang->projectrelease->statusList['passBaseline']    = 'passBaseline'; //已打基线
$lang->projectrelease->statusList['passNoBaseline']  = 'passNoBaseline'; //无需基线
$lang->projectrelease->statusList['terminate']       = 'terminate'; //停止维护

/**
 * 发布状态名称列表
 */
//2023-04-13 待打基线=》待合并代码   待CM确认=》待打基线   名称变更 待处理人取值无变化
$lang->projectrelease->statusLabelList = [];
$lang->projectrelease->statusLabelList['normal']          = '正常'; //正常状态刚创建完成
//$lang->projectrelease->statusLabelList['waitBaseline']    = '待打基线'; //待打基线
$lang->projectrelease->statusLabelList['waitBaseline']    = '待合并代码'; //待合并代码
//$lang->projectrelease->statusLabelList['waitCmConfirm']   = '待CM确认'; //待CM确认
$lang->projectrelease->statusLabelList['waitCmConfirm']   = '待打基线'; //待打基线
$lang->projectrelease->statusLabelList['passBaseline']    = '已打基线'; //已打基线
$lang->projectrelease->statusLabelList['passNoBaseline']  = '无需基线'; //无需基线
$lang->projectrelease->statusLabelList['terminate']       = '停止维护'; //停止维护

$lang->projectrelease->dealResultList = [];
$lang->projectrelease->dealResultList['']       = '';
$lang->projectrelease->dealResultList['pass']   = '通过';
$lang->projectrelease->dealResultList['reject'] = '未通过';

/**
 * 是否打基线
 */
$lang->projectrelease->baseLineConditionList = [
    'yes' => '是',
    'no'  => '否',
];

/**
 *需要处理的状态
 */
$lang->projectrelease->allowDealStatusList = [
    $lang->projectrelease->statusList['waitBaseline'],
    $lang->projectrelease->statusList['waitCmConfirm'],
];

/**
 *需要增加审核节点的状态
 */
$lang->projectrelease->needAddReviewNodeStatusList = [
    $lang->projectrelease->statusList['waitBaseline'],
    $lang->projectrelease->statusList['waitCmConfirm'],
];

/**
 * 需要修改版本的状态
 */
$lang->projectrelease->needChangeVersionStatusList = [
    $lang->projectrelease->statusList['waitCmConfirm'],
    $lang->projectrelease->statusList['passBaseline'],
    $lang->projectrelease->statusList['passNoBaseline'],
];

/**
 *关键字类型
 */
$lang->projectrelease->objectType = 'release';
$lang->projectrelease->baseLineReviewerName = 'wangshanli';
$lang->projectrelease->baseLineCommentDesc = '产品变更成功打tag';
$lang->projectrelease->baseLineCmItemTitle = '产品变更成功';


/**
 * 审核节点标识
 */
$lang->projectrelease->nodeCodeList = [];
$lang->projectrelease->nodeCodeList['baseline']  = 'baseline'; //待打基线
$lang->projectrelease->nodeCodeList['cmConfirm'] = 'cmConfirm'; //待CM确认

/**
 * 不同操作对应的操作视图
 */
$lang->projectrelease->viewSuffixList = [
    $lang->projectrelease->statusList['waitBaseline']    => 'Baseline',
    $lang->projectrelease->statusList['waitCmConfirm']   => 'CmConfirm',
];
/**
 * 公共检查错误提示
 */
$lang->projectrelease->checkCommonResultList = [];
$lang->projectrelease->checkCommonResultList['versionOrStatusChange'] = '版本或状态已经发生变更刷新页面后操作';
$lang->projectrelease->checkCommonResultList['opError'] = '操作失败';

/**
 * 检查处理操作
 */
$lang->projectrelease->checkDealOpResultList = [];
$lang->projectrelease->checkDealOpResultList['statusError'] = '当前状态『%s 』不允许处理';
$lang->projectrelease->checkDealOpResultList['userError']   = '当前用户没权限处理';
$lang->projectrelease->checkDealOpResultList['tagPathBeginError'] = '代码TAG必须以“TAG开头”';
$lang->projectrelease->checkDealOpResultList['tagPathUrlError'] = '代码TAG地址格式错误';
$lang->projectrelease->checkDealOpResultList['tagPathExistError'] = '代码TAG地址有重复';
$lang->projectrelease->checkDealOpResultList['tagPathError'] = '基线地址必须以“http://”开头，以“/”分割，分割的最后一部分须以“TAG开头，以Vx.x.x.x_YYYYMMDD结尾”';
$lang->projectrelease->checkDealOpResultList['resultError']  = '处理结果不能为空';
$lang->projectrelease->checkDealOpResultList['nextStatusError']  = '获得处理后的状态失败';
$lang->projectrelease->alreadyMergeCode  = '已合并代码';
$lang->projectrelease->alreadyBaseLine  = '已打基线';