<?php
/**
 * The create view file of lib module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2012 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @author      Wang Yidong, Zhu Jinyong
 * @package     lib
 * @version     $Id: create.html.php $
 */
?>
<?php
include '../../common/view/header.html.php';
include '../../common/view/form.html.php';
include '../../common/view/kindeditor.html.php';
css::import($jsRoot . 'misc/highlight/styles/code.css');
js::import($jsRoot  . 'misc/highlight/highlight.pack.js');
$encodePath = $this->projectdoc->encodePath($entry);
$version = " <span class=\"label label-info\">$revisionName</span>";
?>
<style>
.libCode .binary {text-align: center;}
.libCode .binary a {display: block; margin: 100px 0px;}
.libCode .binary a .icon-eye{font-size: 50px;}
</style>
<?php if(!isonlybody()):?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php echo html::a($this->session->doclibList, "<i class='icon icon-back icon-sm'></i>" . $lang->goback, '', "class='btn btn-link' data-app='{$app->openApp}'");?>
    <div class="divider"></div>
    <div class="page-title">
      <strong>
        <?php
        echo html::a($this->projectdoc->createLink('browse', "projectID=$projectID&libID=$libID&branchID=$branchID"), $doclib->name, '', "data-app='{$app->openApp}'");
        $paths= explode('/', $entry);
        $fileName = array_pop($paths);
        $postPath = '';
        foreach($paths as $pathName)
        {
            $postPath .= $pathName . '/';
            echo '/' . ' ' . html::a($this->projectdoc->createLink('browse', "projectID=$projectID&libID=$libID&branchID=$branchID&path=" . $this->projectdoc->encodePath($postPath)), trim($pathName, '/'), '', "data-app='{$app->openApp}'");
        }
        echo '/' . ' ' . $fileName;
        echo $version;
        ?>
      </strong>
    </div>
  </div>
  <div class="btn-toolbar pull-right">
    <?php echo html::a($this->projectdoc->createLink('revision', "projectID=$projectID&libID=$libID&revision=$revision"), $lang->projectdoc->allChanges, '', "class='btn btn-primary' data-app='{$app->openApp}'")?>
  </div>
</div>
<?php endif;?>

<?php if(!isonlybody()):?>
<div id="mainContent" class="main-row fade">
<?php endif;?>
  <div class="main-col libCode main">
    <div class="content panel">
      <div class='panel-heading'>
        <div class='panel-title'><?php echo $entry?></div>
        <div class='panel-actions'>
          <?php
          if(common::hasPriv('doclib', 'download')) echo html::a($this->projectdoc->createLink('download', "libID=$libID&path=$encodePath&fromRevision=$revision"), html::icon('download-alt') . $lang->projectdoc->download, 'hiddenwin', "class='btn btn-sm btn-primary'");
          ?>
          <div class='btn-group'>
            <?php echo html::commonButton(zget($lang->projectdoc->encodingList, $encoding, $lang->projectdoc->encoding) . "<span class='caret'></span>", "id='encoding' data-toggle='dropdown'", 'btn btn-sm btn-primary dropdown-toggle')?>
            <ul class='dropdown-menu' role='menu' aria-labelledby='encoding'>
              <?php foreach($lang->projectdoc->encodingList as $key => $val):?>
              <li><?php echo html::a($this->projectdoc->createLink('view', "projectID=$projectID&libID=$libID&entry=$encodePath&revision=$revision&showBug=$showBug&encoding=$key", 'html', isonlybody()), $val, '', "data-app='{$app->openApp}'")?></li>
              <?php endforeach;?>
            </ul>
          </div>
        </div>
      </div>
      <?php if(strpos($config->projectdoc->images, "|$suffix|") !== false):?>
      <div class='image'><img src='data:image/<?php echo $suffix?>;base64,<?php echo $content?>' /></div>
      <?php elseif($suffix == 'binary'):?>
      <div class='binary'>
      <?php echo html::a($collaboraUrl, "<i class='icon-eye'></i>", '_blank'); ?>
      </div>
      <?php else:?>
      <pre class="<?php echo $config->program->suffix[$suffix];?>"><?php echo trim(htmlspecialchars($content, defined('ENT_SUBSTITUTE') ? ENT_QUOTES | ENT_SUBSTITUTE : ENT_QUOTES));?></pre>
      <?php endif;?>
    </div>
  </div>
  <?php if(!isonlybody()):?>
  <div class="side-col" id="sidebar">
    <div class="sidebar-toggle"><i class="icon icon-angle-right"></i></div>
    <div class='side-body'><?php include 'ajaxsidecommits.html.php';?></div>
  </div>
  <?php endif;?>
<?php if(!isonlybody()):?>
</div>
<?php endif;?>
<?php if(!isonlybody()):?>
<div id="mainActions" class='main-actions'>
  <nav class="container">
    <?php if(!empty($preAndNext->pre))  echo html::a($this->projectdoc->createLink('view', "projectID=$projectID&libID=$libID&entry=$encodePath&revision={$preAndNext->pre}&showBug=$showBug", 'html', isonlybody()), "<i class='icon-pre icon-chevron-left'></i>", '', "id='prevPage' class='btn btn-info' data-app='{$app->openApp}' title='{$preAndNext->pre}'")?>
    <?php if(!empty($preAndNext->next)) echo html::a($this->projectdoc->createLink('view', "projectID=$projectID&libID=$libID&entry=$encodePath&revision={$preAndNext->next}&showBug=$showBug", 'html', isonlybody()), "<i class='icon-pre icon-chevron-right'></i>", '', "id='nextPage' class='btn btn-info' data-app='{$app->openApp}' title='{$preAndNext->next}'")?>
  </nav>
</div>
<?php endif;?>
<?php include '../../common/view/footer.html.php';?>
