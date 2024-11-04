<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade" style="min-height:350px;">
    <div class="center-block" >
        <div class="main-header">
            <h2><?php echo $lang->component->editstatus; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>

                        <tr>
                            <th><?php echo $lang->component->name; ?></th>
                            <td><?php echo $component->name; ?></td>
                        </tr>


                    <tr>
                        <th><?php echo $lang->component->status; ?></th>
                        <td class='required'><?php echo html::select('status', array('' => '') + $statusArr, $component->status, "class='form-control chosen'"); ?></td>
                    </tr>

                    </tbody>
                </table>
            </div>
    </div>
    <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php echo html::submitButton(); ?>
                <?php echo html::backButton(); ?>
        </tr>
        </tbody>
    </table>

    </form>
</div>
</div>
<script>



</script>


<?php include '../../common/view/footer.html.php'; ?>
