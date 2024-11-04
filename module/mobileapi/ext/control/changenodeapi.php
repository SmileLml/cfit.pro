<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * @param $id
     * @param $isHistory
     * @return void
     */
    public function changeNodeApi()
    {
        $id = $_POST['id'];
        $isHistory = (int)$_POST['isHistory'];
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'creditNodeApi');
    }

    /**
     * 当前审核节点列表
     * @param $id
     * @return array
     */
    public function reviewNodeList($id)
    {
        $this->app->loadLang('change');
        $this->loadModel('change');

        $users = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $changeInfo = $this->change->getById($id);
        $level = $changeInfo->level;
        $nodes = $this->loadModel('review')->getNodesGroupByNodeCode('change', $id, $changeInfo->version);
        $data = [];
        if (empty($nodes)) {
            return $data;
        }
        foreach ($this->lang->change->reviewLevelNodeCodeList[$level] as $key => $reviewNode) {
            $isSkipReviewerNode = false;
            if (!isset($nodes[$reviewNode]) || !$nodes[$reviewNode]) {
                $isSkipReviewerNode = true;
            }
            $currentNode = new stdClass();
            $currentNode->reviewers = [];
            $currentNode->status = 'ignore';
            $currentNode->isShow = 2;
            if (isset($nodes[$reviewNode])) {
                $currentNode = $nodes[$reviewNode];
            }
            $isShow = $currentNode->isShow;
            if ($isShow == 2) {
                continue;
            }
            $reviewerUserTitle = '';
            $reviewerUsersShow = '';
            $realReviewer = new stdClass();
            $realReviewer->status = '';
            $realReviewer->comment = '';
            $realReviewer->createdDate = '';
            $reviewers = $currentNode->reviewers;
            if (!$reviewers) {
                $reviewers = [];
            }
            //所有审核人
            $reviewersArray = [];
            if (!empty($reviewers)) {
                foreach ($reviewers as $reviewerInfo) {
                    if ($reviewerInfo->reviewer) {
                        $reviewersArray[] = $reviewerInfo->reviewer;
                    }
                }
            }
            $userCount = count($reviewersArray);
            if ($userCount == 0) {
                $isSkipReviewerNode = true;
            }

            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
            $reviewerUserTitle = implode(',', $reviewerUsers);
            $subCount = 3;
            $rowspanCount = count($reviewers);
            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $rowspanCount, ',', false);
            $reviewerUsersTdShow = getArraySubValuesStr($reviewerUsers, $subCount, ',', true);
            //获得实际审核人
            if (!empty($reviewers)) {
                $ignoreComment = '';
                if ($reviewNode == 'deptLeader') { //部门分管领导
                    $ignoreComment = $this->lang->change->deptLeaderIgnoreComment;
                }
                $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers, $ignoreComment);
                $date = $this->loadModel('consumed')->getByIdToDate($changeInfo->id, 'change', $realReviewer->reviewer); // 评审时间
                $realReviewer->createdate = isset($date[$realReviewer->reviewer]) ? date('Y-m-d', strtotime($date[$realReviewer->reviewer])) : '';
            }
            if (in_array($reviewNode, $this->lang->change->needIndependShowUsersNodeCodeList)) {
                foreach ($reviewers as $index => $review) {
                    $nodeName = zget($this->lang->change->reviewNodeCodeDescList, $reviewNode);
                    $reviewerUsers = zget($users, $review->reviewer);
                    if ($isSkipReviewerNode) {
                        $reviewResult = $this->lang->change->skipReviewerNodesDesc;
                    } else {
                        if ($reviewNode == $this->lang->change->reviewNodeCodeList['baseline']) {
                            if ($review->status == 'pass') {
                                $reviewResult = zget($this->lang->change->condition, $changeInfo->baseLineCondition, '');
                            } else {
                                $reviewResult = zget($this->lang->change->confirmResultList, $review->status, '');
                            }
                        } else {
                            if ($realReviewer->status != 'wait') {
                                if ($review->reviewerType == 1) {
                                    $reviewResult = zget($this->lang->change->confirmResultList, $review->status, '');
                                    if ($review->status == 'pass' || $review->status == 'reject') {
                                        $reviewerUsers = zget($users, $review->reviewer, '');
                                    } else {
                                        $reviewerUsers = zget($users, $review->reviewer, '');
                                    }
                                }
                            }else{
                                $reviewerUsers = zget($users, $review->reviewer, '');
                            }
                        }
                    }
                    if (!$review->comment && $review->reviewerType == 2) {
                        $comment = '-';
                    } else {
                        $comment = html_entity_decode(strip_tags(str_replace("<br />", PHP_EOL, strval(htmlspecialchars_decode($review->comment, ENT_QUOTES)))));
                    }
                    if (!$review->comment && $review->reviewerType == 2) {
                        $reviewTime = '-';
                    } else {
                        $reviewTime = $review->reviewTime == '0000-00-00 00:00:00' ? '' : $review->reviewTime;
                    }
                    if(is_array($reviewerUsers)){
                        $reviewerUsers = implode(',' , $reviewerUsers);
                    }
                    $arr = [
                        'reviewNode' => $nodeName,
                        'reviewUser' => $reviewerUsers,
                        'reviewerStatus' => '',
                        'reviewResult' => $reviewResult,
                        'comment' => $comment,
                        'reviewTime' => $reviewTime,
                        'status' => $review->status
                    ];
                    $data[] = $arr;
                    $reviewResult = '';
                }
            } else {
                $nodeName = zget($this->lang->change->reviewNodeCodeDescList, $reviewNode);
                if ($isSkipReviewerNode) {
                    $reviewResult = $this->lang->change->skipReviewerNodesDesc;
                } else {
                    if ($reviewNode == $this->lang->change->reviewNodeCodeList['baseline']) {
                        if ($realReviewer->status == 'pass') {
                            $reviewResult = zget($this->lang->change->condition, $changeInfo->baseLineCondition, '');
                        } else {
                            $reviewResult = zget($this->lang->change->confirmResultList, $realReviewer->status, '');
                        }
                    } else {
                        $reviewResult = zget($this->lang->change->confirmResultList, $realReviewer->status, '');
                    }
                    if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject') {
                        $reviewerUsers = zget($users, $realReviewer->reviewer, '');
                    }else{
                        $reviewerUsers = $reviewerUserTitle;
                    }
                    $comment = html_entity_decode(strip_tags(str_replace("<br />", PHP_EOL, strval(htmlspecialchars_decode($realReviewer->comment, ENT_QUOTES)))));
                    $reviewTime = $realReviewer->reviewTime == '0000-00-00 00:00:00' ? '' : $realReviewer->reviewTime;
                }
                if(is_array($reviewerUsers)){
                    $reviewerUsers = implode(',' , $reviewerUsers);
                }
                $arr = [
                    'reviewNode' => $nodeName,
                    'reviewUser' => $reviewerUsers,
                    'reviewerStatus' => '',
                    'reviewResult' => $reviewResult,
                    'comment' => $comment,
                    'reviewTime' => $reviewTime,
                    'status' => $realReviewer->status
                ];
                $data[] = $arr;
                $reviewResult = '';
            }
        }
        return $data;
    }

    public function historyReviewNode($id)
    {
        $this->app->loadLang('change');
        $this->loadModel('change');
        $dataResult = [];
        $users = $this->loadModel('user')->getPairs('noletter');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('change')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res, 'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('change', $id, $version);
            foreach ($data as $k => $v) {
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))) {
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }

        foreach ($nodes as $key => $node) {
            $countNodes = 0;
            foreach ($node['nodes'] as $nodeReview) {
                $nodeCode = $nodeReview->nodeCode;
                if (in_array($nodeCode, $this->lang->change->needIndependShowUsersNodeCodeList)) {
                    $countNodes += $nodeReview->reviewedCount;
                } else {
                    $countNodes++;
                }
            }
            $nodes[$key]['countNodes'] = $countNodes;
        }


        $historyNodes = $nodes;
        $change = $this->change->getByID($id);
        $level = $change->level;
        $nodes = $this->loadModel('review')->getNodesGroupByNodeCode('change', $id, $change->version);
        $i = 0;
        foreach ($historyNodes as $nk => $nv) {
            $arrs = [];
            $i++;
            $j = 0;
            $node = (array)$nv['nodes'];
            foreach ($node as $key => $value) {
                $reviewNode = $value->nodeCode;
                $isSkipReviewerNode = false;
                if (!isset($nodes[$reviewNode]) || !$nodes[$reviewNode]) {
                    $isSkipReviewerNode = true;
                }
                $currentNode = new stdClass();
                $currentNode->reviewers = [];
                $currentNode->status = 'ignore';
                $currentNode->isShow = 2;

                if (isset($value)) {
                    $currentNode = $value;
                }
                $isShow = $currentNode->isShow;
                if ($isShow == 2) {
                    continue;
                }
                $reviewerUserTitle = '';
                $reviewerUsersShow = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';
                $realReviewer->createdDate = '';
                $reviewers = $currentNode->reviewers;
                //所有审核人
                $reviewersArray = [];
                if (!empty($reviewers)) {
                    foreach ($reviewers as $reviewerInfo) {
                        if ($reviewerInfo->reviewer) {
                            $reviewersArray[] = $reviewerInfo->reviewer;
                        }
                    }
                }
                $userCount = count($reviewersArray);
                if ($userCount == 0) {
                    $isSkipReviewerNode = true;
                }

                $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                $reviewerUserTitle = implode(',', $reviewerUsers);
                $subCount = 3;
                $rowspanCount = count($reviewers);
                $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $rowspanCount, ',', false);
                $reviewerUsersTdShow = getArraySubValuesStr($reviewerUsers, $subCount, ',', true);
                //获得实际审核人
                if (!empty($reviewers)) {
                    $ignoreComment = '';
                    if ($reviewNode == 'deptLeader') { //部门分管领导
                        $ignoreComment = $this->lang->change->deptLeaderIgnoreComment;
                    }
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers, $ignoreComment);
                    $date = $this->loadModel('consumed')->getByIdToDate($change->id, 'change', $realReviewer->reviewer); // 评审时间
                    $realReviewer->createdate = isset($date[$realReviewer->reviewer]) ? date('Y-m-d', strtotime($date[$realReviewer->reviewer])) : '';
                }
                if (in_array($reviewNode, $this->lang->change->needIndependShowUsersNodeCodeList)) {
                    foreach ($reviewers as $index => $review) {
                        $nodeName = zget($this->lang->change->reviewNodeCodeDescList, $reviewNode);
                        if ($isSkipReviewerNode) {
                            $reviewResult = $this->lang->change->skipReviewerNodesDesc;
                        } else {
                            if ($reviewNode == $this->lang->change->reviewNodeCodeList['baseline']) {
                                if ($review->status == 'pass') {
                                    $reviewResult = zget($this->lang->change->condition, $change->baseLineCondition, '');
                                } else {
                                    $reviewResult = zget($this->lang->change->confirmResultList, $review->status, '');
                                }
                            } else {
                                if ($review->reviewerType == 1) {
                                    $reviewResult = zget($this->lang->change->confirmResultList, $review->status, '');
                                    if ($review->status == 'pass' || $review->status == 'reject') {
                                        $reviewerUsers = zget($users, $review->reviewer, '');
                                    }
                                } else {
                                    $reviewResult = '-';
                                }
                            }
                        }
                        $reviewerUsers = zget($users, $review->reviewer, '');
                        if (!$review->comment && $review->reviewerType == 2) {
                            $comment = '-';
                        } else {
                            $comment = html_entity_decode(strip_tags(str_replace("<br />", PHP_EOL, strval(htmlspecialchars_decode($review->comment, ENT_QUOTES)))));
                        }
                        if (!$review->comment && $review->reviewerType == 2) {
                            $reviewTime = '-';
                        } else {
                            $reviewTime = $review->reviewTime == '0000-00-00 00:00:00' ? '' : $review->reviewTime;
                        }
                        if(is_array($reviewerUsers)){
                            $reviewerUsers = implode(',' , $reviewerUsers);
                        }
                        $arr = [
                            'reviewNode' => $nodeName,
                            'reviewUser' => $reviewerUsers,
                            'reviewerStatus' => $reviewResult,
                            'reviewResult' => $reviewResult,
                            'comment' => $comment,
                            'reviewTime' => $reviewTime,
                            'status' => $review->status
                        ];
                        $arrs[] = $arr;
                    }
                } else {
                    $nodeName = zget($this->lang->change->reviewNodeCodeDescList, $reviewNode);
                    if($isSkipReviewerNode){
                        $reviewResult = $this->lang->change->skipReviewerNodesDesc;
                    }{
                        if($reviewNode == $this->lang->change->reviewNodeCodeList['baseline']){
                            if($realReviewer->status == 'pass'){
                                $reviewResult = zget($this->lang->change->condition, $change->baseLineCondition, '');
                            }else{
                                $reviewResult = zget($this->lang->change->confirmResultList, $realReviewer->status, '');
                            }
                        }else{
                            $reviewResult = zget($this->lang->change->confirmResultList, $realReviewer->status, '');
                        }
                        if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                            $reviewerUsers = zget($users, $realReviewer->reviewer, '');
                        }
                        $comment = html_entity_decode(strip_tags(str_replace("<br />", PHP_EOL, strval(htmlspecialchars_decode($realReviewer->comment, ENT_QUOTES)))));
                        $reviewTime = $realReviewer->reviewTime == '0000-00-00 00:00:00' ? '' : $realReviewer->reviewTime;
                    }
                    if(is_array($reviewerUsers)){
                        $reviewerUsers = implode(',' , $reviewerUsers);
                    }
                    $arr = [
                        'reviewNode' => $nodeName,
                        'reviewUser' => $reviewerUsers,
                        'reviewerStatus' => $reviewResult,
                        'reviewResult' => $reviewResult,
                        'comment' => $comment,
                        'reviewTime' => $reviewTime,
                        'status' => $realReviewer->status
                    ];
                    $arrs[] = $arr;
                }
            }
            $dataResult[$version] = $arrs;
        }
        return $dataResult;
    }
}
