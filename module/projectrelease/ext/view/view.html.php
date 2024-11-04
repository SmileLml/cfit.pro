<?php
/**
 * The view file of release module's view method of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     release
 * @version     $Id: view.html.php 4386 2013-02-19 07:37:45Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<style>
    .table-fixed td{
        white-space: unset!important;
    }
</style>
<style class="dialog"></style>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/tablesorter.html.php';?>
<?php js::set('confirmUnlinkStory', $lang->release->confirmUnlinkStory)?>
<?php js::set('confirmUnlinkBug', $lang->release->confirmUnlinkBug)?>
<?php js::set('confirmRepush', $lang->release->confirmRepush)?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php $browseLink = $this->session->releaseList ? $this->session->releaseList : inlink('browse', "productID=$release->product");?>
    <?php common::printBack($browseLink, 'btn btn-primary');?>
    <div class='divider'></div>
    <div class='page-title'>
      <span class='label label-id'><?php echo $release->id;?></span>
      <span class='text' title='<?php echo $release->name;?>'><?php echo $release->name;?></span>
      <?php $flagIcon = $release->marker ? "<icon class='icon icon-flag red' title='{$lang->release->marker}'></icon> " : '';?>
      <?php echo $flagIcon;?>
      <?php if($release->deleted):?>
      <span class='label label-danger'><?php echo $lang->release->deleted;?></span>
      <?php endif; ?>
    </div>
  </div>
  <div class='btn-toolbar pull-right'>
    <?php
    $canBeChanged = common::canBeChanged('projectrelease', $release);

    if(!$release->deleted and $canBeChanged)
    {
        echo $this->buildOperateMenu($release, 'view');

        if($release->isSent == 0 and common::hasPriv('projectrelease', 'publish', $release))
        {
            echo html::a(inlink('publish', "releaseID=$release->id") . "?onlybody=yes", '<i class="icon-bullhorn' . '"></i> ' . $lang->release->publish, 'hiddenwin', "class='btn btn-link iframe'");
        }

        if(common::hasPriv('projectrelease', 'changeStatus', $release))
        {
            $changedStatus = $release->status == 'normal' ? 'terminate' : 'normal';
            echo html::a(inlink('changeStatus', "releaseID=$release->id&status=$changedStatus"), '<i class="icon-' . ($release->status == 'normal' ? 'pause' : 'play') . '"></i> ' . $lang->release->changeStatusList[$changedStatus], 'hiddenwin', "class='btn btn-link' title='{$lang->release->changeStatusList[$changedStatus]}'");
        }
        if(common::hasPriv('projectrelease', 'edit') && $release->syncStateTimes <= 0)   echo html::a(inlink('edit',   "releaseID=$release->id"), "<i class='icon-common-edit icon-edit'></i> " . $this->lang->edit, '', "class='btn btn-link' title='{$this->lang->edit}'");
        if(common::hasPriv('projectrelease', 'delete')) echo html::a(inlink('delete', "releaseID=$release->id"), "<i class='icon-common-delete icon-trash'></i> " . $this->lang->delete, '', "class='btn btn-link' title='{$this->lang->delete}' target='hiddenwin'");
    }
    ?>
  </div>
</div>
<div id='mainContent' class='main-content'>
  <div class='main-col'>
    <div class='main' style="padding-bottom: 30px">
      <div class='tabs' id='tabsNav' style="margin-top:25px">
        <?php $countStories = count($stories); $countBugs = count($bugs); $countLeftBugs = count($leftBugs);?>
        <ul class='nav nav-tabs'>
            <li <?php if($type == 'releaseInfo') echo "class='active'"?>><a href='#releaseInfo' data-toggle='tab'><?php echo html::icon($lang->icons['plan'], 'text-info') . ' ' . $lang->release->view;?></a></li>
            <li <?php if($type == 'story')   echo "class='active'"?>><a href='#stories' data-toggle='tab'><?php echo html::icon($lang->icons['story'], 'text-green') . ' ' . $lang->release->stories;?></a></li>
          <li <?php if($type == 'bug')     echo "class='active'"?>><a href='#bugs' data-toggle='tab'><?php echo html::icon($lang->icons['bug'], 'text-green') . ' ' . $lang->release->bugs;?></a></li>
          <li <?php if($type == 'leftBug') echo "class='active'"?>><a href='#leftBugs' data-toggle='tab'><?php echo html::icon($lang->icons['bug'], 'text-red') . ' ' . $lang->release->generatedBugs;?></a></li>
          <?php if($countStories or $countBugs or $countLeftBugs):?>
          <li class='pull-right'><div><?php common::printIcon('projectrelease', 'export', '', '', 'button', '', '', "export btn-sm");?></div></li>
          <?php endif;?>
        </ul>
        <div class='tab-content'>
          <div class='tab-pane <?php if($type == 'story') echo 'active'?>' id='stories'>
            <?php if(common::hasPriv('projectrelease', 'linkStory') and $canBeChanged):?>
            <div class='actions'><?php echo html::a("javascript:showLink({$release->id}, \"story\")", '<i class="icon-link"></i> ' . $lang->release->linkStory, '', "class='btn btn-primary'");?></div>
            <div class='linkBox cell hidden'></div>
            <?php endif;?>
            <form class='main-table table-story' method='post' id='linkedStoriesForm' data-ride="table">
              <table class='table has-sort-head' id='storyList'>
                <?php
                $canBatchUnlink = common::hasPriv('projectrelease', 'batchUnlinkStory');
                $canBatchClose  = common::hasPriv('story', 'batchClose');
                ?>
                <?php $vars = "releaseID={$release->id}&type=story&link=$link&param=$param&orderBy=%s";?>
                <thead>
                  <tr>
                    <th class='c-id text-left'>
                      <?php if(($canBatchUnlink or $canBatchClose) and $canBeChanged):?>
                      <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                        <label></label>
                      </div>
                      <?php endif;?>
                      <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
                    </th>
                    <th class='c-pri'>    <?php common::printOrderLink('pri',      $orderBy, $vars, $lang->priAB);?></th>
                    <th class="text-left"><?php common::printOrderLink('title',    $orderBy, $vars, $lang->story->title);?></th>
                    <th class='c-user'>   <?php common::printOrderLink('openedBy', $orderBy, $vars, $lang->openedByAB);?></th>
                    <th class='w-80px text-right'><?php common::printOrderLink('estimate', $orderBy, $vars, $lang->story->estimateAB);?></th>
                    <th class='w-90px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->statusAB);?></th>
                    <th class='w-100px'><?php common::printOrderLink('stage',  $orderBy, $vars, $lang->story->stageAB);?></th>
                    <th class='c-actions-1'><?php echo $lang->actions?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($stories as $storyID => $story):?>
                  <?php $storyLink = $this->createLink('story', 'view', "storyID=$story->id&version=0&param=$projectID", '', true);?>
                  <tr>
                    <td class='c-id text-left'>
                      <?php if(($canBatchUnlink or $canBatchClose) and $canBeChanged):?>
                      <div class="checkbox-primary">
                        <input type='checkbox' name='storyIdList[]'  value='<?php echo $story->id;?>'/>
                        <label></label>
                      </div>
                      <?php endif;?>
                      <?php echo sprintf('%03d', $story->id);?>
                    </td>
                    <td><span class='label-pri <?php echo 'label-pri-' . $story->pri;?>' title='<?php echo zget($lang->story->priList, $story->pri, $story->pri);?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri);?></span></td>
                    <td class='text-left nobr' title='<?php echo $story->title?>'>
                      <?php
                      if($story->parent > 0) echo "<span class='label'>{$lang->story->childrenAB}</span>";
                      echo html::a($storyLink,$story->title, '', "class='preview'");
                      ?>
                    </td>
                    <td><?php echo zget($users, $story->openedBy);?></td>
                    <td class='text-right' title="<?php echo $story->estimate . ' ' . $lang->hourCommon;?>"><?php echo $story->estimate . $config->hourUnit;?></td>
                    <td>
                      <span class='status-story status-<?php echo $story->status;?>'><?php echo $this->processStatus('story', $story);?></span>
                    </td>
                    <td><?php echo $lang->story->stageList[$story->stage];?></td>
                    <td class='c-actions'>
                      <?php
                      if(common::hasPriv('projectrelease', 'unlinkStory') and $canBeChanged)
                      {
                          $unlinkURL = $this->createLink('projectrelease', 'unlinkStory', "releaseID=$release->id&story=$story->id");
                          echo html::a("javascript:ajaxDelete(\"$unlinkURL\", \"storyList\", confirmUnlinkStory)", '<i class="icon-unlink"></i>', '', "class='btn' title='{$lang->release->unlinkStory}'");
                      }
                      ?>
                    </td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
              <div class='table-footer'>
                <?php if($countStories and ($canBatchUnlink or $canBatchClose) and $canBeChanged):?>
                <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
                <div class="table-actions btn-toolbar">
                  <?php
                  if(common::hasPriv('projectrelease', 'batchUnlinkStory'))
                  {
                      $unlinkURL = inlink('batchUnlinkStory', "release=$release->id");
                      echo html::a('###', $lang->release->batchUnlink, '', "onclick='setFormAction(\"$unlinkURL\", \"hiddenwin\", this)' class='btn'");
                  }

                  if(common::hasPriv('story', 'batchClose'))
                  {
                      $closeURL = $this->createLink('story', 'batchClose');
                      echo html::a("###", $lang->story->batchClose, '', "onclick='setFormAction(\"$closeURL\", \"\", this)' class='btn'");
                  }
                  ?>
                </div>
                <div class='table-statistic'><?php echo sprintf($lang->release->finishStories, $countStories);?></div>
                <?php endif;?>
                <?php
                $this->app->rawParams['type'] = 'story';
                $storyPager->show('right', 'pagerjs');
                $this->app->rawParams['type'] = $type;
                ?>
              </div>
            </form>
          </div>
          <div class='tab-pane <?php if($type == 'bug') echo 'active'?>' id='bugs'>
            <?php if(common::hasPriv('projectrelease', 'linkBug') and $canBeChanged):?>
            <div class='actions'><?php echo html::a("javascript:showLink({$release->id}, \"bug\")", '<i class="icon-bug"></i> ' . $lang->release->linkBug, '', "class='btn btn-primary'");?></div>
            <div class='linkBox cell hidden'></div>
            <?php endif;?>
            <form class='main-table table-bug' method='post' target='hiddenwin' action="<?php echo inLink('batchUnlinkBug', "releaseID=$release->id");?>" id='linkedBugsForm' data-ride="table">
              <table class='table has-sort-head' id='bugList'>
                <?php $canBatchUnlink = common::hasPriv('projectrelease', 'batchUnlinkBug');?>
                <?php $vars = "releaseID={$release->id}&type=bug&link=$link&param=$param&orderBy=%s";?>
                <thead>
                  <tr class='text-center'>
                    <th class='c-id text-left w-110px'>
                      <?php if($canBatchUnlink and $canBeChanged):?>
                      <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                        <label></label>
                      </div>
                      <?php endif;?>
                      <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
                    </th>
                    <th class='text-left'><?php common::printOrderLink('title',        $orderBy, $vars, $lang->bug->title);?></th>
                    <th class='w-100px'>  <?php common::printOrderLink('status',       $orderBy, $vars, $lang->bug->status);?></th>
                    <th class='c-user'>   <?php common::printOrderLink('openedBy',     $orderBy, $vars, $lang->openedByAB);?></th>
                    <th class='w-date'>   <?php common::printOrderLink('openedDate',   $orderBy, $vars, $lang->bug->openedDateAB);?></th>
                    <th class='c-user'>   <?php common::printOrderLink('resolvedBy',   $orderBy, $vars, $lang->bug->resolvedByAB);?></th>
                    <th class='w-100px'>  <?php common::printOrderLink('resolvedDate', $orderBy, $vars, $lang->bug->resolvedDateAB);?></th>
                    <th class='w-50px'>   <?php echo $lang->actions;?></th>
                  </tr>
                </thead>
                <tbody class='text-center'>
                  <?php foreach($bugs as $bug):?>
                  <?php $bugLink = $this->createLink('bug', 'view', "bugID=$bug->id", '', true);?>
                  <tr>
                    <td class='c-id text-left'>
                      <?php if($canBatchUnlink and $canBeChanged):?>
                      <div class="checkbox-primary">
                        <input type='checkbox' name='unlinkBugs[]'  value='<?php echo $bug->id;?>'/>
                        <label></label>
                      </div>
                      <?php endif;?>
                      <?php echo sprintf('%03d', $bug->id);?>
                    </td>
                    <td class='text-left nobr' title='<?php echo $bug->title?>'><?php echo html::a($bugLink, $bug->title, '', "class='preview'");?></td>
                    <td>
                      <span class='status-bug status-<?php echo $bug->status?>'><?php echo $this->processStatus('bug', $bug);?></span>
                    </td>
                    <td><?php echo zget($users, $bug->openedBy);?></td>
                    <td><?php echo substr($bug->openedDate, 5, 11)?></td>
                    <td><?php echo zget($users, $bug->resolvedBy);?></td>
                    <td><?php echo substr($bug->resolvedDate, 5, 11)?></td>
                    <td class='c-actions'>
                      <?php
                      if(common::hasPriv('projectrelease', 'unlinkBug') and $canBeChanged)
                      {
                          $unlinkURL = $this->createLink('projectrelease', 'unlinkBug', "releaseID=$release->id&bug=$bug->id");
                          echo html::a("javascript:ajaxDelete(\"$unlinkURL\", \"bugList\", confirmUnlinkBug)", '<i class="icon-unlink"></i>', '', "class='btn' title='{$lang->release->unlinkBug}'");
                      }
                      ?>
                    </td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
              <div class='table-footer'>
                <?php if($countBugs and $canBatchUnlink and $canBeChanged):?>
                <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
                <div class="table-actions btn-toolbar">
                  <?php echo html::submitButton($lang->release->batchUnlink, '', 'btn');?>
                </div>
                <div class='table-statistic'><?php echo sprintf($lang->release->resolvedBugs, $countBugs);?></div>
                <?php endif;?>
                <?php
                $this->app->rawParams['type'] = 'bug';
                $bugPager->show('right', 'pagerjs');
                $this->app->rawParams['type'] = $type;
                ?>
              </div>
            </form>
          </div>
          <div class='tab-pane <?php if($type == 'leftBug') echo 'active'?>' id='leftBugs'>
            <?php if(common::hasPriv('projectrelease', 'linkBug') and $canBeChanged):?>
            <div class='actions'><?php echo html::a("javascript:showLink({$release->id}, \"leftBug\")", '<i class="icon-bug"></i> ' . $lang->release->linkBug, '', "class='btn btn-primary'");?></div>
            <div class='linkBox cell hidden'></div>
            <?php endif;?>
            <form class='main-table table-bug' method='post' target='hiddenwin' action="<?php echo inlink('batchUnlinkBug', "releaseID=$release->id&type=leftBug");?>" id='linkedBugsForm' data-ride="table">
              <table class='table has-sort-head' id='leftBugList'>
                <?php $canBatchUnlink = common::hasPriv('projectrelease', 'batchUnlinkBug');?>
                <?php $vars = "releaseID={$release->id}&type=leftBug&link=$link&param=$param&orderBy=%s";?>
                <thead>
                  <tr class='text-center'>
                    <th class='c-id text-left'>
                      <?php if($canBatchUnlink and $canBeChanged):?>
                      <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                        <label></label>
                      </div>
                      <?php endif;?>
                      <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
                    </th>
                    <th class='w-80px'>    <?php common::printOrderLink('severity',   $orderBy, $vars, $lang->bug->severityAB);?></th>
                    <th class='text-left'> <?php common::printOrderLink('title',      $orderBy, $vars, $lang->bug->title);?></th>
                    <th class='w-100px'>   <?php common::printOrderLink('status',     $orderBy, $vars, $lang->bug->status);?></th>
                    <th class='c-user'>    <?php common::printOrderLink('openedBy',   $orderBy, $vars, $lang->openedByAB);?></th>
                    <th class='w-150px'>   <?php common::printOrderLink('openedDate', $orderBy, $vars, $lang->bug->openedDateAB);?></th>
                    <th class='w-50px'>    <?php echo $lang->actions;?></th>
                  </tr>
                </thead>
                <?php
                $hasCustomSeverity = false;
                foreach($lang->bug->severityList as $severityKey => $severityValue)
                {
                    if(!empty($severityKey) and (string)$severityKey != (string)$severityValue)
                    {
                        $hasCustomSeverity = true;
                        break;
                    }
                }
                ?>
                <tbody class='text-center'>
                  <?php foreach($leftBugs as $bug):?>
                  <?php $bugLink = $this->createLink('bug', 'view', "bugID=$bug->id", '', true);?>
                  <tr>
                    <td class='c-id text-left'>
                      <?php if($canBatchUnlink and $canBeChanged):?>
                      <div class="checkbox-primary">
                        <input type='checkbox' name='unlinkBugs[]'  value='<?php echo $bug->id;?>'/>
                        <label></label>
                      </div>
                      <?php endif;?>
                      <?php echo sprintf('%03d', $bug->id);?>
                    </td>
                    <td class='c-severity'>
                      <?php if($hasCustomSeverity):?>
                      <span class='<?php echo 'label-severity-custom';?>' title='<?php echo zget($lang->bug->severityList, $bug->severity);?>' data-severity='<?php echo $bug->severity;?>'><?php echo zget($lang->bug->severityList, $bug->severity, $bug->severity);?></span>
                      <?php else:?>
                      <span class='label-severity' data-severity='<?php echo $bug->severity;?>' title='<?php echo zget($lang->bug->severityList, $bug->severity, $bug->severity);?>'></span>
                      <?php endif;?>
                    </td>
                    <td class='text-left nobr' title='<?php echo $bug->title?>'><?php echo html::a($bugLink, $bug->title, '', "class='preview'");?></td>
                    <td><span class='status-<?php echo $bug->status?>'> <?php echo $this->processStatus('bug', $bug);?></span></td>
                    <td><?php echo zget($users, $bug->openedBy);?></td>
                    <td><?php echo $bug->openedDate?></td>
                    <td class='c-actions'>
                      <?php
                      if(common::hasPriv('projectrelease', 'unlinkBug') and $canBeChanged)
                      {
                          $unlinkURL = $this->createLink('projectrelease', 'unlinkBug', "releaseID=$release->id&bug=$bug->id&type=leftBug");
                          echo html::a("javascript:ajaxDelete(\"$unlinkURL\", \"leftBugList\", confirmUnlinkBug)", '<i class="icon-unlink"></i>', '', "class='btn' title='{$lang->release->unlinkBug}'");
                      }
                      ?>
                    </td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
              <div class='table-footer'>
                <?php if($countLeftBugs and $canBatchUnlink and $canBeChanged):?>
                <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
                <div class="table-actions btn-toolbar">
                  <?php echo html::submitButton($lang->release->batchUnlink, '', 'btn');?>
                </div>
                <div class='table-statistic'><?php echo sprintf($lang->release->createdBugs, $countLeftBugs);?></div>
                <?php endif;?>
                <?php
                $this->app->rawParams['type'] = 'leftBug';
                $leftBugPager->show('right', 'pagerjs');
                $this->app->rawParams['type'] = $type;
                ?>
              </div>
            </form>
          </div>

          <div class='tab-pane <?php if($type == 'releaseInfo') echo 'active'?>' id='releaseInfo'>
            <div class='cell'>
              <div class='detail'>
                <div class='detail-title'><?php echo $lang->release->basicInfo?></div>
                <div class='detail-content'>
                  <table class='table table-data'>
                    <tr>
                          <th class='w-100px'><?php echo $lang->projectrelease->app;?></th>
                          <td><?php echo zget($apps,$release->app,'');?></td>
                    </tr>
                    <tr>
                      <th class='w-100px'><?php echo $lang->projectrelease->product;?></th>
                      <td><?php echo $release->product == '99999' ? '无' :  $release->productName;?></td>
                    </tr>
                    <?php if($release->productType != 'normal' && $release->productType):?>
                      <tr>
                        <th><?php echo $lang->product->branch;?></th>
                        <td><?php echo $branchName;?></td>
                      </tr>
                    <?php endif;?>
                    <tr>
                      <th class='w-90px'><?php echo $lang->projectrelease->productCode;?></th>
                      <td><?php echo $release->productCodeInfo;?></td>
                    </tr>
                    <tr>
                       <th class='w-90px'><?php echo $lang->projectrelease->productversion;?></th>
                       <td><?php echo zget($plans,$release->productVersion,'');?></td>
                    </tr>
                    <tr>
                      <th><?php echo $lang->release->name;?></th>
                      <td><?php echo $release->name;?></td>
                    </tr>
                    <tr>
                      <th><?php echo $lang->projectrelease->buildname;?></th>
                      <td title='<?php echo $release->buildName?>'>
                          <?php echo ($release->execution) ? html::a($this->createLink('build', 'view', "buildID=$release->buildID"), $release->buildName, '_self','data-app="project"') : $release->buildName;?>
                      </td>
                    </tr>
                      <!--
                    <tr>
                      <th><?php echo $lang->release->status;?></th>
                      <td><?php echo $this->processStatus('release', $release);?></td>
                    </tr>
                    -->
                    <tr>
                      <th><?php echo $lang->release->date;?></th>
                      <td><?php echo $release->date;?></td>
                    </tr>
                    <tr>
                      <th><?php echo $lang->release->path;?></th>
                      <td><?php echo $release->path;?></td>
                    </tr>
                      <tr>
                          <th><?php echo $lang->release->pushStatusQz;?></th>
                          <td><?php echo '<div style="width: 120px!important; float: left; vertical-align: center; line-height: 20px">'.zget($lang->release->pushStatusList,$release->pushStatusQz) .'</div>';
                              $rePushURL =$this->createLink('release', 'repush', "releaseID=$release->id&type=Qz");
                              if(common::hasPriv('projectrelease', 'repush')) echo html::a("javascript:ajaxPush(\"$rePushURL\");", '<i class="icon-export"></i>', '', "class='' title='重新推送'");
                              ?></td>
                      </tr>
<!--                      <tr>-->
<!--                          <th>--><?php //echo $lang->release->pushStatusJx;?><!--</th>-->
<!--                          <td>--><?php //echo '<div style="width: 120px!important; float: left; vertical-align: center; line-height: 20px">'.zget($lang->release->pushStatusList,$release->pushStatusJx) .'</div>';
//                              $rePushURL =$this->createLink('release', 'repush', "releaseID=$release->id&type=Jx");
//                              echo html::a("javascript:ajaxDelete(\"$rePushURL\", \"#\", confirmRepush);location.reload();", '<i class="icon-export"></i>', '', "class='' title='重新推送'");
//                              ?><!--</td>-->
<!--                      </tr>-->
                    <tr>
                      <th><?php echo $lang->release->mailto;?></th>
                     <td><?php $to = array_unique(explode(',',$release->mailto));
                       if($to){
                                foreach ($to as $item) {
                                 echo zget($users,$item,'').'  ';
                              }
                         }
                      ?>
                      </td>
                    </tr>
                    <?php $this->printExtendFields($release, 'table', 'inForm=0');?>
                    <tr>
                      <th><?php echo $lang->release->desc;?></th>
                      <td><?php echo $release->desc;?></td>
                    </tr>

                      <tr>
                          <th><?php echo $lang->projectrelease->statusDesc;?></th>
                          <td><?php echo zget($lang->projectrelease->statusLabelList, $release->status);?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->projectrelease->dealUser;?></th>
                          <td>
                              <?php
                              $dealUser = $release->dealUser;
                              if($dealUser):
                                  $dealUserArray = explode(',', $dealUser);
                                  foreach ($dealUserArray as $account):
                                      echo zget($users, $account) . ' ';
                                  endforeach;
                              endif;
                              ?>
                          </td>
                      </tr>
                  </table>
                </div>
              </div>
                <!--基线信息-->
              <div class='detail'>
                  <div class='detail-title'><?php echo $lang->projectrelease->baseLineInfo;?></div>
                  <div class='detail-content article-content'>
                      <table class="table ops  table-fixed">
                          <thead>
                          <tr>
                              <th class='w-160px'><?php echo $lang->projectrelease->baseLineTime;?></th>
                              <th class='w-100px'><?php echo $lang->projectrelease->baseLineUser;?></th>
                              <th class='w-120px'><?php echo $lang->projectrelease->baseLineCondition;?></th>
                              <th><?php echo $lang->projectrelease->baseLinePath;?></th>
                              <th class='w-120px'><?php echo $lang->projectrelease->cmConfirm;?></th>
                              <th class='w-120px'><?php echo $lang->projectrelease->cmConfirmUser;?></th>
                              <th class='w-160px'><?php echo $lang->projectrelease->cmConfirmTime;?></th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php if(empty($baseLineList)):?>
                              <tr>
                                  <td colspan="7" style="text-align: center;"><?php echo $lang->noData;?></td>
                              </tr>
                          <?php else:?>
                              <?php
                                    foreach ($baseLineList as $val):
                                        $baseLinePath = $val->baseLinePath;
                                        $baseLinePathArray = explode(',', $baseLinePath);
                                        $count = count($baseLinePathArray);
                                        $tempCount = $count < 1 ? 1:$count;
                                        $firstBaseLinePath = isset($baseLinePathArray[0])? $baseLinePathArray[0]: '无';
                                  ?>
                                  <tr>
                                      <td rowspan="<?php echo $tempCount;?>"><?php echo $val->baseLineTime; ?></td>
                                      <td rowspan="<?php echo $tempCount;?>"><?php echo zget($users, $val->baseLineUser) ;?></td>
                                      <td rowspan="<?php echo $tempCount;?>"><?php echo zget($lang->projectrelease->baseLineConditionList, $val->baseLineCondition);?></td>
                                      <td><?php echo $firstBaseLinePath; ?></td>
                                      <td rowspan="<?php echo $tempCount;?>"><?php echo $val->cmConfirm == ''? '未确认': zget($lang->projectrelease->dealResultList, $val->cmConfirm); ?></td>
                                      <td rowspan="<?php echo $tempCount;?>"><?php echo zget($users, $val->cmConfirmUser) ;?></td>
                                      <td rowspan="<?php echo $tempCount;?>"><?php echo $val->cmConfirmTime == '0000-00-00 00:00:00'? '': $val->cmConfirmTime;?></td>
                                  </tr>

                                      <?php
                                        if($tempCount > 1):
                                            unset($baseLinePathArray[0]);
                                            foreach ($baseLinePathArray as $currentBaseLinePath):
                                      ?>
                                        <tr>
                                            <td><?php echo $currentBaseLinePath; ?></td>
                                        </tr>
                                            
                                      <?php
                                            endforeach;
                                        endif;
                                      ?>

                              <?php endforeach;?>
                          <?php endif;?>

                          </tbody>
                      </table>
                  </div>
              </div>

              <div class='detail'>
                <div class='detail-title'><?php echo $lang->files?></div>
                <div class='detail-content article-content'>
                  <?php
                  if($release->files)
                  {
                      echo $this->fetch('file', 'printFiles', array('files' => $release->files, 'fieldset' => 'false'));
                  }else{
                      echo  "<div class='text-center'> $lang->noData</div>";
                  }
                 /* elseif($release->filePath)
                  {
                      echo $lang->release->filePath . html::a($release->filePath, $release->filePath, '_blank');
                  }
                  elseif($release->scmPath)
                  {
                      echo $lang->release->scmPath . html::a($release->scmPath, $release->scmPath, '_blank');
                  }*/
                  ?>
                </div>
              </div>
              <?php include '../../../common/view/action.html.php';?>
            </div>
          </div>

        </div>
      </div>
    </div>

      <div class='main-actions' style="position: fixed;bottom: 10px;">
          <div class="btn-toolbar">
              <?php common::printBack(inlink('browse', "projectID=$projectID"));?>
              <div class='divider'></div>
              <?php
              common::printIcon('projectrelease', 'deal', "release=$release->id&version=$release->version&status=$release->status", $release, 'button', 'time', '', 'iframe', true, '', $lang->projectrelease->deal);
              ?>
          </div>
      </div>

  </div>
</div>
<style>
.tabs .tab-content .tab-pane .action{position: absolute; right: <?php echo ($countStories or $countBugs or $countLeftBugs) ? '100px' : '-1px'?>; top: 0px;}
</style>
<?php js::set('param', helper::safe64Decode($param))?>
<?php js::set('link', $link)?>
<?php js::set('releaseID', $release->id)?>
<?php js::set('type', $type)?>

<script>
    var scroll_height = document.body.scrollHeight;
    var window_height = window.innerHeight;
    if (scroll_height > window_height){
        var _top = 120;
        if (window_height <= 700){
            _top = 60
        }
        $(".dialog").append(".modal-dialog{top:"+_top+'px'+"!important}");
    }
</script>
<?php include '../../../common/view/footer.html.php';?>
