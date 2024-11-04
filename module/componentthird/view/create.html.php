<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/datepicker.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->componentthird->create; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <div id="newPublic">
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->componentthird->name; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('name', "", "maxlength='60' class='form-control' required"); ?></td>
                        <th><?php echo $lang->componentthird->category; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('category', array('' => '') + $lang->component->thirdcategoryList, '', "class='form-control chosen'"); ?></td>
                    </tr>
                    <tr>
                       <!-- <th><?php /*echo $lang->componentthird->chineseClassify; */?></th>
                        <td class='required'
                            colspan='2'><?php /*echo html::select('chineseClassify', array('' => '') + $lang->component->chineseClassifyList, '', "class='form-control chosen'"); */?></td>-->
                        <th><?php echo $lang->componentthird->englishClassify; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('englishClassify', array('' => '') + $lang->component->englishClassifyList, '', "class='form-control chosen'"); ?></td>
                        <th><?php echo $lang->componentthird->licenseType; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::input('licenseType', "", "maxlength='40' class='form-control' required"); ?></td>
                    </tr>
                    <tr>

                        <th><?php echo $lang->componentthird->developLanguage; ?></th>
                        <td class='required'
                            colspan='2'><?php echo html::select('developLanguage', array('' => '') + $lang->component->developLanguageList, '', "class='form-control chosen'"); ?></td>
                        <th><?php echo $lang->componentthird->status; ?></th>
                        <td colspan='2'><?php echo html::select('status', array('' => '') + $lang->component->thirdStatusList, '', "class='form-control chosen'"); ?></td>
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
<script>

</script>


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