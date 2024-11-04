<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
        <?php if(!isonlybody()):?>
            <?php $browseLink = $app->session->productionchangeList != false ? $app->session->productionchangeList : inlink('browse');?>

            <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
            <div class="divider"></div>
        <?php endif;?>
        <div class="page-title">
            <span class="label label-id"><?php echo $productionChangeInfo->code?></span>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
    <div class="main-col col-8">
        <div class="cell">
            <!--上线摘要 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->abstract;?></div>
                <div class="detail-content article-content" style="white-space: pre-line">
                    <?php echo !empty($productionChangeInfo->abstract) ? $productionChangeInfo->abstract : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <!--上线实施内容 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->implementContent;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->implementContent) ? $productionChangeInfo->implementContent : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <!--上线影响说明 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->effect;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->effect) ? $productionChangeInfo->effect : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <!--是否影响关联系统 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->ifEffectSystem;?></div>
                <div class="detail-content article-content">
                    <?php echo zget($lang->productionchange->ifEffectSystemList,$productionChangeInfo->ifEffectSystem);?>
                </div>
            </div>
            <!--影响关联系统说明 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->effectSystemExplain;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->effectSystemExplain) ? $productionChangeInfo->effectSystemExplain : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <!--上线材料说明 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->materialExplain;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->materialExplain) ? $productionChangeInfo->materialExplain : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <!--介质包获取地址 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->mediaPackage;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->mediaPackage) ? $productionChangeInfo->mediaPackage : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>

            <!--附件 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->fileTitle;?></div>
                <?php if ($productionChangeInfo->files): ?>
                <div class="detail-content article-content">
                    <?php
                    foreach ($productionChangeInfo->files as $key => $file) {
                        echo $this->fetch('file', 'printFiles', array('files' => array($key => $file), 'fieldset' => 'false', 'object' => $productionChangeInfo, 'canOperate' => $file->addedBy == $this->app->user->account));
                    };
                    ?>
                    <?php if(common::hasPriv('productionchange', 'uploadFile'))
                        {
                            echo $lang->productionchange->uploadFile;
                            echo common::printIcon('productionchange', 'uploadFile', "preproductionID=$productionChangeInfo->id", $productionChangeInfo, 'list', 'edit', '', 'iframe', true);
                        }
                     ?>
                </div>
                <?php endif;?>
            </div>

        </div>
        <div class="cell">
            <!--实施记录 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->record;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->record) ? $productionChangeInfo->record : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
            <!--备注说明 -->
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->remark;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($productionChangeInfo->remark) ? $productionChangeInfo->remark : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        </div>
        <!--处理意见-->
        <div class="cell">
            <div class="detail">
                <div class="clearfix">
                    <div class="detail-title pull-left"><?php echo $lang->productionchange->reviewOpinion; ?></div>
                    <div class="detail-title pull-right">
                        <?php
                        if(common::hasPriv('productionchange', 'showHistoryNodes')) echo html::a($this->createLink('productionchange', 'showHistoryNodes', 'id='.$productionChangeInfo->id, '', true), $lang->productionchange->showHistoryNodes, '', "data-toggle='modal' data-type='iframe' data-width='70%' style='color: #0c60e1;'");
                        ?>
                    </div>
                </div>
                <div class="detail-content article-content">
                    <?php if (!empty($nodes)): ?>
                        <table class="table ops">
                            <tr>
                                <th class="w-180px"><?php echo $lang->productionchange->reviewNode; ?></th>
                                <td class="w-180px"><?php echo $lang->productionchange->reviewer; ?></td>
                                <td class="w-180px"><?php echo $lang->productionchange->dealResult; ?></td>
                                <td class="review-opinion"><?php echo $lang->productionchange->reviewOpinion; ?></td>
                                <td class="w-180px"><?php echo $lang->productionchange->reviewTime; ?></td>
                            </tr>

                            <?php
                            foreach ($nodes as $key => $reviewNode):
                                if(!isset($lang->productionchange->reviewNodeCodeNameList[$reviewNode['nodeName']])){
                                    continue;
                                }

                                //处理待提交的admin
                                if($reviewNode['nodeName'] == 'wait' && in_array('admin',$reviewNode['toDealUser']))
                                {

                                    foreach ($reviewNode['toDealUser'] as $k => $value)
                                    {

                                        if($value == 'admin')
                                        {
                                            unset($reviewNode['toDealUser'][$k]);
                                        }
                                    }
                                }

                                $nodeCode = $reviewNode['nodeName'];
                                $nodeName = zget($lang->productionchange->statusList, $reviewNode['nodeName']);
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
                                            echo zget($lang->productionchange->reviewList,$reviewNode['result']);
                                        ?>
                                        <?php if($reviewNode['dealUser']):?>
                                            （<?php echo zget($users, $reviewNode['dealUser']);?>）
                                        <?php endif;?>

                                    </td>
                                    <td style="white-space: pre-line"> <?php echo $reviewNode['comment']; ?></td>
                                    <td> <?php echo $reviewNode['dealDate']; ?></td>
                                </tr>
                            <?php endforeach;?>
                        </table>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="cell"><?php include '../../common/view/action.html.php';?></div>
        <div class='main-actions'>
            <div class="btn-toolbar">
                <?php common::printBack($browseLink);?>
                <div class='divider'></div>
                <?php
                $account = $this->app->user->account;
                common::printIcon('productionchange', 'edit', "preproductionID=$productionChangeInfo->id", $productionChangeInfo, 'list');

                if(in_array($productionChangeInfo->status,array('wait','feedback'))  and strstr($productionChangeInfo->dealUser, $account) !== false){
                    echo '<button type="button" class="btn" title="' . $lang->productionchange->deal . '" onclick="isClickable('.$productionChangeInfo->id.', \'deal\')"><i class="icon-common-suspend icon-time"></i></button>';
                    common::printIcon('productionchange', 'deal', "preproductionID=$productionChangeInfo->id", $productionChangeInfo, 'list', 'time', '', 'iframe hidden', true, 'id=isClickable_deal' . $productionChangeInfo->id);
                }else{
                    common::printIcon('productionchange', 'deal', "preproductionID=$productionChangeInfo->id", $productionChangeInfo, 'list', 'time', '', 'iframe', true);
                }

                common::printIcon('productionchange', 'review', "preproductionID=$productionChangeInfo->id", $productionChangeInfo, 'list', 'glasses', '', 'iframe', true);
                ?>
            </div>
        </div>
    </div>
    <div class="side-col col-4">
    <div class="cell">
        <div class="detail">
            <div class="detail-title"><?php echo $lang->productionchange->basicInfo;?></div>
            <div class='detail-content'>
                <table class='table table-data'>
                    <tbody>
                    <!--申请人 -->
                    <tr>
                        <th class="w-120px"><?php echo $lang->productionchange->applicant;?></th>
                        <td><?php echo zget($users,$productionChangeInfo->applicant);?></td>
                    </tr>
                    <!--申请人部门 -->
                    <tr>
                        <th><?php echo $lang->productionchange->applicantDept;?></th>
                        <td><?php echo zget($depts, $productionChangeInfo->applicantDept, '');?></td>
                    </tr>
                    <!--上线申请类型 -->
                    <tr>
                        <th><?php echo $lang->productionchange->onlineType;?></th>
                        <td><?php echo zget($lang->productionchange->onlineTypeList,$productionChangeInfo->onlineType);?></td>
                    </tr>

                    <!--上线计划实施时间-开始时间 -->
                    <tr>
                        <th><?php echo $lang->productionchange->onlineStart;?></th>
                        <td><?php echo $productionChangeInfo->onlineStart != '0000-00-00 00:00:00' ? $productionChangeInfo->onlineStart : '';?></td>
                    </tr>
                    <!--上线计划实施时间-结束时间 -->
                    <tr>
                        <th><?php echo $lang->productionchange->onlineEnd;?></th>
                        <td><?php echo  $productionChangeInfo->onlineEnd != '0000-00-00 00:00:00' ? $productionChangeInfo->onlineEnd : '';?></td>
                    </tr>
                    <!--应用系统名称 多选-->
                    <tr>
                        <th><?php echo $lang->productionchange->application;?></th>
                        <td><?php echo zmget($apps, $productionChangeInfo->application, '');?></td>
                    </tr>
                    <!--空间 -->

                    <tr>
                        <th ><?php echo $lang->productionchange->space;?></th>
                        <td><?php echo  zget($projects,$productionChangeInfo->space);?></td>
                    </tr>
                    <!--关联发布-->
                    <tr>
                        <th><?php echo $lang->productionchange->correlationPublish;?></th>
                        <td><?php echo zmget($releases,$productionChangeInfo->correlationPublish);?></td>
                    </tr>
                    <!--关联需求条目 -->
                    <tr>
                        <th><?php echo $lang->productionchange->correlationDemand;?></th>
                        <td>
                            <?php if(isset($correlationDemandInfo)):?>
                                <?php foreach ($correlationDemandInfo as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('demandinside', 'view', 'id=' . $objectID, '', true), $object['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </td>
                    </tr>
                    <!--关联问题单 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->correlationProblem;?></th>
                        <td>
                            <?php if(isset($correlationProblemInfo)):?>
                                <?php foreach ($correlationProblemInfo as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $object['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </td>
                    </tr>

                    <!--关联工单 -->
                    <tr>
                        <th><?php echo $lang->productionchange->correlationSecondorder;?></th>
                        <td>
                            <?php if(isset($correlationSecondaryInfo)):?>
                                <?php foreach ($correlationSecondaryInfo as $objectID => $object): ?>
                                    <p><?php echo html::a($this->createLink('secondorder', 'view', 'id=' . $objectID, '', true), $object['code'], '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'"); ?></p>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </td>
                    </tr>
                    <!--实际上线时间 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->actualOnlineTime;?></th>
                        <td><?php echo $productionChangeInfo->actualOnlineTime;?></td>
                    </tr>
                    <!--抄送人 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->mailto;?></th>
                        <td><?php echo zmget($users, $productionChangeInfo->mailto);?></td>
                    </tr>
                    <!--创建人 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->createdBy;?></th>
                        <td><?php echo zget($users, $productionChangeInfo->createdBy);?></td>
                    </tr>
                    <!--创建时间 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->createdDate;?></th>
                        <td><?php echo $productionChangeInfo->createdDate;?></td>
                    </tr>
                    <!--部门确认责任人 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->deptConfirmPerson;?></th>
                        <td><?php echo zmget($users, $productionChangeInfo->deptConfirmPerson);?></td>
                    </tr>
                    <!--业务方接口人 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->interfacePerson;?></th>
                        <td><?php echo zmget($users, $productionChangeInfo->interfacePerson);?></td>
                    </tr>
                    <!--运维方接口人 -->
                    <tr>
                        <th ><?php echo $lang->productionchange->operationPerson;?></th>
                        <td><?php echo zmget($users, $productionChangeInfo->operationPerson);?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cell">
        <div class="detail">
            <div class="detail-title"><?php echo $lang->productionchange->basicstatus;?></div>
            <div class='detail-content'>
                <table class='table table-data'>
                    <tbody>
                    <tr>
                        <th class="w-100px"><?php echo $lang->productionchange->status;?></th>
                        <td><?php echo zget($this->lang->productionchange->statusList,$productionChangeInfo->status);?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->productionchange->dealUser;?></th>
                        <td><?php echo zmget($users,$productionChangeInfo->dealUser);?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="cell">
            <div class="detail">
                <div class="detail-title"><?php echo $lang->productionchange->consumedTitle;?></div>
                <div class='detail-content'>
                    <table class='table table-data'>
                        <tbody>
                        <tr>
                            <th class='w-100px'><?php echo $lang->productionchange->nodeUser;?></th>
                            <td class='text-center'><?php echo $lang->productionchange->before;?></td>
                            <td class='text-center'><?php echo $lang->productionchange->after;?></td>
                            <!--                <td class='text-left'>--><?php //echo $lang->actions;?><!--</td>-->
                        </tr>
                        <?php foreach($consumeds as $index => $c):?>
                            <tr>
                                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                <td class='text-center'><?php echo zget($lang->productionchange->statusList, $c->before, '-');?></td>
                                <td class='text-center'><?php echo zget($lang->productionchange->statusList, $c->after, '-');?></td>
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
