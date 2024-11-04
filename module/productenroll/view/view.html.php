<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php $browseLink = $app->session->productenrollList != false ? $app->session->productenrollList : inlink('browse');?>
            <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $productenroll->code?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productenroll->productenrollDesc;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productenroll->productenrollDesc) ? $productenroll->productenrollDesc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productenroll->reasonFromJinke;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productenroll->reasonFromJinke) ? $productenroll->reasonFromJinke: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productenroll->introductionToFunctionsAndUses;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productenroll->introductionToFunctionsAndUses) ? html_entity_decode(str_replace("\n","<br/>",$productenroll->introductionToFunctionsAndUses)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productenroll->remark;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productenroll->remark) ? html_entity_decode(str_replace("\n","<br/>",$productenroll->remark)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productenroll->mediaInfo;?></div>
                <?php if ($productenroll->mediaInfo):?>
                    <div class="detail-content article-content">
                        <table class="table ops">
                            <tr>
                                <th class="w-200px"><?php echo $lang->productenroll->num;?></th>
                                <td><?php echo $lang->productenroll->media;?></td>
                                <td><?php echo $lang->productenroll->mediaBytes;?></td>
                            </tr>
                            <?php $num=1; foreach ($productenroll->mediaInfo as $MB):?>
                                <tr>
                                    <th><?php echo $num;?></th>
                                    <td >
                                        <?php echo $MB['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $MB['bytes']; $num=$num+1?>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </table>
                    </div>
                <?php else:?>
                    <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                <?php endif;?>
            </div>
        </div>
        <div class="cell">
          <div class="detail">
              <div class="clearfix">
                  <div class="detail-title pull-left"><?php echo $lang->outwarddelivery->reviewOpinion; ?></div>
                  <div class="detail-title pull-right">
                      <?php
                      if(common::hasPriv('productenroll', 'showHistoryNodes')) echo html::a($this->createLink('productenroll', 'showHistoryNodes', 'id='.$parentId, '', true), $lang->outwarddelivery->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                      ?>
                  </div>
              </div>
                    <div class="detail-content article-content">
                        <?php if(!empty($nodes)):?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->outwarddelivery->reviewNode;?></th>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewer;?></td>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewResult;?></td>
                                <td style="width:370px"><?php echo $lang->outwarddelivery->reviewOpinion;?></td>
                                <td class="w-180px"><?php echo $lang->outwarddelivery->reviewTime;?></td>
                            </tr>
                            <?php
                            unset($lang->outwarddelivery->reviewNodeList[3]);
                            unset($lang->outwarddelivery->reviewNodeList[5]);
                            unset($lang->outwarddelivery->reviewNodeList[6]);
                            //循环数据
                            foreach ($lang->outwarddelivery->reviewNodeList as $key => $reviewNode):
                                $reviewerUserTitle = '';
                                $reviewerUsersShow = '';
                                $realReviewer = new stdClass();
                                $realReviewer->status = '';
                                $realReviewer->comment = '';
                                if(isset($nodes[$key])) {
                                    $currentNode = $nodes[$key];
                                    $reviewers = $currentNode->reviewers;
                                    if(!(is_array($reviewers) && !empty($reviewers))) {
                                        continue;
                                    }
                                    //所有审核人
                                    $reviewersArray = array_column($reviewers, 'reviewer');
                                    $reviewersArrayNew = $this->loadModel('common')->getAuthorizer('outwarddelivery', implode(',', $reviewersArray), $lang->outwarddelivery->reviewBeforeStatusList[$key], $lang->outwarddelivery->authorizeStatusList);
                                    $reviewersArray = explode(',', $reviewersArrayNew);
                                    $userCount = count($reviewersArray);
                                    if ($userCount > 0) {
                                        $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                        $reviewerUserTitle = implode(',', $reviewerUsers);
                                        $subCount = 10;
                                        $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        //获得实际审核人
                                        $realReviewer = $this->loadModel('review')->getRealReviewerInfo($currentNode->status, $reviewers);
                                        $extra = json_decode($realReviewer->extra);
                                        if(!empty($extra->reviewerList)){
                                            $reviewersArray = $extra->reviewerList;
                                            $reviewerUsers = getArrayValuesByKeys($users, $reviewersArray);
                                            $reviewerUserTitle = implode(',', $reviewerUsers);
                                            $subCount = 10;
                                            $reviewerUsersShow = getArraySubValuesStr($reviewerUsers, $subCount);
                                        }
                                    }
                                }
                                if ($key==4 and (! in_array($realReviewer->status,['pass','reject']))) { continue; }
                                ?>

                                <tr>
                                    <th><?php echo $reviewNode;?></th>
                                    <td title="<?php echo $reviewerUserTitle; ?>">
                                        <?php echo $reviewerUsersShow; ?>
                                    </td>
                                    <td>
                                        <?php if($productenroll->status!='waitsubmitted'):?>
                                            <?php
                                            if($realReviewer->status == 'ignore'){
                                                if($key != 3){
                                                    echo '本次跳过';
                                                    $realReviewer->comment = '已审批通过';
                                                }else{
                                                    echo '无需审批';
                                                    $realReviewer->comment = '';
                                                }
                                            }else{
                                                echo zget($lang->outwarddelivery->confirmResultList, $realReviewer->status, '');
                                            }
                                            ?>
                                            <?php
                                            if($realReviewer->status == 'pass' || $realReviewer->status == 'reject'):
                                                ?>
                                                &nbsp;（
                                                <?php $extra = json_decode($realReviewer->extra);
                                                if(!empty($extra->proxy)){
                                                    echo zget($users, $extra->proxy, '')."处理";
                                                    echo "【".zget($users, $realReviewer->reviewer)."授权】";
                                                }else{
                                                    echo zget($users, $realReviewer->reviewer, '');
                                                }?>）
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $realReviewer->comment; ?></td>
                                    <td><?php echo $realReviewer->reviewTime; ?></td>
                                </tr>
                            <?php endforeach; $outwarddelivery->reviewFailReason = json_decode($outwarddelivery->reviewFailReason, true); ?>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->outerReviewNodeList['2'];?></th>
                                <td>
                                    <?php echo zget($users,'guestjk',','); ?>
                                </td>
                                <td>
                                    <?php
                                    if(in_array($productenroll->status,array('waitqingzong','qingzongsynfailed')) ){
                                        echo zget($lang->productenroll->statusList, $productenroll->status, '');
                                    }
                                    elseif(in_array($productenroll->status,array('withexternalapproval','emispass','giteepass','productenrollreject','productenrollpass'))){
                                        echo $lang->outwarddelivery->synSuccess;
                                    }
                                    else{
                                        echo '';
                                    }?>
                                </td>
                                <td>
                                    <?php if($productenroll->pushStatus and $PElog->response->message and in_array($productenroll->status,array('withexternalapproval','emispass','giteepass','productenrollreject','productenrollpass','qingzongsynfailed'))){
                                        echo $PElog->response->message;
                                    }
                                    elseif($productenroll->status=='qingzongsynfailed'){
                                        echo $lang->outwarddelivery->synFail;
                                    }
                                    else{
                                        $PElog->requestDate='';
                                    }?>
                                </td>
                                <td><?php if(strtotime($PElog->requestDate)>0) echo $PElog->requestDate;  ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang->outwarddelivery->outerReviewNodeList['3'];?></th>
                                <td>
                                    <?php echo zget($users,'guestcn',','); ?>
                                </td>
                                <td>
                                    <?php
                                    if(in_array($productenroll->status,array('withexternalapproval','emispass','giteepass','productenrollreject','productenrollpass'))){
                                        echo zget($lang->productenroll->statusList,$productenroll->status);
                                    }
                                    else{
                                        echo '';
                                    }?>
                                </td>
                                <td>
                                    <?php if(in_array($productenroll->status,array('withexternalapproval','emispass','giteepass','productenrollreject','productenrollpass')))
                                    {     
                                        if($productenroll->status == 'productenrollreject'){
                                            echo "打回人：".$productenroll->returnPerson."<br>"."审批意见：".$productenroll->returnCase;
                                        }else{
                                            echo $productenroll->returnCase;
                                        } 
                                    } ?>
                                </td>
                                <td><?php if(strtotime($productenroll->returnDate)>0 and in_array($productenroll->status,array('withexternalapproval','emispass','giteepass','productenrollreject','productenrollpass'))) echo $productenroll->returnDate; ?></td>
                            </tr>
                            <?php else:?>
                                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                            <?php endif;?>
                        </table>
                    </div>
                </div>
        </div>
        <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productenroll->basicInfo;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                        <th class="w-120px"><?php echo $lang->productenroll->giteeId;?></th>
                        <td><?php echo $productenroll->giteeId;?></td>
                        </tr>
                        <tr>
                        <th><?php echo $lang->productenroll->status;?></th>
                        <td><?php echo $productenroll->closed == '1' ? $lang->productenroll->labelList['closed'] :zget($lang->productenroll->statusList, $productenroll->status, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->createdBy;?></th>
                            <td><?php echo zget($users, $productenroll->createdBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->createdDepts;?></th>
                            <td><?php echo zget($depts, $productenroll->createdDept, '');?></td>
                        </tr>
                        <?php
                        if ($productenroll->app){
                            $teams = array();
                            $apps = [];
                            $isPayments = array();
                            foreach($productenroll->appsInfo as $appID=>$appInfo)
                            {
                                $apps[] = $appInfo->name;
                                $isPayments[] = zget($lang->application->isPaymentList,$appInfo->isPayment, '');
                                $teams[] = zget($lang->application->teamList,$appInfo->team, '');
                            }
                        }
                        ?>

                        <tr>
                            <th><?php echo $lang->productenroll->app;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($apps)),',');?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->isPayment;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($isPayments)),',');?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->team;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($teams)),',');?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outwarddelivery->secondorderId; ?></th>
                            <td>
                                <?php foreach (explode(',', $productenroll->secondorderId) as $objectID): ?>
                                    <?php if ($objectID and $secondorder->$objectID['code']) {
                                        echo html::a($this->createLink('secondorder', 'view', 'id=' . $objectID, '', true), $secondorder->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                    } ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->problemId;?></th>
                            <td>
                                <?php foreach(explode(',',$productenroll->problemId) as $objectID):?>
                                <?php if($objectID and $problem->$objectID['code']):?>
                                    <?php echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $problem->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br/>';?>
                                <?php endif;?>
                                <?php endforeach;?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->demandId;?></th>
                            <td>
                                <?php foreach(explode(',',$productenroll->demandId) as $objectID):?>
                                    <?php if ($objectID and $demand->$objectID['code']) {
                                        if($demand->$objectID['sourceDemand'] == 1){
                                            echo html::a($this->createLink('demand', 'view', 'id=' . $objectID, '', true), $demand->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        }else{
                                            echo html::a($this->createLink('demandinside', 'view', 'id=' . $objectID, '', true), $demand->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        }
                                    } ?>

                                <?php endforeach;?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->implementationForm;?></th>
                            <td><?php echo zget($lang->productenroll->implementationFormList, $productenroll->implementationForm, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->projectPlanId;?></th>
                            <td><?php foreach(explode(',', $productenroll->projectPlanId) as $project)
                            {
                                if ($project){
                                    echo  $projects[$project] . '<br/>';
                                }
                            } ?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->dealUserContact;?></th>
                            <td>
                                <?php echo $productenroll->contactTel;?>
                            </td>
                        </tr>

                        <?php
                        if ($productenroll->productId){
                            foreach(explode(',',$productenroll->productId) as $productID)
                            {
                                if($productID){
                                    $productName[] = $allProductNames[$productID];
                                }
                            }
                        }
                        ?>
                        <tr>
                            <th><?php echo $lang->productenroll->productName;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($productName)),',');?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->productLine ;?></th>
                            <td><?php echo zget($allLines,$productenroll->productLine,'');?></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->CBPprojectId;?></th>
                            <?php if($productenroll->CBPprojectId):?>
                                <td><?php echo $productenroll->CBPInfo[0]->code.'（'.$productenroll->CBPInfo[0]->name.'）'; ?></td>
                            <?php endif;?>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->requirementId?></th>
                            <td>
                                <?php foreach(explode(',',$productenroll->requirementId) as $objectID):?>
                                    <?php if ($objectID and $requirement->$objectID['code']) {
                                        if($requirement->$objectID['sourceRequirement'] == 1){
                                            echo html::a($this->createLink('requirement', 'view', 'id=' . $objectID, '', true), $requirement->$objectID['code'], '', "data-toggle='modal' data-type ='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        }else{
                                            echo html::a($this->createLink('requirementinside', 'view', 'id=' . $objectID, '', true), $requirement->$objectID['code'], '', "data-toggle='modal' data-type ='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        }
                                    } ?>
                                <?php endforeach;?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->belongedOutwardDelivery?></th>
                            <td>
                                <?php
                                if($parentId and $outwarddeliveryPairs[$parentId]->code){
                                    $outwardDeliveryMsg = html::a($this->createLink('outwarddelivery', 'view', 'id=' . $parentId, '', true), $outwarddeliveryPairs[$parentId]->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'（';
                                    if ($parent->isNewTestingRequest and $testingrequestPairs[$parent->testingRequestId]){
                                        $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('testingrequest', 'view', 'id=' . $parent->testingRequestId, '', true), $testingrequestPairs[$parent->testingRequestId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                    }
                                    if ($parent->isNewProductEnroll and $productenrollPairs[$parent->productEnrollId]){
                                        $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('productenroll', 'view', 'id=' . $parent->productEnrollId, '', true), $productenrollPairs[$parent->productEnrollId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                    }
                                    if ($parent->isNewModifycncc and $modifycnccPairs[$parent->modifycnccId]){
                                        $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('modifycncc', 'view', 'id=' . $parent->modifycnccId, '', true), $modifycnccPairs[$parent->modifycnccId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                    }
                                    $outwardDeliveryMsg = trim($outwardDeliveryMsg,',').'）<br/>';
                                    echo $outwardDeliveryMsg;
                                }?>
                            </td>
                        </tr>


                        <tr>
                            <th><?php echo $lang->outwarddelivery->relatedOutwardDelivery?></th>
                            <td>
                                <?php foreach($allRelations['parents'] as $object){

                                    $outwardDeliveryMsg = html::a($this->createLink('outwarddelivery', 'view', 'id=' . $object['id'], '', true), $object['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'（';
                                    $trID = $outwarddeliveryPairs[$object['id']]->testingRequestId;
                                    $peID = $outwarddeliveryPairs[$object['id']]->productEnrollId;
                                    $mcID = $outwarddeliveryPairs[$object['id']]->modifycnccId;
                                    if ($trID and $testingrequestPairs[$trID]){
                                        $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('testingrequest', 'view', 'id=' . $trID, '', true), $testingrequestPairs[$trID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                    }
                                    if ($peID and $productenrollPairs[$peID]){
                                        $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('productenroll', 'view', 'id=' . $peID, '', true), $productenrollPairs[$peID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                    }
                                    if ($mcID and $modifycnccPairs[$mcID]){
                                        $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('modifycncc', 'view', 'id=' . $mcID, '', true), $modifycnccPairs[$mcID], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                    }
                                    $outwardDeliveryMsg = trim($outwardDeliveryMsg,',').'）<br/>';
                                    echo $outwardDeliveryMsg;

                                }?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->relatedTestingRequest?></th>
                            <td>
                                <?php if($parent->testingRequestId and $testingrequestPairs[$parent->testingRequestId]):?>
                                    <?php echo html::a($this->createLink('testingrequest', 'view', 'id=' . $parent->testingRequestId, '', true), $testingrequestPairs[$parent->testingRequestId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br/>';?>
                                <?php endif;?>
                                <?php foreach($allRelations['testList'] as $objectId){
                                    $outwardDeliveryMsg = '';
                                    if ($testingrequestPairs[$objectId]){
                                        $outwardDeliveryMsg = html::a($this->createLink('testingrequest', 'view', 'id=' . $objectId, '', true), $testingrequestPairs[$objectId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br>';
                                    }
                                    echo $outwardDeliveryMsg;
                                }?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->relatedModifycncc?></th>
                            <td>
                                <?php if($parent->modifycnccId and $modifycnccPairs[$parent->modifycnccId]):?>
                                <?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $parent->modifycnccId, '', true), $modifycnccPairs[$parent->modifycnccId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br/>';?>
                                <?php endif;?>
                                <?php foreach($allRelations['modifyList'] as $objectId){
                                    $outwardDeliveryMsg = '';
                                    if ($modifycnccPairs[$objectId]){
                                        $outwardDeliveryMsg = html::a($this->createLink('modifycncc', 'view', 'id=' . $objectId, '', true), $modifycnccPairs[$objectId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br>';
                                    }
                                    echo $outwardDeliveryMsg;
                                }?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->productenroll->isPlan;?></th>
                            <td><?php echo zget($lang->productenroll->isPlanList,$productenroll->isPlan,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->planProductName;?></th>
                            <td><?php echo $productenroll->planProductName;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->versionNum;?></th>
                            <td><?php echo $productenroll->versionNum;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->lastVersionNum;?></th>
                            <td><?php echo $productenroll->lastVersionNum;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->checkDepartment;?></th>
                            <td><?php echo zget($lang->productenroll->checkDepartmentList,$productenroll->checkDepartment,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->result ;?></th>
                            <td><?php echo zget($lang->productenroll->resultList,$productenroll->result,'') ;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->installationNode;?></th>
                            <td><?php echo zget($lang->productenroll->installNodeList,$productenroll->installationNode,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->softwareProductPatch ;?></th>
                            <td><?php echo zget($lang->productenroll->softwareProductPatchList,$productenroll->softwareProductPatch,'') ;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->softwareCopyrightRegistration;?></th>
                            <td><?php echo zget($lang->productenroll->softwareCopyrightRegistrationList,$productenroll->softwareCopyrightRegistration,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->platform;?></th>
                            <td><?php echo zget($lang->productenroll->appList,$productenroll->platform);?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->planDistributionTime;?></th>
                            <td><?php echo $productenroll->planDistributionTime;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->planUpTime;?></th>
                            <td><?php echo $productenroll->planUpTime;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->contactEmail;?></th>
                            <td><?php echo $productenroll->contactEmail;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->applyTime;?></th> 
                            <td><?php echo $productenroll->applyTime;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->returnTimes;?></th>
                            <td><?php echo $productenroll->returnTimes;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->createdDate;?></th>
                            <td><?php echo $productenroll->createdDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->editedBy;?></th>
                            <td><?php echo zget($users, $productenroll->editedBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->editedDate;?></th>
                            <td><?php echo $productenroll->editedDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->closedBy;?></th>
                            <td><?php echo zget($users, $productenroll->closedBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->closedDate;?></th>
                            <td><?php echo $productenroll->closedDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->closedReason;?></th>
                            <td><?php echo zget( $lang->outwarddelivery->closedReasonList,$productenroll->closedReason);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outwarddelivery->outerReview;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class="w-100px"><?php echo $lang->productenroll->giteeId;?></th>
                            <td><?php echo $productenroll->giteeId;?></td>
                        </tr>
                        <tr>
                            <th class='w-120px'><?php echo $lang->productenroll->emisRegisterNumber;?></th>
                            <td><?php echo $productenroll->emisRegisterNumber;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->cardStatus;?></th>
                            <td><?php echo zget($lang->productenroll->cardStatusList, $productenroll->cardStatus, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->rejectBy;?></th>
                            <td><?php echo $productenroll->returnPerson;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->rejectReason ;?></th>
                            <td><?php echo $productenroll->returnCase ;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productenroll->rejectDate ;?></th>
                            <td><?php if(strtotime($productenroll->returnDate)>0) echo $productenroll->returnDate ;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if($productenroll->release):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outwarddelivery->release;?></div>
                <div class='detail-content'>
                    <?php include '../../release/view/block.html.php'; ?>
                </div>
            </div>
        </div>
        <?php endif?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>