<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentpublic->delete; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th></th>
                        <?php if($componentpublic->usedNum == 0):?>
                            <td style="font-weight:bold"><?php echo $lang->componentpublic->deleteCanTip; ?></td>
                        <?php else:?>
                            <td style="font-weight:bold"><?php echo $lang->componentpublic->deleteTip; ?></td>
                        <?php endif;?>
                    </tr>
                    <tr>
                        <?php if($componentpublic->usedNum == 0):?>
                            <th><?php echo $lang->componentpublic->comment; ?></th>
                            <td><?php echo html::textarea('comment', '', "class='form-control'"); ?></td>
                        <?php endif;?>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
    <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php if($componentpublic->usedNum == 0){
                    echo html::submitButton();
                }?>
                <?php echo html::backButton(); ?>
        </tr>
        </tbody>
    </table>
    </form>
</div>
</div>
<script>

</script>


<?php include '../../common/view/footer.html.php'; ?>
