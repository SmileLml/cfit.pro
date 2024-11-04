<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class="table table-form">
        <tbody>
        <tr>
            <th class='w-120px'><?php echo $lang->credit->svnUrl;?></th>
            <td colspan='2'>
                <?php echo html::input('svnUrl', $creditInfo->svnUrl, "class='form-control'");?>
            </td>
        </tr>

        <tr>
            <th><?php echo $lang->credit->onLineFile;?></th>
            <td colspan='2' id="suggestTd">
                <?php echo html::textarea('onLineFile', $creditInfo->onLineFile, "class='form-control' style='height:150px'");?></td>
        </tr>
        <tr>
            <td class='form-actions text-center' colspan='3'>
                <?php echo html::submitButton() . html::backButton();?>
            </td>
        </tr>
        </tbody>
    </table>
</form>