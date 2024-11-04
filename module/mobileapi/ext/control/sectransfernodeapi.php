<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * @param $id
     * @param $isHistory
     * @return void
     */
    public function sectransferNodeApi()
    {
        $id = $_POST['id'];
        $isHistory = (int)$_POST['isHistory'];
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'sectransferNodeApi');
    }

    /**
     * 当前审核节点列表
     * @param $id
     * @return array
     */
    public function reviewNodeList($id)
    {
        $this->app->loadLang('sectransfer');
        $this->loadModel('sectransfer');

        $users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $sectransfer = $this->sectransfer->getByID($id);
        $nodes       = $this->loadModel('review')->getNodes('sectransfer', $id, $sectransfer->version);
        $data        = [];
        $secondOrder = 0 != $sectransfer->secondorderId ? $this->loadModel('secondorder')->getById($sectransfer->secondorderId) : false;
        if(!empty($secondOrder) && 'guestjx' == $secondOrder->createdBy){
            $this->lang->sectransfer->reviewNodeStatusList[7] = 'waitjx';
        }

        if(empty($nodes)){
            return $data;
        }

        foreach ($this->lang->sectransfer->reviewNodeStatusList as $key => $reviewNode){
            $reviewerUserTitle = '';
            $realReviewer = new stdClass();
            $realReviewer->status = '';
            $realReviewer->comment = '';
            if (isset($nodes[$key - 1])) {
                $currentNode = $nodes[$key - 1];
                $reviewers = $currentNode->reviewers;
                if (!(is_array($reviewers) && !empty($reviewers))) {
                    continue;
                }
                //所有审核人
                $reviewersArray = array_column($reviewers, 'reviewer');
                $userCount = count($reviewersArray);
                if ($userCount > 0) {
                    $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                    $reviewerUserTitle = implode(',', $reviewerUsers);
                    //获得实际审核人
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                }
            }else{
                continue;
            }
            $arr = [
                'reviewNode'     => zget($this->lang->sectransfer->reviewNodeStatusLableList, $reviewNode),
                'reviewUser'     => $reviewerUserTitle,
                'reviewerStatus' => '',
                'reviewResult'   => '',
                'comment'        => html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($realReviewer->comment,ENT_QUOTES))))),
                'reviewTime'     => $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: '',
                'status'         => $realReviewer->status
            ];
            if(!(($sectransfer->status == 'waitApply' || $sectransfer->status == 'approveReject' || $sectransfer->status == 'externalReject')
                and $realReviewer->status == 'pending' and $reviewNode == 'waitCMApprove')){
                $arr['reviewResult'] = zget($this->lang->sectransfer->reviewStatusList, $realReviewer->status, '');
            }
            if($realReviewer->status == 'pass'
                || $realReviewer->status == 'reject'
                || $realReviewer->status == 'incorporate'
                || $realReviewer->status == 'appoint'){
//                $arr['reviewerStatus'] = "(" . zget($users, $realReviewer->reviewer, '') . ")";
            }

            $data[] = $arr;
        }
        return $data;
    }

    public function historyReviewNode($id)
    {
        $this->app->loadLang('sectransfer');
        $this->loadModel('sectransfer');

        $sectransfer = $this->sectransfer->getByID($id);
        $users = $this->loadModel('user')->getPairs('noletter');
        $res   = $this->dao
            ->select('version')
            ->from(TABLE_REVIEWNODE)
            ->where('objectType')->eq('sectransfer')
            ->andWhere('objectID')->eq($id)
            ->groupby('version')->fetchall();

        $versions = array_column($res,'version');
        foreach ($versions as $version) {
            $data = $this->loadModel('review')->getNodes('sectransfer', $id, $version);
            foreach ($data as $k=>$v){
                if ($v->status == 'wait' || !(is_array($v->reviewers) && !empty($v->reviewers))){
                    unset($data[$k]);
                }
            }
            $nodes[$version]['nodes'] = $data;
        }
        if(empty($nodes)){
            return [];
        }

        $data = [];
        foreach ($nodes as $version => $nv){
            $node = (array)$nv['nodes'];
            $arrs = [];
            foreach ($this->lang->sectransfer->reviewNodeStatusList as $key => $reviewNode){
                $reviewerUserTitle = '';
                $realReviewer = new stdClass();
                $realReviewer->status = '';
                $realReviewer->comment = '';
                if (isset($node[$key - 1])) {
                    $currentNode = $node[$key - 1];
                    $reviewers = $currentNode->reviewers;
                    if (!(is_array($reviewers) && !empty($reviewers))) {
                        continue;
                    }
                    //所有审核人
                    $reviewersArray = array_column($reviewers, 'reviewer');
                    $userCount = count($reviewersArray);
                    if ($userCount > 0) {
                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                        $reviewerUserTitle = implode(',', $reviewerUsers);
                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                    }
                }else{
                    continue;
                }
                $arr = [
                    'reviewNode'     => zget($this->lang->sectransfer->reviewNodeStatusLableList, $reviewNode),
                    'reviewUser'     => $reviewerUserTitle,
                    'reviewerStatus' => zget($this->lang->sectransfer->reviewStatusList, $realReviewer->status, ''),
                    'comment'        => $realReviewer->comment,
                    'reviewTime'     => $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: '',
                    'status'         => $realReviewer->status
                ];
                if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject' || $realReviewer->status == 'incorporate' || $realReviewer->status == 'appoint'){
//                    $arr['reviewerStatus'] = '(' . zget($users, $realReviewer->reviewer, '') . ')';
                }

                $arrs[] = $arr;
            }

            $data[$version] = $arrs;
        }

        return $data;
    }

}
