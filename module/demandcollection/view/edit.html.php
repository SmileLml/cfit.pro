<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id="mainContent" class="main-content">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->demandcollection->edit;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" id="editForm" method="post" target='hiddenwin'>
      <table class="table table-form">
        <tbody>
          <tr>
            <th ><?php echo $lang->demandcollection->submitter;?></th>
            <td  colspan='2'><?php echo html::select('submitter',$users,$demandcollection->submitter, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
              <th ><?php echo $lang->demandcollection->bPlatform;?></th>
              <td colspan='2'><?php echo html::select('belongPlatform',$this->lang->demandcollection->belongPlatform,$demandcollection->belongPlatform, "class='form-control chosen' onchange=belongPlatFormChange(this.value)");?></td><td></td>
          </tr>
          <tr>
              <th ><?php echo $lang->demandcollection->bModel;?></th>
              <td colspan='2'><?php echo html::select('belongModel',$filterBelongPlatform,$demandcollection->belongModel, "class='form-control chosen'");?></td><td></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->title;?></th>
            <td  colspan='2'><?php echo html::input('title',$demandcollection->title,"class='form-control'");?></td><td></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->desc;?></th>
            <td colspan='2'>
            <?php echo html::textarea('desc',$demandcollection->desc,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
            </td>
          </tr>
          <tr>
              <th><?php echo $lang->demandcollection->type;?></th>
              <td><?php echo html::select('type',$lang->demandcollection->typeList,$demandcollection->type, "class='form-control chosen' onchange=typeChange(this.value)");?></td>
              <td class="commConfirm hidden">
                  <div class='input-group required'>
                      <span class='input-group-addon'><?php echo $lang->demandcollection->correctionReason;?></span>
                      <?php echo html::select('correctionReason',$correctionReasonList,$demandcollection->correctionReason, "class='form-control chosen'");?>
                  </div>
              </td>
          </tr>
          <tr class="commConfirm hidden">
              <th><?php echo $lang->demandcollection->commConfirmBy;?></th>
              <td class="required" colspan="2"><?php echo html::select('commConfirmBy[]',$users,$demandcollection->commConfirmBy, "class='form-control chosen' multiple");?></td>
          </tr>
          <tr class="commConfirm hidden">
              <th><?php echo $lang->demandcollection->commConfirmRecord;?></th>
              <td colspan='2' class="required">
                  <?php echo html::textarea('commConfirmRecord',$demandcollection->commConfirmRecord,"rows='8' class='form-control kindeditor' hidefocus='true' tabindex=''");?>
              </td>
          </tr>
          <tr>
              <th><?php echo $lang->demandcollection->productmanager;?></th>
              <td class="required" colspan="2"><?php echo html::select('productmanager',$users,$demandcollection->productmanager, "class='form-control chosen'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->demandcollection->fileList;?></th>
            <td colspan='3'>
              <div class="detail">
                <div class="detail-content article-content">
                  <?php
                  if($demandcollection->files)
                  {
                      echo $this->fetch('file', 'printFiles', array('files' => $demandcollection->files, 'fieldset' => 'false', 'object' => null));
                  }
                  ?>
                </div>
              </div>
            </td>
          </tr>

          <tr>
            <th><?php echo $lang->demandcollection->files;?></th>
            <td colspan='2'><?php echo $this->fetch('file', 'buildForm');?></td>
          </tr>
          <tr>
            <th ><?php echo $lang->demandcollection->assignFor;?></th>
            <td><?php echo html::select('assignFor',$users,$demandcollection->assignFor, "class='form-control chosen'");?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->demandcollection->copyFor;?></span>
                      <?php echo html::select('copyFor[]',$users,$demandcollection->copyFor, "class='form-control chosen' multiple");?>
                  </div>
              </td>
          </tr>
          <tr>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton().html::backButton();?>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<!--关联平台扩展信息--->
<?php js::set('belongPlatformExtendList', $belongPlatformExtendList);?>
<script>
    $(document).ready(function (){
        var belongPlatform = $('belongPlatform').val();
        var type = $('#type').val();
        typeChange(type);
        belongPlatFormChange(belongPlatform); //所属平台

    })
    function typeChange(type){
        if(type == 6){
            $('.commConfirm').removeClass('hidden');
        }else {
            $('.commConfirm').addClass('hidden');
        }
    }



    /**
     * 修改所属平台
     *
     *
     * @param type
     */
    function belongPlatFormChange(type) {
        var extendInfo = belongPlatformExtendList[type];
        var  productmanager = extendInfo.productmanager;
        if(!productmanager){
            productmanager = 'ruantao';
        }
        $('#productmanager').val(productmanager).trigger('chosen:updated'); //关联产品经理
        $.get(createLink('demandcollection', 'ajaxGetChildTypeList', 'type=' + type), function(data)
        {
            $('#belongModel_chosen').remove();
            $('#belongModel').replaceWith(data);
            $('#belongModel').chosen();
        });
    }

</script>
