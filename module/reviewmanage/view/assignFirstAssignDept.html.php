<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class='table table-form'>
        <tr>
            <th class='w-120px'><?php echo $lang->review->isFirstReview;?></th>
            <td class='w-p55-f'>
                <?php
//                    $checked = 1;
//                    if(in_array($review->type, $lang->review->defSkipFirstReviewTypeList)){
//                        $checked = 2;
//                    }
                ?>
                <?php echo html::radio('isFirstReview', $lang->review->isFirstReviewLabelList, $review->isFirstReview, "onclick='setIsFirstReview(this.value);'");?>
                <span class="text-error red">&nbsp;&nbsp;<?php echo $lang->review->firstReviewSkipTipMsg;?></span>
            </td>
            <td class='w-120px'></td>
        </tr>

        <tr>
            <th><?php echo $lang->review->projectType;?></th>
            <td>
                <?php echo html::select('projectTypeTemp', $lang->projectplan->typeList, isset($projectPlan->type) ? $projectPlan->type: 0, "class='form-control chosen' disabled required");?>
                <?php echo html::hidden('projectType', isset($projectPlan->type) ? $projectPlan->type: 0);?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th><?php echo $lang->review->isImportant;?></th>
            <td>
                <?php
                $checked = 2;
                if(isset($projectPlan->isImportant) && ($projectPlan->isImportant == '1')){
                    $checked = 1;
                }
                ?>
                <?php echo html::radio('isImportantTemp', $lang->review->isImportantList, $checked, "disabled");?>
                <?php echo html::hidden('isImportant', $checked);?>

            </td>
            <td></td>
        </tr>

        <tr>
            <th><?php echo $lang->review->grade;?></th>
            <td>
                <?php echo html::select('grade',  $gradeList,  $defGrade, "class='form-control chosen' required");?>
                <?php echo html::hidden('reviewType', isset($review->type) ? $review->type: 0);?>
            </td>
            <td></td>
        </tr>

        <tr class="firstReview1 hidden">
            <th><?php echo $lang->review->trialDept;?></th>
            <td>
                <?php

                    foreach($depts as $dept)
                    {
                        echo "<div class='checkbox-primary'><input type='checkbox' name='depts[]' onchange='ajaxGetManage($dept->id)'  value='$dept->id' >";
                        echo "<label>$dept->name &nbsp;<span class='review-name'>" . zget($users, $dept->firstReviewer, '') . "</span></label>";
                        echo "</div>";
                    }
                ?>
            </td>
            <td></td>
        </tr>

        <tr class="firstReview0 hidden">
            <th><?php echo $lang->review->deadline;?></th>
            <td>
                <?php echo html::input('deadline', $review->deadline != '0000-00-00' ? $review->deadline:'', "class='form-date form-control' required ");?>
            </td>
            <td></td>
        </tr>


       <!-- <tr>
            <th><?php /*echo $lang->review->consumed;*/?></th>
            <td>
                <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
            </td>
            <td></td>
        </tr>-->


        <tr class="firstReview0 hidden">
            <th><?php echo $lang->review->owner;?></th>
            <td><?php echo html::select('owner', $users, $review->owner, "class='form-control chosen' required");?></td><td></td>
        </tr>
        <tr>
            <th class='w-140px'><?php echo $lang->review->mailto;?></th>
            <td colspan="2"><?php echo html::select('mailto[]', '', '', "class='form-control chosen' multiple");?></td>
        </tr>

        <tr>
            <th><?php echo $lang->review->currentComment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
        </tr>
        <tr>
            <td class='text-center' colspan='3'>
                <input type="hidden" name = "status" value="<?php echo $review->status; ?>">
                <input type="hidden" name = "reviewedDate" value="<?php echo helper::now(); ?>">
                <?php echo html::submitButton('', '', 'btn btn-wide btn-primary assignFirstAssignDept');?>
            </td>
        </tr>
    </table>
</form>
<?php
    js::set('isIncludeNotNeedFirstReviewObject', $isIncludeNotNeedFirstReviewObject); //是否包含不需要初审的对象
    js::set('firstReviewSkipTipMsg', $lang->review->firstReviewSkipTipMsg); //不需要初审提示信息
?>
<script>
    /**
     * 确定初审部门提交验证
     */
    $('.assignFirstAssignDept').click(function (){
        var isFirstReview = $("input[name='isFirstReview']:checked").val();
        if(isFirstReview == '1' && isIncludeNotNeedFirstReviewObject){
            bootbox.confirm(firstReviewSkipTipMsg, function (result){
                if((result)){
                    $('button[data-bb-handler="cancel"]').click();
                    $('.assignFirstAssignDept').submit();
                    return false;
                }
            });
            return false;
        }else {
            return true;
        }

    });
</script>
