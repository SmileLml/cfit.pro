<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $curPendingNode->nodeCode == 'deptsSign' ? zget($lang->projectplan->submitnodeCodeDesc, $curPendingNode->nodeCode) : $lang->projectplan->review . ' - ' . zget($lang->projectplan->submitnodeCodeDesc, $curPendingNode->nodeCode);?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <?php if($curPendingNode->nodeCode == 'deptsSign'):?>
          <tr class="hidden">
            <th class='w-110px'><?php echo $lang->projectplan->reviewResult;?></th>
            <td colspan='3'>
            <?php echo html::radio('result', $lang->projectplan->reviewResultList, "pass");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->projectplan->involved;?><i title="<?php echo $lang->projectplan->reviewTip;?>" class="icon icon-help"></i></th>
            <td colspan='3' class='required'><?php echo html::select('involved[]', $deptUsers, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <?php elseif(in_array($curPendingNode->nodeCode,$lang->projectplan->reviewchangeNode)):?>
          <tr>
            <th class='w-110px'><?php echo $lang->projectplan->reviewResult;?></th>
            <td colspan='3'>
            <?php echo html::radio('result', $lang->projectplan->reviewResultList, "");?>
            </td>
          </tr>
              <tr class="hidden" id="sourceallocation">
                  <th><?php echo $lang->projectplan->involved;?><i title="<?php echo $lang->projectplan->reviewTip;?>" class="icon icon-help"></i></th>
                  <td colspan='3' class='required'><?php echo html::select('involved[]', $deptUsers, '', "class='form-control chosen' multiple");?></td>
              </tr>
          <?php if($curPendingNode->nodeCode == 'deptLeader'):?>
              <tr>
                  <th><?php echo $lang->projectplan->PM;?><i title="<?php echo $lang->projectplan->pmTip;?>" class="icon icon-help"></i></th>
                  <td colspan='3' class='required'><?php echo html::select('owner', $deptUsers, $plan->owner, "class='form-control chosen'");?></td>
              </tr>
          <?php endif;?>
          <?php else:?>
              <tr>
                  <th class='w-110px'><?php echo $lang->projectplan->reviewResult;?></th>
                  <td colspan='3'>
                      <?php echo html::radio('result', $lang->projectplan->reviewResultList, "");?>
                  </td>
              </tr>
          <?php endif;?>
          <tr>
            <th class='w-110px'><?php echo $curPendingNode->nodeCode == 'deptsSign' ? $lang->projectplan->involvedComment : $lang->projectplan->reviewComment;?></th>
            <td colspan='3' id="commentWrap">
            <?php echo html::textarea('comment', '', "class='form-control'");?>
            </td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='4'><?php echo html::submitButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
    <?php if(!empty($bookNodes)):?>
    <hr class="small">
    <p><?php echo $lang->projectplan->bookReviewComment;?></p>
    <table class="table ops">
      <tr>
        <th class="w-200px"><?php echo $lang->projectplan->node;?></th>
        <td class="w-100px"><?php echo $lang->projectplan->reviewer;?></td>
        <td class="w-150px"><?php echo $lang->projectplan->involved;?></td>
        <td class="w-130px"><?php echo $lang->projectplan->reviewResult;?></td>
        <td><?php echo $lang->projectplan->reviewComment;?></td>
      </tr>
        <?php
        foreach ($bookNodes as $bnode){
            $reviewedCount = count($bnode->reviewers);
            foreach ($bnode->reviewers as $k=>$bnReviewer){
                if($k == 0){
                    ?>
                    <tr>
                        <th rowspan="<?php echo $reviewedCount; ?>"><?php echo zget($lang->projectplan->submitnodeCodeDesc,$bnode->nodeCode); ?></th>
                        <td><?php echo zget($users, $bnReviewer->reviewer, ''); ?></td>
                        <td>
                            <?php
//                            if($bnode->nodeCode == 'deptsSign'){
                                $involved = json_decode($bnReviewer->extra);
                                if ($involved && $involved->involved) {
                                    foreach ($involved->involved as $u) echo zget($users, $u, '') . ' ';
                                }
//                            }
                            ?>
                        </td>
                        <td><?php echo zget($lang->projectplan->reviewStatusList, $bnReviewer->status, ''); ?></td>
                        <td><?php echo $bnReviewer->comment ?></td>
                    </tr>
                    <?php
                }else{
                    ?>
                    <tr>

                        <td><?php echo zget($users, $bnReviewer->reviewer, ''); ?></td>
                        <td><?php
//                            if($bnode->nodeCode == 'deptsSign'){
                                $involved = json_decode($bnReviewer->extra);
                                if ($involved && $involved->involved) {
                                    foreach ($involved->involved as $u) echo zget($users, $u, '') . ' ';
                                }
//                            }
                            ?></td>
                        <td><?php echo zget($lang->projectplan->reviewStatusList, $bnReviewer->status, ''); ?></td>
                        <td><?php echo $bnReviewer->comment ?></td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>
    <?php endif;?>
  </div>
</div>
<script>
    $(function(){
        $("input[type=radio][name=result]").change(function (){


            if(this.value == 'reject'){
                $("#commentWrap").addClass('required');
                $("#sourceallocation").addClass("hidden")
            }else{
                $("#commentWrap").removeClass('required');
                $("#sourceallocation").removeClass("hidden")
            }
        });
    })
</script>
<?php include '../../../common/view/footer.html.php';?>
