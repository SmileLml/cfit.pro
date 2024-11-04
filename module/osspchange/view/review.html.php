<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->osspchange->review;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->osspchange->comment;?></th>
                    <td colspan='4' class="required"><?php echo html::textarea('comment', '', 'class="form-control""');?></td>
                </tr>
                <tr>
                    <th class='w-120px'><?php echo $lang->osspchange->result;?></th>
                    <td colspan='4'><?php echo html::select('result', $result,'', 'class="form-control  chosen" required');?></td>
                </tr>
                <tr>
                    <th class="w-120px"></th>
                    <td class='form-actions text-center' colspan='4'>
                        <!--保存初始审核节点-->
                        <input type="hidden" name = "version" value="<?php echo $osspchange->version; ?>">
                        <input type="hidden" name = "status" value="<?php echo $osspchange->status; ?>">
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'?>
