<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
 <?php
    $hiddenLabelList = [];
    $i = 0;
    foreach($lang->putproduction->labelList as $label => $labelName) {
        $active = $browseType == $label ? 'btn-active-text' : '';
        $i++;
        if($i < 11) {
            echo html::a($this->createLink('putproduction', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
            echo '<li>' . html::a($this->createLink('putproduction', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
            if(common::hasPriv('putproduction', 'export')){
                $class = "";
                $misc =  "data-toggle='modal' data-type='iframe' class='export'" ;
                $link =  $this->createLink('putproduction', 'export', "orderBy=$orderBy&browseType=$browseType");
            }
            echo "<li $class>" . html::a($link, $lang->putproduction->export, '', $misc) . "</li>";
        ?>
      </ul>
    </div>
    <?php if(common::hasPriv('putproduction', 'create')) echo html::a($this->createLink('putproduction', 'create'), "<i class='icon-plus'></i>", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='putproduction'></div>
    <?php if(empty($data)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' id='putproductionForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
        <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='putproduction'>
          <thead>
              <tr>
                <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->putproduction->code);?></th>
                <th class='w-110px'><?php echo $lang->putproduction->desc;?></th>
                <th class='w-80px'><?php common::printOrderLink('outsidePlanId', $orderBy, $vars, $lang->putproduction->outsidePlanId);?></th>
                <th class='w-80px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->putproduction->app);?></th>
                <th class='w-60px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->putproduction->level);?></th>
                <th class='w-80px'><?php common::printOrderLink('stage', $orderBy, $vars, $lang->putproduction->stage);?></th>
                <th class='w-60px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->putproduction->createdBy);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->putproduction->createdDate);?></th>
                <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->putproduction->status);?></th>
                <th class='w-100px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->putproduction->dealUser);?> </th>
                <th class='text-center w-160px'><?php echo $lang->actions;?></th>
              </tr>
          </thead>

          <tbody>
          <?php
            foreach ($data as $item):
                $levelInfo = zget($lang->putproduction->levelList, $item->level, '');
                $stageInfo = zmget($lang->putproduction->stageList, $item->stage, '');
                $outsidePlanInfo = zmget($outsideProjectList, $item->outsidePlanId, '');
                $appInfo = zmget($appList, $item->app, '');
                $dealUserInfo = zmget($users, $item->dealUser, '');
          ?>
            <tr data-val='<?php echo $item->id?>'>
                <td title="<?php echo $item->code; ?>">
                    <?php echo common::hasPriv('putproduction', 'view') ? html::a(inlink('view', "putproductionID=$item->id"), $item->code) : $item->code;?>
                </td>
                <td class='text-ellipsis viewClick' title="<?php echo strip_tags($item->desc); ?>"><?php echo strip_tags($item->desc);?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $outsidePlanInfo;?>"><?php echo $outsidePlanInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $appInfo;?>"><?php echo $appInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $levelInfo;?>"><?php echo $levelInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo $stageInfo; ?>"><?php echo $stageInfo;?></td>
                <td class='viewClick'><?php echo zget($users, $item->createdBy,'');?></td>
                <td class='viewClick'><?php echo $item->createdDate;?></td>
                <td class='viewClick'><?php echo zget($lang->putproduction->statusList, $item->status,'');?></td>
                <td class='viewClick' title="<?php echo $dealUserInfo;?>" class='text-ellipsis'><?php echo $dealUserInfo; ?></td>
                <td class='c-actions text-center'>
                <?php
                common::printIcon('putproduction', 'edit', "putproductionID=$item->id", $item, 'list');
                $submitClass = 'btn disabled';
                if((common::hasPriv('putproduction', 'submit') && common::isDealUser($item->dealUser, $app->user->account))|| $app->user->account == 'admin'){
                    $submitClass = 'btn';
                }
                common::printIcon('putproduction', 'submit', "putproductionID=$item->id", $item, 'list', 'play', '', 'iframe', true, " class = '{$submitClass}'");
                common::printIcon('putproduction', 'assignment', "putproductionID=$item->id", $item, 'list', 'hand-right', '', 'iframe', true);
                common::printIcon('putproduction', 'review', "putproductionID=$item->id", $item, 'list', 'glasses', '', 'iframe', true);
                common::printIcon('putproduction', 'copy', "putproductionID=$item->id", $item, 'list');
                common::printIcon('putproduction', 'delete', "putproductionID=$item->id", $item, 'list', 'trash', '', 'iframe', true);
//                common::printIcon('putproduction', 'cancel', "putproductionID=$item->id", $item, 'list', 'off', '', 'iframe', true);
                if (common::hasPriv('putproduction', 'cancel')) common::printIcon('putproduction', 'cancel', "putproductionID=$item->id", $item, 'list', 'cancel', '', 'iframe', true);
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
        window.location = createLink('putproduction', 'view', "putproductionID="+id)
    })
</script>
<?php include '../../common/view/footer.html.php';?>
