<?php

include '../../control.php';

class myMobileApi extends mobileapi
{
    /**
     * @param $id
     * @param $isHistory
     * @return void
     */
    public function putproductionNodeApi()
    {
        $id = $_POST['id'];
        $isHistory = (int)$_POST['isHistory'];
        $list = $isHistory ? $this->historyReviewNode($id) : $this->reviewNodeList($id);
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if (in_array($this->app->user->dept,$depts)){
            $this->lang->putproduction->reviewNodeCodeNameList = array(
                $this->lang->putproduction->reviewNodeCodeList['waitcm']      =>  '配置管理CM',
                $this->lang->putproduction->reviewNodeCodeList['waitleader']  =>  '上海分公司领导',
                $this->lang->putproduction->reviewNodeCodeList['waitgm']       => '上海分公司总经理',
                $this->lang->putproduction->reviewNodeCodeList['waitproduct'] =>  '二线专员',
            );
        }
        $this->loadModel('mobileapi')->response('success', '', $list, 0, 200, 'putproductionNodeApi');
    }

    /**
     * 当前审核节点列表
     * @param $id
     * @return array
     */
    public function reviewNodeList($id)
    {
        $this->app->loadLang('putproduction');
        $this->loadModel('putproduction');

        $users       = $this->loadmodel('user')->getPairs('noletter|noclosed');
        $putproductionInfo = $this->putproduction->getByID($id);
        $nodes = $this->loadModel('iwfp')->getCurrentVersionReviewNodes($putproductionInfo->workflowId, $putproductionInfo->version);
        $data        = [];
        if(empty($nodes)){
            return $data;
        }
        foreach ($nodes as $reviewNode){
            if(!isset($this->lang->putproduction->reviewNodeCodeNameList[$reviewNode['nodeName']])){
                continue;
            }
            $nodeCode = $reviewNode['nodeName'];
            $nodeName = zget($this->lang->putproduction->reviewNodeCodeNameList, $reviewNode['nodeName']);
            if($reviewNode['dealUser']){
                $reviewerUsers = zmget($users, $reviewNode['dealUser']);
            }else{
                $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                $reviewerUsers = zmget($users, $nodeDealUsers);
            }
            if($reviewNode['nodeName'] == 'waitexternalreview'){
                if($reviewNode['result'] == 'pass'){
                    $reviewNode['result'] = $putproductionInfo->opResult;
                }
            }
            if($nodeCode == $this->lang->putproduction->reviewNodeCodeList['waitdelivery']){
                $reviewResult =  zget($this->lang->putproduction->syncResultList, $reviewNode['result'], '');
            }else{
                $reviewResult =  zget($this->lang->putproduction->reviewResultList, $reviewNode['result'], '');
            }
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
        $this->app->loadLang('putproduction');
        $this->loadModel('putproduction');

        $users = $this->loadModel('user')->getPairs('noletter');
        $putproductionInfo = $this->putproduction->getByID($id);
        $nodes = $this->loadModel('iwfp')->getAllVersionReviewNodes($putproductionInfo->workflowId);
        foreach ($nodes as $version => $currentVersionReviewNodes){
            $arrs = [];
            foreach ($currentVersionReviewNodes as $reviewNode){
                $nodeCode = $reviewNode['nodeName'];
                $nodeName = zget($this->lang->putproduction->reviewNodeCodeNameList, $reviewNode['nodeName']);
                $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                $reviewerUsers = zmget($users, $nodeDealUsers);
                if($reviewNode['nodeName'] == 'waitexternalreview'){
                    if($reviewNode['result'] == 'pass'){
                        $reviewNode['result'] = $putproductionInfo->opResult;
                    }
                }
                if($nodeCode == $this->lang->putproduction->reviewNodeCodeList['waitdelivery']){
                    $reviewResult =  zget($this->lang->putproduction->syncResultList, $reviewNode['result'], '');
                }else{
                    $reviewResult =  zget($this->lang->putproduction->reviewResultList, $reviewNode['result'], '');
                }
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
            $data[$version] = $arrs;
        }
        return $data;
    }

}
