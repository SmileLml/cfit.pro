<?php
/**
 * The view file of build module's view method of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     build
 * @version     $Id: view.html.php 4386 2013-02-19 07:37:45Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/tablesorter.html.php';?>
<?php js::set('confirmUnlinkStory', $lang->build->confirmUnlinkStory)?>
<?php js::set('confirmUnlinkBug', $lang->build->confirmUnlinkBug)?>
<?php js::set('flow', $config->global->flow)?>
<?php if(isonlybody()):?>
<style>
#stories .action {display: none;}
#bugs .action {display: none;}
tbody tr td:first-child input {display: none;}
</style>
<?php endif;?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php $browseLink = $this->session->buildList ? $this->session->buildList : $this->createLink('execution', 'build', "executionID=$build->execution");?>
    <?php common::printBack($browseLink, 'btn btn-secondary');?>
    <div class='divider'></div>
    <div class='page-title'>
      <span class='text' title='<?php /*echo $build->name;*/?>'>
      <?php echo html::a('javascript:void(0)', "<span class='label label-id'>{$build->id}</span> <b style='white-space:normal'>" . $build->name . "</b> <span class='caret'></span>", '', "data-toggle='dropdown' class='btn btn-link btn-active-text'");?>
      <?php
      echo "<ul class='dropdown-menu' style='width: 650px;text-overflow: ellipsis;overflow: hidden !important;white-space: nowrap;'>";
      foreach($buildPairs as $id => $name)
      {
          echo '<li' . ($id == $build->id ? " class='active'" : '') . "  title=".$name. '>';
          echo html::a($this->createLink('build', 'view', "buildID=$id"), $name);
          echo '</li>';
      }
      echo '</ul>';
      ?>
      </span>
      <?php if($build->deleted):?>
      <span class='label label-danger'><?php echo $lang->build->deleted;?></span>
      <?php endif; ?>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class='btn-toolbar pull-right'>
    <?php
    if(!$build->deleted and $canBeChanged)
    {
       /* echo $this->buildOperateMenu($build, 'view');

        if(common::hasPriv('build', 'edit'))   echo html::a($this->createLink('build', 'edit',   "buildID=$build->id&productID=$build->product&projectID=$build->project"), "<i class='icon-common-edit icon-edit'></i> " . $this->lang->edit, '', "class='btn btn-link' title='{$this->lang->edit}' data-app='{$app->openApp}'");
        if(common::hasPriv('build', 'delete')) echo html::a($this->createLink('build', 'delete', "buildID=$build->id"), "<i class='icon-common-delete icon-trash'></i> " . $this->lang->delete, '', "class='btn btn-link' title='{$this->lang->delete}' target='hiddenwin' data-app='{$app->openApp}'");
*/    }
    ?>
  </div>
  <?php endif;?>
