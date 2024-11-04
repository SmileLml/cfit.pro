<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->osspchange->close;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->osspchange->fileInfo;?></th>
                    <td colspan='4'><?php echo html::textarea('fileInfo', '', 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->reviewResult;?></th>
                    <td colspan='4'><?php echo html::select('closeResult', $this->lang->osspchange->interfaceClosedList, '', 'class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th class='w-180px'><?php echo $lang->osspchange->closeComment;?></th>
                    <td colspan='4'><?php echo html::textarea('closeComment', '', 'class="form-control" required');?></td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->notifyPerson;?></th>
                    <td colspan='4'><?php echo html::select('notifyPerson', $users,$QMDmanager, 'multiple class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th class="w-120px"></th>
                    <td class='form-actions text-center' colspan='4'>
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'?>
