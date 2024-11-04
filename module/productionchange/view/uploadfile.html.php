<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $lang->productionchange->uploadFile;?></span>
                <?php if(!isonlybody()):?>
                    <small><?php echo $lang->arrow . $lang->productionchange->uploadFile;?></small>
                <?php endif;?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post'>
            <table class='table table-form'>
                <tr>
                    <!--附件 -->
                    <th><?php echo $lang->files;?></th>
                    <input type="hidden" name = "isUpload" value='true'>
                    <td colspan='2' class="required"><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                </tr>
                <tr>
                    <td colspan='2' class='text-center form-actions'>
                        <?php echo html::submitButton($this->lang->productionchange->submitBtn);?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
