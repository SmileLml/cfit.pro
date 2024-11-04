 <tr class="hidden">
        <th class='w-150px'><?php echo $lang->review->result;?></th>
        <td>
            <?php echo html::select('result', $lang->review->reviewConclusionList, 'passNoNeedEdit', "class='form-control chosen' required");?>
        </td>
        <td></td>
    </tr>
    <tr class="hidden">
        <th class='w-150px'><?php echo $lang->review->reviewedDate;?></th>
        <td>
            <?php echo html::input('reviewedDate', helper::now(), "class='form-control form-date' required ");?>
        </td>
        <td>
            <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->review->meetingRealTime;?></span>
                <?php echo html::input('meetingRealTime', '', "class='form-control form-datetime' required");?>
            </div>
        </td>
    </tr>


    <tr>
        <th><?php echo $lang->review->realExport;?></th>
        <td colspan="2">
            <?php echo html::input('realExport', '', "class='form-control' required");?>
        </td>
    </tr>
     <tr>
         <th><?php echo $lang->review->consumed;?></th>
         <td>
             <?php echo html::input('consumed', '', "class='form-control' required");?>
         </td>
         <td>
             <div class='input-group'>
                 <span class='input-group-addon'><?php echo $lang->review->meetingConsumed;?></span>
                 <?php echo html::input('meetingConsumed', '', "class='form-control' required");?>
             </div>
         </td>
     </tr>
    <tr>
        <th><?php echo $lang->review->meetingContent;?></th>
        <td colspan='2'>
            <?php echo html::textarea('meetingContent', '', "class='form-control' required");?>
        </td>
    </tr>

    <tr>
        <th><?php echo $lang->review->meetingSummary;?></th>
        <td colspan='2'>
            <?php echo html::textarea('meetingSummary', '', "class='form-control' required");?>
        </td>
    </tr>

    <tr>
        <th><?php echo $lang->review->mailto;?></th>

        <td colspan="2">
            <?php echo html::select('mailto[]', $users, $mailto, "class='form-control chosen' multiple");?>
        </td>

    </tr>

    <tr>
        <th><?php echo $lang->comment ;?></th>
        <td colspan='2'>
            <?php echo html::textarea('comment', '', "class='form-control' placeholder=' ".htmlspecialchars($lang->review->commenttip)."'");?>
        </td>
    </tr>
    <tr>
        <td class='text-center' colspan='3'>
            <?php echo html::submitButton();?>
        </td>
    </tr>

