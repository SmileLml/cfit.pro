<?php
/**
 * The detail view file of flow module of ZDOO.
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
if($flowAction->open == 'modal')
{
    include '../../common/view/header.modal.html.php';
}
else
{
    include 'header.html.php';
}
include '../../common/view/kindeditor.html.php';
$activeTab = $flow->module;
if($currentType)
{
    if(isset($linkedDatas[$currentType]))
    {
        $activeTab = $currentType;
    }
    else
    {
        if($currentMode == 'bysearch') $activeTab = 'common';
    }
}
$canLink     = $this->flow->isClickable($flow->module, 'link', $data);
$sessionName = $flow->module . 'List';
$browseLink  = $this->session->$sessionName ? $this->session->$sessionName : $this->createLink($flow->module, 'browse');
$loadLink    = $this->createLink($flow->module, 'link', "dataID=$data->id&linkType=LINKTYPE&mode=MODE");

if(!empty($flow->css)) css::internal($flow->css);
if(!empty($flowAction->css)) css::internal($flowAction->css);
js::set('module', $flow->module);
js::set('dataID', $data->id);
js::set('linkType', $currentType);
js::set('viewMode', $currentMode);
js::set('loadLink', $loadLink);
?>
<div id='mainTitle' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php if(!isonlybody()):?>
    <?php echo baseHTML::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $data->id;?></span>
      <span class="text" title='<?php echo zget($dataPairs, $data->id);?>'><?php echo zget($dataPairs, $data->id);?></span>
    </div>
  </div>
</div>
<div class='tabs' id='tabsNav'>
  <ul class='nav nav-tabs'>
    <?php
    $attr = $activeTab == $flow->module ? "class='active'" : '';
    echo "<li $attr><a href='#{$flow->module}' data-toggle='tab'>{$flow->name}{$lang->flow->detail}</a></li>";

    foreach($linkedDatas as $linkType => $datas)
    {
        $attr = $activeTab == $linkType ? "class='active'" : '';
        echo "<li $attr><a href='#{$linkType}' data-toggle='tab'>" . zget($linkPairs, $linkType) . '</a></li>';
    }

    if($canLink)
    {
        $attr     = $activeTab == 'common' ? "class='active'" : "class='hidden'";
        $tabTitle = $activeTab == 'common' ? zget($linkPairs, $currentType) : $lang->workflowaction->default->actions['link'];
        echo "<li $attr><a href='#common' data-toggle='tab'>{$tabTitle}</a></li>";
    }
    ?>
  </ul>
  <div class='tab-content'>
    <?php $active = $activeTab == $flow->module ? 'active' : '';?>
    <div id='<?php echo $flow->module;?>' class='tab-pane <?php echo $active;?> <?php if($canLink) echo 'with-link-button';?>'>
      <div class='actions'>
        <?php if($canLink) echo baseHTML::a('#linkTypeBox', $lang->workflowaction->default->actions['link'], "class='btn btn-primary' data-toggle='modal'");?>
      </div>
      <div class='main-row'>
        <div class='main-col col-9'>
          <div class='panel'>
            <div class='panel-body'>
              <?php
              $children = array();
              foreach($fields as $field)
              {
                  if(!$field->show) continue;
                  if($field->position != 'info') continue;

                  if(isset($childFields[$field->field]))
                  {
                      $children[$field->field] = $field->name;
                      continue;
                  }

                  if($field->control == 'file')
                  {
                      $filesName = "{$field->field}files";
                      if(!empty($data->{$filesName}))
                      {
                          echo '<p><strong>' . $field->name . $lang->colon . '</strong>' . $this->fetch('file', 'printFiles', array('files' => $data->{$filesName}, 'fieldset' => 'false')) . '</p>';
                      }
                  }
                  else
                  {
                      $attr     = '';
                      $link     = '';
                      $relation = zget($relations, $field->field, '');
                      if($relation && strpos(",$relation->actions,", ',many2one,') === false)
                      {
                          $prevDataID = isset($data->{$field->field}) ? $data->{$field->field} : 0;
                          $attr       = "class='prevP' data-prev='{$relation->prev}' data-next='{$relation->next}' data-action='$flowAction->action' data-field='{$relation->field}' data-dataID='$prevDataID'";

                          if(commonModel::hasPriv($relation->prev, 'view')) $link = $this->createLink($relation->prev, 'view', "dataID=$prevDataID");
                      }

                      echo "<p $attr>";
                      echo "<strong>" . $field->name . "</strong>";

                      $fieldValue = $field->field;
                      if(!empty($data->{$field->field}))
                      {
                          if(is_array($data->{$field->field}))
                          {
                              foreach($data->{$field->field} as $value) $fieldValue .= zget($field->options, $value) . ' ';
                          }
                          else
                          {
                              $fieldValue = zget($field->options, $data->{$field->field});
                          }
                      }

                      if($fieldValue !== '')
                      {
                          if($link) $fieldValue = baseHTML::a($link, $fieldValue);

                          echo ' ' . $lang->colon . ' ' . $fieldValue;
                      }

                      echo '</p>';
                  }
              }
              ?>
            </div>
          </div>

          <?php foreach($children as $child => $childName):?>
          <?php if(empty($childDatas[$child])) continue;?>
          <div class='panel panel-block'>
            <div class='panel-heading'><strong><?php echo $childName;?></strong></div>
            <div class='panel-body'>
              <table class='table table-hover table-fixed'>
                <tr>
                <?php foreach($childFields[$child] as $childField):?>
                <?php if(!$childField->show) continue;?>
                <?php $childWidth = ($childField->width && $childField->width != 'auto' ? $childField->width . 'px' : 'auto');?>
                <th style='width: <?php echo $childWidth;?>'><?php echo $childField->name;?></th>
                <?php endforeach;?>
                </tr>
                <?php foreach($childDatas[$child] as $childData):?>
                <tr>
                  <?php foreach($childFields[$child] as $childField):?>
                  <?php if(!$childField->show) continue;?>
                  <td>
                    <?php
                    if(strpos(',date,datetime,', ",$childField->control,") !== false)
                    {
                        echo formatTime($childData->{$childField->field});
                    }
                    else
                    {
                        echo zget($childField->options, $childData->{$childField->field});
                    }
                    ?>
                  </td>
                  <?php endforeach;?>
                </tr>
                <?php endforeach;?>
              </table>
            </div>
          </div>
          <?php endforeach;?>

    <?php $actions = $this->loadModel('action')->getList($flow->module, $data->id);?>
    <?php $defaultComment = true;?>
    <div class='cell'><?php include '../../common/view/action.html.php';?></div>
          <?php echo $this->flow->buildOperateMenu($flow, $data, $type = 'view');?>
        </div>
        <div class='side-col col-3'>
          <?php foreach($processBlocks as $blockKey => $block):?>
          <div class='panel panel-block'>
            <?php if(empty($block->tabs)):?>
            <?php if($block->name):?> <div class='panel-heading'><strong><?php echo $block->name;?></strong></div> <?php endif;?>
            <?php else:?>
            <div class='panel-heading with-nav-tabs'>
              <ul class='nav nav-tabs'>
                <?php $index = 1;?>
                <?php foreach($block->tabs as $tabKey => $tab):?>
                <?php $class = $index == 1 ? "class='active'" : '';?>
                <li <?php echo $class;?>><a data-toggle="tab" href="#<?php echo $blockKey . '_' . $tabKey;?>Tab"><?php echo $tab->name;?></a></li>
                <?php $index ++;?>
                <?php endforeach;?>
              </ul>
            </div>
            <?php endif;?>

            <?php if(!empty($block->tabs)):?>
            <div class='tab-content'>
              <?php $index = 1;?>
              <?php foreach($block->tabs as $tabKey => $tab):?>
              <?php $blockFields = $tab->fields;?>
              <?php $class = $index == 1 ? 'active' : '';?>
              <div id="<?php echo $blockKey . '_' . $tabKey;?>Tab" class='<?php echo $class;?> tab-pane panel-body no-padding-top'>
                <?php include 'block.html.php';?>
              </div>
              <?php $index ++;?>
              <?php endforeach;?>
            </div>
            <?php else:?>
            <div class='panel-body'>
              <?php if(!empty($block->fields)):?>
              <?php $blockFields = $block->fields;?>
              <?php include 'block.html.php';?>
              <?php endif;?>
            </div>
            <?php endif;?>

          </div>
          <?php endforeach;?>
        </div>
      </div>
    </div>
    <?php include 'linked.html.php';?>
    <?php if($canLink):?>
    <?php include 'linktype.html.php';?>
    <?php $active = $activeTab == 'common' ? 'active' : '';?>
    <div id='common' class='tab-pane <?php echo $active;?>'>
      <div class='actions'>
        <?php echo baseHTML::a('#linkTypeBox', $lang->workflowaction->default->actions['link'], "class='btn btn-primary' data-toggle='modal'");?>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php if(!empty($flow->js)) js::execute($flow->js);?>
<?php if(!empty($flowAction->js)) js::execute($flowAction->js);?>
<?php if($flowAction->open == 'modal'):?>
<?php include '../../common/view/footer.modal.html.php';?>
<?php else:?>
<?php include 'footer.html.php';?>
<?php endif;?>
