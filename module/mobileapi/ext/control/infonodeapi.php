<?php
include '../../control.php';
class myMobileApi extends mobileapi
{

    /**
     * 金信-数据获取审核节点
     */
    public function infoNodeApi(){
        $id = $_POST['id'];
        $isHistory = $_POST['isHistory'];
        $errMsg = $this->checkInput();
        if (!empty($errMsg)) {
            $this->loadModel('mobileapi')->response('fail', implode(',', $errMsg), array(), 0, 203, 'infoNodeApi');
        }
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
            $this->lang->info->reviewNodeList['0'] = '配置管理CM';
            $this->lang->info->reviewNodeList['5'] = '上海分公司领导审批';
            $this->lang->info->reviewNodeList['6'] = '二线专员';
        }
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'infoNodeApi');
    }
    //当前审核节点
    public function reviewNodeList($id)
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $info = $this->loadModel('info')->getByID($id);
        $nodes = $this->loadModel('review')->getNodes('info', $id, $info->version);
        if ($info->createdDate > "2024-04-02 23:59:59"){
            unset($this->lang->info->reviewerList[3]);
        }
        $data = [];
        $reviewNodeList = $this->lang->info->reviewerList;
        foreach ($reviewNodeList as $key => $reviewNode) {
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
                    }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                        $realReviewer->dealuser = zget($users, $realReviewer->reviewer, '');;
                    }else{
                        $realReviewer->dealuser = $reviewerUsersShow;
                    }
                    $realReviewer->comment = in_array($key, [0,1,7]) && '不用审批' == $realReviewer->comment  ? '不用处理' : $realReviewer->comment;
                    $realReviewer->reviewResult = zget($this->lang->info->confirmResultList, $realReviewer->status, '');
                }
                $data[] = $realReviewer;
            }
        }
        return $data;
    }
    //历史审核节点
    public function historyReviewNode($id)
    {
        $users = $this->loadModel('user')->getPairs('noletter');
        $info = $this->loadModel('info')->getByID($id);
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('info')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        $nodes = [];
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('info', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        if ($info->createdDate > "2024-04-02 23:59:59"){
            unset($this->lang->info->reviewerList[3]);
        }
        $arr = [];
        $reviewNodeList = $this->lang->info->reviewerList;
        $i = 0;
        foreach ($nodes as $version=>$node) {
            $i++;
            $nodes = (array)$node['nodes'];
            $j = 0;
            $data = [];
            foreach ($reviewNodeList as $key => $reviewNode) {
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
                        }elseif ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                            $realReviewer->dealuser = zget($users, $realReviewer->reviewer, '');;
                        }else{
                            $realReviewer->dealuser = $reviewerUsersShow;
                        }
                        $realReviewer->comment = in_array($key, [0,1,7]) && '不用审批' == $realReviewer->comment  ? '不用处理' : $realReviewer->comment;
                        $realReviewer->reviewResult = zget($this->lang->info->confirmResultList, $realReviewer->status, '');
                    }
                    $arr[$version][] = (array)$realReviewer;
                }
            }
        }
        return array_values($arr);
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
