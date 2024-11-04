<?php include '../../common/view/header.html.php';?>
<style>
.w-220px{
  width: 220px;
}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php 
      foreach($lang->opinioninside->labelList as $label => $labelName)
      {   
          $active = $browseType == $label ? 'btn-active-text' : '';
          if($label == "|"){
              echo html::a($this->createLink('opinioninside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
          }else{
              $lang->opinioninside->labelList['|'];
              echo html::a($this->createLink('opinioninside', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
          }
      }
    ?>
    <a class="btn btn-link querybox-toggle" id='bysearchTab'><i class="icon icon-search muted"></i> <?php echo $lang->searchAB;?></a>
  </div>
  <div class="btn-toolbar pull-right">
    <div class='btn-group'>
      <button class="btn btn-link" data-toggle="dropdown"><i class="icon icon-export muted"></i> <span class="text"><?php echo $lang->export ?></span> <span class="caret"></span></button>
      <ul class="dropdown-menu" id='exportActionMenu'>
        <?php
        $class = common::hasPriv('opinioninside', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('opinioninside', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link  = common::hasPriv('opinioninside', 'export') ? $this->createLink('opinioninside', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->opinioninside->export, '', $misc) . "</li>";

        $class = common::hasPriv('opinioninside', 'exportTemplate') ? '' : "class='disabled'";
        $link  = common::hasPriv('opinioninside', 'exportTemplate') ? $this->createLink('opinioninside', 'exportTemplate') : '#';
        $misc  = common::hasPriv('opinioninside', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
        echo "<li $class>" . html::a($link, $lang->opinioninside->exportTemplate, '', $misc) . '</li>';
        ?>  
      </ul>
      <?php if(common::hasPriv('opinioninside', 'import')) echo html::a($this->createLink('opinioninside', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->opinioninside->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
    </div>
    <?php if(common::hasPriv('opinioninside', 'create')) echo html::a($this->createLink('opinioninside', 'create'), "<i class='icon-plus'></i> {$lang->opinioninside->create}", '', "class='btn btn-primary'");?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='opinioninside'></div>
    <?php if(empty($opinions)):?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->noData;?></span></p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='opinionForm'>
      <?php $vars = "browseType=$browseType&param=0&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID"; ?>
      <table class='table has-sort-head' id='opinionList'>
        <thead>
          <tr>
            <th class='c-id w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->opinioninside->code);?></th>
            <th class='w-150px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->opinioninside->name);?></th>
            <th class='w-150px'><?php common::printOrderLink('union', $orderBy, $vars, $lang->opinioninside->union);?></th>
            <th class='w-130px'><?php common::printOrderLink('sourceMode',  $orderBy, $vars, $lang->opinioninside->sourceMode);?></th>
            <th class='c-date'><?php  common::printOrderLink('date',        $orderBy, $vars, $lang->opinioninside->date);?></th>
            <th class='c-date'><?php  common::printOrderLink('deadline',    $orderBy, $vars, $lang->opinioninside->deadlineAB);?></th>
            <th class='c-date w-140px'><?php common::printOrderLink('onlineTimeByDemand', $orderBy, $vars, $lang->opinioninside->onlineTimeByDemand);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->opinioninside->createdBy);?></th>
            <th class='w-90px'><?php  common::printOrderLink('status',      $orderBy, $vars, $lang->opinioninside->status);?></th>
            <th class='w-100px'><?php common::printOrderLink('dealUser',  $orderBy, $vars, $lang->opinioninside->dealUser);?></th>
            <th class='w-220px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($opinions as $opinion):?>
          <tr>
            <td><?php echo $opinion->code;?></td>
            <td class="text-ellipsis <?php if(!empty($opinion->children)) echo 'has-child';?>" title="<?php echo $opinion->name;?>">
            <?php
            echo '<span class="table-nest-child-hide table-nest-icon icon table-nest-toggle collapsed" data-id="' . $opinion->id . '"></span>';
            echo common::hasPriv('opinioninside', 'view') ? html::a(inlink('view', "opinionID=$opinion->id"), $opinion->name) : $opinion->name;
            ?>
            </td>
            <td <?php
                $text = '';
                $unions = explode(',',$opinion->union);
                foreach ($unions as $union)
                {
                    $text .= zget($lang->opinion->unionList, $union, '') .'&nbsp;';
                }
                ?>
                    class="text-ellipsis" title=<?php echo $text;?>><?php echo $text;?></td>
            <td><?php echo zget($lang->opinioninside->sourceModeListOld, $opinion->sourceMode, '');?></td>
            <td><?php echo $opinion->date;?></td>
            <td><?php echo $opinion->deadline;?></td>
<!--            <td>--><?php //echo $opinion->status == 'online' ? substr($opinion->onlineTimeByDemand,0,10):'';?><!--</td>-->
            <td><?php echo $opinion->status == 'online' ? $opinion->onlineTimeByDemand:'';?></td>
            <td><?php echo zget($users, $opinion->createdBy, $opinion->createdBy);?></td>
            <td><?php echo zget($lang->opinioninside->statusList, $opinion->status, '');?></td>
            <td title="<?php echo zget($users, $opinion->dealUser, $opinion->dealUser);?>" class="text-ellipsis"><?php echo zget($users, $opinion->dealUser, $opinion->dealUser);?></td>
            <td class='c-actions'>
            <?php
            common::printIcon('opinioninside', 'subdivide', "opinionID=$opinion->id", $opinion, 'list', 'split', '');
            common::printIcon('opinioninside', 'edit', "opinionID=$opinion->id", $opinion, 'list','edit', '');
            common::printIcon('opinioninside', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe',true);
            common::printIcon('opinioninside', 'assignment', "opinionID=$opinion->id", $opinion, 'list', 'hand-right', '', 'iframe', true);
            common::printIcon('opinioninside', 'review', "opinionID=$opinion->id", $opinion, 'list','', '', 'iframe', true);

            if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executivesOpinion) or $this->app->user->account == $opinion->closedBy or $this->app->user->account == $opinion->createdBy) {
                if ($opinion->status == 'closed') {
                    common::printIcon('opinioninside', 'reset',"opinionID=$opinion->id", $opinion, 'list', 'magic', '', 'iframe', true);
                } else {
                    common::printIcon('opinioninside', 'close', "opinionID=$opinion->id", $opinion, 'list', 'pause', '', 'iframe', true);
                }
            }else if($opinion->status == 'closed'){
                echo '<button type="button" class="disabled btn" title="' . $lang->opinioninside->reset . '"><i class="icon-common-start disabled icon-magic"></i></button>';
            }else{
                echo '<button type="button" class="disabled btn" title="' . $lang->opinioninside->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
            }

            common::printIcon('opinioninside', 'delete', "opinionID=$opinion->id", $opinion, 'list', 'trash', '', 'iframe',true);
            if ($opinion->ignore) {
                common::printIcon('opinioninside', 'recoveryed', "opinionID=$opinion->id", $opinion, 'list', 'bell', '', 'iframe', true);
            } else {
                common::printIcon('opinioninside', 'ignore', "opinionID=$opinion->id", $opinion, 'list', 'ban', '', 'iframe', true);
            }
            ?>
            </td>
          </tr>
          <?php if(!empty($opinion->children)):?>
          <?php $i = 0;?>
          <?php foreach($opinion->children as $key => $requirement):?>
          <?php $class  = $i == 0 ? ' table-child-top' : '';?>
          <?php $class .= ($i + 1 == count($opinion->children)) ? ' table-child-bottom' : '';?>
          <tr class='table-children<?php echo $class;?> parent-<?php echo $opinion->id;?>' data-id='<?php echo $requirement->id?>' data-status='<?php echo $requirement->status?>' style="display: none;">
            <td><?php echo $requirement->code;?></td>
            <td class="child text-ellipsis" title="<?php echo htmlspecialchars_decode($requirement->name);?>">
            <?php 
            echo common::hasPriv('requirementinside', 'view') ? html::a(helper::createLink('requirementinside', 'view', "requirementID=$requirement->id"), htmlspecialchars_decode($requirement->name)) : htmlspecialchars_decode($requirement->name);
            ?>
            </td>
            <td></td>
            <td class="text-ellipsis" title="<?php echo htmlspecialchars_decode(zget($lang->opinioninside->sourceModeListOld, $requirement->sourceMode, ''));?>"><?php echo zget($lang->opinioninside->sourceModeListOld, $requirement->sourceMode, '');?></td>
            <td class="text-ellipsis" title="<?php echo htmlspecialchars_decode($requirement->createdDate);?>"><?php echo $requirement->createdDate;?></td>
            <td class="text-ellipsis"><?php echo $requirement->deadLine != '0000-00-00' ? $requirement->deadLine : '';?></td>
            <td></td>
            <td><?php echo zget($users, $requirement->createdBy, $requirement->createdBy);?></td>
            <td><?php echo zget($lang->requirementinside->statusList, $requirement->status, '');?></td>
            <td><?php echo zmget($users, trim($requirement->dealUser,','), $requirement->dealUser);?></td>
            <td class='c-actions'>
                <?php
                common::printIcon('requirementinside', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                common::printIcon('requirementinside', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                common::printIcon('requirementinside', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                common::printIcon('requirementinside', 'feedback', "requirementID=$requirement->id", $requirement, 'list');
                common::printIcon('requirementinside', 'review', "requirementID=$requirement->id", $requirement, 'list', 'glasses', '', 'iframe', true);
                if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy) {
                    if ($requirement->status == 'closed') {
                        common::printIcon('requirementinside', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                    } else {
                        common::printIcon('requirementinside', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                    }
                }else if($requirement->status == 'closed'){
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirementinside->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                }

                if ($requirement->ignoreStatus) {
                    common::printIcon('requirementinside', 'recover', "requirementID=$requirement->id", $requirement, 'list', 'bell', '', 'iframe', true);
                } else {
                    common::printIcon('requirementinside', 'ignore', "requirementID=$requirement->id", $requirement, 'list', 'ban', '', 'iframe', true);
                }
                common::printIcon('requirementinside', 'delete', "requirementID=$requirement->id", $requirement, 'list', 'trash', '', 'iframe', true);

                ?>
            </td>
          </tr>
          <?php $i ++;?>
          <?php endforeach;?>
          <?php endif;?>
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
// $('#opinionForm').table();
</script>
<?php include '../../common/view/footer.html.php';?>
