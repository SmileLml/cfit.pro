<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .destroyReasonDiv{width: 674px;height: 160px;word-wrap: break-word;overflow: auto;border: 1px solid #ccc;padding:8px;background-color: rgb(245,245,245);}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->datamanagement->infomessage;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->datamanagement->desc;?></th>
                    <td colspan='2'><div class="destroyReasonDiv"><?php echo $datamanagement->desc ;?></div></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->reason;?></th>
                    <td colspan='2'><div class="destroyReasonDiv"><?php echo $datamanagement->reason ;?></div></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->filingNotice;?></th>
                    <td class="required"><?php echo html::select('filingNotice[]', $filingNoticeList, array_keys($filingNoticeList), "class='form-control chosen' multiple");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->datamanagement->comment;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
                </tr>
                <tr>
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton($lang->datamanagement->read) . html::backButton();?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
