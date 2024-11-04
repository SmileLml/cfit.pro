<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade" style="min-height: 350px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplan->yearReview; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-140px'><?php echo $lang->projectplan->submitBy . '/' . $lang->projectplan->dept; ?></th>
                    <td colspan='3'>
                        <?php echo rtrim($this->app->user->realname . ' / ' . zget($deptPairs, $this->app->user->dept, $lang->noData), '/'); ?>
                    </td>
                </tr>


                <?php if (in_array($plan->rejectStatus, $this->lang->projectplan->leaderStage)): ?>
                    <?php if (isset($planPerson)): ?>
                        <tr>
                            <th class='w-140px'><?php echo $lang->projectplan->buildLeader; ?></th>
                            <td colspan='3'>
                                <?php echo rtrim(zget($users, $manager)); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th class='w-140px'><?php echo $lang->projectplan->deptLeader; ?></th>
                            <td colspan='3'>
                                <?php echo rtrim(zget($users, $manager)); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (isset($planPerson)): ?>
                        <tr>
                            <th class='w-140px'><?php echo $lang->projectplan->buildPerson; ?></th>
                            <td colspan='3'>
                                <?php echo rtrim(zget($users, $planPerson)); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th class='w-140px'><?php echo $lang->projectplan->deptLeader; ?></th>
                            <td colspan='3'>
                                <?php echo rtrim(zget($users, $manager)); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>



                <?php if (in_array($reviewStage, $this->lang->projectplan->jumpStage)): ?>
                    <?php if (isset($planPerson)): ?>
                        <td style="display:none"><?php echo html::input('isNeedDeptLeader', 'no', "class='form-control'");?></td>
                    <?php else: ?>
                        <tr>
                            <th class='w-140px'><?php echo $lang->projectplan->isNeedDeptLeader; ?></th>
                            <td colspan='3'>
                                <?php echo html::radio('isNeedDeptLeader', $lang->projectplan->reviewStageList, "yes"); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <tr>
                    <th><?php echo $lang->projectplan->mailto; ?></th>
                    <td colspan='3'><?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->projectplan->commentCommit; ?></th>
                    <td colspan='3'><?php echo html::textarea('commentCommit', '', "rows='8' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <td style="padding-top: 50px;" class='form-actions text-center' colspan='4'>
                        <?php echo html::submitButton(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<style>
    .review-name {
        padding-left: 10px;
        color: #585858;
    }
</style>
<?php include '../../common/view/footer.html.php'; ?>
