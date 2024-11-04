<?php
/**
 * The set view file of custom module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     custom
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php
  $oldDir = getcwd();
  chdir(dirname(dirname(dirname(__FILE__))) . '/view');
  include './header.html.php';
  chdir($oldDir);
?>
<?php
$itemRow = <<<EOT
  <tr class='text-center'>
    <td>
      <input type='text' class="form-control" autocomplete="off" value="" name="keys[]">
      <input type='hidden' value="0" name="systems[]">
    </td>
    <td>
      <input type='text' class="form-control" value="" autocomplete="off" name="values[]">
    </td>
    <td class='c-actions'>
      <a href="javascript:void(0)" class='btn btn-link' onclick="addItem(this)"><i class='icon-plus'></i></a>
      <a href="javascript:void(0)" class='btn btn-link' onclick="delItem(this)"><i class='icon-close'></i></a>
    </td>
  </tr>
EOT;
?>
<?php js::set('itemRow', $itemRow)?>
<?php js::set('module',  $module)?>
<?php js::set('field',   $field)?>
<style>
.checkbox-primary {width: 170px; margin: 0 10px 10px 0; display: inline-block;}
</style>
<div id='mainContent' class='main-row'>
  <div class='side-col' id='sidebar'>
    <div class='cell'>
      <div class='list-group'>
        <?php
        foreach($lang->custom->{$module}->fields as $key => $value)
        {
            echo html::a(inlink('set', "module=$module&field=$key"), $value, '', " id='{$key}Tab'");
        }
        ?>
      </div>
    </div>
  </div>
  <div class='main-col main-content'>
    <form class="load-indicator main-form form-ajax" method='post'>
      <div class='main-header'>
        <div class='heading'>
          <strong><?php echo $lang->custom->object[$module] . $lang->arrow . $lang->custom->$module->fields[$field]?></strong>
        </div>
      </div>
      <table class='table table-form'>
        <tr>
          <th class='w-40px'></th>
          <th class='w-80px text-left'><?php echo $lang->custom->$module->typeList;?></th>
          <th class='w-100px text-left'><?php echo $lang->custom->$module->childTypeListKey;?></th>
          <th class='w-100px text-left'><?php echo $lang->custom->$module->childTypeListValue;?></th>
          <th class='w-100px text-left'><?php echo $lang->actions;?></th>
        </tr>
        <?php $index = 0;?>
        <?php foreach($customList as $custom):?>
        <?php $index = $index + 1;?>
        <tr id='softwareTr<?php echo $index;?>'>
          <th><?php echo $lang->custom->$module->typeConf;?></th>
          <td><?php echo html::select('typeList[]', $typeList, $custom['typeList'], "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::input('childTypeListKey[]', $custom['childTypeListKey'], "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::input('childTypeListValue[]', $custom['childTypeListValue'], "class='form-control' autocomplete='off'")?></td>
          <td>
            <a href="javascript:void(0)" onclick="addSoftwareItem(this)" id="addSoftwareItem" data-id='<?php echo $index;?>' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delSoftwareItem(this)" id="delSoftwareItem" data-id='<?php echo $index;?>' class="btn btn-link"><i class="icon-close"></i></a>
          </td>
        </tr>
        <?php endforeach;?>
        <tr id='softwareTr0'>
          <th><?php echo $lang->custom->$module->typeConf;?></th>
          <td><?php echo html::select('typeList[]', $typeList, '', "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::input('childTypeListKey[]', '', "class='form-control' autocomplete='off'")?></td>
          <td><?php echo html::input('childTypeListValue[]', '', "class='form-control' autocomplete='off'")?></td>
          <td>
            <a href="javascript:void(0)" onclick="addSoftwareItem(this)" id="addSoftwareItem" data-id='0' class="btn btn-link"><i class="icon-plus"></i></a>
            <a href="javascript:void(0)" onclick="delSoftwareItem(this)" id="delSoftwareItem" class="btn btn-link invisible"><i class="icon-close"></i></a>
          </td>
        </tr>
        <tr>
          <td colspan='4' class='text-center'><?php echo html::submitButton();?></td>
        </tr>
      </table>
    </form>
    <div class="alert alert-info alert-block"><?php echo $lang->custom->$module->childTypeFileTip;?></div>
  </div>
</div>
<?php js::set('softwareTotal', count($customList));?>
<script>
$(function()
{
    $('#' + module + 'Tab').addClass('btn-active-text');
    $('#' + field + 'Tab').addClass('active');
})
</script>
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
<?php include '../../../common/view/footer.html.php';?>
