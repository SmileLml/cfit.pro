<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
    <div id="mainContent" class="main-content fade" style="min-height: 350px;">
        <div class="center-block">
            <div class="main-header">
                    <h2><?php echo $lang->environmentorder->confirm; ?></h2>
            </div>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr id="deal" >
                        <th><?php echo $lang->environmentorder->isConfirm;?></th>
                        <td colspan='4'><?php echo html::select('dealResult', $lang->environmentorder->confirmList , '', "class='form-control chosen' required  onchange='changeDealResult();'");?></td>
                        <td></td>
                    </tr>
                    <tr id="assign" style="display:none">
                        <th><?php echo $lang->environmentorder->isAssign;?></th>
                        <td colspan='4' id="assign-td"><?php echo html::select('assignResult', $lang->environmentorder->assignList , '', "class='form-control chosen' required  onchange='changeAssignResult();'");?></td>
                        <td></td>
                    </tr>

                    <tr id="suggest" >
                        <th><?php echo $lang->environmentorder->dealOpinion;?></th>
                        <td  id="suggest-td" colspan='4'><?php echo html::textarea('comment', '', "class='form-control' style='height:150px'");?></td>
                    </tr>

                    <tr id="executors"  style="display:none;">
                        <th><?php echo $lang->environmentorder->executor;?></th>
                        <td colspan='4' id="executor-td"><?php echo html::select('executor', array(''=>'')+$executor , '', "class='form-control  chosen' required");?></td>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='5'>
                            <?php echo html::submitButton() . html::backButton();?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>

        </div>
    </div>
<?php include '../../common/view/footer.html.php'; ?>