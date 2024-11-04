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
<?php include $app->getModuleRoot().'common/view/header.lite.html.php';?>
<div id='mainContent' class='main-content' style="height: 400px;">
    <form class='form-indicator main-form form-ajax' method='post' target='hiddenwin' id='selectProductForm'>
      <table class='table table-form'>
        <tr>
          <th class='w-120px'><?php echo $lang->testcase->selectProducts;?></th>
          <td>
          <?php echo html::select('productID', $products, $productID, "class='form-control chosen'");?>
          <?php echo html::hidden('applicationID', 0);?>
          </td>
          <td class='w-20px' style="color: red;">*</td>
        </tr>
        <tr>
          <th class='w-120px'><?php echo $lang->project->selectCaseLib;?></th>
          <td>
          <?php echo html::select('libID', $libraries, '', "class='form-control chosen' id='lib'");?>
          </td>
          <td class='w-20px' style="color: red;">*</td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton($lang->confirm);?>
          </td>
          <td></td>
        </tr>
    </form>
</div>

<?php js::set('projectID', $projectID);?>
<?php js::set('selectProductsTips', $lang->testcase->selectProductsTips);?>
<?php js::set('selectCaseLibTips', $lang->testcase->selectCaseLibTips);?>
<script>
$(function()
{
    $('#productID').change();
});

$('#productID').on('change', function()
{
    var productID = $(this).val();
    $.get(createLink('product', 'ajaxGetProduct', 'productID=' + productID), function(data)
    {
        $('#applicationID').val(data);
    });
});

$('#selectProductForm').submit(function()
{
    var applicationID = $('#applicationID').val();
    var productID     = $('#productID').val();
    var libID         = $('#lib').val();

    if(productID == 0 || productID == '')
    {
        bootbox.alert(selectProductsTips);
        return false;
    }

    if(libID == 0 || libID == '')
    {
        bootbox.alert(selectCaseLibTips);
        return false;
    }

    var assignLink = createLink('project', 'importFromLib', 'projectID=' + projectID +'&applicationID=' + applicationID + '&productID=' + productID + '&branch=0&libID=' + libID);
    $.closeModal('parent', assignLink);
    return false;
});
</script>
<?php include $app->getModuleRoot().'common/view/footer.lite.html.php';?>
