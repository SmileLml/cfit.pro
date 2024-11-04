<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::import($jsRoot . 'bpmn/bpmn-js-6.1.2.js'); ?>

    <style>
        .review-opinion{
            width: 370px
        }
        #canvas {
            height: 420px;
            width: 1300px;
        }
        .bjs-powered-by{display: none;}
        .nodeSuccess .djs-visual > :nth-child(1){
            stroke: #52c41a !important;
            stroke-width: 3px;
        }
        .nodeProcessing .djs-visual > :nth-child(1){
            fill: #1b85ff !important;
            stroke-width: 3px;
        }
        .nodeProcessing .djs-visual > :nth-child(2){
            fill: #f6fff7 !important;
        }
    </style>
    <div id="mainMenu" class="clearfix">
        <div class="btn-toolbar pull-left">
            <?php if (!isonlybody()): ?>
                <?php $browseLink = $app->session->creditList != false ? $app->session->creditList : inlink('browse'); ?>
                <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
                <div class="divider"></div>
            <?php endif; ?>
            <div class="page-title">
                <span class="label label-id"><?php echo $credit->code ?></span>
            </div>
        </div>
        <?php if (!isonlybody()): ?>
            <div class="btn-toolbar pull-right">

            </div>
        <?php endif; ?>
    </div>
    <div id="mainContent" class="main-row">
        <div class="main-col col-8">

            <div class="cell">
                <div class='tabs' id='tabsNav'>
                    <ul class='nav nav-tabs' id="changeUl">
                        <li id='creditFormLi' <?php if($type == 'creditForm')  echo "class='active'"?>><a href='#creditForm' data-toggle='tab'><?php echo  $lang->credit->formInfo;?></a></li>
                        <?php if(!empty($credit->workflowId)):?>
                            <li id='flowInfoLi' <?php if($type == 'flowInfo')   echo "class='active'"?>><a href='#flowInfo' data-toggle='tab'><?php echo  $lang->credit->flowImg;?></a></li>
                        <?php endif;?>

                    </ul>
                </div>
                <div class='tab-content'>
                    <div class="detail tab-pane <?php if($type == 'creditForm') echo 'active'?>" id="creditForm">

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->subTitle->params; ?></div>
                            <div class='detail-content'>
                                <table class='table table-data'>
                                    <tbody>
                                    <tr>
                                        <th class='w-160px'><?php echo $lang->credit->level; ?></th>
                                        <td><?php echo zget($lang->credit->levelList, $credit->level, ''); ?></td>
                                        <th class='w-160px'><?php echo $lang->credit->changeNode; ?></th>
                                        <td><?php echo zmget($lang->credit->changeNodeList, $credit->changeNode, ''); ?></td>

                                    </tr>

                                    <tr>
                                        <th><?php echo $lang->credit->mode; ?></th>
                                        <td><?php echo zmget($lang->credit->modeList, $credit->mode, ''); ?></td>
                                        <th><?php echo $lang->credit->type; ?></th>
                                        <td><?php echo zmget($lang->credit->typeList, $credit->type, ''); ?></td>
                                    </tr>


                                        <tr>
                                            <th><?php echo $lang->credit->changeSource; ?></th>
                                            <td><?php echo zmget($lang->credit->changeSourceList, $credit->changeSource, ''); ?></td>
                                            <th><?php echo $lang->credit->executeMode; ?></th>
                                            <td><?php echo zmget($lang->credit->executeModeList, $credit->executeMode, ''); ?></td>

                                        </tr>

                                        <tr>
                                            <th><?php echo $lang->credit->emergencyType; ?></th>
                                            <td><?php echo zget($lang->credit->emergencyTypeList, $credit->emergencyType, ''); ?></td>
                                            <th><?php echo $lang->credit->isBusinessAffect; ?></th>
                                            <td><?php echo zget($lang->credit->isBusinessAffectList, $credit->isBusinessAffect, ''); ?></td>
                                        </tr>

                                        <tr>
                                            <th><?php echo $lang->credit->planBeginTime; ?></th>
                                            <td><?php echo $credit->planBeginTime == '0000-00-00 00:00:00'? '':$credit->planBeginTime; ?></td>
                                            <th><?php echo $lang->credit->planEndTime; ?></th>
                                            <td><?php echo $credit->planEndTime == '0000-00-00 00:00:00'? '':$credit->planEndTime; ?></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->summary; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->summary) ?  html_entity_decode(str_replace("\n","<br/>",$credit->summary)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->desc; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->desc) ?  html_entity_decode(str_replace("\n","<br/>",$credit->desc)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->techniqueCheck; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->techniqueCheck) ?  html_entity_decode(str_replace("\n","<br/>",$credit->techniqueCheck)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->riskAnalysisEmergencyHandle; ?></div>
                            <div class="detail-content article-content">
                                <table class="table ops">
                                    <tr>
                                        <th class="w-200px"><?php echo $lang->credit->id; ?></th>
                                        <td><?php echo $lang->credit->riskAnalysis; ?></td>
                                        <td><?php echo $lang->credit->emergencyBackWay; ?></td>
                                    </tr>
                                    <?php
                                        if ($credit->riskAnalysisEmergencyHandle):$riskAnalysisEmergencyHandle = json_decode($credit->riskAnalysisEmergencyHandle);

                                        ?>
                                        <?php
                                            foreach ($riskAnalysisEmergencyHandle as $key => $ER):
                                        ?>
                                            <tr>
                                                <th><?php echo $key + 1; ?></th>
                                                <td>
                                                    <?php echo $ER->riskAnalysis; ?>
                                                </td>
                                                <td>
                                                    <?php echo $ER->emergencyBackWay;?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->feasibilityAnalysis; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->feasibilityAnalysis) ?  html_entity_decode(str_replace("\n","<br/>",$credit->feasibilityAnalysis)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->productAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->productAffect) ?  html_entity_decode(str_replace("\n","<br/>",$credit->productAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->businessAffect; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->businessAffect) ?  html_entity_decode(str_replace("\n","<br/>",$credit->businessAffect)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->svnUrl; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->svnUrl) ?  html_entity_decode(str_replace("\n","<br/>",$credit->svnUrl)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>

                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->onLineFile; ?></div>
                            <div class="detail-content article-content">
                                <?php echo !empty($credit->onLineFile) ?  html_entity_decode(str_replace("\n","<br/>",$credit->onLineFile)) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            </div>
                        </div>


                    </div>

                    <div class="detail hidden <?php if($type == 'flowInfo') echo 'active'?>" id="flowInfo">
                        <div class="detail">
                            <div class="detail-title"><?php echo $lang->credit->currentStatus .': ' . zget($lang->credit->statusList, $credit->status);?></div>
                            <div class="detail-content article-content">
                                <div id="canvas">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!--处理意见-->
            <div class="cell">
                <div class="detail">
                    <div class="clearfix">
                        <div class="detail-title pull-left"><?php echo $lang->credit->reviewOpinion; ?></div>
                        <div class="detail-title pull-right">
                            <?php
                            if(common::hasPriv('credit', 'showHistoryNodes')) echo html::a($this->createLink('credit', 'showHistoryNodes', 'id='.$credit->id, '', true), $lang->credit->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                            ?>
                        </div>
                    </div>
                    <div class="detail-content article-content">
                        <?php if (!empty($nodes)): ?>
                            <table class="table ops">
                                <tr>
                                    <th class="w-180px"><?php echo $lang->credit->reviewNode; ?></th>
                                    <td class="w-180px"><?php echo $lang->credit->reviewer; ?></td>
                                    <td class="w-180px"><?php echo $lang->credit->dealResult; ?></td>
                                    <td class="review-opinion"><?php echo $lang->credit->reviewOpinion; ?></td>
                                    <td class="w-180px"><?php echo $lang->credit->reviewTime; ?></td>
                                </tr>

                                <?php
                                foreach ($nodes as $reviewNode):
                                    if(!isset($lang->credit->reviewNodeNameList[$reviewNode['nodeName']])){
                                        continue;
                                    }
                                    $nodeCode = $reviewNode['nodeName'];
                                    $nodeName = zget($lang->credit->reviewNodeNameList, $reviewNode['nodeName']);
                                    $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                                    $reviewerUsers = zmget($users, $nodeDealUsers);
                                ?>
                                    <tr>
                                        <th><?php echo $nodeName; ?></th>
                                        <td title="<?php echo $reviewerUsers; ?>">
                                            <?php echo $reviewerUsers; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($credit->status == 'cancel' && $reviewNode['result'] == 'pending'){
                                                echo '';
                                            }else{
                                                echo zget($lang->credit->reviewResultList, $reviewNode['result'], '');
                                            }
                                            ?>

                                            <?php if($reviewNode['dealUser']):?>
                                                （<?php echo zget($users, $reviewNode['dealUser']);?>）
                                            <?php endif;?>

                                        </td>
                                        <td> <?php echo $reviewNode['comment']; ?></td>
                                        <td> <?php echo $reviewNode['dealDate']; ?></td>
                                    </tr>
                                <?php endforeach;?>
                            </table>
                        <?php endif;?>
                    </div>
                </div>
            </div>

            <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>

            <div class='main-actions'>
                <div class="btn-toolbar">
                    <?php if(isset($browseLink)):?>
                        <?php common::printBack($browseLink); ?>
                    <?php endif;?>

                    <div class='divider'></div>
                    <?php
                    common::printIcon('credit', 'edit', "creditID=$credit->id", $credit, 'button');
                    common::printIcon('credit', 'submit', "creditID=$credit->id", $credit, 'button', 'play', '', 'iframe', true);
                    common::printIcon('credit', 'review', "creditID=$credit->id&version=$credit->version&status=$credit->status", $credit, 'button', 'glasses', '', 'iframe', true);
                    common::printIcon('credit', 'copy', "creditID=$credit->id", $credit, 'button');
                    common::printIcon('credit', 'cancel', "creditID=$credit->id", $credit, 'button', 'cancel', '', 'iframe', true);
                    common::printIcon('credit', 'delete', "creditID=$credit->id&source=view", $credit, 'button', 'trash', '', 'iframe', true);
                    ?>

                </div>
            </div>
        </div>
        <!-- 右侧基础信息 -->
        <div class="side-col col-4">
            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->credit->baseinfo; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class="w-120px"><?php echo $lang->credit->status; ?></th>
                                <td><?php echo zget($lang->credit->statusList, $credit->status, ''); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->dealUsers; ?></th>
                                <td><?php echo zmget($users, $credit->dealUsers, ''); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->appIds; ?></th>
                                <td><?php echo zmget($appList, $credit->appIds, '', '<br/>'); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->productIds; ?></th>
                                <td><?php echo zmget($productList, $credit->productIds, '', '<br/>'); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->implementationForm; ?></th>
                                <td><?php echo zget($lang->credit->implementationFormList, $credit->implementationForm); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->projectPlanId; ?></th>
                                <td><?php echo zmget($projectList, $credit->projectPlanId, '', '<br/>'); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->problemIds; ?></th>
                                <td>
                                    <?php
                                    $problemIds = array_filter(explode(',', $credit->problemIds));;
                                    if(!empty($problemIds)):
                                            foreach ($problemIds as $problemId):
                                                echo html::a($this->createLink('problem', 'view', 'id=' . $problemId, '', true), zget($problemList, $problemId), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                            endforeach;
                                        endif;
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->secondorderIds; ?></th>
                                <td>
                                    <?php
                                    $secondorderIds = array_filter(explode(',', $credit->secondorderIds));
                                    if(!empty($secondorderIds)):
                                        foreach ($secondorderIds as $secondorderId):
                                            echo html::a($this->createLink('secondorder', 'view', 'id=' . $secondorderId, '', true), zget($secondorderList, $secondorderId), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        endforeach;
                                    endif;
                                    ?>
                                </td>
                            </tr>

                            <?php if($credit->secondorderIds):?>
                                <tr>
                                    <th><?php echo $lang->credit->secondorderCancelLinkage; ?></th>
                                    <td>
                                        <?php echo zget($lang->credit->secondorderCancelLinkageList, $credit->secondorderCancelLinkage);?>
                                        <?php echo  common::printIcon('credit', 'editSecondorderCancelLinkage', "creditId=$credit->id", $credit, 'list','edit','','iframe',true) ;?>
                                    </td>
                                </tr>
                            <?php endif;?>

                            <tr>
                                <th><?php echo $lang->credit->demandIds; ?></th>
                                <td><?php
                                    $demandIds = array_filter(explode(',', $credit->demandIds));
                                    if(!empty($demandIds)):
                                        foreach ($demandIds as $demandId):
                                            echo html::a($this->createLink('demand', 'view', 'id=' . $demandId, '', true), zget($demandList, $demandId), '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'") . '<br/>';
                                        endforeach;
                                    endif;
                                    ?>
                                </td>
                            </tr>

                            <?php if(isset($credit->abnormalCode) && !empty($credit->abnormalCode)):?>
                                <tr>
                                    <th><?php echo $lang->credit->abnormalId; ?></th>
                                    <td><?php echo html::a($this->createLink('credit', 'view', 'id=' . $credit->abnormalId, '', true), $credit->abnormalCode, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></td>
                                </tr>
                            <?php endif;?>

                            <tr>
                                <th><?php echo $lang->credit->createdDept; ?></th>
                                <td><?php echo zget($deptInfo, 'name', ''); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->createdBy; ?></th>
                                <td><?php echo zget($users, $credit->createdBy, ''); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->createdDate; ?></th>
                                <td><?php echo $credit->createdDate; ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->editedBy; ?></th>
                                <td><?php echo zget($users, $credit->editedBy, ''); ?></td>
                            </tr>

                            <tr>
                                <th><?php echo $lang->credit->editedDate; ?></th>
                                <td><?php echo $credit->editedDate != '0000-00-00 00:00:00' ? $credit->editedDate :''; ?></td>
                            </tr>

                            <?php if(!empty($credit->onlineTime) && ($credit->onlineTime != '0000-00-00 00:00:00')):?>
                                <tr>
                                    <th><?php echo $lang->credit->onlineTime; ?></th>
                                    <td><?php echo $credit->onlineTime != '0000-00-00 00:00:00' ? $credit->onlineTime :''; ?></td>
                                </tr>
                            <?php endif;?>
                            <tr class="hidden">
                                <th><?php echo $lang->modify->isMakeAmends;?></th>
                                <td><?php echo zget($lang->modify->isMakeAmendsList,$credit->isMakeAmends,'')?></td>
                            </tr>
                            <?php if($credit->isMakeAmends == 'yes'):?>
                                <tr>
                                    <th><?php echo $lang->modify->actualDeliveryTime;?></th>
                                    <td><?php echo $credit->actualDeliveryTime;?></td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="cell">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->credit->statusTransition; ?></div>
                    <div class='detail-content'>
                        <table class='table table-data'>
                            <tbody>
                            <tr>
                                <th class='w-100px'><?php echo $lang->credit->nodeUser; ?></th>
                                <td class='text-center'><?php echo $lang->consumed->before; ?></td>
                                <td class='text-center'><?php echo $lang->consumed->after; ?></td>
                                <?php if(common::hasPriv('credit', 'workloadedit')): ?>
                                    <td class='text-center'><?php echo $lang->credit->deal; ?></td>
                                <?php endif;?>
                            </tr>

                            <?php foreach ($credit->consumed as $val): ?>
                                <tr>
                                    <th class='w-100px'><?php echo zget($users, $val->account, ''); ?></th>
                                    <?php
                                    echo "<td class='text-center'>" . $val->extra . zget($lang->credit->statusList, $val->before, '-') . "</td>";
                                    echo "<td class='text-center'>" . $val->extra . zget($lang->credit->statusList, $val->after, '-') . "</td>";
                                    ?>

                                    <?php if(common::hasPriv('credit', 'workloadedit')): ?>
                                        <td class='c-actions text-center'>
                                            <?php if(in_array($val->after, $lang->credit->allowEditStatusTurnStatusArray)) {
                                                common::printIcon('credit', 'workloadedit', "creditID={$credit->id}&consumedId={$val->id}", $credit, 'list', 'edit', '', 'iframe', true);
                                            }?>
                                        </td>
                                    <?php endif;?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php
js::set('status', $credit->status);
js::set('endStatusList', $endStatusList); //结束状态
js::set('bpmnXML', $flowImg);
js::set('highLightedActivityIdList', isset($reviewFlowInfo->highLightedActivityIdList) ? $reviewFlowInfo->highLightedActivityIdList : []);
js::set('runningActivityIdList', isset($reviewFlowInfo->runningActivityIdList) ? $reviewFlowInfo->runningActivityIdList : []);
?> <!--必须XMl格式-->
    <script>
        var ul = document.getElementById('changeUl');
        ul.addEventListener('click', function (e) {
            var target = e.target;
            switch (target.hash) {
                case '#creditForm':
                    $('#flowInfo').addClass('hidden');
                    $('#canvas').empty();
                    break;
                case '#flowInfo':
                    $('#flowInfo').removeClass('hidden');
                    if(bpmnXML){
                        //console.log(bpmnXML);
                        var bpmnJS = new BpmnJS({
                            container: '#canvas'
                        });

                        // import diagram
                        bpmnJS.importXML(bpmnXML, function(err) {
                            if (!err) {
                                //设置颜色的方法

                                const setNodeColor = function(ids, newBpmn, colorClass){
                                    const elementRegistry = newBpmn.get('elementRegistry');
                                    $.each(ids, function (index, item) {
                                        if (elementRegistry._elements[item]) {
                                            const element = elementRegistry._elements[item].gfx
                                            element.classList.add(colorClass)
                                        }
                                        // console.log(elementRegistry, element)
                                    });
                                };
                                var canvas = bpmnJS.get('canvas');
                                canvas.zoom('fit-viewport');
                                /*
                                if(highLightedActivityIdList.length > 0){
                                    setNodeColor(highLightedActivityIdList, bpmnJS, 'nodeSuccess');
                                }
                                */
                                if($.inArray(status, endStatusList) !== -1){ //结束标记
                                    runningActivityIdList = ['end'];
                                }else if(status == 'cancel'){
                                    runningActivityIdList = [];
                                }
                                if(runningActivityIdList.length > 0){
                                    setNodeColor(runningActivityIdList, bpmnJS, 'nodeProcessing');
                                }
                            } else {
                                return console.error('could not import BPMN 2.0 diagram', err);
                            }
                        });
                    }
                    break;
            }
        });
    </script>
<?php include '../../common/view/footer.html.php'; ?>