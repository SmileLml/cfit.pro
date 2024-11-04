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
    <?php if(empty($reviewList)):?>
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
              <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->modifycncc->code);?></th>
              <th class='w-100px'><?php common::printOrderLink('type', $orderBy, $vars, $lang->modifycncc->type);?></th>
              <th class='w-100px'><?php common::printOrderLink('app', $orderBy, $vars, $lang->modifycncc->app);?></th>
              <th class='w-100px'><?php common::printOrderLink('planBegin', $orderBy, $vars, $lang->modifycncc->planBegin);?></th>
              <th class='w-100px'><?php common::printOrderLink('planEnd', $orderBy, $vars, $lang->modifycncc->planEnd);?></th>
              <th class='w-100px'><?php common::printOrderLink('createdBy', $orderBy, $vars, $lang->modifycncc->createdBy);?></th>
              <th class='w-100px'><?php common::printOrderLink('createdDept', $orderBy, $vars, $lang->modifycncc->createdDept);?></th>
              <th class='w-100px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->modifycncc->createdDate);?></th>
              <th class='w-80px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->modifycncc->status);?></th>
              <th class='w-80px'> <?php echo $lang->modifycncc->dealUser;?></th>
              <th class='text-left w-40px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList as $modifycncc):?>
            <tr>
              <td><?php echo common::hasPriv('modifycncc', 'view') ? html::a($this->createLink('modifycncc', 'view', "modifycnccID=$modifycncc->id"), $modifycncc->code) : $modifycncc->code;?></td>
              <td><?php echo zget($lang->modifycncc->typeList, $modifycncc->type);?></td>
                <?php
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
                $app = implode(', ', $as);
                ?>
                <td title="<?php echo $app;?>"><?php echo $app;?></td>
              <td><?php echo $modifycncc->planBegin;?></td>
              <td><?php echo $modifycncc->planEnd;?></td>
              <td><?php echo $modifycncc->realname;?></td>
              <td><?php echo zget($depts, $modifycncc->createdDept, '');?></td>
              <td><?php echo substr($modifycncc->createdDate, 0, 11);?></td>
              <td><?php echo zget($lang->modifycncc->statusList, $modifycncc->status);?></td>
              <?php
                $reviewers = $modifycncc->reviewers;
                $reviewersArray = explode(',', $reviewers);
                //所有审核人
                $reviewerUsers    = getArrayValuesByKeys($users, $reviewersArray);
                $reviewerUsersStr = implode(',', $reviewerUsers);
                $subCount = 3;
                $reviewerUsersSubStr = getArraySubValuesStr($reviewerUsers, $subCount);
              ?>
                <td title="<?php echo $reviewerUsersStr; ?>">
                    <?php echo $reviewerUsersSubStr; ?>
                </td>
              <td class='c-actions'>
                <?php
                common::printIcon('modifycncc', 'link', "modifycnccID=$modifycncc->id&version=$modifycncc->version&reviewStage=$modifycncc->reviewStage", $modifycncc, 'list', 'link', '', 'iframe', true);
                common::printIcon('modifycncc', 'review', "modifycnccID=$modifycncc->id&version=$modifycncc->version&reviewStage=$modifycncc->reviewStage", $modifycncc, 'list', 'glasses', '', 'iframe', true);
                ?>
              </td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'></div>
    </form>
    <?php endif;?>
  </div>
</div>
<?php include '../../../common/view/footer.html.php'?>
