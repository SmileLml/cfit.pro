<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $demand->code; ?></span>
                <?php echo isonlybody() ? ("<span title='$demand->code'>" . $lang->demandinside->start . '</span>') : html::a($this->createLink('demand', 'view', "demandID=$demand->id"), $demand->name); ?>
                <?php if (!isonlybody()): ?>
                    <small><?php echo $lang->arrow . $lang->demandinside->start; ?></small>
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
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demandinside->submitBtn); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
        <hr class='small'/>
        <div class='main'><?php include '../../common/view/action.html.php'; ?></div>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
