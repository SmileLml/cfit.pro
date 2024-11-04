<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class="main-row">
    <div class="side-col" id="sidebar">
        <div class="cell">
            <div class="list-group">
                <?php foreach($lang->custommail->objectList as $object => $title):?>
                    <?php if(common::hasPriv('custommail', $object)):?>
                        <?php $active = $object == $browseType ? ' class="active"' : '';?>
                        <a href="<?php echo $this->createLink('custommail', $object);?>" id="<?php echo $object;?>Tab" <?php echo $active;?>><?php echo $title;?></a>
                    <?php endif;?>
                <?php endforeach;?>
            </div>
        </div>
    </div>
    <div class="main-col main-content">
        <div class="main-header">
            <div class="heading">
                <strong><?php echo $lang->custommail->common;?><i class="icon-angle-right"></i><?php echo $lang->custommail->environmentorder;?></strong>
            </div>
        </div>
        <form class="load-indicator main-form form-ajax" method='post'>
            <table class="table table-form">
                <tr>
                    <th class='w-120px'><?php echo $lang->custommail->mailTitle;?></th>
                    <td><?php echo html::input('mailTitle', $mailConf->mailTitle, "class='form-control' autocomplete='off'")?></td>
                    <td></td>
                </tr>
                <tr>
                    <th></th>
                    <td colspan="2"><span style="color:#03b8cf;font-weight:bold;"><?php echo $lang->custommail->promptSettings;?></span></td>
                </tr>
                <?php foreach($mailConf->variables as $index => $variable):?>
                    <?php $index = $index + 1;?>
                    <tr id='variableTag<?php echo $index;?>' class='variable-tag'>
                        <th class='w-120px' id='variableTitle'><?php echo $lang->custommail->variable; echo $index;?></th>
                        <td><?php echo html::input('variables[]', $variable, "class='form-control' autocomplete='off'")?></td>
                        <td></td>
                    </tr>
                <?php endforeach;?>
                <tr>
                    <th class='w-120px'><?php echo $lang->custommail->mailContent;?></th>
                    <td><?php echo html::textarea('mailContent', $mailConf->mailContent, "raws='6'", "class='form-control'")?></td>
                    <td></td>
                </tr>
                <tr>
                    <th class='w-120px'></th>
                    <td class='text-left form-actions'>
                        <?php echo html::submitButton();?>
                        <?php echo html::commonButton($lang->custommail->preview, 'data-type="ajax" data-title="' . $lang->custommail->preview . '" data-remote="' . $this->createLink('custommail', 'ajaxPreview', 'browseType=environmentorder') . '" data-toggle="modal"', 'btn btn-wide btn-info triggerButton');?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<div class='hidden'>
    <table>
        <tr id='variableTr'>
            <th class='w-120px' id='variableTitle'></th>
            <td><?php echo html::input('variables[]', '', "class='form-control' autocomplete='off'")?></td>
            <td></td>
        </tr>
    </table>
</div>
<?php js::set('variableTitle', $lang->custommail->variable);?>
<script>
    $("#mailTitle").on('input', function()
    {
        // 判断变量存在的个数。
        var title = $(this).val();
        var variableNumber = (title.match(/%s/g) || []).length;

        // 克隆一份要复制的对象。
        var variableTr = $('#variableTr');
        var variableTr = variableTr.clone();

        // 获取已有的变量行个数。
        var recordVariableTotal = $('.variable-tag').length;

        // 判断删除还是追加变量行。
        if(recordVariableTotal > variableNumber)
        {
            // 已有变量行数大于变量个数，则删除变量行。
            for(var i = recordVariableTotal; i > variableNumber; i--)
            {
                $("#variableTag" + i).remove();
            }
        }
        else if(recordVariableTotal < variableNumber)
        {
            // 已有变量行数小于变量个数，则怎加变量行。
            for(var i = variableNumber; i > recordVariableTotal; i--)
            {
                var newVariableTitle = variableTitle + i;
                variableTr.find("#variableTitle").text(newVariableTitle);
                var html = "<tr id='variableTag"+ i +"' class='variable-tag'>" + variableTr.html() + "</tr>";

                // 没有变量行时，则直接追加html。有变量行时，则在变量行之后追加html。
                if(recordVariableTotal)
                {
                    $("#variableTag" + recordVariableTotal).after(html);
                }
                else
                {
                    $(this).parent().parent().after(html);
                }
            }
        }
    });
</script>
<?php include '../../common/view/footer.html.php';?>
