<?php include '../../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    foreach($lang->requirement->labelList as $label => $labelName)
    {   
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('product', 'requirement', "product=$productID&browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
    }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='requirement'></div>
    <?php if(empty($requirements)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='requirementForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <table class='table table-fixed' id='requirements'>
        <thead>
          <tr>
            <th class='w-100px'><?php echo $lang->requirement->code;?></th>
            <th><?php echo $lang->requirement->name;?></th>
            <th class='w-250px'><?php echo $lang->requirement->project;?></th>
            <th class='w-100px'><?php echo $lang->requirement->dept;?></th>
            <th class='w-70px'><?php echo $lang->requirement->owner;?></th>
            <th class='w-110px'><?php echo $lang->requirement->end;?></th>
            <th class='w-80px'> <?php echo $lang->requirement->status;?></th>
            <th class='w-70px'> <?php echo $lang->requirement->changedTimes;?></th>
            <th class='text-center w-50px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($requirements as $requirement):?>
          <tr>
            <td><?php echo $requirement->code;?></td>
            <td><?php echo common::hasPriv('requirement', 'view') ? html::a($this->createLink('requirement', 'view', "requirementID=$requirement->id"), $requirement->name, '', "data-app='product'") : $requirement->name;?></td>
            <td title="<?php echo zget($projects, $requirement->project, '');?>"><?php echo zget($projects, $requirement->project, '');?></td>
            <td><?php echo zget($depts, $requirement->dept);?></td>
            <td><?php echo zget($users, $requirement->owner, '');?></td>
            <td><?php if(!helper::isZeroDate($requirement->end)) echo $requirement->end;?></td>
            <td><?php echo zget($lang->requirement->statusList, $requirement->status);?></td>
            <td><?php echo $requirement->changedTimes;?></td>
            <td class='c-actions'>
              <?php // common::printIcon('requirement', 'subdivide', "productID=$productID&branch=&module=&storyID=$requirement->id", $requirement, 'list', 'split', '', '', '', '', $this->lang->story->subdivide); ?>
              <?php $requirement->type = 'requirement'; $requirement->parent = 0;?>
              <?php common::printIcon('story', 'batchCreate', "productID=$productID&branch=&module=&storyID=", $requirement, 'list', 'split', '', '', '', '', $this->lang->story->subdivide);?>
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
<?php include '../../../common/view/footer.html.php';?>
