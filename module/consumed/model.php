<?php
class consumedModel extends model
{
    /**
     * Project: chengfangjinke
     * Method: record
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:10
     * Desc: This is the code comment. This method is called record.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectType
     * @param $objectID
     * @param $consumed
     * @param $account
     * @param $before
     * @param $after
     * @param array $mailto
     * @param null $extra
     * @params $version
     * @param $createdDate
     */
    public function record($objectType, $objectID, $consumed, $account, $before, $after, $mailto = array(), $extra = null, $version = 0, $createdDate = '')
    {
        //问题反馈单前后状态一至不记录状态流转（迭代33）
        if($objectType == 'problem' && $extra == 'problemFeedBack' && $before == $after){
            return true;
        }
        //添加判断
        //$mailto = trim(implode(',', $mailto), ',');
        if(is_array($mailto)){
            if(count($mailto) != 0 ){
                $mailto = trim(implode(',', $mailto), ',');
            }else{
                $mailto = '';
            }
        }

        $workload     = empty($_POST['workload']) ? array() : $_POST['workload'];
        $relevantUser = empty($_POST['relevantUser']) ? array() : $_POST['relevantUser'];
        $details      = array();
        if(!empty($relevantUser)){
            $temp = 0;
            foreach($relevantUser as $index => $user)
            {
                if($user){
                    $details[$temp]['account']  = $user;
                    $details[$temp]['workload'] = isset($workload[$index]) ? $workload[$index] : '0';
                    $temp++;
                }
            }
        }

        // 获取用户信息
        $userInfo = $this->loadModel('user')->getUserInfo($account, 'dept');
        // 用户部门
        $deptId = isset($userInfo->dept) ? $userInfo->dept:0;

        $data = new stdclass();
        $data->objectType  = $objectType;
        $data->objectID    = $objectID;
        $data->consumed    = $consumed;
        $data->account     = $account;
        $data->deptId      = $deptId;
        $data->before      = $before;
        $data->after       = $after;
        $data->mailto      = $mailto;
        $data->details     = empty($details) ? '' : json_encode($details);
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = date('Y-m-d H:i:s');
        if($createdDate){
            $data->createdDate = $createdDate;
        }
        if(isset($extra) && ($extra != '')){
            if($objectType == 'review' || $objectType == 'reviewmeeting' || $objectType == 'change'){
                $data->extra  = json_encode($extra);
            }else{
                $data->extra  = $extra;
            }
        }

        if($version > 0){
            $data->version = $version;
        }
        if($objectType == 'review'){
            $data->reviewStage = $this->getReviewStage($objectType, $before, $after);
        }
        $this->dao->insert(TABLE_CONSUMED)->data($data)->exec();

        //如果是问题和需求单则记录相关配合人员的工作量信息
        //两个字段为空
        $data->mailto  = '';
        $data->details = '';
        $data->parentId = $this->dao->lastInsertID();

        if($objectType == 'demand' or $objectType == 'problem')
        {
            if(!empty($relevantUser)){
                foreach($relevantUser as $index => $user)
                {
                    if($user){
                        $data->account     = $user;
                        $data->consumed    = $workload[$index];
                        $this->dao->insert(TABLE_CONSUMED)->data($data)->exec(); //关联插入
                    }
                }
            }
            //2022-4-20 更新解决时间
            if($after == 'closed' || $after == 'delivery'){
                if($objectType == 'demand') {$table = TABLE_DEMAND;}
                elseif($objectType == 'problem') {$table = TABLE_PROBLEM;}
                //$this->dao->update($table)->set('solvedTime')->eq(date('Y-m-d H:i:s'))->where('id')->eq($objectID)->exec();
            }
        }
    }

    /**
     * 定时任务更新工时
     * @param $objectType
     * @param $objectID
     * @param $consumed
     * @param $before
     * @param $after
     */
    public function recordAuto($objectType, $objectID, $consumed, $before, $after)
    {
        if(empty($objectID)) return;
        $this->record($objectType, $objectID, $consumed, 'guestjk', $before, $after);

    }
    /**
     * Project: chengfangjinke
     * Method: getWorkloadDetails
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:10
     * Desc: This is the code comment. This method is called getWorkloadDetails.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $consumedID
     * @return array|mixed
     */
    public function getWorkloadDetails($consumedID)
    {
        $details = $this->dao->select('details')->from(TABLE_CONSUMED)->where('id')->eq($consumedID)->fetch('details');
        if(!empty($details))
        {
            $details = $this->getConsumedDetailsArray($details);
        }
        else
        {
            $details = array();
        }
        return $details;
    }

