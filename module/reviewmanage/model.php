<?php

class reviewmanageModel extends model
{
    /**
     * 获取与我相关 - 项目评审列表
     * 条件：1、未关闭的 2、评审主席 评审专员 评审专家 评审参与人员  QA预审 初审部门接口人 初审主审人员 初审参与人员  质量部CM
     * @param $orderBy
     * @return array
     */
    public function getByMeUserReviewList($orderBy, $pager, $flag)
    {
        $this->app->loadLang('review');
        $list = array_merge($this->lang->review->allowAssignStatusList, $this->lang->review->allowReviewStatusList);
        //status ！= list  dealuser
        //其他 用reviewer
        $account = $this->app->user->account;
        //查询所有未关闭评审
        $reviews = $this->dao->select('t1.id,t1.title,t1.createdDept,t1.status,t1.dealUser,t1.object,t1.type,t1.grade,t1.rejectStage,t1.reviewer,t1.version,t1.owner,t1.expert,t1.reviewedBy,t1.deadline,t1.createdBy,t1.createdDate,t1.editBy,t1.editDate,t2.mark')
            ->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t2')
            ->on('t1.project = t2.project')
            ->where('t1.deleted')->eq(0)
            ->orderBy($orderBy)
            ->fetchAll('id');

        $array = array();
        $users = $this->loadModel('user')->getPairs('noletter');
        $close = array_flip(array_filter(array_keys($this->lang->review->closeList)));
        foreach ($reviews as $key => $reviewInfo) {
            $status = $reviewInfo->status;
            if (isset($close[$status])) continue;
            $reviews[$key]->statusDesc = $this->loadModel('review')->getReviewStatusDesc($status, $reviewInfo->rejectStage);
            /*获取初审相关数据*/
            $version = $reviewInfo->version;
            $dataTrial = $this->loadModel('review')->getTrial($reviewInfo, $version, $users, 1);
            $deptjkr = explode(' ', $dataTrial['deptjkr']);
            $deptzs = explode(' ', $dataTrial['deptzs']);
            $deptjoin = explode(' ', $dataTrial['deptjoin']);
            $expert = explode(',', $reviewInfo->expert);
            $reviewedBy = explode(',', $reviewInfo->reviewedBy);
            $User = array(
                $reviewInfo->owner,
                $reviewInfo->reviewer,
                $reviewInfo->qa,
                $reviewInfo->qualityCm,
            );
            $allUser = array_merge($User, $deptjkr, $deptzs, $deptjoin, $expert, $reviewedBy);
            $allUser = array_filter(array_unique($allUser));
            $allUser = array_flip($allUser);

            $list = array_flip($list);
            if (isset($allUser[$account])) {
                if (isset($list[$status])) {
                    $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewInfo->version, $reviewInfo->reviewStage);
                    $reviews[$key]->reviewers = $reviewers;
                    $reviews[$key]->dealUser = $reviewers;
                    $array[] = $reviews[$key];
                } else {
                    $reviews[$key]->dealUser = $reviewInfo->dealUser;
                    $reviews[$key]->reviewers = $reviewInfo->dealUser;
                    $array[] = $reviews[$key];
                }
            }
        }
        if ($flag) {
            $pager->recTotal = count($array);
            $start = ($pager->pageID - 1) * $pager->recPerPage;
            $arr = array_slice($array, $start, $pager->recPerPage);
        } else {
            $arr = count($array) ? count($array) : 0;
        }
        return $arr;
    }

    /**
     * Print datatable cell.
     * @param object $col
     * @param object $review
     * @param array $users
     * @param array $products
     * @access public
     * @return void
     */

    public function printCell($col, $review, $users, $products, $tag)
    {
        $reviewID = $review->id;
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList = inlink('view', "reviewID=$review->id");
        $account = $this->app->user->account;
        $id = $col->id;
        $outsideList1 = array('' => '') + $this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 = array('' => '') + $this->loadModel('user')->getUsersNameByType('outside');
        //$relatedUsers  = $this->loadModel('user')->getPairs('noletter');
        if ($col->show) {
            $class = "c-$id";
            $title = '';
            if ($id == 'id') $class .= ' cell-id';
            if ($id == 'status') {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->review->statusLabelList, $review->status, '');
                $title = "title='{$name}'";
            }
            if ($id == 'result') {
                $class .= ' status-' . $review->result;
            }
            if ($id == 'title') {
                $class .= ' text-left';
                $title = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";

            $dataTrial = $this->loadModel('review')->getTrial($reviewID, $review->version, $users, 2);
            $trialDeptIds = $dataTrial['deptid'];
            $trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
            $trialAdjudicatingOfficer = $dataTrial['deptzs'];
            $trialJoinOfficer = $dataTrial['deptjoin'];

            switch ($id) {
                case 'id':
                    if ($tag and $tag!= 5) {
                        echo "<div class='checkbox-primary'><input type='checkbox' name='idList[]' value='$review->id' id='idList.$review->id'> <label for='idList.$review->id'></label></div>";
                    }
                    if ($canBatchAction) {
                        echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
                    } else {
                        printf('%03d', $review->id);
                    }
                    break;
                case 'title':
                    if($tag ==5){
                        echo html::a(helper::createLink('reviewmanage', 'deptview', "reviewID=$review->id"), $review->title);
                    }else{
                        echo html::a(helper::createLink('reviewmanage', 'view', "reviewID=$review->id"), $review->title);
                    }

                    break;
                case 'product':
                    echo zget($products, $review->product);
                    break;
                case 'object':
                    $txt = '';
                    $object = explode(',', $review->object);
                    foreach ($object as $obj) {
                        $obj = trim($obj);
                        if (empty($obj)) continue;
                        $txt .= zget($this->lang->review->objectList, $obj) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'version':
                    echo $review->version;
                    break;

                case 'status':
                    echo $review->statusDesc;

                    break;
                case 'type':
                    echo zget($this->lang->review->typeList, $review->type, '');
                    break;

                case 'grade':
                    if ($review->isConfirmGrade == 1) {
                        echo zget($this->lang->review->gradeList, $review->grade, '');
                    } else {
                        echo '';
                    }
                    break;
                case 'meetingCode':
                    $meetingCode = '';
                    if($review->grade =='meeting'){
                        $meetingCode = $review->meetingCode;
                    }
                    echo  $meetingCode;
                    break;
                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if($review->meetingPlanTime != '0000-00-00 00:00:00' && $review->grade =='meeting'){
                        $meetingPlanTime = $review->meetingPlanTime;
                    }
                    echo $meetingPlanTime;
                    break;

                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if ($review->meetingRealTime != '0000-00-00 00:00:00') {
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo $meetingRealTime;
                    break;

                case 'owner':
                    $txt = '';
                    $owners = explode(',', $review->owner);
                    foreach ($owners as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'expert':
                    $txt = '';
                    $experts = explode(',', $review->expert);
                    foreach ($experts as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'reviewedBy':
                    $txt = '';
                    $reviewedBy = explode(',', $review->reviewedBy);
                    foreach ($reviewedBy as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($outsideList1, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'outside':
                    $txt = '';
                    $outside = explode(',', $review->outside);
                    foreach ($outside as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($outsideList2, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'meetingPlanExport':
                    $txt = '';
                    $meetingPlanExport = explode(',', $review->meetingPlanExport);
                    foreach($meetingPlanExport as $account) {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'relatedUsers':
                    $txt = '';
                    $relatedUsers = explode(',', $review->relatedUsers);

                    foreach ($relatedUsers as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'createdBy':
                    echo zget($users, $review->createdBy, '');
                    break;
                case 'reviewer':
                    echo zget($users, $review->reviewer, '');
                    break;
                case 'createdDate':
                    echo $review->createdDate;
                    break;
                case 'deadline':
                    echo $review->deadline;
                    break;
                case 'projectType':
                    echo  zget($this->lang->projectplan->typeList, $review->projectType,'');
                    break;

                case 'isImportant':
                    echo zget($this->lang->review->isImportantList, $review->isImportant,'');
                    break;
                case 'lastReviewedDate':
                    echo $review->lastReviewedDate;
                    break;
                case 'lastAuditedDate':
                    echo $review->lastAuditedDate;
                    break;
                case 'result':
                    echo zget($this->lang->review->resultList, $review->resulty, '');
                    break;
                case 'auditResult':
                    echo zget($this->lang->review->auditResultList, $review->auditResulty, '');
                    break;
                case 'dealUser':
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    $txt = '';
                    foreach ($dealUser as $account)
                        $txt .= zget($users, $account, '') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'deadDate':
                    $endDate =  $review->endDate != '0000-00-00 00:00:00' ? $review->endDate: '';
                    if(!empty($endDate)){
                        $endDate = date('Y-m-d', strtotime($endDate));
                    }
                    echo '<div class="ellipsis" title="' . $endDate . '">' . $endDate .'</div>';
                    break;
                case 'editBy':
                    echo zget($users, $review->editBy, '');
                    break;
                case 'editDate':
                    echo '<div class="ellipsis" title="' . $review->editDate . '">' . $review->editDate . '</div>';
                    break;

                case 'createdDept':
                    echo '<div class="ellipsis" title="' .zget($deptMap, $review->createdDept, ''). '">' .zget($deptMap, $review->createdDept, ''). '</div>';
                    break;
                case 'closePerson':
                    echo zget($users, $review->closePerson, '');
                    break;
                case 'closeTime':
                    echo '<div class="ellipsis" title="' . $review->closeTime . '">' . $review->closeTime . '</div>';
                    break;
                case 'qualityQa':
                    echo zget($users, $review->qualityQa, '');
                    break;
                case 'trialDept':
                    echo '<div class="ellipsis" title="' . $trialDeptIds . '">' . $trialDeptIds . '</div>';
                    break;

                case 'trialDeptLiasisonOfficer':
                    echo '<div class="ellipsis" title="' . $trialDeptLiasisonOfficer . '">' . $trialDeptLiasisonOfficer . '</div>';
                    break;

                case 'trialAdjudicatingOfficer':
                    echo '<div class="ellipsis" title="' . $trialAdjudicatingOfficer . '">' . $trialAdjudicatingOfficer . '</div>';
                    break;

                case 'trialJoinOfficer':
                    echo '<div class="ellipsis" title="' . $trialJoinOfficer . '">' . $trialJoinOfficer . '</div>';
                    break;

                case 'preReviewDeadline':
                    echo $review->preReviewDeadline;
                    break;
                case 'firstReviewDeadline':
                    echo $review->firstReviewDeadline;
                    break;
                case 'closeDate':
                    echo $review->closeDate;
                    break;
                case 'qa':
                    echo zget($users, $review->qa);
                    break;
                case 'qualityCm':
                    echo zget($users, $review->qualityCm);

                case 'suspendBy':
                    echo zget($users, $review->suspendBy);
                    break;

                case 'suspendTime':
                    $suspendTime = $review->suspendTime != '0000-00-00 00:00:00' ? $review->suspendTime: '';
                    echo '<div class="ellipsis" title="' . $suspendTime . '">' . $suspendTime .'</div>';
                    break;

                case 'suspendReason':
                    $suspendReason = $review->suspendReason;
                    echo '<div class="ellipsis" title="' . $suspendReason . '">' . $suspendReason .'</div>';
                    break;

                case 'renewBy':
                    echo zget($users, $review->renewBy);
                    break;

                case 'renewTime':
                    $renewTime = $review->renewTime != '0000-00-00 00:00:00' ? $review->renewTime: '';
                    echo '<div class="ellipsis" title="' . $renewTime . '">' . $renewTime .'</div>';
                    break;

                case 'renewReason':
                    $renewReason = $review->renewReason;
                    echo '<div class="ellipsis" title="' . $renewReason . '">' . $renewReason .'</div>';
                    break;

                    break;
                case 'actions':
                    if($tag != 5){
                        $params = "reviewID=$review->id";
                        $flag = $this->loadModel('reviewmanage')->isClickable($review, 'recall');
                        $click = $flag ? 'onclick="return recall()"' : '';

                        $closeflag = $this->loadModel('reviewmanage')->isClickable($review, 'close');
                        $id = $review->id;
                        $nodealissue = $this->review->getNoDealIssue($id);
                        $count = isset($nodealissue[$id]) ? $nodealissue[$id] : '';
                        $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                        //取出最后一个评审人
                        //判断当前用户是否是最后一个验证人
                        $lastVerifyer ='';
                        if(count($dealUser) == 1){
                            $lastVerifyer = 1;
                        }
                        //是否允许审批
                        $verFlag = '';
                        $checkRes = $this->review->checkReviewIsAllowReview($review, $this->app->user->account);
                        if($review->status == 'waitVerify' or $review->status == 'verifying' ){
                            $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
                            if($issueCount!=0 and $lastVerifyer ==1){
                                $verFlag = 1;
                            }elseif($issueCount!=0){
                                $verFlag = 2;
                            }
                        }

                        $reviewTipMsg = $this->loadModel('review')->getReviewTipMsg($review->status);
                        if (!$tag) {
                            common::hasPriv('reviewmanage', 'edit') ? common::printIcon('reviewmanage', 'edit', $params."&flag=1", $review, 'list') : '';
                            common::hasPriv('reviewmanage', 'submit') ? common::printIcon('reviewmanage', 'submit', $params, $review, 'list', 'play', '', 'iframe', true, '', $this->lang->review->submit) : '';
                            common::hasPriv('reviewmanage', 'recall') ? common::printIcon('reviewmanage', 'recall', $params, $review, 'list', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
                            common::hasPriv('reviewmanage', 'assign') ? common::printIcon('reviewmanage', 'assign', $params, $review, 'list', 'hand-right', '', 'iframe', true, '', $this->lang->review->assign) : '';

                            //非最最后一个人验证时
                            if(($review->status == 'waitVerify' or $review->status == 'verifying' )&&$verFlag ==2){
                                $clickClose ='onclick="return reviewVerifyConfirm()"';
                                common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', 'hiddenwin', 'iframe', true,"$clickClose", $reviewTipMsg) : '';
                            }else{
                                common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,'data-width="1200px" ', $reviewTipMsg) : '';
                            }
                            // common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true, 'data-width="1200px"', $reviewTipMsg) : '';
                            common::hasPriv('reviewmanage', 'reviewreport') ? common::printIcon('reviewmanage', 'reviewreport', $params, $review, 'list', 'bar-chart', '') : '';
                            // common::hasPriv('review', 'close') ? common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, '', $this->lang->review->close) : '';

                            if (common::hasPriv('reviewmanage', 'close')) {
                                if ($closeflag) {
//                                    $clickClose ="onclick=reviewClose('$review->id','$count')";
//                                    common::printIcon('reviewmanage', 'close', $params, '', 'list', 'off', '', 'iframe', true, "$clickClose", $this->lang->review->close);
                                    echo '<a href="javascript:;" onclick="reviewClose('.$review->id.','.$count.')" class="btn"><i class="icon-review-close icon-off"></i></a>';
                                } else {
                                    common::printIcon('reviewmanage', 'close', $params, $review, 'list', 'off', '', 'iframe', true, '', $this->lang->review->close);
                                }
                            }

                            common::hasPriv('reviewmanage', 'delete') ? common::printIcon('reviewmanage', 'delete', $params, $review, 'list', 'trash', '', 'iframe', true, '', $this->lang->review->delete) : '';
                        }
                    }


            }
            echo '</td>';
        }
    }

    /**
     * 评审管理列表
     * @param $status
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function reviewList($status = 'all',  $queryID = 0,$orderBy, $pager = null)
    {
        $reviewmanageQuery = '';
        if($status == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewmanageQuery', $query->sql);
                $this->session->set('reviewmanageForm', $query->form);
            }
            if($this->session->reviewmanageQuery == false) $this->session->set('reviewmanageQuery', ' 1 = 1');
            $reviewmanageQuery = $this->session->reviewmanageQuery;
        }
        //此判断为了解决连表查询时，重复字段未指明表的问题
        if(strpos($reviewmanageQuery,'createdDate')){
            $reviewmanageQuery = str_replace('AND (`', ' AND (`t1.', $reviewmanageQuery);
            $reviewmanageQuery = str_replace('AND `', ' AND `t1.', $reviewmanageQuery);
            $reviewmanageQuery = str_replace('`', '', $reviewmanageQuery);
        }else{
            $reviewmanageQuery = str_replace('AND `', ' AND `t1.', $reviewmanageQuery);
            $reviewmanageQuery = str_replace('`', '', $reviewmanageQuery);
        }
        $reviewmanageQuery = $this->loadModel('review')->getFormatSearchQuery($reviewmanageQuery);
        $user = $this->app->user->account;
        $dept =  $this->getDeptByUserName($user)->dept;
        $allDeptPerson=$this->getAllDeptPerson($dept);
        $allDeptPerson2 = str_replace (array ('(', ')'), '', $allDeptPerson);
        $allDeptPerson2 = str_replace ("'", '', $allDeptPerson2);
        $allDeptPerson2 = "'".str_replace (" ", '', $allDeptPerson2)."'";
        //拼接查询条件
        $andWhere  = '';

        switch($status){
            case 'bysearch':
               // $andWhere = " where ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.reviewer,t5.qualityCm,t5.meetexport,t5.expert,t5.reviewedBy,t5.outside)  like'%$user%'))";
              $andWhere = " where ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(',','',t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.meetexport)  like'%,$user,%'))";
                break;
            case 'all':
               // $andWhere = " where ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.reviewer,t5.qualityCm,t5.meetexport,t5.expert,t5.reviewedBy,t5.outside)  like'%$user%'))";
                $andWhere = " where ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(',','',t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.meetexport)  like'%,$user,%'))";
                break;
            case 'noclose':
                //$andWhere = " where t5.closePerson is null and ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.reviewer,t5.qualityCm,t5.meetexport,t5.expert,t5.reviewedBy,t5.outside)  like'%$user%'))";
                $andWhere = " where t5.closePerson is null and ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(',','',t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.meetexport)  like'%,$user,%'))";
                break;
            case 'wait':
                $andWhere = " where concat(',',t5.dealUser,',') like '%,$user,%' and  t5.status not in ('waitMeetingOwnerReview','waitMeetingReview')";
                break;
            case 'created':
                $andWhere = " where t5.createdBy = '$user'";
                break;
            case 'reviewbyme':
                $andWhere = " where ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (  concat(',',t5.meetexport,',') like '%,$user,%') )";
                break;
            case 'closed':
                $andWhere = " where t5.closePerson = '$user'";
                break;
            case 'ccto':
                $andWhere = " where concat(',',t5.relatedUsers,',') like '%,$user,%'";
                break;
            case 'deptjoin':
                $andWhere = " where t5.owner in $allDeptPerson 
                or t5.review in $allDeptPerson or concat(t5.expert, ',') regexp concat(replace($allDeptPerson2,',',',|'),',')  or t5.qualityCm in $allDeptPerson 
                or concat(t5.trialDeptLiasisonOfficer, ',') regexp concat(replace($allDeptPerson2,',',',|'),',')  or concat(t5.trialAdjudicatingOfficer, ',') regexp concat(replace($allDeptPerson2,',',',|'),',') 
                 or concat(t5.trialJoinOfficer, ',') regexp concat(replace($allDeptPerson2,',',',|'),',')";
                break;
        }
      $ids = $this->dao->query("select id from (
	       select t1.id id,t4.reviewer review,t4.status statu,t1.title,t1.status,t1.dealUser,t1.meetingPlanTime,t1.reviewer,t1.owner,t1.expert,t1.reviewedBy,
		   t1.outside,t1.meetingPlanExport meetexport,t1.relatedUsers,t1.createdBy,t1.createdDept,t1.editBy,t1.closePerson,t1.qa,t1.qualityCm,t1.trialDeptLiasisonOfficer,
	       t1.trialAdjudicatingOfficer, t1.trialJoinOfficer from `zt_review` as t1
           left join `zt_reviewnode` as t3 on
                t3.objectID = t1.id
                and t3.objectType = 'review'
           left join `zt_reviewer` as t4 on
                t3.id = t4.node
           where t1.deleted = '0') as t5 $andWhere group by id order by `id` desc")->fetchAll();
        $ids = array_column($ids,'id');
        if(strpos($reviewmanageQuery,'closeTime')){
            $reviewmanageQuery = str_replace("AND t1.closeTime", "AND t1.closeTime != '0000-00-00 00:00:00' AND t1.closeTime", $reviewmanageQuery);
        }
        $reviews = $this->dao->select('*')->from(TABLE_REVIEW)->alias('t1')->where('t1.id')->in($ids)
            ->beginIF($status == 'bysearch')->andWhere($reviewmanageQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        if (empty($reviews)) {
            return $reviews;
        }

        $this->loadModel('review');
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;

        foreach ($reviews as $key => $reviewInfo) {
            $status = $reviewInfo->status;
            $reviews[$key]->statusDesc = $this->review->getReviewStatusDesc($status, $reviewInfo->rejectStage);

            if (in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)) {
                if ($status == 'baseline') {
                    $reviews[$key]->reviewers = $reviewInfo->dealUser;
                    $reviews[$key]->dealUser = $reviewInfo->dealUser;
                } else {
                    $reviewVersion = $this->review->getReviewVersion($reviewInfo);
                    $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewVersion, $reviewInfo->reviewStage);
                    $reviews[$key]->reviewers = $reviewers;
                    $reviews[$key]->dealUser = $reviewers;
                }
            }
        }
        return $reviews;
    }
    /**
     * 评审管理列表
     * @param $status
     * @param $orderBy
     * @param null $pager
     * @return mixed
     */
    public function deptjoinList($status = 'all',  $queryID = 0,$orderBy, $pager = null)
    {
        $reviewmanageQuery = '';
        if($status == 'bysearch')
        {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if($query)
            {
                $this->session->set('reviewmanageQuery', $query->sql);
                $this->session->set('reviewmanageForm', $query->form);
            }
            if($this->session->reviewmanageQuery == false) $this->session->set('reviewmanageQuery', ' 1 = 1');
            $reviewmanageQuery = $this->session->reviewmanageQuery;
        }
        //此判断为了解决连表查询时，重复字段未指明表的问题
        if(strpos($reviewmanageQuery,'createdDate')){
            $reviewmanageQuery = str_replace('AND (`', ' AND (`t1.', $reviewmanageQuery);
            $reviewmanageQuery = str_replace('AND `', ' AND `t1.', $reviewmanageQuery);
            $reviewmanageQuery = str_replace('`', '', $reviewmanageQuery);
        }else{
            $reviewmanageQuery = str_replace('AND `', ' AND `t1.', $reviewmanageQuery);
            $reviewmanageQuery = str_replace('`', '', $reviewmanageQuery);
        }
        $reviewmanageQuery = $this->loadModel('review')->getFormatSearchQuery($reviewmanageQuery);
        $user = $this->app->user->account;
        $dept =  $this->getDeptByUserName($user)->dept;
        $allDeptPerson=$this->getAllDeptPerson($dept);
        $allDeptPerson2 = str_replace (array ('(', ')'), '', $allDeptPerson);
        $allDeptPerson2 = str_replace ("'", '', $allDeptPerson2);
        $allDeptPerson2 = "'".str_replace (" ", '', $allDeptPerson2)."'";
        //拼接查询条件
        $andWhere  = '';
        $andWhereDept = " t5.owner in $allDeptPerson  
                or t5.review in $allDeptPerson or concat(t5.expert, ',') regexp concat(replace($allDeptPerson2,',',',|'),',')  or t5.qualityCm in $allDeptPerson 
                or concat(t5.trialDeptLiasisonOfficer, ',') regexp concat(replace($allDeptPerson2,',',',|'),',')  or concat(t5.trialAdjudicatingOfficer, ',') regexp concat(replace($allDeptPerson2,',',',|'),',') 
                or concat(t5.trialJoinOfficer, ',') regexp concat(replace($allDeptPerson2,',',',|'),',')";
        switch($status){
            case 'bysearch':
                // $andWhere = " where ((t5.review = '$user' and t5.statu in ('pass', 'reject')) or (concat_ws(t5.dealUser,t5.createdBy,t5.closePerson,t5.relatedUsers,t5.reviewer,t5.qualityCm,t5.meetexport,t5.expert,t5.reviewedBy,t5.outside)  like'%$user%'))";
                $andWhere = " where  ($andWhereDept)";
                break;
            case 'all':
                $andWhere = " where  ($andWhereDept)";
                break;
            case 'waitapply':
                $andWhere = " where t5.status='waitapply' and ($andWhereDept)";
                break;
            case 'waitprereview':
                $andWhere = " where t5.status in ('waitPreReview') and ($andWhereDept)";
                break;
            case 'waitfirstassigndept':
                $andWhere = " where t5.status in ('waitFirstAssignDept') and ($andWhereDept)";
                break;
            case 'waitprereview':
                $andWhere = " where t5.status in ('waitPreReview') and ($andWhereDept)";
                break;
            case 'waitfirstassignreviewer':
                $andWhere = " where t5.status in ('waitFirstAssignReviewer','firstassigning') and ($andWhereDept)";
                break;
            case 'waitfirstreview':
                $andWhere = " where t5.status in ('waitFirstReview','firstReviewing') and ($andWhereDept)";
                break;
            case 'waitfirstmainreview':
                $andWhere = " where t5.status in ('waitFirstMainReview','firstMainReviewing') and ($andWhereDept)";
                break;
            case 'waitformalassignreviewer':
                $andWhere = " where t5.status in ('waitFormalAssignReviewer') and ($andWhereDept)";
                break;
            case 'waitformalreview':
                $andWhere = " where t5.status in ('waitFormalReview','formalReviewing') and ($andWhereDept)";
                break;
            case 'waitformalownerreview':
                $andWhere = " where t5.status in ('waitformalownerreview') and ($andWhereDept)";
                break;
            case 'waitmeetingreview':
                $andWhere = " where t5.status in ('waitMeetingReview','meetingReviewing') and ($andWhereDept)";
                break;
            case 'waitmeetingownerreview':
                $andWhere = " where t5.status in ('waitmeetingownerreview') and ($andWhereDept)";
                break;
            case 'waitverify':
                $andWhere = " where t5.status in ('waitVerify','verifying') and ($andWhereDept)";
                break;
            case 'waitoutreview':
                $andWhere = " where t5.status in ('waitOutReview','outReviewing') and ($andWhereDept)";
                break;
            case 'pass':
                $andWhere = " where t5.status in ('pass') and ($andWhereDept)";
                break;
            case 'reviewpass':
                $andWhere = " where t5.status in ('reviewpass') and ($andWhereDept)";
                break;
            case 'prepassbutedit':
                $andWhere = " where t5.status in ('prepassbutedit') and ($andWhereDept)";
                break;
            case 'firstpassbutedit':
                $andWhere = " where t5.status in ('firstpassbutedit') and ($andWhereDept)";
                break;
            case 'formalpassbutedit':
                $andWhere = " where t5.status in ('formalpassbutedit') and ($andWhereDept)";
                break;
            case 'meetingpassbutedit':
                $andWhere = " where t5.status in ('meetingpassbutedit') and ($andWhereDept)";
                break;
            case 'outpassbutedit':
                $andWhere = " where t5.status in ('outpassbutedit') and ($andWhereDept)";
                break;
            case 'rejectpre':
                $andWhere = " where t5.status in ('rejectpre') and ($andWhereDept)";
                break;
            case 'rejectfirst':
                $andWhere = " where t5.status in ('rejectfirst') and ($andWhereDept)";
                break;
            case 'rejectmeeting':
                $andWhere = " where t5.status in ('rejectmeeting') and ($andWhereDept)";
                break;
            case 'rejectformal':
                $andWhere = " where t5.status in ('rejectformal') and ($andWhereDept)";
                break;
            case 'rejectout':
                $andWhere = " where t5.status in ('rejectout') and ($andWhereDept)";
                break;
            case 'rejectverify':
                $andWhere = " where t5.status in ('rejectverify') and ($andWhereDept)";
                break;
            case 'reject':
                $andWhere = " where t5.status in ('reject') and ($andWhereDept)";
                break;
            case 'recall':
                $andWhere = " where t5.status in ('recall') and ($andWhereDept)";
                break;
            case 'fail':
                $andWhere = " where t5.status in ('fail') and ($andWhereDept)";
                break;
            case 'drop':
                $andWhere = " where t5.status in ('drop') and ($andWhereDept)";
                break;
            case 'baseline':
                $andWhere = " where t5.status in ('baseline') and ($andWhereDept)";
                break;
            case 'close':
                $andWhere = " where t5.status in ('close') and ($andWhereDept)";
                break;
            case 'suspend':
                $andWhere = " where t5.status in ('suspend') and ($andWhereDept)";
                break;
        }

        $ids = $this->dao->query("select id from (
	       select t1.id id,t4.reviewer review,t4.status statu,t1.title,t1.status,t1.dealUser,t1.meetingPlanTime,t1.reviewer,t1.owner,t1.expert,t1.reviewedBy,
		   t1.outside,t1.meetingPlanExport meetexport,t1.relatedUsers,t1.createdBy,t1.createdDept,t1.editBy,t1.closePerson,t1.qa,t1.qualityCm,t1.trialDeptLiasisonOfficer,
	       t1.trialAdjudicatingOfficer, t1.trialJoinOfficer from `zt_review` as t1
           left join `zt_reviewnode` as t3 on
                t3.objectID = t1.id
                and t3.objectType = 'review'
           left join `zt_reviewer` as t4 on
                t3.id = t4.node
           where t1.deleted = '0') as t5 $andWhere group by id order by `id` desc")->fetchAll();
        $ids = array_column($ids,'id');
        $reviews = $this->dao->select('*')->from(TABLE_REVIEW)->alias('t1')->where('t1.id')->in($ids)
            ->beginIF($status == 'bysearch')->andWhere($reviewmanageQuery)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
        if(!common::hasPriv('reviewmanage', 'judgepermission')){
            $reviews ='';
        }
        if (empty($reviews)) {
            return $reviews;
        }

        $this->loadModel('review');
        $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
        $allowAssignStatusList = $this->lang->review->allowAssignStatusList;

        foreach ($reviews as $key => $reviewInfo) {
            $status = $reviewInfo->status;
            $reviews[$key]->statusDesc = $this->review->getReviewStatusDesc($status, $reviewInfo->rejectStage);

            if (in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)) {
                if ($status == 'baseline') {
                    $reviews[$key]->reviewers = $reviewInfo->dealUser;
                    $reviews[$key]->dealUser = $reviewInfo->dealUser;
                } else {
                    $reviewVersion = $this->review->getReviewVersion($reviewInfo);
                    $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewVersion, $reviewInfo->reviewStage);
                    $reviews[$key]->reviewers = $reviewers;
                    $reviews[$key]->dealUser = $reviewers;
                }
            }
        }
        return $reviews;
    }

    //设置会议排期
    public function setmeeting(){
        $ids = rtrim($_POST['ids'],',');
        $data  = fixer::input('post')
            ->join('expert',',')
            ->remove('ids')
            ->get();
        $data->expert = trim($data->expert,',');
        $meet_detail = new stdClass();
        $meet_detail->owner = $data->owner;
        $meet_detail->meetingPlanExport = $data->expert;
        $meet_detail->meetingPlanTime = $data->feedbackExpireTime;

        $this->dao->update(TABLE_REVIEW_MEETING)->data($meet_detail)->autocheck()
            ->batchCheck($this->config->reviewmanage->create->requiredFields, 'notempty')
            ->where('id')->in($ids);
        $res = $this->dao->select("review_id")->from(TABLE_REVIEW_MEETING_DETAIL)->where('review_meeting_id')->in($ids)->fetchall();
        $review_id = "";
        foreach ($res as $k=>$v) {
            $review_id .= $v->review_id.',';
        }
        $review_id = rtrim($review_id,',');
        $review_data = new  stdClass();
        $review_data->meetingPlanExport = $data->expert;
        $review_data->meetingRealTime = $data->feedbackExpireTime;
        $this->dao->update(TABLE_REVIEW)->data($meet_detail)
            ->where('id')->in($review_id);

        if(!dao::isError()) return dao::getError();
    }

    /**
     * 根据当前用户获取部门
     * @param $user
     * @return mixed
     */
    public function getDeptByUserName($user){
        $dept = $this->dao->select('dept')
            ->from(TABLE_USER)
            ->where('account')->eq($user)
            ->andWhere('deleted')->eq('0')
            ->fetch();
        return $dept;
    }

    public function getProblemList($projectID, $reviewID, $browseType,$queryID,$orderBy, $pager)
    {
        /* 获取搜索条件的查询SQL。*/
        $reviewIssueQuery = '';
        if ($browseType == 'bysearch') {
            $query = $queryID ? $this->loadModel('search')->getQuery($queryID) : '';
            if ($query) {
                $this->session->set('reviewIssueQuery', $query->sql);
                $this->session->set('reviewIssueForm', $query->form);
            }
            if ($this->session->reviewIssueQuery == false) $this->session->set('reviewIssueQuery', ' 1 = 1');
            $reviewIssueQuery = $this->session->reviewissueQuery;
            //关联表相同字段歧义修改  提出阶段
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'title');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'status');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'type');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'createdBy');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'createdDate');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'editBy');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'editDate');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'dealUser');
            $reviewIssueQuery = $this->dealSqlAmbiguous($reviewIssueQuery, 't1', 'meetingCode');
        }
        $statusArray = $this->lang->reviewissue->browseStatus;
        foreach ($statusArray as $key => $value) {
            unset($statusArray['all']);
            unset($statusArray['closed']);//将已处理和已关闭归属为已验证
        }
        //数据库字段为desc等特殊字符，需要增加``进行处理，无法识别
        $order = explode('_', $orderBy);
        $first = $order[0] = "`" . $order[0] . "`";
        $orderBy = $first . "_" . $order[1];
        return $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->Where('t1.deleted')->eq('0')
            ->beginIF($browseType == 'bysearch')->andWhere($reviewIssueQuery)->fi()
            ->beginIF($browseType == 'noclose')->andWhere('t1.status')->in(['closed', 'resolved'])->fi()
//            ->beginIF(in_array($browseType,array_keys($statusArray)))->andWhere('t1.status')->eq($browseType)->fi()
//            ->beginIF($browseType == 'createdBy')->andWhere('t1.createdBy')->eq($this->app->user->account)->fi()
//            ->beginIF($browseType == 'review' || $browseType == 'audit')->andWhere('t1.type')->eq($browseType)->fi()
            ->beginIF($browseType == 'wait')->andWhere('t1.dealUser')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'created')->andWhere('t1.createdBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'resolved')->andWhere('t1.resolutionBy')->eq($this->app->user->account)->fi()
            ->beginIF($browseType == 'verification')->andWhere('t1.validation')->eq($this->app->user->account)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Desc:处理连表sql查询时共有字段无法识别的问题
     * Date: 2022/6/16
     * Time: 17:08
     *
     * @param string $query where条件
     * @param string $alias 别名
     * @param string $field 字段
     * @return string
     *
     */
    public function dealSqlAmbiguous($query = '', $alias = '', $field = '')
    {
        if (strpos($query, "`" . $field . "`") !== false) {
            $query = str_replace("`" . $field . "`", $alias . ".`" . $field . "`", $query);
        }
        return $query;
    }

    /**
     * 待处理在线评审
     * @param $status
     * @param $orderBy
     * @param $pager
     * @return mixed
     */
    public function reviewBoardList($status,$browseType, $orderBy, $pager)
    {
        $this->loadModel('review');
        $user = $this->app->user->account;
       // $date = helper::now();
        $date = date('Y-m-d',time());

        if($status == 'waitFormalReview' ){
            $allmeets = $this->dao->select('t1.*,  t2.category, t2.product')->from(TABLE_REVIEW)->alias('t1')
                    ->leftJoin(TABLE_OBJECT)->alias('t2')
                    ->on('t1.object=t2.id')
                    ->where('t1.deleted')->eq(0)
                    ->andWhere("CONCAT(',', t1.dealUser, ',')")->like("%,{$this->app->user->account},%")
                    ->andWhere('t1.status')->ne('waitMeetingOwnerReview')
                    ->andWhere('t1.status')->ne('waitMeetingReview')
                    ->groupBy('t1.id')
                    ->orderBy('t1.id_desc')
                    ->page($pager)
                    ->fetchAll();

            $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
            $allowAssignStatusList = $this->lang->review->allowAssignStatusList;

            foreach ($allmeets as $key => $reviewInfo) {
                $status = $reviewInfo->status;
                $allmeets[$key]->statusDesc = $this->review->getReviewStatusDesc($status, $reviewInfo->rejectStage);
                if (in_array($status, $allowReviewStatusList) || in_array($status, $allowAssignStatusList)) {
                    if ($status == 'baseline') {
                        $allmeets[$key]->reviewers = $reviewInfo->dealUser;
                        $allmeets[$key]->dealUser = $reviewInfo->dealUser;
                    } else {
                        $reviewVersion = $this->review->getReviewVersion($reviewInfo);
                        $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewVersion, $reviewInfo->reviewStage);
                        $allmeets[$key]->reviewers = $reviewers;
                        $allmeets[$key]->dealUser = $reviewers;
                    }
                }
            }

        }elseif($status == 'waitMeetingReview'){
            $allmeets = $this->dao->select('t1.*,group_concat(distinct t2.title) title,group_concat(distinct t2.object) object,t1.meetingPlanExport,group_concat(distinct t2.relatedUsers)relatedUsers,group_concat(distinct t2.createdBy)createdBy,group_concat(distinct t2.createdDept) createdDept')->from(TABLE_REVIEW_MEETING)->alias('t1')
                ->leftJoin(TABLE_REVIEW)->alias('t2')
                ->on('t1.meetingCode = t2.meetingCode')
                ->where('t1.deleted')->eq(0)
                ->andWhere('t2.deleted')->eq(0)
                ->andWhere("CONCAT(',', t1.dealUser, ',')")->like("%,{$this->app->user->account},%")
                ->groupBy('t1.id')
                ->orderBy('t1.sortId_desc')
                ->fetchAll();
        }elseif($status == 'waitjoin'){
            $allmeets =  $this->dao->select('t1.*,group_concat(t2.title) title,group_concat(distinct t2.object) object,t1.meetingPlanExport,group_concat(distinct t2.createdBy) createdBy,group_concat(distinct t2.createdDept) createdDept,
            group_concat(distinct t2.relatedUsers) relatedUsers,group_concat(distinct t6.PM) PM,group_concat(distinct t4.manager) manager')->from(TABLE_REVIEW_MEETING)->alias('t1')
                ->leftJoin(TABLE_REVIEW)->alias('t2')
                ->on('t1.meetingCode = t2.meetingCode')
                ->leftJoin(TABLE_PROJECT)->alias('t3')
                ->on('t2.project = t3.id')
                ->leftJoin(TABLE_DEPT)->alias('t4')
                ->on('t2.createdDept = t4.id')
                ->leftJoin(TABLE_PROJECTPLAN)->alias('t5')
                ->on('t3.id =t5.project')
                ->leftJoin(TABLE_PROJECTCREATION)->alias('t6')
                ->on('t5.id = t6.plan')
                ->where('t1.deleted')->eq(0)
                ->andWhere('t1.meetingPlanTime')->ge($date)
                ->andWhere('t1.status')->ne('waitMeetingOwnerReview')
                ->andWhere('t1.status')->ne('pass')
                // ->andWhere("(t1.meetingPlanExport like('%$user%') or t1.owner like('%$user%') or t1.reviewer like('%$user%')  or createdBy  like('%$user%'))")
                ->andWhere("concat_ws(',','',t1.meetingPlanExport,t1.owner, t1.reviewer, t2.createdBy, t2.relatedUsers,t6.PM, t4.manager)  like('%,$user,%')")
                ->groupBy('t1.id')
                ->orderBy('t1.sortId_desc')
                ->fetchAll();
        }

        return $allmeets;
    }

    /**
     * 会议评审条数
     * @param string $status
     * @param $orderBy
     * @param null $pager
     */
    public function meetingCount()
    {

        $user = $this->app->user->account;
        $date = date('Y-m-d',time());
        $year = date('Y');
        $this->loadModel('reviewqz');
        //代处理在线评审
        $onlinereviews =  $this->dao->select('t1.*, t2.version, t2.category, t2.product')->from(TABLE_REVIEW)->alias('t1')
            ->leftJoin(TABLE_OBJECT)->alias('t2')
            ->on('t1.object=t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere("CONCAT(',', t1.dealUser, ',')")->like("%,{$this->app->user->account},%")
            ->andWhere('t1.status')->ne('waitMeetingOwnerReview')
            ->andWhere('t1.status')->ne('waitMeetingReview')
            ->groupBy('t1.id')
            ->orderBy('t1.id_desc')
            ->fetchAll();


        //待处理会议评审
        $meetingreviews  = $this->dao->select('t1.*,group_concat(distinct t2.title) title,group_concat(distinct t2.object) object,t2.meetingPlanExport,group_concat(distinct t2.relatedUsers)relatedUsers,group_concat(distinct t2.createdBy)createdBy,group_concat(distinct t2.createdDept) createdDept')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.meetingCode = t2.meetingCode')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere("CONCAT(',', t1.dealUser, ',')")->like("%,{$this->app->user->account},%")
            ->andWhere('t1.status')->ne('waitFormalReview')
            ->groupBy('t1.id')
            ->orderBy('t1.sortId_desc')
            ->fetchAll();
        //待参加会议评审
        $joinmeetings =$this->dao->select('t1.*,group_concat(t2.title) title,group_concat(distinct t2.object) object,t1.meetingPlanExport,group_concat(distinct t2.createdBy) createdBy,group_concat(distinct t2.createdDept) createdDept,
            group_concat(distinct t2.relatedUsers) relatedUsers,group_concat(distinct t6.PM) PM,group_concat(distinct t4.manager) manager')->from(TABLE_REVIEW_MEETING)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.meetingCode = t2.meetingCode')
            ->leftJoin(TABLE_PROJECT)->alias('t3')
            ->on('t2.project = t3.id')
            ->leftJoin(TABLE_DEPT)->alias('t4')
            ->on('t2.createdDept = t4.id')
            ->leftJoin(TABLE_PROJECTPLAN)->alias('t5')
            ->on('t3.id =t5.project')
            ->leftJoin(TABLE_PROJECTCREATION)->alias('t6')
            ->on('t5.id = t6.plan')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.meetingPlanTime')->ge($date)
            ->andWhere('t1.status')->ne('waitMeetingOwnerReview')
            ->andWhere('t1.status')->ne('pass')
            //->andWhere("(t1.meetingPlanExport like('%$user%') or t1.owner like('%$user%') or t1.reviewer like('%$user%')  or createdBy  like('%$user%') )")
            ->andWhere("concat_ws(',','',t1.meetingPlanExport,t1.owner , t1.reviewer , t2.createdBy, t2.relatedUsers,t6.PM, t4.manager)  like('%,$user,%')")
            ->groupBy('t1.id')
            ->orderBy('t1.sortId_desc')
            ->fetchAll();

        // 待处理评审问题
        $issues = $this->dao->select('t1.*,t2.title as reviewtitle')->from(TABLE_REVIEWISSUE)->alias('t1')
            ->leftJoin(TABLE_REVIEW)->alias('t2')
            ->on('t1.review=t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.dealUser')->eq($user)
            ->fetchAll();

        // 查询是否包含待处理人
        $findInSet = '(FIND_IN_SET("'.$user.'",dealUser))';
        // 获取评审id范围
        $ids = $this->dao->select('id')->from(TABLE_REVIEWQZ)
            ->where('deleted')->eq('0')
            ->andWhere($findInSet)
            ->groupBy('id')
            ->orderBy('id_desc')
            ->fetchAll();
        $ids = array_column($ids,'id');
        // 待处理清总评审
        $reviewqzs = $this->dao->select('id')->from(TABLE_REVIEWQZ)
            ->where('id')->in($ids)
            ->fetchAll();

        return array('waitFormalReview' => count($onlinereviews), 'waitMeetingReview' => count($meetingreviews), 'waitjoin' => count($joinmeetings), 'issue' => count($issues), 'reviewqz' => count($reviewqzs));
    }

    /**
     * Print datatable cell.
     * @param object $col
     * @param object $review
     * @param array $users
     * @param array $products
     * @access public
     * @return void
     */

    public function printMeetCell($col, $review, $users, $products)
    {
        $reviewID = $review->id;
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList = inlink('view', "reviewID=$review->id");
        $account = $this->app->user->account;
        $id = $col->id;

        if ($col->show) {
            $class = "c-$id";
            $title = '';
            if ($id == 'id') $class .= ' cell-id';
            if ($id == 'status') {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->review->statusLabelList, $review->status, '');
                $title = "title='{$name}'";
            }

            if ($id == 'title') {
                $class .= ' text-left';
                $title = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";

            switch ($id) {
                case 'id':
                    if ($canBatchAction) {
                        echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
                    } else {
                        printf('%03d', $review->id);
                    }
                    break;
                case 'title':
                    $txt = '';
                    $title = array_unique(explode(',', $review->title));
                    foreach ($title as $obj) {
                        $obj = trim($obj);
                        if (empty($obj)) continue;
                        $txt .= $obj . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                    break;
                case 'object':
                    $txt = '';
                    $object = array_unique(explode(',', $review->object));
                    foreach ($object as $obj) {
                        $obj = trim($obj);
                        if (empty($obj)) continue;
                        $txt .= zget($this->lang->review->objectList, $obj) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'status':
                    echo zget($this->lang->reviewmanage->statuslist, $review->status, '');
                    break;
                case 'meetingCode':
                    echo isset($review->meetingCode) ? html::a(helper::createLink('reviewmeeting', 'meetingview', "reviewID=$review->id&flag=1"), $review->meetingCode) : '';
                    break;
                case 'meetingPlanExport':
                    echo zget($users, $review->meetingPlanExport, '');
                    break;
                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if ($review->meetingPlanTime != '0000-00-00 00:00:00') {
                        $meetingPlanTime = $review->meetingPlanTime;
                    }
                    echo '<div class="ellipsis" title="' . $meetingPlanTime . '">' . $meetingPlanTime . '</div>';
                    break;

                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if ($review->meetingRealTime != '0000-00-00 00:00:00') {
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo '<div class="ellipsis" title="' . $meetingRealTime . '">' . $meetingRealTime . '</div>';
                    break;
                case 'relatedUsers':
                    $txt = '';
                    $relatedUsers = array_unique(explode(',', $review->relatedUsers));

                    foreach ($relatedUsers as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'createdBy':
                    echo zget($users, $review->createdBy, '');
                    break;
                case 'owner':
                    echo zget($users, $review->owner, '');
                    break;
                case 'reviewer':
                    $reviewers =  zget($users, $review->reviewer, '');
                    echo '<div class="ellipsis" title="' . $reviewers . '">' . $reviewers . '</div>';
                    break;
                case 'createdDate':
                    echo $review->createdDate;
                    break;

                case 'dealUser':
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    $txt = '';
                    foreach ($dealUser as $account)
                        $txt .= zget($users, $account, '') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'createdDept':
                    echo zget($deptMap, $review->createdDept, '');
                    break;
                case 'actions':
                    $params  = "reviewID=$review->id";
                    common::hasPriv('reviewmeeting', 'edit') ?  common::printIcon('reviewmeeting', 'edit',    $params, $review, 'list', '', '', 'iframe', true,'data-width="1200px"') : '';
                    common::hasPriv('reviewmeeting', 'review') ? common::printIcon('reviewmeeting', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,'data-width="1200px"', $this->lang->reviewmanage->reviewTipMsg) : '';
                    common::hasPriv('reviewmeeting', 'confirmmeeting') ? common::printIcon('reviewmeeting', 'confirmmeeting', $params, $review, 'list', 'persons', '', 'iframe', true,'data-width="1200px"') : '';
                    common::hasPriv('reviewmeeting', 'notice') ? common::printIcon('reviewmeeting', 'notice', $params, $review, 'list', 'envelope-o', '', 'iframe', true,'data-width="1200px"') : '';
                    common::hasPriv('reviewmeeting', 'downloadfiles') ? common::printIcon('reviewmeeting', 'downloadfiles', $params, $review, 'list', 'download', '', '', '','data-width="1200px"') : '';

            }
            echo '</td>';
        }
    }

    public function printWaitMeetCell($staus,$col, $review, $users, $products)
    {
        $reviewID = $review->id;
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList = inlink('view', "reviewID=$review->id");
        $account = $this->app->user->account;
        $id = $col->id;

        if ($col->show) {
            $class = "c-$id";
            $title = '';
            if ($id == 'id') $class .= ' cell-id';
            if ($id == 'status') {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->review->statusLabelList, $review->status, '');
                $title = "title='{$name}'";
            }

            if ($id == 'title') {
                $class .= ' text-left';
                $title = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";

            switch ($id) {
                case 'id':
                    if ($canBatchAction) {
                        echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
                    } else {
                        printf('%03d', $review->id);
                    }
                    break;
                case 'title':
                    $txt = '';
                    $title = array_unique(explode(',', $review->title));
                    foreach ($title as $obj) {
                        $obj = trim($obj);
                        if (empty($obj)) continue;
                        $txt .= $obj . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                    break;
                case 'object':
                    $txt = '';
                    $object = array_unique(explode(',', $review->object));
                    foreach ($object as $obj) {
                        $obj = trim($obj);
                        if (empty($obj)) continue;
                        $txt .= zget($this->lang->review->objectList, $obj) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;

                case 'status':
                    echo zget($this->lang->reviewmanage->statuslist, $review->status, '');
                    break;
                case 'meetingCode':
                    $meetingCode ='<div class="ellipsis" title="' . $review->meetingCode . '">' . $review->meetingCode . '</div>';
                    echo isset($review->meetingCode) ? html::a(helper::createLink('reviewmeeting', 'meetingview', "reviewID=$review->id&flag=1"), $meetingCode) : '';
                    break;
                case 'meetingPlanExport':
                    $txt = '';
                    $meetingPlanExports = explode(',', str_replace(' ', '',$review->meetingPlanExport));
                    foreach ($meetingPlanExports as $account) {
                        $txt .= rtrim( zget($users, $account).' ',''). " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if ($review->meetingPlanTime != '0000-00-00 00:00:00') {
                        $meetingPlanTime .= $review->meetingPlanTime. " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $meetingPlanTime . '">' . $meetingPlanTime . '</div>';;
                    break;

                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if ($review->meetingRealTime != '0000-00-00 00:00:00') {
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo '<div class="ellipsis" title="' . $meetingRealTime . '">' . $meetingRealTime . '</div>';;
                    break;
                case 'relatedUsers':
                    $txt = '';
                    $relatedUsers = array_unique(explode(',', $review->relatedUsers));

                    foreach ($relatedUsers as $account) {
                        $account = trim($account);
                        if (empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'createdBy':
                    $txt = '';
                    $createdBys = explode(',', str_replace(' ', '', $review->createdBy));
                    foreach ($createdBys as $account){
                        $txt .= rtrim( zget($users, $account).' ',''). " &nbsp;" ;
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'owner':
                    $txt =zget($users, $review->owner, '');
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'reviewer':
                    $reviewer = explode(',', str_replace(' ', '', $review->reviewer));
                    $txt = '';
                    foreach ($reviewer as $account)
                        $txt .= zget($users, $account, '') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'createdDate':
                    $txt = $review->createdDate;
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;

                case 'dealUser':
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    $txt = '';
                    foreach ($dealUser as $account)
                        $txt .= zget($users, $account, '') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                    break;
                case 'createdDept':
                    $depts           = $this->loadModel('dept')->getOptionMenu();
                    $createDepts = explode(',', str_replace(' ', '',$review->createdDept));
                    $txt = '';
                    foreach ($createDepts as $account) {
                        $txt .= rtrim( zget($depts, $account).' ','') ;

                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt . '</div>';
                   // echo zget($deptMap, $review->createdDept, '');
                    break;
                case 'actions':
                    $params  = "reviewID=$review->id";
                    if($staus ==="waitmeeting"){
                        common::hasPriv('reviewmeeting', 'edit') ?  common::printIcon('reviewmeeting', 'edit',    $params, $review, 'list', '', '', 'iframe', true,'data-width="1200px"') : '';
                        common::hasPriv('reviewmeeting', 'review') ? common::printIcon('reviewmeeting', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,'data-width="1200px"', $this->lang->reviewmeeting->reviewTipMsg) : '';
                        common::hasPriv('reviewmeeting', 'confirmmeeting') ? common::printIcon('reviewmeeting', 'confirmmeeting', $params, $review, 'list','menu-users', '', 'iframe', true, 'data-width="750"') : '';
                        common::hasPriv('reviewmeeting', 'notice') ? common::printIcon('reviewmeeting', 'notice', $params, $review, 'list', 'envelope-o', '', 'iframe', true, 'data-width="900" data-height="600"',$this->lang->reviewmeeting->notice.$this->lang->reviewmeeting->common) : '';
                        common::hasPriv('reviewmeeting', 'downloadfiles') ? common::printIcon('reviewmeeting', 'downloadfiles', $params, $review, 'list', 'download', '', '', '','data-width="1200px"') : '';

                    }else if($staus ==="waitjoin"){
                        common::hasPriv('reviewmeeting', 'downloadfiles') ? common::printIcon('reviewmeeting', 'downloadfiles', $params, $review, 'list', 'download', '', '', '','data-width="1200px"') : '';
                    }


            }
            echo '</td>';
        }
    }

    /**
     * Print datatable cell.
     *  新增字段
     * @param  object $col
     * @param  object $review
     * @param  array  $users
     * @param  array  $products
     * @access public
     * @return void
     */

    public function printReviewCell($col, $review, $users, $products)
    {
        $reviewID = $review->id;
        $canView = common::hasPriv('review', 'view');
        $canBatchAction = false;

        $deptMap = $this->loadModel('dept')->getOptionMenu();
        $reviewList = inlink('view', "reviewID=$review->id");
        $account    = $this->app->user->account;
        $id = $col->id;
        $outsideList1 =array(''=>'') +$this->loadModel('user')->getUsersNameByType('outsideExpertType');
        $outsideList2 =array(''=>'') +$this->loadModel('user')->getUsersNameByType('outside');
        //$relatedUsers  = $this->loadModel('user')->getPairs('noletter');
        $this->app->loadLang('projectplan');
        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            if($id == 'id') $class .= ' cell-id';
            if($id == 'status')
            {
                $class .= ' status-' . $review->status;
                $name = zget($this->lang->review->statusLabelList, $review->status,'');
                $title  = "title='{$name}'";
            }
            if($id == 'result')
            {
                $class .= ' status-' . $review->result;
            }
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$review->title}'";
            }

            echo "<td class='" . $class . "' $title>";

            $dataTrial = $this->loadModel('review')->getTrial($reviewID, $review->version, $users, 2);
            $trialDeptIds = $dataTrial['deptid'];
            $trialDeptLiasisonOfficer = $dataTrial['deptjkr'];
            $trialAdjudicatingOfficer = $dataTrial['deptzs'];
            $trialJoinOfficer = $dataTrial['deptjoin'];

            switch($id)
            {

                case 'id':
                    if($canBatchAction)
                    {
                        echo html::checkbox('reviewIDList', array($review->id => '')) . html::a(helper::createLink('review', 'view', "reviewID=$review->id"), sprintf('%03d', $review->id));
                    }
                    else
                    {
                        printf('%03d', $review->id);
                    }
                    break;
                case 'title':
                    echo html::a(helper::createLink('reviewmanage', 'view', "reviewID=$review->id"), $review->title);
                    break;
                case 'product':
                    $txt =  zget($products, $review->product);
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'object':
                    $txt='';
                    $object = explode(',', $review->object);
                    foreach($object as $obj)
                    {
                        $obj = trim($obj);
                        if(empty($obj)) continue;
                        $txt .= zget($this->lang->review->objectList, $obj) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'version':
                    echo $review->version;
                    break;

                case 'status':
                    echo $review->statusDesc;

                    break;
                case 'type':
                    $type =  zget($this->lang->review->typeList, $review->type,'');
                    echo '<div class="ellipsis" title="' . $type . '">' . $type .'</div>';
                    break;

                case 'grade':
                    if($review->isConfirmGrade == 1){
                        echo  zget($this->lang->review->gradeList, $review->grade,'');
                    }else{
                        echo '';
                    }
                    break;

                case 'meetingPlanTime':
                    $meetingPlanTime = '';
                    if($review->meetingPlanTime != '0000-00-00 00:00:00' && $review->grade =='meeting'){
                        $meetingPlanTime = $review->meetingPlanTime;
                    }
                    echo $meetingPlanTime;
                    break;
                case 'meetingCode':
                    $meetingCode = '';
                    if($review->grade =='meeting'){
                        $meetingCode = $review->meetingCode;
                    }
                    echo  $meetingCode;
                    break;
                case 'meetingRealTime':
                    $meetingRealTime = '';
                    if($review->meetingRealTime != '0000-00-00 00:00:00'){
                        $meetingRealTime = $review->meetingRealTime;
                    }
                    echo $meetingRealTime;
                    break;

                case 'owner':
                    $txt='';
                    $owners = explode(',', $review->owner);
                    foreach($owners as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt.= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'expert':
                    $txt='';
                    $experts = explode(',', $review->expert);
                    foreach($experts as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'reviewedBy':
                    $txt='';
                    $reviewedBy = explode(',', $review->reviewedBy);
                    foreach($reviewedBy as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($outsideList1, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'outside':
                    $txt='';
                    $outside = explode(',', $review->outside);
                    foreach($outside as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($outsideList2, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'meetingPlanExport':
                    $txt = '';
                    $meetingPlanExport = explode(',', $review->meetingPlanExport);
                    foreach($meetingPlanExport as $account) {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'relatedUsers':
                    $txt='';
                    $relatedUsers = explode(',', $review->relatedUsers);

                    foreach($relatedUsers as $account)
                    {
                        $account = trim($account);
                        if(empty($account)) continue;
                        $txt .= zget($users, $account) . " &nbsp;";
                    }
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'createdBy':
                    echo zget($users, $review->createdBy,'');
                    break;
                case 'reviewer':
                    $txt=zget($users, $review->reviewer,'');
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'createdDate':
                    $txt=$review->createdDate;
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'deadline':
                    echo $review->deadline;
                    break;

                case 'projectType':
                    $txt= zget($this->lang->projectplan->typeList, $review->projectType,'');
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;

                case 'isImportant':
                    echo zget($this->lang->review->isImportantList, $review->isImportant,'');
                    break;

                case 'lastReviewedDate':
                    $txt= $review->lastReviewedDate;
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'lastAuditedDate':
                    $txt=$review->lastAuditedDate;
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'result':
                    $txt= zget($this->lang->review->resultList, $review->resulty,'');
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'auditResult':
                    $txt= zget($this->lang->review->auditResultList, $review->auditResulty,'');
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'dealUser':
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    $txt = '';
                    foreach($dealUser as $account)
                        $txt .= zget($users, $account,'') . " &nbsp;";
                    echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                    break;
                case 'deadDate':
                    $endDate =  $review->endDate != '0000-00-00 00:00:00' ? $review->endDate: '';
                    if(!empty($endDate)){
                        $endDate  = date('Y-m-d', strtotime($endDate));
                    }
                    echo '<div class="ellipsis" title="' . $endDate . '">' . $endDate .'</div>';
                    break;
                case 'editBy':
                    echo zget($users, $review->editBy,'');
                    break;
                case 'editDate':
                    echo '<div class="ellipsis" title="' . $review->editDate . '">' . $review->editDate .'</div>';
                    break;

                case 'createdDept':
                    echo zget($deptMap, $review->createdDept,'');
                    break;
                case 'closePerson':
                    echo zget($users, $review->closePerson,'');
                    break;
                case 'closeTime':
                    echo '<div class="ellipsis" title="' . $review->closeTime . '">' . $review->closeTime .'</div>';
                    break;
                case 'qualityQa':
                    echo zget($users, $review->qualityQa,'');
                    break;
                case 'trialDept':
                    echo '<div class="ellipsis" title="' . $trialDeptIds . '">' . $trialDeptIds .'</div>';
                    break;

                case 'trialDeptLiasisonOfficer':
                    echo '<div class="ellipsis" title="' . $trialDeptLiasisonOfficer . '">' . $trialDeptLiasisonOfficer .'</div>';
                    break;

                case 'trialAdjudicatingOfficer':
                    echo '<div class="ellipsis" title="' . $trialAdjudicatingOfficer . '">' . $trialAdjudicatingOfficer .'</div>';
                    break;

                case 'trialJoinOfficer':
                    echo '<div class="ellipsis" title="' . $trialJoinOfficer . '">' . $trialJoinOfficer .'</div>';
                    break;

                case 'preReviewDeadline':
                    echo $review->preReviewDeadline;
                    break;
                case 'firstReviewDeadline':
                    echo $review->firstReviewDeadline;
                    break;
                case 'closeDate':
                    echo $review->closeDate;
                    break;
                case 'qa':
                    echo zget($users, $review->qa);
                    break;

                case 'qualityCm':
                    echo zget($users, $review->qualityCm);
                    break;

                case 'actions':

                    $params  = "reviewID=$review->id&flag =1";
                    $params1 = "reviewID=$review->id&flag =1&source =1";
                    $flag = $this->loadModel('review')->isClickable($review, 'recall');
                    $click = $flag ? 'onclick="return recall()"' : '';

                    $closeflag = $this->loadModel('review')->isClickable($review, 'close');
                    $id = $review->id;
                    $nodealissue = $this->review->getNoDealIssue($id);
                    $count  = isset($nodealissue[$id]) ?  $nodealissue[$id] : '';
                    $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                    //取出最后一个评审人
                    //判断当前用户是否是最后一个验证人
                    $lastVerifyer ='';
                    if(count($dealUser) == 1){
                        $lastVerifyer = 1;
                    }
                    //是否允许审批
                    $verFlag = '';
                    $checkRes =  $this->loadModel('review')->checkReviewIsAllowReview($review, $this->app->user->account);
                    if($review->status == 'waitVerify' or $review->status == 'verifying' ){
                        $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
                        if($issueCount!=0 and $lastVerifyer ==1){
                            $verFlag = 1;
                        }elseif($issueCount!=0){
                            $verFlag = 2;
                        }
                    }
                    $reviewTipMsg = $this->loadModel('review')->getReviewTipMsg($review->status);
                    common::hasPriv('reviewmanage', 'edit') ?  common::printIcon('reviewmanage', 'edit',    $params1, $review, 'list') : '';
                    common::hasPriv('reviewmanage', 'submit') ? common::printIcon('reviewmanage', 'submit', $params, $review, 'list', 'play', '', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe"', $this->lang->review->submit) : '';
                    common::hasPriv('reviewmanage', 'recall') ? common::printIcon('reviewmanage', 'recall', $params, $review, 'list', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
                    common::hasPriv('reviewmanage', 'assign') ? common::printIcon('reviewmanage', 'assign', $params, $review, 'list','hand-right', '', 'iframe', true, 'data-position = "50px" data-width="1200px" data-toggle="modal" data-type="iframe" ', $this->lang->review->assign) : '';
                    //非最最后一个人验证时
                    if(($review->status == 'waitVerify' or $review->status == 'verifying' )&&$verFlag ==2){
                        $clickClose ='onclick="return reviewVerifyConfirm()"';
                        common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', 'hiddenwin', 'iframe', true,"$clickClose data-position = '50px' data-toggle='modal' data-type='iframe'", $reviewTipMsg) : '';
                    }else{
                        common::hasPriv('reviewmanage', 'review') ? common::printIcon('reviewmanage', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,'data-position = "50px" data-toggle="modal" data-type="iframe" ', $reviewTipMsg) : '';
                    }

                    common::hasPriv('reviewmanage', 'reviewreport') ? common::printIcon('reviewmanage', 'reviewreport',  $params, $review, 'list', 'bar-chart', '') : '';

                    if(common::hasPriv('reviewmanage', 'close'))
                    {
                        if($closeflag)
                        {
//                            $clickClose ="onclick=reviewClose('$review->id','$count')";
//                            common::printIcon('reviewmanage', 'close', $params, '', 'list', 'off', '', 'iframe', true, "$clickClose data-position = '50px' data-toggle='modal' data-type='iframe'", $this->lang->review->close);
                            echo '<a href="javascript:;" onclick="reviewClose('.$review->id.','.$count.')" class="btn"><i class="icon-review-close icon-off"></i></a>';

                        }
                        else
                        {
                            common::printIcon('reviewmanage', 'close', $params, $review, 'list', 'off','', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px"', $this->lang->review->close);
                        }
                    }

                    common::hasPriv('reviewmanage', 'delete') ? common::printIcon('reviewmanage', 'delete', $params, $review, 'list', 'trash','', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px"', $this->lang->review->delete) : '';


            }
            echo '</td>';
        }
    }

    /**
     * Judge button if can clickable.
     *
     * @param object $review
     * @param string $action
     * @access public
     * @return void
     */
    public static function isClickable($review, $action)
    {
        global $app;

        $action = strtolower($action);

        $dealUsers  = [];
        if($review->dealUser){
            $dealUsers = explode(',', $review->dealUser);
        }
        $reviewers = [];
        if(isset($review->reviewers)){
            $reviewers = explode(',', $review->reviewers);
        }
        $reviewer = [];
        if(isset($review->reviewer)){
            $reviewer = explode(',', $review->reviewer);
        }
        $qas = [];
        if(isset($review->qa)){
            $qas = explode(',', $review->qa);
        }
        $cms = [];
        if(isset($review->qualityCm)){
            $cms = explode(',', $review->qualityCm);
        }
        $allClose = array_merge($qas,$cms,$reviewer);

        $reviewModel = new reviewModel();

        if($action == 'edit') {
            return (in_array($review->status, $reviewModel->lang->review->allowEditStatusList) && (in_array($app->user->account, $dealUsers)));
        }

        //申请审批
        if($action == 'submit')  {
            return (in_array($review->status, $reviewModel->lang->review->allowSubmitStatusList) && ($review->createdBy == $app->user->account || in_array($app->user->account, $dealUsers)));
        }
		
        if($action == 'recall') { //撤销
           $notAllowRecallStatusList = $reviewModel->lang->review->notAllowRecallStatusList;
            return (!in_array($review->status, $notAllowRecallStatusList) && ($review->createdBy == $app->user->account));
        }

        //指派
        if($action == 'assign'){
            return (in_array($review->status,$reviewModel->lang->review->allowAssignStatusList) && (in_array($app->user->account, $reviewers)));
        }
        //审核操作
        if($action == 'review'){
            return (in_array($review->status, $reviewModel->lang->review->allowReviewStatusList) && (in_array($app->user->account, $reviewers)));
        }
        if($action == 'reviewreport')  return $review->status == 'reviewpass';

        if($action == 'close') {
            $notCloseStatusList = $reviewModel->lang->review->notCloseStatusList;
            return $review->status != 'close' && !in_array($review->status, $notCloseStatusList) && (in_array($app->user->account, $allClose));
        }
        if($action == 'delete'){
            return (!in_array($review->status, $reviewModel->lang->review->notAllowDeleteStatusList));
		}
        if($action == 'editnodeusers'){ //是否允许编辑
            if(!in_array($review->currentSubNode, $review->allowEditNodes)){
                return false;
            }
            $res = $reviewModel->getIsAllowEditNodeUsers($review->status);
            return  $res;
        }
        if($action == 'editfiles'){ //是否允许编辑附件
            return  (!in_array($review->status, $reviewModel->lang->review->notAllowEditFileStatusList) && ($review->createdBy == $app->user->account));
        }
        if($action == 'suspend'){ //挂起
            $res = $reviewModel->checkReviewIsAllowSuspend($review);
            return $res['result'];
        }
        if($action == 'renew'){//恢复
            $res =  $reviewModel->checkReviewIsAllowRenew($review, $app->user->account);
            return $res['result'];
        }
        if($action == 'setverifyresult'){
            $res =  $reviewModel->checkReviewIsAllowSetVerifyResult($review, $app->user->account);
            return $res['result'];
        }
        return true;
    }

   
    /**
     * Desc: 固定列表构建td
     * Date: 2022/4/19
     * Time: 15:08
     *
     * @param $col
     * @param $issue
     * @param $users
     * @param $reviews
     * @param $projectID
     *
     */
    public function printProblemReviewCell($col, $issue, $reviewID, $users, $reviews, $projectID, $status, $orderBy, $pager)
    {
        $id = $col->id;
        $params = "project=$projectID" . "&issudID=$issue->id" . "&reviewId=$reviewID" . "&statusNew=$status" . "&orderBy=$orderBy" . "&recTotal=$pager->recTotal" . "&recPerPage=$pager->recPerPage" . "&pageID=$pager->pageID";
        if ($col->show) {
            $class = "c-$id";
            $title = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->title}'";
            }
            if($id == 'review')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->reviewtitle}'";
            }
            echo "<td class='" . $class . "' $title>";
            switch ($id) {
                case 'id':
                    echo $issue->id;
                    break;
                case 'review':
                    echo html::a(helper::createLink('review', 'view', "reviewID=$issue->review"), $issue->reviewtitle);
                    break;
                case 'title':
                    echo html::a(helper::createLink('reviewmanage', 'viewproblem', $params), $issue->title);
                    break;
                case 'desc':
//                    echo $issue->desc;
                    echo '<div class="change" title="' . strip_tags($issue->desc) . '">' . helper::substr($issue->desc, 15, '...') .'</div>';
                    break;
                case 'type':
                    echo zget($this->lang->reviewissue->typeList, $issue->type);
                    break;
                case 'raiseBy':
                    echo zget($users, $issue->raiseBy);
                    break;
                case 'raiseDate':
                    echo $issue->raiseDate;
                    break;

                case 'resolutionBy':
                    echo zget($users, $issue->resolutionBy);
                    break;
                case 'resolutionDate':
                    echo $issue->resolutionDate;
                    break;
                case 'dealDesc':
                    echo $issue->dealDesc;
                    break;
                case 'validation':
                    echo zget($users, $issue->validation);
                    break;
                case 'verifyDate':
                    echo $issue->verifyDate;
                    break;
                case 'meetingCode':
                    echo $issue->meetingCode;
                    break;
                case 'editBy':
                    echo zget($users, $issue->editBy);
                    break;
                case 'editDate':
                    echo $issue->editDate;
                    break;
                case 'createdBy':
                    echo zget($users, $issue->createdBy);
                    break;
                case 'createdDate':
                    echo $issue->createdDate;
                    break;
                case 'status':
                    echo zget($this->lang->reviewissue->statusList, $issue->status);
                    break;
                case 'dealUser':
                    echo zget($users, $issue->dealUser);
                    break;
                case 'actions':
                    $recTotal = $pager->recTotal;
                    $recPerPage = $pager->recPerPage;
                    $pageID = $pager->pageID;
                    $param = "project=$projectID&issueID=$issue->id&source=list&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";

                    $ids = $this->loadModel('reviewissue')->getAllReviewId($projectID);
                    //评审已关闭，评审问题按钮置灰
                    if (in_array($issue->review, $ids)) {
                        common::hasPriv('reviewmanage', 'editProblem') ? common::printIcon('reviewmanage', 'editProblem', $param, $issue, 'list', 'edit', '', 'disabled', '', '') : '';
                        common::hasPriv('reviewmanage', 'resolvedProblem') ? common::printIcon('reviewmanage', 'resolvedProblem', $param, $issue, 'list', 'checked', '', 'disabled', '', "data-width=50%") : '';
                        common::hasPriv('reviewmanage', 'deleteProblem') ? common::printIcon('reviewmanage', 'deleteProblem', $param, $issue, 'list', 'trash', '', 'disabled', true, '', '') : '';

                    } else {
                        common::hasPriv('reviewmanage', 'editProblem') ? common::printIcon('reviewmanage', 'editProblem', $param, $issue, 'list','edit') : '';
                        common::hasPriv('reviewmanage', 'resolvedProblem') ? common::printIcon('reviewmanage', 'resolvedProblem', $param, $issue, 'list', 'checked', '', '', '', "data-width=50%") : '';
                        common::hasPriv('reviewmanage', 'deleteProblem') ? common::printIcon('reviewmanage', 'deleteProblem', $param, $issue, 'list', 'trash', '', 'iframe', true, '', '') : '';
                    }


            }


            echo '</td>';
        }


    }

    public function printIssueCell($col, $issue,$reviewID, $users,$reviews,$projectID,$status,$orderBy,$pager)
    {
        $id = $col->id;
        $params = "project=$projectID"."&issudID=$issue->id"."&reviewId=$reviewID"."&statusNew=$status"."&orderBy=$orderBy"."&recTotal=$pager->recTotal"."&recPerPage=$pager->recPerPage"."&pageID=$pager->pageID";
        if($col->show)
        {
            $class = "c-$id";
            $title  = '';
            if($id == 'title')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->title}'";
            }
            if($id == 'review')
            {
                $class .= ' text-left';
                $title  = "title='{$issue->reviewtitle}'";
            }
            echo "<td class='" . $class . "' $title>";
            switch($id)
            {
                case 'id':
                    echo $issue->id;
                    break;
                case 'review':
                    echo html::a(helper::createLink('reviewmanage', 'view', "reviewID=$issue->review"),'<div class="reviewTitle" title="' . $issue->reviewtitle . '">' . $issue->reviewtitle .'</div>');
                    break;

                case 'title':
                    echo html::a(helper::createLink('reviewproblem', 'view', $params),'<div class="problemTitle" title="' . $issue->title . '">' . $issue->title .'</div>');
                    break;
                case 'desc':
                    echo '<div class="change" title="' . strip_tags($issue->desc) . '">' . strip_tags($issue->desc) .'</div>';
                    break;
                case 'type':
                    echo zget($this->lang->reviewproblem->typeList, $issue->type);
                    break;
                case 'raiseBy':
                    echo zget($users, $issue->raiseBy);
                    break;
                case 'raiseDate':
                    echo $issue->raiseDate;
                    break;
                case 'resolutionBy':
                    echo zget($users, $issue->resolutionBy);
                    break;
                case 'resolutionDate':
                    echo $issue->resolutionDate;
                    break;
                case 'dealDesc':
                    echo $issue->dealDesc;
                    break;
                case 'validation':
                    echo zget($users, $issue->validation);
                    break;
                case 'verifyDate':
                    echo $issue->verifyDate;
                    break;
                case 'meetingCode':
                    echo '<div class="meetingCode" title="' . strip_tags($issue->meetingCode) . '">' . $issue->meetingCode .'</div>';
                    break;
                case 'editBy':
                    echo zget($users, $issue->editBy);
                    break;
                case 'editDate':
                    echo $issue->editDate;
                    break;
                case 'createdBy':
                    echo zget($users, $issue->createdBy);
                    break;
                case 'createdDate':
                    echo $issue->createdDate;
                    break;
                case 'status':
                    echo zget($this->lang->reviewproblem->statusList, $issue->status);
                    break;
                case 'dealUser':
                    echo zget($users, $issue->dealUser);
                    break;
                case 'actions':
                    $recTotal = $pager->recTotal;
                    $recPerPage = $pager->recPerPage;
                    $pageID = $pager->pageID;
                    $param = "project=$projectID&issueID=$issue->id&source=issue&review=$reviewID&status=$status&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";

                    $ids = $this->loadModel('reviewProblem')->getAllReviewId();
                    //评审已关闭，评审问题按钮置灰
                    if(in_array($issue->review,$ids)) {
                        common::hasPriv('reviewproblem','resolved') ? common::printIcon('reviewproblem', 'resolved', $param, $issue, 'list','checked','', 'iframe',true, "data-width=80%") :'';
                    }else{
                        common::hasPriv('reviewproblem','resolved') ? common::printIcon('reviewproblem', 'resolved', $param, $issue, 'list','checked','', 'iframe',true, "data-width=80%") :'';
                    }
            }
            echo '</td>';
        }

    }

    /**
     * Desc:获取评审问题数据
     * Date: 2022/7/25
     * Time: 9:38
     *
     * @param $projectID
     * @return mixed
     *
     */
    public function getReviewProblemPairs()
    {
        return $this->dao->select('id, title')->from(TABLE_REVIEW)
            ->where('deleted')->eq(0)
//            ->where('project')->eq($projectID)
            ->orderBy('id_desc')
            ->fetchPairs();
    }
    //批量获取评审
    public function getByIds($ids){
        if(!$ids) return new stdclass();
        $review = $this->dao->select('*')->from(TABLE_REVIEW)->where('id')->in($ids)->fetchall();
        $objects = $this->dao->select('*')->from(TABLE_REVIEWOBJECT)->where('review')->in($ids)->fetchAll();
        foreach ($review as $k=>$v) {
            foreach ($objects as $k2=>$v2) {
                if ($v2->review == $v->id){
                    $review[$k]->objects[] = $v2;
                }
            }
        }
        return $review;
    }
    public function setmeetingNew(){
        $data  = fixer::input('post')
            ->join('expert',',')
            ->remove('ids')
            ->get();
        $data->expert = trim($data->expert,',');
        $reviewmeetingModel = $this->loadModel("reviewmeeting");
        $ids = rtrim($_POST['ids'],',');

        $reviewIDS = explode(',',$ids);
        $idsCount = count($reviewIDS);

        $meetStatus = '';
        $reviewList = $this->getByIds($ids);
        foreach ($reviewList as $k=>$v) {
            $res = $reviewmeetingModel->checkMeetingIsAllowBind($v);
            if (!$res['result']){
                return $res;
            }
            $meetStatus = $v->status;
            if ($v->status == 'waitFormalReview'){
                $meetStatus = 'waitFormalReview';
            }
        }
        $reviewInfo = $reviewList[0];

        $meetingPlanTime = $data->feedbackExpireTime;
        $res = array(
            'result'  => false,
            'message' => '',
        );
        if(!($reviewInfo && $meetingPlanTime)){
            $res['message'] = $this->lang->reviewmeeting->checkBind['paramsError'];
            return $res;
        }
        $type = $reviewInfo->type;
        $params = [
            'type'              => $type,
            'meetingPlanTime' => $meetingPlanTime,
            'owner'             => $data->owner,
        ];
        $meetingInfo = $reviewmeetingModel->getInfo($params, 'id');
        if($meetingInfo && isset($meetingInfo->id)){
            $res['message'] = $this->lang->reviewmeeting->checkCreate['meetingExist'];
            return $res;
        }

        $currentUser = $this->app->user->account;
        $currentTime = helper::now();
        $meetingCodeSort = $reviewmeetingModel->setMeetingCodeSort($reviewInfo->type);
        $meetingCode     = $reviewmeetingModel->setMeetingCode($reviewInfo->type, $meetingCodeSort);
        $params = new stdClass();
        $params->meetingCode     = $meetingCode;
        $params->sortId          = $meetingCodeSort;
        $params->createUser      = $currentUser;
        $params->createTime      = $currentTime;
        $params->type            = $reviewInfo->type;
        $params->meetingPlanTime = $meetingPlanTime;
        $params->meetingPlanExport = $data->expert;
        $params->owner           = $data->owner;
        $params->status          = $meetStatus;

        if ($meetStatus == 'waitFormalReview'){
            $params->reviewer = "";
            $params->dealUser = "";
        }else{
            $allowReviewStatusList = $this->lang->review->allowReviewStatusList;
            $allowAssignStatusList = $this->lang->review->allowAssignStatusList;
            if (in_array($reviewInfo->status, $allowReviewStatusList) || in_array($reviewInfo->status, $allowAssignStatusList)) {
                if ($reviewInfo->status == 'baseline') {
                    $params->reviewer = $reviewInfo->dealUser;
                    $params->dealUser = $reviewInfo->dealUser;
                } else {
                    $reviewVersion = $this->review->getReviewVersion($reviewInfo);
                    $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewVersion, $reviewInfo->reviewStage);
                    $params->reviewer = $reviewers;
                    $params->dealUser = $reviewers;
                }
            }

        }


        $this->dao->insert(TABLE_REVIEW_MEETING)->data($params)
            ->autoCheck()
            ->batchCheck($this->config->reviewmanage->create->requiredFields, 'notempty')
            ->exec();
        if(dao::isError()){
            $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
            return $res;
        }

        $reviewMeetingId =  $this->dao->lastInsertID();
        //获得评审详情信息
        for ($i=0;$i<$idsCount;$i++){
            $reviewId = $reviewIDS[$i];
            $meetingDetailInfo = $reviewmeetingModel->getMeetingDetailInfoByReviewId($reviewId);
            if($meetingDetailInfo && isset($meetingDetailInfo->id)){
                if($meetingDetailInfo->meetingCode != $meetingCode){
                    $updateParams = new stdClass();
                    $updateParams->review_meeting_id = $reviewMeetingId;
                    $updateParams->meetingCode       = $meetingCode;
                    //更新详情表
                    $this->dao->update(TABLE_REVIEW_MEETING_DETAIL)->data($updateParams)
                        ->autoCheck()
                        ->where('id')->eq($meetingDetailInfo->id)->exec();
                    if(dao::isError()){
                        $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
                        return $res;
                    }
                }
            }else{
                //插入记录
                $params = new stdClass();
                $params->review_meeting_id = $reviewMeetingId;

                $params->meetingCode       = $meetingCode;
                $params->status            = $reviewInfo->status;
                $params->createUser        = $currentUser;
                $params->createTime        = $currentTime;
                $params->review_id         = $reviewId;
                $this->dao->insert(TABLE_REVIEW_MEETING_DETAIL)->data($params)
                    ->autoCheck()
                    ->exec();
                //修改信息

                if(dao::isError()){
                    $res['message'] = $this->lang->reviewmeeting->checkCreate['createError'];
                    return $res;
                }
            }
        }

        $res['result'] = true;
        return $res;
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->reviewmanage->search['actionURL'] = $actionURL;
        $this->config->reviewmanage->search['queryID']   = $queryID;

        $this->loadModel('search')->setSearchParams($this->config->reviewmanage->search);
    }


    /**
     * 根据用户部门id获取部门所有人
     *
     * @param string $browseType
     * @return mixed
     */
    public function getAllDeptPerson($deptId)
    {
        $allDeptPersons = $this->dao->select('account')->from(TABLE_USER)
            ->where('deleted')->eq(0)
            ->andWhere('dept')->eq($deptId)
            ->fetchAll();
        $newAllDeptPerson ="('";
        foreach ($allDeptPersons as $allDeptPerson){
            if(!empty($allDeptPerson->account)){
                $newAllDeptPerson = $newAllDeptPerson.$allDeptPerson->account."', '";
            }
        }
        $newAllDeptPerson=  substr($newAllDeptPerson,0,-3);
        $newAllDeptPerson = $newAllDeptPerson.")";
        return $newAllDeptPerson;
    }


    public function getReviewerInfoByNode($nodeId){
        $data = new stdclass();
        if(!$nodeId) {
            return $data;
        }
        $ret = $this->dao->select('t1.*,t2.id as reviewerID,t2.reviewer,t2.status as reviewerStatus,t2.comment,t2.extra')->from(TABLE_REVIEWNODE)->alias('t1')
            ->leftJoin(TABLE_REVIEWER)->alias('t2')
            ->on('t1.id=t2.node')
            ->where('t1.id')->eq($nodeId)
            ->orderBy('t2.id')
            ->fetchAll();
        if(!empty($ret)){
            $data = $ret;
        }
        return $data;
    }

}