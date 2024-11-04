<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 400px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplanmsrelation->maintenanceRelation;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <?php
                if($planID){
                    ?>
                    <tr>

                        <th class='w-150px'><?php echo $lang->projectplanmsrelation->mainProject;?></th>
                        <td ><?php echo $projectplanList[$planID];?></td>
                        <input type="hidden" name="mainPlanID" id="mainPlanID" value="<?php echo $planID;?>" />
                    </tr>

                        <?php
                }else{
                 ?>
                    <tr>
                        <th class='w-150px'><?php echo $lang->projectplanmsrelation->mainProject;?></th>
                        <td ><?php echo html::select('mainPlanID', $projectplanList, '', " class='form-control chosen' ");?></td>


                    </tr>
                   <?php
                }
                ?>

                <tr>
                    <th class='w-150px'><?php echo $lang->projectplanmsrelation->slaveProject;?></th>
                    <td ><?php echo html::select('slavePlanID[]', $projectplanList, '', " class='form-control chosen' multiple");?></td>

                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='2'><?php echo html::submitButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
$(function(){
    $("#mainPlanID").change(function (){
        let mainPlanID = $(this).val();

        $.post(createLink("projectplanmsrelation","ajaxGetSlaveProjectplan"),{"mainPlanID":mainPlanID},function(data){
            $('#slavePlanID_chosen').remove();
            $('#slavePlanID').val('');
            $('#slavePlanID').replaceWith(data);
            $('#slavePlanID').chosen();
        })
    });
    $("#mainPlanID").change();
})
</script>
<?php include '../../common/view/footer.html.php';?>
