<?php
class  infoqzModel extends model
{
    const MAXNODE           = 6;   //审批节点最大值是7
    const SYSTEMNODE        = 3;   //系统部审批节点，可跳过
    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: 获得查询列表
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $action
     * @param $browseType
     * @param $queryID
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function getList($action, $browseType, $queryID, $orderBy, $pager = null)
    {
        $infoqzQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : ''; 
            if($query)
            {
                $this->session->set('infoqzQuery', $query->sql);
                $this->session->set('infoqzForm', $query->form);
            }

            if($this->session->infoqzQuery == false) $this->session->set('infoqzQuery', ' 1 = 1');

            $infoqzQuery = $this->session->infoqzQuery;

            // 处理受影响的业务系统搜索字段
//            if(strpos($infoqzQuery, '`app`') !== false)
//            {
//                $infoqzQuery = str_replace('`app`', "CONCAT(',', `app`, ',')", $infoqzQuery);
//            }

            // 处理[系统分类]搜索字段
            if(strpos($infoqzQuery, '`isPayment`') !== false)
            {
                $infoqzQuery = str_replace('`isPayment`', "CONCAT(',', `isPayment`, ',')", $infoqzQuery);
            }

            // 处理[执行节点]搜索字段
            if(strpos($infoqzQuery, '`node`') !== false)
            {
                $infoqzQuery = str_replace('`node`', "CONCAT(',', `node`, ',')", $infoqzQuery);
            }

            // 处理[支持人员]搜索字段
            if(strpos($infoqzQuery, '`supply`') !== false)
            {
                $infoqzQuery = str_replace('`supply`', "CONCAT(',', `supply`, ',')", $infoqzQuery);
            }

            // 处理[数据类别]搜索字段
            if(strpos($infoqzQuery, '`classify`') !== false)
            {
                $infoqzQuery = str_replace('`classify`', "CONCAT(',', `classify`, ',')", $infoqzQuery);
            }

            if (strpos($infoqzQuery, 'deadline') ){
                if (strpos($infoqzQuery, "`deadline` = '长期'")) {
                    $infoqzQuery = str_replace('`deadline`', '`isJinke` = 1 AND isDeadline', $infoqzQuery);
                    $infoqzQuery = str_replace('长期', '1', $infoqzQuery);
                }else{
                    $addQuery = " `isDeadline` = 2 AND `deadline`";
                    $infoqzQuery =  str_replace("`useDeadline`", $addQuery, $infoqzQuery);
                }
            }
            if (strpos($infoqzQuery, 'revertReason') ){
                $queryData = explode('AND',$infoqzQuery);
                foreach ($queryData as $qk=>$qv){
                    if (strpos($qv, 'revertReason')){
                        $revertArr = explode("'",$qv);
                        $str = '';
                        if (strpos('.'.$revertArr[1], '%')){
                            $str = '%';
                        }
                        $revertQueryStr = "'".$str.base64_decode(str_replace('%','',$revertArr[1])).$str."'";
                        $queryData[$qk] = $revertArr[0].$revertQueryStr.$revertArr[2];
                    }
                }
                $infoqzQuery = implode('AND',$queryData);
            }
            if (strpos($infoqzQuery, '`demandUnitOrDep`') !== false) {
                $demandQueryArr = explode('demandUnitOrDep',$infoqzQuery);
                $demandQueryArr1 = explode('%',$demandQueryArr[1]);
                $findInSet = '(FIND_IN_SET("'.$demandQueryArr1[1].'",demandUnitOrDep))';
                $demandStr = "`demandUnitOrDep".$demandQueryArr1[0].'%'.$demandQueryArr1[1]."%'";
                $infoqzQuery = str_replace($demandStr,$findInSet,$infoqzQuery);
            }
        }

        $infos = $this->dao->select('*')->from(TABLE_INFO_QZ)
            ->where('action')->eq($action)
            ->andWhere('status')->ne('deleted')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($infoqzQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'infoqz', $browseType != 'bysearch');

        $accountList = array();
        $this->loadModel('review');
        foreach($infos as $key => $info)
        {
            if(in_array($info->status, $this->lang->infoqz->allowReject))
            {
                $info->reviewers = $this->lang->infoqz->apiDealUserList['userAccount'];
            } else {
                $info->reviewers = $this->review->getReviewer('infoQz', $info->id, $info->version, $info->reviewStage);
            }
            $accountList[$info->createdBy] = $info->createdBy;
        }

        $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
        foreach($infos as $key => $info)
        {
            //待处理人
            if ($info->status == 'waitsubmitted'){
                $info->dealUsers = $info->createdBy;
            }else{
                $dealUsers = $this->getInfoDealUsers($info);
                $info->dealUsers = $dealUsers;
            }
            //创建人部门
            $info->createdDept = isset($dmap[$info->createdBy]) ? $dmap[$info->createdBy]->dept : '';
            $infos[$key] = $info;
        }

        return $infos;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->infoqz->search['actionURL'] = $actionURL;
        $this->config->infoqz->search['queryID']   = $queryID;
        $this->config->infoqz->search['params']['createdBy']['values'] = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->infoqz->search['params']['secondorderId']['values'] = array('' => '') + $this->loadModel('secondorder')->getNamePairs();
        $this->config->infoqz->search['params']['problem']['values'] = array('' => '') + $this->loadModel('problem')->getPairsAbstract();
        $this->config->infoqz->search['params']['demand']['values'] = array('' => '') + $this->loadModel('demand')->getPairsTitle('noclosed');;

        $data = $this->getDemandUnitDeptList();
        $this->config->infoqz->search['params']['demandUnitOrDep']['values']     = $data;


        $this->config->infoqz->search['params']['revertReason']['values']= array('' => '');
        $revertReasonList = $this->lang->infoqz->revertReasonList;
        $childTypeList = json_decode($this->lang->infoqz->childTypeList['all'],true);
        foreach($revertReasonList as $key=>$value){
            $this->config->infoqz->search['params']['revertReason']['values'] += array(base64_encode('"RevertReason":"'.$key.'"')=>$value);   //退回原因为json格式，不能只匹配key值

        }

        foreach ($childTypeList as $k=>$v) {
            foreach ($v as $vk=>$vv){
                $this->config->infoqz->search['params']['revertReason']['values'] += array(base64_encode('"RevertReasonChild":"'.$vk.'"')=>$revertReasonList[$k].'-'.$vv);   //退回原因为json格式，不能只匹配key值
            }
        }

