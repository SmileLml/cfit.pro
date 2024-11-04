<?php

use PhpOffice\PhpWord\Escaper\RegExp;

class copyrightqzModel extends model
{
    public function pushcopyrightqz($id){
        if($this->config->global->copyrightqzEnable != 'enable') return false;
//        $fileIP        = $this->config->global->copyrightqzFileIP;
        //url路径增加代理ip,可后台配置
        $fileIP           = $this->lang->copyrightqz->copyrightqzFileIP['copyrightqzFileIP'] ;
        $url           = $this->config->global->copyrightqzUrl;
        $pushAppId     = $this->config->global->copyrightqzAppId;
        $pushAppSecret = $this->config->global->copyrightqzAppSecret;
        
        $data    = $this->getByID($id);
        $giteeId = $this->dao->select('giteeId')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->eq($data->emisCode)->andwhere('deleted')->ne('1')->fetch('giteeId');
        if(!$giteeId){
            $update = array();
            $update['synDate'] = helper::now();
            $update['status'] ='synFailed';
            $update['synStatus'] = '2';
            $update['synFailedReason'] = '产品登记单号不存在';
            $update['synFailedTimes'] = $data->synFailedTimes+1;
            $update['dealUser'] = implode(',',array_keys($this->lang->copyrightqz->secondLineReviewList));
            $this->loadModel('action')->create('copyrightqz', $data->id, 'qingzongsynfailed', '','');
            $this->loadModel('consumed')->record('copyrightqz', $data->id, '0', 'guestjk', $data->status, 'synFailed');
            $this->dao->update(TABLE_COPYRIGHTQZ)->data($update)->where('id')->eq($id)->exec();
            return false;
        }
        $pushData = array();
        $pushData['copyrightRegistrationId']                    = $data->id; //唯一标识, gitee会根据该值判断是更新还是新增
        $pushData['rjdjhzzq']                                   = $giteeId; //关联产品登记单
        $pushData['sqr']                                        = $data->applicant; //用户
        $pushData['rjqc']                                       = $data->fullname; //软件全称
        $pushData['rjjc']                                       = $data->shortName; //软件简称
        $pushData['rjbbh']                                      = $data->version; //软件版本号
        $pushData['ywxt']                                       = Strval($data->system); //业务系统
        $pushData['zjzysm']                                     = Strval($data->descType==99?0:$data->descType); //软件作品说明
        $pushData['rjzpsm']                                     = $data->description; //软件作品说明
        $pushData['kfwcrq']                                     = strtotime($data->devFinishedTime).'000'; //开发完成日期
        $pushData['fbzt']                                       = Strval($data->publishStatus==99?0:$data->publishStatus); //发表状态
        $pushData['scfbrq']                                     = strtotime($data->firstPublicTime).'000'; //首次发表日期
        $pushData['scfbgj']                                     = Strval($data->firstPublicCountry); //首次发表国家
        $pushData['scfbdd']                                     = $data->firstPublicPlace; //首次发表地点
        $pushData['kffs']                                       = Strval($data->devMode==99?0:$data->devMode); //开发方式
        $pushData['qlqdfs']                                     = Strval($data->rightObtainMethod==99?0:$data->rightObtainMethod); //权利取得方式
        $pushData['zzqsfydj']                                   = Strval($data->isRegister==99?0:$data->isRegister); //软件著作权是否已登记
        $pushData['ydjh']                                       = $data->oriRegisNum; //原登记号
        $pushData['ydjsfzgbghbc']                               = Strval($data->isOriRegisNumChanged==99?0:$data->isOriRegisNumChanged); //原登记是否做过变更或补充
        $pushData['bghbczmbh']                                  = $data->proveNum; //变更或补充证明编号
        $pushData['qlfw']                                       = Strval($data->rightRange==99?0:$data->rightRange); //权利范围
        $pushData['ycxl']                                       = $data->sourceProgramAmount; //源程序量
        $pushData['rjjbcl']                                     = Strval($data->identityMaterial==99?0:$data->identityMaterial); //软件鉴别材料
        $pushData['ybjc']                                       = $data->identityMaterial==99?Strval($data->generalDeposit==99?0:$data->generalDeposit):''; //一般交存
        $pushData['ybjcwdzl']                                   = $data->identityMaterial==99?$data->generalDepositType:''; //一般交存文档种类
        $pushData['lwjc']                                       = $data->identityMaterial!=99?Strval($data->exceptionalDeposit==99?0:$data->exceptionalDeposit):''; //例外交存	
        $pushData['ymw']                                        = ($data->identityMaterial!=99 and $data->exceptionalDeposit==99)?$data->pageNum:''; //页码为
        $relationFiles = array();
        $files = $this->loadModel('file')->getByObject('copyrightqz', $id);
        /*foreach ($files as $file){
            if($file->extension){
                $tail = strlen($file->extension) + 1;
            }
            $realRemotePath = substr($fileIP.'/api.php?m=api&f=getfile&code=jinke1problem&time=1&token=1&filename='.$file->pathname, 0, -$tail); //实际存的附件没有后缀 需要去掉
            $localRealFile =  $file->realPath; //实际存的附件
            $md5 = md5_file($localRealFile);
            array_push($relationFiles, array('url'=> $realRemotePath, 'md5Url'=> $md5, 'fileName' => $file->title));
        }*/
        $relationFiles = $this->loadModel('common')->sendFileBySftp($files,'copyrightqz',$data->code);
        //附件传输失败
        if (dao::isError()) {
            $updateComment = dao::getError();
            $updateComment = implode(',',$updateComment);
            $update['status'] ='synFailed';
            $update['synStatus'] = '2';
            $update['synFailedReason'] = $updateComment;
            $update['synFailedTimes'] = $data->synFailedTimes+1;
            $update['dealUser'] = implode(',',array_keys($this->lang->copyrightqz->secondLineReviewList));
            $this->loadModel('action')->create('copyrightqz', $data->id, 'qingzongsynfailed', $updateComment,'');
            $this->loadModel('consumed')->record('copyrightqz', $data->id, '0', 'guestjk', $data->status, 'synFailed');  $update['status'] ='synFailed';
            $this->dao->update(TABLE_COPYRIGHTQZ)->data($update)->where('id')->eq($id)->exec();
            return false;
        }

        $pushData['relationFiles'] = $relationFiles;            
        $pushData['rjfl']                                       = Strval($data->softwareType==99?0:$data->softwareType); //软件分类
        $pushData['kfdyjhj50zj']                                = $data->devHardwareEnv; //开发的硬件环境（50字节）
        $pushData['yxdyjhj50zj']                                = $data->opsHardwareEnv; //运行的硬件环境（50字节）
        $pushData['kfgrjdczxt']                                 = $data->devOS; //开发该软件的操作系统（50字节）
        $pushData['rjkfhjkfgj']                                 = $data->devEnv; //软件开发环境/开发工具（50字节）
        $pushData['grjdyxptczxt50zj']                           = $data->operatingPlatform; //该软件的运行平台/操作系统（50字节）
        $pushData['rjyxzchjzcrj']                               = $data->operationSupportEnv; //软件运行支撑环境/支持软件（50字节）
        $pushData['bcyy1']                                      = implode(',',getArrayValuesByKeys($this->lang->copyrightqz->devLanguageList,explode(',',$data->devLanguage))); //编程语言
        $pushData['kfmd50zj']                                   = $data->devPurpose; //开发目的（50字节）
        $pushData['mxlyhy50zj']                                 = $data->industryOriented; //面向领域行业（50字节）
        $pushData['rjdzygn200zj']                               = $data->mainFunction; //软件的主要功能（200字节）
        $pushData['rjdjstd']                                    = implode(',',getArrayValuesByKeys($this->lang->copyrightqz->techFeatureTypeList,explode(',',$data->techFeatureType))); //软件的技术特点
        $pushData['rjdjstdwb']                                  = $data->techFeature; //软件的技术特点(文本)
        $pushData['qt']                                         = $data->others; //其他

        $headers = array();
        $headers[] = 'App-Id: ' . $pushAppId;
        $headers[] = 'App-Secret: ' . $pushAppSecret;
        $object     = 'copyrightqz';
        $objectType = 'pushcopyrightqz';
        
        $result = $this->loadModel('requestlog')->http($url, $pushData, 'POST', 'json', array(), $headers);
        $response = '';
        $status = 'fail';
        $extra = $data->code;
        $update = array();
        $update['synDate'] = helper::now();
        if (!empty($result)) {
            $resultData = json_decode($result);
            if ($resultData->code == '200' || $resultData->isSave == 1) { //200 = 成功的 isSave == 1 代表成功保存 比如第一次没响应
                $status = 'success';
                $update['status'] = 'tofeedback';
                $update['synStatus'] = '1';// 0未推送,1-推送成功,2-推送失败
                $update['dealUser'] = 'guestcn';
                if($resultData->data->key){
                    $update['copyrightRegistrationId'] = $resultData->data->key;// 我们的copyrightRegistrationId 对应清总的key(product-xxxx)
                }
                $this->loadModel('action')->create('copyrightqz', $data->id, 'pushcopyrightqz', '','');
                $this->loadModel('consumed')->record('copyrightqz', $data->id, '0', 'guestjk', $data->status, 'tofeedback');
                
            }else{
                $update['status'] ='synFailed';
                $update['synStatus'] = '2';
                $update['synFailedReason'] = $resultData->message;
                $update['synFailedTimes'] = $data->synFailedTimes+1;// 默认0
                $update['dealUser'] = implode(',',array_keys($this->lang->copyrightqz->secondLineReviewList));
                $this->loadModel('action')->create('copyrightqz', $data->id, 'qingzongsynfailed', '','');
                $this->loadModel('consumed')->record('copyrightqz', $data->id, '0', 'guestjk', $data->status, 'synFailed');
            }
        }else{
            $update['status'] ='synFailed';
            $update['synStatus'] = '2';
            $update['synFailedReason'] = '请求无响应';
            $update['synFailedTimes'] = $data->synFailedTimes+1;
            $update['dealUser'] = implode(',',array_keys($this->lang->copyrightqz->secondLineReviewList));
            $this->loadModel('action')->create('copyrightqz', $data->id, 'qingzongsynfailed', '','');
            $this->loadModel('consumed')->record('copyrightqz', $data->id, '0', 'guestjk', $data->status, 'synFailed');
        }
        $this->dao->update(TABLE_COPYRIGHTQZ)->data($update)->where('id')->eq($id)->exec();
        $this->requestlog->saveRequestLog($url, $object, $objectType, 'POST', $pushData, $result, $status, $extra, $id);
        return $update['synStatus'] == '1';
    }
    /**
     * Project: chengfangjinke
     * Desc: 获取列表
     * liuyuhan
     */
    public function getList($browseType, $queryID, $orderBy, $pager = null)
    {
        $copyrightqzQuery = '';
        if($browseType == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('copyrightqzQuery', $query->sql);
                $this->session->set('copyrightqzForm', $query->form);
            }

            if($this->session->copyrightqzQuery == false) $this->session->set('copyrightqzQuery', ' 1 = 1');

            $copyrightqzQuery = $this->session->copyrightqzQuery;

        }

