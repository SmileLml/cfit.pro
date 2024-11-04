<?php include '../../common/view/header.html.php';?>


<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->secondmonthdata->createdata;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>

                <tr class="dev">
                    <th class='w-140px'><?php echo $lang->secondmonthdata->sourceyear;?></th>
                    <td  >
                        <div class='input-group'>
                            <?php  echo html::input('sourceyear', '', "class='form-control ' required");?>
                        </div>
                    </td>
                </tr>
                <tr >
                    <th><?php echo $lang->secondmonthdata->objectid;?></th>
                    <td ><?php echo html::input('objectid', '', "class='form-control ' required");?></td>

                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'><?php echo html::hidden('sourcetype', $type);?><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>


</script>
<?php include '../../common/view/footer.html.php';?>
