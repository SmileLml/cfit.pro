<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2>
                <span class='label label-id'><?php echo $requirement->code;?></span>
                <?php echo isonlybody() ? ('<span title="' . $lang->requirement->editEnd . '">' . $lang->requirement->editEnd . '</span>') : html::a($this->createLink('requirement', 'view', 'requirementID=' . $requirement->id), $requirement->name);?>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <tr>
                    <th><?php echo $lang->requirement->planEnd;?></th>
                    <td><?php echo html::input('planEnd', $requirement->end, "class='form-control form-date'");?></td>
                </tr>
                <th><?php echo $lang->requirement->comment;?></th>
                <td ><?php echo html::textarea('comment', '', "rows='8' class='form-control'");?></td>
                <tr>
                    <td colspan='2' class='text-center form-actions'>
                        <?php echo html::submitButton($this->lang->requirement->submitBtn);?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
