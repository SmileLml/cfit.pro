<?php
/**
 * The browse view file of release module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2020 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yuchun Li <liyuchun@cnezsoft.com>
 * @package     release
 * @version     $Id: browse.html.php 4129 2020-11-25 11:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/tablesorter.html.php';?>
<?php js::set('confirmDelete', $lang->release->confirmDelete)?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    echo html::a(inlink('browse', "projectID=$projectID&executionID=$executionID&type=all"), "<span class='text'>{$lang->release->all}</span>" . ($type == 'all' ? ' <span class="label label-light label-badge">' . count($releases) . '</span>' : ''), '', "id='allTab' class='btn btn-link" . ('all' == $type ? ' btn-active-text' : '') . "' data-app='$from'");
    foreach($lang->projectrelease->statusLabelList as $key => $val){
        echo html::a(inlink('browse', "projectID=$projectID&executionID=$executionID&type={$key}"), "<span class='text'>{$val}</span>" . ($type == $key ? ' <span class="label label-light label-badge">' . count($releases) . '</span>' : ''), '', "id='normalTab' class='btn btn-link" . ($key == $type ? ' btn-active-text' : '') . "' data-app='$from'");
    }
    ?>
  </div>
  <div class="btn-toolbar pull-right">
    <?php common::printLink('projectrelease', 'create', "projectID=$projectID", "<i class='icon icon-plus'></i> {$lang->release->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class='main-table'>
  <?php if(empty($releases)):?>
  <div class="table-empty-tip">
    <p>
      <span class="text-muted"><?php echo $lang->release->noRelease;?></span>
      <?php if(common::hasPriv('projectrelease', 'create')):?>
      <?php echo html::a($this->createLink('projectrelease', 'create', "projectID=$projectID"), "<i class='icon icon-plus'></i> " . $lang->release->create, '', "class='btn btn-info'");?>
      <?php endif;?>
    </p>
  </div>
  <?php else:?>
  <table class="table" id='releaseList'>
    <thead>
      <tr>
        <th class='w-id'><?php echo $lang->release->id;?></th>
        <th><?php echo $lang->release->name;?></th>
        <th class='w-220px'><?php echo $lang->projectrelease->buildname;?></th>
        <th class='w-product'><?php echo $lang->projectrelease->product;?></th>
        <th class='w-140px'><?php echo $lang->projectrelease->productCode;?></th>
        <th class='c-date text-center w-100px'><?php echo $lang->release->date;?></th>
        <th class='text-center w-90px'><?php echo $lang->release->status;?></th>
        <th class='w-120px'><?php echo $lang->projectrelease->dealUser;?></th>
        <?php
        $extendFields = $this->projectrelease->getFlowExtendFields();
        foreach($extendFields as $extendField) echo "<th>{$extendField->name}</th>";
        ?>
        <th class='c-actions-6 text-center w-180px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($releases as $release):?>
      <?php $canBeChanged = common::canBeChanged('projectrelease', $release);?>
      <tr>
        <td><?php echo html::a(inlink('view', "releaseID=$release->id"), sprintf('%03d', $release->id));?></td>
          <td title='<?php echo $release->name?>'>
          <?php
          $flagIcon = $release->marker ? "<icon class='icon icon-flag red' title='{$lang->release->marker}'></icon> " : '';
          echo html::a(inlink('view', "release=$release->id"), $release->name, '', "data-app='$from'") . $flagIcon;
          ?>
        </td>
        <td title='<?php echo $release->buildName?>'><?php echo empty($release->execution) ? $release->buildName : html::a($this->createLink('build', 'view', "buildID=$release->buildID"), $release->buildName);?></td>
        <td title='<?php echo $release->product == '99999' ? '无' :$release->productName?>'><?php echo $release->product == '99999' ? '无' : $release->productName?></td>
        <td title='<?php echo $release->productCode?>'><?php echo $release->productCodeInfo?></td>
        <td class='text-center'><?php echo $release->date;?></td>
        <?php $statusDesc = zget($lang->projectrelease->statusLabelList, $release->status);?>
        <td class='c-status text-center' title='<?php echo $statusDesc;?>'>
          <span class="status-release status-<?php echo $release->status?>"><?php echo $statusDesc;?></span>
        </td>
         <td title='<?php echo $release->dealUserStr; ?>'><?php  echo $release->dealUserStr;?></td>
        <?php foreach($extendFields as $extendField) echo "<td>" . $this->loadModel('flow')->getFieldValue($extendField, $release) . "</td>";?>
        <td class='c-actions'>
          <?php

          if($canBeChanged)
          {
              common::hasPriv('projectrelease', 'deal') ? common::printIcon('projectrelease', 'deal', "release=$release->id&version=$release->version&status=$release->status", $release, 'list', 'time', '', 'iframe', true,"data-width='1200px'", $lang->projectrelease->deal): '';
              if(common::hasPriv('projectrelease', 'linkStory')) echo html::a(inlink('view', "releaseID=$release->id&type=story&link=true"), '<i class="icon-link"></i> ', '', "class='btn' title='{$lang->release->linkStory}'");
              if(common::hasPriv('projectrelease', 'linkBug')) echo html::a(inlink('view', "releaseID=$release->id&type=bug&link=true"), '<i class="icon-bug"></i> ', '', "class='btn' title='{$lang->release->linkBug}'");
              if(common::hasPriv('projectrelease', 'changeStatus', $release))
              {
                  $changedStatus = $release->status == 'terminate' ? 'normal' : 'terminate';
                  echo html::a($this->createLink('projectrelease', 'changeStatus', "releaseID=$release->id&status=$changedStatus"), '<i class="icon-' . ($release->status == 'terminate' ? 'play' : 'pause') . '"></i> ', 'hiddenwin', "class='btn' title='{$lang->release->changeStatusList[$changedStatus]}'");
              }
              common::printIcon('projectrelease', 'edit',   "release=$release->id", $release, 'list');
              if(common::hasPriv('projectrelease', 'delete', $release))
              {
                  $deleteURL = $this->createLink('projectrelease', 'delete', "releaseID=$release->id&confirm=yes");
                  echo html::a("javascript:ajaxDelete(\"$deleteURL\", \"releaseList\", confirmDelete)", '<i class="icon-trash"></i>', '', "class='btn' title='{$lang->release->delete}'");
              }
          }
          ?>
        </td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
  <?php endif;?>
</div>
<?php include '../../../common/view/footer.html.php';?>