        $this->loadModel('search')->setSearchParams($this->config->infoqz->search);
    }

    /**
     * Project: chengfangjinke
     * Method: getReviewers
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called getReviewers.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $deptId
     * @return array
     */
    public function getReviewers($deptId = 0)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $reviewers = array();
        if(!$deptId){
            $deptId = $this->app->user->dept;
        }
        $myDept = $this->loadModel('dept')->getByID($deptId);

        // 质量部CM
        $us = array('' => '');
        if(isset($myDept->cm)){
            $cms = explode(',', trim($myDept->cm, ','));
            foreach($cms as $c)
            {
                $us[$c] = $users[$c];
            }
        }

        $reviewers[] = $us;

        //申请部门组长审批
        $us = array('' => '');
        if(isset($myDept->groupleader)){
            $groupUsers = explode(',', trim($myDept->groupleader, ','));
            foreach($groupUsers as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 部门负责人
        $us  = array('' => '');
        if(isset($myDept->manager)){
            $cms = explode(',', trim($myDept->manager, ','));
            foreach($cms as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 系统部
        $us = array('' => '');
        $sysDept = $this->dao->select('id,manager')->from(TABLE_DEPT)->where('name')->eq('系统部')->fetch();
        if(isset($sysDept->manager)){
            $cms = explode(',', trim($sysDept->manager, ','));
            foreach($cms as $c)
            {
                $us[$c] = $users[$c];
            }
        }

        $reviewers[] = $us;

        // 产品经理
        $us  = array('' => '');
        if(isset($myDept->po)){
            $cms = explode(',', trim($myDept->po, ','));
            foreach($cms as $c) {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 部门分管领导
        $us  = array('' => '');
        if(isset($myDept->leader)){
            $cms = explode(',', trim($myDept->leader, ','));
            foreach($cms as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        /*
        // 总经理
        $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
        $reviewers[] = array($reviewer => $users[$reviewer]);
        */

        // 产创部二线专员
        $cms = explode(',', trim($myDept->executive, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        return $reviewers;
    }

    /**
     * Project: chengfangjinke
     * Method: submitReview
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called submitReview.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @param $version
     * @param $type
     */
    public function submitReview($infoID, $version, $type)
    {
        $status = 'pending';
        $stage = 1;
        $nodes = $this->post->nodes;
        $nodes = $this->getFormatReviewNodes($type, $nodes);
        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('infoQz', $infoID, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }


    /**
     * 获得格式化的审核节点以及审核人信息
     *
     * @param $level
     * @param $nodes
     * @param array $skipReviewNode
     * @return mixed
     */
    public function getFormatReviewNodes($type, $nodes){
        if($type == 'tech'){
            $nodes[3] = [];
            $nodes[4] = [];
            $nodes[5] = [];
        }
        else{
            $nodes[4] = [];
        }
        //从小到大排序
        ksort($nodes);
        //保存不校验必填，节点没有选择审核人员会造成数据错乱
        for ($i = 0;$i <= 6;$i++){
            if(!isset($nodes[$i])){
                $nodes[$i] = [];
            }
        }
        ksort($nodes);
        return $nodes;

    }
    /**
     * Project: chengfangjinke
     * Method: submitEditReview
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/5/26
     * Time: 15:43
     * Desc: 提交编辑审核信息.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @param $version
     * @param $type
     * @return bool
     */
    public function submitEditReview($infoID, $version, $type){
        $objectType = 'infoQz';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $infoID, $version);

        //编辑后审核结点的审核人
        $nodes = $this->post->nodes;
        $nodes = $this->getFormatReviewNodes($type, $nodes);

        $withGrade = true;
        foreach($nodes as $key => $currentReviews) {
            if (!is_array($currentReviews)) {
                $currentReviews = array($currentReviews);
            }
            $currentReviews = array_filter($currentReviews);
            //审核节点
            $oldNodeInfo = $oldNodes[$key];
            $oldReviewInfoList = $oldNodeInfo->reviewers;
            //原来节点审核人
            $oldReviews = [];
            if(!empty($oldReviewInfoList)){
                $oldReviews = array_column($oldReviewInfoList, 'reviewer');
            }
            //编辑前后当前节点审核人信息有变化
            if(array_diff($currentReviews, $oldReviews) || array_diff($oldReviews, $currentReviews)){
                $nodeID = $oldNodeInfo->id;

                //删除审核节点原来审核人
                if(!empty($oldReviews)){
                    $oldIds = array_column($oldReviewInfoList, 'id');
                    $res = $this->loadModel('review')->delReviewers($oldIds);
                }

                //新增节点本次编辑设置的
                if(!empty($currentReviews)) {
                    $status = $oldNodeInfo->status;
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                    }
                    $res = $this->loadModel('review')->addNodeReviewers($nodeID, $currentReviews, $withGrade, $status);
                }
            }else{
                if($currentReviews){
                    if($oldNodeInfo->status == 'ignore'){
                        $status = 'wait';
                        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq($status)->where('id')->eq($oldNodeInfo->id)->exec();
                        $this->dao->update(TABLE_REVIEWER)->set('status')->eq($status)->where('node')->eq($oldNodeInfo->id)->exec();
                    }
                }
            }
        }
        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: create
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called create.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $action
     * @return mixed
     */
    public function create($action)
    {

        $data = fixer::input('post')
            ->join('classify', ',')
            ->join('problem', ',')
            ->join('demand', ',')
            ->join('secondorderId', ',')
            ->join('node', ',')
            ->join('app', ',')
            ->join('dataSystem', ',')
            ->join('project', ',')
            ->remove('consumed')
            ->remove('nodes,uid')
            ->remove('demandUnitOrDepInput,demandUnitOrDepSelect')
            ->add('deliveryType', 'clearCenter')
            // ->stripTags($this->config->infoqz->editor->create['id'], $this->config->allowedTags)
            ->get();
        // 判断已关闭的问题单不可被关联
        if($data->problem){
            $problemIds = array_filter(explode(',', $data->problem));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problem'] = $res['msg'];
                return false;
            }
        }
        $status = 'wait';
        //保存的增加待提交状态
        if ($data->issubmit == 'save'){
            $status = 'waitsubmitted';
            $this->post->consumed = 0;
        }
        if($data->planBegin == ''){
            unset($data->planBegin);
        }
        if($data->planEnd == ''){
            unset($data->planEnd);
        }
        if ($data->issubmit == 'submit'){
            if((!isset($data->type)) || ($data->type == '0'))
            {
                return dao::$errors['type'] = $this->lang->infoqz->typeEmpty;
            }
//
            if(!isset($data->classify) or trim($data->classify, ',') == '')
            {
                return dao::$errors['classify'] = $this->lang->infoqz->classifyEmpty;
            }
            //检查表单数据
            $this->checkFormData();
            if(dao::isError()){
                return dao::$errors;
            }
            //检查审核信息
            $checkRes = $this->checkReviewerNodesInfo($data->type, $this->post->nodes);
            //有错误返回
            if(!$checkRes || dao::isError()){
                return dao::$errors;
            }

            if(empty($data->isJinke)){
                return dao::$errors['isJinke'] = sprintf($this->lang->infoqz->emptyObject,$this->lang->infoqz->isJinke);
            }
            if($data->isJinke =='1'){
                if(empty($data->desensitizationType)){
                    return dao::$errors['desensitizationType'] = sprintf($this->lang->infoqz->emptyObject,$this->lang->infoqz->desensitizationType);
                }
            }else{
                unset($data->desensitizationType);
            }
            if($data->isJinke =='1' and $data->isDeadline=='2' and empty($data->deadline)){
                return dao::$errors['deadline'] = sprintf($this->lang->infoqz->emptyObject,$this->lang->infoqz->deadline);
            }
            if(empty($data->deadline)){
                unset($data->deadline);
            }
            if($data->desensitizationType=='1' or $data->desensitizationType=='2'){
                // 是否需要脱敏被disabled掉了，补充上
                $data->isDesensitize = '1';
            }else{
                $data->isDesensitize = '0';
            }
            if($data->isDesensitize == '1' and $data->desensitizeProcess == '')
            {
                return dao::$errors['desensitizeProcess'] = $this->lang->infoqz->desensitizeProcessEmpty;
            }
            /* 判断是否关联了问题单或者需求单 */
            $flag = false;
            foreach($this->post->problem as $problem)
            {
                if(!empty($problem)) $flag = true;
            }

            /* @var requirementModel $requirementModel*/
            $requirementModel = $this->loadModel('requirement');
            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            if(!empty($data->demand))
            {
                $deleteOutDataStr = $requirementModel->getRequirementInfos($data->demand);
            }
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->infoqz->deleteOutTip , $deleteOutDataStr);
                return false;
            }

            foreach($this->post->demand  as $demand)
            {
                if(!empty($demand))  $flag = true;
            }
            foreach($this->post->secondorderId  as $secondorder)
            {
                if(!empty($secondorder))  $flag = true;
            }
            if(!$flag) return dao::$errors[] = $this->lang->infoqz->emptyDemandProblem;
            //检查项目列表
            // $count = count($this->post->project);
            // if($count < 1){
            //     return dao::$errors['project'] = $this->lang->infoqz->projectEmpty;
            // }else{
            //     foreach($this->post->project as $key => $project)
            //     {
            //         if($key > 0 && empty($project)) {
            //             return dao::$errors['project'] = $this->lang->infoqz->projectEmpty;
            //         }
            //     }
            // }

          /*  if(empty($_POST['consumed']))
            {
                return dao::$errors['consumed'] = $this->lang->infoqz->consumedEmpty;
            }
            $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $this->post->consumed))
            {
                dao::$errors['consumed'] = $this->lang->infoqz->consumedError;
                return false;
            }*/
            if(!empty($_POST['desc']))
            {
                if(strlen($_POST['desc'])>200){
                    return dao::$errors['desc'] = $this->lang->infoqz->notExceed200;
                }
            }

            if(!isset($data->app) or trim($data->app, ',') == '')
            {
                return dao::$errors['app'] = $this->lang->infoqz->appEmpty;
            }

            if(!isset($data->reason) or $data->reason == '')
            {
                $errorTips = $action == 'gain' ? $this->lang->infoqz->gainReasonEmpty : $this->lang->infoqz->fixReasonEmpty;
                return dao::$errors['reason'] = $errorTips;
            }

            //用途改为必填项
            if(!isset($data->purpose) or $data->purpose == '')
            {
                $errorTips = $action == 'gain' ? $this->lang->infoqz->gainPurposeEmpty : $this->lang->infoqz->gainPurposeEmpty;
                return dao::$errors['purpose'] = $errorTips;
            }

            if(!isset($data->step) or $data->step == '')
            {
                $errorTips = $action == 'gain' ? $this->lang->infoqz->gainStepEmpty : $this->lang->infoqz->fixStepEmpty;
                return dao::$errors['step'] = $errorTips;
            }

            if($action == 'fix' and (!isset($data->operation) or $data->operation == ''))
            {
                return dao::$errors['operation'] = $this->lang->infoqz->operationEmpty;
            }

        }


        $data->project     = trim($data->project, ',');
        $data->action      = $action;
        $data->status      = $status;
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = helper::now();
        $data->createdDept = $this->app->user->dept;
        $data->editedBy   = $this->app->user->account;
        $data->editedDate = helper::now();
        $data->demandUnitOrDep = in_array($data->dataCollectApplyCompany,[1,2,3]) ? implode(',',$_POST['demandUnitOrDepSelect']) : $_POST['demandUnitOrDepInput'];



        // if($action == 'gain' and (!isset($data->purpose) or $data->purpose == ''))
        // {
        //     return dao::$errors['purpose'] = $this->lang->infoqz->purposeEmpty;
        // }

        // $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        // if(!empty($data->app)){
        //     $as = [];
        //     foreach(explode(',', $data->app) as $apptype)
        //     {
        //         if(!$apptype) continue;
        //         $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
        //     }
        //     $applicationtype = implode(',', $as);
        //     if(!empty($applicationtype))$applicationtype=",".$applicationtype;
        //     $data->isPayment=$applicationtype;
        // }

        $data = $this->loadModel('file')->processImgURL($data, $this->config->infoqz->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_INFO_QZ)->data($data)
            ->batchCheckIF($data->issubmit == 'submit',$this->config->infoqz->create->requiredFields, 'notempty')
            ->exec();

        $infoID = 0;
        if(!dao::isError())
        {
            $infoID = $this->dao->lastInsertId();

            $this->loadModel('file')->updateObjectID($this->post->uid, $infoID, 'infoQz');
            $this->file->saveUpload('infoQz', $infoID);

            $this->submitReview($infoID, 1, $data->type);

            /* Record the relationship between the associated issue and the requisition.todo区分金信交付的数据获取和数据修正 20220526*/
            $tempAction = $action . $this->lang->infoqz->keywordSuffix;
            $this->loadModel('secondline')->saveRelationship($infoID, $tempAction, $data->problem, 'problem');
            $this->loadModel('secondline')->saveRelationship($infoID, $tempAction, $data->secondorderId, 'secondorder');
            $this->secondline->saveRelationship($infoID, $tempAction, $data->demand, 'demand');
            $this->secondline->saveRelationship($infoID, 'project' . ucfirst($tempAction), $data->project, 'project');


            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_INFO_QZ)->where('createdDate')->ge($date)->andWhere('action')->eq($action)->fetch('c');
            $type   = $action == 'gain' ? 'GQ' : 'FQ';
            $code   = "CFIT-$type-" . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_INFO_QZ)->set('code')->eq($code)->where('id')->eq($infoID)->exec();
            $this->loadModel('consumed')->record('infoQz', $infoID,'0', $this->app->user->account, '', 'waitsubmitted', array());
            // 同步数据管理
            $dataManagementCode = $this->loadModel('datamanagement')->syncData($infoID,'infoqz');
            $this->dao->update(TABLE_INFO_QZ)->set('dataManagementCode')->eq($dataManagementCode)->where('id')->eq($infoID)->exec();

            //新建关联二线，解决时间置空
//            /** @var problemModel $problemModel*/
//            $problemModel = $this->loadModel('problem');
//            if(!empty($data->demand)){
//                $problemModel->dealSolveTime($data->demand,'demand',$code);
//            }
            /*if(!empty($data->problem)){
                $problemModel->dealSolveTime($data->problem,'problem',$code);
            }*/
        }

        return $infoID;
    }

    /**
     * Project: chengfangjinke
     * Method: update
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called update.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return array
     */
    public function update($infoID)
    {
        $oldInfo = $this->getByID($infoID);
        $info = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->join('classify', ',')
            ->join('problem', ',')
            ->join('demand', ',')
            ->join('node', ',')
            ->join('secondorderId', ',')
            ->join('app', ',')
            ->join('dataSystem', ',')
            ->join('project', ',')
            ->remove('demandUnitOrDepInput,demandUnitOrDepSelect')
            ->remove('uid,files,labels,comment,nodes,consumed')
            // ->stripTags($this->config->infoqz->editor->edit['id'], $this->config->allowedTags)
            ->get();
        if($info->problem){
            $problemIds = array_filter(explode(',', $info->problem));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problem'] = $res['msg'];
                return false;
            }
        }
        if (!in_array($oldInfo->status,$this->lang->infoqz->allowEditStatusList)){
            return dao::$errors['editerror'] = $this->lang->infoqz->editStatusError;
        }
        if($info->planBegin == ''){
            unset($info->planBegin);
        }
        if($info->planEnd == ''){
            unset($info->planEnd);
        }
        $info->project = trim($info->project, ',');
        if ($info->issubmit == 'submit'){
            if((!isset($info->type)) || ($info->type == '0'))
            {
                return dao::$errors['type'] = $this->lang->infoqz->typeEmpty;
            }

            if(!isset($info->classify) or trim($info->classify, ',') == '')
            {
                return dao::$errors['classify'] = $this->lang->infoqz->classifyEmpty;
            }
            //检查表单数据
            $this->checkFormData();
            if(dao::isError()){
                return dao::$errors;
            }
            //检查审核信息
            $checkRes = $this->checkReviewerNodesInfo($info->type, $this->post->nodes);
            //有错误返回
            if(!$checkRes || dao::isError()){
                return dao::$errors;
            }

            if(empty($info->isJinke)){
                return dao::$errors['isJinke'] = sprintf($this->lang->infoqz->emptyObject,$this->lang->infoqz->isJinke);
            }
            if($info->isJinke =='1'){
                if(empty($info->desensitizationType)){
                    return dao::$errors['desensitizationType'] = sprintf($this->lang->infoqz->emptyObject,$this->lang->infoqz->desensitizationType);
                }
            }else{
                unset($info->desensitizationType);
            }
            if($info->isJinke =='1' and $info->isDeadline=='2' and empty($info->deadline)){
                return dao::$errors['deadline'] = sprintf($this->lang->infoqz->emptyObject,$this->lang->infoqz->deadline);
            }
            if(empty($info->deadline)){
                unset($info->deadline);
            }
            if($info->desensitizationType=='1' or $info->desensitizationType=='2'){
                // 是否需要脱敏被disabled掉了，补充上
                $info->isDesensitize = '1';
            }else{
                $info->isDesensitize = '0';
            }
            if($info->isDesensitize == '1' and $info->desensitizeProcess == '')
            {
                return dao::$errors['desensitizeProcess'] = $this->lang->infoqz->desensitizeProcessEmpty;
            }
           /* if(empty($_POST['consumed']))
            {
                return dao::$errors['consumed'] = $this->lang->infoqz->consumedEmpty;
            }
            $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $this->post->consumed))
            {
                dao::$errors['consumed'] = $this->lang->infoqz->consumedError;
                return false;
            }*/
            if(empty($_POST['app']))
            {
                return dao::$errors['app'] = $this->lang->infoqz->appEmpty;
            }
            $flag = false;
            foreach($this->post->problem as $problem)
            {
                if(!empty($problem)) $flag = true;
            }

            /* @var requirementModel $requirementModel*/
            $requirementModel = $this->loadModel('requirement');
            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            if(!empty($info->demand))
            {
                $deleteOutDataStr = $requirementModel->getRequirementInfos($info->demand);
            }
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->infoqz->deleteOutTip , $deleteOutDataStr);
                return false;
            }

            foreach($this->post->demand  as $demand)
            {
                if(!empty($demand))  $flag = true;
            }
            foreach($this->post->secondorderId  as $secondorder)
            {
                if(!empty($secondorder))  $flag = true;
            }
            if(!$flag) return dao::$errors[] = $this->lang->infoqz->emptyDemandProblem;

            if(!empty($_POST['desc']))
            {
                if(strlen($_POST['desc'])>200){
                    return dao::$errors['desc'] = $this->lang->infoqz->notExceed200;
                }
            }

            if(!isset($info->reason) or $info->reason == '')
            {
                return dao::$errors['reason'] = $this->lang->infoqz->gainReasonEmpty;
            }
            //用途改为必填项
            if(!isset($info->purpose) or $info->purpose == '')
            {
                return dao::$errors['purpose'] = $this->lang->infoqz->gainPurposeEmpty;
            }
        }


        //获得格式化的审核节点以及审核人信息
        $newReviewers = $this->getFormatReviewNodes($info->type, $this->post->nodes);

        //获得审核人信息变更(记录日志使用)
        $changeReviews = $this->loadModel('review')->getChangeReviewers('infoQz', $infoID, $newReviewers, $oldInfo->version);

        /* 判断是否关联了问题单或者需求单 */
        // $flag = false;

        // foreach($this->post->project as $project)
        // {
        //     if(empty($project)) return dao::$errors[] = $this->lang->infoqz->projectEmpty;
        // }
        if ($info->issubmit != 'save'){
            //检查审核信息
            $checkRes = $this->checkReviewerNodesInfo($this->post->type, $this->post->nodes);
            //有错误返回
            if(!$checkRes || dao::isError()){
                return dao::$errors;
            }

            /* 判断是否关联了问题单或者需求单 */
            $flag = false;
            foreach($this->post->problem as $problem)
            {
                if(!empty($problem)) $flag = true;
            }
            foreach($this->post->demand  as $demand)
            {
                if(!empty($demand))  $flag = true;
            }
            foreach($this->post->secondorderId as $secondorderId)
            {
                if(!empty($secondorderId))  $flag = true;
            }
            if(!$flag) return dao::$errors[] = $this->lang->info->emptyDemandProblem;

            if(empty($_POST['app']))
            {
                return dao::$errors['app'] = $this->lang->info->appEmpty;
            }

            if(!isset($info->reason) or $info->reason == '')
            {
                $errorTips = $action == 'gain' ? $this->lang->info->gainReasonEmpty : $this->lang->info->fixReasonEmpty;
                return dao::$errors['reason'] = $errorTips;
            }

            $applicationInfo = $this->loadModel('application')->getapplicationInfo();
            if(!empty($info->app)){
                $as = [];
                foreach(explode(',', $info->app) as $apptype)
                {
                    if(!$apptype) continue;
                    $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
                }
                $applicationtype = implode(',', $as);
                if(!empty($applicationtype))$applicationtype=",".$applicationtype;
                $info->isPayment=$applicationtype;
            }
            if($action == 'gain'){
                if($info->isJinke==''){
                    return dao::$errors['isJinke'] = sprintf($this->lang->info->emptyObject,$this->lang->info->isJinke);
                }
                if($info->isJinke =='1'){
                    if($info->desensitizationType==''){
                        return dao::$errors['desensitizationType'] = sprintf($this->lang->info->emptyObject,$this->lang->info->desensitizationType);
                    }
                }else{
                    unset($info->desensitizationType);
                }
                if($info->isDeadline == '2' and $info->deadline==''){
                    return dao::$errors['deadline'] = sprintf($this->lang->info->emptyObject,$this->lang->info->deadline);
                }
                if(empty($info->deadline)){
                    unset($info->deadline);
                }
            }
         /*   if(empty($_POST['consumed']))
            {
                return dao::$errors['consumed'] = $this->lang->info->consumedEmpty;
            }
            $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $_POST['consumed']))
            {
                dao::$errors['consumed'] = $this->lang->info->consumedError;
                return false;
            }*/
        }
        $info->status      = 'wait';
        if ($info->issubmit == 'save'){
            $info->status      = 'waitsubmitted';
            $info->status = in_array($oldInfo->status, ['reject', 'waitsubmitted','reviewfailed']) ? $oldInfo->status : 'waitsubmitted';
            $this->post->consumed = 0;
        }

        if('save' != $oldInfo->issubmit && ($oldInfo->status == 'reject' || $oldInfo->status == 'reviewfailed'))
        {
            $info->version     = $oldInfo->version + 1;
            $info->reviewStage = 0;
            $info->pushExternalStatus = 0; //修改后，变为未推送状态
            if($info->type != $oldInfo->type){
                $info->requiredReviewNode = '';
            }
        } else { //非打回状态
            $info->pushExternalStatus = 0; //修改后，变为未推送状态
        }
        // $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        // if(!empty($info->app)){
        //     $as = [];
        //     foreach(explode(',', $info->app) as $apptype)
        //     {
        //         if(!$apptype) continue;
        //         $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
        //     }
        //     $applicationtype = implode(',', $as);
        //     if(!empty($applicationtype))$applicationtype=",".$applicationtype;
        //     $info->isPayment=$applicationtype;
        // }
        $info->demandUnitOrDep = in_array($info->dataCollectApplyCompany,[1,2,3]) ? implode(',',$_POST['demandUnitOrDepSelect']) : $_POST['demandUnitOrDepInput'];


        $info = $this->loadModel('file')->processImgURL($info, $this->config->infoqz->editor->edit['id'], $this->post->uid);
        // 同步数据管理
        //tangfei 重新编辑后同步失败原因为空
        $info->synFailedReason = '';
        $this->dao->update(TABLE_INFO_QZ)->data($info)
            ->batchCheckIF($info->issubmit != 'save',$this->config->infoqz->edit->requiredFields, 'notempty')
            ->where('id')->eq($infoID)
            ->exec();

        if('save' != $oldInfo->issubmit && ($oldInfo->status == 'reject' || $oldInfo->status == 'reviewfailed'))
        {
            $this->submitReview($infoID, $info->version, $info->type);
        } else { //非打回状态
            $this->submitEditReview($infoID, $oldInfo->version, $info->type);
        }

        $dataManagementCode = $this->loadModel('datamanagement')->syncData($infoID,'infoqz');
        $this->dao->update(TABLE_INFO_QZ)->set('dataManagementCode')->eq($dataManagementCode)->where('id')->eq($infoID)->exec();

        /* Record the relationship between the associated issue and the requisition. todo ??????? */
        $tempAction = $oldInfo->action . $this->lang->infoqz->keywordSuffix;


        $this->loadModel('secondline')->saveRelationship($infoID, $tempAction, $info->problem, 'problem');
        $this->loadModel('secondline')->saveRelationship($infoID, $tempAction, $info->secondorderId, 'secondorder');
        $this->secondline->saveRelationship($infoID, $tempAction, $info->demand, 'demand');
        $this->secondline->saveRelationship($infoID, 'project' . ucfirst($tempAction), $info->project, 'project');

        $this->loadModel('file')->updateObjectID($this->post->uid, $infoID, 'infoQz');
        $this->file->saveUpload('infoQz', $infoID);

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('infoQz')
            ->andWhere('objectID')->eq($infoID)
            ->orderBy('id_desc')
            ->fetch();

        if('save' != $oldInfo->issubmit && ($oldInfo->status=='reject' || $oldInfo->status == 'reviewfailed')){
            $this->loadModel('consumed')->record('infoQz', $infoID, '0', $this->app->user->account, $oldInfo->status, $info->status, array());
        }else{
            $this->loadModel('consumed')->update($cs->id,'infoQz', $infoID, '0', $this->app->user->account, $oldInfo->status, $info->status, array());
        }

        //审核人信息变更
        $extChangeInfo = [];
        if($changeReviews){
            $extChangeInfo['review_node'] = $changeReviews;
        }
        return common::createChanges($oldInfo, $info, $extChangeInfo);
    }

    /**
     * Project: chengfangjinke
     * Method: close
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called close.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     */
    public function close($infoID)
    {
        $oldStatus = $this->dao->select('status')->from(TABLE_INFO_QZ)->where('id')->eq($infoID)->fetch('status');
        $data = new stdclass();
        if($this->post->result == 'closed')     $data->status = 'closed';
        if($this->post->result == 'feedbacked') $data->status = 'pass';

        $this->dao->update(TABLE_INFO_QZ)->data($data)->where('id')->eq($infoID)->exec();
        $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz');
        $this->loadModel('consumed')->record('info', $infoID, 0, $this->app->user->account, $oldStatus, $data->status, array());
    }

    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return mixed
     */
    public function getByID($infoID)
    {
        $info = $this->dao->select("*")->from(TABLE_INFO_QZ)->where('id')->eq($infoID)->fetch();
        if(empty($info)) return [];
        $info = $this->loadModel('file')->replaceImgURL($info, 'desc,purpose,operation,reason,test,step,checkList,result');
        if($info->planBegin == '0000-00-00 00:00:00' or $info->planBegin == '0000-00-00'){
            $info->planBegin = '';
        }
        if($info->planEnd == '0000-00-00 00:00:00' or $info->planEnd == '0000-00-00'){
            $info->planEnd = '';
        }
        $info->appName = '';
        $info->appTeam = '';
        if($info->app)
        {
            $info->app = trim($info->app, ',');
            $info->app = str_replace(",,",",",$info->app);
            $as = [];
            foreach(explode(',', $info->app) as $app)
            {
                if(!$app) continue;
                $as[] = $app;
            }

            if(is_numeric($as[0])){
                $apps = $this->dao->select('id,code,name')->from(TABLE_APPLICATION)->where('id')->in($as)->fetchAll();
            }else{
                $apps = $this->dao->select('distinct application as id,application as code,applicationCnName as name')
                ->from(TABLE_SYSTEM_PARTITION)
                ->where('application')->in($as)
                ->andWhere('deleted')->eq('0')
                ->fetchAll();
            }

            $this->app->loadLang('application');
            foreach($apps as $app)
            {
                $info->appName .= $app->name . ' ';
                // $info->appTeam .= $this->lang->application->teamList[$app->team] . ' ';
            }
        }
        $info->dataSystem = str_replace(",,",",",trim($info->dataSystem, ','));
        $info->reviewers = $this->loadModel('review')->getReviewer('infoQz', $infoID, $info->version, $info->reviewStage);
        if(in_array($info->status, $this->lang->infoqz->allowReject))
        {
            $info->reviewers = $this->lang->infoqz->apiDealUserList['userAccount'];
        }
        //待处理人
        $dealUsers = $this->getInfoDealUsers($info);
        $info->dealUsers = $dealUsers;

        $info->releases = [];
        if($info->release)
        {
            $releases = $this->loadModel('project')->getReleasesList($info->project);
            foreach(explode(',', $info->release) as $r)
            {
                if(!$r) continue;

                $files = $this->dao->select('*')->from(TABLE_FILE)->where('objectType')->eq('release')
                     ->andWhere('objectID')->eq($r)
                     ->andWhere('deleted')->eq(0)
                     ->fetchAll();

                $release = new stdclass();
                $release->id    = $r;
                $release->name  = $releases[$r]->name;
                $release->path  = $releases[$r]->path;
                $release->remotePathQz  = $releases[$r]->remotePathQz;
                $release->pushStatusQz  = $releases[$r]->pushStatusQz;
                $release->pushTimeQz    = $releases[$r]->pushTimeQz;
                $release->md5  = $releases[$r]->md5;
                $release->files = $files;
                if(empty($release->files))$release->files = $this->loadModel('file')->getByObject('build', $releases[$r]->build);
                $info->releases[] = $release;
            }
        }

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('infoQz')
            ->andWhere('objectID')->eq($infoID)
            ->fetchAll();
        $info->consumed = $cs;
        $this->resetNodeAndReviewerName($info->createdDept);

        return $info;
    }

    /**
     * 获得数据待处理人
     *
     * @param $info
     * @return string
     */
    public function getInfoDealUsers($info){
        $dealUsers = '';
        $status = $info->status;
        if($status == 'pass'){ //审核完毕
            $dealUsers = '';
        }else if(in_array($status, $this->lang->infoqz->allowReviewStatusList)){ //待关联版本或者待审核
            $dealUsers = $info->reviewers;
        }else if(in_array($status, $this->lang->infoqz->allowEditStatusList)){ //待编辑
            $dealUsers = $info->createdBy;
        }else if(in_array($status, $this->lang->infoqz->allowReject)){
            $dealUsers = $info->reviewers;
        }
        return $dealUsers;
    }

    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return false|void
     */
    public function review($infoID)
    {
        $info = $this->getByID($infoID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($info, $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
        $extra = new stdClass();
        /* 判断是否需要系统部评审。*/
        if($info->type == 'business'){
            if($info->reviewStage == 2) {
                if(!$this->post->isNeedSystem)
                {
                    dao::$errors['isNeedSystem'] = $this->lang->infoqz->systemEmpty;
                    return false;
                }
                $extra = $this->post->isNeedSystem == 'yes' ? true : false;
            }
        }

        $is_all_check_pass =  false;
        $postResult = $this->post->result;
        if ($postResult == 'reviewfailed'){
            $postResult = 'reject';
        }
        $result = $this->loadModel('review')->check('infoQz', $infoID, $info->version, $postResult, $this->post->comment, $info->reviewStage, $extra, $is_all_check_pass);
        //生产变更单状态需要存reviewfailed
        if ($result == 'reject'){
            $result = 'reviewfailed';
        }
        if($result == 'pass') {
            //解决时间取二线专员审核通过节点的前一个节点的处理节点时间 问题和需求条目
//            if($info->reviewStage == 6){
//                /** @var infoModel $infoModel*/
//                $infoModel =  $this->loadModel('info');
//                $infoModel->dealDemandAndProblemSolvedTime($info,'infoqz',$infoID,$info->version,$info->demand,$info->problem);
//            }

            if(!empty($info->requiredReviewNode)){
                $requiredStage = explode(',', $info->requiredReviewNode); //修改过审批节点的，以修改的为准
                if(!in_array(3,$requiredStage) and $info->type == 'business'){
                    $requiredStage[] = 3;
                    sort($requiredStage);
                }
            }else if($info->type == 'tech'){
                $requiredStage = $this->lang->infoqz->requiredReviewerList['tech'] ;  //不同表单类型的审批节点不同
            }else{
                $requiredStage = $this->lang->infoqz->requiredReviewerList['business'] ;
            }

            $afterStage = $info->reviewStage + 1;  //审批通过，自动前进一步
            while($afterStage < self::MAXNODE){
                if ( $info->type == 'business' and $afterStage == self::SYSTEMNODE and $this->post->isNeedSystem == 'no') { $afterStage += 1; }  //如果跳过系统部审批，则再前进一步
                if ( ! in_array($afterStage, $requiredStage )) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                }
                else{  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }

            if($afterStage - $info->reviewStage > 1){
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('infoQz')   //将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($info->id)
                    ->andWhere('version')->eq($info->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $info->reviewStage - 1)->exec();
            }

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('infoQz')   //查找下一节点的状态
            ->andWhere('objectID')->eq($info->id)
                ->andWhere('version')->eq($info->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('infoqz', $info->id, $info->version, $afterStage);
            }

            //更新状态
            if(isset($this->lang->infoqz->reviewBeforeStatusList[$afterStage])){
                $status = $this->lang->infoqz->reviewBeforeStatusList[$afterStage];
            }

            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_INFO_QZ)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($infoID)->exec();
            // 修改完状态后同步数据使用
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz');
            $this->loadModel('consumed')->record('infoQz', $infoID, '0', $this->app->user->account, $info->status, $status, array());

        } elseif($result == 'reviewfailed') {
            //如果单子被外部退回过，状态更新为已退回（迭代33）
            $rejectConsumed = $this->dao->select('id')
                ->from(TABLE_CONSUMED)
                ->where('objectType')->eq('infoQz')
                ->andWhere('`objectID`')->eq($infoID)
                ->andWhere('`before`')->eq('reject')
                ->andWhere('deleted')->eq('0')->fetch();
            if(!empty($rejectConsumed)){
                $result = 'reject';
            }
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_INFO_QZ)->set('status')->eq($result)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($infoID)->exec();
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz');
            $this->loadModel('consumed')->record('infoQz', $infoID, '0', $this->app->user->account, $info->status, $result, array());
        }
        //更新需求和问题解决时间
        /** @var problemModel $problemModel */
        $problemModel = $this->loadModel('problem');
//        if(!empty($info->demand)){
//            $demandIds =array_filter(explode(',',$info->demand));
//            if($demandIds){
//                foreach($demandIds as $demandId)
//                {
//                  $problemModel->getAllSecondSolveTime($demandId,'demand');
//                }
//            }
//        }
        /*if(!empty($info->problem)){
            $problemIds =array_filter(explode(',',$info->problem));
            if($problemIds){
                foreach($problemIds as $problemId)
                {
                   $problemModel->getAllSecondSolveTime($problemId,'problem');
                }
            }
        }*/
    }

    /**
     *获得审核的一下状态
     *
     * @param $type
     * @param $status
     * @param int $addStage
     * @return string|void
     */
    public function getNextReviewStatus($type, $status, $addStage = 1){
        $nextStatus = '';
        if(!($type && $status)){
            return $nextStatus;
        }
        if(isset($this->lang->infoqz->reviewStageStatusList[$type])){
            $reviewStageStatusList = $this->lang->infoqz->reviewStageStatusList[$type];
        }else{
            $reviewStageStatusList = $this->lang->infoqz->reviewStageStatusList['deaf'];
        }
        $statusPos = array_search($status, $reviewStageStatusList,true);
        if($statusPos === false){
            return $nextStatus;
        }
        $nextKey = $statusPos + $addStage;
        if(!isset($reviewStageStatusList[$nextKey])){
            return $nextStatus;
        }
        $nextStatus = $reviewStageStatusList[$nextKey];

        return $nextStatus;
    }

    /**
     *获得步长
     *
     * @param $newStatus
     * @param $oldStatus
     * @return int|void
     */
    public function getStepStage($newStatus, $oldStatus){
        $stepStage = 0;
        if(!($newStatus && $oldStatus)){
            return $stepStage;
        }
        $reviewStageStatusList = $this->lang->infoqz->reviewStageStatusList['deaf'];
        $newStatusPos = array_search($newStatus, $reviewStageStatusList,true);
        if($newStatusPos === false){
            return $stepStage;
        }
        $oldStatusPos = array_search($oldStatus, $reviewStageStatusList,true);
        if($oldStatusPos === false){
            return $stepStage;
        }
        $stepStage = $newStatusPos - $oldStatusPos;
        return $stepStage;
    }

    /**
     * Project: chengfangjinke
     * Method: feedback
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called feedback.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return array
     */
    public function feedback($infoID)
    {
        $oldInfo = $this->getByID($infoID);

        $data = fixer::input('post')->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_INFO_QZ)->data($data)->where('id')->eq($infoID)->exec();

        return common::createChanges($oldInfo, $data);
    }

    /**
     * Project: chengfangjinke
     * Method: run
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called run.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     */
    public function run($infoID)
    {
        $data = new stdClass();
        $data = fixer::input('post')
            ->join('supply', ',')
            ->remove('uid')
            ->remove('comment')
            ->remove('consumed')
            ->stripTags($this->config->infoqz->editor->run['id'], $this->config->allowedTags)
            ->get();

        if($data->actualBegin == ''){
            return dao::$errors['actualBegin'] = sprintf($this->lang->info->emptyObject,$this->lang->info->actualBegin);
        }
        if($data->actualEnd == ''){
            return dao::$errors['actualEnd'] = sprintf($this->lang->info->emptyObject,$this->lang->info->actualEnd);
        }
        if($data->supply == ''){
            return dao::$errors['supply'] = sprintf($this->lang->info->emptyObject,$this->lang->info->supply);
        }
        if($data->fetchResult=='1'){
            $data->status = 'fetchsuccess';
        }elseif($data->fetchResult=='2'){
            $data->status = 'fetchfail';
        }else{
            return dao::$errors['fetchResult'] = sprintf($this->lang->info->emptyObject,$this->lang->info->fetchResult);
        }

        if(empty($_POST['consumed']))
        {
            return dao::$errors['consumed'] = $this->lang->info->consumedEmpty;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->infoqz->consumedError;
            return false;
        }

        $data = $this->loadModel('file')->processImgURL($data, $this->config->infoqz->editor->run['id'], $this->post->uid);
        $this->dao->update(TABLE_INFO_QZ)->data($data)->where('id')->eq($infoID)->exec();
        // 修改完状态后同步数据使用
        $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz');

        $this->loadModel('file')->updateObjectID($this->post->uid, $infoID, 'infoQz');
        $this->file->saveUpload('infoQz', $infoID);

        $this->loadModel('consumed')->record('infoQz', $infoID, $this->post->consumed, $this->app->user->account, 'pass', $data->status, array());
    }

    /**
     * Project: chengfangjinke
     * Method: link
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called link.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return false|void
     */
    public function link($infoID)
    {
        $info = $this->getByID($infoID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReview($info,  $this->post->version, $this->post->reviewStage, $this->app->user->account);
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
            return false;
        }
       /* if(empty($_POST['consumed']))
        {
            return dao::$errors['consumed'] = $this->lang->infoqz->consumedEmpty;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->infoqz->consumedError;
            return false;
        }*/

        if(!$this->post->release)
        {
            $oldStatusDesc = zget($this->lang->infoqz->statusList, $info->status);
            dao::$errors[] = sprintf($this->lang->ifoqz->statusError, $oldStatusDesc);
            return false;
        }
        $status = $this->getNextReviewStatus($info->type, $info->status);
        //下一状态
        if(!$status){
            dao::$errors['release'] = $this->lang->infoqz->releaseEmpty;
            return false;
        }
        $this->loadModel('projectrelease');
        $releases = $this->projectrelease->getPaths(array_values($this->post->release));

        $config  = $this->dao->select('`key`,`value`')->from(TABLE_LANG)->where('section')->eq('mediaCheckList')->fetchPairs('key');
        if($config['link'] == 1){ //校验开关
            foreach ($releases as $release){
                if(!$this->projectrelease->checkPath($release->path, $release->name)){
                dao::$errors['release'] = dao::$errors['path'];
                }
            }
            unset(dao::$errors['path']);
            if(dao::$errors['release']){ return false; }
        }
        $data = new stdClass();
        //关联版本发布
        $data->release      = trim(implode(',', $this->post->release), ',');
        $data->reviewStage  = 1;
        $data->lastDealDate = date('Y-m-d');
        $data->status = $status;
        $this->dao->update(TABLE_INFO_QZ)->data($data)->autoCheck()->batchCheck($this->config->infoqz->link->requiredFields, 'notempty')
             ->where('id')->eq($infoID)->exec();
        //一个人审核通过就可以
        $is_all_check_pass = false;
        $this->loadModel('review')->check('infoQz', $infoID, $info->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass);

        //下一审核流程
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('infoQz')
                     ->andWhere('objectID')->eq($infoID)
                     ->andWhere('version')->eq($info->version)
                     ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next->id)->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next->id)->exec();

        $this->loadModel('consumed')->record('infoQz', $infoID, '0', $this->app->user->account, 'wait', 'cmconfirmed', array());
    }

    /**
     * Project: chengfangjinke
     * Method: isClickable
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called isClickable.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $info
     * @param $action
     * @return bool
     */
    public static function isClickable($info, $action)
    {
        global $app,$lang;
        $action = strtolower($action);
        if($info->status == 'deleted'){
            return false;
        }
        if($action == 'edit')   return (in_array($info->status,$lang->infoqz->allowEditStatusList)) and ($info->createdBy == $app->user->account or $app->user->account == 'admin');
        if($action == 'reject') return (new infoqzModel())->checkAllowReject($info) and strpos(",$info->reviewers,", ",{$app->user->account},") !== false;

        if($action == 'link')   return $info->reviewStage == 0 and strpos(",$info->reviewers,", ",{$app->user->account},") !== false and $info->issubmit == 'submit';
        if($action == 'review') return $info->reviewStage != 0 and (strpos(",$info->reviewers,", ",{$app->user->account},") !== false or $app->user->account == 'admin')
        and in_array($info->status, array(
            'cmconfirmed',
            'groupsuccess',
            'managersuccess',
            'systemsuccess',
            'posuccess',
            'leadersuccess',
        ));
        if($action == 'run')    return $info->status == 'pass';
        if($action == 'close')  return ($info->status == 'reject' and ($info->createdBy == $app->user->account or $app->user->account == 'admin')) or ($app->user->account == 'litianzi') or ($app->user->account == 'admin');
        if ($action == 'delete') return $app->user->account == 'admin' or ($app->user->account == $info->createdBy and $info->status == 'waitsubmitted' and $info->version == 1);

        return true;
    }

    /**
     *检查是否允许驳回
     *
     * @param $info
     * @return bool
     */
    public function checkAllowReject($info){
        $res = false;
        if((($info->deliveryType == 'clearCenter')&&in_array($info->status,$this->lang->infoqz->allowReject)) or in_array($info->status, $this->lang->infoqz->allowRejectStatusList)){
            $res = true;
        }
        return  $res;
    }

    /**
     * Send mail.
     *
     * @param  int    $infoID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($infoID, $actionID)
    {
        $this->loadModel('mail');
        $info  = $this->getById($infoID);
        if ($info->issubmit == 'save'){
            return false;
        }
        $users = $this->loadModel('user')->getPairs('noletter');

        //待处理人用户名
        $dealUsersStr = '';
        $dealUsers = $info->dealUsers;
        if($dealUsers){
            $dealUsersArray = explode(',', $dealUsers);
            //所有审核人
            $dealUsers    = getArrayValuesByKeys($users, $dealUsersArray);
            $dealUsersStr = implode(',', $dealUsers);
        }
        $info->dealUsersStr = $dealUsersStr;
        $this->app->loadLang('infoqz');
        //流程状态
        $info->statusDesc = zget($this->lang->infoqz->statusList, $info->status);

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $browseType = $info->action;
        $confObject = 'set' . ucwords($browseType) .'Qz'. 'Mail';
        $mailConf   = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('infoQz')
            ->andWhere('objectID')->eq($infoID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        //是否显示外部状态审核状态
        $isShowExternalStatus = false;
        //外部操作日志动作
        $externalActionArray = [
            'syncstatus', 'editstatus',
        ];

        if(in_array($action->action, $externalActionArray)){
            $isShowExternalStatus = true;
            $info->externalStatusDesc = zget($this->lang->infoqz->externalStatusList, $info->externalStatus);
        }

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'infoqz');
        $viewFile   = $modulePath . 'view/sendmail.html.php';
        chdir($modulePath . 'view');

        if(file_exists($modulePath . 'ext/view/sendmail.html.php'))
        {
            $viewFile = $modulePath . 'ext/view/sendmail.html.php';
            chdir($modulePath . 'ext/view');
        }

        ob_start();
        include $viewFile;
        foreach(glob($modulePath . 'ext/view/sendmail.*.html.hook.php') as $hookFile) include $hookFile;
        $mailContent = ob_get_contents();
        ob_end_clean();

        chdir($oldcwd);
        if(in_array($info->status, $this->lang->infoqz->allowReject))
        {
            $info->reviewers = $this->lang->infoqz->apiDealUserList['userAccount'];
        }else{
            $sendUsers = $this->getToAndCcList($info, $isShowExternalStatus);
        }
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        // 抄送产品经理
        if($info->status == 'leadersuccess'){
            $appIds = explode(",", trim($info->app,","));
            if(!empty($appIds)){
                $POList = $this->dao->select('PO')->from(TABLE_PRODUCT)->where('app')->in($appIds)->fetchall();
                if(!empty($POList)){
                    $ccList = $ccList.','.implode(',', array_column($POList, 'PO'));
                }
            }
        }

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($info);
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:44
     * Desc: This is the code comment. This method is called getToAndCcList.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $object
     * @param $isShowExternalStatus
     * @return array
     */
    public function getToAndCcList($object, $isShowExternalStatus = false)
    {
        /* Set toList and ccList. */
        $ccList = '';
        if($isShowExternalStatus){
            $toList = $object->createdBy;  //创建者
            //获得实际审核人
            $stageIds = [2,3,5,7];
            $realReviewers = $this->loadModel('review')->getRealReviewers('infoQz', $object->id, $object->version, $stageIds);
            /*
            //部门信息
            $createdDept = $object->createdDept;
            $myDept = $this->loadModel('dept')->getByID($createdDept);

            //组长、部门领导、产品经理、二线专员
            //申请部门组长审批
            $groupUsers = [];
            if(isset($myDept->groupleader)){
                $groupUsers = explode(',', trim($myDept->groupleader, ','));
            }

            // 部门负责人
            $managerUsers = [];
            if(isset($myDept->manager)){
                $managerUsers = explode(',', trim($myDept->manager, ','));
            }

            // 产品经理
            $poUsers = [];
            if(isset($myDept->po)){
                $poUsers = explode(',', trim($myDept->po, ','));
            }

            // 产创部二线专员
            $executiveUsers = [];
            if(isset($myDept->executive)){
                $executiveUsers = explode(',', trim($myDept->executive, ','));
            }
            $toUsers = array_merge($realReviewers, $groupUsers, $managerUsers, $poUsers, $executiveUsers);
            $toUsers = array_flip(array_flip($toUsers));
            */
            $toUsers = array_flip(array_flip($realReviewers));
            //抄送人
            $ccList = implode(',', $toUsers);

        }else{
            $toList = $this->getInfoDealUsers($object);
        }
        return array($toList, $ccList);
    }

    /**
     * Get mail subject.
     *
     * @param  object
     * @access public
     * @return string
     */
    public function getSubject($object)
    {
        $common = $object->action == 'fix' ? $this->lang->infoqz->commonFix : $this->lang->infoqz->commonGain;
        return $common  . '#' . $object->id . '-' . $object->code;
    }

    /**
     * Project: chengfangjinke
     * Method: checkAllowReview
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/02/23
     * Time: 14:44
     * Desc: 检查信息是否允许当前用户审核.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $info
     * @param $info
     * @param $version
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($info, $version = 1, $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$info){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $info->version) || ($reviewStage != $info->reviewStage)  || ($info->status == 'reject')){
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('infoQz', $info->id, $version, $reviewStage);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('infoQz', $info->id, $info->version, $info->reviewStage);
        if(!$reviews){
            $res['message'] = $this->lang->review->reviewEnd;
            return $res;
        }
        $reviews = explode(',', $reviews);
        if(!in_array($userAccount, $reviews)){
            $res['message'] = $this->lang->review->statusUserError;
            return $res;
        }
        $res['result'] = true;
        return  $res;
    }


    /**
     *通过外部清算中心id查询数据信息
     *
     * @param $externalId
     * @param string $select
     * @return array
     */
    public function getInfoByCode($code, $select = '*')
    {
        $data = new stdClass();
        if(!$code){
            return $data;
        }
        $ret =  $this->dao->select($select)->from(TABLE_INFO_QZ)->where('code')->eq($code)->fetch();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

   /**
     * 接口更新数据获取/数据修正
    *
     * @param $infoID
     * @return array
     */
    public function updateByApi($infoID){
        $userAccount = $this->lang->infoqz->apiDealUserList['userAccount']; // 默认 'litianzi';
        $oldInfo = $this->getByID($infoID);
        //获得post信息
        $info = fixer::input('post')
            ->get();
        
        //实际开始时间
        if(isset($info->actualBegin)){
            $info->actualBegin = date('Y-m-d H:i:s', substr($info->actualBegin, 0, 10));
        }
        //实际结束时间
        if(isset($info->actualEnd)){
            $info->actualEnd = date('Y-m-d H:i:s', substr($info->actualEnd, 0, 10));
        }
        //外部状态
        $info->externalStatus = zget($this->lang->infoqz->externalStatusMapArray, $info->externalStatus, '');
        $info->editedBy   = $userAccount;
        $info->editedDate = date('Y-m-d');
        $info->status     = zget($this->lang->infoqz->externalStatusToStats,$info->externalStatus);
        $info->reviewFailReason = $this->getHistoryReview((object)array_merge((array)$oldInfo, (array)$info));
        //修改数据记录信息
        $this->dao->update(TABLE_INFO_QZ)->data($info)->autoCheck()
            ->where('id')->eq($infoID)
            ->exec();
        $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz','guestcn');
        $this->loadModel('consumed')->record('infoQz', $infoID, 0, 'guestcn', 'withexternalapproval', $info->status, array());

        return common::createChanges($oldInfo, $info);
    }

    /**
     * 获取允许推送的数据ids
     *
     * @param int $infoID
     * @return array
     */
    public function getAllowPushInfoIds($infoID = 0){
        $data = [];
        $ret = $this->dao->select('id')
            ->from(TABLE_INFO_QZ)
            ->where('status')->eq('pass') //审核完毕待上线
            ->andWhere('deliveryType')->eq('clearCenter')
            ->andWhere('pushExternalStatus')->in($this->lang->infoqz->allowPushExternalStatusArray)
            ->beginIF($infoID > 0)->andWhere('id')->eq($infoID)->fi()
            ->fetchPairs();
        if(!$ret){
            return $data;
        }
        $data = $ret;
        return $data;
    }

    /**
     * 推送消息到接口
     * 修改后这里只处理介质标记推送
     * @param $infoID
     * @return bool
     */
    public function pushInfo($infoID)
    {
        if (!$infoID) {
            return dao::$errors[] = $this->lang->idEmpty;
        }
        //查询信息
        $info = $this->getByID($infoID);
        if (empty($info)) {
            return dao::$errors[] = $this->lang->infoqzEmpty;
        }
        //判断是否允许推送
        $res = $this->checkAllowPush($info);
        if (!$res) {
            return dao::$errors[] = $this->lang->infoqz->notAllowPush;
        }
        //标记需要推送介质
        $this->dao->update(TABLE_RELEASE)->set('pushStatusQz')->eq(1)->where('id')->in(explode(',', trim($info->release,',')))->andwhere('pushStatusQz')->eq(0)->exec();
        $actionID = $this->loadModel('action')->create('infoqz', $infoID, "pushMedia", '', '', 'guestjk');

        return true;
    }

    /**
     * TongYanQi 2022/11/4
     * 以前的是直接发送单子，改版后由轮训触发 判断是否已经推送介质
     */
    public function doPushInfo($infoID){
        if(!$infoID){
            return false;
        }
        $info = $this->getByID($infoID);
        //查询信息
        if(empty($info)){
            return false;
        }
        //判断是否允许推送
        $res = $this->checkAllowPush($info);
        if(!$res){
            return false;
        }
        $pushFailMax = $this->loadModel('release')->getFailsQz($info->release);
        if($pushFailMax){ //推送失败
            $updateParams = new stdClass();
            $updateParams->synFailedReason = '介质推送失败多次';
            $updateParams->status          = 'qingzongsynfailed';
            $this->dao->update(TABLE_INFO_QZ)->data($updateParams)
                ->where('id')->eq($infoID)
                ->exec();
            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'infoqzsynfailed', '', '', 'guestjk');
            $this->loadModel('consumed')->record('infoQz', $infoID, 0, 'guestjk', 'pass', 'qingzongsynfailed', array());
            return false;
        }
        if($this->loadModel('release')->ifReleasesPushed($info->release) == false) {  return false; } //介质未处理完 不推单子

        //推送到清算中心
        $url           = $this->config->global->pushInfoGainUrl;
        $pushAppId     = $this->config->global->pushInfoGainAppId;
        $pushAppSecret = $this->config->global->pushInfoGainAppSecret;
        $pushUsername  = $this->config->global->pushInfoGainUsername;
        $fileIP        = $this->config->global->pushInfoGainFileIP;

        $headers = array();
        $headers[] = 'App-Id: ' . $pushAppId;
        $headers[] = 'App-Secret: ' . $pushAppSecret;

        $deptList = $this->loadModel('dept')->getOptionMenu();
        $userInfo = $this->loadmodel('user')->getUserInfo($info->createdBy, 'realname');
        //关联版本附件
        $relationFiles = array();
        $downloadUrl = $this->config->global->downloadIP; //'http://172.22.67.22/api.php?m=api&f=download&sign=%s&filename=%s';
        if(!empty($info->releases)) {
            foreach ($info->releases as $release) {
                $filename2push = rtrim(end(explode('/', $release->path)), ' ');
                array_push($relationFiles, array('url' => $release->remotePathQz, 'md5' => $release->md5, 'fileName' => $filename2push));
            }
        }
        //节点名称
        $nodeNameList = $this->getInfoNodeNameList($info->isNPC, $info->node);
        //数据类型
        $itemTypeName = $this->getInfoItemTypeName($info->action, $info->type);

        $pushData = array();
        $pushData['businessSystemIdList']       = trim($info->app, ','); //下拉选项	系统名称英文名称
        $pushData['dataSystemIdList']           = trim($info->dataSystem, ','); //下拉选项	数据所属业务系统
        $pushData['itemTypeName']               = $itemTypeName; //数据类型
        $pushData['getDataNum']                 = $info->code; //数据单号
        //$pushData['itemKey']                   = $info->externalId; //第三方返回的单号
        $pushData['systemName']                 = trim($info->app, ',');  //下拉选项	系统名称
        $pushData['nodeName']                   = implode(',' , $nodeNameList); //下拉选项	节点名称
        $pushData['operationType']              = ltrim($info->classify, ','); //下拉选项	数据类别(需要新增文字表述和代号？？？？)
        $pushData['getDataType']                = zget($this->lang->infoqz->apiTypeList, $info->type, ''); //下拉选项	数据类型 (需要新增文字表述和代号？？？？)
        $pushData['dataContent']                = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->content,ENT_QUOTES))))); //文本	数据内容
        $pushData['applicationDepartment']      = zget($deptList, $info->createdDept, ''); //申请人部门
        $pushData['applicant']                  = $userInfo->realname ?? ""; //申请人
        $pushData['applyUsercontact']           = $info->createUserPhone; //申请人联系方式
        $pushData['proposedOperationStartDate'] = strtotime($info->planBegin)*1000; //拟操作开始时间
        $pushData['proposedOperationEndDate']   = strtotime($info->planEnd)*1000; //拟操作结束时间
        $pushData['reasonForApplication']       = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->reason,ENT_QUOTES))))); //文本	申请原因
        $pushData['applicationPurpose']         = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->purpose,ENT_QUOTES))))); //文本 数据获取用途
        $pushData['testSituation']              = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->test,ENT_QUOTES))))); //文本测试情况
        $pushData['whetherTest']                = zget($this->lang->infoqz->isTestList, $info->isTest, '');  //单选：是/否	是否通过测试
        $pushData['relationFiles']              = $relationFiles; //附件
        $pushData['systemType']                 = zget($this->lang->infoqz->systemTypeList, $info->systemType, ''); //下拉选项：交易系统、信息系统	系统分类
        //$pushData['dataCollectDepart']        = ''; //数据获取部门(默认传空就行)
        $pushData['dataCollectSource']          = zget($this->lang->infoqz->gainTypeList, $info->gainType, ''); //下拉选项： 客户端获取、后台获取 数据获取渠道
        $pushData['operationContent']           = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->operation,ENT_QUOTES))))); //文本	操作内容
        $pushData['dataMaskRequire']            = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->desensitization,ENT_QUOTES))))); //文本	数据脱敏要求
        $pushData['operationSteps']             = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->step,ENT_QUOTES))))); //文本	操作步骤
        $pushData['dataCollectSummary']         = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->desc,ENT_QUOTES))))); //文本	数据摘要
        $pushData['isNpc']                      = zget($this->lang->infoqz->isNPCList, $info->isNPC, ''); //是否isNpc
        $pushData['isCountersign']              = '否'; //是否会签
