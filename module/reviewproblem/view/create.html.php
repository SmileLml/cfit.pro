<?php include '../../common/view/header.html.php'?>
<?php include '../../common/view/kindeditor.html.php'?>
<style>
    .center-block  .form-control::-webkit-input-placeholder {font-size: 13px; line-height: 20px;color: rgb(136, 136, 136);}
    .center-block  .form-control::-moz-placeholder {font-size: 13px; line-height: 20px; color: rgb(136, 136, 136);}
    .center-block  .form-control:-ms-input-placeholder {font-size: 13px; line-height: 20px;color: rgb(136, 136, 136);}
    .center-block  .form-control::placeholder {font-size: 13px; line-height: 20px; color: rgb(136, 136, 136);}
</style>

<div id="mainContent" class="main-content fade">
  <div class="center-block">
    <div class="main-header">
      <h2><?php echo $lang->reviewproblem->create;?></h2>
    </div>
    <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
      <table class="table table-form">
        <tbody>
          <tr>
              <th class='w-180px'><?php echo $lang->reviewproblem->review;?></th>
              <td><?php echo html::select('review', $reviewPairs, $reviewID, 'class="form-control chosen"');?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->reviewproblem->type;?></span>
                      <?php echo html::select('type', $this->lang->reviewproblem->typeList, $grade, 'class="form-control chosen"');?>
                  </div>
              </td>
          </tr>

          <tr>
              <th class='w-180px'><?php echo $lang->reviewproblem->raiseBy;?></th>
              <td><?php echo html::select('raiseBy', $users, $this->app->user->account, 'class="form-control chosen"');?></td>
              <td>
                  <div class='input-group'>
                      <span class='input-group-addon'><?php echo $lang->reviewproblem->raiseDate;?></span>
                      <?php echo html::input('raiseDate', helper::today(), "class='form-date form-control' ");?>
                  </div>
              </td>
          </tr>

          <tr>
            <th><?php echo $lang->reviewproblem->title;?></th>
            <td colspan='2'><?php echo html::input('title', '', "class='form-control' placeholder='{$lang->reviewproblem->titleTemplate}'");?></td>
         </tr>
          <tr>
            <th class="w-120px"><?php echo $lang->reviewproblem->desc;?></th>
            <td colspan='2'><?php echo html::textarea('desc', '', 'class="form-control"');?></td>
          </tr>

          <tr class="uploadFileInfo" style="display: none;">
              <th><?php echo $lang->files;?></th>
              <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
          </tr>

          <tr>
            <th class="w-120px"></th>
              <td class='form-actions text-center' colspan='2'><?php echo html::submitButton() . html::backButton();?></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php js::set('reviewType', $reviewType);?>
<script>
    $(function () {
        setUploadFileIsShow(reviewType);
    });
    $('#review').change(function()
    {
        var reviewID = $(this).val();
        var link = createLink('reviewissue', 'ajaxGetType','reviewID=' + reviewID);
        $.post(link, function(data)
        {
            var result = $.parseJSON(data);
            $('#type').val(result.grade);
            $('#type').trigger('chosen:updated');
            //是否显示上传附件
            setUploadFileIsShow(result.type);
        })
    });

    function findCheck()
    {
        var reviewID = $("#review").val();
        var category = $("#category").val();

        var link = createLink('reviewproblem', 'ajaxGetCheck','reviewID=' + reviewID + '&category=' + category);
        $.post(link, function(data)
        {
            $('#listID').replaceWith(data);
            $('#listID_chosen').remove();
            $('#listID').chosen();
        })
    }

    // $('#review').change();
    //
    // $.get(createLink('reviewissue', 'ajaxGetCommonList', "id="+'resolutionBy'), function(data)
    // {
    //     $('#resolutionBy_chosen').remove();
    //     $('#resolutionBy').replaceWith(data);
    //     $('#resolutionBy').chosen();
    // });
    //
    // $.get(createLink('reviewissue', 'ajaxGetCommonList', "id="+'validation'), function(data)
    // {
    //     $('#validation_chosen').remove();
    //     $('#validation').replaceWith(data);
    //     $('#validation').chosen();
    // });
</script>
<?php include '../../common/view/footer.html.php'?>
