<?php
/**
 * Project: chengfangjinke
 * Method: printReviewCell
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:46
 * Desc: This is the code comment. This method is called printReviewCell.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $col
 * @param $review
 * @param $users
 * @param array $products
 */
public function printReviewCell($col, $review, $users, $products = array())
{
    $canView = common::hasPriv('my', 'review');
    $canBatchAction = false;

    $deptMap = $this->loadModel('dept')->getOptionMenu();
    $reviewList = inlink('view', "reviewID=$review->id");
    $account    = $this->app->user->account;
    $id = $col->id;
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
        if($id == 'title')
        {
            $class .= ' text-left';
            $title  = "title='{$review->title}'";
        }

        echo "<td class='" . $class . "' $title>";
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
                echo html::a(helper::createLink('review', 'view', "reviewID=$review->id"), $review->title);
                break;
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
            case 'status':
                echo $review->statusDesc;

                break;
            case 'type':
                $txt = zget($this->lang->review->typeList, $review->type,'');
                echo '<div class="ellipsis" title="' .$txt . '">' . $txt .'</div>';
                break;
            case 'grade':
                $stage = $this->loadModel('review')->getStage($review->id,$review->version);
                if(isset($stage->stage) && ($stage->stage > 6)){
                    echo zget($this->lang->review->gradeList, $review->grade,'');
                    break;
                }else{
                    echo '';
                    break;
                }
            case 'mark':
                echo  $review->mark;
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
                    $txt .= zget($users, $account) . " &nbsp;";
                }
                echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                break;
            case 'createdBy':
                echo zget($users, $review->createdBy,'');
                break;
            case 'reviewer':
                echo zget($users, $review->reviewer,'');
                break;
            case 'createdDate':
                echo $review->createdDate;
                break;
            case 'deadline':
                echo $review->deadline;
                break;
            case 'dealUser':
                $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                $txt = '';
                foreach($dealUser as $account)
                    $txt .= zget($users, $account,'') . " &nbsp;";
                echo '<div class="ellipsis" title="' . $txt . '">' . $txt .'</div>';
                break;
            case 'editBy':
                echo zget($users, $review->editBy,'');
                break;
            case 'editDate':
                echo '<div class="ellipsis" title="' . $review->editDate . '">' . $review->editDate .'</div>';
                break;
            case 'createdDept':
                $dept =  zget($deptMap, $review->createdDept,'');
                echo '<div class="ellipsis" title="' . $dept . '">' .$dept .'</div>';
                break;
            case 'actions':
                $params  = "reviewID=$review->id";
                $flag = $this->loadModel('review')->isClickable($review, 'recall');
                $click = $flag ? 'onclick="return recall()"' : '';
                $closeflag = $this->loadModel('review')->isClickable($review, 'close');
                $id = $review->id;
                $nodealissue = $this->review->getNoDealIssue($id);
                $count  = isset($nodealissue[$id]) ?  $nodealissue[$id] : '';
                $reviewTipMsg = $this->loadModel('review')->getReviewTipMsg($review->status);
                common::hasPriv('review', 'edit') ?  common::printIcon('review', 'edit',    $params, $review, 'list','','app=project') : '';
                common::hasPriv('review', 'submit') ? common::printIcon('review', 'submit', $params, $review, 'list', 'play', 'app=project', 'iframe', true, '', $this->lang->review->submit) : '';
                common::hasPriv('review', 'recall') ? common::printIcon('review', 'recall', $params, $review, 'list', 'back', 'hiddenwin', '', '', "$click", $this->lang->review->recall) : '';
                common::hasPriv('review', 'assign') ? common::printIcon('review', 'assign', $params, $review, 'list','hand-right', 'app=project', 'iframe', true, '', $this->lang->review->assign) : '';
//                common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'list', 'glasses', 'app=project', 'iframe', true, '', $this->lang->review->review) : '';
                //是否允许审批
                $dealUser = explode(',', str_replace(' ', '', $review->dealUser));
                //取出最后一个评审人
                //判断当前用户是否是最后一个验证人
                $lastVerifyer ='';
                if(count($dealUser) == 1){
                    $lastVerifyer = 1;
                }
                $verFlag = '';
                $checkRes = $this->loadModel('review')->checkReviewIsAllowReview($review, $this->app->user->account);
                if($review->status == 'waitVerify' or $review->status == 'verifying' ){
                    $issueCount = $this->loadModel('reviewproblem')->getReviewIssueCount2($review->id,'createAndAccept');
                    if($issueCount!=0 and $lastVerifyer ==1){
                        $verFlag = 1;
                    }elseif($issueCount!=0){
                        $verFlag = 2;
                    }
                }
                //非最最后一个人验证时
                if(($review->status == 'waitVerify' or $review->status == 'verifying' )&&$verFlag ==2){
                    $clickClose ='onclick="return reviewVerifyConfirm()"';
                    common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'list', 'glasses', 'hiddenwin', 'iframe', true,"$clickClose", $reviewTipMsg) : '';
                }else{
                    common::hasPriv('review', 'review') ? common::printIcon('review', 'review', $params, $review, 'list', 'glasses', '', 'iframe', true,' data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px" ', $reviewTipMsg) : '';
                }
                if(common::hasPriv('review', 'close'))
                {
                    if($closeflag)
                    {
                        common::printIcon('review', 'close', $params, '', 'list', 'off', '', 'iframe', true, "style='display:none;' id='reviewClose{$review->id}'", $this->lang->review->close);
                        echo '<a href="javascript:;" onclick="reviewClose('.$review->id.','.$count.')" class="btn"><i class="icon-review-close icon-off"></i></a>';

                    }
                    else
                    {
                        common::printIcon('review', 'close', $params, $review, 'list', 'off','', 'iframe', true, '', $this->lang->review->close);
                    }
                }
                common::hasPriv('review', 'delete') ? common::printIcon('review', 'delete', $params, $review, 'list', 'trash','app=project', 'iframe', true, '', $this->lang->review->delete) : '';


        }
        echo '</td>';
    }
}

/**
 * 获取待处理下的各个对象总数。
 */
public function pendingSummary()
{
    $account         = $this->app->user->account;
    $taskSummary     = $this->taskSummary($account);
    $storySummary    = $this->storySummary($account);
    $bugSummary      = $this->bugSummary($account);
    $caseSummary     = $this->caseSummary($account, 'id_desc', '');
    $testtaskSummary = $this->testtaskSummary($account, 'id_desc', 'wait');
    $issueSummary    = $this->issueSummary('assignedTo', $account, 'id_desc');
    $riskSummary     = $this->riskSummary('assignedTo', $account, 'id_desc');
    $ncSummary       = $this->ncSummary($account, 'assignedToMe', 'id_desc');
    $auditSummary    = 0;

    $copyrightqzCount      = $this->getUserCopyrightqzCount('id_desc');
    $copyrightCount      = $this->getUserCopyrightCount('id_desc');
    $putproductionCount   = $this->getUserPutproductionCount('id_desc');
    $modifyCount      = $this->getUserModifyCount('id_desc');
//    $modifycnccCount      = $this->getUserModifycnccCount('id_desc');
    $outwardDeliveryCount      = $this->getUserOutwardDeliveryCount('id_desc');
    $fixCount         = $this->getUserFixCount('id_desc');
    $gainCount        = $this->getUserGainCount('id_desc');
    $gainQzCount      = $this->getUserGainQzCount('id_desc');
    $creditCount      = $this->getUserCreditCount('id_desc');
    $changeCount      = $this->getUserChangeCount($account,'id_desc');
    $projectplanCount = $this->getUserProjectplanCount('id_desc');
    $projectplanShCount = $this->getUserProjectplanShCount('id_desc');
    $defectCount     = $this->getUserDefectCount('id_desc');
    $projectplanChangeCount = $this->getUserProjectplanChangeCount('id_desc');
    $projectplanStartCount = $this->getUserProjectplanStartCount('id_desc');
    $projectplanShChangeCount = $this->getUserProjectplanShChangeCount('id_desc');
    $projectplanShStartCount = $this->getUserProjectplanShStartCount('id_desc');
    $requirementCount = $this->getUserRequirementCount('id_desc');
    $requirementInsideCount = $this->getUserRequirementInsideCount('id_desc');
    $componentCount = $this->getUserComponentCount('id_desc');
    $sectransferCount = $this->getUserSectransferCount('id_desc');
    $cmdbsyncCount = $this->getUserCmdbsyncCount('id_desc');
    $osspchangeCount = $this->getUserOsspchangeCount('id_desc');
    $closingitemCount = $this->getUserClosingItemCount('id_desc');
    $closingadviseCount = $this->getUserClosingAdviseCount('id_desc');
    $datamanagementCount = $this->getUserDatamanagementCount('id_desc');
    $problemCount     = $this->getUserProblemCount();
    $preproductionCount     = $this->getUserPreproductionCount();
    $secondorderCount = $this->getUserSecondorderCount();
    $deptorderCount = $this->getUserDeptorderCount();
    $demandCount      = $this->getUserDemandCount();
    $demandInsideCount      = $this->getUserDemandInsideCount();
    $opinionCount      = $this->getUserOpinionCount();
    $opinionInsideCount      = $this->getUserOpinionInsideCount();
    $buildCount      = $this->getUserBuildCount();
    //版本发布
    $projectReleaseCount = $this->getUserProjectReleaseCount('id_desc');
    $residentsupportCount = $this->getUserResidentSupportCount();
    $localeSupportCount = $this->getUserLocaleSupportCount($account);

    $qualityGateCount = $this->getUserQualityGateCount($account);
    $EnvironmentOrderCount   = $this->getTodealEnvironmentOrderCount($account);
    $authorityCount   = $this->getTodealAuthorityapplyCount($account);
    $auditSummary    += ($copyrightCount + $copyrightqzCount + $putproductionCount + $modifyCount + $fixCount + $gainCount + $gainQzCount + $creditCount +  $changeCount + $projectplanCount + $projectplanChangeCount +$projectplanStartCount + $projectplanShCount + $projectplanShChangeCount +$projectplanShStartCount + $requirementCount+ $requirementInsideCount + $problemCount + $secondorderCount + $demandCount + $demandInsideCount+ $outwardDeliveryCount + $opinionCount+ $opinionInsideCount + $componentCount + $buildCount + $projectReleaseCount + $residentsupportCount + $localeSupportCount + $datamanagementCount  + $deptorderCount+$sectransferCount+$closingitemCount+$closingadviseCount+$osspchangeCount+$cmdbsyncCount+$preproductionCount+$EnvironmentOrderCount+$authorityCount + $qualityGateCount);

    $summaryList = array(
        'task'     => $taskSummary,
        'story'    => $storySummary,
        'bug'      => $bugSummary,
        'testcase' => $caseSummary,
        'testtask' => $testtaskSummary,
        'issue'    => $issueSummary,
        'risk'     => $riskSummary,
        'nc'       => $ncSummary,
        'audit'    => $auditSummary,
        'reviewObject' => array('copyright'=>$copyrightCount, 'copyrightqz'=>$copyrightqzCount, 'putproduction' => $putproductionCount, 'modify' => $modifyCount, 'fix' => $fixCount, 'gain' => $gainCount, 'gainqz' => $gainQzCount, 'credit' => $creditCount, 'change' => $changeCount, 'projectplan' => $projectplanCount, 'projectplanChange' => $projectplanChangeCount, 'projectplanStart' => $projectplanStartCount, 'projectplansh' => $projectplanShCount,'projectplanshChange' => $projectplanShChangeCount, 'projectplanshStart' => $projectplanShStartCount, 'requirement' => $requirementCount,'requirementinside' => $requirementInsideCount, 'problem' => $problemCount, 'secondorder' => $secondorderCount, 'deptorder' => $deptorderCount, 'demand' => $demandCount,'demandinside' => $demandInsideCount, 'outwarddelivery' => $outwardDeliveryCount, 'opinion' => $opinionCount,'opinioninside' => $opinionInsideCount,'component' => $componentCount,'datamanagement' => $datamanagementCount,'build'=> $buildCount, 'projectrelease' => $projectReleaseCount,'residentsupport'=> $residentsupportCount,'localesupport'=> $localeSupportCount, 'environmentorder'=> $EnvironmentOrderCount,'sectransfer'=>$sectransferCount,'closingitem'=>$closingitemCount,'closingadvise'=>$closingadviseCount,'osspchange'=>$osspchangeCount,'cmdbsync'=>$cmdbsyncCount,'productionchange'=>$preproductionCount,'authorityapply'=>$authorityCount, 'qualitygate' => $qualityGateCount),
        'defect'    => $defectCount
    );

    /* 工作流额外数量统计。*/
    $flowPairs = $this->loadModel('customflow')->getFlowPairs();
    foreach($flowPairs as $flowCode => $flowName)
    {
        $flowTotal = $this->getFlowSummary($flowCode);
        $summaryList['audit'] += $flowTotal;
        $summaryList['reviewObject'][$flowCode] = $flowTotal;
    }

    return $summaryList;
}

public function getFlowSummary($flowCode)
{
    $flowList = $this->loadModel('customflow')->getFlowList();
    $pendingField = empty($flowList[$flowCode]['flowAssign']) ? 'assignedBy' : $flowList[$flowCode]['flowAssign'];

    /* 获取工作流列表待处理数据 */
    $total = $this->dao->select('count(*) as total')->from('zt_flow_' . $flowCode)->where($pendingField)->eq($this->app->user->account)->andWhere('deleted')->eq('0')->fetch('total');
    return empty($total) ? 0 : $total;
}

/**
 * 获取各个要审批对象总数。
 */
