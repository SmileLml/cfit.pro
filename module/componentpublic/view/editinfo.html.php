<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentpublic->basicinfo; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <?php if(in_array($this->app->user->account, $componentpublic->pmrm)): ?>
                        <tr>
                            <th><?php echo $lang->componentpublic->name; ?></th>
                            <td class='required'><?php echo html::input('name', $componentpublic->name, "class='form-control' required"); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th><?php echo $lang->componentpublic->name; ?></th>
                            <td><?php echo $componentpublic->name; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php echo $lang->componentpublic->latestVersion; ?></th>
                        <td class='required'><?php echo html::select('latestVersion', array('' => '') + $versionList ,$componentpublic->latestVersion, "class='form-control chosen'"); ?></td>
                    </tr>
                    <?php if(in_array($this->app->user->account, $componentpublic->pmrm)): ?>
                        <tr>
                            <th><?php echo $lang->componentpublic->level; ?></th>
                            <td class='required'><?php echo html::select('level', $lang->componentpublic->levelList, $componentpublic->level, "class='form-control chosen'"); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th><?php echo $lang->componentpublic->level; ?></th>
                            <td><?php echo zget($lang->componentpublic->levelList, $componentpublic->level); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if(in_array($this->app->user->account, $componentpublic->pmrm)): ?>
                        <tr>
                            <th><?php echo $lang->componentpublic->category; ?></th>
                            <td class='required'><?php echo html::select('category', $lang->component->categoryList, $componentpublic->category, "class='form-control chosen'"); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <th><?php echo $lang->componentpublic->category; ?></th>
                            <td><?php echo zget($lang->component->categoryList, $componentpublic->category); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php echo $lang->componentpublic->functionDesc; ?></th>
                        <td class='required' >
                            <?php echo html::textarea('functionDesc', $componentpublic->functionDesc, "class='form-control' placeholder='可以在编辑器直接贴图，最多支持输入200个字符'"); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->location; ?><i
                                    title="<?php echo $lang->componentpublic->locationTip; ?>"
                                    class="icon icon-help"></i></th>
                        <td class='required'><?php echo html::input('location', $componentpublic->location, "class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->maintainerDept; ?></th>
                        <td class='required'><?php echo html::select('maintainerDept', $depts, $componentpublic->maintainerDept, "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->maintainer; ?></th>
                        <td class='required'><?php echo html::select('maintainer', array('' => '') + $users, $componentpublic->maintainer, "class='form-control chosen' onchange='selectUser(this.value)'"); ?></td>

                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->developLanguage; ?></th>
                        <td class='required'><?php echo html::select('developLanguage', array('' => '') + $lang->component->developLanguageList, $componentpublic->developLanguage, "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->status; ?></th>
                        <td class='required'><?php echo html::select('status', array('' => '') + $lang->component->publishStatusList, $componentpublic->status, "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->relationgit;?></th>
                        <td >
                            <?php
                            $gitlablist = json_decode($componentpublic->gitlab);
                            if($gitlablist){
                                foreach($gitlablist as $key=>$gitval){
                                    ?>
                                    <div class='table-row'>
                                        <div class='table-col '  >
                                            <div class='input-group '>
                                                <?php echo html::input('gitlab[]', $gitval, "class='form-control' maxlength='200' placeholder='最多200字符' ");?>
                                            </div>
                                        </div>

                                        <div class='table-col actionCol' style="width: 90px;">
                                            <div class='btn-group'>
                                                <?php echo html::commonButton("<i class='icon icon-plus'></i>", "onclick='addGitlab(this)'");?>
                                                <?php echo html::commonButton("<i class='icon icon-close'></i>", "onclick='removeGitlab(this)'");?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                }
                            }else{
                                ?>

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


                                <?php
                            }
                            ?>


                        </td>

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
    <input type="hidden" name="hiddenMaintainerDept" value="<?php echo $componentpublic->maintainerDept;?>" id="hiddenMaintainerDept">
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
            $("#hiddenMaintainerDept").val(data);
        })
    }
</script>


<?php include '../../common/view/footer.html.php'; ?>
