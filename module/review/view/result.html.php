<?php include '../../common/view/header.html.php'?>
<div id="mainContent" class="main-row">
  <div class="main-header">
    <h2>
      <span class="label label-id"><?php echo $review->id;?></span>
      <span title="<?php echo $review->title;?>"><?php echo $review->title;?></span>
    </h2>
  </div>
  <div class="main-col col-12">
    <div class="main-table">
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
                <?php echo html::a($this->createLink('reviewissue', 'view', "project=$projectID&issueID=$item->id"), $item->title);?>【<?php echo zget($lang->reviewissue->statusList, $item->status);?>】<br>
            <?php endforeach;?>
            </td>
          </tr>
          </tbody>
        </table>
      <?php endforeach;?>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php'?>
