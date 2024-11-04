<style>
    td p {
        margin-bottom: 0;
    }

    .table-fixed td {
        white-space: unset !important;
    }
    .side-col .cell, .main-col .cell {
        overflow-y: auto;
    }
    .modal-dialog {
        top: 10% !important;
    }
</style>
<?php
$mainContentHeight = 600;
?>
<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>

<?php
if($this->session->reviewmeetingList=='board'):?>
    <?php $browseLink = $this->session->reviewmanageList ? $this->session->reviewmanageList : inlink('board'); ?>
<?php else:?>
    <?php $browseLink = $this->session->reviewmeetingList ? $this->session->reviewmeetingList : inlink('meetingreview'); ?>
<?php endif;?>
<div id="mainMenu" class="clearfix">
    <div class="<?php if ($flag) echo 'btn-toolbar '; ?> pull-left" <?php if (!$flag) echo 'style= "margin-left:20px"'; ?>>
        <?php if ($flag): ?>
            <?php
            echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");
            ?>
        <?php endif; ?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $meetingInfo->id ?></span>
            <span class="text"><?php echo $meetingInfo->meetingCode; ?></span>
        </div>
    </div>
    <div class="<?php if ($flag) echo 'btn-toolbar '; ?>pull-right">
        <?php if ($flag): ?>
            <?php if (common::hasPriv('reviewmeeting', 'batchCreate')) echo html::a($this->createLink('reviewmeeting', 'batchCreate', "meetingId=$meetingInfo->id&source=reviewmeeting"), "<i class='icon-plus'></i> {$lang->review->addproblem }", '', "class='btn btn-primary'"); ?>
        <?php endif; ?>
    </div>
