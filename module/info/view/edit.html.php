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
      <h2><?php echo $lang->info->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->info->type;?></th>
            <td><?php echo html::select('type', $lang->info->typeList, $info->type, "class='form-control chosen'");?></td>
          </tr>

          <tr>
            <th class='w-140px'><?php echo $lang->info->classify;?></th>
            <td><?php echo html::select('classify[]', $lang->info->techList, $info->classify, "class='form-control chosen' multiple");?></td>
            <td class="hidden"><?php //echo html::select('classify', $lang->info->businessList, $info->classify, "class='form-control chosen' multiple");?></td>
          </tr>
          <!-- <?php if($action == 'gain'):?>
          <tr>
            <th><?php echo $lang->info->gainType;?></th>
            <td><?php echo html::select('gainType', $lang->info->gainTypeList, $info->gainType, "class='form-control'");?></td>
          </tr>
          <?php endif;?> -->
          <tr>
            <th class='w-140px'><?php echo $lang->info->app;?></th>
            <td><?php echo html::select('app[]', $apps, $info->app, "class='form-control chosen' multiple");?></td>
          </tr>
          <?php if($action != 'gain'):?>
          <tr>
            <th class='w-140px'><?php echo $action == 'gain' ? $lang->info->gainNode : $lang->info->fixNode;?></th>
            <td><?php echo html::select('node[]', $lang->info->nodeList, $info->node, "class='form-control chosen' multiple");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->info->planBegin;?></th>
            <td><?php echo html::input('planBegin', $info->planBegin, "class='form-control form-datetime'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->info->planEnd;?></th>
            <td><?php echo html::input('planEnd', $info->planEnd, "class='form-control form-datetime'");?></td>
          </tr>
         <!-- <tr>
            <th><?php /*echo $lang->info->consumed;*/?></th>
            <td><?php /*echo html::input('consumed', '', "class='form-control'");*/?></td>
          </tr>-->
          <tr>
              <th><?php echo $lang->info->fixType;?></th>
              <td><?php echo html::select('fixType', $lang->info->fixTypeList, $info->fixType, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->info->project;?></th>
            <td colspan='2'><?php echo html::select('project[]', $projects, $info->project, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->info->problem;?></th>
            <td colspan='2'><?php echo html::select('problem[]', $problems, $info->problem, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->info->demand;?></th>
            <td colspan='2'><?php echo html::select('demand[]', $demands, $info->demand, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->info->secondorderId;?></th>
            <td><?php echo html::select('secondorderId[]', $secondorderList, $info->secondorderId, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr>
            <th><?php echo $action == 'gain' ? $lang->info->gainDesc : $lang->info->fixDesc;?></th>
            <td colspan='2'><?php echo html::textarea('desc', $info->desc, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $action == 'gain' ? $lang->info->gainReason : $lang->info->fixReason;?></th>
            <td colspan='2'><?php echo html::textarea('reason', $info->reason, "class='form-control'");?></td>
          </tr>
          <?php if($action == 'gain'):?>
          <tr>
            <th><?php echo $lang->info->gainPurpose;?></th>
            <td colspan='2'><?php echo html::textarea('purpose', $info->purpose, "class='form-control'");?></td>
          </tr>
          <?php endif;?>
          <?php if($action == 'fix'):?>
          <tr>
            <th><?php echo $lang->info->operation;?></th>
            <td colspan='2'><?php echo html::textarea('operation', $info->operation, "class='form-control'");?></td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->info->test;?></th>
            <td colspan='2'><?php echo html::textarea('test', $info->test, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $action == 'gain' ? $lang->info->gainStep : $lang->info->fixStep;?></th>
            <td colspan='2'><?php echo html::textarea('step', $info->step, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->info->checkList;?></th>
            <td colspan='2'><?php echo html::textarea('checkList', $info->checkList, "class='form-control'");?></td>
          </tr>
          <?php if($action == 'gain'):?>
          <tr>
            <th class='w-140px'><?php echo $lang->info->isJinke ?></th>
            <td class='required'><?php echo html::select('isJinke', $lang->info->isJinkeList, $info->isJinke, "class='form-control chosen' onchange='setIsJinke(this.value)'");?></td>
          </tr>
          <tr class='isJinke-tr'>
            <th class='w-140px'><?php echo $lang->info->desensitizationType ?></th>
            <td class='required'><?php echo html::select('desensitizationType', $lang->info->desensitizationTypeList, $info->desensitizationType, "class='form-control chosen'");?></td>
          </tr>
          <tr class='isJinke-tr'>
            <th class='w-140px'><?php echo $lang->info->deadline; ?></th>
            <td class='required' style="display:flex;align-items:center">
                <span><?php echo html::radio('isDeadline', $lang->info->isDeadlineList,$info->isDeadline, "onclick='setDeadline(this.value)'");?></span>
                <span style="margin-left:40px" class="dealine-tr visible"><?php echo html::input('deadline', substr($info->deadline,0,10), "class='form-control form-date' placeholder='请选择日期'");?></span>
            </td>
          </tr>
          <?php endif;?>
          <tr class="nodes">
            <th class='w-140px'>
                <?php echo $lang->info->reviewNodes;?>
                <i title="<?php echo $lang->info->reviewNodesTip;?>" class="icon icon-help"></i>
            </th>
            <td>
              <?php
                    foreach($lang->info->reviewerList as $key => $nodeName):
                        $currentAccounts = '';
                        if(isset($nodesReviewers[$key]) && !empty($nodesReviewers[$key])):
                            $currentAccounts = implode(',', $nodesReviewers[$key]);
                        endif;
                        if($key == 3 && isset($reviewerAccounts[$key])):
                            $currentAccounts = implode(',', $reviewerAccounts[$key]);
                        endif;
                        if ($key == 4){continue;}
                        if ($key == 6 and $action == 'gain'){continue;}
                  ?>
              <div class='input-group node-item node<?php echo $key;?>'>
                <span class='input-group-addon'><?php echo $nodeName;?></span>
                <?php echo html::select("nodes[$key][]", $reviewers[$key], $currentAccounts, "class='form-control chosen' required multiple");?>
              </div>
              <?php endforeach;?>
            </td>
          </tr>
          <tr>
          <tr>
              <input type="hidden" name="issubmit" value="<?php echo $info->issubmit?>">
              <td class='form-actions text-center' colspan='4'><?php echo html::commonButton($lang->save, '', 'btn btn-wide btn-primary saveBtn') . html::commonButton($lang->info->submit, '', 'btn btn-wide btn-primary submitBtn') . html::backButton();?></td>
          </tr>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<?php js::set('initType', $info->type);?>
<?php js::set('action', $action);?>
<?php js::set('isJinke', $info->isJinke);?>
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
    $(function() {
        setMenuHighlight(action);
        setIsJinke(isJinke);
        setDeadline(isDeadline);
        setReviewNodeInfo(initType);
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
        $.get(createLink('info', 'ajaxGetSecondLine', "fixType=" + fixType), function(data)
        {
            $('#project_chosen').remove();
            $('#project').replaceWith(data);
            $('#project').chosen();
        });
    });
    $('#type').change(function()
    {
        var type = $(this).val();
        setReviewNodeInfo(type);
    });

    function setReviewNodeInfo(type){
      if(action == 'gain'){
        //审核节点的展示隐藏
        if(type == 'tech'){
            $('.node3').addClass('hidden');
            $('.node5').addClass('hidden');
        }else {
            $('.node3').removeClass('hidden');
            $('.node5').removeClass('hidden');
        }
      }
      $('.node3').addClass('hidden');
    }

    function setIsJinke(val){
        if(val=='1'){
            // 是
            $('.isJinke-tr').removeClass('hidden');
        }else{
            $('.isJinke-tr').addClass('hidden');
        }
    }
    function setDeadline(val){
        val == '2' ? $('.dealine-tr').removeClass('visible'):$('.dealine-tr').addClass('visible');
    }
</script>