public function getAuditSummary()
{
    $account         = $this->app->user->account;
    $copyrightqzCount    = $this->getUserCopyrightqzCount('id_desc');
    $copyrightCount    = $this->getUserCopyrightCount('id_desc');
    $putproductionCount   = $this->getUserPutproductionCount('id_desc');
    $modifyCount      = $this->getUserModifyCount('id_desc');
    $outwardDelivery      = $this->getUserOutwardDeliveryCount('id_desc');
    $fixCount         = $this->getUserFixCount('id_desc');
    $gainCount        = $this->getUserGainCount('id_desc');
    $gainqzCount      = $this->getUserGainQzCount('id_desc');
    $creditCount    = $this->getUserCreditCount('id_desc');
    $changeCount      = $this->getUserChangeCount($account, 'id_desc');
    $projectplanCount = $this->getUserProjectplanCount('id_desc');
    $projectplanShCount = $this->getUserProjectplanShCount('id_desc');
    $defectCount = $this->getUserDefectCount('id_desc');
    $projectplanChangeCount = $this->getUserProjectplanChangeCount('id_desc');
    $projectplanStartCount = $this->getUserProjectplanStartCount('id_desc');
    $projectplanShChangeCount = $this->getUserProjectplanShChangeCount('id_desc');
    $projectplanShStartCount = $this->getUserProjectplanShStartCount('id_desc');
    $requirementCount = $this->getUserRequirementCount('id_desc');
    $requirementInsideCount = $this->getUserRequirementInsideCount('id_desc');
    $componentCount = $this->getUserComponentCount('id_desc');
    $sectransferCount = $this->getUserSectransferCount('id_desc');
    $cmdbsyncCount = $this->getUserCmdbsyncCount('id_desc');
    $osspchangeCount = $this->getUserOsspchangeCount('id_desc');
    $closingitemCount = $this->getUserClosingItemCount('id_desc');
    $closingadviseCount = $this->getUserClosingAdviseCount('id_desc');
    $datamanagementCount = $this->getUserDatamanagementCount('id_desc');
    $problemCount     = $this->getUserProblemCount();
    $preproductionCount     = $this->getUserPreproductionCount();
    $secondorderCount = $this->getUserSecondorderCount();
    $deptorderCount = $this->getUserDeptorderCount();
    $demandCount      = $this->getUserDemandCount();
    $demandInsideCount      = $this->getUserDemandInsideCount();
    $opinionCount      = $this->getUserOpinionCount();
    $opinionInsideCount      = $this->getUserOpinionInsideCount();
    $residentsupportCount = $this->getUserResidentSupportCount('id_desc');
    $localeSupportCount   = $this->getUserLocaleSupportCount($account);
    $qualityGateCount   = $this->getUserQualityGateCount($account);
    $EnvironmentOrderCount   = $this->getTodealEnvironmentOrderCount($account);
    $authorityApplyCount   = $this->getTodealAuthorityapplyCount($account);
    $build = $this->getUserBuildCount();
    //$reviewqzCount      = $this->getUserReviewqzCount();
    //版本发布
    $projectReleaseCount = $this->getUserProjectReleaseCount('id_desc');
    $opinionCount = $opinionCount ?? 0;
    $opinionInsideCount = $opinionInsideCount ?? 0;
    $audits = array('copyright' => $copyrightCount,'copyrightqz'=>$copyrightqzCount,'opinion' => $opinionCount,'opinioninside' => $opinionInsideCount,  'putproductionCount' => $putproductionCount, 'modify' => $modifyCount, 'fix' => $fixCount, 'gain' => $gainCount, 'gainqz' => $gainqzCount, 'credit' => $creditCount,'change' => $changeCount, 'projectplan' => $projectplanCount, 'projectplanChange' => $projectplanChangeCount, 'projectplanStart' => $projectplanStartCount, 'projectplanSh' => $projectplanShCount, 'projectplanShChange' => $projectplanShChangeCount, 'projectplanShStart' => $projectplanShStartCount,'requirement' => $requirementCount, 'requirementinside' => $requirementInsideCount, 'problem' => $problemCount, 'secondorder' => $secondorderCount, 'deptorder' => $deptorderCount, 'demand' => $demandCount,'demandInside' => $demandInsideCount, 'outwardDelivery' => $outwardDelivery,'component' => $componentCount,'datamanagement' => $datamanagementCount,'residentsupportCount' => $residentsupportCount, 'localesupport' => $localeSupportCount, 'build'=>$build, 'projectrelease' => $projectReleaseCount, 'sectransfer'=>$sectransferCount,'closingitem'=>$closingitemCount,'closingadvise'=>$closingadviseCount,'osspchange'=>$osspchangeCount,'cmdbsync'=>$cmdbsyncCount,'productionchange'=>$preproductionCount,'environmentorder'=>$EnvironmentOrderCount,'authorityapply' => $authorityApplyCount, 'qualitygate' => $qualityGateCount);

    /* 工作流额外数量统计。*/
    $flowPairs = $this->loadModel('customflow')->getFlowPairs();
    foreach($flowPairs as $flowCode => $flowName)
    {
        $flowTotal = $this->getFlowSummary($flowCode);
        $audits[$flowCode] = $flowTotal;
    }
    return $audits;
}

/**
 * 判断待处理菜单下是否有要处理的对象。
 */
