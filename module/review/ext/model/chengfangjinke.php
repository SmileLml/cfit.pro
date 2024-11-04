<?php
/**
 * Project: chengfangjinke 一次性增加一个审核节点
 * Method: addNode
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:25
 * Desc: This is the code comment. This method is called addNode.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $reviewers
 * @param false $withGrade
 * @param string $status
 * @param int $stage
 * @param array $extParams
 * @return mixed
 */
public function addNode($objectType, $objectID, $version, $reviewers, $withGrade = false, $status = 'wait', $stage = 1, $extParams = [])
{
    return $this->loadExtension('chengfangjinke')->addNode($objectType, $objectID, $version, $reviewers, $withGrade, $status, $stage, $extParams);
}

/**
 * 一次性增加多个审核节点（一个或者多个）
 *
 * @param $objectType  对象类型
 * @param $objectID 对象ID
 * @param $version 版本
 *  @param $reviewNodes 审核节点信息 $reviewNodes =
 *    array(
            array( //参与人员
                'reviewers' => $joinReviewers,        //必填 多个人用数组，单个人可以数组也可以字符串
                'status'    => $nodeStatus,           //非必填
                'stage'     => $firstIncludeStage,    //非必填
                'nodeCode'  => $firstIncludeNodeCode,  //非必填
            ),
                array(//主审人员
                'reviewers' => $mainReviewers,
                'status'    => $nodeStatus,
                'stage'     => $firstMainStage,
                'nodeCode'  => $firstMainNodeCode,
            )
        );
 * @param array $extParams 扩展信息
 * @return bool
 */
public function addReviewNodes($objectType, $objectID, $version, $reviewNodes){
    return $this->loadExtension('chengfangjinke')->addReviewNodes($objectType, $objectID, $version, $reviewNodes);
}

/**
 *获得审核节点
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param array $extParams
 * @return mixed
 */
public function getReviewerNodeIds($objectType, $objectID, $version, $extParams = []){
    return $this->loadExtension('chengfangjinke')->getReviewerNodeIds($objectType, $objectID, $version, $extParams);
}

/**
 * Project: chengfangjinke
 * Method: check
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:25
 * Desc: This is the code comment. This method is called check.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $result
 * @param $comment
 * @param int $grade
 * @param null $extra
 * @param $is_all_check_pass  //该节点是否需要全部人员审核通过才算审核通过
 * @return mixed
 */
public function check($objectType, $objectID, $version, $result, $comment, $grade = 0, $extra = null, $is_all_check_pass = true,$nodeid = 0)
{
    return $this->loadExtension('chengfangjinke')->check($objectType, $objectID, $version, $result, $comment, $grade, $extra, $is_all_check_pass,$nodeid);
}
public function autoDealCheck($objectType, $objectID, $version, $result, $comment, $grade = 0, $extra = null,$dealUser, $is_all_check_pass = true)
{
    return $this->loadExtension('chengfangjinke')->autoDealCheck($objectType, $objectID, $version, $result, $comment, $grade, $extra,$dealUser, $is_all_check_pass);
}
public function checkVerify($objectID, $version, $result, $user)
{
    return $this->loadExtension('chengfangjinke')->checkVerify($objectID, $version, $result, $user);
}

/**
 * Project: chengfangjinke
 * Method: getNodes
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:25
 * Desc: This is the code comment. This method is called getNodes.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @return mixed
 */
public function getNodes($objectType, $objectID, $version = 1)
{
    return $this->loadExtension('chengfangjinke')->getNodes($objectType, $objectID, $version);
}
/**
 * 获取历史所有审批记录（包含本次）
 * @param
 * $objectType
 * @param $objectID
 * @return mixed
 */
public function getAllNodes($objectType, $objectID)
{
    return $this->loadExtension('chengfangjinke')->getAllNodes($objectType, $objectID);
}
/**
 * 获得审核节点以NodeCode分组
 * @param $objectType
 * @param $objectID
 * @param $version
 * @return array
 */
