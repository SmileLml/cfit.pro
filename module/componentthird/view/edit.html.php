<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentthird->edit; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentthird->name;?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('name', $componentthird->name, "maxlength='60' class='form-control' required"); ?></td>
                        <th><?php echo $lang->componentthird->category; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('category', array('' => '') + $lang->component->thirdcategoryList, $componentthird->category, "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                        <!--<th><?php /*echo $lang->componentthird->chineseClassify; */?></th>
                        <td class='required'
                            colspan='2'><?php /*echo html::select('chineseClassify', array('' => '') + $lang->component->chineseClassifyList, $componentthird->chineseClassify, "class='form-control chosen'"); */?></td>-->
                        <th><?php echo $lang->componentthird->englishClassify; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('englishClassify', array('' => '') + $lang->component->englishClassifyList, $componentthird->englishClassify, "class='form-control chosen'"); ?></td>
                        <th><?php echo $lang->componentthird->licenseType; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('licenseType', $componentthird->licenseType, "maxlength='40' class='form-control' required"); ?></td>
                    </tr>
                    <tr>

                        <th><?php echo $lang->componentthird->developLanguage; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('developLanguage', array('' => '') + $lang->component->developLanguageList, $componentthird->developLanguage, "class='form-control chosen'"); ?></td>
                        <th><?php echo $lang->componentthird->status; ?></th>
                        <td colspan='2'><?php echo html::select('status', array('' => '') + $lang->component->thirdStatusList, $componentthird->status, "class='form-control chosen'"); ?></td>
                    </tr>
                    <!--<tr>

                    </tr>-->
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



<?php include '../../common/view/footer.html.php'; ?>
<script>
$(function(){
    /*$("#chineseClassify").change(function (){

        let chineseValue = $(this).val();

        let englishValue = chineseValue.replace (/^.{2}/g,'yw');

        $("#englishClassify").val(englishValue);
        $("#englishClassify").trigger("chosen:updated");


    });*/



})
</script>