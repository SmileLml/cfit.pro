<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade" style="height: 400px">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplan->editDelayYear;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class="w-160px"><?php echo $lang->projectplan->isDelayPreYear;?></th>
                    <td><?php echo html::select('isDelayPreYear', $lang->projectplan->isDelayPreYearList, $plan->isDelayPreYear, "class='form-control chosen' ");?></td>
                </tr>

                <tr>
                    <td class='form-actions text-center' colspan='2'>
                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
