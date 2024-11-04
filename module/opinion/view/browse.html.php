<?php include '../../common/view/header.html.php';?>
<style>
.w-220px{
  width: 220px;
}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
      foreach($lang->opinion->labelList as $label => $labelName)
      {
          $active = $browseType == $label ? 'btn-active-text' : '';
          if($label == "|"){
              echo html::a($this->createLink('opinion', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active' style='font-size:20px;padding-top:3px;color:gray;pointer-events:none' @click=xx()");
          }else{
              $lang->opinion->labelList['|'];
              echo html::a($this->createLink('opinion', 'browse', "browseType=$label&param=0&orderBy=$orderBy&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
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
        $class = common::hasPriv('opinion', 'export') ? '' : "class=disabled";
        $misc  = common::hasPriv('opinion', 'export') ? "data-toggle='modal' data-type='iframe' class='export'" : "class=disabled";
        $link  = common::hasPriv('opinion', 'export') ? $this->createLink('opinion', 'export', "orderBy=$orderBy&browseType=$browseType") : '#';
        echo "<li $class>" . html::a($link, $lang->opinion->export, '', $misc) . "</li>";

        $class = common::hasPriv('opinion', 'exportTemplate') ? '' : "class='disabled'";
        $link  = common::hasPriv('opinion', 'exportTemplate') ? $this->createLink('opinion', 'exportTemplate') : '#';
        $misc  = common::hasPriv('opinion', 'exportTemplate') ? "data-toggle='modal' data-type='iframe' data-width='40%' class='exportTemplate'" : "class='disabled'";
        echo "<li $class>" . html::a($link, $lang->opinion->exportTemplate, '', $misc) . '</li>';
        ?>  
      </ul>
      <?php if($createButton):?>
      <?php if(common::hasPriv('opinion', 'import')) echo html::a($this->createLink('opinion', 'import', ''), '<i class="icon-import muted"></i> <span class="text">' . $lang->opinion->import . '</span>', '', "class='btn btn-link import' data-toggle='modal' data-type='iframe'");?>
      <?php endif;?>
    </div>
    <?php if($createButton):?>
    <?php if(common::hasPriv('opinion', 'create')) echo html::a($this->createLink('opinion', 'create'), "<i class='icon-plus'></i> {$lang->opinion->create}", '', "class='btn btn-primary'");?>
    <?php endif;?>
  </div>
</div>
<div id="mainContent" class="main-row fade">
  <div class="main-col">
    <div class="cell<?php if($browseType == 'bysearch') echo ' show';?>" id="queryBox" data-module='opinion'></div>
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
            <th class='c-id w-90px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->opinion->code);?></th>
            <th class='w-150px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->opinion->name);?></th>
            <th class='w-150px'><?php common::printOrderLink('union', $orderBy, $vars, $lang->opinion->union);?></th>
            <th class='w-130px'><?php common::printOrderLink('sourceMode',  $orderBy, $vars, $lang->opinion->sourceMode);?></th>
            <th class='c-date'><?php  common::printOrderLink('date',        $orderBy, $vars, $lang->opinion->date);?></th>
            <th class='c-date'><?php  common::printOrderLink('deadline',    $orderBy, $vars, $lang->opinion->deadlineAB);?></th>
            <th class='c-date w-140px'><?php common::printOrderLink('onlineTimeByDemand', $orderBy, $vars, $lang->opinion->onlineTimeByDemand);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdBy',   $orderBy, $vars, $lang->opinion->createdBy);?></th>
            <th class='w-90px'><?php  common::printOrderLink('status',      $orderBy, $vars, $lang->opinion->status);?></th>
            <th class='w-100px'><?php common::printOrderLink('dealUser',  $orderBy, $vars, $lang->opinion->dealUser);?></th>
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
            echo common::hasPriv('opinion', 'view') ? html::a(inlink('view', "opinionID=$opinion->id"), $opinion->name) : $opinion->name;
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
            <td><?php echo $opinion->status == 'online' ? $opinion->onlineTimeByDemand:'';?></td>
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
            <td class='c-actions text-center' style="overflow:visible">
            <?php
                common::printIcon('opinion', 'subdivide', "opinionID=$opinion->id", $opinion, 'list', 'split');
                common::printIcon('opinion', 'edit', "opinionID=$opinion->id", $opinion, 'list','edit', '');
                //研发责任人取所有需求条目合集  //迭代三十二 所有人可发起变更
                if(!in_array($opinion->opinionChangeStatus,[2,3]))
                {
                    common::printIcon('opinion', 'change', "opinionID=$opinion->id", $opinion, 'list','alter', '', 'iframe width:90%',true);
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->opinion->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                }
                common::printIcon('opinion', 'assignment', "opinionID=$opinion->id", $opinion, 'list', 'hand-right', '', 'iframe', true);
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
                if($this->app->user->account == 'admin' or (in_array($this->app->user->account, $executivesOpinion) or $this->app->user->account == $opinion->createdBy)) {
                    if ($opinion->status == 'closed') {
                        common::printIcon('opinion', 'reset',"opinionID=$opinion->id", $opinion, 'list', 'magic', '', 'iframe', true);
                    } else {
                        common::printIcon('opinion', 'close', "opinionID=$opinion->id", $opinion, 'list', 'pause', '', 'iframe', true);
                    }
                }else if($opinion->status == 'closed'){
                    echo '<button type="button" class="disabled btn" title="' . $lang->opinion->reset . '"><i class="icon-common-start disabled icon-magic"></i></button>'."\n";
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->opinion->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>'."\n";
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
          <?php if(!empty($opinion->children)):?>
          <?php $i = 0;?>
          <?php foreach($opinion->children as $key => $requirement):?>
          <?php $class  = $i == 0 ? ' table-child-top' : '';?>
          <?php $class .= ($i + 1 == count($opinion->children)) ? ' table-child-bottom' : '';?>
          <tr class='table-children<?php echo $class;?> parent-<?php echo $opinion->id;?>' data-id='<?php echo $requirement->id?>' data-status='<?php echo $requirement->status?>' style="display: none;">
            <td><?php echo $requirement->code;?></td>
            <td class="child text-ellipsis" title="<?php echo htmlspecialchars_decode($requirement->name);?>">
            <?php 
            echo common::hasPriv('requirement', 'view') ? html::a(helper::createLink('requirement', 'view', "requirementID=$requirement->id"), htmlspecialchars_decode($requirement->name)) : htmlspecialchars_decode($requirement->name);
            ?>
            </td>
            <td></td>
            <td class="text-ellipsis" title="<?php echo htmlspecialchars_decode(zget($lang->opinion->sourceModeList, $opinion->sourceMode, ''));?>"><?php echo zget($lang->opinion->sourceModeList, $opinion->sourceMode, '');?></td>
            <td class="text-ellipsis" title="<?php echo htmlspecialchars_decode($requirement->createdDate);?>"><?php echo $requirement->createdDate;?></td>
            <td class="text-ellipsis"><?php echo $requirement->deadLine != '0000-00-00' ? $requirement->deadLine : '';?></td>
            <td></td>
            <td><?php echo zget($users, $requirement->createdBy, $requirement->createdBy);?></td>
            <td><?php echo zget($lang->requirement->statusList, $requirement->status, '');?></td>
              <?php
              $reviewersTitle = '';
              if (!empty($requirement->reviewer)) {
                  foreach (explode(',', $requirement->reviewer) as $reviewers) {
                      if (!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                  }
              }
              //迭代二十八 待处理人拼接变更单待处理人共同显示
              if(!empty($requirement->changeDealUser)){
                  $changeDealUser = $requirement->changeDealUser;
                  foreach (explode(',', $changeDealUser) as $value) {
                      if (!empty($value)) $reviewersTitle .= zget($users, $value, $value) . ',';
                  }
              }
              $reviewersTitleArray = array_filter(array_unique(explode(',',$reviewersTitle)));
              $reviewersTitle = implode(',',$reviewersTitleArray);
              ?>
              <td title='<?php echo $reviewersTitle; ?>' class='text-ellipsis'>
                  <?php echo $reviewersTitle; ?>
              </td>
            <td class='c-actions text-center' style="overflow:visible" >
                <?php
                common::printIcon('requirement', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                common::printIcon('requirement', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                common::printIcon('requirement', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                //研发责任人取所有需求条目合集 迭代三十二 将变更流程发起人范围扩大至全部人员
                if(!in_array($requirement->requirementChangeStatus,[2,3]))
                {
                    common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list','alter', '', 'iframe',true);
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                }
                common::printIcon('requirement', 'feedback', "requirementID=$requirement->id", $requirement, 'list');
                ?>
                <?php if($this->app->user->account == 'admin'
                    or
                    (
                        ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false
                    )
                    and
                    (
                    (strstr($requirement->changeNextDealuser, $app->user->account) !== false)
                    )
                ):
                    ?>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <li><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                            <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                        </ul>
                    </div>
                <?php elseif($requirement->status != 'deleteout' and ($this->app->user->account == 'admin' or  ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false)):?>
                    <div class="btn-group dropup">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <li style="margin-left: -10px"><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                            <li style="margin-top:-10px;margin-bottom:5px;margin-left: -10px"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->reviewchange; ?></span></li>
                        </ul>
                    </div>
                <?php elseif($requirement->status != 'deleteout' and ($this->app->user->account == 'admin' or (strstr($requirement->changeNextDealuser, $app->user->account) !== false))):?>
                    <div class="btn-group dropup">
                        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                        <ul class="dropdown-menu">
                            <li style="margin-top:-10px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->review; ?></span></li>
                            <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                        </ul>
                    </div>
                <?php else:?>
                    <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->requirement->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                <?php endif;?>

                <?php
                if($this->app->user->account == 'admin' or in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy) {
                    if ($requirement->status == 'closed') {
                        common::printIcon('requirement', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                    } else {
                        common::printIcon('requirement', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                    }
                }else if($requirement->status == 'closed'){
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>'."\n";
                }else{
                    echo '<button type="button" class="disabled btn" title="' . $lang->requirement->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>'."\n";
                }

                if ($requirement->ignoreStatus) {
                    common::printIcon('requirement', 'recover', "requirementID=$requirement->id", $requirement, 'list', 'bell', '', 'iframe', true);
                } else {
                    common::printIcon('requirement', 'ignore', "requirementID=$requirement->id", $requirement, 'list', 'ban', '', 'iframe', true);
                }
                common::printIcon('requirement', 'delete', "requirementID=$requirement->id", $requirement, 'list', 'trash', '', 'iframe', true);

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

</script>
<?php include '../../common/view/footer.html.php';?>