public function getNodesGroupByNodeCode($objectType, $objectID, $version = 1)
{
    return $this->loadExtension('chengfangjinke')->getNodesGroupByNodeCode($objectType, $objectID, $version);
}

public function getChangeNodes($objectType, $objectID, $version = 1){
     return $this->loadExtension('chengfangjinke')->getChangeNodes($objectType, $objectID, $version);
}
/**
 * Project: chengfangjinke
 * Method: getReviewer
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:25
 * Desc: This is the code comment. This method is called getReviewer.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param int $grade
 * @return mixed
 */
public function getReviewer($objectType, $objectID, $version, $grade = 0)
{
    return $this->loadExtension('chengfangjinke')->getReviewer($objectType, $objectID, $version, $grade);
}
public function getMuiltNodeReviewer($objectType, $objectID, $version, $stage = [],$status='pending', $extra = null)
{
    return $this->loadExtension('chengfangjinke')->getMuiltNodeReviewer($objectType, $objectID, $version, $stage,$status, $extra);
}
public function getMuiltNodeReviewers($objectType, $objectID, $version, $stage = [],$status='pending', $extra = null)
{
    return $this->loadExtension('chengfangjinke')->getMuiltNodeReviewers($objectType, $objectID, $version, $stage,$status, $extra);
}
public function getReviewByAccount($objectType, $objectID,$account, $version = 1,$status='pending',$extra=null)
{
    return $this->loadExtension('chengfangjinke')->getReviewByAccount($objectType, $objectID,$account, $version ,$status,$extra);
}
public function getAppointUsers($nodeID)
{
    return $this->loadExtension('chengfangjinke')->getAppointUsers($nodeID);
}
/**
 * Project: chengfangjinke
 * Method: getLastPendingPeople
 * User: t_tangfei
 * Year: 2022
 * Date: 2022/1/13
 * Time: 16:25
 * Desc: This is the code comment. This method is called getLastPendingPeople.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param int $stage
 * @param $subObjectType
 * @return mixed
 */
 public function getLastPendingPeople($objectType, $objectID, $version, $stage, $subObjectType = '')
 {
     return $this->loadExtension('chengfangjinke')->getLastPendingPeople($objectType, $objectID, $version, $stage, $subObjectType);
 }


/**
 * Project: chengfangjinke
 * Method: getLastPendingPeople
 * User: t_tangfei
 * Year: 2022
 * Date: 2022/05/08
 * Time: 16:25
 * Desc: 获得审核节点的所有人员
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param int $stage
 * @param $subObjectType
 * @return mixed
 */
public function getStageReviews($objectType, $objectID, $version, $stage, $subObjectType = '', $returnListFlag = false)
{
    return $this->loadExtension('chengfangjinke')->getStageReviews($objectType, $objectID, $version, $stage, $subObjectType, $returnListFlag);
}

/**
 * Project: chengfangjinke
 * Method: setNodePending
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:25
 * Desc: This is the code comment. This method is called setNodePending.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @return mixed
 */
public function setNodePending($objectType, $objectID, $version)
{
    return $this->loadExtension('chengfangjinke')->setNodePending($objectType, $objectID, $version);
}

public function getObjectIdListReviewer($objectIdList = array(), $objectType = '')
{
    $reviewerList = $this->dao->select('t1.objectID,t2.reviewer')->from(TABLE_REVIEWNODE)->alias('t1')
        ->leftjoin(TABLE_REVIEWER)->alias('t2')->on('t1.id=t2.node')
        ->where('t1.status')->eq('pending')
        ->andWhere('t1.objectType')->eq($objectType)
        ->andWhere('t1.objectID')->in($objectIdList)
        ->andWhere('t2.status')->eq('pending')
        ->fetchAll();

    $userGroupModify = array();
    foreach($reviewerList as $reviewer)
    {
        if(empty($reviewer->reviewer)) continue;
        $userGroupModify[$reviewer->objectID][] = $reviewer->reviewer;
    }
    return $userGroupModify;
}


