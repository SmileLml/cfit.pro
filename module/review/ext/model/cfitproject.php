<?php

/**
 * 检查项目审批是否允许申请审批
 *
 * @author wangjiurong
 * @param $reviewInfo
 * @param $userAccount
 * @return array|void
 */
public function checkReviewIsAllowApply($reviewInfo, $userAccount){
    return $this->loadExtension('cfitproject')->checkReviewIsAllowApply($reviewInfo, $userAccount);
}

/**
 * 检查项目审批是否允许审批
 * @param $reviewInfo
 * @param $userAccount
 * @return mixed
 */
public function checkReviewIsAllowReview($reviewInfo, $userAccount){
    return $this->loadExtension('cfitproject')->checkReviewIsAllowReview($reviewInfo, $userAccount);
}

/**
 * 获取全量项目
 * @return mixed
 */
public function getAllReviewList()
{
    return $this->loadExtension('cfitproject')->getAllReviewList();
}

/**
 * 获取差异日期
 * @param $begin
 * @param $end
 * @return mixed
 */
public function getDiffDate($begin,$end)
{
    return $this->loadExtension('cfitproject')->getDiffDate($begin,$end);
}

/**
 * 检查项目审批是否允许指派
 * @param $reviewInfo
 * @param $userAccount
 * @return mixed
 */
public function checkReviewIsAllowAssign($reviewInfo, $userAccount){
    return $this->loadExtension('cfitproject')->checkReviewIsAllowAssign($reviewInfo, $userAccount);
}
/**
 * 创建审批信息
 *
 * @author wangjiurong
 * @param $reviewInfo
 * @param $userAccount
 * @return array|void
 */
public function create($projectID = 0, $reviewRange = 'all', $checkedItem = ''){
    return $this->loadExtension('cfitproject')->create($projectID, $reviewRange, $checkedItem);
}

/**
 * 提交申请审批
 *
 * @param $reviewID
 * @return mixed
 */
public function submit($reviewID){
    return $this->loadExtension('cfitproject')->submit($reviewID);
}

/**
 *审核信息
 *
 * @param $reviewID
 * @return mixed
 */
public function review($reviewID){
    return $this->loadExtension('cfitproject')->review($reviewID);
}

/**
 *验证状态流转
 *
 * @param $reviewID
 * @return mixed
 */
public function reviewVerify($reviewID, $result, $user){
    return $this->loadExtension('cfitproject')->reviewVerify($reviewID, $result, $user);
}


/**
 *指派
 *
 * @param $reviewID
 * @return mixed
 */
public function assign($reviewID){
    return $this->loadExtension('cfitproject')->assign($reviewID);
}

/**
 *获得评审信息列表
 *
 * @param int $projectID
 * @param $browseType
 * @param int $queryID
 * @param $orderBy
 * @param null $pager
 * @return mixed
 */
public function getList($projectID = 0, $browseType, $queryID = 0, $orderBy, $pager = null){
    return $this->loadExtension('cfitproject')->getList($projectID, $browseType, $queryID, $orderBy, $pager);
}

/**
 *得评审信息列表
 *
 * @param $reviewID
 * @return mixed
 */
public function getByID($reviewID){
    return $this->loadExtension('cfitproject')->getByID($reviewID);
}



/**
 * 获得评审主表主要信息
 *
 * @param $reviewID
 * @return mixed
 */
public function getReviewMainInfoByID($reviewID){
    return $this->loadExtension('cfitproject')->getReviewMainInfoByID($reviewID);
}


/**
 * 评审信息展示
 *
 * @param $col
 * @param $review
 * @param $users
 * @param $products
 * @return mixed
 */
/*public function printCell($col, $review, $users, $products){
    return $this->loadExtension('cfitproject')->printCell($col, $review, $users, $products);
}*/

/**
 * 获得评审自定义列表
 *
 * @param $section
 * @params $isSpecial
 * @return array
 */
public function getReviewLangList($section, $isSpecial = false){
    return $this->loadExtension('cfitproject')->getReviewLangList($section, $isSpecial);
}

/**
 *获得项目审批的建议审批方式
 *
 * @param $type
 * @param $grade
 * @return mixed
 */
