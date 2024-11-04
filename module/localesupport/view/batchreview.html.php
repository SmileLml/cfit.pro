<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
    <div id="mainContent" class="main-content fade" style="min-height: 300px;">
        <div class="center-block">
            <div class="main-header">
                <h2>
                    <span class='label label-id'><?php echo $lang->localesupport->batchReview;?></span>
                </h2>
            </div>
            <?php if(!$checkRes['result']):?>
                <div class="tipMsg red">
                    <span><?php echo $checkRes['message']; ?></span>
                </div>
            <?php else:?>
                <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <th><?php echo $lang->localesupport->dealResult;?></th>
                            <td><?php echo html::select('dealResult', $lang->localesupport->dealResultList , '', "class='form-control chosen' required onchange='changeDealResult();'");?></td>
                            <td></td>
                        </tr>

                        <tr>
                            <th><?php echo $lang->localesupport->comment;?></th>
                            <td  id="suggestTd" colspan='2'><?php echo html::textarea('comment', '', "class='form-control' style='height:150px'");?></td>
                        </tr>
                        <tr>
                            <td class='form-actions text-center' colspan='3'>
                                <?php echo html::submitButton() . html::backButton();?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            <?php endif;?>
        </div>
    </div>

<?php include '../../common/view/footer.html.php';?>