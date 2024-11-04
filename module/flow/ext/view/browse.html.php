<?php
/**
 * The browse view file of flow module of ZDOO.
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
$oldDir = getcwd();
chdir(dirname(dirname($oldDir)) . '/view');
include './header.html.php';
chdir($oldDir);
include '../../../common/view/picker.html.php';
?>

<?php if(!empty($flow->css)) css::internal($flow->css);?>
<?php if(!empty($action->css)) css::internal($action->css);?>
<?php js::set('mode', $mode);?>
<?php js::set('module', $flow->module);?>
<?php js::set('label', $label);?>
<?php js::set('category', zget($currentCategory, 'type', '') . $categoryValue);?>

<?php /* Search settings. */ ?>
<?php if(commonModel::hasPriv($flow->module, 'search')):?>
<?php if(empty($this->config->{$flow->module}->search['fields'])):?>
<li id='searchTab'><?php echo baseHTML::a('#emptySearchModal', "<i class='icon-search icon'></i>" . $lang->search->common, "data-toggle='modal'");?></li>
<div class='modal fade' id='emptySearchModal'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>×</span></button>
        <h4 class='modal-title'>
          <span class='modal-title-name'><?php echo $lang->search->common;?></span>
        </h4>
      </div>
      <div class='modal-body'>
        <?php
        if(commonModel::hasPriv('flow.workflowfield', 'setSearch'))
        {
            $designLink = baseHTML::a($this->createLink('workflowfield', 'setSearch', "module={$flow->module}"), $lang->flow->setSearch, "target='_parent'");
        }
        else
        {
            $designLink = $lang->flow->setSearch;
        }
        echo "<div class='alert'>" . sprintf($lang->flow->tips->emptySearchFields, $designLink) . '</div>';
        ?>
      </div>
    </div>
  </div>
</div>
<?php else:?>
<li id='bysearchTab'><?php echo baseHTML::a('javascript:;', "<i class='icon-search icon'></i>" . $lang->search->common);?></li>
<?php endif;?>
<?php endif;?>

<?php echo $menuActions;?>

<?php /* Tree Menu */ ?>
<?php if($categories):?>
<div class='main-row'>
  <div class='side-col'>
    <div class='main-col panel'>
      <a href='javascript:;' class='dropdown-toggle currentMenu' data-toggle='dropdown'>
        <?php echo $currentCategory->name;?>
        <?php if(count($categories) > 1):?>
        <span class='caret'></span>
        <?php endif;?>
      </a>
      <?php if(count($categories) > 1):?>
      <ul class='dropdown-menu'>
        <?php foreach($categories as $type => $category):?>
        <li><a href='javascript:;' data-type='<?php echo $type;?>' class='toggleTreeMenu'><?php echo $category->name;?></a></li>
        <?php endforeach;?>
      </ul>
      <?php endif;?>
    </div>
    <div class='main-col panel-body'>
      <?php foreach($categories as $type => $category):?>
      <div id='<?php echo $type;?>Box' class='treeMenuBox <?php if($type != $currentCategory->type) echo 'hide';?>'>
        <?php echo $category->treeMenu;?>
        <?php extCommonModel::printLink('tree', 'browse', "type=$type&startModule=&root=&from=$flow->module", sprintf($lang->flow->category->manage, $category->name), "class='btn btn-primary'");?>
      </div>
      <?php endforeach;?>
    </div>
  </div>
</div>
<div class='main-col'>
<?php endif;?>