    /**
     * Desc:处理解决时间状态为delivery
     * Date: 2022/3/24
     * Time: 16:01
     *
     * @param $type
     * @param $objectID
     * @return mixed
     *
     */
    public function getDealDate($type,$objectID)
    {
        $dealDate = $this->dao->select('id,createdDate')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($type)
            ->andWhere('objectID')->eq($objectID)
            ->andWhere('after')->eq('delivery')
            ->orderBy('id desc')
            ->fetch();
        return $dealDate;
    }

    /**
     * Project: chengfangjinke
     * Method: getObjectByID
     * User: Tony Stark
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:11
     * Desc: This is the code comment. This method is called getObjectByID.
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID
     * @param $objectType
     * @param string $before
     * @return mixed
     */
    public function getObjectByID($objectID, $objectType, $before = '',$order='')
    {
        $details = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('deleted')->eq(0)
            ->beginIF($before)->andWhere('`after`')->eq($before)->fi()
            ->beginIF($order)->orderBy($order)->fi()
            ->fetch();

        return $details;
    }
    /**
     * @param $objectID
     * @param $objectType
     * @param string $before
     * @return mixed
     */
    public function getObjectByIDToMax($objectID, $objectType, $before = '')
    {
        $details = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('deleted')->eq(0)
            ->beginIF($before)->andWhere('`after`')->eq($before)->fi()
            ->orderBy('id desc')
            ->fetch();

        return $details;
    }
    /**
     * Project: chengfangjinke
     * Method: checkIsLastConsumed
     * User: wangjiurong
     * Year: 2021
     * Date: 2021/10/8
     * Time: 13:11
     * Desc: 检查是否是最后一条工作量信息
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $consumedID
     * @param $objectID
     * @param $objectType
     * @return bool
     */
    public function checkIsLastConsumed($consumedID, $objectID, $objectType){
        $isLastConsumed = false;
        if(!($consumedID && $objectType && $objectID)){
            return $isLastConsumed;
        }
        //排除问题反馈单的操作记录
        $maxConsumedId = $this->dao->select('id')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('parentId')->eq('0')
            ->andWhere('deleted')->eq('0')
            ->beginIF('problem' == $objectType)->andWhere('`extra`')->eq('')->fi()
            ->orderBy('id_desc')
            ->fetch('id');
        if(!$maxConsumedId){
            return $isLastConsumed;
        }
        if($maxConsumedId == $consumedID){
            $isLastConsumed = true;
        }
        return $isLastConsumed;
    }

    /**
     * 查询是否是第一条工作量信息
     *
     * @param $consumedID
     * @param $objectID
     * @param $objectType
     * @return bool
     */
    public function checkIsFirstConsumed($consumedID, $objectID, $objectType){
        $isFirstConsumed = false;
        if(!($consumedID && $objectType && $objectID)){
            return $isFirstConsumed;
        }
        //排除问题反馈单的操作记录
        $minConsumedId = $this->dao->select('id')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('parentId')->eq('0')
            ->andWhere('deleted')->eq('0')
            ->beginIF('problem' == $objectType)->andWhere('`extra`')->eq('')->fi()
            ->orderBy('id_asc')
            ->fetch('id');
        if(!$minConsumedId){
            return $isFirstConsumed;
        }
        if($minConsumedId == $consumedID){
            $isFirstConsumed = true;
        }
        return $isFirstConsumed;
    }

    /**
     *获得工作量details组成数组信息
     *
     * @param string $details
     * @return array
     */
    public function getConsumedDetailsArray($details = ''){
        $detailsArray = [];
        if(!$details){
            return $detailsArray;
        }
        $details = json_decode($details);
        foreach ($details as $val){
            if($val->account){
                $detailsArray[] = $val;
            }
        }
        return $detailsArray;
    }

