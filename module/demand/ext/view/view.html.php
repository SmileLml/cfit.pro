<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<style>.body-modal #mainMenu > .btn-toolbar .page-title {
        width: auto;
    }</style>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if (!isonlybody()): ?>
            <?php $goback = $app->session->demandList ? $app->session->demandList : inlink('browse'); ?>
            <?php echo html::a($goback, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
            <div class="divider"></div>
        <?php endif; ?>
        <div class="page-title">
            <span class="label label-id"><?php echo $demand->code ?></span>
            <span class="text" title='<?php echo $demand->title; ?>'><?php echo $demand->title; ?></span>
        </div>
    </div>
    <?php if (!isonlybody()): ?>
        <div class="btn-toolbar pull-right">
            <?php if (common::hasPriv('demand', 'exportWord')) echo html::a($this->createLink('demand', 'exportWord', "demandID=$demand->id"), "<i class='icon-export'></i> {$lang->demand->exportWord}", '', "class='btn btn-primary'"); ?>
        </div>
    <?php endif; ?>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->desc; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->desc) ? $demand->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->reason; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->reason) ? $demand->reason : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->solution; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->solution) ? $demand->solution : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail hidden">
                <div class="detail-title"><?php echo $lang->demand->plateMakAp; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->plateMakAp) ? $demand->plateMakAp : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
