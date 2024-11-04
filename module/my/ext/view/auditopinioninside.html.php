<?php include '../../../common/view/header.html.php'?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php foreach($lang->my->myReviewList as $key => $type):?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php endforeach;?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class='main-col'>
    <?php if(empty($reviewList) && empty($reviewListIgnore)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' method='post' id='myReviewForm'>
      <div class="table-header fixed-right">
        <nav class="btn-toolbar pull-right"></nav>
      </div>
      <?php
      $vars = "mode=$mode&browseType=$browseType&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID";
      ?>
        <table class='table has-sort-head' id='reviewList'>
          <thead>
            <tr>
              <th class='c-id w-150px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->opinioninside->code);?></th>
                <th class='w-250px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->opinioninside->name);?></th>
                <th class='w-180px'><?php common::printOrderLink('union', $orderBy, $vars, $lang->opinioninside->union);?></th>
                <th class='w-150px'><?php common::printOrderLink('sourceMode',  $orderBy, $vars, $lang->opinioninside->sourceMode);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('date',        $orderBy, $vars, $lang->opinioninside->date);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('deadline',    $orderBy, $vars, $lang->opinioninside->deadlineAB);?></th>
                <th class='c-date w-140px'><?php common::printOrderLink('onlineTimeByDemand', $orderBy, $vars, $lang->opinioninside->onlineTimeByDemand);?></th>
                <th class='w-100px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->opinioninside->createdBy);?></th>
                <th class='w-90px'><?php  common::printOrderLink('status',      $orderBy, $vars, $lang->opinioninside->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser',  $orderBy, $vars, $lang->opinioninside->dealUser);?></th>
              <th class='w-250px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($reviewList as $opinion):?>
              <tr>
                  <td><?php echo $opinion->code;?></td>
                  <td class="text-ellipsis" title="<?php echo $opinion->name;?>">
                      <?php
                      echo common::hasPriv('opinioninside', 'view') ? html::a($this->createLink('opinioninside', 'view', "opinionID=$opinion->id"), $opinion->name) : $opinion->name;
                      ?>
                  </td>
                  <td <?php
                  $text = '';
                  $unions = explode(',',$opinion->union);
                  foreach ($unions as $union)
                  {
                      $text .= zget($lang->opinioninside->unionList, $union, '') .'&nbsp;';
                  }
                  ?>
                      class="text-ellipsis" title=<?php echo $text;?>><?php echo $text;?></td>
                  <td><?php echo zget($lang->opinioninside->sourceModeListOld, $opinion->sourceMode, '');?></td>
                  <td><?php echo $opinion->date;?></td>
                  <td><?php echo $opinion->deadline;?></td>
                  <td><?php echo $opinion->status == 'online' ? substr($opinion->onlineTimeByDemand,0,10):'';?></td>
                  <td><?php echo zget($users, $opinion->createdBy, $opinion->createdBy);?></td>
                  <td><?php echo zget($lang->opinioninside->statusList, $opinion->status, '');?></td>
                  <td><?php echo zget($users, $opinion->dealUser, $opinion->dealUser);?></td>
                  <td class='c-actions'>
                      <?php
                      common::printIcon('opinioninside', 'subdivide', "opinionID=$opinion->id", $opinion, 'list', 'split', '');
                      common::printIcon('opinioninside', 'edit', "opinionID=$opinion->id", $opinion, 'list','edit', '');
                      common::printIcon('opinioninside', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe',true);
                      common::printIcon('opinioninside', 'assignment', "opinionID=$opinion->id", $opinion, 'list', 'hand-right', '', 'iframe', true);
                      common::printIcon('opinioninside', 'review', "opinionID=$opinion->id", $opinion, 'list','', '', 'iframe', true);
                      ?>

                      <?php
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
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'></div>
        <?php if($reviewListIgnore) { ?>
        <div style="padding: 10px 0 5px 10px">已忽略</div>
        <table class='table has-sort-head' id='reviewList'>
            <thead>
            <tr>
                <th class='c-id w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->opinioninside->code);?></th>
                <th class='w-250px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->opinioninside->name);?></th>
                <th class='w-180px'><?php common::printOrderLink('union', $orderBy, $vars, $lang->opinioninside->union);?></th>
                <th class='w-150px'><?php common::printOrderLink('sourceMode',  $orderBy, $vars, $lang->opinioninside->sourceMode);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('date',        $orderBy, $vars, $lang->opinioninside->date);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('deadline',    $orderBy, $vars, $lang->opinioninside->deadlineAB);?></th>
                <th class='c-date w-140px'><?php common::printOrderLink('onlineTimeByDemand', $orderBy, $vars, $lang->opinioninside->onlineTimeByDemand);?></th>
                <th class='w-100px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->opinioninside->createdBy);?></th>
                <th class='w-90px'><?php  common::printOrderLink('status',      $orderBy, $vars, $lang->opinioninside->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser',  $orderBy, $vars, $lang->opinioninside->dealUser);?></th>
                <th class='w-250px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewListIgnore as $opinion):?>
                <tr>
                    <td><?php echo $opinion->code;?></td>
                    <td class="text-ellipsis" title="<?php echo $opinion->name;?>">
                        <?php
                        echo common::hasPriv('opinioninside', 'view') ? html::a($this->createLink('opinioninside', 'view', "opinionID=$opinion->id"), $opinion->name) : $opinion->name;
                        ?>
                    </td>
                    <td <?php
                    $text = '';
                    $unions = explode(',',$opinion->union);
                    foreach ($unions as $union)
                    {
                        $text .= zget($lang->opinioninside->unionList, $union, '') .'&nbsp;';
                    }
                    ?>
                            class="text-ellipsis" title=<?php echo $text;?>><?php echo $text;?></td>
                    <td><?php echo zget($lang->opinioninside->sourceModeList, $opinion->sourceMode, '');?></td>
                    <td><?php echo $opinion->date;?></td>
                    <td><?php echo $opinion->deadline;?></td>
                    <td><?php echo $opinion->status == 'online' ? substr($opinion->onlineTimeByDemand,0,10):'';?></td>
                    <td><?php echo zget($users, $opinion->createdBy, $opinion->createdBy);?></td>
                    <td><?php echo zget($lang->opinioninside->statusList, $opinion->status, '');?></td>
                    <td><?php echo zget($users, $opinion->dealUser, $opinion->dealUser);?></td>
                    <td class='c-actions'>
                        <?php
                        common::printIcon('opinioninside', 'subdivide', "opinionID=$opinion->id", $opinion, 'list', 'split', '');
                        common::printIcon('opinioninside', 'edit', "opinionID=$opinion->id", $opinion, 'list','edit', '');
                        common::printIcon('opinioninside', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe',true);
                        common::printIcon('opinioninside', 'assignment', "opinionID=$opinion->id", $opinion, 'list', 'hand-right', '', 'iframe', true);
                        common::printIcon('opinioninside', 'review', "opinionID=$opinion->id", $opinion, 'list','', '', 'iframe', true);
                        ?>

                        <?php
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
            <?php endforeach;?>
            </tbody>
        </table>
        <div class='table-footer'></div>
        <?php } ?>
    </form>

    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
