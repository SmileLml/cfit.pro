<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
#ganttView {height: 340px!important;}
.review-result {white-space: nowrap;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a(inlink('browse', "project=$review->project"), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title . $lang->arrow . $lang->review->audit;?></span>
    </div>
  </div>
</div>
<form class='form-ajax' method='post' id="assessForm">
  <div id='reviewRow' class='main-row fade split-row in'>
    <div class='side-col' data-min-width='550'>
      <div class='cell scrollbar-hover'>
      <div class="btn-toolbar">
      <?php if($review->category == 'PP') echo html::a('###', $lang->review->fullScreen, '', 'class="btn btn-primary btn-sm" id="fullScreenBtn"')?>
      </div>
      <?php if($review->category == 'PP') include './gantt.html.php';?>
      <?php
      if($review->category == 'SRS' || $review->category == 'URS' || $review->category == 'HLDS' || $review->category == 'DDS' || $review->category == 'DBDS' || $review->category == 'ADS' || $review->category == 'ITTC' || $review->category == 'STTC')
      {
          echo '<div class="tab-pane active" id="book">';
          echo '<ul data-name="docsTree" data-ride="tree" data-initial-state="preserve" class="tree no-margin" data-idx="0">';
          if(isset($bookID) and $bookID)
          {
              include './book.html.php';
          }
          else
          {
              echo '<li>';
              echo "<i class='icon icon-folder-o'></i> " . zget($lang->review->objectList, $review->category);
              echo $tree;
              echo '</li>';
          }
          echo '</ul>';
          echo '</div>';
      }
      ?>
      </div>
      <div class='cell main-table scrollbar-hover'>
      <?php foreach($resultList as $issue):?>
        <table class="table table-bordered" style="margin-bottom: 10px;">
          <tbody>
          <tr>
            <td><strong><?php echo $lang->review->reviewedBy;?></strong></td>
            <td><span class="label label-primary label-outline"><?php echo $issue->username;?></span></td>
            <td><strong><?php echo $lang->review->result?></strong></td>
            <td><span class="label label-badge label-<?php echo zget($lang->review->resultLable, $issue->result);?>"><?php echo zget($lang->review->resultList, $issue->result);?></span></td>
            <td><strong><?php echo $lang->review->createdDate;?></strong></td>
            <td><?php echo $issue->createdDate;?></td>
            <td><strong><?php echo $lang->review->consumed;?></strong></td>
            <td><?php echo $issue->consumed;?></td>
          </tr>
          <tr>
            <td><strong><?php echo $lang->review->finalOpinion;?></strong></td>
            <td colspan="5"><span class="hl-info"><?php echo $issue->opinion;?></span></td>
          </tr>
          <tr>
            <td><strong><?php echo $lang->review->issueList?></strong></td>
            <td colspan="7">
            <?php foreach ($issue->issue as $item):?>
            <?php echo html::a($this->createLink('reviewissue', 'view', "project=$review->project&issueID=$item->id"), $item->title);?>【<?php echo zget($lang->reviewissue->statusList, $item->status);?>】<br>
            <?php endforeach;?>
            </td>
          </tr>
          </tbody>
        </table>
      <?php endforeach;?>
      </div>
    </div>
    <div class='col-spliter' id="splitLine"></div>
    <div class='main-col' data-min-width='600' id="issueList">
      <div class='cell scrollbar-hover' id='reviewcl'>
        <div class="detail-title"><?php echo (!empty($issues) && !empty($result) && $result->remainIssue == 0) ? $lang->review->lastIssue : $lang->review->reviewcl;?></div>
        <div class="detail-content article-content">
          <?php if(!empty($issues) && !empty($result) && $result->remainIssue == 0):?>
          <table class='table reviewcl'>
            <thead>
              <tr>
                 <th class='text-center'><?php echo $lang->reviewissue->title;?></th>
                 <th><?php echo $lang->reviewissue->opinion;?></th>
                 <th><?php echo $lang->reviewissue->status;?></th>
                 <th><?php echo $lang->reviewissue->hasResolved;?></th>
                 <th><?php echo $lang->review->opinion;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($issues as $issue):?>
              <tr>
                 <td class='text-center'><strong><?php echo html::a($this->createLink('reviewissue', 'view', "id=$issue->id"), $issue->title);?></strong></td>
                 <td><?php echo $issue->opinion;?></td>
                 <td><?php echo zget($lang->reviewissue->statusList, $issue->status);?></td>
                 <td><?php echo html::radio("resolved[$issue->id]", $lang->review->resolvedList, 1, "class='resolved'", 'block');?></td>
                 <td><?php echo html::input("issueOpinion[$issue->id]", '', "class='form-control opinion' readonly");?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <?php else:?>
          <table class='table reviewcl'>
            <thead>
              <tr>
                 <th class='text-center w-90px'><?php echo $lang->review->listCategory;?></th>
                 <th class='w-150px'><?php echo $lang->review->listItem;?></th>
                 <th><?php echo $lang->review->listTitle;?></th>
                 <th><?php echo $lang->review->listResult;?></th>
                 <th><?php echo $lang->review->opinion;?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($cmcl as $category => $list):?>
              <tr>
                 <td rowspan=<?php echo count($list);?> class='text-center'><strong><?php echo zget($typeList, $category);?></strong></td>
                 <?php $i = 0 ;?>
                 <?php foreach($list as $data):?>
                 <?php $i++ ;?>
                 <?php if($i != 1) echo "<tr>"?>
                 <td title='<?php echo zget($items, $data->title);?>'><?php echo zget($items, $data->title);?></td>
                 <td><?php echo html::a($this->createLink('cmcl', 'view', "id=$data->id"), $data->contents, '', "title=$data->contents");?></td>
                 <td><?php echo html::radio("issueResult[$data->id]", $lang->review->checkList, '1', "class='issueResult'", 'block');?></td>
                 <td class="issue-opintion">
                 <?php echo html::input("issueOpinion[$data->id]", isset($data->opinion) ? $data->opinion : '', "class='form-control opinion' readonly");?>
                   <div class="input-group opinionDate hidden" style="margin-top:5px;">
                     <span class="input-group-addon"><?php echo $lang->review->opinionDate;?></span>
                     <?php echo html::input("opinionDate[$data->id]", isset($data->opinionDate) ? $data->opinionDate : '', "class='form-control form-date'");?>
                   </div>
                 </td>
                 <?php if($i != 1) echo "</tr>"?>
                 <?php endforeach;?>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
          <?php endif;?>
        </div>
      </div>
      <div class='cell review-footer'>
        <table class='table table-borderless'>
          <tr>
            <th class='w-80px'><?php echo $lang->review->auditResult;?></th>
            <td class='review-result'><?php echo html::radio('result', $lang->review->auditResultList, isset($result->result) ? $result->result : 'pass');?></td>
            <td>
              <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->review->auditOpinion;?></span>
              <?php echo html::input('opinion', isset($result->opinion) ? $result->opinion : '', "class='form-control'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->review->auditedDate;?></th>
            <td><?php echo html::input('createdDate', helper::today(), 'class="form-control form-date"');?></td>
            <td>
              <div class='input-group'>
              <span class='input-group-addon'><?php echo $lang->review->consumed;?></span>
              <?php echo html::input('consumed', isset($result->consumed) ? $result->consumed : 0, "class='form-control'");?>
              <span class='input-group-addon'>h</span>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan='3' class='text-center'>
            <?php echo html::hidden('mode', empty($result) ? 'new' : 'edit');?>
            <?php echo html::submitButton();?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</form>
<style>
.review-footer{margin-top: 10px; height: 165px;}
.review-footer table th{vertical-align: middle}
.reviewcl td{padding: 4px 10px !important;}
</style>
<?php js::set('stopSubmit', $lang->review->stopSubmit);?>
<?php include '../../common/view/footer.html.php';?>
