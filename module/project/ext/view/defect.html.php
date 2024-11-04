<?php $config->project->datatable = $config->project->datatableDefect; ?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/datatable.fix.html.php';?>

<?php js::set('projectID', $projectID)?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i = 0;
    foreach($lang->defect->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a(inlink('defect', "projectID=$projectID&browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

        $i++;
        if($i >= 10) break;
    }
    if($i>=10)
    {
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        $i = 0;
        foreach($lang->defect->labelList as $label => $labelName)
        {
            $i++;
            if($i <= 10) continue;

            $active = $browseType == $label ? 'btn-active-text' : ''; 
            echo '<li>' . html::a(inlink('defect', "projectID=$projectID&browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
        }
        echo '</ul></div>';
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
      <?php
      $class = common::hasPriv('defect', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('defect', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('defect', 'export') ? $this->createLink('defect', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->defect->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='defect'></div>
    <?php if(empty($defects)):?>
    <?php $useDatatable = '';?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <?php
    $datatableId  = $this->moduleName . ucfirst($this->methodName);
    $useDatatable = (isset($config->datatable->$datatableId->mode) and $config->datatable->$datatableId->mode == 'datatable');

    ?>
    <form class='main-table' id='defectForm' method='post'  <?php if(!$useDatatable) echo "data-ride='table'";?> data-checkable='false'>
    <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
    </div>
    <?php 
    $vars = "projectID=$projectID&browseType=$browseType&param=$param&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";

    if($useDatatable) include '../../../common/view/datatable.html.php';
    if(!$useDatatable) include '../../../common/view/tablesorter.html.php';

    $setting = $this->datatable->getSetting('project');
    $widths  = $this->datatable->setFixedFieldWidth($setting);
    $columns = 0;
?>
    <?php if(!$useDatatable) echo '<div class="table-responsive">';?>
    <table class='table has-sort-head<?php if($useDatatable) echo ' datatable';?>' id='caseList' data-fixed-left-width='<?php echo $widths['leftWidth']?>' data-fixed-right-width='<?php echo $widths['rightWidth']?>' data-checkbox-name='caseIDList[]'>
        <thead>
          <tr>
          <?php
          foreach($setting as $key => $value)
          {
              if($value->show)
              {
                  $this->datatable->printHead($value, $orderBy, $vars);
                  $columns ++;
              }
          }
          ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($defects as $defect):?>
          <tr data-id='<?php echo $defect->id?>'>
            <?php foreach($setting as $key => $value) $this->project->printCellDefect($value, $defect, $users,$useDatatable ? 'datatable' : 'table', $projects, $products);?>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <?php if(!$useDatatable) echo '</div>';?>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<script>
<?php if($useDatatable):?>
$(function(){$('#defectForm').table();})
<?php endif;?>
</script>
<?php include '../../../common/view/footer.html.php';?>
