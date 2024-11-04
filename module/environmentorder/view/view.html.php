<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php js::import($jsRoot . 'bpmn/bpmn-js-6.1.2.js'); ?>
<style>
    .body-modal #mainMenu > .btn-toolbar .page-title {
        width: auto;
    }

    .table-data tbody > tr > th {
        width: 86px;
        padding-left: 0;
        font-weight: 400;
        color: #838a9d;
        text-align: right;
        vertical-align: middle;
    }
    .review-opinion{
        width: 370px
    }
    #canvas {
        height: 420px;
        width: 1300px;
    }
    .detail{
        margin:0 !important;
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
        <?php $browseLink = $app->session->environmentorderList != false ? $app->session->environmentorderList : inlink('browse'); ?>
        <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'"); ?>
        <div class="divider"></div>
        <div class="page-title">
            <span class="label label-id"><?php echo $environmentorder->code ?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">

    <div class="main-col col-8">
        <div class="cell">
        <div class='tabs' id='tabsNav'>
            <ul class='nav nav-tabs' id="changeUl">
                <li id='creditFormLi' <?php if($type == 'form')  echo "class='active'"?>><a href='#form' data-toggle='tab'><?php echo  $lang->environmentorder->formInfo;?></a></li>
                <?php if(!empty($environmentorder->processInstanceId)):?>
                    <li id='flowInfoLi' <?php if($type == 'flowInfo')   echo "class='active'"?>><a href='#flowInfo' data-toggle='tab'><?php echo  $lang->environmentorder->flowImg;?></a></li>
                <?php endif;?>

            </ul>
        </div>
        <div class='tab-content'>
            <div class="detail tab-pane <?php if($type == 'form') echo 'active'?>" id="form">
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->environmentorder->description; ?></div>
                        <div class="detail-content article-content">
                            <?php echo !empty($environmentorder->description) ? nl2br($environmentorder->description) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        </div>
                    </div>
                </div>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->environmentorder->content; ?></div>
                        <div class="detail-content article-content">
                            <?php echo !empty($environmentorder->content) ? nl2br($environmentorder->content) : "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                        </div>
                    </div>
                </div>
                <div class="cell">
                    <div class="detail">
                        <div class="detail-title"><?php echo $lang->environmentorder->list; ?></div>
                        <div class="detail-content article-content">
                            <?php if (!empty($environmentorder->list)): ?>
                                <table class="table changeInfo">
                                    <tr>
                                        <th class="w-150px"><?php echo $this->lang->environmentorder->remark; ?></th>
                                        <th class="w-150px"><?php echo $this->lang->environmentorder->material; ?></th>
                                        <th class="w-100px"><?php echo $this->lang->environmentorder->ip; ?></th>

                                    </tr>
                                    <?php $list = json_decode($environmentorder->list); ?>
                                    <?php if (!empty($list)):?>
                                        <?php foreach ($list as $item): ?>
                                            <tr>
                                                <td><?php echo $item->remark??$lang->noData ?></td>
                                                <?php if (isset($item->file)):?>
                                                    <td>
                                                        <?php
                                                        echo $this->fetch('file', 'printFiles', array('files' => [$item->file], 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));
                                                        ?>
                                                    </td>
                                                <?php else: ?>
                                                    <td><?php echo $lang->noData?></td>
                                                <?php endif; ?>
                                                <td><?php echo $item->ip??$lang->noData?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </table>
                            <?php else: ?>
                                <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detail hidden <?php if($type == 'flowInfo') echo 'active'?>" id="flowInfo">
                <div class="detail">
                    <div class="detail-title"><?php echo $lang->environmentorder->currentStatus .': ' . zget($lang->environmentorder->statusList, $environmentorder->status);?></div>
                    <div class="detail-content article-content">
                        <div id="canvas">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="cell">
            <div class="detail">
                <div class="clearfix">
                    <div class="detail-title pull-left"><?php echo $lang->environmentorder->dealOpinion; ?></div>
                    <div class="detail-title pull-right">
                        <?php
                        if(common::hasPriv('environmentorder', 'showHistoryNodes')) echo html::a($this->createLink('environmentorder', 'showHistoryNodes', 'id='.$environmentorder->id, '', true), $lang->environmentorder->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                        ?>
                    </div>
                </div>
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->environmentorder->dealNode; ?></th>
                                <td class="w-180px"><?php echo $lang->environmentorder->dealer; ?></td>
                                <td class="w-180px"><?php echo $lang->environmentorder->dealResult; ?></td>
                                <td class="review-opinion"><?php echo $lang->environmentorder->dealOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->environmentorder->dealTime; ?></td>
                            </tr>

                            <?php
                            foreach ($nodes as $key => $reviewNode):
                                if(!isset($lang->environmentorder->statusArray[$reviewNode['nodeName']])){
                                    continue;
                                }

                                $nodeCode = $reviewNode['nodeName'];
                                $nodeName = zget($lang->environmentorder->statusList, $reviewNode['nodeName']);
                                $nodeDealUsers = implode(',',$reviewNode['toDealUser']);
                                $reviewerUsers = zmget($users, $nodeDealUsers);
                                ?>
                            <?php if ($reviewNode['result'] && $reviewNode['result'] != 'ignore'): ?>
                                <tr>
                                    <th><?php echo $nodeName; ?></th>
                                    <td title="<?php echo $reviewerUsers; ?>">
                                        <?php echo $reviewerUsers; ?>
                                    </td>
                                    <td>
                                        <?php if(in_array($reviewNode['nodeName'],$lang->environmentorder->allowApprovalStatusArray )):?>
                                            <?php echo zget($lang->environmentorder->reviewList,$reviewNode['result']);?>
                                        <?php elseif(in_array($reviewNode['nodeName'],$lang->environmentorder->allowConfirmStatusArray )):?>
                                            <?php echo zget($lang->environmentorder->reviewConfirmList,$reviewNode['result']);?>
                                        <?php elseif(in_array($reviewNode['nodeName'],$lang->environmentorder->allowImplementStatusArray )):?>
                                            <?php echo zget($lang->environmentorder->reviewImplementList,$reviewNode['result']);?>
                                        <?php else:?>
                                            <?php echo zget($lang->environmentorder->reviewList,$reviewNode['result']);?>
                                        <?php endif;?>

                                        <?php if($reviewNode['dealUser']):?>
                                            （<?php echo zget($users, $reviewNode['dealUser']);?>）
                                        <?php endif;?>

                                    </td>
                                    <td style="white-space: pre-line"> <?php echo $reviewNode['comment']; ?></td>
                                    <td> <?php echo $reviewNode['dealDate']; ?></td>
                                </tr>
                            <?php endif;?>
                            <?php endforeach;?>
                        </table>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="cell"><?php include '../../common/view/action.html.php'; ?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($browseLink); ?>
                <div class='divider'></div>
                <?php
                common::printIcon('environmentorder', 'submit', "environmentorderID=$environmentorder->id&confirm=no&source=view", $environmentorder, 'list', 'checked', 'hiddenwin', '', false, '', $lang->environmentorder->submit);
                common::printIcon('environmentorder', 'edit', "environmentorderID=$environmentorder->id&source=view", $environmentorder, 'list');
                common::printIcon('environmentorder', 'deal', "environmentorderID=$environmentorder->id", $environmentorder, 'list', 'time', '', 'iframe', true, '', $lang->environmentorder->deal);
//                common::printIcon('environmentorder', 'copy', "environmentorderID=$environmentorder->id", $environmentorder, 'list');
                common::printIcon('environmentorder', 'delete', "environmentorderID=$environmentorder->id", $environmentorder, 'button', 'trash', 'hiddenwin','',true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->environmentorder->basicInfo; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class="w-100px"><?php echo $lang->environmentorder->title; ?></th>
                            <td><?php echo $environmentorder->title ??''; ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->environmentorder->origin; ?></th>
                            <td><?php
                                if (!empty($environmentorder->origin) || $environmentorder->origin === '0') {
                                    echo zget($lang->environmentorder->originList, $environmentorder->origin, '');
                                } elseif (!empty($environmentorder->origin)) {
                                    echo zget($lang->environmentorder->originList, $environmentorder->origin, '');
                                } else {
                                    echo '';
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <th class="w-100px"><?php echo $lang->environmentorder->priority; ?></th>
                            <td><span class="label <?php echo $this->environmentorder->diffColorPriority($environmentorder->priority); ?>">
                                        <?php echo zget($lang->environmentorder->priorityList, $environmentorder->priority); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->environmentorder->finallytime; ?></th>
                            <td><?php echo $environmentorder->finallytime ??''; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php $workHours = json_decode($environmentorder->workHour); ?>
<?php if(!empty($workHours)):?>
        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->environmentorder->reportwork; ?></div>
                <div class="detail-content article-content">
                    <table class="table ">
                        <tbody>
                        <tr>

                            <th class="w-180px"><?php echo $lang->environmentorder->executor; ?></th>
                            <th class="w-180px"><?php echo $lang->environmentorder->workHour; ?></th>
                        </tr>
                        <?php foreach ($workHours as $wh): ?>

                        <tr>
                                <td><?php echo zget($users, $wh->name); ?></td>
                                <td><?php echo $wh->workHour; ?></td>
                        </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php endif;?>

        <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->environmentorder->flowStatus; ?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th><?php echo $lang->environmentorder->createdBy; ?></th>
                            <td><?php echo zget($users, $environmentorder->createdBy); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->environmentorder->createdTime; ?></th>
                            <td><?php echo $environmentorder->createdTime; ?></td>
                        </tr>
                        <?php if($environmentorder->reviewer):?>
                        <tr>
                            <th><?php echo $lang->environmentorder->reviewer; ?></th>
                            <td><?php echo zget($users, $environmentorder->reviewer);  ?></td>
                        </tr>
                        <?php endif;?>
                        <?php if($environmentorder->executor):?>
                        <tr>
                            <th><?php echo $lang->environmentorder->executor; ?></th>
                            <td><?php echo zmget($users, $environmentorder->executor); ?></td>
                        </tr>
                        <?php endif;?>
                        <?php if($environmentorder->dealUser):?>
                        <tr>
                            <th class="w-100px"><?php echo $lang->environmentorder->dealUser; ?></th>
                            <td><?php echo zmget($users, $environmentorder->dealUser); ?></td>
                        </tr>
                        <?php endif;?>
                        <tr>
                            <th><?php echo $lang->environmentorder->status; ?></th>
                            <td><?php echo zget($lang->environmentorder->statusList, $environmentorder->status, ''); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
js::set('status', $environmentorder->status);
js::set('bpmnXML', $flowImg);
js::set('highLightedActivityIdList', isset($reviewFlowInfo->highLightedActivityIdList) ? $reviewFlowInfo->highLightedActivityIdList : []);
js::set('runningActivityIdList', isset($reviewFlowInfo->runningActivityIdList) ? $reviewFlowInfo->runningActivityIdList : []);
?> <!--必须XMl格式-->
<script>
    var ul = document.getElementById('changeUl');
    ul.addEventListener('click', function (e) {
        var target = e.target;
        switch (target.hash) {
            case '#form':
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
                            if(status=="archived"){ //结束标记
                                runningActivityIdList = ['end'];
                            }else if(status == ''){
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