public function getHandle()
{
    $hasProcessing = false;
    $account       = $this->app->user->account;

    // 优先查是否有审批的数据。
    if(empty($hasProcessing)) $hasProcessing = $this->getUserCopyrightqzCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserCopyrightCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserPutproductionCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserModifyCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserOutwardDeliveryCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserFixCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserGainCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserGainQzCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserCreditCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserChangeCount($account, 'id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectplanCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectplanShCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserDefectCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectplanChangeCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectplanStartCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectplanShChangeCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectplanShStartCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserRequirementCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserRequirementInsideCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserComponentCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserSectransferCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserCmdbsyncCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserOsspchangeCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserClosingItemCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserClosingAdviseCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserDatamanagementCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProblemCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserPreproductionCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserSecondorderCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserDeptorderCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserDemandCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserDemandInsideCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserOpinionCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserOpinionInsideCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserBuildCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserProjectReleaseCount('id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->getUserResidentSupportCount();
    if(empty($hasProcessing)) $hasProcessing = $this->getUserLocaleSupportCount($account);
    if(empty($hasProcessing)) $hasProcessing = $this->getUserQualityGateCount($account);
    if(empty($hasProcessing)) $hasProcessing = $this->getTodealEnvironmentOrderCount($account);
    if(empty($hasProcessing)) $hasProcessing = $this->getTodealAuthorityapplyCount($account);
    /* 工作流额外数量统计。*/
    $flowPairs = $this->loadModel('customflow')->getFlowPairs();
    foreach($flowPairs as $flowCode => $flowName)
    {
        $flowTotal = $this->getFlowSummary($flowCode);
        if($flowTotal && empty($hasProcessing))
        {
            $hasProcessing = $flowTotal;
            break;
        }
    }

    if(empty($hasProcessing)) $hasProcessing = $this->taskSummary($account);
    if(empty($hasProcessing)) $hasProcessing = $this->storySummary($account);
    if(empty($hasProcessing)) $hasProcessing = $this->bugSummary($account);
    if(empty($hasProcessing)) $hasProcessing = $this->caseSummary($account, 'id_desc', 'skip');
    if(empty($hasProcessing)) $hasProcessing = $this->testtaskSummary($account, 'id_desc', 'wait');
    if(empty($hasProcessing)) $hasProcessing = $this->issueSummary('assignedTo', $account, 'id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->riskSummary('assignedTo', $account, 'id_desc');
    if(empty($hasProcessing)) $hasProcessing = $this->ncSummary($account, 'assignedToMe', 'id_desc');

    return $hasProcessing;
}

/**
 * Project: chengfangjinke
 * Method: taskSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called taskSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param string $type
 * @param int $limit
 * @param string $orderBy
 * @param int $projectID
 * @return int|mixed
 */
public function taskSummary($account, $type = 'assignedTo', $limit = 0, $orderBy = "id_desc", $projectID = 0)
{
   $tasks = $this->dao->select('count(*) as taskSummary')
       ->from(TABLE_TASK)
       ->where('deleted')->eq(0)
       ->andWhere('status')->ne('closed')
       ->andWhere("`$type`")->eq($account)
       ->fetch('taskSummary');
    return empty($tasks) ? 0 : $tasks;
}

/**
 * Project: chengfangjinke
 * Method: storySummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called storySummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param string $type
 * @param string $orderBy
 * @param string $storyType
 * @return int|mixed
 */
public function storySummary($account, $type = 'assignedTo', $orderBy = 'id_desc', $storyType = 'story')
{
    $stories = $this->dao->select('count(*) as storySummary')->from(TABLE_STORY)
        ->where('deleted')->eq(0)
        ->andWhere('type')->eq($storyType)
        ->andWhere('status')->ne('closed')
        ->andWhere('assignedTo')->eq($account)
        ->fetch('storySummary');
    return empty($stories) ? 0 : $stories;
}

/**
 * Project: chengfangjinke
 * Method: bugSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called bugSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param string $type
 * @param string $orderBy
 * @param int $limit
 * @param int $executionID
 * @return int|mixed
 */
public function bugSummary($account, $type = 'assignedTo', $orderBy = 'id_desc', $limit = 0, $executionID = 0)
{
    $bugs = $this->dao->select('count(*) as bugSummary')->from(TABLE_BUG)
        ->where('deleted')->eq(0)
        ->andWhere("`$type`")->eq($account)
        ->fetch('bugSummary');
    return empty($bugs) ? 0 : $bugs;
}

/**
 * Project: chengfangjinke
 * Method: caseSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called caseSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param string $orderBy
 * @param string $auto
 * @return int|mixed
 */
public function caseSummary($account, $orderBy = 'id_desc', $auto = 'no')
{
    $ceses = $this->dao->select('count(t1.id) as caseSummary')->from(TABLE_TESTRUN)->alias('t1')
        ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case = t2.id')
        ->leftJoin(TABLE_TESTTASK)->alias('t3')->on('t1.task = t3.id')
        ->where('(t1.assignedTo')->eq($account)->orWhere('t1.lastRunner')->eq($account)->orWhere('t2.openedBy')->eq($account)->markRight(1)
        ->andWhere('t3.deleted')->eq(0)
        ->andWhere('t2.deleted')->eq(0)
        ->beginIF($auto != 'skip' and $auto != 'unit')->andWhere('t2.auto')->ne('unit')->fi()
        ->beginIF($auto == 'unit')->andWhere('t2.auto')->eq('unit')->fi()
        ->fetch('caseSummary');
    return empty($ceses) ? 0 : $ceses;
}

/**
 * Project: chengfangjinke
 * Method: testtaskSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called testtaskSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param string $orderBy
 * @param string $type
 * @return int|mixed
 */
public function testtaskSummary($account, $orderBy = 'id_desc', $type = '')
{
    $testtasks = $this->dao->select('count(*) as testtaskSummary')->from(TABLE_TESTTASK)
        ->where('deleted')->eq(0)
        ->andWhere('auto')->eq('no')
        ->andWhere('owner')->eq($account)
        ->beginIF($type == 'wait')->andWhere('status')->ne('done')->fi()
        ->beginIF($type == 'done')->andWhere('status')->eq('done')->fi()
        ->orderBy($orderBy)
        ->fetch('testtaskSummary');
    return empty($testtasks) ? 0 : $testtasks;
}

/**
 * Project: chengfangjinke
 * Method: issueSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called issueSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param string $type
 * @param string $account
 * @param string $orderBy
 * @return int|mixed
 */
public function issueSummary($type = 'assignedTo', $account = '', $orderBy = 'id_desc')
{
    $issues = $this->dao->select('count(*) as issueSummary')->from(TABLE_ISSUE)
        ->where('deleted')->eq('0')
        ->andWhere("($type= '$account' or frameworkUser = '$account')")
        ->fetch('issueSummary');
    return empty($issues) ? 0 : $issues;
}

/**
 * Project: chengfangjinke
 * Method: riskSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called riskSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param string $type
 * @param string $account
 * @param string $orderBy
 * @return int|mixed
 */
public function riskSummary($type = 'assignedTo', $account = '', $orderBy = 'id_desc')
{
    $risks = $this->dao->select('count(*) as riskSummary')->from(TABLE_RISK)
        ->where('deleted')->eq('0')
        ->andWhere("($type = '$account' or frameworkUser = '$account')")
        ->fetch('riskSummary');
    return empty($risks) ? 0 : $risks;
}

/**
 * Project: chengfangjinke
 * Method: ncSummary
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called ncSummary.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param string $account
 * @param string $browseType
 * @param string $orderBy
 * @return int|mixed
 */
public function ncSummary($account = '', $browseType = 'assigendToMe', $orderBy = 'id_desc')
{
    /*$ncs = $this->dao->select('count(*) as ncSummary')->from(TABLE_NC)
        ->where('deleted')->eq(0)
        ->andWhere('assignedTo')->eq($account)
        ->fetch('ncSummary');*/
    $this->loadModel('weeklyreport');
    $this->loadModel('project');

    //查询当前登录用户所属部门
    $qaDepts = $this->weeklyreport->getUserQADept($this->app->user->account);


    if($qaDepts['isogQA'] == 1){

        $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where(" (status = 'projected') ")->andWhere('deleted')->eq(0);
        $projectplanProjectedList =  $this->dao->orderBy("id desc")->fetchAll();

    }else{
        $deptsIDArr = array_column($qaDepts['depts'],'id');
        if($deptsIDArr){
            $this->dao->select("*")->from(TABLE_PROJECTPLAN)->where(" (status = 'projected') AND ");

            $this->dao->markLeft(1);
            $tempcount = count($deptsIDArr) - 1;
            foreach ($deptsIDArr as $key => $dept){
                if($tempcount == $key){
                    $this->dao->where(" FIND_IN_SET('{$dept}',`bearDept`) ");
                }else{
                    $this->dao->where(" FIND_IN_SET('{$dept}',`bearDept`) or ");
                }
            }
            $this->dao->markRight(1);
            $projectplanProjectedList =  $this->dao->andWhere('deleted')->eq(0)->orderBy("id desc")->fetchAll();
        }else{
            $projectplanProjectedList = [];
        }

    }
    foreach ($projectplanProjectedList as $key=>$projectplan){

            $project = $this->project->getByID($projectplan->project);
            if(!$project){
                unset($projectplanProjectedList[$key]);
                continue;
            }
            if($project->status == 'closed'){
                unset($projectplanProjectedList[$key]);
            }


    }
    return $projectplanProjectedList ? count($projectplanProjectedList) : 0 ;
}

/**
 * 获得金信投产列表
 *
 * @param $orderBy
 * @return array
 */
public function getUserPutproductionList($orderBy){
    $data = [];
    $account = $this->app->user->account;
    $ret = $this->dao->select('*')
        ->from(TABLE_PUTPRODUCTION)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetchAll();
    if($ret){
        $data = $ret;
    }
    return $data;
}

/**
 * 获得cmdb同步列表
 *
 * @param $orderBy
 * @return array
 */
public function getUserCmdbsyncList($orderBy){
    $data = [];
    $account = $this->app->user->account;
    $ret = $this->dao->select('*')
        ->from(TABLE_CMDBSYNC)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetchAll();
    if($ret){
        $data = $ret;
    }
    return $data;
}

/**
 * 征信交付列表
 *
 * @param $orderBy
 * @return array
 */
public function getUserCreditList($orderBy){
    $data = [];
    $account = $this->app->user->account;
    $ret = $this->dao->select('*')
        ->from(TABLE_CREDIT)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUsers)")
        ->orderBy($orderBy)
        ->fetchAll();
    if($ret){
        $data = $ret;
    }
    return $data;
}

/**
 * Project: chengfangjinke
 * Method: getUserModifyList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserModifyList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserModifyList($orderBy)
{
    $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('modify', $this->app->user->account);
    $dealUserList = explode(',', $dealUserList);
    $condition = '';
    if(!empty($dealUserList)){
        $this->loadModel('modify');
        foreach ($dealUserList as $dealUser){
            if(strpos($condition, 'FIND_IN_SET') !== false){
                if($this->app->user->account == $dealUser){
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }else{
                if($this->app->user->account == $dealUser){
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }
        }
    }

    $modifys = $this->dao->select('*')->from(TABLE_MODIFY)
        ->where('status')->ne('deleted')
        //->andWhere('issubmit')->eq('submit')
        ->beginIF(!empty($condition))->andWhere($condition)->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');
    $apps  = $this->loadModel('application')->getapplicationNameCodePairsWithisPayment();
    $appList = array();
    foreach($apps as $app){
        $appList[$app->id] = $app->name;
    }


    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    foreach($modifys as $key => $modify)
    {
        $apps = array();
        foreach(explode(',',$modify->app)  as $app){
          if(!empty($app)){
            $apps[] = zget($appList,$app);
          }
        }
        $modify->app = implode('，',$apps);

        // if(empty($modify->dealUser)){
        //     $modify->dealUser = $this->review->getReviewer('modify', $modify->id, $modify->version, $modify->reviewStage);
        // }
        $modify->dealUser = $this->loadModel('common')->getAuthorizer('modify', $modify->dealUser,$modify->status, $this->lang->modify->authorizeStatusList);
        if(strpos(",$modify->dealUser,", ",{$account},") === false)
        {
            unset($modifys[$key]);
            continue;
        }
        $accountList[$modify->createdBy] = $modify->createdBy;
    }

    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
    foreach($modifys as $key => $modify)
    {
        $modifys[$key]->createdDept = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->dept : '';
        $modifys[$key]->realname    = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->realname : '';
    }

    return $modifys;
}

/**
 * Project: chengfangjinke
 * Method: getUserModifyCnccList
 * User: shixuyang
 * Date: 2022/6/1
 * Desc: 获取清总变更单的代办
 * @param $orderBy
 * @return mixed
 */
public function getUserModifycnccList($orderBy)
{
    $modifycnccs = $this->dao->select('*')->from(TABLE_MODIFYCNCC)
        ->where('status')->ne('deleted')
        //->andWhere('reviewStage')->gt(0)
        ->orderBy($orderBy)
        ->fetchAll('id');

    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    foreach($modifycnccs as $key => $modifycncc)
    {
        $modifycnccs[$key]->reviewers = $this->review->getReviewer('modifycncc', $modifycncc->id, $modifycncc->version, $modifycncc->reviewStage);
        if(strpos(",$modifycncc->reviewers,", ",{$account},") === false)
        {
            unset($modifycnccs[$key]);
            continue;
        }
        $accountList[$modifycncc->createdBy] = $modifycncc->createdBy;
    }

    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
    foreach($modifycnccs as $key => $modifycncc)
    {
        $modifycnccs[$key]->createdDept = isset($dmap[$modifycncc->createdBy]) ? $dmap[$modifycncc->createdBy]->dept : '';
        $modifycnccs[$key]->realname    = isset($dmap[$modifycncc->createdBy]) ? $dmap[$modifycncc->createdBy]->realname : '';
    }

    return $modifycnccs;
}

public function getUserCopyrightqzList($orderBy)
{
    $account = $this->app->user->account;
    $copyrightqzs = $this->dao->select('*')->from(TABLE_COPYRIGHTQZ)
        ->where('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($copyrightqzs as $key=>$copyrightqz)
    {
        if(strpos(",$copyrightqz->dealUser,", ",{$account},") === false)
        {
            unset($copyrightqzs[$key]);
            continue;
        }
    }
    return $copyrightqzs;
}

/**
 * Project: chengfangjinke
 * Desc: 获取自主知识产权信息
 * liuyuhan
 */
public function getUserCopyrightList($orderBy)
{
    $account = $this->app->user->account;
    $copyrights = $this->dao->select('*')->from(TABLE_COPYRIGHT)
        ->where('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($copyrights as $key=>$copyright)
    {
        if(strpos(",$copyright->dealUser,", ",{$account},") === false)
        {
            unset($copyrights[$key]);
            continue;
        }
        $softwareInfo = json_decode($copyright->softwareInfo);
        $copyright->fullname = implode(',', array_column($softwareInfo,'fullname'));
        $copyright->shortName = implode(',', array_column($softwareInfo,'shortName'));
        $copyright->version = implode(',', array_column($softwareInfo,'version'));
    }
    return $copyrights;
}
/**
 * Project: chengfangjinke
 * Method: getUserFixList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserFixList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserFixList($orderBy)
{
   $infos = $this->dao->select('*')->from(TABLE_INFO)
       ->where('action')->eq('fix')
       ->andWhere('status')->ne('deleted')
       ->andWhere('issubmit')->eq('submit')
       //->andWhere('reviewStage')->gt(0)
       ->orderBy($orderBy)
       ->fetchAll('id');

    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    foreach($infos as $key => $modify)
    {
        $infos[$key]->reviewers = $this->review->getReviewer('info', $modify->id, $modify->version, $modify->reviewStage);
        if(strpos(",$modify->reviewers,", ",{$account},") === false)
        {
            unset($infos[$key]);
            continue;
        }
        $accountList[$modify->createdBy] = $modify->createdBy;
    }

    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
    foreach($infos as $key => $modify)
    {
        $infos[$key]->createdDept = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->dept : '';
        $infos[$key]->realname    = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->realname : '';
    }
    return $infos;
}

/**
 * Project: chengfangjinke
 * Method: getUserGainList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserGainList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserGainList($orderBy)
{
   $gains = $this->dao->select('*')->from(TABLE_INFO)
       ->where('action')->eq('gain')
       ->andWhere('status')->ne('deleted')
       ->andWhere('issubmit')->eq('submit')
       //->andWhere('reviewStage')->gt(0)
       ->orderBy($orderBy)
       ->fetchAll('id');

    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    $this->loadModel('info');
    foreach($gains as $key => $info)
    {
        $gains[$key]->reviewers = $this->loadModel('review')->getReviewer('info', $info->id, $info->version, $info->reviewStage);
        $dealUsers = $this->loadModel('info')->getInfoDealUsers($info);
        $info->dealUsers = $dealUsers;
        if(strpos(",$info->dealUsers,", ",{$account},") === false)
        {
            unset($gains[$key]);
            continue;
        }
        $accountList[$info->createdBy] = $info->createdBy;
    }

    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
    foreach($gains as $key => $modify)
    {
        $gains[$key]->createdDept = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->dept : '';
        $gains[$key]->realname    = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->realname : '';
    }
    return $gains;
}

/**
 * Project: chengfangjinke
 * Method: getUserGainqzList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserGainList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserGainqzList($orderBy)
{
    $gains = $this->dao->select('*')->from(TABLE_INFO_QZ)
        ->where('action')->eq('gain')
        ->andWhere('status')->ne('deleted')
        ->andWhere('issubmit')->eq('submit')
        //->andWhere('reviewStage')->gt(0)
        ->orderBy($orderBy)
        ->fetchAll('id');

    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    $this->loadModel('infoqz');
    foreach($gains as $key => $info) {
        if(in_array($info->status, $this->lang->infoqz->allowReject))
        {
            $info->reviewers = $this->lang->infoqz->apiDealUserList['userAccount'];
        } else {
            $info->reviewers = $this->review->getReviewer('infoQz', $info->id, $info->version, $info->reviewStage);
        }

        $dealUsers = $this->loadModel('infoqz')->getInfoDealUsers($info);

        $info->dealUsers = $dealUsers;
        if(strpos(",$info->dealUsers,", ",{$account},") === false) {
            unset($gains[$key]);
            continue;
        }
        $accountList[$info->createdBy] = $info->createdBy;
    }

    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
    foreach($gains as $key => $info)
    {
        $gains[$key]->createdDept = isset($dmap[$info->createdBy]) ? $dmap[$info->createdBy]->dept : '';
        $gains[$key]->realname    = isset($dmap[$info->createdBy]) ? $dmap[$info->createdBy]->realname : '';
    }
    return $gains;
}

/**
 * Project: chengfangjinke
 * Method: getUserReviewList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserReviewList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
/*public function getUserReviewList($orderBy)
{

    $list = array_merge($this->lang->review->allowAssignStatusList,$this->lang->review->allowReviewStatusList);
    $account = $this->app->user->account;
    //status ！= list  dealuser
    //其他 用reviewer
    $this->loadModel('review');
    $reviews = $this->dao->select('*')->from(TABLE_REVIEW)
        ->where('deleted')->eq(0)
        ->orderBy($orderBy)
        ->fetchAll('id');
   $array = array();
    foreach($reviews as $key => $reviewInfo) {
        $status = $reviewInfo->status;
        $reviews[$key]->statusDesc = $this->loadModel('review')->getReviewStatusDesc($status, $reviewInfo->rejectStage);

        if((in_array($reviewInfo->status, $list) ) ){
            $reviewers = $this->review->getReviewer('review', $reviewInfo->id, $reviewInfo->version, $reviewInfo->reviewStage);
            $reviews[$key]->reviewers = $reviewers;
            $reviews[$key]->dealUser = $reviewers;
            $reviewersArray = [];
            if($reviewers){
                $reviewersArray = explode(',',$reviewers);
            }
            if(in_array($account, $reviewersArray)){
                $project = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in($reviewInfo->project)->fetch();
                $reviews[$key]->mark = isset($project->mark) ? $project->mark : '';
                $array[] = $reviews[$key];
            }

        }elseif((!in_array($reviewInfo->status, $list)) ){
            $reviews[$key]->dealUser  = $reviewInfo->dealUser;
            $reviews[$key]->reviewers = $reviewInfo->dealUser;
            if(in_array($account,explode(',',$reviewInfo->dealUser))){
                $project = $this->dao->select('project,mark')->from(TABLE_PROJECTPLAN)->where('project')->in($reviewInfo->project)->fetch();
                $reviews[$key]->mark = isset($project->mark) ? $project->mark : '';
                $array[] = $reviews[$key];
            }

        }
    }
    return $array;
}*/

/**
 * Project: chengfangjinke
 * Method: getUserChangeList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserChangeList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserChangeList($orderBy)
{
    $data = [];
    $account = $this->app->user->account;
    //格式化排序
    $orderByArray    = explode('_', $orderBy);
    $orderByArray[0] = 'zc.'.$orderByArray[0];
    $orderBy = implode('_', $orderByArray);
    $statusArray = ['deleted', 'recall'];
    $changes = $this->dao->select('zc.*, GROUP_CONCAT(zn.id) as nodeIds')->from(TABLE_CHANGE)->alias('zc')
        ->leftJoin(TABLE_REVIEWNODE)->alias('zn')->on('zn.objectType = "change" and zn.objectID = zc.id and zn.version = zc.version')
        ->leftJoin(TABLE_REVIEWER)->alias('zer')->on('zer.node = zn.id')
        ->where('zc.status')->notin($statusArray)
        ->andWhere('zn.status')->eq('pending')
        ->andWhere('zer.status')->eq('pending')
        ->andWhere('zer.reviewer')->eq($account)
        ->groupBy('zc.id')
        ->orderBy($orderBy)
        ->fetchAll();
    if(!$changes){
        return $data;
    }
    $createUsers = [];
    $this->loadModel('review');

    $allNodeIds = [];
    foreach ($changes as $val){
        $nodeIds = $val->nodeIds ? explode(',', $val->nodeIds): [];
        $val->nodeIds = $nodeIds;
        if($nodeIds){
            $allNodeIds = array_merge($allNodeIds, $nodeIds);
        }
    }
    $exWhere = 'status = "pending"';
    $reviewerList =  $this->review->getReviewersByNodeIds($allNodeIds, $exWhere);
    foreach($changes as $key => $change) {
        $status = $change->status;
        $changes[$key]->reviewers    = '';
        $changes[$key]->appiontUsers = '';
        if($status == 'recall' || $status == 'waitcommit'|| $status == 'reject'){ //20222708 修复退回问题
            $changes[$key]->reviewers   = $change->createdBy;
        }elseif(in_array($status, $this->lang->change->statusMapMoreNodeList) ){ //一个状态对应两个或者多个节点
            $reviewer =  $this->review->getMuiltNodeReviewer('change', $change->id, $change->version);
            $changes[$key]->reviewers = implode(',',$reviewer['reviews']);
            $changes[$key]->appiontUsers   = implode(',',$reviewer['appointUsers']);
        }else{
            $nodeIds = $change->nodeIds;
            if($nodeIds){
                foreach ($nodeIds as $key1 => $nodeId){
                    $reviewer = zget($reviewerList, $nodeId, []);
                    if (isset($reviewer['reviews']) && !empty($reviewer['reviews'])) {
                        if($key1 == 0){
                            $changes[$key]->reviewers = implode(',', $reviewer['reviews']);
                        }else{
                            $changes[$key]->reviewers .= ','. implode(',', $reviewer['reviews']);
                        }
                    }
                    if (isset($reviewer['appointUsers']) && !empty($reviewer['appointUsers'])) {
                        if($key1 == 0){
                            $changes[$key]->appiontUsers = implode(',', $reviewer['appointUsers']);
                        }else{
                            $changes[$key]->appiontUsers .= ','. implode(',', $reviewer['appointUsers']);
                        }
                    }

                }

            }
        }
        $createUsers[] = $change->createdBy;
    }

    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($createUsers)->fetchAll('account');
    foreach($changes as $key => $modify)
    {
        $changes[$key]->createdDept = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->dept : '';
        $changes[$key]->realname    = isset($dmap[$modify->createdBy]) ? $dmap[$modify->createdBy]->realname : '';
    }
    return $changes;
}

/**
 * Project: chengfangjinke
 * Method: getUserProjectplanList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserProjectplanList($orderBy)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
        ->where('status')->in('yearwait,yearreviewing,wait,reviewing,yearreject,yearstart')
        ->andWhere('deleted')->eq('0')
        ->andWhere('bearDept')->notin($shanghaiDeptList)
        ->orderBy($orderBy)
        ->fetchAll('id');
    /* 处理外部关联项目字段的值。*/
    $outsideproject = $this->loadModel('outsideplan')->getPairs();
    foreach($plans as $plan)
    {
        $outsideProjectList = explode(',', str_replace(' ', '', $plan->outsideProject));
        $plan->outsides = '';
        $outsideTitle = array();
        foreach($outsideProjectList as $outsideID)
        {
            if(empty($outsideID)) continue;
            $outsideTitle[] = zget($outsideproject, $outsideID, $outsideID);
        }
        if(!empty($outsideTitle)) $plan->outsides = implode(',', $outsideTitle);
    }

    $this->loadModel('review');
    $account   = $this->app->user->account;

    $planyearreviewing = [];
    $planreviewing = [];
    $planowner = [];
    foreach($plans as $planID => $plan)
    {
        if(in_array($plan->status, array('yearwait', 'yearreviewing')))
        {
            $plan->reviewers = $this->review->getReviewer('projectplanyear', $planID, $plan->yearVersion, $plan->reviewStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }else{
                $planyearreviewing[$planID] = $plan;
            }
        }
        elseif(in_array($plan->status, array('wait', 'reviewing')))
        {
//            $plan->reviewStage
            $plan->reviewers = $this->review->getMuiltNodeReviewers('projectplan', $planID, $plan->version, []);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }else{
                $planreviewing[$planID] = $plan;
            }
        }
        elseif(in_array($plan->status, array('yearreject','yearstart')))
        {
            $plan->reviewers = $plan->owner;
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }else{
                $planowner[$planID] = $plan;
            }
        }
    }

    $result = ['planyearreviewing'=>$planyearreviewing,'planreviewing'=>$planreviewing,'planowner'=>$planowner];
    return $result;
}


