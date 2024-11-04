<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class="table table-form">
        <tbody>
        <tr>
            <th><?php echo $lang->credit->dealResult;?></th>
            <td><?php echo html::select('dealResult', $lang->credit->dealResultList , '', "class='form-control chosen' required onchange='changeDealResult()'");?></td>
            <td></td>
        </tr>

        <tr>
            <th><?php echo $lang->credit->dealMessage;?></th>
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