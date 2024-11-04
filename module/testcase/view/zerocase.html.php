<?php
/**
 * The browse view file of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     product
 * @version     $Id: browse.html.php 4909 2013-06-26 07:23:50Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include 'caseheader.html.php';?>
<?php js::set('resetActive', false);?>
<div id='mainContent' class='main-control'>
  <?php if(empty($stories)):?>
  <div class="table-empty-tip">
    <p><span class="text-muted"><?php echo $lang->story->noStory;?></span></p>
  </div>
  <?php else:?>
  <form method='post' id='productStoryForm' class='main-table table-story' data-ride='table'>
    <table class='table has-sort-head table-fixed' id='storyList'>
      <thead>
      <tr>
        <?php
        $vars = "applicationID=$applicationID&productID=$productID&branchID=$branchID&orderBy=%s";
        ?>
        <th class='c-id'>
          <?php common::printOrderLink('id', $orderBy, $vars, $lang->idAB);?>
        </th>
        <th class='w-60px'>  <?php common::printOrderLink('pri',        $orderBy, $vars, $lang->priAB);?></th>
        <th class='w-p30'>  <?php common::printOrderLink('title',      $orderBy, $vars, $lang->story->title);?></th>
        <th>                <?php common::printOrderLink('plan',       $orderBy, $vars, $lang->story->planAB);?></th>
        <th class='thWidth'><?php common::printOrderLink('source',     $orderBy, $vars, $lang->story->source);?></th>
        <th class='w-100px'><?php common::printOrderLink('openedBy',   $orderBy, $vars, $lang->openedByAB);?></th>
        <th class='w-100px'><?php common::printOrderLink('assignedTo', $orderBy, $vars, $lang->assignedToAB);?></th>
        <th class='w-70px'> <?php common::printOrderLink('estimate',   $orderBy, $vars, $lang->story->estimateAB);?></th>
        <th class='w-80px'> <?php common::printOrderLink('status',     $orderBy, $vars, $lang->statusAB);?></th>
        <th class='w-80px'> <?php common::printOrderLink('stage',      $orderBy, $vars, $lang->story->stageAB);?></th>
        <th class='c-actions-5'><?php echo $lang->actions;?></th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($stories as $key => $story):?>
      <?php
      $param = 0;
      $openApp = $this->app->openApp;
      if($this->app->openApp == 'project')   $param = $this->session->project;
      if($this->app->openApp == 'execution') $param = $this->session->execution;
      $viewLink = $this->createLink('story', 'view', "storyID=$story->id&version=0&param=$param");
      $canView  = common::hasPriv('story', 'view');
      ?>
      <tr>
        <td class='c-id'>
          <?php printf('%03d', $story->id);?>
        </td>
        <td><span class='label-pri <?php echo 'label-pri-' . $story->pri;?>' title='<?php echo zget($lang->story->priList, $story->pri);?>'><?php echo zget($lang->story->priList, $story->pri)?></span></td>
        <td class='text-left' title="<?php echo $story->title?>"><nobr><?php echo html::a($viewLink, $story->title, '', "data-app='product'");?></nobr></td>
        <td title="<?php echo $story->planTitle?>"><?php echo $story->planTitle;?></td>
        <td><?php echo $lang->story->sourceList[$story->source];?></td>
        <td><?php echo zget($users, $story->openedBy);?></td>
        <td><?php echo zget($users, $story->assignedTo);?></td>
        <td><?php echo $story->estimate;?></td>
        <td><span class='status-story status-<?php echo $story->status;?>'><?php echo $this->processStatus('story', $story);?></span></td>
        <td><?php echo zget($lang->story->stageList, $story->stage);?></td>
        <td class='c-actions'>
          <?php
          $vars = "storyID={$story->id}";
          $this->app->openApp = 'product';
          common::printIcon('story', 'change', $vars, $story, 'list', 'fork');
          common::printIcon('story', 'review', $vars, $story, 'list', 'glasses');
          common::printIcon('story', 'close',  $vars, $story, 'list', 'off', '', 'iframe', 'yes');
          common::printIcon('story', 'edit',   $vars, $story, 'list');

          if($openApp == 'project') $this->app->openApp = 'project';  // 如果是项目模块下，那么打开到项目下，否则按照之前的逻辑执行
          if($openApp != 'project') $this->app->openApp = 'qa';

          common::printIcon('story', 'createCase', "applicationID=$applicationID&productID=$story->product&branch=0&module=0&from=&param=0&$vars", $story, 'list', 'sitemap', '', '', false, "data-app='{$this->app->openApp}'");
          ?>
        </td>
      </tr>
      <?php endforeach;?>
      </tbody>
    </table>
    <div class='table-footer'>
      <?php $pager->show('right', 'pagerjs');?>
    </div>
  </form>
  <?php endif;?>
</div>
<script>
$(document).ready(function()
{
    $('#zerocaseTab').addClass('btn-active-text');
});
</script>
<?php include '../../common/view/footer.html.php';?>
