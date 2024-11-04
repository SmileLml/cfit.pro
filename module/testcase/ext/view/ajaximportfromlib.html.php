<?php
/**
 * The create view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: create.html.php 4769 2013-05-05 07:24:21Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include $app->getModuleRoot() . 'common/view/header.lite.html.php'; ?>
<?php js::set('applicationID', $applicationID); ?>
<?php js::set('branch', $branch); ?>
<?php js::set('moduleID', $moduleID); ?>
<div id='mainContent' class='main-content' style="height: 460px;">
    <form class='form-indicator main-form form-ajax' method='post' target='hiddenwin' id='selectProductForm'>
        <table class='table table-form'>
            <tr>
                <th class='w-120px'><?php echo $lang->testcase->selectProducts; ?></th>
                <td>
                    <?php echo html::select('productID', $products, (string)$productID, "onchange='loadTheProductProjects(this.value);' class='form-control chosen' id='product'"); ?>
                </td>
                <td class='w-20px' style="color: red;">*</td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->testcase->project; ?></th>
                <td>
                    <?php echo html::select('project', $projects, $projectID, "class='form-control chosen'"); ?>
                </td>
                <td class='w-20px' style="color: red;">*</td>
            </tr>
            <tr>
                <th class='w-120px'><?php echo $lang->testcase->selectCaseLib; ?></th>
                <td>
                    <?php echo html::select('libID', $libraries, '', "class='form-control chosen' id='lib'"); ?>
                </td>
                <td class='w-20px' style="color: red;">*</td>
            </tr>

            <tr>
                <td colspan='2' class='text-center form-actions'>
                    <?php echo html::submitButton($lang->confirm); ?>
                </td>
                <td></td>
            </tr>
    </form>
</div>
<?php js::set('selectProjectTips', $lang->testcase->selectProjectTips); ?>
<?php js::set('selectCaseLibTips', $lang->testcase->selectCaseLibTips); ?>
<?php js::set('selectProductsTips', $lang->testcase->selectProductsTips); ?>
<script>
    $('#selectProductForm').submit(function() {
        var productID = $('#product').val();
        if (productID == 0 || productID == '') {
            bootbox.alert(selectProductsTips);
            return false;
        }
        var libID = $('#lib').val();
        if (libID == 0 || libID == '') {
            bootbox.alert(selectCaseLibTips);
            return false;
        }

        var projectID = $('#project').val();
        if (projectID == 0 || projectID == '') {
            bootbox.alert(selectProjectTips);
            return false;
        }

        var assignLink = createLink('testcase', 'importFromLib', 'applicationID=' + applicationID + '&productID=' + productID + '&projectID=' + projectID + '&branch=' + branch + '&moduleID=' + moduleID + '&libID=' + libID);
        $.closeModal('parent', assignLink);
        return false;
    });

    function loadTheProductProjects(productID) {
        var link = createLink('rebirth', 'ajaxProjectByProduct', 'applicationID=' + applicationID + '&productID=' + productID + '&browseType=testcase&projectID=0');
        $.post(link, function(data) {
            $('#project').replaceWith(data);
            $('#project_chosen').remove();
            $('#project').chosen();
        })
    }
</script>
<?php include $app->getModuleRoot() . 'common/view/footer.lite.html.php'; ?>