public function getReviewAdviceGrade($type, $grade){
    return $this->loadExtension('cfitproject')->getReviewAdviceGrade($type, $grade);
}

/**
 *获得项目审批的建议审批方式列表
 *
 * @param $adviceGrade
 * @return array
 */
public function getReviewAdviceGradeList($adviceGrade){
    return $this->loadExtension('cfitproject')->getReviewAdviceGradeList($adviceGrade);
}

/**
 *获得驳回节点
 *
 * @param $status
 * @return mixed
 */
public function getRejectStage($status){
    return $this->loadExtension('cfitproject')->getRejectStage($status);
}

/**
 *获得撤销后的驳回节点
 *
 * @param $status
 * @return mixed
 */
public function getRecallRejectStage($status){
    return $this->loadExtension('cfitproject')->getRecallRejectStage($status);
}

/**
 * 查询评审人 reviewer 表和 reviewnnode
 * @param $version
 * @param $subObjectType
 * @param $type
 * @param $stage
 */
public function getReviewerName($user,$version,$subObjectType,$type,$stage,$flag){
    return $this->loadExtension('cfitproject')->getReviewerName($user,$version,$subObjectType,$type,$stage,$flag);
}

/**
 * 发送邮件
 * @param $reviewID
 * @param $actionID
 * @return mixed
 */
public function sendmail($reviewID, $actionID,$isAutoSendMail=0,$dealUser='',$realReview1='',$realReview2='',$realReview3=''){
    return $this->loadExtension('cfitproject')->sendmail($reviewID, $actionID,$isAutoSendMail,$dealUser,$realReview1,$realReview2,$realReview3);
}
/**
 * 发送邮件
 * @param $reviewID
 * @param $actionID
 * @return mixed
 */
public function autosendmail(){
    return $this->loadExtension('cfitproject')->autosendmail();
}
/**
 * 自动处理评审
 * @param $reviewID
 * @param $actionID
 * @return mixed
 */
public function autodealreview(){
    return $this->loadExtension('cfitproject')->autodealreview();
}

/**
 *  关闭评审
 * @param $reviewID
 * @return mixed
 */
public function close($reviewID){
    return $this->loadExtension('cfitproject')->close($reviewID);
}

/**
 *  编辑评审
 * @param $reviewID
 * @return mixed
 */
public function update($reviewID){

    return $this->loadExtension('cfitproject')->update($reviewID);
}

/**
 * 获取问题列表
 * @param $projectID
 * @param $reviewID
 * @return mixed
 */
public function getReviewIssue($projectID,$reviewID){
    return $this->loadExtension('cfitproject')->getReviewIssue($projectID,$reviewID);
}

/**
 * 工作量编辑
 * @param $reviewID
 * @return mixed
 */
public function workloadEdit($reviewID , $consumedID){
    return $this->loadExtension('cfitproject')->workloadEdit($reviewID , $consumedID);
}

/**
 * 工作量详情
 * @param $reviewID
 * @return mixed
 */
public function workloadDetails($reviewID , $consumedID){
    return $this->loadExtension('cfitproject')->workloadDetails($reviewID , $consumedID);
}

/**
 * 工作量删除
 * @param $reviewID
 * @return mixed
 */
public function workloadDelete($reviewID , $consumedID ){
    return $this->loadExtension('cfitproject')->workloadDelete($reviewID , $consumedID);
}

/**
 * 获取工作量
 * @param $consumedID
 * @return mixed
 */
public function getConsumedByID( $consumedID ){
    return $this->loadExtension('cfitproject')->getConsumedByID($consumedID);
}

/**
 * 获得发送邮件和抄送人
 *
 * @param $reviewInfo
 * @return mixed
 */
public function getPendingToAndCcList($reviewInfo){
    return $this->loadExtension('cfitproject')->getPendingToAndCcList($reviewInfo);
}

/**
 * 获得编辑驳回的深情的下一个状态
 *
 * @param $rejectStage
 * @param $isFirstReview
 * @return mixed
 */
public function getEditRejectNextStatus($rejectStage, $isFirstReview){
    return $this->loadExtension('cfitproject')->getEditRejectNextStatus($rejectStage, $isFirstReview);
}

