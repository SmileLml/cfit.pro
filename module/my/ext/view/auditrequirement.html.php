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
        <table class='table has-sort-head table-fixed' id='reviewList'>
          <thead>
            <tr>
                <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->requirement->code);?></th>
                <th class='w-250px'><?php common::printOrderLink('name', $orderBy, $vars, $lang->requirement->name);?></th>
                <th class='w-250px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->requirement->project);?></th>
                <th class='w-100px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->requirement->dept);?></th>
                <th class='w-90px'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->requirement->owner);?></th>
                <th class='w-100px'><?php common::printOrderLink('deadLine', $orderBy, $vars, $lang->requirement->deadLine);?></th>
                <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->requirement->end);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->requirement->createdDate);?></th>
                <th class='w-90px'><?php common::printOrderLink('entriesCode', $orderBy, $vars, $lang->requirement->extNum);?></th>
                <th class='w-90px'><?php common::printOrderLink('feedbackStatus', $orderBy, $vars, $lang->requirement->feedbackStatus);?></th>
                <th class='w-90px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->requirement->status);?></th>
                <th class='w-90px'><?php echo $lang->requirement->pending;?></th>
                <th class='text-center w-250px'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($reviewList as $requirement):?>
                <tr>
                    <td><?php echo $requirement->code;?></td>
                    <td class='text-ellipsis' title="<?php echo $requirement->name;?>"><?php echo common::hasPriv('requirement', 'view') ? html::a($this->createLink('requirement', 'view', "requirementID=$requirement->id"), $requirement->name) : $requirement->name;?></td>
                    <td class="text-ellipsis" title="<?php echo zmget($projects, $requirement->project, '');?>"><?php echo zmget($projects, $requirement->project, '');?></td>
                    <td title="<?php echo zmget($depts, $requirement->dept);?>"><?php echo zmget($depts, $requirement->dept);?></td>
                    <td title="<?php echo zmget($users, $requirement->owner);?>"><?php echo zmget($users, $requirement->owner, '');?></td>
                    <td><?php echo $requirement->deadLine;?></td>
                    <td><?php if(!helper::isZeroDate($requirement->end)) echo $requirement->end;?></td>
                    <td class="text-ellipsis" title="<?php echo $requirement->createdDate;?>"><?php echo $requirement->createdDate;?></td>
                    <td class="text-ellipsis" title="<?php echo $requirement->entriesCode;?>"><?php echo $requirement->entriesCode;?></td>
                    <td><?php echo zget($lang->requirement->feedbackStatusList, $requirement->feedbackStatus);?></td>
                    <td><?php echo zget($lang->requirement->statusList, $requirement->status);?></td>

                    <?php
                    //待处理人构造
                    $reviewersTitle = '';
                    $reviewersArray = [];
                    if((!empty($requirement->reviewer)) && ($requirement->status != 'delivered'))
                    {
                        $reviewersArray = explode(',', $requirement->reviewer);
                        $reviewersTitle = implode(',',array_unique(array_filter($reviewersArray)));
                    }
                    if(!empty($requirement->changeDealUser))
                    {
                        $requirementChangeDealUser = explode(',',$requirement->changeDealUser);
                        $finalDealUser = array_merge($reviewersArray,$requirementChangeDealUser);
                        $reviewersTitle = implode(',',array_unique(array_filter($finalDealUser)));
                    }
                    $reviewersTitle = zmget($users,$reviewersTitle);
                    ?>
                    <td title='<?php echo $reviewersTitle;?>' class='text-ellipsis'>
                        <?php echo $reviewersTitle;?>
                    </td>
                    <td class='c-actions text-center' style="overflow:visible">

                        <?php
                        common::printIcon('requirement', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                        //common::printIcon('requirement', 'confirm', "requirementID=$requirement->id", $requirement, 'list', 'ok','', 'iframe', true);
                        common::printIcon('requirement', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                        //common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list', 'alter');
                        common::printIcon('requirement', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');

                        //研发责任人取所有需求条目合集  变更中、已退回[2,3]
                        if(!in_array($requirement->requirementChangeStatus,[2,3]))
//                        if((in_array($this->app->user->account,explode(',',$requirement->owner))  and !in_array($requirement->requirementChangeStatus,[2,3])) or $this->app->user->account == 'admin')
                        {
                            common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list','alter', '', 'iframe',true);
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirement->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                        }

                        common::printIcon('requirement', 'feedback', "requirementID=$requirement->id", $requirement, 'list');
//                        common::printIcon('requirement', 'review', "requirementID=$requirement->id", $requirement, 'list', 'glasses', '', 'iframe', true);
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
                        <?php elseif($this->app->user->account == 'admin' or ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false):?>
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                <ul class="dropdown-menu">
                                    <li style=""><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                    <li style="margin-top:-14px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->reviewchange; ?></span></li>
                                </ul>
                            </div>
                        <?php elseif(($this->app->user->account == 'admin' or (strstr($requirement->changeNextDealuser, $app->user->account) !== false))):?>
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                <ul class="dropdown-menu">
                                    <li style="margin-top:-14px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->review; ?></span></li>
                                    <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                </ul>
                            </div>
                        <?php else:?>
                            <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->requirement->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                        <?php endif;?>

                        <?php
                        if($this->app->user->account == 'admin' or (in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy)) {
                            if ($requirement->status == 'closed') {
                                common::printIcon('requirement', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                            } else {
                                common::printIcon('requirement', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                            }
                        }else if($requirement->status == 'closed'){
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirement->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirement->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
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
            <?php endforeach;?>
          </tbody>
        </table>
      <div class='table-footer'></div>
        <?php if($reviewListIgnore) { ?>
        <div style="padding: 10px 0 5px 10px">已忽略</div>
        <table class='table has-sort-head table-fixed' id='reviewList2'>
            <thead>
            <tr>
                <th class='w-100px'><?php common::printOrderLink('code', $orderBy, $vars, $lang->requirement->code);?></th>
                <th><?php common::printOrderLink('name', $orderBy, $vars, $lang->requirement->name);?></th>
                <th class='w-250px'><?php common::printOrderLink('project', $orderBy, $vars, $lang->requirement->project);?></th>
                <th class='w-100px'><?php common::printOrderLink('dept', $orderBy, $vars, $lang->requirement->dept);?></th>
                <th class='w-90px'><?php common::printOrderLink('owner', $orderBy, $vars, $lang->requirement->owner);?></th>
                <th class='w-100px'><?php common::printOrderLink('deadLine', $orderBy, $vars, $lang->requirement->deadLine);?></th>
                <th class='w-100px'><?php common::printOrderLink('end', $orderBy, $vars, $lang->requirement->end);?></th>
                <th class='w-80px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->requirement->createdDate);?></th>
                <th class='w-90px'><?php common::printOrderLink('entriesCode', $orderBy, $vars, $lang->requirement->extNum);?></th>
                <th class='w-90px'><?php common::printOrderLink('feedbackStatus', $orderBy, $vars, $lang->requirement->feedbackStatus);?></th>
                <th class='w-90px'><?php common::printOrderLink('status', $orderBy, $vars, $lang->requirement->status);?></th>
                <th class='w-90px'><?php echo $lang->requirement->pending;?></th>
                <th class='text-center w-250px'><?php echo $lang->actions;?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($reviewListIgnore as $requirement):?>
                <tr>
                    <td><?php echo $requirement->code;?></td>
                    <td class='text-ellipsis' title="<?php echo $requirement->name;?>"><?php echo common::hasPriv('requirement', 'view') ? html::a($this->createLink('requirement', 'view', "requirementID=$requirement->id"), $requirement->name) : $requirement->name;?></td>
                    <td class="text-ellipsis" title="<?php echo zget($projects, $requirement->project, '');?>"><?php echo zget($projects, $requirement->project, '');?></td>
                    <td><?php echo zget($depts, $requirement->dept);?></td>
                    <td><?php echo zget($users, $requirement->owner, '');?></td>
                    <td><?php echo $requirement->deadLine;?></td>
                    <td><?php if(!helper::isZeroDate($requirement->end)) echo $requirement->end;?></td>
                    <td class="text-ellipsis" title="<?php echo $requirement->createdDate;?>"><?php echo $requirement->createdDate;?></td>
                    <td class="text-ellipsis" title="<?php echo $requirement->entriesCode;?>"><?php echo $requirement->entriesCode;?></td>
                    <td><?php echo zget($lang->requirement->feedbackStatusList, $requirement->feedbackStatus);?></td>
                    <td><?php echo zget($lang->requirement->statusList, $requirement->status);?></td>

                    <?php
                    $reviewersTitle = '';
                    if(!empty($requirement->reviewer))
                    {
                        foreach(explode(',', $requirement->reviewer) as $reviewers)
                        {
                            if(!empty($reviewers)) $reviewersTitle .= zget($users, $reviewers, $reviewers) . ',';
                        }
                    }
                    $reviewersTitle = trim($reviewersTitle, ',');
                    ?>
                    <td title='<?php echo $reviewersTitle;?>' class='text-ellipsis'>
                        <?php echo $reviewersTitle;?>
                    </td>
                    <td class='c-actions text-center' style="overflow:visible">

                        <?php
                        common::printIcon('requirement', 'edit', "requirementID=$requirement->id", $requirement, 'list', 'edit');
                        //common::printIcon('requirement', 'confirm', "requirementID=$requirement->id", $requirement, 'list', 'ok','', 'iframe', true);
                        common::printIcon('requirement', 'assignTo', "requirementID=$requirement->id", $requirement, 'list', '', '', 'iframe', true);
                        //common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list', 'alter');
                        common::printIcon('requirement', 'subdivide', "requirementID=$requirement->id", $requirement, 'list', 'split', '');
                        //研发责任人取所有需求条目合集 迭代三十二 将变更流程发起人范围扩大至全部人员
                        if(!in_array($requirement->requirementChangeStatus,[2,3]))
                        {
                            common::printIcon('requirement', 'change', "requirementID=$requirement->id", $requirement, 'list','alter', '', 'iframe',true);
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirement->change . '"><i class="icon-common-suspend disabled icon-alter"></i></button>'."\n";
                        }

                        common::printIcon('requirement', 'feedback', "requirementID=$requirement->id", $requirement, 'list');
//                        common::printIcon('requirement', 'review', "requirementID=$requirement->id", $requirement, 'list', 'glasses', '', 'iframe', true);
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
                        <?php elseif($this->app->user->account == 'admin' or ($requirement->feedbackStatus == 'todepartapproved' || $requirement->feedbackStatus == 'toinnovateapproved') and strstr($requirement->feedbackDealUser, $app->user->account) !== false):?>
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                <ul class="dropdown-menu">
                                    <li style=""><?php echo html::a($this->createLink('requirement', 'review', 'requirementID=' . $requirement->id , '', true), $lang->requirement->review , '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                    <li style="margin-top:-14px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->reviewchange; ?></span></li>
                                </ul>
                            </div>
                        <?php elseif(($this->app->user->account == 'admin' or (strstr($requirement->changeNextDealuser, $app->user->account) !== false))):?>
                            <div class="btn-group">
                                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" title=""><i class="icon icon-glasses"></i></button>
                                <ul class="dropdown-menu">
                                    <li style="margin-top:-14px;"><a href="javascript:" onclick="return false;"></a><span style="color:#ddc4c4"><?php echo $lang->requirement->review; ?></span></li>
                                    <li><?php echo html::a($this->createLink('requirement', 'reviewchange', 'requirementID=' . $requirement->id , '', true), $lang->requirement->reviewchange, '', "data-toggle='modal' data-type='iframe' ") ?></li>
                                </ul>
                            </div>
                        <?php else:?>
                            <?php echo '<div class="btn-group"><button type="button" class="disabled btn" title="' . $this->lang->requirement->dealReview . '"><i class="icon icon-glasses"></i></button></div>'."\n";?>
                        <?php endif;?>

                        <?php
                        if($this->app->user->account == 'admin' or ($requirement->createdBy != 'guestcn' and (in_array($this->app->user->account, $executives) or $this->app->user->account == $requirement->createdBy))) {
                            if ($requirement->status == 'closed') {
                                common::printIcon('requirement', 'activate', "requirementID=$requirement->id", $requirement, 'list', 'magic', '', 'iframe', true);
                            } else {
                                common::printIcon('requirement', 'close', "requirementID=$requirement->id", $requirement, 'list', 'pause', '', 'iframe', true);
                            }
                        }else if($requirement->status == 'closed'){
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirement->activate . '"><i class="icon-common-start disabled icon-magic"></i></button>';
                        }else{
                            echo '<button type="button" class="disabled btn" title="' . $lang->requirement->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
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
