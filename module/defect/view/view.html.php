<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>.body-modal #mainMenu>.btn-toolbar .page-title {width: auto;}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
  <?php $browseLink = $app->session->defectList != false ? $app->session->defectList : inlink('browse');?>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $defect->code?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->defectTitle;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($defect->title) ? $defect->title : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->defect->steps;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($defect->issues) ? $defect->issues : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->testCase;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->testCase) ? $defect->testCase : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->Dropdown_suspensionreason;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->Dropdown_suspensionreason) ? $defect->Dropdown_suspensionreason : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->defect->testAdvice;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($defect->testAdvice) ? $defect->testAdvice : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->resolution;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($defect->resolution) ? zget($lang->bug->resolutionList, $defect->resolution): "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->resolvedBuild;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->resolvedBuild) ? zget($resolvedBuilds, $defect->resolvedBuild) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->resolvedDate;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->resolvedDate) ? $defect->resolvedDate : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->linkProduct;?></div>
        <div class="detail-content article-content">
          <?php echo !empty($defect->linkProduct) ? $defect->linkProduct : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->ifTest;?></div>
        <div class="detail-content article-content">
            <?php echo isset($defect->ifTest) ? zget($lang->defect->ifList,$defect->ifTest) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->dealSuggest;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->dealSuggest) ? zget($lang->defect->dealSuggestList, $defect->dealSuggest) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->dealComment;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->dealComment) ? $defect->dealComment : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->progress;?></div>
        <div class="detail-content article-content">
            <?php echo !empty($defect->progress) ? $defect->progress : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
        </div>
      </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->defect->changeDate;?></div>
            <div class="detail-content article-content">
                <?php echo $defect->changeDate != '0000-00-00 00:00:00' ? substr($defect->changeDate, 0, 10) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->defect->submitChangeDate;?></div>
            <div class="detail-content article-content">
                <?php echo $defect->submitChangeDate != '0000-00-00 00:00:00' ? substr($defect->submitChangeDate, 0, 10) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->defect->EditorImpactscope;?></div>
            <div class="detail-content article-content">
                <?php echo !empty($defect->EditorImpactscope) ? $defect->EditorImpactscope : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <div class="detail">
            <div class="detail-title"><?php echo $lang->defect->ifHisIssue;?></div>
            <div class="detail-content article-content">
                <?php echo $defect->ifHisIssue != '' ? zget($lang->defect->ifList,$defect->ifHisIssue) : "<div class='text-center text-muted'>" . $lang->noData . '</div>';?>
            </div>
        </div>
        <?php if($defect->files) echo $this->fetch('file', 'printFiles', array('files' => $defect->files, 'fieldset' => 'true'));?>
        <?php $actionFormLink = $this->createLink('action', 'comment', "objectType=defect&objectID=$defect->id");?>
    </div>
    <div class="cell"><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
          if(($defect->status == 'toconfirm') and ($this->app->user->account == $defect->dealUser or  $this->app->user->account == 'admin')) common::printIcon('defect', 'confirm', "defectID=$defect->id", $defect, 'button', 'time', '', 'iframe', true);
          if(($defect->status == 'tosolve' or $defect->status == 'nextfix' or $defect->status == 'hitback') and ($this->app->user->account == $defect->dealUser or  $this->app->user->account == 'admin')) common::printIcon('defect', 'deal', "defectID=$defect->id", $defect, 'button', 'time', '', 'iframe', true);
        if($defect->status == 'tofeedback' and $defect->syncStatus == 1 and ($this->app->user->account == $defect->testrequestCreatedBy or $this->app->user->account == $defect->productenrollCreatedBy or  $this->app->user->account == 'admin'))common::printIcon('defect', 'change', "defectID=$defect->id", $defect, 'button', ' ', '', 'iframe', true);
          if($defect->status == 'tofeedback' and $defect->syncStatus == -1 and ($this->app->user->account == $defect->dealedBy or  $this->app->user->account == $defect->testrequestCreatedBy or  $this->app->user->account == $defect->productenrollCreatedBy or  $this->app->user->account == 'admin'))common::printIcon('defect', 'rePush', "defectID=$defect->id", $defect, 'button', '', '', 'iframe', true);
        ?>
      </div>
    </div>
  </div>
  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class="w-100px"><?php echo $lang->defect->source;?></th>
                <td><?php echo zget($lang->defect->sourceList,$defect->source);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->defect->app;?></th>
                  <td><?php echo zget($apps, $defect->app, '');?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->product;?></th>
                  <td><?php echo zget($products, $defect->product, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->defect->project;?></th>
                <td><?php echo zget($projects, $defect->project, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->defect->CBPproject;?></th>
                <td><?php echo zget($cbpprojectList, $defect->CBPproject, '');?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->defect->sampleVersionNumber;?></th>
                <td><?php echo  $defect->sampleVersionNumber;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->reportUser;?></th>
                  <td><?php echo $defect->source == '1' ? zget($users, $defect->reportUser, '') : $defect->reportUser;?></td>
              </tr>
              <tr>
                <th class="w-100px"><?php echo $lang->defect->reportDate;?></th>
                <td><?php echo $defect->reportDate != '0000-00-00 00:00:00' ? $defect->reportDate : '';?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->pri;?></th>
                  <td><?php echo zget($lang->bug->defectPriList, $defect->pri);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->type;?></th>
                  <td><?php echo zget($lang->bug->typeList, $defect->type);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->childType;?></th>
                  <td><?php echo zget($defectTypeList, $defect->childType);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->severity;?></th>
                  <td><?php echo zget($lang->bug->defectSeverityList, $defect->severity);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->frequency;?></th>
                  <td><?php echo zget($lang->bug->defectFrequencyList, $defect->frequency);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->developer;?></th>
                  <td><?php echo zget($users, $defect->developer);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->dept;?></th>
                  <td><?php echo zget($depts, $defect->dept);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->tester;?></th>
                  <td><?php echo $defect->source == '1' ? zget($users, $defect->tester) : $defect->tester;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->testType;?></th>
                  <td><?php echo zget($lang->testingrequest->acceptanceTestTypeList, $defect->testType);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->projectManager;?></th>
                  <td><?php echo zget($users, $defect->projectManager);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->rounds;?></th>
                  <td><?php echo $defect->rounds;?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->testEnvironment;?></th>
                  <td><?php echo zget($lang->defect->testEnvironmentList, $defect->testEnvironment);?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->verification;?></th>
                  <td><?php echo zget($lang->defect->verificationList, $defect->verification);?></td>
              </tr>
              <tr>
                <th><?php echo $lang->defect->testrequest;?></th>
                <td>
                <?php echo $defect->testrequestId ? html::a($this->createLink('testingrequest', 'view', 'id=' . $defect->testrequestId), $testrequestCode, '', "style='color: #0c60e1;'") : ''; ?>
                </td>
              </tr>

              <tr>
                <th><?php echo $lang->defect->productenroll;?></th>
                <td>
                <?php echo $defect->productenrollId ? html::a($this->createLink('productenroll', 'view', 'id=' . $defect->productenrollId), $productenrollCode, '', "style='color: #0c60e1;'") : ''; ?>
                </td>
              </tr>

<!--              <tr>-->
<!--                <th>--><?php //echo $lang->defect->modifycnccId;?><!--</th>-->
<!--                <td>-->
<!--                --><?php //echo $defect->modifycnccId ? html::a($this->createLink('modifycncc', 'view', 'id=' . $defect->modifycnccId), $modifycnccCode, '', "style='color: #0c60e1;'") : ''; ?>
<!--                </td>-->
<!--              </tr>-->

              <tr>
                  <th><?php echo $lang->defect->nextUser;?></th>
                  <td><?php echo zget($users, $defect->dealUser, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->defect->createdBy;?></th>
                  <td><?php echo zget($users, $defect->createdBy, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->defect->createdDate;?></th>
                  <td><?php echo $defect->createdDate!= '0000-00-00 00:00:00' ? $defect->createdDate : '';?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->defect->confirmedBy;?></th>
                  <td><?php echo zget($users, $defect->confirmedBy, '');?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->defect->confirmedDate;?></th>
                  <td><?php echo $defect->confirmedDate!= '0000-00-00 00:00:00' ? $defect->confirmedDate : '';?></td>
              </tr>
              <tr>
                <th><?php echo $lang->defect->dealedBy;?></th>
                <td><?php echo zget($users, $defect->dealedBy, '');?></td>
              </tr>
              <tr>
                <th><?php echo $lang->defect->dealedDate;?></th>
                <td><?php echo $defect->dealedDate!= '0000-00-00 00:00:00' ? $defect->dealedDate : '';?></td>
              </tr>
              <tr>
                <th><?php echo $lang->defect->bugId;?></th>
                <td><?php echo $defect->bugId;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->defect->uatId;?></th>
                <td><?php echo $defect->uatId;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->defect->syncStatus;?></th>
                <td><?php echo zget($lang->defect->syncStatusList,$defect->syncStatus);?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div> 
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->outReview;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class="w-100px"><?php echo $lang->defect->changeStatus;?></th>
                <td><?php echo $defect->changeStatus;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->defect->approverName;?></th>
                  <td><?php echo $defect->approverName;?></td>
              </tr>
<!--              <tr>-->
<!--                  <th class="w-100px">--><?php //echo $lang->defect->approverComment;?><!--</th>-->
<!--                  <td>--><?php //echo $defect->approverComment;?><!--</td>-->
<!--              </tr>-->
              <tr>
                <th class="w-100px"><?php echo $lang->defect->approverDate;?></th>
                <td><?php echo $defect->approverDate != '0000-00-00 00:00:00' ? $defect->approverDate : '';?></td>
              </tr>
              <tr>
                  <th class="w-100px"><?php echo $lang->defect->feedbackNum;?></th>
                  <td><?php echo $defect->feedbackNum;?></td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->defect->consumedTitle;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-100px'><?php echo $lang->defect->nodeUser;?></th>
                <td class='text-center'><?php echo $lang->defect->before;?></td>
                <td class='text-center'><?php echo $lang->defect->after;?></td>
<!--                <td class='text-left'>--><?php //echo $lang->actions;?><!--</td>-->
              </tr>
              <?php foreach($consumeds as $index => $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                <td class='text-center'><?php echo zget($lang->defect->statusList, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->defect->statusList, $c->after, '-');?></td>
<!--                <td class='c-actions text-left'>-->
<!--                  --><?php
//                  if($index == count($consumeds) - 1) common::printIcon('defect', 'statusedit', "defectID={$defect->id}&consumedid={$c->id}", $defect, 'list', 'edit', '', 'iframe', true);
//                  ?>
<!--                </td>-->
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