/**
 *获得提交评审后的下一状态
 *
 * @param $reviewInfo
 * @return mixed|string
 */
public function getSubmitNextStatus($reviewInfo){
    return $this->loadExtension('cfitproject')->getSubmitNextStatus($reviewInfo);
}

/**
 *获得审批对应的页面后缀
 *
 * @param $nextStatus
 * @return mixed
 */
public function getSubmitViewSuffix($nextStatus){
    return $this->loadExtension('cfitproject')->getSubmitViewSuffix($nextStatus);
}

public function getReviewAllGradeList($isSpecial = false){
    return $this->loadExtension('cfitproject')->getReviewAllGradeList($isSpecial);
}
/**
 * 关闭评审删除白名单
 * @param $objectType
 * @param $objectID
 * @param $reason
 */
public function deleteWhiteList($subOjectID,$reason){
    return $this->loadExtension('cfitproject')->deleteWhiteList($subOjectID,$reason);
}

/**
 * 获取整个评审参与人员
 * @param $objectType
 * @param $version
 * @return mixed
 */
public function getAllReview($objectType,$version,$objectID){
    return $this->loadExtension('cfitproject')->getAllReview($objectType,$version,$objectID);
}

/**
 * 获取评审状态
 * @param $status
 * @param $rejectStage
 * @return mixed
 */
public function getReviewStatusDesc($status, $rejectStage){
    return $this->loadExtension('cfitproject')->getReviewStatusDesc($status, $rejectStage);
}

/**
 * 获取初审部门 、接口人、参与人
 * @param $review
 * @param $users
 * @param string $flag
 * @return mixed
 */
public function getTrial($review, $version, $users,$flag = '', $param = ''){
    return $this->loadExtension('cfitproject')->getTrial($review, $version, $users, $flag, $param);
}

/**
 *检查当前审核节点是否需要修改资料
 *
 * @param $reviewId
 * @param $version
 * @param $nodeCode
 * @return false
 */
public function checkCurrentNodeIsEditInfo($reviewId, $version, $nodeCode = ''){
    return $this->loadExtension('cfitproject')->checkCurrentNodeIsEditInfo($reviewId, $version, $nodeCode);
}

/**
 * 检查当前审核节点是否需要修改资料按照部门分组
 *
 * @param $reviewId
 * @param $version
 * @param $nodeCode
 * @param $deptIds
 * @return array
 */
public function checkNodeIsEditInfoGroupByParentId($nodeId, $parentIds){
    return $this->loadExtension('cfitproject')->checkNodeIsEditInfoGroupByParentId($nodeId, $parentIds);
}
/**
 *  获取各部门负责人（除财务、人力、综合外）
 * @return mixed
 */
public function getAllManager1(){
    return $this->loadExtension('cfitproject')->getAllManager1();
}
/**
 *  获取各部门负责人（多人）（除财务、人力、综合外）
 * @return mixed
 */
public function getAllManager($deptId){
    return $this->loadExtension('cfitproject')->getAllManager($deptId);
}

/**
 *  获取评审未处理问题
 * @param $reviewId
 * @return mixed
 */
public function getNoDealIssue($reviewId){
    return $this->loadExtension('cfitproject')->getNoDealIssue($reviewId);
}

/**
 *变更审核节点用户信息
 *
 * @param $reviewID
 * @return mixed
 */
public function updateReviewNodeUsers($reviewID){
    return $this->loadExtension('cfitproject')->updateReviewNodeUsers($reviewID);
}
/**
 *修改截止日期
 *
 * @param $reviewID
 * @return mixed
 */
public function updateEndDate($reviewID){
    return $this->loadExtension('cfitproject')->updateEndDate($reviewID);
}

/**
 *修改单个评审自动处理
 *
 * @param $reviewID
 * @return mixed
 */
public function reviewDealSingle($reviewID){
    return $this->loadExtension('cfitproject')->reviewDealSingle($reviewID);
}

/**
 * 获得审核节点的最大版本
 *
 * @param $reviewID
 * @return mixed
 */