/**
 * Project: chengfangjinke
 * Method: getAllNodeReviewers
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/02/15
 * Time: 14:25
 * Desc: This is the code comment. This method is called getAllNodeReviewers.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @return mixed
 */
public function getAllNodeReviewers($objectType, $objectID, $version = 1)
{
    return $this->loadExtension('chengfangjinke')->getAllNodeReviewers($objectType, $objectID, $version);
}
public function getChangeAllNodeReviewers($objectType, $objectID, $version = 1)
{
    return $this->loadExtension('chengfangjinke')->getChangeAllNodeReviewers($objectType, $objectID, $version);
}

/**
 * Project: chengfangjinke
 * Method: addNodeReviewers
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/02/15
 * Time: 14:25
 * Desc: This is the code comment. This method is called addNodeReviewers.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $nodeID
 * @param $reviewers
 * @param bool $withGrade
 * @param string $status
 * @param $reviewerExtParams
 * @return mixed
 */
public function addNodeReviewers($nodeID, $reviewers, $withGrade = false, $status = 'wait', $reviewerExtParams = [])
{
    return $this->loadExtension('chengfangjinke')->addNodeReviewers($nodeID, $reviewers, $withGrade, $status, $reviewerExtParams);
}

/**
 * Project: chengfangjinke
 * Method: delReviewers
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/02/15
 * Time: 16:25
 * Desc: This is the code comment. This method is called delReviewers.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $nodeID
 * @param $reviewerIds
 * @return mixed
 */
public function delReviewers($reviewerIds){
    return $this->loadExtension('chengfangjinke')->delReviewers($reviewerIds);
}

/**
 * Project: chengfangjinke
 * Method: getReviewerAccounts
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/02/15
 * Time: 9:43
 * Desc: get reviewer user accounts
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $reviewers
 * @return array
 */

public function getReviewerAccounts($reviewers){
    return $this->loadExtension('chengfangjinke')->getReviewerAccounts($reviewers);
}

/**
 * Project: chengfangjinke
 * Method: getRealReviewerInfo
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/02/15
 * Time: 9:43
 * Desc: 获得真实审核的用户
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $nodeStatus
 * @param $nodeReviewers
 * @param $ignoreComment
 * @return mixed
 */
public function getRealReviewerInfo($nodeStatus, $nodeReviewers = [], $ignoreComment = ''){
    return $this->loadExtension('chengfangjinke')->getRealReviewerInfo($nodeStatus, $nodeReviewers, $ignoreComment);
}

/**
 * Project: chengfangjinke
 * Method: getRealReviewerInfo
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/02/23
 * Time: 9:43
 * Desc: 获得某节点的审核用户信息
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $nodeStatus
 * @param $nodeReviewers
 * @return mixed
 */
public function getReviewedUserInfo($objectType, $objectID, $version = 1, $reviewStage = 0){
    return $this->loadExtension('chengfangjinke')->getReviewedUserInfo($objectType, $objectID, $version, $reviewStage);
}



/**
 *getReviewedUserByNodeCode
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param string $nodeCode
 * @return mixed
 */
public function getReviewedUserByNodeCode($objectType, $objectID, $version, $nodeCode = ''){
    return $this->loadExtension('chengfangjinke')->getReviewedUserByNodeCode($objectType, $objectID, $version, $nodeCode);
}

/**
 * Project: chengfangjinke
 * Method: getChangeReviewers
 * User: wangjiurong
 * Year: 2021
 * Date: 2021/10/8
 * Time: 17:25
 * Desc: This is the code comment. This method is called getNodes.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $newReviewers
 * @param int $oldVersion
 *
 * @return mixed
 */
public function getChangeReviewers($objectType, $objectID, $newReviewers, $oldVersion = 1)
{
    return $this->loadExtension('chengfangjinke')->getChangeReviewers($objectType, $objectID, $newReviewers, $oldVersion);
}

