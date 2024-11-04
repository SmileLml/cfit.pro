<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $hiddenLabelList = [];
    $i = 0;
    foreach($lang->change->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : '';
        $i++;
        if($i < 13){
            echo html::a($this->createLink('change', 'browse', "project=$projectID&browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        }else{
            $hiddenLabelList[$label] = $labelName;
        }
    }

    if(!empty($hiddenLabelList)){
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        foreach($hiddenLabelList   as $label => $labelName) {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo '<li>' . html::a($this->createLink('change', 'browse', "project=$projectID&browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'"). '</li>';
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
      $class = common::hasPriv('change', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('change', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('change', 'export') ? $this->createLink('change', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->change->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php if(common::hasPriv('change', 'create')) echo html::a($this->createLink('change', 'create', "projectID=$projectID"), "<i class='icon-plus'></i> {$lang->change->fixApply}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='change'></div>
    <?php if(empty($changes)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='changeForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "projectID=$projectID&browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='changes'>
        <thead>
          <tr>
            <th class='w-120px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->change->code);?></th>
            <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->change->level);?></th>
            <th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->change->type);?></th>
            <th class='w-80px'><?php common::printOrderLink('category', $orderBy, $vars, $lang->change->category);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->change->createdBy);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->change->createdDept);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->change->createdDate);?></th>
            <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->change->status);?></th>
            <th class='w-80px'><?php echo $lang->change->pending;?></th>
            <th class='text-center w-80px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($changes as $change):?>
          <tr>
            <td><?php echo common::hasPriv('change', 'view') ? html::a(inlink('view', "changeID=$change->id"), $change->code) : $change->code;?></td>
            <td><?php echo zget($lang->change->levelList, $change->level);?></td>
            <td><?php echo zget($lang->change->typeList, $change->type);?></td>
            <td><?php echo zget($lang->change->categoryList, $change->category);?></td>
            <td><?php echo zget($users, $change->createdBy, '');?></td>
            <td><?php echo zget($depts, $change->createdDept, '');?></td>
            <td><?php echo substr($change->createdDate, 0, 11);?></td>
            <td><?php echo zget($lang->change->statusList, $change->status);?></td>
              <?php
              $reviewers = $change->reviewers;
              $reviewersArray = explode(',', $reviewers);
              $appiontUsers = $change->appiontUsers;
              $appiontUsersArray = explode(',', $appiontUsers);
              //所有审核人
              $reviewersArray = array_filter(array_merge($reviewersArray, $appiontUsersArray));
              //所有审核人
              $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
              $reviewerUsersStr = implode(',', $reviewerUsers);
              $subCount = 3;
              $reviewerUsersSubStr = getArraySubValuesStr($reviewerUsers, $subCount);
              ?>
              <td title="<?php echo $reviewerUsersStr; ?>">
                  <?php echo $reviewerUsersSubStr ?>
              </td>

            <td class='c-actions'>
              <?php
              common::printIcon('change', 'edit', "changeID=$change->id", $change, 'list');
              common::printIcon('change', 'run', "changeID=$change->id", $change, 'list', 'play', '', 'iframe', true);
              common::printIcon('change', 'appoint', "changeID=$change->id", $change, 'list', 'hand-right', '', 'iframe', true);
              common::printIcon('change', 'recall', "changeID=$change->id", $change, 'list', 'back', '', 'iframe', true);
              common::printIcon('change', 'review', "changeID=$change->id&version=$change->version&reviewStage=$change->reviewStage", $change, 'list', 'glasses', '', 'iframe', true,'data-width="1200px"');
              common::printIcon('change', 'delete', "changeID=$change->id", $change, 'list', 'trash', '', 'iframe', true);
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
