<?php
class copyrightModel extends model
{
  public function buildSearchForm($queryID, $actionURL)
  {
      $this->config->copyright->search['actionURL'] = $actionURL;
      $this->config->copyright->search['queryID']   = $queryID;
      $applications = $this->loadModel('application')->getapplicationNameCodePairs();
      $this->config->copyright->search['params']['system']['values']   = array('' => '') + $applications;
      $depts = $this->loadModel('dept')->getOptionMenu();
      $depts[0] = '';
      $this->config->copyright->search['params']['createdDept']['values'] = $depts;

      $this->loadModel('search')->setSearchParams($this->config->copyright->search);
  }

  public function getList($browseType, $queryID, $orderBy, $pager = null)
  {
      $copyrightQuery = '';
      if($browseType == 'bysearch')
      {
          $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
          if($query)
          {
              $this->session->set('copyrightQuery', $query->sql);
              $this->session->set('copyrightForm', $query->form);
          }

          if($this->session->copyrightQuery == false) $this->session->set('copyrightQuery', ' 1 = 1');

          $copyrightQuery = $this->session->copyrightQuery;

      }

      $copyright = $this->dao->select('*')->from(TABLE_COPYRIGHT)
          ->where('deleted')->eq('0')
          ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
          ->beginIF($browseType == 'bysearch')->andWhere($copyrightQuery)->fi()
          ->orderBy($orderBy)
          ->page($pager)
          ->fetchAll('id');

      $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'copyright', $browseType != 'bysearch');
      foreach($copyright as $key => $item){
        $softwareInfo = json_decode($item->softwareInfo);
        $item->fullname = implode(',', array_column($softwareInfo,'fullname'));
        if(!empty(array_filter(array_column($softwareInfo,'shortName')))){
            $item->shortName = implode(',', array_column($softwareInfo,'shortName'));
        }else{
            $item->shortName = '';
        }
        $item->version = implode(',', array_column($softwareInfo,'version'));
      }
      //用于导出数据构建查询
      $copyrightExportQuery = $this->dao->sqlobj->select('*')->from(TABLE_COPYRIGHT)
          ->where('deleted')->eq('0')
          ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
          ->beginIF($browseType == 'bysearch')->andWhere($copyrightQuery)->fi();
      $this->session->set('copyrightExportQuery', $copyrightExportQuery->sql);

