<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
        <?php $browseLink = $app->session->opinioninsideList != false ? $app->session->opinioninsideList : inlink('browse');?>

        <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
      <span class="label label-id"><?php echo $opinion->code?></span>
      <span class="text" title='<?php echo $opinion->name;?>'><?php echo $opinion->name;?></span>
    </div>
  </div>
</div>

<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinioninside->background;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($opinion->background) ? $opinion->background : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinioninside->overview;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($opinion->overview) ? $opinion->overview : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <?php if($opinion->desc):?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->opinion->desc;?></div>
                <div class="detail-content article-content">
                    <?php echo !empty($opinion->desc) ? $opinion->desc : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
                </div>
            </div>
        <?php endif;?>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinioninside->remark;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($opinion->remark) ? $opinion->remark : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $opinion->files, 'fieldset' => 'true', 'object' => $opinion, 'canOperate' => $opinion->createdBy == $this->app->user->account or $this->app->user->admin));?>
      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=opinion&objectID=$opinion->id");?>

        <?php
        $app->loadLang('requirement');
        if(!empty($changes)):?>
            <div class="detail">
                <div class="detail-title"><?php echo $lang->requirementinside->changeRecord; ?></div>
                <div class="detail-content article-content">
                    <table class="table ops" style="text-align: center">
                        <tr>
                            <th class="w-100px" style="text-align: center"><?php echo $lang->requirementinside->changeNum; ?></th>
                            <th class="w-200px" style="text-align: center"><?php echo $lang->requirementinside->changeTime; ?></th>
                            <th class="w-200px" style="text-align: center"><?php echo $lang->requirementinside->changeCode; ?></th>