/**
 * Project: chengfangjinke
 * Method: getUserProjectplanShList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanShList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserProjectplanShList($orderBy)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
        ->where('status')->in('yearwait,yearreviewing,wait,reviewing,yearreject,yearstart')
        ->andWhere('deleted')->eq('0')
        ->andWhere('bearDept')->in($shanghaiDeptList)
        ->orderBy($orderBy)
        ->fetchAll('id');
    /* 处理外部关联项目字段的值。*/
    $outsideproject = $this->loadModel('outsideplan')->getPairs();
    foreach($plans as $plan)
    {
        $outsideProjectList = explode(',', str_replace(' ', '', $plan->outsideProject));
        $plan->outsides = '';
        $outsideTitle = array();
        foreach($outsideProjectList as $outsideID)
        {
            if(empty($outsideID)) continue;
            $outsideTitle[] = zget($outsideproject, $outsideID, $outsideID);
        }
        if(!empty($outsideTitle)) $plan->outsides = implode(',', $outsideTitle);
    }

    $this->loadModel('review');
    $account   = $this->app->user->account;

    $planyearreviewing = [];
    $planreviewing = [];
    $planowner = [];
    foreach($plans as $planID => $plan)
    {
        if(in_array($plan->status, array('yearwait', 'yearreviewing')))
        {
            $plan->reviewers = $this->review->getReviewer('projectplanyear', $planID, $plan->yearVersion, $plan->reviewStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }else{
                $planyearreviewing[$planID] = $plan;
            }
        }
        elseif(in_array($plan->status, array('wait', 'reviewing')))
        {
//            $plan->reviewStage
            $plan->reviewers = $this->review->getMuiltNodeReviewers('projectplan', $planID, $plan->version, []);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }else{
                $planreviewing[$planID] = $plan;
            }
        }
        elseif(in_array($plan->status, array('yearreject','yearstart')))
        {
            $plan->reviewers = $plan->owner;
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }else{
                $planowner[$planID] = $plan;
            }
        }
    }

    $result = ['planyearreviewing'=>$planyearreviewing,'planreviewing'=>$planreviewing,'planowner'=>$planowner];
    return $result;
}

/**
 * 清总缺陷
 */
public function getUserDefectList($orderBy,$browseType)
{
    $defects = $this->dao->select('*')->from(TABLE_DEFECT)
        ->where('deleted')->ne('1')
        ->andWhere('dealUser')->eq($this->app->user->account)
        ->beginIF($browseType != 'all')->andWhere('status')->eq($browseType)->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');
    return $defects;
}

/**
 * Project: chengfangjinke
 * Method: getUserProjectplanList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserProjectplanChangeList($orderBy ,$shangHai = null)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
        ->where('changeStatus')->eq('pending')
        ->beginIF($shangHai)->andWhere('bearDept')->in($shangHai)->fi()
        ->beginIF(empty($shangHai))->andWhere('bearDept')->notin($shanghaiDeptList)->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');

    /* 处理外部关联项目字段的值。*/
    $outsideproject = $this->loadModel('outsideplan')->getPairs();
    foreach($plans as $plan)
    {
        $outsideProjectList = explode(',', str_replace(' ', '', $plan->outsideProject));
        $plan->outsides = '';
        $outsideTitle = array();
        foreach($outsideProjectList as $outsideID)
        {
            if(empty($outsideID)) continue;
            $outsideTitle[] = zget($outsideproject, $outsideID, $outsideID);
        }
        if(!empty($outsideTitle)) $plan->outsides = implode(',', $outsideTitle);
    }

    $this->loadModel('review');
    $account   = $this->app->user->account;
    foreach($plans as $planID => $plan)
    {
        if(in_array($plan->status, array('yearpass','start')))
        {
            $plan->reviewers = $this->review->getReviewer('planchange', $planID, $plan->changeVersion, $plan->changeStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }
    }
    return $plans;
}

/**
 * TongYanQi 2022/11/18
 * 待立项列表
 */
public function getUserProjectplanStartList($orderBy,$shangHai = null)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->dao->select('*')->from(TABLE_PROJECTPLAN)
        ->where('status')->in('start,yearpass')
        ->andwhere('deleted')->eq('0')
        ->andwhere('changeStatus')->ne('pending')
        ->andwhere('owner')->like("%".$this->app->user->account."%")
        ->beginIF($shangHai)->andWhere('bearDept')->in($shangHai)->fi()
        ->beginIF(empty($shangHai))->andWhere('bearDept')->notin($shanghaiDeptList)->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');

    $outsideproject = $this->loadModel('outsideplan')->getPairs();
    $this->loadModel('review');
    $creations = $this->dao->select('plan,id')->from(TABLE_PROJECTCREATION)->where('plan')->in(array_keys($plans))->fetchPairs();
    foreach($plans as $planID => $plan)
    {
        $outsideProjectList = explode(',', str_replace(' ', '', $plan->outsideProject));
        $plan->outsides = '';
        $outsideTitle = array();
        foreach($outsideProjectList as $outsideID)
        {
            if(empty($outsideID)) continue;
            $outsideTitle[] = zget($outsideproject, $outsideID, $outsideID);
        }
        if(!empty($outsideTitle)) $plan->outsides = implode(',', $outsideTitle);
        $plan->creationID = isset($creations[$planID]) ? $creations[$planID] : 0;

            if(in_array($plan->status,array("yearreject","yearstart","yearpass","start")) && $plan->changeStatus !== 'pending'){
                $plan->reviewers = $plan->owner;
            }
    }

    return $plans;
}
/**
 * Project: chengfangjinke
 * Method: getUserRequirementList
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserRequirementList.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return mixed
 */
public function getUserRequirementListOld($orderBy)
{
    $account     = $this->app->user->account;
    $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)
        ->where('status')->ne('deleted')
        ->andWhere('version != changeVersion')
        ->orderBy($orderBy)
        ->fetchAll('id');
    $this->loadModel('review');
    $accountList = array();
    foreach($requirements as $id => $requirement)
    {
        $reviewer = $this->review->getReviewer('requirement', $requirement->id, $requirement->changeVersion);
        $requirement->reviewer = $reviewer ? ',' . $reviewer . ',' : '';
        if(strpos(",$requirement->reviewer,", ",{$account},") === false)
        {
            unset($requirements[$id]);
            continue;
        }
        $accountList[$requirement->owner] = $requirement->owner;
    }
    // User dept list.
    $dmap = $this->dao->select('account,realname,dept')->from(TABLE_USER)->where('account')->in($accountList)->fetchAll('account');
    foreach($requirements as $key => $requirement)
    {
        $requirements[$key]->realname = isset($dmap[$requirement->owner]) ? $dmap[$requirement->owner]->realname : '';
    }

    return $requirements;
}

public function getUserRequirementList($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '(( 1    AND  (FIND_IN_SET("'.$account.'",dealUser) AND `status` NOT IN ("delivered","onlined","closed")) OR FIND_IN_SET("'.$account.'",feedbackDealUser)  OR FIND_IN_SET("'.$account.'",changeDealUser)))';
//    $assigntomeQuery = '(( 1    AND  FIND_IN_SET("'.$this->app->user->account.'",dealUser) AND (`status` NOT IN ("delivered","onlined","closed") OR `feedbackStatus` = "tofeedback"))  OR ( 1   AND FIND_IN_SET("'.$this->app->user->account.'",feedbackDealUser)) OR ( 1   AND FIND_IN_SET("'.$this->app->user->account.'",changeDealUser)))';


    //$statusQuery = '(1 AND ((`feedbackStatus` != "feedbacksuccess" OR `status` != "onlined")  AND (`status` != "delivered"  OR `feedbackStatus` != "feedbacksuccess") AND ( `status` != "delivered"  OR `feedbackStatus` != "toexternalapproved") AND ( `status` != "onlined"  OR `feedbackStatus` != "toexternalapproved")))';

    $statusQuery = '(1 AND 
        (( ((`feedbackStatus` != "feedbacksuccess" OR `status` != "onlined")  AND ( `status` != "delivered"  OR `feedbackStatus` != "feedbacksuccess") AND ( `status` != "delivered"  OR`feedbackStatus` != "toexternalapproved") AND ( `status` != "onlined"  OR `feedbackStatus` != "toexternalapproved"))) OR (`createdBy` = "guestcn" and FIND_IN_SET("'.$this->app->user->account.'",changeDealUser)))
        )';
    $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)
        ->where($assigntomeQuery)
        ->andWhere('sourceRequirement')->eq(1)
        ->andWhere('status')->notIN('deleted,closed,deleteout')
        ->andWhere($statusQuery)
        ->andWhere('ignoreStatus')->eq(0)
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($requirements as $id => $requirement){
        if(strstr($requirement->ignoredBy,$account) !== false && $requirement->ignoreStatus == 1)
        {
            unset($requirements[$id]);
            continue;
        }
        $dealUserArray = explode("," , $requirement->dealUser);
        $feedbackDealUserArray = explode("," , $requirement->feedbackDealUser);
        $dealUserArray = array_merge($dealUserArray, $feedbackDealUserArray);
        $dealUserArray = array_unique($dealUserArray);
        $requirement->reviewer = implode(",", $dealUserArray);
        if(in_array($requirement->status,['delivered','onlined']))
        {
//            $requirement->reviewer = implode(",", $feedbackDealUserArray);
            if($requirement->createdBy == 'guestcn'){
                $requirement->reviewer = implode(",", $feedbackDealUserArray);
            }else{
                $requirement->reviewer = '';
                if(empty($requirement->changeDealUser))
                {
                    unset($requirements[$requirement->id]);
                }
            }
        }
    }
    return $requirements;
}

/**
 * @Notes:内部需求任务
 * @Date: 2023/5/18
 * @Time: 18:42
 * @Interface getUserRequirementInsideList
 * @param $orderBy
 * @return mixed
 */
public function getUserRequirementInsideList($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '(( 1    AND  FIND_IN_SET("'.$account.'",dealUser)) OR ( 1   AND FIND_IN_SET("'.$account.'",feedbackDealUser)))';
    $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)
        ->where($assigntomeQuery)
        ->andWhere('status')->ne('deleted')
        ->andWhere('sourceRequirement')->eq(2)
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($requirements as $id => $requirement){
        if(strstr($requirement->ignoredBy, $this->app->user->account) !== false)
        {
            unset($requirements[$id]);
            continue;
        }
        $dealUserArray = explode("," , $requirement->dealUser);
        $feedbackDealUserArray = explode("," , $requirement->feedbackDealUser);
        $dealUserArray = array_merge($dealUserArray, $feedbackDealUserArray);
        $dealUserArray = array_unique($dealUserArray);
        $requirement->reviewer = implode(",", $dealUserArray);
        if(in_array($requirement->status,['delivered','onlined']))
        {
//            $requirement->reviewer = implode(",", $feedbackDealUserArray);
            if($requirement->createdBy == 'guestcn'){
                $requirement->reviewer = implode(",", $feedbackDealUserArray);
            }else{
                unset($requirements[$requirement->id]);
            }
        }
    }
    return $requirements;
}

public function getUserComponentList($orderBy)
{
    $account     = $this->app->user->account;
    //$assigntomeQuery = '(( 1    AND dealUser  LIKE "%'.$account.'%" ) OR ( 1   AND feedbackDealUser  LIKE "%'.$account.'%" ))';
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealUser))';
    $components = $this->dao->select('*')->from(TABLE_COMPONENT)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    //每个数据添加架构部处理人，方便按钮高亮
    //架构部指定人员（架构部处理、架构部确认）节点人员
    $productManagerReviewer = array_keys($this->lang->component->productManagerReviewer);
    //平台结构部领导
    $productManagerReviewerManager = $this->dao->select('id,manager1')->from(TABLE_DEPT)->where('name')->eq('平台架构部')->fetch();
    $pmrm = explode(',', trim($productManagerReviewerManager->manager1, ','));
    $pmrm = array_merge($productManagerReviewer, $pmrm);
    foreach ($components as $component){
        $component->pmrm = $pmrm;
    }
    return $components;
}

public function getUserSectransferList($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",approver))';
    $sectransfers = $this->dao->select('*')->from(TABLE_SECTRANSFER)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    return $sectransfers;
}

public function getUserOsspchangeList($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealuser))';
    $osspchanges = $this->dao->select('*')->from(TABLE_OSSPCHANGE)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    return $osspchanges;
}


public function getUserDatamanagementList($orderBy)
{
    $account     = $this->app->user->account;
    //数据获取描述字段与mysql-desc关键字重名，需要转义
    if(!empty($orderBy)){
        $orderByList = explode("_", $orderBy);
        $orderByList[0] = "`".$orderByList[0]."`";
        $orderByNew = implode("_", $orderByList);
    }
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealUser))';
    $datas = $this->dao->select('*')->from(TABLE_DATAUSE)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderByNew)
        ->fetchAll('id');

    $toreadDatamanagement = $this->dao->select('t1.*')->from(TABLE_DATAUSE)->alias('t1')
        ->innerJoin(TABLE_TOREAD)->alias('t2')->on('t1.id=t2.objectId')
        ->where(1)
        ->andWhere('t1.deleted')->ne('1')->andWhere('t2.objectType')->eq('datamanagement')
        ->andWhere('t2.status')->eq('toread')->andWhere('t2.dealUser')->eq($this->app->user->account)->andWhere('t2.deleted')->ne(1)->orderBy('t1.'.$orderByNew)->fetchAll('id');
    $datas = array_merge($datas, $toreadDatamanagement);
    $datas = $this->array_repeat($datas, 'id');
    foreach ($datas as $data){
        if($data->isDeadline == '1'){
            $data->useDeadline = '';
        }
    }
    if(empty($orderBy)){
        $columnid = 'id';
        $sort = SORT_DESC;
    }else{
        $orderByList = explode("_", $orderBy);
        $columnid = $orderByList[0];
        if($orderByList[1] == 'asc'){
            $sort = SORT_ASC;
        }else{
            $sort = SORT_DESC;
        }
    }
    $ids = array_column($datas, $columnid);
    array_multisort($ids, $sort, $datas);
    foreach ($datas as $data){
        //增加消息通知
        $toreadList = $this->dao->select('*')->from(TABLE_TOREAD)->where('objectType')->eq('datamanagement')->andWhere('objectId')->eq($data->id)->andWhere('status')->eq('toread')
            ->andWhere('dealUser')->eq($this->app->user->account)->andWhere('deleted')->ne(1)->fetchAll('id');
        $toreadNum = 0;
        $reviewedBoolean = false;
        $gainedBoolean = false;
        $destroyedBoolean = false;
        foreach ($toreadList as $toread){
            if($toread->messageType == 'reviewed'){
                if(!$reviewedBoolean){
                    $reviewedBoolean = true;
                    $toreadNum = $toreadNum + 1;
                }
                $data->toreadreviewed = $toread;
            }else if($toread->messageType == 'gained'){
                if(!$gainedBoolean){
                    $gainedBoolean = true;
                    $toreadNum = $toreadNum + 1;
                }
                $data->toreadgained = $toread;
            }else if($toread->messageType == 'destroyed'){
                if(!$destroyedBoolean){
                    $destroyedBoolean = true;
                    $toreadNum = $toreadNum + 1;
                }
                $data->toreaddestroyed = $toread;
            }
        }
        $data->toreadNum = $toreadNum;
    }
    return $datas;
}