    /**
     * 检查工作量信息
     *
     * @param bool $isAllowEmpty
     * @return false
     */
    public function checkPostDetails($isAllowEmpty = true){
        $checkRes = false;
        $relevantUser = empty($_POST['relevantUser']) ? array() : $_POST['relevantUser'];
        $workload     = empty($_POST['workload']) ? array() : $_POST['workload'];
        $count = count(array_filter($relevantUser));

        //没有设置工作量信息
        if($count == 0){
            if($isAllowEmpty){
                $checkRes = true;
            }else{
                dao::$errors[] = $this->lang->consumed->consumedEmpty;
            }
            return $checkRes;
        }
        //设置一组工作量信息
        if($count == 1){
            $consumedUser     = $relevantUser[0];
            $consumedWorkTime =  isset($workload[0]) ? $workload[0] : 'no';
            if($isAllowEmpty){

                if(empty($consumedUser) && empty($consumedWorkTime)){
                    $checkRes = true;
                    return $checkRes;
                }
                //检查用户
                if(empty($consumedUser)){
                    dao::$errors[] = $this->lang->consumed->detailsUserEmpty;
                    return $checkRes;
                }
                //检查时间信息
                if($consumedWorkTime != 'no'){
                    $checkWorkTimeRes = $this->checkConsumedInfo($consumedWorkTime, true);
                    if(!$checkWorkTimeRes){
                        return $checkRes;
                    }
                }

            }else{
                if(empty($consumedUser) && empty($consumedWorkTime)){
                    dao::$errors[] = $this->lang->consumed->detailsEmpty;
                    return $checkRes;
                }
                //检查用户
                if(empty($consumedUser)){
                    dao::$errors[] = $this->lang->consumed->detailsUserEmpty;
                    return $checkRes;
                }
                //检查时间信息
                $checkWorkTimeRes = $this->checkConsumedInfo($consumedWorkTime, true);
                if(!$checkWorkTimeRes){
                    return $checkRes;
                }
            }
            $checkRes = true;
            return $checkRes;
        }

        //设置多组工作量信息
        $users = [];
        foreach($relevantUser as $index => $user) {
            $consumedWorkTime = isset($workload[$index]) ? $workload[$index] : 'no';
            $key = $index + 1;
            if(!$user){
                dao::$errors[] = sprintf($this->lang->consumed->detailsUserNEmpty, $key);
                return $checkRes;
            }
            if($consumedWorkTime != 'no'){
                $checkWorkTimeRes = $this->checkConsumedInfo($consumedWorkTime, true, $key);
                if(!$checkWorkTimeRes){
                    return $checkRes;
                }
            }

            //检查用户重复
            if(in_array($user, $users)){
                dao::$errors[] = sprintf($this->lang->consumed->detailsUserNError, $key);
                return $checkRes;
            }
            $users[] = $user;
        }
        $checkRes = true;
        return $checkRes;
    }

    /**
     *获得post提交的工作量详情信息
     *
     * @return false|string
     */
    public function getPostDetails(){
        $details = '';
        $workload     = empty($_POST['workload']) ? array() : $_POST['workload'];
        $relevantUser = empty($_POST['relevantUser']) ? array() : $_POST['relevantUser'];
        $detailsArray = array();
        if(!empty($relevantUser)){
            $temp = 0;
            foreach($relevantUser as $index => $user)
            {
                if($user){
                    $detailsArray[$temp]['account']  = $user;
                    //$detailsArray[$temp]['workload'] = (int)$workload[$index];
                    $detailsArray[$temp]['workload'] = isset($workload[$index]) ? $workload[$index] : '';
                    $temp++;
                }
            }
            $details = json_encode($detailsArray);
        }
        return $details;
    }

    /**
     * 校验工作量信息
     *
     * @param $consumed
     * @param false $isRelevant
     * @param int $relevantKey
     * @return bool
     */
    public function checkConsumedInfo($consumed, $isRelevant = false, $relevantKey = 0){
        $checkRes = false;
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/';
        if($isRelevant){
            if($relevantKey){
                if (!$consumed) {
                    dao::$errors[] = sprintf($this->lang->consumed->detailsWorkloadNEmpty, $relevantKey);
                    return $checkRes;
                }

                if (!preg_match($reg, $consumed)) {
                    dao::$errors[] = sprintf($this->lang->consumed->detailsWorkloadNError, $relevantKey);
                    return $checkRes;
                }
            }else{
                if (!$consumed) {
                    dao::$errors[] = $this->lang->consumed->detailsWorkloadEmpty;
                    return $checkRes;
                }

                if (!preg_match($reg, $consumed)) {
                    dao::$errors[] = $this->lang->consumed->detailsWorkloadError;
                    return $checkRes;
                }
            }

        }else{
            if(!$relevantKey){
                $relevantKey = 'consumed';
            }
            if (!$consumed) {
                dao::$errors[$relevantKey] = $this->lang->consumed->consumedEmpty;
                return $checkRes;
            }
            if(!preg_match($reg, $consumed)){
                dao::$errors[$relevantKey] = $this->lang->consumed->consumedError;
                return $checkRes;
            }

        }
        $checkRes = true;
        return $checkRes;
    }

