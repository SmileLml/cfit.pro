<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    $i = 0;
    foreach($lang->defect->labelList as $label => $labelName)
    {
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('defect', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");

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
            echo '<li>' . html::a($this->createLink('defect', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='defectForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head text-center' id='defects'>
        <thead>
          <tr>
            <th class='w-p5'><?php common::printOrderLink('id', $orderBy, $vars, $lang->defect->idAB);?></th>
            <th style="width: 12%"><?php common::printOrderLink('title', $orderBy, $vars, $lang->defect->title);?></th>
            <th style="width: 12%"><?php common::printOrderLink('product', $orderBy, $vars, $lang->defect->product);?></th>
            <th style="width: 12%"><?php common::printOrderLink('project', $orderBy, $vars, $lang->defect->project);?></th>
            <th style="width: 8%"><?php common::printOrderLink('pri', $orderBy, $vars, $lang->defect->pri);?></th>
            <th style="width: 8%"><?php common::printOrderLink('severity', $orderBy, $vars, $lang->defect->severity);?></th>
            <th class='w-p10'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->defect->createdDate);?></th>
            <th style="width: 8%"><?php common::printOrderLink('status', $orderBy, $vars, $lang->defect->status);?></th>
            <th style="width: 8%"><?php echo $lang->defect->nextUser;?></th>
              <th class='w-p10'><?php common::printOrderLink('dealSuggest', $orderBy, $vars, $lang->defect->dealSuggest);?></th>
              <th class='text-center w-p10'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($defects as $defect):?>
          <tr>
            <td title="<?php echo $defect->code;?>" class='text-ellipsis'><?php echo common::hasPriv('defect', 'view') ? html::a(inlink('view', "defectID=$defect->id"), $defect->id) : $defect->id;?></td>
            <td title="<?php echo $defect->title;?>" class='text-ellipsis'><?php echo $defect->title;?></td>
            <td title="<?php echo zget($products, $defect->product);?>" class='text-ellipsis'><?php echo zget($products, $defect->product);?></td>
            <td title="<?php echo zget($projects,$defect->project);?>" class='text-ellipsis'><?php echo zget($projects,$defect->project);?></td>
            <td><?php echo zget($lang->bug->defectPriList, $defect->pri);?></td>
            <td><?php echo zget($lang->bug->defectSeverityList, $defect->severity);?></td>
            <td><?php echo $defect->createdDate  != '0000-00-00 00:00:00' ? $defect->createdDate : '';;?></td>
            <td title="<?php echo zget($lang->defect->statusList, $defect->status);?>" class='text-ellipsis'>
                <?php echo zget($lang->defect->statusList, $defect->status);?>
            </td>
            <td title="<?php echo zget($users, $defect->dealUser);?>" class='text-ellipsis'><?php echo zget($users, $defect->dealUser, '');?></td>
              <td title="<?php echo zget($lang->defect->dealSuggestList, $defect->dealSuggest);?>" class='text-ellipsis'><?php echo zget($lang->defect->dealSuggestList, $defect->dealSuggest);?></td>
              <td class='c-actions text-center'>
              <?php
              common::printIcon('defect', 'edit', "defectID=$defect->id", $defect, 'list');
              common::printIcon('defect', 'confirm', "defectID=$defect->id", $defect, 'list', 'ok', '', 'iframe', true);
              common::printIcon('defect', 'deal', "defectID=$defect->id", $defect, 'list', 'time');
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
