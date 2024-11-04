<?php include '../../common/view/header.html.php';?>
<?php $browseLink = $this->session->reviewList ? $this->session->reviewList : inlink('browse', "project=$review->project");?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php common::printBack($browseLink, 'btn btn-secondary');?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $review->id?></span>
      <span class="text"><?php echo $review->title . '<i class="icon-angle-right"></i> ' . $lang->review->report->common?></span>
    </div>
  </div>
</div>
<div class='main-row' id='mainContent'>
  <div class='main-col main-table'>
    <div class='cell'>
      <table class='table table-borderless'>
        <tr><th class='text-center' colspan='8'><?php echo $lang->review->explain;?></th></tr>
        <tr>
          <th><?php echo $lang->review->object;?></th>
          <td colspan='3'>
              <?php
              $object = explode(',', $review->object) ;
              foreach ($object as $item) {
                  echo zget($lang->review->objectList, $item) . " &nbsp";
            };?>
          </td>
          <th><?php echo $lang->review->reviewerCount;?></th>
          <td colspan='3'><?php echo count(explode(',',  trim($review->reviewedBy . $review->owner . $review->expert, ',')));?></td>
        </tr>
        <tr>
          <th><?php echo $lang->review->reviewedDate;?></th>
          <td colspan='3'><?php echo $review->lastReviewedDate;?></td>
          <th><?php echo $lang->review->reviewedHours;?></th>
          <td colspan='3'>
          <?php 
          $consumed = 0;
          foreach($results as $result) $consumed += $result->consumed;
          echo $consumed;
          ?>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->review->issueCount;?></th>
          <td colspan='3'><?php echo count($issues);?></td>
        </tr>
        <tr><th class='text-center' colspan='8'><?php echo $lang->review->record;?></th></tr>
        <?php if(!empty($issues)):?>
        <tr>
          <th colspan='2'><?php echo $lang->idAB;?></th>
          <th colspan='2'><?php echo $lang->review->issues;?></th>
          <th colspan='2'><?php echo $lang->reviewissue->type;?></th>
          <th colspan='2'><?php echo $lang->reviewissue->createdDate;?></th>
        </tr>
        <?php foreach($issues as $issue):?>
        <tr>
          <td colspan='2'><?php echo $issue->id;?></td>
          <td colspan='2'><?php echo html::a($this->createLink('reviewissue', 'view', "id=$issue->id"), $issue->title);?></td>
          <td colspan='2'><?php echo zget($lang->reviewissue->typeList, $issue->type, '');?></td>
          <td colspan='2'><?php echo $issue->createdDate;?></td>
        </tr>
        <?php endforeach;?>
        <?php endif;?>
        <tr>
          <th class='text-center' colspan='6'><?php echo $lang->review->resultExplain;?></th>
          <th class='text-center' colspan='2'><?php echo $lang->review->result;?></th>
        </tr>
        <tr>
          <td colspan='6'><?php echo $lang->review->resultExplainList['pass'];?></td>
          <td rowspan='2' colspan='2' class="text-center status-<?php echo $review->result;?>" style='background: #e3f2fd;'><?php echo zget($lang->review->resultList, $review->result);?></td>
        </tr>
        <tr>
          <td colspan='6'><?php echo $lang->review->resultExplainList['fail'];?></td>
        </tr>
        <tr>
          <th><?php echo $lang->review->reportCreatedBy;?></th>
          <th colspan='3'><?php echo zget($users, $review->createdBy);?></th>
          <th><?php echo $lang->review->owner;?></th>
          <th colspan='3'>
            <?php 
            $owners = explode(',', $review->owner);
            foreach($owners as $account)
            {    
                $account = trim($account);
                if(empty($account)) continue;
                echo zget($users, $account) . " &nbsp;";
            }    
            ?>
          </th>
        </tr>
        <tr>
          <th><?php echo $lang->review->expert;?></th>
          <th colspan='3'>
            <?php 
            $experts = explode(',', $review->expert);
            foreach($experts as $account)
            {    
                $account = trim($account);
                if(empty($account)) continue;
                echo zget($users, $account) . " &nbsp;";
            }    
            ?>
          </th>
          <th><?php echo $lang->review->reviewedBy;?></th>
          <th colspan='3'>
            <?php 
            $reviewedBies = explode(',', $review->reviewedBy);
            foreach($reviewedBies as $account)
            {    
                $account = trim($account);
                if(empty($account)) continue;
                echo zget($outsideList1, $account) . " &nbsp;";
            }    
            ?>
          </th>

        </tr>

          <tr>
              <th><?php echo $lang->review->outside;?></th>
              <th colspan='3'>
                  <?php
                  $experts = explode(',', $review->outside);
                  foreach($experts as $account)
                  {
                      $account = trim($account);
                      if(empty($account)) continue;
                      echo zget($outsideList2, $account) . " &nbsp;";
                  }
                  ?>
              </th>
              <th><?php echo $lang->review->relatedUsers;?></th>
              <th colspan='3'>
                  <?php
                  $reviewedBies = explode(',', $review->relatedUsers);
                  foreach($reviewedBies as $account)
                  {
                      $account = trim($account);
                      if(empty($account)) continue;
                      echo zget($users, $account) . " &nbsp;";
                  }
                  ?>
              </th>
          </tr>

        <?php foreach($results as $result):?>
        <tr>
          <th><?php echo zget($users, $result->reviewer) . '--' . $lang->review->finalOpinion;?></th>
          <th colspan='7'><?php echo $result->opinion;?></th>
        </tr>
        <?php endforeach;?>
      </table>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
