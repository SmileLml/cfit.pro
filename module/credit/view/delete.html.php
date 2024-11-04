<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $info->code;?></span>
                <?php echo isonlybody() ? ("<span title='$info->code'>" . $lang->credit->delete . '</span>') : html::a($this->createLink('credit', 'view', "creditID=$info->id"), $info->summary);?>
                <?php if(!isonlybody()):?>
                    <small><?php echo $lang->arrow . $lang->credit->delete;?></small>
                <?php endif;?>
            </h2>
        </div>
        <?php if(!$checkRes['result']):?>
            <div class="tipMsg red">
                <span><?php echo $checkRes['message']; ?></span>
            </div>
        <?php else:?>
            <form method='post' target='hiddenwin'>
                <table class='table table-form'>
                    <tr>
                        <th><?php echo $lang->credit->remark;?></th>
                        <td colspan='2'><?php echo html::textarea('remark', '', "rows='8' class='form-control'");?></td>
                    </tr>
                    <tr>
                        <td colspan='3' class='text-center form-actions'>
                            <?php echo html::submitButton();?>
                        </td>
                    </tr>
                </table>
            </form>
        <?php endif;?>
        <hr class='small' />
        <div class='main'><?php include '../../common/view/action.html.php';?></div>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
