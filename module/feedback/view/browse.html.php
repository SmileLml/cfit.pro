<?php
/**
 * The browse view file of feedback module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     feedback
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php 
js::set('browseType', isset($browseType) ? $browseType : '');
js::set('moduleID', $moduleID);
?>
<?php $viewMethod = 'view'?>
<div id='mainMenu' class='clearfix'>
  <div class='btn-toolbar pull-left'>
    <?php foreach($lang->feedback->tabList as $type => $name):?>
    <?php $active = (isset($browseType) and $type == $browseType) ? "btn-active-text" : ''?>
    <?php echo html::a(inlink('browse', "browseType=$type"), "<span class='text'>{$name}</span>", '', "id='{$type}Tab' class='btn btn-link $active'")?>
    <?php endforeach?>
    <a href="javascript:;" class="querybox-toggle btn btn-link">
      <span class='text'><i class="icon-search icon"></i><?php echo $lang->searchLang;?></span>
    </a>
  </div>
  <div class='btn-toolbar pull-right'>
  <?php if(common::hasPriv('feedback', 'export')) echo html::a(inlink('export', "browseType=$browseType&orderBy=$orderBy"), "<i class='icon-export muted'></i> " . $lang->export, '', "class='btn btn-link export'")?>
  <?php if(common::hasPriv('feedback', 'create')) echo html::a(inlink('create'), "<i class='icon-plus'></i>" . $lang->feedback->create, '', "class='btn btn-primary'")?>
  </div>
</div>
<div id='queryBox' data-module='feedback' class='cell <?php if($browseType == 'bysearch') echo 'show';?>'></div>
<div id='mainContent' class='main-row'>
  <div class="side-col" id="sidebar">
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class="cell">
      <?php echo $moduleTree;?>
    </div>
  </div>
  <div class='main-col'>
  <?php
  $config->feedback->datatable->fieldList['actions']['width'] = '130';
  include './data.html.php';
  ?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
$('#module' + moduleID).closest('li').addClass('active');
</script>
