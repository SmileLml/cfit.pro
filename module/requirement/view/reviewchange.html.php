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
    <?php if(!$followOpinion):?>
        <h2 style="color:black;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $lang->requirement->changeIng;?></h2>
    <?php else:?>
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->requirement->dealReview;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->requirement->alteration;?></th>
                    <td colspan='8'>
                        <div class="alteration">
                            <?php echo html::checkbox('alteration', $this->lang->requirement->alterationList,$changeInfo->alteration,'onclick="return false"');?>
                        </div>
                    </td>
                </tr>
                <?php if(strpos($changeInfo->alteration,'changeTitle') !== false):?>
                    <div>
                        <tr class="opinionTitleDiv">
                            <th><?php echo $lang->requirement->name;?></th>
                            <td colspan='8'>
                                <?php if (isset($changeInfo))
                                {
                                    echo $changeInfo->requirementTitle;
                                } ?>
                            </td>
                        </tr>
                        <tr class="opinionTitleDiv">
                            <th><?php echo $lang->requirement->changeTitle;?></th>
                            <td colspan='8'><?php echo $changeInfo->changeTitle;?></td>
                        </tr>
                        <td colspan='8' class="opinionTitleDiv">
                            <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                        </td>
                    </div>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementOverview') !== false):?>
                    <div>
                        <tr class="opinionOverviewDiv">
                            <th><?php echo $lang->requirement->requirementOverview;?></th>
                            <td colspan='8'><?php echo $changeInfo->requirementOverview;?></td>
                        </tr>
                        <tr class="opinionOverviewDiv">
                            <th><?php echo $lang->requirement->changeOverview;?></th>
                            <td colspan='8' style="white-space: pre-line"><?php echo $changeInfo->changeOverview;?></td>
                        </tr>
                        <td colspan='8' class="opinionOverviewDiv">
                            <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                        </td>
                    </div>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementDeadline') !== false):?>
                    <?php if($changeInfo->changeDeadline != '' && $changeInfo->changeDeadline != '0000-00-00 00:00:00'):?>
                        <div>
                            <tr class="opinionDeadlineDiv">
                                <th><?php echo $lang->requirement->requirementDeadline;?></th>
                                <td colspan='8'><?php echo substr($changeInfo->requirementDeadline,0,10);?></td>
                            </tr>
                            <tr class="opinionDeadlineDiv">
                                <th><?php echo $lang->requirement->changeDeadline;?></th>
                                <td colspan='8'><?php echo substr($changeInfo->changeDeadline,0,10);?></td>
                            </tr>
                            <td colspan='8' class="opinionDeadlineDiv">
                                <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                            </td>
                        </div>
                    <?php endif;?>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementEnd') !== false):?>
                    <?php if($changeInfo->changePlanEnd != '' && $changeInfo->changePlanEnd != '0000-00-00 00:00:00'):?>
                        <div>
                            <tr class="opinionEndDiv">
                                <th><?php echo $lang->requirement->planEnd;?></th>
                                <td colspan='8'><?php echo substr($changeInfo->requirementEnd,0,10);?></td>
                            </tr>
                            <tr class="opinionEndDiv">
                                <th><?php echo $lang->requirement->changePlanEnd;?></th>
                                <td colspan='8'><?php echo substr($changeInfo->changePlanEnd,0,10);?></td>
                            </tr>
                            <td colspan='8' class="opinionEndDiv">
                                <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                            </td>
                        </div>
                    <?php endif;?>
                <?php endif;?>
                <?php if(strpos($changeInfo->alteration,'requirementFile') !== false):?>
                <div>
                    <tr class="fileDiv">
                        <th><?php echo $lang->requirement->fileTitle;?></th>
                        <td colspan='8'>
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
                        <td colspan='8'>
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
                    <td colspan='8' class="fileDiv">
                        <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                    <?php endif;?>

                </div>

                <div class="changeReasonDiv">
                    <tr>
                        <th><?php echo $lang->requirement->changeReason;?></th>
                        <td colspan='8' style="white-space: pre-line"><?php echo $changeInfo->changeReason;?></td>
                    </tr>
                    <td colspan='8' class="fileDiv">
                        <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                </div>

               <?php if(!$isGuestcn):?>
                    <tr>
                        <th><?php echo $lang->requirement->affectDemand;?></th>
                        <td colspan='8' style="font-size:15px;line-height: 25px;">
                            <?php
                            foreach ($affectDemands as $value)
                            {
                                echo html::a($this->createLink('demand', 'view', array('id' => $value['id']), '', true), $value['name'], '', "class='iframe' style='color:#0c60e1'");
                            }
                            ?>
                        </td>
                    </tr>

                    <td colspan='8' class="fileDiv">
                        <div style='width:106%;margin-left:5%;border:1px dashed #dacaca'></div>
                    </td>
                <?php endif;?>
                </tbody>
            </table>
        </form>
        <br />
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->requirement->dealResult;?></th>
                    <td colspan='6' class="required"><?php echo html::select('status', $lang->requirement->reviewList, '', "class='form-control chosen' onchange='selectResult(this.value)'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->requirement->reviewnodecomment;?></th>
                    <td colspan='6' id ='commentTd'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='6'><?php echo html::submitButton($this->lang->requirement->submitBtn) . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php endif;?>
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

