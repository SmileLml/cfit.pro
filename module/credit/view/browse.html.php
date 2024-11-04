<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
 <?php
    $hiddenLabelList = [];
    $i = 0;
    foreach($lang->credit->labelList as $label => $labelName) {
        $active = $browseType == $label ? 'btn-active-text' : '';
        $i++;
        if($i < 11) {
            echo html::a($this->createLink('credit', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        }else{
            $hiddenLabelList[$label] = $labelName;
        }
    }

    if(!empty($hiddenLabelList)){
        echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
        echo "<ul class='dropdown-menu'>";
        foreach($hiddenLabelList as $label => $labelName)
        {
            $active = $browseType == $label ? 'btn-active-text' : '';
            echo '<li>' . html::a($this->createLink('credit', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
            $class = "class=disabled";
            $misc  =  "class=disabled";
            $link  =  '#';
            if(common::hasPriv('credit', 'export')){
                $class = "";
                $misc =  "data-toggle='modal' data-type='iframe' class='export'" ;
                $link =  $this->createLink('credit', 'export', "orderBy=$orderBy&browseType=$browseType");
            }
            echo "<li $class>" . html::a($link, $lang->credit->export, '', $misc) . "</li>";
        ?>
      </ul>
    </div>
    <?php if(common::hasPriv('credit', 'create')) echo html::a($this->createLink('credit', 'create'), "<i class='icon-plus'></i>", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='credit'></div>
    <?php if(empty($data)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' id='creditForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
        <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='credit'>
          <thead>
              <tr>
                <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->credit->code);?></th>
                <th class='w-180px'><?php common::printOrderLink('summary', $orderBy, $vars, $lang->credit->summary);?></th>
                <th class='w-150px'><?php common::printOrderLink('appIds', $orderBy, $vars, $lang->credit->appIds);?></th>
                <th class='w-80px'><?php common::printOrderLink('projectPlanId', $orderBy, $vars, $lang->credit->projectPlanId);?></th>
                <th class='w-80px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->credit->level);?></th>
                <th class='w-100px'><?php common::printOrderLink('emergencyType', $orderBy, $vars, $lang->credit->emergencyType);?></th>
                <th class='w-120px'><?php common::printOrderLink('planBeginTime', $orderBy, $vars, $lang->credit->planBeginTime);?></th>
                <th class='w-120px'><?php common::printOrderLink('planEndTime', $orderBy, $vars, $lang->credit->planEndTime);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->credit->createdBy);?></th>
                <th class='w-120px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->credit->createdDate);?></th>
               <th class='w-80px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->credit->createdDept);?></th>
                <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->credit->status);?></th>
                <th class='w-100px'><?php common::printOrderLink('dealUsers', $orderBy, $vars, $lang->credit->dealUsers);?> </th>
                <th class='text-center w-160px'><?php echo $lang->actions;?></th>
              </tr>
          </thead>

          <tbody>
          <?php
            foreach ($data as $item):
                $levelInfo    = zget($lang->credit->levelList, $item->level, '');
                $appInfo      = zmget($appList, $item->appIds, '');
                $dealUserInfo = zmget($users, $item->dealUsers, '');
                $projectInfo  = zget($projectList, $item->projectPlanId, '');
          ?>
            <tr data-val='<?php echo $item->id?>'>
                <td title="<?php echo $item->code; ?>">
                    <?php echo common::hasPriv('credit', 'view') ? html::a(inlink('view', "creditId=$item->id"), $item->code) : $item->code;?>
                </td>
                <td class='text-ellipsis viewClick' title="<?php echo strip_tags($item->summary); ?>"><?php echo strip_tags($item->summary);?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $appInfo;?>"><?php echo $appInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo $projectInfo; ?>"><?php echo $projectInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $levelInfo;?>"><?php echo $levelInfo;?></td>
                <td class='viewClick'><?php echo zget($lang->credit->emergencyTypeList, $item->emergencyType,'');?></td>
                <td class='viewClick'><?php echo $item->planBeginTime;?></td>
                <td class='viewClick'><?php echo $item->planEndTime;?></td>
                <td class='viewClick'><?php echo zget($users, $item->createdBy,'');?></td>
                <td class='viewClick'><?php echo $item->createdDate;?></td>
                <td class='viewClick' title="<?php echo zget($deptList, $item->createdDept,''); ?>"><?php echo zget($deptList, $item->createdDept,'');?></td>
                <td class='viewClick' title="<?php echo zget($lang->credit->statusList, $item->status,'');?>"><?php echo zget($lang->credit->statusList, $item->status,'');?></td>
                <td class='viewClick' title="<?php echo $dealUserInfo;?>" class='text-ellipsis'><?php echo $dealUserInfo; ?></td>
                <td class='c-actions text-center'>
                <?php
                common::printIcon('credit', 'edit', "creditId=$item->id", $item, 'list');
                common::printIcon('credit', 'submit', "creditId=$item->id", $item, 'list', 'play', '', 'iframe', true);
                common::printIcon('credit', 'review', "creditId=$item->id", $item, 'list', 'glasses', '', 'iframe', true);
                common::printIcon('credit', 'copy', "creditId=$item->id", $item, 'list');
                common::printIcon('credit', 'cancel', "creditId=$item->id", $item, 'list', 'cancel', '', 'iframe', true);
                common::printIcon('credit', 'delete', "creditId=$item->id", $item, 'list', 'trash', '', 'iframe', true);
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
<script>
    $('.viewClick').live('click', function(){
        var id = $(this).parent().attr('data-val');
        window.location = createLink('credit', 'view', "creditId="+id)
    })
</script>
<?php include '../../common/view/footer.html.php';?>