        $copyrightqz = $this->dao->select('*')->from(TABLE_COPYRIGHTQZ)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($copyrightqzQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        //用于导出数据构建查询
        $copyrightqzExportQuery = $this->dao->sqlobj->select('*')->from(TABLE_COPYRIGHTQZ)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'all' and $browseType != 'bysearch')->andWhere('status')->eq($browseType)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($copyrightqzQuery)->fi();
        $this->session->set('copyrightqzExportQuery', $copyrightqzExportQuery->sql);


        foreach ($copyrightqz as $value){
            $productenroll = $this->dao->select('deleted')->from(TABLE_PRODUCTENROLL)->where('id')->eq($value->productenrollId)->fetch();
            $value->productenrollDeleted = $productenroll->deleted;
        }
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'copyrightqz', $browseType != 'bysearch');
        return $copyrightqz;
    }

    /**
     * Project: chengfangjinke
     * Desc: 构建搜索框
     * liuyuhan
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->copyrightqz->search['actionURL'] = $actionURL;
        $this->config->copyrightqz->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->copyrightqz->search);
    }

    public function create($isSave = false){
        $data = fixer::input('post')
            ->add('applicant',$this->app->user->account)
            ->add('status', $isSave?'tosubmit':'togroupreview')
            ->add('createdTime', helper::now())
            ->add('changeVersion', 1)
            ->add('reviewStage', $isSave?'0':'1')
            ->join('softwareType',',')
            ->join('identityMaterial',',')
            ->join('generalDeposit',',')
            ->join('exceptionalDeposit',',')
            ->join('devLanguage',',')
            ->join('techFeatureType',',')
            ->remove('uid,files,nodes,consumed')
            ->get();
        $this->checkformData();
       /* if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->consumed);
            return false;
        }

        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->copyrightqz->consumedError;
            return false;
        }*/
        $isEmisUsed = $this->dao->select('id')->from(TABLE_COPYRIGHTQZ)->where('emisCode')->eq($data->emisCode)->andwhere('deleted')->ne('1')->fetch('id');
        if($isEmisUsed){
            dao::$errors['emis']=$this->lang->copyrightqz->emiscodeusederror;
        }
        if($data->identityMaterial=='99'){
            if($data->generalDeposit==''){
                dao::$errors['generalDeposit']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->generalDeposit);
            }
            if($data->generalDepositType==''){
                dao::$errors['generalDepositType']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->generalDepositType);
            } 
        }
        if($data->identityMaterial=='1'){
            if($data->exceptionalDeposit==''){
                dao::$errors['exceptionalDeposit']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->exceptionalDeposit);
            }
            if($data->exceptionalDeposit=='99' &&$data->pageNum==''){
                dao::$errors['pageNum']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->pageNum);
            }
        }

        $reg = '/^[1-9][0-9]*$/';
        if ($data->pageNum!='' and !preg_match($reg, $data->pageNum))
        {
            dao::$errors['pageNum']=$this->lang->copyrightqz->pageNumObject;
        }

        if(!$isSave&&$this->loadModel('file')->getCount()==0){//检查附件
            dao::$errors['file']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->file);
        }
        if(!empty($_FILES)){
            foreach($_FILES['files']['name'] as $name){
                if(!in_array(pathinfo($name,PATHINFO_EXTENSION),$this->lang->copyrightqz->allowFileTypes)){
                    dao::$errors['file']= sprintf($this->lang->copyrightqz->fileTypeError,$name);
                }
            }
        }
        if(!$isSave) $data = $this->resetOutStatus($data);//清空推送失败的状态
        $isSave||$this->checkReviewerNodes();//提交的时候校验审批人