<!--            迭代22取消制版申请和制版信息，只做隐藏，不做其他处理 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->plateMakInfo; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->plateMakInfo) ? $demand->plateMakInfo : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->progress; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->progress) ? $demand->progress : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>

            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->comment; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->comment) ? $demand->comment : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <?php if ($demand->files) echo $this->fetch('file', 'printFiles', array('files' => $demand->files, 'fieldset' => 'true')); ?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=demand&objectID=$demand->id"); ?>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirement->liftCycle; ?></div>
                <div class="detail-content article-content">
                    <?php if(!empty($lifeOpinionInfo['id'])):?>
                    <table class="table changeInfo">
                        <tr>
                            <th class="w-150px"><?php echo $this->lang->requirement->commonOpinion; ?></th>
                            <th class="w-150px"><?php echo $this->lang->requirement->common; ?></th>
                            <th class="w-100px"><?php echo $this->lang->requirement->commonDemand; ?></th>
                        </tr>
                        <tr>
                            <td rowspan="<?php echo $lifeOpinionInfo['countAll'];?>">
                                <?php
                                    if(!empty($lifeOpinionInfo['code'])){
                                        echo html::a($this->createLink('opinion', 'view', array('id' => $lifeOpinionInfo['id']), '', true), $lifeOpinionInfo['code'].'('.$lifeOpinionInfo['name'].')', '', "class='iframe' data-width='90%'");
                                    }else{
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <td rowspan="<?php echo $lifeOpinionInfo['requirements'][0]['count'];?>">
                                <?php  $firstRequirement = $lifeOpinionInfo['requirements'][0];
                                if(!empty($firstRequirement['code'])){
                                    echo html::a($this->createLink('requirement', 'view', array('id' => $firstRequirement['id']), '', true), $firstRequirement['code'].'('.$firstRequirement['name'].')', '', "class='iframe' data-width='90%'");
                                }else{
                                    echo '-';
                                }

                                ?>
                            </td>
                            <?php $demandZero = $lifeOpinionInfo['requirements'][0]['demands'][0]; ?>
                            <?php if(isset($demandZero->id)):?>
                                <td>
                                    <?php
                                    if($demandZero->code != $demand->code)
                                    {
                                        echo html::a($this->createLink('demand', 'view', array('id' => $demandZero->id), '', true), $demandZero->code.'('.$demandZero->title.')', '', "class='iframe' data-width='90%'");
                                    }else{
                                        echo $demandZero->code.'('.$demandZero->title.')';
                                    }
                                    ?>
                                </td>
                            <?php else:?>
                                <td style="text-align: center"><?php echo '-';?></td>
                            <?php endif;?>
                        </tr>

                        <?php foreach ($lifeOpinionInfo['requirements'] as $item => $lifeRequirement):?>
                            <?php if($item == 0):?>

                                <?php for($i = 1; $i < $lifeRequirement['count'];$i++):?>
                                    <tr>
                                        <?php if(!empty($lifeRequirement['demands'][$i]->code) && $lifeRequirement['demands'][$i]->code != $demand->code):?>
                                            <td><?php echo html::a($this->createLink('demand', 'view', array('id' => $lifeRequirement['demands'][$i]->id), '', true), $lifeRequirement['demands'][$i]->code.'('.$lifeRequirement['demands'][$i]->title.')', '', "class='iframe' data-width='90%'"); ?>
                                        <?php else:?>
                                            <td><?php echo $lifeRequirement['demands'][$i]->code.'('.$lifeRequirement['demands'][$i]->title.')'; ?></td>
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
                                                    echo html::a($this->createLink('requirement', 'view', array('id' => $lifeRequirement['id']), '', true), $lifeRequirement['code'].'('.$lifeRequirement['name'].')', '', "class='iframe' data-width='90%'");
                                                ?>
                                            </td>
                                        <?php endif;?>

                                        <?php if(isset($lifeDemand->id)):?>
                                            <td>
                                                <?php
                                                if($lifeDemand->code != $demand->code)
                                                {
                                                    echo html::a($this->createLink('demand', 'view', array('id' => $lifeDemand->id), '', true), $lifeDemand->code.'('.$lifeDemand->title.')', '', "class='iframe' data-width='90%'");
                                                }else{
                                                    echo $lifeDemand->code.'('.$lifeDemand->title.')';
                                                }
                                                ?>
                                            </td>
                                        <?php else:?>
                                            <td style="text-align: center"><?php echo '-';?></td>
                                        <?php endif;?>

                                    </tr>
                                <?php endforeach;?>
                            <?php endif;?>
                        <?php endforeach;?>

                    </table>
                    <?php else:?>
                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php if(common::hasPriv('demand', 'fieldsAboutonConlusion')):?>
        <div class="cell">
            <div class="detail-title"><?php echo $lang->demand->conclusionInfo; ?></div>
            <div class="detail" >
                <div class="detail-title"><?php echo $lang->demand->secondLineDevelopmentPlan; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->secondLineDevelopmentPlan) ? nl2br($demand->secondLineDevelopmentPlan) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail" >
                <div class="detail-title"><?php echo $lang->demand->editSpecial; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->conclusion) ? nl2br($demand->conclusion) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail" >
                <div class="detail-title"><?php echo $lang->demand->secondLineDevelopmentStatus; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->secondLineDevelopmentStatus) ? zget($lang->demand->secondLineDepStatusList,$demand->secondLineDevelopmentStatus) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail" >
                <div class="detail-title"><?php echo $lang->demand->secondLineDevelopmentApproved; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->secondLineDevelopmentApproved) ? zget($lang->demand->ifApprovedList,$demand->secondLineDevelopmentApproved) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>
            <div class="detail" >
                <div class="detail-title"><?php echo $lang->demand->secondLineDevelopmentRecord; ?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($demand->secondLineDevelopmentRecord) ? zget($lang->demand->secondLineDepRecordList,$demand->secondLineDevelopmentRecord) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                </div>
            </div>

        </div>
        <?php endif; ?>
        <!-- 审核审批意见/处理意见 -->
        <?php if (!empty($nodes)): ?>
        <div class="cell">
            <div class="detail">
                <div class="clearfix">
                    <div class="detail-title pull-left"><?php echo $lang->demand->delayreviewOpinion; ?></div>
                    <div class="detail-title pull-right">
                        <?php
                        if(common::hasPriv('demand', 'showdelayHistoryNodes')) echo html::a($this->createLink('demand', 'showdelayHistoryNodes', 'id='.$demand->id, '', true), $lang->demand->showdelayHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                        ?>
                    </div>
                </div>
                <div class="detail-content article-content">
                        <table class="table ops">
                            <tr>
                                <td class="w-180px"><?php echo $lang->demand->statusOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->demand->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->demand->reviewResult; ?></td>
                                <td style="width:370px;"><?php echo $lang->demand->dealOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->demand->reviewOpinionTime; ?></td>
                            </tr>
                            <?php foreach ($lang->demand->reviewNodeStatusList as $key => $reviewNode):
                                //$currentNode = $nodes[$key - 1];

                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
                                if (isset($nodes[$key])) {
                                    $currentNode = $nodes[$key];
                                    $reviewers = $currentNode->reviewers;
                                    if (!(is_array($reviewers) && !empty($reviewers))) {
                                        continue;
                                    }
                                    //所有审核人
                                    $reviewersArray = array_column($reviewers, 'reviewer');
                                    $userCount = count($reviewersArray);
                                    if ($userCount > 0) {
                                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                        $reviewerUserTitle = implode(',', $reviewerUsers);
                                        $subCount = 10;
                                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        //获得实际审核人
                                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                    }
                                }else{
                                    continue;
                                }

                                ?>
                                <tr>
                                    <th><?php echo zget($lang->demand->reviewNodeStatusLableList, $reviewNode); ?></th>
                                    <td title="<?php echo $reviewerUserTitle; ?>">
                                        <?php echo $reviewerUsersShow; ?>
                                    </td>
                                    <td><?php echo zget($lang->demand->reviewStatusList, $realReviewer->status, ''); ?>
                                        <?php if ($realReviewer->status == 'pass' || $realReviewer->status == 'reject'): ?>
                                            &nbsp;（<?php echo zget($users, $realReviewer->reviewer, ''); ?>）
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $realReviewer->comment ?>
                                    </td>
                                    <td><?php echo $realReviewer->reviewTime != '0000-00-00 00:00:00' ? $realReviewer->reviewTime: ''?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="cell"><?php include '../../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($this->session->demandList); ?>
                <div class='divider'></div>
                <?php if(common::hasPriv('demand', 'reviewdelay')): ?>
                <?php if($demand->status != 'deleteout' and in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                    <div class="btn-group dropup">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title="<?php echo $lang->datamanagement->readmessage ?>"><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <?php if(in_array($demand->delayStatus, array_keys($this->lang->demand->reviewNodeStatusLableList)) and in_array($this->app->user->account, explode(',', $demand->delayDealUser))): ?>
                                <li><?php echo html::a($this->createLink('demand', 'reviewdelay', 'demandID=' . $demand->id , '', true), $lang->demand->reviewdelay , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                            <?php endif;?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle disabled" data-toggle="dropdown" title="<?php echo $lang->demand->review ?>"><i class="icon icon-glasses disabled"></i></button>
                    </div>
                <?php endif; ?>
                <?php endif; ?>
                <?php
                    common::printIcon('demand', 'edit', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'button');
                    common::printIcon('demand', 'deal', "demandID=$demand->id", $demand, 'button', 'time', '', 'iframe', true);
                    common::printIcon('demand', 'copy', "demandID=$demand->id&opinionID=$demand->opinionID", $demand, 'button');
                    common::printIcon('demand', 'assignment', "demandID=$demand->id", $demand, 'button', 'hand-right', '', 'iframe', true);
                    if(common::hasPriv('demand', 'delay')){
                        //需求收集 2802 需求条目延期申请人范围限制由【创建人】改为 需求条目的【研发责任人】
                        if($this->app->user->admin or  (in_array($demand->status, array('feedbacked')) and $demand->fixType == 'second' and ($demand->delayStatus != 'toDepart' and $demand->delayStatus != 'toManager' and $demand->delayStatus != 'success') and $this->app->user->account == $demand->acceptUser)){
                            common::printIcon('demand', 'delay', "demandID=$demand->id", $demand, 'button', 'delay', '', 'iframe', true);
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->demand->delay . '"><i class="icon-common-delay disabled icon-delay"></i></button>';
                        }
                    }
                    common::printIcon('demand', 'delete', "demandID=$demand->id&requirementID=$demand->requirementID", $demand, 'button', 'trash', '', 'iframe', true);
                    common::printIcon('demand', 'close', "demandID=$demand->id", $demand, 'button', 'off', '', 'iframe', true);
                    //忽略/恢复
                    if ($demand->ignoreStatus == 0){
                        common::printIcon('demand', 'ignore', "demandID=$demand->id", $demand, 'button', 'ban', '', 'iframe', true);
                    }else{
                        common::printIcon('demand', 'recoveryed', "demandID=$demand->id", $demand, 'button', 'bell', '', 'iframe', true);
                    }
                    //挂起/激活  admin、二线专员、产品经理、创建人、后台配置的挂起角色人
                    if ($this->app->user->admin or $this->app->user->account == $demand->createdBy or in_array($this->app->user->account, $executives)) {
                        if ($demand->status == 'suspend') {
                            common::printIcon('demand', 'start', "demandID=$demand->id", $demand, 'button', 'magic', '', 'iframe', true);
                        } else {
                            common::printIcon('demand', 'suspend', "demandID=$demand->id", $demand, 'button', 'pause', '', 'iframe', true);
                        }
                    }
                    else if($demand->status == 'suspend'){
                        echo '<button type="button" class="disabled btn" title="' . $lang->demand->start . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                    }else{
                        echo '<button type="button" class="disabled btn" title="' . $lang->demand->suspend . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                    }
                    common::printIcon('demand', 'editSpecial', "demandID=$demand->id", $demand, 'button', '', '', 'iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->opinionID; ?></th>
                            <?php if ($demand->opinionID): ?>
                                <td><?php echo html::a($this->createLink('opinion', 'view', array('id' => $opinion->id), '', true), $opinion->name, '', 'class="iframe"'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->requirementID; ?></th>
                            <?php if ($demand->requirementID): ?>
                                <td><?php echo html::a($this->createLink('requirement', 'view', array('id' => $demand->requirementID), '', true), $requirementName, '', 'class="iframe"'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->app; ?></th>
                            <td>
                                <?php
                                $as = [];
                                foreach (explode(',', $demand->app) as $app) {
                                    if (!$app) continue;
                                    $as[] = zget($apps, $app,'');
                                }
                                $app = implode(', ', $as);
                                echo $app;
                                ?>
                            </td>
                        </tr>
<!--                        <tr>-->
<!--                            <th class='w-100px'>--><?php //echo $lang->demand->endDate; ?><!--</th>-->
<!--                            <td>--><?php //echo $demand->endDate; ?><!--</td>-->
<!--                        </tr>-->
                        <tr>
                            <th><?php echo $lang->demand->product; ?></th>
                            <td><?php if ($demand->product) echo $demand->product == '99999' ? '无': $product->name; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->productPlan; ?></th>
                            <td><?php if ($demand->productPlan) echo $demand->productPlan  == '1' ? '无' : $productPlan->title; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->fixType; ?></th>
                            <td><?php echo zget($lang->demand->fixTypeList, $demand->fixType, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->acceptDept; ?></th>
                            <td><?php echo zget($depts, $demand->acceptDept, ''); ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->acceptUser; ?></th>
                            <td><?php echo zget($users, $demand->acceptUser, $demand->acceptUser); ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->end; ?></th>
                            <td><?php echo $demand->end; ?></td>
                        </tr>
                        <?php $this->app->loadLang('problem');?>
                        <?php if($demand->fixType == 'second' ):?>

                        <!--是否纳入交付超期--->
                            <?php if($isOverDateInfoVisible):?>
                            <tr>
                                <th><?php echo $lang->demand->isExtended;?></th>
                                <td>
                                    <?php echo zget($this->lang->demand->isExtendedList,$demand->isExtended,'');?>
                                    <?php echo html::a($this->createLink('demand', 'isExtended', "demandID=$demand->id", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                                </td>
                            </tr>
                            <?php endif;?>
                        <?php endif;?>

                        <?php if($demand->fixType == 'second' ):?>
<!--                            <tr>-->
<!--                                <th>--><?php //echo $lang->demand->overDate;?><!--</th>-->
<!--                                <td>--><?php //echo $overDate; ?><!--</td>-->
<!--                            </tr>-->
                         <!--交付是否超期-->
                            <?php if($isOverDateInfoVisible):?>
                                <tr>
                                    <th><?php echo $lang->demand->deliveryOver;?></th>
                                    <td> <?php echo zget($this->lang->demand->deliveryOverList, $demand->deliveryOver,'');?></td>
                                </tr>
                            <?php endif;?>

                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->demand->solvedTime; ?></th>
                            <td><?php echo strpos($demand->solvedTime,'0000-00-00') === false ? $demand->solvedTime : ''; ?></td>
                        </tr>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->demandOnlineDate; ?></th>
                            <td><?php echo $demand->actualOnlineDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->relationModify; ?></th>
                            <td>
                                <?php if(isset($objects['modify'])):?>
                                <?php foreach ($objects['modify'] as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('modify', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                                <?php endif;?>
                                <?php if(isset($objects['modifycncc'])):?>
                                <?php foreach ($objects['modifycncc'] as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                                <?php endif;?>
                            </td>
                        </tr>
                        <?php if(isset($objects['credit']) && !empty($objects['credit'])):?>
                        <tr>
                            <th><?php echo $lang->demand->relationCredit; ?></th>
                            <td>
                                <?php foreach ($objects['credit'] as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('credit', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>

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
                <div class="detail-title"><?php echo $lang->demand->status;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th><?php echo $lang->demand->status; ?></th>
                            <td><?php echo zget($lang->demand->statusList, $demand->status, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->dealUser;?></th>
                            <td>
                                <?php echo  zmget($users, $demand->dealUser, ''); ?>
                            </td>
                        </tr>
                        <?php if($demand->mailto != ''):?>
                            <tr>
                                <th><?php echo $lang->demand->mailto;?></th>
                                <td><?php foreach(explode(',', $demand->mailto) as $user) echo zget($users, $user, '') . ' '; ?></td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->demand->createdBy; ?></th>
                            <td><?php echo zget($users, $demand->createdBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->createdDate; ?></th>
                            <td><?php echo $demand->createdDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->closedBy; ?></th>
                            <td><?php echo zget($users, $demand->closedBy, ''); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->closedDate; ?></th>
                            <td><?php echo $demand->closedDate; ?></td>
                        </tr>
                        <?php if(common::hasPriv('demand', 'updateStatusLinkage') || $this->app->user->account == 'admin') :?>
                        <tr>
                            <th><?php echo $lang->demand->secureStatusLinkage;?></th>
                            <td>
                                <?php echo zget($this->lang->demand->secureStatusLinkageList,$demand->secureStatusLinkage,'');?>
                                <?php echo  common::printIcon('demand', 'updateStatusLinkage', "demandID=$demand->id", $demand, 'list','edit','','iframe',true) ;?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->demand->lockStatus;?></th>
                            <td>
                                <?php echo zget($this->lang->demand->lockStatusList,$demand->changeLock,'');?>
                                <?php if(((common::hasPriv('demand', 'unlockSeparate') && in_array($this->app->user->account,$unLock)) || $this->app->user->account == 'admin') && $demand->changeLock == 2 ) :?>
                                    <?php echo  common::printIcon('demand', 'unlockseparate', "demandID=$demand->id", $demand, 'list','edit','','iframe',true) ;?>
                                <?php endif;?>
                            </td>
                        </tr>
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
                        <!--由于若干个版本存库字段都是projectPlan,固使用projectPlan字段-->
                        <tr>
                            <th><?php echo $lang->opinion->project; ?></th>
                            <td>
<!--                                --><?php //if($demand->project) {
//                                    $array = explode(',', $demand->project);
//                                    foreach ($array as $pid){
//                                        if(empty($pid)) continue;
//                                        /*                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $pid), $plan[$pid], '', " style='color: #0c60e1;'");*/
//                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectplanid), $plan->name, '', "data-app='platform' style='color: #0c60e1;'");
//                                        echo "<br>";
//                                    }
//                                }
//                                ?>
                                <?php if($plans) {
                                    foreach ($plans as $plan){
//                                        if(empty($pid)) continue;
                                        /*                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $pid), $plan[$pid], '', " style='color: #0c60e1;'");*/
                                        echo html::a($this->createLink('projectplan', 'view', 'id=' . $plan->id), $plan->name, '', "data-app='platform' style='color: #0c60e1;'");
                                        echo "<br>";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->task;?></th>
                            <td><?php if($task)  echo html::a('javascript:void(0)', $task->taskName, '', 'data-app="project" onclick="seturl('.$demand->project.','.$task->id.')"')?></td>
                            <td class="hidden"><?php echo html::a('','','','data-app="project"  id="demandtaskurl"')?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->stage;?></th>
                            <td><?php echo zget($executions,$demand->execution,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->buildName;?></th>
                            <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->buildname)  echo html::a('javascript:void(0)', $buildAndRelease->buildname, '', 'data-app="project" onclick="newurl('.$demand->project.','.$buildAndRelease->bid.')"')?></td>
                            <td class="hidden"><?php echo html::a('','','','data-app="project"  id="demandbuildurl"')?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->demand->releaseName;?></th>
                            <td><?php if(isset($buildAndRelease->buildname) && $buildAndRelease->rid)  echo html::a('javascript:void(0)', $buildAndRelease->releasename, '', 'data-app="project" onclick="addurl('.$demand->project.','.$buildAndRelease->rid.')"')?></td>
                            <td class="hidden"><?php echo html::a('','','','data-app="project"  id="demandreleaseurl"')?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if(!empty($demand->delayResolutionDate)):?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->demand->delayInfo;?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->demand->delayDealuser; ?></th>
                                <td><?php echo zmget($users, $demand->delayDealUser, ''); ?></td>
                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->demand->originalResolutionDate; ?></th>
                                <td><?php echo date('Y-m-d', strtotime($demand->originalResolutionDate)); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->demand->delayResolutionDate;?></th>
                                <td><?php echo date('Y-m-d', strtotime($demand->delayResolutionDate)); ?></td>
                            </tr>
                            <!--<tr>
                                <th><?php /*echo $lang->demand->unitAgree;*/?></th>
                                <td><?php /*echo zget($lang->demand->unitAgreeList,$demand->unitAgree,'');*/?></td>
                            </tr>-->
                            <tr>
                                <th><?php echo $lang->demand->delayReason;?></th>
                                <td><?php echo  $demand->delayReason;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->demand->delayUser;?></th>
                                <td><?php echo zget($users,$demand->delayUser,'');?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->demand->delayDate;?></th>
                                <td><?php echo $demand->delayDate;?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->demand->delayStatus;?></th>
                                <td><?php echo zget($lang->demand->delayStatusList,$demand->delayStatus,'');?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->consumedTitle; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->nodeUser; ?></th>
<!--                            <td class='text-right'>--><?php //echo $lang->demand->consumed; ?><!--</td>-->
                            <td class='text-center'><?php echo $lang->demand->before; ?></td>
                            <td class='text-center'><?php echo $lang->demand->after; ?></td>
<!--                            <td class='text-left'>--><?php //echo $lang->actions; ?><!--</td>-->
                        </tr>
                        <?php foreach ($demand->consumed as $index => $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
<!--                                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour; ?><!--</td>-->
                                <td class='text-center'><?php echo zget($lang->demand->statusConsumedList, $c->before, '-'); ?></td>
                                <td class='text-center'><?php echo zget($lang->demand->statusConsumedList, $c->after, '-'); ?></td>
<!--                                <td class='c-actions text-left'>-->
<!--                                    --><?php
//                                    common::printIcon('demand', 'workloadEdit', "demandID={$demand->id}&consumedid={$c->id}", $demand, 'list', 'edit', '', 'iframe', true);
//                                    if ($index) common::printIcon('demand', 'workloadDelete', "demandID=$demand->id&consumedid={$c->id}", $demand, 'list', 'trash', '', 'iframe', true);
//                                    common::printIcon('demand', 'workloadDetails', "demandID={$demand->id}&consumedid={$c->id}", $demand, 'list', 'glasses', '', 'iframe', true);
//                                    ?>
<!--                                </td>-->
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if(!empty($demand->delayResolutionDate)):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->demand->delay.'-'.$lang->consumedTitle; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->demand->nodeUser; ?></th>
                            <td class='text-center'><?php echo $lang->demand->before; ?></td>
                            <td class='text-center'><?php echo $lang->demand->after; ?></td>
                        </tr>
                        <?php foreach ($demand->delayConsumed as $index => $c): ?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, ''); ?></th>
                                <td class='text-center'><?php echo zget($lang->demand->statusConsumedList, $c->before, '-'); ?></td>
                                <td class='text-center'><?php echo zget($lang->demand->statusConsumedList, $c->after, '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif;?>

    </div>
</div>
<?php include '../../../common/view/footer.html.php'; ?>
