<?php
/**
 * The operate view file of flow module of ZDOO.
 *
 * @copyright   Copyright 2009-2016 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     商业软件，非开源软件
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     flow
 * @version     $Id$
 * @link        http://www.zdoo.com
 */
?>
<?php
$isModal = $action->open == 'modal';
$colspan = $isModal ? '' : "colspan='2'";
if($isModal)
{
    include '../../common/view/header.modal.html.php';
}
else
{
    include 'header.html.php';
}
$editorModule = $action->action == 'edit' ? 'edit' : 'operate';
if(!empty($this->config->flow->editor->$editorModule)) include '../../common/view/kindeditor.html.php';
?>
<?php include '../../common/view/picker.html.php';?>
<?php if(!empty($flow->css)) css::internal($flow->css);?>
<?php if(!empty($action->css)) css::internal($action->css);?>
<?php js::set('module', $flow->module);?>
<?php js::set('action', $action->action);?>
<?php if(!$isModal):?>
<div class='panel'>
  <div class='panel-heading'>
    <strong><?php echo str_replace('-', '', $title);?></strong>
  </div>
  <div class='panel-body'>
<?php endif;?>
    <form id='operateForm' method='post' enctype='multipart/form-data' action='<?php echo $actionURL;?>'>
      <table class='table table-form'>
        <?php $hasChildFields = false;?>
        <?php $childKey       = 1;?>
        <?php foreach($fields as $field):?>
        <?php if(!$field->show) continue;?>
        <?php $readonly = $field->readonly;?>
        <?php $width    = ($field->width && $field->width != 'auto' ? $field->width . 'px' : 'auto');?>

        <?php if($readonly)  $value = zget($data, $field->field, ($field->defaultValue ? $field->defaultValue : ''));?>
        <?php if(!$readonly) $value = $field->defaultValue ? $field->defaultValue : zget($data, $field->field, '');?>

        <?php /* Print files. */ ?>
        <?php if($field->control == 'file'):?>
        <tr>
          <th class='w-100px'><?php echo $field->name;?></th>
          <td>
            <?php if($readonly) echo $this->fetch('file', 'printFiles', array('files' => $data->files{$field->field}, 'fieldset' => 'false'));?>
            <?php if(!$readonly) echo $this->fetch('file', 'buildForm', "fileCount=1&percent=0.9&filesName=files{$field->field}&labelsName=labels{$field->field}");?>
          </td>
          <?php if(!$isModal):?>
          <td></td>
          <?php endif;?>
        </tr>

        <?php /* Print mailto. */ ?>
        <?php elseif($field->field == 'mailto'):?>
        <tr>
          <th class='w-100px'><?php echo $lang->workflowaction->toList;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('mailto[]', $users, $value, "class='form-control chosen' data-placeholder='{$lang->chooseUserToMail}' multiple");?>
              <?php echo $this->fetch('my', 'buildContactLists');?>
            </div>
          </td>
          <?php if(!$isModal):?>
          <td class='text-important'><?php echo $lang->flow->tips->notice;?></td>
          <?php endif;?>
        </tr>

        <?php /* Print sub tables. */ ?>
        <?php elseif(isset($childFields[$field->field])):?>
        <?php $hasChildFields = true;?>
        <?php $childModule    = $field->field;?>
        <tr>
          <th><?php echo $field->name;?></th>
          <td <?php echo $colspan;?> class='child'>
            <table class='table table-form table-child' data-child='<?php echo $field->field;?>' id='<?php echo $field->field;?>' style='width: <?php echo $width;?>'>
              <?php $datas = isset($childDatas[$field->field]) ? $childDatas[$field->field] : array('');?>
              <?php foreach($datas as $childData):?>
              <tr>
                <?php foreach($childFields[$field->field] as $childField):?>
                <?php if(!$childField->show) continue;?>
                <?php if($childField->control == 'file') continue;?>
                <?php $childWidth = ($childField->width && $childField->width != 'auto' ? $childField->width . 'px' : 'auto');?>

                <?php if($readonly or $childField->readonly)    $childValue = zget($childData, $childField->field, ($childField->defaultValue ? $childField->defaultValue : ''));?>
                <?php if(!$readonly and !$childField->readonly) $childValue = $childField->defaultValue ? $childField->defaultValue : zget($childData, $childField->field, '');?>
                <td style='width: <?php echo $childWidth;?>'>
                  <?php
                  if($readonly or $childField->readonly)
                  {
                      if($childField->control == 'multi-select' or $childField->control == 'checkbox')
                      {
                          $childValues = explode(',', $childValue);
                          foreach($childValues as $childV)
                          {
                              if(in_array($childV, $this->config->flow->variables)) $childV = $this->loadModel('workflowhook')->getParamRealValue($childV);

                              echo zget($field->options, $childV, '') . ' ';
                          }
                      }
                      else
                      {
                          echo zget($childField->options, $childValue);
                      }

                      html::hidden("children[$childModule][$childField->field][$childKey]", $childValue);
                  }
                  else
                  {
                      $element = "children[$childModule][$childField->field][$childKey]";

                      echo $this->flow->buildControl($childField, $childValue, $element, $field->field);
                  }
                  ?>
                </td>
                <?php endforeach;?>
                <?php if(!$readonly):?>
                <td class='w-100px'>
                  <?php echo html::hidden("children[$childModule][id][$childKey]", $childData->id);?>
                  <a href='javascript:;' class='btn btn-default addItem'><i class='icon-plus'></i></a>
                  <a href='javascript:;' class='btn btn-default delItem'><i class='icon-close'></i></a>
                </td>
                <?php endif;?>
              </tr>
              <?php $childKey++;?>
              <?php endforeach;?>

              <?php /* Add a empty row of sub table. */ ?>
              <?php if(!$readonly and empty($datas)):?>
              <tr>
                <?php foreach($childFields[$field->field] as $childField):?>
                <?php if(!$childField->show) continue;?>
                <?php if($childField->field == 'file') continue;?>
                <td>
                  <?php $element = "children[$childModule][$childField->field][$childKey]";?>
                  <?php if($childField->control == 'multi-select' or $childField->control == 'checkbox') $element = "children[$childModule][$childField->field][$childKey][]";?>
                  <?php echo $this->flow->buildControl($childField, '', $element, $childModule, $emptyValue = true);?>
                </td>
                <?php endforeach;?>
                <td class='w-100px'>
                  <?php echo html::hidden("children[$childModule][id][$childKey]");?>
                  <a href='javascript:;' class='btn btn-default addItem'><i class='icon-plus'></i></a>
                  <a href='javascript:;' class='btn btn-default delItem'><i class='icon-close'></i></a>
                </td>
              </tr>
              <?php $childKey++;?>
              <?php endif;?>
            </table>
          </td>
        </tr>

        <?php /* Print other fields. */ ?>
        <?php else:?>
        <?php
        $attr     = '';
        $relation = zget($relations, $field->field, '');
        if($relation && strpos(",$relation->actions,", ',many2one,') === false)
        {
            $prevDataID = isset($data->{$field->field}) ? $data->{$field->field} : 0;
            $attr       = "class='prevTR' data-prev='{$relation->prev}' data-next='{$relation->next}' data-action='$action->action' data-field='{$relation->field}' data-dataID='$prevDataID'";
        }
        ?>
        <tr <?php echo $attr;?>>
          <th class='w-100px'><?php echo $field->name;?></th>
          <td>
            <div style='width: <?php echo $width;?>'>
              <?php
              if($readonly)
              {
                  if($field->control == 'multi-select' or $field->control == 'checkbox')
                  {
                      $values = explode(',', $value);
                      foreach($values as $v)
                      {
                          if(in_array($v, $this->config->flow->variables)) $v = $this->loadModel('workflowhook')->getParamRealValue($v);

                          echo zget($field->options, $v, '') . ' ';
                      }
                  }
                  else
                  {
                      echo zget($field->options, $value);
                  }

                  if($field->control == 'textarea' or $field->control == 'richtext')
                  {
                      echo html::textarea($field->field, $value, "class='hidden'");
                  }
                  else
                  {
                      echo html::hidden($field->field, $value);
                  }
              }
              else
              {
                  echo $this->flow->buildControl($field, $value);
              }
              ?>
            </div>
          </td>
          <?php if(!$isModal):?>
          <td></td>
          <?php endif;?>
        </tr>
        <?php endif;?>
        <?php endforeach;?>

        <tr>
          <th></th>
          <td <?php echo $colspan;?> class='form-actions'>
            <?php echo baseHTML::submitButton();?>
            <?php if(!$isModal) echo html::backButton();?>
          </td>
        </tr>
      </table>
    </form>
