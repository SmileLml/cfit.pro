<?php include '../../common/view/header.html.php' ?>
<?php include '../../common/view/kindeditor.html.php' ?>
<div id="mainContent" class="main-content fade">
    <div class="center-block">
        <div class="main-header">
            <h2><?php echo $lang->reviewissue->edit; ?></h2>
        </div>
        <form class="load-indicator main-form form-ajax" method="post" enctype="multipart/form-data" id="dataform">
            <table class="table table-form">
                <tbody>
                <tr>
                    <th class="w-120px"><?php echo $lang->reviewissue->review; ?></th>
                    <td><?php echo html::select('review', $reviewPairs, $issue->review, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewissue->type; ?></th>
                    <td><?php echo html::select('type', $this->lang->reviewissue->typeList, $issue->type, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>

                <tr>
                    <th><?php echo $lang->reviewissue->raiseBy; ?></th>
                    <td><?php echo html::select('raiseBy', $users, $issue->raiseBy, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>

                <tr>
                    <th><?php echo $lang->reviewissue->raiseDate; ?></th>
                    <td><?php echo html::input('raiseDate', $issue->raiseDate, "class='form-date form-control' ");?></td>
                    <td></td>
                </tr>


<!--                <tr>-->
<!--                    <th>--><?php //echo $lang->reviewissue->status; ?><!--</th>-->
<!--                    <td>--><?php //echo html::select('status', $lang->reviewissue->statusList, $issue->status, 'class="form-control chosen"'); ?><!--</td>-->
<!--                    <td></td>-->
<!--                </tr>-->
                <tr>
                    <th><?php echo $lang->reviewissue->resolutionBy; ?></th>
                    <td><?php echo html::select('resolutionBy', $users, $issue->resolutionBy, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewissue->validation; ?></th>
                    <td><?php echo html::select('validation', $users, $issue->validation, 'class="form-control chosen"'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th><?php echo $lang->reviewissue->title; ?></th>
                    <td colspan='2'><?php echo html::input('title', $issue->title, 'class="form-control"'); ?></td>
                </tr>
                <tr>
                    <th class="w-120px"><?php echo $lang->reviewissue->desc; ?></th>
                    <td colspan='2'><?php echo html::textarea('desc', $issue->desc, 'class="form-control"'); ?></td>
                </tr>
                <tr class="uploadFileInfo" style="display: none;">
                    <th><?php echo $lang->filesList;?></th>

                    <td  colspan='2'>
                        <div class='detail'>
                            <div class='detail-content ' style="white-space: nowrap;">
                                <?php
                                if($issue->files){
                                    echo $this->fetch('file', 'printFiles', array('files' => $issue->files, 'fieldset' => 'false', 'object' => null, 'canOperate' => true, 'isAjaxDel' => true));
                                }else{
                                    echo "<div class='text-center text-muted'>" . $lang->noData . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="uploadFileInfo" style="display: none;">
                    <th><?php echo $lang->files;?></th>
                    <td colspan='2'><?php echo $this->fetch('file', 'buildform', 'fileCount=1&percent=0.85');?></td>
                </tr>

                <tr>
                    <th class="w-120px"></th>
                    <td colspan="2" class="form-actions">
                        <?php echo html::submitButton(); ?>
                        <?php echo html::backButton(); ?>
                    </td>
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

    $('#review').change(function () {
        var reviewID = $(this).val();
        var link = createLink('reviewissue', 'ajaxGetType', 'reviewID=' + reviewID);
        $.post(link, function (data) {
            var result = $.parseJSON(data);
            $('#resolutionBy').val(result.issue);
            $('#resolutionBy').trigger('chosen:updated');

            $('#type').val(result.grade);
            $('#type').trigger('chosen:updated');
            //是否显示上传附件
            setUploadFileIsShow(result.type);
        })
    });

</script>
<?php include '../../common/view/footer.html.php' ?>
