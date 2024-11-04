<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentpublic->create; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentpublic->name; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('name', "", "maxlength='60' class='form-control' required"); ?></td>
                        <th><?php echo $lang->componentpublic->latestVersion; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('latestVersion', "", "class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->level; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('level', array('' => '') + $lang->componentpublic->levelList, '', "class='form-control chosen'"); ?></td>
                        <th><?php echo $lang->componentpublic->category; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('category', array('' => '') + $lang->component->categoryList, '', "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->developLanguage; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('developLanguage', array('' => '') + $lang->component->developLanguageList, '', "class='form-control chosen'"); ?></td>
                        <th><?php echo $lang->componentpublic->status; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('status', array('' => '') + $lang->component->publishStatusList, '', "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->maintainer; ?></th>
                        <td class='required' colspan='2'><?php echo html::select('maintainer', array('' => '') + $users, '', "class='form-control chosen' onchange='selectUser(this.value)'"); ?></td>
                        <th><?php echo $lang->componentpublic->maintainerDept; ?></th>
                        <td class='required' colspan='2'><?php echo html::select('maintainerDept', $depts, '', "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->relationgit;?></th>
                        <td  colspan='5'>
                            <div class='table-row'>
                                <div class='table-col '  >
                                    <div class='input-group '>
                                        <?php echo html::input('gitlab[]', '', "class='form-control' maxlength='200' placeholder='最多200字符' ");?>
                                    </div>
                                </div>

                                <div class='table-col actionCol' style="width: 90px;">
                                    <div class='btn-group'>
                                        <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addGitlab(this)'");?>
                                        <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeGitlab(this)'");?>
                                    </div>
                                </div>
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->functionDesc; ?></th>
                        <td class='required' colspan='5'>
                            <?php echo html::textarea('functionDesc', '', "class='form-control' placeholder='可以在编辑器直接贴图，最多支持输入200个字符'"); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->location; ?><i
                                    title="<?php echo $lang->componentpublic->locationTip; ?>"
                                    class="icon icon-help"></i></th>
                        <td class='required' colspan='2'><?php echo html::input('location', "", "class='form-control' required"); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </div>
    <table class="table table-form">
        <tbody>
        <tr>
            <td class='form-actions text-center' colspan='5'>
                <?php echo html::submitButton(); ?>
                <?php echo html::backButton(); ?>
        </tr>
        </tbody>
    </table>
    </form>
</div>
</div>
<script>
    function addGitlab(obj)
    {
        stageNum = $(obj).closest('td').find('.table-row').size();
        if(stageNum >= 200) { alert("最多加200个git库"); return; }

        $row = $(obj).closest('.table-row');
        $row.after($row.clone());
        $next = $row.next();

        $next.find('.form-control').val('');

    }

    function removeGitlab(obj)
    {
        if($(obj).closest('td').find('.table-row').size() == 1) {
            $(obj).closest('td').find('.form-control').val('');
            return false;
        }
        $(obj).closest('.table-row').remove();

    }
    $(function () {
        $('#maintainerDept').prop('disabled', true).trigger("chosen:updated");
        $('#maintainerDept_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
        window.editor['functionDesc'].edit.afterChange(function () {
            var limitNum = 200;  //设定限制字数
            window.editor['functionDesc'].sync();
            var strValue = $("#functionDesc").val();
            strValue = strValue.replace(/<[^>]+>/g, "");
            if (strValue.length > limitNum) {
                var oldLength = strValue.length;
                var strValueOld = $("#functionDesc").val();
                var isTag = 0;
                for (var i = strValueOld.length - 1; i >= 0 && oldLength > limitNum; i--) {
                    if (strValueOld.charAt(i) == '>') {
                        isTag = 1;
                    } else if (strValueOld.charAt(i) == '<') {
                        isTag = 0;
                    } else {
                        if (isTag == 0) {
                            strValueOld = strValueOld.slice(0, i) + strValueOld.slice(i + 1);
                            oldLength--;
                        }
                    }
                }
                window.editor['functionDesc'].html(strValueOld);
                window.editor['functionDesc'].focus();
                window.editor['functionDesc'].appendHtml('');
            }
        });
    });

    function selectUser(id)
    {
        $.get(createLink('componentpublic', 'ajaxGetDeptByUser', 'id=' + id), function(data)
        {
            $('#maintainerDept').val(data).trigger("chosen:updated");
        })
    }
</script>


<?php include '../../common/view/footer.html.php'; ?>
