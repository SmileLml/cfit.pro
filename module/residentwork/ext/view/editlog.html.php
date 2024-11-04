<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<style>
    .task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
    .task-toggle .icon{display: inline-block; transform: rotate(90deg);}
    .modal-header{display: block}
    .showDiv div{float: left;padding: 2px 4px;font-size: 13px;border: 1px solid #adb5c6;background-color: #eee;margin-right: 6px;border-radius: 4px}
    .showDiv {
        height: auto !important;
    }
    .show_push_txt{font-size: 16px;font-weight: 600;width: 80%;text-align: center;margin:0 auto;margin-bottom: 10px}
</style>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $this->lang->residentsupport->logBook.'  '.$info->dutyDate;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">
                <tr>
                    <th><?php echo $lang->residentsupport->dutyDate;?></th>
                    <td ><?php echo html::input('dutyDate', $info->dutyDate, "class='form-control' readonly ");?></td>
                </tr>

                <tr>
                    <th><?php echo $lang->residentsupport->type;?></th>
                    <td ><?php echo html::select('type', $type, $info->type, "class='form-control chosen' onchange='switchUsers()' required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentsupport->subType;?></span>
                            <?php echo html::select('subType', $subType, $info->subType, "class='form-control chosen' required");?>
                        </div>
                    </td>
                </tr>
                <tr style="display: none">
                    <th>载入保存/暂存日志信息</th>
                    <td>
                        <input type="radio" name="loadType" value="1" onchange="switchSubType()" <?php if ($loadType == 1) echo 'checked';?>>&nbsp;保存 &nbsp;&nbsp;
                        <input type="radio" name="loadType" value="2" onchange="switchSubType()" <?php if ($loadType == 2) echo 'checked';?>>&nbsp;暂存
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentsupport->dateType;?></th>
                    <td><?php echo html::select('dateType',$dateTypeList, $info->dateType, "class='form-control chosen'  required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentwork->dutyPlace;?></span>
                            <?php echo html::select('area', $areaList, $info->area, "class='form-control chosen' required");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentwork->actualLeader;?></th>
                    <td><?php echo html::select('groupLeader',$users, $info->groupLeader, "class='form-control chosen' onchange='switchUsers()'   required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentwork->actualUser;?></span>
                            <?php echo html::select('realDutyuser[]', $users, array_column($info->details,'realDutyuser'), "class='form-control chosen' onchange='switchUsers()' multiple required");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentwork->isEmergency;?></th>
                    <td>
                        <input type="radio" name="isEmergency" value="1" <?php if($info->isEmergency == 1){echo 'checked';}?> >&nbsp;是 &nbsp;&nbsp;
                        <input type="radio" name="isEmergency" value="2" <?php if($info->isEmergency == 2){echo 'checked';}?> >&nbsp;否
                    </td>
                </tr>
                <tr class="emergencyRemark" <?php if ($info->isEmergency == 2) echo "style='display:none'";?>>
                    <th><?php echo $lang->residentwork->emergencyRemark;?>（限制499字符）</th>
                    <td colspan='2'><?php echo html::textarea('remark', $info->remark, "class='form-control textarea' ");?></td>
                </tr>
                <tr >
                    <th><?php echo $lang->residentwork->desc;?></th>
                    <td colspan='2'><?php echo html::textarea('logs', $info->logs, "class='form-control textarea' required");?></td>
                </tr>
                <tr >
                    <th><?php echo $lang->residentwork->warnLogs;?>（限制499字符）</th>
                    <td colspan='2'><?php echo html::textarea('warnLogs', $info->warnLogs, "class='form-control textarea' required");?></td>
                </tr>
                <tr >
                    <th><?php echo $lang->residentwork->analysis;?>（限制499字符）</th>
                    <td colspan='2'><?php echo html::textarea('analysis', $info->analysis, "class='form-control textarea' ");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentwork->enclosure;?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildForm');?></td>
                </tr>
                <?php if($info->files){ ?>
                <tr>
                    <th><?php echo $lang->residentwork->fileList;?></th>
                    <td colspan='2'>
                        <div class="detail">
                            <div class="detail-content article-content">
                                <?php  echo $this->fetch('file', 'printFiles', array('files' => $info->files, 'fieldset' => 'false', 'object' => null));
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <th><?php echo $lang->residentwork->ccTo;?></th>
                    <td colspan='2'><?php echo html::select('mailCtoUsers[]', $users, $info->mailCtoUsers, "class='form-control chosen' multiple ");?></td>
                </tr>
                <tr>
                    <!--   默认不推送值班日志                 -->
                    <input type="hidden" name="isPush" value="2">
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton('', '', 'btn btn-wide btn-primary checkGroupLeader');?>
                        <?php
                            if ($info->type == 1 && $info->pushStatus == 2){
                                echo html::submitButton('重新推送', '', 'btn btn-wide btn-primary checkGroupLeader');
                            }
                        ?>
                        <?php
                            if ($onlybody == 'yes'){
                                echo html::commonButton('取消','','btn btn-wide cancel');
                            }else{
                                echo html::backButton();
                            }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<div style="display: none;">
    <div id="show" >
        <div class="show_push_txt"><?php echo $lang->residentwork->pushTxt?></div>
        <div class="show_push_txt">抄送人员列表如下</div>
        <table class="table table-form">
            <tr>
                <th><?php echo $lang->residentwork->actualLeader;?>：</th>
                <td>
                    <div class="form-control showDiv clearfix actualLeader">
                    </div>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang->residentwork->actualUser;?>：</th>
                <td>
                    <div class="form-control showDiv clearfix actualUser">
                    </div>
                </td>
            </tr>
            <tr>
                <th>值班组长所在部门负责人：</th>
                <td>
                    <div class="form-control showDiv clearfix LeaderManager">
                    </div>
                </td>
            </tr>
            <tr>
                <th>值班组员所在部门负责人：</th>
                <td>
                    <div class="form-control showDiv clearfix dutyUserManager">
                    </div>
                </td>
            </tr>
            <tr>
                <th>当前填写人：</th>
                <td>
                    <div class="form-control showDiv clearfix createdByUser">
                        <div><?php echo $this->app->user->realname;?></div>
                    </div>
                </td>
            </tr>

            <tr>
                <th>产创部二线专员：</th>
                <td>
                    <div class="form-control showDiv clearfix dealUserName">
                        <div><?php echo $day->dealUserName?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <?php echo html::commonButton('确认', 'onclick="confirmSave()"', 'btn btn-wide btn-primary')?>
                    <?php echo html::commonButton('取消','onclick="cancelPush()"','btn btn-wide');?>
                    <?php echo html::commonButton('取消','data-dismiss="modal" style="display:none"','btn btn-wide');?>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php js::set("loadType",'')?>
<script>
    $(function () {
        $(".form-date").datetimepicker('setEndDate','<?php echo date(DT_DATE1)?>')
    })
    //弹窗取消按钮
    $(".cancel").click(function () {
        top.location.reload();
    })
    //选择值班人员值班组长 获取抄送人
    function switchUsers() {
        var getCc = '<?php echo $this->createLink('residentwork', 'ajaxGetCc')?>';
        var type = $("#type option:selected").val()
        if (type == '' || type == 0){
            bootbox.alert("请选择值班类型");
            return false
        }
        var groupLeader = $("#groupLeader option:selected").val();
        var realDutyuserStr = "";
        var arr = [];
        $("#realDutyuser option:selected").each(function () {
            if ($(this).val() != ''){
                arr.push($(this).val());
                realDutyuserStr += $(this).val()+','
            }
        })
        $.post(getCc,{"LeaderManager":groupLeader,dutyUserManager:realDutyuserStr,type:type},function (res) {
            $('#mailCtoUsers').siblings().remove();
            $('#mailCtoUsers').replaceWith(res);
            $('#mailCtoUsers').chosen()
        })
    }

    var isEmergency = $("[name='isEmergency']:checked").val()
    function changeIsEmergency(_val) {
        if (_val == 1){
            $(".emergencyRemark").css({"display":"table-row"});
        }else{
            $(".emergencyRemark").css({"display":"none"})
        }
    }
    changeIsEmergency(isEmergency)
    $("[name='isEmergency']").change(function () {
        var _val = $(this).val();
        changeIsEmergency(_val)
    })
    function cancelPush() {
        $("[name='isPush']").val(2);
        $('button[data-dismiss="modal"]').click();
        $('form').submit();
    }
    function confirmSave() {
        $('button[data-dismiss="modal"]').click();
        $("[name='isPush']").val(1);
        $('form').submit();
    }
    $('.checkGroupLeader').click(function (){
        var dutyDate = $("#dutyDate").val();
        if (dutyDate == ''){
            bootbox.alert("请选择值班日期");
            return false
        }
        var groupLeader = $("#groupLeader option:selected").val();
        var arr = [];
        var realDutyuserStr = "";
        var actualUserStr = "";
        $("#realDutyuser option:selected").each(function () {
            if ($(this).val() != ''){
                arr.push($(this).val());
                realDutyuserStr += $(this).val()+','
                actualUserStr += "<div>"+$(this).text()+"</div>";
            }
        })
        var type = $("#type option:selected").val()
        if (type == '' || type == 0){
            bootbox.alert("请选择值班类型");
            return false
        }
        var subType = $("#subType option:selected").val()
        if (subType == '' || subType == 0){
            bootbox.alert("请选择值班子类");
            return false
        }
        var dateType = $("#dateType option:selected").val()
        var area = $("#area option:selected").val()
        if (dateType == '' || dateType == 0){
            bootbox.alert("请选择日期类型");
            return false
        }
        if (area == '' || area == 0){
            bootbox.alert("请选择值班地点");
            return false
        }

        if (groupLeader == '' || groupLeader == undefined){
            bootbox.alert("实际值班组长不能为空");
            return false;
        }

        if (realDutyuserStr == '' || realDutyuserStr == undefined){
            bootbox.alert("实际值班人员不能为空");
            return false;
        }

        if($.inArray(groupLeader,arr) == -1){
            bootbox.alert('请确认实际值班人员必须包含实际值班组长，请在提交值班日报前核实相关信息。', function (result){

            });
            return false;
        }
        //支付类将日志同步至总中心
        if (type == 1){
            var dutyModalTrigger = new $.zui.ModalTrigger(
                {
                    width: '70%',
                    rememberPos: 'dutyViewModal',
                    custom : $("#show"),
                    waittime: 5000
                }).show();
            $(".actualLeader").empty().append("<div>"+$("#groupLeader option:selected").text()+"</div>")
            $(".actualUser").empty().append(actualUserStr)
            var getManagerUrl = '<?php echo $this->createLink('residentwork', 'ajaxGetanager')?>';
            $.post(getManagerUrl,{"LeaderManager":groupLeader,dutyUserManager:realDutyuserStr,type:type},function (res) {
                $(".LeaderManager").empty().append(res.LeaderManager)
                $(".dutyUserManager").empty().append(res.dutyUserManager)
                $(".dealUserName").empty().append(res.dealUser)
            },'json')
            return false;
        }
    });

    //暂存按钮
    $(".staging").click(function () {
        $("[name='isDraft']").val(2)
        $('.checkGroupLeader').submit();
    })
</script>
<?php include '../../../common/view/footer.html.php';?>