<div class='main-col' data-ride='table'>
  <div class='main-col'>
    <?php if($batchActions && $dataList):?>
    <form id='batchOperateForm' method='post' data-ride='table'>
    <?php endif;?>
  <div class='main-table'>
    <table class='table has-sort-head' id="<?php echo $flow->module;?>Table">
      <thead>
        <tr class='text-center'>
          <?php
          $vars = '';
          if($mode != 'browse') $vars = "mode=$mode&";
          $vars .= "label=$label&category=$categoryQuery&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";
          ?>
          <?php $index = 1;?>
          <?php foreach($fields as $field):?>
          <?php if(!$field->show) continue;?>
          <?php $width = ($field->width && $field->width != 'auto' ? $field->width . 'px' : 'auto');?>
          <th class="text-<?php echo $field->position;?> c-<?php echo $field->field;?>" style="width:<?php echo $width;?>">
            <?php if($index == 1 && $batchActions && $dataList):?>
            <div class='checkbox-primary check-all' title='<?php echo $this->lang->selectAll;?>'><label></label></div>
            <?php endif;?>
            <?php
            if($field->field == 'desc' || $field->field == 'asc' || $field->field == 'actions')
            {
                echo $field->name;
            }
            else
            {
                commonModel::printOrderLink($field->field, $orderBy, $vars, $field->name, $flow->module, 'browse');
            }
            ?>
          </th>
          <?php $index++;?>
          <?php endforeach;?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($dataList as $data):?>
        <tr>
          <?php $index = 1;?>
          <?php foreach($fields as $field):?>
          <?php if(!$field->show || $field->field == 'actions') continue;?>
          <?php
          $output = '';
          if(is_array($data->{$field->field}))
          {
              foreach($data->{$field->field} as $value) $output .= zget($field->options, $value) . ' ';
          }
          else
          {
              if($field->field == 'id' or $field->field == 'name' or $field->field == 'title')
              {
                  if(commonModel::hasPriv($flow->module, 'view'))
                  {
                      $output = baseHTML::a(helper::createLink($flow->module, 'view', "dataID={$data->id}"), $data->{$field->field});
                  }
                  else
                  {
                      $output = $data->{$field->field};
                  }
              }
              elseif($field->field == 'assignedTo')
              {
                  $assignedTo = zget($field->options, $data->{$field->field});
                  $assignedTo = empty($assignedTo) ? $lang->flow->noAssigned : $assignedTo;
                  if(commonModel::hasPriv($flow->module, 'assign') and $this->flow->checkConditions($action->conditions, $data))
                  {
                      $output = baseHTML::a(helper::createLink($flow->module, 'assign', "dataID={$data->id}"), "<i class='icon icon-hand-right'></i> {$assignedTo}", "data-toggle='modal' class='btn btn-icon-left btn-sm'");
                  }
                  else
                  {
                      $output = $assignedTo;
                  }
              }
              else
              {
                  $output = zget($field->options, $data->{$field->field});
                  if(is_numeric($output) and in_array($field->type, $config->workflowfield->numberTypes)) $output = formatMoney($output);
              }
          }
          ?>
          <td class="text-<?php echo $field->position;?>" title='<?php echo strip_tags(str_replace("</p>", "\n", str_replace(array("\n", "\r"), "", $output)));?>'>
            <?php if($index == 1 && $batchActions):?>
            <div class='checkbox-primary'><input type='checkbox' name='dataIDList[]' value='<?php echo $data->id;?>' id='dataIDList<?php echo $data->id;?>'>
              <label for='dataIDList<?php echo $data->id;?>'></label>
            </div>
            <?php endif;?>
            <?php echo $output;?>
          </td>
          <?php $index++;?>
          <?php endforeach;?>
          <td class="nowrap"><?php echo $this->flow->buildOperateMenu($flow, $data, 'browse');?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
    <?php if($batchActions && $dataList):?>
    </form>
    <?php endif;?>
    <div class='table-footer'>
      <?php if($batchActions && $dataList):?>
      <div class='checkbox-primary check-all'><label><?php echo $lang->selectAll?></label></div>
      <div class='table-actions btn-toolbar'>
        <?php echo $batchActions;?>
      </div>
      <?php endif;?>
      <?php if($summary):?>
      <div class='table-statistic'>
        <?php echo $lang->workflowlayout->summary . '(' . $summary . ')';?>
      </div>
      <?php endif;?>
      <?php if($mode == 'browse') unset($this->app->rawParams['mode']);?>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </div>
</div>

<?php if($categories):?>
  </div>
</div>
<?php endif;?>
<?php if(!empty($flow->js)) js::execute($flow->js);?>
<?php if(!empty($action->js)) js::execute($action->js);?>
<script>
<?php helper::import('../../js/search.js');?>
</script>
<?php
$oldDir = getcwd();
chdir(dirname(dirname($oldDir)) . '/view');
include './footer.html.php';
chdir($oldDir);
?>
