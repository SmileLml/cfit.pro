<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class="table table-form">
        <tbody>

        <tr>
            <th><?php echo $lang->credit->modifyStatus;?></th>
            <td><?php echo html::select('status', array('' => '') + $lang->credit->endStatusList , '', "class='form-control chosen' required onchange='changeModifyStatus()'");?></td>
            <td></td>
        </tr>

        <tr id="onlineTimeInfo">
            <th><?php echo $lang->credit->onlineTime;?></th>
            <td class="required" id="onlineTimeTd">
                <?php echo html::input('onlineTime', '', "class='form-control form-datetime'");?>
            <td></td>
        </tr>

        <tr>
            <th><?php echo $lang->credit->comment;?></th>
            <td colspan='2' id="suggestTd"><?php echo html::textarea('dealMessage', '', "class='form-control' style='height:150px'");?></td>
        </tr>
        <tr>
            <td class='form-actions text-center' colspan='3'>
                <?php echo html::submitButton() . html::backButton();?>
            </td>
        </tr>
        </tbody>
    </table>
</form>