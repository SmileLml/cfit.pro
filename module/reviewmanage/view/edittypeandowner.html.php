<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 420px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $review->id;?></span>
                <span><?php echo $review->title;?></span>

                <small><?php echo $lang->arrow . $reviewNode->statusStageName;?></small>
            </h2>
        </div>

        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class='table table-form'>
                <tr>
                    <th  class='w-150px'><?php echo $lang->review->type;?></th>
                    <td colspan='2'><?php echo html::select('type', $type, $review->type, "class='form-control chosen' ''");?></td>
                    <td></td>
                </tr>
                <tr>
                    <th  class='w-150px'><?php echo $lang->review->owner;?></th>
                    <td colspan='2'><?php echo html::select('owner', $users, $review->owner, "class='form-control chosen' ''");?></td>
                    <td></td>
                </tr>

                <tr>
                    <td class='text-center' colspan='3'>
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