      return $copyright;
  }

    /**
     * Project: chengfangjinke
     * Desc:获取单条知识产权信息
     */
    public function getByID($id)
    {
        $copyright = $this->dao->findByID($id)->from(TABLE_COPYRIGHT)->fetch();
        $copyright->files = $this->loadModel('file')->getByObject('copyright', $id);
        $copyright->productList = json_decode($copyright->softwareInfo);
        $reviewer = $this->loadModel('review')->getReviewer('copyright', $copyright->id, $copyright->changeVersion);
        $copyright->reviewer = $reviewer ? ',' . $reviewer . ','  : '';
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('copyright') //状态流转 工作量
        ->andWhere('objectID')->eq($copyright->id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        if(helper::isZeroDate($copyright->devFinishedTime)) $copyright->devFinishedTime=null;
        if(helper::isZeroDate($copyright->firstPublicTime)) $copyright->firstPublicTime=null;
        $copyright->consumed = $cs;

        return $copyright;
    }

    public function review($copyrightId){
        $copyright = $this->getByID($copyrightId);
        //校验数据
        //$this->checkConsumed();
        if ($this->post->result == 'pass'){
            $this->checkParamsNotEmpty($_POST, $this->config->copyright->review->pass->requiredFields);
        }else if($this->post->result == 'reject' ){
            $this->checkParamsNotEmpty($_POST, $this->config->copyright->review->reject->requiredFields);
        }
        if(!in_array($copyright->status, array('togroupreview','todepartreview','toinnovatereview'))){
            dao::$errors[] = $this->lang->copyright->statuserror;
        }

        if(!in_array($this->app->user->account,explode(',',$copyright->dealUser))){
            dao::$errors[] = $this->lang->copyright->dealusererror;
        }
        $this->tryError();
        if (!dao::isError()){
            //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
            $res = $this->checkAllowReview($copyright, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        }
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        $postData = fixer::input('post')
            ->stripTags($this->config->copyright->editor->review['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $this->loadModel('file')->processImgURL($postData, $this->config->copyright->editor->review['id'], $this->post->uid);
        $extraObj = new stdclass();
        $extraObj->reviewTime = helper::now();
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }
        $is_all_check_pass = false;
        $result = $this->loadModel('review')->check('copyright', $copyrightId, $copyright->changeVersion, $this->post->result, $this->post->comment, '', $extraObj, $is_all_check_pass);
        if($result == 'pass')
        {
            $updateData = new stdclass();
            $add = 1;
            //下一审核节点
            $nextReviewStage = $copyright->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->copyright->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->copyright->reviewNodeStatusList[$nextReviewStage];
                $updateData->reviewStage = $nextReviewStage;
                $updateData->status = $status;
            }else{
                //若已到达二线专员审批阶段，则审批通过后的状态应为【待同步清总】处理人为【成方金科】，且没有下一步的stage，stage置null
                $updateData->reviewStage = null;
                $updateData->status = 'done';
                $updateData->dealUser = '';
            }
            $this->dao->update(TABLE_COPYRIGHT)->data($updateData)->where('id')->eq($copyrightId)->exec();
            $this->loadModel('consumed')->record('copyright', $copyrightId, '0', $this->app->user->account, $copyright->status, $updateData->status, array());
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('copyright')
                ->andWhere('objectID')->eq($copyrightId)
                ->andWhere('version')->eq($copyright->changeVersion)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
                $reviewers = $this->loadModel('review')->getReviewer('copyright', $copyrightId, $copyright->changeVersion, $nextReviewStage);
                $this->dao->update(TABLE_COPYRIGHT)->set('dealUser')->eq($reviewers)->where('id')->eq($copyrightId)->exec();
            }
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：通过" : "审批结论：通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('copyright', $copyrightId, 'review', $historyComment);
        }else if($result == 'reject') {
            $this->dao->update(TABLE_COPYRIGHT)->set('reviewStage')->eq('')->set('status')->eq('returned')->set('dealUser')->eq($copyright->createdBy)->where('id')->eq($copyrightId)->exec();
            $this->loadModel('consumed')->record('copyright', $copyrightId, '0', $this->app->user->account, $copyright->status, 'returned', array());
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：不通过" : "审批结论：不通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('copyright', $copyrightId, 'review', $historyComment);
        }
    }

    public function checkAllowReview($copyright, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$copyright){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $copyright->changeVersion) || ($reviewStage != $copyright->reviewStage)){
            $message = $this->lang->copyright->dealError;
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('copyright', $copyright->id, $copyright->changeVersion, $copyright->reviewStage);
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
     * Project: chengfangjinke
     * Desc:获取审核人
     * liuyuhan
     */
    public function getReviewers($deptId = 0)
    {
        $users = $this->loadModel('user')->getPairs('noletter|noclosed');
        $reviewers = array();
        if(!$deptId){
            $deptId = $this->app->user->dept;
        }
        $myDept = $this->loadModel('dept')->getByID($deptId);
        //申请部门组长审批
        $groupUsers = explode(',', trim($myDept->groupleader, ','));
        $us = array('' => '');
        if(!empty($groupUsers)){
            foreach($groupUsers as $c)
            {
                $us[$c] = $users[$c];
            }
        }
        $reviewers['0'] = $us;

//        // 部门负责人
//        $cms = explode(',', trim($myDept->manager, ','));
//        $us  = array('' => '');
//        foreach($cms as $c)
//        {
//            $us[$c] = $users[$c];
//        }
//        $reviewers['1'] = $us;
        // 产创审核人
        $innovateReviewers = array_keys($this->lang->copyright->innovateReviewerList);
        $us  = array('' => '');
        foreach($innovateReviewers as $c)
        {
            $us[$c] = $users[$c];

        }
        $reviewers['1'] = $us;

        return $reviewers;
    }

    public function getItemsValue($data, $langItemsList)
    {
        $dataItem = '';
        $dataItems = '';
        if (!($data == '')) {
            foreach (explode(',', $data) as $value) {
                if (!($value == '')) $dataItem .= zget($langItemsList, $value, $value) . ',';
            }
        }
        $dataItems = trim($dataItem, ',');
        return $dataItems;
    }
    /**
     * Project: chengfangjinke
     * Desc: 新建
     * isSave=true:保存；isSave=false:提交审核
     * liuyuhan
     */
    public function create($isSave = false){

        $data = fixer::input('post')
            ->add('createdBy',$this->app->user->account)
            ->add('status', $isSave?'tosubmit':'togroupreview')
            ->add('createdTime', helper::now())
            ->add('changeVersion', 1)
            ->add('reviewStage', $isSave? null:'1')
            ->join('softwareType',',')
            ->join('identityMaterial',',')
            ->join('generalDeposit',',')
            ->join('exceptionalDeposit',',')
            ->join('devLanguage',',')
            ->join('techFeatureType',',')
            ->remove('uid,files,nodes,consumed')
            ->get();

       // $this->checkConsumed();

        $productList = array();
        foreach ($data->fullname as $key=>$value){
            $product = new stdClass();
            $product->fullname = $value;
            $product->shortName = $data->shortName[$key];
            $product->version = $data->version[$key];
            $productList[] = $product;
            if((is_null($product->fullname) || $product->fullname == ''  || $product->fullname == ' ') || ((is_null($product->version) || $product->version == ''  || $product->version == ' '))){
                dao::$errors['productInfoError']=$this->lang->copyright->productInfoError;
            }
        }
        $data->fullname = implode(',', $data->fullname);
        unset($data->shortName);
        unset($data->version);

        $ismodifyCodeUsed = $this->dao->select('id')->from(TABLE_COPYRIGHT)->where('modifyCode')->eq($data->modifyCode)->andwhere('deleted')->eq('0')->fetch('id');
        if($ismodifyCodeUsed){
            dao::$errors['modifyCode']=$this->lang->copyright->modifycodeusederror;
        }
        //检查非空
        $this->checkParamsNotEmpty($data,$isSave?$this->config->copyright->save->requiredFields:$this->config->copyright->create->requiredFields);

        //校验字段长度
//        不再校验字符串长度 【2413】
//        $this->checkStrlen($data);

        //检查格式
        $this->checkTypes($data);

        //若选择【提交】，进行数据校验和是否选择审批人校验
        if (!$isSave){
            //进行数据校验
            $this->checkSubmitForm($isSave, $data);
            //检查是否选择提交审批人
            $this->checkReviewerNodes();
            if($this->loadModel('file')->getCount()==0){//检查附件
                dao::$errors['file']=sprintf($this->lang->copyright->emptyObject,$this->lang->copyright->file);
            }
        }
        $this->tryError();
        $data->createdDept =  $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($data->createdBy)->fetch('dept');
        $modify = $this->dao->select('id,code')->from(TABLE_MODIFY)->where('code')->eq($data->modifyCode)->fetch();
        $data->modifyId = $modify->id;
        $data->modifyCode = $modify->code;
        $data->softwareInfo = json_encode($productList);
        $number = $this->dao->select('count(id) c')->from(TABLE_COPYRIGHT)->where('createdTime')->ge( date('Y').'-01-01 00:00:00')->fetch('c');
        if ($number < 999){
            $code   =  date('Y')."CFITSR" . sprintf('%03d', $number+1);
        }else{
            $code   =  date('Y')."CFITSR" . ($number+1);
        }
        $data->code = $code;
        $this->tryError();

        $data = (array)$data;
        $this->dao->insert(TABLE_COPYRIGHT)->data($data)->autoCheck()->exec();
        $id = $this->dao->lastInsertId();
        $this->tryError();

        $copyrightNow = $this->getByID($id);
        $this->submitReview($id, $copyrightNow->changeVersion);
        $firstReviewer = $this->loadModel('review')->getReviewer('copyright', $id, $copyrightNow->changeVersion);
        $dealUser = $isSave?$copyrightNow->createdBy:$firstReviewer;
        $this->dao->update(TABLE_COPYRIGHT)->set('dealUser')->eq($dealUser)->where('id')->eq($id)->exec();
        $this->loadModel('file')->updateObjectID($this->post->uid, $id, 'copyright');
        $this->file->saveUpload('copyright', $id);
        $this->loadModel('consumed')->record('copyright', $id, '0', $this->app->user->account, '-', $copyrightNow->status);
        return $id;
    }

    /**
     * Project: chengfangjinke
     * Desc: 提交时进行数据验证
     * liuyuhan
     */
    public function checkSubmitForm($isSave, $data){
        if(strstr($data->identityMaterial,'99')){
            if($data->generalDeposit==''){
                dao::$errors['generalDeposit']=sprintf($this->lang->copyright->emptyObject,$this->lang->copyright->generalDeposit);
            }
            if($data->generalDepositType==''){
                dao::$errors['generalDepositType']=sprintf($this->lang->copyright->emptyObject,$this->lang->copyright->generalDepositType);
            }
        }
        if(strstr($data->identityMaterial,'1')){
            if($data->exceptionalDeposit==''){
                dao::$errors['exceptionalDeposit']=sprintf($this->lang->copyright->emptyObject,$this->lang->copyright->exceptionalDeposit);
            }else if($data->exceptionalDeposit=='99'){
                if($data->pageNum==''){
                    dao::$errors['pageNum']=sprintf($this->lang->copyright->emptyObject,$this->lang->copyright->pageNum);
                }
            }
        }

    }

    /**
     * Project: chengfangjinke
     * Desc: 检查填写和上传格式
     * liuyuhan
     */
    public function checkTypes($data){
        $reg = '/^[1-9][0-9]*$/';
        if ($data->pageNum!='' and !preg_match($reg, $data->pageNum))
        {
            dao::$errors['pageNum']=$this->lang->copyright->pageNumObject;
        }

        if(!empty($_FILES)){
            foreach($_FILES['files']['name'] as $name){
                if(!in_array(pathinfo($name,PATHINFO_EXTENSION),$this->lang->copyright->allowFileTypes)){
                    dao::$errors['file']= sprintf($this->lang->copyright->fileTypeError,$name);
                }
            }
        }
    }
    /**
     * Project: chengfangjinke
     * Desc: 检查是否选择了审核人
     */
    private function checkReviewerNodes(){
        if(!$this->post->nodes){
            $nodesKeys = [];
        }else{
            $nodesKeys = $this->post->nodes;
        }
        foreach($this->lang->copyright->reviewerList as $key=>$value){
            if(empty($nodesKeys[$key])||$nodesKeys[$key]==array('')||$nodesKeys[$key]==''){
                dao::$errors[] =  sprintf($this->lang->copyright->emptyObject, $this->lang->copyright->reviewerList[$key]);
            }
        }
    }

    /**
     * Project: chengfangjinke
     * Desc:检查字符串长度
     */
    private function checkStrlen($data){
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        foreach($this->lang->copyright->maxLen as $key=>$len){
            if(strlen($data[$key])>$len){
                $itemName = $this->lang->copyright->$key ?? $key;
                dao::$errors[$key] = sprintf($this->lang->copyright->overSizeObject,$itemName,$len);
            }
        }
    }

    /**
     * 尝试报错 或需要rollback
     */
    public function tryError($rollBack = 0)
    {
        if(dao::isError())
        {
            if($rollBack == 1){
                $this->dao->rollBack();
            }
            $response['result']  = 'fail';
            $response['message'] = dao::getError();
            $this->send($response);
        }
    }

    /**
     * 直接输出data数据
     * @access public
     */
    private function send($data)
    {
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Project: chengfangjinke
     * Desc:新建时/退回重新走流程时 创建审核节点和审核信息
     */
    private function submitReview($id, $version)
    {
        $status = 'pending';
        $stage = 1;
        $nodes = $this->post->nodes;
        foreach($nodes as $key => $currentNodes)
        {
            if(!is_array($currentNodes)){
                $currentNodes = array($currentNodes);
            }
            $currentNodes = array_filter($currentNodes);
            $this->loadModel('review')->addNode('copyright', $id, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }

    /**
     * Project: chengfangjinke
     * Desc:检查参数是否为空
     * liuyuhan
     */
    private function checkParamsNotEmpty($data, $fields)
    {
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        $fieldArray = explode(',', str_replace(' ', '', $fields));
        foreach ($fieldArray as $item)
        {
            if(is_null($data[$item]) || $data[$item] == ''  || $data[$item] == ' ' ){
                $itemName = $this->lang->copyright->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->copyright->emptyObject, $itemName);
            }
        }
    }

    /**
     * Project: chengfangjinke
     * Desc:工作量输入验证
     */
    public function checkConsumed(){
        //工作量验证,输入小数点后保留1位小数
        $consumed = $_POST['consumed'];
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(is_null($consumed) || $consumed == ''  || $consumed == ' ')
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyright->emptyObject, $this->lang->copyright->consumed);
        }else if(!is_numeric($consumed)) {
            dao::$errors['consumed'] = sprintf($this->lang->copyright->noNumeric, $this->lang->copyright->consumed);
        }else if(!preg_match($reg, $consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyright->consumedError, $this->lang->copyright->consumed);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc:编辑保存&编辑提交
     * liuyuhan
     */
    public function update($id,$isSave=false){
        $old = $this->getByID($id);

        $data = fixer::input('post')
            ->add('createdBy',$this->app->user->account)
            ->add('status', $isSave?'tosubmit':'togroupreview')
            ->add('editedBy',$this->app->user->account)
            ->add('editedTime', helper::now())
            ->add('reviewStage', $isSave? null:'1')
            ->join('softwareType',',')
            ->join('identityMaterial',',')
            ->join('generalDeposit',',')
            ->join('exceptionalDeposit',',')
            ->join('devLanguage',',')
            ->join('techFeatureType',',')
            ->remove('uid,files,nodes,consumed')
            ->get();
        //$this->checkConsumed();

        $productList = array();
        foreach ($data->fullname as $key=>$value){
            $product = new stdClass();
            $product->fullname = $value;
            $product->shortName = $data->shortName[$key];
            $product->version = $data->version[$key];
            $productList[] = $product;
            if((is_null($product->fullname) || $product->fullname == ''  || $product->fullname == ' ') || ((is_null($product->version) || $product->version == ''  || $product->version == ' '))){
                dao::$errors['productInfoError']=$this->lang->copyright->productInfoError;
            }
        }
        $data->fullname = implode(',', $data->fullname);
        unset($data->shortName);
        unset($data->version);

        $ismodifyCodeUsed = $this->dao->select('id')->from(TABLE_COPYRIGHT)->where('modifyCode')->eq($data->modifyCode)->andwhere('id')->ne($id)->andwhere('deleted')->eq('0')->fetch('id');
        if($ismodifyCodeUsed){
            dao::$errors['modifyCode']=$this->lang->copyright->modifycodeusederror;
        }
        //检查非空
        $this->checkParamsNotEmpty($data,$isSave?$this->config->copyright->save->requiredFields:$this->config->copyright->create->requiredFields);

        //校验字段长度
//        不再校验字符串长度 【2413】
//        $this->checkStrlen($data);

        //检查格式
        $this->checkTypes($data);

        //若选择【提交】，进行数据校验和是否选择审批人校验
        if (!$isSave){
            //进行数据校验
            $this->checkSubmitForm($isSave, $data);
            //检查是否选择提交审批人
            $this->checkReviewerNodes();
            if($this->loadModel('file')->getCount()==0&&count($old->files)==0){//检查附件
                dao::$errors['file']=sprintf($this->lang->copyright->emptyObject,$this->lang->copyright->file);
            }
        }
        $this->tryError();
        $modify = $this->dao->select('id,code')->from(TABLE_MODIFY)->where('code')->eq($data->modifyCode)->fetch();
        $data->modifyId = $modify->id;
        $data->modifyCode = $modify->code;
        $data->softwareInfo = json_encode($productList);
        $this->tryError();
        $version = $old->changeVersion;

        if($old->status=='tosubmit'){
            $this->submitEditReview($id,$version);
        }else{
            $version = $version + 1;//如果是外部退回就版本+1
            $data->changeVersion = $version;
            $this->submitReview($id,$version);
        }
        $firstReviewer = $this->loadModel('review')->getReviewer('copyright', $id, $version);
        $data->dealUser = $isSave?$data->createdBy:$firstReviewer;
        $data = (array)$data;
        $this->dao->update(TABLE_COPYRIGHT)->data($data)
            ->where('id')->eq($id)
            ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $id, 'copyright');
        $this->file->saveUpload('copyright', $id);

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('copyright')
            ->andWhere('objectID')->eq($id)
            ->orderBy('id_desc')
            ->fetch();
        // $this->loadModel('consumed')->record('copyright', $id, $this->post->consumed, $this->app->user->account, $old->status, $data['status'], array());
        if($old->status=='returned'){
            $this->loadModel('consumed')->record('copyright', $id,'0', $this->app->user->account, $old->status, $data['status'], array());
        }else{
            $this->loadModel('consumed')->update($cs->id,'copyright', $id, '0', $this->app->user->account, $old->status, $data['status'], array());
        }
        
        $new = $this->getByID($id);
        return common::createChanges($old, $new);
    }

    /**
     * Project: chengfangjinke
     * Desc: 【待提交】状态下编辑后，比较审核人是否有变化，进行更新
     * liuyuhan
     */
    private function submitEditReview($id, $version){
        $objectType = 'copyright';
        //原审核节点及审核人
        $oldNodes = $this->loadModel('review')->getNodes($objectType, $id, $version);
        $nodes = $this->post->nodes;
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
     * Desc:删除功能
     * liuyuhan
     */
    public function deleted($id=0){
        $old = $this->getByID($id);
        $this->dao->update(TABLE_COPYRIGHT)->set('deleted')->eq('1')->where('id')->eq($id)->exec();
        $this->tryError();
        //获取新的数据
        $new = $this->getByID($id);
        return common::createChanges($old, $new);
    }

    /**
     * Project: chengfangjinke
     * Desc:发送邮件
     * liuyuhan
     */
    public function sendmail($id, $actionID)
    {
        $this->loadModel('mail');
        $copyright  = $this->getByID($id);
        $copyright->fullname = implode(',', array_column($copyright->productList,'fullname'));
        $users = $this->loadModel('user')->getPairs('noletter');
        $copyright->createdByName = zget($users,$copyright->createdBy);


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setcopyrightMail) ? $this->config->global->setcopyrightMail : '{"mailTitle":"【待办】您有一个【著作权申请】%s，请及时登录研发过程管理平台进行处理","mailContent":"请进入【知识产权】-【自主知识产权】，查看详情，具体信息如下："}';
        $mailConf   = json_decode($mailConf);

        if($copyright->status == 'returned'){
            $variables = '已退回';
        }elseif($copyright->status == 'done'){
            $variables = '已通过/已完成';
        }else{
            $variables = '待审批';
            $mailConf->mailContent='请进入【地盘】-【待处理】-【审批】或【知识产权】-【自主知识产权】，查看详情，具体信息如下：';
        }
        $mailTitle = sprintf($mailConf->mailTitle, $variables);
        /* Get action. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        //获取退回原因
        if($action->action == 'review'){
            $rejectNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('copyright')
                ->andwhere('objectID')->eq($copyright->id)
                ->andwhere('version')->eq($copyright->changeVersion)
                ->andwhere('status')->eq('reject')
                ->fetch('id');
            $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)
                ->where('node')->eq($rejectNode)
                ->andwhere('status')->eq('reject')
                ->fetch('extra');
            $copyright->rejectreason = strip_tags(json_decode($extra)->rejectReason);
        }

        //获取通过时间
        if ($copyright->status=='done'){
            $rejectNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('copyright')
                ->andwhere('objectID')->eq($copyright->id)
                ->andwhere('version')->eq($copyright->changeVersion)
                ->andwhere('status')->eq('pass')
                ->andwhere('stage')->eq('2')
                ->fetch('id');
            $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)
                ->where('node')->eq($rejectNode)
                ->andwhere('status')->eq('pass')
                ->fetch('extra');
            $copyright->reviewTime = strip_tags(json_decode($extra)->reviewTime);

        }
        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'copyright');
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

        $sendUsers = $this->getToAndCcList($copyright);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($info);
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    /**
     * Project: chengfangjinke
     * Desc:获取收件人和抄送人
     * liuyuhan
     */
    private function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $status = $object->status;
        if($status == 'returned'){
            $toList = $object->createdBy;  //创建者
        }else if($status == 'done'){
            $toList = $object->createdBy;  //创建者
        }else{
            $toList = $this->loadModel('review')->getReviewer('copyright', $object->id, $object->changeVersion, $object->reviewStage);
        }
        $ccList = '';

        return array($toList, $ccList);
    }


    public static function isClickable($data, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'edit') return ($data->status == 'tosubmit' or $data->status == 'returned') and ($data->createdBy == $app->user->account or $app->user->account=='admin');
        if($action == 'delete') return ($data->status == 'tosubmit' or $data->status == 'returned') and ($data->createdBy == $app->user->account or $app->user->account=='admin');
        if($action == 'review') return (in_array($data->status, array('togroupreview','todepartreview','toinnovatereview')))   and (in_array($app->user->account,explode(',',$data->dealUser)));
        return true;
    }
    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = ''){
        $copyright  = $obj;
        $copyright->fullname = implode(',', array_column($copyright->productList,'fullname'));
        $users = $this->loadModel('user')->getPairs('noletter');
        $copyright->createdByName = zget($users,$copyright->createdBy);

        /* Get action. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();
        //获取退回原因
        if($action->action == 'review'){
            $rejectNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('copyright')
                ->andwhere('objectID')->eq($copyright->id)
                ->andwhere('version')->eq($copyright->changeVersion)
                ->andwhere('status')->eq('reject')
                ->fetch('id');
            $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)
                ->where('node')->eq($rejectNode)
                ->andwhere('status')->eq('reject')
                ->fetch('extra');
            $copyright->rejectreason = strip_tags(json_decode($extra)->rejectReason);
        }

        //获取通过时间
        if ($copyright->status=='done'){
            $rejectNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('copyright')
                ->andwhere('objectID')->eq($copyright->id)
                ->andwhere('version')->eq($copyright->changeVersion)
                ->andwhere('status')->eq('pass')
                ->andwhere('stage')->eq('2')
                ->fetch('id');
            $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)
                ->where('node')->eq($rejectNode)
                ->andwhere('status')->eq('pass')
                ->fetch('extra');
            $copyright->reviewTime = strip_tags(json_decode($extra)->reviewTime);

        }

        $mailConf   = isset($this->config->global->setcopyrightMail) ? $this->config->global->setcopyrightMail : '{"mailTitle":"【待办】您有一个【著作权申请】%s，请及时登录研发过程管理平台进行处理","mailContent":"请进入【知识产权】-【自主知识产权】，查看详情，具体信息如下："}';
        $mailConf   = json_decode($mailConf);

        if($copyright->status == 'returned'){
            $variables = '已退回';
        }elseif($copyright->status == 'done'){
            $variables = '已通过/已完成';
        }else{
            $variables = '待审批';
            $mailConf->mailContent='请进入【地盘】-【待处理】-【审批】或【知识产权】-【自主知识产权】，查看详情，具体信息如下：';
        }
        $mailConf->mailTitle = sprintf($mailConf->mailTitle, $variables);

        $sendUsers = $this->getToAndCcList($copyright);
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

        return ['toList'=>$toList,'subcontent'=>$subcontent,'url'=>$url,'title'=>$title,'actions'=>$actions,'mailconfig'=>json_encode($mailConf)];
    }
}