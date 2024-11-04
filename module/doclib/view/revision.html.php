<?php
/**
 * The revision view file of repo module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @author      Wang Yidong, Zhu Jinyong
 * @package     repo
 * @version     $Id: revision.html.php $
 */
?>
<?php
session_start();
$_SESSION['repoList'] = $this->app->getURI(true);
session_write_close();
$pathInfo = empty($path) ? '' : '&path=' . $this->lib->encodePath($path);
$preDir   = empty($parentDir) ? $pathInfo : '&path=' . $this->lib->encodePath($parentDir);
$typeInfo = $type == 'file' ? '&type=file' : '';
?>
<?php include '../../common/view/header.html.php';?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php $browseLink = $app->session->revisionList != false ? $app->session->revisionList : $this->lib->createLink('browse', "libID={$libID}&branchID=$branchID&objectID=$objectID{$preDir}");?>
    <?php echo html::a($browseLink, "<i class='icon icon-back'></i>" . $lang->goback, '', "class='btn btn-link' data-app='{$app->openApp}'");?>
    <div class="divider"></div>
    <div class="page-title">
      <?php echo $lang->doclib->revisionA . ' ' . ($lib->SCM == 'Git' ? $this->lib->getGitRevisionName($revision, $log->commit) : $revision);?>
    </div>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col col-8'>
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'>
          <?php echo $lang->doclib->changes;?>
          <div class='pull-right'><?php if(common::hasPriv('doclib', 'diff')) echo html::a($this->doclib->createLink('diff', "libID=$libID&entry=&fromRevision=$oldRevision&toRevision=$revision"), $lang->doclib->diffAll, '', "data-app='{$app->openApp}'");?></div>
        </div>
        <div class='detail-content'>
          <table class='table no-margin'>
            <?php foreach($changes as $path => $change):?>
            <tr>
              <td><?php echo "<span class='label label-info label-badge'>" . $change['action'] . '</span> ' . $path?></td>
              <td class='w-80px text-center'><?php echo zget($change, 'view', '') . zget($change, 'diff', '')?></td>
            </tr>
            <?php endforeach;?>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class='side-col col-4'>
    <div class='cell'>
      <div class='detail'>
        <div class='detail-title'><?php echo $lang->doclib->info?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tr>
              <th class='w-80px'><?php echo $lang->doclib->committer?></th>
              <td><?php echo $log->committer?></td>
            </tr>
            <tr>
              <th><?php echo $lang->doclib->revisionA?></th>
              <td><?php echo $log->revision?></td>
            </tr>
            <?php if($lib->SCM == 'Git'):?>
            <tr>
              <th><?php echo $lang->doclib->commit?></th>
              <td><?php echo $log->commit?></td>
            </tr>
            <?php endif;?>
            <tr>
              <th><?php echo $lang->doclib->comment?></th>
              <td><?php echo $log->comment?></td>
            </tr>
            <tr>
              <th><?php echo $lang->doclib->time?></th>
              <td><?php echo $log->time?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="mainActions" class='main-actions'>
  <nav class="container">
    <?php if(!empty($preAndNext->pre))  echo html::a($this->doclib->createLink('revision', "libID=$libID&revision={$preAndNext->pre}" . $pathInfo . $typeInfo, 'html'), "<i class='icon-pre icon-chevron-left'></i>", '', "data-app='{$app->openApp}' id='prevPage' class='btn btn-info' title='{$preAndNext->pre}'")?>
    <?php if(!empty($preAndNext->next)) echo html::a($this->doclib->createLink('revision', "libID=$libID&revision={$preAndNext->next}" . $pathInfo . $typeInfo, 'html'), "<i class='icon-pre icon-chevron-right'></i>", '', "data-app='{$app->openApp}' id='nextPage' class='btn btn-info' title='{$preAndNext->next}'")?>
  </nav>
</div>
<?php include '../../common/view/footer.html.php';?>