/**
 *  更新方法，新增字段
 * @param $col
 * @param $review
 * @param $users
 * @param $products
 */
public function printCell($col, $review, $users, $products){
    return $this->loadExtension('chengfangjinke')->printCell($col, $review, $users, $products);
}
/**
 * 获得历史审核节点
 *
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @param int $maxHistoryStage
 * @return array
 */
public function getHistoryReviewers($objectType, $objectID, $version = 1, $maxHistoryStage = 0){
    return $this->loadExtension('chengfangjinke')->getHistoryReviewers($objectType, $objectID, $version, $maxHistoryStage);
}
/**
 * 获得未审核节点
 *
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @param int $minHistoryStage
 * @return array
 */
public function geWaitReviewers($objectType, $objectID, $version = 1, $minHistoryStage = 0){
    return $this->loadExtension('chengfangjinke')->getWaitReviewers($objectType, $objectID, $version, $minHistoryStage);
}

/**
 *获得节点的排序
 *
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @param string $nodeCode
 * @return mixed
 */
public function getNodeStage($objectType, $objectID, $version = 1, $nodeCode = ''){
    return $this->loadExtension('chengfangjinke')->getNodeStage($objectType, $objectID, $version, $nodeCode);
}
/**
 *设置审核节点忽略
 *
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @return mixed
 */
public function setReviewNodesIgnore($objectType, $objectID, $version = 1){
    return $this->loadExtension('chengfangjinke')->setReviewNodesIgnore($objectType, $objectID, $version);
}

/**
 *获得实际的审核人
 *
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @param $stageIds
 * @return mixed
 */
public function getRealReviewers($objectType, $objectID, $version = 1, $stageIds = []){
    return $this->loadExtension('chengfangjinke')->getRealReviewers($objectType, $objectID, $version, $stageIds);
}

/**
 * 获取阶段
 * @param $reviewID
 * @param $version
 * @return mixed
 */
public function getStage($reviewID,$version){
    return $this->loadExtension('chengfangjinke')->getStage($reviewID,$version);
}

/**
 *获得允许编辑的审核节点
 *
 * @author wangjiurong
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @return mixed
 */
public function getAllowEditNodes($objectType, $objectID, $version = 1){
    return $this->loadExtension('chengfangjinke')->getAllowEditNodes($objectType, $objectID, $version);
}
/**
 *获得未处理的审核节点
 *
 * @author wangjiurong
 * @param $objectType
 * @param $objectID
 * @param int $version
 * @return mixed
 */
public function getUnDealReviewNodes($objectType, $objectID, $version = 1){
    return $this->loadExtension('chengfangjinke')->getUnDealReviewNodes($objectType, $objectID, $version);
}


/**
 *获得审核节点
 *
 * @param $nodeId
 * @return mixed
 */
public function getReviewNodeById($nodeId){
    return $this->loadExtension('chengfangjinke')->getReviewNodeById($nodeId);
}

/**
 *获得某节点未操作的用户信息
 *
 * @param $nodeId
 * @return array
 */
public function getUnActionReviewersByNodeId($nodeId){
    return $this->loadExtension('chengfangjinke')->getUnActionReviewersByNodeId($nodeId);
}

/**
 *获得某节点已经操作的用户信息
 *
 * @param $nodeId
 * @return array
 */
public function getReviewedReviewersByNodeId($nodeId){
    return $this->loadExtension('chengfangjinke')->getReviewedReviewersByNodeId($nodeId);
}

/**
 *获得审核节点最大排序
 *
 * @param $objectID
 * @param $objectType
 * @param int $version
 * @return mixed
 */
public function getReviewMaxStage($objectID, $objectType, $version = 0){
    return $this->loadExtension('chengfangjinke')->getReviewMaxStage($objectID, $objectType, $version);
}

