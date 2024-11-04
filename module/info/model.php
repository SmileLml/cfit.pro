<?php
class infoModel extends model
{
    const MAXNODE           = 7;   //审批节点最大值是7
    const SYSTEMNODE        = 3;   //系统部审批节点，可跳过
    const CEONODE           = 6;   //系统部审批节点，可跳过

    /**
     * Project: chengfangjinke
     * Method: getList
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 14:43
     * Desc: This is the code comment. This method is called getList.
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
        $infoQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : ''; 
            if($query)
            {
                $this->session->set('infoQuery', $query->sql);
                $this->session->set('infoForm', $query->form);
            }

            if($this->session->infoQuery == false) $this->session->set('infoQuery', ' 1 = 1');

            $infoQuery = $this->session->infoQuery;
            if(strpos($infoQuery, '`secondorderId`') !== false)
            {
                $infoQuery = str_replace('`secondorderId`', "CONCAT(',', `secondorderId`, ',')", $infoQuery);
            }
            // 处理受影响的业务系统搜索字段
            if(strpos($infoQuery, '`app`') !== false)
            {
                $infoQuery = str_replace('`app`', "CONCAT(',', `app`, ',')", $infoQuery);
            }

            // 处理[系统分类]搜索字段
            if(strpos($infoQuery, '`isPayment`') !== false)
            {
                $infoQuery = str_replace('`isPayment`', "CONCAT(',', `isPayment`, ',')", $infoQuery);
            }

            // 处理[执行节点]搜索字段
            if(strpos($infoQuery, '`node`') !== false)
            {
                $infoQuery = str_replace('`node`', "CONCAT(',', `node`, ',')", $infoQuery);
            }

            // 处理[支持人员]搜索字段
            if(strpos($infoQuery, '`supply`') !== false)
            {
                $infoQuery = str_replace('`supply`', "CONCAT(',', `supply`, ',')", $infoQuery);
            }

            // 处理[数据类别]搜索字段
            if(strpos($infoQuery, '`classify`') !== false)
            {
                $infoQuery = str_replace('`classify`', "CONCAT(',', `classify`, ',')", $infoQuery);
            }
            if (strpos($infoQuery, 'deadline') ){
                if (strpos($infoQuery, "`deadline` = '长期'")) {
                    $infoQuery = str_replace('`deadline`', '`isJinke` = 1 AND isDeadline', $infoQuery);
                    $infoQuery = str_replace('长期', '1', $infoQuery);
                }else{
                    $addQuery = " `isDeadline` = 2 AND `deadline`";
                    $infoQuery =  str_replace("`useDeadline`", $addQuery, $infoQuery);
                }
            }
            //退回原因（子项）搜索 json
            if (strpos($infoQuery, 'revertReason') ){
                $queryData = explode('AND',$infoQuery);
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
                $infoQuery = implode('AND',$queryData);
            }
        }

        $infos = $this->dao->select('*')->from(TABLE_INFO)
            ->where('action')->eq($action)
            ->andWhere('status')->ne('deleted')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($infoQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'info', $browseType != 'bysearch');

        $accountList = array();
        $this->loadModel('review');
        foreach($infos as $key => $info)
        {
            $infos[$key]->reviewers = $this->review->getReviewer('info', $info->id, $info->version, $info->reviewStage);
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
     * 获得数据待处理人
     *
     * @param $info
     * @return string
     */
    public function getInfoDealUsers($info){
        $dealUsers = '';
        $status = $info->status;
        if($status == 'productsuccess'){ //审核完毕
            $dealUsers = $info->createdBy;
        }else if(in_array($status, $this->lang->info->allowReviewStatusList)){ //待关联版本或者待审核
            $dealUsers = $info->reviewers;
        }else if(in_array($status, $this->lang->info->allowEditStatusList)){ //待编辑
            $dealUsers = $info->createdBy;
        }else if($status == 'fetchfail'){ //获取失败
            $dealUsers = $info->createdBy;
        }else if($status == 'closing'){ //待关闭-待处理人为二线
            $this->app->loadLang('modify');
            $secondLineReviewList = implode(",",array_keys($this->lang->modify->secondLineReviewList));
            $dealUsers = $secondLineReviewList;
        }
        return $dealUsers;
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
        $this->config->info->search['actionURL'] = $actionURL;
        $this->config->info->search['queryID']   = $queryID;
        $this->config->info->search['params']['createdBy']['values'] = array('' => '') + $this->loadModel('user')->getPairs('noletter|noclosed');
        $this->config->info->search['params']['secondorderId']['values']   = array('' => '') + $this->dao->select('id,concat(concat(concat(code,"（"),summary),"）")')->from(TABLE_SECONDORDER)->where('deleted')->ne('1')->fetchPairs();

        $this->config->info->search['params']['revertReason']['values']= array('' => '');
        $revertReasonList = $this->lang->info->revertReasonList;
        $childTypeList = json_decode($this->lang->info->childTypeList['all'],true);
        foreach($revertReasonList as $key=>$value){
            $this->config->info->search['params']['revertReason']['values'] += array(base64_encode('"RevertReason":"'.$key.'"')=>$value);   //退回原因为json格式，不能只匹配key值

        }

        foreach ($childTypeList as $k=>$v) {
            foreach ($v as $vk=>$vv){
                $this->config->info->search['params']['revertReason']['values'] += array(base64_encode('"RevertReasonChild":"'.$vk.'"')=>$revertReasonList[$k].'-'.$vv);   //退回原因为json格式，不能只匹配key值
            }
        }

        $this->loadModel('search')->setSearchParams($this->config->info->search);
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
        $cms = explode(',', trim($myDept->cm, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        //申请部门组长审批
        $groupUsers = explode(',', trim($myDept->groupleader, ','));
        $us = array('' => '');
        if(!empty($groupUsers)){
            foreach($groupUsers as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers[] = $us;

        // 部门负责人
        $cms = explode(',', trim($myDept->manager, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 系统部
        $sysDept = $this->dao->select('id,manager')->from(TABLE_DEPT)->where('name')->eq('系统部')->fetch();
        $cms = explode(',', trim($sysDept->manager, ','));
        $us = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 产品经理
        $cms = explode(',', trim($myDept->po, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 部门分管领导
        $cms = explode(',', trim($myDept->leader, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 总经理
        $reviewer = $this->dao->select('account')->from(TABLE_USER)->where('role')->eq('ceo')->fetch('account');
        $reviewers[] = array($reviewer => $users[$reviewer]);

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
     */
    public function submitReview($infoID, $version, $type, $action = 'gain')
    {
        $myDept = $this->loadModel('dept')->getByID($this->app->user->dept);
        $status = 'pending';
        $stage = 1;

        $nodes = $this->post->nodes;

        if($action == 'gain'){
            if($type == 'tech'){
                $nodes[3] = [];
                $nodes[5] = [];
            }
            $nodes[6] = [];    //去掉总经理
        }
        $nodes[4] = [];    //去掉产品经理节点
        //从小到大排序
        ksort($nodes);
        //保存不校验必填，节点没有选择审核人员会造成数据错乱
        for ($i = 0;$i <= 7;$i++){
            if(!isset($nodes[$i])){
                $nodes[$i] = [];
            }
        }
        ksort($nodes);
        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('info', $infoID, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }

    /**
     * Project: chengfangjinke
     * Method: submitEditReview
     * User: wangjiurong
     * Year: 2022
     * Date: 2022/2/15
     * Time: 15:43
     * Desc: 提交编辑审核信息.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $infoID
     * @param $version
     * @return bool
     */

    public function submitEditReview($infoID, $version,$type,$action){
        $objectType = 'info';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $infoID, $version );
        $nodes = $this->post->nodes;
        if($action == 'gain'){
            if($type == 'tech'){
                $nodes[3] = [];
                $nodes[5] = [];
            }
            $nodes[6] = [];    //去掉总经理
        }
        $nodes[4] = [];    //去掉产品经理节点
        
        ksort($nodes);
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
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $data = fixer::input('post')
            ->join('classify', ',')
            ->join('problem', ',')
            ->join('demand', ',')
            ->join('secondorderId', ',')
            ->join('node', ',')
            ->join('app', ',')
            ->join('project', ',')
            ->remove('consumed')
            ->remove('nodes,uid')
            ->stripTags($this->config->info->editor->create['id'], $this->config->allowedTags)
            ->get();
        $status = 'wait';
        //保存的增加待提交状态
        if ($data->issubmit == 'save'){
            $status = 'waitsubmitted';
        }
        // 判断已关闭的问题单不可被关联
        if($data->problem){
            $problemIds = array_filter(explode(',', $data->problem));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problem'] = $res['msg'];
                return false;
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

        if($data->planBegin == ''){
            unset($data->planBegin);
        }
        if($data->planEnd == ''){
            unset($data->planEnd);
        }
        if($data->isDeadline == '2' and ($data->deadline=='' || $data->deadline=='0000-00-00')){
            return dao::$errors[] = sprintf($this->lang->info->emptyObject,$this->lang->info->deadline);
        }

        if ($data->issubmit != 'save'){
            //检查审核信息
            $checkRes = $this->checkReviewerNodesInfo($data->type, $this->post->nodes);
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

            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            $deleteOutDataStr = $requirementModel->getRequirementInfos($data->demand);
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->info->deleteOutTip , $deleteOutDataStr);
                return false;
            }

            foreach($this->post->demand as $demand)
            {
                if(!empty($demand)) $flag = true;
            }
            foreach($this->post->secondorderId as $secondorderId)
            {
                if(!empty($secondorderId))  $flag = true;
            }
            if(!$flag) return dao::$errors[] = $this->lang->info->emptyDemandProblem;

           /* if(empty($_POST['consumed']))
            {
                return dao::$errors['consumed'] = $this->lang->info->consumedEmpty;
            }
            $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
            if (!preg_match($reg, $this->post->consumed))
            {
                dao::$errors['consumed'] = $this->lang->info->consumedError;
                return false;
            }*/

            if(!isset($data->classify) or trim($data->classify, ',') == '')
            {
                return dao::$errors['classify'] = $this->lang->info->classifyEmpty;
            }

            if(empty($data->app) or trim($data->app, ',') == '')
            {
                return dao::$errors['app'] = $this->lang->info->appEmpty;
            }

            if(!isset($data->reason) or $data->reason == '')
            {
                $errorTips = $action == 'gain' ? $this->lang->info->gainReasonEmpty : $this->lang->info->fixReasonEmpty;
                return dao::$errors['reason'] = $errorTips;
            }

            if(!isset($data->step) or $data->step == '')
            {
                $errorTips = $action == 'gain' ? $this->lang->info->gainStepEmpty : $this->lang->info->fixStepEmpty;
                return dao::$errors['step'] = $errorTips;
            }

            if($action == 'fix' and (!isset($data->operation) or $data->operation == ''))
            {
                return dao::$errors['operation'] = $this->lang->info->operationEmpty;
            }
            if($action == 'gain'){
                if($data->isJinke==''){
                    return dao::$errors['isJinke'] = sprintf($this->lang->info->emptyObject,$this->lang->info->isJinke);
                }
                if($data->isJinke =='1'){
                    if($data->desensitizationType==''){
                        return dao::$errors['desensitizationType'] =sprintf($this->lang->info->emptyObject,$this->lang->info->desensitizationType);
                    }
                }else{
                    unset($data->desensitizationType);
                }
                if(empty($data->deadline)){
                    unset($data->deadline);
                }
            }
        }
        $applicationInfo = $this->loadModel('application')->getapplicationInfo();
        if(!empty($data->app)){
            $as = [];
            foreach(explode(',', $data->app) as $apptype)
            {
                if(!$apptype) continue;
                $as[] = zget($applicationInfo, $apptype,"",$applicationInfo[$apptype]->isPayment);
            }
            $applicationtype = implode(',', $as);
            if(!empty($applicationtype))$applicationtype=",".$applicationtype;
            $data->isPayment=$applicationtype;
        }
        $data = $this->loadModel('file')->processImgURL($data, $this->config->info->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_INFO)->data($data)
            ->batchCheckIF($data->issubmit == 'submit',$this->config->info->create->requiredFields, 'notempty')
            ->exec();
        $infoID = 0;

        if(!dao::isError())
        {
            $infoID = $this->dao->lastInsertId();
            $this->loadModel('file')->updateObjectID($this->post->uid, $infoID, 'info');
            $this->file->saveUpload('info', $infoID);

            $this->submitReview($infoID, 1, $data->type);

            /* Record the relationship between the associated issue and the requisition. */
            $this->loadModel('secondline')->saveRelationship($infoID, $action, $data->problem, 'problem');
            $this->secondline->saveRelationship($infoID, $action, $data->demand, 'demand');
            $this->secondline->saveRelationship($infoID, 'project' . ucfirst($action), $data->project, 'project');

            $date   = date('Y-m-d');
            $number = $this->dao->select('count(id) c')->from(TABLE_INFO)->where('createdDate')->ge($date.' 00:00:00')->andWhere('action')->eq($action)->fetch('c');
            $type   = $action == 'gain' ? 'GJ' : 'FJ';
            $code   = "CFIT-$type-" . date('Ymd-') . sprintf('%02d', $number);

            $this->dao->update(TABLE_INFO)->set('code')->eq($code)->where('id')->eq($infoID)->exec();

            $this->loadModel('consumed')->record('info', $infoID, '0', $this->app->user->account, '', $status, array());
            // 同步数据管理
            $dataManagementCode = $this->loadModel('datamanagement')->syncData($infoID,'info');
            $this->dao->update(TABLE_INFO)->set('dataManagementCode')->eq($dataManagementCode)->where('id')->eq($infoID)->exec();

            //新建关联二线，解决时间置空
//             /** @var problemModel $problemModel*/
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
        /* @var requirementModel $requirementModel*/
        $requirementModel = $this->loadModel('requirement');
        $oldInfo = $this->getByID($infoID);
        $action = $oldInfo->action;
        $info = fixer::input('post')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', helper::now())
            ->join('classify', ',')
            ->join('problem', ',')
            ->join('demand', ',')
            ->join('secondorderId', ',')
            ->join('node', ',')
            ->join('app', ',')
            ->join('project', ',')
            ->remove('uid,files,labels,comment,nodes,consumed')
            ->stripTags($this->config->info->editor->edit['id'], $this->config->allowedTags)
            ->setIF($this->post->demand == '', 'demand', '')
            ->setIF($this->post->secondorderId == '', 'secondorderId', '')
            ->get();
        // 判断已关闭的问题单不可被关联
        if($info->problem){
            $problemIds = array_filter(explode(',', $info->problem));
            $res = $this->loadModel('problem')->checkIsClosed($problemIds);
            if (!$res['result']){
                dao::$errors['problem'] = $res['msg'];
                return false;
            }
        }
        $info->project     = trim($info->project, ',');

        if (!in_array($oldInfo->status,$this->lang->info->allowEditStatusList)){
            return dao::$errors[] = $this->lang->info->editStatusError;
        }
        if($info->planBegin == ''){
            unset($info->planBegin);
        }
        if($info->planEnd == ''){
            unset($info->planEnd);
        }
        if($info->isDeadline == '2' and ($info->deadline=='' || $info->deadline=='0000-00-00')){
            return dao::$errors[] = sprintf($this->lang->info->emptyObject,$this->lang->info->deadline);
        }

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
            //关联需求条目 需判断条目所属需求任务是否外部已删除，如果外部已删除则不允许关联
            $deleteOutDataStr = $requirementModel->getRequirementInfos($this->post->demand);
            if(!empty($deleteOutDataStr))
            {
                dao::$errors[] = sprintf($this->lang->info->deleteOutTip , $deleteOutDataStr);
                return false;
            }

            foreach($this->post->demand  as $demand)
            {
                if(!empty($demand)) $flag = true;
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
                if(empty($info->deadline)){
                    unset($info->deadline);
                }
            }
           /* if(empty($_POST['consumed']))
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
            $info->status = in_array($oldInfo->status, ['reject', 'waitsubmitted']) ? $oldInfo->status : 'waitsubmitted';
            $this->post->consumed = 0;
        }

        if('save' != $oldInfo->issubmit && ($oldInfo->status == 'reject' or $oldInfo->status == 'fetchfail'))
        {
            $info->version     = $oldInfo->version + 1;
            $info->reviewStage = 0;
            if($info->type != $oldInfo->type){
                $info->requiredReviewNode = '';
            }
        }
        $info = $this->loadModel('file')->processImgURL($info, $this->config->info->editor->edit['id'], $this->post->uid);
        // 同步数据管理
        $this->dao->update(TABLE_INFO)->data($info)
            ->batchCheckIF($info->issubmit != 'save',$this->config->info->edit->requiredFields, 'notempty')
            ->where('id')->eq($infoID)
            ->exec();

        $dataManagementCode = $this->loadModel('datamanagement')->syncData($infoID,'info');
        $this->dao->update(TABLE_INFO)->set('dataManagementCode')->eq($dataManagementCode)->where('id')->eq($infoID)->exec();

        if('save' != $oldInfo->issubmit && ($oldInfo->status == 'reject' or $oldInfo->status == 'fetchfail'))
        {
            $this->submitReview($infoID, $info->version, $info->type, $action);
        } else //非打回状态
        {
            $this->submitEditReview($infoID, $oldInfo->version,$info->type, $action);
        }

        /* Record the relationship between the associated issue and the requisition. */
        $this->loadModel('secondline')->saveRelationship($infoID, $oldInfo->action, $info->problem, 'problem');
        $this->secondline->saveRelationship($infoID, $oldInfo->action, $info->demand, 'demand');
        $this->secondline->saveRelationship($infoID, 'project' . ucfirst($oldInfo->action), $info->project, 'project');

        $this->loadModel('file')->updateObjectID($this->post->uid, $infoID, 'info');
        $this->file->saveUpload('info', $infoID);

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('info')
        ->andWhere('objectID')->eq($infoID)
        ->orderBy('id_desc')
        ->fetch();

        if('save' != $oldInfo->issubmit && ($oldInfo->status=='reject' or $oldInfo->status=='fetchfail' or ($cs->before == ''and $cs->after == 'waitsubmitted'))){
            $this->loadModel('consumed')->record('info', $infoID, $this->post->consumed, $this->app->user->account, $oldInfo->status, $info->status, array());
        }else{
            $this->loadModel('consumed')->update($cs->id,'info', $infoID, $this->post->consumed, $this->app->user->account, $oldInfo->status, $info->status, array());
        }

        return common::createChanges($oldInfo, $info);
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
        $oldStatus = $this->dao->select('status')->from(TABLE_INFO)->where('id')->eq($infoID)->fetch('status');
        $data = new stdclass();
        if($this->post->result == 'closed')     $data->status = 'closed';
        if($this->post->result == 'feedbacked') $data->status = 'productsuccess';

        $this->dao->update(TABLE_INFO)->data($data)->where('id')->eq($infoID)->exec();
        $this->loadModel('datamanagement')->syncDataStatus($infoID,'info');
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
        $info = $this->dao->select("*")->from(TABLE_INFO)->where('id')->eq($infoID)->fetch();
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
            $as = [];
            foreach(explode(',', $info->app) as $app)
            {
                if(!$app) continue;
                $as[] = $app;
            }

            $apps = $this->dao->select('name,team')->from(TABLE_APPLICATION)->where('id')->in($as)->fetchAll();
            $this->app->loadLang('application');
            foreach($apps as $app)
            {
                $info->appName .= $app->name . ' ';
                $info->appTeam .= isset($this->lang->application->teamList[$app->team]) ? $this->lang->application->teamList[$app->team]. ' <br/>' : '' ;
            }
        }
        $info->reviewers = $this->loadModel('review')->getReviewer('info', $infoID, $info->version, $info->reviewStage);
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
                $release->files = $files;

                $info->releases[] = $release;
            }
        }

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('info')
            ->andWhere('objectID')->eq($infoID)
            ->fetchAll();
        $info->consumed = $cs;
        $this->resetNodeAndReviewerName($info->createdDept);
        return $info;
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
            if($info->reviewStage == 2)
            {
                if(!$this->post->isNeedSystem)
                {
                    dao::$errors['isNeedSystem'] = $this->lang->info->systemEmpty;
                    return false;
                }
                $extra = $this->post->isNeedSystem == 'yes' ? true : false;
            }
        }

        /* 判断是否需要总经理评审。*/
        if($info->reviewStage == 5)
        {
            if($info->action == 'gain'){
                $this->post->isNeedCEO = 'no';
            }
            if(!$this->post->isNeedCEO)
            {
                dao::$errors['isNeedCEO'] = $this->lang->info->ceoEmpty;
                return false;
            }
            $extra = $this->post->isNeedCEO == 'yes' ? true : false;
        }
        $is_all_check_pass =  false;
        $result = $this->loadModel('review')->check('info', $infoID, $info->version, $this->post->result, $this->post->comment, $info->reviewStage, $extra, $is_all_check_pass);
        if($result == 'pass')
        {
            /*//解决时间取二线专员审核通过节点的前一个节点的处理节点时间 问题和需求条目
            if($info->reviewStage == 7){
                $this->dealDemandAndProblemSolvedTime($info,'info',$infoID,$info->version,$info->demand,$info->problem);
            }*/

            if(!empty($info->requiredReviewNode)){
                $requiredStage = explode(',', $info->requiredReviewNode); //修改过审批节点的，以修改的为准
                if(!in_array(3,$requiredStage) and $info->type == 'business'){
                    $requiredStage[] = 3;
                    sort($requiredStage);
                }
                
            }else if($info->type == 'tech'){
                $requiredStage = $this->lang->info->requiredTechReviewerList;  //不同表单类型的审批节点不同
            }else{
                $requiredStage = $this->lang->info->requiredBusinessReviewerList;
            }

            $afterStage = $info->reviewStage + 1;  //审批通过，自动前进一步
            while($afterStage < self::MAXNODE){
                if ( $info->type == 'business' and $afterStage == self::SYSTEMNODE and $this->post->isNeedSystem == 'no') { $afterStage += 1; }  //如果跳过系统部审批，则再前进一步
                if ( $afterStage == self::CEONODE and $this->post->isNeedCEO == 'no') { $afterStage += 1; }  //如果跳过总经理审批，则再前进一步
                if ( ! in_array($afterStage, $requiredStage )) {  //如果跳过后的节点仍然跳过，继续前进
                    $afterStage += 1;
                }
                else{  //如果节点不用继续跳过，则跳出循环
                    break;
                }
            }

            if($afterStage - $info->reviewStage > 1){
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('ignore')->where('objectType')->eq('info')   //将跳过的节点，更新为ignore
                ->andWhere('objectID')->eq($info->id)
                    ->andWhere('version')->eq($info->version)
                    ->andWhere('status')->eq('wait')->orderBy('stage,id')->limit($afterStage - $info->reviewStage - 1)->exec();
            }

            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('info')   //查找下一节点的状态
            ->andWhere('objectID')->eq($info->id)
                ->andWhere('version')->eq($info->version)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');

            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();  //更新下一节点的状态为pending
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();

                $this->loadModel('review');
                $reviewers = $this->review->getReviewer('info', $info->id, $info->version, $afterStage);
            }

            //更新状态
            if(isset($this->lang->info->reviewBeforeStatusList[$afterStage])){
                $status = $this->lang->info->reviewBeforeStatusList[$afterStage];
            }

            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_INFO)->set('reviewStage')->eq($afterStage)->set('status')->eq($status)->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($infoID)->exec();
            // 修改完状态后同步数据使用
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'info'); 
            $this->loadModel('consumed')->record('info', $infoID, '0', $this->app->user->account, $info->status, $status, array());

        }
        elseif($result == 'reject')
        {
            $lastDealDate = date('Y-m-d');
            $this->dao->update(TABLE_INFO)->set('status')->eq('reject')->set('lastDealDate')->eq($lastDealDate)->where('id')->eq($infoID)->exec();
            // 修改完状态后同步数据使用
            $this->loadModel('datamanagement')->syncDataStatus($infoID,'info'); 
            $this->loadModel('consumed')->record('info', $infoID, '0', $this->app->user->account, $info->status, 'reject', array());
        }
        //更新需求和问题解决时间   迭代三十一点五  内外部需求池同步取消
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
     * @Notes:解决时间取二线专员审核通过节点的前一个节点的处理节点时间 问题和需求条目
     * @Date: 2023/3/20
     * @Time: 18:21
     * @Interface dealDemandAndProblemSolvedTime
     * @param $info
     * @param $objectType
     * @param $infoId
     * @param $updateDemandId
     * @param $updateProblemId
     * @param $version
     */
    public function dealDemandAndProblemSolvedTime($info,$objectType,$infoId,$version,$updateDemandId,$updateProblemId)
    {
        /**
         * @var demandModel $demandModel
         * @var reviewModel $reviewModel
         * @var problemModel $problemModel
         */
        $demandModel = $this->loadModel('demand');
        $reviewModel = $this->loadModel('review');
        $problemModel = $this->loadModel('problem');
        $i = $info->reviewStage;
        for ($i; $i>0; $i--){
            $reviewNodeStatus = $reviewModel->getReviewInfoByStage($objectType, $infoId,$version,$i,'id,status');
            if($reviewNodeStatus->status == 'pass'){
                $nodeId = $reviewNodeStatus ->id;
                $reviewers = $this->dao->select('id,status,reviewTime')->from(TABLE_REVIEWER)->where('node')->eq($nodeId)->fetchAll();
                $reviewTime = array_column($reviewers,'reviewTime');
                $solvedTime = max($reviewTime);
                if(!empty($updateDemandId)){
                    $demandModel->updateDemandSolvedTime($updateDemandId,$solvedTime);
                }
                if(!empty($updateProblemId)){
                    $problemModel->updateProblemSolvedTime($updateProblemId,$solvedTime);
                }
                break;
            }
        }
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
        $oldProblem = $this->getByID($infoID);

        $data = fixer::input('post')->get();
        $data->status = 'feedbacked';
        $this->dao->update(TABLE_INFO)->data($data)->where('id')->eq($infoID)->exec();

        return common::createChanges($oldProblem, $data);
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
        $info = $this->getByID($infoID);
        $data = fixer::input('post')
            ->join('supply', ',')
            ->remove('uid')
            ->remove('comment')
            ->remove('consumed')
            ->stripTags($this->config->info->editor->run['id'], $this->config->allowedTags)
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
        if($info->action == 'gain'){
            if($data->fetchResult=='1'){
                $data->status = 'fetchsuccess';
            }elseif($data->fetchResult=='2'){
                $data->status = 'fetchfail';
            }else{
                return dao::$errors['fetchResult'] = sprintf($this->lang->info->emptyObject,$this->lang->info->fetchResult); 
            }
        }else{
            $data->status = 'closing';
        }

        /*if(empty($_POST['consumed']))
        {
            return dao::$errors['consumed'] = $this->lang->info->consumedEmpty;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->info->consumedError;
            return false;
        }*/

        $data = $this->loadModel('file')->processImgURL($data, $this->config->info->editor->run['id'], $this->post->uid);
        $this->dao->update(TABLE_INFO)->data($data)->where('id')->eq($infoID)->exec();
        // 修改完状态后同步数据使用
        $this->loadModel('datamanagement')->syncDataStatus($infoID,'info'); 

        $this->loadModel('file')->updateObjectID($this->post->uid, $infoID, 'info');
        $this->file->saveUpload('info', $infoID);

        $this->loadModel('consumed')->record('info', $infoID, $this->post->consumed, $this->app->user->account, 'productsuccess', $data->status, array());
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


        if(!$this->post->release)
        {
            dao::$errors['release'] = $this->lang->info->releaseEmpty; 
            return false;
        }

       /* if(empty($_POST['consumed']))
        {
            return dao::$errors['consumed'] = $this->lang->info->consumedEmpty;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->info->consumedError;
            return false;
        }*/

        $data = new stdClass();
        //关联版本发布
        $data->release      = trim(implode(',', $this->post->release), ',');
        $data->reviewStage  = 1;
        $data->status       = 'cmconfirmed'; //待组长审核
        $data->lastDealDate = date('Y-m-d');

        $this->dao->update(TABLE_INFO)->data($data)->autoCheck()->batchCheck($this->config->info->link->requiredFields, 'notempty')
             ->where('id')->eq($infoID)->exec();
        //一个人审核通过就可以
        $is_all_check_pass = false;
        $this->loadModel('review')->check('info', $infoID, $info->version, 'pass', $this->post->comment, 0, null, $is_all_check_pass);

        //下一审核流程
        $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('info')
                     ->andWhere('objectID')->eq($infoID)
                     ->andWhere('version')->eq($info->version)
                     ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch();
        $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next->id)->exec();
        $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next->id)->exec();

        $this->loadModel('consumed')->record('info', $infoID, '0', $this->app->user->account, 'wait', 'cmconfirmed', array());
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
        global $app;
        $action = strtolower($action);
        //单子删除后，所有按钮不可见
        if($info->status == 'deleted'){
            return false;
        }
        if($action == 'edit')   return ($info->status == 'wait' or $info->status == 'reject' or $info->status == 'fetchfail' or $info->status == 'waitsubmitted') and ($info->createdBy == $app->user->account or $app->user->account=='admin');
        if($action == 'reject') {
            $res = (new infoModel())->checkAllowReject($info);
            return  $res;
        }
        if($action == 'submit')   return $info->issubmit == 'submit' && $info->status == 'waitsubmitted' and ($info->createdBy == $app->user->account or $app->user->account=='admin');
        if($action == 'link')   return $info->issubmit == 'submit' && $info->reviewStage == 0 and strpos(",$info->reviewers,", ",{$app->user->account},") !== false;
        if($action == 'review') return $info->reviewStage != 0 and strpos(",$info->reviewers,", ",{$app->user->account},") !== false;
        if($action == 'run')    return $info->status == 'productsuccess' and ($info->createdBy == $app->user->account or $app->user->account=='admin');
        // if($action == 'close' )  return $info->status == 'fetchsuccess' or $info->status == 'fetchfail';
        if ($action == 'delete') return $app->user->account == 'admin' or ($app->user->account == $info->createdBy and $info->status == 'waitsubmitted' and $info->version == 1);

        // if($action == 'delete' and $info->closed != 1) return false;
        return true;
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

        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $browseType = $info->action;
        $confObject = 'set' . ucwords($browseType) . 'Mail';
        $mailConf   = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';
        $mailConf   = json_decode($mailConf);

        /* 处理邮件发信的标题和日期。*/
        $bestDeal = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('info')
            ->andWhere('objectID')->eq($infoID)
            ->orderBy('id_desc')
            ->fetch();
        $bestDate  = empty($bestDeal) ? '' : substr($bestDeal->createdDate, 0, 10);
        $mailTitle = vsprintf($mailConf->mailTitle, $mailConf->variables);

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'info');
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

        $sendUsers = $this->getToAndCcList($info);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        // 抄送产品经理
        if($info->status == 'gmsuccess'){
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
     * @return array
     */
    public function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $status = $object->status;
        if($status == 'reject'){
            $toList = $object->createdBy;  //创建者
        }else{
            $toList = $this->loadModel('review')->getReviewer('info', $object->id, $object->version, $object->reviewStage);
        }
        $ccList = '';

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
        $common = $object->action == 'fix' ? $this->lang->info->commonFix : $this->lang->info->commonGain;
        return $common  . '#' . $object->id . '-' . $object->code;
    }

    public function checkReviewerNodesInfo($type, $nodes){
        
        //检查结果
        $checkRes = true;
        $requiredReviewerKeys = [];
        if(isset($this->lang->info->requiredReviewerList[$type])){
            $requiredReviewerKeys = $this->lang->info->requiredReviewerList[$type];
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
                $nodeDesc = $this->lang->info->reviewerList[$nodeKey];
                dao::$errors[] =  sprintf($this->lang->info->nodereviewerEmpty, $nodeDesc);
                break;
            }
        }

        if(dao::isError()){
            $checkRes = false;
        }
        return $checkRes;
    }

    /**
     * Project: chengfangjinke
     * Method: getToAndCcList
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
            $reviewerInfo = $this->loadModel('review')->getReviewedUserInfo('info', $info->id, $version, $reviewStage);
            $message = $this->lang->review->statusError;
            if($reviewerInfo){
                $message = str_replace('%', $reviewerInfo->realname, $this->lang->review->statusError);
            }
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('info', $info->id, $info->version, $info->reviewStage);
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
     *检查是否允许驳回
     *
     * @param $info
     * @return bool
     */
    public function checkAllowReject($info){
        $res = false;
        if(in_array($info->status, $this->lang->info->allowRejectStatusList)){
            $res = true;
        }
        return  $res;
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
    public function reject($infoID){
        $info = $this->getByID($infoID);
        //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
        $res = $this->checkAllowReject($info);
        if(!$res){
            dao::$errors['statusError'] = $this->lang->info->rejectError;
            return false;
        }
        if(!$this->post->revertReason)
        {
            dao::$errors['revertReason'] = $this->lang->info->revertReasonEmpty ;
            return false;
        }
       /* if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = $this->lang->info->consumedEmpty;
            return false;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->info->consumedError;
            return false;
        }*/
        $comment = trim($this->post->comment);
        if(!$comment){
            dao::$errors['statusError'] = $this->lang->info->rejectCommentEmpty;
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
        $revertReasonArray[]=array('RevertDate'=>helper::now(),'RevertReason'=>$this->post->revertReason,'RevertReasonChild'=>$this->post->revertReasonChild);
        $revertReason = json_encode($revertReasonArray);
        $this->dao->update(TABLE_INFO)->set('status')->eq('reject')->set('lastDealDate')->eq($lastDealDate)
        ->set('requiredReviewNode')->eq($requiredReviewNode)
        ->set('revertReason')->eq($revertReason)
        ->where('id')->eq($infoID)->exec();
        $this->loadModel('consumed')->record('info', $infoID, '0', $this->app->user->account, $info->status, 'reject', array());
        //忽略节点
        $ret = $this->loadModel('review')->setReviewNodesIgnore('info', $infoID, $info->version);

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
     * @Notes: 获取金信数据获取数据，用于状态联动
     * @Date: 2023/4/13
     * @Time: 11:28
     * @Interface getEffectiveInfoData
     * @param $id
     */
    public function getEffectiveInfoData($id){
        return $this->dao->select('id, `status`, actualEnd, reviewStage, version,code')
            ->from(TABLE_INFO)
            ->where('id')->eq($id)
            ->andwhere('action')->in('gain')
            ->andwhere('status')->notIN('closed,deleted') //已关闭不做联动
            ->fetch();
    }
    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $info  = $obj;
        if ($info->issubmit == 'save'){
            return ['isSend'=>'no'];
        }
        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        $sendUsers = $this->getToAndCcList($info);
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
        $confObject = 'set' . ucwords($browseType) . 'Mail';
        $mailConf   = isset($this->config->global->$confObject) ? $this->config->global->$confObject : '{"mailTitle":"","variables":[],"mailContent":""}';

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>$mailConf];
    }
    /**
     * @param int $id 部门id
     * 修改上海分公司节点名称
     */
    public function resetNodeAndReviewerName($id=0){
        $depts = $this->loadModel('dept')->getRecurveSubDeptIds(30);
        if ((in_array($this->app->user->dept,$depts) &&  in_array($this->app->getMethodName(),['create','copy'])) || (in_array($id,$depts) && !in_array($this->app->getMethodName(),['create','copy']))){

            $this->lang->info->reviewerList['5'] = '上海分公司领导';

            $this->lang->info->reviewNodeList['5'] = '上海分公司领导';
        }

    }
}