/*
 * 获取用户要处理的问题。
 */
public function getUserProblemList($orderBy = 'id_desc')
{
    //2022.5.18 tangfei 增加问题反馈单的待处理人为当前用户的问题单
    $users   = $this->loadModel('user')->getPairs('noletter|noclosed');
    $account = $this->app->user->account;
    $field = 't2.originalResolutionDate,t2.delayResolutionDate,t2.delayReason,t2.delayStatus,t2.delayVersion,t2.delayStage,t2.delayDealUser,t2.delayUser,t2.delayDate,  t3.changeOriginalResolutionDate,t3.changeResolutionDate,t3.changeReason,t3.changeStatus,t3.changeVersion,t3.changeStage,t3.changeDealUser,t3.changeUser,t3.changeDate';
    if(strpos($orderBy, 'ifRecive') !== false){
        $orderBy = str_replace('ifRecive','ifReturn',$orderBy);
    }
    $problems = $this->dao
        ->select('t1.*,' . $field)
        ->from(TABLE_PROBLEM)->alias('t1')
        ->leftJoin(TABLE_DELAY)->alias('t2')
        //->on("t1.id = t2.objectId and t2.objectType = 'problem' and t2.isEnd = '1'")
        ->on("t1.id = t2.objectId and t2.objectType = 'problem' ")
        ->leftJoin(TABLE_PROBLEM_CHANGE)->alias('t3')
        ->on("t1.id = t3.objectId and t3.objectType = 'problem' ")
        ->where('t1.status')->ne('deleted')
        ->andWhere("((t1.dealUser = '{$account}' and t1.status not in ('feedbacked','build','released','delivery','onlinesuccess','closed')) or FIND_IN_SET('{$account}',t1.feedbackToHandle) or FIND_IN_SET('{$account}',t2.delayDealUser) or FIND_IN_SET('{$account}',t3.changeDealUser))")
        ->andWhere( " ((t1.status in('confirmed','assigned','toclose','returned') or (t1.status = 'feedbacked' and t1.type = 'noproblem') or t2.delayStatus in ('toDepart','toManager') or t3.changeStatus in ('toDepart','toManager','toProductManager')) or (t1.ReviewStatus  is not null and t1.status != 'closed'))")
        ->orderBy($orderBy)
        ->fetchAll('id');

//    $problems = $this->dao->select('*')->from(TABLE_PROBLEM)
//        ->where('status')->ne('deleted')
//        ->andWhere( "((status in('confirmed','assigned','toclose') or (status = 'feedbacked' and type = 'noproblem')) or (ReviewStatus  is not null and status != 'closed'))")
//        ->orderBy($orderBy)
//        ->fetchAll('id');
    $problems2 = array();
    $userDepts = $this->dao->select('account,dept')->from(TABLE_USER)->fetchAll('account');
    foreach($problems as $key => $problem)
    {
        if(
            ($account === $problem->dealUser) or
            in_array($account, explode(',', $problem->feedbackToHandle)) or
            in_array($account, explode(',', $problem->delayDealUser))or
            in_array($account, explode(',', $problem->changeDealUser))
        )
        {
            $problems[$key]->createdDept = $userDepts[$problem->createdBy]->dept;
            $approver = array();
            if($problem->feedbackToHandle != null){
                $countArray = explode(',', $problem->feedbackToHandle);
                foreach ($countArray as $account2) {
                    $approver[$account2] = $account2;
                }
            }
            $problem->approver   = $approver;

            $handleruser = array();
            $dealUserNname = "";
            //开发中 测试中 已发布 已交付 上线成功 处理人置空显示 已关闭也不显示待处理人
            if(in_array($problem->status, ['feedbacked','build','released','delivery','onlinesuccess','closed'])){
                $problems[$key]->dealUser  = '';
            }
            if($problem->dealUser != null){
                array_push($handleruser,$problem->dealUser);
                $dealUserNname = $users[$problem->dealUser];
            }
            if($problem->feedbackToHandle != null){
                $myArray = explode(',', $problem->feedbackToHandle);
                foreach ($myArray as $account3) {
                    if(!in_array($account3, $handleruser)){
                        array_push($handleruser,$account3);
                        $dealUserNname .= ",";
                        $dealUserNname .= $users[$account3];
                    }
                }
            }
            if(!empty($problem->delayDealUser)){
                $dealUserNname = explode(',', $dealUserNname);
                $delayDealUser = explode(',', zmget($users, $problem->delayDealUser));
                $dealUserNname = implode(',', array_unique(array_merge($dealUserNname, $delayDealUser)));
            }else{
                $dealUserNname = explode(',', $dealUserNname);
                $dealUserNname = implode(',', array_unique($dealUserNname));
            }
            if(!empty($problem->changeDealUser)){
                $dealUserNname = explode(',', $dealUserNname);
                $changeDealUser = explode(',', zmget($users, $problem->changeDealUser));
                $dealUserNname = implode(',', array_unique(array_merge($dealUserNname, $changeDealUser)));
            }

            $problem->dealUsers = $dealUserNname;

            $res = $this->loadModel('problem')->checkAllowReview($problem, $problem->version, $problem->reviewStage, $this->app->user->account);
            $problem->feedBackFlag = $res['result'];

            array_push($problems2,$problem);
        }
    }
    return $problems2;
}

/**
 * @Notes:内部自建投产/变更
 * @Date: 2024/4/26
 * @Time: 10:41
 * @Interface getUserPreproductionList
 * @param string $orderBy
 * @return mixed
 */
public function getUserPreproductionList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $preproductionInfo = $this->dao->select('*')->from(TABLE_PRODUCTIONCHANGE)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetchAll('id');
    return $preproductionInfo;
}

/*
 * 获取用户要处理的二线工单。
 */
public function getUserSecondorderList($orderBy = 'id_desc')
{
    $status = ['toconfirmed', 'assigned', 'tosolve', 'todelivered', 'returned', 'backed'];
    $account = $this->app->user->account;
    $secondorders = $this->dao->select('*')->from(TABLE_SECONDORDER)
        ->where('deleted')->ne('1')
//        ->andWhere('status')->ne('closed')
        ->andWhere('status')->in($status)
        //->andWhere('dealUser')->eq($account)
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetchAll('id');
    return $secondorders;
}

/*
 * 获取用户要处理的部门工单。
 */
public function getUserDeptorderList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $deptorders = $this->dao->select('*')->from(TABLE_DEPTORDER)
        ->where('deleted')->ne('1')
        ->andWhere('status')->ne('closed')
        ->andWhere('dealUser')->eq($account)
        ->orderBy($orderBy)
        ->fetchAll('id');
    return $deptorders;
}

// 获取项目结项列表
public function getUserClosingItemList($orderBy)
{
    $account     = $this->app->user->account;
    $dealuserQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealuser))';
    $ret = $this->dao->select('*')->from(TABLE_CLOSINGITEM)
        ->where($dealuserQuery)
        ->andWhere('deleted')->eq('0')
        ->orderBy($orderBy)
        ->fetchAll();
    return $ret;
}

// 获取项目结项意见列表
public function getUserClosingAdviseList($orderBy)
{
    $account     = $this->app->user->account;
    $dealuserQuery = '( 1    AND  FIND_IN_SET("'.$account.'",t1.dealuser))';
    $ret = $this->dao->select('t1.*,t2.status as itemStatus')->from(TABLE_CLOSINGADVISE)->alias("t1")
        ->leftJoin(TABLE_CLOSINGITEM)->alias('t2')->on('t1.itemId = t2.id')
        ->where($dealuserQuery)
        ->andWhere('t1.deleted')->eq('0')
        ->orderBy($orderBy)
        ->fetchAll();
    return $ret;
}

/*
 * 获取用户要处理的需求。 只查询已录入和已挂起
 */
public function getUserDemandList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $dealUserQuery = "((dealUser = '".$account."' and status in ('wait')) or FIND_IN_SET('".$account."',delayDealUser))";
    $demands = $this->dao->select('*')->from(TABLE_DEMAND)
        /*->where('dealUser')->eq($account)
        ->andWhere('status')->in('wait,suspend')*/
        ->where($dealUserQuery)
        ->andWhere('sourceDemand')->eq(1)
        ->andWhere('status')->notIN('closed,suspend,deleteout')
        ->orderBy($orderBy)
        ->fetchAll();
    foreach ($demands as $key => $demand) {
        //已发布 已交付 上线成功 处理人置空显示 也不显示待处理人
        if(in_array($demand->status, ['feedbacked','changeabnormal','chanereturn','delivery','onlinesuccess'])){
            $demands[$key]->dealUser  = '';
        }

    }
    return $demands;
}

/*
 * 获取用户要处理的需求。 只查询已录入和已挂起 内部需求条目
 */
public function getUserDemandInsideList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $demands = $this->dao->select('*')->from(TABLE_DEMAND)
        ->where('dealUser')->eq($account)
        ->andWhere('status')->in('wait,suspend,feedbacked')
        ->andWhere('sourceDemand')->eq(2)
        ->orderBy($orderBy)
        ->fetchAll();
    return $demands;
}

/*
 * 获取用户要处理的需求意向。
 */
public function getUserOpinionList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $opinions = $this->dao->select('*')->from(TABLE_OPINION)
        ->where('status')->ne('deleted')
        ->andWhere('sourceOpinion')->eq(1)
        ->andWhere('status')->notin('delivery,online,deleteout,closed')
        ->andWhere('dealUser',$markLeft = true)->eq($account)
        ->orWhere('changeDealUser')->like("%{$account}%")
        ->markRight(1)
        ->orderBy($orderBy)
        ->fetchAll();
    return $opinions;
}

/*
 * 内部需求意向
 */
public function getUserOpinionInsideList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $opinions = $this->dao->select('*')->from(TABLE_OPINION)
        ->where('status')->ne('deleted')
        ->andWhere('status')->notin('delivery,online')
        ->andWhere('sourceOpinion')->eq(2)
        ->andWhere('dealUser')->eq($account)
        ->orderBy($orderBy)
        ->fetchAll();
    return $opinions;
}

/*
 * 获取驻场支持列表
 */
public function getUserResidentSupportList($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $residentsupports = $this->dao->select('t1.*, t2.name, t2.`type`, t2.subType, t2.startDate, t2.endDate,t2.enable, group_concat(distinct t3.dutyUser) as dutyUsers, group_concat(distinct t4.dutyGroupLeader) as dutyGroupLeaders')
        ->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)->alias("t1")
        ->leftJoin(TABLE_RESIDENT_SUPPORT_TEMPLATE)->alias('t2')->on('t1.templateId = t2.id')
        ->leftJoin(TABLE_RESIDENT_SUPPORT_DAY_DETAIL)->alias('t3')->on("t1.templateId = t3.templateId and t1.deptId = t3.dutyUserDept and t3.deleted = '0' and t3.dutyUser != ''")
        ->leftJoin(TABLE_RESIDENT_SUPPORT_DAY)->alias('t4')->on("t4.templateId = t1.templateId and t4.id = t3.dayId and t4.deleted = '0' and t4.dutyGroupLeader != ''")
        ->where('t1.deleted')->eq('0')
        ->andWhere("concat(',',dealUsers,',')")->like("%,$account,%")
        ->groupBy('t1.id')
        ->orderBy($orderBy)
        ->fetchAll();

    if(!$residentsupports){
        return $residentsupports;
    }
    $userList = $this->loadModel('user')->getListFiled('account,dept,realname');
    $userList = array_column($userList, null, 'account');
    $deptList = $this->loadModel('dept')->getAllDeptList('id,name');
    $deptNameList = array_column($deptList, 'name', 'id');

    foreach ($residentsupports as $val){
        $dutyGroupLeaderList = [];
        if($val->dutyGroupLeaders){
            $dutyGroupLeaders = explode(',', $val->dutyGroupLeaders);
            foreach ($dutyGroupLeaders as $dutyGroupLeader){
                $userInfo = zget($userList, $dutyGroupLeader);
                $deptId   = zget($userInfo, 'dept');
                $realName = zget($userInfo, 'realname');
                $deptName = zget($deptNameList, $deptId);
                $dutyGroupLeaderInfo = new stdClass();
                $dutyGroupLeaderInfo->dutyGroupLeader = $dutyGroupLeader;
                $dutyGroupLeaderInfo->realname = $realName;
                $dutyGroupLeaderInfo->deptId   = $deptId;
                $dutyGroupLeaderInfo->deptName = $deptName;
                $dutyGroupLeaderList[] = $dutyGroupLeaderInfo;
            }
        }
        $val->dutyGroupLeaderList = $dutyGroupLeaderList;
    }
    return $residentsupports;
}

/**
 * 获得现场支持（新）列表
 *
 * @param $account
 * @param string $orderBy
 * @return array
 */
public function getUserLocaleSupportList($account, $orderBy = 'id_desc'){
    $data = [];
    if(!$account){
        $account = $this->app->user->account;
    }
    $this->app->loadLang('localesupport');
    $allowReportWorkStatusArray = "'".implode("','", $this->lang->localesupport->allowReportWorkStatusArray). "'";
    $ret = $this->dao->select('*')
        ->from(TABLE_LOCALESUPPORT)
        ->where('deleted')->eq('0')
        ->andWhere("(FIND_IN_SET('{$account}', dealUsers) OR (status in (".$allowReportWorkStatusArray.") and  FIND_IN_SET('{$account}', supportUsers)))")
        ->orderBy($orderBy)
        ->fetchAll('id');
    if($ret){
        $ids = array_column($ret, 'id');
        $consumedList = $this->loadModel('localesupport')->getConsumedList($ids);
        foreach ($ret as $val){
            $supportId = $val->id;
            if(isset($consumedList[$supportId])){
                $val->consumedTotal = $consumedList[$supportId];
            }else{
                $val->consumedTotal = 0;
            }
        }
        $data = $ret;
    }
    return $data;
}

