<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datepicker.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
    .table-form>tbody>tr>th {
        width: 100px;
        font-weight: 700;
        text-align: right;
        padding-bottom: 40px;
    }
    .table-form>tbody>tr>td {
        padding-bottom: 40px;
    }
</style>

<div id="mainContent" class="main-content fade" style="height:450px">
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->problem->feedbackTimeEdit; ?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->problem->deptPassTime; ?></th>
                        <td colspan="2"><?php echo html::input('deptPassTime', $problem->deptPassTime, "class='form-control form-datetime'"); ?></td>
                        <th><?php echo $lang->problem->innovationPassTime; ?></th>
                        <td colspan="2"><?php echo html::input('innovationPassTime', $problem->innovationPassTime, "class='form-control form-datetime'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->problem->feedbackStartTimeInside; ?></th>
                        <td colspan="2"><?php echo html::input('feedbackStartTimeInside', $problem->feedbackStartTimeInside, "class='form-control form-datetime'"); ?></td>
                        <th><?php echo $lang->problem->feedbackStartTimeOutside; ?></th>
                        <td colspan="2"><?php echo html::input('feedbackStartTimeOutside', $problem->feedbackStartTimeOutside, "class='form-control form-datetime'"); ?></td>
                    </tr>
                    <tr>
                        <th class='form-actions text-center' colspan='1'>
                            <?php echo html::checkbox('isChangeFeedbackTime', [1 => $lang->problem->isChangeFeedbackTime], $problem->isChangeFeedbackTime); ?>
                        </th>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='6'><?php echo html::submitButton() . html::backButton(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
</div>
<?php include '../../../common/view/footer.html.php';?>

