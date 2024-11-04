<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $demand->code;?></span>
                <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demandinside->close . '</span>') : html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->name);?>
                <?php if(!isonlybody()):?>
                    <small><?php echo $lang->arrow . $lang->demandinside->close;?></small>
                <?php endif;?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->demandinside->thisRemarks; ?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'"); ?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center'
                        colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn) . html::backButton(); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
        <hr class='small'/>
        <div class='main'><?php include '../../common/view/action.html.php'; ?></div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