/**
 * 获得质量门禁（新）列表
 *
 * @param $account
 * @param string $orderBy
 * @return array
 */
public function getUserQualityGateList($account, $orderBy = 'id_desc'){
    $data = [];
    if(!$account){
        $account = $this->app->user->account;
    }

    $this->app->loadLang('qualitygate');
    $allowDealStatusArr = "'".implode("','", $this->lang->qualitygate->allowDealStatusArr). "'";
    $ret = $this->dao->select('zq.*, zp.name as projectName, zpd.name as productName, zb.name as buildName, zb.status as buildStatus, zpp.title as productVersionTitle')
        ->from(TABLE_QUALITYGATE)->alias('zq')
        ->leftJoin(TABLE_PROJECT)->alias('zp')->on('zq.projectId = zp.id')
        ->leftJoin(TABLE_PRODUCT)->alias('zpd')->on('zq.productId = zpd.id')
        ->leftJoin(TABLE_BUILD)->alias('zb')->on('zq.buildId = zb.id')
        ->leftJoin(TABLE_PRODUCTPLAN)->alias('zpp')->on('zq.productVersion = zpp.id')
        ->where('zq.deleted')->eq('0')
        ->andWhere("'{$account}' = zq.dealUser and zq.status in (".$allowDealStatusArr.")")
        ->orderBy($orderBy)
        ->fetchAll('id');
    $data = $ret;
    return $data;
}

//环境部署工单列表
public function getUserEnvironmentOrder($account, $orderBy = 'priority_desc,id_desc'){
    $data = $this->dao->select('*')->from(TABLE_ENVIRONMENTORDER)
        ->where('deleteTime is null')
        ->andWhere("(FIND_IN_SET('{$account}', dealUser))")
        ->orderBy($orderBy)
        ->fetchAll();


    return $data;
}
//环权限申请列表
public function getUserAuthorityapply($account, $orderBy = 'id_desc'){
    $data = [];
    if(!$account){
        $account = $this->app->user->account;
    }
    $data = $this->dao->select('*')->from(TABLE_AUTHORITYAPPLY)
        ->where('deleteTime is null')
        ->andWhere("(FIND_IN_SET('{$account}', dealUser))")
        ->orderBy($orderBy)
        ->fetchAll();

    return $data;
}
/**
 * 获得金信-投产移交数量
 *
 * @param $orderBy
 * @return int
 */
public function getUserPutproductionCount($orderBy){
    $total = 0;
    $account = $this->app->user->account;
    $ret = $this->dao->select('count(id) as total')
        ->from(TABLE_PUTPRODUCTION)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetch();
    if($ret){
        $total = $ret->total;
    }
    return $total;
}

/**
 * 征信交付待办数量
 *
 * @param $orderBy
 * @return int
 */
public function getUserCreditCount($orderBy){
    $total = 0;
    $account = $this->app->user->account;
    $ret = $this->dao->select('count(id) as total')
        ->from(TABLE_CREDIT)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUsers)")
        ->orderBy($orderBy)
        ->fetch();
    if($ret){
        $total = $ret->total;
    }
    return $total;
}

/**
 * Project: chengfangjinke
 * Method: getUserModifyCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserModifyCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserModifyCount($orderBy)
{
    $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('modify', $this->app->user->account);
    $dealUserList = explode(',', $dealUserList);
    $condition = '';
    if(!empty($dealUserList)){
        $this->loadModel('modify');
        foreach ($dealUserList as $dealUser){
            if(strpos($condition, 'FIND_IN_SET') !== false){
                if($this->app->user->account == $dealUser){
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }else{
                if($this->app->user->account == $dealUser){
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->modify->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }
        }
    }
    $modifys = $this->dao->select('id,version,reviewStage,dealUser,status')->from(TABLE_MODIFY)
        ->where('status')->ne('deleted')
        //->andWhere('issubmit')->eq('submit')
        ->beginIF(!empty($condition))->andWhere($condition)->fi()
        //->andWhere('reviewStage')->gt(0)
        ->orderBy($orderBy)
        ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    foreach($modifys as $key => $modify)
    {
        // $modifys[$key]->reviewers = $this->review->getReviewer('modify', $modify->id, $modify->version, $modify->reviewStage);
        $modify->dealUser = $this->loadModel('common')->getAuthorizer('modify', $modify->dealUser,$modify->status, $this->lang->modify->authorizeStatusList);
        if(strpos(",$modify->dealUser,", ",{$account},") === false)
        {
            unset($modifys[$key]);
            continue;
        }
    }

    return count($modifys);
}

/**
 * Project: chengfangjinke
 * Method: getUserModifycnccCount
 * User: shixuyang
 * Date: 2022/6/6
 * @param $orderBy
 * @return int
 */
public function getUserModifycnccCount($orderBy)
{
    $modifycnccs = $this->dao->select('id,version,reviewStage')->from(TABLE_MODIFYCNCC)
        ->where('status')->ne('deleted')
        //->andWhere('reviewStage')->gt(0)
        ->orderBy($orderBy)
        ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    foreach($modifycnccs as $key => $modifycncc)
    {
        $modifycnccs[$key]->reviewers = $this->review->getReviewer('modifycncc', $modifycncc->id, $modifycncc->version, $modifycncc->reviewStage);
        if(strpos(",$modifycncc->reviewers,", ",{$account},") === false)
        {
            unset($modifycnccs[$key]);
            continue;
        }
    }

    return count($modifycnccs);
}

public function getUserCopyrightqzCount($orderBy)
{
    $account = $this->app->user->account;
    $copyrightqzs = $this->dao->select('id,dealUser')->from(TABLE_COPYRIGHTQZ)
        ->where('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($copyrightqzs as $key=>$copyrightqz)
    {
        if(strpos(",$copyrightqz->dealUser,", ",{$account},") === false)
        {
            unset($copyrightqzs[$key]);
            continue;
        }
    }
    return count($copyrightqzs);
}

/**
 * Project: chengfangjinke
 * Desc:获取自主知识产权总数
 * liuyuhan
 */
public function getUserCopyrightCount($orderBy)
{
    $account = $this->app->user->account;
    $copyrights = $this->dao->select('id,dealUser')->from(TABLE_COPYRIGHT)
        ->where('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($copyrights as $key=>$copyright)
    {
        if(strpos(",$copyright->dealUser,", ",{$account},") === false)
        {
            unset($copyrights[$key]);
            continue;
        }
    }
    return count($copyrights);
}

/**
 * Project: chengfangjinke
 * Method: getUserFixCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserFixCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserFixCount($orderBy)
{
   $infos = $this->dao->select('id,version,reviewStage')->from(TABLE_INFO)
       ->where('action')->eq('fix')
       ->andWhere('status')->ne('deleted')
       //->andWhere('reviewStage')->gt(0)
       ->orderBy($orderBy)
       ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    foreach($infos as $key => $modify)
    {
        $infos[$key]->reviewers = $this->review->getReviewer('info', $modify->id, $modify->version, $modify->reviewStage);
        if(strpos(",$modify->reviewers,", ",{$account},") === false)
        {
            unset($infos[$key]);
            continue;
        }
    }

    return count($infos);
}

/**
 * Project: chengfangjinke
 * Method: getUserGainCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserGainCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserGainCount($orderBy)
{

   $gains = $this->dao->select('id,version,reviewStage, status, createdBy')->from(TABLE_INFO)
       ->where('action')->eq('gain')
       ->andWhere('status')->ne('deleted')
       ->andWhere('issubmit')->eq('submit')
       //->andWhere('reviewStage')->gt(0)
       ->orderBy($orderBy)
       ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    $this->loadModel('info');

    foreach($gains as $key => $info)
    {
        $info->reviewers = $this->loadModel('review')->getReviewer('info', $info->id, $info->version, $info->reviewStage);
        $dealUsers = $this->loadModel('info')->getInfoDealUsers($info);
        $info->dealUsers = $dealUsers;
        if(strpos(",$info->dealUsers,", ",{$account},") === false)
        {
            unset($gains[$key]);
            continue;
        }
    }


    return count($gains);
}

/**
 * Project: chengfangjinke
 * Method: getUserGainQzCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserGainQzCount.
 * remarks: 获得用户清总交付-数据获取
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserGainQzCount($orderBy)
{
    $gains = $this->dao->select('id,version,reviewStage, status, createdBy')->from(TABLE_INFO_QZ)
        ->where('action')->eq('gain')
        ->andWhere('status')->ne('deleted')
        ->andWhere('issubmit')->eq('submit')
        ->orderBy($orderBy)
        ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    $this->loadModel('infoqz');
    foreach($gains as $key => $info)
    {
        if(in_array($info->status, $this->lang->infoqz->allowReject))
        {
            $info->reviewers = $this->lang->infoqz->apiDealUserList['userAccount'];
        } else {
            $info->reviewers = $this->review->getReviewer('infoQz', $info->id, $info->version, $info->reviewStage);
        }

        $dealUsers = $this->loadModel('infoqz')->getInfoDealUsers($info);

        $info->dealUsers = $dealUsers;
        if(strpos(",$info->dealUsers,", ",{$account},") === false) {
            unset($gains[$key]);
            continue;
        }
        $accountList[$info->createdBy] = $info->createdBy;

    }
    return count($gains);
}

/**
 * 我的评审
 * @return mixed
 */
public function reviewAndMeetingCount(){
    $review  = $this->getUserReviewCount();
    $meeting = $this->getUserReviewMeetingCount();
    $total = $review + $meeting;
    return $total;
}

/**
 * 统计在线评审 - 我的评审
 * @param $orderBy
 * @return int
 */
public function getUserReviewCount()
{
    $this->app->loadLang('review');
    $account = $this->app->user->account;
    $array   = array();

    // 去掉的评审状态 会议评审中 待确定会议结论
    $reviews = $this->dao->select('*')->from(TABLE_REVIEW)
        ->where('deleted')->eq(0)
        ->andWhere('status')->ne('waitMeetingReview')
        ->andWhere('status')->ne('waitMeetingOwnerReview')
        ->fetchAll('id');
    foreach($reviews as $key => $reviewInfo) {
            $reviews[$key]->dealuser = $reviewInfo->dealUser;
            if(in_array($account,explode(',',$reviewInfo->dealUser))){
                $array[] = $reviews[$key];
            }
    }
    return count($array) ? count($array) : 0;
}

/**
 * 统计会议评审 - 我的评审
 *
 * @return int
 */
public function getUserReviewMeetingCount()
{
    $account = $this->app->user->account;
    $reviews = $this->dao->select('t1.*')->from(TABLE_REVIEW_MEETING)->alias('t1')
        ->leftJoin(TABLE_REVIEW)->alias('t2')
        ->on('t1.meetingCode = t2.meetingCode')
        ->where('t1.deleted')->eq(0)
        ->andWhere('t2.deleted')->eq(0)
        ->andWhere("concat(',',t1.dealUser,',')")->like("%,$account,%")
        ->andWhere('t1.status')->ne('waitFormalReview')
        ->groupBy('t1.id')
        ->fetchAll();

    return count($reviews) ? count($reviews) : 0;
}

/**
 * Project: chengfangjinke
 * Method: getUserChangeCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserChangeCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $account
 * @param $orderBy
 * @return int
 */

public function getUserChangeCount($account, $orderBy = '')
{
    $total = 0;
    $account = $this->app->user->account;
    $statusArray = ['deleted', 'recall'];
    $ret = $this->dao->select('count(zc.id) as total')->from(TABLE_CHANGE)->alias('zc')
        ->leftJoin(TABLE_REVIEWNODE)->alias('zn')->on('zn.objectType = "change" and zn.objectID = zc.id and zn.version = zc.version')
        ->leftJoin(TABLE_REVIEWER)->alias('zer')->on('zer.node = zn.id')
        ->where('zc.status')->notin($statusArray)
        ->andWhere('zn.status')->eq('pending')
        ->andWhere('zer.status')->eq('pending')
        ->andWhere('zer.reviewer')->eq($account)
        ->fetch();
    if($ret){
        $total = $ret->total;
    }
    return $total;
}

/**
 * Project: chengfangjinke
 * Method: getUserProjectplanCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserProjectplanCount($orderBy)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->dao->select('id,status,yearVersion,version,reviewStage,owner')->from(TABLE_PROJECTPLAN)
        ->where('status')->in('yearwait,yearreviewing,wait,reviewing,yearstart,yearreject')
        ->andWhere('deleted')->eq('0')
        ->andWhere('bearDept')->notin($shanghaiDeptList)
        ->orderBy($orderBy)
        ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    foreach($plans as $planID => $plan)
    {
        if(in_array($plan->status, array('yearwait', 'yearreviewing')))
        {
            $plan->reviewers = $this->review->getReviewer('projectplanyear', $planID, $plan->yearVersion, $plan->reviewStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }
        elseif(in_array($plan->status, array('wait', 'reviewing')))
        {
            $plan->reviewers = $this->review->getReviewer('projectplan', $planID, $plan->version, $plan->reviewStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }elseif(in_array($plan->status, array('yearstart', 'yearreject')))
        {
            $plan->reviewers = $plan->owner;
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }
    }

    return count($plans);
}

/**
 * Project: chengfangjinke
 * Method: getUserProjectplanShCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanShCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserProjectplanShCount($orderBy)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->dao->select('id,status,yearVersion,version,reviewStage,owner')->from(TABLE_PROJECTPLAN)
        ->where('status')->in('yearwait,yearreviewing,wait,reviewing,yearstart,yearreject')
        ->andWhere('deleted')->eq('0')
        ->andWhere('bearDept')->in($shanghaiDeptList)
        ->orderBy($orderBy)
        ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    foreach($plans as $planID => $plan)
    {
        if(in_array($plan->status, array('yearwait', 'yearreviewing')))
        {
            $plan->reviewers = $this->review->getReviewer('projectplanyear', $planID, $plan->yearVersion, $plan->reviewStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }
        elseif(in_array($plan->status, array('wait', 'reviewing')))
        {
            $plan->reviewers = $this->review->getReviewer('projectplan', $planID, $plan->version, $plan->reviewStage);
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }elseif(in_array($plan->status, array('yearstart', 'yearreject')))
        {
            $plan->reviewers = $plan->owner;
            if(strpos(",$plan->reviewers,", ",{$account},") === false)
            {
                unset($plans[$planID]);
                continue;
            }
        }
    }

    return count($plans);
}
/**
 * Project: chengfangjinke
 * Method: getUserProjectplanCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserDefectCount($orderBy)
{
    $account = $this->app->user->account;
    $defect = $this->dao->select('*')->from(TABLE_DEFECT)
        ->where('dealUser')->eq($account)
        ->orderBy($orderBy)
        ->fetchAll('id');
    return count($defect);
}

/**
 * Project: chengfangjinke
 * Method: getUserProjectplanCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserProjectplanChangeCount($orderBy)
{
    $plans = $this->getUserProjectplanChangeList($orderBy);
    return count($plans);
}

/**
 * Project: chengfangjinke
 * Method: getUserProjectplanShChangeCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:47
 * Desc: This is the code comment. This method is called getUserProjectplanShChangeCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserProjectplanShChangeCount($orderBy)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->getUserProjectplanChangeList($orderBy,$shanghaiDeptList);
    return count($plans);
}

/**
 * TongYanQi 2022/11/18
 * 我的待立项数
 */
public function getUserProjectplanStartCount($orderBy)
{
    $plans = $this->getUserProjectplanStartList($orderBy); //count
    return count($plans);
}

/**
 * TongYanQi 2022/11/18
 * 我的待立项数
 */
public function getUserProjectplanShStartCount($orderBy)
{
    $shanghaiDeptList = $this->loadModel('dept')->getAllChildId(30); //获取所有上海部门
    $plans = $this->getUserProjectplanStartList($orderBy,$shanghaiDeptList); //count
    return count($plans);
}
/**
 * Project: chengfangjinke
 * Method: getUserRequirementCount
 * User: Tony Stark
 * Year: 2021
 * Date: 2021/10/8
 * Time: 14:48
 * Desc: This is the code comment. This method is called getUserRequirementCount.
 * remarks: The sooner you start to code, the longer the program will take.
 * Product: PhpStorm
 * @param $orderBy
 * @return int
 */
public function getUserRequirementCountOld($orderBy)
{
    $requirements = $this->dao->select('id,changeVersion')->from(TABLE_REQUIREMENT)
        ->andWhere('status')->notin('deleted,delivered,onlined')
        ->andWhere('version != changeVersion')
        ->orderBy($orderBy)
        ->fetchAll('id');

    $account = $this->app->user->account;
    $this->loadModel('review');
    foreach($requirements as $id => $requirement)
    {
        $reviewer = $this->review->getReviewer('requirement', $requirement->id, $requirement->changeVersion);
        $requirement->reviewer = $reviewer ? ',' . $reviewer . ',' : '';

        if(strpos(",$requirement->reviewer,", ",{$account},") === false)
        {
            unset($requirements[$id]);
            continue;
        }
    }

    return count($requirements);
}

/*
 * 获取用户要处理的问题总数。
 */
public function getUserProblemCount($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $problems = $this->dao
        ->select('count(t1.id) as problems')
        ->from(TABLE_PROBLEM)->alias('t1')
        ->leftJoin(TABLE_DELAY)->alias('t2')
        //->on("t1.id = t2.objectId and t2.objectType = 'problem' and t2.isEnd = '1'")
        ->on("t1.id = t2.objectId and t2.objectType = 'problem' ")
        ->leftJoin(TABLE_PROBLEM_CHANGE)->alias('t3')
        ->on("t1.id = t3.objectId and t3.objectType = 'problem' ")
        ->where('t1.status')->ne('deleted')
        ->andWhere("((t1.dealUser = '{$account}' and t1.status not in ('feedbacked','build','released','delivery','onlinesuccess','closed')) or FIND_IN_SET('{$account}',t1.feedbackToHandle) or FIND_IN_SET('{$account}',t2.delayDealUser) or FIND_IN_SET('{$account}',t3.changeDealUser))")
        ->andWhere( " ((t1.status in('confirmed','assigned','toclose','returned') or (t1.status = 'feedbacked' and t1.type = 'noproblem') or t2.delayStatus in ('toDepart','toManager') or t3.changeStatus in ('toDepart','toManager','toProductManager')) or (t1.ReviewStatus  is not null and t1.status != 'closed'))")
        ->fetch('problems');

    return empty($problems) ? 0 : $problems;
    //2022.5.18 tangfei 增加问题反馈单待处理人
    /*$account = $this->app->user->account;
    $problems = $this->dao->select('id,dealUser,feedbackToHandle')->from(TABLE_PROBLEM)
        ->where('status')->ne('deleted')
       // ->andWhere('status')->ne('closed')
        ->andWhere( " ((status in('confirmed','assigned','toclose') or (status = 'feedbacked' and type = 'noproblem')) or (ReviewStatus  is not null and status != 'closed'))")
        ->orderBy($orderBy)
        ->fetchAll('id');
    $count = 0;
    foreach ($problems as $problem)
    {
        if(($account == $problem->dealUser) or (in_array($account, explode(',',$problem->feedbackToHandle)) == true))
        {
            $count++;
        }
    }
    return $count;*/

   /* $problems = $this->dao->select('count(*) as problems')->from(TABLE_PROBLEM)
        ->where('status')->ne('deleted')
        ->andWhere('status')->ne('closed')
        ->andWhere('dealUser')->eq($account)
        ->fetch('problems');*/
   // return empty($problems) ? 0 : $problems;
}

/**
 * @Notes:内部自建投产/变更
 * @Date: 2024/4/26
 * @Time: 10:57
 * @Interface getUserPreproductionCount
 * @param string $orderBy
 * @return int|mixed
 */
public function getUserPreproductionCount($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $preproductionInfo = $this->dao->select('count(id) as preproductions')->from(TABLE_PRODUCTIONCHANGE)
        ->where('deleted')->eq('0')
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetch('preproductions');

    return empty($preproductionInfo) ? 0 : $preproductionInfo;
}

/*
 * 获取用户要处理的二线工单总数。
 */
public function getUserSecondorderCount($orderBy = 'id_desc')
{
    //2022.5.18 tangfei 增加问题反馈单待处理人
    $account = $this->app->user->account;
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_SECONDORDER)
        ->where('deleted')->ne('1')
        ->andWhere('status')->ne('closed')
        //->andWhere('dealUser')->eq($account)
        ->andWhere("FIND_IN_SET('{$account}', dealUser)")
        ->orderBy($orderBy)
        ->fetch('counts');

    return $counts;

}

/**
 *获得驻场支持待办数量2022.10.22 wangjiurong
 *
 * @param string $orderBy
 * @return mixed
 */
public function getUserResidentSupportCount($orderBy = 'id_desc'){
    $account = $this->app->user->account;
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_RESIDENT_SUPPORT_TEMPLATE_DEPT)
        ->where('deleted')->eq('0')
        ->andWhere("concat(',',dealUsers,',')")->like("%,$account,%")
        ->orderBy($orderBy)
        ->fetch('counts');
    return $counts;
}