<!--                            <th class="w-100px" style="text-align: center">--><?php //echo $lang->requirement->changeRemark; ?><!--</th>-->
                        </tr>
                        <?php $num = 1;
                        foreach ($changes as $val): ?>
                            <tr>
                                <td><?php echo $num++; ?></td>
                                <td>
                                    <?php echo $val->createdDate; ?>
                                </td>
                                <td>
                                    <a class="iframe" data-width="900" href='<?php echo $this->createLink('requirementchange', 'changeview', "changeID=$val->id",'',true)?>'><?php echo $val->changeNumber; ?></a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif;?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
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
              echo '<button type="button" class="disabled btn" title="' . $lang->opinion->reset . '"><i class="icon-common-start disabled icon-magic"></i></button>';
          }else{
              echo '<button type="button" class="disabled btn" title="' . $lang->opinion->close . '"><i class="icon-common-suspend disabled icon-pause"></i></button>';
          }

          common::printIcon('opinioninside', 'delete', "opinionID=$opinion->id", $opinion, 'list', 'trash', '', 'iframe',true);
          if ($opinion->ignore) {
              common::printIcon('opinioninside', 'recoveryed', "opinionID=$opinion->id", $opinion, 'list', 'bell', '', 'iframe', true);
          } else {
              common::printIcon('opinioninside', 'ignore', "opinionID=$opinion->id", $opinion, 'list', 'ban', '', 'iframe', true);
          }
          ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->opinioninside->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-120px'><?php echo $lang->opinioninside->category;?></th>
                <td><?php echo zget($lang->opinion->categoryList, $opinion->category, '');?></td>
              </tr>
             <tr>
               <th><?php echo $lang->opinioninside->charge;?></th>
                <td class='c-actions text-left'>
                  <div style="display:flex; flex-wrap:wrap;align-items:center;">
                <?php
                  foreach(explode(',',trim($opinion->assignedTo,',')) as $index => $assignedTo):?>
                  <span>
                    <?php echo zget($users, $assignedTo,'').($index+1==sizeof(explode(',',trim($opinion->assignedTo,',')))?'':'，'); ?>
                  </span>
                  <?php endforeach?><span>
                  <?php common::printIcon('opinioninside', 'editassignedto', "opinionId=$opinion->id", $opinion, 'list', 'edit', '', 'iframe', true);?>
                  </span>
                  </div>
                </td>
             </tr>
              <tr>
                <th><?php echo $lang->opinioninside->sourceMode;?></th>
                <td><?php echo zget($lang->opinioninside->sourceModeListOld, $opinion->sourceMode, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->opinioninside->sourceName;?></th>
                <td><?php echo $opinion->sourceName;?></td>
              </tr>
            <tr>
                <th><?php echo $lang->opinioninside->union;?></th>
                <td>
                    <?php
                    $unionList = explode(',',str_replace(' ', '', $opinion->union));
                    foreach ($unionList as $union)
                    {
                        if($union) echo ' ' . zget($lang->opinion->unionList, $union, '');
                    }
                    ?>
                </td>
            </tr>
              <tr>
                <th><?php echo $lang->opinioninside->contact;?></th>
                <td><?php echo $opinion->contact;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->opinioninside->contactInfo;?></th>
                <td><?php echo $opinion->contactInfo;?></td>
              </tr>
              <?php if($opinion->createdBy == 'guestcn'):?>
              <tr>
                <th><?php echo $lang->opinioninside->date;?></th>
                <td><?php echo $opinion->createdDate;?></td>
              </tr>
              <?php else:?>
              <tr>
                  <th><?php echo $lang->opinioninside->date;?></th>
                  <td><?php echo $opinion->date;?></td>
              </tr>
            <?php endif;?>
            <tr>
                <th><?php echo $lang->opinioninside->receiveDate;?></th>
                <td><?php echo $opinion->receiveDate;?></td>
            </tr>
              <tr>
                <th><?php echo $lang->opinioninside->deadline;?></th>
                <td><?php echo $opinion->deadline;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->opinioninside->project;?></th>
                <td>
                    <?php
                    foreach ($projectList as $projectID => $item) {
                        if ($projectID) {
                            echo html::a($this->createLink('projectplan', 'view', 'id=' . $projectID), $item->name, '', "data-app='platform' style='color: #0c60e1;'");
                            echo "<br>";
                        }
                    }
                    ?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->opinioninside->onlineTimeByDemand;?></th>
                  <td><?php echo $opinion->status == 'online' ?  $opinion->onlineTimeByDemand :'';?></td>
              </tr>
              <tr>
                <th class='w-100px'><?php echo $lang->opinion->synUnion;?></th>
                <td>
                    <?php
                    $synUnionList = explode(',', str_replace(' ', '', $opinion->synUnion));
                    foreach($synUnionList as $synUnion)
                    {
                        if($synUnion) echo ' ' . zget($lang->opinion->synUnionList, $synUnion, '');
                    }
                    ?>
                </td>
              </tr>
              <?php if($opinion->demandCode):?>
                  <tr>
                      <th class='w-120px'><?php echo $lang->opinioninside->demandCode;?></th>
                      <td><?php echo $opinion->demandCode;?></td>
                  </tr>
              <?php endif;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
          <div class="detail-title"><?php echo $lang->opinioninside->processInfo;?></div>
          <div class='detail-content'>
              <table class='table table-data'>
                  <tbody>
                      <tr>
                          <th><?php echo $lang->opinioninside->status;?></th>
                          <td><?php echo zget($lang->opinioninside->statusList, $opinion->status, '');?></td>
                      </tr>
                      <tr>
                          <th><?php echo $lang->opinioninside->dealUser;?></th>
                          <td><?php echo zget($users, $opinion->dealUser, '');?></td>
                      </tr>
                      <?php if($opinion->mailto != ''):?>
                      <tr>
                          <th><?php echo $lang->opinioninside->mailto;?></th>
                          <td><?php foreach(explode(',', $opinion->mailto) as $user) echo zget($users, $user, '') . ' '; ?></td>
                      </tr>
                      <?php endif;?>
                  </tbody>
              </table>
          </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->consumedTitle;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->opinioninside->nodeUser;?></th>
<!--                <td class='text-right'>--><?php //echo $lang->opinioninside->consumed;?><!--</td>-->
                <td class='text-center'><?php echo $lang->opinioninside->before;?></td>
                <td class='text-center'><?php echo $lang->opinioninside->after;?></td>
              </tr>
              <?php foreach($opinion->consumed as $index => $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
<!--                <td class='text-right'>--><?php //echo $c->consumed . ' ' . $lang->hour;?><!--</td>-->
                <td class='text-center'><?php echo zget($lang->opinioninside->statusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->opinioninside->statusList, $c->after, '-');?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
