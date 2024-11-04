<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php $browseLink = $app->session->infoQzList != false ? $app->session->infoQzList : inlink('gain');?>
            <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $info->code?></span>
        </div>
    </div>
    <?php if(!isonlybody()):?>
        <div class="btn-toolbar pull-right">
            <?php if(common::hasPriv('infoqz', 'exportWord')) echo html::a($this->createLink('infoqz', 'exportWord', "infoID=$info->id"), "<i class='icon-export'></i> {$lang->infoqz->exportWord}", '', "class='btn btn-primary'");?>
        </div>
    <?php endif;?>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $action == 'gain' ? $lang->infoqz->gainDesc : $lang->infoqz->fixDesc;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->desc) ? html_entity_decode(str_replace("\n","<br/>",$info->desc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $action == 'gain' ? $lang->infoqz->gainReason : $lang->infoqz->fixReason;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->reason) ? html_entity_decode(str_replace("\n","<br/>",$info->reason)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <?php if($action == 'gain'):?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->infoqz->gainPurpose;?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($info->purpose) ? html_entity_decode(str_replace("\n","<br/>",$info->purpose)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                    </div>
                </div>
            <?php endif;?>
            <?php if($action == 'fix'):?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->infoqz->operation;?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($info->operation) ? html_entity_decode(str_replace("\n","<br/>",$info->operation)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                    </div>
                </div>
            <?php endif;?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->infoqz->test;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->test) ? html_entity_decode(str_replace("\n","<br/>",$info->test)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->infoqz->content;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->content) ? html_entity_decode(str_replace("\n","<br/>",$info->content)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->infoqz->operation;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->operation) ? html_entity_decode(str_replace("\n","<br/>",$info->operation)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $action == 'gain' ? $lang->infoqz->gainStep : $lang->infoqz->fixStep;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->step) ? html_entity_decode(str_replace("\n","<br/>",$info->step)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->infoqz->desensitization;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->desensitization) ? html_entity_decode(str_replace("\n","<br/>",$info->desensitization)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->infoqz->externalRejectReason;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($info->externalRejectReason) ? $info->externalRejectReason : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $action == 'gain' ? $lang->infoqz->gainResult : $lang->infoqz->fixResult;?></div>
                <div class="detail-content article-content">
                    <?php echo zget(array_flip($this->lang->infoqz->externalStatusMapArray), $info->externalStatus, '') ?>
                </div>
            </div>
            <?php if($info->isDesensitize == 1):?>
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->infoqz->desensitizeProcess;?></div>
                    <div class="detail-content article-content">
                        <?php echo !empty($info->desensitizeProcess) ? html_entity_decode(str_replace("\n","<br/>",$info->desensitizeProcess)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                    </div>
                </div>
            <?php endif;?>
            <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=infoQz&objectID=$info->id");?>
        </div>
        <?php if(!empty($nodes)):?>
            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->infoqz->reviewComment;?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('infoqz', 'showHistoryNodes')) echo html::a($this->createLink('infoqz', 'showHistoryNodes', 'id='.$info->id, '', true), $lang->infoqz->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                            ?>
                        </div>
                    </div>
                    <div class="detail-content article-content">
                        <?php if(!empty($nodes)):?>
                        <table class="table ops">
                            <tr>
                                <th class="w-200px"><?php echo $lang->infoqz->reviewNode;?></th>
                                <td class="w-200px"><?php echo $lang->infoqz->reviewer;?></td>
                                <td class="w-200px"><?php echo $lang->infoqz->reviewResult;?></td>
                                <td style="width:370px"><?php echo $lang->infoqz->reviewComment;?></td>
                            </tr>
                            <?php
                            if ($info->createdDate > "2024-04-02 23:59:59"){
                                unset($this->lang->infoqz->reviewerList[3]);
                            }
                            foreach ($lang->infoqz->reviewerList as $key => $reviewNode):
                                if($key=='4') {
                                    continue;
                                }
                                else{
                                    $reviewerUserTitle = '';
                                    $reviewerUsersShow = '';
                                    $realReviewer = new stdClass();
                                    $realReviewer->status = '';
                                    $realReviewer->comment = '';
                                    if(isset($nodes[$key])){
                                        $currentNode = $nodes[$key];
                                        $reviewers = $currentNode->reviewers;
                                        if(!(is_array($reviewers) && !empty($reviewers))) {
                                            continue;
                                        }
                                        //所有审核人
                                        $reviewersArray = array_column($reviewers, 'reviewer');
                                        $userCount = count($reviewersArray);
                                        if($userCount > 0) {
                                            $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                                            $reviewerUserTitle = implode(',', $reviewerUsers);
                                            $subCount = 3;
                                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                            //获得实际审核人
                                            $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                        }

                                    }
                                }
                                ?>
                                <tr>
                                    <th class="w-30px"><?php echo $reviewNode;?></th>
                                    <td title="<?php echo $reviewerUserTitle; ?>" class="w-30px">
                                        <?php echo $reviewerUsersShow; ?>
                                    </td>
                                    <td class="w-30px">
                                        <?php echo zget($lang->infoqz->confirmResultList, $realReviewer->status, '');?>
                                        <?php
                                        if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                            ?>
                                            &nbsp;（<?php echo zget($users, $realReviewer->reviewer, '');?>）
                                        <?php endif; ?>
                                    </td>
                                    <td class="w-80px"><?php echo in_array($key, [0,1,6]) && '不用审批' == $realReviewer->comment ? '不用处理' : $realReviewer->comment; ?></td>
                                </tr>
                            <?php endforeach;?>
                            <?php $info->reviewFailReason = json_decode($info->reviewFailReason, true);
                            if(!empty($info->reviewFailReason)):
                                $flag = in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'));
                                $count = count($info->reviewFailReason[$info->version]);
                                foreach ($info->reviewFailReason[$info->version] as $key => $reasons):
                                    if($flag && $key == $count - 1){
                                        continue;
                                    }
                                    foreach ($reasons as $k => $reason):
                                        $node = $reason['reviewNode'];
                                            ?>
                                            <tr>
                                                <th><?php echo $lang->infoqz->$node; ?></th>
                                                <td><?php echo zget($users, $reason['reviewUser'], ','); ?></td>
                                                <td><?php echo $reason['reviewResult'] ?></td>
                                                <td><?php echo $reason['reviewFailReason'] ?></td>
                                            </tr>
                            <?php endforeach; endforeach; endif; ?>
                            <tr>
                                <th><?php echo $lang->infoqz->guestjk;?></th>
                                <td>
                                    <?php echo zget($users,'guestjk',','); ?>
                                </td>
                                <td>
                                    <?php
                                    if(in_array($info->status,array('pass','qingzongsynfailed')) ){
                                        echo zget($lang->infoqz->statusList, $info->status, '');
                                    }
                                    elseif(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'))){
                                        echo $lang->infoqz->synSuccess;
                                    }
                                    else{
                                        echo '';
                                    }?>
                                </td>
                                <td>
                                    <?php if(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'))){
                                        echo $lang->infoqz->synSuccessMsg;
                                    }
                                    elseif($info->status=='qingzongsynfailed'){
                                        echo $info->synFailedReason;
                                    }?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->infoqz->guestcn;?></th>
                                <td>
                                    <?php echo zget($users,'guestcn',','); ?>
                                </td>
                                <td>
                                    <?php
                                    if(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel'))){
                                        echo zget($lang->infoqz->statusList,$info->status);
                                    }
                                    else{
                                        echo '';
                                    }?>
                                </td>
                                <td>
                                    <?php if(in_array($info->status,array('withexternalapproval','fetchsuccess','fetchfail','outreject','fetchsuccesspart','fetchcancel')))
                                    {     
                                        echo "打回人：".$info->approverName."<br>"."审批意见：".$info->externalRejectReason;
                                    }else{
                                        echo '';
                                    } ?>
                                </td>
                            </tr>

                            <?php else:?>
                                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                            <?php endif;?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <div class="cell"><?php include '../../common/view/action.html.php';?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($browseLink);?>
                <div class='divider'></div>
                <?php
                common::printIcon('infoqz', 'edit', "infoID=$info->id", $info, 'button');
                common::printIcon('infoqz', 'reject', "infoID=$info->id", $info, 'button', 'arrow-left', '', 'iframe', true, '', $this->lang->infoqz->reject);
                common::printIcon('infoqz', 'link', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'button', 'link', '', 'iframe', true);
                common::printIcon('infoqz', 'review', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'button', 'glasses', '', 'iframe', true);
                // common::printIcon('infoqz', 'run', "infoID=$info->id", $info, 'button', 'play', '', 'iframe', true);
                common::printIcon('infoqz', 'delete', "infoID=$info->id", $info, 'button', 'trash', '', 'iframe', true);

                $pushUser = ['admin'];
                $secondNode = $nodes[6] ?? '';
                $reviewers = $secondNode->reviewers ?? '';
                if(is_array($reviewers) && !empty($reviewers)) {
                    $realReviewer = $this->loadModel('review')->getRealReviewerInfo($secondNode->status, $reviewers);
                    $pushUser[] = $realReviewer->reviewer;
                }
                if('qingzongsynfailed' == $info->status && in_array($this->app->user->account, $pushUser)){
//                    common::hasPriv('infoqz','push') ? common::printIcon(
//                            'infoqz',
//                            'push',
//                            "infoID=$info->id",
//                            $info,
//                            'button',
//                            'share',
//                            'hiddenwin'
//                    ) : '';
                }
                ?>
            </div>
        </div>
    </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->infoqz->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->status;?></th>
                <td><?php echo zget($lang->infoqz->statusList, $info->status, '');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->dealUser;?></th>
                <?php
                $dealUsersStr = '';
                $dealUsersSubStr = '';
                $dealUsers = $info->dealUsers;
                if($dealUsers){
                    $dealUsersArray = explode(',', $dealUsers);
                    //所有审核人
                    $dealUsers    = getArrayValuesByKeys($users, $dealUsersArray);
                    $dealUsersStr = implode(',', $dealUsers);
                    $subCount = 3;
                    $dealUsersSubStr = getArraySubValuesStr($dealUsers, $subCount);
                }
                ?>
                <td title="<?php echo $dealUsersStr; ?>">
                    <?php echo $dealUsersSubStr ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->externalId;?></th>
                <td><?php echo $info->externalId;?></td>
            </tr>

            <tr>
                <th><?php echo $lang->infoqz->externalStatus;?></th>
                <td><?php echo zget($lang->infoqz->externalStatusList, $info->externalStatus, '');?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->type;?></th>
                <td><?php echo zget($lang->infoqz->typeList, $info->type, '');?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->classify;?></th>
                <td>
                    <?php
                    $classifyList = explode(',', $info->classify);
                    foreach($classifyList as $classify)
                    {
                        echo '<p>' . zget($lang->infoqz->businessList+$lang->infoqz->techList, $classify, '') . '</p>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->dataCollectApplyCompany;?></th>
                <td><?php echo zget($lang->infoqz->demandUnitTypeList, $info->dataCollectApplyCompany, '');?></td>
            </tr>
            <tr>
                <?php
                    if (in_array($info->dataCollectApplyCompany,[1,2,3])){
                        $arr = [];
                        foreach (explode(',',$info->demandUnitOrDep) as $item) {
                            $arr[] = zget($demandUnitDeptList,$item,'');
                        }
                        $info->demandUnitOrDep = implode(',',$arr);
                    }
                ?>
                <th class='w-120px'><?php echo $lang->infoqz->demandUnitOrDep;?></th>
                <td><?php echo $info->demandUnitOrDep;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->demandUser;?></th>
                <td><?php echo $info->demandUser;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->demandUserPhone;?></th>
                <td><?php echo $info->demandUserPhone;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->demandUserEmail;?></th>
                <td><?php echo $info->demandUserEmail;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->portUser;?></th>
                <td><?php echo $info->portUser;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->portUserPhone;?></th>
                <td><?php echo $info->portUserPhone;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->portUserEmail;?></th>
                <td><?php echo $info->portUserEmail;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->supportUser;?></th>
                <td><?php echo $info->supportUser;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->supportUserPhone;?></th>
                <td><?php echo $info->supportUserPhone;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->supportUserEmail;?></th>
                <td><?php echo $info->supportUserEmail;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->isNPC;?></th>
                <td><?php echo zget( $lang->infoqz->isNPCList, $info->isNPC, '');?></td>
            </tr>
            <tr>
                <th class='w-100px'><?php echo $info->action == 'gain' ? $lang->infoqz->gainNode : $lang->infoqz->fixNode;?></th>
                <td>
                    <?php
                    if($info->node)
                    {
                        $as = array();
                        foreach(explode(',', $info->node) as $nodeID)
                        {
                            if(!$nodeID) continue;
                            $as[] = zget($this->lang->infoqz->gainNodeNPCList + $this->lang->infoqz->gainNodeCNCCList, $nodeID, $nodeID);
                        }
                        echo implode(',', $as);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->gainType;?></th>
                <td><?php echo zget($lang->infoqz->gainTypeList, $info->gainType, '');?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->createUserPhone;?></th>
                <td><?php echo $info->createUserPhone;?></td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->createdDept;?></th>
                <td><?php echo zget($depts, $info->createdDept, '');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->planBegin;?></th>
                <td><?php echo $info->planBegin;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->planEnd;?></th>
                <td><?php echo $info->planEnd;?></td>
            </tr>
            <tr>
                <th class='w-100px'><?php echo $lang->infoqz->actualBegin;?></th>
                <td><?php echo $info->actualBegin;?></td>
            </tr>
            <tr>
                <th class='w-100px'><?php echo $lang->infoqz->actualEnd;?></th>
                <td><?php echo $info->actualEnd;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->appAbbr;?></th>
                <td>
                    <?php echo $info->app;?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->app;?></th>
                <td>
                    <?php
                     $as = [];
                     foreach(explode(',', $info->app) as $app)
                     {
                         if(!$app) continue;
                         $as[] = zget($apps, $app , "");
                     }
                     $app = implode(',', $as);
                     echo $app;
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->dataSystemAbbr;?></th>
                <td>
                    <?php echo $info->dataSystem;?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->dataSystem;?></th>
                <td>
                    <?php
                    $as = [];
                    foreach(explode(',', $info->dataSystem) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app , "");
                    }
                    $app = implode(',', $as);
                    echo $app;
                    ?>
                </td>
            </tr>
            <!-- <tr>
                <th><?php echo $lang->infoqz->isPayment;?></th>
                <td>
                    <?php
                    $as = [];
                    foreach(explode(',', $info->app) as $app)
                    {
                        if(!$app) continue;
                        $as[] = zget($apps, $app, "",zget($lang->application->isPaymentList, $apps[$app]->isPayment, ''));
                    }
                    $applicationtype = implode(',', $as);
                    echo $applicationtype;
                    ?>
                </td>
            </tr> -->
            <!-- <tr>
                <th><?php echo $lang->infoqz->team;?></th>
                <td><?php echo $info->appTeam;?></td>
            </tr> -->
            <tr>
                <th class='w-120px'><?php echo $lang->infoqz->systemType;?></th>
                <td><?php echo zget($lang->infoqz->systemTypeList, $info->systemType,'');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->fixType;?></th>
                <td><?php echo zget($lang->infoqz->fixTypeList, $info->fixType, '');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->project;?></th>
                <td><?php foreach(explode(',', $info->project) as $project) echo '<p>' . zget($projects, $project, '') . '</p>'; ?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->problem;?></th>
                <td>
                    <?php
                        if(!empty($objects['problem'])):
                        foreach($objects['problem'] as $objectID => $object):
                    ?>
                        <p><?php echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                    <?php
                        endforeach;
                        endif;
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->demand;?></th>
                <td>
                    <?php foreach($objects['demand'] as $objectID => $object):?>
                        <?php if($object->sourceDemand == 1){
                            echo html::a($this->createLink('demand', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                        }else{
                            echo html::a($this->createLink('demandinside', 'view', 'id=' . $objectID, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                        } ?>
                    <?php endforeach;?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->secondorderId;?></th>
                <td>
                    <?php foreach($objects['secondorder'] as $objectID => $object):?>
                        <p><?php echo html::a($this->createLink('secondorder', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                    <?php endforeach;?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->isTest;?></th>
                <td><?php echo zget($lang->infoqz->isTestList, $info->isTest, '');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->createdBy;?></th>
                <td><?php echo zget($users, $info->createdBy, '');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->createdDate;?></th>
                <td><?php echo $info->createdDate;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->editedBy;?></th>
                <td><?php echo zget($users, $info->editedBy, '');?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->editedDate;?></th>
                <td><?php echo $info->editedDate;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->supply;?></th>
                <?php if(!empty($info->supply)):?>
                    <td><?php foreach(explode(',', $info->supply) as $supply) echo '<p>' . zget($users, $supply) . '</p>'; ?></td>
                <?php else:?>
                    <td></td>
                <?php endif;?>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->qingzongsynfailed;?></th>
                <td><?php echo $info->synFailedReason;?></td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->revertReason; ?></th>
                <td>
                    <?php
                    if($info->revertReason){
                        foreach(json_decode($info->revertReason) as $item){
                            echo $item->RevertDate.' '.zget($lang->infoqz->revertReasonList, $item->RevertReason, '');
                            echo '<br/>';
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->infoqz->revertReasonChild; ?></th>
                <td>
                    <?php
                    if($info->revertReason){
                        $childTypeList = isset($this->lang->infoqz->childTypeList) ? $this->lang->infoqz->childTypeList['all'] : '[]';
                        $childTypeList = json_decode($childTypeList, true);
                        foreach(json_decode($info->revertReason) as $item){
                            if (isset($item->RevertReasonChild) && $item->RevertReasonChild != ''){
                                echo $item->RevertDate.' '.$childTypeList[$item->RevertReason][$item->RevertReasonChild];
                            }
                            echo '<br/>';
                        }
                    }
                    ?>
                </td>
            </tr>
            <?php if(isset($this->lang->infoqz->cancelLinkageUserList[$this->app->user->account]) || $this->app->user->account == 'admin'):?>
                <tr>
                    <th><?php echo $lang->infoqz->problemCancelLinkage;?></th>
                    <td>
                        <?php echo zget($this->lang->infoqz->cancelLinkageList,$info->problemCancelLinkage,'');?>
                        <?php echo html::a($this->createLink('infoqz', 'cancelLinkage', "infoId=$info->id&type=problemCancelLinkage", '', true), "<i class='icon-edit'></i>", '', "data-toggle='modal' data-type='iframe' class='btn iframe'");?>
                    </td>
                </tr>
            <?php endif;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php foreach($info->releases as $release):?>
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->infoqz->release;?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->release->name;?></th>
                                <td colspan="3"><?php echo html::a($this->createLink('projectrelease', 'view', array('releaseID' => $release->id)), $release->name, '', 'data-app="project"'); ?></td>

                            </tr>
                            <tr>
                                <th class='w-100px'><?php echo $lang->infoqz->path;?></th>
                                <td colspan="3"><?php if($release->path) echo $release->path. $lang->api->sftpList['info'];?></td>
                            </tr>
                            <?php if(isset($releasePushLogs[$release->id])){
                                $logNum = 0;
                                foreach ($releasePushLogs[$release->id] as $pushlog) {
                                    $logNum++;
                                    ?>
                                    <tr>
                                        <th class='w-100px'><?php echo $lang->release->pushTime; ?></th>
                                        <td><?php echo  $pushlog->pushTime; ?></td>
                                        <th class='w-100px'><?php echo $lang->release->pushStatus; ?></td>
                                        <td><span ><?php echo $pushStatus = in_array($pushlog->pushStatus,[0,1]) ? '未推送' : ($pushlog->pushStatus == 3 ? "成功" : "失败"); ?></span></td>
                                    </tr>
                                    <?php if($pushStatus == '失败'){?>
                                        <tr>
                                            <th class='w-100px'><?php echo $lang->release->failReason; ?></th>
                                            <td colspan=><?php echo $lang->release->pushStatusList[$pushlog->pushStatus]; ?></td>
                                            <th class='w-100px'><?php echo $lang->release->pushTimes; ?></th>
                                            <td><span>第<?php echo $logNum; ?>次</span></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            if(in_array($releaseInfoList[$release->id]->pushStatusQz,[0,1,2])) { //没有发送记录
                                ?>
                                <th class='w-100px'><?php echo $lang->release->pushTime; ?></th>
                                <td><?php echo $releaseInfoList[$release->id]->pushTimeQz; ?></td>
                                <td class='w-100px'  style="color: #838a9d;"><?php echo $lang->release->pushStatus; ?></td>
                                <td><span><?php echo $pushStatus = in_array($releaseInfoList[$release->id]->pushStatusQz,[0,1]) ? '未推送' : "推送中"; ?></span></td>
                                <?php
                            }
                            ?>
                            <tr>
                                <th><?php echo $lang->file->common;?></th>
                                <td colspan="3">
                                    <div class='detail'>
                                        <div class='detail-content article-content'>
                                            <?php echo $this->fetch('file', 'printFiles', array('files' => $release->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false));?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <?php endforeach;?>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->infoqz->dataMasking?></div>
        <div class="detail-content">
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-120px'><?php echo $lang->infoqz->isJinke; ?></th>
                <td><?php echo zget($lang->infoqz->isJinkeList, $info->isJinke); ?></td>
              </tr>
              <?php if($info->isJinke =='1'):?>
              <tr>
                <th><?php echo $lang->infoqz->desensitizationType; ?></th>
                <td><?php echo zget($lang->infoqz->desensitizationTypeList, $info->desensitizationType); ?></td>
              </tr>
              <tr>
                <th><?php echo $lang->infoqz->deadline; ?></th>
                <td>
                  <?php
                  if($info->isDeadline == '1'){
                    echo '长期';
                  }else{
                    echo substr($info->deadline,0,10);
                  }
                  ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->infoqz->dataManagementCode; ?></th>           
                <td><?php echo  $info->dataManagementCode ? html::a($this->createLink('dataManagement', 'view', 'id=' . $info->dataManagementID, '', true), $info->dataManagementCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"):'';?></td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->infoqz->consumedTitle;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->infoqz->nodeUser;?></th>
             <!--   <td class='text-right'><?php /*echo $lang->infoqz->consumed;*/?></td>-->
                <td class='text-center'><?php echo $lang->infoqz->before;?></td>
                <td class='text-center'><?php echo $lang->infoqz->after;?></td>
              </tr>
              <?php foreach($info->consumed as $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
              <!--  <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                <td class='text-center'><?php echo zget($lang->infoqz->statusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->infoqz->statusList, $c->after, '-');?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