    /**
     * 检查允许两位小数的工作量信息
     *
     * @param $consumed
     * @return array
     */
    public function checkConsumedTwoDecimal($consumed){
        $res = [
            'result'  => false,
            'message' => '',
        ];
        $reg = '/^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/';
        if (!$consumed) {
            $res['message'] = $this->lang->consumed->consumedEmpty;
            return $res;
        }

        if (!preg_match($reg, $consumed)) {
            $res['message'] = $this->lang->consumed->consumedTwoError;
            return $res;
        }
        $res['result'] = true;
        return $res;
    }


    /* 处理相关配合人员的记录（增删改） */
    public function dealRelevantUser($consumedID)
    {
        /* 处理相关配合人员的记录（增删改） */
        $relevantUser = empty($_POST['relevantUser']) ? array() : $_POST['relevantUser'];
        $workload     = empty($_POST['workload']) ? array() : $_POST['workload'];

        /*数据库查询当前处理人员信息*/
        $data = $this->dao->select('*')->from(TABLE_CONSUMED)
                ->where('id')->eq($consumedID)
                ->fetch();

        /*数据库中的配合人员信息，和表单中的名单对比，人员重复的修改，表单中新增加的则新增，数据库中多出来的逻辑删除*/
        $details =  $this->dao->select('*')->from(TABLE_CONSUMED)
                        ->where('parentId')->eq($consumedID)
                        ->fetchAll();

        $data2 = new stdclass();
        $data2->objectType  = $data->objectType;
        $data2->objectID    = $data->objectID;
        $data2->consumed    = 0;
        $data2->account     = '';
        $data2->before      = $data->before;
        $data2->after       = $data->after;
        $data2->createdBy   = $data->createdBy;
        $data2->createdDate = $data->createdDate;
        $data2->parentId    = $consumedID;

        if(!empty($relevantUser)){
            foreach($relevantUser as $index => $user)
            {
                if($user)
                {
                    foreach ($details as $detail)
                    {
                        /* 该配合人员在数据库中存在，进行修改操作 */
                        if ($detail->account == $user) {
                            //进行修改操作
                            $detail->consumed = isset($workload[$index]) ? $workload[$index] : '0';
                            $detail->deleted = 0;
                            $this->dao->update(TABLE_CONSUMED) //处理相关配合人员的记录
                                ->data($detail)
                                ->where('id')->eq($detail->id)
                                ->exec();
                            //标记配合人员为该用户的这条记录已经操作过了，没操作过的需要删除
                            $detail->account = '';
                            //标记配合人员为该用户的这条记录已经操作过了，没操作过的需要插入数据库进行新增
                            $user = '';
                            break;
                        }
                    }

                    //提交表单中的配合人员还没有进行操作，则插入数据库,新增一条记录
                    if($user != ''){
                        $data2->account     = $user;
                        $data2->consumed    = isset($workload[$index]) ? $workload[$index] : '0';
                        $this->dao->insert(TABLE_CONSUMED)->data($data2)->exec(); //预先插入
                    }
                }
            }

            //进行删除操作
            foreach ($details as $detail)
            {
                if($detail->account != '')
                {
                    $detail->deleted = 1;
                    $this->dao->update(TABLE_CONSUMED) //进行删除操作
                        ->data($detail)
                        ->where('id')->eq($detail->id)
                        ->exec();
                }
            }
        }
    }

    /**
     * 逻辑删除原有状态
     * @param $objectType
     * @param $objectId
     * @param $account
     * @param $afterStatus
     */
    public function remove($objectType, $objectId, $account, $afterStatus)
    {
        $detail = new stdClass();
        $detail->deleted = 1;
        $this->dao->update(TABLE_CONSUMED) //进行删除操作
            ->data($detail)
            ->where('objectType')->eq($objectType)
            ->andwhere('objectID')->eq($objectId)
            ->andwhere('account')->eq($account)
            ->andwhere('after')->eq($afterStatus)
            ->exec();
    }