/**
 * 获得现场支持新待办数量
 *
 * @param $account
 * @param string $orderBy
 * @return mixed
 */
public function getUserLocaleSupportCount($account, $orderBy = 'id_desc'){
    if(!$account){
        $account = $this->app->user->account;
    }
    $this->app->loadLang('localesupport');
    $allowReportWorkStatusArray = "'".implode("','", $this->lang->localesupport->allowReportWorkStatusArray). "'";
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_LOCALESUPPORT)
        ->where('deleted')->eq('0')
        ->andWhere("(FIND_IN_SET('{$account}', dealUsers) OR (status in (".$allowReportWorkStatusArray.") and  FIND_IN_SET('{$account}', supportUsers)))")
        ->orderBy($orderBy)
        ->fetch('counts');
    return $counts;
}
/**
 * 获得质量门禁新待办数量
 *
 * @param $account
 * @param string $orderBy
 * @return mixed
 */
public function getUserQualityGateCount($account, $orderBy = 'id_desc'){
    if(!$account){
        $account = $this->app->user->account;
    }
    $this->app->loadLang('qualitygate');
    $allowDealStatusArr = "'".implode("','", $this->lang->qualitygate->allowDealStatusArr). "'";
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_QUALITYGATE)
        ->where('deleted')->eq('0')
        ->andWhere("'{$account}' = dealUser and status in (".$allowDealStatusArr.")")
        ->orderBy($orderBy)
        ->fetch('counts');
    return $counts;
}
/*
 * 获取用户要处理的需求总数。 只查询已录入和已挂起
 */
public function getUserDemandCount()
{
    $account = $this->app->user->account;
    $dealUserQuery = "((dealUser = '".$account."' and status in ('wait')) or FIND_IN_SET('".$account."',delayDealUser))";
    $demands = $this->dao->select('count(*) as demands')->from(TABLE_DEMAND)
       /* ->where('dealUser')->eq($account)
        ->andWhere('status')->in('wait,suspend')*/
        ->where($dealUserQuery)
        ->andWhere('sourceDemand')->eq(1)
        ->andWhere('ignoreStatus')->eq(0)
        ->andWhere('status')->notIN('closed,suspend,deleteout')
        ->fetch('demands');
    return empty($demands) ? 0 : $demands;
}

/*
 * 获取用户要处理的需求总数。 只查询已录入和已挂起
 */
public function getUserDemandInsideCount()
{
    $account = $this->app->user->account;
    $demands = $this->dao->select('count(*) as demands')->from(TABLE_DEMAND)
        ->where('dealUser')->eq($account)
        ->andWhere('status')->in('wait,suspend,feedbacked')
        ->andWhere('sourceDemand')->eq(2)
        ->andWhere('ignoreStatus')->eq(0)
        ->fetch('demands');
    return empty($demands) ? 0 : $demands;
}

/*
 * 获取用户要处理的需求意向总数。
 */
public function getUserOpinionCount()
{
    $account = $this->app->user->account;
    $opinions = $this->dao->select('id,dealUser,`ignore`')->from(TABLE_OPINION)
        ->where('status')->ne('deleted')
        ->andWhere('sourceOpinion')->eq(1)
        ->andWhere('status')->notin('delivery,online,deleteout,closed')
        ->andWhere('dealUser',$markLeft = true)->eq($account)
        ->orWhere('changeDealUser')->like("%{$account}%")
        ->markRight(1)
        ->fetchAll();
    $count = count($opinions);
    foreach ($opinions as $index=>$opinion)
    {
        if(in_array($account, explode(',',$opinion->ignore)) == true)
        {
            $count--;
        }
    }
    return $count;
}

/*
 * 获取用户要处理的内部-需求意向总数。
 */
public function getUserOpinionInsideCount()
{
    $account = $this->app->user->account;
    $opinions = $this->dao->select('id,dealUser,`ignore`')->from(TABLE_OPINION)
        ->where('status')->ne('deleted')
        ->andWhere('status')->notin('delivery,online')
        ->andWhere('sourceOpinion')->eq(2)
        ->andWhere('dealUser')->eq($account)
        ->fetchAll();
    $count = count($opinions);
    foreach ($opinions as $index=>$opinion)
    {
        if(in_array($account, explode(',',$opinion->ignore)) == true)
        {
            $count--;
        }
    }
    return $count;
}


/**
 * Desc:获取用户要处理的需求任务总数
 * Date: 2022/5/23
 * Time: 14:40
 *
 * @param $orderBy
 * @return int|mixed
 *
 */
public function getUserRequirementCount($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '(( 1    AND  (FIND_IN_SET("'.$account.'",dealUser) AND `status` NOT IN ("delivered","onlined","closed")) OR FIND_IN_SET("'.$account.'",feedbackDealUser)  OR FIND_IN_SET("'.$account.'",changeDealUser)))';
    //$statusQuery = '(1 AND ((`feedbackStatus` != "feedbacksuccess" OR `status` != "onlined")  AND (`status` != "delivered"  OR `feedbackStatus` != "feedbacksuccess") AND ( `status` != "delivered"  OR `feedbackStatus` != "toexternalapproved") AND ( `status` != "onlined"  OR `feedbackStatus` != "toexternalapproved")))';
    $statusQuery = '(1 AND 
        (( ((`feedbackStatus` != "feedbacksuccess" OR `status` != "onlined")  AND ( `status` != "delivered"  OR `feedbackStatus` != "feedbacksuccess") AND ( `status` != "delivered"  OR`feedbackStatus` != "toexternalapproved") AND ( `status` != "onlined"  OR `feedbackStatus` != "toexternalapproved"))) OR (`createdBy` = "guestcn" and FIND_IN_SET("'.$this->app->user->account.'",changeDealUser)))
        )';
    $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)
        ->where($assigntomeQuery)
        ->andWhere('sourceRequirement')->eq(1)
        ->andWhere('status')->notIN('deleted,closed,deleteout')
        ->andWhere($statusQuery)
        ->andWhere('ignoreStatus')->eq(0)
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($requirements as $id => $requirement){
        if(strstr($requirement->ignoredBy, $account) !== false && $requirement->ignoreStatus == 1)
        {
            unset($requirements[$id]);
            continue;
        }
        $dealUserArray = explode("," , $requirement->dealUser);
        $feedbackDealUserArray = explode("," , $requirement->feedbackDealUser);
        $dealUserArray = array_merge($dealUserArray, $feedbackDealUserArray);
        $dealUserArray = array_unique($dealUserArray);
        $requirement->reviewer = implode(",", $dealUserArray);
        if(in_array($requirement->status,['delivered','onlined']))
        {
            $requirement->reviewer = implode(",", $feedbackDealUserArray);
            if($requirement->createdBy == 'guestcn'){
//                $requirement->reviewer = implode(",", $feedbackDealUserArray);
            }else{
                $requirement->reviewer = '';
                if(empty($requirement->changeDealUser))
                {
                    unset($requirements[$requirement->id]);
                }
            }
        }
    }
    return empty($requirements) ? 0 : count($requirements);
}

/**
 * @Notes: 内部需求任务总数
 * @Date: 2023/5/18
 * @Time: 18:43
 * @Interface getUserRequirementInsideCount
 * @param $orderBy
 * @return int
 */
public function getUserRequirementInsideCount($orderBy)
{
    $account     = $this->app->user->account;
    //$assigntomeQuery = '(( 1    AND dealUser  LIKE "%'.$account.'%" ) OR ( 1   AND feedbackDealUser  LIKE "%'.$account.'%" ))';
    $assigntomeQuery = '(( 1    AND  FIND_IN_SET("'.$account.'",dealUser)) OR ( 1   AND FIND_IN_SET("'.$account.'",feedbackDealUser)))';
    $requirements = $this->dao->select('*')->from(TABLE_REQUIREMENT)
        ->where($assigntomeQuery)
        ->andWhere('status')->ne('deleted')
        ->andWhere('sourceRequirement')->eq(2)
        ->orderBy($orderBy)
        ->fetchAll('id');
    foreach ($requirements as $id => $requirement){
        if(strstr($requirement->ignoredBy, $this->app->user->account) !== false)
        {
            unset($requirements[$id]);
            continue;
        }
        $dealUserArray = explode("," , $requirement->dealUser);
        $feedbackDealUserArray = explode("," , $requirement->feedbackDealUser);
        $dealUserArray = array_merge($dealUserArray, $feedbackDealUserArray);
        $dealUserArray = array_unique($dealUserArray);
        $requirement->reviewer = implode(",", $dealUserArray);
        if(in_array($requirement->status,['delivered','onlined']))
        {
//            $requirement->reviewer = implode(",", $feedbackDealUserArray);
            if($requirement->createdBy == 'guestcn'){
                $requirement->reviewer = implode(",", $feedbackDealUserArray);
            }else{
                unset($requirements[$requirement->id]);
            }
        }
    }
    return empty($requirements) ? 0 : count($requirements);
}