<?php if(!$isModal):?>
  </div>
</div>
<?php endif;?>

<?php /* The table below is used to generate dom when click plus button. */ ?>
<?php
if($hasChildFields)
{
    $itemRows = array();
    foreach($childFields as $childModule => $moduleFields)
    {
        $itemRow = '<tr>';
        foreach($moduleFields as $childField)
        {
            if(!$childField->show) continue;
            if($childField->control == 'file') continue;
            $element = "children[$childModule][$childField->field][KEY]";
            $childWidth = ($childField->width && $childField->width != 'auto' ? $childField->width . 'px' : 'auto');
            $itemRow .= "<td style='width: {$childWidth}'>";
            $itemRow .= $this->flow->buildControl($childField, $childField->defaultValue, $element, $childModule);
            $itemRow .= '</td>';
        }
        $itemRow .= "<td class='w-100px'>";
        $itemRow .= html::hidden("children[$childModule][id][KEY]");
        $itemRow .= "<a href='javascript:;' class='btn btn-default addItem'><i class='icon-plus'></i></a> ";
        $itemRow .= "<a href='javascript:;' class='btn btn-default delItem'><i class='icon-close'></i></a>";
        $itemRow .= '</td>';
        $itemRow .= '</tr>';

        $itemRows[$childModule] = $itemRow;
    }

    js::set('itemRows', $itemRows);
}
?>

