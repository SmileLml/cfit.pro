<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
    <div class="btn-toolbar pull-left">
    <?php
    $i=0;
    foreach($lang->productenroll->labelList as $label => $labelName)
    {
      $active = $browseType == $label ? 'btn-active-text' : '';
      echo html::a($this->createLink('productenroll', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
      $i++;
      if($i >= 11) break;
    }
    if($i >= 11)   
    {
      echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
      echo "<ul class='dropdown-menu'>";
      $i = 0;
      foreach($lang->productenroll->labelList as $label => $labelName)
      {
          $i++;
          if($i <= 11) continue;

          $active = $browseType == $label ? 'btn-active-text' : '';
          echo '<li>' . html::a($this->createLink('productenroll', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
              $class = common::hasPriv('productenroll', 'export') ? '' : "class=disabled";
              $misc  = common::hasPriv('productenroll', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
              $link  = common::hasPriv('productenroll', 'export') ? $this->createLink('productenroll', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
              echo "<li $class>" . html::a($link, $lang->productenroll->export, '', $misc) . "</li>";
              ?>
            </ul>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row fade">
    <div class='main-col'>
      <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='productEnroll'></div>
      <?php if(empty($productenroll)):?>
          <div class="table-empty-tip">
              <p>
                  <span class="text-muted"><?php echo $lang->noData;?></span>
              </p>
          </div>
      <?php else:?>
        <form class='main-table' id='productenrollForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
            <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
            <table class='table table-fixed has-sort-head' id='productenroll'>
                <thead>
                    <tr>
                        <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->productenroll->code);?></th>
                        <th class='w-80px'><?php common::printOrderLink('emisRegisterNumber', $orderBy, $vars, $lang->productenroll->emisRegisterNumber);?></th>
                        <th class='w-120px'><?php common::printOrderLink('productenrollDesc', $orderBy, $vars, $lang->productenroll->productenrollDesc);?></th>
                        <th class='w-120px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->productenroll->app);?></th>
                        <th class='w-120px'><?php common::printOrderLink('productName', $orderBy, $vars, $lang->productenroll->productName);?></th>
                        <th class='w-120px'><?php common::printOrderLink('projectName', $orderBy, $vars, $lang->productenroll->projectName);?></th>
                        <th class='w-60px'><?php common::printOrderLink('versionNum', $orderBy, $vars, $lang->productenroll->versionNum);?></th>
                        <th class='w-60px'><?php common::printOrderLink('returnTimes', $orderBy, $vars, $lang->productenroll->returnTimes);?></th>
                        <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->productenroll->createdDepts);?></th>
                        <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->productenroll->createdBy);?></th>
                        <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->productenroll->createdDate);?></th>
                        <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->productenroll->status);?></th>
                        <th class='w-120px'><?php common::printOrderLink('outwarddeliveryCode', $orderBy, $vars, $lang->outwarddelivery->outwarddeliveryCode);?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productenroll as $item):?>
                        <tr>
                            <td><?php echo common::hasPriv('productenroll', 'view') ? html::a(inlink('view', "productenrollID=$item->id"), $item->code) : $item->code;?></td>
                            <td><?php echo $item->emisRegisterNumber;?></td>
                            <td title="<?php echo $item->productenrollDesc?>"><?php echo $item->productenrollDesc;?></td>
                            <td title="<?php echo zget($apps, trim($item->app,',') ,'')?>"><?php echo zget($apps, trim($item->app,','),'');?></td>
                            <td title="<?php echo $item->dynacommCn?>"><?php echo $item->dynacommCn;?></td>
                            <td title="<?php echo $item->projectPlanId?>"><?php echo $item->projectPlanId;?></td>
                            <td title="<?php echo $item->versionNum?>"><?php echo $item->versionNum;?></td>
                            <td><?php echo $item->returnTimes;?></td>
                            <td title="<?php echo $item->createdBy?$depts[$dmap[$item->createdBy]->dept]:''?>"><?php echo $item->createdBy?$depts[$dmap[$item->createdBy]->dept]:'';?></td>
                            <td><?php echo zget($users,$item->createdBy,'');?></td>
                            <td><?php echo $item->createdDate;?></td>
                            <td><?php echo $item->closed == '1' ? $lang->productenroll->labelList['closed'] :zget($lang->productenroll->statusList, $item->status);?></td>
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