//        $this->checkStrlen($data);//校验字段长度
        $this->checkParamsNotEmpty($data,$isSave?$this->config->copyrightqz->save->requiredFields:$this->config->copyrightqz->create->requiredFields);
        $data->applicantDept =  $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($data->applicant)->fetch('dept');
        $productenroll = $this->dao->select('id,code')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->eq($data->emisCode)->fetch();
        $data->productenrollId = $productenroll->id;
        $data->productenrollCode = $productenroll->code;
        if(dao::isError()) return;
        $data = (array)$data;
        $this->dao->insert(TABLE_COPYRIGHTQZ)->data($data)->autoCheck()->exec();
        if(dao::isError()) return;
        $id = $this->dao->lastInsertId();
        $this->submitReview($id, 1, $isSave);
        $date   = date('Y-m-d');
        $number = $this->dao->select('count(id) c')->from(TABLE_COPYRIGHTQZ)->where('createdTime')->ge($date.' 00:00:00')->fetch('c');
        $code   = "CFIT-SR-" . date('Ymd-') . sprintf('%02d', $number);
        $firstReviewer = $this->loadModel('review')->getReviewer('copyrightqz', $id, 1);
        $dealUser = $isSave?$data['applicant']:$firstReviewer;
        $this->dao->update(TABLE_COPYRIGHTQZ)->set('code')->eq($code)->set('dealUser')->eq($dealUser)->where('id')->eq($id)->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $id, 'copyrightqz');
        $this->file->saveUpload('copyrightqz', $id);
        $this->loadModel('consumed')->record('copyrightqz', $id, '0', $this->app->user->account, '-', $data['status']);
        return $id;
    }

    public function update($id,$isSave=false){
        $old = $this->getByID($id);

        $data = fixer::input('post')
            ->add('applicant',$this->app->user->account)
            ->add('status', $isSave?'tosubmit':'togroupreview')
            ->add('editedBy',$this->app->user->account)
            ->add('editedTime', helper::now())
            ->add('reviewStage', $isSave?'0':'1')
            ->join('softwareType',',')
            ->join('identityMaterial',',')
            ->join('generalDeposit',',')
            ->join('exceptionalDeposit',',')
            ->join('devLanguage',',')
            ->join('techFeatureType',',')
            ->remove('uid,files,nodes,consumed')
            ->get();
        $this->checkformData();
       /* if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->consumed);
            return false;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->copyrightqz->consumedError;
            return false;
        }*/
        $isEmisUsed = $this->dao->select('id')->from(TABLE_COPYRIGHTQZ)->where('emisCode')->eq($data->emisCode)->andwhere('id')->ne($id)->andwhere('deleted')->ne('1')->fetch('id');
        if($isEmisUsed){
            dao::$errors['emis']=$this->lang->copyrightqz->emiscodeusederror;
        }
        if($data->identityMaterial=='99'){
            if($data->generalDeposit==''){
                dao::$errors['generalDeposit']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->generalDeposit);
            }
            if($data->generalDepositType==''){
                dao::$errors['generalDepositType']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->generalDepositType);
            } 
        }
        if($data->identityMaterial=='1'){
            if($data->exceptionalDeposit==''){
                dao::$errors['exceptionalDeposit']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->exceptionalDeposit);
            }
            if($data->exceptionalDeposit=='99' &&$data->pageNum==''){
                dao::$errors['pageNum']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->pageNum);
            }
        }
        $reg = '/^[1-9][0-9]*$/';
        if ($data->pageNum!='' and !preg_match($reg, $data->pageNum))
        {
            dao::$errors['pageNum']=$this->lang->copyrightqz->pageNumObject;
        }
        if(!$isSave&&$this->loadModel('file')->getCount()==0&&count($old->files)==0){//检查附件
            dao::$errors['file']=sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->file);
        }
        if(!empty($_FILES)){
            foreach($_FILES['files']['name'] as $name){
                if(!in_array(pathinfo($name,PATHINFO_EXTENSION),$this->lang->copyrightqz->allowFileTypes)){
                    dao::$errors['file']= sprintf($this->lang->copyrightqz->fileTypeError,$name);
                }
            }
        }
        if(!$isSave) $data = $this->resetOutStatus($data);//清空推送失败的状态
        $isSave||$this->checkReviewerNodes();//提交的时候校验审批人