/**
 *获得审核信息节点的默认排序
 *
 * @param $objectID
 * @param $objectType
 * @param int $version
 * @return mixed
 */
public function getReviewDefaultStage($objectID, $objectType, $version = 0){
    return $this->loadExtension('chengfangjinke')->getReviewDefaultStage($objectID, $objectType, $version);
}

/**
 * 获得当前版本信息最后节点评审状态
 *
 * @param $objectID
 * @param $objectType
 * @param $version
 * @return mixed
 */
public function getReviewLastStatus($objectID, $objectType, $version = 0){
    return $this->loadExtension('chengfangjinke')->getReviewLastStatus($objectID, $objectType, $version);
}

public function getReviewNodeDefaultStatus($objectID, $objectType, $version = 0){
    return $this->loadExtension('chengfangjinke')->getReviewNodeDefaultStatus($objectID, $objectType, $version);
}


/**
 * Project: chengfangjinke
 * Method: getReviewersByNodeCode
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/06/18
 * Time: 16:25
 * Desc: This is the code comment. This method is called getReviewersByNodeCode.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @param $returnType
 * @return mixed
 */
public function getReviewersByNodeCode($objectType, $objectID, $version, $nodeCode, $returnType = 'string'){
    return $this->loadExtension('chengfangjinke')->getReviewersByNodeCode($objectType, $objectID, $version, $nodeCode, $returnType);
}

public function getReviewersAndCommentByNodeCode($objectType, $objectID, $version, $nodeCode){
    return $this->loadExtension('chengfangjinke')->getReviewersAndCommentByNodeCode($objectType, $objectID, $version, $nodeCode);
}

/**
* Project: chengfangjinke
* Method: getReviewersByNodeCodeGroupGrade
* User: wangjiurong
* Year: 2022
* Date: 2022/06/18
* Time: 16:25
* Desc: This is the code comment. This method is called getReviewersByNodeCodeGroupGrade.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
* @param $objectType
* @param $objectID
* @param $version
* @param $nodeCode
* @return mixed
    */
public function getReviewersByNodeCodeGroupGrade($objectType, $objectID, $version, $nodeCode){
    return $this->loadExtension('chengfangjinke')->getReviewersByNodeCodeGroupGrade($objectType, $objectID, $version, $nodeCode);
}

/**
 * 获得审核人信息按照审核标识分组展示
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCodes
 * @return mixed
 */
public function getReviewersListByNodeCodes($objectType, $objectID, $version, $nodeCodes){
    return $this->loadExtension('chengfangjinke')->getReviewersListByNodeCodes($objectType, $objectID, $version, $nodeCodes);
}


/**
 *获得发送邮件的抄送人
 *
 * @param $objectID
 * @param $objectType
 * @param string $before
 * @param int $version
 * @return mixed
 */
public function getSendMailCcList($objectID, $objectType, $before = '', $version = 0){
    return $this->loadExtension('chengfangjinke')->getSendMailCcList($objectID, $objectType, $before, $version);
}
/**
 *获得审核节点
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @return mixed
 */
public function getReviewNodeId($objectType, $objectID, $version, $nodeCode){
    return $this->loadExtension('chengfangjinke')->getReviewNodeId($objectType, $objectID, $version, $nodeCode);
}

/**
 *获得审核节点的最大版本
 *
 * @param $objectID
 * @param $objectType
 * @return int|void
 */
public function getObjectReviewNodeMaxVersion($objectID, $objectType){
    return $this->loadExtension('chengfangjinke')->getObjectReviewNodeMaxVersion($objectID, $objectType);
}

/**
 * 忽略审核节点和审核人
 *
 * @param $nodeIds
 * @return mixed
 */
public function ignoreReviewNodeAndReviewers($nodeIds){
    return $this->loadExtension('chengfangjinke')->ignoreReviewNodeAndReviewers($nodeIds);
}

/**
 * 获得审核的版本列表
 *
 * @param $objectType
 * @param $objectIds
 * @return array
 */
