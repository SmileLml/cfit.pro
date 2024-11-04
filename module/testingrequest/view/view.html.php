<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php $browseLink = $app->session->testingrequestList != false ? $app->session->testingrequestList : inlink('browse');?>
            <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $testingrequest->code?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->testSummary;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->testSummary) ? $testingrequest->testSummary : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->testTarget;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->testTarget) ? $testingrequest->testTarget : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->isCentralizedTest;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->isCentralizedTest) ? zget($lang->testingrequest->isCentralizedTestList, $testingrequest->isCentralizedTest) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->acceptanceTestType;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->acceptanceTestType) ? zget($lang->testingrequest->acceptanceTestTypeList, $testingrequest->acceptanceTestType) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->currentStage;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->currentStage) ? $testingrequest->currentStage: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->os;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->os) ? $testingrequest->os: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->db;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->db) ? $testingrequest->db: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->content;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->content) ? html_entity_decode(str_replace("\n","<br/>",$testingrequest->content)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->env;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($testingrequest->env) ? html_entity_decode(str_replace("\n","<br/>",$testingrequest->env)): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->testingrequest->testReport;?></div>
                <div class="detail-content article-content">
                    <?php echo $this->fetch('file', 'printFiles', array('files' => $testingrequest->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => false));?>
                </div>
            </div>
        </div>
        <div class="cell">
            <div class="detail">
                <div class="clearfix">
                    <div class="detail-title pull-left"><?php echo $lang->outwarddelivery->reviewOpinion; ?></div>
                    <div class="detail-title pull-right">
                        <?php
                        if(common::hasPriv('testingrequest', 'showHistoryNodes')) echo html::a($this->createLink('testingrequest', 'showHistoryNodes', 'id='.$parentId, '', true), $lang->outwarddelivery->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
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
                            <td style="width:370px;"><?php echo $lang->outwarddelivery->reviewOpinion;?></td>
                            <td class="w-180px"><?php echo $lang->outwarddelivery->reviewTime;?></td>
                        </tr>
                        <?php
                        unset($lang->outwarddelivery->reviewNodeList[3]);
                        unset($lang->outwarddelivery->reviewNodeList[4]);
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
                                    <?php if($testingrequest->status!='waitsubmitted'):?>
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
                                        &nbsp;  （
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
                        <?php endforeach;?>
                        <tr>
                            <th><?php echo $lang->outwarddelivery->outerReviewNodeList['0'];?></th>
                            <td>
                                <?php echo zget($users,'guestjk',','); ?>
                            </td>
                            <td>
                                <?php
                                if(in_array($testingrequest->status,array('waitqingzong','qingzongsynfailed')) ){
                                    echo zget($lang->testingrequest->statusList, $testingrequest->status, '');
                                }
                                elseif(in_array($testingrequest->status,array('withexternalapproval','testingrequestreject','testingrequestpass','testing'))){
                                    echo $lang->outwarddelivery->synSuccess;
                                }
                                else{
                                    echo '';
                                }?>
                            </td>
                            <td>
                                <?php if($testingrequest->pushStatus and $TRlog->response->message and in_array($testingrequest->status,array('withexternalapproval','testingrequestreject','testingrequestpass','testing','qingzongsynfailed'))){
                                    echo $TRlog->response->message;
                                }
                                elseif($testingrequest->status=='qingzongsynfailed'){
                                    echo $lang->outwarddelivery->synFail;
                                }
                                else{
                                    $TRlog->requestDate='';
                                }?>
                            </td>
                            <td><?php if(isset($TRlog->requestDate)) echo $TRlog->requestDate; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outwarddelivery->outerReviewNodeList['1'];?></th>
                            <td>
                                <?php echo zget($users,'guestcn',','); ?>
                            </td>
                            <td>
                                <?php
                                if(in_array($testingrequest->status,array('withexternalapproval','testingrequestreject','testingrequestpass','testing'))){
                                    echo zget($lang->testingrequest->statusList,$testingrequest->status);
                                }
                                else{
                                    echo '';
                                }?>
                            </td>
                            <td><?php
                                if(in_array($testingrequest->status,array('withexternalapproval','testingrequestreject','testingrequestpass','testing'))){
                                    if($testingrequest->status == 'testingrequestreject'){
                                        echo "打回人：".$testingrequest->returnPerson."<br>"."审批意见：".$testingrequest->returnCase;
                                    }else{
                                        echo $testingrequest->returnCase;
                                    }
                                }else{
                                    echo '';
                                } 
                                ?></td>
                            <td><?php if(strtotime($testingrequest->returnDate)>0 and in_array($testingrequest->status,array('withexternalapproval','testingrequestreject','testingrequestpass','testing'))) echo $testingrequest->returnDate; ?></td>
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
                <div class="detail-title"><?php echo $lang->testingrequest->basicInfo;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class="w-120px"><?php echo $lang->testingrequest->giteeId;?></th>
                            <td><?php echo $testingrequest->giteeId;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->cardStatus;?></th>
                            <td><?php echo $testingrequest->closed == '1' ? $lang->testingrequest->labelList['closed'] :zget($lang->testingrequest->statusList, $testingrequest->status, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->createdBy;?></th>
                            <td><?php echo zget($users, $testingrequest->createdBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->createdDept;?></th>
                            <td><?php echo zget($depts, $testingrequest->createdDept, '');?></td>
                        </tr>

                        <?php
                        if ($testingrequest->app){
                            foreach($testingrequest->appsInfo as $appInfo)
                            {
                                $apps[] = $appInfo->name;
                                $isPayments[] = zget($lang->application->isPaymentList,$appInfo->isPayment, '');
                                $teams[] = zget($lang->application->teamList,$appInfo->team, '');
                            }
                        }
                        ?>
                        <tr>
                            <th><?php echo $lang->testingrequest->app;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($apps)),',');?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->isPayment;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($isPayments)),',');?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->team;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($teams)),'<br/>');?>
                            </td>
                        </tr>

                        <?php
                        if ($testingrequest->productId){
                            foreach(explode(',',$testingrequest->productId) as $productID)
                            {
                                if ($productID){
                                    $productName[] = $allProductNames[$productID];
                                }
                            }
                        }
                        ?>
                        <tr>
                            <th><?php echo $lang->testingrequest->productName;?></th>
                            <td>
                                <?php echo trim(implode('<br/>', array_unique($productName)),'<br/>');?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->implementationForm;?></th>
                            <td><?php echo zget($lang->outwarddelivery->implementationFormList, $testingrequest->implementationForm, '');?></td>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->project;?></th>
                            <td><?php foreach(explode(',', trim($testingrequest->projectPlanId,',')) as $project)
                                {
                                    if ($project)  echo  $projects[$project] . '<br/>';
                                } ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->CBPprojectId;?></th>
                            <?php if($testingrequest->CBPprojectId):?>
                                <td><?php echo $testingrequest->CBPInfo[0]->code.'（'.$testingrequest->CBPInfo[0]->name.'）'; ?></td>
                            <?php endif;?>
                        </tr>
                        <tr>
                            <th><?php echo $lang->outwarddelivery->secondorderId; ?></th>
                            <td>
                                <?php foreach (explode(',', $testingrequest->secondorderId) as $objectID): ?>
                                    <?php if ($objectID and $secondorder->$objectID['code']) {
                                        echo html::a($this->createLink('secondorder', 'view', 'id=' . $objectID, '', true), $secondorder->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                    } ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->problemId;?></th>
                            <td>
                                <?php foreach(explode(',',trim($testingrequest->problemId,',')) as $objectID):?>
                                    <?php if($objectID and $problem->$objectID['code'])  echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $problem->$objectID['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
                                <?php endforeach;?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->demandId;?></th>
                            <td>
                                <?php foreach(explode(',',trim($testingrequest->demandId,',')) as $objectID):?>
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
                            <th><?php echo $lang->testingrequest->requirementId?></th>
                            <td>
                                <?php foreach(explode(',',trim($testingrequest->requirementId,',')) as $objectID):?>
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
                            <th><?php echo $lang->testingrequest->belongedOutwardDelivery?></th>
                            <td>
                                <?php
                                    if($parentId and $outwarddeliveryPairs[$parentId]->code){
                                        $outwardDeliveryMsg = html::a($this->createLink('outwarddelivery', 'view', 'id=' . $parentId, '', true), $outwarddeliveryPairs[$parentId]->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'（';
                                        if ($parent->isNewTestingRequest and  $testingrequestPairs[$parent->testingRequestId]){
                                            $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('testingrequest', 'view', 'id=' . $parent->testingRequestId, '', true), $testingrequestPairs[$parent->testingRequestId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                        }
                                        if ($parent->isNewProductEnroll and   $productenrollPairs[$parent->productEnrollId]){
                                            $outwardDeliveryMsg = $outwardDeliveryMsg . html::a($this->createLink('productenroll', 'view', 'id=' . $parent->productEnrollId, '', true), $productenrollPairs[$parent->productEnrollId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").',';
                                        }
                                        if ($parent->isNewModifycncc and  $modifycnccPairs[$parent->modifycnccId]){
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
                            <th><?php echo $lang->testingrequest->relatedProductEnroll?></th>
                            <td>
                                <?php if($parent->productEnrollId and $productenrollPairs[$parent->productEnrollId]):?>
                                    <?php echo html::a($this->createLink('productenroll', 'view', 'id=' . $parent->productEnrollId, '', true), $productenrollPairs[$parent->productEnrollId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
                                <?php endif;?>
                                <?php foreach($allRelations['productList'] as $objectId){
                                    $outwardDeliveryMsg = '';
                                        if ($productenrollPairs[$objectId]){
                                            $outwardDeliveryMsg = html::a($this->createLink('productenroll', 'view', 'id=' . $objectId, '', true), $productenrollPairs[$objectId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'").'<br>';
                                        }
                                        echo $outwardDeliveryMsg;
                                }?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->relatedModifycncc?></th>
                            <td>
                                <?php if($parent->modifycnccId and $modifycnccPairs[$parent->modifycnccId]):?>
                                    <?php echo html::a($this->createLink('modifycncc', 'view', 'id=' . $parent->modifycnccId, '', true), $modifycnccPairs[$parent->modifycnccId], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?><br/>
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
                            <th><?php echo $lang->testingrequest->returnTimes;?></th>
                            <td><?php echo $testingrequest->returnTimes;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->dealUserContact;?></th>
                            <td><?php echo $testingrequest->contactTel;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->createdDate;?></th>
                            <td><?php echo $testingrequest->createdDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->editedBy;?></th>
                            <td><?php echo zget($users, $testingrequest->editedBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->editedDate;?></th>
                            <td><?php echo $testingrequest->editedDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->closedBy;?></th>
                            <td><?php echo zget($users, $testingrequest->closedBy, '');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->closedDate;?></th>
                            <td><?php echo $testingrequest->closedDate;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->closedReason;?></th>
                            <td><?php echo zget($lang->outwarddelivery->closedReasonList,$testingrequest->closedReason);?></td>
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
                            <th class='w-120px'><?php echo $lang->testingrequest->outerReviewResult;?></th>
                            <td><?php echo zget($lang->testingrequest->cardStatusList,$testingrequest->cardStatus,'');?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->rejectBy;?></th>
                            <td><?php echo $testingrequest->returnPerson;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->rejectReason;?></th>
                            <td><?php echo $testingrequest->returnCase ;?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->testingrequest->rejectDate;?></th>
                            <td><?php if(strtotime($testingrequest->returnDate)>0) echo $testingrequest->returnDate ;?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if ($testingrequest->release):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->outwarddelivery->release;?></div>
                <div class='detail-content'>
                    <?php include '../../release/view/block.html.php'; ?>
                </div>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