</div>
<div id="mainContent" class="main-row" ">
<div class="main-col col-8">
    <div class='cell'>
        <div class='detail'>
            <div class='detail-title'><?php echo $lang->reviewmeeting->meetingTopic; ?></div>
            <div class="detail-content article-content ">
                <table class="table ops  table-fixed ">
                    <?php if (!empty($reviewProjects)) : ?>
                    <thead>
                    <tr>
                        <?php echo zget($lang->reviewmeeting->typeList, $meetingInfo->type, '') . "_" . zget($users, $meetingInfo->owner) . "(" . "$reviewMeetingDetailCount" . ")"; ?>
                        <th class='w-70px'><?php echo $lang->reviewmeeting->createdDept; ?></th>
                        <th class='w-60px'><?php echo $lang->reviewmeeting->createBy; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->project; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->title; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->expert; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->reviewedBy; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->outside; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->createdDate; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->reviewStatus; ?></th>
                        <th class='w-70px'><?php echo $lang->reviewmeeting->dealUser; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reviewProjects as $reviewProject): ?>
                        <tr>
                            <td><?php echo zget($depts, $reviewProject->createdDept, ''); ?>   </td>
                            <td><?php echo zget($users, $reviewProject->createdBy, ''); ?></td>
                            <td><?php
                                $projects = $this->loadModel('project')->getByID($reviewProject->project);
                                echo $projects->name; ?>
                            </td>
                            <td>
                                <?php echo html::a($this->createLink('reviewmeeting', 'reviewview', 'id=' . $reviewProject->id, '', true), $reviewProject->title, '', "data-position = '50px' data-toggle='modal' data-type='iframe' data-width='95%' data-height = '90%' data-top='10%' style='color: #0c60e1;'"); ?>
                               <!--
                                <?php if($reviewProject->consumed > 0):?>【<?php echo $reviewProject->consumed; ?>H】<?php endif;?>
                                -->
                            </td>
                            <td><?php $experts = explode(',', str_replace(' ', '', $reviewProject->expert));
                                foreach ($experts as $account) echo ' ' . zget($users, $account); ?></td>
                            <td><?php $reviewedBy = explode(',', str_replace(' ', '', $reviewProject->reviewedBy));
                                foreach ($reviewedBy as $account) echo ' ' . zget($users, $account); ?></td>
                            <td><?php $outside = explode(',', str_replace(' ', '', $reviewProject->outside));
                                foreach ($outside as $account) echo ' ' . zget($users, $account); ?></td>
                            <td><?php echo $reviewProject->createdDate; ?></td>
                            <td><?php echo zget($lang->reviewmeeting->statusLabelList, $reviewProject->status, ''); ?></td>
                            <td><?php $dealUser = explode(',', str_replace(' ', '', $reviewProject->dealUser));
                                foreach ($dealUser as $account) echo ' ' . zget($users, $account); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11"> <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cell">
        <div class='detail'>
            <div class='detail-title'><?php echo $lang->reviewmeeting->reviewAdvice; ?></div>
            <div class="detail-content article-content ">
                <table class="table ops  table-fixed ">
                    <?php if (!empty($meetingInfo)) : ?>
                    <thead>
                    <tr>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->reviewStage; ?></th>
                        <th class='w-150px'><?php echo $lang->reviewmeeting->reviewNode; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->reviewPerson; ?></th>
                        <th class='w-180px'><?php echo $lang->reviewmeeting->reviewResult; ?></th>
                        <th class='w-150px'><?php echo $lang->reviewmeeting->reviewOpinion; ?></th>
                        <th class='w-100px'><?php echo $lang->reviewmeeting->reviewDate ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th rowspan="2" > <?php echo $lang->reviewmeeting->formalReview; ?></th>
                        <th><?php echo $lang->reviewmeeting->reviewNodeList['meetingReviewing']; ?></th>
                        <td><?php
                            if(!empty($meetingExperts)){
                                foreach ($meetingExperts as $dealUser){
                                    if(!empty($dealUser)){
                                        echo zget($users, $dealUser->reviewer, '');
                                    }
                                }
                            }else{
                             if(!empty($meetingInfo->dealUser)){
                                    echo zget($users, $meetingInfo->dealUser, '');
                                }else{
                                    echo zget($users, $meetingInfo->reviewer, '');
                                }
                            }
                            ?></td>
                        <td>
                            <?php
                              if(empty($meetingExperts) && $meetingInfo->status=='waitMeetingReview'){
                                  echo $lang->reviewmeeting->waitdeal;
                              }else{
                                foreach ($meetingExperts as $dealUser) {
                                    if ($dealUser->status !== 'pending' && !empty($meetingExperts)) {
                                        echo $lang->reviewmeeting->summaryTips;
                                    } else if ($dealUser->status === 'pending' && !empty($meetingExperts)) {
                                        echo $lang->reviewmeeting->waitdeal;
                                    }
                                }
                              }
                            ?>
                        </td>
                        <td><?php
                            foreach ($meetingExperts as $dealUser) {
                                if ($dealUser->status !== 'pending') {
                                    echo $dealUser->comment . '<br>';
                                }
                            }
                            ?>
                        </td>
                        <td><?php
                            foreach ($meetingExperts as $dealUser) {
                                if ($dealUser->status !== 'pending') {
                                    echo $dealUser->createdDate . '<br>';
                                }
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->reviewNodeList['waitMeetingOwnerReview']; ?></th>
                        <td><?php
                            echo zget($users, $meetingInfo->owner, '');
                            ?></td>
                        <td><?php
                            if(!empty($meetingOwners)) {
                                foreach ($meetingOwners as $dealUser) {
                                    if ($dealUser->status !== 'pending'&&!empty($meetingOwners)) {
                                        $dealResult = "";
                                        foreach ($titleAndResult as $value) {
                                            if ($value->status !== 'pending') {
                                                $status = $value->status;
                                                if (!empty($value->extra)) {
                                                    $result = json_decode($value->extra)->isEditInfo;
                                                    if ($result === 1) {
                                                        $status = 'passNeedEdit';
                                                    } elseif ($result === 2) {
                                                        $status = 'passNoNeedEdit';
                                                    }
                                                }
                                                $dealResult .= $value->title . "(" . $lang->reviewmeeting->reviewConclusionList[$status] . ")" . ",";
                                            }
                                        }
                                        if (!empty($value->status)) {
                                                echo rtrim($dealResult, ",");
                                        }
                                    }else if($dealUser->status === 'pending'&&!empty($meetingOwners)){
                                        echo $lang->reviewmeeting->waitdeal;
                                }
                                }
                            }
                            ?></td>
                        <td><?php
                            foreach ($meetingOwners as $dealUser) {
                                echo $dealUser->comment . '<br>';
                            }
                            ?></td>
                        <td><?php
                            foreach ($meetingOwners as $dealUser) {
                                if ($dealUser->status !== 'pending') {
                                    echo $dealUser->createdDate . '<br>';
                                }
                            }
                            ?></td>
                    </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="11"> <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cell">
        <div class='detail '>
            <div class='detail-title'><?php echo $lang->reviewmeeting->reviewMeetingSummary; ?></div>
            <div class="detail-content article-content ">
                <table class="table ops  table-fixed ">
                    <thead>

                    <tr>
                        <th colspan="4"><?php
                            if($allmeetingInfo->status=='pass'||$allmeetingInfo->status=='waitMeetingOwnerReview') {
                                echo $lang->reviewmeeting->meetingTime . "" . $meetingInfo->meetingRealTime;
                            }else{
                                echo $lang->reviewmeeting->meetingTime;
                            }
                            ?></th>
                        <th colspan="5"
                            style='text-align:left;white-space:pre-wrap'><?php
                            echo $lang->reviewmeeting->reviewDocumentNumber . "" . ltrim(rtrim($allmeetingInfo->meetingSummaryCode, ","), ","); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th colspan="2"><?php echo $lang->reviewmeeting->meetingContent; ?></th>
                        <td colspan="7">
                            <?php
                                $meetingDetailList = $allmeetingInfo->meetingDetailList;
                                if(($allmeetingInfo->status=='pass'||$allmeetingInfo->status=='waitMeetingOwnerReview') && $meetingDetailList):
                            ?>
                                <?php foreach ($meetingDetailList as $meetingDetailInfo):?>
                                <?php echo $meetingDetailInfo->meetingContent;?>
                                    <?php if($meetingDetailInfo->consumed):?>
                                        【<?php echo $meetingDetailInfo->consumed . 'H';?>】
                                    <?php endif;?>
                                <br/>
                                <?php endforeach;?>
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><?php echo $lang->reviewmeeting->reviewOwner; ?></th>
                        <td><?php $owner = explode(' ', str_replace(' ', '', $meetingInfo->owner));
                            foreach ($owner as $account) {
                                if($allmeetingInfo->status=='pass'||$allmeetingInfo->status=='waitMeetingOwnerReview'){
                                    echo ' ' . zget($users, $account);
                                }
                            } ?></td>
                        <th colspan="2"><?php echo $lang->reviewmeeting->reviewer; ?></th>
                        <td><?php
                            $reviewer = explode(',', str_replace(' ', '',$meetingInfo->reviewer));
                            foreach (array_unique($reviewer) as $account) {
                            if($allmeetingInfo->status=='pass'||$allmeetingInfo->status=='waitMeetingOwnerReview'){
                                echo ' ' . zget($users, $account);
                            }
                            } ?></td>
                        <th colspan="2"><?php echo $lang->reviewmeeting->author; ?></th>
                        <td ><?php $createdBys = explode(',', str_replace(' ', '', $allmeetingInfo->createdBys));
                            $createUsers = "";
                            foreach ($createdBys as $account) $createUsers = $createUsers . zget($users, $account) . '、';
                            if($allmeetingInfo->status=='pass'||$allmeetingInfo->status=='waitMeetingOwnerReview'){
                                echo mb_substr($createUsers, 0,-1,'utf-8');
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><?php echo $lang->reviewmeeting->reviewerExperts; ?></th>
                        <td colspan="7">
                            <?php if($meetingInfo->realExportVersion == 2):?>
                                <?php $realExport = explode(',', $meetingInfo->realExport);
                                foreach($realExport as $account) {
                                    echo ' ' . zget($users, $account);
                                }
                                ?>
                            <?php else:?>
                                <?php $allreviewer = explode(' ', str_replace(' ', '', $meetingInfo->realExport));
                                foreach ($allreviewer as $account){
                                    if($allmeetingInfo->status=='pass'|| $allmeetingInfo->status=='waitMeetingOwnerReview'){
                                        echo ' ' . $account;
                                    }
                                }
                            endif;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><?php echo $lang->reviewmeeting->reviewMeetingSummary; ?></th>
                        <td colspan="7"> <?php
                            if($allmeetingInfo->status=='pass'||$allmeetingInfo->status=='waitMeetingOwnerReview') {
                                echo $lang->reviewmeeting->meetingSummaryTips;
                            }
                            ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div class="cell">
        <div class="btn-toolbar pull-right">
            <?php if ($flag): ?>
                <?php if (common::hasPriv('reviewproblem', 'issueMeeting')) common::printLink('reviewproblem', 'issueMeeting', "meetingCode=$meetingInfo->id&browseType =all", "<i class='icon icon-checked'></i>" . $lang->reviewmeeting->checkMore, '', "class='btn btn-primary '"); ?>
            <?php endif; ?>
        </div>
        <div class='detail'>

            <div class="detail-title btn-toolbar <?php if ($flag) echo 'pull-left';?>">
                <tr>
                    <?php
                    $n = 0;
                    foreach ($issueLists as $issueList) {
                        if (!empty($issueList)) {
                            $n++;
                        }
                    }
                    echo sprintf($lang->reviewmeeting->reviewIssueTotal, $n); ?>
                </tr>
            </div>

            <div style="color:grey">
                <?php if ($flag): ?>
                    <?php echo "&emsp;&emsp;&emsp;&emsp;&emsp;" . $lang->reviewmeeting->issueTips ?>
                <?php endif; ?>
            </div>
            <div class='panel-body  scrollbar-hover detail-content article-content' style="height: 300px;">
                <table class='table table-detail table-bordered table-condensed table-striped table-fixed  '>
                    <thead>
                    <tr>
                        <th class='w-350px'><?php echo $lang->reviewissue->review; ?></th>
                        <th class='w-350px'><?php echo $lang->reviewissue->title; ?></th>
                        <th class='w-350px'><?php echo $lang->reviewissue->desc; ?></th>
                        <th class='w-80px'><?php echo $lang->reviewmeeting->operatecColumn; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($issueLists)) : ?>
                        <?php foreach ($issueLists as $issue): ?>
                            <tr>
                                <td class="text-left"><?php echo $issue->reviewtitle; ?></td>
                                <td class="text-left"><?php
                                    echo html::a($this->createLink('reviewissue', 'view', "projectID=$issue->project&issueID=$issue->id&reviewID=$issue->review", '', true), html_entity_decode($issue->title), '', "data-position = '50px' data-width='90%' data-height ='85%' data-toggle='modal' data-type='iframe'  style='color: #0c60e1;' position = 'absolute'  "); ?></td>
                                <td class="text-left text-ellipsis"><?php echo $issue->desc; ?></td>
                                <td class='c-actions' data-id='<?php echo $issue->id ?>'><?php
                                    $reviewInfo = $this->reviewmeeting->getReviewTitle($issue->review, "*");
                                    js::set('confirmActive', $lang->reviewissue->confirmActive);
                                    js::set('confirmClose', $lang->reviewissue->confirmClose);
                                    $params = "meetingId=$meetingInfo->id&project=$issue->project&issueID=$issue->id&source=detail&review=$issue->review";
                                    $params2 = "meetingId=$meetingInfo->id&issueID=$issue->id";
                                    $editURL = $this->createLink('reviewmeeting', 'editissue', $params);
                                    $delURL = $this->createLink('reviewmeeting', 'deleteissue', $params2);
                                    common::printIcon('reviewmeeting', 'editissue', $params, $meetingInfo, 'list', 'edit', '', 'iframe', true, 'data-position="50px" data-width="80%"  style="color: #0c60e1;"');
                                    echo html::a($delURL, "<i class='icon-trash'></i> ", 'hiddenwin', " data-size='sm' class='btn btn-action' style='color: #0c60e1;' title='{$lang->delete}'");
                                    ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"> <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=reviewmeeting&objectID=$meetingInfo->id");?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
    <div class='main-actions' style="/*margin-left:150px">
        <div class="btn-toolbar">
            <?php $params = "reviewID=$meetingInfo->id"; ?>
            <?php common::printBack($browseLink); ?>
            <div class='divider'></div>
            <?php
            $flag0 = $this->reviewmeeting->isClickable($meetingInfo, 'recall');
            $flag1 = $this->reviewmeeting->isClickable($meetingInfo, 'edit');
            $flag2 = $this->reviewmeeting->isClickable($meetingInfo, 'review');
            $flag3 = $this->reviewmeeting->isClickable($meetingInfo, 'confirmmeeting');
            $flag4 = $this->reviewmeeting->isClickable($meetingInfo, 'notice');
            $flag5 = $this->reviewmeeting->isClickable($meetingInfo, 'downloadfiles');
            $click = $flag0 ? 'onclick="return recall()"' : '';
            $closeflag = $this->reviewmeeting->isClickable($meetingInfo, 'close');
            $id = $meetingInfo->id;
            $user = $this->app->user->account;
            if ($flag1&&((in_array($user, explode(',', $meetingInfo->owner))) || (in_array($user, explode(',', $meetingInfo->reviewer))))) {
                common::hasPriv('reviewmeeting', 'edit') ? common::printIcon('reviewmeeting', 'edit', $params, $meetingInfo, 'list', '', '', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px" ') : '';
            }
            if (!empty($meetingInfo->dealUser) && (in_array($user, explode(',', $meetingInfo->dealUser)))&&$flag2) {
                common::hasPriv('reviewmeeting', 'review') ? common::printIcon('reviewmeeting', 'review', $params, $meetingInfo, 'list', 'glasses', '', 'iframe', true, 'data-position = "50px" data-toggle="modal" data-type="iframe" data-width="1200px" ', $this->lang->reviewmeeting->reviewTipMsg) : '';
            }
            if ((in_array($user, explode(',', $meetingInfo->owner)))&&$flag3) {
                common::hasPriv('reviewmeeting', 'confirmmeeting') ? common::printIcon('reviewmeeting', 'confirmmeeting', $params, $meetingInfo, 'list','menu-users', '', 'iframe', true, 'data-width="750" data-toggle="modal"') : '';
            }
            if ((in_array($user, explode(',', $meetingInfo->reviewer)))&&$flag4) {
                common::hasPriv('reviewmeeting', 'notice') ? common::printIcon('reviewmeeting', 'notice', $params, $meetingInfo, 'list', 'envelope-o', '', 'iframe', true, 'data-width="900" data-height="600" data-toggle="modal"',$this->lang->reviewmeeting->notice.$this->lang->reviewmeeting->common) : '';
            }
            if ($flag5) {
                common::hasPriv('reviewmeeting', 'downloadfiles') ? common::printIcon('reviewmeeting', 'downloadfiles', $params, $meetingInfo, 'list', 'download', '', '', '', '') : '';
            }
            ?>
        </div>
    </div>
</div>
<div class="side-col col-4 ">
    <div class="cell">
        <div class='detail'>
            <div class='detail-title'><?php echo $lang->review->basicInfo; ?></div>

            <div class='detail-content'>
                <table class='table table-data'>
                    <tbody>
                    <tr>
                        <th class='w-100px'><?php echo $lang->reviewmeeting->meetingCode; ?></th>
                        <td><?php echo $meetingInfo->meetingCode; ?></td>
                    </tr>
                    <tr>
                        <th class='w-100px'><?php echo $lang->reviewmeeting->reviewOwner; ?></th>
                        <td>
                            <?php echo zget($users, $meetingInfo->owner, ''); ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->reviewer; ?></th>
                        <td><?php
                            $reviewer = explode(',', str_replace(' ', '',$meetingInfo->reviewer));
                            foreach (array_unique($reviewer) as $account) echo ' ' . zget($users, $account);
                            ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->dealUser; ?></th>
                        <td><?php
                            $dealUser = explode(',', str_replace(' ', '', $meetingInfo->dealUser));
                            $txt = '';
                            foreach ($dealUser as $account)
                                $txt .= zget($users, $account, '') . " &nbsp;";
                            echo $txt;
                            ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->meetingstatus; ?></th>
                        <td><?php echo zget($lang->reviewmeeting->statusLabelList, $meetingInfo->status, ''); ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->expectedExperts; ?></th>
                        <td><?php
                            $meetingPlanExport = explode(',', str_replace(' ', '', $meetingInfo->meetingPlanExport));
                            foreach (array_unique($meetingPlanExport) as $account) echo ' ' . zget($users, $account)
                            ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->review->expert; ?></th>
                        <td><?php
                            $expert = "";
                            foreach ($reviewProjects as $reviewProject) {
                                $expert = $expert . "," . $reviewProject->expert;

                            }
                            $expert = explode(',', str_replace(' ', '', $expert));
                            foreach (array_unique($expert) as $account) echo ' ' . zget($users, $account);
                            ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->review->reviewedBy; ?></th>
                        <td><?php
                            $reviewedBy = "";
                            foreach ($reviewProjects as $reviewProject) {
                                $reviewedBy = $reviewedBy . "," . $reviewProject->reviewedBy;

                            }
                            $reviewedBy = explode(',', str_replace(' ', '', $reviewedBy));
                            foreach (array_unique($reviewedBy) as $account) echo ' ' . zget($users, $account)
                            ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->review->outside; ?></th>
                        <td><?php
                            $outside = "";
                            foreach ($reviewProjects as $reviewProject) {
                                $outside = $outside . "," . $reviewProject->outside;

                            }
                            $outside = explode(',', str_replace(' ', '', $outside));
                            foreach (array_unique($outside) as $account) echo ' ' . zget($users, $account)
                            ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->meetingPlanTime; ?></th>
                        <td><?php echo $meetingInfo->meetingPlanTime != '0000-00-00 00:00:00' ? $meetingInfo->meetingPlanTime : ''; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->meetingRealTime; ?></th>
                        <td><?php echo $meetingInfo->meetingRealTime != '0000-00-00 00:00:00' ? $meetingInfo->meetingRealTime : ''; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->realExport; ?></th>
                        <td>
                            <?php if($meetingInfo->realExportVersion == 2):?>
                                <?php $realExport = explode(',', $meetingInfo->realExport);
                                foreach($realExport as $account){
                                    echo ' ' . zget($users, $account);
                                }
                                ?>
                            <?php else:?>
                                <?php $allreviewer = explode(' ', str_replace(' ', '', $meetingInfo->realExport));
                                foreach ($allreviewer as $account){
                                        echo ' ' . $account;
                                }
                            endif;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->reviewTopic; ?></th>
                        <td><?php
                            $title = "";
                            foreach ($reviewProjects as $reviewProject) {
                                if ($title === "") {
                                    $title = $reviewProject->title;
                                } else {
                                    $title = $title . "<br>" . $reviewProject->title;
                                }
                            }
                            echo $title; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->reviewIDList; ?></th>
                        <td>
                            <?php
                            $reviewID = "";
                            foreach ($reviewProjects as $reviewProject) {
                                if ($reviewID === "") {
                                    $reviewID = $reviewProject->id;
                                } else {
                                    $reviewID = $reviewID . "," . $reviewProject->id;
                                }
                            }
                            echo $reviewID; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->createdDept; ?></th>
                        <td>
                            <?php
                            $createdDept = array();
                            foreach ($reviewProjects as $reviewProject) {
                                $createdDept[] = $reviewProject->createdDept;
                            }
                            foreach (array_unique($createdDept) as $account) {
                                echo  zget($depts, $account, "")."<br>";
                             }
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->createBy; ?></th>
                        <td>
                            <?php

                            $createdBys = explode(',', str_replace(' ', '', $allmeetingInfo->createdBys));
                            foreach ($createdBys as $account) echo rtrim(zget($users, $account) . ' ', '');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->projectManager; ?></th>
                        <td> <?php
                            $projectManager = array();
                            foreach ($reviewProjects as $reviewProject) {
                                $projectManager[] = $this->reviewmeeting->getPMById($reviewProject->project);
                            }
                            foreach (array_unique($projectManager) as $account) {
                                $PMs = explode(',', str_replace(' ', '', $account));
                                foreach ($PMs as $account) echo rtrim(zget($users, $account) . ' ', '')."<br>";
                                //echo  zget($users, $account, "")
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->deptLeads; ?></th>
                        <td> <?php
                            $deptLeads = array();
                            $manage = array();
                            foreach ($reviewProjects as $reviewProject) {
                                $deptLeads[] = $this->reviewmeeting->getManager1ByCreatedDept($reviewProject->createdDept);
                            }
                            foreach ($deptLeads as $v1) {
                                foreach ($v1 as $v2) {
                                    foreach ($v2 as $v3) {
                                        $manage[]=  $v3 ;
                                    }
                                }
                            }
                            if (!empty($manage)) {
                                foreach (array_unique($manage) as $account){
                                    $account =   explode(',',  $account);
                                    foreach($account as $item) echo ' ' . zget($users, $item);
                                }
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->projectSource; ?></th>
                        <td>
                            <?php
                            foreach ($reviewProjects as $reviewProject) {
                                $sources = [];
                                $plan =  $this->loadModel('projectplan')->getByProjectID($reviewProject->project);
                                foreach (explode(',',$plan->creation->source) as $source)
                                {
                                    if(empty($source)) continue;
                                    $sources[] = zget($lang->reviewmeeting->basisList, $source, '').'<br>' ;
                                }
                                echo implode(',', $sources);

                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->project; ?></th>
                        <td>
                            <?php
                            $projectName = "";
                            foreach ($reviewProjects as $reviewProject) {
                                $projects = $this->loadModel('project')->getByID($reviewProject->project);
                                $projectName .= $projects->name . "<br>";
                            }
                            echo $projectName; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->projectType; ?></th>
                        <td>
                            <?php
                            $projectType = "";
                            foreach ($reviewProjects as $reviewProject) {
                                echo zget($lang->reviewmeeting->typeList, $reviewProject->projectType, '') . "<br>";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->createUser; ?></th>
                        <td><?php echo ' ' . zget($users, $meetingInfo->createUser); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->createdTime; ?></th>
                        <td><?php echo $meetingInfo->createTime != '0000-00-00 00:00:00' ? $meetingInfo->createTime : ''; ?></td>
                    </tr>

                    <tr>
                        <th><?php echo $lang->reviewmeeting->editBy; ?></th>
                        <td><?php echo ' ' . zget($users, $meetingInfo->editBy); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->reviewmeeting->editTime; ?></th>
                        <td><?php echo $meetingInfo->editTime; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cell">
        <div class="detail">
            <div class="detail-title"><?php echo $lang->consumedTitle; ?></div>
            <div class='detail-content'>
                <table class='<?php if($flag) echo 'table';?> table-data'>
                    <tbody>
                    <tr>
                        <th class='w-80px'><?php echo $lang->review->nodeUser; ?></th>
                        <!--<td class='text-center w-50px'><?php /*echo $lang->review->consumed; */?></td>-->
                        <td class='text-center  w-100px'><?php echo $lang->review->before; ?></td>
                        <td class='text-center w-130px'><?php echo $lang->review->after; ?></td>
                    </tr>
                    <?php foreach ($meetingInfo->consumed as $index => $c): ?>
                        <tr>
                            <th class='w-80px'><?php echo zget($users, $c->account, ''); ?></th>
                           <!-- <td class='text-center  w-50px'><?php /*echo $c->consumed . ' ' . $lang->hour; */?></td>-->
                            <td class='text-center  w-100px'>
                                <?php
                                if ($c->before == 'pass') {
                                    echo '已确定会议评审';
                                }else{
                                    echo zget($allstatus, $c->before, '-');
                                }
                                ?>
                            </td>
                            <td class='text-center w-130px'>
                                <?php

                                if (empty($c->before)) {
                                    echo zget($allstatus, $c->after, '-');
                                } elseif ($c->before == 'statusLabelList') {
                                    echo zget($lang->review->condition, $lang->reviewmeeting->statusLabelList['waitMeetingReview']);
                                } elseif ($c->before == 'waitMeetingOwnerReview' && $c->after == 'pass') {
                                    //baseline状态展示位评审通过
                                    echo '已确定会议评审';
                                } else {
                                    echo $allstatus[$c->after];
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>


<?php include '../../common/view/footer.html.php'; ?>


