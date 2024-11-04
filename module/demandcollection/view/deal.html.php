<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandcollection->deal;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="dealForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody id="demandcollectiontbody">
        <tr>
            <th ><?php echo $lang->demandcollection->submitter;?></th>
            <td colspan='2'><?php echo html::select('submitter',$users,$demandcollection->submitter, "class='form-control chosen'");?></td>
        </tr>
        <tr>
            <th ><?php echo $lang->demandcollection->bPlatform;?></th>
            <td colspan='2'><?php echo html::select('belongPlatform',$this->lang->demandcollection->belongPlatform,$demandcollection->belongPlatform, "class='form-control chosen'");?></td><td></td>
        </tr>
        <tr>
            <th ><?php echo $lang->demandcollection->bModel;?></th>
            <td colspan='2'><?php echo html::select('belongModel',$filterBelongPlatform,$demandcollection->belongModel, "class='form-control chosen'");?></td><td></td>
        </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->title;?></th>
            <td colspan='2'><?php echo html::input('title',$demandcollection->title,"class='form-control'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->type;?></th>
            <td><?php echo html::select('type',$lang->demandcollection->typeList,$demandcollection->type, "class='form-control chosen' onchange=typeChange(this.value)");?></td>
            <td class="commConfirm">
              <div class='input-group required'>
                  <span class='input-group-addon'><?php echo $lang->demandcollection->correctionReason;?></span>
                  <?php echo html::select('correctionReason',$correctionReasonList,$demandcollection->correctionReason, "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
        <tr class="commConfirm">
            <th><?php echo $lang->demandcollection->commConfirmBy;?></th>
            <td class="required" colspan="2"><?php echo html::select('commConfirmBy[]',$users,$demandcollection->commConfirmBy, "class='form-control chosen' multiple");?></td>
        </tr>
        <tr class="commConfirm">
            <th><?php echo $lang->demandcollection->commConfirmRecord;?></th>
            <td colspan='2' class="required">
                <?php echo html::textarea('commConfirmRecord',$demandcollection->commConfirmRecord,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
        </tr>
          <tr>
            <th><?php echo $lang->demandcollection->desc;?></th>
            <td colspan='2'>
            <?php echo html::textarea('desc',$demandcollection->desc,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->analysis;?></th>
            <td colspan='2'>
            <?php echo html::textarea('analysis',$demandcollection->analysis,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
              <th ><?php echo $lang->demandcollection->product;?></th>
              <td colspan='2'><?php echo html::select('product[]', $productList, $demandcollection->product, "class='form-control chosen' multiple onchange='getProductPlanList();'");?></td>
              <td></td>
          </tr>

          <?php if(in_array($this->session->user->account,$this->demandcollection->getScheme('writerList'))): ?>
          <tr>
              <th><?php echo $lang->demandcollection->scheme;?></th>
              <td colspan='2'>
                  <?php echo html::textarea('scheme',$demandcollection->scheme,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
              </td>
          </tr>
          <?php endif;?>
          <tr>
            <th ><?php echo $lang->demandcollection->processingDate;?></th>
            <td colspan='2'><?php echo html::input('processingDate', helper::now(), "class='form-control form-datetime'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->Implementation;?></th>
            <td><?php echo html::select('Implementation', $depts, '11', "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->demandcollection->priority;?></span>
                <?php echo html::select('priority', $prioritys, $demandcollection->priority, "class='form-control chosen'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->Expected;?></th>
            <td><?php echo html::select('Expected[]', $plans, $demandcollection->Expected,"class='form-control chosen' multiple placeholder='{$lang->demandcollection->placeholder->Expected}'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->demandcollection->Actual;?></span>
                <?php echo html::select('Actual[]', $plans, $demandcollection->Actual,"class='form-control chosen' multiple placeholder='{$lang->demandcollection->placeholder->Actual}'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->state;?></th>
            <td><?php echo html::select('state',$lang->demandcollection->statusList,$demandcollection->state, "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->demandcollection->launchDate;?></span>
                <?php echo html::input('launchDate', $demandcollection->launchDate, "class='form-control form-date'");?>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->assignFor;?></th>
            <td><?php echo html::select('assignFor',$users,$demandcollection->assignFor, "class='form-control chosen'");?></td>
            <td>
              <div class='input-group'>
                <span class='input-group-addon'><?php echo $lang->demandcollection->copyFor;?></span>
                <?php echo html::select('copyFor[]',$users,$demandcollection->copyFor, "class='form-control chosen' multiple");?>
              </div>
            </td>
          </tr>

          <tr>
            <td><?php echo html::hidden('operations','save','id="operations"');?></td>
          </tr>
          <tr>
            <td colspan='4' class='text-center form-actions'>
              <?php echo html::submitButton('','onclick=change("save")','btn btn-wide btn-primary');?>
              <?php echo html::submitButton($lang->demandcollection->transfer ,'onclick=change("transfer")' , 'btn btn-wide btn-primary');?>
              <?php echo html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('expectedIds', !empty($demandcollection->Expected) ? array_filter(explode(',', $demandcollection->Expected)): []);?>
<?php js::set('actualIds',   !empty($demandcollection->Actual) ? array_filter(explode(',', $demandcollection->Actual)): []);?>
<script>
    $(document).ready(function (){
        typeChange($('#type').val())
    })

    function typeChange(type){
        if(type == 6){
            $('.commConfirm').removeClass('hidden');
        }else {
            $('.commConfirm').addClass('hidden');
        }
    }

    getProductPlanList(expectedIds, actualIds);
    /**
     *根据产品获得版本列表
     *
     * @param productIds
     */
    function getProductPlanList(expectedIds, actualIds){
        expectedIds = expectedIds || [];
        actualIds || [];
        var productIds = '';
        $("#product option:selected").each(function () {
            if ($(this).val() != ''){
                productIds += $(this).val()+',';
            }
        });
        //获得产品版本
        $.get(createLink('demandcollection', 'ajaxGetProductPlanList', 'productIds=' + productIds), function(data){
            $('#Expected_chosen').remove();
            $('#Expected').replaceWith(data[0]);
            $('#Expected').chosen();
            $('#Expected').val(expectedIds).trigger('chosen:updated');

            $('#Actual_chosen').remove();
            $('#Actual').replaceWith(data[1]);
            $('#Actual').chosen();
            $('#Actual').val(actualIds).trigger('chosen:updated');
        },'json');
    }

    function change(data){
          //console.log(data);
          $('#operations').val(data);
    }

    $('#belongPlatform').change(function()
    {
        var type = $(this).val();
        $.get(createLink('demandcollection', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            $('#belongModel_chosen').remove();
            $('#belongModel').replaceWith(data);
            $('#belongModel').chosen();
        });
    });
</script>
<?php include '../../common/view/footer.html.php';?>
