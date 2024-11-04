<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    foreach($lang->productline->labelList as $label => $labelName)
    {   
        $active = $browseType == $label ? 'btn-active-text' : '';
        echo html::a($this->createLink('productline', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }   
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <?php if(common::hasPriv('productline', 'create')) echo html::a($this->createLink('productline', 'create'), "<i class='icon-plus'></i> {$lang->productline->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='productline'></div>
    <?php if(empty($productlines)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='productlineForm'>
      <?php $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
      <table class='table has-sort-head' id='productlineList' data-ride="table">
        <thead>
          <tr>
            <th class='c-id w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->productline->code);?></th>
            <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->productline->name);?></th>
            <th class='w-800px'><?php  common::printOrderLink('depts',      $orderBy, $vars, $lang->productline->depts);?></th>
            <th class='c-date'><?php  common::printOrderLink('date',      $orderBy, $vars, $lang->productline->date);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->productline->createdBy);?></th>
            <th class='c-actions-4'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($productlines as $app):?>
          <tr>
            <td><?php echo $app->code;?></td>
            <td title=<?php echo $app->name;?>>
             <?php echo common::hasPriv('productline', 'view') ? html::a(inlink('view', "appID=$app->id"), $app->name) : $app->name; ?>
            </td>
            <?php
              $ds = [];
              foreach(explode(',', $app->depts) as $dept)
              {
                  if(!$dept) continue;
                  $ds[] = zget($depts, $dept);
              }
              $dept = implode(', ', $ds);
            ?>
            <td title='<?php echo $dept?>' class='text-ellipsis'>
              <?php echo $dept?>
            </td>
            <td><?php echo $app->createdDate;?></td>
            <td><?php echo zget($users, $app->createdBy, $app->createdBy);?></td>
            <td class='c-actions'>
            <?php
              common::printIcon('productline', 'edit', "appID=$app->id", $app, 'list');
              if(common::hasPriv('productline', 'delete'))  echo html::a($this->createLink("productline", "delete", "appID=$app->id"), "<i class='icon-trash'></i> ", 'hiddenwin', "class='btn btn-action'");
              // common::printIcon('productline', 'activate', "appID=$app->id", $app, 'list', 'magic', '', 'hiddenwin');
            ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
      <div class="table-footer">
        <?php $pager->show('right', 'pagerjs');?>
      </div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