/**
 * Desc:获取用户要处理的组件申请总数
 * User: shixuyang
 *
 * @param $orderBy
 * @return int|mixed
 *
 */
public function getUserComponentCount($orderBy)
{
    $account     = $this->app->user->account;
    //$assigntomeQuery = '(( 1    AND dealUser  LIKE "%'.$account.'%" ) OR ( 1   AND feedbackDealUser  LIKE "%'.$account.'%" ))';
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealUser))';
    $components = $this->dao->select('*')->from(TABLE_COMPONENT)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    return empty($components) ? 0 : count($components);
}

public function getUserSectransferCount($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",approver))';
    $sectransfers = $this->dao->select('*')->from(TABLE_SECTRANSFER)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    return empty($sectransfers) ? 0 : count($sectransfers);
}

public function getUserCmdbsyncCount($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealUser))';
    $cmdbsyncs = $this->dao->select('*')->from(TABLE_CMDBSYNC)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    return empty($cmdbsyncs) ? 0 : count($cmdbsyncs);
}

public function getUserOsspchangeCount($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealuser))';
    $osspchanges = $this->dao->select('*')->from(TABLE_OSSPCHANGE)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');
    return empty($osspchanges) ? 0 : count($osspchanges);
}


public function getUserDatamanagementCount($orderBy)
{
    $account     = $this->app->user->account;
    $assigntomeQuery = '( 1    AND  FIND_IN_SET("'.$account.'",dealUser))';
    $datas = $this->dao->select('*')->from(TABLE_DATAUSE)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->ne('1')
        ->orderBy($orderBy)
        ->fetchAll('id');

    $toreadDatamanagement = $this->dao->select('t1.*')->from(TABLE_DATAUSE)->alias('t1')
        ->innerJoin(TABLE_TOREAD)->alias('t2')->on('t1.id=t2.objectId')
        ->where(1)
        ->andWhere('t1.deleted')->ne('1')->andWhere('t2.objectType')->eq('datamanagement')
        ->andWhere('t2.status')->eq('toread')->andWhere('t2.dealUser')->eq($this->app->user->account)->andWhere('t2.deleted')->ne(1)->orderBy('t1.'.$orderBy)->fetchAll('id');
    $datas = array_merge($datas, $toreadDatamanagement);
    $datas = $this->array_repeat($datas, 'id');
    $ids = array_column($datas, 'id');
    array_multisort($ids, SORT_DESC, $datas);
    return empty($datas) ? 0 : count($datas);
}

/**
 * 我的地盘清总-对外交付列表
 * @param $orderBy
 * @return array
 */
public function getUserOutwardDeliveryList($orderBy)
{
    $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('outwarddelivery', $this->app->user->account);
    $dealUserList = explode(',', $dealUserList);
    $condition = '';
    if(!empty($dealUserList)){
        $this->loadModel('outwarddelivery');
        foreach ($dealUserList as $dealUser){
            if(strpos($condition, 'FIND_IN_SET') !== false){
                if($this->app->user->account == $dealUser){
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }else{
                if($this->app->user->account == $dealUser){
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }
        }
    }
    $list = $this->dao->select('*')->from(TABLE_OUTWARDDELIVERY)
        ->where('status')->ne('deleted')
        ->andWhere('issubmit')->eq('submit')
        ->beginIF(!empty($condition))->andWhere($condition)->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');
    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    $this->loadModel('outwarddelivery');
    $dataList = [];
    foreach($list as $key => $value)
    {
//        $value->reviewers = $this->review->getReviewer('outwardDelivery', $value->id, $value->version, $value->reviewStage);
//        print_r($value->reviewers);die();
        $value->dealUser = $this->loadModel('common')->getAuthorizer('outwarddelivery', $value->dealUser,$value->status, $this->lang->outwarddelivery->authorizeStatusList);
        $reviewersArray = explode(',', $value->dealUser);
        if( in_array($account,$reviewersArray) === false)
        {
            continue;
        }
        $dataList[] = $value;
        $accountList[$value->createdBy] = $value->createdBy;
    }

    return $dataList;
}

public function getUserOutwardDeliveryCount($orderBy)
{
    $dealUserList = $this->loadModel('common')->getOriginalAuthorizer('outwarddelivery', $this->app->user->account);
    $dealUserList = explode(',', $dealUserList);
    $condition = '';
    if(!empty($dealUserList)){
        $this->loadModel('outwarddelivery');
        foreach ($dealUserList as $dealUser){
            if(strpos($condition, 'FIND_IN_SET') !== false){
                if($this->app->user->account == $dealUser){
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' or (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }else{
                if($this->app->user->account == $dealUser){
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser))';
                }else{
                    $condition .= ' (FIND_IN_SET("'.$dealUser.'",dealUser) and status in(';
                    $i = 0;
                    foreach ($this->lang->outwarddelivery->authorizeStatusList as $key=>$value){
                        if($i == 0){
                            $condition .= "'".$key."'";
                        }else{
                            $condition .= ",'".$key."'";
                        }
                        $i++;
                    }
                    $condition .= '))';
                }
            }
        }
    }
    $list = $this->dao->select('id,version,reviewStage,dealUser,status')->from(TABLE_OUTWARDDELIVERY)
        ->where('status')->ne('deleted')
        ->andWhere('issubmit')->eq('submit')
        ->beginIF(!empty($condition))->andWhere($condition)->fi()
        ->orderBy($orderBy)
        ->fetchAll('id');
    $accountList = array();
    $account     = $this->app->user->account;
    $this->loadModel('review');
    $dataList = [];
    foreach($list as $key => $value)
    {
        $value->dealUser = $this->loadModel('common')->getAuthorizer('outwarddelivery', $value->dealUser,$value->status, $this->lang->outwarddelivery->authorizeStatusList);
        $reviewersArray = explode(',', $value->dealUser);
        if( in_array($account,$reviewersArray) === false)
        {
            continue;
        }
        $dataList[] = $value;
    }

    return count($dataList);
}

/***
 *	二维数组去重
 *	data_arr = 需要去重的数组
 *	keyworld = 数组中的key名
 *	例如根据uid去重，那就传uid
 */
function array_repeat($data_arr, $keyworld){
    $new_arr = array();
    foreach($data_arr as $k => $v){
        //如果匹配上的话，就将其数组删除
        if(in_array($v->$keyworld, $new_arr)){
            unset($data_arr[$k]);
        } else {
            //唯一性的数据就赋值到新数组中
            $new_arr[$k] = $v->$keyworld;
        }
    }
    return $data_arr;
}

/*
 * 获取用户要处理的测试版本总数。
 */
public function getUserBuildCount()
{
    $account = $this->app->user->account;
    /*
    $builds = $this->dao->select('t1.*, t2.name as executionName, t2.id as executionID, t3.name as productName, t4.name as branchName')
        ->from(TABLE_BUILD)->alias('t1')
        ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
        ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
        ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
        ->where('t1.deleted')->eq(0)
        ->andWhere('t1.project')->ne(0)
        ->andWhere('t1.dealuser')->eq($account)
       // ->andWhere('t1.status')->ne('waittest')
        ->orderBy('t1.id desc')
        ->fetchAll('id');
    $total  = isset($builds) ? count($builds) : 0;
    */

    $ret = $this->dao->select('count(id) as total')
        ->from(TABLE_BUILD)
        ->where('deleted')->eq('0')
        ->andWhere('project')->ne(0)
        ->andWhere(' FIND_IN_SET("'.$account.'",dealUser)')
        ->fetch();
    $total =  isset($ret->total) ? $ret->total : 0;
    return $total;
}

/*
 * 获取用户测试版本。
 */
public function getUserBuildList($orderBy = 'id_desc',$pager = null)
{

    $account = $this->app->user->account;
    /*
   $builds = $this->dao->select('t1.*, t2.name as executionName, t2.id as executionID, t3.name as productName, t4.name as branchName')
       ->from(TABLE_BUILD)->alias('t1')
       ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
       ->leftJoin(TABLE_PRODUCT)->alias('t3')->on('t1.product = t3.id')
       ->leftJoin(TABLE_BRANCH)->alias('t4')->on('t1.branch = t4.id')
       ->where('t1.deleted')->eq(0)
       ->andWhere('t1.project')->ne(0)
       ->andWhere('t1.dealuser')->eq($account)
       //->andWhere('t1.status')->ne('waittest')
       ->orderBy('t1.id desc')
       ->page($pager)
       ->fetchAll();
   foreach ($builds as $build){
       $build->versions =  array("" => "",'1'=>'无') + $this->loadModel('productplan')->getPairs($build->product);
   }
   return $builds;
   */
    $data = [];
    $ret =  $this->dao->select('*')
        ->from(TABLE_BUILD)
        ->where('deleted')->eq('0')
        ->andWhere('project')->ne(0)
        ->andWhere(' FIND_IN_SET("'.$account.'",dealUser)')
        ->orderBy('id desc')
        ->page($pager)
        ->fetchAll();
    if(!$ret){
        return $data;
    }
    $executionIds = array_column($ret, 'execution');
    $productIds   = array_column($ret, 'product');
    $branchIds    = array_column($ret, 'branch');
    //阶段
    $executionList = $this->loadModel('project')->getListByIds($executionIds, 'id, name as executionName');
    if($executionList){
        $executionList = array_column($executionList, null, 'id');
    }
    //产品
    $productList =  $this->loadModel('product')->getListByIds($productIds, 'id, name as productName');
    if($productList){
        $productList = array_column($productList, null, 'id');
    }
    //分支
    $branchIdList = $this->loadModel('branch')->getListByIds($branchIds, 'id, name as branchName');
    if($branchIdList){
        $branchIdList = array_column($branchIdList, null, 'id');
    }
    //版本
    $versionsList = $this->loadModel('productplan')->getPairsGroupProduct($productIds);

    foreach ($ret as $val){
        $executionId = $val->execution;
        $productId   = $val->product;
        $branchId    = $val->branch;
        $executionInfo = zget($executionList, $executionId, new stdClass());
        $productInfo   = zget($productList, $productId, new stdClass());
        $branchInfo    = zget($branchIdList, $branchId, new stdClass());
        $val->executionID   = zget($executionInfo, 'id', 0);
        $val->executionName = zget($executionInfo, 'executionName', '');
        $val->productName   = zget($productInfo, 'productName', '');
        $val->branchName    = zget($$branchInfo, 'branchName', '');
        $val->versions =  array("" => "",'1'=>'无') + zget($versionsList, $productId, []);
    }
    $data = $ret;
    return $data;
}

/**
 *获得项目发布的待办数量
 *
 * @param $orderBy
 * @return int
 */
public function getUserProjectReleaseCount($orderBy){
    $count = 0;
    $account = $this->app->user->account;
    $assigntomeQuery = '( 1 AND  FIND_IN_SET("'.$account.'",dealUser))';
    $ret = $this->dao->select('count(id) as count')->from(TABLE_RELEASE)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->eq('0')
        ->orderBy($orderBy)
        ->fetch();
    if($ret){
        $count = $ret->count;
    }
    return $count;
}

/**
 *获得项目发布待办列表
 *
 * @param string $orderBy
 * @return array
 */
public function getUserProjectReleaseList($orderBy = 't1.id_desc'){
    $data = [];
    $account = $this->app->user->account;
    $assigntomeQuery = '( 1 AND  FIND_IN_SET("'.$account.'",t1.dealUser))';
    $ret = $this->dao->select('t1.*, t2.name as productName,t2.code as productCode,t3.id as buildID, t3.name as buildName, t3.execution, t4.name as executionName')
        ->from(TABLE_RELEASE)->alias('t1')
        ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
        ->leftJoin(TABLE_BUILD)->alias('t3')->on('t1.build = t3.id')
        ->leftJoin(TABLE_EXECUTION)->alias('t4')->on('t3.execution = t4.id')
        ->where($assigntomeQuery)
        ->andWhere('t1.deleted')->eq('0')
        ->orderBy($orderBy)
        ->fetchAll();
    if($ret){
        $data = $ret;
    }
    $users = $this->loadModel('user')->getPairs('noclosed|noletter');
    foreach ($data as $release) {
        if ($release->dealUser != ''){
            $dealUsers = explode(',',$release->dealUser);
            $userArray = [];
            foreach ($dealUsers as $dealUser) {
                $userArray[] = $users[$dealUser];
            }
            $release->dealUserStr = implode(',',$userArray);
        }
    }
    return $data;
}

// 获取清总评审待处理数量
public function getUserReviewqzCount(){
    $count = 0;
    $account         = $this->app->user->account;
    $assigntomeQuery = '(FIND_IN_SET("'.$account.'",dealUser))';
    $ret = $this->dao->select('count(id) as count')
        ->from(TABLE_REVIEWQZ)
        ->where($assigntomeQuery)
        ->andWhere('deleted')->eq('0')
        ->fetch();
    if($ret){
        $count = $ret->count;
    }
    return $count;
}

/*
 * 获取用户要处理的部门工单总数。
 */
public function getUserDeptorderCount($orderBy = 'id_desc')
{
    $account = $this->app->user->account;
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_DEPTORDER)
        ->where('deleted')->ne('1')
        ->andWhere('status')->ne('closed')
        ->andWhere('dealUser')->eq($account)
        ->orderBy($orderBy)
        ->fetch('counts');

    return $counts;

}
//获取待处理的环境部署工单总数
public function getTodealEnvironmentOrderCount($account){
    if(!$account){
        $account = $this->app->user->account;
    }
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_ENVIRONMENTORDER)
        ->where('deleteTime is null')
        ->andWhere("(FIND_IN_SET('{$account}', dealUser))")
        ->fetch('counts');
    return $counts;
}
//获取待处理的权限申请总数
public function getTodealAuthorityapplyCount($orderBy = 'id_desc',$pager = null)
{
    $account = $this->app->user->account;
    $counts = $this->dao->select('count(*) as counts')->from(TABLE_AUTHORITYAPPLY)
        ->where('deleteTime is null')
        ->andWhere("(FIND_IN_SET('{$account}', dealUser))")
        ->orderBy('id desc')
        ->page($pager)
        ->fetch('counts');
    return $counts;
}

