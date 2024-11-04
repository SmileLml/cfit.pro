<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更审核节点
     */
    public function problemNodeApi(){
        $id = $_POST['id'];
        $isHistory = $_POST['isHistory'];
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(), 0, 203, 'problemNodeApi');
        }
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'problemNodeApi');
    }
    //当前审核节点
    public function reviewNodeList($id){
        $this->app->loadLang('problem');
        $problem = $this->loadModel('problem')->getByID($id);
        $this->lang->problem->reviewNodeLabelList['2'] = $problem->createdBy == 'guestjx' ? '同步金信' : '同步清总';
        $users = $this->loadModel('user')->getPairs('noletter');

        $nodes = $this->loadModel('review')->getNodes('problem', $id,$problem->version);
        //修正历史数据，原来是三节点，现在是四节点，所以要创建一个同步清总的数据
        if(count($nodes) == 3){
            $childNodeList = array();
            $childNode = new stdClass();
            $childNode->reviewer = $problem->createdBy == 'guestjx' ? 'guestjx' : 'guestjk';
            $node = new stdClass();
            $node->type = '1';
            $node->stage = '3';
            //重组数组
            $newNodes = array();
            foreach ($nodes as $key => $oldnode ){
                if($key == 2){
                    if($nodes[$key-1]->status == 'pass'){
                        $childNode->status = 'syncsuccess';
                        $childNode->comment = '反馈单数据同步成功';
                        array_push($childNodeList, $childNode);
                        $node->status = 'syncsuccess';
                        $node->reviewers = $childNodeList;
                    }else{
                        array_push($childNodeList, $childNode);
                        $node->status = 'wait';
                        $node->reviewers = $childNodeList;
                    }
                    array_push($newNodes, $node);
                }
                array_push($newNodes, $oldnode);
            }
            $nodes = $newNodes;
        }
        foreach ($this->lang->problem->reviewNodeLabelList as $key => $reviewNode){
            $reviewerUserTitle = '';
            $reviewerUsersShow = '';
            $realReviewer = new stdClass();
            $realReviewer->status = '';
            $realReviewer->comment = '';
            if(isset($nodes[$key])) {
                $currentNode = $nodes[$key];
                $reviewers = $currentNode->reviewers;
                if(is_array($reviewers) || !empty($reviewers)) {
                    //所有审核人
                    $reviewersArray = array_column($reviewers, 'reviewer');
                    $userCount = count($reviewersArray);
                    if ($userCount > 0) {
                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $subCount = 3;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $userCount);
                        //获得实际审核人
                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                    }
                }
                $nodes[$key]->nodeName = $reviewNode;
                if($realReviewer->status == 'ignore'){
                    $nodes[$key]->dealuser = '';
                }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                    $nodes[$key]->dealuser = $nodes[$key]->reviewUser;
                }else{
                    $nodes[$key]->dealuser = $reviewerUsersShow;
                }
                $nodes[$key]->reviewResult = zget($this->lang->problem->confirmResultList, $realReviewer->status, '');
                if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'approvesuccess' || $realReviewer->status == 'externalsendback' || $realReviewer->status == 'closed' || $realReviewer->status == 'suspend' || $realReviewer->status == 'feedbacked'
                    || $realReviewer->status == 'firstpassed' || $realReviewer->status == 'finalpassed'){
                    $nodes[$key]->dealuser = zget($users, $realReviewer->reviewer, '');
                }
                if( $realReviewer->status == 'externalsendback'  && $reviewNode =='外部审批' && $problem->approverName) {
                    $realReviewer->comment = "打回人：".$problem->approverName.'<br> 审批意见：' . $realReviewer->comment;
                }
                $nodes[$key]->comment = html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($realReviewer->comment,ENT_QUOTES)))));
                $nodes[$key]->reviewTime = $realReviewer->reviewTime;
                $newNode[] = $nodes[$key];
            }
        }
        $data['nodes'] = $newNode;
        return $newNode;
    }
    //历史审核节点
    public function historyReviewNode($id)
    {
        $this->app->loadLang('problem');
        $users = $this->loadModel('user')->getPairs('noletter');
        $allNodes = $this->loadModel('review')->getAllNodes('problem', $id); //所有历史审批信息
        $problem = $this->loadModel('problem')->getByID($id);
        $this->lang->problem->reviewNodeLabelList['2'] = $problem->createdBy == 'guestjx' ? '同步金信' : '同步清总';
        $k = 1;
        foreach ($allNodes as $k=>$nodes) {
            // $count = count($lang->problem->reviewNodeLabelList);
            $count = count($nodes);
            $key = sprintf($this->lang->problem->countTip, $k);
            $k++;
            foreach ($this->lang->problem->reviewNodeLabelList as $key => $reviewNode){
                $reviewerUserTitle = '';
                $reviewerUsersShow = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';
                if(isset($nodes[$key])) {
                    $currentNode = $nodes[$key];
                    $reviewers = $currentNode->reviewers;
                    if(is_array($reviewers) || !empty($reviewers)) {
                        //所有审核人
                        $reviewersArray = array_column($reviewers, 'reviewer');
                        $userCount = count($reviewersArray);
                        if ($userCount > 0) {
                            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUserTitle = implode(',', $reviewerUsers);
                            $subCount = 3;
                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $userCount);
                            //获得实际审核人
                            $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                        }
                    }
                    $nodes[$key]->nodeName = $reviewNode;
                    if($realReviewer->status == 'ignore'){
                        $nodes[$key]->dealuser = '';
                    }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                        $nodes[$key]->dealuser = $nodes[$key]->reviewUser;
                    }else{
                        $nodes[$key]->dealuser = $reviewerUsersShow;
                    }
                    $nodes[$key]->reviewResult = zget($this->lang->problem->confirmResultList, $realReviewer->status, '');
                    if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'approvesuccess' || $realReviewer->status == 'externalsendback' || $realReviewer->status == 'closed' || $realReviewer->status == 'suspend' || $realReviewer->status == 'feedbacked'
                        || $realReviewer->status == 'firstpassed' || $realReviewer->status == 'finalpassed'){
                        $nodes[$key]->dealuser = zget($users, $realReviewer->reviewer, '');
                    }
                    if( $realReviewer->status == 'externalsendback'  && $reviewNode =='外部审批' && $problem->approverName) {
                        $realReviewer->comment = "打回人：".$problem->approverName.'<br> 审批意见：' . $realReviewer->comment;
                    }
                    $nodes[$key]->comment = html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($realReviewer->comment,ENT_QUOTES)))));
                    $nodes[$key]->reviewTime = $realReviewer->reviewTime;
                    $data[$k][] = $nodes[$key];
                }
            }
//            a($newNode);
//            $data[$k] = $newNode;
        }
        return $data;
    }
    private function checkInput()
    {
        $errMsg = [];
        if(!isset($_POST['id'])){
            $errMsg[] = "缺少『id』参数";
            return $errMsg;
        }

        if( isset($_POST['id']) && !$_POST['id']){
            $errMsg[] = '『问题单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '问题单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
