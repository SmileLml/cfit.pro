<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <?php if(isset($canChange) && !$canChange):?>
        <h2 style="color:red;text-align: center;margin-top:-3%;letter-spacing:5px;"><?php echo $lang->demand->startTip;?></h2>
    <?php else:?>
    <div class="center-block">
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $demand->code; ?></span>
                <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demand->start . '</span>') : html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->name); ?>
                <?php if (!isonlybody()): ?>
                    <small><?php echo $lang->arrow . $lang->demand->start; ?></small>
                <?php endif; ?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr class='hidden'>
                    <td><input name='lastStatus' value=''/></td>
                    <td><input name='status' value='<?php echo $demand->lastStatus; ?>'/></td>
                </tr>
                <tr>
                    <th><?php echo $lang->comment; ?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demand->submitBtn); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
        <hr class='small'/>
        <div class='main'><?php include '../../common/view/action.html.php'; ?></div>
    </div>
    <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php'; ?>
