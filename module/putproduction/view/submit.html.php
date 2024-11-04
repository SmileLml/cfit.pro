<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->putproduction->submit;?></h2>
        </div>

       <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span ><?php echo $lang->putproduction->submitMsgTip; ?>:<br/></span>
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
                            <th><?php echo $lang->putproduction->dealMessage;?></th>
                            <td colspan='2' id="suggestTd"><?php echo html::textarea('comment', '', "class='form-control'");?></td>
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