public function getReviewVersionList($objectType, $objectIds){
    return $this->loadExtension('chengfangjinke')->getReviewVersionList($objectType, $objectIds);
}

/**
 * 获得审核的版本列表
 *
 * @param $objectIds
 * @return array
 */
public function getReviewRejectNodes($objectIds){
    return $this->loadExtension('chengfangjinke')->getReviewRejectNodes($objectIds);
}

/**
 *获得未审核的节点列表
 * @param $objectType
 * @param $objectId
 * @param int $version
 * @param string $select
 * @param int $count
 * @return mixed
 */
public function getUnReviewNodeList($objectType, $objectId, $version = 0, $select = '*', $count = 1){
    return $this->loadExtension('chengfangjinke')->getUnReviewNodeList($objectType, $objectId, $version, $select, $count);
}


/**
 * Project: chengfangjinke
 * Method: getPendingReviewNodeInfo
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/06/18
 * Time: 16:25
 * Desc: This is the code comment. This method is called getReviewersByNodeCode.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @return mixed
 */
public function getPendingReviewNode($objectType, $objectID, $version, $nodeCode){
    return $this->loadExtension('chengfangjinke')->getPendingReviewNode($objectType, $objectID, $version, $nodeCode);
}

/**
 * 获得审核节点的审核结果
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @return mixed
 */
public function getReviewNodeReviewAction($nodeId, $reviewAction = 'pass'){
    return $this->loadExtension('chengfangjinke')->getReviewNodeReviewAction($nodeId, $reviewAction);
}

/**
 * 更新节点审核人
 *
 * @param $nodeId
 * @return mixed
 */
public function updateReviewersByNodeId($nodeId, $reviewers = []){
    return $this->loadExtension('chengfangjinke')->updateReviewersByNodeId($nodeId, $reviewers);
}


/**
 * Project: chengfangjinke
 * Method: getReviewerListByNodeCode
 * User: wangjiurong
 * Year: 2022
 * Date: 2022/06/18
 * Time: 16:25
 * Desc: This is the code comment. This method is called getReviewersByNodeCode.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @param $statusArray
 * @param $exWhere
 * @return mixed
 */
public function getReviewerListByNodeCode($objectType, $objectID, $version, $nodeCode, $statusArray = [], $exWhere = ''){
    return $this->loadExtension('chengfangjinke')->getReviewerListByNodeCode($objectType, $objectID, $version, $nodeCode, $statusArray, $exWhere);
}

/**
 * 获得指定节点
 *
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $nodeCode
 * @param $statusArray
 * @return mixed
 */
public function getNodeByNodeCode($objectType, $objectID, $version, $nodeCode, $statusArray = []){
    return $this->loadExtension('chengfangjinke')->getNodeByNodeCode($objectType, $objectID, $version, $nodeCode, $statusArray);
}




/**
 * @Notes: 根据某一个节点获取相应节点数据
 * @Date: 2023/3/20
 * @Time: 15:14
 * @param $objectType
 * @param $objectId
 * @param $version
 * @param $stage
 * @param string $field
 * @param string $orderBy
 */
public function getReviewInfoByStage($objectType,$objectId,$version,$stage,$field='*',$orderBy='id_desc'){
    return $this->loadExtension('chengfangjinke')->getReviewInfoByStage($objectType,$objectId,$version,$stage,$field,$orderBy);
}

/**
 * 获取审批数据，根据stage作为key值返回
 * @param $objectType
 * @param $objectID
 * @param $version
 * @return array
 */
public function getNodesByStage($objectType, $objectID, $version = 1){
    return $this->loadExtension('chengfangjinke')->getNodesByStage($objectType, $objectID, $version);
}

/**
 * @Notes:部门管理层审核逻辑 需求任务和需求意向的变更审核场景
 * @Date: 2023/8/23
 * @Time: 10:40
 * @Interface checkRequirementAndOpinion
 * @param $objectType
 * @param $objectID
 * @param $version
 * @param $stage
 * @param $comment
 * @return string
 */
