<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更审核节点
     */
    public function modifyNodeApi(){
        $id = $_POST['id'];
        $isHistory = $_POST['isHistory'];
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(), 0, 203, 'modifyNodeApi');
        }
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'modifyNodeApi');
    }
    //当前审核节点
    public function reviewNodeList($id)
    {
        $this->loadModel('outwarddelivery');
        $users = $this->loadModel('user')->getPairs('noletter');

        $modify = $this->loadModel('modify')->getByID($_POST['id']);
        $nodes = $this->loadModel('review')->getNodes('modify', $id, $modify->version);
        if ($modify->level == 2){
            unset($this->lang->modify->reviewNodeList[6]);
        }elseif ($this->modify->level == 3){
            unset($this->lang->modify->reviewNodeList[5]);
            unset($this->lang->modify->reviewNodeList[6]);
        }
        if ($modify->createdDate > "2024-04-02 23:59:59"){
            unset($this->lang->modify->reviewNodeList[3]);
        }
        $newNode = [];
        $reviewNodeList = $this->lang->modify->reviewNodeList;
        foreach ($reviewNodeList as $key => $reviewNode) {
            $reviewerUserTitle = '';
            $reviewerUsersShow = '';
            $realReviewer = new stdClass();
            $realReviewer->status = '';
            $realReviewer->comment = '';
            if (isset($nodes[$key])) {
                $nodes[$key]->nodeName = $reviewNode;
                $currentNode = $nodes[$key];
                $reviewers = $currentNode->reviewers;
                if (!(is_array($reviewers) && !empty($reviewers))) {
                    continue;
                }

                //所有审核人
                $reviewersArray = array_column($reviewers, 'reviewer');
                $reviewersArrayNew = $this->loadModel('common')->getAuthorizer('modify', implode(',', $reviewersArray), $this->lang->modify->reviewBeforeStatusList[$key], $this->lang->modify->authorizeStatusList);
                $reviewersArray = explode(',', $reviewersArrayNew);
                $userCount = count($reviewersArray);
                if ($userCount > 0) {
                    $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                    $reviewerUserTitle = implode(',', $reviewerUsers);
                    $subCount = 10;
                    $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                    //获得实际审核人
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                    $extra = json_decode($realReviewer->extra);
                    if (!empty($extra->reviewerList)) {
                        $reviewersArray = $extra->reviewerList;
                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $subCount = 10;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                    }
                }
                if($realReviewer->status == 'ignore'){
                    if($key == 3 or $key == 7){
                        $nodes[$key]->reviewResult = '无需处理';
                        if ($key == 7) {
                            $realReviewer->comment = '';
                        }else{
                            $realReviewer->comment = $reviewers[0]->comment;
                        }
                    }else{
                        $nodes[$key]->reviewResult = '本次跳过';
                        $realReviewer->comment = '已通过';
                    }
                }else{
                    $nodes[$key]->reviewResult = zget($this->lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                }
                if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                    $extra = json_decode($realReviewer->extra);
                    if(!empty($extra->proxy)){
                        $nodes[$key]->reviewUser = zget($users, $extra->proxy, '')."处理" .  "  【".zget($users, $realReviewer->reviewer)."授权】";
                    }else{
                        $nodes[$key]->reviewUser = zget($users, $realReviewer->reviewer, '');
                    }
                }
                if($realReviewer->status == 'ignore'){
                    $nodes[$key]->dealuser = '';
                }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                    $nodes[$key]->dealuser = $nodes[$key]->reviewUser;
                }else{
                    $nodes[$key]->dealuser = $reviewerUsersShow;
                }

                $nodes[$key]->comment = html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($realReviewer->comment,ENT_QUOTES)))));
                $nodes[$key]->reviewTime = $realReviewer->reviewTime;
                $newNode[] = $nodes[$key];
            }

        }
        $modify->reviewFailReason = json_decode($modify->reviewFailReason, true);
        if (!empty($modify->reviewFailReason)) {
            $continueStatus = ['modifyreject', 'modifycancel', 'modifysuccess', 'modifysuccesspart', 'modifyrollback', 'modifyfail', 'modifyerror'];
            $count = count($modify->reviewFailReason[$modify->version]);
            foreach ($modify->reviewFailReason[$modify->version] as $key => $reasons){
                if (in_array($modify->status, $continueStatus) && $key == $count - 1) {
                    continue;
                }
                foreach ($reasons as $reason) {
                    $newNode[] = [
                        'nodeName'     => $this->lang->modify->outerReviewNodeList[$reason['reviewNode']],
                        'reviewResult'   => '',
                        'dealuser'       => zget($users, $reason['reviewUser'], ','),
                        'comment'       => '',
                        'reviewTime'       => '',
                    ];
                    if(!($modify->lastStatus == 'waitqingzong' && $modify->cancelStatus)){
                        $newNode[]['reviewResult'] = $reason['reviewResult'];
                        $newNode[]['comment'] = $reason['reviewFailReason'];
                        $newNode[]['reviewTime'] = $reason['reviewPushDate'];

                    }
                }
            }
        }
        $node4 = [];
        if($modify->isDiskDelivery==0){
            $node4['nodeName'] = $this->lang->modify->outerReviewNodeList['4'];
            $node4['dealuser'] = zget($users, 'guestjk', ',');
            if(!($modify->lastStatus == 'waitqingzong' && $modify->cancelStatus)){
                if (in_array($modify->status, array('waitqingzong', 'jxsynfailed','waitImplement'))) {
                    $node4['reviewResult'] = zget($this->lang->modify->statusList, $modify->status=='waitImplement'?'jxsynfailed':$modify->status, '');
                } elseif (in_array($modify->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) {
                    $node4['reviewResult'] = $this->lang->modify->synSuccess;
                }else{
                    $node4['reviewResult'] = '';
                }
            }
            if (in_array($modify->status, array('withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)) {
                $node4['comment'] = '生产变更单同步成功';
            } elseif (in_array($modify->status, array('waitqingzong', 'jxsynfailed','waitImplement'))) {
                $node4['comment'] = $modify->pushFailReason;
            }
            if($modify->pushDate != '0000-00-00 00:00:00' and in_array($modify->status, array('waitqingzong', 'jxsynfailed','withexternalapproval', 'modifyfail', 'modifysuccesspart', 'modifysuccess', 'modifyreject', 'modifyrollback','modifycancel','giteepass','jxSubmitImplement', 'modifyerror','jxacceptorReview')) and !empty($modify->externalCode)){
                $node4['reviewTime'] = $modify->pushDate;
            }
//            $newNode[] = $node4;

            $jxResult = '';
            if (in_array($modify->status, $this->lang->modify->failReason[3])) {
                $jxResult = zget($this->lang->modify->statusList, $modify->status);
            }
            $jxReason = '';
            if (in_array($modify->status, $this->lang->modify->failReason[4])) {
                if($modify->status == 'modifyreject'){
                    $jxReason = "打回人：".$modify->approverName."<br>审批意见：".$modify->returnReason;
                }else{
                    $jxReason = $modify->returnReason;
                }
            }

            $jxPushDate = '';
            if (strtotime($modify->changeDate) > 0 and in_array($modify->status, $this->lang->modify->failReason[4])) {
                $jxPushDate = $modify->changeDate;
            }
//            $newNode[] = [
//                'nodeName'        => $this->lang->modify->outerReviewNodeList['5'],
//                'dealuser'        => zget($users, 'guestjx', ','),
//                'reviewResult'      => $jxResult,
//                'reviewFailReason'  => $jxReason,
//                'reviewPushDate'      => $jxPushDate,
//            ];
        }else{
//            $newNode[] = [
//                'nodeName'        => $this->lang->modify->outerReviewNodeList['4'],
//                'dealuser'        => zget($users, 'guestjk', ','),
//                'reviewResult'      => '同步金信失败',
//                'reviewFailReason'  => $modify->pushFailReason,
//                'reviewPushDate'      => $modify->pushDate,
//            ];
        }
        $data['nodes'] = $newNode;
        unset($data['modify']);
        return $data;
    }
    //历史审核节点
    public function historyReviewNode($id)
    {
        $this->app->loadLang('outwarddelivery');
        $this->loadModel('modify');
        $modify = $this->modify->getByID($id);
        $users = $this->loadModel('user')->getPairs('noletter');
        $reviewFailReason = json_decode($modify->reviewFailReason, true);
        $this->app->loadLang('outwarddelivery');
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('modify')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res, 'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('modify', $id, $version);
            foreach ($data as $k => $v) {
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))) {
                    unset($data[$k]);
                }
                // 后续不再显示系统部审核节点，去掉
                if ($v->stage == 4 && isset($modify->createdDate) && $modify->createdDate > "2024-04-02 23:59:59"){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key => $node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if (isset($reviewFailReason[$key][4]) && !empty($reviewFailReason[$key][4])) {
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key][5]) && !empty($reviewFailReason[$key][5])) {
                $nodes[$key]['countNodes']++;
            }
        }
        $i = 0;
        $data = [];
        foreach ($nodes as $nk=>$nv) {
            $i++;
            $nodes = (array)$nv['nodes'];
            $j = 0;
            if ($modify->level == 2){
                unset($this->lang->outwarddelivery->reviewNodeList[6]);
            }elseif ($modify->level == 3){
                unset($this->lang->outwarddelivery->reviewNodeList[5]);
                unset($this->lang->outwarddelivery->reviewNodeList[6]);
            }
            if ($modify->createdDate > "2024-04-02 23:59:59"){
                unset($this->lang->modify->reviewNodeList[3]);
            }
            $reviewNodeList = $this->lang->modify->reviewNodeList;
            foreach ($reviewNodeList as $key => $reviewNode) {
                $reviewerUserTitle = '';
                $reviewerUsersShow = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';
                if (isset($nodes[$key])) {
                    $nodes[$key]->nodeName = $reviewNode;
                    $currentNode = $nodes[$key];
                    $reviewers = $currentNode->reviewers;
                    if (!(is_array($reviewers) && !empty($reviewers))) {
                        continue;
                    }

                    //所有审核人
                    $reviewersArray = array_column($reviewers, 'reviewer');
                    $reviewersArrayNew = $this->loadModel('common')->getAuthorizer('modify', implode(',', $reviewersArray), $this->lang->modify->reviewBeforeStatusList[$key], $this->lang->modify->authorizeStatusList);
                    $reviewersArray = explode(',', $reviewersArrayNew);
                    $userCount = count($reviewersArray);
                    if ($userCount > 0) {
                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $subCount = 10;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                        //获得实际审核人
                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                        $extra = json_decode($realReviewer->extra);
                        if (!empty($extra->reviewerList)) {
                            $reviewersArray = $extra->reviewerList;
                            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUserTitle = implode(',', $reviewerUsers);
                            $subCount = 10;
                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                        }
                    }
                    if($realReviewer->status == 'ignore'){
                        if($key == 3 or $key == 7){
                            $nodes[$key]->reviewResult = '无需处理';
                            if ($key == 7) {
                                $realReviewer->comment = '';
                            }else{
                                $realReviewer->comment = $reviewers[0]->comment;
                            }
                        }else{
                            $nodes[$key]->reviewResult = '本次跳过';
                            $realReviewer->comment = '已通过';
                        }
                    }else{
                        $nodes[$key]->reviewResult = zget($this->lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                    }
                    if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                        $extra = json_decode($realReviewer->extra);
                        if(!empty($extra->proxy)){
                            $nodes[$key]->reviewUser = zget($users, $extra->proxy, '')."处理" .  "  【".zget($users, $realReviewer->reviewer)."授权】";
                        }else{
                            $nodes[$key]->reviewUser = zget($users, $realReviewer->reviewer, '');
                        }
                    }
                    if($realReviewer->status == 'ignore'){
                        $nodes[$key]->dealuser = '';
                    }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                        $nodes[$key]->dealuser = $nodes[$key]->reviewUser;
                    }else{
                        $nodes[$key]->dealuser = $reviewerUsersShow;
                    }

                    $nodes[$key]->comment = html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($realReviewer->comment,ENT_QUOTES)))));
                    $nodes[$key]->reviewTime = $realReviewer->reviewTime;
                    $newNode[] = $nodes[$key];
                }

            }

            $data[$nk] = $nodes;
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
            $errMsg[] = '『生产变更单ID』不能为空';
            return $errMsg;
        }
        if(isset($_POST['id']) && $_POST['id']){
            if(!preg_match("/^[1-9][0-9]*$/",$_POST['id'])){
                $errMsg = '生产变更单ID只能正整数';
                return $errMsg;
            }
        }
        return $errMsg;
    }
}
