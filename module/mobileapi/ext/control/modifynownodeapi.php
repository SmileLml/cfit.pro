<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-生产变更当前审核节点
     */
    public function modifyNowNodeApi()
    {
        $this->app->loadLang('outwarddelivery');
        $this->app->loadLang('release');
        $this->app->loadLang('projectrelease');
        $this->app->loadLang('testingrequest');
        $this->app->loadLang('productenroll');
        $this->app->loadLang('modifycncc');
        $this->app->loadLang('application');
        $this->app->loadLang('file');
        $this->app->loadLang('api');
        $this->loadModel('outwarddelivery');
        $this->app->loadLang('modifycncc');
        $users = $this->loadModel('user')->getPairs('noletter');

        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(), 0, 203, 'workReportAppApi');
        }
        $data = $this->loadModel('modify')->getHistoryNodesApi($_POST['id']);
        $modify = $data['modify'];
        $nodes = $data['nodes'][$data['modify']->version]['nodes'];
        $reviewNodeList = $this->lang->modify->reviewNodeList;
        if ($modify->isDiskDelivery == 0) {

        }
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
                        $realReviewer->comment = '';
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
                $nodes[$key]->dealuser = $reviewerUsersShow;
                $nodes[$key]->comment = $realReviewer->comment;
                $nodes[$key]->reviewTime = $realReviewer->reviewTime;
            }
        }
        $data['nodes'] = $nodes;
        unset($data['modify']);
        $this->loadModel('mobileapi')->response('success', '', $data ,  0, 200,'modifyViewApi');
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
