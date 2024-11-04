<div class="cell">
    <div class='detail'>
        <div class='detail-title'><?php echo $lang->review->reviewMeetingSummary;?></div>
        <div class="detail-content article-content ">
            <table class="table ops  table-fixed ">
                <thead>
                <tr>
                    <th class='w-120px' style="vertical-align:middle"><?php echo $lang->review->meetingRealTime;?></th>
                    <td class='w-180px' style="vertical-align:middle"><?php echo $meetingDetailInfo->meetingRealTime;?></td>
                    <th  class='w-70px' style="vertical-align:middle"><?php echo $lang->review->reviewOwner;?></th>
                    <td class='w-80px' style="vertical-align:middle"><?php echo zget($users, $review->meetingInfo->owner);?></td>
                    <th class='w-160px' style="vertical-align:middle"><?php echo $lang->review->meetingConsumed?></th>
                    <td class='w-80px' style="vertical-align:middle"><?php echo $meetingDetailInfo->consumed;?></td>
                    <th class='w-50px' style="vertical-align:middle"><?php echo  $lang->review->author;?></th>
                    <td  class='w-80px' style="vertical-align:middle"><?php echo  zget($users, $review->createdBy);?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?php echo $lang->review->meetingContent;?></th>
                    <td colspan="7">
                        <?php echo strip_tags($meetingDetailInfo->meetingContent);?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->review->realExport;?></th>
                    <td colspan="7">
                        <?php if($meetingDetailInfo->realExportVersion == 2):?>
                            <?php $realExport = explode(',', $meetingDetailInfo->realExport); foreach($realExport as $account) echo ' ' . zget($users, $account);?>
                        <?php else:?>
                            <?php echo $meetingDetailInfo->realExport;?>
                        <?php endif;?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->review->meetingSummary;?></th>
                    <td colspan="7">
                        <?php if($meetingDetailInfo->meetingSummary):?>
                            <?php echo strip_tags($meetingDetailInfo->meetingSummary);?>
                        <?php else:?>
                            <?php if(isset($meetingDetailInfo->meetingSummaryArray)):?>
                            <table>
                                <tr>
                                    <th class='w-200px'>评审问题ID</th>
                                    <th>评审问题描述</th>
                                </tr>
                                <?php foreach ($meetingDetailInfo->meetingSummaryArray as $val):?>
                                    <tr>
                                        <td><?php echo $val->reviewIssueId;?></td>
                                        <td><?php echo $val->desc;?></td>
                                    </tr>
                                <?php endforeach;?>

                            </table>
                           </td>
                        <?php endif;?>
                    <?php endif;?>
                </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>