//        $this->checkStrlen($data);//校验字段长度
        $this->checkParamsNotEmpty($data,$isSave?$this->config->copyrightqz->save->requiredFields:$this->config->copyrightqz->edit->requiredFields);
        $data->applicantDept =  $this->dao->select('dept')->from(TABLE_USER)->where('account')->eq($data->applicant)->fetch('dept');
        $productenroll = $this->dao->select('id,code')->from(TABLE_PRODUCTENROLL)->where('emisRegisterNumber')->eq($data->emisCode)->fetch();
        $data->productenrollId = $productenroll->id;
        $data->productenrollCode = $productenroll->code;
        if(dao::isError()) return;
        $version = $old->changeVersion;
        if($old->status=='tosubmit'){
            $this->submitEditReview($id,$version);
        }else{
            $version = $version + 1;//如果是外部退回就版本+1
            $data->changeVersion = $version;
            $this->submitReview($id,$version,$isSave);
        }
        $firstReviewer = $this->loadModel('review')->getReviewer('copyrightqz', $id, $version);
        $data->dealUser = $isSave?$data->applicant:$firstReviewer;
        $data = (array)$data;
        $this->dao->update(TABLE_COPYRIGHTQZ)->data($data)
        ->where('id')->eq($id)
        ->exec();

        $this->loadModel('file')->updateObjectID($this->post->uid, $id, 'copyrightqz');
        $this->file->saveUpload('copyrightqz', $id);

        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('copyrightqz')
        ->andWhere('objectID')->eq($id)
        ->orderBy('id_desc')
        ->fetch();
        // $this->loadModel('consumed')->record('copyrightqz', $id, $this->post->consumed, $this->app->user->account, $old->status, $data['status'], array());
        if($old->status=='returned'){
            $this->loadModel('consumed')->record('copyrightqz', $id, '0', $this->app->user->account, $old->status, $data['status'], array());
        }else{
            $this->loadModel('consumed')->update($cs->id,'copyrightqz', $id, '0', $this->app->user->account, $old->status, $data['status'], array());
        }

        return common::createChanges($old, $data);
    }

    private function resetOutStatus($data){
        $data->synStatus = '0';
        $data->synFailedReason = '';
        $data->synFailedTimes = '0';
        $data->synDate = null;
        $data->reason = '';
        $data->outsideReviewTime = null;
        $data->outsideReviewResult = '';
        $data->approverName = '';
        return $data;
    }

    private function checkStrlen($data){
        if(!is_array($data)) {
            if (!is_object($data)) $data = (object)$data;
            $data = (array)$data;
        }
        foreach($this->lang->copyrightqz->maxLen as $key=>$len){
            if(strlen($data[$key])>$len){
                $itemName = $this->lang->copyrightqz->$key ?? $key;
                dao::$errors[$key] = sprintf($this->lang->copyrightqz->overSizeObject,$itemName,$len);
            }
        }
    }

    private function checkReviewerNodes(){
        if(!$this->post->nodes){
            $nodesKeys = [];
        }else{
            $nodesKeys = $this->post->nodes;
        }
        foreach($this->lang->copyrightqz->reviewerList as $key=>$value){
            if(empty($nodesKeys[$key])||$nodesKeys[$key]==array('')||$nodesKeys[$key]==''){
                dao::$errors[] =  sprintf($this->lang->copyrightqz->emptyObject, $this->lang->copyrightqz->reviewerList[$key]);
            }
        }
    }

    private function submitReview($id, $version,$isSave)
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
            $this->loadModel('review')->addNode('copyrightqz', $id, $version, $currentNodes, true, $status, $stage);
            $status = 'wait';
            $stage++;
        }
    }

    private function submitEditReview($id, $version){
        $objectType = 'copyrightqz';
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
        $reviewers[] = $us;

        // 部门负责人
        $cms = explode(',', trim($myDept->manager, ','));
        $us  = array('' => '');
        foreach($cms as $c)
        {
            $us[$c] = $users[$c];
        }
        $reviewers[] = $us;

        // 产创部二线专员
        // $cms = explode(',', trim($myDept->executive, ','));
        // $us = array('' => '');
        // foreach($cms as $c)
        // {
        //     $us[$c] = $users[$c];
        // }
        // $reviewers[] = $us;
        $reviewers[] = $this->lang->copyrightqz->secondLineReviewList;

        return $reviewers;
    }

    public function reject($id){
        $data = $this->getByID($id);
       /* if(!$this->post->consumed)
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->consumed);
            return false;
        }
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if (!preg_match($reg, $this->post->consumed))
        {
            dao::$errors['consumed'] = $this->lang->copyrightqz->consumedError;
            return false;
        }*/

        $this->dao->update(TABLE_COPYRIGHTQZ)->set('status')->eq('returned')
        ->set('dealUser')->eq($data->applicant)
        ->where('id')->eq($id)->exec();
        $this->loadModel('consumed')->record('copyrightqz', $id, '0', $this->app->user->account, $data->status, 'returned', array());
        return true;
    }

    public function getByID($id)
    {
        $copyrightqz = $this->dao->findByID($id)->from(TABLE_COPYRIGHTQZ)->fetch();
        $copyrightqz->files = $this->loadModel('file')->getByObject('copyrightqz', $id);
        $reviewer = $this->loadModel('review')->getReviewer('copyrightqz', $copyrightqz->id, $copyrightqz->changeVersion);
        $copyrightqz->reviewer = $reviewer ? ',' . $reviewer . ','  : '';
        $cs = $this->dao->select('*')->from(TABLE_CONSUMED)->where('objectType')->eq('copyrightqz') //状态流转 工作量
            ->andWhere('objectID')->eq($copyrightqz->id)
            ->andWhere('deleted')->ne(1)
            ->orderBy('id_asc')
            ->fetchAll();
        if(helper::isZeroDate($copyrightqz->devFinishedTime)) $copyrightqz->devFinishedTime=null;
        if(helper::isZeroDate($copyrightqz->firstPublicTime)) $copyrightqz->firstPublicTime=null;
        if(helper::isZeroDate($copyrightqz->outsideReviewTime)) $copyrightqz->outsideReviewTime=null;
        if(helper::isZeroDate($copyrightqz->synDate)) $copyrightqz->synDate=null;
        $copyrightqz->consumed = $cs;
        $productenroll = $this->dao->select('deleted')->from(TABLE_PRODUCTENROLL)->where('id')->eq($copyrightqz->productenrollId)->fetch();
        $copyrightqz->productenrollDeleted = $productenroll->deleted;
        return $copyrightqz;
    }

    public function sendmail($id, $actionID)
    {
        $this->loadModel('mail');
        $copyrightqz  = $this->getByID($id);
        $users = $this->loadModel('user')->getPairs('noletter');
        $copyrightqz->applicantname = zget($users,$copyrightqz->applicant);


        /* 获取后台通知中配置的邮件发信。*/
        $this->app->loadLang('custommail');
        $mailConf   = isset($this->config->global->setCopyrightqzMail) ? $this->config->global->setCopyrightqzMail : '{"mailTitle":"【待办】您有一个【著作权申请】%s，请及时登录研发过程管理平台进行处理","mailContent":"请进入【知识产权】-【清总知识产权】，查看详情，具体信息如下："}';
        $mailConf   = json_decode($mailConf);

        if($copyrightqz->status == 'returned' or $copyrightqz->status == 'returned'){
            $variables = '已退回';
        }elseif($copyrightqz->status == 'done'){
            $variables = '已通过/已完成';
        }elseif($copyrightqz->status == 'feedbackFailed'){
            $variables = '待处理';
        }else{
            $variables = '待审批';
            $mailConf->mailContent='请进入【地盘】-【待处理】-【审批】或【知识产权】-【清总知识产权】，查看详情，具体信息如下：';
        }
        $mailTitle = sprintf($mailConf->mailTitle, $variables);
        /* Get action. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        if($action->action == 'review' and $copyrightqz->status != 'feedbackFailed'){
            $rejectNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('copyrightqz')
                ->andwhere('objectID')->eq($copyrightqz->id)
                ->andwhere('version')->eq($copyrightqz->changeVersion)
                ->andwhere('status')->eq('reject')
                ->fetch('id');
            $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)
                ->where('node')->eq($rejectNode)
                ->andwhere('status')->eq('reject')
                ->fetch('extra');
            $copyrightqz->rejectreason = strip_tags(json_decode($extra)->rejectReason);
        }else{
            $copyrightqz->rejectreason = $copyrightqz->reason;
        }

        /* Get mail content. */
        $oldcwd     = getcwd();
        $modulePath = $this->app->getModulePath($appName = '', 'copyrightqz');
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

        $sendUsers = $this->getToAndCcList($copyrightqz);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        /* 处理邮件标题。*/
        //$subject = $this->getSubject($info);
        $subject = $mailTitle;

        /* Send emails. */
        $this->mail->send($toList, $subject, $mailContent, $ccList);
        if($this->mail->isError()) error_log(join("\n", $this->mail->getError()));
    }

    private function getToAndCcList($object)
    {
        /* Set toList and ccList. */
        $status = $object->status;
        if($status == 'returned'){
            $toList = $object->applicant;  //创建者
        }else if($status == 'feedbackFailed'){
            $toList = implode(',',array_keys($this->lang->copyrightqz->secondLineReviewList));
        }else if($status == 'done'){
            $toList = $object->applicant;  //创建者
        }else{
            $toList = $this->loadModel('review')->getReviewer('copyrightqz', $object->id, $object->changeVersion, $object->reviewStage);
        }
        $ccList = '';

        return array($toList, $ccList);
    }

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
                $itemName = $this->lang->copyrightqz->$item ?? $item;
                dao::$errors[$item] =  sprintf($this->lang->copyrightqz->emptyObject, $itemName);
            }
        }
    }

    public function updatedoc(){
        $data = fixer::input('post')
            ->remove('uid,files')
            ->get();
        $this->loadModel('file')->updateObjectID($this->post->uid, 0, $data->module);
        $this->file->saveUpload($data->module, 0);
    }

    public static function isClickable($data, $action)
    {
        global $app;
        $action = strtolower($action);

        if($action == 'edit') return ($data->status == 'tosubmit' or $data->status == 'returned') and ($data->applicant == $app->user->account or $app->user->account=='admin');
        if($action == 'delete') return ($data->status == 'tosubmit' or $data->status == 'returned') and ($data->applicant == $app->user->account or $app->user->account=='admin');
        if($action == 'review') return (in_array($data->status, array('togroupreview','todepartreview','toinnovatereview')))   and (in_array($app->user->account,explode(',',$data->dealUser)));
        if($action == 'reject') return ($data->status == 'feedbackFailed' or $data->status == 'synFailed') and strpos(",$data->dealUser,", ",{$app->user->account},") !== false;
        return true;
    }

    /**
     * 检查用户审批
     * @param $copyrightqz
     * @param $version
     * @param $reviewStage
     * @param $userAccount
     * @return array
     */
    public function checkAllowReview($copyrightqz, $version = 1,  $reviewStage = 0, $userAccount = '')
    {
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!$copyrightqz){
            $res['message'] = $this->lang->common->errorParamId;
            return $res;
        }
        if(!$userAccount){
            $res['message'] = $this->lang->common->errorParamUser;
            return $res;
        }
        //审核节点已经经过
        if(($version != $copyrightqz->changeVersion) || ($reviewStage != $copyrightqz->reviewStage)){
            $message = $this->lang->copyrightqz->dealError;
            $res['message'] = $message;
            return $res;
        }
        //获得当前的的审核人信息
        $reviews =  $this->loadModel('review')->getReviewer('copyrightqz', $copyrightqz->id, $copyrightqz->changeVersion, $copyrightqz->reviewStage);
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
     * Desc:工作量输入验证
     */
    public function checkConsumed(){
        //工作量验证,输入小数点后保留1位小数
        $consumed = $_POST['consumed'];
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if(empty($consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyrightqz->emptyObject, $this->lang->copyrightqz->consumed);
        }else if(!is_numeric($consumed)) {
            dao::$errors['consumed'] = sprintf($this->lang->copyrightqz->noNumeric, $this->lang->copyrightqz->consumed);
        }else if(!preg_match($reg, $consumed))
        {
            dao::$errors['consumed'] = sprintf($this->lang->copyrightqz->consumedError, $this->lang->copyrightqz->consumed);
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
     * Desc:审核(待组长审核/待负责人审核/待产创审核)
     * liuyuhan
     */
    public function review($copyrightqzId){
        $copyrightqz = $this->getByID($copyrightqzId);
        //校验数据
        //$this->checkConsumed();
        if ($this->post->result == 'pass'){
            $this->checkParamsNotEmpty($_POST, $this->config->copyrightqz->review->pass->requiredFields);
        }else if($this->post->result == 'reject' ){
            $this->checkParamsNotEmpty($_POST, $this->config->copyrightqz->review->reject->requiredFields);
        }
        if(!in_array($copyrightqz->status, array('togroupreview','todepartreview','toinnovatereview'))){
            dao::$errors[] = $this->lang->copyrightqz->statuserror;
        }

        if(!in_array($this->app->user->account,explode(',',$copyrightqz->dealUser))){
            dao::$errors[] = $this->lang->copyrightqz->dealusererror;
        }
        $this->tryError();
        if (!dao::isError()){
            //判断当前状态和审核中传入的状态是否一致，以防有同组中其他人修改
            $res = $this->checkAllowReview($copyrightqz, $this->post->changeVersion, $this->post->reviewStage, $this->app->user->account);
        }
        if(!$res['result']){
            dao::$errors['statusError'] = $res['message'];
        }
        $this->tryError();
        $postData = fixer::input('post')
            ->stripTags($this->config->copyrightqz->editor->review['id'], $this->config->allowedTags)
            ->get();
        //富文本框处理
        $this->loadModel('file')->processImgURL($postData, $this->config->copyrightqz->editor->review['id'], $this->post->uid);
        $extraObj = new stdclass();
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }
        $is_all_check_pass = false;
        $extraObj = new stdclass();
        if($this->post->result == 'reject'){
            $extraObj->rejectReason = $this->post->rejectReason;
        }
        $result = $this->loadModel('review')->check('copyrightqz', $copyrightqzId, $copyrightqz->changeVersion, $this->post->result, $this->post->comment, '', $extraObj, $is_all_check_pass);
        if($result == 'pass')
        {
            $updateData = new stdclass();
            $add = 1;
            //下一审核节点
            $nextReviewStage = $copyrightqz->reviewStage + $add;
            //下一审核状态
            if(isset($this->lang->copyrightqz->reviewNodeList[$nextReviewStage])){
                $status = $this->lang->copyrightqz->reviewNodeStatusList[$nextReviewStage];
                $updateData->reviewStage = $nextReviewStage;
                $updateData->status = $status;
            }else{
                //若已到达二线专员审批阶段，则审批通过后的状态应为【待同步清总】处理人为【成方金科】，且没有下一步的stage，stage置null
                $updateData->reviewStage = null;
                $updateData->status = 'tofeedback';
                $updateData->dealUser = 'guestcn';
            }
            $this->dao->update(TABLE_COPYRIGHTQZ)->data($updateData)->where('id')->eq($copyrightqzId)->exec();
            $this->loadModel('consumed')->record('copyrightqz', $copyrightqzId, '0', $this->app->user->account, $copyrightqz->status, $updateData->status, array());
            $next = $this->dao->select('id')->from(TABLE_REVIEWNODE)->where('objectType')->eq('copyrightqz')
                ->andWhere('objectID')->eq($copyrightqzId)
                ->andWhere('version')->eq($copyrightqz->changeVersion)
                ->andWhere('status')->eq('wait')->orderBy('stage,id')->fetch('id');
            if($next)
            {
                $this->dao->update(TABLE_REVIEWNODE)->set('status')->eq('pending')->where('id')->eq($next)->exec();
                $this->dao->update(TABLE_REVIEWER)->set('status')->eq('pending')->where('node')->eq($next)->exec();
                $reviewers = $this->loadModel('review')->getReviewer('copyrightqz', $copyrightqzId, $copyrightqz->changeVersion, $nextReviewStage);
                $this->dao->update(TABLE_COPYRIGHTQZ)->set('dealUser')->eq($reviewers)->where('id')->eq($copyrightqzId)->exec();
            }
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：通过" : "审批结论：通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('copyrightqz', $copyrightqzId, 'review', $historyComment);
        }else if($result == 'reject') {
            $this->dao->update(TABLE_COPYRIGHTQZ)->set('reviewStage')->eq('')->set('status')->eq('returned')->set('dealUser')->eq($copyrightqz->applicant)->where('id')->eq($copyrightqzId)->exec();
            $this->loadModel('consumed')->record('copyrightqz', $copyrightqzId, '0', $this->app->user->account, $copyrightqz->status, 'returned', array());
            $historyComment = (is_null(trim(str_replace('&nbsp;','',$this->post->comment))) or (trim(str_replace('&nbsp;','',$this->post->comment))=='')) ?  "审批结论：不通过" : "审批结论：不通过<br>本次操作备注：".$this->post->comment;
            $actionID = $this->loadModel('action')->create('copyrightqz', $copyrightqzId, 'review', $historyComment);
        }
        //检查此刻状态是否为【待同步清总】，处理人是否为【成方金科】，若是，则进行自动同步清总
        $copyrightqzNew = $this->getByID($copyrightqzId);
        if ($copyrightqzNew->status=='tofeedback'&& $copyrightqzNew->dealUser=='guestcn'){
            $this->pushcopyrightqz($copyrightqzId);
        }
    }

    /**
     * Project: chengfangjinke
     * Desc: 复选框字段获取具体选项值并拼接
     * liuyuhan
     */
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
    //校验必填
    public function checkFormData(){
        if($this->post->system == '')
        {
            dao::$errors['system'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->system);
            return false;
        }
        if($this->post->descType == 99 && !$this->post->devFinishedTime)
        {
            dao::$errors['devFinishedTime'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->devFinishedTime);
            return false;
        }
        if($this->post->descType == 1 && !$this->post->description)
        {
            dao::$errors['description'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->description);
            return false;
        }
        if($this->post->publishStatus == 99){
            if (!$this->post->firstPublicTime){
                dao::$errors['firstPublicTime'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->firstPublicTime);
                return false;
            }
            if ($this->post->firstPublicCountry == ''){
                dao::$errors['firstPublicCountry'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->firstPublicCountry);
                return false;
            }
            if (!$this->post->firstPublicPlace){
                dao::$errors['firstPublicPlace'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->firstPublicPlace);
                return false;
            }
        }
        if($this->post->rightObtainMethod == 1){
            if (!$this->post->isRegister){
                dao::$errors['isRegister'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->isRegister);
                return false;
            }
            if (!$this->post->isOriRegisNumChanged){
                dao::$errors['isOriRegisNumChanged'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->isOriRegisNumChanged);
                return false;
            }
        }
        if ($this->post->isRegister == 99 && !$this->post->oriRegisNum){
            dao::$errors['oriRegisNum'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->oriRegisNum);
            return false;
        }
        if ($this->post->generalDeposit == 1 && !$this->post->generalDepositType){
            dao::$errors['generalDepositType'] = sprintf($this->lang->copyrightqz->emptyObject,$this->lang->copyrightqz->generalDepositType);
            return false;
        }
    }
    //喧喧发信
    public function getXuanxuanTargetUser($obj,$objectType, $objectID, $actionType, $actionID, $actor = '')
    {
        $copyrightqz  = $obj;
        $users = $this->loadModel('user')->getPairs('noletter');
        $copyrightqz->applicantname = zget($users,$copyrightqz->applicant);

        /* Get action. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        if($action->action == 'review' and $copyrightqz->status != 'feedbackFailed'){
            $rejectNode = $this->dao->select('id')->from(TABLE_REVIEWNODE)
                ->where('objectType')->eq('copyrightqz')
                ->andwhere('objectID')->eq($copyrightqz->id)
                ->andwhere('version')->eq($copyrightqz->changeVersion)
                ->andwhere('status')->eq('reject')
                ->fetch('id');
            $extra = $this->dao->select('extra')->from(TABLE_REVIEWER)
                ->where('node')->eq($rejectNode)
                ->andwhere('status')->eq('reject')
                ->fetch('extra');
            $copyrightqz->rejectreason = strip_tags(json_decode($extra)->rejectReason);
        }else{
            $copyrightqz->rejectreason = $copyrightqz->reason;
        }

        $sendUsers = $this->getToAndCcList($copyrightqz);
        if(!$sendUsers) return;
        list($toList, $ccList) = $sendUsers;

        $mailConf   = isset($this->config->global->setCopyrightqzMail) ? $this->config->global->setCopyrightqzMail : '{"mailTitle":"【待办】您有一个【著作权申请】%s，请及时登录研发过程管理平台进行处理","mailContent":"请进入【知识产权】-【清总知识产权】，查看详情，具体信息如下："}';
        $mailConf   = json_decode($mailConf);

        if($copyrightqz->status == 'returned' or $copyrightqz->status == 'returned'){
            $variables = '已退回';
        }elseif($copyrightqz->status == 'done'){
            $variables = '已通过/已完成';
        }elseif($copyrightqz->status == 'feedbackFailed'){
            $variables = '待处理';
        }else{
            $variables = '待审批';
            $mailConf->mailContent='请进入【地盘】-【待处理】-【审批】或【知识产权】-【清总知识产权】，查看详情，具体信息如下：';
        }
        $mailConf->mailTitle = sprintf($mailConf->mailTitle, $variables);

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