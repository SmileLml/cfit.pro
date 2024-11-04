<?php include '../../common/view/header.html.php';?>
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
            <h2><?php echo $lang->opinion->dealReview;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->opinion->alteration;?></th>
                    <td colspan='5'>
                        <div class="alteration">
                            <?php echo html::checkbox('alteration', $this->lang->opinion->alterationList,$changeInfo->alteration,'onclick="return false"');?>
                        </div>
                    </td>
                </tr>
                <?php if(strpos($changeInfo->alteration,'changeTitle') !== false):?>
                    <div>
                        <tr class="opinionTitleDiv">
                            <th><?php echo $lang->opinion->name;?></th>
                            <td colspan='5'>
                                <?php if (isset($changeInfo))
                                {
                                    echo $changeInfo->opinionTitle;
                                } ?>
                            </td>
                        </tr>
                        <tr class="opinionTitleDiv">
                            <th><?php echo $lang->opinion->changeTitle;?></th>
                            <td colspan='5'><?php echo $changeInfo->changeTitle;?></td>
                        </tr>
                        <td colspan='6' class="opinionTitleDiv">
                            <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                        </td>
                    </div>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'opinionBackground') !== false):?>
                    <div>
                        <tr class="opinionBackgroundDiv">
                            <th><?php echo $lang->opinion->opinionBackground;?></th>
                            <?php if(strip_tags($changeInfo->opinionBackground) == $changeInfo->opinionBackground):?>
                                <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->opinionBackground;?></td>
                            <?php else:?>
                                <td colspan='5'><?php echo $changeInfo->opinionBackground;?></td>
                            <?php endif;?>
                        </tr>
                        <tr class="opinionBackgroundDiv">
                            <th><?php echo $lang->opinion->changeBackground;?></th>
                            <?php if(strip_tags($changeInfo->changeBackground) == $changeInfo->changeBackground):?>
                                <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->changeBackground;?></td>
                            <?php else:?>
                                <td colspan='5'><?php echo $changeInfo->changeBackground;?></td>
                            <?php endif;?>
                        </tr>
                        <td colspan='6' class="opinionBackgroundDiv">
                            <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                        </td>
                    </div>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'opinionOverview') !== false):?>
                    <div>
                        <tr class="opinionOverviewDiv">
                            <th><?php echo $lang->opinion->opinionOverview;?></th>
                            <?php if(strip_tags($changeInfo->opinionOverview) == $changeInfo->opinionOverview):?>
                                <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->opinionOverview;?></td>
                            <?php else:?>
                                <td colspan='5'><?php echo $changeInfo->opinionOverview;?></td>
                            <?php endif;?>
                        </tr>
                        <tr class="opinionOverviewDiv">
                            <th><?php echo $lang->opinion->changeOverview;?></th>
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
                <?php if(strpos($changeInfo->alteration,'opinionDeadline') !== false):?>
                    <?php if($changeInfo->changeDeadline != '' && $changeInfo->changeDeadline != '0000-00-00 00:00:00'):?>
                        <div>
                            <tr class="opinionDeadlineDiv">
                                <th><?php echo $lang->opinion->opinionDeadline;?></th>
                                <td colspan='5'><?php echo $changeInfo->opinionDeadline;?></td>
                            </tr>
                            <tr class="opinionDeadlineDiv">
                                <th><?php echo $lang->opinion->changeDeadline;?></th>
                                <td colspan='5'><?php echo $changeInfo->changeDeadline;?></td>
                            </tr>
                            <td colspan='6' class="opinionDeadlineDiv">
                                <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                            </td>
                        </div>
                    <?php endif;?>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'opinionFile') !== false):?>
                    <div>
                        <tr class="fileDiv">
                            <th><?php echo $lang->opinion->opinionFile;?></th>
                            <td colspan='5'>
                                <div class='detail'>
                                    <div class='detail-content article-content'>
                                        <?php
                                        if($opinion->opinionFiles){
                                            echo $this->fetch('file', 'printFiles', array('files' => $opinion->opinionFiles, 'fieldset' => 'false', 'object' => null, 'canOperate' => false, 'isAjaxDel' => false));
                                        }else{
                                            echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="fileDiv">
                            <th><?php echo $lang->opinion->changeFile;?></th>
                            <td colspan='5'>
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
                    </div>
                <?php endif;?>
                <div>
                    <tr>
                        <th><?php echo $lang->opinion->affectRequirement;?></th>
                        <td colspan='5' style="font-size:15px;line-height: 25px;">
                            <?php
                            foreach ($affectRequirementArr as $value)
                            {
                                echo html::a($this->createLink('requirement', 'view', array('id' => $value['id']), '', true), $value['name'], '', "class='iframe' style='color:#0c60e1'");
                            }
                            ?>
                        </td>
                    </tr>
                    <td colspan='6' class="fileDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <tr>
                    <th><?php echo $lang->opinion->affectDemand;?></th>
                    <td colspan='5' style="font-size:15px;line-height: 25px;">
                        <?php
                        foreach ($affectDemandArr as $value)
                        {
                            echo html::a($this->createLink('demand', 'view', array('id' => $value['id']), '', true), $value['name'], '', "class='iframe' style='color:#0c60e1'");
                        }
                        ?>
                    </td>
                </tr>
                <td colspan='6' class="fileDiv">
                    <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                </td>
                <div class="changeReasonDiv">
                    <tr>
                        <th><?php echo $lang->opinion->changeReason;?></th>
                        <td colspan='5' style="white-space: pre-line"><?php echo $changeInfo->changeReason;?></td>
                    </tr>
                    <td colspan='6' class="fileDiv">
                        <div style='width:96%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>
                <!--                <tr>-->
                <!--                    <th>--><?php //echo '';?><!--</th>-->
                <!--                    <td colspan='5'>-->
                <!--                        <div class="reportLeaderDiv">-->
                <!--                            --><?php //echo html::checkbox('reportLeader', $this->lang->opinion->reportLeaderList,$changeInfo->reportLeader,'onclick="return false"');?>
                <!--                        </div>-->
                <!--                    </td>-->
                <!--                </tr>-->
                </tbody>
            </table>

        </form>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->opinion->dealResult;?></th>
                    <td colspan='6' class="required"><?php echo html::select('status', $lang->opinion->resultList, '', "class='form-control chosen' onchange='selectResult(this.value)'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->opinion->suggestion;?></th>
                    <td colspan='6' id ='commentTd'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
                </tr>
<!--                --><?php //if($nextDealNode != 'deptLeader'):?>
<!--                    <tr>-->
<!--                        <th>--><?php //echo '';?><!--</th>-->
<!--                        <td colspan='6'>-->
<!--                            <div class="reportLeaderDiv">-->
<!--                                --><?php //echo html::checkbox('reportLeader', $this->lang->opinion->reportLeaderList,2);?>
<!--                            </div>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                --><?php //endif;?>
                <tr>
                    <td class='form-actions text-center' colspan='6'><?php echo html::submitButton($this->lang->opinion->submit) . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
    //选择通过 则操作备注必填
    function selectResult(val){
        if(val == 'reject')
        {
            $('#commentTd').addClass('required');
            $('.reportLeaderDiv').hide();
        }else{
            $('#commentTd').removeClass('required');
            $('.reportLeaderDiv').show();
        }
    }
</script>

