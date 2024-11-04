<?php
$index           = 1;
$disabledFields  = in_array($action->action, $config->workflowaction->defaultActions) ? $config->workflowlayout->disabledFields[$action->action] : $config->workflowlayout->disabledFields['custom'];
$controlList     = array('select', 'multi-select', 'checkbox', 'radio', 'date', 'datetime');
$dateControlList = array('date', 'datetime');
?>

<table class='table table-bordered table-fixed table-origin'>
  <?php /* Begin foreach of fields. */ ?>
  <?php foreach($fields as $key => $field):?>
  <?php
  if($mode == 'view' && !$field->show) continue;
  if(strpos(",{$disabledFields},", ",{$key},") !== false) continue;
  $required = $key == 'actions';
  $fixed    = $required ? 'required' : 'enabled';
  $show     = $field->show == '1';
  $subTable = isset($subTables[$key]);
  $disabled = $mode == 'edit' ? '' : "disabled='disabled'";
  ?>

  <tr class='<?php echo (!$show ? ' disabled' : '') . ($required ? ' required' : '') . (' fixed-' . $fixed) . ($subTable ? " module-{$key}" : '');?>' <?php echo $subTable ? "data-child={$key}" : '';?> data-fixed='<?php echo $fixed;?>' data-module='<?php echo $field->module;?>' data-field='<?php echo $field->field;?>' data-control='<?php echo $field->control;?>'>
    <?php /* Row title. */ ?>
    <td class='title'>
      <i class='icon-check'></i>
      <span class='title-bar' title='<?php echo $field->name;?>'>
        <strong><?php echo $field->name;?></strong>
        <?php if($mode == 'edit'):?>
        <i class='icon icon-move'></i>
        <?php endif;?>
      </span>
      <?php /* Row Actions. */ ?>
      <?php if($required):?>
      <?php echo '(' . $lang->workflowlayout->require . ')';?>
      <?php endif;?>
    </td>

    <?php /* Summary. */ ?>
    <?php if($action->action == 'browse'):?>
    <?php if(in_array($field->type, $config->workflowfield->numberTypes) && strpos(",{$config->workflowlayout->noTotalFields},", ",{$field->field},") === false):?>
    <td class='w-300px'>
      <div class='input-group'>
        <span class='text-muted input-group-addon'><?php echo $lang->workflowlayout->summary;?></span>
        <?php echo html::select("summary[$key][]", $lang->workflowlayout->summaryList, !empty($field->summary) ? $field->summary: 0, "class='form-control chosen' multiple $disabled");?>
      </div>
    </td>
    <?php else:?>
    <td></td>
    <?php endif;?>
    <?php endif;?>

    <?php /* Width. */ ?>
    <?php if($action->action != 'view'):?>
    <td class='w-130px'>
      <div class='input-group'>
        <span class='input-group-addon text-muted'><?php echo $lang->workflowlayout->width;?></span>
        <?php echo html::input("width[$key]", $field->width, "class='form-control' $disabled");?>
      </div>
    </td>
    <?php endif;?>

    <?php if($action->action != 'browse' && $action->action != 'view' && !is_numeric($key) && $action->action != 'admin' && $action->action != 'adminview'):?>
    <?php /* Layout rules. */ ?>
    <td class='w-160px'>
      <div class='input-group'>
        <span class='text-muted input-group-addon'><?php echo $lang->workflowlayout->layoutRules;?></span>
        <?php echo html::select("layoutRules[$key][]", $rules, $field->layoutRules, "class='form-control chosen' multiple='multiple' $disabled");?>
      </div>
    </td>

    <?php if($field->control != 'file'):?>
    <?php /* Default value. */ ?>
    <td class='w-280px'>
      <div class='input-group'>
        <span class='text-muted input-group-addon'><?php echo $lang->workflowlayout->defaultValue;?></span>
        <?php
        $data = "data-module='{$flow->module}' data-field='{$field->field}'";
        if($field->control == 'multi-select' or $field->control == 'checkbox')
        {
            echo str_replace('picker-select', 'chosen', html::select("defaultValue[$key][]", $field->options, $field->defaultValue, "id='defaultValue{$key}' class='form-control chosen' multiple $data $disabled"));
        }
        else
        {
            echo html::select("defaultValue[$key]", array('' => '') + $field->options, $field->defaultValue, "id='defaultValue{$key}' class='form-control picker-select' $data $disabled");
        }
        echo "<span class='input-group-addon fix-border'></span>";
        if(in_array($field->control, $dateControlList))
        {
            $class = 'form-' . $field->control;
            echo html::input("defaultValue[$key]", ($field->defaultValue && $field->defaultValue != 'currentTime') ? $field->defaultValue : '', "id='defaultValue{$key}' class='form-control $class' $disabled");
        }
        else
        {
            echo html::input("defaultValue[$key]", (isset($field->defaultValue) && strpos(',currentUser,currentDept,currentTime,', ",$field->defaultValue,") === false) ? $field->defaultValue : '', "id='defaultValue{$key}' class='form-control' disabled='disabled'");
        }
        $checked = '';
        if(!in_array($field->control, $controlList)) $checked .= "checked='checked'";
        if(in_array($field->control, $dateControlList) && !empty($field->defaultValue) && $field->defaultValue != 'currentTime') $checked .= "checked='checked'";
        if(!in_array($field->control, $dateControlList)) $checked .= "disabled='disabled'";
        ?>
        <span class='input-group-addon'><input type='checkbox' name="custom[<?php echo $key;?>]" value='1' <?php echo "$checked $disabled";?>/> <?php echo $lang->workflowlayout->custom;?></span>
      </div>
    </td>
    <?php else:?>
    <td class='w-280px'>
      <div class='input-group'>
        <span class='text-muted input-group-addon'><?php echo $lang->workflowlayout->defaultValue;?></span>
        <?php echo html::input("defaultValue[$key]", '', "id='defaultValue{$key}' class='form-control' disabled='disabled'");?>
        <span class='input-group-addon'><input type='checkbox' name="custom[<?php echo $key;?>]" value='1' disabled='disabled'/> <?php echo $lang->workflowlayout->custom;?></span>
      </div>
    </td>
    <?php endif;?>
    <?php endif;?>

    <?php if($action->action == 'browse' or ($action->module == 'feedback' && $action->action == 'admin') or ($action->type == 'batch' && $action->batchMode == 'different')):?>
    <?php /* Display in mobile device. */ ?>
    <td class='w-160px'>
      <div class='input-group'>
        <span class='text-muted input-group-addon'><?php echo $lang->workflowlayout->mobileShow;?></span>
        <?php echo html::select("mobileShow[$key]", $lang->workflowlayout->mobileList, !empty($field->mobileShow) ? $field->mobileShow : 0, "class='form-control' $disabled");?>
      </div>
    </td>
    <?php endif;?>

    <?php /* Position. */ ?>
    <?php if($action->action == 'view' or $action->action == 'browse' or $action->layout == 'side' or ($action->module == 'feedback' and ($action->action == 'adminview' or $action->action == 'admin'))):?>
    <?php $width = $action->action == 'view' ? 'w-200px' : 'w-130px';?>
        <td class="<?php echo $width;?>">
      <div class='input-group'>
        <span class='text-muted input-group-addon'>
          <?php if($action->action == 'view' and $index == 1):?>
          <a data-toggle='tooltip' class='position-tips' title='<?php echo $lang->workflowlayout->tips->position;?>'><i class='icon-help-circle icon-md'></i></a>
          <?php endif;?>
          <?php echo $lang->workflowlayout->position;?>
        </span>
        <?php
        if($action->module == 'feedback' and $action->action == 'adminview') $positionList = $lang->workflowlayout->positionList['view'];
        if($action->module == 'feedback' and $action->action == 'admin') $positionList = $lang->workflowlayout->positionList['browse'];
        ?>
        <?php echo html::select("position[$key]", $positionList, !empty($field->position) ? $field->position : zget($defaultPositions, $field->field, ''), "class='form-control' $disabled");?>
      </div>
    </td>
    <?php endif;?>

    <?php /* Readonly. */ ?>
    <?php if($action->type == 'single' && !in_array($action->action, $config->workflowaction->readonlyActions) && $field->field != 'actions'):?>
    <td class='w-60px'>
      <?php $checked = $field->readonly ? "checked='checked'" : '';?>
      <input type='checkbox' name="readonly[<?php echo $key;?>]" value='1' <?php echo "$checked $disabled";?>/> <?php echo $lang->workflowlayout->readonly;?>
    </td>
    <?php endif;?>

    <?php /* Display or not. */ ?>
    <td class='w-100px text-center'>
      <?php if($mode == 'edit'):?>
      <button type='button' class='btn btn-link show-hide'>
        <span class='label-show'><?php echo $lang->workflowlayout->show;?></span>
        <span class='text-muted'>/</span>
        <span class='label-hide'><?php echo $lang->workflowlayout->hide;?></span>
      </button>
      <?php else:?>
      <?php echo $show ? $lang->workflowlayout->show : $lang->workflowlayout->hide;?>
      <?php endif;?>
      <?php echo html::hidden("show[$key]",  $show ? '1' : '0');?>
    </td>

  </tr>
  <?php $index++;?>
  <?php endforeach;?>
  <?php /* End foreach of fields. */ ?>
</table>
