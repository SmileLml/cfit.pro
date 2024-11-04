<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php
    $i=0;
    foreach($lang->testingrequest->labelList as $label => $labelName)
    {
      $active = $browseType == $label ? 'btn-active-text' : '';
      echo html::a($this->createLink('testingrequest', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
      $i++;
      if($i >= 11) break;
    }
    if($i >= 11)
    {
      echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
      echo "<ul class='dropdown-menu'>";
      $i = 0;
      foreach($lang->testingrequest->labelList as $label => $labelName)
      {
          $i++;
          if($i <= 11) continue;

          $active = $browseType == $label ? 'btn-active-text' : '';
          echo '<li>' . html::a($this->createLink('testingrequest', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
        $class = common::hasPriv('testingrequest', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('testingrequest', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link  = common::hasPriv('testingrequest', 'export') ? $this->createLink('testingrequest', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->testingrequest->export, '', $misc) . "</li>";
        ?>
      </ul>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='testingRequest'></div>
    <?php if(empty($testingrequest)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' id='testingrequestForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
        <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='testingrequest'>
          <thead>
          <tr>
            <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->testingrequest->code);?></th>
            <th class='w-80px'><?php common::printOrderLink('giteeId', $orderBy, $vars, $lang->testingrequest->giteeId);?></th>
            <th class='w-180px'><?php common::printOrderLink('testSummary', $orderBy, $vars, $lang->testingrequest->testSummary);?></th>
            <th class='w-160px'><?php common::printOrderLink('acceptanceTestType', $orderBy, $vars, $lang->testingrequest->acceptanceTestType);?></th>
            <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->testingrequest->app);?></th>
            <th class='w-120px'><?php common::printOrderLink('testProductName', $orderBy, $vars, $lang->testingrequest->testProductName);?></th>
            <th class='w-120px'><?php common::printOrderLink('projectCode', $orderBy, $vars, $lang->testingrequest->projectName);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->testingrequest->createdDept);?></th>
            <th class='w-60px'><?php common::printOrderLink('returnTimes', $orderBy, $vars, $lang->testingrequest->returnTimes);?></th>
            <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->testingrequest->createdBy);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->testingrequest->createdDate);?></th>
            <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->testingrequest->cardStatus);?></th>
            <th class='w-120px'><?php common::printOrderLink('outwarddeliveryCode', $orderBy, $vars, $lang->outwarddelivery->outwarddeliveryCode);?></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($testingrequest as $item):?>
            <tr>
              <td><?php echo common::hasPriv('testingrequest', 'view') ? html::a(inlink('view', "testingrequestID=$item->id"), $item->code) : $item->code;?></td>
              <td><?php echo $item->giteeId;?></td>
              <td title="<?php echo $item->testSummary;?>"><?php echo $item->testSummary;?></td>
              <td title="<?php echo zget($lang->testingrequest->acceptanceTestTypeList, $item->acceptanceTestType);?>"><?php echo zget($lang->testingrequest->acceptanceTestTypeList, $item->acceptanceTestType);?></td>
              <td title="<?php echo $item->appName?>"><?php echo $item->appName;?></td>
              <td title="<?php echo $item->testProduct?>"><?php echo $item->testProduct;?></td>
              <td title="<?php echo $item->projectPlanId?>"><?php echo $item->projectPlanId;?></td>
              <td title="<?php echo $item->createdBy?$depts[$dmap[$item->createdBy]->dept]:''?>"><?php echo $item->createdBy?$depts[$dmap[$item->createdBy]->dept]:'';?></td>
              <td><?php echo $item->returnTimes;?></td>
              <td><?php echo zget($users,$item->createdBy,'');?></td>
              <td><?php echo $item->createdDate;?></td>
              <td title="<?php echo $item->closed == '1' ? $lang->testingrequest->labelList['closed'] :zget($lang->testingrequest->statusList, $item->status);?>" class="text-ellipsis" ><?php echo $item->closed == '1' ? $lang->testingrequest->labelList['closed'] :zget($lang->testingrequest->statusList, $item->status);?></td>
              <td title="<?php echo $item->outwarddeliveryCode;?>" class='text-ellipsis'><?php echo $item->outwarddeliveryId ? html::a($this->createLink('outwarddelivery','view', "outwarddeliveryID=$item->outwarddeliveryId"),$item->outwarddeliveryCode) : '' ;?></td>
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
