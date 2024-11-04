<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->projectplan->review . ' - ' . zget($lang->projectplan->reviewList, $plan->reviewStage);?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-110px'><?php echo $lang->projectplan->reviewResult;?></th>
            <td colspan='3'>
            <?php echo html::radio('result', $lang->projectplan->reviewResultList, "");?>
            </td>
          </tr>
          <tr>
            <th class='w-110px'><?php echo $lang->projectplan->reviewComment;?></th>
            <td colspan='3'>
            <?php echo html::textarea('comment', '', "class='form-control'");?>
            </td>
          </tr>
          <?php if($plan->reviewStage == 2):?>
          <tr>
            <th><?php echo $lang->projectplan->involved;?></th>
            <td colspan='3' class='required'><?php echo html::select('involved[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
    <?php if($nodes[0]->status != 'wait' and $nodes[0]->status != 'pending'):?>
    <hr class="small">
    <p><?php echo $lang->projectplan->reviewComment;?></p>
    <table class="table ops">
      <tr>
        <th><?php echo $lang->projectplan->node;?></th>
        <td><?php echo $lang->projectplan->reviewer;?></td>
        <td><?php echo $lang->projectplan->reviewResult;?></td>
        <td><?php echo $lang->projectplan->reviewComment;?></td>
      </tr>
      <?php if($nodes[0]->status != 'wait' and $nodes[0]->status != 'pending'):?>
      <tr>
        <th rowspan="<?php echo $nodes[0]->reviewedCount;?>"><?php echo $lang->projectplan->managerOpinion;?></th>
        <td><?php echo zget($users, $nodes[0]->reviewers[0]->reviewer);?></td>
        <td><?php echo zget($lang->projectplan->reviewResultList, $nodes[0]->reviewers[0]->status);?></td>
        <td><?php echo $nodes[0]->reviewers[0]->comment?></td>
      </tr>
      <?php endif;?>
      <?php if($nodes[1]->status != 'wait' and $nodes[1]->status != 'pending'):?>
      <tr>
        <th rowspan="<?php echo $nodes[1]->reviewedCount;?>"><?php echo $lang->projectplan->leaderOpinion;?></th>
        <td><?php echo zget($users, $nodes[1]->reviewers[0]->reviewer);?></td>
        <td><?php echo zget($lang->projectplan->reviewResultList, $nodes[1]->reviewers[0]->status);?></td>
        <td><?php echo $nodes[1]->reviewers[0]->comment?></td>
      </tr>
      <?php endif;?>
      <?php if($nodes[2]->status != 'wait' and $nodes[2]->status != 'pending'):?>
      <tr>
        <th rowspan="<?php echo $nodes[2]->reviewedCount;?>"><?php echo $lang->projectplan->deptsOpinion;?></th>
        <td><?php echo zget($users, $nodes[2]->reviewers[0]->reviewer);?></td>
        <td><?php echo zget($lang->projectplan->reviewResultList, $nodes[2]->reviewers[0]->status);?></td>
        <td><?php echo $nodes[2]->reviewers[0]->comment?></td>
      </tr>
      <?php endif;?>
      <?php if($nodes[3]->status != 'wait' and $nodes[3]->status != 'pending'):?>
      <tr>
        <th rowspan="<?php echo $nodes[3]->reviewedCount;?>"><?php echo $lang->projectplan->gmOpinion;?></th>
        <td><?php echo zget($users, $nodes[3]->reviewers[0]->reviewer);?></td>
        <td><?php echo zget($lang->projectplan->reviewResultList, $nodes[3]->reviewers[0]->status);?></td>
        <td><?php echo $nodes[3]->reviewers[0]->comment?></td>
      </tr>
      <?php endif;?>
    </table>
    <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
