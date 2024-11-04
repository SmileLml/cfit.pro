<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentthird->createversion; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentthird->version; ?></th>
                        <td class='required'><?php echo html::input('version', "", "class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->updatedDate; ?></th>
                        <td class='required'><?php echo html::input('updatedDate', '', "class='form-control form-date'");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->vulnerabilityLevel; ?></th>
                        <td class='required'><?php echo html::select('vulnerabilityLevel', array('' => '') + $lang->componentthird->vulnerabilityLevelList, '', "class='form-control chosen'"); ?></td>
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
        <tr style="height: 140px">

        </tr>
        </tbody>
    </table>
    </form>
</div>
</div>
<script>

</script>


<?php include '../../common/view/footer.html.php'; ?>
