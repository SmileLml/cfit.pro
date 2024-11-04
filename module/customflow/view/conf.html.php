<?php
/**
 * The browse view of requestconf module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author
 * @package     browse
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainContent' class="main-content">
  <div class="main-header">
    <h2><?php echo $lang->customflow->common?></h2>
  </div>
  <form class="load-indicator main-form form-ajax" method='post'>
  <table class="table table-form">
    <tr>
      <th class='w-60px'></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->flowCode;?></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->flowName;?></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->flowView;?></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->flowAssign;?></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->flowApp;?></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->flowOrder;?></th>
      <th class='w-100px text-left'><?php echo $lang->customflow->partyEnable;?></th>
      <th class='w-100px text-left'><?php echo $lang->actions;?></th>
    </tr>
    <?php $index = 0;?>
    <?php foreach($customList as $custom):?>
    <?php $index = $index + 1;?>
    <tr id='softwareTr<?php echo $index;?>'>
      <th><?php echo $lang->customflow->conf;?></th>
      <td><?php echo html::input('flowCode[]',   $custom['flowCode'],   "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowName[]',   $custom['flowName'],   "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowView[]',   $custom['flowView'],   "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowAssign[]', $custom['flowAssign'], "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowApp[]',    $custom['flowApp'],    "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowOrder[]',  $custom['flowOrder'],  "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::radio('partyEnable' . $index,$lang->customflow->partyEnableList, $custom['flowEnable'])?></td>
      <td>
        <a href="javascript:void(0)" onclick="addSoftwareItem(this)" id="addSoftwareItem" data-id='<?php echo $index;?>' class="btn btn-link"><i class="icon-plus"></i></a>
        <a href="javascript:void(0)" onclick="delSoftwareItem(this)" id="delSoftwareItem" data-id='<?php echo $index;?>' class="btn btn-link"><i class="icon-close"></i></a>
      </td>
    </tr>
    <?php endforeach;?>
    <tr id="softwareTr0">
      <th><?php echo $lang->customflow->conf;?></th>
      <td><?php echo html::input('flowCode[]',   '', "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowName[]',   '', "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowView[]',   '', "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowAssign[]', '', "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowApp[]',    '', "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::input('flowOrder[]',  '',  "class='form-control' autocomplete='off'")?></td>
      <td><?php echo html::radio('partyEnable0', $lang->customflow->partyEnableList, 'disable')?></td>
      <td>
        <a href="javascript:void(0)" onclick="addSoftwareItem(this)" id="addSoftwareItem" data-id='0' class="btn btn-link"><i class="icon-plus"></i></a>
        <a href="javascript:void(0)" onclick="delSoftwareItem(this)" id="delSoftwareItem" class="btn btn-link invisible"><i class="icon-close"></i></a>
      </td>
    </tr>
    <tr>
      <th></th>
      <td class='text-left form-actions'>
        <?php echo html::submitButton();?>
      </td>
    </tr>
  </table>
  </form>
</div>
<?php js::set('softwareTotal', count($customList));?>
<script>
function addSoftwareItem(obj)
{
    var tableDataID = $(obj).attr('data-id');
    softwareTotal = parseInt(softwareTotal) + 1;

     var tableObject = $('#softwareTr0');
    //var tableObject = $(obj).parent().parent();
    var tableObject = tableObject.clone();

    tableObject.find('#addSoftwareItem').attr('data-id', softwareTotal);
    tableObject.find('#delSoftwareItem').attr('data-id', softwareTotal);
    tableObject.find('#delSoftwareItem').removeClass('invisible');
    tableObject.find('#partyEnable1enable').attr('name', 'partyEnable' + softwareTotal);
    tableObject.find('#partyEnable1disable').attr('name', 'partyEnable' + softwareTotal);

    var trHtml = tableObject.html();
    var trHtml = '<tr id="softwareTr'+ softwareTotal +'">' + trHtml + '</tr>';
    $('#softwareTr' + tableDataID).after(trHtml);
}
function delSoftwareItem(obj)
{
    var tableDataID = $(obj).attr('data-id');
    $('#softwareTr' + tableDataID).remove();
}
</script>
<?php include '../../common/view/footer.html.php';?>
