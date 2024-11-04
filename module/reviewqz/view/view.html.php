<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .table-fixed td{
        white-space: unset!important;
    }
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php if(!isonlybody()):?>
        <?php echo html::backButton('<i class="icon icon-back icon-sm"></i>' . $lang->goback , '','btn btn-secondary');?>
      <div class="divider"></div>
    <?php endif;?>
    <div class="page-title">
        <span class="label label-id"><?php  printf('%03d', $review->id);?></span>
        <span class="text"><?php echo $review->title;?></span>
    </div>
  </div>
    <div class="pull-right">
        <div class="btn-toolbar pull-right">
            <?php
            $params = "reviewID=$review->id";
            ?>
            <?php
                echo '<a href="javascript:void(0)"  class="btn btn-primary" onclick="$.zui.messager.danger(\''.$lang->reviewissueqz->issueCreateMsgTip.'\', {html: \'true\'});" title="'.$lang->reviewqz->issueCreate.'" data-app="issueCreate"><i class="icon icon-plus"></i>'.$lang->reviewqz->issueCreate.'</a>';
            ?>
        </div>
    </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
        <div class="detail">
            <div class="detail-title"><?php echo $lang->reviewqz->content;?></div>
            <div class="detail-content article-content">
              <?php echo $review->content;?>
            </div>
        </div>
    </div>

      <div class="cell">
         <div class="detail">
            <div class="detail-title"><?php echo $lang->reviewqz->remark;?></div>
            <div class="detail-content article-content">
                <?php echo $review->remark;?>
            </div>
         </div>
      </div>

      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->reviewqz->relationFiles;?></div>
              <div class="detail-content article-content">
                  <?php
                  if($review->files){
                      echo $this->fetch('file', 'printFiles', array('files' => $review->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true));
                  }else{
                      echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                  }
                  ?>
              </div>
          </div>
      </div>

      <?php if(!empty($exportsFeedbackList)):?>
          <div class="cell">
              <div class="detail">
                  <div class="detail-title"><?php echo $lang->reviewqz->feedbackInfo;?></div>
                  <div class="detail-content article-content">
                      <table class="table ops  table-fixed ">
                          <thead>
                          <tr>
                              <th class='w-80px'><?php echo $lang->reviewqz->feedbackNum;?></th>
                              <th><?php echo $lang->reviewqz->feedbackExports;?></th>
                              <th><?php echo $lang->reviewqz->comment;?></th>
                              <th class='w-120px'><?php echo $lang->reviewqz->feedbackResult;?></th>
                              <th class='w-160px'><?php echo $lang->reviewqz->rejectReason;?></th>
                          </tr>
                          </thead>
                          <tbody>
                              <?php foreach ($exportsFeedbackList as $val):?>
                                  <tr>
                                      <td><?php echo $val->num;?></td>
                                      <td><?php echo $val->expertList;?></td>
                                      <td><?php echo $val->comment;?></td>
                                      <td><?php echo $val->result;?></td>
                                      <td><?php echo $val->reason;?></td>
                                  </tr>
                              <?php endforeach;?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif;?>

      <?php if(!empty($exportsReviewResultList)):?>
          <div class="cell">
              <div class="detail">
                  <div class="detail-title"><?php echo $lang->reviewqz->reviewInfo;?></div>
                  <div class="detail-content article-content">
                      <table class="table ops  table-fixed ">
                          <thead>
                          <tr>
                              <th class='w-80px'><?php echo $lang->reviewqz->reviewer;?></th>
                              <th class='w-120px'><?php echo $lang->reviewqz->reviewResult;?></th>
                              <th><?php echo $lang->reviewqz->reviewInfo;?></th>
                              <th class='w-160px'><?php echo $lang->reviewqz->reviewTime;?></th>
                          </tr>
                          </thead>
                          <tbody>
                          <?php foreach ($exportsReviewResultList as $val):?>
                              <tr>
                                  <td><?php echo zget($users, $val->reviewer); ?></td>
                                  <td><?php echo zget($lang->review->statusNameList, $val->status);?></td>
                                  <td><?php echo $val->comment;?></td>
                                  <td><?php echo $val->reviewTime;?></td>
                              </tr>
                          <?php endforeach;?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      <?php endif;?>

      <?php $actionFormLink = $this->createLink('action', 'comment', "objectType={$objectType}&objectID=$review->id");?>

    <div class="cell">
        <?php include '../../common/view/action.html.php';?>
    </div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php
        $browseLink = $this->session->reviewqzList ? $this->session->reviewqzList : inlink('browse');
        ?>
        <?php common::printBack($browseLink);?>
        <div class='divider'></div>
        <?php
        $submitClass = common::hasPriv('reviewqz', 'submit') && $isAllowSubmit['result'] ? 'btn' : 'btn disabled';
        $params = "id=$review->id";
        common::printIcon('reviewqz', 'assignExports', $params, $review, 'list', 'hand-right', '', 'iframe', true, '', $this->lang->reviewqz->assignExports);
        common::printIcon('reviewqz', 'confirm', $params, $review, 'list', 'play', '', 'iframe', true, '', $this->lang->reviewqz->confirm);
        common::printIcon('reviewqz', 'feedback', $params, $review, 'list', 'feedback', '', 'iframe', true, '', $this->lang->reviewqz->feedback);
        echo html::a("javascript:void(0);", '<i class="icon-glasses"></i>', '', "title='{$lang->reviewqz->submit}' class='{$submitClass}' onClick='checkSubmit(this);' node-val='".$review->id."'");
        common::printIcon('reviewqz', 'submit', $params, $review, 'button', 'glasses', '', 'iframe hidden', true, "id='submit_".$review->id."'", $this->lang->reviewqz->submit);
        common::printIcon('reviewqz', 'change', $params, $review, 'list', 'time', '', 'iframe', true, '', $this->lang->reviewqz->change);
        ?>
      </div>
    </div>
  </div>

  <div class="side-col col-4">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->reviewqz->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tbody>
              <tr>
                <th class='w-120px'><?php echo $lang->reviewqz->applicant;?></th>
                <td><?php echo $review->applicant;?></td>
              </tr>
              <tr>
                <th><?php echo $lang->reviewqz->applicationTime;?></th>
                <td><?php echo $review->applicationTime;?></td>
              </tr>
              <tr>
                <th class='w-120px'><?php echo $lang->reviewqz->applicationDept;?></th>
                <td>
                    <?php echo $review->applicationDept;?>
                </td>
              </tr>
              <tr>
                <th><?php echo $lang->reviewqz->deptManager;?></th>
                <td><?php echo $review->deptManager;?></td>
              </tr>

              <tr>
                <th><?php echo $lang->reviewqz->isProject;?></th>
                <td>
                <?php echo zget($lang->reviewqz->isProjectList, $review->isProject);?>
                </td>
              </tr>

              <tr>
                <th><?php echo $lang->reviewqz->project;?></th>
                <td><?php echo $review->project;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->type;?></th>
                  <td><?php echo zget($lang->reviewqz->typeList, $review->type);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->reviewCenter;?></th>
                  <td><?php echo zget($lang->reviewqz->reviewCenterList, $review->reviewCenter);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->owner;?></th>
                  <td><?php echo $review->owner;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->planJinkeExports;?></th>
                  <td><?php echo $review->planJinkeExports;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->review_method;?></th>
                  <td><?php echo zget($lang->reviewqz->gradeList, $review->review_method);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->timeInterval;?></th>
                  <td><?php echo zget($lang->reviewqz->timeIntervalNameList, $review->timeInterval);?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->planReviewMeetingTime;?></th>
                  <td><?php echo $review->planReviewMeetingTime  == '0000-00-00 00:00:00' ? '':$review->planReviewMeetingTime;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->planFeedbackTime;?></th>
                  <td><?php echo $review->planFeedbackTime  == '0000-00-00 00:00:00' ? '':$review->planFeedbackTime;?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->reviewqz->verifier;?></th>
                  <td><?php echo $review->verifier;?></td>
              </tr>
              <tr>
                  <th><?php echo $lang->reviewqz->verifierTime;?></th>
                  <td><?php echo $review->verifierTime == '0000-00-00 00:00:00' ? '':$review->verifierTime;?></td>
              </tr>

              <tr>
                  <th><?php echo $lang->reviewqz->confirmJoinDeadLine;?></th>
                  <td><?php echo $review->confirmJoinDeadLine == '0000-00-00 00:00:00' ? '':$review->confirmJoinDeadLine;?></td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>

      <div class="cell">
          <div class="detail">
              <div class="detail-title"><?php echo $lang->reviewqz->planExports;?></div>
              <div class='detail-content article-content'>
                  <table class="table ops  table-fixed ">
                      <thead>
                      <tr>
                          <th class='w-120px'><?php echo $lang->reviewqz->realName;?></th>
                          <th><?php echo $lang->reviewqz->isJoinReview;?></th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php if(empty($planExportsList)):?>
                          <tr>
                              <td colspan="2" style="text-align: center;"><?php echo $lang->noData;?></td>
                          </tr>
                      <?php else:?>
                          <?php foreach ($planExportsList as $val):?>
                              <tr>
                                  <td><?php echo zget($users, $val->reviewer); ?></td>
                                  <td><?php echo zget($lang->reviewqz->meetjoinList, $val->status, '');?></td>
                              </tr>
                          <?php endforeach;?>
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
                <th class='w-100px'><?php echo $lang->consumed->nodeUser;?></th>
                  <!--
                <td class='text-right'><?php echo $lang->consumed->consumed;?></td>
                -->
                <td class='text-center'><?php echo $lang->consumed->before;?></td>
                <td class='text-center'><?php echo $lang->consumed->after;?></td>
              </tr>
              <?php foreach($review->consumed as $c):?>
              <tr>
                <th class='w-100px'><?php echo zget($users, $c->account, '');?></th>
                  <!--
                <td class='text-right'><?php echo $c->consumed . ' ' . $lang->hour;?></td>
                -->
                <td class='text-center'><?php echo zget($lang->reviewqz->browseStatus, $c->before, '-');?></td>
                <td class='text-center'><?php echo zget($lang->reviewqz->browseStatus, $c->after, '-');?></td>
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
