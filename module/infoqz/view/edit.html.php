<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.input-group-addon{min-width: 150px;}
.input-group{margin-bottom: 2px;}
.visible{visibility: hidden;opacity: 0 !important;}
.dealine-tr{transition: visibility 0.5s,opacity 0.5s;opacity: 1;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->infoqz->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->type;?></th>
              <td><?php echo html::select('type', $lang->infoqz->typeList, $info->type, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->classify;?></span>
                      <?php echo html::select('classify[]',$classifyList, $info->classify, "class='form-control chosen' multiple");?>
                  </div>
              </td>
          </tr>
          <!-- 需求收集4653         -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->dataCollectApplyCompany;?></th>
              <td class="required"><?php echo html::select('dataCollectApplyCompany', $lang->infoqz->demandUnitTypeList, $info->dataCollectApplyCompany, "class='form-control chosen' onchange='selectCompany()'");?></td>
              <td class="required">
                  <?php
                    $str = 'demandUnitList'.$info->dataCollectApplyCompany;
                    $demandUnitList = $lang->infoqz->{$str};
                  ?>
                  <div class='input-group companySelect'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->demandUnitOrDep;?><i title="<?php echo $lang->infoqz->companyTips;?>" class="icon icon-help"></i></span>
                      <?php echo html::select('demandUnitOrDepSelect[]', $demandUnitList, $info->demandUnitOrDep, "class='form-control chosen' multiple");?>
                  </div>
                  <div class='input-group companyInput hidden'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->demandUnitOrDep;?><i title="<?php echo $lang->infoqz->companyTips;?>" class="icon icon-help"></i></span>
                      <?php echo html::input('demandUnitOrDepInput', $info->demandUnitOrDep, "class='form-control'");?>
                  </div>
              </td>
          </tr>
          <!-- 需求人、需求电话 -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->demandUser;?><i title="<?php echo $lang->infoqz->demandUserTips;?>" class="icon icon-help"></i></th>
              <td class="required"><?php echo html::input('demandUser', $info->demandUser, "class='form-control'");?></td>
              <td class="required">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->demandUserPhone;?></span>
                      <?php echo html::input('demandUserPhone', $info->demandUserPhone, "class='form-control'");?>
                  </div>
              </td>
          </tr>
          <!-- 需求人邮箱 -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->demandUserEmail;?></th>
              <td class="required" colspan="2"><input type="email" name="demandUserEmail" value="<?php echo $info->demandUserEmail?>" class="form-control"></td>
          </tr>
          <!-- 接口人、接口人电话 -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->portUser;?></th>
              <td class="required"><?php echo html::input('portUser', $info->portUser != '' ? $info->portUser : $lang->infoqz->portList['portUser'], "class='form-control'");?></td>
              <td class="required">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->portUserPhone;?></span>
                      <?php echo html::input('portUserPhone', $info->portUserPhone != '' ? $info->portUserPhone : $lang->infoqz->portList['portUserPhone'], "class='form-control'");?>
                  </div>
              </td>
          </tr>
          <!-- 接口人邮箱 -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->portUserEmail;?></th>
              <td class="required" colspan="2"><input type="email" name="portUserEmail" value="<?php echo $info->portUserEmail != '' ? $info->portUserEmail : $lang->infoqz->portList['portUserEmail'];?>" class="form-control"></td>
          </tr>
          <!-- 支持人、支持电话 -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->supportUser;?></th>
              <td class="required"><?php echo html::input('supportUser', $info->supportUser, "class='form-control'");?></td>
              <td class="required">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->supportUserPhone;?></span>
                      <?php echo html::input('supportUserPhone', $info->supportUserPhone, "class='form-control'");?>
                  </div>
              </td>
          </tr>
          <!-- 支持人邮箱 -->
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->supportUserEmail;?></th>
              <td class="required" colspan="2"><input type="email" name="supportUserEmail" value="<?php echo $info->supportUserEmail;?>" class="form-control"></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->isNPC;?></th>
              <td><?php echo html::select('isNPC', $lang->infoqz->isNPCList, $info->isNPC, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $action == 'gain' ? $lang->infoqz->gainNode : $lang->infoqz->fixNode;?></span>
                      <?php echo html::select('node[]', $nodeList, $info->node, "class='form-control chosen' multiple");?>
                  </div>
              </td>
          </tr>
          <?php if($action == 'gain'):?>
              <tr>
                    <th class='w-140px'><?php echo $lang->infoqz->gainType;?></th>
                    <td><?php echo html::select('gainType', $lang->infoqz->gainTypeList, $info->gainType, "class='form-control chosen'");?></td>
                    <td>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->infoqz->createUserPhone;?></span>
                            <?php echo html::input('createUserPhone', $info->createUserPhone, "class='form-control'");?>
                        </div>
                    </td>
              </tr>
          <?php endif;?>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->planBegin;?></th>
              <td class="required"><?php echo html::input('planBegin', $info->planBegin, "class='form-control form-datetime'");?></td>
              <td class="required">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->planEnd;?></span>
                      <?php echo html::input('planEnd', $info->planEnd, "class='form-control form-datetime'");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->isTest;?></th>
              <td><?php echo html::select('isTest', $lang->infoqz->isTestList, $info->isTest, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo$lang->infoqz->systemType;?></span>
                      <?php echo html::select('systemType', $lang->infoqz->systemTypeList, $info->systemType,  "class='form-control chosen' ");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->app;?></th>
              <td> <?php echo html::select('app[]', $apps, $info->app, "class='form-control chosen' multiple");?></td>
              <td class="required">
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->dataSystem;?></span>
                      <?php echo html::select('dataSystem[]', $apps, $info->dataSystem,  "class='form-control chosen' multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->infoqz->fixType;?></th>
              <td><?php echo html::select('fixType', $lang->infoqz->fixTypeList, $info->fixType, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->infoqz->project;?></span>
                      <?php echo html::select('project[]', $projects,  $info->project, "class='form-control chosen' multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->demand;?></th>
              <td colspan='2'><?php echo html::select('demand[]', $demands, $info->demand, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->problem;?></th>
              <td colspan='2'><?php echo html::select('problem[]', $problems, $info->problem, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
              <th class='w-140px'><?php echo $lang->infoqz->secondorderId;?></th>
              <td colspan='2'><?php echo html::select('secondorderId[]', $secondorders, $info->secondorderId, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
<!--              <th class='w-140px'>--><?php //echo $lang->infoqz->isTest;?><!--</th>-->
<!--              <td colspan='2'>--><?php //echo html::select('isTest', $lang->infoqz->isTestList, $info->isTest, "class='form-control chosen'");?><!--</td>-->
             <!-- <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php /*echo $lang->infoqz->consumed;*/?></span>
                      <?php /*echo html::input('consumed', '', "class='form-control'");*/?>
                  </div>
              </td>-->
          </tr>
          <tr>
              <th><?php echo $action == 'gain' ? $lang->infoqz->gainDesc : $lang->infoqz->fixDesc;?></th>
              <td colspan='2'><?php echo html::input('desc', $info->desc, "class='form-control' maxlength='200'");?></td>
          </tr>
          <tr>
              <th><?php echo $action == 'gain' ? $lang->infoqz->gainReason : $lang->infoqz->fixReason;?></th>
              <td colspan='2'><?php echo html::textarea('reason', $info->reason, "class='form-control' rows='5' maxlength='2000'");?></td>
          </tr>
          <?php if($action == 'gain'):?>
              <tr>
                  <th><?php echo $lang->infoqz->gainPurpose;?></th>
                  <td colspan='2'><?php echo html::textarea('purpose', $info->purpose, "class='form-control' R rows='5' maxlength='2000'");?></td>
              </tr>
          <?php endif;?>

          <tr>
              <th><?php echo $lang->infoqz->test;?></th>
              <td colspan='2'><?php echo html::textarea('test', $info->test, "class='form-control' rows='5' maxlength='1000'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->infoqz->content;?></th>
              <td colspan='2'><?php echo html::textarea('content', $info->content, "class='form-control' rows='5'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->infoqz->operation;?></th>
              <td colspan='2'><?php echo html::textarea('operation', $info->operation, "class='form-control' rows='5' maxlength='1000'");?></td>
          </tr>

          <tr>
              <th><?php echo $action == 'gain' ? $lang->infoqz->gainStep : $lang->infoqz->fixStep;?></th>
              <td colspan='2'><?php echo html::textarea('step', $info->step, "class='form-control' rows='5' maxlength='1000'");?></td>
          </tr>

          <tr>
              <th><?php echo $lang->infoqz->desensitization;?></th>
              <td colspan='2'><?php echo html::textarea('desensitization', $info->desensitization, "class='form-control' rows='5'");?></td>
          </tr>
          <tr>
              <th><?php echo $lang->infoqz->checkList;?></th>
              <td colspan='2'><?php echo html::textarea('checkList', $info->checkList, "class='form-control' rows='5'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->infoqz->isJinke ?></th>
            <td class='required'><?php echo html::select('isJinke', $lang->infoqz->isJinkeList, $info->isJinke, "class='form-control chosen' onchange='setIsJinke(this.value)'");?></td>
          </tr>
          <tr class='isJinke-tr'>
            <th class='w-140px'><?php echo $lang->infoqz->desensitizationType ?></th>
            <td class='required'><?php echo html::select('desensitizationType', $lang->infoqz->desensitizationTypeList, $info->desensitizationType, "class='form-control chosen' onchange='setDesensitizationType(this.value)'");?></td>
          </tr>
          <tr class='isJinke-tr'>
            <th class='w-140px'><?php echo $lang->infoqz->deadline; ?></th>
            <td class='required' style="display:flex;align-items:center">
                <span><?php echo html::radio('isDeadline', $lang->infoqz->isDeadlineList,$info->isDeadline, "onclick='setDeadline(this.value)'");?></span>
                <span style="margin-left:40px" class="dealine-tr visible"><?php echo html::input('deadline', substr($info->deadline,0,10), "class='form-control form-date' placeholder='请选择日期'");?></span>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->infoqz->isDesensitize;?></th>
              <td class='required'><?php echo html::radio('isDesensitize', $lang->infoqz->aclList, $info->isDesensitize, "onclick='setWhite(this.value);'");?></td>
          </tr>
          <tr id="whitelistBox" class="hidden">
              <th><?php echo $lang->infoqz->desensitizeProcess;?></th>
              <td class="required" colspan='2'><?php echo html::textarea('desensitizeProcess', $info->desensitizeProcess, "class='form-control' rows='5' ");?></td>
          </tr>
          <tr class="nodes">
              <th class='w-140px'>
                  <?php echo $lang->infoqz->reviewNodes;?>
                  <i title="<?php echo $lang->infoqz->reviewNodesTip;?>" class="icon icon-help"></i>
              </th>
              <td>
                  <?php
                  foreach($lang->infoqz->reviewerList as $key => $nodeName):
                      if($key!='4'):
                          $currentAccounts = '';
                          if(isset($nodesReviewers[$key]) && !empty($nodesReviewers[$key])):
                              $currentAccounts = implode(',', $nodesReviewers[$key]);
                          endif;
                          if($key == 3 && isset($reviewerAccounts[$key])):
                              $currentAccounts = implode(',', $reviewerAccounts[$key]);
                          endif;
                          ?>
                          <div class='input-group node-item node<?php echo $key;?>'>
                              <span class='input-group-addon'><?php echo $nodeName;?></span>
                              <?php echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");?>
                          </div>
                      <?php endif;?>
                  <?php endforeach;?>
              </td>
          </tr>
          <tr>
              <input type="hidden" name="issubmit" value="<?php echo $info->issubmit?>">
              <td class='form-actions text-center' colspan='4'><?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') . html::commonButton($lang->infoqz->submit, '', 'btn btn-wide btn-primary submitBtn') . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php js::set('initType', $info->type);?>
<?php js::set('isJinke', $info->isJinke);?>
<?php js::set('isDesensitize', $info->isDesensitize);?>
<?php js::set('isDeadline', $info->isDeadline);?>
<script>
    //保存不需要校验数据
    $(".saveBtn").click(function () {
        $("[name='issubmit']").val("save");
        $("#dataform").submit();
    })
    //提交需要校验数据
    $(".submitBtn").click(function () {
        $("[name='issubmit']").val("submit");
        $("#dataform").submit();
    });
    /**
     * 自动加载函数
     */
    $(function(){
        selectCompany(1);
        setWhite(isDesensitize);
        setReviewNodeInfo(initType);
        setIsJinke(isJinke);
        setDeadline(isDeadline);
        $('#submit').click(function() {
            if($('#isJinke').val() != isJinke){
                var msg = "当前操作会新增/删除对应的数据使用信息，是否继续保存";
                if(confirm(msg) == true){
                    return true;
                }else{
                    return false;
                }
            }
        });
    });

    $('#fixType').change(function()
    {
        var fixType = $(this).val();
        $.get(createLink('infoqz', 'ajaxGetSecondLine', "fixType=" + fixType), function(data)
        {
            $('#project_chosen').remove();
            $('#project').replaceWith(data);
            $('#project').chosen();
        });
    });
    $('#isNPC').change(function()
    {
        var isNPC = $(this).val();
        $.get(createLink('infoqz', 'ajaxGetisNPC', "isNPC=" + isNPC), function(data)
        {
            $('#node_chosen').remove();
            $('#node').replaceWith(data);
            $('#node').chosen();
        });
    });

    $('#type').change(function()
    {
        var type = $(this).val();
        $.get(createLink('infoqz', 'ajaxGetclassify', "type=" + type), function(data)
        {
            $('#classify_chosen').remove();
            $('#classify').replaceWith(data);
            $('#classify').chosen();
        });
        setReviewNodeInfo(type);
    });

    $(function() {
        window.editor['test'].edit.afterChange(function (){
            var limitNum = 1000;  //设定限制字数
            window.editor['test'].sync();
            var strValue = $("#test").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['test'].text();
                value = value.substring(0,limitNum);
                window.editor['test'].text(value);
                window.editor['test'].focus();
                window.editor['test'].appendHtml('');
            }
        });
    });

    $(function() {
        window.editor['operation'].edit.afterChange(function (){
            var limitNum = 1000;  //设定限制字数
            window.editor['operation'].sync();
            var strValue = $("#operation").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['operation'].text();
                value = value.substring(0,limitNum);
                window.editor['operation'].text(value);
                window.editor['operation'].focus();
                window.editor['operation'].appendHtml('');
            }
        });
    });

    $(function() {
        window.editor['step'].edit.afterChange(function (){
            var limitNum = 1000;  //设定限制字数
            window.editor['step'].sync();
            var strValue = $("#step").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['step'].text();
                value = value.substring(0,limitNum);
                window.editor['step'].text(value);
                window.editor['step'].focus();
                window.editor['step'].appendHtml('');
            }
        });
    });

    $(function() {
        window.editor['purpose'].edit.afterChange(function (){
            var limitNum = 2000;  //设定限制字数
            window.editor['purpose'].sync();
            var strValue = $("#purpose").val();
            strValue = strValue.replace(/<[^>]+>/g,"");
            if(strValue.length > limitNum) {
                var value = window.editor['purpose'].text();
                value = value.substring(0,limitNum);
                window.editor['purpose'].text(value);
                window.editor['purpose'].focus();
                window.editor['purpose'].appendHtml('');
            }
        });

    });

    function setIsJinke(val){
        if(val=='1'){
            // 是
            $('.isJinke-tr').removeClass('hidden');
            setDesensitizationType($('#desensitizationType').val())
        }else{
            $('.isJinke-tr').addClass('hidden');
        }
    }

    function setDesensitizationType(val)
    {
        $('#isDesensitize0').removeAttr('disabled')
        $('#isDesensitize1').removeAttr('disabled')
        if(val=='1' || val =='2'){
            // 全部脱敏数据
            $('#isDesensitize1').trigger('click')
        }else{
            $('#isDesensitize0').trigger('click')
        }
        $('#isDesensitize0').attr('disabled',true)
        $('#isDesensitize1').attr('disabled',true)
    }

    function setWhite(acl)
    {
        acl == '1' ? $('#whitelistBox').removeClass('hidden') : $('#whitelistBox').addClass('hidden');
    }

    function setDeadline(val){
        val == '2' ? $('.dealine-tr').removeClass('visible'):$('.dealine-tr').addClass('visible');
    }


</script>
