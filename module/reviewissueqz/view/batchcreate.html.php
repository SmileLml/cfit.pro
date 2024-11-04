<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php'?>
<style>
    .table-responsive  .form-control::-webkit-input-placeholder {font-size: 12px; line-height: 20px;color: rgb(136, 136, 136);}
    .table-responsive  .form-control::-moz-placeholder {font-size: 12px; line-height: 20px; color: rgb(136, 136, 136);}
    .table-responsive  .form-control:-ms-input-placeholder {font-size: 12px; line-height: 20px;color: rgb(136, 136, 136);}
    .table-responsive  .form-control::placeholder {font-size: 12px; line-height: 20px; color: rgb(136, 136, 136);}
</style>
<div id="mainContent" class="main-content fade">
    <div class="main-header">
        <h2><?php echo $lang->reviewissueqz->batchCreate;?></h2>
    </div>
    <?php
    $visibleFields  = array();
    $requiredFields = array();
    foreach(explode(',', $showFields) as $field)
    {
        if(strpos(",{$config->reviewissueqz->availableBatchCreateFields},", ",{$field},") === false) continue;
        if($field) $visibleFields[$field] = '';
    }
    foreach(explode(',', $config->reviewissueqz->create->requiredFields) as $field)
    {
        if($field)
        {
            $requiredFields[$field] = '';
            if(strpos(",{$config->reviewissueqz->availableBatchCreateFields},", ",{$field},") !== false) $visibleFields[$field] = '';
        }
    }
    $minWidth = (count($visibleFields) > 5) ? 'w-150px' : '';
    ?>
    <form method='post' class='load-indicator main-form' enctype='multipart/form-data' target='hiddenwin' id="batchCreateForm">
        <div class="table-responsive">
            <table class="table table-form">
                <thead>
                <tr>
                    <th class='w-40px'> <?php echo $lang->idAB;?></th>
                    <th class='w-300px required text-center' <?php echo zget($requiredFields, 'reviewId', '', ' required')?>'><?php echo $lang->reviewissueqz->review;?></th>
                    <th class='w-200px required text-center' <?php echo zget($requiredFields, 'raiseBy', '', '')?>'><?php echo $lang->reviewissueqz->raiseBy;?></th>
                    <th class='w-200px required text-center' <?php echo zget($requiredFields, 'raiseDate', '', '')?>'><?php echo $lang->reviewissueqz->raiseDate;?></th>
                    <th class='w-400px required text-center' <?php echo zget($requiredFields, 'title', '', ' required')?>'><?php echo $lang->reviewissueqz->title;?></th>
                    <th class='w-500px required text-center' <?php echo zget($requiredFields, 'desc', '', ' required')?>'><?php echo $lang->reviewissueqz->desc;?></th>
                </tr>
                </thead>
                <tbody>

                <?php for($i = 0; $i < $config->reviewissueqz->batchCreateNum; $i++):?>
                    <tr>
                        <td><?php echo $i+1;?></td>
                        <td><?php echo html::select("reviewId[$i]", $reviewPairs, 'ditto', "class='form-control chosen' onchange='reviewCheck($i)'")?></td>
                        <td><?php echo html::select("raiseBy[$i]",  $users, $this->app->user->account, 'class="form-control chosen"');?></td>
                        <td><?php echo html::input("raiseDate[$i]", helper::now(), "class='form-datetime form-control' ");?></td>
                        <td><?php echo html::input("title[$i]", '', "class='form-control' placeholder='{$lang->reviewissueqz->titleTemplate}'");?></td>
                        <td><?php echo html::textarea("desc[$i]", '', "rows='2' class='form-control'");?></td>
                    </tr>
                <?php endfor;?>

                </tbody>
                <tfoot>
                <tr>
                    <td colspan="<?php echo 7;?>" class="text-center form-actions">
                        <?php echo html::submitButton($lang->save);?>
                        <?php echo html::backButton();?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </form>
</div>
<script>
    function reviewCheck($i)
    {
        var reviewID = $("#reviewId"+$i).val();
        var link = createLink('reviewissueqz', 'ajaxGetType','reviewID=' + reviewID);
        $.post(link, function(data)
        {
            var result = $.parseJSON(data);

            $("#type"+$i).val(result.grade);
            $("#type"+$i).trigger('chosen:updated');
        })
    }

    $('#reviewId').change();
</script>
<?php include '../../common/view/footer.html.php';?>
