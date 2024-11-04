<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
    <div id="mainContent" class="main-content fade">
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->component->changeteamreviewer; ?></h2>
            </div>
                <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                    <table class="table table-form">
                        <tbody>
                        <tr id='teamMemberTr'>
                            <th><?php echo $lang->component->teamMember; ?></th>
                            <td class="required"><?php echo html::select('teamMember[]', $users, $selectedViewers, "class='form-control chosen' multiple"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->component->mailto; ?></th>
                            <td><?php echo html::select('mailto[]', $users, "", "class='form-control chosen' multiple"); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo $lang->component->dealcomment; ?></th>
                            <td colspan='2'><?php echo html::textarea('dealcomment', '', "class='form-control'"); ?></td>
                        </tr>
                        <tr>
                            <td class='form-actions text-center' colspan='3'>
                                <!--保存初始审核节点-->
                                <input type="hidden" name = "changeVersion" value="<?php echo $component->changeVersion; ?>">
                                <input type="hidden" name = "reviewStage" value="<?php echo $component->reviewStage; ?>">
                                <?php echo html::submitButton() . html::backButton(); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
        </div>
    </div>
<?php include '../../common/view/footer.html.php'; ?>