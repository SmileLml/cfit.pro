<?php include '../../../common/view/header.html.php';?>
<?php include 'auditSetCommonJs.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
        foreach($lang->my->myReviewList as $key => $type):
    ?>
    <?php $active = $key == $browseType ? 'btn-active-text' : '';?>
    <?php echo html::a($this->createLink('my', $app->rawMethod, "mode=$mode&browseType=$key"), '<span class="text">' . $type . '</span>', '', 'class="btn btn-link ' . $active .'"' . "id='audit{$key}'");?>
    <?php
        endforeach;
    ?>
  </div>
</div>

<div id='mainContent' class='main-row'>
  <div class='main-col'>
    <?php if(empty($reviewList)):?>
    <div class="table-empty-tip">
      <p>
        <span class="text-muted"><?php echo $lang->noData;?></span>
      </p>
    </div>
    <?php else:?>
    <form class='main-table' id='problemForm' method='post' data-ride='table' data-nested='true' data-checkable='false'>
      <?php $vars = "type=$mode&browseType=$browseType&orderBy=%s&recTotal=$pager->recTotal&recPerPage=$pager->recPerPage&pageID=$pager->pageID";?>
      <table class='table table-fixed has-sort-head' id='problems'>
        <thead>
          <tr>
            <th class='w-140px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->problem->code);?></th>
            <th><?php common::printOrderLink('abstract', $orderBy, $vars, $lang->problem->abstract);?></th>
            <th class='w-140px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->problem->app);?></th>
            <th class='w-100px'><?php common::printOrderLink('ifRecive', $orderBy, $vars, $lang->problem->ifRecive);?></th>
            <th class='w-60px'><?php common::printOrderLink('pri', $orderBy, $vars, $lang->problem->pri);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->problem->createdBy);?></th>
            <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->problem->createdDept);?></th>
            <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->problem->createdDate);?></th>
            <th class='w-80px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->problem->status);?></th>
            <th class='w-80px'><?php common::printOrderLink('dealUser', $orderBy, $vars, $lang->problem->dealUser);?></th>
            <th class='text-center w-120px'><?php echo $lang->actions;?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($reviewList as $problem):?>
          <tr>
            <td><?php echo common::hasPriv('problem', 'view') ? html::a($this->createLink('problem', 'view', "problemID=$problem->id"), $problem->code) : $problem->code;?></td>
            <?php
            $as = array();
            foreach(explode(',', $problem->app) as $app)
            {
                if(!$app) continue;
                $as[] = zget($apps, $app);
            }
            $app = implode(', ', $as);
            ?>
            <td title="<?php echo $problem->abstract;?>" class='text-ellipsis'><?php echo $problem->abstract;?></td>
            <td title="<?php echo $app;?>"><?php echo $app;?></td>
              <td><?php echo zget($lang->problem->ifReturnList , $problem->ifReturn); ?></td>
            <td><?php echo zget($lang->problem->priList, $problem->pri);?></td>
            <td><?php echo zget($users, $problem->createdBy, '');?></td>
            <td><?php echo zget($depts, $problem->createdDept, '');?></td>
            <td><?php echo $problem->createdDate;?></td>
            <td><?php echo zget($lang->problem->statusList, $problem->status);?></td>
            <td title="<?php echo trim($problem->dealUsers,',');?>"><?php echo trim($problem->dealUsers,',');?></td>
            <td class='c-actions' style="overflow:visible">
              <?php
              $status = array('confirmed','assigned','toclose'); //20220930 待分配和待分析 或待开发且不是问题 高亮
              if($this->app->user->admin or ( $this->app->user->account == $problem->dealUser && (in_array($problem->status,$status) or ($problem->status == 'feedbacked' && $problem->type == 'noproblem'))))//非当前处理人，图标置灰不能操作
              {
                  common::printIcon('problem', 'deal', "problemID=$problem->id", $problem, 'list', 'time', '', 'iframe', true);
              }
              else
              {
                  echo '<button type="button" class="disabled btn" title="' . $lang->problem->deal . '"><i class="icon-common-suspend disabled icon-time"></i></button>';
              }
              //新建问题反馈单
              common::printIcon('problem', 'createfeedback', "problemID=$problem->id", $problem, 'list', 'feedback', '', 'iframe', true);
              //common::printIcon('problem', 'approvefeedback', "problemID=$problem->id", $problem, 'list', 'glasses', '', 'iframe', true);
              $delayFlag = common::hasPriv('problem', 'reviewdelay') && in_array($problem->changeStatus, array_keys($this->lang->problem->reviewNodeStatusLableList)) && in_array($this->app->user->account, explode(',', $problem->changeDealUser));
              if($delayFlag && $problem->feedBackFlag){
                  $str =  '<div class="btn-group">';
                  $str .= "<button class='btn btn-primary dropdown-toggle' data-toggle='dropdown' title='" . $this->lang->problem->review . "'><i class='icon icon-glasses'></i></button>";
                  $str .= '<ul class="dropdown-menu">';
                  $str .= '<li>' . html::a($this->createLink('problem', 'reviewdelay', 'problemId=' . $problem->id , '', true), $this->lang->problem->reviewdelay, '', "data-toggle='modal' data-type='iframe' ") . '</li>';
                  $str .= '<li>' . html::a($this->createLink('problem', 'approvefeedback', 'problemID=' . $problem->id , '', true), $this->lang->problem->approvefeedback, '', "data-toggle='modal' data-type='iframe' ") . '</li>';
                  $str .= '</ul></div>';
                  echo $str;
              }elseif($delayFlag){
                  common::printIcon('problem', 'reviewdelay', "problemID=$problem->id", $problem, 'list', 'glasses', '', 'iframe', true);
              }elseif ($problem->feedBackFlag){
                  common::printIcon('problem', 'approvefeedback', "problemID=$problem->id", $problem, 'list', 'glasses', '', 'iframe', true);
              }else{
                  echo "<button class='btn disabled' title='" . $this->lang->problem->review . "'><i class='icon icon-glasses disabled'></i></button>";
              }
              ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php';?>

