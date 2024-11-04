<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datatable.fix.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
 <?php
    $hiddenLabelList = [];
    $i = 0;
    foreach($lang->cmdbsync->labelList as $label => $labelName) {
        $active = $browseType == $label ? 'btn-active-text' : '';
        $i++;
        if($i < 11) {
            echo html::a($this->createLink('cmdbsync', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
            echo '<li>' . html::a($this->createLink('cmdbsync', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
            if(common::hasPriv('cmdbsync', 'export')){
                $class = "";
                $misc =  "data-toggle='modal' data-type='iframe' class='export'" ;
                $link =  $this->createLink('cmdbsync', 'export', "orderBy=$orderBy&browseType=$browseType");
            }
            echo "<li $class>" . html::a($link, $lang->cmdbsync->export, '', $misc) . "</li>";
        ?>
      </ul>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='cmdbsync'></div>
    <?php if(empty($data)):?>
      <div class="table-empty-tip">
        <p>
          <span class="text-muted"><?php echo $lang->noData;?></span>
        </p>
      </div>
    <?php else:?>
      <form class='main-table' id='cmdbsyncForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
        <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
        <table class='table table-fixed has-sort-head' id='cmdbsync'>
          <thead>
              <tr>
                <th class='w-70px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->cmdbsync->id);?></th>
                <th class='w-260px'><?php echo $lang->cmdbsync->app;?></th>
                <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->cmdbsync->type);?></th>
                <th class='w-120px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->cmdbsync->status);?></th>
                <th class='w-160px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->cmdbsync->createdDate);?></th>
                  <th class='w-160px'><?php common::printOrderLink('sendStatus', $orderBy, $vars, $lang->cmdbsync->sendStatus);?></th>
                <th class='w-160px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->cmdbsync->dealUser);?></th>
                <th class='text-center w-80px'><?php echo $lang->actions;?></th>
              </tr>
          </thead>

          <tbody>
          <?php
            foreach ($data as $item):
                $typeInfo = zget($lang->cmdbsync->typeList, $item->type, '');
                $statusInfo = zget($lang->cmdbsync->statusList, $item->status, '');
                $appInfo = zmget($appList, $item->app, '');
                $dealUserInfo = zmget($users, $item->dealUser, '');
                $sendStatusInfo = zget($lang->cmdbsync->sendStatusList, $item->sendStatus, '');
          ?>
            <tr data-val='<?php echo $item->id?>'>
                <td title="<?php echo $item->id; ?>">
                    <?php echo common::hasPriv('cmdbsync', 'view') ? html::a(inlink('view', "cmdbsyncId=$item->id"), $item->id) : $item->id;?>
                </td>
                <td class='text-ellipsis viewClick' title="<?php echo $appInfo; ?>"><?php echo $appInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $typeInfo;?>"><?php echo $typeInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $statusInfo;?>"><?php echo $statusInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $item->createdDate;?>"><?php echo $item->createdDate;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo  $sendStatusInfo;?>"><?php echo $sendStatusInfo;?></td>
                <td class='text-ellipsis viewClick' title="<?php echo $dealUserInfo; ?>"><?php echo $dealUserInfo;?></td>
                <td class='c-actions text-center'>
                <?php
                common::printIcon('cmdbsync', 'deal', "id=$item->id", $item, 'list', 'time', '', 'iframe', true);
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
        window.location = createLink('cmdbsync', 'view', "cmdbsyncId="+id)
    })
</script>
<?php include '../../common/view/footer.html.php';?>
