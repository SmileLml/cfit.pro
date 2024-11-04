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
            <h2><?php echo $lang->reviewissueqz->create;?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class='w-180px'><?php echo $lang->reviewissueqz->review;?></th>
                    <td class = 'required'><?php echo html::select('reviewId', $reviewPairs, $reviewID, 'class="form-control chosen"');?></td>
                </tr>

                <tr>
                    <th class='w-180px'><?php echo $lang->reviewissueqz->raiseBy;?></th>
                    <td class = 'required'><?php echo html::select('raiseBy', $users, $this->app->user->account, 'class="form-control chosen"');?></td>
                    <td class = 'required'>
                        <div class='input-group'>
                            <span class='input-group-addon'><?php echo $lang->reviewissueqz->raiseTime;?></span>
                            <?php echo html::input('raiseDate', helper::now(), "class='form-datetime form-control' ");?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><?php echo $lang->reviewissueqz->title;?></th>
                    <td colspan='2'  class = 'required'><?php echo html::input('title', '', "class='form-control' placeholder='{$lang->reviewissueqz->titleTemplate}'");?></td>
                </tr>
                <tr>
                    <th class="w-120px"><?php echo $lang->reviewissueqz->desc;?></th>
                    <td colspan='2'  class = 'required'><?php echo html::textarea('desc', '', 'class="form-control"');?></td>
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
<script>
    $('#review').change(function()
    {
        var reviewID = $(this).val();
        var link = createLink('reviewissueqz', 'ajaxGetType','reviewID=' + reviewID);
        var link = createLink('reviewissueqz', 'ajaxGetType','reviewID=' + reviewID);
        $.post(link, function(data)
        {
            var result = $.parseJSON(data);
            $('#type').val(result.grade);
            $('#type').trigger('chosen:updated');
        })
    });

    function findCheck()
    {
        var reviewID = $("#review").val();
        var category = $("#category").val();

        var link = createLink('reviewissueqz', 'ajaxGetCheck','reviewID=' + reviewID + '&category=' + category);
        $.post(link, function(data)
        {
            $('#listID').replaceWith(data);
            $('#listID_chosen').remove();
            $('#listID').chosen();
        })
    }

</script>
<?php include '../../common/view/footer.html.php'?>
