<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
    <div id="mainContent" class="main-content fade" style="min-height: 400px; max-height:  480px;">
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->localesupport->submit;?></h2>
            </div>

            <?php if(!$checkRes['result']):?>
                <div class="tipMsg text-danger help-text red">
                    <span ><?php echo $lang->localesupport->submitMsgTip; ?>:<br/></span>
                    <span>
                    <?php if(is_array($checkRes['message'])): ?>
                        <?php foreach ($checkRes['message'] as $val):?>
                            <?php echo $val . '<br/>'; ?>
                        <?php endforeach;?>
                    <?php else: ?>
                        <?php echo $checkRes['message']; ?>
                    <?php endif;?>
              </span>
                </div>
            <?php else: ?>
                <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th><?php echo $lang->localesupport->remarkComment;?></th>
                            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                        </tr>

                        <tr>
                            <td class='form-actions text-center' colspan='3'>
                                <?php echo html::submitButton('提交') . html::backButton();?>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </form>

            <?php endif;?>

        </div>
    </div>
<?php include '../../common/view/footer.html.php';?>