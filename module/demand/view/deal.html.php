<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<style>
.task-toggle{line-height: 28px; color: #0c60e1; cursor:pointer;}
.task-toggle .icon{display: inline-block; transform: rotate(90deg);}
.more-tips{display: none;}
</style>
<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demand->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->handler;?></th>
            <td><?php echo html::select('user', $users, $this->app->user->account, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->consumed;?></th>
            <td><?php echo html::input('consumed', '', "class='form-control'");?></td>
          </tr>
          <tr id='relevantDept1'>
            <th class='w-140px'><?php echo $lang->demand->relevantDept;?></th>
            <td>
              <div class='table-row'>
                <div class='table-col'>
                  <?php echo html::select('relevantUser[]', $users, '', "class='form-control chosen'");?>
                </div>
                <div class='table-col'>
                  <div class='input-group'>
                    <span class='input-group-addon fix-border'><?php echo $lang->demand->workload;?></span>
                    <?php echo html::input('workload[]', '', "class='form-control'");?>
                  </div>
                </div>
              </div>
            </td>
            <td class="c-actions">
              <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='1' class="btn btn-link"><i class="icon-plus"></i></a>
            </td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->dealStatus;?></th>
            <td><?php echo html::select('status', $statusList, '', "class='form-control chosen' onchange='selectStatus(this.value)'");?></td>
          </tr>
          <tr>
            <th class='w-140px'></th>
            <td colspan='2'>
              <p>*产品经理：处理后状态为【待分配】,下一节点处理人请选择【二线专员】</p>
              <p>*二线专员：处理后状态为【待分析】,下一节点处理人请选择【分析人员】</p>
              <p>*分析人员：处理后状态为【待开发】,下一节点处理人请选择【解决人员】</p>
              <p>*开发人员：处理后状态为【待制版】,下一节点处理人请选择【质量部CM】</p>
              <p><a class="task-toggle" id='moreTips'>点击展开更多<i class="icon icon-angle-double-right"></i></a></p>
              <p class='more-tips'>*配置管理：处理后状态为【待测试】,下一节点处理人请选择【测试人员】</p>
              <p class='more-tips'>*测试人员：处理后状态为【待验证】,下一节点处理人请选择【质量部CM】</p>
              <p class='more-tips'>***说明：若【测试未通过】,下一节点处理人请选择【解决人员】</p>
              <p class='more-tips'>*验证人员：处理后状态为【待发布】,下一节点处理人请选择【质量部CM】</p>
              <p class='more-tips'>***说明：若【验证未通过】,下一节点处理人请选择【解决人员】</p>
              <p class='more-tips'>*配置管理：处理后状态为【待交付】,下一节点处理人请选择【二线专员】</p>
              <p class='more-tips'>*二线专员：处理后状态为【待上线】,下一节点处理人选择【创建人/二线专员】</p>
              <p class='more-tips'>*创建人/二线专员：处理后状态为【上线成功】或【上线失败】,下一节点处理人选择【二线专员】</p>
            </td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->nextUser;?></th>
            <td><?php echo html::select('dealUser', $users, '', "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th class='w-140px'><?php echo $lang->demand->mailto;?></th>
            <td colspan="2"><?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple");?></td>
          </tr>
          <tr class="hidden dev">
            <th class='w-140px'><?php echo $lang->demand->end;?></th>
            <?php $demand->end = helper::isZeroDate($demand->end) ? '' : $demand->end;?>
            <td class='required'><?php echo html::input('end', $demand->end, "class='form-control form-date'");?></td>
          </tr>
          <tr class="hidden dev dev2">
            <th class='w-140px'><?php echo $lang->demand->fixType;?></th>
            <td class='required notrequired'><?php echo html::select('fixType', $lang->demand->fixTypeList, $demand->fixType, "class='form-control chosen'");?></td>
          </tr>
          <tr class="hidden dev dev2">
            <th><?php echo $lang->demand->projectPlan;?></th>
            <td class='required notrequired'><?php echo html::select('projectPlan', $plans, $demand->projectPlan, "class='form-control chosen'");?></td>
          </tr>
          <tr class="hidden dev dev2">
            <th><?php echo $lang->demand->app;?></th>
            <td colspan="2"><?php echo html::select('app[]', $apps, $demand->app, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr class="hidden dev dev2">
            <th><?php echo $lang->demand->product;?></th>
            <td><?php echo html::select('product', $products, $demand->product, "class='form-control chosen'");?></td>
          </tr>
          <tr class="hidden dev dev2">
            <th><?php echo $lang->demand->productPlan;?></th>
            <td><?php echo html::select('productPlan', array('0' => ''), $demand->productPlan, "class='form-control chosen'");?></td>
            <td>
              <?php echo html::a('javascript:void(0);', '<i class="icon-plus"></i> ' . $lang->demand->createPlan, '', "class='btn btn-info hidden' data-app='product' id='createPlan'");?>
            </td>
          </tr>
          <tr class="hidden dev">
            <th><?php echo $lang->demand->reason;?></th>
            <td colspan='2' class='required'><?php echo html::textarea('reason', $demand->reason, "class='form-control'");?></td>
          </tr>
          <tr class="hidden dev">
            <th><?php echo $lang->demand->solution;?></th>
            <td colspan='2'><?php echo html::textarea('solution', $demand->solution, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demand->progress;?></th>
            <td colspan='2'><?php echo html::textarea('progress', $demand->progress, "class='form-control'");?></td>
          </tr>
          <tr>
            <td class='form-actions text-center' colspan='3'><?php echo html::submitButton($this->lang->demand->submitBtn) . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<table class='hidden'>
  <tbody id="relevantDeptTable">
    <tr id='relevantDept0'>
      <th class='w-140px'><?php echo $lang->demand->relevantDept;?></th>
      <td>
        <div class='table-row'>
          <div class='table-col'>
            <?php echo html::select('relevantUser[]', $users, '', "class='form-control' id='relevantUser0'");?>
          </div>
          <div class='table-col'>
            <div class='input-group'>
              <span class='input-group-addon fix-border'><?php echo $lang->demand->workload;?></span>
              <?php echo html::input('workload[]', '', "class='form-control'");?>
            </div>
          </div>
        </div>
      </td>
      <td class="c-actions">
        <a href="javascript:void(0)" onclick="addRelevantItem(this)" data-id='0' id='codePlus0' class="btn btn-link"><i class="icon-plus"></i></a>
        <a href="javascript:void(0)" onclick="delRelevantItem(this)" data-id='0' id='codeClose0' class="btn btn-link"><i class="icon-close"></i></a>
      </td>
    </tr>
  </tbody>
</table>
<?php
echo js::set('createPlanTips', $lang->demand->createPlanTips);
echo js::set('productPlan', $demand->productPlan);
?>
<script>
$('#fixType').change(function()
{
    var fixType = $(this).val();
    $.get(createLink('demand', 'ajaxGetSecondLine', "fixType=" + fixType), function(data)
    {
        $('#projectPlan_chosen').remove();
        $('#projectPlan').replaceWith(data);
        $('#projectPlan').chosen();
    });
});

function selectStatus(status)
{
  if(status === 'feedbacked' || status === 'solved')
  {
      $('.dev').removeClass('hidden');
      $('.notrequired').addClass('required')
  }
  else if(status === 'confirmed')
  {
    $('.dev2').removeClass('hidden');
    $('.notrequired').removeClass('required');
  }
  else
  {
      $('.dev').addClass('hidden');
  }
}

$('#moreTips').bind('click', function()
{
    $('.more-tips').attr('class', '');
    $('#moreTips').remove();
});

var relevantIndex = 1;
function addRelevantItem(obj)
{
    var relevantObj  = $('#relevantDeptTable');
    var relevantHtml = relevantObj.clone();
    relevantIndex++;

    relevantHtml.find('#codePlus0').attr({'id':'codePlus' + relevantIndex, 'data-id': relevantIndex});
    relevantHtml.find('#codeClose0').attr({'id':'codeClose' + relevantIndex, 'data-id': relevantIndex});

    relevantHtml.find('#relevantUser0').attr({'id':'relevantUser' + relevantIndex});
    relevantHtml.find('#relevantDept0').attr({'id':'relevantDept' + relevantIndex});

    var objIndex = $(obj).attr('data-id');
    $('#relevantDept' + objIndex).after(relevantHtml.html());

    $('#relevantUser' + relevantIndex).attr('class','form-control chosen');
    $('#relevantUser' + relevantIndex).chosen();

    console.log(relevantHtml.html());
}

function delRelevantItem(obj)
{
    var objIndex = $(obj).attr('data-id');
    $('#relevantDept' + objIndex).remove();
}
</script>
<script>
$(document).ready(function(){
    $('#product').change();
});

$('#product').change(function()
{
    var productID = $(this).val();
    $.get(createLink('demand', 'ajaxGetProductPlan', "productID=" + productID), function(planList)
    {
        $('#productPlan_chosen').remove();
        $('#productPlan').replaceWith(planList);
        $('#productPlan').val(productPlan);
        $('#productPlan').chosen();
    });

    // 处理创建产品计划按钮是否展示。
    if(productID > 0)
    {
        $("#createPlan").removeClass('hidden');
    }
    else
    {
        $("#createPlan").addClass('hidden');
    }
});

$("#createPlan").click(function()
{
    alert(createPlanTips);
});
</script>
<?php include '../../common/view/footer.html.php';?>
