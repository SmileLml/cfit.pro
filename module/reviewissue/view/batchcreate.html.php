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
            if(strpos(",{$config->reviewissue->availableBatchCreateFields},", ",{$field},") === false) continue;
            if($field) $visibleFields[$field] = '';
        }
        foreach(explode(',', $config->reviewissue->create->requiredFields) as $field)
        {
            if($field)
            {
                $requiredFields[$field] = '';
                if(strpos(",{$config->reviewissue->availableBatchCreateFields},", ",{$field},") !== false) $visibleFields[$field] = '';
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
                    <th class='w-300px required text-center' <?php echo zget($requiredFields, 'review', '', ' required')?>'><?php echo $lang->reviewissue->review;?></th>
                    <th class='w-200px required text-center' <?php echo zget($requiredFields, 'type', '', ' required')?>'><?php echo $lang->reviewissue->type;?></th>
                    <th class='w-200px required text-center' <?php echo zget($requiredFields, 'raiseBy', '', '')?>'><?php echo $lang->reviewissue->raiseBy;?></th>
                    <th class='w-200px required text-center' <?php echo zget($requiredFields, 'raiseDate', '', '')?>'><?php echo $lang->reviewissue->raiseDate;?></th>
                    <th class='w-400px required text-center' <?php echo zget($requiredFields, 'title', '', ' required')?>'><?php echo $lang->reviewissue->title;?></th>
                    <th class='w-500px required text-center' <?php echo zget($requiredFields, 'desc', '', ' required')?>'><?php echo $lang->reviewissue->desc;?></th>
                </tr>
                </thead>
                <tbody>

                <?php for($i = 0; $i < $config->reviewissue->batchCreate; $i++):?>
                    <tr>
                            <td><?php echo $i+1;?></td>
                        <?php if($i > 0):?>
                            <td><?php echo html::select("review[$i]", $reviewPairs, 'ditto', "class='form-control chosen' onchange='reviewCheck($i)'")?></td>
                        <?php else:?>
                            <td><?php echo html::select("review[$i]", $reviewPairsNoditto, $reviewID, "class='form-control chosen' onchange='reviewCheck($i)'")?></td>
                        <?php endif;?>


                        <?php if($i > 0):?>
                            <td><?php echo html::select("type[$i]",  $typeList, 'ditto', 'class="form-control chosen"');?></td>
                        <?php else:?>
                            <td><?php echo html::select("type[$i]",  $typeListNoDitto, $grade, 'class="form-control chosen"');?></td>
                        <?php endif;?>
                            <td><?php echo html::select("raiseBy[$i]",  $users, $this->app->user->account, 'class="form-control chosen"');?></td>
                            <td><?php echo html::input("raiseDate[$i]", helper::today(), "class='form-date form-control' ");?></td>
                            <td><?php echo html::input("title[$i]", '', "class='form-control' placeholder='{$lang->reviewissue->titleTemplate}'");?></td>
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
        var reviewID = $("#review"+$i).val();
        var link = createLink('reviewissue', 'ajaxGetType','reviewID=' + reviewID);
        $.post(link, function(data)
        {
            var result = $.parseJSON(data);
            // $("#resolutionBy"+$i).val(result.issue);
            // $("#resolutionBy"+$i).trigger('chosen:updated');

            $("#type"+$i).val(result.grade);
            $("#type"+$i).trigger('chosen:updated');
        })
    }

    $('#review').change();
</script>
<?php include '../../common/view/footer.html.php';?>
