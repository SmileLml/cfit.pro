<?php
/**
 * The admin view file of feedback module of ZenTaoPMS.
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
js::set('moduleID', $moduleID);
js::set('browseType', isset($browseType) ? $browseType : '');
?>
<div id='mainMenu' class="clearfix">
  <div class='btn-toolbar pull-left'>
    <?php
    foreach($lang->feedback->featurebar as $type => $link)
    {
        $activeClass = $type == $browseType ? "btn-active-text" : '';
        list($label, $module, $method, $params) = explode('|', $link);
        echo html::a($this->createLink($module, $method, $params), "<span class='text'>$label</span>", '',"class='btn btn-link $activeClass' data-app={$app->openApp}");
    }
    ?>
  </div>
  <div class='btn-toolbar pull-right'>
    <?php
    if(common::hasPriv('feedback', 'export')) echo html::a($this->createLink('feedback', 'export', "browseType=$browseType&orderBy=$orderBy"), "<i class='icon-export muted'></i> " . $this->lang->export, '', "class='btn btn-link export'");
    if(common::hasPriv('feedback', 'create')) echo html::a($this->createLink('feedback', 'create'), "<i class='icon-plus'></i> " . $this->lang->feedback->create, '', "class='btn btn-primary'");
    ?>
  </div>
</div>
<div id='queryBox' data-module='feedback' class='cell <?php if($browseType == 'bysearch') echo 'show';?>'></div>
<div id='mainContent' class='main-row'>
  <div class="side-col" id="sidebar">
    <div class="sidebar-toggle"><i class="icon icon-angle-left"></i></div>
    <div class="cell">
      <?php if(!$moduleTree):?>
      <hr class="space">
      <div class="text-center text-muted"><?php echo $lang->feedback->noModule;?></div>
      <hr class="space">
      <?php endif;?>
      <?php echo $moduleTree;?>
      <div class="text-center">
        <?php common::printLink('tree', 'browse', "productID=0&view=feedback", $lang->feedback->manageCate, '', "class='btn btn-info btn-wide' data-group='feedback'");?>
        <hr class="space-sm" />
      </div>
    </div>
  </div>
  <div class="main-col">
  <?php $viewMethod = 'adminView'?>
  <?php include './data.html.php';?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>
$('#module' + moduleID).closest('li').addClass('active');
<?php if(count($feedbacks) <= 2):?>
$('#feedbackForm .table-footer .table-actions #assignedTo').closest('.btn-group.dropup').removeClass('dropup').addClass('dropdown');
$('#feedbackForm .table-footer .table-actions #moduleSearchBox').closest('.btn-group.dropup').removeClass('dropup').addClass('dropdown');
<?php endif;?>
</script>
