<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade" style="height:400px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->weeklyreportout->pushWeeklyreportQingZong;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form" >
                <tbody>
                <tr>
                    <th class='w-110px'><?php echo $lang->weeklyreportout->outweeknum;?></th>
                    <td colspan='3'>
<!--                        --><?php //echo html::input("outweeknum",'',"class='form-control'  "); ?>
<!--                        picker-select-->
                        <?php echo html::select('outweeknum', $weekNumList, '', "class='form-control  chosen'  ");?>
                    </td>
                </tr>

                <tr>
                    <td  class='form-actions text-center' colspan='4'>
                        <?php echo html::submitButton(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php'; ?>
<script>

</script>
