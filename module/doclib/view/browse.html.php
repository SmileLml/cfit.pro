<?php
/**
 * The browse view file of doclib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @author      Wang Yidong, Zhu Jinyong
 * @package     doclib
 * @version     $Id: browse.html.php $
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <div class='btn-group'>
      <a href='javascript:;' class='btn btn-link btn-limit text-ellipsis' data-toggle='dropdown' style="max-width: 120px;"><span class='text' title='<?php echo $doclib->name;?>'><?php echo $doclib->name;?></span> <span class='caret'></span></a>
      <ul class='dropdown-menu' style='max-height:240px; max-width: 300px; overflow-y:auto'>
        <?php
        foreach($libs as $id => $doclibName)
        {
            echo "<li>" . html::a($this->createLink('doclib', 'browse', "libID=$id&branchID="), $doclibName, '', "title='{$doclibName}' class='text-ellipsis' data-app='{$app->openApp}'") . "</li>";
        }
        ?>
      </ul>
    </div>
    <?php if(!empty($branches)):?>
    <div class='btn-group'>
      <a href='javascript:;' class='btn btn-link btn-limit text-ellipsis' data-toggle='dropdown' style="max-width: 120px;"><span class='text' title='<?php echo $branches[$branchID];?>'><?php echo $branches[$branchID];?></span> <span class='caret'></span></a>
      <ul class='dropdown-menu' style='max-height:240px; max-width: 300px; overflow-y:auto'>
        <?php
        foreach($branches as $id => $branchName)
        {
            echo "<li>" . html::a($this->createLink('doclib', 'browse', "libID=$libID&branchID=$id"), $branchName, '', "title='{$branchName}' class='text-ellipsis' data-app='{$app->openApp}'") . "</li>";
        }
        ?>
      </ul>
    </div>
    <?php endif;?>
    <div class="page-title">
      <strong>
        <?php
        echo html::a($this->doclib->createLink('browse', "libID=$libID&branchID=$branchID"), $doclib->name, '');
        $paths= explode('/', $path);
        $fileName = array_pop($paths);
        $postPath = '';
        foreach($paths as $pathName)
        {
            $postPath .= $pathName . '/';
            echo '/' . ' ' . html::a($this->doclib->createLink('browse', "libID=$libID&branchID=$branchID&path=" . $this->doclib->encodePath($postPath)), trim($pathName, '/'), '', "data-app='{$app->openApp}'");
        }
        echo '/' . ' ' . $fileName;
        ?>
      </strong>
    </div>
  </div>
  <div class="btn-toolbar pull-right">
    <span class='last-sync-time'><?php echo $lang->doclib->notice->lastSyncTime . $cacheTime?></span>
    <?php echo html::a($this->doclib->createLink('browse', "libID=$libID&branchID=$branchID&path=" . $this->doclib->encodePath($path) . "&revision=$revision&refresh=1"), "<i class='icon icon-refresh'></i> " . $lang->refresh, '', "class='btn btn-primary' data-app={$app->openApp}");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col main-table">
    <table class='table table-fixed'>
      <thead>
        <tr>
          <th width='30'></th>
          <th style="min-width: 150px;"><?php echo $lang->doclib->name?></th>
          <th width='80' class='text-center'><?php echo $lang->doclib->revisions?></th>
          <th width='80'><?php echo $lang->doclib->time?></th>
          <th width='120'><?php echo $lang->doclib->committer?></th>
          <th><?php echo $lang->doclib->comment?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($infos as $info):?>
        <?php if(empty($info->name)) continue;?>
        <tr>
          <td class="icon">
            <span class="<?php echo $info->kind == 'dir' ? 'directory' : 'file';?> mini-icon"></span>
          </td>
          <td>
          <?php
          $infoPath = trim($path . '/' . $info->name, '/');
          $link = $info->kind == 'dir' ? $this->doclib->createLink('browse', "libID=$libID&branchID=$branchID&path=" . $this->doclib->encodePath($infoPath)) : $this->doclib->createLink('view', "libID=$libID&entry=" . $this->doclib->encodePath($infoPath));
          echo html::a($link, $info->name, '', "title='{$info->name}' data-app={$app->openApp}");
          ?>
          </td>
          <td align='center'><?php echo $doclib->SCM == 'Git' ? substr($info->revision, 0, 10) : $info->revision;?></td>
          <td><?php echo substr($info->date, 0, 10)?></td>
          <td><?php echo $info->committer?></td>
          <?php $comment = htmlspecialchars($info->comment, ENT_QUOTES);?>
          <td class='comment' title='<?php echo $comment?>'><?php echo $comment?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <div class="side-col" id="sidebar">
    <div class="sidebar-toggle"><i class="icon icon-angle-right"></i></div>
    <div class='side-body'><?php include 'ajaxsidecommits.html.php';?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
