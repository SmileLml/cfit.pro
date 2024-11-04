<?php include '../../../common/view/header.html.php'; ?>
<?php include '../../../common/view/datepicker.html.php'; ?>
<?php include '../../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $problem->code; ?></span>
                <?php echo isonlybody() ? ("<span title='$problem->code'>" . $lang->problem->change . '</span>') : html::a($this->createLink('problem', 'view', "problemID=$problem->id"), $problem->name); ?>
                <?php if (!isonlybody()): ?>
                    <small><?php echo $lang->arrow . $lang->problem->delay; ?></small>
                <?php endif; ?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class="w-120px"><?php echo $lang->problem->changeOriginalResolutionDate; ?></th>

                    <td colspan='2'><?php echo html::input('changeOriginalResolutionDate', $problem->PlannedTimeOfChange, "class='form-control form-date' disabled");?></td>
                    <td class="hidden"><?php echo html::input('changeOriginalResolutionDate', $problem->PlannedTimeOfChange, "class='form-control form-date'");?></td>
                <tr>
                    <th><?php echo $lang->problem->changeResolutionDate; ?></th>
                    <td colspan='2' class="required"><?php echo html::input('changeResolutionDate',$problem->PlannedTimeOfChange, "class='form-control form-datetime'");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->problem->changeReason; ?></th>
                    <td colspan='2' class="required"><?php echo html::textarea('changeReason', '', "rows='6' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->problem->changeCommunicate; ?></th>
                    <td colspan='2' class="required"><?php echo html::textarea('changeCommunicate', '', "rows='6' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->comment; ?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'"); ?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center'
                        colspan='3'><?php echo html::submitButton('提交') . html::backButton(); ?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
$(function()
{
/*开始时间*/
$(".form-datetime").datetimepicker(
'setStartDate','<?php echo date(DT_DATE1)?>'
);
});
</script>
<?php include '../../../common/view/footer.html.php'; ?>
