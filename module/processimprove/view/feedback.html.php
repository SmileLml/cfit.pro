<?php
/**
 * The create view of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: create.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('noProject', false);?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->processimprove->feedback;?></h2>
    </div>
    <table class="table table-form content">
      <tbody>
        <tr>
          <th><?php echo $lang->processimprove->source;?></th>
          <td><?php echo zget($lang->processimprove->sourceList, $processImprove->source);?></td>
        </tr>
        <tr>
          <th><?php echo $lang->processimprove->desc;?></th>
          <td><?php echo $processImprove->desc;?></td>
        </tr>
      </tbody>
    </table>
    <form class="load-indicator main-form form-ajax" id="createForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <?php if(common::hasPriv('processimprove', 'edit')):?>
          <tr>
            <th><?php echo $lang->processimprove->isAccept;?></th>
            <td><?php echo html::radio('isAccept', $lang->processimprove->isAcceptList, $processImprove->isAccept, "onchange='showFields(this.value)'");?></td>
          </tr>
          <tr class="fields <?php if($processImprove->isAccept === '2' || $processImprove->isAccept === '3') echo 'hide';?>">
            <th><?php echo $lang->processimprove->process;?></th>
            <td><?php echo html::select('process', $lang->processimprove->processList, $processImprove->process, "class='form-control chosen'");?></td>
          </tr>
          <tr class="fields <?php if($processImprove->isAccept === '2' || $processImprove->isAccept === '3') echo 'hide';?>">
            <th><?php echo $lang->processimprove->involved;?></th>
            <td><?php echo html::select('involved', $lang->processimprove->involvedList, $processImprove->involved, "class='form-control chosen'");?></td>
          </tr>
          <tr class="fields <?php if($processImprove->isAccept === '2' || $processImprove->isAccept === '3') echo 'hide';?>">
            <th><?php echo $lang->processimprove->pri;?></th>
            <td><?php echo html::select('pri', $lang->processimprove->priorityList, $processImprove->pri, "class='form-control chosen'");?></td>
          </tr>
          <tr class="fields <?php if($processImprove->isAccept === '2' || $processImprove->isAccept === '3') echo 'hide';?>">
            <th><?php echo $lang->processimprove->reviewedBy;?></th>
            <td colspan='2'><?php echo html::select('reviewedBy[]', $users, $processImprove->reviewedBy, 'class="form-control chosen" multiple');?></td>
          </tr>
          <tr class="fields <?php if($processImprove->isAccept === '2' || $processImprove->isAccept === '3') echo 'hide';?>">
            <th><?php echo $lang->processimprove->judge;?></th>
            <td colspan='2'><?php echo html::textarea('judge', $processImprove->judge, "class='form-control'");?></td>
          </tr>
          <tr class="fields <?php if($processImprove->isAccept === '2' || $processImprove->isAccept === '3') echo 'hide';?>">
            <th><?php echo $lang->processimprove->isDeploy;?></th>
            <td><?php echo html::radio('isDeploy', $lang->processimprove->isAcceptList, $processImprove->isDeploy, "onchange=showDeployDate(this.value)");?></td>
          </tr>
          <tr class="<?php if($processImprove->isDeploy !== '1') echo 'hide';?> fields <?php if($processImprove->isAccept === '2') echo 'hide';?>">
            <th><?php echo $lang->processimprove->deployDate;?></th>
            <td><?php echo html::input('deployDate', $processImprove->deployDate, "class='form-date form-control'");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->comment;?></th>
            <td colspan='2'>
              <?php echo html::textarea('comment', '', "rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton();?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<script>
function showFields(value)
{
    if(value == 1) 
    {
        $('.fields').closest('tr').removeClass('hide')
    }
    else
    {
        $('.fields').closest('tr').addClass('hide')
    }
}

function showDeployDate(value)
{
    if(value == 1) 
    {
        $('#deployDate').closest('tr').removeClass('hide')
    }
    else
    {
        $('#deployDate').closest('tr').addClass('hide')
    }
}
</script>
<?php include '../../common/view/footer.html.php';?>
