<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="height: 450px;">
    <div class='center-block'>
        <div class='main-header'>
            <h2>
                <span class='label label-id'><?php echo $review->id;?></span>
                <span><?php echo $review->title;?></span>

                <small><?php echo $lang->arrow . isset($reviewNode->statusStageName) ? $reviewNode->statusStageName : '';?></small>
            </h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class='table table-form'>
                <?php
                foreach($reviewerInfos as $data):
                    $extraInfo =  json_decode($data->extra, true);
                    ?>
                    <?php
                    $disabled1 = empty($data->nodeCode) || $data->nodeCode == 'firstAssignReviewer' || $data->nodeCode == 'firstAssignDept' ? 'disabled' : '';
                    $disabled2 = $data->comment == '系统自动关闭'  ? 'disabled' : '';
                    ?>
                    <tr>
                        <td><?php echo $lang->review->reviewPerson.'：'.$users[$data->reviewer];?></td>
                    </tr>
                    <tr>
                        <th  class='w-150px'><?php echo $lang->review->currentNodeDealUsers;?></th>
                        <td colspan='2'><?php echo html::select('reviewers[]', $users, $data->reviewer, "class='form-control chosen' ");?></td>
                        <td></td>
                    </tr>

                    <?php if(in_array($data->nodeCode,  $lang->review->adviceGradeNodeCodes) && isset($extraInfo['grade'])):?>
                    <tr>
                        <th  class='w-150px'><?php echo $lang->review->adviceGrade;?></th>
                        <td colspan='2'>
                            <?php echo html::select('grade[]', $adviceGradeList, $extraInfo['grade'], "class='form-control chosen' required");?>
                        </td>
                        <td></td>
                    </tr>

                <?php endif;?>

                    <?php if($data->nodeCode != 'meetingReview'):?>
                    <tr>
                        <th  class='w-150px'><?php echo $lang->review->currentNodeExtras;?></th>
                        <td colspan='2'><?php echo html::select('results[]',$select, $data->status, "class='form-control chosen' $multiple $disabled1");?></td>
                        <td></td>
                    </tr>
                <?php endif;?>
                    <tr>
                        <th  class='w-150px'><?php echo $lang->review->currentNodeComments;?></th>
                        <td colspan='2'><?php echo html::input('comment[]',strip_tags($data->comment),"class='form-control' $disabled2");?></td>
                        <td></td>
                    </tr>
                    <input type="hidden" name = "reviewerID[]" value="<?php echo $data->reviewerID; ?>">
                <?php endforeach;?>
                <tr>
                    <td class='text-center' colspan='3'>
                        <?php echo html::submitButton();?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
