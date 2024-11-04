<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * @param $id
     * @param $isHistory
     * @return void
     */
    public function creditNodeApi()
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
        $this->app->loadLang('credit');
        $this->loadModel('credit');

        $users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $creditInfo = $this->credit->getById($id);
        $nodes = $this->loadModel('iwfp')->getCurrentVersionReviewNodes($creditInfo->workflowId, $creditInfo->version);
        $data        = [];
        if(empty($nodes)){
            return $data;
        }
        foreach ($nodes as $reviewNode){
            if(!isset($this->lang->credit->reviewNodeNameList[$reviewNode['nodeName']])){
                continue;
            }
            $nodeCode = $reviewNode['nodeName'];
            $nodeName = zget($this->lang->credit->reviewNodeNameList, $reviewNode['nodeName']);
            $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
            $reviewerUsers = zmget($users, $nodeDealUsers);

            if($reviewNode['dealUser']){
                $reviewerUsers = zmget($users, $reviewNode['dealUser']);
            }else{
                $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                $reviewerUsers = zmget($users, $nodeDealUsers);
            }
            $reviewResult =  zget($this->lang->credit->reviewResultList, $reviewNode['result'], '');
            $arr = [
                'reviewNode'     => $nodeName,
                'reviewUser'     => $reviewerUsers,
                'reviewerStatus' => '',
                'reviewResult'   => $reviewResult,
                'comment'        => html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($reviewNode['comment'],ENT_QUOTES))))),
                'reviewTime'     => $reviewNode['dealDate'] != '0000-00-00 00:00:00' ? $reviewNode['dealDate']: '',
                'status'         => $reviewNode['result']
            ];

            $data[] = $arr;
        }
        return $data;
    }

    public function historyReviewNode($id)
    {
        $this->app->loadLang('credit');
        $this->loadModel('credit');
        $dataResult        = [];
        $users = $this->loadModel('user')->getPairs('noletter');
        $creditInfo = $this->credit->getById($id);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($creditInfo->workflowId);
        foreach ($nodes as $version => $currentVersionReviewNodes){
            $arrs = [];
            foreach ($currentVersionReviewNodes as $reviewNode){
                $nodeCode = $reviewNode['nodeName'];
                $nodeName = zget($this->lang->credit->reviewNodeNameList, $reviewNode['nodeName']);
                if($reviewNode['dealUser']){
                    $reviewerUsers = zmget($users, $reviewNode['dealUser']);
                }else{
                    $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                    $reviewerUsers = zmget($users, $nodeDealUsers);
                }

                $reviewResult =  zget($this->lang->credit->reviewResultList, $reviewNode['result'], '');
                $arr = [
                    'reviewNode'     => $nodeName,
                    'reviewUser'     => $reviewerUsers,
                    'reviewerStatus' => $reviewResult,
                    'reviewResult'   => $reviewResult,
                    'comment'        => html_entity_decode(strip_tags(str_replace("<br />",PHP_EOL,strval(htmlspecialchars_decode($reviewNode['comment'],ENT_QUOTES))))),
                    'reviewTime'     => $reviewNode['dealDate'] != '0000-00-00 00:00:00' ? $reviewNode['dealDate']: '',
                    'status'         => $reviewNode['result']
                ];
                $arrs[] = $arr;
            }
            $dataResult[$version] = $arrs;
        }
        return $dataResult;
    }
}
