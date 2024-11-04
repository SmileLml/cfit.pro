<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-数据获取审核节点
     */
    public function requirementNodeApi(){
        $id = $_POST['id'];
        $isHistory = $_POST['isHistory'];
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(), 0, 203, 'requirementNodeApi');
        }
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'requirementNodeApi');
    }
    //当前审核节点
    public function reviewNodeList($id)
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $requirement = $this->loadModel('requirement')->getByID($id);
        $nodes = $this->loadModel('review')->getNodes('requirement', $id, $requirement->version);
        if ($nodes){
            $nodes = array_column($nodes,null,'stage');
        }
        $data = [];
        if ($nodes[1]->reviewers){
            foreach ($this->lang->requirement->reviewerStageList as $key => $reviewNode) {
                $reviewerUserTitle = '';
                $reviewerUsersShow = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';

                if (isset($nodes[$key])) {
                    $currentNode = $nodes[$key];
                    $reviewers = $currentNode->reviewers;
                    if(!(is_array($reviewers) && !empty($reviewers))) {
                        continue;
                    }
                    //所有审核人
                    $reviewersArray = array_column($reviewers, 'reviewer');
                    $userCount = count($reviewersArray);
                    if($userCount > 0) {
                        $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $subCount = 3;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                        //获得实际审核人
                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                        $realReviewer->nodeName = $reviewNode;
                        if($realReviewer->status == 'ignore'){
                            $realReviewer->dealuser = '';
                        }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'syncfail' || $realReviewer->status == 'syncsuccess' || $realReviewer->status == 'feedbacksuccess' || $realReviewer->status == 'feedbackfail'){
                            $realReviewer->dealuser = zget($users, $realReviewer->reviewer, '');
                        }else{
                            $realReviewer->dealuser = $reviewerUsersShow;
                        }
                        $realReviewer->reviewResult = zget($this->lang->requirement->resultstatusList, $realReviewer->status,'');
                    }
                    $data[] = $realReviewer;
                }
            }
        }

        return $data;
    }
    //历史审核节点
    public function historyReviewNode($id)
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $requirement = $this->loadModel('requirement')->getByID($id);
        $allNodes = $this->loadModel('review')->getAllNodes('requirement', $id); //所有历史审批信息

        foreach ($allNodes as $key=>$node){
            $allNodes[$key] = array_column($node,null,'stage');

        }
        $arr = [];
        foreach ($allNodes as $version=>$nodes) {
            $data = [];
            foreach ($this->lang->requirement->reviewerStageList as $key => $reviewNode) {
                $reviewerUserTitle = '';
                $reviewerUsersShow = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';

                if (isset($nodes[$key])) {
                    $currentNode = $nodes[$key];
                    $reviewers = $currentNode->reviewers;
                    if(!(is_array($reviewers) && !empty($reviewers))) {
                        continue;
                    }
                    //所有审核人
                    $reviewersArray = array_column($reviewers, 'reviewer');
                    $userCount = count($reviewersArray);
                    if($userCount > 0) {
                        $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $subCount = 3;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                        //获得实际审核人
                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                        $realReviewer->nodeName = $reviewNode;
                        if($realReviewer->status == 'ignore'){
                            $realReviewer->dealuser = '';
                        }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'syncfail' || $realReviewer->status == 'syncsuccess' || $realReviewer->status == 'feedbacksuccess' || $realReviewer->status == 'feedbackfail'){
                            $realReviewer->dealuser = zget($users, $realReviewer->reviewer, '');
                        }else{
                            $realReviewer->dealuser = $reviewerUsersShow;
                        }
                        $realReviewer->reviewResult = zget($this->lang->requirement->resultstatusList, $realReviewer->status,'');
                    }
                    $arr[$version][] = (array)$realReviewer;
                }
            }
        }
        return $arr;
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『数据获取单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '数据获取单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
