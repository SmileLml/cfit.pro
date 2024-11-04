<?php include '../../common/view/header.html.php';?>
<div id="mainContent" class="main-content fade" style="height: 300px">
    <div class="center-block">
        <div class="main-header">
            <h2>编辑状态</h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->outsideplan->status;?></th>
                    <td><?php echo html::select('status', $lang->outsideplan->statusList, $plan->status, "class='form-control chosen' ");?></td>
                </tr>

                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton() . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
