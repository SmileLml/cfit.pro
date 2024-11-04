<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
    $i=0;
    foreach($lang->modifycncc->labelList as $label => $labelName)
    {   
        $active = $browseType == $label ? 'btn-active-text' : ''; 
        echo html::a($this->createLink('modifycncc', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
        $i++;
        if($i >= 13) break;
      }
    if($i >= 13)   
    {
      echo "<div class='btn-group'><a href='javascript:;' data-toggle='dropdown' class='btn btn-link'>{$lang->more}<span class='caret'></span></a>";
      echo "<ul class='dropdown-menu'>";
      $i = 0;
      foreach($lang->modifycncc->labelList as $label => $labelName)
      {
          $i++;
          if($i <= 13) continue;

          $active = $browseType == $label ? 'btn-active-text' : '';
          echo '<li>' . html::a($this->createLink('modifycncc', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), "<span class='text'>{$labelName}</span>", '', "class='btn btn-link $active'") . '</li>';
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
      $class = common::hasPriv('modifycncc', 'export') ? '' : "class=disabled";
      $misc  = common::hasPriv('modifycncc', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";    
      $link  = common::hasPriv('modifycncc', 'export') ? $this->createLink('modifycncc', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
      echo "<li $class>" . html::a($link, $lang->modifycncc->export, '', $misc) . "</li>";
      ?>
      </ul>
    </div>
    <?php if(common::hasPriv('modifycncc', 'importpartition')) echo html::a($this->createLink('modifycncc', 'importpartition'), "<i class='icon-import'></i><span style='margin-left:5px'>导入分区<span>", '', "class='btn btn-warning' data-toggle='modal' data-type='iframe'");?>
  </div>
</div>
<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='modifycncc'></div>
    <?php if(empty($modifycnccs)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='modifycnccForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "browseType=$browseType&param=$param&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='modifycnccs'>
        <thead>
          <tr>
            <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->modifycncc->code);?></th>
            <th class='w-150px'><?php common::printOrderLink('desc', $orderBy, $vars, $lang->modifycncc->desc);?></th>
            <th class='w-120px'><?php common::printOrderLink('belongedApp', $orderBy, $vars, $lang->modifycncc->belongApp);?></th>
            <th class='w-80px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->modifycncc->project);?></th>
            <th class='w-60px'><?php common::printOrderLink('level', $orderBy, $vars, $lang->modifycncc->level);?></th>
            <th class='w-80px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->modifycncc->type);?></th>
            <th class='w-110px'><?php common::printOrderLink('planBegin', $orderBy, $vars, $lang->modifycncc->planBegin);?></th>
            <th class='w-110px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->modifycncc->planEnd);?></th>
            <th class='w-70px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->modifycncc->createdBy);?></th>
            <th class='w-70px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->modifycncc->createdDate);?></th>
            <th class='w-70px'><?php common::printOrderLink('returnTimes', $orderBy, $vars, $lang->modifycncc->returnTimes);?></th>
            <th class='w-90px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->modifycncc->createdDept);?></th>
            <th class='w-90px'><?php common::printOrderLink('giteeId', $orderBy, $vars, $lang->modifycncc->giteeId);?></th>
            <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->modifycncc->status);?></th>
            <th class='w-120px'><?php common::printOrderLink('outwarddeliveryCode', $orderBy, $vars, $lang->outwarddelivery->outwarddeliveryCode);?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($modifycnccs as $modifycncc):?>
          <tr>
            <td title="<?php echo $modifycncc->code;?>"><?php echo common::hasPriv('modifycncc', 'view') ? html::a(inlink('view', "modifycnccID=$modifycncc->id"), $modifycncc->code) : $modifycncc->code;?></td>
            <td class="text-ellipsis" title="<?php echo $modifycncc->desc;?>"><?php echo $modifycncc->desc;?></td>
            <!-- <?php
            $as = [];
            $modifycncc->app = substr($modifycncc->app,1,strlen($modifycncc->app)-2);
            foreach(explode(',', $modifycncc->app) as $app)
            {
                if(!$app) continue;
                $app = substr($app,1,strlen($app)-2);
                $app = explode('/', $app);
                $app[0] = trim($app[0],'"');
                $as[] = zget($apps, $app[0]);
            }
            $app = implode('，',array_unique($as));
            ?> -->
            <td title="<?php echo zget($appList,trim($modifycncc->belongedApp, ','));?>" class='text-ellipsis'><?php echo zget($appList,trim($modifycncc->belongedApp, ','));?></td>
            <td title="<?php echo zget($projectList,$modifycncc->project);?>"><?php echo zget($projectList,$modifycncc->project);?></td>
            <td title="<?php echo zget( $lang->modifycncc->levelList,$modifycncc->level);?>"><?php echo zget( $lang->modifycncc->levelList,$modifycncc->level);?></td>
            <td><?php echo zget($lang->modifycncc->typeList, $modifycncc->type);?></td>
            <td><?php echo $modifycncc->planBegin;?></td>
            <td><?php echo $modifycncc->planEnd;?></td>
            <td><?php echo zget($users, $modifycncc->createdBy, '');?></td>
            <td><?php echo date("Y-m-d",strtotime($modifycncc->createdDate));?></td>
            <td><?php echo $modifycncc->returnTimes;?></td>
            <td title="<?php echo zget($depts, $modifycncc->createdDept, '');?>" class='text-ellipsis'><?php echo zget($depts, $modifycncc->createdDept, '');?></td>
            <td title="<?php echo $modifycncc->giteeId;?>" class='text-ellipsis'><?php echo $modifycncc->giteeId;?></td>
            <td title="<?php echo $modifycncc->closed == '1' ? $lang->modifycncc->labelList['closed'] :zget($lang->modifycncc->statusList, $modifycncc->status);?>" class='text-ellipsis'><?php echo $modifycncc->closed == '1' ? $lang->modifycncc->labelList['closed'] :zget($lang->modifycncc->statusList, $modifycncc->status);?></td>
            <td title="<?php echo $modifycncc->outwarddeliveryCode;?>" class='text-ellipsis'><?php echo $modifycncc->outwarddeliveryId ? html::a($this->createLink('outwarddelivery','view', "outwarddeliveryID=$modifycncc->outwarddeliveryId"),$modifycncc->outwarddeliveryCode) : '' ;?></td>
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
