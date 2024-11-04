<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .table-form > tbody > tr > th {
        width: 137px;
        font-weight: 700;
        text-align: right;
    }
    .alteration{
        display:flex;flex-wrap:wrap;
    }
    .alteration>div{
        margin-right: 20px;
    }
    .reportLeaderDiv{
        margin-left:-65px;
    }
</style>

<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->requirement->changeDetail;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->requirement->alteration;?></th>
                    <td colspan='5'>
                        <div class="alteration">
                            <?php echo html::checkbox('alteration', $this->lang->requirement->alterationList,$changeInfo->alteration,'onclick="return false"');?>
                        </div>
                    </td>
                </tr>
                <?php if(strpos($changeInfo->alteration,'changeTitle') !== false):?>
                <div>
                    <tr class="opinionTitleDiv">
                        <th><?php echo $lang->requirement->name;?></th>
                        <td colspan='5'>
                            <?php if (isset($changeInfo))
                            {
                                echo $changeInfo->requirementTitle;
                            } ?>
                        </td>
                    </tr>
                    <tr class="opinionTitleDiv">
                        <th><?php echo $lang->requirement->changeTitle;?></th>
                        <td colspan='5'><?php echo $changeInfo->changeTitle;?></td>
                    </tr>
                    <td colspan='6' class="opinionTitleDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementOverview') !== false):?>
                    <div>
                        <tr class="opinionOverviewDiv">
                            <th><?php echo $lang->requirement->requirementOverview;?></th>
                            <?php if(strip_tags($changeInfo->requirementOverview) == $changeInfo->requirementOverview):?>
                                <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->requirementOverview;?></td>
                            <?php else:?>
                                <td colspan='5'><?php echo $changeInfo->requirementOverview;?></td>
                            <?php endif;?>
                        </tr>
                        <tr class="opinionOverviewDiv">
                            <th><?php echo $lang->requirement->changeOverview;?></th>
                            <?php if(strip_tags($changeInfo->changeOverview) == $changeInfo->changeOverview):?>
                                <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->changeOverview;?></td>
                            <?php else:?>
                                <td colspan='5'><?php echo $changeInfo->changeOverview;?></td>
                            <?php endif;?>
                        </tr>
                        <td colspan='6' class="opinionOverviewDiv">
                            <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                        </td>
                    </div>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementDeadline') !== false):?>
                    <?php if($changeInfo->changeDeadline != '' && $changeInfo->changeDeadline != '0000-00-00 00:00:00'):?>
                    <div>
                        <tr class="opinionDeadlineDiv">
                            <th><?php echo $lang->requirement->requirementDeadline;?></th>
                            <td colspan='5'><?php echo substr($changeInfo->requirementDeadline,0,10);?></td>
                        </tr>
                        <tr class="opinionDeadlineDiv">
                            <th><?php echo $lang->requirement->changeDeadline;?></th>
                            <td colspan='5'><?php echo substr($changeInfo->changeDeadline,0,10);?></td>
                        </tr>
                        <td colspan='6' class="opinionDeadlineDiv">
                            <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                        </td>
                    </div>
                    <?php endif;?>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementEnd') !== false):?>
                    <?php if($changeInfo->changePlanEnd != '' && $changeInfo->changePlanEnd != '0000-00-00 00:00:00'):?>
                        <div>
                            <tr class="opinionEndDiv">
                                <th><?php echo $lang->requirement->planEnd;?></th>
                                <td colspan='5'><?php echo substr($changeInfo->requirementEnd,0,10);?></td>
                            </tr>
                            <tr class="opinionEndDiv">
                                <th><?php echo $lang->requirement->changePlanEnd;?></th>
                                <td colspan='5'><?php echo substr($changeInfo->changePlanEnd,0,10);?></td>
                            </tr>
                            <td colspan='6' class="opinionEndDiv">
                                <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                            </td>
                        </div>
                    <?php endif;?>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementFile') !== false):?>
                <div>
                    <tr class="fileDiv">
                        <th><?php echo $lang->requirement->fileTitle;?></th>
                        <td colspan='6'>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if($requirement->requirementFiles){
                                        echo $this->fetch('file', 'printFiles', array('files' => $requirement->requirementFiles, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));
                                    }else{
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="fileDiv">
                        <th><?php echo $lang->requirement->changeFile;?></th>
                        <td colspan='6'>
                            <div class='detail'>
                                <div class='detail-content article-content'>
                                    <?php
                                    if($changeInfo->changeFiles){
                                        echo $this->fetch('file', 'printFiles', array('files' => $changeInfo->changeFiles, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));
                                    }else{
                                        echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <td colspan='6' class="fileDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                    <?php endif;?>

                </div>
                <div class="changeReasonDiv">
                    <tr>
                        <th><?php echo $lang->requirement->changeReason;?></th>
                        <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->changeReason;?></td>
                    </tr>
                    <td colspan='6' class="fileDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <tr>
                    <th><?php echo $lang->requirement->affectDemand;?></th>
                    <td colspan='5' style="font-size:15px;line-height: 25px;">
                        <?php
                        foreach ($affectDemands as $value)
                        {
                            echo html::a($this->createLink('demand', 'view', array('id' => $value['id']), '', true), $value['name'], '', "class='iframe' style='color:#0c60e1'");
                        }
                        ?>
                    </td>
                </tr>
                <td colspan='6' class="fileDiv">
                    <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                </td>
<!--                <tr>-->
<!--                    <th>--><?php //echo '';?><!--</th>-->
<!--                    <td colspan='5'>-->
<!--                        <div class="reportLeaderDiv">-->
<!--                            --><?php //echo html::checkbox('reportLeader', $this->lang->requirement->reportLeaderList,$changeInfo->reportLeader,'onclick="return false"');?>
<!--                        </div>-->
<!--                    </td>-->
<!--                </tr>-->
                </tbody>
            </table>
        </form>
    </div>
    <?php if (!empty($bookNodes)): ?>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->requirement->reviewInfo; ?></div>
            <div class="detail-content article-content">
                <?php if (!empty($bookNodes)): ?>
                    <table class="table ops">
                        <tr>
                            <th class="w-100px"><?php echo $lang->requirement->node; ?></th>
                            <td class="w-100px"><?php echo $lang->requirement->dealUser; ?></td>
                            <td class="w-100px"><?php echo $lang->requirement->dealResult; ?></td>
                            <td class="w-180px"><?php echo $lang->requirement->suggestions; ?></td>
                            <td class="w-100px"><?php echo $lang->requirement->dealTime; ?></td>
                        </tr>
                        <tr>
                            <th rowspan="<?php echo $bookNodes[0]->reviewedCount; ?>"><?php echo $lang->requirement->productManager; ?></th>
                            <td><?php echo zmget($users, $bookNodes[0]->reviewers[0]->reviewer, ''); ?></td>
                            <td><?php echo zmget($lang->requirement->reviewResultList, $bookNodes[0]->reviewers[0]->status, ''); ?></td>
                            <td><?php echo $bookNodes[0]->reviewers[0]->comment; ?></td>
                            <td><?php echo $bookNodes[0]->reviewers[0]->reviewTime != '0000-00-00 00:00:00' ? $bookNodes[0]->reviewers[0]->reviewTime : '';?></td>
                        </tr>
                        <?php $reviewedCount = isset($bookNodes[1]) ? $bookNodes[1]->reviewedCount : 0; ?>
                        <?php if ($reviewedCount >= 1): ?>
                            <tr>
                                <th rowspan="<?php echo $reviewedCount; ?>"><?php echo $lang->requirement->deptLeader; ?></th>
                                <td><?php echo zmget($users, $bookNodes[1]->reviewers[0]->reviewer, ''); ?></td>
                                <td><?php echo zget($lang->requirement->reviewResultList, $bookNodes[1]->reviewers[0]->status, ''); ?></td>
                                <td><?php echo $bookNodes[1]->reviewers[0]->comment; ?></td>
                                <td><?php echo $bookNodes[1]->reviewers[0]->reviewTime != '0000-00-00 00:00:00' ? $bookNodes[1]->reviewers[0]->reviewTime : ''; ?></td>
                            </tr>
                            <?php for ($i = 1; $i < $reviewedCount; $i++): ?>
                                <tr>
                                    <td><?php echo zmget($users, $bookNodes[1]->reviewers[$i]->reviewer, ''); ?></td>
                                    <td><?php echo zget($lang->requirement->reviewResultList, $bookNodes[1]->reviewers[$i]->status, ''); ?></td>
                                    <td><?php echo $bookNodes[1]->reviewers[$i]->comment; ?></td>
                                    <td><?php echo $bookNodes[1]->reviewers[$i]->reviewTime != '0000-00-00 00:00:00' ? $bookNodes[1]->reviewers[$i]->reviewTime : ''; ?></td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </table>
                <?php else: ?>
                    <?php echo "<div class='text-center text-muted'>" . $lang->noData . '</div>'; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<!--    <p class='form-actions text-center' colspan='6'>--><?php //echo html::submitButton($this->lang->requirement->closeView) . html::backButton();?><!--</p>-->
</div>
<?php js::set('chooseAlteration', $lang->requirement->chooseAlteration);?>
<?php include '../../common/view/footer.html.php';?>