<?php js::set('childKey', $childKey);?>
<?php if($formulaScript) echo $formulaScript;?>
<?php if($linkageScript) echo $linkageScript;?>
<script>
<?php if($isModal):?>
<?php else:?>
$(document).on('click', 'td.child .addItem', function()
{
    var child = $(this).parents('table').data('child');
    $(this).closest('tr').after(itemRows[child].replace(/KEY/g, childKey));
    initSelect($(this).closest('tr').next().find('.picker-select'));
    $(this).closest('tr').next().find('.form-date').datetimepicker(
    {
        language:  config.clientLang,
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
        format: 'yyyy-mm-dd'
    });
    $(this).closest('tr').next().find('.form-datetime').datetimepicker(
    {
        language:  config.clientLang,
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
        format: 'yyyy-mm-dd hh:ii'
    });

    childKey++;
});

$(document).on('click', 'td.child .delItem', function()
{
    if($(this).parents('.table-child').find('tr').size() > 1)
    {
        $(this).closest('tr').remove();
    }
    else
    {
        $(this).closest('tr').find('input,select,textarea').val('');
    }
})
<?php endif;?>
</script>
<?php if(!empty($flow->js)) js::execute($flow->js);?>
<?php if(!empty($action->js)) js::execute($action->js);?>
<script>
<?php helper::import('../js/search.js');?>
</script>
<?php if($isModal):?>
<?php include '../../common/view/footer.modal.html.php';?>
<?php else:?>
<?php include 'footer.html.php';?>
<?php endif;?>
