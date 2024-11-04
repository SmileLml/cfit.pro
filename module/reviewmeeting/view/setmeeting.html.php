<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
    .task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
    .task-toggle .icon{display: inline-block; transform: rotate(90deg);}
    .more-tips{display: none;}
    .close-tips{display: none}
    .remarkshow{display: none}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->reviewmeeting->meeting->setmeetingTitle;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tbody>
                <?php if (count($reviewList) == 1){?>
                <tr style="display: none">
                    <th><?php echo $lang->reviewmeeting->title;?></th>
                    <td>
                        <?php foreach ($reviewList as $v){?>
                        <?php echo html::input('', $v->title, "class='form-control' disabled");?>
                        <?php }?>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <th><?php echo $lang->reviewmeeting->owner;?></th>
                    <td ><?php echo html::select('owner', $users, $owner, "class='form-control chosen' required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->review->reviewer;?></span>
                            <?php echo html::select('reviewer', $users, $reviewer, "class='form-control chosen' required");?>
                        </div>
                    </td>
                </tr>
                <tr class="gradeMeeting ">
                    <th><?php echo $lang->review->meetingPlanType;?></th>
                    <td>
                        <?php echo html::radio('meetingPlanType', $lang->review->meetingPlanTypeLabelList, '1');?>
                    </td>
                    <td>
                        <div class="input-group meetingPlan meetingPlanType-1">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->review->meetingPlanList;?></span>
                            <?php echo html::select('meetingCode', '', '', "class='form-control chosen' required");?>
                        </div>
                        <div class="input-group meetingPlan meetingPlanType-2 hidden">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->reviewmeeting->meeting->setsched;?></span>
                            <?php echo html::input('feedbackExpireTime', '', "class='form-control form-datetime' required");?>
                        </div>
                    </td>

                </tr>
                <tr>
                    <th><?php echo $lang->reviewmeet->meetingPlanExport;?></th>
                    <td id="experttd" colspan="2"><?php echo html::select('expert[]',$users, $expert, "class='form-control chosen'  multiple required");?></td>
                </tr>
<!--                --><?php //if (count($reviewList) > 1){?><!--class="remarkshow"--><?php //}?>
                <tr >
                    <th><?php echo $lang->reviewmeeting->meeting->remark;?></th>
                    <td colspan='2'><?php echo html::textarea('comment', '', "class='form-control textarea'");?></td>
                </tr>
                <tr>
                    <?php echo html::hidden("ids",$ids)?>
                    <td class='form-actions text-center' colspan='3'><?php echo html::submitButton() . html::backButton();?></td>
                </tr>
                </tbody>
            </table>
        </form>
        <input type="hidden" class="type" value="<?php echo $reviewList[0]->type?>">
        <input type="hidden" class="reviewId" value="<?php echo $reviewList[0]->id?>">
    </div>
</div>
<script>
    $("#meetingPlanType3").parent().hide();
    function ajaxGetMeetingList(reviewType, meetingCode = ''){
        $.get(createLink('reviewmeeting', 'ajaxAllowBindMeetingList', "type=" + reviewType+ "&reviewID=" +  meetingCode), function(data) {
            $('#meetingCode_chosen').remove();
            $('#meetingCode').replaceWith(data);
            $('#meetingCode').chosen();
        });
    }
    var type = $(".type").val();
    var reviewId = $(".reviewId").val()
    ajaxGetMeetingList(type, reviewId)
    $('input:radio[name="meetingPlanType"]').change(function() {
        var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
        $(".meetingPlan").addClass("hidden")
        $(".meetingPlanType-"+meetingPlanType).removeClass("hidden")
        var ids = "<?php echo $ids;?>"
        if (meetingPlanType == 2){
            ajaxGetMeetExpert('',ids,meetingPlanType)
        }else{
            var meetingCode = $("#meetingCode").val();
            ajaxGetMeetExpert(meetingCode,ids,meetingPlanType)
        }
    });
    //预计参会专家回显
    $('body').delegate('#meetingCode','change',function () {
        var meetingPlanType = $('input:radio[name="meetingPlanType"]:checked').val();
        var ids = "<?php echo $ids;?>";
        var meetingCode = $(this).val();
        ajaxGetMeetExpert(meetingCode,ids,meetingPlanType);
    })
    //获取预计参会专家
    function ajaxGetMeetExpert(meetingCode,ids,meetingPlanType) {
        $.post(createLink('reviewmeeting', 'ajaxGetMeetExpert'),{"meetingCode":meetingCode,"ids":ids,"meetingPlanType":meetingPlanType}, function(data) {
            $('#expert').siblings().remove();
            $('#expert').replaceWith(data.expert);
            $('#expert').chosen();

            $('#reviewer').next().remove();
            $('#reviewer').replaceWith(data.reviewer);
            $('#reviewer').chosen();

            $('#owner').next().remove();
            $('#owner').replaceWith(data.owner);
            $('#owner').chosen();
        },'json');
    }
</script>
<?php include '../../common/view/footer.html.php';?>