</div>
<div id='mainContent' class='main-row'>
  <div class='tabs' id='tabsNav'>
  <?php $countStories = count($stories); $countBugs = count($bugs); $countGeneratedBugs = count($generatedBugs);?>
    <ul class='nav nav-tabs'>
        <li <?php if($type == 'buildInfo')    echo "class='active'"?>><a href='#buildInfo' data-toggle='tab'><?php echo html::icon($lang->icons['plan'], 'text-info') . ' ' . $lang->build->view;?></a></li>
        <li <?php if($type == 'story')        echo "class='active'"?>><a href='#stories' data-toggle='tab'><?php echo html::icon($lang->icons['story'], 'text-primary') . ' ' . $lang->build->stories;?></a></li>
      <li <?php if($type == 'bug')          echo "class='active'"?>><a href='#bugs' data-toggle='tab'><?php echo html::icon($lang->icons['bug'], 'text-green') . ' ' . $lang->build->bugs;?></a></li>
      <li <?php if($type == 'generatedBug') echo "class='active'"?>><a href='#generatedBugs' data-toggle='tab'><?php echo html::icon($lang->icons['bug'], 'text-red') . ' ' . $lang->build->generatedBugs;?></a></li>
    </ul>
    <div class='tab-content'>
      <div class='tab-pane <?php if($type == 'story') echo 'active'?>' id='stories'>
        <?php if($canBeChanged and common::hasPriv('build', 'linkStory')):?>
        <div class='actions'><?php echo html::a("javascript:showLink($build->id, \"story\")", '<i class="icon-link"></i> ' . $lang->build->linkStory, '', "class='btn btn-primary'");?></div>
        <div class='linkBox cell hidden'></div>
        <?php endif;?>
        <form class='main-table table-story' data-ride='table' method='post' target='hiddenwin' action='<?php echo inlink('batchUnlinkStory', "buildID={$build->id}")?>' id='linkedStoriesForm'>
          <table class='table has-sort-head' id='storyList'>
            <?php $canBatchUnlink = ($canBeChanged and common::hasPriv('build', 'batchUnlinkStory'));?>
            <?php $vars = "buildID={$build->id}&type=story&link=$link&param=$param&orderBy=%s";?>
            <thead>
              <tr class='text-center'>
                <th class='c-id text-left'>
                  <?php if($canBatchUnlink):?>
                  <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                    <label></label>
                  </div>
                  <?php endif;?>
                  <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
                </th>
                <th class='w-70px'><?php common::printOrderLink('pri',      $orderBy, $vars, $lang->priAB);?></th>
                <th class='text-left'><?php common::printOrderLink('title',    $orderBy, $vars, $lang->story->title);?></th>
                <th class='c-user'><?php common::printOrderLink('openedBy', $orderBy, $vars, $lang->openedByAB);?></th>
                <th class='w-70px text-right'><?php common::printOrderLink('estimate', $orderBy, $vars, $lang->story->estimateAB);?></th>
                <th class='w-70px'><?php common::printOrderLink('status',   $orderBy, $vars, $lang->statusAB);?></th>
                <th class='w-100px'><?php common::printOrderLink('stage',    $orderBy, $vars, $lang->story->stageAB);?></th>
                <th class='c-actions-1'><?php echo $lang->actions?></th>
              </tr>
            </thead>
            <tbody class='text-center'>
              <?php $objectID = $this->app->openApp == 'execution' ? $build->execution : $build->project;?>
              <?php foreach($stories as $storyID => $story):?>
              <?php $storyLink = $this->createLink('story', 'view', "storyID=$story->id&version=0&param=$objectID", '', true);?>
              <tr>
                <td class='c-id text-left'>
                  <?php if($canBatchUnlink):?>
                  <?php echo html::checkbox('unlinkStories', array($story->id => sprintf('%03d', $story->id)));?>
                  <?php else:?>
                  <?php printf('%03d', $story->id);?>
                  <?php endif;?>
                </td>
                <td><span class='label-pri label-pri-<?php echo $story->pri;?>' title='<?php echo zget($lang->story->priList, $story->pri, $story->pri);?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri);?></span></td>
                <td class='text-left nobr' title='<?php echo $story->title?>'>
                  <?php
                  if($story->parent > 0) echo "<span class='label'>{$lang->story->childrenAB}</span>";
                  echo html::a($storyLink,$story->title, '', isonlybody() ? "data-width='1000'" : "class='iframe' data-width='1000'");
                  ?>
                </td>
                <td><?php echo zget($users, $story->openedBy);?></td>
                <td class='text-right' title="<?php echo $story->estimate . ' ' . $lang->hourCommon;?>"><?php echo $story->estimate . $config->hourUnit;?></td>
                <td>
                  <span class='status-story status-<?php echo $story->status;?>'>
                    <?php echo $this->processStatus('story', $story);?>
                  </span>
                </td>
                <td><?php echo $lang->story->stageList[$story->stage];?></td>
                <td class='c-actions'>
                  <?php
                  if($canBeChanged and common::hasPriv('build', 'unlinkStory'))
                  {
                      $unlinkURL = inlink('unlinkStory', "buildID=$build->id&story=$story->id");
                      echo html::a("###", '<i class="icon-unlink"></i>', '', "onclick='ajaxDelete(\"$unlinkURL\", \"storyList\", confirmUnlinkStory)' class='btn' title='{$lang->build->unlinkStory}'");
                  }
                  ?>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='table-footer'>
            <?php if($countStories):?>
            <?php if($canBatchUnlink):?>
            <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
            <div class="table-actions btn-toolbar">
              <?php echo html::submitButton($lang->build->batchUnlink, '', 'btn');?>
            </div>
            <?php endif;?>
            <div class='table-statistic'><?php echo sprintf($lang->build->finishStories, $countStories);?></div>
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
        <?php if($canBeChanged and common::hasPriv('build', 'linkBug')):?>
        <div class='actions'><?php echo html::a("javascript:showLink($build->id, \"bug\")", '<i class="icon-bug"></i> ' . $lang->build->linkBug, '', "class='btn btn-primary'");?></div>
        <div class='linkBox cell hidden'></div>
        <?php endif;?>
        <form class='main-table table-bug' data-ride='table' method='post' target='hiddenwin' action="<?php echo inLink('batchUnlinkBug', "build=$build->id");?>" id='linkedBugsForm'>
          <table class='table has-sort-head' id='bugList'>
            <?php $canBatchUnlink = $canBeChanged and common::hasPriv('build', 'batchUnlinkBug');?>
            <?php $vars = "buildID={$build->id}&type=bug&link=$link&param=$param&orderBy=%s";?>
            <thead>
              <tr class='text-center'>
                <th class='c-id text-left'>
                  <?php if($canBatchUnlink):?>
                  <div class="checkbox-primary check-all" title="<?php echo $lang->selectAll?>">
                    <label></label>
                  </div>
                  <?php endif;?>
                  <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
                </th>
                <th class='text-left'><?php common::printOrderLink('title',        $orderBy, $vars, $lang->bug->title);?></th>
                <th class='w-100px'>  <?php common::printOrderLink('status',       $orderBy, $vars, $lang->bug->status);?></th>
                <th class='c-user'>   <?php common::printOrderLink('openedBy',     $orderBy, $vars, $lang->openedByAB);?></th>
                <th class='c-date'>   <?php common::printOrderLink('openedDate',   $orderBy, $vars, $lang->bug->openedDateAB);?></th>
                <th class='c-user'>   <?php common::printOrderLink('resolvedBy',   $orderBy, $vars, $lang->bug->resolvedByAB);?></th>
                <th class='w-100px'>  <?php common::printOrderLink('resolvedDate', $orderBy, $vars, $lang->bug->resolvedDateAB);?></th>
                <th class='w-60px'>   <?php echo $lang->actions?></th>
              </tr>
            </thead>
            <tbody class='text-center'>
              <?php foreach($bugs as $bug):?>
              <?php $bugLink = $this->createLink('bug', 'view', "bugID=$bug->id", '', true);?>
              <tr>
                <td class='c-id text-left'>
                  <?php if($canBatchUnlink):?>
                  <?php echo html::checkbox('unlinkBugs', array($bug->id => sprintf('%03d', $bug->id)));?>
                  <?php else:?>
                  <?php printf('%03d', $bug->id);?>
                  <?php endif;?>
                <td class='text-left nobr' title='<?php echo $bug->title?>'>
                    <?php echo html::a($bugLink, $bug->title, '', isonlybody() ? "data-width='1000'" : "class='iframe' data-width='1000'");?>
                </td>
                <td>
                  <span class='status-bug status-<?php echo $bug->status?>'>
                    <?php echo $this->processStatus('bug', $bug);?>
                  </span>
                </td>
                <td><?php echo zget($users, $bug->openedBy);?></td>
                <td><?php echo substr($bug->openedDate, 5, 11)?></td>
                <td><?php echo zget($users, $bug->resolvedBy);?></td>
                <td><?php echo substr($bug->resolvedDate, 5, 11)?></td>
                <td class='c-actions'>
                  <?php
                  if($canBeChanged and common::hasPriv('build', 'unlinkBug'))
                  {
                      $unlinkURL = inlink('unlinkBug', "buildID=$build->id&bug=$bug->id");
                      echo html::a("###", '<i class="icon-unlink"></i>', '', "onclick='ajaxDelete(\"$unlinkURL\", \"bugList\", confirmUnlinkBug)' class='btn' title='{$lang->build->unlinkBug}'");
                  }
                  ?>
                </td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='table-footer'>
            <?php if($countBugs):?>
            <?php if($canBatchUnlink):?>
            <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
            <div class="table-actions btn-toolbar">
              <?php echo html::submitButton($lang->build->batchUnlink, '', 'btn');?>
            </div>
            <?php endif;?>
            <div class='table-statistic'><?php echo sprintf($lang->build->resolvedBugs, $countBugs);?></div>
            <?php endif;?>
            <?php
            $this->app->rawParams['type'] = 'bug';
            $bugPager->show('right', 'pagerjs');
            $this->app->rawParams['type'] = $type;
            ?>
          </div>
        </form>
      </div>
      <div class='tab-pane <?php if($type == 'generatedBug') echo 'active'?>' id='generatedBugs'>
        <div class='main-table' data-ride='table'>
          <table class='table has-sort-head'>
            <?php $vars = "buildID={$build->id}&type=generatedBug&link=$link&param=$param&orderBy=%s";?>
            <thead>
              <tr class='text-center'>
                <th class='c-id text-left'><?php common::printOrderLink('id',       $orderBy, $vars, $lang->idAB);?></th>
                <th class='w-severity'><?php common::printOrderLink('severity',     $orderBy, $vars, $lang->bug->severityAB);?></th>
                <th class='text-left'> <?php common::printOrderLink('title',        $orderBy, $vars, $lang->bug->title);?></th>
                <th class='w-100px'>   <?php common::printOrderLink('status',       $orderBy, $vars, $lang->bug->status);?></th>
                <th class='c-user'>    <?php common::printOrderLink('openedBy',     $orderBy, $vars, $lang->openedByAB);?></th>
                <th class='c-date'>    <?php common::printOrderLink('openedDate',   $orderBy, $vars, $lang->bug->openedDateAB);?></th>
                <th class='c-user'>    <?php common::printOrderLink('resolvedBy',   $orderBy, $vars, $lang->bug->resolvedByAB);?></th>
                <th class='w-100px'>   <?php common::printOrderLink('resolvedDate', $orderBy, $vars, $lang->bug->resolvedDateAB);?></th>
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
              <?php foreach($generatedBugs as $bug):?>
              <?php $bugLink = $this->createLink('bug', 'view', "bugID=$bug->id", '', true);?>
              <tr>
                <td class='text-left'><?php printf('%03d', $bug->id);?></td>
                <td>
                  <?php if($hasCustomSeverity):?>
                  <span class='label-severity-custom' data-severity='<?php echo $bug->severity;?>' title='<?php echo zget($lang->bug->severityList, $bug->severity);?>'><?php echo zget($lang->bug->severityList, $bug->severity, $bug->severity);?></span>
                  <?php else:?>
                  <span class='label-severity' data-severity='<?php echo $bug->severity;?>' title='<?php echo zget($lang->bug->severityList, $bug->severity, $bug->severity);?>'></span>
                  <?php endif;?>
                </td>
                <td class='text-left nobr' title='<?php echo $bug->title?>'>
                    <?php echo html::a($bugLink, $bug->title, '', isonlybody() ? "data-width='1000'" : "class='iframe' data-width='1000'");?>
                </td>
                <td>
                  <span class='status-bug status-<?php echo $bug->status?>'>
                    <?php echo $this->processStatus('bug', $bug);?>
                  </span>
                </td>
                <td><?php echo zget($users, $bug->openedBy);?></td>
                <td><?php echo substr($bug->openedDate, 5, 11)?></td>
                <td><?php echo zget($users, $bug->resolvedBy);?></td>
                <td><?php echo substr($bug->resolvedDate, 5, 11)?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <div class='table-footer'>
            <?php if($countGeneratedBugs):?>
            <div class='table-statistic'><?php echo sprintf($lang->build->createdBugs, $countGeneratedBugs);?></div>
            <?php endif;?>
            <?php
            $this->app->rawParams['type'] = 'generatedBug';
            $generatedBugPager->show('right', 'pagerjs');
            $this->app->rawParams['type'] = $type;
            ?>
          </div>
        </div>
      </div>
      <div class='tab-pane <?php if($type == 'buildInfo') echo 'active'?>' id='buildInfo'>
          <div id="mainContent" class="main-row">
              <div class="main-col col-8">
                  <div class="cell">
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->scmPath;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->scmPath) ? htmlspecialchars_decode($build->scmPath): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->svnPath;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->svnPath) ? htmlspecialchars_decode($build->svnPath): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->buildManual;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->buildManual) ? $build->buildManual: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->testPath;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->filePath) ? $build->filePath: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->releasePath;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->releasePath) ? $build->releasePath: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->plateName;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->plateName) ? $build->plateName: "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->desc;?></div>
                          <div class="detail-content article-content">
                              <?php echo !empty($build->desc) ? $build->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                          </div>
                      </div>
                      <div class="detail">
                          <div class='detail'>
                              <?php   $canOperate = in_array($build->status, $this->lang->build->fileCanOperateList) && ($this->app->user->account == 'admin' || $this->app->user->account == $build->verifyUser)  ?  true: false;
                              ?>
                              <div class='detail-title'><?php echo $lang->files;?>
                                  <span class="action-span pull-right detail-content article-content">
                                  <?php $canOperate && $build->systemverify == '1' ?  common::printIcon('build', 'editfiles', "buildID=$build->id", $build, 'list','edit', '', 'iframe', true,'data-position="50px"') : ''; ?>
                                 </span>
                              </div>
                              <div class='detail-content article-content'>
                                  <?php
                                  if($build->files){
                                      echo $this->fetch('file', 'printFiles', array('files' => $build->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => $canOperate));
                                  }else{
                                      echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                  }
                                  ?>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="cell" style="margin-top: 10px;"><?php include '../../../common/view/action.html.php';?></div>
                  <div class='main-actions'>
                      <div class="btn-toolbar">
                          <?php common::printBack($browseLink);?>
                          <div class='divider'></div>
                          <?php
                          $user = explode(',',$build->dealuser);
                          common::printIcon('build',   'edit', "buildID=$build->id&productID=$build->product&projectID=$build->project", $build, 'list');
                          if($this->app->user->account == 'admin' or  in_array($this->app->user->account,$user)) common::printIcon('build', 'deal', "buildID=$build->id", $build, 'list', 'time', '', 'iframe', true,"data-width='1200px'");
                          if(common::hasPriv('build', 'linkstory') and common::hasPriv('build', 'view') and common::canBeChanged('build', $build))
                          {
                              echo html::a($this->createLink('build', 'view', "buildID=$build->id&type=story&link=true"), "<i class='icon icon-link'></i>", '', "class='btn' title='{$lang->build->linkStory}' data-app='project'");
                          }
                          $lang->build->view = $lang->project->bug;
                          common::printIcon('build', 'view', "buildID=$build->id&type=generatedBug", $build, 'list', 'bug', '', '', '', "data-app='project'");
                          if(($this->app->user->account == 'admin' or in_array($this->app->user->account,$user) or $this->app->user->account == $build->createdBy) and $build->status == 'build') common::printIcon('build',   'edit', "buildID=$build->id&productID=$build->product&projectID=$build->project", $build, 'list');
                          common::printIcon('build',   'copy', "buildID=$build->id&productID=$build->product&projectID=$build->project", $build, 'list');
                          if(($this->app->user->account == 'admin' or in_array($this->app->user->account,$user)) and in_array($build->status, array('testfailed', 'versionfailed', 'verifyfailed'))) common::printIcon('build',   'rebuild', "buildID=$build->id&productID=$build->product&projectID=$build->project", $build, 'list','start', '', 'iframe', true);
                          common::hasPriv('build',  'delete') ?  common::printIcon('build',   'delete', "buildID=$build->id", $build, 'list','trash','hiddenwin') : '';
                          common::hasPriv('build',  'back') and $build->status != 'back' ?  common::printIcon('build',   'back', "buildID=$build->id", $build, 'list','history','','iframe',true) : '';
                          common::hasPriv('build',  'ignore') ?  common::printIcon('build',   'ignore', "buildID=$build->id", $build, 'list','ban','hiddenwin') : '';

                          /* if(common::hasPriv('build',  'delete', $build))
                           {
                               $deleteURL = $this->createLink('build', 'delete', "buildID=$build->id&confirm=yes");
                               echo html::a("###", '<i class="icon-trash"></i>', '', "onclick='ajaxDelete(\"$deleteURL\", \"buildList\", confirmDelete)' class='btn' title='{$lang->build->delete}'");
                           }*/
                          ?>
                      </div>
                  </div>
              </div>
              <div class="side-col col-4">
                  <div class="cell">
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->build->basicInfo;?></div>
                          <div class='detail-content'>
                              <table class='table table-data'>
                                  <tbody>
                                  <tr>
                                      <th class="w-p25"><?php echo $lang->build->status;?></th>
                                      <td><?php echo zget($lang->build->statusList, $build->status, '');?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->dealuser;?></th>
                                      <td><?php echo zmget($users, $build->dealuser, '');?></td>
                                  </tr>
                                  <tr>
                                      <th class="w-100px"><?php echo $lang->build->exection;?></th>
                                      <td><?php /*if(!common::printLink('execution', 'view', "executionID=$build->execution", $build->executionName))*/
                                          foreach ($build->executionName as $item) {
                                              echo $item->name.'<br>';
                                      }?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->taskName;?></th>
                                      <!-- <td><?php /*echo $build->taskid ? html::a($this->createLink('task', 'view', 'id=' . $build->taskid), $build->taskName, '', "style='color: #0c60e1;'") : '';*/?></td>-->
                                      <?php
                                      $taskids = explode(',',$build->taskid);
                                      $taskNames = array_filter(explode(',',$build->taskName));
                                     ?>
                                          <td><?php  foreach ($taskids as $key=> $item) { echo $item ?  html::a('javascript:void(0)', ($key+1).'、'.$taskNames[$key], '', 'onclick="seturl('.$build->project.','.$item.')" style="color: #0c60e1;"').'<br>' : '';}?></td>
                                          <td class="hidden"><?php echo html::a('','','','  id="buildtaskurl"')?></td>

                                  </tr>
                                  <tr>
                                      <th class="w-100px"><?php echo $lang->build->app;?></th>
                                      <td><?php echo zget($apps, $build->app,'');?></td>
                                  </tr>
                                  <tr>
                                      <th class="w-100px"><?php echo $lang->build->product;?></th>
                                      <td><?php echo zget($products, $build->product);?></td>
                                  </tr>
                                  <tr>
                                      <th class="w-100px"><?php echo $lang->build->code;?></th>
                                      <td><?php echo $build->code;?></td>
                                  </tr>
                                  <tr>
                                      <th class="w-100px"><?php echo $lang->build->version;?></th>
                                      <td><?php echo zget($plans, $build->version);?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->name;?></th>
                                      <td><?php echo $build->name;?></td>
                                  </tr>
                                  <tr>
                                      <th class='w-100px'><?php echo $lang->build->releaseName;?></th>
                                      <td><?php  if($releaseId->name)  echo html::a($this->createLink('projectrelease', 'view', 'id=' .$releaseId->id , ''), $releaseId->name, '', " data-type='iframe' data-width='90%' style='color: #0c60e1;'") ;?>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->problemid;?></th>
                                      <td>
                                          <?php foreach($problems as $objectID => $object):?>
                                              <p><?php echo html::a($this->createLink('problem', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                          <?php endforeach;?>
                                      </td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->demandid;?></th>
                                      <td>
                                          <?php foreach($demands as $objectID => $object):?>
                                              <p><?php echo html::a($this->createLink(strpos($object,'WD') ? 'demandinside' : 'demand', 'view', 'id=' . $objectID, '', true), $object, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                          <?php endforeach;?>
                                      </td>
                                  </tr>
                                  <tr>
                                      <th class='w-100px'><?php echo $lang->build->sendlineId;?></th>
                                      <td><?php foreach($build->secondorder as $object):?>
                                              <p><?php echo html::a($this->createLink('secondorder', 'view', 'id=' . $object->id, '', true), $object->code, '', "data-toggle='modal' data-type='iframe' data-width='90%' style='color: #0c60e1;'");?></p>
                                          <?php endforeach;?>
                                      </td>
                                  </tr>
                                  <?php if($build->purpose):?>
                                  <tr>
                                      <th><?php echo $lang->build->purpose;?></th>
                                      <td><?php echo zget($lang->build->purposeList, $build->purpose);?></td>
                                  </tr>
                                  <?php endif;?>
                                  <?php if($build->rounds):?>
                                  <tr>
                                      <th><?php echo $lang->build->rounds;?></th>
                                      <td><?php echo zget($lang->build->roundsList, $build->rounds);?></td>
                                  </tr>
                                  <?php endif;?>
                                  <tr>
                                      <th><?php echo $lang->build->createdBy;?></th>
                                      <td><?php echo zget($users, $build->createdBy, '');?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->createdDate;?></th>
                                      <td><?php echo $build->createdDate;?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->editedBy;?></th>
                                      <td><?php echo zget($users, $build->editedBy, '');?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->editedDate;?></th>
                                      <td><?php echo $build->editedDate == '0000-00-00 00:00:00' ? '' : $build->editedDate;?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->builder;?></th>
                                      <td><?php echo $build->builder ? zget($users, $build->builder, '') : '';?></td>
                                  </tr>
                                  <tr>
                                      <th class='w-100px'><?php echo $lang->build->date;?></th>
                                      <td><?php echo $build->date == '0000-00-00' ? '' : $build->date;?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->testUser;?></th>
                                      <td><?php  echo $build->testUser ? zget($users, $build->testUser, '') : '';?></td>
                                  </tr>
                                  <?php if($build->testRelevantUser):?>
                                  <tr>
                                      <th><?php echo $lang->build->testRelevantUser;?></th>
                                      <td><?php  echo  zmget($users, $build->testRelevantUser, '');?></td>
                                  </tr>
                                  <?php endif;?>

                                  <?php if($build->severityTestUser):?>
                                      <tr>
                                          <th><?php echo $lang->build->severityTestUser;?></th>
                                          <td><?php  echo  zmget($users, $build->severityTestUser, '');?></td>
                                      </tr>
                                  <?php endif;?>

                                  <?php if($qualitygateInfo):?>
                                      <tr>
                                          <th><?php echo $lang->build->qualitygate;?></th>
                                          <td>
                                              <?php echo html::a($this->createLink('qualitygate', 'view', 'id=' . $qualitygateInfo->id, '', true), $qualitygateInfo->code, '', 'class="iframe"');?>
                                          </td>
                                      </tr>
                                  <?php endif;?>


                                  <?php if($build->createdDate < '2024-04-02 20:00'):?>
                                  <tr>
                                      <th><?php echo $lang->build->systemverify;?></th>
                                      <td><?php echo zget($lang->build->needOptions, $build->systemverify);?></td>
                                  </tr>
                                  <tr>
                                      <th><?php echo $lang->build->verifyUser;?></th>
                                      <td><?php echo $build->verifyUser ? zget($users, $build->verifyUser, '') : '';?></td>
                                  </tr>

                                  <tr>
                                      <th><?php echo $lang->build->actualVerifyUser;?></th>
                                      <td><?php echo zmget($users, $build->actualVerifyUser);?></td>
                                  </tr>
                                  <?php if(!empty($build->verifyRejectBack)):?>
                                  <tr>
                                      <th><?php echo $lang->build->verifyRejectBack;?></th>
                                      <td><?php echo  $build->verifyRejectBack;?></td>
                                  </tr>
                                  <?php endif;?>
                                  <?php if($build->verifyRelevantUser):?>
                                      <tr>
                                          <th><?php echo $lang->build->verifyRelevantUser;?></th>
                                          <td><?php  echo  zmget($users, $build->verifyRelevantUser, '');?></td>
                                      </tr>
                                  <?php endif;?>
                                  <tr>
                                      <th><?php echo $lang->build->actualVerifyDate;?></th>
                                      <td><?php echo  strpos($build->actualVerifyDate,'0000-00-00') === false &&  $build->actualVerifyDate ? date('Y-m-d',strtotime($build->actualVerifyDate)) : '';?></td>
                                  </tr>
                                  <?php endif;?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                  <div class="cell">
                      <div class="detail">
                          <div class="detail-title"><?php echo $lang->consumedTitle;?>
                       <!-- <span style="color: lightslategray;font-weight: 400;font-size:13px"><?php /*echo $lang->build->consumedTip*/?></span>-->
                          </div>
                          <div class='detail-content'>
                              <table class='table table-data'>
                                  <tbody>
                                  <tr>
                                      <th class='w-100px'><?php echo $lang->build->nodeUser;?></th>
                                      <!--<td class='text-right'><?php /*echo $lang->build->workload;*/?></td>-->
                                      <td class='text-center'><?php echo $lang->build->before;?></td>
                                      <td class='text-center'><?php echo $lang->build->after;?></td>
                                      <td class='text-left'><?php echo $lang->actions;?></td>
                                  </tr>
                                  <?php foreach($consumeds as $index => $c):?>
                                      <tr>
                                          <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                                         <!-- <td class='text-right'><?php /*echo $c->consumed . ' ' . $lang->hour;*/?></td>-->
                                          <td class='text-center'><?php echo zget($lang->build->statusList, $c->before, '-');?></td>
                                          <td class='text-center'><?php if($c->after == 'verifysuccess' and $build->systemverify == '0') echo $lang->build->versionsuccess; else echo zget($lang->build->changestatus, $c->after, '-');?></td>
                                          <td class='c-actions text-left'>
                                              <?php
                                              common::printIcon('build', 'workloadEdit', "buildID={$build->id}&consumedid={$c->id}", $build, 'list', 'edit', '', 'iframe', true);
                                              if($index) common::printIcon('build', 'workloadDelete', "buildID=$build->id&consumedid={$c->id}", $build, 'list', 'trash', '', 'iframe', true);
                                              common::printIcon('build', 'workloadDetails', "buildID={$build->id}&consumedid={$c->id}", $build, 'list', 'glasses', '', 'iframe', true);
                                              ?>
                                          </td>
                                      </tr>
                                  <?php endforeach;?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>
<?php js::set('param', helper::safe64Decode($param))?>
<?php js::set('link', $link)?>
<?php js::set('buildID', $build->id)?>
<?php js::set('type', $type)?>
<?php include '../../../common/view/footer.html.php';?>
