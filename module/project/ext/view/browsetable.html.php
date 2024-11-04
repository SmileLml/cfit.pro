<?php
/**
 * The prjbrowse view file of project module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     project
 * @version     $Id: prjbrowse.html.php 4769 2013-05-05 07:24:21Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php
include '../../../common/view/datatable.fix.html.php';
js::set('orderBy', $orderBy);
js::set('programID', $programID);
js::set('browseType', $browseType);
?>
<style>
.project-type-label.label-outline {width: 50px; min-width: 50px;}
.project-type-label.label {overflow: unset !important; text-overflow: unset !important; white-space: unset !important;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolBar pull-left">
    <?php foreach($lang->project->featureBar as $key => $label):?>
    <?php $active = $browseType == $key ? 'btn-active-text' : '';?>
    <?php $label = "<span class='text'>$label</span>";?>
    <?php if($browseType == $key) $label .= " <span class='label label-light label-badge'>{$pager->recTotal}</span>";?>
    <?php echo html::a(inlink('browse', "programID=$programID&browseType=$key"), $label, '', "class='btn btn-link $active'");?>
    <?php endforeach;?>
    <?php echo html::checkbox('involved', array('1' => $lang->project->mine), '', $this->cookie->involved ? 'checked=checked' : '');?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->project->search;?></a>
  </div>
    <div class="btn-toolbar pull-right">
        <div class='btn-group'>
            <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
            <ul class="dropdown-menu" id='exportActionMenu'>
        <?php
//        $class = common::hasPriv('application', 'export') ? '' : "class=disabled";
        $class =  '';
        $misc  = "data-toggle='modal' data-type='iframe' class='export'";
        $link  = $this->createLink('weeklyreport', 'export', "orderBy=id_desc&browseType=all") ;
        echo "<li $class>" . html::a($link, '导出周报', '', $misc) . "</li>";

        $class = common::hasPriv('project', 'exportList') ? '' : "class=disabled";
        $misc = common::hasPriv('project', 'exportList') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link = common::hasPriv('project', 'exportList') ? $this->createLink('project', 'exportList',"orderBy=id_desc&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->project->exportList, '', $misc) . "</li>";
        ?>
            </ul>
    </div>
</div>
<div id='mainContent' class="main-row fade">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='project'></div>
    <?php if(empty($projectStats)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->project->empty;?></span>
      </p>
    </div>
    <?php else:?>
    <?php
    $datatableId  = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');
    $useDatatable = true;
    ?>
    <form class='main-table table-project' id='projectForm' method='post' <?php if($useDatatable) echo "data-ride='table'";?>>
      <!--
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      -->
      <?php
      $vars = "programID=$programID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";
      if($useDatatable) include '../../../common/view/datatable.html.php';

      $setting = $this->datatable->getSetting('project');
      $widths  = $this->datatable->setFixedFieldWidth($setting);
      $canBatchAction = true;
      ?>
      <?php if(!$useDatatable) echo '<div class="table-responsive">';?>
        <table class='table has-sort-head <?php if($useDatatable) echo ' datatable';?>' id='projectList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>'>
          <thead>
            <tr>
            <?php
            foreach($setting as $value)
            {
                if($value->show)
                {
                    $this->datatable->printHead($value, $orderBy, $vars, $canBatchAction);
                }
            }
            ?>
            </tr>
          </thead>
          <tbody>
           <?php foreach($projectStats as $project):?>
           <tr data-id='<?php echo $project->id?>'>
             <?php foreach($setting as $value) $this->project->printCellNew($value, $project, $users, $programID);?>
           </tr>
           <?php endforeach;?>
          </tbody>
        </table>
      <?php if(!$useDatatable) echo '</div>';?>
      <div class='table-footer'>
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>
<script>$('ul.dropdown-menu').find('button').attr('class', 'disabled btn iframe btn-action');</script>
