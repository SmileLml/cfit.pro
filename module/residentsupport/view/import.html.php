<?php include '../../common/view/header.lite.html.php';?>
<?php
$type = $this->lang->residentsupport->typeList;
$subType = $this->lang->residentsupport->subTypeList;
?>
<main id="main">
  <div class="container">
    <div id="mainContent" class='main-content'>
      <div class='main-header'>
        <h2><?php echo $title;?></h2>
      </div>
      <form method='post' enctype='multipart/form-data' target='hiddenwin' style="padding: 20px 0 15px">
      <table class='table table-form w-p100'>
          <tr>
              <th class="width-80px">新建模板&nbsp;&nbsp;</th>
              <td class="width-80px"><input type="radio" name="editMethod" value="add" checked id=""></td>
              <th class="width-80px">编辑已有模板&nbsp;&nbsp;</th>
              <td class="width-80px"><input type="radio" name="editMethod" value="edit" id=""></td>
          </tr>
          <tr class="edit" style="display: none;">
              <th class='w-80px'><?php echo $lang->residentsupport->type;?></th>
              <td class='w-150px'><?php echo html::select('type', $type,'1', "class='form-control' onchange='getTemplate()'");?></td>
              <th class='w-80px'><?php echo $lang->residentsupport->subType;?></th>
              <td class='w-100px'><?php echo html::select('subType', $subType, '1', "class='form-control' onchange='getTemplate()'");?>
          </tr>
          <tr>
              <th class='w-80px editth' style="display: none;">选择模板</th>
              <td class='w-150px editth' style="display: none"><?php echo html::select('templateId', [],'1', "class='form-control chosen' required");?></td>
              <th class='w-80px'>选择文件</th>
              <td align='center' class="width-200px">
                  <input type='file' name='file' class='form-control'/>
              </td>
          </tr>
        <tr style="display: none;">
            <td class="w-150px" style="display: none">
                <?php echo html::select('encode', $config->charsets[$this->cookie->lang], 'utf-8', "class='form-control'");?>
            </td>
        </tr>
          <tr>
              <td class="w-150px">
                  <?php echo html::submitButton('', '', 'btn btn-primary btn-block');?>
              </td></tr>
          <tr>
              <td colspan="2" class="text-left"><span class="label label-info"><?php echo $msgTxt;?></span></td>
          </tr>
        </tr>
      </table>
      </form>
    </div>
  </div>
</main>
<script>
    setInterval(function () {
        $("#submit").removeAttr("disabled");
    },5000)
    function getTemplate() {
        var type = $("#type option:selected").val();
        var subType = $("#subType option:selected").val();
        if (type == '' || subType == ''){
            return false
        }
        $.post(createLink('residentsupport', 'ajaxGetTemplate'),{type:type,subType:subType},function (res) {
            $('#templateId').siblings().remove();
            $('#templateId').replaceWith(res);
            $('#templateId').chosen();
        })
    }
    getTemplate();
    $("[name='editMethod']").change(function () {
        var _val = $(this).val();
        if (_val == 'edit'){
            $(".edit").css("display",'table-row')
            $(".editth").css("display",'table-cell')
        }else{
            $(".edit").css("display",'none')
            $(".editth").css("display",'none')

        }
    })
</script>
<?php include '../../common/view/footer.lite.html.php';?>
