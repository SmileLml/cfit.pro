<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php
$type = $this->lang->residentsupport->typeList;
$subType = $this->lang->residentsupport->subTypeList;
$displayNone = "";
?>
<div id='mainContent' class='main-content fade in  scrollbar-hover' style="min-height:300px;">

  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id' style="padding: 2px 5px"><?php echo $type[$templateInfo->type]?>排班模板<?php echo $templateInfo->id;?></span>
        <span><?php echo $deptInfo->name;?></span>
        <small><?php echo $lang->arrow . $lang->residentsupport->editScheduling;?></small>
      </h2>
        <div style="clear: both"><?php echo $lang->residentsupport->cozyTips?></div>
    </div>
      <?php if(!$checkRes['result']):?>
          <div class="tipMsg">
              <span><?php echo $checkRes['message']; ?></span>
          </div>
      <?php
      else:
          $params = "browseType=$browseType&param=$param&orderBy=$orderBy&recTotal=$recTotal&recPerPage=$recPerPage&pageID=$pageID&sourceLabel=$sourceLabel"
      ?>

          <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
              <div class='detail' style="padding: 0px !important;">
                  <div class="detail-title">
                      <?php
                      foreach ($lang->residentsupport->schedulingDeptLabelList as $label => $labelName) {
                          $active = $schedulingDeptType == $label ? 'btn-active-text' : '';
                          echo html::a($this->createLink('residentsupport', 'editScheduling', "templateId=$templateId&schedulingDeptType=$label&".$params), '<span class="text">' . $labelName . '</span>', '', "class='btn btn-link $active'");
                      }
                      ?>
                  </div>
                  <div class="detail-title">
                      <div id="mainMenu" class="clearfix" style="margin-top: 15px !important;">
                          <table class='table table-form table_tr'>
                              <tr id="search_1">
                                  <th class='w-40px'><?php echo $lang->residentsupport->type;?></th>
                                  <td class='w-60px'><?php echo html::select('type', $type,$typeVal, "class='form-control' onchange='getTemplate()'");?></td>
                                  <th class='w-40px'><?php echo $lang->residentsupport->subType;?></th>
                                  <td class='w-60px'><?php echo html::select('subType', $subType, $subTypeVal, "class='form-control' onchange='getTemplate()'");?>
                                  <th class='w-40px'><?php echo $lang->residentsupport->choiceRostering;?></th>
                                  <td class='w-100px'><?php echo html::select('templateId', [],'1', "class='form-control chosen' required onchange='setTemplate()'");?></td>
                                  <td class='w-120px'>
                                      <div class="table-row" style="width: 100%">
                                          <div class="table-col">
                                              <div class="input-group">
                                                  <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentsupport->startDate;?></span>
                                                  <?php echo html::input('startDate', $startDate, "class='form-control form-date' onchange='checkDate()'");?>
                                                  <span class="input-group-addon" style="border-radius: 2px 0px 0px 2px; border-left-width: 1px;"><?php echo $lang->residentsupport->endDate;?></span>
                                                  <?php echo html::input('endDate', $endDate, "class='form-control form-date' onchange='checkDate()'");;?>
                                              </div>
                                          </div>
                                      </div>
                                  </td>

                                  <td class='w-40px'><?php echo html::commonButton('搜索', 'onclick="search()"', 'btn btn-primary centerauto ','');?></td>
                              </tr>
                              <input type="hidden" id="date" value="">
                          </table>
                      </div>
                  </div>
                  <div class="detail-content article-content ">
                    <table class='table ops scheduling-table'>
                  <thead>
                      <tr>
                          <th><?php echo $lang->residentsupport->dutyDate ;?></th>
                          <th><?php echo $lang->residentsupport->dutyGroupLeader;?></th>
                          <th class='w-120px'><?php echo $lang->residentsupport->dutyDept;?></th>
                          <th><?php echo $lang->residentsupport->postTypeInfo;?></th>
                          <th class='w-120px'><?php echo $lang->residentsupport->requireInfo;?></th>
                          <th class='w-180px'><?php echo $lang->residentsupport->timeType ;?></th>
                          <th><?php echo  $lang->residentsupport->dutyTime ;?></th>
                          <th><?php echo  $lang->residentsupport->dutyUser ?></th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php if(empty($dutyUserList)):?>
                      <tr>
                          <th colspan="8" style="text-align: center;"><?php echo $lang->noData;?></th>
                      </tr>
                  <?php else:?>
                       <?php
                        foreach($dutyUserList as $dayId => $dayDutyInfo):
                            $dayUserCount = $dayDutyInfo['total'];
                            $dutyDeptList = $dayDutyInfo['data'];
                            $dayInfo = zget($dayList, $dayId); //当天信息
                            $dutyDate = isset($dayInfo->dutyDate)?$dayInfo->dutyDate:'';
                            $dutyGroupLeader = isset($dayInfo->dutyGroupLeader)?$dayInfo->dutyGroupLeader:'';
                        ?>
                            <tr>
                                <th rowspan="<?php echo $dayUserCount;?>" style="vertical-align: middle;">
                                    <?php echo $dutyDate; ?>
                                </th>

                                <th rowspan="<?php echo $dayUserCount;?>" style="vertical-align: middle;">
                                    <?php echo $dutyGroupLeader ? zget($users, $dutyGroupLeader):''; ?>
                                </th>

                            <?php
                            foreach ($dutyDeptList as $deptId => $deptDutyInfo):
                                $deptUserCount = $deptDutyInfo['total'];
                                $currentDeptUerList = $deptDutyInfo['data'];
                                //部门下第一个值班用户信息
                                $deptFirstUerInfo =  $currentDeptUerList[0]; //当前部门下的第一个用户信息
                            ?>
                                <th rowspan="<?php echo $deptUserCount;?>" style="vertical-align: middle;">
                                    <?php echo zget($depts, $deptId); ?>
                                </th>
                                <td><?php echo zget($lang->residentsupport->postType, $deptFirstUerInfo->postType); ?></td>
                                <td title='<?php echo $deptFirstUerInfo->requireInfo;?>' class='text-ellipsis'>
                                    <?php echo Helper::substr($deptFirstUerInfo->requireInfo, 10,'...'); ?>
                                </td>
                                <td><?php echo zget($lang->residentsupport->durationTypeList, $deptFirstUerInfo->timeType); ?></td>
                                <td><?php echo $deptFirstUerInfo->startTime.'-'.$deptFirstUerInfo->endTime; ?></td>
                                <td>
                                    <?php if(($deptInfo->id == $deptId) && ($dutyDate > $currentDay)):?>
                                        <?php echo html::select("dutyUsers[$deptFirstUerInfo->id]", $currentDeptUsers, $deptFirstUerInfo->dutyUser, "class='form-control chosen dutyUsers' data-id='".$deptFirstUerInfo->id."'");?>
                                    <?php else:?>
                                        <?php echo html::select("temp[]", $users, $deptFirstUerInfo->dutyUser, "class='form-control chosen' disabled='disabled' readonly='readonly'");?>
                                    <?php endif;?>
                                </td>
                            </tr>
                                    <?php
                                        if($deptUserCount > 1):
                                            for($i = 1; $i < $deptUserCount; $i++):
                                                $dutyUserInfo =  $currentDeptUerList[$i];
                                    ?>
                                    <tr>
                                        <td><?php echo zget($lang->residentsupport->postType, $dutyUserInfo->postType); ?></td>
                                        <td title='<?php echo $dutyUserInfo->requireInfo;?>' class='text-ellipsis'>
                                            <?php echo Helper::substr($dutyUserInfo->requireInfo, 10, '...'); ?>
                                        </td>
                                        <td><?php echo zget($lang->residentsupport->durationTypeList, $dutyUserInfo->timeType); ?></td>
                                        <td><?php echo $dutyUserInfo->startTime.'-'.$dutyUserInfo->endTime; ?></td>
                                        <td>
                                            <?php if(($deptInfo->id == $deptId) && ($dutyDate > $currentDay)):?>
                                                <?php echo html::select("dutyUsers[$dutyUserInfo->id]", $currentDeptUsers, $dutyUserInfo->dutyUser, "class='form-control chosen dutyUsers' data-id='".$dutyUserInfo->id."'");?>
                                            <?php else:?>
                                                <?php echo html::select("temp[]", $users, $dutyUserInfo->dutyUser, "class='form-control chosen' disabled='disabled' readonly='readonly'");?>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                    <?php
                                            endfor;
                                        endif;
                                    ?>
                               <?php
                                    endforeach;
                                ?>

                    <?php
                        endforeach;
                   ?>
                  <?php endif;?>

                      <tr>
                          <td class='text-center' colspan='8'>
                              <input type="hidden" id="templateId" name = "templateId" value="<?php echo $templateInfo->id; ?>">
                              <input type="hidden" id="templateDeptId" name = "templateDeptId" value="<?php echo $templateDeptInfo->id; ?>">
                              <input type="hidden" id="submitType" name = "submitType" value="0">
                              <?php echo html::submitButton('提交', '', 'btn btn-wide btn-primary enableScheduling').'&nbsp;'. html::backButton('取消','onclick="return returnBack();"');?>
                          </td>
                      </tr>
                  </tbody>
              </table>
                 </div>
            </div>
          </form>
      <?php endif;?>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php
