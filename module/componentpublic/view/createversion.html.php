<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentpublic->createversion; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentpublic->version; ?></th>
                        <td class='required'><?php echo html::input('version', "", "class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->updatedDate; ?></th>
                        <td class='required'><?php echo html::input('updatedDate', '', "class='form-control form-date'");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->desc; ?></th>
                        <td class='required'><?php echo html::textarea('desc', '', "class='form-control' placeholder='可以在编辑器直接贴图，最多支持输入2000个字符'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentpublic->useFile;?><i title="<?php echo $lang->componentpublic->filesTip;?>" class="icon icon-help"></i></th>
                        <td><?php echo $this->fetch('file', 'buildform');?></td>
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
    $(function () {
        window.editor['desc'].edit.afterChange(function () {
            var limitNum = 2000;  //设定限制字数
            window.editor['desc'].sync();
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
                window.editor['desc'].html(strValueOld);
                window.editor['desc'].focus();
                window.editor['desc'].appendHtml('');
            }
        });
    });

</script>


<?php include '../../common/view/footer.html.php'; ?>
