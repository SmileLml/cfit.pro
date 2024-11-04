<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade" style="min-height: 350px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo zget($lang->productionchange,$productionChangeInfo->status).' '.$lang->productionchange->dealReview; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <!--部门审批节点显示页面 -->
                <?php if(in_array($productionChangeInfo->status,$lang->productionchange->resultListOnly)):?>
                    <tr>
                        <th><?php echo $lang->productionchange->dealResult; ?></th>
                        <td colspan='2' class="required"><?php echo html::select('result', $lang->productionchange->resultList, '', "class='form-control chosen' onchange='changeResult(this.value)'"); ?></td>
                    </tr>
                <?php endif;?>
                <!--业务接口人显示页面 -->
                <?php if($productionChangeInfo->status == 'vocalInterfacePerson'):?>
                <tr>
                    <th><?php echo $lang->productionchange->dealResult;?></th>
                    <td class="required"><?php echo html::select('result', $lang->productionchange->resReportList, '', "class='form-control chosen' onchange='changeVocalInterfacePerson(this.value)'"); ?></td>
                    <td class="required vocalDeptPersonCla" colspan='2' hidden>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->productionchange->vocalDeptPerson;?></span>
                            <?php echo html::select('vocalDeptPerson[]', $users, $deptManagers, "class='form-control chosen' multiple"); ?>
                        </div>
                    </td>
                </tr>
                <?php endif;?>

                <!--运维方接口人显示页面 -->
                <?php if($productionChangeInfo->status == 'implementInterfacePerson'):?>
                <tr>
                    <th><?php echo $lang->productionchange->dealResult;?></th>
                    <td class="required"><?php echo html::select('result', $lang->productionchange->resRejectList, '', "class='form-control chosen' onchange='changeImplementInterfacePerson(this.value)'"); ?></td>
                    <td class="implementInterfacePersonCla" colspan='2' hidden>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->productionchange->executanter;?></span>
                            <?php echo html::select('executanter[]', $users, '', "class='form-control chosen' multiple"); ?>
                        </div>
                        <div class='input-group required'>
                            <span class='input-group-addon'><?php echo $lang->productionchange->reviewPerson;?></span>
                            <?php echo html::select('reviewPerson[]', $users, '', "class='form-control chosen' multiple"); ?>
                        </div>
                    </td>
                </tr>
                <?php endif;?>

                <!--实施人员1/复核人员显示页面2 -->
                <?php if($productionChangeInfo->status == 'feedbackAndRepeatConfirm'):?>
                    <?php if($productionChangeInfo->personType == 1):?>
                        <tr>
                            <th><?php echo $lang->productionchange->actualOnlineTime;?></th>
                            <td class="required"><?php echo html::input('actualOnlineTime', $productionChangeInfo->actualOnlineTime != '0000-00-00 00:00:00' ? $productionChangeInfo->actualOnlineTime : '', "class='form-control form-datetime'");?></td>
                            <td class="required" colspan='2'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><?php echo $lang->productionchange->operationDept;?></span>
                                    <?php echo html::select('operationDept[]', $users, $productionChangeInfo->operationDept, "class='form-control chosen' multiple"); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <!--实施记录 -->
                            <th><?php echo $lang->productionchange->record;?></th>
                            <td colspan='3' class="required"><?php echo html::textarea('record', $productionChangeInfo->record, "rows='6' class='form-control'"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productionchange->remark; ?></th>
                            <td colspan='3' class="comment"><?php echo html::textarea('remark', $productionChangeInfo->remark, "rows='8' class='form-control'"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productionchange->mailto; ?></th>
                            <td colspan='3'><?php echo html::select('mailto[]', $users, $productionChangeInfo->mailto, "class='form-control chosen' multiple"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productionchange->defaultMailto; ?></th>
                            <td colspan='3'><?php echo html::select('defaultMailto[]', $users, $defaultMailto, "class='form-control chosen' multiple"); ?></td>
                        </tr>
                    <?php else:?>
                        <tr>
                            <th><?php echo $lang->productionchange->actualOnlineTime;?></th>
                            <td class="required"><?php echo html::input('actualOnlineTime', $productionChangeInfo->actualOnlineTime != '0000-00-00 00:00:00' ? $productionChangeInfo->actualOnlineTime : '', "class='form-control form-datetime'");?></td>
                            <td class="operationDeptCla" colspan='2' hidden>
                                <div class='input-group required'>
                                    <span class='input-group-addon'><?php echo $lang->productionchange->operationDept;?></span>
                                    <?php echo html::select('operationDept[]', $users, $productionChangeInfo->operationDept, "class='form-control chosen' multiple"); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <!--实施记录 -->
                            <th><?php echo $lang->productionchange->record;?></th>
                            <td colspan='3' class="required"><?php echo html::textarea('record', $productionChangeInfo->record, "rows='6' class='form-control'"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productionchange->remark; ?></th>
                            <td colspan='3' class="comment"><?php echo html::textarea('remark', $productionChangeInfo->remark, "rows='8' class='form-control'"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productionchange->dealResult;?></th>
                            <td class="required"><?php echo html::select('result', $lang->productionchange->feedbackAndRepeatConfirmList, '', "class='form-control chosen' onchange='changeFeedbackResult(this.value)'"); ?></td>
                            <td colspan='2'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><?php echo $lang->productionchange->mailto;?></span>
                                    <?php echo html::select('mailto[]', $users, $productionChangeInfo->mailto, "class='form-control chosen' multiple"); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->productionchange->defaultMailto; ?></th>
                            <td colspan='3'><?php echo html::select('defaultMailto[]', $users, $defaultMailto, "class='form-control chosen' multiple"); ?></td>
                        </tr>
                    <?php endif;?>
                <?php endif;?>
                <?php if($productionChangeInfo->status != 'feedbackAndRepeatConfirm'):?>
                <tr>
                    <th><?php echo $lang->productionchange->dealComment; ?></th>
                    <td colspan='3' class="comment"><?php echo html::textarea('comment', '', "rows='8' class='form-control'"); ?></td>
                </tr>
                <tr class="mailtoCla">
                    <th><?php echo $lang->productionchange->mailto; ?></th>
                    <td colspan='3'><?php echo html::select('mailto[]', $users, $productionChangeInfo->mailto, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr class="mailtoCla">
                    <th><?php echo $lang->productionchange->defaultMailto; ?></th>
                    <td colspan='3'><?php echo html::select('defaultMailto[]', $users, $defaultMailto, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <?php endif;?>
                <tr>
                    <td style="padding-top: 50px;" class='form-actions text-center' colspan='4'>
                        <?php echo html::submitButton('提交'); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>
    //部门相关处理
    function changeResult($val)
    {
        var statusMain = '<?php echo $productionChangeInfo->status;?>';
        //不通过，处理意见必填
        if($val == '2')
        {
            $('.comment').addClass('required')
            $('.mailtoCla').show();
        }else{
            //待验证环节选择通过则隐藏抄送人
            if(statusMain == 'waitValidate')
            {
                $('.mailtoCla').hide();
            }else{
                $('.mailtoCla').show();
            }
            $('.comment').removeClass('required')
        }
    }

    //业务接口人处理
    function changeVocalInterfacePerson($val)
    {
        //标识上报
        if($val == '6')
        {
            $('.vocalDeptPersonCla').show();
        }else{
            $('.vocalDeptPersonCla').hide();
        }

        //不通过，处理意见必填
        if($val == '2')
        {
            $('.comment').addClass('required')
        }else{
            $('.comment').removeClass('required')
        }

    }

    //实施接口人(运维方接口人)
    function changeImplementInterfacePerson($val)
    {
        if($val == '1')
        {
            $('.implementInterfacePersonCla').show();
        }else{
            $('.implementInterfacePersonCla').hide();
        }

        //不通过，处理意见必填
        if($val != '1')
        {
            $('.comment').addClass('required')
        }else{
            $('.comment').removeClass('required')
        }
    }

    //实施人/复核人
    function changeFeedbackResult($val)
    {
        //标识上报
        if($val == '6')
        {
            $('.operationDeptCla').show();
        }else{
            $('.operationDeptCla').hide();
        }

        //不通过，处理意见必填
        if($val == '2')
        {
            $('.comment').addClass('required')
        }else{
            $('.comment').removeClass('required')
        }
    }


</script>

