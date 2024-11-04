<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade" style="min-height: 350px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->productionchange->deal; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-180px'><?php echo $lang->productionchange->applicant.'/'.$lang->productionchange->applicantDept; ?></th>
                    <td colspan='3'>
                        <?php echo rtrim($this->app->user->realname) . ' ' . zget($depts,$productionChangeInfo->applicantDept); ?>
                    </td>
                </tr>
                <!-- 选择上报 则经过部门负责人，不上报则跳过 -->
                <?php if($productionChangeInfo->status == 'wait' and $productionChangeInfo->ifReport == 1):?>
                    <tr>
                        <th class='w-140px'><?php echo $lang->productionchange->constructionDeptConfirm; ?></th>
                        <td colspan='3'>
                            <?php echo zmget($users,$productionChangeInfo->deptConfirmPerson); ?>
                        </td>
                    </tr>
                <?php elseif($productionChangeInfo->status == 'wait' and $productionChangeInfo->ifReport == 2):?>
                    <tr>
                        <th class='w-140px'><?php echo $lang->productionchange->vocalInterfacePerson; ?></th>
                        <td colspan='3'>
                            <?php echo zmget($users,$productionChangeInfo->interfacePerson); ?>
                        </td>
                    </tr>
                <?php endif;?>
                <!--运维方接口人退回再提交 -->
                <?php if($productionChangeInfo->status == 'feedback'):?>
                    <tr>
                        <th class='w-140px'><?php echo $lang->productionchange->implementInterfacePerson; ?></th>
                        <td colspan='3'>
                            <?php echo zmget($users,$productionChangeInfo->dealUser); ?>
                        </td>
                    </tr>
                <?php endif;?>
                <tr>
                    <th><?php echo $lang->productionchange->mailto; ?></th>
                    <td colspan='3'><?php echo html::select('mailto[]', $users, $productionChangeInfo->mailto, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->productionchange->defaultMailto; ?></th>
                    <td colspan='3'><?php echo html::select('defaultMailto[]', $users, $defaultMailto, "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->productionchange->commentCommit; ?></th>
                    <td colspan='3'><?php echo html::textarea('comment', '', "rows='8' class='form-control'"); ?></td>
                </tr>
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
