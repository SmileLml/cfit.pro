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
              <th class='c-id w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->opinion->code);?></th>
                <th class='w-250px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->opinion->name);?></th>
                <th class='w-180px'><?php common::printOrderLink('union', $orderBy, $vars, $lang->opinion->union);?></th>
                <th class='w-150px'><?php common::printOrderLink('sourceMode',  $orderBy, $vars, $lang->opinion->sourceMode);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('date',        $orderBy, $vars, $lang->opinion->date);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('deadline',    $orderBy, $vars, $lang->opinion->deadlineAB);?></th>
                <th class='c-date w-140px'><?php common::printOrderLink('onlineTimeByDemand', $orderBy, $vars, $lang->opinion->onlineTimeByDemand);?></th>
                <th class='w-100px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->opinion->createdBy);?></th>
                <th class='w-90px'><?php  common::printOrderLink('status',      $orderBy, $vars, $lang->opinion->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser',  $orderBy, $vars, $lang->opinion->dealUser);?></th>
              <th class='w-250px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($reviewList as $opinion):?>
              <tr>
                  <td><?php echo $opinion->code;?></td>
                  <td class="text-ellipsis" title="<?php echo $opinion->name;?>">
                      <?php
                      echo common::hasPriv('opinion', 'view') ? html::a($this->createLink('opinion', 'view', "opinionID=$opinion->id"), $opinion->name) : $opinion->name;
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
                  <td><?php echo zget($lang->opinion->sourceModeListOld, $opinion->sourceMode, '');?></td>
                  <td><?php echo $opinion->date;?></td>
                  <td><?php echo $opinion->deadline;?></td>
                  <td><?php echo $opinion->status == 'online' ? substr($opinion->onlineTimeByDemand,0,10):'';?></td>
                  <td><?php echo zget($users, $opinion->createdBy, $opinion->createdBy);?></td>
                  <td><?php echo zget($lang->opinion->statusList, $opinion->status, '');?></td>
                  <?php
                  //迭代二十八 待处理人拼接变更单待处理人共同显示  放到页面单独定义不影响权限
                  if(in_array($opinion->status,['delivery','online'])){
                      if(empty($opinion->changeDealUser)){
                          $dealUser = '';
                      }else{
                          $dealUser = $opinion->changeDealUser;
                      }
                  }else{
                      $opinionDealUser = explode(',',$opinion->dealUser);
                      $opinionChangeDealUser = explode(',',$opinion->changeDealUser);
                      $finalDealUser = array_merge($opinionDealUser,$opinionChangeDealUser);
                      $dealUser = implode(',',array_unique(array_filter($finalDealUser)));
                  }
                  ?>
                  <td title="<?php echo zmget($users, $dealUser, $dealUser);?>" class="text-ellipsis"><?php echo zmget($users, $dealUser, $dealUser);?></td>
                  <td class='c-actions' style="overflow:visible">
                      <?php
                      common::printIcon('opinion', 'subdivide', "opinionID=$opinion->id", $opinion, 'list', 'split', '');
                      common::printIcon('opinion', 'edit', "opinionID=$opinion->id", $opinion, 'list','edit', '');
//                      common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe',true);
                      //研发责任人取所有需求条目合集  //迭代三十二 所有人可发起变更
                      //if((in_array($this->app->user->account,explode(',',$opinion->acceptUser)) and !in_array($opinion->opinionChangeStatus,[2,3])) or $this->app->user->account == 'admin')
                      if(!in_array($opinion->opinionChangeStatus,[2,3]))
                      {
                          common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe width:90%',true);
                      }else{
                          echo '<button type="button" class="disabled btn" title="' . $lang->opinion->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                      }
                      common::printIcon('opinion', 'assignment', "opinionID=$opinion->id", $opinion, 'list', 'hand-right', '', 'iframe', true);
//                      common::printIcon('opinion', 'review', "opinionID=$opinion->id", $opinion, 'list','', '', 'iframe', true);
                      ?>
                      <?php if($this->app->user->account != 'admin' and $opinion->demandCode):?>
                          <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->opinion->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                      <?php elseif($this->app->user->account != 'admin' and (($this->app->user->account != $opinion->dealUser and in_array($opinion->status,array('created')) or (!in_array($opinion->status,array('created')))) and (strstr($opinion->changeNextDealuser, $app->user->account) == false))):?>
                          <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->opinion->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                      <?php else:?>
                          <div class="btn-group">
                              <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                              <ul class="dropdown-menu">
                                  <?php if($this->app->user->account == 'admin' or (in_array($opinion->status,array('created')) and $this->app->user->account == $opinion->dealUser and !$opinion->demandCode)): ?>
                                      <li><?php echo html::a($this->createLink('opinion', 'review', 'opinionID=' . $opinion->id , '', true), $lang->opinion->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                  <?php else:?>
                                      <li style="margin-top:-10px;margin-left: 10px"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->opinion->review; ?></span></li>
                                  <?php endif;?>
                                  <?php if(!empty($this->app->user->account == 'admin' or (strstr($opinion->changeNextDealuser, $app->user->account) !== false))):?>
                                      <li><?php echo html::a($this->createLink('opinion', 'reviewchange', 'opinionID=' . $opinion->id , '', true), $lang->opinion->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                  <?php else:?>
                                      <li style="margin-top:-10px;margin-left: 10px"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->opinion->reviewchange; ?></span></li>
                                  <?php endif;?>
                              </ul>
                          </div>
                      <?php endif;?>

                      <?php
                      if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executivesOpinion) or $this->app->user->account == $opinion->closedBy or $this->app->user->account == $opinion->createdBy) {
                          if ($opinion->status == 'closed') {
                              common::printIcon('opinion', 'reset',"opinionID=$opinion->id", $opinion, 'list', 'magic', '', 'iframe', true);
                          } else {
                              common::printIcon('opinion', 'close', "opinionID=$opinion->id", $opinion, 'list', 'pause', '', 'iframe', true);
                          }
                      }else if($opinion->status == 'closed'){
                          echo '<button type="button" class="disabled btn" title="' . $lang->opinion->reset . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                      }else{
                          echo '<button type="button" class="disabled btn" title="' . $lang->opinion->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                      }

                      common::printIcon('opinion', 'delete', "opinionID=$opinion->id", $opinion, 'list', 'trash', '', 'iframe',true);
                      if ($opinion->ignore) {
                          common::printIcon('opinion', 'recoveryed', "opinionID=$opinion->id", $opinion, 'list', 'bell', '', 'iframe', true);
                      } else {
                          common::printIcon('opinion', 'ignore', "opinionID=$opinion->id", $opinion, 'list', 'ban', '', 'iframe', true);
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
                <th class='c-id w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->opinion->code);?></th>
                <th class='w-250px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->opinion->name);?></th>
                <th class='w-180px'><?php common::printOrderLink('union', $orderBy, $vars, $lang->opinion->union);?></th>
                <th class='w-150px'><?php common::printOrderLink('sourceMode',  $orderBy, $vars, $lang->opinion->sourceMode);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('date',        $orderBy, $vars, $lang->opinion->date);?></th>
                <th class='c-date' style="padding-right: 50px"><?php  common::printOrderLink('deadline',    $orderBy, $vars, $lang->opinion->deadlineAB);?></th>
                <th class='c-date w-140px'><?php common::printOrderLink('onlineTimeByDemand', $orderBy, $vars, $lang->opinion->onlineTimeByDemand);?></th>
                <th class='w-100px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->opinion->createdBy);?></th>
                <th class='w-90px'><?php  common::printOrderLink('status',      $orderBy, $vars, $lang->opinion->status);?></th>
                <th class='w-80px'><?php common::printOrderLink('dealUser',  $orderBy, $vars, $lang->opinion->dealUser);?></th>
                <th class='w-250px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewListIgnore as $opinion):?>
                <tr>
                    <td><?php echo $opinion->code;?></td>
                    <td class="text-ellipsis" title="<?php echo $opinion->name;?>">
                        <?php
                        echo common::hasPriv('opinion', 'view') ? html::a($this->createLink('opinion', 'view', "opinionID=$opinion->id"), $opinion->name) : $opinion->name;
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
                    <td><?php echo zget($lang->opinion->sourceModeList, $opinion->sourceMode, '');?></td>
                    <td><?php echo $opinion->date;?></td>
                    <td><?php echo $opinion->deadline;?></td>
                    <td><?php echo $opinion->status == 'online' ? substr($opinion->onlineTimeByDemand,0,10):'';?></td>
                    <td><?php echo zget($users, $opinion->createdBy, $opinion->createdBy);?></td>
                    <td><?php echo zget($lang->opinion->statusList, $opinion->status, '');?></td>
                    <td><?php echo zget($users, $opinion->dealUser, $opinion->dealUser);?></td>
                    <td class='c-actions'>
                        <?php
                        common::printIcon('opinion', 'subdivide', "opinionID=$opinion->id", $opinion, 'list', 'split', '');
                        common::printIcon('opinion', 'edit', "opinionID=$opinion->id", $opinion, 'list','edit', '');
                        common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe',true);
                        //研发责任人取所有需求条目合集
                        //                if((in_array($this->app->user->account,explode(',',$opinion->acceptUser)) and !in_array($opinion->opinionChangeStatus,[2,3])) or $this->app->user->account == 'admin')
                        //                {
                        //                    common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe width:90%',true);
                        //                }else{
                        //                    echo '<button type="button" class="disabled btn" title="' . $lang->opinion->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                        //                }
                        common::printIcon('opinion', 'assignment', "opinionID=$opinion->id", $opinion, 'list', 'hand-right', '', 'iframe', true);
                        common::printIcon('opinion', 'review', "opinionID=$opinion->id", $opinion, 'list','', '', 'iframe', true);
                        ?>
                        <!--            --><?php //if($this->app->user->account != 'admin' and $opinion->demandCode):?>
                        <!--                --><?php //echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->opinion->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                        <!--            --><?php //elseif($this->app->user->account != 'admin' and (($this->app->user->account != $opinion->dealUser and in_array($opinion->status,array('created')) or (!in_array($opinion->status,array('created')))) and (strstr($opinion->changeNextDealuser, $app->user->account) == false))):?>
                        <!--                --><?php //echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->opinion->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                        <!--            --><?php //else:?>
                        <!--                <div class="btn-group">-->
                        <!--                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>-->
                        <!--                    <ul class="dropdown-menu">-->
                        <!--                        --><?php //if($this->app->user->account == 'admin' or (in_array($opinion->status,array('created')) and $this->app->user->account == $opinion->dealUser and !$opinion->demandCode)): ?>
                        <!--                            <li>--><?php //echo html::a($this->createLink('opinion', 'review', 'opinionID=' . $opinion->id , '', true), $lang->opinion->review , '', "data-toggle='modal' data-type='iframe' ") ?><!--</li>-->
                        <!--                        --><?php //else:?>
                        <!--                            <li style="margin-top:-10px;margin-left: 10px"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4">--><?php //echo $lang->opinion->review; ?><!--</span></li>-->
                        <!--                        --><?php //endif;?>
                        <!--                        --><?php //if(!empty($this->app->user->account == 'admin' or (strstr($opinion->changeNextDealuser, $app->user->account) !== false))):?>
                        <!--                            <li>--><?php //echo html::a($this->createLink('opinion', 'reviewchange', 'opinionID=' . $opinion->id , '', true), $lang->opinion->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?><!--</li>-->
                        <!--                        --><?php //else:?>
                        <!--                            <li style="margin-top:-10px;margin-left: 10px"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4">--><?php //echo $lang->opinion->reviewchange; ?><!--</span></li>-->
                        <!--                        --><?php //endif;?>
                        <!--                    </ul>-->
                        <!--                </div>-->
                        <!--            --><?php //endif;?>

                        <?php
                        if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executivesOpinion) or $this->app->user->account == $opinion->closedBy or $this->app->user->account == $opinion->createdBy) {
                            if ($opinion->status == 'closed') {
                                common::printIcon('opinion', 'reset',"opinionID=$opinion->id", $opinion, 'list', 'magic', '', 'iframe', true);
                            } else {
                                common::printIcon('opinion', 'close', "opinionID=$opinion->id", $opinion, 'list', 'pause', '', 'iframe', true);
                            }
                        }else if($opinion->status == 'closed'){
                            echo '<button type="button" class="disabled btn" title="' . $lang->opinion->reset . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->opinion->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
                        }

                        common::printIcon('opinion', 'delete', "opinionID=$opinion->id", $opinion, 'list', 'trash', '', 'iframe',true);
                        if ($opinion->ignore) {
                            common::printIcon('opinion', 'recoveryed', "opinionID=$opinion->id", $opinion, 'list', 'bell', '', 'iframe', true);
                        } else {
                            common::printIcon('opinion', 'ignore', "opinionID=$opinion->id", $opinion, 'list', 'ban', '', 'iframe', true);
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
