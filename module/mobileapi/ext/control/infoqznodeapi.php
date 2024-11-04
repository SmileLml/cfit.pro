<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * @param $id
     * @param $isHistory
     * @return void
     */
    public function infoqzNodeApi()
    {
        $id = $_POST['id'];
        $isHistory = (int)$_POST['isHistory'];
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'infoqzNodeApi');
    }

    /**
     * 当前审核节点列表
     * @param $id
     * @return array
     */
    public function reviewNodeList($id)
    {
        $this->app->loadLang('infoqz');
        $this->loadModel('infoqz');

        $users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $infoqzInfo = $this->infoqz->getByID($id);
        $nodes    = $this->loadModel('review')->getNodes('infoQz', $id, $infoqzInfo->version);
        $data        = [];
        if(empty($nodes)){
            return $data;
        }
        if ($infoqzInfo->createdDate > "2024-04-02 23:59:59"){
            unset($this->lang->infoqz->reviewerList[3]);
        }
        foreach ($this->lang->infoqz->reviewerList as $key => $reviewNode){
            if($key=='4') {
                continue;
            }else{
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';
                if(isset($nodes[$key])){
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
                        $reviewerUsers = implode(',',$reviewerUsers);
                        $subCount = 3;
                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                        //获得实际审核人
                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                        if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                            $reviewerUsers = zget($users, $realReviewer->reviewer, '');
                        }
                    }
                    $arr = [
                        'reviewNode'     => $reviewNode,
                        'reviewUser'     => $reviewerUsers,
                        'reviewerStatus' => '',
                        'reviewResult'   =>  zget($this->lang->infoqz->confirmResultList, $realReviewer->status, ''),
                        'comment'        => $realReviewer->comment,
                        'reviewTime'     => $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: '',
                        'status'         => $realReviewer->status
                    ];
                    $data[] = $arr;
                }
            }
        }
        return $data;
    }

    public function historyReviewNode($id)
    {
        $this->app->loadLang('infoqz');
        $this->loadModel('infoqz');
        $dataResult        = [];
        $users = $this->loadModel('user')->getPairs('noletter');
        $infoqzInfo = $this->infoqz->getByID($id);
        $reviewFailReason = json_decode($infoqzInfo->reviewFailReason,true);
        $res = $this->dao->select('version')->from(TABLE_REVIEWNODE)->where('objectType')->eq('infoqz')->andWhere('objectID')->eq($id)->groupby('version')->fetchall();
        $versions = array_column($res,'version');
        $nodes = [];
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('infoqz', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        foreach ($nodes as $key=>$node) {
            $nodes[$key]['countNodes'] = count($node['nodes']);
            if (isset($reviewFailReason[$key]['guestjk']) && !empty($reviewFailReason[$key]['guestjk'])){
                $nodes[$key]['countNodes']++;
            }
            if (isset($reviewFailReason[$key]['guestcn']) && !empty($reviewFailReason[$key]['guestcn'])){
                $nodes[$key]['countNodes']++;
            }
        }
        $i = 0;
        foreach ($nodes as $nk=>$nv) {
            $arrs = [];
            $i++;
            $chilrednnodes = (array)$nv['nodes'];
            $j = 0;
            if ($infoqzInfo->createdDate > "2024-04-02 23:59:59"){
                unset($this->lang->infoqz->reviewerList[3]);
            }
            foreach ($this->lang->infoqz->reviewerList as $key => $reviewNode){
                if($key=='4') {
                    continue;
                }else{
                    $realReviewer = new stdClass();
                    $realReviewer->status = '';
                    $realReviewer->comment = '';
                    if(isset($chilrednnodes[$key])){
                        $currentNode = $chilrednnodes[$key];
                        $reviewers = $currentNode->reviewers;
                        if(!(is_array($reviewers) && !empty($reviewers))) {
                            continue;
                        }
                        //所有审核人
                        $reviewersArray = array_column($reviewers, 'reviewer');
                        $userCount = count($reviewersArray);
                        if($userCount > 0) {
                            $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                            $reviewerUsers = implode(',',$reviewerUsers);
                            $subCount = 3;
                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                            //获得实际审核人
                            $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                            if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'){
                                $reviewerUsers = zget($users, $realReviewer->reviewer, '');
                            }
                        }
                        $arr = [
                            'reviewNode'     => $reviewNode,
                            'reviewUser'     => $reviewerUsers,
                            'reviewerStatus' => zget($this->lang->infoqz->confirmResultList, $realReviewer->status, ''),
                            'reviewResult'   => zget($this->lang->infoqz->confirmResultList, $realReviewer->status, ''),
                            'comment'        => $realReviewer->comment,
                            'reviewTime'     => $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: '',
                            'status'         => $realReviewer->status
                        ];
                        $arrs[] = $arr;
                    }
                }
            }
            $dataResult[$nk] = $arrs;
        }
        return $dataResult;
    }
}
