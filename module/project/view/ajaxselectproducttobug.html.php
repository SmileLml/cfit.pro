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
<?php include '../../common/view/header.lite.html.php';?>
<?php js::set('projectID', $projectID);?>
<?php js::set('objectType', $objectType);?>
<?php js::set('selectProductTips', $lang->project->selectProductTips);?>
<div id='mainContent' class='main-content'>
    <form class='form-indicator main-form form-ajax' method='post' target='hiddenwin' id='selectProductForm'>
      <table class='table table-form'>
        <tr>
        <th class='w-120px'><?php echo $lang->project->selectProduct;?></th>
          <td>
          <?php echo html::select('product', $products, '', "class='form-control chosen'");?>
          </td>
        </tr>
        <tr>
          <td colspan='2' class='text-center form-actions'>
            <?php echo html::submitButton($lang->confirm);?>
          </td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
    </form>
</div>
<script>
$('#selectProductForm').submit(function()
{
    var productKey = $('#product').val();
    if(productKey == 0 || productKey == '')
    {
        alert(selectProductTips);
        return false;
    }

    var keyList = productKey.split('-');

    var applicationID = keyList[0];
    var productID     = keyList[1];

    if(objectType == 'bug')       var assignLink  = createLink('bug', 'create', 'applicationID=' + applicationID + '&productID=' + productID + '&branch=0&extras=projectID=' + projectID);
    if(objectType == 'testcase')  var assignLink  = createLink('testcase', 'create', 'applicationID=' + applicationID + '&productID=' + productID);
    if(objectType == 'testtask')  var assignLink  = createLink('testtask', 'create', 'applicationID=' + applicationID + '&productID=' + productID + '&build=0&projectID=' + projectID);
    if(objectType == 'testsuite') var assignLink  = createLink('testsuite', 'create', 'applicationID=' + applicationID + '&productID=' + productID);
    if(objectType == 'testreport') var assignLink = createLink('testreport', 'create', 'applicationID=' + applicationID + '&productID=' + productID);

    if(objectType == 'testcaseBatchCreate') var assignLink = createLink('testcase', 'batchCreate', 'applicationID=' + applicationID + '&productID=' + productID);
    if(objectType == 'bugBatchCreate')      var assignLink = createLink('bug', 'batchCreate', 'applicationID=' + applicationID + '&productID=' + productID);

    $.closeModal('parent', assignLink);
    return false;
});
</script>
<?php include '../../common/view/footer.lite.html.php';?>
