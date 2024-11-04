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
            <h2><?php echo $lang->reviewissue->batchCreate;?></h2>
        </div>
        <?php
        $visibleFields  = array();
        $requiredFields = array();
        foreach(explode(',', $showFields) as $field)
        {
            if(strpos(",{$config->reviewmeeting->availableBatchCreateFields},", ",{$field},") === false) continue;
            if($field) $visibleFields[$field] = '';
        }
        foreach(explode(',', $config->reviewmeeting->create->requiredFields) as $field)
        {
            if($field)
            {
                $requiredFields[$field] = '';
                if(strpos(",{$config->reviewmeeting->availableBatchCreateFields},", ",{$field},") !== false) $visibleFields[$field] = '';
            }
        }
        $minWidth = (count($visibleFields) > 5) ? 'w-150px' : '';
        ?>
        <form method='post' class='load-indicator main-form' enctype='multipart/form-data' target='hiddenwin' id="batchCreateForm">
        <div class="table-responsive">
            <table class="table table-form">
                <thead>
                <tr>
                    <th class='w-20px'> <?php echo $lang->idAB;?></th>
                    <th class='w-150px required text-center' <?php echo zget($requiredFields, 'review', '', ' required')?>'><?php echo $lang->reviewissue->review;?></th>
                    <th class='w-60px required text-center' <?php echo zget($requiredFields, 'type', '', ' required')?>'><?php echo $lang->reviewissue->type;?></th>
                    <th class='w-60px required text-center' <?php echo zget($requiredFields, 'raiseBy', '', '')?>'><?php echo $lang->reviewissue->raiseBy;?></th>
                    <th class='w-150px required text-center' <?php echo zget($requiredFields, 'title', '', ' required')?>'><?php echo $lang->reviewissue->title;?></th>
                    <th class='w-200px required text-center' <?php echo zget($requiredFields, 'desc', '', ' required')?>'><?php echo $lang->reviewissue->desc;?></th>
                </tr>
                </thead>
                <tbody>

                <?php for($i = 0; $i < $config->reviewissue->batchCreate; $i++):?>
                    <tr>
                            <td><?php echo $i+1;?></td>
                            <td><?php echo html::select("review[$i]", $reviewInfos, '', "class='form-control chosen' onchange='reviewCheck($i)'")?></td>
                            <td><?php echo html::select("type[$i]",  $typeListNoDitto, $lang->reviewmeeting->reviewTypeList['meeting'], 'class="form-control chosen"');?></td>
                            <td><?php echo html::select("raiseBy[$i]",  $users, $this->app->user->account, 'class="form-control chosen"');?></td>
                            <td><?php echo html::input("title[$i]", '', "class='form-control' placeholder='{$lang->reviewissue->titleTemplate}'");?></td>
                            <td><?php echo html::textarea("desc[$i]", '', "rows='2' class='form-control'");?></td>
                    </tr>
                <?php endfor;?>

                </tbody>
                <tfoot>
                <tr>
                    <td colspan="<?php echo count($visibleFields) + 5?>" class="text-center form-actions">
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
        var reviewID = $("#review"+$i).val();
        var link = createLink('reviewissue', 'ajaxGetType','reviewID=' + reviewID);
        $.post(link, function(data)
        {
            var result = $.parseJSON(data);

            $("#type"+$i).val(result.grade);
            $("#type"+$i).trigger('chosen:updated');
        })
    }

    $('#review').change();
</script>
<?php include '../../common/view/footer.html.php';?>