public function getReviewNodeMaxVersion($reviewID){
    return $this->loadExtension('cfitproject')->getReviewNodeMaxVersion($reviewID);
}

/**
 *获得所有审核节点的格式化审核人信息
 *
 * @param $reviewID
 * @return mixed
 */
public function getAllReviewNodeFormatReviewerList($reviewID,$maxVersion)
{
    return $this->loadExtension('cfitproject')->getAllReviewNodeFormatReviewerList($reviewID,$maxVersion);
}
/**
 *获得所有审核节点的格式化审核人信息
 *
 * @param $reviewID
 * @return mixed
 */
public function getNodeEndDate($objectType, $objectID, $version )
{
    return $this->loadExtension('cfitproject')->getNodeEndDate($objectType, $objectID, $version);
}
/**
 *获得所有版本的开始日期
 *
 * @param $reviewID
 * @return mixed
 */
public function getNodeStartDate($objectType, $objectID, $version )
{
    return $this->loadExtension('cfitproject')->getNodeStartDate($objectType, $objectID, $version);
}
/**
 *获得状态的截止日期
 *
 * @param $reviewID
 * @return mixed
 */
public function getEndDate($status, $begin, $reviewID)
{
    return $this->loadExtension('cfitproject')->getEndDate($status, $begin,$reviewID);
}

/**
 *获得审核节点的格式化审核人信息
 *
 * @param $reviewID
 * @return mixed
 */
public function getReviewNodeFormatReviewerList($reviewID)
{
    return $this->loadExtension('cfitproject')->getReviewNodeFormatReviewerList($reviewID);
}
/**
 *判断初审人员和主审人员是否一致
 *
 * @param $reviewID
 * @return mixed
 */
public function judgeSamePerson($reviewID)
{
    return $this->loadExtension('cfitproject')->judgeSamePerson($reviewID);
}

/**
 *编辑附件
 *
 * @param $reviewID
 * @return mixed
 */
public function editFilesByID($reviewID){
    return $this->loadExtension('cfitproject')->editFilesByID($reviewID);
}
/**
 *项目移动空间
 *
 * @param $reviewID
 * @return mixed
 */
public function projectSwap($reviewID){
    return $this->loadExtension('cfitproject')->projectSwap($reviewID);
}

/**
 * @param $meetingCode
 * @param $reviewId
 * @return mixed
 */
public function getMeetingDetailInfo($meetingCode, $reviewId){
    return $this->loadExtension('cfitproject')->getMeetingDetailInfo($meetingCode, $reviewId);
}

/**
 *获得默认的评审方式
 *
 * @param $reviewObjects
 * @param $planType
 * @param $planIsImportant
 * @return string
 */
public function getDefGrade($reviewObjects, $planType, $planIsImportant){
    return $this->loadExtension('cfitproject')->getDefGrade($reviewObjects, $planType, $planIsImportant);
}

/**
 * @param $reviewInfo
 * @return mixed
 */
public function getReviewVersion($reviewInfo){
    return $this->loadExtension('cfitproject')->getReviewVersion($reviewInfo);
}

/**
 *获得评审结果列表
 *
 * @param $reviewId
 * @param $version
 * @param $nodeCode
 * @param $statusArray
 * @param string $select
 * @param null $extra
 * @return mixed
 */
public function getReviewResultList($reviewId, $version, $nodeCode, $statusArray, $select = '*', $extra = null)
{
    return $this->loadExtension('cfitproject')->getReviewResultList($reviewId, $version, $nodeCode, $statusArray, $select, $extra);
}

/**
 * 添加评审节点
 *
 * @param $reviewID
 * @param $objectType
 * @param $version
 * @param $reviewNodes
 * @param array $extParams
 * @return mixed
 */
public function submitReview($reviewID, $objectType, $version, $reviewNodes, $extParams = []){
    return $this->loadExtension('cfitproject')->submitReview($reviewID, $objectType, $version, $reviewNodes, $extParams);
}

/**
 * 添加会议评审结论
 *
 * @param $params
 * @return mixed
 */
public function addReviewMeetingResultInfo($params){
    return $this->loadExtension('cfitproject')->addReviewMeetingResultInfo($params);
}