//        $pushData['isExposeToOuter']            = '否'; //是否交付单位
        $pushData['isDesensitize']              = $info->isDesensitize; //是否脱敏
        $pushData['desensitizeProcess']            = html_entity_decode(strip_tags(str_replace("<br />","\n",strval(htmlspecialchars_decode($info->desensitizeProcess,ENT_QUOTES)))));//脱敏步骤
        //需求收集4653
        $pushData['dataCollectApplyCompany']    = zget($this->lang->infoqz->demandUnitTypeList,$info->dataCollectApplyCompany); //需求单位或部门类型
        $pushData['demandUser']                 = $info->demandUser; //需求人
        $pushData['demandUserPhone']            = $info->demandUserPhone; //需求人电话
        $pushData['demandUserEmail']            = $info->demandUserEmail; //需求人邮箱
        $pushData['portUser']                   = $info->portUser; //接口人
        $pushData['portUserPhone']              = $info->portUserPhone; //接口人电话
        $pushData['portUserEmail']              = $info->portUserEmail; //接口人邮箱
        $pushData['supportUser']                = $info->supportUser; //支持人
        $pushData['supportUserPhone']           = $info->supportUserPhone; //电话
        $pushData['supportUserEmail']           = $info->supportUserEmail; //邮箱

        $demandUnitDeptList = $this->getDemandUnitDeptList();
        if (in_array($info->dataCollectApplyCompany,[1,2,3])){
            $arr = [];
            foreach (explode(',',$info->demandUnitOrDep) as $item) {
                $arr[] = zget($demandUnitDeptList,$item,'');
            }
            $pushData['demandUnitOrDep'] = implode(',',$arr);
        }else{
            $pushData['demandUnitOrDepInput']       = $info->demandUnitOrDep; //需求单位或部门
        }

        $apps = $this->loadModel('application')->getapplicationNameCodePairsWithPartition();
        $app1 = [];
        foreach (explode(',',$pushData['businessSystemIdList']) as $item1){
            $app1[] = zget($apps,$item1,'');
        }
        $pushData['businessSystemNameList']     = implode(',',$app1);
        $app2 = [];
        foreach (explode(',',$pushData['dataSystemIdList']) as $item2){
            $app2[] = zget($apps,$item2,'');
        }
        $pushData['dataSystemNameList']     = implode(',',$app2);

        $object     = 'info'.ucfirst($info->action);
        $objectType = 'syncToCenter';
        $response = '';
        $status = 'fail';
        $extra  = '';

        //默认推送状失败
        $pushExternalStatus = 2;
        if (!empty($pushData['relationFiles'])){
            foreach ($pushData['relationFiles'] as $relationFile) {
                if ($relationFile['md5'] == ''){
                    $res = ['status'=>'fail','msg'=>'MD5值不能为空'];
                    $this->loadModel('requestlog')->saveRequestLog($url, $object, $objectType, 'POST', $pushData, json_encode($res), $status, $extra);
                    return 'md5empty';
                }
            }
        }
        $updateParams = new stdClass();
        $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
        if (!empty($result)) {
            $resultData = json_decode($result);
            if (isset($resultData->code) and $resultData->code == '200') {
                $data = $resultData->data;
                $status = 'success';
                $pushExternalStatus = 1; //推送成功
                $updateParams->status = 'withexternalapproval';
                if((isset($data)) && (isset($data->objectId))){
                    $updateParams->externalId = $data->objectId;
                }
                //记录日志
                $action = 'sync';
                if($info->externalId){
                    $action = 'update';
                }
                $actionID = $this->loadModel('action')->create('infoqz', $infoID, $action, '', '', 'guestjk');
                $this->loadModel('consumed')->record('infoQz', $infoID, 0, 'guestjk', 'pass', 'withexternalapproval', array());
            }else{
                if(empty($resultData->message)) $resultData->message = '无';
                $updateParams->synFailedReason = $resultData->message;
                $updateParams->status          = 'qingzongsynfailed';
            }
            $response = $result;
        }else{
            $updateParams->synFailedReason = '无';
            $updateParams->status          = 'qingzongsynfailed';
        }
        $this->loadModel('datamanagement')->syncDataStatus($infoID,'infoqz','guestcn');
        $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, $extra);

        //修改数据获取或者数据更正信息
        $updateParams->pushExternalStatus = $pushExternalStatus;
        $this->dao->update(TABLE_INFO_QZ)->data($updateParams)->autoCheck()
            ->where('id')->eq($infoID)
            ->exec();

        if($pushExternalStatus != 1){ //推送失败
            //tangfei 发送邮件
            $actionID = $this->loadModel('action')->create('infoqz', $infoID, 'infoqzsynfailed', '', '', 'guestjk');
            $this->loadModel('consumed')->record('infoQz', $infoID, 0, 'guestjk', 'pass', 'qingzongsynfailed', array());
            return false;
        }
        return true;
    }

    /**
     * 获得节点名称
     *
     * @param $isNpc
     * @param $node
     * @return array
     */
    public function getInfoNodeNameList($isNpc, $node){
        $data = [];
        if(!($isNpc && $node)){
            return $data;
        }
        $nodeMapList = $this->getNodeListByIsNPC($isNpc);
        $nodes = explode(',', $node);
        foreach ($nodes as $nodeId){
            if($nodeId){
                $nodeName = zget($nodeMapList, $nodeId, '');
                if($nodeName){
                    $data[] = $nodeName;
                }
            }
        }
        return $data;
    }

    /**
     *获取数据分类名称
     *
     * @param $action
     * @param $type
     * @return string|void
     */
    public function getInfoItemTypeName($action, $type){
        $infoTypeName = '';
        if(!($action && $type)){
            return $infoTypeName;
        }
        $infoTypeName = zget($this->lang->infoqz->typeList, $type);
        $actionDesc = '获取';
        if($action == 'fix'){
            $actionDesc = '修正';
        }
        $infoTypeName .= $actionDesc;
        return $infoTypeName;
    }


    /**
     * 获得消息是否允许推送
     *
     * @param $info
     * @return false
     */
    public function checkAllowPush($info){
        $res = false;
        $status = $info->status;
        $deliveryType = $info->deliveryType;
        $pushExternalStatus = $info->pushExternalStatus;
        if($status == 'pass' && $deliveryType == 'clearCenter' && in_array($pushExternalStatus, $this->lang->infoqz->allowPushExternalStatusArray)){
            $res = true;
        }
        return $res;
    }

    public function  getNodeListByIsNPC($isNPC)
    {
        if($isNPC == '1')
        {
            $result=$this->lang->infoqz->gainNodeNPCList;
        }else if ($isNPC == '2')
        {
            $result=$this->lang->infoqz->gainNodeCNCCList;
        }else{
            $result=array();
        }
        return $result;
    }

    public function  getClassifyByType($type)
    {

        if($type == 'tech')
        {
            $result=$this->lang->infoqz->techList;
        }else if ($type == 'business')
        {
            $result=$this->lang->infoqz->businessList;
        }else{
            $result=array();
        }
        return $result;
    }

    /**
     * 获得数据信息
     *
     * @param $infoID
     * @param string $select
     * @return stdClass
     */
    public function getSearchInfoByID($infoID, $select = '*'){
        $data = new stdClass();
        $info = $this->dao->select($select)->from(TABLE_INFO_QZ)->where('id')->eq($infoID)->fetch();
        if($info){
            $data = $info;
        }
        return $data;
    }


    /**
     * Project: chengfangjinke
     * Method: review
     * User: Tony Stark
     * Year: 2021
     * Date: 2022/05/28
     * Time: 14:44
     * Desc: This is the code comment. This method is called review.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @return false|void
     */
    public function reject($infoID)
    {
        $info = $this->getByID($infoID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReject($info);
        if(!$res){
            dao::$errors['statusError'] = $this->lang->infoqz->rejectError;
            return false;
        }
        if(!$this->post->revertReason)
        {
            dao::$errors['revertReason'] = $this->lang->infoqz->revertReasonEmpty ;
            return false;
        }
        $comment = trim($this->post->comment);
        if(!$comment){
            dao::$errors['statusError'] = $this->lang->infoqz->rejectCommentEmpty;
            return false;
        }
        $skipReviewNodes = array_keys($this->post->skipReviewNode);
        $requiredReviewNode = '';
        if($this->post->skipReviewNode){
            $requiredReviewNode = implode(',', $skipReviewNodes);
        }
        $lastDealDate = date('Y-m-d');
        $revertReasonOld = $info->revertReason;
        if(empty($revertReasonOld)){
            $revertReasonArray = array();
        }else{
            $revertReasonArray = json_decode($revertReasonOld);
        }
        $status = 'reject';
        //内部审核节点退回记为审核未通过，外部记为已退回
        if (in_array($info->status,$this->lang->infoqz->reviewrejectStatus)){
            $status = 'reviewfailed';
        }
        $revertReasonArray[]=array('RevertDate'=>helper::now(),'RevertReason'=>$this->post->revertReason,'RevertReasonChild'=>$this->post->revertReasonChild);
        $revertReason = json_encode($revertReasonArray);
        //保存外部失败原因
        //$reviewFailReason = $this->getHistoryReview($info);
        $this->dao->update(TABLE_INFO_QZ)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)
        ->set('requiredReviewNode')->eq($requiredReviewNode)
        ->set('revertReason')->eq($revertReason)
        //->set('reviewFailReason')->eq($reviewFailReason)
        ->where('id')->eq($infoID)->exec();
        $this->loadModel('consumed')->record('infoqz', $infoID, $this->post->consumed, $this->app->user->account, $info->status, $status, array());
        //忽略节点
        $ret = $this->loadModel('review')->setReviewNodesIgnore('infoqz', $infoID, $info->version);

        //新建关联二线，解决时间置空
//        /** @var problemModel $problemModel*/
//        $problemModel = $this->loadModel('problem');
//        if(!empty($info->demand)){
//            $problemModel->dealSolveTime($info->demand,'demand',$info->code,true);
//        }
        /*if(!empty($info->problem)){
            $problemModel->dealSolveTime($info->problem,'problem',$info->code,true);
        }*/
        return true;
    }

    /**
     *检查审核节点的审核人
     *
     * @param $level
     * @param $nodes
     * @param array $skipReviewNode
     * @return false
     */
    public function checkReviewerNodesInfo($type, $nodes){
        //检查结果
        $checkRes = true;
        $requiredReviewerKeys = [];
        if(isset($this->lang->infoqz->requiredReviewerList[$type])){
            $requiredReviewerKeys = $this->lang->infoqz->requiredReviewerList[$type];
        }
        $nodeKeys = array();
        foreach($nodes as $key => $currentNodes)
        {
            //去除空元素
            $currentNodes = array_filter($currentNodes);
            if(!empty($currentNodes))
            {
                $nodeKeys[] = $key;
            }
        }
        //必选审核人，却没有选
        $diffKeys = array_diff($requiredReviewerKeys, $nodeKeys);
        if(!empty($diffKeys)){
            foreach ($diffKeys as  $nodeKey){
                $nodeDesc = $this->lang->infoqz->reviewerList[$nodeKey];
                dao::$errors[] =  sprintf($this->lang->infoqz->nodereviewerEmpty, $nodeDesc);
                break;
            }
        }

        if(dao::isError()){
            $checkRes = false;
        }
        return $checkRes;
    }
    /**
     * 下载文件签名
     * @param $filename
     * @return int
     */
    public function getSign($filename)
    {
        return $this->loadModel('downloads')->getSign($filename);
    }

    public function getUnPushedAndPush()
    {
        $unPushedInfoIds = $this->dao->select('*')->from(TABLE_INFO_QZ)->where('status')->eq('pass')->andwhere('deliveryType')->eq('clearCenter')->andwhere('pushExternalStatus')->in($this->lang->infoqz->allowPushExternalStatusArray)->fetchALl('id');  //选取没推送成功的产品登记

        if(empty($unPushedInfoIds)) return [];

        $res = [];
        foreach ($unPushedInfoIds as $id => $item) {
            $res =  $this->doPushInfo($id);
        }
        return $res;
    }

    public function pushDestroyedData(){
        $destroyed = $this->dao->select('ti.id,ti.isDeadline,ti.desensitizationType,ti.type,ti.code,ti.isSyncDestroyed,td.status as tdStatus')->from(TABLE_INFO_QZ)->alias('ti')
        ->leftJoin(TABLE_DATAUSE)->alias('td')
        ->on('ti.code=td.infoCode')
        ->where('ti.isSyncDestroyed')->in(array('0','2'))//0：未同步，1：已同步销毁，2：已同步暂未销毁，3：已同步不需要销毁;
        ->andwhere('ti.status')->ne('deleted')
        ->andwhere('ti.isJinke')->eq('1') // 由于某些数据会改是否进入金科
        ->andwhere('ti.externalId')->ne('')
        ->fetchAll();
        $url           = $this->config->global->destroyInfoGainUrl;
        $pushAppId     = $this->config->global->destroyInfoGainAppId;
        $pushAppSecret = $this->config->global->destroyInfoGainAppSecret;
        $headers = array();
        $headers[] = 'App-Id: ' . $pushAppId;
        $headers[] = 'App-Secret: ' . $pushAppSecret;
        $object     = 'infoGain';
        $objectType = 'syncDestroyToCenter';

        $res = [];
        foreach($destroyed as $item){
            $status = 'fail';
            $pushData = array();
            if($item->isSyncDestroyed=='0'){
                // 未同步过数据
                if($item->desensitizationType==1 or $item->isDeadline==1){
                    // 全部脱敏或长期的数据同步不需要销毁
                    $pushData['destoryStatus'] = '不需销毁';
                }else{
                    // 其他数据根据状态同步
                    $pushData['destoryStatus'] = $item->tdStatus == 'destroyed' ? '已销毁' : '暂未销毁';
                }
            }elseif($item->isSyncDestroyed=='2'){
                if($item->tdStatus == 'destroyed'){
                    // 已经同步暂未销毁的，此次变更为已销毁
                    $pushData['destoryStatus'] = '已销毁';
                }else{
                    // 已同步暂未销毁，且无变化
                    continue;
                }
            }            
            $pushData['itemTypeName']  = $this->lang->infoqz->outTypeList[$item->type];
            $pushData['getDataNum']    = $item->code;
            
            $response = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
            if (!empty($response)) {
                $resultData = json_decode($response);
                if (isset($resultData->code) and $resultData->code == '200') {
                    $status = 'success';
                    $isSyncDestroyed = $pushData['destoryStatus']=='已销毁'?1:($pushData['destoryStatus']=='暂未销毁'?2:3);   
                    //记录日志
                    $run['response'] = $response;
                    $this->dao->update(TABLE_INFO_QZ)->set('isSyncDestroyed')->eq($isSyncDestroyed)
                    ->where('id')->eq($item->id)
                    ->exec();
                    $this->loadModel('action')->create('infoqz', $item->id, 'syncDestory', '', $pushData['destoryStatus'], 'guestjk');
                }
            }
            $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $response, $status, '');
            $res[] = $run;
        }
        return $res;
    }

    /**
     * @Notes:获取金信数据获取数据，用于状态联动
     * @Date: 2023/4/13
     * @Time: 16:33
     * @Interface getEffectiveModifyData
     * @param $id
     * @return mixed
     */
    public function getEffectiveInfoQzData($id){
        return $this->dao->select('id,code,`status`, externalStatus, actualEnd, version, reviewStage')
            ->from(TABLE_INFO_QZ)
            ->where('id')
            ->eq($id)
            ->andwhere('action')
            ->eq('gain')
            ->andWhere('status')->notIN("closed,fetchclose,fetchcancel") //已关闭、数据获取关闭、数据获取取消不做联动
            ->fetch();
    }
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info  = $this->getByID($objectID);
        if ($info->issubmit == 'save'){
            return ['isSend'=>'no'];
        }
        $users = $this->loadModel('user')->getPairs('noletter');

        //待处理人用户名
        $dealUsersStr = '';
        $dealUsers = $info->dealUsers;
        if($dealUsers){
            $dealUsersArray = explode(',', $dealUsers);
            //所有审核人
            $dealUsers    = getArrayValuesByKeys($users, $dealUsersArray);
            $dealUsersStr = implode(',', $dealUsers);
        }
        $info->dealUsersStr = $dealUsersStr;
        $this->app->loadLang('infoqz');
        //流程状态
        $info->statusDesc = zget($this->lang->infoqz->statusList, $info->status);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        //是否显示外部状态审核状态
        $isShowExternalStatus = false;
        //外部操作日志动作
        $externalActionArray = [
            'syncstatus', 'editstatus',
        ];

        if(in_array($action->action, $externalActionArray)){
            $isShowExternalStatus = true;
            $info->externalStatusDesc = zget($this->lang->infoqz->externalStatusList, $info->externalStatus);
        }

        if(in_array($info->status, $this->lang->infoqz->allowReject))
        {
            $info->reviewers = $this->lang->infoqz->apiDealUserList['userAccount'];
        }else{
            $sendUsers = $this->getToAndCcList($info, $isShowExternalStatus);
        }
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        $url = '';
        $subcontent = [];
        $subcontent['headTitle']    = '';
        $subcontent['headSubTitle'] = '';

        $subcontent['count']       = 0;
        $subcontent['id']       = 0;
        $subcontent['parent']       = '';
        $subcontent['parentURL']    = "";
        $subcontent['cardURL']      = $url;
        $subcontent['name']      = '';//消息体 编号后边位置 标题
        //标题
        $title = '';
        $actions = [];

        $browseType = $info->action;
        $confObject = 'set' . ucwords($browseType) .'Qz'. 'Mail';
        $mailConf   = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>$mailConf];
    }

    /**
     * @param $info
     */
    public function getHistoryReview($info){
        $historyReview = [];
        //数据获取-同步清总
        $jkResult = "";
        if(in_array($info->status,array('pass','qingzongsynfailed')) ){
            $jkResult = zget($this->lang->infoqz->statusList, $info->status, '');
        }
        elseif(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'))){
            $jkResult = $this->lang->infoqz->synSuccess;
        }
        $jkReason = '';
        if(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'))){
            $jkReason = $this->lang->infoqz->synSuccessMsg;
        }
        elseif($info->status=='qingzongsynfailed'){
            $jkReason = $info->synFailedReason;
        }
        if($jkResult != ''){
            $historyReview['guestjk'] = [
                'reviewNode'        => 'guestjk',
                'reviewUser'        => 'guestjk',
                'reviewResult'      => $jkResult,
                'reviewFailReason'  => $jkReason,
                'date'              => helper::now()
            ];
        }
        $cnResult = '';
        if(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'))){
            $cnResult = zget($this->lang->infoqz->statusList,$info->status);
        }
        $cnReason = '';
        if (in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel')))
        {
            $cnReason = "打回人：".$info->approverName."<br>"."审批意见：".$info->externalRejectReason;
        }
        if ($cnResult != ''){
            $historyReview['guestcn'] = [
                'reviewNode'        => 'guestcn',
                'reviewUser'        => 'guestcn',
                'reviewResult'      => $cnResult,
                'reviewFailReason'  => $cnReason,
                'date'              => helper::now()
            ];
        }
        $reviewFailReason = json_decode($info->reviewFailReason,true);

        $reviewFailReason[$info->version][] = $historyReview;
        return json_encode($reviewFailReason);
    }
    //判断form表单提交数据
    public function checkFormData(){
        $fileds = ['demandUser','demandUserPhone','demandUserEmail','portUser','portUserPhone','portUserEmail','supportUser','supportUserPhone','supportUserEmail','dataSystem','planBegin','planEnd'];

        if ($_POST['issubmit'] == 'submit') {
            if (!isset($_POST['dataCollectApplyCompany']) || (int)$_POST['dataCollectApplyCompany'] <= 0) {
                return dao::$errors['dataCollectApplyCompany'] = sprintf($this->lang->infoqz->emptyObject, $this->lang->infoqz->dataCollectApplyCompany);
            }
            if (in_array($_POST['dataCollectApplyCompany'], [1, 2, 3]) && (!isset($_POST['demandUnitOrDepSelect']) || (int)$_POST['demandUnitOrDepSelect'] <= 0)) {
                return dao::$errors['demandUnitOrDepSelect'] = sprintf($this->lang->infoqz->emptyObject, $this->lang->infoqz->demandUnitOrDep);
            }
            if(!in_array($_POST['dataCollectApplyCompany'],[1,2,3]) && $_POST['demandUnitOrDepInput'] == ''){
                return dao::$errors['demandUnitOrDepInput'] = sprintf($this->lang->infoqz->emptyObject, $this->lang->infoqz->demandUnitOrDep);
            }
            foreach ($fileds as $filed) {
                if ($_POST[$filed] == ''){
                    return dao::$errors[$filed] = sprintf($this->lang->infoqz->emptyObject, $this->lang->infoqz->{$filed});
                }
            }
            if ($_POST['planBegin'] >= $_POST['planEnd']){
                return dao::$errors['planBegin'] = $this->lang->infoqz->timeError;
            }
        }
    }
    //获取需求单位枚举值
    public function getDemandUnitDeptList(){
        $list = $this->lang->infoqz->demandUnitList1 + $this->lang->infoqz->demandUnitList2 + $this->lang->infoqz->demandUnitList3;
        $infos = $this->dao->select('demandUnitOrDep')->from(TABLE_INFO_QZ)
            ->where('action')->eq('gain')
            ->andWhere('status')->ne('deleted')
            ->andWhere('demandUnitOrDep')->notin(array_keys($list))
            ->groupby('demandUnitOrDep')
            ->fetchpairs('demandUnitOrDep');
        $list += $infos;
        return $list;
    }
    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){

            $this->lang->infoqz->reviewerList['5']    = '上海分公司领导';

            $this->lang->infoqz->reviewNodeList['5']  = '上海分公司领导';
        }

    }
}
