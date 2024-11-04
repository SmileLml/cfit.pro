<?php
    if($reviewList):
        foreach ($reviewList as $reviewInfo):
?>
<tr>
    <th class='w-150px' title="<?php echo $reviewInfo->title;?>"><?php echo helper::substr($reviewInfo->title, 10, '...');?></th>
    <td colspan="3">
        <?php echo html::hidden('reviewIds[]', "$reviewInfo->id");?>
        <?php echo html::select("meetingRealExportList[$reviewInfo->id][]", $users, $reviewInfo->realExportUsers, "class='form-control chosen' multiple required");?>
    </td>

    <td>
        <div class='input-group'>
            <?php echo html::input('meetingConsumedList[]', '', "class='form-control' placeholder='评审会议工作量（小时）' required");?>
        </div>
    </td>
</tr>
<?php
        endforeach;
    endif;
?>

<tr>
    <th><?php echo $lang->reviewmeeting->meetingRealTime;?></th>
    <td colspan="4">
        <?php echo html::input('meetingRealTime', '', "class='form-control form-datetime' required  ");?>
    </td>

    <!--<td colspan="2">
        <div class='input-group'>
            <span class='input-group-addon'><?php /*echo  $lang->reviewmeeting->consumed;*/?></span>
            <?php /*echo html::input('consumed', '', "class='form-control' required");*/?>
        </div>
    </td>-->

</tr>

<tr>
    <th><?php echo $lang->reviewmeeting->mailto;?></th>

    <td colspan="4">
        <?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple  data-drop_direction='down'");?>
    </td>

</tr>

<?php
    if($reviewList):
        foreach ($reviewList as $key => $reviewInfo):
?>

    <tr>
        <th title="<?php echo $reviewInfo->title . $lang->reviewmeeting->comment;?>"><?php echo $reviewInfo->title . $lang->reviewmeeting->comment;?></th>
        <td colspan='4'>
            <?php echo html::textarea("comment_$key", '', "class='form-control'");?>
        </td>
    </tr>

<?php
        endforeach;
    endif;
?>

<tr>
    <td class='text-center' colspan='5'>
        <?php echo html::submitButton();?>
    </td>
</tr>

<script>
    $(".form-datetime").datetimepicker(
        {
            weekStart: 1,
            todayBtn:  0,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
           // minView: 2,
            forceParse: 0,
           // format: "yyyy-mm-dd",
            pickerPosition:'bottem-right',
            // dropdown:'bottem-right'
        });
</script>