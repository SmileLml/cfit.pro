<form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
    <table class='table table-form'>
        <tr>
            <th class='w-120px'><?php echo $lang->review->type;?></th>
            <td class='w-p80'><?php echo html::select('type', $lang->review->typeList, $review->type, "class='form-control chosen' required");?></td>
            <td></td>
        </tr>

        <tr>
            <th>确定<?php echo $lang->review->grade;?></th>
            <td ><?php echo html::select('grade', $gradeList, $review->grade, "class='form-control chosen' required");?></td>
            <td></td>
        </tr>

        <tr class="gradeMeeting hidden">
            <th><?php echo $lang->review->meetingPlanType;?></th>
            <td>
                <?php echo html::radio('meetingPlanType', $lang->review->meetingPlanTypeLabelList, '1');?>
            </td>
            <td></td>
        </tr>

        <tr class="meetingPlanType-1 hidden">
            <th><?php echo $lang->review->meetingPlanList;?></th>
            <td>
                <?php echo html::select('meetingCode', '', $review->meetingCode, "class='form-control chosen' required");?>
            </td>
            <td></td>
        </tr>

        <tr class="meetingPlanType-2 hidden">
            <th><?php echo $lang->review->meetingPlanTime;?></th>
            <?php if($isRenew ==1):?>
            <td>
                <?php echo html::input('meetingPlanTime', '', "class='form-control form-datetime' required");?>
            </td>
            <td></td>
            <?php else:?>
                <td>
                    <?php echo html::input('meetingPlanTime', $review->meetingPlanTime != '0000-00-00 00:00:00' ? $review->meetingPlanTime :'', "class='form-control form-datetime' required");?>
                </td>
                <td></td>
            <?php endif;?>
        </tr>

        <tr class="gradeMeeting hidden">
            <th><?php echo $lang->review->isSkipMeetingResult;?></th>
            <td>
                <?php
                $checked = 2;
                if($review->grade == 'meeting'){
                    $checked = 1;
                }
                ?>
                <div style="width: 20%; float: left;">
                    <?php echo html::radio('isSkipMeetingResult', $lang->review->isSkipMeetingResultLabelList, $checked);?>
                </div>
                <div class="tipMsg-right"><small style="color:#838a9d;">选择“是”，则无需评审主席“确定在线评审结论”，直接进入“会议评审中”流程节点</small></div>
            </td>
            <td>
            </td>
        </tr>

        <tr>
            <th><?php echo $lang->review->reviewer;?></th>
            <td ><?php echo html::select('reviewer', '', $review->reviewer, "class='form-control chosen' multiple required");?></td>
            <td></td>
        </tr>
        <tr>
            <th><?php echo $lang->review->owner;?></th>
            <td>
                <?php echo html::select('owner', $users, $review->owner, "class='form-control chosen'  required");?>
                <div class="tipMsg"><small>修改评审主席可委托其他人承担，修改评审类型可实现组织级评审和部门级评审的转换</small></div>
            </td>
            <td></td>
        </tr>
        <tr>
            <th><?php echo $lang->review->expert;?></th>
            <td><?php echo html::select('expert[]', $users, $review->expert, "class='form-control chosen' multiple");?>
                <div class="tipMsg"><small> <?php echo $lang->review->expertTip?></small></div>
            </td>
            <td>

            </td>
        </tr>
        <?php if($review->type =='dept'):?>
            <tr id='reviewedBy' class='hidden'>
                <th><?php echo $lang->review->reviewedBy;?></th>
                <td>
                    <?php echo html::select('reviewedBy[]', $outsideList1,  $review->reviewedBy, "class='form-control chosen' multiple");?>
                    <div class="tipMsg"><small>  <?php echo $lang->review->reviewedByTip?></small></div>
                </td>
                <td>
                </td>
                <td></td>
            </tr>
            <tr id='outside' class='hidden'>
                <th><?php echo $lang->review->outside;?></th>
                <td>
                    <?php echo html::select('outside[]',$outsideList2, $review->outside, "class='form-control  chosen' multiple");?>
                    <div class="tipMsg"><small> <?php echo $lang->review->outsideTip?></small></div>
                </td>
                <td class=''>
                </td>
                <td></td>
            </tr>
        <?php else:?>
            <tr id='reviewedBy'>
                <th><?php echo $lang->review->reviewedBy;?></th>
                <td>
                    <?php echo html::select('reviewedBy[]', $outsideList1,  $review->reviewedBy, "class='form-control chosen' multiple");?>
                    <div class="tipMsg"><small>  <?php echo $lang->review->reviewedByTip?></small></div>
                </td>
                <td>
                </td>
                <td></td>
            </tr>
            <tr id='outside' >
                <th><?php echo $lang->review->outside;?></th>
                <td>
                    <?php echo html::select('outside[]',$outsideList2, $review->outside, "class='form-control  chosen' multiple");?>
                    <div class="tipMsg"><small> <?php echo $lang->review->outsideTip?></small></div>
                </td>
                <td class=''>
                </td>
                <td></td>
            </tr>
        <?php endif;?>
        <tr>
            <th><?php echo $lang->review->relatedUsers;?></th>
            <td>
                <?php echo html::select('relatedUsers[]', $users, $review->relatedUsers, "class='form-control chosen' multiple");?>
            </td>
            <td></td>
        </tr>
        <tr>
            <th><?php echo $lang->review->deadline;?></th>
            <td ><?php echo html::input('deadline', $review->deadline != '0000-00-00' ? $review->deadline:'', "class='form-date form-control' required");?></td>
            <td></td>
        </tr>


       <!-- <tr>
            <th><?php /*echo $lang->review->consumed;*/?></th>
            <td>
                <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
            </td>
            <td></td>
        </tr>-->
        <tr>
            <th class='w-140px'><?php echo $lang->review->mailto;?></th>
            <td><?php echo html::select('mailto[]', $users, $mailto, "class='form-control chosen' multiple");?></td>
            <td></td>
        </tr>

        <tr>
            <th><?php echo $lang->review->currentComment;?></th>
            <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control'");?></td>
        </tr>
        <tr>
            <td class='text-center' colspan='3'>
                <input type="hidden" name = "status" value="<?php echo $review->status; ?>">
                <input type="hidden" name = "reviewedDate" value="<?php echo helper::now(); ?>">
                <?php echo html::submitButton();?>
            </td>
        </tr>
    </table>
</form>
