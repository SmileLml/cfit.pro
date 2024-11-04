<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php 
    $i=0;
    foreach($lang->infoqz->labelList as $label => $labelName)
    {   
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('infoqz', 'gain', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        $i++;
        if($i >= 13) break;
      }
    if($i >= 13)   
    {
      echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
      echo "<ul class='dropdown-menu'>";
      $i = 0;
      foreach($lang->infoqz->labelList as $label => $labelName)
      {
          $i++;
          if($i <= 13) continue;

          $active = $browseType == $label ? 'btn-active-text' : '';
          echo '<li>' . html::a($this->createLink('infoqz', 'gain', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
      $class = common::hasPriv('infoqz', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('infoqz', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
      $link  = common::hasPriv('infoqz', 'export') ? $this->createLink('infoqz', 'export', "action=gain&orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->infoqz->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php if(common::hasPriv('infoqz', 'create')) echo html::a($this->createLink('infoqz', 'create', "action=gain"), "<i class='icon-plus'></i> {$lang->infoqz->gainApply}", '', "class='btn btn-primary'");?>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='infoqz'></div>
    <?php if(empty($infos)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='infoForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='infos'>
        <thead>
          <tr>
            <th class='w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->infoqz->code);?></th>
            <th class='w-70px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->infoqz->type);?></th>
            <th class='w-100px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->infoqz->app);?></th>
            <th class='w-110px'><?php common::printOrderLink('planBegin', $orderBy, $vars, $lang->infoqz->planBegin);?></th>
            <th class='w-110px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->infoqz->planEnd);?></th>
            <th class='w-70px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->infoqz->createdBy);?></th>
            <th class='w-70px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->infoqz->createdDept);?></th>
            <th class='w-110px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->infoqz->createdDate);?></th>
            <th class='w-70px'><?php common::printOrderLink('externalId', $orderBy, $vars, $lang->infoqz->externalId);?></th>
            <th class='w-70px'><?php common::printOrderLink('externalStatus', $orderBy, $vars, $lang->infoqz->externalStatus);?></th>
            <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->infoqz->status);?></th>
            <th class='w-70px'> <?php echo $lang->infoqz->dealUser;?></th>
            <th class='text-center w-160px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($infos as $info):?>
          <tr>
            <td><?php echo common::hasPriv('infoqz', 'view') ? html::a(inlink('view', "infoID=$info->id"), $info->code) : $info->code;?></td>
            <td><?php echo zget($lang->infoqz->typeList, $info->type);?></td>
            <?php
            $as = [];
            foreach(explode(',', $info->app) as $app)
            {
                if(!$app) continue;
                $as[] = zget($apps, $app);
            }
            $app = implode(', ', $as);
            ?>
            <td title="<?php echo $app;?>"><?php echo $app;?></td>
            <td><?php echo $info->planBegin;?></td>
            <td><?php echo $info->planEnd;?></td>
            <td><?php echo zget($users, $info->createdBy, '');?></td>
            <td><?php echo zget($depts, $info->createdDept, '');?></td>
            <td><?php echo substr($info->createdDate, 0, 20);?></td>
            <td><?php echo $info->externalId;?></td>
            <td><?php echo zget($lang->infoqz->externalStatusList, $info->externalStatus,'');?></td>
            <td><?php echo zget($lang->infoqz->statusList, $info->status);?></td>
              <?php
                  $dealUsersStr = '';
                  $dealUsersSubStr = '';
                  $dealUsers = $info->dealUsers;
                  if($dealUsers){
                      $dealUsersArray = explode(',', $dealUsers);
                      //所有审核人
                      $dealUsers    = getArrayValuesByKeys($users, $dealUsersArray);
                      $dealUsersStr = implode(',', $dealUsers);
                      $subCount = 3;
                      $dealUsersSubStr = getArraySubValuesStr($dealUsers, $subCount);
                  }
              ?>
            <td title="<?php echo $dealUsersStr; ?>">
                <?php echo $dealUsersSubStr ?>
            </td>
            <td class='c-actions text-center'>
              <?php
              common::printIcon('infoqz', 'edit', "infoID=$info->id", $info, 'list');
              common::printIcon('infoqz', 'link', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'list', 'link', '', 'iframe', true);
              common::printIcon('infoqz', 'review', "infoID=$info->id&version=$info->version&reviewStage=$info->reviewStage", $info, 'list', 'glasses', '', 'iframe', true);
              // common::printIcon('infoqz', 'run', "infoID=$info->id", $info, 'list', 'play', '', 'iframe', true);
              common::printIcon('infoqz', 'close', "infoID=$info->id", $info, 'list', 'off', '', 'iframe', true);
              common::printIcon('infoqz', 'copy', "infoID=$info->id&action=gain", $info, 'list');
              common::printIcon('infoqz', 'delete', "infoID=$info->id", $info, 'list', 'trash', '', 'iframe', true);
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