    /**
     * 更新原有状态
     * @param $objectType
     * @param $objectId
     * @param $account
     * @param $afterStatus
     */
    public function update($id,$objectType, $objectID, $consumed, $account, $before, $after, $mailto = array(), $extra = null, $version = 0)
    {
        $data = new stdclass();
        if(is_array($mailto)){
            if(count($mailto) != 0 ){
                $mailto = trim(implode(',', $mailto), ',');
            }else{
                $mailto = '';
            }
        }
        $data->objectType  = $objectType;
        $data->objectID    = $objectID;
        $data->consumed    = $consumed;
        $data->account     = $account;
        $data->before      = $before;
        $data->after       = $after;
        $data->mailto      = $mailto;
        $data->details     = empty($details) ? '' : json_encode($details);
        $data->createdBy   = $this->app->user->account;
        $data->createdDate = date('Y-m-d H:i:s');
        $res = $this->dao->update(TABLE_CONSUMED)
//            ->data($data)->autoCheck()
            ->data($data)
            ->where('id')->eq((int)$id)->exec();
        return $res;
    }

    /**
     * 根据id account 获取准确评审时间
     * @param $objectID
     * @param $objectType
     * @param $account
     * @return mixed
     */
    public function getByIdToDate($objectID, $objectType, $account )
    {
        $details = $this->dao->select('account,createdDate')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('account')->eq($account)
            ->fetchPairs();

        return $details;
    }

    /**
     * Project: chengfangjinke
     * Method: checkIsLastConsumed
     * User: shixuyang
     * Year: 2022
     * Date: 2022/7/27
     * Desc: 获取最后一条工作量信息
     * remarks: The sooner you start to code, the longer the program will take.
     * Product: PhpStorm
     * @param $objectID
     * @param $objectType
     * @return bool
     */
    public function getLastConsumed($objectID, $objectType){
        $lastConsumed = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectID')->eq($objectID)
            ->andWhere('objectType')->eq($objectType)
            ->andWhere('parentId')->eq('0')
            ->andWhere('deleted')->eq('0')
            ->orderBy('id_desc')
            ->limit(1)
            ->fetch();
        return $lastConsumed;
    }

    /**
     *获得评审阶段
     *
     * @param $objectType
     * @param $before
     * @param $after
     * @return string
     */
    public function getReviewStage($objectType, $before, $after){
        $reviewStage = '';
        if($objectType == 'review'){
            $reviewStage = $this->loadModel('review')->getReviewStage($before, $after);
        }
        return $reviewStage;
    }

    /**
     * 获取工时信息
     *
     * @param $objectType
     * @param $objectID
     * @return array
     */
    public function getConsumed($objectType, $objectID){
        $data = [];
        if(!($objectType && $objectID)){
            return $data;
        }
        $ret = $this->dao->select('*')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($objectType)
            ->andWhere('objectID')->eq($objectID)
            ->orderBy('createdDate')
            ->fetchAll();
        if($ret){
            $data = $ret;
        }
        return $data;
    }

    /**
     * @Notes:
     * @Date: 2023/4/19
     * @Time: 14:05
     * @Interface getCreatedDate
     * @param $type
     * @param $objectID
     * @param $before
     * @param $after
     * @return mixed
     */
    public function getCreatedDate($type,$objectID,$before,$after)
    {
        $createdDate = $this->dao->select('id,createdDate')->from(TABLE_CONSUMED)
            ->where('objectType')->eq($type)
            ->beginIF(isset($objectID) && !empty($objectID))->andWhere('objectID')->eq($objectID)
            ->beginIF(isset($before)  && !empty($before))->andWhere('`before`')->eq($before)
            ->beginIF(isset($after)  && !empty($after))->andWhere('`after`')->eq($after)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetch();
        return $createdDate;
    }

    /**
     * 获得单条信息
     *
     * @param $id
     * @param string $select
     * @return mixed
     */
    public function getById($id, $select = '*'){
        $ret = $this->dao->select($select)
            ->from(TABLE_CONSUMED)
            ->where('id')->eq($id)
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetch();
        return $ret;
    }
}
