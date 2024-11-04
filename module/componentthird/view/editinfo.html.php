<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<?php
js::set('componentthirdId', $componentthird->id);
?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentthird->basicinfo; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentthird->name; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('name', $componentthird->name, "maxlength='60' class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->baseline; ?></th>
                        <td colspan='2'><?php echo html::input('baseline', zget($versionList, $componentthird->baseline, $componentthird->baseline), "maxlength='100' class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->recommendVersion; ?></th>
                        <td colspan='2'><?php echo html::input('recommendVersion', zget($versionList, $componentthird->recommendVersion, $componentthird->recommendVersion), "maxlength='100' class='form-control' onchange='selectVersion(this.value)'"); ?></td>
                    </tr>
                    <tr class="hidden">
                        <th><?php echo $lang->componentthird->versionDate; ?></th>
                        <td colspan='2'><?php echo html::input('versionDate', $componentthird->versionDate, "class='form-control form-date'");?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->category; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('category', array('' => '') + $lang->component->thirdcategoryList, $componentthird->category, "class='form-control chosen'"); ?></td>
                    </tr>
                    <!--<tr>
                        <th><?php /*echo $lang->componentthird->chineseClassify; */?></th>
                        <td class='required'
                            colspan='2'><?php /*echo html::select('chineseClassify', array('' => '') + $lang->component->chineseClassifyList, $componentthird->chineseClassify, "class='form-control chosen'"); */?></td>
                    </tr>-->
                    <tr>
                        <th><?php echo $lang->componentthird->englishClassify; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('englishClassify', array('' => '') + $lang->component->englishClassifyList, $componentthird->englishClassify, "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->licenseType; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('licenseType', $componentthird->licenseType, "maxlength='40' class='form-control' required"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->developLanguage; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('developLanguage', array('' => '') + $lang->component->developLanguageList, $componentthird->developLanguage, "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang->componentthird->status; ?></th>
                        <td
                            colspan='2'><?php echo html::select('status', array('' => '') + $lang->component->thirdStatusList, $componentthird->status, "class='form-control chosen' onchange='selectStatus()'"); ?></td>
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
        $('#versionDate').prop('disabled', true).trigger("chosen:updated");
        $('#versionDate_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
        selectStatus();
    });

    function selectVersion(id)
    {
        if(id != '' && id != null && id != undefined){
            $.get(createLink('componentthird', 'ajaxGetVersion', 'id=' + id + '&componentthirdId=' + componentthirdId), function(data)
            {
                $('#versionDate').val(data).trigger("chosen:updated");
            });
        }else{
            $('#versionDate').val('').trigger("chosen:updated");
        }
    }

    function selectStatus()
    {
        var value = $('#status').val();
        if(value == 'signout'){
            $('#versionDate').val('').trigger("chosen:updated");
            $('#versionDate').prop('disabled', true).trigger("chosen:updated");
            $('#versionDate_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
            $('#recommendVersion').val('').trigger("chosen:updated");
            $('#recommendVersion').prop('disabled', true).trigger("chosen:updated");
            $('#recommendVersion_chosen').find('.chosen-single').attr('style','background-color:#f5f5f5');
        }else{
            $('#recommendVersion').prop('disabled', false).trigger("chosen:updated");
            $('#recommendVersion_chosen').find('.chosen-single').attr('style','background-color:#ffffff');
        }
    }
</script>


<?php include '../../common/view/footer.html.php'; ?>
