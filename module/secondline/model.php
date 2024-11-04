<?php
class secondlineModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: getByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:49
     * Desc: This is the code comment. This method is called getByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID
     * @param $objectType
     * @return array
     */
    public function getByID($objectID, $objectType, $filterDeleted = 1)
    {
        $relationships = $this->dao->select('*')->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andWhere('deleted')->ne('1')
            ->andWhere('objectID')->eq($objectID)
            ->beginIF($filterDeleted)->andWhere('deleted')->eq(0)->fi()
            ->orderBy('relationType_desc')
            ->fetchAll();

        if(empty($relationships)) return [];
        $associatedObject = array();
        $dbProfix = $this->config->db->prefix;
        if($objectType == 'problem' or $objectType == 'demand')
        {
            $associatedObject['modify']       = array();
            $associatedObject['fix']          = array();
            $associatedObject['gain']         = array();
            $associatedObject['modifycncc']   = array();
            foreach($relationships as $relationship) {
                if (in_array($relationship->relationType, array('gainQz', 'fixQz'))) {
                    $table = 'infoqz';
                } elseif (in_array($relationship->relationType, array('gain', 'fix'))) {
                    $table = 'info';
                } elseif ($relationship->relationType == 'credit') {
                    $table = $relationship->relationType;
                } elseif ($relationship->relationType == 'outwardDelivery') {
                    $table = 'outwarddelivery';
                }else {
                    $table = $relationship->relationType == 'modify' ? 'modify' : 'modifycncc';
                }
                $objectTitle = $this->dao->select('code')->from($dbProfix . $table)->where('id')->eq($relationship->relationID)->fetch('code');
                $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
            }
        }

        if($objectType == 'modify' or $objectType == 'fix' or $objectType == 'gain' or $objectType == 'fixQz' or $objectType == 'gainQz')
        {
            $associatedObject['problem'] = array();
            $associatedObject['demand']  = array();
            foreach($relationships as $relationship)
            {
                if(empty($relationship->relationType)) continue;
                if($relationship->relationType == 'demand'){
                    $objectTitle = $this->dao->select('code,sourceDemand')->from($dbProfix . $relationship->relationType)->where('id')->eq($relationship->relationID)->fetch();
                    if($objectTitle){
                        $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                    }
                }
                if($relationship->relationType == 'problem'){
                    $objectTitle = $this->dao->select('code')->from($dbProfix . $relationship->relationType)->where('id')->eq($relationship->relationID)->fetch('code');
                    if($objectTitle){
                        $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                    }
                }
                if($relationship->relationType == 'secondorder'){
                    $objectTitle = $this->dao->select('code')->from($dbProfix . $relationship->relationType)->where('id')->eq($relationship->relationID)->fetch('code');
                    if($objectTitle){
                        $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                    }
                }

            }
        }

        if ($objectType == 'modifycncc') {  #待处理：关联变更单逻辑
            $associatedObject['problem'] = array();
            $associatedObject['demand'] = array();
            $associatedObject['modifycncc'] = array();
            $associatedObject['requirement'] = array();
            foreach($this->lang->modifycncc->relateTypeList as $relation=>$value){
                $associatedObject['modifycncc'][$relation]=array();
            }
            foreach ($relationships as $relationship) {
                if(empty($relationship->relationType)) continue;
                $objectTitle = $this->dao->select('code')->from($dbProfix . strtolower($relationship->relationType))->where('id')->eq($relationship->relationID)->fetch('code');
                if($relationship->relationType == 'demand'){
                    $objectTitle = $this->dao->select('code,sourceDemand')->from($dbProfix . strtolower($relationship->relationType))->where('id')->eq($relationship->relationID)->fetch();
                }
                if($relationship->relationType == 'requirement'){
                    $objectTitle = $this->dao->select('code,sourceRequirement')->from($dbProfix . strtolower($relationship->relationType))->where('id')->eq($relationship->relationID)->fetch();
                }

                if ($relationship->relationType=='modifycncc'){
                    $associatedObject['modifycncc'][$relationship->relationship][]=array($relationship->relationID,$objectTitle);
                }
                else{
                    $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                }
            }
        }
        // Obtain the problems, requirements, production changes, data correction and data acquisition associated with the project.
        if($objectType == 'project')
        {
            $associatedObject['projectModify']  = array();
            $associatedObject['projectProblem'] = array();
            $associatedObject['projectDemand']  = array();
            $associatedObject['projectFix']     = array();
            $associatedObject['projectGain']    = array();
            $associatedObject['projectModifycncc']  = array();

            $objectTables                   = array();
            $objectTables['outwardDelivery']        = 'outwarddelivery';
            $objectTables['projectModify']  = 'modify';
            $objectTables['modify']         = 'modify'; //兼容老数据
            $objectTables['projectProblem'] = 'problem';
            $objectTables['projectDemand']  = 'demand';
            $objectTables['projectFix']     = 'info';
            $objectTables['projectGain']    = 'info';
            $objectTables['projectFixQz']   = 'infoqz';
            $objectTables['projectGainQz']  = 'infoqz';
            $objectTables['projectModifycncc']  = 'modifycncc';

            foreach($relationships as $relationship)
            {
                $table = $objectTables[$relationship->relationType] ?? "";
                if(empty($table)) continue;
                $objectTitle = $this->dao->select('code')->from($dbProfix . strtolower($table))->where('id')->eq($relationship->relationID)->fetch('code');
                $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
            }
        }
        return $associatedObject;
    }


    //迭代30  年度计划详情页右侧下方几个区域顺序调整
    public function getNewByID($objectID, $objectType, $filterDeleted = 1)
    {
        $relationships = $this->dao->select('*')->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andWhere('deleted')->ne('1')
            ->andWhere('objectID')->eq($objectID)
            ->beginIF($filterDeleted)->andWhere('deleted')->eq(0)->fi()
            ->orderBy('relationType_desc')
            ->fetchAll();

        if(empty($relationships)) return [];
        $associatedObject = array();
        $dbProfix = $this->config->db->prefix;
        if($objectType == 'problem' or $objectType == 'demand')
        {
            $associatedObject['modify']       = array();
            $associatedObject['fix']          = array();
            $associatedObject['gain']         = array();
            $associatedObject['modifycncc']   = array();
            foreach($relationships as $relationship) {
                if (in_array($relationship->relationType, array('gainQz', 'fixQz'))) {
                    $table = 'infoqz';
                } elseif (in_array($relationship->relationType, array('gain', 'fix'))) {
                    $table = 'info';
                } elseif ($relationship->relationType == 'outwardDelivery') {
                    $table = 'outwarddelivery';
                }else {
                    $table = $relationship->relationType == 'modify' ? 'modify' : 'modifycncc';
                }
                $objectTitle = $this->dao->select('code')->from($dbProfix . $table)->where('id')->eq($relationship->relationID)->fetch('code');
                $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
            }
        }

        if($objectType == 'modify' or $objectType == 'fix' or $objectType == 'gain' or $objectType == 'fixQz' or $objectType == 'gainQz')
        {
            $associatedObject['problem'] = array();
            $associatedObject['demand']  = array();
            foreach($relationships as $relationship)
            {
                if(empty($relationship->relationType)) continue;
                if($relationship->relationType == 'demand'){
                    $objectTitle = $this->dao->select('code,sourceDemand')->from($dbProfix . $relationship->relationType)->where('id')->eq($relationship->relationID)->fetch();
                    if($objectTitle){
                        $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                    }
                }
                if($relationship->relationType == 'problem'){
                    $objectTitle = $this->dao->select('code')->from($dbProfix . $relationship->relationType)->where('id')->eq($relationship->relationID)->fetch('code');
                    if($objectTitle){
                        $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                    }
                }

            }
        }

        if ($objectType == 'modifycncc') {  #待处理：关联变更单逻辑
            $associatedObject['problem'] = array();
            $associatedObject['demand'] = array();
            $associatedObject['modifycncc'] = array();
            $associatedObject['requirement'] = array();
            foreach($this->lang->modifycncc->relateTypeList as $relation=>$value){
                $associatedObject['modifycncc'][$relation]=array();
            }
            foreach ($relationships as $relationship) {
                if(empty($relationship->relationType)) continue;
                $objectTitle = $this->dao->select('code')->from($dbProfix . strtolower($relationship->relationType))->where('id')->eq($relationship->relationID)->fetch('code');
                if($relationship->relationType == 'demand'){
                    $objectTitle = $this->dao->select('code,sourceDemand')->from($dbProfix . strtolower($relationship->relationType))->where('id')->eq($relationship->relationID)->fetch();
                }
                if($relationship->relationType == 'requirement'){
                    $objectTitle = $this->dao->select('code,sourceRequirement')->from($dbProfix . strtolower($relationship->relationType))->where('id')->eq($relationship->relationID)->fetch();
                }

                if ($relationship->relationType=='modifycncc'){
                    $associatedObject['modifycncc'][$relationship->relationship][]=array($relationship->relationID,$objectTitle);
                }
                else{
                    $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                }
            }
        }
        // Obtain the problems, requirements, production changes, data correction and data acquisition associated with the project.
        if($objectType == 'project')
        {
            $associatedObject['projectModify']  = array();
            $associatedObject['projectProblem'] = array();//问题单
            $associatedObject['projectDemand']  = array();//需求条目
            $associatedObject['projectFix']     = array();//数据修正单
            $associatedObject['projectGain']    = array();
            $associatedObject['projectModifycncc']  = array();
            $associatedObject['projectProblem']  = array();


            $objectTables                   = array();
            $objectTables['outwardDelivery']        = 'outwarddelivery';

            $objectTables['projectModify']  = 'modify';
            $objectTables['modify']         = 'modify'; //兼容老数据
            $objectTables['projectProblem'] = 'problem';
            $objectTables['projectDemand']  = 'demand';
            $objectTables['projectFix']     = 'info';
            $objectTables['projectGain']    = 'info';
            $objectTables['projectFixQz']   = 'infoqz';
            $objectTables['projectGainQz']  = 'infoqz';
            $objectTables['projectModifycncc']  = 'modifycncc';

            foreach($relationships as $relationship)
            {
                $table = $objectTables[$relationship->relationType] ?? "";
                if(empty($table)) continue;
                if($table == 'outwarddelivery'){
                    $objectTitle = $this->dao->select('id,code,testingRequestId,productEnrollId,modifycnccId')->from($dbProfix . strtolower($table))->where('id')->eq($relationship->relationID)->fetch();
                    if($objectTitle->testingRequestId){
                        $testingRequesInfo = $this->dao->select("code")->from(TABLE_TESTINGREQUEST)->where('id')->eq($objectTitle->testingRequestId)->fetch();
                        $associatedObject[$relationship->relationType][$relationship->relationID]['sun'][] = ['module'=>'testingrequest','code'=>$testingRequesInfo->code,'id'=>$objectTitle->testingRequestId];
                    }
                    if($objectTitle->productEnrollId){
                        $productenrollInfo = $this->dao->select("code")->from(TABLE_PRODUCTENROLL)->where('id')->eq($objectTitle->productEnrollId)->fetch();
                        $associatedObject[$relationship->relationType][$relationship->relationID]['sun'][] = ['module'=>'productenroll','code'=>$productenrollInfo->code,'id'=>$objectTitle->productEnrollId];
                    }
                    if($objectTitle->modifycnccId){
                        $modifycnccInfo = $this->dao->select("code")->from(TABLE_MODIFYCNCC)->where('id')->eq($objectTitle->modifycnccId)->fetch();
                        $associatedObject[$relationship->relationType][$relationship->relationID]['sun'][] = ['module'=>'modifycncc','code'=>$modifycnccInfo->code,'id'=>$objectTitle->modifycnccId];
                    }
                    $associatedObject[$relationship->relationType][$relationship->relationID]['parent'] = ['module'=>$objectTables['outwardDelivery'],'code'=>$objectTitle->code,'id'=>$relationship->relationID];
                }else{
                    $objectTitle = $this->dao->select('code')->from($dbProfix . strtolower($table))->where('id')->eq($relationship->relationID)->fetch('code');
                    $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
                }

            }
        }
        return $associatedObject;
    }

    /**
     * @Notes:需求条目关联生产变更单
     * @Date: 2024/2/21
     * @Time: 10:45
     * @Interface getInfoByDemand
     * @param $objectID
     * @param string $objectType
     * @param int $filterDeleted
     * @return array
     */
    public function getInfoByDemand($objectID,$objectType = 'demand',$filterDeleted = 1)
    {
        $relationships = $this->dao->select('*')->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andWhere('deleted')->ne('1')
            ->andWhere('objectID')->eq($objectID)
            ->beginIF($filterDeleted)->andWhere('deleted')->eq(0)->fi()
            ->orderBy('relationType_desc')
            ->fetchAll();

        if(empty($relationships)) return [];
        $associatedObject = array();
        $dbProfix = $this->config->db->prefix;
        $associatedObject['modify']       = array();
        $associatedObject['modifycncc']   = array();
        foreach($relationships as $relationship) {
            $objectTitle = '';
            //金信生产变更
            if($relationship->relationType == 'modify')
            {
                $objectInfoModify = $this->dao->select('code,status,externalId')->from($dbProfix . 'modify')->where('id')->eq($relationship->relationID)->fetch();
                $objectTitle = $objectInfoModify->code;
                //内部取消拼接（内部取消）字样
                if(empty($objectInfoModify->externalId) && $objectInfoModify->status == 'modifycancel')
                {
                    $objectTitle = $objectInfoModify->code.'(内部取消)';
                }
            }else if($relationship->relationType == 'modifycncc') //清总生产变更
            {
                $objectInfoCncc = $this->dao->select('code,status,giteeId')->from($dbProfix . 'modifycncc')->where('id')->eq($relationship->relationID)->fetch();
                $objectTitle = $objectInfoCncc->code;
                //内部取消拼接（内部取消）字样
                if(empty($objectInfoCncc->giteeId) && $objectInfoCncc->status == 'modifycancel')
                {
                    $objectTitle = $objectInfoCncc->code.'(内部取消)';
                }
            }else if($relationship->relationType == 'credit') //征信交付
            {
                $table =$relationship->relationType;
                $objectInfo = $this->dao->select('code,status')->from($dbProfix . $table)->where('id')->eq($relationship->relationID)->andWhere('deleted')->eq('0')->fetch();
                if(!$objectInfo){
                    continue;
                }
                $objectTitle = $objectInfo->code;
            }

            $associatedObject[$relationship->relationType][$relationship->relationID] = $objectTitle;
        }

        return $associatedObject;
    }

    /**
     * Project: chengfangjinke
     * Method: saveRelationship
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:49
     * Desc: This is the code comment. This method is called saveRelationship.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID      对象ID
     * @param $objectType    对象类型
     * @param $relationIdList 被关联对象ID
     * @param $relationType   被关联对象类型
     * @return bool
     */
    public function saveRelationship($objectID, $objectType, $relationIdList, $relationType)
    {
        // 处理多个被关联对象的情况。
        if(!is_array($relationIdList)) $relationIdList = explode(',', $relationIdList);
        if(empty($relationIdList)) return false;

        /* Handle the relationship between objects and associated objects. */
        /* 处理对象和关联对象之间的关系。*/
        $this->dao->delete()->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('relationType')->eq($relationType)
            ->exec();

        /* Handle the relationship between associated objects and objects. */
        /* 处理关联对象和对象之间的关系。*/
        $this->dao->delete()->from(TABLE_SECONDLINE)
            ->where('relationType')->eq($objectType)
            ->andWhere('relationID')->eq($objectID)
            ->andWhere('objectType')->eq($relationType)
            ->exec();

        /* 重新建立关联关系。*/
        $createdBy   = $this->app->user->account;
        $createdDate = date('Y-m-d H:i:s');
        foreach($relationIdList as $relationID)
        {
            if(empty($relationID)) continue;

            $data                 = new stdClass();
            $data->objectID       = $objectID;
            $data->objectType     = $objectType;
            $data->relationID     = $relationID;
            $data->relationType   = $relationType;
            $data->createdBy      = $createdBy;
            $data->createdDate    = $createdDate;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

            $data                 = new stdClass();
            $data->objectID       = $relationID;
            $data->objectType     = $relationType;
            $data->relationID     = $objectID;
            $data->relationType   = $objectType;
            $data->createdBy      = $createdBy;
            $data->createdDate    = $createdDate;
            $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();
        }

        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: buildSearchForm
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/9
     * Time: 7:49
     * Desc: This is the code comment. This method is called buildSearchForm.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $queryID
     * @param $actionURL
     */
    public function buildSearchForm($queryID, $actionURL,$apps=[])
    {
        $this->config->secondline->search['actionURL'] = $actionURL;
        $this->config->secondline->search['queryID']   = $queryID;
        //新增所属应用系统搜索
        if (empty($apps)){
            $apps = array('0' => '') + $this->loadModel('application')->getapplicationNameCodePairs();
        }
        $this->config->secondline->search['params']['app']['values'] = $apps;
        $depts = $this->loadModel('dept')->getOptionMenu();
        $this->config->secondline->search['params']['belongDeptIds']['values'] = $depts;
        $this->loadModel('search')->setSearchParams($this->config->secondline->search);
    }


    /**
     * Project: chengfangjinke
     * Method: saveModifycnccRelationship
     * User: chendongcheng
     * Year: 2022
     * Date: 2022/5/30
     * Time: 17:16
     * Desc: This is the code comment. This method is called saveModifycnccRelationship.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID      对象ID
     * @param $relationID 被关联对象ID
     * @param $relationship   关联关系
     * @return bool
     */
    public function saveModifycnccRelationship($objectID, $relationID, $relationship)
    {
        $createdBy = $this->app->user->account;
        $createdDate = date('Y-m-d H:i:s');
        $data = new stdClass();
        $data->objectID = $objectID;
        $data->relationID = $relationID;
        $data->objectType = 'modifycncc';
        $data->relationType = 'modifycncc';
        $data->createdBy = $createdBy;
        $data->createdDate = $createdDate;
        $data->relationship = $relationship;
        $this->dao->insert(TABLE_SECONDLINE)->data($data)->exec();

        return true;
    }

    /**
     * Project: chengfangjinke
     * Method: cleanModifycncc
     * User: chendongcheng
     * Year: 2022
     * Date: 2022/6/9
     * Time: 16:57
     * Desc: 变更单之间相互关联必须同时删除所有所有变更单相关信息，不然数据会乱
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID      对象ID
     * @return bool
     */
    public function cleanModifycncc($objectID)
    {
      /* Handle the relationship between objects and associated objects. */
      /* 处理对象和关联对象之间的关系。*/
      $this->dao->delete()->from(TABLE_SECONDLINE)
        ->where('objectType')->eq('modifycncc')
        ->andWhere('relationType')->eq('modifycncc')
        ->andWhere('objectID')->eq($objectID)
        ->andWhere('relationship')->ne('beInclude')
        ->exec();

      $this->dao->delete()->from(TABLE_SECONDLINE)
        ->where('relationType')->eq('modifycncc')
        ->andWhere('objectType')->eq('modifycncc')
        ->andWhere('relationID')->eq($objectID)
        ->andWhere('relationship')->ne('include')
        ->exec();
    }

    /**
     * @Notes:获取金信生产变更 有效联动数据
     * @Date: 2023/4/13
     * @Time: 11:14
     * @Interface getEffectiveSecondLineInfo
     * @param $demandId
     * @return mixed
     */
    public function getEffectiveSecondLineInfo($demandId)
    {
        return $this->dao->select('relationID as last_relation_id, relationType')
            ->from(TABLE_SECONDLINE)
            ->where('objectType')->eq('demand')
            ->andwhere('objectID')->eq($demandId)
            ->andwhere('deleted')->eq(0)
            ->andwhere('relationType')->in('modify,gain,modifycncc,gainQz,outwardDelivery')
            ->orderBY("id_asc")
            ->fetchAll();
    }

    /**
     * @Notes: 获取不同状态数据集合
     * @Date: 2023/8/25
     * @Time: 15:40
     * @Interface getSecondInfo
     * @param $demandIDs
     * @param $objectType
     * @param $relationType
     * @param string $field
     * @return mixed
     */
    public function getSecondInfo($demandIDs,$objectType,$relationType,$field = '*')
    {
        return $this->dao->select($field)
            ->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andwhere('objectID')->in($demandIDs)
            ->andwhere('deleted')->eq(0)
            ->andwhere('relationType')->eq($relationType)
            ->fetchAll();
    }

    /**
     * 取消绑定
     *
     * @param $objectType
     * @param $objectID
     * @param array $relationType
     * @return bool
     */
    public function cancelRelationship($objectType, $objectID, $relationType = []){
        if(!($objectType && $objectID)){
            return false;
        }
        /* 处理对象和关联对象之间的关系。*/
        $this->dao->delete()->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->beginIF($relationType)->andWhere('relationType')->in($relationType)->fi()
            ->exec();

        /* Handle the relationship between associated objects and objects. */
        /* 处理关联对象和对象之间的关系。*/
        $this->dao->delete()->from(TABLE_SECONDLINE)
            ->where('relationType')->eq($objectType)
            ->andWhere('relationID')->eq($objectID)
            ->beginIF($relationType)->andWhere('objectType')->in($relationType)->fi()
            ->exec();
        return true;
    }

    /**
     * 获得关联ids
     *
     * @param $objectType
     * @param $objectId
     * @param $relationType
     * @return array
     */
    public function getRelationIds($objectType, $objectId, $relationType){
        $data = [];
        if(!($objectType && $objectId && $relationType)){
            return $data;
        }
       $ret = $this->dao->select('relationID')
            ->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andwhere('objectID')->eq($objectId)
           ->beginIF(is_array($relationType))->andWhere('relationType')->in($relationType)->fi()
           ->beginIF(!is_array($relationType))->andwhere('relationType')->eq($relationType)->fi()
            ->andwhere('deleted')->eq(0)
            ->fetchAll();
        if($ret){
            $data = array_column($ret, 'relationID');
        }
        return $data;
    }


    /**
     * 获得关联列表
     *
     * @param $objectType
     * @param $objectId
     * @param $relationType
     * @return array
     */
    public function getRelationList($objectType, $objectId, $relationType){
        $data = [];
        if(!($objectType && $objectId && $relationType)){
            return $data;
        }
        $ret = $this->dao->select('*')
            ->from(TABLE_SECONDLINE)
            ->where('objectType')->eq($objectType)
            ->andwhere('objectID')->eq($objectId)
            ->beginIF(is_array($relationType))->andWhere('relationType')->in($relationType)->fi()
            ->beginIF(!is_array($relationType))->andwhere('relationType')->eq($relationType)->fi()
            ->andwhere('deleted')->eq(0)
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

}
