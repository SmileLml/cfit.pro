<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<style>
    .detail-title-bold{font-size:14px;lisne-height:20px;font-weight:bold;}
    .changeInfo{
        text-align: center;
    }
    .changeInfo th{
        text-align: center;
    }
</style>

<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php $requirementHistory = $app->session->requirementHistory? $app->session->requirementHistory: inlink('browse')?>
            <?php echo html::a($requirementHistory, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $requirement->code ?></span>
            <span class="text" title='<?php echo htmlspecialchars_decode($requirement->name); ?>'><?php echo htmlspecialchars_decode($requirement->name); ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->desc; ?></div>
                <?php if(strip_tags($requirement->desc) == $requirement->desc):?>
                    <div class="detail-content article-content" style="white-space: pre-line">
                <?php else:?>
                    <div class="detail-content article-content">
                <?php endif;?>
                    <?php echo !empty($requirement->desc) ? $requirement->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->baseChangeTip; ?></div>
                <div class="detail-content article-content">
                    <?php if (!empty($changeInfo)): ?>
                        <table class="table changeInfo">
                            <tr>
                                <th class="w-50px"><?php echo $this->lang->requirement->changeTimes; ?></th>
                                <th class="w-150px"><?php echo $this->lang->requirement->changeDate; ?></th>
                                <th class="w-150px"><?php echo $this->lang->requirement->changeCode; ?></th>
                                <th class="w-100px"><?php echo $this->lang->requirement->changeStatus; ?></th>
                                <th class="w-50px"><?php echo '操作'; ?></th>
                            </tr>
                            <?php foreach ($changeInfo as $key => $item):?>
                                <tr>
                                    <td><?php echo $key+1; ?></td>
                                    <td><?php echo $item->createdDate;?></td>
                                    <td>
                                        <?php echo html::a($this->createLink('requirement', 'changeview', array('id' => $item->id,'requirementID'=>$item->requirementID), '', true), $item->changeCode, '', 'class="iframe"'); ?>
                                    </td>
                                    <td><?php echo zget($this->lang->requirement->changeStatusList,$item->status); ?></td>
                                    <td>
                                        <?php
                                        if($item->status == 'back' && $this->app->user->account == $item->createdBy)
                                        {
                                            if(common::hasPriv('requirement','revoke')) common::printLink('requirement', 'revoke', array('id' => $item->id,'requirementID'=>$item->requirementID),  '撤销', '', "class='iframe text-blue'",'',true);
                                            echo "<span>&nbsp;&nbsp;</span>";
                                            if(common::hasPriv('requirement','editchange')) common::printLink('requirement', 'editchange', array('id' => $item->id,'requirementID'=>$item->requirementID), '编辑', '', "class='iframe text-blue'",'',true);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </table>
                    <?php else: ?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->comment; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($requirement->comment) ? $requirement->comment : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->liftCycle; ?></div>
                <div class="detail-content article-content">
                    <table class="table changeInfo">
                        <tr>
                            <th class="w-150px"><?php echo $this->lang->requirement->commonOpinion; ?></th>
                            <th class="w-150px"><?php echo $this->lang->requirement->common; ?></th>
                            <th class="w-100px"><?php echo $this->lang->requirement->commonDemand; ?></th>
                        </tr>
                        <tr>
                            <td rowspan="<?php echo $lifeOpinionInfo['countAll'];?>">
                                <?php echo html::a($this->createLink('opinion', 'view', array('id' => $lifeOpinionInfo['id']), '', true), $lifeOpinionInfo['code'].'('.$lifeOpinionInfo['name'].')', '', "class='iframe' data-width='90%'");?>
                            </td>
                            <td rowspan="<?php echo $lifeOpinionInfo['requirements'][0]['count'];?>">
                                <!-- 当前任务无需跳转-->
                                <?php
                                $firstRequirement = $lifeOpinionInfo['requirements'][0];
                                if($lifeOpinionInfo['requirements'][0]['code'] != $requirement->code)
                                {
                                    echo html::a($this->createLink('requirement', 'view', array('id' => $firstRequirement['id']), '', true), $firstRequirement['code'].'('.$firstRequirement['name'].')', '', "class='iframe' data-width='90%'");
                                }else{
                                    echo $firstRequirement['code'].'('.$firstRequirement['name'].')';
                                }
                                ?>
                            </td>
                            <?php $demandZero = $lifeOpinionInfo['requirements'][0]['demands'][0]; ?>
                            <?php if(isset($demandZero->id)):?>
                                <td>
                                    <?php echo html::a($this->createLink('demand', 'view', array('id' => $demandZero->id), '', true), $demandZero->code.'('.$demandZero->title.')', '', "class='iframe' data-width='90%'"); ?>
                                </td>
                            <?php else:?>
                                <td><?php echo '-';?></td>
                            <?php endif;?>
                        </tr>

                        <?php foreach ($lifeOpinionInfo['requirements'] as $item => $lifeRequirement):?>
                            <?php if($item == 0):?>

                                <?php for($i = 1; $i < $lifeRequirement['count'];$i++):?>
                                    <tr>
                                        <?php if(!empty($lifeRequirement['demands'][$i]->code)):?>
                                            <td><?php echo html::a($this->createLink('demand', 'view', array('id' => $lifeRequirement['demands'][$i]->id), '', true), $lifeRequirement['demands'][$i]->code.'('.$lifeRequirement['demands'][$i]->title.')', '', "class='iframe' data-width='90%'"); ?>
                                        <?php else:?>
                                            <td><?php echo $lifeRequirement['demands'][$i]->code; ?></td>
                                        <?php endif;?>
                                    </tr>
                                <?php endfor;?>

                            <?php endif;?>

                            <?php if($item > 0):?>
                                <?php foreach ($lifeRequirement['demands'] as $demandNum => $lifeDemand):?>
                                    <tr>
                                        <?php if($demandNum == 0):?>
                                            <td rowspan="<?php echo $lifeRequirement['count'];?>">
                                                <?php
                                                if($lifeRequirement['code'] != $requirement->code)
                                                {
                                                    echo html::a($this->createLink('requirement', 'view', array('id' => $lifeRequirement['id']), '', true), $lifeRequirement['code'].'('.$lifeRequirement['name'].')', '', "class='iframe' data-width='90%'");
                                                }else{
                                                    echo $lifeRequirement['code'].'('.$lifeRequirement['name'].')';
                                                }
                                                ?>
                                            </td>
                                        <?php endif;?>

                                        <?php if(isset($lifeDemand->id)):?>
                                            <td><?php echo html::a($this->createLink('demand', 'view', array('id' => $lifeDemand->id), '', true), $lifeDemand->code.'('.$lifeDemand->title.')', '', "class='iframe' data-width='90%'"); ?>
                                        <?php else:?>
                                            <td><?php echo '-';?></td>
                                        <?php endif;?>

                                    </tr>
                                <?php endforeach;?>
                            <?php endif;?>
                        <?php endforeach;?>

                    </table>
                </div>
            </div>
        </div>
        <?php if (!empty($nodes) && !empty($requirement->feedbackStatus)): ?>
            <div class="cell">
                <div class="detail">
                    <div class="pull-right">
                        <?php if(common::hasPriv('requirement','historyRecord')) common::printLink('requirement', 'historyRecord', "requirementID=$requirement->id", "<i class='icon icon-history'></i>" . $lang->requirement->historyRecord, '', "class='iframe text-blue' data-width='90%' ",'',true);?>
                    </div>
                    <div class="detail-title">
                        <?php echo $lang->requirement->reviewDetails; ?>
                    </div>
                    <div class="detail-content article-content">
                        <?php if (!empty($nodes)): ?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-120px"><?php echo $lang->requirement->reviewnodes; ?></th>
                                    <th class="w-120px"><?php echo $lang->requirement->currentreview; ?></th>
                                    <th class="w-120px"><?php echo $lang->requirement->reviewresults; ?></th>
                                    <th class="w-120px"><?php echo $lang->requirement->reviewnodecomment; ?></th>
                                    <th class="w-120px"><?php echo $lang->requirement->reviewdate; ?></th>
                                </tr>
                                <?php if ($nodes[1]->reviewers): ?>
                                    <?php
                                    //循环数据

                                    foreach ($lang->requirement->reviewerStageList as $key => $reviewNode):
                                        $reviewerUserTitle = '';
                                        $reviewerUsersShow = '';
                                        $realReviewer = new stdClass();
                                        $realReviewer->status = '';
                                        $realReviewer->comment = '';
                                        if(isset($nodes[$key])) {
                                            $currentNode = $nodes[$key];
                                            $reviewers = $currentNode->reviewers;
                                            if(is_array($reviewers) || !empty($reviewers)) {
                                                //所有审核人
                                                $reviewersArray = array_column($reviewers, 'reviewer');
                                                $userCount = count($reviewersArray);
                                                if ($userCount > 0) {
                                                    $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                                    $reviewerUserTitle = implode(',', $reviewerUsers);
                                                    $subCount = 3;
                                                    $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                                    //获得实际审核人
                                                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                                }
                                            }
                                        }else{
                                            continue;
                                        }
                                        ?>
                                        <tr>
                                            <th><?php echo $reviewNode;?></th>
                                            <td title="<?php echo $reviewerUserTitle; ?>">
                                                <?php echo $reviewerUsersShow; ?>
                                            </td>
                                            <td>
                                                <?php echo zget($lang->requirement->resultstatusList, $realReviewer->status, '');?>
                                                <?php
                                                if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'|| $realReviewer->status == 'syncfail' || $realReviewer->status == 'syncsuccess' || $realReviewer->status == 'feedbacksuccess' || $realReviewer->status == 'feedbackfail'):
                                                    ?>
                                                    &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                                <?php endif; ?>
                                            </td>
                                            <td><?php
                                                echo $realReviewer->comment;
                                                ?></td>
                                            <td><?php echo $realReviewer->reviewTime; ?></td>
                                        </tr>

                                    <?php endforeach;?>
                                <?php endif; ?>
                            </table>
                        <?php else: ?>
                            <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($requirement->feedbackStatus) and $requirement->feedbackStatus != ''): ?>
            <div class="cell">
                <div class="detail-title"><?php echo $lang->requirement->feedbackInfo . '<br/>'; ?></div>
                <div class="detail">
                    <div class="detail-title-bold"><?php echo $lang->requirement->analysis; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($requirement->analysis) ? $requirement->analysis : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title-bold"><?php echo $lang->requirement->handling; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($requirement->handling) ? $requirement->handling : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
                <div class="detail">
                    <div class="detail-title-bold"><?php echo $lang->requirement->implement; ?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($requirement->implement) ? $requirement->implement : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    </div>
                </div>
            </div>
            <?php if(!empty($requirementChangeInfo)):?>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->requirement->changeRecord; ?></div>
                        <div class="detail-content article-content">
                            <table class="table ops" style="text-align: center">
                                <tr>
                                    <th class="w-100px" style="text-align: center"><?php echo $lang->requirement->changeNum; ?></th>
                                    <th class="w-200px" style="text-align: center"><?php echo $lang->requirement->changeTime; ?></th>
                                    <th class="w-200px" style="text-align: center"><?php echo $lang->requirement->changeCode; ?></th>
                                    <!--                                <th class="w-100px" style="text-align: center">--><?php //echo $lang->requirement->changeRemark; ?><!--</th>-->
                                </tr>
                                <?php $num = 1;
                                foreach ($requirementChangeInfo as $val): ?>
                                    <tr>
                                        <td><?php echo $num++; ?></td>
                                        <td>
                                            <?php echo $val->createdDate; ?>
                                        </td>
                                        <td>
                                            <a class="iframe" data-width="900" href='<?php echo $this->createLink('requirementchange', 'changeview', "changeID=$val->id",'',true)?>'><?php echo $val->changeNumber; ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif;?>


        <?php endif; ?>

        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=requirement&objectID=$requirement->id"); ?>
        <?php if ($requirement->files): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->requirement->fileTitle; ?> <i
                                class="icon icon-paper-clip icon-sm"></i></div>
                    <div class="detail-content">
                        <?php
                        foreach ($requirement->files as $key => $file) {
                            echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $requirement, 'canOperate' => $file->addedBy == $this->app->user->account));
                        }; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($requirementHistory);?>
                <div class='divider'></div>
                <?php
                common::printIcon('requirement', 'edit', "requirementID=$requirement->id", $requirement, 'button', 'edit');
                common::printIcon('requirement', 'assignTo', "requirementID=$requirement->id", $requirement, 'button', '', '', 'iframe', true);
                common::printIcon('requirement', 'subdivide', "requirementID=$requirement->id", $requirement, 'button', 'split', '');
                //                        common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list','alter', '', 'iframe',true);
                //研发责任人取所有需求条目合集 迭代三十二 将变更流程发起人范围扩大至全部人员  变更中、已退回[2,3]
                if(!in_array($requirement->requirementChangeStatus,[2,3]))
                {
                    common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'button','alter', '', 'iframe',true);
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                }

                common::printIcon('requirement', 'feedback', "requirementID=$requirement->id", $requirement, 'button');
                //                        common::printIcon('requirement', 'review', "requirementID=$requirement->id", $requirement, 'list', 'glasses', '', 'iframe', true);
                ?>
                <?php if($requirement->status != 'deleted') :?>
                <?php if($this->app->user->account == 'admin'
                    or
                    (
                        ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false
                    )
                    and
                    (
                    (strstr($requirement->changeNextDealuser, $app->user->account) !== false)
                    )
                ):
                    ?>
                    <div class="btn-group dropup" >
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <li><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                            <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                        </ul>
                    </div>
                <?php elseif($requirement->status != 'deleteout' and ($this->app->user->account == 'admin' or  ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false)):?>
                    <div class="btn-group dropup">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <li style="margin-left: -10px"><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                            <li style="margin-top:-10px;margin-bottom:5px;margin-left: -10px"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->reviewchange; ?></span></li>
                        </ul>
                    </div>
                <?php elseif($requirement->status != 'deleteout' and ($this->app->user->account == 'admin' or (strstr($requirement->changeNextDealuser, $app->user->account) !== false))):?>
                    <div class="btn-group dropup">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <li style="margin-top:-10px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->review; ?></span></li>
                            <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                        </ul>
                    </div>
                <?php else:?>
                    <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->requirement->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                <?php endif;?>
                <?php endif;?>

                <?php
                if($this->app->user->account == 'admin' or (in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy)) {
                    if ($requirement->status == 'closed') {
                        common::printIcon('requirement', 'activate', "requirementID=$requirement->id", $requirement, 'button', 'magic', '', 'iframe', true);
                    } else {
                        common::printIcon('requirement', 'close', "requirementID=$requirement->id", $requirement, 'button', 'pause', '', 'iframe', true);
                    }
                }else if($requirement->status == 'closed'){
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                }

                if ($requirement->ignoreStatus) {
                    common::printIcon('requirement', 'recover', "requirementID=$requirement->id", $requirement, 'button', 'bell', '', 'iframe', true);
                } else {
                    common::printIcon('requirement', 'ignore', "requirementID=$requirement->id", $requirement, 'button', 'ban', '', 'iframe', true);
                }
                common::printIcon('requirement', 'delete', "requirementID=$requirement->id", $requirement, 'button', 'trash', '', 'iframe', true);
                if(in_array($this->app->user->account,$alloWRePush))
                {
                    common::printIcon('requirement', 'push',  "requirementID=$requirement->id", $requirement, 'button', 'share', '', 'iframe', true);
                }
                //反馈期限维护
                common::printIcon('requirement', 'defend', "requirementID=$requirement->id", $requirement, 'button', '', '', 'iframe', true,'',$lang->requirement->defend);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->opinionID; ?></th>
                            <td><?php echo html::a($this->createLink('opinion', 'view', "id=$requirement->opinion"), $opinion->name); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->app; ?></th>
                            <?php
                            $appNames = '';
                            $appList = explode(',', $requirement->app);
                            foreach ($appList as $app) {
                                if ($app) $appNames .= ' ' . zget($apps, $app, '');
                                $appNames .= '<br>';
                            }; ?>
                            <td title='<?php echo $appNames; ?>'><?php echo $appNames; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->acceptTime; ?></th>
                            <td><?php echo $requirement->acceptTime; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->lastChangeTime; ?></th>
                            <td><?php echo $requirement->lastChangeTime; ?></td>
                        </tr>

                        <?php if(!empty($requirement->requirementChangeTimes)):?>
                            <tr>
                                <th><?php echo $lang->requirement->changeTimes;?></th>
                                <td><?php echo $requirement->requirementChangeTimes;?></td>
                            </tr>
                        <?php endif;?>

                        <tr>
                            <th><?php echo $lang->requirement->planEnd; ?></th>
                            <td>
                                <?php  echo ($requirement->planEnd == '0000-00-00 00:00:00' || $requirement->planEnd == '0000-00-00' || empty($requirement->planEnd)) ? '': $requirement->planEnd; ?>
