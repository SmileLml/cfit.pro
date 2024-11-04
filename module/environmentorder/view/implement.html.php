<?php include '../../common/view/header.html.php'; ?>
<?php include '../../common/view/kindeditor.html.php'; ?>
    <div id="mainContent" class="main-content fade" style="min-height: 350px;">
        <div class="center-block">
            <div class="main-header">
                    <h2><?php echo $lang->environmentorder->implement; ?></h2>
            </div>
            <form class="load-indicator main-form form-ajax" method='post' enctype='multipart/form-data' id='dataform'>
                <table class="table table-form">
                    <tbody>
                    <tr>
                        <th><?php echo $lang->environmentorder->isImplement;?></th>
                        <td colspan='4'><?php echo html::select('dealResult', $lang->environmentorder->implementList , '', "class='form-control chosen' required  onchange='changeDealResult();'");?></td>
                        <td></td>
                    </tr>

                    <tr id="suggest" style="display: none">
                        <th><?php echo $lang->environmentorder->dealOpinion;?></th>
                        <td  id="suggest-td" colspan='4'><?php echo html::textarea('comment', '', "class='form-control' style='height:150px'");?></td>
                    </tr>
                    <tr id="workHours" style="display: none">
                        <th><?php echo $lang->environmentorder->workHour;?></th>
                        <td  id="workHour-td" colspan='4'>
                            <input type='text' name='workHour' value=""
                                   placeholder='处理该任务整体花费的时间'  required autocomplete="off"
                                   onblur="validateNumber(this)" class='form-control' />
                        </td>

                    </tr>
                    <tr>
                        <td class='form-actions text-center' colspan='5'>
                            <?php echo html::submitButton() . html::backButton();?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>

        </div>
    </div>
<?php include '../../common/view/footer.html.php'; ?>