<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade" style="min-height: 400px;">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->projectplanmsrelation->edit;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-150px'><?php echo $lang->projectplanmsrelation->mainProject;?></th>
                    <td ><?php echo $projectplanList[$relationInfo->mainPlanID];?></td>


                </tr>
                <tr>
                    <th class='w-150px'><?php echo $lang->projectplanmsrelation->slaveProject;?></th>
                    <td ><?php echo html::select('slavePlanID[]', $projectplanList, $relationInfo->slavePlanID, " class='form-control chosen' multiple");?></td>

                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='2'><?php echo html::submitButton(). html::a(inlink('browse'), $lang->goback, '', "class='btn btn-back btn-wide'");?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<?php include '../../common/view/footer.html.php';?>