/**
 * 获得允许绑定的会议列表
 *
 * @param $type
 * @return mixed
 */
public function getAllowBindReviewListByType($type){
    return $this->loadExtension('cfitproject')->getAllowBindReviewListByType($type);
}

/**
 *获得审核验证人用户
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @return mixed
 */
public function getReviewVerifyUsers($reviewId, $version){
    return $this->loadExtension('cfitproject')->getReviewVerifyUsers($reviewId, $version);
}

public function getReviewVerifyPendingUsers($reviewId, $version){
    return $this->loadExtension('cfitproject')->getReviewVerifyPendingUsers($reviewId, $version);
}

/**
 *挂起项目评审
 *
 * @param $reviewID
 * @return mixed
 */
public function suspend($reviewID){
    return $this->loadExtension('cfitproject')->suspend($reviewID);
}

/**
 *恢复项目评审
 *
 * @param $reviewID
 * @return mixed
 */
public function renew($reviewID){
    return $this->loadExtension('cfitproject')->renew($reviewID);
}

/**
 * 设置审核人通过
 *
 * @param $reviewID
 * @return mixed
 */
public function setReviewVerifyUsersPass($reviewID){
    return $this->loadExtension('cfitproject')->setReviewVerifyUsersPass($reviewID);
}

/**
 *获得评审阶段
 *
 * @param string $beforeStatus
 * @param string $afterStatus
 * @return mixed
 */
public function getReviewStage($beforeStatus = '', $afterStatus = ''){
    return $this->loadExtension('cfitproject')->getReviewStage($beforeStatus, $afterStatus);
}

// 修改评审类型和评审会主席
public function updateReviewInfos($reviewID){
    return $this->loadExtension('cfitproject')->updateReviewInfos($reviewID);
}

// 修改评审节点处理信息
public function updateReviewNodeInfos($reviewID, $nodeID){
    return $this->loadExtension('cfitproject')->updateReviewNodeInfos($reviewID, $nodeID);
}

// 修改评审节点处理信息
public function getNextDealUser($reviewInfo, $nextStatus, $postUser = ''){
    return $this->loadExtension('cfitproject')->getNextDealUser($reviewInfo, $nextStatus, $postUser = '');
}

// 评审自动关闭
public function autoclose($reviewId){
    return $this->loadExtension('cfitproject')->autoclose($reviewId);
}

// 喧喧消息
public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
    return $this->loadExtension('cfitproject')->getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '');
}

/**
 * 获得关闭时的邮件账户信息
 *
 * @param $review
 * @return array
 */
public function getCloseMailUsersInfo($review){
    return $this->loadExtension('cfitproject')->getCloseMailUsersInfo($review);
}

public function getReviewNodeCodeByStatus($status, $oldStatus){
    return $this->loadExtension('cfitproject')->getReviewNodeCodeByStatus($status, $oldStatus);
}

/**
 * 通过$nodeCode获取对应状态
 *
 * @param $nodeCode
 * @return mixed
 */
public function getStatusByNodeCode($nodeCode){
    return $this->loadExtension('cfitproject')->getStatusByNodeCode($nodeCode);
}

/**
 * 通过$nodeCode获取对应状态
 *
 * @param $nodeCode
 * @return mixed
 */
public function getReNewNextDealUser($reviewInfo, $nextStatus, $nodeCode){
    return $this->loadExtension('cfitproject')->getReNewNextDealUser($reviewInfo, $nextStatus, $nodeCode);
}

/**
 * 修改评审用户信息
 *
 * @param $reviewId
 * @param $field
 * @return mixed
 */
public function editUsersByField($reviewId, $field){
    return $this->loadExtension('cfitproject')->editUsersByField($reviewId, $field);
}

/**
 * 设置验证结果
 *
 * @param $reviewId
 * @return mixed
 */
public function setVerifyResult($reviewId){
    return $this->loadExtension('cfitproject')->setVerifyResult($reviewId);
}

/**
 * 设置验证结果
 *
 * @param $reviewId
 * @return mixed
 */
public function sendUnDealIssueUsersMail($reviewId){
    return $this->loadExtension('cfitproject')->sendUnDealIssueUsersMail($reviewId);
}



