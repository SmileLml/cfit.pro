<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
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
            <h2><?php echo $this->lang->residentsupport->logBook.'  '.$day->dutyDate;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
            <table class="table table-form">

                <tr>
                    <th><?php echo $lang->residentsupport->type;?></th>
                    <td ><?php echo html::select('type', $typeList, $day->type, "class='form-control chosen' onchange='switchType()' required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentsupport->subType;?></span>
                            <?php echo html::select('subType', $subTypeList, $day->subType, "class='form-control chosen' onchange='switchSubType()' required");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>载入保存/暂存日志信息</th>
                    <td>
                        <input type="radio" name="loadType" value="1" onchange="switchSubType()" <?php if ($loadType == 1) echo 'checked';?>>&nbsp;保存 &nbsp;&nbsp;
                        <input type="radio" name="loadType" value="2" onchange="switchSubType()" <?php if ($loadType == 2) echo 'checked';?>>&nbsp;暂存
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentsupport->dateType;?></th>
                    <td><?php echo html::select('dateType',$dateTypeList, '', "class='form-control chosen'  required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentwork->dutyPlace;?></span>
                            <?php echo html::select('area', $areaList, $day->area, "class='form-control chosen' required");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentwork->actualLeader;?></th>
                    <td><?php echo html::select('groupLeader',$users, $day->dutyGroupLeader, "class='form-control chosen'   required");?></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentwork->actualUser;?></span>
                            <?php echo html::select('realDutyuser[]', $users, $day->dutyUser, "class='form-control chosen' multiple required");?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentwork->isEmergency;?></th>
                    <td>
                        <input type="radio" name="isEmergency" value="1" <?php if ($day->isEmergency == 1) echo 'checked';?>>&nbsp;是 &nbsp;&nbsp;
                        <input type="radio" name="isEmergency" value="2" <?php if ($day->isEmergency == 2) echo 'checked';?>>&nbsp;否
                    </td>
                </tr>
                <tr class="emergencyRemark" <?php if ($day->isEmergency == 2) echo "style='display:none'";?>>
                    <th><?php echo $lang->residentwork->emergencyRemark;?></th>
                    <td colspan='2'><?php echo html::textarea('remark', $day->remark, "class='form-control textarea' ");?></td>
                </tr>
                <tr >
                    <th><?php echo $lang->residentwork->desc;?></th>
                    <td colspan='2'><?php echo html::textarea('logs', $day->logs, "class='form-control textarea' required");?></td>
                </tr>
                <tr >
                    <th><?php echo $lang->residentwork->warnLogs;?></th>
                    <td colspan='2'><?php echo html::textarea('warnLogs', $day->warnLogs, "class='form-control textarea' required");?></td>
                </tr>
                <tr >
                    <th><?php echo $lang->residentwork->analysis;?></th>
                    <td colspan='2'><?php echo html::textarea('analysis', $day->analysis, "class='form-control textarea' ");?></td>
                </tr>
                <tr>
                    <th><?php echo $lang->residentwork->enclosure;?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildForm');?></td>
                </tr>
                <?php if($day->files){ ?>
                <tr>
                    <th><?php echo $lang->residentwork->fileList;?></th>
                    <td colspan='2'>
                        <div class="detail">
                            <div class="detail-content article-content">
                                <?php  echo $this->fetch('file', 'printFiles', array('files' => $day->files, 'fieldset' => 'false', 'object' => null));
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <th><?php echo $lang->residentwork->ccTo;?></th>
                    <td colspan='2'><?php echo html::select('mailCtoUsers[]', $users, $day->mailCtoUsers, "class='form-control chosen' multiple ");?></td>
                </tr>
                <tr>
                    <input type="hidden" name="isDraft" value="1">
                    <input type="hidden" name="dayId" value="<?php echo $day->dayId?>">
                    <!--   默认不推送值班日志                 -->
                    <input type="hidden" name="isPush" value="2">
                    <input type="hidden" name="dutyDate" value="<?php echo $day->dutyDate?>">
                    <input type="hidden" name="templateId" value="<?php echo $day->templateId?>">
                    <td class='form-actions text-center' colspan='3'>
                        <?php echo html::submitButton('', '', 'btn btn-wide btn-primary checkGroupLeader') . html::commonButton('暂存','','btn btn-wide btn-primary staging');?>
                        <?php
                            if ($day->type == 1 && $day->pushStatus == 2){
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
        <input type="hidden" id="oldDate" value="<?php echo str_replace("-",',',$day->dutyDate)?>">
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
    var userstr = "";
    $("#realDutyuser option:selected").each(function () {
        userstr += $(this).val()+',';
    })
    //弹窗取消按钮
    $(".cancel").click(function () {
        top.location.reload();
    })
    var dutyDate = '<?php echo $day->dutyDate?>';
    var list = '<?php echo $list?>';
    //切换值班类型
    function switchType() {
        var type = $("#type option:selected").val()
        var ajaxGetsubtypeUrl = '<?php echo $this->createLink('residentwork', 'ajaxGetsubType')?>'
        $.post(ajaxGetsubtypeUrl,{dutyDate:dutyDate,type:type},function (res) {
            $('#subType').next().remove();
            $('#subType').replaceWith(res);
            $('#subType').chosen();
        })
    }
    //切换二级分类
    function switchSubType() {
        var type = $("#type option:selected").val()
        if (type == ''){
            alert("请选择值班类型");
            return false;
        }

        loadType = $("[name='loadType']:checked").val();
        var subType = $("#subType option:selected").val()
        var link = "<?php echo $this->createLink('residentwork', 'recordDutyLog', 'dutyDate=' . str_replace("-", ',', $day->dutyDate) . '&dayId=IDSTR&loadType=');?>";
        link = link.replace(".html",loadType+".html");
        var dayId = "";
        var data = JSON.parse(list);
        for (var i=0;i<data.length;i++){
            if (data[i]['type'] == type && subType == data[i]['subType']){
                dayId = data[i]['id'];
            }
        }
        location.href = link.replace("IDSTR",dayId);
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
        var groupLeader = $("#groupLeader option:selected").val();
        var realDutyuser = $("#realDutyuser option:selected").val();
        var type = $("#type option:selected").val()
        if (type == ''){
            bootbox.alert("请选择值班类型");
            return false
        }
        var subType = $("#subType option:selected").val()
        if (subType == ''){
            bootbox.alert("请选择值班子类");
            return false
        }
        if (groupLeader == '' || groupLeader == undefined){
            bootbox.alert("实际值班组长不能为空");
            return false;
        }

        if (realDutyuser == '' || realDutyuser == undefined){
            bootbox.alert("实际值班人员不能为空");
            return false;
        }
        var arr = [];
        var realDutyuserStr = "";
        var actualUserStr = "";
        $("#realDutyuser option:selected").each(function () {
            arr.push($(this).val());
            realDutyuserStr += $(this).val()+','
            actualUserStr += "<div>"+$(this).text()+"</div>";
        })
        if($.inArray(groupLeader,arr) == -1 && realDutyuserStr != userstr){
            bootbox.alert('确认实际值班组长和实际值班人员是否一致，请在填写值班日志前变更排班？', function (result){
                // if((result)){
                //     $("[name='isDraft']").val(2)
                //     $('button[data-bb-handler="cancel"]').click();
                //     $('.checkGroupLeader').submit();
                //     return false;
                // }
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
<?php include '../../common/view/footer.html.php';?>