if(strpos($params, 'workWaitList')){
    $tempParams = "mode=$browseType&type=$param";
    js::set('backUrl',  $this->createLink('my', 'work', $tempParams));
}else{
    js::set('backUrl',  $this->createLink('residentsupport', 'browse', $params));
}
?>
<script>
    /**
     * 返回
     */
    function returnBack() {
        window.location.href = backUrl;
        return false;
    }

    var params = "<?php echo $params;?>";
    var templateId = "<?php echo $templateId?>";
    var schedulingDeptType = "<?php echo $schedulingDeptType;?>";
    var source = "<?php echo $source?>";
    var startDate = "<?php echo $startDate?>";
    var endDate = "<?php echo $endDate?>";
    var type = "<?php echo $typeVal?>";
    var subType = "<?php echo $subTypeVal?>";
    function getTemplate() {
        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        $.post(createLink('residentsupport', 'ajaxGetTemplate'),{type:type,subType:subType,templateId:templateId,source:1},function (res) {
            $('#templateId').siblings().remove();
            $('#templateId').replaceWith(res);
            $('#templateId').chosen();
            var templateStr = $("#templateId option:selected").text();
            if (templateStr){
                var arr = templateStr.split(" ");
                var date = arr[1].split("~");

                if (startDate != ''){
                    $("#startDate").val(startDate);
                }else{
                    $("#startDate").val(date[0]);
                }
                if (endDate != ''){
                    $("#endDate").val(endDate);
                }else{
                    $("#endDate").val(date[1]);
                }
                $("#date").val(arr[1]);
            }else{
                $("#startDate").val('');
                $("#endDate").val('');
                $("#date").val('');
            }
        })
    }
    getTemplate();
    //设置搜索框前几个元素隐藏
    if (source == 0){
        $("#search_1").children().each(function (i) {
            if (i < 6){
                $(this).css("display",'none');
            }
        })
    }
    function setTemplate() {
        var templateStr = $("#templateId option:selected").text();
        if (templateStr){
            var arr = templateStr.split(" ");
            var date = arr[1].split("~");
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            $("#date").val(arr[1]);
        }else{
            $("#startDate").val('');
            $("#endDate").val('');
            $("#date").val('');
        }
    }
    function checkDate() {
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var date = $("#date").val();
        date = date.split("~")
        if (startDate < date[0]){
            $("#startDate").val(date[0]);
            alert("开始时间不能小于模板开始时间");
        }
        if (endDate > date[1]){
            $("#endDate").val(date[0]);
            alert("开始时间不能大于模板结束时间");
        }
        if (startDate > endDate){
            $("#startDate").val(date[0]);
            $("#endDate").val(date[1]);
            alert("开始时间不能大于结束时间");
        }
    }

    /**
     * 搜索
     * @returns {boolean}
     */
    function search() {
        checkDate();
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var templateId = $("#templateId option:selected").val()
        if (templateId == '' || startDate == '' || endDate == ''){
            alert('请选择模板或是时间范围');
            return false
        }
        var checkLink = '<?php echo $this->createLink('residentsupport', 'ajaxCheckSearch');?>';
        var dutyUsers = [];
        var id = [];
        $(".dutyUsers").each(function (i) {
            dutyUsers.push($(".dutyUsers").eq(i).find("option:selected").val())
            id.push($(this).attr("data-id"))
        });
        var templateId = $("#templateId").val();
        var templateDeptId = $("#templateDeptId").val();
        var link = '<?php echo $this->createLink('residentsupport', 'editScheduling');?>';

        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        link = link+"?source="+source+"&templateId="+templateId+"&schedulingDeptType="+schedulingDeptType+"&startDate="+startDate+"&endDate="+endDate+"&type="+type+"&subType="+subType+"&"+params;
        $.post(checkLink,{dutyUsers:dutyUsers,templateId:templateId,templateDeptId:templateDeptId,id:id},function (res) {
            if (res == 1){
                bootbox.confirm('当前排班数据有修改，请确认是否需要保存', function (result){
                    if((result)){
                        $('button[data-bb-handler="cancel"]').click();
                        $('#submit').submit();
                        return false;
                    }else{
                        location.href = link;
                    }
                });
                return false;
            }else{
                location.href = link;
            }
        })
    }
</script>
