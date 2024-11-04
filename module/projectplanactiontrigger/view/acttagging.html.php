<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 400px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplanactiontrigger->acttagging;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>

                    <tr>

                        <th class='w-150px'><?php echo $lang->projectplanactiontrigger->snapshotVersion;?></th>
                        <td ><?php echo html::input("snapshotVersion",$planActionInfo->snapshotVersion,"class='form-control' ")?></td>
<!--                        <input type="hidden" name="mainPlanID" id="mainPlanID" value="--><?php //echo $planID;?><!--" />-->
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

})
</script>
<?php include '../../common/view/footer.html.php';?>
