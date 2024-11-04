<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
    <style>
        /*tbody {*/
        /*    height: 300px;*/
        /*}*/
        /*td.required:after {*/
        /*    top: 30px;*/
        /*    right: -5px;*/
        /*}*/
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

    <div id="mainContent" class="main-content fade" style="height:430px">
        <div class="center-block">
            <div class="main-header">
                <h2><?php echo $lang->requirement->defend; ?></h2>
            </div>
            <!--      multipart/form-data-->
            <form class="load-indicator main-form form-ajax" method='post' enctype='' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->requirement->insideStart; ?></th>
                        <td colspan="2"><?php echo html::input('feekBackStartTime', $requirement->feekBackStartTime, "class='form-control form-datetime'"); ?></td>
                        <th><?php echo $lang->requirement->outsideStart; ?></th>
                        <td colspan="2"><?php echo html::input('feekBackStartTimeOutside', $requirement->feekBackStartTimeOutside, "class='form-control form-datetime'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->requirement->insideEnd; ?></th>
                        <td colspan="2"><?php echo html::input('deptPassTime', $requirement->deptPassTime, "class='form-control form-datetime'"); ?></td>
                        <th><?php echo $lang->requirement->outsideEnd; ?></th>
                        <td colspan="2"><?php echo html::input('innovationPassTime', $requirement->innovationPassTime, "class='form-control form-datetime'"); ?></td>
                    </tr>

                    <tr>
                        <th class='form-actions text-center' colspan='1'>
                            <?php echo html::checkbox('isUpdateOverStatus', [2 => $lang->requirement->noUpdate], $requirement->isUpdateOverStatus); ?>
                        </th>
                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='6'>
                            <?php echo html::submitButton() . html::backButton(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
<?php include '../../common/view/footer.html.php';?>
<script>
    // $(".form-datetime-minutes").datetimepicker(
    //     {
    //         format: "yyyy-mm-dd hh:ii:ss",
    //         'minView':0,
    //         'minuteStep':1,
    //         'autoclose':true
    //     });
</script>