<!--                                --><?php //echo  common::printIcon('requirement', 'editEnd', "requirementID=$requirement->id", $requirement, 'list','edit','','iframe',true) ;?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->requirement->deadLine; ?></th>
                            <td><?php echo ($requirement->deadLine == '0000-00-00 00:00:00' || $requirement->deadLine == '0000-00-00' || empty($requirement->deadLine)) ? '': $requirement->deadLine; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->newPublishedTime; ?></th>
                            <td><?php echo $requirement->newPublishedTime; ?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->project; ?></th>

                            <td>
                                <?php
                                foreach ($projectList as $projectID => $item) {
                                    if ($projectID) {
                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectID), $item->name, '', "data-app='platform' style='color: #0c60e1;'");
                                        echo "<br>";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->ChildName; ?></th>
                            <td><?php echo $requirement->ChildName; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->solvedTime; ?></th>
                            <?php if(in_array($requirement->status,['delivered','onlined'])):?>
                                <td><?php echo $requirement->solvedTime; ?></td>
                            <?php endif;?>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->taskLaunchTime; ?></th>
                            <td><?php echo $requirement->onlineTimeByDemand; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->productManager; ?></th>
                            <td><?php echo zmget($users,  $requirement->productManager, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->dept; ?></th>
                            <td><?php echo zmget($depts, $requirement->dept, ''); ?></td>

                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->ownerCN; ?></th>
                            <td><?php echo zmget($users, $requirement->demandOwner, ''); ?></td>
                        </tr>
                        <?php if($requirement->createdBy != 'guestcn'):?>
                            <tr>
                                <th><?php echo $lang->requirement->lockStatus;?></th>
                                <td>
                                    <?php echo zget($this->lang->requirement->lockStatusList,$requirement->changeLock,'');?>
                                    <?php if(((common::hasPriv('requirement', 'unlockSeparate') && in_array($this->app->user->account,$unLock)) || $this->app->user->account == 'admin') && $requirement->changeLock == 2 ) :?>
                                        <?php echo  common::printIcon('requirement', 'unlockSeparate', "requirementID=$requirement->id", $requirement, 'list','edit','','iframe',true) ;?>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endif;?>

                        <?php if($requirement->createdBy == 'guestcn'):?>
                            <tr>
                                <th><?php echo $lang->requirement->type;?></th>
                                <td>
                                    <?php echo $requirement->type; ?>
                                </td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->requirement->requireStartTime;?></th>
                                <td>
                                    <?php echo $requirement->requireStartTime != '0000-00-00' ? $requirement->requireStartTime:''; ?>
                                </td>
                            </tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->status;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->status; ?></th>
                            <td><?php echo zget($lang->requirement->statusList, $requirement->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->pending; ?></th>
                            <?php
                            $dealUserArray = explode(",", $requirement->dealUser);
                            $dealUserChnArray = array();
                            foreach ($dealUserArray as $dealUser) {
                                array_push($dealUserChnArray, zget($users, $dealUser, ''));
                            }
                            $dealUserChn = trim(implode(",", $dealUserChnArray),','); ?>
                            <td><?php echo $dealUserChn; ?></td>
                        </tr>
                        <?php if($requirement->mailto != ''):?>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->mailto;?></th>
                                <td><?php foreach(explode(',', $requirement->mailto) as $user) echo zget($users, $user, '') . ' '; ?></td>
                            </tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo "关联属性";?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->sourceMode; ?></th>
                            <td><?php echo zget($lang->opinion->sourceModeList, $opinion->sourceMode, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->sourceName; ?></th>
                            <td><?php echo $opinion->sourceName; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->line; ?></th>
                            <?php
                            $lineTitle = '';
                            $lineList = explode(',', str_replace(' ', '', $requirement->line));
                            foreach ($lineList as $lineID) {
                                if ($lineID) {
                                    $lineTitle .= ' ' . zget($lines, $lineID, '');
                                    $lineTitle .= '<br>';
                                }
                            }
                            ?>
                            <td title='<?php echo $lineTitle; ?>'><?php echo $lineTitle; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->requirement->ownproduct; ?></th>
                            <?php
                            $productTitle = '';
                            $productList = explode(',', str_replace(' ', '', $requirement->product));
                            foreach ($productList as $productID) {
                                if ($productID) {
                                    $productTitle .= ' ' . zget($products, $productID, '');
                                    $productTitle .= '<br>';
                                }
                            }
                            ?>
                            <td title='<?php echo $productTitle; ?>'><?php echo $productTitle; ?></td>
                        </tr>
                        <?php if ($opinion->sourceMode == 8):?>
                            <tr>
                                <th><?php echo $lang->requirement->isImprovementTitle; ?></th>
                                <td title=''><?php echo $lang->requirement->isImprovementServicesList[$requirement->isImprovementServices]; ?></td>
                            </tr>
                            <tr style="display: none;">
                                <th><?php echo $lang->requirement->estimateWorkloadTitle; ?></th>
                                <td title=''><?php echo $requirement->estimateWorkload; ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->requirement->projectManager; ?></th>
                            <?php
                            $projectManagerArray = explode(",", $requirement->projectManager);
                            $projectManagerChnArray = array();
                            foreach ($projectManagerArray as $projectManager) {
                                array_push($projectManagerChnArray, zget($users, $projectManager, ''));
                            }
                            $projectManagerChn = trim(implode(",", $projectManagerChnArray),','); ?>
                            <td title='<?php echo $projectManagerChn; ?>'><?php echo $projectManagerChn; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if (!empty($requirement->feedbackStatus) and $requirement->feedbackStatus != ''): ?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->requirement->feedbackInfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->feedbackDealuser; ?></th>
                                <?php $feedbackDealuser = zmget($users,$requirement->feedbackDealUser);?>
                                <td title='<?php echo $feedbackDealuser; ?>'><?php echo $feedbackDealuser; ?></td>
                            </tr>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->feedbackStatus; ?></th>
                                <td><?php echo zget($lang->requirement->feedbackStatusList, $requirement->feedbackStatus, ''); ?></td>
                            </tr>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->parentCode; ?></th>
                                <td><?php echo $requirement->parentCode; ?></td>
                            </tr>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->entriesCode; ?></th>
                                <td><?php echo $requirement->entriesCode; ?></td>
                            </tr>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->feedbackCode; ?></th>
                                <td><?php echo $requirement->feedbackCode; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->requirement->contact; ?></th>
                                <td><?php echo $requirement->contact; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->requirement->feedbackBy; ?></th>
                                <td><?php echo zget($users, $requirement->feedbackBy, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->requirement->owner; ?></th>
                                <td><?php echo zget($users, $requirement->owner, ''); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->requirement->end; ?></th>
                                <td>
                                    <?php echo $requirement->end; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->requirement->feedbackDate; ?></th>
                                <td><?php echo $requirement->feedbackDate; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->requirement->method; ?></th>
                                <td><?php echo zget($lang->requirement->methodList, $requirement->method, ''); ?></td>
                            </tr>
                            <tr>
                                <th class='w-120px'><?php echo $lang->requirement->reviewComments; ?></th>
                                <td><?php echo $requirement->reviewComments; ?></td>
                            </tr>

                            <!--是否纳入内部反馈超期-->
                            <?php if($isOverDateInfoVisible && $requirement->createdBy == 'guestcn' ):?>
                                <tr>
                                    <th><?php echo $lang->requirement->feedbackOver;?></th>
                                    <td>
                                        <?php echo zget($this->lang->requirement->feedbackOverList,$requirement->feedbackOver,'');?>
                                        <!--二线实现、自定义配置人有权限-->
                                        <?php if(isset($this->lang->demand->feedbackOverErList[$this->app->user->account]) || $this->app->user->account == 'admin'):?>
                                            <?php echo html::a($this->createLink('requirement', 'feedbackOver', "requirementID=$requirement->id", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                        <?php endif;?>
                                    </td>
                                </tr>
                            <?php endif;?>

                            <!--迭代二十九 内部反馈是否超时：（22年9月3日之前的内部反馈是否超时相关字段数据隐藏）-->
                            <?php if($requirement->createdDate >= '2022-09-04 00:00:00'):?>
                                <tr>
                                    <th><?php echo $lang->requirement->ifOverTime; ?></th>
                                    <td>
                                        <?php echo zget($lang->requirement->ifOverDateList, $requirement->ifOverDate, ''); ?>
                                        <?php echo $requirement->feekBackBetweenTimeInside; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->requirement->insideFeedback; ?></th>
                                    <td><?php echo $requirement->feekBackEndTimeInside; ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang->requirement->insideDays; ?></th>
                                    <td><?php echo $insideDays; ?></td>
                                </tr>
                            <?php endif;?>

                            <?php if($isOverDateInfoVisible):?>
                                <!--外部反馈是否超时-->
                                <tr>
                                    <th><?php echo $lang->requirement->ifOverTimeOutSide; ?></th>
                                    <td>
                                        <?php echo zget($lang->requirement->ifOverDateList, $requirement->ifOverTimeOutSide, ''); ?>
                                        <?php echo $requirement->feekBackBetweenOutSide; ?>
                                    </td>
                                </tr>

                                <!--外部反馈期限-->
                                <tr>
                                    <th><?php echo $lang->requirement->outsideFeedback; ?></th>
                                    <td><?php echo $requirement->feekBackEndTimeOutSide; ?></td>
                                </tr>

                                <!--距外部超期剩余-->
                                <tr>
                                    <th><?php echo $lang->requirement->outsideDays; ?></th>
                                    <td><?php echo $outsideDays; ?></td>
                                </tr>

                            <?php endif;?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->statusTransition;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->nodeUser;?></th>
                            <!--                            <td class='text-right'>--><?php //echo $lang->requirement->consumed;?><!--</td>-->
                            <td class='text-center'><?php echo $lang->requirement->before;?></td>
                            <td class='text-center'><?php echo $lang->requirement->after;?></td>
                        </tr>
                        <?php foreach($requirement->consumed as $c):?>
                            <tr>
                                <th class='w-120px'><?php echo zget($users, $c['account'], '');?></th>
                                <!--                                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                                <?php
                                echo "<td class='text-center'>".zget($lang->requirement->statusList, $c['before'], '-')."</td>";
                                echo "<td class='text-center'>".zget($lang->requirement->statusList, $c['after'], '-')."</td>";
                                ?>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->FeedbackStatusTransition;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-120px'><?php echo $lang->requirement->nodeUser;?></th>
                            <!--                            <td class='text-right'>--><?php //echo $lang->requirement->consumed;?><!--</td>-->
                            <td class='text-center'><?php echo $lang->requirement->before;?></td>
                            <td class='text-center'><?php echo $lang->requirement->after;?></td>
                        </tr>
                        <?php foreach($requirement->feedBackStatusInfo as $c):?>
                            <tr>
                                <th class='w-120px'><?php echo zget($users, $c['account'], '');?></th>
                                <!--                                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                                <?php
                                echo "<td class='text-center'>".zget($lang->requirement->statusList, $c['before'], '-')."</td>";
                                echo "<td class='text-center'>".zget($lang->requirement->statusList, $c['after'], '-')."</td>";
                                ?>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
