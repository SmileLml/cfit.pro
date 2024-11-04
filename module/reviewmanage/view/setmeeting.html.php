<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
    .task-toggle .icon{display: inline-block; transform: rotate(90deg);}
    .more-tips{display: none;}
    .close-tips{display: none}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->reviewmanage->meeting->setsched;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-140px'><?php echo $lang->reviewmanage->title;?></th>
                    <td>
                        <?php foreach ($reviewList as $v){?>
                        <?php echo html::input('', $v->title, "class='form-control' disabled");?><br/>
                        <?php }?>
                    </td>

                </tr>
                <tr>
                    <th><?php echo $lang->reviewmanage->owner;?></th>
                    <td ><?php echo html::select('owner', $users, $owner, "class='form-control chosen' required");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewmeet->meetingPlanExport;?></th>
                    <td ><?php echo html::select('expert[]',$users, $expert, "class='form-control chosen'  multiple required");?></td>
                </tr>
                <tr>
                    <th class='w-140px'><?php echo $lang->reviewmanage->meeting->setsched;?></th>
                    <td><?php echo html::input('feedbackExpireTime', '', "class='form-control form-datetime' required");?></td>
                </tr>
                <tr>
                    <?php echo html::hidden("ids",$ids)?>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>

</script>
<?php include '../../common/view/footer.html.php';?>
