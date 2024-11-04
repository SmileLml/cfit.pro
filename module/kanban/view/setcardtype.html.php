
<?php include '../../common/view/headerkanban.lite.html.php';?>
<style>
    .btn-wide{border-color:#d8dbde !important;}
</style>
<div id='mainContent' class='main-content importModal'>

    <form class="load-indicator main-form form-ajax" method="post">
        <div class="main-header">
            <div class="heading">
                <strong><?php echo $lang->kanban->setCardType;?></strong>
            </div>
        </div>
        <table class="table table-form active-disabled table-condensed mw-600px">
            <tbody class="typeBody">
            <tr class="text-center">
                <td class="w-120px"><strong>key值</strong></td>
                <td><strong>分类名称</strong></td>
                <th class="w-90px"></th>
            </tr>
            <?php foreach ($type as $key=>$val) :?>
                <tr class="text-center">
                    <td>
                        <?php echo $val->key?>
                        <input type="hidden" name="key[]" id="key[]" value="<?php echo $val->key?>">
                        <input type="hidden" name="id[]" id="id[]" value="<?php echo $val->id?>">
                        <input type="hidden" name="status[]" id="status[]" value="0">
                    </td>
                    <td>
                        <input type="text" name="name[]" id="name[]" value="<?php echo $val->name?>" class="form-control " <?php if($key == 0){echo 'readonly';}?> autocomplete="off">
                    </td>
                    <td class="c-actions">
                        <a href="javascript:void(0)" onclick="addItem(this)" class="btn btn-link "><i class="icon-plus"></i></a>
                        <?php if ($key > 0):?>
                            <a href="javascript:void(0)" onclick="delItem(this,<?php echo $val->id?>)" class="btn btn-link delItem"><i class="icon-close"></i></a>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>

            <tr>
                <td></td>
                <td><?php echo html::submitButton()?></td>
            </tr>
            </tbody>
        </table>

    </form>
</div>
<?php include '../../common/view/footer.lite.html.php';?>
<script>

function delItem(obj,id) {
    if (id > 0){
        $(obj).closest('td').siblings().children('[name="status[]"]').val(1);
        $(obj).closest('tr').addClass('hidden')
    }else{
        $(obj).closest('tr').remove()
    }
}
function addItem(obj) {
    var str = '<tr class="text-center">\n' +
        '            <td>\n' +
        '                <input type="text" class="form-control" autocomplete="off" value="" name="key[]">\n' +
        '                <input type="hidden" name="id[]" id="id[]" value="0"><input type="hidden" name="status[]" id="status[]" value="0">\n'+
        '            </td>\n' +
        '            <td>\n' +
        '                <input type="text" class="form-control" value="" autocomplete="off" name="name[]">\n' +
        '            </td>\n' +
        '            <td class="c-actions">\n' +
        '                <a href="javascript:void(0)" onclick="addItem(this)" class="btn btn-link"><i class="icon-plus"></i></a>\n' +
        '                <a href="javascript:void(0)" onclick="delItem(this,0)" class="btn btn-link"><i class="icon-close"></i></a>\n' +
        '            </td>\n' +
        '        </tr>'
    $(obj).closest('tr').after(str);
}

</script>