public function checkRequirementAndOpinion($objectType,$objectID,$version,$stage,$comment){
    return $this->loadExtension('chengfangjinke')->checkRequirementAndOpinion($objectType,$objectID,$version,$stage,$comment);
}

/**
 * 设置下一节点待处理
 *
 * @param $objectType
 * @param $objectId
 * @param $version
 * @return bool
 */
public function setNextReviewNodePending($objectType, $objectId, $version = 0){
    return $this->loadExtension('chengfangjinke')->setNextReviewNodePending($objectType, $objectId, $version);
}

/**
 * 设置某节点待处理
 *
 * @param $nodeId
 * @return bool
 */
public function setReviewNodePending($nodeId){
    return $this->loadExtension('chengfangjinke')->setReviewNodePending($nodeId);
}

/**
 * 根据节点id获得节点信息
 *
 * @param $nodeId
 * @return mixed
 */
public function getNodeInfoByNodeId($nodeId){
    return $this->loadExtension('chengfangjinke')->getNodeInfoByNodeId($nodeId);
}


/**
 * @param $objectType
 * @param $objectId
 * @param $version
 * @param $nodeCode
 * @return mixed
 */
public function getNodeInfoByNodeCode($objectType, $objectId, $version, $nodeCode){
    return $this->loadExtension('chengfangjinke')->getNodeInfoByNodeCode($objectType, $objectId, $version, $nodeCode);
}

/**
 *根据审核节点id获得审核用户列表
 *
 * @param $nodeIds
 * @param $exWhere
 * @return mixed
 */
public function getReviewersByNodeIds($nodeIds, $exWhere = ''){
    return $this->loadExtension('chengfangjinke')->getReviewersByNodeIds($nodeIds, $exWhere);
}

/**
 * 获得历史审核也节点
 *
 * @param $objectType
 * @param $objectId
 * @param $version
 * @param $nodeCode
 * @param $exWhere
 * @return mixed
 */
public function getHistoryReviewStageList($objectType, $objectId, $version, $nodeCode = '', $exWhere = ''){
    return $this->loadExtension('chengfangjinke')->getHistoryReviewStageList($objectType, $objectId, $version, $nodeCode, $exWhere);
}

/**
 * 忽略审核人
 *
 * @param $nodeId
 * @param string $exWhere
 * @return bool
 */
public function setReviewersIgnore($nodeId, $exWhere = ''){
    return $this->loadExtension('chengfangjinke')->setReviewersIgnore($nodeId, $exWhere);
}

/**
 * 获取审批节点是否在审批过程中
 *
 * @param $objectType
 * @param $objectId
 * @param $version
 * @param $nodeCode
 * @return mixed
 */
public function getReviewNodeIsProcessing($objectType, $objectId, $version, $nodeCode){
    return $this->loadExtension('chengfangjinke')->getReviewNodeIsProcessing($objectType, $objectId, $version, $nodeCode);
}

/**
 *获得指定版本的审核列表
 *
 * @param $objectType
 * @param $objectId
 * @param int $version
 * @return mixed
 */
public function getReviewListByVersion($objectType, $objectId, $version = 1){
    return $this->loadExtension('chengfangjinke')->getReviewListByVersion($objectType, $objectId, $version);
}

/**
 *获得指定版本的审核列表
 *
 * @param $objectType
 * @param $objectId
 * @param int $version
 * @return mixed
 */
public function getAllVersionReviewList($objectType, $objectId){
    return $this->loadExtension('chengfangjinke')->getAllVersionReviewList($objectType, $objectId);
}


/**
 * 设置节点自动审核通过
 *
 * @param $nodeId
 * @param string $comment
 * @return bool
 */
public function setReviewNodeAutoPass($nodeId, $comment = ''){
    return $this->loadExtension('chengfangjinke')->setReviewNodeAutoPass($nodeId, $comment);
}